<?php
namespace App\Models\Grpcomp;

use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Libraries\enc_string;
use App\Models\CommonModel;
use App\Models\Admin\ReportsModel;

class ChartModel extends Model	{

  public function __construct() {
    parent::__construct();        
    $this->externaldb    = new externaldb();	
    $this->CommonModel   = new CommonModel();
    $this->ReportsModel = new ReportsModel();
    $this->db            =  $this->externaldb->get_company_db();
    $this->session       =  \Config\Services::session();
    $this->company_id    =  $this->session->get('ses_company_id');
    $this->comp_fy_id    =  $this->session->get('ses_comp_fy_id');
    $this->user_id       =  $this->session->get('uuid_aicountly');
    $this->enc_string    =  new enc_string();
  }

  function get_sub_group_ids($array){
      
      if(!empty($array)){
        $tbl_name = $this->company_id.'_acctgroupn_'.$this->comp_fy_id;
        $data = $this->db->table($tbl_name)
              ->select('acc_grp_id')
              ->whereIn('under_main_grp_id', $array)
              ->get()->getResultArray();
      
      
        if($data){
          foreach($data as $key => $value) {
            array_push($array, $value['acc_grp_id']);
          }
        }
      }
      return $array;
  }

  function get_cash_groups()
  {
      
    $groups = [23,21];
    $array = $this->get_sub_group_ids($groups);
      
    return $array;
  }
 
  function getCashAccountDetails()
  {
      $cash_groups = $this->get_cash_groups();

      $account_master_tbl = $this->company_id.'_acctmaster_'.$this->comp_fy_id;
      $builder = $this->db->table($account_master_tbl); 
      $builder->whereIn('acc_grp_id', $cash_groups);
      return $builder->get()->getResultArray();
  }

  function getTotalAmount($acc_id, $drcr)
  {
      $acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$acc_id.'_'.$this->comp_fy_id;
      $builder = $this->db->table($acc_txn_tbl);
      $builder->select('SUM(`acc_txn_amount`) AS total_amount, DATE_FORMAT(`acc_txn_date`, "%m") AS month');
      $builder->where('acc_id', $acc_id);
	  if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
      $builder->where('acc_txn_drcr', $drcr);
      $builder->groupBy('month');
      return $builder->get()->getResultArray();
  }

  function getAccountsByGroup($groups)
  {
    $acc_groups = $this->get_sub_group_ids($groups);

    $account_master_tbl = $this->company_id.'_acctmaster_'.$this->comp_fy_id;
    $builder = $this->db->table($account_master_tbl); 
    $builder->whereIn('acc_grp_id', $acc_groups);
    return $builder->get()->getResultArray();

  }

  function get_cash_equivalent_details()
  {
      $fy_months_list =  $this->CommonModel->get_fy_info($this->session->get('ses_comp_fy_id'),$this->company_id);
      $start_date     = strtotime($fy_months_list['fy_begndt']);
      $end_date       = strtotime($fy_months_list['fy_end']); 
      
      $blue = [];
      $pink = [];

      while( $start_date <= $end_date ) {
        $from_date = date( 'Y-m-01', $start_date );
        $to_date = date( 'Y-m-t', $start_date );

        $asset = $this->getAccountBalance(23,$from_date,$to_date);
        $borrowing = $this->getAccountBalance(21,$from_date,$to_date);

        $blue[] = [
          'month' => date( 'M', $start_date ),
          'total_amount' => round($asset['amount']),
          'cash' => round($asset['cash']),
          'bank' => round($asset['bank']),
        ];

        $pink[] = [
          'month' => date( 'M', $start_date ),
          'total_amount' => round($borrowing['amount']),
        ];

        $start_date = strtotime( '+1 month', $start_date );
      }
      
      return ['blue' => $blue, 'pink' => $pink];
  }

  function get_quick_assets()
  {
      $fy_months_list =  $this->CommonModel->get_fy_info($this->session->get('ses_comp_fy_id'),$this->company_id);
      $start_date     = strtotime($fy_months_list['fy_begndt']);
      $end_date       = strtotime($fy_months_list['fy_end']); 

      $from_date = date( 'Y-m-01', $start_date );
      $to_date = date( 'Y-m-t', $end_date );

      $asset = $this->getAccountBalance(23,$from_date,$to_date);
      $borrowing = $this->getAccountBalance(21,$from_date,$to_date);

      $data = [
        'qa_limit' => round($borrowing['amount']),
        'qa_cash' => round($asset['cash']),
        'qa_bank' => round($asset['bank']),
      ];

      return $data;
  }

  function getAccountBalance($group_id,$from_date,$to_date)
  {
    $array = $this->get_sub_group_ids([$group_id]);

    $amount = 0;

    $cash = 0;
    $bank = 0;

    $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
    $builder = $this->db->table($account_master_tbl); 
    $builder->select(array('acc_id','acc_name','acc_grp_id'));
    $builder->whereIn('acc_grp_id', $array);
    $accounts = $builder->get()->getResultArray();
    if($accounts){
      foreach ($accounts as $account) {

        $acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$account['acc_id'].'_'.$this->session->get('ses_comp_fy_id');
        $builder = $this->db->table($acc_txn_tbl);
        $builder->select('SUM(CASE WHEN `acc_txn_drcr`="c" THEN (`acc_txn_amount`*-1) ELSE `acc_txn_amount` END) AS amount');
        $builder->where('acc_id', $account['acc_id']);
        $builder->where('acc_txn_date >=', $from_date);
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
        $builder->where('acc_txn_date <=', $to_date);
        $transaction = $builder->get()->getRowArray();
        if($transaction){
          $amount += parseAmount($transaction['amount']);

          if($group_id == 23)
          {
            if(str_contains(strtolower($account['acc_name']), 'cash') && !str_contains(strtolower($account['acc_name']), 'bank')){
              $cash += parseAmount($transaction['amount']);
            }
            else{
              $bank += parseAmount($transaction['amount']);
            }
          }
          
        }
      }
    }

    return [
      'cash' => parseAmount($cash),
      'bank' => parseAmount($bank),
      'amount' => parseAmount($amount),
    ];
  }

  function get_revenue_details()
  {
      $acc_grp_parent_id_array = [8,10,12];

      $fy_months_list =  $this->CommonModel->get_fy_info($this->session->get('ses_comp_fy_id'),$this->company_id);
      $start_date     = strtotime($fy_months_list['fy_begndt']);
      $end_date       = strtotime($fy_months_list['fy_end']); 
      
      $months = [];
      $prices = [];

      while( $start_date <= $end_date ) {
        $from_date = date( 'Y-m-01', $start_date );
        $to_date = date( 'Y-m-t', $start_date );

        $amount = -$this->get_parent_amount_pnl($acc_grp_parent_id_array, $from_date, $to_date);

        $months[] = date( 'M', $start_date );
        $prices[] = round($amount);

        $start_date = strtotime( '+1 month', $start_date );
      }
      
      return ['months' => $months, 'prices' => $prices];
  }

  function get_net_worth_details()
  {
      $acc_grp_parent_id_array = [1];

      $fy_months_list =  $this->CommonModel->get_fy_info($this->session->get('ses_comp_fy_id'),$this->company_id);
      $start_date     = strtotime($fy_months_list['fy_begndt']);
      $end_date       = strtotime($fy_months_list['fy_end']); 
      
      $months = [];
      $prices = [];

      while( $start_date <= $end_date ) {
        $from_date = date( 'Y-m-01', $start_date );
        $to_date = date( 'Y-m-t', $start_date );
        
        $amount = 0;
        $amount += -$this->get_parent_amount($acc_grp_parent_id_array, $from_date, $to_date);
        $amount += -$this->get_pl_details($from_date,$to_date)['balance'];

        $months[] = date( 'M', $start_date );
        $prices[] = round($amount);

        $start_date = strtotime( '+1 month', $start_date );
      }
  
      return ['months' => $months, 'prices' => $prices];
  }

