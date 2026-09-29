<?php
namespace App\Models\Grpcomp;

use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Libraries\enc_string;

class TransactionModel extends Model	{

	// Cash & Cash Equivalents		23
	// Bank OC ODD					21
	// Trade Receivables			22 (sundry debitors)
	// Trade Payable				16 (sundry creditors)

	// PURCHASE 			7 (parent_id)
	// DIRECT EXPENSE		11 (parent_id)
	// INDIRECT EXPENSE		13 (parent_id)

    public function __construct() {
        parent::__construct();        
       $this->externaldb    = new externaldb();	
	   $this->db            = $this->externaldb->get_company_db();
	   $this->session       = \Config\Services::session();
	   $this->company_id    = $this->session->get('ses_company_id');
	   $this->enc_string    = new enc_string();
    }


    //-------------------------------------------------ADD METHODS START

    function add_voucher_cons_data($data){
		$comp_vch_cons_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
		
		 if($this->session->get('ses_boid')!='')
			$bo_id =  $this->session->get('ses_boid');
		  else 
			$bo_id =1;
		$data['bo_id'] = $bo_id;
		$this->db->table($comp_vch_cons_tbl)->insert($data);	
		return $this->db->insertID();	
	}

    function add_company_transaction($data)
    {
         $comp_txn_master_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
         $this->db->table($comp_txn_master_tbl)->insert($data);
         
         return $this->db->insertID();
    }

    function add_comp_txn_data($data){
	 	$comp_txn_master_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	   	$this->db->table($comp_txn_master_tbl)->insert($data);	
       	return  $this->db->insertID();
	}

    function add_voucher_trail($data)
    {
		$comp_vch_trail_tbl = $this->company_id.'_vhtxntrail_'.$this->session->get('ses_comp_fy_id');
		$this->db->table($comp_vch_trail_tbl)->insert($data);
    }

