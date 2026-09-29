<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Models\CommonModel;
use App\Models\Admin\ItemSummaryModel;
use App\Models\Admin\ItemsModel;
use App\Models\Admin\VouchersModel;

class Stock_summary  extends BaseController
{
  function __construct()
    {   
	    helper(['form', 'url','text']);
		$this->auth_session    = new auth_session();			
	    $this->auth_session->user_restrict();
	    $this->CommonModel     = new CommonModel();
		$this->ItemSummaryModel  = new ItemSummaryModel();
		$this->ItemsModel      = new ItemsModel();
		$this->VouchersModel   = new VouchersModel();
	    $this->auth_session->role_restrict('CS');
		$this->base_url      =  base_url().'/'.getenv('AdminPath');
		$this->folder_path   =  getenv('AdminPath');
		$this->session    	 = \Config\Services::session();
	    $this->auth_session->is_company_opened();		
		$this->company_id    =  $this->session->get('ses_company_id');		
		$this->fy_id         =  $this->session->get('ses_comp_fy_id');
		$this->bo_id         =  $this->session->get('ses_boid');
    }
   
    public function index(){
         
        $data['base_url']           = $this->base_url;
		$data['message_output']     = $this->message_output;
		$data['folder_path']        = $this->folder_path;
		$data['items_dropdown']     = GetJsonFileContent($this->fy_id,$this->company_id,'itm');
		
       if($this->request->getMethod() == 'POST'){
	         $bo_id = $this->request->getVar('bo_id'); 
			 if($bo_id)
		     $this->session->set('ses_boid',$bo_id);  
           
            if(isset($_REQUEST['summary_type']) && $_REQUEST['summary_type']!=''){
          	$summary_type = $_REQUEST['summary_type'];
            $item_id      = $_REQUEST['item_id'];	
			/* if($_REQUEST['criteria_type']=='all_mc_cr')	{		
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
		   else{ */
			$mat_cent_id  = '';
			$mat_cent_grp_id  ='';
		   //}
		   
		  if($_REQUEST['unitid']!=''){
			 $unitid  = $_REQUEST['unitid'];
		  } else
			  $unitid  = '';
            $stck_summary_type = $_REQUEST['stck_summary_type'];
	     	$data["voucher_trans"]       = array();
	     
	     	 if($summary_type=='stock_summary' && $stck_summary_type=='stock_item')
	     	     return redirect()->to($this->base_url.'reports/item_summary?item_id='.$item_id.'&mc_id='.$mat_cent_id.'&unit_id='.$unitid.'&mc_grp_id='.$mat_cent_grp_id);	     	     
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
		$data['bo_dropdown']           = [];//$this->CommonModel->bo_dropdown();
        return view($this->folder_path.'stock_summary/view',$data);  
    }


      public function item_summary(){

        $item_id   = !empty($_GET['item_id']) ? $_GET['item_id'] : 0;
        $unit_id   = (isset($_GET['unit_id']) && $_GET['unit_id'] != '') ? $_GET['unit_id'] : 0;
        $mc_id     = (isset($_GET['mc_id']) && $_GET['mc_id'] != '') ? $_GET['mc_id'] : 0;
        $mc_grp_id = (isset($_GET['mc_grp_id']) && $_GET['mc_grp_id'] != '') ? $_GET['mc_grp_id'] : 0;
        $val       = (isset($_GET['val']) && $_GET['val'] != '') ? $_GET['val'] : '';
        $invtp_id  = (isset($_GET['invtp_id']) && $_GET['invtp_id'] != '') ? $_GET['invtp_id'] : 1;        
           
		$get_item_info = $this->ItemsModel->item_info($item_id);
		if(empty($get_item_info)){
          throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        } 
        $item_name               = $get_item_info['itm_name'];
        if($val == '')
          $itm_val_method_id     = $this->session->get('ses_dflt_val_method');
        else
          $itm_val_method_id     = $val;

        $balance     			 = 0;
        $item_value 			 = 0;
		$from_date               = $this->session->get('ses_company_fy_beginning');
        $to_date                 = $this->session->get('ses_company_fy_end');
		$hobo_id                 = '';
		$p = [
            'cmp_id'            => $this->company_id,
            'cmpfymasr_id'      => $this->fy_id,
            'fy_start_ymd'      => date('Y-m-d',strtotime($from_date)),
            'fy_end_ymd'        => date('Y-m-d',strtotime($to_date)),
            'itm_id_unit_id'    => $item_id.'_'.$unit_id,
			'itm_id'            => $item_id,
            'itm_val_method_id' => $itm_val_method_id,
            'mat_cent_id'       => $mc_id ? (int)$mc_id : null,
            'hobo_id'           => $hobo_id ? (int)$hobo_id : $this->bo_id,
            'uom'               => $unit_id,
        ];		
		
        $summary                 = $this->ItemSummaryModel->getMonthlySummary($p);
      
  	    $data['base_url']        = $this->base_url;
		$data['from_date']       = $from_date;
	    $data['to_date']         = $to_date;
        $data['item_unit_list']  = $this->VouchersModel->units_dropdown();
		$data['valuation_list']  = [''=>'','AVG','FIFO','LIFO'];
        $data['item_id'] = $item_id;
        $data['item_name'] = $item_name;
        $data['unit_id'] = $unit_id;
        $data['mc_id'] = $mc_id;
        $data['mc_grp_id'] = $mc_grp_id;
        $data['mc_grp_name'] = '';
        $data['val'] = $val;
        $data['to_date'] = $to_date;
        $data['val_id'] = $itm_val_method_id;
        $data['summary'] = $summary;
	    $data['invtp_id'] = $invtp_id;
        $data['inventory_type_list'] =array("1"=>"AVAILABLE","2"=>"PACKED","0"=>"OBSOLETE","3"=>"IN-TRANSIT");
        return view($this->folder_path.'reports/item_summary',$data); 
      }
}