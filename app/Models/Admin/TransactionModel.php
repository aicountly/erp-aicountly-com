<?php
namespace App\Models\Admin;

use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Libraries\enc_string;
use App\Libraries\UUIDtables; 
use App\Libraries\ERPtables;

class TransactionModel extends Model{

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
	   $this->dberpunvrsl   =  $this->externaldb->erp_db();
	   $this->session       = \Config\Services::session();
	   $this->company_id    = $this->session->get('ses_company_id');
	   $this->enc_string    = new enc_string();
	   $this->aicountly_db  = $this->externaldb->aicountly_db();
	   $this->uuid_db          = $this->externaldb->connect_universal_uuid_db();
	   $this->bo_id            = $this->session->get('ses_boid');
	   $this->uuid             = $this->session->get('uuid');
    }
 
 /* function total_sundry_txn_amount($bsd_id,$voucher_txn_id,$voucher_type_id){
	$sundrytxnn_tbl  = $this->company_id.'_sundrytxnn_'.$bsd_id.'_'.$this->session->get('ses_comp_fy_id');	
    $sundrytxnn_info =  $this->db->table($sundrytxnn_tbl)->select('sundry_txn_amount,comp_id,sundry_txn_drcr')->where('comp_id',$this->company_id)->where('voucher_txn_id',$voucher_txn_id)->where('voucher_type_id',$voucher_type_id)->get()->getRowArray();
    $billsundry_amount =0;
	if($sundrytxnn_info){
		$billsundry_amount = $value['sundry_txn_amount'];
	} 
	return $billsundry_amount;
 }   */
 
  function SaveServerQueryLog($response){
	 $uuid =  $this->session->get('uuid');  
	 $comp_id    =  $this->session->get('ses_company_id');
	 $comp_name  =  $this->session->get('ses_company_name');
	 $data       =  array("comp_id"=>$comp_id,"comp_name"=>$comp_name,
	                      "page_url"=>$response['url'],
	                      "queries_log"=>$response['query'],"querytime"=>$response['time_seconds'],
						  "entry_time"=>date("Y-m-d H:i:s"),'uuid'=>$uuid);
     $this->dberpunvrsl->table('aictlyerp_univerpssa_univdb')->insert($data);
  }
 
  function GetOtherBoInfo($bo_id){
		$tbl_name     = $this->company_id.'_hobomaster_'.$this->session->get('ses_comp_fy_id');
		$data         = $this->db->table($tbl_name)
		->select('bo_id,bo_name,bo_ho,bo_state_code')
		->where('bo_id',$bo_id)
		->get()->getRowArray();
		return 	$data;
	}
  function all_mig_bo_lists(){ 
     $current_bo          = $this->session->get('ses_boid');
	 $hobomaster_tbl = $this->company_id.'_hobomaster_'.$this->session->get('ses_comp_fy_id'); 
	 $data           = $this->db->table($hobomaster_tbl)->where('bo_id !=',$current_bo)->orderBy('bo_name')->get()->getResultArray();
	 $final_result   = array();
	 if($data){
	  foreach($data as $row)
	   $final_result[$row['bo_id']] = ucwords($row['bo_name']);			   			
	 } 
	return $final_result; 
  }
 
 function migration_voucher_boid($voucher_txn_id,$move_bo_id){
	try{ 
	$ewbmstreqn_tbl     =  $this->company_id.'_ewbmstreqn_'.$this->session->get('ses_comp_fy_id');
    $acctcrsref_tbl     =  $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
	$memotxnnnn_tbl     =  $this->company_id.'_memotxnnnn_'.$this->session->get('ses_comp_fy_id');
	$comptxnmst_master  =  $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	$accoppybal_tbl     =  $this->company_id.'_accoppybal_'.$this->session->get('ses_comp_fy_id');
	$billstxnnn_tbl     =  $this->company_id.'_billstxnnn_'.$this->session->get('ses_comp_fy_id');
	$costcttxnn_tbl     =  $this->company_id.'_costcttxnn_'.$this->session->get('ses_comp_fy_id');
	$accttxnoth_tbl     =  $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
	$itemtxnoth_tbl     =  $this->company_id.'_itemtxnoth_'.$this->session->get('ses_comp_fy_id');
	$voucher_cons_tbl   =  $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	$prjliabtxn_tbl     =  $this->company_id.'_prjliabtxn_'.$this->session->get('ses_comp_fy_id');
	$projasttxn_tbl     =  $this->company_id.'_projasttxn_'.$this->session->get('ses_comp_fy_id');
	$projexptxn_tbl     =  $this->company_id.'_projexptxn_'.$this->session->get('ses_comp_fy_id');
	$projrevtxn_tbl     =  $this->company_id.'_projrevtxn_'.$this->session->get('ses_comp_fy_id');
	$bsdoppybal_tbl      =  $this->company_id.'_bsdoppybal_'.$this->session->get('ses_comp_fy_id');
    
	$this->db->table($voucher_cons_tbl)->where('voucher_txn_id',$voucher_txn_id)->update(array('bo_id'=>$move_bo_id));
	$this->db->table($ewbmstreqn_tbl)->where('voucher_txn_id',$voucher_txn_id)->update(array('bo_id'=>$move_bo_id));	  
	$this->db->table($billstxnnn_tbl)->where('voucher_txn_id',$voucher_txn_id)->update(array('bo_id'=>$move_bo_id));
	$this->db->table($prjliabtxn_tbl)->where('voucher_txn_id',$voucher_txn_id)->update(array('bo_id'=>$move_bo_id));
	$this->db->table($memotxnnnn_tbl)->where('voucher_txn_id',$voucher_txn_id)->update(array('bo_id'=>$move_bo_id));	  
	$this->db->table($costcttxnn_tbl)->where('voucher_txn_id',$voucher_txn_id)->update(array('bo_id'=>$move_bo_id));
				  			
	$comp_itemtexn      =  $this->db->table($comptxnmst_master)->where('voucher_txn_id',$voucher_txn_id)->get()->getResultArray(); 
	if($comp_itemtexn){
	    foreach($comp_itemtexn as $cmptxn_row){
	        $master_id_type =  $cmptxn_row['master_id_type'];
	        $itm_txn_id     =  $cmptxn_row['txn_id'];
			if($master_id_type=='acc'){
			  $acc_id	 = $cmptxn_row['master_id'];
			  
			  $accxn_tbl = $this->company_id.'_accnttxnnn_'.$acc_id.'_'.$this->session->get('ses_comp_fy_id');
			  $this->db->table($accxn_tbl)->where('acc_id',$acc_id)->where('voucher_txn_id',$voucher_txn_id)->update(array('bo_id'=>$move_bo_id));
			  
			  $bal=0; 		
		      $balance = $this->db->table($accoppybal_tbl)->where('bo_id', $move_bo_id)->where('acc_id', $acc_id)->get()->getRowArray();
		      if($balance){
			    $bal  = $balance['acc_op_bal'];
			    if($bal=='' || $bal=='0')
			    $bal=0;
		       }
			  $this->db->query('SET @bal = '.$bal.';');
			  $this->db->query('UPDATE '.$accxn_tbl.' SET acc_bal = CASE WHEN acc_txn_drcr = "c" THEN @bal:=@bal - acc_txn_amount WHEN acc_txn_drcr = "d" THEN @bal:=@bal + acc_txn_amount ELSE 0 END where acc_id = '.$acc_id.' and bo_id = '.$move_bo_id.' order by acc_txn_date,voucher_txn_id,acc_txn_id;');
			 
			  $this->db->table($acctcrsref_tbl)->where('voucher_txn_id',$voucher_txn_id)->update(array('bo_id'=>$move_bo_id));	  
			  $this->db->table($accttxnoth_tbl)->where('acc_id',$acc_id)->where('voucher_txn_id',$voucher_txn_id)->update(array('bo_id'=>$move_bo_id));
			  
			 }
			 
			if($master_id_type=='itm'){
			 $item_id	   =  $cmptxn_row['master_id'];
	         $itemtxn_tbl =  $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
	         $this->db->table($itemtxn_tbl)->where('item_id',$item_id)->where('voucher_txn_id',$voucher_txn_id)->update(array('bo_id'=>$move_bo_id));
		     $this->db->table($itemtxnoth_tbl)->where('item_id',$item_id)->where('voucher_txn_id',$voucher_txn_id)->update(array('bo_id'=>$move_bo_id));
			 
	     	 $iteminfo     = $this->get_item_info($item_id);
			 $valmethod_id = ($iteminfo['valmethod_id'])?$iteminfo['valmethod_id']:1;
		    
			 $item_txn_data = $this->db->table($itemtxn_tbl)->where('item_id',$item_id)->where('voucher_txn_id',$voucher_txn_id)->orderBy('item_txn_date', 'asc')->orderBy('voucher_txn_id', 'asc')->orderBy('item_txn_id', 'asc')->get()->getResultArray(); 
			 if($item_txn_data){
				foreach($item_txn_data as $txnrow){
				   $item_unit    = $txnrow['item_unit'];
			       $mat_cent_id  = $txnrow['mat_cent_id'];
			       $batch_id     = $txnrow['batch_id'];	
				   $item_avail   = $txnrow['item_avail'];
				   $item_txnid   = $txnrow['txn_id'];
		     	  
				   $bal = $this->get_item_op_bal($item_id,$item_unit,$mat_cent_id,$batch_id,$move_bo_id);
				   $this->db->query('SET @bal = '.$bal.';');		
				   $this->db->query('UPDATE '.$itemtxn_tbl.' SET item_bal_qty = CASE WHEN item_txn_drcr = "c" THEN @bal:=@bal - item_txn_qty WHEN item_txn_drcr = "d" THEN @bal:=@bal + item_txn_qty ELSE 0 END where item_id = "'.$item_id.'" AND item_unit = "'.$item_unit.'" AND mat_cent_id = "'.$mat_cent_id.'" AND batch_id = "'.$batch_id.'" AND item_avail = "'.$item_avail.'" AND bo_id = "'.$move_bo_id.'" order by item_txn_date,voucher_txn_id,item_txn_id;');
				   $this->calculate_valuation_avg($item_id, $item_unit, $mat_cent_id, $batch_id,$bal,1,0,$voucher_txn_id,$item_txnid,$move_bo_id);
				   $this->calculate_valuation_fifo($item_id, $item_unit, $mat_cent_id, $batch_id,$bal,2,0,$voucher_txn_id,$item_txnid,$move_bo_id);   
				   $this->calculate_valuation_lifo($item_id, $item_unit, $mat_cent_id, $batch_id,$bal,3,0,$voucher_txn_id,$item_txnid,$move_bo_id);
				}
			  }   
			}
			if($master_id_type=='bsd'){
			  $bsd_id	   =  $cmptxn_row['master_id'];
	          $bsdtxn_tbl  =  $this->company_id.'_sundrytxnn_'.$bsd_id.'_'.$this->session->get('ses_comp_fy_id');
	          $this->db->table($bsdtxn_tbl)->where('bill_sundry_id',$bsd_id)->where('voucher_txn_id',$voucher_txn_id)->update(array('bo_id'=>$move_bo_id));
		    
			  $bal=0; //include opening balance
		      $this->db->query('SET @bal = '.$bal.';');
		      $this->db->query('UPDATE '.$memotxnnnn_tbl.' SET acc_bal = CASE WHEN acc_txn_drcr = "c" THEN @bal:=@bal - acc_txn_amount WHEN acc_txn_drcr = "d" THEN @bal:=@bal + acc_txn_amount ELSE 0 END where acc_id = "'.$bsd_id.'" AND bo_id = "'.$move_bo_id.'" AND acc_type = "bsd" order by acc_txn_date, acc_txn_id;'); 
    		 
			  $bals = 0;        
              $result = $this->db->table($bsdoppybal_tbl)->where('bo_id', $move_bo_id)->where('bill_sundry_id', $bsd_id)->get()->getRowArray();
			  $bals = !empty($result['bsd_op_bal']) ? $result['bsd_op_bal'] : 0;
    	      $bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$bsd_id.'_'.$this->session->get('ses_comp_fy_id');
		      $this->db->query('SET @bal = '.$bals.';');
			  $this->db->query('UPDATE '.$bs_txn_tbl.' SET sundry_bal = CASE WHEN sundry_txn_drcr = "c" THEN @bal:=@bal - sundry_txn_amount WHEN sundry_txn_drcr = "d" THEN @bal:=@bal + sundry_txn_amount ELSE 0 END where bill_sundry_id = "'.$bsd_id.'" AND bo_id = "'.$move_bo_id.'" order by sundry_txn_date,voucher_txn_id,sundry_txn_id;');
 			   }
	        }
			
		$prjliabtxn_result = $this->db->table($prjliabtxn_tbl)->where('bo_id', $move_bo_id)->where('voucher_txn_id', $voucher_txn_id)->get()->getResultArray();
    	if($prjliabtxn_result){
			foreach($prjliabtxn_result as $key => $value){
				$proj_txn_id = $value['proj_txn_id'];
				$acc_id      = $value['acc_id'];
				$project_id  = $value['project_id'];
				$acc_type    = $value['acc_type'];
				$acc_grp_parent_id = $this->get_account_parent_id($acc_id,$acc_type);				
				//liability
				if(in_array($acc_grp_parent_id, [1,2,4])){					
					$bal = $this->get_project_op_bal($project_id, 'lia',$move_bo_id);
					$this->db->query('SET @bal = '.$bal.';');
					$this->db->query('UPDATE '.$prjliabtxn_tbl.' SET proj_txn_bal = CASE WHEN proj_txn_drcr = "C" THEN @bal:=@bal - proj_txn_amt WHEN proj_txn_drcr = "D" THEN @bal:=@bal + proj_txn_amt ELSE 0 END where project_id = "'.$project_id.'" AND bo_id = "'.$move_bo_id.'" order by proj_txn_date,voucher_txn_id,proj_txn_id;'); 
				 }
				//assets
				if(in_array($acc_grp_parent_id, [3,5])){
					$bal = $this->get_project_op_bal($project_id, 'ast',$move_bo_id);
					$this->db->query('SET @bal = '.$bal.';');
					$this->db->query('UPDATE '.$projasttxn_tbl.' SET proj_txn_bal = CASE WHEN proj_txn_drcr = "C" THEN @bal:=@bal - proj_txn_amt WHEN proj_txn_drcr = "D" THEN @bal:=@bal + proj_txn_amt ELSE 0 END where project_id = "'.$project_id.'" AND bo_id = "'.$move_bo_id.'" order by proj_txn_date,voucher_txn_id,proj_txn_id;');
				}
				//expense
				if(in_array($acc_grp_parent_id, [7,11,13])){
					$bal = $this->get_project_op_bal($project_id, 'exp',$move_bo_id);
					$this->db->query('SET @bal = '.$bal.';');
					$this->db->query('UPDATE '.$projexptxn_tbl.' SET proj_txn_bal = CASE WHEN proj_txn_drcr = "C" THEN @bal:=@bal - proj_txn_amt WHEN proj_txn_drcr = "D" THEN @bal:=@bal + proj_txn_amt ELSE 0 END where project_id = '.$project_id.' AND bo_id = '.$move_bo_id.' order by proj_txn_date,voucher_txn_id,proj_txn_id;');
				}
				//revenue
				if(in_array($acc_grp_parent_id, [8,10,12])){
					$bal = $this->get_project_op_bal($project_id, 'rev',$move_bo_id);
					$this->db->query('SET @bal = '.$bal.';');
					$this->db->query('UPDATE '.$projrevtxn_tbl.' SET proj_txn_bal = CASE WHEN proj_txn_drcr = "C" THEN @bal:=@bal - proj_txn_amt WHEN proj_txn_drcr = "D" THEN @bal:=@bal + proj_txn_amt ELSE 0 END where project_id = '.$project_id.' AND bo_id = '.$move_bo_id.' order by proj_txn_date,voucher_txn_id,proj_txn_id;');
				}

			}
		}
		
			
		$billstxn_result = $this->db->table($billstxnnn_tbl)->where('bo_id', $move_bo_id)->where('voucher_txn_id', $voucher_txn_id)->get()->getResultArray();
    	$bills_ref_ids = [];
    	if($billstxn_result){
	    	foreach($billstxn_result as $key => $value){
	        	$bills_ref_ids[] = $value['bills_ref_id'];
	    	}
	     }
		if($bills_ref_ids){
        	foreach($bills_ref_ids as  $bills_ref_id){
	        	$this->update_bill_txn_balance($bills_ref_id,$move_bo_id);
	        }
        }
		
		$costcttxnn_result = $this->db->table($costcttxnn_tbl)->where('voucher_txn_id', $voucher_txn_id)->get()->getResultArray();
    	if($costcttxnn_result){
	    	foreach($costcttxnn_result as $key => $value){
				$cc_id = $value['cc_id'];
	        	$this->update_cc_txn_balance($cc_id,$move_bo_id);
	    	}
	     }
		
	   }
	   return array('status'=>1,'message'=>'');
	}
	catch (\Exception $e) {
		return array('status'=>0,'message'=>$e->getMessage().'<br> Line No. '.$e->getLine());
	}	
 }
 
 function check_default_prnttheme_info($data){
	$response=array();  
	$usrprefnn_tbl = $this->company_id.'_usr_prefnn';
    $builder = $this->db->table($usrprefnn_tbl);
	$builder->where('uuid', $data['uuid']);
	$builder->where('usr_config_id', $data['usr_config_id']);
    $builder->whereIn('usr_config_value',$data['usr_config_value']);
    $response = $builder->countAllResults();    
	 return $response;
  }
  
 function isvoucher_autobillno($voucher_type_id,$comp_vch_series_id){
	$cmpvchseri_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id'); 
	$vchseriesa_tbl = $this->company_id.'_vchseriesa_'.$this->session->get('ses_comp_fy_id');
	$vhtxnconso_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	
   $builder = $this->db->table($cmpvchseri_tbl.' cmpvchseri');	
   $builder->join($vchseriesa_tbl.' vchseriesa', 'vchseriesa.comp_vch_series_id=cmpvchseri.comp_vch_series_id');
   $builder->where('cmpvchseri.voucher_type_id', $voucher_type_id);   
   $builder->where('cmpvchseri.comp_vch_method', 1);
   if($comp_vch_series_id>0)
	 $builder->where('cmpvchseri.comp_vch_series_id', $comp_vch_series_id); 
     $builder->orderBy('cmpvchseri.comp_vch_series_id');
     $response = $builder->get()->getRowArray();
    if($response)
      return 1;
     else 
	  return 0;	 
 }
 function get_bank_info($voucher_txn_id){
   $cmpvchseri_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
   $vhtxnconso_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
   $cmpbankmst_tbl = $this->company_id.'_cmpbankmst_'.$this->session->get('ses_comp_fy_id');
   $builder        = $this->db->table($cmpbankmst_tbl.' bank');
   $builder->select('bank.bo_id,bank.comp_bank_name,bank.comp_bank_acc_no,bank.comp_bank_ifsc,bank.comp_bank_adrs');
   $builder->join($cmpvchseri_tbl.' series','series.comp_bank_id=bank.comp_bank_id');
   $builder->join($vhtxnconso_tbl.' conso','conso.comp_vch_series_id=series.comp_vch_series_id');
   $builder->where('conso.voucher_txn_id',$voucher_txn_id);
   $response = $builder->get()->getRowArray();  
   $result = array();
   if($response){
	
		   $result['comp_bank_name']   = $response['comp_bank_name'];
		   $result['comp_bank_acc_no'] = $response['comp_bank_acc_no'];
		   $result['comp_bank_ifsc']   = $response['comp_bank_ifsc'];
		   $result['comp_bank_adrs']   = $response['comp_bank_adrs'];
		  
   }
   else{
		   $result['comp_bank_name']   = '';
		   $result['comp_bank_acc_no'] = '';
		   $result['comp_bank_ifsc']   = '';
		   $result['comp_bank_adrs']   = '';  
		  }
   
   return $result;
 }
 function reset_billref_no($voucher_type_id){
  $vhtxnconso_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
  $this->db->table($vhtxnconso_tbl)
				->where('voucher_type_id',$voucher_type_id)
				->update(['vch_bill_ref_no' => 0]);		 
	 
 }
 function get_billno_format($voucher_type_id,$comp_vch_series_id,$voucher_date,$format_counter=0,$counter=''){
	$cmpvchseri_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id'); 
	$vchseriesa_tbl = $this->company_id.'_vchseriesa_'.$this->session->get('ses_comp_fy_id');
	$vhtxnconso_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	$comp_vch_start=$comp_vch_counter=0;
    
    $calendaer_data = fy_calender_js();
 
    $half_years    = $calendaer_data['half_years'];
    $quarters      = $calendaer_data['quarters'];
     /********** quaterly ******************/
    $quarters1        = $quarters[0]; 
	$quarters2        = $quarters[1]; 
	$quarters3        = $quarters[2]; 
	$quarters4        = $quarters[3];  
    $current_date     = strtotime(date('Y-m-d'));
	$year1start      = $quarters1['from_date'];
	$year1end        = $quarters1['to_date'];
	$qr1short_start_month= date('m',strtotime($year1start));
	$qr1short_end_month  = date('m',strtotime($year1end));	
	$qr1startend      = $qr1short_start_month.'-'.$qr1short_end_month;

	$year2start      = $quarters2['from_date'];
	$year2end        = $quarters2['to_date'];
	$qr2short_start_month= date('m',strtotime($year2start));
	$qr2short_end_month  = date('m',strtotime($year2end));	
	$qr2startend      = $qr2short_start_month.'-'.$qr2short_end_month;

	$year3start      = $quarters3['from_date'];
	$year3end        = $quarters3['to_date'];
	$qr3short_start_month= date('m',strtotime($year3start));
	$qr3short_end_month  = date('m',strtotime($year3end));	
	$qr3startend      = $qr3short_start_month.'-'.$qr3short_end_month;

	$year4start      = $quarters4['from_date'];
	$year4end        = $quarters4['to_date'];
	$qr4short_start_month= date('m',strtotime($year4start));
	$qr4short_end_month  = date('m',strtotime($year4end));	
	$qr4startend      = $qr4short_start_month.'-'.$qr4short_end_month;

    if(($current_date>=strtotime($quarters1['from_date'])) && ($current_date<=strtotime($quarters1['to_date'])) ){
	$year1_start      = $quarters1['from_date'];
	$year1_end        = $quarters1['to_date'];

	$qr_short_start_month= date('m',strtotime($year1_start));
	$qr_short_end_month  = date('m',strtotime($year1_end));		   
	$qr_long_start_month = date('M',strtotime($year1_start));
	$qr_long_end_month   = date('M',strtotime($year1_start));	
	$short_day        = date('d',strtotime($year1_start));
	$long_month       = date('M',strtotime($year1_start));
	$short_month      = date('m',strtotime($year1_start));
	}
	if(($current_date>=strtotime($quarters2['from_date'])) && ($current_date<=strtotime($quarters2['to_date'])) ){
	$year1_start      = $quarters2['from_date'];
	$year1_end        = $quarters2['to_date'];

	$qr_short_start_month= date('m',strtotime($year1_start));
	$qr_short_end_month  = date('m',strtotime($year1_end));		   
	$qr_long_start_month = date('M',strtotime($year1_start));
	$qr_long_end_month   = date('M',strtotime($year1_end));
	}
	if(($current_date>=strtotime($quarters3['from_date'])) && ($current_date<=strtotime($quarters3['to_date'])) ){
	$year1_start      = $quarters3['from_date'];
	$year1_end        = $quarters3['to_date'];

	$qr_short_start_month= date('m',strtotime($year1_start));
	$qr_short_end_month  = date('m',strtotime($year1_end));		   
	$qr_long_start_month = date('M',strtotime($year1_start));
	$qr_long_end_month   = date('M',strtotime($year1_end));
		
	}
	if(($current_date>=strtotime($quarters4['from_date'])) && ($current_date<=strtotime($quarters4['to_date'])) ){
	$year1_start      = $quarters4['from_date'];
	$year1_end        = $quarters4['to_date'];
	$qr_short_start_month= date('m',strtotime($year1_start));
	$qr_short_end_month  = date('m',strtotime($year1_end));		   
	$qr_long_start_month = date('M',strtotime($year1_start));
	$qr_long_end_month   = date('M',strtotime($year1_end));	

	}
	 /********** quaterly ******************/
   
   $short_start_year    = date('y',strtotime($calendaer_data['from_date']));
   $short_end_year    = date('y',strtotime($calendaer_data['to_date']));
   
   $long_start_year    = date('Y',strtotime($calendaer_data['from_date']));
   $long_end_year    = date('Y',strtotime($calendaer_data['to_date']));
   
   
   $builder = $this->db->table($cmpvchseri_tbl.' cmpvchseri');	
   $builder->join($vchseriesa_tbl.' vchseriesa', 'vchseriesa.comp_vch_series_id=cmpvchseri.comp_vch_series_id');
   $builder->where('cmpvchseri.voucher_type_id', $voucher_type_id);   
   $builder->where('cmpvchseri.comp_vch_method', 1);
   if($comp_vch_series_id>0)
	 $builder->where('cmpvchseri.comp_vch_series_id', $comp_vch_series_id); 
     $builder->orderBy('cmpvchseri.comp_vch_series_id');
     $response = $builder->get()->getRowArray();
	$prefix_label = $suffix_label='';
     if($response){		 
	     $comp_vch_renum_freq = $response['comp_vch_renum_freq'];
		 $comp_vch_prefix     = $response['comp_vch_prefix'];
		 if($response['comp_vch_prefix']!=''){
		   $comp_vch_prefixs   = substr($response['comp_vch_prefix'],0,-1);
		   $prefix_seperrator  = substr($response['comp_vch_prefix'], -1);
		 }
		 else{
			$comp_vch_prefixs = $prefix_seperrator='';
		 }
		 
		 if($response['comp_vch_suffix']!=''){
			   $suffix_seperrator = mb_substr($response['comp_vch_suffix'],0,1); // first character of string
		  }else 
			  $suffix_seperrator='';
		 
		 if($comp_vch_renum_freq=='1'){// yearly
			if($comp_vch_prefixs=='YY-YY'){
				$prefix_label = $short_start_year.'-'.$short_end_year.$prefix_seperrator;
			}
           else if($comp_vch_prefixs=='YYYY-YY'){
			 $prefix_label = $long_start_year.'-'.$short_end_year.$prefix_seperrator;	
			}
			else if($comp_vch_prefixs=='YY/YY'){
			 $prefix_label = $short_start_year.'/'.$short_end_year.$prefix_seperrator;		
			}
			else if($comp_vch_prefixs=='YYYY/YY'){
			 $prefix_label = $long_start_year.'/'.$short_end_year.$prefix_seperrator;	
			}
			else{
				$prefix_label =  $comp_vch_prefix;
			}	
		 }
		 else if($comp_vch_renum_freq=='2'){// half yearly
		    $year1_start = $half_years[0]['from_date'];
			$year1_end   = $half_years[0]['to_date'];
			
			$year2_start = $half_years[1]['from_date'];
			$year2_end = $half_years[1]['to_date'];
			
			$short_start_month  = date('m',strtotime($year1_start));
		    $short_end_month    = date('m',strtotime($year2_start));		   
		    $long_start_month   = date('M',strtotime($year1_start));
		    $long_end_month     = date('M',strtotime($year2_start));
   
			
			if($comp_vch_prefixs=='MM-MM'){
				$prefix_label = $short_start_month.'-'.$short_end_month.$prefix_seperrator;
			}
           else if($comp_vch_prefixs=='MMM-MMM'){
			 $prefix_label = $long_start_month.'-'.$long_end_month.$prefix_seperrator;	
			}
		  else if($comp_vch_prefixs=='MM/MM'){
			 $prefix_label = $short_start_month.'/'.$short_end_month.$prefix_seperrator;		
			}
		  else	if($comp_vch_prefixs=='MMM/MMM'){
			 $prefix_label = $long_start_month.'/'.$long_end_month.$prefix_seperrator;	
			}
		  else{
				$prefix_label =  $comp_vch_prefix;
			}		
		 }
		 else if($comp_vch_renum_freq=='3'){// Quaterly
		 if($comp_vch_prefixs=='MM-MM'){
				$prefix_label = $qr_short_start_month.'-'.$qr_short_end_month.$prefix_seperrator;	
			}
           else if($comp_vch_prefixs=='MMM-MMM'){
			 $prefix_label = $qr_long_start_month.'-'.$qr_long_end_month.$prefix_seperrator;		
			}
		  else if($comp_vch_prefixs=='MM/MM'){
			 $prefix_label = $qr_short_start_month.'/'.$qr_short_end_month.$prefix_seperrator;			
			}
		  else	if($comp_vch_prefixs=='MMM/MMM'){
			 $prefix_label = $qr_long_start_month.'/'.$qr_long_end_month.$prefix_seperrator;		
			}		  
		  else if($comp_vch_prefixs=='Q1/Q2/Q3/Q4'){
			 $prefix_label = $qr1startend.'/'.$qr2startend.'/'.$qr3startend.'/'.$qr4startend.$prefix_seperrator;		
			}	
		  else{
				$prefix_label = $comp_vch_prefix;
			}
		}		 
		 else if($comp_vch_renum_freq=='4'){// daily
		    $short_day = date('d',strtotime($voucher_date));
			$long_month = date('M',strtotime($voucher_date));
			$short_month = date('m',strtotime($voucher_date));
			if($comp_vch_prefixs=='DD-MMM'){
			 $prefix_label= $short_day.'-'.$long_month.$prefix_seperrator;
			}
            else if($comp_vch_prefixs=='DD/MM'){
			 $prefix_label = $short_day.'/'.$short_month.$prefix_seperrator;	
			}
			else if($comp_vch_prefixs=='DD/MMM'){
			 $prefix_label = $short_day.'/'.$long_month.$prefix_seperrator;	
			} 
			else{
				$prefix_label =  $comp_vch_prefix;
			}
		 }else if($comp_vch_renum_freq=='0' || $comp_vch_renum_freq==''){
			$prefix_label =  $comp_vch_prefix;
		 }
		 
		 
		 $comp_vch_suffix     = $response['comp_vch_suffix'];
		 if($response['comp_vch_suffix']!='')
		 $comp_vch_suffixs     = substr($response['comp_vch_suffix'],1);
	     else
		  $comp_vch_suffixs ='';
	  
		 if($comp_vch_renum_freq=='1'){ // yearly
			if($comp_vch_suffixs=='YY-YY'){
			 $suffix_label= $suffix_seperrator.$short_start_year.'-'.$short_end_year;
			}
           else if($comp_vch_suffixs=='YYYY-YY'){
			 $suffix_label = $suffix_seperrator.$long_start_year.'-'.$short_end_year;	
			}
		  else if($comp_vch_suffixs=='YY/YY'){
			 $suffix_label = $suffix_seperrator.$short_start_year.'/'.$short_end_year;		
			}
			else if($comp_vch_suffixs=='YYYY/YY'){
			 $suffix_label = $suffix_seperrator.$long_start_year.'/'.$short_end_year;	
			}	
			else{
			$suffix_label =  $comp_vch_suffix;
		    }
		 }
		 else if($comp_vch_renum_freq=='2'){// half yearly
		    $year1_start = $half_years[0]['from_date'];
			$year1_end   = $half_years[0]['to_date'];
			
			$year2_start = $half_years[1]['from_date'];
			$year2_end   = $half_years[1]['to_date'];
			
			$short_start_month  = date('m',strtotime($year1_start));
		    $short_end_month    = date('m',strtotime($year2_start));		   
		    $long_start_month   = date('M',strtotime($year1_start));
		    $long_end_month     = date('M',strtotime($year2_start));   
			
			if($comp_vch_suffixs=='MM-MM'){
				$suffix_label = $suffix_seperrator.$short_start_month.'-'.$short_end_month;
			}
           else if($comp_vch_suffixs=='MMM-MMM'){
			 $suffix_label = $suffix_seperrator.$long_start_month.'-'.$long_end_month;	
			}
			else if($comp_vch_suffixs=='MM/MM'){
			 $suffix_label = $suffix_seperrator.$short_start_month.'/'.$short_end_month;		
			}
			else if($comp_vch_suffixs=='MMM/MMM'){
			 $suffix_label = $suffix_seperrator.$long_start_month.'/'.$long_end_month;	
			}	
			else{
			$suffix_label =  $comp_vch_suffix;
		    }
		 }
		 else if($comp_vch_renum_freq=='3'){// Quaterly
		 if($comp_vch_suffixs=='MM-MM'){
				$suffix_label = $suffix_seperrator.$qr_short_start_month.'-'.$qr_short_end_month;
			}
           else if($comp_vch_suffixs=='MMM-MMM'){
			 $suffix_label = $suffix_seperrator.$qr_long_start_month.'-'.$qr_long_end_month;	
			}
		  else if($comp_vch_suffixs=='MM/MM'){
			 $suffix_label = $suffix_seperrator.$qr_short_start_month.'/'.$qr_short_end_month;		
			}
		  else	if($comp_vch_suffixs=='MMM/MMM'){
			 $suffix_label = $suffix_seperrator.$qr_long_start_month.'/'.$qr_long_end_month;	
			}		  
		  else if($comp_vch_suffixs=='Q1/Q2/Q3/Q4'){
			 $suffix_label = $suffix_seperrator.$qr1startend.'/'.$qr2startend.'/'.$qr3startend.'/'.$qr4startend;	
			}	
		  else{
				$suffix_label = $comp_vch_suffix;
			}
		}
		 else if($comp_vch_renum_freq=='4'){// daily
		    $short_day = date('d',strtotime($voucher_date));
			$long_month = date('M',strtotime($voucher_date));
			$short_month = date('m',strtotime($voucher_date));
			
			if($comp_vch_suffixs=='DD-MMM'){
			 $suffix_label= $suffix_seperrator.$short_day.'-'.$long_month;
			}
            else if($comp_vch_suffixs=='DD/MM'){
			 $suffix_label = $suffix_seperrator.$short_day.'/'.$short_month;	
			}
			else if($comp_vch_suffixs=='DD/MMM'){
			 $suffix_label = $suffix_seperrator.$short_day.'/'.$long_month;	
			} 
			else{
			$suffix_label =  $comp_vch_suffix;
		    }
		 }else if($comp_vch_renum_freq=='0' || $comp_vch_renum_freq==''){
			$suffix_label =  $comp_vch_suffix;
		 }
		
	     $comp_vch_start      = $response['comp_vch_start'];
		 $comp_vch_counter    = $response['comp_vch_start'];
		 $comp_vch_no_padding = $response['comp_vch_no_padding'];
		 $comp_vch_no_length  = $response['comp_vch_no_length'];
		 // fix numeric number upto this number
		 
         // 1= yearly, 2=half yearly,3=quaterly,4=daily		 
		
		$conso_builder = $this->db->table($vhtxnconso_tbl); 
		$conso_builder->select('MAX(CAST(vch_bill_ref_no AS UNSIGNED)) as max_bill_ref_no');
		$conso_builder->where('voucher_type_id',$voucher_type_id);
		$conso_builder->where('comp_vch_series_id',$comp_vch_series_id);		
		
		if($comp_vch_renum_freq=='1'){
		 $conso_builder->where("DATE_FORMAT(voucher_date,'%Y')", date('Y',strtotime($voucher_date)));	
		}
		if($comp_vch_renum_freq=='2'){
		  foreach($half_years as $hdates){	
		    if($voucher_date>=$hdates['from_date'] && $voucher_date<=$hdates['to_date'])
				$conso_builder->where('(DATE(voucher_date) >="'.$hdates['from_date'].'" AND DATE(voucher_date) <="'.$hdates['to_date'].'" )');	
		  }
		}
       if($comp_vch_renum_freq=='3'){
		  foreach($quarters as $qdates){	
		    if($voucher_date>=$qdates['from_date'] && $voucher_date<=$qdates['to_date'])
				$conso_builder->where('(DATE(voucher_date) >="'.$qdates['from_date'].'" AND DATE(voucher_date) <="'.$qdates['to_date'].'" )');	
		    }
		}
	    if($comp_vch_renum_freq=='4'){
		 $conso_builder->where('DATE(voucher_date)', date('Y-m-d',strtotime($voucher_date)));	
		 }
		$conso_builder->orderBy('voucher_txn_id','DESC');
		
		$result =$conso_builder->get()->getRowArray();
		
		$max_bill_ref_no = $result['max_bill_ref_no'];
		if($max_bill_ref_no){
		  $comp_vch_start =  $result['max_bill_ref_no']+1;
		  $comp_vch_counter= $result['max_bill_ref_no']+1;
		}
		if($comp_vch_start >0){						
		 if($counter=='' && $format_counter >0){
			
				 $comp_vch_no_padding_label='';
				if(strlen(trim($comp_vch_start-1))!=$comp_vch_no_length){
					for($i=0;$i<$comp_vch_no_length;$i++){
					  $comp_vch_no_padding_label .=$comp_vch_no_padding;	
					}
				//$compvch_start     = $comp_vch_no_padding_label.trim($format_counter);	
				}
				//else
				$compvch_start     = sprintf("%'.0".trim($comp_vch_no_length)."d", trim($format_counter));
				
			
				$saved_seriesformat   = trim($prefix_label).$compvch_start.trim($suffix_label); 
		 }
	     else {
			    $comp_vch_no_padding_label='';
				if(strlen(trim($comp_vch_start))!=$comp_vch_no_length){
					for($i=0;$i<$comp_vch_no_length;$i++){
					  $comp_vch_no_padding_label .=$comp_vch_no_padding;	
					}
				//$comp_vch_start     = $comp_vch_no_padding_label.trim($comp_vch_start);	
				}
				//else
				$comp_vch_start     = sprintf("%'.0".trim($comp_vch_no_length)."d", trim($comp_vch_start));
			
				$saved_seriesformat   = trim($prefix_label).$comp_vch_start.trim($suffix_label); 
				
		     }
		}
   if($format_counter >0){ // show bill number with configuration  like ADD/33/FFF 
	  return $saved_seriesformat;
   }
   else 
	 return  $comp_vch_counter;	 
 
	  }
		 
 }
 
 function PurchaseBillNoDuplicate($billno,$voucher_txn_id=0){
	$gstroutsup_tbl = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
	$builder = $this->db->table($gstroutsup_tbl);
	$builder->where('LOWER(inwsup_bill_ref_no)', strtolower(trim($billno)));
	if($voucher_txn_id>0)
	$builder->where('voucher_txn_id !=',$voucher_txn_id);	
	$response = $builder->get()->getRowArray();	
	if($response)
		return "1"; //duplicate exists
	else 
		return "0";//fresh
 }
 
 function BillNoDuplicate($billno,$voucher_txn_id=0){
	$gstroutsup_tbl = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
	$builder = $this->db->table($gstroutsup_tbl);
	$builder->where('LOWER(outsup_bill_ref_no)', strtolower(trim($billno)));
	if($voucher_txn_id>0)
	$builder->where('voucher_txn_id !=',$voucher_txn_id);	
	$response = $builder->get()->getRowArray();	
	if($response)
		return "1"; //duplicate exists
	else 
		return "0";//fresh
 }
 
 function BillNoRequired($voucher_type_id,$comp_vch_series_id){
	$cmpvchseri_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id'); 
	$vchseriesm_tbl = $this->company_id.'_vchseriesm_'.$this->session->get('ses_comp_fy_id');
	
	$builder = $this->db->table($cmpvchseri_tbl.' cmpvchseri');
	$builder->join($vchseriesm_tbl.' vchseriesm', 'vchseriesm.comp_vch_series_id=cmpvchseri.comp_vch_series_id');
	$builder->where('cmpvchseri.voucher_type_id', $voucher_type_id);
	$builder->where('cmpvchseri.comp_vch_series_id', $comp_vch_series_id);
	$builder->where('cmpvchseri.comp_vch_method','0');
	$response = $builder->get()->getRowArray();	
	
	$ajax_response =false;
	if($response){
		$comp_vch_blank = $response['comp_vch_blank'];
		if($comp_vch_blank=="0")
		     $ajax_response = true;
	}

	return $ajax_response;
 }
 
 function AutoSeriesExists($voucher_type_id,$series_format,$series_id){
	$cmpvchseri_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id'); 
	$vchseriesa_tbl = $this->company_id.'_vchseriesa_'.$this->session->get('ses_comp_fy_id');	
	$builder        = $this->db->table($cmpvchseri_tbl.' cmpvchseri');
	$builder->join($vchseriesa_tbl.' vchseriesa', 'vchseriesa.comp_vch_series_id=cmpvchseri.comp_vch_series_id');
	$builder->where('cmpvchseri.voucher_type_id', $voucher_type_id);	
	if($series_id>0){
	 $builder->where('cmpvchseri.comp_vch_series_id !=',$series_id);	
	}
	$response      = $builder->get()->getResultArray();	
	
	$series_exists = 0;
	if($response){
		foreach($response as $row){
		if($row['comp_vch_no_padding'] >0)
		$comp_vch_start     = sprintf("%'.0".trim($row['comp_vch_no_padding'])."d", trim($row['comp_vch_start']));
	    else
		$comp_vch_start     = trim($row['comp_vch_start']);	
	
	
		$saved_seriesformat = trim($row['comp_vch_prefix']).$comp_vch_start.trim($row['comp_vch_suffix']);
		if(strtolower(trim($saved_seriesformat))==strtolower(trim($series_format)))
		     $series_exists=$series_exists+1;
	    }
	  }
	  $ajax_response = ['series_exists'=>$series_exists];
	return $ajax_response;
  } 
 
 function auto_roundoff_create($sundry_nature,$type){
	 $bo_id = $this->session->get('ses_boid');
	 $billsundry_master_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
	 if($type=="add"){
	 $billsundry_data2   = [
						'bill_sundry_name'     => 'Round off (+)',
						'bill_sundry_alias'    => 'Round off (+)',
						'sundry_print_name'    => 'Round off (+)',
						'sundry_nature'        => $sundry_nature,
						'sundry_def_value'     => 0,
						'sundry_type'          => 0,
						'sundry_calc_base'     => 0,
						'sundry_calc_fed'      => 0,
						'acc_grp_id'           => 19,
						'acc_grp_parent_id'    => 0,
						'bo_id'                => $bo_id						
					  ];
	      }
	if($type=="minus"){
	 $billsundry_data2   = [
						'bill_sundry_name'     => 'Round off (-)',
						'bill_sundry_alias'    => 'Round off (-)',
						'sundry_print_name'    => 'Round off (-)',
						'sundry_nature'        => $sundry_nature,
						'sundry_def_value'     => 0,
						'sundry_type'          => 0,
						'sundry_calc_base'     => 0,
						'sundry_calc_fed'      => 0,
						'acc_grp_id'           => 19,
						'acc_grp_parent_id'    => 0,
						'bo_id'                => $bo_id						
					  ];
	      }	  
			$check2 = $this->db->table($billsundry_master_tbl)->where('LOWER(bill_sundry_name)', strtolower(trim($billsundry_data2['bill_sundry_name'])))
                                            	      ->orWhere('LOWER(bill_sundry_alias)', strtolower(trim($billsundry_data2['bill_sundry_name'])))
                                             	      ->get()->getRowArray();		  
			if(!$check2){													
			 $response = $this->db->table($billsundry_master_tbl)->insert($billsundry_data2);	 
			 $billsundry_id = $this->db->insertID();
			 
			 $mst_base_id = $this->create_mst_base_id($billsundry_id,'billsundry');
	         $this->db->table($billsundry_master_tbl)
				->where('bill_sundry_id',$billsundry_id)
				->update(['mst_base_id' => $mst_base_id]);
			
			$ERPtables = new ERPtables($this->company_id,$this->session->get('ses_comp_fy_id'));
     	    $ERPtables->bill_sundry_txn_tables($billsundry_id);
			return $billsundry_id;
			}else{
			  return $check2['bill_sundry_id'];	
			}
	 
 }
 
 function transporter_info($trnid,$voucher_txn_id=0){
	$gsttptmstn_tbl  = $this->company_id.'_gsttptmstn_'.$this->session->get('ses_comp_fy_id');	
    $ewbpartbdt_tbl = $this->company_id.'_ewbpartbdt_'.$this->session->get('ses_comp_fy_id');	
    $builder = $this->db->table($gsttptmstn_tbl.' gstmst');
	$builder->join($ewbpartbdt_tbl.' partb', 'partb.gsttpt_id=gstmst.gsttpt_id');
	$builder->where('gstmst.gsttpt_id', $trnid);
	if($voucher_txn_id>0)	
	 $builder->where('partb.voucher_txn_id', $voucher_txn_id);	
	$response = $builder->get()->getRowArray();
	$final = array();
	if($response){
	    $transport_doc_date = date("d/m/Y",strtotime($response["trans_doc_date"]));
		$final=array("gsttpt_id"=>$response["gsttpt_id"],"gsttpt_enrl_id"=>$response["gsttpt_enrl_id"],"gsttpt_name"=>$response["gsttpt_name"],"gsttpt_gstin"=>$response["gsttpt_gstin"],"trans_veh_no"=>$response["trans_veh_no"],"trans_veh_type"=>$response["trans_veh_type"],"trans_mode"=>$response["trans_mode"],
					"trans_doc_no"=>$response["trans_doc_no"],"trans_doc_date"=>$transport_doc_date,"trans_dist"=>$response["trans_dist"],
					);
		
	 }
	 	 
	 return $final;
	}
	
 function upate_transporter_master($gsttpt_id,$data,$voucher_txn_id){
	$stcode     = ltrim($data['state_code'],"0");
	$state_info =  $this->aicountly_db->table('aicountly_stateslist_univdb')->select('state_id,state_name,country_id, state_code')->where('state_code',$stcode)->get()->getRowArray();
    if($data["transport_doc_date"])
   		$transport_doc_date = date("Y-m-d",strtotime($data["transport_doc_date"]));
	else 
		$transport_doc_date = "";
	
	$tpt_update_date = $veh_update_date = date('Y-m-d');
	$ewbpartbdt_tbl = $this->company_id.'_ewbpartbdt_'.$this->session->get('ses_comp_fy_id');	
    $ewbpartbdt_data = array("trans_veh_no"=>$data['vehicle_no'],
							 "trans_veh_type"=>$data["vehicle_type"],"trans_mode"=>$data["transport_mode"],
							 "trans_doc_no"=>$data["transporter_doc_no"],"trans_doc_date"=>$transport_doc_date,
							 "trans_dist"=>$data["transport_distance"],"trans_frm_place"=>$state_info["state_name"],"trans_frm_state_code"=>$data['state_code'],
							 "tpt_update_date"=>$tpt_update_date,"veh_update_date"=>$veh_update_date);
    $this->db->table($ewbpartbdt_tbl)->where("voucher_txn_id",$voucher_txn_id)->where("gsttpt_id",$gsttpt_id)->update($ewbpartbdt_data);	
	}
	
	
	
	
	function transport_single_info($voucher_txn_id){
	$ewbpartbdt_tbl = $this->company_id.'_ewbpartbdt_'.$this->session->get('ses_comp_fy_id');	
    $response = $this->db->table($ewbpartbdt_tbl)->select('gsttpt_id')->where('voucher_txn_id',$voucher_txn_id)->get()->getRowArray();
	 if($response)
	 return $response['gsttpt_id'];
     else
	  return "";
	}
	
	
    function add_transporter_master($data){
	$gsttptmstn_tbl  = $this->company_id.'_gsttptmstn_'.$this->session->get('ses_comp_fy_id');	
    $gsttptmstn_data = array("gsttpt_name"=>$data['transporter_name'],"gsttpt_gstin"=>$data["gstin_id"],
	                          "gsttpt_enrl_id"=>$data["transporter_id"]);
	$exists = $this->db->table($gsttptmstn_tbl)->where('LOWER(gsttpt_name)', strtolower($data['transporter_name']))->countAllResults();						  
	if($exists==0){
	   $this->db->table($gsttptmstn_tbl)->insert($gsttptmstn_data);	
	   $gsttpt_id = $this->db->insertID();
       return 	$gsttpt_id;
	  }
	  else{
	   return 	"0";	
	  }
	}
	function exists_transporter_master($transporter_name){
	 $gsttptmstn_tbl  = $this->company_id.'_gsttptmstn_'.$this->session->get('ses_comp_fy_id');	
     return $this->db->table($gsttptmstn_tbl)->where('LOWER(gsttpt_name)', strtolower($transporter_name))->countAllResults();						  
	}
	
	function get_ewbmstcons_data($ewb_id){
		$conso_ewb_id =0;		
		$ewbmstcons_tbl   = $this->company_id.'_ewbmstcons_'.$this->session->get('ses_comp_fy_id');
	    $conso_ewb_idinfo = $this->db->table($ewbmstcons_tbl)->where('ewb_id',$ewb_id)->get()->getRowArray();
	    if($conso_ewb_idinfo){
		  $conso_ewb_id = $conso_ewb_idinfo['conso_ewb_id'];
	  }
	 return $conso_ewb_id;	
	}
	
	function add_ewbpartbdt($data){
		$ewbmstreqn_tbl = $this->company_id.'_ewbmstreqn_'.$this->session->get('ses_comp_fy_id');	
        $ewbmstcons_tbl = $this->company_id.'_ewbmstcons_'.$this->session->get('ses_comp_fy_id');	
        $bo_id            = $this->session->get('ses_boid');	
   
		if(isset($data['trans_frm_state_code'])){
		 $state_code = ltrim($data['trans_frm_state_code'],'0');
		 $state_info =  $this->aicountly_db->table('aicountly_stateslist_univdb')->select('state_id,state_name,country_id, state_code')->where('state_code',$state_code)->get()->getRowArray();
		
		 $trans_frm_place      = $state_info["state_name"];
		 $trans_frm_state_code = $data['trans_frm_state_code'];
		} else{
			$trans_frm_place ='';
			$trans_frm_state_code ='';
		}
	
if($data['ewb_id']==0){	
		
	$ewb_id=$conso_ewb_id=0;	
    $ewb_idinfo       = $this->db->table($ewbmstreqn_tbl)->where('voucher_txn_id',$data["voucher_txn_id"])->where('bo_id',$bo_id)->get()->getRowArray();
	if($ewb_idinfo){
      $ewb_id = $ewb_idinfo['ewb_id'];		
	  $conso_ewb_idinfo = $this->db->table($ewbmstcons_tbl)->where('ewb_id',$ewb_id)->get()->getRowArray();
	  if($conso_ewb_idinfo){
		$conso_ewb_id =   $conso_ewb_idinfo['conso_ewb_id'];
	  }
   	}
}else
	$ewb_id = $data['ewb_id'];
if(isset($data['conso_ewb_id']) && $data['conso_ewb_id'] >0)
    $conso_ewb_id = $data['conso_ewb_id'];
 else{
	 $conso_ewb_idinfo = $this->db->table($ewbmstcons_tbl)->where('ewb_id',$ewb_id)->get()->getRowArray();
	  if($conso_ewb_idinfo){
		$conso_ewb_id =   $conso_ewb_idinfo['conso_ewb_id'];
	  } else
		  $conso_ewb_id =0;
     }
	$ewbpartbdt_tbl = $this->company_id.'_ewbpartbdt_'.$this->session->get('ses_comp_fy_id');	
    $ewbpartbdt_data = array("gsttpt_id"=>$data['gsttpt_id'],"ewb_id"=>$ewb_id,"conso_ewb_id"=>$conso_ewb_id,"trans_veh_no"=>$data['trans_veh_no'],
							 "trans_veh_type"=>$data["trans_veh_type"],"trans_mode"=>$data["trans_mode"],
							 "trans_doc_no"=>$data["trans_doc_no"],"trans_doc_date"=>$data["trans_doc_date"],"trans_rsn_code"=>"",
							 "trans_rsn_rem"=>"","trans_dist"=>$data["trans_dist"],"trans_frm_place"=>$trans_frm_place,
							 "trans_frm_state_code"=>$trans_frm_state_code,"voucher_txn_id"=>$data["voucher_txn_id"]);
    $this->db->table($ewbpartbdt_tbl)->insert($ewbpartbdt_data);	
	
	}
	
	function update_ewbpartbdt($gsttpt_id,$voucher_txn_id,$data){
	  if(isset($data['trans_frm_state_code'])){
		 $state_code = ltrim($data['trans_frm_state_code'],'0'); 
		 $state_info =  $this->aicountly_db->table('aicountly_stateslist_univdb')->select('state_id,state_name,country_id, state_code')->where('state_code',$state_code)->get()->getRowArray();
		 $trans_frm_place      = $state_info["state_name"];
		 $trans_frm_state_code = $data['trans_frm_state_code'];
		} else{
			$trans_frm_place ='';
			$trans_frm_state_code ='';
		}	
		
	$ewbpartbdt_tbl = $this->company_id.'_ewbpartbdt_'.$this->session->get('ses_comp_fy_id');	
    $ewbpartbdt_data = array("trans_veh_no"=>$data['vehicle_no'],
							 "trans_veh_type"=>$data["vehicle_type"],"trans_mode"=>$data["transport_mode"],
							 "trans_doc_no"=>$data["transporter_doc_no"],"trans_doc_date"=>$data["transport_doc_date"],
							 "trans_rsn_rem"=>"","trans_dist"=>$data["transport_distance"],"trans_frm_place"=>$trans_frm_place,"trans_frm_state_code"=>$trans_frm_state_code);
    $this->db->table($ewbpartbdt_tbl)->where("voucher_txn_id",$voucher_txn_id)->where("gsttpt_id",$gsttpt_id)->update($ewbpartbdt_data);	
	
	}
	
	function transporter_dropdown(){
		$gsttptmstn_tbl =  $this->company_id.'_gsttptmstn_'.$this->session->get('ses_comp_fy_id');	 
		$data =  $this->db->table($gsttptmstn_tbl)->orderBy('gsttpt_name')->get()->getResultArray();
		$final_result      = array();
		$final_result['']  = 'Choose';
		if($data){
			foreach($data as $row){
				$final_result[$row['gsttpt_id']] =$row['gsttpt_name'];			   
			}
		}
		$final_result['0']  = 'Add New';
		return $final_result;	
    }
	
    function create_mst_base_id($id,$type)
    {
    	$uuid  = $this->session->get('comp_uuid');
  		$UUIDtables = new UUIDtables($uuid,$this->company_id,$this->session->get('ses_comp_fy_id'));
  		$mst_base_id = $UUIDtables->get_mst_base_id($id,$type);
  		return $mst_base_id;
    }

    function delete_comp_fy_mst_map($id,$type)
    {
    	$uuid  = $this->session->get('comp_uuid');
  		$UUIDtables = new UUIDtables($uuid,$this->company_id,$this->session->get('ses_comp_fy_id'));
  		$mst_base_id = $UUIDtables->delete_comp_fy_mst_map_by_id($id,$type);
    }
   
   function get_local_tax_catg_info($tax_catg_id){
	   $cmptaxcatm_tbl = $this->company_id.'_cmptaxcatm_'.$this->session->get('ses_comp_fy_id');
	   $response       = $this->db->table($cmptaxcatm_tbl)
							->where('cmp_tax_cat_id', $tax_catg_id)
							->where('comp_id', $this->company_id)->get()->getRowArray();
		return $response;	
    }
   
   function get_tax_master_info($tax_catg_id){
	 $final_response = array();  
	 $response = $this->dberpunvrsl->table('aictlyerp_taxcatmstn_univdb')
				->where('tax_cat_id', $tax_catg_id)
				->where('status', '1')->get()->getRowArray();
	 if($response){
      $tax_cat_type   = trim($response['tax_cat_type']);
	  $tax_cat_basis  = $response['tax_cat_basis'];
	  // get tax detauils from table tax_cat_type 
	  $row = $this->dberpunvrsl->table('aictlyerp_'.$tax_cat_type.'_univdb')
				  ->where('tax_cat_id', $tax_catg_id)->get()->getRowArray();
      if($row){		  
		 if($tax_cat_type=='gsttcscatd'){
			$final_response=array("tax_basis"=>$tax_cat_basis,
			                      "tax_cat_act"=>$response['tax_cat_act'],
								  "tax_cat_name"=>$response['tax_cat_name'],
								  "tax_cat_section"=>$response['tax_cat_section'],
								  "tax_cat_basis"=>$response['tax_cat_basis'],
								  "tax_cat_type"=>$response['tax_cat_type'],
			                      "igst_rate"=>$row['tax_cat_igst_rate'],
								  "cess_rate"=>"",
								  "wef"=>$row['tax_cat_wef']); 			 
		 }
		if($tax_cat_type=='gsttaxcatd'){
			$final_response=array("tax_cat_act"=>$response['tax_cat_act'],
								  "tax_cat_name"=>$response['tax_cat_name'],
								  "tax_cat_section"=>$response['tax_cat_section'],
								  "tax_cat_basis"=>$response['tax_cat_basis'],
								  "tax_cat_type"=>$response['tax_cat_type'],
								  "tax_basis"=>$tax_cat_basis,"igst_rate"=>$row['tax_cat_igst'],
								  "cess_rate"=>$row['tax_cat_cess'],"wef"=>$row['tax_cat_wef'],
								  "tax_short_code"=>$row['tax_short_code']); 			 
		 }		
		if($tax_cat_type=='gstwhtcatd'){
			$final_response=array("tax_cat_act"=>$response['tax_cat_act'],
								  "tax_cat_name"=>$response['tax_cat_name'],
								  "tax_cat_section"=>$response['tax_cat_section'],
								  "tax_cat_basis"=>$response['tax_cat_basis'],
								  "tax_cat_type"=>$response['tax_cat_type'],
								  "tax_basis"=>$tax_cat_basis,"igst_rate"=>$row['tax_cat_igst_rate'],
								  "cess_rate"=>$row['tax_cat_cess_rate'],"wef"=>$row['tax_cat_wef']); 			 
		 }
		if($tax_cat_type=='ittcscatdt'){
			$final_response=array("tax_cat_act"=>$response['tax_cat_act'],
								  "tax_cat_name"=>$response['tax_cat_name'],
								  "tax_cat_section"=>$response['tax_cat_section'],
								  "tax_cat_basis"=>$response['tax_cat_basis'],
								  "tax_cat_type"=>$response['tax_cat_type'],
								  "tax_basis"=>$tax_cat_basis,"igst_rate"=>"","cess_rate"=>"","wef"=>$row['tax_cat_wef']);
		 }	
		if($tax_cat_type=='itwhtcatdt'){
			$final_response=array("tax_cat_act"=>$response['tax_cat_act'],
								  "tax_cat_name"=>$response['tax_cat_name'],
								  "tax_cat_section"=>$response['tax_cat_section'],
								  "tax_cat_basis"=>$response['tax_cat_basis'],
								  "tax_cat_type"=>$response['tax_cat_type'],
								  "tax_basis"=>$tax_cat_basis,"igst_rate"=>"","cess_rate"=>"","wef"=>$row['tax_cat_wef']);
		 } 
	   }	
	}	  
    return $final_response;
   }
   
   /******   used in printing voucher in pdf or preview in browser ***/
   
   function GetDefaultPrintingTemplate($usr_config_id){
	$uuid =  $this->session->get('uuid');   
	$usrprefnn_tbl = $this->company_id.'_usr_prefnn';
	$response = $this->db->table($usrprefnn_tbl)->select('usr_config_id,usr_config_value')
	       ->where('uuid', $uuid)
		   ->where('usr_config_id', $usr_config_id)
		   ->get()->getRowArray();  
  if($response)		   
	  return $response['usr_config_value']; 
   else
	  return false; 
    }
	
	function FetchCompUUId(){
		$response = $this->aicountly_db->table('aicountly_compidgenr_univdb')->select('uuid')->where('comp_id',$this->company_id)->get()->getRowArray();
		return $response['uuid'];
	}
  
   function get_print_config_info($user_config_id,$vch_series_id){
	   $prntconfig_id =0;
	   $prntconfig_tbl  = $this->company_id.'_prntconfig_'.$this->session->get('ses_comp_fy_id');
	   $response        = $this->db->table($prntconfig_tbl)->select('prntconfig_id')->where('comp_id',$this->company_id)->where('vch_series_id',$vch_series_id)->where('usr_config_id',$user_config_id)
	                     ->get()->getRowArray(); 
	   if($response)					 
	   $prntconfig_id  = $response['prntconfig_id']; 				 
        
	   return $prntconfig_id; 
   }
   
  function GetPrintLabelVal($usr_config_id,$label_id,$vch_series_id,$fldvalue=''){
	  $prntdesign_tbl  = $this->company_id.'_prntconfig_'.$this->session->get('ses_comp_fy_id');
	  $response        = $this->db->table($prntdesign_tbl)->select('prntdsg_style,prntdsg_value')->where('vch_series_id',$vch_series_id)->where('usr_config_id',$usr_config_id)->where('erpprevaln_label_id',$label_id)
	                     ->get()->getRowArray();   
						 
	  if($response){ 
	   if($fldvalue==1)
		   return $response['prntdsg_value']; 
		   else{
			   $styletext    = $response['prntdsg_style'];
			   $styletext_val = str_replace('"', '', $styletext); 
			return $styletext_val; 
		   }
	  }
     else
	  return "";	 
   }

    //-------------------------------------------------ADD METHODS START
   
	
    function add_voucher_cons_data($data,$VchOthrBo=0){
		$comp_vch_cons_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
		
		 if($this->session->get('ses_boid')!='' && $VchOthrBo==0)
			$bo_id =  $this->session->get('ses_boid');
		  else 
			$bo_id =$VchOthrBo;
		$data['bo_id'] = $bo_id;
		$this->db->table($comp_vch_cons_tbl)->insert($data);
		//echo $this->db->getlastquery();
		return $this->db->insertID();	
	}

    function add_company_transaction($data)
    {
         $comp_txn_master_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
         $this->db->table($comp_txn_master_tbl)->insert($data);
         
         return $this->db->insertID();
    }
    
	function pos_state_info($state_code){
	return $this->aicountly_db->table('aicountly_stateslist_univdb')->select('state_code,state_id,state_name,country_id')->where('state_code', $state_code)->get()->getRowArray();		
		
	}
	
	function party_gst_info($party_id){
	 // fetch party gstin info 
	 $acctgstmst_tbl = $this->company_id.'_acctgstmst_'.$this->session->get('ses_comp_fy_id'); 	
	 $data =  $this->db->table($acctgstmst_tbl)->where('acc_id',$party_id)->get()->getRowArray(); 
	 
	 $acctaddmst_tbl = $this->company_id.'_acctaddmst_'.$this->session->get('ses_comp_fy_id');
     $stedata = $this->db->table($acctaddmst_tbl)->select('acc_state')->where('acc_id', $party_id)->get()->getRowArray();
     if($stedata){
		$state_data = $this->aicountly_db->table('aicountly_stateslist_univdb')->select('state_code,state_id,country_id')->where('state_id', $stedata['acc_state'])->get()->getRowArray();
        if($state_data){
		  $country_id = $state_data['country_id'];			 
		  $country_data = $this->aicountly_db->table('aicountly_countrylst_univdb')->select('countryid,countryname')->where('countryid', $country_id)->get()->getRowArray();
          $country_name = $country_data['countryname'];
		
		  $statecode  = sprintf( '%02d', $state_data['state_code']);
		}
	    else {
          $statecode ='';
		  $country_name='';
		}
	  }
     else{ 
        $statecode ='';		 
		$country_name='';
	 }
		
	 if($data){	 		 
	   $final_array = array("acc_gstin"=>$data['acc_gstin'],"acc_legal_name"=>$data['acc_legal_name'],"acc_trade_name"=>$data['acc_trade_name'],"statecode"=>$statecode,"country"=>$country_name);	 
	 }else{
	   $final_array = array("acc_gstin"=>"-","acc_trade_name"=>"-","acc_legal_name"=>"-","statecode"=>"-","country"=>"-");
	 }	 
	 return $final_array;	
	}
	
	function get_cgstinmastr_info(){
	 $gstinmastr_tbl = $this->company_id.'_gstinmastr_'.$this->session->get('ses_comp_fy_id');       
     $response       = $this->db->table($gstinmastr_tbl)->where('comp_id',$this->company_id)->where('bo_id', $this->session->get('ses_boid'))->get()->getRowArray(); 
	 return $response;
	}
	
	function billfrm_info(){
	 $country_name="";
	 $response= array();
	 $gstinmastr_tbl = $this->company_id.'_gstinmastr_'.$this->session->get('ses_comp_fy_id');       
     $response =  $this->db->table($gstinmastr_tbl)->where('comp_id',$this->company_id)->where('bo_id', $this->session->get('ses_boid'))->get()->getRowArray(); 
	 if($response)
	 {
		 $state_code = ltrim($response['gstin_state_code'],0); // remove leading zeros
         $state_info =  $this->aicountly_db->table('aicountly_stateslist_univdb')->select('state_id,state_name,country_id, state_code')->where('state_code',$state_code)->get()->getRowArray();
         if($state_info){
			 $country_id = $state_info['country_id'];			 
		     $country_data = $this->aicountly_db->table('aicountly_countrylst_univdb')->select('countryid,countryname')->where('countryid', $country_id)->get()->getRowArray();
             $country_name = $country_data['countryname'];
		 }
		 $response['country']=$country_name;
	 }

	
	 
	 return $response;
	 }
	
	function add_taxtype_data($taxtype_id,$ewb_id,$einv_id,$data){
		//BILL TO – SHIP TO
		if($taxtype_id==2){ // save data into gstshipton table
		 $gstshipton_tbl   =  $this->company_id.'_gstshipton_'.$this->session->get('ses_comp_fy_id');
		 $builder          =  $this->db->table($gstshipton_tbl);
		 $builder->where('ewb_id',$ewb_id);
		 $gstshipton_exists =  $builder->countAllResults();
         if($gstshipton_exists==0){
		   $insert_data=array("ewb_id"=>$ewb_id,"einv_id"=>$einv_id,"shipto_addr1"=>$data["shipto2_addr1"],
								"shipto_addr2"=>$data["shipto2_addr2"],"shipto_place"=>$data["shipto2_place"],"shipto_pin"=>$data["shipto2_pin"],
								"shipto_state_code"=>$data["shipto2_state_code"],
								"shipto_gstin"=>$data["shipto2_gstin"],"shipto_legal_name"=>$data["shipto2_legalname"],
								"shipto_trade_name"=>$data["shipto2_tradename"]
								);			 
		   $this->db->table($gstshipton_tbl)->insert($insert_data);					
		  }
		  else{
			$update_data=array("shipto_addr1"=>$data["shipto2_addr1"],
								"shipto_addr2"=>$data["shipto2_addr2"],"shipto_place"=>$data["shipto2_place"],"shipto_pin"=>$data["shipto2_pin"],
								"shipto_state_code"=>$data["shipto2_state_code"],
								"shipto_gstin"=>$data["shipto2_gstin"],"shipto_legal_name"=>$data["shipto2_legalname"],
								"shipto_trade_name"=>$data["shipto2_tradename"]
								);			 
			 $this->db->table($gstshipton_tbl)->where('ewb_id',$ewb_id)->update($update_data);  
		  }
			
		}
		//BILL FROM – DISPATCH FROM
		if($taxtype_id==3){ // save data into gstdispfrm table
         $gstdispfrm_tbl  = $this->company_id.'_gstdispfrm_'.$this->session->get('ses_comp_fy_id');
         $builder         =  $this->db->table($gstdispfrm_tbl);
		 $builder->where('ewb_id',$ewb_id);
		 $gstdispfrm_exists =  $builder->countAllResults();
		 if($gstdispfrm_exists==0){
			$insert_data=array("ewb_id"=>$ewb_id,"einv_id"=>$einv_id,"dispfrm_addr1"=>$data["dispfrm3_addr1"],
							"dispfrm_addr2"=>$data["dispfrm3_addr2"],"dispfrm_place"=>$data["dispfrm3_place"],"dispfrm_pin"=>$data["dispfrm3_pin"],
							"dispfrm_state_code"=>$data["dispfrm3_state_code"]
							); 
			$this->db->table($gstdispfrm_tbl)->insert($insert_data);							
		    }
		else{
		    $update_data=array("dispfrm_addr1"=>$data["dispfrm3_addr1"],
							"dispfrm_addr2"=>$data["dispfrm3_addr2"],"dispfrm_place"=>$data["dispfrm3_place"],"dispfrm_pin"=>$data["dispfrm3_pin"],
							"dispfrm_state_code"=>$data["dispfrm3_state_code"]
							); 
			$this->db->table($gstdispfrm_tbl)->where('ewb_id',$ewb_id)->update($update_data);	  
		  }
		 				
			
		}
		
		//BILL TO – SHIP TO – BILL FROM – DISPATCH FROM
		if($taxtype_id==4){ // save data into gstshipton & gstdispfrm table
         $gstshipton_tbl = $this->company_id.'_gstshipton_'.$this->session->get('ses_comp_fy_id');
         
		 $builder          =  $this->db->table($gstshipton_tbl);
		 $builder->where('ewb_id',$ewb_id);
		 $gstshipton_exists =  $builder->countAllResults();
		 if($gstshipton_exists==0){
		 $insert_data1=array("ewb_id"=>$ewb_id,"einv_id"=>$einv_id,"shipto_addr1"=>$data["shipto4_addr1"],
							"shipto_addr2"=>$data["shipto4_addr2"],"shipto_place"=>$data["shipto4_place"],"shipto_pin"=>$data["shipto4_pin"],
							"shipto_state_code"=>$data["shipto4_state_code"]
							);	
		 $this->db->table($gstshipton_tbl)->insert($insert_data1);
		 }else{
		  $insert_data1=array("shipto_addr1"=>$data["shipto4_addr1"],
							"shipto_addr2"=>$data["shipto4_addr2"],"shipto_place"=>$data["shipto4_place"],"shipto_pin"=>$data["shipto4_pin"],
							"shipto_state_code"=>$data["shipto4_state_code"],
							"shipto_gstin"=>$data["shipto4_gstin"],"shipto_legal_name"=>$data["shipto4_legalname"],
								"shipto_trade_name"=>$data["shipto4_tradename"]
							);	
		  $this->db->table($gstshipton_tbl)->where('ewb_id',$ewb_id)->update($insert_data1); 
			 
		 }
		 
		 $gstdispfrm_tbl = $this->company_id.'_gstdispfrm_'.$this->session->get('ses_comp_fy_id');
         
		 $builder1         =  $this->db->table($gstdispfrm_tbl);
		 $builder1->where('ewb_id',$ewb_id);
		 $gstdispfrm_exists =  $builder1->countAllResults();
		 if($gstdispfrm_exists==0){
		 $insert_data2=array("ewb_id"=>$ewb_id,"einv_id"=>$einv_id,"dispfrm_addr1"=>$data["dispfrm4_addr1"],
							"dispfrm_addr2"=>$data["dispfrm4_addr2"],"dispfrm_place"=>$data["dispfrm4_place"],"dispfrm_pin"=>$data["dispfrm4_pin"],
							"dispfrm_state_code"=>$data["dispfrm4_state_code"]
							);	
		 $this->db->table($gstdispfrm_tbl)->insert($insert_data2);
		 }
		else{
		$insert_data2=array("dispfrm_addr1"=>$data["dispfrm4_addr1"],
							"dispfrm_addr2"=>$data["dispfrm4_addr2"],"dispfrm_place"=>$data["dispfrm4_place"],"dispfrm_pin"=>$data["dispfrm4_pin"],
							"dispfrm_state_code"=>$data["dispfrm4_state_code"]
							);	
		 $this->db->table($gstdispfrm_tbl)->where('ewb_id',$ewb_id)->update($insert_data2);
		 }		 
			
		}
		
	}
	
	function updategstroutsup_info($voucher_txn_id,$data){
	  $gstroutsup_tbl = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
	  
	   $builder         =  $this->db->table($gstroutsup_tbl);
	   $builder->where('voucher_txn_id',$voucher_txn_id);
	   $exists =  $builder->countAllResults();
	   if($exists)
	    $this->db->table($gstroutsup_tbl)->where('voucher_txn_id',$voucher_txn_id)->update($data);
       else{
		$insert_data = array("voucher_txn_id"=>$voucher_txn_id,"outsup_bill_ref_no"=>$data['outsup_bill_ref_no'],"outsup_rev_chg"=>"0");   
		$this->db->table($gstroutsup_tbl)->insert($insert_data);   
	   }
     }
	
    function add_ewbmstreqn_data($data){
         $ewbmstreqn_tbl = $this->company_id.'_ewbmstreqn_'.$this->session->get('ses_comp_fy_id');
         $this->db->table($ewbmstreqn_tbl)->insert($data);
		 return $this->db->insertID();
    }
    function add_einvmaster_data($data){
         $einvmaster_tbl = $this->company_id.'_einvmaster_'.$this->session->get('ses_comp_fy_id');
         $this->db->table($einvmaster_tbl)->insert($data);
		 return $this->db->insertID();
	}
	function add_ewbmstcons_data($data){
         $ewbmstcons_tbl = $this->company_id.'_ewbmstcons_'.$this->session->get('ses_comp_fy_id');
         $this->db->table($ewbmstcons_tbl)->insert($data);
		 return $this->db->insertID();
	}
   function update_ewbmstcons_data($ewb_id,$data){
         $ewbmstcons_tbl = $this->company_id.'_ewbmstcons_'.$this->session->get('ses_comp_fy_id');
         $this->db->table($ewbmstcons_tbl)->where('ewb_id',$ewb_id)->update($data);
		 return $ewb_id;
	}	
	
	function gstroutsup_info($voucher_txn_id){
	 $gstroutsup_tbl = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');	
     $response = $this->db->table($gstroutsup_tbl)->where('voucher_txn_id',$voucher_txn_id)->get()->getRowArray();
	 return $response;			
	}
	function hsnwise_creditnotes_info($from_date, $to_date,$vouchers_result,$tableinfo){
	    $acctgstsum_tbl     = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	        $comp_txn_tbl       = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	        $gstroutsup_tbl     = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
	        $gstrinwsup_tbl     = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
	       	
			$total_counter= array();
			$ttt_taxable_amt=0;
			$ttt_total_tax =0;
			$total_invoice_value=0;
			$ttt_acc_igst =0;
			$ttt_acc_cgst =0;
			$ttt_acc_sgst =0;
            $ttt_acc_cess =0;
			$ttt_acc_nonadv_cess =0;
		    $tt_total_vch_counters=0;
			if($vouchers_result){
			foreach($vouchers_result as $row){
			    $response=array();
				$voucher_txn_id = $row['voucher_txn_id'];
				$voucher_type_id = $row['voucher_type_id'];
				$total_vch_counters=0;
				if($voucher_type_id=="2"){
					$voucher_txn_id = $row['voucher_txn_id'];
					
					/****** Counter ************/ 
					$builders = $this->db->table($acctgstsum_tbl.' gstsum');
					$builders->join($gstrinwsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
					$builders->select('gstsup.inwsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
					$builders->orderBy('gstsum.vch_txn_id');	        
					$builders->where('gstsup.inwsup_rev_chg',$tableinfo['outsup_rev_chg']);
					$builders->where('gstsup.inwsup_eco',$tableinfo['outsup_eco']);
					$builders->where('gstsup.inwsup_cr_note',$tableinfo['outsup_cr_note']);	
					$builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
					$builders->where('gstsum.vch_txn_id', $voucher_txn_id);	
					$builders->whereIn('gstsup.	inwsup_inv_type',$tableinfo['outsup_inv_type']);	
				    $builders->where('gstsum.acc_txn_date >=', $from_date);
					$builders->where('gstsum.acc_txn_date <=', $to_date);
					$builders->groupBy('gstsum.vch_txn_id');		 
				   	$txncounter = $builders->countAllResults();
					$total_vch_counters +=$txncounter;
					 
					$tt_total_vch_counters +=$total_vch_counters;
					
					
					/******* Records ***********/ 
				    $builders = $this->db->table($acctgstsum_tbl.' gstsum');
					$builders->join($gstrinwsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
					$builders->select('gstsup.inwsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
					$builders->orderBy('gstsum.vch_txn_id');	        
					$builders->where('gstsup.inwsup_rev_chg',$tableinfo['outsup_rev_chg']);
					$builders->where('gstsup.inwsup_eco',$tableinfo['outsup_eco']);
					$builders->where('gstsup.inwsup_cr_note',$tableinfo['outsup_cr_note']);	
					$builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
					$builders->where('gstsum.vch_txn_id', $voucher_txn_id);	
					$builders->whereIn('gstsup.	inwsup_inv_type',$tableinfo['outsup_inv_type']);	
				    $builders->where('gstsum.acc_txn_date >=', $from_date);
					$builders->where('gstsum.acc_txn_date <=', $to_date);
					$response = $builders->get()->getResultArray();
					
					
					
					
				 }
		
		
					 
			$total_voucher=0;
			$tt_credit = 0;
	      	$tt_debit = 0;
	      	$tt_credit_total = 0;
	      	$tt_debit_total = 0;
			$tt_taxable_amt =0;
			$tt_acc_igst=0;
			$tt_acc_cgst=0;
			$tt_acc_sgst=0;
			$tt_acc_cess=0;
			$tt_acc_nonadv_cess=0;
			$tt_total_tax=0;
			$tt_total_value=0;
			$tt_total_voucher=0;
			  if($response){
				foreach($response as $txrow){
                    $total_voucher++;						
					$taxable_amt= $txrow['taxable_amt'];
					$acc_igst    = $txrow['acc_igst'];
					$acc_cgst    = $txrow['acc_cgst'];
					$acc_sgst    = $txrow['acc_sgst'];
					$acc_cess    = $txrow['acc_cess'];
					$acc_nonadv_cess    = $txrow['acc_nonadv_cess'];
					$total_tax         = $txrow['total_tax'];
					$bill_ref_no      = $txrow['bill_ref_no'];
					
					
					$tt_total_voucher +=$total_voucher;
					$tt_taxable_amt +=$taxable_amt;
					$tt_total_tax +=$total_tax;
					$tt_acc_igst +=$acc_igst;				
					$tt_acc_cgst +=$acc_cgst;
					$tt_acc_sgst +=$acc_sgst;				
					$tt_acc_cess +=$acc_cess;
					$tt_acc_nonadv_cess +=$acc_nonadv_cess;
					
					
					
			
				    }
				    
				    $ttt_taxable_amt +=$tt_taxable_amt;
				    $ttt_total_tax +=$tt_total_tax;
				    $ttt_acc_igst +=$tt_acc_igst;
				  	$ttt_acc_cgst +=$tt_acc_cgst;
				    $ttt_acc_sgst +=$tt_acc_sgst;
                    $ttt_acc_cess +=$tt_acc_cess;
				    $ttt_acc_nonadv_cess +=$tt_acc_nonadv_cess;


			      }
				  
				
			
			   }	
				
			}
			$total_invoice_value = $ttt_taxable_amt+$ttt_total_tax;
		
	$data=["total_vouchers"=>$tt_total_vch_counters,"invoice_value"=>$total_invoice_value,
		      'taxable_amt'=>$ttt_taxable_amt,"igst"=>$ttt_acc_igst,"cgst"=>$ttt_acc_cgst,
			  "sgst"=>$ttt_acc_sgst,"cess"=>$ttt_acc_cess,"nonadv_cess"=>$ttt_acc_nonadv_cess,
			       "total_tax"=>$ttt_total_tax
				  ];	
		return $data;
	    
	}
	
	function hsnwise_info($from_date,$to_date,$voucher_result,$purchase_sale='sale'){
	 $acctgstsum_tbl= $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	 $comp_txn_tbl  = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	 $gstroutsup_tbl     = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
	 $gstrinwsup_tbl     = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
	 $voucher_tbl      = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	      	      	
   	 $ttt_taxable_amt=0;
			$ttt_total_tax =0;
			$total_invoice_value=0;
			$ttt_acc_igst =0;
			$ttt_acc_cgst =0;
			$ttt_acc_sgst =0;
            $ttt_acc_cess =0;
			$ttt_acc_nonadv_cess =0;
		    $tt_total_vch_counters=0;
			$alltxn_ids=array();	 
	        
			
		   if($voucher_result){
			  foreach($voucher_result as $row){
			      $total_vch_counters=0;
				 $vch_txn_id      = $row['voucher_txn_id'];
				 $voucher_txn_id  = $row['voucher_txn_id'];
				 $voucher_type_id = $row['voucher_type_id'];
				 
				 if($voucher_type_id==2){					
					$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
					$builders = $this->db->table($acctgstsum_tbl.' gstsum');
					$builders->join($gstrinwsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
					$builders->select('gstsup.inwsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
					$builders->orderBy('gstsum.vch_txn_id');
					$builders->where('gstsum.vch_txn_id', $voucher_txn_id);
				    $builders->where('gstsum.acc_txn_date >=', $from_date);
					$builders->where('gstsum.acc_txn_date <=', $to_date);
					$builders->groupBy('gstsum.vch_txn_id');		 
				   	$txncounter = $builders->countAllResults();
					$total_vch_counters +=$txncounter;
					$tt_total_vch_counters +=$total_vch_counters;					
					
					$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
				    $builders = $this->db->table($acctgstsum_tbl.' gstsum');
					$builders->join($gstrinwsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
					$builders->select('gstsup.inwsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
					$builders->orderBy('gstsum.vch_txn_id');
					$builders->where('gstsum.vch_txn_id', $voucher_txn_id);					
				    $builders->where('gstsum.acc_txn_date >=', $from_date);
					$builders->where('gstsum.acc_txn_date <=', $to_date);						
					$response = $builders->get()->getResultArray();					
				 }
				 
				 if($voucher_type_id==3){					
					$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
					$builders = $this->db->table($acctgstsum_tbl.' gstsum');
					$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
					$builders->select('gstsup.outsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
					$builders->orderBy('gstsum.vch_txn_id');
					$builders->where('gstsum.vch_txn_id', $voucher_txn_id);
				    $builders->where('gstsum.acc_txn_date >=', $from_date);
					$builders->where('gstsum.acc_txn_date <=', $to_date);
					$builders->groupBy('gstsum.vch_txn_id');		 
				   	$txncounter = $builders->countAllResults();
					$total_vch_counters +=$txncounter;
					$tt_total_vch_counters +=$total_vch_counters;
					
					
					$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
				    $builders = $this->db->table($acctgstsum_tbl.' gstsum');
					$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
					$builders->select('gstsup.outsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
					$builders->orderBy('gstsum.vch_txn_id');
					$builders->where('gstsum.vch_txn_id', $voucher_txn_id);					
				    $builders->where('gstsum.acc_txn_date >=', $from_date);
					$builders->where('gstsum.acc_txn_date <=', $to_date);						
					$response = $builders->get()->getResultArray();
					
				 } 
				 if($voucher_type_id==18){					
					 $alltxn_ids[$voucher_type_id]=$voucher_type_id;
					/****** Counter ************/ 
					$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
					$builders = $this->db->table($acctgstsum_tbl.' gstsum');
    				$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
    				$builders->select('gstsup.outsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
    				$builders->orderBy('gstsum.vch_txn_id');						
    				$builders->where('gstsum.vch_txn_id', $voucher_txn_id);	
    				$builders->where('gstsum.acc_txn_date >=', $from_date);
    				$builders->where('gstsum.acc_txn_date <=', $to_date);
    				$builders->groupBy('gstsum.vch_txn_id');		 
					 $txncounter = $builders->countAllResults();
					 $total_vch_counters +=$txncounter;					 
					 $tt_total_vch_counters +=$total_vch_counters;
					 
					/******* Records ***********/ 
					$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
					
    				$builders = $this->db->table($acctgstsum_tbl.' gstsum');
    				$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
    				$builders->select('gstsup.outsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
    				$builders->orderBy('gstsum.vch_txn_id');	  
    				$builders->where('gstsum.vch_txn_id', $voucher_txn_id);							
    				$builders->where('gstsum.acc_txn_date >=', $from_date);
    				$builders->where('gstsum.acc_txn_date <=', $to_date);						
    				$response = $builders->get()->getResultArray();
				 }
				 if($voucher_type_id==11){					
					 $alltxn_ids[$voucher_type_id]=$voucher_type_id;
					/****** Counter ************/ 
					$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
					$builders = $this->db->table($acctgstsum_tbl.' gstsum');
    				$builders->join($gstrinwsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
    				$builders->select('gstsup.inwsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
    				$builders->orderBy('gstsum.vch_txn_id');						
    				$builders->where('gstsum.vch_txn_id', $voucher_txn_id);	
    				$builders->where('gstsum.acc_txn_date >=', $from_date);
    				$builders->where('gstsum.acc_txn_date <=', $to_date);
    				$builders->groupBy('gstsum.vch_txn_id');		 
					 $txncounter = $builders->countAllResults();
					 $total_vch_counters +=$txncounter;					 
					 $tt_total_vch_counters +=$total_vch_counters;
					 
					/******* Records ***********/ 
					$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
					
    				$builders = $this->db->table($acctgstsum_tbl.' gstsum');
    				$builders->join($gstrinwsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
    				$builders->select('gstsup.inwsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
    				$builders->orderBy('gstsum.vch_txn_id');	  
    				$builders->where('gstsum.vch_txn_id', $voucher_txn_id);							
    				$builders->where('gstsum.acc_txn_date >=', $from_date);
    				$builders->where('gstsum.acc_txn_date <=', $to_date);						
    				$response = $builders->get()->getResultArray();
				 }
				  
			$total_voucher=0;
			$tt_credit = 0;
	      	$tt_debit = 0;
	      	$tt_credit_total = 0;
	      	$tt_debit_total = 0;
			$tt_taxable_amt =0;
			$tt_acc_igst=0;
			$tt_acc_cgst=0;
			$tt_acc_sgst=0;
			$tt_acc_cess=0;
			$tt_acc_nonadv_cess=0;
			$tt_total_tax=0;
			$tt_total_value=0;
			$tt_total_voucher=0;
			
			  if(isset($response)){
				  
				  /*  echo '<pre>';
				  print_r($response);
				  echo '<br>';  */
				foreach($response as $txrow){
                    $total_voucher++;						
					$taxable_amt= $txrow['taxable_amt'];
					$acc_igst    = $txrow['acc_igst'];
					$acc_cgst    = $txrow['acc_cgst'];
					$acc_sgst    = $txrow['acc_sgst'];
					$acc_cess    = $txrow['acc_cess'];
					$acc_nonadv_cess    = $txrow['acc_nonadv_cess'];
					$total_tax         = $txrow['total_tax'];
					$bill_ref_no      = $txrow['bill_ref_no'];
					
					
					$tt_total_voucher +=$total_voucher;
					$tt_taxable_amt +=$taxable_amt;
					$tt_total_tax +=$total_tax;
					$tt_acc_igst +=$acc_igst;				
					$tt_acc_cgst +=$acc_cgst;
					$tt_acc_sgst +=$acc_sgst;				
					$tt_acc_cess +=$acc_cess;
					$tt_acc_nonadv_cess +=$acc_nonadv_cess;
					
					
					
			
				    }
				    
				    $ttt_taxable_amt +=$tt_taxable_amt;
				    $ttt_total_tax +=$tt_total_tax;
				    $ttt_acc_igst +=$tt_acc_igst;
				  	$ttt_acc_cgst +=$tt_acc_cgst;
				    $ttt_acc_sgst +=$tt_acc_sgst;
                    $ttt_acc_cess +=$tt_acc_cess;
				    $ttt_acc_nonadv_cess +=$tt_acc_nonadv_cess;
			      }				 
		     	}
		      }	
	$total_invoice_value = $ttt_taxable_amt+$ttt_total_tax;		
	$data=["total_vouchers"=>$tt_total_vch_counters,"invoice_value"=>$total_invoice_value,
		      'taxable_amt'=>$ttt_taxable_amt,"igst"=>$ttt_acc_igst,"cgst"=>$ttt_acc_cgst,
			  "sgst"=>$ttt_acc_sgst,"cess"=>$ttt_acc_cess,"nonadv_cess"=>$ttt_acc_nonadv_cess,
			       "total_tax"=>$ttt_total_tax,'alltxn_ids'=>$alltxn_ids
				  ];	

		return $data;
		
		
	  	
	}
	
	function gst_eco_table_info($from_date,$to_date,$voucher_result,$tableinfo){
	 
	 $acctgstsum_tbl= $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	 $comp_txn_tbl  = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	 $gstroutsup_tbl     = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
   	 $ttt_taxable_amt=0;
			$ttt_total_tax =0;
			$total_invoice_value=0;
			$ttt_acc_igst =0;
			$ttt_acc_cgst =0;
			$ttt_acc_sgst =0;
            $ttt_acc_cess =0;
			$ttt_acc_nonadv_cess =0;
		    $tt_total_vch_counters=0;
	 $alltxn_ids=array();	 
	        
		   if($voucher_result){
			  foreach($voucher_result as $row){
			      $total_vch_counters=0;
				 $vch_txn_id = $row['voucher_txn_id'];
				 $voucher_type_id = $row['voucher_type_id'];
				 if($voucher_type_id==18){
					 $voucher_txn_id = $row['voucher_txn_id'];
					/****** Counter ************/ 
						$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
					 $builders = $this->db->table($acctgstsum_tbl.' gstsum');
    				$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
    				$builders->select('gstsup.outsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
    				$builders->orderBy('gstsum.vch_txn_id');	 
    				$builders->where('gstsum.vch_txn_id', $voucher_txn_id);	
    				$builders->where('gstsum.acc_txn_date >=', $from_date);
    				$builders->where('gstsum.acc_txn_date <=', $to_date);
    				$builders->where('gstsup.outsup_rev_chg',$tableinfo['outsup_rev_chg']);
				    $builders->where('gstsup.outsup_eco',$tableinfo['outsup_eco']);
				    $builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    				 $builders->groupBy('gstsum.vch_txn_id');		 
					 $txncounter = $builders->countAllResults();
					 $total_vch_counters +=$txncounter;
					 
					 $tt_total_vch_counters +=$total_vch_counters;
					 
					/******* Records ***********/ 
    				$builders = $this->db->table($acctgstsum_tbl.' gstsum');
    				$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
    				$builders->select('gstsup.outsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
    				$builders->orderBy('gstsum.vch_txn_id');	  
    				$builders->where('gstsup.outsup_rev_chg',$tableinfo['outsup_rev_chg']);
				    $builders->where('gstsup.outsup_eco',$tableinfo['outsup_eco']);
				    $builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    				$builders->where('gstsum.vch_txn_id', $voucher_txn_id);	
    				$builders->where('gstsum.acc_txn_date >=', $from_date);
    				$builders->where('gstsum.acc_txn_date <=', $to_date);
    				$response = $builders->get()->getResultArray();
				  
				  
				  
				  $total_voucher=0;
			$tt_credit = 0;
	      	$tt_debit = 0;
	      	$tt_credit_total = 0;
	      	$tt_debit_total = 0;
			$tt_taxable_amt =0;
			$tt_acc_igst=0;
			$tt_acc_cgst=0;
			$tt_acc_sgst=0;
			$tt_acc_cess=0;
			$tt_acc_nonadv_cess=0;
			$tt_total_tax=0;
			$tt_total_value=0;
			$tt_total_voucher=0;
			  if($response){
				foreach($response as $txrow){
                    $total_voucher++;						
					$taxable_amt= $txrow['taxable_amt'];
					$acc_igst    = $txrow['acc_igst'];
					$acc_cgst    = $txrow['acc_cgst'];
					$acc_sgst    = $txrow['acc_sgst'];
					$acc_cess    = $txrow['acc_cess'];
					$acc_nonadv_cess    = $txrow['acc_nonadv_cess'];
					$total_tax         = $txrow['total_tax'];
					$bill_ref_no      = $txrow['bill_ref_no'];
					
					
					$tt_total_voucher +=$total_voucher;
					$tt_taxable_amt +=$taxable_amt;
					$tt_total_tax +=$total_tax;
					$tt_acc_igst +=$acc_igst;				
					$tt_acc_cgst +=$acc_cgst;
					$tt_acc_sgst +=$acc_sgst;				
					$tt_acc_cess +=$acc_cess;
					$tt_acc_nonadv_cess +=$acc_nonadv_cess;
					
					
					
			
				    }
				    
				    $ttt_taxable_amt +=$tt_taxable_amt;
				    $ttt_total_tax +=$tt_total_tax;
				    $ttt_acc_igst +=$tt_acc_igst;
				  	$ttt_acc_cgst +=$tt_acc_cgst;
				    $ttt_acc_sgst +=$tt_acc_sgst;
                    $ttt_acc_cess +=$tt_acc_cess;
				    $ttt_acc_nonadv_cess +=$tt_acc_nonadv_cess;


			      }
			      
			      
				 }
				 
				 
				 
		     	}
		      }	
	$total_invoice_value = $ttt_taxable_amt+$ttt_total_tax;
		
	$data=["total_vouchers"=>$tt_total_vch_counters,"invoice_value"=>$total_invoice_value,
		      'taxable_amt'=>$ttt_taxable_amt,"igst"=>$ttt_acc_igst,"cgst"=>$ttt_acc_cgst,
			  "sgst"=>$ttt_acc_sgst,"cess"=>$ttt_acc_cess,"nonadv_cess"=>$ttt_acc_nonadv_cess,
			       "total_tax"=>$ttt_total_tax
				  ];	
		return $data; 	    
	}
	function gstr2ab_eco_table_info($from_date,$to_date,$voucher_result,$tableinfo){
	 
	 $acctgstsum_tbl= $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	 $comp_txn_tbl  = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	 $gstrinwsup_tbl     = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
   	 $ttt_taxable_amt=0;
			$ttt_total_tax =0;
			$total_invoice_value=0;
			$ttt_acc_igst =0;
			$ttt_acc_cgst =0;
			$ttt_acc_sgst =0;
            $ttt_acc_cess =0;
			$ttt_acc_nonadv_cess =0;
		    $tt_total_vch_counters=0;
	 $alltxn_ids=array();	 
	        
		   if($voucher_result){
			  foreach($voucher_result as $row){
			      $total_vch_counters=0;
				 $vch_txn_id = $row['voucher_txn_id'];
				 $voucher_type_id = $row['voucher_type_id'];
				 if($voucher_type_id==11){
					 $voucher_txn_id = $row['voucher_txn_id'];
					/****** Counter ************/ 
						$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
					 $builders = $this->db->table($acctgstsum_tbl.' gstsum');
    				$builders->join($gstrinwsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
    				$builders->select('gstsup.inwsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
    				$builders->orderBy('gstsum.vch_txn_id');	 
    				$builders->where('gstsum.vch_txn_id', $voucher_txn_id);	
    				$builders->where('gstsum.acc_txn_date >=', $from_date);
    				$builders->where('gstsum.acc_txn_date <=', $to_date);
    				$builders->where('gstsup.inwsup_rev_chg',$tableinfo['outsup_rev_chg']);
				    $builders->where('gstsup.inwsup_eco',$tableinfo['outsup_eco']);
				    $builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    				 $builders->groupBy('gstsum.vch_txn_id');		 
					 $txncounter = $builders->countAllResults();
					 $total_vch_counters +=$txncounter;
					 
					 $tt_total_vch_counters +=$total_vch_counters;
					 
					/******* Records ***********/ 
    				$builders = $this->db->table($acctgstsum_tbl.' gstsum');
    				$builders->join($gstrinwsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
    				$builders->select('gstsup.inwsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
    				$builders->orderBy('gstsum.vch_txn_id');	  
    				$builders->where('gstsup.inwsup_rev_chg',$tableinfo['outsup_rev_chg']);
				    $builders->where('gstsup.inwsup_eco',$tableinfo['outsup_eco']);
				    $builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    				$builders->where('gstsum.vch_txn_id', $voucher_txn_id);	
    				$builders->where('gstsum.acc_txn_date >=', $from_date);
    				$builders->where('gstsum.acc_txn_date <=', $to_date);
    				$response = $builders->get()->getResultArray();
				  
				  
				  
				  $total_voucher=0;
			$tt_credit = 0;
	      	$tt_debit = 0;
	      	$tt_credit_total = 0;
	      	$tt_debit_total = 0;
			$tt_taxable_amt =0;
			$tt_acc_igst=0;
			$tt_acc_cgst=0;
			$tt_acc_sgst=0;
			$tt_acc_cess=0;
			$tt_acc_nonadv_cess=0;
			$tt_total_tax=0;
			$tt_total_value=0;
			$tt_total_voucher=0;
			  if($response){
				foreach($response as $txrow){
                    $total_voucher++;						
					$taxable_amt= $txrow['taxable_amt'];
					$acc_igst    = $txrow['acc_igst'];
					$acc_cgst    = $txrow['acc_cgst'];
					$acc_sgst    = $txrow['acc_sgst'];
					$acc_cess    = $txrow['acc_cess'];
					$acc_nonadv_cess    = $txrow['acc_nonadv_cess'];
					$total_tax         = $txrow['total_tax'];
					$bill_ref_no      = $txrow['bill_ref_no'];
					
					
					$tt_total_voucher +=$total_voucher;
					$tt_taxable_amt +=$taxable_amt;
					$tt_total_tax +=$total_tax;
					$tt_acc_igst +=$acc_igst;				
					$tt_acc_cgst +=$acc_cgst;
					$tt_acc_sgst +=$acc_sgst;				
					$tt_acc_cess +=$acc_cess;
					$tt_acc_nonadv_cess +=$acc_nonadv_cess;
					
					
					
			
				    }
				    
				    $ttt_taxable_amt +=$tt_taxable_amt;
				    $ttt_total_tax +=$tt_total_tax;
				    $ttt_acc_igst +=$tt_acc_igst;
				  	$ttt_acc_cgst +=$tt_acc_cgst;
				    $ttt_acc_sgst +=$tt_acc_sgst;
                    $ttt_acc_cess +=$tt_acc_cess;
				    $ttt_acc_nonadv_cess +=$tt_acc_nonadv_cess;


			      }
			      
			      
				 }
				 
				 
				 
		     	}
		      }	
	$total_invoice_value = $ttt_taxable_amt+$ttt_total_tax;
		
	$data=["total_vouchers"=>$tt_total_vch_counters,"invoice_value"=>$total_invoice_value,
		      'taxable_amt'=>$ttt_taxable_amt,"igst"=>$ttt_acc_igst,"cgst"=>$ttt_acc_cgst,
			  "sgst"=>$ttt_acc_sgst,"cess"=>$ttt_acc_cess,"nonadv_cess"=>$ttt_acc_nonadv_cess,
			       "total_tax"=>$ttt_total_tax
				  ];	
		return $data; 	    
	}
	
	function gst_supplies_table_info($from_date,$to_date,$voucher_result,$tableinfo){
	    $acctgstsum_tbl= $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	 $comp_txn_tbl  = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	 $gstroutsup_tbl     = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
   	 $ttt_taxable_amt=0;
			$ttt_total_tax =0;
			$total_invoice_value=0;
			$ttt_acc_igst =0;
			$ttt_acc_cgst =0;
			$ttt_acc_sgst =0;
            $ttt_acc_cess =0;
			$ttt_acc_nonadv_cess =0;
		    $tt_total_vch_counters=0;
	 $alltxn_ids=array();	 
	        
		   if($voucher_result){
			  foreach($voucher_result as $row){
			      $total_vch_counters=0;
				 $vch_txn_id = $row['voucher_txn_id'];
				 $voucher_type_id = $row['voucher_type_id'];
				 if($voucher_type_id==18){
					 $voucher_txn_id = $row['voucher_txn_id'];
					/****** Counter ************/ 
						$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
					 $builders = $this->db->table($acctgstsum_tbl.' gstsum');
    				$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
    				$builders->select('gstsup.outsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
    				$builders->orderBy('gstsum.vch_txn_id');	 
    				$builders->where('gstsum.vch_txn_id', $voucher_txn_id);	
    				$builders->where('gstsum.acc_txn_date >=', $from_date);
    				$builders->where('gstsum.acc_txn_date <=', $to_date);
    				$builders->where('gstsup.outsup_rev_chg',$tableinfo['outsup_rev_chg']);
				    $builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    				 $builders->groupBy('gstsum.vch_txn_id');		 
					 $txncounter = $builders->countAllResults();
					 $total_vch_counters +=$txncounter;
					 
					 $tt_total_vch_counters +=$total_vch_counters;
					 
					/******* Records ***********/ 
    				$builders = $this->db->table($acctgstsum_tbl.' gstsum');
    				$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
    				$builders->select('gstsup.outsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
    				$builders->orderBy('gstsum.vch_txn_id');	  
    				$builders->where('gstsum.vch_txn_id', $voucher_txn_id);	
    				$builders->where('gstsum.acc_txn_date >=', $from_date);
    				$builders->where('gstsum.acc_txn_date <=', $to_date);
    				$builders->where('gstsup.outsup_rev_chg',$tableinfo['outsup_rev_chg']);
				    $builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    				$response = $builders->get()->getResultArray();
				  
				  
				  
				  $total_voucher=0;
			$tt_credit = 0;
	      	$tt_debit = 0;
	      	$tt_credit_total = 0;
	      	$tt_debit_total = 0;
			$tt_taxable_amt =0;
			$tt_acc_igst=0;
			$tt_acc_cgst=0;
			$tt_acc_sgst=0;
			$tt_acc_cess=0;
			$tt_acc_nonadv_cess=0;
			$tt_total_tax=0;
			$tt_total_value=0;
			$tt_total_voucher=0;
			  if($response){
				foreach($response as $txrow){
                    $total_voucher++;						
					$taxable_amt= $txrow['taxable_amt'];
					$acc_igst    = $txrow['acc_igst'];
					$acc_cgst    = $txrow['acc_cgst'];
					$acc_sgst    = $txrow['acc_sgst'];
					$acc_cess    = $txrow['acc_cess'];
					$acc_nonadv_cess    = $txrow['acc_nonadv_cess'];
					$total_tax         = $txrow['total_tax'];
					$bill_ref_no      = $txrow['bill_ref_no'];
					
					
					$tt_total_voucher +=$total_voucher;
					$tt_taxable_amt +=$taxable_amt;
					$tt_total_tax +=$total_tax;
					$tt_acc_igst +=$acc_igst;				
					$tt_acc_cgst +=$acc_cgst;
					$tt_acc_sgst +=$acc_sgst;				
					$tt_acc_cess +=$acc_cess;
					$tt_acc_nonadv_cess +=$acc_nonadv_cess;
					
					
					
			
				    }
				    
				    $ttt_taxable_amt +=$tt_taxable_amt;
				    $ttt_total_tax +=$tt_total_tax;
				    $ttt_acc_igst +=$tt_acc_igst;
				  	$ttt_acc_cgst +=$tt_acc_cgst;
				    $ttt_acc_sgst +=$tt_acc_sgst;
                    $ttt_acc_cess +=$tt_acc_cess;
				    $ttt_acc_nonadv_cess +=$tt_acc_nonadv_cess;


			      }
			      
			      
				 }
				 
				 
				 
		     	}
		      }	
	$total_invoice_value = $ttt_taxable_amt+$ttt_total_tax;
		
	$data=["total_vouchers"=>$tt_total_vch_counters,"invoice_value"=>$total_invoice_value,
		      'taxable_amt'=>$ttt_taxable_amt,"igst"=>$ttt_acc_igst,"cgst"=>$ttt_acc_cgst,
			  "sgst"=>$ttt_acc_sgst,"cess"=>$ttt_acc_cess,"nonadv_cess"=>$ttt_acc_nonadv_cess,
			       "total_tax"=>$ttt_total_tax
				  ];	
		return $data;
	    
	}
	
	function gstr2ab_supplies_table_info($from_date,$to_date,$voucher_result,$tableinfo){
	    $acctgstsum_tbl= $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	 $comp_txn_tbl  = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	 $gstrinwsup_tbl     = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
   	 $ttt_taxable_amt=0;
			$ttt_total_tax =0;
			$total_invoice_value=0;
			$ttt_acc_igst =0;
			$ttt_acc_cgst =0;
			$ttt_acc_sgst =0;
            $ttt_acc_cess =0;
			$ttt_acc_nonadv_cess =0;
		    $tt_total_vch_counters=0;
	 $alltxn_ids=array();	 
	        
		   if($voucher_result){
			  foreach($voucher_result as $row){
			      $total_vch_counters=0;
				 $vch_txn_id = $row['voucher_txn_id'];
				 $voucher_type_id = $row['voucher_type_id'];
				 if($voucher_type_id==11){
					 $voucher_txn_id = $row['voucher_txn_id'];
					/****** Counter ************/ 
						$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
					 $builders = $this->db->table($acctgstsum_tbl.' gstsum');
    				$builders->join($gstrinwsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
    				$builders->select('gstsup.inwsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
    				$builders->orderBy('gstsum.vch_txn_id');	 
    				$builders->where('gstsum.vch_txn_id', $voucher_txn_id);	
    				$builders->where('gstsum.acc_txn_date >=', $from_date);
    				$builders->where('gstsum.acc_txn_date <=', $to_date);
    				$builders->where('gstsup.inwsup_rev_chg',$tableinfo['outsup_rev_chg']);
				    $builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    				 $builders->groupBy('gstsum.vch_txn_id');		 
					 $txncounter = $builders->countAllResults();
					 $total_vch_counters +=$txncounter;
					 
					 $tt_total_vch_counters +=$total_vch_counters;
					 
					/******* Records ***********/ 
    				$builders = $this->db->table($acctgstsum_tbl.' gstsum');
    				$builders->join($gstrinwsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
    				$builders->select('gstsup.inwsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
    				$builders->orderBy('gstsum.vch_txn_id');	  
    				$builders->where('gstsum.vch_txn_id', $voucher_txn_id);	
    				$builders->where('gstsum.acc_txn_date >=', $from_date);
    				$builders->where('gstsum.acc_txn_date <=', $to_date);
    				$builders->where('gstsup.inwsup_rev_chg',$tableinfo['outsup_rev_chg']);
				    $builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    				$response = $builders->get()->getResultArray();
				  
				  
				  
				  $total_voucher=0;
			$tt_credit = 0;
	      	$tt_debit = 0;
	      	$tt_credit_total = 0;
	      	$tt_debit_total = 0;
			$tt_taxable_amt =0;
			$tt_acc_igst=0;
			$tt_acc_cgst=0;
			$tt_acc_sgst=0;
			$tt_acc_cess=0;
			$tt_acc_nonadv_cess=0;
			$tt_total_tax=0;
			$tt_total_value=0;
			$tt_total_voucher=0;
			  if($response){
				foreach($response as $txrow){
                    $total_voucher++;						
					$taxable_amt= $txrow['taxable_amt'];
					$acc_igst    = $txrow['acc_igst'];
					$acc_cgst    = $txrow['acc_cgst'];
					$acc_sgst    = $txrow['acc_sgst'];
					$acc_cess    = $txrow['acc_cess'];
					$acc_nonadv_cess    = $txrow['acc_nonadv_cess'];
					$total_tax         = $txrow['total_tax'];
					$bill_ref_no      = $txrow['bill_ref_no'];
					
					
					$tt_total_voucher +=$total_voucher;
					$tt_taxable_amt +=$taxable_amt;
					$tt_total_tax +=$total_tax;
					$tt_acc_igst +=$acc_igst;				
					$tt_acc_cgst +=$acc_cgst;
					$tt_acc_sgst +=$acc_sgst;				
					$tt_acc_cess +=$acc_cess;
					$tt_acc_nonadv_cess +=$acc_nonadv_cess;
					
					
					
			
				    }
				    
				    $ttt_taxable_amt +=$tt_taxable_amt;
				    $ttt_total_tax +=$tt_total_tax;
				    $ttt_acc_igst +=$tt_acc_igst;
				  	$ttt_acc_cgst +=$tt_acc_cgst;
				    $ttt_acc_sgst +=$tt_acc_sgst;
                    $ttt_acc_cess +=$tt_acc_cess;
				    $ttt_acc_nonadv_cess +=$tt_acc_nonadv_cess;


			      }
			      
			      
				 }
				 
				 
				 
		     	}
		      }	
	$total_invoice_value = $ttt_taxable_amt+$ttt_total_tax;
		
	$data=["total_vouchers"=>$tt_total_vch_counters,"invoice_value"=>$total_invoice_value,
		      'taxable_amt'=>$ttt_taxable_amt,"igst"=>$ttt_acc_igst,"cgst"=>$ttt_acc_cgst,
			  "sgst"=>$ttt_acc_sgst,"cess"=>$ttt_acc_cess,"nonadv_cess"=>$ttt_acc_nonadv_cess,
			       "total_tax"=>$ttt_total_tax
				  ];	
		return $data;
	    
	}
	
	public function gst_nillexempted_table_info(
        string $from_date,
        string $to_date,
        array  $voucher_result,
        array  $tableinfo
): array {

    /* ───── dynamic table names ───── */
    $fy            = $this->session->get('ses_comp_fy_id');
    $gstsum_tbl    = "{$this->company_id}_acctgstsum_{$fy}";
    $outsup_tbl    = "{$this->company_id}_gstroutsup_{$fy}";
    $vchconso_tbl  = "{$this->company_id}_vhtxnconso_{$fy}";

    /* ───── bail-out when there’s nothing to total ───── */
    if (!$voucher_result) {
        return [
            'total_vouchers' => 0,
            'invoice_value'  => 0,
            'taxable_value'  => 0,
            'taxable_amt'    => 0,
            'igst'           => 0,
            'cgst'           => 0,
            'sgst'           => 0,
            'cess'           => 0,
            'nonadv_cess'    => 0,
            'total_tax'      => 0,
        ];
    }

    /* ───── list of vouchers & voucher types we care about ───── */
    $voucherIds   = array_column($voucher_result, 'voucher_txn_id');
    $voucherTypes = array_unique(array_column($voucher_result, 'voucher_type_id'));

    /* ───── single aggregated query ───── */
    $tot = $this->db->table("$gstsum_tbl gst")
        ->join("$outsup_tbl sup",  'sup.voucher_txn_id  = gst.vch_txn_id')
        ->join("$vchconso_tbl vc", 'vc.voucher_txn_id   = gst.vch_txn_id')
        ->select([
            'COUNT(DISTINCT gst.vch_txn_id)           AS total_vouchers',
            'SUM(gst.taxable_amt)                     AS taxable_amt',
            'SUM(gst.acc_igst)                        AS igst',
            'SUM(gst.acc_cgst)                        AS cgst',
            'SUM(gst.acc_sgst)                        AS sgst',
            'SUM(gst.acc_cess)                        AS cess',
            'SUM(gst.acc_nonadv_cess)                 AS nonadv_cess',
            'SUM(gst.total_tax)                       AS total_tax',
        ])
        ->whereIn('gst.vch_txn_id', $voucherIds)
        ->whereIn('vc.voucher_type_id', array_intersect($voucherTypes, [18]))   // only sales
        ->where('sup.outsup_rev_chg',        $tableinfo['outsup_rev_chg'])
        ->whereIn('gst.cmp_tax_short_code',  $tableinfo['cmp_tax_short_code'])
        ->where('gst.acc_txn_date >=', $from_date)
        ->where('gst.acc_txn_date <=', $to_date)
        ->get()
        ->getRowArray();

    // NULL → 0 and cast to float for safety
    $tot = array_map(static fn($v) => (float) $v, $tot ?: []);

    /* For NIL / Exempt we treat invoice = taxable (tax sums are 0 anyway) */
    $invoiceValue = $tot['taxable_amt'];

    return [
        'total_vouchers' => (int) $tot['total_vouchers'],

        'invoice_value'  => $invoiceValue,
        'taxable_value'  => $invoiceValue,      // UI key you already use
        'taxable_amt'    => $tot['taxable_amt'],

        'igst'           => $tot['igst'],
        'cgst'           => $tot['cgst'],
        'sgst'           => $tot['sgst'],
        'cess'           => $tot['cess'],
        'nonadv_cess'    => $tot['nonadv_cess'],
        'total_tax'      => $tot['total_tax'],  // will be 0 for NIL/Exempt
    ];
}
	
	function gstr2ab_nillexempted_table_info($from_date,$to_date,$voucher_result,$tableinfo){
		
	    $acctgstsum_tbl= $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	    $comp_txn_tbl  = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	    $gstrinwsup_tbl  = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
	 
		$comptxnmst_master  =  $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	
		$ttt_taxable_amt=0;
			$ttt_total_tax =0;
			$total_invoice_value=0;
			$ttt_acc_igst =0;
			$ttt_acc_cgst =0;
			$ttt_acc_sgst =0;
            $ttt_acc_cess =0;
			$ttt_acc_nonadv_cess =0;
		    $tt_total_vch_counters=0;
	        $alltxn_ids=array();	 
	        $taxable_amt =0;
		   if($voucher_result){
			  foreach($voucher_result as $row){
			      $total_vch_counters=0;
				 $vch_txn_id = $row['voucher_txn_id'];
				 $voucher_type_id = $row['voucher_type_id'];
				
					 $voucher_txn_id  = $row['voucher_txn_id'];
					 $comp_itemtexn   =  $this->db->table($comptxnmst_master)->select('master_id')->where('master_id_type','acc')->where('voucher_txn_id',$voucher_txn_id)->orderBy('txn_id','ASC')->limit(1)->get()->getRowArray(); 
					 if($comp_itemtexn){
					   $party_id  = $comp_itemtexn['master_id'];
					   $accxn_tbl = $this->company_id.'_accnttxnnn_'.$party_id.'_'.$this->session->get('ses_comp_fy_id');
			 
					   $acctxn_res = $this->db->table($accxn_tbl)->select('acc_txn_amount')->where('voucher_txn_id', $voucher_txn_id)->where('voucher_type_id',$voucher_type_id)->where('acc_id', $party_id)->get()->getRowArray();
		               if($acctxn_res)
						   $taxable_amt = $acctxn_res['acc_txn_amount'];
					 }else
						 $taxable_amt =0;
					 
					$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
					$builders = $this->db->table($gstrinwsup_tbl.' gstsup');
					$builders->join($acctgstsum_tbl.' gstsum','gstsum.vch_txn_id=gstsup.voucher_txn_id');
					$builders->select('gstsup.inwsup_bill_ref_no as bill_ref_no');
    				$builders->orderBy('gstsup.voucher_txn_id');	 
    				$builders->where('gstsup.voucher_txn_id', $voucher_txn_id);	
    				$builders->where('gstsup.inwsup_rev_chg',$tableinfo['outsup_rev_chg']);
					$builders->where('gstsup.inwsup_eco',$tableinfo['outsup_eco']);
					$builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    				 $builders->groupBy('gstsup.voucher_txn_id');		 
					 $txncounter = $builders->countAllResults();
					 $total_vch_counters +=$txncounter;					 
					 $tt_total_vch_counters +=$total_vch_counters;					 
				    				
    				$builders = $this->db->table($gstrinwsup_tbl.' gstsup');
					$builders->join($acctgstsum_tbl.' gstsum','gstsum.vch_txn_id=gstsup.voucher_txn_id');
					
    				$builders->select('gstsup.inwsup_bill_ref_no as bill_ref_no');$builders->orderBy('gstsup.voucher_txn_id');	 
    				$builders->where('gstsup.voucher_txn_id', $voucher_txn_id);	
    				$builders->where('gstsup.inwsup_rev_chg',$tableinfo['outsup_rev_chg']);
					$builders->where('gstsup.inwsup_eco',$tableinfo['outsup_eco']);
					$builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    				$response = $builders->get()->getResultArray();
				
				  
				  
				  $total_voucher=0;
			$tt_credit = 0;
	      	$tt_debit = 0;
	      	$tt_credit_total = 0;
	      	$tt_debit_total = 0;
			$tt_taxable_amt =0;
			$tt_acc_igst=0;
			$tt_acc_cgst=0;
			$tt_acc_sgst=0;
			$tt_acc_cess=0;
			$tt_acc_nonadv_cess=0;
			$tt_total_tax=0;
			$tt_total_value=0;
			$tt_total_voucher=0;
			  if($response){
				foreach($response as $txrow){
                    $total_voucher++;											
					$acc_igst    = 0;
					$acc_cgst    = 0;
					$acc_sgst    = 0;
					$acc_cess    = 0;
					$acc_nonadv_cess    = 0;
					$total_tax         = 0;
					$bill_ref_no      = $txrow['bill_ref_no'];
					
					
					$tt_total_voucher +=$total_voucher;
					$tt_taxable_amt +=$taxable_amt;
					$tt_total_tax +=$total_tax;
					$tt_acc_igst +=$acc_igst;				
					$tt_acc_cgst +=$acc_cgst;
					$tt_acc_sgst +=$acc_sgst;				
					$tt_acc_cess +=$acc_cess;
					$tt_acc_nonadv_cess +=$acc_nonadv_cess;
					
					
					
			
				    }
				    
				    $ttt_taxable_amt +=$tt_taxable_amt;
				    $ttt_total_tax +=$tt_total_tax;
				    $ttt_acc_igst +=$tt_acc_igst;
				  	$ttt_acc_cgst +=$tt_acc_cgst;
				    $ttt_acc_sgst +=$tt_acc_sgst;
                    $ttt_acc_cess +=$tt_acc_cess;
				    $ttt_acc_nonadv_cess +=$tt_acc_nonadv_cess;


			      }
			      
			     
				 
				 
		     	}
		      }	
	$total_invoice_value = $ttt_taxable_amt;
		
	$data=["total_vouchers"=>$tt_total_vch_counters,"invoice_value"=>$total_invoice_value,"taxable_value"=>$total_invoice_value,
		      'taxable_amt'=>0,"igst"=>$ttt_acc_igst,"cgst"=>$ttt_acc_cgst,
			  "sgst"=>$ttt_acc_sgst,"cess"=>$ttt_acc_cess,"nonadv_cess"=>$ttt_acc_nonadv_cess,
			       "total_tax"=>$ttt_total_tax
				  ];				  
		return $data;
	    
	}
	function gstr2ab_undefined_table_info($from_date,$to_date,$voucher_result,$tableinfo){
		
	    $acctgstsum_tbl= $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	    $comp_txn_tbl  = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	    $gstrinwsup_tbl  = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
	 
		$comptxnmst_master  =  $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	
		$ttt_taxable_amt=0;
			$ttt_total_tax =0;
			$total_invoice_value=0;
			$ttt_acc_igst =0;
			$ttt_acc_cgst =0;
			$ttt_acc_sgst =0;
            $ttt_acc_cess =0;
			$ttt_acc_nonadv_cess =0;
		    $tt_total_vch_counters=0;
	        $alltxn_ids=array();	 
	        $taxable_amt =0;
		   if($voucher_result){
			  foreach($voucher_result as $row){
			      $total_vch_counters=0;
				 $vch_txn_id = $row['voucher_txn_id'];
				 $voucher_type_id = $row['voucher_type_id'];
				
					 $voucher_txn_id  = $row['voucher_txn_id'];
					 $comp_itemtexn   =  $this->db->table($comptxnmst_master)->select('master_id')->where('master_id_type','acc')->where('voucher_txn_id',$voucher_txn_id)->orderBy('txn_id','ASC')->limit(1)->get()->getRowArray(); 
					 if($comp_itemtexn){
					   $party_id  = $comp_itemtexn['master_id'];
					   $accxn_tbl = $this->company_id.'_accnttxnnn_'.$party_id.'_'.$this->session->get('ses_comp_fy_id');
			 
					   $acctxn_res = $this->db->table($accxn_tbl)->select('acc_txn_amount')->where('voucher_txn_id', $voucher_txn_id)->where('voucher_type_id',$voucher_type_id)->where('acc_id', $party_id)->get()->getRowArray();
		               if($acctxn_res)
						   $taxable_amt = $acctxn_res['acc_txn_amount'];
					 }else
						 $taxable_amt =0;
					 
					$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
					$builders = $this->db->table($gstrinwsup_tbl.' gstsup');
					$builders->join($acctgstsum_tbl.' gstsum','gstsum.vch_txn_id=gstsup.voucher_txn_id');
					$builders->select('gstsup.inwsup_bill_ref_no as bill_ref_no');
    				$builders->orderBy('gstsup.voucher_txn_id');	 
    				$builders->where('gstsup.voucher_txn_id', $voucher_txn_id);	
    				$builders->where('gstsup.inwsup_rev_chg',$tableinfo['outsup_rev_chg']);
					$builders->where('gstsup.inwsup_eco',$tableinfo['outsup_eco']);
					$builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    				 $builders->groupBy('gstsup.voucher_txn_id');		 
					 $txncounter = $builders->countAllResults();
					 $total_vch_counters +=$txncounter;					 
					 $tt_total_vch_counters +=$total_vch_counters;					 
				    				
    				$builders = $this->db->table($gstrinwsup_tbl.' gstsup');
					$builders->join($acctgstsum_tbl.' gstsum','gstsum.vch_txn_id=gstsup.voucher_txn_id');
					
    				$builders->select('gstsup.inwsup_bill_ref_no as bill_ref_no');$builders->orderBy('gstsup.voucher_txn_id');	 
    				$builders->where('gstsup.voucher_txn_id', $voucher_txn_id);	
    				$builders->where('gstsup.inwsup_rev_chg',$tableinfo['outsup_rev_chg']);
					$builders->where('gstsup.inwsup_eco',$tableinfo['outsup_eco']);
					$builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    				$response = $builders->get()->getResultArray();
				
				  
				  
				  $total_voucher=0;
			$tt_credit = 0;
	      	$tt_debit = 0;
	      	$tt_credit_total = 0;
	      	$tt_debit_total = 0;
			$tt_taxable_amt =0;
			$tt_acc_igst=0;
			$tt_acc_cgst=0;
			$tt_acc_sgst=0;
			$tt_acc_cess=0;
			$tt_acc_nonadv_cess=0;
			$tt_total_tax=0;
			$tt_total_value=0;
			$tt_total_voucher=0;
			  if($response){
				foreach($response as $txrow){
                    $total_voucher++;											
					$acc_igst    = 0;
					$acc_cgst    = 0;
					$acc_sgst    = 0;
					$acc_cess    = 0;
					$acc_nonadv_cess    = 0;
					$total_tax         = 0;
					$bill_ref_no      = $txrow['bill_ref_no'];
					
					
					$tt_total_voucher +=$total_voucher;
					$tt_taxable_amt +=$taxable_amt;
					$tt_total_tax +=$total_tax;
					$tt_acc_igst +=$acc_igst;				
					$tt_acc_cgst +=$acc_cgst;
					$tt_acc_sgst +=$acc_sgst;				
					$tt_acc_cess +=$acc_cess;
					$tt_acc_nonadv_cess +=$acc_nonadv_cess;
					
					
					
			
				    }
				    
				    $ttt_taxable_amt +=$tt_taxable_amt;
				    $ttt_total_tax +=$tt_total_tax;
				    $ttt_acc_igst +=$tt_acc_igst;
				  	$ttt_acc_cgst +=$tt_acc_cgst;
				    $ttt_acc_sgst +=$tt_acc_sgst;
                    $ttt_acc_cess +=$tt_acc_cess;
				    $ttt_acc_nonadv_cess +=$tt_acc_nonadv_cess;


			      }
			      
			     
				 
				 
		     	}
		      }	
	$total_invoice_value = $ttt_taxable_amt;
		
	$data=["total_vouchers"=>$tt_total_vch_counters,"invoice_value"=>$total_invoice_value,"taxable_value"=>$total_invoice_value,
		      'taxable_amt'=>0,"igst"=>$ttt_acc_igst,"cgst"=>$ttt_acc_cgst,
			  "sgst"=>$ttt_acc_sgst,"cess"=>$ttt_acc_cess,"nonadv_cess"=>$ttt_acc_nonadv_cess,
			       "total_tax"=>$ttt_total_tax
				  ];				  
		return $data;
	    
	}function gst_undefined_table_info($from_date,$to_date,$voucher_result,$tableinfo){
		
	    $acctgstsum_tbl= $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	    $comp_txn_tbl  = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	    $gstrinwsup_tbl  = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
	 $gstroutsup_tbl     = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
   	    
		$comptxnmst_master  =  $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	
		$ttt_taxable_amt=0;
			$ttt_total_tax =0;
			$total_invoice_value=0;
			$ttt_acc_igst =0;
			$ttt_acc_cgst =0;
			$ttt_acc_sgst =0;
            $ttt_acc_cess =0;
			$ttt_acc_nonadv_cess =0;
		    $tt_total_vch_counters=0;
	        $alltxn_ids=array();	 
	        $taxable_amt =0;
		   if($voucher_result){
			  foreach($voucher_result as $row){
			      $total_vch_counters=0;
				 $vch_txn_id = $row['voucher_txn_id'];
				 $voucher_type_id = $row['voucher_type_id'];
				
					 $voucher_txn_id  = $row['voucher_txn_id'];
					 $comp_itemtexn   =  $this->db->table($comptxnmst_master)->select('master_id')->where('master_id_type','acc')->where('voucher_txn_id',$voucher_txn_id)->orderBy('txn_id','ASC')->limit(1)->get()->getRowArray(); 
					 if($comp_itemtexn){
					   $party_id  = $comp_itemtexn['master_id'];
					   $accxn_tbl = $this->company_id.'_accnttxnnn_'.$party_id.'_'.$this->session->get('ses_comp_fy_id');
			 
					   $acctxn_res = $this->db->table($accxn_tbl)->select('acc_txn_amount')->where('voucher_txn_id', $voucher_txn_id)->where('voucher_type_id',$voucher_type_id)->where('acc_id', $party_id)->get()->getRowArray();
		               if($acctxn_res)
						   $taxable_amt = $acctxn_res['acc_txn_amount'];
					 }else
						 $taxable_amt =0;
					 
					$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
					$builders = $this->db->table($gstroutsup_tbl.' gstsup');
					$builders->join($acctgstsum_tbl.' gstsum','gstsum.vch_txn_id=gstsup.voucher_txn_id');
					$builders->select('gstsup.outsup_bill_ref_no as bill_ref_no');
    				$builders->orderBy('gstsup.voucher_txn_id');	 
    				$builders->where('gstsup.voucher_txn_id', $voucher_txn_id);	
    				$builders->where('gstsup.outsup_rev_chg',$tableinfo['outsup_rev_chg']);
					$builders->where('gstsup.outsup_eco',$tableinfo['outsup_eco']);
					$builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    				 $builders->groupBy('gstsup.voucher_txn_id');		 
					 $txncounter = $builders->countAllResults();
					 $total_vch_counters +=$txncounter;					 
					 $tt_total_vch_counters +=$total_vch_counters;					 
				    				
    				$builders = $this->db->table($gstroutsup_tbl.' gstsup');
					$builders->join($acctgstsum_tbl.' gstsum','gstsum.vch_txn_id=gstsup.voucher_txn_id');
					
    				$builders->select('gstsup.outsup_bill_ref_no as bill_ref_no');$builders->orderBy('gstsup.voucher_txn_id');	 
    				$builders->where('gstsup.voucher_txn_id', $voucher_txn_id);	
    				$builders->where('gstsup.outsup_rev_chg',$tableinfo['outsup_rev_chg']);
					$builders->where('gstsup.outsup_eco',$tableinfo['outsup_eco']);
					$builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    				$response = $builders->get()->getResultArray();
				
				  
				  
				  $total_voucher=0;
			$tt_credit = 0;
	      	$tt_debit = 0;
	      	$tt_credit_total = 0;
	      	$tt_debit_total = 0;
			$tt_taxable_amt =0;
			$tt_acc_igst=0;
			$tt_acc_cgst=0;
			$tt_acc_sgst=0;
			$tt_acc_cess=0;
			$tt_acc_nonadv_cess=0;
			$tt_total_tax=0;
			$tt_total_value=0;
			$tt_total_voucher=0;
			  if($response){
				foreach($response as $txrow){
                    $total_voucher++;											
					$acc_igst    = 0;
					$acc_cgst    = 0;
					$acc_sgst    = 0;
					$acc_cess    = 0;
					$acc_nonadv_cess    = 0;
					$total_tax         = 0;
					$bill_ref_no      = $txrow['bill_ref_no'];
					
					
					$tt_total_voucher +=$total_voucher;
					$tt_taxable_amt +=$taxable_amt;
					$tt_total_tax +=$total_tax;
					$tt_acc_igst +=$acc_igst;				
					$tt_acc_cgst +=$acc_cgst;
					$tt_acc_sgst +=$acc_sgst;				
					$tt_acc_cess +=$acc_cess;
					$tt_acc_nonadv_cess +=$acc_nonadv_cess;
					
					
					
			
				    }
				    
				    $ttt_taxable_amt +=$tt_taxable_amt;
				    $ttt_total_tax +=$tt_total_tax;
				    $ttt_acc_igst +=$tt_acc_igst;
				  	$ttt_acc_cgst +=$tt_acc_cgst;
				    $ttt_acc_sgst +=$tt_acc_sgst;
                    $ttt_acc_cess +=$tt_acc_cess;
				    $ttt_acc_nonadv_cess +=$tt_acc_nonadv_cess;


			      }
			      
			     
				 
				 
		     	}
		      }	
	$total_invoice_value = $ttt_taxable_amt;
		
	$data=["total_vouchers"=>$tt_total_vch_counters,"invoice_value"=>$total_invoice_value,"taxable_value"=>$total_invoice_value,
		      'taxable_amt'=>0,"igst"=>$ttt_acc_igst,"cgst"=>$ttt_acc_cgst,
			  "sgst"=>$ttt_acc_sgst,"cess"=>$ttt_acc_cess,"nonadv_cess"=>$ttt_acc_nonadv_cess,
			       "total_tax"=>$ttt_total_tax
				  ];				  
		return $data;
	    
	}
	
	
	function GetItemTxShrtCd($item_id,$tax_cat_id){
	$company_id= $this->company_id;
	$cmpgstcatn_tbl= $this->company_id.'_cmpgstcatn_'.$this->session->get('ses_comp_fy_id');
	$builder = $this->db->table($cmpgstcatn_tbl);    	
	$builder->select('cmp_tax_cat_id,cmp_tax_short_code');
	$builder->where('comp_id',$company_id);
	$builder->where('cmp_tax_cat_id',$tax_cat_id);
	$builder->orderBy('cmp_tax_cat_id');	
	$builder->limit(1);
	$taxable_result = $builder->get()->getRowArray();	
	if($taxable_result)
		$cmp_tax_short_code = $taxable_result['cmp_tax_short_code'];
	 else 
		$cmp_tax_short_code ='REGSPLY'; 
	 return $cmp_tax_short_code;	 
	}
	
	public function gst_hsnwise_transactions(
        string $from_date,
        string $to_date,
        array  $states,
        array  $result,
        int    $limit      = 10,
        int    $pq_curPage = 1
) {
    /* ── dynamic table names ─────────────────────────────────────────── */
    $fy = $this->session->get('ses_comp_fy_id');

    $gstsum_tbl   = "{$this->company_id}_acctgstsum_{$fy}      gstsum";
    $gstsup_tbl   = "{$this->company_id}_gstroutsup_{$fy}      gstsup";
    $vchconso_tbl = "{$this->company_id}_vhtxnconso_{$fy}     vchc";
    $comptxn_tbl  = "{$this->company_id}_comptxnmst_{$fy}";
    $acct_tbl     = "{$this->company_id}_acctmaster_{$fy}     acc";

    /* ── sub-query: 1 acc-row per voucher (lowest txn_id) ────────────── */
    $sub = $this->db->table("$comptxn_tbl c1")
        ->select('c1.voucher_txn_id, c1.master_id')
        ->join("(SELECT voucher_txn_id, MIN(txn_id) AS first_txn_id
                 FROM {$comptxn_tbl}
                 WHERE master_id_type = 'acc'
                 GROUP BY voucher_txn_id) f",
               'f.voucher_txn_id = c1.voucher_txn_id
                AND f.first_txn_id = c1.txn_id')
        ->where('c1.master_id_type', 'acc')
        ->getCompiledSelect();   // ready to embed

    /* ── build the base query ────────────────────────────────────────── */
    $base = $this->db->table($gstsum_tbl)
        ->join($gstsup_tbl,   'gstsup.voucher_txn_id = gstsum.vch_txn_id')
        ->join($vchconso_tbl, 'vchc.voucher_txn_id   = gstsum.vch_txn_id')

        // ← ONE LEFT JOIN to the derived table (ctm_acc)
        ->join("($sub) ctm_acc", 'ctm_acc.voucher_txn_id = gstsum.vch_txn_id',
               'left', false)

        ->join($acct_tbl, 'acc.acc_id = ctm_acc.master_id', 'left')

        ->where('vchc.bo_id', $this->bo_id)
        ->whereIn('vchc.voucher_type_id', [18])
        ->where('gstsum.acc_txn_date >=', $from_date)
        ->where('gstsum.acc_txn_date <=', $to_date);

    /* ── 1) total voucher rows for pagination ───────────────────────── */
    $totalRecords = (clone $base)
        ->select('COUNT(DISTINCT gstsum.vch_txn_id) AS tot', false)
        ->get()->getRow()->tot ?? 0;

    $totalPages = max(1, (int)ceil($totalRecords / $limit));
    $pq_curPage = max(1, min($pq_curPage, $totalPages));
    $offset     = ($pq_curPage - 1) * $limit;

    /* ── 2) page of voucher-level summaries ─────────────────────────── */
    $rows = (clone $base)
        ->select([
            'gstsum.vch_txn_id                                AS voucher_txn_id',
            'DATE_FORMAT(MIN(gstsum.acc_txn_date),"%d-%m-%Y") AS date',

            // “constant within voucher” columns
            'ANY_VALUE(acc.acc_name)                          AS party',
            'ANY_VALUE(gstsup.outsup_pos)                     AS pos_code',
            'ANY_VALUE(vchc.bo_id)                            AS bo_id',
            'ANY_VALUE(vchc.voucher_type_id)                  AS voucher_type_id',

            // money totals
            'SUM(gstsum.taxable_amt   + gstsum.total_tax)     AS invoice_value',
            'SUM(gstsum.taxable_amt)                          AS taxable_value',
            'SUM(gstsum.acc_igst)                             AS igst',
            'SUM(gstsum.acc_cgst)                             AS cgst',
            'SUM(gstsum.acc_sgst)                             AS sgst',
            'SUM(gstsum.acc_cess)                             AS cess',
            'SUM(gstsum.total_tax)                            AS total_tax',

            // raw “sm_*” (export) fields
            'SUM(gstsum.taxable_amt   + gstsum.total_tax)     AS sm_invoice_value',
            'SUM(gstsum.taxable_amt)                          AS sm_taxable_value',
            'SUM(gstsum.acc_igst)                             AS sm_igst',
            'SUM(gstsum.acc_cgst)                             AS sm_cgst',
            'SUM(gstsum.acc_sgst)                             AS sm_sgst',
            'SUM(gstsum.acc_cess)                             AS sm_cess',
            'SUM(gstsum.total_tax)                            AS sm_total_tax',
        ])
        ->groupBy('gstsum.vch_txn_id')
        ->orderBy('gstsum.vch_txn_id')
        ->limit($limit, $offset)
        ->get()->getResultArray();

    /* ── 3) map POS → state name & format numbers ───────────────────── */
    foreach ($rows as &$r) {
        $r['pos'] = $states[$r['pos_code']] ?? '';
        unset($r['pos_code']);

        $r['invoice_value']  = formatAmount(parseAmount($r['invoice_value']));
        $r['taxable_value']  = formatAmount(parseAmount($r['taxable_value']));
        $r['igst']           = formatAmount(parseAmount($r['igst']));
        $r['cgst']           = formatAmount(parseAmount($r['cgst']));
        $r['sgst']           = formatAmount(parseAmount($r['sgst']));
        $r['cess']           = formatAmount(parseAmount($r['cess']));
        $r['total_tax']      = formatAmount(parseAmount($r['total_tax']));
    }

    return [
        'totalRecords' => $totalRecords,
        'data'         => $rows,
        'curPage'      => $pq_curPage,
    ];
}

	function gst_hsnwise_transactions_16_05_2025($from_date,$to_date,$result,$states,$limit,$pq_curPage){
	 $acctgstsum_tbl  = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	 $comp_txn_tbl    = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	 $gstroutsup_tbl  = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
	 $vhtxn_conso_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
    
	 $builders = $this->db->table($acctgstsum_tbl.' gstsum');
	 $builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
	 $builders->join($vhtxn_conso_tbl.' vchconso', 'vchconso.voucher_txn_id =gstsum.vch_txn_id');
     $builders->select('gstsum.vch_txn_id');
	 $builders->whereIn('vchconso.voucher_type_id',array(18));
     $builders->where('vchconso.bo_id',$this->bo_id);	 
	 $builders->where('gstsum.acc_txn_date >=', $from_date);
	 $builders->where('gstsum.acc_txn_date <=', $to_date);
	 $builders->groupBy('gstsum.vch_txn_id');
	 $builders->orderBy('gstsum.vch_txn_id');	 
	 $total_records = $builders->countAllResults();	
	 if($pq_curPage==0){$pq_curPage=1;}
			$offset = ($limit * ($pq_curPage - 1));
			if ($offset > $total_records){        
				$pq_curPage = ceil($total_records / $limit);
				$offset = ($limit * ($pq_curPage - 1));
			}	
	$builders = $this->db->table($acctgstsum_tbl.' gstsum');
	$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
	$builders->join($vhtxn_conso_tbl.' vchconso', 'vchconso.voucher_txn_id =gstsum.vch_txn_id');
    $builders->whereIn('vchconso.voucher_type_id',array(18));
	$builders->where('vchconso.bo_id',$this->bo_id);	
	$builders->where('gstsum.acc_txn_date >=', $from_date);
	$builders->where('gstsum.acc_txn_date <=', $to_date);
	$builders->orderBy('gstsum.vch_txn_id');			
	$response = $builders->get()->getResultArray();	
	
	$final_response = array();			
	if($response){
	  foreach($response as $row){
	    if(isset($states[$row["outsup_pos"]])) 
           $pos_name        = $states[$row["outsup_pos"]];
        else 			 
		   $pos_name       = "";			
		$party_transaction = $this->get_party_transaction($row['vch_txn_id']);
	    $accountinfo       = $this->get_account_info($party_transaction['acc_id']);
		
		$final_response[$row['vch_txn_id']][]  = array("date"=>date('d-m-Y',strtotime($party_transaction['acc_txn_date'])),
		                           "party"=>$accountinfo['acc_name'],
			    				   "pos"=>$pos_name,
								   "bo_id"=>$party_transaction['bo_id'],
								   "voucher_type_id"=>$party_transaction['voucher_type_id'],
								   "voucher_txn_id"=>$row['voucher_txn_id'],
								   "invoice_value"=>parseAmount($row["taxable_amt"]+$row["total_tax"]),
								   "taxable_value"=>parseAmount($row["taxable_amt"]),
								   "igst"=>parseAmount($row["acc_igst"]),
								   "cgst"=>parseAmount($row["acc_cgst"]),
								   "sgst"=>parseAmount($row["acc_sgst"]),
								   "cess"=>parseAmount($row["acc_cess"]),
								   "total_tax"=>parseAmount($row["total_tax"]),
								   "sm_invoice_value"=>parseAmount($row["taxable_amt"]+$row["total_tax"]),
								   "sm_igst"=>parseAmount($row["acc_igst"]),
								   "sm_cgst"=>parseAmount($row["acc_cgst"]),
								   "sm_sgst"=>parseAmount($row["acc_sgst"]),
								   "sm_cess"=>parseAmount($row["acc_cess"]),
								   "sm_taxable_value"=>parseAmount($row["taxable_amt"]),
								   "sm_total_tax"=>parseAmount($row["total_tax"]),
								 );
			    }					
			}	
		$master_response=array();
		if($final_response){
				 foreach($final_response as $vchtxn_id =>$frow){
					$invoice_value     = array_sum(array_column($frow, 'invoice_value'));
					$val               = array_sum(array_column($frow, 'val')); 
					$taxable_value     = array_sum(array_column($frow, 'taxable_value'));
					$sm_taxable_value  = array_sum(array_column($frow, 'sm_taxable_value'));
					$igst              = array_sum(array_column($frow, 'igst'));
					$cgst              = array_sum(array_column($frow, 'cgst'));
					$sgst              = array_sum(array_column($frow, 'sgst'));
					$cess              = array_sum(array_column($frow, 'cess'));
					$total_tax         = array_sum(array_column($frow, 'total_tax'));
					$sm_invoice_value  = array_sum(array_column($frow, 'sm_invoice_value'));
					$sm_igst           = array_sum(array_column($frow, 'sm_igst'));
					$sm_cgst           = array_sum(array_column($frow, 'sm_cgst'));
					$sm_sgst           = array_sum(array_column($frow, 'sm_sgst'));
					$sm_cess           = array_sum(array_column($frow, 'sm_cess'));
					$sm_total_tax      = array_sum(array_column($frow, 'sm_total_tax'));
					if($frow){
					$final_row=	$frow[0];
					$final_responses  = array("date"=> $final_row['date'],
				                            "party"=>$final_row['party'],
											"pos"=>$final_row['pos'],
											"bo_id"=>$final_row['bo_id'],
											"voucher_type_id"=>$final_row['voucher_type_id'],
											"voucher_txn_id"=>$final_row['voucher_txn_id'],
											"invoice_value"=>formatAmount(parseAmount($invoice_value)),
											"val"=>parseAmount($val),
											"taxable_value"=>formatAmount(parseAmount($taxable_value)),											
											"igst"=>formatAmount(parseAmount($igst)),
											"cgst"=>formatAmount(parseAmount($cgst)),
											"sgst"=>formatAmount(parseAmount($sgst)),
											"cess"=>formatAmount(parseAmount($cess)),
										    "total_tax"=>formatAmount(parseAmount($total_tax)),
										    "sm_invoice_value"=>parseAmount($sm_invoice_value),
											"sm_igst"=>parseAmount($sm_igst),
											"sm_cgst"=>parseAmount($sm_cgst),
											"sm_sgst"=>parseAmount($sm_sgst),
											"sm_cess"=>parseAmount($sm_cess),
										    "sm_total_tax"=>parseAmount($sm_total_tax),
											"sm_taxable_value"=>parseAmount($sm_taxable_value),										    									
										   );
						$master_response[]=$final_responses;				   
										   
					   }
				 }
		}		 
		$totalPages = ceil(count($master_response) / $limit);
		 $page = max(1, min($pq_curPage, $totalPages)); // Clamp to valid range
		 $offset = ($page - 1) * $limit;		 
		 $paginatedItems = array_slice($master_response, $offset, $limit);
	   	return [
			'totalRecords'	=> count($master_response),
			'data'	=> $paginatedItems,
			'curPage' =>$pq_curPage
		    ];
			
	}
	
	function gst_hsncredit_transactions($from_date,$to_date,$result,$states,$limit,$pq_curPage){
	 $acctgstsum_tbl  = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	 $comp_txn_tbl    = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	 $gstroutsup_tbl  = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
	 $voucher_tbl     = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	 
	 $builders = $this->db->table($acctgstsum_tbl.' gstsum');
	 $builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
	 $builders->join($voucher_tbl.' vchtbl','vchtbl.voucher_txn_id=gstsum.vch_txn_id');	 
	 $builders->where('vchtbl.voucher_type_id','2');
	 $builders->where('vchtbl.bo_id',$this->bo_id);
	 $builders->orderBy('gstsum.vch_txn_id');
	 $builders->where('gstsum.acc_txn_date >=', $from_date);
	 $builders->where('gstsum.acc_txn_date <=', $to_date);
	 $total_records = $builders->countAllResults();
	 
	 if($pq_curPage==0){$pq_curPage=1;}
			$offset = ($limit * ($pq_curPage - 1));
			if ($offset > $total_records){        
				$pq_curPage = ceil($total_records / $limit);
				$offset = ($limit * ($pq_curPage - 1));
			}	
	$builders = $this->db->table($acctgstsum_tbl.' gstsum');
	$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
	$builders->join($voucher_tbl.' vchtbl','vchtbl.voucher_txn_id=gstsum.vch_txn_id');	 
	$builders->where('vchtbl.voucher_type_id','2');
	$builders->where('vchtbl.bo_id',$this->bo_id);
	$builders->where('gstsum.acc_txn_date >=', $from_date);
	$builders->where('gstsum.acc_txn_date <=', $to_date);
	$builders->orderBy('gstsum.vch_txn_id');		
	$builders->limit($limit,$offset);
	$response = $builders->get()->getResultArray();			
	$final_response = array();			
	if($response){
	  foreach($response as $row){
	    if(isset($states[$row["outsup_pos"]])) 
           $pos_name        = $states[$row["outsup_pos"]];
        else 			 
		   $pos_name        = "";			
		$party_transaction = $this->get_party_transaction($row['vch_txn_id']);
	    $accountinfo       = $this->get_account_info($party_transaction['acc_id']);
		$final_response[]  = array("date"=>date('d-m-Y',strtotime($party_transaction['acc_txn_date'])),
		                           "party"=>$accountinfo['acc_name'],
			    				   "pos"=>$pos_name,
								   "bo_id"=>$party_transaction['bo_id'],
								   "voucher_type_id"=>$party_transaction['voucher_type_id'],
								   "voucher_txn_id"=>$row['voucher_txn_id'],
								   "invoice_value"=>formatAmount(parseAmount($row["taxable_amt"]+$row["total_tax"])),
								   "taxable_value"=>formatAmount(parseAmount($row["taxable_amt"])),
								   "igst"=>formatAmount(parseAmount($row["acc_igst"])),
								   "cgst"=>formatAmount(parseAmount($row["acc_cgst"])),
								   "sgst"=>formatAmount(parseAmount($row["acc_sgst"])),
								   "cess"=>formatAmount(parseAmount($row["acc_cess"])),
								   "total_tax"=>formatAmount(parseAmount($row["total_tax"])),
								   "sm_invoice_value"=>parseAmount($row["taxable_amt"]+$row["total_tax"]),
											"sm_igst"=>parseAmount($row["acc_igst"]),
											"sm_cgst"=>parseAmount($row["acc_cgst"]),
											"sm_sgst"=>parseAmount($row["acc_sgst"]),
											"sm_cess"=>parseAmount($row["acc_cess"]),
										    "sm_total_tax"=>parseAmount($row["total_tax"]),
								 );
			    }					
			}	
	   	return [
			'totalRecords'	=> $total_records,
			'data'	=> $final_response
		    ];
			
	}
	function gst_hsnnocredit_transactions($from_date,$to_date,$result,$states,$limit,$pq_curPage){
	 $acctgstsum_tbl  = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	 $comp_txn_tbl    = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	 $gstroutsup_tbl  = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
	 $voucher_tbl     = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	 $builders        = $this->db->table($acctgstsum_tbl.' gstsum');
	 $builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
	 $builders->join($voucher_tbl.' vchtbl','vchtbl.voucher_txn_id=gstsum.vch_txn_id');	 
	 $builders->where('vchtbl.voucher_type_id !=','2');
	 $builders->where('vchtbl.bo_id',$this->bo_id);
	 $builders->orderBy('gstsum.vch_txn_id');
	 $builders->where('gstsum.acc_txn_date >=', $from_date);
	 $builders->where('gstsum.acc_txn_date <=', $to_date);
	 $total_records = $builders->countAllResults();
	 
	 if($pq_curPage==0){$pq_curPage=1;}
			$offset = ($limit * ($pq_curPage - 1));
			if ($offset > $total_records){        
				$pq_curPage = ceil($total_records / $limit);
				$offset = ($limit * ($pq_curPage - 1));
			}	
	$builders = $this->db->table($acctgstsum_tbl.' gstsum');
	$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
	$builders->join($voucher_tbl.' vchtbl','vchtbl.voucher_txn_id=gstsum.vch_txn_id');	 
	$builders->where('vchtbl.voucher_type_id !=','2');
	$builders->where('vchtbl.bo_id',$this->bo_id);
	$builders->where('gstsum.acc_txn_date >=', $from_date);
	$builders->where('gstsum.acc_txn_date <=', $to_date);
	$builders->orderBy('gstsum.vch_txn_id');		
	$builders->limit($limit,$offset);
	$response = $builders->get()->getResultArray();			
	$final_response = array();			
	if($response){
	  foreach($response as $row){
	    if(isset($states[$row["outsup_pos"]])) 
           $pos_name        = $states[$row["outsup_pos"]];
        else 			 
		   $pos_name        = "";			
		$party_transaction = $this->get_party_transaction($row['vch_txn_id']);
	    $accountinfo       = $this->get_account_info($party_transaction['acc_id']);
		$final_response[]  = array("date"=>date('d-m-Y',strtotime($party_transaction['acc_txn_date'])),
		                           "party"=>$accountinfo['acc_name'],
			    				   "pos"=>$pos_name,
								   "bo_id"=>$party_transaction['bo_id'],
								   "voucher_type_id"=>$party_transaction['voucher_type_id'],
								   "voucher_txn_id"=>$row['voucher_txn_id'],
								   "invoice_value"=>formatAmount(parseAmount($row["taxable_amt"]+$row["total_tax"])),
								   "taxable_value"=>formatAmount(parseAmount($row["taxable_amt"])),
								   "igst"=>formatAmount(parseAmount($row["acc_igst"])),
								   "cgst"=>formatAmount(parseAmount($row["acc_cgst"])),
								   "sgst"=>formatAmount(parseAmount($row["acc_sgst"])),
								   "cess"=>formatAmount(parseAmount($row["acc_cess"])),
								   "total_tax"=>formatAmount(parseAmount($row["total_tax"])),
								   "sm_invoice_value"=>parseAmount($row["taxable_amt"]+$row["total_tax"]),
											"sm_igst"=>parseAmount($row["acc_igst"]),
											"sm_cgst"=>parseAmount($row["acc_cgst"]),
											"sm_sgst"=>parseAmount($row["acc_sgst"]),
											"sm_cess"=>parseAmount($row["acc_cess"]),
										    "sm_total_tax"=>parseAmount($row["total_tax"]),
								 );
			    }					
			}	
	   	return [
			'totalRecords'	=> $total_records,
			'data'	=> $final_response
		    ];
			
	}
	
	public function get_branch_accnt_info($branch_id){
	 $tbl_name       = $this->company_id.'_hobomaster_'.$this->session->get('ses_comp_fy_id');
	 $acct_tbl_name  = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	 $acctaddmst_tbl = $this->company_id.'_acctaddmst_'.$this->session->get('ses_comp_fy_id');				
	 $builder        = $this->db->table($acct_tbl_name); 
	 $builder->where('acc_grp_parent_id', 14);
	 $builder->where('bo_id', $branch_id);
	 $result   =  $builder->get()->getRowArray();	
	 $response = array();
	 if($result){
		 $account_id =  $result['acc_id'];
		 $res_row    =  $this->db->table($acctaddmst_tbl)
					    ->select('acc_id,acc_state,acc_city,acc_country,acc_add1,acc_add2,acc_pin')
					    ->where('acc_id', $account_id)
					    ->orderBy('acc_id','ASC')
					    ->get()->getRowArray();
		if($res_row){
			$acc_state_code= $this->get_acc_state_code($result['acc_id']);
			$response  = array("Addr1"=>$res_row['acc_add1'],"Addr2"=>$res_row['acc_add2'],"Loc"=>$res_row['acc_city'],"Pin"=>$res_row['acc_pin'],"Stcd"=>$acc_state_code);
		  }										
					  
	   }
	 return $response;
	}
	
	public function get_branchinfo($branch_id){
	    $tbl_name  = $this->company_id.'_hobomaster_'.$this->session->get('ses_comp_fy_id');
		$builder   = $this->db->table($tbl_name); 
		$builder->where('bo_id', $branch_id);
		$result =  $builder->get()->getRowArray();		
		return $result;
	 } 
	 public function get_einvmaster_info($voucher_txn_id){
	    $tbl_name  = $this->company_id.'_einvmaster_'.$this->session->get('ses_comp_fy_id');
		$builder   = $this->db->table($tbl_name); 
		$builder->where('voucher_txn_id', $voucher_txn_id);
		$result =  $builder->get()->getRowArray();		
		return $result;
	 } 
	 
	 
	 /**
 * Summarise GST outward / inward-supply transactions table-wise.
 *
 * → Builds ONE row per voucher directly in SQL, pages in SQL,
 *   and then does the minimal PHP touch-ups (state name + formatting).
 *
 * @param string $from_date     YYYY-MM-DD
 * @param string $to_date       YYYY-MM-DD
 * @param array  $result        (kept for signature; not used here)
 * @param array  $tableinfo     All the flags & filters that used to steer the old code
 * @param array  $states        POS code → state name
 * @param int    $limit
 * @param int    $pq_curPage
 *
 * @return array [ 'totalRecords' => int,
 *                 'data'         => array<row>,
 *                 'curPage'      => int ]
 */
public function gst_table_transactions(
        string $from_date,
        string $to_date,
        array  $result,
        array  $tableinfo,
        array  $states,
        int    $limit      = 10,
        int    $pq_curPage = 1
) {
    /* ─────────── dynamic table names & aliases ─────────── */
    $fy = $this->session->get('ses_comp_fy_id');

    $gstsum_tbl   = "{$this->company_id}_acctgstsum_{$fy}     gstsum";
    $outsup_tbl   = "{$this->company_id}_gstroutsup_{$fy}";
    $inwsup_tbl   = "{$this->company_id}_gstrinwsup_{$fy}";
    $vchconso_tbl = "{$this->company_id}_vhtxnconso_{$fy}     vchc";
    $comptxn_tbl  = "{$this->company_id}_comptxnmst_{$fy}";
    $acct_tbl     = "{$this->company_id}_acctmaster_{$fy}     acc";
    $acctgst_tbl  = "{$this->company_id}_acctgstmst_{$fy}     agst";

    /* ─────────── decide which *supply* table we need ───── */
    $isDR  = ($tableinfo['outsup_dr_note'] ?? '0') === '1';
    $isCR  = ($tableinfo['outsup_cr_note'] ?? '0') === '1';

    if ($isCR) {
        $supTable = $inwsup_tbl;      // credit note → inward sup
        $supAlias = 'gstsup';
        $supType  = 'inwsup';         // prefix for column names
        $voucherTypes = [18, 2];      // sale + credit note
    } else {                         // normal sale or debit note
        $supTable = $outsup_tbl;
        $supAlias = 'gstsup';
        $supType  = 'outsup';
        $voucherTypes = $isDR ? [18, 3] : [18];
    }

    /* ─────────── derived table → first “ACC” row per voucher ───── */
    $subAcc = $this->db->table("$comptxn_tbl c1")
        ->select('c1.voucher_txn_id, c1.master_id')
        ->join("(SELECT voucher_txn_id, MIN(txn_id) AS first_txn_id
                 FROM {$comptxn_tbl}
                 WHERE master_id_type = 'acc'
                 GROUP BY voucher_txn_id) f",
               'f.voucher_txn_id = c1.voucher_txn_id
                AND f.first_txn_id = c1.txn_id')
        ->where('c1.master_id_type', 'acc')
        ->getCompiledSelect();                 // string

    /* ─────────── build the common query once ─────────── */
    $base = $this->db->table($gstsum_tbl)
        ->join("$supTable $supAlias", "$supAlias.voucher_txn_id = gstsum.vch_txn_id")
        ->join($vchconso_tbl,        "vchc.voucher_txn_id     = gstsum.vch_txn_id")
        ->join("($subAcc) ctm_acc",  'ctm_acc.voucher_txn_id  = gstsum.vch_txn_id', 'left', false)
        ->join($acct_tbl,            'acc.acc_id              = ctm_acc.master_id', 'left')
        ->join($acctgst_tbl,         'agst.acc_id             = acc.acc_id',        'left')

        ->where('vchc.bo_id', $this->bo_id)
        ->whereIn('vchc.voucher_type_id', $voucherTypes)

        ->where("$supAlias.{$supType}_rev_chg", $tableinfo['outsup_rev_chg'])
        ->where("$supAlias.{$supType}_eco",     $tableinfo['outsup_eco'])

        ->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code'])
        ->where('gstsum.acc_txn_date >=', $from_date)
        ->where('gstsum.acc_txn_date <=', $to_date);

    /* optional filters lifted straight from your old code */
    if (!empty($tableinfo['outsup_inv_type'])) {
        $base->whereIn("$supAlias.{$supType}_inv_type", $tableinfo['outsup_inv_type']);
    }

    if (!empty($tableinfo['value']) && !empty($tableinfo['cnd'])) {
        $base->where("(gstsum.total_tax + gstsum.taxable_amt) {$tableinfo['cnd']}",
                     $tableinfo['value']);
    }

    /* ─────────── total rows for pagination (COUNT DISTINCT) ─── */
    $totalRecords = (clone $base)
        ->select('COUNT(DISTINCT gstsum.vch_txn_id) AS tot', false)
        ->get()->getRow()->tot ?? 0;

    $totalPages = max(1, (int)ceil($totalRecords / $limit));
    $pq_curPage = max(1, min($pq_curPage, $totalPages));
    $offset     = ($pq_curPage - 1) * $limit;

    /* ─────────── page of voucher-level summaries ───────────── */
    $rows = (clone $base)
        ->select([
            'gstsum.vch_txn_id                                AS voucher_txn_id',
            'DATE_FORMAT(MIN(gstsum.acc_txn_date),"%d-%m-%Y") AS date',

            // constants inside each voucher (ANY_VALUE to satisfy ONLY_FULL_GROUP_BY)
            "ANY_VALUE($supAlias.{$supType}_inv_type)         AS inv_typ",
            "ANY_VALUE($supAlias.{$supType}_bill_ref_no)      AS bill_ref_no",
            "ANY_VALUE($supAlias.{$supType}_pos)              AS pos_code",
            "ANY_VALUE($supAlias.{$supType}_rev_chg)          AS rev_chg",
            'ANY_VALUE(vchc.bo_id)                            AS bo_id',
            'ANY_VALUE(vchc.voucher_type_id)                  AS voucher_type_id',
            'ANY_VALUE(acc.acc_name)                          AS party',
            'ANY_VALUE(agst.acc_gstin)                        AS party_gstin',

            // money
            'SUM(gstsum.taxable_amt   + gstsum.total_tax)     AS invoice_value',
            'SUM(gstsum.taxable_amt)                          AS taxable_value',
            'SUM(gstsum.acc_igst)                             AS igst',
            'SUM(gstsum.acc_cgst)                             AS cgst',
            'SUM(gstsum.acc_sgst)                             AS sgst',
            'SUM(gstsum.acc_cess)                             AS cess',
            'SUM(gstsum.total_tax)                            AS total_tax',

            // raw “sm_*” fields (export / Excel)
            'SUM(gstsum.taxable_amt   + gstsum.total_tax)     AS sm_invoice_value',
            'SUM(gstsum.taxable_amt)                          AS sm_taxable_value',
            'SUM(gstsum.acc_igst)                             AS sm_igst',
            'SUM(gstsum.acc_cgst)                             AS sm_cgst',
            'SUM(gstsum.acc_sgst)                             AS sm_sgst',
            'SUM(gstsum.acc_cess)                             AS sm_cess',
            'SUM(gstsum.total_tax)                            AS sm_total_tax'
        ])
        ->groupBy('gstsum.vch_txn_id')
        ->orderBy('gstsum.vch_txn_id')
        ->limit($limit, $offset)
        ->get()->getResultArray();
		//echo $this->db->getlastquery();

    /* ─────────── PHP touch-ups exactly once per row ───────────── */
    foreach ($rows as &$r) {
        /* POS state name */
        $r['pos'] = $states[$r['pos_code']] ?? '';
        unset($r['pos_code']);

        /* rev_chg flag Y/N */
        $r['rchrg'] = ($r['rev_chg'] ?? '0') === '1' ? 'Y' : 'N';
        unset($r['rev_chg']);

        /* branch alias (one simple helper call) */
        $branch           = $this->get_branchinfo($r['bo_id']);
        $r['branch_name'] = $branch['bo_alias'] ?? '';

        /* amount pretty-print for screen */
        $r['invoice_value'] = formatAmount(parseAmount($r['invoice_value']));
        $r['taxable_value'] = formatAmount(parseAmount($r['taxable_value']));
        $r['igst']          = formatAmount(parseAmount($r['igst']));
        $r['cgst']          = formatAmount(parseAmount($r['cgst']));
        $r['sgst']          = formatAmount(parseAmount($r['sgst']));
        $r['cess']          = formatAmount(parseAmount($r['cess']));
        $r['total_tax']     = formatAmount(parseAmount($r['total_tax']));

        /* build the minimal item-array the JSON export needs */
        $r['itms'] = [[
            'num'      => 1,
            'itm_det'  => [
                'txval' => (float)parseAmount($r['sm_taxable_value']),
                'rt'    => 0.0,
                'iamt'  => (float)parseAmount($r['sm_igst']),
                'cgst'  => (float)parseAmount($r['sm_cgst']),
                'sgst'  => (float)parseAmount($r['sm_sgst']),
                'csamt' => (float)parseAmount($r['sm_cess']),
            ],
        ]];
    }

    return [
        'totalRecords' => $totalRecords,
        'data'         => $rows,
        'curPage'      => $pq_curPage,
    ];
}

	 
	function gst_table_transactions_16_05_2025($from_date,$to_date,$result,$tableinfo,$states,$limit,$pq_curPage){
	 $acctgstsum_tbl  = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	 $comp_txn_tbl    = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	 $gstroutsup_tbl  = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
	 $gstrinwsup_tbl  = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
	 $acctgstmst_tbl  = $this->company_id.'_acctgstmst_'.$this->session->get('ses_comp_fy_id');
	 $vhtxn_conso_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
    
	 //Code for NIL RATED, EXEMPTED AND NON GST
	 if(in_array(array("ZERSPLY","NILSPLY","NONGSTS","EXMSPLY"),$tableinfo)){
		 $this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
					 
		 $builders = $this->db->table($gstroutsup_tbl.' gstsup');
		 $builders->join($acctgstsum_tbl.' gstsum', 'gstsum.vch_txn_id =gstsup.voucher_txn_id');
		 $builders->join($vhtxn_conso_tbl.' vchconso', 'vchconso.voucher_txn_id =gstsup.voucher_txn_id');
		 $builders->select('gstsup.outsup_bill_ref_no as bill_ref_no');
    	 $builders->orderBy('gstsup.voucher_txn_id');	 
		 $builders->where('vchconso.bo_id',$this->bo_id);
    	 $builders->where('gstsup.outsup_rev_chg',$tableinfo['outsup_rev_chg']);
		 $builders->where('gstsup.outsup_eco',$tableinfo['outsup_eco']);
		 $builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    	 $builders->groupBy('gstsup.voucher_txn_id');		 
	     $total_records = $builders->countAllResults();
		  
		 if($limit!='-1'){
	       if($pq_curPage==0){$pq_curPage=1;}
			$offset = ($limit * ($pq_curPage - 1));
			  if ($offset > $total_records){        
				$pq_curPage = ceil($total_records / $limit);
				$offset = ($limit * ($pq_curPage - 1));
			  } 
		  }
		 $builders = $this->db->table($gstroutsup_tbl.' gstsup');
		 $builders->join($acctgstsum_tbl.' gstsum', 'gstsum.vch_txn_id =gstsup.voucher_txn_id');
		 $builders->join($vhtxn_conso_tbl.' vchconso', 'vchconso.voucher_txn_id =gstsup.voucher_txn_id');
		 $builders->orderBy('gstsup.voucher_txn_id');	 
		 $builders->where('vchconso.bo_id',$this->bo_id);
    	 $builders->where('gstsup.outsup_rev_chg',$tableinfo['outsup_rev_chg']);
		 $builders->where('gstsup.outsup_eco',$tableinfo['outsup_eco']);
		 $builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    	 if($limit!='-1'){
		 $builders->limit($limit,$offset);
		 }
		    $response = $builders->get()->getResultArray();	
			
			$final_response = array();
			$inv            = array();	
            $ctin           = array();	
			
			if($response){
			  foreach($response as $row){
				  $itms_data      = array();		
				  if(isset($row["outsup_pos"])){
					if(isset($states[$row["outsup_pos"]])){ 
						$pos_name = $states[$row["outsup_pos"]];
						$pos_id   = $row["outsup_pos"];
						$invoice_type = strtolower($row["outsup_inv_type"]);
						$invoice_no   =$row["outsup_bill_ref_no"];
						$rev_chg   = ($row["outsup_rev_chg"]=="1")?"Y":"N";
					    }
					else{ 			 
						$pos_name = "";
 						$pos_id   = 0;
						$invoice_type = strtolower("B2B");
						$invoice_no   ="";
						$rev_chg   = "N";
					   }
				  }
				  else if(isset($row["outsup_pos"])){
					if(isset($states[$row["outsup_pos"]])){ 
						$pos_name = $states[$row["outsup_pos"]];
						$pos_id   = $row["outsup_pos"];
						$invoice_type = strtolower($row["outsup_inv_type"]);
						$invoice_no   =$row["outsup_bill_ref_no"];
						$rev_chg   = ($row["outsup_rev_chg"]=="1")?"Y":"N";
					}
					else{ 			 
						$pos_name = "";
						$pos_id   =0;
						$invoice_type = strtolower("B2B"); 
						$invoice_no ="";
						$rev_chg   = "N";
					}
				  }
				  
				  
					$itm_invoice_type = 'R';
				  
				
				 $itms_data[] = array("num"=>(integer)$row['outsup_id'],
					                  "itm_det"  => array(
									  "txval"    => 0,
									  "rt"       => 0,
									  "iamt"     => 0,
									  "csamt"    => 0
									   )
									 ); 			   
				 
				 $party_transaction = $this->get_party_transaction($row['voucher_txn_id']);
			
	             $accountinfo       = $this->get_account_info($party_transaction['acc_id']);
				 $date              = date('d-m-Y',strtotime($party_transaction['acc_txn_date']));				 
				 
				 $party_gstin_data = $this->db->table($acctgstmst_tbl)->where('acc_id',$party_transaction['acc_id'])->get()->getRowArray(); 
				if($party_gstin_data)
				 $party_gstin     = $party_gstin_data['acc_gstin'];
			     else 
				 $party_gstin     = "";
			 
			     $invoice_no_value = $party_transaction['acc_txn_amount'];
			     
			    $branch_info  = $this->get_branchinfo($row['bo_id']);
			    $branch_name  = $branch_info['bo_alias'];
								
				 $final_response[$row['voucher_txn_id']][]  = array("date"=> $date,"idt"=>$date,"inum"=>$invoice_no,
				                            "party"=>$accountinfo['acc_name'],
											"pos"=>$pos_name,
											"pos_id"=>$pos_id,
											"party_gstin"=>$party_gstin,											
											"acc_igst_rate"=>0,
											"num"=>(integer)$row['outsup_id'],
											"inv_typ"=>$invoice_type,
											"branch_name" =>$branch_name,
											"itm_invoice_type"=>$itm_invoice_type,
											"bo_id"=>$party_transaction['bo_id'],
											"voucher_type_id"=>$party_transaction['voucher_type_id'],
											"voucher_txn_id"=>$row['voucher_txn_id'],
											"invoice_value"=>$invoice_no_value,
											"val"=>parseAmount($invoice_no_value),
											"rchrg"=>$rev_chg,
											"taxable_value"=>0,
											"igst"=>parseAmount(0),
											"cgst"=>parseAmount(0),
											"sgst"=>parseAmount(0),
											"cess"=>parseAmount(0),
										    "total_tax"=>parseAmount(0),
										    "sm_invoice_value"=>parseAmount($invoice_no_value),
											"sm_igst"=>parseAmount(0),
											"sm_cgst"=>parseAmount(0),
											"sm_sgst"=>parseAmount(0),
											"sm_cess"=>parseAmount(0),
										    "sm_total_tax"=>parseAmount(0),
											"sm_taxable_value"=>0,
										   									
										   );
			    }					
			}
	 }
	 
 else{		
	
		
				if($tableinfo['outsup_dr_note']=="1"){
					$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
	
				    $builders = $this->db->table($acctgstsum_tbl.' gstsum');
					$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
					$builders->join($vhtxn_conso_tbl.' vchconso', 'vchconso.voucher_txn_id =gstsum.vch_txn_id');
					$builders->select('gstsum.vch_txn_id');
					$builders->orderBy('gstsum.vch_txn_id');
					$builders->groupBy('gstsum.vch_txn_id');	
					$builders->whereIn('vchconso.voucher_type_id',array(18,3));	
					$builders->where('vchconso.bo_id',$this->bo_id);
					$builders->where('gstsup.outsup_rev_chg',$tableinfo['outsup_rev_chg']);
					$builders->where('gstsup.outsup_eco',$tableinfo['outsup_eco']);
					$builders->where('gstsup.outsup_dr_note',$tableinfo['outsup_dr_note']);
					$builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
					$builders->whereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);						
					$builders->where('gstsum.acc_txn_date >=', $from_date);
					$builders->where('gstsum.acc_txn_date <=', $to_date);
					$total_records = $builders->countAllResults();
				 }
				else if($tableinfo['outsup_cr_note']=="1"){
					$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
	
				    $builders = $this->db->table($acctgstsum_tbl.' gstsum');
					$builders->join($gstrinwsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
					$builders->join($vhtxn_conso_tbl.' vchconso', 'vchconso.voucher_txn_id =gstsum.vch_txn_id');
					$builders->orderBy('gstsum.vch_txn_id');
					$builders->groupBy('gstsum.vch_txn_id');
					$builders->select('gstsum.vch_txn_id');
					$builders->whereIn('vchconso.voucher_type_id',array(18,2));	
					$builders->where('vchconso.bo_id',$this->bo_id);					
					$builders->where('gstsup.inwsup_rev_chg',$tableinfo['outsup_rev_chg']);
					$builders->where('gstsup.inwsup_eco',$tableinfo['outsup_eco']);
					$builders->where('gstsup.inwsup_cr_note',$tableinfo['outsup_cr_note']);	
					$builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
					$builders->whereIn('gstsup.	inwsup_inv_type',$tableinfo['outsup_inv_type']);	
					$builders->where('gstsum.acc_txn_date >=', $from_date);
					$builders->where('gstsum.acc_txn_date <=', $to_date);
					$total_records = $builders->countAllResults();
				 } 
			
			else if($tableinfo['outsup_dr_note']=="0" && $tableinfo['outsup_cr_note']=="0"){
			$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
		
			$builders = $this->db->table($acctgstsum_tbl.' gstsum');
			$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
			$builders->join($vhtxn_conso_tbl.' vchconso', 'vchconso.voucher_txn_id =gstsum.vch_txn_id');
			$builders->orderBy('gstsum.vch_txn_id');	        
			$builders->groupBy('gstsum.vch_txn_id');
			$builders->select('gstsum.vch_txn_id');
			$builders->where('vchconso.voucher_type_id',18);
			$builders->where('vchconso.bo_id',$this->bo_id);
			$builders->where('gstsup.outsup_rev_chg',$tableinfo['outsup_rev_chg']);
            $builders->where('gstsup.outsup_eco',$tableinfo['outsup_eco']);
			if(in_array("DE",$tableinfo['outsup_inv_type']) && $tableinfo['value']=='0'){
			  $builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
              $builders->orWhereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);	
			}
			elseif(isset($tableinfo['cnd']) && $tableinfo['cnd']!='' && $tableinfo['value']!=''){
			  $builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
              $builders->whereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);	
			  $builders->where('(gstsum.total_tax+gstsum.taxable_amt) '.$tableinfo['cnd'].'',$tableinfo['value']);
			}
			else{
			  $builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
              if(!empty($tableinfo['outsup_inv_type']))
			  $builders->whereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);	
			  }
			$builders->where('gstsum.acc_txn_date >=', $from_date);
			$builders->where('gstsum.acc_txn_date <=', $to_date);
			$total_records = $builders->countAllResults();
			}
		
		
		if($limit!='-1'){
	 if($pq_curPage==0){$pq_curPage=1;}
			$offset = ($limit * ($pq_curPage - 1));
			if ($offset > $total_records){        
				$pq_curPage = ceil($total_records / $limit);
				$offset = ($limit * ($pq_curPage - 1));
			} 
		}
	 	    
				if($tableinfo['outsup_dr_note']=="1"){
				    $builders = $this->db->table($acctgstsum_tbl.' gstsum');
					$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
					$builders->join($vhtxn_conso_tbl.' vchconso', 'vchconso.voucher_txn_id =gstsum.vch_txn_id');
					$builders->orderBy('gstsum.vch_txn_id');
					$builders->select('gstsum.*,gstsup.*,vchconso.bo_id');	
				    $builders->whereIn('vchconso.voucher_type_id',array(18,3));	
				    $builders->where('vchconso.bo_id',$this->bo_id);					
					$builders->where('gstsup.outsup_rev_chg',$tableinfo['outsup_rev_chg']);
					$builders->where('gstsup.outsup_eco',$tableinfo['outsup_eco']);
					$builders->where('gstsup.outsup_dr_note',$tableinfo['outsup_dr_note']);
					$builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
					$builders->whereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);	
					$builders->where('gstsum.acc_txn_date >=', $from_date);
					$builders->where('gstsum.acc_txn_date <=', $to_date);
					$response = $builders->get()->getResultArray();
				 }
				else if($tableinfo['outsup_cr_note']=="1"){
				    $builders = $this->db->table($acctgstsum_tbl.' gstsum');
					$builders->join($gstrinwsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
					$builders->join($vhtxn_conso_tbl.' vchconso', 'vchconso.voucher_txn_id =gstsum.vch_txn_id');
					$builders->orderBy('gstsum.vch_txn_id');
					$builders->select('gstsum.*,gstsup.*,vchconso.bo_id');
					$builders->whereIn('vchconso.voucher_type_id',array(18,2));	
					$builders->where('vchconso.bo_id',$this->bo_id);		
					$builders->where('gstsup.inwsup_rev_chg',$tableinfo['outsup_rev_chg']);
					$builders->where('gstsup.inwsup_eco',$tableinfo['outsup_eco']);
					$builders->where('gstsup.inwsup_cr_note',$tableinfo['outsup_cr_note']);	
					$builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
					$builders->whereIn('gstsup.	inwsup_inv_type',$tableinfo['outsup_inv_type']);	
					$builders->where('gstsum.acc_txn_date >=', $from_date);
					$builders->where('gstsum.acc_txn_date <=', $to_date);
					$response = $builders->get()->getResultArray();
				 } 
			
			else if($tableinfo['outsup_dr_note']=="0" && $tableinfo['outsup_cr_note']=="0"){
			$builders = $this->db->table($acctgstsum_tbl.' gstsum');
			$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
			$builders->join($vhtxn_conso_tbl.' vchconso', 'vchconso.voucher_txn_id =gstsum.vch_txn_id');
			$builders->orderBy('gstsum.vch_txn_id');
            $builders->select('gstsum.*,gstsup.*,vchconso.bo_id');				
			$builders->where('vchconso.voucher_type_id',18);
			$builders->where('vchconso.bo_id',$this->bo_id);	
			$builders->where('gstsup.outsup_rev_chg',$tableinfo['outsup_rev_chg']);
            $builders->where('gstsup.outsup_eco',$tableinfo['outsup_eco']);
			if(in_array("DE",$tableinfo['outsup_inv_type']) && $tableinfo['value']=='0'){
			  $builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
              $builders->orWhereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);	
			}
			elseif(isset($tableinfo['cnd']) && $tableinfo['cnd']!='' && $tableinfo['value']!=''){
			  $builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
              $builders->whereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);	
			  $builders->where('(gstsum.total_tax+gstsum.taxable_amt) '.$tableinfo['cnd'].'',$tableinfo['value']);
			}
			else{
			  $builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
              if(!empty($tableinfo['outsup_inv_type']))
			  $builders->whereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);	
			  }
			$builders->where('gstsum.acc_txn_date >=', $from_date);
			$builders->where('gstsum.acc_txn_date <=', $to_date);
			$response = $builders->get()->getResultArray();	
			}
			/* if($limit!='-1'){
			$builders->limit($limit,$offset);
			} */
				
			
			$final_response = array();
			$inv            = array();	
            $ctin           = array();	
			
			if($response){
			  foreach($response as $row){
				  
				  $itms_data      = array();		
				  if(isset($row["outsup_pos"])){
					if(isset($states[$row["outsup_pos"]])){ 
						$pos_name = $states[$row["outsup_pos"]];
						$pos_id   = $row["outsup_pos"];
						$invoice_type = strtolower($row["outsup_inv_type"]);
						$invoice_no   =$row["outsup_bill_ref_no"];
						$rev_chg   = ($row["outsup_rev_chg"]=="1")?"Y":"N";
					    }
					else{ 			 
						$pos_name = "";
 						$pos_id   = 0;
						$invoice_type = strtolower("B2B");
						$invoice_no   ="";
						$rev_chg   = "N";
					   }
				  }
				  else if(isset($row["inwsup_pos"])){
					if(isset($states[$row["inwsup_pos"]])){ 
						$pos_name = $states[$row["inwsup_pos"]];
						$pos_id   = $row["inwsup_pos"];
						$invoice_type = strtolower($row["inwsup_inv_type"]);
						$invoice_no   =$row["inwsup_bill_ref_no"];
						$rev_chg   = ($row["inwsup_rev_chg"]=="1")?"Y":"N";
					}
					else{ 			 
						$pos_name = "";
						$pos_id   =0;
						$invoice_type = strtolower("B2B"); 
						$invoice_no ="";
						$rev_chg   = "N";
					}
				  }
				  
				  if(in_array('B2B',$tableinfo['outsup_inv_type'])){
					  if($invoice_type=='b2b' || $invoice_type=='b2brcm') 
						  $itm_invoice_type = 'R';
					  if($invoice_type=='de') 
						  $itm_invoice_type = 'DE';
					  if($invoice_type=='sezwp') 
						  $itm_invoice_type = 'SEWP';
					  if($invoice_type=='sezwop') 
						  $itm_invoice_type = 'SEWOP';
					  if($invoice_type=='cbw') 
						  $itm_invoice_type = 'CBW';
				  }
				  else{
					  $itm_invoice_type = 'R';
				  }
				 //$item_details = $this->get_item_transactions($row['vch_txn_id']);
				 $itms_data[] = array("num"=>(integer)$row['tgsmid'],
					                  "itm_det"  => array(
									  "txval"    => (float)parseAmount($row["taxable_amt"]),
									  "rt"       => (float)$row["acc_igst_rate"],
									  "iamt"     => (float)parseAmount($row["acc_igst"]),// IGST AMOUNT
									  "csamt"    => (float)parseAmount($row["acc_cess"])
									   )
									 ); 			   
				 
				 $party_transaction = $this->get_partyo_transaction($row['vch_txn_id']);
				
	             $accountinfo       = $this->get_account_info($party_transaction['acc_id']);
				 $date              = date('d-m-Y',strtotime($row['acc_txn_date']));				 
				 
				 $party_gstin_data = $this->db->table($acctgstmst_tbl)->where('acc_id',$party_transaction['acc_id'])->get()->getRowArray(); 
				if($party_gstin_data)
				 $party_gstin     = $party_gstin_data['acc_gstin'];
			     else 
				 $party_gstin     = "";
			     
			    $branch_info = $this->get_branchinfo($row['bo_id']);
			    $branch_name  = $branch_info['bo_alias'];
								
				 $final_response[$row['vch_txn_id']][]  = array("date"=> $date,"idt"=>$date,"inum"=>$invoice_no,
				                            "party"=>$accountinfo['acc_name'],
											"pos"=>$pos_name,
											"pos_id"=>$pos_id,
											"party_gstin"=>$party_gstin,
											"branch_name"=>$branch_name,			
											"acc_igst_rate"=>(float)$row["acc_igst_rate"],
											"num"=>(integer)$row['tgsmid'],
											"inv_typ"=>$invoice_type,
											"itm_invoice_type"=>$itm_invoice_type,
											"bo_id"=>$party_transaction['bo_id'],
											"voucher_type_id"=>$party_transaction['voucher_type_id'],
											"voucher_txn_id"=>$row['voucher_txn_id'],
											"invoice_value"=>($row["taxable_amt"]+$row["total_tax"]),
											"val"=>parseAmount($row["taxable_amt"]+$row["total_tax"]),
											"rchrg"=>$rev_chg,
											"taxable_value"=>$row["taxable_amt"],
											"igst"=>parseAmount($row["acc_igst"]),
											"cgst"=>parseAmount($row["acc_cgst"]),
											"sgst"=>parseAmount($row["acc_sgst"]),
											"cess"=>parseAmount($row["acc_cess"]),
										    "total_tax"=>parseAmount($row["total_tax"]),
										    "sm_invoice_value"=>parseAmount($row["taxable_amt"]+$row["total_tax"]),
											"sm_igst"=>parseAmount($row["acc_igst"]),
											"sm_cgst"=>parseAmount($row["acc_cgst"]),
											"sm_sgst"=>parseAmount($row["acc_sgst"]),
											"sm_cess"=>parseAmount($row["acc_cess"]),
										    "sm_total_tax"=>parseAmount($row["total_tax"]),
											"sm_taxable_value"=>$row["taxable_amt"],
										   									
										   );
			    }					
			}	
			
		
	}	
	 $master_response=array();
	 $inv=array();
	 $ctin=array();
	
     if($final_response){
		 foreach($final_response as $vchtxn_id =>$frow){
			$invoice_value     = array_sum(array_column($frow, 'invoice_value'));
			$val               = array_sum(array_column($frow, 'val')); 
			$taxable_value     = array_sum(array_column($frow, 'taxable_value'));
			$sm_taxable_value  = array_sum(array_column($frow, 'sm_taxable_value'));
			$igst              = array_sum(array_column($frow, 'igst'));
			$cgst              = array_sum(array_column($frow, 'cgst'));
            $sgst              = array_sum(array_column($frow, 'sgst'));
			$cess              = array_sum(array_column($frow, 'cess'));
			$total_tax         = array_sum(array_column($frow, 'total_tax'));
			$sm_invoice_value  = array_sum(array_column($frow, 'sm_invoice_value'));
			$sm_igst           = array_sum(array_column($frow, 'sm_igst'));
			$sm_cgst           = array_sum(array_column($frow, 'sm_cgst'));
			$sm_sgst           = array_sum(array_column($frow, 'sm_sgst'));
			$sm_cess           = array_sum(array_column($frow, 'sm_cess'));
			$sm_total_tax      = array_sum(array_column($frow, 'sm_total_tax'));
			if($frow){
			$final_row=	$frow[0];
			$itms_data = array("num"=>(integer)$final_row['num'],
					                  "itm_det"  => array(
									  "txval"    => (float)parseAmount($taxable_value),
									  "rt"       => (float)$final_row["acc_igst_rate"],
									  "iamt"     => (float)parseAmount($igst),// IGST AMOUNT
									  "cgst"     => (float)parseAmount($cgst),// IGST AMOUNT
									  "sgst"     => (float)parseAmount($sgst),// IGST AMOUNT
									  "csamt"    => (float)parseAmount($cess)
									   )
									 ); 
			$inv[] = array("inum"=>$final_row['inum'],"idt"=>$final_row['idt'],"val"=>parseAmount($val),
								"pos"=>$final_row['pos'],"rchrg"=>$final_row['rchrg'],"inv_typ"=>$final_row['inv_typ'],
								"itms"=>$itms_data
								);
								
			$ctin[]=array('ctin'=>$final_row['party_gstin'],'inv'=>$inv);
				 
			
			$final_responses  = array("date"=> $final_row['date'],"idt"=>$final_row['idt'],"inum"=>$final_row['inum'],
				                            "party"=>$final_row['party'],
											"pos"=>$final_row['pos'],
											"pos_id"=>$final_row['pos_id'],
											"inv_typ"=>$final_row['inv_typ'],
											"bo_id"=>$final_row['bo_id'],
											"branch_name"=>$final_row['branch_name'],
											"voucher_type_id"=>$final_row['voucher_type_id'],
											"voucher_txn_id"=>$final_row['voucher_txn_id'],
											"invoice_value"=>formatAmount(parseAmount($invoice_value)),
											"val"=>parseAmount($val),
											"rchrg"=>$final_row['rchrg'],
											"taxable_value"=>formatAmount(parseAmount($taxable_value)),											
											"igst"=>formatAmount(parseAmount($igst)),
											"cgst"=>formatAmount(parseAmount($cgst)),
											"sgst"=>formatAmount(parseAmount($sgst)),
											"cess"=>formatAmount(parseAmount($cess)),
										    "total_tax"=>formatAmount(parseAmount($total_tax)),
										    "sm_invoice_value"=>parseAmount($sm_invoice_value),
											"sm_igst"=>parseAmount($sm_igst),
											"sm_cgst"=>parseAmount($sm_cgst),
											"sm_sgst"=>parseAmount($sm_sgst),
											"sm_cess"=>parseAmount($sm_cess),
										    "sm_total_tax"=>parseAmount($sm_total_tax),
											"sm_taxable_value"=>parseAmount($sm_taxable_value),
										    "itms"=>$itms_data,											
										   );
			}
			
			$master_response[]=$final_responses; 
		 }
	 }	
	 $totalItems = count($master_response);
	     if($limit!='-1'){
		 
		 $totalPages = ceil($totalItems / $limit);
		 $page = max(1, min($pq_curPage, $totalPages)); // Clamp to valid range
		 $offset = ($page - 1) * $limit;		 
		 $paginatedItems = array_slice($master_response, $offset, $limit);
	   	 return [
			'totalRecords'	=> $totalItems,
			'data'	=> $paginatedItems			
			];
		 } else{
			return [
			'totalRecords'	=> $totalItems,
			'data'	=> $master_response			
			]; 
		 }
	}
	
	
	
	function gst_crdr_transactions($from_date,$to_date,$result,$tableinfo,$states,$limit,$pq_curPage){
	     $respone1 = $this->gst_debit_transactions($from_date,$to_date,$result,$tableinfo,$states,$limit,$pq_curPage);
	  	  $respone2 = $this->gst_credit_transactions($from_date,$to_date,$result,$tableinfo,$states,$limit,$pq_curPage);
	   return array_merge($respone1,$respone2);
	
	}
	
	function gst_debit_transactions($from_date,$to_date,$result,$tableinfo,$states,$limit,$pq_curPage){
	 $acctgstsum_tbl  = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	 $comp_txn_tbl    = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	 $gstroutsup_tbl  = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
	 $gstrinwsup_tbl  = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
	 $acctgstmst_tbl  = $this->company_id.'_acctgstmst_'.$this->session->get('ses_comp_fy_id');
	 $vhtxn_conso_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
 
	 $this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
	 if(isset($tableinfo['outsup_dr_note']) && $tableinfo['outsup_dr_note']=='1' && isset($tableinfo['outsup_cr_note']) && $tableinfo['outsup_cr_note']=='1'){
				if($tableinfo['outsup_dr_note']=="1"){
				    $builders = $this->db->table($acctgstsum_tbl.' gstsum');
					$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
					$builders->join($vhtxn_conso_tbl.' vchconso', 'vchconso.voucher_txn_id =gstsum.vch_txn_id');
					$builders->orderBy('gstsum.vch_txn_id');
					$builders->where('vchconso.bo_id',$this->bo_id);	
					$builders->where('gstsup.outsup_rev_chg',$tableinfo['outsup_rev_chg']);
					$builders->where('gstsup.outsup_eco',$tableinfo['outsup_eco']);
					$builders->where('gstsup.outsup_dr_note',$tableinfo['outsup_dr_note']);
					$builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
					$builders->whereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);						
					$builders->where('gstsum.acc_txn_date >=', $from_date);
					$builders->where('gstsum.acc_txn_date <=', $to_date);
				 }
				
			}
			else{
			$builders = $this->db->table($acctgstsum_tbl.' gstsum');
			$builders->join($vhtxn_conso_tbl.' vchconso', 'vchconso.voucher_txn_id =gstsum.vch_txn_id');
			$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
			$builders->orderBy('gstsum.vch_txn_id');	
            $builders->where('vchconso.bo_id',$this->bo_id);				
			$builders->where('gstsup.outsup_rev_chg',$tableinfo['outsup_rev_chg']);
            $builders->where('gstsup.outsup_eco',$tableinfo['outsup_eco']);
			if(in_array("DE",$tableinfo['outsup_inv_type']) && $tableinfo['value']=='0'){
			  $builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
              $builders->orWhereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);	
			}
			elseif(isset($tableinfo['cnd']) && $tableinfo['cnd']!='' && $tableinfo['value']!=''){
			  $builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
              $builders->whereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);	
			  $builders->where('(gstsum.total_tax+gstsum.taxable_amt) '.$tableinfo['cnd'].'',$tableinfo['value']);
			}
			else{
			  $builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
              $builders->whereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);	
			  }
			$builders->where('gstsum.acc_txn_date >=', $from_date);
			$builders->where('gstsum.acc_txn_date <=', $to_date); 
			}
		$total_records = $builders->countAllResults();	
		
		
		if($limit!='-1'){
	 if($pq_curPage==0){$pq_curPage=1;}
			$offset = ($limit * ($pq_curPage - 1));
			if ($offset > $total_records){        
				$pq_curPage = ceil($total_records / $limit);
				$offset = ($limit * ($pq_curPage - 1));
			} 
		}
	 	    
			if(isset($tableinfo['outsup_dr_note']) && $tableinfo['outsup_dr_note']=='1' && isset($tableinfo['outsup_cr_note']) && $tableinfo['outsup_cr_note']=='1'){
				if($tableinfo['outsup_dr_note']=="1"){
				    $builders = $this->db->table($acctgstsum_tbl.' gstsum');
					$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
					$builders->join($vhtxn_conso_tbl.' vchconso', 'vchconso.voucher_txn_id =gstsum.vch_txn_id');
					$builders->orderBy('gstsum.vch_txn_id');	
					$builders->where('vchconso.bo_id',$this->bo_id);	
					$builders->where('gstsup.outsup_rev_chg',$tableinfo['outsup_rev_chg']);
					$builders->where('gstsup.outsup_eco',$tableinfo['outsup_eco']);
					$builders->where('gstsup.outsup_dr_note',$tableinfo['outsup_dr_note']);
					$builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
					$builders->whereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);	
					$builders->where('gstsum.acc_txn_date >=', $from_date);
					$builders->where('gstsum.acc_txn_date <=', $to_date);
				 }
				
			}
			else{
			$builders = $this->db->table($acctgstsum_tbl.' gstsum');
			$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
			$builders->join($vhtxn_conso_tbl.' vchconso', 'vchconso.voucher_txn_id =gstsum.vch_txn_id');
			$builders->orderBy('gstsum.vch_txn_id');
			$builders->where('vchconso.bo_id',$this->bo_id);	
			$builders->where('gstsup.outsup_rev_chg',$tableinfo['outsup_rev_chg']);
            $builders->where('gstsup.outsup_eco',$tableinfo['outsup_eco']);
			if(in_array("DE",$tableinfo['outsup_inv_type']) && $tableinfo['value']=='0'){
			  $builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
              $builders->orWhereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);	
			}
			elseif(isset($tableinfo['cnd']) && $tableinfo['cnd']!='' && $tableinfo['value']!=''){
			  $builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
              $builders->whereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);	
			  $builders->where('(gstsum.total_tax+gstsum.taxable_amt) '.$tableinfo['cnd'].'',$tableinfo['value']);
			}
			else{
			  $builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
              $builders->whereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);	
			  }
			$builders->where('gstsum.acc_txn_date >=', $from_date);
			$builders->where('gstsum.acc_txn_date <=', $to_date);  
			}
			if($limit!='-1'){
			$builders->limit($limit,$offset);
			}
			$response = $builders->get()->getResultArray();
			
			$final_response = array();
			$inv            = array();	
            $ctin           = array();	
			if($response){
			  foreach($response as $row){
				  $itms_data      = array();		
				  if(isset($row["outsup_pos"])){
					if(isset($states[$row["outsup_pos"]])){ 
						$pos_name = $states[$row["outsup_pos"]];
						$pos_id   = $row["outsup_pos"];
						$invoice_type = strtolower($row["outsup_inv_type"]);
						$invoice_no   =$row["outsup_bill_ref_no"];
						$rev_chg   = ($row["outsup_rev_chg"]=="1")?"Y":"N";
					    }
					else{ 			 
						$pos_name = "";
 						$pos_id   = 0;
						$invoice_type = strtolower("B2B");
						$invoice_no   ="";
						$rev_chg   = "N";
					   }
				  }
				  else if(isset($row["inwsup_pos"])){
					if(isset($states[$row["inwsup_pos"]])){ 
						$pos_name = $states[$row["inwsup_pos"]];
						$pos_id   = $row["inwsup_pos"];
						$invoice_type = strtolower($row["inwsup_inv_type"]);
						$invoice_no   =$row["inwsup_bill_ref_no"];
						$rev_chg   = ($row["inwsup_rev_chg"]=="1")?"Y":"N";
					}
					else{ 			 
						$pos_name = "";
						$pos_id   =0;
						$invoice_type = strtolower("B2B"); 
						$invoice_no ="";
						$rev_chg   = "N";
					}
				  }
				  
				  if(in_array('B2B',$tableinfo['outsup_inv_type'])){
					  if($invoice_type=='b2b' || $invoice_type=='b2brcm') 
						  $itm_invoice_type = 'R';
					  if($invoice_type=='de') 
						  $itm_invoice_type = 'DE';
					  if($invoice_type=='sezwp') 
						  $itm_invoice_type = 'SEWP';
					  if($invoice_type=='sezwop') 
						  $itm_invoice_type = 'SEWOP';
					  if($invoice_type=='cbw') 
						  $itm_invoice_type = 'CBW';
				  }
				  else{
					  $itm_invoice_type = 'R';
				  }
				 //$item_details = $this->get_item_transactions($row['vch_txn_id']);
				 $itms_data[] = array("num"=>(integer)$row['tgsmid'],
					                  "itm_det"  => array(
									  "txval"    => (float)parseAmount($row["taxable_amt"]),
									  "rt"       => (float)$row["acc_igst_rate"],
									  "iamt"     => (float)parseAmount($row["acc_igst"]),// IGST AMOUNT
									  "csamt"    => (float)parseAmount($row["acc_cess"])
									   )
									 ); 			   
				 
				 $party_transaction = $this->get_party_transaction($row['vch_txn_id']);
	             $accountinfo       = $this->get_account_info($party_transaction['acc_id']);
				 $date              = date('d-m-Y',strtotime($party_transaction['acc_txn_date']));				 
				 
				 $party_gstin_data = $this->db->table($acctgstmst_tbl)->where('acc_id',$party_transaction['acc_id'])->get()->getRowArray(); 
				if($party_gstin_data)
				 $party_gstin     = $party_gstin_data['acc_gstin'];
			     else 
				 $party_gstin     = "";
			     
			    
				 $inv[] = array("inum"=>$invoice_no,"idt"=>$date,"val"=>parseAmount($row["taxable_amt"]+$row["total_tax"]),
								"pos"=>$pos_id,"rchrg"=>$rev_chg,"inv_typ"=>$itm_invoice_type,
								"itms"=>$itms_data
								);
								
				 $ctin[]=array('ctin'=>$party_gstin,'inv'=>$inv);				
				 $final_response[]  = array("date"=> $date,"idt"=>$date,"inum"=>$invoice_no,
				                            "party"=>$accountinfo['acc_name'],
											"pos"=>$pos_name,
											"pos_id"=>$pos_id,
											"inv_typ"=>$invoice_type,
											"bo_id"=>$party_transaction['bo_id'],
											"voucher_type_id"=>$party_transaction['voucher_type_id'],
											"voucher_txn_id"=>$row['voucher_txn_id'],
											"invoice_value"=>formatAmount(parseAmount($row["taxable_amt"]+$row["total_tax"])),
											"val"=>parseAmount($row["taxable_amt"]+$row["total_tax"]),
											"rchrg"=>$rev_chg,
											"taxable_value"=>formatAmount(parseAmount($row["taxable_amt"])),
											"igst"=>formatAmount(parseAmount($row["acc_igst"])),
											"cgst"=>formatAmount(parseAmount($row["acc_cgst"])),
											"sgst"=>formatAmount(parseAmount($row["acc_sgst"])),
											"cess"=>formatAmount(parseAmount($row["acc_cess"])),
										    "total_tax"=>formatAmount(parseAmount($row["total_tax"])),
										    "sm_invoice_value"=>parseAmount($row["taxable_amt"]+$row["total_tax"]),
											"sm_igst"=>parseAmount($row["acc_igst"]),
											"sm_cgst"=>parseAmount($row["acc_cgst"]),
											"sm_sgst"=>parseAmount($row["acc_sgst"]),
											"sm_cess"=>parseAmount($row["acc_cess"]),
										    "sm_total_tax"=>parseAmount($row["total_tax"]),
										    
										    "itms"=>$itms_data
										   );
			    }					
			}	
	   	return [
			'totalRecords'	=> $total_records,
			'data'	=> $final_response,
			'inv'  => $ctin
			

		    ];
	}
	function gst_credit_transactions($from_date,$to_date,$result,$tableinfo,$states,$limit,$pq_curPage){
	 $acctgstsum_tbl  = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	 $comp_txn_tbl    = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	 $gstroutsup_tbl  = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
	 $gstrinwsup_tbl  = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
	 $acctgstmst_tbl  = $this->company_id.'_acctgstmst_'.$this->session->get('ses_comp_fy_id');
	 $vhtxn_conso_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');

	 $this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
	 if(isset($tableinfo['outsup_dr_note']) && $tableinfo['outsup_dr_note']=='1' && isset($tableinfo['outsup_cr_note']) && $tableinfo['outsup_cr_note']=='1'){
				
				if($tableinfo['outsup_cr_note']=="1"){
				    $builders = $this->db->table($acctgstsum_tbl.' gstsum');
					$builders->join($gstrinwsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
					$builders->join($vhtxn_conso_tbl.' vchconso', 'vchconso.voucher_txn_id =gstsum.vch_txn_id');
                    $builders->orderBy('gstsum.vch_txn_id');
                    $builders->where('vchconso.bo_id',$this->bo_id);					
					$builders->where('gstsup.inwsup_rev_chg',$tableinfo['outsup_rev_chg']);
					$builders->where('gstsup.inwsup_eco',$tableinfo['outsup_eco']);
					$builders->where('gstsup.inwsup_cr_note',$tableinfo['outsup_cr_note']);	
					$builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
					$builders->whereIn('gstsup.	inwsup_inv_type',$tableinfo['outsup_inv_type']);	
					$builders->where('gstsum.acc_txn_date >=', $from_date);
					$builders->where('gstsum.acc_txn_date <=', $to_date);
				 } 
			}
			else{
			$builders = $this->db->table($acctgstsum_tbl.' gstsum');
			$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
			$builders->join($vhtxn_conso_tbl.' vchconso', 'vchconso.voucher_txn_id =gstsum.vch_txn_id');
            $builders->orderBy('gstsum.vch_txn_id');
			$builders->where('vchconso.bo_id',$this->bo_id);	
			$builders->where('gstsup.outsup_rev_chg',$tableinfo['outsup_rev_chg']);
            $builders->where('gstsup.outsup_eco',$tableinfo['outsup_eco']);
			if(in_array("DE",$tableinfo['outsup_inv_type']) && $tableinfo['value']=='0'){
			  $builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
              $builders->orWhereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);	
			}
			elseif(isset($tableinfo['cnd']) && $tableinfo['cnd']!='' && $tableinfo['value']!=''){
			  $builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
              $builders->whereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);	
			  $builders->where('(gstsum.total_tax+gstsum.taxable_amt) '.$tableinfo['cnd'].'',$tableinfo['value']);
			}
			else{
			  $builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
              $builders->whereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);	
			  }
			$builders->where('gstsum.acc_txn_date >=', $from_date);
			$builders->where('gstsum.acc_txn_date <=', $to_date); 
			}
		$total_records = $builders->countAllResults();	
		
		
		if($limit!='-1'){
	 if($pq_curPage==0){$pq_curPage=1;}
			$offset = ($limit * ($pq_curPage - 1));
			if ($offset > $total_records){        
				$pq_curPage = ceil($total_records / $limit);
				$offset = ($limit * ($pq_curPage - 1));
			} 
		}
	 	    
			if(isset($tableinfo['outsup_dr_note']) && $tableinfo['outsup_dr_note']=='1' && isset($tableinfo['outsup_cr_note']) && $tableinfo['outsup_cr_note']=='1'){
				
				if($tableinfo['outsup_cr_note']=="1"){
				    $builders = $this->db->table($acctgstsum_tbl.' gstsum');
					$builders->join($gstrinwsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
					$builders->join($vhtxn_conso_tbl.' vchconso', 'vchconso.voucher_txn_id =gstsum.vch_txn_id');
					$builders->orderBy('gstsum.vch_txn_id');	        
					$builders->where('vchconso.bo_id',$this->bo_id);
					$builders->where('gstsup.inwsup_rev_chg',$tableinfo['outsup_rev_chg']);
					$builders->where('gstsup.inwsup_eco',$tableinfo['outsup_eco']);
					$builders->where('gstsup.inwsup_cr_note',$tableinfo['outsup_cr_note']);	
					$builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
					$builders->whereIn('gstsup.	inwsup_inv_type',$tableinfo['outsup_inv_type']);	
					$builders->where('gstsum.acc_txn_date >=', $from_date);
					$builders->where('gstsum.acc_txn_date <=', $to_date);
				 } 
			}
			else{
			$builders = $this->db->table($acctgstsum_tbl.' gstsum');
			$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
			$builders->join($vhtxn_conso_tbl.' vchconso', 'vchconso.voucher_txn_id =gstsum.vch_txn_id');
			$builders->orderBy('gstsum.vch_txn_id');
			$builders->where('vchconso.bo_id',$this->bo_id);	
			$builders->where('gstsup.outsup_rev_chg',$tableinfo['outsup_rev_chg']);
            $builders->where('gstsup.outsup_eco',$tableinfo['outsup_eco']);
			if(in_array("DE",$tableinfo['outsup_inv_type']) && $tableinfo['value']=='0'){
			  $builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
              $builders->orWhereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);	
			}
			elseif(isset($tableinfo['cnd']) && $tableinfo['cnd']!='' && $tableinfo['value']!=''){
			  $builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
              $builders->whereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);	
			  $builders->where('(gstsum.total_tax+gstsum.taxable_amt) '.$tableinfo['cnd'].'',$tableinfo['value']);
			}
			else{
			  $builders->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
              $builders->whereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);	
			  }
			$builders->where('gstsum.acc_txn_date >=', $from_date);
			$builders->where('gstsum.acc_txn_date <=', $to_date);  
			}
			if($limit!='-1'){
			$builders->limit($limit,$offset);
			}
			$response = $builders->get()->getResultArray();
			
			$final_response = array();
			$inv            = array();	
            $ctin           = array();	
			if($response){
			  foreach($response as $row){
				  $itms_data      = array();		
				  if(isset($row["outsup_pos"])){
					if(isset($states[$row["outsup_pos"]])){ 
						$pos_name = $states[$row["outsup_pos"]];
						$pos_id   = $row["outsup_pos"];
						$invoice_type = strtolower($row["outsup_inv_type"]);
						$invoice_no   =$row["outsup_bill_ref_no"];
						$rev_chg   = ($row["outsup_rev_chg"]=="1")?"Y":"N";
					    }
					else{ 			 
						$pos_name = "";
 						$pos_id   = 0;
						$invoice_type = strtolower("B2B");
						$invoice_no   ="";
						$rev_chg   = "N";
					   }
				  }
				  else if(isset($row["inwsup_pos"])){
					if(isset($states[$row["inwsup_pos"]])){ 
						$pos_name = $states[$row["inwsup_pos"]];
						$pos_id   = $row["inwsup_pos"];
						$invoice_type = strtolower($row["inwsup_inv_type"]);
						$invoice_no   =$row["inwsup_bill_ref_no"];
						$rev_chg   = ($row["inwsup_rev_chg"]=="1")?"Y":"N";
					}
					else{ 			 
						$pos_name = "";
						$pos_id   =0;
						$invoice_type = strtolower("B2B"); 
						$invoice_no ="";
						$rev_chg   = "N";
					}
				  }
				  
				  if(in_array('B2B',$tableinfo['outsup_inv_type'])){
					  if($invoice_type=='b2b' || $invoice_type=='b2brcm') 
						  $itm_invoice_type = 'R';
					  if($invoice_type=='de') 
						  $itm_invoice_type = 'DE';
					  if($invoice_type=='sezwp') 
						  $itm_invoice_type = 'SEWP';
					  if($invoice_type=='sezwop') 
						  $itm_invoice_type = 'SEWOP';
					  if($invoice_type=='cbw') 
						  $itm_invoice_type = 'CBW';
				  }
				  else{
					  $itm_invoice_type = 'R';
				  }
				 //$item_details = $this->get_item_transactions($row['vch_txn_id']);
				 $itms_data[] = array("num"=>(integer)$row['tgsmid'],
					                  "itm_det"  => array(
									  "txval"    => (float)parseAmount($row["taxable_amt"]),
									  "rt"       => (float)$row["acc_igst_rate"],
									  "iamt"     => (float)parseAmount($row["acc_igst"]),// IGST AMOUNT
									  "csamt"    => (float)parseAmount($row["acc_cess"])
									   )
									 ); 			   
				 
				 $party_transaction = $this->get_party_transaction($row['vch_txn_id']);
	             $accountinfo       = $this->get_account_info($party_transaction['acc_id']);
				 $date              = date('d-m-Y',strtotime($party_transaction['acc_txn_date']));				 
				 
				 $party_gstin_data = $this->db->table($acctgstmst_tbl)->where('acc_id',$party_transaction['acc_id'])->get()->getRowArray(); 
				if($party_gstin_data)
				 $party_gstin     = $party_gstin_data['acc_gstin'];
			     else 
				 $party_gstin     = "";
			     
			    
				 $inv[] = array("inum"=>$invoice_no,"idt"=>$date,"val"=>parseAmount($row["taxable_amt"]+$row["total_tax"]),
								"pos"=>$pos_id,"rchrg"=>$rev_chg,"inv_typ"=>$itm_invoice_type,
								"itms"=>$itms_data
								);
								
				 $ctin[]=array('ctin'=>$party_gstin,'inv'=>$inv);				
				 $final_response[]  = array("date"=> $date,"idt"=>$date,"inum"=>$invoice_no,
				                            "party"=>$accountinfo['acc_name'],
											"pos"=>$pos_name,
											"pos_id"=>$pos_id,
											"inv_typ"=>$invoice_type,
											"bo_id"=>$party_transaction['bo_id'],
											"voucher_type_id"=>$party_transaction['voucher_type_id'],
											"voucher_txn_id"=>$row['voucher_txn_id'],
											"invoice_value"=>formatAmount(parseAmount($row["taxable_amt"]+$row["total_tax"])),
											"val"=>parseAmount($row["taxable_amt"]+$row["total_tax"]),
											"rchrg"=>$rev_chg,
											"taxable_value"=>formatAmount(parseAmount($row["taxable_amt"])),
											"igst"=>formatAmount(parseAmount($row["acc_igst"])),
											"cgst"=>formatAmount(parseAmount($row["acc_cgst"])),
											"sgst"=>formatAmount(parseAmount($row["acc_sgst"])),
											"cess"=>formatAmount(parseAmount($row["acc_cess"])),
										    "total_tax"=>formatAmount(parseAmount($row["total_tax"])),
										    "sm_invoice_value"=>parseAmount($row["taxable_amt"]+$row["total_tax"]),
											"sm_igst"=>parseAmount($row["acc_igst"]),
											"sm_cgst"=>parseAmount($row["acc_cgst"]),
											"sm_sgst"=>parseAmount($row["acc_sgst"]),
											"sm_cess"=>parseAmount($row["acc_cess"]),
										    "sm_total_tax"=>parseAmount($row["total_tax"]),
										    "itms"=>$itms_data
										   );
			    }					
			}	
	   	return [
			'totalRecords'	=> $total_records,
			'data'	=> $final_response,
			'inv'  => $ctin
			

		    ];
	}
	function get_acctgstsum_info($voucher_txn_id){
	  $acctgstsum_tbl  = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	  $builders = $this->db->table($acctgstsum_tbl.' gstsum'); 
      $builders->where('gstsum.vch_txn_id',$voucher_txn_id);	  
	  $builders->orderBy('gstsum.vch_txn_id');	
	  $response = $builders->get()->getResultArray();
	  $CgstVal=0;
	  $SgstVal=0;
	  $IgstVal=0;
	  $CesVal=0;
	  $StCesVal=0;
	  $TotInvVal=0;
	  if($response){
		 foreach($response as $row){
			$CgstVal +=$row['acc_cgst']; 
			$SgstVal +=$row['acc_sgst'];
			$IgstVal +=$row['acc_igst'];
			$CesVal  +=$row['acc_cess'];
			$TotInvVal +=($row['taxable_amt']+$row['total_tax']);			
		 } 
	  }
	  $response = array('CgstVal'=>$CgstVal,'SgstVal'=>$SgstVal,'IgstVal'=>$IgstVal,
	                    'CesVal'=>$CesVal,'TotInvVal'=>$TotInvVal
					   );
	  return $response;
	}
	
	function gst_supplies_transactions($from_date,$to_date,$result,$tableinfo,$states,$limit,$pq_curPage,$type){
	 $acctgstsum_tbl  = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	 $comp_txn_tbl    = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	 $gstroutsup_tbl  = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
	 $gstrinwsup_tbl  = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
	 $vhtxn_conso_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');

	 
	 $this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
		
			$builders = $this->db->table($acctgstsum_tbl.' gstsum');
			$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
			$builders->join($vhtxn_conso_tbl.' vchconso', 'vchconso.voucher_txn_id =gstsum.vch_txn_id');
			$builders->orderBy('gstsum.vch_txn_id');
			$builders->where('vchconso.bo_id',$this->bo_id);	
			$builders->where('gstsup.outsup_rev_chg',$tableinfo['outsup_rev_chg']);
			if($type==14)
            $builders->where('gstsup.outsup_eco',$tableinfo['outsup_eco']);
			$builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
			$builders->where('gstsum.acc_txn_date >=', $from_date);
			$builders->where('gstsum.acc_txn_date <=', $to_date); 
			
		$total_records = $builders->countAllResults();	
	   if($pq_curPage==0){$pq_curPage=1;}
			$offset = ($limit * ($pq_curPage - 1));
			if ($offset > $total_records){        
				$pq_curPage = ceil($total_records / $limit);
				$offset = ($limit * ($pq_curPage - 1));
			} 
	 	    
			$builders = $this->db->table($acctgstsum_tbl.' gstsum');
			$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
			$builders->join($vhtxn_conso_tbl.' vchconso', 'vchconso.voucher_txn_id =gstsum.vch_txn_id');
			$builders->orderBy('gstsum.vch_txn_id');	        
			$builders->where('gstsup.outsup_rev_chg',$tableinfo['outsup_rev_chg']);
            if($type==14)
			$builders->where('gstsup.outsup_eco',$tableinfo['outsup_eco']);
		    $builders->where('vchconso.bo_id',$this->bo_id);
			$builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
           	$builders->where('gstsum.acc_txn_date >=', $from_date);
			$builders->where('gstsum.acc_txn_date <=', $to_date);
			$builders->limit($limit,$offset);
			$response = $builders->get()->getResultArray();
			
			$final_response = array();			
			if($response){
			  foreach($response as $row){
				 if(isset($states[$row["outsup_pos"]])) 
                 $pos_name        = $states[$row["outsup_pos"]];
                  else 			 
			     $pos_name        = "";			
			 
				 $party_transaction = $this->get_party_transaction($row['vch_txn_id']);
	             $accountinfo       = $this->get_account_info($party_transaction['acc_id']);
				 $final_response[]  = array("date"=>date('d-m-Y',strtotime($party_transaction['acc_txn_date'])),
				                            "party"=>$accountinfo['acc_name'],
											"pos"=>$pos_name,
											"bo_id"=>$party_transaction['bo_id'],
											"voucher_type_id"=>$party_transaction['voucher_type_id'],
											"voucher_txn_id"=>$row['voucher_txn_id'],
											"invoice_value"=>formatAmount(parseAmount($row["taxable_amt"]+$row["total_tax"])),
											"taxable_value"=>formatAmount(parseAmount($row["taxable_amt"])),
											"igst"=>formatAmount(parseAmount($row["acc_igst"])),
											"cgst"=>formatAmount(parseAmount($row["acc_cgst"])),
											"sgst"=>formatAmount(parseAmount($row["acc_sgst"])),
											"cess"=>formatAmount(parseAmount($row["acc_cess"])),
										    "total_tax"=>formatAmount(parseAmount($row["total_tax"]))
										   );
			    }					
			}	
	   	return [
			'totalRecords'	=> $total_records,
			'data'	=> $final_response
		    ];
	}
	
   function gstsummary_ratewise_outputtax_table_info($from_date,$to_date,$vouchers_result,$igst_rate){
	        $acctgstsum_tbl     = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	        $comp_txn_tbl       = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	        $gstroutsup_tbl     = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
	        $gstrinwsup_tbl     = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
	       	
			$total_counter= array();
			$ttt_taxable_amt=0;
			$ttt_total_tax =0;
			$total_invoice_value=0;
			$ttt_acc_igst =0;
			$ttt_acc_cgst =0;
			$ttt_acc_sgst =0;
            $ttt_acc_cess =0;
			$ttt_acc_nonadv_cess =0;
		    $tt_total_vch_counters=0;
			if($vouchers_result){
			foreach($vouchers_result as $row){
			    $response=array();
				$voucher_txn_id    = $row['voucher_txn_id'];
				$voucher_type_id   = $row['voucher_type_id'];
				$total_vch_counters=0;
					
			     if($voucher_type_id==18){
					 
					 $response = $this->db->query("SELECT 
							`gstsup`.`outsup_bill_ref_no` AS `bill_ref_no`, 
							`gstsum`.`taxable_amt`, 
							`gstsum`.`acc_igst`, 
							`gstsum`.`acc_cgst`, 
							`gstsum`.`acc_sgst`, 
							`gstsum`.`acc_cess`, 
							`gstsum`.`acc_nonadv_cess`, 
							`gstsum`.`total_tax`, 
							`gstsum`.`acc_txn_date`, 
							CASE 
								WHEN gstsum.acc_igst_rate = 0 THEN (gstsum.acc_cgst_rate + gstsum.acc_sgst_rate) 
								ELSE gstsum.acc_igst_rate 
							END AS `rate_value`
						FROM 
							`".$acctgstsum_tbl."` `gstsum` 
						JOIN 
							`".$gstroutsup_tbl."` `gstsup` 
							ON `gstsup`.`voucher_txn_id` = `gstsum`.`vch_txn_id` 
						WHERE 
							`gstsup`.`outsup_dr_note` = '0' 
							AND `gstsum`.`vch_txn_id` = '".$voucher_txn_id."' 
							AND `gstsum`.`acc_txn_date` >= '".$from_date."' 
							AND `gstsum`.`acc_txn_date` <= '".$to_date."' 
							AND (
								CASE 
									WHEN gstsum.acc_igst_rate = 0 THEN (gstsum.acc_cgst_rate + gstsum.acc_sgst_rate) 
									ELSE gstsum.acc_igst_rate 
								END
							) = '".$igst_rate."'
						ORDER BY 
							`gstsum`.`vch_txn_id`;")->getResultArray();
					
					
				 }	 
			$total_voucher=0;
			$tt_credit = 0;
	      	$tt_debit = 0;
	      	$tt_credit_total = 0;
	      	$tt_debit_total = 0;
			$tt_taxable_amt =0;
			$tt_acc_igst=0;
			$tt_acc_cgst=0;
			$tt_acc_sgst=0;
			$tt_acc_cess=0;
			$tt_acc_nonadv_cess=0;
			$tt_total_tax=0;
			$tt_total_value=0;
			$tt_total_voucher=0;
			  if($response){
				foreach($response as $txrow){
                    $total_voucher++;						
					$taxable_amt= $txrow['taxable_amt'];
					$acc_igst    = $txrow['acc_igst'];
					$acc_cgst    = $txrow['acc_cgst'];
					$acc_sgst    = $txrow['acc_sgst'];
					$acc_cess    = $txrow['acc_cess'];
					$acc_nonadv_cess    = $txrow['acc_nonadv_cess'];
					$total_tax         = $txrow['total_tax'];
					$bill_ref_no      = $txrow['bill_ref_no'];
					
					
					$tt_total_voucher +=$total_voucher;
					$tt_taxable_amt +=$taxable_amt;
					$tt_total_tax +=$total_tax;
					$tt_acc_igst +=$acc_igst;				
					$tt_acc_cgst +=$acc_cgst;
					$tt_acc_sgst +=$acc_sgst;				
					$tt_acc_cess +=$acc_cess;
					$tt_acc_nonadv_cess +=$acc_nonadv_cess;
					}
				    
				    $ttt_taxable_amt +=$tt_taxable_amt;
				    $ttt_total_tax +=$tt_total_tax;
				    $ttt_acc_igst +=$tt_acc_igst;
				  	$ttt_acc_cgst +=$tt_acc_cgst;
				    $ttt_acc_sgst +=$tt_acc_sgst;
                    $ttt_acc_cess +=$tt_acc_cess;
				    $ttt_acc_nonadv_cess +=$tt_acc_nonadv_cess;
			      }
				  
			   }	
				
			}
			$total_invoice_value = $ttt_taxable_amt+$ttt_total_tax;
		
	$data=["invoice_value"=>$total_invoice_value,
		      'taxable_amt'=>$ttt_taxable_amt,"igst"=>$ttt_acc_igst,"cgst"=>$ttt_acc_cgst,
			  "sgst"=>$ttt_acc_sgst,"cess"=>$ttt_acc_cess,"nonadv_cess"=>$ttt_acc_nonadv_cess,
			       "total_tax"=>$ttt_total_tax
				  ];	
		return $data;     
	
   }function gstsummary_ratewise_outputtax_dr_table_info($from_date,$to_date,$vouchers_result,$igst_rate){
	        $acctgstsum_tbl     = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	        $comp_txn_tbl       = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	        $gstroutsup_tbl     = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
	        $gstrinwsup_tbl     = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
	       	
			$total_counter= array();
			$ttt_taxable_amt=0;
			$ttt_total_tax =0;
			$total_invoice_value=0;
			$ttt_acc_igst =0;
			$ttt_acc_cgst =0;
			$ttt_acc_sgst =0;
            $ttt_acc_cess =0;
			$ttt_acc_nonadv_cess =0;
		    $tt_total_vch_counters=0;
			if($vouchers_result){
			foreach($vouchers_result as $row){
			    $response=array();
				$voucher_txn_id    = $row['voucher_txn_id'];
				$voucher_type_id   = $row['voucher_type_id'];
				$total_vch_counters=0;
					
			     if($voucher_type_id==3){
					 
					 $response = $this->db->query("SELECT 
							`gstsup`.`outsup_bill_ref_no` AS `bill_ref_no`, 
							`gstsum`.`taxable_amt`, 
							`gstsum`.`acc_igst`, 
							`gstsum`.`acc_cgst`, 
							`gstsum`.`acc_sgst`, 
							`gstsum`.`acc_cess`, 
							`gstsum`.`acc_nonadv_cess`, 
							`gstsum`.`total_tax`, 
							`gstsum`.`acc_txn_date`, 
							CASE 
								WHEN gstsum.acc_igst_rate = 0 THEN (gstsum.acc_cgst_rate + gstsum.acc_sgst_rate) 
								ELSE gstsum.acc_igst_rate 
							END AS `rate_value`
						FROM 
							`".$acctgstsum_tbl."` `gstsum` 
						JOIN 
							`".$gstroutsup_tbl."` `gstsup` 
							ON `gstsup`.`voucher_txn_id` = `gstsum`.`vch_txn_id` 
						WHERE 
							`gstsup`.`outsup_dr_note` = '1' 
							AND `gstsum`.`vch_txn_id` = '".$voucher_txn_id."' 
							AND `gstsum`.`acc_txn_date` >= '".$from_date."' 
							AND `gstsum`.`acc_txn_date` <= '".$to_date."' 
							AND (
								CASE 
									WHEN gstsum.acc_igst_rate = 0 THEN (gstsum.acc_cgst_rate + gstsum.acc_sgst_rate) 
									ELSE gstsum.acc_igst_rate 
								END
							) = '".$igst_rate."'
						ORDER BY 
							`gstsum`.`vch_txn_id`;")->getResultArray();
					
					
				 }	 
			$total_voucher=0;
			$tt_credit = 0;
	      	$tt_debit = 0;
	      	$tt_credit_total = 0;
	      	$tt_debit_total = 0;
			$tt_taxable_amt =0;
			$tt_acc_igst=0;
			$tt_acc_cgst=0;
			$tt_acc_sgst=0;
			$tt_acc_cess=0;
			$tt_acc_nonadv_cess=0;
			$tt_total_tax=0;
			$tt_total_value=0;
			$tt_total_voucher=0;
			  if($response){
				foreach($response as $txrow){
                    $total_voucher++;						
					$taxable_amt= $txrow['taxable_amt'];
					$acc_igst    = $txrow['acc_igst'];
					$acc_cgst    = $txrow['acc_cgst'];
					$acc_sgst    = $txrow['acc_sgst'];
					$acc_cess    = $txrow['acc_cess'];
					$acc_nonadv_cess    = $txrow['acc_nonadv_cess'];
					$total_tax         = $txrow['total_tax'];
					$bill_ref_no      = $txrow['bill_ref_no'];
					
					
					$tt_total_voucher +=$total_voucher;
					$tt_taxable_amt +=$taxable_amt;
					$tt_total_tax +=$total_tax;
					$tt_acc_igst +=$acc_igst;				
					$tt_acc_cgst +=$acc_cgst;
					$tt_acc_sgst +=$acc_sgst;				
					$tt_acc_cess +=$acc_cess;
					$tt_acc_nonadv_cess +=$acc_nonadv_cess;
					}
				    
				    $ttt_taxable_amt +=$tt_taxable_amt;
				    $ttt_total_tax +=$tt_total_tax;
				    $ttt_acc_igst +=$tt_acc_igst;
				  	$ttt_acc_cgst +=$tt_acc_cgst;
				    $ttt_acc_sgst +=$tt_acc_sgst;
                    $ttt_acc_cess +=$tt_acc_cess;
				    $ttt_acc_nonadv_cess +=$tt_acc_nonadv_cess;
			      }
				  
			   }	
				
			}
			$total_invoice_value = $ttt_taxable_amt+$ttt_total_tax;
		
	$data=["invoice_value"=>$total_invoice_value,
		      'taxable_amt'=>$ttt_taxable_amt,"igst"=>$ttt_acc_igst,"cgst"=>$ttt_acc_cgst,
			  "sgst"=>$ttt_acc_sgst,"cess"=>$ttt_acc_cess,"nonadv_cess"=>$ttt_acc_nonadv_cess,
			       "total_tax"=>$ttt_total_tax
				  ];	
		return $data;     
	
   }
   function gstsummary_ratewise_inputtax_table_info($from_date,$to_date,$vouchers_result,$igst_rate){
	        $acctgstsum_tbl     = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	        $comp_txn_tbl       = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	        $gstroutsup_tbl     = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
	        $gstrinwsup_tbl     = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
	       	
			$total_counter= array();
			$ttt_taxable_amt=0;
			$ttt_total_tax =0;
			$total_invoice_value=0;
			$ttt_acc_igst =0;
			$ttt_acc_cgst =0;
			$ttt_acc_sgst =0;
            $ttt_acc_cess =0;
			$ttt_acc_nonadv_cess =0;
		    $tt_total_vch_counters=0;
			if($vouchers_result){
			foreach($vouchers_result as $row){
			    $response=array();
				$voucher_txn_id    = $row['voucher_txn_id'];
				$voucher_type_id   = $row['voucher_type_id'];
				$total_vch_counters=0;
					
			     if($voucher_type_id==11){
					 
					 $response = $this->db->query("SELECT 
							`gstsup`.`inwsup_bill_ref_no` AS `bill_ref_no`, 
							`gstsum`.`taxable_amt`, 
							`gstsum`.`acc_igst`, 
							`gstsum`.`acc_cgst`, 
							`gstsum`.`acc_sgst`, 
							`gstsum`.`acc_cess`, 
							`gstsum`.`acc_nonadv_cess`, 
							`gstsum`.`total_tax`, 
							`gstsum`.`acc_txn_date`, 
							CASE 
								WHEN gstsum.acc_igst_rate = 0 THEN (gstsum.acc_cgst_rate + gstsum.acc_sgst_rate) 
								ELSE gstsum.acc_igst_rate 
							END AS `rate_value`
						FROM 
							`".$acctgstsum_tbl."` `gstsum` 
						JOIN 
							`".$gstrinwsup_tbl."` `gstsup` 
							ON `gstsup`.`voucher_txn_id` = `gstsum`.`vch_txn_id` 
						WHERE 
							`gstsup`.`inwsup_cr_note` = '0' 
							AND `gstsum`.`vch_txn_id` = '".$voucher_txn_id."' 
							AND `gstsum`.`acc_txn_date` >= '".$from_date."' 
							AND `gstsum`.`acc_txn_date` <= '".$to_date."' 
							AND (
								CASE 
									WHEN gstsum.acc_igst_rate = 0 THEN (gstsum.acc_cgst_rate + gstsum.acc_sgst_rate) 
									ELSE gstsum.acc_igst_rate 
								END
							) = '".$igst_rate."'
						ORDER BY 
							`gstsum`.`vch_txn_id`;")->getResultArray();
					
					
				 }	 
			$total_voucher=0;
			$tt_credit = 0;
	      	$tt_debit = 0;
	      	$tt_credit_total = 0;
	      	$tt_debit_total = 0;
			$tt_taxable_amt =0;
			$tt_acc_igst=0;
			$tt_acc_cgst=0;
			$tt_acc_sgst=0;
			$tt_acc_cess=0;
			$tt_acc_nonadv_cess=0;
			$tt_total_tax=0;
			$tt_total_value=0;
			$tt_total_voucher=0;
			  if($response){
				foreach($response as $txrow){
                    $total_voucher++;						
					$taxable_amt= $txrow['taxable_amt'];
					$acc_igst    = $txrow['acc_igst'];
					$acc_cgst    = $txrow['acc_cgst'];
					$acc_sgst    = $txrow['acc_sgst'];
					$acc_cess    = $txrow['acc_cess'];
					$acc_nonadv_cess    = $txrow['acc_nonadv_cess'];
					$total_tax         = $txrow['total_tax'];
					$bill_ref_no      = $txrow['bill_ref_no'];
					
					
					$tt_total_voucher +=$total_voucher;
					$tt_taxable_amt +=$taxable_amt;
					$tt_total_tax +=$total_tax;
					$tt_acc_igst +=$acc_igst;				
					$tt_acc_cgst +=$acc_cgst;
					$tt_acc_sgst +=$acc_sgst;				
					$tt_acc_cess +=$acc_cess;
					$tt_acc_nonadv_cess +=$acc_nonadv_cess;
					}
				    
				    $ttt_taxable_amt +=$tt_taxable_amt;
				    $ttt_total_tax +=$tt_total_tax;
				    $ttt_acc_igst +=$tt_acc_igst;
				  	$ttt_acc_cgst +=$tt_acc_cgst;
				    $ttt_acc_sgst +=$tt_acc_sgst;
                    $ttt_acc_cess +=$tt_acc_cess;
				    $ttt_acc_nonadv_cess +=$tt_acc_nonadv_cess;
			      }
				  
			   }	
				
			}
			$total_invoice_value = $ttt_taxable_amt+$ttt_total_tax;
		
	$data=["invoice_value"=>$total_invoice_value,
		      'taxable_amt'=>$ttt_taxable_amt,"igst"=>$ttt_acc_igst,"cgst"=>$ttt_acc_cgst,
			  "sgst"=>$ttt_acc_sgst,"cess"=>$ttt_acc_cess,"nonadv_cess"=>$ttt_acc_nonadv_cess,
			       "total_tax"=>$ttt_total_tax
				  ];	
		return $data;     
	
   }
   function gstsummary_ratewise_outputtax_cr_table_info($from_date,$to_date,$vouchers_result,$igst_rate){
	        $acctgstsum_tbl     = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	        $comp_txn_tbl       = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	        $gstroutsup_tbl     = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
	        $gstrinwsup_tbl     = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
	       	
			$total_counter= array();
			$ttt_taxable_amt=0;
			$ttt_total_tax =0;
			$total_invoice_value=0;
			$ttt_acc_igst =0;
			$ttt_acc_cgst =0;
			$ttt_acc_sgst =0;
            $ttt_acc_cess =0;
			$ttt_acc_nonadv_cess =0;
		    $tt_total_vch_counters=0;
			if($vouchers_result){
			foreach($vouchers_result as $row){
			    $response=array();
				$voucher_txn_id    = $row['voucher_txn_id'];
				$voucher_type_id   = $row['voucher_type_id'];
				$total_vch_counters=0;
					
			     if($voucher_type_id==2){
					 
					 $response = $this->db->query("SELECT 
							`gstsup`.`inwsup_bill_ref_no` AS `bill_ref_no`, 
							`gstsum`.`taxable_amt`, 
							`gstsum`.`acc_igst`, 
							`gstsum`.`acc_cgst`, 
							`gstsum`.`acc_sgst`, 
							`gstsum`.`acc_cess`, 
							`gstsum`.`acc_nonadv_cess`, 
							`gstsum`.`total_tax`, 
							`gstsum`.`acc_txn_date`, 
							CASE 
								WHEN gstsum.acc_igst_rate = 0 THEN (gstsum.acc_cgst_rate + gstsum.acc_sgst_rate) 
								ELSE gstsum.acc_igst_rate 
							END AS `rate_value`
						FROM 
							`".$acctgstsum_tbl."` `gstsum` 
						JOIN 
							`".$gstrinwsup_tbl."` `gstsup` 
							ON `gstsup`.`voucher_txn_id` = `gstsum`.`vch_txn_id` 
						WHERE 
							`gstsup`.`inwsup_cr_note` = '1' 
							AND `gstsum`.`vch_txn_id` = '".$voucher_txn_id."' 
							AND `gstsum`.`acc_txn_date` >= '".$from_date."' 
							AND `gstsum`.`acc_txn_date` <= '".$to_date."' 
							AND (
								CASE 
									WHEN gstsum.acc_igst_rate = 0 THEN (gstsum.acc_cgst_rate + gstsum.acc_sgst_rate) 
									ELSE gstsum.acc_igst_rate 
								END
							) = '".$igst_rate."'
						ORDER BY 
							`gstsum`.`vch_txn_id`;")->getResultArray();
					
					
				 }	 
			$total_voucher=0;
			$tt_credit = 0;
	      	$tt_debit = 0;
	      	$tt_credit_total = 0;
	      	$tt_debit_total = 0;
			$tt_taxable_amt =0;
			$tt_acc_igst=0;
			$tt_acc_cgst=0;
			$tt_acc_sgst=0;
			$tt_acc_cess=0;
			$tt_acc_nonadv_cess=0;
			$tt_total_tax=0;
			$tt_total_value=0;
			$tt_total_voucher=0;
			  if($response){
				foreach($response as $txrow){
                    $total_voucher++;						
					$taxable_amt= $txrow['taxable_amt'];
					$acc_igst    = $txrow['acc_igst'];
					$acc_cgst    = $txrow['acc_cgst'];
					$acc_sgst    = $txrow['acc_sgst'];
					$acc_cess    = $txrow['acc_cess'];
					$acc_nonadv_cess    = $txrow['acc_nonadv_cess'];
					$total_tax         = $txrow['total_tax'];
					$bill_ref_no      = $txrow['bill_ref_no'];
					
					
					$tt_total_voucher +=$total_voucher;
					$tt_taxable_amt +=$taxable_amt;
					$tt_total_tax +=$total_tax;
					$tt_acc_igst +=$acc_igst;				
					$tt_acc_cgst +=$acc_cgst;
					$tt_acc_sgst +=$acc_sgst;				
					$tt_acc_cess +=$acc_cess;
					$tt_acc_nonadv_cess +=$acc_nonadv_cess;
					}
				    
				    $ttt_taxable_amt +=$tt_taxable_amt;
				    $ttt_total_tax +=$tt_total_tax;
				    $ttt_acc_igst +=$tt_acc_igst;
				  	$ttt_acc_cgst +=$tt_acc_cgst;
				    $ttt_acc_sgst +=$tt_acc_sgst;
                    $ttt_acc_cess +=$tt_acc_cess;
				    $ttt_acc_nonadv_cess +=$tt_acc_nonadv_cess;
			      }
				  
			   }	
				
			}
			$total_invoice_value = $ttt_taxable_amt+$ttt_total_tax;
		
	$data=["invoice_value"=>$total_invoice_value,
		      'taxable_amt'=>$ttt_taxable_amt,"igst"=>$ttt_acc_igst,"cgst"=>$ttt_acc_cgst,
			  "sgst"=>$ttt_acc_sgst,"cess"=>$ttt_acc_cess,"nonadv_cess"=>$ttt_acc_nonadv_cess,
			       "total_tax"=>$ttt_total_tax
				  ];	
		return $data;     
	
   }
   
   function hsn_outputtax_table_info($from_date,$to_date,$voucher_txn_ids){
	    $acctgstsum_tbl     = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	    $comp_txn_tbl       = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	    $gstroutsup_tbl     = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
	    $gstrinwsup_tbl     = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
		$voucher_tbl        = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	      	
        // Query for records from database
        $builders = $this->db->table($acctgstsum_tbl . ' gstsum');
        $builders->join($gstroutsup_tbl . ' gstsup', 'gstsup.voucher_txn_id = gstsum.vch_txn_id');
        $builders->join($voucher_tbl, $voucher_tbl.'.voucher_txn_id =gstsum.vch_txn_id');
		$builders->where('gstsup.outsup_dr_note', '0');
        $builders->whereIn('gstsum.vch_txn_id', $voucher_txn_ids);
		$builders->whereIn($voucher_tbl.'.voucher_txn_id', $voucher_txn_ids);
        $builders->where('gstsum.acc_txn_date >=', $from_date);
		$builders->where('gstsum.acc_txn_date >=', $from_date);
		$builders->whereIn($voucher_tbl.'.voucher_type_id',[18,2]);
        $builders->where('gstsum.acc_type','itm');
        $builders->orderBy('gstsum.acc_txn_date');
        $builders->orderBy('gstsum.vch_txn_id');
        $response = $builders->get()->getResultArray();
	    $hsn_array = [];  // Initialize an empty array to store the unique item_hsn and sums of invoice values and other fields
		if ($response) {
			foreach ($response as $row) {
				$item_id = $row['acc_id'];
				$taxable_amt = $row['taxable_amt'];
				$total_tax = $row['total_tax'];
				$igst = $row['acc_igst'];
				$cgst = $row['acc_cgst'];
				$sgst = $row['acc_sgst'];
				$cess = $row['acc_cess'];
				$nonadv_cess = $row['acc_nonadv_cess'];
				$voucher_type_id = $row['voucher_type_id'];
				$item_hsn = $row['acc_hsn_sac'];
					
					// If item_hsn is empty, assign 'UNDEFINED'
					if (empty($item_hsn)) {
						$item_hsn = 'UNDEFINED';
					} else {
						// Add the chapter info
						$item_hsn = 'CHAPTER ' . substr($item_hsn, 0, 2);
					}

					// Initialize the hsn_array if it doesn't exist for the current item_hsn
					if (!isset($hsn_array[$item_hsn])) {
						$hsn_array[$item_hsn] = [
						    'sum_invoice_value' =>0,
							'sum_taxable_amt' => 0,
							'sum_igst' => 0,
							'sum_cgst' => 0,
							'sum_sgst' => 0,
							'sum_cess' => 0,
							'sum_nonadv_cess' => 0,
							'sum_total_tax' => 0
						];
					}

					// Accumulate the values for each field
					$hsn_array[$item_hsn]['from_date'] = $from_date;
					$hsn_array[$item_hsn]['voucher_type_id'] = $voucher_type_id;
					$hsn_array[$item_hsn]['to_date'] = $to_date;
					$hsn_array[$item_hsn]['table_name'] =$item_hsn;
					$hsn_array[$item_hsn]['sum_invoice_value'] += ($taxable_amt+$total_tax);
					$hsn_array[$item_hsn]['sum_taxable_amt'] += $taxable_amt;
					$hsn_array[$item_hsn]['sum_igst'] += $igst;
					$hsn_array[$item_hsn]['sum_cgst'] += $cgst;
					$hsn_array[$item_hsn]['sum_sgst'] += $sgst;
					$hsn_array[$item_hsn]['sum_cess'] += $cess;
					$hsn_array[$item_hsn]['sum_nonadv_cess'] += $nonadv_cess;
					$hsn_array[$item_hsn]['sum_total_tax'] += $total_tax;
				
			}
		   }			
	 return $hsn_array;	  
   }
   function hsn_outputtax_table_detailed_info($from_date,$to_date,$voucher_txn_ids){
	    $acctgstsum_tbl     = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	    $comp_txn_tbl       = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	    $gstroutsup_tbl     = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
	    $gstrinwsup_tbl     = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
		$voucher_tbl        = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	      	
        // Query for records from database
        $builders = $this->db->table($acctgstsum_tbl . ' gstsum');
        $builders->join($gstroutsup_tbl . ' gstsup', 'gstsup.voucher_txn_id = gstsum.vch_txn_id');
        $builders->join($voucher_tbl, $voucher_tbl.'.voucher_txn_id =gstsum.vch_txn_id');
		$builders->where('gstsup.outsup_dr_note', '0');
        $builders->whereIn('gstsum.vch_txn_id', $voucher_txn_ids);
		$builders->whereIn($voucher_tbl.'.voucher_txn_id', $voucher_txn_ids);
        $builders->where('gstsum.acc_txn_date >=', $from_date);
		$builders->where('gstsum.acc_txn_date >=', $from_date);
		$builders->whereIn($voucher_tbl.'.voucher_type_id',[18,2]);
        $builders->where('gstsum.acc_type','itm');
        $builders->orderBy('gstsum.acc_txn_date');
        $builders->orderBy('gstsum.vch_txn_id');
        $response = $builders->get()->getResultArray();
	    $hsn_array = [];  // Initialize an empty array to store the unique item_hsn and sums of invoice values and other fields
		if ($response) {
			foreach ($response as $row) {
				$item_id = $row['acc_id'];
				$taxable_amt = $row['taxable_amt'];
				$total_tax = $row['total_tax'];
				$igst = $row['acc_igst'];
				$cgst = $row['acc_cgst'];
				$sgst = $row['acc_sgst'];
				$cess = $row['acc_cess'];
				$nonadv_cess = $row['acc_nonadv_cess'];
				$voucher_type_id = $row['voucher_type_id'];
				$item_hsn = $row['acc_hsn_sac'];
					
				
					
					// If item_hsn is empty, assign 'UNDEFINED'
					if (empty($row['acc_hsn_sac'])) {
						$item_hsn = '';
					} else {
						// Add the chapter info
						$item_hsn  = $item_hsn;
					}
					
					
					// If item_hsn is empty, assign 'UNDEFINED'
					if (empty($row['acc_hsn_sac'])) {
						$short_item_hsn = 'UNDEFINED';
					} else {
						// Add the chapter info
						$short_item_hsn  = 'CHAPTER ' . substr($item_hsn, 0, 2);
					}

					if (!isset($hsn_array[$short_item_hsn])) {
						$hsn_array[$short_item_hsn] = []; // Initialize an empty array for the shortened hsn
					}

					// Initialize the full hsn array if it doesn't exist for the current item_hsn
					if (!isset($hsn_array[$short_item_hsn][$item_hsn])) {
						$hsn_array[$short_item_hsn][$item_hsn] = [
							'sum_invoice_value' => 0,
							'sum_taxable_amt' => 0,
							'sum_igst' => 0,
							'sum_cgst' => 0,
							'sum_sgst' => 0,
							'sum_cess' => 0,
							'sum_nonadv_cess' => 0,
							'sum_total_tax' => 0,
							'from_date' => $from_date,
							'voucher_type_id' => $voucher_type_id,
							'to_date' => $to_date,
							'table_name' => $item_hsn,
						];
					}

					// Accumulate the values for each field under the specific full item_hsn
					$hsn_array[$short_item_hsn][$item_hsn]['from_date'] = $from_date;
					$hsn_array[$short_item_hsn][$item_hsn]['voucher_type_id'] = $voucher_type_id;
					$hsn_array[$short_item_hsn][$item_hsn]['to_date'] = $to_date;
					$hsn_array[$short_item_hsn][$item_hsn]['sum_invoice_value'] += ($taxable_amt + $total_tax);
					$hsn_array[$short_item_hsn][$item_hsn]['sum_taxable_amt'] += $taxable_amt;
					$hsn_array[$short_item_hsn][$item_hsn]['sum_igst'] += $igst;
					$hsn_array[$short_item_hsn][$item_hsn]['sum_cgst'] += $cgst;
					$hsn_array[$short_item_hsn][$item_hsn]['sum_sgst'] += $sgst;
					$hsn_array[$short_item_hsn][$item_hsn]['sum_cess'] += $cess;
					$hsn_array[$short_item_hsn][$item_hsn]['sum_nonadv_cess'] += $nonadv_cess;
					$hsn_array[$short_item_hsn][$item_hsn]['sum_total_tax'] += $total_tax;
				
			}
		   }			
	 return $hsn_array;	  
   }
   function hsn_inputtax_table_detailed_info($from_date,$to_date,$voucher_txn_ids){
	    $acctgstsum_tbl     = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	    $comp_txn_tbl       = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	    $gstroutsup_tbl     = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
	    $gstrinwsup_tbl     = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
		$voucher_tbl        = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	      	
        // Query for records from database
        $builders = $this->db->table($acctgstsum_tbl . ' gstsum');
        $builders->join($gstrinwsup_tbl . ' gstsup', 'gstsup.voucher_txn_id = gstsum.vch_txn_id');
        $builders->join($voucher_tbl, $voucher_tbl.'.voucher_txn_id =gstsum.vch_txn_id');
		$builders->where('gstsup.inwsup_cr_note', '0');
        $builders->whereIn('gstsum.vch_txn_id', $voucher_txn_ids);
		$builders->whereIn($voucher_tbl.'.voucher_txn_id', $voucher_txn_ids);
        $builders->where('gstsum.acc_txn_date >=', $from_date);
		$builders->where('gstsum.acc_txn_date >=', $from_date);
		$builders->whereIn($voucher_tbl.'.voucher_type_id',[11,3]);
        $builders->where('gstsum.acc_type','itm');
        $builders->orderBy('gstsum.acc_txn_date');
        $builders->orderBy('gstsum.vch_txn_id');
        $response = $builders->get()->getResultArray();
	    $hsn_array = [];  // Initialize an empty array to store the unique item_hsn and sums of invoice values and other fields
		if ($response) {
			foreach ($response as $row) {
				$item_id = $row['acc_id'];
				$taxable_amt = $row['taxable_amt'];
				$total_tax = $row['total_tax'];
				$igst = $row['acc_igst'];
				$cgst = $row['acc_cgst'];
				$sgst = $row['acc_sgst'];
				$cess = $row['acc_cess'];
				$nonadv_cess = $row['acc_nonadv_cess'];
				$voucher_type_id = $row['voucher_type_id'];
				$item_hsn = $row['acc_hsn_sac'];
					
				
					
					// If item_hsn is empty, assign 'UNDEFINED'
					if (empty($row['acc_hsn_sac'])) {
						$item_hsn = '';
					} else {
						// Add the chapter info
						$item_hsn  = $item_hsn;
					}
					
					
					// If item_hsn is empty, assign 'UNDEFINED'
					if (empty($row['acc_hsn_sac'])) {
						$short_item_hsn = 'UNDEFINED';
					} else {
						// Add the chapter info
						$short_item_hsn  = 'CHAPTER ' . substr($item_hsn, 0, 2);
					}

					if (!isset($hsn_array[$short_item_hsn])) {
						$hsn_array[$short_item_hsn] = []; // Initialize an empty array for the shortened hsn
					}

					// Initialize the full hsn array if it doesn't exist for the current item_hsn
					if (!isset($hsn_array[$short_item_hsn][$item_hsn])) {
						$hsn_array[$short_item_hsn][$item_hsn] = [
							'sum_invoice_value' => 0,
							'sum_taxable_amt' => 0,
							'sum_igst' => 0,
							'sum_cgst' => 0,
							'sum_sgst' => 0,
							'sum_cess' => 0,
							'sum_nonadv_cess' => 0,
							'sum_total_tax' => 0,
							'from_date' => $from_date,
							'voucher_type_id' => $voucher_type_id,
							'to_date' => $to_date,
							'table_name' => $item_hsn,
						];
					}

					// Accumulate the values for each field under the specific full item_hsn
					$hsn_array[$short_item_hsn][$item_hsn]['from_date'] = $from_date;
					$hsn_array[$short_item_hsn][$item_hsn]['voucher_type_id'] = $voucher_type_id;
					$hsn_array[$short_item_hsn][$item_hsn]['to_date'] = $to_date;
					$hsn_array[$short_item_hsn][$item_hsn]['sum_invoice_value'] += ($taxable_amt + $total_tax);
					$hsn_array[$short_item_hsn][$item_hsn]['sum_taxable_amt'] += $taxable_amt;
					$hsn_array[$short_item_hsn][$item_hsn]['sum_igst'] += $igst;
					$hsn_array[$short_item_hsn][$item_hsn]['sum_cgst'] += $cgst;
					$hsn_array[$short_item_hsn][$item_hsn]['sum_sgst'] += $sgst;
					$hsn_array[$short_item_hsn][$item_hsn]['sum_cess'] += $cess;
					$hsn_array[$short_item_hsn][$item_hsn]['sum_nonadv_cess'] += $nonadv_cess;
					$hsn_array[$short_item_hsn][$item_hsn]['sum_total_tax'] += $total_tax;
				
			}
		   }			
	 return $hsn_array;	  
   }
   
   function hsn_inputtax_table_info($from_date,$to_date,$voucher_txn_ids){
	    $acctgstsum_tbl     = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	    $comp_txn_tbl       = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	    $gstroutsup_tbl     = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
	    $gstrinwsup_tbl     = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
		$voucher_tbl      = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	      	
			
			//$_SESSION['s_user_id']=
        // Query for records from database
        $builders = $this->db->table($acctgstsum_tbl . ' gstsum');
        $builders->join($voucher_tbl, $voucher_tbl.'.voucher_txn_id =gstsum.vch_txn_id');
		$builders->whereIn('gstsum.vch_txn_id', $voucher_txn_ids);
		$builders->whereIn($voucher_tbl.'.voucher_txn_id', $voucher_txn_ids);
        $builders->where('gstsum.acc_txn_date >=', $from_date);
		$builders->where('gstsum.acc_txn_date >=', $from_date);
		$builders->whereIn($voucher_tbl.'.voucher_type_id',[11,3]);
        $builders->where('gstsum.acc_type','itm');
        $builders->orderBy('gstsum.acc_txn_date');
        $builders->orderBy('gstsum.vch_txn_id');
        $response = $builders->get()->getResultArray();
	    $hsn_array = [];  // Initialize an empty array to store the unique item_hsn and sums of invoice values and other fields
		if ($response) {
			foreach ($response as $row) {
				$item_id = $row['acc_id'];
				$taxable_amt = $row['taxable_amt'];
				$total_tax = $row['total_tax'];
				$igst = $row['acc_igst'];
				$cgst = $row['acc_cgst'];
				$sgst = $row['acc_sgst'];
				$cess = $row['acc_cess'];
				$nonadv_cess = $row['acc_nonadv_cess'];
				$voucher_type_id = $row['voucher_type_id'];
				$item_hsn       = $row['acc_hsn_sac'];				
					
					// If item_hsn is empty, assign 'UNDEFINED'
					if (empty($item_hsn)) {
						$item_hsn = 'UNDEFINED';
					} else {
						// Add the chapter info
						$item_hsn = 'CHAPTER ' . substr($item_hsn, 0, 2);
					}

					// Initialize the hsn_array if it doesn't exist for the current item_hsn
					if (!isset($hsn_array[$item_hsn])) {
						$hsn_array[$item_hsn] = [
						    'sum_invoice_value' =>0,
							'sum_taxable_amt' => 0,
							'sum_igst' => 0,
							'sum_cgst' => 0,
							'sum_sgst' => 0,
							'sum_cess' => 0,
							'sum_nonadv_cess' => 0,
							'sum_total_tax' => 0
						];
					}

					// Accumulate the values for each field
					$hsn_array[$item_hsn]['from_date'] = $from_date;
					$hsn_array[$item_hsn]['voucher_type_id'] = $voucher_type_id;
					$hsn_array[$item_hsn]['to_date'] = $to_date;
					$hsn_array[$item_hsn]['table_name'] =$item_hsn;
					$hsn_array[$item_hsn]['sum_invoice_value'] += ($taxable_amt+$total_tax);
					$hsn_array[$item_hsn]['sum_taxable_amt'] += $taxable_amt;
					$hsn_array[$item_hsn]['sum_igst'] += $igst;
					$hsn_array[$item_hsn]['sum_cgst'] += $cgst;
					$hsn_array[$item_hsn]['sum_sgst'] += $sgst;
					$hsn_array[$item_hsn]['sum_cess'] += $cess;
					$hsn_array[$item_hsn]['sum_nonadv_cess'] += $nonadv_cess;
					$hsn_array[$item_hsn]['sum_total_tax'] += $total_tax;
				
			}
		   }			
	 return $hsn_array;	  
   }
   
   function gstsummary_outputtax_table_info($from_date,$to_date,$voucher_txn_ids){
	
	try{
	    $acctgstsum_tbl     = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	    $comp_txn_tbl       = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	    $gstroutsup_tbl     = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
	    $gstrinwsup_tbl     = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
		   
		// Query for records from database
        $builders = $this->db->table($acctgstsum_tbl . ' gstsum');
        $builders->join($gstroutsup_tbl . ' gstsup', 'gstsup.voucher_txn_id = gstsum.vch_txn_id');
        $builders->select([
			'SUM(gstsum.taxable_amt + gstsum.total_tax) AS invoice_value',
			'SUM(gstsum.taxable_amt) AS taxable_amt',
			'SUM(gstsum.acc_igst) AS igst',
			'SUM(gstsum.acc_cgst) AS cgst',
			'SUM(gstsum.acc_sgst) AS sgst',
			'SUM(gstsum.acc_cess) AS cess',
			'SUM(gstsum.acc_nonadv_cess) AS nonadv_cess',
			'SUM(gstsum.total_tax) AS total_tax'
		]);
		$builders->where('gstsup.outsup_dr_note', '0');
        $builders->whereIn('gstsum.vch_txn_id', $voucher_txn_ids);
        $builders->where('gstsum.acc_txn_date >=', $from_date);
        $builders->where('gstsum.acc_txn_date <=', $to_date);
        $builders->orderBy('gstsum.acc_txn_date');
        $builders->orderBy('gstsum.vch_txn_id');
        $response = $builders->get()->getRowArray();
		
		
	$data = [
    "invoice_value" => $response['invoice_value'],
    'taxable_amt' =>  $response['taxable_amt'],
    "igst" =>  $response['igst'],
    "cgst" =>  $response['cgst'],
    "sgst" =>  $response['sgst'],
    "cess" =>  $response['cess'],
    "nonadv_cess" =>  $response['nonadv_cess'],
    "total_tax" =>  $response['total_tax'],
];	
		return $data;  
	}
	catch (\Exception $e) {
				/* echo '<pre>';
				print_r($voucher_txn_ids);
				echo 'Message: ' .$e->getMessage(); */
				
			}
/* 
		
			
			die();
			// Initialize the final totals
$total_invoice_value = 0;
$ttt_taxable_amt = 0;
$ttt_total_tax = 0;
$ttt_acc_igst = 0;
$ttt_acc_cgst = 0;
$ttt_acc_sgst = 0;
$ttt_acc_cess = 0;
$ttt_acc_nonadv_cess = 0;

$tbb = '';
$tbb .= '<table width="100%" class="table">';

// Loop through voucher records
if ($vouchers_result) {
    foreach ($vouchers_result as $row) {
        $voucher_txn_id = $row['voucher_txn_id'];
        $voucher_type_id = $row['voucher_type_id'];

        // Query for records from database
        $builders = $this->db->table($acctgstsum_tbl . ' gstsum');
        $builders->join($gstroutsup_tbl . ' gstsup', 'gstsup.voucher_txn_id = gstsum.vch_txn_id');
        $builders->select('gstsup.outsup_bill_ref_no as bill_ref_no, gstsum.taxable_amt, gstsum.acc_igst, gstsum.acc_cgst, gstsum.acc_sgst, gstsum.acc_cess, gstsum.acc_nonadv_cess, gstsum.total_tax, gstsum.acc_txn_date');
        $builders->where('gstsup.outsup_dr_note', '0');
        $builders->where('gstsum.vch_txn_id', $voucher_txn_id);
        $builders->where('gstsum.acc_txn_date >=', $from_date);
        $builders->where('gstsum.acc_txn_date <=', $to_date);
        $builders->orderBy('gstsum.acc_txn_date');
        $builders->orderBy('gstsum.vch_txn_id');
        $response = $builders->get()->getResultArray();

        // Initialize the totals for this voucher
        $tt_taxable_amt = 0;
        $tt_total_tax = 0;
        $tt_acc_igst = 0;
        $tt_acc_cgst = 0;
        $tt_acc_sgst = 0;
        $tt_acc_cess = 0;
        $tt_acc_nonadv_cess = 0;

        // If the response contains data
        if ($response) {
            foreach ($response as $txrow) {
                // Calculate individual values
                $taxable_amt = parseAmount($txrow['taxable_amt']);
                $acc_igst = parseAmount($txrow['acc_igst']);
                $acc_cgst = parseAmount($txrow['acc_cgst']);
                $acc_sgst = parseAmount($txrow['acc_sgst']);
                $acc_cess = parseAmount($txrow['acc_cess']);
                $acc_nonadv_cess = parseAmount($txrow['acc_nonadv_cess']);
                $total_tax = parseAmount($txrow['total_tax']);
                $bill_ref_no = $txrow['bill_ref_no'];

                // Add to totals for this voucher
                $tt_taxable_amt += $taxable_amt;
                $tt_total_tax += $total_tax;
                $tt_acc_igst += $acc_igst;
                $tt_acc_cgst += $acc_cgst;
                $tt_acc_sgst += $acc_sgst;
                $tt_acc_cess += $acc_cess;
                $tt_acc_nonadv_cess += $acc_nonadv_cess;

                // Add the row to the HTML table
                $tbb .= '<tr><td>'.$voucher_txn_id.'</td><td>' . $bill_ref_no . '</td><td>' . ($taxable_amt + $total_tax) . '</td></tr>';
            }
        }

        // Accumulate totals
        $ttt_taxable_amt += $tt_taxable_amt;
        $ttt_total_tax += $tt_total_tax;
        $ttt_acc_igst += $tt_acc_igst;
        $ttt_acc_cgst += $tt_acc_cgst;
        $ttt_acc_sgst += $tt_acc_sgst;
        $ttt_acc_cess += $tt_acc_cess;
        $ttt_acc_nonadv_cess += $tt_acc_nonadv_cess;
    }
}

$tbb .= '</table>';

// Calculate the final invoice value
$total_invoice_value = $ttt_taxable_amt + $ttt_total_tax;

// Prepare the result array
$data = [
    "invoice_value" => $total_invoice_value,
    'taxable_amt' => $ttt_taxable_amt,
    "igst" => $ttt_acc_igst,
    "cgst" => $ttt_acc_cgst,
    "sgst" => $ttt_acc_sgst,
    "cess" => $ttt_acc_cess,
    "nonadv_cess" => $ttt_acc_nonadv_cess,
    "total_tax" => $ttt_total_tax
];	
		return $data;     
	 */
   }
   function gstsummary_drnote_table_info($from_date,$to_date,$vouchers_result){
	        $acctgstsum_tbl     = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	        $comp_txn_tbl       = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	        $gstroutsup_tbl     = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
	        $gstrinwsup_tbl     = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
	       	
			$total_counter= array();
			$ttt_taxable_amt=0;
			$ttt_total_tax =0;
			$total_invoice_value=0;
			$ttt_acc_igst =0;
			$ttt_acc_cgst =0;
			$ttt_acc_sgst =0;
            $ttt_acc_cess =0;
			$ttt_acc_nonadv_cess =0;
		    $tt_total_vch_counters=0;
			if($vouchers_result){
			foreach($vouchers_result as $row){
			    $response=array();
				$voucher_txn_id = $row['voucher_txn_id'];
				$voucher_type_id = $row['voucher_type_id'];
				$total_vch_counters=0;
					
			     if($voucher_type_id==3){
					 $voucher_txn_id = $row['voucher_txn_id'];
					
					/******* Records ***********/ 
    				$builders = $this->db->table($acctgstsum_tbl.' gstsum');
    				$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
    				$builders->select('gstsup.outsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
    				$builders->orderBy('gstsum.vch_txn_id');	        
    				$builders->where('gstsup.outsup_dr_note','1');					
    				$builders->where('gstsum.vch_txn_id', $voucher_txn_id);	
    				$builders->where('gstsum.acc_txn_date >=', $from_date);
    				$builders->where('gstsum.acc_txn_date <=', $to_date);				
    				$response = $builders->get()->getResultArray();					
					
				 }	 
			$total_voucher=0;
			$tt_credit = 0;
	      	$tt_debit = 0;
	      	$tt_credit_total = 0;
	      	$tt_debit_total = 0;
			$tt_taxable_amt =0;
			$tt_acc_igst=0;
			$tt_acc_cgst=0;
			$tt_acc_sgst=0;
			$tt_acc_cess=0;
			$tt_acc_nonadv_cess=0;
			$tt_total_tax=0;
			$tt_total_value=0;
			$tt_total_voucher=0;
			  if($response){
				foreach($response as $txrow){
                    $total_voucher++;						
					$taxable_amt= $txrow['taxable_amt'];
					$acc_igst    = $txrow['acc_igst'];
					$acc_cgst    = $txrow['acc_cgst'];
					$acc_sgst    = $txrow['acc_sgst'];
					$acc_cess    = $txrow['acc_cess'];
					$acc_nonadv_cess    = $txrow['acc_nonadv_cess'];
					$total_tax         = $txrow['total_tax'];
					$bill_ref_no      = $txrow['bill_ref_no'];
					
					
					$tt_total_voucher +=$total_voucher;
					$tt_taxable_amt +=$taxable_amt;
					$tt_total_tax +=$total_tax;
					$tt_acc_igst +=$acc_igst;				
					$tt_acc_cgst +=$acc_cgst;
					$tt_acc_sgst +=$acc_sgst;				
					$tt_acc_cess +=$acc_cess;
					$tt_acc_nonadv_cess +=$acc_nonadv_cess;
					
					
					
			
				    }
				    
				    $ttt_taxable_amt +=$tt_taxable_amt;
				    $ttt_total_tax +=$tt_total_tax;
				    $ttt_acc_igst +=$tt_acc_igst;
				  	$ttt_acc_cgst +=$tt_acc_cgst;
				    $ttt_acc_sgst +=$tt_acc_sgst;
                    $ttt_acc_cess +=$tt_acc_cess;
				    $ttt_acc_nonadv_cess +=$tt_acc_nonadv_cess;


			      }
				  
				
			
			   }	
				
			}
			$total_invoice_value = $ttt_taxable_amt+$ttt_total_tax;
		
	$data=["invoice_value"=>$total_invoice_value,
		      'taxable_amt'=>$ttt_taxable_amt,"igst"=>$ttt_acc_igst,"cgst"=>$ttt_acc_cgst,
			  "sgst"=>$ttt_acc_sgst,"cess"=>$ttt_acc_cess,"nonadv_cess"=>$ttt_acc_nonadv_cess,
			       "total_tax"=>$ttt_total_tax
				  ];	
		return $data;     
	
   }
   function gstsummary_inputtax_table_info($from_date,$to_date,$vouchers_result){
	        $acctgstsum_tbl     = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	        $comp_txn_tbl       = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	        $gstroutsup_tbl     = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
	        $gstrinwsup_tbl     = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
	       	
			$total_counter= array();
			$ttt_taxable_amt=0;
			$ttt_total_tax =0;
			$total_invoice_value=0;
			$ttt_acc_igst =0;
			$ttt_acc_cgst =0;
			$ttt_acc_sgst =0;
            $ttt_acc_cess =0;
			$ttt_acc_nonadv_cess =0;
		    $tt_total_vch_counters=0;
			if($vouchers_result){
			foreach($vouchers_result as $row){
			    $response=array();
				$voucher_txn_id = $row['voucher_txn_id'];
				$voucher_type_id = $row['voucher_type_id'];
				$total_vch_counters=0;
			  		
			     if($voucher_type_id==11){
					 $voucher_txn_id = $row['voucher_txn_id'];
					
					/******* Records ***********/ 
    				$builders = $this->db->table($acctgstsum_tbl.' gstsum');
    				$builders->join($gstrinwsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
    				$builders->select('gstsup.inwsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
    				$builders->orderBy('gstsum.vch_txn_id');	        
    				$builders->where('gstsup.inwsup_cr_note','0');					
    				$builders->where('gstsum.vch_txn_id', $voucher_txn_id);	
    				$builders->where('gstsum.acc_txn_date >=', $from_date);
    				$builders->where('gstsum.acc_txn_date <=', $to_date);				
    				$response = $builders->get()->getResultArray();
					
				 }	 
			$total_voucher=0;
			$tt_credit = 0;
	      	$tt_debit = 0;
	      	$tt_credit_total = 0;
	      	$tt_debit_total = 0;
			$tt_taxable_amt =0;
			$tt_acc_igst=0;
			$tt_acc_cgst=0;
			$tt_acc_sgst=0;
			$tt_acc_cess=0;
			$tt_acc_nonadv_cess=0;
			$tt_total_tax=0;
			$tt_total_value=0;
			$tt_total_voucher=0;
			  if($response){
				foreach($response as $txrow){
                    $total_voucher++;						
					$taxable_amt= $txrow['taxable_amt'];
					$acc_igst    = $txrow['acc_igst'];
					$acc_cgst    = $txrow['acc_cgst'];
					$acc_sgst    = $txrow['acc_sgst'];
					$acc_cess    = $txrow['acc_cess'];
					$acc_nonadv_cess    = $txrow['acc_nonadv_cess'];
					$total_tax         = $txrow['total_tax'];
					$bill_ref_no      = $txrow['bill_ref_no'];
					
					
					$tt_total_voucher +=$total_voucher;
					$tt_taxable_amt +=$taxable_amt;
					$tt_total_tax +=$total_tax;
					$tt_acc_igst +=$acc_igst;				
					$tt_acc_cgst +=$acc_cgst;
					$tt_acc_sgst +=$acc_sgst;				
					$tt_acc_cess +=$acc_cess;
					$tt_acc_nonadv_cess +=$acc_nonadv_cess;
					
					
					
			
				    }
				    
				    $ttt_taxable_amt +=$tt_taxable_amt;
				    $ttt_total_tax +=$tt_total_tax;
				    $ttt_acc_igst +=$tt_acc_igst;
				  	$ttt_acc_cgst +=$tt_acc_cgst;
				    $ttt_acc_sgst +=$tt_acc_sgst;
                    $ttt_acc_cess +=$tt_acc_cess;
				    $ttt_acc_nonadv_cess +=$tt_acc_nonadv_cess;


			      }
				  
				
			
			   }	
				
			}
			$total_invoice_value = $ttt_taxable_amt+$ttt_total_tax;
		
	$data=["invoice_value"=>$total_invoice_value,
		      'taxable_amt'=>$ttt_taxable_amt,"igst"=>$ttt_acc_igst,"cgst"=>$ttt_acc_cgst,
			  "sgst"=>$ttt_acc_sgst,"cess"=>$ttt_acc_cess,"nonadv_cess"=>$ttt_acc_nonadv_cess,
			       "total_tax"=>$ttt_total_tax
				  ];	
		return $data;     
	
   }
   function gstsummary_crnote_table_info($from_date,$to_date,$vouchers_result){
	        $acctgstsum_tbl     = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	        $comp_txn_tbl       = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	        $gstroutsup_tbl     = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
	        $gstrinwsup_tbl     = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
	       	
			$total_counter= array();
			$ttt_taxable_amt=0;
			$ttt_total_tax =0;
			$total_invoice_value=0;
			$ttt_acc_igst =0;
			$ttt_acc_cgst =0;
			$ttt_acc_sgst =0;
            $ttt_acc_cess =0;
			$ttt_acc_nonadv_cess =0;
		    $tt_total_vch_counters=0;
			if($vouchers_result){
			foreach($vouchers_result as $row){
			    $response=array();
				$voucher_txn_id = $row['voucher_txn_id'];
				$voucher_type_id = $row['voucher_type_id'];
				$total_vch_counters=0;
			  		
			     if($voucher_type_id==2){
					 $voucher_txn_id = $row['voucher_txn_id'];
					
					/******* Records ***********/ 
    				$builders = $this->db->table($acctgstsum_tbl.' gstsum');
    				$builders->join($gstrinwsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
    				$builders->select('gstsup.inwsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
    				$builders->orderBy('gstsum.vch_txn_id');	        
    				$builders->where('gstsup.inwsup_cr_note','1');					
    				$builders->where('gstsum.vch_txn_id', $voucher_txn_id);	
    				$builders->where('gstsum.acc_txn_date >=', $from_date);
    				$builders->where('gstsum.acc_txn_date <=', $to_date);				
    				$response = $builders->get()->getResultArray();
					
				 }	 
			$total_voucher=0;
			$tt_credit = 0;
	      	$tt_debit = 0;
	      	$tt_credit_total = 0;
	      	$tt_debit_total = 0;
			$tt_taxable_amt =0;
			$tt_acc_igst=0;
			$tt_acc_cgst=0;
			$tt_acc_sgst=0;
			$tt_acc_cess=0;
			$tt_acc_nonadv_cess=0;
			$tt_total_tax=0;
			$tt_total_value=0;
			$tt_total_voucher=0;
			  if($response){
				foreach($response as $txrow){
                    $total_voucher++;						
					$taxable_amt= $txrow['taxable_amt'];
					$acc_igst    = $txrow['acc_igst'];
					$acc_cgst    = $txrow['acc_cgst'];
					$acc_sgst    = $txrow['acc_sgst'];
					$acc_cess    = $txrow['acc_cess'];
					$acc_nonadv_cess    = $txrow['acc_nonadv_cess'];
					$total_tax         = $txrow['total_tax'];
					$bill_ref_no      = $txrow['bill_ref_no'];
					
					
					$tt_total_voucher +=$total_voucher;
					$tt_taxable_amt +=$taxable_amt;
					$tt_total_tax +=$total_tax;
					$tt_acc_igst +=$acc_igst;				
					$tt_acc_cgst +=$acc_cgst;
					$tt_acc_sgst +=$acc_sgst;				
					$tt_acc_cess +=$acc_cess;
					$tt_acc_nonadv_cess +=$acc_nonadv_cess;
					
					
					
			
				    }
				    
				    $ttt_taxable_amt +=$tt_taxable_amt;
				    $ttt_total_tax +=$tt_total_tax;
				    $ttt_acc_igst +=$tt_acc_igst;
				  	$ttt_acc_cgst +=$tt_acc_cgst;
				    $ttt_acc_sgst +=$tt_acc_sgst;
                    $ttt_acc_cess +=$tt_acc_cess;
				    $ttt_acc_nonadv_cess +=$tt_acc_nonadv_cess;


			      }
				  
				
			
			   }	
				
			}
			$total_invoice_value = $ttt_taxable_amt+$ttt_total_tax;
		
	$data=["invoice_value"=>$total_invoice_value,
		      'taxable_amt'=>$ttt_taxable_amt,"igst"=>$ttt_acc_igst,"cgst"=>$ttt_acc_cgst,
			  "sgst"=>$ttt_acc_sgst,"cess"=>$ttt_acc_cess,"nonadv_cess"=>$ttt_acc_nonadv_cess,
			       "total_tax"=>$ttt_total_tax
				  ];	
		return $data;     
	
   }
	
	public function gst_table_info(
        string $from_date,
        string $to_date,
        array  $vouchers,
        array  $tableinfo
): array {

    /* ───── nothing to summarise? ───── */
    if (!$vouchers) {
        return array_fill_keys(
            ['total_vouchers','invoice_value','taxable_amt',
             'igst','cgst','sgst','cess','nonadv_cess','total_tax'], 0
        );
    }
	$start = microtime(true);
    /* ───── dynamic table names ───── */
    $fy        = $this->session->get('ses_comp_fy_id');
    $gstsumTbl = "{$this->company_id}_acctgstsum_{$fy}";
    $outsupTbl = "{$this->company_id}_gstroutsup_{$fy}";
    $inwsupTbl = "{$this->company_id}_gstrinwsup_{$fy}";
    $vchconTbl = "{$this->company_id}_vhtxnconso_{$fy}";

    /* ───── choose supply table & voucher-type list ───── */
    $isDR = ($tableinfo['outsup_dr_note'] ?? '0') === '1';   // debit-note flag
    $isCR = ($tableinfo['outsup_cr_note'] ?? '0') === '1';   // credit-note flag

    if ($isCR) {                                 // 9B – credit notes
        $supTbl   = $inwsupTbl;
        $supAlias = 'sup';
        $colPref  = 'inwsup';
        $types    = [2];                          // voucher_type_id = 2
    } elseif ($isDR) {                           // debit notes
        $supTbl   = $outsupTbl;
        $supAlias = 'sup';
        $colPref  = 'outsup';
        $types    = [3];                          // voucher_type_id = 3
    } else {                                     // normal outward supplies
        $supTbl   = $outsupTbl;
        $supAlias = 'sup';
        $colPref  = 'outsup';
        $types    = [18];                         // voucher_type_id = 18
    }

    /* ───── voucher IDs that belong to this table ───── */
    $ids = [];
    foreach ($vouchers as $v) {
        if (in_array((int)$v['voucher_type_id'], $types, true)) {
            $ids[] = $v['voucher_txn_id'];
        }
    }
    if (!$ids) {                                    // no matches
        return array_fill_keys(
            ['total_vouchers','invoice_value','taxable_amt',
             'igst','cgst','sgst','cess','nonadv_cess','total_tax'], 0
        );
    }

    /* ───── single aggregated query ───── */
    $builder = $this->db->table("$gstsumTbl g")
        ->join("$supTbl $supAlias", "$supAlias.voucher_txn_id = g.vch_txn_id")
        ->join("$vchconTbl v",      'v.voucher_txn_id = g.vch_txn_id')
        ->select([
            'COUNT(DISTINCT g.vch_txn_id)       AS total_vouchers',
            'SUM(g.taxable_amt)                 AS taxable_amt',
            'SUM(g.total_tax)                   AS total_tax',
            'SUM(g.acc_igst)                    AS igst',
            'SUM(g.acc_cgst)                    AS cgst',
            'SUM(g.acc_sgst)                    AS sgst',
            'SUM(g.acc_cess)                    AS cess',
            'SUM(g.acc_nonadv_cess)             AS nonadv_cess',
        ])

        ->whereIn('g.vch_txn_id', $ids)
        ->where("$supAlias.{$colPref}_rev_chg", $tableinfo['outsup_rev_chg'])
        ->where("$supAlias.{$colPref}_eco",     $tableinfo['outsup_eco'])
        ->where('g.acc_txn_date >=', $from_date)
        ->where('g.acc_txn_date <=', $to_date);

    /* cmp_tax_short_code may be array or single value */
    $builder->whereIn('g.cmp_tax_short_code',
                      (array)$tableinfo['cmp_tax_short_code']);

    /* invoice-type filter (array may be empty) */
    if (!empty($tableinfo['outsup_inv_type'])) {
        $builder->whereIn("$supAlias.{$colPref}_inv_type",
                          $tableinfo['outsup_inv_type']);
    }

    /* optional (taxable+tax) value filter */
    /* if (isset($tableinfo['cnd'], $tableinfo['value']) &&
        $tableinfo['cnd'] !== '') {
        $builder->where(
            "(g.total_tax + g.taxable_amt) {$tableinfo['cnd']}",
            $tableinfo['value']
        );
    } */

    $tot = $builder->get()->getRowArray() ?: [];
	$elapsed = round((microtime(true) - $start) * 1000, 3);
	$lsquery = $this->db->getlastquery();
			$qry_response=['url'=>current_url(true)->getPath(),'query'=>$lsquery,'time_seconds'=>$elapsed];
			$this->SaveServerQueryLog($qry_response);
	//echo $this->db->getlastquery();
	

    /* ───── derive invoice_value & cast ───── */
    $tot = array_map(static fn($v) => (float)$v, $tot);
    $tot['invoice_value'] = $tot['taxable_amt'] + $tot['total_tax'];

    return [
        'total_vouchers' => (int)$tot['total_vouchers'],
        'invoice_value'  => $tot['invoice_value'],
        'taxable_amt'    => $tot['taxable_amt'],
        'igst'           => $tot['igst'],
        'cgst'           => $tot['cgst'],
        'sgst'           => $tot['sgst'],
        'cess'           => $tot['cess'],
        'nonadv_cess'    => $tot['nonadv_cess'],
        'total_tax'      => $tot['total_tax'],
    ];
}


	function gst_table_info_16_05_2025($from_date,$to_date,$vouchers_result,$tableinfo){
		
            $acctgstsum_tbl     = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	        $comp_txn_tbl       = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	        $gstroutsup_tbl     = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
	        $gstrinwsup_tbl     = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
	       	
			$total_counter= array();
			$ttt_taxable_amt=0;
			$ttt_total_tax =0;
			$total_invoice_value=0;
			$ttt_acc_igst =0;
			$ttt_acc_cgst =0;
			$ttt_acc_sgst =0;
            $ttt_acc_cess =0;
			$ttt_acc_nonadv_cess =0;
		    $tt_total_vch_counters=0;
			if($vouchers_result){
			foreach($vouchers_result as $row){
			    $response=array();
				$voucher_txn_id = $row['voucher_txn_id'];
				$voucher_type_id = $row['voucher_type_id'];
				$total_vch_counters=0;
				
				if($tableinfo['outsup_dr_note']=="1" && $voucher_type_id=="3"){
				   $voucher_txn_id = $row['voucher_txn_id'];
				    
						$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
						$builders = $this->db->table($acctgstsum_tbl.' gstsum');
    					$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
    					$builders->select('gstsup.outsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
    					$builders->orderBy('gstsum.vch_txn_id');	        
    					$builders->where('gstsup.outsup_rev_chg',$tableinfo['outsup_rev_chg']);
    					$builders->where('gstsup.outsup_eco',$tableinfo['outsup_eco']);
    					$builders->where('gstsup.outsup_dr_note',$tableinfo['outsup_dr_note']);
    					$builders->where('gstsum.vch_txn_id', $voucher_txn_id);
    					$builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    					$builders->whereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);	
    				    $builders->where('gstsum.acc_txn_date >=', $from_date);
    					$builders->where('gstsum.acc_txn_date <=', $to_date);
						$builders->groupBy('gstsum.vch_txn_id');		 
				   	    $txncounter = $builders->countAllResults();
					    $total_vch_counters +=$txncounter;
					 
					    $tt_total_vch_counters +=$total_vch_counters;
						
						
				    $builders = $this->db->table($acctgstsum_tbl.' gstsum');
					$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
					$builders->select('gstsup.outsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
					$builders->orderBy('gstsum.vch_txn_id');	        
					$builders->where('gstsup.outsup_rev_chg',$tableinfo['outsup_rev_chg']);
					$builders->where('gstsup.outsup_eco',$tableinfo['outsup_eco']);
					$builders->where('gstsup.outsup_dr_note',$tableinfo['outsup_dr_note']);
					$builders->where('gstsum.vch_txn_id', $voucher_txn_id);
					$builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
					$builders->whereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);	
				    $builders->where('gstsum.acc_txn_date >=', $from_date);
					$builders->where('gstsum.acc_txn_date <=', $to_date);
					$response = $builders->get()->getResultArray();
				 } 
				else if($tableinfo['outsup_cr_note']=="1" && $voucher_type_id=="2"){
					$voucher_txn_id = $row['voucher_txn_id'];
					
					/****** Counter ************/ 
					$builders = $this->db->table($acctgstsum_tbl.' gstsum');
					$builders->join($gstrinwsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
					$builders->select('gstsup.inwsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
					$builders->orderBy('gstsum.vch_txn_id');	        
					$builders->where('gstsup.inwsup_rev_chg',$tableinfo['outsup_rev_chg']);
					$builders->where('gstsup.inwsup_eco',$tableinfo['outsup_eco']);
					$builders->where('gstsup.inwsup_cr_note',$tableinfo['outsup_cr_note']);	
					$builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
					$builders->where('gstsum.vch_txn_id', $voucher_txn_id);	
					$builders->whereIn('gstsup.	inwsup_inv_type',$tableinfo['outsup_inv_type']);	
				    $builders->where('gstsum.acc_txn_date >=', $from_date);
					$builders->where('gstsum.acc_txn_date <=', $to_date);
					$builders->groupBy('gstsum.vch_txn_id');		 
				   	$txncounter = $builders->countAllResults();
					$total_vch_counters +=$txncounter;
					 
					$tt_total_vch_counters +=$total_vch_counters;
					
					
					/******* Records ***********/ 
				    $builders = $this->db->table($acctgstsum_tbl.' gstsum');
					$builders->join($gstrinwsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
					$builders->select('gstsup.inwsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
					$builders->orderBy('gstsum.vch_txn_id');	        
					$builders->where('gstsup.inwsup_rev_chg',$tableinfo['outsup_rev_chg']);
					$builders->where('gstsup.inwsup_eco',$tableinfo['outsup_eco']);
					$builders->where('gstsup.inwsup_cr_note',$tableinfo['outsup_cr_note']);	
					$builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
					$builders->where('gstsum.vch_txn_id', $voucher_txn_id);	
					$builders->whereIn('gstsup.	inwsup_inv_type',$tableinfo['outsup_inv_type']);	
				    $builders->where('gstsum.acc_txn_date >=', $from_date);
					$builders->where('gstsum.acc_txn_date <=', $to_date);
					$response = $builders->get()->getResultArray();
				 }						
			
			   else if($tableinfo['outsup_dr_note']=="0" && $tableinfo['outsup_cr_note']=="0"){		
			
			     
					 $voucher_txn_id = $row['voucher_txn_id'];
					/****** Counter ************/ 
						$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
					 $builders = $this->db->table($acctgstsum_tbl.' gstsum');
    				$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
    				$builders->select('gstsup.outsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
    				$builders->orderBy('gstsum.vch_txn_id');	        
    				$builders->where('gstsup.outsup_rev_chg',$tableinfo['outsup_rev_chg']);
    				$builders->where('gstsup.outsup_eco',$tableinfo['outsup_eco']);
    				$builders->where('gstsum.vch_txn_id', $voucher_txn_id);	
    				$builders->where('gstsum.acc_txn_date >=', $from_date);
    				$builders->where('gstsum.acc_txn_date <=', $to_date);				
    				if(in_array("DE",$tableinfo['outsup_inv_type']) && $tableinfo['value']=='0'){
    				  $builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    				  $builders->orWhereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);				  
    				}
    				 elseif(isset($tableinfo['cnd']) && $tableinfo['cnd']!='' && $tableinfo['value']!=''){
    				  $builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    				  $builders->whereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);	
    				  $builders->where('(gstsum.total_tax+gstsum.taxable_amt) '.$tableinfo['cnd'].'',$tableinfo['value']);		
    				} 
    				else{			
    				  $builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    				  $builders->whereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);				  
    				  }
    				 $builders->groupBy('gstsum.vch_txn_id');		 
					 $txncounter = $builders->countAllResults();
					  
					 $total_vch_counters +=$txncounter;
					 
					 $tt_total_vch_counters +=$total_vch_counters;
					 
					/******* Records ***********/ 
    				$builders = $this->db->table($acctgstsum_tbl.' gstsum');
    				$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
    				$builders->select('gstsup.outsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
    				$builders->orderBy('gstsum.vch_txn_id');	        
    				$builders->where('gstsup.outsup_rev_chg',$tableinfo['outsup_rev_chg']);
    				$builders->where('gstsup.outsup_eco',$tableinfo['outsup_eco']);
    				$builders->where('gstsum.vch_txn_id', $voucher_txn_id);	
    				$builders->where('gstsum.acc_txn_date >=', $from_date);
    				$builders->where('gstsum.acc_txn_date <=', $to_date);				
    				if(in_array("DE",$tableinfo['outsup_inv_type']) && $tableinfo['value']=='0'){
    				  $builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    				  $builders->orWhereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);				  
    				}
    				 elseif(isset($tableinfo['cnd']) && $tableinfo['cnd']!='' && $tableinfo['value']!=''){
    				  $builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    				  $builders->whereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);	
    				  $builders->where('(gstsum.total_tax+gstsum.taxable_amt) '.$tableinfo['cnd'].'',$tableinfo['value']);		
    				} 
    				else{			
    				  $builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    				  $builders->whereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);				  
    				  }
				     $response = $builders->get()->getResultArray();				 
				 
			}		
					 
			$total_voucher=0;
			$tt_credit = 0;
	      	$tt_debit = 0;
	      	$tt_credit_total = 0;
	      	$tt_debit_total = 0;
			$tt_taxable_amt =0;
			$tt_acc_igst=0;
			$tt_acc_cgst=0;
			$tt_acc_sgst=0;
			$tt_acc_cess=0;
			$tt_acc_nonadv_cess=0;
			$tt_total_tax=0;
			$tt_total_value=0;
			$tt_total_voucher=0;
			  if($response){
				foreach($response as $txrow){
                    $total_voucher++;						
					$taxable_amt= $txrow['taxable_amt'];
					$acc_igst    = $txrow['acc_igst'];
					$acc_cgst    = $txrow['acc_cgst'];
					$acc_sgst    = $txrow['acc_sgst'];
					$acc_cess    = $txrow['acc_cess'];
					$acc_nonadv_cess    = $txrow['acc_nonadv_cess'];
					$total_tax         = $txrow['total_tax'];
					$bill_ref_no      = $txrow['bill_ref_no'];
					
					
					$tt_total_voucher +=$total_voucher;
					$tt_taxable_amt +=$taxable_amt;
					$tt_total_tax +=$total_tax;
					$tt_acc_igst +=$acc_igst;				
					$tt_acc_cgst +=$acc_cgst;
					$tt_acc_sgst +=$acc_sgst;				
					$tt_acc_cess +=$acc_cess;
					$tt_acc_nonadv_cess +=$acc_nonadv_cess;
					
					
					
			
				    }
				    
				    $ttt_taxable_amt +=$tt_taxable_amt;
				    $ttt_total_tax +=$tt_total_tax;
				    $ttt_acc_igst +=$tt_acc_igst;
				  	$ttt_acc_cgst +=$tt_acc_cgst;
				    $ttt_acc_sgst +=$tt_acc_sgst;
                    $ttt_acc_cess +=$tt_acc_cess;
				    $ttt_acc_nonadv_cess +=$tt_acc_nonadv_cess;


			      }
				  
				
			
			   }	
				
			}
			$total_invoice_value = $ttt_taxable_amt+$ttt_total_tax;
		
	$data=["total_vouchers"=>$tt_total_vch_counters,"invoice_value"=>$total_invoice_value,
		      'taxable_amt'=>$ttt_taxable_amt,"igst"=>$ttt_acc_igst,"cgst"=>$ttt_acc_cgst,
			  "sgst"=>$ttt_acc_sgst,"cess"=>$ttt_acc_cess,"nonadv_cess"=>$ttt_acc_nonadv_cess,
			       "total_tax"=>$ttt_total_tax
				  ];	
	return $data;		  
	}
	
	
	function gst_hsn_table_info($from_date,$to_date,$vouchers_result){
		     $start = microtime(true);
            $acctgstsum_tbl     = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	        $comp_txn_tbl       = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	        $gstroutsup_tbl     = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
	        $gstrinwsup_tbl     = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
	       	
			$total_counter= array();
			$ttt_taxable_amt=0;
			$ttt_total_tax =0;
			$total_invoice_value=0;
			$ttt_acc_igst =0;
			$ttt_acc_cgst =0;
			$ttt_acc_sgst =0;
            $ttt_acc_cess =0;
			$ttt_acc_nonadv_cess =0;
		    $tt_total_vch_counters=0;
			if($vouchers_result){
			foreach($vouchers_result as $row){
			    $response=array();
				$voucher_txn_id = $row['voucher_txn_id'];
				$voucher_type_id = $row['voucher_type_id'];
				$total_vch_counters=0;
						
			     if($voucher_type_id==18){
					 $voucher_txn_id = $row['voucher_txn_id'];
					/****** Counter ************/ 
						$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
					 $builders = $this->db->table($acctgstsum_tbl.' gstsum');
    				$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
    				$builders->select('gstsup.outsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
    				$builders->orderBy('gstsum.vch_txn_id');	        
    				
    				$builders->where('gstsum.vch_txn_id', $voucher_txn_id);	
    				$builders->where('gstsum.acc_txn_date >=', $from_date);
    				$builders->where('gstsum.acc_txn_date <=', $to_date);				
    				
    				 $builders->groupBy('gstsum.vch_txn_id');		 
					 $txncounter = $builders->countAllResults();
					 $total_vch_counters +=$txncounter;
					 
					 $tt_total_vch_counters +=$total_vch_counters;
					 
					/******* Records ***********/ 
    				$builders = $this->db->table($acctgstsum_tbl.' gstsum');
    				$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
    				$builders->select('gstsup.outsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
    				$builders->orderBy('gstsum.vch_txn_id');	            				
    				$builders->where('gstsum.vch_txn_id', $voucher_txn_id);	
    				$builders->where('gstsum.acc_txn_date >=', $from_date);
    				$builders->where('gstsum.acc_txn_date <=', $to_date);				
    				
				    $response = $builders->get()->getResultArray();
				   $elapsed   = round((microtime(true) - $start) * 1000, 3);
				   $lsquery      = $this->db->getlastquery();
				   $qry_response =['url'=>current_url(true)->getPath(),'query'=>$lsquery,'time_seconds'=>$elapsed];
			       $this->SaveServerQueryLog($qry_response);		
				 
				 }
			
					 
			$total_voucher=0;
			$tt_credit = 0;
	      	$tt_debit = 0;
	      	$tt_credit_total = 0;
	      	$tt_debit_total = 0;
			$tt_taxable_amt =0;
			$tt_acc_igst=0;
			$tt_acc_cgst=0;
			$tt_acc_sgst=0;
			$tt_acc_cess=0;
			$tt_acc_nonadv_cess=0;
			$tt_total_tax=0;
			$tt_total_value=0;
			$tt_total_voucher=0;
			  if($response){
				foreach($response as $txrow){
                    $total_voucher++;						
					$taxable_amt= $txrow['taxable_amt'];
					$acc_igst    = $txrow['acc_igst'];
					$acc_cgst    = $txrow['acc_cgst'];
					$acc_sgst    = $txrow['acc_sgst'];
					$acc_cess    = $txrow['acc_cess'];
					$acc_nonadv_cess    = $txrow['acc_nonadv_cess'];
					$total_tax         = $txrow['total_tax'];
					$bill_ref_no      = $txrow['bill_ref_no'];
					
					
					$tt_total_voucher +=$total_voucher;
					$tt_taxable_amt +=$taxable_amt;
					$tt_total_tax +=$total_tax;
					$tt_acc_igst +=$acc_igst;				
					$tt_acc_cgst +=$acc_cgst;
					$tt_acc_sgst +=$acc_sgst;				
					$tt_acc_cess +=$acc_cess;
					$tt_acc_nonadv_cess +=$acc_nonadv_cess;
					
					
					
			
				    }
				    
				    $ttt_taxable_amt +=$tt_taxable_amt;
				    $ttt_total_tax +=$tt_total_tax;
				    $ttt_acc_igst +=$tt_acc_igst;
				  	$ttt_acc_cgst +=$tt_acc_cgst;
				    $ttt_acc_sgst +=$tt_acc_sgst;
                    $ttt_acc_cess +=$tt_acc_cess;
				    $ttt_acc_nonadv_cess +=$tt_acc_nonadv_cess;


			      }
				  
				
			
			   }	
				
			}
			$total_invoice_value = $ttt_taxable_amt+$ttt_total_tax;
		
	$data=["total_vouchers"=>$tt_total_vch_counters,"invoice_value"=>$total_invoice_value,
		      'taxable_amt'=>$ttt_taxable_amt,"igst"=>$ttt_acc_igst,"cgst"=>$ttt_acc_cgst,
			  "sgst"=>$ttt_acc_sgst,"cess"=>$ttt_acc_cess,"nonadv_cess"=>$ttt_acc_nonadv_cess,
			       "total_tax"=>$ttt_total_tax
				  ];	
		return $data;		  
	
	}
	function gstr2ab_hsn_table_info($from_date,$to_date,$vouchers_result){
		
            $acctgstsum_tbl     = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	        $comp_txn_tbl       = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	        $gstroutsup_tbl     = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
	        $gstrinwsup_tbl     = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
	       	
			$total_counter= array();
			$ttt_taxable_amt=0;
			$ttt_total_tax =0;
			$total_invoice_value=0;
			$ttt_acc_igst =0;
			$ttt_acc_cgst =0;
			$ttt_acc_sgst =0;
            $ttt_acc_cess =0;
			$ttt_acc_nonadv_cess =0;
		    $tt_total_vch_counters=0;
			if($vouchers_result){
			foreach($vouchers_result as $row){
			    $response=array();
				$voucher_txn_id = $row['voucher_txn_id'];
				$voucher_type_id = $row['voucher_type_id'];
				$total_vch_counters=0;
						
			     if($voucher_type_id==11){
					 $voucher_txn_id = $row['voucher_txn_id'];
					/****** Counter ************/ 
						$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
					 $builders = $this->db->table($acctgstsum_tbl.' gstsum');
    				$builders->join($gstrinwsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
    				$builders->select('gstsup.inwsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
    				$builders->orderBy('gstsum.vch_txn_id');	        
    				
    				$builders->where('gstsum.vch_txn_id', $voucher_txn_id);	
    				$builders->where('gstsum.acc_txn_date >=', $from_date);
    				$builders->where('gstsum.acc_txn_date <=', $to_date);				
    				
    				 $builders->groupBy('gstsum.vch_txn_id');		 
					 $txncounter = $builders->countAllResults();
					 $total_vch_counters +=$txncounter;
					 
					 $tt_total_vch_counters +=$total_vch_counters;
					 
					/******* Records ***********/ 
    				$builders = $this->db->table($acctgstsum_tbl.' gstsum');
    				$builders->join($gstrinwsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
    				$builders->select('gstsup.inwsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
    				$builders->orderBy('gstsum.vch_txn_id');	            				
    				$builders->where('gstsum.vch_txn_id', $voucher_txn_id);	
    				$builders->where('gstsum.acc_txn_date >=', $from_date);
    				$builders->where('gstsum.acc_txn_date <=', $to_date);				
    				
				    $response = $builders->get()->getResultArray();
				  
				 
				 }
			
					 
			$total_voucher=0;
			$tt_credit = 0;
	      	$tt_debit = 0;
	      	$tt_credit_total = 0;
	      	$tt_debit_total = 0;
			$tt_taxable_amt =0;
			$tt_acc_igst=0;
			$tt_acc_cgst=0;
			$tt_acc_sgst=0;
			$tt_acc_cess=0;
			$tt_acc_nonadv_cess=0;
			$tt_total_tax=0;
			$tt_total_value=0;
			$tt_total_voucher=0;
			  if($response){
				foreach($response as $txrow){
                    $total_voucher++;						
					$taxable_amt= $txrow['taxable_amt'];
					$acc_igst    = $txrow['acc_igst'];
					$acc_cgst    = $txrow['acc_cgst'];
					$acc_sgst    = $txrow['acc_sgst'];
					$acc_cess    = $txrow['acc_cess'];
					$acc_nonadv_cess    = $txrow['acc_nonadv_cess'];
					$total_tax         = $txrow['total_tax'];
					$bill_ref_no      = $txrow['bill_ref_no'];
					
					
					$tt_total_voucher +=$total_voucher;
					$tt_taxable_amt +=$taxable_amt;
					$tt_total_tax +=$total_tax;
					$tt_acc_igst +=$acc_igst;				
					$tt_acc_cgst +=$acc_cgst;
					$tt_acc_sgst +=$acc_sgst;				
					$tt_acc_cess +=$acc_cess;
					$tt_acc_nonadv_cess +=$acc_nonadv_cess;
					
					
					
			
				    }
				    
				    $ttt_taxable_amt +=$tt_taxable_amt;
				    $ttt_total_tax +=$tt_total_tax;
				    $ttt_acc_igst +=$tt_acc_igst;
				  	$ttt_acc_cgst +=$tt_acc_cgst;
				    $ttt_acc_sgst +=$tt_acc_sgst;
                    $ttt_acc_cess +=$tt_acc_cess;
				    $ttt_acc_nonadv_cess +=$tt_acc_nonadv_cess;


			      }
				  
				
			
			   }	
				
			}
			$total_invoice_value = $ttt_taxable_amt+$ttt_total_tax;
		
	$data=["total_vouchers"=>$tt_total_vch_counters,"invoice_value"=>$total_invoice_value,
		      'taxable_amt'=>$ttt_taxable_amt,"igst"=>$ttt_acc_igst,"cgst"=>$ttt_acc_cgst,
			  "sgst"=>$ttt_acc_sgst,"cess"=>$ttt_acc_cess,"nonadv_cess"=>$ttt_acc_nonadv_cess,
			       "total_tax"=>$ttt_total_tax
				  ];	
		return $data;		  
	
	}
	function get_vchr_txnid($voucher_txn_id){
	 $acctcrsref_tbl = $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');	
     $response = $this->db->table($acctcrsref_tbl)->select('voucher_txn_id,acc_cross_ref_data,acc_cross_ref_type,bo_id,comp_id')
	                  ->where('comp_id',$this->company_id)
					  ->where('bo_id',$this->session->get('ses_boid'))
					  ->where('voucher_txn_id',$voucher_txn_id)
					  ->get()->getRowArray();
	 if($response)				  
	 return $response['acc_cross_ref_data'];		
     else
     return false;		 
		
	}
	
	function add_gstroutsup_data($data)
    {   if(!isset($data['outsup_rev_chg']))
		  $data['outsup_rev_chg']=0;
         $gstroutsup_tbl = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
         $this->db->table($gstroutsup_tbl)->insert($data);
    }
	
	function update_gstroutsup_data($voucher_txn_id,$data){
		
		 if(!isset($data['outsup_rev_chg']))
		  $data['outsup_rev_chg']=0;
	  
		$response = $this->gstroutsup_info($voucher_txn_id);
		if($response){		
			$gstroutsup_tbl = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
			$this->db->table($gstroutsup_tbl)->where('voucher_txn_id',$voucher_txn_id)->update($data);
        //$df = $this->db->GetLastQuery();
			//SaveErrorLog($df);
		}
		else{			 
			$gstroutsup_insert_data = array("outsup_rev_chg"=>$data['outsup_rev_chg'],"outsup_bill_ref_no"=>$data['outsup_bill_ref_no'],"voucher_txn_id"=>$data['voucher_txn_id'],"outsup_pos"=>$data['outsup_pos'],"outsup_inv_type"=>$data['outsup_inv_type']);
			$this->add_gstroutsup_data($gstroutsup_insert_data);
			//$df = $this->db->GetLastQuery();
			//$SaveErrorLog($df);			
		   }	
	 }
	
	function gstrinwsup_info($voucher_txn_id){
	 $gstrinwsup_tbl = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');	
     $response = $this->db->table($gstrinwsup_tbl)->where('voucher_txn_id',$voucher_txn_id)->get()->getRowArray();
	 return $response;			
	}
	
	function add_ewbmastern_info($ewb_id,$data)
    {    $ewayBillDate   = date('Y-m-d H:i:s',strtotime($data["ewayBillDate"])); 
    	 $validUpto      = date('Y-m-d H:i:s',strtotime($data["validUpto"])); 
    	 $rep_data       = array("ewb_id"=>$ewb_id,"ewb_no"=>$data["ewayBillNo"],"ewb_date"=>$ewayBillDate,"ewb_valid_dt"=>$validUpto,"ewb_status"=>'PART-B PENDING');
	     $ewbmastern_tbl = $this->company_id.'_ewbmastern_'.$this->session->get('ses_comp_fy_id');
         $this->db->table($ewbmastern_tbl)->insert($rep_data);
    }
	
	function update_ewbmastern_info($ewb_id,$data){
		if($data['status']=='ACT'){
			$transporterName = $data['transporterName'];
			$transporterId   = $data['transporterId'];
			if($transporterId=='')
			$rep_data       = array("ewb_status"=>'PART-B PENDING');
            else
            $rep_data       = array("ewb_status"=>'ACTIVE');				
		}
		else{
		   $rep_data       = array("ewb_status"=>$data['status']);	
		}
		 
		$ewbmastern_tbl = $this->company_id.'_ewbmastern_'.$this->session->get('ses_comp_fy_id');
		   
        $this->db->table($ewbmastern_tbl)->where('ewb_id',$ewb_id)->update($rep_data);
	}
	
	function add_einvmstreq_info($voucher_txn_id,$save_data){
		 $einvmstreq_tbl = $this->company_id.'_einvmstreq_'.$this->session->get('ses_comp_fy_id');
         $this->db->table($einvmstreq_tbl)->insert($save_data);
	}
	
	function update_einvmaster_info($envid,$successData){
		 $einvmaster_tbl = $this->company_id.'_einvmaster_'.$this->session->get('ses_comp_fy_id');
		 if($successData['Status']=='ACT') 
			 $status = 'ACTIVE';
		 if($successData['Status']=='CNL')	 
			 $status = 'CANCELLED';
			$update_data = array('einv_status'=>$status); 
         $this->db->table($einvmaster_tbl)->where('einv_id',$envid)->update($update_data);
	}
	
	function add_einvmaster_info($voucher_txn_id,$data)
     {  
	     $einv_date      = date('Y-m-d H:i:s',strtotime($data["AckDt"])); 
    	 $einv_valid_dt  = date('Y-m-d H:i:s',strtotime($data["AckDt"])); 
		 $einv_status  ='';
		 if($data["Status"]=='ACT')
			 $einv_status ='ACTIVE';
		 if($data["Status"]=='CNL')
			 $einv_status ='CANCELLED';
    	 $rep_data       = array("einv_no"=>$data["AckNo"],
		                         "einv_date"=>$einv_date,"einv_valid_dt"=>$einv_valid_dt,
								 "einv_status"=>$einv_status,"einv_irn"=>$data["Irn"],
								 "einv_SignedInvoice"=>$data["SignedInvoice"],
								 'voucher_txn_id'=>$voucher_txn_id,
								 "einv_QRCode"=>$data["SignedQRCode"]);
	     $einvmaster_tbl = $this->company_id.'_einvmaster_'.$this->session->get('ses_comp_fy_id');
         $this->db->table($einvmaster_tbl)->insert($rep_data);
		 $einv_id =  $this->db->insertID();
		 if(isset($data['EwbNo']) && $data['EwbNo']!=''){
			 $get_ewbmstreqn_data = $this->get_ewbmstreqn_data($voucher_txn_id);
			 if($get_ewbmstreqn_data){
			     $ewb_id = $get_ewbmstreqn_data['ewb_id'];
				 
				 	
			  }			 
		 }
		 return $einv_id;
    }
	
	function add_gstrinwsup_data($data)
    {
		 if(!isset($data['inwsup_rev_chg']))
		  $data['inwsup_rev_chg']=0;
	  if(!isset($data['inwsup_eco']))
		  $data['inwsup_eco']=0;
         $gstrinwsup_tbl = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
         $this->db->table($gstrinwsup_tbl)->insert($data);
    }
	function update_gstrinwsup_data($voucher_txn_id,$data)
    {
		 if(!isset($data['inwsup_rev_chg']))
		  $data['inwsup_rev_chg']=0;
	  if(!isset($data['inwsup_eco']))
		  $data['inwsup_eco']=0;
	  
         $gstrinwsup_tbl = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
			$exists = $this->db->table($gstrinwsup_tbl)->where('voucher_txn_id',$voucher_txn_id)->countAllResults();
        if($exists>0)
			$this->db->table($gstrinwsup_tbl)->where('voucher_txn_id',$voucher_txn_id)->update($data);
        else{
			$data['voucher_txn_id']=$voucher_txn_id;
			$this->add_gstrinwsup_data($data);
		}
	
	
	}
    
    function update_ewbmstreqn_data($voucher_txn_id,$data){
        $ewbmstreqn_tbl = $this->company_id.'_ewbmstreqn_'.$this->session->get('ses_comp_fy_id');
         $this->db->table($ewbmstreqn_tbl)->where('voucher_txn_id',$voucher_txn_id)->update($data); 
    }
    
    function get_ewbmstreqn_data($voucher_txn_id){
       $ewbmstreqn_tbl = $this->company_id.'_ewbmstreqn_'.$this->session->get('ses_comp_fy_id'); 
       $gstshipton_tbl = $this->company_id.'_gstshipton_'.$this->session->get('ses_comp_fy_id'); 
       $gstdispfrm_tbl = $this->company_id.'_gstdispfrm_'.$this->session->get('ses_comp_fy_id'); 
         
	   $response =  $this->db->table($ewbmstreqn_tbl)->where('voucher_txn_id',$voucher_txn_id)->get()->getRowArray();
       if($response){
		   // gstshipton  , gstdispfrm details
		   $gstshipton_info =  $this->db->table($gstshipton_tbl)->where('ewb_id',$response['ewb_id'])->get()->getRowArray();
           $gstdispfrm_info =  $this->db->table($gstdispfrm_tbl)->where('ewb_id',$response['ewb_id'])->get()->getRowArray();
        
		   $response['gstdispfrm_info']=$gstdispfrm_info;
		   $response['gstshipton_info']=$gstshipton_info;
           return $response;
       }
       else{
           return ["ewb_supply_type"=>"","ewb_txn_type"=>"","ewb_sub_supply_desc"=>"","gstdispfrm_info"=>"","gstshipton_info"=>""];
           
       }
    }
    
	function getcompany_info($company_id){	 
	  return $this->dberpunvrsl->table('aictlyerp_compmastern_univdb')->where('comp_id', $company_id)->get()->getRowArray();   	   
   }
   public function groupmst_info($crs_master_id,$crs_master_type){
	return $this->dberpunvrsl->table('aictlyerp_grpcomstid_univdb')->where('crs_master_type', $crs_master_type)->where('crs_master_id', $crs_master_id)->get()->getRowArray();   	    
 }
 public function create_gstpaid_account(){
 	$account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');  
	$response = $this->db->table($account_master_tbl)->where('acc_short_code','sagstpd')->where('LOWER(acc_name)',strtolower('GST PAID A/C'))->get()->getRowArray();   
	if($response){
		$return_arr =['status'=>true,'acc_id'=>$response['acc_id'],'message'=>''];
	 return $return_arr;
	} else{
		
	$acc_insert_data   = [
						'comp_id'           => $this->company_id,
						'acc_name'          => 'GST PAID A/C',
						'acc_name_alias'    => 'GST PAID A/C',
						'acc_name_print'    => 'GST PAID A/C',
						'acc_grp_id'        => 0,
						'acc_grp_parent_id' => 13,
						'acc_short_code'    => 'sagstpd'
					    ];
			
        $this->db->table($account_master_tbl)->insert($acc_insert_data);		 
        $account_id = $this->db->insertID();

        $ERPtables = new ERPtables($this->company_id,$this->session->get('ses_comp_fy_id'));
		$errors = $ERPtables->account_txn_tables($account_id);

        $mst_base_id = $this->create_mst_base_id($account_id,'acctmaster');
		$this->db->table($account_master_tbl)
					->where('acc_id',$account_id)
					->update(['mst_base_id' => $mst_base_id]);
					
		$return_arr =['status'=>true,'acc_id'=>$account_id,'message'=>''];
		return $return_arr;
	}
 }
 
 public function remove_mapping_group_master($master_id,$crs_master_type){
	 $uuid =  $this->session->get('uuid');
	 $builder = $this->dberpunvrsl->table('aictlyerp_grpmasternn_univdb');
	 $builder->where('aictlyerp_grpmasternn_univdb.uuid',$uuid);   
	 $response =  $builder->get()->getResultArray();
	 $final_companies=array();	 
	 if($response){ 
		 $all_group_masters[]=array();
		 foreach($response as $row){
			 $grpco_id = $row['grpco_id'];
			 $builder1 = $this->dberpunvrsl->table('aictlyerp_grpmapping_univdb');
			 $builder1->where('grpco_id',$grpco_id);
             $builder1->where('master_id',$master_id);
             $builder1->where('master_type',$crs_master_type);	
             $builder1->where('comp_id',$this->company_id);			 
			 $response1 =  $builder1->get()->getRowArray();
			 if($response1){
				$crs_master_id =  $response1['crs_master_id'];
				$grpco_id      =  $response1['grpco_id'];
				$grpmpid       =  $response1['grpmpid'];
				$master_type   = $response1['master_type'];
				$this->dberpunvrsl->table('aictlyerp_grpmapping_univdb')->where('master_type', $master_type)->where('grpmpid', $grpmpid)->where('grpco_id', $grpco_id)->where('crs_master_id', $crs_master_id)->delete();   	  
				$this->dberpunvrsl->table('aictlyerp_grpcomstid_univdb')->where('crs_master_type', $master_type)->where('grpco_id', $grpco_id)->where('crs_master_id', $crs_master_id)->delete();   	  
			 }			 
		 }
	 }
   }
   
    function update_mapping_group($master_id,$master_type,$comp_id){
       $acctmaster_tbl  =  $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	   $acctgrpmst_tbl  =  $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
	   $billsundry_tbl   = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
       $master_grp_parent_id=0;
	   if($master_type=='acc'){  
					   $acc_result  =  $this->db->query("SELECT acc_id,acc_grp_id,acc_grp_parent_id FROM `".$acctmaster_tbl."` WHERE acc_id='".$master_id."' ");
					   $acc_response = $acc_result->getRowArray();	
					   if($acc_response){
						  $acc_grp_parent_id = $acc_response['acc_grp_parent_id'];
						  $acc_grp_id        = $acc_response['acc_grp_id'];
					   
					   if(trim($acc_grp_parent_id)==0){  
				  
						$result  =  $this->db->query("SELECT acc_grp_parent_id FROM `".$acctgrpmst_tbl."` WHERE acc_grp_id='".$acc_grp_id."' ");
						 $response = $result->getRowArray();		
						if($response){		   
							   $master_grp_parent_id = $response['acc_grp_parent_id'];
						  }
						  else{
							 $master_grp_parent_id = $acc_grp_parent_id;	  
						  }
						 }else{
						  $master_grp_parent_id = $acc_grp_parent_id;		   
						  } 
					  
					   } 
					  }
					  if( $master_type=='bsd'){  
					   $acc_result  =  $this->db->query("SELECT bill_sundry_id,acc_grp_id,acc_grp_parent_id FROM `".$billsundry_tbl."` WHERE bill_sundry_id='".$master_id."' ");
					   $acc_response = $acc_result->getRowArray();	
					   if($acc_response){
						  $acc_grp_parent_id = $acc_response['acc_grp_parent_id'];
						  $acc_grp_id        = $acc_response['acc_grp_id'];
					   
					   if(trim($acc_grp_parent_id)==0){  
				  
						$result  =  $this->db->query("SELECT acc_grp_parent_id FROM `".$acctgrpmst_tbl."` WHERE acc_grp_id='".$acc_grp_id."' ");
						 $response = $result->getRowArray();		
						if($response){		   
							   $master_grp_parent_id = $response['acc_grp_parent_id'];
						  }
						  else{
							 $master_grp_parent_id = $acc_grp_parent_id;	  
						  }
						 }else{
						  $master_grp_parent_id = $acc_grp_parent_id;		   
						  } 
					  
					   } 
					  }

	$data  = array("acc_grp_parent_id"=>$master_grp_parent_id);	
	  $this->dberpunvrsl->table('aictlyerp_grpmapping_univdb')->where('master_id',$master_id)
	                    ->where('master_type',$master_type)
						->where('comp_id',$comp_id)
						->update($data);
		
	}
 
    public function mapping_group_master($master_id,$crs_master_ids,$crs_master_type,$crs_master_name){
		if($crs_master_ids){
			foreach($crs_master_ids as $key => $crs_master_id){
				if($crs_master_id>0){
					$groupmst_info  = $this->groupmst_info($crs_master_id,$crs_master_type);
					$grpco_id       = $groupmst_info['grpco_id'];		
					$data		     = array("grpco_id"=>$grpco_id,"master_id"=>$master_id,"master_type"=>$crs_master_type,
											"crs_master_id"=>$crs_master_id,"comp_id"=>$this->company_id);
					$this->dberpunvrsl->table('aictlyerp_grpmapping_univdb')->insert($data);
				}
				else{					
				 $this->new_mapping_group_master($master_id,$crs_master_type,$crs_master_name);	
				}
			}			
		}
	}
	
	
	public function new_mapping_group_master($master_id,$crs_master_type,$crs_master_name){	
	 $uuid =  $this->session->get('uuid');
	 $builder = $this->dberpunvrsl->table('aictlyerp_grpmasternn_univdb');
	 $builder->where('aictlyerp_grpmasternn_univdb.uuid',$uuid);   
	 $response =  $builder->get()->getResultArray();
	 $final_companies=array();	 
	 if($response){
		 $all_group_masters[]=array();
		 foreach($response as $row){
			 $grpco_id = $row['grpco_id'];
			 $builder1 = $this->dberpunvrsl->table('aictlyerp_grpcompacs_univdb');
			 $builder1->where('aictlyerp_grpcompacs_univdb.grpco_id',$grpco_id);
			 $builder1->where('aictlyerp_grpcompacs_univdb.comp_id',$this->company_id);
			 
			 $response1 =  $builder1->get()->getResultArray();
			 
			 if($response1){
				 
				 foreach($response1 as $key =>  $row1){					
					$grpco_id   = $row1['grpco_id'];
			        $comp_id    = $row1['comp_id'];		
			$res_exists = $this->dberpunvrsl->table('aictlyerp_grpcomstid_univdb')->where('comp_id',",".$comp_id.",")->where('grpco_id',$grpco_id)->where('LOWER(crs_master_name)',$crs_master_name)->where('crs_master_type',$crs_master_type)->get()->getRowArray();
			if(!$res_exists){	
				$insert_data = array("grpco_id"=>$grpco_id,"crs_master_type"=>$crs_master_type,"crs_master_name"=>$crs_master_name,
				                     "comp_id"=>",".$comp_id.",");
				$this->dberpunvrsl->table('aictlyerp_grpcomstid_univdb')->insert($insert_data);
				$crs_master_id = $this->dberpunvrsl->insertID();
				
				$data = array("grpco_id"=>$grpco_id,"master_id"=>$master_id,"master_type"=>$crs_master_type,
							  "crs_master_id"=>$crs_master_id,"comp_id"=>$comp_id);
				$this->dberpunvrsl->table('aictlyerp_grpmapping_univdb')->insert($data);	
			}
					
				  }
			 }
			 
	 
		 }
	 }
		
	}
	
   
	public function Load_Group_Companies($master_type){
	 $uuid =  $this->session->get('uuid');
	 $builder = $this->dberpunvrsl->table('aictlyerp_grpmasternn_univdb');
	 $builder->select('aictlyerp_grpcompacs_univdb.comp_id,aictlyerp_grpcompacs_univdb.grpco_id,aictlyerp_grpmasternn_univdb.grpco_name');
	 $builder->join('aictlyerp_grpcompacs_univdb','aictlyerp_grpcompacs_univdb.grpco_id=aictlyerp_grpmasternn_univdb.grpco_id');
	 $builder->where('aictlyerp_grpmasternn_univdb.uuid',$uuid);
     $builder->where('aictlyerp_grpcompacs_univdb.comp_id',$this->company_id);	 
	 $response =  $builder->get()->getResultArray();
	 //echo  "???".$this->dberpunvrsl->GetLastQuery();
	 //echo'<br>';
	
	 $final_companies=array();	 
	 if($response){
		 foreach($response as $row){
			 $grpco_id   = $row['grpco_id'];
			 $grpco_name = $row['grpco_name'];
			 $comp_id    = $row['comp_id'];
			 $member_master_lists =$this->member_master_lists($grpco_id,$comp_id,$master_type);
			 $final_companies[]= array(
					  'grpco_id'  => $grpco_id,
                      'grp_name'  => $grpco_name,
					  'comp_id'   => $comp_id,
                      'member_master_lists'=>$member_master_lists					  
					  );
			 
		 }
		 
	 }
	 return $final_companies;	
	}
	
	public function member_master_lists($grpco_id,$comp_id,$crs_master_type){	
     $builder = $this->dberpunvrsl->table('aictlyerp_grpmasternn_univdb');
	 $builder->select('aictlyerp_grpcomstid_univdb.grpco_id,aictlyerp_grpcomstid_univdb.comp_id,aictlyerp_grpcomstid_univdb.crs_master_id,aictlyerp_grpcomstid_univdb.crs_master_name');
	 $builder->join('aictlyerp_grpcomstid_univdb','aictlyerp_grpcomstid_univdb.grpco_id=aictlyerp_grpmasternn_univdb.grpco_id');
	 $builder->where('aictlyerp_grpmasternn_univdb.uuid',$this->session->get('uuid'));	 
	 $builder->where('aictlyerp_grpcomstid_univdb.crs_master_type',$crs_master_type);
	 $builder->where('aictlyerp_grpcomstid_univdb.grpco_id',$grpco_id);
	 if($comp_id!=''){
	    $builder->where('aictlyerp_grpcomstid_univdb.comp_id LIKE "%,'.$comp_id.',%" ');
	 }
 
	 $builder->orderBy('aictlyerp_grpcomstid_univdb.crs_master_id');
	 $response =  $builder->get()->getResultArray(); 
	 //echo  $this->dberpunvrsl->GetLastQuery();
	 //echo'<br>';	 
	return $response; 
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

    function add_sundry_txn_data($data,$VchOthrBo=0) // 1
    {
    	$rate = 1;

	   	$voucher_info = $this->get_voucher_cons_info($data['voucher_txn_id']);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$rate = $this->get_forex_rate_inr($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

	   	$data['sundry_txn_amount'] = ($data['sundry_txn_amount'] * $rate);
	   	$data['sundry_tag_rate'] = (floatval($data['sundry_tag_rate']) * $rate);

        $sundry_txn_tbl=$this->company_id.'_sundrytxnn_'.$data['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
		 
		if(isset($data['sundry_txn_narr'])){
			$this->save_voucher_narration($data['voucher_txn_id'],$data['txn_id'],'short',$data['sundry_txn_narr']);
			unset($data['sundry_txn_narr']);		 
		 }
		 
		 if($VchOthrBo==0)
		 $data['bo_id']= $this->session->get('ses_boid');
		 else
		 $data['bo_id'] =$VchOthrBo;
		 $this->db->table($sundry_txn_tbl)->insert($data);

		 $this->update_bill_sundry_balance($data['bill_sundry_id']);
    }

    
    function add_bill_master($data,$VchOthrBo=0) // 1
    {
        $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
        $bill_mst = $this->db->table($bill_mst_tbl)
						->where('acc_id', $data['acc_id'])
						->where('bills_ref_name', $data['bills_ref_name'])
						->get()->getRowArray();
		if($bill_mst){
			return $bill_mst['bills_ref_id'];
		}
		$mst_data = [
			'bills_ref_name' => $data['bills_ref_name'],
			'acc_id'		 => $data['acc_id'],
			'bill_due_date'	 => validate_date_by_fy($data['bill_due_date']),
			'bills_status'	 => 'pending',
		];
        $this->db->table($bill_mst_tbl)->insert($mst_data);
        $bills_ref_id = $this->db->insertID();
        
        if($VchOthrBo==0)
         $bo_id = $this->bo_id;
         else
         $bo_id = $VchOthrBo;
        $op_data = [
      		'bills_ref_id' 	=> $bills_ref_id,
      		'bo_id' 		=> $this->bo_id,
      		'bills_op_bal' 	=> $data['bills_op_bal'] ?? 0,
      		'bills_py_bal' 	=> 0,
      	];
      	$billsoppyn_tbl = $this->company_id.'_billsoppyn_'.$this->session->get('ses_comp_fy_id');
      	$this->db->table($billsoppyn_tbl)->insert($op_data);
		$mst_base_id = $this->create_mst_base_id($bills_ref_id,'billmaster');
		$this->db->table($bill_mst_tbl)
				->where('bills_ref_id',$bills_ref_id)
				->update(['mst_base_id' => $mst_base_id]);
	    return $bills_ref_id;
    }
 
    function delete_bill_master($bills_ref_id) // 1
    {
    	$bill_txn_tbl = $this->company_id.'_billstxnnn_'.$this->session->get('ses_comp_fy_id');
 
    	$bill_txn = $this->db->table($bill_txn_tbl)
						->where('bills_ref_id', $bills_ref_id)
						->get()->getResultArray();
		if($bill_txn){
			return false;
		}
 
        $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
 
      	$this->db->table($bill_mst_tbl)
					->where('bills_ref_id', $bills_ref_id)
					->delete();
 
		$this->delete_comp_fy_mst_map($bills_ref_id,'billmaster');
		return true;
    }
	
    function add_bill_txn($data,$VchOthrBo=0)
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
		
		if($VchOthrBo==0)
		$data['bo_id'] = $this->bo_id;
		else
		$data['bo_id'] = $VchOthrBo;
		
        $this->db->table($bill_txn_tbl)->insert($data);
        $txnid = $this->db->insertID();
		$this->save_voucher_narration($data['voucher_txn_id'],$txnid,'short',$last_data['bills_txn_narr']);
		
        $this->update_bill_txn_balance($data['bills_ref_id']);
    }

    function add_cc_txn($data,$VchOthrBo=0)
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
		
		 if($this->session->get('ses_boid')!='' && $VchOthrBo==0)
			$bo_id =  $this->session->get('ses_boid');
		  else 
			$bo_id = $VchOthrBo;
			
		$data['bo_id'] = $bo_id;
        $this->db->table($cc_txn_tbl)->insert($data);
		$txnid = $this->db->insertID();
		$this->save_voucher_narration($data['voucher_txn_id'],$txnid,'short',$last_data['cc_txn_narr']);		 
				
        $this->update_cc_txn_balance($data['cc_id']);
    }

    function get_account_parent_id($acc_id,$type)
    {
    	$acc_grp_parent_id = 0;

    	if($type == 'acc'){
    		$account_info = $this->get_account_info($acc_id);
	    	if($account_info){
	    		$acc_grp_id = $account_info['acc_grp_id'];
	    		if($acc_grp_id == 0){
	    			$acc_grp_parent_id = $account_info['acc_grp_parent_id'];
	    		}
	    		else{
	    			$group_info = $this->group_info($account_info['acc_grp_id']);
	    			if($group_info){
	    				$acc_grp_parent_id = $group_info['acc_grp_parent_id'];
	    			}
	    		}
	    	}
    	}

    	if($type == 'bsd'){
    		$account_info = $this->get_billsundry_info($acc_id);
	    	if($account_info){
	    		$acc_grp_id = $account_info['acc_grp_id'];
	    		if($acc_grp_id == 0){
	    			$acc_grp_parent_id = $account_info['acc_grp_parent_id'];
	    		}
	    		else{
	    			$group_info = $this->group_info($account_info['acc_grp_id']);
	    			if($group_info){
	    				$acc_grp_parent_id = $group_info['acc_grp_parent_id'];
	    			}
	    		}
	    	}
    	}
	    	

    	return $acc_grp_parent_id;
    }

    function add_pr_txn_data($data,$VchOthrBo=0)
    {
    	$rate = 1;

	   	$voucher_info = $this->get_voucher_cons_info($data['voucher_txn_id']);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$rate = $this->get_forex_rate_inr($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

	   	$data['proj_txn_amt'] = ($data['proj_txn_amt'] * $rate);
	   	if($VchOthrBo==0)
	   	$data['bo_id']	= $this->bo_id;
	   	else
	   	$data['bo_id'] = $VchOthrBo;
	   	
	   	$proj_txn_narr = $data['proj_txn_narr'];
	   	unset($data['proj_txn_narr']);

	   	$acc_grp_parent_id = $this->get_account_parent_id($data['acc_id'],$data['acc_type']);

	   	//liability
	   	if(in_array($acc_grp_parent_id, [1,2,4])){
	   		$data['project_id'] = $data['project_id'] == 0 ? 1 : $data['project_id'];

	   		$prjliabtxn_tbl = $this->company_id.'_prjliabtxn_'.$this->session->get('ses_comp_fy_id');
	   		$this->db->table($prjliabtxn_tbl)->insert($data);
	   		$proj_txn_id = $this->db->insertID();
	   		$this->update_project_lia_bal($data['project_id']);
	   	}

	   	//assets
	   	if(in_array($acc_grp_parent_id, [3,5])){
	   		$data['project_id'] = $data['project_id'] == 0 ? 1 : $data['project_id'];

	   		$projasttxn_tbl = $this->company_id.'_projasttxn_'.$this->session->get('ses_comp_fy_id');
	   		$this->db->table($projasttxn_tbl)->insert($data);
	   		$proj_txn_id = $this->db->insertID();
	   		$this->update_project_ast_bal($data['project_id']);
	   	}

	   	//expense
	   	if(in_array($acc_grp_parent_id, [7,11,13])){

	   		$data['project_id'] = $data['project_id'] == 0 ? 1 : $data['project_id'];

	   		$projexptxn_tbl = $this->company_id.'_projexptxn_'.$this->session->get('ses_comp_fy_id');
	   		$this->db->table($projexptxn_tbl)->insert($data);
	   		$proj_txn_id = $this->db->insertID();
	   		$this->update_project_exp_bal($data['project_id']);
	   	}

	   	//revenue
	   	if(in_array($acc_grp_parent_id, [8,10,12])){
	   		$data['project_id'] = $data['project_id'] == 0 ? 1 : $data['project_id'];

	   		$projrevtxn_tbl = $this->company_id.'_projrevtxn_'.$this->session->get('ses_comp_fy_id');
	   		$this->db->table($projrevtxn_tbl)->insert($data);
	   		$proj_txn_id = $this->db->insertID();
	   		$this->update_project_rev_bal($data['project_id']);
	   	}


	   	if(isset($proj_txn_id)){
	   		$this->save_voucher_narration($data['voucher_txn_id'],$proj_txn_id,'short',$proj_txn_narr);
	   	}
    }



    function get_pr_txn_data($voucher_txn_id)
    {
    	$fcy_rate = 1;
    	$final = [];

	   	$voucher_info = $this->get_voucher_cons_info($voucher_txn_id);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$fcy_rate = $this->get_forex_rate_fcy($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

    	$projexptxn_tbl = $this->company_id.'_projexptxn_'.$this->session->get('ses_comp_fy_id');
    	$projrevtxn_tbl = $this->company_id.'_projrevtxn_'.$this->session->get('ses_comp_fy_id');
    	$prjliabtxn_tbl = $this->company_id.'_prjliabtxn_'.$this->session->get('ses_comp_fy_id');
    	$projasttxn_tbl = $this->company_id.'_projasttxn_'.$this->session->get('ses_comp_fy_id');
    	$final = [];

    	$result_ = $this->db->table($projexptxn_tbl)
    						->select('acc_id,acc_type')
	   						->where('voucher_txn_id', $voucher_txn_id)
	   						->where('project_id !=', 1)
	   						->groupBy('acc_id')
	   						->groupBy('acc_type')
	   						->get()->getResultArray();

	   	foreach ($result_ as $key_ => $value_) {

	   		$result = $this->db->table($projexptxn_tbl)
		   						->where('voucher_txn_id', $voucher_txn_id)
		   						->where('project_id !=', 1)
		   						->where('acc_id', $value_['acc_id'])
		   						->where('acc_type', $value_['acc_type'])
		   						->get()->getResultArray();

		   	foreach ($result as $key => $value) {

		   		$result[$key]['proj_txn_amt'] = parseAmount($value['proj_txn_amt'] * $fcy_rate);

		   		$project_info = $this->get_project_info($value['project_id']);
		   		$result[$key]['project_name'] = $project_info['project_name'] ?? '';

		   		$narration_info = $this->get_voucher_narration_info($value['voucher_txn_id'],'short',$value['proj_txn_id']);
		   		$result[$key]['proj_txn_narr'] = $narration_info['vch_short_narr'] ?? '';
		   	}

	   		$final[] = [
	   			'acc_id' 		=> $value_['acc_id'],
	   			'acc_type' 		=> $value_['acc_type'],
	   			'pr_txn_list' 	=> $result,
	   		];

	   	}

		   	
	   	$result_ = $this->db->table($projrevtxn_tbl)
    						->select('acc_id,acc_type')
	   						->where('voucher_txn_id', $voucher_txn_id)
	   						->where('project_id !=', 1)
	   						->groupBy('acc_id')
	   						->groupBy('acc_type')
	   						->get()->getResultArray();

	   	foreach ($result_ as $key_ => $value_) {

	   		$result = $this->db->table($projrevtxn_tbl)
		   						->where('voucher_txn_id', $voucher_txn_id)
		   						->where('project_id !=', 1)
		   						->where('acc_id', $value_['acc_id'])
		   						->where('acc_type', $value_['acc_type'])
		   						->get()->getResultArray();

		   	foreach ($result as $key => $value) {
		   		$project_info = $this->get_project_info($value['project_id']);
		   		$result[$key]['project_name'] = $project_info['project_name'] ?? '';

		   		$narration_info = $this->get_voucher_narration_info($value['voucher_txn_id'],'short',$value['proj_txn_id']);
		   		$result[$key]['proj_txn_narr'] = $narration_info['vch_short_narr'] ?? '';
		   	}

	   		$final[] = [
	   			'acc_id' 		=> $value_['acc_id'],
	   			'acc_type' 		=> $value_['acc_type'],
	   			'pr_txn_list' 	=> $result,
	   		];
	   	}

	   	$result_ = $this->db->table($prjliabtxn_tbl)
    						->select('acc_id,acc_type')
	   						->where('voucher_txn_id', $voucher_txn_id)
	   						->where('project_id !=', 1)
	   						->groupBy('acc_id')
	   						->groupBy('acc_type')
	   						->get()->getResultArray();

	   	foreach ($result_ as $key_ => $value_) {

	   		$result = $this->db->table($prjliabtxn_tbl)
		   						->where('voucher_txn_id', $voucher_txn_id)
		   						->where('project_id !=', 1)
		   						->where('acc_id', $value_['acc_id'])
		   						->where('acc_type', $value_['acc_type'])
		   						->get()->getResultArray();

		   	foreach ($result as $key => $value) {
		   		$project_info = $this->get_project_info($value['project_id']);
		   		$result[$key]['project_name'] = $project_info['project_name'] ?? '';

		   		$narration_info = $this->get_voucher_narration_info($value['voucher_txn_id'],'short',$value['proj_txn_id']);
		   		$result[$key]['proj_txn_narr'] = $narration_info['vch_short_narr'] ?? '';
		   	}

	   		$final[] = [
	   			'acc_id' 		=> $value_['acc_id'],
	   			'acc_type' 		=> $value_['acc_type'],
	   			'pr_txn_list' 	=> $result,
	   		];
	   	}

	   	$result_ = $this->db->table($projasttxn_tbl)
    						->select('acc_id,acc_type')
	   						->where('voucher_txn_id', $voucher_txn_id)
	   						->where('project_id !=', 1)
	   						->groupBy('acc_id')
	   						->groupBy('acc_type')
	   						->get()->getResultArray();

	   	foreach ($result_ as $key_ => $value_) {

	   		$result = $this->db->table($projasttxn_tbl)
		   						->where('voucher_txn_id', $voucher_txn_id)
		   						->where('project_id !=', 1)
		   						->where('acc_id', $value_['acc_id'])
		   						->where('acc_type', $value_['acc_type'])
		   						->get()->getResultArray();

		   	foreach ($result as $key => $value) {
		   		$project_info = $this->get_project_info($value['project_id']);
		   		$result[$key]['project_name'] = $project_info['project_name'] ?? '';

		   		$narration_info = $this->get_voucher_narration_info($value['voucher_txn_id'],'short',$value['proj_txn_id']);
		   		$result[$key]['proj_txn_narr'] = $narration_info['vch_short_narr'] ?? '';
		   	}

	   		$final[] = [
	   			'acc_id' 		=> $value_['acc_id'],
	   			'acc_type' 		=> $value_['acc_type'],
	   			'pr_txn_list' 	=> $result,
	   		];
	   	}

	   	return $final;
    }

    function delete_pr_txn($voucher_txn_id)
    {
    	$projexptxn_tbl = $this->company_id.'_projexptxn_'.$this->session->get('ses_comp_fy_id');
    	$projrevtxn_tbl = $this->company_id.'_projrevtxn_'.$this->session->get('ses_comp_fy_id');
    	$prjliabtxn_tbl = $this->company_id.'_prjliabtxn_'.$this->session->get('ses_comp_fy_id');
    	$projasttxn_tbl = $this->company_id.'_projasttxn_'.$this->session->get('ses_comp_fy_id');

    	$this->db->table($projexptxn_tbl)
	   						->where('voucher_txn_id', $voucher_txn_id)
	   						->delete();

	   	$this->db->table($projrevtxn_tbl)
	   						->where('voucher_txn_id', $voucher_txn_id)
	   						->delete();

	   	$this->db->table($prjliabtxn_tbl)
	   						->where('voucher_txn_id', $voucher_txn_id)
	   						->delete();

	   	$this->db->table($projasttxn_tbl)
	   						->where('voucher_txn_id', $voucher_txn_id)
	   						->delete();
    }

    function get_project_info($project_id)
	{
		$projectmst_tbl = $this->company_id.'_projectmst_'.$this->session->get('ses_comp_fy_id');

		$data = $this->db->table($projectmst_tbl)
    								->select($projectmst_tbl.'.*')
    								->where('project_id',$project_id)
    	    						->get()->getRowArray();
    	return $data;
	}
    
	function add_acc_oth_data($data,$VchOthrBo=0){

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
		
		if($this->session->get('ses_boid')!='' && $VchOthrBo==0)
            $bo_id = $this->session->get('ses_boid');
		 else 
			  $bo_id =$VchOthrBo;
		$data['bo_id'] =$bo_id;
	 	$this->db->table($table)->insert($data);
	}
	function add_acc_crsref_data($data,$VchOthrBo=0){
	    
	    if($VchOthrBo==0)
		$bo_id = $this->session->get('ses_boid');
		else
		$bo_id = $VchOthrBo;
		 
		$data['bo_id'] =$bo_id;
	 	$table = $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
	 	$this->db->table($table)->insert($data);
	}
	
	function add_taxsummary_data($data){
		if(!isset($data['acc_hsn_sac']))
			$data['acc_hsn_sac']=0;
		$table = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
		$this->db->table($table)->insert($data);
	}
	function add_acctgstfcy_data($data){
		$table = $this->company_id.'_acctgstfcy_'.$this->session->get('ses_comp_fy_id');
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
		if($data['description'])
		$this->save_voucher_narration($data['voucher_txn_id'],$data['txn_id'],'short',$data['description']);		 
	
		 unset($data['description']);	 
		
		if($this->session->get('ses_boid')!='')
            $bo_id = $this->session->get('ses_boid');
		 else 
			  $bo_id =1;
		$data['bo_id'] =$bo_id;
	 	$this->db->table($table)->insert($data);
	}
	

	function txn_det($id){
		// $account_table_name = $this->company_id.'_accnttxnnn_'.$id.'_'.$this->session->get('ses_comp_fy_id'); 

		$account_table_name = $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id'); 

		return $account_table_name;
	}

	function add_acc_txn_data($data,$VchOthrBo=0)
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
		 
		 if($VchOthrBo==0)
			$bo_id =  $this->session->get('ses_boid');
			else
			$bo_id = $VchOthrBo;
		 
		 $data['bo_id']=$bo_id;
		 $this->db->table($account_table_name)->insert($data);
		 
		 if(isset($last_data['acc_txn_narr'])){
			   $acc_txn_narr = $last_data['acc_txn_narr'];		    
		      $this->save_voucher_narration($data['voucher_txn_id'],$data['txn_id'],'short',$acc_txn_narr);		 
		 } 
    }
    function save_sale_memeo_txns($data){
	     $memotxnnnn_table_name = $this->company_id.'_memotxnnnn_'.$this->session->get('ses_comp_fy_id');
		 $this->db->table($memotxnnnn_table_name)->insert($data);	
		
	}
	
	function add_taxinclusive_txn_data($data){
	     $vchaddinfo_table_name = $this->company_id.'_vchaddinfo_'.$this->session->get('ses_comp_fy_id');
		 $this->db->table($vchaddinfo_table_name)->insert($data);	
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
        // get item default valuation method id 
		$iteminfo     = $this->get_item_info($data['item_id']);
		$valmethod_id = ($iteminfo['valmethod_id'])?$iteminfo['valmethod_id']:1; 
		
	   	$voucher_info = $this->get_voucher_cons_info($data['voucher_txn_id']);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$rate = $this->get_forex_rate_inr($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

	   	$data['item_txn_amount'] = ($data['item_txn_amount'] * $rate);
        if(isset($data['description']))
			$description =$data['description'];
		 else 
			 $description ='';
	   	$this->save_voucher_narration($data['voucher_txn_id'],$data['txn_id'],'short',$description);
		 unset($data['description']);		
		 $data['bo_id'] =  $this->session->get('ses_boid');
         $itemtxnnnn_tbl =  $this->company_id.'_itemtxnnnn_'.$data['item_id'].'_'.$this->session->get('ses_comp_fy_id');
		 $this->db->table($itemtxnnnn_tbl)->insert($data);
		 $itemtxnid = $this->db->insertID(); 
		 
		 //clear reporting tables and valuation before calculation
		 $this->delete_valuation_tbls_txn_data($data['voucher_txn_id']);		 
		 $this->valuation_calculations($data);	
		// $this->update_item_unit_balance($data['item_id'], $data['item_unit'], $data['mat_cent_id'], $data['batch_id'], $data['item_avail'],$valmethod_id,$data['voucher_txn_id'],$itemtxnid);
		 				 
		return $itemtxnid; 	 
    }
    
	function valuation_calculations($data){

		$item_id        = $data['item_id'];
		$voucher_txn_id  = $data['voucher_txn_id'];
		$item_unit= $data['item_unit'];
		$mat_cent_id= $data['mat_cent_id'];
		$batch_id= $data['batch_id'];
		$item_avail= $data['item_avail'];
	    $bal = $this->get_item_op_bal($item_id,$item_unit,$mat_cent_id,$batch_id);
        $item_txn_tbl = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		$this->db->query('SET @bal = '.$bal.';');		
		$this->db->query('UPDATE '.$item_txn_tbl.' SET item_bal_qty = CASE WHEN item_txn_drcr = "c" THEN @bal:=@bal - item_txn_qty WHEN item_txn_drcr = "d" THEN @bal:=@bal + item_txn_qty ELSE 0 END where item_id = "'.$item_id.'" AND item_unit = "'.$item_unit.'" AND mat_cent_id = "'.$mat_cent_id.'" AND batch_id = "'.$batch_id.'"  AND bo_id = "'.$this->bo_id.'" order by item_txn_date,voucher_txn_id,item_txn_id;');
	 
	    // first check item default valuation method has valuation or not , if not then stop and show error
	   $itemrepbon_tbl  = $this->company_id.'_itemrepbon_'.$this->session->get('ses_comp_fy_id');	
	   $item_info       = $this->get_item_info($item_id);
		
	   $item_default_val_method = $item_info['valmethod_id'];
	   $itemtxnvaln_tbl  = $this->company_id.'_itemtxnval_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
  	   	  
	   $item_txn_tbl = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
	   $builder = $this->db->table($item_txn_tbl); 
	   $builder->where('item_id', $item_id);
	   $builder->where('voucher_txn_id', $voucher_txn_id);
	   $builder->where('bo_id', $this->bo_id);
	   $builder->orderBy('item_txn_date', 'asc');
	   $builder->orderBy('voucher_txn_id', 'asc');
	   $builder->orderBy('item_txn_id', 'asc');
	   $result = $builder->get()->getResultArray();
	   // echo $this->db->GetLastQuery();
	  
	   if($result){
		 foreach($result as $row){
			   $item_unit    = $row['item_unit'];
			   $mat_cent_id  = $row['mat_cent_id'];
			   $batch_id     = $row['batch_id'];
			   $item_bal_qty = $row['item_bal_qty'];
			   $item_txn_qty = $row['item_txn_qty'];
			   $item_txn_id = $row['item_txn_id'];
			   $item_avail  = $row['item_avail']; 
			   $item_txn_date  = $row['item_txn_date'];
			   $item_id_unit_id = $item_id.'_'.$item_unit;
			   
			 /*   $avg_data = $this->calculate_valuation_avg($item_id, $item_unit, $mat_cent_id, $batch_id,$bal,1,1,$voucher_txn_id,$item_txn_id);
			   $fifo_data = $this->calculate_valuation_fifo($item_id, $item_unit, $mat_cent_id, $batch_id,$bal,2,1,$voucher_txn_id,$item_txn_id);
			   $lifo_data=  $this->calculate_valuation_lifo($item_id, $item_unit, $mat_cent_id, $batch_id,$bal,3,1,$voucher_txn_id,$item_txn_id);
			  */
			   if($item_default_val_method==1)
				   $avg_data = $this->calculate_valuation_avg($item_id, $item_unit, $mat_cent_id, $batch_id,$bal,1,2,$voucher_txn_id,$item_txn_id);
			   
			  else if($item_default_val_method==2)
				   $fifo_data = $this->calculate_valuation_fifo($item_id, $item_unit, $mat_cent_id, $batch_id,$bal,2,2,$voucher_txn_id,$item_txn_id);
			   
			  else  if($item_default_val_method==3)	   
					$lifo_data=  $this->calculate_valuation_lifo($item_id, $item_unit, $mat_cent_id, $batch_id,$bal,3,2,$voucher_txn_id,$item_txn_id);
			 
 				   
			 
			 /*
			   $builder = $this->db->table($itemtxnvaln_tbl);
      		   $builder->select('item_value,valuation_id,item_txn_id');   
			   $builder->where('item_id', $item_id);
			   $builder->where('unit_id', $item_unit);			  
			   $builder->where('method_id', $item_default_val_method);
			   $builder->where('item_txn_id', $item_txn_id);
			   $builder->where('voucher_txn_id', $voucher_txn_id);
			   $builder->orderBy('voucher_date', 'asc');
			   $builder->orderBy('voucher_txn_id', 'asc');
			   $builder->orderBy('item_txn_id', 'asc');
			   $builder->limit(1);
			   $df_result = $builder->get()->getRowArray();
			   if($df_result){
				$buffer_amount =$df_result['item_value']; 
                $item_txn_id = $df_result['item_txn_id'];
				$valuation_id = $df_result['valuation_id'];
				
				
				
				$rpt_data     = array("item_id_unit_id"=>$item_id_unit_id,
			                  "voucher_txn_id"=>$voucher_txn_id,"method_id"=>0,
							  "item_value"=>$buffer_amount,"item_txn_id"=>$item_txn_id, 
							  "item_bal_qty"=>$item_txn_qty,"item_avail"=>$item_avail,
							  "item_unit"=>$item_unit,"item_txn_date"=>$item_txn_date,
							  "valuation_id"=>$valuation_id,
							  "bo_id"=>$this->bo_id);
				 $ifexists = $this->db->table($itemrepbon_tbl)->where('method_id',0)->where("item_id_unit_id",$item_id_unit_id)->where('item_txn_id',$item_txn_id)->countAllResults();
				if($ifexists)
				$this->db->table($itemrepbon_tbl)->where('method_id',0)->where("item_id_unit_id",$item_id_unit_id)->where('item_txn_id',$item_txn_id)->update(array("item_value"=>$buffer_amount));
			    else
				$this->db->table($itemrepbon_tbl)->insert($rpt_data);   
			
			    
				   
			   }*/ 
			 
		    }  
	    }		
	 
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

        $batch_id = $this->db->insertID();

		$mst_base_id = $this->create_mst_base_id($batch_id,'itmbatchmt');
		$this->db->table($itmbatchmt_tbl)
				->where('batch_id',$batch_id)
				->update(['mst_base_id' => $mst_base_id]);

	    return $batch_id;
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
	  // get item default valuation method id 
		$iteminfo     = $this->get_item_info($data['item_id']);
		$valmethod_id = ($iteminfo['valmethod_id'])?$iteminfo['valmethod_id']:1;
		
      $itemtxnnnn_tbl = $this->company_id.'_itemtxnnnn_'.$data['item_id'].'_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!='')
			$bo_id =  $this->session->get('ses_boid');
		  else 
			$bo_id =1;
		$data['bo_id']=$bo_id;
		unset($data['description']);
        $this->db->table($itemtxnnnn_tbl)->insert($data);
        $itemtxnid = $this->db->insertID(); 

		$this->update_item_unit_balance($data['item_id'], $data['item_unit'], $data['mat_cent_id'], $data['batch_id'], $data['item_avail'],$valmethod_id,$data['voucher_txn_id'],$itemtxnid);
        
    }


    //---------------------------------------------------ADD METHODS END
 
  public function add_batch_value($data){
	 $batchval_tbl = $this->company_id.'_itmbatchmt_'.$this->session->get('ses_comp_fy_id');    
	 $this->db->table($batchval_tbl)->insert($data);

	 $batch_id = $this->db->insertID();

		$mst_base_id = $this->create_mst_base_id($batch_id,'itmbatchmt');
		$this->db->table($batchval_tbl)
				->where('batch_id',$batch_id)
				->update(['mst_base_id' => $mst_base_id]);

	    return $batch_id;

     	 
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
            $item_unit_name = $item_unit_info['item_unit'];		 
        else
            $item_unit_name ='' ;
			
		$AvailQty =$PackQty=$ObseQty=$IntrsQty=0;

        $itm_txn_tbl = $this->company_id.'_itemtxnnnn_'.$itemid.'_'.$this->session->get('ses_comp_fy_id');

        $builder = $this->db->table($itm_txn_tbl);
        $builder->select('item_bal_qty');
        $builder->where('item_id', $itemid);
        $builder->where('item_unit', $unit_id);
        $builder->where('batch_id', 0);
        $builder->where('item_avail', 1);
        if($this->session->get('ses_boid')!='')
        	$builder->where('bo_id', $this->session->get('ses_boid'));
        $builder->where('item_txn_date <=',date('Y-m-d',strtotime($voucher_date)));
        $builder->orderBy('item_txn_date', 'desc');
        $builder->orderBy('voucher_txn_id', 'desc');
        $builder->orderBy('item_txn_id', 'desc');
        $builder->limit(1);
        $result = $builder->get()->getRowArray();
      						
     if($result){
     	$AvailQty = floatval($result['item_bal_qty']);
     }
     else{
     	$result =  $this->item_opn_balance_info($itemid,$unit_id);
     	if($result){
     		$AvailQty = floatval($result['item_bal_qty']);
     	}
     }

     	$builder = $this->db->table($itm_txn_tbl);
        $builder->select('item_bal_qty');
        $builder->where('item_id', $itemid);
        $builder->where('item_unit', $unit_id);
        $builder->where('batch_id', 0);
        $builder->where('item_avail', 2);
        if($this->session->get('ses_boid')!='')
        	$builder->where('bo_id', $this->session->get('ses_boid'));
        $builder->where('item_txn_date <=',date('Y-m-d',strtotime($voucher_date)));
        $builder->orderBy('item_txn_date', 'desc');
        $builder->orderBy('voucher_txn_id', 'desc');
        $builder->orderBy('item_txn_id', 'desc');
        $builder->limit(1);
        $result = $builder->get()->getRowArray();
      						
     if($result){
     	$PackQty = floatval($result['item_bal_qty']);
     }

     $builder = $this->db->table($itm_txn_tbl);
        $builder->select('item_bal_qty');
        $builder->where('item_id', $itemid);
        $builder->where('item_unit', $unit_id);
        $builder->where('batch_id', 0);
        $builder->where('item_avail', 0);
        if($this->session->get('ses_boid')!='')
        	$builder->where('bo_id', $this->session->get('ses_boid'));
        $builder->where('item_txn_date <=',date('Y-m-d',strtotime($voucher_date)));
        $builder->orderBy('item_txn_date', 'desc');
        $builder->orderBy('voucher_txn_id', 'desc');
        $builder->orderBy('item_txn_id', 'desc');
        $builder->limit(1);
        $result = $builder->get()->getRowArray();
      						
     if($result){
     	$ObseQty = floatval($result['item_bal_qty']);
     }

     $builder = $this->db->table($itm_txn_tbl);
        $builder->select('item_bal_qty');
        $builder->where('item_id', $itemid);
        $builder->where('item_unit', $unit_id);
        $builder->where('batch_id', 0);
        $builder->where('item_avail', 3);
        if($this->session->get('ses_boid')!='')
        	$builder->where('bo_id', $this->session->get('ses_boid'));
        $builder->where('item_txn_date <=',date('Y-m-d',strtotime($voucher_date)));
        $builder->orderBy('item_txn_date', 'desc');
        $builder->orderBy('voucher_txn_id', 'desc');
        $builder->orderBy('item_txn_id', 'desc');
        $builder->limit(1);
        $result = $builder->get()->getRowArray();
      						
     if($result){
     	$IntrsQty = floatval($result['item_bal_qty']);
     }
	
	   
	 
	 
     $json_array[$itemid]=array("unit_name"=>$item_unit_name,"AvailQty"=>$AvailQty,"PackQty"=>$PackQty,
		                                     "ObseQty"=>$ObseQty,"IntrsQty"=>$IntrsQty);	 	   
	
      
		return $json_array;
	 }
  else
    return "0";	   
 } 


 	 public function get_item_last_balance($item_id, $unit_id, $mc_id, $date, $voucher_txn_id = 0){
		$itemvalmst_info   = $this->itemvalmst_info($item_id);
		if($itemvalmst_info)
		   $item_mrp = parseAmount($itemvalmst_info['item_mrp']);
		else 
		  $item_mrp =0;			   
        $item_unit_info = $this->item_unit_info($unit_id);
		
		$json_array = array();
		
		if($item_unit_info)
            $unit_name = $item_unit_info['item_unit'];		 
        else
            $unit_name ='' ;
			
		$AvailQty = $PackQty = $ObseQty = $IntrsQty = $item_last_price = 0;

        $itm_txn_tbl = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');

        $builder = $this->db->table($itm_txn_tbl);
        $builder->select('item_bal_qty,item_txn_amount,item_txn_qty');
        $builder->where('item_id', $item_id);
        $builder->where('item_unit', $unit_id);
        $builder->where('mat_cent_id', $mc_id);
        $builder->where('batch_id', 0);
        $builder->where('item_avail', 1);
        if($this->session->get('ses_boid')!='')
        	$builder->where('bo_id', $this->session->get('ses_boid'));
        $builder->where('item_txn_date <=',date('Y-m-d',strtotime($date)));
        $builder->where('voucher_txn_id !=', $voucher_txn_id);
        $builder->orderBy('item_txn_date', 'desc');
        $builder->orderBy('voucher_txn_id', 'desc');
        $builder->orderBy('item_txn_id', 'desc');
        $builder->limit(1);
        $result = $builder->get()->getRowArray();
      						
	     if($result){
			 if($result['item_txn_qty']=='' || $result['item_txn_qty']==0)
				 $result['item_txn_qty']=1;
			$item_last_price =  parseAmount($result['item_txn_amount']/$result['item_txn_qty']);
	     	$AvailQty = floatval($result['item_bal_qty']);
	     }
	     else{
	     	$result_ =  $this->item_opn_balance_info($item_id,$unit_id,$mc_id);
	     	if($result_){
	     		$AvailQty = floatval($result_['op_bal_qty']);
	     	}
	     }

     	$builder = $this->db->table($itm_txn_tbl);
        $builder->select('item_bal_qty');
        $builder->where('item_id', $item_id);
        $builder->where('item_unit', $unit_id);
        $builder->where('mat_cent_id', $mc_id);
        $builder->where('batch_id', 0);
        $builder->where('item_avail', 2);
        if($this->session->get('ses_boid')!='')
        	$builder->where('bo_id', $this->session->get('ses_boid'));
        $builder->where('item_txn_date <=',date('Y-m-d',strtotime($date)));
        $builder->where('voucher_txn_id !=', $voucher_txn_id);
        $builder->orderBy('item_txn_date', 'desc');
        $builder->orderBy('voucher_txn_id', 'desc');
        $builder->orderBy('item_txn_id', 'desc');
        $builder->limit(1);
        $result = $builder->get()->getRowArray();
      						
		if($result){
			$PackQty = floatval($result['item_bal_qty']);
		}

     	$builder = $this->db->table($itm_txn_tbl);
        $builder->select('item_bal_qty');
        $builder->where('item_id', $item_id);
        $builder->where('item_unit', $unit_id);
        $builder->where('mat_cent_id', $mc_id);
        $builder->where('batch_id', 0);
        $builder->where('item_avail', 0);
        if($this->session->get('ses_boid')!='')
        	$builder->where('bo_id', $this->session->get('ses_boid'));
        $builder->where('item_txn_date <=',date('Y-m-d',strtotime($date)));
        $builder->where('voucher_txn_id !=', $voucher_txn_id);
        $builder->orderBy('item_txn_date', 'desc');
        $builder->orderBy('voucher_txn_id', 'desc');
        $builder->orderBy('item_txn_id', 'desc');
        $builder->limit(1);
        $result = $builder->get()->getRowArray();
      						
		if($result){
			$ObseQty = floatval($result['item_bal_qty']);
		}

		$builder = $this->db->table($itm_txn_tbl);
		$builder->select('item_bal_qty');
		$builder->where('item_id', $item_id);
		$builder->where('item_unit', $unit_id);
		$builder->where('mat_cent_id', $mc_id);
		$builder->where('batch_id', 0);
		$builder->where('item_avail', 3);
		if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
		$builder->where('item_txn_date <=',date('Y-m-d',strtotime($date)));
		$builder->where('voucher_txn_id !=', $voucher_txn_id);
		$builder->orderBy('item_txn_date', 'desc');
		$builder->orderBy('voucher_txn_id', 'desc');
		$builder->orderBy('item_txn_id', 'desc');
		$builder->limit(1);
		$result = $builder->get()->getRowArray();
      						
     if($result){
     	$IntrsQty = floatval($result['item_bal_qty']);
     }
	
	   
	return [
		"unit_name"		=> $unit_name,
		"AvailQty"		=> $AvailQty,
		"PackQty"		=> $PackQty,
		"ObseQty"		=> $ObseQty,
		"IntrsQty"		=> $IntrsQty,
		"LastPrice"     => $item_last_price,
		"ItemMrp"       => $item_mrp
	];	 	   
	
      
	}
 
 
  public function item_opn_balance_info($item_id,$unit_id,$mc_id){      
	$itemoppybal_tbl = $this->company_id.'_itmoppybal_'.$this->session->get('ses_comp_fy_id');
	$builder = $this->db->table($itemoppybal_tbl);
	if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
	$builder->where('item_id', $item_id);
	$builder->where('item_unit', $unit_id);
	$builder->where('mat_cent_id', $mc_id);
	$builder->where('batch_id', 0);
	$result = $builder->get()->getRowArray();

	return $result; 
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

  function acc_all_bo_op_balances($account_id){
		$accoppybal_tbl = $this->company_id.'_accoppybal_'.$this->session->get('ses_comp_fy_id');  
		$tbl_name       = $this->company_id.'_hobomaster_'.$this->session->get('ses_comp_fy_id');
		$data           = $this->db->table($tbl_name)
			->select('bo_id,bo_name,bo_ho')
			->get()->getResultArray();
	 $final_array=array();		
		if($data){
		foreach($data as $row){
		   $bo_id = $row['bo_id'];
		   $acc_balance_info = $this->db->table($accoppybal_tbl)->where('bo_id', $bo_id)->where('acc_id', $account_id)->get()->getRowArray();
		   if($acc_balance_info){
			 $acc_op_bal= $acc_balance_info['acc_op_bal'];
			 $acc_py_bal= $acc_balance_info['acc_py_bal'];
		   if($acc_op_bal)	 
		   $row['open_balance'] = parseAmount(abs($acc_op_bal));
	       else 
			$row['open_balance'] = 0;

		  if($acc_py_bal)
		   $row['py_balance']   = parseAmount(abs($acc_py_bal));
	       else
           $row['py_balance']   = 0;
		
			if($acc_op_bal)
			   $row['acopen_balance'] = parseAmount($acc_op_bal);
		   else
			   $row['acopen_balance'] =0;
		   if($acc_py_bal)
			   $row['acpy_balance']   = parseAmount($acc_py_bal);
		   else
			   $row['acpy_balance']   =0;	
		   }
		   else{
			$acc_op_bal =   $acc_py_bal=0; 
			$row['open_balance'] = $row['py_balance']=$row['acopen_balance'] = $row['acpy_balance']=0;   
		   }		   
		   $row['open_bal_drcr'] = ($acc_op_bal<0)?'cr':'dr';
		   $row['py_bal_drcr']   = ( $acc_py_bal<0)?'cr':'dr';
		   $final_array[]=$row;
		  }				
		}
	  return $final_array;	
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

    function party_dropdown($array = [23,22,16,21]){ // group ids

    	$final_array = $this->get_sub_group_ids($array);
    	$bbb_groups = $this->get_bbb_groups();

		$comp_id = $this->company_id;
		$acctgstmst_tbl = $this->company_id.'_acctgstmst_'.$this->session->get('ses_comp_fy_id'); 	
				
		$comp_party_tbl = $comp_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
		$data =  $this->db->table($comp_party_tbl)
						  ->select('acc_id, acc_name, acc_grp_id')
						  ->whereIn('acc_grp_id', $final_array)
						  ->orderBy('acc_name','ASC')
						  ->get()->getResultArray();
		if($data){
			foreach($data as $key => $value){
                $state_code       = $this->get_acc_state_code($value['acc_id']);   
				$party_gstin_data = $this->db->table($acctgstmst_tbl)->where('acc_id',$value['acc_id'])->get()->getRowArray(); 
				if($party_gstin_data){
				 $data[$key]['gstin']     = $party_gstin_data['acc_gstin'];
				 $data[$key]['dealer_type']     = $party_gstin_data['acc_dealer_type'];
				 
				}
			     else{ 
				 $data[$key]['gstin']     = "";	 
				 $data[$key]['dealer_type']     =0;
				 }
			
				$data[$key]['is_bbb']     = 0;
				$data[$key]['grpid']     = $value['acc_grp_id'];
				$data[$key]['state_code'] = sprintf('%02d',$state_code);
		       	if(in_array($value['acc_grp_id'], $bbb_groups)){
		           	$data[$key]['is_bbb'] = 1;
		       	}
			   
			}
		}
		return $data;	
	}
	
	function ajax_receipt_accounts_list($array = [23,59]){ // group ids

    	$final_array = $this->get_sub_group_ids($array);
    	
		$comp_id = $this->company_id;
				
		$comp_party_tbl = $comp_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
		$data =  $this->db->table($comp_party_tbl)
						  ->select('acc_id, acc_name, acc_grp_id')
						  ->whereIn('acc_grp_id', $final_array)
						  ->orderBy('acc_name','ASC')
						  ->get()->getResultArray();
		$final = array();				  
		if($data){
			foreach($data as $key => $value){
               $final[]= array("acc_id"=>$value['acc_id'],"account_name"=>$value['acc_name']);
			}
		}
		
		return $final;	
	}
	
	function get_acc_state_code($account_id){
		$acctaddmst_tbl = $this->company_id.'_acctaddmst_'.$this->session->get('ses_comp_fy_id');
		$res_row =  $this->db->table($acctaddmst_tbl)
					  ->select('acc_id,acc_state,acc_city,acc_country')
					  ->where('acc_id', $account_id)
					  ->where('comp_id', $this->company_id)
					  ->orderBy('acc_id','ASC')
					  ->get()->getRowArray();	
		if($res_row){
		   $response = $this->aicountly_db->table('aicountly_stateslist_univdb')->select('state_id, country_id, state_code')->where('state_id',$res_row['acc_state'])->where('country_id',$res_row['acc_country'])->get()->getRowArray();
		   if($response)
            return $response['state_code'];		
            else
			return "04";		   
		  }
	  	  else
		   return "04";
					  
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

 		if($bbb_groups > 0){
 			$account = $this->get_account_info($account_id);
	 		if(in_array($account['acc_grp_id'], $bbb_groups))
	 			return true;
 		}
 		
 		return false;

 	}
 	function check_cc_account($account_id)
 	{
 		$cc_groups = $this->get_cc_groups();

 		if(count($cc_groups) > 0){
 			$account = $this->get_account_info($account_id);
	 		if(in_array($account['acc_grp_id'], $cc_groups) || in_array($account['acc_grp_parent_id'], [7,11,13]))
	 			return true;
 		}
 		return false;
 	}

 	function check_cc_bill_sundry($billsundry_id)
 	{
 		$cc_groups = $this->get_cc_groups();

 		if(count($cc_groups) > 0){
 			$billsundry = $this->get_billsundry_info($billsundry_id);
	 		if(in_array($billsundry['acc_grp_id'], $cc_groups) || in_array($billsundry['acc_grp_parent_id'], [7,11,13]))
	 			return true;
 		}
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
				$final_result[$row['mat_cent_id']] =$row['mat_cent_name'];			   
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
                	$item_unit_name = $item_unit_info['item_unit'];				 
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
       $data = $this->db->table($cc_mst_tbl)
		       ->select('cc_id as id, cc_name as label, cc_name as value')
		       ->where('cc_name !=', 'UNDEFINED')
		       ->get()->getResultArray();
        
        return $data;
    }

    function get_pr()
    {
        $projectmst_tbl = $this->company_id.'_projectmst_'.$this->session->get('ses_comp_fy_id');
		
		$data = $this->db->table($projectmst_tbl)
					->select('project_id as id, project_name as label, project_name as value')
					->where('project_id >', 1)
					->get()->getResultArray();

        
        return $data;
    }

    function get_cc_op_bal($cc_id)
    {
    	$bal = 0;
    	$costctoppy_tbl = $this->company_id.'_costctoppy_'.$this->session->get('ses_comp_fy_id');
    	$data = $this->db->table($costctoppy_tbl)
      				->where('cc_id', $cc_id)
      				->where('bo_id', $this->bo_id)
      				->get()->getRowArray();
		if($data){
			$bal = floatval($data['cc_op_bal']);
		}
		else{
			$op_data = [
				'cc_id'			=> $cc_id,
				'bo_id'			=> $this->bo_id,
				'cc_op_bal'		=> 0,
				'cc_py_bal'		=> 0,
			];
			$this->db->table($costctoppy_tbl)->insert($op_data);
		}
		return $bal;
    }
    

    function update_cc_txn_balance($cc_id,$bo_id=0)
    {
		if($bo_id==0 || $bo_id=='')
			$bo_id = $this->bo_id;
        $bal = $this->get_cc_op_bal($cc_id);
        
        $cc_txn_tbl = $this->company_id.'_costcttxnn_'.$this->session->get('ses_comp_fy_id');
		$this->db->query('SET @bal = '.$bal.';');
			
		$this->db->query('UPDATE '.$cc_txn_tbl.' SET cc_txn_bal = CASE WHEN cc_txn_drcr = "C" THEN @bal:=@bal - cc_txn_amt WHEN cc_txn_drcr = "D" THEN @bal:=@bal + cc_txn_amt ELSE 0 END where bo_id = '.$bo_id.' AND cc_id = '.$cc_id.' order by cc_txn_date,voucher_txn_id,cc_txn_id;');  
    }

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

    // delete it
    public function get_cc_txn_data_old($voucher_txn_id)
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

    public function get_cc_txn_data($voucher_txn_id)
    {
    	$fcy_rate = 1;
    	$final = [];

	   	$voucher_info = $this->get_voucher_cons_info($voucher_txn_id);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$fcy_rate = $this->get_forex_rate_fcy($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}

        $cc_txn_tbl = $this->company_id.'_costcttxnn_'.$this->session->get('ses_comp_fy_id');
        $cc_mst_tbl = $this->company_id.'_costctmstr_'.$this->session->get('ses_comp_fy_id');
        $acc_mst_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
        
        $final = [];
        $result_ = $this->db->table($cc_txn_tbl)
				        ->select('acc_id, acc_type')
						->where('voucher_txn_id', $voucher_txn_id)
						->where('cc_id !=', 1)
						->where('bo_id', $this->bo_id)
						->groupBy('acc_id')
						->groupBy('acc_type')
						->orderBy('acc_id')
						->orderBy('acc_type')
						->get()->getResultArray();
        
        foreach($result_ as $key_ => $value_)
        {
            $result = $this->db->table($cc_txn_tbl)
            				  ->where('cc_id !=', 1)
            				  ->where('voucher_txn_id', $voucher_txn_id)
            				  ->where('bo_id', $this->bo_id)
            				  ->where('acc_id', $value_['acc_id'])
            				  ->where('acc_type', $value_['acc_type'])
            				  ->get()->getResultArray();

            if($result){
            	foreach ($result as $key => $value) {

            		$result[$key]['cc_txn_amt'] = parseAmount($value['cc_txn_amt'] * $fcy_rate);

            		$cc_mst = $this->db->table($cc_mst_tbl)
            				  ->where('cc_id', $value['cc_id'])
            				  ->get()->getRowArray();

            		$result[$key]['cc_name'] = $cc_mst['cc_name'] ?? '';

            		$narration_info = $this->get_voucher_narration_info($value['voucher_txn_id'],'short',$value['cc_txn_id']);
		   			$result[$key]['cc_txn_narr'] = $narration_info['vch_short_narr'] ?? '';
            	}
            }

            $final[] = [
                'acc_id' => $value_['acc_id'],
                'acc_type' => $value_['acc_type'],
                'cc_txn_list' => $result
            ]; 
        }

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

	function get_first_voucher_series($voucher_type_id){
		$comp_vch_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
		$data =  $this->db->table($comp_vch_series_tbl)
							->where('voucher_type_id', $voucher_type_id)
							->where('comp_id', $this->company_id)
							->get()->getRowArray();

		return $data['comp_vch_series_id'];	
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
					$item_unit_name = $item_unit_info['item_unit'];
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
				$item_name      = $row['item_unit'];
				$final_result[] =array("label"=>$item_name,"value"=>$item_name,"id"=>$row['unit_id']);			   
			}
		}
		return $final_result;	
    }
   function relevant_parent_dropdown($acc_grp_parent){
	    $final_result   = array();
		$acc_grp_parent = strtolower(html_entity_decode($acc_grp_parent));
		$grpparentn_tbl = $this->company_id.'_grpparentn_'.$this->session->get('ses_comp_fy_id');
	    $exists = $this->db->table($grpparentn_tbl)
		               ->where('LOWER(acc_grp_parent)',$acc_grp_parent)
					   ->where('comp_id',$this->company_id)
					   ->countAllResults(); 
    
		if($exists==0 && $acc_grp_parent!=''){		
		$builder = $this->db->table($grpparentn_tbl); 
		$builder->select('acc_grp_parent_id as id, acc_grp_parent as label, acc_grp_parent as value');
        $builder->where('comp_id', $this->company_id);
        $builder->like('LOWER(acc_grp_parent)', $acc_grp_parent);
		$result = $builder->get()->getResultArray();
		if($result){
			foreach($result as $row){
				$acc_name    = ucwords($row['label']);
				$acc_id      = $row['id'];
				$final_result[] =array("label"=>$acc_name,"value"=>$acc_id,"id"=>$acc_id);			   
			  }
		   }		 
		}
		
		if($acc_grp_parent==''){
		 $builder = $this->db->table($grpparentn_tbl); 
		 $builder->select('acc_grp_parent_id as id, acc_grp_parent as label, acc_grp_parent as value');
         $builder->where('comp_id', $this->company_id);
         $result = $builder->get()->getResultArray();
		 if($result){
			foreach($result as $row){
				$acc_name    = ucwords($row['label']);
				$acc_id      = $row['id'];
				$final_result[] =array("label"=>$acc_name,"value"=>$acc_id,"id"=>$acc_id);			   
			  }
		   }	
		}
		
		 return $final_result; 
    }
	
	function relevant_undergroup_dropdown($acc_grp_name){
	    $final_result = array();
		$acc_grp_name = strtolower(html_entity_decode($acc_grp_name));
		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
	    $exists =$this->db->table($acctgroupn_tbl)
		            ->where('LOWER(acc_grp_name)',$acc_grp_name)
					->where('comp_id',$this->company_id)
					->where('acc_grp_parent_id !=',14)
					->where('acc_grp_parent_id !=',19)
					->countAllResults(); 					
		if($exists==0 && $acc_grp_name!=''){		
		$builder = $this->db->table($acctgroupn_tbl); 
		$builder->select('acc_grp_id as id, acc_grp_name as label, acc_grp_name as value');
        $builder->where('comp_id', $this->company_id);        
		$builder->where('acc_grp_parent_id !=',14);
		$builder->where('acc_grp_parent_id !=',19);
		$result = $builder->get()->getResultArray();

		if($result){
			foreach($result as $row){
				$acc_name    = ucwords($row['label']);
				$acc_id      = $row['id'];
				$final_result[] =array("label"=>$acc_name,"value"=>$acc_id,"id"=>$acc_id);			   
			  }
		   }
		 
		}
		
		if($acc_grp_name==''){
		 $builder = $this->db->table($acctgroupn_tbl); 
		 $builder->select('acc_grp_id as id, acc_grp_name as label, acc_grp_name as value');
         $builder->where('comp_id', $this->company_id);
		 $builder->where('acc_grp_parent_id !=',14);
         $result = $builder->get()->getResultArray();

		if($result){
			foreach($result as $row){
				$acc_name    = ucwords($row['label']);
				$acc_id      = $row['id'];
				$final_result[] =array("label"=>$acc_name,"value"=>$acc_id,"id"=>$acc_id);			   
			  }
		   }	
		}
		 return $final_result; 
    }


function relevant_item_undergroup_dropdown($acc_grp_name){
	    $final_result = array();
		$acc_grp_name = strtolower(html_entity_decode($acc_grp_name));
		$acctgroupn_tbl = $this->company_id.'_itemgrpmst_'.$this->session->get('ses_comp_fy_id');
	    $exists =$this->db->table($acctgroupn_tbl)
		            ->where('LOWER(item_grp_name)',$acc_grp_name)
					->where('comp_id',$this->company_id)					
					->countAllResults(); 					
		if($exists==0 && $acc_grp_name!=''){		
		$builder = $this->db->table($acctgroupn_tbl); 
		$builder->select('item_grp_id as id, item_grp_name as label, item_grp_name as value');
        $builder->where('comp_id', $this->company_id);        		
		$result = $builder->get()->getResultArray();
		if($result){
			foreach($result as $row){
				$acc_name    = ucwords($row['label']);
				$acc_id      = $row['id'];
				$final_result[] =array("label"=>$acc_name,"value"=>$acc_id,"id"=>$acc_id);			   
			  }
		   }
		 
		}
		
		if($acc_grp_name==''){
		 $builder = $this->db->table($acctgroupn_tbl); 
		 $builder->select('item_grp_id as id, item_grp_name as label, item_grp_name as value');
         $builder->where('comp_id', $this->company_id);		
         $result = $builder->get()->getResultArray();
		if($result){
			foreach($result as $row){
				$acc_name    = ucwords($row['label']);
				$acc_id      = $row['id'];
				$final_result[] =array("label"=>$acc_name,"value"=>$acc_id,"id"=>$acc_id);			   
			  }
		   }	
		}
		 return $final_result; 
    }

   function relevant_catg_dropdown($item_cat){
	    $final_result   = array();
		$item_cat       = strtolower(html_entity_decode($item_cat));
		$itemcatmst_tbl = $this->company_id.'_itemcatmst_'.$this->session->get('ses_comp_fy_id');
	    $exists = $this->db->table($itemcatmst_tbl)
		               ->where('LOWER(item_cat)',$item_cat)
					   ->where('comp_id',$this->company_id)
					   ->countAllResults(); 
    
		if($exists==0 && $item_cat!=''){		
		$builder = $this->db->table($itemcatmst_tbl); 
		$builder->select('icatgms_id as id, item_cat as label, item_cat as value');
        $builder->where('comp_id', $this->company_id);
        $builder->like('LOWER(item_cat)', $item_cat);
		$result = $builder->get()->getResultArray();		
		if($result){
			foreach($result as $row){
				$acc_name    = ucwords($row['label']);
				$acc_id      = $row['id'];
				$final_result[] =array("label"=>$acc_name,"value"=>$acc_id,"id"=>$acc_id);			   
			  }
		   }		 
		}
		
		if($item_cat==''){
		 $builder = $this->db->table($itemcatmst_tbl); 
		 $builder->select('icatgms_id as id, item_cat as label, item_cat as value');
         $builder->where('comp_id', $this->company_id);
         $result = $builder->get()->getResultArray();
		 if($result){
			foreach($result as $row){
				$acc_name    = ucwords($row['label']);
				$acc_id      = $row['id'];
				$final_result[] =array("label"=>$acc_name,"value"=>$acc_id,"id"=>$acc_id);			   
			  }
		   }	
		}
		
		 return $final_result; 
    }
	
   function getgroups_by_parent($id)
	 {
	 	$array = [];
	 	$tbl_name = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$data = $this->db->table($tbl_name)
    					->select('acc_grp_id')
    					->where('acc_grp_parent_id', $id)
    					->get()->getResultArray();

    	if($data){
    		foreach($data as $key => $value) {
    			array_push($array, $value['acc_grp_id']);
    		}
    	}
    	return $array;
	 }	
   function relevant_saleacc_dropdown($acc_name){
	    $final_result   = array();
		$acc_name       = strtolower(html_entity_decode($acc_name));
		$array = $this->getgroups_by_parent(8);
	    $acc_sales_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	    $exists = $this->db->table($acc_sales_tbl)
		               ->where('LOWER(acc_name)',$acc_name)
		     	       ->countAllResults(); 
		if($array){
	      $data =  $this->db->table($acc_sales_tbl)->select('acc_id as id, acc_name as label, acc_name as value')->where('LOWER(acc_name)',$acc_name)->whereIn('acc_grp_id', $array)->orWhere('acc_grp_parent_id', 8)->orderBy('acc_name','ASC')->get()->getResultArray();	       
	    }
	    else{
	    $data =  $this->db->table($acc_sales_tbl)->select('acc_id as id, acc_name as label, acc_name as value')->where('LOWER(acc_name)',$acc_name)->orWhere('acc_grp_parent_id', 8)->orderBy('acc_name','ASC')->get()->getResultArray();	       
	    }	   		
		if($exists==0 && $data!=''){
			foreach($data as $row){
				$acc_name    = ucwords($row['label']);
				$acc_id      = $row['id'];
				$final_result[] =array("label"=>$acc_name,"value"=>$acc_id,"id"=>$acc_id);			   
			  }
		   }		 
		
		
		if($acc_name==''){
		 if($array){
	      $data =  $this->db->table($acc_sales_tbl)->select('acc_id as id, acc_name as label, acc_name as value')->whereIn('acc_grp_id', $array)->orWhere('acc_grp_parent_id', 8)->orderBy('acc_name','ASC')->get()->getResultArray();	       
	     }
	    else{
	     $data =  $this->db->table($acc_sales_tbl)->select('acc_id as id, acc_name as label, acc_name as value')->orWhere('acc_grp_parent_id', 8)->orderBy('acc_name','ASC')->get()->getResultArray();	       
	    }
		 if($data){
			foreach($data as $row){
				$acc_name    = ucwords($row['label']);
				$acc_id      = $row['id'];
				$final_result[] =array("label"=>$acc_name,"value"=>$acc_id,"id"=>$acc_id);			   
			  }
		   }	
		}
		
		 return $final_result; 
    }
	function relevant_purchaseacc_dropdown($acc_name){
	    $final_result   = array();
		$acc_name       = strtolower(html_entity_decode($acc_name));
		$array = $this->getgroups_by_parent(7);
	    $acc_sales_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	    $exists = $this->db->table($acc_sales_tbl)
		               ->where('LOWER(acc_name)',$acc_name)
		     	       ->countAllResults(); 
		
		if($array){
	     $data =  $this->db->table($acc_sales_tbl)->select('acc_id as id, acc_name as label, acc_name as value')->where('LOWER(acc_name)',$acc_name)->whereIn('acc_grp_id', $array)->orWhere('acc_grp_parent_id', 7)->orderBy('acc_name','ASC')->get()->getResultArray();	       
	    }
	    else{
	    $data =  $this->db->table($acc_sales_tbl)->select('acc_id as id, acc_name as label, acc_name as value')->where('LOWER(acc_name)',$acc_name)->orWhere('acc_grp_parent_id', 7)->orderBy('acc_name','ASC')->get()->getResultArray();	       
	    }
	   		
		if($exists==0 && $data!=''){
			foreach($data as $row){
				$acc_name    = ucwords($row['label']);
				$acc_id      = $row['id'];
				$final_result[] =array("label"=>$acc_name,"value"=>$acc_id,"id"=>$acc_id);			   
			  }
		   }		 
		
		
		if($acc_name==''){
		 if($array){
	      $data =  $this->db->table($acc_sales_tbl)->select('acc_id as id, acc_name as label, acc_name as value')->whereIn('acc_grp_id', $array)->orWhere('acc_grp_parent_id', 8)->orderBy('acc_name','ASC')->get()->getResultArray();	       
	     }
	    else{
	     $data =  $this->db->table($acc_sales_tbl)->select('acc_id as id, acc_name as label, acc_name as value')->orWhere('acc_grp_parent_id', 8)->orderBy('acc_name','ASC')->get()->getResultArray();	       
	    }
		 if($data){
			foreach($data as $row){
				$acc_name    = ucwords($row['label']);
				$acc_id      = $row['id'];
				$final_result[] =array("label"=>$acc_name,"value"=>$acc_id,"id"=>$acc_id);			   
			  }
		   }	
		}
		
		 return $final_result; 
    }
	
	function relevant_vchseries_dropdown($series_name){
	    $final_result   = array();$final_resultn   = array();
		$series_name       = strtolower(html_entity_decode($series_name));
		$comp_vch_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
        
		$exists = $this->db->table($comp_vch_series_tbl)
		               ->where('LOWER(comp_vch_series)',$series_name)
					   ->countAllResults(); 
		
		$data =  $this->db->table($comp_vch_series_tbl)->select('comp_vch_series_id as id, comp_vch_series as label, comp_vch_series as value')
							->where('comp_id', $this->company_id)
							->where('LOWER(comp_vch_series)',$series_name)
							->orderBy('comp_vch_series','ASC')
							->get()->getResultArray();
	   		
		if($exists==0 && $data!=''){
			foreach($data as $row){
				$acc_name    = ucwords($row['label']);
				$final_result[$acc_name] =array("label"=>$acc_name,"value"=>$acc_name,"id"=>$acc_name);			   
			  }
		   }		 
		if($final_result){
			foreach($final_result as $row){
				$acc_name    = ucwords($row['label']);
				$final_resultn[] =array("label"=>$acc_name,"value"=>$acc_name,"id"=>$acc_name);			   
			  }
		   }	
		
		if($series_name==''){
		 $data =  $this->db->table($comp_vch_series_tbl)->select('comp_vch_series_id as id, comp_vch_series as label, comp_vch_series as value')
							->where('comp_id', $this->company_id)
							->orderBy('comp_vch_series','ASC')
							->get()->getResultArray();
		 if($data){
			foreach($data as $row){
				$acc_name    = ucwords($row['label']);
				$acc_id      = $row['id'];
				$final_result[] =array("label"=>$acc_name,"value"=>$acc_id,"id"=>$acc_id);			   
			  }
		   }
		if($final_result){
			foreach($final_result as $row){
				$acc_name    = ucwords($row['label']);
				$final_resultn[] =array("label"=>$acc_name,"value"=>$acc_name,"id"=>$acc_name);			   
			  }
		   }	   
		}
		
		 return $final_resultn; 
    }
	

	function relevant_acc_bds_dropdown($accbsd_name)
  	{
  		$acc_file_name    = 'acc'.$this->session->get('ses_comp_fy_id').'.json';
    	$bsd_file_name    = 'bsd'.$this->session->get('ses_comp_fy_id').'.json';

  		$accounts_list = json_decode(file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$acc_file_name),true);
    	$bsd_accounts  = json_decode(file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$bsd_file_name),true);

    	if($accounts_list!='' &&  $bsd_accounts!='')
        	$list=array_merge($accounts_list,$bsd_accounts);
	   	else
		   	$list  = $accounts_list;
		$final_result=array();
		foreach($list as $row){
			if(strtolower(trim($row['label']))!=strtolower(trim($accbsd_name))){
			$final_result[]=array("label"=>$row['label'],"value"=>$row['id'],"id"=>$row['id']);
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

    function get_voucher_no($voucher_type_id,$VchOthrBo=0){
		$comp_vch_cons_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
		$builder = $this->db->table($comp_vch_cons_tbl); 
		$builder->select('MAX(comp_vch_no) as max_comp_vch_no');
		$builder->where('voucher_type_id',$voucher_type_id);
		if($this->session->get('ses_boid')!='' && $VchOthrBo==0)
	   $builder->where('bo_id', $this->session->get('ses_boid'));
	   else
	   $builder->where('bo_id',$VchOthrBo);
		$builder->orderBy('voucher_txn_id','DESC');	
		$result =  $builder->get()->getRowArray();
		if($result){
			return $result['max_comp_vch_no'] + 1;
		}
		else
			return 1;
	}
	
	function get_billno_no($voucher_type_id){
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
	
	function voucher_series_dropdowns($voucher_type_id){
		$comp_vch_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
		$data =  $this->db->table($comp_vch_series_tbl)
							->where('voucher_type_id', $voucher_type_id)
							->where('comp_id', $this->company_id)
							->orderBy('comp_vch_series','ASC')
							->get()->getResultArray();
		return $data;	
	}
	
	function get_voucherconsinfo($voucher_txn_id,$voucher_type_id = 0)
    {	 
	    $vch_conso_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	  	$builder = $this->db->table($vch_conso_tbl);
	  	$builder->where('voucher_txn_id', $voucher_txn_id);		
   
	  	if($voucher_type_id != 0)
	  		$builder->where('voucher_type_id', $voucher_type_id);
	  	$builder->where('comp_id', $this->company_id);
	  	$result = $builder->get()->getRowArray();

	  	return $result;   	   
    }
	

    function get_voucher_cons_info($voucher_txn_id,$voucher_type_id = 0)
    {	 
	    $vch_conso_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	  	$builder = $this->db->table($vch_conso_tbl);
	  	$builder->where('voucher_txn_id', $voucher_txn_id);
	    $builder->where('bo_id', $this->session->get('ses_boid'));   
	  	if($voucher_type_id >0)
	  	$builder->where('voucher_type_id', $voucher_type_id);
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

     function delete_gstroutsup_data($voucher_txn_id)
    {
    	$gstroutsup_tbl = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
		$this->db->table($gstroutsup_tbl)->where('voucher_txn_id', $voucher_txn_id)->delete();
    }
	
	 function delete_gstrinwsup_data($voucher_txn_id)
    {
    	$gstrinwsup_tbl = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
		$this->db->table($gstrinwsup_tbl)->where('voucher_txn_id', $voucher_txn_id)->delete();
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
				$itemtxnnnn_tbl = $this->company_id.'_itemtxnnnn_'.$value_['master_id'].'_'.$this->session->get('ses_comp_fy_id');
				
				if($this->session->get('ses_boid')!=''){
					$result = $this->db->table($itemtxnnnn_tbl)
									->where('voucher_txn_id', $voucher_txn_id)
									->where('item_id', $value_['master_id'])
									->where('bo_id', $this->session->get('ses_boid'))
									->where('batch_id !=', 0)
									->get()->getResultArray();
				}
				else{
				$result = $this->db->table($itemtxnnnn_tbl)
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
	
	function delete_acccrsref_data($voucher_txn_id){// composition
	 	$table = $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
	   	$this->db->table($table)->where('acc_cross_ref_data', $voucher_txn_id)->delete();
	}

	function delete_acc_crsref_data_by_tag($voucher_txn_id, $tag){
	 	$table = $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!='')
		$this->db->table($table)->where('bo_id', $this->session->get('ses_boid'))->where('voucher_txn_id', $voucher_txn_id)->where('acc_cross_ref_type', $tag)->delete();
	   else 
	   	$this->db->table($table)->where('voucher_txn_id', $voucher_txn_id)->where('acc_cross_ref_type', $tag)->delete();
	  }

	function delete_acctgstsum_data($voucher_txn_id){
	 	$table1 = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
		$table2 = $this->company_id.'_acctgstfcy_'.$this->session->get('ses_comp_fy_id');
		
		$this->db->table($table1)->where('vch_txn_id', $voucher_txn_id)->delete();
		$this->db->table($table2)->where('vch_txn_id', $voucher_txn_id)->delete();
	}
    function delete_ewbpartbdt_data($voucher_txn_id){
	 	$table1 = $this->company_id.'_ewbpartbdt_'.$this->session->get('ses_comp_fy_id');		
		$this->db->table($table1)->where('voucher_txn_id', $voucher_txn_id)->delete();
	}

	function delete_itm_oth_data($voucher_txn_id){
	 	$table = $this->company_id.'_itemtxnoth_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!='')
		$this->db->table($table)->where('bo_id', $this->session->get('ses_boid'))->where('voucher_txn_id', $voucher_txn_id)->delete();
		
        else 
	   	$this->db->table($table)->where('voucher_txn_id', $voucher_txn_id)->delete();
	}
    function get_party_info($voucher_txn_id){
		$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($comp_txn_tbl);
    	$builder->where('voucher_txn_id', $voucher_txn_id);
		$builder->where('master_id_type', 'acc');
    	$builder->orderBy('txn_id', 'asc');
    	$builder->limit(1);
    	$comp_txn = $builder->get()->getRowArray();
		return $comp_txn;
	} 
	function get_party_oth_info($voucher_txn_id){
		$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($comp_txn_tbl);
    	$builder->where('voucher_txn_id', $voucher_txn_id);
		$builder->where('master_id_type', 'aco');
    	$builder->orderBy('txn_id', 'asc');
    	$builder->limit(1);
    	$comp_txn = $builder->get()->getRowArray();
		return $comp_txn;
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
    	$builder      = $this->db->table($comp_txn_tbl);
    	$builder->where('voucher_txn_id', $voucher_txn_id);
    	$builder->where('master_id_type', 'acc');
    	$builder->orderBy('txn_id', 'asc');
    	$builder->limit(1);
    	$comp_txn    = $builder->get()->getRowArray();
		
     	if($comp_txn){
		$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$comp_txn['master_id'].'_'.$this->session->get('ses_comp_fy_id');
    	$builders    = $this->db->table($acc_txn_tbl);
    	$builders->where('voucher_txn_id', $voucher_txn_id);				
    	$builders->where('acc_id', $comp_txn['master_id']);
    	$result = $builders->get()->getRowArray();
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
		}	
		else
			$result['acc_txn_narr']='';		
    	return $result;
    }
	function get_partyo_transaction($voucher_txn_id)
    {   
	    $comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
    	$builder      = $this->db->table($comp_txn_tbl);
    	$builder->where('voucher_txn_id', $voucher_txn_id);
    	$builder->whereIn('master_id_type', array('acc','aco'));
    	$builder->orderBy('txn_id', 'asc');
    	$builder->limit(1);
    	$comp_txn    = $builder->get()->getRowArray();
		
		if($comp_txn['master_id_type']=='aco'){
		  $acc_txn_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
	  	}
		
	   else{	  
		$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$comp_txn['master_id'].'_'.$this->session->get('ses_comp_fy_id');
	   }
    	$builders    = $this->db->table($acc_txn_tbl);
    	$builders->where('voucher_txn_id', $voucher_txn_id);				
    	$builders->where('acc_id', $comp_txn['master_id']);
    	$result = $builders->get()->getRowArray();
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
	
	function get_bsd_taxinfo($bill_sundry_id){
      $bdstaxmstn_tbl = $this->company_id.'_bdstaxmstn_'.$this->session->get('ses_comp_fy_id');
	   $tax_data       = $this->db->table($bdstaxmstn_tbl)
	   					->where('bill_sundry_id',$bill_sundry_id)
						->orderBy('cmp_tax_cat_id','DESC')						
	   					->get()->getRowArray();	
						
	   if($tax_data){			
			$tax_rates          = $this->cmpgstcatninfo($tax_data['cmp_tax_cat_id']);			
		  	$item_hsn_sac       = $tax_data['bill_hsn_sac'];
			$tax_cat_id         = $tax_data['cmp_tax_cat_id'];
			$bill_tax_type      = $tax_data['bill_tax_type'];
			if($tax_rates){
			$item_tax_igst_rate = $tax_rates['cmp_tax_cat_igst'];
			$item_tax_cess_rate = $tax_rates['cmp_tax_cat_cess'];
			$item_tax_wef       = $tax_rates['cmp_tax_cat_wef'];
			}else{
			$item_tax_igst_rate = 0;
			$item_tax_cess_rate = 0;
			$item_tax_wef       = '';	
			}
		}else{
		    $item_hsn_sac       = '';
			$tax_cat_id         = '';
			$item_tax_igst_rate = 0;
			$item_tax_cess_rate = 0;
			$item_tax_wef       = '';
			$bill_tax_type	    =0;		
		    }	
  $final_array = array("item_hsn_sac"=>$item_hsn_sac,"tax_cat_id"=>$tax_cat_id,
                       "item_tax_igst_rate"=>$item_tax_igst_rate,"item_tax_cess_rate"=>$item_tax_cess_rate,
					   "item_tax_wef"=>$item_tax_wef,'bill_tax_type'=>$bill_tax_type);
     return $final_array;					   
	}
	
	function get_account_taxinfo($account_id){
       $acctaxmstn_tbl = $this->company_id.'_acctaxmstn_'.$this->session->get('ses_comp_fy_id');
	   $tax_data       = $this->db->table($acctaxmstn_tbl)
	   					->where('acc_id',$account_id)
						->where('comp_id',$this->company_id)
						->orderBy('cmp_tax_cat_id','DESC')						
	   					->get()->getRowArray();	
						
	   if($tax_data){			
			$tax_rates          = $this->cmpgstcatninfo($tax_data['cmp_tax_cat_id']);			
		  	$item_hsn_sac       = $tax_data['acc_hsn_sac'];
			$tax_cat_id         = $tax_data['cmp_tax_cat_id'];
			if($tax_rates){
			$item_tax_igst_rate = $tax_rates['cmp_tax_cat_igst'];
			$item_tax_cess_rate = $tax_rates['cmp_tax_cat_cess'];
			$item_tax_wef       = $tax_rates['cmp_tax_cat_wef'];
			$tax_cess_basis     = $tax_rates['cmp_tax_cat_cess_basis'];
			}
			else{
			$item_tax_igst_rate = 0;
			$item_tax_cess_rate = 0;
			$item_tax_wef       = '';	
			$tax_cess_basis     = 1;	
			}
		}else{
		    $item_hsn_sac       = '';
			$tax_cat_id         = '';
			$item_tax_igst_rate = 0;
			$item_tax_cess_rate = 0;
			$item_tax_wef       = '';	
			$tax_cess_basis     = 1;		
		    }	
  $final_array = array("item_hsn_sac"=>$item_hsn_sac,"tax_cat_id"=>$tax_cat_id,
                       "item_tax_igst_rate"=>$item_tax_igst_rate,"item_tax_cess_rate"=>$item_tax_cess_rate,
					   "item_tax_wef"=>$item_tax_wef,'bill_tax_type'=>'',"tax_cess_basis"=>$tax_cess_basis);
     return $final_array;					   
	}
	
	
	function GetAccountInfoAutoRcptParty($voucher_txn_id){
		$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
    	
		$builder_st = $this->db->table($comp_txn_tbl);
    	$builder_st->where('voucher_txn_id', $voucher_txn_id);
    	$builder_st->whereIn('master_id_type', ['acc']);
    	$builder_st->orderBy('txn_id', 'asc');
    	$acc_txns = $builder_st->get()->getRowArray();
		return  $acc_txns['master_id'];		
	}	
	
	function get_acc_tax_oth($voucher_txn_id)
    {   $result = array(); 
		$bo_state_code    = $this->session->get('ses_bostecd');
    	$fcy_rate = 1;

	   	$voucher_info = $this->get_voucher_cons_info($voucher_txn_id);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$fcy_rate = $this->get_forex_rate_fcy($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}
		
		/*************** Start get party account state code ********************************/
		$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
    	
		$builder_st = $this->db->table($comp_txn_tbl);
    	$builder_st->where('voucher_txn_id', $voucher_txn_id);
    	$builder_st->whereIn('master_id_type', ['acc']);
    	$builder_st->orderBy('txn_id', 'asc');
    	$acc_txns = $builder_st->get()->getRowArray();
		$party_id = $acc_txns['master_id'];
		
		$party_state_code = $this->get_acc_state_code($party_id);
		/*************** End get party account state code    ********************************/
		
    	
		// Account Tax Account Data Show In Tax summary
		$builder = $this->db->table($comp_txn_tbl);
    	$builder->where('voucher_txn_id', $voucher_txn_id);
    	$builder->where('master_id_type', 'aco');// ACC OTH
    	$builder->orderBy('txn_id', 'asc');
    	$comp_txns   = $builder->get()->getResultArray();
    	$accttxn_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
    	foreach ($comp_txns as $key => $value) {
    		$builder = $this->db->table($accttxn_tbl);
	    	$builder->where('voucher_txn_id', $voucher_txn_id);
	    	$builder->where('acc_id', $value['master_id']);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
	    	$builder->where('txn_id', $value['txn_id']);
	    	$data = $builder->get()->getRowArray();
			
	    	if($data){
				$get_item_taxinfo = $this->get_account_taxinfo($value['master_id']);
				
				if($get_item_taxinfo){
					$tax_catg_id = $get_item_taxinfo['tax_cat_id'];
				}else
				  $tax_catg_id = 0;				  				
				
				$item_txnn_tbl = $this->company_id.'_accnttxnnn_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
				$builder3 = $this->db->table($item_txnn_tbl);
				$builder3->where('acc_id', $value['master_id']);
				$builder3->where('voucher_txn_id', $voucher_txn_id);
				$builder3->where('comp_id', $this->company_id);
				$itm_txn_data = $builder3->get()->getRowArray();
				
				if($itm_txn_data){
				$item_taxable_amount = $itm_txn_data['acc_txn_amount'];
		        // party state code and ho state code same then only cgst & sgst applied
				$cgst_tt = (($get_item_taxinfo['item_tax_igst_rate']/2*$item_taxable_amount)/100);
				$sgst_tt = (($get_item_taxinfo['item_tax_igst_rate']/2*$item_taxable_amount)/100);
				$igst_tt = (($get_item_taxinfo['item_tax_igst_rate']*$item_taxable_amount)/100);
					
				}else{
					$cgst_tt=$sgst_tt =$igst_tt=0;					
				 }				
				if($party_state_code==$bo_state_code){
					$total_tax = $sgst_tt+$cgst_tt;					
				 }
				 else{
					$total_tax = $igst_tt;
				 }
				$accountinfo = $this->get_account_info($value['master_id']);
	    		
	    		$result[] = [
	    				'tax_item_id'=> $data['acc_id'],	
                        'tax_catg_id'=> $tax_catg_id,
						'tax_cat_id'=> $tax_catg_id,						
						'tax_hsn_sac'=> $get_item_taxinfo['item_hsn_sac'],
						'tax_amt' 	 => parseAmount($item_taxable_amount * $fcy_rate),
						'tax_rate' 	 => $get_item_taxinfo['item_tax_igst_rate'],
						'account_name'       => $accountinfo['acc_name'],
						'igst'       => $igst_tt,
						'cess' 		 => 0,
						'cgst' 		 => $cgst_tt,
						'sgst'		 => $sgst_tt,
						'is_bsd'     => '0',
						"is_sundry" => "0",
						"is_acc"    =>"1",
						"is_bbb"=>"",
						"is_cc" =>"",
						"is_cash"=>"",
						'is_item'    => '1',
						'account_id' => $data['acc_id'],
						'amount' 	 => parseAmount($item_taxable_amount * $fcy_rate),
						'acc_id' => $data['acc_id'],
						'id' => $data['acc_id'],
						'total_tax'	 => parseAmount($total_tax)
	    			];
				
	    	}
    	}
		
		// Bill Sundry Tax Account Data Show In Tax summary
		$builder = $this->db->table($comp_txn_tbl);
    	$builder->where('voucher_txn_id', $voucher_txn_id);
    	$builder->where('master_id_type', 'bso');// Bill Sundry OTH IN accttxnoth
    	$builder->orderBy('txn_id', 'asc');
    	$comp_txns   = $builder->get()->getResultArray();
    	$accttxn_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
    	foreach ($comp_txns as $key => $value) {
    		$builder = $this->db->table($accttxn_tbl);
	    	$builder->where('voucher_txn_id', $voucher_txn_id);
	    	$builder->where('acc_id', $value['master_id']);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
	    	$builder->where('txn_id', $value['txn_id']);
	    	$data = $builder->get()->getRowArray();
			
	    	if($data){
				$get_item_taxinfo = $this->get_bsd_taxinfo($value['master_id']);
				
				if($get_item_taxinfo){
					$tax_catg_id = $get_item_taxinfo['tax_cat_id'];
				}else
				  $tax_catg_id = 0;				  				
				
				$item_txnn_tbl = $this->company_id.'_sundrytxnn_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
				$builder3 = $this->db->table($item_txnn_tbl);
				$builder3->where('bill_sundry_id', $value['master_id']);
				$builder3->where('voucher_txn_id', $voucher_txn_id);
				$builder3->where('comp_id', $this->company_id);
				$itm_txn_data = $builder3->get()->getRowArray();
				
				if($itm_txn_data){
				$item_taxable_amount = $itm_txn_data['sundry_txn_amount'];
		        // party state code and ho state code same then only cgst & sgst applied
				$cgst_tt = (($get_item_taxinfo['item_tax_igst_rate']/2*$item_taxable_amount)/100);
				$sgst_tt = (($get_item_taxinfo['item_tax_igst_rate']/2*$item_taxable_amount)/100);
				$igst_tt = (($get_item_taxinfo['item_tax_igst_rate']*$item_taxable_amount)/100);
					
				}else{
					$cgst_tt=$sgst_tt =$igst_tt=0;					
				 }				
				if($party_state_code==$bo_state_code){
					$total_tax = $sgst_tt+$cgst_tt;					
				 }
				 else{
					$total_tax = $igst_tt;
				 }
				$bsdinfo = $this->get_billsundry_info($value['master_id']);
	    		
	    		$result[] = [
	    				'tax_item_id'=> $data['acc_id'],	
                        'tax_catg_id'=> $tax_catg_id,'tax_cat_id'=> $tax_catg_id,						
						'tax_hsn_sac'=> $get_item_taxinfo['item_hsn_sac'],
						'tax_amt' 	 => parseAmount($item_taxable_amount * $fcy_rate),
						'tax_rate' 	 => $get_item_taxinfo['item_tax_igst_rate'],
						'bl_tax_rate' 	 => $get_item_taxinfo['item_tax_igst_rate'],
						'billsundry_name'       => $bsdinfo['bill_sundry_name'],
						'igst'       => $igst_tt,
						'bl_nature'  => $get_item_taxinfo['bill_tax_type'],
						'cess' 		 => 0,
						'cgst' 		 => $cgst_tt,
						'sgst'		 => $sgst_tt,
						'is_bsd'     => '1',
						'is_item'    => '0',
						'acc_id'     => $data['acc_id'],
						'amount' 	 => parseAmount($item_taxable_amount * $fcy_rate),
						'total_tax'	 => parseAmount($total_tax)
	    			];
				
	    	}
    	}		
		
    	return $result;
    }
	
	function get_gstfcy_info($voucher_txn_id,$txn_id){
	    $acctgstsum_tbl = $this->company_id.'_acctgstfcy_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($acctgstsum_tbl);
    	$builder->where('vch_txn_id', $voucher_txn_id);
		$builder->where('txn_id', $txn_id);
		$builder->orderBy('txn_id', 'asc');
    	$response = $builder->get()->getRowArray();		
		return $response;
	}
	
	function get_item_tax_oth($voucher_txn_id,$vch_subtype_id)
    {   
	    $tax_catg_exempted = array('ZERSPLY','DEEMEXP','EXMSPLY','NONGSTS','UNDSPLY','NILSPLY');
	    $bo_state_code    = $this->session->get('ses_bostecd');			
    	$fcy_rate         = 1;
	   	$voucher_info     = $this->get_voucher_cons_info($voucher_txn_id);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$fcy_rate = $this->get_forex_rate_fcy($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}
		
		$acctgstsum_tbl = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($acctgstsum_tbl);
    	$builder->where('vch_txn_id', $voucher_txn_id);
		$builder->orderBy('txn_id', 'asc');
    	$response = $builder->get()->getResultArray();
		$result   = array();
		if($response){
		foreach($response as $rows){
			$acc_type      = $rows['acc_type'];
			$acc_id        = $rows['acc_id'];
			$txn_id        = $rows['txn_id'];
			$taxable_amt   = $rows['taxable_amt']* $fcy_rate;
			$total_tax     = $rows['total_tax']* $fcy_rate;
			$item_hsn_sac  = $rows['acc_hsn_sac'];
			
			if($voucher_info['currency_id']>1){
			$gstfcy        = $this->get_gstfcy_info($voucher_txn_id,$txn_id);
			if($gstfcy){
			 $taxable_val_fcy = parseAmount($gstfcy['acc_taxable_val_fcy']);	
			 $total_tax_fcy   = parseAmount($gstfcy['total_tax_fcy']);
			}
			else{
			 $taxable_val_fcy  = parseAmount($rows['taxable_amt']* $fcy_rate);	
			 $total_tax_fcy    = parseAmount($rows['total_tax']* $fcy_rate);
			 }
			}
			else{
			$taxable_val_fcy = $total_tax_fcy=0;	
			}
			
			if($acc_type=='acc'){
				$get_item_taxinfo = $this->get_account_taxinfo($rows['acc_id']);
				
				if($get_item_taxinfo){
					$tax_catg_id = $get_item_taxinfo['tax_cat_id'];
				}else
				  $tax_catg_id = 0;	
				
				   $item_mrp =0;
				   
				if(parseAmount($rows['acc_igst_rate'])==0){
				  $acc_igst_rate = 	$rows['acc_cgst_rate']+$rows['acc_sgst_rate'];
				}   
			     else
				   $acc_igst_rate =$rows['acc_igst_rate']; 
				$result[] = [
	    				'tax_item_id'=> $rows['acc_id'],	
						'item_id'    => $rows['acc_id'],
						'item_mrp'   => $item_mrp,
                        'tax_catg_id'=> $tax_catg_id,
						'tax_cat_id' => $tax_catg_id,						
						'tax_hsn_sac'=> $item_hsn_sac,
						'tax_amt' 	 => parseAmount($taxable_amt),
						'tax_rate' 	 => parseAmount($acc_igst_rate),
						'cess_rate'  => parseAmount($rows['acc_cess_rate']),
						'igst'       => parseAmount($rows['acc_igst']),
						'cess' 		 => parseAmount($rows['acc_cess']),
						'cgst' 		 => parseAmount($rows['acc_cgst']),
						'sgst'		 => parseAmount($rows['acc_sgst']),
						'cess_basis' => $rows['acc_cess_basis'],
						'is_bsd'     => '0',
						'is_item'    => '1',
						'total_tax'	 => parseAmount($total_tax),
						'total_tax_fcy'	 => parseAmount($total_tax_fcy),
						'tax_amt_fcy'	 => parseAmount($taxable_val_fcy),						
	    			];
			}
			
			if($acc_type=='itm'){
				$get_item_taxinfo = $this->get_item_taxinfo($acc_id);
				if($get_item_taxinfo){
					//$item_hsn_sac = $get_item_taxinfo['item_hsn_sac'];
					$tax_catg_id    = $get_item_taxinfo['tax_cat_id'];
					$tax_short_code = $get_item_taxinfo['tax_short_code'];
				}				
				else{
				   $tax_catg_id     = 0;	
				   $tax_short_code  = '';
				  // $item_hsn_sac  = '0';
				}
				
				$itemvalmst_info   = $this->itemvalmst_info($acc_id);
				if($itemvalmst_info)
				   $item_mrp = parseAmount($itemvalmst_info['item_mrp']);
				else 
				   $item_mrp =0;
			   
			   if(parseAmount($rows['acc_igst_rate'])==0){
				  $acc_igst_rate  = $rows['acc_cgst_rate']+$rows['acc_sgst_rate'];
				}   
			     else
				   $acc_igst_rate = $rows['acc_igst_rate']; 
			   
				$result[] = [
	    				'tax_item_id'=> $rows['acc_id'],	
						'item_id'    => $rows['acc_id'],
						'item_mrp'   => $item_mrp,
                        'tax_catg_id'=> $tax_catg_id,
						'tax_cat_id' => $tax_catg_id,						
						'tax_hsn_sac'=> $item_hsn_sac,
						'tax_amt' 	 => parseAmount($taxable_amt),
						'tax_rate' 	 => parseAmount($acc_igst_rate),
						'cess_rate'  => parseAmount($rows['acc_cess_rate']),
						'igst'       => parseAmount($rows['acc_igst']),
						'cess' 		 => parseAmount($rows['acc_cess']),
						'cgst' 		 => parseAmount($rows['acc_cgst']),
						'sgst'		 => parseAmount($rows['acc_sgst']),
						'cess_basis' => $rows['acc_cess_basis'],
						'is_bsd'     => '0',
						'is_item'    => '1',
						'total_tax'	 => parseAmount($total_tax),
						'total_tax_fcy'	 => parseAmount($total_tax_fcy),
						'tax_amt_fcy'	 => parseAmount($taxable_val_fcy),
						'tax_short_code'  => $tax_short_code
	    			];
			}
			if($acc_type=='bsd'){
			 $bsdinfo = $this->get_billsundry_info($rows['acc_id']);
			 if($bsdinfo){	
			 $get_item_taxinfo = $this->get_bsd_taxinfo($rows['acc_id']);				
				if($get_item_taxinfo){
					$tax_catg_id = $get_item_taxinfo['tax_cat_id'];
					//$item_hsn_sac = $get_item_taxinfo['item_hsn_sac'];
					
				}
				else{
				  $tax_catg_id = 0;	
				 // $item_hsn_sac = 0;
				}
				if(parseAmount($rows['acc_igst_rate'])==0){
				  $acc_igst_rate = 	$rows['acc_cgst_rate']+$rows['acc_sgst_rate'];
				}   
			     else
				   $acc_igst_rate =$rows['acc_igst_rate']; 
			 $result[]=[
	    				'tax_item_id'=> $rows['acc_id'],							
                        'tax_catg_id'=> $tax_catg_id,
						'tax_cat_id'=> $tax_catg_id,						
						'tax_hsn_sac'=> $item_hsn_sac,
						'tax_amt' 	 => parseAmount($taxable_amt),
						'tax_rate' 	 => parseAmount($acc_igst_rate),
						'bl_tax_rate' 	  => parseAmount($rows['acc_igst_rate']),
						'b_tx_cess_rte'   => parseAmount($rows['acc_cess_rate']),
						'billsundry_name' => $bsdinfo['bill_sundry_name'],
						'label'       => $bsdinfo['bill_sundry_name'],
						'value'       => $bsdinfo['bill_sundry_name'],
						'igst'       => parseAmount($rows['acc_igst']),
						'cess' 		 => parseAmount($rows['acc_cess']),
						'cgst' 		 => parseAmount($rows['acc_cgst']),
						'sgst'		 => parseAmount($rows['acc_sgst']),
						'cess_basis' => $rows['acc_cess_basis'],
						'bl_nature'   => '',
						'is_bsd'      => '1',
						'is_item'     => '0',
						'total_tax'	 => parseAmount($total_tax),
						'total_tax_fcy'	 => parseAmount($total_tax_fcy),
						'tax_amt_fcy'	 => parseAmount($taxable_val_fcy),	
	    			 ];	
					 
			    }
			}
		  }	
	  }else{
	
	/* Dicussion Pending IF supplytype=='3' || supplytype=='5' || supplytype=='14' || supplytype=='15' || supplytype=='16')  no tax will applied on this type of supply types
	 
	 */
	
	 /* $comptxnmst_master = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');


	$comp_itemtexn      =  $this->db->table($comptxnmst_master)->whereIn('master_id_type',array('acc','aco','itm'))->where('voucher_txn_id',$voucher_txn_id)->get()->getResultArray(); 
	
	if($comp_itemtexn){
	    foreach($comp_itemtexn as $key => $cmptxn_row){
			if($key >0){
	        $master_id_type =  $cmptxn_row['master_id_type'];
			if($master_id_type=='acc'){
				$item_id	 = $cmptxn_row['master_id'];
				$tem_taxinfo   = $this->get_account_taxinfo($item_id);
				$result[]=[
	    				'tax_item_id'=> $item_id,							
                        'tax_catg_id'=> $tem_taxinfo['tax_cat_id'],
						'tax_cat_id'=> $tem_taxinfo['tax_cat_id'],						
						'tax_hsn_sac'=> $tem_taxinfo['item_hsn_sac'],
						'tax_amt' 	 => 0,
						'tax_rate' 	 => 0,
						'bl_tax_rate' 	  => 0,
						'b_tx_cess_rte'   => 0,
						'billsundry_name' => 'ssds',
						'label'       => 'sdsds',
						'value'       => 'xcxcxcw',
						'igst'       => 0,
						'cess' 		 => 0,
						'cgst' 		 => 0,
						'sgst'		 => 0,
						'cess_basis' => '',
						'bl_nature'   => '',
						'is_bsd'      => '0',
						'is_item'     => '0',
						'total_tax'	 => 0
	    			 ];
			}
			if($master_id_type=='itm'){
				$item_id	   =  $cmptxn_row['master_id'];
				$tem_taxinfo   = $this->get_item_taxinfo($item_id);
				$result[]=[
	    				'tax_item_id'=> $item_id,							
                        'tax_catg_id'=> $tem_taxinfo['tax_cat_id'],
						'tax_cat_id'=> $tem_taxinfo['tax_cat_id'],						
						'tax_hsn_sac'=> $tem_taxinfo['item_hsn_sac'],
						'tax_amt' 	 => 0,
						'tax_rate' 	 => 0,
						'bl_tax_rate' 	  => 0,
						'b_tx_cess_rte'   => 0,
						'billsundry_name' => 'sdsd',
						'label'       => 'sdsd',
						'value'       => 'xcxc',
						'igst'       => 0,
						'cess' 		 => 0,
						'cgst' 		 => 0,
						'sgst'		 => 0,
						'cess_basis' => '',
						'bl_nature'   => '',
						'is_bsd'      => '0',
						'is_item'     => '1',
						'total_tax'	 => 0
	    			 ];
			    }
			}
		}
	 } */
	}	
		
	 return $result;
	}
	
	function get_memoamount_sale_txn($voucher_txn_id,$type,$account_id){
	    $memotxnnnn_tbl = $this->company_id.'_memotxnnnn_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($memotxnnnn_tbl);
		$builder->select('acc_txn_amount');
    	$builder->where('voucher_txn_id', $voucher_txn_id);
    	$builder->where('acc_type', $type);
		$builder->where('acc_id', $account_id);
		if($this->session->get('ses_boid')!='')
		 $builder->where('bo_id', $this->session->get('ses_boid'));		
    	$builder->orderBy('acc_txn_id', 'asc');
    	$row = $builder->get()->getRowArray();
		if($row)
		return $row['acc_txn_amount'];
	    else
		 return  '';	
	}
	
	function get_taxamount_sale_txn($voucher_txn_id,$type,$txn_id){
	    $vchaddinfo_tbl = $this->company_id.'_vchaddinfo_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($vchaddinfo_tbl);
		$builder->select('vch_txn_incl');
    	$builder->where('voucher_txn_id', $voucher_txn_id);
    	$builder->where('txn_type', $type);
		$builder->where('txn_id', $txn_id);
		$row = $builder->get()->getRowArray();
		//echo $this->db->GetLastQuery();
		if($row)
		return parseAmount($row['vch_txn_incl']);
	    else
		 return  '';	
	}
    
     function get_item_transactions($voucher_txn_id)
    {   
		$tax_catg_exempted = array('ZERSPLY','DEEMEXP','EXMSPLY','NONGSTS','UNDSPLY','NILSPLY');
		
	    //$tax_catg_exempted = array(16,14,8,15,18);
		$get_fcrates       = $this->get_fcrates($voucher_txn_id);		
    	$fcy_rate          = 1;
		$bo_state_code     = $this->session->get('ses_bostecd');
	   	$voucher_info      = $this->get_voucher_cons_info($voucher_txn_id);
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
				$itemvalmst_info   = $this->itemvalmst_info($value['master_id']);
				if($itemvalmst_info)
				   $item_mrp = $itemvalmst_info['item_mrp'];
				else 
				   $item_mrp =0;	
				
				
				
				$get_item_taxinfo = $this->get_item_taxinfo($value['master_id']);
				
				$item = $this->get_item_info($value['master_id']);
	    		$data['item_name'] = $item['item_name'];
				$bom_id= $item['bom_id'];
				$bom_batch_qty= $item['bom_item_container'];
				$data['item_sku'] = $item['item_sku'];
                $data['item_hsn'] = $get_item_taxinfo['item_hsn_sac'];
				$supply_type      = $get_item_taxinfo['supply_type'];
	    		
	    		$item_unit_id = $data['item_unit'];
	    		$item_unit_info = $this->item_unit_info($item_unit_id);
				if(isset($item_unit_info)){
    				$item_unit = $item_unit_info['item_unit'];
				}
			    else{
					$item_unit_id = 0;
		    		$item_unit = 'No Unit';	
				}	
				
				
				if($get_item_taxinfo){
					$tax_catg_id   = $get_item_taxinfo['tax_cat_id'];
					$item_tax_igst_rate  = $get_item_taxinfo['item_tax_igst_rate'];
					$item_tax_cess_rate  = $get_item_taxinfo['item_tax_cess_rate'];
					$tax_cat_cess_basis  = $get_item_taxinfo['tax_cat_cess_basis'];
					$tax_short_code      = $get_item_taxinfo['tax_short_code'];
				}else{
				  $tax_catg_id = 0;					
				  $item_tax_igst_rate =0;
				  $item_tax_cess_rate=0;
				  $tax_cat_cess_basis =1;
				  $tax_short_code='';
				}
				
				if($tax_short_code!='' && in_array($tax_short_code,$tax_catg_exempted))
				   $tax_exempted='y'; 
				 else if(isset($tax_data['tax_cat_id']) && $tax_data['tax_cat_id']>0 && in_array($tax_data['tax_cat_id'],$tax_catg_exempted))
				   $tax_exempted='y'; 
				else
				  $tax_exempted='n';
	  
				// get item narration_txt
				$get_narration_info = $this->get_voucher_narration_info($voucher_txn_id,'short',$value['txn_id']);

				$item_description = $get_narration_info['vch_short_narr'] ?? '';

				$data['item_txn_qty'] = $data['item_txn_qty'] != 0 ? $data['item_txn_qty'] : 1;
				if($item_tax_igst_rate >0 && $item_tax_cess_rate >0){
					$item_tax_rate = parseAmount($item_tax_igst_rate).'%+'.parseAmount($item_tax_cess_rate).'%';
				}else
				 $item_tax_rate = parseAmount($item_tax_igst_rate);
			 
			    if($tax_cat_cess_basis=="1"){
					$item_price = parseAmountPrice($data['item_txn_amount'] * $fcy_rate,4);
					$cess_total = ($item_tax_cess_rate*$item_price)/100;
				}else{
					$cess_total = ($item_tax_cess_rate*$data['item_txn_qty']*$item_mrp)/100;
				}
				
				$memo_amount = $this->get_memoamount_sale_txn($voucher_txn_id,'itm',$data['item_id']);
				$taxinc_amount  = $this->get_taxamount_sale_txn($voucher_txn_id,'itm',$value['txn_id']);
				if($taxinc_amount >0 ){
					 // Tax Inclusive Enabled					 
				    $itemPrice  = parseAmountPrice(($taxinc_amount / $data['item_txn_qty']) * $fcy_rate,4);	 
					
				}
				else{
					if($voucher_info['currency_id']>1)
					 $itemPrice  = parseAmountPrice(($data['item_txn_amount'] * $get_fcrates) / $data['item_txn_qty'],4);	
					
					else
					$itemPrice  = parseAmountPrice(($data['item_txn_amount'] / $data['item_txn_qty']) * $fcy_rate,4);
					
				}
				if($voucher_info['currency_id']>1)
				$item_amountfc = ($data['item_txn_fcy']==0)?parseAmount($data['item_txn_amount']*$get_fcrates):$data['item_txn_fcy'];
			    else 
				 $item_amountfc = 0;
					 
				$result[] = [
	    				'item_id' 				=> $data['item_id'],
						'item_hsn' 				=> $data['item_hsn'],
						'item_hsn_sac' 			=> $data['item_hsn'],
						'tax_catg_id'   		=> $tax_catg_id,
						'tax_cat_id'   		    => $tax_catg_id,
						'igst_rate'             => $item_tax_igst_rate,
						'item_name' 			=> $data['item_name'],
						'item_unit' 			=> $item_unit,
						'item_unit_id' 			=> $item_unit_id,
						'item_qty' 				=> floatval($data['item_txn_qty']),
						'item_txn_drcr' 		=> $data['item_txn_drcr'],
						//'item_amount' 			=> parseAmount($data['item_txn_qty']*$itemPrice),
						'item_amount' 			=> parseAmount($data['item_txn_amount']* $fcy_rate),
						'description' 		    => $item_description,
						'item_price'			=> $itemPrice,
						'txn_id'				=> $value['txn_id'],// physical verification,
						'tax_item_id' => $data['item_id'],
						'tax_hsn_sac' => $data['item_hsn'],
						'tax_amt' 	  => parseAmount($data['item_txn_amount'] * $fcy_rate),
						'tax_rate' 	  => $item_tax_rate,
						'igst'        => 0,
						'cess' 		  => parseAmount($cess_total),
						'cess_rate'   => $item_tax_cess_rate,
						'cess_basis'   => $tax_cat_cess_basis,
						'cgst' 		  => 0,
						'sgst'		  => 0,
						'is_bsd'      => '0',
						'is_item'     => '1',
						'total_tax'	  => 0,
						'item_mrp'    => $item_mrp,
						'item_sku'    => $data['item_sku'],
						'supply_type' => $supply_type,
						'fcrates'     => $get_fcrates,
						'memo_amt'    =>  $memo_amount,
						'item_amounttxs'=>$taxinc_amount,
						'tax_exempted' => $tax_exempted,
						'valmethod_id' =>$item['valmethod_id'],
						'item_amountfc' => $item_amountfc,
						'tax_short_code' => $tax_short_code,
						'bom_id' => $bom_id,
						'bom_batch_qty' => $bom_batch_qty
	    			   ];
					   
	    	}
    	}		
    	
		return $result;
    }

    

    function get_item_transactions_oth($voucher_txn_id)
    {
		$tax_catg_exempted = array(16,14,8,15,18);
		$get_fcrates      = $this->get_fcrates($voucher_txn_id);
    	$fcy_rate = 1;

	   	$bo_state_code    = $this->session->get('ses_bostecd');
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
			$builder->where('item_oth_txn_tag!=','TAXSMR');
			
	    	$data = $builder->get()->getRowArray();

	    	if($data){
	    		$item = $this->get_item_info($value['master_id']);
				
				$itemvalmst_info   = $this->itemvalmst_info($value['master_id']);
				if($itemvalmst_info)
				   $item_mrp = $itemvalmst_info['item_mrp'];
				else 
				   $item_mrp =0;
				
				$get_item_taxinfo = $this->get_item_taxinfo($value['master_id']);
				
				$item = $this->get_item_info($value['master_id']);
	    		$data['item_name'] = $item['item_name'];
				$data['item_sku'] = $item['item_sku'];
                $data['item_hsn'] = $get_item_taxinfo['item_hsn_sac'];
				$supply_type      = $get_item_taxinfo['supply_type'];
	    		
	    		$item_unit_id = $data['item_unit'];
	    		$item_unit_info = $this->item_unit_info($item_unit_id);
				if(isset($item_unit_info)){
    				$item_unit = $item_unit_info['item_unit'];
				}
			    else{
					$item_unit_id = 0;
		    		$item_unit = 'No Unit';	
				}	
				
				
				if($get_item_taxinfo){
					$tax_catg_id   = $get_item_taxinfo['tax_cat_id'];
					$item_tax_igst_rate  = $get_item_taxinfo['item_tax_igst_rate'];
					$item_tax_cess_rate  = $get_item_taxinfo['item_tax_cess_rate'];
					$tax_cat_cess_basis  = $get_item_taxinfo['tax_cat_cess_basis'];
				}else{
				  $tax_catg_id = 0;					
				  $item_tax_igst_rate =0;
				  $item_tax_cess_rate=0;
				  $tax_cat_cess_basis =1;
				}
				
				if($tax_catg_id >0 && in_array($tax_catg_id,$tax_catg_exempted))
				   $tax_exempted='y'; 
				 else if(isset($tax_data['tax_cat_id']) && $tax_data['tax_cat_id']>0 && in_array($tax_data['tax_cat_id'],$tax_catg_exempted))
				   $tax_exempted='y'; 
				else
				  $tax_exempted='n';
			  
			  $data['item_oth_txn_qty'] = $data['item_oth_txn_qty'] != 0 ? $data['item_oth_txn_qty'] : 1;
				if($item_tax_igst_rate >0 && $item_tax_cess_rate >0){
					$item_tax_rate = parseAmount($item_tax_igst_rate).'%+'.parseAmount($item_tax_cess_rate).'%';
				}else
				 $item_tax_rate = parseAmount($item_tax_igst_rate);
			 
			    if($tax_cat_cess_basis=="1"){
					$item_price = parseAmountPrice($data['item_oth_txn_amount'] * $fcy_rate,4);
					$cess_total = ($item_tax_cess_rate*$item_price)/100;
				}else{
					$cess_total = ($item_tax_cess_rate*$data['item_oth_txn_qty']*$item_mrp)/100;
				}
			  
	    		$data['item_name'] = $item['item_name'];
				$data['item_hsn'] = $get_item_taxinfo['item_hsn_sac'];
	    		
	    		$item_unit_info = $this->item_unit_info($data['item_unit']);
				if($item_unit_info)
	    		$item_unit_name = $item_unit_info['item_unit'];
			  else
				 $item_unit_name = ''; 
				
				// get item narration_txt
				$get_narration_info = $this->get_voucher_narration_info($voucher_txn_id,'short',$value['txn_id']);
				
				if($get_narration_info){
					$item_description = $get_narration_info['vch_short_narr'];
				}else{
					$item_description = '';
				}
				
				$memo_amount = $this->get_memoamount_sale_txn($voucher_txn_id,'itm',$data['item_id']);
				$taxinc_amount  = $this->get_taxamount_sale_txn($voucher_txn_id,'itm',$value['txn_id']);
				if($taxinc_amount >0 ){
					 // Tax Inclusive Enabled		
                   if($data['item_oth_txn_qty']=='' || $data['item_oth_txn_qty']==0)
                     $data['item_oth_txn_qty']=1;					   
				    $itemPrice  = parseAmount(($taxinc_amount / $data['item_oth_txn_qty']) * $fcy_rate);	 
					
				}
				else{
					if($voucher_info['currency_id']>1)
					 $itemPrice  = parseAmount((parseAmount($data['item_oth_txn_amount'] * $get_fcrates) / $data['item_oth_txn_qty']));	
					
					else{
					if($data['item_oth_txn_qty']=='' || $data['item_oth_txn_qty']==0)
                      $data['item_oth_txn_qty']=1;						
					$itemPrice  = parseAmount(($data['item_oth_txn_amount'] / $data['item_oth_txn_qty']) * $fcy_rate);
					}
					
				}
				
				
				 $item_oth_txn_amount = $data['item_oth_txn_amount'];
				 $item_oth_rate       = $data['item_oth_txn_qty'];
				 
				 
				$cgst_amount = ($item_oth_txn_amount*($item_oth_rate/2)/100);
				$sgst_amount = ($item_oth_txn_amount*($item_oth_rate/2)/100);
				$igst_amount = ($item_oth_txn_amount*$item_oth_rate/100);
				
				if($voucher_info['currency_id']>1){
			     $item_amountfc = ($data['item_oth_txn_fcy']==0)?parseAmount($data['item_oth_txn_amount'] * $get_fcrates):$data['item_oth_txn_fcy'];
				}else 
				 $item_amountfc = 0;	
				
	    		$result[] = [
	    				'item_id' 				=> $data['item_id'],
						'item_hsn' 				=> $data['item_hsn'],
						'item_name' 			=> $data['item_name'],
						'item_unit' 			=> $item_unit_name,
						'item_unit_id' 			=> $data['item_unit'],
						'item_qty' 				=> $data['item_oth_txn_qty'],
						'item_oth_txn_drcr' 	=> $data['item_oth_txn_drcr'],
						'item_amount' 			=> parseAmount($data['item_oth_txn_amount'] * $fcy_rate),
						'description' 			=> $item_description,
						'item_price'			=> $itemPrice,
						'txn_id'				=> $value['txn_id'],// physical verification
						'tax_item_id'=> $data['item_id'],	
                        'tax_catg_id'=> $tax_catg_id,
						'tax_cat_id'=> $tax_catg_id,						
						'tax_hsn_sac'=> $get_item_taxinfo['item_hsn_sac'],
						'item_hsn_sac'=> $get_item_taxinfo['item_hsn_sac'],
						'tax_amt' 	 => parseAmount($data['item_oth_txn_amount'] * $fcy_rate),
						'tax_rate' 	  => $item_tax_rate,
						'igst'       => 0,
						'igst_rate'  => $item_tax_igst_rate,
						'cess' 		  => parseAmount($cess_total),
						'cess_rate'   => $item_tax_cess_rate,
						'cess_basis'   => $tax_cat_cess_basis,
						'item_mrp'    => $item_mrp,
						'cgst' 		 => 0,
						'sgst'		 => 0,
						'is_bsd'     => '0',
						'is_item'    => '1',
						'total_tax'	 => 0,
						'fcrates'        => $get_fcrates,
						'memo_amt'       => $memo_amount,
						'item_amounttxs' => $taxinc_amount,
						'tax_exempted'   => $tax_exempted,
						'item_amountfc'  => $item_amountfc
	    			];
				
	    	}
    	}	
    	return $result;
    }
	
	 public function cmptaxcatminfo($cmp_tax_cat_id){
	 $cmptaxcatm_tbl = $this->company_id.'_cmptaxcatm_'.$this->session->get('ses_comp_fy_id');
	 $response =  $this->db->table($cmptaxcatm_tbl)->where('cmp_tax_cat_id',$cmp_tax_cat_id)->where('comp_id',$this->company_id)->get()->getRowArray();
	 return  $response;
   } 
    public function cmpgstcatninfo($cmp_tax_cat_id){
	 $cmpgstcatn_tbl = $this->company_id.'_cmpgstcatn_'.$this->session->get('ses_comp_fy_id');
	 $response =  $this->db->table($cmpgstcatn_tbl)->where('cmp_tax_cat_id',$cmp_tax_cat_id)->where('comp_id',$this->company_id)->get()->getRowArray();
	 return  $response;
   }	
   
   function itemvalmst_info($item_id){
	$itemvalmst_tbl = $this->company_id.'_itemvalmst_'.$this->session->get('ses_comp_fy_id');
	$response =  $this->db->table($itemvalmst_tbl)->where('item_id',$item_id)->where('comp_id',$this->company_id)->get()->getRowArray();
	 return  $response;     
	   
	   
   }
   
    function get_item_taxinfo($item_id){
      $itemtaxmst_tbl = $this->company_id.'_itemtaxmst_'.$this->session->get('ses_comp_fy_id');
	   $tax_data       = $this->db->table($itemtaxmst_tbl)
	   					->where('comp_id',$this->company_id)
						->where('item_id',$item_id)
						->orderBy('cmp_tax_cat_id','DESC')						
	   					->get()->getRowArray();	
	   if($tax_data){			
			$tax_rates          = $this->cmpgstcatninfo($tax_data['cmp_tax_cat_id']);			
		  	$item_hsn_sac       = $tax_data['item_hsn_sac'];
			$tax_cat_id         = $tax_data['cmp_tax_cat_id'];
			$item_tax_igst_rate = $tax_rates['cmp_tax_cat_igst'] ?? 0;
			$item_tax_cess_rate = $tax_rates['cmp_tax_cat_cess'] ?? 0;
			$item_tax_wef       = $tax_rates['cmp_tax_cat_wef'] ?? '';			
			$tax_cat_cess_basis = $tax_rates['cmp_tax_cat_cess_basis'] ?? 1;
			$tax_short_code     = $tax_rates['cmp_tax_short_code'] ?? '';
			$item_supply_type   = $tax_data['item_supply_type'] ?? '';
			
		}else{
		    $item_hsn_sac       = '';
			$tax_cat_id         = '';
			$item_tax_igst_rate = 0;
			$item_tax_cess_rate = 0;
			$item_tax_wef       = '';
			$tax_cat_cess_basis = 1; 
			$tax_short_code        ='';
			$item_supply_type   ='';	
		    }	
  $final_array = array("item_hsn_sac"=>$item_hsn_sac,"tax_cat_id"=>$tax_cat_id,
                       "item_tax_igst_rate"=>$item_tax_igst_rate,"item_tax_cess_rate"=>$item_tax_cess_rate,
					   "item_tax_wef"=>$item_tax_wef,"tax_cat_cess_basis"=>$tax_cat_cess_basis,"supply_type"=>$item_supply_type,
					   "tax_short_code"=>$tax_short_code
);
     return $final_array;					   
	}
	
	function item_tax_history($item_id){
		$itemtaxmst_tbl = $this->company_id.'_itemtaxmst_'.$this->session->get('ses_comp_fy_id');
	    $result = $this->db->table($itemtaxmst_tbl)->where('item_supply_type >','0')->where('item_id', $item_id)->orderBy('cmp_tax_cat_id','DESC')->get()->getResultArray();
		$final_result= array();
		if($result){
			foreach($result as $row){
				$cmp_tax_cat_id  = $row['cmp_tax_cat_id'];
				$cmptaxcatm_info = $this->cmptaxcatminfo($cmp_tax_cat_id);
				$cmpgstcatn_info = $this->cmpgstcatninfo($cmp_tax_cat_id);
				
				$row['cmptaxcatm_info'] = $cmptaxcatm_info;
				$row['cmpgstcatn_info'] = $cmpgstcatn_info;				
				$final_result[]=$row;
			}
		}
	  return $final_result;		
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

	    		$item_unit_id = $data['item_unit'];
	    		$item_unit_info = $this->item_unit_info($item_unit_id);
				if(isset($item_unit_info))
    				$item_unit = $item_unit_info['item_unit'];
			    else{
					$item_unit_id = 0;
		    		$item_unit = 'No Unit';		
				}
				
				$get_narration_info = $this->get_voucher_narration_info($voucher_txn_id,'short',$value['txn_id']);

				$item_description = $get_narration_info['vch_short_narr'] ?? '';

				$data['item_txn_qty'] = $data['item_txn_qty'] != 0 ? $data['item_txn_qty'] : 1;
				

				   
				if($data['item_txn_drcr'] == 'd'){
					$l_items[] = [
	    				'item_id' 				=> $data['item_id'],
						'item_name' 			=> $data['item_name'],
						'item_unit' 			=> $item_unit,
						'item_unit_id' 			=> $item_unit_id,
						'item_qty' 				=> floatval($data['item_txn_qty']),
						'item_txn_drcr' 		=> $data['item_txn_drcr'],
						'item_amount' 			=> parseAmount($data['item_txn_amount'] * $fcy_rate),
						'description' 		    => $item_description,
						'item_price'			=> parseAmountPrice(($data['item_txn_amount']/$data['item_txn_qty']) * $fcy_rate,4),
	    			];
				}
				if($data['item_txn_drcr'] == 'c'){
					$r_items[] = [
	    				'item_id' 				=> $data['item_id'],
						'item_name' 			=> $data['item_name'],
						'item_unit' 			=> $item_unit,
						'item_unit_id' 			=> $item_unit_id,
						'item_qty' 				=> floatval($data['item_txn_qty']),
						'item_txn_drcr' 		=> $data['item_txn_drcr'],
						'item_amount' 			=> parseAmount($data['item_txn_amount'] * $fcy_rate),
						'description' 		    => $item_description,
						'item_price'			=> parseAmountPrice(($data['item_txn_amount']/$data['item_txn_qty']) * $fcy_rate,4),
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

	    		$item_unit_id = $data['item_unit'];
	    		$item_unit_info = $this->item_unit_info($item_unit_id);
	    		if($item_unit_info){
	    			$item_unit = $item_unit_info['item_unit'];
	    		}
    			else{
		    		$item_unit_id = 0;
		    		$item_unit = 'No Unit';
		    	}

				$description = $get_narration_info['vch_short_narr'] ?? '';

				$data['item_txn_qty'] = $data['item_txn_qty'] != 0 ? $data['item_txn_qty'] : 1;
				
	    		$result[] = [
	    				'item_id' 				=> $data['item_id'],
						'item_name' 			=> $data['item_name'],
						'mat_cent_id' 			=> $data['mat_cent_id'],
						'item_unit' 			=> $item_unit,
						'item_unit_id' 			=> $item_unit_id,
						'item_qty' 				=> floatval($data['item_txn_qty']),
						'item_txn_drcr' 		=> $data['item_txn_drcr'],
						'item_amount' 			=> parseAmount($data['item_txn_amount'] * $fcy_rate),
						'description' 		    => $description,
						'item_price'			=> parseAmount(($data['item_txn_amount']/$data['item_txn_qty']) * $fcy_rate),
	    			];
	    	}
    	}
    	return $result;
    }

    function get_account_transactions_oth($voucher_txn_id)
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
					
					$adress_info    = $this->get_account_adrs_info($value['master_id']);
					 if($adress_info){
					   $acc_country   =  $adress_info['acc_country'];
					   $acc_state     =  $adress_info['acc_state'];
					   $state_info    =  $this->get_state_info($acc_country,$acc_state);
					   if($state_info)
						$state_code   = $state_info['state_code'];
					   else
						$state_code   = '4';
					   
					 }
					  else{
						$state_code    = "4";
						$acc_country   = "1";
						$acc_state     = "31";
					  }
					  
					$itemtaxmst_tbl = $this->company_id.'_acctaxmstn_'.$this->session->get('ses_comp_fy_id');
	        $tax_data       = $this->db->table($itemtaxmst_tbl)
								->where('comp_id',$this->company_id)
								->where('acc_id',$value['master_id'])
								->orderBy('cmp_tax_cat_id','DESC')						
								->get()->getRowArray();
		  if($tax_data){			
			$tax_rates          = $this->cmpgstcatninfo($tax_data['cmp_tax_cat_id']);			
		  	$item_hsn_sac       = $tax_data['acc_hsn_sac'];
			$tax_cat_id         = $tax_data['cmp_tax_cat_id'];
			$acc_supply_type    = $tax_data['acc_supply_type'] ?? '';
			if($tax_rates){
			  $item_tax_igst_rate = $tax_rates['cmp_tax_cat_igst'];
			  $item_tax_cess_rate = $tax_rates['cmp_tax_cat_cess'];
			  $item_tax_wef       = $tax_rates['cmp_tax_cat_wef'];
			}
			else{
			  $item_tax_igst_rate = 0;
			  $item_tax_cess_rate = 0;
			  $item_tax_wef       = 0;	
			  $acc_supply_type    = '';
			}
			
		   }else{
		    $item_hsn_sac       = '';
			$tax_cat_id         = '';
			$item_tax_igst_rate = 0;
			$item_tax_cess_rate = 0;
			$item_tax_wef       = '';
			$acc_supply_type    = '';	
		    }
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
					$memo_amount = $this->get_memoamount_sale_txn($voucher_txn_id,'acc',$data['acc_id']);
					$taxinc_amount  = $this->get_taxamount_sale_txn($voucher_txn_id,'acc',$value['txn_id']);
					
					if($voucher_info['currency_id']>1){
			        $amountfc = ($data['acc_oth_txn_fcy']==0)?parseAmount($data['acc_txn_amount'] * $get_fcrates):$data['acc_oth_txn_fcy'];
				   }else 
				    $amountfc = 0;
	    		
		    		$result[] = [
	    				'acc_id' 	  => $data['acc_id'],
						'account_name'=> $data['acc_name'],
						'description' => $acc_description,
						'amount' 	  => parseAmount($data['acc_oth_txn_amount'] * $fcy_rate),
						'is_cc'		  => $is_cc,
						'is_sundry'   => '0',
						'is_acc'      => '1',
						'is_bbb'      => 0,
						'is_cash'	  => 0,
						'acc_state'   => ($acc_state >0)?$acc_state:'31',
						'acc_country' => ($acc_country >0)?$acc_country:'1',
						'state_code'  => ($state_code>0)?$state_code:'4',
						'item_hsn_sac'=> $item_hsn_sac,
						'tax_cat_id'  => $tax_cat_id,
						'igst_rate'   => $item_tax_igst_rate,
						'cess_rate'   => $item_tax_cess_rate,
						'wef'         => $item_tax_wef,
						'supply_type' => $acc_supply_type,
						'memo_amt'    => $memo_amount,
						'amounttxs'   => $taxinc_amount,
						'amountfc'    => $amountfc
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
	    							if($data['acc_txn_amount'] == parseAmount($item_account_array[$value['master_id']]['amount'])){
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
										'is_cc'					=> $is_cc,
										'is_bbb'				=> $is_bbb,
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

		    			$is_cc = 0;
              if(in_array($account['acc_grp_id'], $cc_groups) || in_array($account['acc_grp_parent_id'], [7,11,13])){
                 $is_cc = 1;
              }

			    		if($data['sundry_txn_drcr'] == 'd'){

				    		$l_accounts[] = [
				    				'acc_id' 			=> $data['bill_sundry_id'],
										'acc_name' 			=> $data['bill_sundry_name'],
										'acc_type' 			=> 'bsd',
										'acc_amount' 		=> $data['sundry_txn_amount'],
										'is_cc'					=> $is_cc,
										'is_bbb'				=> 0,
				    			];
			    		}
			    		if($data['sundry_txn_drcr'] == 'c'){

				    		$r_accounts[] = [
				    				'acc_id' 			=> $data['bill_sundry_id'],
										'acc_name' 			=> $data['bill_sundry_name'],
										'acc_type' 			=> 'bsd',
										'acc_amount' 		=> $data['sundry_txn_amount'],
										'is_cc'					=> $is_cc,
										'is_bbb'				=> 0,
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
    
	function get_account_adrs_info($account_id){	 
       $acctaddmst_tbl = $this->company_id.'_acctaddmst_'.$this->session->get('ses_comp_fy_id');
       return $this->db->table($acctaddmst_tbl)->where('acc_id', $account_id)->get()->getRowArray();   	   
   }
   
   function show_states_lists($show_empty=''){
	   $final    = array();
	   if($show_empty=='')
	   $final['']   = 'Choose';
	   $response =  $this->aicountly_db->table('aicountly_stateslist_univdb')->orderBy('state_name')->get()->getResultArray();
	   foreach($response as $row){
	   		$state_code = sprintf( '%02d', $row['state_code'] );
	       $final[$state_code] = $row['state_name'].'('.$state_code.')';
	   }
	   return $final;
	   
     }
	 
  function show_eco_lists(){
	   $final    = array();
	   $final['']   = 'Not Applicable';
	   $acctgstmst_tbl = $this->company_id.'_acctgstmst_'.$this->session->get('ses_comp_fy_id');
       $acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
       
	   $builder  = $this->db->table($acctmaster_tbl.' actmst');
	   $builder->join($acctgstmst_tbl.' actgst','actgst.acc_id=actmst.acc_id','LEFT');
	   $builder->where('actgst.acc_eco',1);
	   $response = $builder->get()->getResultArray();
	   foreach($response as $row){
	   	   $final[$row['acc_id']] = ucwords($row['acc_name']);
	   }
	   return $final;
	   
     }	 
   
   function get_state_info($country_id,$state_id){
	   return $this->aicountly_db->table('aicountly_stateslist_univdb')->where('country_id',$country_id)->where('state_id',$state_id)->get()->getRowArray();	   		 	
    }
   
   function get_country_info($country_id){
	   return $this->aicountly_db->table('aicountly_countrylst_univdb')->where('countryid',$country_id)->get()->getRowArray();	   		 	
    }	 
	 
   function get_account_transactions($voucher_txn_id)
    {
	   	$cc_groups = $this->get_cc_groups();
	   	$fcy_rate = 1;
		$get_fcrates      = $this->get_fcrates($voucher_txn_id);
		
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
				
					$adress_info    = $this->get_account_adrs_info($value['master_id']);
					 if($adress_info){
					   $acc_country   =  $adress_info['acc_country'];
					   $acc_state     =  $adress_info['acc_state'];
					   $state_info    =  $this->get_state_info($acc_country,$acc_state);
					   if($state_info)
						$state_code   = $state_info['state_code'];
					   else
						$state_code   = '4';
					   
					 }
					  else{
						$state_code    = "4";
						$acc_country   = "1";
						$acc_state     = "31";
					  }
					
			$itemtaxmst_tbl = $this->company_id.'_acctaxmstn_'.$this->session->get('ses_comp_fy_id');
	        $tax_data       = $this->db->table($itemtaxmst_tbl)
								->where('comp_id',$this->company_id)
								->where('acc_id',$value['master_id'])
								->orderBy('cmp_tax_cat_id','DESC')						
								->get()->getRowArray();
		  if($tax_data){			
			$tax_rates          = $this->cmpgstcatninfo($tax_data['cmp_tax_cat_id']);			
		  	$item_hsn_sac       = $tax_data['acc_hsn_sac'];
			$tax_cat_id         = $tax_data['cmp_tax_cat_id'];
			$acc_supply_type    = $tax_data['acc_supply_type'] ?? '';
			if($tax_rates){
			  $item_tax_igst_rate = $tax_rates['cmp_tax_cat_igst'];
			  $item_tax_cess_rate = $tax_rates['cmp_tax_cat_cess'];
			  $item_tax_wef       = $tax_rates['cmp_tax_cat_wef'];
			}
			else{
			  $item_tax_igst_rate = 0;
			  $item_tax_cess_rate = 0;
			  $item_tax_wef       = 0;	
			  $acc_supply_type    = '';
			}
			
		   }else{
		    $item_hsn_sac       = '';
			$tax_cat_id         = '';
			$item_tax_igst_rate = 0;
			$item_tax_cess_rate = 0;
			$item_tax_wef       = '';
			$acc_supply_type    = '';	
		    }		
				
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
					$memo_amount    = $this->get_memoamount_sale_txn($voucher_txn_id,'acc',$data['acc_id']);
					$taxinc_amount  = $this->get_taxamount_sale_txn($voucher_txn_id,'acc',$value['txn_id']);
					
					if($voucher_info['currency_id']>1){					
					   $amountfc   = ($data['acc_txn_fcy']==0)?$data['acc_txn_amount'] * $get_fcrates:$data['acc_txn_fcy'];
					}
					else{
					   $amountfc=0;	
					 }
					
		    		$result[] = [
	    				'acc_id' 	  => $data['acc_id'],
						'account_name'=> $data['acc_name'],
						'description' => $acc_description,
						'amount' 	  => parseAmount($data['acc_txn_amount'] * $fcy_rate),
						'is_cc'		  => $is_cc,
						'is_sundry'   => '0',
						'is_acc'      => '1',
						'is_bbb'      => 0,
						'is_cash'	  => 0,
						'acc_state'   => ($acc_state >0)?$acc_state:'31',
						'acc_country' => ($acc_country >0)?$acc_country:'1',
						'state_code'  => ($state_code>0)?$state_code:'4',
						'item_hsn_sac'=> $item_hsn_sac,
						'tax_cat_id'  => $tax_cat_id,
						'igst_rate'   => $item_tax_igst_rate,
						'cess_rate'   => $item_tax_cess_rate,
						'wef'         => $item_tax_wef,
						'supply_type' => $acc_supply_type,
						'memo_amt'    => $memo_amount,
						'amounttxs'   => $taxinc_amount,
						'amountfc'    => $amountfc
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
		$get_fcrates      = $this->get_fcrates($voucher_txn_id);

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
			$itemtaxmst_tbl = $this->company_id.'_acctaxmstn_'.$this->session->get('ses_comp_fy_id');
	        $tax_data       = $this->db->table($itemtaxmst_tbl)
								->where('comp_id',$this->company_id)
								->where('acc_id',$value['master_id'])
								->orderBy('cmp_tax_cat_id','DESC')						
								->get()->getRowArray();
		  if($tax_data){			
			$tax_rates          = $this->cmpgstcatninfo($tax_data['cmp_tax_cat_id']);			
		  	$item_hsn_sac       = $tax_data['acc_hsn_sac'];
			$tax_cat_id         = $tax_data['cmp_tax_cat_id'];
			$supply_type        = $tax_data['acc_supply_type'] ?? '';
			
			if($tax_rates){
			  $igst_rate = $tax_rates['cmp_tax_cat_igst'];
			  $cess_rate = $tax_rates['cmp_tax_cat_cess'];
			  $wef       = $tax_rates['cmp_tax_cat_wef'];
			  $cess_basis         = $tax_rates['cmp_tax_cat_cess_basis'] ?? 1;
			}
			else{
			  $igst_rate   = 0;
			  $cess_rate   = 0;
			  $wef         = 0;	
			  $supply_type = '';
			  $cess_basis  =''; 
			}
			
		   }else{
		    $item_hsn_sac = '';
			$tax_cat_id   = '';
			$igst_rate    = 0;
			$cess_rate    = 0;
			$wef          = '';
			$supply_type  = '';
			$cess_basis   =''; 			
		    }
			

    		if($value['master_id_type'] == 'acc')
    		{
    			$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
	    		if ($this->db->tableExists($acc_txn_tbl)) {
				$builder = $this->db->table($acc_txn_tbl);
		    	$builder->where('voucher_txn_id', $voucher_txn_id);
				$builder->where('bo_id', $this->session->get('ses_boid'));
		    	$builder->where('acc_id', $value['master_id']);
		    	$builder->where('txn_id', $value['txn_id']);
		    	$data = $builder->get()->getRowArray();
		    	if($data){
	    			$account = $this->get_account_info($value['master_id']);
					if($account){
						$data['acc_name'] = $account['acc_name'];
						$acc_short_code   = $account['acc_short_code'];
					}else{
						$data['acc_name'] = 'N/A';
						$acc_short_code   ='N/A';
						$account['acc_grp_id']=0;
						$account['acc_grp_parent_id']=0;
					}

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
						if($voucher_info['currency_id']>1){					
					      $creditfc   = ($data['acc_txn_fcy']==0)?$data['acc_txn_amount'] * $get_fcrates:$data['acc_txn_fcy'];
					     }
					     else{
						  $creditfc=0;	
					     }
						
						//$creditfc= ($voucher_info['currency_id']>1)?parseAmount($data['acc_txn_amount'] * $get_fcrates):0;
	                    $debit = '';
						$debitfc ='';
	                    $acc_txn_drcr ='C';
	                }
	                else{
	                    $debit    = parseAmount($data["acc_txn_amount"] * $fcy_rate);
						if($voucher_info['currency_id']>1){					
					      $debitfc   = ($data['acc_txn_fcy']==0)?$data['acc_txn_amount'] * $get_fcrates:$data['acc_txn_fcy'];
					     }
					     else{
						  $debitfc=0;	
					     }						
						$credit   = '';
						$creditfc = '';
	                    $acc_txn_drcr ='D';
	                }
					
		    		$result[] = [
		    				'account_id' 		=> $data['acc_id'],
							'account_name' 		=> $data['acc_name'],
							'acc_type' 			=> 'acc',
							'description' 		=> $acc_description,
							'credit' 			=> $credit,
							'debit' 			=> $debit,
							'creditfc' 			=> $creditfc,
							'debitfc' 			=> $debitfc,
							'drcr'				=> $acc_txn_drcr,
							'is_cc'				=> $is_cc,
							'is_bbb'			=> $is_bbb,
							'is_cash'	  		=> $is_cash,
							'igst_rate'         => $igst_rate,
							'cess_rate'         => $cess_rate,
							'item_hsn_sac'      => $item_hsn_sac,
							'tax_cat_id'        => $tax_cat_id,
							'supply_type'       => $supply_type,
							'acc_short_code'    => $acc_short_code,
							'cannotselected'    => '',
							'cess_basis'        => $cess_basis
		    			];	
			} }
    		}
    		
	    	if($value['master_id_type'] == 'bsd')
    		{
    			$sundry_txn_tbl =  $this->company_id.'_sundrytxnn_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
	    		if ($this->db->tableExists($sundry_txn_tbl)) {
				$builder = $this->db->table($sundry_txn_tbl);
		    	$builder->where('voucher_txn_id', $voucher_txn_id);
				$builder->where('bo_id', $this->session->get('ses_boid'));
		    	$builder->where('bill_sundry_id', $value['master_id']);
		    	$builder->where('txn_id', $value['txn_id']);
		    	$data = $builder->get()->getRowArray();		    	
		    	if($data){
		    		$account = $this->get_billsundry_info($value['master_id']);
	    			$data['bill_sundry_name'] = $account['bill_sundry_name'];

	    			$is_cc = 0;
	                if(in_array($account['acc_grp_id'], $cc_groups) || in_array($account['acc_grp_parent_id'], [7,11,13])){
	                   $is_cc = 1;
	                }


					$get_narration_info = $this->get_voucher_narration_info($voucher_txn_id,'short',$value['txn_id']);
					if($get_narration_info){
						$acc_description = $get_narration_info['vch_short_narr'];
					}else{
						$acc_description = '';
					}

	    			if($data['sundry_txn_drcr'] == 'c'){
	                    $credit = parseAmount($data["sundry_txn_amount"] * $fcy_rate);
						if($voucher_info['currency_id']>1){					
					      $creditfc   = ($data['sundry_txn_fcy']==0)?$data['sundry_txn_amount'] * $get_fcrates:$data['sundry_txn_fcy'];
					     }
					     else{
						  $creditfc=0;	
					     }
						
						$debit = '';
						$debitfc = '';
	                    $sundry_txn_drcr ='C';
	                }
	                else{
	                    $debit = parseAmount($data["sundry_txn_amount"] * $fcy_rate);
						if($voucher_info['currency_id']>1){					
					      $debitfc   = ($data['sundry_txn_fcy']==0)?$data['sundry_txn_amount'] * $get_fcrates:$data['sundry_txn_fcy'];
					     }
					     else{
						  $debitfc=0;	
					     }
						
						$credit = '';
						$creditfc = '';
	                    $sundry_txn_drcr ='D';
	                }



	                $result[] = [
	    				'account_id' 		=> $data['bill_sundry_id'],
						'account_name' 		=> $data['bill_sundry_name'],
						'acc_type' 			=> 'bsd',
						'description' 		=> $acc_description,
						'credit' 			=> $credit,
						'debit' 			=> $debit,
						'creditfc' 			=> $creditfc,
						'debitfc' 			=> $debitfc,
						'drcr'				=> $sundry_txn_drcr,
						'is_cc'				=> $is_cc,
						'is_bbb'			=> 0,
						'is_cash'	  		=> 0,
						'igst_rate'         => $igst_rate,
						'cess_rate'         => $cess_rate,
						'item_hsn_sac'      => $item_hsn_sac,
						'tax_cat_id'        => $tax_cat_id,
						'supply_type'       => $supply_type,
						'acc_short_code'    => '',
						'cannotselected'    => '',
						'cess_basis'        => $cess_basis
	    			];
		    	} }
    		}
    	}
    	return $result;
    }

    function get_all_account_oth_transactions($voucher_txn_id) //voucher contra payment receipt journal
    {
    	$fcy_rate = 1;
		$get_fcrates      = $this->get_fcrates($voucher_txn_id);

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
					$acc_short_code   = $account['acc_short_code'];

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
						$creditfc= ($voucher_info['currency_id']>1)?parseAmount($data['acc_oth_txn_amount'] * $get_fcrates):0;
	                    $debit = '';
						$debitfc ='';
	                    $acc_txn_drcr ='C';
	                }
	                else{
	                    $debit   = parseAmount($data["acc_oth_txn_amount"] * $fcy_rate);
						$debitfc = ($voucher_info['currency_id']>1)?parseAmount($data['acc_oth_txn_amount'] * $get_fcrates):0;
	                    $credit  = '';
						$creditfc = '';
	                    $acc_txn_drcr ='D';
	                }
					
		    		$result[] = [
		    				'account_id' 		=> $data['acc_id'],
							'account_name' 		=> $data['acc_name'],
							'acc_type' 			=> 'acc',
							'description' 		=> $acc_description,
							'credit' 			=> $credit,
							'debit' 			=> $debit,
							'creditfc' 			=> $creditfc,
							'debitfc' 			=> $debitfc,
							'drcr'				=> $acc_txn_drcr,
							'acc_short_code'    => $acc_short_code,
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
		$get_fcrates = $this->get_fcrates($voucher_txn_id);
    	$cc_groups = $this->get_cc_groups();

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
    		$builder     = $this->db->table($acc_oth_tbl);
	    	$builder->where('voucher_txn_id', $voucher_txn_id);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
	    	$builder->where('acc_id', $value['master_id']);
	    	$builder->where('txn_id', $value['txn_id']);
	    	$data = $builder->get()->getRowArray();
	    	if($data){
    			$account = $this->get_billsundry_info($value['master_id']);
    			$data['bill_sundry_name'] = $account['bill_sundry_name'];
				// Get Item Last tax WEF 
	            $bdstaxmstn_tbl = $this->company_id.'_bdstaxmstn_'.$this->session->get('ses_comp_fy_id');
	            $tax_data       = $this->db->table($bdstaxmstn_tbl)
									->select('bill_hsn_sac,bill_tax_account,cmp_tax_cat_id,bill_input_output')
									->where('bill_sundry_id',$value['master_id'])
									->orderBy('cmp_tax_cat_id','DESC')
									->limit(1)
									->get()->getRowArray();
		if($tax_data){			
			// get gst tax rates from  cmpgstcatn
			$tax_rates          = $this->cmpgstcatninfo($tax_data['cmp_tax_cat_id']);			
		  	$item_hsn_sac       = $tax_data['bill_hsn_sac'];
			$tax_cat_id         = $tax_data['cmp_tax_cat_id'];
			
			if($tax_data['bill_tax_account']=="1")
				$is_tax_account     = "1"; 	
			 else 
                $is_tax_account     = "0"; 				 
			if($tax_rates){
				$item_tax_igst_rate = $tax_rates['cmp_tax_cat_igst'];
				$item_tax_cess_rate = $tax_rates['cmp_tax_cat_cess'];
				$item_tax_wef       = $tax_rates['cmp_tax_cat_wef'];
			}
			else{
				$item_tax_igst_rate = 0;
				$item_tax_cess_rate = 0;
				$item_tax_wef       = '';				
			}
			if(isset($tax_data['bill_input_output']))
				$bill_input_output  = $tax_data['bill_input_output'];
		    else
				$bill_input_output  = '';
		    if(isset($tax_data['bill_supply_type']))
				$bill_supply_type   = $tax_data['bill_supply_type'];
		    else
				$bill_supply_type   = 0;	
		   }else{
		    $item_hsn_sac       = '';
			$tax_cat_id         = '';
			$item_tax_igst_rate = 0;
			$item_tax_cess_rate = 0;
			$item_tax_wef       = '';
            $bill_input_output  = 0;	
			$bill_supply_type   = 0;
			$is_tax_account     = "0"; 		
		   }	   
		   
		     $memo_amount = $this->get_memoamount_sale_txn($voucher_txn_id,'bsd',$value['master_id']);
		     $account     = $this->get_billsundry_info($value['master_id']);				 
    		 if($account){				
					$data['bill_sundry_name'] = $account['bill_sundry_name'];
	                $data['sundry_nature']    = $account['sundry_nature'];
	                $acc_grp_id               = $account['acc_grp_id'];
	                $acc_grp_parent_id        = $account['acc_grp_parent_id'];
				}
			 else{
					$data['bill_sundry_name'] = '';
	                $data['sundry_nature']    = '';
	                $acc_grp_id               = 0;
	                $acc_grp_parent_id        = 0;	
				}

				$is_cc = 0;
                if(in_array($acc_grp_id, $cc_groups) || in_array($acc_grp_parent_id, [7,11,13])){
                   $is_cc = 1;
                }
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
				if($voucher_info['currency_id'] >1){
				  $billsundry_amountfc = ($data['acc_oth_txn_fcy']==0)?parseAmount($amount * $get_fcrates):$data['acc_oth_txn_fcy'];
				}
				else 
				  $billsundry_amountfc =0;	
				
	    		$result[] = [
	    				'billsundry_id'     => $data['acc_id'],
						'billsundry_name'   => $data['bill_sundry_name'],
						'value' 		    => $data['bill_sundry_name'],
						'label' 		    => $data['bill_sundry_name'],
						'billsundry_amount' => parseAmount($amount * $fcy_rate),
						'account_id'    => $data['acc_id'],
               			'acc_id'        => $data['acc_id'],
						'is_sundry'     => '1',
						'is_acc'        => '0',
						'is_bbb'        => 0,
						'is_cc'	        => $is_cc,						
						'tx_ct_id'	    => $tax_cat_id,
						'bl_sply_tpe'	=> $bill_supply_type,
						'bl_hsn_sac'	=> $item_hsn_sac,
						'bl_tx_igst_rte'=> $item_tax_igst_rate,
						'bl_tax_rate'   => $item_tax_igst_rate,
						'b_tx_cess_rte'	=> $item_tax_cess_rate,
						'bl_tx_wef'	    => $item_tax_wef,
						'bl_ipt_ott'	=> $bill_input_output,
						'bl_nature'     => $data['sundry_nature'],
						'is_tax_account'=> $is_tax_account,
						'fcrates'       => $get_fcrates,
						'billsundry_memoamnt' => parseAmount($memo_amount),
						'billsundry_amountfc' => $billsundry_amountfc
	    			];
	    	}
    	}
    	return $result;
    }

    function get_sundry_transactions($voucher_txn_id, $inverse = false)
    {
    	$fcy_rate = 1;
		$get_fcrates = $this->get_fcrates($voucher_txn_id);
    	$cc_groups = $this->get_cc_groups();

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
				
				 // Get Item Last tax WEF 
	   $bdstaxmstn_tbl = $this->company_id.'_bdstaxmstn_'.$this->session->get('ses_comp_fy_id');
	   $tax_data       = $this->db->table($bdstaxmstn_tbl)
	   					->select('bill_hsn_sac,bill_tax_account,cmp_tax_cat_id,bill_input_output')
	   					->where('bill_sundry_id',$value['master_id'])
						->orderBy('cmp_tax_cat_id','DESC')
						->limit(1)
	   					->get()->getRowArray();
		if($tax_data){			
			// get gst tax rates from  cmpgstcatn
			$tax_rates          = $this->cmpgstcatninfo($tax_data['cmp_tax_cat_id']);			
		  	$item_hsn_sac       = $tax_data['bill_hsn_sac'];
			$tax_cat_id         = $tax_data['cmp_tax_cat_id'];
			
			if($tax_data['bill_tax_account']=="1")
				$is_tax_account     = "1"; 	
			 else 
                $is_tax_account     = "0"; 				 
			if($tax_rates){
				$item_tax_igst_rate = $tax_rates['cmp_tax_cat_igst'];
				$item_tax_cess_rate = $tax_rates['cmp_tax_cat_cess'];
				$item_tax_wef       = $tax_rates['cmp_tax_cat_wef'];
				
			}
			else{
				$item_tax_igst_rate = 0;
				$item_tax_cess_rate = 0;
				$item_tax_wef       = '';				
			}
			if(isset($tax_data['bill_input_output']))
				$bill_input_output  = $tax_data['bill_input_output'];
		    else
				$bill_input_output  = '';
		    if(isset($tax_data['bill_supply_type']))
				$bill_supply_type   = $tax_data['bill_supply_type'];
		    else
				$bill_supply_type   = 0;	
		   }else{
		    $item_hsn_sac       = '';
			$tax_cat_id         = '';
			$item_tax_igst_rate = 0;
			$item_tax_cess_rate = 0;
			$item_tax_wef       = '';
            $bill_input_output  = 0;	
			$bill_supply_type   = 0;
			$is_tax_account     = "0"; 		
		   }	   
		   
		     $memo_amount= $this->get_memoamount_sale_txn($voucher_txn_id,'bsd',$value['master_id']);
		     $account = $this->get_billsundry_info($value['master_id']);				 
    			if($account){				
					$data['bill_sundry_name'] = $account['bill_sundry_name'];
	                $data['sundry_nature'] = $account['sundry_nature'];
	                $acc_grp_id = $account['acc_grp_id'];
	                $acc_grp_parent_id = $account['acc_grp_parent_id'];
				}
				else{
					$data['bill_sundry_name'] = '';
	                $data['sundry_nature']    = '';
	                $acc_grp_id = 0;
	                $acc_grp_parent_id = 0;	
				}

				$is_cc = 0;
                if(in_array($acc_grp_id, $cc_groups) || in_array($acc_grp_parent_id, [7,11,13])){
                   $is_cc = 1;
                }
    			
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
    			if($voucher_info['currency_id']>1)
				$billsundry_amountfc = ($data['sundry_txn_fcy']==0)?parseAmount($amount * $get_fcrates):$data['sundry_txn_fcy'];
			    else 
				$billsundry_amountfc =0;	
	    		$result[] = [
	    				'billsundry_id' 		=> $data['bill_sundry_id'],
						'billsundry_name' 		=> $data['bill_sundry_name'],
						'value' 		=> $data['bill_sundry_name'],
						'label' 		=> $data['bill_sundry_name'],
						'billsundry_rate' 		=> $data['sundry_tag_rate'],
						'billsundry_amount' 	=> parseAmount($amount * $fcy_rate),
						'account_id'    => $data['bill_sundry_id'],
               			'acc_id'        => $data['bill_sundry_id'],
						'is_sundry'     => '1',
						'is_acc'        => '0',
						'is_bbb'        => 0,
						'is_cc'	        => $is_cc,						
						'tx_ct_id'	    => $tax_cat_id,
						'bl_sply_tpe'	=> $bill_supply_type,
						'bl_hsn_sac'	=> $item_hsn_sac,
						'bl_tx_igst_rte'=> $item_tax_igst_rate,
						'bl_tax_rate'   => $item_tax_igst_rate,
						'b_tx_cess_rte'	=> $item_tax_cess_rate,
						'bl_tx_wef'	    => $item_tax_wef,
						'bl_ipt_ott'	=> $bill_input_output,
						'bl_nature'     => $data['sundry_nature'],
						'is_tax_account'=> $is_tax_account,
						'fcrates'       => $get_fcrates,
						'billsundry_memoamnt' => parseAmount($memo_amount),
						'billsundry_amountfc' => $billsundry_amountfc
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

    function fetch_voucherconso_info($voucher_txn_id){	 
	  $comp_voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	   $comp_vhtxnconso_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	  
	      // get voucher last entry date
	      $builder = $this->db->table($comp_vhtxnconso_tbl);
	      $builder->where('voucher_txn_id', $voucher_txn_id);
	      $builder->orderBy('voucher_txn_id','DESC');
	      $builder->limit(1);
	      $response = $builder->get()->getRowArray();   
	       if($response){
			 $voucher_type_id = $response['voucher_type_id'];  
			 $data = $this->db->table($comp_voucher_type_tbl)->where('voucher_type_id', $voucher_type_id)->where('comp_id', $this->company_id)->get()->getRowArray(); 
	   
			   
	        $response['comp_vch_type'] = $data['comp_vch_type'];   
	       }
	  return $response;
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

 	function get_voucher_long_narration($voucher_txn_id)
 	{
 		$narration = '';

 		$comp_txn_master_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	   	$comp_txn = $this->db->table($comp_txn_master_tbl)
	   					->where('voucher_txn_id', $voucher_txn_id)
	   					->where('master_id', 0)
	   					->where('master_id_type', 'nrr')
	   					->get()->getRowArray();

	   	if($comp_txn){
	   		$txn_id = $comp_txn['txn_id'];

	   		$long_narr_tbl = $this->company_id.'_long_narrn_'.$this->session->get('ses_comp_fy_id');
	   		$narr = $this->db->table($long_narr_tbl)
	   					->where('txn_id',$txn_id)
	   					->where('vch_txn_id',$voucher_txn_id)
	   					->get()->getRowArray();
	   		if($narr){
	   			$narration = $narr['vch_narr'];
	   		}
	   	}

	   	return $narration;
 	}
		
  function get_voucher_narration_info($voucher_txn_id,$narr_type,$txn_id){
	 if($narr_type=='long'){
		 // get txn id from comptxnmst table 
		$comptxnmst_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id'); 
		$txn_row =  $this->db->table($comptxnmst_tbl)->where('voucher_txn_id',$voucher_txn_id)->where('comp_id',$this->company_id)->where('master_id','0')->where('master_id_type','nrr')->get()->getRowArray(); 
		 
		$txn_id  = $txn_row['txn_id'] ?? 0;
        $long_narr_tbl = $this->company_id.'_long_narrn_'.$this->session->get('ses_comp_fy_id');
		return $this->db->table($long_narr_tbl)->where('txn_id',$txn_id)->where('vch_txn_id',$voucher_txn_id)->get()->getRowArray();
		
	 }
	else  if($narr_type=='short'){
        $short_narr_tbl = $this->company_id.'_short_narr_'.$this->session->get('ses_comp_fy_id');
		return $this->db->table($short_narr_tbl)->where('txn_id',$txn_id)->where('vch_txn_id',$voucher_txn_id)->get()->getRowArray();
	}		
	  
  }		
  
  function save_voucher_narration($vch_txn_id,$txn_id,$narr_type,$narration_txt){
	  if(empty($narration_txt))
		  $narration_txt='N/A';
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
  
  public function check_sale_taxinc($voucher_txn_id){
	$bo_id          = $this->bo_id; 
	$vchaddinfo_tbl = $this->company_id.'_vchaddinfo_'.$this->session->get('ses_comp_fy_id'); 
	$txn_row        = $this->db->table($vchaddinfo_tbl)->where('voucher_txn_id',$voucher_txn_id)->get()->getRowArray(); 
	//echo $this->db->GetLastQuery();
	if($txn_row)
		return "1";
     else
     return "0";
   }
   
  public function check_sale_memoentry($voucher_txn_id){
	$bo_id          = $this->bo_id; 
	$memotxnnnn_tbl = $this->company_id.'_memotxnnnn_'.$this->session->get('ses_comp_fy_id'); 
	$txn_row        = $this->db->table($memotxnnnn_tbl)->where('voucher_txn_id',$voucher_txn_id)->where('comp_id',$this->company_id)->where('bo_id',$bo_id)->get()->getRowArray(); 
	if($txn_row)
		return "1";
     else
     return "0";
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
	
	function delete_taxinc_txn_data($voucher_txn_id,$type)
    {
         $vchaddinfo_tbl =  $this->company_id.'_vchaddinfo_'.$this->session->get('ses_comp_fy_id');
		 $this->db->table($vchaddinfo_tbl)->where('txn_type',$type)->where('voucher_txn_id',$voucher_txn_id)->delete();
    }
	
	function delete_memo_txn_data($voucher_txn_id)
    {
         $memotxnnnn_tbl =  $this->company_id.'_memotxnnnn_'.$this->session->get('ses_comp_fy_id');
		 $this->db->table($memotxnnnn_tbl)->where('voucher_txn_id',$voucher_txn_id)->delete();
		
    }
    function delete_valuation_tbls_txn_data($voucher_txn_id)
    {  
    	$table1 = $this->company_id.'_itemrepall_'.$this->session->get('ses_comp_fy_id');
		$table2 = $this->company_id.'_itemrepmcn_'.$this->session->get('ses_comp_fy_id');
		$table3 = $this->company_id.'_itemrepgrp_'.$this->session->get('ses_comp_fy_id');
		$table4 = $this->company_id.'_itemrepcat_'.$this->session->get('ses_comp_fy_id');
		$table5 = $this->company_id.'_itemrepprj_'.$this->session->get('ses_comp_fy_id');
		$table6 = $this->company_id.'_itemrepbon_'.$this->session->get('ses_comp_fy_id');
		$table7 = $this->company_id.'_itemrepbat_'.$this->session->get('ses_comp_fy_id');
		$table8 = $this->company_id.'_buffercalc_'.$this->session->get('ses_comp_fy_id');
        $table9 = $this->company_id.'_buffrevlog_'.$this->session->get('ses_comp_fy_id');
        $table10 = $this->company_id.'_bufflogrec_'.$this->session->get('ses_comp_fy_id');
        		
		$this->db->table($table1)->where('voucher_txn_id',$voucher_txn_id)->delete();
		$this->db->table($table2)->where('voucher_txn_id',$voucher_txn_id)->delete();
		$this->db->table($table3)->where('voucher_txn_id',$voucher_txn_id)->delete();
		$this->db->table($table4)->where('voucher_txn_id',$voucher_txn_id)->delete();
		$this->db->table($table5)->where('voucher_txn_id',$voucher_txn_id)->delete();
		$this->db->table($table6)->where('voucher_txn_id',$voucher_txn_id)->delete();
		$this->db->table($table7)->where('voucher_txn_id',$voucher_txn_id)->delete();
		$this->db->table($table8)->where('voucher_txn_id',$voucher_txn_id)->delete();
		$this->db->table($table9)->where('voucher_txn_id',$voucher_txn_id)->delete();
        $this->db->table($table10)->where('voucher_txn_id',$voucher_txn_id)->delete();
	}
    
     function delete_itm_txn_data($item_id,$voucher_txn_id)
    {
         $itemtxnnnn_tbl =  $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		 if($this->session->get('ses_boid')!='')
		 	$this->db->table($itemtxnnnn_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('voucher_txn_id',$voucher_txn_id)->delete();
		 else 
			 $this->db->table($itemtxnnnn_tbl)->where('voucher_txn_id',$voucher_txn_id)->delete();

		$itemtxnval_tbl =  $this->company_id.'_itemtxnval_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		$this->db->table($itemtxnval_tbl)->where('voucher_txn_id',$voucher_txn_id)->delete();
    }
    
   
    
    function delete_sundry_txn_data($bsd,$voucher_txn_id)
    {
         $sundry_txn_tbl =  $this->company_id.'_sundrytxnn_'.$bsd.'_'.$this->session->get('ses_comp_fy_id');
		 $this->db->table($sundry_txn_tbl)->where('voucher_txn_id',$voucher_txn_id)->delete();

		 $this->update_bill_sundry_balance($bsd);
    }


    function update_account_balance($acc_id,$VchOthrBo=0) 
    {
        $bal=0; //include opening balance
        $accoppybal_tbl = $this->company_id.'_accoppybal_'.$this->session->get('ses_comp_fy_id');
        if($VchOthrBo==0)
          $bo_id = $this->bo_id;
          else
          $bo_id = $VchOthrBo;
		
		$balance = $this->db->table($accoppybal_tbl)->where('bo_id', $bo_id)->where('acc_id', $acc_id)->get()->getRowArray();
		if($balance){
			$bal  = $balance['acc_op_bal'];
			if($bal=='' || $bal=='0')
			 $bal=0;
		}

        $acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$acc_id.'_'.$this->session->get('ses_comp_fy_id');
		$this->db->query('SET @bal = '.$bal.';');

		$this->db->query('UPDATE '.$acc_txn_tbl.' SET acc_bal = CASE WHEN acc_txn_drcr = "c" THEN @bal:=@bal - acc_txn_amount WHEN acc_txn_drcr = "d" THEN @bal:=@bal + acc_txn_amount ELSE 0 END where acc_id = '.$acc_id.' and bo_id = '.$bo_id.' and voucher_txn_id >0  order by acc_txn_date,voucher_txn_id,acc_txn_id;');
		
    }

    function update_account_balance2($acc_id) //delete it
    {

		$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$acc_id.'_'.$this->session->get('ses_comp_fy_id');
		$result = $this->db->table($acc_txn_tbl)
        					->where('acc_id', $acc_id)
        					->orderBy('acc_txn_date', 'asc')
        					->orderBy('acc_txn_id', 'asc')
        					->get()->getResultArray();
							
		 if($result){
	        foreach($result as $key2 => $value){
	        	
	        	$vhtxnconso_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	            $exists = $this->db->table($vhtxnconso_tbl)
				            		->where('voucher_txn_id', $value['voucher_txn_id'])
				            		->get()->getRowArray();
				if(!$exists){
					$this->delete_voucher($value['voucher_txn_id']);
					$this->delete_acc_txn_data($acc_id,$value['voucher_txn_id']);
				}


	        } 
    	}  
    }

    function delete_voucher($voucher_txn_id)
    {
    	$data = $this->get_comp_txn_data($voucher_txn_id);
        foreach ($data as $key => $value) {
                if($value['master_id_type'] == 'acc'){
                        $this->delete_acc_txn_data($value['master_id'],$voucher_txn_id);
                }
                if($value['master_id_type'] == 'itm'){
                        $this->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
                }
                if($value['master_id_type'] == 'bsd'){
                        $this->delete_sundry_txn_data($value['master_id'],$voucher_txn_id);
                }
        }
        $this->delete_acc_oth_data($voucher_txn_id);
        $this->delete_acc_crsref_data($voucher_txn_id);
        $this->delete_itm_oth_data($voucher_txn_id);
        $this->delete_comp_txn_data($voucher_txn_id);
        $this->delete_voucher_conso_data($voucher_txn_id);

        $this->delete_cc_txn($voucher_txn_id);
        $this->delete_bills_txn($voucher_txn_id);                 
        $this->delete_all_narrations($voucher_txn_id);
    }
    

    function update_account_memo_balance($acc_id,$type='acc')
    {
        $bal=0; //include opening balance

		$acc_txn_tbl = $this->company_id.'_memotxnnnn_'.$this->session->get('ses_comp_fy_id');
		$this->db->query('SET @bal = '.$bal.';');
		$this->db->query('UPDATE '.$acc_txn_tbl.' SET acc_bal = CASE WHEN acc_txn_drcr = "c" THEN @bal:=@bal - acc_txn_amount WHEN acc_txn_drcr = "d" THEN @bal:=@bal + acc_txn_amount ELSE 0 END where acc_id = "'.$acc_id.'" AND bo_id = "'.$this->bo_id.'" AND acc_type = "'.$type.'" order by acc_txn_date, acc_txn_id;'); 
    }


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
	    // $final_list[''] = array(''=>'Choose');
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

	    $final_list = array(); 
		$billref_no = '';		
        if($result){
            foreach($result as $row){
                $gstroutsup_info = $this->gstroutsup_info($row['voucher_txn_id']);
				$gstrinwsup_info = $this->gstrinwsup_info($row['voucher_txn_id']);
				
				if($gstroutsup_info){
				  $billref_no = $gstroutsup_info['outsup_bill_ref_no'];	
				}
				if($gstrinwsup_info){
					$billref_no =$gstrinwsup_info['inwsup_bill_ref_no'];
				}
				$voucher_date = date('d/m/Y',strtotime($row['voucher_date']));
				if($billref_no)
                $final_list[$row['voucher_txn_id']]= "BILL REF NO.: ".$billref_no." DATED ".$voucher_date."(VCH NO.:". $row['comp_vch_no'].")";
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

		$result = $this->db->table($bsdoppybal_tbl)->where('bo_id', $this->bo_id)->where('bill_sundry_id', $bill_sundry_id)->get()->getRowArray();

        
        $bal = !empty($result['bsd_op_bal']) ? $result['bsd_op_bal'] : 0;

    	$bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$bill_sundry_id.'_'.$this->session->get('ses_comp_fy_id');
		$this->db->query('SET @bal = '.$bal.';');
		$this->db->query('UPDATE '.$bs_txn_tbl.' SET sundry_bal = CASE WHEN sundry_txn_drcr = "c" THEN @bal:=@bal - sundry_txn_amount WHEN sundry_txn_drcr = "d" THEN @bal:=@bal + sundry_txn_amount ELSE 0 END where bill_sundry_id = "'.$bill_sundry_id.'" AND bo_id = "'.$this->bo_id.'" order by sundry_txn_date,voucher_txn_id,sundry_txn_id;');
    }

    function getUndefinedBillRefId($account_id)
    {
      $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');

			$data = $this->db->table($bill_mst_tbl)
							->select('bills_ref_id')
							->where('acc_id', $account_id)
							->where('bills_ref_name', 'UNDEFINED')
							->get()->getRowArray();

      if($data)
	  		return $data['bills_ref_id'];
        else
			return $this->createUndefinedBillRefId($account_id);	  
    }

    function createUndefinedBillRefId($account_id)
    {
    	$data = [
                'bills_ref_name' 	=> 'UNDEFINED',
                'acc_id'         	=> $account_id,
                'bills_status'    => 'undefined',
                'bill_due_date'  	=> company()->fy_from_date,
								'bo_id'          	=> $this->bo_id,
            ];
        
        $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
        $this->db->table($bill_mst_tbl)->insert($data);

        $bills_ref_id = $this->db->insertID();

				$mst_base_id = $this->create_mst_base_id($bills_ref_id,'billmaster');
				$this->db->table($bill_mst_tbl)
						->where('bills_ref_id',$bills_ref_id)
						->update(['mst_base_id' => $mst_base_id]);

				

				$data = [
					'bills_ref_id'	=> $bills_ref_id,
					'bo_id'			=> $this->bo_id,
					'bills_op_bal'	=> 0,
					'bills_py_bal'	=> 0,
				];
			$billsoppyn_tbl = $this->company_id.'_billsoppyn_'.$this->session->get('ses_comp_fy_id');
					
			$exists = $this->db->table($billsoppyn_tbl)
							->select('bills_ref_id')
							->where('bills_ref_id', $bills_ref_id)							
							->countAllResults();
				if($exists==0)							
				$this->db->table($billsoppyn_tbl)->insert($data);

	    return $bills_ref_id;
    }
     
    
    function update_bill_master($id,$data,$VchOthrBo=0)
    {
        $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
        $this->db->table($bill_mst_tbl)
			        ->where('bills_ref_id', $id)
			        ->update(['bill_due_date' => $data['bill_due_date']]);
		// check applied on dated: 27-01-2025
		if(isset($data['bills_op_bal'])){
		$balance = $data['bills_op_bal'] ?? 0;
		
		if($VchOthrBo==0)
		  $bo_id = $this->bo_id;
		 else
		 $bo_id = $VchOthrBo;
		$billsoppyn_tbl = $this->company_id.'_billsoppyn_'.$this->session->get('ses_comp_fy_id');
		$exists = $this->db->table($billsoppyn_tbl)
							->where('bills_ref_id',$id)
							->where('bo_id',$bo_id)
							->get()->getRowArray();
		if($exists){
			$this->db->table($billsoppyn_tbl)
				->where('bills_ref_id',$id)
				->where('bo_id',$bo_id)
				->update(['bills_op_bal' => floatval($balance)]);
		}
		else{
			$op_data = [
				'bills_ref_id'	=> $id,
				'bo_id'			=> $bo_id,
				'bills_op_bal'	=> floatval($balance),
				'bills_py_bal'	=> 0,
			];
			$this->db->table($billsoppyn_tbl)->insert($op_data);
		}
		$this->update_bill_txn_balance($id);	
		}
		
		
    }
    
    function delete_bills_txn($voucher_txn_id)
    {
    	$bill_txn_tbl = $this->company_id.'_billstxnnn_'.$this->session->get('ses_comp_fy_id');
		
		$result = $this->db->table($bill_txn_tbl)
							->where('bo_id', $this->bo_id)
							->where('voucher_txn_id', $voucher_txn_id)
							->get()->getResultArray();

    	$bills_ref_ids = [];
    	if($result){
	    	foreach($result as $key => $value){
	        	$bills_ref_ids[] = $value['bills_ref_id'];
	    	}
	    }

        $this->db->table($bill_txn_tbl)
			        ->where('voucher_txn_id', $voucher_txn_id)
			        ->delete();

        if($bills_ref_ids){
        	foreach($bills_ref_ids as  $bills_ref_id){
	        	$this->update_bill_txn_balance($bills_ref_id);
	        }
        }
    }

    function update_bill_txn_balance($bills_ref_id,$bo_id=0)
    {
		if($bo_id==0 || $bo_id=='')
			$bo_id=$this->bo_id;
		
    	$bal = $this->get_bills_op_bal($bills_ref_id,$bo_id);

		$bill_txn_tbl = $this->company_id.'_billstxnnn_'.$this->session->get('ses_comp_fy_id');
		$this->db->query('SET @bal = '.$bal.';');
	
		$this->db->query('UPDATE '.$bill_txn_tbl.' SET bills_txn_bal = CASE WHEN bills_txn_drcr = "C" THEN @bal:=@bal - bills_txn_amt WHEN bills_txn_drcr = "D" THEN @bal:=@bal + bills_txn_amt ELSE 0 END where bo_id = "'.$bo_id.'" AND bills_ref_id = "'.$bills_ref_id.'" order by bills_txn_date,voucher_txn_id,bills_txn_id;');
    }

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
		       $batch_unit_name       = $get_units_info['item_unit'];
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
			 if($row['batch_expiry']!=''){
				$batch_expiry =date('d-m-Y',strtotime($row['batch_expiry']));
				
			 }  
			 else{
				$batch_expiry=''; 
			 }
			 
			 if($row['batch_mfr']!=''){
				$batch_mfr =date('d-m-Y',strtotime($row['batch_mfr']));
				
			 }  
			 else{
				$batch_mfr=''; 
			 }
			 
			 $all_batches[]=array("id"=>$row['batch_id'],"label"=>$row['batch_no'],"value"=>$row['batch_no'],
			                       "item_id"=>$row['item_id'],"batch_unit"=>$row['batch_unit'],'batch_qty'=>$row['batch_qty'],'batch_expiry'=>$batch_expiry,
								   'batch_mfr'=>$batch_mfr );   
		   }
		   
	   }
	   	
      return $all_batches; 
    } 
	
	
	
    function get_account_bill_refs($account_id)
    {
        $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
        $data = $this->db->table($bill_mst_tbl)
		        ->select('bills_ref_id as id, bills_ref_name as label, bills_ref_name as value, bill_due_date, DATE_FORMAT(bill_due_date, "%d-%m-%Y") as due_date')
		        ->where('acc_id', $account_id)
		        ->where('bills_ref_name !=', 'UNDEFINED')
		        ->get()->getResultArray();
        
        return $data;
    }

    function get_project_op_bal($project_id,$project_bal_type,$bo_id=0)
	{   
	    if($bo_id==0 || $bo_id=='')
			$bo_id = $this->bo_id;
		$bal = 0;
		$prjoppybal_tbl = $this->company_id.'_prjoppybal_'.$this->session->get('ses_comp_fy_id');

		$data = $this->db->table($prjoppybal_tbl)
					->where('project_id', $project_id)
					->where('project_bal_type', $project_bal_type)
					->where('bo_id', $bo_id)
					->get()->getRowArray();
	if($data){
		$bal = floatval($data['project_op_bal']);
	}
	else{
		$op_data = [
			'project_id' 			=> $project_id,
			'bo_id' 				=> $bo_id,
			'project_op_bal' 		=> 0,
			'project_py_bal' 		=> 0,
			'project_bal_type' 		=> $project_bal_type
		];
		$this->db->table($prjoppybal_tbl)->insert($op_data);
	}
	return $bal;
	}

    function update_project_lia_bal($project_id)
    {
        $bal = $this->get_project_op_bal($project_id, 'lia');

    	$txn_tbl = $this->company_id.'_prjliabtxn_'.$this->session->get('ses_comp_fy_id');
		$this->db->query('SET @bal = '.$bal.';');
		$this->db->query('UPDATE '.$txn_tbl.' SET proj_txn_bal = CASE WHEN proj_txn_drcr = "C" THEN @bal:=@bal - proj_txn_amt WHEN proj_txn_drcr = "D" THEN @bal:=@bal + proj_txn_amt ELSE 0 END where project_id = "'.$project_id.'" AND bo_id = "'.$this->bo_id.'" order by proj_txn_date,voucher_txn_id,proj_txn_id;');
    }

    function update_project_ast_bal($project_id)
    {
        $bal = $this->get_project_op_bal($project_id, 'ast');

    	$txn_tbl = $this->company_id.'_projasttxn_'.$this->session->get('ses_comp_fy_id');
		$this->db->query('SET @bal = '.$bal.';');
		$this->db->query('UPDATE '.$txn_tbl.' SET proj_txn_bal = CASE WHEN proj_txn_drcr = "C" THEN @bal:=@bal - proj_txn_amt WHEN proj_txn_drcr = "D" THEN @bal:=@bal + proj_txn_amt ELSE 0 END where project_id = "'.$project_id.'" AND bo_id = "'.$this->bo_id.'" order by proj_txn_date,voucher_txn_id,proj_txn_id;');
    }

    function update_project_exp_bal($project_id)
    {
        $bal = $this->get_project_op_bal($project_id, 'exp');

    	$txn_tbl = $this->company_id.'_projexptxn_'.$this->session->get('ses_comp_fy_id');
		$this->db->query('SET @bal = '.$bal.';');
		$this->db->query('UPDATE '.$txn_tbl.' SET proj_txn_bal = CASE WHEN proj_txn_drcr = "C" THEN @bal:=@bal - proj_txn_amt WHEN proj_txn_drcr = "D" THEN @bal:=@bal + proj_txn_amt ELSE 0 END where project_id = '.$project_id.' AND bo_id = '.$this->bo_id.' order by proj_txn_date,voucher_txn_id,proj_txn_id;');
    }

    function update_project_rev_bal($project_id)
    {
        $bal = $this->get_project_op_bal($project_id, 'rev');

    	$txn_tbl = $this->company_id.'_projrevtxn_'.$this->session->get('ses_comp_fy_id');
		$this->db->query('SET @bal = '.$bal.';');
		$this->db->query('UPDATE '.$txn_tbl.' SET proj_txn_bal = CASE WHEN proj_txn_drcr = "C" THEN @bal:=@bal - proj_txn_amt WHEN proj_txn_drcr = "D" THEN @bal:=@bal + proj_txn_amt ELSE 0 END where project_id = '.$project_id.' AND bo_id = '.$this->bo_id.' order by proj_txn_date,voucher_txn_id,proj_txn_id;');
    }

    function get_bills_op_bal($bills_ref_id,$bo_id=0)
    {
		if($bo_id==0 || $bo_id=='')
			$bo_id = $this->bo_id;
    	$bal = 0;
		$billsoppyn_tbl = $this->company_id.'_billsoppyn_'.$this->session->get('ses_comp_fy_id');

      $data = $this->db->table($billsoppyn_tbl)
      				->where('bills_ref_id', $bills_ref_id)
      				->where('bo_id', $bo_id)
      				->get()->getRowArray();
      if($data){
      	$bal = floatval($data['bills_op_bal']);
      }
      else{
      	$op_data = [
      		'bills_ref_id' 	=> $bills_ref_id,
      		'bo_id' 		=> $bo_id,
      		'bills_op_bal' 	=> 0,
      		'bills_py_bal' 	=> 0,
      	];
      	$this->db->table($billsoppyn_tbl)->insert($op_data);
      }
      return $bal;
    }

    function get_account_all_bill_refs($account_id)
    {
        $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
        $data = $this->db->table($bill_mst_tbl)
        				->select('bills_ref_id, bills_ref_name, bills_ref_name as label, bills_ref_name as value, DATE_FORMAT(bill_due_date, "%d-%m-%Y") as bill_due_date')
        				->where('acc_id', $account_id)
        				->orderBy('bills_ref_id')
        				->get()->getResultArray();

        foreach ($data as $key => $value) {
        	$data[$key]['bill_op_drcr'] = '';
        	$data[$key]['bills_op_bal'] = 0;
        	$data[$key]['method'] = 'Adjustment';

        	$bills_op_bal = $this->get_bills_op_bal($value['bills_ref_id']);

        	if($bills_op_bal < 0){
        		$data[$key]['bill_op_drcr'] = 'C';
        		$data[$key]['bills_op_bal'] = abs($bills_op_bal);
        	}
        	if($bills_op_bal > 0){
        		$data[$key]['bill_op_drcr'] = 'D';
        		$data[$key]['bills_op_bal'] = $bills_op_bal;
        	}

        	if($value['bill_due_date'] == '00-00-0000')
        		$data[$key]['bill_due_date'] = '';
        }
        
        return $data;
    }
  function get_account_all_bill_refs_new($account_id,$opntotal)
    {
        $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
        $data = $this->db->table($bill_mst_tbl)
        				->select('bills_ref_id, bills_ref_name, bills_ref_name as label, bills_ref_name as value, DATE_FORMAT(bill_due_date, "%d-%m-%Y") as bill_due_date')
        				->where('acc_id', $account_id)
        				->orderBy('bills_ref_id')
        				->get()->getResultArray();
       $tt=0;
        foreach ($data as $key => $value) {
        	$data[$key]['bill_op_drcr'] = '';
        	$data[$key]['bills_op_bal'] = 0;
        	$data[$key]['method'] = 'Adjustment';

        	$bills_op_bal = $this->get_bills_op_bal($value['bills_ref_id']);
            
        	if($bills_op_bal < 0){
        		$data[$key]['bill_op_drcr'] = 'C';
        		$data[$key]['bills_op_bal'] = abs($bills_op_bal);
				$tt +=abs($bills_op_bal);
        	}
        	if($bills_op_bal > 0){
        		$data[$key]['bill_op_drcr'] = 'D';
        		$data[$key]['bills_op_bal'] = $bills_op_bal;
				$tt +=$bills_op_bal;
        	}

        	if($value['bill_due_date'] == '00-00-0000')
        		$data[$key]['bill_due_date'] = '';
			
			
			
        }
        if($tt==abs($opntotal)){
	      $final_array = array();
		 foreach($data as $key => $rows){
			 if($rows['bills_op_bal']==0 && $rows['bill_op_drcr']==''){}else{
				$final_array[$key]=$rows;
			 }
		 }	
			
		}else{
		$final_array = $data;	
		}
		
        return $final_array;
    }

    // delete it
    // function update_account_all_bill_refs($data)
    // {
    //     $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
    //     $this->db->table($bill_mst_tbl)->where('bills_ref_id', $data['bills_ref_id'])->update($data);
		
	// 	$this->update_bill_txn_balance($data['bills_ref_id']);

    // }

    function reset_acc_bill_refs_op_balance($acc_id)
    {
        $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
        $billsoppyn_tbl = $this->company_id.'_billsoppyn_'.$this->session->get('ses_comp_fy_id');

        $result = $this->db->table($bill_mst_tbl)
					        ->where('acc_id', $acc_id)
					        ->get()->getResultArray();
				foreach ($result as $key => $value) {

      		$this->db->table($billsoppyn_tbl)
					        ->where('bills_ref_id', $value['bills_ref_id'])
					        ->where('bo_id', $this->bo_id)
					        ->update(['bills_op_bal' => 0]);
				}
		
		
				$this->update_all_acc_bill_ref_bal($acc_id);
    }

    function update_all_acc_bill_ref_bal($acc_id, $undefined = false)
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

 function mc_info($centre_id){
      $mat_centre_master_tbl =  $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
	  $response =  $this->db->table($mat_centre_master_tbl)->where('mat_cent_id', $centre_id)->get()->getRowArray();   	   
	  if($response){
		  if($response['mat_cent_country']!='' && $response['mat_cent_state']!=''){
		   $state_info    =  $this->get_state_info($response['mat_cent_country'],$response['mat_cent_state']);   
		   $state_code    =   $state_info['state_code'];
		  }
		  else 
		  $state_code ="";	  
	  
	  
	   $response['state_code'] = sprintf( '%02d', $state_code);		  

	  }
	 return $response; 
   } 
   
    function update_item_balances($item_id)
    {
		// get item default valuation method id 
		$iteminfo     = $this->get_item_info($item_id);
		$valmethod_id = ($iteminfo['valmethod_id']!='')?$iteminfo['valmethod_id']:1;
		
    	$mat_centre_master_tbl  = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
	    $result = $this->db->table($mat_centre_master_tbl)->get()->getResultArray();

	    foreach ($result as $key => $value) {

	    	$result2 = $this->get_item_units($item_id);

	    	foreach ($result2 as $key2 => $value2) {
	    		$this->update_item_unit_balance($item_id, $value2, $value['mat_cent_id'], 0, 1,$valmethod_id,0);
	    	}
	    }
    }
	
	function update_item_balances_only($item_id)
    { 
	    $fy_start_date    = date('Y-m-d',strtotime($this->session->get('ses_company_fy_beginning')));
		$itemrepall_tbl   = $this->company_id.'_itemrepall_'.$this->session->get('ses_comp_fy_id');	
		$itemrepmcn_tbl   = $this->company_id.'_itemrepmcn_'.$this->session->get('ses_comp_fy_id');	
		$itemrepgrp_tbl   = $this->company_id.'_itemrepgrp_'.$this->session->get('ses_comp_fy_id');	
		$itemrepcat_tbl   = $this->company_id.'_itemrepcat_'.$this->session->get('ses_comp_fy_id');	
		$itemrepprj_tbl   = $this->company_id.'_itemrepprj_'.$this->session->get('ses_comp_fy_id');	
		$itemrepbon_tbl   = $this->company_id.'_itemrepbon_'.$this->session->get('ses_comp_fy_id');	
		$itemrepbat_tbl   = $this->company_id.'_itemrepbat_'.$this->session->get('ses_comp_fy_id');	
		$bo_id            = $this->bo_id;					
		// get item default valuation method id 
		$iteminfo               = $this->get_item_info($item_id);
		$valmethod_id           = $iteminfo['valmethod_id'];
		$mat_centre_master_tbl  = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
	    $mc_result                 = $this->db->table($mat_centre_master_tbl)->get()->getResultArray();
        $master_mc_qtybal       = array();
		$item_bal_qty           = 0;
		$item_opening_balances =0;	
		$item_opening_values =0;	
	    foreach ($mc_result as $key => $value) {
	    	$result2 = $this->get_item_units($item_id);
					
	    	foreach ($result2 as $key2 => $value2) {
			 if($value2>0){	
	    	    $item_bal_qty += $this->update_itemunit_balance($item_id, $value2, $value['mat_cent_id'], 0, 1,$valmethod_id);
	    	    $batch_id=0;
				$opnbal              = $this->get_item_op_bal($item_id,$value2,$value['mat_cent_id'],$batch_id);
				$opnval              = $this->get_item_op_val($item_id,$value2,$value['mat_cent_id'],$batch_id,$valmethod_id);
				$opnbal = $opnbal ? $opnbal : 0;
                $opnval = $opnval ? $opnval : 0;
				$mc_id = $value['mat_cent_id'];
				$item_id_unit_id = $item_id.'_'.$value2;
				$master_mc_qtybal[$mc_id][$item_id_unit_id]=array("opnbal"=>$opnbal,"opnval"=>$opnval);
				
			 }
			}
	    }		
		
		// Mc wise not included its sum of all the mc's
		$items_units_balances = array();
		$mc_items_units_balances = array();

		if ($master_mc_qtybal) {
			foreach ($master_mc_qtybal as $mc_id => $item_unit_info) {
				foreach ($item_unit_info as $itemid_unitid => $bal_row) {
					
					if (!isset($mc_items_units_balances[$mc_id][$itemid_unitid]['opnbal'])) {
					$mc_items_units_balances[$mc_id][$itemid_unitid]['opnbal'] = 0;
					$mc_items_units_balances[$mc_id][$itemid_unitid]['opnval'] = 0;
				}
				$mc_items_units_balances[$mc_id][$itemid_unitid]['opnbal'] += $bal_row['opnbal'];
				$mc_items_units_balances[$mc_id][$itemid_unitid]['opnval'] += $bal_row['opnval'];
					
					// Initialize if not set
					if (!isset($items_units_balances[$itemid_unitid]['opnbal'])) {
						$items_units_balances[$itemid_unitid]['opnbal'] = 0;
						$items_units_balances[$itemid_unitid]['opnval'] = 0;
					}
					// Accumulate values
					$items_units_balances[$itemid_unitid]['opnbal'] += $bal_row['opnbal'];
					$items_units_balances[$itemid_unitid]['opnval'] += $bal_row['opnval'];
				}
			}
		}
		
		if($mc_items_units_balances){
			foreach($mc_items_units_balances as $mat_cent_id => $item_unit_info){				
				foreach($item_unit_info as $item_id_unit_id => $bal_info){
					$item_id = explode("_",$item_id_unit_id)[0];
				    $item_unit = explode("_",$item_id_unit_id)[1];
				 $itemrepmcn_tbl_rows = $this->db->table($itemrepmcn_tbl)->where('voucher_txn_id',0)->where('method_id',0)->where('mat_cent_id',$mat_cent_id)->where('bo_id',$bo_id)->where('item_id_unit_id',$item_id_unit_id)->countAllResults();  
				if($itemrepmcn_tbl_rows==0){
				  $this->db->table($itemrepmcn_tbl)->insert(array('voucher_txn_id'=>0,'item_txn_date'=>$fy_start_date,'item_avail'=>1,'bo_id'=>$bo_id,'mat_cent_id'=>$mat_cent_id,'item_id_unit_id'=>$item_id_unit_id,'method_id'=>0,'item_bal_qty'=>$bal_info['opnbal'],'item_value'=>$bal_info['opnval'],'item_unit'=>$item_unit));
				}else{
				  $this->db->table($itemrepmcn_tbl)->where('voucher_txn_id',0)->where('method_id',0)->where('mat_cent_id',$mat_cent_id)->where('bo_id',$bo_id)->where('item_id_unit_id',$item_id_unit_id)->update(array('item_bal_qty'=>$bal_info['opnbal'],'item_value'=>$bal_info['opnval']));
				} 
				}
			 }
			
		}
		
		if($items_units_balances){
			foreach($items_units_balances as $item_id_unit_id => $item_info){
				 $opnbal = $item_info['opnbal'];
				 $opnval = $item_info['opnval'];
				 $mat_cent_id = 0;
				// SaveErrorLog($item_id_unit_id.'---'.$opnbal);
				 $item_id = explode("_",$item_id_unit_id)[0];
				 $item_unit = explode("_",$item_id_unit_id)[1];
				 
				 $item_id_unit_id =  $item_id.'_'.$item_unit.'_1';
				 $itemrepbon_tbl_rows  = $this->db->table($itemrepbon_tbl)->where('voucher_txn_id',0)->where('method_id',9)->where('bo_id',$bo_id)->where('item_id_unit_id',$item_id_unit_id)->countAllResults();  
				$dd = $this->db->getlastquery();
				SaveErrorLog($dd);
				if($itemrepbon_tbl_rows==0){
				  $this->db->table($itemrepbon_tbl)->insert(array('voucher_txn_id'=>0,'item_txn_date'=>$fy_start_date,'item_avail'=>1,'bo_id'=>$bo_id,'item_id_unit_id'=>$item_id_unit_id,'method_id'=>9,'item_bal_qty'=>$opnbal,'item_value'=>$opnval,'item_unit'=>$item_unit));
				}else{
				  $this->db->table($itemrepbon_tbl)->where('voucher_txn_id',0)->where('method_id',9)->where('bo_id',$bo_id)->where('item_id_unit_id',$item_id_unit_id)->update(array('item_bal_qty'=>$opnbal,'item_value'=>$opnval));
				}  
				
				/* $itemrepall_tbl_rows = $this->db->table($itemrepall_tbl)->where('voucher_txn_id',0)->where('method_id',9)->where('item_id_unit_id',$item_id_unit_id)->countAllResults();  
				if($itemrepall_tbl_rows==0){
				  $this->db->table($itemrepall_tbl)->insert(array('voucher_txn_id'=>0,'item_txn_date'=>$fy_start_date,'item_avail'=>1,'item_id_unit_id'=>$item_id_unit_id,'method_id'=>9,'item_bal_qty'=>$opnbal,'item_value'=>$opnval,'item_unit'=>$item_unit));
				}else{
				 $this->db->table($itemrepall_tbl)->where('voucher_txn_id',0)->where('method_id',9)->where('item_id_unit_id',$item_id_unit_id)->update(array('item_bal_qty'=>$opnbal,'item_value'=>$opnval));
				}
				
				
				
				$itemrepgrp_tbl_rows = $this->db->table($itemrepgrp_tbl)->where('voucher_txn_id',0)->where('method_id',9)->where('bo_id',$bo_id)->where('item_id_unit_id',$item_id_unit_id)->countAllResults();  
				if($itemrepgrp_tbl_rows==0){
				  $this->db->table($itemrepgrp_tbl)->insert(array('voucher_txn_id'=>0,'item_txn_date'=>$fy_start_date,'item_avail'=>1,'bo_id'=>$bo_id,'item_id_unit_id'=>$item_id_unit_id,'method_id'=>9,'item_bal_qty'=>$opnbal,'item_value'=>$opnval,'item_unit'=>$item_unit));
				}else{
				  $this->db->table($itemrepgrp_tbl)->where('voucher_txn_id',0)->where('method_id',9)->where('bo_id',$bo_id)->where('item_id_unit_id',$item_id_unit_id)->update(array('item_bal_qty'=>$opnbal,'item_value'=>$opnval));
				}
				
				$itemrepcat_tbl_rows = $this->db->table($itemrepcat_tbl)->where('voucher_txn_id',0)->where('method_id',9)->where('bo_id',$bo_id)->where('item_id_unit_id',$item_id_unit_id)->countAllResults();  
				if($itemrepcat_tbl_rows==0){
				  $this->db->table($itemrepcat_tbl)->insert(array('voucher_txn_id'=>0,'item_txn_date'=>$fy_start_date,'item_avail'=>1,'bo_id'=>$bo_id,'item_id_unit_id'=>$item_id_unit_id,'method_id'=>9,'item_bal_qty'=>$opnbal,'item_value'=>$opnval,'item_unit'=>$item_unit));
				}else{
				  $this->db->table($itemrepcat_tbl)->where('voucher_txn_id',0)->where('method_id',9)->where('bo_id',$bo_id)->where('item_id_unit_id',$item_id_unit_id)->update(array('item_bal_qty'=>$opnbal,'item_value'=>$opnval));
				}
				
				$itemrepprj_tbl_rows = $this->db->table($itemrepprj_tbl)->where('voucher_txn_id',0)->where('method_id',9)->where('bo_id',$bo_id)->where('item_id_unit_id',$item_id_unit_id)->countAllResults();  
				if($itemrepprj_tbl_rows==0){
				  $this->db->table($itemrepprj_tbl)->insert(array('voucher_txn_id'=>0,'item_txn_date'=>$fy_start_date,'item_avail'=>1,'bo_id'=>$bo_id,'item_id_unit_id'=>$item_id_unit_id,'method_id'=>9,'item_bal_qty'=>$opnbal,'item_value'=>$opnval,'item_unit'=>$item_unit));
				}else{
				  $this->db->table($itemrepprj_tbl)->where('voucher_txn_id',0)->where('method_id',9)->where('bo_id',$bo_id)->where('item_id_unit_id',$item_id_unit_id)->update(array('item_bal_qty'=>$opnbal,'item_value'=>$opnval));
				}
				
				$itemrepbat_tbl_rows = $this->db->table($itemrepbat_tbl)->where('voucher_txn_id',0)->where('method_id',$valmethod_id)->where('bo_id',$bo_id)->where('item_id_unit_id',$item_id_unit_id)->countAllResults();  
				if($itemrepbat_tbl_rows==0){
				  $this->db->table($itemrepbat_tbl)->insert(array('voucher_txn_id'=>0,'item_txn_date'=>$fy_start_date,'item_avail'=>1,'bo_id'=>$bo_id,'item_id_unit_id'=>$item_id_unit_id,'method_id'=>9,'item_bal_qty'=>$opnbal,'item_value'=>$opnval,'item_unit'=>$item_unit));
				}else{
				  $this->db->table($itemrepbat_tbl)->where('voucher_txn_id',0)->where('method_id',9)->where('bo_id',$bo_id)->where('item_id_unit_id',$item_id_unit_id)->update(array('item_bal_qty'=>$opnbal,'item_value'=>$opnval));
				} */
				
				$item_txn_tbl = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
			    $builder = $this->db->table($item_txn_tbl); 
			    $builder->where('item_id', $item_id);
			    $builder->where('bo_id', $this->bo_id);
			    $builder->orderBy('item_txn_date', 'asc');
			    $builder->orderBy('voucher_txn_id', 'asc');
			    $builder->orderBy('item_txn_id', 'asc');
			    $txn_result = $builder->get()->getResultArray();
				
				if($txn_result){
					foreach($txn_result as $txnrow){
					$voucher_txn_id = $txnrow['voucher_txn_id'];
					$item_txn_qty   = $txnrow['item_txn_qty'];
					$item_avail     = $txnrow['item_avail'];
					$item_txn_date  = $txnrow['item_txn_date'];
					$item_txn_id    = $txnrow['item_txn_id'];
					$mat_cent_id    = $txnrow['mat_cent_id'];
					
					$itemrepbon_tbl_rowss  = $this->db->table($itemrepbon_tbl)->where('item_txn_id',$item_txn_id)->where('method_id',$valmethod_id)->where('bo_id',$bo_id)->where('item_id_unit_id',$item_id_unit_id)->countAllResults();  
    				if($itemrepbon_tbl_rowss==0){
    				  $this->db->table($itemrepbon_tbl)->insert(array('item_txn_id'=>$item_txn_id,'voucher_txn_id'=>$voucher_txn_id,'item_txn_date'=>$item_txn_date,'item_avail'=>$item_avail,'bo_id'=>$bo_id,'item_id_unit_id'=>$item_id_unit_id,'method_id'=>$valmethod_id,'item_bal_qty'=>$item_txn_qty,'item_value'=>0,'item_unit'=>$item_unit));
    				}else{
    				  $this->db->table($itemrepbon_tbl)->where('item_txn_id',$item_txn_id)->where('method_id',$valmethod_id)->where('bo_id',$bo_id)->where('item_id_unit_id',$item_id_unit_id)->update(array('item_bal_qty'=>$item_txn_qty,'item_value'=>0));
    				}  

					$itemrepall_tbl_rows  = $this->db->table($itemrepall_tbl)->where('item_txn_id',$item_txn_id)->where('method_id',$valmethod_id)->where('item_id_unit_id',$item_id_unit_id)->countAllResults();  
    				if($itemrepall_tbl_rows==0){
    				  $this->db->table($itemrepall_tbl)->insert(array('item_txn_id'=>$item_txn_id,'voucher_txn_id'=>$voucher_txn_id,'item_txn_date'=>$item_txn_date,'item_avail'=>$item_avail,'item_id_unit_id'=>$item_id_unit_id,'method_id'=>$valmethod_id,'item_bal_qty'=>$item_txn_qty,'item_value'=>0,'item_unit'=>$item_unit));
    				}else{
    				  $this->db->table($itemrepall_tbl)->where('item_txn_id',$item_txn_id)->where('method_id',$valmethod_id)->where('item_id_unit_id',$item_id_unit_id)->update(array('item_bal_qty'=>$item_txn_qty,'item_value'=>0));
    				}
					
					$itemrepmcn_tbl_rows  = $this->db->table($itemrepmcn_tbl)->where('item_txn_id',$item_txn_id)->where('mat_cent_id',$mat_cent_id)->where('method_id',$valmethod_id)->where('bo_id',$bo_id)->where('item_id_unit_id',$item_id_unit_id)->countAllResults();  
    				if($itemrepmcn_tbl_rows==0){
    				  $this->db->table($itemrepmcn_tbl)->insert(array('item_txn_id'=>$item_txn_id,'voucher_txn_id'=>$voucher_txn_id,'mat_cent_id'=>$mat_cent_id,'item_txn_date'=>$item_txn_date,'item_avail'=>$item_avail,'bo_id'=>$bo_id,'item_id_unit_id'=>$item_id_unit_id,'method_id'=>$valmethod_id,'item_bal_qty'=>$item_txn_qty,'item_value'=>0,'item_unit'=>$item_unit));
    				}else{
    				  $this->db->table($itemrepmcn_tbl)->where('item_txn_id',$item_txn_id)->where('method_id',$valmethod_id)->where('mat_cent_id',$mat_cent_id)->where('bo_id',$bo_id)->where('item_id_unit_id',$item_id_unit_id)->update(array('item_bal_qty'=>$item_txn_qty,'item_value'=>0));
    				} 

					$itemrepgrp_tbl_rows  = $this->db->table($itemrepgrp_tbl)->where('item_txn_id',$item_txn_id)->where('method_id',$valmethod_id)->where('bo_id',$bo_id)->where('item_id_unit_id',$item_id_unit_id)->countAllResults();  
    				if($itemrepgrp_tbl_rows==0){
    				  $this->db->table($itemrepgrp_tbl)->insert(array('item_txn_id'=>$item_txn_id,'voucher_txn_id'=>$voucher_txn_id,'item_txn_date'=>$item_txn_date,'item_avail'=>$item_avail,'bo_id'=>$bo_id,'item_id_unit_id'=>$item_id_unit_id,'method_id'=>$valmethod_id,'item_bal_qty'=>$item_txn_qty,'item_value'=>0,'item_unit'=>$item_unit));
    				}else{
    				  $this->db->table($itemrepgrp_tbl)->where('item_txn_id',$item_txn_id)->where('method_id',$valmethod_id)->where('bo_id',$bo_id)->where('item_id_unit_id',$item_id_unit_id)->update(array('item_bal_qty'=>$item_txn_qty,'item_value'=>0));
    				} 
					
					$itemrepcat_tbl_rows  = $this->db->table($itemrepcat_tbl)->where('item_txn_id',$item_txn_id)->where('method_id',$valmethod_id)->where('bo_id',$bo_id)->where('item_id_unit_id',$item_id_unit_id)->countAllResults();  
    				if($itemrepcat_tbl_rows==0){
    				  $this->db->table($itemrepcat_tbl)->insert(array('item_txn_id'=>$item_txn_id,'voucher_txn_id'=>$voucher_txn_id,'item_txn_date'=>$item_txn_date,'item_avail'=>$item_avail,'bo_id'=>$bo_id,'item_id_unit_id'=>$item_id_unit_id,'method_id'=>$valmethod_id,'item_bal_qty'=>$item_txn_qty,'item_value'=>0,'item_unit'=>$item_unit));
    				}else{
    				  $this->db->table($itemrepcat_tbl)->where('item_txn_id',$item_txn_id)->where('method_id',$valmethod_id)->where('bo_id',$bo_id)->where('item_id_unit_id',$item_id_unit_id)->update(array('item_bal_qty'=>$item_txn_qty,'item_value'=>0));
    				} 
			
					$itemrepprj_tbl_rows  = $this->db->table($itemrepprj_tbl)->where('item_txn_id',$item_txn_id)->where('method_id',$valmethod_id)->where('bo_id',$bo_id)->where('item_id_unit_id',$item_id_unit_id)->countAllResults();  
    				if($itemrepprj_tbl_rows==0){
    				  $this->db->table($itemrepprj_tbl)->insert(array('item_txn_id'=>$item_txn_id,'voucher_txn_id'=>$voucher_txn_id,'item_txn_date'=>$item_txn_date,'item_avail'=>$item_avail,'bo_id'=>$bo_id,'item_id_unit_id'=>$item_id_unit_id,'method_id'=>$valmethod_id,'item_bal_qty'=>$item_txn_qty,'item_value'=>0,'item_unit'=>$item_unit));
    				}else{
    				  $this->db->table($itemrepprj_tbl)->where('item_txn_id',$item_txn_id)->where('method_id',$valmethod_id)->where('bo_id',$bo_id)->where('item_id_unit_id',$item_id_unit_id)->update(array('item_bal_qty'=>$item_txn_qty,'item_value'=>0));
    				} 
		
				$itemrepbat_tbl_rows  = $this->db->table($itemrepbat_tbl)->where('item_txn_id',$item_txn_id)->where('method_id',$valmethod_id)->where('bo_id',$bo_id)->where('item_id_unit_id',$item_id_unit_id)->countAllResults();  
    				if($itemrepbat_tbl_rows==0){
    				  $this->db->table($itemrepbat_tbl)->insert(array('item_txn_id'=>$item_txn_id,'voucher_txn_id'=>$voucher_txn_id,'item_txn_date'=>$item_txn_date,'item_avail'=>$item_avail,'bo_id'=>$bo_id,'item_id_unit_id'=>$item_id_unit_id,'method_id'=>$valmethod_id,'item_bal_qty'=>$item_txn_qty,'item_value'=>0,'item_unit'=>$item_unit));
    				}else{
    				  $this->db->table($itemrepbat_tbl)->where('item_txn_id',$item_txn_id)->where('method_id',$valmethod_id)->where('bo_id',$bo_id)->where('item_id_unit_id',$item_id_unit_id)->update(array('item_bal_qty'=>$item_txn_qty,'item_value'=>0));
    				}


					
					
					}
				}
				 
				
				
			}
		}
    }

	function update_item_txnval_only($item_id,$method_id){
	 $mat_centre_master_tbl  = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
	 $result                 = $this->db->table($mat_centre_master_tbl)->get()->getResultArray();
     foreach ($result as $key => $value) {
	    	$result2 = $this->get_item_units($item_id);
					
	    	foreach ($result2 as $key2 => $unit_id) {
			 if($unit_id>0){
				$itemtxnval_tbl = $this->company_id.'_itemtxnval_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
			    $this->db->table($itemtxnval_tbl)
				                    ->where('item_id', $item_id)
									->where('unit_id', $unit_id)
									->where('method_id', $method_id)
									->where('mat_cent_id', $value['mat_cent_id'])
									->update(['item_value'=>0]);
				
	    	   	
			    }
			}
	    }
	
	}
    function get_item_units($item_id)
    {
    	$unit_result = [];

    	$itemmaster_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($itemmaster_tbl)
    						->where('item_id', $item_id)
							->get()->getRowArray();
		if($result){
			$unit_result[] = $result['item_unit'];
		}

    	$itmoppybal_tbl = $this->company_id.'_itmoppybal_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($itmoppybal_tbl);
		$builder->select('item_unit');
		$builder->where('item_id', $item_id);
		$builder->groupBy('item_unit');
		$result2 = $builder->get()->getResultArray();
		if($result2){
			foreach ($result2 as $key2 => $value2) {
				if(!in_array($value2['item_unit'], $unit_result)){
					$unit_result[] = $value2['item_unit'];
				}
			}
		}

		$itemtxnnnn_tbl = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($itemtxnnnn_tbl);
		$builder->select('item_unit');
		$builder->where('item_id', $item_id);
		$builder->groupBy('item_unit');
		$result2 = $builder->get()->getResultArray();
		if($result2){
			foreach ($result2 as $key2 => $value2) {
				if(!in_array($value2['item_unit'], $unit_result)){
					$unit_result[] = $value2['item_unit'];
				}
			}
		}

		return $unit_result;
    }

    function get_item_op_bal($item_id,$item_unit,$mat_cent_id,$batch_id,$bo_id=0)
    {
    	$bal=0; 
		if($bo_id==0 || $bo_id=='')
			$bo_id = $this->bo_id;

        $itmoppybal_tbl = $this->company_id.'_itmoppybal_'.$this->session->get('ses_comp_fy_id');

		$builder = $this->db->table($itmoppybal_tbl); 
		$builder->where('item_id', $item_id);
		$builder->where('item_unit', $item_unit);
		$builder->where('mat_cent_id', $mat_cent_id);
		$builder->where('bo_id', $bo_id);
		$builder->where('batch_id', $batch_id);	
    	$itmoppybal = $builder->get()->getRowArray();
    	if($itmoppybal){
    		$bal = floatval($itmoppybal['op_bal_qty']);
    	}
    	else{
    		$data = [
    			'comp_id'		=> $this->company_id,
    			'item_id' 		=> $item_id,
    			'item_unit' 	=> $item_unit,
    			'mat_cent_id' 	=> $mat_cent_id,
    			'bo_id' 		=> $bo_id,
    			'batch_id' 		=> $batch_id,
    			'op_bal_qty'	=> 0,
    			'py_bal_qty'	=> 0,
    		];
    		$this->db->table($itmoppybal_tbl)->insert($data);
    	}

    	return $bal;
    }
   
	
   
	
    function update_item_unit_balance($item_id, $item_unit, $mat_cent_id, $batch_id, $item_avail,$valmethod_id,$voucher_txn_id,$itemtxnid)
    {
        $bal = $this->get_item_op_bal($item_id,$item_unit,$mat_cent_id,$batch_id);
        $item_txn_tbl = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		$this->db->query('SET @bal = '.$bal.';');		
		$this->db->query('UPDATE '.$item_txn_tbl.' SET item_bal_qty = CASE WHEN item_txn_drcr = "c" THEN @bal:=@bal - item_txn_qty WHEN item_txn_drcr = "d" THEN @bal:=@bal + item_txn_qty ELSE 0 END where item_id = "'.$item_id.'" AND item_unit = "'.$item_unit.'" AND mat_cent_id = "'.$mat_cent_id.'" AND batch_id = "'.$batch_id.'" AND item_avail = "'.$item_avail.'" AND bo_id = "'.$this->bo_id.'" order by item_txn_date,voucher_txn_id,item_txn_id;');
		$this->calculate_valuation_avg($item_id, $item_unit, $mat_cent_id, $batch_id,$bal,1,0,$voucher_txn_id,$itemtxnid);
		$this->calculate_valuation_fifo($item_id, $item_unit, $mat_cent_id, $batch_id,$bal,2,0,$voucher_txn_id,$itemtxnid);   
		$this->calculate_valuation_lifo($item_id, $item_unit, $mat_cent_id, $batch_id,$bal,3,0,$voucher_txn_id,$itemtxnid);
	
   }
   function update_itemunit_balance($item_id, $item_unit, $mat_cent_id, $batch_id, $item_avail,$valmethod_id=0)
    {
        $bal = $this->get_item_op_bal($item_id,$item_unit,$mat_cent_id,$batch_id);
        $item_txn_tbl = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
		$this->db->query('SET @bal = '.$bal.';');		
		$this->db->query('UPDATE '.$item_txn_tbl.' SET item_bal_qty = CASE WHEN item_txn_drcr = "c" THEN @bal:=@bal - item_txn_qty WHEN item_txn_drcr = "d" THEN @bal:=@bal + item_txn_qty ELSE 0 END where item_id = "'.$item_id.'" AND item_unit = "'.$item_unit.'" AND mat_cent_id = "'.$mat_cent_id.'" AND batch_id = "'.$batch_id.'" AND item_avail = "'.$item_avail.'" AND bo_id = "'.$this->bo_id.'" order by item_txn_date,voucher_txn_id,item_txn_id;');
         
		$bo_id = $this->bo_id;
       // clear reporting tables valuation value and add latest qty 
	    $itemtxnnnn_tbl = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
	    $builder = $this->db->table($itemtxnnnn_tbl); 
		$builder->select('item_bal_qty,item_avail');
		$builder->where('item_id', $item_id);
		$builder->where('item_unit', $item_unit);
		$builder->where('mat_cent_id', $mat_cent_id);
		$builder->where('batch_id', $batch_id);			
		$builder->where('bo_id', $bo_id);
		$builder->orderBy('item_txn_date', 'desc');
		$builder->orderBy('voucher_txn_id', 'desc');
		$builder->orderBy('item_txn_id', 'desc');
        $builder->limit(1);		
		$response = $builder->get()->getRowArray();	
        $dd = $this->db->getlastquery();		
		if($response){
			$item_bal_qty = $response['item_bal_qty'];
			$item_avail   = $response['item_avail'];
		}
		else {
			$item_bal_qty = 0; 
			$item_avail   =1;
		   }
		return $item_bal_qty;
		
		
   }


/**
 * Universal AVG‑rate valuation – supports opening stock, purchases (DR) & sales (CR)
 *
 * Rules
 * ───────────────────────────────────────────────────────────
 * ▸ Opening (method_id 9) is copied into buffercalc once (qty + rate).
 * ▸ Purchases (DR) → push into buffercalc FIFO (qty, rate).
 * ▸ Sales (CR)    → pop from buffercalc FIFO until requested qty filled.
 *                   item_value = Σ(issued_qty × layer_rate).
 * ▸ repbon keeps a row per txn for chosen $valmethod_id **plus** a copy for method_id 0.
 * ▸ itemtxnval mirrors item_value for audit.
 *
 * Buffercalc columns used
 * ───────────────────────────────────────────────────────────
 *  buffer_ball_id (PK auto)
 *  cont_item_id_unit_avail_id   e.g. 143_18_1
 *  method_id                    valuation method (1/2/3 …)
 *  txn_id                       original item_txn_id that created the layer (0 = opening)
 *  buffer_ball_qty              remaining qty in that layer
 *  buffer_ball_rate             rate of that layer (4‑dp)
 *  voucher_txn_id               back‑ref for purchases
 */
public function calculate_valuation_avg(
    int  $item_id,
    int  $item_unit,
    int  $mat_cent_id,
    int  $batch_id,
    int  $opening_qty,      // not used – kept for signature compatibility
    int  $valmethod_id,
    int  $rewrite_tool,
    int  $voucher_txn_id,
    int  $item_txn_id = 0,
    int  $bo_id        = 0
) {
    // ─────────────────────────────────────────────────────────── db helpers
    $fy   = $this->session->get('ses_comp_fy_id');
    $pref = $this->company_id . '_';

    $tbl_repbon   = $pref.'itemrepbon_'.$fy;
    $tbl_buffer   = $pref.'buffercalc_'.$fy;	
    $tbl_txn      = $pref.'itemtxnnnn_'.$item_id.'_'.$fy;
    $tbl_txnval   = $pref.'itemtxnval_'.$item_id.'_'.$fy;
    $tbl_buffrevlog= $pref.'buffrevlog_'.$fy;
    $tbl_bufflogrec   = $pref.'bufflogrec_'.$fy;
  
    if (!$bo_id) $bo_id = $this->bo_id;

    /* ------------------------------------------------------------
     * 1⃣  fetch the current transaction (if any)
     * ---------------------------------------------------------- */
    $builder = $this->db->table($tbl_txn)
        ->where([
            'item_id'       => $item_id,
            'item_unit'     => $item_unit,
            'mat_cent_id'   => $mat_cent_id,
            'batch_id'      => $batch_id,
            'voucher_txn_id'=> $voucher_txn_id,
            'bo_id'         => $bo_id
        ])
        ->limit(1);

    if ($item_txn_id) $builder->where('item_txn_id', $item_txn_id);

    $orderDir = ($rewrite_tool == 1) ? 'asc' : 'desc';
    $builder->orderBy('item_txn_date', $orderDir)
            ->orderBy('voucher_txn_id', $orderDir)
            ->orderBy('item_txn_id',   $orderDir);

    $txn = $builder->get()->getRowArray(); // null if no txn (opening only run)

    $item_avail = $txn['item_avail'] ?? 1;                 // default container 1
    $cont_id    = $item_id.'_'.$item_unit.'_'.$item_avail; // composite key used in buffer/repbon

    /* ------------------------------------------------------------
     * 2⃣  ensure opening stock is pushed into buffer (once)
     * ---------------------------------------------------------- */
    $open = $this->db->table($tbl_repbon)
        ->where('item_id_unit_id', $item_id.'_'.$item_unit.'_1')
        ->where('method_id', '9')
        ->get()->getRowArray();

    if ($open && $open['item_bal_qty'] > 0) {
        $exists = $this->db->table($tbl_buffer)
            ->where('cont_item_id_unit_avail_id', $cont_id)
            ->where('method_id', $valmethod_id)
            ->where('txn_id', 0)  // 0 marks opening layer
            ->countAllResults();
		    //$exists_tbl_buffer = $this->db->getlastquery();
			//SaveErrorLog('exists_tbl_buffer-->'.$exists_tbl_buffer);	
        if ($exists == 0) {
            $this->db->table($tbl_buffer)->insert([
                'cont_item_id_unit_avail_id' => $cont_id,
                'method_id'                  => $valmethod_id,
                'txn_id'                     => 0,
                'buffer_ball_qty'            => parseAmount($open['item_bal_qty']),
                'buffer_ball_rate'           => parseAmountPrice($open['item_value'] / $open['item_bal_qty'],4),
                'valuation_id'               => 0,
                'voucher_txn_id'             => 0
            ]);
			//$tbl_bufferlog = $this->db->getlastquery();
			//SaveErrorLog($tbl_bufferlog);
        }
    }

    /* ------------------------------------------------------------
     * 3⃣  handle purchase / sale / no‑txn scenarios
     * ---------------------------------------------------------- */
    $item_value = 0;                  // monetary value for this txn (or opening calc)
    $bal_after  = 0;                  // container balance after operation

    // pull existing layers (ascending FIFO)
    $layers = $this->db->table($tbl_buffer)
        ->where('cont_item_id_unit_avail_id', $cont_id)
        ->where('method_id', $valmethod_id)
        ->orderBy('buffer_ball_id', 'asc')
        ->get()->getResultArray();

    if ($txn) {
        if ($txn['item_txn_drcr'] === 'd') {              // ▸ PURCHASE
            $rate = ($txn['item_txn_qty'] > 0)
                  ? $txn['item_txn_amount'] / $txn['item_txn_qty']
                  : 0;
            // push new layer
            $this->db->table($tbl_buffer)->insert([
                'cont_item_id_unit_avail_id' => $cont_id,
                'method_id'                  => $valmethod_id,
                'txn_id'                     => $txn['item_txn_id'],
                'buffer_ball_qty'            => parseAmount($txn['item_txn_qty']),
                'buffer_ball_rate'           => parseAmountPrice($rate,4),
                'valuation_id'               => 0,
                'voucher_txn_id'             => $txn['voucher_txn_id']
            ]);
			$buffer_ball_id = $this->db->insertID();
			 // push new record in bufflogrec_tbl
			 $buffer_ball_qty   = parseAmount($txn['item_txn_qty']); 
			$buffer_ball_rate  = parseAmountPrice($rate,4);  
			$valuation_id      = 0; 
			$op_valuation_id   = 0; 			
			$log_data          = array("buff_rev_log_id"=>0,"cont_item_id_unit_avail_id"=>$cont_id,
			                           "method_id"=>$valmethod_id,"buffer_ball_qty_rec"=>$buffer_ball_qty,"buffer_bal_rate_rec"=>$buffer_ball_rate,
							           "valuation_id"=>$valuation_id,"op_valuation_id"=>$op_valuation_id,"voucher_txn_id"=>$txn['voucher_txn_id'],
									   "item_id"=>$item_id);  
			$this->db->table($tbl_bufflogrec)->insert($log_data);
			$buffer_log_rec_id = $this->db->insertID();	

			$buffrevlog_info = $this->db->table($tbl_buffrevlog)->where('buffer_ball_id',$buffer_ball_id)->where("method_id",$valmethod_id)->where('voucher_txn_id',$txn['voucher_txn_id'])->where('cont_item_id_unit_avail_id',$cont_id)->get()->getRowArray();  
			$buff_rev_log_id =0;
			if($buffrevlog_info){
			  $buff_rev_log_id = $buffrevlog_info['buff_rev_log_id'];	
			}	
			$this->db->table($tbl_bufflogrec)->where('buffer_log_rec_id',$buffer_log_rec_id)->update(array('buff_rev_log_id'=>$buff_rev_log_id)); 
			 
			 /////////////////////////
			
			
            $item_value = $txn['item_txn_amount'];
        } else {                                          // ▸ SALE
            $need = parseAmount($txn['item_txn_qty']);
			$consumedLayers = [];          // [buffer_ball_id, qty, rate]
            foreach ($layers as $layer) {
                if ($need <= 0) break;
                $layQty = parseAmount($layer['buffer_ball_qty']);
                $layRate= parseAmountPrice($layer['buffer_ball_rate'],4);
                $consume = min($layQty, $need);
                $item_value += $consume * $layRate;
                // reduce layer
                if ($layQty == $consume) {
                    $this->db->table($tbl_buffer)
                        ->where('buffer_ball_id', $layer['buffer_ball_id'])
                        ->delete();
                } else {
                    $this->db->table($tbl_buffer)
                        ->where('buffer_ball_id', $layer['buffer_ball_id'])
                        ->update(['buffer_ball_qty' => $layQty - $consume]);
                }
				/* remember slice for logging */
                $consumedLayers[] = [
                    'buffer_ball_id' => $layer['buffer_ball_id'],
                    'qty'            => $consume,
                    'rate'           => $layRate
                ];
                $need -= $consume;
            }
            // if sale qty exceeds buffer (negative stock) fallback to opening rate or 0
            if ($need > 0) {
                $fallbackRate = ($open && $open['item_bal_qty']>0)
                             ? $open['item_value']/$open['item_bal_qty'] : 0;
                $item_value += $need * $fallbackRate;
            }
			
			/* ───── write logs for every slice consumed ───── */
            foreach ($consumedLayers as $cl) {

                /* 1️⃣  buffrevlog (ties sale txn ↔ layer) */
                $this->db->table($tbl_buffrevlog)->insert([
                    'cont_item_id_unit_avail_id' => $cont_id,
                    'method_id'                  => $valmethod_id,
                    'txn_id'                     => $txn['item_txn_id'],
                    'buffer_ball_id'             => $cl['buffer_ball_id'],
                    'buff_ball_rev_qty'          => $cl['qty'],
                    'valuation_id'               => 0,
                    'voucher_txn_id'             => $txn['voucher_txn_id'],
                    'op_valuation_id'            => 0
                ]);
                $buff_rev_log_id = $this->db->insertID();

                /* 2️⃣  mirror in bufflogrec (qty-OUT) */
                $this->db->table($tbl_bufflogrec)->insert([
                    'buff_rev_log_id'            => $buff_rev_log_id,
                    'cont_item_id_unit_avail_id' => $cont_id,
                    'method_id'                  => $valmethod_id,
                    'buffer_ball_qty_rec'        => $cl['qty'],
                    'buffer_bal_rate_rec'        => $cl['rate'],
                    'valuation_id'               => 0,
                    'op_valuation_id'            => 0,
                    'voucher_txn_id'             => $txn['voucher_txn_id'],
                    'item_id'                    => $item_id
                ]);
            }
			
			
        }
    } else {
        // no txn → just output opening valuation
        $item_value = $open['item_value'] ?? 0;
    }

    /* ------------------------------------------------------------
     * 4⃣  compute balance and remaining valuation after operation
     * ---------------------------------------------------------- */
    $layers_after = $this->db->table($tbl_buffer)
        ->where('cont_item_id_unit_avail_id', $cont_id)
        ->where('method_id', $valmethod_id)
        ->get()->getResultArray();

    $bal_after      = 0.0;
    $val_after_total= 0.0;
    foreach ($layers_after as $la) {
        $q = parseAmount($la['buffer_ball_qty']);
        $r = parseAmountPrice($la['buffer_ball_rate'],4);
        $bal_after       += $q;
        $val_after_total += $q * $r;
    }
    $bal_after = parseAmount($bal_after);
	

    // if this txn is a SALE we want repbon item_value to show remaining valuation
    if ($txn && $txn['item_txn_drcr'] === 'c') {
        $item_value_for_repbon = $val_after_total; // e.g. 40 qty × 10 = 400
    } else {
        $item_value_for_repbon = $item_value;      // purchase/opening uses txn value
    }
	/* 🆕─ final guard ─────────────────────────────────────────── */
	if ($bal_after <=0 || $txn['item_bal_qty']<=0) {
		$item_value_for_repbon = 0;   // <- forces both repbon & txnval to ₹0
	}
	
    /* ------------------------------------------------------------
     * 5⃣  upsert itemtxnval & repbon
     * ---------------------------------------------------------- */
    // itemtxnval
    $val_row = $this->db->table($tbl_txnval)
        ->where('item_txn_id', $txn['item_txn_id'] ?? 0)
        ->where('method_id',  $valmethod_id)
        ->get()->getRowArray();

    if ($val_row) {
        $valuation_id = $val_row['valuation_id'];
        $this->db->table($tbl_txnval)
            ->where('valuation_id', $valuation_id)
            ->update(['item_value'      => $item_value_for_repbon]);
    } else {
        $this->db->table($tbl_txnval)->insert([
            'item_id'      => $item_id,
            'unit_id'      => $item_unit,
            'mat_cent_id'  => $mat_cent_id,
            'item_txn_id'  => $txn['item_txn_id'] ?? 0,
            'voucher_txn_id'=> $txn['voucher_txn_id'] ?? 0,
            'voucher_date' => $txn['item_txn_date'] ?? date('Y-m-d'),
            'method_id'    => $valmethod_id,
            'item_value'   => $item_value_for_repbon
        ]);
        $valuation_id = $this->db->insertID();
    }

    // repbon helper
    $saveRep = function(int $method) use ($tbl_repbon,$cont_id,$txn,$valuation_id,$item_value_for_repbon,$bal_after,$bo_id,$item_unit) {
        $where = [
            'item_id_unit_id' => $cont_id,
            'voucher_txn_id'  => $txn['voucher_txn_id'] ?? 0,
            'item_txn_id'     => $txn['item_txn_id'] ?? 0,
            'method_id'       => $method
        ];
        $data = $where + [
            'item_value'    => $item_value_for_repbon,
            'item_bal_qty'  => $bal_after,
            'item_avail'    => $txn['item_avail'] ?? 1,
            'item_unit'     => $txn['item_unit'] ?? $GLOBALS['item_unit'],
            'valuation_id'  => $valuation_id,
            'bo_id'         => $bo_id,
            'item_txn_date' => $txn['item_txn_date'] ?? date('Y-m-d')
        ];
        $exist = $this->db->table($tbl_repbon)->where($where)->countAllResults();
        if ($exist) {
            $this->db->table($tbl_repbon)->where($where)->update($data);
        } else {
            $this->db->table($tbl_repbon)->insert($data);
        }
    };

    $saveRep($valmethod_id); // primary method
    $saveRep(0);            // copy for method_id 0

    /* ------------------------------------------------------------ */
    return [
        'item_id_unit_id' => $cont_id,
        'voucher_txn_id'  => $txn['voucher_txn_id'] ?? 0,
        'item_value'    => $item_value_for_repbon,
        'item_txn_id'     => $txn['item_txn_id'] ?? 0,
        'item_bal_qty'    => $bal_after,
        'item_unit'       => $item_unit,
        'item_avail'      => $item_avail,
        'valuation_id'    => $valuation_id,
        'item_txn_date'   => $txn['item_txn_date'] ?? date('Y-m-d')
    ];
}

   
   public function calculate_valuation_avg07_05_2025($item_id, $item_unit, $mat_cent_id, $batch_id,$op_bal_qty,$valmethod_id,$rewrite_tool,$voucher_txn_id,$item_txn_id=0,$bo_id=0)
	{		
	    if($bo_id==0 || $bo_id=='')
			$bo_id =$this->bo_id;
	    $itemrepall_tbl   = $this->company_id.'_itemrepall_'.$this->session->get('ses_comp_fy_id');	
		$itemrepmcn_tbl   = $this->company_id.'_itemrepmcn_'.$this->session->get('ses_comp_fy_id');	
		$itemrepgrp_tbl   = $this->company_id.'_itemrepgrp_'.$this->session->get('ses_comp_fy_id');	
		$itemrepcat_tbl   = $this->company_id.'_itemrepcat_'.$this->session->get('ses_comp_fy_id');	
		$itemrepprj_tbl   = $this->company_id.'_itemrepprj_'.$this->session->get('ses_comp_fy_id');	
		$itemrepbon_tbl   = $this->company_id.'_itemrepbon_'.$this->session->get('ses_comp_fy_id');	
		$itemrepbat_tbl   = $this->company_id.'_itemrepbat_'.$this->session->get('ses_comp_fy_id');	
		
		$item_info   = $this->get_item_info($item_id);	
		$op_avg_val  = $this->get_item_op_val($item_id,$item_unit,$mat_cent_id,$batch_id,1,$bo_id);
		$op_fifo_val = 0;//$this->get_item_op_val($item_id,$item_unit,$mat_cent_id,$batch_id,2);
		$op_lifo_val = 0;//$this->get_item_op_val($item_id,$item_unit,$mat_cent_id,$batch_id,3);
        $profit = 0; 
	    $itemtxnnnn_tbl = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
	    $builder = $this->db->table($itemtxnnnn_tbl); 
		$builder->where('item_id', $item_id);
		$builder->where('item_unit', $item_unit);
		$builder->where('mat_cent_id', $mat_cent_id);
		$builder->where('batch_id', $batch_id);
		$builder->where('voucher_txn_id', $voucher_txn_id);	
		if($item_txn_id)
		$builder->where('item_txn_id', $item_txn_id);	
		$builder->where('bo_id', $bo_id);
		if($rewrite_tool==1){
		$builder->orderBy('item_txn_date', 'asc');
		$builder->orderBy('voucher_txn_id', 'asc');
		$builder->orderBy('item_txn_id', 'asc');	
		}else{
		$builder->orderBy('item_txn_date', 'desc');
		$builder->orderBy('voucher_txn_id', 'desc');
		$builder->orderBy('item_txn_id', 'desc');
		}
		$builder->limit(1);
    	$value = $builder->get()->getRowArray();
		if($value){
    	$info = array("valmethod_id"=>$valmethod_id,"item_id"=>$item_id,"item_unit"=>$item_unit,"mat_cent_id"=>$mat_cent_id,
			           "batch_id"=>$batch_id,"op_bal_qty"=>$op_bal_qty,"op_avg_val"=>$op_avg_val,"item_txn_id"=>$value['item_txn_id'],
					   "item_txn_drcr"=>$value['item_txn_drcr'],"item_txn_qty"=>$value['item_txn_qty'],
					   "item_txn_date"=>$value['item_txn_date'],"voucher_txn_id"=>$value['voucher_txn_id'],"item_avail"=>$value['item_avail'],
					   "item_bal_qty"=>$value['item_bal_qty'],"item_txn_amount"=>$value['item_txn_amount'],"item_info"=>$item_info,
					   "op_lifo_val"=>$op_lifo_val,"op_fifo_val"=>$op_fifo_val
					   );				     
    	//$item_valuation_info = $this->update_item_avg_valuation_new($info,$valmethod_id,$rewrite_tool);//AVG.		              		 		    
		$item_valuation_info = $this->update_item_avg_valuation_buffernew($info,$valmethod_id,$rewrite_tool);//AVG.		              		 		    
		
		
		$item_avail = $item_valuation_info['item_avail'];
		
		
		if(($item_info['valmethod_id']==$valmethod_id) && ($rewrite_tool==0)){
		    
			
			$rpt_data     = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>$item_valuation_info['item_value'],"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],
							  "bo_id"=>$bo_id,"item_txn_date"=>$value['item_txn_date']);
			
			$isexists =$this->db->table($itemrepbon_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)			   
			$this->db->table($itemrepbon_tbl)->insert($rpt_data);
		   	
			$itemrepall_data     = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"item_txn_date"=>$value['item_txn_date']);
		$isexists =$this->db->table($itemrepall_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepall_tbl)->insert($itemrepall_data);
		
		$itemrepmcn_data     = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "mat_cent_id"=>$mat_cent_id,"item_txn_date"=>$value['item_txn_date']);
		$isexists =$this->db->table($itemrepmcn_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepmcn_tbl)->insert($itemrepmcn_data);
		
		
		$itemrepgrp_data  = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "item_grp_id"=>$item_info['item_grp_id'],"item_txn_date"=>$value['item_txn_date']);
		$isexists =$this->db->table($itemrepgrp_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepgrp_tbl)->insert($itemrepgrp_data);
		
		$itemrepcat_data  = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "item_cat"=>$item_info['item_cat'],"item_txn_date"=>$value['item_txn_date']);
		$isexists =$this->db->table($itemrepcat_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepcat_tbl)->insert($itemrepcat_data);
		
		$itemrepprj_data  = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "project_id"=>1,"item_txn_date"=>$value['item_txn_date']);
		$isexists =$this->db->table($itemrepprj_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepprj_tbl)->insert($itemrepprj_data);
			
		// get batch for items and unit id 
		 $GetItemBatchs = $this->GetItemBatchs($item_id);
		if($GetItemBatchs){
		  foreach($GetItemBatchs as $btrow){
			$batchid =   $btrow['batch_id'];
			$itemrepbat_data  = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "batch_id"=>$batchid,"item_txn_date"=>$value['item_txn_date']);
		    
			$isexists =$this->db->table($itemrepbat_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
			$this->db->table($itemrepbat_tbl)->insert($itemrepbat_data);
			}
			
			}	
		}
		if(($item_info['valmethod_id']==$valmethod_id) && ($rewrite_tool==2)){
		    
			
			$rpt_data     = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>$item_valuation_info['item_value'],"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],
							  "bo_id"=>$bo_id,"item_txn_date"=>$value['item_txn_date']);
			
			$isexists =$this->db->table($itemrepbon_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)			   
			$this->db->table($itemrepbon_tbl)->insert($rpt_data);
		   	
			$itemrepall_data     = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"item_txn_date"=>$value['item_txn_date']);
		$isexists =$this->db->table($itemrepall_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepall_tbl)->insert($itemrepall_data);
		
		$itemrepmcn_data     = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "mat_cent_id"=>$mat_cent_id,"item_txn_date"=>$value['item_txn_date']);
		$isexists =$this->db->table($itemrepmcn_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepmcn_tbl)->insert($itemrepmcn_data);
		
		
		$itemrepgrp_data  = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "item_grp_id"=>$item_info['item_grp_id'],"item_txn_date"=>$value['item_txn_date']);
		$isexists =$this->db->table($itemrepgrp_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepgrp_tbl)->insert($itemrepgrp_data);
		
		$itemrepcat_data  = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "item_cat"=>$item_info['item_cat'],"item_txn_date"=>$value['item_txn_date']);
		$isexists =$this->db->table($itemrepcat_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepcat_tbl)->insert($itemrepcat_data);
		
		$itemrepprj_data  = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "project_id"=>1,"item_txn_date"=>$value['item_txn_date']);
		$isexists =$this->db->table($itemrepprj_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepprj_tbl)->insert($itemrepprj_data);
			
		// get batch for items and unit id 
		 $GetItemBatchs = $this->GetItemBatchs($item_id);
		if($GetItemBatchs){
		  foreach($GetItemBatchs as $btrow){
			$batchid =   $btrow['batch_id'];
			$itemrepbat_data  = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "batch_id"=>$batchid,"item_txn_date"=>$value['item_txn_date']);
		    
			$isexists =$this->db->table($itemrepbat_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
			$this->db->table($itemrepbat_tbl)->insert($itemrepbat_data);
			}
			
			}	
		}
		return $item_valuation_info;
    	}
	  	
	  	
  	}
  	
  	
  	public function calculate_valuation_fifo(
    int  $item_id,
    int  $item_unit,
    int  $mat_cent_id,
    int  $batch_id,
    int  $opening_qty,      // kept for signature compatibility
    int  $valmethod_id,
    int  $rewrite_tool,
    int  $voucher_txn_id,
    int  $item_txn_id = 0,
    int  $bo_id        = 0
) {
    /* ──────────────────────────────────── helpers & table names */
    $fy   = $this->session->get('ses_comp_fy_id');
    $pref = $this->company_id . '_';

    $tbl_repbon     = $pref.'itemrepbon_'.$fy;
    $tbl_buffer     = $pref.'buffercalc_'.$fy;
    $tbl_buffrevlog = $pref.'buffrevlog_'.$fy;            // ➊  NEW
    $tbl_txn        = $pref.'itemtxnnnn_'.$item_id.'_'.$fy;
    $tbl_txnval     = $pref.'itemtxnval_'.$item_id.'_'.$fy;
	$tbl_bufflogrec   = $pref.'bufflogrec_'.$fy;

    if (!$bo_id) $bo_id = $this->bo_id;

    /* --------------------------------------------------- ① fetch txn */
    $builder = $this->db->table($tbl_txn)
        ->where([
            'item_id'        => $item_id,
            'item_unit'      => $item_unit,
            'mat_cent_id'    => $mat_cent_id,
            'batch_id'       => $batch_id,
            'voucher_txn_id' => $voucher_txn_id,
            'bo_id'          => $bo_id
        ])
        ->limit(1);

    if ($item_txn_id) $builder->where('item_txn_id', $item_txn_id);

    $orderDir = ($rewrite_tool == 1) ? 'asc' : 'desc';
    $builder->orderBy('item_txn_date', $orderDir)
            ->orderBy('voucher_txn_id', $orderDir)
            ->orderBy('item_txn_id',   $orderDir);

    $txn = $builder->get()->getRowArray();      // null on opening-only run

    $item_avail = $txn['item_avail'] ?? 1;                 // default container 1
    $cont_id    = $item_id.'_'.$item_unit.'_'.$item_avail; // composite key

    /* --------------------------------------------------- ② push opening */
    $open = $this->db->table($tbl_repbon)
        ->where('item_id_unit_id', $cont_id)
        ->where('method_id', 9)
        ->get()->getRowArray();

    if ($open && $open['item_bal_qty'] > 0) {
        $exists = $this->db->table($tbl_buffer)
            ->where('cont_item_id_unit_avail_id', $cont_id)
            ->where('method_id', $valmethod_id)
            ->where('txn_id', 0)
            ->countAllResults();
        if ($exists == 0) {
            $this->db->table($tbl_buffer)->insert([
                'cont_item_id_unit_avail_id' => $cont_id,
                'method_id'                  => $valmethod_id,
                'txn_id'                     => 0,
                'buffer_ball_qty'            => parseAmount($open['item_bal_qty']),
                'buffer_ball_rate'           => parseAmountPrice($open['item_value'] / $open['item_bal_qty'],4),
                'valuation_id'               => 0,
                'voucher_txn_id'             => 0
            ]);
        }
    }

    /* --------------------------------------------------- ③ purchase / sale */
    $item_value = 0;                     // monetary value of *this* txn
    $bal_after  = 0;                     // container balance after txn

    // fetch existing FIFO layers
    $layers = $this->db->table($tbl_buffer)
        ->where('cont_item_id_unit_avail_id', $cont_id)
        ->where('method_id', $valmethod_id)
        ->orderBy('buffer_ball_id', 'asc')
        ->get()->getResultArray();

    /* ----------- NEW: prepare array to hold layer-consumption log rows */
    $consumedLayers = [];                                            // ➋

    if ($txn) {
        if ($txn['item_txn_drcr'] === 'd') {          /* ▸ PURCHASE */
            $rate = ($txn['item_txn_qty'] > 0)
                   ? $txn['item_txn_amount'] / $txn['item_txn_qty']
                   : 0;
            $this->db->table($tbl_buffer)->insert([
                'cont_item_id_unit_avail_id' => $cont_id,
                'method_id'                  => $valmethod_id,
                'txn_id'                     => $txn['item_txn_id'],
                'buffer_ball_qty'            => parseAmount($txn['item_txn_qty']),
                'buffer_ball_rate'           => parseAmountPrice($rate,4),
                'valuation_id'               => 0,
                'voucher_txn_id'             => $txn['voucher_txn_id']
            ]);
			$buffer_ball_id = $this->db->insertID();
			 // push new record in bufflogrec_tbl
			 $buffer_ball_qty   = parseAmount($txn['item_txn_qty']); 
			$buffer_ball_rate  = parseAmountPrice($rate,4);  
			$valuation_id      = 0; 
			$op_valuation_id   = 0; 			
			$log_data          = array("buff_rev_log_id"=>0,"cont_item_id_unit_avail_id"=>$cont_id,
			                           "method_id"=>$valmethod_id,"buffer_ball_qty_rec"=>$buffer_ball_qty,"buffer_bal_rate_rec"=>$buffer_ball_rate,
							           "valuation_id"=>$valuation_id,"op_valuation_id"=>$op_valuation_id,"voucher_txn_id"=>$txn['voucher_txn_id'],
									   "item_id"=>$item_id);  
			$this->db->table($tbl_bufflogrec)->insert($log_data);
			$buffer_log_rec_id = $this->db->insertID();	

			$buffrevlog_info = $this->db->table($tbl_buffrevlog)->where('buffer_ball_id',$buffer_ball_id)->where("method_id",$valmethod_id)->where('voucher_txn_id',$txn['voucher_txn_id'])->where('cont_item_id_unit_avail_id',$cont_id)->get()->getRowArray();  
			$buff_rev_log_id =0;
			if($buffrevlog_info){
			  $buff_rev_log_id = $buffrevlog_info['buff_rev_log_id'];	
			}	
			$this->db->table($tbl_bufflogrec)->where('buffer_log_rec_id',$buffer_log_rec_id)->update(array('buff_rev_log_id'=>$buff_rev_log_id)); 
			 
			 /////////////////////////
            $item_value = $txn['item_txn_amount'];

        } else {                                      /* ▸ SALE */
            $need = parseAmount($txn['item_txn_qty']);			
            foreach ($layers as $layer) {
                if ($need <= 0) break;

                $layQty   = parseAmount($layer['buffer_ball_qty']);
                $layRate  = parseAmountPrice($layer['buffer_ball_rate'],4);
                $consume  = min($layQty, $need);

                $item_value += $consume * $layRate;

                /* --------- track what was taken from which layer (for log) */
                $consumedLayers[] = [                             // ➌
                    'buffer_ball_id' => $layer['buffer_ball_id'],
                    'qty'            => $consume,
					'rate'           => $layRate
                ];

                /* reduce / delete layer */
                if ($layQty == $consume) {
                    $this->db->table($tbl_buffer)
                        ->where('buffer_ball_id', $layer['buffer_ball_id'])
                        ->delete();
                } else {
                    $this->db->table($tbl_buffer)
                        ->where('buffer_ball_id', $layer['buffer_ball_id'])
                        ->update(['buffer_ball_qty' => $layQty - $consume]);
                }
                $need -= $consume;
            }

            /* negative stock safety */
            if ($need > 0) {
                $fallbackRate = ($open && $open['item_bal_qty']>0)
                               ? $open['item_value']/$open['item_bal_qty'] : 0;
                $item_value += $need * $fallbackRate;
            }
			/* ───── write logs for every slice consumed ───── */
            foreach ($consumedLayers as $cl) {

                /* 1️⃣  buffrevlog (ties sale txn ↔ layer) */
                $this->db->table($tbl_buffrevlog)->insert([
                    'cont_item_id_unit_avail_id' => $cont_id,
                    'method_id'                  => $valmethod_id,
                    'txn_id'                     => $txn['item_txn_id'],
                    'buffer_ball_id'             => $cl['buffer_ball_id'],
                    'buff_ball_rev_qty'          => $cl['qty'],
                    'valuation_id'               => 0,
                    'voucher_txn_id'             => $txn['voucher_txn_id'],
                    'op_valuation_id'            => 0
                ]);
                $buff_rev_log_id = $this->db->insertID();

                /* 2️⃣  mirror in bufflogrec (qty-OUT) */
                $this->db->table($tbl_bufflogrec)->insert([
                    'buff_rev_log_id'            => $buff_rev_log_id,
                    'cont_item_id_unit_avail_id' => $cont_id,
                    'method_id'                  => $valmethod_id,
                    'buffer_ball_qty_rec'        => $cl['qty'],
                    'buffer_bal_rate_rec'        => $cl['rate'],
                    'valuation_id'               => 0,
                    'op_valuation_id'            => 0,
                    'voucher_txn_id'             => $txn['voucher_txn_id'],
                    'item_id'                    => $item_id
                ]);
            }
        }
    } else {
        /* opening-only valuation */
        $item_value = $open['item_value'] ?? 0;
    }

    /* --------------------------------------------------- ④ balance calc */
    $layers_after = $this->db->table($tbl_buffer)
        ->where('cont_item_id_unit_avail_id', $cont_id)
        ->where('method_id', $valmethod_id)
        ->get()->getResultArray();

    $bal_after       = 0.0;
    $val_after_total = 0.0;
    foreach ($layers_after as $la) {
        $q = parseAmount($la['buffer_ball_qty']);
        $r = parseAmountPrice($la['buffer_ball_rate'],4);
        $bal_after       += $q;
        $val_after_total += $q * $r;
    }
    $bal_after = parseAmount($bal_after);

    $item_value_for_repbon = ($txn && $txn['item_txn_drcr'] === 'c')
                           ? $val_after_total           // remaining valu
                           : $item_value;               // purchase/opening


   /* 🆕─ final guard ─────────────────────────────────────────── */
	if ($bal_after <=0 || $txn['item_bal_qty']<=0) {
		$item_value_for_repbon = 0;   // <- forces both repbon & txnval to ₹0
	}

    /* --------------------------------------------------- ⑤ upsert valuation */
    $val_row = $this->db->table($tbl_txnval)
        ->where('item_txn_id', $txn['item_txn_id'] ?? 0)
        ->where('method_id',  $valmethod_id)
        ->get()->getRowArray();

    if ($val_row) {
        $valuation_id = $val_row['valuation_id'];
        $this->db->table($tbl_txnval)
                 ->where('valuation_id', $valuation_id)
                 ->update(['item_value' => $item_value_for_repbon]);
    } else {
        $this->db->table($tbl_txnval)->insert([
            'item_id'        => $item_id,
            'unit_id'        => $item_unit,
            'mat_cent_id'    => $mat_cent_id,
            'item_txn_id'    => $txn['item_txn_id'] ?? 0,
            'voucher_txn_id' => $txn['voucher_txn_id'] ?? 0,
            'voucher_date'   => $txn['item_txn_date'] ?? date('Y-m-d'),
            'method_id'      => $valmethod_id,
            'item_value'     => $item_value_for_repbon
        ]);
        $valuation_id = $this->db->insertID();
    }

    /* --------------------------------------------------- ⑥ NEW: write reverse-log */
    if ($txn && $txn['item_txn_drcr'] === 'c' && !empty($consumedLayers)) {     // ➍
        $logRows = [];
        foreach ($consumedLayers as $c) {
            $logRows[] = [
                'cont_item_id_unit_avail_id' => $cont_id,
                'method_id'                  => $valmethod_id,
                'txn_id'                     => $txn['item_txn_id'],
                'buffer_ball_id'             => $c['buffer_ball_id'],
                'buff_ball_rev_qty'          => $c['qty'],
                'valuation_id'               => $valuation_id,
                'voucher_txn_id'             => $txn['voucher_txn_id'],
                'op_valuation_id'            => 0               // keep 0 unless you use it
            ];
        }
        $this->db->table($tbl_buffrevlog)->insertBatch($logRows);
    }

    /* --------------------------------------------------- ⑦ repbon helper */
    $saveRep = function(int $method) use (
        $tbl_repbon,$cont_id,$txn,$valuation_id,$item_value_for_repbon,
        $bal_after,$bo_id,$item_unit
    ) {
        $where = [
            'item_id_unit_id' => $cont_id,
            'voucher_txn_id'  => $txn['voucher_txn_id'] ?? 0,
            'item_txn_id'     => $txn['item_txn_id'] ?? 0,
            'method_id'       => $method
        ];
        $data = $where + [
            'item_value'    => $item_value_for_repbon,
            'item_bal_qty'  => $bal_after,
            'item_avail'    => $txn['item_avail'] ?? 1,
            'item_unit'     => $txn['item_unit'] ?? $GLOBALS['item_unit'],
            'valuation_id'  => $valuation_id,
            'bo_id'         => $bo_id,
            'item_txn_date' => $txn['item_txn_date'] ?? date('Y-m-d')
        ];
        $exist = $this->db->table($tbl_repbon)->where($where)->countAllResults();
        if ($exist) {
            $this->db->table($tbl_repbon)->where($where)->update($data);
        } else {
            $this->db->table($tbl_repbon)->insert($data);
        }
    };

    $saveRep($valmethod_id);   // primary
    $saveRep(0);               // mirror copy

    /* --------------------------------------------------- ⑧ return */
    return [
        'item_id_unit_id' => $cont_id,
        'voucher_txn_id'  => $txn['voucher_txn_id'] ?? 0,
        'item_value'      => $item_value_for_repbon,
        'item_txn_id'     => $txn['item_txn_id'] ?? 0,
        'item_bal_qty'    => $bal_after,
        'item_unit'       => $item_unit,
        'item_avail'      => $item_avail,
        'valuation_id'    => $valuation_id,
        'item_txn_date'   => $txn['item_txn_date'] ?? date('Y-m-d')
    ];
}

  	
	
   public function calculate_valuation_fifo_10_05_2025($item_id, $item_unit, $mat_cent_id, $batch_id,$op_bal_qty,$valmethod_id,$rewrite_tool,$voucher_txn_id,$item_txn_id=0,$bo_id=0)
	{	
	     if($bo_id==0 || $bo_id=='')
			$bo_id =$this->bo_id;
		
        $itemrepall_tbl   = $this->company_id.'_itemrepall_'.$this->session->get('ses_comp_fy_id');	
		$itemrepmcn_tbl   = $this->company_id.'_itemrepmcn_'.$this->session->get('ses_comp_fy_id');	
		$itemrepgrp_tbl   = $this->company_id.'_itemrepgrp_'.$this->session->get('ses_comp_fy_id');	
		$itemrepcat_tbl   = $this->company_id.'_itemrepcat_'.$this->session->get('ses_comp_fy_id');	
		$itemrepprj_tbl   = $this->company_id.'_itemrepprj_'.$this->session->get('ses_comp_fy_id');	
	    $itemrepbon_tbl   = $this->company_id.'_itemrepbon_'.$this->session->get('ses_comp_fy_id');	
		$itemrepbat_tbl   = $this->company_id.'_itemrepbat_'.$this->session->get('ses_comp_fy_id');	
		
		$item_info   = $this->get_item_info($item_id);	
		$op_avg_val  = $this->get_item_op_val($item_id,$item_unit,$mat_cent_id,$batch_id,1);
		$op_fifo_val = $this->get_item_op_val($item_id,$item_unit,$mat_cent_id,$batch_id,2);
		$op_lifo_val = $this->get_item_op_val($item_id,$item_unit,$mat_cent_id,$batch_id,3);
       
	    $itemtxnnnn_tbl = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
	    SaveErrorLog("calculating reporting tables ...".$itemtxnnnn_tbl);
	    $builder = $this->db->table($itemtxnnnn_tbl); 
		$builder->where('item_id', $item_id);
		$builder->where('item_unit', $item_unit);
		$builder->where('mat_cent_id', $mat_cent_id);
		$builder->where('batch_id', $batch_id);
        $builder->where('voucher_txn_id', $voucher_txn_id);	
        if($item_txn_id)
		$builder->where('item_txn_id', $item_txn_id);	
		$builder->where('bo_id', $bo_id);
		if($rewrite_tool==1){
		$builder->orderBy('item_txn_date', 'asc');
		$builder->orderBy('voucher_txn_id', 'asc');
		$builder->orderBy('item_txn_id', 'asc');	
		}else{
		$builder->orderBy('item_txn_date', 'desc');
		$builder->orderBy('voucher_txn_id', 'desc');
		$builder->orderBy('item_txn_id', 'desc');
		}
		$builder->limit(1);
    	$value = $builder->get()->getRowArray();
		
		$item_valuation_info=array();
		
		if($value){
			if($op_bal_qty=='')
				 $op_bal_qty=0;
			 if($op_avg_val=='')
				 $op_avg_val=0;
			 if($op_lifo_val=='')
				 $op_lifo_val=0;
    		 if($op_fifo_val=='')
				 $op_fifo_val=0;	
			$info = array("valmethod_id"=>$valmethod_id,"item_id"=>$item_id,"item_unit"=>$item_unit,"mat_cent_id"=>$mat_cent_id,
			           "batch_id"=>$batch_id,"op_bal_qty"=>$op_bal_qty,"op_avg_val"=>$op_avg_val,"item_txn_id"=>$value['item_txn_id'],
					   "item_txn_drcr"=>$value['item_txn_drcr'],"item_txn_qty"=>$value['item_txn_qty'],
					   "item_txn_date"=>$value['item_txn_date'],"voucher_txn_id"=>$value['voucher_txn_id'],"item_avail"=>$value['item_avail'],
					   "item_bal_qty"=>$value['item_bal_qty'],"item_txn_amount"=>$value['item_txn_amount'],"item_info"=>$item_info,
					   "op_lifo_val"=>$op_lifo_val,"op_fifo_val"=>$op_fifo_val
					   );				     
    	$item_valuation_info =   $this->update_item_fifo_valuation_new($info,$valmethod_id,$rewrite_tool);// FIFO			 
    	$item_avail = $item_valuation_info['item_avail'];     
    	if(($item_info['valmethod_id']==$valmethod_id) && $rewrite_tool==0){
			$rpt_data     = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>$item_valuation_info['item_value'],"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],
							  "bo_id"=>$bo_id,"item_txn_date"=>$item_valuation_info['item_txn_date']);
			$isexists =$this->db->table($itemrepbon_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
			$this->db->table($itemrepbon_tbl)->insert($rpt_data);
			
			$itemrepall_data = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"item_txn_date"=>$item_valuation_info['item_txn_date']);
		$isexists =$this->db->table($itemrepall_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepall_tbl)->insert($itemrepall_data);
			
			$itemrepmcn_data     = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "mat_cent_id"=>$mat_cent_id,"item_txn_date"=>$item_valuation_info['item_txn_date']);
		$isexists =$this->db->table($itemrepmcn_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepmcn_tbl)->insert($itemrepmcn_data);
		
		
		$itemrepgrp_data  = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "item_grp_id"=>$item_info['item_grp_id'],"item_txn_date"=>$item_valuation_info['item_txn_date']);
		$isexists =$this->db->table($itemrepgrp_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepgrp_tbl)->insert($itemrepgrp_data);
		
		$itemrepcat_data  = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "item_cat"=>$item_info['item_cat'],"item_txn_date"=>$item_valuation_info['item_txn_date']);
		$isexists =$this->db->table($itemrepcat_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepcat_tbl)->insert($itemrepcat_data);
		
		$itemrepprj_data  = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "project_id"=>1,"item_txn_date"=>$item_valuation_info['item_txn_date']);
		$isexists =$this->db->table($itemrepprj_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepprj_tbl)->insert($itemrepprj_data);
		
		// get batch for items and unit id 
		 $GetItemBatchs = $this->GetItemBatchs($item_id);
		if($GetItemBatchs){
		  foreach($GetItemBatchs as $btrow){
			$batchid =   $btrow['batch_id'];
			$itemrepbat_data  = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "batch_id"=>$batchid,"item_txn_date"=>$item_valuation_info['item_txn_date']);
		    $isexists =$this->db->table($itemrepbat_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
			$this->db->table($itemrepbat_tbl)->insert($itemrepbat_data);
			}
			
			}
			
		}
		if(($item_info['valmethod_id']==$valmethod_id) && $rewrite_tool==2){
			$rpt_data     = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>$item_valuation_info['item_value'],"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],
							  "bo_id"=>$bo_id,"item_txn_date"=>$item_valuation_info['item_txn_date']);
			$isexists =$this->db->table($itemrepbon_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
			$this->db->table($itemrepbon_tbl)->insert($rpt_data);
			
			$itemrepall_data = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"item_txn_date"=>$item_valuation_info['item_txn_date']);
		$isexists =$this->db->table($itemrepall_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepall_tbl)->insert($itemrepall_data);
			
			$itemrepmcn_data     = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "mat_cent_id"=>$mat_cent_id,"item_txn_date"=>$item_valuation_info['item_txn_date']);
		$isexists =$this->db->table($itemrepmcn_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepmcn_tbl)->insert($itemrepmcn_data);
		
		
		$itemrepgrp_data  = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "item_grp_id"=>$item_info['item_grp_id'],"item_txn_date"=>$item_valuation_info['item_txn_date']);
		$isexists =$this->db->table($itemrepgrp_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepgrp_tbl)->insert($itemrepgrp_data);
		
		$itemrepcat_data  = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "item_cat"=>$item_info['item_cat'],"item_txn_date"=>$item_valuation_info['item_txn_date']);
		$isexists =$this->db->table($itemrepcat_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepcat_tbl)->insert($itemrepcat_data);
		
		$itemrepprj_data  = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "project_id"=>1,"item_txn_date"=>$item_valuation_info['item_txn_date']);
		$isexists =$this->db->table($itemrepprj_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepprj_tbl)->insert($itemrepprj_data);
		
		// get batch for items and unit id 
		 $GetItemBatchs = $this->GetItemBatchs($item_id);
		if($GetItemBatchs){
		  foreach($GetItemBatchs as $btrow){
			$batchid =   $btrow['batch_id'];
			$itemrepbat_data  = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "batch_id"=>$batchid,"item_txn_date"=>$item_valuation_info['item_txn_date']);
		    $isexists =$this->db->table($itemrepbat_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
			$this->db->table($itemrepbat_tbl)->insert($itemrepbat_data);
			}
			
			}
			
		}
	  return $item_valuation_info;	
	}
    
  	}
  	
  	
  public function calculate_valuation_lifo(
    int  $item_id,
    int  $item_unit,
    int  $mat_cent_id,
    int  $batch_id,
    int  $opening_qty,      // kept for signature compatibility
    int  $valmethod_id,
    int  $rewrite_tool,
    int  $voucher_txn_id,
    int  $item_txn_id = 0,
    int  $bo_id        = 0
) {
    /* ──────────────────────────────────── helpers & table names */
    $fy   = $this->session->get('ses_comp_fy_id');
    $pref = $this->company_id . '_';

    $tbl_repbon     = $pref.'itemrepbon_'.$fy;
    $tbl_buffer     = $pref.'buffercalc_'.$fy;
    $tbl_buffrevlog = $pref.'buffrevlog_'.$fy;            // ➊
    $tbl_txn        = $pref.'itemtxnnnn_'.$item_id.'_'.$fy;
    $tbl_txnval     = $pref.'itemtxnval_'.$item_id.'_'.$fy;
	$tbl_bufflogrec   = $pref.'bufflogrec_'.$fy;

    if (!$bo_id) $bo_id = $this->bo_id;

    /* --------------------------------------------------- ① fetch txn */
    $builder = $this->db->table($tbl_txn)
        ->where([
            'item_id'        => $item_id,
            'item_unit'      => $item_unit,
            'mat_cent_id'    => $mat_cent_id,
            'batch_id'       => $batch_id,
            'voucher_txn_id' => $voucher_txn_id,
            'bo_id'          => $bo_id
        ])
        ->limit(1);

    if ($item_txn_id) $builder->where('item_txn_id', $item_txn_id);

    $orderDir = ($rewrite_tool == 1) ? 'asc' : 'desc';
    $builder->orderBy('item_txn_date', $orderDir)
            ->orderBy('voucher_txn_id', $orderDir)
            ->orderBy('item_txn_id',   $orderDir);

    $txn = $builder->get()->getRowArray();      // null on opening-only run

    $item_avail = $txn['item_avail'] ?? 1;                 // default container 1
    $cont_id    = $item_id.'_'.$item_unit.'_'.$item_avail; // composite key

    /* --------------------------------------------------- ② push opening */
    $open = $this->db->table($tbl_repbon)
        ->where('item_id_unit_id', $cont_id)
        ->where('method_id', 9)
        ->get()->getRowArray();

    if ($open && $open['item_bal_qty'] > 0) {
        $exists = $this->db->table($tbl_buffer)
            ->where('cont_item_id_unit_avail_id', $cont_id)
            ->where('method_id', $valmethod_id)
            ->where('txn_id', 0)
            ->countAllResults();
        if ($exists == 0) {
            $this->db->table($tbl_buffer)->insert([
                'cont_item_id_unit_avail_id' => $cont_id,
                'method_id'                  => $valmethod_id,
                'txn_id'                     => 0,
                'buffer_ball_qty'            => parseAmount($open['item_bal_qty']),
                'buffer_ball_rate'           => parseAmountPrice($open['item_value'] / $open['item_bal_qty'],4),
                'valuation_id'               => 0,
                'voucher_txn_id'             => 0
            ]);
        }
    }

    /* --------------------------------------------------- ③ purchase / sale */
    $item_value = 0;                     // monetary value of *this* txn
    $bal_after  = 0;                     // container balance after txn

    // fetch **existing LIFO layers**  (latest buffer_ball_id first)
    $layers = $this->db->table($tbl_buffer)
        ->where('cont_item_id_unit_avail_id', $cont_id)
        ->where('method_id', $valmethod_id)
        ->orderBy('buffer_ball_id', 'desc')     // <-- LIFO CHANGE
        ->get()->getResultArray();

    /* ----------- prepare array to hold layer-consumption log rows */
    $consumedLayers = [];

    if ($txn) {
        if ($txn['item_txn_drcr'] === 'd') {          /* ▸ PURCHASE */
            $rate = ($txn['item_txn_qty'] > 0)
                   ? $txn['item_txn_amount'] / $txn['item_txn_qty']
                   : 0;
            $this->db->table($tbl_buffer)->insert([
                'cont_item_id_unit_avail_id' => $cont_id,
                'method_id'                  => $valmethod_id,
                'txn_id'                     => $txn['item_txn_id'],
                'buffer_ball_qty'            => parseAmount($txn['item_txn_qty']),
                'buffer_ball_rate'           => parseAmountPrice($rate,4),
                'valuation_id'               => 0,
                'voucher_txn_id'             => $txn['voucher_txn_id']
            ]);
			$buffer_ball_id = $this->db->insertID();
			 // push new record in bufflogrec_tbl
			 $buffer_ball_qty   = parseAmount($txn['item_txn_qty']); 
			$buffer_ball_rate  = parseAmountPrice($rate,4);  
			$valuation_id      = 0; 
			$op_valuation_id   = 0; 			
			$log_data          = array("buff_rev_log_id"=>0,"cont_item_id_unit_avail_id"=>$cont_id,
			                           "method_id"=>$valmethod_id,"buffer_ball_qty_rec"=>$buffer_ball_qty,"buffer_bal_rate_rec"=>$buffer_ball_rate,
							           "valuation_id"=>$valuation_id,"op_valuation_id"=>$op_valuation_id,"voucher_txn_id"=>$txn['voucher_txn_id'],
									   "item_id"=>$item_id);  
			$this->db->table($tbl_bufflogrec)->insert($log_data);
			$buffer_log_rec_id = $this->db->insertID();	

			$buffrevlog_info = $this->db->table($tbl_buffrevlog)->where('buffer_ball_id',$buffer_ball_id)->where("method_id",$valmethod_id)->where('voucher_txn_id',$txn['voucher_txn_id'])->where('cont_item_id_unit_avail_id',$cont_id)->get()->getRowArray();  
			$buff_rev_log_id =0;
			if($buffrevlog_info){
			  $buff_rev_log_id = $buffrevlog_info['buff_rev_log_id'];	
			}	
			$this->db->table($tbl_bufflogrec)->where('buffer_log_rec_id',$buffer_log_rec_id)->update(array('buff_rev_log_id'=>$buff_rev_log_id)); 
			 
			 /////////////////////////
            $item_value = $txn['item_txn_amount'];

        } else {                                      /* ▸ SALE  (LIFO) */
            $need = parseAmount($txn['item_txn_qty']);

            foreach ($layers as $layer) {             // layers already newest→oldest
                if ($need <= 0) break;

                $layQty   = parseAmount($layer['buffer_ball_qty']);
                $layRate  = parseAmountPrice($layer['buffer_ball_rate'],4);
                $consume  = min($layQty, $need);

                $item_value += $consume * $layRate;

                /* track what was taken from which layer (for log) */
                $consumedLayers[] = [
                    'buffer_ball_id' => $layer['buffer_ball_id'],
                    'qty'            => $consume,
					'rate'           => $layRate
                ];

                /* reduce / delete layer */
                if ($layQty == $consume) {
                    $this->db->table($tbl_buffer)
                        ->where('buffer_ball_id', $layer['buffer_ball_id'])
                        ->delete();
                } else {
                    $this->db->table($tbl_buffer)
                        ->where('buffer_ball_id', $layer['buffer_ball_id'])
                        ->update(['buffer_ball_qty' => $layQty - $consume]);
                }
                $need -= $consume;
            }

            /* negative stock safety */
            if ($need > 0) {
                $fallbackRate = ($open && $open['item_bal_qty']>0)
                               ? $open['item_value']/$open['item_bal_qty'] : 0;
                $item_value += $need * $fallbackRate;
            }
			/* ───── write logs for every slice consumed ───── */
            foreach ($consumedLayers as $cl) {

                /* 1️⃣  buffrevlog (ties sale txn ↔ layer) */
                $this->db->table($tbl_buffrevlog)->insert([
                    'cont_item_id_unit_avail_id' => $cont_id,
                    'method_id'                  => $valmethod_id,
                    'txn_id'                     => $txn['item_txn_id'],
                    'buffer_ball_id'             => $cl['buffer_ball_id'],
                    'buff_ball_rev_qty'          => $cl['qty'],
                    'valuation_id'               => 0,
                    'voucher_txn_id'             => $txn['voucher_txn_id'],
                    'op_valuation_id'            => 0
                ]);
                $buff_rev_log_id = $this->db->insertID();

                /* 2️⃣  mirror in bufflogrec (qty-OUT) */
                $this->db->table($tbl_bufflogrec)->insert([
                    'buff_rev_log_id'            => $buff_rev_log_id,
                    'cont_item_id_unit_avail_id' => $cont_id,
                    'method_id'                  => $valmethod_id,
                    'buffer_ball_qty_rec'        => $cl['qty'],
                    'buffer_bal_rate_rec'        => $cl['rate'],
                    'valuation_id'               => 0,
                    'op_valuation_id'            => 0,
                    'voucher_txn_id'             => $txn['voucher_txn_id'],
                    'item_id'                    => $item_id
                ]);
            }
        }
    } else {
        /* opening-only valuation */
        $item_value = $open['item_value'] ?? 0;
    }

    /* --------------------------------------------------- ④ balance calc */
    $layers_after = $this->db->table($tbl_buffer)
        ->where('cont_item_id_unit_avail_id', $cont_id)
        ->where('method_id', $valmethod_id)
        ->get()->getResultArray();

    $bal_after       = 0.0;
    $val_after_total = 0.0;
    foreach ($layers_after as $la) {
        $q = parseAmount($la['buffer_ball_qty']);
        $r = parseAmountPrice($la['buffer_ball_rate'],4);
        $bal_after       += $q;
        $val_after_total += $q * $r;
    }
    $bal_after = parseAmount($bal_after);

    $item_value_for_repbon = ($txn && $txn['item_txn_drcr'] === 'c')
                           ? $val_after_total           // remaining value
                           : $item_value;               // purchase/opening

	/* 🆕─ final guard ─────────────────────────────────────────── */
	if ($bal_after <=0 || $txn['item_bal_qty']<=0) {
		$item_value_for_repbon = 0;   // <- forces both repbon & txnval to ₹0
	}

    /* --------------------------------------------------- ⑤ upsert valuation */
    $val_row = $this->db->table($tbl_txnval)
        ->where('item_txn_id', $txn['item_txn_id'] ?? 0)
        ->where('method_id',  $valmethod_id)
        ->get()->getRowArray(); 

    if ($val_row) {
        $valuation_id = $val_row['valuation_id'];
        $this->db->table($tbl_txnval)
                 ->where('valuation_id', $valuation_id)
                 ->update(['item_value' => $item_value_for_repbon]);
    } else {
        $this->db->table($tbl_txnval)->insert([
            'item_id'        => $item_id,
            'unit_id'        => $item_unit,
            'mat_cent_id'    => $mat_cent_id,
            'item_txn_id'    => $txn['item_txn_id'] ?? 0,
            'voucher_txn_id' => $txn['voucher_txn_id'] ?? 0,
            'voucher_date'   => $txn['item_txn_date'] ?? date('Y-m-d'),
            'method_id'      => $valmethod_id,
            'item_value'     => $item_value_for_repbon
        ]);
        $valuation_id = $this->db->insertID();
    }

    /* --------------------------------------------------- ⑥ write reverse-log */
    if ($txn && $txn['item_txn_drcr'] === 'c' && !empty($consumedLayers)) {
        $logRows = [];
        foreach ($consumedLayers as $c) {
            $logRows[] = [
                'cont_item_id_unit_avail_id' => $cont_id,
                'method_id'                  => $valmethod_id,
                'txn_id'                     => $txn['item_txn_id'],
                'buffer_ball_id'             => $c['buffer_ball_id'],
                'buff_ball_rev_qty'          => $c['qty'],
                'valuation_id'               => $valuation_id,
                'voucher_txn_id'             => $txn['voucher_txn_id'],
                'op_valuation_id'            => 0
            ];
        }
        $this->db->table($tbl_buffrevlog)->insertBatch($logRows);
    }

    /* --------------------------------------------------- ⑦ repbon helper */
    $saveRep = function(int $method) use (
        $tbl_repbon,$cont_id,$txn,$valuation_id,$item_value_for_repbon,
        $bal_after,$bo_id,$item_unit
    ) {
        $where = [
            'item_id_unit_id' => $cont_id,
            'voucher_txn_id'  => $txn['voucher_txn_id'] ?? 0,
            'item_txn_id'     => $txn['item_txn_id'] ?? 0,
            'method_id'       => $method
        ];
        $data = $where + [
            'item_value'    => $item_value_for_repbon,
            'item_bal_qty'  => $bal_after,
            'item_avail'    => $txn['item_avail'] ?? 1,
            'item_unit'     => $txn['item_unit'] ?? $GLOBALS['item_unit'],
            'valuation_id'  => $valuation_id,
            'bo_id'         => $bo_id,
            'item_txn_date' => $txn['item_txn_date'] ?? date('Y-m-d')
        ];
        $exist = $this->db->table($tbl_repbon)->where($where)->countAllResults();
        if ($exist) {
            $this->db->table($tbl_repbon)->where($where)->update($data);
        } else {
            $this->db->table($tbl_repbon)->insert($data);
        }
    };

    $saveRep($valmethod_id);   // primary
    $saveRep(0);               // mirror copy

    /* --------------------------------------------------- ⑧ return */
    return [
        'item_id_unit_id' => $cont_id,
        'voucher_txn_id'  => $txn['voucher_txn_id'] ?? 0,
        'item_value'      => $item_value_for_repbon,
        'item_txn_id'     => $txn['item_txn_id'] ?? 0,
        'item_bal_qty'    => $bal_after,
        'item_unit'       => $item_unit,
        'item_avail'      => $item_avail,
        'valuation_id'    => $valuation_id,
        'item_txn_date'   => $txn['item_txn_date'] ?? date('Y-m-d')
    ];
}


   public function calculate_valuation_lifo_10_05_2025($item_id, $item_unit, $mat_cent_id, $batch_id,$op_bal_qty,$valmethod_id,$rewrite_tool,$voucher_txn_id,$item_txn_id=0,$bo_id=0)
	{		
		 if($bo_id==0 || $bo_id=='')
			$bo_id =$this->bo_id;
	    $itemrepall_tbl   = $this->company_id.'_itemrepall_'.$this->session->get('ses_comp_fy_id');	
		$itemrepmcn_tbl   = $this->company_id.'_itemrepmcn_'.$this->session->get('ses_comp_fy_id');	
		$itemrepgrp_tbl   = $this->company_id.'_itemrepgrp_'.$this->session->get('ses_comp_fy_id');	
		$itemrepcat_tbl   = $this->company_id.'_itemrepcat_'.$this->session->get('ses_comp_fy_id');	
		$itemrepprj_tbl   = $this->company_id.'_itemrepprj_'.$this->session->get('ses_comp_fy_id');	
	    $itemrepbon_tbl   = $this->company_id.'_itemrepbon_'.$this->session->get('ses_comp_fy_id');	
		$itemrepbat_tbl   = $this->company_id.'_itemrepbat_'.$this->session->get('ses_comp_fy_id');	
		
		$item_info   = $this->get_item_info($item_id);	
		$op_avg_val  = 0;//$this->get_item_op_val($item_id,$item_unit,$mat_cent_id,$batch_id,1);
		$op_fifo_val = 0;//$this->get_item_op_val($item_id,$item_unit,$mat_cent_id,$batch_id,2);
		$op_lifo_val = 0;//$this->get_item_op_val($item_id,$item_unit,$mat_cent_id,$batch_id,3);

	    $itemtxnnnn_tbl = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
	    $builder = $this->db->table($itemtxnnnn_tbl); 
		$builder->where('item_id', $item_id);
		$builder->where('item_unit', $item_unit);
		$builder->where('mat_cent_id', $mat_cent_id);
		$builder->where('batch_id', $batch_id);	
		$builder->where('voucher_txn_id', $voucher_txn_id);
		if($item_txn_id)
		$builder->where('item_txn_id', $item_txn_id);	
		$builder->where('bo_id', $bo_id);
		if($rewrite_tool==1){
		$builder->orderBy('item_txn_date', 'asc');
		$builder->orderBy('voucher_txn_id', 'asc');
		$builder->orderBy('item_txn_id', 'asc');	
		}else{
		$builder->orderBy('item_txn_date', 'desc');
		$builder->orderBy('voucher_txn_id', 'desc');
		$builder->orderBy('item_txn_id', 'desc');
		}
		$builder->limit(1);
    	$value = $builder->get()->getRowArray();
    	if($value){
		//echo $rewrite_tool.'==>'.$this->db->GetLastQuery();
			if($op_bal_qty=='')
				 $op_bal_qty=0;
			 if($op_avg_val=='')
				 $op_avg_val=0;
			 if($op_lifo_val=='')
				 $op_lifo_val=0;
			 
		$info = array("valmethod_id"=>$valmethod_id,"item_id"=>$item_id,"item_unit"=>$item_unit,"mat_cent_id"=>$mat_cent_id,
			           "batch_id"=>$batch_id,"op_bal_qty"=>$op_bal_qty,"op_avg_val"=>$op_avg_val,"item_txn_id"=>$value['item_txn_id'],
					   "item_txn_drcr"=>$value['item_txn_drcr'],"item_txn_qty"=>$value['item_txn_qty'],
					   "item_txn_date"=>$value['item_txn_date'],"voucher_txn_id"=>$value['voucher_txn_id'],"item_avail"=>$value['item_avail'],
					   "item_bal_qty"=>$value['item_bal_qty'],"item_txn_amount"=>$value['item_txn_amount'],"item_info"=>$item_info,
					   "op_lifo_val"=>$op_lifo_val,"op_fifo_val"=>$op_fifo_val
					   );				     
    		$item_valuation_info =  $this->update_item_lifo_valuation_new($info,$valmethod_id,$rewrite_tool);//LIFO 		    
    	   $item_avail = $item_valuation_info['item_avail'];
		 
			if(($item_info['valmethod_id']==$valmethod_id) && ($rewrite_tool==0)){
			$rpt_data     = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>$item_valuation_info['item_value'],"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],
							  "bo_id"=>$bo_id,"item_txn_date"=>$item_valuation_info['item_txn_date']);
			$isexists =$this->db->table($itemrepbon_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
			$this->db->table($itemrepbon_tbl)->insert($rpt_data);
			
			
			$itemrepall_data     = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"item_txn_date"=>$item_valuation_info['item_txn_date']);
		$isexists =$this->db->table($itemrepall_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepall_tbl)->insert($itemrepall_data);
		
		$itemrepmcn_data     = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "mat_cent_id"=>$mat_cent_id,"item_txn_date"=>$item_valuation_info['item_txn_date']);
		$isexists =$this->db->table($itemrepmcn_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepmcn_tbl)->insert($itemrepmcn_data);
		
		
		$itemrepgrp_data  = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "item_grp_id"=>$item_info['item_grp_id'],"item_txn_date"=>$item_valuation_info['item_txn_date']);
		$isexists =$this->db->table($itemrepgrp_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepgrp_tbl)->insert($itemrepgrp_data);
		
		$itemrepcat_data  = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "item_cat"=>$item_info['item_cat'],"item_txn_date"=>$item_valuation_info['item_txn_date']);
		$isexists =$this->db->table($itemrepcat_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepcat_tbl)->insert($itemrepcat_data);
		
		$itemrepprj_data  = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "project_id"=>1,"item_txn_date"=>$item_valuation_info['item_txn_date']);
		$isexists =$this->db->table($itemrepprj_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepprj_tbl)->insert($itemrepprj_data);
		
		// get batch for items and unit id 
		 $GetItemBatchs = $this->GetItemBatchs($item_id);
		if($GetItemBatchs){
		  foreach($GetItemBatchs as $btrow){
			$batchid =   $btrow['batch_id'];
			$itemrepbat_data  = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "batch_id"=>$batchid,"item_txn_date"=>$item_valuation_info['item_txn_date']);
		    $isexists =$this->db->table($itemrepbat_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
			$this->db->table($itemrepbat_tbl)->insert($itemrepbat_data);
			}
			
			}	
			}
			if(($item_info['valmethod_id']==$valmethod_id) && ($rewrite_tool==0)){
			$rpt_data     = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>$item_valuation_info['item_value'],"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],
							  "bo_id"=>$bo_id,"item_txn_date"=>$item_valuation_info['item_txn_date']);
			$isexists =$this->db->table($itemrepbon_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
			$this->db->table($itemrepbon_tbl)->insert($rpt_data);
			
			
			$itemrepall_data     = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"item_txn_date"=>$item_valuation_info['item_txn_date']);
		$isexists =$this->db->table($itemrepall_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepall_tbl)->insert($itemrepall_data);
		
		$itemrepmcn_data     = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "mat_cent_id"=>$mat_cent_id,"item_txn_date"=>$item_valuation_info['item_txn_date']);
		$isexists =$this->db->table($itemrepmcn_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepmcn_tbl)->insert($itemrepmcn_data);
		
		
		$itemrepgrp_data  = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "item_grp_id"=>$item_info['item_grp_id'],"item_txn_date"=>$item_valuation_info['item_txn_date']);
		$isexists =$this->db->table($itemrepgrp_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepgrp_tbl)->insert($itemrepgrp_data);
		
		$itemrepcat_data  = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "item_cat"=>$item_info['item_cat'],"item_txn_date"=>$item_valuation_info['item_txn_date']);
		$isexists =$this->db->table($itemrepcat_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepcat_tbl)->insert($itemrepcat_data);
		
		$itemrepprj_data  = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "project_id"=>1,"item_txn_date"=>$item_valuation_info['item_txn_date']);
		$isexists =$this->db->table($itemrepprj_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
		$this->db->table($itemrepprj_tbl)->insert($itemrepprj_data);
		
		// get batch for items and unit id 
		 $GetItemBatchs = $this->GetItemBatchs($item_id);
		if($GetItemBatchs){
		  foreach($GetItemBatchs as $btrow){
			$batchid =   $btrow['batch_id'];
			$itemrepbat_data  = array("item_id_unit_id"=>$item_valuation_info['item_id_unit_id'],
			                  "voucher_txn_id"=>$item_valuation_info['voucher_txn_id'],"method_id"=>0,
							  "item_value"=>0,"item_txn_id"=>$item_valuation_info['item_txn_id'],
							  "item_bal_qty"=>$item_valuation_info['item_bal_qty'],"item_avail"=>$item_avail,"item_unit"=>$item_valuation_info['item_unit'],
							  "valuation_id"=>$item_valuation_info['valuation_id'],"bo_id"=>$bo_id,
							  "batch_id"=>$batchid,"item_txn_date"=>$item_valuation_info['item_txn_date']);
		    $isexists =$this->db->table($itemrepbat_tbl)->where('item_id_unit_id',$item_valuation_info['item_id_unit_id'])
			               ->where('voucher_txn_id',$item_valuation_info['voucher_txn_id'])
						   ->where('item_txn_id',$item_valuation_info['item_txn_id'])
						   ->where('method_id',0)
						   ->countAllResults();
			if($isexists==0)
			$this->db->table($itemrepbat_tbl)->insert($itemrepbat_data);
			}
			
			}	
			}
		return $item_valuation_info;
    	}
  	}
  	
  	
	public function GetItemBatchs($item_id){
	$table_name = $this->company_id.'_itmbatchmt_'.$this->session->get('ses_comp_fy_id'); 
	$response   =  $this->db->table($table_name)->where('item_id',$item_id)->get()->getResultArray();
	return $response;
   }
	
	public function update_item_fifo_valuation_new($info,$method_id,$rewrite_tool=0){
	    $itemrepall_tbl   = $this->company_id.'_itemrepall_'.$this->session->get('ses_comp_fy_id');	
		$itemrepmcn_tbl   = $this->company_id.'_itemrepmcn_'.$this->session->get('ses_comp_fy_id');	
		$itemrepgrp_tbl   = $this->company_id.'_itemrepgrp_'.$this->session->get('ses_comp_fy_id');	
		$itemrepcat_tbl   = $this->company_id.'_itemrepcat_'.$this->session->get('ses_comp_fy_id');	
		$itemrepprj_tbl   = $this->company_id.'_itemrepprj_'.$this->session->get('ses_comp_fy_id');	
	    $itemrepbon_tbl   = $this->company_id.'_itemrepbon_'.$this->session->get('ses_comp_fy_id');	
		$itemrepbat_tbl   = $this->company_id.'_itemrepbat_'.$this->session->get('ses_comp_fy_id');
		$itmoppyval_tbl   = $this->company_id.'_itmoppyval_'.$this->session->get('ses_comp_fy_id');
		
	  $item_id          = $info['item_id'];
	  $item_unit        = $info['item_unit'];
	  $mat_cent_id      = $info['mat_cent_id'];
	  $batch_id         = $info['batch_id'];
	  $op_bal_qty       = $info['op_bal_qty'];
	  $op_avg_val       = $info['op_avg_val'];
	  $item_txn_id      = $info['item_txn_id'];
	  $item_txn_drcr    = $info['item_txn_drcr'];
	  $item_txn_qty     = parseAmount($info['item_txn_qty']);
	  $item_txn_date    = $info['item_txn_date'];
	  $voucher_txn_id   = $info['voucher_txn_id'];
	  $item_avail       = $info['item_avail'];
	  $item_bal_qty     = $info['item_bal_qty'];
	  $item_txn_amount  = $info['item_txn_amount'];	
      $item_info	    = $info['item_info'];
	  $op_fifo_val      = $info['op_fifo_val'];
	  $op_lifo_val      = $info['op_lifo_val'];
	  if($item_txn_qty>0)
	  $item_rate        = parseAmountPrice(($item_txn_amount/$item_txn_qty),4);
      else
		 $item_rate        =1;  
	  $item_value       = 0;	
      
      $profit = 0;	  
	  $itemtxnnnn_tbl   = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
  	  $itemtxnvaln_tbl  = $this->company_id.'_itemtxnval_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
  	  $data = [
				'item_id'        => $item_id,
				'unit_id'        => $item_unit,
				'mat_cent_id'    => $mat_cent_id,
				'item_txn_id'    => $item_txn_id,
				'voucher_txn_id' => $voucher_txn_id,
				'voucher_date'   => $item_txn_date,
				'method_id'      => $method_id,
				'item_value'     => $item_value				
			    ];
	  $item_id_unit_id =$item_id.'_'.$item_unit;
	  $item_id_unit_avail_id =$item_id.'_'.$item_unit.'_'.$item_avail;
	  
	  $this->db->table($itemtxnvaln_tbl)->insert($data);
	  $valuation_id    = $this->db->insertID();	  	 
      $buffercalc_tbl  = $this->company_id.'_buffercalc_'.$this->session->get('ses_comp_fy_id');	  
      $buffrevlog_tbl  = $this->company_id.'_buffrevlog_'.$this->session->get('ses_comp_fy_id');
	  $bufflogrec_tbl  = $this->company_id.'_bufflogrec_'.$this->session->get('ses_comp_fy_id');	
	 $buffer_amount=0;
	  
	  if($item_txn_drcr=='d'){	     
  	     $buffercalc_data = array("cont_item_id_unit_avail_id"=>$item_id_unit_avail_id,"method_id"=>$method_id,
							   "txn_id"=>$item_txn_id,"buffer_ball_qty"=>$item_txn_qty,
	                           "buffer_ball_rate"=>$item_rate,"valuation_id"=>$valuation_id,
							   "voucher_txn_id"=>$voucher_txn_id
							   );
	     $this->db->table($buffercalc_tbl)->insert($buffercalc_data);
		 
		
					
		 $buffer_amount=0;
		 $builder = $this->db->table($buffercalc_tbl);			
	     $builder->select('op_valuation_id,buffer_ball_id,buffer_ball_qty,buffer_ball_rate');
		 $builder->where('cont_item_id_unit_avail_id', $item_id_unit_avail_id);
		 $builder->where('method_id', $method_id);			
		 $bfrtxn = $builder->get()->getResultArray();
		 if($bfrtxn){
		   foreach($bfrtxn as $row){
			   $buffer_ball_qty   = parseAmount($row['buffer_ball_qty']);
			   $op_valuation_id   = $row['op_valuation_id'];
			   $buffer_ball_rate = parseAmountPrice($row['buffer_ball_rate'],4);
	      	   $buffer_amount += parseAmount($buffer_ball_qty*$buffer_ball_rate);
			   
			   
			  }
		 }
		 $this->db->table($itemtxnvaln_tbl)->where('valuation_id',$valuation_id)->update(array("item_value"=>$buffer_amount));
			//echo 'fifo'.$this->db->GetLastQuery().'<br />';		
		}
		if($item_txn_drcr=='c'){
			
			$builder = $this->db->table($buffercalc_tbl);			
	        $builder->select('txn_id,op_valuation_id,voucher_txn_id,cont_item_id_unit_avail_id,buffer_ball_id,buffer_ball_qty,buffer_ball_rate,method_id');
			$builder->where('cont_item_id_unit_avail_id', $item_id_unit_avail_id);
			$builder->where('method_id', $method_id);	
			$builder->orderBy('buffer_ball_id', 'asc');			
			$bfrtxn = $builder->get()->getResultArray();
			$saleoutqty=0;
			$counter=0;
			$total_entries=count($bfrtxn);
			$buffer_amount=0;
            $total_out=0;			
			$pending_out_qty=$item_txn_qty;	
			if($bfrtxn){
			  foreach($bfrtxn as $row){ 
				  $counter++;
				  $buffer_ball_qty   = parseAmount($row['buffer_ball_qty']);
				  $buffer_ball_rate  = parseAmountPrice($row['buffer_ball_rate'],4);
				  $buffer_ball_id    = $row['buffer_ball_id'];
				  $bfr_vch_txn_id    = $row['voucher_txn_id'];
				  $bfr_txn_id        = $row['txn_id'];
				  $op_valuation_id   = $row['op_valuation_id'];
				  $opval_mc_id       = 0;
				  if($bfr_vch_txn_id==0){
					$op_valuation_id = $row['op_valuation_id']; 
					$builderr = $this->db->table($itmoppyval_tbl); 
					$builderr->select('mat_cent_id');
					$builderr->where('op_valuation_id', $op_valuation_id);
					$builderr->where('bo_id', $this->bo_id);
					$builderr->where('method_id',$method_id);
					$pyval_res  = $builderr->get()->getRowArray();	
					if($pyval_res)
					$opval_mc_id = $pyval_res['mat_cent_id'];
				  }
				  
				  if($opval_mc_id>0 && $opval_mc_id==$mat_cent_id){
					if ($buffer_ball_qty > $item_txn_qty) {
                   $saleoutqty     = $saleoutqty + $item_txn_qty;
                    $buffer_ball_qty = $buffer_ball_qty - $item_txn_qty;
                    
                   $this->db->table($buffercalc_tbl)->where('buffer_ball_id',$buffer_ball_id)->where("method_id",$method_id)->where('cont_item_id_unit_avail_id',$item_id_unit_avail_id)->update(array('buffer_ball_qty'=>$buffer_ball_qty,'voucher_txn_id'=>$voucher_txn_id));  
				  
				  if($item_txn_qty >0){
				   $buffrevlog_data = array("cont_item_id_unit_avail_id"=>$item_id_unit_avail_id,"method_id"=>$method_id,
										  "txn_id"=>$item_txn_id,"buff_ball_rev_qty"=>$item_txn_qty,"buffer_ball_id"=>$buffer_ball_id,
										  "valuation_id"=>$valuation_id, "voucher_txn_id"=>$voucher_txn_id
							             );
				    $this->db->table($buffrevlog_tbl)->insert($buffrevlog_data);
					$buff_rev_log_id = $this->db->insertID(); 
				  }
				  
				 $item_txn_qty = 0; 
				 
				 
                } else {
                    // allocate everything available fully delete
                    $item_txn_qty = $item_txn_qty - $buffer_ball_qty;
                    $saleoutqty     =$buffer_ball_qty;
                    
                    
                   	$this->db->table($buffercalc_tbl)
					         ->where("method_id",$method_id)
							 ->where('buffer_ball_id',$buffer_ball_id)
							 ->where('cont_item_id_unit_avail_id',$item_id_unit_avail_id)->delete(); 
                    
                   if($saleoutqty>0){
				    $buffrevlog_data = array("cont_item_id_unit_avail_id"=>$item_id_unit_avail_id,"method_id"=>$method_id,
										  "txn_id"=>$item_txn_id,"buff_ball_rev_qty"=>$saleoutqty,"buffer_ball_id"=>$buffer_ball_id,
										  "valuation_id"=>$valuation_id, "voucher_txn_id"=>$voucher_txn_id
							             );
				    $this->db->table($buffrevlog_tbl)->insert($buffrevlog_data);
					$buff_rev_log_id = $this->db->insertID(); 
					}
					
					$buffer_ball_qty = 0;
					
                   }  
				  }
				  else{
					if($buffer_ball_qty > $item_txn_qty) {
                     $saleoutqty       = $saleoutqty + $item_txn_qty;
                     $buffer_ball_qty  = $buffer_ball_qty - $item_txn_qty;
                     
                     $this->db->table($buffercalc_tbl)->where('buffer_ball_id',$buffer_ball_id)->where("method_id",$method_id)->where('cont_item_id_unit_avail_id',$item_id_unit_avail_id)->update(array('buffer_ball_qty'=>$buffer_ball_qty,'voucher_txn_id'=>$voucher_txn_id));  
				  	 
					 if($item_txn_qty>0){
				      $buffrevlog_data = array("cont_item_id_unit_avail_id"=>$item_id_unit_avail_id,"method_id"=>$method_id,
										  "txn_id"=>$item_txn_id,"buff_ball_rev_qty"=>$item_txn_qty,"buffer_ball_id"=>$buffer_ball_id,
										  "valuation_id"=>$valuation_id, "voucher_txn_id"=>$voucher_txn_id
							             );
				     $this->db->table($buffrevlog_tbl)->insert($buffrevlog_data);
					 $buff_rev_log_id = $this->db->insertID(); 
					 }
					 
				      $item_txn_qty     = 0;
				    
				  
                } else {
                    // allocate everything available fully delete
                    $item_txn_qty = $item_txn_qty - $buffer_ball_qty;
                    $saleoutqty     =$buffer_ball_qty;
                    
                    
                   	$this->db->table($buffercalc_tbl)
					         ->where("method_id",$method_id)
							 ->where('buffer_ball_id',$buffer_ball_id)
							 ->where('cont_item_id_unit_avail_id',$item_id_unit_avail_id)->delete(); 
                    
                   
					if($saleoutqty>0){   
				    $buffrevlog_data = array("cont_item_id_unit_avail_id"=>$item_id_unit_avail_id,"method_id"=>$method_id,
										  "txn_id"=>$item_txn_id,"buff_ball_rev_qty"=>$saleoutqty,"buffer_ball_id"=>$buffer_ball_id,
										  "valuation_id"=>$valuation_id, "voucher_txn_id"=>$voucher_txn_id
							             );
				    $this->db->table($buffrevlog_tbl)->insert($buffrevlog_data);
                    $buff_rev_log_id = $this->db->insertID(); 					
					}
					
					$buffer_ball_qty = 0;
					
                   }  
				   
				   
				   
				   
				   
				  }
				   
				
				
				 
			    }	
			}else{
			  // 0 valuation 	
			$this->db->table($itemtxnvaln_tbl)->where('method_id', $method_id)->where('valuation_id',$valuation_id)->update(array("item_value"=>0));
						
			}
		 $buffer_amount=0;
		 $builder = $this->db->table($buffercalc_tbl);			
	     $builder->select('buffer_ball_id,buffer_ball_qty,buffer_ball_rate');
		 $builder->where('cont_item_id_unit_avail_id', $item_id_unit_avail_id);
		 $builder->where('method_id', $method_id);
		 $builder->where('voucher_txn_id', $voucher_txn_id);	
         $builder->orderBy('buffer_ball_id');		 
		 $bfrtxn = $builder->get()->getResultArray();
		 if($bfrtxn){
		   foreach($bfrtxn as $row){
			  $buffer_ball_qty   = parseAmount($row['buffer_ball_qty']);
			  $buffer_ball_rate  = parseAmountPrice($row['buffer_ball_rate'],4);
	      	  $buffer_amount     += parseAmount($buffer_ball_qty*$buffer_ball_rate);
		    }
		 } 
		 $this->db->table($itemtxnvaln_tbl)->where('method_id', $method_id)->where('valuation_id',$valuation_id)->update(array("item_value"=>$buffer_amount));
	     						
			
		}
		
	  $builder = $this->db->table($itemtxnnnn_tbl);
	  $builder->select('item_txn_id, item_bal_qty'); 
	  $builder->where('item_id', $item_id);
	  $builder->where('item_unit', $item_unit);
	  $builder->where('mat_cent_id', $mat_cent_id);
	  $builder->where('batch_id', $batch_id);
	  $builder->where('item_avail', $item_avail);	
	  $builder->where('voucher_txn_id',$voucher_txn_id);
	   $builder->where('item_txn_id', $item_txn_id);
      $item_tax_res = $builder->get()->getRowArray();
	    
	  if($item_tax_res){
		$item_bal_qty = $item_tax_res['item_bal_qty'];  
	  }	
	  if($rewrite_tool==0){
		if($method_id==$item_info['valmethod_id']){	  
	     $rpt_data     = array("item_id_unit_id"=>$item_id_unit_id,
			                  "voucher_txn_id"=>$voucher_txn_id,"method_id"=>$method_id,
							  "item_value"=>$buffer_amount,"item_txn_id"=>$item_txn_id,
							  "item_bal_qty"=>$item_bal_qty,"item_avail"=>$item_avail,"item_unit"=>$item_unit,
							  "valuation_id"=>$valuation_id,
							  "bo_id"=>$this->bo_id,"item_txn_date"=>$item_txn_date);
		 $this->db->table($itemrepbon_tbl)->insert($rpt_data);
	    }else{
		  $rpt_data     = array("item_id_unit_id"=>$item_id_unit_id,
			                  "voucher_txn_id"=>$voucher_txn_id,"method_id"=>$method_id,
							  "item_value"=>0,"item_txn_id"=>$item_txn_id,
							  "item_bal_qty"=>$item_bal_qty,"item_avail"=>$item_avail,"item_unit"=>$item_unit,
							  "valuation_id"=>$valuation_id,
							  "bo_id"=>$this->bo_id,"item_txn_date"=>$item_txn_date);
		$this->db->table($itemrepbon_tbl)->insert($rpt_data);  
	    }  
	  }
	  else{
		 $rpt_data     = array("item_id_unit_id"=>$item_id_unit_id,
			                  "voucher_txn_id"=>$voucher_txn_id,"method_id"=>$method_id,
							  "item_value"=>$buffer_amount,"item_txn_id"=>$item_txn_id,
							  "item_bal_qty"=>$item_bal_qty,"item_avail"=>$item_avail,"item_unit"=>$item_unit,
							  "valuation_id"=>$valuation_id,
							  "bo_id"=>$this->bo_id,"item_txn_date"=>$item_txn_date);
		$this->db->table($itemrepbon_tbl)->insert($rpt_data);
		$dd = $this->db->getlastquery();
		SaveErrorLog($dd);
		if($method_id==$item_info['valmethod_id']){
		$this->db->table($itemrepbon_tbl)->where("item_id_unit_id",$item_id_unit_id)
										 ->where("voucher_txn_id",$voucher_txn_id)
										 ->where("method_id",$item_info['valmethod_id'])
										 ->where("item_txn_id",$item_txn_id)
		                                 ->update(["item_value"=>$buffer_amount]);
		}
		
		
	  }
	  
		//////////////
		$itemrepall_data     = array("item_id_unit_id"=>$item_id_unit_id,
			                  "voucher_txn_id"=>$voucher_txn_id,"method_id"=>$method_id,
							  "item_value"=>0,"item_txn_id"=>$item_txn_id,
							  "item_bal_qty"=>$item_bal_qty,"item_avail"=>$item_avail,"item_unit"=>$item_unit,
							  "valuation_id"=>$valuation_id,"item_txn_date"=>$item_txn_date);
		$this->db->table($itemrepall_tbl)->insert($itemrepall_data);
		
		$itemrepmcn_data     = array("item_id_unit_id"=>$item_id_unit_id,
			                  "voucher_txn_id"=>$voucher_txn_id,"method_id"=>$method_id,
							  "item_value"=>0,"item_txn_id"=>$item_txn_id,
							  "item_bal_qty"=>$item_bal_qty,"item_avail"=>$item_avail,"item_unit"=>$item_unit,
							  "valuation_id"=>$valuation_id,"bo_id"=>$this->bo_id,
							  "mat_cent_id"=>$mat_cent_id,"item_txn_date"=>$item_txn_date);
		$this->db->table($itemrepmcn_tbl)->insert($itemrepmcn_data);
		
		
		$itemrepgrp_data  = array("item_id_unit_id"=>$item_id_unit_id,
			                  "voucher_txn_id"=>$voucher_txn_id,"method_id"=>$method_id,
							  "item_value"=>0,"item_txn_id"=>$item_txn_id,
							  "item_bal_qty"=>$item_bal_qty,"item_avail"=>$item_avail,"item_unit"=>$item_unit,
							  "valuation_id"=>$valuation_id,"bo_id"=>$this->bo_id,
							  "item_grp_id"=>$item_info['item_grp_id'],"item_txn_date"=>$item_txn_date);
		$this->db->table($itemrepgrp_tbl)->insert($itemrepgrp_data);
		
		$itemrepcat_data  = array("item_id_unit_id"=>$item_id_unit_id,
			                  "voucher_txn_id"=>$voucher_txn_id,"method_id"=>$method_id,
							  "item_value"=>0,"item_txn_id"=>$item_txn_id,
							  "item_bal_qty"=>$item_bal_qty,"item_avail"=>$item_avail,"item_unit"=>$item_unit,
							  "valuation_id"=>$valuation_id,"bo_id"=>$this->bo_id,
							  "item_cat"=>$item_info['item_cat'],"item_txn_date"=>$item_txn_date);
		$this->db->table($itemrepcat_tbl)->insert($itemrepcat_data);
		
		$itemrepprj_data  = array("item_id_unit_id"=>$item_id_unit_id,
			                  "voucher_txn_id"=>$voucher_txn_id,"method_id"=>$method_id,
							  "item_value"=>0,"item_txn_id"=>$item_txn_id,
							  "item_bal_qty"=>$item_bal_qty,"item_avail"=>$item_avail,"item_unit"=>$item_unit,
							  "valuation_id"=>$valuation_id,"bo_id"=>$this->bo_id,
							  "project_id"=>1,"item_txn_date"=>$item_txn_date);
		$this->db->table($itemrepprj_tbl)->insert($itemrepprj_data);

		// get batch for items and unit id 
		 $GetItemBatchs = $this->GetItemBatchs($item_id);
		if($GetItemBatchs){
		  foreach($GetItemBatchs as $btrow){
			$batchid =   $btrow['batch_id'];
			$itemrepbat_data  = array("item_id_unit_id"=>$item_id_unit_id,
			                  "voucher_txn_id"=>$voucher_txn_id,"method_id"=>$method_id,
							  "item_value"=>0,"item_txn_id"=>$item_txn_id,
							  "item_bal_qty"=>$item_bal_qty,"item_avail"=>$item_avail,"item_unit"=>$item_unit,
							  "valuation_id"=>$valuation_id,"bo_id"=>$this->bo_id,
							  "batch_id"=>$batchid,"item_txn_date"=>$item_txn_date);
		    $this->db->table($itemrepbat_tbl)->insert($itemrepbat_data);
			}
			
			}
      		
	  
	   $item_valuation_info=array('item_id_unit_id'=>$item_id_unit_id,'voucher_txn_id'=>$voucher_txn_id,
		                          'item_value'=>$buffer_amount,'item_txn_id'=>$item_txn_id,
								  'item_bal_qty'=>$item_bal_qty,'item_unit'=>$item_unit,'item_avail'=>$item_avail,
								  'valuation_id'=>$valuation_id,"item_txn_date"=>$item_txn_date); 
		return $item_valuation_info;	
	 
	}	
	
	public function update_item_lifo_valuation_new($info,$method_id,$rewrite_tool=0){
	    $itemrepall_tbl   = $this->company_id.'_itemrepall_'.$this->session->get('ses_comp_fy_id');	
		$itemrepmcn_tbl   = $this->company_id.'_itemrepmcn_'.$this->session->get('ses_comp_fy_id');	
		$itemrepgrp_tbl   = $this->company_id.'_itemrepgrp_'.$this->session->get('ses_comp_fy_id');	
		$itemrepcat_tbl   = $this->company_id.'_itemrepcat_'.$this->session->get('ses_comp_fy_id');	
		$itemrepprj_tbl   = $this->company_id.'_itemrepprj_'.$this->session->get('ses_comp_fy_id');	
	    $itemrepbon_tbl   = $this->company_id.'_itemrepbon_'.$this->session->get('ses_comp_fy_id');	
		$itemrepbat_tbl   = $this->company_id.'_itemrepbat_'.$this->session->get('ses_comp_fy_id');
		$itmoppyval_tbl   = $this->company_id.'_itmoppyval_'.$this->session->get('ses_comp_fy_id');
		
	  $item_id          = $info['item_id'];
	  $item_unit        = $info['item_unit'];
	  $mat_cent_id      = $info['mat_cent_id'];
	  $batch_id         = $info['batch_id'];
	  $op_bal_qty       = $info['op_bal_qty'];
	  $op_avg_val       = $info['op_avg_val'];
	  $item_txn_id      = $info['item_txn_id'];
	  $item_txn_drcr    = $info['item_txn_drcr'];
	  $item_txn_qty     = parseAmount($info['item_txn_qty']);
	  $item_txn_date    = $info['item_txn_date'];
	  $voucher_txn_id   = $info['voucher_txn_id'];
	  $item_avail       = $info['item_avail'];
	  $item_bal_qty     = $info['item_bal_qty'];
	  $item_txn_amount  = $info['item_txn_amount'];	
      $item_info	    = $info['item_info'];
	  $op_fifo_val      = $info['op_fifo_val'];
	  $op_lifo_val      = $info['op_lifo_val'];
	  if($item_txn_qty>0)
	  $item_rate        = parseAmountPrice(($item_txn_amount/$item_txn_qty),4);
     else
		$item_rate        =1; 
	  $item_value       = 0;
	  
	  $itemtxnnnn_tbl   = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
  	  $itemtxnvaln_tbl  = $this->company_id.'_itemtxnval_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
  	 
	  $data = [
				'item_id'        => $item_id,
				'unit_id'        => $item_unit,
				'mat_cent_id'    => $mat_cent_id,
				'item_txn_id'    => $item_txn_id,
				'voucher_txn_id' => $voucher_txn_id,
				'voucher_date'   => $item_txn_date,
				'method_id'      => $method_id,
				'item_value'     => $item_value				
			    ];
				
	  $item_id_unit_id = $item_id.'_'.$item_unit;

	  $item_id_unit_avail_id = $item_id.'_'.$item_unit.'_'.$item_avail;
	  $this->db->table($itemtxnvaln_tbl)->insert($data);
	  $valuation_id    = $this->db->insertID();	
      $buffercalc_tbl  = $this->company_id.'_buffercalc_'.$this->session->get('ses_comp_fy_id');	  
      $buffrevlog_tbl  = $this->company_id.'_buffrevlog_'.$this->session->get('ses_comp_fy_id');
	  //	echo '<br /><hr><br />';
	  if($item_txn_drcr=='d'){	     
  	     $buffercalc_data = array("cont_item_id_unit_avail_id"=>$item_id_unit_avail_id,"method_id"=>$method_id,
							   "txn_id"=>$item_txn_id,"buffer_ball_qty"=>$item_txn_qty,
	                           "buffer_ball_rate"=>$item_rate,"valuation_id"=>$valuation_id,
							   "voucher_txn_id"=>$voucher_txn_id
							   );
	     $this->db->table($buffercalc_tbl)->insert($buffercalc_data);
		 
		 $buffer_amount=0;
		 $builder = $this->db->table($buffercalc_tbl);			
	     $builder->select('buffer_ball_id,buffer_ball_qty,buffer_ball_rate');
		 $builder->where('cont_item_id_unit_avail_id', $item_id_unit_avail_id);
		 $builder->where('method_id', $method_id);			
		 $bfrtxn = $builder->get()->getResultArray();
		 if($bfrtxn){
		   foreach($bfrtxn as $row){
			   $buffer_ball_qty   = parseAmount($row['buffer_ball_qty']);
			   $buffer_ball_rate = parseAmountPrice($row['buffer_ball_rate'],4);
	      	   $buffer_amount += parseAmount($buffer_ball_qty*$buffer_ball_rate);
			  }
		 }
		 $this->db->table($itemtxnvaln_tbl)->where('method_id', $method_id)->where('valuation_id',$valuation_id)->update(array("item_value"=>$buffer_amount));
		// echo 'lifo'.$this->db->GetLastQuery().'<br />';			
		}
		if($item_txn_drcr=='c'){
			$builder = $this->db->table($buffercalc_tbl);			
	        $builder->select('op_valuation_id,voucher_txn_id,cont_item_id_unit_avail_id,buffer_ball_id,buffer_ball_qty,buffer_ball_rate,method_id');
			$builder->where('cont_item_id_unit_avail_id', $item_id_unit_avail_id);
			$builder->where('method_id', $method_id);	
			$builder->orderBy('method_id', 'desc');	
			$builder->orderBy('buffer_ball_id', 'desc');
			$bfrtxn = $builder->get()->getResultArray();
			$saleoutqty=0;
			$counter=1;
			$total_entries=count($bfrtxn);
			$buffer_amount=0;
            $total_out=0;	
			
			if($bfrtxn){
			  foreach($bfrtxn as $row){
				  $bfr_vch_txn_id = $row['voucher_txn_id'];
				  $opval_mc_id=0;
				  $buffer_ball_qty  = parseAmount($row['buffer_ball_qty']);
				  $buffer_ball_rate =  parseAmountPrice($row['buffer_ball_rate'],4);
				  $buffer_ball_id   =  $row['buffer_ball_id'];
				  if($bfr_vch_txn_id==0){
					$op_valuation_id = $row['op_valuation_id']; 
					$builderr = $this->db->table($itmoppyval_tbl); 
					$builderr->select('mat_cent_id');
					$builderr->where('op_valuation_id', $op_valuation_id);
					$builderr->where('bo_id', $this->bo_id);
					$builderr->where('method_id',$method_id);
					$pyval_res  = $builderr->get()->getRowArray();	
					if($pyval_res)
					  $opval_mc_id = $pyval_res['mat_cent_id'];
				  }
				  
				  	
				  if($opval_mc_id>0 && $opval_mc_id==$mat_cent_id){
					if ($buffer_ball_qty > $item_txn_qty) {
                     $saleoutqty      = $saleoutqty + $item_txn_qty;
                     $buffer_ball_qty = $buffer_ball_qty - $item_txn_qty;
                      
                     $this->db->table($buffercalc_tbl)->where('buffer_ball_id',$buffer_ball_id)->where("method_id",$method_id)->where('cont_item_id_unit_avail_id',$item_id_unit_avail_id)->update(array('buffer_ball_qty'=>$buffer_ball_qty,'txn_id'=>$item_txn_id,'valuation_id'=>$valuation_id,'voucher_txn_id'=>$voucher_txn_id));  					
				     if($item_txn_qty >0){
				     $buffrevlog_data = array("cont_item_id_unit_avail_id"=>$item_id_unit_avail_id,"method_id"=>$method_id,
										  "txn_id"=>$item_txn_id,"buff_ball_rev_qty"=>$item_txn_qty,"buffer_ball_id"=>$buffer_ball_id,
										  "valuation_id"=>$valuation_id, "voucher_txn_id"=>$voucher_txn_id
							             );
				     $this->db->table($buffrevlog_tbl)->insert($buffrevlog_data);
					 }
					 
					 $item_txn_qty    = 0;
                } else {
                    // allocate everything available fully delete
                    $item_txn_qty = $item_txn_qty - $buffer_ball_qty;
                    $saleoutqty   = $buffer_ball_qty; 
                   	$this->db->table($buffercalc_tbl)
					         ->where("method_id",$method_id)
							 ->where('buffer_ball_id',$buffer_ball_id)
							 ->where('cont_item_id_unit_avail_id',$item_id_unit_avail_id)->delete();                     
                   if($saleoutqty>0){
				    $buffrevlog_data = array("cont_item_id_unit_avail_id"=>$item_id_unit_avail_id,"method_id"=>$method_id,
										  "txn_id"=>$item_txn_id,"buff_ball_rev_qty"=>$saleoutqty,"buffer_ball_id"=>$buffer_ball_id,
										  "valuation_id"=>$valuation_id, "voucher_txn_id"=>$voucher_txn_id
							             );
				    $this->db->table($buffrevlog_tbl)->insert($buffrevlog_data);
					}
				   $buffer_ball_qty = 0;	
                }  
					  
				  }else{
					if ($buffer_ball_qty > $item_txn_qty) {
                     $saleoutqty      = $saleoutqty + $item_txn_qty;
                     $buffer_ball_qty = $buffer_ball_qty - $item_txn_qty;
                      
                     $this->db->table($buffercalc_tbl)->where('buffer_ball_id',$buffer_ball_id)->where("method_id",$method_id)->where('cont_item_id_unit_avail_id',$item_id_unit_avail_id)->update(array('buffer_ball_qty'=>$buffer_ball_qty,'txn_id'=>$item_txn_id,'valuation_id'=>$valuation_id,'voucher_txn_id'=>$voucher_txn_id));  					
				     if($item_txn_qty >0){
				     $buffrevlog_data = array("cont_item_id_unit_avail_id"=>$item_id_unit_avail_id,"method_id"=>$method_id,
										  "txn_id"=>$item_txn_id,"buff_ball_rev_qty"=>$item_txn_qty,"buffer_ball_id"=>$buffer_ball_id,
										  "valuation_id"=>$valuation_id, "voucher_txn_id"=>$voucher_txn_id
							             );
				     $this->db->table($buffrevlog_tbl)->insert($buffrevlog_data);
					 }
					 $item_txn_qty    = 0;
                } else {
                    // allocate everything available fully delete
                    $item_txn_qty = $item_txn_qty - $buffer_ball_qty;
                    $saleoutqty   = $buffer_ball_qty; 
                   	$this->db->table($buffercalc_tbl)
					         ->where("method_id",$method_id)
							 ->where('buffer_ball_id',$buffer_ball_id)
							 ->where('cont_item_id_unit_avail_id',$item_id_unit_avail_id)->delete();                     
                   if($saleoutqty>0){
				    $buffrevlog_data = array("cont_item_id_unit_avail_id"=>$item_id_unit_avail_id,"method_id"=>$method_id,
										  "txn_id"=>$item_txn_id,"buff_ball_rev_qty"=>$saleoutqty,"buffer_ball_id"=>$buffer_ball_id,
										  "valuation_id"=>$valuation_id, "voucher_txn_id"=>$voucher_txn_id
							             );
				    $this->db->table($buffrevlog_tbl)->insert($buffrevlog_data);
					}
				   $buffer_ball_qty = 0;	
                }  
					  
				  }
				  
				
				 
            	$counter++; 
			    }	
			}else{
			  // 0 valuation 	
			$this->db->table($itemtxnvaln_tbl)->where('method_id', $method_id)->where('valuation_id',$valuation_id)->update(array("item_value"=>0));
						
			}
		 $buffer_amount=0;
		 $builder = $this->db->table($buffercalc_tbl);			
	     $builder->select('buffer_ball_id,buffer_ball_qty,buffer_ball_rate');
		 $builder->where('cont_item_id_unit_avail_id', $item_id_unit_avail_id);
		 $builder->where('method_id', $method_id);	
		 $builder->where('voucher_txn_id', $voucher_txn_id);	
		 $bfrtxn = $builder->get()->getResultArray();
		 if($bfrtxn){
		   foreach($bfrtxn as $row){
			  $buffer_ball_qty   = parseAmount($row['buffer_ball_qty']);
			  $buffer_ball_rate  = parseAmountPrice($row['buffer_ball_rate'],4);
	      	  $buffer_amount     += parseAmount($buffer_ball_qty*$buffer_ball_rate);
		    }
		 }
		 $this->db->table($itemtxnvaln_tbl)->where('method_id', $method_id)->where('valuation_id',$valuation_id)->update(array("item_value"=>$buffer_amount));
		}
		
		
	  $builder = $this->db->table($itemtxnnnn_tbl);
	  $builder->select('item_txn_id, item_bal_qty'); 
	  $builder->where('item_id', $item_id);
	  $builder->where('item_unit', $item_unit);
	  $builder->where('mat_cent_id', $mat_cent_id);
	  $builder->where('batch_id', $batch_id);
	  $builder->where('item_avail', $item_avail);	
	   $builder->where('item_txn_id', $item_txn_id);
	  $builder->where('voucher_txn_id',$voucher_txn_id);
      $item_tax_res = $builder->get()->getRowArray();
	  	  
	  if($item_tax_res){
		$item_bal_qty = $item_tax_res['item_bal_qty'];  
	  }	
	  if($rewrite_tool==0){
		if($method_id==$item_info['valmethod_id']){	  
	     $rpt_data     = array("item_id_unit_id"=>$item_id_unit_id,
			                  "voucher_txn_id"=>$voucher_txn_id,"method_id"=>$method_id,
							  "item_value"=>$buffer_amount,"item_txn_id"=>$item_txn_id,
							  "item_bal_qty"=>$item_bal_qty,"item_avail"=>$item_avail,"item_unit"=>$item_unit,
							  "valuation_id"=>$valuation_id,
							  "bo_id"=>$this->bo_id,"item_txn_date"=>$item_txn_date);
		 $this->db->table($itemrepbon_tbl)->insert($rpt_data);
	    }else{
		  $rpt_data     = array("item_id_unit_id"=>$item_id_unit_id,
			                  "voucher_txn_id"=>$voucher_txn_id,"method_id"=>$method_id,
							  "item_value"=>0,"item_txn_id"=>$item_txn_id,
							  "item_bal_qty"=>$item_bal_qty,"item_avail"=>$item_avail,"item_unit"=>$item_unit,
							  "valuation_id"=>$valuation_id,
							  "bo_id"=>$this->bo_id,"item_txn_date"=>$item_txn_date);
		$this->db->table($itemrepbon_tbl)->insert($rpt_data);  
	    }  
	  }
	  else{
		 $rpt_data     = array("item_id_unit_id"=>$item_id_unit_id,
			                  "voucher_txn_id"=>$voucher_txn_id,"method_id"=>$method_id,
							  "item_value"=>$buffer_amount,"item_txn_id"=>$item_txn_id,
							  "item_bal_qty"=>$item_bal_qty,"item_avail"=>$item_avail,"item_unit"=>$item_unit,
							  "valuation_id"=>$valuation_id,
							  "bo_id"=>$this->bo_id,"item_txn_date"=>$item_txn_date);
		$this->db->table($itemrepbon_tbl)->insert($rpt_data);
		$dd = $this->db->getlastquery();
		SaveErrorLog($dd);
		if($method_id==$item_info['valmethod_id']){
		$this->db->table($itemrepbon_tbl)->where("item_id_unit_id",$item_id_unit_id)
										 ->where("voucher_txn_id",$voucher_txn_id)
										 ->where("method_id",$item_info['valmethod_id'])
										 ->where("item_txn_id",$item_txn_id)
		                                 ->update(["item_value"=>$buffer_amount]);
		}
		
		
	  }
	  
		//////////////
		$itemrepall_data     = array("item_id_unit_id"=>$item_id_unit_id,
			                  "voucher_txn_id"=>$voucher_txn_id,"method_id"=>$method_id,
							  "item_value"=>0,"item_txn_id"=>$item_txn_id,
							  "item_bal_qty"=>$item_bal_qty,"item_avail"=>$item_avail,"item_unit"=>$item_unit,
							  "valuation_id"=>$valuation_id,"item_txn_date"=>$item_txn_date);
		$this->db->table($itemrepall_tbl)->insert($itemrepall_data);
		
		$itemrepmcn_data     = array("item_id_unit_id"=>$item_id_unit_id,
			                  "voucher_txn_id"=>$voucher_txn_id,"method_id"=>$method_id,
							  "item_value"=>0,"item_txn_id"=>$item_txn_id,
							  "item_bal_qty"=>$item_bal_qty,"item_avail"=>$item_avail,"item_unit"=>$item_unit,
							  "valuation_id"=>$valuation_id,"bo_id"=>$this->bo_id,
							  "mat_cent_id"=>$mat_cent_id,"item_txn_date"=>$item_txn_date);
		$this->db->table($itemrepmcn_tbl)->insert($itemrepmcn_data);
		
		
		$itemrepgrp_data  = array("item_id_unit_id"=>$item_id_unit_id,
			                  "voucher_txn_id"=>$voucher_txn_id,"method_id"=>$method_id,
							  "item_value"=>0,"item_txn_id"=>$item_txn_id,
							  "item_bal_qty"=>$item_bal_qty,"item_avail"=>$item_avail,"item_unit"=>$item_unit,
							  "valuation_id"=>$valuation_id,"bo_id"=>$this->bo_id,
							  "item_grp_id"=>$item_info['item_grp_id'],"item_txn_date"=>$item_txn_date);
		$this->db->table($itemrepgrp_tbl)->insert($itemrepgrp_data);
		
		$itemrepcat_data  = array("item_id_unit_id"=>$item_id_unit_id,
			                  "voucher_txn_id"=>$voucher_txn_id,"method_id"=>$method_id,
							  "item_value"=>0,"item_txn_id"=>$item_txn_id,
							  "item_bal_qty"=>$item_bal_qty,"item_avail"=>$item_avail,"item_unit"=>$item_unit,
							  "valuation_id"=>$valuation_id,"bo_id"=>$this->bo_id,
							  "item_cat"=>$item_info['item_cat'],"item_txn_date"=>$item_txn_date);
		$this->db->table($itemrepcat_tbl)->insert($itemrepcat_data);
		
		$itemrepprj_data  = array("item_id_unit_id"=>$item_id_unit_id,
			                  "voucher_txn_id"=>$voucher_txn_id,"method_id"=>$method_id,
							  "item_value"=>0,"item_txn_id"=>$item_txn_id,
							  "item_bal_qty"=>$item_bal_qty,"item_avail"=>$item_avail,"item_unit"=>$item_unit,
							  "valuation_id"=>$valuation_id,"bo_id"=>$this->bo_id,
							  "project_id"=>1,"item_txn_date"=>$item_txn_date);
		$this->db->table($itemrepprj_tbl)->insert($itemrepprj_data);

		// get batch for items and unit id 
		 $GetItemBatchs = $this->GetItemBatchs($item_id);
		if($GetItemBatchs){
		  foreach($GetItemBatchs as $btrow){
			$batchid =   $btrow['batch_id'];
			$itemrepbat_data  = array("item_id_unit_id"=>$item_id_unit_id,
			                  "voucher_txn_id"=>$voucher_txn_id,"method_id"=>$method_id,
							  "item_value"=>0,"item_txn_id"=>$item_txn_id,
							  "item_bal_qty"=>$item_bal_qty,"item_avail"=>$item_avail,"item_unit"=>$item_unit,
							  "valuation_id"=>$valuation_id,"bo_id"=>$this->bo_id,
							  "batch_id"=>$batchid,"item_txn_date"=>$item_txn_date);
		    $this->db->table($itemrepbat_tbl)->insert($itemrepbat_data);
			}
			
			}
      		
	  
	   $item_valuation_info=array('item_id_unit_id'=>$item_id_unit_id,'voucher_txn_id'=>$voucher_txn_id,
		                          'item_value'=>$buffer_amount,'item_txn_id'=>$item_txn_id,
								  'item_bal_qty'=>$item_bal_qty,'item_unit'=>$item_unit,'item_avail'=>$item_avail,
								  'valuation_id'=>$valuation_id,"item_txn_date"=>$item_txn_date); 
		return $item_valuation_info;	
	
	}
   
  
    public function update_item_avg_valuation_new($info,$method_id,$rewrite_tool=0){
	  $itemrepall_tbl   = $this->company_id.'_itemrepall_'.$this->session->get('ses_comp_fy_id');	
	  $itemrepmcn_tbl   = $this->company_id.'_itemrepmcn_'.$this->session->get('ses_comp_fy_id');	
	  $itemrepgrp_tbl   = $this->company_id.'_itemrepgrp_'.$this->session->get('ses_comp_fy_id');	
	  $itemrepcat_tbl   = $this->company_id.'_itemrepcat_'.$this->session->get('ses_comp_fy_id');	
	  $itemrepprj_tbl   = $this->company_id.'_itemrepprj_'.$this->session->get('ses_comp_fy_id');	
	  $itemrepbon_tbl   = $this->company_id.'_itemrepbon_'.$this->session->get('ses_comp_fy_id');	
	  $itemrepbat_tbl   = $this->company_id.'_itemrepbat_'.$this->session->get('ses_comp_fy_id');
		
	  $item_id          = $info['item_id'];
	  $item_unit        = $info['item_unit'];
	  $mat_cent_id      = $info['mat_cent_id'];
	  $batch_id         = $info['batch_id'];
	  $op_bal_qty       = $info['op_bal_qty'];
	  $op_avg_val       = $info['op_avg_val'];
	  $item_txn_id      = $info['item_txn_id'];
	  $item_txn_drcr    = $info['item_txn_drcr'];
	  $item_txn_qty     = $info['item_txn_qty'];
	  $item_txn_date    = $info['item_txn_date'];
	  $voucher_txn_id   = $info['voucher_txn_id'];
	  $item_avail       = $info['item_avail'];
	  $item_bal_qty     = $info['item_bal_qty'];
	  $item_txn_amount  = $info['item_txn_amount'];	
      $item_info	    = $info['item_info'];
	  $item_value       = 0;
  	  $itemtxnnnn_tbl   = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
  	  $itemtxnvaln_tbl  = $this->company_id.'_itemtxnval_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
  	  $item_id_unit_id =$item_id.'_'.$item_unit;
	 
  	  	/* $rate = $op_bal_qty != 0 ? floatval($op_avg_val/$op_bal_qty) : 0;
		$item_value = floatval($item_bal_qty * $rate);
		 */
  	  	if($item_txn_drcr == 'c'){			
			$builder = $this->db->table($itemtxnnnn_tbl);
			$builder->select('item_txn_id, item_bal_qty,item_txn_amount,item_txn_qty'); 
			$builder->where('item_id', $item_id);
			$builder->where('item_unit', $item_unit);
			$builder->where('mat_cent_id', $mat_cent_id);
			$builder->where('batch_id', $batch_id);
			$builder->where('item_txn_drcr', 'd');
			$builder->where('item_avail',$item_avail);
			$builder->groupStart();
		    $builder->where('item_txn_date <', $item_txn_date);
		    $builder->orGroupStart();
		    $builder->where('item_txn_date', $item_txn_date);
		    $builder->where('voucher_txn_id <', $voucher_txn_id);
		    $builder->orGroupStart();
		    $builder->where('item_txn_date', $item_txn_date);
			$builder->where('voucher_txn_id', $voucher_txn_id);
			$builder->where('item_txn_id <=', $item_txn_id);
			$builder->groupEnd();
		    $builder->groupEnd();
		    $builder->groupEnd();	
			$builder->where('bo_id', $this->bo_id);
			if($rewrite_tool==1){
			$builder->orderBy('item_txn_date', 'asc');
			$builder->orderBy('voucher_txn_id', 'asc');
			$builder->orderBy('item_txn_id', 'asc');	
			}
			else{
			$builder->orderBy('item_txn_date', 'desc');
			$builder->orderBy('voucher_txn_id', 'desc');
			$builder->orderBy('item_txn_id', 'desc');	
			}
			
			$builder->limit(1);
			$transaction = $builder->get()->getRowArray();
			if($transaction){
					
				 $builder = $this->db->table($itemtxnvaln_tbl);
				$builder->where('item_id', $item_id);
				$builder->where('item_txn_id', $transaction['item_txn_id']);
				$builder->where('method_id', $method_id);
				$itemtxnvaln = $builder->get()->getRowArray();
				if($itemtxnvaln){
					
					$last_quantity  = floatval($transaction['item_bal_qty']);
					$last_value     = floatval($itemtxnvaln['item_value']);
					$rate           = $last_quantity != 0 ? floatval($last_value/$last_quantity) : 0;
					if($item_bal_qty >0)
					$item_value     = floatval($item_bal_qty * $rate);
				    else 
					$item_value     = 0;	
					
				} 
			}
		}
		if($item_txn_drcr == 'd'){
			// total debit amount / total debit quantity
			$builder = $this->db->table($itemtxnnnn_tbl);
			$builder->select('count(item_txn_id) as debit_transactions, sum(item_txn_qty) as debit_quantity, sum(item_txn_amount) as debit_amount'); 
			$builder->where('item_id', $item_id);
			$builder->where('item_unit', $item_unit);
			$builder->where('mat_cent_id', $mat_cent_id);
			$builder->where('batch_id', $batch_id);
			$builder->where('item_txn_drcr', 'd');
			$builder->where('item_avail', $item_avail);
			$builder->groupStart();
		    $builder->where('item_txn_date <', $item_txn_date);
		    $builder->orGroupStart();
		    $builder->where('item_txn_date', $item_txn_date);
		    $builder->where('voucher_txn_id <', $voucher_txn_id);
		    $builder->orGroupStart();
		    $builder->where('item_txn_date', $item_txn_date);
			$builder->where('voucher_txn_id', $voucher_txn_id);
			$builder->where('item_txn_id <=', $item_txn_id);
			$builder->groupEnd();
		    $builder->groupEnd();
		    $builder->groupEnd();
			$builder->where('bo_id', $this->bo_id);
			$transaction = $builder->get()->getRowArray();
			
			if($transaction){
				if(floatval($transaction['debit_transactions']) > 0){
					
					$total_quantity  = floatval($op_bal_qty + floatval($transaction['debit_quantity']));
					$total_amount    = floatval($op_avg_val + floatval($transaction['debit_amount']));
					$rate            = $total_quantity != 0 ? floatval($total_amount/$total_quantity) : 0;
					if($item_bal_qty >0)
					$item_value      = floatval($item_bal_qty * $rate);
				    else
					$item_value      = 0;	
				}
			}
		}
	
	
	  $builder = $this->db->table($itemtxnvaln_tbl);
	  $builder->where('item_id', $item_id);
	  $builder->where('unit_id', $item_unit);
	  $builder->where('item_txn_id', $item_txn_id);
	  $builder->where('method_id', $method_id);
	  $itemtxnvaln = $builder->get()->getRowArray();		
	  if($itemtxnvaln){
		$valuation_id = $itemtxnvaln['valuation_id'];
		$this->db->table($itemtxnvaln_tbl)
		            ->where('valuation_id', $valuation_id)
					->where('method_id', $method_id)
					->update(array('item_value'=>$item_value));
		}
		else{
			$data = [
				'item_id'        => $item_id,
				'unit_id'        => $item_unit,
				'mat_cent_id'    => $mat_cent_id,
				'item_txn_id'    => $item_txn_id,
				'voucher_txn_id' => $voucher_txn_id,
				'voucher_date'   => $item_txn_date,
				'method_id'      => $method_id,
				'item_value'     => $item_value				
			    ];
			
			$this->db->table($itemtxnvaln_tbl)->insert($data);
			$valuation_id = $this->db->insertID();			
		}		
		   
	  $builder = $this->db->table($itemtxnnnn_tbl);
	  $builder->select('item_txn_id, item_bal_qty'); 
	  $builder->where('item_id', $item_id);
	  $builder->where('item_unit', $item_unit);
	  $builder->where('mat_cent_id', $mat_cent_id);
	  $builder->where('batch_id', $batch_id);
	  $builder->where('item_txn_id', $item_txn_id);
	  $builder->where('item_avail', $item_avail);		  
	  $builder->where('voucher_txn_id',$voucher_txn_id);
      $item_tax_res = $builder->get()->getRowArray();
	  $item_bal_qty =0; 	  
	  if($item_tax_res){
		$item_bal_qty = $item_tax_res['item_bal_qty']; 		
	  }	 
	  
		 $rpt_data     = array("item_id_unit_id"=>$item_id_unit_id,
			                  "voucher_txn_id"=>$voucher_txn_id,"method_id"=>$method_id,
							  "item_value"=>$item_value,"item_txn_id"=>$item_txn_id,
							  "item_bal_qty"=>$item_bal_qty,"item_avail"=>$item_avail,"item_unit"=>$item_unit,
							  "valuation_id"=>$valuation_id,
							  "bo_id"=>$this->bo_id,"item_txn_date"=>$item_txn_date);
		$ifexists = $this->db->table($itemrepbon_tbl)->where("item_id_unit_id",$item_id_unit_id)->where("voucher_txn_id",$voucher_txn_id)->where("method_id",$method_id)->where("item_txn_id",$item_txn_id)->countAllResults();		
	    if($ifexists==0) 
		$this->db->table($itemrepbon_tbl)->insert($rpt_data);
	    else{
		 $this->db->table($itemrepbon_tbl)->where("item_id_unit_id",$item_id_unit_id)->where("voucher_txn_id",$voucher_txn_id)->where("method_id",$method_id)->where("item_txn_id",$item_txn_id)->update(["valuation_id"=>$valuation_id,"item_value"=>$item_value]);		
	     	
		 }
		
		if($method_id==$item_info['valmethod_id']){
		$this->db->table($itemrepbon_tbl)->where("item_id_unit_id",$item_id_unit_id)
										 ->where("voucher_txn_id",$voucher_txn_id)
										 ->where("method_id",0)
										 ->where("item_txn_id",$item_txn_id)
		                                 ->update(["valuation_id"=>$valuation_id,"item_value"=>$item_value,"item_txn_date"=>$item_txn_date]);
         }
		
	

     $itemrepmcn_data     = array("item_id_unit_id"=>$item_id_unit_id,
			                  "voucher_txn_id"=>$voucher_txn_id,"method_id"=>$method_id,
							  "item_value"=>$item_value,"item_txn_id"=>$item_txn_id,
							  "item_bal_qty"=>$item_bal_qty,"item_avail"=>$item_avail,"item_unit"=>$item_unit,
							  "valuation_id"=>$valuation_id,"bo_id"=>$this->bo_id,
							  "mat_cent_id"=>$mat_cent_id,"item_txn_date"=>$item_txn_date);
		$ifexists = $this->db->table($itemrepmcn_tbl)->where("mat_cent_id",$mat_cent_id)->where("item_id_unit_id",$item_id_unit_id)->where("voucher_txn_id",$voucher_txn_id)->where("method_id",$method_id)->where("item_txn_id",$item_txn_id)->countAllResults();		
	    if($ifexists==0) 
		$this->db->table($itemrepmcn_tbl)->insert($itemrepmcn_data);
	    else{
		 $this->db->table($itemrepmcn_tbl)->where("mat_cent_id",$mat_cent_id)->where("item_id_unit_id",$item_id_unit_id)->where("voucher_txn_id",$voucher_txn_id)->where("method_id",$method_id)->where("item_txn_id",$item_txn_id)->update(["valuation_id"=>$valuation_id,"item_value"=>$item_value]);		
	     }
		if($method_id==$item_info['valmethod_id']){
		$this->db->table($itemrepmcn_tbl)->where("item_id_unit_id",$item_id_unit_id)
										 ->where("voucher_txn_id",$voucher_txn_id)
										 ->where("method_id",0)->where("mat_cent_id",$mat_cent_id)
										 ->where("item_txn_id",$item_txn_id)
		                                 ->update(["valuation_id"=>$valuation_id,"item_value"=>$item_value,"item_txn_date"=>$item_txn_date]);
            }
			
		$itemrepall_data     = array("item_id_unit_id"=>$item_id_unit_id,
			                  "voucher_txn_id"=>$voucher_txn_id,"method_id"=>$method_id,
							  "item_value"=>$item_value,"item_txn_id"=>$item_txn_id,
							  "item_bal_qty"=>$item_bal_qty,"item_avail"=>$item_avail,"item_unit"=>$item_unit,
							  "valuation_id"=>$valuation_id,"item_txn_date"=>$item_txn_date);
		$ifexists = $this->db->table($itemrepall_tbl)->where("item_id_unit_id",$item_id_unit_id)->where("voucher_txn_id",$voucher_txn_id)->where("method_id",$method_id)->where("item_txn_id",$item_txn_id)->countAllResults();		
	    if($ifexists==0) 
		$this->db->table($itemrepall_tbl)->insert($itemrepall_data);
	    else{
		 $this->db->table($itemrepall_tbl)->where("item_id_unit_id",$item_id_unit_id)->where("voucher_txn_id",$voucher_txn_id)->where("method_id",$method_id)->where("item_txn_id",$item_txn_id)->update(["valuation_id"=>$valuation_id,"item_value"=>$item_value]);		
	     }
		if($method_id==$item_info['valmethod_id']){
		$this->db->table($itemrepall_tbl)->where("item_id_unit_id",$item_id_unit_id)
										 ->where("voucher_txn_id",$voucher_txn_id)
										 ->where("method_id",0)
										 ->where("item_txn_id",$item_txn_id)
		                                 ->update(["valuation_id"=>$valuation_id,"item_value"=>$item_value,"item_txn_date"=>$item_txn_date]);
            }
			
		$itemrepgrp_data  = array("item_id_unit_id"=>$item_id_unit_id,
			                  "voucher_txn_id"=>$voucher_txn_id,"method_id"=>$method_id,
							  "item_value"=>$item_value,"item_txn_id"=>$item_txn_id,
							  "item_bal_qty"=>$item_bal_qty,"item_avail"=>$item_avail,"item_unit"=>$item_unit,
							  "valuation_id"=>$valuation_id,"bo_id"=>$this->bo_id,
							  "item_grp_id"=>$item_info['item_grp_id'],"item_txn_date"=>$item_txn_date);
		$ifexists = $this->db->table($itemrepgrp_tbl)->where("item_id_unit_id",$item_id_unit_id)->where("voucher_txn_id",$voucher_txn_id)->where("method_id",$method_id)->where("item_txn_id",$item_txn_id)->countAllResults();		
	    if($ifexists==0) 
		$this->db->table($itemrepgrp_tbl)->insert($itemrepgrp_data);
	    else{
		 $this->db->table($itemrepgrp_tbl)->where("item_grp_id",$item_info['item_grp_id'])->where("item_id_unit_id",$item_id_unit_id)->where("voucher_txn_id",$voucher_txn_id)->where("method_id",$method_id)->where("item_txn_id",$item_txn_id)->update(["valuation_id"=>$valuation_id,"item_value"=>$item_value]);		
	     }
		if($method_id==$item_info['valmethod_id']){
		$this->db->table($itemrepgrp_tbl)->where("item_id_unit_id",$item_id_unit_id)
										 ->where("voucher_txn_id",$voucher_txn_id)
										 ->where("method_id",0)->where("item_grp_id",$item_info['item_grp_id'])
										 ->where("item_txn_id",$item_txn_id)
		                                 ->update(["valuation_id"=>$valuation_id,"item_value"=>$item_value,"item_txn_date"=>$item_txn_date]);
            }
				
		$itemrepcat_data  = array("item_id_unit_id"=>$item_id_unit_id,
			                  "voucher_txn_id"=>$voucher_txn_id,"method_id"=>$method_id,
							  "item_value"=>$item_value,"item_txn_id"=>$item_txn_id,
							  "item_bal_qty"=>$item_bal_qty,"item_avail"=>$item_avail,"item_unit"=>$item_unit,
							  "valuation_id"=>$valuation_id,"bo_id"=>$this->bo_id,
							  "item_cat"=>$item_info['item_cat'],"item_txn_date"=>$item_txn_date);
		$ifexists = $this->db->table($itemrepcat_tbl)->where("item_id_unit_id",$item_id_unit_id)->where("voucher_txn_id",$voucher_txn_id)->where("method_id",$method_id)->where("item_txn_id",$item_txn_id)->countAllResults();		
	    if($ifexists==0) 
		$this->db->table($itemrepcat_tbl)->insert($itemrepcat_data);
	    else{
		 $this->db->table($itemrepcat_tbl)->where("item_cat",$item_info['item_cat'])->where("item_id_unit_id",$item_id_unit_id)->where("voucher_txn_id",$voucher_txn_id)->where("method_id",$method_id)->where("item_txn_id",$item_txn_id)->update(["valuation_id"=>$valuation_id,"item_value"=>$item_value]);		
	     }
		if($method_id==$item_info['valmethod_id']){
		$this->db->table($itemrepcat_tbl)->where("item_id_unit_id",$item_id_unit_id)
										 ->where("voucher_txn_id",$voucher_txn_id)
										 ->where("method_id",0)->where("item_cat",$item_info['item_cat'])
										 ->where("item_txn_id",$item_txn_id)
		                                 ->update(["valuation_id"=>$valuation_id,"item_value"=>$item_value,"item_txn_date"=>$item_txn_date]);
            }
			
	
	 	
		$itemrepprj_data  = array("item_id_unit_id"=>$item_id_unit_id,
			                  "voucher_txn_id"=>$voucher_txn_id,"method_id"=>$method_id,
							  "item_value"=>$item_value,"item_txn_id"=>$item_txn_id,"bo_id"=>$this->bo_id,
							  "item_bal_qty"=>$item_bal_qty,"item_avail"=>$item_avail,"item_unit"=>$item_unit,
							  "project_id"=>1,"valuation_id"=>$valuation_id,"item_txn_date"=>$item_txn_date);
		$ifexists = $this->db->table($itemrepprj_tbl)->where("project_id",1)->where("item_id_unit_id",$item_id_unit_id)->where("voucher_txn_id",$voucher_txn_id)->where("method_id",$method_id)->where("item_txn_id",$item_txn_id)->countAllResults();		
	    if($ifexists==0) 
		$this->db->table($itemrepprj_tbl)->insert($itemrepprj_data);
	    else{
		 $this->db->table($itemrepprj_tbl)->where("project_id",1)->where("item_id_unit_id",$item_id_unit_id)->where("voucher_txn_id",$voucher_txn_id)->where("method_id",$method_id)->where("item_txn_id",$item_txn_id)->update(["valuation_id"=>$valuation_id,"item_value"=>$item_value]);		
	     }
		if($method_id==$item_info['valmethod_id']){
		$this->db->table($itemrepprj_tbl)->where("item_id_unit_id",$item_id_unit_id)
										 ->where("voucher_txn_id",$voucher_txn_id)
										 ->where("method_id",0)->where("project_id",1)
										 ->where("item_txn_id",$item_txn_id)
		                                 ->update(["valuation_id"=>$valuation_id,"item_value"=>$item_value,"item_txn_date"=>$item_txn_date]);
            }
		
		
		
		// get batch for items and unit id 
		 $GetItemBatchs = $this->GetItemBatchs($item_id);
		if($GetItemBatchs){
		  foreach($GetItemBatchs as $btrow){
			$batchid =   $btrow['batch_id'];
			$itemrepbat_data  = array("item_id_unit_id"=>$item_id_unit_id,
			                  "voucher_txn_id"=>$voucher_txn_id,"method_id"=>$method_id,
							  "item_value"=>$item_value,"item_txn_id"=>$item_txn_id,
							  "item_bal_qty"=>$item_bal_qty,"item_avail"=>$item_avail,"item_unit"=>$item_unit,
							  "valuation_id"=>$valuation_id,"bo_id"=>$this->bo_id,
							  "batch_id"=>$batchid,"item_txn_date"=>$item_txn_date);
		   
		   $ifexists = $this->db->table($itemrepbat_tbl)->where("batch_id",$batchid)->where("item_id_unit_id",$item_id_unit_id)->where("voucher_txn_id",$voucher_txn_id)->where("method_id",$method_id)->where("item_txn_id",$item_txn_id)->countAllResults();		
	    if($ifexists==0) 
		$this->db->table($itemrepbat_tbl)->insert($itemrepbat_data);
	    else{
		 $this->db->table($itemrepbat_tbl)->where("batch_id",$batchid)->where("item_id_unit_id",$item_id_unit_id)->where("voucher_txn_id",$voucher_txn_id)->where("method_id",$method_id)->where("item_txn_id",$item_txn_id)->update(["valuation_id"=>$valuation_id,"item_value"=>$item_value]);		
	     }
		if($method_id==$item_info['valmethod_id']){
		$this->db->table($itemrepbat_tbl)->where("item_id_unit_id",$item_id_unit_id)
										 ->where("voucher_txn_id",$voucher_txn_id)
										 ->where("method_id",0)->where("batch_id",$batchid)
										 ->where("item_txn_id",$item_txn_id)
		                                 ->update(["valuation_id"=>$valuation_id,"item_value"=>$item_value,"item_txn_date"=>$item_txn_date]);
            }
		    
		    }	
			
		}
	   
		$item_valuation_info=array('item_id_unit_id'=>$item_id_unit_id,'voucher_txn_id'=>$voucher_txn_id,
		                          'item_value'=>$item_value,'item_txn_id'=>$item_txn_id,
								  'item_bal_qty'=>$item_bal_qty,'item_unit'=>$item_unit,'item_avail'=>$item_avail,
								  'valuation_id'=>$valuation_id,'item_txn_date'=>$item_txn_date); 
		return $item_valuation_info;
			
	
	}
	
	public function update_item_avg_valuation_buffernew($info, $method_id, $rewrite_tool = 0)
{
    $buffercalc_tbl = $this->company_id . '_buffercalc_' . $this->session->get('ses_comp_fy_id');
    $itemtxnvaln_tbl = $this->company_id . '_itemtxnval_' . $info['item_id'] . '_' . $this->session->get('ses_comp_fy_id');
    $itemtxnnnn_tbl  = $this->company_id . '_itemtxnnnn_' . $info['item_id'] . '_' . $this->session->get('ses_comp_fy_id');

    $item_id_unit_avail_id = $info['item_id'] . '_' . $info['item_unit'] . '_' . $info['item_avail'];
    $item_id_unit_id = $info['item_id'] . '_' . $info['item_unit'];

    // Set item rate
    $item_rate = ($info['item_txn_qty'] > 0) ? parseAmountPrice($info['item_txn_amount'] / $info['item_txn_qty'], 4) : 1;

    // Insert valuation base record
    $valuation_data = [
        'item_id'        => $info['item_id'],
        'unit_id'        => $info['item_unit'],
        'mat_cent_id'    => $info['mat_cent_id'],
        'item_txn_id'    => $info['item_txn_id'],
        'voucher_txn_id' => $info['voucher_txn_id'],
        'voucher_date'   => $info['item_txn_date'],
        'method_id'      => $method_id,
        'item_value'     => 0
    ];
    $this->db->table($itemtxnvaln_tbl)->insert($valuation_data);
    $valuation_id = $this->db->insertID();

    if ($info['item_txn_drcr'] == 'd') {
        // Insert buffer for purchase
        $buffer_data = [
            'cont_item_id_unit_avail_id' => $item_id_unit_avail_id,
            'method_id'                  => $method_id,
            'txn_id'                     => 0,
            'buffer_ball_qty'           => parseAmount($info['item_txn_qty']),
            'buffer_ball_rate'          => $item_rate,
            'valuation_id'              => $valuation_id,
            'voucher_txn_id'            => $info['voucher_txn_id']
        ];
        $this->db->table($buffercalc_tbl)->insert($buffer_data);
    } elseif ($info['item_txn_drcr'] == 'c') {
        // Consumption
        $buffers = $this->db->table($buffercalc_tbl)
            ->where('cont_item_id_unit_avail_id', $item_id_unit_avail_id)
            ->where('method_id', $method_id)
            ->orderBy('buffer_ball_id', 'asc')
            ->get()
            ->getResultArray();

        $pending_qty = parseAmount($info['item_txn_qty']);
        $total_balance = 0;

        foreach ($buffers as $row) {
            $buffer_id = $row['buffer_ball_id'];
            $buffer_qty = parseAmount($row['buffer_ball_qty']);

            if ($pending_qty <= 0) break;

            if ($buffer_qty > $pending_qty) {
                $this->db->table($buffercalc_tbl)
                    ->where('buffer_ball_id', $buffer_id)
                    ->update(['buffer_ball_qty' => $buffer_qty - $pending_qty]);
                $pending_qty = 0;
            } else {
                $this->db->table($buffercalc_tbl)
                    ->where('buffer_ball_id', $buffer_id)
                    ->delete();
                $pending_qty -= $buffer_qty;
            }
        }

        // Re-check buffer balance
        $total_balance = $this->db->table($buffercalc_tbl)
            ->selectSum('buffer_ball_qty')
            ->where('cont_item_id_unit_avail_id', $item_id_unit_avail_id)
            ->where('method_id', $method_id)
            ->get()
            ->getRow('buffer_ball_qty');

        if (parseAmount($total_balance) == 0) {
            // buffer now empty => record buffer point and clean old entries
            $first_zero_txn = $info['item_txn_id'];

            // Delete all existing records for this container
            $this->db->table($buffercalc_tbl)
                ->where('cont_item_id_unit_avail_id', $item_id_unit_avail_id)
                ->where('method_id', $method_id)
                ->delete();

            // Insert buffer point marker
            $this->db->table($buffercalc_tbl)->insert([
                'cont_item_id_unit_avail_id' => $item_id_unit_avail_id,
                'method_id'                  => $method_id,
                'txn_id'                     => $first_zero_txn,
                'buffer_ball_qty'           => 0,
                'buffer_ball_rate'          => 0,
                'valuation_id'              => $valuation_id,
                'voucher_txn_id'            => $info['voucher_txn_id']
            ]);
        }
    }

    // === AVG Cost Calculation from Buffer Point ===
    $buffer_point = $this->db->table($buffercalc_tbl)
        ->selectMin('buffer_ball_id', 'min_id')
        ->where('cont_item_id_unit_avail_id', $item_id_unit_avail_id)
        ->where('method_id', $method_id)
        ->where('txn_id !=', 0)
        ->get()
        ->getRow('min_id');

    $avg_data = $this->db->table($buffercalc_tbl)
        ->select('SUM(buffer_ball_qty) as total_qty, SUM(buffer_ball_qty * buffer_ball_rate) as total_value')
        ->where('cont_item_id_unit_avail_id', $item_id_unit_avail_id)
        ->where('method_id', $method_id)
        ->where('buffer_ball_qty >', 0)
        ->where('buffer_ball_id >', $buffer_point)
        ->get()
        ->getRowArray();

    $avg_rate = 0;
    if ($avg_data && $avg_data['total_qty'] > 0) {
        $avg_rate = parseAmountPrice($avg_data['total_value'] / $avg_data['total_qty'], 4);
    }

    $item_value = parseAmount($info['item_txn_qty'] * $avg_rate);

    $this->db->table($itemtxnvaln_tbl)
        ->where('valuation_id', $valuation_id)
        ->update(['item_value' => $item_value]);

    // Get final item balance qty
    $bal_data = $this->db->table($itemtxnnnn_tbl)
        ->where('item_id', $info['item_id'])
        ->where('item_unit', $info['item_unit'])
        ->where('mat_cent_id', $info['mat_cent_id'])
        ->where('batch_id', $info['batch_id'])
        ->where('item_txn_id', $info['item_txn_id'])
        ->where('item_avail', $info['item_avail'])
        ->where('voucher_txn_id', $info['voucher_txn_id'])
        ->get()
        ->getRowArray();

    return [
        'item_id_unit_id' => $item_id_unit_id,
        'voucher_txn_id' => $info['voucher_txn_id'],
        'item_value' => $item_value,
        'item_txn_id' => $info['item_txn_id'],
        'item_bal_qty' => $bal_data['item_bal_qty'] ?? 0,
        'item_unit' => $info['item_unit'],
        'item_avail' => $info['item_avail'],
        'valuation_id' => $valuation_id,
        'item_txn_date' => $info['item_txn_date']
    ];
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
              $item_unit_name = $item_unit_info['item_unit'];
  	          
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
              $cu_unit_name =  $cu_unit_info['item_unit'];
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
              $cu_unit_name =  $cu_unit_info['item_unit'];
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
              $cu_unit_name =  $cu_unit_info['item_unit'];
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
              $item_unit_name = $item_unit_info['item_unit'];
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
              $cu_unit_name = $cu_unit_info['item_unit'];
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
        $cu_unit_name = $cu_unit_info['item_unit'];
        
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
                    // $this->delete_itm_bal_data($value['master_id'],$voucher_txn_id);
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

function get_currency_info($comp_currency_id)
	{
		$compcurrcy_tbl = $this->company_id.'_compcurrcy_'.$this->session->get('ses_comp_fy_id');
    	$data = $this->db->table($compcurrcy_tbl)->where('comp_id', $this->company_id)->where('comp_currency_id', $comp_currency_id)->get()->getRowArray();

    	if($data){
    		return $data;
    	}
    	return '';
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
    
	public function get_fcrates($voucher_txn_id)
	{
        $acctcrsref_tbl = $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');;
    	$builder = $this->db->table($acctcrsref_tbl);
    	$builder->where('voucher_txn_id', $voucher_txn_id);
		$builder->where('acc_cross_ref_type', 'FOREXRT');
        $builder->limit(1);
    	$result = $builder->get()->getRowArray();
    	if($result){
    		return $result['acc_cross_ref_data'];
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

	function get_item_op_val($item_id,$item_unit,$mat_cent_id,$batch_id,$method_id,$bo_id=0)
	{
		if($bo_id==0 || $bo_id=='')
			$bo_id = $this->bo_id;
		$op_val = 0;

		$itmoppyval_tbl = $this->company_id.'_itmoppyval_'.$this->session->get('ses_comp_fy_id');
		$builder = $this->db->table($itmoppyval_tbl); 
		$builder->where('item_id', $item_id);
		$builder->where('item_unit', $item_unit);
		$builder->where('mat_cent_id', $mat_cent_id);
		$builder->where('bo_id', $bo_id);
		$builder->where('batch_id', $batch_id);
		$builder->where('method_id', $method_id);
		$itmoppyval = $builder->get()->getRowArray();
		
		if($itmoppyval){
    		$op_val = floatval($itmoppyval['op_bal_val']);
    	}
    	else{
    		$data = [
    			'comp_id'		=> $this->company_id,
    			'item_id'		=> $item_id,
    			'item_unit'		=> $item_unit,
    			'mat_cent_id'	=> $mat_cent_id,
    			'bo_id'			=> $bo_id,
    			'batch_id'		=> $batch_id,
    			'method_id'		=> $method_id,
    			'op_bal_val'	=> 0,
    			'py_bal_val' 	=> 0,
    		];
    		$this->db->table($itmoppyval_tbl)->insert($data);
    	}

    	return $op_val;
	}

	public function get_boms(){
	   $billofmatn_tbl =  $this->company_id.'_billofmatn_'.$this->session->get('ses_comp_fy_id');	 
	   $data           =  $this->db->table($billofmatn_tbl)->orderBy('bom_name','ASC')->get()->getResultArray();
	   $final_result      = array();
	   if($data){
		  foreach($data as $row){
		      $bom_name       = $row['bom_name'];
              $final_result[] =array("id"=>$row['bom_id'],"name"=>$bom_name);			   
	        }
        }
	  return $final_result;	
	}

  	/* public function update_item_avg_valuation($item_id,$item_unit,$mat_cent_id,$batch_id,$op_bal_qty,$op_avg_val,$item_txn_id,$item_txn_drcr,$item_txn_qty,$item_txn_date,$voucher_txn_id,$item_avail,$item_bal_qty,$item_txn_amount)
  	{

  	  $item_value = 0;
  	  $profit = 0;
  	  $profit_string = '';

  	  $itemtxnnnn_tbl = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
  	  $itemtxnvaln_tbl  = $this->company_id.'_itemtxnval_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');

  	  if($item_avail == 1)
  	  {
  	  	$rate = $op_bal_qty != 0 ? floatval($op_avg_val/$op_bal_qty) : 0;
		$item_value = floatval($item_bal_qty * $rate);

  	  	if($item_txn_drcr == 'c')
		{
			//check last debit transaction
			$builder = $this->db->table($itemtxnnnn_tbl);
			$builder->select('item_txn_id, item_bal_qty'); 
			$builder->where('item_id', $item_id);
			$builder->where('item_unit', $item_unit);
			$builder->where('mat_cent_id', $mat_cent_id);
			$builder->where('batch_id', $batch_id);
			$builder->where('item_txn_drcr', 'd');
			$builder->where('item_avail', 1);
			$builder->groupStart();
		        $builder->where('item_txn_date <', $item_txn_date);
		        $builder->orGroupStart();
		        	$builder->where('item_txn_date', $item_txn_date);
		            $builder->where('voucher_txn_id <', $voucher_txn_id);
		            $builder->orGroupStart();
		            	$builder->where('item_txn_date', $item_txn_date);
			        	$builder->where('voucher_txn_id', $voucher_txn_id);
			            $builder->where('item_txn_id <=', $item_txn_id);
			        $builder->groupEnd();
		        $builder->groupEnd();
		    $builder->groupEnd();	
			
			$builder->where('bo_id', $this->bo_id);
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
					$last_quantity = floatval($transaction['item_bal_qty']);
					$last_value = floatval($itemtxnvaln['item_value']);
					$rate = $last_quantity != 0 ? floatval($last_value/$last_quantity) : 0;
					$item_value = floatval($item_bal_qty * $rate);

					$sp = $item_txn_qty != 0 ? floatval($item_txn_amount/$item_txn_qty) : 0;
					$profit_rate = floatval($sp - $rate); // sp - cp
					$profit = floatval($item_txn_qty * $profit_rate);

					$profit_string = 'cp '.parseAmount($last_value).' / '.parseAmount($last_quantity).' = '.parseAmount($rate);
					$profit_string .= '<br>sp '.parseAmount($item_txn_amount).' / '.parseAmount($item_txn_qty).' = '.parseAmount($sp);
					$profit_string .= '<br>('.parseAmount($sp).' - '.parseAmount($rate).') * '.parseAmount($item_txn_qty).' = '.parseAmount($profit);
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

			$builder->groupStart();
		        $builder->where('item_txn_date <', $item_txn_date);
		        $builder->orGroupStart();
		        	$builder->where('item_txn_date', $item_txn_date);
		            $builder->where('voucher_txn_id <', $voucher_txn_id);
		            $builder->orGroupStart();
		            	$builder->where('item_txn_date', $item_txn_date);
			        	$builder->where('voucher_txn_id', $voucher_txn_id);
			            $builder->where('item_txn_id <=', $item_txn_id);
			        $builder->groupEnd();
		        $builder->groupEnd();
		    $builder->groupEnd();

			$builder->where('bo_id', $this->bo_id);
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
					->update(['item_value' => $item_value, 'profit' => $profit, 'profit_string' => $profit_string, 'voucher_txn_id' => $voucher_txn_id,'voucher_date' => $item_txn_date,'unit_id' => $item_unit,'mat_cent_id' => $mat_cent_id,]);

		}
		else{
			$data = [
				'item_id' => $item_id,
				'unit_id' => $item_unit,
				'mat_cent_id' => $mat_cent_id,
				'item_txn_id' => $item_txn_id,
				'voucher_txn_id' => $voucher_txn_id,
				'voucher_date' => $item_txn_date,
				'method_id' => 1,
				'item_value' => $item_value,
				'profit' => $profit,
				'profit_string' => $profit_string,
			];
			$this->db->table($itemtxnvaln_tbl)->insert($data);
		}
  	}

  	public function update_item_fifo_valuation($item_id,$item_unit,$mat_cent_id,$batch_id,$op_bal_qty,$op_fifo_val,$item_txn_id,$item_txn_drcr,$item_txn_qty,$item_txn_date,$voucher_txn_id,$item_avail,$item_bal_qty,$item_txn_amount)
  	{

  	  $item_value = 0;
  	  $profit = 0;
  	  $profit_string = '';
  	  $cp_string = '';

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
    	$item_txn_id;

  	  	$builder = $this->db->table($itemtxnnnn_tbl);
		$builder->select('count(item_txn_id) as credit_transactions, sum(item_txn_qty) as credit_quantity'); 
		$builder->where('item_id', $item_id);
		$builder->where('item_unit', $item_unit);
		$builder->where('mat_cent_id', $mat_cent_id);
		$builder->where('batch_id', $batch_id);
		$builder->where('item_txn_drcr', 'c');
		$builder->where('item_avail', 1);

		$builder->groupStart();
	        $builder->where('item_txn_date <', $item_txn_date);
	        $builder->orGroupStart();
	        	$builder->where('item_txn_date', $item_txn_date);
	            $builder->where('voucher_txn_id <', $voucher_txn_id);
	            $builder->orGroupStart();
	            	$builder->where('item_txn_date', $item_txn_date);
		        	$builder->where('voucher_txn_id', $voucher_txn_id);
		            $builder->where('item_txn_id <=', $item_txn_id);
		        $builder->groupEnd();
	        $builder->groupEnd();
	    $builder->groupEnd();
	
		$builder->where('bo_id', $this->bo_id);
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
    		$cp_string = 'cp '.parseAmount($op_fifo_val).' / '.parseAmount($op_bal_qty).' = '.parseAmount($rate);
    	}
    	else{
    		$builder = $this->db->table($itemtxnnnn_tbl);
	        $builder->where('item_id', $item_id);
			$builder->where('item_unit', $item_unit);
			$builder->where('mat_cent_id', $mat_cent_id);
			$builder->where('batch_id', $batch_id);
			$builder->where('item_txn_drcr', 'd');
			$builder->where('item_avail', 1);
			$builder->groupStart();
		        $builder->where('item_txn_date <', $item_txn_date);
		        $builder->orGroupStart();
		        	$builder->where('item_txn_date', $item_txn_date);
		            $builder->where('voucher_txn_id <', $voucher_txn_id);
		            $builder->orGroupStart();
		            	$builder->where('item_txn_date', $item_txn_date);
			        	$builder->where('voucher_txn_id', $voucher_txn_id);
			            $builder->where('item_txn_id <=', $item_txn_id);
			        $builder->groupEnd();
		        $builder->groupEnd();
		    $builder->groupEnd();

			$builder->where('bo_id', $this->bo_id);
			$builder->orderBy('item_txn_date', 'asc');
			$builder->orderBy('voucher_txn_id', 'asc');
			$builder->orderBy('item_txn_id', 'asc');
        	$transactions = $builder->get()->getResultArray();

        	$bal = $op_bal_qty; 
        	if($transactions){
        		foreach ($transactions as $key => $transaction) {
        			$bal += floatval($transaction['item_txn_qty']);

        			if($bal  >= $credit_quantity){

			        	$left_quantity = $bal - $credit_quantity; 
			        	$rate = $transaction['item_txn_qty'] != 0 ? floatval($transaction['item_txn_amount']/$transaction['item_txn_qty']) : 0;

    					$cp_string = 'cp '.parseAmount($transaction['item_txn_amount']).'/'.parseAmount($transaction['item_txn_qty']).' = '.parseAmount($rate);

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
		$builder->groupStart();
	        $builder->where('item_txn_date <', $item_txn_date);
	        $builder->orGroupStart();
	        	$builder->where('item_txn_date', $item_txn_date);
	            $builder->where('voucher_txn_id <', $voucher_txn_id);
	            $builder->orGroupStart();
	            	$builder->where('item_txn_date', $item_txn_date);
		        	$builder->where('voucher_txn_id', $voucher_txn_id);
		            $builder->where('item_txn_id <=', $item_txn_id);
		        $builder->groupEnd();
	        $builder->groupEnd();
	    $builder->groupEnd();

		if($case == 2){
			$builder->groupStart();
		        $builder->where('item_txn_date >', $debit_item_txn_date);
		        $builder->orGroupStart();
		        	$builder->where('item_txn_date', $debit_item_txn_date);
		            $builder->where('voucher_txn_id >', $debit_voucher_txn_id);
		            $builder->orGroupStart();
		            	$builder->where('item_txn_date', $debit_item_txn_date);
			        	$builder->where('voucher_txn_id', $debit_voucher_txn_id);
			            $builder->where('item_txn_id >', $debit_item_txn_id);
			        $builder->groupEnd();
		        $builder->groupEnd();
		    $builder->groupEnd();
		}	
	
		$builder->where('bo_id', $this->bo_id);
		$transaction = $builder->get()->getRowArray();
		if($transaction){
			if(floatval($transaction['debit_transactions']) > 0){
				$debit_amount = floatval($transaction['debit_amount']);
			}
		}

        $item_value = floatval(($left_quantity * $rate) + $debit_amount);

        if($item_txn_drcr == 'c')
		{
			$cp = $rate;//$item_bal_qty != 0 ? floatval($item_value/$item_bal_qty) : 0;
			$sp = $item_txn_qty != 0 ? floatval($item_txn_amount/$item_txn_qty) : 0;
			$profit_rate = floatval($sp - $cp); // sp - cp
			$profit = floatval($item_txn_qty * $profit_rate);

			$profit_string = $cp_string;
			$profit_string .= '<br>sp '.parseAmount($item_txn_amount).' / '.parseAmount($item_txn_qty).' = '.parseAmount($sp);
			$profit_string .= '<br>('.parseAmount($sp).' - '.parseAmount($rate).') * '.parseAmount($item_txn_qty).' = '.parseAmount($profit);
		}
        

	  }

	  	$builder = $this->db->table($itemtxnvaln_tbl);
		$builder->where('item_id', $item_id);
		$builder->where('item_txn_id', $item_txn_id);
		$builder->where('method_id', 2);
		$itemtxnvaln = $builder->get()->getRowArray();
		
		if($itemtxnvaln){
			$valuation_id = $itemtxnvaln['valuation_id'];
			$this->db->table($itemtxnvaln_tbl)
					->where('valuation_id', $valuation_id)
					->update(['item_value' => $item_value, 'profit' => $profit, 'profit_string' => $profit_string, 'voucher_txn_id' => $voucher_txn_id, 'voucher_date' => $item_txn_date, 'unit_id' => $item_unit, 'mat_cent_id' => $mat_cent_id,]);
		}
		else{
			$data = [
				'item_id' => $item_id,
				'unit_id' => $item_unit,
				'mat_cent_id' => $mat_cent_id,
				'item_txn_id' => $item_txn_id,
				'voucher_txn_id' => $voucher_txn_id,
				'voucher_date' => $item_txn_date,
				'method_id' => 2,
				'item_value' => $item_value,
				'profit' => $profit,
				'profit_string' => $profit_string, 
			];
			$this->db->table($itemtxnvaln_tbl)->insert($data);
		}

  	} */

  	public function delete_transaction_voucher($voucher_txn_id)
  	{
  		
  		$voucher_info =  $this->get_voucher_cons_info($voucher_txn_id);
  		/* if(!$voucher_info){
  			return ['status' => false, 'message' => 'Voucher does not exists']; 
  		} */
     if($voucher_info){
  		$status = false;
  		$message = '';

        $voucher_type_id = $voucher_info['voucher_type_id'];
        $this->delete_valuation_tbls_txn_data($voucher_txn_id);
        if($voucher_type_id == '18') //Sales
        {
            $check = $this->check_crsref($voucher_txn_id, 'SALEINV');
            if($check)
            {
                $status = false;
                $message = 'Sales Voucher No. '.$voucher_info['comp_vch_no'].' has linked Credit Note or Delivery Challan';
            }
            else
            {
                $status = true;
                $vch_subtype_id = $voucher_info['vch_subtype_id'];
                if($vch_subtype_id == 10){
                    $delivery_challan_id = $this->get_crsref($voucher_txn_id, 'DELCHAL');
                }

                $data = $this->get_comp_txn_data($voucher_txn_id);
                foreach ($data as $key => $value) {
                    if($value['master_id_type'] == 'acc'){
                            $this->delete_acc_txn_data($value['master_id'],$voucher_txn_id);
                    }
                    if($value['master_id_type'] == 'itm'){
                            $this->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
                    }
                    if($value['master_id_type'] == 'bsd'){
                            $this->delete_sundry_txn_data($value['master_id'],$voucher_txn_id);
                    }
                }
				
				$this->delete_memo_txn_data($voucher_txn_id);
				$this->delete_gstroutsup_data($voucher_txn_id);
				$this->delete_acctgstsum_data($voucher_txn_id);
				$this->delete_acc_crsref_data_by_tag($voucher_txn_id,'FOREXRT');
				$this->delete_taxinc_txn_data($voucher_txn_id,'acc');
				
                $this->delete_acc_oth_data($voucher_txn_id);
                $this->delete_acc_crsref_data($voucher_txn_id);
                $this->delete_itm_oth_data($voucher_txn_id);
                $this->delete_comp_txn_data($voucher_txn_id);
                $this->delete_voucher_conso_data($voucher_txn_id);
                $this->delete_bills_txn($voucher_txn_id);
                $this->delete_all_narrations($voucher_txn_id);
					
				$this->delete_gstrinwsup_data($voucher_txn_id);
				
                
                if($vch_subtype_id == 7 && $delivery_challan_id){
                    $voucher_tag = $voucher_info['voucher_tag'];
                    $count = $this->count_vouchers_by_tag($voucher_type_id,$voucher_tag);
                    if($count > 0)
                    {
                        
                        $summary = $this->get_party_oth_summary($delivery_challan_id,'DCES');
                        $cr_total = $summary['cr_total'];
                        $dr_total = $summary['dr_total'];

                        if($cr_total > $dr_total)  
                            $this->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESDEF");
                      
                        if($cr_total < $dr_total)
                            $this->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESSUR");

                        if($cr_total == $dr_total)
                            $this->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESEXE");
                    }
                    else
                    {
                        $this->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESDUE");
                    }
                }
            }  
        }

        if($voucher_type_id == '11') //Purchase
        {
            $check = $this->check_crsref($voucher_txn_id, 'PURCINV');
            if($check){
                $status = false;
                $message = 'Purchase Voucher No. '.$voucher_info['comp_vch_no'].' has linked Debit Note or Inward Challan';
               
            }
            else
            {
                $status = true;
                $vch_subtype_id = $voucher_info['vch_subtype_id'];

                if($vch_subtype_id == 7){
                    $inward_challan_id = $this->get_crsref($voucher_txn_id, 'INWCHAL');
                }

                $data = $this->get_comp_txn_data($voucher_txn_id);
                foreach ($data as $key => $value) {
                        if($value['master_id_type'] == 'acc'){
                                $this->delete_acc_txn_data($value['master_id'],$voucher_txn_id);
                        }
                        if($value['master_id_type'] == 'itm'){
                                $this->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
                        }
                        if($value['master_id_type'] == 'bsd'){
                                $this->delete_sundry_txn_data($value['master_id'],$voucher_txn_id);
                        }
                }
				
				$this->delete_memo_txn_data($voucher_txn_id);
				$this->delete_gstrinwsup_data($voucher_txn_id);
				$this->delete_acctgstsum_data($voucher_txn_id);
				$this->delete_acc_crsref_data_by_tag($voucher_txn_id,'FOREXRT');
				$this->delete_taxinc_txn_data($voucher_txn_id,'acc');
				
                $this->delete_acc_oth_data($voucher_txn_id);
                $this->delete_acc_crsref_data($voucher_txn_id);
                $this->delete_itm_oth_data($voucher_txn_id);
                $this->delete_comp_txn_data($voucher_txn_id);
                $this->delete_voucher_conso_data($voucher_txn_id);

                $this->delete_cc_txn($voucher_txn_id);
                $this->delete_bills_txn($voucher_txn_id);                 
                $this->delete_all_narrations($voucher_txn_id);
                
                if($vch_subtype_id == 7 && $inward_challan_id){
                    $voucher_tag = $voucher_info['voucher_tag'];
                    $count = $this->count_vouchers_by_tag($voucher_type_id,$voucher_tag);
                    if($count > 0)
                    {
                        
                      $summary = $this->get_party_oth_summary($inward_challan_id,'ICEP');
                    $cr_total = $summary['cr_total'];
                    $dr_total = $summary['dr_total'];

                    if($cr_total > $dr_total)  
                        $this->update_accttxnoth_party_status($inward_challan_id,'ICEP',"ICEPDEF");
                  
                      if($cr_total < $dr_total)
                            $this->update_accttxnoth_party_status($inward_challan_id,'ICEP',"ICEPSUR");

                      if($cr_total == $dr_total)
                            $this->update_accttxnoth_party_status($inward_challan_id,'ICEP',"ICEPEXE");
                    }
                    else
                    {
                            $this->update_accttxnoth_party_status($inward_challan_id,'ICEP',"ICEPDUE");
                    }
                } 
            }    
        }

        if($voucher_type_id == '2') //Credit Note
        {
            //check inward challan
            $check = $this->check_crsref($voucher_txn_id, 'CRNTINV');
            if($check){
                $status = false;
                $message = 'Credit Note No. '.$voucher_info['comp_vch_no'].' has associated Inward Challan.';
                
            }
            else
            {
                $status = true;
                $voucher_tag = $voucher_info['voucher_tag'];
                $sale_voucher_id = $this->get_crsref($voucher_txn_id, 'SALEINV');

                $data = $this->get_comp_txn_data($voucher_txn_id);
                foreach ($data as $key => $value) {
                    if($value['master_id_type'] == 'acc'){
                        $this->delete_acc_txn_data($value['master_id'],$voucher_txn_id);
                    }
                    if($value['master_id_type'] == 'itm'){
                        $this->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
                    }
                    if($value['master_id_type'] == 'bsd'){
                        $this->delete_sundry_txn_data($value['master_id'],$voucher_txn_id);
                    }
                }
				
				$this->delete_memo_txn_data($voucher_txn_id);
				$this->delete_gstroutsup_data($voucher_txn_id);
				$this->delete_acctgstsum_data($voucher_txn_id);
				$this->delete_acc_crsref_data_by_tag($voucher_txn_id,'FOREXRT');
				$this->delete_taxinc_txn_data($voucher_txn_id,'acc');
				
                $this->delete_acc_oth_data($voucher_txn_id);
                $this->delete_acc_crsref_data($voucher_txn_id);
                $this->delete_itm_oth_data($voucher_txn_id);
                $this->delete_comp_txn_data($voucher_txn_id);
                $this->delete_voucher_conso_data($voucher_txn_id);

                $this->delete_bills_txn($voucher_txn_id);
                $this->delete_all_narrations($voucher_txn_id);

                if(str_contains($voucher_tag, 'SEDC'))
                {
                    $summary = $this->get_party_oth_summary($sale_voucher_id,'SEDC');
                    $cr_total = $summary['cr_total'];
                    $dr_total = $summary['dr_total'];

                    if($cr_total > $dr_total)  
                        $this->update_accttxnoth_party_status($sale_voucher_id,'SEDC',"SEDCDEF");
                  
                    if($cr_total < $dr_total)
                        $this->update_accttxnoth_party_status($sale_voucher_id,'SEDC',"SEDCSUR");

                    if($cr_total == $dr_total)
                        $this->update_accttxnoth_party_status($sale_voucher_id,'SEDC',"SEDCEXE");
                }
                if(str_contains($voucher_tag, 'DCES'))
                {
                    $delivery_challan_id = $this->get_crsref($sale_voucher_id, 'DELCHAL');
                    $summary = $this->get_party_oth_summary($delivery_challan_id,'DCES');
                    $cr_total = $summary['cr_total'];
                    $dr_total = $summary['dr_total'];

                    if($cr_total > $dr_total)  
                        $this->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESDEF");
                  
                    if($cr_total < $dr_total)
                        $this->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESSUR");

                    if($cr_total == $dr_total)
                        $this->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESEXE");
                }  
            }   
        }

        if($voucher_type_id == '3') //Debit Note
        {
            //check delivery challan
            $check = $this->check_crsref($voucher_txn_id, 'DRNTINV');
            if($check){
                $status = false;
                $message = 'Debit Note No. '.$voucher_info['comp_vch_no'].' has associated Delivery Challan.';
            }
            else
            {
                $status = true;
                $voucher_tag = $voucher_info['voucher_tag'];
                $purchase_voucher_id = $this->get_crsref($voucher_txn_id, 'PURCINV');

                $data = $this->get_comp_txn_data($voucher_txn_id);
                foreach ($data as $key => $value) {
                    if($value['master_id_type'] == 'acc'){
                        $this->delete_acc_txn_data($value['master_id'],$voucher_txn_id);
                    }
                    if($value['master_id_type'] == 'itm'){
                        
                        $this->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
                    }
                    if($value['master_id_type'] == 'bsd'){
                        $this->delete_sundry_txn_data($value['master_id'],$voucher_txn_id);
                    }
                }
				
				$this->delete_memo_txn_data($voucher_txn_id);
				$this->delete_gstroutsup_data($voucher_txn_id);
				$this->delete_acctgstsum_data($voucher_txn_id);
				$this->delete_acc_crsref_data_by_tag($voucher_txn_id,'FOREXRT');
				$this->delete_taxinc_txn_data($voucher_txn_id,'acc');
                $this->delete_acc_oth_data($voucher_txn_id);
                $this->delete_acc_crsref_data($voucher_txn_id);
                $this->delete_itm_oth_data($voucher_txn_id);
                $this->delete_comp_txn_data($voucher_txn_id);
                $this->delete_voucher_conso_data($voucher_txn_id);

                $this->delete_cc_txn($voucher_txn_id);
                $this->delete_bills_txn($voucher_txn_id);
                $this->delete_all_narrations($voucher_txn_id);
                if(str_contains($voucher_tag, 'PESI'))
                {
                      $summary = $this->get_party_oth_summary($purchase_voucher_id,'PESI');
                    $cr_total = $summary['cr_total'];
                    $dr_total = $summary['dr_total'];

                    if($cr_total > $dr_total)  
                        $this->update_accttxnoth_party_status($purchase_voucher_id,'PESI',"PESIDEF");
                  
                    if($cr_total < $dr_total)
                        $this->update_accttxnoth_party_status($purchase_voucher_id,'PESI',"PESISUR");

                    if($cr_total == $dr_total)
                        $this->update_accttxnoth_party_status($purchase_voucher_id,'PESI',"PESIEXE");
                }
                if(str_contains($voucher_tag, 'ICEP'))
                {
                    $inward_challan_id = $this->get_crsref($purchase_voucher_id, 'INWCHAL');
                    $summary = $this->get_party_oth_summary($inward_challan_id,'ICEP');
                    $cr_total = $summary['cr_total'];
                    $dr_total = $summary['dr_total'];

                    if($cr_total > $dr_total)  
                        $this->update_accttxnoth_party_status($inward_challan_id,'ICEP',"ICEPDEF");
                  
                    if($cr_total < $dr_total)
                        $this->update_accttxnoth_party_status($inward_challan_id,'ICEP',"ICEPSUR");

                    if($cr_total == $dr_total)
                        $this->update_accttxnoth_party_status($inward_challan_id,'ICEP',"ICEPEXE");
                }
            }   
        }

        if($voucher_type_id == '7') //Material Out
        {

            $vch_subtype_id = $voucher_info['vch_subtype_id'];

            $flag = 1;
            if($vch_subtype_id == 13){// check against sale
                $check = $this->check_crsref($voucher_txn_id, 'DELCHAL');
                if($check){
                    $status = false;
                    $message = 'Delivery Challan No. '.$voucher_info['comp_vch_no'].' has linked Sale Voucher';
                    $flag = 0; 
                }
            }

            if($vch_subtype_id == 14){// against sale
                $sale_voucher_id = $this->get_crsref($voucher_txn_id, 'SALEINV');
            }
            if($vch_subtype_id == 22){// against debit note
                $debitnote_voucher_id = $this->get_crsref($voucher_txn_id, 'DRNTINV');
            }

            if($flag)
            {
                $status = true;
                $data = $this->get_comp_txn_data($voucher_txn_id);
                foreach ($data as $key => $value) {
                        if($value['master_id_type'] == 'acc'){
                                $this->delete_acc_txn_data($value['master_id'],$voucher_txn_id);
                        }
                        if($value['master_id_type'] == 'itm'){
                                
                                $this->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
                        }
                        if($value['master_id_type'] == 'bsd'){
                                $this->delete_sundry_txn_data($value['master_id'],$voucher_txn_id);
                        }
                }
				
				$this->delete_memo_txn_data($voucher_txn_id);
				$this->delete_gstroutsup_data($voucher_txn_id);
				$this->delete_acctgstsum_data($voucher_txn_id);
				$this->delete_acc_crsref_data_by_tag($voucher_txn_id,'FOREXRT');
				$this->delete_taxinc_txn_data($voucher_txn_id,'acc');
				
                $this->delete_acc_oth_data($voucher_txn_id);
                $this->delete_acc_crsref_data($voucher_txn_id);
                $this->delete_itm_oth_data($voucher_txn_id);
                $this->delete_comp_txn_data($voucher_txn_id);
                $this->delete_voucher_conso_data($voucher_txn_id);
                $this->delete_all_narrations($voucher_txn_id);

                if($vch_subtype_id == 14 && $sale_voucher_id){// against sale
                        $voucher_tag = $voucher_info['voucher_tag'];
                        $count = $this->count_vouchers_by_tag($voucher_type_id,$voucher_tag);
                        if($count > 0)
                        {
                            
                          $summary = $this->get_party_oth_summary($sale_voucher_id,'SEDC');
                        $cr_total = $summary['cr_total'];
                        $dr_total = $summary['dr_total'];

                        if($cr_total > $dr_total)  
                            $this->update_accttxnoth_party_status($sale_voucher_id,'SEDC',"SEDCDEF");
                      
                      if($cr_total < $dr_total)
                            $this->update_accttxnoth_party_status($sale_voucher_id,'SEDC',"SEDCSUR");

                      if($cr_total == $dr_total)
                            $this->update_accttxnoth_party_status($sale_voucher_id,'SEDC',"SEDCEXE");
                        }
                        else
                        {
                                $this->update_accttxnoth_party_status($sale_voucher_id,'SEDC',"SEDCDUE");
                        }
                }
                if($vch_subtype_id == 22 && $debitnote_voucher_id){// against debit note
                    $voucher_tag = $voucher_info['voucher_tag'];
                    $purchase_voucher_id = $this->get_crsref($debitnote_voucher_id, 'PURCINV');

                    if(str_contains($voucher_tag, 'PESI'))
                    {
                        $summary = $this->get_party_oth_summary($purchase_voucher_id,'PESI');
                        $cr_total = $summary['cr_total'];
                        $dr_total = $summary['dr_total'];

                        if($cr_total > $dr_total)  
                            $this->update_accttxnoth_party_status($purchase_voucher_id,'PESI',"PESIDEF");
                      
                        if($cr_total < $dr_total)
                            $this->update_accttxnoth_party_status($purchase_voucher_id,'PESI',"PESISUR");

                        if($cr_total == $dr_total)
                            $this->update_accttxnoth_party_status($purchase_voucher_id,'PESI',"PESIEXE");
                    }
                    if(str_contains($voucher_tag, 'ICEP'))
                    {
                        $inward_challan_id = $this->get_crsref($purchase_voucher_id, 'INWDCHAL');

                        $summary = $this->get_party_oth_summary($inward_challan_id,'ICEP');
                        $cr_total = $summary['cr_total'];
                        $dr_total = $summary['dr_total'];

                        if($cr_total > $dr_total)  
                            $this->update_accttxnoth_party_status($inward_challan_id,'ICEP',"ICEPDEF");
                      
                        if($cr_total < $dr_total)
                            $this->update_accttxnoth_party_status($inward_challan_id,'ICEP',"ICEPSUR");

                        if($cr_total == $dr_total)
                            $this->update_accttxnoth_party_status($inward_challan_id,'ICEP',"ICEPEXE");
                    }
                    $insert_data = [
                                'acct_crs_id_type'      => 3,
                                'comp_id'                           => $this->company_id,
                                'txn_id'                            => '',
                                'voucher_txn_id'            => $debitnote_voucher_id,
                                'bo_id'                             => 0,
                                'acc_cross_ref_type'    => 'PESODUE',
                                'acc_cross_ref_data'    => $purchase_voucher_id,
                                'acc_cross_logdate'   => date('Y-m-d'),
                        ];
                    $this->add_acc_crsref_data($insert_data);
                } 
            }   
        }

        if($voucher_type_id == '6') //Material In
        {
            $vch_subtype_id = $voucher_info['vch_subtype_id'];

            $flag = 1;
            if($vch_subtype_id == 11){// check purchase
                $check = $this->check_crsref($voucher_txn_id, 'INWCHAL');
                if($check){
                    $status = false;
                    $message = 'Inward Challan No. '.$voucher_info['comp_vch_no'].' has linked Purchase Voucher';
                    $flag = 0;
                }
            }

            if($vch_subtype_id == 12){// against against purchase
                $purchase_voucher_id = $this->get_crsref($voucher_txn_id, 'PURCINV');
            }
            if($vch_subtype_id == 21){// against credit note
                $creditnote_voucher_id = $this->get_crsref($voucher_txn_id, 'CRNTINV');
            }

            if($flag)
            {
                $status = true;
                $data = $this->get_comp_txn_data($voucher_txn_id);
                foreach ($data as $key => $value) {
                        if($value['master_id_type'] == 'acc'){
                                $this->delete_acc_txn_data($value['master_id'],$voucher_txn_id);
                        }
                        if($value['master_id_type'] == 'itm'){
                                
                                $this->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
                        }
                        if($value['master_id_type'] == 'bsd'){
                                $this->delete_sundry_txn_data($value['master_id'],$voucher_txn_id);
                        }
                }
				$this->delete_memo_txn_data($voucher_txn_id);
				$this->delete_gstroutsup_data($voucher_txn_id);
				$this->delete_acctgstsum_data($voucher_txn_id);
				$this->delete_acc_crsref_data_by_tag($voucher_txn_id,'FOREXRT');
				$this->delete_taxinc_txn_data($voucher_txn_id,'acc');
				
                $this->delete_acc_oth_data($voucher_txn_id);
                $this->delete_acc_crsref_data($voucher_txn_id);
                $this->delete_itm_oth_data($voucher_txn_id);
                $this->delete_comp_txn_data($voucher_txn_id);
                $this->delete_voucher_conso_data($voucher_txn_id);
                $this->delete_all_narrations($voucher_txn_id);

                if($vch_subtype_id == 12 && $purchase_voucher_id){// against purchase
                    $voucher_tag = $voucher_info['voucher_tag'];
                    $count = $this->count_vouchers_by_tag($voucher_type_id,$voucher_tag);
                    if($count > 0)
                    {
                        
                      $summary = $this->get_party_oth_summary($purchase_voucher_id,'PESI');
                        $cr_total = $summary['cr_total'];
                        $dr_total = $summary['dr_total'];

                        if($cr_total > $dr_total)  
                            $this->update_accttxnoth_party_status($purchase_voucher_id,'PESI',"PESIDEF");
                      
                        if($cr_total < $dr_total)
                            $this->update_accttxnoth_party_status($purchase_voucher_id,'PESI',"PESISUR");

                        if($cr_total == $dr_total)
                            $this->update_accttxnoth_party_status($purchase_voucher_id,'PESI',"PESIEXE");
                    }
                    else
                    {
                        $this->update_accttxnoth_party_status($purchase_voucher_id,'PESI',"PESIDUE");
                    }
                }

                if($vch_subtype_id == 21 && $creditnote_voucher_id){// against credit note
                    $voucher_tag = $voucher_info['voucher_tag'];
                    $sale_voucher_id = $this->get_crsref($creditnote_voucher_id, 'SALEINV');
                    
                     if(str_contains($voucher_tag, 'SEDC'))
                    {
                            $summary = $this->get_party_oth_summary($sale_voucher_id,'SEDC');
                        $cr_total = $summary['cr_total'];
                        $dr_total = $summary['dr_total'];

                        if($cr_total > $dr_total)  
                            $this->update_accttxnoth_party_status($sale_voucher_id,'SEDC',"SEDCDEF");
                      
                        if($cr_total < $dr_total)
                            $this->update_accttxnoth_party_status($sale_voucher_id,'SEDC',"SEDCSUR");

                        if($cr_total == $dr_total)
                            $this->update_accttxnoth_party_status($sale_voucher_id,'SEDC',"SEDCEXE");
                    }
                    if(str_contains($voucher_tag, 'DCES'))
                    {
                        $delivery_challan_id = $this->get_crsref($sale_voucher_id, 'DELCHAL');

                        $summary = $this->get_party_oth_summary($delivery_challan_id,'DCES');
                        $cr_total = $summary['cr_total'];
                        $dr_total = $summary['dr_total'];

                        if($cr_total > $dr_total)  
                            $this->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESDEF");
                      
                        if($cr_total < $dr_total)
                            $this->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESSUR");

                        if($cr_total == $dr_total)
                            $this->update_accttxnoth_party_status($delivery_challan_id,'DCES',"DCESEXE");
                    }
                    $insert_data = [
                                'acct_crs_id_type'      => 2,
                                'comp_id'                           => $this->company_id,
                                'txn_id'                            => '',
                                'voucher_txn_id'            => $creditnote_voucher_id,
                                'bo_id'                             => 0,
                                'acc_cross_ref_type'    => 'SESIDUE',
                                'acc_cross_ref_data'    => $sale_voucher_id,
                                'acc_cross_logdate'   => date('Y-m-d'),
                        ];
                        $this->add_acc_crsref_data($insert_data);
                }
            }   
        }

        //Sales Order, Purchase Order, Quotation, Purchase Requisition
        if($voucher_type_id == '19' || $voucher_type_id == '12' || $voucher_type_id == '17' || $voucher_type_id == '21') 
        {
            $status = true;
            
			$this->delete_memo_txn_data($voucher_txn_id);
				$this->delete_gstroutsup_data($voucher_txn_id);
				$this->delete_acctgstsum_data($voucher_txn_id);
				$this->delete_acc_crsref_data_by_tag($voucher_txn_id,'FOREXRT');
				$this->delete_taxinc_txn_data($voucher_txn_id,'acc');
				
             $this->delete_acc_oth_data($voucher_txn_id);
             $this->delete_acc_crsref_data($voucher_txn_id);
             $this->delete_itm_oth_data($voucher_txn_id);
             $this->delete_comp_txn_data($voucher_txn_id);
             $this->delete_voucher_conso_data($voucher_txn_id);
             $this->delete_all_narrations($voucher_txn_id);   
        }

        if($voucher_type_id == '9' || $voucher_type_id == '13' || $voucher_type_id == '1' || $voucher_type_id == '5') //payment, receipt, contra, journal
        {
            
            $status = true;
            $data = $this->get_comp_txn_data($voucher_txn_id);
            foreach ($data as $key => $value) {
                if($value['master_id_type'] == 'acc'){
                    $this->delete_acc_txn_data($value['master_id'],$voucher_txn_id);
                }
                if($value['master_id_type'] == 'itm'){
                    
                    $this->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
                }
                if($value['master_id_type'] == 'bsd'){
                    $this->delete_sundry_txn_data($value['master_id'],$voucher_txn_id);
                }
            }
				$this->delete_memo_txn_data($voucher_txn_id);
				$this->delete_gstroutsup_data($voucher_txn_id);
				$this->delete_acctgstsum_data($voucher_txn_id);
				$this->delete_acc_crsref_data_by_tag($voucher_txn_id,'FOREXRT');
				$this->delete_taxinc_txn_data($voucher_txn_id,'acc');
				
                $this->delete_comp_txn_data($voucher_txn_id);
                $this->delete_voucher_conso_data($voucher_txn_id);

                $this->delete_cc_txn($voucher_txn_id);
                $this->delete_bills_txn($voucher_txn_id);                 
                $this->delete_all_narrations($voucher_txn_id);    
        }

        if($voucher_type_id == '15') //stock transfer
        {
            $status = true;
            $data = $this->get_comp_txn_data($voucher_txn_id);
            foreach ($data as $key => $value) {
                    if($value['master_id_type'] == 'itm'){
                            
                            $this->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
                    }
            }
			$this->delete_memo_txn_data($voucher_txn_id);
				$this->delete_gstroutsup_data($voucher_txn_id);
				$this->delete_acctgstsum_data($voucher_txn_id);
				$this->delete_acc_crsref_data_by_tag($voucher_txn_id,'FOREXRT');
				$this->delete_taxinc_txn_data($voucher_txn_id,'acc');
				
            $this->delete_acc_oth_data($voucher_txn_id);
            $this->delete_acc_crsref_data($voucher_txn_id);
            $this->delete_itm_oth_data($voucher_txn_id);
            $this->delete_comp_txn_data($voucher_txn_id);
            $this->delete_voucher_conso_data($voucher_txn_id);
            $this->delete_all_narrations($voucher_txn_id);    
        }

        if($voucher_type_id == '10') //physical verification
        { 
            $status = true;
            $data = $this->get_comp_txn_data($voucher_txn_id);
            foreach ($data as $key => $value) {
                if($value['master_id_type'] == 'itm'){
  
                    $this->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
                }
            }
			
			$this->delete_memo_txn_data($voucher_txn_id);
				$this->delete_gstroutsup_data($voucher_txn_id);
				$this->delete_acctgstsum_data($voucher_txn_id);
				$this->delete_acc_crsref_data_by_tag($voucher_txn_id,'FOREXRT');
				$this->delete_taxinc_txn_data($voucher_txn_id,'acc');
				
            $this->delete_acc_oth_data($voucher_txn_id);
            $this->delete_acc_crsref_data($voucher_txn_id);
            $this->delete_itm_oth_data($voucher_txn_id);
            $this->delete_comp_txn_data($voucher_txn_id);
            $this->delete_voucher_conso_data($voucher_txn_id);
            $this->delete_all_narrations($voucher_txn_id);    
        }

        if($voucher_type_id == '14') //production
        { 
            $status = true;
            $data = $this->get_comp_txn_data($voucher_txn_id);
            foreach ($data as $key => $value) {
                if($value['master_id_type'] == 'acc'){
                    $this->delete_acc_txn_data($value['master_id'],$voucher_txn_id);
                }
                if($value['master_id_type'] == 'itm'){
                    
                    $this->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
                }
                if($value['master_id_type'] == 'bsd'){
                    $this->delete_sundry_txn_data($value['master_id'],$voucher_txn_id);
                }
            }

            $this->delete_comp_txn_data($voucher_txn_id);
            $this->delete_voucher_conso_data($voucher_txn_id);

			$this->delete_memo_txn_data($voucher_txn_id);
				$this->delete_gstroutsup_data($voucher_txn_id);
				$this->delete_acctgstsum_data($voucher_txn_id);
				$this->delete_acc_crsref_data_by_tag($voucher_txn_id,'FOREXRT');
				$this->delete_taxinc_txn_data($voucher_txn_id,'acc');
				
            $this->delete_cc_txn($voucher_txn_id);
            $this->delete_bills_txn($voucher_txn_id);                 
            $this->delete_all_narrations($voucher_txn_id);   
        }

        if($voucher_type_id == '20') //stock journal
        {
            $status = true;
            $data = $this->get_comp_txn_data($voucher_txn_id);
            foreach ($data as $key => $value) {
                if($value['master_id_type'] == 'itm'){
                        
                    $this->delete_itm_txn_data($value['master_id'],$voucher_txn_id);
                }
            }
			$this->delete_memo_txn_data($voucher_txn_id);
				$this->delete_gstroutsup_data($voucher_txn_id);
				$this->delete_acctgstsum_data($voucher_txn_id);
				$this->delete_acc_crsref_data_by_tag($voucher_txn_id,'FOREXRT');
				$this->delete_taxinc_txn_data($voucher_txn_id,'acc');
				
            $this->delete_acc_oth_data($voucher_txn_id);
            $this->delete_acc_crsref_data($voucher_txn_id);
            $this->delete_itm_oth_data($voucher_txn_id);
            $this->delete_comp_txn_data($voucher_txn_id);
            $this->delete_voucher_conso_data($voucher_txn_id);
            $this->delete_all_narrations($voucher_txn_id);  
        }
        if($voucher_type_id == '4') //consignment packing
        {
            $status = false;
            $message = 'Consignement Packing deletetion not created yet';
        }

       
	 }
		$message = 'Voucher Deleted';
      
  		return ['status' => true, 'message' => $message]; 
  		
  	}

  	function get_acc_bsd_list()
  	{
  		$acc_file_name    = 'acc'.$this->session->get('ses_comp_fy_id').'.json';
    	$bsd_file_name    = 'bsd'.$this->session->get('ses_comp_fy_id').'.json';

  		$accounts_list = json_decode(file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$acc_file_name),true);
    	$bsd_accounts  = json_decode(file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$bsd_file_name),true);

    	if($accounts_list!='' &&  $bsd_accounts!='')
        	$list=array_merge($accounts_list,$bsd_accounts);
	   	else
		   	$list  = $accounts_list;

			return $list;
  	}

  	function get_itm_list()
  	{
  		$itm_file_name    = 'item'.$this->session->get('ses_comp_fy_id').'.json';

  		$item_list = json_decode(file_get_contents(WRITEPATH.'comp'.$this->company_id.'/'.$itm_file_name),true);

		return $item_list;
  	}	
  function add_replica_banking_vouchers($comptxn,$vchtype,$voucher_txn_id,$voucher_date,$bo_id,$vchno){
	if($comptxn){ 
		$comptxn_data = json_decode($comptxn,true);
	foreach($comptxn_data as $value){
		  $voucher_series     = $value['comp_vch_series_id'];
		  $master_id_type     = $value['master_id_type'];
	    if($value['master_id_type'] == 'acc'){
			$bill_ref_id    = $this->createUndefinedBillRefId($value['master_id']);
	        $billstxnnn_tbl = $this->company_id.'_billstxnnn_'.$this->session->get('ses_comp_fy_id');
       
            $insert_data = [
              "comp_id"             => $this->company_id,
              "comp_vch_series_id"  => $voucher_series,
              "voucher_txn_id"      => $voucher_txn_id,
              "master_id"           => $value['master_id'],
              'master_id_type'      => 'acc'
             ];
            $txn_id     = $this->add_comp_txn_data($insert_data);
			/****************   Forex Rates START    ****************/
			$acc_cross_ref_data = $this->get_crsref($value['voucher_txn_id'],'FOREXRT');
			if($acc_cross_ref_data!=''){			
			 	 $insert_data = [
					'acct_crs_id_type'		=> '',
					'acct_txn_id'           => $txn_id,
					'comp_id'				=> $this->company_id,
					'txn_id'				=> $txn_id,
					'voucher_txn_id'		=> $voucher_txn_id,
					'bo_id'					=> $bo_id,
					'acc_cross_ref_type'	=> 'FOREXRT',
					'acc_cross_ref_data'	=> $acc_cross_ref_data,
					'acc_cross_logdate'   	=> $voucher_date,
				    ];					
				 $this->add_acc_crsref_data($insert_data);				 
			 		
			}
			// account txn data 
			$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
		    $acc_result  = $this->db->table($acc_txn_tbl)
		                    ->where('acc_id',$value['master_id'])
							->where('bo_id',$bo_id)
							->where('voucher_type_id',$vchtype)
        					->orderBy('acc_txn_date','desc')
        					->orderBy('acc_txn_id','desc')
        					->get()->getResultArray();
			if($acc_result){
			  foreach($acc_result as $acrow){
				 $insert_data  = [
					  'comp_id'            => $this->company_id,
					  'acc_id'             => $acrow['acc_id'],
					  'acc_txn_date'       => $voucher_date,
					  'acc_txn_amount'     => $acrow['acc_txn_amount'],                        
					  'acc_txn_drcr'       => $acrow['acc_txn_drcr'],
					  'comp_vch_series_no' => $vchno,
					  'posted_on'          => date('Y-m-d H:i:s'),                        
					  'voucher_txn_id'     => $voucher_txn_id,
					  'voucher_type_id'    => $vchtype,
					  'txn_id'             => $txn_id,
					  'acc_bal'            => 0
					];  
				$bill_txn_data[$bill_ref_id] = [
                    'bills_ref_id'       => $bill_ref_id,
                    'comp_id'            => $this->company_id,
                    'acc_id'             => $value['master_id'],
                    'voucher_txn_id'     => $voucher_txn_id,
                    'voucher_type_id'    => $vchtype,
                    'comp_vch_series_id' => $voucher_series,
                    'bills_txn_date'     => $voucher_date,
                    'bills_txn_drcr'     => $acrow['acc_txn_drcr'],
                    'bills_txn_amt'      => $acrow['acc_txn_amount'],
                    'bills_txn_bal'      => 0,
                    'bills_txn_narr'     => '',
                  ];	
            $this->add_acc_txn_data($insert_data);
            $this->update_account_balance($value['master_id']);
			
			
			$bill_txn = $this->db->table($billstxnnn_tbl)
						->where('bills_ref_id', $bill_ref_id)
						->where('acc_id',$value['master_id'])
						->countAllResults();
			 if($bill_txn==0){
			   /******  Bill By Bill ************/
		       $this->add_bill_txn($bill_txn_data[$bill_ref_id]);	
			  }			
			    }	
			}				
		 	 
		/******  Cost Centre ************/	 
		$cc_txn_tbl = $this->company_id.'_costcttxnn_'.$this->session->get('ses_comp_fy_id');
        $this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
		$cc_result  = $this->db->table($cc_txn_tbl)
				        ->where('voucher_txn_id', $value['voucher_txn_id'])
						->where('cc_id !=', 1)
						->where('bo_id', $bo_id)
						->where('voucher_type_id',$vchtype)
						->groupBy('acc_id')
						->groupBy('acc_type')
						->orderBy('acc_id')
						->orderBy('acc_type')
						->get()->getResultArray();
		if($cc_result){
			foreach($cc_result as $ccrow){
				$cc_txn_data = [
                  'cc_id'              => 1,
                  'comp_id'            => $this->company_id,
                  'acc_id'             => $value['master_id'],
                  'acc_type'           => 'acc',
                  'voucher_txn_id'     => $voucher_txn_id,
                  'voucher_type_id'    => $vchtype,
                  'comp_vch_series_id' => $voucher_series,
                  'cc_txn_date'        => $voucher_date,
                  'cc_txn_drcr'        => $ccrow['cc_txn_drcr'],
                  'cc_txn_amt'         => $ccrow['cc_txn_amt'],
                  'cc_txn_bal'         => 0,
                  'cc_txn_narr'        => '',
                ];
                $this->add_cc_txn($cc_txn_data);
			 }			
		   }				
			/******  Project Reporting ************/	 
		$projasttxn_tbl = $this->company_id.'_projasttxn_'.$this->session->get('ses_comp_fy_id');
	   	$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
		$proj_result = $this->db->table($projasttxn_tbl)
				        ->where('voucher_txn_id', $value['voucher_txn_id'])
						->where('bo_id', $bo_id)
						->where('voucher_type_id',$vchtype)
						->where('project_id !=', 1)
	   					->groupBy('acc_id')
	   					->groupBy('acc_type')
						->get()->getResultArray();	
        if($proj_result){
			foreach($proj_result as $prctrow){
				$proj_txn_data = [
					'project_id'         => 0,
					'comp_id'            => $this->company_id,
					'acc_id'             => $value['master_id'],
					'acc_type'           => 'acc',
					'voucher_txn_id'     => $voucher_txn_id,
					'voucher_type_id'    => $vchtype,
					'comp_vch_series_id' => $voucher_series,
					'proj_txn_date'      => $voucher_date,
					'proj_txn_drcr'      => $prctrow['proj_txn_drcr'],
					'proj_txn_amt'       => $prctrow['proj_txn_amt'],
					'proj_txn_bal'       => 0,
					'proj_txn_narr'      => '',
				  ];
              $this->add_pr_txn_data($proj_txn_data);
			}
		}
	}
	if($value['master_id_type'] == 'bsd'){
        $txn_data = array(
              "comp_id"               => $this->company_id,
              "comp_vch_series_id"    => $voucher_series,
              "voucher_txn_id"        => $voucher_txn_id,
              "master_id"             => $value['master_id'],
              'master_id_type'        => 'bsd'
            );
       $txn_id  = $this->TransactionModel->add_comp_txn_data($txn_data);
	   $acc_cross_ref_data = $this->get_crsref($value['voucher_txn_id'],'FOREXRT');
	   /****************   Forex Rates START    ****************/
	   if($acc_cross_ref_data!=''){			
			 	 $insert_data = [
					'acct_crs_id_type'		=> '',
					'acct_txn_id'           => $txn_id,
					'comp_id'				=> $this->company_id,
					'txn_id'				=> $txn_id,
					'voucher_txn_id'		=> $voucher_txn_id,
					'bo_id'					=> $bo_id,
					'acc_cross_ref_type'	=> 'FOREXRT',
					'acc_cross_ref_data'	=> $acc_cross_ref_data,
					'acc_cross_logdate'   	=> $voucher_date,
				    ];					
				 $this->add_acc_crsref_data($insert_data);
			}
		// account txn data 
			$bsd_txn_tbl = $this->company_id.'_sundrytxnn_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
		    $bsd_result  = $this->db->table($acc_txn_tbl)
		                    ->where('bill_sundry_id',$value['master_id'])
							->where('bo_id',$bo_id)
							->where('voucher_type_id',$vchtype)
        					->orderBy('	sundry_txn_date','desc')
        					->orderBy('sundry_txn_id','desc')
        					->get()->getResultArray();
			if($bsd_result){	
				foreach($bsd_result as $bsdrow){
					$insert_data  = array(
					  "comp_id"                   => $this->company_id,
					  "sundry_txn_date"           => $voucher_date,
					  "sundry_txn_amount"         => $bsdrow['sundry_txn_amount'],
					  "sundry_txn_drcr"           => $bsdrow['sundry_txn_drcr'],
					  "comp_vch_name"             => $vchno, // ?
					  "comp_vch_series_no"        => $vchno,
					  "bill_sundry_id"            => $value['master_id'],
					  "sundry_txn_narr"           => '',
					  "sundry_bal"                => 0,
					  "voucher_txn_id"            => $voucher_txn_id, 
					  "voucher_type_id"           => $vchtype,
					  'txn_id'                    => $txn_id,
					  'sundry_tag_rate'           => ''
					);
					$this->TransactionModel->add_sundry_txn_data($insert_data);	
					}
				}
		/******  Cost Centre ************/	 
		$cc_txn_tbl = $this->company_id.'_costcttxnn_'.$this->session->get('ses_comp_fy_id');
        $this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
	    $cc_result = $this->db->table($cc_txn_tbl)
				        ->where('voucher_txn_id', $value['voucher_txn_id'])
						->where('cc_id !=', 1)
						->where('bo_id', $bo_id)
						->where('voucher_type_id',$vchtype)
						->groupBy('acc_id')
						->groupBy('acc_type')
						->orderBy('acc_id')
						->orderBy('acc_type')
						->get()->getResultArray();
		if($cc_result){
			foreach($cc_result as $ccrow){
				$cc_txn_data = [
                  'cc_id'              => 1,
                  'comp_id'            => $this->company_id,
                  'acc_id'             => $value['master_id'],
                  'acc_type'           => 'bsd',
                  'voucher_txn_id'     => $voucher_txn_id,
                  'voucher_type_id'    => $vchtype,
                  'comp_vch_series_id' => $voucher_series,
                  'cc_txn_date'        => $voucher_date,
                  'cc_txn_drcr'        => $ccrow['cc_txn_drcr'],
                  'cc_txn_amt'         => $ccrow['cc_txn_amt'],
                  'cc_txn_bal'         => 0,
                  'cc_txn_narr'        => '',
                ];
                $this->add_cc_txn($cc_txn_data);
			 }			
		   }	
		/******  Project Reporting ************/	 
		$projasttxn_tbl = $this->company_id.'_projasttxn_'.$this->session->get('ses_comp_fy_id');
	   	$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
	    $proj_result = $this->db->table($projasttxn_tbl)
				        ->where('voucher_txn_id', $value['voucher_txn_id'])
						->where('bo_id', $bo_id)
						->where('voucher_type_id',$vchtype)
						->where('project_id !=', 1)
	   					->groupBy('acc_id')
	   					->groupBy('acc_type')
						->get()->getResultArray();	
        if($proj_result){
			foreach($proj_result as $prctrow){
				$proj_txn_data = [
					'project_id'         => 0,
					'comp_id'            => $this->company_id,
					'acc_id'             => $value['master_id'],
					'acc_type'           => 'bsd',
					'voucher_txn_id'     => $voucher_txn_id,
					'voucher_type_id'    => $vchtype,
					'comp_vch_series_id' => $voucher_series,
					'proj_txn_date'      => $voucher_date,
					'proj_txn_drcr'      => $prctrow['proj_txn_drcr'],
					'proj_txn_amt'       => $prctrow['proj_txn_amt'],
					'proj_txn_bal'       => 0,
					'proj_txn_narr'      => '',
				  ];
              $this->add_pr_txn_data($proj_txn_data);
			}
		}	
		   
	}		
	if($value['master_id_type'] == 'nrr'){
		   $insert_data = [
			"comp_id"             => $this->company_id,
			"comp_vch_series_id"  => $voucher_series,
			"voucher_txn_id"      => $voucher_txn_id,
			"master_id"           => 0,
			'master_id_type'      => 'nrr'
			];
            $txn_id = $this->add_comp_txn_data($insert_data);
			$long_narration = $this->get_voucher_narration_info($value['voucher_txn_id'],'long',$value['txn_id']);
            if($long_narration){
				$txn_narr=$long_narration['vch_narr'];
			}else
				$txn_narr='';
			$this->save_voucher_narration($voucher_txn_id,$txn_id,'long',$txn_narr);
	    }
		  
	  }			
	} 
  }
  
  function get_branch_gstin_info($bo_id){
		$tbl_name     = $this->session->get('ses_company_id').'_gstinmastr_'.$this->session->get('ses_comp_fy_id');
		$comp_fy_id   = $this->session->get('ses_company_code');
		$result         = $this->db->table($tbl_name)->where('bo_id',$bo_id)->get()->getRowArray(); 
		return $result; 
  }
  function get_credntial_gstin_info($site_id=''){
	    $boid = $this->session->get('ses_boid');
		$tbl_name     = 'credential';
        $uuid         = $this->uuid;	
        $comp_id      = $this->session->get('ses_company_id');	
		if($site_id!=''){
		$result         = $this->uuid_db->table($tbl_name)->where('comp_id',$comp_id)->where('uuid',$uuid)->where('cred_site',$site_id)->get()->getRowArray(); 
		
		return $result; 
		}
		return false;
  }
	
  function get_tax_list($voucher_type='')
  { 
  	$tax = [
  		'CGST' => [
	  			'id' => 0,
	  			'name' => '',
	  			'nature' => 33,
	  			'status' => 0,
	  			'value'	=> 0,
	  			'error' => '',
	  		],
  		'SGST' => [
	  			'id' => 0,
	  			'name' => '',
	  			'nature' => 34,
	  			'status' => 0,
	  			'value'	=> 0,
	  			'error' => '',
	  		],
  		'IGST' => [
	  			'id' => 0,
	  			'name' => '',
	  			'nature' => 35,
	  			'status' => 0,
	  			'value'	=> 0,
	  			'error' => '',
	  		],
  		'CESS' => [
	  			'id' => 0,
	  			'name' => '',
	  			'nature' => 36,
	  			'status' => 0,
	  			'value'	=> 0,
	  			'error' => '',
	  		],
  		'TCS-IT' => [
	  			'id' => 0,
	  			'name' => '',
	  			'nature' => 37,
	  			'status' => 0,
	  			'value'	=> 0,
	  			'error' => '',
	  		],
  		'TDS-IT' => [
	  			'id' => 0,
	  			'name' => '',
	  			'nature' => 38,
	  			'status' => 0,
	  			'value'	=> 0,
	  			'error' => '',
	  		],
  		'TCS-GST' => [
	  			'id' => 0,
	  			'name' => '',
	  			'nature' => 39,
	  			'status' => 0,
	  			'value'	=> 0,
	  			'error' => '',
	  		],
  		'TDS-GST' => [
	  			'id' => 0,
	  			'name' => '',
	  			'nature' => 40,
	  			'status' => 0,
	  			'value'	=> 0,
	  			'error' => '',
	  		],
  		'UT-TAX' => [
	  			'id' => 0,
	  			'name' => '',
	  			'nature' => 195,
	  			'status' => 0,
	  			'value'	=> 0,
	  			'error' => '',
	  		],
  	];

  	$billsundry_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
  	$bdstaxmstn_tbl = $this->company_id.'_bdstaxmstn_'.$this->session->get('ses_comp_fy_id');

  	foreach ($tax as $key => $value) {
  		if($voucher_type=='purchase'){
			$bill_input_output=[2,0];
		}else
			$bill_input_output=[2,1];
  		$result = $this->db->table($billsundry_tbl)
  								->select($billsundry_tbl.'.*')
									->join($bdstaxmstn_tbl, $bdstaxmstn_tbl.'.bill_sundry_id = '.$billsundry_tbl.'.bill_sundry_id')
									->where('sundry_type', 1)
									->where('sundry_nature', $value['nature'])
									->whereIn('bill_input_output', $bill_input_output)
									->orderBy('bill_input_output', 'desc')
									->limit(1)
									->get()->getRowArray();
			if($result){
				$tax[$key]['id'] = $result['bill_sundry_id'];
				$tax[$key]['name'] = $result['bill_sundry_name'];
				
			}
			unset($tax[$key]['nature']);
  	}

  	return $tax;
  }
}