    function add_sundry_txn_data($data) // 1
    {
    	$rate = 1;

	   	$voucher_info = $this->get_voucher_cons_info($data['voucher_txn_id']);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$rate = $this->get_forex_rate_inr($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

	   	$data['sundry_txn_amount'] = ($data['sundry_txn_amount'] * $rate);
	   	$data['sundry_tag_rate'] = ($data['sundry_tag_rate'] * $rate);

        $sundry_txn_tbl=$this->company_id.'_sundrytxnn_'.$data['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
		 
		if(isset($data['sundry_txn_narr'])){
			$this->save_voucher_narration($data['voucher_txn_id'],$data['txn_id'],'short',$data['sundry_txn_narr']);
			unset($data['sundry_txn_narr']);		 
		 }
		 
		 $this->db->table($sundry_txn_tbl)->insert($data);

		 $this->update_bill_sundry_balance($data['bill_sundry_id']);
    }

    
    function add_bill_master($data) // 1
    {
        $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
		 if($this->session->get('ses_boid')!='')
			$bo_id =  $this->session->get('ses_boid');
		  else 
			$bo_id =1;
		$data['bo_id'] = $bo_id;
        $this->db->table($bill_mst_tbl)->insert($data);
         
        return $this->db->insertID();
    }
    function add_bill_txn($data)
    {
    	$rate = 1;

	   	$voucher_info = $this->get_voucher_cons_info($data['voucher_txn_id']);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$rate = $this->get_forex_rate_inr($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

	   	$data['bills_txn_amt'] = ($data['bills_txn_amt'] * $rate);

		 $last_data = $data;
        $bill_txn_tbl = $this->company_id.'_billstxnnn_'.$this->session->get('ses_comp_fy_id');
		unset($data['bills_txn_narr']);
		
		
		if($this->session->get('ses_boid')!='')
			$bo_id =  $this->session->get('ses_boid');
		  else 
			$bo_id =1;
		$data['bo_id'] = $bo_id;
        $this->db->table($bill_txn_tbl)->insert($data);
        $txnid = $this->db->insertID();
		$this->save_voucher_narration($data['voucher_txn_id'],$txnid,'short',$last_data['bills_txn_narr']);
		
        $this->update_bill_txn_balance($data['bills_ref_id']);
    }

    function add_cc_txn($data)
    {
    	$rate = 1;

	   	$voucher_info = $this->get_voucher_cons_info($data['voucher_txn_id']);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$rate = $this->get_forex_rate_inr($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

	   	$data['cc_txn_amt'] = ($data['cc_txn_amt'] * $rate);

       	$last_data = $data;
        $cc_txn_tbl = $this->company_id.'_costcttxnn_'.$this->session->get('ses_comp_fy_id');
		
		unset($data['cc_txn_narr']);
		$new_data = $data;
		
		 if($this->session->get('ses_boid')!='')
			$bo_id =  $this->session->get('ses_boid');
		  else 
			$bo_id =1;
		$data['bo_id'] = $bo_id;
        $this->db->table($cc_txn_tbl)->insert($data);
		$txnid = $this->db->insertID();
		$this->save_voucher_narration($data['voucher_txn_id'],$txnid,'short',$last_data['cc_txn_narr']);		 
				
        $this->update_cc_txn_balance($data['cc_id']);
    }
    
	function add_acc_oth_data($data){

		$rate = 1;

	   	$voucher_info = $this->get_voucher_cons_info($data['voucher_txn_id']);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$rate = $this->get_forex_rate_inr($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

	   	$data['acc_oth_txn_amount'] = ($data['acc_oth_txn_amount'] * $rate);

	 	$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
		if(isset($data['description'])){
		$this->save_voucher_narration($data['voucher_txn_id'],$data['txn_id'],'short',$data['description']);
		 unset($data['description']);
		}
		if(isset($data['acc_oth_txn_narr'])){
		$this->save_voucher_narration($data['voucher_txn_id'],$data['txn_id'],'short',$data['acc_oth_txn_narr']);
		 unset($data['acc_oth_txn_narr']);
		}		
		
		if($this->session->get('ses_boid')!='')
            $bo_id = $this->session->get('ses_boid');
		 else 
			  $bo_id =1;
		$data['bo_id'] =$bo_id;
	 	$this->db->table($table)->insert($data);
	}
	function add_acc_crsref_data($data){
		if($this->session->get('ses_boid')!='')
            $bo_id = $this->session->get('ses_boid');
		 else 
			  $bo_id =1;
		$data['bo_id'] =$bo_id;
	 	$table = $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
	 	$this->db->table($table)->insert($data);
	}
	function add_itm_oth_data($data){

		$rate = 1;

	   	$voucher_info = $this->get_voucher_cons_info($data['voucher_txn_id']);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$rate = $this->get_forex_rate_inr($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

	   	$data['item_oth_txn_amount'] = ($data['item_oth_txn_amount'] * $rate);

	 	$table = $this->company_id.'_itemtxnoth_'.$this->session->get('ses_comp_fy_id');
		
		$this->save_voucher_narration($data['voucher_txn_id'],$data['txn_id'],'short',$data['description']);		 
		 unset($data['description']);	 
		
		if($this->session->get('ses_boid')!='')
            $bo_id = $this->session->get('ses_boid');
		 else 
			  $bo_id =1;
		$data['bo_id'] =$bo_id;
	 	$this->db->table($table)->insert($data);
	}
	
	function add_acc_txn_data($data)
    {  
    	$rate = 1;

	   	$voucher_info = $this->get_voucher_cons_info($data['voucher_txn_id']);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$rate = $this->get_forex_rate_inr($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

	   	$data['acc_txn_amount'] = ($data['acc_txn_amount'] * $rate);


	     $last_data = $data;
		 if(isset($data['acc_txn_narr']))
		 unset($data['acc_txn_narr']);
				
         $account_table_name = $this->company_id.'_accnttxnnn_'.$data['acc_id'].'_'.$this->session->get('ses_comp_fy_id');
		 
		 if($this->session->get('ses_boid')!='')
			$bo_id =  $this->session->get('ses_boid');
		  else 
			$bo_id =1;
		
		 $data['bo_id']=$bo_id;
		 $this->db->table($account_table_name)->insert($data);
		 
		 if(isset($last_data['acc_txn_narr'])){
			   $acc_txn_narr = $last_data['acc_txn_narr'];		    
		      $this->save_voucher_narration($data['voucher_txn_id'],$data['txn_id'],'short',$acc_txn_narr);		 
		 } 
    }

    function add_acc_memo_data($data)
    {
    	$rate = 1;

	   	$voucher_info = $this->get_voucher_cons_info($data['voucher_txn_id']);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$rate = $this->get_forex_rate_inr($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

	   	$data['acc_txn_amount'] = ($data['acc_txn_amount'] * $rate);

	     $last_data = $data;
		 if(isset($data['acc_txn_narr']))
		 unset($data['acc_txn_narr']);
				
         $account_table_name = $this->company_id.'_memotxnnnn_'.$this->session->get('ses_comp_fy_id');
		 $this->db->table($account_table_name)->insert($data);
		 $acc_txn_id = $this->db->insertID();
		 
		 if(isset($last_data['acc_txn_narr'])){
			   $acc_txn_narr = $last_data['acc_txn_narr'];		    
		      $this->save_voucher_narration($data['voucher_txn_id'],$acc_txn_id,'short',$acc_txn_narr);		 
		 }

		 $this->update_account_memo_balance($data['acc_id']);
    }
    function add_itm_txn_data($data)
    {
    	$rate = 1;

	   	$voucher_info = $this->get_voucher_cons_info($data['voucher_txn_id']);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$rate = $this->get_forex_rate_inr($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

	   	$data['item_txn_amount'] = ($data['item_txn_amount'] * $rate);

	   	$this->save_voucher_narration($data['voucher_txn_id'],$data['txn_id'],'short',$data['description']);
		 unset($data['description']);

		 if($this->session->get('ses_boid')!='')
			$data['bo_id'] =  $this->session->get('ses_boid');
		  else 
			$data['bo_id'] = 1;

         $itemtxnnnn_tbl =  $this->company_id.'_itemtxnnnn_'.$data['item_id'].'_'.$this->session->get('ses_comp_fy_id');
		 $this->db->table($itemtxnnnn_tbl)->insert($data);

		 $this->update_item_unit_balance($data['item_id'], $data['item_unit'], $data['mat_cent_id'], $data['batch_id'], $data['item_avail']);
		 				 
		return $this->db->insertID(); 	 
    }

    function add_itm_txn_data_old($data)
    {
    	$rate = 1;

	   	$voucher_info = $this->get_voucher_cons_info($data['voucher_txn_id']);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$rate = $this->get_forex_rate_inr($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

	   	$data['item_txn_amount'] = ($data['item_txn_amount'] * $rate);

         $itemtxnnnn_tbl =  $this->company_id.'_itemtxnnnn_'.$data['item_id'].'_'.$this->session->get('ses_comp_fy_id');		 
		 $this->save_voucher_narration($data['voucher_txn_id'],$data['txn_id'],'short',$data['description']);		 
		 unset($data['description']);
		 
		  if($this->session->get('ses_boid')!='')
			$bo_id =  $this->session->get('ses_boid');
		  else 
			$bo_id =1;
		$data['bo_id']=$bo_id;
		 $this->db->table($itemtxnnnn_tbl)->insert($data);
		 
		  // $row_nums = $this->get_itemstable_rownums($itemtxnnnn_tbl);
	      // if($row_nums){
	      //            foreach($row_nums as $drow){
	      //                $item_txn_id = $drow['item_txn_id'];
	      //                $row_num     = $drow['row_num'];
	      //                $txn_id      = $drow['txn_id'];
	      //                $comp_id     = $drow['comp_id'];
	      //                $voucher_txn_id  = $drow['voucher_txn_id'];
	                     
	      //                $updte_data = array("item_txn_id"=>$row_num);
	      //                $this->update_itm_txn_entries($itemtxnnnn_tbl,$updte_data,$txn_id,$comp_id);
	      //            }
		// 		 }
		
		// $item_txn_id  = $this->get_item_txn_id($itemtxnnnn_tbl,$data['item_id'],$data['txn_id']);				 
		return $this->db->insertID(); 	 
    }


    function add_tracking_master($data)
    {
        $itemtrackn_tbl = $this->company_id.'_itemtrackn_'.$this->session->get('ses_comp_fy_id');
        $this->db->table($itemtrackn_tbl)->insert($data);
         
        return $this->db->insertID();
    }
    
    
    function add_batch_master($data)
    {
        $itmbatchmt_tbl = $this->company_id.'_itmbatchmt_'.$this->session->get('ses_comp_fy_id');
        $this->db->table($itmbatchmt_tbl)->insert($data);
         
        return $this->db->insertID();
    }
    function add_tracking_txn($data)
    {
	
      $itemtxnbal_tbl = $this->company_id.'_itemtxnoth_'.$this->session->get('ses_comp_fy_id');
		 if($this->session->get('ses_boid')!='')
			$bo_id =  $this->session->get('ses_boid');
		  else 
			$bo_id =1;
		$data['bo_id']=$bo_id;
        $this->db->table($itemtxnbal_tbl)->insert($data);

		
        // $this->update_bill_txn_balance($data['bills_ref_id']);
    }
    function add_batch_txn($data)
    {
	
      $itemtxnnnn_tbl = $this->company_id.'_itemtxnnnn_'.$data['item_id'].'_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!='')
			$bo_id =  $this->session->get('ses_boid');
		  else 
			$bo_id =1;
		$data['bo_id']=$bo_id;
        $this->db->table($itemtxnnnn_tbl)->insert($data);

		$this->update_item_unit_balance($data['item_id'], $data['item_unit'], $data['mat_cent_id'], $data['batch_id'], $data['item_avail']);
        
    }


    //---------------------------------------------------ADD METHODS END

 	public function GetUnqUnitList($item_id){
	    $final_units = array();
		
		$get_item_info = $this->get_item_info($item_id);
			
		
	    $itmoppybal_tbl = $this->company_id.'_itmoppybal_'.$this->session->get('ses_comp_fy_id');
		$builder        = $this->db->table($itmoppybal_tbl); 
		$builder->where('item_id', $item_id);
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
		$itmoppybal = $builder->get()->getResultArray();
    	
	  
	  
	  $itemtxnbal_tbl = $this->company_id.'_itemtxnbal_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
	  $builder        =  $this->db->table($itemtxnbal_tbl); 
      $builder->select('DISTINCT(item_unit) as item_unit');    		
      $builder->where('item_id', $item_id);
	  if($this->session->get('ses_boid')!='')
	  $builder->where('bo_id', $this->session->get('ses_boid'));

	  $result      = $builder->get()->getResultArray();
	 
	  if($result){
		  foreach($result as $row){
			    $final_units[$row['item_unit']] =0; 			  
		     }		  
	      }
	else if($itmoppybal){
		  foreach(	$itmoppybal as $oprow){
    		$final_units[$oprow['item_unit']]= 0;
    	   }
		
		}  
	 elseif($get_item_info){
		$final_units[$get_item_info['item_unit']]= 0; 
	 }	  
	    return $final_units;
	}	
   
  public function recursive_condition($open_unit_qty, $first_txn_dr_qty, $first_item_txn_id,$credit_unit_qty,$unit_id,$mc_id,$item_id){
	   $array=array();
	   $itemtxnnnn_tbl  = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
       $itemtxnbal_tbl  = $this->company_id.'_itemtxnbal_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');	
	   $builder         = $this->db->table($itemtxnnnn_tbl); 
	   $builder->join($itemtxnbal_tbl,$itemtxnbal_tbl.'.itemtxnbal_id='.$itemtxnnnn_tbl.'.itemtxnbal_id');  
	   $builder->where($itemtxnbal_tbl.'.item_unit', $unit_id);
	   $builder->where($itemtxnnnn_tbl.'.mat_cent_id', $mc_id);
	   $builder->where($itemtxnnnn_tbl.'.item_txn_drcr', 'd');
	   if($this->session->get('ses_boid')!=''){
		$builder->where($itemtxnnnn_tbl.'.bo_id', $this->session->get('ses_boid'));
		$builder->where($itemtxnbal_tbl.'.bo_id', $this->session->get('ses_boid'));
	   }

	   $builder->orderBy($itemtxnnnn_tbl.'.item_txn_date,'.$itemtxnnnn_tbl.'.item_txn_id');
	   $itemtxnnnn = $builder->get()->getResultArray();
		$next_txn_qty=0;	   
		$item_txn_qty=0;
		$status=0;		
				
		//echo 'first_txn_dr_qty => '.$open_unit_qty;
		//echo"\n";
		   if($itemtxnnnn){
			   foreach($itemtxnnnn as $row){$item_txn_qty_new=0;
				   $item_txn_qty  = $row['item_txn_qty']; $item_txn_id  = $row['item_txn_id'];
    			   $item_txn_drcr = $row['item_txn_drcr'];	      
				   $item_txn_amount  = $row['item_txn_amount'];
				   $first_txn_rate       = ($item_txn_amount/$item_txn_qty);
		           $item_txn_qty_new   =$item_txn_qty_new+$row['item_txn_qty'];
				   $checkbalanceqty = $open_unit_qty+$item_txn_qty+$next_txn_qty;
				  //echo $checkbalanceqty .'>'. $credit_unit_qty;
				  // echo '<br />';
					//echo '\n\n';
				   if( ($checkbalanceqty > $credit_unit_qty) ){
					  // echo 'if true break '.($checkbalanceqty-$credit_unit_qty);
					   // echo '<br />';
					   
					   $array[$item_txn_id]= array('item_txn_id'=>$item_txn_id,'qty'=>($checkbalanceqty-$credit_unit_qty),'rate'=>($item_txn_amount/$item_txn_qty),'amount'=>
					                ($checkbalanceqty-$credit_unit_qty)*($item_txn_amount/$item_txn_qty));
					   
					   //echo 'actual amount'. ($checkbalanceqty-$credit_unit_qty)*($item_txn_amount/$item_txn_qty);
					  // echo '\n\n';
					 //  echo'>>>>'.   $item_txn_qty.'-----'.($item_txn_amount/$item_txn_qty).'=>'.$item_txn_qty*($item_txn_amount/$item_txn_qty);
					 	
						break;
				   }
				   
				  
				   $next_txn_qty +=     $item_txn_qty;
			       }
        	}
			return  $array;
  }  
  
  public function backdatevaluation_calculation($item_id,$voucher_date){
	     $itemtxnnnn_tbl  = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		 $itemtxnbal_tbl  = $this->company_id.'_itemtxnbal_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		  
    	 $builder       = $this->db->table($itemtxnnnn_tbl); 
		 $builder->join($itemtxnbal_tbl,$itemtxnbal_tbl.'.itemtxnbal_id='.$itemtxnnnn_tbl.'.itemtxnbal_id');
		 $builder->where($itemtxnnnn_tbl.'.item_txn_date >=',date('Y-m-d',strtotime($voucher_date)));
		 if($this->session->get('ses_boid')!=''){
		$builder->where($itemtxnnnn_tbl.'.bo_id', $this->session->get('ses_boid'));
		$builder->where($itemtxnbal_tbl.'.bo_id', $this->session->get('ses_boid'));
	   }
	   
		 $itemtxnnnn      = $builder->get()->getResultArray();   		 
		 if($itemtxnnnn){
			 foreach($itemtxnnnn as $row){
				 $unit_id       = $row['item_unit'];
				 $mc_id         = $row['mat_cent_id'];
				 $itemtxnbal_id = $row['itemtxnbal_id'];
				 $drcr          = $row['item_txn_drcr'];
				 $this->CalculateValuation($item_id,$unit_id,$mc_id,0,$itemtxnbal_id,$drcr,$voucher_date);
				  $this->CalculateValuation($item_id,$unit_id,$mc_id,1,$itemtxnbal_id,$drcr,$voucher_date);
				 
			 }
		 }
  }
  

  //needs to change
  public function CalculateValuation($item_id,$unit_id,$mc_id,$method_id,$itemtxnbal_id,$drcr,$voucher_date){
	  
	   /********************************** CALCULATE AVG COST AND VALUATION  SALE & PURCHASE***************************************/
	    $open_unit_qty       = $this->GetUnqUnitList($item_id);
		$open_unit_amount    = $this->GetUnqUnitList($item_id);
	    $debit_unit_qty      = $this->GetUnqUnitList($item_id);
		$debit_unit_amount   = $this->GetUnqUnitList($item_id);
	    $credit_unit_qty     = $this->GetUnqUnitList($item_id);
		
		$item_bal_tbl =  $this->company_id.'_itemtxnbal_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
	    $builder = $this->db->table($item_bal_tbl);
		$builder->where('itemtxnbal_id', $itemtxnbal_id);
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
		
		$data2 = $builder->get()->getRowArray();
		if($data2)
		  $item_bal_qty = $data2['item_bal_qty'];
	    else 
		  $item_bal_qty = 0;	
		  
		
		/*************  in case of sale ******************/
		
	if($drcr=='c'){
		
		
		if($method_id==0){
		
		 $itemtxnvaln_tbl  = $this->company_id.'_itemtxnvaln_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');	
		 $builder          = $this->db->table($itemtxnvaln_tbl); 
		 $builder->orderBy('valuation_id','DESC');
		 $builder->limit(1);
	   	 $itemtxnnnn       = $builder->get()->getRowArray(); 	
		 if($itemtxnnnn){
			  // if last valuation exists for the item then
			 $avg_rate   = $itemtxnnnn['avg_cost'];			 
			 $final_valuation = parseAmount($item_bal_qty*$avg_rate);	  
			 // echo "<br> final valuation  item bal qty * avg rae => ".$final_valuation;			  
			  $itemtxnvaln_tbl = $this->company_id.'_itemtxnvaln_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
			  $insert_data      = array("item_id"=>$item_id,"itemtxnbal_id"=>$itemtxnbal_id,
										"method_id"=>$method_id,"item_value"=>$final_valuation,"avg_cost"=>$avg_rate
										);
										
			  							
			  $is_exists = $this->db->table($itemtxnvaln_tbl)->where('itemtxnbal_id', $itemtxnbal_id)->where('item_id', $item_id)->where('method_id', $method_id)->orderBy('valuation_id')->countAllResults();							
			  if($is_exists==0)							
				$this->db->table($itemtxnvaln_tbl)->insert($insert_data);
				else{
				$update_data = array("itemtxnbal_id"=>$itemtxnbal_id,"item_value"=>$final_valuation,"avg_cost"=>$avg_rate);
				$this->db->table($itemtxnvaln_tbl)->where('itemtxnbal_id', $itemtxnbal_id)->where('item_id', $item_id)->where('method_id', $method_id)->update($update_data);
				}				 
			  }
		}	
	  if($method_id==1){
		 $amount_balance = 0;
		$itmoppyval_tbl = $this->company_id.'_itmoppyval_'.$this->session->get('ses_comp_fy_id');
		$builder        = $this->db->table($itmoppyval_tbl); 
		$builder->where('item_id', $item_id);
		$builder->where('item_unit', $unit_id);
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
		$builder->where('mat_cent_id', $mc_id);
		
    	$itmoppyval = $builder->get()->getRowArray();
    	if($itmoppyval){
    		$amount_balance = parseAmount($itmoppyval['op_bal_val']);
			$open_unit_amount[$unit_id]=$open_unit_amount[$unit_id]+$amount_balance;
    	  }
		
	    $qty_balance    = 0;
		$itmoppybal_tbl = $this->company_id.'_itmoppybal_'.$this->session->get('ses_comp_fy_id');
		$builder        = $this->db->table($itmoppybal_tbl); 
		$builder->where('item_id', $item_id);
		$builder->where('item_unit', $unit_id);		
		$builder->where('mat_cent_id', $mc_id);
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
		
    	$itmoppybal = $builder->get()->getRowArray();
    	if($itmoppybal){
    		$qty_balance = $itmoppybal['op_bal_qty'];  // upto 4 decimal
			$open_unit_qty[$unit_id]=$open_unit_qty[$unit_id]+$qty_balance;
    	}
		
		  
		   $itemtxnnnn_tbl  = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		    $itemtxnbal_tbl  = $this->company_id.'_itemtxnbal_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		   $builder         = $this->db->table($itemtxnnnn_tbl); 
		   $builder->join($itemtxnbal_tbl,$itemtxnbal_tbl.'.itemtxnbal_id='.$itemtxnnnn_tbl.'.itemtxnbal_id');
		   $builder->where($itemtxnbal_tbl.'.item_unit', $unit_id);
		   $builder->where($itemtxnnnn_tbl.'.mat_cent_id', $mc_id);
		   if($this->session->get('ses_boid')!=''){
			$builder->where($itemtxnnnn_tbl.'.bo_id', $this->session->get('ses_boid'));
			$builder->where($itemtxnbal_tbl.'.bo_id', $this->session->get('ses_boid'));
		}
	   
		   $builder->orderBy($itemtxnnnn_tbl.'.item_txn_date,'.$itemtxnnnn_tbl.'.item_txn_id');
	   	   $itemtxnnnn      = $builder->get()->getResultArray();          
		   if($itemtxnnnn){
			   foreach($itemtxnnnn as $row){
				   $item_txn_qty  = $row['item_txn_qty'];
    			   $item_txn_drcr = $row['item_txn_drcr'];	                   			   
				   if($item_txn_drcr == 'c'){
					     $credit_unit_qty[$unit_id]=$credit_unit_qty[$unit_id]+$item_txn_qty;						 				 
				         }
			       }
        	}
		//echo ' SUM OF TXNNN QTY CR<pre>';
		//print_r($credit_unit_qty);
		//echo "\n";
		
		//echo "OP. BAL > POINT NO. 2";
		if($open_unit_qty[$unit_id] > $credit_unit_qty[$unit_id]){
			//echo "TRUE";
			//echo "\n";
		  // echo 'case open_unit_qty > credit_unit_qty';	
          //echo "\n";		   
	       $sub_item_txn_qty    = 0;   
		   $sub_item_txn_amount = 0;   			  
		   $fifo_rate           =  ($open_unit_amount[$unit_id]/$open_unit_qty[$unit_id]);
		   $fifo_qty            =  ($open_unit_qty[$unit_id]-$credit_unit_qty[$unit_id]);
		   $fifo_opn_amount     =  $fifo_qty*$fifo_rate;
			 // echo $fifo_qty .' =>'.$fifo_rate.'=='.$fifo_opn_amount;
			  //echo "\n";
		   $itemtxnnnn_tbl  = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		   $itemtxnbal_tbl  = $this->company_id.'_itemtxnbal_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		   $builder         = $this->db->table($itemtxnnnn_tbl); 
		   $builder->join($itemtxnbal_tbl,$itemtxnbal_tbl.'.itemtxnbal_id='.$itemtxnnnn_tbl.'.itemtxnbal_id');
		   $builder->where($itemtxnbal_tbl.'.item_unit', $unit_id);
		   $builder->where($itemtxnnnn_tbl.'.mat_cent_id', $mc_id);
		   if($this->session->get('ses_boid')!=''){
			$builder->where($itemtxnnnn_tbl.'.bo_id', $this->session->get('ses_boid'));
			$builder->where($itemtxnbal_tbl.'.bo_id', $this->session->get('ses_boid'));
		}
	   
		   $builder->where($itemtxnnnn_tbl.'.item_txn_drcr', 'd');
		   $builder->orderBy($itemtxnnnn_tbl.'.item_txn_date,'.$itemtxnnnn_tbl.'.item_txn_id');
	   	   $itemtxnnnn      = $builder->get()->getResultArray();	
		   if($itemtxnnnn){
			   foreach($itemtxnnnn as $row){
				          $sub_item_txn_qty  = $sub_item_txn_qty+$row['item_txn_qty'];
						  $sub_item_txn_amount  =$fifo_opn_amount+ $sub_item_txn_amount+$row['item_txn_amount'];
						  
						   //echo $row['item_txn_qty'] .' =>'.($row['item_txn_amount']/$row['item_txn_qty']).'=='.$row['item_txn_amount'];
						  // echo "\n";
		             }
			  }
		   //  var_dump($sub_item_txn_amount);
			 
			 $fifo_qty_sum    =  $fifo_qty+$sub_item_txn_qty;
			 $fifo_amount_sum =  $sub_item_txn_amount;
			 $valuation_rate  = number_format(($fifo_amount_sum/$fifo_qty_sum), 4, '.', '');
			
			 $itemtxnvaln_tbl = $this->company_id.'_itemtxnvaln_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
			  $insert_data      = array("item_id"=>$item_id,"itemtxnbal_id"=>$itemtxnbal_id,
										"method_id"=>$method_id,"item_value"=>$fifo_amount_sum,"avg_cost"=>$valuation_rate
										);

			 $is_exists = $this->db->table($itemtxnvaln_tbl)->where('itemtxnbal_id', $itemtxnbal_id)->where('item_id', $item_id)->where('method_id', $method_id)->orderBy('valuation_id')->countAllResults();							
			  if($is_exists==0)							
				 $this->db->table($itemtxnvaln_tbl)->insert($insert_data);
			  else{
			    $update_data = array("itemtxnbal_id"=>$itemtxnbal_id,"item_value"=>$fifo_amount_sum,"avg_cost"=>$valuation_rate);
				 $this->db->table($itemtxnvaln_tbl)->where('itemtxnbal_id', $itemtxnbal_id)->where('item_id', $item_id)->where('method_id', $method_id)->update($update_data);
			     } 
		}
		else{
			//echo " FALSE";
			//echo "\n";
			//echo 'esle part case open_unit_qty < credit_unit_qty';
			
			$itemtxnnnn_tbl  = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		    $itemtxnbal_tbl  = $this->company_id.'_itemtxnbal_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		   $builder         = $this->db->table($itemtxnnnn_tbl); 
		   $builder->join($itemtxnbal_tbl,$itemtxnbal_tbl.'.itemtxnbal_id='.$itemtxnnnn_tbl.'.itemtxnbal_id');
		   $builder->where($itemtxnbal_tbl.'.item_unit', $unit_id);
		   $builder->where($itemtxnnnn_tbl.'.mat_cent_id', $mc_id);
		   if($this->session->get('ses_boid')!=''){
			$builder->where($itemtxnnnn_tbl.'.bo_id', $this->session->get('ses_boid'));
			$builder->where($itemtxnbal_tbl.'.bo_id', $this->session->get('ses_boid'));
			}
	   
		   $builder->where($itemtxnnnn_tbl.'.item_txn_drcr', 'd');
		   $builder->orderBy($itemtxnnnn_tbl.'.item_txn_date,'.$itemtxnnnn_tbl.'.item_txn_id');
		   $itemtxnnnn_row      = $builder->get()->getRowArray();
		   if($itemtxnnnn_row){
		   // OP. BAL + 1ST TXN DR.
		    $first_item_txn_id   = $itemtxnnnn_row['item_txn_id']; 
		   $first_txn_dr_qty     = $itemtxnnnn_row['item_txn_qty'];
		   $first_txn_dr_amount  = $itemtxnnnn_row['item_txn_amount'];
		   $first_txn_rate       = ($first_txn_dr_amount/$first_txn_dr_qty);
		   
		   }
		   else{
			   $first_item_txn_id=$first_txn_dr_qty=$first_txn_dr_amount=$first_txn_rate=0;
			   
		   }
		   //echo ' OP. BAL + 1ST TXN DR. => '.($open_unit_qty[$unit_id]+$first_txn_dr_qty);
		// echo "\n";
		 
		   $fifo_valuation=0;
		   $fifo_qty=0;
		  // echo " OP. BAL + 1ST TXN DR." .($open_unit_qty[$unit_id]+$first_txn_dr_qty).'>'.($credit_unit_qty[$unit_id]).'  IF TRUE OR FALSE';
		  //  echo "\n";
		   if(($open_unit_qty[$unit_id]+$first_txn_dr_qty)>($credit_unit_qty[$unit_id])){
				 $fifo_qty  =  ($open_unit_qty[$unit_id]+$first_txn_dr_qty)-($credit_unit_qty[$unit_id]);
				 $fifo_valuation  = $first_txn_rate*$fifo_qty;
				
				
				//echo "1ST LEVEL OF FIFO VALUATION => " .$fifo_qty;  
				//echo "\n";
				
				//echo "IF FALSE (OP. BAL + 1ST TXN DR + 2ND TXN DR > POINT NO. 2) ";
			// var_dump($fifo_qty);
		
		   $item_txn_amoun=0;$item_txn_qtyn=0;$item_txn_amount=0;
		   $itemtxnnnn_tbl  = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		   $itemtxnbal_tbl  = $this->company_id.'_itemtxnbal_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		   $builder         = $this->db->table($itemtxnnnn_tbl); 
		   $builder->join($itemtxnbal_tbl,$itemtxnbal_tbl.'.itemtxnbal_id='.$itemtxnnnn_tbl.'.itemtxnbal_id');
		   $builder->where($itemtxnbal_tbl.'.item_unit', $unit_id);
		   $builder->where($itemtxnnnn_tbl.'.mat_cent_id', $mc_id);
		   if($this->session->get('ses_boid')!=''){
			$builder->where($itemtxnnnn_tbl.'.bo_id', $this->session->get('ses_boid'));
			$builder->where($itemtxnbal_tbl.'.bo_id', $this->session->get('ses_boid'));
		 }
	   
		   $builder->where($itemtxnnnn_tbl.'.item_txn_drcr', 'd');
		   $builder->orderBy($itemtxnnnn_tbl.'.item_txn_date,'.$itemtxnnnn_tbl.'.item_txn_id');
		   if($first_item_txn_id >0)
		    $builder->where($itemtxnnnn_tbl.'.item_txn_id !=', $first_item_txn_id);
	   	   $itemtxnnnn      = $builder->get()->getResultArray();	   
		   if($itemtxnnnn){
			   foreach($itemtxnnnn as $row){
				          $item_txn_qtyn  =$item_txn_qtyn+ $row['item_txn_qty'];						
						  $item_txn_amount  = $item_txn_amount+$row['item_txn_amount'];
			            }
				      }			
					  
				$fifo_amount_sum = ($item_txn_amount+$fifo_valuation);
			    $valuation_rate = ($item_txn_amount+$fifo_valuation)/($fifo_qty+$item_txn_qtyn);
				
				$valuation_rate = 	number_format($valuation_rate, 4, '.', '');
				
				 $itemtxnvaln_tbl = $this->company_id.'_itemtxnvaln_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
			 $insert_data      = array("item_id"=>$item_id,"itemtxnbal_id"=>$itemtxnbal_id,
									"method_id"=>$method_id,"item_value"=>$fifo_amount_sum,"avg_cost"=>$valuation_rate
								    );
	
									
			  $is_exists = $this->db->table($itemtxnvaln_tbl)->where('itemtxnbal_id', $itemtxnbal_id)->where('item_id', $item_id)->where('method_id', $method_id)->orderBy('valuation_id')->countAllResults();							
			 if($is_exists==0)							
				 $this->db->table($itemtxnvaln_tbl)->insert($insert_data);
				else{
				 $update_data = array("itemtxnbal_id"=>$itemtxnbal_id,"item_value"=>$fifo_amount_sum,"avg_cost"=>$valuation_rate);
				 $this->db->table($itemtxnvaln_tbl)->where('itemtxnbal_id', $itemtxnbal_id)->where('item_id', $item_id)->where('method_id', $method_id)->update($update_data);
				} 
				
			    }
			else{
				//echo 'else recursive part ';
				//recursive loop to match condition OP. BAL + 1ST TXN DR. > credit side sum 
				
				$recursive_resposne = $this->recursive_condition($open_unit_qty[$unit_id], $first_txn_dr_qty, $first_item_txn_id,$credit_unit_qty[$unit_id],$unit_id,$mc_id,$item_id);
				
				
				$item_txn_amoun=0;
				$final_valuation_qty=0;
				$final_valuation_amount=0;
				$itemtxnnnn_tbl  = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
			   $itemtxnbal_tbl  = $this->company_id.'_itemtxnbal_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
			   $builder         = $this->db->table($itemtxnnnn_tbl); 
			   $builder->join($itemtxnbal_tbl,$itemtxnbal_tbl.'.itemtxnbal_id='.$itemtxnnnn_tbl.'.itemtxnbal_id');
			   $builder->where($itemtxnbal_tbl.'.item_unit', $unit_id);
			   $builder->where($itemtxnnnn_tbl.'.mat_cent_id', $mc_id);
			   if($this->session->get('ses_boid')!=''){
				$builder->where($itemtxnnnn_tbl.'.bo_id', $this->session->get('ses_boid'));
				$builder->where($itemtxnbal_tbl.'.bo_id', $this->session->get('ses_boid'));
				}
		
			   $builder->where($itemtxnnnn_tbl.'.item_txn_drcr', 'd');
			   $builder->orderBy($itemtxnnnn_tbl.'.item_txn_date,'.$itemtxnnnn_tbl.'.item_txn_id');
				$builder->where($itemtxnnnn_tbl.'.item_txn_id !=', $first_item_txn_id);
			   $itemtxnnnn      = $builder->get()->getResultArray();	   
			if($itemtxnnnn){
			   foreach($itemtxnnnn as $row){
						  $item_txn_id  = $row['item_txn_id'];
						  if(!isset($recursive_resposne[$item_txn_id])){
							 
								   $final_valuation_qty  +=$row['item_txn_qty'];
								   $final_valuation_amount +=$row['item_txn_amount'];
							    }
			               }
				      }	
			  
				
				if($recursive_resposne ){
					foreach($recursive_resposne  as $itemtxnid => $row){
						$final_valuation_qty +=$row['qty'];
						$final_valuation_amount +=$row['amount'];
					}
					
				}
			   
			  
			     if($final_valuation_qty >0)
			    $valuation_rate = ($final_valuation_amount/$final_valuation_qty);
			    else
					$valuation_rate = 0;
			   $valuation_rate = 	number_format($valuation_rate, 4, '.', '');
			   
			   
			    $final_valuation_amount = 	number_format($final_valuation_amount, 4, '.', '');
			
			 $itemtxnvaln_tbl = $this->company_id.'_itemtxnvaln_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
			 $insert_data      = array("item_id"=>$item_id,"itemtxnbal_id"=>$itemtxnbal_id,
									"method_id"=>$method_id,"item_value"=>$final_valuation_amount,"avg_cost"=>$valuation_rate
								    );
	
									
			  $is_exists = $this->db->table($itemtxnvaln_tbl)->where('itemtxnbal_id', $itemtxnbal_id)->where('item_id', $item_id)->where('method_id', $method_id)->orderBy('valuation_id')->countAllResults();							
			 if($is_exists==0)							
				 $this->db->table($itemtxnvaln_tbl)->insert($insert_data);
				else{
				 $update_data = array("itemtxnbal_id"=>$itemtxnbal_id,"item_value"=>$final_valuation_amount,"avg_cost"=>$valuation_rate);
				 $this->db->table($itemtxnvaln_tbl)->where('itemtxnbal_id', $itemtxnbal_id)->where('item_id', $item_id)->where('method_id', $method_id)->update($update_data);
				} 
			}
			
				
				
			
			
		}
		
	  }
		
		
	}
	if($drcr=='d'){
		
		//echo "Item bal qty =>".$item_bal_qty;
		//echo '<br>';
		
		$final_valuation=0;
		if($method_id==0){
		// All amount sum unit wise
		$amount_balance = 0;
		$itmoppyval_tbl = $this->company_id.'_itmoppyval_'.$this->session->get('ses_comp_fy_id');
		$builder        = $this->db->table($itmoppyval_tbl); 
		$builder->where('item_id', $item_id);
		$builder->where('item_unit', $unit_id);
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
		$builder->where('mat_cent_id', $mc_id);
		
    	$itmoppyval = $builder->get()->getRowArray();
    	if($itmoppyval){
    		$amount_balance = parseAmount($itmoppyval['op_bal_val']);
			$debit_unit_amount[$unit_id]=$debit_unit_amount[$unit_id]+$amount_balance;
    	  }
		// All qty sub unit wise  
	    $qty_balance    = 0;
		$itmoppybal_tbl = $this->company_id.'_itmoppybal_'.$this->session->get('ses_comp_fy_id');
		$builder        = $this->db->table($itmoppybal_tbl); 
		$builder->where('item_id', $item_id);
		$builder->where('item_unit', $unit_id);		
		if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));		
		$builder->where('mat_cent_id', $mc_id);
		
    	$itmoppybal = $builder->get()->getRowArray();
    	if($itmoppybal){
    		$qty_balance = $itmoppybal['op_bal_qty'];  // upto 4 decimal
			$debit_unit_qty[$unit_id]=$debit_unit_qty[$unit_id]+$qty_balance;
    	}
        
         $itemtxnnnn_tbl  = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		  $itemtxnbal_tbl  = $this->company_id.'_itemtxnbal_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		  
    	 $builder         = $this->db->table($itemtxnnnn_tbl); 
		 $builder->join($itemtxnbal_tbl,$itemtxnbal_tbl.'.itemtxnbal_id='.$itemtxnnnn_tbl.'.itemtxnbal_id');
		 $builder->where($itemtxnbal_tbl.'.item_unit', $unit_id);
		 $builder->where($itemtxnnnn_tbl.'.mat_cent_id', $mc_id);
		 if($this->session->get('ses_boid')!=''){
		  $builder->where($itemtxnnnn_tbl.'.bo_id', $this->session->get('ses_boid'));
		  $builder->where($itemtxnbal_tbl.'.bo_id', $this->session->get('ses_boid'));
	     }
	   
	   	 $itemtxnnnn      = $builder->get()->getResultArray();          
		  if($itemtxnnnn){
			   foreach($itemtxnnnn as $row){
				   $item_txn_qty  = $row['item_txn_qty'];
				   if($item_txn_qty=='')
					    $item_txn_qty=1;
    			   $item_txn_drcr = $row['item_txn_drcr'];	
                   $item_txn_balance  = $row['item_txn_amount']; 				   
				   if($item_txn_drcr == 'd'){
					     $debit_unit_qty[$unit_id]=$debit_unit_qty[$unit_id]+$item_txn_qty;
						 $debit_unit_amount[$unit_id]=$debit_unit_amount[$unit_id]+$item_txn_balance;						 
				         }
			       }
        	}
	  
	  //echo "<br> Total Debit Qty with Opening Qty => ".$debit_unit_qty[$unit_id];
	  //echo "<br> Total Debit Amount with Opening Amount or valua => ".$debit_unit_amount[$unit_id];
	  if(isset($debit_unit_qty[$unit_id]))
	  $debit_qty       = $debit_unit_qty[$unit_id];
     else 
		$debit_qty    = 1;
	if(isset($debit_unit_amount[$unit_id]))
	  $debit_amount    = $debit_unit_amount[$unit_id];	
    else 
		$debit_amount    =0;
	  if($debit_qty==0)
		  $debit_qty=1;
	  $avg_rate        = number_format(($debit_amount/$debit_qty), 4, '.', '');
	  //echo "<br> Avg Rate debit amount / debit qty => ". $avg_rate;
	  $final_valuation = parseAmount(($item_bal_qty*$avg_rate));
	 // echo "<br> final valuation  item bal qty * avg rae => ".$final_valuation;
	  $itemtxnvaln_tbl = $this->company_id.'_itemtxnvaln_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
	  $insert_data      = array("item_id"=>$item_id,"itemtxnbal_id"=>$itemtxnbal_id,
	                            "method_id"=>$method_id,"item_value"=>$final_valuation,"avg_cost"=>$avg_rate
								);
	  $is_exists = $this->db->table($itemtxnvaln_tbl)->where('itemtxnbal_id', $itemtxnbal_id)->where('item_id', $item_id)->where('method_id', $method_id)->orderBy('valuation_id')->countAllResults();							
	  if($is_exists==0)							
		  $this->db->table($itemtxnvaln_tbl)->insert($insert_data);
	    else{
		  $update_data = array("itemtxnbal_id"=>$itemtxnbal_id,"item_value"=>$final_valuation,"avg_cost"=>$avg_rate);
		  $this->db->table($itemtxnvaln_tbl)->where('itemtxnbal_id', $itemtxnbal_id)->where('item_id', $item_id)->where('method_id', $method_id)->update($update_data);
		 }
	  
	}	
	if($method_id==1){
		 $amount_balance = 0;
		$itmoppyval_tbl = $this->company_id.'_itmoppyval_'.$this->session->get('ses_comp_fy_id');
		$builder        = $this->db->table($itmoppyval_tbl); 
		$builder->where('item_id', $item_id);
		$builder->where('item_unit', $unit_id);
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
		$builder->where('mat_cent_id', $mc_id);
		
    	$itmoppyval = $builder->get()->getRowArray();
    	if($itmoppyval){
    		$amount_balance = parseAmount($itmoppyval['op_bal_val']);
			$open_unit_amount[$unit_id]=$open_unit_amount[$unit_id]+$amount_balance;
    	  }
		
	    $qty_balance    = 0;
		$itmoppybal_tbl = $this->company_id.'_itmoppybal_'.$this->session->get('ses_comp_fy_id');
		$builder        = $this->db->table($itmoppybal_tbl); 
		$builder->where('item_id', $item_id);
		$builder->where('item_unit', $unit_id);		
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
		$builder->where('mat_cent_id', $mc_id);
		
    	$itmoppybal = $builder->get()->getRowArray();
    	if($itmoppybal){
    		$qty_balance = $itmoppybal['op_bal_qty'];  // upto 4 decimal
			$open_unit_qty[$unit_id]=$open_unit_qty[$unit_id]+$qty_balance;
    	}
		
		  
		   $itemtxnnnn_tbl  = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		    $itemtxnbal_tbl  = $this->company_id.'_itemtxnbal_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		   $builder         = $this->db->table($itemtxnnnn_tbl); 
		   $builder->join($itemtxnbal_tbl,$itemtxnbal_tbl.'.itemtxnbal_id='.$itemtxnnnn_tbl.'.itemtxnbal_id');
		   $builder->where($itemtxnbal_tbl.'.item_unit', $unit_id);
		   $builder->where($itemtxnnnn_tbl.'.mat_cent_id', $mc_id);
			if($this->session->get('ses_boid')!=''){
			$builder->where($itemtxnnnn_tbl.'.bo_id', $this->session->get('ses_boid'));
			$builder->where($itemtxnbal_tbl.'.bo_id', $this->session->get('ses_boid'));
		}
	   
		   $builder->orderBy($itemtxnnnn_tbl.'.item_txn_date,'.$itemtxnnnn_tbl.'.item_txn_id');
	   	   $itemtxnnnn      = $builder->get()->getResultArray();          
		   if($itemtxnnnn){
			   foreach($itemtxnnnn as $row){
				   $item_txn_qty  = $row['item_txn_qty'];
    			   $item_txn_drcr = $row['item_txn_drcr'];	                   			   
				   if($item_txn_drcr == 'c'){
					   if($item_txn_qty=='')
						    $item_txn_qty=1;
					     $credit_unit_qty[$unit_id]=$credit_unit_qty[$unit_id]+$item_txn_qty;						 				 
				         }
			       }
        	}
		
		if($open_unit_qty[$unit_id] > $credit_unit_qty[$unit_id]){
			//echo 'case open_unit_qty > credit_unit_qty';			
	       $item_txn_qty        = $row['item_txn_qty'];
		   $item_txn_amount     = $row['item_txn_amount'];
		   $sub_item_txn_qty    = 0;   
		   $sub_item_txn_amount = 0;   			  
		   $fifo_rate           = ($open_unit_amount[$unit_id]/$open_unit_qty[$unit_id]);
		   $fifo_qty            =  ($open_unit_qty[$unit_id]-$credit_unit_qty[$unit_id]);
		   $fifo_opn_amount     = $fifo_qty*$fifo_rate;
			  
		   $itemtxnnnn_tbl  = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		   $itemtxnbal_tbl  = $this->company_id.'_itemtxnbal_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		   $builder         = $this->db->table($itemtxnnnn_tbl); 
		   $builder->join($itemtxnbal_tbl,$itemtxnbal_tbl.'.itemtxnbal_id='.$itemtxnnnn_tbl.'.itemtxnbal_id');
		   $builder->where($itemtxnbal_tbl.'.item_unit', $unit_id);
		   $builder->where($itemtxnnnn_tbl.'.mat_cent_id', $mc_id);
		   if($this->session->get('ses_boid')!=''){
		$builder->where($itemtxnnnn_tbl.'.bo_id', $this->session->get('ses_boid'));
		$builder->where($itemtxnbal_tbl.'.bo_id', $this->session->get('ses_boid'));
	   }
	   
		   $builder->where($itemtxnnnn_tbl.'.item_txn_drcr', 'd');
		   $builder->orderBy($itemtxnnnn_tbl.'.item_txn_date,'.$itemtxnnnn_tbl.'.item_txn_id');
	   	   $itemtxnnnn      = $builder->get()->getResultArray();	
		   if($itemtxnnnn){
			   foreach($itemtxnnnn as $row){
				   if($row['item_txn_qty']=='')
					    $row['item_txn_qty']=1;
				          $sub_item_txn_qty  = $sub_item_txn_qty+$row['item_txn_qty'];
						  $sub_item_txn_amount  =$fifo_opn_amount+ $sub_item_txn_amount+$row['item_txn_amount'];
		             }
			  }
		     
			 $fifo_qty_sum    =  $fifo_qty+$sub_item_txn_qty;
			 $fifo_amount_sum =  $sub_item_txn_amount;
			 $valuation_rate  = number_format(($fifo_amount_sum/$fifo_qty_sum), 4, '.', '');
			
			 $itemtxnvaln_tbl = $this->company_id.'_itemtxnvaln_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
			  $insert_data      = array("item_id"=>$item_id,"itemtxnbal_id"=>$itemtxnbal_id,
										"method_id"=>$method_id,"item_value"=>$fifo_amount_sum,"avg_cost"=>$valuation_rate
										);
			  $is_exists = $this->db->table($itemtxnvaln_tbl)->where('itemtxnbal_id', $itemtxnbal_id)->where('item_id', $item_id)->where('method_id', $method_id)->orderBy('valuation_id')->countAllResults();							
			  if($is_exists==0)							
				 $this->db->table($itemtxnvaln_tbl)->insert($insert_data);
			  else{
			    $update_data = array("itemtxnbal_id"=>$itemtxnbal_id,"item_value"=>$fifo_amount_sum,"avg_cost"=>$valuation_rate);
				 $this->db->table($itemtxnvaln_tbl)->where('itemtxnbal_id', $itemtxnbal_id)->where('item_id', $item_id)->where('method_id', $method_id)->update($update_data);
			     }
		}
		else{
			//echo 'esle part case open_unit_qty < credit_unit_qty';
			
			$itemtxnnnn_tbl  = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		    $itemtxnbal_tbl  = $this->company_id.'_itemtxnbal_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		   $builder         = $this->db->table($itemtxnnnn_tbl); 
		   $builder->join($itemtxnbal_tbl,$itemtxnbal_tbl.'.itemtxnbal_id='.$itemtxnnnn_tbl.'.itemtxnbal_id');
		   $builder->where($itemtxnbal_tbl.'.item_unit', $unit_id);
		   $builder->where($itemtxnnnn_tbl.'.mat_cent_id', $mc_id);
		   if($this->session->get('ses_boid')!=''){
		$builder->where($itemtxnnnn_tbl.'.bo_id', $this->session->get('ses_boid'));
		$builder->where($itemtxnbal_tbl.'.bo_id', $this->session->get('ses_boid'));
	   }
	   
		   $builder->where($itemtxnnnn_tbl.'.item_txn_drcr', 'd');
		   $builder->orderBy($itemtxnnnn_tbl.'.item_txn_date,'.$itemtxnnnn_tbl.'.item_txn_id');
		   $itemtxnnnn_row      = $builder->get()->getRowArray();
		   if($itemtxnnnn_row){
		  
		   // OP. BAL + 1ST TXN DR.
		    $first_item_txn_id  = $itemtxnnnn_row['item_txn_id']; 
		   $first_txn_dr_qty  = $itemtxnnnn_row['item_txn_qty'];
		   if($first_txn_dr_qty=='')
			    $first_txn_dr_qty=1;
		   $first_txn_dr_amount  = $itemtxnnnn_row['item_txn_amount'];
		   $first_txn_rate  = ($first_txn_dr_amount/$first_txn_dr_qty);
		   
		   }else{
			  $first_txn_dr_qty= $first_item_txn_id = $first_txn_dr_amount =$first_txn_rate=0;
			   
		   }
		   
		$fifo_qty =$fifo_valuation=0;
		   if(($open_unit_qty[$unit_id]+$first_txn_dr_qty)>($credit_unit_qty[$unit_id])){
				  $fifo_qty  =  ($open_unit_qty[$unit_id]+$first_txn_dr_qty)-($credit_unit_qty[$unit_id]);
				  $fifo_valuation  = $first_txn_rate*$fifo_qty;
			    }
			
		
		   $item_txn_amoun=0;$item_txn_qtyn=0;$item_txn_amount=0;
		   $itemtxnnnn_tbl  = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		   $itemtxnbal_tbl  = $this->company_id.'_itemtxnbal_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		   $builder         = $this->db->table($itemtxnnnn_tbl); 
		   $builder->join($itemtxnbal_tbl,$itemtxnbal_tbl.'.itemtxnbal_id='.$itemtxnnnn_tbl.'.itemtxnbal_id');
		   $builder->where($itemtxnbal_tbl.'.item_unit', $unit_id);
		   $builder->where($itemtxnnnn_tbl.'.mat_cent_id', $mc_id);
		   if($this->session->get('ses_boid')!=''){
		$builder->where($itemtxnnnn_tbl.'.bo_id', $this->session->get('ses_boid'));
		$builder->where($itemtxnbal_tbl.'.bo_id', $this->session->get('ses_boid'));
	   }
	   
		   $builder->where($itemtxnnnn_tbl.'.item_txn_drcr', 'd');
		   $builder->orderBy($itemtxnnnn_tbl.'.item_txn_date,'.$itemtxnnnn_tbl.'.item_txn_id');
		   if($first_item_txn_id>0)
		    $builder->where($itemtxnnnn_tbl.'.item_txn_id !=', $first_item_txn_id);
	   	   $itemtxnnnn      = $builder->get()->getResultArray();	   
		   if($itemtxnnnn){
			   foreach($itemtxnnnn as $row){
				   if($row['item_txn_qty']=='')
					    $row['item_txn_qty']=1;
				          $item_txn_qtyn  =$item_txn_qtyn+ $row['item_txn_qty'];						
						  $item_txn_amount  = $item_txn_amount+$row['item_txn_amount'];
			            }
				      }
				$fifo_amount_sum = ($item_txn_amount+$fifo_valuation);
			    if($fifo_qty >0 && $item_txn_qtyn >0)
			    $valuation_rate = ($item_txn_amount+$fifo_valuation)/($fifo_qty+$item_txn_qtyn);
			    else
					$valuation_rate = 0;
				
				
			$valuation_rate = 	number_format($valuation_rate, 4, '.', '');
			
			 $itemtxnvaln_tbl = $this->company_id.'_itemtxnvaln_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
			 $insert_data      = array("item_id"=>$item_id,"itemtxnbal_id"=>$itemtxnbal_id,
									"method_id"=>$method_id,"item_value"=>$fifo_amount_sum,"avg_cost"=>$valuation_rate
								    );
			  $is_exists = $this->db->table($itemtxnvaln_tbl)->where('itemtxnbal_id', $itemtxnbal_id)->where('item_id', $item_id)->where('method_id', $method_id)->orderBy('valuation_id')->countAllResults();							
			 if($is_exists==0)							
				 $this->db->table($itemtxnvaln_tbl)->insert($insert_data);
				else{
				 $update_data = array("itemtxnbal_id"=>$itemtxnbal_id,"item_value"=>$fifo_amount_sum,"avg_cost"=>$valuation_rate);
				 $this->db->table($itemtxnvaln_tbl)->where('itemtxnbal_id', $itemtxnbal_id)->where('item_id', $item_id)->where('method_id', $method_id)->update($update_data);
				} 
			
		}
		
	  }			
			
		}
	
    // update all valuation if in back date entry
	
	
  }	
    
  public function add_batch_value($data){
	 $batchval_tbl = $this->company_id.'_itmbatchmt_'.$this->session->get('ses_comp_fy_id');    
	 $this->db->table($batchval_tbl)->insert($data);
     return $this->db->insertID();	 
	}
   public function update_batch_value($update_data,$batch_id){
	    $table_name = $this->company_id.'_itmbatchmt_'.$this->session->get('ses_comp_fy_id'); 
	    $this->db->table($table_name)->where('batch_id',$batch_id)->update($update_data);
   }
		
 public function get_item_balance($itemid,$voucher_date,$unit_id){
	 if($unit_id!='' && $itemid!=''){
        $item_unit_info = $this->item_unit_info($unit_id);
		
		$json_array = array();
	  /*  $balance_string ='[[UNITNAME]]
		AVAILABLE : [[AVAILABLEQTY]]
		PACKED : [[PACKEDQTY]]
		OBSELETE: [[OBSELETEQTY]]
		IN TRANSIT: [[INTRANSITQTY]]'; */
		
		if($item_unit_info)
                	$item_unit_name = $this->enc_string->nc_string($item_unit_info['item_unit'],'de');				 
              	else
                	$item_unit_name ='' ;
				
        $itm_txn_tbl = $this->company_id.'_itemtxnbal_'.$itemid.'_'.$this->session->get('ses_comp_fy_id');
      	
		if($this->session->get('ses_boid')!=''){
		$result = $this->db->table($itm_txn_tbl)
		                    ->select('(CASE WHEN item_avail =1 THEN item_bal_qty ELSE 0 END) AS AvailQty,
									  (CASE WHEN item_avail =2 THEN item_bal_qty ELSE 0 END) AS PackQty,
									  (CASE WHEN item_avail =0 THEN item_bal_qty ELSE 0 END) AS ObseQty,
									  (CASE WHEN item_avail =3 THEN item_bal_qty ELSE 0 END) AS IntrsQty')
        					->where('item_id', $itemid)
        					->where('item_unit', $unit_id)
							->where('bo_id', $this->session->get('ses_boid'))
							->where('item_txn_date <=',date('Y-m-d',strtotime($voucher_date)))
        					//->orderBy('item_txn_date', 'desc')
        					->orderBy('itemtxnbal_id', 'desc')
        					->get()->getResultArray();
							
	   }else{
		$result = $this->db->table($itm_txn_tbl)
		                    ->select('(CASE WHEN item_avail =1 THEN item_bal_qty ELSE 0 END) AS AvailQty,
									  (CASE WHEN item_avail =2 THEN item_bal_qty ELSE 0 END) AS PackQty,
									  (CASE WHEN item_avail =0 THEN item_bal_qty ELSE 0 END) AS ObseQty,
									  (CASE WHEN item_avail =3 THEN item_bal_qty ELSE 0 END) AS IntrsQty')
        					->where('item_id', $itemid)
        					->where('item_unit', $unit_id)
							->where('item_txn_date <=',date('Y-m-d',strtotime($voucher_date)))
        					//->orderBy('item_txn_date', 'desc')
        					->orderBy('itemtxnbal_id', 'desc')
        					->get()->getResultArray();   
		   
	   }					
     if($result){	
	
	   $AvailQty =$PackQty=$ObseQty=$IntrsQty=0;
	     foreach($result as $rr){
			$AvailQty = $AvailQty+$rr['AvailQty'];
			$PackQty  = $PackQty+$rr['PackQty'];
			$ObseQty  = $ObseQty+$rr['ObseQty'];
			$IntrsQty = $IntrsQty+$rr['IntrsQty'];
		 }
	 
	 
        $json_array[$itemid]=array("unit_name"=>$item_unit_name,"AvailQty"=>$AvailQty,"PackQty"=>$PackQty,
		                                     "ObseQty"=>$ObseQty,"IntrsQty"=>$IntrsQty);	 	   
	 }
        else{
			  $result =  $this->item_opn_balance_info($itemid,$unit_id);
			  if($result){ 
			  $json_array[$itemid]=array("unit_name"=>$item_unit_name,"AvailQty"=>$result['op_bal_qty'],"PackQty"=>"0",
		                                     "ObseQty"=>"0","IntrsQty"=>"0");
			  }
			  else{
				$json_array[$itemid]=array("unit_name"=>$item_unit_name,"AvailQty"=>"0","PackQty"=>"0",
		                                     "ObseQty"=>"0","IntrsQty"=>"0");
			     }
        }
	return $json_array;
	 }
  else
    return "0";	   
 } 
 
  public function item_opn_balance_info($itemid,$unit_id){      
       $itemoppybal_tbl = $this->company_id.'_itmoppybal_'.$this->session->get('ses_comp_fy_id');
	   
	   if($this->session->get('ses_boid')!='')
	return $this->db->table($itemoppybal_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('item_id', $itemid)->where('item_unit', $unit_id)->get()->getRowArray();  
	  else	   
     return $this->db->table($itemoppybal_tbl)->where('item_id', $itemid)->where('item_unit', $unit_id)->get()->getRowArray();  
   }
    
  public function get_account_balance($accountid,$voucher_date){
      
        $acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$accountid.'_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!=''){
			$result = $this->db->table($acc_txn_tbl)
		                    ->select('acc_bal')
        					->where('acc_id', $accountid)
							->where('bo_id', $this->session->get('ses_boid'))
        					->where('acc_txn_date <=',date('Y-m-d',strtotime($voucher_date)))
        					->orderBy('acc_txn_date', 'desc')
        					->orderBy('acc_txn_id', 'desc')
        					->limit(1)
        					->get()->getRowArray();
		}
		else{
		$result = $this->db->table($acc_txn_tbl)
		                    ->select('acc_bal')
        					->where('acc_id', $accountid)
        					->where('acc_txn_date <=',date('Y-m-d',strtotime($voucher_date)))
        					->orderBy('acc_txn_date', 'desc')
        					->orderBy('acc_txn_id', 'desc')
        					->limit(1)
        					->get()->getRowArray();
		}
        if($result)				
        return ($result['acc_bal']<0)?'CR. '.abs($result['acc_bal']):'DR. '.abs($result['acc_bal']);
        
        else{
          $result =  $this->account_opn_balance_info($accountid);
          if($result)   {  
              
              
        return ($result['acc_op_bal']<0)?'CR. '.abs($result['acc_op_bal']):'DR. '.abs($result['acc_op_bal']);
          }
          else
          return "0";
        
        }
      
  }    
    
   public function account_opn_balance_info($acc_id){      
       $accoppybal_tbl = $this->company_id.'_accoppybal_'.$this->session->get('ses_comp_fy_id');
	    if($this->session->get('ses_boid')!='')
       return $this->db->table($accoppybal_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('acc_id', $acc_id)->get()->getRowArray(); 
 
	   else	   
     return $this->db->table($accoppybal_tbl)->where('acc_id', $acc_id)->get()->getRowArray();  
   }
    
    function get_item_info($item_id){
        $item_master_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
     	return $this->db->table($item_master_tbl)->where('item_id', $item_id)->where('comp_id', $this->company_id)->get()->getRowArray();  
    } 
    
  	function get_account_info($acc_id){	 
	  	$account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	  	return $this->db->table($account_master_tbl)->where('acc_id', $acc_id)->get()->getRowArray();   	   
    }

    function item_unit_info($unit_id){
       $item_unit_master_tbl =  $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');	 
	   return  $this->db->table($item_unit_master_tbl)->where('unit_id', $unit_id)->where('comp_id', $this->company_id)->orderBy('item_unit','ASC')->get()->getRowArray();   
    }
    function get_billsundry_info($bsd_id){	 
	  	$bsd_mst_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!='')     
	   return $this->db->table($bsd_mst_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('bill_sundry_id', $bsd_id)->get()->getRowArray(); 
	   else	
	  	return $this->db->table($bsd_mst_tbl)->where('bill_sundry_id', $bsd_id)->get()->getRowArray();   	   
    }


    function get_sub_group_ids($array){
    	
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

    function party_dropdown($array = [23,22,16,21]){

    	$final_array = $this->get_sub_group_ids($array);
    	$bbb_groups = $this->get_bbb_groups();

		$comp_id = $this->company_id;
		$comp_party_tbl = $comp_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
		$data =  $this->db->table($comp_party_tbl)
						  ->select('acc_id, acc_name, acc_grp_id')
						  ->whereIn('acc_grp_id', $final_array)
						  ->orderBy('acc_name','ASC')
						  ->get()->getResultArray();

		if($data){
			foreach($data as $key => $value){

				$data[$key]['is_bbb'] = 0;
		       	if(in_array($value['acc_grp_id'], $bbb_groups)){
		           	$data[$key]['is_bbb'] = 1;
		       	}
			   
			}
		}
		return $data;	
	}
	
	function ajax_acc_bsd_accounts($search){ // for journal voucher with items

		$bbb_groups = $this->get_bbb_groups();
		$cc_groups = $this->get_cc_groups();
		$final_result    = array();

	    $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	    $acc_data =  $this->db->table($account_master_tbl)
	   					 ->select('acc_name as label, acc_name as value, acc_id, acc_grp_id, acc_grp_parent_id')
	   					 ->like('acc_name', $search)
	   					 ->orLike('acc_name_alias', $search)
	   					 ->orderBy('acc_name','ASC')
	   					 ->get()->getResultArray();	
	  
       if($acc_data){
		  foreach($acc_data as $key => $row){

		  		$is_bbb = 0;
		       	if(in_array($row['acc_grp_id'], $bbb_groups)){
		           	$is_bbb = 1;
		       	}
		       	$is_cc = 0;
		       	if(in_array($row['acc_grp_id'], $cc_groups) || in_array($row['acc_grp_parent_id'], [7,11,13])){
		          	$is_cc = 1;
		       	}

               	$final_result[]    = array(
               			"label" => ucwords($row['label']),
               			'value' => $row['value'],
               			'id'    => $row['acc_id'],
						'is_sundry'  => '0',
						'is_acc'  => '1',
						'is_bbb'  => $is_bbb,
						'is_cc'	  => $is_cc,
               	);	
		      }
        	}
 
	   $billsundry_tbl  = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
	   if($this->session->get('ses_boid')!=''){
       $bsd_data  =  $this->db->table($billsundry_tbl)
	                      ->select('bill_sundry_name as label, bill_sundry_name as value, bill_sundry_id, acc_grp_id')
						  ->where('bo_id', $this->session->get('ses_boid'))
	   					  ->like('bill_sundry_name', $search)
	   					  ->orLike('bill_sundry_alias', $search)
	   					  ->orderBy('bill_sundry_name','ASC')
	   					  ->get()->getResultArray();
	   }
	   else{
		$bsd_data  =  $this->db->table($billsundry_tbl)
	                      ->select('bill_sundry_name as label, bill_sundry_name as value, bill_sundry_id, acc_grp_id')
	   					  ->like('bill_sundry_name', $search)
	   					  ->orLike('bill_sundry_alias', $search)
	   					  ->orderBy('bill_sundry_name','ASC')
	   					  ->get()->getResultArray();   
		   
	   }
	   
	   if($bsd_data){
		  foreach($bsd_data as $key => $row){
               $final_result[]    = array(
               			"label" => ucwords($row['label']),
               			'value' => $row['value'],
               			'id'    => $row['bill_sundry_id'],
						'is_sundry'  => '1',
						'is_acc'  => '0',
						'is_bbb'  => 0,
						'is_cc'	  => 0,
               		);	
		      }
        	}	
     return $final_result; 	
	}
	
	
 	function check_bbb_account($account_id)
 	{
 		$bbb_groups = $this->get_bbb_groups();

 		$account = $this->get_account_info($account_id);
 		if(in_array($account['acc_grp_id'], $bbb_groups))
 			return true;
 		
 		return false;

 	}
 	function check_cc_account($account_id)
 	{
 		$cc_groups = $this->get_cc_groups();

 		$account = $this->get_account_info($account_id);
 		if(in_array($account['acc_grp_id'], $cc_groups) || in_array($account['acc_grp_parent_id'], [7,11,13]))
 			return true;
 		
 		return false;

 	}
 
	function matrcntr_dropdown(){
		$comp_id = $this->company_id;
		$comp_mtcnt_tbl = $comp_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
		$data =  $this->db->table($comp_mtcnt_tbl)
						  ->where('comp_id', $comp_id)
						  ->orderBy('mat_cent_name','ASC')
						  ->get()->getResultArray();

		$final_result      = array();
		$final_result['']  = '';
		if($data){
			foreach($data as $row){
				$final_result[$row['mat_cent_id']] =$this->enc_string->nc_string($row['mat_cent_name'],'de');			   
			}
		}
		return $final_result;	
	}
     
  	function ajax_items($search){	
       
	   $item_master_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
	   $data =  $this->db->table($item_master_tbl)
	   					->select('item_name as label, item_name as value, item_id, item_unit')
	   					->like('item_name', $search)
	   					->orLike('item_alias', $search)
	   					->orLike('product_id', $search)
	   					->orderBy('item_name','ASC')
	   					->get()->getResultArray();

	   $final_result = array();
	   if($data){
		  foreach($data as $row){
		      
		       	$item_unit_info  = $this->item_unit_info($row['item_unit']); 
             	if($item_unit_info)
                	$item_unit_name = $this->enc_string->nc_string($item_unit_info['item_unit'],'de');				 
              	else
                	$item_unit_name ='' ;
			  
		      
               $final_result[]    = array(
               		"label"=>ucwords($row['label']),
               		'value'=> $row['value'],
               		'item_id'=> $row['item_id'],
               		'item_unit'=>$item_unit_name,
               		'item_unit_id'=>$row['item_unit'],
               	);	
		      }
        }
	    return $final_result;  
     }
    function ajax_accounts($search, $array = []){	
       
       $array = $this->get_sub_group_ids($array);

       $bbb_groups = $this->get_bbb_groups();
	   $cc_groups = $this->get_cc_groups();

	   $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	    $builder = $this->db->table($account_master_tbl);
	   	$builder->select('acc_name as label, acc_name as value, acc_id, acc_grp_id, acc_grp_parent_id');
	   	
	   	$builder->like('acc_name', $search);
	   	if(!empty($array))
	   		$builder->whereIn('acc_grp_id', $array);
	   	$builder->orLike('acc_name_alias', $search);
	   	if(!empty($array))
	   		$builder->whereIn('acc_grp_id', $array);
	   	$builder->orderBy('acc_name','ASC');
	   	$data = $builder->get()->getResultArray();

	   	foreach ($data as $key => $value) {
	   		$data[$key]['is_bbb'] = 0;
	       	if(in_array($value['acc_grp_id'], $bbb_groups)){
	           	$data[$key]['is_bbb'] = 1;
	       	}
	       	$data[$key]['is_cc'] = 0;
	       	if(in_array($value['acc_grp_id'], $cc_groups) || in_array($value['acc_grp_parent_id'], [7,11,13])){
	          	$data[$key]['is_cc'] = 1;
	       	}
	   	}

	    return $data;  
    }

    function get_bbb_groups()
    {
        
		$groups = [16,22];
		$array = $this->get_sub_group_ids($groups);
        
        return $array;
    }
    
    function get_cc_groups()
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
    function get_cc()
    {
        $cc_mst_tbl = $this->company_id.'_costctmstr_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!=''){
		$data = $this->db->table($cc_mst_tbl)->select('cc_id as id, cc_name as label, cc_name as value')->where('bo_id', $this->session->get('ses_boid'))->where('cc_name !=', 'UNDEFINED')->get()->getResultArray();
		}
		else
       $data = $this->db->table($cc_mst_tbl)->select('cc_id as id, cc_name as label, cc_name as value')->where('cc_name !=', 'UNDEFINED')->get()->getResultArray();
        
        return $data;
    }
    

    function update_cc_txn_balance($cc_id)
    {
        $bal = 0;
        
        $cc_mst_tbl = $this->company_id.'_costctmstr_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!=''){
        $result = $this->db->table($cc_mst_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('cc_id', $cc_id)->get()->getRowArray();
		}else{
		 $result = $this->db->table($cc_mst_tbl)->where('cc_id', $cc_id)->get()->getRowArray();	
		}
        
        if($result)
        {
            if($result['cc_op_drcr'] == 'dr')
                $bal = $result['cc_op_bal'];
            if($result['cc_op_drcr'] == 'cr')
                $bal = -$result['cc_op_bal'];
        }

        $cc_txn_tbl = $this->company_id.'_costcttxnn_'.$this->session->get('ses_comp_fy_id');
		$this->db->query('SET @bal = '.$bal.';');
		
		if($this->session->get('ses_boid')!=''){
			$bo_id = $this->session->get('ses_boid');
		     $this->db->query('UPDATE '.$cc_txn_tbl.' SET cc_txn_bal = CASE WHEN cc_txn_drcr = "C" THEN @bal:=@bal - cc_txn_amt WHEN cc_txn_drcr = "D" THEN @bal:=@bal + cc_txn_amt ELSE 0 END where bo_id = '.$bo_id.' AND cc_id = '.$cc_id.' order by cc_txn_date, cc_txn_id;');  
		}
		else 
		$this->db->query('UPDATE '.$cc_txn_tbl.' SET cc_txn_bal = CASE WHEN cc_txn_drcr = "C" THEN @bal:=@bal - cc_txn_amt WHEN cc_txn_drcr = "D" THEN @bal:=@bal + cc_txn_amt ELSE 0 END where cc_id = '.$cc_id.' order by cc_txn_date, cc_txn_id;');  
    }

    // function update_cc_txn_balance($cc_id)
    // {
    //     $bal = 0;
        
    //     $cc_mst_tbl = $this->company_id.'_costctmstr_'.$this->session->get('ses_comp_fy_id');
    //     $result = $this->db->table($cc_mst_tbl)->where('cc_id', $cc_id)->get()->getRowArray();
        
    //     if($result)
    //     {
    //         if($result['cc_op_drcr'] == 'dr')
    //             $bal = $result['cc_op_bal'];
    //         if($result['cc_op_drcr'] == 'cr')
    //             $bal = -$result['cc_op_bal'];
    //     }
        
    //     $cc_txn_tbl = $this->company_id.'_costcttxnn_'.$this->session->get('ses_comp_fy_id');
    //     $result = $this->db->table($cc_txn_tbl)->where('cc_id', $cc_id)->orderBy('cc_txn_date', 'asc')->get()->getResultArray();
        
        
    //     if($result)
    //     {
    //         foreach($result as $key => $value)
    //         {
    //             if($value['cc_txn_drcr'] == 'D'){
    //                 $bal += $value['cc_txn_amt'];
    //             }
    //             if($value['cc_txn_drcr'] == 'C'){
    //                 $bal += -$value['cc_txn_amt'];
    //             }
                
    //             $data = ['cc_txn_bal' => $bal];
    //             $this->db->table($cc_txn_tbl)->where('cc_txn_id', $value['cc_txn_id'])->update($data);
    //         } 
    //     }   
    // }
    function delete_cc_txn($voucher_txn_id)
    {
        $cc_txn_tbl = $this->company_id.'_costcttxnn_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!='')
		  $result = $this->db->table($cc_txn_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('voucher_txn_id', $voucher_txn_id)->get()->getResultArray();			
		else
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
    public function get_cc_txn_data($voucher_txn_id)
    {
    	$fcy_rate = 1;

	   	$voucher_info = $this->get_voucher_cons_info($voucher_txn_id);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$fcy_rate = $this->get_forex_rate_fcy($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

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
		if($this->session->get('ses_boid')!='')
		$builder->where($cc_txn_tbl.'.bo_id', $this->session->get('ses_boid'));

        $builder->groupBy($acc_mst_tbl.'.acc_id');
        $builder->orderBy($acc_mst_tbl.'.acc_id', 'asc');
        $result_ = $builder->get()->getResultArray();
        
        foreach($result_ as $key_ => $value_)
        {
            $builder = $this->db->table($cc_txn_tbl);
            $builder->select($cc_txn_tbl.'.cc_id, '.$cc_txn_tbl.'.acc_id, cc_txn_amt, cc_txn_drcr, cc_txn_id');
            $builder->join($cc_mst_tbl, $cc_mst_tbl.'.cc_id ='.$cc_txn_tbl.'.cc_id');
            $builder->select($cc_mst_tbl.'.cc_name');
            if($this->session->get('ses_boid')!='')
			$builder->where($cc_mst_tbl.'.bo_id', $this->session->get('ses_boid'));
            $builder->where('voucher_txn_id', $voucher_txn_id);
            $builder->where($cc_txn_tbl.'.acc_id', $value_['acc_id']);
            $builder->where($cc_txn_tbl.'.cc_id !=', 1);
            $result = $builder->get()->getResultArray();
			$final_cc_txn=array();
			if($result){
				foreach($result as $ccrow){
					$get_narration_info = $this->get_voucher_narration_info($voucher_txn_id,'short',$ccrow['cc_txn_id']);
					if($get_narration_info)
						$narration=$get_narration_info['vch_short_narr'];
					  else
						$narration='';  
					
					$ccrow['cc_txn_narr']= $narration;

					$ccrow['cc_txn_amt'] = parseAmount($ccrow['cc_txn_amt'] * $fcy_rate);

					$final_cc_txn[]=$ccrow;
				}
				
			}
			
            
            $final[] = [
                    'acc_id' => $value_['acc_id'],
                    'acc_name' => $value_['acc_name'],
                    'cc_txn_list' => $final_cc_txn
                ];
        }
        
        
        // echo "<pre>";print_r($final);exit;
        return $final;
    }

    function get_voucher_series($comp_vch_series_id){
		$comp_vch_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
		$data =  $this->db->table($comp_vch_series_tbl)
							->where('comp_vch_series_id', $comp_vch_series_id)
							->where('comp_id', $this->company_id)
							->get()->getRowArray();

		return $data;	
	}

     function ajax_billsundry_items($search){

	   $billsundry_tbl  = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
	   if($this->session->get('ses_boid')!=''){
		$data  =  $this->db->table($billsundry_tbl)
						  ->where('bo_id', $this->session->get('ses_boid'))
	   					  ->like('bill_sundry_name', $search)
	   					  ->orLike('bill_sundry_alias', $search)
	   					  ->orderBy('bill_sundry_name','ASC')
	   					  ->get()->getResultArray();
	   }
	   else{
	   $data  =  $this->db->table($billsundry_tbl)
	   					  ->like('bill_sundry_name', $search)
	   					  ->orLike('bill_sundry_alias', $search)
	   					  ->orderBy('bill_sundry_name','ASC')
	   					  ->get()->getResultArray();
	   }

	   $final_result    = array();
	   if($data){
		  foreach($data as $row){
               $final_result[]    = array(
               			"label" => ucwords($row['bill_sundry_name']),
               			'value' => $row['bill_sundry_name'],
               			'id'    => $row['bill_sundry_id'],
               		);	
		      }
        	}
        
        return $final_result; 
     }
	 
	function items_list(){
		$comp_id = $this->company_id;
		$item_master_tbl =  $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');	 
		$data            =  $this->db->table($item_master_tbl)->where('comp_id', $comp_id)->orderBy('item_name','ASC')->get()->getResultArray();
		$final_result    =  array();
		$itemkeyval =array();
		if($data){
			foreach($data as $row){
				$item_name      = $row['item_name'];
				// $final_result[] = array($row['item_id']=>$item_name);	

				$item_unit_info  = $this->item_unit_info($row['item_unit']); 
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
     
     
	function units_dropdown(){
		$comp_id = $this->company_id;
		$item_unit_master_tbl =  $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');	 
		$data =  $this->db->table($item_unit_master_tbl)->where('comp_id', $comp_id)->orderBy('item_unit','ASC')->get()->getResultArray();
		$final_result      = array();
		if($data){
			foreach($data as $row){
				$item_name      = $this->enc_string->nc_string($row['item_unit'],'de');
				$final_result[] =array("label"=>$item_name,"value"=>$item_name,"id"=>$row['unit_id']);			   
			}
		}
		return $final_result;	
    }

    function vch_subtype_dropdown($voucher_type_id){
		$vch_subtype_tbl =  $this->company_id.'_vchsubtype_'.$this->session->get('ses_comp_fy_id');	 
		$data =  $this->db->table($vch_subtype_tbl)->where('voucher_type_id', $voucher_type_id)->get()->getResultArray();
		$final_result      = array();
		if($data){
			foreach($data as $row){
				$final_result[$row['vch_subtype_id']] =$row['comp_vch_subtype'];			   
			}
		}
		return $final_result;	
    }

    function get_vch_subtype($vch_subtype_id){
		$vch_subtype_tbl =  $this->company_id.'_vchsubtype_'.$this->session->get('ses_comp_fy_id');	 
		$data =  $this->db->table($vch_subtype_tbl)->where('vch_subtype_id', $vch_subtype_id)->get()->getRowArray();
		return $data['comp_vch_subtype'];	
    }

    function get_voucher_no($voucher_type_id){
		$comp_vch_cons_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
		$builder = $this->db->table($comp_vch_cons_tbl); 
		$builder->select('MAX(comp_vch_no) as max_comp_vch_no');
		$builder->where('voucher_type_id',$voucher_type_id);
		if($this->session->get('ses_boid')!='')
	   $builder->where('bo_id', $this->session->get('ses_boid'));
		$builder->orderBy('voucher_txn_id','DESC');	
		$result =  $builder->get()->getRowArray();
		if($result){
			return $result['max_comp_vch_no'] + 1;
		}
		else
			return 1;
	}

	function comp_voucher_series($voucher_type_id){
		$comp_vch_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
		$data =  $this->db->table($comp_vch_series_tbl)
							->where('voucher_type_id', $voucher_type_id)
							->where('comp_id', $this->company_id)
							->orderBy('comp_vch_series','ASC')
							->get()->getResultArray();

		$final_result      = array();
		$final_result['']  = '';
		if($data){
			foreach($data as $row){
				$final_result[$row['comp_vch_series_id']] = $row['comp_vch_series'];			   
			}
		}
		return $final_result;	
	}

    function get_voucher_cons_info($voucher_txn_id,$voucher_type_id = 0)
    {	 
	    $vch_conso_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	  	$builder = $this->db->table($vch_conso_tbl);
	  	$builder->where('voucher_txn_id', $voucher_txn_id);
		if($this->session->get('ses_boid')!='')
	   $builder->where('bo_id', $this->session->get('ses_boid'));
   
	  	if($voucher_type_id != 0)
	  		$builder->where('voucher_type_id', $voucher_type_id);
	  	$builder->where('comp_id', $this->company_id);
	  	$result = $builder->get()->getRowArray();

	  	return $result;   	   
    }
    function count_vouchers_by_tag($voucher_type_id,$voucher_tag)
    {
    	$vch_conso_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	  	$result = $this->db->table($vch_conso_tbl)
	  					->where('voucher_tag', $voucher_tag)
	  					->where('voucher_type_id', $voucher_type_id)
	  					->where('comp_id', $this->company_id)
	  					->countAllResults();
	  	return $result;
    }

    function delete_voucher_conso_data($voucher_txn_id)
    {
    	$vch_conso_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!='')	
	   $this->db->table($vch_conso_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('voucher_txn_id', $voucher_txn_id)->delete();
	  else 
	  	$this->db->table($vch_conso_tbl)->where('voucher_txn_id', $voucher_txn_id)->delete();
    }

    
	
	function modified_item_batch_data($voucher_txn_id){
		$comp_txn_data = $this->get_comp_txn_data($voucher_txn_id);
		$get_item_batch_list= array();
		if($comp_txn_data){
			foreach($comp_txn_data as $row){
				$master_id_type = $row['master_id_type'];
				if($master_id_type =='itm'){
					$item_id = $row['master_id'];
					
					$get_item_batch_list[] =array("item_id"=>$item_id,"batch_txn_data"=> $this->get_item_batch_list($item_id));
					
					
				}
			}
		}
		return $get_item_batch_list;
	}

	function get_item_batch_data($voucher_txn_id){
		$data = [];

		$comp_txn_master_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
		$result_ = $this->db->table($comp_txn_master_tbl)
							->where('master_id_type', 'itm')
							->where('voucher_txn_id', $voucher_txn_id)
							->groupBy('master_id')
							->get()->getResultArray();

		if($result_){
			foreach ($result_ as $key_ => $value_) {
				$itemtxnbal_tbl = $this->company_id.'_itemtxnbal_'.$value_['master_id'].'_'.$this->session->get('ses_comp_fy_id');
				
				if($this->session->get('ses_boid')!=''){
					$result = $this->db->table($itemtxnbal_tbl)
									->where('voucher_txn_id', $voucher_txn_id)
									->where('item_id', $value_['master_id'])
									->where('bo_id', $this->session->get('ses_boid'))
									->where('batch_id !=', 0)
									->get()->getResultArray();
				}
				else{
				$result = $this->db->table($itemtxnbal_tbl)
									->where('voucher_txn_id', $voucher_txn_id)
									->where('item_id', $value_['master_id'])
									->where('batch_id !=', 0)
									->get()->getResultArray();
				}
				foreach($result as $key => $value){

					$index = -1;
					foreach ($data as $ke => $val) {
						if($val['item_id'] == $value['item_id'] && $val['item_unit_id'] == $value['item_unit']){
							$index = $ke;
							break;
						}
					}

					$batch = $this->get_batch_master_info($value['batch_id']);
					if($batch)
					{
						if($index > -1){
							$data[$index]['grid'][] = [
								'batch_method' => 'Adjustment',
								'batch_no' => $batch['batch_no'],
								'batch_id' => $value['batch_id'],
								'manufacturing_date' => $batch['batch_mfr'],
								'expiry_date' => $batch['batch_expiry'],
								'batch_qty' => $value['item_bal_qty'],
							];
						}
						else{
							$data[] = [
								'item_id' => $value['item_id'],
								'item_unit_id' => $value['item_unit'],
								'grid' => [
									0 => [
										'batch_method' => 'Adjustment',
										'batch_no' => $batch['batch_no'],
										'batch_id' => $value['batch_id'],
										'manufacturing_date' => $batch['batch_mfr'],
										'expiry_date' => $batch['batch_expiry'],
										'batch_qty' => $value['item_bal_qty'],
									]
								] ,
							];
						}
					}
				}
			}
		}
		return $data;
	}
	
	function get_item_tracking_data($voucher_txn_id){
		$data = [];

		$comp_txn_master_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
		$result_ = $this->db->table($comp_txn_master_tbl)
							->where('master_id_type', 'itm')
							->where('voucher_txn_id', $voucher_txn_id)
							->groupBy('master_id')
							->get()->getResultArray();

		if($result_){
			foreach ($result_ as $key_ => $value_) {
				$itemtxnbal_tbl = $this->company_id.'_itemtrackn_'.$this->session->get('ses_comp_fy_id');
				$result = $this->db->table($itemtxnbal_tbl)
									->where('txn_id', $value_['txn_id'])
									->where('item_id', $value_['master_id'])
									->get()->getResultArray();
									
				foreach($result as $key => $value){

					$index = -1;
					foreach ($data as $ke => $val) {
						if($val['item_id'] == $value['item_id'] && $val['item_unit_id'] == $value['unit_id']){
							$index = $ke;
							break;
						}
					}

					$trackingd = $this->get_tracking_master_info($value['tracking_id']);
					if($trackingd)
					{
						if($index > -1){
							$data[$index]['grid'][] = [
								'tracking_method' => 'Adjustment',
								'tracking_no' => $trackingd['tracking_no'],
								'tracking_id' => $value['tracking_id'],
								'tracking_qty' => 1,
							];
						}
						else{
							$data[] = [
								'item_id' => $value['item_id'],
								'item_unit_id' => $value['unit_id'],
								'grid' => [
									0 => [
										'tracking_method' => 'Adjustment',
										'tracking_no' => $trackingd['tracking_no'],
										'tracking_id' => $value['tracking_id'],
										'tracking_qty' =>1,
									]
								] ,
							];
						}
					}
				}
			}
		}
		return $data;
	}
	
		
	function get_tracking_master_info($tracking_id){
		$itmbatchmt_tbl = $this->company_id.'_itemtrackn_'.$this->session->get('ses_comp_fy_id');
        $data = $this->db->table($itmbatchmt_tbl)->where('tracking_id', $tracking_id)->where('tracking_no !=', 'UNDEFINED')->get()->getRowArray();
        return $data;
	}
	
	function get_batch_master_info($batch_id){
		$itmbatchmt_tbl = $this->company_id.'_itmbatchmt_'.$this->session->get('ses_comp_fy_id');
        $data = $this->db->table($itmbatchmt_tbl)->where('batch_id', $batch_id)->where('batch_no !=', 'UNDEFINED')->get()->getRowArray();
        return $data;
	}

	function get_comp_txn_data($voucher_txn_id){
	 	$comp_txn_master_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	   	return $this->db->table($comp_txn_master_tbl)->where('voucher_txn_id', $voucher_txn_id)->get()->getResultArray();
	}

	function delete_comp_txn_data($voucher_txn_id){
	 	$comp_txn_master_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	   	$this->db->table($comp_txn_master_tbl)->where('voucher_txn_id', $voucher_txn_id)->delete();
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
	
	
	

	function get_crsref($voucher_txn_id, $acc_cross_ref_type)
    {
    	$acc_crsref_tbl = $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($acc_crsref_tbl);
 		$builder->where('acc_cross_ref_type', $acc_cross_ref_type);
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
 		$builder->where('voucher_txn_id', $voucher_txn_id);
 		$ref = $builder->get()->getRowArray();
 		if($ref){
 			return $ref['acc_cross_ref_data'];
 		}
 		return '';
    }
    function get_crsref_reverse($voucher_txn_id, $acc_cross_ref_type)
    {
    	$acc_crsref_tbl = $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($acc_crsref_tbl);
 		$builder->where('acc_cross_ref_type', $acc_cross_ref_type);
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
 		$builder->where('acc_cross_ref_data', $voucher_txn_id);
 		$ref = $builder->get()->getRowArray();
 		if($ref){
 			return $ref['voucher_txn_id'];
 		}
 		return '';
    }
    function check_crsref($voucher_txn_id, $acc_cross_ref_type)
    {
    	$acc_crsref_tbl = $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($acc_crsref_tbl);
 		$builder->where('acc_cross_ref_type', $acc_cross_ref_type);
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
 		$builder->where('acc_cross_ref_data', $voucher_txn_id);
 		$ref = $builder->get()->getRowArray();
 		if($ref){
 			return true;
 		}
 		return false;
    }

	function delete_acc_oth_data($voucher_txn_id){
	 	$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!='')
		$this->db->table($table)->where('bo_id', $this->session->get('ses_boid'))->where('voucher_txn_id', $voucher_txn_id)->delete();
		else 
	   	$this->db->table($table)->where('voucher_txn_id', $voucher_txn_id)->delete();
	}

	

	function add_onloadchks_data($data){
	 	$onloadchks_tbl = $this->company_id.'_onloadchks_'.$this->session->get('ses_comp_fy_id');
	 	$this->db->table($onloadchks_tbl)->insert($data);

	 	return $this->db->insertID();
	}

	function getDBName()
	{
		return $this->db->database;
	}

	function delete_onloadchks_data($voucher_txn_id){
	 	$onloadchks_tbl = $this->company_id.'_onloadchks_'.$this->session->get('ses_comp_fy_id');
	   	$this->db->table($onloadchks_tbl)->where('voucher_txn_id', $voucher_txn_id)->delete();
	}

	function delete_acc_crsref_data($voucher_txn_id){
	 	$table = $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
	   	$this->db->table($table)->where('voucher_txn_id', $voucher_txn_id)->delete();
	}

	function delete_acc_crsref_data_by_tag($voucher_txn_id, $tag){
	 	$table = $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!='')
		$this->db->table($table)->where('bo_id', $this->session->get('ses_boid'))->where('voucher_txn_id', $voucher_txn_id)->where('acc_cross_ref_type', $tag)->delete();
	   else 
	   	$this->db->table($table)->where('voucher_txn_id', $voucher_txn_id)->where('acc_cross_ref_type', $tag)->delete();
	  }

	

	function delete_itm_oth_data($voucher_txn_id){
	 	$table = $this->company_id.'_itemtxnoth_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!='')
		$this->db->table($table)->where('bo_id', $this->session->get('ses_boid'))->where('voucher_txn_id', $voucher_txn_id)->delete();
		
        else 
	   	$this->db->table($table)->where('voucher_txn_id', $voucher_txn_id)->delete();
	}

    function get_party_transaction_oth($voucher_txn_id)
    {
    	$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($comp_txn_tbl);
    	$builder->where('voucher_txn_id', $voucher_txn_id);
		
    	$builder->where('master_id_type', 'aco');
    	$builder->orderBy('txn_id', 'asc');
    	$builder->limit(1);
    	$comp_txn = $builder->get()->getRowArray();

    	$acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($acc_oth_tbl);
    	$builder->where('voucher_txn_id', $voucher_txn_id);
    	$builder->where('acc_id', $comp_txn['master_id']);
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));

    	$result = $builder->get()->getRowArray();
		if(!$result){
		$result['acc_oth_txn_narr']='';	
		}else{
		$get_narration_info = $this->get_voucher_narration_info($voucher_txn_id,'short',$result['txn_id']);
		if($get_narration_info){
			$result['acc_oth_txn_narr']=$get_narration_info['vch_short_narr'];
		}else
			$result['acc_oth_txn_narr']='';
		}
    	return $result;
    }
    function get_party_transaction($voucher_txn_id)
    {
    	$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($comp_txn_tbl);
    	$builder->where('voucher_txn_id', $voucher_txn_id);
    	$builder->where('master_id_type', 'acc');
    	$builder->orderBy('txn_id', 'asc');
    	$builder->limit(1);
    	$comp_txn = $builder->get()->getRowArray();

    	$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$comp_txn['master_id'].'_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($acc_txn_tbl);
    	$builder->where('voucher_txn_id', $voucher_txn_id);
		
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));

    	$builder->where('acc_id', $comp_txn['master_id']);
    	$result = $builder->get()->getRowArray();
		if(!$result){
			$result['acc_txn_narr']='';$result['acc_id']='';
		}
		else{
		$get_narration_info = $this->get_voucher_narration_info($voucher_txn_id,'short',$result['txn_id']);
		if($get_narration_info){
			$result['acc_txn_narr']=$get_narration_info['vch_short_narr'];
		}else
			$result['acc_txn_narr']='';	
		}		
		
	
		
    	return $result;
    }

    function get_item_transactions_oth($voucher_txn_id)
    {
    	$fcy_rate = 1;

	   	$voucher_info = $this->get_voucher_cons_info($voucher_txn_id);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$fcy_rate = $this->get_forex_rate_fcy($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

    	$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($comp_txn_tbl);
    	$builder->where('voucher_txn_id', $voucher_txn_id);
    	$builder->where('master_id_type', 'ito');
    	$builder->orderBy('txn_id', 'asc');
    	$comp_txns = $builder->get()->getResultArray();

    	$item_oth_tbl = $this->company_id.'_itemtxnoth_'.$this->session->get('ses_comp_fy_id');
    	$result = [];
    	foreach ($comp_txns as $key => $value) {
    		$builder = $this->db->table($item_oth_tbl);
	    	$builder->where('voucher_txn_id', $voucher_txn_id);
	    	$builder->where('item_id', $value['master_id']);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
	    	$builder->where('txn_id', $value['txn_id']);
	    	$data = $builder->get()->getRowArray();

	    	if($data){
	    		$item = $this->get_item_info($value['master_id']);
	    		$data['item_name'] = $item['item_name'];
	    		
	    		$item_unit_info = $this->item_unit_info($data['item_unit']);
	    		$item_unit_name = $this->enc_string->nc_string($item_unit_info['item_unit'],'de');
				
				// get item narration_txt
				$get_narration_info = $this->get_voucher_narration_info($voucher_txn_id,'short',$value['txn_id']);
				
				if($get_narration_info){
					$item_description = $get_narration_info['vch_short_narr'];
				}else{
					$item_description = '';
				}

				$data['item_oth_txn_qty'] = $data['item_oth_txn_qty'] != 0 ? $data['item_oth_txn_qty'] : 1;

				// if(floatval($data['item_oth_txn_qty']) >0)					
				// 	$item_price = 	(parseAmount($data['item_oth_txn_amount'])/floatval($data['item_oth_txn_qty']));
			   	// else
				// 	$item_price =  0;
				
				
	    		$result[] = [
	    				'item_id' 				=> $data['item_id'],
						'item_name' 			=> $data['item_name'],
						'item_unit' 			=> $item_unit_name,
						'item_unit_id' 			=> $data['item_unit'],
						'item_qty' 				=> $data['item_oth_txn_qty'],
						'item_oth_txn_drcr' 	=> $data['item_oth_txn_drcr'],
						'item_amount' 			=> parseAmount($data['item_oth_txn_amount'] * $fcy_rate),
						'description' 			=> $item_description,
						'item_price'			=> parseAmount(($data['item_oth_txn_amount']/$data['item_oth_txn_qty']) * $fcy_rate),
						'txn_id'				=> $value['txn_id'],// physical verification
	    			];
				
	    	}
    	}
    	return $result;
    }

    function get_item_transactions($voucher_txn_id)
    {
    	$fcy_rate = 1;

	   	$voucher_info = $this->get_voucher_cons_info($voucher_txn_id);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$fcy_rate = $this->get_forex_rate_fcy($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

    	$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($comp_txn_tbl);
    	$builder->where('voucher_txn_id', $voucher_txn_id);
    	$builder->where('master_id_type', 'itm');
    	$builder->orderBy('txn_id', 'asc');
    	$comp_txns = $builder->get()->getResultArray();

    	$result = [];
    	foreach ($comp_txns as $key => $value) {

    		$item_txn_tbl =  $this->company_id.'_itemtxnnnn_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
    		$builder = $this->db->table($item_txn_tbl);
	    	$builder->where('voucher_txn_id', $voucher_txn_id);
	    	$builder->where('item_id', $value['master_id']);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
	    	$builder->where('txn_id', $value['txn_id']);
	    	$data = $builder->get()->getRowArray();

	    	if($data){
	    		$item = $this->get_item_info($value['master_id']);
	    		$data['item_name'] = $item['item_name'];

	    		$item_bal_tbl =  $this->company_id.'_itemtxnbal_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
	    		$builder = $this->db->table($item_bal_tbl);
		    	$builder->where('voucher_txn_id', $voucher_txn_id);
		    	$builder->where('item_id', $value['master_id']);
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));

		    	$builder->where('txn_id', $value['txn_id']);
		    	$builder->where('item_txn_id', $data['item_txn_id']);
		    	$data2 = $builder->get()->getRowArray();

		    	if($data2){
		    		$item_unit_id = $data2['item_unit'];
		    		$item_unit_info = $this->item_unit_info($item_unit_id);
					if(isset($item_unit_info))
	    			$item_unit = $this->enc_string->nc_string($item_unit_info['item_unit'],'de');
				    else{
					$item_unit_id = 0;
		    		$item_unit = 'No Unit';	
						
					}
		    	}
		    	else{
		    		$item_unit_id = 0;
		    		$item_unit = 'No Unit';
		    	}
				
				// get item narration_txt
				$get_narration_info = $this->get_voucher_narration_info($voucher_txn_id,'short',$value['txn_id']);
				
				if($get_narration_info){
					$item_description = $get_narration_info['vch_short_narr'];
				}else{
					$item_description = '';
				}

				$data['item_txn_qty'] = $data['item_txn_qty'] != 0 ? $data['item_txn_qty'] : 1;
				
			// 	if(floatval($data['item_txn_qty']) >0)					
			// 	$item_price = 	(parseAmount($data['item_txn_amount'])/floatval($data['item_txn_qty']));
			   // else
			// 	 $item_price =  0;
				$item_balance_tooltip = $this->get_item_balance($data['item_id'],$data['item_txn_date'],$item_unit_id);	
				
				
				$tooltip_info         = $item_balance_tooltip[$data['item_id']];
				$balancetable = "<table style='width:100%;'><tr><td colspan='2'>".$tooltip_info['unit_name']."</td></tr><tr><td>Available</td><td align='right'>".$tooltip_info["AvailQty"]."</td></tr><tr><td>Packed</td><td align='right'>".$tooltip_info["PackQty"]."</td></tr><tr><td>Obselete</td><td align='right'>".$tooltip_info["ObseQty"]."</td></tr><tr><td>In Transit</td><td align='right'>".$tooltip_info["IntrsQty"]."</td></tr></table>";
				
	    		$result[] = [
	    				'item_id' 				=> $data['item_id'],
						'AvailQty'              => $tooltip_info["AvailQty"],
						'item_name' 			=> $data['item_name'],
						'item_unit' 			=> $item_unit,
						'item_unit_id' 			=> $item_unit_id,
						'item_qty' 				=> $data['item_txn_qty'],
						'item_txn_drcr' 		=> $data['item_txn_drcr'],
						'item_amount' 			=> parseAmount($data['item_txn_amount'] * $fcy_rate),
						"pq_cellattr"           => array("item_name"=>array("title"=>$balancetable)),
						'description' 		    => $item_description,
						'item_price'			=> parseAmount(($data['item_txn_amount'] / $data['item_txn_qty']) * $fcy_rate),
						
	    			   ];
	    	}
    	}		
    	return $result;
    }

    function get_journal_item_transactions($voucher_txn_id)
    {
    	$fcy_rate = 1;

	   	$voucher_info = $this->get_voucher_cons_info($voucher_txn_id);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$fcy_rate = $this->get_forex_rate_fcy($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

    	$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($comp_txn_tbl);
    	$builder->where('voucher_txn_id', $voucher_txn_id);
    	$builder->where('master_id_type', 'itm');
    	$builder->orderBy('txn_id', 'asc');
    	$comp_txns = $builder->get()->getResultArray();

    	$l_items = [];
    	$r_items = [];
    	foreach ($comp_txns as $key => $value) {

    		$item_txn_tbl =  $this->company_id.'_itemtxnnnn_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
    		$builder = $this->db->table($item_txn_tbl);
	    	$builder->where('voucher_txn_id', $voucher_txn_id);
	    	$builder->where('item_id', $value['master_id']);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
	    	$builder->where('txn_id', $value['txn_id']);
	    	$data = $builder->get()->getRowArray();

	    	if($data){
	    		$item = $this->get_item_info($value['master_id']);
	    		$data['item_name'] = $item['item_name'];

	    		$item_bal_tbl =  $this->company_id.'_itemtxnbal_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
	    		$builder = $this->db->table($item_bal_tbl);
		    	$builder->where('voucher_txn_id', $voucher_txn_id);
		    	$builder->where('item_id', $value['master_id']);
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));

		    	$builder->where('txn_id', $value['txn_id']);
		    	$builder->where('item_txn_id', $data['item_txn_id']);
		    	$data2 = $builder->get()->getRowArray();

		    	if($data2){
		    		$item_unit_id = $data2['item_unit'];
		    		$item_unit_info = $this->item_unit_info($item_unit_id);
					if(isset($item_unit_info))
	    			$item_unit = $this->enc_string->nc_string($item_unit_info['item_unit'],'de');
				    else{
					$item_unit_id = 0;
		    		$item_unit = 'No Unit';	
						
					}
		    	}
		    	else{
		    		$item_unit_id = 0;
		    		$item_unit = 'No Unit';
		    	}
				
				// get item narration_txt
				$get_narration_info = $this->get_voucher_narration_info($voucher_txn_id,'short',$value['txn_id']);
				
				if($get_narration_info){
					$item_description = $get_narration_info['vch_short_narr'];
				}else{
					$item_description = '';
				}

				$data['item_txn_qty'] = $data['item_txn_qty'] != 0 ? $data['item_txn_qty'] : 1;
				
				// if(floatval($data['item_txn_qty']) >0)					
				// 	$item_price = 	(parseAmount($data['item_txn_amount'])/floatval($data['item_txn_qty']));
			   	// else
				// 	$item_price =  0;
				   
				if($data['item_txn_drcr'] == 'd'){
					$l_items[] = [
	    				'item_id' 				=> $data['item_id'],
						'item_name' 			=> $data['item_name'],
						'item_unit' 			=> $item_unit,
						'item_unit_id' 			=> $item_unit_id,
						'item_qty' 				=> $data['item_txn_qty'],
						'item_txn_drcr' 		=> $data['item_txn_drcr'],
						'item_amount' 			=> parseAmount($data['item_txn_amount'] * $fcy_rate),
						'description' 		    => $item_description,
						'item_price'			=> parseAmount(($data['item_txn_amount']/$data['item_txn_qty']) * $fcy_rate),
	    			];
				}
				if($data['item_txn_drcr'] == 'c'){
					$r_items[] = [
	    				'item_id' 				=> $data['item_id'],
						'item_name' 			=> $data['item_name'],
						'item_unit' 			=> $item_unit,
						'item_unit_id' 			=> $item_unit_id,
						'item_qty' 				=> $data['item_txn_qty'],
						'item_txn_drcr' 		=> $data['item_txn_drcr'],
						'item_amount' 			=> parseAmount($data['item_txn_amount'] * $fcy_rate),
						'description' 		    => $item_description,
						'item_price'			=> parseAmount(($data['item_txn_amount']/$data['item_txn_qty']) * $fcy_rate),
	    			];
				}
	    		
	    	}
    	}
    	$result = [
    		'l_items' => $l_items,
    		'r_items' => $r_items,
    	];
    	// echo "<pre>";print_r($result);exit;
    	return $result;
    }

    function get_item_transactions_by_mc($voucher_txn_id, $mat_cent_id)
    {
    	$fcy_rate = 1;

	   	$voucher_info = $this->get_voucher_cons_info($voucher_txn_id);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$fcy_rate = $this->get_forex_rate_fcy($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

    	$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($comp_txn_tbl);
    	$builder->where('voucher_txn_id', $voucher_txn_id);
    	$builder->where('master_id_type', 'itm');
    	$builder->orderBy('txn_id', 'asc');
    	$comp_txns = $builder->get()->getResultArray();

    	$result = [];
    	foreach ($comp_txns as $key => $value) {
			
			$get_narration_info = $this->get_voucher_narration_info($voucher_txn_id,'short',$value['txn_id']);
			
    		$item_txn_tbl =  $this->company_id.'_itemtxnnnn_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
    		$builder = $this->db->table($item_txn_tbl);
	    	$builder->where('voucher_txn_id', $voucher_txn_id);
	    	$builder->where('item_id', $value['master_id']);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
	    	$builder->where('txn_id', $value['txn_id']);
	    	$builder->where('mat_cent_id', $mat_cent_id);
	    	$data = $builder->get()->getRowArray();

	    	if($data){
	    		$item = $this->get_item_info($value['master_id']);
	    		$data['item_name'] = $item['item_name'];

	    		$item_bal_tbl =  $this->company_id.'_itemtxnbal_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
	    		$builder = $this->db->table($item_bal_tbl);
		    	$builder->where('voucher_txn_id', $voucher_txn_id);
		    	$builder->where('item_id', $value['master_id']);
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
		    	$builder->where('txn_id', $value['txn_id']);
		    	$builder->where('item_txn_id', $data['item_txn_id']);
		    	$data2 = $builder->get()->getRowArray();
		    	if($data2){
		    		$item_unit_id = $data2['item_unit'];
		    		$item_unit_info = $this->item_unit_info($item_unit_id);
	    			$item_unit = $this->enc_string->nc_string($item_unit_info['item_unit'],'de');
		    	}
		    	else{
		    		$item_unit_id = 0;
		    		$item_unit = 'No Unit';
		    	}
	    		
				if($get_narration_info)
					 $description = $get_narration_info['vch_short_narr'];
                else
                     $description = '';
			            
				$item_balance_tooltip = $this->get_item_balance($data['item_id'],$data['item_txn_date'],$item_unit_id);									
				$tooltip_info         = $item_balance_tooltip[$data['item_id']];
				$balancetable = "<table style='width:100%;'><tr><td colspan='2'>".$tooltip_info['unit_name']."</td></tr><tr><td>Available</td><td align='right'>".$tooltip_info["AvailQty"]."</td></tr><tr><td>Packed</td><td align='right'>".$tooltip_info["PackQty"]."</td></tr><tr><td>Obselete</td><td align='right'>".$tooltip_info["ObseQty"]."</td></tr><tr><td>In Transit</td><td align='right'>".$tooltip_info["IntrsQty"]."</td></tr></table>";

				$data['item_txn_qty'] = $data['item_txn_qty'] != 0 ? $data['item_txn_qty'] : 1;
				
	    		$result[] = [
	    				'item_id' 				=> $data['item_id'],
						'AvailQty'              => $tooltip_info["AvailQty"],
						'item_name' 			=> $data['item_name'],
						'mat_cent_id' 			=> $data['mat_cent_id'],
						'item_unit' 			=> $item_unit,
						'item_unit_id' 			=> $item_unit_id,
						'item_qty' 				=> $data['item_txn_qty'],
						'item_txn_drcr' 		=> $data['item_txn_drcr'],
						'item_amount' 			=> parseAmount($data['item_txn_amount'] * $fcy_rate),
						"pq_cellattr"           => array("item_name"=>array("title"=>$balancetable)),
						'description' 		    => $description,
						'item_price'			=> parseAmount(($data['item_txn_amount']/$data['item_txn_qty']) * $fcy_rate),
	    			];
	    	}
    	}
    	return $result;
    }

    function get_account_transactions_oth($voucher_txn_id)
    {
    	$fcy_rate = 1;

	   	$voucher_info = $this->get_voucher_cons_info($voucher_txn_id);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$fcy_rate = $this->get_forex_rate_fcy($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

    	$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($comp_txn_tbl);
    	$builder->where('voucher_txn_id', $voucher_txn_id);
    	$builder->where('master_id_type', 'aco');
    	$builder->orderBy('txn_id', 'asc');
    	$comp_txns = $builder->get()->getResultArray();
    	
    	$acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
    	$result = [];
    	foreach ($comp_txns as $key => $value) {
    		$builder = $this->db->table($acc_oth_tbl);
	    	$builder->where('voucher_txn_id', $voucher_txn_id);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
	    	$builder->where('acc_id', $value['master_id']);
	    	$builder->where('txn_id', $value['txn_id']);
	    	$data = $builder->get()->getRowArray();

	    	
	    	if($data){
	    		if($key > 0){ //exclude party account
	    			$account = $this->get_account_info($value['master_id']);
	    			$data['acc_name'] = $account['acc_name'];
					
					// get item narration_txt
					$get_narration_info = $this->get_voucher_narration_info($voucher_txn_id,'short',$value['txn_id']);
					
					if($get_narration_info){
						$acc_description = $get_narration_info['vch_short_narr'];
					}else{
						$acc_description = '';
					}
					
	    		
		    		$result[] = [
		    				'acc_id' 			=> $data['acc_id'],
							'account_name' 		=> $data['acc_name'],
							'description' 		=> $acc_description,
							'amount' 			=> parseAmount($data['acc_oth_txn_amount'] * $fcy_rate),
		    			];
	    		}
				
	    	}
    	}
    	return $result;
    }
	
	function get_journal_acc_transactions($voucher_txn_id){
		$inverse=false;
		$cc_groups = $this->get_cc_groups();

    	$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($comp_txn_tbl);
    	$builder->where('voucher_txn_id', $voucher_txn_id);
    	$builder->orderBy('txn_id', 'asc');
    	$comp_txns = $builder->get()->getResultArray();
		$result = [];
		$skiped_counter=0;
    	foreach ($comp_txns as $key => $value) {
			$filter_data[]=$value['master_id_type'];
			if($comp_txns[$key]['master_id_type']=='itm' || (isset($comp_txns[$key-1]) && $comp_txns[$key-1]['master_id_type']=='itm' && $comp_txns[$key]['master_id_type']=='acc')){
				
			}else{	
			
			
			 if($value['master_id_type']=='acc'){
				$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
				$builder = $this->db->table($acc_txn_tbl);
				$builder->where('voucher_txn_id', $voucher_txn_id);
				$builder->where('acc_id', $value['master_id']);
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));

				$builder->where('txn_id', $value['txn_id']);
				$data = $builder->get()->getRowArray();
				$account = $this->get_account_info($value['master_id']);
	    		$data['acc_name'] = $account['acc_name'];

                $is_cc = 0;
                if(in_array($account['acc_grp_id'], $cc_groups) || in_array($account['acc_grp_parent_id'], [7,11,13])){
                    $is_cc = 1;
                  }
				$result[] = [
		    				'acc_id' 	 => $data['acc_id'],
							'acc_name' 	 => $data['acc_name'],
							'acc_type' 	 => 'acc',
							'acc_amount' => $data['acc_txn_amount'],
							'is_cc'		 => $is_cc
		    			];
				 
			 }
		if($value['master_id_type']=='bsd'){
			$sundry_txn_tbl =  $this->company_id.'_sundrytxnn_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
    		$builder = $this->db->table($sundry_txn_tbl);
	    	$builder->where('voucher_txn_id', $voucher_txn_id);
	    	$builder->where('bill_sundry_id', $value['master_id']);
	    	$builder->where('txn_id', $value['txn_id']);
	    	$data = $builder->get()->getRowArray();	    	
	    	if($data){
    			$account = $this->get_billsundry_info($value['master_id']);
    			$data['bill_sundry_name'] = $account['bill_sundry_name'];

    			
    			if($inverse){
    				if($data['sundry_txn_drcr'] == 'd')
    					$amount = -$data['sundry_txn_amount'];
    				else
    					$amount = $data['sundry_txn_amount'];
    			}
    			else{
    				if($data['sundry_txn_drcr'] == 'c')
    					$amount = -$data['sundry_txn_amount'];
    				else
    					$amount = $data['sundry_txn_amount'];
    			}    			

	    		$result[] = [
	    				'acc_id'      => $data['bill_sundry_id'],
						'acc_name' 	  => $data['bill_sundry_name'],
						'acc_type' 	  => 'bsd',
						'acc_amount'  => $amount,
	    			]; 				 
			     } 	
			  }
			}				
	
		}
		$purchase_accounts = array();
		$sale_accounts     = array();
		
		
		if($result){
			foreach($result as $row){
				 if($row['acc_amount'] >0)
					 $purchase_accounts[]=$row;
				 else
					$sale_accounts[]=array("acc_id_to"=>$row['acc_id'],"acc_name_to"=>$row['acc_name'],"acc_type_to"=>$row['acc_type'],
				                            "acc_amount_to"=>abs($row['acc_amount'])); 
				
			}
			
		}
	  $response = array('purchase_accounts'=>$purchase_accounts,'sale_accounts'=>$sale_accounts);
	  return $response;	
	}

	function get_journal_item_account_transactions($voucher_txn_id)
	{
		$item_account_array = [];

		$item_transactions = $this->get_journal_item_transactions($voucher_txn_id);
        $l_items = $item_transactions['l_items'];
        $r_items= $item_transactions['r_items'];

        if(count($l_items)){ // d
        	foreach ($l_items as $key => $value) {
        		$item_info  =  $this->get_item_info($value['item_id']);
                $account_id  =  $item_info['item_pur_acc'];

                if(isset($item_account_array[$account_id]))
                    $item_account_array[$account_id]['amount'] += parseAmount($value['item_amount']);
                else{
                    $item_account_array[$account_id]['amount'] = parseAmount($value['item_amount']);
                    $item_account_array[$account_id]['drcr'] = 'd';
                    $item_account_array[$account_id]['status'] = true;
                }
        	}
        }

        if(count($r_items)){ // c
        	foreach ($r_items as $key => $value) {
        		$item_info  =  $this->get_item_info($value['item_id']);
                $account_id  =  $item_info['item_sales_acc'];

                if(isset($item_account_array[$account_id]))
                    $item_account_array[$account_id]['amount'] += parseAmount($value['item_amount']);
                else{
                    $item_account_array[$account_id]['amount'] = parseAmount($value['item_amount']);
                    $item_account_array[$account_id]['drcr'] = 'c';
                    $item_account_array[$account_id]['status'] = true;
                }
        	}
        }

        return $item_account_array;
	}
	
	function get_journal_account_transactions($voucher_txn_id)
    {
    	$fcy_rate = 1;

	   	$voucher_info = $this->get_voucher_cons_info($voucher_txn_id);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$fcy_rate = $this->get_forex_rate_fcy($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

	   	$cc_groups = $this->get_cc_groups();
	   	$bbb_groups = $this->get_bbb_groups();
	   	$item_account_array = $this->get_journal_item_account_transactions($voucher_txn_id);

    	$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($comp_txn_tbl);
    	$builder->where('voucher_txn_id', $voucher_txn_id);
    	$builder->whereIn('master_id_type', ['acc', 'bsd']);
    	$builder->orderBy('txn_id', 'asc');
    	$comp_txns = $builder->get()->getResultArray();
    	
    	$l_accounts = [];
    	$r_accounts = [];
    	if($comp_txns){
	    	foreach ($comp_txns as $key => $value) {



	    		if($value['master_id_type'] == 'acc')
	    		{
	    			

	    			$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
		    		$builder = $this->db->table($acc_txn_tbl);
			    	$builder->where('voucher_txn_id', $voucher_txn_id);
			    	$builder->where('acc_id', $value['master_id']);
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));

			    	$builder->where('txn_id', $value['txn_id']);
			    	$data = $builder->get()->getRowArray();

			    	if($data){
			    		$status = true;
			    		$data['acc_txn_amount'] = parseAmount($data['acc_txn_amount'] * $fcy_rate);

			    		if(count($item_account_array))
		    			{
	    					if(isset($item_account_array[$value['master_id']])){
	    						if($item_account_array[$value['master_id']]['status']){
	    							if($data['acc_txn_amount'] == $item_account_array[$value['master_id']]['amount']){
	    								if($data['acc_txn_drcr'] == $item_account_array[$value['master_id']]['drcr']){
	    									$item_account_array[$value['master_id']]['status'] = false;
	    									$status = false;
	    								}
	    							}
	    						}
	    					}
		    			}


			    		if($status){
			    			$account = $this->get_account_info($value['master_id']);
				    		$data['acc_name'] = $account['acc_name'];

				    		$is_cc = 0;
			                if(in_array($account['acc_grp_id'], $cc_groups) || in_array($account['acc_grp_parent_id'], [7,11,13])){
			                   $is_cc = 1;
			                }

			                $is_bbb = 0;
			                if(in_array($account['acc_grp_id'], $bbb_groups)){
			                   $is_bbb = 1;
	                		}

				    		if($data['acc_txn_drcr'] == 'd'){
				    			
					    		$l_accounts[] = [
					    				'acc_id' 			=> $data['acc_id'],
										'acc_name' 			=> $data['acc_name'],
										'acc_type' 			=> 'acc',
										'acc_amount' 		=> $data['acc_txn_amount'],
										'is_cc'				=> $is_cc,
										'is_bbb'			=> $is_bbb,
					    			];
				    		}
				    		if($data['acc_txn_drcr'] == 'c'){

					    		$r_accounts[] = [
					    				'acc_id' 			=> $data['acc_id'],
										'acc_name' 			=> $data['acc_name'],
										'acc_type' 			=> 'acc',
										'acc_amount' 		=> $data['acc_txn_amount'],
										'is_cc'				=> $is_cc,
										'is_bbb'			=> $is_bbb,
					    			];
				    		}
			    		}
			    	}	
	    		}
	    		if($value['master_id_type'] == 'bsd')
	    		{
	    			$sundry_txn_tbl =  $this->company_id.'_sundrytxnn_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
		    		$builder = $this->db->table($sundry_txn_tbl);
			    	$builder->where('voucher_txn_id', $voucher_txn_id);
			    	$builder->where('bill_sundry_id', $value['master_id']);
			    	$builder->where('txn_id', $value['txn_id']);
			    	$data = $builder->get()->getRowArray();

			    	
			    	if($data){
			    		$data['sundry_txn_amount'] = parseAmount($data['sundry_txn_amount'] * $fcy_rate);

			    		$account = $this->get_billsundry_info($value['master_id']);
		    			$data['bill_sundry_name'] = $account['bill_sundry_name'];

			    		if($data['sundry_txn_drcr'] == 'd'){

				    		$l_accounts[] = [
				    				'acc_id' 			=> $data['bill_sundry_id'],
									'acc_name' 			=> $data['bill_sundry_name'],
									'acc_type' 			=> 'bsd',
									'acc_amount' 		=> $data['sundry_txn_amount'],
									'is_cc'				=> 0,
									'is_bbb'			=> 0,
				    			];
			    		}
			    		if($data['sundry_txn_drcr'] == 'c'){

				    		$r_accounts[] = [
				    				'acc_id' 			=> $data['bill_sundry_id'],
									'acc_name' 			=> $data['bill_sundry_name'],
									'acc_type' 			=> 'bsd',
									'acc_amount' 		=> $data['sundry_txn_amount'],
									'is_cc'				=> 0,
									'is_bbb'			=> $is_bbb,
				    			];
			    		}
		    			
			    	}
	    		}
	    		
	    	}
    	}
    	
    	$final = [
    		'l_accounts' => $l_accounts,
    		'r_accounts' => $r_accounts
    	];
    	
    	return $final;
    }

    function get_account_transactions($voucher_txn_id)
    {
	   	$cc_groups = $this->get_cc_groups();
	   	$fcy_rate = 1;

	   	$voucher_info = $this->get_voucher_cons_info($voucher_txn_id);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$fcy_rate = $this->get_forex_rate_fcy($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

    	$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($comp_txn_tbl);
    	$builder->where('voucher_txn_id', $voucher_txn_id);
    	$builder->where('master_id_type', 'acc');
    	$builder->orderBy('txn_id', 'asc');
    	$comp_txns = $builder->get()->getResultArray();
    	
    	$result = [];
    	foreach ($comp_txns as $key => $value) {
    		$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
    		$builder = $this->db->table($acc_txn_tbl);
	    	$builder->where('voucher_txn_id', $voucher_txn_id);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));

	    	$builder->where('acc_id', $value['master_id']);
	    	$builder->where('txn_id', $value['txn_id']);
	    	$data = $builder->get()->getRowArray();

	    	
	    	if($data){
	    		if($key > 0){ //exclude party account
	    			$account = $this->get_account_info($value['master_id']);
	    			$data['acc_name'] = $account['acc_name'];

                    $is_cc = 0;
                    if(in_array($account['acc_grp_id'], $cc_groups) || in_array($account['acc_grp_parent_id'], [7,11,13])){
                       $is_cc = 1;
                    }
					
					// get item narration_txt
					$get_narration_info = $this->get_voucher_narration_info($voucher_txn_id,'short',$value['txn_id']);
					
					if($get_narration_info){
						$acc_description = $get_narration_info['vch_short_narr'];
					}else{
						$acc_description = '';
					}
					
		    		$result[] = [
	    				'acc_id' 			=> $data['acc_id'],
						'account_name' 		=> $data['acc_name'],
						'description' 		=> $acc_description,
						'amount' 			=> parseAmount($data['acc_txn_amount'] * $fcy_rate),
						'is_cc'				=> $is_cc
	    			];
	    		}
				
	    	}
    	}
    	return $result;
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
  
    function get_cash_groups()
    {// get branch accounts primary groups
	    $groups = $this->get_branch_primary_groups();
        array_push($groups,23);
		array_push($groups,21);
		//$groups = [23,21];
		$array = $this->get_sub_group_ids($groups);
        
        return $array;
    }

    function get_all_account_transactions($voucher_txn_id) //voucher contra payment receipt journal
    {
    	$fcy_rate = 1;

	   	$voucher_info = $this->get_voucher_cons_info($voucher_txn_id);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$fcy_rate = $this->get_forex_rate_fcy($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

	   	$cc_groups = $this->get_cc_groups();
	   	$bbb_groups = $this->get_bbb_groups();
	   	$cash_groups = $this->get_cash_groups();

    	$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($comp_txn_tbl);
    	$builder->where('voucher_txn_id', $voucher_txn_id);
    	$builder->whereIn('master_id_type', ['acc','bsd']);
    	$builder->orderBy('txn_id', 'asc');
    	$comp_txns = $builder->get()->getResultArray();
    	
    	$result = [];
    	foreach ($comp_txns as $key => $value) {

    		if($value['master_id_type'] == 'acc')
    		{
    			$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
	    		$builder = $this->db->table($acc_txn_tbl);
		    	$builder->where('voucher_txn_id', $voucher_txn_id);
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));

		    	$builder->where('acc_id', $value['master_id']);
		    	$builder->where('txn_id', $value['txn_id']);
		    	$data = $builder->get()->getRowArray();

		    	
		    	if($data){

	    			$account = $this->get_account_info($value['master_id']);
	    			$data['acc_name'] = $account['acc_name'];

	                $is_cc = 0;
	                if(in_array($account['acc_grp_id'], $cc_groups) || in_array($account['acc_grp_parent_id'], [7,11,13])){
	                   $is_cc = 1;
	                }

	                $is_bbb = 0;
	                if(in_array($account['acc_grp_id'], $bbb_groups)){
	                   $is_bbb = 1;
	                }
					
					$is_cash = 0;
			       	if(in_array($account['acc_grp_id'], $cash_groups)){
			          	$is_cash = 1;
			       	}

					// get item narration_txt
					$get_narration_info = $this->get_voucher_narration_info($voucher_txn_id,'short',$value['txn_id']);
					
					if($get_narration_info){
						$acc_description = $get_narration_info['vch_short_narr'];
					}else{
						$acc_description = '';
					}

					if($data['acc_txn_drcr'] == 'c'){
	                    $credit = parseAmount($data["acc_txn_amount"] * $fcy_rate);
	                    $debit = '';
	                    $acc_txn_drcr ='C';
	                }
	                else{
	                    $debit = parseAmount($data["acc_txn_amount"] * $fcy_rate);
	                    $credit = '';
	                    $acc_txn_drcr ='D';
	                }
					
		    		$result[] = [
		    				'account_id' 		=> $data['acc_id'],
							'account_name' 		=> $data['acc_name'],
							'acc_type' 			=> 'acc',
							'description' 		=> $acc_description,
							'credit' 			=> $credit,
							'debit' 			=> $debit,
							'drcr'				=> $acc_txn_drcr,
							'is_cc'				=> $is_cc,
							'is_bbb'			=> $is_bbb,
							'is_cash'	  		=> $is_cash,
		    			];	
		    	}
    		}
    		
	    	if($value['master_id_type'] == 'bsd')
    		{
    			$sundry_txn_tbl =  $this->company_id.'_sundrytxnn_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
	    		$builder = $this->db->table($sundry_txn_tbl);
		    	$builder->where('voucher_txn_id', $voucher_txn_id);
		    	$builder->where('bill_sundry_id', $value['master_id']);
		    	$builder->where('txn_id', $value['txn_id']);
		    	$data = $builder->get()->getRowArray();

		    	
		    	if($data){
		    		$account = $this->get_billsundry_info($value['master_id']);
	    			$data['bill_sundry_name'] = $account['bill_sundry_name'];


					$get_narration_info = $this->get_voucher_narration_info($voucher_txn_id,'short',$value['txn_id']);
					if($get_narration_info){
						$acc_description = $get_narration_info['vch_short_narr'];
					}else{
						$acc_description = '';
					}

	    			if($data['sundry_txn_drcr'] == 'c'){
	                    $credit = parseAmount($data["sundry_txn_amount"] * $fcy_rate);
	                    $debit = '';
	                    $sundry_txn_drcr ='C';
	                }
	                else{
	                    $debit = parseAmount($data["sundry_txn_amount"] * $fcy_rate);
	                    $credit = '';
	                    $sundry_txn_drcr ='D';
	                }

	                $result[] = [
	    				'account_id' 		=> $data['bill_sundry_id'],
						'account_name' 		=> $data['bill_sundry_name'],
						'acc_type' 			=> 'bsd',
						'description' 		=> $acc_description,
						'credit' 			=> $credit,
						'debit' 			=> $debit,
						'drcr'				=> $sundry_txn_drcr,
						'is_cc'				=> 0,
						'is_bbb'			=> 0,
						'is_cash'	  		=> 0,
	    			];
		    	}
    		}
    	}
    	return $result;
    }

    function get_all_account_oth_transactions($voucher_txn_id) //voucher contra payment receipt journal
    {
    	$fcy_rate = 1;

	   	$voucher_info = $this->get_voucher_cons_info($voucher_txn_id);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$fcy_rate = $this->get_forex_rate_fcy($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

	   	$cc_groups = $this->get_cc_groups();
	   	$bbb_groups = $this->get_bbb_groups();
	   	$cash_groups = $this->get_cash_groups();

    	$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($comp_txn_tbl);
    	$builder->where('voucher_txn_id', $voucher_txn_id);
    	$builder->whereIn('master_id_type', ['aco','bso']);
    	$builder->orderBy('txn_id', 'asc');
    	$comp_txns = $builder->get()->getResultArray();
    	
    	$acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
    	$result = [];
    	foreach ($comp_txns as $key => $value) {
    		
    		if($value['master_id_type'] == 'aco')
    		{
	    		$builder = $this->db->table($acc_oth_tbl);
		    	$builder->where('voucher_txn_id', $voucher_txn_id);
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
		    	$builder->where('acc_id', $value['master_id']);
		    	$builder->where('txn_id', $value['txn_id']);
		    	$data = $builder->get()->getRowArray();

		    	
		    	if($data){

	    			$account = $this->get_account_info($value['master_id']);
	    			$data['acc_name'] = $account['acc_name'];

	                $is_cc = 0;
	                if(in_array($account['acc_grp_id'], $cc_groups) || in_array($account['acc_grp_parent_id'], [7,11,13])){
	                   $is_cc = 1;
	                }

	                $is_bbb = 0;
	                if(in_array($account['acc_grp_id'], $bbb_groups)){
	                   $is_bbb = 1;
	                }
					
					$is_cash = 0;
			       	if(in_array($account['acc_grp_id'], $cash_groups)){
			          	$is_cash = 1;
			       	}

					// get item narration_txt
					$get_narration_info = $this->get_voucher_narration_info($voucher_txn_id,'short',$value['txn_id']);
					
					if($get_narration_info){
						$acc_description = $get_narration_info['vch_short_narr'];
					}else{
						$acc_description = '';
					}

					if($data['acc_oth_txn_drcr'] == 'c'){
	                    $credit = parseAmount($data["acc_oth_txn_amount"] * $fcy_rate);
	                    $debit = '';
	                    $acc_txn_drcr ='C';
	                }
	                else{
	                    $debit = parseAmount($data["acc_oth_txn_amount"] * $fcy_rate);
	                    $credit = '';
	                    $acc_txn_drcr ='D';
	                }
					
		    		$result[] = [
		    				'account_id' 		=> $data['acc_id'],
							'account_name' 		=> $data['acc_name'],
							'acc_type' 			=> 'acc',
							'description' 		=> $acc_description,
							'credit' 			=> $credit,
							'debit' 			=> $debit,
							'drcr'				=> $acc_txn_drcr,
							'is_cc'				=> $is_cc,
							'is_bbb'			=> $is_bbb,
							'is_cash'	  		=> $is_cash,
		    			];	
		    	}
	    	}
	    	if($value['master_id_type'] == 'bso')
    		{
    			$builder = $this->db->table($acc_oth_tbl);
		    	$builder->where('voucher_txn_id', $voucher_txn_id);
		    	$builder->where('acc_id', $value['master_id']);
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
		    	$builder->where('txn_id', $value['txn_id']);
		    	$data = $builder->get()->getRowArray();

		    	
		    	if($data){

	    			$account = $this->get_billsundry_info($value['master_id']);
	    			$data['bill_sundry_name'] = $account['bill_sundry_name'];
	    	

					// get item narration_txt
					$get_narration_info = $this->get_voucher_narration_info($voucher_txn_id,'short',$value['txn_id']);
					
					if($get_narration_info){
						$acc_description = $get_narration_info['vch_short_narr'];
					}else{
						$acc_description = '';
					}

					if($data['acc_oth_txn_drcr'] == 'c'){
	                    $credit = parseAmount($data["acc_oth_txn_amount"] * $fcy_rate);
	                    $debit = '';
	                    $sundry_txn_drcr ='C';
	                }
	                else{
	                    $debit = parseAmount($data["acc_oth_txn_amount"] * $fcy_rate);
	                    $credit = '';
	                    $sundry_txn_drcr ='D';
	                }
					
		    		$result[] = [
	    				'account_id' 		=> $value['master_id'],
						'account_name' 		=> $data['bill_sundry_name'],
						'acc_type' 			=> 'bsd',
						'description' 		=> $acc_description,
						'credit' 			=> $credit,
						'debit' 			=> $debit,
						'drcr'				=> $sundry_txn_drcr,
						'is_cc'				=> 0,
						'is_bbb'			=> 0,
						'is_cash'	  		=> 0,
	    			];	
		    	}
    		}
    	}
    	return $result;
    }

    function get_memo_account_transactions($voucher_txn_id) 
    {
    	$fcy_rate = 1;

	   	$voucher_info = $this->get_voucher_cons_info($voucher_txn_id);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$fcy_rate = $this->get_forex_rate_fcy($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

    	$memo_txn_tbl = $this->company_id.'_memotxnnnn_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($memo_txn_tbl);
    	$builder->where('voucher_txn_id', $voucher_txn_id);
    	$builder->orderBy('acc_txn_id', 'asc');
    	$comp_txns = $builder->get()->getResultArray();
    	
    	$result = [];
    	foreach ($comp_txns as $key => $value) {

    		$acc_name = '';
			$account = $this->get_account_info($value['acc_id']);
			if($account){
				$acc_name = $account['acc_name'];
			}

			// get item narration_txt
			$get_narration_info = $this->get_voucher_narration_info($voucher_txn_id,'short',$value['acc_txn_id']);
			
			if($get_narration_info){
				$acc_description = $get_narration_info['vch_short_narr'];
			}else{
				$acc_description = '';
			}

			if($value['acc_txn_drcr'] == 'c'){
                $credit = parseAmount($value["acc_txn_amount"] * $fcy_rate);
                $debit = '';
                $acc_txn_drcr ='C';
            }
            else{
                $debit = parseAmount($value["acc_txn_amount"] * $fcy_rate);
                $credit = '';
                $acc_txn_drcr ='D';
            }
			
    		$result[] = [
    				'acc_id' 		    => $value['acc_id'],
    				'acc_type' 		    => $value['acc_type'],
					'acc_name' 		    => $acc_name,
					'description' 		=> $acc_description,
					'credit' 			=> $credit,
					'debit' 			=> $debit,
					'drcr'				=> $acc_txn_drcr,
    			];	
	    }
    	
    	return $result;
    }

    function get_sundry_transactions_oth($voucher_txn_id, $inverse = false)
    {
    	$fcy_rate = 1;

	   	$voucher_info = $this->get_voucher_cons_info($voucher_txn_id);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$fcy_rate = $this->get_forex_rate_fcy($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

    	$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($comp_txn_tbl);
    	$builder->where('voucher_txn_id', $voucher_txn_id);
    	$builder->where('master_id_type', 'bso');
    	$builder->orderBy('txn_id', 'asc');
    	$comp_txns = $builder->get()->getResultArray();

    	$result = [];
    	foreach ($comp_txns as $key => $value) {
    		$acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
    		$builder = $this->db->table($acc_oth_tbl);
	    	$builder->where('voucher_txn_id', $voucher_txn_id);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
	    	$builder->where('acc_id', $value['master_id']);
	    	$builder->where('txn_id', $value['txn_id']);
	    	$data = $builder->get()->getRowArray();

	    	
	    	if($data){
    			$account = $this->get_billsundry_info($value['master_id']);
    			$data['bill_sundry_name'] = $account['bill_sundry_name'];

    			if($inverse){
    				if($data['acc_oth_txn_drcr'] == 'd')
    					$amount = -$data['acc_oth_txn_amount'];
    				else
    					$amount = $data['acc_oth_txn_amount'];
    			}
    			else{
    				if($data['acc_oth_txn_drcr'] == 'c')
    					$amount = -$data['acc_oth_txn_amount'];
    				else
    					$amount = $data['acc_oth_txn_amount'];
    			}

	    		$result[] = [
	    				'billsundry_id' 		=> $data['acc_id'],
						'billsundry_name' 		=> $data['bill_sundry_name'],
						'billsundry_rate' 		=> $data['acc_oth_txn_status'],
						'billsundry_amount' 	=> parseAmount($amount * $fcy_rate),
	    			];
	    	}
    	}
    	return $result;
    }

    function get_sundry_transactions($voucher_txn_id, $inverse = false)
    {
    	$fcy_rate = 1;

	   	$voucher_info = $this->get_voucher_cons_info($voucher_txn_id);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$fcy_rate = $this->get_forex_rate_fcy($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

    	$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($comp_txn_tbl);
    	$builder->where('voucher_txn_id', $voucher_txn_id);
    	$builder->where('master_id_type', 'bsd');
    	$builder->orderBy('txn_id', 'asc');
    	$comp_txns = $builder->get()->getResultArray();

    	$result = [];
    	foreach ($comp_txns as $key => $value) {
    		$sundry_txn_tbl =  $this->company_id.'_sundrytxnn_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
    		$builder = $this->db->table($sundry_txn_tbl);
	    	$builder->where('voucher_txn_id', $voucher_txn_id);
	    	$builder->where('bill_sundry_id', $value['master_id']);
	    	$builder->where('txn_id', $value['txn_id']);
	    	$data = $builder->get()->getRowArray();

	    	
	    	if($data){
    			$account = $this->get_billsundry_info($value['master_id']);
    			$data['bill_sundry_name'] = $account['bill_sundry_name'];

    			
    			if($inverse){
    				if($data['sundry_txn_drcr'] == 'd')
    					$amount = -$data['sundry_txn_amount'];
    				else
    					$amount = $data['sundry_txn_amount'];
    			}
    			else{
    				if($data['sundry_txn_drcr'] == 'c')
    					$amount = -$data['sundry_txn_amount'];
    				else
    					$amount = $data['sundry_txn_amount'];
    			}
    			

	    		$result[] = [
	    				'billsundry_id' 		=> $data['bill_sundry_id'],
						'billsundry_name' 		=> $data['bill_sundry_name'],
						'billsundry_rate' 		=> $data['sundry_tag_rate'],
						'billsundry_amount' 	=> parseAmount($amount * $fcy_rate),
	    			];
	    	}
    	}
    	return $result;
    }
    	
		
    function get_voucher_info($voucher_type_id){	 
	  $comp_voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	   $comp_vhtxnconso_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	  $data = $this->db->table($comp_voucher_type_tbl)->where('voucher_type_id', $voucher_type_id)->where('comp_id', $this->company_id)->get()->getRowArray(); 
	 
	      // get voucher last entry date
	      $builder = $this->db->table($comp_vhtxnconso_tbl);
	      $builder->select('voucher_date');
	      $builder->where('voucher_type_id', $voucher_type_id);
	      $builder->orderBy('voucher_txn_id','DESC');
	      $builder->limit(1);
	      $response = $builder->get()->getRowArray();   
	       if($response){
	        $data['last_entry'] = date('d-m-Y',strtotime($response['voucher_date']));   
	      }else
	        $data['last_entry'] = date('d-m-Y');  
	  
	  return $data;
    }

    

    function update_voucher_cons_data($id,$data){
		 $comp_vch_cons_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	     $this->db->table($comp_vch_cons_tbl)->where('voucher_txn_id',$id)->update($data);		
	}

    

    function delete_acc_memo_data($voucher_txn_id)
    {
         $account_table_name = $this->company_id.'_memotxnnnn_'.$this->session->get('ses_comp_fy_id');

         $acc_id_array = [];
         $result = $this->db->table($account_table_name)->where('voucher_txn_id',$voucher_txn_id)->get()->getResultArray();
         foreach ($result as $key => $value) {

         	if (!in_array($value['acc_id'],$acc_id_array)){
         		array_push($acc_id_array, $value['acc_id']);
         	}
         }

		 $this->db->table($account_table_name)->where('voucher_txn_id',$voucher_txn_id)->delete();

		 foreach ($acc_id_array as $acc_id) {
		 	$this->update_account_memo_balance($acc_id);
		 }
		 
    }

    function delete_acc_txn_data($account_id,$voucher_txn_id)
    {
    	try{
         $account_table_name = $this->company_id.'_accnttxnnn_'.$account_id.'_'.$this->session->get('ses_comp_fy_id');
		 if($this->session->get('ses_boid')!='')
			$this->db->table($account_table_name)->where('bo_id', $this->session->get('ses_boid'))->where('voucher_txn_id',$voucher_txn_id)->delete();
			else		 
		 $this->db->table($account_table_name)->where('voucher_txn_id',$voucher_txn_id)->delete();

		 $this->update_account_balance($account_id);
		}
		 catch (\Exception $e) {
		  echo " Error- ".$e->getMessage()."<br>";
		}
    }
   
   function update_voucher_narration($vch_txn_id,$txn_id,$narr_type,$narration_txt){
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
  
  function save_voucher_narration($vch_txn_id,$txn_id,$narr_type,$narration_txt){
	  
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
  
  public function get_itemstable_rownums($tblname){
        $builder = $this->db->table($tblname);
        $builder->select('*,ROW_NUMBER() OVER (ORDER BY item_txn_date) row_num');
        $result = $builder->get()->getResultArray();
        return $result;
     }
	public function update_itm_txn_entries($table_name,$update_data,$txn_id,$comp_id){
       $this->db->table($table_name)->where('txn_id',$txn_id)->where('comp_id',$comp_id)->update($update_data);	
     } 
   
    function get_item_txn_id($table_name,$item_id,$txn_id){
		$row= $this->db->table($table_name)->where('item_id',$item_id)->where('txn_id',$txn_id)->where('comp_id',$this->company_id)->get()->getRowArray();
		return $row['item_txn_id'];
		
	}   
	
    
    function delete_tracking_data($item_id,$txn_id)
    {
         $itemtxnnnn_tbl =  $this->company_id.'_itemtrackn_'.$this->session->get('ses_comp_fy_id');
		 $this->db->table($itemtxnnnn_tbl)->where('tracking_id >',0)->where('txn_id',$txn_id)->where('item_id',$item_id)->delete();
    }
    
     function delete_itm_txn_data($item_id,$voucher_txn_id)
    {
         $itemtxnnnn_tbl =  $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		 if($this->session->get('ses_boid')!='')
		 $this->db->table($itemtxnnnn_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('voucher_txn_id',$voucher_txn_id)->delete();
		 else 
			 $this->db->table($itemtxnnnn_tbl)->where('voucher_txn_id',$voucher_txn_id)->delete();
    }
    
    
    
    function delete_itm_bal_data($item_id,$voucher_txn_id)
    {
         $itemtxnbal_tbl =  $this->company_id.'_itemtxnbal_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		 // fetch txnbal data and delet item avg data also after that delet txnbal entries
		 
		 $itemtxnvaln_tbl =  $this->company_id.'_itemtxnvaln_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		 if($this->session->get('ses_boid')!='')
		   $txndata = $this->db->table($itemtxnbal_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('item_id', $item_id)->get()->getResultArray();
		 else
		 $txndata = $this->db->table($itemtxnbal_tbl)->where('item_id', $item_id)->get()->getResultArray();
		 if($txndata){
			 foreach($txndata as $rr){
				  $itemtxnbal_id = $rr['itemtxnbal_id'];
				  $item_id       = $rr['item_id'];				  
				  $this->db->table($itemtxnvaln_tbl)->where('item_id',$item_id)->where('itemtxnbal_id',$itemtxnbal_id)->delete();				 
			     }
		   }
		 
		 $this->db->table($itemtxnbal_tbl)->where('voucher_txn_id',$voucher_txn_id)->delete();
    }

    
    function delete_sundry_txn_data($bsd,$voucher_txn_id)
    {
         $sundry_txn_tbl =  $this->company_id.'_sundrytxnn_'.$bsd.'_'.$this->session->get('ses_comp_fy_id');
		 $this->db->table($sundry_txn_tbl)->where('voucher_txn_id',$voucher_txn_id)->delete();

		 $this->update_bill_sundry_balance($bsd);
    }

    

    function update_account_balance($acc_id)
    {
        $bal=0; //include opening balance

        $accoppybal_tbl = $this->company_id.'_accoppybal_'.$this->session->get('ses_comp_fy_id');
		 if($this->session->get('ses_boid')!='')
       return $this->db->table($accoppybal_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('acc_id', $acc_id)->get()->getRowArray(); 
 
	   else
		$balance = $this->db->table($accoppybal_tbl)->where('acc_id', $acc_id)->get()->getRowArray();
		if($balance){
			$bal  = $balance['acc_op_bal'];
		}

        $acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$acc_id.'_'.$this->session->get('ses_comp_fy_id');
		$this->db->query('SET @bal = '.$bal.';');
		
		if($this->session->get('ses_boid')!=''){
			$bo_id = $this->session->get('ses_boid');
			$this->db->query('UPDATE '.$acc_txn_tbl.' SET acc_bal = CASE WHEN acc_txn_drcr = "c" THEN @bal:=@bal - acc_txn_amount WHEN acc_txn_drcr = "d" THEN @bal:=@bal + acc_txn_amount ELSE 0 END where acc_id = '.$acc_id.' and bo_id = '.$bo_id.' order by acc_txn_date,voucher_txn_id,acc_txn_id;');
		}else 
		$this->db->query('UPDATE '.$acc_txn_tbl.' SET acc_bal = CASE WHEN acc_txn_drcr = "c" THEN @bal:=@bal - acc_txn_amount WHEN acc_txn_drcr = "d" THEN @bal:=@bal + acc_txn_amount ELSE 0 END where acc_id = '.$acc_id.' order by acc_txn_date,voucher_txn_id,acc_txn_id;');
    }
	
    // function update_account_balance($acc_id)
    // {
    //     $bal=0; //include opening balance

    //     $accoppybal_tbl = $this->company_id.'_accoppybal_'.$this->session->get('ses_comp_fy_id');
	// 	$balance = $this->db->table($accoppybal_tbl)->where('acc_id', $acc_id)->get()->getRowArray();
	// 	if($balance){
	// 		$bal  = $balance['acc_op_bal'];
	// 	}

	// 	$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$acc_id.'_'.$this->session->get('ses_comp_fy_id');
	// 	$result = $this->db->table($acc_txn_tbl)
    //     					->where('acc_id', $acc_id)
    //     					->orderBy('acc_txn_date', 'asc')
    //     					->orderBy('acc_txn_id', 'asc')
    //     					->get()->getResultArray();
							
	// 	 if($result){
	//         foreach($result as $key2 => $value){
	//         	if($value['acc_txn_drcr'] == 'd')
	//             	$bal += $value['acc_txn_amount'];
	//             if($value['acc_txn_drcr'] == 'c')
	//             	$bal += -$value['acc_txn_amount'];
	            
	//             $this->db->table($acc_txn_tbl)->where('acc_txn_id', $value['acc_txn_id'])->update(['acc_bal' => $bal]);
	//         } 
    // 	}  
    // }

    

    function update_account_memo_balance($acc_id)
    {
        $bal=0; //include opening balance

		$acc_txn_tbl = $this->company_id.'_memotxnnnn_'.$this->session->get('ses_comp_fy_id');
		$this->db->query('SET @bal = '.$bal.';');
		$this->db->query('UPDATE '.$acc_txn_tbl.' SET acc_bal = CASE WHEN acc_txn_drcr = "c" THEN @bal:=@bal - acc_txn_amount WHEN acc_txn_drcr = "d" THEN @bal:=@bal + acc_txn_amount ELSE 0 END where acc_id = '.$acc_id.' order by acc_txn_date, acc_txn_id;'); 
    }

    // function update_account_memo_balance($acc_id)
    // {
    //     $bal=0; //include opening balance

	// 	$acc_txn_tbl = $this->company_id.'_memotxnnnn_'.$this->session->get('ses_comp_fy_id');
	// 	$result = $this->db->table($acc_txn_tbl)
    //     					->where('acc_id', $acc_id)
    //     					->orderBy('acc_txn_date', 'asc')
    //     					->orderBy('acc_txn_id', 'asc')
    //     					->get()->getResultArray();
							
	// 	 if($result){
	//         foreach($result as $key2 => $value){
	//         	if($value['acc_txn_drcr'] == 'd')
	//             	$bal += $value['acc_txn_amount'];
	//             if($value['acc_txn_drcr'] == 'c')
	//             	$bal += -$value['acc_txn_amount'];
	            
	//             $this->db->table($acc_txn_tbl)->where('acc_txn_id', $value['acc_txn_id'])->update(['acc_bal' => $bal]);
	//         } 
    // 	}  
    // }

    function against_dropdown($voucher_type_id,$tag_array){
        $accttxnoth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!=''){
$result         =  $this->db->table($accttxnoth_tbl)
                                   ->where('bo_id', $this->session->get('ses_boid'))
         							->where("voucher_type_id", $voucher_type_id)
         							->whereIn('acc_oth_txn_tag', $tag_array)
         							->get()->getResultArray();
		}
else{		
        $result         =  $this->db->table($accttxnoth_tbl)
         							->where("voucher_type_id", $voucher_type_id)
         							->whereIn('acc_oth_txn_tag', $tag_array)
         							->get()->getResultArray();
}

	     $final_list     = array();
	     $final_list[''] = array(''=>'Choose');
         if($result){
             foreach($result as $row){
                  $voucher_cons_info = $this->get_voucher_cons_info($row['voucher_txn_id'],$row['voucher_type_id']);
                  $final_list[$row['voucher_txn_id']]= 'Voucher No. '. $voucher_cons_info['comp_vch_no'];
             }
         }
         return $final_list;
    }
    function against_crsref_dropdown($voucher_type_id,$tag_array){
        $acctcrsref_tbl = $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
        if($this->session->get('ses_boid')!=''){
			$result         =  $this->db->table($acctcrsref_tbl)
         							->where("acct_crs_id_type", $voucher_type_id)
									->where('bo_id', $this->session->get('ses_boid'))
         							->whereIn('acc_cross_ref_type', $tag_array)
         							->get()->getResultArray();
		}else{
		
		$result         =  $this->db->table($acctcrsref_tbl)
         							->where("acct_crs_id_type", $voucher_type_id)
         							->whereIn('acc_cross_ref_type', $tag_array)
         							->get()->getResultArray();
		}

	     $final_list     = array();
	     $final_list[''] = array(''=>'Choose');
         if($result){
             foreach($result as $row){
                  $voucher_cons_info = $this->get_voucher_cons_info($row['voucher_txn_id'],$row['acct_crs_id_type']);
                  $final_list[$row['voucher_txn_id']]= 'Voucher No. '. $voucher_cons_info['comp_vch_no'];
             }
         }
         return $final_list;
    }
    function against_voucher_dropdown($voucher_type_id,$voucher_subtype_array,$date)
    {
        $vhtxnconso_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	    $result = $this->db->table($vhtxnconso_tbl)
	    					->where('voucher_type_id',$voucher_type_id)
	    					->whereIn('vch_subtype_id',$voucher_subtype_array)
	    					->where('voucher_date <=',$date)
	    					->get()->getResultArray();

	    $final_list=array();     
        if($result){
            foreach($result as $row){
                
                $final_list[$row['voucher_txn_id']]= "Vch No.- ". $row['comp_vch_no'];
            }
            
        }
     	return $final_list;
    }

    function check_party_oth_status($voucher_txn_id, $tag)
    {
    	$accttxnoth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!=''){
		$data  =  $this->db->table($accttxnoth_tbl)->where("voucher_txn_id", $voucher_txn_id)
									->where('acc_oth_txn_tag', $tag)
									->where('bo_id', $this->session->get('ses_boid')) 
									->get()->getRowArray();	
		}else{
		$data  =  $this->db->table($accttxnoth_tbl)->where("voucher_txn_id", $voucher_txn_id)
									->where('acc_oth_txn_tag', $tag)
									->get()->getRowArray();
		}
		if($data)
			return true;
		return false;
    }


    function billsundry_items(){
		
		if(isset($_GET['term'])){ 
			$searchtext        = $_GET['term'];
			
			$billsundry_tbl  = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
			if($this->session->get('ses_boid')!=''){
			  $data            =  $this->db->table($billsundry_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('( `bill_sundry_name` LIKE  "%'.$searchtext.'%" OR `bill_sundry_alias` LIKE  "%'.$searchtext.'%" ) ')->orderBy('bill_sundry_name','ASC')->get()->getResultArray();	
			}else{
			  $data            =  $this->db->table($billsundry_tbl)->where('( `bill_sundry_name` LIKE  "%'.$searchtext.'%" OR `bill_sundry_alias` LIKE  "%'.$searchtext.'%" ) ')->orderBy('bill_sundry_name','ASC')->get()->getResultArray();	
			}
			
			
			$final_result    = array();
			if($data){
				foreach($data as $row){
					$final_result[]    = array("label"=>ucwords($row['bill_sundry_name']),'value'=> $row['bill_sundry_id']);	
				}
			}
		return json_encode($final_result);  
		}
    }

    function get_party_oth_summary($voucher_txn_id,$tag)
    {
      $cr_total=0;
      $dr_total =0;

      $acct_txnoth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
	  if($this->session->get('ses_boid')!=''){
       $result =  $this->db->table($acct_txnoth_tbl)
      						->where('acc_oth_txn_status', $voucher_txn_id)
							->where('bo_id', $this->session->get('ses_boid'))
      						->like('acc_oth_txn_tag', $tag)
      						->get()->getResultArray(); 
	  }else{
      $result =  $this->db->table($acct_txnoth_tbl)
      						->where('acc_oth_txn_status', $voucher_txn_id)
      						->like('acc_oth_txn_tag', $tag)
      						->get()->getResultArray(); 
	  }
      if($result){
          foreach($result as $row){
              if($row['acc_oth_txn_drcr']=='c')
                 $cr_total += $row['acc_oth_txn_amount'];
              if($row['acc_oth_txn_drcr']=='d')
                 $dr_total += $row['acc_oth_txn_amount'];
              
          }
          
      }
     return array('cr_total'=>$cr_total,'dr_total'=>$dr_total); 
  }

  	function update_accttxnoth_party_status($voucher_txn_id,$tag,$status)
  	{
  		$acct_txnoth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
		 if($this->session->get('ses_boid')!=''){
		$this->db->table($acct_txnoth_tbl)
  				 ->where('acc_oth_txn_status', $voucher_txn_id)
				 ->where('bo_id', $this->session->get('ses_boid'))
  				 ->like('acc_oth_txn_tag', $tag)
  				 ->update(['acc_oth_txn_tag' => $status]);	 
		 }else{
  		$this->db->table($acct_txnoth_tbl)
  				 ->where('acc_oth_txn_status', $voucher_txn_id)
  				 ->like('acc_oth_txn_tag', $tag)
  				 ->update(['acc_oth_txn_tag' => $status]);
		 }
  	}

  	function update_bill_sundry_balance($bill_sundry_id)
    {
        $bal = 0;
        
        $bsdoppybal_tbl = $this->company_id.'_bsdoppybal_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!='')
		$result = $this->db->table($bsdoppybal_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('bill_sundry_id', $bill_sundry_id)->get()->getRowArray();
		else 
        $result = $this->db->table($bsdoppybal_tbl)->where('bill_sundry_id', $bill_sundry_id)->get()->getRowArray();
        
        $bal = !empty($result['bsd_op_bal']) ? $result['bsd_op_bal'] : 0;

    	$bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$bill_sundry_id.'_'.$this->session->get('ses_comp_fy_id');
		$this->db->query('SET @bal = '.$bal.';');
		$this->db->query('UPDATE '.$bs_txn_tbl.' SET sundry_bal = CASE WHEN sundry_txn_drcr = "c" THEN @bal:=@bal - sundry_txn_amount WHEN sundry_txn_drcr = "d" THEN @bal:=@bal + sundry_txn_amount ELSE 0 END where bill_sundry_id = '.$bill_sundry_id.' order by sundry_txn_date, sundry_txn_id;');
    }

  	// function update_bill_sundry_balance($bill_sundry_id)
    // {
    //     $bal = 0;
        
    //     $bill_sundry_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
    //     $result = $this->db->table($bill_sundry_tbl)->where('bill_sundry_id', $bill_sundry_id)->get()->getRowArray();
        
    //     $bal = !empty($result['sundry_bal']) ? $result['sundry_bal'] : 0;

    //     $bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$bill_sundry_id.'_'.$this->session->get('ses_comp_fy_id');
    //     $result = $this->db->table($bs_txn_tbl)
    //     					->where('bill_sundry_id', $bill_sundry_id)
    //     					->orderBy('sundry_txn_date', 'asc')
    //     					->orderBy('sundry_txn_id', 'asc')
    //     					->get()->getResultArray();
        
    //     if($result){
	//         foreach($result as $key => $value){
	//         	if($value['sundry_txn_drcr'] == 'd')
	//             	$bal += $value['sundry_txn_amount'];
	//             if($value['sundry_txn_drcr'] == 'c')
	//             	$bal += -$value['sundry_txn_amount'];
	            
	//             $this->db->table($bs_txn_tbl)->where('sundry_txn_id', $value['sundry_txn_id'])->update(['sundry_bal' => $bal]);
	//         } 
    // 	}
    // }

    function getUndefinedBillRefId($account_id)
    {
        $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!='')
		$data = $this->db->table($bill_mst_tbl)->select('bills_ref_id')->where('bo_id', $this->session->get('ses_boid'))->where('acc_id', $account_id)->where('bills_ref_name', 'UNDEFINED')->get()->getRowArray();
     else
        $data = $this->db->table($bill_mst_tbl)->select('bills_ref_id')->where('acc_id', $account_id)->where('bills_ref_name', 'UNDEFINED')->get()->getRowArray();
      if($data)
	  return $data['bills_ref_id'];
      else 
	return "0";	  
    }
     
    
    function update_bill_master($id,$data)
    {
        $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
        $this->db->table($bill_mst_tbl)->where('bills_ref_id', $id)->update($data);
    }
    
    function delete_bills_txn($voucher_txn_id)
    {
    	$bill_txn_tbl = $this->company_id.'_billstxnnn_'.$this->session->get('ses_comp_fy_id');
		
		if($this->session->get('ses_boid')!='')
		$result = $this->db->table($bill_txn_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('voucher_txn_id', $voucher_txn_id)->get()->getResultArray();
		else
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
    	$bal = 0;
		$bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
        $data = $this->db->table($bill_mst_tbl)
        				->where('bills_ref_id', $bills_ref_id)
        				->get()->getRowArray();
        if($data)
        	$bal = $data['bill_op_bal'];

    	$bill_txn_tbl = $this->company_id.'_billstxnnn_'.$this->session->get('ses_comp_fy_id');
		$this->db->query('SET @bal = '.$bal.';');
		
		if($this->session->get('ses_boid')!=''){
		$bo_id = $this->session->get('ses_boid');
		$this->db->query('UPDATE '.$bill_txn_tbl.' SET bills_txn_bal = CASE WHEN bills_txn_drcr = "C" THEN @bal:=@bal - bills_txn_amt WHEN bills_txn_drcr = "D" THEN @bal:=@bal + bills_txn_amt ELSE 0 END where bo_id = '.$bo_id.' AND bills_ref_id = '.$bills_ref_id.' order by bills_txn_date, bills_txn_id;');  
		}
		else
		$this->db->query('UPDATE '.$bill_txn_tbl.' SET bills_txn_bal = CASE WHEN bills_txn_drcr = "C" THEN @bal:=@bal - bills_txn_amt WHEN bills_txn_drcr = "D" THEN @bal:=@bal + bills_txn_amt ELSE 0 END where bills_ref_id = '.$bills_ref_id.' order by bills_txn_date, bills_txn_id;');  
    }

    // function update_bill_txn_balance($bills_ref_id)
    // {
    //     $bill_txn_tbl = $this->company_id.'_billstxnnn_'.$this->session->get('ses_comp_fy_id');
    //     $result = $this->db->table($bill_txn_tbl)->where('bills_ref_id', $bills_ref_id)->orderBy('bills_txn_date', 'asc')->orderBy('bills_txn_id', 'asc')->get()->getResultArray();
        
    //     $bal = 0;
    //     if($result)
    //     {
    //         foreach($result as $key => $value)
    //         {
    //             if($value['bills_txn_drcr'] == 'D'){
    //                 $bal += $value['bills_txn_amt'];
    //             }
    //             if($value['bills_txn_drcr'] == 'C'){
    //                 $bal += -$value['bills_txn_amt'];
    //             }
                
    //             $data = ['bills_txn_bal' => $bal];
    //             $this->db->table($bill_txn_tbl)->where('bills_txn_id', $value['bills_txn_id'])->update($data);
    //         } 
    //     }   
    // }

function getUndefinedTrackingId($item_id,$unit_id,$txn_id)
    {
        $itmbatchmt_tbl = $this->company_id.'_itemtrackn_'.$this->session->get('ses_comp_fy_id');
        $data = $this->db->table($itmbatchmt_tbl)->select('tracking_id')->where('unit_id', $unit_id)->where('item_id', $item_id)->where('tracking_no', 'UNDEFINED')->get()->getRowArray();
        if($data)
        	return $data['tracking_id'];

        return $this->createUndefinedTrackingId($item_id,$unit_id,$txn_id);
    }

    function getUndefinedBatchId($item_id)
    {
        $itmbatchmt_tbl = $this->company_id.'_itmbatchmt_'.$this->session->get('ses_comp_fy_id');
        $data = $this->db->table($itmbatchmt_tbl)->select('batch_id')->where('item_id', $item_id)->where('batch_no', 'UNDEFINED')->get()->getRowArray();
        if($data)
        	return $data['batch_id'];

        return $this->createUndefinedBatchId($item_id);
    }
    
    function createUndefinedTrackingId($item_id,$unit_id,$txn_id)
    {
    	$batch_master_data = [
            'tracking_no' 	 => 'UNDEFINED',
            'item_id'  	     => $item_id,
            'txn_id'         => $txn_id,
            'unit_id'        => $unit_id
        ];
        return $this->add_tracking_master($batch_master_data);
    }
    

    function createUndefinedBatchId($item_id)
    {
    	$batch_master_data = [
            'batch_no' 		 => 'UNDEFINED',
            'batch_mfr'      => '',
            'batch_expiry'   => '',
            'item_id'  	     => $item_id,
        ];
        return $this->add_batch_master($batch_master_data);
    }
    
    
    function update_batch_master($id,$data)
    {
        $itmbatchmt_tbl = $this->company_id.'_itmbatchmt_'.$this->session->get('ses_comp_fy_id');
        $this->db->table($itmbatchmt_tbl)->where('batch_id', $id)->update($data);
    }
    
    
    	 public function tracking_items_trackno($tracking_no){    
			$item_id='';		 
	     $table_name = $this->company_id.'_itemtrackn_'.$this->session->get('ses_comp_fy_id');	   
         $track_data =  $this->db->table($table_name)->select(array('tracking_id','tracking_no','item_id','txn_id'))->where('tracking_no',$tracking_no)->get()->getRowArray();
	     if($track_data){
			$item_id = $track_data['item_id']; 
		   
		   
	   }
	   	
      return $item_id; 
    } 
	
	
    
    function delete_batch_txn($voucher_txn_id)
    {
    	$bill_txn_tbl = $this->company_id.'_billstxnnn_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!='')
	    $result = $this->db->table($bill_txn_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('voucher_txn_id', $voucher_txn_id)->get()->getResultArray();
    	else
		$result = $this->db->table($bill_txn_tbl)->where('voucher_txn_id', $voucher_txn_id)->get()->getResultArray();

    	// $bills_ref_ids = [];
    	// if($result){
	    // 	foreach($result as $key => $value){
	    //     	$bills_ref_ids[] = $value['bills_ref_id'];
	    // 	}
	    // }

        $this->db->table($bill_txn_tbl)->where('voucher_txn_id', $voucher_txn_id)->delete();

        // if($bills_ref_ids){
        // 	foreach($bills_ref_ids as  $bills_ref_id){
	    //     	$this->update_bill_txn_balance($bills_ref_id);
	    //     }
        // }
    }
	
	function show_units_info($unit_id,$comp_id){	 
	
	 $item_unit_master_tbl = $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($item_unit_master_tbl)->where('unit_id', $unit_id)->where('comp_id', $comp_id)->get()->getRowArray();   	   
    } 
	
	 public function undefined_balance($unit_id,$item_id){
	$table_name = $this->company_id.'_itmoppybal_'.$this->session->get('ses_comp_fy_id');	   
    $row   = $batch_data =  $this->db->table($table_name)->where('batch_id >',0)->where('item_id',$item_id)->where('item_unit',$unit_id)->get()->getRowArray();
     $all_list = array();
	if($row){		
			 if($row['item_unit']){
			   $get_units_info  = $this->show_units_info($row['item_unit'],$this->company_id);
		       $batch_unit_name       = $this->enc_string->nc_string($get_units_info['item_unit'],'de');
			   }
		     else 
			  $batch_unit_name = '';
			$all_list= array("id"=>$row['batch_id'],"label"=>"UNDEFINED","value"=>"UNDEFINED","batch_expiry"=>"","batch_mfr"=>"","item_id"=>$row['item_id'],
			                   "batch_qty"=>$row['op_bal_qty'],"batch_unit"=>$row['item_unit'],"batch_unit_name"=>$batch_unit_name);
		
		
	}
   return $all_list;
   }
   
   	 public function get_item_tracking_list($item_id){       
	   $table_name = $this->company_id.'_itemtrackn_'.$this->session->get('ses_comp_fy_id');	   
       $batch_data =  $this->db->table($table_name)->select(array('tracking_id','tracking_no','item_id','txn_id'))->where('item_id',$item_id)->get()->getResultArray();
	   $all_batches =array();
	   
	   if($batch_data){
		   foreach($batch_data as $row){
			   
			 $all_batches[]=array("id"=>$row['tracking_id'],"label"=>$row['tracking_no'],"value"=>$row['tracking_no'],
			                       "item_id"=>$row['item_id'],"txn_id"=>$row['txn_id'] );   
		   }
		   
	   }
	   	
      return $all_batches; 
    } 
	
   
   
	 public function get_item_batch_list($item_id){       
	   $table_name = $this->company_id.'_itmbatchmt_'.$this->session->get('ses_comp_fy_id');	   
       $batch_data =  $this->db->table($table_name)->select(array('batch_id','batch_no','item_id','batch_unit','batch_qty','batch_expiry','batch_mfr'))->where('item_id',$item_id)->get()->getResultArray();
	   $all_batches =array();
	   
	   if($batch_data){
		   foreach($batch_data as $row){
			   
			 $all_batches[]=array("id"=>$row['batch_id'],"label"=>$row['batch_no'],"value"=>$row['batch_no'],
			                       "item_id"=>$row['item_id'],"batch_unit"=>$row['batch_unit'],'batch_qty'=>$row['batch_qty'],'batch_expiry'=>date('d-m-Y',strtotime($row['batch_expiry'])),
								   'batch_mfr'=>date('d-m-Y',strtotime($row['batch_mfr'])) );   
		   }
		   
	   }
	   	
      return $all_batches; 
    } 
	
	
	
    function get_account_bill_refs($account_id)
    {
        $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
        $data = $this->db->table($bill_mst_tbl)->select('bills_ref_id as id, bills_ref_name as label, bills_ref_name as value, bill_due_date, DATE_FORMAT(bill_due_date, "%d-%m-%Y") as due_date, bill_op_bal')->where('acc_id', $account_id)->where('bills_ref_name !=', 'UNDEFINED')->get()->getResultArray();
        
        return $data;
    }

    function get_account_all_bill_refs($account_id)
    {
        $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
        $data = $this->db->table($bill_mst_tbl)
        				->select('bills_ref_id, bills_ref_name, bills_ref_name as label, bills_ref_name as value, DATE_FORMAT(bill_due_date, "%d-%m-%Y") as bill_due_date, bill_op_bal')
        				->where('acc_id', $account_id)
        				->orderBy('bills_ref_id')
        				->get()->getResultArray();

        foreach ($data as $key => $value) {
        	$data[$key]['bill_op_drcr'] = '';
        	$data[$key]['bill_op_bal'] = 0;

        	if($value['bill_op_bal'] < 0){
        		$data[$key]['bill_op_drcr'] = 'C';
        		$data[$key]['bill_op_bal'] = abs($value['bill_op_bal']);
        	}
        	if($value['bill_op_bal'] > 0){
        		$data[$key]['bill_op_drcr'] = 'D';
        		$data[$key]['bill_op_bal'] = $value['bill_op_bal'];
        	}

        	if($value['bill_due_date'] == '00-00-0000')
        		$data[$key]['bill_due_date'] = '';
        }
        
        return $data;
    }

    function update_account_all_bill_refs($data)
    {
        $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
        $this->db->table($bill_mst_tbl)->where('bills_ref_id', $data['bills_ref_id'])->update($data);
		
		$this->update_bill_txn_balance($data['bills_ref_id']);

    }

    public function get_bills_txn_data($voucher_txn_id)
    {
    	$fcy_rate = 1;

	   	$voucher_info = $this->get_voucher_cons_info($voucher_txn_id);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$fcy_rate = $this->get_forex_rate_fcy($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

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
		if($this->session->get('ses_boid')!='')
        $builder->where($bill_txn_tbl.'.bo_id', $this->session->get('ses_boid'));

        $builder->where('bills_ref_name !=', 'UNDEFINED');
        $builder->groupBy($acc_mst_tbl.'.acc_id');
        $builder->orderBy($acc_mst_tbl.'.acc_id', 'asc');
        $result_ = $builder->get()->getResultArray();
        
        foreach($result_ as $key_ => $value_)
        {
            $builder = $this->db->table($bill_txn_tbl);
            $builder->select($bill_txn_tbl.'.bills_txn_id, '.$bill_txn_tbl.'.bills_ref_id, '.$bill_txn_tbl.'.acc_id, bills_txn_amt, bills_txn_drcr');
            $builder->join($bill_mst_tbl, $bill_mst_tbl.'.bills_ref_id ='.$bill_txn_tbl.'.bills_ref_id');
            $builder->select($bill_mst_tbl.'.bills_ref_name');
            $builder->select('DATE_FORMAT(bill_due_date, "%d-%m-%Y") as bill_due_date');
			if($this->session->get('ses_boid')!='')
            $builder->where($bill_txn_tbl.'.bo_id', $this->session->get('ses_boid'));
            $builder->where('voucher_txn_id', $voucher_txn_id);
            $builder->where($bill_txn_tbl.'.acc_id', $value_['acc_id']);
            $builder->where('bills_ref_name !=', 'UNDEFINED');
            $result = $builder->get()->getResultArray();
			
			
			$final_bills_txn=array();
			if($result){
				foreach($result as $ccrow){
					$get_narration_info = $this->get_voucher_narration_info($voucher_txn_id,'short',$ccrow['bills_txn_id']);
					if($get_narration_info)
						$narration=$get_narration_info['vch_short_narr'];
					  else
						$narration='';  
					
					$ccrow['bills_txn_narr'] = $narration;
					$ccrow['bills_txn_amt'] = parseAmount($ccrow['bills_txn_amt'] * $fcy_rate);

					$final_bills_txn[]=$ccrow;
				}
				
			}
			
			
            
            $final[] = [
                    'acc_id' => $value_['acc_id'],
                    'acc_name' => $value_['acc_name'],
                    'bills_txn_list' => $final_bills_txn
                ];
        }
        
        
        // echo "<pre>";print_r($final);exit;
        return $final;
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


    public function remove_vouchers($voucher_type_id,$ids,$comp_id){
        
        $company_accounts = $this->comp_accounts_dropdown($comp_id);
       
       
		$ses_comp_fy_id            = $this->session->get('ses_comp_fy_id');
		$comp_vch_txn_conso_tbl    = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
  	    $comp_vch_txn_trail_tbl    = $this->company_id.'_vhtxntrail_'.$this->session->get('ses_comp_fy_id');
  	    
  	   
		$ids                       = explode(",",$ids);
        foreach($ids as $voucher_txn_id){
		    $this->db->table($comp_vch_txn_conso_tbl)->where('voucher_txn_id',$voucher_txn_id)->delete();		 
            $this->db->table($comp_vch_txn_trail_tbl)->where('voucher_txn_id',$voucher_txn_id)->where('comp_id',$comp_id)->delete();
		   
	       
	       
	       $comptxnmst_master = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		  $comp_itemtexn =  $this->db->table($comptxnmst_master)->where('voucher_txn_id',$voucher_txn_id)->where('comp_id',$comp_id)->where('master_id_type','itm')->get()->getResultArray(); 
		  if($comp_itemtexn){
		      foreach($comp_itemtexn as $cmptxn_row){
		          $item_id    =  $cmptxn_row['master_id'];
		          $itm_txn_id =  $cmptxn_row['txn_id'];
		          $item_txn_table = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		          $this->db->table($item_txn_table)->where('txn_id',$itm_txn_id)->where('comp_id',$comp_id)->delete();
		          
		      }
		      
		  } 
		 $this->db->table($comptxnmst_master)->where('voucher_txn_id',$voucher_txn_id)->where('comp_id',$comp_id)->delete();
		 
		 
		 
		 if($company_accounts){
		     
		     foreach($company_accounts as $account_info){
		         $accound_id = $account_info['value'];
		         $account_table_name = $this->company_id.'_accnttxnnn_'.$accound_id.'_'.$this->session->get('ses_comp_fy_id');
		         
				 if($this->session->get('ses_boid')!='')
				 $this->db->table($account_table_name)->where('bo_id', $this->session->get('ses_boid'))->where('voucher_txn_id',$voucher_txn_id)->where('comp_id',$comp_id)->where('acc_id',$accound_id)->delete();
		         else
					 $this->db->table($account_table_name)->where('voucher_txn_id',$voucher_txn_id)->where('comp_id',$comp_id)->where('acc_id',$accound_id)->delete();
		         
		          // after deletion update table balances 
		         $row_nums = $this->get_rownums($account_table_name);
	             if($row_nums){
	                 foreach($row_nums as $drow){
	                     $acc_txn_id = $drow['acc_txn_id'];
	                     $row_num    = $drow['sale_row_num'];
	                     $txn_id     = $drow['txn_id'];
	                     $comp_id    = $drow['comp_id'];
	                     $svoucher_txn_id  = $drow['voucher_txn_id'];
	                     
	                     // update voucher number first
	                     
	                     $voucher_number =  $this->update_voucherno_entries($comp_vch_txn_conso_tbl,$svoucher_txn_id,$comp_id);
	                     $updte_data = array("acc_txn_id"=>$row_num,"comp_vch_series_no"=>$voucher_number);
						if($this->session->get('ses_boid')!='')
	                     $this->db->table($account_table_name)->where('bo_id', $this->session->get('ses_boid'))->where('txn_id',$txn_id)->where('comp_id',$comp_id)->update($updte_data);
						 else
							 $this->db->table($account_table_name)->where('txn_id',$txn_id)->where('comp_id',$comp_id)->update($updte_data);
	                     }
	                    $account_master_table_name = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	                    $account_table = $this->db->table($account_master_table_name)->where('acc_id', $accound_id)->get()->getRowArray();
	                 
	                    $this->update_account_all_balances($account_table_name,$account_table['acc_op_bal'],$account_table['acc_op_drcr']);
	                 
	                  } 
		         
		           }
		           
		        }
		        
		 	 }
		return TRUE;
	 }

	public function delete_all_xyz()
    {
		$comp_vch_txn_conso_tbl    = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
  	    $comp_vch_txn_trail_tbl    = $this->company_id.'_vhtxntrail_'.$this->session->get('ses_comp_fy_id');
  	    
  	   
	    $this->db->table($comp_vch_txn_conso_tbl)->truncate();		 
        $this->db->table($comp_vch_txn_trail_tbl)->truncate();
	       
	       // delete items from voucher
	     $comptxnmst_master = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		 $this->db->table($comptxnmst_master)->truncate();
		 
		 $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
         $data = $this->db->table($account_master_tbl)->select('acc_id')->get()->getResultArray();
		 if($data){
		      foreach($data as $id){
    		          $acc_txn_table = $this->company_id.'_accnttxnnn_'.$id['acc_id'].'_'.$this->session->get('ses_comp_fy_id');
    		          $this->db->table($acc_txn_table)->truncate();
		      }
		 }
		 $item_master_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
         $data = $this->db->table($item_master_tbl)->select('item_id')->get()->getResultArray();
         if($data){
		      foreach($data as $id){
    		          $item_txn_table = $this->company_id.'_itemtxnnnn_'.$id['item_id'].'_'.$this->session->get('ses_comp_fy_id');
    		          $this->db->table($item_txn_table)->truncate();
		      }
		 }
		        
		 $bill_txn_tbl = $this->company_id.'_billstxnnn_'.$this->session->get('ses_comp_fy_id'); 
		 $this->db->table($bill_txn_tbl)->truncate();

		 $cc_txn_tbl = $this->company_id.'_costcttxnn_'.$this->session->get('ses_comp_fy_id'); 
		 $this->db->table($cc_txn_tbl)->truncate();

		 $sundry_master_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
		 if($this->session->get('ses_boid')!='')
	    	$data = $this->db->table($sundry_master_tbl)->select('bill_sundry_id')->where('bo_id', $this->session->get('ses_boid'))->get()->getResultArray();
         else
		  $data = $this->db->table($sundry_master_tbl)->select('bill_sundry_id')->get()->getResultArray();
		 
         if($data){
		      foreach($data as $id){
    		          $sundry_txn_table = $this->company_id.'_sundrytxnn_'.$id['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
    		          $this->db->table($sundry_txn_table)->truncate();
		      }
		 }

		        
		 	 
		return TRUE;
    }

    function update_item_balances($item_id)
    {
    	$mat_centre_master_tbl  = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
	    $result = $this->db->table($mat_centre_master_tbl)->get()->getResultArray();

	    foreach ($result as $key => $value) {

	    	$item_unit_master_tbl = $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');
	    	$result2 = $this->db->table($item_unit_master_tbl)->get()->getResultArray();

	    	foreach ($result2 as $key2 => $value2) {
	    		$this->update_item_unit_balance($item_id, $value2['unit_id'], $value['mat_cent_id'], 0, 1);
	    	}
	    }
    }

    function update_item_unit_balance($item_id, $item_unit, $mat_cent_id, $batch_id, $item_avail)
    {
        $bal=0; //include opening balance

        $itmoppybal_tbl = $this->company_id.'_itmoppybal_'.$this->session->get('ses_comp_fy_id');
		$builder = $this->db->table($itmoppybal_tbl); 
		$builder->where('item_id', $item_id);
		$builder->where('item_unit', $item_unit);
		$builder->where('mat_cent_id', $mat_cent_id);
		if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
		$builder->where('batch_id', $batch_id);	
    	$itmoppybal = $builder->get()->getRowArray();
    	if($itmoppybal){
    		$balance = floatval($itmoppybal['op_bal_qty']);
    	}

        $item_txn_tbl = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		$this->db->query('SET @bal = '.$bal.';');
		
		if($this->session->get('ses_boid')!=''){
			$bo_id = $this->session->get('ses_boid');
			$this->db->query('UPDATE '.$item_txn_tbl.' SET item_bal_qty = CASE WHEN item_txn_drcr = "c" THEN @bal:=@bal - item_txn_qty WHEN item_txn_drcr = "d" THEN @bal:=@bal + item_txn_qty ELSE 0 END where item_id = "'.$item_id.'" AND item_unit = "'.$item_unit.'" AND mat_cent_id = "'.$mat_cent_id.'" AND batch_id = "'.$batch_id.'" AND item_avail = "'.$item_avail.'" AND bo_id = "'.$bo_id.'" order by item_txn_date,voucher_txn_id,item_txn_id;');
		}else {
			$this->db->query('UPDATE '.$item_txn_tbl.' SET item_bal_qty = CASE WHEN item_txn_drcr = "c" THEN @bal:=@bal - item_txn_qty WHEN item_txn_drcr = "d" THEN @bal:=@bal + item_txn_qty ELSE 0 END where item_id = "'.$item_id.'" AND item_unit = "'.$item_unit.'" AND mat_cent_id = "'.$mat_cent_id.'" AND batch_id = "'.$batch_id.'" AND item_avail = "'.$item_avail.'" order by item_txn_date,voucher_txn_id,item_txn_id;');
		}

		$this->calculate_valuation($item_id, $item_unit, $mat_cent_id, $batch_id);
		
    }

    public function update_item_unit_mc_balance($item_id,$unit_id,$mc_id)
	{
		$balance = 0;

		$itmoppybal_tbl = $this->company_id.'_itmoppybal_'.$this->session->get('ses_comp_fy_id');
		$builder = $this->db->table($itmoppybal_tbl); 
		$builder->where('item_id', $item_id);
		$builder->where('item_unit', $unit_id);
		$builder->where('mat_cent_id', $mc_id);
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));

		$builder->where('batch_id', 0);	
    	$itmoppybal = $builder->get()->getRowArray();
    	if($itmoppybal){
    		$balance = parseAmount($itmoppybal['op_bal_qty']);
    	}

    	$itemtxnbal_tbl = $this->company_id.'_itemtxnbal_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($itemtxnbal_tbl); 
		$builder->where('item_id', $item_id);
		$builder->where('item_unit', $unit_id);
		$builder->where('mat_cent_id', $mc_id);
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));

		$builder->where('item_avail', '1');
		$builder->orderBy('item_txn_date', 'asc');	
		$builder->orderBy('itemtxnbal_id', 'asc');		
    	$result = $builder->get()->getResultArray();

    	foreach ($result as $key => $value) {
    		$item_txn_qty = 0;
    		$item_txn_drcr = 'd';
            $item_txn_date = $value['item_txn_date']; 
    		$itemtxnnnn_tbl = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
    		$builder = $this->db->table($itemtxnnnn_tbl); 
			$builder->where('item_txn_id', $value['item_txn_id']);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
			
        	$itemtxnnnn = $builder->get()->getRowArray();
        	if($itemtxnnnn){
        		$item_txn_qty = $itemtxnnnn['item_txn_qty'];
    			$item_txn_drcr = $itemtxnnnn['item_txn_drcr'];
        	}

        	if($item_txn_drcr == 'd')
        		$balance += parseAmount($item_txn_qty);
        	if($item_txn_drcr == 'c')
        		$balance -= parseAmount($item_txn_qty);

			if($this->session->get('ses_boid')!='')
				$this->db->table($itemtxnbal_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('itemtxnbal_id', $value['itemtxnbal_id'])->update(['item_bal_qty' => $balance]);
			else 
				$this->db->table($itemtxnbal_tbl)->where('itemtxnbal_id', $value['itemtxnbal_id'])->update(['item_bal_qty' => $balance]);
			
			$this->CalculateValuation($item_id,$unit_id,$mc_id,0,$value['itemtxnbal_id'],$item_txn_drcr,$item_txn_date);
            $this->CalculateValuation($item_id,$unit_id,$mc_id,1,$value['itemtxnbal_id'],$item_txn_drcr,$item_txn_date);			

    	}
	}

	public function update_item_balance($item_id)
	{
		$mat_centre_master_tbl  = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
	    $result = $this->db->table($mat_centre_master_tbl)->get()->getResultArray();

	    foreach ($result as $key => $value) {

	    	$itemtxnbal_tbl = $this->company_id.'_itemtxnbal_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
	    	$builder = $this->db->table($itemtxnbal_tbl);
	    	$builder->select('item_unit');
			$builder->where('item_id', $item_id);
			$builder->where('mat_cent_id', $value['mat_cent_id']);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));

			$builder->where('item_avail', '1');
			$builder->groupBy('item_unit');
			$builder->orderBy('item_unit', 'asc');		
	    	$result2 = $builder->get()->getResultArray();

	    	foreach ($result2 as $key2 => $value2) {
	    		$this->update_item_unit_mc_balance($item_id,$value2['item_unit'],$value['mat_cent_id']);
	    	}
	    }
	}
	
	/***************  Consignment Packing Start  *******************/	
	function GetPackingListNumber(){
		$pcklistmst_tbl  = $this->company_id.'_pcklistmst_'.$this->session->get('ses_comp_fy_id');
	    $builder = $this->db->table($pcklistmst_tbl);
		$builder->select('MAX(list_id) as max_packing_id');
		$result  = $builder->get()->getRowArray();			
        if($result['max_packing_id'] >0)		
		return $result['max_packing_id']+1;
	   else return "1";
			
		
	}
	
	function GetListAllCu($list_id){
	$cupackingn_tbl  = $this->company_id.'_cupackingn_'.$this->session->get('ses_comp_fy_id');  	
	 return $this->db->table($cupackingn_tbl)->where('list_id', $list_id)->get()->getResultArray();  	
	}
	
	function finalize_cu($packing_list_id,$cu_id,$pack_level){
	  $cupackingn_tbl  = $this->company_id.'_cupackingn_'.$this->session->get('ses_comp_fy_id');  
	  $this->db->table($cupackingn_tbl)->where('cu_id', $cu_id)->where('list_id',$packing_list_id)->update(array("cu_status"=>"0","pck_status"=>"1"));    
	   return "1";
	    
	}
	
	function remove_pcklistqty($item_id,$packing_list_id,$cu_id,$unit_id){
	  $table_name  = $this->company_id.'_listpacked_'.$this->session->get('ses_comp_fy_id');  
	  $this->db->table($table_name)->where('list_id', $packing_list_id)->where('item_id',$item_id)->where('item_unit',$unit_id)->where('cu_id',$cu_id)->delete();   
	
	
	
	$pcklistqty_tbl  = $this->company_id.'_pcklistqty_'.$this->session->get('ses_comp_fy_id'); 
    $this->db->table($pcklistqty_tbl)->where('list_id', $packing_list_id)->where('item_id',$item_id)->where('item_unit',$unit_id)->delete();   
	
	
	
	}
	
	function cu_info($cu_id){	 
      $pcklistmst_tbl = $this->company_id.'_listcumast_'.$this->session->get('ses_comp_fy_id'); 
  	  return $this->db->table($pcklistmst_tbl)->where('cu_id', $cu_id)->get()->getRowArray();   	   
   }
   
   function revised_pcklistqty($table_name,$item_id,$packing_list_id,$cu_id,$unit_id,$update_data){
     $this->db->table($table_name)->where('list_id', $packing_list_id)->where('item_id',$item_id)->where('item_unit',$unit_id)->where('cu_id',$cu_id)->update($update_data);
    
     $pcklistqty_tbl  = $this->company_id.'_pcklistqty_'.$this->session->get('ses_comp_fy_id');	
	 
	 $builder    = $this->db->table($pcklistqty_tbl);
	 $builder->select('item_qty_initial');
	 $builder->where('list_id',$packing_list_id);
	 $builder->where('item_id',$item_id);
	 $builder->where('item_unit',$unit_id);
	 $result = $builder->get()->getRowArray();
	 
     $item_qty_initial = $result['item_qty_initial'];
	 
	 $item_qty_available = $item_qty_initial-$update_data['item_qty_packed'];
	 
   
    $update_data = array("item_qty_available" =>$item_qty_available );
   
   $this->update_pcklistqty($pcklistqty_tbl,$packing_list_id,$item_id,$unit_id,$update_data);

   
   }
   
   
	function packing_list_info($list_id){	 
      $pcklistmst_tbl = $this->company_id.'_pcklistmst_'.$this->session->get('ses_comp_fy_id'); 
  	  return $this->db->table($pcklistmst_tbl)->where('list_id', $list_id)->get()->getRowArray();   	   
   }
   
   function get_cu_items_packed($cu_id,$level_id,$list_id){
	   
	   
	   
	   
       $listpacked_tbl = $this->company_id.'_listpacked_'.$this->session->get('ses_comp_fy_id'); 
  	   $result= $this->db->table($listpacked_tbl)->where('list_id', $list_id)->where('cu_id', $cu_id)->where('list_level_id', $level_id)->get()->getResultArray(); 
  	   $items_array=array();
  	   $i=0;
  	   if($result){
  	       foreach($result as $row){
  	          $item_id  = $row['item_id'];
  	          $unit_id  = $row['item_unit'];
  	          $qty_packed = $row['item_qty_packed'];
  	          
			  $item_availability = $this->pcklistqty_availability($list_id,$item_id,$unit_id);
			  
			  
			  
  	          $item_info      = $this->get_item_info($item_id);
  	          $item_unit_info = $this->item_unit_info($unit_id);
              $item_unit_name = $this->enc_string->nc_string($item_unit_info['item_unit'],'de');
  	          
  	          $item_name      = $item_info['item_name'];
  	          $items_array[]=array('qty_available'=>$item_availability,"cu_id" => $cu_id,"item_unit_id"=>$unit_id,"batch_no"=>"","specification_no"=>"","item_id"=>$item_id,"id"=>$item_id,"pq_cellattr"=>array("item_name"=>array("title"=>"")),"name"=>$item_name,"item_name"=>$item_name,'item_qty'=>$qty_packed,'item_unit'=>$item_unit_name,"to_carrying_unit_id"=>"","to_carrying_unit"=>"",'to_qty_packed'=>'','to_cu_save'=>'<a href="javascript:void(0);" onClick="update_revised_qty_row(\''.$i.'\',\''.$cu_id.'\');"><img src="'.base_url().'/public/assets/img/icon-done.png"></a>&nbsp;<a href="javascript:void(0);" onClick="unpack_item(\''.$i.'\',\''.$cu_id.'\');"><img src="'.base_url().'/public/assets/img/icon-close.png"></a>');
  	         $i++;
  	           
  	       }
  	       
  	   }
      return $items_array; 
       
   }
   
function cupackingn_info($packing_id,$list_id){
	$subarray=array();
	$cupackingn_tbl = $this->company_id.'_cupackingn_'.$this->session->get('ses_comp_fy_id'); 
  	$result         = $this->db->table($cupackingn_tbl)
	                    ->where('list_id', $list_id)
						->where('packing_id', $packing_id)
	                   ->get()->getRowArray(); 
					  if($result){ 
 $cu_id   = $result['cu_id'];
               $unit_id = $result['cu_unit'];
			    $level_no = $result['list_level_id'];
              
              $cu_info      =  $this->cu_info($result['cu_id']); 
	          $cu_unit_info =  $this->item_unit_info($result['cu_unit']);
              $cu_unit_name =  $this->enc_string->nc_string($cu_unit_info['item_unit'],'de');
              $cu_name      =  $cu_info['cu_label'];
			  
			 // $subarray= array("name" => $cu_unit_name.' '.$cu_name.'(L'.$level_no.')<span style="float:right;"><a href="javascript:void(0);" onClick="finalize_cu_status(\''.$packing_id.'\',\''.$result['cu_id'].'\')" data-id="'.$packing_id.'" data-ajax="'.$result['cu_id'].'"  class="btn btn-sm btn-outline-success"> Finalize</a></span>');
	$subarray= array("name" => $cu_unit_name.' '.$cu_name.'(L'.$level_no.')');
	
					  }
					 else
					$subarray= array("name" =>'NA');	  
	return $subarray; 
	 
 }
 
 function level1($packing_id,$packing_list_id){
	
	   $cupackingn_tbl = $this->company_id.'_cupackingn_'.$this->session->get('ses_comp_fy_id'); 
       $leveltrack_tbl = $this->company_id.'_leveltrack_'.$this->session->get('ses_comp_fy_id'); 
	    if($packing_id){				  
				    $result1         = $this->db->table($leveltrack_tbl)
										->where($leveltrack_tbl.'.packing_id', $packing_id)
										->where($leveltrack_tbl.'.list_id', $packing_list_id)
										->get()->getRowArray();				  
				     if($result1){
						    
						  $l1_prev_packing_id = $result1['prev_packing_id']; 
						 if($l1_prev_packing_id==0){
							$packing_id = $result1['packing_id']; 
								// if no child left then ftech items 
								$result1         = $this->db->table($cupackingn_tbl)
													->where('list_id', $packing_list_id)
													->where('packing_id', $packing_id)
												   ->get()->getResultArray();
								  $sub1=array();												   
								 if($result1){
									 foreach($result1 as $r1){
										  $cu_id   = $r1['cu_id'];
										   $list_level_id = $r1['list_level_id'];
										   $list_id        = $r1['list_id'];
										  
										    $items_arary= $this->get_cu_items_packed($cu_id,$list_level_id,$list_id);
											$sub_array1       = $items_arary; 
										    
									 } 
						     }
						 }
						 
						 else{
						  $sub1               = $this->cupackingn_info($l1_prev_packing_id,$packing_list_id);
						  $sub_array1         = array("name" => $sub1['name']);
						
							
						 $result2   = $this->db->table($leveltrack_tbl)
										->where($leveltrack_tbl.'.packing_id', $l1_prev_packing_id)
										->where($leveltrack_tbl.'.list_id', $packing_list_id)
										->get()->getRowArray();			

						  if($result2){
							 $l2_prev_packing_id = $result2['prev_packing_id']; 
							if($l2_prev_packing_id!=0){
						    $sub2               = $this->cupackingn_info($l2_prev_packing_id,$packing_list_id);
							$sub_array1['data'] = $sub2;
							
							}else{
								$packing_id = $result2['packing_id']; 								
								$result2         = $this->db->table($cupackingn_tbl)
													->where('list_id', $packing_list_id)
													->where('packing_id', $packing_id)
												   ->get()->getResultArray(); 
								 if($result2){
									 foreach($result2 as $r2){
										   $cu_id         = $r2['cu_id'];
										   $list_level_id = $r2['list_level_id'];		
											$list_id        = $r2['list_id'];	
										   $items_arary   = $this->get_cu_items_packed($cu_id,$list_level_id,$list_id);										 
										    $sub2[]       = $items_arary; 
									 }
									 
								 }else 
									 $items_arary=array();
								
								
															
						    	$sub_array1['data'] = $sub2;
							  }
								
							   
						  } 
					 }			
				   
			       }
			    } 
	return $sub_array1; 
   }
   

 function level2($packing_id,$packing_list_id){
	   $cupackingn_tbl = $this->company_id.'_cupackingn_'.$this->session->get('ses_comp_fy_id'); 
       $leveltrack_tbl = $this->company_id.'_leveltrack_'.$this->session->get('ses_comp_fy_id'); 
	   if($packing_id){				  
				    $result1         = $this->db->table($leveltrack_tbl)
										->where($leveltrack_tbl.'.packing_id', $packing_id)
										->where($leveltrack_tbl.'.list_id', $packing_list_id)
										->get()->getRowArray();				  
				     if($result1){
						  $l1_prev_packing_id = $result1['prev_packing_id']; 
						 if($l1_prev_packing_id==0){
							$packing_id = $result1['packing_id']; 
								// if no child left then ftech items 
								$result1         = $this->db->table($cupackingn_tbl)
													->where('list_id', $packing_list_id)
													->where('packing_id', $packing_id)
												   ->get()->getResultArray();
								  $sub1=array();												   
								 if($result1){
									 foreach($result1 as $r1){
										  $cu_id   = $r1['cu_id'];
										   $list_level_id = $r1['list_level_id'];
										  $list_id        = $r1['list_id'];
										    $items_arary= $this->get_cu_items_packed($cu_id,$list_level_id,$list_id);
										    $sub_array1       = $items_arary; 
										    
									 } 
						     }
						 }
						 else{
						  
						  $sub1               = $this->cupackingn_info($l1_prev_packing_id,$packing_list_id);
						  $sub_array1         = array("name" => $sub1['name']);
						
							
						  $result2   = $this->db->table($leveltrack_tbl)
										->where($leveltrack_tbl.'.packing_id', $l1_prev_packing_id)
										->where($leveltrack_tbl.'.list_id', $packing_list_id)
										->get()->getRowArray();			

						  if($result2){
							 $l2_prev_packing_id = $result2['prev_packing_id']; 
							if($l2_prev_packing_id!=0){
						       $sub_array1['data']  = $this->cupackingn_info($l2_prev_packing_id,$packing_list_id);
							 
							
							}else{
								$packing_id = $result2['packing_id']; 								
								$result2    = $this->db->table($cupackingn_tbl)
										    	->where('list_id', $packing_list_id)
												->where('packing_id', $packing_id)
											    ->get()->getResultArray(); 
								 if($result2){
									 foreach($result2 as $r2){
										    $cu_id         = $r2['cu_id'];
										    $list_level_id = $r2['list_level_id'];		
											$list_id        = $r2['list_id'];	
										    $items_arary   = $this->get_cu_items_packed($cu_id,$list_level_id,$list_id);										 
										    $sub2  = $items_arary; 
									      }									 
								 }						
						    	$sub_array1['data'] = $sub2;
					        }
								
					      }
						   
					  }			
				   
			       }
			    } 				
	
	return $sub_array1; 
 }

 function level3($packing_id,$packing_list_id){
	 $cupackingn_tbl = $this->company_id.'_cupackingn_'.$this->session->get('ses_comp_fy_id'); 
       $leveltrack_tbl = $this->company_id.'_leveltrack_'.$this->session->get('ses_comp_fy_id');
					if($packing_id){				  
				    $result1         = $this->db->table($leveltrack_tbl)
										->where($leveltrack_tbl.'.packing_id', $packing_id)
										->where($leveltrack_tbl.'.list_id', $packing_list_id)
										->get()->getRowArray();	
					if($result1){
						  $l1_prev_packing_id = $result1['prev_packing_id']; 
						 if($l1_prev_packing_id==0){
							$packing_id = $result1['packing_id']; 								
								$result1         = $this->db->table($cupackingn_tbl)
													->where('list_id', $packing_list_id)
													->where('packing_id', $packing_id)
												   ->get()->getResultArray();
																				   
								 if($result1){
									 foreach($result1 as $r1){
										  $cu_id   = $r1['cu_id'];
										   $list_level_id = $r1['list_level_id'];
										  $list_id        = $r1['list_id'];	
										    $items_arary= $this->get_cu_items_packed($cu_id,$list_level_id, $list_id);
										    $sub_array1       = $items_arary; 
										    
									 } 
						     }
						 }
						 else{
						  
						  $sub1               = $this->cupackingn_info($l1_prev_packing_id,$packing_list_id);
						  $sub_array1         = array("name" => $sub1['name']);
						
						  $result2   = $this->db->table($leveltrack_tbl)
										->where($leveltrack_tbl.'.packing_id', $l1_prev_packing_id)
										->where($leveltrack_tbl.'.list_id', $packing_list_id)
										->get()->getRowArray();			

						  if($result2){
							 $l2_prev_packing_id = $result2['prev_packing_id']; 
							if($l2_prev_packing_id!=0){
						       $sub2                  = $this->cupackingn_info($l2_prev_packing_id,$packing_list_id);
							   $sub_array1['data']    = $sub2;
							
							
							   $result3   = $this->db->table($leveltrack_tbl)
										->where($leveltrack_tbl.'.packing_id', $l2_prev_packing_id)
										->where($leveltrack_tbl.'.list_id', $packing_list_id)
										->get()->getRowArray();		
							
						 if($result3){
							 $l3_prev_packing_id = $result3['prev_packing_id']; 
							if($l3_prev_packing_id!=0){
						    $sub3               = $this->cupackingn_info($l3_prev_packing_id,$packing_list_id);
							$sub2['data']       = $sub3;							
							$sub_array1['data'] = $sub2;
							
							}else{
								$packing_id = $result3['packing_id']; 
								
								$result3         = $this->db->table($cupackingn_tbl)
													->where('list_id', $packing_list_id)
													->where('packing_id', $packing_id)
												   ->get()->getResultArray(); 
								 if($result3){
									 foreach($result3 as $r3){
										    $cu_id          = $r3['cu_id'];
										    $list_level_id  = $r3['list_level_id'];	
											$list_id = $r3['list_id'];	
										    $items_arary    = $this->get_cu_items_packed($cu_id,$list_level_id,$list_id);										 
										     $sub3          = $items_arary; 
										     $sub2['data'] = $sub3;
									      }
									 
								    }
								
															
						    	$sub_array1['data'] = $sub2;
								
					
							  }
								
							   
							   
						     } 
							 
							
							}else{
								$packing_id = $result2['packing_id']; 								
								$result2    = $this->db->table($cupackingn_tbl)
										    	->where('list_id', $packing_list_id)
												->where('packing_id', $packing_id)
											    ->get()->getResultArray(); 
								 if($result2){
									 foreach($result2 as $r2){
										    $cu_id         = $r2['cu_id'];
										    $list_level_id = $r2['list_level_id'];	
											$list_id = $r2['list_id'];	
										    $items_arary   = $this->get_cu_items_packed($cu_id,$list_level_id,$list_id);										 
										    $sub2  = $items_arary; 
											$sub_array1['data'] = $sub2;
									      }									 
								 }						
						    	
					        }
								
					      }
						   
					  }			
				   
			       }

				   
					} 
					
					
  return $sub_array1;					
 }
 
 function level4($packing_id,$packing_list_id){
	 $cupackingn_tbl = $this->company_id.'_cupackingn_'.$this->session->get('ses_comp_fy_id'); 
       $leveltrack_tbl = $this->company_id.'_leveltrack_'.$this->session->get('ses_comp_fy_id');
	if($packing_id){				  
				    $result1         = $this->db->table($leveltrack_tbl)
										->where($leveltrack_tbl.'.packing_id', $packing_id)
										->where($leveltrack_tbl.'.list_id', $packing_list_id)
										->get()->getRowArray();
										
			if($result1){
						  $l1_prev_packing_id = $result1['prev_packing_id']; 
						 if($l1_prev_packing_id==0){
							$packing_id = $result1['packing_id']; 								
								$result1         = $this->db->table($cupackingn_tbl)
													->where('list_id', $packing_list_id)
													->where('packing_id', $packing_id)
												   ->get()->getResultArray();
																				   
								 if($result1){
									 foreach($result1 as $r1){
										  $cu_id   = $r1['cu_id'];
										   $list_level_id = $r1['list_level_id'];
										  $list_id = $r1['list_id'];
										    $items_arary= $this->get_cu_items_packed($cu_id,$list_level_id,$list_id);
										    $sub_array1       = $items_arary; 
										    
									 } 
						     }
						 }
						 else{
						  
						  $sub1               = $this->cupackingn_info($l1_prev_packing_id,$packing_list_id);
						  $sub_array1         = array("name" => $sub1['name']);
						
						  $result2   = $this->db->table($leveltrack_tbl)
										->where($leveltrack_tbl.'.packing_id', $l1_prev_packing_id)
										->where($leveltrack_tbl.'.list_id', $packing_list_id)
										->get()->getRowArray();			

						  if($result2){
							 $l2_prev_packing_id = $result2['prev_packing_id']; 
							if($l2_prev_packing_id!=0){
						       $sub2                  = $this->cupackingn_info($l2_prev_packing_id,$packing_list_id);
							   $sub_array1['data']    = $sub2;
							
							
							   $result3   = $this->db->table($leveltrack_tbl)
										->where($leveltrack_tbl.'.packing_id', $l2_prev_packing_id)
										->where($leveltrack_tbl.'.list_id', $packing_list_id)
										->get()->getRowArray();		
							
						 if($result3){
							 $l3_prev_packing_id = $result3['prev_packing_id']; 
							if($l3_prev_packing_id!=0){
						    $sub3               = $this->cupackingn_info($l3_prev_packing_id,$packing_list_id);
							$sub2['data']       = $sub3;							
							$sub_array1['data'] = $sub2;
							
							
							$result4   = $this->db->table($leveltrack_tbl)
										->where($leveltrack_tbl.'.packing_id', $l3_prev_packing_id)
										->where($leveltrack_tbl.'.list_id', $packing_list_id)
										->get()->getRowArray();		
							
						   if($result4){
							    //echo 'case4'; 
							 $l4_prev_packing_id = $result4['prev_packing_id']; 
							if($l4_prev_packing_id!=0){
						    $sub4               = $this->cupackingn_info($l4_prev_packing_id,$packing_list_id);
							$sub3['data']       = $sub4;	
							$sub2['data'] 		= $sub3;
							$sub_array1['data'] = $sub2;
							
							}else{
								$packing_id = $result4['packing_id']; 
								// if no child left then ftech items 
								$result4         = $this->db->table($cupackingn_tbl)
													->where('list_id', $packing_list_id)
													->where('packing_id', $packing_id)
												   ->get()->getResultArray(); 
								 if($result4){
									 foreach($result4 as $r4){
										  $cu_id   = $r4['cu_id'];
										   $list_level_id = $r4['list_level_id'];
										   $list_id = $r4['list_id'];
										    $items_arary= $this->get_cu_items_packed($cu_id,$list_level_id,$list_id);
										 
										$sub3['data']        = $items_arary; 
									 }
									 
								 }
								
															
						    	
								$sub2['data'] 		= $sub3;
							   $sub_array1['data'] = $sub2;
							  }
								
							   
							   
						     }
							
							}else{
								$packing_id = $result3['packing_id']; 
								
								$result3         = $this->db->table($cupackingn_tbl)
													->where('list_id', $packing_list_id)
													->where('packing_id', $packing_id)
												   ->get()->getResultArray(); 
								 if($result3){
									 foreach($result3 as $r3){
										    $cu_id          = $r3['cu_id'];
										    $list_level_id  = $r3['list_level_id'];		
											$list_id = $r3['list_id'];		
										    $items_arary    = $this->get_cu_items_packed($cu_id,$list_level_id,$list_id);										 
										     $sub3          = $items_arary; 
										     $sub2['data'] = $sub3;
									      }
									 
								    }
								
															
						    	$sub_array1['data'] = $sub2;
								
					
							  }
								
							   
							   
						     } 
							 
							
							}else{
								$packing_id = $result2['packing_id']; 								
								$result2    = $this->db->table($cupackingn_tbl)
										    	->where('list_id', $packing_list_id)
												->where('packing_id', $packing_id)
											    ->get()->getResultArray(); 
								 if($result2){
									 foreach($result2 as $r2){
										    $cu_id         = $r2['cu_id'];
										    $list_level_id = $r2['list_level_id'];		
											$list_id = $r2['list_id'];		
										    $items_arary   = $this->get_cu_items_packed($cu_id,$list_level_id,$list_id);										 
										    $sub2  = $items_arary; 
											$sub_array1['data'] = $sub2;
									      }									 
								 }						
						    	
					        }
								
					      }
						   
					  }			
				   
			       }							
										
					
					}   
	 return $sub_array1;  
 }
 
 
 function level5($packing_id,$packing_list_id){
	 $cupackingn_tbl = $this->company_id.'_cupackingn_'.$this->session->get('ses_comp_fy_id'); 
       $leveltrack_tbl = $this->company_id.'_leveltrack_'.$this->session->get('ses_comp_fy_id');
	 if($packing_id){				  
				    $result1         = $this->db->table($leveltrack_tbl)
										->where($leveltrack_tbl.'.packing_id', $packing_id)
										->where($leveltrack_tbl.'.list_id', $packing_list_id)
										->get()->getRowArray();				  
				    if($result1){
						  $l1_prev_packing_id = $result1['prev_packing_id']; 
						 if($l1_prev_packing_id==0){
							$packing_id = $result1['packing_id']; 								
								$result1         = $this->db->table($cupackingn_tbl)
													->where('list_id', $packing_list_id)
													->where('packing_id', $packing_id)
												   ->get()->getResultArray();
																				   
								 if($result1){
									 foreach($result1 as $r1){
										  $cu_id   = $r1['cu_id'];
										   $list_level_id = $r1['list_level_id'];
										  $list_id = $r1['list_id'];
										    $items_arary= $this->get_cu_items_packed($cu_id,$list_level_id,$list_id);
										    $sub_array1       = $items_arary; 
										    
									 } 
						     }
						 }
						 else{
						  
						  $sub1               = $this->cupackingn_info($l1_prev_packing_id,$packing_list_id);
						  $sub_array1         = array("name" => $sub1['name']);
						
						  $result2   = $this->db->table($leveltrack_tbl)
										->where($leveltrack_tbl.'.packing_id', $l1_prev_packing_id)
										->where($leveltrack_tbl.'.list_id', $packing_list_id)
										->get()->getRowArray();			

						  if($result2){
							 $l2_prev_packing_id = $result2['prev_packing_id']; 
							if($l2_prev_packing_id!=0){
						       $sub2                  = $this->cupackingn_info($l2_prev_packing_id,$packing_list_id);
							   $sub_array1['data']    = $sub2;
							
							
							   $result3   = $this->db->table($leveltrack_tbl)
										->where($leveltrack_tbl.'.packing_id', $l2_prev_packing_id)
										->where($leveltrack_tbl.'.list_id', $packing_list_id)
										->get()->getRowArray();		
							
						 if($result3){
							 $l3_prev_packing_id = $result3['prev_packing_id']; 
							if($l3_prev_packing_id!=0){
						    $sub3               = $this->cupackingn_info($l3_prev_packing_id,$packing_list_id);
							$sub2['data']       = $sub3;							
							$sub_array1['data'] = $sub2;
							
							
							$result4   = $this->db->table($leveltrack_tbl)
										->where($leveltrack_tbl.'.packing_id', $l3_prev_packing_id)
										->where($leveltrack_tbl.'.list_id', $packing_list_id)
										->get()->getRowArray();		
							
						   if($result4){
							    //echo 'case4'; 
							 $l4_prev_packing_id = $result4['prev_packing_id']; 
							if($l4_prev_packing_id!=0){
						    $sub4               = $this->cupackingn_info($l4_prev_packing_id,$packing_list_id);
							$sub3['data']       = $sub4;	
							$sub2['data'] 		= $sub3;
							$sub_array1['data'] = $sub2;
							
							
								$result5   = $this->db->table($leveltrack_tbl)
										->where($leveltrack_tbl.'.packing_id', $l4_prev_packing_id)
										->where($leveltrack_tbl.'.list_id', $packing_list_id)
										->get()->getRowArray();	
										
							 if($result5){
							       $l5_prev_packing_id = $result5['prev_packing_id']; 
        							if($l5_prev_packing_id!=0){
        						    $sub5               = $this->cupackingn_info($l5_prev_packing_id,$packing_list_id);
        						    $sub4['data']       = $sub5;
        							$sub3['data']       = $sub4;	
        							$sub2['data'] 		= $sub3;
        							$sub_array1['data'] = $sub2;
        							
        							}
							     else{
								$packing_id = $result5['packing_id']; 
								// if no child left then ftech items 
								$result5         = $this->db->table($cupackingn_tbl)
													->where('list_id', $packing_list_id)
													->where('packing_id', $packing_id)
												   ->get()->getResultArray(); 
								 if($result5){
									 foreach($result5 as $r5){
										  $cu_id   = $r5['cu_id'];
										   $list_level_id = $r5['list_level_id'];
										   $list_id = $r5['list_id'];
										    $items_arary= $this->get_cu_items_packed($cu_id,$list_level_id,$list_id);
										 
										$sub5['data']        = $items_arary; 
									 }
									 
								 }
								
								$sub4['data'] 		= $sub5;							
						    	$sub3['data'] 		= $sub4;
								$sub2['data'] 		= $sub3;
							   $sub_array1['data'] = $sub2;
							  }
							     
							     
							 }
							
							
							}else{
								$packing_id = $result4['packing_id']; 
								// if no child left then ftech items 
								$result4         = $this->db->table($cupackingn_tbl)
													->where('list_id', $packing_list_id)
													->where('packing_id', $packing_id)
												   ->get()->getResultArray(); 
								 if($result4){
									 foreach($result4 as $r4){
										  $cu_id   = $r4['cu_id'];
										   $list_level_id = $r4['list_level_id'];
										   $list_id = $r4['list_id'];
										    $items_arary= $this->get_cu_items_packed($cu_id,$list_level_id,$list_id);
										 
										$sub4['data']        = $items_arary; 
									 }
									 
								 }
								
															
						    	$sub3['data'] 		= $sub4;
								$sub2['data'] 		= $sub3;
							   $sub_array1['data'] = $sub2;
							  }
								
							   
							   
						     }
							
							}else{
								$packing_id = $result3['packing_id']; 
								
								$result3         = $this->db->table($cupackingn_tbl)
													->where('list_id', $packing_list_id)
													->where('packing_id', $packing_id)
												   ->get()->getResultArray(); 
								 if($result3){
									 foreach($result3 as $r3){
										    $cu_id          = $r3['cu_id'];
										    $list_level_id  = $r3['list_level_id'];		
											$list_id = $r3['list_id'];		
										    $items_arary    = $this->get_cu_items_packed($cu_id,$list_level_id,$list_id);										 
										     $sub3          = $items_arary; 
										     $sub2['data'] = $sub3;
									      }
									 
								    }
								
															
						    	$sub_array1['data'] = $sub2;
								
					
							  }
								
							   
							   
						     } 
							 
							
							}else{
								$packing_id = $result2['packing_id']; 								
								$result2    = $this->db->table($cupackingn_tbl)
										    	->where('list_id', $packing_list_id)
												->where('packing_id', $packing_id)
											    ->get()->getResultArray(); 
								 if($result2){
									 foreach($result2 as $r2){
										    $cu_id         = $r2['cu_id'];
										    $list_level_id = $r2['list_level_id'];		
											$list_id = $r2['list_id'];		
										    $items_arary   = $this->get_cu_items_packed($cu_id,$list_level_id,$list_id);										 
										    $sub2  = $items_arary; 
											$sub_array1['data'] = $sub2;
									      }									 
								 }						
						    	
					        }
								
					      }
						   
					  }			
				   
			       }
			 }
	return $sub_array1;			  
 }
 
   function nested_packed_items_lists($packing_list_id,$level_id){
	   //RUN LOOP FROM LEVELTRACK TABLE -> CUPAKCINGN -> CUID TO OBTAIN NESTED LOOP OF ALL CU AVAILABLE WITHIN MASTER CU + ALSO CHECK FROM MASTER CU AT ALL LEVELS FROM CU_ID FIELD IN LISTPACKED TO OBTAIN PACKING STATUS TILL CURRENT LEVEL 
     
	   $items1_array =array();
	   $cupackingn_tbl = $this->company_id.'_cupackingn_'.$this->session->get('ses_comp_fy_id'); 
       $leveltrack_tbl = $this->company_id.'_leveltrack_'.$this->session->get('ses_comp_fy_id'); 
  	   $result         = $this->db->table($cupackingn_tbl)
						->where($cupackingn_tbl.'.list_id', $packing_list_id)
						->where($cupackingn_tbl.'.list_level_id <=', $level_id)
						->where($cupackingn_tbl.'.cu_status', 1)
	                    ->orderBy($cupackingn_tbl.'.cu_id')
						->get()->getResultArray(); 							
	  $packing_ids=$prev_packing_ids=array();
      $master_array = array();
      $final_master_array=array();		
	 if($result){
  	       foreach($result as $row){
  	         
			  $cu_id   = $row['cu_id'];
			  $unit_id = $row['cu_unit'];
			  $level_no = $row['list_level_id'];
			  $packing_id       = $row['packing_id'];
			  $master_array[$packing_id]=array();
			  	 
              
              $cu_info      =  $this->cu_info($row['cu_id']); 
	          $cu_unit_info =  $this->item_unit_info($row['cu_unit']);
              $cu_unit_name =  $this->enc_string->nc_string($cu_unit_info['item_unit'],'de');
              $cu_name      =  $cu_info['cu_label'];
			  
			 //$cuname       =  '<strong>'.$cu_unit_name.' '.$cu_name.'</strong><span style="float:right;"><a href="javascript:void(0);" onClick="finalize_cu_status(\''.$packing_id.'\',\''.$row['cu_id'].'\')" data-id="'.$packing_id.'" data-ajax="'.$row['cu_id'].'"  class="btn btn-sm btn-outline-success"> Finalize</a></span>';
			  $cuname       =  '<strong>'.$cu_unit_name.' '.$cu_name.'</strong>';
			  
			  if($level_id==1){
				$sub_array1 = $this->level1($packing_id,$packing_list_id);  	
			    if(isset($sub_array1['data']))
					 $sub_array = array($sub_array1);
				else 
					 $sub_array = $sub_array1; 
					 
				
				
			    $final_master_array[]= array("packing_id"=>$packing_id,"name" =>$cuname,"cu_id"=> $cu_id, "data"=>$sub_array,'level'=>'Level '.$level_no,'pq_detail'=>["show"=>true]); 
			  }		
            if($level_id==2){
				 $sub_array2 = $this->level2($packing_id,$packing_list_id);  
				 if(isset($sub_array2['data']))
					 $sub_array = array($sub_array2);
				else 
					 $sub_array = $sub_array2; 
				 
				 
			     $final_master_array[]= array("packing_id"=>$packing_id,"name" => $cuname,"cu_id"=> $cu_id,"data"=>$sub_array,'level'=>'Level '.$level_no,'pq_detail'=>["show"=>true]); 
			   }
			
          if($level_id==3){
			    $sub_array3 = $this->level3($packing_id,$packing_list_id);
				if(isset($sub_array3['data']))
					 $sub_array = array($sub_array3);
				else 
					 $sub_array = $sub_array3; 
				 
				$final_master_array[]= array("packing_id"=>$packing_id,"name" => $cuname,"cu_id"=> $cu_id,"data"=>$sub_array,'level'=>'Level '.$level_no,'pq_detail'=>["show"=>true]);
			  
			 
					
			  }
			 if($level_id==4){
			  $sub_array4 = $this->level4($packing_id,$packing_list_id); 
			  
			  if(isset($sub_array4['data']))
					 $sub_array = array($sub_array4);
				else 
					 $sub_array = $sub_array4; 
			  $final_master_array[]= array("packing_id"=>$packing_id,"name" =>$cuname,"cu_id"=> $cu_id,"data"=>$sub_array,'level'=>'Level '.$level_no,'pq_detail'=>["show"=>true]);
		     }	
			
		   if($level_id==5){
			  $sub_array5 = $this->level5($packing_id,$packing_list_id);										   
			  $final_master_array[]= array("packing_id"=>$packing_id,"name" => $cuname,"cu_id"=> $cu_id,"data"=>$sub_array5,'level'=>'Level '.$level_no);
		    }
 
					   
  	       }
  
	   
      }	
	   
   return $final_master_array;
   }
 
 
 function level_packed_items_lists($packing_list_id,$level_id){
	   //FETCH DATA HAVING VALUE 1 IN CU_STATUS FROM ALL LIST_LEVEL_ID BELOW THE CURRENT LEVEL FROM CUPACKINGN TABLE
       if($level_id >=2)
			 $freeze_column=1;
		 else
			 $freeze_column=0; 
	   $items1_array =array();
       $cupackingn_tbl = $this->company_id.'_cupackingn_'.$this->session->get('ses_comp_fy_id'); 
  	   $result         = $this->db->table($cupackingn_tbl)
	                    ->where('list_id', $packing_list_id)
	                    ->where('list_level_id <',$level_id)
						->where("cu_status","1")
						->groupBy('cu_id')
						->get()->getResultArray(); 
						
		$i=0;
     if($result){
  	       foreach($result as $row){
  	           $cu_id   = $row['cu_id'];
               $unit_id = $row['cu_unit'];
              
              $cu_info      =  $this->cu_info($row['cu_id']); 
	          $cu_unit_info =  $this->item_unit_info($row['cu_unit']);
              $cu_unit_name =  $this->enc_string->nc_string($cu_unit_info['item_unit'],'de');
              $cu_name      =  $cu_info['cu_label'];
        
		 
       $items1_array[]=array("freeze_column"=>$freeze_column,"editable"=>"false","is_cu"=>"1","cu_id" => $cu_id,"item_unit_id"=>$unit_id,"batch_no"=>"","specification_no"=>"","item_id"=>$cu_id,"id"=>$cu_id,"pq_cellattr"=>array("item_name"=>array("title"=>"")),"item_name"=>$cu_name.'('.$cu_unit_name.')','item_qty'=>1,'item_unit'=>$cu_unit_name,"to_carrying_unit_id"=>"","to_carrying_unit"=>"",'to_qty_packed'=>'','to_cu_save'=>'<a href="javascript:void(0);" onClick="save_unpacked_row(\''.$i.'\',\''.$cu_id.'\');"><img src="'.base_url().'/public/assets/img/icon-done.png"></a>');
  	    $i++;
  	       }
         
     }
	 
	//WITH DATA FROM PCKLISTQTY TABLE HAVING ITEM_QTY_AVAILABLE FIELD > 0   
	   $pcklistqty_tbl = $this->company_id.'_pcklistqty_'.$this->session->get('ses_comp_fy_id'); 
  	   $itsm_result    = $this->db->table($pcklistqty_tbl)
	                    ->where('item_qty_available >',0)
						->where('list_id', $packing_list_id)
						->get()->getResultArray();
					
		$items2_array=array();		

	   if($itsm_result){
		 foreach($itsm_result as $itmrow){
			 $item_id   = $itmrow['item_id'];
             $unit_id   = $itmrow['item_unit'];
			 $item_qty_available = $itmrow['item_qty_available'];
              
              $item_info      =  $this->get_item_info($item_id); 
			
	          $item_unit_info = $this->item_unit_info($unit_id);
              $item_unit_name = $this->enc_string->nc_string($item_unit_info['item_unit'],'de');
              $item_name      = $item_info['item_name']; 
			$items2_array[]=array("freeze_column"=>$freeze_column,"is_cu"=>"0","item_id" => $item_id,"item_unit_id"=>$unit_id,"batch_no"=>"","specification_no"=>"","id"=>$item_id,"pq_cellattr"=>array("item_name"=>array("title"=>"")),"item_name"=>$item_name.'('.$item_unit_name.')','item_qty'=>$item_qty_available,'item_unit'=>$item_unit_name,"to_carrying_unit_id"=>"","to_carrying_unit"=>"",'to_qty_packed'=>'','to_cu_save'=>'<a href="javascript:void(0);" onClick="save_row(\''.$i.'\');"><img src="'.base_url().'/public/assets/img/icon-done.png"></a>');
  	    $i++; 
		 }  
		   
		   
	   }					
						
     $final_array = array_merge($items1_array,$items2_array);
  return $final_array;
       
   }
   
   function modify_packed_items_lists($packing_list_id,$level_id){
       $items_array =array();
       $cupackingn_tbl = $this->company_id.'_cupackingn_'.$this->session->get('ses_comp_fy_id'); 
  	   $result         = $this->db->table($cupackingn_tbl)
	                    ->where('list_id', $packing_list_id)
	                    ->where('list_level_id <=',$level_id)
						->where("cu_status","1")
						->groupBy('cu_id')
						->get()->getResultArray(); 
						
		$i=0;
     if($result){
  	       foreach($result as $row){
  	           $cu_id   = $row['cu_id'];
               $unit_id = $row['cu_unit'];
              
              $cu_info      =  $this->cu_info($row['cu_id']); 
	          $cu_unit_info = $this->item_unit_info($row['cu_unit']);
              $cu_unit_name = $this->enc_string->nc_string($cu_unit_info['item_unit'],'de');
              $cu_name      =  $cu_info['cu_label'];
        
       $items_array[]=array("cu_id" => $cu_id,"item_unit_id"=>$unit_id,"batch_no"=>"","specification_no"=>"","item_id"=>$cu_id,"id"=>$cu_id,"pq_cellattr"=>array("item_name"=>array("title"=>"")),"item_name"=>$cu_name,'item_qty'=>1,'item_unit'=>$cu_unit_name,"to_carrying_unit_id"=>"","to_carrying_unit"=>"",'to_qty_packed'=>'','to_cu_save'=>'<a href="javascript:void(0);" onClick="save_unpacked_row(\''.$i.'\',\''.$cu_id.'\');"><img src="'.base_url().'/public/assets/img/icon-done.png"></a>');
  	    $i++;
  	       }
         
     }
     
  return $items_array;
       
   }
   
   
   function cupackingn_items($packing_list_id,$level_id){
	 // $all_cu_items_array = $this->itemscu_list($packing_list_id);
	  
	  $cupackingn_tbl = $this->company_id.'_cupackingn_'.$this->session->get('ses_comp_fy_id'); 
  	  $result         = $this->db->table($cupackingn_tbl)
	                    ->where('list_id', $packing_list_id)
	                    ->where("( (list_level_id='".$level_id."' AND cu_status='1') OR (list_level_id='".$level_id."' AND pck_status='1') )")
						->get()->getResultArray(); 
					//echo $this->db->GEtLastQuery();	
		
	 $cu_items=array();
     $i=0;	 
    
	 foreach($result as $row){
		$cu_info         =  $this->cu_info($row['cu_id']); 
	    $cu_unit_info = $this->item_unit_info($row['cu_unit']);
        $cu_unit_name = $this->enc_string->nc_string($cu_unit_info['item_unit'],'de');
        
        $cu_name        =  $cu_info['cu_label'];
        $cu_items[$row['cu_id']]=array("cu_unit_name"=>$cu_unit_name,"cu_label_name"=>$cu_name,"items"=>$this->get_cu_items_packed($row['cu_id'],$level_id,$row['cu_id']));
        
        
	   
		$i++; 
	 }					
	   

	  return $cu_items; 
   }

   function itemscu_list($packing_list_id,$level_id){	
      $item_json      = file_get_contents(WRITEPATH.'comp'.$this->company_id.'/item.json');   
      $cupackingn_tbl = $this->company_id.'_cupackingn_'.$this->session->get('ses_comp_fy_id'); 
      $this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
  	  $result         = $this->db->table($cupackingn_tbl)->where('list_level_id <', $level_id)->where('list_id', $packing_list_id)->groupBy('cu_id,cu_unit')->get()->getResultArray(); 	 
	  
	  $cu_final_list  = array();
      if($result){
		  foreach($result as $row){
			 $cu_info         =  $this->cu_info($row['cu_id']);
			 $cu_final_list[] = array('is_cu'=>'1','item_unit'=>'','item_unit_id'=>'','value'=>$cu_info['cu_label'],'item_id'=>$row['cu_id'],'label'=>$cu_info['cu_label'],'list_id'=>$row['list_id']);
		  }	
		  
	   $item_json = json_decode($item_json,true);
	   $items_array = array();
	   foreach($item_json as $r){
		  $r['is_cu'] =0;
		  $items_array[]=$r;  
	     }
	    $new_array = json_encode(array_merge($items_array,$cu_final_list));	  
	  }	  
	  else
	  $new_array  =$item_json;
	 
	 return $new_array; 
   }
   
  function GetPackingLevel($list_id){
	  $cupackingn_tbl  = $this->company_id.'_cupackingn_'.$this->session->get('ses_comp_fy_id'); 	
	  $builder         = $this->db->table($cupackingn_tbl); 
	  $builder->where('list_id',$list_id);
	  $builder->orderBy('packing_id','DESC');
	  $builder->limit(1);
	  return $builder->get()->getRowArray(); 
  } 
   

 function ajax_packing_list(){
	    $base_url      = base_url().'/'.getenv('AdminPath');
	    
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
        	
	 $pcklistmst_tbl  = $this->company_id.'_pcklistmst_'.$this->session->get('ses_comp_fy_id'); 	
	 $builder            = $this->db->table($pcklistmst_tbl); 
	  $result = $builder->get()->getResultArray();
	 $records = array(); 	
		
        foreach($result as $values){
			    $consignee_info   =  $this->get_account_info($values['list_consignee']);
			   	if(isset($consignee_info['acc_name']))
				    $acc_name = $consignee_info['acc_name'];
				else
					$acc_name = '';
				
			 // get packing max level 	
			$packing_level = $this->GetPackingLevel($values['list_id']);
			if($packing_level){
				$cu_status     = $packing_level['cu_status'];
				$list_level_id = $packing_level['list_level_id'];
				
				if($cu_status==1){					
					$next_level =$list_level_id;
					$packed_unpacked = 'packed';
					$manage_url = base_url().'/admin/consignment_packing/manage_list/'.$values['list_id'].'/'.$packed_unpacked.'/'.$next_level;
				}
				else {
					 $manage_url ='#';
				   }
				
			}
            else{
				$manage_url = base_url().'/admin/consignment_packing/manage_list/'.$values['list_id'];
			}			
				
			$records[] = array(	
			          'list_id'         => $values['list_id'],
                      'list_name'       => ucwords($values['list_name']),					 
					  'packing_against' => $acc_name,
                      'tenative_level'  => $values['list_delivery'],	
					  'packing_action'  =>'<a href="'.$manage_url.'" style="color:white;" class="btn btn-success btn-xs">Manage</a> &nbsp;<a href="javascript:void(0);" style="color:white;" onClick="delete_packing('.$values['list_id'].');" class="btn btn-danger btn-xs">Delete</a>'
 					  );
					  
				
		}
	 echo  "{\"totalRecords\":" .count($records) . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($records)."}"; 
 }
 
 function delete_consignment_packing($packing_id){
	 
	  $pcklistmst_name	 = $this->company_id.'_pcklistmst_'.$this->session->get('ses_comp_fy_id');
	  $pcklistqty_name	 = $this->company_id.'_pcklistqty_'.$this->session->get('ses_comp_fy_id');
	  $leveltrack_name	 = $this->company_id.'_leveltrack_'.$this->session->get('ses_comp_fy_id');
	  $listpacked_name	 = $this->company_id.'_listpacked_'.$this->session->get('ses_comp_fy_id');
	  $listcumast_name	 = $this->company_id.'_listcumast_'.$this->session->get('ses_comp_fy_id');
	  $cupackingn_name	 = $this->company_id.'_cupackingn_'.$this->session->get('ses_comp_fy_id');
 
	 
	 $Consoinfo = $this->GetConsoPackinginfo($packing_id);
	 $voucher_txn_id = $Consoinfo['voucher_txn_id'];
	  $data = $this->get_comp_txn_data($voucher_txn_id);
            foreach ($data as $key => $value) {
                if($value['master_id_type'] == 'acc'){
                    $this->delete_acc_txn_data($value['master_id'],$voucher_txn_id);
                }
                if($value['master_id_type'] == 'pck' || $value['master_id_type'] == 'upk'){
                    $this->delete_itm_bal_data($value['master_id'],$voucher_txn_id);
                    $this->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
                }               
            }

      $this->delete_comp_txn_data($voucher_txn_id);
      $this->delete_voucher_conso_data($voucher_txn_id);
      $this->db->table($pcklistmst_name)->where('list_id',$packing_id)->delete();
	  $this->db->table($pcklistqty_name)->where('list_id',$packing_id)->delete();
	  $this->db->table($leveltrack_name)->where('list_id',$packing_id)->delete();
	  $this->db->table($listpacked_name)->where('list_id',$packing_id)->delete();
	  $this->db->table($listcumast_name)->where('list_id',$packing_id)->delete();
	  $this->db->table($cupackingn_name)->where('list_id',$packing_id)->delete();			
            
 }
 
 function group_info($acc_grp_id){	 
      $account_grp_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id'); 
  	  return $this->db->table($account_grp_tbl)->where('acc_grp_id', $acc_grp_id)->get()->getRowArray();   	   
   }

 function add_list($table_name,$data){	
	$this->db->table($table_name)->insert($data);
	return $this->db->insertID();
 }
function add_cumast_list($table_name,$data){	
    $response = $this->db->table($table_name)->where('list_id',$data['list_id'])->where('list_level_id',$data['list_level_id'])->where('cu_unit',$data['cu_unit'])->where('LOWER(cu_label)',strtolower($data['cu_label']))->get()->getRowArray(); 
	if($response){
		return $response['cu_id'];
	}else{	
	$this->db->table($table_name)->insert($data);
	return $this->db->insertID();
	}
 }
 
 function add_cupackingn_list($table_name,$data){	
    $response = $this->db->table($table_name)->where('list_id',$data['list_id'])->where('cu_id',$data['cu_id'])->where('list_level_id',$data['list_level_id'])->countAllResults(); 
	if($response==0){	
	  $this->db->table($table_name)->insert($data);
	 return  $packing_id =  $this->db->insertID();
	  
 
		
	}
 }
 
 function previous_packing_info($cu_id,$list_id){
	 $cupackingn_table         = $this->company_id.'_cupackingn_'.$this->session->get('ses_comp_fy_id');	 
	 return $this->db->table($cupackingn_table)->where('list_id',$list_id)->where('cu_id',$cu_id)->get()->getRowArray();  
	 
 }
 
 function add_pcklistqty_list($table_name,$data){	
    $response = $this->db->table($table_name)->where('list_id',$data['list_id'])->where('item_id',$data['item_id'])->where('item_unit',$data['item_unit'])->get()->getRowArray(); 
	if(!$response){	
		$this->db->table($table_name)->insert($data);	
	}
 }
 
 function delete_listpacked($table_name,$list_level_id,$unit_id,$item_id,$list_id){
	$response = $this->db->table($table_name)->where('list_level_id',$list_level_id)->where('item_unit',$unit_id)->where('item_id',$item_id)->get()->getRowArray(); 
	if($response)
	  $item_qty_packed = $response['item_qty_packed'];
    else
	  $item_qty_packed = 0;	
	
   $availaqty       = $this->pcklistqty_availability($list_id,$item_id,$unit_id);	
   $pcklistqty_tbl  = $this->company_id.'_pcklistqty_'.$this->session->get('ses_comp_fy_id');	
   $update_data     = array("item_qty_available"=>$availaqty+$item_qty_packed);
   $this->update_pcklistqty($pcklistqty_tbl,$list_id,$item_id,$unit_id,$update_data);

   $this->db->table($table_name)->where('list_level_id',$list_level_id)->where('item_unit',$unit_id)->where('item_id',$item_id)->delete();
	 
 }
 
 function pcklistqty_availability($list_id,$item_id,$item_unit){
	 $pcklistqty_tbl  = $this->company_id.'_pcklistqty_'.$this->session->get('ses_comp_fy_id');	
	 
	 $builder    = $this->db->table($pcklistqty_tbl);
	 $builder->select('item_qty_available');
	 $builder->where('list_id',$list_id);
	 $builder->where('item_id',$item_id);
	 $builder->where('item_unit',$item_unit);
	 $result = $builder->get()->getRowArray();
	 if($result){
		 return $result['item_qty_available'];
		 }
      else
		  return 0;
 }
 
 function update_leveltrack($tablename,$data,$packing_id){
	$this->db->table($tablename)->where('packing_id', $packing_id)->update($data); 
	 
 }

 function update_pcklistqty($tablename,$list_id,$item_id,$item_unit,$update_data){
	$this->db->table($tablename)->where('item_unit', $item_unit)->where('item_id', $item_id)->where('list_id', $list_id)->update($update_data);
 }
 
 function update_prev_cu_status($tablename,$list_id,$packing_id,$update_data){
	$this->db->table($tablename)->where('packing_id', $packing_id)->where('list_id', $list_id)->update($update_data);
 } 
 function update_pcklistmst($tablename,$update_data,$list_id){
	$this->db->table($tablename)->where('list_id', $list_id)->update($update_data);
 }

function consignment_packing_info($packing_id){
	$pcklistmst_tbl  = $this->company_id.'_pcklistmst_'.$this->session->get('ses_comp_fy_id');
   return $this->db->table($pcklistmst_tbl)->where('list_id', $packing_id)->where('comp_id', $this->company_id)->get()->getRowArray();	
	
}

function GetConsoPackinginfo($packing_id){
	$vhtxnconso_tbl  = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');	
	return $this->db->table($vhtxnconso_tbl)->where('comp_id', $this->company_id)
	                   ->where('voucher_type_id', '4')
					   ->where('voucher_tag', trim($packing_id))
					   ->get()->getRowArray();
			 
}

function consignmentto_dropdown($array = [22]){

    	$final_array = $this->get_sub_group_ids($array);
    	
		$comp_id = $this->company_id;
		$comp_party_tbl = $comp_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
		$data =  $this->db->table($comp_party_tbl)
						  ->select('acc_id, acc_name, acc_grp_id')
						  ->whereIn('acc_grp_id', $final_array)
						  ->orderBy('acc_name','ASC')
						  ->get()->getResultArray();
		$final_array = array();	
        $final_array[''] = 'Choose';		
		if($data){
			foreach($data as $row){
				$final_array[$row['acc_id']] = $row['acc_name'];		
				
			}
			
		}				 
		return $final_array;	
	}

	function get_currency_list()
	{
		$compcurrcy_tbl = $this->company_id.'_compcurrcy_'.$this->session->get('ses_comp_fy_id');
    	$data = $this->db->table($compcurrcy_tbl)->where('comp_id', $this->company_id)->get()->getResultArray();

    	return $data;
	}

	function get_currency($comp_currency_id)
	{
		$compcurrcy_tbl = $this->company_id.'_compcurrcy_'.$this->session->get('ses_comp_fy_id');
    	$data = $this->db->table($compcurrcy_tbl)->where('comp_id', $this->company_id)->where('comp_currency_id', $comp_currency_id)->get()->getRowArray();

    	if($data){
    		return $data['curr_name'] . '('. $data['curr_symbol'] .')';
    	}
    	return '';
	}

	public function get_forex_rate_inr($comp_currency_id,$curr_date)
	{
        $forexrates_tbl = $this->company_id.'_forexrates_'.$this->session->get('ses_comp_fy_id');;
    	$builder = $this->db->table($forexrates_tbl);
    	$builder->where('curr_date <=', $curr_date);
    	$builder->where('comp_currency_id', $comp_currency_id);
   		$builder->orderBy('curr_date', 'desc');
   		$builder->orderBy('forex_rate_id', 'desc');
   		$builder->limit(1);
    	$result = $builder->get()->getRowArray();

    	if($result){
    		return $result['rate_per_inr'];
    	}

    	return 1;
	}

	public function get_forex_rate_fcy($comp_currency_id,$curr_date)
	{
        $forexrates_tbl = $this->company_id.'_forexrates_'.$this->session->get('ses_comp_fy_id');;
    	$builder = $this->db->table($forexrates_tbl);
    	$builder->where('curr_date <=', $curr_date);
    	$builder->where('comp_currency_id', $comp_currency_id);
   		$builder->orderBy('curr_date', 'desc');
   		$builder->orderBy('forex_rate_id', 'desc');
   		$builder->limit(1);
    	$result = $builder->get()->getRowArray();

    	if($result){
    		return $result['rate_per_fcy'];
    	}

    	return 1;
	}

	function calculate_valuation($item_id, $item_unit, $mat_cent_id, $batch_id)
	{
		$op_bal_qty = 0;
		$op_avg_val = 0;
		$op_fifo_val = 0;

		$itmoppybal_tbl = $this->company_id.'_itmoppybal_'.$this->session->get('ses_comp_fy_id');
		$builder = $this->db->table($itmoppybal_tbl); 
		$builder->where('item_id', $item_id);
		$builder->where('item_unit', $item_unit);
		$builder->where('mat_cent_id', $mat_cent_id);
		if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
		$builder->where('batch_id', $batch_id);	
    	$itmoppybal = $builder->get()->getRowArray();
    	if($itmoppybal){
    		$op_bal_qty = floatval($itmoppybal['op_bal_qty']);
    	}

	    $itmoppyval_tbl = $this->company_id.'_itmoppyval_'.$this->session->get('ses_comp_fy_id');
		$builder = $this->db->table($itmoppyval_tbl); 
		$builder->where('item_id', $item_id);
		$builder->where('item_unit', $item_unit);
		$builder->where('mat_cent_id', $mat_cent_id);
		if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
		$builder->where('batch_id', $batch_id);
		$builder->where('method_id', 1);
		$itmoppyval = $builder->get()->getRowArray();
		if($itmoppyval){
    		$op_avg_val = floatval($itmoppyval['op_bal_val']);
    	}

	    $builder = $this->db->table($itmoppyval_tbl); 
		$builder->where('item_id', $item_id);
		$builder->where('item_unit', $item_unit);
		$builder->where('mat_cent_id', $mat_cent_id);
		if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
		$builder->where('batch_id', $batch_id);
		$builder->where('method_id', 2);
		$itmoppyval = $builder->get()->getRowArray();
		if($itmoppyval){
    		$op_fifo_val = floatval($itmoppyval['op_bal_val']);
    	}

	    $itemtxnnnn_tbl = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');

	    $builder = $this->db->table($itemtxnnnn_tbl); 
		$builder->where('item_id', $item_id);
		$builder->where('item_unit', $item_unit);
		$builder->where('mat_cent_id', $mat_cent_id);
		$builder->where('batch_id', $batch_id);	
		if($this->session->get('ses_boid')!=''){
		 	$builder->where('bo_id', $this->session->get('ses_boid'));
		}
		$builder->orderBy('item_txn_date', 'asc');
		$builder->orderBy('voucher_txn_id', 'asc');
		$builder->orderBy('item_txn_id', 'asc');
    	$transactions = $builder->get()->getResultArray();

    	foreach ($transactions as $key => $value) {

    		$this->update_item_avg_valuation($item_id,$item_unit,$mat_cent_id,$batch_id,$op_bal_qty,$op_avg_val,$value['item_txn_id'],$value['item_txn_drcr'],$value['item_txn_qty'],$value['item_txn_date'],$value['voucher_txn_id'],$value['item_avail'],$value['item_bal_qty']);

    		$this->update_item_fifo_valuation($item_id,$item_unit,$mat_cent_id,$batch_id,$op_bal_qty,$op_fifo_val,$value['item_txn_id'],$value['item_txn_drcr'],$value['item_txn_qty'],$value['item_txn_date'],$value['voucher_txn_id'],$value['item_avail'],$value['item_bal_qty']);
    	}

  	}


  	public function update_item_avg_valuation($item_id,$item_unit,$mat_cent_id,$batch_id,$op_bal_qty,$op_avg_val,$item_txn_id,$item_txn_drcr,$item_txn_qty,$item_txn_date,$voucher_txn_id,$item_avail,$item_bal_qty)
  	{

  	  $item_value = 0;

  	  $itemtxnnnn_tbl = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
  	  $itemtxnvaln_tbl  = $this->company_id.'_itemtxnval_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');

  	  if($item_avail == 1)
  	  {
  	  	$item_value = $op_avg_val;

  	  	if($item_txn_drcr == 'c')
		{
			//check last debit transaction
			$builder = $this->db->table($itemtxnnnn_tbl);
			$builder->select('item_txn_id'); 
			$builder->where('item_id', $item_id);
			$builder->where('item_unit', $item_unit);
			$builder->where('mat_cent_id', $mat_cent_id);
			$builder->where('batch_id', $batch_id);
			$builder->where('item_txn_drcr', 'd');
			$builder->where('item_avail', 1);
			$builder->where('item_txn_date <=', $item_txn_date);
			$builder->where('voucher_txn_id <=', $voucher_txn_id);
			$builder->where('item_txn_id <', $item_txn_id);	
			if($this->session->get('ses_boid')!=''){
				$builder->where('bo_id', $this->session->get('ses_boid'));
			}
			$builder->orderBy('item_txn_date', 'desc');
			$builder->orderBy('voucher_txn_id', 'desc');
			$builder->orderBy('item_txn_id', 'desc');
			$builder->limit(1);
			$transaction = $builder->get()->getRowArray();
			if($transaction){
					
				$builder = $this->db->table($itemtxnvaln_tbl);
				$builder->where('item_id', $item_id);
				$builder->where('item_txn_id', $transaction['item_txn_id']);
				$builder->where('method_id', 1);
				$itemtxnvaln = $builder->get()->getRowArray();
				if($itemtxnvaln){
					$item_value = floatval($itemtxnvaln['item_value']);
				}
			}
		}
		if($item_txn_drcr == 'd')
		{
			// total debit amount / total debit quantity
			$builder = $this->db->table($itemtxnnnn_tbl);
			$builder->select('count(item_txn_id) as debit_transactions, sum(item_txn_qty) as debit_quantity, sum(item_txn_amount) as debit_amount'); 
			$builder->where('item_id', $item_id);
			$builder->where('item_unit', $item_unit);
			$builder->where('mat_cent_id', $mat_cent_id);
			$builder->where('batch_id', $batch_id);
			$builder->where('item_txn_drcr', 'd');
			$builder->where('item_avail', 1);
			$builder->where('item_txn_date <=', $item_txn_date);
			$builder->where('voucher_txn_id <=', $voucher_txn_id);
			$builder->where('item_txn_id <', $item_txn_id);	
			if($this->session->get('ses_boid')!=''){
			$builder->where('bo_id', $this->session->get('ses_boid'));
			}
			$transaction = $builder->get()->getRowArray();
			if($transaction){
				if(floatval($transaction['debit_transactions']) > 0){
					$total_quantity = floatval($op_bal_qty + floatval($transaction['debit_quantity']));
					$total_amount = floatval($op_avg_val + floatval($transaction['debit_amount']));

					$rate = $total_quantity != 0 ? floatval($total_amount/$total_quantity) : 0;
					$item_value = floatval($item_bal_qty * $rate);
				}
			}
		}
  	  }

		$builder = $this->db->table($itemtxnvaln_tbl);
		$builder->where('item_id', $item_id);
		$builder->where('item_txn_id', $item_txn_id);
		$builder->where('method_id', 1);
		$itemtxnvaln = $builder->get()->getRowArray();
		
		if($itemtxnvaln){
			$valuation_id = $itemtxnvaln['valuation_id'];
			$this->db->table($itemtxnvaln_tbl)
					->where('valuation_id', $valuation_id)
					->update(['item_value' => $item_value]);
		}
		else{
			$data = [
				'item_id' => $item_id,
				'item_txn_id' => $item_txn_id,
				'method_id' => 1,
				'item_value' => $item_value,
			];
			$this->db->table($itemtxnvaln_tbl)->insert($data);
		}
  	}

  	public function update_item_fifo_valuation($item_id,$item_unit,$mat_cent_id,$batch_id,$op_bal_qty,$op_fifo_val,$item_txn_id,$item_txn_drcr,$item_txn_qty,$item_txn_date,$voucher_txn_id,$item_avail,$item_bal_qty)
  	{

  	  $item_value = 0;

  	  $itemtxnnnn_tbl = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
  	  $itemtxnvaln_tbl  = $this->company_id.'_itemtxnval_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');

  	  if($item_avail == 1)
  	  {
  	  	$credit_quantity = 0;
    	$left_quantity = 0;
    	$rate = 0;

    	$case = 0;
    	$debit_item_txn_id = 0;
    	$debit_voucher_txn_id = 0;
    	$debit_item_txn_date = 0;

    	$debit_amount = 0;

  	  	$builder = $this->db->table($itemtxnnnn_tbl);
		$builder->select('count(item_txn_id) as credit_transactions, sum(item_txn_qty) as credit_quantity'); 
		$builder->where('item_id', $item_id);
		$builder->where('item_unit', $item_unit);
		$builder->where('mat_cent_id', $mat_cent_id);
		$builder->where('batch_id', $batch_id);
		$builder->where('item_txn_drcr', 'c');
		$builder->where('item_avail', 1);
		$builder->where('item_txn_date <=', $item_txn_date);
		$builder->where('voucher_txn_id <=', $voucher_txn_id);
		$builder->where('item_txn_id <', $item_txn_id);	
		if($this->session->get('ses_boid')!=''){
		$builder->where('bo_id', $this->session->get('ses_boid'));
		}
		$transaction = $builder->get()->getRowArray();
		if($transaction){
			if(floatval($transaction['credit_transactions']) > 0){
				$credit_quantity = floatval($transaction['credit_quantity']);
			}
		}
    		

    	if($op_bal_qty >= $credit_quantity){

    		$left_quantity = $op_bal_qty - $credit_quantity;
    		$rate = $op_bal_qty != 0 ? floatval($op_fifo_val / $op_bal_qty) : 0;
    		$case = 1;
    	}
    	else{
	        $builder->where('item_id', $item_id);
			$builder->where('item_unit', $item_unit);
			$builder->where('mat_cent_id', $mat_cent_id);
			$builder->where('batch_id', $batch_id);
			$builder->where('item_txn_drcr', 'd');
			$builder->where('item_avail', 1);
			$builder->where('item_txn_date <=', $item_txn_date);
			$builder->where('voucher_txn_id <=', $voucher_txn_id);
			$builder->where('item_txn_id <=', $item_txn_id);	
			if($this->session->get('ses_boid')!=''){
				$builder->where('bo_id', $this->session->get('ses_boid'));
			}
        	$transactions = $builder->get()->getResultArray();

        	$bal = $op_bal_qty; 
        	if($transactions){
        		foreach ($transactions as $key => $transaction) {
        			$bal += floatval($transaction['item_txn_qty']);

        			if($bal  >= $credit_quantity){

			        	$left_quantity = $bal - $credit_quantity; 
			        	$rate = $transaction['item_txn_qty'] != 0 ? floatval($transaction['item_txn_amount']/$transaction['item_txn_qty']) : 0;

		        		$debit_item_txn_id = $transaction['item_txn_id'];
		        		$debit_voucher_txn_id = $transaction['voucher_txn_id'];
		        		$debit_item_txn_date = $transaction['item_txn_date'];
		        		$case = 2;
		        		break;
        			}
        			
        		}
        	}	
    	}

        $builder = $this->db->table($itemtxnnnn_tbl);
		$builder->select('count(item_txn_id) as debit_transactions, sum(item_txn_amount) as debit_amount'); 
		$builder->where('item_id', $item_id);
		$builder->where('item_unit', $item_unit);
		$builder->where('mat_cent_id', $mat_cent_id);
		$builder->where('batch_id', $batch_id);
		$builder->where('item_txn_drcr', 'd');
		$builder->where('item_avail', 1);
		$builder->where('item_txn_date <=', $item_txn_date);
		$builder->where('voucher_txn_id <=', $voucher_txn_id);
		$builder->where('item_txn_id <=', $item_txn_id);
		if($case == 2){
			$builder->where('item_txn_date >=', $debit_item_txn_date);
			$builder->where('voucher_txn_id >=', $debit_voucher_txn_id);
			$builder->where('item_txn_id >', $debit_item_txn_id);
		}	
		if($this->session->get('ses_boid')!=''){
			$builder->where('bo_id', $this->session->get('ses_boid'));
		}
		$transaction = $builder->get()->getRowArray();
		if($transaction){
			if(floatval($transaction['debit_transactions']) > 0){
				$debit_amount = floatval($transaction['debit_amount']);
			}
		}

        $item_value = floatval(($left_quantity * $rate) + $debit_amount);

        $builder = $this->db->table($itemtxnvaln_tbl);
		$builder->where('item_id', $item_id);
		$builder->where('item_txn_id', $item_txn_id);
		$builder->where('method_id', 2);
		$itemtxnvaln = $builder->get()->getRowArray();
		
		if($itemtxnvaln){
			$valuation_id = $itemtxnvaln['valuation_id'];
			$this->db->table($itemtxnvaln_tbl)
					->where('valuation_id', $valuation_id)
					->update(['item_value' => $item_value]);
		}
		else{
			$data = [
				'item_id' => $item_id,
				'item_txn_id' => $item_txn_id,
				'method_id' => 2,
				'item_value' => $item_value,
			];
			$this->db->table($itemtxnvaln_tbl)->insert($data);
		}
	  }
  	}
	
}