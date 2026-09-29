<?php
namespace App\Models\Admin;

use CodeIgniter\Model;
use App\Models\CommonModel;
use App\Libraries\externaldb;
use App\Libraries\enc_string;
use App\Libraries\UUIDtables; 
use App\Libraries\ERPtables;
use App\Models\Admin\TransactionModel;

class Gstr2abModel extends Model	{
	function __construct() {
		parent::__construct();        
		$this->externaldb    = new externaldb();	
		$this->db            = $this->externaldb->get_company_db();
		$this->session       = \Config\Services::session();
		$this->company_id    = $this->session->get('ses_company_id');
		$this->dberpunvrsl   =  $this->externaldb->erp_db();
		$this->CommonModel   = new CommonModel();	   
		$this->TransactionModel  = new TransactionModel();
		$this->enc_string    = new enc_string();
		if($this->session->get('ses_boid'))
			$this->bo_id = $this->session->get('ses_boid');
		else
			$this->bo_id = 1;
	}
	function gstr2ab_transactions($table_key,$from_date,$to_date,$tableinfo,$states,$limit,$pq_curPage){
	    
		$fy_id = $this->session->get('ses_comp_fy_id');
		$acctgstsum_tbl  = "{$this->company_id}_acctgstsum_{$fy_id}";
		$comp_txn_tbl    = "{$this->company_id}_comptxnmst_{$fy_id}";
		$gstroutsup_tbl  = "{$this->company_id}_gstroutsup_{$fy_id}";
		$gstrinwsup_tbl  = "{$this->company_id}_gstrinwsup_{$fy_id}";
		$acctgstmst_tbl  = "{$this->company_id}_acctgstmst_{$fy_id}";
		$vhtxn_conso_tbl = "{$this->company_id}_vhtxnconso_{$fy_id}";
		$ses_bostecd = $this->session->get('ses_bostecd');
	    $state_code = sprintf( '%02d', $ses_bostecd );
	
		// Disable ONLY_FULL_GROUP_BY
		$this->db->query("SET sql_mode = (SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''))");
		if($table_key==3){
		$baseQuery = $this->db->table("$acctgstsum_tbl gstsum")
			->join("$gstrinwsup_tbl gstsup", 'gstsup.voucher_txn_id = gstsum.vch_txn_id')
			->join("$vhtxn_conso_tbl vchconso", 'vchconso.voucher_txn_id = gstsum.vch_txn_id')
			->where('vchconso.voucher_type_id', 11)
			->where('vchconso.bo_id', $this->bo_id)
			->where('gstsup.inwsup_rev_chg', $tableinfo['outsup_rev_chg'])
			->where('gstsup.inwsup_eco', $tableinfo['outsup_eco'])
			->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code'])
			->whereNotIn('gstsum.cmp_tax_short_code', ['NILSPLY','EXMSPLY','NONGSTS'])
			->where('gstsum.acc_txn_date >=', $from_date)
			->where('gstsum.acc_txn_date <=', $to_date)
			->whereNotIn('gstsup.inwsup_pos', [96, 97]);
		 }
		if($table_key==5){
		$baseQuery = $this->db->table("$acctgstsum_tbl gstsum")
			->join("$gstrinwsup_tbl gstsup", 'gstsup.voucher_txn_id = gstsum.vch_txn_id')
			->join("$vhtxn_conso_tbl vchconso", 'vchconso.voucher_txn_id = gstsum.vch_txn_id')
			->where('vchconso.voucher_type_id', 11)
			->where('vchconso.vch_subtype_id >',0)
			->where('vchconso.bo_id', $this->bo_id)
			->where('gstsup.inwsup_rev_chg', $tableinfo['outsup_rev_chg'])
			->where('gstsup.inwsup_eco', $tableinfo['outsup_eco'])
			->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code'])
			->whereNotIn('gstsum.cmp_tax_short_code', ['NILSPLY','EXMSPLY','NONGSTS'])
			->where('gstsum.acc_txn_date >=', $from_date)
			->where('gstsum.acc_txn_date <=', $to_date)
			->whereIn('gstsup.inwsup_pos', [96, 97]);
		 }
		 if($table_key=='5A_IMP'){
		  $baseQuery = $this->db->table("$acctgstsum_tbl gstsum")
			->join("$gstrinwsup_tbl gstsup", 'gstsup.voucher_txn_id = gstsum.vch_txn_id')
			->join("$vhtxn_conso_tbl vchconso", 'vchconso.voucher_txn_id = gstsum.vch_txn_id')
			->where('vchconso.voucher_type_id', 11)
			->where('vchconso.vch_subtype_id >',0)
			->where('vchconso.bo_id', $this->bo_id)
			->where('gstsup.inwsup_rev_chg', $tableinfo['outsup_rev_chg'])
			->where('gstsup.inwsup_eco', $tableinfo['outsup_eco'])
			->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code'])
			->whereNotIn('gstsum.cmp_tax_short_code', ['NILSPLY','EXMSPLY','NONGSTS'])
			->where('gstsum.acc_txn_date >=', $from_date)
			->where('gstsum.acc_txn_date <=', $to_date)
			->whereIn('gstsup.inwsup_pos', [96]);
		 }
		 if($table_key=='5B_RCDSEZ'){
		  $baseQuery = $this->db->table("$acctgstsum_tbl gstsum")
			->join("$gstrinwsup_tbl gstsup", 'gstsup.voucher_txn_id = gstsum.vch_txn_id')
			->join("$vhtxn_conso_tbl vchconso", 'vchconso.voucher_txn_id = gstsum.vch_txn_id')
			->where('vchconso.voucher_type_id', 11)
			->where('vchconso.vch_subtype_id >',0)
			->where('vchconso.bo_id', $this->bo_id)
			->where('gstsup.inwsup_rev_chg', $tableinfo['outsup_rev_chg'])
			->where('gstsup.inwsup_eco', $tableinfo['outsup_eco'])
			->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code'])
			->whereNotIn('gstsum.cmp_tax_short_code', ['NILSPLY','EXMSPLY','NONGSTS'])
			->where('gstsum.acc_txn_date >=', $from_date)
			->where('gstsum.acc_txn_date <=', $to_date)
			 ->whereIn('gstsup.inwsup_pos', [97]);
		 }
		 if($table_key=='7A'){		  			
			$baseQuery = $this->db->table("$acctgstsum_tbl gstsum")
				->join("$gstrinwsup_tbl gstsup", 'gstsup.voucher_txn_id = gstsum.vch_txn_id')
				->join("$vhtxn_conso_tbl vchconso", 'vchconso.voucher_txn_id = gstsum.vch_txn_id')
				->where('vchconso.voucher_type_id', 11)
				->where('vchconso.vch_subtype_id >',0)
				->where('vchconso.bo_id', $this->bo_id)
				->where('gstsup.inwsup_rev_chg', $tableinfo['outsup_rev_chg'])
				->where('gstsup.inwsup_eco', $tableinfo['outsup_eco'])				
				->where('gstsum.acc_txn_date >=', $from_date)
				->where('gstsum.acc_txn_date <=', $to_date)
				->whereNotIn('gstsup.inwsup_pos', [$state_code]);		     
		 }
		 if($table_key=='7A_1' || $table_key=='7A_2' || $table_key=='7A_3' || $table_key=='7A_4'){
		  $Composition_exists    = $this->isCompositionGstin($from_date,$to_date);
		  if($table_key=='7A_1' && $Composition_exists){			
			$baseQuery = $this->db->table("$acctgstsum_tbl gstsum")
				->join("$gstrinwsup_tbl gstsup", 'gstsup.voucher_txn_id = gstsum.vch_txn_id')
				->join("$vhtxn_conso_tbl vchconso", 'vchconso.voucher_txn_id = gstsum.vch_txn_id')
				->where('vchconso.voucher_type_id', 11)
				->where('vchconso.vch_subtype_id >',0)
				->where('vchconso.bo_id', $this->bo_id)
				->where('gstsup.inwsup_rev_chg', $tableinfo['outsup_rev_chg'])
				->where('gstsup.inwsup_eco', $tableinfo['outsup_eco'])				
				->where('gstsum.acc_txn_date >=', $from_date)
				->where('gstsum.acc_txn_date <=', $to_date)
				->whereNotIn('gstsup.inwsup_pos', [$state_code]);
		     }
	      if($table_key=='7A_2' || $table_key=='7A_3' || $table_key=='7A_4'){		
			$baseQuery = $this->db->table("$acctgstsum_tbl gstsum")
				->join("$gstrinwsup_tbl gstsup", 'gstsup.voucher_txn_id = gstsum.vch_txn_id')
				->join("$vhtxn_conso_tbl vchconso", 'vchconso.voucher_txn_id = gstsum.vch_txn_id')
				->where('vchconso.voucher_type_id', 11)
				->where('vchconso.vch_subtype_id >',0)
				->where('vchconso.bo_id', $this->bo_id)
				->where('gstsup.inwsup_rev_chg', $tableinfo['outsup_rev_chg'])
				->where('gstsup.inwsup_eco', $tableinfo['outsup_eco'])				
				->where('gstsum.acc_txn_date >=', $from_date)
				->where('gstsum.acc_txn_date <=', $to_date)
				->whereNotIn('gstsup.inwsup_pos', [$state_code]);
		     }
		 }
		 
		 if($table_key=='7B'){		  			
			$baseQuery = $this->db->table("$acctgstsum_tbl gstsum")
				->join("$gstrinwsup_tbl gstsup", 'gstsup.voucher_txn_id = gstsum.vch_txn_id')
				->join("$vhtxn_conso_tbl vchconso", 'vchconso.voucher_txn_id = gstsum.vch_txn_id')
				->where('vchconso.voucher_type_id', 11)
				->where('vchconso.vch_subtype_id >',0)
				->where('vchconso.bo_id', $this->bo_id)
				->where('gstsup.inwsup_rev_chg', $tableinfo['outsup_rev_chg'])
				->where('gstsup.inwsup_eco', $tableinfo['outsup_eco'])				
				->where('gstsum.acc_txn_date >=', $from_date)
				->where('gstsum.acc_txn_date <=', $to_date)
				->whereIn('gstsup.inwsup_pos', [$state_code]);		     
		 }
		 if($table_key=='7B_1' || $table_key=='7B_2' || $table_key=='7B_3' || $table_key=='7B_4'){
		  $Composition_exists    = $this->isCompositionGstin($from_date,$to_date);
		  if($table_key=='7B_1' && $Composition_exists){			
			$baseQuery = $this->db->table("$acctgstsum_tbl gstsum")
				->join("$gstrinwsup_tbl gstsup", 'gstsup.voucher_txn_id = gstsum.vch_txn_id')
				->join("$vhtxn_conso_tbl vchconso", 'vchconso.voucher_txn_id = gstsum.vch_txn_id')
				->where('vchconso.voucher_type_id', 11)
				->where('vchconso.vch_subtype_id >',0)
				->where('vchconso.bo_id', $this->bo_id)
				->where('gstsup.inwsup_rev_chg', $tableinfo['outsup_rev_chg'])
				->where('gstsup.inwsup_eco', $tableinfo['outsup_eco'])				
				->where('gstsum.acc_txn_date >=', $from_date)
				->where('gstsum.acc_txn_date <=', $to_date)
				->whereIn('gstsup.inwsup_pos', [$state_code]);
		     }
	      if($table_key=='7B_2' || $table_key=='7B_3' || $table_key=='7B_4'){		
			$baseQuery = $this->db->table("$acctgstsum_tbl gstsum")
				->join("$gstrinwsup_tbl gstsup", 'gstsup.voucher_txn_id = gstsum.vch_txn_id')
				->join("$vhtxn_conso_tbl vchconso", 'vchconso.voucher_txn_id = gstsum.vch_txn_id')
				->where('vchconso.voucher_type_id', 11)
				->where('vchconso.vch_subtype_id >',0)
				->where('vchconso.bo_id', $this->bo_id)
				->where('gstsup.inwsup_rev_chg', $tableinfo['outsup_rev_chg'])
				->where('gstsup.inwsup_eco', $tableinfo['outsup_eco'])
				->where('gstsum.acc_txn_date >=', $from_date)
				->where('gstsum.acc_txn_date <=', $to_date)
				->whereIn('gstsup.inwsup_pos', [$state_code]);
		     }
		 }
		// Clone query for count
		$countQuery = clone $baseQuery;
		$total_records = $countQuery->groupBy('gstsum.vch_txn_id')->select('gstsum.vch_txn_id')->countAllResults();

		// Pagination
		$page = max(1, (int)$pq_curPage);
		$limit = (int)$limit;
		$offset = ($limit > 0) ? ($limit * ($page - 1)) : 0;

		// Fetch data
		$response = $baseQuery
			->orderBy('gstsum.vch_txn_id')
			->select('gstsum.*, gstsup.*, vchconso.bo_id')
			->limit($limit, $offset)
			->get()->getResultArray();
			//echo $this->db->getlastquery();
		$final_response = [];
		$ctin = [];

		foreach ($response as $row) {
			$pos_id = $row['inwsup_pos'] ?? 0;
			$pos_name = $states[$pos_id] ?? '';
			$invoice_type = strtolower($row['inwsup_inv_type'] ?? 'B2B');
			$invoice_no = $row['inwsup_bill_ref_no'] ?? '';
			$rev_chg = ($row['inwsup_rev_chg'] == "1") ? "Y" : "N";

			$itm_invoice_type = 'R'; // Default
			if (in_array('B2B', $tableinfo['outsup_inv_type'] ?? [])) {
				$map = ['b2brcm' => 'R', 'de' => 'DE', 'sezwp' => 'SEWP', 'sezwop' => 'SEWOP', 'cbw' => 'CBW'];
				$itm_invoice_type = $map[$invoice_type] ?? 'R';
			}

			$party_transaction = $this->TransactionModel->get_partyo_transaction($row['vch_txn_id']);
			$accountinfo = $this->TransactionModel->get_account_info($party_transaction['acc_id']);
			$party_gstin_data = $this->db->table($acctgstmst_tbl)->where('acc_id', $party_transaction['acc_id'])->get()->getRowArray();
			$party_gstin = $party_gstin_data['acc_gstin'] ?? '';
			$branch_name = $this->TransactionModel->get_branchinfo($row['bo_id'])['bo_alias'] ?? '';
			$date = date('d-m-Y', strtotime($row['acc_txn_date']));

			$taxable_amt = parseAmount($row['taxable_amt']);
			$total_tax = parseAmount($row['total_tax']);

			$item = [
				"date" => $date,
				"idt" => $date,
				"inum" => $invoice_no,
				"party" => $accountinfo['acc_name'],
				"pos" => $pos_name,
				"pos_id" => $pos_id,
				"party_gstin" => $party_gstin,
				"branch_name" => $branch_name,
				"acc_igst_rate" => (float)$row["acc_igst_rate"],
				"num" => (int)$row['tgsmid'],
				"inv_typ" => $invoice_type,
				"itm_invoice_type" => $itm_invoice_type,
				"bo_id" => $party_transaction['bo_id'],
				"voucher_type_id" => $party_transaction['voucher_type_id'],
				"voucher_txn_id" => $row['voucher_txn_id'],
				"invoice_value" => $taxable_amt + $total_tax,
				"val" => $taxable_amt + $total_tax,
				"rchrg" => $rev_chg,
				"taxable_value" => $taxable_amt,
				"igst" => parseAmount($row["acc_igst"]),
				"cgst" => parseAmount($row["acc_cgst"]),
				"sgst" => parseAmount($row["acc_sgst"]),
				"cess" => parseAmount($row["acc_cess"]),
				"total_tax" => $total_tax,
				"sm_invoice_value" => $taxable_amt + $total_tax,
				"sm_igst" => parseAmount($row["acc_igst"]),
				"sm_cgst" => parseAmount($row["acc_cgst"]),
				"sm_sgst" => parseAmount($row["acc_sgst"]),
				"sm_cess" => parseAmount($row["acc_cess"]),
				"sm_total_tax" => $total_tax,
				"sm_taxable_value" => $taxable_amt,
				"itms" => [
					[
						"num" => (int)$row['tgsmid'],
						"itm_det" => [
							"txval" => $taxable_amt,
							"rt" => (float)$row["acc_igst_rate"],
							"iamt" => parseAmount($row["acc_igst"]),
							"cgst" => parseAmount($row["acc_cgst"]),
							"sgst" => parseAmount($row["acc_sgst"]),
							"csamt" => parseAmount($row["acc_cess"]),
						]
					]
				]
			];

			$final_response[] = $item;

			$ctin[] = [
				'ctin' => $party_gstin,
				'inv' => [
					[
						"inum" => $invoice_no,
						"idt" => $date,
						"val" => $taxable_amt + $total_tax,
						"pos" => $pos_name,
						"rchrg" => $rev_chg,
						"inv_typ" => $invoice_type,
						"itms" => $item['itms']
					]
				]
			];
		}

		return [
			'totalRecords' => $total_records,
			'data' => $final_response,
			'inv' => $ctin
		];
	}
	
