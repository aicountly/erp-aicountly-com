<?php
namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\externaldb;

class LoginModel extends Model{

   
     public function __construct() {
        parent::__construct();        
        $this->session 	= \Config\Services::session();	
        $this->db            = \Config\Database::connect();
        $this->externaldb    = new externaldb();
    }
    
    public function get_user_by_email($email)
    {
        $sispl_uuid_db = $this->externaldb->postgr_univaictlydb_old();
        return $sispl_uuid_db->table('uuidaictly')->where('user_regdemail', $email)->get()->getRowArray();
    }
    
     public function get_user_by_username($email)
    {
        $sispl_uuid_db = $this->externaldb->postgr_univaictlydb_old();
        return $sispl_uuid_db->table('uuidaictly')->where('user_name', $email)->get()->getRowArray();
    }
    
    public function confirm_user($uuid)
    {    $sispl_uuid_db = $this->externaldb->postgr_univaictlydb_old();
        return $response = $sispl_uuid_db->table('uuidaictly')->select('user_regdemail')->where('uuid', $uuid)->get()->getRowArray();
    }
    public function get_sms_trigger()
    {
        $shivansh_db = $this->externaldb->get_shivansherp_db();
        return $shivansh_db->table('trigger_type_sms')->where('workflow_trigger_id', 31)->get()->getRowArray();
    }
    public function get_email_trigger()
    {
        $shivansh_db = $this->externaldb->get_shivansherp_db();
        return $shivansh_db->table('trigger_type_email')->where('workflow_trigger_id', 31)->get()->getRowArray();
    }

    function check_auth($auth_code){
        
        $aicountly_db = $this->externaldb->postgr_myaicountlydb();
        
       $data = $aicountly_db->table('useraictly')->where('auth_code', $auth_code)->get()->getRowArray();
       if($data)
       {
           if($data['auth_time'] >= time() - (2*60)) // 2 minute
           {
               return ['status' => true, 'data' => $data];
           }
       }
       return ['status' => false];
   }
	
}