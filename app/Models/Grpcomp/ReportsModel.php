<?php
namespace App\Models\Grpcomp;
use CodeIgniter\Model;
use App\Models\CommonModel;
use App\Models\Grpcomp\TransactionModel;
use App\Libraries\externaldb;
use App\Libraries\enc_string;
class ReportsModel extends Model	{
    public function __construct() {
       parent::__construct();        
       $this->externaldb    	= new externaldb();	
	   $this->db            	= $this->externaldb->get_company_db();
	   $this->erp_db   			=  $this->externaldb->erp_db();
	   $this->aicountly_db   	=  $this->externaldb->aicountly_db();
	   $this->session       	= \Config\Services::session();
	   $this->company_id    	= $this->session->get('ses_company_id');	   
	   $this->TransactionModel  = new TransactionModel();
       $this->CommonModel   	= new CommonModel();	   
	   $this->enc_string    	= new enc_string();
	   $this->bo_id         	=1;
    } 

    // Cash & Cash Equivalents		23
    // Bank OD/OCC A/c		21
	// Trade Receivables			22 (sundry debitors)
	// Trade Payable				16 (sundry creditors)

	// PURCHASE 			7 (parent_id)
	// DIRECT EXPENSE		11 (parent_id)
	// INDIRECT EXPENSE		13 (parent_id)

  public function load_balance_sheet1($view,$from_date,$to_date,$nil_type)
	{   $ses_grp_id           = $this->session->get('ses_grp_id');  
	    $group_companies_list = $this->group_companies_list(); 
		// OWNER'S FUND	 1							NON CURRENT ASSETS 3	
		// NON CURRENT LIABILITIES 	2				CURRENT ASSETS 5
		// CURRENT LIABILITIES	4					DIFF. IN OP. BALANCE


		$array1 = [];
    	$array2 = [];	

		$data = $this->get_parent_group_details(1, $view,$from_date,$to_date,$this->erp_db,$group_companies_list); // OWNER'S FUND
    
		if($data){
    		foreach ($data as $key => $value) {
    			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
    				$array1[] = [
			    		'l_group_id'	   => $value['group_id'],
			    		'l_group_name'    => $value['group_name'],
			    		'l_balance'		   => $value['balance'] != '' ? formatAmount(-$value['balance']) : '',
			    		'l_balance_total' => $value['balance'] != '' ? -$value['balance'] : '',
			    		'l_type'		      => $value['type'],
			    		'l_style'		   => $value['style'],
			    	];
    			}
    		}
    	}

    	$data = $this->get_pl_details($from_date,$to_date,$this->erp_db,$group_companies_list); // PROFIT & LOSS
    	if($data){

    		$array1[] = [
	    		'l_group_id'	  	=> $data['group_id'],
	    		'l_group_name'    => $data['group_name'],
	    		'l_balance'		   => $data['balance'] != '' ? formatAmount(-$data['balance']) : '',
		    	'l_balance_total' => $data['balance'] != '' ? -$data['balance'] : '',
	    		'l_type'		  		=> $data['type'],
	    		'l_style'		   => $data['style'],
	    	];
    	}

