<?php
namespace App\Models\Admin;

use CodeIgniter\Model;
use App\Libraries\externaldb;

class UniversalModel extends Model	{
	protected $externaldb;
	protected $session;
	protected $company_id;
	protected $bo_id;
	protected $uuid;
	protected $aicountly_db;
	 public function __construct() {
	    	 
       parent::__construct();        
       $this->externaldb    = new externaldb();	
	   $this->session       =  \Config\Services::session();
	   $this->company_id    =  $this->session->get('ses_company_id');
	   $this->bo_id         =  $this->session->get('ses_boid');
	   $this->uuid          =  $this->session->get('uuid');
	   $this->aicountly_db  =  $this->externaldb->aicountly_db();	 
    }
	
	function show_states_lists($show_empty=''){
	   $final    = array();
	   if($show_empty=='')
	   $final['']   = 'Choose';
	   $response =  $this->aicountly_db->table('aicountly_stateslist_univdb')->select(['state_name','state_code'])->orderBy('state_name')->get()->getResultArray();
	   foreach($response as $row){
	   		$state_code = sprintf( '%02d', $row['state_code'] );
	       $final[$state_code] = $row['state_name'].'('.$state_code.')';
	   }
	   return $final;	   
     }
	
}