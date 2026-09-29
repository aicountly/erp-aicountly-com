<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Libraries\externaldb;

class ERPLogModel extends Model	{
	protected $externaldb;
	protected $session;
	protected $aicountly_db;
	protected $company_id;
    public function __construct() {
       parent::__construct();        
       $this->externaldb    = new externaldb();
	   $this->session       = \Config\Services::session();
	   $this->aicountly_db  = $this->externaldb->aicountly_db();	
	   $this->company_id    = $this->session->get('ses_company_id');
    } 
    
    public function add_log($data)
    {
	   $log_tbl = $this->company_id.'_cmperplogs_'.$this->session->get('ses_comp_fy_id');
       $data['uuid_aicountly'] = $this->session->get('uuid');
	   $this->db->table($log_tbl)->insert($data);
	   return $this->db->insertID();
    }
    
    public function get_user_name($id){
         $data = $this->aicountly_db->table('aicountly_useraictly_univdb')->select('user_name')->where('uuid', $id)->get()->getRowArray();
         if($data)
		 return $data['user_name'];
	     else
		 return "N/A";
     }
    
    public function get_logs()
    {
     if($this->session->get('ses_comp_fy_id')){   
        
		$builder = $this->db->table("erpactivty");
		$builder->where('cmp_id',$this->company_id);
        $builder->orderBy('erp_activity_id', 'DESC');
        $builder->limit(10);
        $result = $builder->get()->getResultArray();
        
        return $result;
	  
     }
     else{
				$this->session->destroy();
			}
    }
    
    
    public function get_notifications()
    {
     if($this->session->get('ses_comp_fy_id')){   
         
		$builder = $this->db->table("erpnotifyn");
		$builder->join("vchtxnconso","vchtxnconso.vch_txn_id=erpnotifyn.vch_txn_id");
		$builder->select('erpnotifyn.*,vchtxnconso.vch_type_id');
		$builder->where('erpnotifyn.cmp_id',$this->company_id);
		$builder->where('erpnotifyn.notify_read',1);
		$builder->where('erpnotifyn.uuid_to',$this->session->get('uuid'));
        $builder->orderBy('erpnotifyn.notify_date_time', 'DESC');
        $builder->limit(10);
        $result = $builder->get()->getResultArray();
        return $result;
	  
     }
     else{
				$this->session->destroy();
			}
    }
    
    
    
    public function get_alllogs()
    {
        
        $builder = $this->db->table("erpactivty");
        $builder->orderBy('erp_activity_id', 'DESC');
        $result = $builder->get()->getResultArray();
        return $result;
    }
}