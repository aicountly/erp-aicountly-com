<?php
namespace App\Models\Admin;

use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Libraries\enc_string;

class RegistersModel extends Model	{

    public function __construct() {
        parent::__construct();        
       $this->externaldb    = new externaldb();	
	   $this->db            = $this->externaldb->get_company_db();
	   $this->session       = \Config\Services::session();
	   $this->company_id    = $this->session->get('ses_company_id');
	   $this->enc_string    = new enc_string();
    }
  
   function get_voucher_info($voucher_type_id,$comp_id){	 
	  $comp_voucher_type_tbl = $comp_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($comp_voucher_type_tbl)->where('voucher_type_id', $voucher_type_id)->where('comp_id', $comp_id)->get()->getRowArray();   	   
    }   
	
	function get_account_info($acc_id){	 
	  $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($account_master_tbl)->where('acc_id', $acc_id)->get()->getRowArray();   	   
    } 
    
   function get_company_all_accounts($company_id){
	    $account_master_tbl = $company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	    $builder = $this->db->table($account_master_tbl); 
		$builder->select(array('acc_id','acc_name'));
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
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
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
						$list[] = array("bill_no"=>$values['comp_vch_series_no'],"narration"=>'',"comp_id"=>$comp_id,"txn_id"=>$txn_id,"txn_date" =>$values['acc_txn_date'],'account_name'=>$show_account_name,'debit'=>$debit,'credit'=>$credit,'posted_on'=>$posted_on);
				       } 
				    }
				 }
	          }
	   return $list;
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
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
				$result = $builder->get()->getRowArray();
                 if($result)
                   return $result['item_qty_bal'];
                 else
                  return "0";
         } 
         
  public function ajax_stockjournal_register_list(){
        $comp_id         = $this->session->get('ses_company_id');
	    $ses_comp_fy_id  = $this->session->get('ses_comp_fy_id');
	    $comptxnmst_tbl  = $comp_id.'_comptxnmst_'.$ses_comp_fy_id;
	    $base_url        = base_url().'/'.getenv('AdminPath');
	    $voucher_type_id ='20';
	    
	   $from_date =  $_POST["from_date"];
	   $to_date   =  $_POST["to_date"];
	    if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
        $offset = ($pq_curPage > 1) ? ($pq_rPP * ($pq_curPage - 1)) : 0;
         
	    $vch_txn_conso_tbl = $comp_id.'_vhtxnconso_'.$ses_comp_fy_id;
		
		

	    $builder           = $this->db->table($vch_txn_conso_tbl); 
        $builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);	 
		$builder->where('voucher_type_id',$voucher_type_id);
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));

		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
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
	
	 
	  $all_from_ids  =  $all_to_ids = array();   
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
	    
	   if($row['item_txn_drcr']=='c'){
	       $all_from_ids[$row['voucher_no']][] = array("postion"=>"l","Lgroup_name"=>"from","voucher_txn_id"=>$row['voucher_txn_id'],"voucher_type_id"=>$row['voucher_type_id'],"material_centre"=>$material_centre,"bill_no"=>$row['voucher_no'],"voucher_date"=>date("d M Y", strtotime($row['txn_date'])),"from_txn_id"=>$row['txn_id'],"from_item_name"=>$row['item_name'],"from_item_qty"=>$row['itemtxn_qty'],"from_item_price"=>$row['item_price'],"from_item_amount"=>$row['item_price']*$row['itemtxn_qty']);
	       
	   }   
	   if($row['item_txn_drcr']=='d'){
	        $all_to_ids[$row['voucher_no']][] =array("postion"=>"r","Rgroup_name"=>"to","voucher_txn_id"=>$row['voucher_txn_id'],"voucher_type_id"=>$row['voucher_type_id'],"material_centre"=>$material_centre,"bill_no"=>$row['voucher_no'],"voucher_date"=>date("d M Y", strtotime($row['txn_date'])),"to_txn_id"=>$row['txn_id'],"to_item_name"=>$row['item_name'],"to_item_qty"=>$row['itemtxn_qty'],"to_item_price"=>$row['item_price'],"to_item_amount"=>$row['item_price']*$row['itemtxn_qty']);
	       
	   }   
	   
	 }	 
	 
	  $final_array=array();
  	 foreach($all_vouchers as $vkey ){
	    $d = array_map(null, $all_from_ids[$vkey], $all_to_ids[$vkey]); 
	 
	 
       foreach($d as $dkey => $dsubarray){
        
         
        foreach($dsubarray as $sbkey => $sbrow){
               if(empty($sbrow)){
                   if(isset($dsubarray[1]['postion'])){
                   
                    $nextpost = $dsubarray[1]['postion'];
                   if($nextpost=='r'){
                          $subpost = 'l';
                             $sbrow =array("postion"=>$subpost,'Lgroup_name'=>'from','from_item_name'=>'','from_item_qty'=>'','from_item_price'=>'',
                                            'from_item_amount'=>'','from_txn_id'=>'');
                             }
                   else{
                         $subpost = 'r';
                         $sbrow =array("postion"=>$subpost,'Rgroup_name'=>'to','to_item_name'=>'','to_item_qty'=>'','to_item_price'=>'',
                                      'to_item_amount'=>'','to_txn_id'=>'');
                   }
                   }
                }
      
            $final_array[]=$sbrow;
      
             }
  
          }
 
	} 


   $check     = "Lgroup_name";
   $keys      = array_keys($final_array);
    $new_array = array();
    $n=-1;
    for($i = 0; $i < count($final_array); $i++) {
      
        if(isset($final_array[$keys[$i]])){
        foreach($final_array[$keys[$i]] as $key => $value) {
         
        if($key==$check )
        		{$n++;}
        	
           $new_array[$n][$key]=$value;
        }
        }
    }
    
    
    $final_list = array();
    foreach($new_array as $key => $row){
        if($key!='-1'){
        $final_list[]=$row;
        }
       
    } 
    	if($pq_curPage=='0') $pq_curPage='1';
    $total_Records = count($final_list);
    
    	$offset = ($pq_rPP * ($pq_curPage - 1));
        if ($offset > $total_Records)
          {        
           $pq_curPage = ceil($total_Records / $pq_rPP);
           $offset = ($pq_rPP * ($pq_curPage - 1));
          }
    
    

    $menuItems = array_slice( $final_list, $offset, $pq_rPP );
 
     
 
    echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($menuItems)."}"; 
      
  }
         
    public function ajax_physicalverification_register_list(){
        $comp_id          = $this->session->get('ses_company_id');
	    $ses_comp_fy_id   = $this->session->get('ses_comp_fy_id');
	    $comptxnmst_tbl   = $comp_id.'_comptxnmst_'.$ses_comp_fy_id;
	    $base_url         = base_url().'/'.getenv('AdminPath');
	    $voucher_type_id  = '10';
	    $from_date        =  $_POST["from_date"];
	    $to_date          =  $_POST["to_date"];
	    if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
            
        
	    $vch_txn_conso_tbl = $comp_id.'_vhtxnconso_'.$ses_comp_fy_id;
	    $builder           = $this->db->table($vch_txn_conso_tbl); 
	    
        $builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);	 
		$builder->where('voucher_type_id',$voucher_type_id);
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
	   
	    $result      = $builder->get()->getResultArray();
    	$all_records = array();	
        $records     = array();
        foreach($result as $values){
		            $builder   = $this->db->table($comptxnmst_tbl); 
                    $builder->orderBy('txn_id');      
                    $builder->where('voucher_txn_id', $values['voucher_txn_id']);	
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

	 $final_records = array();     
	 foreach($all_records as $row){
	    if(isset($row['mat_cent_id'])){  
	     
	    $material_centre_info = $this->material_centre_info($comp_id,$row['mat_cent_id']);
	    if($material_centre_info)
	        $material_centre = $this->enc_string->nc_string($material_centre_info['mat_cent_name'],'de');
	       else
	       $material_centre  = '';
	    }
	    else
	    $material_centre  = '';
	    
	    $final_records[]=array("voucher_date"=>date("d M Y", strtotime($row['txn_date'])),"bill_no"=>$row['voucher_no'],"item_name"=>$row['item_name'],
	                           "material_centre"=>$material_centre,"phy_stock"=>$row["item_qty"],"book_stock"=>$row['book_stock'],
	                            "stock_diff"=>$row['stock_diff'],"narration"=>$row['item_txn_narr'],"voucher_txn_id"=>$row['voucher_txn_id'],
	                            "voucher_type_id"=>$row['voucher_type_id']
	                            ); 
	     
	 }	     
	 
	 $total_Records	   = count($final_records);  
if($pq_curPage=='0') $pq_curPage='1';
		$offset = ($pq_rPP * ($pq_curPage - 1));

            if ($offset > $total_Records)
            {        
                $pq_curPage = ceil($total_Records / $pq_rPP);
                $offset = ($pq_rPP * ($pq_curPage - 1));
            }
	 	
	 	
	 	
   $menuItems = array_slice( $final_records, $offset, $pq_rPP );	 
		     
    echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($menuItems)."}"; 
      
  } 
  
   function material_centre_info($comp_id,$id){
	   $mat_centre_master_tbl =$comp_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
	   return $this->db->table($mat_centre_master_tbl)->where('mat_cent_id', $id)->where('comp_id', $comp_id)->orderBy('mat_cent_name','ASC')->get()->getRowArray();
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
				if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));

				$builder->where('txn_id', $txn_id);
				$values = $builder->get()->getRowArray();
			    $list   = array();
			    if($values){			
				   //foreach($result as $values){	
                      $account_id   = $values['item_id'];	
                      $acc_txn_drcr = $values['item_txn_drcr'];
                      $acc_txn_narr = $values['item_txn_narr'];					  
					  $posted_on    = $values['item_txn_date'];
					  $item_qty     = $values['item_qty_bal'];
					  $matrcentrid  = $values['mat_cent_id'];
					  $voucher_no   = $values['comp_vch_series_no'];
					  $item_price   = $values['item_txn_amount'];
					  $remarks      = $values['item_remarks'];
					  $voucher_type_id = $values['voucher_type_id'];
					  $voucher_txn_id = $values['voucher_txn_id'];
					  $itemtxn_qty    = $values['item_txn_qty'];
					  
					  if($remarks){
					      $remarks =  explode("_",$remarks);
					      $book_stock = $remarks[1];
					      $stock_diff = $remarks[2];
					  }else{
					       $book_stock = '';
					       $stock_diff = '';
					      }
					  
					  if($values['item_txn_drcr']=='d'){
						 $debit  = $values['item_txn_amount'];
						 $credit = '0.00';
					   }
					  else if($values['item_txn_drcr']=='c'){
						 $credit = $values['item_txn_amount'];
						 $debit  = '0.00';
						 }
						 
				 	    $item_balance = $this->get_item_balance($comp_id,$values['item_id'],$values['item_txn_date']);
						$list        = array("itemtxn_qty"=>$itemtxn_qty,"voucher_txn_id"=>$voucher_txn_id,"voucher_type_id"=>$voucher_type_id,"stock_diff"=>$stock_diff,"book_stock"=>$book_stock,"voucher_no"=>$voucher_no,"mat_cent_id"=>$matrcentrid,"item_unit"=>$item_unit_name,"item_price"=>$item_price,"item_qty"=>$item_qty,"item_name"=>$item_name,"item_txn_narr"=>$acc_txn_narr,"comp_id"=>$comp_id,"txn_id"=>$values['txn_id'],"txn_date" =>$values['item_txn_date'],'item_id'=>$item_id,'item_txn_drcr'=>$acc_txn_drcr,'debit'=>$debit,'credit'=>$credit,'item_balance'=>$item_balance);
				      // } 
				    }
	   return $list;  
   } 
  
  
  public function ajax_purchase_due_register_list(){
        $company_sales_accounts = $this->company_sales_accounts();
        $comp_id         = $this->session->get('ses_company_id');
	    $ses_comp_fy_id  = $this->session->get('ses_comp_fy_id');
	    $base_url        = base_url().'/'.getenv('AdminPath');
	    $comptxnmst_tbl  = $comp_id.'_comptxnmst_'.$ses_comp_fy_id;
	    $from_date       =  $_POST["from_date"];
	    $to_date         =  $_POST["to_date"];
	   
	   if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
	   if($from_date!='' || $to_date!='')
         {
	    
       
         
	    $vch_txn_conso_tbl = $comp_id.'_vhtxnconso_'.$ses_comp_fy_id;
	    $builder           = $this->db->table($vch_txn_conso_tbl); 
        $builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);	 
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
		$builder->where('(voucher_tag LIKE "PESIDUE%" OR voucher_tag LIKE "ICEPSUR%")');		
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
		$total_Records = $builder->countAllResults();
	    
	    if($pq_curPage=='0') $pq_curPage='1';
		$offset = ($pq_rPP * ($pq_curPage - 1));

            if ($offset > $total_Records)
            {        
                $pq_curPage = ceil($total_Records / $pq_rPP);
                $offset = ($pq_rPP * ($pq_curPage - 1));
            }
            
		 
		$builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);	 
		$builder->where('(voucher_tag LIKE "PESIDUE%" OR voucher_tag LIKE "ICEPSUR%")');		
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
	    $builder->limit($pq_rPP,$offset);
		$result  = $builder->get()->getResultArray();
		
		$all_records = array();	
		if($result){
		    foreach($result as $row){
		        $voucher_txn_id   = $row['voucher_txn_id'];
		        $voucher_type_id  = $row['voucher_type_id'];
		        $comp_vch_series_id = $row['comp_vch_series_id'];
		       	
				$material_centre_info = $this->material_centre_info($comp_id,$row['mat_cent_id']);
        	    if($material_centre_info)
        	        $material_centre = $this->enc_string->nc_string($material_centre_info['mat_cent_name'],'de');
        	       else
        	       $material_centre  = '';
			   
			   
				$builder         = $this->db->table($comptxnmst_tbl); 
                $builder->orderBy('txn_id');    
                $builder->limit(1);
                $builder->where('voucher_txn_id', $voucher_txn_id);	
                $builder->where('master_id_type', 'acc');             	
        		$builder->where('comp_id', $comp_id);	
        		$result = $builder->get()->getResultArray();
                 // echo $this->db->GetLastQuery();				
                foreach($result as $values){		
                    
                    // $mat_cent_id =   
                    $values['bill_no']          =  $row['comp_vch_no'];
                    $values['voucher_date']     =  date("d-m-Y", strtotime($row['voucher_date']));
                    $values['material_centre']  =  $material_centre;
                    $values['voucher_type_id']  =  $voucher_type_id;
                    $values['voucher_txn_id']   =  $voucher_txn_id;
                
                    $party_name = $this->get_invoice_account_info($voucher_txn_id,$comp_vch_series_id);
                    $values['account_name']      =  $party_name;
                    $values['gstin']             = "";
                   
                     
                    $sale_account_amount    = $this->account_amount($voucher_txn_id,$comp_vch_series_id,$comp_id,$values['master_id'],$values['txn_id']); 
                    $values['sale_amount']   =  $sale_account_amount;
                    $all_records[] = $values;
                    
        		        
    	           }
		      }
		  }  
	
		     
   }
   else{
     $total_Records=0; $all_records=array(); 
       
   }
   
    echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($all_records)."}"; 	       

  } 
  
  
   public function ajax_purchase_register_list(){
        $company_sales_accounts = $this->company_sales_accounts();
        $comp_id         = $this->session->get('ses_company_id');
	    $ses_comp_fy_id  = $this->session->get('ses_comp_fy_id');
	    $base_url        = base_url().'/'.getenv('AdminPath');
	    $comptxnmst_tbl  = $comp_id.'_comptxnmst_'.$ses_comp_fy_id;
	    $from_date       =  $_POST["from_date"];
	    $to_date         =  $_POST["to_date"];
	   
	   if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
	   if($from_date!='' || $to_date!='')
         {
	    
      
         
	    $vch_txn_conso_tbl = $comp_id.'_vhtxnconso_'.$ses_comp_fy_id;
	    $builder           = $this->db->table($vch_txn_conso_tbl); 
        $builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);	 
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
		$builder->where('voucher_type_id','11');
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
		$total_Records = $builder->countAllResults();
		
			if($pq_curPage=='0') $pq_curPage='1';
		$offset = ($pq_rPP * ($pq_curPage - 1));

            if ($offset > $total_Records)
            {        
                $pq_curPage = ceil($total_Records / $pq_rPP);
                $offset = ($pq_rPP * ($pq_curPage - 1));
            }
            
            
		
		$builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);	 
		$builder->where('voucher_type_id','11');
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
	    $builder->limit($pq_rPP,$offset);
		$result  = $builder->get()->getResultArray();
		$all_records = array();	
		if($result){
		    foreach($result as $row){
		       $voucher_txn_id   = $row['voucher_txn_id'];
		       $voucher_type_id  = $row['voucher_type_id'];
		       $comp_vch_series_id = $row['comp_vch_series_id'];
		       	$builder         = $this->db->table($comptxnmst_tbl); 
                $builder->orderBy('txn_id');    
                $builder->limit(1);
                $builder->where('voucher_txn_id', $voucher_txn_id);	
                $builder->where('master_id_type', 'acc');
                $builder->where('comp_vch_series_id', $voucher_type_id);	
        		$builder->where('comp_id', $comp_id);	
        		$result = $builder->get()->getResultArray();
        		
        		 $material_centre_info = $this->material_centre_info($comp_id,$row['mat_cent_id']);
        	    if($material_centre_info)
        	        $material_centre = $this->enc_string->nc_string($material_centre_info['mat_cent_name'],'de');
        	       else
        	       $material_centre  = '';
	       
                foreach($result as $values){		
                    
                    // $mat_cent_id =   
                    $values['bill_no']          =  $row['comp_vch_no'];
                    $values['voucher_date']     =  date("d-m-Y", strtotime($row['voucher_date']));
                    $values['material_centre']  =  $material_centre;
                    $values['voucher_type_id']  =  $voucher_type_id;
                    $values['voucher_txn_id']   =  $voucher_txn_id;
                
                    $party_name = $this->get_invoice_account_info($voucher_txn_id,$comp_vch_series_id);
                    $values['account_name']      =  $party_name;
                    $values['gstin']             = "";
                   
                     
                    $sale_account_amount    = $this->account_amount($voucher_txn_id,$comp_vch_series_id,$comp_id,$values['master_id'],$values['txn_id']); 
                    $values['sale_amount']   =  formatAmount($sale_account_amount);
                    $all_records[] = $values;
                    
        		        
    	           }
		      }
		  }  
	
		     
   }
   else{
     $total_Records=0; $all_records=array(); 
       
   }
   
    echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($all_records)."}"; 	       

  }  
   public function ajax_purchase_register_listpppp(){
        $comp_id         = $this->session->get('ses_company_id');
	    $ses_comp_fy_id  = $this->session->get('ses_comp_fy_id');
	    $base_url        = base_url().'/'.getenv('AdminPath');
	    $comptxnmst_tbl  = $comp_id.'_comptxnmst_'.$ses_comp_fy_id;
	    
	   $from_date =  $_POST["from_date"];
	   $to_date   =  $_POST["to_date"];
	   
	   if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
	   if($from_date!='' || $to_date!='')
        {
	     
	    $vch_txn_conso_tbl = $comp_id.'_vhtxnconso_'.$ses_comp_fy_id;
	    $builder           = $this->db->table($vch_txn_conso_tbl); 
        $builder->orderBy('voucher_date');     
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));		
		$builder->where('comp_id', $comp_id);	 
		$builder->where('voucher_type_id','11');
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
		$total_Records = $builder->countAllResults();
		
		if($pq_curPage=='0') $pq_curPage='1';
		$offset = ($pq_rPP * ($pq_curPage - 1));

            if ($offset > $total_Records)
            {        
                $pq_curPage = ceil($total_Records / $pq_rPP);
                $offset = ($pq_rPP * ($pq_curPage - 1));
            }
            
		
		
		$builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);	 
		$builder->where('voucher_type_id','11');
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
	    $builder->limit($pq_rPP,$offset);
		$result  = $builder->get()->getResultArray();
		$all_records = array();	
		if($result){
		    foreach($result as $row){
		       $voucher_txn_id   = $row['voucher_txn_id'];
		       $voucher_type_id  = $row['voucher_type_id'];
		       	$builder         = $this->db->table($comptxnmst_tbl); 
                $builder->orderBy('txn_id');      
                $builder->where('voucher_txn_id', $voucher_txn_id);	
                $builder->where('comp_vch_series_id', $voucher_type_id);	
        		$builder->where('comp_id', $comp_id);	
        		$result = $builder->get()->getResultArray();
                foreach($result as $values){		
                      if($values['master_id_type']=='itm'){
                        $itm_txn_tables_result =  $this->all_items_transactions($comp_id,$values['master_id'],'d');
                        
                        $itm_txn_tables_result['voucher_txn_id'] = $values['voucher_txn_id'];
                        $itm_txn_tables_result['comp_vch_series_id'] = $values['comp_vch_series_id'];
                        $itm_txn_tables_result['comp_vch_no'] = $row['comp_vch_no'];
                        
                       
        		        if($itm_txn_tables_result)
        			       $all_records[$values['txn_id']] = $itm_txn_tables_result;
        		        }
    	           }
		      }
		  }  
	

    	$final_records = array();     
	    foreach($all_records as $row){
	     
	    $material_centre_info = $this->material_centre_info($comp_id,$row['mat_cent_id']);
	    if($material_centre_info)
	        $material_centre = $this->enc_string->nc_string($material_centre_info['mat_cent_name'],'de');
	       else
	       $material_centre  = '';
	       
	       $voucher_txn_id = $row['voucher_txn_id'];
	       $comp_vch_series_id = $row['comp_vch_series_id'];
	      
	       $party_name = $this->get_invoice_account_info($voucher_txn_id,$comp_vch_series_id);
           
	       $final_records[]=array("voucher_date"=>$row['txn_date'],"bill_no"=>$row['comp_vch_no'],"account_name"=>$party_name,
	                           "material_centre"=>$material_centre,"gstin"=>"","item_amount"=>$row['item_qty']*$row['item_price'],
	                           "voucher_type_id" => $row['comp_vch_series_id'],"voucher_txn_id"=>$row['voucher_txn_id']
	                            ); 
	     
	 }	     
		     
   }
   else{
     $total_Records=0; $final_records=array(); 
       
   }
   
    echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($final_records)."}"; 	       

  }  
  
  public function account_amount($voucher_txn_id,$voucher_series_id,$comp_id,$account_id,$txn_id){
    $ses_comp_fy_id  = $this->session->get('ses_comp_fy_id');
    $acnttxnmst_tbl  = $comp_id.'_accnttxnnn_'.$account_id.'_'.$ses_comp_fy_id;  
    $builder           = $this->db->table($acnttxnmst_tbl); 
    $builder->orderBy('acc_txn_date');                
    $builder->where('comp_id', $comp_id);	 
	$builder->where('voucher_txn_id',$voucher_txn_id);    
if($this->session->get('ses_boid')!='')
	$builder->where('bo_id', $this->session->get('ses_boid'));	
    $builder->where('txn_id',$txn_id);  
    $result  = $builder->get()->getRowArray();
    if($result)
    return $result['acc_txn_amount'];
    else
    return "0";
  } 
   
  public function ajax_sale_register_list(){
        $company_sales_accounts = $this->company_sales_accounts();
        $comp_id         = $this->session->get('ses_company_id');
	    $ses_comp_fy_id  = $this->session->get('ses_comp_fy_id');
	    $base_url        = base_url().'/'.getenv('AdminPath');
	    $comptxnmst_tbl  = $comp_id.'_comptxnmst_'.$ses_comp_fy_id;
	    $from_date       =  $_POST["from_date"];
	    $to_date         =  $_POST["to_date"];
	   
	   if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
            
        if($pq_curPage==0) $pq_curPage='1';   
	   if($from_date!='' || $to_date!='')
         {
	    $vch_txn_conso_tbl = $comp_id.'_vhtxnconso_'.$ses_comp_fy_id;
	    $builder           = $this->db->table($vch_txn_conso_tbl); 
        $builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);	 
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
		$builder->where('voucher_type_id','18');
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
		$total_Records = $builder->countAllResults();
		
		if($pq_curPage=='0') $pq_curPage='1';
		$offset = ($pq_rPP * ($pq_curPage - 1));

            if ($offset > $total_Records)
            {        
                $pq_curPage = ceil($total_Records / $pq_rPP);
                $offset = ($pq_rPP * ($pq_curPage - 1));
            }
		
		
		
		$builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);	 
		$builder->where('voucher_type_id','18');
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
	    $builder->limit($pq_rPP,$offset);
		$result  = $builder->get()->getResultArray();
		$all_records = array();	
		if($result){
		    foreach($result as $row){
		       $voucher_txn_id   = $row['voucher_txn_id'];
		       $voucher_type_id  = $row['voucher_type_id'];
		       $comp_vch_series_id = $row['comp_vch_series_id'];
		       	$builder         = $this->db->table($comptxnmst_tbl); 
                $builder->orderBy('txn_id');    
                $builder->where('voucher_txn_id', $voucher_txn_id);	
                $builder->where('master_id_type', 'acc');
                $builder->where('comp_vch_series_id', $voucher_type_id);	
        		$builder->where('comp_id', $comp_id);
        		$builder->limit(1);
        		$result = $builder->get()->getResultArray();
        		
        		 $material_centre_info = $this->material_centre_info($comp_id,$row['mat_cent_id']);
        	    if($material_centre_info)
        	        $material_centre = $this->enc_string->nc_string($material_centre_info['mat_cent_name'],'de');
        	       else
        	       $material_centre  = '';
	       
	           // echo "<pre>";print_r($result);exit;
        	
                foreach($result as $values){		
                    
                    // $mat_cent_id =   
                    $values['bill_no']          =  $row['comp_vch_no'];
                    $values['voucher_date']     =  date("d-m-Y", strtotime($row['voucher_date']));
                    $values['material_centre']  =  $material_centre;
                    $values['voucher_type_id']  =  $voucher_type_id;
                    $values['voucher_txn_id']   =  $voucher_txn_id;
                
                    $party_name = $this->get_invoice_account_info($voucher_txn_id,$comp_vch_series_id);
                    $values['account_name']      =  $party_name;
                    $values['gstin']             = "";
                   
                     
                    $sale_account_amount    = $this->account_amount($voucher_txn_id,$comp_vch_series_id,$comp_id,$values['master_id'],$values['txn_id']); 
                    $values['sale_amount']   =  formatAmount($sale_account_amount);
                    $all_records[] = $values;
                    
        		        
    	           }
		      }
		  }  
	
		     
   }
   else{
     $total_Records=0; $all_records=array(); 
       
   }
   
    echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($all_records)."}"; 	       

  } 
  
 public  function get_invoice_account_info($voucher_txn_id,$comp_vch_series_id){
      $act_master_tbl =  $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');	 
      $result =  $this->db->table($act_master_tbl)->where('comp_vch_series_id',$comp_vch_series_id)
                      ->where('voucher_txn_id',$voucher_txn_id)->where('master_id_type','acc')
                      ->where('comp_id', $this->company_id)->orderBy('txn_id','ASC')->get()->getRowArray();  
      if($result){
          $account_id = $result['master_id'];
          $account_info =  $this->get_account_info($account_id); 
          return  $account_info['acc_name'];
          
      }
      else
       return "";
      
  }
  
  function company_sales_accounts(){
     $act_master_tbl =  $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');	 
	 $result =  $this->db->table($act_master_tbl)->where('acc_grp_id','9')->where('comp_id', $this->company_id)->orderBy('acc_name','ASC')->get()->getResultArray();  
    
    $all_sales_acnt = array();
    if($result){
        foreach($result as $row){
          $all_sales_acnt[$row['acc_id']]=  $row['acc_id']; 
            
        }
        
    }
   return $all_sales_acnt;
  }
  
    function company_purchase_accounts(){
     $act_master_tbl =  $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');	 
	 $result =  $this->db->table($act_master_tbl)->where('acc_grp_id','8')->where('comp_id', $this->company_id)->orderBy('acc_name','ASC')->get()->getResultArray();  
    
    $all_sales_acnt = array();
    if($result){
        foreach($result as $row){
          $all_sales_acnt[$row['acc_id']]=  $row['acc_id']; 
            
        }
        
    }
   return $all_sales_acnt;
  }
  
  

  function item_unit_info($comp_id,$unit_id){
       $item_unit_master_tbl =  $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');	 
	   return  $this->db->table($item_unit_master_tbl)->where('unit_id', $unit_id)->where('comp_id', $comp_id)->orderBy('item_unit','ASC')->get()->getRowArray();  
         
     }
  
     
  public function get_item_info($item_id){
       $comp_id = $this->company_id;
       $item_master_tbl = $comp_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
     return $this->db->table($item_master_tbl)->where('item_id', $item_id)->where('comp_id', $comp_id)->get()->getRowArray();  
   }    
   
 function all_items_transactions($comp_id,$item_id,$item_type){
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
				$builder->orderBy('item_txn_id');     
				$builder->where('comp_id', $comp_id);
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
				$builder->where('item_txn_drcr', $item_type);               
				$result = $builder->get()->getResultArray();
			
			    if($result){			
				   foreach($result as $values){	
                      $account_id    = $values['item_id'];	
                      $acc_txn_drcr  = $values['item_txn_drcr'];
                      $acc_txn_narr  = $values['item_txn_narr'];					  
					  $item_txn_date = $values['item_txn_date'];
					  $item_qty      = $values['item_txn_qty'];
					  $matrcentrid   = $values['mat_cent_id'];
					  $txn_id        = $values['txn_id'];
					  $comp_vch_series_no = $values['comp_vch_series_no'];
					   
					  $item_price   = $values['item_txn_amount'];
					  if($values['item_txn_drcr']=='d'){
						 $debit  = $values['item_txn_amount'];
						 $credit = '0.00';
					   }
					   else if($values['item_txn_drcr']=='c'){
						 $credit = $values['item_txn_amount'];
						 $debit  = '0.00';
						 }
						$list= array("mat_cent_id"=>$matrcentrid,"item_unit"=>$item_unit_name,"item_price"=>$item_price,"item_qty"=>$item_qty,"item_name"=>$item_name,"item_txn_narr"=>$acc_txn_narr,"comp_id"=>$comp_id,"txn_id"=>$txn_id,"txn_date" =>$item_txn_date,'item_id'=>$item_id,'item_txn_drcr'=>$acc_txn_drcr,'debit'=>$debit,'credit'=>$credit);
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
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
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
   
  public function financial_vouchers_list($voucher_type_id,$comp_id,$from_date,$to_date){ 	   
	    $ses_comp_fy_id  = $this->session->get('ses_comp_fy_id');
	    $base_url        = base_url().'/'.getenv('AdminPath');
	    
	    $vch_txn_conso_tbl = $comp_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	    $builder           = $this->db->table($vch_txn_conso_tbl); 
        $builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);	
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
		$builder->where('voucher_type_id', $voucher_type_id);
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
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
						  'credit'=>$transvalues['credit'],
						  'bill_no'=> $transvalues['bill_no'],
						  'narration'=> '',//$transvalues['narration']
				          );  
			        }
		        }           
		     }	 		
      return $records;
   }

   	function account_info($account_id){	 
       $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
       return $this->db->table($account_master_tbl)->where('acc_id', $account_id)->get()->getRowArray();   	   
    }

    function bill_sundry_info($id)
    {
    	$bill_sundry_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!='')		
		$result = $this->db->table($bill_sundry_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('bill_sundry_id', $id)->get()->getRowArray();
		else 
	    $result = $this->db->table($bill_sundry_tbl)->where('bill_sundry_id', $id)->get()->getRowArray();
	    return $result; 
    }
    function get_voucher_narration_info($voucher_txn_id,$narr_type,$txn_id){
		if($narr_type=='long'){
			$long_narr_tbl = $this->company_id.'_long_narrn_'.$this->session->get('ses_comp_fy_id');
			return $this->db->table($long_narr_tbl)->where('vch_txn_id',$voucher_txn_id)->get()->getRowArray();
		}
		else if($narr_type=='short'){
	        $short_narr_tbl = $this->company_id.'_short_narr_'.$this->session->get('ses_comp_fy_id');
			return $this->db->table($short_narr_tbl)->where('txn_id',$txn_id)->where('vch_txn_id',$voucher_txn_id)->get()->getRowArray();
		}		
  	}


   	public function load_payment_register($pq_curPage, $limit, $from_date, $to_date, $voucher_type_id)
    {
        $voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	    $voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	    $voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
	    $comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		$acctcrsref_tbl      =  $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
	    
	    $builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	    $builder->join($comp_txn_tbl, $comp_txn_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id');
	    $builder->select($comp_txn_tbl.'.txn_id, '.$comp_txn_tbl.'.master_id, '. $comp_txn_tbl.'.master_id_type');
	    
		$builder->where($voucher_tbl.'.comp_id', $this->company_id);
		$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);	
		if($this->session->get('ses_boid')!='')
		$builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));
		
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}
		$builder->where('(master_id_type = "acc" or master_id_type = "bsd")');
		$builder->orderBy('voucher_txn_id');
		$total_records = $builder->countAllResults();
		
		if($pq_curPage=='0') $pq_curPage='1';
		$offset = ($limit * ($pq_curPage - 1));

            if ($offset > $total_records)
            {        
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
	    $builder->select($comp_txn_tbl.'.txn_id, '.$comp_txn_tbl.'.master_id, '. $comp_txn_tbl.'.master_id_type');
	    
		$builder->where($voucher_tbl.'.comp_id', $this->company_id);
		$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);				
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}
		$builder->where('(master_id_type = "acc" or master_id_type = "bsd")');
		$builder->orderBy('voucher_date');
		$builder->orderBy('voucher_txn_id');
		$builder->limit($limit,$offset);  
		$result = $builder->get()->getResultArray();

		// echo "<pre>";print_r($result);exit;
		$data = [];
		foreach($result as $key => $value)
        {
        	$voucher_txn_id  = $value['voucher_txn_id'];
        	$comp_vch_no = $value['comp_vch_no'];
        	$account_name = '';
        	$narration = '';
        	$credit = '';
        	$debit = '';

            if($value['master_id_type'] == 'acc'){
         		$table = $this->company_id.'_accnttxnnn_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
              	$builder = $this->db->table($table)->select($table.'.*');
    			$builder->where($table.'.txn_id', $value['txn_id']);
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
              	$account = $builder->get()->getRowArray();
              	
              	if($account){
                 	if($account['acc_txn_drcr'] == 'c'){
                 		$account_info   = $this->account_info($value['master_id']);
						$account_name   = $account_info['acc_name'];
                    	$credit 		= $account['acc_txn_amount'];
                 	}
                 	if($account['acc_txn_drcr'] == 'd'){
                 		$account_info   = $this->account_info($value['master_id']);
						$account_name   = $account_info['acc_name'];
                    	$debit 		  	= $account['acc_txn_amount'];
              		}
              	}
         	}
         	if($value['master_id_type'] == 'bsd'){
				$table = $this->company_id.'_sundrytxnn_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
				$builder = $this->db->table($table);
				$builder->where('txn_id', $value['txn_id']);
				$account = $builder->get()->getRowArray();
				if($account){
					if($account['sundry_txn_drcr'] == 'c'){
						$bill_sundry_info   = $this->bill_sundry_info($value['master_id']);
						$account_name 		= $bill_sundry_info['bill_sundry_name'];
						$credit 			= $account['sundry_txn_amount'];
					}
					if($account['sundry_txn_drcr'] == 'd'){
						$bill_sundry_info   = $this->bill_sundry_info($value['master_id']);
						$account_name  	    = $bill_sundry_info['bill_sundry_name'];
						$debit  			= $account['sundry_txn_amount'];
					}
				}
         	}

         	$credit = $credit != '' ? formatAmount($credit) : '';
         	$debit = $debit != '' ? formatAmount($debit) : '';

         	$voucher_narration = $this->get_voucher_narration_info($voucher_txn_id,'short',$value['txn_id']);
         	if($voucher_narration){
         		$narration = $voucher_narration['vch_short_narr'];
         	}

        	$voucher_date         = date("d-m-Y", strtotime($value['voucher_date']));
        	$data[] = [

                'voucher_txn_id'  => $voucher_txn_id,
                'voucher_type_id' => $voucher_type_id,
                'voucher_no'      => $comp_vch_no,
                'account_name'    => $account_name,
                'voucher_date'    => $voucher_date,
                'credit'          => $credit,
                'debit'           => $debit,
                'narration'		  => $narration
            ];
			
        }
		// echo "<pre>";print_r($data);exit;
		return  [
			'totalRecords'	=> $total_records,
			'curPage'		=> $pq_curPage,
			'data'			=> $data,
		];
	}

	public function load_voucher_register($pq_curPage, $limit, $from_date, $to_date, $voucher_type_id)
    {
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
	    
		$builder->where($voucher_tbl.'.comp_id', $this->company_id);
		if($this->session->get('ses_boid')!='')
		$builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));
		$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);					
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}
		$builder->orderBy('voucher_txn_id');
		$total_records = $builder->countAllResults();
		
		if($pq_curPage=='0') $pq_curPage='1';
		$offset = ($limit * ($pq_curPage - 1));

            if ($offset > $total_records)
            {        
                $pq_curPage = ceil($total_records / $limit);
                $offset = ($limit * ($pq_curPage - 1));
            }
            
            

		$builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	    
		$builder->where($voucher_tbl.'.comp_id', $this->company_id);
		$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);				
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}
		$builder->orderBy('voucher_date');
		$builder->orderBy('voucher_txn_id');
		$builder->limit($limit,$offset);  
		$result = $builder->get()->getResultArray();

		// echo "<pre>";print_r($result);exit;
		$data = [];
		foreach($result as $key => $value)
        {
        	$voucher_txn_id  = $value['voucher_txn_id'];
        	$comp_vch_no = $value['comp_vch_no'];
        	$account_name = '';
        	$credit = '';
        	$debit = '';
        	$credit_total = 0;
        	$debit_total = 0;


            if($voucher_type_id == '9') //payment cr
        	{
        		$builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $voucher_txn_id);
                $builder->whereIn('master_id_type', ['acc','bsd']);
                $txn_result = $builder->get()->getResultArray();

                $credit_total = 0;
                $debit_total = 0;
                foreach ($txn_result as $key2 => $value2) {

                	if($value2['master_id_type'] == 'acc'){
                		$table = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
	                    $builder = $this->db->table($table)->select($table.'.*');
	        			$builder->where($table.'.txn_id', $value2['txn_id']);
						if($this->session->get('ses_boid')!='')
						$builder->where('bo_id', $this->session->get('ses_boid'));
	                    $account = $builder->get()->getRowArray();
	                    if($account){
	                        if($account['acc_txn_drcr'] == 'c'){
	                        	$account_info  = $this->account_info($value2['master_id']);
								$account_name  .= $account_info['acc_name'] . ',';
	                            $credit_total += $account['acc_txn_amount'];
	                        }
	                    }
                	}
                	if($value2['master_id_type'] == 'bsd'){

	          			$table = $this->company_id.'_sundrytxnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($table);
						$builder->where('txn_id', $value2['txn_id']);
						$account = $builder->get()->getRowArray();
						if($account){
		                    if($account['sundry_txn_drcr'] == 'c'){
		                     	$bill_sundry_info  = $this->bill_sundry_info($value2['master_id']);
		                        $credit_total += $account['sundry_txn_amount'];
		                     }
		                     if($account['sundry_txn_drcr'] == 'd'){
		                     	$bill_sundry_info  = $this->bill_sundry_info($value2['master_id']);
		                        $credit_total  += -$account['sundry_txn_amount'];
		                  	}
	                  	}
	               	}
                	
                }

                if($credit_total > 0)
                	$credit = formatAmount($credit_total);
                if($debit_total > 0)
                	$debit = formatAmount($debit_total);

                $account_name = rtrim($account_name,',');
        	}
        	if($voucher_type_id == '13') //receipt dr
        	{
        		$builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $voucher_txn_id);
                $builder->whereIn('master_id_type', ['acc','bsd']);
                $txn_result = $builder->get()->getResultArray();

                $credit_total = 0;
                $debit_total = 0;
                foreach ($txn_result as $key2 => $value2) {

                	if($value2['master_id_type'] == 'acc'){
	                	$table = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
	                    $builder = $this->db->table($table)->select($table.'.*');
	        			$builder->where($table.'.txn_id', $value2['txn_id']);
						if($this->session->get('ses_boid')!='')
							$builder->where('bo_id', $this->session->get('ses_boid'));
	                    $account = $builder->get()->getRowArray();
	                    if($account){
	                        if($account['acc_txn_drcr'] == 'd'){
	                        	$account_info  = $this->account_info($value2['master_id']);
								$account_name  .= $account_info['acc_name'] . ',';
	                            $debit_total  += $account['acc_txn_amount'];
	                        }
	                    }
	                }
	                if($value2['master_id_type'] == 'bsd'){

	          			$table = $this->company_id.'_sundrytxnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($table);
						$builder->where('txn_id', $value2['txn_id']);
						$account = $builder->get()->getRowArray();
						if($account){
		                    if($account['sundry_txn_drcr'] == 'c'){
		                     	$bill_sundry_info  = $this->bill_sundry_info($value2['master_id']);
		                        $debit_total += -$account['sundry_txn_amount'];
		                     }
		                     if($account['sundry_txn_drcr'] == 'd'){
		                     	$bill_sundry_info  = $this->bill_sundry_info($value2['master_id']);
		                        $debit_total  += $account['sundry_txn_amount'];
		                  	}
	                  	}
	               	}
                }

                if($credit_total > 0)
                	$credit = formatAmount($credit_total);
                if($debit_total > 0)
                	$debit = formatAmount($debit_total);

                $account_name = rtrim($account_name,',');
        	}
        	if($voucher_type_id == '1') //contra
        	{
        		$builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $voucher_txn_id);
                $builder->whereIn('master_id_type', ['acc','bsd']);
                $txn_result = $builder->get()->getResultArray();

                $credit_total = 0;
                $debit_total = 0;
                foreach ($txn_result as $key2 => $value2) {

                	if($value2['master_id_type'] == 'acc'){
	                	$table = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
	                    $builder = $this->db->table($table)->select($table.'.*');
	        			$builder->where($table.'.txn_id', $value2['txn_id']);
						if($this->session->get('ses_boid')!='')
							$builder->where('bo_id', $this->session->get('ses_boid'));
	                    $account = $builder->get()->getRowArray();
	                    if($account){
	                        if($account['acc_txn_drcr'] == 'c'){
	                        	$account_info  = $this->account_info($value2['master_id']);
								$account_name  .= $account_info['acc_name'] . ',';
	                            $credit_total += $account['acc_txn_amount'];
	                        }
	                        if($account['acc_txn_drcr'] == 'd'){
	                        	$account_info  = $this->account_info($value2['master_id']);
								$account_name  .= $account_info['acc_name'] . ',';
	                            $debit_total  += $account['acc_txn_amount'];
	                        }
	                    }
	                }
	                if($value2['master_id_type'] == 'bsd'){

	          			$table = $this->company_id.'_sundrytxnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($table);
						$builder->where('txn_id', $value2['txn_id']);
						$account = $builder->get()->getRowArray();
						if($account){
		                    if($account['sundry_txn_drcr'] == 'c'){
		                     	$bill_sundry_info  = $this->bill_sundry_info($value2['master_id']);
		                        $credit_total += $account['sundry_txn_amount'];
		                     }
		                     if($account['sundry_txn_drcr'] == 'd'){
		                     	$bill_sundry_info  = $this->bill_sundry_info($value2['master_id']);
		                        $debit_total  += $account['sundry_txn_amount'];
		                  	}
	                  	}
	               	}
                }

                if($credit_total > 0)
                	$credit = formatAmount($credit_total);
                if($debit_total > 0)
                	$debit = formatAmount($debit_total);

                $account_name = rtrim($account_name,',');
        	}
        	if($voucher_type_id == '5') //journal
        	{
				$builder = $this->db->table($comp_txn_tbl);
				$builder->orderBy('txn_id');
				$builder->where('voucher_txn_id', $voucher_txn_id);
				$builder->whereIn('master_id_type', ['acc','bsd','aco','bso']);
				$txn_result = $builder->get()->getResultArray();

				$credit_total = 0;
				$debit_total = 0;
             	foreach ($txn_result as $key2 => $value2) {

	             	if($value['voucher_tag'] == 'OPTIONL')
	             	{
	             		if($value2['master_id_type'] == 'aco'){

	             			$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
		                  	$builder = $this->db->table($table)->select($table.'.*');
		        			$builder->where($table.'.txn_id', $value2['txn_id']);
							if($this->session->get('ses_boid')!='')
							$builder->where('bo_id', $this->session->get('ses_boid'));
		                  	$account = $builder->get()->getRowArray();
			                if($account){
			                    if($account['acc_oth_txn_drcr'] == 'c'){
			                     	$account_info  = $this->account_info($value2['master_id']);
									$account_name  .= $account_info['acc_name'] . ',';
			                        $credit_total += $account['acc_oth_txn_amount'];
			                    }
			                    if($account['acc_oth_txn_drcr'] == 'd'){
			                     	$account_info  = $this->account_info($value2['master_id']);
									$account_name  .= $account_info['acc_name'] . ',';
			                        $debit_total  += $account['acc_oth_txn_amount'];
			                  	}
			                }
	             		}
	             		if($value2['master_id_type'] == 'bso'){

	             			$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
		                  	$builder = $this->db->table($table)->select($table.'.*');
		        			$builder->where($table.'.txn_id', $value2['txn_id']);
			                $account = $builder->get()->getRowArray();
			                if($account){
			                    if($account['acc_oth_txn_drcr'] == 'c'){
			                     	$account_info  = $this->account_info($value2['master_id']);
											$account_name  .= $account_info['acc_name'] . ',';
			                        $credit_total += $account['acc_oth_txn_amount'];
			                    }
			                    if($account['acc_oth_txn_drcr'] == 'd'){
			                     	$account_info  = $this->account_info($value2['master_id']);
											$account_name  .= $account_info['acc_name'] . ',';
			                        $debit_total  += $account['acc_oth_txn_amount'];
			                  	}
			                }
	             		}
	             	}
	             	else
	             	{
	             		if($value2['master_id_type'] == 'acc'){

		          			$table = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
		                  	$builder = $this->db->table($table)->select($table.'.*');
		        			$builder->where($table.'.txn_id', $value2['txn_id']);
							if($this->session->get('ses_boid')!='')
								$builder->where('bo_id', $this->session->get('ses_boid'));		
		                  	$account = $builder->get()->getRowArray();
			                if($account){
			                    if($account['acc_txn_drcr'] == 'c'){
			                     	$account_info  = $this->account_info($value2['master_id']);
											$account_name  .= $account_info['acc_name'] . ',';
			                        $credit_total += $account['acc_txn_amount'];
			                    }
			                    if($account['acc_txn_drcr'] == 'd'){
			                     	$account_info  = $this->account_info($value2['master_id']);
											$account_name  .= $account_info['acc_name'] . ',';
			                        $debit_total  += $account['acc_txn_amount'];
			                  	}
			                }
		             	}
		             	if($value2['master_id_type'] == 'bsd'){

		          			$table = $this->company_id.'_sundrytxnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
							$builder = $this->db->table($table);
							$builder->where('txn_id', $value2['txn_id']);
							$account = $builder->get()->getRowArray();
							if($account){
			                    if($account['sundry_txn_drcr'] == 'c'){
			                     	$bill_sundry_info  = $this->bill_sundry_info($value2['master_id']);
									$account_name  .= $bill_sundry_info['bill_sundry_name'] . ',';
			                        $credit_total += $account['sundry_txn_amount'];
			                     }
			                     if($account['sundry_txn_drcr'] == 'd'){
			                     	$bill_sundry_info  = $this->bill_sundry_info($value2['master_id']);
									$account_name  .= $bill_sundry_info['bill_sundry_name'] . ',';
			                        $debit_total  += $account['sundry_txn_amount'];
			                  	}
		                  	}
		               	}
	             	}
	             	
	            }

				if($credit_total > 0)
					$credit = formatAmount($credit_total);
				if($debit_total > 0)
					$debit = formatAmount($debit_total);

				$account_name = rtrim($account_name,',');
        	}

         	$voucher_narration = $this->get_voucher_narration_info($voucher_txn_id,'long',0);
         	if($voucher_narration){
         		$narration = $voucher_narration['vch_narr'];
         	}

        	$voucher_date         = date("d-m-Y", strtotime($value['voucher_date']));
        	$data[] = [

                'voucher_txn_id'  => $voucher_txn_id,
                'voucher_type_id' => $voucher_type_id,
                'voucher_no'      => $comp_vch_no,
                'account_name'    => $account_name,
                'voucher_date'    => $voucher_date,
                'credit'          => $credit,
                'debit'           => $debit,
                'credit_total'    => $credit_total,
                'debit_total'     => $debit_total,
                'narration'		  => $narration
            ];
			
        }
		// echo "<pre>";print_r($data);exit;
		return  [
			'totalRecords'	=> $total_records,
			'curPage'		=> $pq_curPage,
			'data'			=> $data,
		];
	}

   	public function load_memorandum_register($pq_curPage, $limit, $from_date, $to_date, $voucher_type_id)
    {
        $voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	    $voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	    $voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
	
		$account_memo_tbl = $this->company_id.'_memotxnnnn_'.$this->session->get('ses_comp_fy_id');
	    
	    $builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	    $builder->join($account_memo_tbl, $account_memo_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id');
	    $builder->select($account_memo_tbl.'.acc_type, '.$account_memo_tbl.'.acc_id, '. $account_memo_tbl.'.acc_txn_drcr, '. $account_memo_tbl.'.acc_bal');
	    
		$builder->where($voucher_tbl.'.comp_id', $this->company_id);
		if($this->session->get('ses_boid')!='')
		$builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));

		$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);					
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}
		$builder->orderBy('voucher_txn_id');
		$total_records = $builder->countAllResults();
		
		if($pq_curPage=='0') $pq_curPage='1';
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
	    $builder->join($account_memo_tbl, $account_memo_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id');
	    $builder->select($account_memo_tbl.'.acc_type, '.$account_memo_tbl.'.acc_id, '. $account_memo_tbl.'.acc_txn_drcr, '. $account_memo_tbl.'.acc_bal, '. $account_memo_tbl.'.acc_txn_amount, '. $account_memo_tbl.'.acc_txn_id');
	    
		$builder->where($voucher_tbl.'.comp_id', $this->company_id);
		$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);				
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}
		$builder->orderBy('voucher_date');
		$builder->orderBy('voucher_txn_id');
		$builder->limit($limit,$offset);  
		$result = $builder->get()->getResultArray();

		$data = [];
		foreach($result as $key => $value)
        {
        	$voucher_txn_id  = $value['voucher_txn_id'];
        	$comp_vch_no = $value['comp_vch_no'];
        	$account_name = '';
        	$narration = '';
        	$credit = '';
        	$debit = '';

            if($value['acc_type'] == 'acc'){
    			$account_info   = $this->account_info($value['acc_id']);
				$account_name   = $account_info['acc_name'];
         	}
         	if($value['acc_type'] == 'bsd'){
         		$bill_sundry_info   = $this->bill_sundry_info($value['master_id']);
				$account_name 		= $bill_sundry_info['bill_sundry_name'];
         	}

         	if($value['acc_txn_drcr'] == 'c')
            	$credit 		= formatAmount($value['acc_txn_amount']);
         	
         	if($value['acc_txn_drcr'] == 'd')
            	$debit 		  	= formatAmount($value['acc_txn_amount']);

         	$voucher_narration = $this->get_voucher_narration_info($voucher_txn_id,'short',$value['acc_txn_id']);
         	if($voucher_narration){
         		$narration = $voucher_narration['vch_short_narr'];
         	}

        	$voucher_date         = date("d-m-Y", strtotime($value['voucher_date']));
        	$data[] = [

                'voucher_txn_id'  => $voucher_txn_id,
                'voucher_type_id' => $voucher_type_id,
                'voucher_no'      => $comp_vch_no,
                'account_name'    => $account_name,
                'voucher_date'    => $voucher_date,
                'credit'          => $credit,
                'debit'           => $debit,
                'narration'		  => $narration
            ];	
        }
		// echo "<pre>";print_r($data);exit;
		return  [
			'totalRecords'	=> $total_records,
			'curPage'		=> $pq_curPage,
			'data'			=> $data,
		];	
	}
	
	function GetPackingLevel($list_id){
	  $cupackingn_tbl  = $this->company_id.'_cupackingn_'.$this->session->get('ses_comp_fy_id'); 	
	  $builder         = $this->db->table($cupackingn_tbl); 
	  $builder->where('list_id',$list_id);
	  $builder->orderBy('packing_id','DESC');
	  $builder->limit(1);
	  return $builder->get()->getRowArray(); 
  } 

	public function load_consignment_packing_register($pq_curPage, $limit, $from_date, $to_date)
    {
        $voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	    $voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	    $voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
	
		$pcklistmst_tbl = $this->company_id.'_pcklistmst_'.$this->session->get('ses_comp_fy_id');
		$cupackingn_tbl = $this->company_id.'_cupackingn_'.$this->session->get('ses_comp_fy_id');
		
		$pcklistqty_tbl = $this->company_id.'_pcklistqty_'.$this->session->get('ses_comp_fy_id');
		$listpacked_tbl = $this->company_id.'_listpacked_'.$this->session->get('ses_comp_fy_id');
	    
	    $builder = $this->db->table($pcklistmst_tbl);	    
		$builder->join($voucher_tbl,$voucher_tbl.'.voucher_tag='.$pcklistmst_tbl.'.list_id');
		$builder->where($pcklistmst_tbl.'.comp_id', $this->company_id);					
		if($from_date!=''){
			$builder->where($voucher_tbl.'.voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where($voucher_tbl.'.voucher_date <=', $to_date);
		}
		if($this->session->get('ses_boid')!='')
		$builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));
		$builder->orderBy($pcklistmst_tbl.'.list_id');
		$total_records = $builder->countAllResults();
		
		if($pq_curPage=='0') $pq_curPage='1';
		$offset = ($limit * ($pq_curPage - 1));
        if ($offset > $total_records){        
            $pq_curPage = ceil($total_records / $limit);
            $offset = ($limit * ($pq_curPage - 1));
        }            

		$builder = $this->db->table($pcklistmst_tbl);	    
		$builder->join($voucher_tbl,$voucher_tbl.'.voucher_tag='.$pcklistmst_tbl.'.list_id');
		$builder->where($pcklistmst_tbl.'.comp_id', $this->company_id);					
		if($from_date!=''){
			$builder->where($voucher_tbl.'.voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where($voucher_tbl.'.voucher_date <=', $to_date);
		}
		$builder->orderBy($pcklistmst_tbl.'.list_id');
		$builder->limit($limit,$offset);  
		$result = $builder->get()->getResultArray();

		$data = [];
		foreach($result as $key => $value)
        {
			
        	$builder = $this->db->table($cupackingn_tbl);
		    $builder->select('COUNT(cu_id) as total_cu,MAX(list_level_id) as max_level');
			$builder->where('list_id', $value['list_id']);
			$cupackingn    = $builder->get()->getRowArray();
			$total_cu      = $cupackingn['total_cu'] ?? 0;
			$max_level     = $cupackingn['max_level'] ?? 0;
        	$voucher_date  = date("d-m-Y", strtotime($value['voucher_date']));

			// count qty packed or unpacked
			$builder1 = $this->db->table($pcklistqty_tbl);
		    $builder1->select('SUM(item_qty_available) as total_unpacked');
			$builder1->where('list_id', $value['list_id']);
			$pcklistqty_row = $builder1->get()->getRowArray();
			$total_unpacked = $pcklistqty_row['total_unpacked'] ?? 0;
			
			$builder2 = $this->db->table($listpacked_tbl);
		    $builder2->select('SUM(item_qty_packed) as total_packed');
			$builder2->where('list_id', $value['list_id']);
			$listpacked_row = $builder2->get()->getRowArray();
			$total_packed   = $listpacked_row['total_packed'] ?? 0;
			
            // get packing max level 	
			$packing_level = $this->GetPackingLevel($value['list_id']); 			
            $list_level_id = $packing_level['list_level_id'];
        	$data[] = [
                'list_id'  		  => $value['list_id'],
                'list_name' 	  => $value['list_name'],
                'voucher_no'      => $value['comp_vch_no'],
                'no_of_level'     => $max_level,
                'voucher_date'    => $voucher_date,
                'total_cu'        => $total_cu,
                'total_packed'    => $total_packed,
                'total_unpacked'  => $total_unpacked,
				'list_level_id'   => $list_level_id,
                'status'  		  => '',
               ];	
        }
		// echo "<pre>";print_r($data);exit;
		return  [
			'totalRecords'	=> $total_records,
			'curPage'		=> $pq_curPage,
			'data'			=> $data,
		];	
	}

	public function load_optional_register($pq_curPage, $limit, $from_date, $to_date, $voucher_type_id)
    {
        $voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	    $voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	    $voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
	    $comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		$acctcrsref_tbl      =  $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
	    
	    $builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	    $builder->join($comp_txn_tbl, $comp_txn_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id  AND (master_id_type = "acc" or master_id_type = "bsd")', 'left');
	    $builder->select($comp_txn_tbl.'.txn_id, '.$comp_txn_tbl.'.master_id, '. $comp_txn_tbl.'.master_id_type');
	    
		$builder->where($voucher_tbl.'.comp_id', $this->company_id);
		if($this->session->get('ses_boid')!='')
		$builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));

		if($voucher_type_id != 0)
			$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);	

		$builder->where($voucher_tbl.'.voucher_tag', 'OPTIONL');				
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}

		$builder->orderBy('voucher_txn_id');
		$total_records = $builder->countAllResults();
		
		if($pq_curPage=='0') $pq_curPage='1';
		$offset = ($limit * ($pq_curPage - 1));

            if ($offset > $total_records)
            {        
                $pq_curPage = ceil($total_records / $limit);
                $offset = ($limit * ($pq_curPage - 1));
            }

		$builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	    $builder->join($comp_txn_tbl, $comp_txn_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id AND (master_id_type = "acc" or master_id_type = "bsd")', 'left');
	    $builder->select($comp_txn_tbl.'.txn_id, '.$comp_txn_tbl.'.master_id, '. $comp_txn_tbl.'.master_id_type');
	    
		$builder->where($voucher_tbl.'.comp_id', $this->company_id);
		if($voucher_type_id != 0)
			$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);
		$builder->where($voucher_tbl.'.voucher_tag', 'OPTIONL');
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}

		$builder->orderBy('voucher_date');
		$builder->orderBy('voucher_txn_id');
		$builder->limit($limit,$offset);  
		$result = $builder->get()->getResultArray();

		$data = [];
		foreach($result as $key => $value)
        {
        	$voucher_txn_id  = $value['voucher_txn_id'];
        	$voucher_type_id  = $value['voucher_type_id'];
        	$voucher_type  = $value['comp_vch_type'];
        	$comp_vch_no = $value['comp_vch_no'];
        	$account_name = 'Self';
        	$narration = '';
        	$credit = '';
        	$debit = '';

            if($value['master_id_type'] == 'acc'){

         		$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
              	$builder = $this->db->table($table)->select($table.'.*');
    			$builder->where($table.'.txn_id', $value['txn_id']);
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
              	$account = $builder->get()->getRowArray();
              	if($account){

                 	if($account['acc_oth_txn_drcr'] == 'c'){
                 		$account_info   = $this->account_info($value['master_id']);
						$account_name   = $account_info['acc_name'];
                    	$credit 		= $account['acc_oth_txn_amount'];
                 	}
                 	if($account['acc_oth_txn_drcr'] == 'd'){
                 		$account_info   = $this->account_info($value['master_id']);
						$account_name   = $account_info['acc_name'];
                    	$debit 		  	= $account['acc_oth_txn_amount'];
              		}
              	}
         	}
         	if($value['master_id_type'] == 'bsd'){
				$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
				$builder = $this->db->table($table);
				$builder->where('txn_id', $value['txn_id']);
				if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
				$account = $builder->get()->getRowArray();
				if($account){
					if($account['acc_oth_txn_drcr'] == 'c'){
						$bill_sundry_info   = $this->bill_sundry_info($value['master_id']);
						$account_name 		= $bill_sundry_info['bill_sundry_name'];
						$credit 			= $account['acc_oth_txn_drcr'];
					}
					if($account['acc_oth_txn_drcr'] == 'd'){
						$bill_sundry_info   = $this->bill_sundry_info($value['master_id']);
						$account_name  	    = $bill_sundry_info['bill_sundry_name'];
						$debit  			= $account['acc_oth_txn_drcr'];
					}
				}
         	}

         	$credit = $credit != '' ? formatAmount($credit) : '';
         	$debit = $debit != '' ? formatAmount($debit) : '';

         	$voucher_narration = $this->get_voucher_narration_info($voucher_txn_id,'short',$value['txn_id']);
         	if($voucher_narration){
         		$narration = $voucher_narration['vch_short_narr'];
         	}

        	$voucher_date         = date("d-m-Y", strtotime($value['voucher_date']));
        	$data[] = [

                'voucher_txn_id'  => $voucher_txn_id,
                'voucher_type_id' => $voucher_type_id,
                'voucher_type' 	  => $voucher_type,
                'voucher_no'      => $comp_vch_no,
                'account_name'    => $account_name,
                'voucher_date'    => $voucher_date,
                'credit'          => $credit,
                'debit'           => $debit,
                'narration'		  => $narration
            ];
			
        }
		return  [
			'totalRecords'	=> $total_records,
			'curPage'		=> $pq_curPage,
			'data'			=> $data,
		];
		
	}
   function get_unit_name($unit_id){	 
	 $item_unit_master_tbl = $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');
	 $item_unit = $this->db->table($item_unit_master_tbl)->where('unit_id', $unit_id)->where('comp_id', $this->company_id)->get()->getRowArray();
	 return $this->enc_string->nc_string($item_unit['item_unit'],'de');
    }
    
     public function ajax_material_issue_register_list(){
        $company_sales_accounts = $this->company_sales_accounts();
        $comp_id         = $this->session->get('ses_company_id');
	    $ses_comp_fy_id  = $this->session->get('ses_comp_fy_id');
	    $base_url        = base_url().'/'.getenv('AdminPath');
	    $comptxnmst_tbl  = $comp_id.'_comptxnmst_'.$ses_comp_fy_id;
	    $from_date       =  $_POST["from_date"];
	    $to_date         =  $_POST["to_date"];
	   
	   if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
	   if($from_date!='' || $to_date!='')
         {
	    
        
         
	    $vch_txn_conso_tbl = $comp_id.'_vhtxnconso_'.$ses_comp_fy_id;
	    $builder = $this->db->table($vch_txn_conso_tbl); 
        $builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);	 
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
		$builder->where('voucher_type_id','7');
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
		$total_Records = $builder->countAllResults();
		
		if($pq_curPage=='0') $pq_curPage='1';
		$offset = ($pq_rPP * ($pq_curPage - 1));

            if ($offset > $total_Records)
            {        
                $pq_curPage = ceil($total_Records / $pq_rPP);
                $offset = ($pq_rPP * ($pq_curPage - 1));
            }
            
		
		$builder = $this->db->table($vch_txn_conso_tbl);                
		$builder->where('comp_id', $comp_id);	 
		$builder->where('voucher_type_id','7');
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
		$builder->orderBy('voucher_date');
		$builder->orderBy('voucher_txn_id');
	    $builder->limit($pq_rPP,$offset);
		$result  = $builder->get()->getResultArray();
		
		
		$all_records = array();	
		if($result){
		    foreach($result as $row){
		       $voucher_txn_id   = $row['voucher_txn_id'];
		       $voucher_type_id  = $row['voucher_type_id'];
		       $comp_vch_series_id = $row['comp_vch_series_id'];
		       
		       	$builder = $this->db->table($comptxnmst_tbl); 
                $builder->orderBy('txn_id');    
                $builder->limit(1);
                $builder->where('voucher_txn_id', $voucher_txn_id);	
                $builder->where('master_id_type', 'acc');
                $builder->where('comp_vch_series_id', $voucher_type_id);	
        		$builder->where('comp_id', $comp_id);	
        		$party_data = $builder->get()->getRowArray();
        		
        		$account_name = 'Self';
        		if($party_data)
    		    {
        		    $account_info = $this->get_account_info($party_data['master_id']);     
        		    if(isset($account_info['acc_name'])){
        		        $account_name  = $account_info['acc_name'];
        		    }
    		    }
        		
        		 $material_centre_info = $this->material_centre_info($comp_id,$row['mat_cent_id']);
        	     if($material_centre_info)
        	        $material_centre = $this->enc_string->nc_string($material_centre_info['mat_cent_name'],'de');
        	     else
        	        $material_centre  = '';     
	       
	            $item_data = $this->db->table($comptxnmst_tbl)
                                            ->select('txn_id, master_id')
                                            ->where('voucher_txn_id', $voucher_txn_id)
                                            ->where('comp_vch_series_id', $voucher_type_id)
                                            ->where('master_id_type', 'itm')
                                            ->where('comp_id', $comp_id)
                                            ->get()->getResultArray();
                if($item_data)
    		    {
    		        foreach($item_data as $key2 => $value2)
    		        {
    		            
    		            $item_info = $this->get_item_info($value2['master_id']);
                        $item_name = $item_info['item_name'];
                        $item_unit = $this->get_unit_name($item_info['item_unit']);
                        
                        
    		            $item_txn_table =  $this->company_id.'_itemtxnnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						if($this->session->get('ses_boid')!=''){
							$single_item_data = $this->db->table($item_txn_table)
                                            ->select('*')
                                            ->where('txn_id', $value2['txn_id'])//2
											->where('bo_id', $this->session->get('ses_boid'))
                                            ->get()->getRowArray();
						}
						else{
						$single_item_data = $this->db->table($item_txn_table)
                                            ->select('*')
                                            ->where('txn_id', $value2['txn_id'])//2
                                            ->get()->getRowArray();
						}
 
                        $quantity = 0;
                        if($single_item_data['item_txn_drcr']=='c') // Sales
						 {
						    $quantity = $single_item_data['item_txn_qty'];
						 }
					     
						 
						 $price = ($single_item_data['item_txn_amount'] / $single_item_data['item_txn_qty']);
						 $amount = $single_item_data['item_txn_amount'];
						 $short_narration = $single_item_data['item_txn_narr'];
						 
						$all_records[] = [
						    'voucher_txn_id'            => $row['voucher_txn_id'],
						    'voucher_type_id'           => $row['voucher_type_id'],
						    'comp_vch_series_id'        => $row['comp_vch_series_id'],
						    'voucher_date'              => date("d-m-Y", strtotime($row['voucher_date'])),
						    'material_centre'           => $material_centre,
						    'comp_vch_no'               => $row['comp_vch_no'],
						    'account_name'              => $account_name,
						    'item_name'                 => $item_name,
						    'item_unit'                 => $item_unit,
						    'quantity'                  => $quantity,
						    'price'                     => formatAmount($price),
						    'amount'                    => formatAmount($amount),
						    'short_narration'           => $short_narration
						    ];

                        
    		        }
    		    }
        	
		      }
		  }  
	
