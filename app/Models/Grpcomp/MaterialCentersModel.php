<?php
namespace App\Models\Grpcomp;
use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Models\CommonModel;
use App\Libraries\enc_string;

class MaterialCentersModel extends Model	{
    public function __construct() {
       parent::__construct();        
       $this->externaldb    = new externaldb();	
	   $this->db            = $this->externaldb->get_company_db();
	    $this->CommonModel   =  new CommonModel();	
	   $this->session       = \Config\Services::session();
	    $this->company_id    =  $this->session->get('ses_company_id');
	   $this->enc_string    = new enc_string();
      }
 
   public function add_centres($data){
	   $mat_centre_master_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
	    $mat_cent_name = $this->enc_string->nc_string($data['mat_cent_name'],'de');
	    $exists        = $this->db->table($mat_centre_master_tbl)->where('comp_id',$data['comp_id'])->where('LOWER(mat_cent_name)', strtolower(trim($mat_cent_name)))->get()->getRowArray(); 
	    if($exists)
		   return "0";
	    else{
		   $this->db->table($mat_centre_master_tbl)->insert($data);
		   return $this->db->insertID();
	       }
      }
  
  
  public function add_mc_stores($data){
	    $mat_centre_stores_tbl = $this->company_id.'_mcstoremst_'.$this->session->get('ses_comp_fy_id');
	    $mat_cent_store_name   = $data['mc_store_name'];
	    $mc_store_alias        = $data['mc_store_alias'];
	    $exists1                = $this->db->table($mat_centre_stores_tbl)->where('LOWER(mc_store_alias)', strtolower(trim($mc_store_alias)))->get()->getRowArray(); 
	    $exists2                = $this->db->table($mat_centre_stores_tbl)->where('LOWER(mc_store_name)', strtolower(trim($mat_cent_store_name)))->get()->getRowArray(); 
	    
	    
	     if($exists1 || $exists2)
		   return "0";
	    else{
		   $this->db->table($mat_centre_stores_tbl)->insert($data);
		   return $this->db->insertID();
	       }
      }
      
      
   public function add_group($data){
	   $mat_centre_grp_master_tbl =$this->company_id.'_mcgrpmstnn_'.$this->session->get('ses_comp_fy_id');
	   $mat_cent_grp = $this->enc_string->nc_string($data['mc_grp_name'],'de');
	   $exists = $this->db->table($mat_centre_grp_master_tbl)->where('comp_id',$data['comp_id'])->where('LOWER(mc_grp_name)', strtolower(trim($mat_cent_grp)))->get()->getRowArray(); 
	    if($exists)
		 return "0";
	   else{
		  $this->db->table($mat_centre_grp_master_tbl)->insert($data);
		  return $this->db->insertID();
	     }
   }
  
    public function remove_centres($ids,$comp_id){
		$ses_comp_fy_id        =  $this->session->get('ses_comp_fy_id');
		$mat_centre_master_tbl  = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
		$ids                   = explode(",",$ids);
        foreach($ids as $centre_id){			
			$this->db->table($mat_centre_master_tbl)->where('mat_cent_id',$centre_id)->delete();			
		 }
		return TRUE;
	 }
	 
	 public function remove_single_centres($id){

		$mat_centre_master_tbl  = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
		$this->db->table($mat_centre_master_tbl)->where('mat_cent_id',$id)->delete();			
		return TRUE;
	 }
	 public function get_mc_name($id)
	 {
	     $mat_centre_master_tbl  = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
	     $data = $this->db->table($mat_centre_master_tbl)->select('mat_cent_name')->where('mat_cent_id', $id)->get()->getRowArray();
	     if($data)
            $mc_name = $this->enc_string->nc_string($data['mat_cent_name'],'de');
         else
            $mc_name = '';
         return $mc_name;
	 }
	 
	 function account_info($account_id){	 
       $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
       return $this->db->table($account_master_tbl)->where('acc_id', $account_id)->get()->getRowArray();   	   
    }
    
    function get_item_info($item_id){	 
	  $item_master_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($item_master_tbl)->where('item_id', $item_id)->where('comp_id', $this->company_id)->get()->getRowArray();   	   
    }
    
    function get_unit_name($unit_id){	 
	 $item_unit_master_tbl = $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');
	 $item_unit = $this->db->table($item_unit_master_tbl)->where('unit_id', $unit_id)->where('comp_id', $this->company_id)->get()->getRowArray();
	 return $this->enc_string->nc_string($item_unit['item_unit'],'de');
    }
    