    	$data = $this->get_parent_group_details(2, $view,$from_date,$to_date,$this->erp_db,$group_companies_list); // NON CURRENT LIABILITIES
    	if($data){
    		foreach ($data as $key => $value) {
    			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
    				$array1[] = [
			    		'l_group_id'	   => $value['group_id'],
			    		'l_group_name'    => $value['group_name'],
			    		'l_balance'		   => $value['balance'] != '' ? formatAmount(-$value['balance']) : '',
			    		'l_balance_total' => $value['balance'] != '' ? -$value['balance'] : '',
			    		'l_type'		      => $value['type'],
			    		'l_style'		   => $value['style'],
			    	];
    			}
    		}
    	}
    	$data = $this->get_parent_group_details(4, $view,$from_date,$to_date,$this->erp_db,$group_companies_list); // CURRENT LIABILITIES
    	if($data){
    		foreach ($data as $key => $value) {
    			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
    				$array1[] = [
			    		'l_group_id'	   => $value['group_id'],
			    		'l_group_name'    => $value['group_name'],
			    		'l_balance'		   => $value['balance'] != '' ? formatAmount(-$value['balance']) : '',
			    		'l_balance_total' => $value['balance'] != '' ? -$value['balance'] : '',
			    		'l_type'		      => $value['type'],
			    		'l_style'		   => $value['style'],
			    	];
    			}
    		}
    	}

    	
 
    	//-----------------------------------------------------

    	$data = $this->get_parent_group_details(3, $view,$from_date,$to_date,$this->erp_db,$group_companies_list); // NON CURRENT ASSETS
    	if($data){
    		foreach ($data as $key => $value) {
		    	if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
    				$array2[] = [
			    		'r_group_id'	  	=> $value['group_id'],
			    		'r_group_name'    => $value['group_name'],
			    		'r_balance'		  	=> $value['balance'] != '' ? formatAmount($value['balance']) : '',
			    		'r_balance_total'	=> $value['balance'] != '' ? $value['balance'] : '',
			    		'r_type'		  		=> $value['type'],
			    		'r_style'		   => $value['style'],
			    	];
    			}
    		}
    	}
    	$data = $this->get_parent_group_details(5, $view,$from_date,$to_date,$this->erp_db,$group_companies_list); // CURRENT ASSETS 
    	if($data){
    		foreach ($data as $key => $value) {
    			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
    				$array2[] = [
			    		'r_group_id'	  	=> $value['group_id'],
			    		'r_group_name'    => $value['group_name'],
			    		'r_balance'		  	=> $value['balance'] != '' ? formatAmount($value['balance']) : '',
			    		'r_balance_total'	=> $value['balance'] != '' ? $value['balance'] : '',
			    		'r_type'		  		=> $value['type'],
			    		'r_style'		   => $value['style'],
			    	];
    			}
    		}
    	}
    	$data = $this->load_stock_status_items(0,0,$to_date);
    	if($view == 1 || $view == 2){
    		$array2[] = [
	    		'r_group_id'	  	=> 0,
	    		'r_group_name'    => '&nbsp;&nbsp; &raquo; Inventories',
	    		'r_balance'		  	=> $data != '' ? formatAmount($data) : '',
	    		'r_balance_total'	=> $data != '' ? $data : '',
	    		'r_type'		  		=> 'clo',
	    		'r_style'		   => $view == 2 ? 'font-weight: 500;' : '',
	    	];
    	}
    	
    	if($view == 0){
			
    		$r_balance_total = $array2[count($array2)-1]['r_balance_total'];
    		$r_balance_total += $data;

    		$array2[count($array2)-1]['r_balance'] = $r_balance_total > 0 ? formatAmount($r_balance_total) : '';
    		$array2[count($array2)-1]['r_balance_total'] = $r_balance_total;
    	}

    	//,6,7,8,9,10,11,12,13
    	$data = $this->get_op_diff_balance_details($this->erp_db,$group_companies_list,[1,2,3,4,5,6,7,8,9,10,11,12,13]); // DIFF. IN OP. BALANCE
    
		
		if($data){
    		$array2[] = [
	    		'r_group_id'	  => $data['group_id'],
	    		'r_group_name'    => $data['group_name'],
		    	'r_balance'		  => $data['balance'] != '' ? formatAmount($data['balance']) : '',
		    	'r_balance_total'	=> $data['balance'] != '' ? $data['balance'] : '',
	    		'r_type'		  => $data['type'],
	    		'r_style'		  => $data['style'],
	    	];
    	}

    	$l_total = array_sum(array_column($array1, 'l_balance_total'));
    	$r_total = array_sum(array_column($array2, 'r_balance_total'));

    	$final = [];

    	$count = count($array1) > count($array2) ? count($array1) : count($array2);
    	for($i=0; $i<$count; $i++)
    	{
    		$temp1 = !empty($array1[$i]) ? $array1[$i] : [];
    		$temp2 = !empty($array2[$i]) ? $array2[$i] : [];

    		if(count($temp1) || count($temp2)){
    			$temp3 = array_merge($temp1,$temp2);
    			$l_style = isset($temp3['l_style']) ? ['style' => $temp3['l_style']] : [];
    			$r_style = isset($temp3['r_style']) ? ['style' => $temp3['r_style']] : [];
    			
    			$temp3['pq_cellattr'] = ['l_group_name' => $l_style, 'r_group_name' => $r_style];
    			$final[] = $temp3;
    		}

    		
    	}

    	$final[] = [
	    		'l_group_id'	  => 0,
	    		'l_group_name'    => '',
	    		'l_balance'		  => formatAmount($l_total),
	    		'r_group_id'	  => 0,
	    		'r_group_name'    => '',
	    		'r_balance'		  => formatAmount($r_total),
	    		'pq_rowattr'	  => ['style' => 'background:#E6E6FA;font-weight:bold;']
    	];
    	// echo "<pre>";print_r($final);exit;
    	return $final;

	}
  
   public function load_balance_sheet($view,$from_date,$to_date,$nil_type)
	{   $ses_grp_id           = $this->session->get('ses_grp_id');  
	    $group_companies_list = $this->group_companies_list(); 
		// OWNER'S FUND	 1							NON CURRENT ASSETS 3	
		// NON CURRENT LIABILITIES 	2				CURRENT ASSETS 5
		// CURRENT LIABILITIES	4					DIFF. IN OP. BALANCE


		$array1 = [];
    	$array2 = [];	

		$data = $this->get_parent_group_details(1, $view,$from_date,$to_date,$this->erp_db,$group_companies_list); // OWNER'S FUND
    
		if($data){
    		foreach ($data as $key => $value) {
    			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
    				$array1[] = [
			    		'l_group_id'	   => $value['group_id'],
			    		'l_group_name'    => $value['group_name'],
			    		'l_balance'		   => $value['balance'] != '' ? formatAmount(-$value['balance']) : '',
			    		'l_balance_total' => $value['balance'] != '' ? -$value['balance'] : '',
			    		'l_type'		      => $value['type'],
			    		'l_style'		   => $value['style'],
			    	];
    			}
    		}
    	}

    	$data = $this->get_pl_details($from_date,$to_date,$this->erp_db,$group_companies_list); // PROFIT & LOSS
    	if($data){

    		$array1[] = [
	    		'l_group_id'	  	=> $data['group_id'],
	    		'l_group_name'    => $data['group_name'],
	    		'l_balance'		   => $data['balance'] != '' ? formatAmount(-$data['balance']) : '',
		    	'l_balance_total' => $data['balance'] != '' ? -$data['balance'] : '',
	    		'l_type'		  		=> $data['type'],
	    		'l_style'		   => $data['style'],
	    	];
    	}

    	$data = $this->get_parent_group_details(2, $view,$from_date,$to_date,$this->erp_db,$group_companies_list); // NON CURRENT LIABILITIES
    	if($data){
    		foreach ($data as $key => $value) {
    			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
    				$array1[] = [
			    		'l_group_id'	   => $value['group_id'],
			    		'l_group_name'    => $value['group_name'],
			    		'l_balance'		   => $value['balance'] != '' ? formatAmount(-$value['balance']) : '',
			    		'l_balance_total' => $value['balance'] != '' ? -$value['balance'] : '',
			    		'l_type'		      => $value['type'],
			    		'l_style'		   => $value['style'],
			    	];
    			}
    		}
    	}
    	$data = $this->get_parent_group_details(4, $view,$from_date,$to_date,$this->erp_db,$group_companies_list); // CURRENT LIABILITIES
    	if($data){
    		foreach ($data as $key => $value) {
    			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
    				$array1[] = [
			    		'l_group_id'	   => $value['group_id'],
			    		'l_group_name'    => $value['group_name'],
			    		'l_balance'		   => $value['balance'] != '' ? formatAmount(-$value['balance']) : '',
			    		'l_balance_total' => $value['balance'] != '' ? -$value['balance'] : '',
			    		'l_type'		      => $value['type'],
			    		'l_style'		   => $value['style'],
			    	];
    			}
    		}
    	}

    	
 
    	//-----------------------------------------------------

    	$data = $this->get_parent_group_details(3, $view,$from_date,$to_date,$this->erp_db,$group_companies_list); // NON CURRENT ASSETS
    	if($data){
    		foreach ($data as $key => $value) {
		    	if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
    				$array2[] = [
			    		'r_group_id'	  	=> $value['group_id'],
			    		'r_group_name'    => $value['group_name'],
			    		'r_balance'		  	=> $value['balance'] != '' ? formatAmount($value['balance']) : '',
			    		'r_balance_total'	=> $value['balance'] != '' ? $value['balance'] : '',
			    		'r_type'		  		=> $value['type'],
			    		'r_style'		   => $value['style'],
			    	];
    			}
    		}
    	}
    	$data = $this->get_parent_group_details(5, $view,$from_date,$to_date,$this->erp_db,$group_companies_list); // CURRENT ASSETS 
    	if($data){
    		foreach ($data as $key => $value) {
    			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
    				$array2[] = [
			    		'r_group_id'	  	=> $value['group_id'],
			    		'r_group_name'    => $value['group_name'],
			    		'r_balance'		  	=> $value['balance'] != '' ? formatAmount($value['balance']) : '',
			    		'r_balance_total'	=> $value['balance'] != '' ? $value['balance'] : '',
			    		'r_type'		  		=> $value['type'],
			    		'r_style'		   => $value['style'],
			    	];
    			}
    		}
    	}
    	$data = $this->load_stock_status_items(0,0,$to_date);
    	if($view == 1 || $view == 2){
    		$array2[] = [
	    		'r_group_id'	  	=> 0,
	    		'r_group_name'    => '&nbsp;&nbsp; &raquo; Inventories',
	    		'r_balance'		  	=> $data != '' ? formatAmount($data) : '',
	    		'r_balance_total'	=> $data != '' ? $data : '',
	    		'r_type'		  		=> 'clo',
	    		'r_style'		   => $view == 2 ? 'font-weight: 500;' : '',
	    	];
    	}
    	
    	if($view == 0){
			
    		$r_balance_total = $array2[count($array2)-1]['r_balance_total'];
    		$r_balance_total += $data;

    		$array2[count($array2)-1]['r_balance'] = $r_balance_total > 0 ? formatAmount($r_balance_total) : '';
    		$array2[count($array2)-1]['r_balance_total'] = $r_balance_total;
    	}

    	//,6,7,8,9,10,11,12,13
    	$data = $this->get_op_diff_balance_details($this->erp_db,$group_companies_list,[1,2,3,4,5,6,7,8,9,10,11,12,13]); // DIFF. IN OP. BALANCE
    
		
		if($data){
    		$array2[] = [
	    		'r_group_id'	  => $data['group_id'],
	    		'r_group_name'    => $data['group_name'],
		    	'r_balance'		  => $data['balance'] != '' ? formatAmount($data['balance']) : '',
		    	'r_balance_total'	=> $data['balance'] != '' ? $data['balance'] : '',
	    		'r_type'		  => $data['type'],
	    		'r_style'		  => $data['style'],
	    	];
    	}

    	$l_total = array_sum(array_column($array1, 'l_balance_total'));
    	$r_total = array_sum(array_column($array2, 'r_balance_total'));

    	$final = [];

    	$count = count($array1) > count($array2) ? count($array1) : count($array2);
    	for($i=0; $i<$count; $i++)
    	{
    		$temp1 = !empty($array1[$i]) ? $array1[$i] : [];
    		$temp2 = !empty($array2[$i]) ? $array2[$i] : [];

    		if(count($temp1) || count($temp2)){
    			$temp3 = array_merge($temp1,$temp2);
    			$l_style = isset($temp3['l_style']) ? ['style' => $temp3['l_style']] : [];
    			$r_style = isset($temp3['r_style']) ? ['style' => $temp3['r_style']] : [];
    			
    			$temp3['pq_cellattr'] = ['l_group_name' => $l_style, 'r_group_name' => $r_style];
    			$final[] = $temp3;
    		}

    		
    	}

    	$final[] = [
	    		'l_group_id'	  => 0,
	    		'l_group_name'    => '',
	    		'l_balance'		  => formatAmount($l_total),
	    		'r_group_id'	  => 0,
	    		'r_group_name'    => '',
	    		'r_balance'		  => formatAmount($r_total),
	    		'pq_rowattr'	  => ['style' => 'background:#E6E6FA;font-weight:bold;']
    	];
    	// echo "<pre>";print_r($final);exit;
    	return $final;

	}
    
	public function acc_opn_balance_info($acc_id,$db,$company_id,$comp_fy_id){
       $accoppybal_tbl =$company_id.'_accoppybal_'.$comp_fy_id;	   
       $res= $db->table($accoppybal_tbl)->where('bo_id', $this->bo_id)->where('acc_id', $acc_id)->get()->getRowArray(); 
	/* echo $db->GetLastQuery();
				echo'<br>
				
			 \n';
			 echo '------'; */

	   return $res;
      } 
	  public function sundry_opn_balance_info($acc_id,$db,$company_id,$comp_fy_id){
         $bsdoppybal_tbl = $company_id.'_bsdoppybal_'.$comp_fy_id;
		 $result =$db->table($bsdoppybal_tbl)->where('bo_id', $this->bo_id)->where('bill_sundry_id', $acc_id)->get()->getRowArray();
		 
			 
	    return $result;   
      }  
	  
	public function get_parent_group_details($acc_grp_parent_id, $view, $from_date, $to_date,$erp_db,$group_companies_list)
    { 
    	$ses_grp_id = $this->session->get('ses_grp_id');  
    	
    	$final = [];
    	$final1 = [];
    	$final2 = [];
        $balance_total=0;
    	$acc_grp_par_tbl = 'aictlyerp_grpparentn_univdb';
    	$parent =  $erp_db->table($acc_grp_par_tbl)->where('acc_grp_parent_id', $acc_grp_parent_id)->get()->getRowArray();

    	if($parent)
    	{
    		$final1[] = [
	  			'group_id'	 	=> $parent['acc_grp_parent_id'],
	  			'group_name' 	=> $parent['grp_name'],
	  			'balance'	 	=> '',
	  			'type'			=> 'prt',
	  			'style'			=> 'font-weight:bold;'
	  		];

	  		$final2[] = [
	  			'group_id'	 	=> $parent['acc_grp_parent_id'],
	  			'group_name' 	=> $parent['grp_name'],
	  			'balance'	 	=> '',
	  			'type'			=> 'prt',
	  			'style'			=> 'font-weight:bold;'
	  		];

    		$debit_total=$credit_total=0;
			foreach($group_companies_list as $grprow){
				 $company_id         =  $grprow['company_id'];
				 $comp_fy_id         =  $grprow['comp_fy_id'];
				 $comp_code          =  $grprow['comp_code'];
				 $comp_name          =  ucwords(strtolower($grprow['comp_name']));
				 $comp_short_name    =  ucwords(strtolower($grprow['comp_short_name']));
                 $company_name       =  $comp_name;
				 $extdb              =  $this->externaldb->single_company_db($comp_code);
				 
				$builder = $this->erp_db->table('aictlyerp_grpmapping_univdb grpmp');
				$builder->join('aictlyerp_grpcomstid_univdb grpcom','grpcom.crs_master_id=grpmp.crs_master_id');
				$builder->select(array('grpmp.master_id','grpmp.crs_master_id','grpcom.crs_master_name'));
				$builder->where('grpmp.grpco_id', $ses_grp_id);
				$builder->where('grpmp.master_type','acc');	
				$builder->where('grpmp.crs_master_id >','0');
				$builder->where('grpmp.comp_id',$company_id);	
				$builder->where('grpmp.acc_grp_parent_id',$acc_grp_parent_id);
				$builder->orderBy('grpcom.crs_master_name');
				$accounts = $builder->get()->getResultArray();
				//echo $this->erp_db->GetLastQuery();
				//echo'
				
				//<br>';
				if($accounts){
					foreach ($accounts as $account) {
						$crs_master_id = $account['crs_master_id'];
						$group_id      = $account['master_id'];
						$group_name    = $account['crs_master_name'].'('.$company_name.')';
						
						    $op_balance = 0;
							$fr_balance = 0;
							$to_balance = 0;

							$credit_detail_total = 0;
							$debit_detail_total = 0;
							
						
						 $get_opn_balance_info = $this->acc_opn_balance_info($account['master_id'],$extdb,$company_id,$comp_fy_id);
						
						if($get_opn_balance_info)
						 $op_balance  = $get_opn_balance_info['acc_op_bal'];
						
						$acc_txn_tbl = $company_id.'_accnttxnnn_'.$account['master_id'].'_'.$comp_fy_id;
						$builder = $extdb->table($acc_txn_tbl);
						$builder->where('acc_id', $account['master_id']);
						$builder->where('acc_txn_date <=', $to_date);
						$builder->where('bo_id', $this->bo_id);
						$builder->orderBy('acc_txn_date', 'desc');
						$builder->orderBy('voucher_txn_id', 'desc');
						$builder->orderBy('acc_txn_id', 'desc');
						$builder->limit(1);
						$transaction = $builder->get()->getRowArray();
						if($transaction)
					    	{
					    		if($transaction['acc_bal'] < 0){
									$credit_total += abs($transaction['acc_bal']);
									$credit_detail_total = abs($transaction['acc_bal']);
					    		}
								if($transaction['acc_bal'] >= 0){
									$debit_total += $transaction['acc_bal'];
									$debit_detail_total = $transaction['acc_bal'];
								}
					    	}
					    	else
					    	{
					    		//check opening balance
								if($get_opn_balance_info){
									$acc_opn_balance  = $get_opn_balance_info['acc_op_bal'];
									if($acc_opn_balance<0){
										$credit_total +=abs($acc_opn_balance);
										$credit_detail_total = abs($acc_opn_balance);
									}
									if($acc_opn_balance>0){
										$debit_total +=$acc_opn_balance;
										$debit_detail_total = $acc_opn_balance;
									}

								}
					    	}
						$balance = $debit_detail_total - $credit_detail_total;
						$balance_total += $balance;	
						$final2[] = [
					  			'group_id'	 	=> $crs_master_id,//$account['master_id'],
					  			'group_name' 	=> '&nbsp;&nbsp;&nbsp;&raquo;&raquo; '.$account['crs_master_name'].'('.$company_name.')',
					  			'balance'	 	=> $balance,
					  			'type'			=> 'acc',
					  			'style'			=> 'font-style:italic;'
					  		];	
						
						}						
						
			       }
				//check sundry accounts
			    $builder = $this->erp_db->table('aictlyerp_grpmapping_univdb grpmp');
				$builder->join('aictlyerp_grpcomstid_univdb grpcom','grpcom.crs_master_id=grpmp.crs_master_id');
				$builder->select(array('grpmp.master_id','grpmp.crs_master_id','grpcom.crs_master_name'));
				$builder->where('grpmp.grpco_id', $ses_grp_id);
				$builder->where('grpmp.master_type','bsd');	
				$builder->where('grpmp.crs_master_id >','0');
				$builder->where('grpmp.comp_id',$company_id);	
				$builder->where('grpmp.acc_grp_parent_id',$acc_grp_parent_id);
				$builder->orderBy('grpcom.crs_master_name');
				$sundry_accounts = $builder->get()->getResultArray();
				if($sundry_accounts){
						foreach ($sundry_accounts as $sundry_account) {
							$crs_master_id = $sundry_account['crs_master_id'];
							$op_balance = 0;
							$fr_balance = 0;
							$to_balance = 0;

							$credit_detail_total = 0;
							$debit_detail_total = 0;
							
						
						
						$acc_txn_tbl = $company_id.'_sundrytxnn_'.$sundry_account['master_id'].'_'.$comp_fy_id;
						$builder = $extdb->table($acc_txn_tbl);
						$builder->where('bill_sundry_id', $sundry_account['master_id']);
						$builder->where('sundry_txn_date <=', $to_date);
						$builder->where('bo_id', $this->bo_id);
						$builder->orderBy('sundry_txn_date', 'desc');
						$builder->orderBy('voucher_txn_id', 'desc');
						$builder->orderBy('sundry_txn_id', 'desc');
						$builder->limit(1);
						$transaction = $builder->get()->getRowArray();
						if($transaction)
					    	{
					    		if($transaction['sundry_bal'] < 0){
									$credit_total += abs($transaction['sundry_bal']);
									$credit_detail_total = abs($transaction['sundry_bal']);
					    		}
								if($transaction['sundry_bal'] >= 0){
									$debit_total += $transaction['sundry_bal'];
									$debit_detail_total = $transaction['sundry_bal'];
								}
					    	}
					    	else
					    	{
					    		$bill_sundry_op_balance = $this->sundry_opn_balance_info($sundry_account['master_id'],$extdb,$company_id,$comp_fy_id);
					    		if($bill_sundry_op_balance)
					    		{
					    			if($bill_sundry_op_balance['bsd_op_bal'] < 0){
										$credit_total += abs($bill_sundry_op_balance['bsd_op_bal']);
										$credit_detail_total = abs($bill_sundry_op_balance['bsd_op_bal']);
						    		}
									if($bill_sundry_op_balance['bsd_op_bal'] >= 0){
										$debit_total += $bill_sundry_op_balance['bsd_op_bal'];
										$debit_detail_total = $bill_sundry_op_balance['bsd_op_bal'];
									}
					    		}
					    		
					    	}

					    	$balance = $debit_detail_total - $credit_detail_total;

					  		
							$balance_total += $balance;
							$final2[] = [
					  			'group_id'	 	=> $crs_master_id,//$sundry_account['master_id'],
					  			'group_name' 	=> '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &raquo;&raquo; '.$sundry_account['crs_master_name'].'('.$company_name.')',
					  			'balance'	 	=> $balance,
					  			'type'			=> 'bsd',
					  			'style'			=> 'font-style:italic;'
					  		];
							
					  		
						
						
						
						
						
						
				         }
				}
			
				
			}
			$final = [0 => [
		  			'group_id'	 	=> $parent['acc_grp_parent_id'],
		  			'group_name' 	=> $parent['grp_name'],
		  			'balance'	 	=> $balance_total,
		  			'type'			=> 'prt',
		  			'style'			=> 'font-weight:bold;'
		  		] ];
		}
	
	    if($view == 0)
	  		return $final;
	  	if($view == 2)
	  		return $final2;
		
		return $final;
	}
		


    public function get_pl_details($from_date,$to_date,$erp_db,$group_companies_list)
    {
    	$array1 = [];
    	$array2 = [];

    	$l_step1_total = 0;
    	$r_step1_total = 0;

    	// $data = $this->get_parent_group_details_pl(6,0,$from_date,$to_date); // Opening Stock
    	// if($data)
    		$l_step1_total += 0;
		
		
    	$data = $this->get_parent_group_details_dl(7,0,$from_date,$to_date,$erp_db,$group_companies_list); // PURCHASES
    	
		if($data)
    		$l_step1_total += array_sum(array_column($data, 'balance'));

    	$data = $this->get_parent_group_details_dl(11,0,$from_date,$to_date,$erp_db,$group_companies_list); // DIRECTO EXPENSES
    	
		
		if($data)
    		$l_step1_total += array_sum(array_column($data, 'balance'));

    	$data = $this->get_parent_group_details_dl(8,0,$from_date,$to_date,$erp_db,$group_companies_list); // SALES
    	if($data)
    		$r_step1_total += -array_sum(array_column($data, 'balance'));

    	$data = $this->get_parent_group_details_dl(10,0,$from_date,$to_date,$erp_db,$group_companies_list); // DIRECT INCOMES
    	if($data)
    		$r_step1_total += -array_sum(array_column($data, 'balance'));

    	// $data = $this->get_parent_group_details_pl(9,0,$from_date,$to_date); // CLOSING STOCK
    	// if($data)
    	// 	$r_step1_total += -array_sum(array_column($data, 'balance'));

    	$data = $this->load_stock_status_items(0,0,$to_date);
    	if($data)
    		$r_step1_total += $data;

	
		$l_step2_total = 0;
		$r_step2_total = 0;
		

		if($l_step1_total > $r_step1_total){
			$diff = $l_step1_total - $r_step1_total;
			$l_step2_total += $diff;
		}
		if($l_step1_total < $r_step1_total){
			$diff = $r_step1_total - $l_step1_total;
			$r_step2_total += $diff;
		}

		$data = $this->get_parent_group_details_dl(13,0,$from_date,$to_date,$erp_db,$group_companies_list); // INDIRECT EXPENSES
    	if($data)
    		$l_step2_total += array_sum(array_column($data, 'balance'));

    	$data = $this->get_parent_group_details_dl(12,0,$from_date,$to_date,$erp_db,$group_companies_list); // INDIRECT INCOMES
    	if($data)
    		$r_step2_total += -array_sum(array_column($data, 'balance'));

    	$balance = parseAmount($l_step2_total) - parseAmount($r_step2_total);
    	// $balance = $r_step2_total - $l_step2_total;

    	$final = [
    		'group_id'	=> 0,
    		'group_name'=> 'Profit / Loss',
    		'balance'	=> $balance,
    		'type'		=> 'pnl',
    		'style'		=> 'font-weight:bold;'
    	];
    	
    	return $final;
    }

    public function get_op_diff_balance_details($erp_db,$group_companies_list,$array)
    {
		
		$master_final=array();
	    $ses_grp_id = $this->session->get('ses_grp_id');  
	   
	    $acc_grp_par_tbl = 'aictlyerp_grpparentn_univdb';
    	$result =  $erp_db->table($acc_grp_par_tbl)->whereIn('acc_grp_parent_id', $array)->get()->getResultArray();
		
		$master_op_balance=0;
    	foreach ($result as  $value) 
	  	{
			$acc_grp_parent_id = $value['acc_grp_parent_id'];
		$group_companies_list = $this->group_companies_list();
	    if($group_companies_list){
			foreach($group_companies_list as $grprow){
                 $company_id         =  $grprow['company_id'];
				 $comp_fy_id         =  $grprow['comp_fy_id'];
				 $comp_code          =  $grprow['comp_code'];
				 $comp_name          =  ucwords(strtolower($grprow['comp_name']));
				 $comp_short_name    =  ucwords(strtolower($grprow['comp_short_name']));
                 $company_name       =  $comp_name.'('.$comp_short_name.')';				 
				  $extdb             = $this->externaldb->single_company_db($comp_code);
				  
				
		$final = [];
		$credit_op_total = 0;
		$debit_op_total = 0;

	  	//check accounts
		
		$builder = $this->erp_db->table('aictlyerp_grpmapping_univdb grpmp');
		$builder->join('aictlyerp_grpcomstid_univdb grpcom','grpcom.crs_master_id=grpmp.crs_master_id');
		$builder->select(array('grpmp.master_id','grpmp.crs_master_id','grpcom.crs_master_name'));
		$builder->where('grpmp.grpco_id', $ses_grp_id);
		$builder->where('grpmp.master_type','acc');	
		$builder->where('grpmp.crs_master_id >','0');
		$builder->where('grpmp.comp_id',$company_id);
        $builder->where('grpmp.acc_grp_parent_id',$acc_grp_parent_id);		
		$builder->orderBy('grpcom.crs_master_name');
		$accounts = $builder->get()->getResultArray();
		if($accounts){
			foreach ($accounts as $account) {
                $crs_master_id = $account['crs_master_id'];
				$group_id      = $account['master_id'];
				$group_name    = $account['crs_master_name'];
				
		  		$credit = 0;
		  		$debit = 0;
		  		$credit_total = 0;
		  		$debit_total = 0;

		  		
		  		
				//check opening balance
				$get_opn_balance_info = $this->acc_opn_balance_info($account['master_id'],$extdb,$company_id,$comp_fy_id);
				if($get_opn_balance_info){
				 $acc_opn_balance  = $get_opn_balance_info['acc_op_bal'];
				 if($acc_opn_balance<0)
					$credit_op_total +=abs($acc_opn_balance);
				 if($acc_opn_balance>0)
				    $debit_op_total +=$acc_opn_balance;
				 }

		    	
		    		//check opening balance
				 if($get_opn_balance_info){
				   $acc_opn_balance  = $get_opn_balance_info['acc_op_bal'];
				   if($acc_opn_balance<0)
					  $credit_total += abs(parseAmount($acc_opn_balance));
				   if($acc_opn_balance>0)
				      $debit_total += parseAmount($acc_opn_balance);
				 }
		    	

		    	$balance = parseAmount($debit_total) - parseAmount($credit_total);
		  		if($balance < 0)
		  			$credit = abs($balance);
		  		if($balance >= 0)
		  			$debit = $balance;
		  		
				}
			} 
		//check sundry accounts
		$builder1 = $this->erp_db->table('aictlyerp_grpmapping_univdb grpmp');
		$builder1->join('aictlyerp_grpcomstid_univdb grpcom','grpcom.crs_master_id=grpmp.crs_master_id');
		$builder1->select(array('grpmp.master_id','grpmp.crs_master_id','grpcom.crs_master_name'));
		$builder1->where('grpmp.grpco_id', $ses_grp_id);
		$builder1->where('grpmp.master_type','bsd');	
		$builder1->where('grpmp.crs_master_id >','0');
		$builder1->where('grpmp.comp_id',$company_id);	
		$builder->where('grpmp.acc_grp_parent_id',$acc_grp_parent_id);	
		$builder1->orderBy('grpcom.crs_master_name');
		
		$sundry_accounts = $builder1->get()->getResultArray();
		//echo $this->erp_db->GetLastQuery();
		//echo'<br>';
		if($sundry_accounts){
			foreach ($sundry_accounts as $sundry_account) {

				$crs_master_id = $sundry_account['crs_master_id'];
				$group_name    = $sundry_account['crs_master_name'];
				
		  		$credit = 0;
		  		$debit = 0;
		  		$credit_total = 0;
		  		$debit_total = 0;

		  		

				//check opening balance
				$bill_sundry_op_balance = $this->bill_sundry_op_balance($sundry_account['master_id'],$extdb,$company_id,$comp_fy_id);
				if($bill_sundry_op_balance)
				{
					if($bill_sundry_op_balance['bsd_op_bal'] < 0)
						$credit_op_total += abs(parseAmount($bill_sundry_op_balance['bsd_op_bal']));
					if($bill_sundry_op_balance['bsd_op_bal'] >= 0)
						$debit_op_total += parseAmount($bill_sundry_op_balance['bsd_op_bal']);
				}

		    	
		    		//check opening balance
					if($bill_sundry_op_balance)
					{
						if($bill_sundry_op_balance['bsd_op_bal'] < 0)
							$credit_total += abs(parseAmount($bill_sundry_op_balance['bsd_op_bal']));
						if($bill_sundry_op_balance['bsd_op_bal'] >= 0)
							$debit_total += parseAmount($bill_sundry_op_balance['bsd_op_bal']);
					}
		    	

					$balance = parseAmount($debit_total) - parseAmount($credit_total);
					if($balance < 0)
						$credit = abs($balance);
					if($balance >= 0)
						$debit = $balance;

				  }
			    }			
			
			$credit = $debit = 0;
			$op_balance = 0;
			
			//echo $company_name .'==>'.parseAmount($debit_op_total).' -'. parseAmount($credit_op_total);
			//echo '<br>';

			$op_balance = parseAmount($debit_op_total) - parseAmount($credit_op_total);
			$master_op_balance +=$op_balance;		
			   
	        }
				
		   }	
		}
			
	$final = [
  			'group_id'		=> 0,
  			'group_name'	=> 'Difference in Opening',
  			'balance'		=> $master_op_balance,
  			'type'			=> 'opn',
  			'style'			=> 'font-weight:bold;'
  		];
  		// echo $op_balance;exit;
  		if($master_op_balance != 0)
  			return $final;

  		return [];   
	  
    }

    public function get_parent_group_details_dl($acc_grp_parent_id, $view, $from_date, $to_date,$erp_db,$group_companies_list)
    {  $ses_grp_id           = $this->session->get('ses_grp_id');  
    	$final = [];
    	$final1 = [];
    	$final2 = [];
        $balance_total=0;
    	$acc_grp_par_tbl = 'aictlyerp_grpparentn_univdb';
    	$parent =  $erp_db->table($acc_grp_par_tbl)->where('acc_grp_parent_id', $acc_grp_parent_id)->get()->getRowArray();

    	if($parent)
    	{
    		$final1[] = [
	  			'group_id'	 	=> $parent['acc_grp_parent_id'],
	  			'group_name' 	=> $parent['grp_name'],
	  			'balance'	 	=> '',
	  			'type'			=> 'prt',
	  			'style'			=> 'font-weight:bold;'
	  		];

	  		$final2[] = [
	  			'group_id'	 	=> $parent['acc_grp_parent_id'],
	  			'group_name' 	=> $parent['grp_name'],
	  			'balance'	 	=> '',
	  			'type'			=> 'prt',
	  			'style'			=> 'font-weight:bold;'
	  		];
	  		$balance = 0;
			  		$credit_total = 0;
			  		$debit_total = 0;

		 if($group_companies_list){
			foreach($group_companies_list as $grprow){
				 $company_id         =  $grprow['company_id'];
				 $comp_fy_id         =  $grprow['comp_fy_id'];
				 $comp_code          =  $grprow['comp_code'];
				 $comp_name          =  ucwords(strtolower($grprow['comp_name']));
				 $comp_short_name    =  ucwords(strtolower($grprow['comp_short_name']));
                 $company_name       =  $comp_name;
				 $extdb              =  $this->externaldb->single_company_db($comp_code);
				 
				$builder = $this->erp_db->table('aictlyerp_grpmapping_univdb grpmp');
				$builder->join('aictlyerp_grpcomstid_univdb grpcom','grpcom.crs_master_id=grpmp.crs_master_id');
				$builder->select(array('grpmp.master_id','grpmp.crs_master_id','grpcom.crs_master_name'));
				$builder->where('grpmp.grpco_id', $ses_grp_id);
				$builder->where('grpmp.master_type','acc');	
				$builder->where('grpmp.crs_master_id >','0');
				$builder->where('grpmp.comp_id',$company_id);	
				$builder->where('grpmp.acc_grp_parent_id',$acc_grp_parent_id);
				$builder->orderBy('grpcom.crs_master_name');
				$accounts = $builder->get()->getResultArray();
				if($accounts){
					foreach ($accounts as $account) {
						$crs_master_id = $account['crs_master_id'];
						$group_id      = $account['master_id'];
						$group_name    = $account['crs_master_name'].'('.$company_name.')';
						
						    $op_balance = 0;
							$fr_balance = 0;
							$to_balance = 0;

							$credit_detail_total = 0;
							$debit_detail_total = 0;
							
						
						 $get_opn_balance_info = $this->acc_opn_balance_info($account['master_id'],$extdb,$company_id,$comp_fy_id);
						
						if($get_opn_balance_info)
						 $op_balance  = $get_opn_balance_info['acc_op_bal'];
						 
						
						//check last transaction- from
						$acc_txn_tbl = $company_id.'_accnttxnnn_'.$account['master_id'].'_'.$comp_fy_id;
						$builder = $extdb->table($acc_txn_tbl);
						$builder->where('acc_id', $account['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$builder->where('acc_txn_date <=', $from_date);
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
						$acc_txn_tbl = $company_id.'_accnttxnnn_'.$account['master_id'].'_'.$comp_fy_id;
						$builder = $extdb->table($acc_txn_tbl);
						$builder->where('acc_id', $account['master_id']);
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
								$credit_detail_total = abs($tr_balance);
					    }
						if($tr_balance >= 0){
								$debit_total += $tr_balance;
								$debit_detail_total = $tr_balance;
						}
						$balance = $debit_detail_total - $credit_detail_total;	
						$balance_total += $balance;		
						$final2[] = [
					  			'group_id'	 	=> $crs_master_id,//$account['master_id'],
					  			'group_name' 	=> '&nbsp;&nbsp;&nbsp;&raquo;&raquo; '.$account['crs_master_name'].'('.$company_name.')',
					  			'balance'	 	=> $balance,
					  			'type'			=> 'acc',
					  			'style'			=> 'font-style:italic;'
					  		];	
						
						}						
						
			       }
					//check sundry accounts
			    $builder = $this->erp_db->table('aictlyerp_grpmapping_univdb grpmp');
				$builder->join('aictlyerp_grpcomstid_univdb grpcom','grpcom.crs_master_id=grpmp.crs_master_id');
				$builder->select(array('grpmp.master_id','grpmp.crs_master_id','grpcom.crs_master_name'));
				$builder->where('grpmp.grpco_id', $ses_grp_id);
				$builder->where('grpmp.master_type','bsd');	
				$builder->where('grpmp.crs_master_id >','0');
				$builder->where('grpmp.comp_id',$company_id);	
				$builder->where('grpmp.acc_grp_parent_id',$acc_grp_parent_id);
				$builder->orderBy('grpcom.crs_master_name');
				$sundry_accounts = $builder->get()->getResultArray();
				if($sundry_accounts){
						foreach ($sundry_accounts as $sundry_account) {
							$crs_master_id = $sundry_account['crs_master_id'];
							$op_balance = 0;
							$fr_balance = 0;
							$to_balance = 0;

							$credit_detail_total = 0;
							$debit_detail_total = 0;
							
							 //check opening balance
						 $get_opn_balance_info = $this->sundry_opn_balance_info($sundry_account['master_id'],$extdb,$company_id,$comp_fy_id);
						
					//check last transaction- from
						$acc_txn_tbl = $company_id.'_sundrytxnn_'.$sundry_account['master_id'].'_'.$comp_fy_id;
						$builder = $extdb->table($acc_txn_tbl);
						$builder->where('bill_sundry_id', $sundry_account['master_id']);
						$builder->where('sundry_txn_date <=', $from_date);
						$builder->where('bo_id', $this->bo_id);
						$builder->orderBy('sundry_txn_date', 'desc');
						$builder->orderBy('voucher_txn_id', 'desc');
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
						$acc_txn_tbl = $company_id.'_sundrytxnn_'.$sundry_account['master_id'].'_'.$comp_fy_id;
						$builder = $extdb->table($acc_txn_tbl);
						$builder->where('bill_sundry_id', $sundry_account['master_id']);
						$builder->where('sundry_txn_date <=', $to_date);
						$builder->where('bo_id', $this->bo_id);
						$builder->orderBy('sundry_txn_date', 'desc');
						$builder->orderBy('voucher_txn_id', 'desc');
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
							$balance_total += $balance;
					  		$final2[] = [
					  			'group_id'	 	=> $crs_master_id,//$sundry_account['master_id'],
					  			'group_name' 	=> '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &raquo;&raquo; '.$sundry_account['crs_master_name'],
					  			'balance'	 	=> $balance,
					  			'type'			=> 'bsd',
					  			'style'			=> 'font-style:italic;'
					  		];
						
						
						
						
						
						
				         }
				}
			
				
			}
			$final = [0 => [
		  			'group_id'	 	=> $parent['acc_grp_parent_id'],
	  			    'group_name' 	=> $parent['grp_name'],
		  			'balance'	 	=> $balance_total,
		  			'type'			=> 'prt',
		  			'style'			=> 'font-weight:bold;'
		  		] ];
			
		 }
	   
		
		if($view == 0)
		  		return $final;
		  	if($view == 1)
		  		return $final1;
		  	if($view == 2)
		  		return $final2;
		}
		return $final;
	}
	
	public function get_parent_group_details_ls($acc_grp_parent_id, $view, $from_date, $to_date,$erp_db,$group_companies_list)
    {  $ses_grp_id           = $this->session->get('ses_grp_id');  
    	$final = [];
    	$final1 = [];
    	$final2 = [];

    	$acc_grp_par_tbl = 'aictlyerp_grpparentn_univdb';
    	$parent =  $erp_db->table($acc_grp_par_tbl)->where('acc_grp_parent_id', $acc_grp_parent_id)->get()->getRowArray();

    	if($parent)
    	{
    		

	  		$balance = 0;
			  		$credit_total = 0;
			  		$debit_total = 0;

		 if($group_companies_list){
			foreach($group_companies_list as $grprow){
				 $company_id         =  $grprow['company_id'];
				 $comp_fy_id         =  $grprow['comp_fy_id'];
				 $comp_code          =  $grprow['comp_code'];
				 $comp_name          =  ucwords(strtolower($grprow['comp_name']));
				 $comp_short_name    =  ucwords(strtolower($grprow['comp_short_name']));
                 $company_name       =  $comp_name;
				 $extdb              =  $this->externaldb->single_company_db($comp_code);
				 
				$builder = $this->erp_db->table('aictlyerp_grpmapping_univdb grpmp');
				$builder->join('aictlyerp_grpcomstid_univdb grpcom','grpcom.crs_master_id=grpmp.crs_master_id');
				$builder->select(array('grpmp.master_id','grpmp.crs_master_id','grpcom.crs_master_name'));
				$builder->where('grpmp.grpco_id', $ses_grp_id);
				$builder->where('grpmp.master_type','acc');	
				$builder->where('grpmp.crs_master_id >','0');
				$builder->where('grpmp.comp_id',$company_id);	
				$builder->where('grpmp.acc_grp_parent_id',$acc_grp_parent_id);
				$builder->orderBy('grpcom.crs_master_name');
				$accounts = $builder->get()->getResultArray();
				if($accounts){
					foreach ($accounts as $account) {
						$crs_master_id = $account['crs_master_id'];
						$group_id      = $account['master_id'];
						$group_name    = $account['crs_master_name'].'('.$company_name.')';
						
						    $op_balance = 0;
							$fr_balance = 0;
							$to_balance = 0;

							$credit_detail_total = 0;
							$debit_detail_total = 0;
							
						
						 $get_opn_balance_info = $this->acc_opn_balance_info($account['master_id'],$extdb,$company_id,$comp_fy_id);
						
						if($get_opn_balance_info)
						 $op_balance  = $get_opn_balance_info['acc_op_bal'];
						 
						
						//check last transaction- from
						$acc_txn_tbl = $company_id.'_accnttxnnn_'.$account['master_id'].'_'.$comp_fy_id;
						$builder = $extdb->table($acc_txn_tbl);
						$builder->where('acc_id', $account['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$builder->where('acc_txn_date <=', $from_date);
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
						$acc_txn_tbl = $company_id.'_accnttxnnn_'.$account['master_id'].'_'.$comp_fy_id;
						$builder = $extdb->table($acc_txn_tbl);
						$builder->where('acc_id', $account['master_id']);
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
								$credit_detail_total = abs($tr_balance);
					    }
						if($tr_balance >= 0){
								$debit_total += $tr_balance;
								$debit_detail_total = $tr_balance;
						}
						$balance += $debit_detail_total - $credit_detail_total;	
						
						
						
						}						
						
			       }
			//check sundry accounts
			    $builder = $this->erp_db->table('aictlyerp_grpmapping_univdb grpmp');
				$builder->join('aictlyerp_grpcomstid_univdb grpcom','grpcom.crs_master_id=grpmp.crs_master_id');
				$builder->select(array('grpmp.master_id','grpmp.crs_master_id','grpcom.crs_master_name'));
				$builder->where('grpmp.grpco_id', $ses_grp_id);
				$builder->where('grpmp.master_type','bsd');	
				$builder->where('grpmp.crs_master_id >','0');
				$builder->where('grpmp.comp_id',$company_id);	
				$builder->where('grpmp.acc_grp_parent_id',$acc_grp_parent_id);
				$builder->orderBy('grpcom.crs_master_name');
				$sundry_accounts = $builder->get()->getResultArray();
				if($sundry_accounts){
						foreach ($sundry_accounts as $sundry_account) {
							$op_balance = 0;
							$fr_balance = 0;
							$to_balance = 0;
							$group_id      = $sundry_account['master_id'];
						    $group_name    = $sundry_account['crs_master_name'].'('.$company_name.')';

							$credit_detail_total = 0;
							$debit_detail_total = 0;
							
							 //check opening balance
						 $get_opn_balance_info = $this->sundry_opn_balance_info($sundry_account['master_id'],$extdb,$company_id,$comp_fy_id);
						
					//check last transaction- from
						$acc_txn_tbl = $company_id.'_sundrytxnn_'.$sundry_account['master_id'].'_'.$comp_fy_id;
						$builder = $extdb->table($acc_txn_tbl);
						$builder->where('bill_sundry_id', $sundry_account['master_id']);
						$builder->where('sundry_txn_date <=', $from_date);
						$builder->where('bo_id', $this->bo_id);						
						$builder->orderBy('sundry_txn_date', 'desc');
						$builder->orderBy('voucher_txn_id', 'desc');
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
						$acc_txn_tbl = $company_id.'_sundrytxnn_'.$sundry_account['master_id'].'_'.$comp_fy_id;
						$builder = $extdb->table($acc_txn_tbl);
						$builder->where('bill_sundry_id', $sundry_account['master_id']);
						$builder->where('sundry_txn_date <=', $to_date);
						$builder->where('bo_id', $this->bo_id);
						$builder->orderBy('sundry_txn_date', 'desc');
						$builder->orderBy('voucher_txn_id', 'desc');
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

							$balance += $debit_detail_total - $credit_detail_total;

					  		
				         }
				}
			
				
			}
			
		 }
	   
		 $final1[] = [
	  			'group_id'	 	=> $parent['acc_grp_parent_id'],
	  			'group_name' 	=> $parent['grp_name'],
	  			'balance'	 	=> $balance,
	  			'type'			=> 'prt',
	  			'style'			=> 'font-weight:bold;'
	  		];
		
		}		
		return $final1;
	}
	
   public function get_parent_group_details_pl($acc_grp_parent_id, $view, $from_date, $to_date)
    {
    	$final = [];
    	$final1 = [];
    	$final2 = [];

    	$acc_grp_par_tbl = 'aictlyerp_grpparentn_univdb';
    	$parent =  $this->db->table($acc_grp_par_tbl)->where('acc_grp_parent_id', $acc_grp_parent_id)->get()->getRowArray();

    	if($parent)
    	{
    		$final1[] = [
	  			'group_id'	 	=> $parent['acc_grp_parent_id'],
	  			'group_name' 	=> $parent['grp_name'],
	  			'balance'	 	=> '',
	  			'type'			=> 'prt',
	  			'style'			=> 'font-weight:bold;'
	  		];

	  		$final2[] = [
	  			'group_id'	 	=> $parent['acc_grp_parent_id'],
	  			'group_name' 	=> $parent['grp_name'],
	  			'balance'	 	=> '',
	  			'type'			=> 'prt',
	  			'style'			=> 'font-weight:bold;'
	  		];

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

			  		$final2[] = [
			  			'group_id'	 	=> $group_id,
			  			'group_name' 	=> '&nbsp;&nbsp; &raquo; '.$group_name,
			  			'balance'	 	=> '',
			  			'type'			=> 'grp',
			  			'style'			=> 'font-weight:500;'
			  		];

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
							// branch check 
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
							// branch check 
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

					  		$final2[] = [
					  			'group_id'	 	=> $account['acc_id'],
					  			'group_name' 	=> '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &raquo;&raquo; '.$account['acc_name'],
					  			'balance'	 	=> $balance,
					  			'type'			=> 'acc',
					  			'style'			=> 'font-style:italic;'
					  		];
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

					  		$final2[] = [
					  			'group_id'	 	=> $sundry_account['bill_sundry_id'],
					  			'group_name' 	=> '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &raquo;&raquo; '.$sundry_account['bill_sundry_name'],
					  			'balance'	 	=> $balance,
					  			'type'			=> 'bsd',
					  			'style'			=> 'font-style:italic;'
					  		];
						}
					}

					$balance = $debit_total - $credit_total;
			  		$balance_total += $balance;

			  		$final1[] = [
			  			'group_id'	 	=> $group_id,
			  			'group_name' 	=> '&nbsp;&nbsp; &raquo; '.$group_name,
			  			'balance'	 	=> $balance,
			  			'type'			=> 'grp',
			  			'style'			=> '',
			  		];
		  		}
		  	}


	   	$account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	   	$result3 =  $this->db->table($account_master_tbl)
											 ->where('acc_grp_parent_id', $acc_grp_parent_id)
											 ->where('acc_grp_parent_id !=',14)
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
					// branch check 
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
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value3['acc_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
			    	$builder->where('acc_id', $value3['acc_id']);
					// branch check 
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
			  			'group_id'	 	=> $group_id,
			  			'group_name' 	=> '&nbsp;&nbsp; &raquo; '.$group_name,
			  			'balance'	 	=> $balance,
			  			'type'			=> 'acc',
			  			'style'			=> '',
			  		];

			  		$final2[] = [
			  			'group_id'	 	=> $group_id,
			  			'group_name' 	=> '&nbsp;&nbsp; &raquo; '.$group_name,
			  			'balance'	 	=> $balance,
			  			'type'			=> 'acc',
			  			'style'			=> 'font-weight:500;'
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
			}
			else{
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
			  			'group_id'	 	=> $group_id,
			  			'group_name' 	=> '&nbsp;&nbsp; &raquo; '.$group_name,
			  			'balance'	 	=> $balance,
			  			'type'			=> 'bsd',
			  			'style'			=> '',
			  		];

			  		$final2[] = [
			  			'group_id'	 	=> $group_id,
			  			'group_name' 	=> '&nbsp;&nbsp; &raquo; '.$group_name,
			  			'balance'	 	=> $balance,
			  			'type'			=> 'bsd',
			  			'style'			=> 'font-weight:500;'
			  		];
		  		}
		  	}

		  	$final = [0 => [
		  			'group_id'	 	=> $parent['acc_grp_parent_id'],
		  			'group_name' 	=> $parent['acc_grp_parent'],
		  			'balance'	 	=> $balance_total,
		  			'type'			=> 'prt',
		  			'style'			=> 'font-weight:bold;'
		  		] ];

		  	if($view == 0)
		  		return $final;
		  	if($view == 1)
		  		return $final1;
		  	if($view == 2)
		  		return $final2;
		  	
    	}

	  	return $final;
    }
  
  	function view_item_info($item_id,$comp_code,$company_id,$comp_fy_id){	 
	  $extdb              =  $this->externaldb->single_company_db($comp_code);
	  $item_master_tbl = $company_id.'_itemmaster_'.$comp_fy_id;
	  return $extdb->table($item_master_tbl)->where('item_id', $item_id)->get()->getRowArray();   	   
    }
	function view_units_info($unit_id,$comp_code,$company_id,$comp_fy_id){	
      $extdb                =  $this->externaldb->single_company_db($comp_code);	
	  $item_unit_master_tbl = $company_id.'_itmunitmst_'.$comp_fy_id;
	  return $extdb->table($item_unit_master_tbl)->where('unit_id', $unit_id)->get()->getRowArray();   	   
    }   
	
	public function load_stock_status_items($mc_id,$val,$to_date)
	{	$total_valuation=0;
        $ses_grp_id           = $this->session->get('ses_grp_id');  
	    $group_companies_list = $this->group_companies_list(); 
		 $company_array=array();				
		   if($group_companies_list){
			foreach($group_companies_list as $grprow){
				
				 $company_id         =  $grprow['company_id'];
				 $comp_fy_id         =  $grprow['comp_fy_id'];
				 $comp_code          =  $grprow['comp_code'];
				 $comp_name          =  ucwords(strtolower($grprow['comp_name']));
				 $comp_short_name    =  ucwords(strtolower($grprow['comp_short_name']));
                 $company_name       =  $comp_name;
				 $extdb              =  $this->externaldb->single_company_db($comp_code);
				 
				$builder = $this->erp_db->table('aictlyerp_grpmapping_univdb grpmp');
				$builder->join('aictlyerp_grpcomstid_univdb grpcom','grpcom.crs_master_id=grpmp.crs_master_id');
				$builder->select(array('grpmp.master_id','grpmp.crs_master_id','grpcom.crs_master_name'));
				$builder->where('grpmp.grpco_id', $ses_grp_id);
				$builder->where('grpmp.master_type','itm');	
				$builder->where('grpmp.crs_master_id >','0');
				$builder->where('grpmp.comp_id',$company_id);					
				$builder->orderBy('grpcom.crs_master_name');
				$items = $builder->get()->getResultArray();
				if($items){
					foreach ($items as $item) {
						$item_id         = $item['master_id'];
						$item_info       = $this->view_item_info($item_id,$comp_code,$company_id,$comp_fy_id);
						
						$itemtxnnnn_tbl  = $company_id.'_itemtxnnnn_'.$item_id.'_'.$comp_fy_id;
						$itemtxnval_tbl  = $company_id.'_itemtxnval_'.$item_id.'_'.$comp_fy_id;
						$item_name       = $item['crs_master_name'];
						
						$itmoppybal_tbl  = $company_id.'_itmoppybal_'.$comp_fy_id;
					    $itmoppyval_tbl  = $company_id.'_itmoppyval_'.$comp_fy_id;
		

						$item_type = '';

						if($val != 0)
							$val_id = $val;
						else
							$val_id = $item_info['valmethod_id'];

						$method = '';
						if($val_id == 1)
							$method = 'AVG';
						if($val_id == 2)
							$method = 'FIFO';
						if($val_id == 3)
							$method = 'LIFO';
			
			if($mc_id == 0)
			{
				$builder = $this->erp_db->table('aictlyerp_grpmapping_univdb grpmp');
				$builder->join('aictlyerp_grpcomstid_univdb grpcom','grpcom.crs_master_id=grpmp.crs_master_id');
				$builder->select(array('grpmp.master_id','grpmp.crs_master_id','grpcom.crs_master_name'));
				$builder->where('grpmp.grpco_id', $ses_grp_id);
				$builder->where('grpmp.master_type','mcmst');	
				$builder->where('grpmp.crs_master_id >','0');
				$builder->where('grpmp.comp_id',$company_id);	
				$builder->orderBy('grpcom.crs_master_name');
				$mc_result = $builder->get()->getResultArray();
    			$unit_result = [];
				$unit_result[] = $item_info['item_unit'];

    			$builder = $extdb->table($itmoppybal_tbl);
    			$builder->select('item_unit');
    			$builder->where('item_id', $item_id);
				$builder->where('bo_id', $this->bo_id);				
    			$builder->groupBy('item_unit');
    			$result2 = $builder->get()->getResultArray();
    			if($result2){
    				foreach ($result2 as $key2 => $value2){
    					if(!in_array($value2['item_unit'], $unit_result)){
    						$unit_result[] = $value2['item_unit'];
    					}
    				 }
    			 }

    			$builder = $extdb->table($itemtxnnnn_tbl);
    			$builder->select('item_unit');
    			$builder->where('item_id', $item_id);
				$builder->where('bo_id', $this->bo_id);
    			$builder->groupBy('item_unit');
    			$result2 = $builder->get()->getResultArray();
    			if($result2){
    				foreach ($result2 as $key2 => $value2) {
    					if(!in_array($value2['item_unit'], $unit_result)){
    						$unit_result[] = $value2['item_unit'];
    					}
    				}
    			}

    			
    			foreach ($unit_result as $unit_key => $unit_value) {
    				
    				$item_qty = 0;
	    			$item_value = 0;
	    			$unit_name = '';

	    			$item_unit_info  = $this->view_units_info($unit_value,$comp_code,$company_id,$comp_fy_id);
					if(isset($item_unit_info['item_unit']))
					   $unit_name = $this->enc_string->nc_string($item_unit_info['item_unit'],'de');

	    			foreach ($mc_result as $mc_value) {
		    			$mat_cent_id = $mc_value['master_id'];
						
		    			$builder = $extdb->table($itemtxnnnn_tbl);
				    	$builder->where('item_id', $item_id);
				    	$builder->where('item_unit', $unit_value);
				    	$builder->where('mat_cent_id', $mat_cent_id);
				    	$builder->where('item_txn_date <=', $to_date);
				    	$builder->where('batch_id', 0);
						$builder->where('bo_id', $this->bo_id);
				    	$builder->where('item_avail', 1);
				    	$builder->orderBy('item_txn_date', 'desc');
				    	$builder->orderBy('voucher_txn_id', 'desc');
				    	$builder->orderBy('item_txn_id', 'desc');
				    	$builder->limit(1);
				    	$transaction = $builder->get()->getRowArray();

				    	if($transaction){
				    		$item_qty += floatval($transaction['item_bal_qty']);

				    		$builder = $extdb->table($itemtxnval_tbl);
							$builder->where('item_id', $item_id);
							$builder->where('item_txn_id', $transaction['item_txn_id']);
							$builder->where('method_id', $val_id);
							$itemtxnval = $builder->get()->getRowArray();
							if($itemtxnval){
								$item_value += floatval($itemtxnval['item_value']);
							}
				    	}
				    	else{
				    		$builder = $extdb->table($itmoppybal_tbl); 
							$builder->where('item_id', $item_id);
							$builder->where('item_unit', $unit_value);
							$builder->where('mat_cent_id', $mat_cent_id);
							$builder->where('bo_id', $this->bo_id);							
							$builder->where('batch_id', 0);
					    	$itmoppybal = $builder->get()->getRowArray();
					    	if($itmoppybal){
					    		$item_qty += floatval($itmoppybal['op_bal_qty']);

								$builder = $extdb->table($itmoppyval_tbl); 
								$builder->where('item_id', $item_id);
								$builder->where('item_unit', $unit_value);
								$builder->where('mat_cent_id', $mat_cent_id);
								$builder->where('bo_id', $this->bo_id);								
								$builder->where('batch_id', 0);
								$builder->where('method_id', $val_id);
								$itmoppyval = $builder->get()->getRowArray();
								
								if($itmoppyval){
						    		$item_value += floatval($itmoppyval['op_bal_val']);
						    	}
					    	}
				    	}	
		    		}

		    		$total_valuation += parseValue($item_value);
		    		
    			}
			}	
					}
				}
				 
				
		      }
		   }
	 return $total_valuation;	
	}
	
	  
		

    public function load_profit_loss1($view,$from_date,$to_date,$nil_type)
    {  $group_companies_list = $this->group_companies_list();
    	$array1 = [];
    	$array2 = [];

    	$data = 0;
    	if($data != '')
    	{
 			$array1[] = [
	    		'l_group_id'	   	=> 0,
	    		'l_group_name'    	=> 'Opening Stock',
	    		'l_balance'		    => $data != '' ? formatAmount($data) : '',
	    		'l_balance_total'   => $data != '' ? $data : '',
	    		'l_type'		  	=> 'opn',
	    		'l_style'		  	=> 'font-weight:bold;',
	    	];
    	}


    	$data = $this->get_parent_group_details_dl(7, $view,$from_date,$to_date,$this->erp_db,$group_companies_list);

		
		if($data)
    	{
    		foreach ($data as $key => $value) {
    			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
	    			$array1[] = [
			    		'l_group_id'	  		=> $value['group_id'],
			    		'l_group_name'    	=> $value['group_name'],
			    		'l_balance'		  		=> $value['balance'] != '' ? formatAmount($value['balance']) : '',
			    		'l_balance_total'		=> $value['balance'],
			    		'l_type'		  			=> $value['type'],
			    		'l_style'		  		=> $value['style'],
			    	];
		    	}
    		}
    	}
    	$data = $this->get_parent_group_details_dl(11, $view,$from_date,$to_date,$this->erp_db,$group_companies_list);
    	if($data)
    	{
    		foreach ($data as $key => $value) {
    			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
	    			$array1[] = [
			    		'l_group_id'	  		=> $value['group_id'],
			    		'l_group_name'    	=> $value['group_name'],
			    		'l_balance'		  		=> $value['balance'] != '' ? formatAmount($value['balance']) : '',
			    		'l_balance_total'		=> $value['balance'],
			    		'l_type'		  			=> $value['type'],
			    		'l_style'		  		=> $value['style'],
			    	];
		    	}
    		}
    	}


    	$data = $this->get_parent_group_details_dl(8, $view,$from_date,$to_date,$this->erp_db,$group_companies_list);
    	if($data)
    	{
    		foreach ($data as $key => $value) {
    			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
	    			$array2[] = [
			    		'r_group_id'	   	=> $value['group_id'],
			    		'r_group_name'    	=> $value['group_name'],
			    		'r_balance'		  		=> $value['balance'] != '' ? formatAmount(-$value['balance']) : '',
			    		'r_balance_total'		=> $value['balance'] != '' ? -$value['balance'] : '',
			    		'r_type'		  			=> $value['type'],
			    		'r_style'		  		=> $value['style'],
			    	];
		    	}
    		}
    	}
    	$data = $this->get_parent_group_details_dl(10, $view,$from_date,$to_date,$this->erp_db,$group_companies_list);
    	if($data)
    	{
    		foreach ($data as $key => $value) {
    			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
	    			$array2[] = [
			    		'r_group_id'	   	=> $value['group_id'],
			    		'r_group_name'    	=> $value['group_name'],
			    		'r_balance'		  		=> $value['balance'] != '' ? formatAmount(-$value['balance']) : '',
			    		'r_balance_total'		=> $value['balance'] != '' ? -$value['balance'] : '',
			    		'r_type'		  			=> $value['type'],
			    		'r_style'		  		=> $value['style'],
			    	];
		    	}
    		}
    	}
    	

    	$data = $this->load_stock_status_items(0,0,$to_date);
    	if($data != '')
    	{
 			$array2[] = [
	    		'r_group_id'	   	=> 0,
	    		'r_group_name'    	=> 'Closing Stock',
	    		'r_balance'		  		=> $data != '' ? formatAmount($data) : '',
	    		'r_balance_total'		=> $data != '' ? $data : '',
	    		'r_type'		  			=> 'clo',
	    		'r_style'		  		=> 'font-weight:bold;',
	    	];
    	}

    	$final = [];

    	$count = count($array1) > count($array2) ? count($array1) : count($array2);
    	for($i=0; $i<$count; $i++)
    	{
    		$temp1 = !empty($array1[$i]) ? $array1[$i] : [];
    		$temp2 = !empty($array2[$i]) ? $array2[$i] : [];

    		if(count($temp1) || count($temp2)){
    			$temp3 = array_merge($temp1,$temp2);
    			$l_style = isset($temp3['l_style']) ? ['style' => $temp3['l_style']] : [];
    			$r_style = isset($temp3['r_style']) ? ['style' => $temp3['r_style']] : [];
    			
    			$temp3['pq_cellattr'] = ['l_group_name' => $l_style, 'r_group_name' => $r_style];
    			$final[] = $temp3;
    		}
    	}

    	$l_total = array_sum(array_column($array1, 'l_balance_total'));
    	$r_total = array_sum(array_column($array2, 'r_balance_total'));

    	$l_diff = 0;
    	$r_diff = 0;
    	$total = $l_total;

    	if($l_total > $r_total)
    	{
    		$l_diff = 0;
    		$r_diff = parseAmount($l_total) - parseAmount($r_total);
    		$total = $l_total;
    	}
    	if($l_total < $r_total)
    	{
    		$l_diff = parseAmount($r_total) - parseAmount($l_total);
    		$r_diff = 0;
    		$total = $r_total;
    	}

    	$final[] = [
    			'l_group_id'	  => 0,
	    		'l_group_name'    => 'Gross Profit C/F',
	    		'l_balance'		  => formatAmount($l_diff),
	    		'l_balance_total'		  => $l_diff,
	    		'r_group_id'	  => 0,
	    		'r_group_name'    => 'Gross Loss C/F',
	    		'r_balance'		  => formatAmount($r_diff),
	    		'r_balance_total'		  => $r_diff,
    	];

    	$final[] = [
    			'l_group_id'	  => 0,
	    		'l_group_name'    => '',
	    		'l_balance'		  => formatAmount($total),
	    		'l_balance_total'		  => $total,
	    		'r_group_id'	  => 0,
	    		'r_group_name'    => '',
	    		'r_balance'		  => formatAmount($total),
	    		'r_balance_total'		  => $total,
	    		'step'			  => 0,
	    		'pq_rowattr'	  => ['style' => 'background:#E6E6FA;font-weight:bold;']
    	];

    	$array1 = [];
    	$array2 = [];

    	$array1[] = [
    		'l_group_id'	  => 0,
    		'l_group_name'    => 'Gross Loss B/D',
    		'l_balance'		  => formatAmount($r_diff),
    		'l_balance_total'		  => $r_diff,
    	];
    	$array2[] = [
    		'r_group_id'	  => 0,
    		'r_group_name'    => 'Gross Profit B/D',
    		'r_balance'		  => formatAmount($l_diff),
    		'r_balance_total'		  => $l_diff,
    	];

    	$data = $this->get_parent_group_details_dl(13, $view,$from_date,$to_date,$this->erp_db,$group_companies_list);
    	if($data)
    	{
    		foreach ($data as $key => $value) {
    			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
	    			$array1[] = [
			    		'l_group_id'	  		=> $value['group_id'],
			    		'l_group_name'    	=> $value['group_name'],
			    		'l_balance'		  		=> $value['balance'] != '' ? formatAmount($value['balance']) : '',
			    		'l_balance_total'		=> $value['balance'],
			    		'l_type'		  			=> $value['type'],
			    		'l_style'		  		=> $value['style'],
			    	];
		    	}
    		}
    	}
    	$data = $this->get_parent_group_details_dl(12, $view,$from_date,$to_date,$this->erp_db,$group_companies_list);
    	if($data)
    	{
    		foreach ($data as $key => $value) {
    			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
	    			$array2[] = [
			    		'r_group_id'	   	=> $value['group_id'],
			    		'r_group_name'    	=> $value['group_name'],
			    		'r_balance'		  		=> $value['balance'] != '' ? formatAmount(-$value['balance']) : '',
			    		'r_balance_total'		=> $value['balance'] != '' ? -$value['balance'] : '',
			    		'r_type'		  			=> $value['type'],
			    		'r_style'		  		=> $value['style'],
			    	];
		    	}
    		}
    	}

    	$count = count($array1) > count($array2) ? count($array1) : count($array2);
    	for($i=0; $i<$count; $i++)
    	{
    		$temp1 = !empty($array1[$i]) ? $array1[$i] : [];
    		$temp2 = !empty($array2[$i]) ? $array2[$i] : [];

    		if(count($temp1) || count($temp2)){
    			$temp3 = array_merge($temp1,$temp2);
    			$l_style = isset($temp3['l_style']) ? ['style' => $temp3['l_style']] : [];
    			$r_style = isset($temp3['r_style']) ? ['style' => $temp3['r_style']] : [];
    			
    			$temp3['pq_cellattr'] = ['l_group_name' => $l_style, 'r_group_name' => $r_style];
    			$final[] = $temp3;
    		}
    	}

    	$l_total = array_sum(array_column($array1, 'l_balance_total')); //10,000
    	$r_total = array_sum(array_column($array2, 'r_balance_total')); // 8,000

    	$l_diff = 0;
    	$r_diff = 0;
    	$total = $l_total;

    	if($l_total > $r_total)
    	{
    		$l_diff = 0;
    		$r_diff = parseAmount($l_total) - parseAmount($r_total);
    		$total = $l_total;
    	}
    	if($l_total < $r_total)
    	{
    		$l_diff = parseAmount($r_total) - parseAmount($l_total);
    		$r_diff = 0;
    		$total = $r_total;
    	}

    	$final[] = [
    		'l_group_id'	  => 0,
    		'l_group_name'    => 'Net Profit C/D',
    		'l_balance'		  => formatAmount($l_diff),
    		'l_balance_total'		  => $l_diff,
	    	'r_group_id'	  => 0,
    		'r_group_name'    => 'Net Loss C/D',
    		'r_balance'		  => formatAmount($r_diff),
    		'r_balance_total'		  => $r_diff,
		];

		$final[] = [
    		'l_group_id'	  => 0,
    		'l_group_name'    => '',
    		'l_balance'		  => formatAmount($total),
    		'l_balance_total'		  => $total,
	    	'r_group_id'	  => 0,
    		'r_group_name'    => '',
    		'r_balance'		  => formatAmount($total),
    		'r_balance_total'		  => $total,
    		'pq_rowattr'	  => ['style' => 'background:#E6E6FA;font-weight:bold;']
		];

    	return $final;
    }
    
	public function load_profit_loss($view,$from_date,$to_date,$nil_type)
    { $group_companies_list = $this->group_companies_list();
    	$array1 = [];
    	$array2 = [];

    	$data = 0;
    	if($data != '')
    	{
 			$array1[] = [
	    		'l_group_id'	   	=> 0,
	    		'l_group_name'    	=> 'Opening Stock',
	    		'l_balance'		  		=> $data != '' ? formatAmount($data) : '',
	    		'l_balance_total'		=> $data != '' ? $data : '',
	    		'l_type'		  			=> 'opn',
	    		'l_style'		  		=> 'font-weight:bold;',
	    	];
    	}

    	$data = $this->get_parent_group_details_ls(7, $view,$from_date,$to_date,$this->erp_db,$group_companies_list);
    	if($data)
    	{
    		foreach ($data as $key => $value) {
    			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
	    			$array1[] = [
			    		'l_group_id'	  		=> $value['group_id'],
			    		'l_group_name'    	=> $value['group_name'],
			    		'l_balance'		  		=> $value['balance'] != '' ? formatAmount($value['balance']) : '',
			    		'l_balance_total'		=> $value['balance'],
			    		'l_type'		  			=> $value['type'],
			    		'l_style'		  		=> $value['style'],
			    	];
		    	}
    		}
    	}
    	$data = $this->get_parent_group_details_ls(11, $view,$from_date,$to_date,$this->erp_db,$group_companies_list);
    	if($data)
    	{
    		foreach ($data as $key => $value) {
    			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
	    			$array1[] = [
			    		'l_group_id'	  		=> $value['group_id'],
			    		'l_group_name'    	=> $value['group_name'],
			    		'l_balance'		  		=> $value['balance'] != '' ? formatAmount($value['balance']) : '',
			    		'l_balance_total'		=> $value['balance'],
			    		'l_type'		  			=> $value['type'],
			    		'l_style'		  		=> $value['style'],
			    	];
		    	}
    		}
    	}


    	$data = $this->get_parent_group_details_ls(8, $view,$from_date,$to_date,$this->erp_db,$group_companies_list);
    	if($data)
    	{
    		foreach ($data as $key => $value) {
    			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
	    			$array2[] = [
			    		'r_group_id'	   	=> $value['group_id'],
			    		'r_group_name'    	=> $value['group_name'],
			    		'r_balance'		  		=> $value['balance'] != '' ? formatAmount(-$value['balance']) : '',
			    		'r_balance_total'		=> $value['balance'] != '' ? -$value['balance'] : '',
			    		'r_type'		  			=> $value['type'],
			    		'r_style'		  		=> $value['style'],
			    	];
		    	}
    		}
    	}
    	$data = $this->get_parent_group_details_ls(10, $view,$from_date,$to_date,$this->erp_db,$group_companies_list);
    	if($data)
    	{
    		foreach ($data as $key => $value) {
    			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
	    			$array2[] = [
			    		'r_group_id'	   	=> $value['group_id'],
			    		'r_group_name'    	=> $value['group_name'],
			    		'r_balance'		  		=> $value['balance'] != '' ? formatAmount(-$value['balance']) : '',
			    		'r_balance_total'		=> $value['balance'] != '' ? -$value['balance'] : '',
			    		'r_type'		  			=> $value['type'],
			    		'r_style'		  		=> $value['style'],
			    	];
		    	}
    		}
    	}
    	

    	$data = $this->load_stock_status_items(0,0,$to_date);
    	if($data != '')
    	{
 			$array2[] = [
	    		'r_group_id'	   	=> 0,
	    		'r_group_name'    	=> 'Closing Stock',
	    		'r_balance'		  		=> $data != '' ? formatAmount($data) : '',
	    		'r_balance_total'		=> $data != '' ? $data : '',
	    		'r_type'		  			=> 'clo',
	    		'r_style'		  		=> 'font-weight:bold;',
	    	];
    	}

    	$final = [];

    	$count = count($array1) > count($array2) ? count($array1) : count($array2);
    	for($i=0; $i<$count; $i++)
    	{
    		$temp1 = !empty($array1[$i]) ? $array1[$i] : [];
    		$temp2 = !empty($array2[$i]) ? $array2[$i] : [];

    		if(count($temp1) || count($temp2)){
    			$temp3 = array_merge($temp1,$temp2);
    			$l_style = isset($temp3['l_style']) ? ['style' => $temp3['l_style']] : [];
    			$r_style = isset($temp3['r_style']) ? ['style' => $temp3['r_style']] : [];
    			
    			$temp3['pq_cellattr'] = ['l_group_name' => $l_style, 'r_group_name' => $r_style];
    			$final[] = $temp3;
    		}
    	}

    	$l_total = array_sum(array_column($array1, 'l_balance_total'));
    	$r_total = array_sum(array_column($array2, 'r_balance_total'));

    	$l_diff = 0;
    	$r_diff = 0;
    	$total = $l_total;

    	if($l_total > $r_total)
    	{
    		$l_diff = 0;
    		$r_diff = parseAmount($l_total) - parseAmount($r_total);
    		$total = $l_total;
    	}
    	if($l_total < $r_total)
    	{
    		$l_diff = parseAmount($r_total) - parseAmount($l_total);
    		$r_diff = 0;
    		$total = $r_total;
    	}

    	$final[] = [
    			'l_group_id'	  => 0,
	    		'l_group_name'    => 'Gross Profit C/F',
	    		'l_balance'		  => formatAmount($l_diff),
	    		'l_balance_total'		  => $l_diff,
	    		'r_group_id'	  => 0,
	    		'r_group_name'    => 'Gross Loss C/F',
	    		'r_balance'		  => formatAmount($r_diff),
	    		'r_balance_total'		  => $r_diff,
    	];

    	$final[] = [
    			'l_group_id'	  => 0,
	    		'l_group_name'    => '',
	    		'l_balance'		  => formatAmount($total),
	    		'l_balance_total'		  => $total,
	    		'r_group_id'	  => 0,
	    		'r_group_name'    => '',
	    		'r_balance'		  => formatAmount($total),
	    		'r_balance_total'		  => $total,
	    		'step'			  => 0,
	    		'pq_rowattr'	  => ['style' => 'background:#E6E6FA;font-weight:bold;']
    	];

    	$array1 = [];
    	$array2 = [];

    	$array1[] = [
    		'l_group_id'	  => 0,
    		'l_group_name'    => 'Gross Loss B/D',
    		'l_balance'		  => formatAmount($r_diff),
    		'l_balance_total'		  => $r_diff,
    	];
    	$array2[] = [
    		'r_group_id'	  => 0,
    		'r_group_name'    => 'Gross Profit B/D',
    		'r_balance'		  => formatAmount($l_diff),
    		'r_balance_total'		  => $l_diff,
    	];

    	$data = $this->get_parent_group_details_ls(13, $view,$from_date,$to_date,$this->erp_db,$group_companies_list);
    	if($data)
    	{
    		foreach ($data as $key => $value) {
    			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
	    			$array1[] = [
			    		'l_group_id'	  		=> $value['group_id'],
			    		'l_group_name'    	=> $value['group_name'],
			    		'l_balance'		  		=> $value['balance'] != '' ? formatAmount($value['balance']) : '',
			    		'l_balance_total'		=> $value['balance'],
			    		'l_type'		  			=> $value['type'],
			    		'l_style'		  		=> $value['style'],
			    	];
		    	}
    		}
    	}
    	$data = $this->get_parent_group_details_ls(12, $view,$from_date,$to_date,$this->erp_db,$group_companies_list);
    	if($data)
    	{
    		foreach ($data as $key => $value) {
    			if($nil_type == 1 || ($nil_type == 0 && ($value['type'] == 'prt' || $value['balance'] != 0)))
    			{
	    			$array2[] = [
			    		'r_group_id'	   	=> $value['group_id'],
			    		'r_group_name'    	=> $value['group_name'],
			    		'r_balance'		  		=> $value['balance'] != '' ? formatAmount(-$value['balance']) : '',
			    		'r_balance_total'		=> $value['balance'] != '' ? -$value['balance'] : '',
			    		'r_type'		  			=> $value['type'],
			    		'r_style'		  		=> $value['style'],
			    	];
		    	}
    		}
    	}

    	$count = count($array1) > count($array2) ? count($array1) : count($array2);
    	for($i=0; $i<$count; $i++)
    	{
    		$temp1 = !empty($array1[$i]) ? $array1[$i] : [];
    		$temp2 = !empty($array2[$i]) ? $array2[$i] : [];

    		if(count($temp1) || count($temp2)){
    			$temp3 = array_merge($temp1,$temp2);
    			$l_style = isset($temp3['l_style']) ? ['style' => $temp3['l_style']] : [];
    			$r_style = isset($temp3['r_style']) ? ['style' => $temp3['r_style']] : [];
    			
    			$temp3['pq_cellattr'] = ['l_group_name' => $l_style, 'r_group_name' => $r_style];
    			$final[] = $temp3;
    		}
    	}

    	$l_total = array_sum(array_column($array1, 'l_balance_total')); //10,000
    	$r_total = array_sum(array_column($array2, 'r_balance_total')); // 8,000

    	$l_diff = 0;
    	$r_diff = 0;
    	$total = $l_total;

    	if($l_total > $r_total)
    	{
    		$l_diff = 0;
    		$r_diff = parseAmount($l_total) - parseAmount($r_total);
    		$total = $l_total;
    	}
    	if($l_total < $r_total)
    	{
    		$l_diff = parseAmount($r_total) - parseAmount($l_total);
    		$r_diff = 0;
    		$total = $r_total;
    	}

    	$final[] = [
    		'l_group_id'	  => 0,
    		'l_group_name'    => 'Net Profit C/D',
    		'l_balance'		  => formatAmount($l_diff),
    		'l_balance_total'		  => $l_diff,
	    	'r_group_id'	  => 0,
    		'r_group_name'    => 'Net Loss C/D',
    		'r_balance'		  => formatAmount($r_diff),
    		'r_balance_total'		  => $r_diff,
		];

		$final[] = [
    		'l_group_id'	  => 0,
    		'l_group_name'    => '',
    		'l_balance'		  => formatAmount($total),
    		'l_balance_total'		  => $total,
	    	'r_group_id'	  => 0,
    		'r_group_name'    => '',
    		'r_balance'		  => formatAmount($total),
    		'r_balance_total'		  => $total,
    		'pq_rowattr'	  => ['style' => 'background:#E6E6FA;font-weight:bold;']
		];

    	return $final;
    }


   function group_parent_info($acc_grp_id,$extdb,$company_id,$comp_fy_id){	 
      $account_grp_tbl = $company_id.'_grpparentn_'.$comp_fy_id; 
  	  return $extdb->table($account_grp_tbl)->where('acc_grp_parent_id', $acc_grp_id)->get()->getRowArray();   	   
   }

   function main_group_info($acc_grp_id){	 
      $account_grp_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id'); 
  	  return $this->db->table($account_grp_tbl)->where('acc_grp_id', $acc_grp_id)->get()->getRowArray();   	   
   }
   function getcompany_info($company_id){	 
	  return $this->aicountly_db->table('aicountly_cmpmastern_univdb')->where('comp_id', $company_id)->get()->getRowArray();   	   
   }
   public function group_companies_list(){
	 $ses_grp_id = $this->session->get('ses_grp_id');  
	 $this->erp_db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
	 $builder = $this->erp_db->table('aictlyerp_grpcompacs_univdb');
	 $builder->where('aictlyerp_grpcompacs_univdb.grpco_id',$ses_grp_id);
     $builder->groupBy('aictlyerp_grpcompacs_univdb.comp_id');	
	 $builder->orderBy('aictlyerp_grpcompacs_univdb.comp_id');	 
	 $response =  $builder->get()->getResultArray();
	 
	 $final_companies=array();	 
	 if($response){
		 foreach($response as $row){
			 $comp_id = $row['comp_id'];
			 $grpco_id = $row['grpco_id'];
			 $companyinfo = $this->getcompany_info($comp_id);
			 $final_companies[] = array(
					  'company_id'     => $comp_id,
                      'comp_code'   => $companyinfo['comp_code'],
					  'comp_fy_id'  => $companyinfo['comp_fy_id'],
					  'comp_name'  => $companyinfo['comp_name'],
					  'comp_short_name'  => $companyinfo['comp_short_name'],
					  );
			 
		 }
		 
	 }
	 return $final_companies;
 }
 
  function group_master_info($crs_master_id){	
	  $ses_grp_id = $this->session->get('ses_grp_id');  
	  return $this->erp_db->table('aictlyerp_grpcomstid_univdb')->where('crs_master_id', $crs_master_id)->where('grpco_id', $ses_grp_id)->get()->getRowArray();   	   
   }
   
   public function load_trial_balance($from_date,$to_date)
   {  
	  $master_final         = array(); 
	  $opening_balance      = array();
	  $comp_tooltip_balance = array();
	  $opening_cr=$opening_dr=0;
	  $ses_grp_id           = $this->session->get('ses_grp_id');  
	  $group_companies_list = $this->group_companies_list(); 
	 
	  
	  $groups_data =  $this->erp_db->table('aictlyerp_grpparentn_univdb')->orderBy('acc_grp_parent_id')->get()->getResultArray();
      foreach($groups_data as $grprow){
		  $acc_grp_parent_id = $grprow['acc_grp_parent_id'];
		  $group_name        = $grprow['grp_name'];
		   
		   $comp_credit_sum=0;
		   $comp_debit_sum=0;
		   
		   $comp_credit_opnsum=0;
		   $comp_debit_opnsum=0;
		   
							
		   $company_array=array();				
		   if($group_companies_list){
			foreach($group_companies_list as $grprow){
				
				$credit_total_comp=0;
				$debit_total_comp =0;
				$opn_credit_total_comp=0;
		        $opn_debit_total_comp=0;
		   
                 $company_id         =  $grprow['company_id'];
				 $comp_fy_id         =  $grprow['comp_fy_id'];
				 $comp_code          =  $grprow['comp_code'];
				 $comp_name          =  ucwords(strtolower($grprow['comp_name']));
				 $comp_short_name    =  ucwords(strtolower($grprow['comp_short_name']));
                 $company_name       =  $comp_name;
				 $extdb              =  $this->externaldb->single_company_db($comp_code);
				
		
				$builder = $this->erp_db->table('aictlyerp_grpmapping_univdb grpmp');
				$builder->join('aictlyerp_grpcomstid_univdb grpcom','grpcom.crs_master_id=grpmp.crs_master_id');
				$builder->select(array('grpmp.master_id','grpmp.crs_master_id','grpcom.crs_master_name'));
				$builder->where('grpmp.grpco_id', $ses_grp_id);
				$builder->where('grpmp.master_type','acc');	
				$builder->where('grpmp.crs_master_id >','0');
				$builder->where('grpmp.comp_id',$company_id);	
				$builder->where('grpmp.acc_grp_parent_id',$acc_grp_parent_id);
				$builder->orderBy('grpcom.crs_master_name');
				$accounts = $builder->get()->getResultArray();
				
				if($accounts){
					foreach ($accounts as $account) {
						$crs_master_id = $account['crs_master_id'];
						$group_id      = $account['master_id'];
						
						
						//check opening balance
						 $get_opn_balance_info = $this->acc_opn_balance_info($account['master_id'],$extdb,$company_id,$comp_fy_id);
						
						if($get_opn_balance_info){
						 $acc_opn_balance  = $get_opn_balance_info['acc_op_bal'];
						 if($acc_opn_balance<0){
							$opn_credit_total_comp +=abs($acc_opn_balance);
							$opening_cr+=abs($acc_opn_balance);
						 }
						 if($acc_opn_balance>0){
							$opn_debit_total_comp +=$acc_opn_balance;
							$opening_dr+=abs($acc_opn_balance);
						   }
						 } 
						
						//check last transaction
						$acc_txn_tbl = $company_id.'_accnttxnnn_'.$account['master_id'].'_'.$comp_fy_id;
								
					    $builder = $extdb->table($acc_txn_tbl);
						$builder->where('acc_id', $account['master_id']);
						$builder->where('acc_txn_date <=', $to_date);
						$builder->where('bo_id', $this->bo_id);
						$builder->orderBy('acc_txn_date', 'desc');
						$builder->orderBy('voucher_txn_id', 'desc');
						$builder->orderBy('acc_txn_id', 'desc');
						$builder->limit(1);
						$transaction = $builder->get()->getRowArray();
						
						if($transaction)
						{
							if($transaction['acc_bal'] < 0)
								$credit_total_comp += abs(parseAmount($transaction['acc_bal']));
							if($transaction['acc_bal'] >= 0)
								$debit_total_comp += parseAmount($transaction['acc_bal']);
						}
						else
						 {
							//check opening balance
						   if($get_opn_balance_info){
						      $acc_opn_balance  = $get_opn_balance_info['acc_op_bal'];
						   if($acc_opn_balance<0)
							  $credit_total_comp += abs(parseAmount($acc_opn_balance));
						   if($acc_opn_balance>0)
							  $debit_total_comp += parseAmount($acc_opn_balance);
						   }
						 } 	
									
					//echo ' => cr ='	.$credit_total_comp+$opn_credit_total_comp.' and dr ='.$debit_total_comp+$opn_debit_total_comp;
					//echo'<br>----<br>';
			       }
				} 
			 // bsd accounts
		    	$builder = $this->erp_db->table('aictlyerp_grpmapping_univdb grpmp');
				$builder->join('aictlyerp_grpcomstid_univdb grpcom','grpcom.crs_master_id=grpmp.crs_master_id');
				$builder->select(array('grpmp.master_id','grpmp.crs_master_id','grpcom.crs_master_name'));
				$builder->where('grpmp.grpco_id', $ses_grp_id);
				$builder->where('grpmp.master_type','bsd');	
				$builder->where('grpmp.crs_master_id >','0');
				$builder->where('grpmp.comp_id',$company_id);	
				$builder->where('grpmp.acc_grp_parent_id',$acc_grp_parent_id);
				$builder->orderBy('grpcom.crs_master_name');
				$bsd_accounts = $builder->get()->getResultArray();
				
				if($bsd_accounts){
					foreach ($bsd_accounts as $bsd_account) {
						$crs_master_id = $bsd_account['crs_master_id'];
						$group_id      = $bsd_account['master_id'];
						
						
						//check opening balance
						 $get_opn_balance_info = $this->sundry_opn_balance_info($bsd_account['master_id'],$extdb,$company_id,$comp_fy_id);
						
						if($get_opn_balance_info){
						 $acc_opn_balance  = $get_opn_balance_info['bsd_op_bal'];
						 if($acc_opn_balance<0){
							$opn_credit_total_comp +=abs($acc_opn_balance);
							$opening_cr+=abs($acc_opn_balance);
						 }
						 if($acc_opn_balance>0){
							$opn_debit_total_comp +=$acc_opn_balance;
							$opening_dr+=abs($acc_opn_balance);
						   }
						 } 
						
						//check last transaction
						$acc_txn_tbl = $company_id.'_sundrytxnn_'.$bsd_account['master_id'].'_'.$comp_fy_id;
						$builder = $extdb->table($acc_txn_tbl);
						$builder->where('bill_sundry_id', $bsd_account['master_id']);
						$builder->where('sundry_txn_date <=', $to_date);
						$builder->where('bo_id', $this->bo_id);
						$builder->orderBy('sundry_txn_date', 'desc');
						$builder->orderBy('voucher_txn_id', 'desc');
						$builder->orderBy('sundry_txn_id', 'desc');
						$builder->limit(1);
						$transaction = $builder->get()->getRowArray();
						if($transaction)
						{
							if($transaction['sundry_bal'] < 0)
								$credit_total_comp += abs(parseAmount($transaction['sundry_bal']));
							if($transaction['sundry_bal'] >= 0)
								$debit_total_comp += parseAmount($transaction['sundry_bal']);
						}
						else
						 {
							//check opening balance
						   if($get_opn_balance_info){
						      $acc_opn_balance  = $get_opn_balance_info['bsd_op_bal'];
						   if($acc_opn_balance<0)
							  $credit_total_comp += abs(parseAmount($acc_opn_balance));
						   if($acc_opn_balance>0)
							  $debit_total_comp += parseAmount($acc_opn_balance);
						   }
						 } 						
						}						
						
			       }		
				   
				   
				   
				   
				   
				   $credit =$debit=0;
			    $balance = parseAmount($credit_total_comp) - parseAmount($debit_total_comp);
						if($balance < 0)
							$credit = abs($balance);
						if($balance >= 0)
							$debit = $balance;
				$comp_credit_sum=$comp_credit_sum+$credit;
				$comp_debit_sum=$comp_debit_sum+$debit;
				
				$comp_credit_opnsum=$comp_credit_opnsum+$opn_credit_total_comp;
				$comp_debit_opnsum=$comp_debit_opnsum+$opn_debit_total_comp;
				
				if(isset($opening_balance[$company_id])){
					$opening_balance[$company_id]['cr'] +=  $opn_credit_total_comp;
					$opening_balance[$company_id]['dr'] +=  $opn_debit_total_comp;
				}else{
					$opening_balance[$company_id]['cr'] =  $opn_credit_total_comp;
					$opening_balance[$company_id]['dr'] =  $opn_debit_total_comp;
					$opening_balance[$company_id]['company_name'] =  $company_name;
				}
				
				
				
				$comp_tooltip_balance[$acc_grp_parent_id][$company_id]['dcr'] =  $credit;
			    $comp_tooltip_balance[$acc_grp_parent_id][$company_id]['ddr'] =  $debit;
			    $comp_tooltip_balance[$acc_grp_parent_id][$company_id]['company_name'] =  $company_name;
					
				
				
			    }
				
				
		   }
	
		
 $company_balance_tooltip='';
 $company_balance_tooltip .='<table style="width:100%;"><tr><td>COMPANY</td><td>DEBIT</td><td>CREDIT</td></tr>';
 if(isset($comp_tooltip_balance[$acc_grp_parent_id])){
	foreach($comp_tooltip_balance[$acc_grp_parent_id] as $comrow){
	  $company_balance_tooltip .= '<tr><td>'.$comrow['company_name'].'</td><td>'.parseAmount($comrow['ddr']).'</td><td>'.parseAmount($comrow['dcr']).'</td></tr>';			
	  }
   }
$company_balance_tooltip .= '</table>';
							
		   $master_final[] = [
							'group_id'	    => $acc_grp_parent_id,
							'group_name'	=> $group_name,
							'company'		=> '',
							'comp_id'       =>'',
							'credit'	    => !empty($comp_credit_sum) ? formatAmount($comp_credit_sum) : '',
							'debit'	        => !empty($comp_debit_sum) ? formatAmount($comp_debit_sum) : '',
							'credit_total'	=>$comp_credit_sum,
							'debit_total'	=> $comp_debit_sum,
							'type'       	=> 'acc',
							"pq_cellattr"   => array("credit"=>array("title"=>$company_balance_tooltip),"debit"=>array("title"=>$company_balance_tooltip))
							];
	
		
	  }	

	  
	$diff_opn_tooltip='';
    $diff_opn_tooltip .='<table style="width:100%;"><tr><td>COMPANY</td><td>DEBIT</td><td>CREDIT</td></tr>';
   if(isset($opening_balance)){
	foreach($opening_balance as $comopnrow){
		$op_balance = 0;
		  	$op_balance = parseAmount($comopnrow['dr']) - parseAmount($comopnrow['cr']);
		  	
	  		if($op_balance < 0)
	  			$credit = abs($op_balance);
	  		if($op_balance >= 0)
	  			$debit = $op_balance;
			
	  $diff_opn_tooltip .= '<tr><td>'.$comopnrow['company_name'].'</td><td>'.$debit.'</td><td>'.$credit.'</td></tr>';			
	  }
   }
     $diff_opn_tooltip .= '</table>';
	  
	 
	        $op_balance = 0;
		  	$op_balance = parseAmount($opening_dr) - parseAmount($opening_cr);
		  	
	  		if($op_balance < 0)
	  			$credit = abs($op_balance);
	  		if($op_balance >= 0)
	  			$debit = $op_balance;
	 $master_final[] = [
							'group_id'	    => 0,
							'group_name'	=> 'Difference In Opening',
							'company'		=> '',
							'comp_id'       =>'',
							'credit'	    => !empty($credit) ? formatAmount($credit) : '',
							'debit'	        => !empty($debit) ? formatAmount($debit) : '',
							'credit_total'	=>$credit,
							'debit_total'	=> $debit,
							'type'       	=> 'opn',
							"pq_cellattr"   => array("credit"=>array("title"=>$diff_opn_tooltip),"debit"=>array("title"=>$diff_opn_tooltip))
							]; 
    return $master_final;

   }
   
   
   public function load_trial_balance1($from_date,$to_date)
   {  
       $master_final=array();
	   $ses_grp_id = $this->session->get('ses_grp_id');  
	   $group_companies_list = $this->group_companies_list();
	    if($group_companies_list){
			foreach($group_companies_list as $grprow){
                 $company_id         =  $grprow['company_id'];
				 $comp_fy_id         =  $grprow['comp_fy_id'];
				 $comp_code          =  $grprow['comp_code'];
				 $comp_name          =  ucwords(strtolower($grprow['comp_name']));
				 $comp_short_name    =  ucwords(strtolower($grprow['comp_short_name']));
                 $company_name       =  $comp_name;				 
				 $extdb             = $this->externaldb->single_company_db($comp_code);
				
		$final = [];
		$credit_op_total = 0;
		$debit_op_total = 0;

	  	//check accounts
		
		$builder = $this->erp_db->table('aictlyerp_grpmapping_univdb grpmp');
		$builder->join('aictlyerp_grpcomstid_univdb grpcom','grpcom.crs_master_id=grpmp.crs_master_id');
		$builder->select(array('grpmp.master_id','grpmp.crs_master_id','grpcom.crs_master_name'));
		$builder->where('grpmp.grpco_id', $ses_grp_id);
		$builder->where('grpmp.master_type','acc');	
		$builder->where('grpmp.crs_master_id >','0');
		$builder->where('grpmp.comp_id',$company_id);	
		$builder->orderBy('grpcom.crs_master_name');
		$accounts = $builder->get()->getResultArray();
		if($accounts){
			foreach ($accounts as $account) {
                $crs_master_id = $account['crs_master_id'];
				$group_id      = $account['master_id'];
				$group_name    = $account['crs_master_name'];
				
		  		$credit = 0;
		  		$debit = 0;
		  		$credit_total = 0;
		  		$debit_total = 0;

		  		
		  		
				//check opening balance
				$get_opn_balance_info = $this->acc_opn_balance_info($account['master_id'],$extdb,$company_id,$comp_fy_id);
				if($get_opn_balance_info){
				 $acc_opn_balance  = $get_opn_balance_info['acc_op_bal'];
				 if($acc_opn_balance<0)
					$credit_op_total +=abs($acc_opn_balance);
				 if($acc_opn_balance>0)
				    $debit_op_total +=$acc_opn_balance;
				 }

				//check last transaction
				$acc_txn_tbl = $company_id.'_accnttxnnn_'.$account['master_id'].'_'.$comp_fy_id;
				$builder = $extdb->table($acc_txn_tbl);
		    	$builder->where('acc_id', $account['master_id']);
				$builder->where('bo_id', $this->bo_id);
				$builder->where('acc_txn_date <=', $to_date);
		    	$builder->orderBy('acc_txn_date', 'desc');
		    	$builder->orderBy('voucher_txn_id', 'desc');
		    	$builder->orderBy('acc_txn_id', 'desc');
		    	$builder->limit(1);
		    	$transaction = $builder->get()->getRowArray();
		    	if($transaction)
		    	{
		    		if($transaction['acc_bal'] < 0)
						$credit_total += abs(parseAmount($transaction['acc_bal']));
					if($transaction['acc_bal'] >= 0)
						$debit_total += parseAmount($transaction['acc_bal']);
		    	}
		    	else
		    	{
		    		//check opening balance
				 if($get_opn_balance_info){
				   $acc_opn_balance  = $get_opn_balance_info['acc_op_bal'];
				   if($acc_opn_balance<0)
					  $credit_total += abs(parseAmount($acc_opn_balance));
				   if($acc_opn_balance>0)
				      $debit_total += parseAmount($acc_opn_balance);
				 }
		    	}

		    	$balance = parseAmount($debit_total) - parseAmount($credit_total);
		  		if($balance < 0)
		  			$credit = abs($balance);
		  		if($balance >= 0)
		  			$debit = $balance;

		  		$final[] = [
		  			'group_id'	=> $group_id,
		  			'group_name'	=> $group_name,
		  			'company'		=> $company_name,
					'comp_id'       =>$company_id,
		  			'credit'	=> !empty($credit) ? formatAmount($credit) : '',
		  			'debit'	=> !empty($debit) ? formatAmount($debit) : '',
		  			'credit_total'	=> $credit,
		  			'debit_total'	=> $debit,
		  			'type'	=> 'acc',
					"pq_cellattr"	=> ''
					];
				}
			} 
		//check sundry accounts
		$builder1 = $this->erp_db->table('aictlyerp_grpmapping_univdb grpmp');
		$builder1->join('aictlyerp_grpcomstid_univdb grpcom','grpcom.crs_master_id=grpmp.crs_master_id');
		$builder1->select(array('grpmp.master_id','grpmp.crs_master_id','grpcom.crs_master_name'));
		$builder1->where('grpmp.grpco_id', $ses_grp_id);
		$builder1->where('grpmp.master_type','bsd');	
		$builder1->where('grpmp.crs_master_id >','0');
		$builder1->where('grpmp.comp_id',$company_id);	
		$builder1->orderBy('grpcom.crs_master_name');
		
		$sundry_accounts = $builder1->get()->getResultArray();
		//echo $this->erp_db->GetLastQuery();
		//echo'<br>';
		if($sundry_accounts){
			foreach ($sundry_accounts as $sundry_account) {

				$crs_master_id = $sundry_account['crs_master_id'];
				$group_name    = $sundry_account['crs_master_name'];
				
		  		$credit = 0;
		  		$debit = 0;
		  		$credit_total = 0;
		  		$debit_total = 0;

		  		

				//check opening balance
				$bill_sundry_op_balance = $this->bill_sundry_op_balance($sundry_account['master_id'],$extdb,$company_id,$comp_fy_id);
				if($bill_sundry_op_balance)
				{
					if($bill_sundry_op_balance['bsd_op_bal'] < 0)
						$credit_op_total += abs(parseAmount($bill_sundry_op_balance['bsd_op_bal']));
					if($bill_sundry_op_balance['bsd_op_bal'] >= 0)
						$debit_op_total += parseAmount($bill_sundry_op_balance['bsd_op_bal']);
				}

				//check last transaction
				$bs_txn_tbl = $company_id.'_sundrytxnn_'.$sundry_account['master_id'].'_'.$comp_fy_id;
				$builder = $extdb->table($bs_txn_tbl);
				$builder->where('bill_sundry_id', $sundry_account['master_id']);
				$builder->where('bo_id', $this->bo_id);
				$builder->where('sundry_txn_date <=', $to_date);
				$builder->orderBy('sundry_txn_date', 'desc');
				$builder->orderBy('sundry_txn_id', 'desc');
				$builder->limit(1);
				$transaction = $builder->get()->getRowArray();
				if($transaction)
		    	{
		    		if($transaction['sundry_bal'] < 0)
						$credit_total += abs(parseAmount($transaction['sundry_bal']));
					if($transaction['sundry_bal'] >= 0)
						$debit_total += parseAmount($transaction['sundry_bal']);
		    	}
		    	else
		    	{
		    		//check opening balance
					if($bill_sundry_op_balance)
					{
						if($bill_sundry_op_balance['bsd_op_bal'] < 0)
							$credit_total += abs(parseAmount($bill_sundry_op_balance['bsd_op_bal']));
						if($bill_sundry_op_balance['bsd_op_bal'] >= 0)
							$debit_total += parseAmount($bill_sundry_op_balance['bsd_op_bal']);
					}
		    	}

					$balance = parseAmount($debit_total) - parseAmount($credit_total);
					if($balance < 0)
						$credit = abs($balance);
					if($balance >= 0)
						$debit = $balance;

					$final[] = [
						'group_id'	    => $group_id,
						'group_name'    => $group_name,
						'comp_id'       =>$company_id,
						'company'	    => $company_name,
						'credit'	    => !empty($credit) ? formatAmount($credit) : '',
						'debit'	        => !empty($debit) ? formatAmount($debit) : '',
						'credit_total'  => $credit,
						'debit_total'	=> $debit,
						'type'	        => 'bsd',
						"pq_cellattr"	=> ''
					];
				  }
			    }			
			
			$credit = $debit = 0;
			$op_balance = 0;
			
			//echo $company_name .'==>'.parseAmount($debit_op_total).' -'. parseAmount($credit_op_total);
			//echo '<br>';

			$op_balance = parseAmount($debit_op_total) - parseAmount($credit_op_total);
			if($op_balance < 0)
				$debit = abs($op_balance);
			if($op_balance >= 0)
				$credit = $op_balance;

				$final[] = [
					'group_id'		=> 0,
					'group_name'	=> 'Difference in Opening',
					'company'	    => $company_name,
					'credit'		=> !empty($credit) ? formatAmount($credit) : '',
					'debit'			=> !empty($debit) ? formatAmount($debit) : '',
					'credit_total'	=> $credit,
					'debit_total'	=> $debit,
					'type'			=> 'opn',
					"pq_cellattr"	=> ''				
				   ];			
				
			   $master_final[]=	$final;		
			   
			   
	        }
			$final_list =array();		
			foreach($master_final as $mkey => $subarray){	  
			    foreach($subarray as $skey => $row){				
				    $final_list[]=$row;	
					}
		
	         }	
		}
	
	return $final_list;
		}
	

   public function load_trial_balance2()
   {
	   $master_final=array();
	   $ses_grp_id = $this->session->get('ses_grp_id');  
	   $group_companies_list = $this->group_companies_list();
	    if($group_companies_list){
			foreach($group_companies_list as $grprow){
                 $company_id         =  $grprow['company_id'];
				 $comp_fy_id         =  $grprow['comp_fy_id'];
				 $comp_code          =  $grprow['comp_code'];
				 $comp_name          =  ucwords(strtolower($grprow['comp_name']));
				 $comp_short_name    =  ucwords(strtolower($grprow['comp_short_name']));
                 $company_name       =  $comp_name.'('.$comp_short_name.')';				 
				  $extdb             = $this->externaldb->single_company_db($comp_code);
				  
				
		$final = [];
		$credit_op_total = 0;
		$debit_op_total = 0;

	  	//check accounts
		
		$builder = $this->erp_db->table('aictlyerp_grpmapping_univdb grpmp');
		$builder->join('aictlyerp_grpcomstid_univdb grpcom','grpcom.crs_master_id=grpmp.crs_master_id');
		$builder->select(array('grpmp.master_id','grpmp.crs_master_id','grpcom.crs_master_name'));
		$builder->where('grpmp.grpco_id', $ses_grp_id);
		$builder->where('grpmp.master_type','acc');	
		$builder->where('grpmp.crs_master_id >','0');
		$builder->where('grpmp.comp_id',$company_id);	
		$builder->orderBy('grpcom.crs_master_name');
		$accounts = $builder->get()->getResultArray();
		if($accounts){
			foreach ($accounts as $account) {
                $crs_master_id = $account['crs_master_id'];
				$group_id      = $account['master_id'];
				$group_name    = $account['crs_master_name'];
				
		  		$credit = 0;
		  		$debit = 0;
		  		$credit_total = 0;
		  		$debit_total = 0;

		  		
		  		
				//check opening balance
				$get_opn_balance_info = $this->acc_opn_balance_info($account['master_id'],$extdb,$company_id,$comp_fy_id);
				if($get_opn_balance_info){
				 $acc_opn_balance  = $get_opn_balance_info['acc_op_bal'];
				 if($acc_opn_balance<0)
					$credit_op_total +=abs($acc_opn_balance);
				 if($acc_opn_balance>0)
				    $debit_op_total +=$acc_opn_balance;
				 }

		    	
		    		//check opening balance
				 if($get_opn_balance_info){
				   $acc_opn_balance  = $get_opn_balance_info['acc_op_bal'];
				   if($acc_opn_balance<0)
					  $credit_total += abs(parseAmount($acc_opn_balance));
				   if($acc_opn_balance>0)
				      $debit_total += parseAmount($acc_opn_balance);
				 }
		    	

		    	$balance = parseAmount($debit_total) - parseAmount($credit_total);
		  		if($balance < 0)
		  			$credit = abs($balance);
		  		if($balance >= 0)
		  			$debit = $balance;

		  		$final[] = [
		  			'group_id'	=> $group_id,
		  			'group_name'	=> $group_name,
		  			'company'		=> $company_name,
					'comp_id'       =>$company_id,
		  			'credit'	=> !empty($credit) ? formatAmount($credit) : '',
		  			'debit'	=> !empty($debit) ? formatAmount($debit) : '',
		  			'credit_total'	=> $credit,
		  			'debit_total'	=> $debit,
		  			'type'	=> 'acc',
					"pq_cellattr"	=> ''
					];
				}
			} 
		//check sundry accounts
		$builder1 = $this->erp_db->table('aictlyerp_grpmapping_univdb grpmp');
		$builder1->join('aictlyerp_grpcomstid_univdb grpcom','grpcom.crs_master_id=grpmp.crs_master_id');
		$builder1->select(array('grpmp.master_id','grpmp.crs_master_id','grpcom.crs_master_name'));
		$builder1->where('grpmp.grpco_id', $ses_grp_id);
		$builder1->where('grpmp.master_type','bsd');	
		$builder1->where('grpmp.crs_master_id >','0');
		$builder1->where('grpmp.comp_id',$company_id);	
		$builder1->orderBy('grpcom.crs_master_name');
		
		$sundry_accounts = $builder1->get()->getResultArray();
		//echo $this->erp_db->GetLastQuery();
		//echo'<br>';
		if($sundry_accounts){
			foreach ($sundry_accounts as $sundry_account) {

				$crs_master_id = $sundry_account['crs_master_id'];
				$group_name    = $sundry_account['crs_master_name'];
				
		  		$credit = 0;
		  		$debit = 0;
		  		$credit_total = 0;
		  		$debit_total = 0;

		  		

				//check opening balance
				$bill_sundry_op_balance = $this->bill_sundry_op_balance($sundry_account['master_id'],$extdb,$company_id,$comp_fy_id);
				if($bill_sundry_op_balance)
				{
					if($bill_sundry_op_balance['bsd_op_bal'] < 0)
						$credit_op_total += abs(parseAmount($bill_sundry_op_balance['bsd_op_bal']));
					if($bill_sundry_op_balance['bsd_op_bal'] >= 0)
						$debit_op_total += parseAmount($bill_sundry_op_balance['bsd_op_bal']);
				}

		    	
		    		//check opening balance
					if($bill_sundry_op_balance)
					{
						if($bill_sundry_op_balance['bsd_op_bal'] < 0)
							$credit_total += abs(parseAmount($bill_sundry_op_balance['bsd_op_bal']));
						if($bill_sundry_op_balance['bsd_op_bal'] >= 0)
							$debit_total += parseAmount($bill_sundry_op_balance['bsd_op_bal']);
					}
		    	

					$balance = parseAmount($debit_total) - parseAmount($credit_total);
					if($balance < 0)
						$credit = abs($balance);
					if($balance >= 0)
						$debit = $balance;

					$final[] = [
						'group_id'	    => $group_id,
						'group_name'    => $group_name,
						'comp_id'       =>$company_id,
						'company'	    => $company_name,
						'credit'	    => !empty($credit) ? formatAmount($credit) : '',
						'debit'	        => !empty($debit) ? formatAmount($debit) : '',
						'credit_total'  => $credit,
						'debit_total'	=> $debit,
						'type'	        => 'bsd',
						"pq_cellattr"	=> ''
					];
				  }
			    }			
			
			$credit = $debit = 0;
			$op_balance = 0;
			
			//echo $company_name .'==>'.parseAmount($debit_op_total).' -'. parseAmount($credit_op_total);
			//echo '<br>';

			$op_balance = parseAmount($debit_op_total) - parseAmount($credit_op_total);
			if($op_balance < 0)
				$debit = abs($op_balance);
			if($op_balance >= 0)
				$credit = $op_balance;

				$final[] = [
					'group_id'		=> 0,
					'group_name'	=> 'Difference in Opening',
					'company'	    => $company_name,
					'credit'		=> !empty($credit) ? formatAmount($credit) : '',
					'debit'			=> !empty($debit) ? formatAmount($debit) : '',
					'credit_total'	=> $credit,
					'debit_total'	=> $debit,
					'type'			=> 'opn',
				    "pq_cellattr"	=> ''					
				   ];			
				
			   $master_final[]=	$final;		
			   
			   
	        }
			$final_list =array();		
			foreach($master_final as $mkey => $subarray){	  
			    foreach($subarray as $skey => $row){				
				    $final_list[]=$row;	
					}
		
	         }	
		}
	
	return $final_list;
	
   }

   function get_voucher_narration_info($voucher_txn_id,$narr_type,$txn_id){
	 if($narr_type=='long'){
        $long_narr_tbl = $this->company_id.'_long_narrn_'.$this->session->get('ses_comp_fy_id');
		return $this->db->table($long_narr_tbl)->where('vch_txn_id',$voucher_txn_id)->get()->getRowArray();
		
	 }
	else  if($narr_type=='short'){
        $short_narr_tbl = $this->company_id.'_short_narr_'.$this->session->get('ses_comp_fy_id');
		return $this->db->table($short_narr_tbl)->where('txn_id',$txn_id)->where('vch_txn_id',$voucher_txn_id)->get()->getRowArray();
	}		
	  
  }
 
 
  public function remove_exceptions_items($accttxnoth_tbl,$voucher_txn_id,$acc_oth_txn_ids){
      $acctcrsref_tbl = $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
      $itemtxnoth_tbl = $this->company_id.'_itemtxnoth_'.$this->session->get('ses_comp_fy_id');
      
      if($acc_oth_txn_ids){
          $acc_oth_txn_ids  = explode(",",$acc_oth_txn_ids);
          if($acc_oth_txn_ids){
              foreach($acc_oth_txn_ids as $acc_oth_txn_id){
                 $this->db->table($accttxnoth_tbl)->where('acc_oth_txn_id',$acc_oth_txn_id)->where('comp_id',$this->company_id)->where('acc_oth_txn_tag','recycled')->delete(); 
                $this->db->table($acctcrsref_tbl)->where('acct_txn_id',$acc_oth_txn_id)->where('comp_id',$this->company_id)->delete();  
              }
          }
      }
    
    // check if voucher has items sale , purchase in ward, delivery etc.
    
    if($voucher_txn_id){
      $this->db->table($itemtxnoth_tbl)->where('voucher_txn_id',$voucher_txn_id)->where('comp_id',$this->company_id)->where('item_oth_txn_tag','recycled')->delete();   
    }
    
  }
 
 
  public function get_exception_txn_items($company_id,$acct_txn_id,$voucher_type_id){
       
	    $comp_id         = $this->session->get('ses_company_id');
	    $base_url        = base_url().'/'.getenv('AdminPath');
	    $item_master_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id'); 
	    $builder         = $this->db->table($item_master_tbl); 
       // $total_Records = $builder->countAll();
        if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
            
         $offset = ($pq_curPage > 1) ? ($pq_rPP * ($pq_curPage - 1)) : 0;
         
         $next_txn_id = $acct_txn_id+1;
         $acc_oth_txn_id =array($acct_txn_id,$next_txn_id);
         
         $builder->orderBy('acc_oth_txn_date');    
          $builder->whereIn('acc_oth_txn_id', $acc_oth_txn_id);
	 	 $builder->where('comp_id', $comp_id);
	 	 $builder->where('acc_oth_txn_tag', 'recycled');
		 $result = $builder->get()->getResultArray();
		 
		

		 if($result){
		     foreach($result as $values){
		         
		          $account_info = $this->account_info($values['acc_id']);
		          
		          $account_name = $account_info['acc_name'];  
		          
		         if($values['acc_oth_txn_drcr']=='d'){
		             $debit  = $values['acc_oth_txn_amount']; 
		             $credit = 0;
		         }
		         else{
		           $debit   =  0;
		           $credit  =  $values['acc_oth_txn_amount']; 
		             
		         }
		         $deletion_date = date('d M,Y',strtotime($values['acc_oth_txn_duedate']));
		         
		         $records[] = array(	
		                          'date'           => date('d M,Y',strtotime($values['acc_oth_txn_date'])),
    							  'voucher_no'    => $values['comp_vch_series_no'],
    							  'particulars'    => $account_name,
    							  'debit'          => $debit,
    							  'credit'         => $credit,
    							  'acc_oth_txn_id' => $values['acc_oth_txn_id'],
    							  'voucher_txn_id' => $values['voucher_txn_id'],
    							  'voucher_type_id' => $values['comp_vch_name']
    			                 );    
		            }
		         }
        
           echo  "{\"totalRecords\":" . count($records) . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($records)."}"; 
        
     }
   
   public function get_tracked_items($item_id,$txn_id){
	  $itemtrackn_tbl = $this->company_id.'_itemtrackn_'.$this->session->get('ses_comp_fy_id'); 
	  $builder        = $this->db->table($itemtrackn_tbl);   
	  $builder->where('item_id', $item_id);
	  $builder->where('tracking_no !=','');
	  $builder->where('txn_id', $txn_id);
	  $result = $builder->get()->getResultArray();	  
	  return 	$result	; 
   }   
   
   
   public function get_item_tracking_report($item_id,$from_date,$to_date){  
        $pq_curPage = (int)$_POST["pq_curpage"];
        $limit     = (int)$_POST["pq_rpp"];
		$records   = array();
		
        $comp_id         = $this->session->get('ses_company_id');
	    $base_url        = base_url().'/'.getenv('AdminPath');   
      // item tracking item wise
      if($item_id >0){
		 $itemtxnnnn_tbl = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id'); 
	    $builder         = $this->db->table($itemtxnnnn_tbl); 
       // $total_Records = $builder->countAll();
        if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
            
         $offset = ($pq_curPage > 1) ? ($pq_rPP * ($pq_curPage - 1)) : 0;
         
         $builder->orderBy('item_txn_date');    
         $builder->where('item_txn_date >=', $from_date);
		 $builder->where('item_txn_date <=', $to_date);
	 	 $builder->where('comp_id', $comp_id);
	 	 $builder->where('item_id', $item_id);
		 $result = $builder->get()->getResultArray();
         $total_tracked=0;	
         $item_total_qty=0;		 
		 if($result){
		     foreach($result as $values){
		               $item_total_qty  =  $item_total_qty+$values['item_txn_qty'];				  
				        $item_tracking_details = $this->get_tracked_items($item_id,$values['txn_id']);
						$item_info        = $this->get_item_info($values['item_id'],$comp_id);
						if($item_tracking_details){
							  $total_tracked    = $total_tracked+count($item_tracking_details);
							  $total_untracked  = ($item_total_qty-$total_tracked);		          
							  
							  $records[$values['item_id']] = array(	
											  'item_id'       => $values['item_id'],
											   'txn_id'       => $values['txn_id'],
											  'item_name'     => $item_info['item_name'],
											  'tracked_txn'   => $total_tracked,
											  'untracked_txn' => $total_untracked,
                                              'from_date'     => $from_date,
                                              'to_date'       => $to_date												  
											 );  
														
								} 
						else{
							$records[$values['item_id']] = array(	
											  'item_id'       => $values['item_id'],
											   'txn_id'       => $values['txn_id'],
											  'item_name'     => $item_info['item_name'],
											  'tracked_txn'   => 0,
											  'untracked_txn' => $item_total_qty,
											  'from_date'     => $from_date,
                                              'to_date'       => $to_date											  
											  
											 );    
							
						   }  
									
			            }
		            }  
			$all_rec=array();
            if($records){
				foreach($records as $row){
					$all_rec[]=$row;
				}
			}			
			$total_Records = count($all_rec);
			
			$offset = ($limit * ($pq_curPage - 1));
			if ($offset > $total_Records)
			  {        
			   $pq_curPage = ceil($total_Records / $limit);
			   $offset = ($limit * ($pq_curPage - 1));
			  }
          
           $final_records = array_slice( $all_rec, $offset, $limit );	
					
			echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($final_records)."}"; 		
	        }
		else{
		$records =array();	
		if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            }             
         $offset = ($pq_curPage > 1) ? ($pq_rPP * ($pq_curPage - 1)) : 0;
		 
		 $matrcntr_dropdown =  $this->matrcntr_dropdown();	
			// all items tracking report
		 $itemtrackn_tbl = $this->company_id.'_itemtrackn_'.$this->session->get('ses_comp_fy_id'); 
	     $builder         = $this->db->table($itemtrackn_tbl);
         $builder->where('tracking_no !=','');                 
         $builder->orderBy('tracking_id'); 
		 $result = $builder->get()->getResultArray();         
		 if($result){
		     foreach($result as $values){
		               	$item_info        = $this->get_item_info($values['item_id'],$comp_id);
						
						 
						 
						 $itemtxnnnn_tbl = $this->company_id.'_itemtxnnnn_'.$values['item_id'].'_'.$this->session->get('ses_comp_fy_id');
	                     $itrrow = $this->db->table($itemtxnnnn_tbl)->where('txn_id',$values['txn_id'])->where('comp_id',$comp_id)->get()->getRowArray();
									
						 $mc_name         = $matrcntr_dropdown[$itrrow['mat_cent_id']];
						 $records[] = array(	
											  'item_id'       => $values['item_id'],
											  'txn_id'        => $values['txn_id'],
											  'item_name'     => $item_info['item_name'],
											  'track_mc'      => $mc_name,
											  'track_no'      => $values['tracking_no'],
											  'from_date'     => $from_date,
                                              'to_date'       => $to_date		
											 ); 
			            }
		            }  
			$all_rec=array();
            if($records){
				foreach($records as $row){
					$all_rec[]=$row;
				}
			}			
			$total_Records = count($all_rec);
			
			$offset = ($limit * ($pq_curPage - 1));
			if ($offset > $total_Records)
			  {        
			   $pq_curPage = ceil($total_Records / $limit);
			   $offset = ($limit * ($pq_curPage - 1));
			  }
          
           $final_records = array_slice( $all_rec, $offset, $limit );	
					
			echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($final_records)."}"; 		
	        	
			
			
		}	
        
     } 
	 
    public function get_item_tracking_ledger_report($item_id,$from_date,$to_date){ 
         $matrcntr_dropdown =  $this->matrcntr_dropdown();	
        $pq_curPage = (int)$_POST["pq_curpage"];
        $limit     = (int)$_POST["pq_rpp"];
		
        $comp_id         = $this->session->get('ses_company_id');
	    $base_url        = base_url().'/'.getenv('AdminPath');   
      
		$itemtxnnnn_tbl = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id'); 
	    $builder         = $this->db->table($itemtxnnnn_tbl); 
        if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
            
         $offset = ($pq_curPage > 1) ? ($pq_rPP * ($pq_curPage - 1)) : 0;
         
         $builder->orderBy('item_txn_date');    
         $builder->where('item_txn_date >=', $from_date);
		 $builder->where('item_txn_date <=', $to_date);
	 	 $builder->where('comp_id', $comp_id);
	 	 $builder->where('item_id', $item_id);
		 $result = $builder->get()->getResultArray();
        		 
		 if($result){
		     foreach($result as $values){
		             $item_total_qty  =  $values['item_txn_qty'];		
					 $item_txn_drcr	  =  $values['item_txn_drcr'];	
					 if($item_txn_drcr=='d'){
						 $drqty=$values['item_txn_qty'];
						 $crqty ='';
					 }
					 else if($item_txn_drcr=='c'){
						 $drqty='';
						 $crqty =$values['item_txn_qty']; 
					 }
					 
				     $item_info       =  $this->get_item_info($values['item_id'],$comp_id);
					 
					 $mc_name         = $matrcntr_dropdown[$values['mat_cent_id']];
							  
				     $records[] = array(	
											  'item_track_date' => date('d-m-Y',strtotime($values['item_txn_date'])), 
											  'item_id'         => $values['item_id'],
											  'txn_id'          => $values['txn_id'],
											  'item_name'       => $item_info['item_name'],
											  'tracked_upc'     => $item_info['item_upc'],
											  'tracked_mc'      => $mc_name,
											  'tracked_drqty'   => $drqty,
											  'tracked_crqty'   => $crqty,
                                              'from_date'       => $from_date,
                                              'to_date'         => $to_date												  
											 );  
						
								
			            }
		            }  
					
			$all_rec=array();
            if($records){
				foreach($records as $row){
					$all_rec[]=$row;
				}
			}			
			$total_Records = count($all_rec);
			
			$offset = ($limit * ($pq_curPage - 1));
			if ($offset > $total_Records)
			  {        
			   $pq_curPage = ceil($total_Records / $limit);
			   $offset = ($limit * ($pq_curPage - 1));
			  }
          
           $final_records = array_slice( $all_rec, $offset, $limit );	
					
			echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($final_records)."}"; 		
	    
     }
  
 
  public function exceptions_txns_list(){
        $voucherMasterTypes = VoucherMasterTypes();
	    $comp_id         = $this->session->get('ses_company_id');
	    $base_url        = base_url().'/'.getenv('AdminPath');
	    $item_master_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id'); 
	    $builder         = $this->db->table($item_master_tbl); 
       // $total_Records = $builder->countAll();
        if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
            
         
         
         $builder->orderBy('acc_oth_txn_date');                
	 	 $builder->where('comp_id', $comp_id);
	 	 $builder->where('acc_oth_txn_tag', 'recycled');
		 $result = $builder->get()->getResultArray();
		 $ff=array();
		 foreach($result as $key => $r1){
		     $ff[$r1['voucher_txn_id']][]=$r1;
		 }
		 
		 $fresult =array();
		 foreach($ff as $kk =>$rr){
		     if(isset($rr[0]))
		              $fresult[]=   $rr[0];
		 }
         $records=array(); 
		 if($fresult){
		     foreach($fresult as $values){
		         
		          $account_info = $this->account_info($values['acc_id']);
		          
		          $account_name = $account_info['acc_name'];  
		          
		         if($values['acc_oth_txn_drcr']=='d'){
		             $debit  = $values['acc_oth_txn_amount']; 
		             $credit = 0;
		         }
		         else{
		           $debit   =  0;
		           $credit  =  $values['acc_oth_txn_amount']; 
		             
		         }
		         $deletion_date = date('d M,Y',strtotime($values['acc_oth_txn_duedate']));
		         
		         $records[] = array(	
		                          'checkbox'      => '<input name="voucher_ids[]" class="checkbox hidden voucher_row"  data-id="'.$values['acc_oth_txn_id'].'||'.$values['voucher_txn_id'].'"  type="checkbox" value="'.$values['acc_oth_txn_id'].','.($values['acc_oth_txn_id']+1).'||'.$values['voucher_txn_id'].'">',
			                      'date'           => date('d M,Y',strtotime($values['acc_oth_txn_date'])),
    							  'voucher_type'   => $voucherMasterTypes[$values['comp_vch_name']],
    							  'particulars'    => $account_name,
    							  'debit'          => $debit,
    							  'credit'         => $credit,
    							  'deletion_date'  => $deletion_date,
    							  'acc_oth_txn_id' => $values['acc_oth_txn_id'],
    							  'voucher_txn_id' => $values['voucher_txn_id'],
    							  'voucher_type_id' => $values['comp_vch_name']
    			                 );    
		            }
		         }
		         
		         
		  	$total_Records = count($records);
		  	
		  	$offset = ($pq_rPP * ($pq_curPage - 1));
        if ($offset > $total_Records)
          {        
           $pq_curPage = ceil($total_Records / $pq_rPP);
           $offset = ($pq_rPP * ($pq_curPage - 1));
          }
          
          
        $final_records = array_slice( $records, $offset, $pq_rPP );
        echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($final_records)."}";
        
        
        
        //   echo  "{\"totalRecords\":" . count($records) . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($records)."}"; 
        
     }
    
        
  function save_cons_trail($acc_oth_txn_id,$txn_id,$account_id,$comp_id,$voucher_txn_id){
          //get vhtxnconso table 
    	   $vhtxnconso_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	       
		   if($this->session->get('ses_boid')!='')
			$conso_row      = $this->db->table($vhtxnconso_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('voucher_txn_id',$voucher_txn_id)->where('comp_id',$comp_id)->get()->getRowArray(); 
		else
		   $conso_row      = $this->db->table($vhtxnconso_tbl)->where('voucher_txn_id',$voucher_txn_id)->where('comp_id',$comp_id)->get()->getRowArray(); 
	       
	       if($conso_row){
			   if(isset($conso_row["document_ref_no"])){
				$conso_data =  [
    	                           ["acct_txn_id"=>$acc_oth_txn_id,"comp_id"=>$comp_id,"txn_id"=>$txn_id,"voucher_txn_id"=>$voucher_txn_id,"acc_cross_ref_type"=>"vchr_conso_series_id",
    	                            "acc_cross_ref_data"=>$conso_row["comp_vch_series_id"],"acc_cross_logdate"=>date("Y-m-d")
    	                           ],
    	                              
    	                           ["acct_txn_id"=>$acc_oth_txn_id,"comp_id"=>$comp_id,"txn_id"=>$txn_id,"voucher_txn_id"=>$voucher_txn_id,"acc_cross_ref_type"=>"vchr_conso_vch_no",
    	                            "acc_cross_ref_data"=>$conso_row["comp_vch_no"],"acc_cross_logdate"=>date("Y-m-d")
    	                            ], 
    	                               
    	                           ["acct_txn_id"=>$acc_oth_txn_id,"comp_id"=>$comp_id,"txn_id"=>$txn_id,"voucher_txn_id"=>$voucher_txn_id,"acc_cross_ref_type"=>"vchr_conso_voucher_type_id",
    	                            "acc_cross_ref_data"=>$conso_row["voucher_type_id"],"acc_cross_logdate"=>date("Y-m-d")
    	                            ], 
    	                               
    	                           ["acct_txn_id"=>$acc_oth_txn_id,"comp_id"=>$comp_id,"txn_id"=>$txn_id,"voucher_txn_id"=>$voucher_txn_id,"acc_cross_ref_type"=>"vchr_conso_voucher_date",
    	                             "acc_cross_ref_data"=>$conso_row["voucher_date"],"acc_cross_logdate"=>date("Y-m-d")
    	                            ], 
    	                               
    	                            ["acct_txn_id"=>$acc_oth_txn_id,"comp_id"=>$comp_id,"txn_id"=>$txn_id,"voucher_txn_id"=>$voucher_txn_id,"acc_cross_ref_type"=>"vchr_conso_mat_cent_id",
    	                              "acc_cross_ref_data"=>$conso_row["mat_cent_id"],"acc_cross_logdate"=>date("Y-m-d")
    	                            ], 
    	                              ["acct_txn_id"=>$acc_oth_txn_id,"comp_id"=>$comp_id,"txn_id"=>$txn_id,"voucher_txn_id"=>$voucher_txn_id,"acc_cross_ref_type"=>"vchr_conso_document_ref_no",
    	                              "acc_cross_ref_data"=>$conso_row["document_ref_no"],"acc_cross_logdate"=>date("Y-m-d")
    	                             ],
	                              ];   
			   }
			   else{
	           $conso_data =  [
    	                           ["acct_txn_id"=>$acc_oth_txn_id,"comp_id"=>$comp_id,"txn_id"=>$txn_id,"voucher_txn_id"=>$voucher_txn_id,"acc_cross_ref_type"=>"vchr_conso_series_id",
    	                            "acc_cross_ref_data"=>$conso_row["comp_vch_series_id"],"acc_cross_logdate"=>date("Y-m-d")
    	                           ],
    	                              
    	                           ["acct_txn_id"=>$acc_oth_txn_id,"comp_id"=>$comp_id,"txn_id"=>$txn_id,"voucher_txn_id"=>$voucher_txn_id,"acc_cross_ref_type"=>"vchr_conso_vch_no",
    	                            "acc_cross_ref_data"=>$conso_row["comp_vch_no"],"acc_cross_logdate"=>date("Y-m-d")
    	                            ], 
    	                               
    	                           ["acct_txn_id"=>$acc_oth_txn_id,"comp_id"=>$comp_id,"txn_id"=>$txn_id,"voucher_txn_id"=>$voucher_txn_id,"acc_cross_ref_type"=>"vchr_conso_voucher_type_id",
    	                            "acc_cross_ref_data"=>$conso_row["voucher_type_id"],"acc_cross_logdate"=>date("Y-m-d")
    	                            ], 
    	                               
    	                           ["acct_txn_id"=>$acc_oth_txn_id,"comp_id"=>$comp_id,"txn_id"=>$txn_id,"voucher_txn_id"=>$voucher_txn_id,"acc_cross_ref_type"=>"vchr_conso_voucher_date",
    	                             "acc_cross_ref_data"=>$conso_row["voucher_date"],"acc_cross_logdate"=>date("Y-m-d")
    	                            ], 
    	                               
    	                            ["acct_txn_id"=>$acc_oth_txn_id,"comp_id"=>$comp_id,"txn_id"=>$txn_id,"voucher_txn_id"=>$voucher_txn_id,"acc_cross_ref_type"=>"vchr_conso_mat_cent_id",
    	                              "acc_cross_ref_data"=>$conso_row["mat_cent_id"],"acc_cross_logdate"=>date("Y-m-d")
    	                            ], 
    	                            
    	                         
									
	                              ];
			   }
	                           
	          $acctcrsref_tbl = $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
    	      $this->db->table($acctcrsref_tbl)->insertBatch($conso_data); 
	         
	          }
	          
	      // get vhtxntrail    
	      $vhtxntrail_tbl = $this->company_id.'_vhtxntrail_'.$this->session->get('ses_comp_fy_id');
	      $vhtxntrail_row = $this->db->table($vhtxntrail_tbl)->where('txn_id',$txn_id)->where('voucher_txn_id',$voucher_txn_id)->where('comp_id',$comp_id)->get()->getRowArray();   
	      if($vhtxntrail_row){
	          $vhtxntrail_data =  ["acct_txn_id"=>$acc_oth_txn_id,"comp_id"=>$comp_id,"txn_id"=>$txn_id,"voucher_txn_id"=>$voucher_txn_id,"acc_cross_ref_type"=>"vchr_trail_txn_trail_id",
    	                            "acc_cross_ref_data"=>$vhtxntrail_row["comp_vch_txn_trail_id"],"acc_cross_logdate"=>date("Y-m-d")
    	                           ];
    	      $this->db->table($acctcrsref_tbl)->insert($vhtxntrail_data); 
	       
	         }
	         
	         
      
  }    
    
  function get_save_account_txn_data($txn_id,$account_id,$comp_id,$voucher_txn_id,$voucher_type_id){
       $accnttxnnn_tbl = $this->company_id.'_accnttxnnn_'.$account_id.'_'.$this->session->get('ses_comp_fy_id');
	   $row            = $this->db->table($accnttxnnn_tbl)->where('txn_id',$txn_id)->where('comp_id',$comp_id)->get()->getRowArray(); 
       if($row){
          $data        = array('comp_id'=>$row['comp_id'],'acc_oth_txn_date'=>$row['acc_txn_date'],'acc_oth_txn_amount'=>$row['acc_txn_amount'],'acc_oth_txn_drcr'=>$row['acc_txn_drcr'],
                               'acc_oth_txn_narr'=>$row['acc_txn_narr'],'voucher_type_id'=>$voucher_type_id,'comp_vch_series_no'=>$row['comp_vch_series_no'],'acc_id'=>$row['acc_id'],
                               'voucher_txn_id'=>$row['voucher_txn_id'],'acc_oth_txn_status'=>'','bo_id'=>$this->session->get('ses_boid'),'acc_oth_txn_duedate'=>date('Y-m-d'),
                               'acc_oth_txn_tag'=>'recycled'); 
          $accttxnoth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
    	  $this->db->table($accttxnoth_tbl)->insert($data);    
           return $this->db->insertID();    
         }
    }  
  
   function get_save_item_txn_data($txn_id,$item_id,$comp_id,$voucher_txn_id,$voucher_type_id){
       $itemtxnnnn_tbl = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
	   $row = $this->db->table($itemtxnnnn_tbl)->where('txn_id',$txn_id)->where('comp_id',$comp_id)->get()->getRowArray(); 
       
       if($row){
          $data= array( 'comp_id'=>$row['comp_id'],'item_oth_txn_date'=>$row['item_txn_date'],'item_oth_txn_amount'=>$row['item_txn_amount'],'item_oth_txn_drcr'=>$row['item_txn_drcr'],
                        'item_oth_txn_qty'=>$row['item_txn_qty'],'item_oth_txn_narr'=>$row['item_txn_narr'],'comp_vch_series_no'=>$row['comp_vch_series_no'],'item_id'=>$row['item_id'],
                        'voucher_txn_id'=>$row['voucher_txn_id'],'bo_id'=>$this->session->get('ses_boid'),'txn_id'=>$row['txn_id'],'mat_cent_id'=>$row['mat_cent_id'],'voucher_type_id'=>$row['voucher_type_id'],'item_oth_txn_tag'=>'recycled'); 
          $accttxnoth_tbl = $this->company_id.'_itemtxnoth_'.$this->session->get('ses_comp_fy_id');
    	  $this->db->table($accttxnoth_tbl)->insert($data);    
    	  return $this->db->insertID();
    	  
       }
  }
   
   function get_comp_txn_items($voucher_txn_id,$comp_id,$comp_vch_series_id){
        $comptxnmst_master = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	    return  $this->db->table($comptxnmst_master)->where('voucher_txn_id',$voucher_txn_id)->where('comp_id',$comp_id)->where('comp_vch_series_id',$comp_vch_series_id)->get()->getResultArray(); 
    }
   
   	function get_item_info($item_id,$comp_id){	 
	  $item_master_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($item_master_tbl)->where('item_id', $item_id)->where('comp_id', $comp_id)->get()->getRowArray();   	   
    }
    
    function get_company_items(){
        
        $searchtext        = $_GET['term'];
	   $item_master_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
	   $data =  $this->db->table($item_master_tbl)->where('( `item_name` LIKE  "%'.$searchtext.'%" OR `item_alias` LIKE  "%'.$searchtext.'%" OR `product_id` LIKE  "%'.$searchtext.'%") ')->where('comp_id', $this->company_id)->orderBy('item_name','ASC')->get()->getResultArray();

	   $final_result      = array();
	   if($data){
		  foreach($data as $row){
               $final_result[]    = array("label"=>ucwords($row['item_name']),"value" => ucwords($row['item_name']),"id"=>$row['item_id']);	
		      }
        }
	    return json_encode($final_result);  
     }
     function get_company_mc(){
		 $comp_mtcnt_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
	     $data =  $this->db->table($comp_mtcnt_tbl)->where('comp_id', $this->company_id)->orderBy('mat_cent_name','ASC')->get()->getResultArray();
	     $final_result      = array();
	     if($data){
		  foreach($data as $row){
              $name =$this->enc_string->nc_string($row['mat_cent_name'],'de');	
              $final_result[] = [
                    'label' => ucwords($name),
                    'value' => ucwords($name),
                    'id'    => $row['mat_cent_id']
                  ];
	        }
        }
	  return json_encode($final_result);	
     }
    
	public function item_txn_info($item_id,$txn_id){
	  $itemtxnnnn_tbl = $this->company_id.'_itemtxnnnn_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
	  return  $this->db->table($itemtxnnnn_tbl)->where('txn_id', $txn_id)->where('comp_id', $this->company_id)->get()->getRowArray();
	}
	
	public function itmunitmst_list(){
	$itmunitmst_tbl = $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');
	$result =  $this->db->table($itmunitmst_tbl)->where('comp_id', $this->company_id)->get()->getResultArray();
	 
	 $final_units = array();
	  if($result){
		  foreach($result as $row){
			    $item_unit_name = $this->enc_string->nc_string($row['item_unit'],'de');
			    $final_units[$row['unit_id']] =$item_unit_name; 				  
		     }		  
	      }
	    return $final_units;
		
		
	}
	
	public function GetUnqUnits($item_id){
	  $itemtxnbal_tbl = $this->company_id.'_itemtxnbal_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');
	  $builder        =  $this->db->table($itemtxnbal_tbl); 
      $builder->select('DISTINCT(item_unit) as item_unit');    		
      $builder->where('item_id', $item_id);
	  $result      = $builder->get()->getResultArray();
	  $final_units = array();
	  if($result){
		  foreach($result as $row){
			    $final_units[$row['item_unit']] =0; 			  
		     }		  
	      }
	    return $final_units;
	}
	
	public function all_matrcntr_dropdown(){
		$comp_id = $this->company_id;
		$comp_mtcnt_tbl = $comp_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
		$data =  $this->db->table($comp_mtcnt_tbl)
						  ->where('comp_id', $comp_id)
						  ->orderBy('mat_cent_name','ASC')
						  ->get()->getResultArray();

		$final_result      = array();		
		if($data){
			foreach($data as $row){
				$final_result[$row['mat_cent_id']] =$this->enc_string->nc_string($row['mat_cent_name'],'de');			   
			}
		}
		return $final_result;	
	}
     
	public function mcgrp_mc_lists($mc_grp_id){
	  $mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
	  $builder        =  $this->db->table($mcmasternn_tbl); 
      $builder->select('mat_cent_id');    		
      $builder->where('mat_cent_grp_id', $mc_grp_id);
	  $result      = $builder->get()->getResultArray();
	  $final_mcids = array();
	  if($result){
		  foreach($result as $row){
			    $final_mcids[$row['mat_cent_id']] = $row['mat_cent_id'];		  
		     }		  
	      }
	    return $final_mcids;	
		
		
	}
	
   public function groups_dropdown(){
	    $comp_id = $this->company_id;
	   	$account_master_tbl = $comp_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
	    $data =  $this->db->table($account_master_tbl)->where('comp_id', $comp_id)->orderBy('acc_grp_name','ASC')->get()->getResultArray();
	    $final_result      = array();
	    $final_result['']  = 'Choose';
	    if($data){
		  foreach($data as $row){
		       $account_id        = $row['acc_grp_id'];
		       $account_name      = $row['acc_grp_name'];
               $final_result[$account_id] = $account_name;	
		    }
        }
	  return $final_result;
   }
  
   public function accounts_dropdown(){
	    $comp_id = $this->company_id;
	   	$account_master_tbl = $comp_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	    $data =  $this->db->table($account_master_tbl)->where('comp_id', $comp_id)->orderBy('acc_name','ASC')->get()->getResultArray();
	    $final_result      = array();
	    $final_result['']  = 'Choose';
	    if($data){
		  foreach($data as $row){
		       $account_id        = $row['acc_id'];
		       $account_name      = $row['acc_name'];
               $final_result[$account_id]    = $account_name;	
		    }
        }
	  return $final_result;
   }
   
   public function items_dropdown(){
	    $comp_id         = $this->company_id;
	   	$item_master_tbl = $comp_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
	    $data =  $this->db->table($item_master_tbl)->where('comp_id', $comp_id)->orderBy('item_name','ASC')->get()->getResultArray();
	    $final_result      = array();
	    if($data){
		  foreach($data as $row){
		       $item_id                = $row['item_id'];
		       $item_name              = $row['item_name'];
               $final_result[$item_id] = $item_name;	
		    }
        }
	  return $final_result;
   }

   function item_list(){
   	     $ses_grp_id           = $this->session->get('ses_grp_id');
	    $builder = $this->erp_db->table('aictlyerp_grpmapping_univdb grpmp');
		$builder->join('aictlyerp_grpcomstid_univdb grpcom','grpcom.crs_master_id=grpmp.crs_master_id');
		$builder->select(array('grpmp.master_id as id','grpcom.crs_master_name as value','grpcom.crs_master_name as label'));
		$builder->where('grpmp.grpco_id', $ses_grp_id);
		$builder->where('grpmp.master_type','itm');	
		$builder->where('grpmp.crs_master_id >','0');
		$builder->orderBy('grpcom.crs_master_name');
		$data = $builder->get()->getResultArray();	
		return $data;
   }
   
   
   
   function matrcntr_grp_dropdown(){
 $final_result      = array();
		/*  $mcgrpmstnn_tbl = $this->company_id.'_mcgrpmstnn_'.$this->session->get('ses_comp_fy_id');
	     $data =  $this->db->table($mcgrpmstnn_tbl)->where('comp_id', $this->company_id)->orderBy('mc_grp_name','ASC')->get()->getResultArray();
	    
	     if($data){
		  foreach($data as $row){
              $final_result[$row['mc_grp_id']] =$this->enc_string->nc_string($row['mc_grp_name'],'de');			   
	        }
        }
	  */	 return $final_result;
     }
	 
    function matrcntr_dropdown(){
		$ses_grp_id           = $this->session->get('ses_grp_id');
	    $builder = $this->erp_db->table('aictlyerp_grpmapping_univdb grpmp');
		$builder->join('aictlyerp_grpcomstid_univdb grpcom','grpcom.crs_master_id=grpmp.crs_master_id');
		$builder->select(array('grpmp.master_id','grpcom.crs_master_name'));
		$builder->where('grpmp.grpco_id', $ses_grp_id);
		$builder->where('grpmp.master_type','mcmst');	
		$builder->where('grpmp.crs_master_id >','0');
		$builder->orderBy('grpcom.crs_master_name');
		$data = $builder->get()->getResultArray();
		$final_result      = array();
	     if($data){
		  foreach($data as $row){
              $final_result[$row['master_id']] =$row['crs_master_name'];			   
	        }
        }
	  return $final_result;	
     }
	
   function batches_dropdown(){
		 $comp_itmbatchmt_tbl = $this->company_id.'_itmbatchmt_'.$this->session->get('ses_comp_fy_id');
	     $data =  $this->db->table($comp_itmbatchmt_tbl)->orderBy('batch_no','ASC')->get()->getResultArray();
	     $final_result      = array();
	     if($data){
		  foreach($data as $row){
              $final_result[$row['batch_id']] = $row['batch_no'];
	        }
        }
	  return $final_result;	
     }	
	 
   
    function get_units_info($unit_id,$comp_id){	 
	 $item_unit_master_tbl = $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($item_unit_master_tbl)->where('unit_id', $unit_id)->where('comp_id', $comp_id)->get()->getRowArray();   	   
    }   
   
 public function stock_status_bw_report(){
	    $batches_dropdown =  $this->batches_dropdown();
	    $this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
	    $comp_id         = $this->company_id;
        $frmdate         = $_POST['frmdate'];
        $item_master_tbl = $comp_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id'); 
	    $builder         = $this->db->table($item_master_tbl); 
       
        if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
         $offset = ($pq_curPage > 1) ? ($pq_rPP * ($pq_curPage - 1)) : 0;
         $builder->orderBy('item_name');                
	 	 $builder->where('comp_id', $comp_id);
	 	 $result     = $builder->get()->getResultArray();
		 $final_list = array();
		 $batch_array = array();
         if($result){
		  foreach($result as $key => $row){
		       $item_id            =  $row['item_id'];		       
		       $itemtxnbal_table   =  $comp_id.'_itemtxnbal_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');	 
    		   $builder            =  $this->db->table($itemtxnbal_table); 
    	  	   $builder->orderBy('itemtxnbal_id');
    		   $builder->where('item_id', $item_id);
			   $builder->where('batch_id >','0');
    	   	   $builder->where("DATE_FORMAT(`item_txn_date`, '%Y-%m-%d')<=", date('Y-m-d',strtotime($frmdate)));
			   $builder->groupBy('batch_id');
    		   $txnresult = $builder->get()->getResultArray();	
			   if($txnresult){
    		       foreach($txnresult as $values){
    		           $batchid = $values['batch_id'];
					   $batch_array[$batchid][] = $values['item_id'];	
    		       }    		       
    		   }   	   
			 
		    }
        }
		
		$result_list=array();
		$items_array=array();		
		foreach($batch_array as $batchid => $mcrow){
			 $batch_name =  $batches_dropdown[$batchid]; 
			  $result_list[]=array("batchid"=>$batchid,"batch_name"=>$batch_name,"no_of_items"=>count($mcrow));
		}
		$total_Records = count($result_list);
        $final_records = array_slice( $result_list, $offset, $pq_rPP );
        echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($final_records)."}";
    }

	
   public function stock_status_mc_report(){
	    $matrcntr_dropdown =  $this->matrcntr_dropdown();
	    $this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
	    $comp_id         = $this->company_id;
        $frmdate         = $_POST['frmdate'];
        $item_master_tbl = $comp_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id'); 
	    $builder         = $this->db->table($item_master_tbl); 
       
        if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
         $offset = ($pq_curPage > 1) ? ($pq_rPP * ($pq_curPage - 1)) : 0;
         $builder->orderBy('item_name');                
	 	 $builder->where('comp_id', $comp_id);
	 	 $result     = $builder->get()->getResultArray();
		 $final_list = array();
		 $matcentr_array = array();
         if($result){
		  foreach($result as $key => $row){
		       $item_id            =  $row['item_id'];		       
		       $itemtxnbal_table   =  $comp_id.'_itemtxnbal_'.$item_id.'_'.$this->session->get('ses_comp_fy_id');	 
    		   $builder            =  $this->db->table($itemtxnbal_table); 
    	  	   $builder->orderBy('itemtxnbal_id');
    		   $builder->where('item_id', $item_id);
			   if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
			
    	   	   $builder->where("DATE_FORMAT(`item_txn_date`, '%Y-%m-%d')<=", date('Y-m-d',strtotime($frmdate)));
			   $builder->groupBy('mat_cent_id');
    		   $txnresult = $builder->get()->getResultArray();			   
    		   if($txnresult){
    		       foreach($txnresult as $values){
    		           $matcentr = $values['mat_cent_id'];
					   $matcentr_array[$matcentr][] = $values['item_id'];	
    		       }    		       
    		   }   	   
			 
		    }
        }
		  
		$result_list=array();
		$items_array=array();		
		foreach($matcentr_array as $mc_id => $mcrow){
			 $material_centre =  $matrcntr_dropdown[$mc_id]; 
			  $result_list[]=array("mc_id"=>$mc_id,"material_centre"=>$material_centre,"no_of_items"=>count($mcrow),"item_value"=>"");
		}
		$total_Records = count($result_list);
        $final_records = array_slice( $result_list, $offset, $pq_rPP );
        echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($final_records)."}";
    }	
  
  
    function account_info($account_id){	 
       $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
       return $this->db->table($account_master_tbl)->where('acc_id', $account_id)->get()->getRowArray();   	   
    }
    
  
  
    public function load_cash_bank_book($pq_curPage, $limit, $from_date, $to_date)
    {
       $voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	    $voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	    $voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
	    
	    $comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	    $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	    
	    $builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    
	    $builder->join($comp_txn_tbl, $comp_txn_tbl.'.voucher_txn_id ='.$voucher_tbl.'.voucher_txn_id AND '.$comp_txn_tbl.'.master_id_type = "acc"');
	    $builder->select($comp_txn_tbl.'.master_id, '.$comp_txn_tbl.'.txn_id');
	    

	    $builder->join($account_master_tbl, $account_master_tbl.'.acc_id ='.$comp_txn_tbl.'.master_id AND ('.$account_master_tbl.'.acc_grp_id = "23" OR '.$account_master_tbl.'.acc_grp_id = "21" )'); // Cash and cash equivlants 
	    $builder->select($account_master_tbl.'.acc_name');
	    
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	    
		$builder->where($voucher_tbl.'.comp_id', $this->company_id);
		if($this->session->get('ses_boid')!='')
          $builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));
	
		
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}
		$builder->whereNotIn($voucher_tbl.'.voucher_type_id', [12,17,19,21]);
		$total_Records = $builder->countAllResults();
		
		$offset = ($limit * ($pq_curPage - 1));

            if ($offset > $total_Records)
            {        
                $pq_curPage = ceil($total_Records / $limit);
                $offset = ($limit * ($pq_curPage - 1));
            }
            
            
	    
	    $builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    
	    $builder->join($comp_txn_tbl, $comp_txn_tbl.'.voucher_txn_id ='.$voucher_tbl.'.voucher_txn_id AND '.$comp_txn_tbl.'.master_id_type = "acc"', 'left');
	    $builder->select($comp_txn_tbl.'.master_id, '.$comp_txn_tbl.'.txn_id');
	    
	    $builder->join($account_master_tbl, $account_master_tbl.'.acc_id ='.$comp_txn_tbl.'.master_id AND ('.$account_master_tbl.'.acc_grp_id = "23" OR '.$account_master_tbl.'.acc_grp_id = "21" )'); // Cash and cash equivlants 
	    
	    $builder->select($account_master_tbl.'.acc_name');
	    
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	    
		$builder->where($voucher_tbl.'.comp_id', $this->company_id);
		if($this->session->get('ses_boid')!='')
          $builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}
		$builder->whereNotIn($voucher_tbl.'.voucher_type_id', [12,17,19,21]); //sale order, purchase order, quotation, requisition
		$builder->orderBy($voucher_tbl.'.voucher_date');
		$builder->orderBy($voucher_tbl.'.voucher_txn_id');
	 	$builder->limit($limit,$offset);   // *
		$result = $builder->get()->getResultArray();
		

        $data = [];
        foreach($result as $key => $value)
        {
            $date = date("d-m-Y", strtotime($value['voucher_date']));
            $account_name = $value['acc_name'];

            $payment = '';
            $receipt = '';
            $balance = '';
            $narration = '';
                
            $table = $this->company_id.'_accnttxnnn_'.$value['master_id'].'_'.$this->session->get('ses_comp_fy_id');
            $builder = $this->db->table($table)->select($table.'.*');
				$builder->where($table.'.txn_id', $value['txn_id']);
				if($this->session->get('ses_boid')!='')
				$builder->where($table.'.bo_id', $this->session->get('ses_boid'));
            $account = $builder->get()->getRowArray();
            
            if($account)
            {
            	if($account['acc_txn_drcr'] == 'c'){
	                $payment       = formatAmount($account['acc_txn_amount']);
	            }
	            if($account['acc_txn_drcr'] == 'd'){
	                $receipt        = formatAmount($account['acc_txn_amount']);
	            }

	            $balance = formatAmount($account['acc_bal']);

	            $narr = $this->get_voucher_narration_info($value['voucher_txn_id'],'long',$value['txn_id']);
	            if($narr){
	            	$narration = $narr['vch_narr'];
	            	$narration = strlen($narration) > 25 ? substr($narration , 0, 22).'...' : $narration;
	            }
            	
            }

            $data[] = [
                    'receipt'        => $receipt,
                    'payment'        => $payment,
                    'account'        => $account_name,
                    'date'           => $date,
                    'balance'        => $balance,
                    'narration'      => $narration,
                    'voucher_no'     => $value['comp_vch_no'],
                    'voucher_type'   => $value['comp_vch_type'],
                    'voucher_type_id'=> $value['voucher_type_id'],
                    'voucher_txn_id' => $value['voucher_txn_id']
                ];
  
        }
        
        // echo "<pre>";print_r($data);exit;
        // return $data;
        echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($data)."}";
    }
	
	public function get_bom_info($voucher_txn_id){
		 $acctcrsref_tbl      =  $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
		 $billofmatn_tbl      =  $this->company_id.'_billofmatn_'.$this->session->get('ses_comp_fy_id');
		 $builder = $this->db->table($billofmatn_tbl.' bomtbl');
		 $builder->select('bomtbl.*');
		 $builder->join($acctcrsref_tbl.' acctcrsref','acctcrsref.acc_cross_ref_data=bomtbl.bom_id','left');
         $builder->where('acctcrsref.acc_cross_ref_type','bom_id'); 
		 $builder->where('acctcrsref.voucher_txn_id',$voucher_txn_id);
		 $builder->where('acctcrsref.comp_id',$this->company_id);
		 if($this->session->get('ses_boid')!='')
			$builder->where('acctcrsref.bo_id', $this->session->get('ses_boid'));
		 $bom_result = $builder->get()->getRowArray();
		 
		
		 $builder2 = $this->db->table($acctcrsref_tbl);
         $builder2->like('acc_cross_ref_type','bom_batches','before'); 
		 $builder2->where('voucher_txn_id',$voucher_txn_id);
		 if($this->session->get('ses_boid')!='')
			$builder2->where('bo_id', $this->session->get('ses_boid'));
		 $builder2->where('comp_id',$this->company_id);
		 $bom_batch_result = $builder2->get()->getRowArray();
		
		 $final_result= array();
		 if($bom_batch_result)
		   $final_result['bom_batches']= $bom_batch_result['acc_cross_ref_data'];
		 else
		   $final_result['bom_batches']= 1;
	   
		 if($bom_result){
			 $final_result['bom_name']= $bom_result['bom_name'];
			 $final_result['bom_id']= $bom_result['bom_id'];			
		 }
		
	  
		 return $final_result;
	}
	
	
    function production_credit_debit_result($voucher_txn_id){
	
	    $comp_id         = $this->company_id;
	    $comptxnmst_tble = $comp_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');	   
		$final_d         = array();$credit=$debit=0;
		$items           = $this->db->table($comptxnmst_tble)->where('master_id_type','itm')->where('voucher_txn_id',$voucher_txn_id)->where('comp_id',$comp_id)->get()->getResultArray();	   
		 if($items){
			 foreach($items as $item_row){
				 
				  $itemtxnntable = $comp_id.'_itemtxnnnn_'.$item_row['master_id'].'_'.$this->session->get('ses_comp_fy_id');
				  $builder = $this->db->table($itemtxnntable);
				  $builder->where('voucher_txn_id',$voucher_txn_id);
				  if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
		
				  $result_txn = $builder->get()->getResultArray();
				  if($result_txn){
					  foreach($result_txn as $txnrow){
						   if($txnrow['item_txn_drcr'] == 'c'){
                             $credit       = $credit+$txnrow['item_txn_amount'];
							}
							if($txnrow['item_txn_drcr'] == 'd'){
								$debit        = $debit+$txnrow['item_txn_amount'];
							}
							
					  }
				  }		
				 
			 }			 
			 
		 } 
	  return array('cr'=>$credit,'dr'=>$debit);
	}
	
	
		public function load_ltst_voucher_txn($pq_curPage, $limit, $offset, $from_date,$voucher_type_id)
    {
        $voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	    $voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	    $voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
	    $comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		$acctcrsref_tbl      =  $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
	    

		$builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	    
		$builder->where($voucher_tbl.'.comp_id', $this->company_id);
		if($this->session->get('ses_boid')!='')
		$builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));

		if($voucher_type_id!='0')
		$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);
		if($from_date!='0'){
		    $from_date =  date('Y-m-d', strtotime($from_date)); 
			$builder->where('voucher_date <=', $from_date);
		}
		
		$builder->orderBy('voucher_date','desc');
		$builder->orderBy('voucher_txn_id','desc');
		$builder->limit($limit);  
		$result = $builder->get()->getResultArray();

		$data = [];
		foreach($result as $key => $value)
        {
        	$voucher_txn_id  = $value['voucher_txn_id'];
        	$voucher_type_id = $value['voucher_type_id'];
        	$particulars = '';
        	$credit = '';
        	$debit = '';
        	$bom_id = 0;
        	$bom_batches = 0;

        	if($voucher_type_id == '18') //Sales
        	{
        		$builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $voucher_txn_id);
                $builder->where('master_id_type', 'acc');
                $builder->limit(1);
                $txn_result = $builder->get()->getRowArray();

                $account_info = $this->account_info($txn_result['master_id']);
					 $particulars  = $account_info['acc_name'];

                $table = $this->company_id.'_accnttxnnn_'.$txn_result['master_id'].'_'.$this->session->get('ses_comp_fy_id');
                $builder = $this->db->table($table);
    		     $builder->where('txn_id', $txn_result['txn_id']);
				 if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
					 
					 
                $account = $builder->get()->getRowArray();
                if($account){
                    if($account['acc_txn_drcr'] == 'c'){
                        $credit = formatAmount($account['acc_txn_amount']);
                    }
                    if($account['acc_txn_drcr'] == 'd'){
                        $debit = formatAmount($account['acc_txn_amount']);
                    }
                }
        	}
        	if($voucher_type_id == '11') //Purchase
        	{
        		$builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $voucher_txn_id);
                $builder->where('master_id_type', 'acc');
                $builder->limit(1);
                $txn_result = $builder->get()->getRowArray();

                $account_info = $this->account_info($txn_result['master_id']);
				$particulars  = $account_info['acc_name'];

                $table = $this->company_id.'_accnttxnnn_'.$txn_result['master_id'].'_'.$this->session->get('ses_comp_fy_id');
                $builder = $this->db->table($table);
    			$builder->where('txn_id', $txn_result['txn_id']);
				if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
                $account = $builder->get()->getRowArray();
                if($account){
                    if($account['acc_txn_drcr'] == 'c'){
                        $credit = formatAmount($account['acc_txn_amount']);
                    }
                    if($account['acc_txn_drcr'] == 'd'){
                        $debit = formatAmount($account['acc_txn_amount']);
                    }
                }
        	}

        	if($voucher_type_id == '2') //Credit Note
        	{
        		$builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $voucher_txn_id);
                $builder->where('master_id_type', 'acc');
                $builder->limit(1);
                $txn_result = $builder->get()->getRowArray();

                $account_info = $this->account_info($txn_result['master_id']);
				$particulars  = $account_info['acc_name'];

                $table = $this->company_id.'_accnttxnnn_'.$txn_result['master_id'].'_'.$this->session->get('ses_comp_fy_id');
                $builder = $this->db->table($table);
    			$builder->where('txn_id', $txn_result['txn_id']);
				if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
                $account = $builder->get()->getRowArray();
                if($account){
                    if($account['acc_txn_drcr'] == 'c'){
                        $credit = formatAmount($account['acc_txn_amount']);
                    }
                    if($account['acc_txn_drcr'] == 'd'){
                        $debit = formatAmount($account['acc_txn_amount']);
                    }
                }
        	}
        	if($voucher_type_id == '3') //Debit Note
        	{
        		$builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $voucher_txn_id);
                $builder->where('master_id_type', 'acc');
                $builder->limit(1);
                $txn_result = $builder->get()->getRowArray();

                $account_info = $this->account_info($txn_result['master_id']);
				$particulars  = $account_info['acc_name'];

                $table = $this->company_id.'_accnttxnnn_'.$txn_result['master_id'].'_'.$this->session->get('ses_comp_fy_id');
                $builder = $this->db->table($table);
    			$builder->where('txn_id', $txn_result['txn_id']);
				if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
                $account = $builder->get()->getRowArray();
                if($account){
                    if($account['acc_txn_drcr'] == 'c'){
                        $credit = formatAmount($account['acc_txn_amount']);
                    }
                    if($account['acc_txn_drcr'] == 'd'){
                        $debit = formatAmount($account['acc_txn_amount']);
                    }
                }
        	}
        	if($voucher_type_id == '7') //Material Out
        	{
        		$builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $voucher_txn_id);
                $builder->where('master_id_type', 'acc');
                $builder->limit(1);
                $txn_result = $builder->get()->getRowArray();

                $account_info = $this->account_info($txn_result['master_id']);
				$particulars  = $account_info['acc_name'];

				$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
				$builder = $this->db->table($table);
    			$builder->where('txn_id', $txn_result['txn_id']);
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
		
                $account = $builder->get()->getRowArray();
                if($account){
                    if($account['acc_oth_txn_drcr'] == 'c'){
                        $credit = formatAmount($account['acc_oth_txn_amount']);
                    }
                    if($account['acc_oth_txn_drcr'] == 'd'){
                        $debit = formatAmount($account['acc_oth_txn_amount']);
                    }
                }
        	}
        	if($voucher_type_id == '6') //Material In
        	{
        		$builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $voucher_txn_id);
                $builder->where('master_id_type', 'acc');
                $builder->limit(1);
                $txn_result = $builder->get()->getRowArray();

                $account_info = $this->account_info($txn_result['master_id']);
				$particulars  = $account_info['acc_name'];

				$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
				$builder = $this->db->table($table);
    			$builder->where('txn_id', $txn_result['txn_id']);
				if($this->session->get('ses_boid')!='')
			   $builder->where('bo_id', $this->session->get('ses_boid'));
                $account = $builder->get()->getRowArray();
                if($account){
                    if($account['acc_oth_txn_drcr'] == 'c'){
                        $credit = formatAmount($account['acc_oth_txn_amount']);
                    }
                    if($account['acc_oth_txn_drcr'] == 'd'){
                        $debit = formatAmount($account['acc_oth_txn_amount']);
                    }
                }
        	}

        	if($voucher_type_id == '19') //Sales Order
        	{
        		$builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $voucher_txn_id);
                $builder->where('master_id_type', 'acc');
                $builder->limit(1);
                $txn_result = $builder->get()->getRowArray();

                $account_info = $this->account_info($txn_result['master_id']);
				$particulars  = $account_info['acc_name'];

				$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
				$builder = $this->db->table($table);
    			$builder->where('txn_id', $txn_result['txn_id']);
				if($this->session->get('ses_boid')!='')
		    	 $builder->where('bo_id', $this->session->get('ses_boid'));
		
                $account = $builder->get()->getRowArray();
                if($account){
                    if($account['acc_oth_txn_drcr'] == 'c'){
                        $credit = formatAmount($account['acc_oth_txn_amount']);
                    }
                    if($account['acc_oth_txn_drcr'] == 'd'){
                        $debit = formatAmount($account['acc_oth_txn_amount']);
                    }
                }
        	}
        	if($voucher_type_id == '12') //Purchase Order
        	{
        		$builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $voucher_txn_id);
                $builder->where('master_id_type', 'acc');
                $builder->limit(1);
                $txn_result = $builder->get()->getRowArray();

                $account_info = $this->account_info($txn_result['master_id']);
				$particulars  = $account_info['acc_name'];

				$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
				$builder = $this->db->table($table);
    			$builder->where('txn_id', $txn_result['txn_id']);
				if($this->session->get('ses_boid')!='')
		     	$builder->where('bo_id', $this->session->get('ses_boid'));
		
				
                $account = $builder->get()->getRowArray();
                if($account){
                    if($account['acc_oth_txn_drcr'] == 'c'){
                        $credit = formatAmount($account['acc_oth_txn_amount']);
                    }
                    if($account['acc_oth_txn_drcr'] == 'd'){
                        $debit = formatAmount($account['acc_oth_txn_amount']);
                    }
                }
        	}
        	if($voucher_type_id == '17') //Quotation
        	{
        		$builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $voucher_txn_id);
                $builder->where('master_id_type', 'acc');
                $builder->limit(1);
                $txn_result = $builder->get()->getRowArray();

                $account_info = $this->account_info($txn_result['master_id']);
				$particulars  = $account_info['acc_name'];

				$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
				$builder = $this->db->table($table);
    			$builder->where('txn_id', $txn_result['txn_id']);
				if($this->session->get('ses_boid')!='')
		    	$builder->where('bo_id', $this->session->get('ses_boid'));
		
                $account = $builder->get()->getRowArray();
                if($account){
                    if($account['acc_oth_txn_drcr'] == 'c'){
                        $credit = formatAmount($account['acc_oth_txn_amount']);
                    }
                    if($account['acc_oth_txn_drcr'] == 'd'){
                        $debit = formatAmount($account['acc_oth_txn_amount']);
                    }
                }
        	}
        	if($voucher_type_id == '21') //Purchase Requisition
        	{
        		$builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $voucher_txn_id);
                $builder->where('master_id_type', 'acc');
                $builder->limit(1);
                $txn_result = $builder->get()->getRowArray();

                $account_info = $this->account_info($txn_result['master_id']);
				$particulars  = $account_info['acc_name'];

				$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
				$builder = $this->db->table($table);
    			$builder->where('txn_id', $txn_result['txn_id']);
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
		
                $account = $builder->get()->getRowArray();
                if($account){
                    if($account['acc_oth_txn_drcr'] == 'c'){
                        $credit = formatAmount($account['acc_oth_txn_amount']);
                    }
                    if($account['acc_oth_txn_drcr'] == 'd'){
                        $debit = formatAmount($account['acc_oth_txn_amount']);
                    }
                }
        	}
        	if($voucher_type_id == '9') //payment cr
        	{
        		$builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $voucher_txn_id);
                $builder->where('master_id_type', 'acc');
                $txn_result = $builder->get()->getResultArray();

                $credit_total = 0;
                $debit_total = 0;
                foreach ($txn_result as $key2 => $value2) {
                	$table = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
                    $builder = $this->db->table($table)->select($table.'.*');
        			$builder->where($table.'.txn_id', $value2['txn_id']);
					if($this->session->get('ses_boid')!='')
						$builder->where($table.'.bo_id', $this->session->get('ses_boid'));
		
                    $account = $builder->get()->getRowArray();
                    if($account){
                        if($account['acc_txn_drcr'] == 'c'){
                        	$account_info  = $this->account_info($value2['master_id']);
									$particulars  .= $account_info['acc_name'] . ',';
                            $credit_total += $account['acc_txn_amount'];
                        }
                        // if($account['acc_txn_drcr'] == 'd'){
                        // 	$account_info  = $this->account_info($txn_result['master_id']);
						// 	$particulars  .= $account_info['acc_name'] . ', ';
                        //     $debit_total  += $account['acc_txn_amount'];
                        // }
                    }
                }

                if($credit_total > 0)
                	$credit = formatAmount($credit_total);
                if($debit_total > 0)
                	$debit = formatAmount($debit_total);

                $particulars = rtrim($particulars,',');
        	//	$particulars = strlen($particulars) > 25 ? substr($particulars , 0, 22).'...' : $particulars;
        	}
        	if($voucher_type_id == '13') //receipt dr
        	{
        		$builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $voucher_txn_id);
                $builder->where('master_id_type', 'acc');
                $txn_result = $builder->get()->getResultArray();

                $credit_total = 0;
                $debit_total = 0;
                foreach ($txn_result as $key2 => $value2) {
                	$table = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
                    $builder = $this->db->table($table)->select($table.'.*');
        			$builder->where($table.'.txn_id', $value2['txn_id']);
					if($this->session->get('ses_boid')!='')
					$builder->where($table.'.bo_id', $this->session->get('ses_boid'));
		
                    $account = $builder->get()->getRowArray();
                    if($account){
                        // if($account['acc_txn_drcr'] == 'c'){
                        // 	$account_info  = $this->account_info($txn_result['master_id']);
						// 	$particulars  .= $account_info['acc_name'] . ', ';
                        //     $credit_total += $account['acc_txn_amount'];
                        // }
                        if($account['acc_txn_drcr'] == 'd'){
                        	$account_info  = $this->account_info($value2['master_id']);
							$particulars  .= $account_info['acc_name'] . ',';
                            $debit_total  += $account['acc_txn_amount'];
                        }
                    }
                }

                if($credit_total > 0)
                	$credit = formatAmount($credit_total);
                if($debit_total > 0)
                	$debit = formatAmount($debit_total);

                $particulars = rtrim($particulars,',');
        	//	$particulars = strlen($particulars) > 25 ? substr($particulars , 0, 22).'...' : $particulars;
        	}
        	if($voucher_type_id == '1') //contra
        	{
        		$builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $voucher_txn_id);
                $builder->where('master_id_type', 'acc');
                $txn_result = $builder->get()->getResultArray();

                $credit_total = 0;
                $debit_total = 0;
                foreach ($txn_result as $key2 => $value2) {
                	$table = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
                    $builder = $this->db->table($table)->select($table.'.*');
        			$builder->where($table.'.txn_id', $value2['txn_id']);
					if($this->session->get('ses_boid')!='')
					$builder->where($table.'.bo_id', $this->session->get('ses_boid'));
				
                    $account = $builder->get()->getRowArray();
                    if($account){
                        if($account['acc_txn_drcr'] == 'c'){
                        	$account_info  = $this->account_info($value2['master_id']);
							$particulars  .= $account_info['acc_name'] . ',';
                            $credit_total += $account['acc_txn_amount'];
                        }
                        if($account['acc_txn_drcr'] == 'd'){
                        	$account_info  = $this->account_info($value2['master_id']);
							$particulars  .= $account_info['acc_name'] . ',';
                            $debit_total  += $account['acc_txn_amount'];
                        }
                    }
                }

                if($credit_total > 0)
                	$credit = formatAmount($credit_total);
                if($debit_total > 0)
                	$debit = formatAmount($debit_total);

                $particulars = rtrim($particulars,',');
        	//	$particulars = strlen($particulars) > 25 ? substr($particulars , 0, 22).'...' : $particulars;
        	}
        	if($voucher_type_id == '5') //journal
        	{
        		$builder = $this->db->table($comp_txn_tbl);
             $builder->orderBy('txn_id');
             $builder->where('voucher_txn_id', $voucher_txn_id);
             $builder->whereIn('master_id_type', ['acc','bsd']);
             $txn_result = $builder->get()->getResultArray();

             $credit_total = 0;
             $debit_total = 0;
             foreach ($txn_result as $key2 => $value2) {
             	if($value2['master_id_type'] == 'acc'){
             		$table = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
                    $builder = $this->db->table($table)->select($table.'.*');
        			$builder->where($table.'.txn_id', $value2['txn_id']);
					if($this->session->get('ses_boid')!='')
					$builder->where($table.'.bo_id', $this->session->get('ses_boid'));
				
                  $account = $builder->get()->getRowArray();
                  if($account){
                     if($account['acc_txn_drcr'] == 'c'){
                     	$account_info  = $this->account_info($value2['master_id']);
								$particulars  .= $account_info['acc_name'] . ',';
                        $credit_total += $account['acc_txn_amount'];
                     }
                     if($account['acc_txn_drcr'] == 'd'){
                     	$account_info  = $this->account_info($value2['master_id']);
								$particulars  .= $account_info['acc_name'] . ',';
                        $debit_total  += $account['acc_txn_amount'];
                  	}
                  }
             	}
             	if($value2['master_id_type'] == 'bsd'){
             		$table = $this->company_id.'_sundrytxnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($table);
						$builder->where('txn_id', $value2['txn_id']);
						$account = $builder->get()->getRowArray();
						if($account){
                     if($account['sundry_txn_drcr'] == 'c'){
                     	$bill_sundry_info  = $this->bill_sundry_info($value2['master_id']);
								$particulars  .= $bill_sundry_info['bill_sundry_name'] . ',';
                        $credit_total += $account['sundry_txn_amount'];
                     }
                     if($account['sundry_txn_drcr'] == 'd'){
                     	$bill_sundry_info  = $this->bill_sundry_info($value2['master_id']);
								$particulars  .= $bill_sundry_info['bill_sundry_name'] . ',';
                        $debit_total  += $account['sundry_txn_amount'];
                  	}
                  }
             	}
             	
             }

             if($credit_total > 0)
             	$credit = formatAmount($credit_total);
             if($debit_total > 0)
             	$debit = formatAmount($debit_total);

             $particulars = rtrim($particulars,',');
        	//	$particulars = strlen($particulars) > 25 ? substr($particulars , 0, 22).'...' : $particulars;
        	}
        	if($voucher_type_id == '15') //stock transfer
        	{
        		$balance = 0;

				$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
				$builder = $this->db->table($table);
				$builder->where('acc_id', 0);
    			$builder->where('voucher_txn_id', $voucher_txn_id);
				if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
				
                $account = $builder->get()->getRowArray();
                if($account){
                    $balance = $account['acc_oth_txn_amount'];
                }

                if($balance > 0)
                	$credit = formatAmount($balance);
                if($balance > 0)
                	$debit = formatAmount($balance);

                $particulars = 'Self';
        	}
        	if($voucher_type_id == '10') //physical verification
        	{
                $particulars = 'Self';
        	}
        	if($voucher_type_id == '14') //production
        	{
        		$builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $voucher_txn_id);
                $builder->where('master_id_type', 'itm');
                $txn_result = $builder->get()->getResultArray();

                $credit_total = 0;
                $debit_total = 0;
                foreach ($txn_result as $key2 => $value2) {
	                $table = $this->company_id.'_itemtxnnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
	                $builder = $this->db->table($table);
	    			$builder->where($table.'.txn_id', $value2['txn_id']);
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
				
	                $item = $builder->get()->getRowArray();
	                

	                if($item){
	                    if($item['item_txn_drcr'] == 'c'){
	                        $credit_total += ($item['item_txn_amount']);
	                    }
	                    if($item['item_txn_drcr'] == 'd'){
	                        $debit_total  += ($item['item_txn_amount']);
	                    }
	                }
            	}

                if($credit_total > 0)
                	$credit = formatAmount($credit_total);
                if($debit_total > 0)
                	$debit = formatAmount($debit_total);

                $particulars = 'Self';
        	}
        	if($voucher_type_id == '20') //stock journal
        	{
                $builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $voucher_txn_id);
                $builder->where('master_id_type', 'itm');
                $txn_result = $builder->get()->getResultArray();

                $credit_total = 0;
                $debit_total = 0;
                foreach ($txn_result as $key2 => $value2) {
	                $table = $this->company_id.'_itemtxnnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
	                $builder = $this->db->table($table);
	    			$builder->where($table.'.txn_id', $value2['txn_id']);
					if($this->session->get('ses_boid')!='')
					$builder->where($table.'.bo_id', $this->session->get('ses_boid'));
	                $item = $builder->get()->getRowArray();
	                

	                if($item){
	                    if($item['item_txn_drcr'] == 'c'){
	                        $credit_total += ($item['item_txn_amount']);
	                    }
	                    if($item['item_txn_drcr'] == 'd'){
	                        $debit_total  += ($item['item_txn_amount']);
	                    }
	                }
            	}

                if($credit_total > 0)
                	$credit = formatAmount($credit_total);
                if($debit_total > 0)
                	$debit = formatAmount($debit_total);

                $particulars = 'Self';
        	}
        	if($voucher_type_id == '4') //consignment packing
        	{
                $particulars = 'Self';
        	}
        	 
        	
        	$date         = date("d-m-Y", strtotime($value['voucher_date']));
        	$data[] = [
                'checkbox'     =>'<input name="voucher_ids[]" class="checkbox hidden voucher_row"  data-id="'.$value['comp_vch_series_id'].'||'.$value['voucher_txn_id'].'"  type="checkbox" value="'.$value['comp_vch_series_id'].'||'.$value['voucher_txn_id'].'">',
                'particulars'     => $particulars,
                'date'            => $date,
                'credit'          => $credit,
                'debit'           => $debit,
                'voucher_no'      => $value['comp_vch_no'],
                'voucher_type'    => $value['comp_vch_type'],
                'voucher_type_id' => $value['voucher_type_id'],
                'voucher_txn_id'  => $value['voucher_txn_id'],
				'bom_id'          => $bom_id,
				'bom_batches'     => $bom_batches
            ];
			
        }
		// echo "<pre>";print_r($data);exit;
		return  "{\"data\":".json_encode($data)."}";
	}
	
	
	
	public function load_day_book_condensed($pq_curPage, $limit, $from_date, $to_date, $view,$search)
    {
		$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
		$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
		$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
		$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		$acctcrsref_tbl      =  $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
	    
	    $builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	    
		$builder->where($voucher_tbl.'.comp_id', $this->company_id);
		if($this->session->get('ses_boid')!='')
			$builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));
		
		if($from_date!='')
			$builder->where('voucher_date >=', $from_date);
		if($to_date!='')
			$builder->where('voucher_date <=', $to_date);
		if($search != ''){
			$builder->groupStart();
			$builder->like('voucher_date', $search);
			$builder->orLike('comp_vch_type', $search);
			$builder->orLike('comp_vch_no', $search);
			$builder->groupEnd();
		}
		if($this->session->get('ses_boid')!='')
			$builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));
		$builder->where($voucher_tbl.'.voucher_type_id !=', 8);

		if($view == 2)
			$builder->where('voucher_tag', 'OPTIONL');
		else
			$builder->whereNotIn('voucher_tag', ['OPTIONL','RJVHTXP']);

		$builder->orderBy('voucher_txn_id');
		$total_Records = $builder->countAllResults();
		
		
		if($pq_curPage==0){ $pq_curPage=1;}
		$offset = ($limit * ($pq_curPage - 1));

        if ($offset > $total_Records)
            {        
                $pq_curPage = ceil($total_Records / $limit);
                $offset = ($limit * ($pq_curPage - 1));
            }
            

		$builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	    
		$builder->where($voucher_tbl.'.comp_id', $this->company_id);					
		if($this->session->get('ses_boid')!='')
			$builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));
		if($from_date!='')
			$builder->where('voucher_date >=', $from_date);
		if($to_date!='')
			$builder->where('voucher_date <=', $to_date);
		if($search != ''){
			$builder->groupStart();
			$builder->like('voucher_date', $search);
			$builder->orLike('comp_vch_type', $search);
			$builder->orLike('comp_vch_no', $search);
			$builder->groupEnd();
		}
		
		$builder->where($voucher_tbl.'.voucher_type_id !=', 8);

		if($view == 2)
			$builder->where('voucher_tag', 'OPTIONL');
		else
			$builder->whereNotIn('voucher_tag', ['OPTIONL','RJVHTXP']);

		$builder->orderBy('voucher_date');
		$builder->orderBy('voucher_txn_id');
		$builder->limit($limit,$offset);  
		$result = $builder->get()->getResultArray();

		$data = [];
		foreach($result as $key => $value)
        {
        	$voucher_txn_id  = $value['voucher_txn_id'];
        	$voucher_type_id = $value['voucher_type_id'];
        	$particulars = '';
        	$credit = '';
        	$debit = '';
        	$credit_total = 0;
        	$debit_total = 0;
        	$bom_id = 0;
        	$bom_batches = 0;

        	if($voucher_type_id == '18') //Sales
        	{
        		$builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $voucher_txn_id);
                $builder->where('master_id_type', 'acc');
                $builder->limit(1);
                $txn_result = $builder->get()->getRowArray();

                $account_info = $this->account_info($txn_result['master_id']);
					 $particulars  = $account_info['acc_name'];

                $table = $this->company_id.'_accnttxnnn_'.$txn_result['master_id'].'_'.$this->session->get('ses_comp_fy_id');
                $builder = $this->db->table($table);
    			$builder->where('txn_id', $txn_result['txn_id']);
				if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
				
                $account = $builder->get()->getRowArray();
                if($account){
                    if($account['acc_txn_drcr'] == 'c'){
                        $credit = formatAmount($account['acc_txn_amount']);
                        $credit_total = parseAmount($account['acc_txn_amount']);
                    }
                    if($account['acc_txn_drcr'] == 'd'){
                        $debit = formatAmount($account['acc_txn_amount']);
                        $debit_total = parseAmount($account['acc_txn_amount']);
                    }
                }
        	}
        	if($voucher_type_id == '11') //Purchase
        	{
        		$builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $voucher_txn_id);
                $builder->where('master_id_type', 'acc');
                $builder->limit(1);
                $txn_result = $builder->get()->getRowArray();

                $account_info = $this->account_info($txn_result['master_id']);
				$particulars  = $account_info['acc_name'];

                $table = $this->company_id.'_accnttxnnn_'.$txn_result['master_id'].'_'.$this->session->get('ses_comp_fy_id');
                $builder = $this->db->table($table);
    			$builder->where('txn_id', $txn_result['txn_id']);
				if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
				
                $account = $builder->get()->getRowArray();
                if($account){
                    if($account['acc_txn_drcr'] == 'c'){
                        $credit = formatAmount($account['acc_txn_amount']);
                        $credit_total = parseAmount($account['acc_txn_amount']);
                    }
                    if($account['acc_txn_drcr'] == 'd'){
                        $debit = formatAmount($account['acc_txn_amount']);
                        $debit_total = parseAmount($account['acc_txn_amount']);
                    }
                }
        	}

        	if($voucher_type_id == '2') //Credit Note
        	{
        		$builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $voucher_txn_id);
                $builder->where('master_id_type', 'acc');
                $builder->limit(1);
                $txn_result = $builder->get()->getRowArray();

                $account_info = $this->account_info($txn_result['master_id']);
				$particulars  = $account_info['acc_name'];

                $table = $this->company_id.'_accnttxnnn_'.$txn_result['master_id'].'_'.$this->session->get('ses_comp_fy_id');
                $builder = $this->db->table($table);
    			$builder->where('txn_id', $txn_result['txn_id']);
				if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
				
                $account = $builder->get()->getRowArray();
                if($account){
                    if($account['acc_txn_drcr'] == 'c'){
                        $credit = formatAmount($account['acc_txn_amount']);
                        $credit_total = parseAmount($account['acc_txn_amount']);
                    }
                    if($account['acc_txn_drcr'] == 'd'){
                        $debit = formatAmount($account['acc_txn_amount']);
                        $debit_total = parseAmount($account['acc_txn_amount']);
                    }
                }
        	}
        	if($voucher_type_id == '3') //Debit Note
        	{
				$builder = $this->db->table($comp_txn_tbl);
				$builder->orderBy('txn_id');
				$builder->where('voucher_txn_id', $voucher_txn_id);
				$builder->where('master_id_type', 'acc');
				$builder->limit(1);
				$txn_result = $builder->get()->getRowArray();

				$account_info = $this->account_info($txn_result['master_id']);
				$particulars  = $account_info['acc_name'];

				$table = $this->company_id.'_accnttxnnn_'.$txn_result['master_id'].'_'.$this->session->get('ses_comp_fy_id');
				$builder = $this->db->table($table);
				$builder->where('txn_id', $txn_result['txn_id']);
				if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
				
				$account = $builder->get()->getRowArray();
				if($account){
				  if($account['acc_txn_drcr'] == 'c'){
				      $credit = formatAmount($account['acc_txn_amount']);
				      $credit_total = parseAmount($account['acc_txn_amount']);
				  }
				  if($account['acc_txn_drcr'] == 'd'){
				      $debit = formatAmount($account['acc_txn_amount']);
				      $debit_total = parseAmount($account['acc_txn_amount']);
				  }
				}
        	}
        	if($voucher_type_id == '7') //Material Out
        	{
        		$builder = $this->db->table($comp_txn_tbl);
             $builder->orderBy('txn_id');
             $builder->where('voucher_txn_id', $voucher_txn_id);
             $builder->where('master_id_type', 'aco');
             $builder->limit(1);
             $txn_result = $builder->get()->getRowArray();

             if($txn_result){
             		$account_info = $this->account_info($txn_result['master_id']);
					$particulars  = $account_info['acc_name'];

					$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($table);
    				$builder->where('txn_id', $txn_result['txn_id']);
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
				
                $account = $builder->get()->getRowArray();
                if($account){
                    if($account['acc_oth_txn_drcr'] == 'c'){
                        $credit = formatAmount($account['acc_oth_txn_amount']);
                        $credit_total = parseAmount($account['acc_oth_txn_amount']);
                    }
                    if($account['acc_oth_txn_drcr'] == 'd'){
                        $debit = formatAmount($account['acc_oth_txn_amount']);
                        $debit_total = parseAmount($account['acc_oth_txn_amount']);
                    }
                }
             }
	               
        	}
        	if($voucher_type_id == '6') //Material In
        	{
        		$builder = $this->db->table($comp_txn_tbl);
				 $builder->orderBy('txn_id');
				 $builder->where('voucher_txn_id', $voucher_txn_id);
				 $builder->where('master_id_type', 'aco');
				 $builder->limit(1);
				 $txn_result = $builder->get()->getRowArray();
				 if($txn_result){
						$account_info = $this->account_info($txn_result['master_id']);
						$particulars  = $account_info['acc_name'];

						$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($table);
						$builder->where('txn_id', $txn_result['txn_id']);
						if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
				
						$account = $builder->get()->getRowArray();
						if($account){
							if($account['acc_oth_txn_drcr'] == 'c'){
								$credit = formatAmount($account['acc_oth_txn_amount']);
								$credit_total = parseAmount($account['acc_oth_txn_amount']);
							}
							if($account['acc_oth_txn_drcr'] == 'd'){
								$debit = formatAmount($account['acc_oth_txn_amount']);
								$debit_total = parseAmount($account['acc_oth_txn_amount']);
							}
						}
				 }
        	}

        	if($voucher_type_id == '19') //Sales Order
        	{
        		$builder = $this->db->table($comp_txn_tbl);
            $builder->orderBy('txn_id');
            $builder->where('voucher_txn_id', $voucher_txn_id);
            $builder->where('master_id_type', 'aco');
            $builder->limit(1);
            $txn_result = $builder->get()->getRowArray();

            if($txn_result)
            {
            	$account_info = $this->account_info($txn_result['master_id']);
					$particulars  = $account_info['acc_name'] ?? '';

					$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($table);
	    			$builder->where('txn_id', $txn_result['txn_id']);
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
				
	            $account = $builder->get()->getRowArray();
	            if($account){
	              	if($account['acc_oth_txn_drcr'] == 'c'){
	                  $credit = formatAmount($account['acc_oth_txn_amount']);
	                  $credit_total = parseAmount($account['acc_oth_txn_amount']);
	              	}
	              	if($account['acc_oth_txn_drcr'] == 'd'){
	                  $debit = formatAmount($account['acc_oth_txn_amount']);
	                  $debit_total = parseAmount($account['acc_oth_txn_amount']);
              	}
            }
            }
        	}
        	if($voucher_type_id == '12') //Purchase Order
        	{
        		$builder = $this->db->table($comp_txn_tbl);
				$builder->orderBy('txn_id');
				$builder->where('voucher_txn_id', $voucher_txn_id);
				$builder->where('master_id_type', 'aco');
				$builder->limit(1);
				$txn_result = $builder->get()->getRowArray();

				if($txn_result)
				{
					$account_info = $this->account_info($txn_result['master_id']);
					$particulars  = $account_info['acc_name'];

					$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($table);
					$builder->where('txn_id', $txn_result['txn_id']);
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
				
					$account = $builder->get()->getRowArray();
					if($account){
						if($account['acc_oth_txn_drcr'] == 'c'){
						   $credit = formatAmount($account['acc_oth_txn_amount']);
						   $credit_total = parseAmount($account['acc_oth_txn_amount']);
						}
						if($account['acc_oth_txn_drcr'] == 'd'){
						   $debit = formatAmount($account['acc_oth_txn_amount']);
						   $debit_total = parseAmount($account['acc_oth_txn_amount']);
						}
					}
				}
        	}
        	if($voucher_type_id == '17') //Quotation
        	{
				$builder = $this->db->table($comp_txn_tbl);
				$builder->orderBy('txn_id');
				$builder->where('voucher_txn_id', $voucher_txn_id);
				$builder->where('master_id_type', 'aco');
				$builder->limit(1);
				$txn_result = $builder->get()->getRowArray();

				if($txn_result){
					$account_info = $this->account_info($txn_result['master_id']);
					$particulars  = $account_info['acc_name'];

					$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($table);
					$builder->where('txn_id', $txn_result['txn_id']);
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
				
					$account = $builder->get()->getRowArray();
					if($account){
						if($account['acc_oth_txn_drcr'] == 'c'){
							$credit = formatAmount($account['acc_oth_txn_amount']);
							$credit_total = parseAmount($account['acc_oth_txn_amount']);
						}
						if($account['acc_oth_txn_drcr'] == 'd'){
							$debit = formatAmount($account['acc_oth_txn_amount']);
							$debit_total = parseAmount($account['acc_oth_txn_amount']);
						}
					}
				}
        	}
        	if($voucher_type_id == '21') //Purchase Requisition
        	{
				$builder = $this->db->table($comp_txn_tbl);
				$builder->orderBy('txn_id');
				$builder->where('voucher_txn_id', $voucher_txn_id);
				$builder->where('master_id_type', 'aco');
				$builder->limit(1);
				$txn_result = $builder->get()->getRowArray();
				if($txn_result){
					$account_info = $this->account_info($txn_result['master_id']);
					$particulars  = $account_info['acc_name'];

					$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($table);
					$builder->where('txn_id', $txn_result['txn_id']);
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
				
					$account = $builder->get()->getRowArray();
					if($account){
					  if($account['acc_oth_txn_drcr'] == 'c'){
					      $credit = formatAmount($account['acc_oth_txn_amount']);
					      $credit_total = parseAmount($account['acc_oth_txn_amount']);
					  }
					  if($account['acc_oth_txn_drcr'] == 'd'){
					      $debit = formatAmount($account['acc_oth_txn_amount']);
					      $debit_total = parseAmount($account['acc_oth_txn_amount']);
					  }
					}	
				}
        	}
        	if($voucher_type_id == '9') //payment cr
        	{
        		$builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $voucher_txn_id);
                $builder->where('master_id_type', 'acc');
                $txn_result = $builder->get()->getResultArray();

                $credit_total = 0;
                $debit_total = 0;
                foreach ($txn_result as $key2 => $value2) {
                	$table = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
                    $builder = $this->db->table($table)->select($table.'.*');
        			$builder->where($table.'.txn_id', $value2['txn_id']);
					if($this->session->get('ses_boid')!='')
					$builder->where($table.'.bo_id', $this->session->get('ses_boid'));
                    $account = $builder->get()->getRowArray();
                    if($account){
                        if($account['acc_txn_drcr'] == 'c'){
                        	$account_info  = $this->account_info($value2['master_id']);
									$particulars  .= $account_info['acc_name'] . ',';
                            $credit_total += $account['acc_txn_amount'];
                        }
                        // if($account['acc_txn_drcr'] == 'd'){
                        // 	$account_info  = $this->account_info($txn_result['master_id']);
						// 	$particulars  .= $account_info['acc_name'] . ', ';
                        //     $debit_total  += $account['acc_txn_amount'];
                        // }
                    }
                }

                if($credit_total > 0)
                	$credit = formatAmount($credit_total);
                if($debit_total > 0)
                	$debit = formatAmount($debit_total);

                $particulars = rtrim($particulars,',');
        	//	$particulars = strlen($particulars) > 25 ? substr($particulars , 0, 22).'...' : $particulars;
        	}
        	if($voucher_type_id == '13') //receipt dr
        	{
        		$builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $voucher_txn_id);
                $builder->where('master_id_type', 'acc');
                $txn_result = $builder->get()->getResultArray();

                $credit_total = 0;
                $debit_total = 0;
                foreach ($txn_result as $key2 => $value2) {
                	$table = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
                    $builder = $this->db->table($table)->select($table.'.*');
        			$builder->where($table.'.txn_id', $value2['txn_id']);
					if($this->session->get('ses_boid')!='')
					$builder->where($table.'.bo_id', $this->session->get('ses_boid'));
                    $account = $builder->get()->getRowArray();
                    if($account){
                        // if($account['acc_txn_drcr'] == 'c'){
                        // 	$account_info  = $this->account_info($txn_result['master_id']);
						// 	$particulars  .= $account_info['acc_name'] . ', ';
                        //     $credit_total += $account['acc_txn_amount'];
                        // }
                        if($account['acc_txn_drcr'] == 'd'){
                        	$account_info  = $this->account_info($value2['master_id']);
							$particulars  .= $account_info['acc_name'] . ',';
                            $debit_total  += $account['acc_txn_amount'];
                        }
                    }
                }

                if($credit_total > 0)
                	$credit = formatAmount($credit_total);
                if($debit_total > 0)
                	$debit = formatAmount($debit_total);

                $particulars = rtrim($particulars,',');
        	//	$particulars = strlen($particulars) > 25 ? substr($particulars , 0, 22).'...' : $particulars;
        	}
        	if($voucher_type_id == '1') //contra
        	{
        		$builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $voucher_txn_id);
                $builder->where('master_id_type', 'acc');
                $txn_result = $builder->get()->getResultArray();

                $credit_total = 0;
                $debit_total = 0;
                foreach ($txn_result as $key2 => $value2) {
                	$table = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
                    $builder = $this->db->table($table)->select($table.'.*');
        			$builder->where($table.'.txn_id', $value2['txn_id']);
					if($this->session->get('ses_boid')!='')
					$builder->where($table.'.bo_id', $this->session->get('ses_boid'));
                    $account = $builder->get()->getRowArray();
                    if($account){
                        if($account['acc_txn_drcr'] == 'c'){
                        	$account_info  = $this->account_info($value2['master_id']);
							$particulars  .= $account_info['acc_name'] . ',';
                            $credit_total += $account['acc_txn_amount'];
                        }
                        if($account['acc_txn_drcr'] == 'd'){
                        	$account_info  = $this->account_info($value2['master_id']);
							$particulars  .= $account_info['acc_name'] . ',';
                            $debit_total  += $account['acc_txn_amount'];
                        }
                    }
                }

                if($credit_total > 0)
                	$credit = formatAmount($credit_total);
                if($debit_total > 0)
                	$debit = formatAmount($debit_total);

                $particulars = rtrim($particulars,',');
        	//	$particulars = strlen($particulars) > 25 ? substr($particulars , 0, 22).'...' : $particulars;
        	}
        	if($voucher_type_id == '5') //journal
        	{
        		$builder = $this->db->table($comp_txn_tbl);
             $builder->orderBy('txn_id');
             $builder->where('voucher_txn_id', $voucher_txn_id);
             $builder->whereIn('master_id_type', ['acc','bsd','aco','bso']);
             $txn_result = $builder->get()->getResultArray();

             $credit_total = 0;
             $debit_total = 0;
             foreach ($txn_result as $key2 => $value2) {

             	if($value['voucher_tag'] == 'OPTIONL')
             	{
             		if($value2['master_id_type'] == 'aco'){

             			$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
	                  $builder = $this->db->table($table)->select($table.'.*');
	        				$builder->where($table.'.txn_id', $value2['txn_id']);
							if($this->session->get('ses_boid')!='')
					$builder->where($table.'.bo_id', $this->session->get('ses_boid'));
				
	                  $account = $builder->get()->getRowArray();
	                  if($account){
	                     if($account['acc_oth_txn_drcr'] == 'c'){
	                     	$account_info  = $this->account_info($value2['master_id']);
									$particulars  .= $account_info['acc_name'] . ',';
	                        $credit_total += $account['acc_oth_txn_amount'];
	                     }
	                     if($account['acc_oth_txn_drcr'] == 'd'){
	                     	$account_info  = $this->account_info($value2['master_id']);
									$particulars  .= $account_info['acc_name'] . ',';
	                        $debit_total  += $account['acc_oth_txn_amount'];
	                  	}
	                  }
             		}
             		if($value2['master_id_type'] == 'bso'){

             			$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
	                  $builder = $this->db->table($table)->select($table.'.*');
	        				$builder->where($table.'.txn_id', $value2['txn_id']);
							if($this->session->get('ses_boid')!='')
					$builder->where($table.'.bo_id', $this->session->get('ses_boid'));
				
	                  $account = $builder->get()->getRowArray();
	                  if($account){
	                     if($account['acc_oth_txn_drcr'] == 'c'){
	                     	$account_info  = $this->account_info($value2['master_id']);
									$particulars  .= $account_info['acc_name'] . ',';
	                        $credit_total += $account['acc_oth_txn_amount'];
	                     }
	                     if($account['acc_oth_txn_drcr'] == 'd'){
	                     	$account_info  = $this->account_info($value2['master_id']);
									$particulars  .= $account_info['acc_name'] . ',';
	                        $debit_total  += $account['acc_oth_txn_amount'];
	                  	}
	                  }
             		}
             	}
             	else
             	{
             		if($value2['master_id_type'] == 'acc'){

	          			$table = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
	                  $builder = $this->db->table($table)->select($table.'.*');
	        				$builder->where($table.'.txn_id', $value2['txn_id']);
							if($this->session->get('ses_boid')!='')
					$builder->where($table.'.bo_id', $this->session->get('ses_boid'));
				
	                  $account = $builder->get()->getRowArray();
	                  if($account){
	                     if($account['acc_txn_drcr'] == 'c'){
	                     	$account_info  = $this->account_info($value2['master_id']);
									$particulars  .= $account_info['acc_name'] . ',';
	                        $credit_total += $account['acc_txn_amount'];
	                     }
	                     if($account['acc_txn_drcr'] == 'd'){
	                     	$account_info  = $this->account_info($value2['master_id']);
									$particulars  .= $account_info['acc_name'] . ',';
	                        $debit_total  += $account['acc_txn_amount'];
	                  	}
	                  }
	             		
	             	}
	             	if($value2['master_id_type'] == 'bsd'){

	          			$table = $this->company_id.'_sundrytxnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
							$builder = $this->db->table($table);
							$builder->where('txn_id', $value2['txn_id']);
							$account = $builder->get()->getRowArray();
							if($account){
	                     if($account['sundry_txn_drcr'] == 'c'){
	                     	$bill_sundry_info  = $this->bill_sundry_info($value2['master_id']);
									$particulars  .= $bill_sundry_info['bill_sundry_name'] . ',';
	                        $credit_total += $account['sundry_txn_amount'];
	                     }
	                     if($account['sundry_txn_drcr'] == 'd'){
	                     	$bill_sundry_info  = $this->bill_sundry_info($value2['master_id']);
									$particulars  .= $bill_sundry_info['bill_sundry_name'] . ',';
	                        $debit_total  += $account['sundry_txn_amount'];
	                  	}
	                  }
	               }
             	}
             	
             }

             if($credit_total > 0)
             	$credit = formatAmount($credit_total);
             if($debit_total > 0)
             	$debit = formatAmount($debit_total);

             $particulars = rtrim($particulars,',');
        	//	$particulars = strlen($particulars) > 25 ? substr($particulars , 0, 22).'...' : $particulars;
        	}

        	if($voucher_type_id == '16') // reverse journal
        	{
        		$builder = $this->db->table($comp_txn_tbl);
             $builder->orderBy('txn_id');
             $builder->where('voucher_txn_id', $voucher_txn_id);
             $builder->whereIn('master_id_type', ['acc','bsd']);
             $txn_result = $builder->get()->getResultArray();

             $credit_total = 0;
             $debit_total = 0;
             foreach ($txn_result as $key2 => $value2) {
             	if($value2['master_id_type'] == 'acc'){
             		
          			$table = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
                  $builder = $this->db->table($table)->select($table.'.*');
        				$builder->where($table.'.txn_id', $value2['txn_id']);
						if($this->session->get('ses_boid')!='')
					$builder->where($table.'.bo_id', $this->session->get('ses_boid'));
				
                  $account = $builder->get()->getRowArray();
                  if($account){
                     if($account['acc_txn_drcr'] == 'c'){
                     	$account_info  = $this->account_info($value2['master_id']);
								$particulars  .= $account_info['acc_name'] . ',';
                        $credit_total += $account['acc_txn_amount'];
                     }
                     if($account['acc_txn_drcr'] == 'd'){
                     	$account_info  = $this->account_info($value2['master_id']);
								$particulars  .= $account_info['acc_name'] . ',';
                        $debit_total  += $account['acc_txn_amount'];
                  	}
                  }
             		
             		
             	}
             	if($value2['master_id_type'] == 'bsd'){

          			$table = $this->company_id.'_sundrytxnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($table);
						$builder->where('txn_id', $value2['txn_id']);
						$account = $builder->get()->getRowArray();
						if($account){
                     if($account['sundry_txn_drcr'] == 'c'){
                     	$bill_sundry_info  = $this->bill_sundry_info($value2['master_id']);
								$particulars  .= $bill_sundry_info['bill_sundry_name'] . ',';
                        $credit_total += $account['sundry_txn_amount'];
                     }
                     if($account['sundry_txn_drcr'] == 'd'){
                     	$bill_sundry_info  = $this->bill_sundry_info($value2['master_id']);
								$particulars  .= $bill_sundry_info['bill_sundry_name'] . ',';
                        $debit_total  += $account['sundry_txn_amount'];
                  	}
                  }
             		
             	}
             }

             if($credit_total > 0)
             	$credit = formatAmount($credit_total);
             if($debit_total > 0)
             	$debit = formatAmount($debit_total);

             $particulars = rtrim($particulars,',');
        		//	$particulars = strlen($particulars) > 25 ? substr($particulars , 0, 22).'...' : $particulars;
        	}
        	if($voucher_type_id == '15') //stock transfer
        	{
        		$balance = 0;

				$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
				$builder = $this->db->table($table);
				$builder->where('acc_id', 0);
    			$builder->where('voucher_txn_id', $voucher_txn_id);
				if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
				
                $account = $builder->get()->getRowArray();
                if($account){
                    $balance = $account['acc_oth_txn_amount'];
                }

                if($balance > 0){
                	$credit = formatAmount($balance);
                	$credit_total = parseAmount($balance);
                }
                if($balance > 0){
                	$debit = formatAmount($balance);
                	$debit_total = parseAmount($balance);
                }

                $particulars = 'Self';
        	}
        	if($voucher_type_id == '10') //physical verification
        	{
                $particulars = 'Self';
        	}
        	if($voucher_type_id == '14') //production
        	{
        		$builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $voucher_txn_id);
                $builder->where('master_id_type', 'itm');
                $txn_result = $builder->get()->getResultArray();

                $credit_total = 0;
                $debit_total = 0;
                foreach ($txn_result as $key2 => $value2) {
	                $table = $this->company_id.'_itemtxnnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
	                $builder = $this->db->table($table);
	    			$builder->where($table.'.txn_id', $value2['txn_id']);
					if($this->session->get('ses_boid')!='')
					$builder->where($table.'.bo_id', $this->session->get('ses_boid'));
				
	                $item = $builder->get()->getRowArray();
	                

	                if($item){
	                    if($item['item_txn_drcr'] == 'c'){
	                        $credit_total += ($item['item_txn_amount']);
	                    }
	                    if($item['item_txn_drcr'] == 'd'){
	                        $debit_total  += ($item['item_txn_amount']);
	                    }
	                }
            	}

                if($credit_total > 0)
                	$credit = formatAmount($credit_total);
                if($debit_total > 0)
                	$debit = formatAmount($debit_total);

                $particulars = 'Self';
                $get_bom_info = $this->get_bom_info($value['voucher_txn_id']);
						if($get_bom_info){
							$bom_id       = $get_bom_info['bom_id'];
						}					
						 
        	}
        	if($voucher_type_id == '20') //stock journal
        	{
                $builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $voucher_txn_id);
                $builder->where('master_id_type', 'itm');
                $txn_result = $builder->get()->getResultArray();

                $credit_total = 0;
                $debit_total = 0;
                foreach ($txn_result as $key2 => $value2) {
	                $table = $this->company_id.'_itemtxnnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
	                $builder = $this->db->table($table);
	    			$builder->where($table.'.txn_id', $value2['txn_id']);
					if($this->session->get('ses_boid')!='')
					$builder->where($table.'.bo_id', $this->session->get('ses_boid'));
				
	                $item = $builder->get()->getRowArray();
	                

	                if($item){
	                    if($item['item_txn_drcr'] == 'c'){
	                        $credit_total += ($item['item_txn_amount']);
	                    }
	                    if($item['item_txn_drcr'] == 'd'){
	                        $debit_total  += ($item['item_txn_amount']);
	                    }
	                }
            	}

                if($credit_total > 0)
                	$credit = formatAmount($credit_total);
                if($debit_total > 0)
                	$debit = formatAmount($debit_total);

                $particulars = 'Self';
        	}
        	if($voucher_type_id == '4') //consignment packing
        	{
                $particulars = 'Self';
        	}
        	 
        	
        	$date         = date("d-m-Y", strtotime($value['voucher_date']));
        	$data[] = [
                'checkbox'     =>'<input name="voucher_ids[]" class="checkbox hidden voucher_row"  data-id="'.$value['comp_vch_series_id'].'||'.$value['voucher_txn_id'].'"  type="checkbox" value="'.$value['comp_vch_series_id'].'||'.$value['voucher_txn_id'].'">',
                'particulars'     => $particulars,
                'date'            => $date,
                'credit'          => $credit,
                'debit'           => $debit,
                'credit_total'    => $credit_total,
                'debit_total'     => $debit_total,
                'voucher_no'      => $value['comp_vch_no'],
                'voucher_type'    => $value['comp_vch_type'],
                'voucher_type_id' => $value['voucher_type_id'],
                'voucher_txn_id'  => $value['voucher_txn_id'],
				'bom_id'          => $bom_id,
				'bom_batches'     => $bom_batches
            ];
			
        }
		// echo "<pre>";print_r($data);exit;
		echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($data)."}";
	}
	
    public function load_day_book($pq_curPage, $limit, $offset, $from_date, $to_date,$view_type)
    {
        $voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	    $voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	    $voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
	    $comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		$acctcrsref_tbl      =  $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
	    
	    $builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	    
		$builder->where($voucher_tbl.'.comp_id', $this->company_id);		
		if($this->session->get('ses_boid')!='')
		$builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));	
		
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}
		$builder->orderBy('voucher_txn_id');
		$total_Records = $builder->countAllResults();
	   
		
	    $builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	    
		$builder->where($voucher_tbl.'.comp_id', $this->company_id);					
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}
		$builder->orderBy('voucher_txn_id');
	 	// $builder->limit($limit,$offset);  
		$result = $builder->get()->getResultArray();
	
        $data = [];
     
        foreach($result as $key => $value)
        {
			// if production voucher 
			$voucher_type_id = $value['voucher_type_id'];
			$voucher_txn_id  = $value['voucher_txn_id'];
			
			if($voucher_type_id=='14'){
			  $debit_credit_data = $this->production_credit_debit_result($value['voucher_txn_id']);
			  
			  //check type of voucher
            $builder = $this->db->table($comp_txn_tbl);
            $builder->orderBy('txn_id');
            $builder->where('voucher_txn_id', $value['voucher_txn_id']);
            $count = $builder->countAllResults();
			}
			else{
            //check type of voucher
            $builder = $this->db->table($comp_txn_tbl);
            $builder->orderBy('txn_id');
            $builder->where('voucher_txn_id', $value['voucher_txn_id']);
            $builder->where('master_id_type', 'acc');
            $count = $builder->countAllResults();
			}
            if($count > 0)
            {
				
                $type = 'external';
                $builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $value['voucher_txn_id']);
                $builder->where('master_id_type', 'acc');
                $result2 = $builder->get()->getResultArray();  //list of accounts with that voucher
                
                foreach($result2 as $key2 => $value2)
                {					
                    // $account_name = 'Self';
                    $date         = date("d-m-Y", strtotime($value['voucher_date']));
                    $debit        = '';
                    $credit       = '';
                                     
					if($voucher_type_id=='14'){
						// fetch bom id for production voucher from table acctcrsref
						 $get_bom_info = $this->get_bom_info($value['voucher_txn_id']);
						 $account_name = $get_bom_info['bom_name'];
						 $bom_id       = $get_bom_info['bom_id']; 	
                         $bom_batches  = $get_bom_info['bom_batches']; 						 
						 $credit       = $debit_credit_data['cr'];
						 $debit        = $debit_credit_data['dr'];
					}else{
						  $bom_id=0;
						  $bom_batches=0;
						  $account_info = $this->account_info($value2['master_id']);
						  $account_name = $account_info['acc_name'];
                   
                    $table = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
                    $builder = $this->db->table($table)->select($table.'.*');
        			$builder->where($table.'.txn_id', $value2['txn_id']);
					if($this->session->get('ses_boid')!='')
					$builder->where($table.'.bo_id', $this->session->get('ses_boid'));
				
                    $account = $builder->get()->getRowArray();
                    if($account){
                        if($account['acc_txn_drcr'] == 'c'){
                            $credit       = $account['acc_txn_amount'];
                        }
                        if($account['acc_txn_drcr'] == 'd'){
                            $debit        = $account['acc_txn_amount'];
                        }
                    }
                 }
                     
                 if($key2==0){
                     $checkbox_val = '<input name="voucher_ids[]" class="checkbox hidden voucher_row"  data-id="'.$value2['comp_vch_series_id'].'||'.$value2['voucher_txn_id'].'"  type="checkbox" value="'.$value2['comp_vch_series_id'].'||'.$value2['voucher_txn_id'].'">';
                     $date_val  = $date;
                     
                 }
                 
                 else{
                    $checkbox_val = '';
                     $date_val  = '';   
                     
                 }
                 
                    
                    $data[] = [
                           'checkbox'     =>$checkbox_val,
                            'particulars'   => $account_name,
                            'date'          => $date_val,
                            'credit'        => $credit,
                            'debit'         => $debit,
                            'voucher_no'    => $value['comp_vch_no'],
                            'voucher_type'  => $value['comp_vch_type'],
                            'voucher_type_id' => $value['voucher_type_id'],
                            'voucher_txn_id'  => $value['voucher_txn_id'],
                            'account_id'      => $value2['master_id'],
                            'type'            => $type,
							'bom_id'          => $bom_id,
							'bom_batches'     => $bom_batches
                           ];
                        
               if($view_type=='condensed'){
                    if($key2!=0){
                     array_pop($data);
                     
                 }
                 
                     
                 }
                 
                 
                 }
            
            }
            
            else
            {
                $type = 'internal';
                $builder = $this->db->table($comp_txn_tbl);
                $builder->orderBy('txn_id');
                $builder->where('voucher_txn_id', $value['voucher_txn_id']);
                $builder->where('master_id_type', 'itm');
                $result2 = $builder->get()->getResultArray();  //list of accounts with that voucher
                
                $account_name = 'Self';
                $date         = date("d-m-Y", strtotime($value['voucher_date']));
                $debit        = 0;
                $credit       = 0;
                    
                foreach($result2 as $key2 => $value2)
                {
                    $table = $this->company_id.'_itemtxnnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
                    $builder = $this->db->table($table)->select($table.'.*');
        			$builder->where($table.'.txn_id', $value2['txn_id']);
					if($this->session->get('ses_boid')!='')
					$builder->where($table.'.bo_id', $this->session->get('ses_boid'));
				
                    $item = $builder->get()->getRowArray();
                    
    
                    if($item){
                        if($item['item_txn_drcr'] == 'c'){
                            $credit       += $item['item_txn_amount']*$item['item_txn_qty'];
                        }
                        if($item['item_txn_drcr'] == 'd'){
                            $debit        += $item['item_txn_amount']*$item['item_txn_qty'];
                        }
                    }
   
                }
                
               
                       
                   
              
                $data[] = [
                        'checkbox'     =>'<input name="voucher_ids[]" class="checkbox hidden voucher_row"  data-id="'.$value['comp_vch_series_id'].'||'.$value['voucher_txn_id'].'"  type="checkbox" value="'.$value['comp_vch_series_id'].'||'.$value['voucher_txn_id'].'">',
                        'particulars'     => $account_name,
                        'date'            => $date,
                        'credit'          => $credit,
                        'debit'           => $debit,
                        'voucher_no'      => $value['comp_vch_no'],
                        'voucher_type'    => $value['comp_vch_type'],
                        'voucher_type_id' => $value['voucher_type_id'],
                        'voucher_txn_id'  => $value['voucher_txn_id'],
                        'account_id'      => '',
                        'type'            => $type,
						'bom_id'          => "0",
						'bom_batches'     => "0"
                    ];
                    
                    
                           
                    
            }
       
            
  
        }
       
           
       $final_arary = array();
       if($data) {
          foreach(  $data as $rows)
            $final_arary[]=$rows;
           
       }
       $final = array_slice( $final_arary, $offset, $limit );
       
        echo  "{\"totalRecords\":" . count($final_arary) . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($final)."}";
    }

    function get_all_bill_sundry_accounts()
    {
        
        $bill_sundry_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
	    
	    $builder = $this->db->table($bill_sundry_tbl); 
		$builder->select('bill_sundry_id as id, bill_sundry_name as label, bill_sundry_name as value');
		
		$builder->where('bo_id', $this->bo_id);
		$result = $builder->get()->getResultArray();
        
		
		return $result;
    }
    function bill_sundry_info($id)
    {
    	$bill_sundry_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
		$result = $this->db->table($bill_sundry_tbl)->where('bill_sundry_id', $id)->where('bo_id', $this->bo_id)->get()->getRowArray();
	    return $result; 
    }

    function bill_sundry_op_balance($id,$extdb,$company_id,$comp_fy_id)
    {
    	$bsdoppybal_tbl = $company_id.'_bsdoppybal_'.$comp_fy_id;
		$result = $extdb->table($bsdoppybal_tbl)->where('bill_sundry_id', $id)->where('bo_id', $this->bo_id)->get()->getRowArray();
	    return $result; 
    }
   
   
   
   function get_all_batches()
    {
        $itmbatchmt_tbl = $this->company_id.'_itmbatchmt_'.$this->session->get('ses_comp_fy_id');
	    $builder = $this->db->table($itmbatchmt_tbl); 
		$builder->select('batch_id as id, batch_no as label, batch_no as value,item_id,batch_qty,batch_expiry,batch_mfr');
        $result = $builder->get()->getResultArray();
        return $result;
    }
	
	
	
	function get_all_stock_category()
    {
        $itemcatmst_tbl = $this->company_id.'_itemcatmst_'.$this->session->get('ses_comp_fy_id');
	    $builder = $this->db->table($itemcatmst_tbl); 
		$builder->select('icatgms_id, item_cat');
        $result = $builder->get()->getResultArray();
		$all_category=array();
		foreach($result as $row){
			$category_name = $this->enc_string->nc_string($row['item_cat'],'de');
			$all_category[]= array("id"=>$row['icatgms_id'],'label'=>$category_name,'value'=> $category_name);
			
		}
        return $all_category;
    }
	
	function get_all_item_groups_list()
    {
        $itemgrpmst_tbl = $this->company_id.'_itemgrpmst_'.$this->session->get('ses_comp_fy_id');
	    $builder = $this->db->table($itemgrpmst_tbl); 
		$builder->select('item_grp_id, item_grp_name');
        $result = $builder->get()->getResultArray();
		$all_groups=array();
		foreach($result as $row){
			$item_grp_name = $this->enc_string->nc_string($row['item_grp_name'],'de');
			$all_groups[]= array("id"=>$row['item_grp_id'],'label'=>$item_grp_name,'value'=> $item_grp_name);
			
		}
        return $all_groups;
    }
	
	function get_all_items()
    {
        $itemmaster_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
	    $builder = $this->db->table($itemmaster_tbl); 
		$builder->select('item_id, item_name');
        $result = $builder->get()->getResultArray();
		$all_items=array();
		foreach($result as $row){
			$item_name = $row['item_name'];
			$all_items[]= array("id"=>$row['item_id'],'label'=>$item_name,'value'=> $item_name);
			
		}
        return $all_items;
    }
	
    function get_all_accounts()
    {
        
        $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	    
	    $builder = $this->db->table($account_master_tbl); 
		$builder->select('acc_id as id,  acc_grp_id, acc_name as label, acc_name as value');
        $builder->where('comp_id', $this->company_id);
		$result = $builder->get()->getResultArray();
        
		
		return $result;
    }

    function get_all_groups()
    {
        $tbl_name = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
        
        $builder = $this->db->table($tbl_name); 
		$builder->select('acc_grp_id as id, acc_grp_name as label, acc_grp_name as value');
        $builder->where('comp_id', $this->company_id);
		$result = $builder->get()->getResultArray();
        
		
		return $result;
    }

    function get_sundry_accounts()
    {
        $groups = $this->get_sundry_groups();
        $groups_ids = array_column($groups, 'id');
        
        $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	    
	    $builder = $this->db->table($account_master_tbl); 
		$builder->select('acc_id as id,  acc_grp_id, acc_name as label, acc_name as value');
        $builder->where('comp_id', $this->company_id);		
		$builder->whereIn('acc_grp_id', $groups_ids);
		$result = $builder->get()->getResultArray();
        
		
		return $result;
    }
    
    function get_group_details($id)
    {
        $grup_mst_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
        $builder = $this->db->table($grup_mst_tbl); 
        $builder->where('comp_id', $this->company_id);		
		$builder->where('acc_grp_id', $id);
		$result = $builder->get()->getRowArray();
		
		return $result;
    }
    
    function get_bill_details($id)
    {
        $bill_master_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
        $builder = $this->db->table($bill_master_tbl);	
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));	
		$builder->where('bills_ref_id', $id);
		$result = $builder->get()->getRowArray();
		
		if($result)
		{
		    $result['start_date'] = '';
		    $bill_txn_tbl = $this->company_id.'_billstxnnn_'.$this->session->get('ses_comp_fy_id');
		    $builder = $this->db->table($bill_txn_tbl); 
            $builder->where('bills_ref_id', $result['bills_ref_id']);
			if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));	
            $builder->orderBy('bills_txn_id', 'asc');
            $builder->limit(1);
            $bill_txn = $builder->get()->getRowArray();
            if($bill_txn){
                $result['start_date'] = date("d-m-Y", strtotime($bill_txn['bills_txn_date']));
            }
		    
		}
		
		return $result;
    }
    
    function get_sundry_groups()
    {
    	$array = [22,16];
        	$tbl_name = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($tbl_name); 
			$builder->select('acc_grp_id as id, acc_grp_name as label, acc_grp_name as value');
			$builder->where('comp_id', $this->company_id);		
			$builder->whereIn('acc_grp_id', $array);
			$builder->orWhereIn('under_main_grp_id', $array);
			$groups = $builder->get()->getResultArray();
        
        return $groups;
    }
    
    function get_cc_list()
    {
        $tbl_name = $this->company_id.'_costctmstr_'.$this->session->get('ses_comp_fy_id');
        $builder = $this->db->table($tbl_name); 
		$builder->select('cc_id as id, cc_name as label, cc_name as value');
        $builder->where('comp_id', $this->company_id);
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
		$result = $builder->get()->getResultArray();
		
		return $result;
    }
    
    function get_cc_groups()
    {
        $tbl_name = $this->company_id.'_costctgrup_'.$this->session->get('ses_comp_fy_id');
        $builder = $this->db->table($tbl_name); 
		$builder->select('cc_grp_id as id, cc_grp_name as label, cc_grp_name as value');
        $builder->where('comp_id', $this->company_id);
		$result = $builder->get()->getResultArray();
        // echo "<pre>";print_r($result);exit;
        return $result;
    }
    
    function get_sub_group_ids($array){
    	$tbl_name = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$data = $this->db->table($tbl_name)
    					->select('acc_grp_id')
    					->whereIn('under_main_grp_id', $array)
    					->get()->getResultArray();

    	if($data){
    		foreach($data as $key => $value) {
    			array_push($array, $value['acc_grp_id']);
    		}
    	}
    	return $array;
    }

    function get_sundry_creditors()
    {
        $groups = [16];
        $final_groups = $this->get_sub_group_ids($groups);
        
        return $final_groups;
    }
    function get_sundry_debitors()
    {
        $groups = [22];
        $final_groups = $this->get_sub_group_ids($groups);
        
        return $final_groups;
    }
    
    function load_bills_management_accounts($pq_curPage, $limit, $from_date, $to_date, $bill_type)
    {
        $groups = $this->get_sundry_groups();
        $groups_ids = array_column($groups, 'id');
        
        $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
        
        $builder = $this->db->table($account_master_tbl); 
        $builder->select('acc_id as id, acc_name as account_name');
        $builder->where('comp_id', $this->company_id);		
        $builder->whereIn('acc_grp_id', $groups_ids);
        $result = $builder->get()->getResultArray();
        
        $final = [];
        
        foreach($result as $key => $value)
        {
            $bal_undefined = 0;
            $receivable = 0;
            $payable = 0;
            
            $bill_master_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
            $bill_txn_tbl = $this->company_id.'_billstxnnn_'.$this->session->get('ses_comp_fy_id');
            
            $builder = $this->db->table($bill_master_tbl); 
            $builder->select('bills_ref_id');
            $builder->where('bills_ref_name', 'UNDEFINED');
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
            $builder->where('acc_id', $value['id']);
            $bill_master_un = $builder->get()->getRowArray();
            
            if($bill_master_un){
                $builder = $this->db->table($bill_txn_tbl); 
                $builder->select('bills_txn_bal'); // bills_txn_bal
				if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));	
                $builder->where('bills_ref_id', $bill_master_un['bills_ref_id']);
                if($to_date != '')
                    $builder->where('bills_txn_date <=', $to_date);
                $builder->orderBy('bills_txn_date', 'desc');
                $builder->orderBy('bills_txn_id', 'desc');
                $builder->limit(1);
                $bill_txn_un = $builder->get()->getRowArray();
                if($bill_txn_un){
                    $bal_undefined = $bill_txn_un['bills_txn_bal'];// bills_txn_bal
                }
            }
            
            $drcr_undefined = ' DR';
            if($bal_undefined < 0){
                $drcr_undefined = ' CR';
            }
            
            $builder = $this->db->table($bill_master_tbl); 
            $builder->select('bills_ref_id');
            $builder->where('bills_ref_name !=', 'UNDEFINED');
            $builder->where('acc_id', $value['id']);
            $bill_masters = $builder->get()->getResultArray();
            
            if($bill_masters){
                foreach($bill_masters as $bill_master){
                    $builder = $this->db->table($bill_txn_tbl); 
                    $builder->select('bills_txn_bal'); // bills_txn_bal
                    $builder->where('bills_ref_id', $bill_master['bills_ref_id']);
                    if($to_date != '')
                        $builder->where('bills_txn_date <=', $to_date);
                    $builder->orderBy('bills_txn_date', 'desc');
                    $builder->orderBy('bills_txn_id', 'desc');
                    $builder->limit(1);
                    $bill_txn = $builder->get()->getRowArray();
                    if($bill_txn){
                    $balance = $bill_txn['bills_txn_bal'];// bills_txn_bal
                        if($balance >= 0){
                            $receivable += $balance;
                        }
                        else{
                            $payable += $balance;
                        }
                    }
                }
            }
            
            if($receivable == 0 || $payable == 0)
            {
                if($receivable == 0 && $bill_type == 'R')
                    continue;
                if($payable == 0 && $bill_type == 'P')
                    continue;
            }
            
            $net_bill_amount = $receivable + $payable;
            $net_bill_drcr = ' DR';
            if($net_bill_amount < 0){
                $net_bill_drcr = ' CR';
            }
            
            $net_bill_os = $bal_undefined + $net_bill_amount;
            $net_bill_os_drcr = ' DR';
            if($net_bill_os < 0){
                $net_bill_os_drcr = ' CR';
            }
            
            $ledger_bal = 0;
            $acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value['id'].'_'.$this->session->get('ses_comp_fy_id');
            $builder = $this->db->table($acc_txn_tbl); 
			
			if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
				
				
            $builder->select('acc_bal');
            if($to_date != '')
                $builder->where('acc_txn_date <=', $to_date);
            $builder->orderBy('acc_txn_date', 'desc');
            $builder->orderBy('voucher_txn_id', 'desc');
            $builder->orderBy('acc_txn_id', 'desc');
            $builder->limit(1);
            $acc_txn = $builder->get()->getRowArray();
            if($acc_txn){
                $ledger_bal = $acc_txn['acc_bal'];
            }
            
            $ledger_bal_drcr = ' DR';
            if($ledger_bal < 0){
                $ledger_bal_drcr = ' CR';
            }
            
            
            $final[] = [
                'account_id'       =>   $value['id'],
                'account_name'     =>   $value['account_name'],
                'bills_receivable' =>   $receivable ? formatAmount(abs($receivable)).' DR' : '',
                'bills_payable'    =>   $payable ? formatAmount(abs($payable)).' CR' : '',
                'net_bill_amount'  =>   formatAmount(abs($net_bill_amount)).$net_bill_drcr,
                'on_account'       =>   $bal_undefined ? formatAmount(abs($bal_undefined)).$drcr_undefined : '',
                'net_bill_os'      =>   formatAmount(abs($net_bill_os)).$net_bill_os_drcr,
                'ledger_bal'       =>   formatAmount(abs($ledger_bal)).$ledger_bal_drcr.'&nbsp;&nbsp;',
                
                'count_receivable' =>   $receivable,
                'count_payable'    =>   abs($payable),
                'count_net_bill'   =>   $net_bill_amount,
                'count_undefined'  =>   $bal_undefined,
                'count_net_os'     =>   $net_bill_os,
                'count_ledger'     =>   $ledger_bal
            ];
        
        }

        // 		echo "<pre>";print_r($final);exit;
        $total_Records = count($final);
        
        $offset = ($limit * ($pq_curPage - 1));
        if ($offset > $total_Records)
          {        
           $pq_curPage = ceil($total_Records / $limit);
           $offset = ($limit * ($pq_curPage - 1));
          }
          
  
        
        
        $final_records = array_slice( $final, $offset, $limit );

        echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($final_records)."}";
    }
    
    function load_bills_management_one_account($pq_curPage, $limit, $offset, $from_date, $to_date, $account_id,$bill_type,$nil_type)
    {
        $sundry_creditors = $this->get_sundry_creditors();
        $sundry_debitors = $this->get_sundry_debitors();
        
        $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
        
        $builder = $this->db->table($account_master_tbl); 
        $builder->where('comp_id', $this->company_id);		
        $builder->where('acc_id', $account_id);
        $result = $builder->get()->getRowArray();
        $account_name = $result['acc_name'];
        $acc_grp_id = $result['acc_grp_id'];
        
        $bill_master_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
        $bill_txn_tbl = $this->company_id.'_billstxnnn_'.$this->session->get('ses_comp_fy_id');
        
        $builder = $this->db->table($bill_master_tbl);
        $builder->select($bill_master_tbl.'.*');
        
		if($this->session->get('ses_boid')!=''){
			$bo_id =  $this->session->get('ses_boid');
			$builder->join( '(SELECT `bills_ref_id`, MIN(`bills_txn_date`) as `start_date` from `'.$bill_txn_tbl.'` where `bo_id`="'.$bo_id.'" GROUP BY `bills_ref_id`) AS `billstxnnn`',
                        $bill_master_tbl.'.`bills_ref_id` = `billstxnnn`.`bills_ref_id`','INNER', NULL); 
		}
		else{
        $builder->join( '(SELECT `bills_ref_id`, MIN(`bills_txn_date`) as `start_date` from `'.$bill_txn_tbl.'` GROUP BY `bills_ref_id`) AS `billstxnnn`',
                        $bill_master_tbl.'.`bills_ref_id` = `billstxnnn`.`bills_ref_id`','INNER', NULL); 
		}			
        $builder->select('`billstxnnn`.`start_date`');
        
        $builder->where('acc_id', $account_id);
		if($this->session->get('ses_boid')!='')
		$builder->where($bill_master_tbl.'.bo_id', $this->session->get('ses_boid'));
        
        if($to_date != '')
            $builder->where('start_date <=', $to_date);
            
        $builder->orderBy('`billstxnnn`.`start_date`', 'asc');
        $result = $builder->get()->getResultArray();
        
        $final = [];
        
        foreach($result as $key => $value)
        {
            $total_amount = '';
            $receivable = '';
            $payable = '';
            $due_date = '';
            $overdue_days = '';
            $dated = '';
            $count_total = 0;
            $count_receivable = 0;
            $count_payable = 0;
            
            
            if($value['start_date']){
                $dated = date("d-m-Y", strtotime($value['start_date']));
            }

            if(in_array($acc_grp_id, $sundry_creditors)) //creditors
            {
                $builder = $this->db->table($bill_txn_tbl);
                $builder->select('sum(bills_txn_amt) as total_amount');
                $builder->where('bills_txn_drcr', 'C');
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
                $builder->where('bills_ref_id', $value['bills_ref_id']);
                if($to_date != '')
                    $builder->where('bills_txn_date <=', $to_date);
                $result2 = $builder->get()->getRowArray();
                
                if(!empty($result2['total_amount'])){
                    $total_amount = formatAmount($result2['total_amount']).' CR';
                    $count_total = -$result2['total_amount'];
                }
                else
                    $total_amount = '0.00 CR';
            }
            if(in_array($acc_grp_id, $sundry_debitors)) //debitors
            {
                $builder = $this->db->table($bill_txn_tbl);
                $builder->select('sum(bills_txn_amt) as total_amount');
                $builder->where('bills_txn_drcr', 'D');
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
                $builder->where('bills_ref_id', $value['bills_ref_id']);
                if($to_date != '')
                    $builder->where('bills_txn_date <=', $to_date);
                $result2 = $builder->get()->getRowArray();
                
                if(!empty($result2['total_amount'])){
                    $total_amount = formatAmount($result2['total_amount']).' DR';
                    $count_total = $result2['total_amount'];
                }
                else
                    $total_amount = '0.00 DR';
            }
            
            
            
            $builder = $this->db->table($bill_txn_tbl); 
            $builder->select('bills_txn_bal'); // bills_txn_bal
            $builder->where('bills_ref_id', $value['bills_ref_id']);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
            if($to_date != '')
                $builder->where('bills_txn_date <=', $to_date);
            $builder->orderBy('bills_txn_date', 'desc');
            $builder->orderBy('bills_txn_id', 'desc');
            $builder->limit(1);
            $bill_txn = $builder->get()->getRowArray();
            
            if($bill_txn){
                $balance = $bill_txn['bills_txn_bal'];// bills_txn_bal

                if($balance == 0 && $nil_type == 0){
                	continue;
                }

                if($balance >= 0){
                    $receivable = formatAmount($balance).' DR';
                    $count_receivable = $balance;
                    if($bill_type == 'P'){
                        continue;
                    }
                }
                else{
                    $payable = formatAmount(abs($balance)).' CR';
                    $count_payable = abs($balance);
                    if($bill_type == 'R'){
                        continue;
                    }
                }
            }
            
            if($value['bill_due_date']){
                $due_date = date("d-m-Y", strtotime($value['bill_due_date']));
                
                $now = time();
                $your_date = strtotime($value['bill_due_date']);
                $datediff = $now - $your_date;
                
                $overdue_days = round($datediff / (60 * 60 * 24));
            }

            $final[] = [
                'bills_ref_id'     =>   $value['bills_ref_id'],
                'ref_no'           =>   $value['bills_ref_name'],
                'total_amount'     =>   $total_amount,
                'receivable'       =>   $receivable,
                'payable'          =>   $payable,
                'date'             =>   $dated,
                'due_date'         =>   $due_date,
                'overdue_days'     =>   $overdue_days,
                'count_total'      =>   $count_total,
                'count_receivable' =>   $count_receivable,
                'count_payable'    =>   $count_payable,
            ];
        }
        // 		echo "<pre>";print_r($final);exit;
        $total_Records = count($final);
        $final_records = array_slice( $final, $offset, $limit );
        
        echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($final_records)."}";
    }
    
    function load_bills_management_groups($pq_curPage, $limit, $offset, $from_date, $to_date, $group_id,$bill_type)
    {
        $sundry_creditors = $this->get_sundry_creditors();
        $sundry_debitors = $this->get_sundry_debitors();
        
        $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
        
        $builder = $this->db->table($account_master_tbl); 
        $builder->select('acc_id, acc_name');
        $builder->where('comp_id', $this->company_id);		
        $builder->where('acc_grp_id', $group_id);
        $result_ = $builder->get()->getResultArray();
        
        $final = [];
        
        foreach($result_ as $key_ => $value_)
        {
            $account_id = $value_['acc_id'];
            $account_name = $value_['acc_name'];
            
            $bill_master_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
            $bill_txn_tbl = $this->company_id.'_billstxnnn_'.$this->session->get('ses_comp_fy_id');
            
            $builder = $this->db->table($bill_master_tbl);
            $builder->select($bill_master_tbl.'.*');
            if($this->session->get('ses_boid')!=''){
			$bo_id =  $this->session->get('ses_boid');
			$builder->join( '(SELECT `bills_ref_id`, MIN(`bills_txn_date`) as `start_date` from `'.$bill_txn_tbl.'` where `bo_id`="'.$bo_id.'" GROUP BY `bills_ref_id`) AS `billstxnnn`',
                            $bill_master_tbl.'.`bills_ref_id` = `billstxnnn`.`bills_ref_id`','INNER', NULL);
			}
			else{
            $builder->join( '(SELECT `bills_ref_id`, MIN(`bills_txn_date`) as `start_date` from `'.$bill_txn_tbl.'` GROUP BY `bills_ref_id`) AS `billstxnnn`',
                            $bill_master_tbl.'.`bills_ref_id` = `billstxnnn`.`bills_ref_id`','INNER', NULL);            
            }
			
			$builder->select('`billstxnnn`.`start_date`');
			
            
            $builder->where('acc_id', $account_id);
			if($this->session->get('ses_boid')!='')
			$builder->where($bill_master_tbl.'.bo_id', $this->session->get('ses_boid'));
            
            if($to_date != '')
                $builder->where('start_date <=', $to_date);
                
            $builder->orderBy('`billstxnnn`.`start_date`', 'asc');
            $result = $builder->get()->getResultArray();
            
            
            foreach($result as $key => $value)
            {
                $total_amount = '';
                $receivable = '';
                $payable = '';
                $due_date = '';
                $overdue_days = '';
                $dated = '';
                $count_total = 0;
                $count_receivable = 0;
                $count_payable = 0;
                
                if($value['start_date']){
                    $dated = date("d-m-Y", strtotime($value['start_date']));
                }
                
                if(in_array($group_id, $sundry_creditors))
                {
                    $builder = $this->db->table($bill_txn_tbl);
                    $builder->select('sum(bills_txn_amt) as total_amount');
                    $builder->where('bills_txn_drcr', 'C');
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
                    $builder->where('bills_ref_id', $value['bills_ref_id']);
                    if($to_date != '')
                        $builder->where('bills_txn_date <=', $to_date);
                    $result2 = $builder->get()->getRowArray();
                    if(!empty($result2['total_amount'])){
                       $total_amount = formatAmount($result2['total_amount']).' CR';
                       $count_total = -$result2['total_amount'];
                    }
                        
                    else
                        $total_amount = '0 CR';
                }
                if(in_array($group_id, $sundry_debitors))
                {
                    $builder = $this->db->table($bill_txn_tbl);
                    $builder->select('sum(bills_txn_amt) as total_amount');
                    $builder->where('bills_txn_drcr', 'D');
					if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
                    $builder->where('bills_ref_id', $value['bills_ref_id']);
                    if($to_date != '')
                        $builder->where('bills_txn_date <=', $to_date);
                    $result2 = $builder->get()->getRowArray();
                    if(!empty($result2['total_amount'])){
                        $total_amount = formatAmount($result2['total_amount']).' DR';
                        $count_total = $result2['total_amount'];
                    }
                    else
                        $total_amount = '0 DR';
                }
                
                $builder = $this->db->table($bill_txn_tbl); 
                $builder->select('bills_txn_bal'); // bills_txn_bal
                $builder->where('bills_ref_id', $value['bills_ref_id']);
                if($to_date != '')
                    $builder->where('bills_txn_date <=', $to_date);
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));

                $builder->orderBy('bills_txn_date', 'desc');
                $builder->orderBy('bills_txn_id', 'desc');
                $builder->limit(1);
                $bill_txn = $builder->get()->getRowArray();
                if($bill_txn){
                    $balance = $bill_txn['bills_txn_bal'];// bills_txn_bal
                    if($balance >= 0){
                        $receivable = formatAmount($balance).' DR';
                        $count_receivable = $balance;
                        if($bill_type == 'P'){
                            continue;
                        }
                    }
                    else{
                        $payable = formatAmount(abs($balance)).' CR';
                        $count_payable = abs($balance);
                        if($bill_type == 'R'){
                            continue;
                        }
                    }
                }
                
                if($value['bill_due_date']){
                    $due_date = date("d-m-Y", strtotime($value['bill_due_date']));
                    
                    $now = $to_date != '' ? strtotime($to_date) : time();
                    $your_date = strtotime($value['bill_due_date']);
                    $datediff = $now - $your_date;
                    
                    $overdue_days = round($datediff / (60 * 60 * 24));
                }
    
                $final[] = [
                    'account_name'     =>   $account_name,
                    'bills_ref_id'     =>   $value['bills_ref_id'],
                    'ref_no'           =>   $value['bills_ref_name'],
                    'total_amount'     =>   $total_amount,
                    'receivable'       =>   $receivable,
                    'payable'          =>   $payable,
                    'date'             =>   $dated,
                    'due_date'         =>   $due_date,
                    'overdue_days'     =>   $overdue_days,
                    'count_total'      =>   $count_total,
                    'count_receivable' =>   $count_receivable,
                    'count_payable'    =>   $count_payable,
                ];
            }
            
        }
        
        
        // 		echo "<pre>";print_r($final);exit;
        $total_Records = count($final);
        $final_records = array_slice( $final, $offset, $limit );

        echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($final_records)."}";
    }
    
    function load_bills_management_statement($pq_curPage, $limit, $offset, $from_date, $to_date,$bill_type)
    {
        $sundry_creditors = $this->get_sundry_creditors();
        $sundry_debitors = $this->get_sundry_debitors();
        
        
        
        $bill_master_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
        $bill_txn_tbl = $this->company_id.'_billstxnnn_'.$this->session->get('ses_comp_fy_id');
        $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
        
        $builder = $this->db->table($bill_master_tbl);
        $builder->select($bill_master_tbl.'.*');
        
        $builder->join($account_master_tbl, $account_master_tbl.'.acc_id = '.$bill_master_tbl.'.acc_id','INNER');
        $builder->select('acc_name, acc_grp_id');
        
		if($this->session->get('ses_boid')!=''){
		$bo_id = $this->session->get('ses_boid');
		 $builder->join( '(SELECT `bills_ref_id`, MIN(`bills_txn_date`) as `start_date` from `'.$bill_txn_tbl.'` where `bo_id`="'.$bo_id.'" GROUP BY `bills_ref_id`) AS `billstxnnn`',
                        $bill_master_tbl.'.`bills_ref_id` = `billstxnnn`.`bills_ref_id`','INNER', NULL);   
		}
        else{
        $builder->join( '(SELECT `bills_ref_id`, MIN(`bills_txn_date`) as `start_date` from `'.$bill_txn_tbl.'` GROUP BY `bills_ref_id`) AS `billstxnnn`',
                        $bill_master_tbl.'.`bills_ref_id` = `billstxnnn`.`bills_ref_id`','INNER', NULL);            
		}
        $builder->select('`billstxnnn`.`start_date`');
        
		if($this->session->get('ses_boid')!='')
		$builder->where($bill_master_tbl.'.bo_id', $this->session->get('ses_boid'));

        if($to_date != '')
            $builder->where('start_date <=', $to_date);
            
        $builder->orderBy('`billstxnnn`.`start_date`', 'asc');
        // $builder->limit($limit,$offset);
        $result = $builder->get()->getResultArray();
        
        $final = [];
        
        foreach($result as $key => $value)
        {
            $total_amount = '';
            $receivable = '';
            $payable = '';
            $due_date = '';
            $overdue_days = '';
            $dated = '';
            $count_total = 0;
            $count_receivable = 0;
            $count_payable = 0;
            
            
            if($value['start_date']){
                $dated = date("d-m-Y", strtotime($value['start_date']));
            }

            if(in_array($value['acc_grp_id'], $sundry_creditors)) //creditors
            {
                $builder = $this->db->table($bill_txn_tbl);
                $builder->select('sum(bills_txn_amt) as total_amount');
                $builder->where('bills_txn_drcr', 'C');
                $builder->where('bills_ref_id', $value['bills_ref_id']);
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
                if($to_date != '')
                    $builder->where('bills_txn_date <=', $to_date);
                $result2 = $builder->get()->getRowArray();
                
                if(!empty($result2['total_amount'])){
                    $total_amount = formatAmount($result2['total_amount']).' CR';
                    $count_total = -$result2['total_amount'];
                }
                else
                    $total_amount = '0.00 CR';
            }
            if(in_array($value['acc_grp_id'], $sundry_debitors)) //debitors
            {
                $builder = $this->db->table($bill_txn_tbl);
                $builder->select('sum(bills_txn_amt) as total_amount');
                $builder->where('bills_txn_drcr', 'D');
				if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
				
                $builder->where('bills_ref_id', $value['bills_ref_id']);
                if($to_date != '')
                    $builder->where('bills_txn_date <=', $to_date);
                $result2 = $builder->get()->getRowArray();
                
                if(!empty($result2['total_amount'])){
                    $total_amount = formatAmount($result2['total_amount']).' DR';
                    $count_total = $result2['total_amount'];
                }
                else
                    $total_amount = '0.00 DR';
            }
            
            
            
            $builder = $this->db->table($bill_txn_tbl); 
            $builder->select('bills_txn_bal'); // bills_txn_bal
            $builder->where('bills_ref_id', $value['bills_ref_id']);
            if($to_date != '')
                $builder->where('bills_txn_date <=', $to_date);
			if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));

            $builder->orderBy('bills_txn_date', 'desc');
            $builder->orderBy('bills_txn_id', 'desc');
            $builder->limit(1);
            $bill_txn = $builder->get()->getRowArray();
            
            if($bill_txn){
                $balance = $bill_txn['bills_txn_bal'];// bills_txn_bal
                if($balance >= 0){
                    $receivable = formatAmount($balance).' DR';
                    $count_receivable = $balance;
                    if($bill_type == 'P'){
                        continue;
                    }
                }
                else{
                    $payable = formatAmount(abs($balance)).' CR';
                    $count_payable = abs($balance);
                    if($bill_type == 'R'){
                        continue;
                    }
                }
            }
            
            if($value['bill_due_date']){
                $due_date = date("d-m-Y", strtotime($value['bill_due_date']));
                
                $now = time();
                $your_date = strtotime($value['bill_due_date']);
                $datediff = $now - $your_date;
                
                $overdue_days = round($datediff / (60 * 60 * 24));
            }

            $final[] = [
                'bills_ref_id'     =>   $value['bills_ref_id'],
                'account_name'     =>   $value['acc_name'],
                'ref_no'           =>   $value['bills_ref_name'],
                'total_amount'     =>   $total_amount,
                'receivable'       =>   $receivable,
                'payable'          =>   $payable,
                'date'             =>   $dated,
                'due_date'         =>   $due_date,
                'overdue_days'     =>   $overdue_days.'&nbsp;&nbsp;',
                'count_total'      =>   $count_total,
                'count_receivable' =>   $count_receivable,
                'count_payable'    =>   $count_payable,
            ];
        }
        // 		echo "<pre>";print_r($final);exit;
        $total_Records = count($final);
        $final_records = array_slice( $final, $offset, $limit );
        
        echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($final_records)."}";
    }
    
    function load_bills_management_details($pq_curPage, $limit, $offset, $from_date, $to_date, $bills_ref_id)
    {
        $bill_txn_tbl = $this->company_id.'_billstxnnn_'.$this->session->get('ses_comp_fy_id');
        $final = [];

        $show_opening = true;
        if($from_date != ''){
        	  $builder = $this->db->table($bill_txn_tbl);
	        $builder->select('*');
	        $builder->where('bills_ref_id', $bills_ref_id);
	        $builder->where('bills_txn_date <', $from_date);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
	        $count_transaction = $builder->countAllResults();
	        if($count_transaction > 0){
	        		$show_opening = false;
	        }
        }

        if($from_date == '' || $show_opening)
        {
        		$bill_master_tbl = $this->company_id.'_billmaster_'.$this->session->get('ses_comp_fy_id');
            $builder = $this->db->table($bill_master_tbl);
            $builder->select('*');
            $builder->where('bills_ref_id', $bills_ref_id);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
            $result = $builder->get()->getRowArray();
            
            if($result)
            {
                $balance = '';

                if($result['bill_op_bal'] >= 0){
                    $balance = formatAmount($result['bill_op_bal']).' DR';
                }
                else{
                    $balance = formatAmount(abs($result['bill_op_bal'])).' CR';
                }
                
                $final[] = [
                    'voucher_type'     =>   'BILL OPENING BALANCE',
                    'debit'     			=>   '',
                    'credit'      		=>   '',
                    'balance'          =>   $balance.'&nbsp;&nbsp;',
                    'voucher_txn_id'   =>   0,
                    'voucher_type_id'  =>   -1,
                    'voucher_date'     =>   '',
                    'debit_total'      =>   0,
                    'credit_total'     =>   0,
                ];
            }
        }
        
        if($from_date != '')
        {
            $builder = $this->db->table($bill_txn_tbl);
            $builder->select('*');
            $builder->where('bills_ref_id', $bills_ref_id);
            $builder->where('bills_txn_date <', $from_date);
            $builder->orderBy('bills_txn_date', 'desc');
            $builder->orderBy('bills_txn_id', 'desc');
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
            $builder->limit(1);
            $result = $builder->get()->getRowArray();
            
            if($result)
            {
                $balance = '';
                
                if($result['bills_txn_bal'] >= 0){
                    $balance = formatAmount($result['bills_txn_bal']).' DR';
                }
                else{
                    $balance = formatAmount(abs($result['bills_txn_bal'])).' CR';
                }
                
                $final[] = [
                    'voucher_type'     =>   'OPENING BALANCE',
                    'debit'     			=>   '',
                    'credit'      		=>   '',
                    'balance'          =>   $balance.'&nbsp;&nbsp;',
                    'voucher_txn_id'   =>   0,
                    'voucher_type_id'  =>   0,
                    'voucher_date'     =>   '',
                    'debit_total'      =>   0,
                    'credit_total'     =>   0,
                ];
            }
        }

        $builder = $this->db->table($bill_txn_tbl);
        $builder->select('*');
        $builder->where('bills_ref_id', $bills_ref_id);
        if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
        if($from_date != '')
            $builder->where('bills_txn_date >=', $from_date);
        if($to_date != '')
            $builder->where('bills_txn_date <=', $to_date);
            
        $builder->orderBy('bills_txn_date', 'asc');
        $builder->orderBy('bills_txn_id', 'asc');
        $result2 = $builder->get()->getResultArray();
        
        foreach($result2 as $key2 => $value2)
        {
            $debit = '';
            $credit = '';
            $balance = '';
            $debit_total = 0;
            $credit_total = 0;
            $drcr = ' DR';

            
            $voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
            $builder = $this->db->table($voucher_type_tbl);
            $builder->select('comp_vch_type');
            $builder->where('voucher_type_id',$value2['voucher_type_id']);
            $voucher_type = $builder->get()->getRowArray();
            
            if($value2['bills_txn_drcr'] == 'C'){
                $credit = formatAmount($value2['bills_txn_amt']);
                $credit_total = $value2['bills_txn_amt'];
            }
            if($value2['bills_txn_drcr'] == 'D'){
                $debit = formatAmount($value2['bills_txn_amt']);
                $debit_total = $value2['bills_txn_amt'];
            }

            if($value2['bills_txn_bal'] >= 0)
                $balance = formatAmount($value2['bills_txn_bal']).' DR';
            else
                $balance = formatAmount(abs($value2['bills_txn_bal'])).' CR';
            

            $voucher_date = date("d-m-Y", strtotime($value2['bills_txn_date']));
            
            $final[] = [
                'voucher_type'     =>   $voucher_type['comp_vch_type'],
                'debit'     		  =>   $debit,
                'credit'      	  =>   $credit,
                'balance'          =>   $balance.'&nbsp;&nbsp;',
                'voucher_txn_id'   =>   $value2['voucher_txn_id'],
                'voucher_type_id'  =>   $value2['voucher_type_id'],
                'voucher_date'     =>   $voucher_date,
                'debit_total'      =>   $debit_total,
                'credit_total'     =>   $credit_total,
            ];
        }
        

        $total_Records = count($final);
        $final_records = array_slice( $final, $offset, $limit );
        echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($final_records)."}";
    }
    
    function cc_info($id)
    {
        $cc_mst_tbl = $this->company_id.'_costctmstr_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!='')
    	$result = $this->db->table($cc_mst_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('cc_id', $id)->get()->getRowArray();
		else	
        $result = $this->db->table($cc_mst_tbl)->where('cc_id', $id)->get()->getRowArray();
        return $result;
    }
    function get_last_cc_txn($id, $date)
    {
        $cc_txn_tbl = $this->company_id.'_costcttxnn_'.$this->session->get('ses_comp_fy_id');
		
		if($this->session->get('ses_boid')!=''){
		  $result = $this->db->table($cc_txn_tbl)
                           ->where('cc_id', $id)->where('cc_txn_date <', $date)
						   ->where('bo_id', $this->session->get('ses_boid'))
                           ->orderBy('cc_txn_date', 'desc')->orderBy('cc_txn_id', 'desc')
                           ->limit(1)
                           ->get()->getRowArray();	
		}
		else{
        $result = $this->db->table($cc_txn_tbl)
                           ->where('cc_id', $id)->where('cc_txn_date <', $date)
                           ->orderBy('cc_txn_date', 'desc')->orderBy('cc_txn_id', 'desc')
                           ->limit(1)
                           ->get()->getRowArray();
		}
        return $result;
    }
    function get_last_bill_sundry_txn($id, $date)
    {
    	$sundry_txn_tbl = $this->company_id.'_sundrytxnn_'.$id.'_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!=''){
			$result = $this->db->table($sundry_txn_tbl)
                           ->where('bill_sundry_id', $id)->where('sundry_txn_date <', $date)
						   ->where('bo_id', $this->session->get('ses_boid'))
                           ->orderBy('sundry_txn_date', 'desc')->orderBy('sundry_txn_id', 'desc')
                           ->limit(1)
                           ->get()->getRowArray();
		}else{
	  $result = $this->db->table($sundry_txn_tbl)
                           ->where('bill_sundry_id', $id)->where('sundry_txn_date <', $date)
                           ->orderBy('sundry_txn_date', 'desc')->orderBy('sundry_txn_id', 'desc')
                           ->limit(1)
                           ->get()->getRowArray();
		}
        return $result;
    }
    
    function cc_group_info($id)
    {
        $cc_grp_tbl = $this->company_id.'_costctgrup_'.$this->session->get('ses_comp_fy_id');
        $result = $this->db->table($cc_grp_tbl)->where('cc_grp_id', $id)->get()->getRowArray();
        return $result;
    }

    function load_bill_sundry_ledger($pq_curPage, $limit, $offset, $from_date, $to_date, $id)
    {
        $sundry_txn_tbl = $this->company_id.'_sundrytxnn_'.$id.'_'.$this->session->get('ses_comp_fy_id');
        $voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
        $vhtxn_conso_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
        
        $builder = $this->db->table($sundry_txn_tbl);
        $builder->select($sundry_txn_tbl.'.*');
        $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$sundry_txn_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($vhtxn_conso_tbl, $vhtxn_conso_tbl.'.voucher_txn_id ='.$sundry_txn_tbl.'.voucher_txn_id');
	    $builder->select($vhtxn_conso_tbl.'.comp_vch_no');
	    $builder->where('sundry_txn_date >=', $from_date);
        $builder->where('sundry_txn_date <=', $to_date);
		if($this->session->get('ses_boid')!='')
  		$builder->where($sundry_txn_tbl.'.bo_id', $this->session->get('ses_boid'));
	    if($this->session->get('ses_boid')!='')
		$builder->where($vhtxn_conso_tbl.'.bo_id', $this->session->get('ses_boid'));
        $builder->where('bill_sundry_id', $id);
        $builder->orderBy('sundry_txn_date', 'asc');
        $builder->orderBy('sundry_txn_id', 'asc');
        $total_Records = $builder->countAllResults();
        
        $builder = $this->db->table($sundry_txn_tbl);
        $builder->select($sundry_txn_tbl.'.*');
        $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$sundry_txn_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($vhtxn_conso_tbl, $vhtxn_conso_tbl.'.voucher_txn_id ='.$sundry_txn_tbl.'.voucher_txn_id');
	    $builder->select($vhtxn_conso_tbl.'.comp_vch_no');
		if($this->session->get('ses_boid')!='')
		$builder->where($sundry_txn_tbl.'.bo_id', $this->session->get('ses_boid'));
	if($this->session->get('ses_boid')!='')
		$builder->where($vhtxn_conso_tbl.'.bo_id', $this->session->get('ses_boid'));
	    $builder->where('sundry_txn_date >=', $from_date);
        $builder->where('sundry_txn_date <=', $to_date);
        $builder->where('bill_sundry_id', $id);
        $builder->orderBy('sundry_txn_date', 'asc');
        $builder->orderBy('sundry_txn_id', 'asc');
        $builder->limit($limit,$offset);
        $result = $builder->get()->getResultArray();
        
        $final = [];
        foreach($result as $key => $value)
        {
            $debit = '';
            $credit = '';
            $balance = '';
            
            if($value['sundry_txn_drcr'] == 'd'){
                $debit = formatAmount($value['sundry_txn_amount']);
            }
            if($value['sundry_txn_drcr'] == 'c'){
                $credit = formatAmount($value['sundry_txn_amount']);
            }
	        
	        if($value['sundry_bal'] >= 0){
	            $balance = formatAmount($value['sundry_bal']).' DR'; 
	        }
	        else{
	            $balance = formatAmount(abs($value['sundry_bal'])).' CR';
	        }
	        
	        $voucher_date = date("d-m-Y", strtotime($value['sundry_txn_date']));
	        
	        $final[] = [
	                'voucher_txn_id'    => $value['voucher_txn_id'],
	                'voucher_type_id'   => $value['voucher_type_id'],
	                'voucher_no'        => $value['comp_vch_no'],
	                'voucher_type'      => $value['comp_vch_type'],
	                'voucher_date'      => $voucher_date,
	                'debit'             => $debit,
	                'credit'            => $credit,
	                'balance'           => $balance,
	            ];
        }
        echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($final)."}";
        
    }

    function load_bill_sundry_summary($bsd_id)
    {
        $fy_months_list =  $this->CommonModel->get_fy_info($this->session->get('ses_comp_fy_id'),$this->company_id);
        $start_date     = strtotime($fy_months_list['fy_begndt']);
 	    $end_date       = strtotime($fy_months_list['fy_end']); 
 	    
 	    $months = [];
 	    while( $start_date <= $end_date ) {
		    $from_date = date( 'Y-m-01', $start_date );
		    $to_date = date( 'Y-m-t', $start_date );
		    $months[] = [
		            'from_date' => $from_date,
		            'to_date'   => $to_date
		        ];
            $start_date = strtotime( '+1 month', $start_date );
          }
          
        $final = [];
        $count_balance = 0;
        
        $bill_sundry_op_balance = $this->bill_sundry_op_balance($bsd_id);
        if(!empty($bill_sundry_op_balance['bsd_op_bal'])){
        	$count_balance = $bill_sundry_op_balance['bsd_op_bal'];
        }
        
        $sundry_txn_tbl = $this->company_id.'_sundrytxnn_'.$bsd_id.'_'.$this->session->get('ses_comp_fy_id');
        foreach($months as $key => $value)
        {
            $debit = '';
            $credit = '';
            $balance = '';
            
            $count_debit = 0;
            $count_credit = 0;
            
            $month = date("F", strtotime($value['from_date']));
            
            $builder = $this->db->table($sundry_txn_tbl);
            $builder->select('sum(sundry_txn_amount) as total');
            $builder->where('sundry_txn_drcr', 'd');
            $builder->where('bill_sundry_id', $bsd_id);
			if($this->session->get('ses_boid')!='')
			$builder->where($sundry_txn_tbl.'.bo_id', $this->session->get('ses_boid'));
            $builder->where('sundry_txn_date >=', $value['from_date']);
            $builder->where('sundry_txn_date <=', $value['to_date']);
            $sundry_txn = $builder->get()->getRowArray();
            if(!empty($sundry_txn) && $sundry_txn['total'] != null){
                $debit = formatAmount($sundry_txn['total']);
                $count_debit = $sundry_txn['total'];
            }
                
                
            $builder = $this->db->table($sundry_txn_tbl);
            $builder->select('sum(sundry_txn_amount) as total');
            $builder->where('sundry_txn_drcr', 'c');
            $builder->where('bill_sundry_id', $bsd_id);
			if($this->session->get('ses_boid')!='')
			$builder->where($sundry_txn_tbl.'.bo_id', $this->session->get('ses_boid'));
            $builder->where('sundry_txn_date >=', $value['from_date']);
            $builder->where('sundry_txn_date <=', $value['to_date']);
            $sundry_txn = $builder->get()->getRowArray();
            if(!empty($sundry_txn) && $sundry_txn['total'] != null){
                $credit = formatAmount($sundry_txn['total']);
                $count_credit = $sundry_txn['total'];
            }
                
            $count_balance = $count_debit - $count_credit + $count_balance;
            if($count_balance >= 0){
                $balance = formatAmount($count_balance).' DR';
            }
            else{
                $balance = formatAmount(abs($count_balance)).' CR';
            }
            
            $final[] = [
                'bsd_id'        => $bsd_id,
                'from_date'     => $value['from_date'],
                'to_date'       => $value['to_date'],
                'month'         => $month,
                'debit'         => $debit,
                'credit'        => $credit,
                'balance'       => $balance.'&nbsp;&nbsp;',
                'count_debit'   => $count_debit,
                'count_credit'  => $count_credit,
                ];
        }
        
        return $final;
    }
    
    function load_cost_centre_trial($pq_curPage, $limit, $from_date, $to_date)
    {
        $cc_mst_tbl = $this->company_id.'_costctmstr_'.$this->session->get('ses_comp_fy_id');
        $cc_txn_tbl = $this->company_id.'_costcttxnn_'.$this->session->get('ses_comp_fy_id');
        
        $builder = $this->db->table($cc_mst_tbl);
        $builder->where('comp_id', $this->company_id);
        $total_Records = $builder->countAllResults();
        
        $offset = ($limit * ($pq_curPage - 1));

            if ($offset > $total_Records)
            {        
                $pq_curPage = ceil($total_Records / $limit);
                $offset = ($limit * ($pq_curPage - 1));
            }
            
            
        $builder = $this->db->table($cc_mst_tbl);
        $builder->where('comp_id', $this->company_id);
		if($this->session->get('ses_boid')!='')
		$builder->where($cc_mst_tbl.'.bo_id', $this->session->get('ses_boid'));

        $builder->limit($limit,$offset);
        $result = $builder->get()->getResultArray();
        
        $final = [];
        if($result)
        {
            foreach($result as $key => $value)
            {
                $debit = '';
                $credit = '';
                $debit_total = '';
                $credit_total = '';
                $opening_balance = '';
                $closing_balance = '';
                
                $cc_name = $value['cc_name'];
                if($value['cc_op_drcr'] == 'dr')
                    $opening_balance = formatAmount($value['cc_op_bal']) . ' DR';
                if($value['cc_op_drcr'] == 'cr')
                    $opening_balance = formatAmount($value['cc_op_bal']) . ' CR';
                    
                 
                $builder = $this->db->table($cc_txn_tbl);
                $builder->select('sum(cc_txn_amt) as total');
                $builder->where('cc_txn_drcr', 'D');
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
				if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));

                $builder->where('cc_id', $value['cc_id']);
                if($to_date != '')
                    $builder->where('cc_txn_date <=', $to_date);
                $cc_txn = $builder->get()->getRowArray();
                
                if(!empty($cc_txn) && $cc_txn['total'] != null){
                    $debit = formatAmount($cc_txn['total']);
                    $debit_total = $cc_txn['total'];
                }
                    
                $builder = $this->db->table($cc_txn_tbl);
                $builder->select('sum(cc_txn_amt) as total');
                $builder->where('cc_txn_drcr', 'C');
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
                $builder->where('cc_id', $value['cc_id']);
                if($to_date != '')
                    $builder->where('cc_txn_date <=', $to_date);
                $cc_txn = $builder->get()->getRowArray();
                if(!empty($cc_txn) && $cc_txn['total'] != null){
                    $credit = formatAmount($cc_txn['total']);
                    $credit_total = $cc_txn['total'];
                }
                    
                $builder = $this->db->table($cc_txn_tbl);
                $builder->select('cc_txn_bal');
                $builder->where('cc_id', $value['cc_id']);
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
                if($to_date != '')
                    $builder->where('cc_txn_date <=', $to_date);
                $builder->orderBy('cc_txn_date', 'desc');
                $builder->orderBy('cc_txn_id', 'desc');
                $cc_txn = $builder->get()->getRowArray();
                if($cc_txn)
                {
                    $closing_balance = $cc_txn['cc_txn_bal'];
                    if($closing_balance >= 0){
                        $closing_balance = formatAmount($cc_txn['cc_txn_bal']) . ' DR';
                    }
                    if($closing_balance < 0){
                        $closing_balance = formatAmount(abs($cc_txn['cc_txn_bal'])) . ' CR';
                    }
                }
                    
                $final[] = [
                        'cc_id'             => $value['cc_id'],
                        'cc_name'           => $cc_name,
                        'opening_balance'   => $opening_balance,
                        'debit'             => $debit,
                        'credit'            => $credit,
                        'debit_total'       => $debit_total,
                        'credit_total'      => $credit_total,
                        'closing_balance'   => $closing_balance,
                    ]; 
            }
  
        }
        
        echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($final)."}";
    }
    
    function load_cost_centre_trial_group($pq_curPage, $limit, $from_date, $to_date, $group_id)
    {  
        $final = [];
        $cc_grp_tbl = $this->company_id.'_costctgrup_'.$this->session->get('ses_comp_fy_id');
        $cc_mst_tbl = $this->company_id.'_costctmstr_'.$this->session->get('ses_comp_fy_id');
        $cc_txn_tbl = $this->company_id.'_costcttxnn_'.$this->session->get('ses_comp_fy_id');
        
        $builder = $this->db->table($cc_grp_tbl);
        $builder->select($cc_grp_tbl.'.cc_grp_name');
        $builder->join($cc_mst_tbl, $cc_mst_tbl.'.cc_grp_id='.$cc_grp_tbl.'.cc_grp_id');
        $builder->select($cc_mst_tbl.'.*');
        $builder->where($cc_mst_tbl.'.comp_id', $this->company_id);
        if($group_id)
            $builder->where($cc_mst_tbl.'.cc_grp_id', $group_id);
		
		if($this->session->get('ses_boid')!='')
		$builder->where($cc_mst_tbl.'.bo_id', $this->session->get('ses_boid'));
		
        $builder->orderBy($cc_mst_tbl.'.cc_grp_id', 'asc');
        $total_Records = $builder->countAllResults();
        
		if($pq_curPage==0)
			 $pq_curPage=1;
        $offset = ($limit * ($pq_curPage - 1));

            if ($offset > $total_Records)
            {        
                $pq_curPage = ceil($total_Records / $limit);
                $offset = ($limit * ($pq_curPage - 1));
            }
        
        $builder = $this->db->table($cc_grp_tbl);
        $builder->select($cc_grp_tbl.'.cc_grp_name');
        $builder->join($cc_mst_tbl, $cc_mst_tbl.'.cc_grp_id='.$cc_grp_tbl.'.cc_grp_id');
        $builder->select($cc_mst_tbl.'.*');
        $builder->where($cc_mst_tbl.'.comp_id', $this->company_id);
		if($this->session->get('ses_boid')!='')
		$builder->where($cc_mst_tbl.'.bo_id', $this->session->get('ses_boid'));
        if($group_id)
            $builder->where($cc_mst_tbl.'.cc_grp_id', $group_id);
        $builder->orderBy($cc_mst_tbl.'.cc_grp_id', 'asc');
        $builder->limit($limit,$offset);
        
        $result = $builder->get()->getResultArray();
        
        
        if($result)
        {
            foreach($result as $key => $value)
            {
                $debit = '';
                $credit = '';
                $opening_balance = '';
                $closing_balance = '';
                
                $cc_grp_name = $value['cc_grp_name'];
                $cc_name = $value['cc_name'];
                if($value['cc_op_drcr'] == 'dr')
                    $opening_balance = formatAmount($value['cc_op_bal']) . ' DR';
                if($value['cc_op_drcr'] == 'cr')
                    $opening_balance = formatAmount($value['cc_op_bal']) . ' CR';
                    
                 
                $builder = $this->db->table($cc_txn_tbl);
                $builder->select('sum(cc_txn_amt) as total');
                $builder->where('cc_txn_drcr', 'D');
				if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));

                $builder->where('cc_id', $value['cc_id']);
                if($to_date != '')
                    $builder->where('cc_txn_date <=', $to_date);
                $cc_txn = $builder->get()->getRowArray();
                
                if(!empty($cc_txn) && $cc_txn['total'] != null)
                    $debit = formatAmount($cc_txn['total']) . ' DR';
                    
                $builder = $this->db->table($cc_txn_tbl);
                $builder->select('sum(cc_txn_amt) as total');
                $builder->where('cc_txn_drcr', 'C');
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
                $builder->where('cc_id', $value['cc_id']);
                if($to_date != '')
                    $builder->where('cc_txn_date <=', $to_date);
                $cc_txn = $builder->get()->getRowArray();
                if(!empty($cc_txn) && $cc_txn['total'] != null)
                    $credit = formatAmount($cc_txn['total']) . ' CR';
                    
                $builder = $this->db->table($cc_txn_tbl);
                $builder->select('cc_txn_bal');
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
                $builder->where('cc_id', $value['cc_id']);
                if($to_date != '')
                    $builder->where('cc_txn_date <=', $to_date);
                $builder->orderBy('cc_txn_date', 'desc');
                $builder->orderBy('cc_txn_id', 'desc');
                $cc_txn = $builder->get()->getRowArray();
                if($cc_txn)
                {
                    $closing_balance = $cc_txn['cc_txn_bal'];
                    if($closing_balance >= 0){
                        $closing_balance = formatAmount($cc_txn['cc_txn_bal']) . ' DR';
                    }
                    if($closing_balance < 0){
                        $closing_balance = formatAmount(abs($cc_txn['cc_txn_bal'])) . ' CR';
                    }
                }
                    
                $final[] = [
                        'cc_grp_id'         => $value['cc_grp_id'],
                        'cc_grp_name'       => $cc_grp_name,
                        'cc_id'             => $value['cc_id'],
                        'cc_name'           => $cc_name,
                        'opening_balance'   => $opening_balance,
                        'debit'             => $debit,
                        'credit'            => $credit,
                        'closing_balance'   => $closing_balance,
                    ]; 
            }
  
        }
        
        echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($final)."}";
    }
    function load_cost_centre_account_ledger($pq_curPage, $limit, $from_date, $to_date, $cc_id)
    {
        $cc_txn_tbl = $this->company_id.'_costcttxnn_'.$this->session->get('ses_comp_fy_id');
        $voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
        $vhtxn_conso_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
        
        $builder = $this->db->table($cc_txn_tbl);
        $builder->select($cc_txn_tbl.'.*');
        $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$cc_txn_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($vhtxn_conso_tbl, $vhtxn_conso_tbl.'.voucher_txn_id ='.$cc_txn_tbl.'.voucher_txn_id');
	    $builder->select($vhtxn_conso_tbl.'.comp_vch_no');
	    $builder->where('cc_txn_date >=', $from_date);
		if($this->session->get('ses_boid')!='')
        $builder->where($cc_txn_tbl.'.bo_id', $this->session->get('ses_boid'));
        if($this->session->get('ses_boid')!='')
		$builder->where($vhtxn_conso_tbl.'.bo_id', $this->session->get('ses_boid'));
	
        $builder->where('cc_txn_date <=', $to_date);
        $builder->where('cc_id', $cc_id);
        $total_Records = $builder->countAllResults();
        
        $pq_curPage = $pq_curPage == 0 ? 1 : $pq_curPage;
        $offset = ($limit * ($pq_curPage - 1));

            if ($offset > $total_Records)
            {        
                $pq_curPage = ceil($total_Records / $limit);
                $offset = ($limit * ($pq_curPage - 1));
            }
            
            
        
        $builder = $this->db->table($cc_txn_tbl);
        $builder->select($cc_txn_tbl.'.*');
        $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$cc_txn_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($vhtxn_conso_tbl, $vhtxn_conso_tbl.'.voucher_txn_id ='.$cc_txn_tbl.'.voucher_txn_id');
	    $builder->select($vhtxn_conso_tbl.'.comp_vch_no');
	    $builder->where('cc_txn_date >=', $from_date);
        $builder->where('cc_txn_date <=', $to_date);
		if($this->session->get('ses_boid')!='')
        $builder->where($cc_txn_tbl.'.bo_id', $this->session->get('ses_boid'));
        $builder->where('cc_id', $cc_id);
        $builder->orderBy('cc_txn_date', 'asc');
        $builder->orderBy('cc_txn_id', 'asc');
        $builder->limit($limit,$offset);
        $result = $builder->get()->getResultArray();
        
        $final = [];
        foreach($result as $key => $value)
        {
            $debit = '';
            $credit = '';
            $balance = '';
            $debit_total = 0;
            $credit_total = 0;
            
            if($value['cc_txn_drcr'] == 'D'){
                $debit = formatAmount($value['cc_txn_amt']);
                $debit_total = parseAmount($value['cc_txn_amt']);
            }
            if($value['cc_txn_drcr'] == 'C'){
                $credit = formatAmount($value['cc_txn_amt']);
                $credit_total = parseAmount($value['cc_txn_amt']);
            }
	        
	        if($value['cc_txn_bal'] >= 0){
	            $balance = formatAmount($value['cc_txn_bal']).' DR &nbsp;&nbsp;'; 
	        }
	        else{
	            $balance = formatAmount(abs($value['cc_txn_bal'])).' CR &nbsp;&nbsp;';
	        }
	        
	        $voucher_date = date("d-m-Y", strtotime($value['cc_txn_date']));
	        
	        $final[] = [
	                'voucher_txn_id'    => $value['voucher_txn_id'],
	                'voucher_type_id'   => $value['voucher_type_id'],
	                'voucher_no'        => $value['comp_vch_no'],
	                'voucher_type'      => $value['comp_vch_type'],
	                'voucher_date'      => $voucher_date,
	                'debit'             => $debit,
	                'credit'            => $credit,
	                'balance'           => $balance,
	                'debit_total'       => $debit_total,
	                'credit_total'      => $credit_total,
	            ];
        }
        echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($final)."}";
        
    }
    
    function load_cost_centre_group_ledger($pq_curPage, $limit, $from_date, $to_date, $group_id)
    {
        $cc_txn_tbl = $this->company_id.'_costcttxnn_'.$this->session->get('ses_comp_fy_id');
        $voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
        $vhtxn_conso_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
        $cc_mst_tbl = $this->company_id.'_costctmstr_'.$this->session->get('ses_comp_fy_id');
        
        $builder = $this->db->table($cc_txn_tbl);
        $builder->select($cc_txn_tbl.'.*');
        $builder->join($cc_mst_tbl, $cc_mst_tbl.'.cc_id ='.$cc_txn_tbl.'.cc_id');
	    $builder->select($cc_mst_tbl.'.cc_name');
        $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$cc_txn_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($vhtxn_conso_tbl, $vhtxn_conso_tbl.'.voucher_txn_id ='.$cc_txn_tbl.'.voucher_txn_id');
	    $builder->select($vhtxn_conso_tbl.'.comp_vch_no');
	    $builder->where('cc_txn_date >=', $from_date);
		if($this->session->get('ses_boid')!='')
		$builder->where($cc_mst_tbl.'.bo_id', $this->session->get('ses_boid'));
		if($this->session->get('ses_boid')!='')
		$builder->where($vhtxn_conso_tbl.'.bo_id', $this->session->get('ses_boid'));
        $builder->where('cc_txn_date <=', $to_date);
        $builder->where('cc_grp_id', $group_id);
        $builder->orderBy('cc_txn_date', 'asc');
        $total_Records = $builder->countAllResults();
        
        $offset = ($limit * ($pq_curPage - 1));

        if ($offset > $total_Records)
            {        
                $pq_curPage = ceil($total_Records / $limit);
                $offset = ($limit * ($pq_curPage - 1));
            }
            
            
        
        $builder = $this->db->table($cc_txn_tbl);
        $builder->select($cc_txn_tbl.'.*');
        $builder->join($cc_mst_tbl, $cc_mst_tbl.'.cc_id ='.$cc_txn_tbl.'.cc_id');
	    $builder->select($cc_mst_tbl.'.cc_name');
        $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$cc_txn_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($vhtxn_conso_tbl, $vhtxn_conso_tbl.'.voucher_txn_id ='.$cc_txn_tbl.'.voucher_txn_id');
	    $builder->select($vhtxn_conso_tbl.'.comp_vch_no');
	    $builder->where('cc_txn_date >=', $from_date);
        $builder->where('cc_txn_date <=', $to_date);
		if($this->session->get('ses_boid')!='')
        $builder->where($cc_txn_tbl.'.bo_id', $this->session->get('ses_boid'));
        $builder->where('cc_grp_id', $group_id);
        $builder->orderBy('cc_txn_date', 'asc');
        $builder->orderBy('cc_txn_id', 'asc');
        $builder->limit($limit,$offset);
        $result = $builder->get()->getResultArray();
        
        $final = [];
        foreach($result as $key => $value)
        {
            $debit = '';
            $credit = '';
            $balance = '';
            
            if($value['cc_txn_drcr'] == 'D'){
                $debit = formatAmount($value['cc_txn_amt']);
            }
            if($value['cc_txn_drcr'] == 'C'){
                $credit = formatAmount($value['cc_txn_amt']);
            }
	        
	        if($value['cc_txn_bal'] >= 0){
	            $balance = formatAmount($value['cc_txn_bal']).' DR &nbsp;&nbsp;'; 
	        }
	        else{
	            $balance = formatAmount(abs($value['cc_txn_bal'])).' CR &nbsp;&nbsp;';
	        }
	        
	        $voucher_date = date("d-m-Y", strtotime($value['cc_txn_date']));
	        
	        $final[] = [
	                'cc_name'           => $value['cc_name'],
	                'voucher_txn_id'    => $value['voucher_txn_id'],
	                'voucher_type_id'   => $value['voucher_type_id'],
	                'voucher_no'        => $value['comp_vch_no'],
	                'voucher_type'      => $value['comp_vch_type'],
	                'voucher_date'      => $voucher_date,
	                'debit'             => $debit,
	                'credit'            => $credit,
	                'balance'           => $balance,
	            ];
        }
        echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($final)."}";
        
    }
    
    function load_cost_centre_account_wise($pq_curPage, $limit, $from_date, $to_date)
    {
        $cc_txn_tbl = $this->company_id.'_costcttxnn_'.$this->session->get('ses_comp_fy_id');
        $cc_mst_tbl = $this->company_id.'_costctmstr_'.$this->session->get('ses_comp_fy_id');
        $acc_mst_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
        
        $builder = $this->db->table($cc_txn_tbl);
        $builder->select($cc_txn_tbl.'.cc_id,'.$cc_txn_tbl.'.acc_id');
        $builder->join($cc_mst_tbl, $cc_mst_tbl.'.cc_id ='.$cc_txn_tbl.'.cc_id');
	    $builder->select($cc_mst_tbl.'.cc_name');
	    $builder->join($acc_mst_tbl, $acc_mst_tbl.'.acc_id ='.$cc_txn_tbl.'.acc_id');
	    $builder->select($acc_mst_tbl.'.acc_name');
		if($this->session->get('ses_boid')!='')
		$builder->where($cc_mst_tbl.'.bo_id', $this->session->get('ses_boid'));
		if($this->session->get('ses_boid')!='')
		$builder->where($cc_txn_tbl.'.bo_id', $this->session->get('ses_boid'));
        $builder->groupBy('cc_id');
        $builder->groupBy('acc_id');
        $builder->orderBy('cc_id', 'asc');
        $total_Records = $builder->countAllResults();
        
        $offset = ($limit * ($pq_curPage - 1));

            if ($offset > $total_Records)
            {        
                $pq_curPage = ceil($total_Records / $limit);
                $offset = ($limit * ($pq_curPage - 1));
            }
            
            
        
        $builder = $this->db->table($cc_txn_tbl);
        $builder->select($cc_txn_tbl.'.cc_id,'.$cc_txn_tbl.'.acc_id');
        $builder->join($cc_mst_tbl, $cc_mst_tbl.'.cc_id ='.$cc_txn_tbl.'.cc_id');
	    $builder->select($cc_mst_tbl.'.cc_name');
	    $builder->join($acc_mst_tbl, $acc_mst_tbl.'.acc_id ='.$cc_txn_tbl.'.acc_id');
	    $builder->select($acc_mst_tbl.'.acc_name');
		if($this->session->get('ses_boid')!='')
        $builder->where($cc_txn_tbl.'.bo_id', $this->session->get('ses_boid'));
        $builder->groupBy('cc_id');
        $builder->groupBy('acc_id');
        $builder->orderBy('cc_id', 'asc');
        $builder->limit($limit,$offset);
        $result = $builder->get()->getResultArray();
        
        $final = [];
        foreach($result as $key => $value)
        {
            $cc_name = '';
            if($key == 0 || $result[$key]['cc_id'] != $result[$key-1]['cc_id']){
                $cc_name = $value['cc_name'];
            }
            
            
            $debit = '';
            $credit = '';
            $balance = '';
            
            $count_debit = 0;
            $count_credit = 0;
            $count_balance = 0;
            
            $builder = $this->db->table($cc_txn_tbl);
            $builder->select('sum(cc_txn_amt) as total');
            $builder->where('cc_id', $value['cc_id']);
            $builder->where('acc_id', $value['acc_id']);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
            $builder->where('cc_txn_drcr', 'D');
            $builder->where('cc_txn_date <=', $to_date);
            $data = $builder->get()->getRowArray();
            if(!empty($data) && $data['total'] != null){
                $debit = formatAmount($data['total']);
                $count_debit = $data['total'];
            }
            
            $builder = $this->db->table($cc_txn_tbl);
            $builder->select('sum(cc_txn_amt) as total');
            $builder->where('cc_id', $value['cc_id']);
            $builder->where('acc_id', $value['acc_id']);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
            $builder->where('cc_txn_drcr', 'C');
            $builder->where('cc_txn_date <=', $to_date);
            $data = $builder->get()->getRowArray();
            if(!empty($data) && $data['total'] != null){
                $credit = formatAmount($data['total']);
                $count_credit = $data['total'];
            }
            
            $count_balance = $count_debit - $count_credit;
            if($debit != '' || $credit != '')
            {
                if($count_balance >= 0){
                    $balance = formatAmount($count_balance).' DR &nbsp;&nbsp';
                }
                else{
                    $balance = formatAmount(abs($count_balance)).' CR &nbsp;&nbsp';
                }
            }
            
            $final[] = [
                    'cc_id'         => $value['cc_id'],
                    'cc_name'       => $cc_name,
                    'acc_id'        => $value['acc_id'],
                    'acc_name'      => $value['acc_name'],
                    'debit'         => $debit,
                    'credit'        => $credit,
                    'balance'       => $balance,
                    'count_debit'   => $count_debit,
                    'count_credit'  => $count_credit,
                    'count_balance' => $count_balance,
                ];
        }
        
        echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($final)."}";
    }
    
    function load_cost_centre_wise($pq_curPage, $limit, $offset, $from_date, $to_date)
    {
        $cc_txn_tbl = $this->company_id.'_costcttxnn_'.$this->session->get('ses_comp_fy_id');
        $cc_mst_tbl = $this->company_id.'_costctmstr_'.$this->session->get('ses_comp_fy_id');
        $acc_mst_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
        
        $builder = $this->db->table($cc_txn_tbl);
        $builder->select($cc_txn_tbl.'.cc_id,'.$cc_txn_tbl.'.acc_id');
        $builder->join($cc_mst_tbl, $cc_mst_tbl.'.cc_id ='.$cc_txn_tbl.'.cc_id');
	    $builder->select($cc_mst_tbl.'.cc_name');
	    $builder->join($acc_mst_tbl, $acc_mst_tbl.'.acc_id ='.$cc_txn_tbl.'.acc_id');
	    $builder->select($acc_mst_tbl.'.acc_name');
		if($this->session->get('ses_boid')!='')
		$builder->where($cc_mst_tbl.'.bo_id', $this->session->get('ses_boid'));
		if($this->session->get('ses_boid')!='')
		$builder->where($cc_txn_tbl.'.bo_id', $this->session->get('ses_boid'));
        $builder->groupBy('acc_id');
        $builder->groupBy('cc_id');
        $builder->orderBy('acc_id', 'asc');
        $total_Records = $builder->countAllResults();
        
        $builder = $this->db->table($cc_txn_tbl);
        $builder->select($cc_txn_tbl.'.cc_id,'.$cc_txn_tbl.'.acc_id');
        $builder->join($cc_mst_tbl, $cc_mst_tbl.'.cc_id ='.$cc_txn_tbl.'.cc_id');
	    $builder->select($cc_mst_tbl.'.cc_name');
	    $builder->join($acc_mst_tbl, $acc_mst_tbl.'.acc_id ='.$cc_txn_tbl.'.acc_id');
	    $builder->select($acc_mst_tbl.'.acc_name');
		if($this->session->get('ses_boid')!='')
		$builder->where($cc_mst_tbl.'.bo_id', $this->session->get('ses_boid'));
		if($this->session->get('ses_boid')!='')
		$builder->where($cc_txn_tbl.'.bo_id', $this->session->get('ses_boid'));
        $builder->groupBy('acc_id');
        $builder->groupBy('cc_id');
        $builder->orderBy('acc_id', 'asc');
        $builder->limit($limit,$offset);
        $result = $builder->get()->getResultArray();
        
        $final = [];
        foreach($result as $key => $value)
        {
            $acc_name = '';
            if($key == 0 || $result[$key]['acc_id'] != $result[$key-1]['acc_id']){
                $acc_name = $value['acc_name'];
            }
            
            $debit = '';
            $credit = '';
            $balance = '';
            
            $count_debit = 0;
            $count_credit = 0;
            $count_balance = 0;
            
            $builder = $this->db->table($cc_txn_tbl);
            $builder->select('sum(cc_txn_amt) as total');
            $builder->where('cc_id', $value['cc_id']);
            $builder->where('acc_id', $value['acc_id']);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
            $builder->where('cc_txn_drcr', 'D');
            $builder->where('cc_txn_date <=', $to_date);
            $data = $builder->get()->getRowArray();
            if(!empty($data) && $data['total'] != null){
                $debit = formatAmount($data['total']);
                $count_debit = $data['total'];
            }
            
            $builder = $this->db->table($cc_txn_tbl);
            $builder->select('sum(cc_txn_amt) as total');
            $builder->where('cc_id', $value['cc_id']);
            $builder->where('acc_id', $value['acc_id']);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
            $builder->where('cc_txn_drcr', 'C');
            $builder->where('cc_txn_date <=', $to_date);
            $data = $builder->get()->getRowArray();
            if(!empty($data) && $data['total'] != null){
                $credit = formatAmount($data['total']);
                $count_credit = $data['total'];
            }
            
            $count_balance = $count_debit - $count_credit;
            if($debit != '' || $credit != '')
            {
                if($count_balance >= 0){
                    $balance = formatAmount($count_balance).' DR &nbsp;&nbsp;';
                }
                else{
                    $balance = formatAmount(abs($count_balance)).' CR &nbsp;&nbsp;';
                }
            }

            $final[] = [
                    'cc_id'         => $value['cc_id'],
                    'cc_name'       => $value['cc_name'],
                    'acc_id'        => $value['acc_id'],
                    'acc_name'      => $acc_name,
                    'debit'         => $debit,
                    'credit'        => $credit,
                    'balance'       => $balance,
                    'count_debit'   => $count_debit,
                    'count_credit'  => $count_credit,
                    'count_balance' => $count_balance,
                ];
        }
        
        echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($final)."}";
    }
    
    function load_cost_centre_account_summary($cc_id)
    {
        $fy_months_list =  $this->CommonModel->get_fy_info($this->session->get('ses_comp_fy_id'),$this->company_id);
        $start_date     = strtotime($fy_months_list['fy_begndt']);
 	    $end_date       = strtotime($fy_months_list['fy_end']); 
 	    
 	    $months = [];
 	    while( $start_date <= $end_date ) {
		    $from_date = date( 'Y-m-01', $start_date );
		    $to_date = date( 'Y-m-t', $start_date );
		    $months[] = [
		            'from_date' => $from_date,
		            'to_date'   => $to_date
		        ];
            $start_date = strtotime( '+1 month', $start_date );
          }
          
        $final = [];
        $cc_txn_tbl = $this->company_id.'_costcttxnn_'.$this->session->get('ses_comp_fy_id');
        $count_balance = 0;
        
        $cc = $this->cc_info($cc_id);
        if($cc){
            if($cc['cc_op_drcr'] == 'dr')
                $count_balance = $cc['cc_op_bal'];
            else
                $count_balance = -$cc['cc_op_bal'];
        }
        
        foreach($months as $key => $value)
        {
            $debit = '';
            $credit = '';
            $balance = '';
            
            $count_debit = 0;
            $count_credit = 0;
            
            $month = date("F", strtotime($value['from_date']));
            
            $builder = $this->db->table($cc_txn_tbl);
            $builder->select('sum(cc_txn_amt) as total');
            $builder->where('cc_txn_drcr', 'D');
            $builder->where('cc_id', $cc_id);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
            $builder->where('cc_txn_date >=', $value['from_date']);
            $builder->where('cc_txn_date <=', $value['to_date']);
            $cc_txn = $builder->get()->getRowArray();
            if(!empty($cc_txn) && $cc_txn['total'] != null){
                $debit = formatAmount($cc_txn['total']);
                $count_debit = $cc_txn['total'];
            }
                
                
            $builder = $this->db->table($cc_txn_tbl);
            $builder->select('sum(cc_txn_amt) as total');
            $builder->where('cc_txn_drcr', 'C');
            $builder->where('cc_id', $cc_id);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
            $builder->where('cc_txn_date >=', $value['from_date']);
            $builder->where('cc_txn_date <=', $value['to_date']);
            $cc_txn = $builder->get()->getRowArray();
            if(!empty($cc_txn) && $cc_txn['total'] != null){
                $credit = formatAmount($cc_txn['total']);
                $count_credit = $cc_txn['total'];
            }
                
            $count_balance = $count_debit - $count_credit + $count_balance;
            if($count_balance >= 0){
                $balance = formatAmount($count_balance).' DR';
            }
            else{
                $balance = formatAmount(abs($count_balance)).' CR';
            }
            
            $final[] = [
                'cc_id'         => $cc_id,
                'from_date'     => $value['from_date'],
                'to_date'       => $value['to_date'],
                'month'         => $month,
                'debit'         => $debit,
                'credit'        => $credit,
                'balance'       => $balance.'&nbsp;&nbsp;',
                'count_debit'   => $count_debit,
                'count_credit'  => $count_credit,
                ];
        }
        
        return $final;
    }
    
    function load_cost_centre_group_summary($cc_grp_id)
    {
        $fy_months_list =  $this->CommonModel->get_fy_info($this->session->get('ses_comp_fy_id'),$this->company_id);
        $start_date     = strtotime($fy_months_list['fy_begndt']);
 	    $end_date       = strtotime($fy_months_list['fy_end']); 
 	    
 	    $months = [];
 	    while( $start_date <= $end_date ) {
		    $from_date = date( 'Y-m-01', $start_date );
		    $to_date = date( 'Y-m-t', $start_date );
		    $months[] = [
		            'from_date' => $from_date,
		            'to_date'   => $to_date
		        ];
            $start_date = strtotime( '+1 month', $start_date );
          }
          
        $final = [];
        $cc_txn_tbl = $this->company_id.'_costcttxnn_'.$this->session->get('ses_comp_fy_id');
        $cc_mst_tbl = $this->company_id.'_costctmstr_'.$this->session->get('ses_comp_fy_id');
        $count_balance = 0;
        
        foreach($months as $key => $value)
        {
            $debit = '';
            $credit = '';
            $balance = '';
            
            $count_debit = 0;
            $count_credit = 0;
            
            $month = date("F", strtotime($value['from_date']));
            
            $builder = $this->db->table($cc_txn_tbl);
            $builder->join($cc_mst_tbl, $cc_mst_tbl.'.cc_id ='.$cc_txn_tbl.'.cc_id');
            $builder->select('sum(cc_txn_amt) as total');
            $builder->where('cc_txn_drcr', 'D');
            $builder->where('cc_grp_id', $cc_grp_id);
            $builder->where('cc_txn_date >=', $value['from_date']);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
		
            $builder->where('cc_txn_date <=', $value['to_date']);
            $cc_txn = $builder->get()->getRowArray();
            if(!empty($cc_txn) && $cc_txn['total'] != null){
                $debit = formatAmount($cc_txn['total']);
                $count_debit = $cc_txn['total'];
            }
                
                
            $builder = $this->db->table($cc_txn_tbl);
            $builder->join($cc_mst_tbl, $cc_mst_tbl.'.cc_id ='.$cc_txn_tbl.'.cc_id');
            $builder->select('sum(cc_txn_amt) as total');
            $builder->where('cc_txn_drcr', 'C');
            $builder->where('cc_grp_id', $cc_grp_id);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
            $builder->where('cc_txn_date >=', $value['from_date']);
            $builder->where('cc_txn_date <=', $value['to_date']);
            $cc_txn = $builder->get()->getRowArray();
            if(!empty($cc_txn) && $cc_txn['total'] != null){
                $credit = formatAmount($cc_txn['total']);
                $count_credit = $cc_txn['total'];
            }
                
            $count_balance = $count_debit - $count_credit + $count_balance;
            if($count_balance >= 0){
                $balance = formatAmount($count_balance).' DR';
            }
            else{
                $balance = formatAmount(abs($count_balance)).' CR';
            }
            
            $final[] = [
                'cc_grp_id'     => $cc_grp_id,
                'from_date'     => $value['from_date'],
                'to_date'       => $value['to_date'],
                'month'         => $month,
                'debit'         => $debit,
                'credit'        => $credit,
                'balance'       => $balance.'&nbsp;&nbsp;',
                'count_debit'   => $count_debit,
                'count_credit'  => $count_credit,
                ];
        }
        
        return $final;
    }
}