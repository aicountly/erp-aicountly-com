<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Libraries\enc_string;
use App\Models\CommonModel;
use App\Models\Admin\ReportsModel;

class Stock_summary  extends BaseController
{
  function __construct()
    {   
	    helper(['form', 'url','text']);
		$this->auth_session  = new auth_session();			
	    $this->auth_session->user_restrict();
	    $this->CommonModel     = new CommonModel();	
	    $this->ReportsModel    = new ReportsModel();	
	    $this->auth_session->role_restrict('CS');
		$this->base_url      =  base_url().'/'.getenv('AdminPath');
		$this->folder_path   =  getenv('AdminPath');
		$this->session    	 = \Config\Services::session();
	    $this->auth_session->is_company_opened();
		$this->comp_code     =  $this->session->get('ses_company_code');
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->enc_string    =  new enc_string();
		$this->getReferrer   =  \Config\Services::request()->getUserAgent()->getReferrer();
		$this->ses_comp_fy_id=  $this->session->get('ses_comp_fy_id');
    }
   
    public function index(){
         
        $data['base_url']           = $this->base_url;
		$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$fy_months_list             =  $this->CommonModel->get_fy_info($this->ses_comp_fy_id,$this->company_id);
		$data['items_dropdown']     = $this->ReportsModel->items_dropdown();
		$data['matrcntr_dropdown']     = $this->ReportsModel->matrcntr_dropdown();
		$data['matrcntr_grp_dropdown']     = $this->ReportsModel->matrcntr_grp_dropdown();
		$data['fy_months_list']     = $fy_months_list;  
       if($this->request->getMethod() == 'post'){	
	   
	         $bo_id = $this->request->getVar('bo_id'); 
		     $this->session->set('ses_boid',$bo_id);  
           
            if(isset($_REQUEST['summary_type']) && $_REQUEST['summary_type']!=''){
          	$summary_type = $_REQUEST['summary_type'];
            $item_id      = $_REQUEST['item_id'];	
			if($_REQUEST['criteria_type']=='all_mc_cr')	{		
            $mat_cent_id  = '';
			$mat_cent_grp_id='';
			}
		    else if($_REQUEST['criteria_type']=='one_mc_cr'){			
            $mat_cent_id  = $_REQUEST['mat_cent_id'];
			$mat_cent_grp_id='';
			}
		    else if($_REQUEST['criteria_type']=='mc_group_cr')	{		
            $mat_cent_grp_id  = $_REQUEST['mat_cent_grpid'];
			$mat_cent_id  = '';
			}
		   else{
			$mat_cent_id  = '';
			$mat_cent_grp_id  ='';
		   }
		   
		  if($_REQUEST['unitid']!=''){
			 $unitid  = $_REQUEST['unitid'];
		  } else
			  $unitid  = '';
            $stck_summary_type = $_REQUEST['stck_summary_type'];
	     	$data["voucher_trans"]       = array();
	     
	     	 if($summary_type=='stock_summary' && $stck_summary_type=='stock_item')
	     	     return redirect()->to($this->base_url.'reports/item_summary?itmid='.$item_id.'&mcid='.$mat_cent_id.'&untid='.$unitid.'&mcgrpid='.$mat_cent_grp_id);	     	     
	     	else if($summary_type=='mc_summary')
	     	        return view($this->folder_path.'reports/mc_summary',$data);
	       else if($summary_type=='stock_summary' && $stck_summary_type=='item_grp')
	     	     return view($this->folder_path.'reports/item_group_summary',$data);      
	       
	       else if($summary_type=='stock_summary' && $stck_summary_type=='stock_catg' )
	     	      return view($this->folder_path.'reports/stock_category_summary',$data);
	        else
	     	 return false;
            }
       }
       
        $data['ses_boid']              = $this->session->get('ses_boid');		
		$data['bo_dropdown']           = $this->CommonModel->bo_dropdown();
        return view($this->folder_path.'stock_summary/view',$data);  
    }

}