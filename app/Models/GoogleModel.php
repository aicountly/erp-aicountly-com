<?php
namespace App\Models;

use CodeIgniter\Model;
use App\Libraries\externaldb;

class GoogleModel extends Model{

     public function __construct() {
        parent::__construct();        
        $this->session 	= \Config\Services::session();
        $this->externaldb    = new externaldb();
        $this->db            = \Config\Database::connect();	
    }


    function glogin($email,$full_name){
         
         $full_name_info = explode(" ", $full_name);
         $first_name     = $full_name_info[0];
         $last_name      = isset($full_name_info[1]) ? $full_name_info[1] : '';
         
         $email = trim($email);
	     $info  = $this->db->table('aicountly_useraictly_univdb')->where('user_regdemail', $email)->get()->getRowArray();   	   
	     if($info){
	         
	        $user_info =  $info;
	     }
          
         else{
          
          	$sispl_uuid_db = $this->externaldb->sispl_uuid_db();

            $builder = $sispl_uuid_db->table('sispluuid_uuidgenert_univdb');
            $builder->select('sispluuid_uuidgenert_univdb.uuid, uuid_domain_active');
            $builder->join('sispluuid_uuiddomain_univdb', 'sispluuid_uuiddomain_univdb.uuid=sispluuid_uuidgenert_univdb.uuid');
            $builder->where('uuid_regdemail', $email);
            $result = $builder->get()->getRowArray();
            
            if(!empty($result)){
                $uuid = $result['uuid'];  
            } 
            else{
                $data = [
                    'uuid_regdemail' => $email,
                    'uuid_regdmobile' => '',
                    'uuid_regdwa' => '',
                    'uuid_aicountly_status' => '1'
                ]; //1 for pending
                
               $sispl_uuid_db->table('sispluuid_uuidgenert_univdb')->insert($data);
               $uuid = $sispl_uuid_db->insertID();
            }
          	

    	   $user_data = array(
    	   		'uuid' => $uuid,
    	   		'user_firstname' => $first_name,
    	   		'user_midname' => "",
    	   		"user_lastname" => $last_name,
    	   		"user_name" => $email,
    	   		"user_regdemail" => $email
    	   	);
		   $this->db->table('aicountly_useraictly_univdb')->insert($user_data);
		   $uuid_aicountly = $this->db->insertID();
           
           $user_info  = $this->db->table('aicountly_useraictly_univdb')->where('user_regdemail', $email)->get()->getRowArray();

            $domain_data = [
		       'uuid' => $uuid,
		       'uuid_domain_active' => 'erp.aicountly.in',
		       'uuid_domain_status' => 'active'
	        ];
	       
	   		$sispl_uuid_db->table('sispluuid_uuiddomain_univdb')->insert($domain_data);         
         
         }
         return $user_info;    
    }
	
}
	
