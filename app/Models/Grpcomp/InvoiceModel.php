<?php
namespace App\Models\Admin;

use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Libraries\enc_string;

class InvoiceModel extends Model	{

    public function __construct() {
        parent::__construct();        
       $this->externaldb    = new externaldb();	
	   $this->db            = $this->externaldb->get_company_db();
	   $this->session       = \Config\Services::session();
	   $this->company_id    = $this->session->get('ses_company_id');
	   $this->enc_string    = new enc_string();
    }
    
    public function add_acctcrsref_transactions($tablename,$transdata){
        $this->db->table($tablename)->insert($transdata);        
   }
   
    public function BillSundryCompTxnEntry($comp_vch_series_id,$voucher_txn_id,$billsundry_id){
	   $comp_txn_master_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	   $txn_data = array("comp_id"=> $this->company_id,"comp_vch_series_id"=>$comp_vch_series_id,"voucher_txn_id"=>$voucher_txn_id,"master_id"=>$billsundry_id,'master_id_type'=>'bsd');
	   $this->db->table($comp_txn_master_tbl)->insert($txn_data);
	   $txn_id =  $this->db->insertID();	
	   return $txn_id;
  }	 
   
  function party_sale_accttxnoth_row($tablename){
      $company_id = $this->company_id;
      $allcr_sum=0;
      $alldr_sum =0;
      $result =  $this->db->table($tablename)->where('acc_oth_txn_tag LIKE "DCES%"')->where('comp_id', $company_id)->orderBy('acc_oth_txn_id','ASC')->get()->getResultArray(); 
      if($result){
          foreach($result as $row){
              if($row['acc_oth_txn_drcr']=='c')
                 $allcr_sum = $allcr_sum+$row['acc_oth_txn_amount'];
              if($row['acc_oth_txn_drcr']=='d')
                 $alldr_sum = $alldr_sum+$row['acc_oth_txn_amount'];
              
          }
          
      }
     $summarray = array('crsum'=>$allcr_sum,'drsum'=>$alldr_sum);
     return $summarray;  
    }    

   function party_accttxnoth_row($tablename){
      $company_id = $this->company_id;
      $allcr_sum=0;
      $alldr_sum =0;
      $result =  $this->db->table($tablename)->where('acc_oth_txn_tag LIKE "ICEP%"')->where('comp_id', $company_id)->orderBy('acc_oth_txn_id','ASC')->get()->getResultArray(); 
      if($result){
          foreach($result as $row){
              if($row['acc_oth_txn_drcr']=='c')
                 $allcr_sum = $allcr_sum+$row['acc_oth_txn_amount'];
              if($row['acc_oth_txn_drcr']=='d')
                 $alldr_sum = $alldr_sum+$row['acc_oth_txn_amount'];
              
          }
          
      }
     $summarray = array('crsum'=>$allcr_sum,'drsum'=>$alldr_sum);
     return $summarray;  
    } 
   
    function update_accttxnoth_party($oth_table,$voucher_txn_id,$acc_oth_txn_tag){
     $company_id = $this->company_id;  
     $udate_data = array("acc_oth_txn_tag"=>$acc_oth_txn_tag);
     $this->db->table($oth_table)->where('comp_id', $company_id)->where('voucher_txn_id', $voucher_txn_id)->update($udate_data);   
   }
   
    function bill_sundry_account_info($bill_sundry_id){
        
         $comp_id        = $this->company_id;
         $billsundry_tbl = $comp_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
         return $this->db->table($billsundry_tbl)->where('bill_sundry_id', $bill_sundry_id)->get()->getRowArray();   
        
    }
     
     function billsundry_items(){
         $comp_id        = $this->company_id;
        if(isset($_GET['term'])){ 
        $searchtext        = $_GET['term'];
       
	   $billsundry_tbl  = $comp_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
	   $data            =  $this->db->table($billsundry_tbl)->where('( `bill_sundry_name` LIKE  "%'.$searchtext.'%" OR `bill_sundry_alias` LIKE  "%'.$searchtext.'%" ) ')->orderBy('bill_sundry_name','ASC')->get()->getResultArray();
	   $final_result    = array();
	   if($data){
		  foreach($data as $row){
               $final_result[]    = array("label"=>ucwords($row['bill_sundry_name']),'value'=> $row['bill_sundry_id']);	
		      }
        }
	    return json_encode($final_result);  
        }
     }
     

     
     function add_accttxnoth_data($table,$data){
        $this->db->table($table)->insert($data); 
         
     }
     
