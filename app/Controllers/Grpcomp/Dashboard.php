<?php
namespace App\Controllers\Grpcomp;
use App\Models\Grpcomp\DashboardModel;

use App\Models\Grpcomp\ChartModel;
use App\Controllers\BaseController;
use App\Libraries\auth_session;
use App\Models\Grpcomp\ERPLogModel;

class Dashboard extends BaseController
{
    var	$folder_path;
	function __construct()
    {  
	    helper(['form', 'url']);
	     
		$this->DashboardModel = new DashboardModel();

		$this->ChartModel = new ChartModel();		
		$this->auth_session   = new auth_session();
	    $this->folder_path    = getenv('GroupPath');
	    $this->auth_session->group_restrict();
	    $this->auth_session->role_restrict('CS');
		$this->base_url      =  base_url().'/'.getenv('GroupPath');
        $this->session    	 =  \Config\Services::session();
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->user_id       =  $this->session->get('uuid_aicountly');
		$this->LogModel      =  new ERPLogModel();
    }
    
    public function index()
    {
    	// throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    	
        // get profit and loss and revenue graph on login time, save data in session and show them on dashboartd graph
         $montyhs = months_list();
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
	
         $data= array('folder_path'=> $this->folder_path,'month_keys'=>$all_month_keys,'revenue_keys'=>$revenue_keys,'profit_loss_keys'=>$profit_loss_keys ); 
	     return view($this->folder_path.'dashboard',$data);	
    }	

    public function error()
    {
    	echo view($this->folder_path.'error_page');
    }
    
    	
    
	function makeactive($tabid){
		$this->session->set('s_ctab',$tabid);
		
		$ses_menu_item    = $this->session->get('menu_item'); 
	    $search_tab       = getPrevKey($tabid, $ses_menu_item);
		if(isset($search_tab[0]))  
		 $last_tab_key =  $search_tab[0];
       else
        $last_tab_key  =0;	
	
	 $last_item_url  = $ses_menu_item[$last_tab_key]['url'];	
	 return redirect()->to($last_item_url);
	 die();
	}
	
    function currenttab($tab_id){
           $result = $this->DashboardModel->set_current_tab($tab_id);
      }
    
    function update_tab(){
         $s_ctab =$this->session->get('s_ctab');
        $tab_url  = $_POST['pageclicked'];
        $tab_name = $_POST['pagetitle'];
         $ses_menu_item  = $this->session->get('menu_item');
	    $updated_menus = array();
	    if($ses_menu_item){
	    foreach($ses_menu_item as $row){
		    $tab_id   = $row['menuid'];
		    $tburl    = $row['url'];
		    $tabname  = $row['name'];
		    if($s_ctab==$tab_id)
		    $updated_menus[] = array("name"=>$tab_name,"url"=>$tab_url,"quantity"=>"1","menuid"=>$tab_id);  
		    else
		     $updated_menus[] = array("name"=>$tabname,"url"=>$tburl,"quantity"=>"1","menuid"=>$tab_id);  
	      }
	    }
	   
	   $this->session->set('menu_item', $updated_menus);
       return true; 
    }
    
    function usertabs($type){
        $tab_data = array("tab_name"=>"Dashboard","tab_url"=>$this->base_url.'dashboard','user_id'=>$this->session->get('uuid_aicountly'));
        $result = $this->DashboardModel->add_tab($tab_data);
		return redirect()->to($this->base_url.'dashboard');
        die();
    }
    
   function remove_tab($tab_id){
         $this->session->set('pagefromremove_tab','1');
         $lasturl =  $this->DashboardModel->remove_tab($tab_id);
        return redirect()->to($lasturl);
    }
    
	 public function close_company(){
		 $this->session->remove('ses_grp_id');
		 $this->session->remove('grp_comp_ids');
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
		 
		 $this->session->remove('visted_links_list');
		 
		 
		 return redirect()->to(base_url().'/groupcompany');
		 die;
	}	
	