  function get_profit_details()
  {


      $fy_months_list =  $this->CommonModel->get_fy_info($this->session->get('ses_comp_fy_id'),$this->company_id);
      $start_date     = strtotime($fy_months_list['fy_begndt']);
      $end_date       = strtotime($fy_months_list['fy_end']); 
      
      $months = [];
      $net_profit = [];
      $gross_profit = [];

      while( $start_date <= $end_date ) {
        $from_date = date( 'Y-m-01', $start_date );
        $to_date = date( 'Y-m-t', $start_date );
        
        $amount = 0;
        $get_pl_details = $this->get_pl_details($from_date,$to_date);
        $gross_profit[] = round($get_pl_details['gross_profit']);
        $net_profit[] = round($get_pl_details['net_profit']);

        $months[] = date( 'M', $start_date );
        
        $start_date = strtotime( '+1 month', $start_date );
      }
  
      return ['months' => $months, 'gross_profit' => $gross_profit, 'net_profit' => $net_profit];
  }

  function get_pie_chart_details()
  {
    $fy_months_list =  $this->CommonModel->get_fy_info($this->session->get('ses_comp_fy_id'),$this->company_id);
    $start_date     = strtotime($fy_months_list['fy_begndt']);
    $end_date       = strtotime($fy_months_list['fy_end']);

    $from_date = date( 'Y-m-01', $start_date );
    $to_date = date( 'Y-m-t', $end_date );


    // $opening_stock = 0;
    // $purchase = $this->get_parent_amount_pnl([7],$from_date,$to_date);
    // $closing_stock = $this->ReportsModel->get_closing_stock_details($from_date,$to_date);

    // $cogs = round($opening_stock) + round($purchase) - round($closing_stock);

    $result1 = $this->get_parent_group_details_pl(11, $from_date, $to_date);
    $result2 = $this->get_parent_group_details_pl(13, $from_date, $to_date);
    $result = array_merge($result1,$result2);

    $final = [];
    $others = [];

    // $final[] = [
    //         'group' => 'COGS',
    //         'amount' => $cogs,
    //     ];

    if(count($result) > 0){
      $max = max(array_column($result, 'balance'));
      $index = array_search($max, array_column($result, 'balance'));
      $final[] = [
          'group' => $result[$index]['group_name'],
          'amount' => round($result[$index]['balance']),
      ];
      array_splice($result, $index, 1);
    }
    if(count($result) > 0){
      $max = max(array_column($result, 'balance'));
      $index = array_search($max, array_column($result, 'balance'));
      $final[] = [
          'group' => $result[$index]['group_name'],
          'amount' => round($result[$index]['balance']),
      ];
      array_splice($result, $index, 1);
    }
    if(count($result) > 0){
      $max = max(array_column($result, 'balance'));
      $index = array_search($max, array_column($result, 'balance'));
      $final[] = [
          'group' => $result[$index]['group_name'],
          'amount' => round($result[$index]['balance']),
      ];
      array_splice($result, $index, 1);
    }
    if(count($result) > 0){
      $max = max(array_column($result, 'balance'));
      $index = array_search($max, array_column($result, 'balance'));
      $final[] = [
          'group' => $result[$index]['group_name'],
          'amount' => round($result[$index]['balance']),
      ];
      array_splice($result, $index, 1);
    }
    if(count($result) > 0){
      $max = max(array_column($result, 'balance'));
      $index = array_search($max, array_column($result, 'balance'));
      $final[] = [
          'group' => $result[$index]['group_name'],
          'amount' => round($result[$index]['balance']),
      ];
      array_splice($result, $index, 1);
    }
    
    if(count($result) > 0){
      $balance = 0;
      foreach ($result as $key => $value) {
         $balance += $value['balance'];
      }
      $final[] = [
          'group' => 'Others',
          'amount' => round($balance),
      ];
    }
    return $final;

  }


  function get_pl_details($from_date,$to_date)
  {
      $array1 = [];
      $array2 = [];

      $l_step1_total = 0;
      $r_step1_total = 0;

      $l_step1_total += $this->get_parent_amount_pnl([6],$from_date,$to_date); // Opening Stock

      $l_step1_total += $this->get_parent_amount_pnl([7],$from_date,$to_date); // PURCHASES

      $l_step1_total += $this->get_parent_amount_pnl([11],$from_date,$to_date); // DIRECTO EXPENSES

      $r_step1_total += -$this->get_parent_amount_pnl([8],$from_date,$to_date); // SALES

      $r_step1_total += -$this->get_parent_amount_pnl([10],$from_date,$to_date); // DIRECT INCOMES


      $r_step1_total += $this->ReportsModel->get_closing_stock_details($from_date,$to_date); // CLOSING STOCK

    $l_step2_total = 0;
    $r_step2_total = 0;
    
    $gross_profit = 0;

    if($l_step1_total > $r_step1_total){
      $diff = $l_step1_total - $r_step1_total;
      $l_step2_total += $diff;

      $gross_profit = -$diff;
    }
    if($l_step1_total < $r_step1_total){
      $diff = $r_step1_total - $l_step1_total;
      $r_step2_total += $diff;

      $gross_profit = $diff;
    }

    $l_step2_total += $this->get_parent_amount_pnl([13],$from_date,$to_date); // INDIRECT EXPENSES

    $r_step2_total += -$this->get_parent_amount_pnl([12],$from_date,$to_date); // INDIRECT INCOMES

    $balance = parseAmount($l_step2_total) - parseAmount($r_step2_total);

    $net_profit = 0;
    if($l_step2_total < $r_step2_total)
    {
      $net_profit = -$balance;
    }
    else{
      $net_profit = $balance;
    }
      
      return [
        'balance' => parseAmount($balance),
        'net_profit' => parseAmount($net_profit),
        'gross_profit' => parseAmount($gross_profit),
      ];

  }

