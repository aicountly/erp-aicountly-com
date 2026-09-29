<?php
namespace App\Controllers\Admin;
use App\Models\CommonModel;
use App\Models\Admin\VouchersModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Models\Admin\LedgerModel;

class Reportsaccounts extends BaseController{
  protected $session;
  protected $CommonModel;
  protected $VouchersModel;
  protected $auth_session;
  protected $LedgerModel;
  protected $base_url;
  protected $folder_path;
  protected $company_id;
  protected $fy_id;
  protected $bo_id;
  function __construct(){  
    helper(['form', 'url','text']);
	$this->session    	 = \Config\Services::session();
	$this->CommonModel   =  new CommonModel();		
    $this->VouchersModel =  new VouchersModel();
    $this->auth_session  =  new auth_session();
	$this->LedgerModel   =  new LedgerModel();	
	$this->auth_session->user_restrict();
    $this->auth_session->is_company_opened();
	$this->base_url      = base_url().getenv('AdminPath');
    $this->folder_path   = getenv('AdminPath');
    $this->company_id    =  $this->session->get('ses_company_id');
    $this->fy_id         =  $this->session->get('ses_comp_fy_id');
	$this->bo_id         =  $this->session->get('ses_boid');
    }
	
	public function ajax_subledger_masters_dropdown(){
		if($this->request->getMethod() == 'POST'){
		  $acc_id  =  $this->request->getVar('acc_id');
   	   	  $list    =  $this->LedgerModel->sub_ledger_masters($acc_id);
		  $final_masters['-1'] = 'All Masters';
   		  foreach($list as $key => $value){
		 	  $final_masters[$value['sub_acc_id']] = ucwords(strtolower(trim($value['sub_acc_name'])));
		  }
		  echo form_dropdown('sub_acc_id',$final_masters, '',' id="sub_acc_id" style="font-size:18px;border-radius: 6px 0px 0px 6px;" class="selectwidget form-control"'); 
		}
		
	}
	
	public function ledger(){
       
	   if($this->request->getMethod() == 'POST'){
		  	$type           = $this->request->getVar('type');
            $detail_type    = $this->request->getVar('detail');			
            $from_date      = date('d-m-Y',strtotime($this->request->getVar('fromdate')));
            $to_date        = date('d-m-Y',strtotime($this->request->getVar('todate')));
            if($type == 'Account Ledger')
            {
                if($detail_type=='Account'){
                    $account_id    = $this->request->getVar('id');
                    return redirect()->to($this->base_url.'accounts/ledger_detail/'.$account_id.'?from_date='.$from_date.'&to_date='.$to_date); 
                }
                
                else if($detail_type=='Account Group'){
                    $group_id    = $this->request->getVar('id');
                    return redirect()->to($this->base_url.'accounts/group_ledger_detail?group_id='.$group_id.'&from_date='.$from_date.'&to_date='.$to_date);  
                } 
                else if($detail_type=='Account/Group List'){
                   $group_id    = $this->request->getVar('id');
                    return redirect()->to($this->base_url.'accounts/accounts_trial/'.$group_id.'&from_date='.$from_date.'&to_date='.$to_date);  
                }
                else if($detail_type=='Account/Group Parent'){
                   $id    = $this->request->getVar('id');
                   return redirect()->to($this->base_url.'accounts/parent_ledger_detail?parent_id='.$id.'&from_date='.$from_date.'&to_date='.$to_date);  
                }
				else if($detail_type=='Sub Ledger'){
                   $id    = $this->request->getVar('sub_acc_id');
                   return redirect()->to($this->base_url.'accounts/subledger_detail/'.$id.'?from_date='.$from_date.'&to_date='.$to_date); 
                }
            }
            
            elseif($type == 'Bill Sundry Ledger'){
                if($detail_type=='Bill Sundry Account'){
                    $account_id    = $this->request->getVar('id');
                    return redirect()->to($this->base_url.'billsundry/ledger_detail/'.$account_id.'?from_date='.$from_date.'&to_date='.$to_date); 
                }
				else if($detail_type=='Sub Ledger'){
                   $id    = $this->request->getVar('sub_acc_id');
                   return redirect()->to($this->base_url.'billsundry/subledger_detail/'.$id.'?from_date='.$from_date.'&to_date='.$to_date); 
                }
            }
			
	   }
       
        $dropdown = [
                'Account Ledger'     => ['Account', 'Sub Ledger'],
                'Bill Sundry Ledger' => ['Bill Sundry Account', 'Sub Ledger'],
                'Cost Centre Ledger' => ['Cost Centre Account']
                ];
        
		
       	$data['message_output']        = $this->message_output;
		$data['accounts_list']         = GetJsonFileContent($this->fy_id,$this->company_id,'acc');
		$data['folder_path']           = $this->folder_path;
		$data['groups_list']           = [];//$this->ReportsModel->get_all_groups();
        $data['group_parent_list']     = [];//$this->ReportsModel->get_all_group_parents();
        $data['bill_sundry_list']      = GetJsonFileContent($this->fy_id,$this->company_id,'bsd');
        $data['cc_list']               = [];//$this->ReportsModel->get_cc_list();
        $data['cc_groups']             = [];//$this->ReportsModel->get_cc_groups();
        $data['base_url']              = $this->base_url;
		$data['dropdown']              = $dropdown;
        return view($this->folder_path.'reports/account_ledger',$data);  
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
        $accounts_list   = GetJsonFileContent($this->fy_id,$this->company_id,'acc');
       	$data['message_output']          = $this->message_output;
		$data['folder_path']             = $this->folder_path;
		$data['groups_list']             = [];//$this->ReportsModel->get_all_groups();
        $data['group_parent_list']       = [];//$this->ReportsModel->get_all_group_parents();
        $data['bill_sundry_list']        = [];//$this->ReportsModel->get_all_bill_sundry_accounts();
        $data['cc_list']                 = [];//$this->ReportsModel->get_cc_list();
        $data['cc_groups']               = [];//$this->ReportsModel->get_cc_groups();
        $data['base_url']                = $this->base_url;
		$data['accounts_list']           = $accounts_list;
        $data['summary_detail_dropdown'] = $summary_detail_dropdown;
        return view($this->folder_path.'reports/account_summary',$data);  
    }


}