<?php
namespace App\Models\Admin;

use CodeIgniter\Model;
use App\Models\Admin\BalancesModel;
use App\Models\Admin\TransactionModel;
use App\Libraries\externaldb;
use App\Libraries\enc_string;

class StockjournalModel extends Model	{

    public function __construct() {
        parent::__construct();        
       $this->externaldb    = new externaldb();	
	   $this->db            = $this->externaldb->get_company_db();
	   $this->session       = \Config\Services::session();
	   $this->BalancesModel  = new BalancesModel();
	   $this->TransactionModel  = new TransactionModel();	
	   $this->company_id    = $this->session->get('ses_company_id');
	   $this->enc_string    = new enc_string();
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
  
      function get_company_items($comp_id){	
        $searchtext        = $_GET['term'];
       
	   $item_master_tbl = $comp_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
	   $data =  $this->db->table($item_master_tbl)->where('( `item_name` LIKE  "%'.$searchtext.'%" OR `item_alias` LIKE  "%'.$searchtext.'%" OR `item_short` LIKE  "%'.$searchtext.'%") ')->where('comp_id', $comp_id)->orderBy('item_name','ASC')->get()->getResultArray();

	   $final_result      = array();
	   if($data){
		  foreach($data as $row){
		      
		       $item_unit_info  = $this->item_unit_info($comp_id,$row['item_unit']); 
             if($item_unit_info)
                 $item_unit_name = $this->enc_string->nc_string($item_unit_info['item_unit'],'de');
              else
                $item_unit_name ='' ;
		      
               $final_result[]    = array("label"=>ucwords($row['item_name']),"value"=>$row['item_id'],'units'=>$item_unit_name);	
		      }
        }
	    return json_encode($final_result);  
     }
  
    function get_voucher_cons_info($voucher_txn_id,$comp_id){	 
	   $comp_voucher_type_tbl = $comp_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($comp_voucher_type_tbl)->where('voucher_txn_id', $voucher_txn_id)->where('comp_id', $comp_id)->get()->getRowArray();   	   
    } 	
		
    function get_voucher_info($voucher_type_id,$comp_id){	 
	  $comp_voucher_type_tbl = $comp_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($comp_voucher_type_tbl)->where('voucher_type_id', $voucher_type_id)->where('comp_id', $comp_id)->get()->getRowArray();   	   
    }   
    
  public function item_txntbl_entry($item_id,$tablename,$comp_id){
   $response =  $this->db->table($tablename)->where('item_id', $item_id)->where('comp_id', $comp_id)->orderBy('item_txn_id')->limit(1)->get()->getRowArray();      
    return $response;  
  }   
  
 public function count_item_txn($item_id,$tablename,$comp_id){
   $response =  $this->db->table($tablename)->where('item_id', $item_id)->where('comp_id', $comp_id)->orderBy('item_txn_id')->countAllResults();
//   echo $this->db->GetLastQuery();
    return $response;  
  }   
  
  
   public function stockjournal_register_listings($voucher_txn_id,$voucher_type_id,$vch_subtype_id){
        $comp_id         = $this->session->get('ses_company_id');
	    $ses_comp_fy_id  = $this->session->get('ses_comp_fy_id');
	    $comptxnmst_tbl  = $comp_id.'_comptxnmst_'.$ses_comp_fy_id;
	    $base_url        = base_url().'/'.getenv('AdminPath');
	    
	    $vch_txn_conso_tbl = $comp_id.'_vhtxnconso_'.$ses_comp_fy_id;
	    $builder           = $this->db->table($vch_txn_conso_tbl); 
        $builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);	 
		$builder->where('voucher_txn_id',$voucher_txn_id);
	    $builder->where('voucher_type_id',$voucher_type_id);
	    $builder->where('vch_subtype_id',$vch_subtype_id);
	  	$result = $builder->get()->getResultArray();

		$all_records = array();	
        $records= array();
        foreach($result as $values){
		            $builder   = $this->db->table($comptxnmst_tbl); 
                    $builder->orderBy('txn_id');      
                    $builder->where('voucher_txn_id', $values['voucher_txn_id']);	
                    $builder->where('comp_vch_series_id',$values['voucher_type_id']);	
            		$builder->where('comp_id', $comp_id);	
            		$result = $builder->get()->getResultArray();
            		
                    foreach($result as $rowvalues){		
                        if($rowvalues['master_id_type']=='itm'){
            		      	$itm_txn_tables_result =  $this->items_transactions($comp_id,$rowvalues['master_id'],$rowvalues['txn_id']);
            		        if($itm_txn_tables_result)
            			       $all_records[$rowvalues['txn_id']] = $itm_txn_tables_result;
            		        }
            		    
                    }
		     }	 	
	
	 
	  $all_ids   = array();   
	  $all_vouchers=array();
	 
	 foreach($all_records as $row){
	  $all_vouchers[$row['voucher_no']]=$row['voucher_no'];
	    if(isset($row['mat_cent_id'])){  
	    $material_centre_info = $this->material_centre_info($comp_id,$row['mat_cent_id']);
	    if($material_centre_info)
	        $material_centre = $this->enc_string->nc_string($material_centre_info['mat_cent_name'],'de');
	       else
	       $material_centre  = '';
	    }
	    else 
	    $material_centre  = '';
	    
		$item_balance_tooltip = $this->get_item_balances($row['item_id'],$row['txn_date'],$row["item_unit_id"]);	
		$tooltip_info         = $item_balance_tooltip[$row['item_id']];
		$balancetable = "<table style='width:100%;'><tr><td colspan='2'>".$tooltip_info['unit_name']."</td></tr><tr><td>Available</td><td align='right'>".$tooltip_info["AvailQty"]."</td></tr><tr><td>Packed</td><td align='right'>".$tooltip_info["PackQty"]."</td></tr><tr><td>Obselete</td><td align='right'>".$tooltip_info["ObseQty"]."</td></tr><tr><td>In Transit</td><td align='right'>".$tooltip_info["IntrsQty"]."</td></tr></table>";
				
				
		
	   if($row['item_txn_drcr']=='c'){
	       $all_ids["from"][] = array("item_unit_id"=>$row["item_unit_id"],"AvailQty"=>$tooltip_info["AvailQty"],"pq_cellattr"=> array("item_name"=>array("title"=>$balancetable)),"item_unit"=>$row['item_unit'],"item_id"=>$row['item_id'],"postion"=>"l","Lgroup_name"=>"from","voucher_txn_id"=>$row['voucher_txn_id'],"voucher_type_id"=>$row['voucher_type_id'],"material_centre"=>$material_centre,"bill_no"=>$row['voucher_no'],"voucher_date"=>date("d M Y", strtotime($row['txn_date'])),"from_txn_id"=>$row['txn_id'],"from_item_name"=>$row['item_name'],"from_item_qty"=>$row['itemtxn_qty'],"from_item_price"=>$row['item_price']/$row['itemtxn_qty'],"from_item_amount"=>$row['item_price']);
	       
	   }   
	   if($row['item_txn_drcr']=='d'){
	        $all_ids["to"][] =array("item_unit_id"=>$row["item_unit_id"],"item_unit"=>$row['item_unit'],"item_id"=>$row['item_id'],"postion"=>"r","Rgroup_name"=>"to","voucher_txn_id"=>$row['voucher_txn_id'],"voucher_type_id"=>$row['voucher_type_id'],"material_centre"=>$material_centre,"bill_no"=>$row['voucher_no'],"voucher_date"=>date("d M Y", strtotime($row['txn_date'])),"to_txn_id"=>$row['txn_id'],"to_item_name"=>$row['item_name'],"to_item_qty"=>$row['itemtxn_qty'],"to_item_price"=>$row['item_price']/$row['itemtxn_qty'],"to_item_amount"=>$row['item_price']);
	       
	      }   
	 }	
   return $all_ids;
    }
	