// 		echo "<pre>";print_r($all_records);exit;     
  }
  else{
     $total_Records=0; $all_records=array(); 
       
  }
   
    echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($all_records)."}"; 	       

  }
  
  
  public function ajax_inward_challan_due_list(){
        $company_sales_accounts = $this->company_sales_accounts();
        $comp_id         =  $this->session->get('ses_company_id');
	    $ses_comp_fy_id  =  $this->session->get('ses_comp_fy_id');
	    $base_url        =  base_url().'/'.getenv('AdminPath');
	    $comptxnmst_tbl  =  $comp_id.'_comptxnmst_'.$ses_comp_fy_id;
	    $from_date       =  $_POST["from_date"];
	    $to_date         =  $_POST["to_date"];
	   
	   if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
	   if($from_date!='' || $to_date!='')
         {
	    
         
	    $vch_txn_conso_tbl = $comp_id.'_vhtxnconso_'.$ses_comp_fy_id;
	    $builder           = $this->db->table($vch_txn_conso_tbl); 
        $builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));		
		$builder->where('(voucher_tag LIKE "PESIDEF%" OR voucher_tag LIKE "ICEPDEF%")');
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
		$total_Records = $builder->countAllResults();
			if($pq_curPage=='0') $pq_curPage='1';
		$offset = ($pq_rPP * ($pq_curPage - 1));

            if ($offset > $total_Records)
            {        
                $pq_curPage = ceil($total_Records / $pq_rPP);
                $offset = ($pq_rPP * ($pq_curPage - 1));
            }
		
		
		$builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);	 
		$builder->where('(voucher_tag LIKE "PESIDEF%" OR voucher_tag LIKE "ICEPDEF%")');
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
	    $builder->limit($pq_rPP,$offset);
		$result  = $builder->get()->getResultArray();
		
		$all_records = array();	
		if($result){
		    foreach($result as $row){
		       $voucher_txn_id   = $row['voucher_txn_id'];
		       $voucher_type_id  = $row['voucher_type_id'];
		       $comp_vch_series_id = $row['comp_vch_series_id'];
		       
		       	$builder = $this->db->table($comptxnmst_tbl); 
                $builder->orderBy('txn_id');    
                $builder->limit(1);
                $builder->where('voucher_txn_id', $voucher_txn_id);	
                $builder->where('master_id_type', 'acc');
                $builder->where('comp_vch_series_id', $comp_vch_series_id);	
        		$builder->where('comp_id', $comp_id);	
        		$party_data = $builder->get()->getRowArray();
        		//echo $this->db->GetLastquery();
        		$account_name = 'Self';
        		if($party_data)
    		    {
        		    $account_info = $this->get_account_info($party_data['master_id']);     
        		    if(isset($account_info['acc_name'])){
        		        $account_name  = $account_info['acc_name'];
        		    }
    		    }
        		
        		 $material_centre_info = $this->material_centre_info($comp_id,$row['mat_cent_id']);
        	     if($material_centre_info)
        	        $material_centre = $this->enc_string->nc_string($material_centre_info['mat_cent_name'],'de');
        	     else
        	        $material_centre  = '';     
	       
	            $item_data = $this->db->table($comptxnmst_tbl)
                                            ->select('txn_id, master_id')
                                            ->where('voucher_txn_id', $voucher_txn_id)
                                            ->where('comp_vch_series_id', $comp_vch_series_id)
                                            ->where('master_id_type', 'itm')
                                            ->where('comp_id', $comp_id)
                                            ->get()->getResultArray();
                if($item_data)
    		    {
    		        foreach($item_data as $key2 => $value2)
    		        {
    		            
    		            $item_info = $this->get_item_info($value2['master_id']);
                        $item_name = $item_info['item_name'];
                        $item_unit = $this->get_unit_name($item_info['item_unit']);
                        
                        
    		            $item_txn_table =  $this->company_id.'_itemtxnnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
    		            
						if($this->session->get('ses_boid')!=''){
						  $single_item_data = $this->db->table($item_txn_table)
                                            ->select('*')
                                            ->where('txn_id', $value2['txn_id'])
											->where('bo_id', $this->session->get('ses_boid'))
                                            ->get()->getRowArray();
											
						
						}
						else{
						$single_item_data = $this->db->table($item_txn_table)
                                            ->select('*')
                                            ->where('txn_id', $value2['txn_id'])
                                            ->get()->getRowArray();
						}
 
                        $quantity = 0;
                        if($single_item_data['item_txn_drcr']=='d') // Purchase
						 {
						    $quantity = $single_item_data['item_txn_qty'];
						 }
					     
						 
						 $price  = $single_item_data['item_txn_amount'];
						 $amount = $single_item_data['item_txn_amount'];
						 $short_narration = $single_item_data['item_txn_narr'];
						 
						$all_records[] = [
						    'voucher_txn_id'            => $row['voucher_txn_id'],
						    'voucher_type_id'           => $row['voucher_type_id'],
						    'comp_vch_series_id'        => $row['comp_vch_series_id'],
						    'voucher_date'              => date("d-m-Y", strtotime($row['voucher_date'])),
						    'material_centre'           => $material_centre,
						    'comp_vch_no'               => $row['comp_vch_no'],
						    'account_name'              => $account_name,
						    'item_name'                 => $item_name,
						    'item_unit'                 => $item_unit,
						    'quantity'                  => $quantity,
						    'price'                     => $price,
						    'amount'                    => $amount,
						    'short_narration'           => $short_narration
						    ];

                        
    		        }
    		    }
        	
		      }
		  }  
	
