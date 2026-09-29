<?php
namespace App\Models\Admin;

use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Libraries\enc_string;
use App\Models\CommonModel;
use App\Libraries\UUIDtables;

class PrintingConfigModel extends Model	{

  public function __construct() {
    parent::__construct();        
    $this->externaldb    = new externaldb();	
    $this->CommonModel   = new CommonModel();
    $this->db            =  $this->externaldb->get_company_db();
    $this->session       =  \Config\Services::session();
	$this->erp_db  = $this->externaldb->erp_db();
		
    $this->company_id    =  $this->session->get('ses_company_id');
    $this->comp_fy_id    =  $this->session->get('ses_comp_fy_id');
    $this->user_id       =  $this->session->get('uuid_aicountly');
    $this->enc_string    =  new enc_string();
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
  
  function save_template_changes(){
	 
	  $prntconfig_style     = trim($_POST['fldstyle']);	  
      $vch_series_id        = trim($_POST['vch_srs_id']);	 
	  $usr_config_id        = trim($_POST['usr_config_id']);
	  $erpprevaln_label_id  = trim($_POST['erpprevaln_label_id']);
	  
	  if($erpprevaln_label_id==105 && isset($_POST['terms_conditions'])){
		$prntconfig_label     = trim($_POST['terms_conditions']);
		$result = explode(PHP_EOL, $prntconfig_label);
		$html_term_cond='';
		$html_term_cond .='<ol style="padding-left:15px;list-style-type:none;">';
		
		if($result){
			foreach($result as $row){
				$html_term_cond .='<li>'.$row.'</li>';
			}
		}
		$html_term_cond .='</ol>';
		$prntconfig_label =$html_term_cond;
	  }
	  else{
	    $prntconfig_label     = trim($_POST['rcpt_title']);	  
	  }
	  $prntconfig_tbl       = $this->company_id.'_prntconfig_'.$this->session->get('ses_comp_fy_id');	  
	  $response_exists      = $this->db->table($prntconfig_tbl)->select('prntconfig_id')->where('vch_series_id',$vch_series_id)->where('erpprevaln_label_id',$erpprevaln_label_id)->where('usr_config_id',$usr_config_id)
	                                   ->get()->getRowArray();
	  if($response_exists){
		  $updata         = array('prntdsg_style'=>$prntconfig_style,'prntdsg_value'=>$prntconfig_label);
          $this->db->table($prntconfig_tbl)->where('usr_config_id',$usr_config_id)->where('erpprevaln_label_id',$erpprevaln_label_id)->update($updata); 	  
	    }					 
	  else{
	      $data           = array('comp_id'=>$this->company_id,'vch_series_id'=>$vch_series_id,'erpprevaln_label_id'=>$erpprevaln_label_id,'usr_config_id'=>$usr_config_id,
	                              'prntdsg_style'=>$prntconfig_style,'prntdsg_value'=>$prntconfig_label);
          $this->db->table($prntconfig_tbl)->insert($data); 
		  $prntconfig_id =  $this->db->insertID();

		  $mst_base_id = $this->create_mst_base_id($prntconfig_id,'prntconfig');
		  $this->db->table($prntconfig_tbl)
					->where('prntconfig_id',$prntconfig_id)
					->update(['mst_base_id' => $mst_base_id]);	
	    }
    }
    
    
    
  function get_default_template($data){
	$response=array();  
	$usrprefnn_tbl = $this->company_id.'_usr_prefnn';
    $builder = $this->db->table($usrprefnn_tbl);
	$builder->where('uuid', $data['uuid']);
	$builder->where('usr_config_id', $data['usr_config_id']);
    $builder->whereIn('usr_config_value',$data['usr_config_value']);
    $response = $builder->get()->getRowArray();
	//echo $this->db->GetLAstQuery();    
	 return $response;
  }
  
  function save_default_template($data){  
    $usrprefnn_tbl = $this->company_id.'_usr_prefnn';
    $builder = $this->db->table($usrprefnn_tbl);
    $builder->where('uuid', $data['uuid']);
    $builder->where('usr_config_id', $data['usr_config_id']);   	
    $exists = $builder->get()->getRowArray();
    if($exists){
      //update
	  $update_data = array("usr_config_value"=>$data['usr_config_value']);
	  $this->db->table($usrprefnn_tbl)->where('uuid', $data['uuid'])
	           ->where('usr_config_id', $data['usr_config_id'])			  
			   ->update($update_data);
    
    }else{
		//insert
	  $this->db->table($usrprefnn_tbl)->insert($data);    
	 }
    
  }
  function receipt_labels_dropdown($data){
	  $erpprevaln_tbl='aictlyerp_erpprevaln_univdb';
	  $final_array=array();
	  $final_array['']='Choose';
	foreach($data as $label_id => $label){
		$db_label_info = $this->erp_db->table($erpprevaln_tbl)->select('erppreval_id,erppreval_data')->where('erppreval_token','')->where('erppreval_id',$label_id)->get()->getRowArray();
	    if($db_label_info){
			$label_name = $db_label_info['erppreval_data'];
			
			$final_array[$db_label_info['erppreval_id']]=$db_label_info['erppreval_data'];
		}      
	}  
	return $final_array;  
  }
  
 function get_item_info($item_id)
  {
      $itemmaster_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
      $builder = $this->db->table($itemmaster_tbl);
      $builder->select('item_name');
      $builder->where('item_id',$item_id);  
      return $builder->get()->getRowArray();
  }

}