	function isCompositionGstin($start_date,$end_date){
		$bo_id = $this->session->get('ses_boid');
		$gstinmastr_tbl = $this->company_id.'_gstinmastr_'.$this->session->get('ses_comp_fy_id');
		$query = $this->db->table($gstinmastr_tbl)
			->where('comp_gstin_type','2')
			->where('bo_id',$bo_id)
			->where('gstin_wefdate >=', $start_date)
			->where('gstin_wefdate <=', $end_date)
			->get();
		if($query->getNumRows() > 0)
			return true;
		else 
	      return false;
	  }
     function isGstin($start_date,$end_date){
		$bo_id = $this->session->get('ses_boid');
		$gstinmastr_tbl = $this->company_id.'_gstinmastr_'.$this->session->get('ses_comp_fy_id');
		$query = $this->db->table($gstinmastr_tbl)
			->where('comp_gstin!=','')
			->where('bo_id',$bo_id)
			->where('gstin_wefdate >=', $start_date)
			->where('gstin_wefdate <=', $end_date)
			->get();
		if($query->getNumRows() > 0)
			return true;
		else 
	      return false;
	  }
	
	function gstr2ab_table_info($from_date,$to_date,$vouchers_result,$tableinfo){	
            $acctgstsum_tbl     = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	        $comp_txn_tbl       = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	        $gstroutsup_tbl     = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
	        $gstrinwsup_tbl     = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
	       	
			$total_counter= array();
			$ttt_taxable_amt=0;
			$ttt_total_tax =0;
			$total_invoice_value=0;
			$ttt_acc_igst =0;
			$ttt_acc_cgst =0;
			$ttt_acc_sgst =0;
            $ttt_acc_cess =0;
			$ttt_acc_nonadv_cess =0;
		    $tt_total_vch_counters=0;
			if($vouchers_result){
			foreach($vouchers_result as $row){
			    $response=array();
				$voucher_txn_id = $row['voucher_txn_id'];
				$voucher_type_id = $row['voucher_type_id'];
				$total_vch_counters=0;
				
				
				if($tableinfo['outsup_dr_note']=="1" && $voucher_type_id=="3"){
				   $voucher_txn_id = $row['voucher_txn_id'];
				    
						$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
						$builders = $this->db->table($acctgstsum_tbl.' gstsum');
    					$builders->join($gstroutsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
    					$builders->select('gstsup.outsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
    					$builders->orderBy('gstsum.vch_txn_id');	        
    					$builders->where('gstsup.outsup_rev_chg',$tableinfo['outsup_rev_chg']);
    					$builders->where('gstsup.outsup_eco',$tableinfo['outsup_eco']);
    					$builders->where('gstsup.outsup_dr_note',$tableinfo['outsup_dr_note']);
    					$builders->where('gstsum.vch_txn_id', $voucher_txn_id);
    					$builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    					$builders->whereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);	
    				    $builders->where('gstsum.acc_txn_date >=', $from_date);
    					$builders->where('gstsum.acc_txn_date <=', $to_date);
						$builders->groupBy('gstsum.vch_txn_id');		 
				   	    $txncounter = $builders->countAllResults();
					    $total_vch_counters +=$txncounter;
					 
					    $tt_total_vch_counters +=$total_vch_counters;
						
						
				    $builders = $this->db->table($acctgstsum_tbl.' gstsum');
					$builders->join($gstrinwsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
					$builders->select('gstsup.inwsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
					$builders->orderBy('gstsum.vch_txn_id');	        
					$builders->where('gstsup.inwsup_rev_chg',$tableinfo['outsup_rev_chg']);
					$builders->where('gstsup.inwsup_eco',$tableinfo['outsup_eco']);
					$builders->where('gstsup.inwsup_dr_note',$tableinfo['outsup_dr_note']);
					$builders->where('gstsum.vch_txn_id', $voucher_txn_id);
					$builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
					$builders->whereIn('gstsup.outsup_inv_type',$tableinfo['outsup_inv_type']);	
				    $builders->where('gstsum.acc_txn_date >=', $from_date);
					$builders->where('gstsum.acc_txn_date <=', $to_date);
					$response = $builders->get()->getResultArray();
					
				 } 
			    else if($tableinfo['outsup_dr_note']=="0" && $tableinfo['outsup_cr_note']=="0")
				{		
			
			     
					 $voucher_txn_id = $row['voucher_txn_id'];
					/****** Counter ************/ 
						$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
					 $builders = $this->db->table($acctgstsum_tbl.' gstsum');
    				$builders->join($gstrinwsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
    				$builders->select('gstsup.inwsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
    				$builders->orderBy('gstsum.vch_txn_id');	        
    				$builders->where('gstsup.inwsup_rev_chg',$tableinfo['outsup_rev_chg']);
    				$builders->where('gstsup.inwsup_eco',$tableinfo['outsup_eco']);
    				$builders->where('gstsum.vch_txn_id', $voucher_txn_id);	
    				$builders->where('gstsum.acc_txn_date >=', $from_date);
    				$builders->where('gstsum.acc_txn_date <=', $to_date);				
    				if(in_array("DE",$tableinfo['outsup_inv_type']) && $tableinfo['value']=='0'){
    				  $builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    				  $builders->orWhereIn('gstsup.inwsup_inv_type',$tableinfo['outsup_inv_type']);				  
    				}
    				 elseif(isset($tableinfo['cnd']) && $tableinfo['cnd']!='' && $tableinfo['value']!=''){
    				  $builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    				  $builders->whereIn('gstsup.inwsup_inv_type',$tableinfo['outsup_inv_type']);	
    				  $builders->where('(gstsum.total_tax+gstsum.taxable_amt) '.$tableinfo['cnd'].'',$tableinfo['value']);		
    				} 
    				else{			
    				  $builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    				  $builders->whereIn('gstsup.inwsup_inv_type',$tableinfo['outsup_inv_type']);				  
    				  }
					 $builders->whereNotIn('gstsup.inwsup_pos', [96, 97]); 
    				 $builders->groupBy('gstsum.vch_txn_id');		 
					 $txncounter = $builders->countAllResults();
					  
					 $total_vch_counters +=$txncounter;
					 
					 $tt_total_vch_counters +=$total_vch_counters;
					 
					/******* Records ***********/ 
    				$builders = $this->db->table($acctgstsum_tbl.' gstsum');
    				$builders->join($gstrinwsup_tbl.' gstsup','gstsup.voucher_txn_id=gstsum.vch_txn_id');
    				$builders->select('gstsup.inwsup_bill_ref_no as bill_ref_no,gstsum.taxable_amt,gstsum.acc_igst,gstsum.acc_cgst,gstsum.acc_sgst,gstsum.acc_cess,gstsum.acc_nonadv_cess,gstsum.total_tax,gstsum.acc_txn_date');
    				$builders->orderBy('gstsum.vch_txn_id');	        
    				$builders->where('gstsup.inwsup_rev_chg',$tableinfo['outsup_rev_chg']);
    				$builders->where('gstsup.inwsup_eco',$tableinfo['outsup_eco']);
    				$builders->where('gstsum.vch_txn_id', $voucher_txn_id);	
    				$builders->where('gstsum.acc_txn_date >=', $from_date);
    				$builders->where('gstsum.acc_txn_date <=', $to_date);				
    				if(in_array("DE",$tableinfo['outsup_inv_type']) && $tableinfo['value']=='0'){
    				  $builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    				  $builders->orWhereIn('gstsup.inwsup_inv_type',$tableinfo['outsup_inv_type']);				  
    				}
    				 elseif(isset($tableinfo['cnd']) && $tableinfo['cnd']!='' && $tableinfo['value']!=''){
    				  $builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    				  $builders->whereIn('gstsup.inwsup_inv_type',$tableinfo['outsup_inv_type']);	
    				  $builders->where('(gstsum.total_tax+gstsum.taxable_amt) '.$tableinfo['cnd'].'',$tableinfo['value']);		
    				} 
    				else{			
    				  $builders->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code']);
    				  $builders->whereIn('gstsup.inwsup_inv_type',$tableinfo['outsup_inv_type']);				  
    				  }
					  $builders->whereNotIn('gstsup.inwsup_pos', [96, 97]);
				     $response = $builders->get()->getResultArray();
				 
				 
			}		
					 
			$total_voucher=0;
			$tt_credit = 0;
	      	$tt_debit = 0;
	      	$tt_credit_total = 0;
	      	$tt_debit_total = 0;
			$tt_taxable_amt =0;
			$tt_acc_igst=0;
			$tt_acc_cgst=0;
			$tt_acc_sgst=0;
			$tt_acc_cess=0;
			$tt_acc_nonadv_cess=0;
			$tt_total_tax=0;
			$tt_total_value=0;
			$tt_total_voucher=0;
			  if($response){
				foreach($response as $txrow){
                    $total_voucher++;						
					$taxable_amt= $txrow['taxable_amt'];
					$acc_igst    = $txrow['acc_igst'];
					$acc_cgst    = $txrow['acc_cgst'];
					$acc_sgst    = $txrow['acc_sgst'];
					$acc_cess    = $txrow['acc_cess'];
					$acc_nonadv_cess    = $txrow['acc_nonadv_cess'];
					$total_tax         = $txrow['total_tax'];
					$bill_ref_no      = $txrow['bill_ref_no'];
					
					
					$tt_total_voucher +=$total_voucher;
					$tt_taxable_amt +=$taxable_amt;
					$tt_total_tax +=$total_tax;
					$tt_acc_igst +=$acc_igst;				
					$tt_acc_cgst +=$acc_cgst;
					$tt_acc_sgst +=$acc_sgst;				
					$tt_acc_cess +=$acc_cess;
					$tt_acc_nonadv_cess +=$acc_nonadv_cess;
					
					
					
			
				    }
				    
				    $ttt_taxable_amt +=$tt_taxable_amt;
				    $ttt_total_tax +=$tt_total_tax;
				    $ttt_acc_igst +=$tt_acc_igst;
				  	$ttt_acc_cgst +=$tt_acc_cgst;
				    $ttt_acc_sgst +=$tt_acc_sgst;
                    $ttt_acc_cess +=$tt_acc_cess;
				    $ttt_acc_nonadv_cess +=$tt_acc_nonadv_cess;


			      }
				  
				
			
			   }	
				
			}
			$total_invoice_value = $ttt_taxable_amt+$ttt_total_tax;
		
	$data=["total_vouchers"=>$tt_total_vch_counters,"invoice_value"=>$total_invoice_value,
		      'taxable_amt'=>$ttt_taxable_amt,"igst"=>$ttt_acc_igst,"cgst"=>$ttt_acc_cgst,
			  "sgst"=>$ttt_acc_sgst,"cess"=>$ttt_acc_cess,"nonadv_cess"=>$ttt_acc_nonadv_cess,
			       "total_tax"=>$ttt_total_tax
				  ];	
	return $data;		  
	}
	
	function importsezsupply_table_info($from_date, $to_date, $vouchers_result, $tableinfo) {
    $fy_id = $this->session->get('ses_comp_fy_id');
    $acctgstsum_tbl = $this->company_id . '_acctgstsum_' . $fy_id;
    $gstrinwsup_tbl = $this->company_id . '_gstrinwsup_' . $fy_id;

    $ttt_taxable_amt = $ttt_total_tax = $ttt_acc_igst = $ttt_acc_cgst = $ttt_acc_sgst = $ttt_acc_cess = $ttt_acc_nonadv_cess = $tt_total_vch_counters = 0;

    if (!empty($vouchers_result)) {
        $this->db->query("SET sql_mode = (SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");

        foreach ($vouchers_result as $row) {
            $voucher_txn_id = $row['voucher_txn_id'];
			$vch_subtype_id = $row['vch_subtype_id'];
			if($vch_subtype_id!=0){

            // Shared builder for both count and data
            $builder = $this->db->table("{$acctgstsum_tbl} gstsum")
                ->join("{$gstrinwsup_tbl} gstsup", 'gstsup.voucher_txn_id = gstsum.vch_txn_id')
                ->select('gstsup.inwsup_bill_ref_no as bill_ref_no, gstsum.taxable_amt, gstsum.acc_igst, gstsum.acc_cgst, gstsum.acc_sgst, gstsum.acc_cess, gstsum.acc_nonadv_cess, gstsum.total_tax, gstsum.acc_txn_date')
                ->where('gstsup.inwsup_rev_chg', $tableinfo['outsup_rev_chg'])
                ->where('gstsup.inwsup_eco', $tableinfo['outsup_eco'])
                ->where('gstsum.vch_txn_id', $voucher_txn_id)
                ->where('gstsum.acc_txn_date >=', $from_date)
                ->where('gstsum.acc_txn_date <=', $to_date)
                ->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code'])
                ->whereIn('gstsup.inwsup_pos', [96, 97])
                ->orderBy('gstsum.vch_txn_id');

            // Clone builder for count
            $count_builder = clone $builder;
            $count_builder->groupBy('gstsum.vch_txn_id');
            $txncounter = $count_builder->countAllResults(false);
            $tt_total_vch_counters += $txncounter;

            // Fetch data
            $result = $builder->get()->getResultArray();
			
            if (!$result) continue;

            foreach ($result as $txrow) {
                $ttt_taxable_amt += $txrow['taxable_amt'];
                $ttt_total_tax += $txrow['total_tax'];
                $ttt_acc_igst += $txrow['acc_igst'];
                $ttt_acc_cgst += $txrow['acc_cgst'];
                $ttt_acc_sgst += $txrow['acc_sgst'];
                $ttt_acc_cess += $txrow['acc_cess'];
                $ttt_acc_nonadv_cess += $txrow['acc_nonadv_cess'];
               }
			}
        }
    }

    $total_invoice_value = $ttt_taxable_amt + $ttt_total_tax;

    return [
        "total_vouchers" => $tt_total_vch_counters,
        "invoice_value" => $total_invoice_value,
        "taxable_amt" => $ttt_taxable_amt,
        "igst" => $ttt_acc_igst,
        "cgst" => $ttt_acc_cgst,
        "sgst" => $ttt_acc_sgst,
        "cess" => $ttt_acc_cess,
        "nonadv_cess" => $ttt_acc_nonadv_cess,
        "total_tax" => $ttt_total_tax
    ];
}
function IMPORTS_SEZ_SUPPLIES_table_info($from_date, $to_date, $vouchers_result, $tableinfo) {
    $fy_id = $this->session->get('ses_comp_fy_id');
    $acctgstsum_tbl = $this->company_id . '_acctgstsum_' . $fy_id;
    $gstrinwsup_tbl = $this->company_id . '_gstrinwsup_' . $fy_id;

    $ttt_taxable_amt = $ttt_total_tax = $ttt_acc_igst = $ttt_acc_cgst = $ttt_acc_sgst = $ttt_acc_cess = $ttt_acc_nonadv_cess = $tt_total_vch_counters = 0;

    if (!empty($vouchers_result)) {
        $this->db->query("SET sql_mode = (SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
     if($tableinfo['tablekey']=='5A_IMP'){
        foreach ($vouchers_result as $row) {
            $voucher_txn_id = $row['voucher_txn_id'];
			$vch_subtype_id = $row['vch_subtype_id'];
			if($vch_subtype_id!=0){

            // Shared builder for both count and data
            $builder = $this->db->table("{$acctgstsum_tbl} gstsum")
                ->join("{$gstrinwsup_tbl} gstsup", 'gstsup.voucher_txn_id = gstsum.vch_txn_id')
                ->select('gstsup.inwsup_bill_ref_no as bill_ref_no, gstsum.taxable_amt, gstsum.acc_igst, gstsum.acc_cgst, gstsum.acc_sgst, gstsum.acc_cess, gstsum.acc_nonadv_cess, gstsum.total_tax, gstsum.acc_txn_date')
                ->where('gstsup.inwsup_rev_chg', $tableinfo['outsup_rev_chg'])
                ->where('gstsup.inwsup_eco', $tableinfo['outsup_eco'])
                ->where('gstsum.vch_txn_id', $voucher_txn_id)
                ->where('gstsum.acc_txn_date >=', $from_date)
                ->where('gstsum.acc_txn_date <=', $to_date)
                ->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code'])
                ->whereIn('gstsup.inwsup_pos', [96])
                ->orderBy('gstsum.vch_txn_id');

            // Clone builder for count
            $count_builder = clone $builder;
            $count_builder->groupBy('gstsum.vch_txn_id');
            $txncounter = $count_builder->countAllResults(false);
            $tt_total_vch_counters += $txncounter;

            // Fetch data
            $result = $builder->get()->getResultArray();
			
            if (!$result) continue;

            foreach ($result as $txrow) {
                $ttt_taxable_amt += $txrow['taxable_amt'];
                $ttt_total_tax += $txrow['total_tax'];
                $ttt_acc_igst += $txrow['acc_igst'];
                $ttt_acc_cgst += $txrow['acc_cgst'];
                $ttt_acc_sgst += $txrow['acc_sgst'];
                $ttt_acc_cess += $txrow['acc_cess'];
                $ttt_acc_nonadv_cess += $txrow['acc_nonadv_cess'];
               }
			}
	} }
	if($tableinfo['tablekey']=='5B_RCDSEZ'){
        foreach ($vouchers_result as $row) {
            $voucher_txn_id = $row['voucher_txn_id'];
			$vch_subtype_id = $row['vch_subtype_id'];
			if($vch_subtype_id!=0){

            // Shared builder for both count and data
            $builder = $this->db->table("{$acctgstsum_tbl} gstsum")
                ->join("{$gstrinwsup_tbl} gstsup", 'gstsup.voucher_txn_id = gstsum.vch_txn_id')
                ->select('gstsup.inwsup_bill_ref_no as bill_ref_no, gstsum.taxable_amt, gstsum.acc_igst, gstsum.acc_cgst, gstsum.acc_sgst, gstsum.acc_cess, gstsum.acc_nonadv_cess, gstsum.total_tax, gstsum.acc_txn_date')
                ->where('gstsup.inwsup_rev_chg', $tableinfo['outsup_rev_chg'])
                ->where('gstsup.inwsup_eco', $tableinfo['outsup_eco'])
                ->where('gstsum.vch_txn_id', $voucher_txn_id)
                ->where('gstsum.acc_txn_date >=', $from_date)
                ->where('gstsum.acc_txn_date <=', $to_date)
                ->where('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code'])
                ->whereIn('gstsup.inwsup_pos', [97])
                ->orderBy('gstsum.vch_txn_id');

            // Clone builder for count
            $count_builder = clone $builder;
            $count_builder->groupBy('gstsum.vch_txn_id');
            $txncounter = $count_builder->countAllResults(false);
            $tt_total_vch_counters += $txncounter;

            // Fetch data
            $result = $builder->get()->getResultArray();
			
            if (!$result) continue;

            foreach ($result as $txrow) {
                $ttt_taxable_amt += $txrow['taxable_amt'];
                $ttt_total_tax += $txrow['total_tax'];
                $ttt_acc_igst += $txrow['acc_igst'];
                $ttt_acc_cgst += $txrow['acc_cgst'];
                $ttt_acc_sgst += $txrow['acc_sgst'];
                $ttt_acc_cess += $txrow['acc_cess'];
                $ttt_acc_nonadv_cess += $txrow['acc_nonadv_cess'];
               }
			}
	} }
    }

    $total_invoice_value = $ttt_taxable_amt + $ttt_total_tax;

    return [
        "total_vouchers" => $tt_total_vch_counters,
        "invoice_value" => $total_invoice_value,
        "taxable_amt" => $ttt_taxable_amt,
        "igst" => $ttt_acc_igst,
        "cgst" => $ttt_acc_cgst,
        "sgst" => $ttt_acc_sgst,
        "cess" => $ttt_acc_cess,
        "nonadv_cess" => $ttt_acc_nonadv_cess,
        "total_tax" => $ttt_total_tax
    ];
}

function interstatesupply_table_info($from_date, $to_date, $vouchers_result, $tableinfo) {
    $fy_id = $this->session->get('ses_comp_fy_id');
	$bo_id = $this->bo_id;
	$ses_bostecd = $this->session->get('ses_bostecd');
	$state_code = sprintf( '%02d', $ses_bostecd );
    $acctgstsum_tbl = $this->company_id . '_acctgstsum_' . $fy_id;
    $gstrinwsup_tbl = $this->company_id . '_gstrinwsup_' . $fy_id;

    $ttt_taxable_amt = $ttt_total_tax = $ttt_acc_igst = $ttt_acc_cgst = $ttt_acc_sgst = $ttt_acc_cess = $ttt_acc_nonadv_cess = $tt_total_vch_counters = 0;

    if (!empty($vouchers_result)) {
        $this->db->query("SET sql_mode = (SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");

        foreach ($vouchers_result as $row) {
            $voucher_txn_id = $row['voucher_txn_id'];
			$vch_subtype_id = $row['vch_subtype_id'];			
            // Shared builder for both count and data
            $builder = $this->db->table("{$acctgstsum_tbl} gstsum")
                ->join("{$gstrinwsup_tbl} gstsup", 'gstsup.voucher_txn_id = gstsum.vch_txn_id')
                ->select('gstsup.inwsup_bill_ref_no as bill_ref_no, gstsum.taxable_amt, gstsum.acc_igst, gstsum.acc_cgst, gstsum.acc_sgst, gstsum.acc_cess, gstsum.acc_nonadv_cess, gstsum.total_tax, gstsum.acc_txn_date')
                ->where('gstsup.inwsup_rev_chg', $tableinfo['outsup_rev_chg'])
                ->where('gstsup.inwsup_eco', $tableinfo['outsup_eco'])
                ->where('gstsum.vch_txn_id', $voucher_txn_id)
                ->where('gstsum.acc_txn_date >=', $from_date)
                ->where('gstsum.acc_txn_date <=', $to_date)
                ->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code'])
                ->whereNotIn('gstsup.inwsup_pos', [$state_code])
                ->orderBy('gstsum.vch_txn_id');

            // Clone builder for count
            $count_builder = clone $builder;
            $count_builder->groupBy('gstsum.vch_txn_id');
            $txncounter = $count_builder->countAllResults(false);
            $tt_total_vch_counters += $txncounter;

            // Fetch data
            $result = $builder->get()->getResultArray();
            if (!$result) continue;

            foreach ($result as $txrow) {
                $ttt_taxable_amt += $txrow['taxable_amt'];
                $ttt_total_tax += $txrow['total_tax'];
                $ttt_acc_igst += $txrow['acc_igst'];
                $ttt_acc_cgst += $txrow['acc_cgst'];
                $ttt_acc_sgst += $txrow['acc_sgst'];
                $ttt_acc_cess += $txrow['acc_cess'];
                $ttt_acc_nonadv_cess += $txrow['acc_nonadv_cess'];
               }			
        }
    }

    $total_invoice_value = $ttt_taxable_amt + $ttt_total_tax;

    return [
        "total_vouchers" => $tt_total_vch_counters,
        "invoice_value" => $total_invoice_value,
        "taxable_amt" => $ttt_taxable_amt,
        "igst" => $ttt_acc_igst,
        "cgst" => $ttt_acc_cgst,
        "sgst" => $ttt_acc_sgst,
        "cess" => $ttt_acc_cess,
        "nonadv_cess" => $ttt_acc_nonadv_cess,
        "total_tax" => $ttt_total_tax
    ];
}
function INTERSTATE_SUPPLIES7A_table_info($from_date, $to_date, $vouchers_result, $tableinfo) {
    $fy_id = $this->session->get('ses_comp_fy_id');
	$Composition_exists    = $this->isCompositionGstin($from_date,$to_date);
	$bo_id = $this->bo_id;
	$ses_bostecd = $this->session->get('ses_bostecd');
	$state_code = sprintf( '%02d', $ses_bostecd );
    $acctgstsum_tbl = $this->company_id . '_acctgstsum_' . $fy_id;
    $gstrinwsup_tbl = $this->company_id . '_gstrinwsup_' . $fy_id;

    $ttt_taxable_amt = $ttt_total_tax = $ttt_acc_igst = $ttt_acc_cgst = $ttt_acc_sgst = $ttt_acc_cess = $ttt_acc_nonadv_cess = $tt_total_vch_counters = 0;

    if (!empty($vouchers_result)) {
        $this->db->query("SET sql_mode = (SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		
		if($tableinfo['tablekey']=='7A_1' && $Composition_exists){
			foreach ($vouchers_result as $row) {
            $voucher_txn_id = $row['voucher_txn_id'];
			$vch_subtype_id = $row['vch_subtype_id'];			
            // Shared builder for both count and data
            $builder = $this->db->table("{$acctgstsum_tbl} gstsum")
                ->join("{$gstrinwsup_tbl} gstsup", 'gstsup.voucher_txn_id = gstsum.vch_txn_id')
                ->select('gstsup.inwsup_bill_ref_no as bill_ref_no, gstsum.taxable_amt, gstsum.acc_igst, gstsum.acc_cgst, gstsum.acc_sgst, gstsum.acc_cess, gstsum.acc_nonadv_cess, gstsum.total_tax, gstsum.acc_txn_date')
                ->where('gstsup.inwsup_rev_chg', $tableinfo['outsup_rev_chg'])
                ->where('gstsup.inwsup_eco', $tableinfo['outsup_eco'])
                ->where('gstsum.vch_txn_id', $voucher_txn_id)
                ->where('gstsum.acc_txn_date >=', $from_date)
                ->where('gstsum.acc_txn_date <=', $to_date)
                ->whereNotIn('gstsup.inwsup_pos', [$state_code])
                ->orderBy('gstsum.vch_txn_id');

            // Clone builder for count
            $count_builder = clone $builder;
            $count_builder->groupBy('gstsum.vch_txn_id');
            $txncounter = $count_builder->countAllResults(false);
            $tt_total_vch_counters += $txncounter;

            // Fetch data
            $result = $builder->get()->getResultArray();
            if (!$result) continue;

            foreach ($result as $txrow) {
                $ttt_taxable_amt += $txrow['taxable_amt'];
                $ttt_total_tax += $txrow['total_tax'];
                $ttt_acc_igst += $txrow['acc_igst'];
                $ttt_acc_cgst += $txrow['acc_cgst'];
                $ttt_acc_sgst += $txrow['acc_sgst'];
                $ttt_acc_cess += $txrow['acc_cess'];
                $ttt_acc_nonadv_cess += $txrow['acc_nonadv_cess'];
               }			
        }
			
		}
		if($tableinfo['tablekey']=='7A_2' || $tableinfo['tablekey']=='7A_3' || $tableinfo['tablekey']=='7A_4'){
			foreach ($vouchers_result as $row) {
            $voucher_txn_id = $row['voucher_txn_id'];
			$vch_subtype_id = $row['vch_subtype_id'];			
            // Shared builder for both count and data
            $builder = $this->db->table("{$acctgstsum_tbl} gstsum")
                ->join("{$gstrinwsup_tbl} gstsup", 'gstsup.voucher_txn_id = gstsum.vch_txn_id')
                ->select('gstsup.inwsup_bill_ref_no as bill_ref_no, gstsum.taxable_amt, gstsum.acc_igst, gstsum.acc_cgst, gstsum.acc_sgst, gstsum.acc_cess, gstsum.acc_nonadv_cess, gstsum.total_tax, gstsum.acc_txn_date')
                ->where('gstsup.inwsup_rev_chg', $tableinfo['outsup_rev_chg'])
                ->where('gstsup.inwsup_eco', $tableinfo['outsup_eco'])
                ->where('gstsum.vch_txn_id', $voucher_txn_id)
                ->where('gstsum.acc_txn_date >=', $from_date)
                ->where('gstsum.acc_txn_date <=', $to_date)
                ->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code'])
				->whereNotIn('gstsup.inwsup_pos', [$state_code])
                ->orderBy('gstsum.vch_txn_id');

            // Clone builder for count
            $count_builder = clone $builder;
            $count_builder->groupBy('gstsum.vch_txn_id');
            $txncounter = $count_builder->countAllResults(false);
            $tt_total_vch_counters += $txncounter;

            // Fetch data
            $result = $builder->get()->getResultArray();
            if (!$result) continue;

            foreach ($result as $txrow) {
                $ttt_taxable_amt += $txrow['taxable_amt'];
                $ttt_total_tax += $txrow['total_tax'];
                $ttt_acc_igst += $txrow['acc_igst'];
                $ttt_acc_cgst += $txrow['acc_cgst'];
                $ttt_acc_sgst += $txrow['acc_sgst'];
                $ttt_acc_cess += $txrow['acc_cess'];
                $ttt_acc_nonadv_cess += $txrow['acc_nonadv_cess'];
               }			
        }
			
		}
		
        
    }

    $total_invoice_value = $ttt_taxable_amt + $ttt_total_tax;

    return [
        "total_vouchers" => $tt_total_vch_counters,
        "invoice_value" => $total_invoice_value,
        "taxable_amt" => $ttt_taxable_amt,
        "igst" => $ttt_acc_igst,
        "cgst" => $ttt_acc_cgst,
        "sgst" => $ttt_acc_sgst,
        "cess" => $ttt_acc_cess,
        "nonadv_cess" => $ttt_acc_nonadv_cess,
        "total_tax" => $ttt_total_tax
    ];
}function INTERSTATE_SUPPLIES7B_table_info($from_date, $to_date, $vouchers_result, $tableinfo) {
    $fy_id = $this->session->get('ses_comp_fy_id');
	$Composition_exists    = $this->isCompositionGstin($from_date,$to_date);
	$bo_id = $this->bo_id;
	$ses_bostecd = $this->session->get('ses_bostecd');
	$state_code = sprintf( '%02d', $ses_bostecd );
    $acctgstsum_tbl = $this->company_id . '_acctgstsum_' . $fy_id;
    $gstrinwsup_tbl = $this->company_id . '_gstrinwsup_' . $fy_id;

    $ttt_taxable_amt = $ttt_total_tax = $ttt_acc_igst = $ttt_acc_cgst = $ttt_acc_sgst = $ttt_acc_cess = $ttt_acc_nonadv_cess = $tt_total_vch_counters = 0;

    if (!empty($vouchers_result)) {
        $this->db->query("SET sql_mode = (SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		
		if($tableinfo['tablekey']=='7B_1' && $Composition_exists){
			foreach ($vouchers_result as $row) {
            $voucher_txn_id = $row['voucher_txn_id'];
			$vch_subtype_id = $row['vch_subtype_id'];			
            // Shared builder for both count and data
            $builder = $this->db->table("{$acctgstsum_tbl} gstsum")
                ->join("{$gstrinwsup_tbl} gstsup", 'gstsup.voucher_txn_id = gstsum.vch_txn_id')
                ->select('gstsup.inwsup_bill_ref_no as bill_ref_no, gstsum.taxable_amt, gstsum.acc_igst, gstsum.acc_cgst, gstsum.acc_sgst, gstsum.acc_cess, gstsum.acc_nonadv_cess, gstsum.total_tax, gstsum.acc_txn_date')
                ->where('gstsup.inwsup_rev_chg', $tableinfo['outsup_rev_chg'])
                ->where('gstsup.inwsup_eco', $tableinfo['outsup_eco'])
                ->where('gstsum.vch_txn_id', $voucher_txn_id)
                ->where('gstsum.acc_txn_date >=', $from_date)
                ->where('gstsum.acc_txn_date <=', $to_date)
                ->whereIn('gstsup.inwsup_pos', [$state_code])
                ->orderBy('gstsum.vch_txn_id');

            // Clone builder for count
            $count_builder = clone $builder;
            $count_builder->groupBy('gstsum.vch_txn_id');
            $txncounter = $count_builder->countAllResults(false);
            $tt_total_vch_counters += $txncounter;

            // Fetch data
            $result = $builder->get()->getResultArray();
            if (!$result) continue;

            foreach ($result as $txrow) {
                $ttt_taxable_amt += $txrow['taxable_amt'];
                $ttt_total_tax += $txrow['total_tax'];
                $ttt_acc_igst += $txrow['acc_igst'];
                $ttt_acc_cgst += $txrow['acc_cgst'];
                $ttt_acc_sgst += $txrow['acc_sgst'];
                $ttt_acc_cess += $txrow['acc_cess'];
                $ttt_acc_nonadv_cess += $txrow['acc_nonadv_cess'];
               }			
        }
			
		}
		if($tableinfo['tablekey']=='7B_2' || $tableinfo['tablekey']=='7B_3' || $tableinfo['tablekey']=='7B_4'){
			foreach ($vouchers_result as $row) {
            $voucher_txn_id = $row['voucher_txn_id'];
			$vch_subtype_id = $row['vch_subtype_id'];			
            // Shared builder for both count and data
            $builder = $this->db->table("{$acctgstsum_tbl} gstsum")
                ->join("{$gstrinwsup_tbl} gstsup", 'gstsup.voucher_txn_id = gstsum.vch_txn_id')
                ->select('gstsup.inwsup_bill_ref_no as bill_ref_no, gstsum.taxable_amt, gstsum.acc_igst, gstsum.acc_cgst, gstsum.acc_sgst, gstsum.acc_cess, gstsum.acc_nonadv_cess, gstsum.total_tax, gstsum.acc_txn_date')
                ->where('gstsup.inwsup_rev_chg', $tableinfo['outsup_rev_chg'])
                ->where('gstsup.inwsup_eco', $tableinfo['outsup_eco'])
                ->where('gstsum.vch_txn_id', $voucher_txn_id)
                ->where('gstsum.acc_txn_date >=', $from_date)
                ->where('gstsum.acc_txn_date <=', $to_date)
                ->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code'])
				->whereIn('gstsup.inwsup_pos', [$state_code])
                ->orderBy('gstsum.vch_txn_id');

            // Clone builder for count
            $count_builder = clone $builder;
            $count_builder->groupBy('gstsum.vch_txn_id');
            $txncounter = $count_builder->countAllResults(false);
            $tt_total_vch_counters += $txncounter;

            // Fetch data
            $result = $builder->get()->getResultArray();
            if (!$result) continue;

            foreach ($result as $txrow) {
                $ttt_taxable_amt += $txrow['taxable_amt'];
                $ttt_total_tax += $txrow['total_tax'];
                $ttt_acc_igst += $txrow['acc_igst'];
                $ttt_acc_cgst += $txrow['acc_cgst'];
                $ttt_acc_sgst += $txrow['acc_sgst'];
                $ttt_acc_cess += $txrow['acc_cess'];
                $ttt_acc_nonadv_cess += $txrow['acc_nonadv_cess'];
               }			
        }
			
		}
		
        
    }

    $total_invoice_value = $ttt_taxable_amt + $ttt_total_tax;

    return [
        "total_vouchers" => $tt_total_vch_counters,
        "invoice_value" => $total_invoice_value,
        "taxable_amt" => $ttt_taxable_amt,
        "igst" => $ttt_acc_igst,
        "cgst" => $ttt_acc_cgst,
        "sgst" => $ttt_acc_sgst,
        "cess" => $ttt_acc_cess,
        "nonadv_cess" => $ttt_acc_nonadv_cess,
        "total_tax" => $ttt_total_tax
    ];
}

function B2B_LIABLE_RCM4_table_info($from_date, $to_date, $vouchers_result, $tableinfo) {
	
    $fy_id           = $this->session->get('ses_comp_fy_id');
	$Gstin_exists    = $this->isGstin($from_date,$to_date);
	$bo_id           = $this->bo_id;
	$ses_bostecd     = $this->session->get('ses_bostecd');
	$state_code      = sprintf( '%02d', $ses_bostecd );
    $acctgstsum_tbl  = $this->company_id . '_acctgstsum_' . $fy_id;
    $gstrinwsup_tbl  = $this->company_id . '_gstrinwsup_' . $fy_id;
    $ttt_taxable_amt = $ttt_total_tax = $ttt_acc_igst = $ttt_acc_cgst = $ttt_acc_sgst = $ttt_acc_cess = $ttt_acc_nonadv_cess = $tt_total_vch_counters = 0;
    if (!empty($vouchers_result)) {
        $this->db->query("SET sql_mode = (SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");
		if($tableinfo['tablekey']=='4A_B2B' && $Gstin_exists){
        foreach ($vouchers_result as $row) {
            $voucher_txn_id = $row['voucher_txn_id'];
			$vch_subtype_id = $row['vch_subtype_id'];			
            // Shared builder for both count and data
            $builder = $this->db->table("{$acctgstsum_tbl} gstsum")
                ->join("{$gstrinwsup_tbl} gstsup", 'gstsup.voucher_txn_id = gstsum.vch_txn_id')
                ->select('gstsup.inwsup_bill_ref_no as bill_ref_no, gstsum.taxable_amt, gstsum.acc_igst, gstsum.acc_cgst, gstsum.acc_sgst, gstsum.acc_cess, gstsum.acc_nonadv_cess, gstsum.total_tax, gstsum.acc_txn_date')
                ->where('gstsup.inwsup_rev_chg', $tableinfo['outsup_rev_chg'])
                ->where('gstsup.inwsup_eco', $tableinfo['outsup_eco'])
                ->where('gstsum.vch_txn_id', $voucher_txn_id)
                ->where('gstsum.acc_txn_date >=', $from_date)
                ->where('gstsum.acc_txn_date <=', $to_date)
                ->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code'])
				->whereIn('gstsup.inwsup_inv_type',$tableinfo['outsup_inv_type'])		
                ->orderBy('gstsum.vch_txn_id');

            // Clone builder for count
            $count_builder = clone $builder;
            $count_builder->groupBy('gstsum.vch_txn_id');
            $txncounter = $count_builder->countAllResults(false);
            $tt_total_vch_counters += $txncounter;
            // Fetch data
            $result = $builder->get()->getResultArray();
            if (!$result) continue;
            foreach ($result as $txrow) {
                $ttt_taxable_amt += $txrow['taxable_amt'];
                $ttt_total_tax += $txrow['total_tax'];
                $ttt_acc_igst += $txrow['acc_igst'];
                $ttt_acc_cgst += $txrow['acc_cgst'];
                $ttt_acc_sgst += $txrow['acc_sgst'];
                $ttt_acc_cess += $txrow['acc_cess'];
                $ttt_acc_nonadv_cess += $txrow['acc_nonadv_cess'];
               }			
            }
		 }
		 if($tableinfo['tablekey']=='4A_B2BUR' && !$Gstin_exists){
        foreach ($vouchers_result as $row) {
            $voucher_txn_id = $row['voucher_txn_id'];
			$vch_subtype_id = $row['vch_subtype_id'];			
            // Shared builder for both count and data
            $builder = $this->db->table("{$acctgstsum_tbl} gstsum")
                ->join("{$gstrinwsup_tbl} gstsup", 'gstsup.voucher_txn_id = gstsum.vch_txn_id')
                ->select('gstsup.inwsup_bill_ref_no as bill_ref_no, gstsum.taxable_amt, gstsum.acc_igst, gstsum.acc_cgst, gstsum.acc_sgst, gstsum.acc_cess, gstsum.acc_nonadv_cess, gstsum.total_tax, gstsum.acc_txn_date')
                ->where('gstsup.inwsup_rev_chg', $tableinfo['outsup_rev_chg'])
                ->where('gstsup.inwsup_eco', $tableinfo['outsup_eco'])
                ->where('gstsum.vch_txn_id', $voucher_txn_id)
                ->where('gstsum.acc_txn_date >=', $from_date)
                ->where('gstsum.acc_txn_date <=', $to_date)
                ->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code'])
				->whereIn('gstsup.inwsup_inv_type',$tableinfo['outsup_inv_type'])		
                ->orderBy('gstsum.vch_txn_id');

            // Clone builder for count
            $count_builder = clone $builder;
            $count_builder->groupBy('gstsum.vch_txn_id');
            $txncounter = $count_builder->countAllResults(false);
            $tt_total_vch_counters += $txncounter;
            // Fetch data
            $result = $builder->get()->getResultArray();
            if (!$result) continue;
            foreach ($result as $txrow) {
                $ttt_taxable_amt += $txrow['taxable_amt'];
                $ttt_total_tax += $txrow['total_tax'];
                $ttt_acc_igst += $txrow['acc_igst'];
                $ttt_acc_cgst += $txrow['acc_cgst'];
                $ttt_acc_sgst += $txrow['acc_sgst'];
                $ttt_acc_cess += $txrow['acc_cess'];
                $ttt_acc_nonadv_cess += $txrow['acc_nonadv_cess'];
               }			
            }
		 }
		 if($tableinfo['tablekey']=='4A_IMPRT'){
        foreach ($vouchers_result as $row) {
            $voucher_txn_id = $row['voucher_txn_id'];
			$vch_subtype_id = $row['vch_subtype_id'];
			if($vch_subtype_id==0){	
            // Shared builder for both count and data
            $builder = $this->db->table("{$acctgstsum_tbl} gstsum")
                ->join("{$gstrinwsup_tbl} gstsup", 'gstsup.voucher_txn_id = gstsum.vch_txn_id')
                ->select('gstsup.inwsup_bill_ref_no as bill_ref_no, gstsum.taxable_amt, gstsum.acc_igst, gstsum.acc_cgst, gstsum.acc_sgst, gstsum.acc_cess, gstsum.acc_nonadv_cess, gstsum.total_tax, gstsum.acc_txn_date')
                ->where('gstsup.inwsup_rev_chg', $tableinfo['outsup_rev_chg'])
                ->where('gstsup.inwsup_eco', $tableinfo['outsup_eco'])
                ->where('gstsum.vch_txn_id', $voucher_txn_id)
                ->where('gstsum.acc_txn_date >=', $from_date)
                ->where('gstsum.acc_txn_date <=', $to_date)
                ->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code'])
				->whereIn('gstsup.inwsup_inv_type',$tableinfo['outsup_inv_type'])	
				->whereIn('gstsup.inwsup_pos', [96,97])	
                ->orderBy('gstsum.vch_txn_id');

            // Clone builder for count
            $count_builder = clone $builder;
            $count_builder->groupBy('gstsum.vch_txn_id');
            $txncounter = $count_builder->countAllResults(false);
            $tt_total_vch_counters += $txncounter;
            // Fetch data
            $result = $builder->get()->getResultArray();
            if (!$result) continue;
            foreach ($result as $txrow) {
                $ttt_taxable_amt += $txrow['taxable_amt'];
                $ttt_total_tax += $txrow['total_tax'];
                $ttt_acc_igst += $txrow['acc_igst'];
                $ttt_acc_cgst += $txrow['acc_cgst'];
                $ttt_acc_sgst += $txrow['acc_sgst'];
                $ttt_acc_cess += $txrow['acc_cess'];
                $ttt_acc_nonadv_cess += $txrow['acc_nonadv_cess'];
               }			
            }
		   }
		 }
    }

    $total_invoice_value = $ttt_taxable_amt + $ttt_total_tax;

    return [
        "total_vouchers" => $tt_total_vch_counters,
        "invoice_value" => $total_invoice_value,
        "taxable_amt" => $ttt_taxable_amt,
        "igst" => $ttt_acc_igst,
        "cgst" => $ttt_acc_cgst,
        "sgst" => $ttt_acc_sgst,
        "cess" => $ttt_acc_cess,
        "nonadv_cess" => $ttt_acc_nonadv_cess,
        "total_tax" => $ttt_total_tax
    ];
}

function hsnsummary_table_info($from_date,$to_date,$vouchers_result,$tableinfo){		
            $acctgstsum_tbl   = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
			$gstrinwsup_tbl   = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');

			$total_data = [
				'total_vouchers'     => 0,
				'invoice_value'      => 0,
				'taxable_amt'        => 0,
				'igst'               => 0,
				'cgst'               => 0,
				'sgst'               => 0,
				'cess'               => 0,
				'nonadv_cess'        => 0,
				'total_tax'          => 0
			];

			if ($vouchers_result) {
				$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");

				foreach ($vouchers_result as $row) {
					if ($row['voucher_type_id'] != 11) {
						continue;
					}

					$voucher_txn_id = $row['voucher_txn_id'];

					// Fetch GST summary records directly
					$records = $this->db->table("{$acctgstsum_tbl} gstsum")
						->join("{$gstrinwsup_tbl} gstsup", 'gstsup.voucher_txn_id = gstsum.vch_txn_id')
						->select('gstsup.inwsup_bill_ref_no as bill_ref_no, gstsum.taxable_amt, gstsum.acc_igst, gstsum.acc_cgst, gstsum.acc_sgst, gstsum.acc_cess, gstsum.acc_nonadv_cess, gstsum.total_tax')
						->where('gstsum.vch_txn_id', $voucher_txn_id)
						->where('gstsum.acc_txn_date >=', $from_date)
						->where('gstsum.acc_txn_date <=', $to_date)
						->get()
						->getResultArray();

					$voucher_count = count($records);
					$total_data['total_vouchers'] += $voucher_count;

					foreach ($records as $r) {
						$total_data['taxable_amt']    += (float)$r['taxable_amt'];
						$total_data['igst']           += (float)$r['acc_igst'];
						$total_data['cgst']           += (float)$r['acc_cgst'];
						$total_data['sgst']           += (float)$r['acc_sgst'];
						$total_data['cess']           += (float)$r['acc_cess'];
						$total_data['nonadv_cess']    += (float)$r['acc_nonadv_cess'];
						$total_data['total_tax']      += (float)$r['total_tax'];
					}
				}

				// Final invoice value
				$total_data['invoice_value'] = $total_data['taxable_amt'] + $total_data['total_tax'];
			}

			return $total_data;		  
	
	}
function intrastatesupply_table_info($from_date, $to_date, $vouchers_result, $tableinfo) {
    $fy_id = $this->session->get('ses_comp_fy_id');
	$ses_bostecd = $this->session->get('ses_bostecd');
	$bo_id = $this->bo_id;
	$state_code = sprintf( '%02d', $ses_bostecd );
    $acctgstsum_tbl = $this->company_id . '_acctgstsum_' . $fy_id;
    $gstrinwsup_tbl = $this->company_id . '_gstrinwsup_' . $fy_id;

    $ttt_taxable_amt = $ttt_total_tax = $ttt_acc_igst = $ttt_acc_cgst = $ttt_acc_sgst = $ttt_acc_cess = $ttt_acc_nonadv_cess = $tt_total_vch_counters = 0;

    if (!empty($vouchers_result)) {
        $this->db->query("SET sql_mode = (SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', ''));");

        foreach ($vouchers_result as $row) {
            $voucher_txn_id = $row['voucher_txn_id'];
			$vch_subtype_id = $row['vch_subtype_id'];			
            // Shared builder for both count and data
            $builder = $this->db->table("{$acctgstsum_tbl} gstsum")
                ->join("{$gstrinwsup_tbl} gstsup", 'gstsup.voucher_txn_id = gstsum.vch_txn_id')
                ->select('gstsup.inwsup_bill_ref_no as bill_ref_no, gstsum.taxable_amt, gstsum.acc_igst, gstsum.acc_cgst, gstsum.acc_sgst, gstsum.acc_cess, gstsum.acc_nonadv_cess, gstsum.total_tax, gstsum.acc_txn_date')
                ->where('gstsup.inwsup_rev_chg', $tableinfo['outsup_rev_chg'])
                ->where('gstsup.inwsup_eco', $tableinfo['outsup_eco'])
                ->where('gstsum.vch_txn_id', $voucher_txn_id)
                ->where('gstsum.acc_txn_date >=', $from_date)
                ->where('gstsum.acc_txn_date <=', $to_date)
                ->whereIn('gstsum.cmp_tax_short_code', $tableinfo['cmp_tax_short_code'])
                ->whereIn('gstsup.inwsup_pos', [$state_code])
                ->orderBy('gstsum.vch_txn_id');

            // Clone builder for count
            $count_builder = clone $builder;
			
            $count_builder->groupBy('gstsum.vch_txn_id');
            $txncounter = $count_builder->countAllResults(false);
			
            $tt_total_vch_counters += $txncounter;

            // Fetch data
            $result = $builder->get()->getResultArray();
			//echo $this->db->getlastquery();
			//echo '--<br>--';
            if (!$result) continue;

            foreach ($result as $txrow) {
                $ttt_taxable_amt += $txrow['taxable_amt'];
                $ttt_total_tax += $txrow['total_tax'];
                $ttt_acc_igst += $txrow['acc_igst'];
                $ttt_acc_cgst += $txrow['acc_cgst'];
                $ttt_acc_sgst += $txrow['acc_sgst'];
                $ttt_acc_cess += $txrow['acc_cess'];
                $ttt_acc_nonadv_cess += $txrow['acc_nonadv_cess'];
               }
			
        }
    }

    $total_invoice_value = $ttt_taxable_amt + $ttt_total_tax;

    return [
        "total_vouchers" => $tt_total_vch_counters,
        "invoice_value" => $total_invoice_value,
        "taxable_amt" => $ttt_taxable_amt,
        "igst" => $ttt_acc_igst,
        "cgst" => $ttt_acc_cgst,
        "sgst" => $ttt_acc_sgst,
        "cess" => $ttt_acc_cess,
        "nonadv_cess" => $ttt_acc_nonadv_cess,
        "total_tax" => $ttt_total_tax
    ];
}
	
}