// 		echo "<pre>";print_r($all_records);exit;     
  }
  else{
     $total_Records=0; $all_records=array(); 
       
  }
   
    echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($all_records)."}"; 	       

  }
  
  
    public function ajax_material_receipt_register_list(){
        $company_sales_accounts = $this->company_sales_accounts();
        $comp_id         = $this->session->get('ses_company_id');
	    $ses_comp_fy_id  = $this->session->get('ses_comp_fy_id');
	    $base_url        = base_url().'/'.getenv('AdminPath');
	    $comptxnmst_tbl  = $comp_id.'_comptxnmst_'.$ses_comp_fy_id;
	    $from_date       =  $_POST["from_date"];
	    $to_date         =  $_POST["to_date"];
	   
	   if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
	   if($from_date!='' || $to_date!='')
         {
	  
	    $vch_txn_conso_tbl = $comp_id.'_vhtxnconso_'.$ses_comp_fy_id;
	    $builder           = $this->db->table($vch_txn_conso_tbl); 
        $builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);	
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));		
		$builder->where('voucher_type_id','6');
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
		$total_Records = $builder->countAllResults();
		
		if($pq_curPage=='0') $pq_curPage='1';
		$offset = ($pq_rPP * ($pq_curPage - 1));

            if ($offset > $total_Records)
            {        
                $pq_curPage = ceil($total_Records / $pq_rPP);
                $offset = ($pq_rPP * ($pq_curPage - 1));
            }
            
            
		
		$builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);	 
		$builder->where('voucher_type_id','6');
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
	    $builder->limit($pq_rPP,$offset);
		$result  = $builder->get()->getResultArray();
		
		$all_records = array();	
		if($result){
		    foreach($result as $row){
		       $voucher_txn_id   = $row['voucher_txn_id'];
		       $voucher_type_id  = $row['voucher_type_id'];
		       $comp_vch_series_id = $row['comp_vch_series_id'];
		       
		       	$builder = $this->db->table($comptxnmst_tbl); 
                $builder->orderBy('txn_id');    
                $builder->limit(1);
                $builder->where('voucher_txn_id', $voucher_txn_id);	
                $builder->where('master_id_type', 'acc');
                $builder->where('comp_vch_series_id', $voucher_type_id);	
        		$builder->where('comp_id', $comp_id);	
        		$party_data = $builder->get()->getRowArray();
        		
        		$account_name = 'Self';
        		if($party_data)
    		    {
        		    $account_info = $this->get_account_info($party_data['master_id']);     
        		    if(isset($account_info['acc_name'])){
        		        $account_name  = $account_info['acc_name'];
        		    }
    		    }
        		
        		 $material_centre_info = $this->material_centre_info($comp_id,$row['mat_cent_id']);
        	     if($material_centre_info)
        	        $material_centre = $this->enc_string->nc_string($material_centre_info['mat_cent_name'],'de');
        	     else
        	        $material_centre  = '';     
	       
	            $item_data = $this->db->table($comptxnmst_tbl)
                                            ->select('txn_id, master_id')
                                            ->where('voucher_txn_id', $voucher_txn_id)
                                            ->where('comp_vch_series_id', $voucher_type_id)
                                            ->where('master_id_type', 'itm')
                                            ->where('comp_id', $comp_id)
                                            ->get()->getResultArray();
                if($item_data)
    		    {
    		        foreach($item_data as $key2 => $value2)
    		        {
    		            
    		            $item_info = $this->get_item_info($value2['master_id']);
                        $item_name = $item_info['item_name'];
                        $item_unit = $this->get_unit_name($item_info['item_unit']);
                        
                        
    		            $item_txn_table =  $this->company_id.'_itemtxnnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
    		            
						if($this->session->get('ses_boid')!=''){
						 $single_item_data = $this->db->table($item_txn_table)
                                            ->select('*')
                                            ->where('txn_id', $value2['txn_id'])
											->where('bo_id', $this->session->get('ses_boid'))
                                            ->get()->getRowArray();
						}
						else{
						$single_item_data = $this->db->table($item_txn_table)
                                            ->select('*')
                                            ->where('txn_id', $value2['txn_id'])
                                            ->get()->getRowArray();
						}
						
						
						
						
 
                        $quantity = 0;
                        if($single_item_data['item_txn_drcr']=='d') // Purchase
						 {
						    $quantity = $single_item_data['item_txn_qty'];
						 }
					     
						 
						 $price = ($single_item_data['item_txn_amount'] / $single_item_data['item_txn_qty']);
						 $amount = $single_item_data['item_txn_amount'];
						 $short_narration = $single_item_data['item_txn_narr'];
						 
						$all_records[] = [
						    'voucher_txn_id'            => $row['voucher_txn_id'],
						    'voucher_type_id'           => $row['voucher_type_id'],
						    'comp_vch_series_id'        => $row['comp_vch_series_id'],
						    'voucher_date'              => date("d-m-Y", strtotime($row['voucher_date'])),
						    'material_centre'           => $material_centre,
						    'comp_vch_no'               => $row['comp_vch_no'],
						    'account_name'              => $account_name,
						    'item_name'                 => $item_name,
						    'item_unit'                 => $item_unit,
						    'quantity'                  => $quantity,
						    'price'                     => formatAmount($price),
						    'amount'                    => formatAmount($amount),
						    'short_narration'           => $short_narration
						    ];

                        
    		        }
    		    }
        	
		      }
		  }  
	
