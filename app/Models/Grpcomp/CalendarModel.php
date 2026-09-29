<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
class CalendarModel extends Model	{
	 public function __construct() {
       parent::__construct();        
       $this->session        = \Config\Services::session();	  
    }
 
   public function total_events($currentDate){
		/* $result = $this->db->query("SELECT COUNT(*) as `total_events` FROM `events` WHERE `date`='".$currentDate."'   ")->getRowArray();
		if(isset($result))
		  $total_events =$result['total_events'];
         else
          $total_events =0;	
          
       return $total_events; */	
return "5";	   
 	}
 	
 	public function all_events($currentDate){
		return array();	 
		//$result = $this->db->query("SELECT * FROM `events` WHERE `date`='".$currentDate."' AND `status`='1'   ")->getResultArray();
	
       //return $result;	  
 	}
 
}