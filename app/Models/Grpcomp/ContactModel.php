<?php
namespace App\Models\Admin;

use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Libraries\enc_string;

class ContactModel extends Model	{

    public function __construct() {
        parent::__construct();        
      $this->externaldb    = new externaldb();	
	   //$this->db            = $this->externaldb->get_company_db();
	   $this->db            = \Config\Database::connect();
	   $this->session       = \Config\Services::session();
	   $this->company_id    =  $this->session->get('ses_company_id');
	   $this->enc_string    = new enc_string();
    }
    
    public function get_email_trigger($id)
    {
        $shivansh_db = $this->externaldb->get_shivansherp_db();
        return $shivansh_db->table('trigger_type_email')->where('workflow_trigger_id', $id)->get()->getRowArray();
    }

    public function get_user($id)
    {
        $response = $this->db->table('aicountly_useraictly_univdb')->where('uuid_aicountly', $id)->get()->getRowArray();
        
        return $response;
    }

    public function add_contact($data)
    {
        $this->db->table('aicountly_contactaic_univdb ')->insert($data);
    }
    
    public function update_contact($uuid_aicountly,$uuid_contactid, $data)
    {
        $this->db->table('aicountly_contactaic_univdb')->where('uuid_aicountly', $uuid_aicountly)->where('uuid_contactid', $uuid_contactid)->update($data);
    }
    
    public function update_contact_status($uuid_aicountly, $status)
    {
        $uuid_contactid = $this->session->get('uuid_aicountly');
        $this->db->table('aicountly_contactaic_univdb')->where('uuid_aicountly', $uuid_aicountly)->where('uuid_contactid', $uuid_contactid)->update(['contact_status' => $status]);
    }
    public function update_status($uuid_aicountly,$status,$uuid_contactid)
    {
        $this->db->table('aicountly_contactaic_univdb')->where('uuid_aicountly', $uuid_aicountly)->where('uuid_contactid', $uuid_contactid)->update(['contact_status' => $status]);
    }
    public function get_status($uuid_aicountly,$uuid_contactid)
    {
        $result = $this->db->table('aicountly_contactaic_univdb')->where('uuid_aicountly', $uuid_aicountly)->where('uuid_contactid', $uuid_contactid)->get()->getRowArray();
        return $result;
    }

    public function delete_contact($uuid_aicountly,$uuid_contactid)
    {
        $this->db->table('aicountly_contactaic_univdb')->where('uuid_aicountly', $uuid_aicountly)->where('uuid_contactid', $uuid_contactid)->delete();
    }
    
    public function all_contacts($uuid_aicountly,$category_id, $status, $search)
    {
        
        $builder = $this->db->table('aicountly_contactaic_univdb');
        $builder->select('aicountly_contactaic_univdb.*');
        
        $builder->join('aicountly_useraictly_univdb', 'aicountly_useraictly_univdb.uuid_aicountly = aicountly_contactaic_univdb.uuid_contactid', 'left');
        $builder->select('user_firstname, user_lastname, user_regdemail');
        
        $builder->where('aicountly_contactaic_univdb.uuid_aicountly', $uuid_aicountly);
        
        if($status != '')
           $builder->where('aicountly_contactaic_univdb.contact_status', $status);
        else
           $builder->where('aicountly_contactaic_univdb.contact_status !=', 'deleted');
            
        if($category_id != 0)
            $builder->like('aicountly_contactaic_univdb.contact_cat_id', $category_id);
        if($search != '')
        {    
            $builder->like('CONCAT(user_firstname," ",user_lastname)', $search);
        }
            
		$result = $builder->get()->getResultArray();
        return $result;
    }
    public function get_shared_companies($uuid_aicountly,$uuid_contactid) 
    {
       $records = [];
       $erp_db = $this->externaldb->erp_db();
        
       $builder = $erp_db->table("aictlyerp_cmpidacsnn_univdb");
       $builder->select('aictlyerp_cmpidacsnn_univdb.uuid_aicountly, "shared" as "company_type" ');
       $builder->join('aictlyerp_compmastern_univdb', 'aictlyerp_cmpidacsnn_univdb.comp_id = aictlyerp_compmastern_univdb.comp_id');
       $builder->select('aictlyerp_compmastern_univdb.comp_id, aictlyerp_compmastern_univdb.comp_name, aictlyerp_compmastern_univdb.comp_code, aictlyerp_compmastern_univdb.fy_begndt');
       $builder->where('aictlyerp_cmpidacsnn_univdb.uuid_aicountly',$uuid_aicountly);
       $builder->where('aictlyerp_cmpidacsnn_univdb.uuid_access',$uuid_contactid);
       $builder->orderBy('comp_code','DESC');
       $result = $builder->get()->getResultArray();
        
	           
        if(!empty($result)){
            foreach($result as $values){
            $records[] = array(
		              'company_id'       =>$values['comp_id'],
					  'company_name'     =>$values['comp_name'],
					  'company_code'     =>$values['comp_code'],
					  'fy_begndt'        =>$values['fy_begndt']
					   );     
 	       
		  } 	       
        }
        return $records;
    }
    
