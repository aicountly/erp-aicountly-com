<?php
namespace App\Models\Admin;

use CodeIgniter\Model;
use App\Libraries\externaldb;


class BackupModel extends Model	{

	public function __construct() {
		parent::__construct();        
		$this->externaldb    = new externaldb();	
		$this->session       =  \Config\Services::session();
    $this->comp_id    =  $this->session->get('ses_company_id');
    $this->db            = $this->externaldb->get_company_db();
  }

  function db_backup()
  {
    $tables = $this->db->query("SHOW TABLES")->getResultArray();
    $text = '';
    
    foreach($tables as $k => $val)
    {
      $arr = array_keys($val);
      $table = $val[$arr[0]];
      

      $text .= "\n";
      $text .= "DROP TABLE ".$table.";\n";

      $structure = $this->db->query("SHOW CREATE TABLE `".$table."`;")->getRowArray();
      $text .= $structure['Create Table'].";\n";
      
      $result = $this->db->query("SHOW columns FROM `".$table."`;")->getResultArray();
      $result2 = $this->db->query("SELECT * FROM `".$table."`;")->getResultArray();
      
      foreach($result2 as $key2 => $value2){
        $text .= "INSERT INTO ".$table." VALUES(";
        
        foreach($result as $key => $value){
            $field = $value['Field'];
            
            $var = '""';
            if(isset($value2[$field]))
                $var = $value2[$field];
            
            $var = addslashes($var);
            $text .= '"'.$var.'"';
            
            if($key != count($result) - 1)
                $text .= ',';
        }
        
        $text .= ");\n";
      }   
    }
      
    return $text;
      
  }
}