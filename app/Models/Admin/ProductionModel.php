<?php
namespace App\Models\Admin;

use CodeIgniter\Model;
use App\Models\Admin\TransactionModel;
use App\Libraries\externaldb;
use App\Libraries\enc_string;

class ProductionModel extends Model	{

    public function __construct() {
        parent::__construct();        
       $this->externaldb    = new externaldb();		
	   $this->TransactionModel  = new TransactionModel();
	   $this->db            = $this->externaldb->get_company_db();
	   $this->session       = \Config\Services::session();
	   $this->company_id    = $this->session->get('ses_company_id');
	   $this->enc_string    = new enc_string();
	   $this->bo_id            = $this->session->get('ses_boid');
    }
  
  
   public function update_all_entries($table_name,$update_data,$txn_id,$comp_id){
       $this->db->table($table_name)->where('txn_id',$txn_id)->where('comp_id',$comp_id)->update($update_data);	
   }
    public function update_voucher_cons_data($data,$voucher_txn_id,$comp_id){
		 $comp_vch_cons_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
         $this->db->table($comp_vch_cons_tbl)->where('comp_id',$comp_id)->where('voucher_txn_id',$voucher_txn_id)->update($data);

	 
	 }
 
  public function get_itemstable_rownums($tblname){
        $builder = $this->db->table($tblname);
        $builder->select('*,ROW_NUMBER() OVER (ORDER BY item_txn_date) row_num');
        $result = $builder->get()->getResultArray();
        return $result;
   }
 
  function bom_info($bom_id){
       $billofmatn_tbl =  $this->company_id.'_billofmatn_'.$this->session->get('ses_comp_fy_id');	 
	   return  $this->db->table($billofmatn_tbl)->where('bom_id', $bom_id)->get()->getRowArray();  
         
   }
  
  function delete_comp_txn($voucher_txn_id)
    {
        $comptxnmst_master = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
        $this->db->table($comptxnmst_master)->where('voucher_txn_id', $voucher_txn_id)->delete();
    }
  
  public function delete_itemtxnnnn($voucher_txn_id,$item_id)
    {
        $itemtxnnnn_tabl = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
        $this->db->table($itemtxnnnn_tabl)->where('item_id', $item_id)->where('voucher_txn_id', $voucher_txn_id)->delete();
    }

 public  function delete_accttxnoth($voucher_txn_id)
    {
        $acctcrsref_tabl = $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
        $this->db->table($acctcrsref_tabl)->where('voucher_txn_id', $voucher_txn_id)->delete();
    }
	
  public function ajax_vouchers_transactions_list($voucher_txn_id){ 
	    $comp_id          = $this->session->get('ses_company_id');
	    $ses_comp_fy_id   = $this->session->get('ses_comp_fy_id');
		$comptxnmst_tbl   = $comp_id.'_comptxnmst_'.$ses_comp_fy_id;
		$builder          = $this->db->table($comptxnmst_tbl); 
        $builder->orderBy('txn_id');      
        $builder->where('voucher_txn_id', $voucher_txn_id);			
		$builder->where('comp_id', $comp_id);	
		$result = $builder->get()->getResultArray(); 
	
 	    return $result;
     }
  public function get_itemtxnnnn($txn_id,$voucher_txn_id,$item_id,$itemtremrks,$bom_id,$counter){
	  $itemtxnnnn_tabl = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
      $row =   $this->db->table($itemtxnnnn_tabl)
	                    ->where('txn_id', $txn_id)
	                    ->where('item_id', $item_id)
						->where('voucher_txn_id', $voucher_txn_id)->get()
						->getRowArray();  
	 if($row){
		$fcy_rate = 1;
 
	   	$voucher_info = $this->get_voucher_cons_info($voucher_txn_id,$this->company_id);
	   	if($voucher_info && $voucher_info['currency_id'] != 1){
	   		$fcy_rate = $this->TransactionModel->get_forex_rate_fcy($voucher_info['currency_id'],$voucher_info['voucher_date']);
	   	}
		
		 
		 
	 $unit_id         = $row['item_unit'];		 
		 
	  $item_info       = $this->get_item_info($item_id);	
	 
	  $item_name       = $item_info['item_name'];
	  $get_units_info  = $this->get_units_info($unit_id);
	  $unit_name       = $get_units_info['item_unit'];
	  $item_qty        = $row['item_txn_qty']*$counter;
	  $item_data       = array("item_unit_id"=>$unit_id,"id"=>$bom_id,"item_id"=>$item_id,"item_name"=>$item_name,'item_qty'=>$item_qty ,'item_unit'=>$unit_name,'item_price'=>parseAmount($fcy_rate*($row['item_txn_amount']/$item_qty)),'item_amount'=>parseAmount($fcy_rate*($row['item_txn_amount']*$item_qty)),'item_value'=>'');
	  $unit_labels     = array("label"=>$item_name,"value"=>$unit_name);	  
	  $data            = array('item_data'=>$item_data,'unit_labels'=>$unit_labels);		
				
	   return $data;
	 }    
	}	 
	
