<?php
namespace App\Controllers\Admin;
use App\Models\CommonModel;
use App\Models\Admin\VouchersModel;
use App\Models\Admin\StockStatusModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Models\Admin\LedgerModel;

class ReportsStock extends BaseController{
	
  function __construct(){  
    helper(['form', 'url','text']);
	$this->session    	 = \Config\Services::session();  
	$this->CommonModel   =  new CommonModel();		
    $this->VouchersModel =  new VouchersModel();
	$this->StockStatusModel =  new StockStatusModel();
    $this->auth_session  =  new auth_session();
	$this->LedgerModel   =  new LedgerModel();	
	$this->auth_session->user_restrict();
    $this->auth_session->is_company_opened();
	$this->base_url      =  base_url().getenv('AdminPath');
    $this->folder_path   =  getenv('AdminPath');
    $this->company_id    =  $this->session->get('ses_company_id');
    $this->fy_id         =  $this->session->get('ses_comp_fy_id');
	$this->bo_id         =  $this->session->get('ses_boid');
	$this->encrypter     =  \Config\Services::encrypter();
    }
	
	public function ledger(){       
	   if($this->request->getMethod() == 'POST'){
		   	$type           = $this->request->getVar('type');
            $detail_type    = $this->request->getVar('module');
			$unitid         = $this->request->getVar('unitid');
			$mc_id          = $this->request->getVar('mat_cent_id');
			$mc_grpid       = $this->request->getVar('mat_cent_grpid');			
            $from_date      = date('Y-m-d',strtotime($this->request->getVar('fromdate')));
            $to_date        = date('Y-m-d',strtotime($this->request->getVar('todate')));
            if($type == 'Stock Ledger'){
                if($detail_type=='Item Account'){
                    $account_id    = $this->request->getVar('item_id');
                    return redirect()->to($this->base_url.'items/ledger_detail/'.$account_id.'?mc_grp_id='.$mc_grpid.'&mc_id='.$mc_id.'&unit_id='.$unitid.'&from_date='.$from_date.'&to_date='.$to_date); 
                }
            }
	    }
        $dropdown = [
                'Stock Ledger' => ['Item Account']
            ];

       	$data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;
		$data['groups_list']           = [];//$this->ReportsModel->get_all_groups();
        $data['group_parent_list']     = [];//$this->ReportsModel->get_all_group_parents();
        $data['bill_sundry_list']      = [];//$this->ReportsModel->get_all_bill_sundry_accounts();
        $data['items_dropdown']        = GetJsonFileContent($this->fy_id,$this->company_id,'itm');
		$data['cc_groups']             = [];//$this->ReportsModel->get_cc_groups();
        $data['base_url']              = $this->base_url;
		$data['dropdown']              = $dropdown;
		$data['units_list']     	   = $this->VouchersModel->units_dropdown();
		$data['matrcntr_dropdown']     = $this->VouchersModel->material_centre_dropdown();
		$data['matrcntr_grp_dropdown'] = [];//$this->ReportsModel->matrcntr_grp_dropdown();

        return view($this->folder_path.'reports/stock_ledger',$data);  
    }
	
	public function ajax_load_stock_status(){
		if($this->request->getMethod() == 'POST'){
			$cmpId  = $this->company_id;
            $boId   = $this->bo_id;
            $fyId   = $this->fy_id;
			
			echo $this->StockStatusModel->inventoryStatusPaged($cmpId,$boId,$fyId);
		}
	}
	
	public function stock_status(){ 
        $data['val_id']                = $_GET['val_id'] ?? 0;
        $data['mc_id']                 = $_GET['mc_id'] ?? 0;
        $data['unit_id']               = $_GET['unit_id'] ?? 0;
        $from_date                     = $_GET['from_date'] ?? '';
        $to_date                       = $_GET['to_date'] ?? '';
        $data['from_date']             = validate_from_date($from_date);
        $data['to_date']               = validate_to_date($to_date);
		$to_date_ymd                   = date('Y-m-d', strtotime($data['to_date']));
		$data['to_date_ymd']           = $to_date_ymd;
       	$data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;		
        $data['base_url']              = $this->base_url;		
		$data['units_list']     	   = $this->VouchersModel->units_dropdown();
		$data['valuation_list']        = [''=>'','AVG','FIFO','LIFO'];
		$data['matrcntr_dropdown']     = $this->VouchersModel->material_centre_dropdown();
        return view($this->folder_path.'reports/stock_status',$data);  
    }