     function get_inwardchallan_total($voucher_txn_id){
         $comp_id        = $this->company_id;
         $acctcrsref_tbl = $comp_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
         return $this->db->table($acctcrsref_tbl)->where('voucher_txn_id', $voucher_txn_id)
                                                ->where('acc_cross_ref_type','inward_challan_total')
                                                ->where('comp_id', $comp_id)->get()->getRowArray();   
      }
     
     
      function get_company_items($comp_id){	
        $searchtext        = $_GET['term'];
       
	   $item_master_tbl = $comp_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
	   $data            =  $this->db->table($item_master_tbl)->where('( `item_name` LIKE  "%'.$searchtext.'%" OR `item_alias` LIKE  "%'.$searchtext.'%" OR `product_id` LIKE  "%'.$searchtext.'%") ')->where('comp_id', $comp_id)->orderBy('item_name','ASC')->get()->getResultArray();
	   $final_result    = array();
	   if($data){
		  foreach($data as $row){
		      
		       $item_unit_info  = $this->item_unit_info($comp_id,$row['item_unit']); 
             if($item_unit_info){
                 $item_unit_name = $this->enc_string->nc_string($item_unit_info['item_unit'],'de');				 
			 }
              else{
                $item_unit_name ='' ;
			  }
		      
               $final_result[]    = array("label"=>ucwords($row['item_name']),'value'=> $row['item_id'],'unit_id'=>$row['item_unit'],'units'=>$item_unit_name);	
		      }
        }
	    return json_encode($final_result);  
     }
  
  
  function get_units_info($unit_id,$comp_id){	 
	 $item_unit_master_tbl = $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($item_unit_master_tbl)->where('unit_id', $unit_id)->where('comp_id', $comp_id)->get()->getRowArray();   	   
    }  
  
  
   function sale_against_challan_items($vouche_txn_id){
         $company_id     =  $this->company_id;
         $comptxnmst_tbl =  $company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
         $item_row       =  $this->db->table($comptxnmst_tbl)->where('master_id_type', 'itm')->where('comp_id', $company_id)->where('voucher_txn_id', $vouche_txn_id)->get()->getRowArray();  
          
         $item_id        =  $item_row['master_id'];
           
          $itemtxnnnn_tbl =  $company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
          
	      $result         =  $this->db->table($itemtxnnnn_tbl)->where('item_id', $item_id)->where('comp_id', $company_id)->where('voucher_txn_id', $vouche_txn_id)->get()->getResultArray();  
          $records        =  array();
          foreach($result as $values){
               $get_item_info   =  $this->get_item_info($values['item_id']);
               $item_unit_info  =  $this->get_units_info($get_item_info['item_unit'],$values['comp_id']);
               if(isset($item_unit_info['item_unit']))
				 $item_unit   = $this->enc_string->nc_string($item_unit_info['item_unit'],'de');
			   else
				 $item_unit   = ''; 
                $records[]  = array(	
			                      'item_id'        => $values['item_id'],
    							  'item_name'      => ucwords($get_item_info['item_name']),
    							  'item_qty'       => $values['item_txn_qty'],
    							  'short_narator'  => $values['item_txn_narr'],
    							  'item_unit'      => $item_unit,
    							  'item_price'     => $values['item_txn_amount']/$values['item_txn_qty'],
    							  'item_amount'    => $values['item_txn_amount'],
    							  'voucher_txn_id' => $values['voucher_txn_id'],
    							  'voucher_type_id' => $values['voucher_type_id']
				                 );  
				                 
		           }
		           
		  for($i=0;$i<=20;$i++){
		      
		     $records[]   = array(	
			                      'item_id'       => '',
    							  'item_name'     => '',
    							  'item_qty'      => '',
    							  'short_narator' => '',
    							  'item_unit'     => '',
    							  'item_price'    => '',
    							  'item_amount'   => '',
    							  'voucher_txn_id' => '',
    							  'voucher_type_id' => ''
				                 );  
		      
		  }         
       return  $records;  
   }
  
     
   public function add_itemcrsref_transactions($tablename,$transdata){
        $this->db->table($tablename)->insert($transdata); 
       
   }
   
