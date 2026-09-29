<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Libraries\enc_string;
use App\Libraries\UUIDtables;

class MaterialCentersModel extends Model	{
    public function __construct() {
       parent::__construct();        
       $this->session       = \Config\Services::session();
	   $this->company_id    =  $this->session->get('ses_company_id');
	   $this->fy_id         =  $this->session->get('ses_comp_fy_id');
	   $this->bo_id         =  $this->session->get('ses_boid');	   
      }
 
   public function add_centres($data){
	    $exists1 = $this->db->table( "matcentmst")->where('cmp_id',trim($data['cmp_id']))
	              ->where('LOWER(mat_cent_name)', strtolower(trim($data['mat_cent_name'])))
				   ->where('mat_cent_is_active',1)
				  ->get()->getRowArray(); 
	    $exists2 = $this->db->table("matcentmst")->where('cmp_id',trim($data['cmp_id']))
	              ->where('LOWER(mat_cent_alias)', strtolower(trim($data['mat_cent_alias'])))
				   ->where('mat_cent_is_active',1)
				  ->get()->getRowArray();
		$exists3 = $this->db->table("matcentmst")->where('cmp_id',trim($data['cmp_id']))
	              ->where('LOWER(mat_cent_print_name)', strtolower(trim($data['mat_cent_print_name'])))
				   ->where('mat_cent_is_active',1)
				  ->get()->getRowArray(); 		  
	    if($exists1)
		    return "-1";
	    else  if($exists2)
		   return "-2";
	    else  if($exists2)
		   return "-3";
	    else{
		   $this->db->table("matcentmst")->insert($data);
		   $mat_cent_id = $this->db->insertID();
		   return $mat_cent_id;
	       }
      }
  
    public function add_undercrsmt($txn_data){
	  	$this->db->table("undercrsmt")->insert($txn_data);	
    } 
	
	public function add_centre_details($matcentdet_data){
	  	$this->db->table("matcentdet")->insert($matcentdet_data);	
    }  
	public function update_undercrsmt($data,$account_id,$crs_mst_type){		
	    $undercrsmt_tbl = "undercrsmt";	 
		$table = $this->db->table($undercrsmt_tbl)
		       ->where('cmp_id',$this->company_id)
	    	   ->where('crs_mst_id',$account_id)
			   ->where('crs_mst_type',$crs_mst_type)
			   ->where('cmpfymastr_id ',$this->fy_id)
        	   ->get()->getRowArray(); 
		if($table){
		 $updata =array("under_crs_mst_id"=>$data['under_crs_mst_id'],"crs_mst_parent_id"=>$data['crs_mst_parent_id'],
		                "under_main_id"=>$data['under_main_id']);	
		 $this->db->table($undercrsmt_tbl)
		      ->where('cmp_id',$this->company_id)
			  ->where('crs_mst_id',$account_id)
			  ->where('crs_mst_type',$crs_mst_type)
			  ->where('cmpfymastr_id',$this->fy_id)->update($updata);	
		}else{
		 $this->db->table($undercrsmt_tbl)->insert($data);		
		}
		return ['status' => true, 'account_id' => $account_id];	
   } 
   public function update_centre_details($account_id,$data){		
	    $matcentdet_tbl = "matcentdet";	 
		$table = $this->db->table($matcentdet_tbl)
		       ->where('cmp_id',$this->company_id)
	    	   ->where('mat_cent_id',$account_id)
			   ->get()->getRowArray(); 
		if($table){
		 $updata =array("mat_cent_addr1"=>$data['mat_cent_addr1'],"mat_cent_addr2"=>$data['mat_cent_addr2'],
		                "mat_cent_city"=>$data['mat_cent_city'],"mat_cent_state"=>$data["mat_cent_state"],"mat_cent_pin_zip"=>$data["mat_cent_pin_zip"],
						"mat_cent_country"=>$data["mat_cent_country"]
						);	
		 $this->db->table($matcentdet_tbl)
		      ->where('cmp_id',$this->company_id)
			  ->where('mat_cent_id',$account_id)
			  ->update($updata);	
		}else{
		 $this->db->table($matcentdet_tbl)->insert($data);		
		}
		return ['status' => true, 'account_id' => $account_id];	
   } 
    public function add_group($data){	  
	   $exists1 = $this->db->table( "matcentgrp")->where('cmp_id',trim($data['cmp_id']))
	              ->where('LOWER(mat_cent_grp_name)', strtolower(trim($data['mat_cent_grp_name'])))
				  ->get()->getRowArray(); 
	   $exists2 = $this->db->table("matcentgrp")->where('cmp_id',trim($data['cmp_id']))
	              ->where('LOWER(mat_cent_grp_alias)', strtolower(trim($data['mat_cent_grp_alias'])))
				  ->get()->getRowArray(); 
	    if($exists1)
		    return "-1";
	    else  if($exists2)
		   return "-2";
	    else{
		  $this->db->table("matcentgrp")->insert($data);
		  $mc_grp_id = $this->db->insertID();
		  return $mc_grp_id;
	     }
     }
  