    public function signout(){
		$this->session->remove('ses_grp_id');
         $this->session->remove('grp_comp_ids');
         $this->session->remove('ses_comp_fy_id'); 
		 $this->session->remove('ses_company_id');
		 $this->session->remove('ses_company_code');
		 $this->session->remove('ses_compl_company_code');
		 $this->session->remove('ses_company_name');
		 $this->session->remove('ses_company_print_name');
		 $this->session->remove('ses_company_short_name');
		 $this->session->remove('ses_company_fy_beginning');
		 $this->session->remove('ses_company_email');
		 $this->session->remove('ses_company_mobile');
		 $this->session->remove('ses_company_wa_mobile');
		 $this->session->remove('ses_company_gstin');
		 $this->session->remove('ses_company_tan_no');
		 $this->session->remove('ses_company_pan_no');		 
		 
		 return redirect()->to(base_url().'/home/companies');
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
        $dataPointsPink = array();
        $dataPointsBlue = array();
        $s1 = array();
    
        // Fetch data for 'c'
        $chartModel = new ChartModel();
        $result = $chartModel->getCashAccountDetails();
    
        // Assuming $queryPink contains the data fetched from the database
        $monthNames = [
	        4 => [
	        		'month' => 'Apr',
	        		'date' => 'Apr 2023',
	        		'total_amount' => 0
	        ],
	        5 => [
	        		'month' => 'May',
	        		'date' => 'May 2023',
	        		'total_amount' => 0
	        ],
	        6 => [
	        		'month' => 'Jun',
	        		'date' => 'Jun 2023',
	        		'total_amount' => 0
	        ],
	        7 => [
	        		'month' => 'Jul',
	        		'date' => 'Jul 2023',
	        		'total_amount' => 0
	        ],
	        8 => [
	        		'month' => 'Aug',
	        		'date' => 'Aug 2023',
	        		'total_amount' => 0
	        ],
	        9 => [
	        		'month' => 'Sep',
	        		'date' => 'Sep 2023',
	        		'total_amount' => 0
	        ],
	        10 => [
	        		'month' => 'Oct',
	        		'date' => 'Oct 2023',
	        		'total_amount' => 0
	        ],
	        11 => [
	        		'month' => 'Nov',
	        		'date' => 'Nov 2023',
	        		'total_amount' => 0
	        ],
	        12 => [
	        		'month' => 'Dec',
	        		'date' => 'Dec 2023',
	        		'total_amount' => 0
	        ],
	        1 =>  [
	        		'month' => 'Jan',
	        		'date' => 'Jan 2024',
	        		'total_amount' => 0
	        ],
	        2 =>  [
	        		'month' => 'Feb',
	        		'date' => 'Feb 2024',
	        		'total_amount' => 0
	        ],
	        3 =>  [
	        		'month' => 'Mar',
	        		'date' => 'Mar 2024',
	        		'total_amount' => 0
	        ],
        ];

        
    
        // Initialize an array to store the aggregated values for each month
        $aggregatedDataPink = $monthNames;
        $aggregatedDataBlue = $monthNames;
    
        foreach ($result as $account) {

            $resultPink = $chartModel->getTotalAmount($account['acc_id'], 'd');

            $resultBlue = $chartModel->getTotalAmount($account['acc_id'], 'c');
    
            // Update aggregated values for 'd'
            foreach ($resultPink as $row) {
                $aggregatedDataPink[(int)$row['month']]['total_amount'] += round($row['total_amount']);
            }
    
            // Update aggregated values for 'c'
            foreach ($resultBlue as $row) {
                $aggregatedDataBlue[(int)$row['month']]['total_amount'] += round($row['total_amount']);
            }
        }

       

        
    
        // Now $aggregatedDataPink and $aggregatedDataBlue contain the aggregated data for each month
    
        // Format the data into the required format
        foreach ($monthNames as $key => $month) {
            $dataPointsPink[] = $aggregatedDataPink[$key];
    
            $dataPointsBlue[] = $aggregatedDataBlue[$key];
        }
    
        // Now $dataPointsPink and $dataPointsBlue contain data for all months,
        // with total_amount set to 0 for months without data
    
        $data = array(
            'pink' => $dataPointsPink,
            'blue' => $dataPointsBlue
        );
    
        return $this->response->setJSON($data);
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

        return $this->response->setJSON(['labels' => $result['months'], 'prices' => $result['prices']]);
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
            'netProfitData' => $result['net_profit']
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
}