   function against_purchase_challan_items($company_id,$vouche_txn_id){
          $comptxnmst_tbl =  $company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
          $item_row       =  $this->db->table($comptxnmst_tbl)->where('master_id_type', 'itm')->where('comp_id', $company_id)->where('voucher_txn_id', $vouche_txn_id)->get()->getRowArray();  
          
          $item_id       =  $item_row['master_id'];
         
          $itemtxnnnn_tbl =  $company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
          
	      $result         =  $this->db->table($itemtxnnnn_tbl)->where('item_id', $item_id)->where('comp_id', $company_id)->where('voucher_txn_id', $vouche_txn_id)->get()->getResultArray();  
          $records        =  array();
          foreach($result as $values){
               $get_item_info   =  $this->get_item_info($values['item_id']);
               $item_unit_info  =  $this->get_units_info($get_item_info['item_unit'],$values['comp_id']);
               if(isset($item_unit_info['item_unit']))
				 $item_unit   = $this->enc_string->nc_string($item_unit_info['item_unit'],'de');
			   else
				 $item_unit   = ''; 
                $records[]  = array(	
			                      'item_id'        => $values['item_id'],
    							  'item_name'      => ucwords($get_item_info['item_name']),
    							  'item_qty'       => $values['item_txn_qty'],
    							  'short_narator'  => $values['item_txn_narr'],
    							  'item_unit'      => $item_unit,
    							  'item_price'     => $values['item_txn_amount']/$values['item_txn_qty'],
    							  'item_amount'    => $values['item_txn_amount'],
    							  'voucher_txn_id' => $values['voucher_txn_id'],
    							  'voucher_type_id' => $values['voucher_type_id']
				                 );  
				                 
		           }
		           
		  for($i=0;$i<=20;$i++){
		      
		     $records[]   = array(	
			                      'item_id'       => '',
    							  'item_name'     => '',
    							  'item_qty'      => '',
    							  'short_narator' => '',
    							  'item_unit'     => '',
    							  'item_price'    => '',
    							  'item_amount'   => '',
    							  'voucher_txn_id' => '',
    							  'voucher_type_id' => ''
				                 );  
		      
		  }         
       return  $records;
       
       
   }
   
