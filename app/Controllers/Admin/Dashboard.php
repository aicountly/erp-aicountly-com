<?php
namespace App\Controllers\Admin;
use App\Models\Admin\DashboardModel;
use App\Models\Admin\ChartModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Models\CommonModel;
use App\Models\Admin\ERPLogModel;
class Dashboard extends BaseController
{
    var	$folder_path;
	function __construct()
    {  
	    helper(['form', 'url']);
	     
		$this->DashboardModel = new DashboardModel();
		$this->auth_session   = new auth_session();
	    $this->folder_path    = getenv('AdminPath');
	    $this->LogModel      =  new ERPLogModel();
	    $this->auth_session->user_restrict();
        $this->auth_session->is_company_opened();
	    $this->auth_session->role_restrict('CS');
		$this->base_url       =  base_url().getenv('AdminPath');
        $this->session    	  =  \Config\Services::session();
		$this->company_id     =  $this->session->get('ses_company_id');
        $this->bo_id          =  $this->session->get('ses_boid');
        $this->fy_id          =  $this->session->get('ses_comp_fy_id');
		$this->user_id        =  $this->session->get('uuid_aicountly');
		$this->CommonModel    = new CommonModel();
		
    }
    
	
	public function ajax_quickassets_data_info($type,$from_date,$to_date,$parent=0){
	 if($type=='cash' || $type=='bank'){
		 $group_id=23;
	 }	
	 if($type=='limit'){
		  $group_id=21;
	 }	
	 $cash_final_report = array();
	 $bank_final_report = array();
	 $from_date_ymd     = date('Y-m-d', strtotime($from_date));
     $to_date_ymd       = date('Y-m-d', strtotime($to_date));	
	 if($parent == 1)
		$ac_trial_balance_list = $this->AccountsModel->load_accounts_trial_balance3($group_id,$from_date_ymd,$to_date_ymd);
     else
    	$ac_trial_balance_list = $this->AccountsModel->load_accounts_trial_balance2($group_id,$from_date_ymd,$to_date_ymd);
   	
	 if($type=='cash' || $type=='bank'){		  
		if($ac_trial_balance_list){
		    foreach($ac_trial_balance_list as $account){				  
		  	   if($group_id == 23){
				   if(str_contains(strtolower($account['entity_name']), 'cash') && !str_contains(strtolower($account['entity_name']), 'bank')){
				     $cash_final_report[]=$account;
				   }
				   else{
				     $bank_final_report[]=$account;
				    }
				  } 
			  }			  
		   }	
	    }		  
		if($type=='cash'){
		   echo json_encode(["data"=>$cash_final_report]);
		}	
		else if($type=='bank'){
		  echo json_encode(["data"=>$bank_final_report]);	
		}
		else{
		  echo json_encode(["data"=>$ac_trial_balance_list]);	 
		}
	    die();	 
	}
	
    public function index()
    {
        
        // $commands = $this->CommonModel->commands();
        // echo '<pre>';
        // print_r($commands);
        // die();
		
        /*  $montyhs = months_list();
        $profitloss_chart_values =  $this->DashboardModel->profitloss_chart_values();
        $profit_loss_keys ='';
        $all_month_keys   = '';
        foreach($profitloss_chart_values as $month_key => $row_val)
          {
             $month_name = substr($montyhs[$month_key], 0,3) ;
              $profit_loss_keys .=$row_val.',';
              $all_month_keys  .= '"'.$month_name.'",';
          }
        $profit_loss_keys = rtrim($profit_loss_keys,",");
        $all_month_keys    = rtrim($all_month_keys,",");
        
        
        $revenue_chart_values    =  $this->DashboardModel->revenue_chart_values();
        $revenue_keys            =  '';
      
        foreach($revenue_chart_values as $month_key => $row_val) {
              $revenue_keys .=$row_val.',';
          }
          
         $revenue_keys = rtrim($revenue_keys,",");
		 
		 
	  
         $data= array('from_date'=>$from_date,'to_date'=>$to_date,'month_keys'=>$all_month_keys,'revenue_keys'=>$revenue_keys,'profit_loss_keys'=>$profit_loss_keys ); 
	     return view($this->folder_path.'dashboard',$data);	 */
		 $fy_calender = fy_calender();
         $from_date = $fy_calender->from_date;
         $to_date = $fy_calender->to_date;
		 $data= array('from_date'=>$from_date,'to_date'=>$to_date,'month_keys'=>[],'revenue_keys'=>[],'profit_loss_keys'=>[] ); 
	     return view($this->folder_path.'dashboard',$data);
    }
    
    

    public function error()
    {
    	echo view($this->folder_path.'error_page');
    }
    
     public function close_company(){
		 $this->session->remove('ses_company_id');
		 $this->session->remove('ses_company_code');
		 $this->session->remove('ses_company_print_name');
		 $this->session->remove('ses_company_fy_beginning');
		 $this->session->remove('ses_company_email');
		 $this->session->remove('ses_company_mobile');
		 $this->session->remove('ses_company_wa_mobile');
		 $this->session->remove('ses_company_gstin');
		 $this->session->remove('ses_company_tan_no');
		 $this->session->remove('ses_company_pan_no');		 
		 return redirect()->to(base_url().'companies');
		 die;
	}
    
 
    public function signout(){
         $this->session->remove('ses_tab_urls');
         $this->session->remove('ses_tab_names');
         $this->session->remove('logged_in');
         $this->session->remove('suser_comp_list');
		 $this->session->remove('ses_company_id');
		 $this->session->remove('ses_company_code');
		 $this->session->remove('ses_company_print_name');
		 $this->session->remove('ses_company_fy_beginning');
		 $this->session->remove('ses_company_email');
		 $this->session->remove('ses_company_mobile');
		 $this->session->remove('ses_company_wa_mobile');
		 $this->session->remove('ses_company_gstin');
		 $this->session->remove('ses_company_tan_no');
		 $this->session->remove('ses_company_pan_no');
		 $this->session->remove('user_type');
		 $this->session->remove('logged_in');
		 $this->session->remove('uuid');
		 $this->session->remove('uuid_aicountly');
		 $this->session->remove('email');
		 $this->session->remove('f_name');
		 $this->session->remove('m_name');
		 $this->session->remove('l_name');
		 $this->session->remove('menu_item');
		 $this->session->remove('s_ctab');
		 $this->session->remove('visted_links_list');
		 
		 
		 return redirect()->to(base_url());
		 die;
	}
	