	 public function load_mc_transactions($mat_cent_id,$start_date,$end_date)
	 {
	    $final_result = [];
	    $voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	    $voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	    $voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
	    
	    $builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	    
		$builder->where($voucher_tbl.'.comp_id', $this->company_id);					
		$builder->where('mat_cent_id', $mat_cent_id);
		if($start_date!=''){
			$builder->where('voucher_date >=', $start_date);
		}
		if($end_date!=''){
			$builder->where('voucher_date <=', $end_date);
		}
		$builder->orderBy('voucher_txn_id');
		$result = $builder->get()->getResultArray();
// 		echo "<pre>";print_r($result);exit;
		
		if($result){
    		foreach($result as $key => $row){
    		    
    		    $account_name = 'Self';
    		    
    		    $company_txn_master_table = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
    		    $party_data = $this->db->table($company_txn_master_table)
                                            ->select('master_id')
                                            ->where('voucher_txn_id', $row['voucher_txn_id'])
                                            ->where('comp_vch_series_id', $row['voucher_type_id'])
                                            ->where('master_id_type', 'acc')
                                            ->limit(1)
                                            ->get()->getRowArray();
    		    if($party_data)
    		    {
        		    $account_info = $this->account_info($party_data['master_id']);     
        		    if(isset($account_info['acc_name'])){
        		        $account_name  = $account_info['acc_name'];
        		    }
    		    }
    		    
    		    $item_data = $this->db->table($company_txn_master_table)
                                            ->select('txn_id, master_id')
                                            ->where('voucher_txn_id', $row['voucher_txn_id'])
                                            ->where('comp_vch_series_id', $row['voucher_type_id'])
                                            ->where('master_id_type', 'itm')
                                            ->get()->getResultArray();
    		    if($item_data)
    		    {
    		        foreach($item_data as $key2 => $value2)
    		        {
    		            
    		            $item_info = $this->get_item_info($value2['master_id']);
                        $item_name = $item_info['item_name'];
                        $item_unit = $this->get_unit_name($item_info['item_unit']);
                        
                        
    		            $item_txn_table =  $this->company_id.'_itemtxnnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
    		            $single_item_data = $this->db->table($item_txn_table)
                                            ->select('*')
                                            ->where('txn_id', $value2['txn_id'])//2
                                            ->get()->getRowArray();
 
                        if($single_item_data['item_txn_drcr']=='c') // Sales
						 {
						    $inward_qty  = '0';
						    $outward_qty = $single_item_data['item_txn_qty'];
						 }
					     else if($single_item_data['item_txn_drcr'] =='d') // purchase
					     {
						     $inward_qty  = $single_item_data['item_txn_qty'];
					         $outward_qty = 0;
						 }
						 
						 $price = $single_item_data['item_txn_amount'];
						 $amount = ($single_item_data['item_txn_amount']*$single_item_data['item_txn_qty']);
						 $closing_qty = $single_item_data['item_qty_bal'];
						 $closing_bal = $single_item_data['item_value_bal'];
						 
						$final_result[] = [
						    'voucher_txn_id'            => $row['voucher_txn_id'],
						    'voucher_type_id'           => $row['voucher_type_id'],
						    'comp_vch_series_id'        => $row['comp_vch_series_id'],
						    'voucher_date'              => date("d-m-Y", strtotime($row['voucher_date'])),
						    'mat_cent_id'               => $row['mat_cent_id'],
						    'comp_vch_type'             => $row['comp_vch_type'],
						    'comp_vch_series'           => $row['comp_vch_series'],
						    'comp_vch_no'               => $row['comp_vch_no'],
						    'account_name'              => $account_name,
						    'item_name'                 => $item_name,
						    'item_unit'                 => $item_unit,
						    'qty_in'                    => $inward_qty,
						    'qty_out'                   => $outward_qty,
						    'price'                     => $price,
						    'amount'                    => $amount,
						    'closing_qty'               => $closing_qty,
						    'closing_bal'               => $closing_bal
						    ];

                        
    		        }
    		    }
        		 $final_result[] = [];    
              }
    	   }

		return $final_result;
	 }
	 
