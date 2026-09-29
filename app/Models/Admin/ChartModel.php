<?php
namespace App\Models\Admin;

use CodeIgniter\Model;
use App\Models\CommonModel;
use App\Models\Admin\ReportingModel;
use App\Models\Admin\AccountsModel;

class ChartModel extends Model	{

  public function __construct() {
    parent::__construct();        
   
    $this->CommonModel    = new CommonModel();
    $this->ReportingModel = new ReportingModel();
	$this->AccountsModel = new AccountsModel();
    $this->session       =  \Config\Services::session();   
    $this->comp_fy_id    =  $this->session->get('ses_comp_fy_id');
    $this->user_id       =  $this->session->get('uuid_aicountly');
    $this->company_id    =  $this->session->get('ses_company_id');
	$this->fy_id         =  $this->session->get('ses_comp_fy_id');
	$this->bo_id         =  $this->session->get('ses_boid');
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
 
  function getCashAccountDetails($from_date, $to_date)
  {
      $credit_total = 0;
      $debit_total = 0;

      $cash_groups = $this->get_sub_group_ids([23,21]);

      $account_master_tbl = $this->company_id.'_acctmaster_'.$this->comp_fy_id;
      $builder = $this->db->table($account_master_tbl); 
      $builder->whereIn('acc_grp_id', $cash_groups);
      $result = $builder->get()->getResultArray();

      foreach ($result as $key => $value) {
        
        $acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value['acc_id'].'_'.$this->session->get('ses_comp_fy_id');

        $builder = $this->db->table($acc_txn_tbl);
        $builder->select('SUM(CASE WHEN `acc_txn_drcr`="c" THEN `acc_txn_amount` ELSE 0 END) AS credit, SUM(CASE WHEN `acc_txn_drcr`="d" THEN `acc_txn_amount` ELSE 0 END) AS debit');
        $builder->where('acc_id', $value['acc_id']);
        $builder->where('acc_txn_date >=', $from_date);
        $builder->where('acc_txn_date <=', $to_date);
        $builder->where('bo_id', $this->bo_id);
        $acc_txns = $builder->get()->getRowArray();
        if($acc_txns){
            $debit_total += floatval($acc_txns['debit']);
            $credit_total += floatval($acc_txns['credit']); 
        }
      }

      return [
        'credit_total' => $credit_total,
        'debit_total' => $debit_total,
      ];
  }

  function get_cash_flow_details()
  {
	$fy_calender = fy_calender();
    $fy_months   = $fy_calender->months;
    $monthRanges = [];
	$blue        = [];
    $pink        = [];
	foreach ($fy_months as $fy_month) {
		$monthRanges[] = [
			'month'      => $fy_month['month_short'],
			'from_date'  => $fy_month['from_date'],
			'to_date'    => $fy_month['to_date']
		];
	}
	
	$monthCase = "CASE";
	foreach ($monthRanges as $i => $range) {
		$from      = $range['from_date'];
		$to        = $range['to_date'];
		$month     = $range['month'];
		$monthCase .= " WHEN accttxnmst.acc_txn_date BETWEEN '$from' AND '$to' THEN '$month'";
	}
	
	$monthCase .= " END AS month_label";
    $all_parent_ids = [23, 21];	
    $under_groups = [];
	foreach ($all_parent_ids as $pid) {
	 $under_groups[$pid] = array_merge([$pid], $this->ReportingModel->getAllSubGroupIds($pid));
	}  
	$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");		
	
	$builder = $this->db->table('accttxnmst');
	$builder->select("
		acctmaster.acc_id,
		acctmaster.acc_name,
		undercrsmt.under_crs_mst_id,
		undercrsmt.crs_mst_parent_id,
		$monthCase,
		undercrsmt.crs_mst_parent_id AS parent_group_id,
		SUM(CASE WHEN accttxnmst.acc_txn_dr_cr = 1 THEN accttxnmst.acc_txn_amt ELSE 0 END) AS total_dr,
		SUM(CASE WHEN accttxnmst.acc_txn_dr_cr = 2 THEN accttxnmst.acc_txn_amt ELSE 0 END) AS total_cr
	");
	$builder->join('acctmaster', 'acctmaster.acc_id = accttxnmst.acc_id', 'left');
	$builder->join("undercrsmt", "undercrsmt.crs_mst_id = acctmaster.acc_id AND undercrsmt.crs_mst_type = 1 AND undercrsmt.cmp_id = $this->company_id", 'left');
	$builder->where('accttxnmst.cmp_id', $this->company_id);
	$builder->where('accttxnmst.hobo_id', $this->bo_id);
	$builder->where('accttxnmst.acc_txn_type', 1);
	// All date range union
	$date_min = min(array_column($monthRanges, 'from_date'));
	$date_max = max(array_column($monthRanges, 'to_date'));
	$builder->where('accttxnmst.acc_txn_date >=', $date_min);
	$builder->where('accttxnmst.acc_txn_date <=', $date_max);
	// Group match
	$builder->groupStart();
	foreach ($under_groups as $group_ids) {
		$builder->orGroupStart();
			$builder->whereIn('undercrsmt.under_crs_mst_id', $group_ids);
			$builder->orWhereIn('undercrsmt.crs_mst_parent_id', $group_ids);
		$builder->groupEnd();
	}
	$builder->groupEnd();
	$builder->groupBy(['month_label', 'accttxnmst.acc_id']);
	$results = $builder->get()->getResultArray();
	
	$monthly = [];
	
	foreach ($results as $row) {
		$cr_balance=0;
	    $dr_balance=0;
		$month = $row['month_label'];
		$parent = $row['parent_group_id'];
		$name   = strtolower($row['acc_name']);
		$cr_balance += $row['total_cr'];
		$dr_balance += $row['total_dr'];
		if (!isset($monthly[$month])) {
			$monthly[$month] =  ['cr_amount' => $cr_balance,'dr_amount' => $dr_balance];
		}
	}
	$blue = [];
	$pink = [];
	foreach ($fy_months as $fy_month) {
		$month = $fy_month['month_short'];
		$cr_amount = $monthly[$month]['cr_amount'] ??  0;
		$dr_amount = $monthly[$month]['dr_amount'] ?? 0;
		$blue[] = [
			'month' => $month,
			'total_amount' => round($cr_amount)			
		];
		$pink[] = [
			'month' => $month,
			'total_amount' => round($dr_amount)
		];
	}
    return ['blue' => $blue, 'pink' => $pink];	
  }

  function get_cash_equivalent_details()
  {
    $fy_calender = fy_calender();
    $fy_months   = $fy_calender->months;
    $monthRanges = [];
	$blue        = [];
    $pink        = [];
	foreach ($fy_months as $fy_month) {
		$monthRanges[] = [
			'month'      => $fy_month['month_short'],
			'from_date'  => $fy_month['from_date'],
			'to_date'    => $fy_month['to_date']
		];
	}
	
	$monthCase = "CASE";
	foreach ($monthRanges as $i => $range) {
		$from      = $range['from_date'];
		$to        = $range['to_date'];
		$month     = $range['month'];
		$monthCase .= " WHEN accttxnmst.acc_txn_date BETWEEN '$from' AND '$to' THEN '$month'";
	}
	
	$monthCase .= " END AS month_label";
    $all_parent_ids = [23, 21];
	$groupToName = [23 => 'asset', 21 => 'borrowing'];  
    $under_groups = [];
	foreach ($all_parent_ids as $pid) {
	 $under_groups[$pid] = array_merge([$pid], $this->ReportingModel->getAllSubGroupIds($pid));
	}  
	$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");		
	
	$builder = $this->db->table('accttxnmst');
	$builder->select("
		acctmaster.acc_id,
		acctmaster.acc_name,
		undercrsmt.under_crs_mst_id,
		undercrsmt.crs_mst_parent_id,
		$monthCase,
		undercrsmt.crs_mst_parent_id AS parent_group_id,
		SUM(CASE WHEN accttxnmst.acc_txn_dr_cr = 1 THEN accttxnmst.acc_txn_amt ELSE 0 END) AS total_dr,
		SUM(CASE WHEN accttxnmst.acc_txn_dr_cr = 2 THEN accttxnmst.acc_txn_amt ELSE 0 END) AS total_cr
	");
	$builder->join('acctmaster', 'acctmaster.acc_id = accttxnmst.acc_id', 'left');
	$builder->join("undercrsmt", "undercrsmt.crs_mst_id = acctmaster.acc_id AND undercrsmt.crs_mst_type = 1 AND undercrsmt.cmp_id = $this->company_id", 'left');
	$builder->where('accttxnmst.cmp_id', $this->company_id);
	$builder->where('accttxnmst.hobo_id', $this->bo_id);
	$builder->where('accttxnmst.acc_txn_type', 1);
	// All date range union
	$date_min = min(array_column($monthRanges, 'from_date'));
	$date_max = max(array_column($monthRanges, 'to_date'));
	$builder->where('accttxnmst.acc_txn_date >=', $date_min);
	$builder->where('accttxnmst.acc_txn_date <=', $date_max);
	// Group match
	$builder->groupStart();
	foreach ($under_groups as $group_ids) {
		$builder->orGroupStart();
			$builder->whereIn('undercrsmt.under_crs_mst_id', $group_ids);
			$builder->orWhereIn('undercrsmt.crs_mst_parent_id', $group_ids);
		$builder->groupEnd();
	}
	$builder->groupEnd();
	$builder->groupBy(['month_label', 'accttxnmst.acc_id']);
	$results = $builder->get()->getResultArray();
	
	$monthly = [];
	foreach ($results as $row) {
		$month = $row['month_label'];
		$parent = $row['parent_group_id'];
		$name   = strtolower($row['acc_name']);
		$clo_balance = $row['total_dr'] - $row['total_cr'];
		if (!isset($monthly[$month])) {
			$monthly[$month] = [
				'asset' => ['amount' => 0, 'cash' => 0, 'bank' => 0],
				'borrowing' => ['amount' => 0],
			];
		}

		if ($parent == 23) {
			$monthly[$month]['asset']['amount'] += $clo_balance;
			if (str_contains($name, 'cash') && !str_contains($name, 'bank')) {
				$monthly[$month]['asset']['cash'] += $clo_balance;
			} else {
				$monthly[$month]['asset']['bank'] += $clo_balance;
			}
		} elseif ($parent == 21) {
			$monthly[$month]['borrowing']['amount'] += $clo_balance;
		}
	}
	$blue = [];
	$pink = [];

	foreach ($fy_months as $fy_month) {
		$month = $fy_month['month_short'];
		$asset = $monthly[$month]['asset'] ?? ['amount' => 0, 'cash' => 0, 'bank' => 0];
		$borrowing = $monthly[$month]['borrowing'] ?? ['amount' => 0];

		$blue[] = [
			'month' => $month,
			'total_amount' => round($asset['amount']),
			'cash' => round($asset['cash']),
			'bank' => round($asset['bank']),
		];
		$pink[] = [
			'month' => $month,
			'total_amount' => round($borrowing['amount']),
		];
	}

    return ['blue' => $blue, 'pink' => $pink];

  }

  function get_quick_assets()
  {
      $fy_calender = fy_calender();
      $from_date   = $fy_calender->from_date;
      $to_date     = $fy_calender->to_date;
      $asset       = $this->getAccountBalance(23,$from_date,$to_date);
      $borrowing   = $this->getAccountBalance(21,$from_date,$to_date);
      $data = [
        'qa_limit' => parseAmount($borrowing['amount']),
        'qa_cash' => parseAmount($asset['cash']),
        'qa_bank' => parseAmount($asset['bank']),
      ];
      return $data;
  }

  function getAccountBalance($acc_grp_parent_id,$from_date,$to_date)
  {
	$group_ids      = $this->ReportingModel->getAllSubGroupIds($acc_grp_parent_id);		
	$all_group_ids  = array_merge([$acc_grp_parent_id],$group_ids);	      
    $amount         = 0;
    $cash           = 0;
    $bank           = 0;
	$clo_acc_op_bal = 0;
	$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");		
			
	$builder = $this->db->table('accttxnmst');
	$builder->select("
		acctmaster.acc_id,
		acctmaster.acc_name,
		undercrsmt.under_crs_mst_id,
		undercrsmt.crs_mst_parent_id,
		SUM(CASE WHEN accttxnmst.acc_txn_dr_cr = 1 THEN accttxnmst.acc_txn_amt ELSE 0 END) AS total_dr,
		SUM(CASE WHEN accttxnmst.acc_txn_dr_cr = 2 THEN accttxnmst.acc_txn_amt ELSE 0 END) AS total_cr
	");
	$builder->join('acctmaster', 'acctmaster.acc_id = accttxnmst.acc_id', 'left');
	$builder->join("undercrsmt undercrsmt", "undercrsmt.crs_mst_id = acctmaster.acc_id AND undercrsmt.crs_mst_type =1 AND undercrsmt.cmp_id =$this->company_id", 'left');
	$builder->where('accttxnmst.cmp_id', $this->company_id);
	$builder->groupStart();
	  $builder->whereIn('undercrsmt.under_crs_mst_id', $all_group_ids);
	  $builder->orWhereIn('undercrsmt.crs_mst_parent_id', $all_group_ids);
	$builder->groupEnd();
	$builder->where('accttxnmst.hobo_id', $this->bo_id);
	$builder->where('accttxnmst.acc_txn_type', 1);
	$builder->where('accttxnmst.acc_txn_date >=', $from_date);
	$builder->where('accttxnmst.acc_txn_date <=', $to_date);
	$builder->groupBy('accttxnmst.acc_id');
	$results = $builder->get()->getResultArray();
	// Now classify as cash / bank / general total
	
	$cash = $bank = $amount = 0;
	foreach ($results as $row){
		$clo_balance = $row['total_dr'] - $row['total_cr'];
		$amount += $clo_balance;
		if ($acc_grp_parent_id == 23) {
			$acc_name = strtolower($row['acc_name']);
			if (str_contains($acc_name, 'cash') && !str_contains($acc_name, 'bank')) {
				$cash += parseAmount($clo_balance);
			} else {
				$bank += parseAmount($clo_balance);
			}
		 }
	  }
	return [
		'cash' => parseAmount($cash),
		'bank' => parseAmount($bank),
		'amount' => parseAmount($amount),
	];	
  }

  public function getMonthlyGroupAmounts(array $parent_ids, string $monthCase, string $date_min, string $date_max)
	{
		$under_groups = [];
		foreach ($parent_ids as $pid) {
			$under_groups = array_merge($under_groups, [$pid], $this->ReportingModel->getAllSubGroupIds($pid));
		}

		$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");

		$builder = $this->db->table('accttxnmst');
		$builder->select("
			acctmaster.acc_id,
			$monthCase,
			SUM(CASE WHEN accttxnmst.acc_txn_dr_cr = 1 THEN accttxnmst.acc_txn_amt ELSE 0 END) AS total_dr,
			SUM(CASE WHEN accttxnmst.acc_txn_dr_cr = 2 THEN accttxnmst.acc_txn_amt ELSE 0 END) AS total_cr
		");
		$builder->join('acctmaster', 'acctmaster.acc_id = accttxnmst.acc_id', 'left');
		$builder->join("undercrsmt", "undercrsmt.crs_mst_id = acctmaster.acc_id AND undercrsmt.crs_mst_type = 1 AND undercrsmt.cmp_id = $this->company_id", 'left');
		$builder->where('accttxnmst.cmp_id', $this->company_id);
		$builder->where('accttxnmst.hobo_id', $this->bo_id);
		$builder->where('accttxnmst.acc_txn_type', 1);
		$builder->where('accttxnmst.acc_txn_date >=', $date_min);
		$builder->where('accttxnmst.acc_txn_date <=', $date_max);

		$builder->groupStart();
		$builder->whereIn('undercrsmt.under_crs_mst_id', $under_groups);
		$builder->orWhereIn('undercrsmt.crs_mst_parent_id', $under_groups);
		$builder->groupEnd();

		$builder->groupBy(['month_label']);
		$results = $builder->get()->getResultArray();

		$monthly = [];
		foreach ($results as $row) {
			$month = $row['month_label'];
			$balance = ($row['total_dr'] ?? 0) - ($row['total_cr'] ?? 0);
			$monthly[$month] = round($balance);
		}

		return $monthly;
	}
  function get_revenue_details()
  {
		$fy_calender = fy_calender();
		$fy_months = $fy_calender->months;

		$monthRanges = [];
		$month_labels = [];
		foreach ($fy_months as $m) {
			$monthRanges[] = ['month' => $m['month_short'], 'from_date' => $m['from_date'], 'to_date' => $m['to_date']];
			$month_labels[] = $m['month_short'];
		}

		// Create CASE statement for month labels
		$monthCase = "CASE";
		foreach ($monthRanges as $range) {
			$monthCase .= " WHEN accttxnmst.acc_txn_date BETWEEN '{$range['from_date']}' AND '{$range['to_date']}' THEN '{$range['month']}'";
		}
		$monthCase .= " END AS month_label";

		// Set overall date range
		$date_min = min(array_column($monthRanges, 'from_date'));
		$date_max = max(array_column($monthRanges, 'to_date'));

		// Get balances for each set
		$price_monthly_balances   = $this->getMonthlyGroupAmounts([8, 10, 12], $monthCase, $date_min, $date_max);
		$expense_monthly_balances = $this->getMonthlyGroupAmounts([11, 13, 9, 6], $monthCase, $date_min, $date_max);

		// Build return arrays
		$prices_ar = [];
		$expenses_ar = [];
		$months = [];
		foreach ($month_labels as $month) {
			$months[] = $month;
			$prices_ar[] =  $price_monthly_balances[$month] ?? 0;			
			$expenses_ar[] = $expense_monthly_balances[$month] ?? 0;			
		}
	  return ['months' => $months, 'prices' => $prices_ar, 'pricesExpense' => $expenses_ar];
  }

  function get_net_worth_details()
  {
	  
	  $pl_detail_result = $this->get_profit_details();
	  ////////////
	$fy_calender = fy_calender();
    $fy_months   = $fy_calender->months;

    $monthRanges = [];
    $month_labels = [];

    foreach ($fy_months as $fy_month) {
        $monthRanges[] = [
            'month'     => $fy_month['month_short'],
            'from_date' => $fy_month['from_date'],
            'to_date'   => $fy_month['to_date']
        ];
        $month_labels[] = $fy_month['month_short'];
    }

    // CASE WHEN for month labels
    $monthCase = "CASE";
    foreach ($monthRanges as $range) {
        $from = $range['from_date'];
        $to = $range['to_date'];
        $month = $range['month'];
        $monthCase .= " WHEN accttxnmst.acc_txn_date BETWEEN '$from' AND '$to' THEN '$month'";
    }
    $monthCase .= " END AS month_label";

    $all_parent_ids = [1];
    $under_groups = [];

    foreach ($all_parent_ids as $pid) {
        $under_groups[$pid] = array_merge([$pid], $this->ReportingModel->getAllSubGroupIds($pid));
    }

    $this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");

    $builder = $this->db->table('accttxnmst');
    $builder->select("
        acctmaster.acc_id,
        acctmaster.acc_name,
        undercrsmt.under_crs_mst_id,
        undercrsmt.crs_mst_parent_id,
        $monthCase,
        undercrsmt.crs_mst_parent_id AS parent_group_id,
        SUM(CASE WHEN accttxnmst.acc_txn_dr_cr = 1 THEN accttxnmst.acc_txn_amt ELSE 0 END) AS total_dr,
        SUM(CASE WHEN accttxnmst.acc_txn_dr_cr = 2 THEN accttxnmst.acc_txn_amt ELSE 0 END) AS total_cr
    ");
    $builder->join('acctmaster', 'acctmaster.acc_id = accttxnmst.acc_id', 'left');
    $builder->join("undercrsmt", "undercrsmt.crs_mst_id = acctmaster.acc_id AND undercrsmt.crs_mst_type = 1 AND undercrsmt.cmp_id = $this->company_id", 'left');
    $builder->where('accttxnmst.cmp_id', $this->company_id);
    $builder->where('accttxnmst.hobo_id', $this->bo_id);
    $builder->where('accttxnmst.acc_txn_type', 1);

    $date_min = min(array_column($monthRanges, 'from_date'));
    $date_max = max(array_column($monthRanges, 'to_date'));
    $builder->where('accttxnmst.acc_txn_date >=', $date_min);
    $builder->where('accttxnmst.acc_txn_date <=', $date_max);

    $builder->groupStart();
    foreach ($under_groups as $group_ids) {
        $builder->orGroupStart();
        $builder->whereIn('undercrsmt.under_crs_mst_id', $group_ids);
        $builder->orWhereIn('undercrsmt.crs_mst_parent_id', $group_ids);
        $builder->groupEnd();
    }
    $builder->groupEnd();

    $builder->groupBy(['month_label', 'accttxnmst.acc_id']);

    $results = $builder->get()->getResultArray();

    // Initialize result by month
    $month_indexed = array_fill_keys($month_labels, ['gross_profit' => 0, 'net_profit' => 0,'balance' => 0]);

		if ($results) {
		foreach ($results as $row) {
				$month = $row['month_label'];
				$balance = (($row['total_dr'] ?? 0) - ($row['total_cr'] ?? 0));
				$month_indexed[$month]['balance'] += round($balance);
			}
		}

		// Final combined balance output (pl + parent)
		$months = [];
		$final_prices = [];

		foreach ($month_labels as $i => $month) {
			$months[] = $month;
			$pl_balance = $pl_detail_result['balance'][$i] ?? 0;
			$parent_balance = $month_indexed[$month]['balance'] ?? 0;

			$final_prices[] = $pl_balance + $parent_balance;
		}

		return [
			'months' => $months,
			'prices' => $final_prices
		];
	  
  }

  function get_profit_details()
  {
	  
	$fy_calender = fy_calender();
    $fy_months   = $fy_calender->months;

    $monthRanges = [];
    $month_labels = [];

    foreach ($fy_months as $fy_month) {
        $monthRanges[] = [
            'month'     => $fy_month['month_short'],
            'from_date' => $fy_month['from_date'],
            'to_date'   => $fy_month['to_date']
        ];
        $month_labels[] = $fy_month['month_short'];
    }

    // CASE WHEN for month labels
    $monthCase = "CASE";
    foreach ($monthRanges as $range) {
        $from = $range['from_date'];
        $to = $range['to_date'];
        $month = $range['month'];
        $monthCase .= " WHEN accttxnmst.acc_txn_date BETWEEN '$from' AND '$to' THEN '$month'";
    }
    $monthCase .= " END AS month_label";

    $all_parent_ids = [7, 11, 8, 10, 13, 12];
    $under_groups = [];

    foreach ($all_parent_ids as $pid) {
        $under_groups[$pid] = array_merge([$pid], $this->ReportingModel->getAllSubGroupIds($pid));
    }

    $this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");

    $builder = $this->db->table('accttxnmst');
    $builder->select("
        acctmaster.acc_id,
        acctmaster.acc_name,
        undercrsmt.under_crs_mst_id,
        undercrsmt.crs_mst_parent_id,
        $monthCase,
        undercrsmt.crs_mst_parent_id AS parent_group_id,
        SUM(CASE WHEN accttxnmst.acc_txn_dr_cr = 1 THEN accttxnmst.acc_txn_amt ELSE 0 END) AS total_dr,
        SUM(CASE WHEN accttxnmst.acc_txn_dr_cr = 2 THEN accttxnmst.acc_txn_amt ELSE 0 END) AS total_cr
    ");
    $builder->join('acctmaster', 'acctmaster.acc_id = accttxnmst.acc_id', 'left');
    $builder->join("undercrsmt", "undercrsmt.crs_mst_id = acctmaster.acc_id AND undercrsmt.crs_mst_type = 1 AND undercrsmt.cmp_id = $this->company_id", 'left');
    $builder->where('accttxnmst.cmp_id', $this->company_id);
    $builder->where('accttxnmst.hobo_id', $this->bo_id);
    $builder->where('accttxnmst.acc_txn_type', 1);

    $date_min = min(array_column($monthRanges, 'from_date'));
    $date_max = max(array_column($monthRanges, 'to_date'));
    $builder->where('accttxnmst.acc_txn_date >=', $date_min);
    $builder->where('accttxnmst.acc_txn_date <=', $date_max);

    $builder->groupStart();
    foreach ($under_groups as $group_ids) {
        $builder->orGroupStart();
        $builder->whereIn('undercrsmt.under_crs_mst_id', $group_ids);
        $builder->orWhereIn('undercrsmt.crs_mst_parent_id', $group_ids);
        $builder->groupEnd();
    }
    $builder->groupEnd();

    $builder->groupBy(['month_label', 'accttxnmst.acc_id']);

    $results = $builder->get()->getResultArray();

    // Initialize result by month
    $month_indexed = array_fill_keys($month_labels, ['gross_profit' => 0, 'net_profit' => 0,'balance' => 0]);

    if ($results) {
        foreach ($results as $row) {
            $month = $row['month_label'];
            $parent_id = $row['crs_mst_parent_id'];
            $balance = (($row['total_dr'] ?? 0) - ($row['total_cr'] ?? 0));

            $l_step1_total = $r_step1_total = $l_step2_total = $r_step2_total = 0;
            $gross_profit = 0;

            if (in_array($parent_id, [7, 11])) {
                $l_step1_total += $balance;
            }
            if (in_array($parent_id, [8, 10])) {
                $r_step1_total += -$balance;
            }

            if ($l_step1_total > $r_step1_total) {
                $diff = $l_step1_total - $r_step1_total;
                $l_step2_total += $diff;
                $gross_profit = -$diff;
            } elseif ($l_step1_total < $r_step1_total) {
                $diff = $r_step1_total - $l_step1_total;
                $r_step2_total += $diff;
                $gross_profit = $diff;
            }

            if ($parent_id == 13) {
                $l_step2_total += $balance;
            }
            if ($parent_id == 12) {
                $r_step2_total += -$balance;
            }

            $balanceprft = parseAmount($l_step2_total) - parseAmount($r_step2_total);
            $net_profit = $l_step2_total < $r_step2_total ? -$balanceprft : $balanceprft;

            $month_indexed[$month]['gross_profit'] += round($gross_profit);
            $month_indexed[$month]['net_profit'] += round($net_profit);
			$month_indexed[$month]['balance'] += round($balanceprft);
        }
    }

    // Build final output
    $months            = [];
    $gross_profit_list = [];
    $net_profit_list   = [];
	$balance_list      = [];

    foreach ($month_indexed as $month => $values) {
        $months[]            = $month;
        $gross_profit_list[] = $values['gross_profit'];
        $net_profit_list[]   = $values['net_profit'];
		$balance_list[]      = $values['balance'];
     }

      return [
        'months' => $months,
        'gross_profit' => $gross_profit_list,
        'net_profit'   => $net_profit_list,
		'balance'      => $balance_list,
    ];
  }

  function get_pie_chart_details()
  {
    $fy_calender = fy_calender();
    $from_date   = $fy_calender->from_date;
    $to_date     = $fy_calender->to_date; // 1 or 2


   // $opening_stock = $this->ReportsModel->get_opn_stock_val();
    // $purchase = $this->get_parent_amount_pnl([7],$from_date,$to_date);
    //$closing_stock = $this->ReportsModel->load_stock_status_items(0,0, 0, $to_date,0,'')['total_valuation'];
    //$closing_stock = $this->ReportsModel->bl_pl_trl_stock_valuation($from_date,$to_date)['total_valuation'];

    // $cogs = round($opening_stock) + round($purchase) - round($closing_stock);

    $result1 = $this->ReportingModel->get_parent_group_details_pl(11,1, $from_date, $to_date,0);
    $result2 = $this->ReportingModel->get_parent_group_details_pl(13,1, $from_date, $to_date,0);
    $result  = array_merge($result1,$result2);
    $final   = [];
    $others  = [];
    
    if(count($result) > 0){
      $max = max(array_column($result, 'balance'));
      $index = array_search($max, array_column($result, 'balance'));
	  $grpname = html_entity_decode($result[$index]['group_name']);
	  $cl_grpname = trim(str_replace('»', '', $grpname));
	  $cl_grpname = preg_replace('/\s+/', ' ', trim($cl_grpname));
      $final[] = [
          'group' => $cl_grpname,
          'amount' => round($result[$index]['balance']),
      ];
      array_splice($result, $index, 1);
    }
    if(count($result) > 0){
      $max = max(array_column($result, 'balance'));
      $index = array_search($max, array_column($result, 'balance'));
	  $grpname = html_entity_decode($result[$index]['group_name']);
	  $cl_grpname = trim(str_replace('»', '', $grpname));
	  $cl_grpname = preg_replace('/\s+/', ' ', trim($cl_grpname));
      $final[] = [
          'group' => $cl_grpname,
          'amount' => round($result[$index]['balance']),
      ];
      array_splice($result, $index, 1);
    }
    if(count($result) > 0){
      $max = max(array_column($result, 'balance'));
      $index = array_search($max, array_column($result, 'balance'));
	  $grpname = html_entity_decode($result[$index]['group_name']);
	  $cl_grpname = trim(str_replace('»', '', $grpname));
	  $cl_grpname = preg_replace('/\s+/', ' ', trim($cl_grpname));
      $final[] = [
          'group' => $cl_grpname,
          'amount' => ($result[$index]['balance']) ? round($result[$index]['balance']):0,
      ];
      array_splice($result, $index, 1);
    }
    if(count($result) > 0){
      $max = max(array_column($result, 'balance'));
      $index = array_search($max, array_column($result, 'balance'));
	  $grpname = html_entity_decode($result[$index]['group_name']);
	  $cl_grpname = trim(str_replace('»', '', $grpname));
	  $cl_grpname = preg_replace('/\s+/', ' ', trim($cl_grpname));
      $final[] = [
          'group' => $cl_grpname,
          'amount' => ($result[$index]['balance']) ? round($result[$index]['balance']) : 0,
      ];
      array_splice($result, $index, 1);
    }
    if(count($result) > 0){
      $max = max(array_column($result, 'balance'));
      $index = array_search($max, array_column($result, 'balance'));
	  $grpname = html_entity_decode($result[$index]['group_name']);
	  $cl_grpname = trim(str_replace('»', '', $grpname));
	  $cl_grpname = preg_replace('/\s+/', ' ', trim($cl_grpname));
      $final[] = [
          'group' => $cl_grpname,
          'amount' => ($result[$index]['balance']) ? round($result[$index]['balance']): 0,
      ];
      array_splice($result, $index, 1);
    }
    
    if(count($result) > 0){
      $balance = 0;
      foreach ($result as $key => $value) {
         $balance += !empty($value['balance'])? $value['balance'] : 0;
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
      $array1        = [];
      $array2        = [];
      $l_step1_total = 0;
      $r_step1_total = 0;
      $l_step1_total += 0;//$this->ReportsModel->get_opn_stock_val();
	  $l_step1_total += array_reduce(
				$this->ReportingModel->get_parent_group_details_pl(7,1,$from_date,$to_date,0),
					fn($carry, $row) => $carry + (!empty($row['balance']) ? (float)$row['balance'] : 0),
					0
				  );// PURCHASES
				  
	  $l_step1_total += array_reduce(
				$this->ReportingModel->get_parent_group_details_pl(11,1,$from_date,$to_date,0),
					fn($carry, $row) => $carry + (!empty($row['balance']) ? (float)$row['balance'] : 0),
					0
				  );// DIRECTO EXPENSES
				  
	  $r_step1_total += -array_reduce(
				$this->ReportingModel->get_parent_group_details_pl(8,1,$from_date,$to_date,0),
					fn($carry, $row) => $carry + (!empty($row['balance']) ? (float)$row['balance'] : 0),
					0
				  );// SALES
				  
     $r_step1_total += -array_reduce(
				$this->ReportingModel->get_parent_group_details_pl(10,1,$from_date,$to_date,0),
					fn($carry, $row) => $carry + (!empty($row['balance']) ? (float)$row['balance'] : 0),
					0
				  );// DIRECT INCOMES
				  
     // $l_step1_total += $this->get_parent_group_details_pl(7,$from_date,$to_date); // PURCHASES

      //$l_step1_total += $this->get_parent_group_details_pl(11,$from_date,$to_date); // DIRECTO EXPENSES

     // $r_step1_total += -$this->get_parent_group_details_pl(8,$from_date,$to_date); // SALES

      //$r_step1_total += -$this->get_parent_group_details_pl(10,$from_date,$to_date); // DIRECT INCOMES


     $r_step1_total += 0;//$this->ReportsModel->bl_pl_trl_stock_valuation($from_date,$to_date)['total_valuation']; // CLOSING STOCK

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
	
	$l_step2_total += array_reduce(
				$this->ReportingModel->get_parent_group_details_pl(13,1,$from_date,$to_date,0),
					fn($carry, $row) => $carry + (!empty($row['balance']) ? (float)$row['balance'] : 0),
					0
				  );// INDIRECT EXPENSES
	$r_step2_total += -array_reduce(
				$this->ReportingModel->get_parent_group_details_pl(12,1,$from_date,$to_date,0),
					fn($carry, $row) => $carry + (!empty($row['balance']) ? (float)$row['balance'] : 0),
					0
				  );// INDIRECT INCOMES			  
				  
    //$l_step2_total += $this->get_parent_group_details_pl(13,$from_date,$to_date); // INDIRECT EXPENSES

    //$r_step2_total += -$this->get_parent_group_details_pl(12,$from_date,$to_date); // INDIRECT INCOMES

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
		
				        $builder->where('bo_id', $this->bo_id);
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

			       $builder->where('bo_id', $this->bo_id);
            $builder->orderBy('bill_sundry_name', 'asc');
            $sundry_accounts = $builder->get()->getResultArray();
            if($sundry_accounts){
              foreach ($sundry_accounts as $sundry_account) {

                $bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$sundry_account['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
                $builder = $this->db->table($bs_txn_tbl);
                $builder->select('SUM(CASE WHEN `sundry_txn_drcr`="c" THEN (`sundry_txn_amount`*-1) ELSE `sundry_txn_amount` END) AS amount');
                $builder->where('bill_sundry_id', $sundry_account['bill_sundry_id']);
                $builder->where('sundry_txn_date >=', $from_date);

				        $builder->where('bo_id', $this->bo_id);
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
					         $builder->where('bo_id', $this->bo_id);
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
				        $builder->where('bo_id', $this->bo_id);
                $builder->orderBy('bill_sundry_name', 'asc');
                $sundry_accounts = $builder->get()->getResultArray();
                if($sundry_accounts){
                  foreach ($sundry_accounts as $sundry_account) {

                    $bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$sundry_account['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
                    $builder = $this->db->table($bs_txn_tbl);
                    $builder->select('SUM(CASE WHEN `sundry_txn_drcr`="c" THEN (`sundry_txn_amount`*-1) ELSE `sundry_txn_amount` END) AS amount');
                    $builder->where('bill_sundry_id', $sundry_account['bill_sundry_id']);
                    $builder->where('sundry_txn_date >=', $from_date);
					           $builder->where('bo_id', $this->bo_id);
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
				    $builder->where('bo_id', $this->bo_id);
            $builder->where('acc_txn_date <=', $to_date);
            $transaction = $builder->get()->getRowArray();
            if($transaction){
              $amount += parseAmount($transaction['amount']);
            }
          }
        }

        $sundry_master_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');

		
		 $result4 =  $this->db->table($sundry_master_tbl)
                       ->where('acc_grp_parent_id', $parent['acc_grp_parent_id'])
					   ->where('bo_id', $this->bo_id)
                       ->orderBy('bill_sundry_name', 'asc')
                       ->get()->getResultArray();	
		
        if($result4) 
        {
          foreach ($result4 as $key4 => $value4) 
          {
              $bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$value4['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
              $builder = $this->db->table($bs_txn_tbl);
              $builder->select('SUM(CASE WHEN `sundry_txn_drcr`="c" THEN (`sundry_txn_amount`*-1) ELSE `sundry_txn_amount` END) AS amount');
              $builder->where('bill_sundry_id', $value4['bill_sundry_id']);
              $builder->where('sundry_txn_date >=', $from_date);
				      $builder->where('bo_id', $this->bo_id);
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

  function get_parent_expense_pnl($acc_grp_parent_id_array, $from_date, $to_date)
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

                              //  echo $this->db->getLastQuery();

                              // die();
        
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
		
				        $builder->where('bo_id', $this->bo_id);
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

			
            $builder->orderBy('bill_sundry_name', 'asc');
            $sundry_accounts = $builder->get()->getResultArray();
            if($sundry_accounts){
              foreach ($sundry_accounts as $sundry_account) {

                $bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$sundry_account['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
                $builder = $this->db->table($bs_txn_tbl);
                $builder->select('SUM(CASE WHEN `sundry_txn_drcr`="c" THEN (`sundry_txn_amount`*-1) ELSE `sundry_txn_amount` END) AS amount');
                $builder->where('bill_sundry_id', $sundry_account['bill_sundry_id']);
                $builder->where('sundry_txn_date >=', $from_date);

				        $builder->where('bo_id', $this->bo_id);
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
					         $builder->where('bo_id', $this->bo_id);
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
				        $builder->where('bo_id', $this->bo_id);
                $builder->orderBy('bill_sundry_name', 'asc');
                $sundry_accounts = $builder->get()->getResultArray();
                if($sundry_accounts){
                  foreach ($sundry_accounts as $sundry_account) {

                    $bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$sundry_account['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
                    $builder = $this->db->table($bs_txn_tbl);
                    $builder->select('SUM(CASE WHEN `sundry_txn_drcr`="c" THEN (`sundry_txn_amount`*-1) ELSE `sundry_txn_amount` END) AS amount');
                    $builder->where('bill_sundry_id', $sundry_account['bill_sundry_id']);
                    $builder->where('sundry_txn_date >=', $from_date);
					           $builder->where('bo_id', $this->bo_id);
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
				    $builder->where('bo_id', $this->bo_id);
            $builder->where('acc_txn_date <=', $to_date);
            $transaction = $builder->get()->getRowArray();
            if($transaction){
              $amount += parseAmount($transaction['amount']);
            }
          }
        }

        $sundry_master_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');

		
		 $result4 =  $this->db->table($sundry_master_tbl)
                       ->where('acc_grp_parent_id', $parent['acc_grp_parent_id'])
					   ->where('bo_id', $this->bo_id)
                       ->orderBy('bill_sundry_name', 'asc')
                       ->get()->getResultArray();	
		
        if($result4) 
        {
          foreach ($result4 as $key4 => $value4) 
          {
              $bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$value4['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
              $builder = $this->db->table($bs_txn_tbl);
              $builder->select('SUM(CASE WHEN `sundry_txn_drcr`="c" THEN (`sundry_txn_amount`*-1) ELSE `sundry_txn_amount` END) AS amount');
              $builder->where('bill_sundry_id', $value4['bill_sundry_id']);
              $builder->where('sundry_txn_date >=', $from_date);
				      $builder->where('bo_id', $this->bo_id);
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
				        $builder->where('bo_id', $this->bo_id);
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
			       $builder->where('bo_id', $this->bo_id);
            $builder->orderBy('bill_sundry_name', 'asc');
            $sundry_accounts = $builder->get()->getResultArray();
            if($sundry_accounts){
              foreach ($sundry_accounts as $sundry_account) {

                $bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$sundry_account['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
                $builder = $this->db->table($bs_txn_tbl);
                $builder->select('sundry_bal');
                $builder->where('bill_sundry_id', $sundry_account['bill_sundry_id']);
				        $builder->where('bo_id', $this->bo_id);
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
					          $builder->where('bo_id', $this->bo_id);
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
				        $builder->where('bo_id', $this->bo_id);
                $builder->orderBy('bill_sundry_name', 'asc');
                $sundry_accounts = $builder->get()->getResultArray();
                if($sundry_accounts){
                  foreach ($sundry_accounts as $sundry_account) {

                    $bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$sundry_account['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
                    $builder = $this->db->table($bs_txn_tbl);
                    $builder->select('sundry_bal');
                    $builder->where('bill_sundry_id', $sundry_account['bill_sundry_id']);
                    $builder->where('sundry_txn_date <=', $to_date);
					          $builder->where('bo_id', $this->bo_id);
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
					  $builder->where('bo_id', $this->bo_id);
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
		

				
				$result4 =  $this->db->table($sundry_master_tbl)
                       ->where('acc_grp_parent_id', $parent['acc_grp_parent_id'])
					   ->where('bo_id', $this->bo_id)
                       ->orderBy('bill_sundry_name', 'asc')
                       ->get()->getResultArray();
	
        if($result4) 
        {
          foreach ($result4 as $key4 => $value4) 
          {
              $bs_txn_tbl = $this->company_id.'_sundrytxnn_'.$value4['bill_sundry_id'].'_'.$this->session->get('ses_comp_fy_id');
              $builder = $this->db->table($bs_txn_tbl);
              $builder->select('sundry_bal');
              $builder->where('bill_sundry_id', $value4['bill_sundry_id']);
              $builder->where('sundry_txn_date <=', $to_date);
				      $builder->where('bo_id', $this->bo_id);
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
		  return $this->db->table($accoppybal_tbl)->where('bo_id', $this->bo_id)->where('acc_id', $acc_id)->get()->getRowArray();  
  }

  function bill_sundry_op_balance($id)
  {
    $bsdoppybal_tbl = $this->company_id.'_bsdoppybal_'.$this->session->get('ses_comp_fy_id');
		 return $this->db->table($bsdoppybal_tbl)->where('bo_id', $this->bo_id)->where('bill_sundry_id', $id)->get()->getRowArray(); 
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
		if($this->session->get('ses_boid'))
		$builder->where('bo_id', $this->session->get('ses_boid'));
        if($from_date != '')
          $builder->where('DATE(bill_due_date) >', $from_date);
        if($to_date != '')
          $builder->where('DATE(bill_due_date) <=', $to_date);
        $result = $builder->get()->getResultArray();
        
        foreach($result as $key => $value)
        {
            $builders = $this->db->table($bill_txn_tbl); 
            $builders->select('bills_txn_bal');
            $builders->where('bills_ref_id', $value['bills_ref_id']);
			$builders->where('bo_id', $this->bo_id);
            if($final_date != '')
              $builders->where('DATE(bills_txn_date) <=', $final_date);
            $builders->orderBy('bills_txn_date', 'desc');
            $builders->orderBy('bills_txn_id', 'desc');
            $builders->limit(1);
            $bill_txn = $builders->get()->getRowArray();

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
			  if($this->session->get('ses_boid'))
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
			  if($this->session->get('ses_boid'))
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
		$builder->where('bo_id', $this->bo_id);
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
				    $builder->where('bo_id', $this->bo_id);
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
				    $builder->where('bo_id', $this->bo_id);
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
				          $builder->where('bo_id', $this->bo_id);
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
				          $builder->where('bo_id', $this->bo_id);
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
				    $builder->where('bo_id', $this->bo_id);
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
				        $builder->where('bo_id', $this->bo_id);
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
				        $builder->where('bo_id', $this->bo_id);
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
				  $builder->where('bo_id', $this->bo_id);
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
				  $builder->where('bo_id', $this->bo_id);
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
			
			$result4 =  $this->db->table($sundry_master_tbl)
                     ->where('acc_grp_parent_id', $acc_grp_parent_id)
					           ->where('bo_id', $this->bo_id)
                     ->orderBy('bill_sundry_name', 'asc')
                     ->get()->getResultArray();

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
		    $builder->where('bo_id', $this->bo_id);
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
		    $builder->where('bo_id', $this->bo_id);
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
      $fy_calender = fy_calender();
      $fy_months = $fy_calender->months;
      $total_year = $fy_calender->total_year; // 1 or 2 
      
      $months = [];
      
      $within_due = [];
      $overdue = [];
      $advances = [];

      $from = [];
      $to = [];

      foreach ($fy_months as $fy_month)
      {
        $from_date = $fy_month['from_date'];
        $to_date = $fy_month['to_date'];

        $month = $fy_month['month_short'];

        $within_due[] = round($this->get_trade_total(22, $to_date, '', false, $to_date));
        $overdue[] = round($this->get_trade_total(22, '', $to_date, false, $to_date));
        $advances[] = round($this->get_trade_total(16, '', '', true, $to_date));


        $months[] = $month;
        $from[] = $from_date;
        $to[] = $to_date;
        
      }
  
      return ['months' => $months, 'overdue' => $overdue, 'within_due' => $within_due, 'advances' => $advances, 'from_date' => $from, 'to_date' => $to];
  }

  function get_payable_facts()
  {
      $fy_calender = fy_calender();
      $fy_months = $fy_calender->months;
      $total_year = $fy_calender->total_year; // 1 or 2  
      
      $months = [];
      $within_due = [];
      $overdue = [];
      $advances = [];

      $from = [];
      $to = [];

      foreach ($fy_months as $fy_month)
      {
        $from_date = $fy_month['from_date'];
        $to_date = $fy_month['to_date'];

        $month = $fy_month['month_short'];

        $within_due[] = round($this->get_trade_total(16, $to_date, '', false, $to_date));
        $overdue[] = round($this->get_trade_total(16, '', $to_date, false, $to_date));
        $advances[] = round($this->get_trade_total(22, '', '', true, $to_date));

        $months[] = $month;
        $from[] = $from_date;
        $to[] = $to_date;
        
        
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
		
		      $builder->where('bo_id', $this->bo_id);
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
			     $builder->where('bo_id', $this->bo_id);

          if($to_date != '')
            $builder->where('DATE(bill_due_date) <=', $to_date);
          $bill_master = $builder->get()->getResultArray();
          
          foreach($bill_master as $key => $value)
          {
              $builders = $this->db->table($bill_txn_tbl); 
              $builders->select('bills_txn_bal');
              $builders->where('bills_ref_id', $value['bills_ref_id']);
			  $builders->where('bo_id', $this->bo_id);
              if($to_date != '')
                $builders->where('DATE(bills_txn_date) <=', $to_date);
              $builders->orderBy('bills_txn_date', 'desc');
              $builders->orderBy('bills_txn_id', 'desc');
              $builders->limit(1);
              $bill_txn = $builders->get()->getRowArray();
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
            'balance_total'  => $balance_total,
            'overdue_total'  => $overdue_total,
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