	public function get_item_balances($itemid,$voucher_date,$unit_id){
	 if($unit_id!='' && $itemid!=''){
        $item_unit_info = $this->item_unit_info($this->company_id,$unit_id);
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
      	$result = $this->db->table($itm_txn_tbl)
		                    ->select('(CASE WHEN item_avail =1 THEN item_bal_qty ELSE 0 END) AS AvailQty,
									  (CASE WHEN item_avail =2 THEN item_bal_qty ELSE 0 END) AS PackQty,
									  (CASE WHEN item_avail =0 THEN item_bal_qty ELSE 0 END) AS ObseQty,
									  (CASE WHEN item_avail =3 THEN item_bal_qty ELSE 0 END) AS IntrsQty')
        					->where('item_id', $itemid)
        					->where('item_unit', $unit_id)
							->where('item_txn_date <=',date('Y-m-d',strtotime($voucher_date)))
        					->orderBy('item_txn_date', 'desc')
        					->orderBy('itemtxnbal_id', 'desc')
        					->limit(1)
        					->get()->getRowArray();
     if($result){	
        $json_array[$itemid]=array("unit_name"=>$item_unit_name,"AvailQty"=>$result['AvailQty'],"PackQty"=>$result['PackQty'],
		                                     "ObseQty"=>$result['ObseQty'],"IntrsQty"=>$result['IntrsQty']);	 	   
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
  
   function material_centre_info($comp_id,$id){
	   $mat_centre_master_tbl =$comp_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
	   return $this->db->table($mat_centre_master_tbl)->where('mat_cent_id', $id)->where('comp_id', $comp_id)->orderBy('mat_cent_name','ASC')->get()->getRowArray();
	 }
	 
   function get_item_balance($comp_id,$item_id,$voucher_date){
                $ses_comp_fy_id  = $this->session->get('ses_comp_fy_id');
                $item_txn_table  = $comp_id.'_itemtxnnnn_'.$item_id.'_'.$ses_comp_fy_id;	
				$builder = $this->db->table($item_txn_table);
				$builder->limit(1);
				$builder->orderBy('item_txn_id','DESC');     
				$builder->where('comp_id', $comp_id);
				$builder->where('item_id', $item_id);
				$builder->where('item_txn_date',$voucher_date);
				$result = $builder->get()->getRowArray();
                 if($result)
                   return 0;//$result['item_qty_bal'];
                 else
                  return "0";
         }
         
  
  function items_transactions($comp_id,$item_id,$txn_id){
	            $ses_comp_fy_id     = $this->session->get('ses_comp_fy_id');
	            $list               = array();
			    $voucher_txn_table  = $comp_id.'_itemtxnnnn_'.$item_id.'_'.$ses_comp_fy_id;	
			    
			    $item_info = $this->get_item_info($item_id);
                if($item_info)
                  $item_name = $item_info['item_name'];
                else
                  $item_name = "";
                
               
               $item_unit_info  = $this->item_unit_info($comp_id,$item_info['item_unit']); 
             if($item_unit_info)
                 $item_unit_name = $this->enc_string->nc_string($item_unit_info['item_unit'],'de');
              else
                $item_unit_name ='' ;
              
			  
				$builder = $this->db->table($voucher_txn_table); 
				$builder->orderBy('item_txn_date');     
				$builder->where('comp_id', $comp_id);
				$builder->where('item_id', $item_id);
				$builder->where('txn_id', $txn_id);
				$values = $builder->get()->getRowArray();
				
			    $list   = array();
			    if($values){			
				   //foreach($result as $values){	
                      $account_id   = $values['item_id'];	
                      $acc_txn_drcr = $values['item_txn_drcr'];
                      $acc_txn_narr = '';//$values['item_txn_narr'];					  
					  $posted_on    = $values['item_txn_date'];
					  $item_qty     = 0;//$values['item_qty_bal'];
					  $matrcentrid  = $values['mat_cent_id'];
					  $voucher_no   = 0;//$values['comp_vch_series_no'];
					  $item_price   = $values['item_txn_amount'];
					  
					  $voucher_type_id = $values['voucher_type_id'];
					  $voucher_txn_id = $values['voucher_txn_id'];
					  $itemtxn_qty    = $values['item_txn_qty'];
					  
					 
					       $book_stock = '';
					       $stock_diff = '';
					  
					  if($values['item_txn_drcr']=='d'){
						 $debit  = $values['item_txn_amount'];
						 $credit = '0.00';
					   }
					  else if($values['item_txn_drcr']=='c'){
						 $credit = $values['item_txn_amount'];
						 $debit  = '0.00';
						 }
						 
				 	    $item_balance = 0;//$this->get_item_balance($comp_id,$values['item_id'],$values['item_txn_date']);
						$list        = array("item_unit_id"=>$item_unit_info['unit_id'],"itemtxn_qty"=>$itemtxn_qty,"voucher_txn_id"=>$voucher_txn_id,"voucher_type_id"=>$voucher_type_id,"stock_diff"=>$stock_diff,"book_stock"=>$book_stock,"voucher_no"=>$voucher_no,"mat_cent_id"=>$matrcentrid,"item_unit"=>$item_unit_name,"item_price"=>$item_price,"item_qty"=>$item_qty,"item_name"=>$item_name,"item_txn_narr"=>$acc_txn_narr,"comp_id"=>$comp_id,"txn_id"=>$values['txn_id'],"txn_date" =>$values['item_txn_date'],'item_id'=>$item_id,'item_txn_drcr'=>$acc_txn_drcr,'debit'=>$debit,'credit'=>$credit,'item_balance'=>$item_balance);
				      // } 
				    }
	   return $list;  
   } 
   
  public function get_item_info($item_id){
       $comp_id = $this->company_id;
       $item_master_tbl = $comp_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
     return $this->db->table($item_master_tbl)->where('item_id', $item_id)->where('comp_id', $comp_id)->get()->getRowArray();  
   }    
   
   public function delete_transaction_byid($tn,$txnid,$comp_id,$sel_account_id){ 
     $this->db->table($tn)->where('txn_id',$txnid)->where('comp_id',$comp_id)->where('acc_id',$sel_account_id)->delete();
     
     $comptxntable =$comp_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
     $this->db->table($comptxntable)->where('txn_id',$txnid)->where('comp_id',$comp_id)->where('master_id',$sel_account_id)->delete();
   }
   
   public function delete_item_transaction_byid($tn,$txnid,$comp_id,$sel_item_id){ 
     $this->db->table($tn)->where('txn_id',$txnid)->where('comp_id',$comp_id)->where('item_id',$sel_item_id)->delete();
     
     
     $itemtxnbal_table = $comp_id.'_itemtxnbal_'.$sel_item_id.'_'.$this->session->get('ses_comp_fy_id');
     $this->db->table($itemtxnbal_table)->where('txn_id',$txnid)->where('item_id',$sel_item_id)->delete();
     
     $comptxntable =$comp_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
     $this->db->table($comptxntable)->where('txn_id',$txnid)->where('comp_id',$comp_id)->where('master_id',$sel_item_id)->delete();
    // echo $this->db->GetLastQuery();
   }
   
  public function sale_transactions_list($voucher_txn_id,$voucher_type_id){
        $comp_id                =  $this->session->get('ses_company_id');
	    $base_url               = base_url().'/'.getenv('AdminPath');
		$ses_comp_fy_id         = $this->session->get('ses_comp_fy_id'); 
        $comptxnmst_tbl         = $comp_id.'_comptxnmst_'.$ses_comp_fy_id;
		$builder                = $this->db->table($comptxnmst_tbl); 
        $builder->orderBy('txn_id');      
        $builder->where('voucher_txn_id', $voucher_txn_id);	
        $builder->where('comp_vch_series_id', $voucher_type_id);	
		$builder->where('comp_id', $comp_id);	
		$result = $builder->get()->getResultArray();
		$all_records = array();		
        foreach($result as $values){		
            if($values['master_id_type']=='itm'){
                
		     	$itm_txn_tables_result =  $this->all_items_transactions($voucher_txn_id,$comp_id,$values['master_id'],'c');
			    if($itm_txn_tables_result)
			       $all_records['items'][$values['txn_id']] = $itm_txn_tables_result;
		        }
		     elseif($values['master_id_type']=='acc'){  
		        $acc_txn_tables_result =  $this->all_transactions($comp_id,$values['txn_id']);
			    if($acc_txn_tables_result)
			       $all_records['acc'][$values['txn_id']] = $acc_txn_tables_result;
		     }
        }
 	    return $all_records;
  }    
   
   public function purchase_transactions_list($voucher_txn_id,$voucher_type_id){
        $comp_id                =  $this->session->get('ses_company_id');
	    $base_url               = base_url().'/'.getenv('AdminPath');
		$ses_comp_fy_id         = $this->session->get('ses_comp_fy_id'); 
        $comptxnmst_tbl         = $comp_id.'_comptxnmst_'.$ses_comp_fy_id;
		$builder                = $this->db->table($comptxnmst_tbl); 
        $builder->orderBy('txn_id');      
        $builder->where('voucher_txn_id', $voucher_txn_id);	
        $builder->where('comp_vch_series_id', $voucher_type_id);	
		$builder->where('comp_id', $comp_id);	
		$result = $builder->get()->getResultArray();
	
		$all_records = array();		
        foreach($result as $values){		
            if($values['master_id_type']=='itm'){
                
		     	$itm_txn_tables_result =  $this->all_items_transactions($voucher_txn_id,$comp_id,$values['master_id'],'d');
			    if($itm_txn_tables_result)
			       $all_records['items'][$values['txn_id']] = $itm_txn_tables_result;
		        }
		     elseif($values['master_id_type']=='acc'){  
		        $acc_txn_tables_result =  $this->all_transactions($comp_id,$values['txn_id']);
			    if($acc_txn_tables_result)
			       $all_records['acc'][$values['txn_id']] = $acc_txn_tables_result;
		     }
        }
     
 	    return $all_records;
  }  
  
  public function ajax_vouchers_transactions_list($voucher_txn_id){ 
	    $comp_id                =  $this->session->get('ses_company_id');
	    $base_url               = base_url().'/'.getenv('AdminPath');
		$ses_comp_fy_id         = $this->session->get('ses_comp_fy_id');
		$voucher_txn_master_tbl = $comp_id.'_vhtxnconso_'.$ses_comp_fy_id;
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
     
   function get_company_all_accounts($company_id){
	    $account_master_tbl = $company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	    $builder = $this->db->table($account_master_tbl); 
		$builder->select(array('acc_id','acc_name'));
        $builder->where('comp_id', $company_id);
		$result = $builder->get()->getResultArray();
	    return $result;
   }   
  
    
   function get_company_all_items($company_id){
	    $items_master_tbl = $company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
	    $builder = $this->db->table($items_master_tbl); 
		$builder->select(array('item_id','item_name'));
        $builder->where('comp_id', $company_id);
		$result = $builder->get()->getResultArray();
	    return $result;
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
       
		$ses_comp_fy_id            =  $this->session->get('ses_comp_fy_id');
		$comp_vch_txn_conso_tbl    = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
  	    $comp_vch_txn_trail_tbl    = $this->company_id.'_vhtxntrail_'.$this->session->get('ses_comp_fy_id');
  	    
  	   
		$ids                       = explode(",",$ids);
        foreach($ids as $voucher_txn_id){
		    $this->db->table($comp_vch_txn_conso_tbl)->where('voucher_txn_id',$voucher_txn_id)->delete();		 
            $this->db->table($comp_vch_txn_trail_tbl)->where('voucher_txn_id',$voucher_txn_id)->where('comp_id',$comp_id)->delete();
		    //$this->db->query("DROP TABLE `".$account_txn_table."` ");
	       
	       // delete items from voucher
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
	                    $account_table_name = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	                    $account_table = $this->db->table($account_table_name)->where('acc_id', $party_id)->get()->getRowArray();
	                 
	                    $this->update_account_all_balances($insert_table_name,$account_table['acc_op_bal'],$account_table['acc_op_drcr']);
	                 
	                  } 
		         
		           }
		           
		        }
		        
		 	 }
		return TRUE;
	 }  
   
    function all_items_transactions($voucher_txn_id,$comp_id,$item_id,$item_type){
	            $ses_comp_fy_id     = $this->session->get('ses_comp_fy_id');
	            $list               = array();
			    $voucher_txn_table  = $comp_id.'_itemtxnnnn_'.$item_id.'_'.$ses_comp_fy_id;	
			    
			    $item_info = $this->get_item_info($item_id);
                if($item_info)
                  $item_name = $item_info['item_name'];
                else
                  $item_name = "";
                
               
               $item_unit_info  = $this->item_unit_info($comp_id,$item_info['item_unit']); 
             if($item_unit_info)
                 $item_unit_name = $this->enc_string->nc_string($item_unit_info['item_unit'],'de');
              else
                $item_unit_name ='' ;
              
			    
			  
				$builder = $this->db->table($voucher_txn_table); 
				$builder->orderBy('item_txn_date');     
				$builder->where('comp_id', $comp_id);
				$builder->where('item_txn_drcr', $item_type); 
				$builder->where('voucher_txn_id', $voucher_txn_id);
				
				$result = $builder->get()->getResultArray();
			
			    if($result){			
				   foreach($result as $values){	
                      $account_id   = $values['item_id'];	
                      $acc_txn_drcr = $values['item_txn_drcr'];
                      $acc_txn_narr = $values['item_txn_narr'];					  
					  $posted_on    = $values['item_txn_date'];
					  $item_qty     = $values['item_txn_qty'];
					  $matrcentrid  = $values['mat_cent_id'];
					   
					   
					  $item_price   = $values['item_txn_amount'];
					  if($values['item_txn_drcr']=='d'){
						 $debit  = $values['item_txn_amount'];
						 $credit = '0.00';
					   }
					   else if($values['item_txn_drcr']=='c'){
						 $credit = $values['item_txn_amount'];
						 $debit  = '0.00';
						 }
						$list= array("mat_cent_id"=>$matrcentrid,"item_unit"=>$item_unit_name,"item_price"=>$item_price,"item_qty"=>$item_qty,"item_name"=>$item_name,"item_txn_narr"=>$acc_txn_narr,"comp_id"=>$comp_id,"txn_id"=>$values['txn_id'],"txn_date" =>$values['item_txn_date'],'item_id'=>$item_id,'item_txn_drcr'=>$acc_txn_drcr,'debit'=>$debit,'credit'=>$credit,'posted_on'=>$posted_on);
				       } 
				    }
	   return $list;  
   }
 
 function all_transactions($comp_id,$txn_id){
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
                      $account_id   = $values['acc_id'];	
                      $acc_txn_drcr = $values['acc_txn_drcr'];
                      $acc_txn_narr = $values['acc_txn_narr'];					  
					  $posted_on    = $values['posted_on'];
					  if($values['acc_txn_drcr']=='d'){
						 $debit  = $values['acc_txn_amount'];
						 $credit = '0.00';
					   }
					   else if($values['acc_txn_drcr']=='c'){
						 $credit = $values['acc_txn_amount'];
						 $debit  = '0.00';
						 }
						$list = array("acc_txn_narr"=>$acc_txn_narr,"comp_id"=>$comp_id,"txn_id"=>$txn_id,"txn_date" =>$values['acc_txn_date'],'account_id'=>$account_id,'acc_txn_drcr'=>$acc_txn_drcr,'account_name'=>$show_account_name,'debit'=>$debit,'credit'=>$credit,'posted_on'=>$posted_on);
				       } 
				    }
				 }
	          }
	   return $list;  
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
	 
	public function update_voucher_cons_data($id,$data){
		 $comp_vch_cons_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	     $this->db->table($comp_vch_cons_tbl)->where('voucher_txn_id', $id)->update($data);	
		 return 1;	
	 }
	 
  public function get_voucher_no($voucher_id){
      $comp_vch_cons_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
      $builder = $this->db->table($comp_vch_cons_tbl); 
      $builder->select('MAX(comp_vch_no) as max_comp_vch_no');
      $builder->where('comp_vch_series_id',$voucher_id);
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
   
  /*	
	 public function get_item_qty_balance($comp_id,$tbl_name,$item_id,$item_type,$qtyvalue){
       $item_type = strtolower($item_type);
		$builder = $this->db->table($tbl_name); 
		$builder->orderBy('item_txn_id','DESC');	
        $builder->limit('1'); 		
		$builder->where('comp_id', $comp_id);
		$builder->where('item_id', $item_id);	
		$result =  $builder->get()->getRowArray();		
		 if(!$result)
		   { 
			$last_sums = 0;
			if($item_type=='c')
				$qtbalance = $last_sums - $qtyvalue;
			else if($item_type=='d')
				$qtbalance = $last_sums + $qtyvalue;
		   } else{	
			if($item_type=='c')
				$qtbalance = $result['item_qty_bal'] - $qtyvalue;
			else if($item_type=='d')
				$qtbalance = $result['item_qty_bal'] + $qtyvalue;
			  }
			  
		return $qtbalance;	
		
		}
		
   public function get_item_op_balance($comp_id,$tbl_name,$item_id,$item_type,$qtyvalue,$qtybal){
       
        $row_bal = $qtybal*$qtyvalue;
        $item_type = strtolower($item_type);
		$builder = $this->db->table($tbl_name); 
		$builder->orderBy('item_txn_id','DESC');	
        $builder->limit('1'); 		
		$builder->where('comp_id', $comp_id);
		$builder->where('item_id', $item_id);	
		$result =  $builder->get()->getRowArray();		
		 if(!$result)
		 { 
			$last_sums = 0;
			if($item_type=='c')
				$balance = $last_sums - $row_bal;
			else if($item_type=='d')
				$balance = $last_sums + $row_bal;
		 }	else{	
			if($item_type=='c')
				$balance = $result['item_value_bal'] - $row_bal;
			else if($item_type=='d')
				$balance = $result['item_value_bal'] + $row_bal;
			  }
		return $balance;	
		
		}
	 */
	 public function add_itemstxn_transactions($tblename ,$items_data,$voucher_txn_data,$item_open_qty,$item_open_value,$item_unit_id){
	     if($items_data){
	             $comp_vch_series_id  = $voucher_txn_data['comp_vch_series_id'];
	             $voucher_txn_id      = $items_data['voucher_txn_id'];
	             $comp_txn_master_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	             
	             $txn_data = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$comp_vch_series_id,"voucher_txn_id"=>$voucher_txn_id,"master_id"=>$items_data['item_id'],'master_id_type'=>'itm');
            	 $this->db->table($comp_txn_master_tbl)->insert($txn_data);	
                 $txn_id =  $this->db->insertID();	
                 
	             $items_data['txn_id'] = $txn_id;  
	             
                 $this->db->table($tblename)->insert($items_data);   
            	 // Add voucher transdactions trail 
            	 
            	 $comp_vch_trail_tbl = $this->company_id.'_vhtxntrail_'.$this->session->get('ses_comp_fy_id');
            	 $voucher_trail_data = array("comp_id"=>$this->company_id,"voucher_txn_id"=>$voucher_txn_id,"txn_id"=>$txn_id);
            	 $this->db->table($comp_vch_trail_tbl)->insert($voucher_trail_data);
            	 
            	 
            	 // check row num datewise and update row num value in table 
				 $row_nums = $this->get_itemstable_rownums($tblename);
	             if($row_nums){
	                 foreach($row_nums as $drow){
	                     $item_txn_id     = $drow['item_txn_id'];
	                     $row_num         = $drow['row_num'];
	                     $txn_id          = $drow['txn_id'];
	                     $comp_id         = $drow['comp_id'];
	                     $voucher_txn_id  = $drow['voucher_txn_id'];
	                     
	                     $updte_data = array("item_txn_id"=>$row_num);
	                     // $this->update_all_entries($tblename,$updte_data,$txn_id,$comp_id);
	                  }
	                  
	                 $item_balance_data = array("item_txn_date"=>$items_data['item_txn_date'],"item_id"=>$items_data['item_id'],"txn_id"=>$items_data['txn_id'],"item_txn_drcr"=>$items_data['item_txn_drcr'],"item_txn_id"=>"","voucher_txn_id"=>$items_data['voucher_txn_id'],
					                             "bo_id"=>"0","mat_cent_id"=>$items_data['mat_cent_id'],"item_unit"=>$item_unit_id,"item_bal_qty"=>$items_data['item_txn_qty'],"item_avail"=>"1");
					  
	                   $itemtxnbal_id = $this->BalancesModel->add_itemtxnbal($items_data['item_id'],$item_balance_data,$items_data['item_txn_drcr'],$item_open_qty,$item_open_value);     
	                   $this->TransactionModel->CalculateValuation($items_data['item_id'],$items_data['item_unit_id'],$items_data['mat_cent_id'],0,$itemtxnbal_id,$items_data['item_txn_drcr'],$items_data['item_txn_date']);
					   $this->TransactionModel->CalculateValuation($items_data['item_id'],$items_data['item_unit_id'],$items_data['mat_cent_id'],1,$itemtxnbal_id,$items_data['item_txn_drcr'],$items_data['item_txn_date']);
	                   $this->TransactionModel->backdatevaluation_calculation($items_data['item_id'],$items_data['item_txn_date']);
				  
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
		 }
		 else{	
			 if($amount_type=='c')
				$balance = $result['acc_bal'] - $new_amount;
			 else if($amount_type=='d')
				$balance = $result['acc_bal'] + $new_amount;
		   }
		return $balance;	
		
		}
	 
   public function add_sale_transactions($insert_table_name,$data,$voucher_txn_data,$voucher_txn_name,$voucher_txn_id,$sales_accounts_sum,$party_id){
	   // voucher conso table 
	     $vhtxnconso_table        =  $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	  
	  // insert into transaction master table also
	   $comp_txn_master_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	   
	   $comp_vch_series_id  = $voucher_txn_data['comp_vch_series_id'];
	  // insert in comp_txn_master /////////////////////////////// 
	   $txn_data = array("comp_id"=>$data["comp_id"],"comp_vch_series_id"=>$comp_vch_series_id,"voucher_txn_id"=>$voucher_txn_id,"master_id"=>$party_id,'master_id_type'=>'acc');
	   $this->db->table($comp_txn_master_tbl)->insert($txn_data);	
       $txn_id =  $this->db->insertID();	
       
	  // Add voucher transdactions trail 
	  $comp_vch_trail_tbl = $this->company_id.'_vhtxntrail_'.$this->session->get('ses_comp_fy_id');
	  $voucher_trail_data = array("comp_id"=>$data["comp_id"],"voucher_txn_id"=>$voucher_txn_id,"txn_id"=>$txn_id);
	  $this->db->table($comp_vch_trail_tbl)->insert($voucher_trail_data);	 
	  
	  // inserted in party account tx table
	  $data['txn_id'] = $txn_id;	
	  $data['acc_bal'] = $data['acc_txn_amount'];
	  $this->db->table($insert_table_name)->insert($data);
	  $acc_txn_id =  $this->db->insertID();	
	  
	  // update party account balances
	  $row_nums_party = $this->get_rownums($insert_table_name);
	             if($row_nums_party){
	                 foreach($row_nums_party as $drow){
	                     $acc_txn_id = $drow['acc_txn_id'];
	                     $row_num    = $drow['sale_row_num'];
	                     $txn_id     = $drow['txn_id'];
	                     $comp_id    = $drow['comp_id'];
	                     $voucher_txn_id  = $drow['voucher_txn_id'];
	                     
	                     // update voucher number first
	                     $voucher_number =  $this->update_voucherno_entries($vhtxnconso_table,$voucher_txn_id,$comp_id);
	                     
	                     
	                     $updte_data = array("acc_txn_id"=>$row_num,"comp_vch_series_no"=>$voucher_number);
	                     
	                     $this->update_all_entries($insert_table_name,$updte_data,$txn_id,$comp_id);
	                 }
	                 
	                    $account_table_name = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	                    $account_table = $this->db->table($account_table_name)->where('acc_id', $party_id)->get()->getRowArray();
	                 
	                    $this->update_account_all_balances($insert_table_name,$account_table['acc_op_bal'],$account_table['acc_op_drcr']);
	                    
	              }
	              
	              
	   
	 
	  // add items sales accounts txn table entry
	  if($sales_accounts_sum){
	     foreach($sales_accounts_sum as $acc_id => $acc_total_info){  
	         
	          $txn_data = array("comp_id"=>$data["comp_id"],"comp_vch_series_id"=>$comp_vch_series_id,"voucher_txn_id"=>$voucher_txn_id,"master_id"=>$acc_id,'master_id_type'=>'acc');
	          $this->db->table($comp_txn_master_tbl)->insert($txn_data);
	          $txn_id   =  $this->db->insertID();	
	         
	          $voucher_trail_data = array("comp_id"=>$data["comp_id"],"voucher_txn_id"=>$voucher_txn_id,"txn_id"=>$txn_id);
	          $this->db->table($comp_vch_trail_tbl)->insert($voucher_trail_data);
	          
	          
	          $itemacctotal            =  "-".$acc_total_info['total'];
	       	  $item_sales_txn_table    =  $this->company_id.'_accnttxnnn_'.$acc_id.'_'.$this->session->get('ses_comp_fy_id');
	         
	       	  $account_total_sum       =  $this->get_account_balance($data["comp_id"],$item_sales_txn_table,$acc_id,'c',$acc_total_info['total']);
	        
	          $data['txn_id']          =  $txn_id;	
	          $data['acc_bal']         =  $account_total_sum;//$itemacctotal;
	          $data['acc_id']          =  $acc_id;
	          $data['acc_txn_drcr']    =  'c';
	          $data['acc_txn_amount']  = $acc_total_info['total'];
	          $this->db->table($item_sales_txn_table)->insert($data);
	          
	          // check row num datewise and update row num value in table 
				 $row_nums = $this->get_rownums($item_sales_txn_table);
	             if($row_nums){
	                 foreach($row_nums as $drow){
	                     $acc_txn_id = $drow['acc_txn_id'];
	                     $row_num    = $drow['sale_row_num'];
	                     $txn_id     = $drow['txn_id'];
	                     $comp_id    = $drow['comp_id'];
	                     $voucher_txn_id  = $drow['voucher_txn_id'];
	                     
	                     // update voucher number first
	                     $voucher_number =  $this->update_voucherno_entries($vhtxnconso_table,$voucher_txn_id,$comp_id);
	                     
	                     
	                     $updte_data = array("acc_txn_id"=>$row_num,"comp_vch_series_no"=>$voucher_number);
	                     
	                     $this->update_all_entries($item_sales_txn_table,$updte_data,$txn_id,$comp_id);
	                 }
	                    $account_table_name = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	                    $account_table = $this->db->table($account_table_name)->where('acc_id', $party_id)->get()->getRowArray();
	                 
	                    $this->update_account_all_balances($insert_table_name,$account_table['acc_op_bal'],$account_table['acc_op_drcr']);
	                    
	              }
	              
	         
	         }
          }
    
	 }

   public function add_purchase_transactions($insert_table_name,$data,$voucher_txn_data,$voucher_txn_name,$voucher_txn_id,$sales_accounts_sum,$party_id){
	  // insert into transaction master table also
	   $comp_txn_master_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	   // voucher conso table 
	    $vhtxnconso_table        =  $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	   $comp_vch_series_id  = $voucher_txn_data['comp_vch_series_id'];
	  // insert in comp_txn_master /////////////////////////////// 
	   $txn_data = array("comp_id"=>$data["comp_id"],"comp_vch_series_id"=>$comp_vch_series_id,"voucher_txn_id"=>$voucher_txn_id,"master_id"=>$party_id,'master_id_type'=>'acc');
	   $this->db->table($comp_txn_master_tbl)->insert($txn_data);	
       $txn_id =  $this->db->insertID();	
       
	  // Add voucher transdactions trail 
	  $comp_vch_trail_tbl = $this->company_id.'_vhtxntrail_'.$this->session->get('ses_comp_fy_id');
	  $voucher_trail_data = array("comp_id"=>$data["comp_id"],"voucher_txn_id"=>$voucher_txn_id,"txn_id"=>$txn_id);
	  $this->db->table($comp_vch_trail_tbl)->insert($voucher_trail_data);	 
	  
	  // inserted in party account tx table
	  $data['txn_id'] = $txn_id;	
	  $data['acc_bal'] = $data['acc_txn_amount'];
	  $this->db->table($insert_table_name)->insert($data);
	  $acc_txn_id =  $this->db->insertID();	
	  
	   // update party account balances
	  $row_nums_party = $this->get_rownums($insert_table_name);
	             if($row_nums_party){
	                 foreach($row_nums_party as $drow){
	                     $acc_txn_id = $drow['acc_txn_id'];
	                     $row_num    = $drow['sale_row_num'];
	                     $txn_id     = $drow['txn_id'];
	                     $comp_id    = $drow['comp_id'];
	                     $voucher_txn_id  = $drow['voucher_txn_id'];
	                     
	                     // update voucher number first
	                     $voucher_number =  $this->update_voucherno_entries($vhtxnconso_table,$voucher_txn_id,$comp_id);
	                     
	                     $updte_data = array("acc_txn_id"=>$row_num,"comp_vch_series_no"=>$voucher_number);
	                     
	                     $this->update_all_entries($insert_table_name,$updte_data,$txn_id,$comp_id);
	                 }
	                 
	                   $account_table_name = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	                    $account_table = $this->db->table($account_table_name)->where('acc_id', $party_id)->get()->getRowArray();
	                 
	                    $this->update_account_all_balances($insert_table_name,$account_table['acc_op_bal'],$account_table['acc_op_drcr']);
	                    
	              }
	              
	  
	 
	  // add items sales accounts txn table entry
	  if($sales_accounts_sum){
	     foreach($sales_accounts_sum as $acc_id => $acc_total_info){  
	         
	          $txn_data = array("comp_id"=>$data["comp_id"],"comp_vch_series_id"=>$comp_vch_series_id,"voucher_txn_id"=>$voucher_txn_id,"master_id"=>$acc_id,'master_id_type'=>'acc');
	          $this->db->table($comp_txn_master_tbl)->insert($txn_data);
	          $txn_id   =  $this->db->insertID();	
	         
	          $voucher_trail_data = array("comp_id"=>$data["comp_id"],"voucher_txn_id"=>$voucher_txn_id,"txn_id"=>$txn_id);
	          $this->db->table($comp_vch_trail_tbl)->insert($voucher_trail_data);
	          
	          
	          $itemacctotal            =  $acc_total_info['total'];
	       	  $item_sales_txn_table    =  $this->company_id.'_accnttxnnn_'.$acc_id.'_'.$this->session->get('ses_comp_fy_id');
	          
	       	  $account_total_sum       =  $this->get_account_balance($data["comp_id"],$item_sales_txn_table,$acc_id,'d',$acc_total_info['total']);
	        
	          $data['txn_id']          =  $txn_id;	
	          $data['acc_bal']         =  $account_total_sum;
	          $data['acc_id']          =  $acc_id;
	          $data['acc_txn_drcr']    =  'c';
	          $data['acc_txn_amount']  = $acc_total_info['total'];
	          $this->db->table($item_sales_txn_table)->insert($data);
	          
	          // check row num datewise and update row num value in table 
				 $row_nums = $this->get_rownums($item_sales_txn_table);
	             if($row_nums){
	                 foreach($row_nums as $drow){
	                     $acc_txn_id = $drow['acc_txn_id'];
	                     $row_num    = $drow['sale_row_num'];
	                     $txn_id     = $drow['txn_id'];
	                     $comp_id    = $drow['comp_id'];
	                     $voucher_txn_id  = $drow['voucher_txn_id'];
	                     
	                     // update voucher number first
	                     $voucher_number =  $this->update_voucherno_entries($vhtxnconso_table,$voucher_txn_id,$comp_id);
	                     
	                     
	                     $updte_data = array("acc_txn_id"=>$row_num,"comp_vch_series_no"=>$voucher_number);
	                     
	                     $this->update_all_entries($item_sales_txn_table,$updte_data,$txn_id,$comp_id);
	                 }
	                    $account_table_name = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	                    $account_table = $this->db->table($account_table_name)->where('acc_id', $party_id)->get()->getRowArray();
	                 
	                    $this->update_account_all_balances($insert_table_name,$account_table['acc_op_bal'],$account_table['acc_op_drcr']);
	                    
	              }
	          }
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
        $builder->select('*,ROW_NUMBER() OVER (ORDER BY acc_txn_date) sale_row_num');
        $result = $builder->get()->getResultArray();
        return $result;
     }
	
	public function get_itemstable_rownums($tblname){
        $builder = $this->db->table($tblname);
        $builder->select('*,ROW_NUMBER() OVER (ORDER BY item_txn_date) row_num');
        $result = $builder->get()->getResultArray();
        return $result;
     }
	
	 
	 public function update_account_all_balances($table_name, $acc_op_bal, $acc_op_drcr){
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
  
     public function update_item_all_balances($table_name,$item_open_qty,$item_open_value){
	 
	   $this->db->transStart();
	    $builder = $this->db->table($table_name); 
		$builder->orderBy('item_txn_id');	
        $result =  $builder->get()->getResultArray();
		$balance =0;
		$counter=0;
		$qty_balance =0;
		$first_balance=0;
        if($result){
            
           $balance += $item_open_value;
           $qty_balance = $item_open_qty;
           
           
		   foreach($result as $key => $row){
		        $counter=$counter+1;
		         $sel_voucher_typer = $row['item_txn_drcr'];

		        if(strtolower($sel_voucher_typer)=='c'){
					  $acc_txn_drcr_amount = -($row['item_txn_amount']*$row['item_txn_qty']);
					  $acc_txn_drcr_qty    = -$row['item_txn_qty'];
					  $amount_type ='c';
					}
				else if(strtolower($sel_voucher_typer)=='d'){
					  $acc_txn_drcr_amount = ($row['item_txn_amount']*$row['item_txn_qty']);
					  $acc_txn_drcr_qty    = $row['item_txn_qty'];
					  $amount_type ='d';
					}

	            $balance += $acc_txn_drcr_amount;
				$qty_balance += $acc_txn_drcr_qty;
	        
		
			  
             $this->db->table($table_name)->where('item_txn_id',$row['item_txn_id'])->where('comp_id',$row['comp_id'])->where('item_id',$row['item_id'])->update(array('item_value_bal'=>$balance,'item_qty_bal'=>$qty_balance));		   
		  
		    if ($this->db->transStatus() === true) {
				$this->db->transComplete();
		     }

		   }				
		}
		
    }

	 public function update_all_entries($table_name,$update_data,$txn_id,$comp_id){
       $this->db->table($table_name)->where('txn_id',$txn_id)->where('comp_id',$comp_id)->update($update_data);	
   }
   
    function item_sale_account_info($item_id){
        $itemmaster_table = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
        $data =  $this->db->table($itemmaster_table)->where('item_id',$item_id)->where('comp_id', $this->company_id)->orderBy('item_name','ASC')->get()->getRowArray();
        return $data;
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

 function item_purchase_account_info($item_id){
        $itemmaster_table = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
        $data =  $this->db->table($itemmaster_table)->where('item_id',$item_id)->where('comp_id', $this->company_id)->orderBy('item_name','ASC')->get()->getRowArray();
        return $data;
      }
      
 function party_dropdown($comp_id){
		 $comp_party_tbl = $comp_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	     $data =  $this->db->table($comp_party_tbl)->whereIn('acc_grp_id', array("15","16","17"))->where('comp_id', $comp_id)->orderBy('acc_name','ASC')->get()->getResultArray();
	     $final_result      = array();
	     $final_result['']  = '';
	     if($data){
		  foreach($data as $row){
              $final_result[$row['acc_id']] =$row['acc_name'];			   
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
              $final_result[$row['mat_cent_id']] =$this->enc_string->nc_string($row['mat_cent_name'],'de');			   
	        }
        }
	  return $final_result;	
     }
     
     
   public function add_sale_invoice($data){	    
        $item_master_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
		$item_name = $this->enc_string->nc_string($data['item_name'],'de');
	    $exists = $this->db->table($item_master_tbl)->where('comp_id',$data['comp_id'])->where('LOWER(item_name)', strtolower(trim($item_name)))->get()->getRowArray(); 
	    if($exists)
		 return "0";
	    else{
		  $this->db->table($item_master_tbl)->insert($data);
		  return $this->db->insertID();
	     }
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
   
}