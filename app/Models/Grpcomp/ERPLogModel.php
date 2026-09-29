<?php
namespace App\Models\Grpcomp;
use CodeIgniter\Model;
use App\Libraries\externaldb;

class ERPLogModel extends Model	{
    public function __construct() {
       parent::__construct();        
       $this->externaldb    = new externaldb();	
	   $this->db            = $this->externaldb->get_company_db();
	   $this->session       = \Config\Services::session();
	   $this->db2           = \Config\Database::connect();
	   $this->company_id    = $this->session->get('ses_company_id');
    } 
    
    public function add_log($data)
    {
	   $log_tbl = $this->company_id.'_cmperplogs_'.$this->session->get('ses_comp_fy_id');
	   $this->db->table($log_tbl)->insert($data);
	   return $this->db->insertID();
    }
    
    public function get_user_name($id){
         $data = $this->db2->table('aicountly_useraictly_univdb')->select('user_name')->where('uuid_aicountly', $id)->get()->getRowArray();
         return $data['user_name'];
     }
    
    public function get_logs()
    {
        $log_tbl = $this->company_id.'_cmperplogs_'.$this->session->get('ses_comp_fy_id');
        $builder = $this->db->table($log_tbl);
        $builder->select('log_id, uuid_aicountly, user_aicountly_buisness_id, log_action_tags, log_field_id,log_field_name, log_field_type');
        $builder->select('DATE_FORMAT(log_date,"%d %M %Y") AS log_date');
        $builder->select('DATE_FORMAT(log_time,"%h:%i %p") AS log_time');
        $builder->orderBy('log_id', 'DESC');
        $builder->limit(10);
        $result = $builder->get()->getResultArray();
        $new_array = [];
        if(!empty($result)){
            foreach($result as $row){
                $row['user_name'] = $this->get_user_name($row['uuid_aicountly']);
                $new_array[] = $row;
            }
        }
        return $new_array;
    }
    
    public function get_alllogs()
    {
        $log_tbl = $this->company_id.'_cmperplogs_'.$this->session->get('ses_comp_fy_id');
        $builder = $this->db->table($log_tbl);
        $builder->select('log_id, uuid_aicountly, user_aicountly_buisness_id, log_action_tags, log_field_id,log_field_name, log_field_type');
        $builder->select('DATE_FORMAT(log_date,"%d %M %Y") AS log_date');
        $builder->select('DATE_FORMAT(log_time,"%h:%i %p") AS log_time');
        $builder->orderBy('log_id', 'DESC');
        $result = $builder->get()->getResultArray();
        $new_array = [];
        if(!empty($result)){
            foreach($result as $row){
                $row['user_name'] = $this->get_user_name($row['uuid_aicountly']);
                $new_array[] = $row;
            }
        }
        return $new_array;
    }
}