  public function get_bomaddcost($bom_id,$acc_id,$counter){
	 $comp_id          = $this->session->get('ses_company_id');
	 $ses_comp_fy_id   = $this->session->get('ses_comp_fy_id');
	 $bomaddcost_tbl   = $comp_id.'_bomaddcost_'.$ses_comp_fy_id;
  	 $builder          = $this->db->table($bomaddcost_tbl); 
	    
     $builder->where('bom_id', $bom_id);				
	 $result = $builder->get()->getRowArray();
     $expense_amount = $result['bom_addcost_amt'];
	 
	 if(strtolower($result['bom_addcost_type'])=='p')
		  $bom_addcost_type ='Percentage';
	  else if(strtolower($result['bom_addcost_type'])=='a')
		  $bom_addcost_type ='Absolute';
	  $expensetypes_labels= array("label"=>$bom_addcost_type,"value"=>$result['bom_addcost_type']);
				  
			
				  
	 $account_table_name = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
     $account_table      = $this->db->table($account_table_name)->where('acc_id', $result['bom_addcost_acc_id'])->get()->getRowArray();
	 $expense_name       = $account_table['acc_name'];
	 $final_additional_cost_data= array("id"=>'',"expense_name"=>$expense_name,'expense_type'=>$bom_addcost_type,'expense_amount'=>$expense_amount);
				
	 $data = array('item_data'=>$final_additional_cost_data,'unit_labels'=>$expensetypes_labels);	 
	  return $data;
  }	
	 