    public function remove_centres($ids,$comp_id){
		$ses_comp_fy_id        =  $this->session->get('ses_comp_fy_id');
		$mat_centre_master_tbl  = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
		$ids                   = explode(",",$ids);
        foreach($ids as $centre_id){			
			$this->db->table($mat_centre_master_tbl)->where('mat_cent_id',$centre_id)->delete();
			$this->delete_comp_fy_mst_map($centre_id,'mcmasternn');			
		 }
		return TRUE;
	 }
	 
	 public function remove_single_centres($id){
		$this->db->table("matcentmst")->where('cmp_id',$this->company_id)->where('mat_cent_id',$id)->delete();
	    $this->db->table("undercrsmt")->where('cmp_id',$this->company_id)->where('crs_mst_type',7)->where('crs_mst_id',$id)->delete();	
	    $this->db->table("matcentdet")->where('cmp_id',$this->company_id)->where('mat_cent_id',$id)->delete();
	    return TRUE;
	 }
	 
	public function check_mc_exists($id,$name){
     $data = $this->db->table("matcentmst")->select('mat_cent_name')->where('mat_cent_is_active',1)->where('cmp_id',$this->company_id)->where('LOWER(mat_cent_name)', strtolower($name))->get()->getRowArray();
     if($data)
        return true; 
        else
        return false;
     }	
	 
	public function changestatus_single_mc($id,$status){
		$this->db->table("matcentmst")->where('cmp_id',$this->company_id)->where("mat_cent_id",$id)->update(["mat_cent_is_active"=>$status]);
		$this->db->table("undercrsmt")->where('cmp_id',$this->company_id)->where("crs_mst_id",$id)->where("crs_mst_type",7)->where("cmpfymastr_id",$this->fy_id)->update(["crs_is_active"=>$status]); 
    }
	
	public function check_mc_txn_exists($id){
        $data = $this->db->table("vchtxnconso")->select('vch_txn_id')->where('mat_cent_id',$id)->where('cmp_id',$this->company_id)->where('hobo_id', $this->bo_id)->get()->getRowArray();
       if($data)
        return true; 
        else
        return false;
     }
	 