    public function other_stock_report(){       
       if($this->request->getMethod() == 'POST'){	
  
            $type           = $this->request->getVar('type');		   
            $detail_type    = $this->request->getVar('detail');
            $from_date      = date('Y-m-d',strtotime($this->request->getVar('fromdate')));
            $to_date        = date('Y-m-d',strtotime($this->request->getVar('todate')));
            $batch_id       = date('Y-m-d',strtotime($this->request->getVar('id')));
            
            if($type == 'Batch Report')
            {
                if($detail_type=='All Batches'){
                   return redirect()->to($this->base_url.'reports/allbatches?from_date='.$from_date.'&to_date='.$to_date); 
                }
                else if($detail_type=='Batch Wise'){
                   $batch_id    = $this->request->getVar('id');
                    return redirect()->to($this->base_url.'reports/batchwise/'.$batch_id.'?from_date='.$from_date.'&end_date='.$to_date);  
                }
            }
            /* if($type == 'Item Tracking Report')
            {
                if($detail_type=='Item Wise'){
                    $id    = $this->request->getVar('id');    
                    return redirect()->to($this->base_url.'reports/item_tracking/'.$id.'?type=item_wise&from_date='.$from_date.'&to_date='.$to_date);  
                }
				else if($detail_type=='All Tracked Items'){
                    return redirect()->to($this->base_url.'reports/item_tracking/?type=all_item&from_date='.$from_date.'&to_date='.$to_date);  
                }				
            }
			if($type == 'BOM Profitability Report')
            {
                if($detail_type=='BOM Wise'){
                    $id    = $this->request->getVar('id');    
                    return redirect()->to($this->base_url.'reports/bom_profitability/'.$id.'?type=bom_wise&from_date='.$from_date.'&to_date='.$to_date);  
                }
				else if($detail_type=='All BOM'){
                    return redirect()->to($this->base_url.'reports/bom_profitability/?type=all_bom&from_date='.$from_date.'&to_date='.$to_date);  
                }				
            } */
          }  
		  
        $dropdown = [
                'Batch Report'     => ['All Batches', 'Batch Wise'],
                //'Item Tracking Report' => ['All Tracked Items','Stock Category Wise','Item Group Wise','Item Wise','Untracked Item'],                
                //'BOM Profitability Report'     => ['All BOM', 'BOM Wise'],
				];        
       	$data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;
		$data['accounts_list']         = [];//$this->ReportsModel->get_all_accounts();
		$data['batch_list']            = $this->VouchersModel->all_item_batch_list();	
		
		$data['bom_list']              = [];//$this->ReportsModel->get_all_bom();
		$data['items_list']            = [];//$this->ReportsModel->get_all_items();
        $data['bill_sundry_list']      = [];//$this->ReportsModel->get_all_bill_sundry_accounts();
        $data['item_groups_list']      = [];//$this->ReportsModel->get_all_item_groups_list();
        $data['stock_category_list']   = [];//$this->ReportsModel->get_all_stock_category();
        $data['base_url']              = $this->base_url;
        $data['dropdown']              = $dropdown;
        return view($this->folder_path.'reports/other_stock_report',$data);  
    }
	