  public function saved_entries_production($bom_id,$voucher_txn_id,$counter){
	    $comp_id          = $this->session->get('ses_company_id');
	    $ses_comp_fy_id   = $this->session->get('ses_comp_fy_id');
		$comptxnmst_tbl   = $comp_id.'_comptxnmst_'.$ses_comp_fy_id;
		$builder          = $this->db->table($comptxnmst_tbl); 
        $builder->orderBy('txn_id');      
        $builder->where('voucher_txn_id', $voucher_txn_id);				
		$builder->where('comp_id', $comp_id);	
		$result = $builder->get()->getResultArray();
        
		
		$bom_info = $this->bom_info($bom_id);
		if($bom_info)
			$bom_name = $bom_info['bom_name'];
		else
			$bom_name = '';
	
		
        $final_item_consumed_data = array(); 		
		$final_item_produced_data = array(); 
		$final_byproducts_produced_data = array();


		$item_consumed_unit_labels = array(); 		
		$item_produced_unit_labels = array(); 
		$byproduct_produced_unit_labels = array();

		$final_additional_cost_data  = array();
		$expensetypes_labels         = array();
		if($result){
			foreach($result as $row){
				if($row['master_id_type']=='itm'){				
				    $item_consumed_data       = $this->get_itemtxnnnn($row['txn_id'],$voucher_txn_id,$row['master_id'],'CONSUME',$bom_id,$counter);
					$item_produced_data       = $this->get_itemtxnnnn($row['txn_id'],$voucher_txn_id,$row['master_id'],'FINALN',$bom_id,$counter);
					$byproducts_produced_data = $this->get_itemtxnnnn($row['txn_id'],$voucher_txn_id,$row['master_id'],'BYPRODN',$bom_id,$counter);					
					
				if(isset($item_consumed_data['item_data']))
					$final_item_consumed_data[]        =  $item_consumed_data['item_data']; 
				
				if(isset($item_produced_data['item_data']))
					$final_item_produced_data[]        =  $item_produced_data['item_data']; 
				
				if(isset($byproducts_produced_data['item_data']))
					$final_byproducts_produced_data[]  =  $byproducts_produced_data['item_data']; 					
				
			    if(isset($item_consumed_data['unit_labels']))				
					$item_consumed_unit_labels[]        =  $item_consumed_data['unit_labels']; 
			    if(isset($item_produced_data['unit_labels']))
					$item_produced_unit_labels[]        =  $item_produced_data['unit_labels']; 
			    if(isset($byproducts_produced_data['unit_labels']))	
					$byproduct_produced_unit_labels[]   =  $byproducts_produced_data['unit_labels']; 
				  }
				if($row['master_id_type']=='acc'){	
				   
				   $accttxnoth_data                    =  $this->get_bomaddcost($bom_id,$row['master_id'],$counter);
				   $final_additional_cost_data[]       =  $accttxnoth_data['item_data'];				   
				   $expensetypes_labels[]              =  $accttxnoth_data['unit_labels'];
				   
				}
				 			
			}
			
		}
	
	$final_array = array('bom_name'=>$bom_name,'item_consumed_unit_labels'=>$item_consumed_unit_labels,
		                     'item_produced_unit_labels'=> $item_produced_unit_labels,
							 'byproduct_produced_unit_labels' => $byproduct_produced_unit_labels,
		                     'item_consumed_data'=>$final_item_consumed_data,'item_produced_data'=>$final_item_produced_data,
							 'byproduct_produced_data'=>$final_byproducts_produced_data,
		                     'additional_cost_data'=>$final_additional_cost_data,'expensetypes_labels'=>$expensetypes_labels);
  // echo '<pre>';
  // print_r($final_array);
	return $final_array;			
  }
  
  
  function save_data($tbatch,$bom_id,$voucher_id,$voucher_txn_id,$matrcntr_id,$voucher_series,$production_date,$item_consumed,$item_produced,$byproducts_produced,$additional_cost){
		if($item_consumed){
			$comp_txn_master_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$acctcrsref_tbl      = $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
			
			// save bomid in  acctcrsref
			$txn_data = array("acct_txn_id"=>$voucher_txn_id,"comp_id"=>$this->company_id,"acct_crs_id_type"=>"","txn_id"=>$voucher_txn_id ,"voucher_txn_id"=>$voucher_txn_id,"acc_cross_ref_type"=>"bom_id",'acc_cross_ref_data'=>$bom_id,'acc_cross_logdate'=>date('Y-m-d'));
	        $this->db->table($acctcrsref_tbl)->insert($txn_data);	
						
			foreach($item_consumed as $item_row){
				if(isset($item_row['item_id'])){					
                   
				$unit_id  = $item_row['item_unit_id'];
		 		$txn_data = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$voucher_series,"voucher_txn_id"=>$voucher_txn_id,"master_id"=>$item_row['item_id'],'master_id_type'=>'itm');
	            $this->db->table($comp_txn_master_tbl)->insert($txn_data);	
                $txn_id   =  $this->db->insertID();
	            
				$item_txnn_tbl = $this->company_id.'_itemtxnnnn_'.$item_row['item_id'].'_'.$this->session->get('ses_comp_fy_id');	
				$item_data     = array(
						'comp_id'             => $this->company_id,
						'item_txn_date'       => $production_date,
						'item_txn_amount'     => $item_row['item_total_amount'],
						'item_txn_drcr'       => 'c',
						'item_txn_qty'        => $item_row['item_qty'],
                        'item_id'             => $item_row['item_id'],
						'voucher_txn_id'      => $voucher_txn_id,
                       	'voucher_type_id'     => $voucher_id,
                       	'mat_cent_id'         => $matrcntr_id,
						'txn_id'              => $txn_id,						
				        "item_unit"			  => $unit_id,
						"item_bal_qty"		  => 0,
						"item_avail"  		  => 1,
						"batch_id"  		  => 0,
					   );	
			   $this->TransactionModel->add_itm_txn_data($item_data);
				  
			    }
			}
		}
		