    public function contact_details($uuid_contactid)
    {
        $uuid_aicountly= $this->session->get('uuid_aicountly');
        $result = $builder = $this->db->table('aicountly_contactaic_univdb');
        $builder->select('aicountly_contactaic_univdb.*');
        
        $builder->join('aicountly_useraictly_univdb', 'aicountly_useraictly_univdb.uuid_aicountly = aicountly_contactaic_univdb.uuid_contactid', 'left');
        $builder->select('user_firstname, user_lastname, user_regdemail, user_regdmobile');
        
        $builder->where('aicountly_contactaic_univdb.uuid_aicountly', $uuid_aicountly);
        $builder->where('aicountly_contactaic_univdb.uuid_contactid', $uuid_contactid);
		$result = $builder->get()->getRowArray();
		
		$result['category'] = [];
		$result['cat_ids'] = [];
		if(!empty($result['contact_cat_id']))
	    {
	        $arr = explode(",",$result['contact_cat_id']);
	        $cat_arr = [];
	        foreach($arr as $cat_id)
	        {
	            $category = $this->get_category_by_id($cat_id);
	            if(!empty($category))
	            {
	                array_push($cat_arr, [
	                    'category_id'    => $category['contact_cat_id'],
	                    'category_name'  => $category['contact_category'],
	                    'category_color' => $category['contact_category_color']
	                ]);
	            }
	        }
	        $result['cat_ids'] = $arr;
	        $result['category'] = $cat_arr;
	    }
		    
        return $result;
    }
    public function contact_by_email($uuid_aicountly,$email)
    {
        
        $result = $builder = $this->db->table('aicountly_useraictly_univdb');
    
        $builder->select('user_firstname, user_lastname, uuid_aicountly, user_regdemail');
        
        $builder->where('uuid_aicountly != ', $uuid_aicountly);
        $builder->where('user_regdemail', $email);
        
		$result = $builder->get()->getRowArray();
		
		
		if($result)
		{
            $status1 = '';
            $status2 = '';
		    
            $contact1 = $this->db->table('aicountly_contactaic_univdb')->select('contact_status')
                                 ->where('uuid_aicountly', $uuid_aicountly)
                                 ->where('uuid_contactid', $result['uuid_aicountly'])
                                 ->get()->getRowArray();

            if($contact1){
                $status1 = $contact1['contact_status'];
            }

            $contact2 = $this->db->table('aicountly_contactaic_univdb')->select('contact_status')
                                 ->where('uuid_aicountly', $result['uuid_aicountly'])
                                 ->where('uuid_contactid', $uuid_aicountly)
                                 ->get()->getRowArray();

            if($contact2){
                $status2 = $contact2['contact_status'];
            }

            $result['status1'] = $status1;
            $result['status2'] = $status2;

		}
		
		
        return $result;
    }
    public function check_requests()
    {
        $uuid_aicountly= $this->session->get('uuid_aicountly');
        $builder = $this->db->table('aicountly_contactaic_univdb');
        $builder->select('aicountly_contactaic_univdb.*');
        
        $builder->join('aicountly_useraictly_univdb', 'aicountly_useraictly_univdb.uuid_aicountly = aicountly_contactaic_univdb.uuid_aicountly', 'left');
        $builder->select('user_firstname, user_lastname, user_regdemail');
        
        $builder->where('aicountly_contactaic_univdb.uuid_contactid', $uuid_aicountly);
        $builder->where('aicountly_contactaic_univdb.contact_status', 'pending');
		$result = $builder->get()->getResultArray();
        return $result;
    }
    
    public function add_category($data)
    {
        $this->db->table('aicountly_contaiccat_univdb ')->insert($data);
    }
    public function update_category($id,$data)
    {
        $this->db->table('aicountly_contaiccat_univdb ')->where('contact_cat_id', $id)->update($data);
    }
    public function delete_category($id)
    {
        $this->db->table('aicountly_contaiccat_univdb ')->where('contact_cat_id', $id)->delete();
    }
    
    public function get_categories()
    {
        $uuid_aicountly= $this->session->get('uuid_aicountly');
        $result = $builder = $this->db->table('aicountly_contaiccat_univdb ')->where('uuid_aicountly', $uuid_aicountly)->get()->getResultArray();
        return $result;
    }
    public function get_category_by_id($id)
    {
        $result = $builder = $this->db->table('aicountly_contaiccat_univdb ')->where('contact_cat_id', $id)->get()->getRowArray();
        return $result;
    }

    
}