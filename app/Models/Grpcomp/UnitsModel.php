<?php
namespace App\Models\Grpcomp;

use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Libraries\enc_string;

class UnitsModel extends Model	{
	 protected $table = 'item_unit_master';
	 protected $primaryKey = 'unit_id';
     public function __construct() {
        parent::__construct();        
       $this->externaldb    = new externaldb();	
	   $this->db            = $this->externaldb->get_company_db();
	   $this->session       = \Config\Services::session();
	   $this->company_id    =  $this->session->get('ses_company_id');
	   $this->enc_string    = new enc_string();
    }
 
   public function ajax_units_list(){
	   $pq_curPage =1;
	   
	    if($this->company_id){
			
        $base_url = base_url().'/'.getenv('AdminPath');   
        $item_unit_master_tbl = $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');	 
        
        $builder = $this->db->table($item_unit_master_tbl); 	
        $builder->orderBy('Item_unit');		  
        $result  = $builder->get()->getResultArray();
		$records = array();
		if($result){		
		foreach($result as $values){            
	        $records[] = array(
			    'unit_id'         => $values['unit_id'],
				'item_unit'       => $this->enc_string->nc_string($values['item_unit'],'de'),		
				'item_unit_alias' => $this->enc_string->nc_string($values['item_unit_alias'],'de'),
				'item_unit_print' => $this->enc_string->nc_string($values['item_unit_print'],'de'),
				'item_unit_uqc'   => $this->enc_string->nc_string($values['item_unit_uqc'],'de'),	
			   );
		     }
		   }
		}
		else{
			$records=array();
		
		}
		$totalrec= count($records);
 echo  "{\"totalRecords\":" .$totalrec . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($records)."}"; 
      		
     }
	 function get_info($unit_id){	 
	 $item_unit_master_tbl = $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');	
	  return $this->db->table($item_unit_master_tbl)->where('unit_id', $unit_id)->get()->getRowArray();   	   
   } 
   
   	public function get_item_unit_name($id)
	{
	    $item_unit_master_tbl = $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');
	    $data = $this->db->table($item_unit_master_tbl)->select('item_unit')->where('unit_id', $id)->get()->getRowArray();
        $name = $this->enc_string->nc_string($data['item_unit'],'de');
        return $name;
	}
	
	function check_item_with_unit($id)
     {
         $item_master_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
	     $data =  $this->db->table($item_master_tbl)->where('item_unit', $id)->get()->getRowArray();
	     if(!empty($data)){
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
		 }
		return TRUE;
	 }
	 
    public function remove_single_units($id)
    {
        $item_unit_master_tbl  = $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');
        $this->db->table($item_unit_master_tbl)->where('unit_id',$id)->delete();
    }
	 
	 
    public function update_unit($unit_id,$update_data){
		$item_unit_master_tbl =$this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');	
	    $this->db->table($item_unit_master_tbl)->where('unit_id',$unit_id)->update($update_data);		
		return true;	
   } 
   function add_units($insert_data){
	   $item_unit_master_tbl = $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');	
	   $item_unit = $this->enc_string->nc_string($insert_data['item_unit'],'de');	 
	   $exists = $this->db->table($item_unit_master_tbl)->where('comp_id',$insert_data['comp_id'])->where('item_unit',$item_unit)->countAllResults();   
	   if($exists ==0){
	     $this->db->table($item_unit_master_tbl)->insert($insert_data);		
	     return $this->db->insertID();	 
	   }
	   else
		  return "0"; 	   
     }

}