	if($item_produced){
			foreach($item_produced as $item_prd_row){
				if(isset($item_prd_row['item_id'])){
					
				$unit_id  = $item_prd_row['item_unit_id'];	
				$txn_data = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$voucher_series,"voucher_txn_id"=>$voucher_txn_id,"master_id"=>$item_prd_row['item_id'],'master_id_type'=>'itm');
	            $this->db->table($comp_txn_master_tbl)->insert($txn_data);	
                $txn_id =  $this->db->insertID();
			    
                // save bomid in  acctcrsref
				$txn_data = array("acct_txn_id"=>$txn_id,"comp_id"=>$this->company_id,"acct_crs_id_type"=>"","txn_id"=>$txn_id ,"voucher_txn_id"=>$voucher_txn_id,"acc_cross_ref_type"=>"bom_id",'acc_cross_ref_data'=>$bom_id,'acc_cross_logdate'=>date('Y-m-d'));
	            $this->db->table($acctcrsref_tbl)->insert($txn_data);	
								
					
				$item_txnn_tbl = $this->company_id.'_itemtxnnnn_'.$item_prd_row['item_id'].'_'.$this->session->get('ses_comp_fy_id');	
				$item_data  = array(
						'comp_id'             => $this->company_id,
						'item_txn_date'       => $production_date,
						'item_txn_amount'     => $item_prd_row['item_total_amount'],
						'item_txn_drcr'       => 'd',
						'item_txn_qty'        => $item_prd_row['item_qty'],
                        'item_id'             => $item_prd_row['item_id'],
						'voucher_txn_id'      => $voucher_txn_id,
                       	'voucher_type_id'     => $voucher_id,
                       	'mat_cent_id'         => $matrcntr_id,
						'txn_id'              => $txn_id,
					    "item_unit"			  => $unit_id,
						"item_bal_qty"		  => 0,
						"item_avail"  		  => 1,
						"batch_id"  		  => 0,
					   );	
			     $this->TransactionModel->add_itm_txn_data($item_data);
			 
			    }
			}
		}
		
	if($byproducts_produced){
			foreach($byproducts_produced as $by_prd_row){
				if(isset($by_prd_row['item_id'])){
				$unit_id  = $by_prd_row['item_unit_id'];	
				
				$txn_data = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$voucher_series,"voucher_txn_id"=>$voucher_txn_id,"master_id"=>$by_prd_row['item_id'],'master_id_type'=>'itm');
	            $this->db->table($comp_txn_master_tbl)->insert($txn_data);	
                $txn_id =  $this->db->insertID();
				
				// save bomid in  acctcrsref
				$txn_data = array("acct_txn_id"=>$txn_id,"comp_id"=>$this->company_id,"acct_crs_id_type"=>"","txn_id"=>$txn_id ,"voucher_txn_id"=>$voucher_txn_id,"acc_cross_ref_type"=>"bom_id",'acc_cross_ref_data'=>$bom_id,'acc_cross_logdate'=>date('Y-m-d'));
	            $this->db->table($acctcrsref_tbl)->insert($txn_data);	
				
				
				$item_txnn_tbl = $this->company_id.'_itemtxnnnn_'.$by_prd_row['item_id'].'_'.$this->session->get('ses_comp_fy_id');	
				$item_data  = array(
						'comp_id'             => $this->company_id,
						'item_txn_date'       => $production_date,
						'item_txn_amount'     => $by_prd_row['item_total_amount'],
						'item_txn_drcr'       => 'd',
						'item_txn_qty'        => $by_prd_row['item_qty'],
                        'item_id'             => $by_prd_row['item_id'],
						'voucher_txn_id'      => $voucher_txn_id,
                       	'voucher_type_id'     => $voucher_id,
                       	'mat_cent_id'         => $matrcntr_id,
						'txn_id'              => $txn_id,
					    "item_unit"			  => $unit_id,
						"item_bal_qty"		  => 0,
						"item_avail"  		  => 1,
						"batch_id"  		  => 0,
					   );	
			     $this->TransactionModel->add_itm_txn_data($item_data);		   
			    
			    }
			}
		}
		
     if($additional_cost){
			foreach($additional_cost as $additional_cost_row){
				
				$bomaddcost_tbl   = $this->company_id.'_bomaddcost_'.$this->session->get('ses_comp_fy_id');
				$builder_ncost    = $this->db->table($bomaddcost_tbl); 
				
				$builder_ncost->where('bom_id', $bom_id);				
				$bilcost_result = $builder_ncost->get()->getRowArray();
				$sav_bom_addcost_acc_id =  $bilcost_result['bom_addcost_acc_id'];
				
				if($sav_bom_addcost_acc_id){
				  $txn_data = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$voucher_series,"voucher_txn_id"=>$voucher_txn_id,"master_id"=>$sav_bom_addcost_acc_id,'master_id_type'=>'acc');
	              $this->db->table($comp_txn_master_tbl)->insert($txn_data);	
                  $txn_id =  $this->db->insertID();
				  // save bomid in  acctcrsref
				  $txn_data = array("acct_txn_id"=>$txn_id,"comp_id"=>$this->company_id,"acct_crs_id_type"=>"","txn_id"=>$txn_id ,"voucher_txn_id"=>$voucher_txn_id,"acc_cross_ref_type"=>"bom_id",'acc_cross_ref_data'=>$bom_id,'acc_cross_logdate'=>date('Y-m-d'));
	              $this->db->table($acctcrsref_tbl)->insert($txn_data);
									
				  $accttxnoth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');	
				  $item_data  = array(
						'comp_id'             => $this->company_id,
						'acc_oth_txn_date'    => $production_date,
						'acc_oth_txn_amount'  => $additional_cost_row['expense_amount'],
						'acc_oth_txn_drcr'    => 'd',
						// 'acc_oth_txn_narr'    => '',
						'voucher_type_id'     => $voucher_id,
                        'comp_vch_series_no'  => $voucher_series,
						'acc_id'              => $sav_bom_addcost_acc_id,
						'voucher_txn_id'      => $voucher_txn_id,
                        'acc_oth_txn_status'  => '',
						'bo_id'               => '0',
                       	'acc_oth_txn_duedate' => date('Y-m-d'),
						'acc_oth_txn_tag'     =>''						
					   );	
			   $this->db->table($accttxnoth_tbl)->insert($item_data);
		       }
			}
		}
		
	 }	
   
	 
   function billofmaterial_info($bom_id,$counter,$last_entry=''){	 
        $billofmatn_tble =  $this->company_id.'_billofmatn_'.$this->session->get('ses_comp_fy_id');
		
	    $bominputnn_tble =  $this->company_id.'_bominputnn_'.$this->session->get('ses_comp_fy_id');
		$bomoutputn_tble =  $this->company_id.'_bomoutputn_'.$this->session->get('ses_comp_fy_id');
		$bomaddcost_tble =  $this->company_id.'_bomaddcost_'.$this->session->get('ses_comp_fy_id');
		$itemrepbon      =  $this->company_id.'_itemrepbon_'.$this->session->get('ses_comp_fy_id');
		
		$bom_data = $this->db->table($billofmatn_tble)->where('bom_id', $bom_id)->get()->getRowArray(); 
		$bom_name  = $bom_data['bom_name'];
		$bom_consm_pricing  = $bom_data['bom_consm_pricing'];
		
		
	    $item_consumed_data = $this->db->table($bominputnn_tble)->where('bom_id', $bom_id)->get()->getResultArray(); 
		$item_produced_data = $this->db->table($bomoutputn_tble)->where('bom_id', $bom_id)->where('bom_output_type','f')->get()->getResultArray(); 
		$byproduct_produced_data = $this->db->table($bomoutputn_tble)->where('bom_id', $bom_id)->where('bom_output_type','p')->get()->getResultArray(); 
		$additional_cost_data   = $this->db->table($bomaddcost_tble)->where('bom_id', $bom_id)->get()->getResultArray(); 
		
		//item_consumed_data
		$final_item_consumed_data=array();
		$item_consumed_unit_labels=array();
		if($item_consumed_data){
			foreach($item_consumed_data as $row1){
				$item_info   = $this->get_item_info($row1['item_id']);	
				$unit_id     = $row1['item_uom'];	
				$item_name   = $item_info['item_name'];
				$container_id  = $row1['item_id'].'_'.$unit_id.'_1'; //itemid_unitid_avail(1)
				$default_method_id = $item_info['valmethod_id'];
				// Get container default valuation for that particular date
				if($bom_consm_pricing=='a'){
				$item_valuation_data = $this->db->table($itemrepbon)->select('item_value,item_bal_qty')->where('bo_id', $this->bo_id)->where('item_txn_date', date('Y-m-d',strtotime($last_entry)))->where('method_id', $default_method_id)->where('item_id_unit_id', $container_id)->get()->getRowArray(); 
				//echo $this->db->getlastquery();
				if(isset($item_valuation_data) && $item_valuation_data['item_bal_qty'] >0)
				  
			      $item_price  = parseAmountPrice($item_valuation_data['item_value']/$item_valuation_data['item_bal_qty'],4);
			     else 
				  $item_price = 0;

				 $item_amount = $row1['item_qty']*$item_price;	
				}else{
				  $item_price   = parseAmountPrice($row1['item_amt']/$row1['item_qty'],4);	
				  $item_amount  = parseAmount($row1['item_amt']);   
				}
				$get_units_info  = $this->get_units_info($unit_id);
				$unit_name       =  $get_units_info['item_unit'];
				$item_consumed_unit_labels[]= array("label"=>$item_name,"value"=>$unit_name);				
				$item_qty                   =  $row1['item_qty']*$counter;				
				$final_item_consumed_data[] = array("item_unit_id"=>$unit_id,"id"=>$row1['bom_id'],"item_id"=>$row1['item_id'],"item_name"=>$item_name,'item_qty'=>$item_qty,'item_unit'=>$unit_name,'item_price'=>$item_price,'item_amount'=>$item_amount);
				
			}
		}		
		//item_produced_data
		$item_produced_unit_labels= array();
		$final_item_produced_data = array();
		if($item_produced_data){
			foreach($item_produced_data as $row2){
			    $item_info   = $this->get_item_info($row2['item_id']);	
			 	$unit_id     = $item_info['item_unit'];	
				$item_name   = $item_info['item_name'];
				
				$get_units_info  = $this->get_units_info($unit_id);
				$unit_name       =  $get_units_info['item_unit'];
				$item_produced_unit_labels[]= array("label"=>$item_name,"value"=>$unit_name);
				 $item_qty =  $row2['item_qty']*$counter;
				 $item_price  = ($row2['item_usr_amt']/$row2['item_qty']);
				
				$final_item_produced_data[]= array("item_unit_id"=>$unit_id,"id"=>$row2['bom_id'],"item_id"=>$row2['item_id'],"item_name"=>$item_name,'item_qty'=>$item_qty ,'item_unit'=>$unit_name,'item_price'=>$item_price,'item_amount'=>$row2['item_usr_amt'],'item_value'=>'');
					
				
				
			}
			
		}
		
		
		//byproduct_produced_data
		$byproduct_produced_unit_labels = array();
		$final_byproducts_produced_data = array();
		if($byproduct_produced_data){
			foreach($byproduct_produced_data as $row3){
				$item_info   = $this->get_item_info($row3['item_id']);	
			 	$unit_id     = $item_info['item_unit'];	
				$item_name   = $item_info['item_name'];
				
				 $item_qty =  $row3['item_qty']*$counter;
				 $item_price  = ($row3['item_usr_amt']/$row3['item_qty']);
				
				
				
				$get_units_info  = $this->get_units_info($unit_id);
				$unit_name       = $get_units_info['item_unit'];
				$byproduct_produced_unit_labels[]= array("label"=>$item_name,"value"=>$unit_name);
				$final_byproducts_produced_data[]= array("item_unit_id"=>$unit_id,"id"=>$row3['bom_id'],"item_id"=>$row3['item_id'],"item_name"=>$item_name,'item_qty'=>$item_qty,'item_unit'=>$unit_name,'item_price'=>$item_price,'item_amount'=>$row3['item_usr_amt'],'item_value'=>'');
				
			}
			
		}
		
		//additional_cost_data
		$final_additional_cost_data = array();
		$expensetypes_labels        = array();
		if($additional_cost_data){
			foreach($additional_cost_data as $row4){
			   
				if(strtolower($row4['bom_addcost_type'])=='p')
					  $bom_addcost_type ='Percentage';
				else if(strtolower($row4['bom_addcost_type'])=='a')
					  $bom_addcost_type ='Absolute';
				
				
				  $expensetypes_labels[]= array("label"=>$bom_addcost_type,"value"=>$row4['bom_addcost_type']);
				  
				  $account_table_name = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
                  $account_table = $this->db->table($account_table_name)->where('acc_id', $row4['bom_addcost_acc_id'])->get()->getRowArray();
				  $expense_name   = $account_table['acc_name'];
				$final_additional_cost_data[]= array("id"=>$row4['bom_addcost_acc_id'],"expense_name"=>$expense_name,'expense_type'=>$bom_addcost_type,'expense_amount'=>$row4['bom_addcost_amt']);
						
			}			
		}
		
		$final_array = array('bom_consm_pricing'=>$bom_consm_pricing,'bom_name'=>$bom_name,'item_consumed_unit_labels'=>$item_consumed_unit_labels,
		                     'item_produced_unit_labels'=> $item_produced_unit_labels,
							 'byproduct_produced_unit_labels' => $byproduct_produced_unit_labels,
		                     'item_consumed_data'=>$final_item_consumed_data,'item_produced_data'=>$final_item_produced_data,
							 'byproduct_produced_data'=>$final_byproducts_produced_data,
		                     'additional_cost_data'=>$final_additional_cost_data,'expensetypes_labels'=>$expensetypes_labels);
		return $final_array;					 
    } 
	
   function get_voucher_cons_info($voucher_txn_id,$comp_id){	 
	   $comp_voucher_type_tbl = $comp_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($comp_voucher_type_tbl)->where('voucher_txn_id', $voucher_txn_id)->where('comp_id', $comp_id)->get()->getRowArray();   	   
    } 	
		
    function get_voucher_info($voucher_type_id,$comp_id){	 
	   $comp_voucher_type_tbl = $comp_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	   $comp_vhtxnconso_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	   $data =  $this->db->table($comp_voucher_type_tbl)->where('voucher_type_id', $voucher_type_id)->where('comp_id', $comp_id)->get()->getRowArray();  
	  
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
    
  public function get_item_info($item_id){
       $comp_id = $this->company_id;
       $item_master_tbl = $comp_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
     return $this->db->table($item_master_tbl)->where('item_id', $item_id)->where('comp_id', $comp_id)->get()->getRowArray();  
   }    
  
  function get_units_info($unit_id){	
      $comp_id	= $this->company_id;
	  $item_unit_master_tbl = $comp_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($item_unit_master_tbl)->where('unit_id', $unit_id)->where('comp_id', $comp_id)->get()->getRowArray();   	   
    }  
	
    function get_eh_groups()
    { 
		$p_groups = [11,13];
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

    function expense_heads_dropdown($comp_id){

    	$eh_groups = $this->get_eh_groups();
		if(count($eh_groups)==0)
			$eh_groups=[0];
	
    	$account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	  	return $this->db->table($account_master_tbl)
	  					->select('acc_name as label, acc_id as value')
	  					->whereIn('acc_grp_id', $eh_groups)
	  					->orWhereIn('acc_grp_parent_id', array('11','13'))
	  					->get()->getResultArray(); 
    }
	 
     function expense_heads_dropdown2($comp_id){
	   $groups_list = array('12','14');  // Direct/Indirect Expense
	   $accounts    = array();
	   foreach($groups_list as $group_id){
		   $accounts[]= $this->get_all_accounts($group_id);
		   
	   }
	  $accountsarray=array(); 
	  if($accounts){
		  foreach($accounts as $rows){
			  if($rows){
			   foreach($rows as $account_id => $account_name){
				  $accountsarray[]=  array("label"=>ucwords($account_name),'value'=> $account_id); 
			   }
				  
			   }
			  
		  }
		  
	  } 	
	   return $accountsarray;
     } 

function get_all_accounts($group_id){
	    $final_accounts  = array();
	    $comp_id         = $this->company_id;
	    $ses_comp_fy_id  = $this->session->get('ses_comp_fy_id');
		$tbl_name        = $comp_id.'_acctgroupn_'.$ses_comp_fy_id;
	    $sql             = $this->db->query("select `acc_grp_id`, `acc_grp_parent_id`, `acc_grp_name`,`under_acc_grp_id` from (select * from `".$tbl_name."` order by `under_acc_grp_id`, `acc_grp_id`) `".$tbl_name."` , (select @pv := '".$group_id."') initialisation where find_in_set(`under_acc_grp_id`, @pv) > 0 and @pv := concat(@pv, ',', `acc_grp_id`);");
               $result     = $sql->getResultArray();
               foreach($result as $key => $row){
                   if($row!=''){
                       $company_all_accounts  = $this->get_group_all_accounts($row['acc_grp_id'],$comp_id);	
                        foreach($company_all_accounts as $company_row){	
                          $final_accounts[$company_row['acc_id']]=$company_row['acc_name'];						
						}
				   }
			   }
			  return $final_accounts; 
       }
   
    function get_group_all_accounts($group_id,$company_id){	
	    $account_master_tbl = $company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	    $builder = $this->db->table($account_master_tbl); 
		$builder->select(array('acc_id','acc_name','acc_op_bal','acc_grp_id'));
        $builder->where('comp_id', $company_id);
		$builder->where('acc_grp_id', $group_id);
		$result = $builder->get()->getResultArray();
	    return $result;
     }   
	 
    function bom_dropdown(){
  	     $comp_id  = $this->company_id;
		 $billofmatn_tbl = $comp_id.'_billofmatn_'.$this->session->get('ses_comp_fy_id');
	     $data =  $this->db->table($billofmatn_tbl)->orderBy('bom_name','ASC')->get()->getResultArray();
	     $final_result      = array();
	     $final_result['']  = 'Choose';
	     if($data){
		  foreach($data as $row){
              $final_result[$row['bom_id']] = $row['bom_name'];			   
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
	 
  public function get_voucher_no($voucher_id){
      $comp_vch_cons_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
      $builder = $this->db->table($comp_vch_cons_tbl); 
      $builder->select('MAX(comp_vch_no) as max_comp_vch_no');
      $builder->where('voucher_type_id',$voucher_id);
	  if($this->session->get('ses_boid')!='')
	   $builder->where('bo_id', $this->session->get('ses_boid'));
	  $builder->orderBy('voucher_txn_id','DESC');	
	  $result =  $builder->get()->getRowArray();
      if($result){
          $max_comp_vch_no = $result['max_comp_vch_no'];
          return $max_comp_vch_no+1;
        }
      else
        return 1;
     }	 
     
     function item_unit_info($comp_id,$unit_id){
       $item_unit_master_tbl =  $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');	 
	   return  $this->db->table($item_unit_master_tbl)->where('unit_id', $unit_id)->where('comp_id', $comp_id)->orderBy('item_unit','ASC')->get()->getRowArray();  
         
     }
	 
	 function units_dropdown($comp_id){
	   $item_unit_master_tbl =  $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');	 
	   $data =  $this->db->table($item_unit_master_tbl)->where('comp_id', $comp_id)->orderBy('item_unit','ASC')->get()->getResultArray();
	   $final_result      = array();
	   if($data){
		  foreach($data as $row){
		      $unit_name      = $row['item_unit']; 
              $final_result[] =array("label"=>$unit_name,"value"=>$row['unit_id']);			   
	        }
        }
	  return $final_result;	
     }
  
 function matrcntr_dropdown($comp_id){
		 $comp_mtcnt_tbl = $comp_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
	     $data =  $this->db->table($comp_mtcnt_tbl)->where('comp_id', $comp_id)->orderBy('mat_cent_name','ASC')->get()->getResultArray();
	     $final_result      = array();
	     $final_result['']  = '';
	     if($data){
		  foreach($data as $row){
              $final_result[$row['mat_cent_id']] =$row['mat_cent_name'];			   
	        }
        }
	  return $final_result;	
     }
       
}