// 		echo "<pre>";print_r($all_records);exit;     
  }
  else{
     $total_Records=0; $all_records=array(); 
       
  }
   
    echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($all_records)."}"; 	       

  }
  
  public function ajax_purchase_return_register_list(){
        $company_sales_accounts = $this->company_sales_accounts();
        $comp_id         = $this->session->get('ses_company_id');
	    $ses_comp_fy_id  = $this->session->get('ses_comp_fy_id');
	    $base_url        = base_url().'/'.getenv('AdminPath');
	    $comptxnmst_tbl  = $comp_id.'_comptxnmst_'.$ses_comp_fy_id;
	    $from_date       =  $_POST["from_date"];
	    $to_date         =  $_POST["to_date"];
	   
	   if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
	   if($from_date!='' || $to_date!='')
         {
	    
	    $vch_txn_conso_tbl = $comp_id.'_vhtxnconso_'.$ses_comp_fy_id;
	    $builder           = $this->db->table($vch_txn_conso_tbl); 
        $builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));		
		$builder->where('voucher_type_id','3');
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
		$total_Records = $builder->countAllResults();
		
		if($pq_curPage=='0') $pq_curPage='1';
		$offset = ($pq_rPP * ($pq_curPage - 1));

            if ($offset > $total_Records)
            {        
                $pq_curPage = ceil($total_Records / $pq_rPP);
                $offset = ($pq_rPP * ($pq_curPage - 1));
            }
		
		
		$builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);	 
		$builder->where('voucher_type_id','3');
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
	    $builder->limit($pq_rPP,$offset);
		$result  = $builder->get()->getResultArray();
		$all_records = array();	
		if($result){
		    foreach($result as $row){
		       $voucher_txn_id   = $row['voucher_txn_id'];
		       $voucher_type_id  = $row['voucher_type_id'];
		       $comp_vch_series_id = $row['comp_vch_series_id'];
		       	$builder         = $this->db->table($comptxnmst_tbl); 
                $builder->orderBy('txn_id');    
                $builder->limit(1);
                $builder->where('voucher_txn_id', $voucher_txn_id);	
                $builder->where('master_id_type', 'acc');
                $builder->where('comp_vch_series_id', $voucher_type_id);	
        		$builder->where('comp_id', $comp_id);	
        		$result = $builder->get()->getResultArray();
        		
        		 $material_centre_info = $this->material_centre_info($comp_id,$row['mat_cent_id']);
        	    if($material_centre_info)
        	        $material_centre = $this->enc_string->nc_string($material_centre_info['mat_cent_name'],'de');
        	       else
        	       $material_centre  = '';
	       
                foreach($result as $values){		
                    
                    // $mat_cent_id =   
                    $values['bill_no']          =  $row['comp_vch_no'];
                    $values['voucher_date']     =  date("d-m-Y", strtotime($row['voucher_date']));
                    $values['material_centre']  =  $material_centre;
                    $values['voucher_type_id']  =  $voucher_type_id;
                    $values['voucher_txn_id']   =  $voucher_txn_id;
                

                    $party_name = $this->get_invoice_account_info($voucher_txn_id,$comp_vch_series_id);
                    $values['account_name']      =  $party_name;
                    $values['gstin']             = "";
                   
                     
                    $sale_account_amount    = $this->account_amount($voucher_txn_id,$comp_vch_series_id,$comp_id,$values['master_id'],$values['txn_id']); 
                    $values['sale_amount']   =  formatAmount($sale_account_amount);
                    
                    $values['notes']   =  '';
                    $all_records[] = $values;
                    
        		        
    	           }
		      }
		  }  
	
		     
   }
   else{
     $total_Records=0; $all_records=array(); 
       
   }
   
    echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($all_records)."}"; 	       

  } 
  
    public function ajax_sale_return_register_list(){
        $company_sales_accounts = $this->company_sales_accounts();
        $comp_id         = $this->session->get('ses_company_id');
	    $ses_comp_fy_id  = $this->session->get('ses_comp_fy_id');
	    $base_url        = base_url().'/'.getenv('AdminPath');
	    $comptxnmst_tbl  = $comp_id.'_comptxnmst_'.$ses_comp_fy_id;
	    $from_date       =  $_POST["from_date"];
	    $to_date         =  $_POST["to_date"];
	   
	   if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
	   if($from_date!='' || $to_date!='')
         {
	    $vch_txn_conso_tbl = $comp_id.'_vhtxnconso_'.$ses_comp_fy_id;
	    $builder           = $this->db->table($vch_txn_conso_tbl); 
        $builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);	 
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
		$builder->where('voucher_type_id','2');
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
		$total_Records = $builder->countAllResults();
		
		if($pq_curPage=='0') $pq_curPage='1';
		
		$offset = ($pq_rPP * ($pq_curPage - 1));

            if ($offset > $total_Records)
            {        
                $pq_curPage = ceil($total_Records / $pq_rPP);
                $offset = ($pq_rPP * ($pq_curPage - 1));
            }
            
            
		$builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);	 
		$builder->where('voucher_type_id','2');
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
	    $builder->limit($pq_rPP,$offset);
		$result  = $builder->get()->getResultArray();
		$all_records = array();	
		if($result){
		    foreach($result as $row){
		       $voucher_txn_id   = $row['voucher_txn_id'];
		       $voucher_type_id  = $row['voucher_type_id'];
		       $comp_vch_series_id = $row['comp_vch_series_id'];
		       	$builder         = $this->db->table($comptxnmst_tbl); 
                $builder->orderBy('txn_id');    
                $builder->limit(1);
                $builder->where('voucher_txn_id', $voucher_txn_id);	
                $builder->where('master_id_type', 'acc');
                $builder->where('comp_vch_series_id', $voucher_type_id);	
        		$builder->where('comp_id', $comp_id);	
        		$result = $builder->get()->getResultArray();
        		
        		 $material_centre_info = $this->material_centre_info($comp_id,$row['mat_cent_id']);
        	    if($material_centre_info)
        	        $material_centre = $this->enc_string->nc_string($material_centre_info['mat_cent_name'],'de');
        	       else
        	       $material_centre  = '';
	       
                foreach($result as $values){		
                    
                    // $mat_cent_id =   
                    $values['bill_no']          =  $row['comp_vch_no'];
                    $values['voucher_date']     =  date("d-m-Y", strtotime($row['voucher_date']));
                    $values['material_centre']  =  $material_centre;
                    $values['voucher_type_id']  =  $voucher_type_id;
                    $values['voucher_txn_id']   =  $voucher_txn_id;

                    $party_name = $this->get_invoice_account_info($voucher_txn_id,$comp_vch_series_id);
                    $values['account_name']      =  $party_name;
                    $values['gstin']             = "";
                   
                     
                    $sale_account_amount    = $this->account_amount($voucher_txn_id,$comp_vch_series_id,$comp_id,$values['master_id'],$values['txn_id']); 
                    $values['sale_amount']   =  formatAmount($sale_account_amount);
                    
                    $values['notes']   =  '';
                    $all_records[] = $values;
                    
        		        
    	           }
		      }
		  }  
	
		     
   }
   else{
     $total_Records=0; $all_records=array(); 
       
   }
   
    echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($all_records)."}"; 	       

  } 


  	public function load_sales_order_register($pq_curPage, $limit, $from_date, $to_date){
        
  		$voucher_type_id = 19;
  		$final = [];

        $voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	    $voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	    $voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
	    $comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	    $acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
	    $acc_crsref_tbl = $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
	    
	    $builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	   	$builder->where($voucher_tbl.'.comp_id', $this->company_id);
	   	$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);
		if($this->session->get('ses_boid')!='')
		$builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));
		
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}
		$builder->orderBy('voucher_date');
		$builder->orderBy('voucher_txn_id');
		$total_records = $builder->countAllResults();
		
		if($pq_curPage=='0') $pq_curPage='1';
		$offset = ($limit * ($pq_curPage - 1));

            if ($offset > $total_records)
            {        
                $pq_curPage = ceil($total_records / $limit);
                $offset = ($limit * ($pq_curPage - 1));
            }
            
            

	    $builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	   	$builder->where($voucher_tbl.'.comp_id', $this->company_id);
	   	$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);					
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}
		$builder->orderBy('voucher_txn_id');
	 	$builder->limit($limit,$offset);  
		$result = $builder->get()->getResultArray();

		foreach($result as $key => $value)
		{
			$amount = '';
			$count_amount = 0;

			$builder = $this->db->table($comp_txn_tbl);
			$builder->select('txn_id, master_id');
			$builder->where('voucher_txn_id', $value['voucher_txn_id']);
			$builder->where('master_id_type', 'aco');
			$builder->orderBy('txn_id', 'acc');
			$builder->limit(1);
			$table = $builder->get()->getRowArray();

			$txn_id = $table['txn_id'];
			$account_id = $table['master_id'];
			$account_name = $this->get_account_info($account_id)['acc_name'];

			$builder = $this->db->table($acc_oth_tbl);
			$builder->where('voucher_txn_id', $value['voucher_txn_id']);
			$builder->where('acc_id', $account_id);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
			$data = $builder->get()->getRowArray();

			if($data['acc_oth_txn_drcr'] == 'd')
			{
				$amount = formatAmount($data['acc_oth_txn_amount']).' DR';
                $count_amount = $data['acc_oth_txn_amount'];
			}
			if($data['acc_oth_txn_drcr'] == 'c')
			{
				$amount = formatAmount($data['acc_oth_txn_amount']).' CR';
                $count_amount = -$data['acc_oth_txn_amount'];
			}

			$order_no = '';
			
	 		$builder = $this->db->table($acc_crsref_tbl);
	 		$builder->where('acc_cross_ref_type', 'SONONNN');
	 		$builder->where('txn_id', $txn_id);
	 		$builder->where('voucher_txn_id', $value['voucher_txn_id']);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
	 		$ref = $builder->get()->getRowArray();
	 		if($ref){
	 			$order_no = $ref['acc_cross_ref_data'];
	 		}

			$voucher_date = date('d-m-Y', strtotime($value['voucher_date']));

			$final[] = [
                    'account_name'     =>   $account_name,
                    'voucher_date'     =>   $voucher_date,
                    'voucher_no'       =>   $value['comp_vch_no'],
                    'order_no'         =>   $order_no,
                    'amount'           =>   $amount,
                    'count_amount'     =>   $count_amount,
                    'comp_vch_type'    =>   $value['comp_vch_type'],
                    'voucher_txn_id'   =>   $value['voucher_txn_id'],
                    'voucher_type_id'  =>   $value['voucher_type_id'],
                ];
		}
		// echo "<pre>";print_r($final);exit;
   
   	return [
   			'totalRecords'	=> $total_records,
   			'curPage'		=> $pq_curPage,
   			'data'			=> $final
   		];	       

  	}

    public function load_purchase_order_register($pq_curPage, $limit,  $from_date, $to_date){
        
  		$voucher_type_id = 12;
  		$final = [];

        $voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	    $voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	    $voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
	    $comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	    $acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
	    $acc_crsref_tbl = $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
	    
	    $builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	   	$builder->where($voucher_tbl.'.comp_id', $this->company_id);
	   	$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);
		if($this->session->get('ses_boid')!='')
		$builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));
		
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}
		$builder->orderBy('voucher_txn_id'); 
		$total_records = $builder->countAllResults();
		if($pq_curPage=='0') $pq_curPage='1';
		$offset = ($limit * ($pq_curPage - 1));

            if ($offset > $total_records)
            {        
                $pq_curPage = ceil($total_records / $limit);
                $offset = ($limit * ($pq_curPage - 1));
            }
            
            

	    $builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	   	$builder->where($voucher_tbl.'.comp_id', $this->company_id);
	   	$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);					
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}
		$builder->orderBy('voucher_date');
		$builder->orderBy('voucher_txn_id');
	 	$builder->limit($limit,$offset);  
		$result = $builder->get()->getResultArray();

		foreach($result as $key => $value)
		{
			$amount = '';
			$count_amount = 0;

			$builder = $this->db->table($comp_txn_tbl);
			$builder->select('txn_id, master_id');
			$builder->where('voucher_txn_id', $value['voucher_txn_id']);
			$builder->where('master_id_type', 'aco');
			$builder->orderBy('txn_id', 'acc');
			$builder->limit(1);
			$table = $builder->get()->getRowArray();

			$txn_id = $table['txn_id'];

			$account_id = $table['master_id'];
			$account_name = $this->get_account_info($account_id)['acc_name'];

			$builder = $this->db->table($acc_oth_tbl);
			$builder->where('voucher_txn_id', $value['voucher_txn_id']);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
			$builder->where('acc_id', $account_id);
			$data = $builder->get()->getRowArray();

			if($data['acc_oth_txn_drcr'] == 'd')
			{
				$amount = number_format($data['acc_oth_txn_amount'],2).' DR';
                $count_amount = $data['acc_oth_txn_amount'];
			}
			if($data['acc_oth_txn_drcr'] == 'c')
			{
				$amount = number_format($data['acc_oth_txn_amount'],2).' CR';
                $count_amount = -$data['acc_oth_txn_amount'];
			}

			$order_no = '';
			
	 		$builder = $this->db->table($acc_crsref_tbl);
	 		$builder->where('acc_cross_ref_type', 'PONONNN');
	 		$builder->where('txn_id', $txn_id);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
	 		$builder->where('voucher_txn_id', $value['voucher_txn_id']);
	 		$ref = $builder->get()->getRowArray();
	 		if($ref){
	 			$order_no = $ref['acc_cross_ref_data'];
	 		}

			$voucher_date = date('d-m-Y', strtotime($value['voucher_date']));
			$voucher_date = date('d-m-Y', strtotime($value['voucher_date']));

			$final[] = [
                    'account_name'     =>   $account_name,
                    'voucher_date'     =>   $voucher_date,
                    'voucher_no'       =>   $value['comp_vch_no'],
                    'order_no'         =>   $order_no,
                    'amount'           =>   $amount,
                    'count_amount'     =>   $count_amount,
                    'comp_vch_type'    =>   $value['comp_vch_type'],
                    'voucher_txn_id'   =>   $value['voucher_txn_id'],
                    'voucher_type_id'  =>   $value['voucher_type_id'],
                ];
		}
		// echo "<pre>";print_r($final);exit;
   
   		return [
   			'totalRecords'	=> $total_records,
   			'curPage'		=> $pq_curPage,
   			'data'			=> $final
   		];	       

	} 

	public function load_quotation_register($pq_curPage, $limit, $from_date, $to_date){
        
  		$voucher_type_id = 17;
  		$final = [];

        $voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	    $voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	    $voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
	    $comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	    $acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
	    $acc_crsref_tbl = $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
	    
	    $builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	   	$builder->where($voucher_tbl.'.comp_id', $this->company_id);
	   	$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);
		if($this->session->get('ses_boid')!='')
		$builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));
		
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}
		$builder->orderBy('voucher_txn_id'); 
		$total_records = $builder->countAllResults();
		if($pq_curPage=='0') $pq_curPage='1';
		$offset = ($limit * ($pq_curPage - 1));

            if ($offset > $total_records)
            {        
                $pq_curPage = ceil($total_records / $limit);
                $offset = ($limit * ($pq_curPage - 1));
            }

	    $builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	   	$builder->where($voucher_tbl.'.comp_id', $this->company_id);
	   	$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);					
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}
		$builder->orderBy('voucher_date');
		$builder->orderBy('voucher_txn_id');
	 	$builder->limit($limit,$offset);  
		$result = $builder->get()->getResultArray();

		foreach($result as $key => $value)
		{
			$amount = '';
			$count_amount = 0;

			$builder = $this->db->table($comp_txn_tbl);
			$builder->select('txn_id, master_id');
			$builder->where('voucher_txn_id', $value['voucher_txn_id']);
			$builder->where('master_id_type', 'aco');
			$builder->orderBy('txn_id', 'acc');
			$builder->limit(1);
			$table = $builder->get()->getRowArray();

			$txn_id = $table['txn_id'];

			$account_id = $table['master_id'];
			$account_name = $this->get_account_info($account_id)['acc_name'];

			$builder = $this->db->table($acc_oth_tbl);
			$builder->where('voucher_txn_id', $value['voucher_txn_id']);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
			$builder->where('acc_id', $account_id);
			$data = $builder->get()->getRowArray();

			if($data['acc_oth_txn_drcr'] == 'd')
			{
				$amount = formatAmount($data['acc_oth_txn_amount']).' DR';
                $count_amount = $data['acc_oth_txn_amount'];
			}
			if($data['acc_oth_txn_drcr'] == 'c')
			{
				$amount = formatAmount($data['acc_oth_txn_amount']).' CR';
                $count_amount = -$data['acc_oth_txn_amount'];
			}


			$voucher_date = date('d-m-Y', strtotime($value['voucher_date']));

			$final[] = [
                    'account_name'     =>   $account_name,
                    'voucher_date'     =>   $voucher_date,
                    'voucher_no'       =>   $value['comp_vch_no'],
                    'unit'         	   =>   '',
                    'quantity'         =>   '',
                    'amount'           =>   $amount,
                    'count_amount'     =>   $count_amount,
                    'comp_vch_type'    =>   $value['comp_vch_type'],
                    'voucher_txn_id'   =>   $value['voucher_txn_id'],
                    'voucher_type_id'  =>   $value['voucher_type_id'],
                ];
		}
		// echo "<pre>";print_r($final);exit;
   
   		return [
   			'totalRecords'	=> $total_records,
   			'curPage'		=> $pq_curPage,
   			'data'			=> $final
   		];	       

	}

	public function load_purchase_requisition_register($pq_curPage, $limit,  $from_date, $to_date){
        
  		$voucher_type_id = 21;
  		$final = [];

        $voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	    $voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	    $voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
	    $comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	    $acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
	    $acc_crsref_tbl = $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
	    
	    $builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	   	$builder->where($voucher_tbl.'.comp_id', $this->company_id);
	   	$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);
		if($this->session->get('ses_boid')!='')
		$builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));
		
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}
		$builder->orderBy('voucher_txn_id'); 
		$total_records = $builder->countAllResults();
		if($pq_curPage=='0') $pq_curPage='1';
		$offset = ($limit * ($pq_curPage - 1));

            if ($offset > $total_records)
            {        
                $pq_curPage = ceil($total_records / $limit);
                $offset = ($limit * ($pq_curPage - 1));
            }
            
            

	    $builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	   	$builder->where($voucher_tbl.'.comp_id', $this->company_id);
	   	$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);					
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}
		$builder->orderBy('voucher_date');
		$builder->orderBy('voucher_txn_id');
	 	$builder->limit($limit,$offset);  
		$result = $builder->get()->getResultArray();

		foreach($result as $key => $value)
		{
			$amount = '';
			$count_amount = 0;

			$builder = $this->db->table($comp_txn_tbl);
			$builder->select('txn_id, master_id');
			$builder->where('voucher_txn_id', $value['voucher_txn_id']);
			$builder->where('master_id_type', 'aco');
			$builder->orderBy('txn_id', 'acc');
			$builder->limit(1);
			$table = $builder->get()->getRowArray();

			$txn_id = $table['txn_id'];

			$account_id = $table['master_id'];
			$account_name = $this->get_account_info($account_id)['acc_name'];

			$builder = $this->db->table($acc_oth_tbl);
			$builder->where('voucher_txn_id', $value['voucher_txn_id']);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
			$builder->where('acc_id', $account_id);
			$data = $builder->get()->getRowArray();

			if($data['acc_oth_txn_drcr'] == 'd')
			{
				$amount = formatAmount($data['acc_oth_txn_amount']).' DR';
                $count_amount = $data['acc_oth_txn_amount'];
			}
			if($data['acc_oth_txn_drcr'] == 'c')
			{
				$amount = formatAmount($data['acc_oth_txn_amount']).' CR';
                $count_amount = -$data['acc_oth_txn_amount'];
			}


			$voucher_date = date('d-m-Y', strtotime($value['voucher_date']));

			$final[] = [
                    'account_name'     =>   $account_name,
                    'voucher_date'     =>   $voucher_date,
                    'voucher_no'       =>   $value['comp_vch_no'],
                    'amount'           =>   $amount,
                    'count_amount'     =>   $count_amount,
                    'comp_vch_type'    =>   $value['comp_vch_type'],
                    'voucher_txn_id'   =>   $value['voucher_txn_id'],
                    'voucher_type_id'  =>   $value['voucher_type_id'],
                ];
		}
		// echo "<pre>";print_r($final);exit;
   
   		return [
   			'totalRecords'	=> $total_records,
   			'curPage'		=> $pq_curPage,
   			'data'			=> $final
   		];	       

	}
    
}