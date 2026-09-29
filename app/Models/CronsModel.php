<?php
namespace App\Models;
use CodeIgniter\Model;
use App\Libraries\enc_string;
use App\Libraries\externaldb;
use App\Libraries\cPanelApi;

class CronsModel extends Model{
  
   public function __construct() {
        parent::__construct();        
		$this->db            = \Config\Database::connect();		
        $this->session       = \Config\Services::session();
        $this->company_id    =  $this->session->get('ses_company_id');
        $this->user_id       =  $this->session->get('uuid_aicountly');
		$this->enc_string    = new enc_string();
		$this->externaldb    = new externaldb();
		$this->cpanelapi     = new cPanelApi();
    }
  
  	public function ajax_recyclebin_company_list(){
	    
		$records = array();
        $builder = $this->db->table("aictlyerp_compmastern_univdb"); 
        $builder->where('comp_db_status','recycled');
        $builder->where("DATEDIFF(CURDATE(),STR_TO_DATE(`recycled_date`, '%Y-%m-%d')) >",30);
		$builder->orderBy('comp_code','DESC');		 
		$result = $builder->get()->getResultArray();
		foreach($result as $values){
		   $db_name    = $values['comp_code'];
		   $company_id = $values['comp_id'];
		   $this->remove_company_permanent($db_name,$company_id);
		  } 
		  return true;
   }
   
   
   public function ajax_recyclebin_vouchers_list(){
	    // get all dbs and then check company id fy to access table accttxnoth and itemtxnoth
	    $table_name =  '_acctcrsref_';
	    $response   =   $this->cpanelapi->TotalDataBaseMySQL();
    	$response   =   json_decode($response);
    	$db_result  =   $response->data;
	    if($db_result){
	       foreach($db_result as $dbrow){
	            $db_name        = $dbrow->database;
	            $username       = $dbrow->users[0];
	            
	            if($db_name!='aicountlyin_erpacountly'){
	            $upasswd        ='(316RR7uUs.p';
	            $comp_db        = $this->externaldb->company_db($db_name,$username,$upasswd);
	           
	           
	          $query_result    = $comp_db->query("SELECT TABLE_NAME FROM INFORMATION_SCHEMA.TABLES WHERE TABLE_NAME LIKE '%".$table_name."%'");
	          $tables_info     = $query_result->getResultArray();
	           if($tables_info){
	                   foreach($tables_info as $table_row){
	                      $db_table_info = explode("_acctcrsref_",$table_row['TABLE_NAME']);
	                      $company_id    = $db_table_info[0];
	                      $comp_fy_id    = $db_table_info[1];
	                      $tblname       = trim($table_row['TABLE_NAME']);
	                      $accttxnoth    = $company_id.'_accttxnoth_'.$comp_fy_id;
	                      
	                     $tablresult     = $comp_db->query("SELECT * FROM `".$accttxnoth."` as `accoth`,`".$tblname."` as `acctcrsref` WHERE `accoth`.`voucher_txn_id`= `acctcrsref`.`voucher_txn_id` AND 
	                                                       `accoth`.`comp_id`= `acctcrsref`.`comp_id` AND `accoth`.`acc_oth_txn_tag`='recycled' AND 
	                                                        DATEDIFF(CURDATE(),STR_TO_DATE(`acctcrsref`.`acc_cross_logdate`, '%Y-%m-%d')) >=1 
	                                                       ");
	                     $tablesss_info  = $tablresult->getResultArray();
	                     if($tablesss_info){
	                    echo'<pre>';
	                    print_r($tablesss_info);
	                     }
	                     }
	                  }
	               }
	             }
	          }
           }
  
  public function remove_company_permanent($db_name,$company_id){
      
   $db_usernaem =   getenv("DB_PREFIX_NAME").'u'.$db_name;
   $dbname      =   getenv("DB_PREFIX_NAME").$db_name;
   $db          =   $this->cpanelapi->deleteDataBaseMySQL($dbname);
   $user        =   $this->cpanelapi->deleteUserMySQL($db_usernaem);
  
   $this->db->table("aictlyerp_cmpfymastr_univdb")->where('comp_id',$company_id)->delete();
   $this->db->table("aictlyerp_cmpidacsnn_univdb")->where('comp_id',$company_id)->delete();
   $this->db->table("aictlyerp_cmpstatus_univdb")->where('comp_id',$company_id)->delete();
   $this->db->table("aictlyerp_compidgenr_univdb")->where('comp_id',$company_id)->delete();
   $this->db->table("aictlyerp_compmastern_univdb")->where('comp_id',$company_id)->delete();
  }  
   
}