	public function settings()
	{
	    
	    return view($this->folder_path.'settings');
	}
	
	public function logs()
	{ 
	    $data['loglist'] = $this->LogModel->get_alllogs();
	    
	    return view($this->folder_path.'logs',$data);
	}
    

	public function cashFlowChart()
    {
    	$chartModel = new ChartModel();
        $result = $chartModel->get_cash_flow_details();
    
        return $this->response->setJSON($result);
    }

    public function cashEquivalentChart()
    {
    	$chartModel = new ChartModel();
        $result = $chartModel->get_cash_equivalent_details();
    
        return $this->response->setJSON($result);
    }

    public function revenueChart()
    {
    	$chartModel = new ChartModel();
   		$result = $chartModel->get_revenue_details();		
        return $this->response->setJSON(['labels' => $result['months'], 'prices' => $result['prices'], 'pricesExpense' => $result['pricesExpense']]);
    }

    public function netWorthChart()
    {
    	$chartModel = new ChartModel();
   		$result = $chartModel->get_net_worth_details();
        $data = [
            'labels' =>  $result['months'],
            'data' =>  $result['prices'],
        ];

        return $this->response->setJSON($data);
    }

    public function pieChart()
    {
        $chartModel = new ChartModel();
   		$result = $chartModel->get_pie_chart_details();
    
        return $this->response->setJSON($result);
    }

    public function profitChart()
    {
      	$chartModel = new ChartModel();
   		$result = $chartModel->get_profit_details();
		// Pass the data to the view
        $data = [
            'months' => $result['months'],
            'grossProfitData' => $result['gross_profit'],
            'netProfitData' => $result['net_profit'],
			'balanceData' => $result['balance']
        ];

        return $this->response->setJSON($data);
    }

    public function tradeReceivable()
    {
    	$days = $_GET['days'] ?? 30;

    	$chartModel = new ChartModel();
   		$result = $chartModel->get_trade_receivable($days);

   		return $this->response->setJSON($result);
    }

    public function tradePayable()
    {
    	$days = $_GET['days'] ?? 30;
    	
    	$chartModel = new ChartModel();
   		$result = $chartModel->get_trade_payable($days);

   		return $this->response->setJSON($result);
    }

    public function quickAssets()
    {
    	$chartModel = new ChartModel();
   		$result = $chartModel->get_quick_assets();

   		return $this->response->setJSON($result);
    }

    public function receivableFactsChart()
    {
      	$chartModel = new ChartModel();
   		$result = $chartModel->get_receivable_facts();

        // Pass the data to the view
        $data = [
            'months' => $result['months'],
            'from_date' => $result['from_date'],
            'to_date' => $result['to_date'],
            'withinDueData' => $result['within_due'],
            'overDueData' => $result['overdue'],
            'advancesData' => $result['advances'],
        ];

        return $this->response->setJSON($data);
    }

    public function payableFactsChart()
    {
      	$chartModel = new ChartModel();
   		$result = $chartModel->get_payable_facts();

        // Pass the data to the view
        $data = [
            'months' => $result['months'],
            'from_date' => $result['from_date'],
            'to_date' => $result['to_date'],
            'withinDueData' => $result['within_due'],
            'overDueData' => $result['overdue'],
            'advancesData' => $result['advances'],
        ];

        return $this->response->setJSON($data);
    }

    public function accountsReceivable()
    {
        $from_date = $_GET['from_date'] ?? '';
        $to_date = $_GET['to_date'] ?? '';

        $chartModel = new ChartModel();
        $result = $chartModel->get_receivable_accounts($from_date, $to_date);

        return $this->response->setJSON($result);
    }

    public function accountsPayable()
    {
        $from_date = $_GET['from_date'] ?? '';
        $to_date = $_GET['to_date'] ?? '';

        $chartModel = new ChartModel();
        $result = $chartModel->get_payable_accounts($from_date, $to_date);

        return $this->response->setJSON($result);
    }

    public function keyFacts()
    {
    	$chartModel = new ChartModel();
   		$result = $chartModel->get_key_facts();

   		return $this->response->setJSON($result);
    }

    public function calender()
    {
    	$response = fy_calender();
    	echo "<pre>";print_r($response);
    }

	public function markBranch(){
		$id = $this->request->getPost('id'); 
		$comp_id = $this->request->getPost('comp_id');
		
		print_r($this->DashboardModel->markBranch($id, $comp_id));
		die();
	
	  }

	  public function markFinancial(){
		$id = $this->request->getPost('id'); 
		$comp_id = $this->request->getPost('comp_id');
		
		print_r($this->DashboardModel->markFinancial($id, $comp_id));
		die();
	
	  }

	  public function checkBranch(){
		$comp_id = $this->request->getPost('comp_id');
		
		print_r($this->DashboardModel->checkBranch($comp_id));
		die();
	
	  }
}
