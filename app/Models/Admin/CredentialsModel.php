<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Models\CommonModel;

class CredentialsModel extends Model {
    public function __construct() {
       parent::__construct();                 
	   $this->CommonModel      = new CommonModel();	  
	   $this->session          = \Config\Services::session();
	   $this->company_id       = $this->session->get('ses_company_id');
	   $this->bo_id            = $this->session->get('ses_boid');
	   $this->uuid             = $this->session->get('uuid');   
    }
	
	function credential_info($cred_id){	 
       $credential_tbl = 'erppassmgr';
	   $uuid = $this->uuid;
       return $this->db->table($credential_tbl)->where('erp_pass_id', $cred_id)->get()->getRowArray();   	   
    }
	
	function update_credential($cred_id,$update_data){
        $credential_tbl = 'erppassmgr';
		$this->db->table($credential_tbl)->where('erp_pass_id',$cred_id)->update($update_data);
		return ['status' => true, 'cred_id' => $cred_id,'message'=>'Updated successfully'];	
    } 
   	
	function ajax_credentials_list(){
		$cred_site_dropdown = GST_Sites_Types();
		$cred_type_dropdown = array(''=>'Choose','1'=>'Tax Payer','2'=>'TCS (E-Commerce)','3'=>'TDS','4'=>'Non Resident');
		$uuid               = $this->uuid;
		$comp_id            = $this->session->get('ses_company_id');
	    $base_url           = base_url().getenv('AdminPath');
	    
		if(isset($_POST['type']) && $_POST['type']>0)
		  $pq_filter_type      = $_POST["type"];
	   else
		   $pq_filter_type     = '';
		
	    if(isset($_POST["pq_filter"])){
	       $pq_filter    = $_POST["pq_filter"];	       
	       $filter_data  = json_decode($_POST["pq_filter"],true);	    
	       $pq_filters   = $filter_data['data'][0];	    
	       if(isset($pq_filters['condition']))
	       $condition    = $pq_filters['condition'];
	    else
		  $condition     = '';
	      $search_text   = strtolower($pq_filters['value']);
	      $dataIndx      = $pq_filters['dataIndx']; 
	    }
	    else{
	      $pq_filter     = '';
	      $search_text   = '';
	      $dataIndx      = '';
	      $condition     = '';
	     }
		
		$credential_tbl = 'erppassmgr'; 
	    $builder        = $this->db->table($credential_tbl); 	    
        if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
         $builder->orderBy('erp_pass_site');
		 $builder->where('cmp_id', $comp_id);
		 if($pq_filter_type!='')
		 $builder->where('erp_pass_site', $pq_filter_type);
	 	 $result  = $builder->get()->getResultArray();
		 $records = array(); 
		
         foreach($result as $values){						
			$clientid        = $values['erp_client_id'];
			$cred_secret_key = $values['erp_secret_key'];			
           	$records[] = array(	
			          'chkbx'          => '<input name="cred_ids[]" class="checkbox accounts_row" data-id="'.$values['erp_pass_id'].'" type="checkbox" value="'.$values['erp_pass_id'].'">',           
 			          'cred_id'        => $values['erp_pass_id'],
                      'cred_site_name' => $cred_site_dropdown[$values['erp_pass_site']] ?? '',
					  'cred_type'      => $cred_type_dropdown[$values['erp_pass_type']] ?? '',
					  'cred_user'      => $values['erp_pass_user'],
                      'cred_password'  => $values['erp_pass_pwd'],					                    
                      'clientid'       => $values['erp_client_id'],
					  'secret_key'     => $values['erp_secret_key']
 					  ); 
		    }	 
	
	    if($pq_curPage==0){$pq_curPage=1;}
    	   $offset        = ($pq_rPP * ($pq_curPage - 1));
           $total_records = count($records);
        if ($offset > $total_records)
            {        
                $pq_curPage = ceil($total_records / $pq_rPP);
                $offset     = ($pq_rPP * ($pq_curPage - 1));
            }
		$data       = 	 array_slice($records, $offset, $pq_rPP);
		if($records)
		  $totalrec = count($records);
		 else
		 $totalrec = 0;

		echo  "{\"totalRecords\":" .$totalrec . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($data)."}"; 
		 
	}
	 
	public function add_credential($data){
	 $credential_tbl = 'erppassmgr'; 
	 $this->db->table($credential_tbl)->insert($data); 
	 $cred_id = $this->db->insertID();	 
	 return ['status' => true, 'cred_id' => $cred_id,'message'=>'Added successfully'];
    }
	
    public function remove_single($id){
		$credential_tbl = 'erppassmgr';
     	$this->db->table($credential_tbl)->where('erp_pass_id', $id)->delete(); 
	  }		
  }