<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Libraries\externaldb;

class LogModel extends Model	{
    protected $db;
	protected $session;
	protected $externaldb;
    public function __construct() {
        parent::__construct();        
		$this->db            = \Config\Database::connect();		
        $this->session       = \Config\Services::session();
        $this->externaldb    = new externaldb();
    }
    
    public function add_log()
    {
    	$aicountly_db = $this->externaldb->aicountly_db();
        $data = [
            'uuid' => $this->session->get('uuid'),
            'uuid_domain' => 'aicountly.com'
            ];
	   $aicountly_db->table('aicountly_globallogs_univdb')->insert($data);
	   $log_id = $aicountly_db->insertID();
	    
	   $log = [
	       'log_id' => $log_id,
	       'last_accessed' => date('Y-m-d H:i:s'),
	       'access_type' => 'login'
	       ];
	       
	   $aicountly_db->table('aicountly_loginlogsn_univdb')->insert($log);
    }
}