<?php
namespace App\Models\Admin;

use CodeIgniter\Model;
use App\Libraries\auth_session; 

class UnitsModel extends Model	{
	 public function __construct() {
       parent::__construct();               
	   $this->session       = \Config\Services::session();
	   $this->company_id    =  $this->session->get('ses_company_id');
	   $this->fy_id         =  $this->session->get('ses_comp_fy_id');
	   $this->bo_id         =  $this->session->get('ses_boid');   
    }

   public function ajax_units_list(){	  
        $builder = $this->db->table("itmunitmst"); 	
        $builder->where('cmp_id', $this->company_id);	
		$builder->orderBy('itm_unit_name');		  
        $result  = $builder->get()->getResultArray();
		$records = array();
		if($result){		
		foreach($result as $values){            
	        $records[] = array(
			    'unit_id'         => $values['itm_unit_id'],
				'item_unit'       => $values['itm_unit_name'],		
				'item_unit_alias' => $values['itm_unit_alias'],
				'item_unit_print' => $values['itm_unit_print'],
				'item_unit_uqc'   => $values['itm_unit_uqc'],	
			   );
		    }
		}
		 return $records;      
     }

     public function all_units_export(){
        $item_unit_master_tbl = $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');	 
        $builder = $this->db->table($item_unit_master_tbl);
		$builder->where('cmp_id', $this->company_id);		
        $builder->orderBy('Item_unit');		  
        $result  = $builder->get()->getResultArray();
		$records = array();
		if($result){
		
		foreach($result as $values){            
	        $records[] = array(
			    'unit_id'         => $values['unit_id'],
				'item_unit'       => $values['item_unit'],		
				'item_unit_alias' => $values['item_unit_alias'],
				'item_unit_print' => $values['item_unit_print'],
				'item_unit_uqc'   => $values['item_unit_uqc'],	
			   );
		    }
		}
		   return $records;      
     }

	 function get_info($unit_id){	 
	  return $this->db->table("itmunitmst")->where('cmp_id', $this->company_id)->where('itm_unit_id', $unit_id)->get()->getRowArray();   	   
   } 
   
   	public function get_item_unit_name($id)
	{
	    $data = $this->db->table("itmunitmst")->select('itm_unit_name')->where('cmp_id', $this->company_id)->where('itm_unit_id', $id)->get()->getRowArray();
        $name = $data['itm_unit_name'];
        return $name;
	}
	
	function check_item_with_unit($id)
     {
         $builder =  $this->db->table("itemmaster");
		 $builder->where('itemmaster.itm_def_unit_id',$id);
		 $builder->where('itemmaster.cmp_id',$this->company_id);
		 $data    =  $builder->get()->getRowArray();
		 if($data){
	         return 1;
	     }
	     return 0;
     }
    
	 public function remove_units($ids,$comp_id){
		$ses_comp_fy_id        =  $this->session->get('ses_comp_fy_id');
		$item_unit_master_tbl  = $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');	
		$ids                   = explode(",",$ids);
        foreach($ids as $unit_id){			
			$this->db->table($item_unit_master_tbl)->where('unit_id',$unit_id)->delete();	
			$this->delete_comp_fy_mst_map($unit_id,'itmunitmst');		
		 }
		return TRUE;
	 }
	 
    public function remove_single_units($id)
    {   
	    $this->db->table("itmunitmst")->where('cmp_id',$this->company_id)->where('itm_unit_id',$id)->delete();
	    return TRUE;		
    }
	 
	 
    public function update_unit($itm_unit_id,$update_data){
		$table = $this->db->table("itmunitmst")
	    		->where('LOWER(itm_unit_name)',strtolower(trim($update_data['itm_unit_name'])))
				->where('cmp_id',$this->company_id)
                ->where('itm_unit_id !=',$itm_unit_id)
        	    ->get()->getRowArray(); 
	    if($table){
	        return ['status' => false, 'message' => 'Name must be unique'];
	    }
	    
	    $table = $this->db->table("itmunitmst")
		       ->where('cmp_id',$this->company_id)
	    	   ->where('itm_unit_id !=',$itm_unit_id)
        	   ->Where('LOWER(itm_unit_alias)',strtolower(trim($update_data['itm_unit_alias'])))
        	   ->get()->getRowArray();
	     if($table){
	        return ['status' => false, 'message' => 'Alias must be unique'];
	     }  
		 
		 $table = $this->db->table("itmunitmst")
		       ->where('cmp_id',$this->company_id)
	    	   ->where('itm_unit_id !=',$itm_unit_id)
        	   ->Where('LOWER(itm_unit_print)',strtolower(trim($update_data['itm_unit_print'])))
        	   ->get()->getRowArray();
	     if($table){
	        return ['status' => false, 'message' => 'Print name must be unique'];
	     }  
		 
		 $table = $this->db->table("itmunitmst")->where('cmp_id',$this->company_id)->where('itm_unit_id',$itm_unit_id)->update($update_data);		
		 if($table){	
		   return ['status' => true, 'account_id' => $itm_unit_id];
		 }
		 return ['status' => false,'message'=>'Something wrong'];
	 	
   } 
   function add_units($data){
	   $exists1 = $this->db->table("itmunitmst")->where('cmp_id',trim($data['cmp_id']))
	              ->where('LOWER(itm_unit_name)', strtolower(trim($data['itm_unit_name'])))
				  ->get()->getRowArray(); 
	   $exists2 = $this->db->table("itmunitmst")->where('cmp_id',trim($data['cmp_id']))
	              ->where('LOWER(itm_unit_alias)', strtolower(trim($data['itm_unit_alias'])))
				  ->get()->getRowArray(); 
	   $exists3 = $this->db->table("itmunitmst")->where('cmp_id',trim($data['cmp_id']))
	              ->where('LOWER(itm_unit_print)', strtolower(trim($data['itm_unit_print'])))
				  ->get()->getRowArray(); 			  
	    if($exists1)
		 return "-1";
	    else if($exists2)
		 return "-2";
	   else if($exists3)
		 return "-3";
	    else{
		  $this->db->table("itmunitmst")->insert($data);
		  $itm_unit_id = $this->db->insertID();
		  return $itm_unit_id;
	     }
	  	   
     }

}