	public function summary(){
       $summary_detail_dropdown = [
            'Account Summary' => ['Ledger']            
        ];
	   if($this->request->getMethod() == 'POST'){
		  
			$type           = $this->request->getVar('summary_type');
            $detail_type    = $this->request->getVar('summary_detail');
            $from_date      = date('Y-m-d',strtotime($this->request->getVar('fromdate')));
            $to_date        = date('Y-m-d',strtotime($this->request->getVar('todate')));
            if($type == 'Account Summary')
            {
                if($detail_type=='Ledger'){
                    $account_id    = $this->request->getVar('id');
                   return redirect()->to($this->base_url.'accounts/monthly_detail/'.$account_id);
                }                
            }
			
	   }
       
        $dropdown = [
                'Account Ledger'     => ['Account'],
                'Bill Sundry Ledger' => ['Bill Sundry Account'],
                'Cost Centre Ledger' => ['Cost Centre Account']
                ];
        
       	$data['message_output']        = $this->message_output;
		$data['folder_path']           = $this->folder_path;
		$data['groups_list']           = [];//$this->ReportsModel->get_all_groups();
        $data['group_parent_list']     = [];//$this->ReportsModel->get_all_group_parents();
        $data['bill_sundry_list']      = [];//$this->ReportsModel->get_all_bill_sundry_accounts();
        $data['cc_list']               = [];//$this->ReportsModel->get_cc_list();
        $data['cc_groups']             = [];//$this->ReportsModel->get_cc_groups();
        $data['base_url']              = $this->base_url;
        $data['summary_detail_dropdown']              = $summary_detail_dropdown;
        return view($this->folder_path.'reports/account_summary',$data);  
    }
	
	public function ajax_batches_report(){
		$from_date       = date("Y-m-d",strtotime($this->request->getVar("from_date")));
		$to_date         = date("Y-m-d",strtotime($this->request->getVar("to_date")));
		$response = $this->LedgerModel->load_batches_listings($from_date, $to_date);
	    echo $response;
	}
	public function ajax_batchwise_report(){
		$from_date       = date("Y-m-d",strtotime($this->request->getVar("from_date")));
		$to_date         = date("Y-m-d",strtotime($this->request->getVar("to_date")));
		$batch_id        = $this->request->getVar("batchid");
		$response = $this->LedgerModel->load_batchwise_listings($batch_id,$from_date, $to_date);
	    echo $response;
	}
	public function all_batch_report(){
        $from_date   = $_GET['from_date'] ?? '';
		$to_date     = $_GET['to_date'] ?? '';
		$from_date   = validate_from_date($from_date);
		$to_date     = validate_to_date($to_date);		
		$from_datem  = date('Y-m-d',strtotime($from_date));
		$to_datem    = date('Y-m-d',strtotime($to_date));
		$data = [
			'from_date'			=> $from_date,
			'to_date'			=> $to_date,
			'bo_id'             => $this->bo_id			
	       ];			  
	   return view($this->folder_path.'reports/all_batch_listing',$data);      
   }
   
   public function batch_wise_report($batchid){
	    $from_date   = $_GET['from_date'] ?? '';
		$to_date     = $_GET['end_date'] ?? '';
		$from_date   = validate_from_date($from_date);
		$to_date     = validate_to_date($to_date);		
		$from_datem  = date('Y-m-d',strtotime($from_date));
		$to_datem    = date('Y-m-d',strtotime($to_date));
	    $batch_info   = $this->LedgerModel->batch_info($batchid);
		if(!$batch_info)
		   throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound(); 
	   
		$batch_no     = $batch_info['batch_no'];
		$batch_id     = $batch_info['batch_master_id'];
	    $item_name    = $batch_info['itm_name'];
		$unit_name    = $batch_info['itm_unit_name'];
		$mnfcr_date   = $batch_info['batch_mfr_date'];
		$expry_date   = $batch_info['batch_expiry_date'];	
        
		$data = [
			'from_date'			=> $from_date,
			'to_date'			=> $to_date,
			'bo_id'             => $this->bo_id,
			'batch_id'          => $batch_id,
			'batch_no'          => $batch_no,
			'mnfcr_date'		=> $mnfcr_date,
			'item_name'		    => $item_name,
			'unit_name'		    => $unit_name,
		    'expry_date'		=> $expry_date	
	       ];			  
	   return view($this->folder_path.'reports/single_batch_listing',$data);      
   }


}