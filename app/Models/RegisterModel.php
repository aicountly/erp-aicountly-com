<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Libraries\cPanelApi;

class RegisterModel extends Model	{
    
    public function __construct() {
        parent::__construct();        
		$this->db            = \Config\Database::connect();		
        $this->session       = \Config\Services::session();
        $this->externaldb    = new externaldb();
		$this->cpanelapi     = new cPanelApi();
    }
    
    public function get_country_array()
    {
        $result = $this->db->table('aicountly_countrylst_univdb')->get()->getResultArray();
        
        return $result;
    }
    
    public function get_state_array()
    {
        $result = $this->db->table('aicountly_stateslist_univdb')->get()->getResultArray();
        
        return $result;
    }
    
    public function add_user($user_data)
    {
	    $sispl_uuid_db = $this->externaldb->sispl_uuid_db();
        $builder = $sispl_uuid_db->table('sispluuid_uuidgenert_univdb');
        $builder->select('sispluuid_uuidgenert_univdb.uuid, uuid_domain_active');
        $builder->join('sispluuid_uuiddomain_univdb', 'sispluuid_uuiddomain_univdb.uuid=sispluuid_uuidgenert_univdb.uuid');
        $builder->where('uuid_regdemail', $user_data['user_regdemail']);
        $result = $builder->get()->getRowArray();        
        if(!empty($result))
        {
            if($result['uuid_domain_active'] == 'erp.aicountly.in'){
                return 0;
            }
            else{
                $uuid = $result['uuid'];
            }
        } 
        else
        {
            $data = [
                'uuid_regdemail' => $user_data['user_regdemail'],
                'uuid_regdmobile' => $user_data['user_regdmobile'],
                'uuid_regdwa' => $user_data['user_wamobile'],
                'uuid_aicountly_status' => '1'
            ]; //0 for pending
            
    	   $sispl_uuid_db->table('sispluuid_uuidgenert_univdb')->insert($data);
    	   $uuid = $sispl_uuid_db->insertID();
        }
	   
	   $user_data['uuid'] = $uuid;
	   $this->db->table('aicountly_useraictly_univdb')->insert($user_data);
	   $uuid_aicountly = $this->db->insertID();
	   
	   $domain_data = [
	       'uuid' => $uuid,
	       'uuid_domain_active' => 'erp.aicountly.in',
	       'uuid_domain_status' => 'active'
	       ];
	       
	   $sispl_uuid_db->table('sispluuid_uuiddomain_univdb')->insert($domain_data); 
	   
	   
	    // Create uuid_{$uuid}  new db to store baseidgenrt table & cofymstmap table 
		$gen_db_nbame        = 'aicountlyin_uuid_'.$uuid;	
		$gen_username_name   = 'aicountlyin_uuid_'.$uuid.'_usr';	
		$gen_user_passwpord  = getenv("DB_USR_PASSWORD");
		 
		 $db   =   $this->cpanelapi->createDataBaseMySQL($gen_db_nbame);
		 $user =   $this->cpanelapi->createUserMySQL($gen_username_name, $gen_user_passwpord);

		 $prev =   $this->cpanelapi->setPrivilegesMySQL($gen_username_name,$gen_db_nbame);
         $this->db->close();
	     $external_uuid_db = $this->externaldb->connect_uuid_db($gen_db_nbame,$gen_username_name,$uuid);
	     $cofymstmap_tbl    = 'cofymstmap';
	     $baseidgenrt_tbl   = 'baseidgenrt';
         $external_uuid_db->query("CREATE TABLE IF NOT EXISTS `".$cofymstmap_tbl."` (`cofymst_id` BIGINT NOT NULL AUTO_INCREMENT,`comp_id` BIGINT NOT NULL , `comp_fy_id` BIGINT NOT NULL , `mst_type` VARCHAR(5) , `mst_base_id` BIGINT DEFAULT 0 , `mst_fy_id` BIGINT NOT NULL , PRIMARY KEY (`cofymst_id`),KEY `cofymstindex` (`comp_id`,`comp_fy_id`,`mst_type`,`mst_base_id`,`mst_fy_id`)) ENGINE = InnoDB;" 	);
         $external_uuid_db->query("CREATE TABLE IF NOT EXISTS `".$baseidgenrt_tbl."` (`mst_base_id` BIGINT NOT NULL AUTO_INCREMENT,`comp_id` BIGINT NOT NULL , `mst_type` VARCHAR(5) , PRIMARY KEY (`mst_base_id`),KEY `baseindex` (`comp_id`,`mst_type`)) ENGINE = InnoDB;" 	);

	     return $uuid;
    }
    public function is_email_unique($email)
    {
        $sispl_uuid_db = $this->externaldb->sispl_uuid_db();
        $builder = $sispl_uuid_db->table('sispluuid_uuidgenert_univdb');
        $builder->join('sispluuid_uuiddomain_univdb', 'sispluuid_uuiddomain_univdb.uuid=sispluuid_uuidgenert_univdb.uuid');
        $builder->where('uuid_regdemail', $email);
        $builder->where('uuid_domain_active', 'erp.aicountly.in');
        $result = $builder->get()->getRowArray();
        if(empty($result)){
            return true;
        }
        return false;
    }
    public function confirm_user($uuid)
    {
        $sispl_uuid_db = $this->externaldb->sispl_uuid_db();
        $response = $sispl_uuid_db->table('sispluuid_uuidgenert_univdb')->select('uuid_aicountly_status')->where('uuid', $uuid)->get()->getRowArray();
        if($response['uuid_aicountly_status'] == '2'){
            return false;
        }
        else{
            $sispl_uuid_db->table('sispluuid_uuidgenert_univdb')->where('uuid', $uuid)->update(['uuid_aicountly_status' => '2']);
            return true;
        }
        
    }
    public function add_password($uuid, $pass)
    {
        $response = $this->db->table('aicountly_useraictly_univdb')->select('uuid_aicountly')->where('uuid', $uuid)->get()->getRowArray();
        
        $this->db->table('aicountly_useraictly_univdb')->where('uuid_aicountly', $response['uuid_aicountly'])->update(['user_pass' => $pass]);
        
        return $response['uuid_aicountly'];
    }
    public function get_user_email($id)
    {
        $sispl_uuid_db = $this->externaldb->sispl_uuid_db();
        $response = $sispl_uuid_db->table('sispluuid_useraictly_univdb')->select('user_regdemail')->where('uuid_aicountly', $id)->get()->getRowArray();
        
        return $response['user_regdemail'];
    }
    public function get_sms_trigger()
    {
        $shivansh_db = $this->externaldb->get_shivansherp_db();
        return $shivansh_db->table('trigger_type_sms')->where('workflow_trigger_id', 40)->get()->getRowArray();
    }
    public function get_email_trigger()
    {
        $shivansh_db = $this->externaldb->get_shivansherp_db();
        return $shivansh_db->table('trigger_type_email')->where('workflow_trigger_id', 36)->get()->getRowArray();
    }

