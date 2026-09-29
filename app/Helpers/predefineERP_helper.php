<?php

	function predefineAccountGroupParent(){
		$final = [];

		$model = new \App\Models\CommonModel();
		$result =  $model->get_master_groups();

		foreach ($result as $key => $value) {
			
			$final[] = [
				'acc_grp_parent_id'	=> $value['acc_grp_parent_id'],
				'comp_id'			=> 0,
				'acc_grp_parent'	=>	$value['grp_name'],
				'acc_grp_restrict'	=>	$value['acc_grp_restrict'],
			];
		}	

		return $final; 
	}

	function predefineAccountGroup(){
		$final = [];

		$final = [
			// First Branch group 
			[
				'acc_grp_id' 			=> 1, 
				'comp_id' 				=> 0, 
				'acc_grp_name' 			=> 'Main', 
				'acc_grp_alias' 		=> 'Main', 
				'acc_grp_primary' 		=> 'Y', 
				'acc_grp_parent_id'	 	=> 14, 
				'under_acc_grp_id' 		=> 0, 
				'under_main_grp_id' 	=> 0, 
				'restrictions' 			=> ''
			],

			// OWNER'S FUND 1
			[
				'acc_grp_id' 			=> 2, 
				'comp_id' 				=> 0, 
				'acc_grp_name' 			=> 'Reserves & Surplus', 
				'acc_grp_alias' 		=> 'Reserves & Surplus', 
				'acc_grp_primary' 		=> 'Y', 
				'acc_grp_parent_id'	 	=> 1, 
				'under_acc_grp_id' 		=> 0, 
				'under_main_grp_id' 	=> 0, 
				'restrictions' 			=> 'DELREST'
			],

			// NON CURRENT LIABILITIES
			['acc_grp_id' => 3, 'comp_id' => 0, 'acc_grp_name' => 'Long Term Borrowings', 'acc_grp_alias' => 'Long Term Borrowings', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 2, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => 'DELREST'],
			['acc_grp_id' => 4, 'comp_id' => 0, 'acc_grp_name' => 'Deferred Tax Liabilities', 'acc_grp_alias' => 'Deferred Tax Liabilities', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 2, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''],
			['acc_grp_id' => 5, 'comp_id' => 0, 'acc_grp_name' => 'Other Long Term Liabilities', 'acc_grp_alias' => 'Other Long Term Liabilities', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 2, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''],
			['acc_grp_id' => 6, 'comp_id' => 0, 'acc_grp_name' => 'Long Term Provisions', 'acc_grp_alias' => 'Long Term Provisions', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 2, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''],

			// NON CURRENT ASSETS
			['acc_grp_id' => 7, 'comp_id' => 0, 'acc_grp_name' => 'Fixed Assets', 'acc_grp_alias' => 'Fixed Assets', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 3, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''],
			['acc_grp_id' => 8, 'comp_id' => 0, 'acc_grp_name' => 'Intangible Assets', 'acc_grp_alias' => 'Intangible Assets', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 3, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''],
			['acc_grp_id' => 9, 'comp_id' => 0, 'acc_grp_name' => 'Capital Work In Progress', 'acc_grp_alias' => 'Capital Work In Progress', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 3, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''],
			['acc_grp_id' => 10, 'comp_id' => 0, 'acc_grp_name' => 'Intangible Assets Under Development', 'acc_grp_alias' => 'Intangible Assets Under Development', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 3, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''],
			['acc_grp_id' => 11, 'comp_id' => 0, 'acc_grp_name' => 'Non Current Investments', 'acc_grp_alias' => 'Non Current Investments', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 3, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''],
			['acc_grp_id' => 12, 'comp_id' => 0, 'acc_grp_name' => 'Deferred Tax Assets', 'acc_grp_alias' => 'Deferred Tax Assets', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 3, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''],
			['acc_grp_id' => 13, 'comp_id' => 0, 'acc_grp_name' => 'Long Term Loans & Advances', 'acc_grp_alias' => 'Long Term Loans & Advances', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 3, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''],
			['acc_grp_id' => 14, 'comp_id' => 0, 'acc_grp_name' => 'Other Non Current Assets', 'acc_grp_alias' => 'Other Non Current Assets', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 3, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''],

			// CURRENT LIABILITIES
			['acc_grp_id' => 15, 'comp_id' => 0, 'acc_grp_name' => 'Short Term Borrowings', 'acc_grp_alias' => 'Short Term Borrowings', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 4, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => 'DELREST'],
			['acc_grp_id' => 16, 'comp_id' => 0, 'acc_grp_name' => 'Trade Payable', 'acc_grp_alias' => 'Trade Payable', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 4, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => 'DELREST'],
			['acc_grp_id' => 17, 'comp_id' => 0, 'acc_grp_name' => 'Other Current Liabilities', 'acc_grp_alias' => 'Other Current Liabilities', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 4, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''],
			['acc_grp_id' => 18, 'comp_id' => 0, 'acc_grp_name' => 'Short Term Provisions', 'acc_grp_alias' => 'Short Term Provisions', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 4, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''],
			['acc_grp_id' => 19, 'comp_id' => 0, 'acc_grp_name' => 'Duties & Taxes', 'acc_grp_alias' => 'Duties & Taxes', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 4, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''],
			['acc_grp_id' => 21, 'comp_id' => 0, 'acc_grp_name' => 'Bank OD / OCC A/c', 'acc_grp_alias' => 'Bank OD / OCC A/c', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 4, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => 'DELREST'],

			// CURRENT ASSETS
			['acc_grp_id' => 20, 'comp_id' => 0, 'acc_grp_name' => 'Current Investments', 'acc_grp_alias' => 'Current Investments', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 5, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''],
			['acc_grp_id' => 22, 'comp_id' => 0, 'acc_grp_name' => 'Trade Receivables', 'acc_grp_alias' => 'Trade Receivables', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 5, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => 'DELREST'],

			['acc_grp_id' => 23, 'comp_id' => 0, 'acc_grp_name' => 'Cash & Cash Equivalents', 'acc_grp_alias' => 'Cash & Cash Equivalents', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 5, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => 'DELREST'],
			['acc_grp_id' => 24, 'comp_id' => 0, 'acc_grp_name' => 'Short Term Loans & Advances', 'acc_grp_alias' => 'Short Term Loans & Advances', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 5, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => ''],
			['acc_grp_id' => 25, 'comp_id' => 0, 'acc_grp_name' => 'Other Current Assets', 'acc_grp_alias' => 'Other Current Assets', 'acc_grp_primary' => 'Y', 'acc_grp_parent_id' => 5, 'under_acc_grp_id' => 0, 'under_main_grp_id' => 0, 'restrictions' => '']
		];	

		return $final; 
	}

	function predefineBillSundry(){
		$final = [];

		$model = new \App\Models\CommonModel();
		$result =  $model->get_common_comp_values('billsundry_nature');

		foreach ($result as $key => $value) {
			if($key!='' && strtolower($value)!='others'){
				// Tax Account Yes
				if($key=='33' || $key=='34' || $key=='35' || $key=='36' || $key=='37' || $key=='38' || $key=='39' || $key=='40' || $key=='195'){
					$sundry_type='1';
				}else{
				   $sundry_type='0';	
				}

				// if DISCOUNT or ROUND OFF then assign INDIRECT EXPENSES 
				if($key=='41' || $key=='45'){

					$final [] = [
						'bill_sundry_name'	=>	$value,
						'bill_sundry_alias'	=>	$value,
						'sundry_print_name'	=>	$value,
						'sundry_type'	=>	$sundry_type,
						'sundry_nature'	=>	$key,
						'acc_grp_id'	=>	0,
						'sundry_calc_base' => 0,
						'sundry_calc_fed' =>0,
						'acc_grp_parent_id'	=>	13,
					]; 
			  	} 			  
		   		else{	

			   		$final [] = [
						'bill_sundry_name'	=>	$value,
						'bill_sundry_alias'	=>	$value,
						'sundry_print_name'	=>	$value,
						'sundry_type'	=>	$sundry_type,
						'sundry_nature'	=>	$key,
						'acc_grp_id'	=>	19,
						'sundry_calc_base' => 0,
						'sundry_calc_fed' =>0,
						'acc_grp_parent_id'	=>	0,
					];
			    }

			}
		}	

    	return $final;    
   	}

   	function predefineAccount(){
		$final = [];

			
		$final = [
			[
				'acc_id'			=> 1,
				'comp_id'			=> 0,
				'acc_name'			=> 'Capital Account',
				'acc_name_alias'	=> 'Capital Account',
				'acc_name_print'	=> 'Capital Account',
				'acc_grp_id'		=> 0,
				'acc_grp_parent_id'	=> 1,
			],
			[
				'acc_id'			=> 2,
				'comp_id'			=> 0,
				'acc_name'			=> 'Cash In Hand',
				'acc_name_alias'	=> 'Cash In Hand',
				'acc_name_print'	=> 'Cash In Hand',
				'acc_grp_id'		=> 23,
				'acc_grp_parent_id'	=> 0,
			],
			[
				'acc_id'			=> 3,
				'comp_id'			=> 0,
				'acc_name'			=> 'HO',
				'acc_name_alias'	=> 'HO',
				'acc_name_print'	=> 'HO',
				'acc_grp_id'		=> 1,
				'acc_grp_parent_id'	=> 0,
			],
			// [
			// 	'acc_id'			=> 3,
			// 	'comp_id'			=> 0,
			// 	'acc_name'			=> 'Closing Stock',
			// 	'acc_name_alias'	=> 'Closing Stock',
			// 	'acc_name_print'	=> 'Closing Stock',
			// 	'acc_grp_id'		=> 0,
			// 	'acc_grp_parent_id'	=> 9,
			// ],
			[
				'acc_id'			=> 4,
				'comp_id'			=> 0,
				'acc_name'			=> 'Sales Account',
				'acc_name_alias'	=> 'Sales Account',
				'acc_name_print'	=> 'Sales Account',
				'acc_grp_id'		=> 0,
				'acc_grp_parent_id'	=> 8,
			],
			[
				'acc_id'			=> 5,
				'comp_id'			=> 0,
				'acc_name'			=> 'Purchase Account',
				'acc_name_alias'	=> 'Purchase Account',
				'acc_name_print'	=> 'Purchase Account',
				'acc_grp_id'		=> 0,
				'acc_grp_parent_id'	=> 7,
			],
			[
				'acc_id'			=> 6,
				'comp_id'			=> 0,
				'acc_name'			=> 'GST PAID A/C',
				'acc_name_alias'	=> 'GST PAID A/C',
				'acc_name_print'	=> 'GST PAID A/C',
				'acc_grp_id'		=> 0,
				'acc_grp_parent_id'	=> 13,
				'acc_short_code'    => 'sagstpd'
			],
			[
				'acc_id'			=> 7,
				'comp_id'			=> 0,
				'acc_name'			=> 'Profit & Loss Appropriation',
				'acc_name_alias'	=> 'Profit & Loss Appropriation',
				'acc_name_print'	=> 'Profit & Loss Appropriation',
				'acc_grp_id'		=> 2,
				'acc_grp_parent_id'	=> 0,
				'acc_short_code'    => 'sapnlap'
			],
			
		];

		return $final; 
	}

	function predefineCostCenterGroup(){
		$final = [];
			
		$final = [
			[
				'cc_grp_id'				=> 1,
				'comp_id'				=> 0,
				'cc_grp_name'			=> 'MAIN',
				'cc_grp_alias'			=> 'MAIN',
				'under_cc_grp_id'		=> 0,
			],
		];

		return $final; 
	}

	function predefineCostCenter(){
		$final = [];
			
		$final = [
			[
				'cc_id'				=> 1,
				'comp_id'			=> 0,
				'cc_name'			=> 'UNDEFINED',
				'cc_alias'			=> 'UNDEFINED',
				'cc_print'			=> 'UNDEFINED',
				'cc_grp_id'			=> 1,
				'bo_id'				=> 1,
				'cc_op_bal'			=> 0,
				'cc_op_drcr'		=> 'dr',
				'cc_py_bal'			=> 0,
				'cc_py_drcr'		=> 'dr',
			],
			
		];
		return $final; 
	}

	function predefineUnits(){
		$final = [];

	 	$model = new \App\Models\CommonModel();
	 	$result  = $model->get_common_comp_values('companyunits');

	 	$count = 1;
	 	foreach ($result as $key => $value) {
	 		if($value!='' && strtolower($value)!='choose'){
		 		$final[] = [
		 			'unit_id'				=> $count,
		 			'comp_id'				=> 0,
		 			'item_unit'				=> $value,
		 			'item_unit_alias'		=> $value,
		 			'item_unit_print'		=> $value,
		 			'item_unit_uqc'			=> '',
		 		];
		 		$count++;
	 		}
	 	}

	 	return $final;
	}

	function predefineItemCategory(){
		$final = [];
			
		$final = [
			[
				'icatgms_id'		=> 1,
				'comp_id'			=> 0,
				'item_cat'			=> 'Main',
				'item_cat_alias'	=> 'Main',

			],
		];
		return $final; 
	}

	function predefineItemGroup(){
		$final = [];
			
		$final = [
			[
				'item_grp_id'		=> 1,
				'comp_id'			=> 0,
				'item_grp_name'		=> 'Main',
				'item_grp_alias'	=> 'Main',

			],
		];
		return $final; 
	}

	function predefineMCGroup(){
		$final = [];
			
		$final = [
			[
				'mc_grp_id'			=> 1,
				'comp_id'			=> 0,
				'mc_grp_name'		=> 'General',
				'mc_alias'			=> 'General',
				'mc_primary'		=> 0,
				'under_mc_grp_id'	=> 1,

			],
		];
		
		return $final; 
	}

	function predefineMC(){
		$final = [];
			
		$final = [
			[
				'mat_cent_id'		=> 1,
				'comp_id'			=> 0,
				'mat_cent_name'		=> 'Main',
				'mat_cent_alias'	=> 'Main',
				'mat_cent_alias'	=> 'Main',
				'mat_cent_grp_id'	=> 1,

			],
		];
		return $final; 
	}

	function predefineVoucherTypes(){
		$final = [];

		$model = new \App\Models\CommonModel();
		$result = $model->load_common_vouchers();
		$count = 1;

		foreach ($result as $key => $value) {

			if($key!='' && $value!=''){
				$final[] = [
					'voucher_type_id'	=> $count,
					'comp_id'			=> 0,
					'comp_vch_type'		=> $value,
				];
				$count++;
			}
			
		}

		return $final; 
	}

	function predefineVoucherSeries(){
		$final = [];
        $sale_vouchers=[18,19,2,7,17];
        $purchase_vouchers=[11,12,3,6,21];		
		$model = new \App\Models\CommonModel();
		$result = $model->load_common_vouchers();

		$count = 1;
		foreach ($result as $key => $value) {
			if(in_array($count,$sale_vouchers))
				$comp_vch_method ="1";
			else if(in_array($count,$purchase_vouchers))
				$comp_vch_method ="0";
			 else
				$comp_vch_method ="0"; 
			
			if($key!='' && $value!=''){
				$final[] = [
					'comp_vch_series_id'	=> $count,
					'comp_id'				=> 0,
					'comp_vch_series'		=> 'Main',
					'comp_vch_prefix'		=> '',
					'voucher_type_id'		=> $count,
					'comp_vch_suffix'		=> '',
					'comp_vch_method'		=> $comp_vch_method,
				];
				$count++;
			}
		}

		return $final; 
	}

	function predefineVoucherSubTypes(){
		$final = [];

		
		$final = [
			[
				'vch_subtype_id'	=> 1,
				'comp_id'			=> 0,
				'comp_vch_subtype'	=> 'Credit Note With Stock Inward',
				'voucher_type_id'	=> 2
			],
			[
				'vch_subtype_id'	=> 2,
				'comp_id'			=> 0,
				'comp_vch_subtype'	=> 'Credit Note W/out Stock',
				'voucher_type_id'	=> 2
			],
			[
				'vch_subtype_id'	=> 3,
				'comp_id'			=> 0,
				'comp_vch_subtype'	=> 'Debit Note With Stock Outward',
				'voucher_type_id'	=> 3
			],
			[
				'vch_subtype_id'	=> 4,
				'comp_id'			=> 0,
				'comp_vch_subtype'	=> 'Debit Note W/out Stock',
				'voucher_type_id'	=> 3
			],
			[
				'vch_subtype_id'	=> 5,
				'comp_id'			=> 0,
				'comp_vch_subtype'	=> 'Purchase With Stock Inward',
				'voucher_type_id'	=> 11
			],
			[
				'vch_subtype_id'	=> 6,
				'comp_id'			=> 0,
				'comp_vch_subtype'	=> 'Purchase W/out Stock',
				'voucher_type_id'	=> 11
			],
			[
				'vch_subtype_id'	=> 7,
				'comp_id'			=> 0,
				'comp_vch_subtype'	=> 'Pur. Against Challan',
				'voucher_type_id'	=> 11
			],
			[
				'vch_subtype_id'	=> 8,
				'comp_id'			=> 0,
				'comp_vch_subtype'	=> 'Sales With Stock Outward',
				'voucher_type_id'	=> 18
			],
			[
				'vch_subtype_id'	=> 9,
				'comp_id'			=> 0,
				'comp_vch_subtype'	=> 'Sale W/out Stock',
				'voucher_type_id'	=> 18
			],
			[
				'vch_subtype_id'	=> 10,
				'comp_id'			=> 0,
				'comp_vch_subtype'	=> 'Sale Against Challan',
				'voucher_type_id'	=> 18
			],
			[
				'vch_subtype_id'	=> 11,
				'comp_id'			=> 0,
				'comp_vch_subtype'	=> 'W/out Purchase',
				'voucher_type_id'	=> 6
			],
			[
				'vch_subtype_id'	=> 12,
				'comp_id'			=> 0,
				'comp_vch_subtype'	=> 'Against Purchase',
				'voucher_type_id'	=> 6
			],
			[
				'vch_subtype_id'	=> 13,
				'comp_id'			=> 0,
				'comp_vch_subtype'	=> 'W/out Sale',
				'voucher_type_id'	=> 7
			],
			[
				'vch_subtype_id'	=> 14,
				'comp_id'			=> 0,
				'comp_vch_subtype'	=> 'Against Sale',
				'voucher_type_id'	=> 7
			],
			[
				'vch_subtype_id'	=> 15,
				'comp_id'			=> 0,
				'comp_vch_subtype'	=> 'Stock Transfer BO',
				'voucher_type_id'	=> 15
			],
			[
				'vch_subtype_id'	=> 16,
				'comp_id'			=> 0,
				'comp_vch_subtype'	=> 'Stock Transfer MC',
				'voucher_type_id'	=> 15
			],
			[
				'vch_subtype_id'	=> 17,
				'comp_id'			=> 0,
				'comp_vch_subtype'	=> 'Stock Transfer Receipt',
				'voucher_type_id'	=> 15
			],
			[
				'vch_subtype_id'	=> 18,
				'comp_id'			=> 0,
				'comp_vch_subtype'	=> 'Stock Journal',
				'voucher_type_id'	=> 20
			],
			[
				'vch_subtype_id'	=> 19,
				'comp_id'			=> 0,
				'comp_vch_subtype'	=> 'Pack / Assemble',
				'voucher_type_id'	=> 20
			],
			[
				'vch_subtype_id'	=> 20,
				'comp_id'			=> 0,
				'comp_vch_subtype'	=> 'Unpack / Unassemble',
				'voucher_type_id'	=> 20
			],
			[
				'vch_subtype_id'	=> 21,
				'comp_id'			=> 0,
				'comp_vch_subtype'	=> 'Against Credit Note',
				'voucher_type_id'	=> 6
			],
			[
				'vch_subtype_id'	=> 22,
				'comp_id'			=> 0,
				'comp_vch_subtype'	=> 'Against Debit Note',
				'voucher_type_id'	=> 7
			],
			[
				'vch_subtype_id'	=> 23,
				'comp_id'			=> 0,
				'comp_vch_subtype'	=> 'Journal Item Based',
				'voucher_type_id'	=> 5
			],
		];

		return $final; 
	}

	function predefineProjectGroup(){
		$final = [];
			
		$final = [
			[
				'project_grp_id'		=> 1,
				'comp_id'				=> 0,
				'project_grp_name'		=> 'Main',
				'project_grp_alias'		=> 'Main',
				'under_project_grp_id'	=> 0,
				'under_main_grp_id'		=> 0,

			],
			
		];
		
		return $final; 
	}

	function predefineProject(){
		$final = [];
			
		$final = [
			[
				'project_id'		=> 1,
				'comp_id'			=> 0,
				'project_name'		=> 'UNDEFINED',
				'project_alias'		=> 'UNDEFINED',
				'project_print'		=> 'UNDEFINED',
				'project_grp_id'	=> 1,
			],
		];
		
		return $final; 
	}

	function predefinePrintConfig(){
		$final = [];
			
		$final = [
			[
				'prntconfig_id'		=> 1,
				'comp_id'			=> 0,
				'vch_series_id'		=> 13,
				'usr_config_id'		=> 17,
			],
			[
				'prntconfig_id'		=> 2,
				'comp_id'			=> 0,
				'vch_series_id'		=> 13,
				'usr_config_id'		=> 18,
			],
		];
		
		return $final; 
	}

	function predefinePrintDesign(){
		$final = [];
			
		$final = [
			[ 'prntconfig_id' => 1, 'erpprevaln_label_id' =>	6,	'prntconfig_style' => 'font-weight: bold; font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 1, 'erpprevaln_label_id' =>	7,	'prntconfig_style' => 'font-weight: bold; font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 1, 'erpprevaln_label_id' =>	8,	'prntconfig_style' => 'text-align: center; padding: 0px; margin: 0px; font-family: Verdana; text-decoration: underline; font-weight: bold; font-size: 12px;'],
			[ 'prntconfig_id' => 1, 'erpprevaln_label_id' =>	9,	'prntconfig_style' => 'line-height: 42px; padding: 0px; margin: 0px; text-align: center; font-weight: bold; font-family: Verdana; font-size: 16px;'],
			[ 'prntconfig_id' => 1, 'erpprevaln_label_id' =>	10,'prntconfig_style' => 	'font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 1, 'erpprevaln_label_id' =>	11,'prntconfig_style' => 	'font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 1, 'erpprevaln_label_id' =>	12,'prntconfig_style' => 	'font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 1, 'erpprevaln_label_id' =>	13,'prntconfig_style' => 	'width: 150px; float: left; font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 1, 'erpprevaln_label_id' =>	14,'prntconfig_style' => 	'width: 150px; float: left; font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 1, 'erpprevaln_label_id' =>	15,'prntconfig_style' => 	'width: 150px; float: left; font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 1, 'erpprevaln_label_id' =>	16,'prntconfig_style' => 	'width: 150px; float: left; font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 1, 'erpprevaln_label_id' =>	17,'prntconfig_style' => 	'padding-left: 29px; font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 1, 'erpprevaln_label_id' =>	18,'prntconfig_style' => 	'padding-left: 8px; font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 1, 'erpprevaln_label_id' =>	19,'prntconfig_style' => 	'text-align: right; font-family: Verdana; font-size: 14px;'],
			[ 'prntconfig_id' => 1, 'erpprevaln_label_id' =>	20,'prntconfig_style' => 	'font-family: Verdana; font-size: 14px; font-weight: bold;'],
			[ 'prntconfig_id' => 1, 'erpprevaln_label_id' =>	26,'prntconfig_style' => 	'padding-left: 10px; font-weight: bold; font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 1, 'erpprevaln_label_id' =>	27,'prntconfig_style' => 	'padding-left: 10px; font-weight: bold; font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 1, 'erpprevaln_label_id' =>	28,'prntconfig_style' => 	'padding-left: 18px; font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 1, 'erpprevaln_label_id' =>	29,'prntconfig_style' => 	'padding-left: 18px; font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 1, 'erpprevaln_label_id' =>	30,'prntconfig_style' => 	'font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 1, 'erpprevaln_label_id' =>	31,'prntconfig_style' => 	'font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 1, 'erpprevaln_label_id' =>	32,'prntconfig_style' => 	'font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 1, 'erpprevaln_label_id' =>	33,'prntconfig_style' => 	'font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 1, 'erpprevaln_label_id' =>	34,'prntconfig_style' => 	'text-align: center; padding: 0px 0px 8px; line-height: 20px; margin: 0px; font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 1, 'erpprevaln_label_id' =>	36,'prntconfig_style' => 	'padding: 6px 0px; font-family: Verdana; font-size: 14px;'],
			[ 'prntconfig_id' => 1, 'erpprevaln_label_id' =>	37,'prntconfig_style' => 	'font-family: Verdana; font-size: 12px;'],

			[ 'prntconfig_id' => 2, 'erpprevaln_label_id' =>	6,	'prntconfig_style' => 'font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 2, 'erpprevaln_label_id' =>	26,'prntconfig_style' => 	'padding-left: 10px; font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 2, 'erpprevaln_label_id' =>	7,	'prntconfig_style' => 'font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 2, 'erpprevaln_label_id' =>	27,'prntconfig_style' => 	'padding-left: 10px; font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 2, 'erpprevaln_label_id' =>	8,	'prntconfig_style' => 'font-family: Verdana; font-size: 24px; text-decoration: underline;'],
			[ 'prntconfig_id' => 2, 'erpprevaln_label_id' =>	9,	'prntconfig_style' => 'margin-top: 15px; line-height: 42px; font-family: Verdana; font-size: 14px;'],
			[ 'prntconfig_id' => 2, 'erpprevaln_label_id' =>	34,'prntconfig_style' => 	'font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 2, 'erpprevaln_label_id' =>	28,'prntconfig_style' => 	'font-family: Verdana; font-size: 12px; font-weight: bold;'],
			[ 'prntconfig_id' => 2, 'erpprevaln_label_id' =>	11,'prntconfig_style' => 	'font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 2, 'erpprevaln_label_id' =>	29,'prntconfig_style' => 	'padding-left: 12px; font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 2, 'erpprevaln_label_id' =>	12,'prntconfig_style' => 	'border-right: 1px solid rgb(0, 0, 0); padding: 6px; width: 56%; font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 2, 'erpprevaln_label_id' =>	30,'prntconfig_style' => 	'border-right: 1px solid rgb(0, 0, 0); padding: 6px; width: 56%; font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 2, 'erpprevaln_label_id' =>	13,'prntconfig_style' => 	'undefined'],
			[ 'prntconfig_id' => 2, 'erpprevaln_label_id' =>	31,'prntconfig_style' => 	'undefined'],
			[ 'prntconfig_id' => 2, 'erpprevaln_label_id' =>	14,'prntconfig_style' => 	'border-right: 1px solid rgb(0, 0, 0); padding: 6px; width: 24%; font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 2, 'erpprevaln_label_id' =>	32,'prntconfig_style' => 	'border-right: 1px solid rgb(0, 0, 0); padding: 6px; width: 24%; font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 2, 'erpprevaln_label_id' =>	15,'prntconfig_style' => 	'undefined'],
			[ 'prntconfig_id' => 2, 'erpprevaln_label_id' =>	33,'prntconfig_style' => 	'undefined'],
			[ 'prntconfig_id' => 2, 'erpprevaln_label_id' =>	16,'prntconfig_style' => 	'padding: 6px; text-align: right; width: 20%; font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 2, 'erpprevaln_label_id' =>	37,'prntconfig_style' => 	'padding: 6px; text-align: right; width: 20%; font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 2, 'erpprevaln_label_id' =>	17,'prntconfig_style' => 	'padding-left: 29px; font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 2, 'erpprevaln_label_id' =>	18,'prntconfig_style' => 	'padding-left: 8px; font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 2, 'erpprevaln_label_id' =>	19,'prntconfig_style' => 	'text-align: right; font-family: Verdana; font-size: 14px;'],
			[ 'prntconfig_id' => 2, 'erpprevaln_label_id' =>	36,'prntconfig_style' => 	'font-family: Verdana; font-size: 14px;'],
			[ 'prntconfig_id' => 2, 'erpprevaln_label_id' =>	20,'prntconfig_style' => 	'font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 2, 'erpprevaln_label_id' =>	24,'prntconfig_style' => 	'font-family: Verdana; font-size: 12px;'],
			[ 'prntconfig_id' => 2, 'erpprevaln_label_id' =>	25,'prntconfig_style' => 	'font-family: Verdana; font-size: 12px;'],
		];
		
		return $final; 
	}

	function predefineCurrency(){
		$final = [];
			
		$final = [
			[
				'comp_currency_id'		=> 1,
				'comp_id'				=> 0,
				'curr_name'				=> 'Rupee',
				'curr_symbol'			=> '₹',
				'curr_string'			=> 'Rs.',
				'curr_sub_string'		=> 'Rs.',
				'curr_initial'			=> 'INR',
				'forex_type'			=> 'M',
			],

		];
		
		return $final; 
	}

	function predefineItemBatch(){
		$final = [];
			
		$final = [
			[
				'batch_id'		=> 1,
				'batch_no'		=> 'UNDEFINED',
			],
		];
		
		return $final; 
	}

?>