	 function check_mc_with_voucher($id)
     {
         $voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	     $data =  $this->db->table($voucher_tbl)->where('mat_cent_id', $id)->get()->getRowArray();
	     if(!empty($data)){
	         return 1;
	     }
	     return 0;
     }
	
   public function modify_mc_stores($store_id,$update_data){
      $mat_store_master_tbl = $this->company_id.'_mcstoremst_'.$this->session->get('ses_comp_fy_id');
	  $this->db->table($mat_store_master_tbl)->where('mc_store_id',$store_id)->update($update_data);		
	  return true;
   }	 
	 
   public function update_centres($centre_id,$update_data){
	   $mat_centre_master_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
	    $this->db->table($mat_centre_master_tbl)->where('mat_cent_id',$centre_id)->update($update_data);		
		return true;	
   } 

   public function update_group($update_data,$group_id){
	   $mat_centre_grp_master_tbl = $this->company_id.'_mcgrpmstnn_'.$this->session->get('ses_comp_fy_id');
	    $this->db->table($mat_centre_grp_master_tbl)->where('mc_grp_id',$group_id)->update($update_data);		
		return true;	
     }   
   
   public function ajax_group_list($limit,$pq_curPage){
	   $records = array();		
	   if($this->company_id){
	    $comp_id  =  $this->session->get('ses_company_id');
	    $base_url = base_url().'/'.getenv('AdminPath');		
		$mat_centre_grp_master_tbl = $this->company_id.'_mcgrpmstnn_'.$this->session->get('ses_comp_fy_id');		
        $builder  = $this->db->table($mat_centre_grp_master_tbl); 
        $builder->orderBy('mc_grp_name');
		$builder->where('comp_id', $comp_id);		 	
        $result = $builder->get()->getResultArray();   
        
		$records = array();
        foreach($result as $values){
            $records[] = array(	
			          'group_id'    => $values['mc_grp_id'],
                      'group_name'  => ucwords($this->enc_string->nc_string($values['mc_grp_name'],'de')),
					  'alias_name'  => $this->enc_string->nc_string($values['mc_alias'],'de'),
					  'grp_primary' => ($values['mc_primary']!='' && $values['mc_primary']!='0')?$values['mc_primary']:'No'					  					  	
				   );  
		     }	
	      }
	   
 	      $total_records = count($records);
		  return [
			'total_records'	=> $total_records,
			'data'	=> $records
		 ];      
      }
   public function remove_centre_grps($ids,$comp_id){
		$ses_comp_fy_id        =  $this->session->get('ses_comp_fy_id');
		$mat_centre_grp_master_tbl  = $this->company_id.'_mcgrpmstnn_'.$this->session->get('ses_comp_fy_id');
		$ids                   = explode(",",$ids);
        foreach($ids as $centre_group_id){			
			$this->db->table($mat_centre_grp_master_tbl)->where('centre_grp_id',$centre_group_id)->delete();			
		 }
		return TRUE;
	 }
	 
	 public function remove_single_store($id){
	    $mat_store_master_tbl  = $this->company_id.'_mcstoremst_'.$this->session->get('ses_comp_fy_id');
		$this->db->table($mat_store_master_tbl)->where('mc_store_id',$id)->delete();	
		return TRUE;   
	   }
	 
	 public function remove_single_centre_grps($id)
	 {
	     $mat_centre_grp_master_tbl  = $this->company_id.'_mcgrpmstnn_'.$this->session->get('ses_comp_fy_id');
		 $this->db->table($mat_centre_grp_master_tbl)->where('mc_grp_id',$id)->delete();			
		 return TRUE;
	 }
	 
	 public function get_mc_group_name($id)
	 {
	     $mat_centre_grp_master_tbl  = $this->company_id.'_mcgrpmstnn_'.$this->session->get('ses_comp_fy_id');
	     $data = $this->db->table($mat_centre_grp_master_tbl)->select('mc_grp_name')->where('mc_grp_id', $id)->get()->getRowArray();
         $mc_group_name = $this->enc_string->nc_string($data['mc_grp_name'],'de');
         return $mc_group_name;
	 }
	 
	 function check_mc_with_group($group_id)
     {
         $mat_centre_master_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
	     $data =  $this->db->table($mat_centre_master_tbl)->where('mat_cent_grp_id', $group_id)->get()->getRowArray();
	     if(!empty($data)){
	         return 1;
	     }
	     return 0;
     }