	 public function get_mc_name($id)
	 {
	     $data = $this->db->table("matcentmst")->select('mat_cent_name')->where('cmp_id', $this->company_id)->where('mat_cent_id', $id)->get()->getRowArray();
	     if($data)
            $mc_name = $data['mat_cent_name'];
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
	 return $item_unit['item_unit'];
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
         $data =  $this->db->table("vchtxnconso")->where('mat_cent_id', $id)->get()->getRowArray();
	     if($data){
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
	   $table = $this->db->table("matcentmst")
	    		->where('LOWER(mat_cent_name)',strtolower(trim($update_data['mat_cent_name'])))
				 ->where('cmp_id',$this->company_id)
                ->where('mat_cent_id !=',$centre_id)
        	    ->get()->getRowArray(); 
	    if($table){
	        return ['status' => false, 'message' => 'Name must be unique'];
	    }
	    
	    $table = $this->db->table("matcentmst")
		       ->where('cmp_id',$this->company_id)
	    	   ->where('mat_cent_id !=',$centre_id)
        	   ->Where('LOWER(mat_cent_alias)',strtolower(trim($update_data['mat_cent_alias'])))
        	   ->get()->getRowArray();
	    if($table){
	        return ['status' => false, 'message' => 'Alias name must be unique'];
	    }  
		
		$table = $this->db->table("matcentmst")
		       ->where('cmp_id',$this->company_id)
	    	   ->where('mat_cent_id !=',$centre_id)
        	   ->Where('LOWER(mat_cent_print_name)',strtolower(trim($update_data['mat_cent_print_name'])))
        	   ->get()->getRowArray();
	    if($table){
	        return ['status' => false, 'message' => 'Print name must be unique'];
	    }  
		$this->db->table("matcentmst")->where('mat_cent_id',$centre_id)->update($update_data);		
		 return ['status' => true, 'account_id' => $centre_id];
   } 

   public function update_group($update_data,$mat_cent_grp_id){
	 $table = $this->db->table("matcentgrp")
	    		->where('LOWER(mat_cent_grp_name)',strtolower(trim($update_data['mat_cent_grp_name'])))
				 ->where('cmp_id',$this->company_id)
                ->where('mat_cent_grp_id !=',$mat_cent_grp_id)
        	    ->get()->getRowArray(); 
	    if($table){
	        return ['status' => false, 'message' => 'Name must be unique'];
	    }
	    
	    $table = $this->db->table("matcentgrp")
		       ->where('cmp_id',$this->company_id)
	    	   ->where('mat_cent_grp_id !=',$mat_cent_grp_id)
        	   ->Where('LOWER(mat_cent_grp_alias)',strtolower(trim($update_data['mat_cent_grp_alias'])))
        	   ->get()->getRowArray();
	    if($table){
	        return ['status' => false, 'message' => 'Alias must be unique'];
	    }  
	 $table = $this->db->table("matcentgrp")->where('cmp_id',$this->company_id)->where('mat_cent_grp_id',$mat_cent_grp_id)->update($update_data);		
     if($table){	
	   return ['status' => true, 'account_id' => $mat_cent_grp_id];
	 }
	 return ['status' => false,'message'=>'Something wrong'];
    }      
   public function ajax_group_list(){
	   $builder =  $this->db->table("matcentgrp");
	   $builder->join("undercrsmt", "undercrsmt.crs_mst_id  = matcentgrp.mat_cent_grp_id AND undercrsmt.crs_mst_type =8 AND undercrsmt.cmp_id =$this->company_id", 'left');
	   $builder->where('matcentgrp.cmp_id', $this->company_id);			 	
	   $builder->orderBy('mat_cent_grp_name');
       $result  = $builder->get()->getResultArray();   
	   $records = array();
       foreach($result as $values){
		   $confirmstatus = ($values['crs_is_active']==1)?'INACTIVE':'ACTIVE';	
		    $acc_status_vl  = ($values['crs_is_active']==1)?0:1;  
            $records[] = array(	
					  'checkbox'=>'<input name="group_ids[]" class="checkbox group_row" data-acc_status_vl="'.$acc_status_vl.'" data-confirmstatus= "'.$confirmstatus.'"  data-id="'.$values['mat_cent_grp_id'].'"  type="checkbox" value="'.$values['mat_cent_grp_id'].'">',
                      'group_id'    => $values['mat_cent_grp_id'],
                      'group_name'  => ucwords($values['mat_cent_grp_name']),
					  'alias_name'  => $values['mat_cent_grp_alias'],
					  'grp_primary' => ($values['crs_mst_is_primary']!='' && $values['crs_mst_is_primary']!='0')?'YES':'No',
					  'acc_status_vl'   => ($values['crs_is_active']==1)?0:1,
					  'grp_status'   => ($values['crs_is_active']==1)?'ACTIVE':'INACTIVE',
					  'alert_acc_status'   => ($values['crs_is_active']==1)?'INACTIVE':'ACTIVE',	
				   );  
		     }	

 	       return $records;      
      }

    public function all_mc_group_export(){
		
		$mat_centre_grp_master_tbl = $this->company_id.'_mcgrpmstnn_'.$this->session->get('ses_comp_fy_id');		
        $builder  = $this->db->table($mat_centre_grp_master_tbl); 
        $builder->orderBy('mc_grp_name');	 	
        $result = $builder->get()->getResultArray();   
        
		$records = array();
        foreach($result as $values){
            $records[] = array(	
			          'group_id'    => $values['mc_grp_id'],
                      'group_name'  => ucwords($values['mc_grp_name']),
					  'alias_name'  => $values['mc_alias'],
					  'grp_primary' => ($values['mc_primary']!='' && $values['mc_primary']!='0')?$values['mc_primary']:'No'					  					  	
				   );  
		     }	

 	      	return $records;      
      }
   public function remove_centre_grps($ids,$comp_id){
		$ses_comp_fy_id        =  $this->session->get('ses_comp_fy_id');
		$mat_centre_grp_master_tbl  = $this->company_id.'_mcgrpmstnn_'.$this->session->get('ses_comp_fy_id');
		$ids                   = explode(",",$ids);
        foreach($ids as $centre_group_id){			
			$this->db->table($mat_centre_grp_master_tbl)->where('centre_grp_id',$centre_group_id)->delete();

			$this->delete_comp_fy_mst_map($centre_group_id,'mcgrpmstnn');			
		 }
		return TRUE;
	 }
	
	 
	 public function remove_single_centre_grps($id)
	 {
		  $this->db->table("matcentgrp")->where('cmp_id',$this->company_id)->where('mat_cent_grp_id',$id)->delete();
	      $this->db->table("undercrsmt")->where('cmp_id',$this->company_id)->where('crs_mst_type',8)->where('crs_mst_id',$id)->delete();	
	      return TRUE;
	 }
	 
	 public function get_mc_group_name($id)
	 {
	     $data = $this->db->table("matcentgrp")->select('mat_cent_grp_name')->where('mat_cent_grp_id', $id)->get()->getRowArray();
         $mc_group_name = $data['mat_cent_grp_name'];
         return $mc_group_name;
	 }
	 public function check_mc_group_exists($id,$name){
     $data = $this->db->table("matcentgrp")->select('mat_cent_grp_name')->where('mat_cent_grp_is_active',1)->where('cmp_id',$this->company_id)->where('LOWER(mat_cent_grp_name)', strtolower($name))->get()->getRowArray();
     if($data)
        return true; 
        else
        return false;
     }	
    
	public function changestatus_single_group($id,$status){
 		$this->db->table("matcentgrp")->where('cmp_id',$this->company_id)->where("mat_cent_grp_id",$id)->update(["mat_cent_grp_is_active"=>$status]);
		$this->db->table("undercrsmt")->where('cmp_id',$this->company_id)->where("crs_mst_id",$id)->where("crs_mst_type",8)->where("cmpfymastr_id",$this->fy_id)->update(["crs_is_active"=>$status]); 
    } 
	
	function check_mc_with_group($group_id)
     {
		 $builder =  $this->db->table("matcentmst");
		 $builder->join("undercrsmt", "undercrsmt.crs_mst_id = matcentmst.mat_cent_id AND undercrsmt.crs_mst_type =7 AND undercrsmt.cmp_id =$this->company_id", 'left');
     	 $builder->where('undercrsmt.under_crs_mst_id',$group_id);
		 $data    =  $builder->get()->getRowArray();
		 if($data){
	         return 1;
	     }
	     return 0;
     }

    public function all_mc_export(){
    
        $mat_centre_master_tbl =  $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');			
        $builder   = $this->db->table($mat_centre_master_tbl); 
        $builder->orderBy('mat_cent_name');
        $result = $builder->get()->getResultArray(); 

        $records   = array();		
        foreach($result as $values){

			$centre_group_info = $this->group_info($values['mat_cent_grp_id']);
			if(isset($centre_group_info['mc_grp_name'])){
				$show_group = $centre_group_info['mc_grp_name'];
			}
			else{
				$show_group ='';	
			}			
			$records[] = array(	
					'center_id'      => $values['mat_cent_id'],
					'mat_cent_name'  => $values['mat_cent_name'],
					'mat_cent_alias' => $values['mat_cent_alias'],
					'mat_cent_print' => $values['mat_cent_print'],
					'group_name'     => $show_group 					  
				);  
			}	 	       
			return $records;      
    }
      
      
  public function ajax_centres_list(){
	    $builder   = $this->db->table("matcentmst"); 
		$builder->join("undercrsmt", "undercrsmt.crs_mst_id = matcentmst.mat_cent_id AND undercrsmt.crs_mst_type =7 AND undercrsmt.cmp_id =$this->company_id", 'left');
        $builder->orderBy('mat_cent_name');
		$builder->where('matcentmst.cmp_id', $this->company_id);		 	
        $result    = $builder->get()->getResultArray();  
        $records   = array();		
        foreach($result as $values){
            $centre_group_info = $this->group_info($values['under_crs_mst_id']);
			if(isset($centre_group_info['mat_cent_grp_name'])){
			  $show_group = $centre_group_info['mat_cent_grp_name'];
			  }
			else{
				$show_group ='';	
			  }			
			$confirmstatus = ($values['crs_is_active']==1)?'INACTIVE':'ACTIVE';	
		    $acc_status_vl  = ($values['crs_is_active']==1)?0:1;  
			$records[] = array(	
			          'checkbox'=>'<input name="mc_ids[]" class="checkbox material_centres" data-acc_status_vl="'.$acc_status_vl.'" data-confirmstatus= "'.$confirmstatus.'"  data-id="'.$values['mat_cent_id'].'"  type="checkbox" value="'.$values['mat_cent_id'].'">',
                      'center_id'      => $values['mat_cent_id'],
					  'mat_cent_name'  => $values['mat_cent_name'],
					  'mat_cent_alias' => $values['mat_cent_alias'],
					  'mat_cent_print' => $values['mat_cent_print_name'],
					  'acc_status_vl'   => ($values['crs_is_active']==1)?0:1,
					  'mc_status'   => ($values['crs_is_active']==1)?'ACTIVE':'INACTIVE',
					  'alert_acc_status'   => ($values['crs_is_active']==1)?'INACTIVE':'ACTIVE',
					  'group_name'     => $show_group 					  
				     );  
		     }	 	       
		   return $records;      
      }
   	 
   function centres_group_dropdown(){
	   $builder =  $this->db->table("matcentgrp");
	   $builder->join("undercrsmt", "undercrsmt.crs_mst_id  = matcentgrp.mat_cent_grp_id AND undercrsmt.crs_mst_type =8 AND undercrsmt.cmp_id =$this->company_id", 'left');
       $builder->orderBy('mat_cent_grp_name','ASC');
	   $data         =  $builder->get()->getResultArray();
	   $final_result = array();
	   $final_result['']  = 'Choose';
	   if($data){
		  foreach($data as $row){
              $final_result[$row['mat_cent_grp_id']] = $row['mat_cent_grp_name'];			   
	        }
        }
	  return $final_result;	
     } 
  
  
  function mc_centres_dropdown(){
	   $data         =  $this->db->table("matcentmst")->orderBy('mat_cent_name')->get()->getResultArray();
	   $final_result = array();
	   $final_result['']  = 'Choose';
	   if($data){
		  foreach($data as $row){
              $final_result[$row['mat_cent_id']] = $row['mat_cent_name'];			   
	        }
        }
	  return $final_result;	
     }

  
  
   function mc_store_info($store_id){
      $mat_store_master_tbl =  $this->company_id.'_mcstoremst_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($mat_store_master_tbl)->where('mc_store_id', $store_id)->get()->getRowArray();   	   
   }  
   
     
   function center_info($centre_id){
	   $builder =  $this->db->table("matcentmst");
	   $builder->join("undercrsmt", "undercrsmt.crs_mst_id  = matcentmst.mat_cent_id AND undercrsmt.crs_mst_type =7 AND undercrsmt.cmp_id =$this->company_id", 'left');
       $builder->join("matcentdet", "matcentdet.mat_cent_id  = matcentmst.mat_cent_id AND matcentdet.cmp_id =$this->company_id", 'left');
       $builder->where("undercrsmt.crs_mst_id",$centre_id);
       $builder->where("matcentmst.cmp_id",$this->company_id);		  
	   $row = $builder->get()->getRowArray();
	   return $row;
   }     
   
   function group_info($centre_grp_id){	 
      $builder =  $this->db->table("matcentgrp");
	  $builder->join("undercrsmt", "undercrsmt.crs_mst_id  = matcentgrp.mat_cent_grp_id AND undercrsmt.crs_mst_type =8 AND undercrsmt.cmp_id =$this->company_id", 'left');
      $builder->where("undercrsmt.crs_mst_id",$centre_grp_id);		
	  $row = $builder->get()->getRowArray();  	
	  return $row;   	   
   }   
}