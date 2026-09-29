<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Models\CommonModel;
class BanksModel extends Model	{
    public function __construct() {
       parent::__construct();        
       $this->externaldb    = new externaldb();	
	   $this->CommonModel   = new CommonModel();	
	   $this->session       = \Config\Services::session();
	   $this->company_id    = $this->session->get('ses_company_id');
	   $this->aicountly_db  = $this->externaldb->aicountly_db();	
	   $this->univaictly    =  $this->externaldb->univaictly_db();
    } 
	
	public function branches_dropdown(){
		 $data =  $this->univaictly->table("hobomaster")->where('cmp_id', $this->company_id)->orderBy('hobo_name','ASC')->get()->getResultArray();
	     $final_result      = array();
		 $final_result['0'] = 'All';  
	     if($data){
		  foreach($data as $row){
              $final_result[$row['hobo_id']] =$row['hobo_name'];			   
	        }
        }
	  return $final_result;	
     }
	 
	public function get_bank_info($bank_id){
        $data = $this->univaictly->table("cmpbankmst")->where('cmp_id', $this->company_id)->where('bank_id', $bank_id)->get()->getRowArray();
        return $data;
     }
	
    public function add_bank($data){	
        $table = $this->univaictly->table("cmpbankmst")->where('LOWER(bank_name)', strtolower(trim($data['bank_name'])))
                                            	       ->get()->getRowArray(); 
                                            	  
        if($table){
            return ['status' => false, 'message' => 'Bank name must be unique'];
        }
		
		$table = $this->univaictly->table("cmpbankmst")->where('LOWER(bank_acc_no)', strtolower(trim($data['bank_acc_no'])))
                                            	       ->get()->getRowArray(); 
                                            	  
        if($table){
            return ['status' => false, 'message' => 'Bank acc. no must be unique'];
        }
       
        $this->db->univaictly("cmpbankmst")->insert($data);
        $bank_id = $this->univaictly->insertID();
	    return ['status' => true, 'bank_id' => $bank_id];
    
    }

	  public function remove_single_bank($bank_id){
	   if($bank_id >0 ){	  
		$this->univaictly->table("cmpbankmst")->where('cmp_id',$this->company_id)->where('bank_id',$bank_id)->delete();
        return TRUE;
	   }
	   return false;
	  }
	  
	 public function update_bank($data,$bank_id,$selbank_id){			 
	    $table = $this->univaictly->table("cmpbankmst")->where('LOWER(bank_name)', strtolower(trim($data['bank_name'])))
	                                                   ->where('bank_id !=',$bank_id)->where('cmp_id',$this->company_id)
                                                	   ->get()->getRowArray(); 
	    if($table){
            return ['status' => false, 'message' => 'Bank name must be unique'];
         }
	    $table = $this->univaictly->table("cmpbankmst")->where('LOWER(bank_acc_no)', strtolower(trim($data['bank_acc_no'])))
		                                           ->where('bank_id !=',$bank_id)->where('cmp_id',$this->company_id)
                                            	   ->get()->getRowArray();
        if($table){
            return ['status' => false, 'message' => 'Bank acc. no must be unique'];
        }
		$this->univaictly->table("cmpbankmst")->where('bank_id',$selbank_id)->update($data);
		return ['status' => true, 'bank_id' => $bank_id];	
    } 
   
   public function ajax_banks_list(){	    
        $comp_id         = $this->session->get('ses_company_id');
	    $base_url        = base_url().getenv('AdminPath');	    
	    if(isset($_POST["pq_filter"])){
	       $pq_filter     = $_POST["pq_filter"];	       
	       $filter_data   = json_decode($_POST["pq_filter"],true);
	      $pq_filters     = $filter_data['data'][0];	    
	       if(isset($pq_filters['condition']))
	         $condition   = $pq_filters['condition'];
	       else
		      $condition='';
	      $search_text = strtolower($pq_filters['value']);
	      $dataIndx    = $pq_filters['dataIndx']; 
	     }
	     else{
	      $pq_filter     ='';
	      $search_text   ='';
	      $dataIndx      ='';
	      $condition='';
	     }		
	    $cmpbankmst_tbl = "cmpbankmst"; 
	    $builder        = $this->univaictly->table($cmpbankmst_tbl); 		
	    // Get pagination params safely
		$pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
		$pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10; // default 10 rows per page

		// Add the filter condition early
		$builder->where('cmp_id', $comp_id);
		if ($pq_curPage < 1) $pq_curPage = 1;
		if ($pq_rPP < 1) $pq_rPP = 10;

		// First, clone the builder to get total count without limit & order
		$countBuilder = clone $builder;
		$total_Records = $countBuilder->countAllResults(false); // false keeps the builder intact

		// Calculate offset and correct page if needed
		$offset = ($pq_rPP * ($pq_curPage - 1));
		if ($offset > $total_Records) {
			$pq_curPage = ceil($total_Records / $pq_rPP);
			$offset = ($pq_rPP * ($pq_curPage - 1));
		}
		if ($offset < 0) {
			$offset = 0;
		}

	
		$builder->orderBy('bank_name');
		$builder->limit($pq_rPP, $offset);
        $result  = $builder->get()->getResultArray();
      
         $records = array(); 		
         foreach($result as $values){  
            $state_info      = $this->CommonModel->get_state_info($values['bank_country'],$values['bank_state']);	
			if($state_info)
				 $state_name = ucwords(strtolower($state_info['state_name']));
			 else
				 $state_name = '';
			    
				if($values['bank_acc_type']!=''){
				 $bank_name =ucwords($values['bank_name']).'(<small>'.$values['bank_acc_type'].'</small>)';	
				}else{
				 $bank_name =ucwords($values['bank_name']);	
				}
				
			    $checkbox_html ='<input name="banks_ids[]" class="checkbox banks_row" data-id="'.$values['bank_id'].'" type="checkbox" value="'.$values['bank_id'].'">';			 	
				$records[] = array(	
			          'chkbx'         => $checkbox_html,           
 			          'comp_bank_id'  => $values['bank_id'],
                      'bank_name'     => $bank_name,
					  'account_no'    => $values['bank_acc_no'],
					  'ifsc_code'     => $values['bank_ifsc'],
                      'micr'          => $values['bank_micr'],					                    
                      'branch'        => $values['bank_branch'],		
					  'city'          => $values['bank_city'],		
					  'state'         => $state_name,					                    
					  'pin'           => $values['bank_pin_code'],
					  'address'       => $values['bank_addr']
 					  ); 
		           }	 	
	     
       echo  "{\"totalRecords\":" .$total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($records)."}"; 
     } 
}