  public function ajax_mc_stores_list($limit,$pq_curPage){
	    $records   = array();
	  if($this->company_id){
	    $comp_id   =  $this->session->get('ses_company_id');	      
        $mat_centre_stores_tbl =  $this->company_id.'_mcstoremst_'.$this->session->get('ses_comp_fy_id');			
        $builder   = $this->db->table($mat_centre_stores_tbl); 
        $builder->orderBy('mc_store_id');		 	
        $result    = $builder->get()->getResultArray();  
       
        foreach($result as $values){
            $center_info = $this->center_info($values['mat_cent_id']);
			if(isset($center_info['mat_cent_name'])){
			  $show_mc_name = $this->enc_string->nc_string($center_info['mat_cent_name'],'de');
			  }
			else{
				$show_mc_name ='';	
			  }
			 
			$records[] = array(	
                      'mc_store_id'    => $values['mc_store_id'],
					  'mc_store_name'  => $values['mc_store_name'],
					  'mc_store_alias' => $values['mc_store_alias'],
					  'mc_name'        => $show_mc_name 					  
				     );  
		     }	 
		}			 
		  return [
			'total_records'	=> count($records),
			'data'	=> $records
		];    
      }
      
      
  public function ajax_centres_list(){
	   
	    if($this->company_id){			
         $mat_centre_master_tbl =  $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');			
	 
        $builder   = $this->db->table($mat_centre_master_tbl); 
        $builder->orderBy('mat_cent_name');
		$builder->where('comp_id', $this->company_id);		 	
        $result    = $builder->get()->getResultArray();  
        $records   = array();		
        foreach($result as $values){
            $centre_group_info = $this->group_info($values['mat_cent_grp_id']);
			if(isset($centre_group_info['mc_grp_name'])){
			  $show_group = $this->enc_string->nc_string($centre_group_info['mc_grp_name'],'de');
			  }
			else{
				$show_group ='';	
			  }			
			$records[] = array(	
                      'center_id'      => $values['mat_cent_id'],
					  'mat_cent_name'  => $this->enc_string->nc_string($values['mat_cent_name'],'de'),
					  'mat_cent_alias' => $this->enc_string->nc_string($values['mat_cent_alias'],'de'),
					  'mat_cent_print' => $this->enc_string->nc_string($values['mat_cent_print'],'de'),
					  'group_name'     => $show_group 					  
				     );  
		     }	 	       
		} else{
			$records=array();
			
		
		}
		$pq_curPage =1;
		$totalrec =count($records);
		 echo  "{\"totalRecords\":" .$totalrec . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($records)."}"; 
      
      }
   	 
   function centres_group_dropdown(){
	   $mat_centre_grp_master_tbl = $this->company_id.'_mcgrpmstnn_'.$this->session->get('ses_comp_fy_id');
	   $data =  $this->db->table($mat_centre_grp_master_tbl)->orderBy('mc_grp_name','ASC')->get()->getResultArray();
	   $final_result = array();
	   $final_result['']  = 'Choose';
	   if($data){
		  foreach($data as $row){
              $final_result[$row['mc_grp_id']] = $this->enc_string->nc_string($row['mc_grp_name'],'de');			   
	        }
        }
	  return $final_result;	
     } 
  
  
  function mc_centres_dropdown(){
	   $mat_centre_master_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
	   $data         =  $this->db->table($mat_centre_master_tbl)->orderBy('mat_cent_id')->get()->getResultArray();
	   $final_result = array();
	   $final_result['']  = 'Choose';
	   if($data){
		  foreach($data as $row){
              $final_result[$row['mat_cent_id']] = $this->enc_string->nc_string($row['mat_cent_name'],'de');			   
	        }
        }
	  return $final_result;	
     }

  
  
   function mc_store_info($store_id){
      $mat_store_master_tbl =  $this->company_id.'_mcstoremst_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($mat_store_master_tbl)->where('mc_store_id', $store_id)->get()->getRowArray();   	   
   }  
   
     
   function center_info($centre_id){
      $mat_centre_master_tbl =  $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($mat_centre_master_tbl)->where('mat_cent_id', $centre_id)->get()->getRowArray();   	   
   }  
   
   function group_info($centre_grp_id){	 
     $mat_centre_grp_master_tbl = $this->company_id.'_mcgrpmstnn_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($mat_centre_grp_master_tbl)->where('mc_grp_id', $centre_grp_id)->get()->getRowArray();   	   
   }   
}