   function sale_against_challan_dropdown($comp_id){
         $accttxnoth_tbl = $comp_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
	     $result         =  $this->db->table($accttxnoth_tbl)->whereIn('acc_oth_txn_tag', array('DCESDUE','DCESDEF','SEDCSUR'))->where('comp_id', $comp_id)->get()->getResultArray(); 
	     $final_list     = array();
	     $final_list[''] = array(''=>'Choose');
         if($result){
             foreach($result as $row){
                $final_list[$row['voucher_txn_id']]= 'Challan No. '. $row['comp_vch_series_no'];
             }
         }
        return $final_list;  
   }
   
   
    function purchase_against_challan_dropdown($comp_id){
      
         $accttxnoth_tbl = $comp_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
	     $result         =  $this->db->table($accttxnoth_tbl)->whereIn('acc_oth_txn_tag', array('ICEPDUE','ICEPDEF','PESISUR'))->where('comp_id', $comp_id)->get()->getResultArray(); 
	 
		$final_list     = array();
	     $final_list[''] = array(''=>'Choose');
         if($result){
             foreach($result as $row){
                $voucher_cons_info = $this->get_voucher_cons_info($row['voucher_txn_id'],$comp_id);
                if($voucher_cons_info)
				$final_list[$row['voucher_txn_id']]= 'Voucher No. '. $voucher_cons_info['comp_vch_no'];
             }
         }
        return $final_list;
      
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
    
  public function item_txntbl_entry($item_id,$tablename,$comp_id){
   $response =  $this->db->table($tablename)->where('item_id', $item_id)->where('comp_id', $comp_id)->orderBy('item_txn_id')->limit(1)->get()->getRowArray();      
    return $response;  
  }   
  
 public function count_item_txn($item_id,$tablename,$comp_id){
   $response =  $this->db->table($tablename)->where('item_id', $item_id)->where('comp_id', $comp_id)->orderBy('item_txn_id')->countAllResults();
//   echo $this->db->GetLastQuery();
    return $response;  
  }   
  
  function update_accttxnoth_table($accttxnoth_tbl,$voucher_type_id,$voucher_txn_id,$data){
    $this->db->table($accttxnoth_tbl)->where('comp_id', $this->company_id)->where('voucher_txn_id', $voucher_txn_id)->where('voucher_type_id', $voucher_type_id)->update($data);    
    
      
  }
 
 function update_accttxnoth_transaction($company_id,$acc_id,$voucher_type_id,$voucher_txn_id,$txn_status){
    $accttxnoth_tbl =  $company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
    $udate_data     =  array('acc_oth_txn_tag' =>$txn_status);
    $this->db->table($accttxnoth_tbl)->where('comp_id', $company_id)->where('voucher_txn_id', $voucher_txn_id)->where('acc_id', $acc_id)->where('voucher_type_id', $voucher_type_id)->update($udate_data);    
   }
    
  
 public function get_item_txn_data($company_id,$item_id,$voucher_txn_id,$challan_type){
    $item_itemtxnnnn_tbl = $company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id'); 
     return $this->db->table($item_itemtxnnnn_tbl)->where('voucher_type_id',$challan_type)->where('voucher_txn_id', $voucher_txn_id)->where('item_id', $item_id)->where('comp_id', $company_id)->get()->getRowArray();  
     
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
     
     $comptxntable =$comp_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
     $this->db->table($comptxntable)->where('txn_id',$txnid)->where('comp_id',$comp_id)->where('master_id',$sel_item_id)->delete();
    // echo $this->db->GetLastQuery();
   }
   
  public function sale_transactions_list($voucher_txn_id,$voucher_type_id){
        $comp_id                = $this->session->get('ses_company_id');
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
	                     
	                     $this->update_all_entries($account_table_name,$updte_data,$txn_id,$comp_id);
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
					   
					   
					  $item_price   = $values['item_txn_amount']/$values['item_txn_qty'];
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
  
   
  function add_itemcrsref_transaction($itemcrsref_table,$data){
        $this->db->table($itemcrsref_table)->insert($data);
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
	  $builder->orderBy('voucher_txn_id','DESC');	
	  $result =  $builder->get()->getRowArray();
      if($result){
          $max_comp_vch_no = $result['max_comp_vch_no'];
          return $max_comp_vch_no+1;
        }
      else
        return 1;
     }	 
   
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
	public function add_itemtxnoth_sale_transactions($tblename ,$items_data,$voucher_txn_data,$item_open_qty,$item_open_value){
	     if($items_data){
	             $comp_vch_series_id  = $voucher_txn_data['comp_vch_series_id'];
	             $voucher_txn_id      = $items_data['voucher_txn_id'];
	             $comp_txn_master_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	             
	             $txn_data = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$comp_vch_series_id,"voucher_txn_id"=>$voucher_txn_id,"master_id"=>$items_data['item_id'],'master_id_type'=>'itm');
            	 $this->db->table($comp_txn_master_tbl)->insert($txn_data);	
                 $txn_id =  $this->db->insertID();	
                 
                 $items_data['txn_id']  = $txn_id; 
                 
                  $this->db->table($tblename)->insert($items_data); 
                  
                  $txnothid =  $this->db->insertID();
                  
                  $comp_vch_trail_tbl = $this->company_id.'_vhtxntrail_'.$this->session->get('ses_comp_fy_id');
            	 $voucher_trail_data = array("comp_id"=>$this->company_id,"voucher_txn_id"=>$voucher_txn_id,"txn_id"=>$txn_id);
            	 $this->db->table($comp_vch_trail_tbl)->insert($voucher_trail_data);
            	 
            	 
            	  $acctcrsref_table  = $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
            	  $acctcrsref_data   = array(
		                                     'acct_txn_id'       => $txnothid,
		                                     'comp_id'           => $this->company_id,
		                                     'txn_id'            => $items_data['txn_id'],
		                                    'voucher_txn_id'     => $items_data['voucher_txn_id'], 
		                                   	'bo_id'             => '',
		                                   'acc_cross_ref_type' => 'sale_voucher_no',	
		                                   'acc_cross_ref_data' => 	$items_data['comp_vch_series_no'],
		                                   'acc_cross_logdate'  => date('Y-m-d H:i:s') 
                                	      );
		                            
		              $this->db->table($acctcrsref_table)->insert($acctcrsref_data); 
		                             
		                             
            	 
            	 return $txnothid;
	     }
	    
	    
	}		
		
	public function add_itemtxnoth_transactions($tblename ,$items_data,$voucher_txn_data,$item_open_qty,$item_open_value){
	     if($items_data){
	             $comp_vch_series_id  = $voucher_txn_data['comp_vch_series_id'];
	             $voucher_txn_id      = $items_data['voucher_txn_id'];
	             $comp_txn_master_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	             
	             $txn_data = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$comp_vch_series_id,"voucher_txn_id"=>$voucher_txn_id,"master_id"=>$items_data['item_id'],'master_id_type'=>'itm');
            	 $this->db->table($comp_txn_master_tbl)->insert($txn_data);	
                 $txn_id =  $this->db->insertID();	
                 
                 $items_data['txn_id']  = $txn_id; 
                 
                  $this->db->table($tblename)->insert($items_data); 
                  
                  $txnothid =  $this->db->insertID();
                  
                  $comp_vch_trail_tbl = $this->company_id.'_vhtxntrail_'.$this->session->get('ses_comp_fy_id');
            	 $voucher_trail_data = array("comp_id"=>$this->company_id,"voucher_txn_id"=>$voucher_txn_id,"txn_id"=>$txn_id);
            	 $this->db->table($comp_vch_trail_tbl)->insert($voucher_trail_data);
            	 
            	 
            	  $itemcrsref_table  = $this->company_id.'_itemcrsref_'.$this->session->get('ses_comp_fy_id');
            	  $itemcrsref_data   = array(
		                                                'itm_crs_id'          => $txnothid,
		                                                'itm_crs_id_type'     => 'item_oth_txn_id',
		                                                'comp_id'             => $this->company_id,
                                						'item_id'             => $items_data['item_id'],
                                						'txn_id'              => $items_data['txn_id'],
                                						'bo_id'               => '',
                                                        'voucher_txn_id'      => $items_data['voucher_txn_id'],
                                                        'item_cross_ref_type' => 'purchase_voucher',
                                                        'item_cross_ref_data' => $items_data['comp_vch_series_no'],
                                                        'item_cross_logdate'  => date('Y-m-d H:i:s'), 
                                					   );
		                            
		            $this->add_itemcrsref_transaction($itemcrsref_table,$itemcrsref_data);
		                             
		                             
            	 
            	 return $txnothid;
	     }
	    
	    
	}	
	 