  function get_parent_amount_pnl($acc_grp_parent_id_array, $from_date, $to_date)
  {
    $account_grp_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
    $sundry_master_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
    $acc_grp_par_tbl = $this->company_id.'_grpparentn_'.$this->session->get('ses_comp_fy_id');

    $amount = 0;
    $parents =  $this->db->table($acc_grp_par_tbl)->whereIn('acc_grp_parent_id', $acc_grp_parent_id_array)->get()->getResultArray();

    if($parents)
    {
      foreach ($parents as $parent) 
      {

        $result =  $this->db->table($account_grp_tbl)->where('acc_grp_primary', 'Y')
                               ->where('acc_grp_parent_id', $parent['acc_grp_parent_id'])
                               ->orderBy('acc_grp_name', 'asc')
                               ->get()->getResultArray();
        
        if($result) 
        {
          foreach ($result as $key => $value) 
          {

            //check accounts
            $builder = $this->db->table($account_master_tbl); 
            $builder->select(array('acc_id','acc_name','acc_grp_id'));
            $builder->where('acc_grp_id', $value['acc_grp_id']);
            $builder->orderBy('acc_name', 'asc');
            $accounts = $builder->get()->getResultArray();
            if($accounts){
              foreach ($accounts as $account) {

  
                $acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$account['acc_id'].'_'.$this->session->get('ses_comp_fy_id');
                $builder = $this->db->table($acc_txn_tbl);
                $builder->select('SUM(CASE WHEN `acc_txn_drcr`="c" THEN (`acc_txn_amount`*-1) ELSE `acc_txn_amount` END) AS amount');
                $builder->where('acc_id', $account['acc_id']);
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
                $builder->where('acc_txn_date >=', $from_date);
                $builder->where('acc_txn_date <=', $to_date);
                $transaction = $builder->get()->getRowArray();
                if($transaction){
                  $amount += parseAmount($transaction['amount']);
                }
              }
            }

            //check sundry accounts
            $builder = $this->db->table($sundry_master_tbl); 
            $builder->select(array('bill_sundry_id','bill_sundry_name','acc_grp_id'));
            $builder->where('acc_grp_id', $value['acc_grp_id']);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
            $builder->orderBy('bill_sundry_name', 'asc');
            $sundry_accounts = $builder->get()->getResultArray();
            if($sundry_accounts){
              foreach ($sundry_accounts as $sundry_account) {

                $bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$sundry_account['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
                $builder = $this->db->table($bs_txn_tbl);
                $builder->select('SUM(CASE WHEN `sundry_txn_drcr`="c" THEN (`sundry_txn_amount`*-1) ELSE `sundry_txn_amount` END) AS amount');
                $builder->where('bill_sundry_id', $sundry_account['bill_sundry_id']);
                $builder->where('sundry_txn_date >=', $from_date);
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
                $builder->where('sundry_txn_date <=', $to_date);
                $transaction = $builder->get()->getRowArray();
                if($transaction){
                    $amount += parseAmount($transaction['amount']);
                }
              }
            }


            //check sub group
            $result2 = $this->db->table($account_grp_tbl)->where('under_main_grp_id',$value['acc_grp_id'])->orderBy('acc_grp_name', 'asc')->get()->getResultArray();
            if($result2)
            {
              foreach ($result2 as $key2 => $value2) {

                //check accounts
                $builder = $this->db->table($account_master_tbl); 
                $builder->select(array('acc_id','acc_name','acc_grp_id'));
                $builder->where('acc_grp_id', $value2['acc_grp_id']);
                $builder->orderBy('acc_name', 'asc');
                $accounts = $builder->get()->getResultArray();
                if($accounts){
                  foreach ($accounts as $account) {

      
                    $acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$account['acc_id'].'_'.$this->session->get('ses_comp_fy_id');
                    $builder = $this->db->table($acc_txn_tbl);
                    $builder->select('SUM(CASE WHEN `acc_txn_drcr`="c" THEN (`acc_txn_amount`*-1) ELSE `acc_txn_amount` END) AS amount');
                    $builder->where('acc_id', $account['acc_id']);
                    $builder->where('acc_txn_date >=', $from_date);
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
                    $builder->where('acc_txn_date <=', $to_date);
                    $transaction = $builder->get()->getRowArray();
                    if($transaction){
                      $amount += parseAmount($transaction['amount']);
                    }
                  }
                }

                //check sundry accounts
                $builder = $this->db->table($sundry_master_tbl); 
                $builder->select(array('bill_sundry_id','bill_sundry_name','acc_grp_id'));
                $builder->where('acc_grp_id', $value2['acc_grp_id']);
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
                $builder->orderBy('bill_sundry_name', 'asc');
                $sundry_accounts = $builder->get()->getResultArray();
                if($sundry_accounts){
                  foreach ($sundry_accounts as $sundry_account) {

                    $bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$sundry_account['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
                    $builder = $this->db->table($bs_txn_tbl);
                    $builder->select('SUM(CASE WHEN `sundry_txn_drcr`="c" THEN (`sundry_txn_amount`*-1) ELSE `sundry_txn_amount` END) AS amount');
                    $builder->where('bill_sundry_id', $sundry_account['bill_sundry_id']);
                    $builder->where('sundry_txn_date >=', $from_date);
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
                    $builder->where('sundry_txn_date <=', $to_date);
                    $transaction = $builder->get()->getRowArray();
                    if($transaction){
                        $amount += parseAmount($transaction['amount']);
                    }
                  }
                }

              }
            }
          }
        }

        $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
        $result3 =  $this->db->table($account_master_tbl)
                       ->where('acc_grp_parent_id', $parent['acc_grp_parent_id'])
                       ->orderBy('acc_name', 'asc')
                       ->get()->getResultArray();
        if($result3) 
        {
          foreach ($result3 as $key3 => $value3) 
          {
            $acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value3['acc_id'].'_'.$this->session->get('ses_comp_fy_id');
            $builder = $this->db->table($acc_txn_tbl);
            $builder->select('SUM(CASE WHEN `acc_txn_drcr`="c" THEN (`acc_txn_amount`*-1) ELSE `acc_txn_amount` END) AS amount');
            $builder->where('acc_id', $value3['acc_id']);
            $builder->where('acc_txn_date >=', $from_date);
			if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
            $builder->where('acc_txn_date <=', $to_date);
            $transaction = $builder->get()->getRowArray();
            if($transaction){
              $amount += parseAmount($transaction['amount']);
            }
          }
        }

        $sundry_master_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!=''){
		
		 $result4 =  $this->db->table($sundry_master_tbl)
                       ->where('acc_grp_parent_id', $parent['acc_grp_parent_id'])
					   ->where('bo_id', $this->session->get('ses_boid'))
                       ->orderBy('bill_sundry_name', 'asc')
                       ->get()->getResultArray();	
		}else{

        $result4 =  $this->db->table($sundry_master_tbl)
                       ->where('acc_grp_parent_id', $parent['acc_grp_parent_id'])
                       ->orderBy('bill_sundry_name', 'asc')
                       ->get()->getResultArray();
		}
        if($result4) 
        {
          foreach ($result4 as $key4 => $value4) 
          {
              $bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$value4['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
              $builder = $this->db->table($bs_txn_tbl);
              $builder->select('SUM(CASE WHEN `sundry_txn_drcr`="c" THEN (`sundry_txn_amount`*-1) ELSE `sundry_txn_amount` END) AS amount');
              $builder->where('bill_sundry_id', $value4['bill_sundry_id']);
              $builder->where('sundry_txn_date >=', $from_date);
			  if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
              $builder->where('sundry_txn_date <=', $to_date);
              $transaction = $builder->get()->getRowArray();
              if($transaction){
                  $amount += parseAmount($transaction['amount']);
              }
          }
        }
      }
    }

    return parseAmount($amount);
  }

  function get_parent_amount($acc_grp_parent_id_array, $from_date, $to_date)
  {
    $account_grp_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
    $sundry_master_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
    $acc_grp_par_tbl = $this->company_id.'_grpparentn_'.$this->session->get('ses_comp_fy_id');

    $amount = 0;
    $parents =  $this->db->table($acc_grp_par_tbl)->whereIn('acc_grp_parent_id', $acc_grp_parent_id_array)->get()->getResultArray();

    if($parents)
    {
      foreach ($parents as $parent) 
      {

        $result =  $this->db->table($account_grp_tbl)->where('acc_grp_primary', 'Y')
                               ->where('acc_grp_parent_id', $parent['acc_grp_parent_id'])
                               ->orderBy('acc_grp_name', 'asc')
                               ->get()->getResultArray();
        
        if($result) 
        {
          foreach ($result as $key => $value) 
          {

            //check accounts
            $builder = $this->db->table($account_master_tbl); 
            $builder->select(array('acc_id','acc_name','acc_grp_id'));
            $builder->where('acc_grp_id', $value['acc_grp_id']);
            $builder->orderBy('acc_name', 'asc');
            $accounts = $builder->get()->getResultArray();
            if($accounts){
              foreach ($accounts as $account) {

  
                $acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$account['acc_id'].'_'.$this->session->get('ses_comp_fy_id');
                $builder = $this->db->table($acc_txn_tbl);
                $builder->select('acc_bal');
                $builder->where('acc_id', $account['acc_id']);
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
                $builder->where('acc_txn_date <=', $to_date);
                $builder->orderBy('acc_txn_date', 'desc');
                $builder->orderBy('voucher_txn_id', 'desc');
                $builder->orderBy('acc_txn_id', 'desc');
                $builder->limit(1);
                $transaction = $builder->get()->getRowArray();
                if($transaction){
                  $amount += parseAmount($transaction['acc_bal']);
                }
                else
                {
                  $get_opn_balance_info = $this->acc_opn_balance_info($account['acc_id']);
                  if($get_opn_balance_info){
                    $amount += parseAmount($get_opn_balance_info['acc_op_bal']);
                  }
                }
              }
            }

            //check sundry accounts
            $builder = $this->db->table($sundry_master_tbl); 
            $builder->select(array('bill_sundry_id','bill_sundry_name','acc_grp_id'));
            $builder->where('acc_grp_id', $value['acc_grp_id']);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
            $builder->orderBy('bill_sundry_name', 'asc');
            $sundry_accounts = $builder->get()->getResultArray();
            if($sundry_accounts){
              foreach ($sundry_accounts as $sundry_account) {

                $bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$sundry_account['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
                $builder = $this->db->table($bs_txn_tbl);
                $builder->select('sundry_bal');
                $builder->where('bill_sundry_id', $sundry_account['bill_sundry_id']);
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
                $builder->where('sundry_txn_date <=', $to_date);
                $builder->orderBy('sundry_txn_date', 'desc');
                $builder->orderBy('sundry_txn_id', 'desc');
                $builder->limit(1);
                $transaction = $builder->get()->getRowArray();
                if($transaction){
                    $amount += parseAmount($transaction['sundry_bal']);
                }
                else{
                  $bill_sundry_op_balance = $this->bill_sundry_op_balance($sundry_account['bill_sundry_id']);
                  if($bill_sundry_op_balance){
                      $amount +=  parseAmount($bill_sundry_op_balance['bsd_op_bal']);
                  }
                }
              }
            }


            //check sub group
            $result2 = $this->db->table($account_grp_tbl)->where('under_main_grp_id',$value['acc_grp_id'])->orderBy('acc_grp_name', 'asc')->get()->getResultArray();
            if($result2)
            {
              foreach ($result2 as $key2 => $value2) {

                //check accounts
                $builder = $this->db->table($account_master_tbl); 
                $builder->select(array('acc_id','acc_name','acc_grp_id'));
                $builder->where('acc_grp_id', $value2['acc_grp_id']);
                $builder->orderBy('acc_name', 'asc');
                $accounts = $builder->get()->getResultArray();
                if($accounts){
                  foreach ($accounts as $account) {

      
                    $acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$account['acc_id'].'_'.$this->session->get('ses_comp_fy_id');
                    $builder = $this->db->table($acc_txn_tbl);
                    $builder->select('acc_bal');
                    $builder->where('acc_id', $account['acc_id']);
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
                    $builder->where('acc_txn_date <=', $to_date);
                    $builder->orderBy('acc_txn_date', 'desc');
                    $builder->orderBy('voucher_txn_id', 'desc');
                    $builder->orderBy('acc_txn_id', 'desc');
                    $builder->limit(1);
                    $transaction = $builder->get()->getRowArray();
                    if($transaction){
                      $amount += parseAmount($transaction['acc_bal']);
                    }
                    else
                    {
                      $get_opn_balance_info = $this->acc_opn_balance_info($account['acc_id']);
                      if($get_opn_balance_info){
                        $amount += parseAmount($get_opn_balance_info['acc_op_bal']);
                      }
                    }
                  }
                }

                //check sundry accounts
                $builder = $this->db->table($sundry_master_tbl); 
                $builder->select(array('bill_sundry_id','bill_sundry_name','acc_grp_id'));
                $builder->where('acc_grp_id', $value2['acc_grp_id']);
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
                $builder->orderBy('bill_sundry_name', 'asc');
                $sundry_accounts = $builder->get()->getResultArray();
                if($sundry_accounts){
                  foreach ($sundry_accounts as $sundry_account) {

                    $bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$sundry_account['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
                    $builder = $this->db->table($bs_txn_tbl);
                    $builder->select('sundry_bal');
                    $builder->where('bill_sundry_id', $sundry_account['bill_sundry_id']);
                    $builder->where('sundry_txn_date <=', $to_date);
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
                    $builder->orderBy('sundry_txn_date', 'desc');
                    $builder->orderBy('sundry_txn_id', 'desc');
                    $builder->limit(1);
                    $transaction = $builder->get()->getRowArray();
                    if($transaction){
                        $amount += parseAmount($transaction['sundry_bal']);
                    }
                    else{
                      $bill_sundry_op_balance = $this->bill_sundry_op_balance($sundry_account['bill_sundry_id']);
                      if($bill_sundry_op_balance){
                         $amount +=  parseAmount($bill_sundry_op_balance['bsd_op_bal']);
                      }
                    }
                  }
                }

              }
            }
          }
        }

        $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
        $result3 =  $this->db->table($account_master_tbl)
                       ->where('acc_grp_parent_id', $parent['acc_grp_parent_id'])
                       ->orderBy('acc_name', 'asc')
                       ->get()->getResultArray();
        if($result3) 
        {
          foreach ($result3 as $key3 => $value3) 
          {
            $acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value3['acc_id'].'_'.$this->session->get('ses_comp_fy_id');
            $builder = $this->db->table($acc_txn_tbl);
            $builder->select('acc_bal');
			if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
            $builder->where('acc_id', $value3['acc_id']);
            $builder->where('acc_txn_date <=', $to_date);
            $builder->orderBy('acc_txn_date', 'desc');
            $builder->orderBy('voucher_txn_id', 'desc');
            $builder->orderBy('acc_txn_id', 'desc');
            $builder->limit(1);
            $transaction = $builder->get()->getRowArray();
            if($transaction){
              $amount += parseAmount($transaction['acc_bal']);
            }
            else
            {
              $get_opn_balance_info = $this->acc_opn_balance_info($value3['acc_id']);
              if($get_opn_balance_info){
               $amount += parseAmount($get_opn_balance_info['acc_op_bal']);
              }
            }
          }
        }

        $sundry_master_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
		
		if($this->session->get('ses_boid')!=''){
				
					$result4 =  $this->db->table($sundry_master_tbl)
                       ->where('acc_grp_parent_id', $parent['acc_grp_parent_id'])
					   ->where('bo_id', $this->session->get('ses_boid'))
                       ->orderBy('bill_sundry_name', 'asc')
                       ->get()->getResultArray();
	  }else{
        $result4 =  $this->db->table($sundry_master_tbl)
                       ->where('acc_grp_parent_id', $parent['acc_grp_parent_id'])
                       ->orderBy('bill_sundry_name', 'asc')
                       ->get()->getResultArray();
	  }
        if($result4) 
        {
          foreach ($result4 as $key4 => $value4) 
          {
              $bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$value4['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
              $builder = $this->db->table($bs_txn_tbl);
              $builder->select('sundry_bal');
              $builder->where('bill_sundry_id', $value4['bill_sundry_id']);
              $builder->where('sundry_txn_date <=', $to_date);
			  if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
              $builder->orderBy('sundry_txn_date', 'desc');
              $builder->orderBy('sundry_txn_id', 'desc');
              $builder->limit(1);
              $transaction = $builder->get()->getRowArray();
              if($transaction){
                $amount += parseAmount($transaction['sundry_bal']);
              }
              else{
                $bill_sundry_op_balance = $this->bill_sundry_op_balance($value4['bill_sundry_id']);
                if($bill_sundry_op_balance){
                   $amount +=  parseAmount($bill_sundry_op_balance['bsd_op_bal']);
                }
              }
          }
        }

      }
    }
  
    return parseAmount($amount);
  }

  function acc_opn_balance_info($acc_id){
    $accoppybal_tbl =$this->company_id.'_accoppybal_'.$this->session->get('ses_comp_fy_id');
	 if($this->session->get('ses_boid')!=''){
		  return $this->db->table($accoppybal_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('acc_id', $acc_id)->get()->getRowArray();  
	
	 }
			else
    return $this->db->table($accoppybal_tbl)->where('acc_id', $acc_id)->get()->getRowArray();  
  }

  function bill_sundry_op_balance($id)
  {
    $bsdoppybal_tbl = $this->company_id.'_bsdoppybal_'.$this->session->get('ses_comp_fy_id');
	if($this->session->get('ses_boid')!=''){
		 return $this->db->table($bsdoppybal_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('bill_sundry_id', $id)->get()->getRowArray(); 
	}else
    return $this->db->table($bsdoppybal_tbl)->where('bill_sundry_id', $id)->get()->getRowArray(); 
  }

  function get_trade_receivable($days)
  {
    
    $date = date('Y-m-d', strtotime("+${days} day"));

    $from_date = '';
    $to_date = '';
    $data['all'] = $this->get_trade_total(22, $from_date, $to_date);

    // receipts not due
    $from_date = $date;
    $to_date = '';
    $data['notdue'] = $this->get_trade_total(22, $from_date, $to_date);

    // current receivale
    $from_date = date('Y-m-d');
    $to_date = $date;
    $data['due'] = $this->get_trade_total(22, $from_date, $to_date);

    // overdue
    $from_date = '';
    $to_date = date('Y-m-d');
    $data['overdue'] = $this->get_trade_total(22, $from_date, $to_date);

    //rest
    $from_date = '';
    $to_date = '';
    $data['rest'] = $this->get_trade_total(22, $from_date, $to_date, true);

    return $data;
  }

  function get_trade_total($group_id, $from_date, $to_date, $reverse = false, $final_date = '')
  {
    $sundry_groups = $this->get_sub_group_ids([$group_id]);
    
    $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
    $bill_master_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
    $bill_txn_tbl = $this->company_id.'_billstxnnn_'.$this->session->get('ses_comp_fy_id');
    
    $builder = $this->db->table($account_master_tbl); 
    $builder->select('acc_id, acc_name');
    $builder->where('comp_id', $this->company_id);    
    $builder->whereIn('acc_grp_id', $sundry_groups);
    $result_ = $builder->get()->getResultArray();
    
    $total_balance = 0;
    
    foreach($result_ as $key_ => $value_)
    {
        $account_id = $value_['acc_id'];

        $builder = $this->db->table($bill_master_tbl);
        $builder->select($bill_master_tbl.'.*');
        $builder->where('acc_id', $account_id);
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
        if($from_date != '')
          $builder->where('DATE(bill_due_date) >', $from_date);
        if($to_date != '')
          $builder->where('DATE(bill_due_date) <=', $to_date);
        $result = $builder->get()->getResultArray();
        
        foreach($result as $key => $value)
        {
            $builder = $this->db->table($bill_txn_tbl); 
            $builder->select('bills_txn_bal');
            $builder->where('bills_ref_id', $value['bills_ref_id']);
			if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));

            if($final_date != '')
              $builder->where('DATE(bills_txn_date) <=', $final_date);
            $builder->orderBy('bills_txn_date', 'desc');
            $builder->orderBy('bills_txn_id', 'desc');
            $builder->limit(1);
            $bill_txn = $builder->get()->getRowArray();

            if($group_id == 22)
            {
                if($bill_txn){
                    $balance = parseAmount($bill_txn['bills_txn_bal']);
                    if($balance > 0 && !$reverse){
                        $total_balance += $balance;
                    }
                    if($balance < 0 && $reverse){
                        $total_balance += abs($balance);
                    }
                }
                else{
                    $balance = parseAmount($value['bill_op_bal']);
                    if($balance > 0 && !$reverse){
                        $total_balance += $balance;
                    }
                    if($balance < 0 && $reverse){
                        $total_balance += abs($balance);
                    }
                }
            }
            if($group_id == 16)
            {
                if($bill_txn){
                    $balance = parseAmount($bill_txn['bills_txn_bal']);
                    if($balance < 0 && !$reverse){
                        $total_balance += abs($balance);
                    }
                    if($balance > 0 && $reverse){
                        $total_balance += $balance;
                    }
                }
                else{
                    $balance = parseAmount($value['bill_op_bal']);
                    if($balance < 0 && !$reverse){
                        $total_balance += abs($balance);
                    }
                    if($balance > 0 && $reverse){
                        $total_balance += $balance;
                    }
                }
            }

            
        }
    }

    return parseAmount($total_balance);
  }

  function get_trade_payable($days)
  {
    
    $date = date('Y-m-d', strtotime("+${days} day"));

    $from_date = '';
    $to_date = '';
    $data['all'] = $this->get_trade_total(16, $from_date, $to_date);

    //  not due
    $from_date = $date;
    $to_date = '';
    $data['notdue'] = $this->get_trade_total(16, $from_date, $to_date);

    // current 
    $from_date = date('Y-m-d');
    $to_date = $date;
    $data['due'] = $this->get_trade_total(16, $from_date, $to_date);

    // overdue
    $from_date = '';
    $to_date = date('Y-m-d');
    $data['overdue'] = $this->get_trade_total(16, $from_date, $to_date);

    //rest
    $from_date = '';
    $to_date = '';
    $data['rest'] = $this->get_trade_total(16, $from_date, $to_date, true);

    return $data;
  }


  function get_parent_group_details_pl($acc_grp_parent_id, $from_date, $to_date)
  {
    $final1 = [];


    $acc_grp_par_tbl = $this->company_id.'_grpparentn_'.$this->session->get('ses_comp_fy_id');
    $parent =  $this->db->table($acc_grp_par_tbl)->where('acc_grp_parent_id', $acc_grp_parent_id)->get()->getRowArray();

    if($parent)
    {

      $account_grp_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
    $sundry_master_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');

    $result =  $this->db->table($account_grp_tbl)->where('acc_grp_primary', 'Y')
                             ->where('acc_grp_parent_id', $acc_grp_parent_id)
                             ->orderBy('acc_grp_name', 'asc')
                             ->get()->getResultArray();
      $balance_total = 0;
      if($result) 
      {
        foreach ($result as $key => $value) 
        {
          $group_id = $value['acc_grp_id'];
          $group_name = $value['acc_grp_name'];
          $balance = 0;
          $credit_total = 0;
          $debit_total = 0;

          //check accounts
          $builder = $this->db->table($account_master_tbl); 
        $builder->select(array('acc_id','acc_name','acc_grp_id'));
        $builder->where('acc_grp_id', $value['acc_grp_id']);
        $builder->orderBy('acc_name', 'asc');
        $accounts = $builder->get()->getResultArray();
        if($accounts){
          foreach ($accounts as $account) {

            $op_balance = 0;
            $fr_balance = 0;
            $to_balance = 0;

            $credit_detail_total = 0;
            $debit_detail_total = 0;

                 $get_opn_balance_info = $this->acc_opn_balance_info($account['acc_id']);
                 if($get_opn_balance_info)
                  $op_balance = $get_opn_balance_info['acc_op_bal'];

                 //check last transaction- from
            $acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$account['acc_id'].'_'.$this->session->get('ses_comp_fy_id');
            $builder = $this->db->table($acc_txn_tbl);
              $builder->where('acc_id', $account['acc_id']);
			  if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
              $builder->where('acc_txn_date <', $from_date);
              $builder->orderBy('acc_txn_date', 'desc');
              $builder->orderBy('voucher_txn_id', 'desc');
              $builder->orderBy('acc_txn_id', 'desc');
              $builder->limit(1);
              $transaction = $builder->get()->getRowArray();
              if($transaction){
                $fr_balance = $transaction['acc_bal'];
              }
              else{
                $fr_balance = $op_balance;
              }

            //check last transaction- to
            $acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$account['acc_id'].'_'.$this->session->get('ses_comp_fy_id');
            $builder = $this->db->table($acc_txn_tbl);
              $builder->where('acc_id', $account['acc_id']);
			  if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
              $builder->where('acc_txn_date <=', $to_date);
              $builder->orderBy('acc_txn_date', 'desc');
              $builder->orderBy('voucher_txn_id', 'desc');
              $builder->orderBy('acc_txn_id', 'desc');
              $builder->limit(1);
              $transaction = $builder->get()->getRowArray();
              if($transaction){
                $to_balance = $transaction['acc_bal'];
              }
              else{
                $to_balance = $op_balance;
              }

              $tr_balance = $to_balance - $fr_balance;

              if($tr_balance < 0){
              $credit_total += abs($tr_balance);
              $credit_detail_total = abs($tr_balance);
              }
            if($tr_balance >= 0){
              $debit_total += $tr_balance;
              $debit_detail_total = $tr_balance;
            }

            $balance = $debit_detail_total - $credit_detail_total;

          }
        }

        //check sundry accounts
        $builder = $this->db->table($sundry_master_tbl); 
        $builder->select(array('bill_sundry_id','bill_sundry_name','acc_grp_id'));
        $builder->where('acc_grp_id', $value['acc_grp_id']);
		if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
        $builder->orderBy('bill_sundry_name', 'asc');
        $sundry_accounts = $builder->get()->getResultArray();
        if($sundry_accounts){
          foreach ($sundry_accounts as $sundry_account) {

            $op_balance = 0;
            $fr_balance = 0;
            $to_balance = 0;

            $credit_detail_total = 0;
            $debit_detail_total = 0;

            $bill_sundry_op_balance = $this->bill_sundry_op_balance($sundry_account['bill_sundry_id']);
            if($bill_sundry_op_balance)
              $op_balance = $bill_sundry_op_balance['bsd_op_bal'];

            //check last transaction- from
            $bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$sundry_account['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
            $builder = $this->db->table($bs_txn_tbl);
            $builder->where('bill_sundry_id', $sundry_account['bill_sundry_id']);
              $builder->where('sundry_txn_date <', $from_date);
			  if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
            $builder->orderBy('sundry_txn_date', 'desc');
            $builder->orderBy('sundry_txn_id', 'desc');
            $builder->limit(1);
            $transaction = $builder->get()->getRowArray();
            if($transaction){
                $fr_balance = $transaction['sundry_bal'];
              }
              else{
                $fr_balance = $op_balance;
              }

            //check last transaction- to
            $bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$sundry_account['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
            $builder = $this->db->table($bs_txn_tbl);
            $builder->where('bill_sundry_id', $sundry_account['bill_sundry_id']);
              $builder->where('sundry_txn_date <=', $to_date);
			  if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
            $builder->orderBy('sundry_txn_date', 'desc');
            $builder->orderBy('sundry_txn_id', 'desc');
            $builder->limit(1);
            $transaction = $builder->get()->getRowArray();
            if($transaction){
                $to_balance = $transaction['sundry_bal'];
              }
              else{
                $to_balance = $op_balance;
              }

              $tr_balance = $to_balance - $fr_balance;

            if($tr_balance < 0){
              $credit_total += abs($tr_balance);
              $credit_detail_total = abs($tr_balance);
              }
            if($tr_balance >= 0){
              $debit_total += $tr_balance;
              $debit_detail_total = $tr_balance;
            }

            $balance = $debit_detail_total - $credit_detail_total;

          }
        }


        //check sub group
          $result2 = $this->db->table($account_grp_tbl)->where('under_main_grp_id',$value['acc_grp_id'])->orderBy('acc_grp_name', 'asc')->get()->getResultArray();
          if($result2)
          {
            foreach ($result2 as $key2 => $value2) {

              $credit_detail_total = 0;
            $debit_detail_total = 0;

              //check accounts
             $builder = $this->db->table($account_master_tbl); 
            $builder->select(array('acc_id','acc_name','acc_grp_id'));
            $builder->where('acc_grp_id', $value2['acc_grp_id']);
            $accounts = $builder->get()->getResultArray();
            if($accounts){
              foreach ($accounts as  $account) {

                $op_balance = 0;
                $fr_balance = 0;
                $to_balance = 0;

                     $get_opn_balance_info = $this->acc_opn_balance_info($account['acc_id']);
                     if($get_opn_balance_info)
                      $op_balance = $get_opn_balance_info['acc_op_bal'];

                     //check last transaction- from
                $acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$account['acc_id'].'_'.$this->session->get('ses_comp_fy_id');
                $builder = $this->db->table($acc_txn_tbl);
                  $builder->where('acc_id', $account['acc_id']);
                  $builder->where('acc_txn_date <', $from_date);
				  if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
                  $builder->orderBy('acc_txn_date', 'desc');
                  $builder->orderBy('voucher_txn_id', 'desc');
                  $builder->orderBy('acc_txn_id', 'desc');
                  $builder->limit(1);
                  $transaction = $builder->get()->getRowArray();
                  if($transaction){
                    $fr_balance = $transaction['acc_bal'];
                  }
                  else{
                    $fr_balance = $op_balance;
                  }

                //check last transaction- to
                $acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$account['acc_id'].'_'.$this->session->get('ses_comp_fy_id');
                $builder = $this->db->table($acc_txn_tbl);
                  $builder->where('acc_id', $account['acc_id']);
                  $builder->where('acc_txn_date <=', $to_date);
				  if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
                  $builder->orderBy('acc_txn_date', 'desc');
                  $builder->orderBy('voucher_txn_id', 'desc');
                  $builder->orderBy('acc_txn_id', 'desc');
                  $builder->limit(1);
                  $transaction = $builder->get()->getRowArray();
                  if($transaction){
                    $to_balance = $transaction['acc_bal'];
                  }
                  else{
                    $to_balance = $op_balance;
                  }

                  $tr_balance = $to_balance - $fr_balance;

                  if($tr_balance < 0){
                  $credit_total += abs($tr_balance);
                  $credit_detail_total += abs($tr_balance);
                  }
                if($tr_balance >= 0){
                  $debit_total += $tr_balance;
                  $debit_detail_total += $tr_balance;
                }
            
              }
            }

              //check sundry accounts
            $builder = $this->db->table($sundry_master_tbl); 
            $builder->select(array('bill_sundry_id','bill_sundry_name','acc_grp_id'));
            $builder->where('acc_grp_id', $value2['acc_grp_id']);
			if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
            $sundry_accounts = $builder->get()->getResultArray();
            if($sundry_accounts){
              foreach ($sundry_accounts as $sundry_account) {

                $op_balance = 0;
                $fr_balance = 0;
                $to_balance = 0;

                $bill_sundry_op_balance = $this->bill_sundry_op_balance($sundry_account['bill_sundry_id']);
                if($bill_sundry_op_balance)
                  $op_balance = $bill_sundry_op_balance['bsd_op_bal'];

                //check last transaction- from
                $bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$sundry_account['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
                $builder = $this->db->table($bs_txn_tbl);
                $builder->where('bill_sundry_id', $sundry_account['bill_sundry_id']);
                  $builder->where('sundry_txn_date <', $from_date);
				  if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
                $builder->orderBy('sundry_txn_date', 'desc');
                $builder->orderBy('sundry_txn_id', 'desc');
                $builder->limit(1);
                $transaction = $builder->get()->getRowArray();
                if($transaction){
                    $fr_balance = $transaction['sundry_bal'];
                  }
                  else{
                    $fr_balance = $op_balance;
                  }

                //check last transaction- to
                $bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$sundry_account['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
                $builder = $this->db->table($bs_txn_tbl);
                $builder->where('bill_sundry_id', $sundry_account['bill_sundry_id']);
                  $builder->where('sundry_txn_date <=', $to_date);
				  if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
                $builder->orderBy('sundry_txn_date', 'desc');
                $builder->orderBy('sundry_txn_id', 'desc');
                $builder->limit(1);
                $transaction = $builder->get()->getRowArray();
                if($transaction){
                    $to_balance = $transaction['sundry_bal'];
                  }
                  else{
                    $to_balance = $op_balance;
                  }

                  $tr_balance = $to_balance - $fr_balance;

                  if($tr_balance < 0){
                  $credit_total += abs($tr_balance);
                  $credit_detail_total += abs($tr_balance);
                  }
                if($tr_balance >= 0){
                  $debit_total += $tr_balance;
                  $debit_detail_total += $tr_balance;
                }
              }
            }

            $balance = $debit_detail_total - $credit_detail_total;

            }
          }

          $balance = $debit_total - $credit_total;
          $balance_total += $balance;

          $final1[] = [
            'group_id'    => $group_id,
            'group_name'  => $group_name,
            'balance'   => $balance,
            'type'      => 'grp',
            'style'     => '',
          ];
        }
      }


    $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
    $result3 =  $this->db->table($account_master_tbl)
                     ->where('acc_grp_parent_id', $acc_grp_parent_id)
                     ->orderBy('acc_name', 'asc')
                     ->get()->getResultArray();
      if($result3) 
      {
        foreach ($result3 as $key3 => $value3) 
        {
          $group_id = $value3['acc_id'];
          $group_name = $value3['acc_name'];
          $balance = 0;
          $credit_total = 0;
          $debit_total = 0;
                      
        $op_balance = 0;
        $fr_balance = 0;
        $to_balance = 0;

             $get_opn_balance_info = $this->acc_opn_balance_info($value3['acc_id']);
             if($get_opn_balance_info)
              $op_balance = $get_opn_balance_info['acc_op_bal'];

             //check last transaction- from
        $acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value3['acc_id'].'_'.$this->session->get('ses_comp_fy_id');
        $builder = $this->db->table($acc_txn_tbl);
          $builder->where('acc_id', $value3['acc_id']);
          $builder->where('acc_txn_date <', $from_date);
		  if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
          $builder->orderBy('acc_txn_date', 'desc');
          $builder->orderBy('voucher_txn_id', 'desc');
          $builder->orderBy('acc_txn_id', 'desc');
          $builder->limit(1);
          $transaction = $builder->get()->getRowArray();
          if($transaction){
            $fr_balance = $transaction['acc_bal'];
          }
          else{
            $fr_balance = $op_balance;
          }

        //check last transaction- to
        $acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value3['acc_id'].'_'.$this->session->get('ses_comp_fy_id');
        $builder = $this->db->table($acc_txn_tbl);
          $builder->where('acc_id', $value3['acc_id']);
		  if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
          $builder->where('acc_txn_date <=', $to_date);
          $builder->orderBy('acc_txn_date', 'desc');
          $builder->orderBy('voucher_txn_id', 'desc');
          $builder->orderBy('acc_txn_id', 'desc');
          $builder->limit(1);
          $transaction = $builder->get()->getRowArray();
          if($transaction){
            $to_balance = $transaction['acc_bal'];
          }
          else{
            $to_balance = $op_balance;
          }

          $tr_balance = $to_balance - $fr_balance;

          if($tr_balance < 0){
          $credit_total += abs($tr_balance);
          }
        if($tr_balance >= 0){
          $debit_total += $tr_balance;
        }


          $balance = $debit_total - $credit_total;
          $balance_total += $balance;

          $final1[] = [
            'group_id'    => $group_id,
            'group_name'  => $group_name,
            'balance'   => $balance,
            'type'      => 'acc',
            'style'     => '',
          ];

        }
      }

      $sundry_master_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
	  
	  if($this->session->get('ses_boid')!=''){
			
				$result4 =  $this->db->table($sundry_master_tbl)
                     ->where('acc_grp_parent_id', $acc_grp_parent_id)
					 ->where('bo_id', $this->session->get('ses_boid'))
                     ->orderBy('bill_sundry_name', 'asc')
                     ->get()->getResultArray();
	}else{
      $result4 =  $this->db->table($sundry_master_tbl)
                     ->where('acc_grp_parent_id', $acc_grp_parent_id)
                     ->orderBy('bill_sundry_name', 'asc')
                     ->get()->getResultArray();
	}
      if($result4) 
      {
        foreach ($result4 as $key4 => $value4) 
        {
          $group_id = $value4['bill_sundry_id'];
          $group_name = $value4['bill_sundry_name'];
          $balance = 0;
          $credit_total = 0;
          $debit_total = 0;
                      
        $op_balance = 0;
        $fr_balance = 0;
        $to_balance = 0;

             //check opening balance
        $bill_sundry_op_balance = $this->bill_sundry_op_balance($value4['bill_sundry_id']);
        if($bill_sundry_op_balance)
          $op_balance = $bill_sundry_op_balance['bsd_op_bal'];
        

          //check last transaction
        $bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$value4['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
        $builder = $this->db->table($bs_txn_tbl);
        $builder->where('bill_sundry_id', $value4['bill_sundry_id']);
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
        $builder->where('sundry_txn_date <', $from_date);
        $builder->orderBy('sundry_txn_date', 'desc');
        $builder->orderBy('sundry_txn_id', 'desc');
        $builder->limit(1);
        $transaction = $builder->get()->getRowArray();
          if($transaction){
            $fr_balance = $transaction['sundry_bal'];
          }
          else{
            $fr_balance = $op_balance;
          }

        //check last transaction- to
          $bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$value4['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
        $builder = $this->db->table($bs_txn_tbl);
        $builder->where('bill_sundry_id', $value4['bill_sundry_id']);
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
        $builder->where('sundry_txn_date <=', $to_date);
        $builder->orderBy('sundry_txn_date', 'desc');
        $builder->orderBy('sundry_txn_id', 'desc');
        $builder->limit(1);
        $transaction = $builder->get()->getRowArray();
          if($transaction){
            $to_balance = $transaction['sundry_bal'];
          }
          else{
            $to_balance = $op_balance;
          }

          $tr_balance = $to_balance - $fr_balance;

          if($tr_balance < 0){
          $credit_total += abs($tr_balance);
          }
        if($tr_balance >= 0){
          $debit_total += $tr_balance;
        }


          $balance = $debit_total - $credit_total;
          $balance_total += $balance;

          $final1[] = [
            'group_id'    => $group_id,
            'group_name'  => $group_name,
            'balance'   => $balance,
            'type'      => 'bsd',
            'style'     => '',
          ];

        }
      }
      
    }

    return $final1;
  }

  function get_receivable_facts()
  {
      $fy_months_list =  $this->CommonModel->get_fy_info($this->session->get('ses_comp_fy_id'),$this->company_id);
      $start_date     = strtotime($fy_months_list['fy_begndt']);
      $end_date       = strtotime($fy_months_list['fy_end']); 
      
      $months = [];
      
      $within_due = [];
      $overdue = [];
      $advances = [];

      $from = [];
      $to = [];

      while( $start_date <= $end_date ) {
        $from_date = date( 'Y-m-01', $start_date );
        $to_date = date( 'Y-m-t', $start_date );

        $within_due[] = round($this->get_trade_total(22, $to_date, '', false, $to_date));
        $overdue[] = round($this->get_trade_total(22, '', $to_date, false, $to_date));
        $advances[] = round($this->get_trade_total(16, '', '', true, $to_date));


        $months[] = date( 'M', $start_date );
        $from[] = $from_date;
        $to[] = $to_date;
        
        $start_date = strtotime( '+1 month', $start_date );
      }
  
      return ['months' => $months, 'overdue' => $overdue, 'within_due' => $within_due, 'advances' => $advances, 'from_date' => $from, 'to_date' => $to];
  }

  function get_payable_facts()
  {
      $fy_months_list =  $this->CommonModel->get_fy_info($this->session->get('ses_comp_fy_id'),$this->company_id);
      $start_date     = strtotime($fy_months_list['fy_begndt']);
      $end_date       = strtotime($fy_months_list['fy_end']); 
      
      $months = [];
      $within_due = [];
      $overdue = [];
      $advances = [];

      $from = [];
      $to = [];

      while( $start_date <= $end_date ) {
        $from_date = date( 'Y-m-01', $start_date );
        $to_date = date( 'Y-m-t', $start_date );

        $within_due[] = round($this->get_trade_total(16, $to_date, '', false, $to_date));
        $overdue[] = round($this->get_trade_total(16, '', $to_date, false, $to_date));
        $advances[] = round($this->get_trade_total(22, '', '', true, $to_date));

        $months[] = date( 'M', $start_date );
        $from[] = $from_date;
        $to[] = $to_date;
        
        $start_date = strtotime( '+1 month', $start_date );
      }
  
      return ['months' => $months, 'overdue' => $overdue, 'within_due' => $within_due, 'advances' => $advances, 'from_date' => $from, 'to_date' => $to];
  }

  function get_receivable_accounts($from_date,$to_date)
  {
    if($from_date != '' && $to_date != '')
      $accounts = $this->get_trade_accounts(22,$from_date,$to_date);
    else
      $accounts = $this->get_trade_accounts(22);

      return $accounts;
  }

  function get_payable_accounts($from_date,$to_date)
  {
      if($from_date != '' && $to_date != '')
      $accounts = $this->get_trade_accounts(16,$from_date,$to_date);
    else
      $accounts = $this->get_trade_accounts(16);

      return $accounts;
  }

  public function get_trade_accounts($group_id,$from_date = '',$to_date = '')
    {
      $all_groups = $this->get_sub_group_ids([$group_id]);

      $account_grp_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
      $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
      $sundry_master_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');

      $bill_master_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
      $bill_txn_tbl = $this->company_id.'_billstxnnn_'.$this->session->get('ses_comp_fy_id');
      
      $final = [];
      $balance_total = 0;

      //check accounts
      $builder = $this->db->table($account_master_tbl); 
      $builder->select(array('acc_id','acc_name','acc_grp_id'));
      $builder->whereIn('acc_grp_id', $all_groups);
      $accounts = $builder->get()->getResultArray(); 
      if($accounts){
        foreach ($accounts as $account) {
          $account_name = $account['acc_name'];
          $account_id = $account['acc_id'];

          $balance = '';
          $balance_total = 0;
          $overdue = '';
          $overdue_total = 0;

          //check all transaction
          $acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$account['acc_id'].'_'.$this->session->get('ses_comp_fy_id');

          //check last transaction
          $builder = $this->db->table($acc_txn_tbl);
          $builder->where('acc_id', $account['acc_id']);
          if($to_date != '')
            $builder->where('acc_txn_date <=', $to_date);
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
          $builder->orderBy('acc_txn_date', 'desc');
          $builder->orderBy('voucher_txn_id', 'desc');
          $builder->orderBy('acc_txn_id', 'desc');
          $builder->limit(1);
          $transaction = $builder->get()->getRowArray();
          if($transaction){
            $balance_total = parseAmount($transaction['acc_bal']);
          }
          else{   //check opening balance
            $get_opn_balance_info = $this->acc_opn_balance_info($account['acc_id']);
            if($get_opn_balance_info)
              $balance_total = parseAmount($get_opn_balance_info['acc_op_bal']);
          }

          $balance_total = parseAmount($balance_total);

          if($balance_total < 0){
            $balance = formatAmount(abs($balance_total)) . ' CR';
          }
          if($balance_total >= 0){
            $balance = formatAmount($balance_total) . ' DR';
          }

          $builder = $this->db->table($bill_master_tbl);
          $builder->select($bill_master_tbl.'.*');
          $builder->where('acc_id', $account_id);
		  if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));

          if($to_date != '')
            $builder->where('DATE(bill_due_date) <=', $to_date);
          $bill_master = $builder->get()->getResultArray();
          
          foreach($bill_master as $key => $value)
          {
              $builder = $this->db->table($bill_txn_tbl); 
              $builder->select('bills_txn_bal');
              $builder->where('bills_ref_id', $value['bills_ref_id']);
			  if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
              if($to_date != '')
                $builder->where('DATE(bills_txn_date) <=', $to_date);
              $builder->orderBy('bills_txn_date', 'desc');
              $builder->orderBy('bills_txn_id', 'desc');
              $builder->limit(1);
              $bill_txn = $builder->get()->getRowArray();
              if($bill_txn){
                  $bills_txn_bal = parseAmount($bill_txn['bills_txn_bal']);
                  if($bills_txn_bal > 0 && $group_id == 22){
                      $overdue_total += $bills_txn_bal;
                  }
                  if($bills_txn_bal < 0 && $group_id == 16){
                      $overdue_total += $bills_txn_bal;
                  }
              }
              else{
                  $bills_txn_bal = parseAmount($value['bill_op_bal']);
                  if($bills_txn_bal > 0 && $group_id == 22){
                      $overdue_total += $bills_txn_bal;
                  }
                  if($bills_txn_bal < 0 && $group_id == 16){
                      $overdue_total += $bills_txn_bal;
                  }
              }

              if($overdue_total < 0){
                $overdue = formatAmount(abs($overdue_total)) . ' CR';
              }
              if($overdue_total >= 0){
                $overdue = formatAmount($overdue_total) . ' DR';
              }
          }

          $final[] = [
            'account_name'   => $account_name,
            'account_id'     => $account_id,
            'balance'        => $balance,
            'overdue'        => $overdue,
          ];

        }
      }

      return $final;
    }

    function get_key_facts()
    {
      $kf_customers = 0;
      $kf_suppliers = 0;
      $kf_total_items = 0;
      $kf_total_item_groups = 0;

      $item_master_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
      $data =  $this->db->table($item_master_tbl)
                    ->select('count(item_id) as total')
                    ->get()->getRowArray();
      $kf_total_items = $data['total'] != '' ? $data['total'] : 0;

      $item_grp_master_tbl = $this->company_id.'_itemgrpmst_'.$this->session->get('ses_comp_fy_id');
      $data =  $this->db->table($item_grp_master_tbl)
                    ->select('count(item_grp_id) as total')
                    ->get()->getRowArray();
      $kf_total_item_groups = $data['total'] != '' ? $data['total'] : 0;


      $all_groups = $this->get_sub_group_ids([22]);
      $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
      $data =  $this->db->table($account_master_tbl)
                    ->select('count(acc_id) as total')
                    ->whereIn('acc_grp_id', $all_groups)
                    ->get()->getRowArray();
      $kf_customers = $data['total'] != '' ? $data['total'] : 0;

      $all_groups = $this->get_sub_group_ids([16]);
      $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
      $data =  $this->db->table($account_master_tbl)
                    ->select('count(acc_id) as total')
                    ->whereIn('acc_grp_id', $all_groups)
                    ->get()->getRowArray();
      $kf_suppliers = $data['total'] != '' ? $data['total'] : 0;



      return [
        'kf_customers'  =>  $kf_customers,
        'kf_suppliers'  =>  $kf_suppliers,
        'kf_total_items'  =>  $kf_total_items,
        'kf_total_item_groups'  =>  $kf_total_item_groups,
        
      ];
    }

}