    public function createAccount($email)
    {
        $sispl_uuid_db = $this->externaldb->sispl_uuid_db();
        $builder = $sispl_uuid_db->table('sispluuid_uuidgenert_univdb');
        $builder->select('sispluuid_uuidgenert_univdb.uuid, uuid_domain_active');
        $builder->join('sispluuid_uuiddomain_univdb', 'sispluuid_uuiddomain_univdb.uuid=sispluuid_uuidgenert_univdb.uuid');
        $builder->where('uuid_regdemail', $email);
        $result = $builder->get()->getRowArray();
        
        if(!empty($result))
        {
            if($result['uuid_domain_active'] == 'erp.aicountly.in'){
                return 0;
            }
            else{
                $uuid = $result['uuid'];
            }
        } 
        else
        {
            $data = [
                'uuid_regdemail' => $email,
                'uuid_regdmobile' => '',
                'uuid_regdwa' => '',
                'uuid_aicountly_status' => '0'
            ]; //0 for pending
            
           $sispl_uuid_db->table('sispluuid_uuidgenert_univdb')->insert($data);
           $uuid = $sispl_uuid_db->insertID();
        }
       
        $user_pass = getRandomString();
        $user_pass_hash = password_hash($user_pass, PASSWORD_BCRYPT);

       $user_data = [
           'uuid' => $uuid,
           'user_firstname' => trim(explode("@",$email)[0]),
           'user_regdemail' => $email,
           'user_name' => $email,
           'user_pass' => $user_pass_hash, 
       ];
       
       $this->db->table('aicountly_useraictly_univdb')->insert($user_data);
       $uuid_aicountly = $this->db->insertID();
       
       $domain_data = [
           'uuid' => $uuid,
           'uuid_domain_active' => 'erp.aicountly.in',
           'uuid_domain_status' => 'active'
           ];
           
       $sispl_uuid_db->table('sispluuid_uuiddomain_univdb')->insert($domain_data); 
       
       
       return $uuid_aicountly;
    }
}