	 public function add_itemstxn_transactions($tblename ,$items_data,$voucher_txn_data,$item_open_qty,$item_open_value){
	     if($items_data){
	             $comp_vch_series_id  = $voucher_txn_data['comp_vch_series_id'];
	             $voucher_txn_id      = $items_data['voucher_txn_id'];
	             $comp_txn_master_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	             
	             $txn_data = array("comp_id"=>$this->company_id,"comp_vch_series_id"=>$comp_vch_series_id,"voucher_txn_id"=>$voucher_txn_id,"master_id"=>$items_data['item_id'],'master_id_type'=>'itm');
            	 $this->db->table($comp_txn_master_tbl)->insert($txn_data);	
                 $txn_id =  $this->db->insertID();	
                   
                 $item_value_total_sum =  $this->get_item_op_balance($items_data["comp_id"],$tblename,$items_data["item_id"],$items_data["item_txn_drcr"],$items_data["item_txn_amount"],$items_data["item_txn_qty"]);
	             $item_qty_total_sum =  $this->get_item_qty_balance($items_data["comp_id"],$tblename,$items_data["item_id"],$items_data["item_txn_drcr"],$items_data["item_txn_qty"]);
	             
	             
	             $items_data['txn_id']         = $txn_id;  
	             
                 $this->db->table($tblename)->insert($items_data);   
            	 // Add voucher transdactions trail 
            	 
            	 $comp_vch_trail_tbl = $this->company_id.'_vhtxntrail_'.$this->session->get('ses_comp_fy_id');
            	 $voucher_trail_data = array("comp_id"=>$this->company_id,"voucher_txn_id"=>$voucher_txn_id,"txn_id"=>$txn_id);
            	 $this->db->table($comp_vch_trail_tbl)->insert($voucher_trail_data);
            	 
            	 
            	 // check row num datewise and update row num value in table 
				 $row_nums = $this->get_itemstable_rownums($tblename);
	             if($row_nums){
	                 foreach($row_nums as $drow){
	                     $item_txn_id = $drow['item_txn_id'];
	                     $row_num     = $drow['row_num'];
	                     $txn_id      = $drow['txn_id'];
	                     $comp_id     = $drow['comp_id'];
	                     $voucher_txn_id  = $drow['voucher_txn_id'];
	                   
	                     
	                     $updte_data = array("item_txn_id"=>$row_num);
	                     
	                     $this->update_all_entries($tblename,$updte_data,$txn_id,$comp_id);
	                     
	                    
	                     
	                 }
	                  
	                  $this->update_item_all_balances($tblename,$item_open_qty,$item_open_value);     
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
 
  public function add_sundrytxn_transactions($table_name,$data){
	 $this->db->table($table_name)->insert($data);	 
  }
 
   public function add_purchase_transactions($insert_table_name,$data,$voucher_txn_data,$voucher_txn_name,$voucher_txn_id,$purchase_accounts_sum,$party_id){
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
	                   if($account_table)
	                    $this->update_account_all_balances($insert_table_name,$account_table['acc_op_bal'],$account_table['acc_op_drcr']);
	                    
	              }
	              
	  
	 
	  // add items sales accounts txn table entry
	  if($purchase_accounts_sum){
	     foreach($purchase_accounts_sum as $acc_id => $acc_total_info){  
	         
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
	          $data['acc_txn_drcr']    =  'd';
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
        if($tablerpow){
            
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
    
  	 function comp_voucher_series($comp_id){
  	     $voucher_type_id='18';
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

function comp_purchase_voucher_series($comp_id){
  	     $voucher_type_id='11';
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
     
    function party_dropdown2($comp_id){
        $comp_party_tbl = $comp_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
        $data =  $this->db->table($comp_party_tbl)->whereIn('acc_grp_id', array("15","16","17"))->where('comp_id', $comp_id)->orderBy('acc_name','ASC')->get()->getResultArray();
        $final_result      = array();
        $final_result  = [];
        if($data){
            foreach($data as $row){
                $final_result[] = [
                    'acc_id'    => $row['acc_id'],
                    'acc_name'  => $row['acc_name'],
                    'acc_grp_id'=> $row['acc_grp_id'],
                ];
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
     
     function add_account_transaction($data)
     {
         $account_table_name = $this->company_id.'_accnttxnnn_'.$data['acc_id'].'_'.$this->session->get('ses_comp_fy_id');
		 $this->db->table($account_table_name)->insert($data);
     }
     function add_company_transaction($data)
     {
         $comp_txn_master_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
         $this->db->table($comp_txn_master_tbl)->insert($data);
         
         return $this->db->insertID(); 
     }
     function add_voucher_trail($data)
     {
          $comp_vch_trail_tbl = $this->company_id.'_vhtxntrail_'.$this->session->get('ses_comp_fy_id');
    	  $this->db->table($comp_vch_trail_tbl)->insert($data);
     }
     
     
  function update_account_balance($id)
     {
         $insert_table_name = $this->company_id.'_accnttxnnn_'.$id.'_'.$this->session->get('ses_comp_fy_id');
         
         $row_nums = $this->get_rownums($insert_table_name);
         if($row_nums){
             foreach($row_nums as $drow){
                 $acc_txn_id = $drow['acc_txn_id'];
                 $row_num    = $drow['sale_row_num'];
                 $txn_id     = $drow['txn_id'];
                 $comp_id    = $drow['comp_id'];
                 $voucher_txn_id  = $drow['voucher_txn_id'];
                 
                 // update voucher number first
                 $vhtxnconso_table        =  $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
                 $voucher_number =  $this->update_voucherno_entries($vhtxnconso_table,$voucher_txn_id,$comp_id);
                 
                 
                 $updte_data = array("acc_txn_id"=>$row_num,"comp_vch_series_no"=>$voucher_number);
                 
                 $this->update_all_entries($insert_table_name,$updte_data,$txn_id,$comp_id);
             }
                $account_table_name = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
                $account_table = $this->db->table($account_table_name)->where('acc_id', $id)->get()->getRowArray();
             
                $this->update_account_all_balances($insert_table_name,$account_table['acc_op_bal'],$account_table['acc_op_drcr']);
                
          }
         
        
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
     
     function get_account_bill_refs($account_id)
     {
         $bill_mst_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
         $data = $this->db->table($bill_mst_tbl)->select('*, DATE_FORMAT(bill_due_date, "%d-%m-%Y") as due_date')->where('acc_id', $account_id)->get()->getResultArray();
         
         return $data;
     }
    
   
}