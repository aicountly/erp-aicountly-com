<?php
namespace App\Models\Admin;

use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Models\CommonModel;
use App\Libraries\enc_string;

class VouchersModel extends Model	{
  protected $externaldb;
  protected $univerpaic_db;
  protected $aicountly_db;
  protected $session;
  protected $company_id;
  protected $fy_id;
  protected $bo_id; 
  protected $profile_id;
  
    public function __construct() { 
       parent::__construct();        
       $this->externaldb     = new externaldb();	
	   $this->CommonModel    = new CommonModel();	
	   $this->univerpaic_db  = $this->externaldb->univerpaic_db();
	   $this->aicountly_db   = $this->externaldb->aicountly_db();
	   $this->session        = \Config\Services::session();
	   $this->company_id     = $this->session->get('ses_company_id');
	   $this->fy_id          = $this->session->get('ses_comp_fy_id');
       $this->bo_id          = $this->session->get('ses_boid');
	   $this->profile_id     = $this->session->get('ses_cmp_prf_id');
       $this->contactaic_db  = $this->externaldb->contactaic_db();	 
	   $this->univaictly    =  $this->externaldb->univaictly_db();	   
    }
    
	public function transporter_info($voucher_txn_id,$compId=null) {
			if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
        $row = $this->db->table('ewbmastern h')
            ->join('ewbpartbdt p', 'h.ewb_id = p.ewb_id', 'LEFT')
            ->where('h.cmp_id', $sel_compId)
            ->where('h.vch_txn_id', $voucher_txn_id)
            ->orderBy('p.tpt_update_date', 'DESC')  // Get latest entry
            ->orderBy('p.veh_update_date', 'DESC')  // Secondary sort
            ->limit(1)
            ->select([
                'h.ewb_id',
                'h.ewb_no',
                'h.ewb_date',
                'h.ewb_valid_dt',
                'h.ewb_status',
                'h.vch_txn_id',
                'h.cmp_id',
                'p.gsttpt_id',
                'p.trans_veh_no',
                'p.trans_veh_type',
                'p.trans_mode',
                'p.trans_doc_no',
                'p.trans_doc_date',
                'p.trans_rsn_code',
                'p.trans_rsn_rem',
                'p.trans_dist',
                'p.trans_frm_place',
                'p.trans_frm_state_code',
                'p.tpt_update_date',
                'p.veh_update_date',
				'p.tpt_id',
				'p.tpt_gstin'
            ])
            ->get()
            ->getRowArray();
	     return $row;
     }
     
     
	 
	public function get_bank_info($vch_series_id, $vch_type_id)
	{
		// Step 1: get bank_id from main DB
		$builder = $this->db->table('vchseriesn');
		$builder->select('bank_id');
		$builder->where('vch_series_id', $vch_series_id);
		$builder->where('vch_type_id', $vch_type_id);
		$builder->where('cmp_id', $this->company_id);

		$row = $builder->get()->getRowArray();

		if (!$row || empty($row['bank_id'])) {
			return null;
		}

		// Step 2: external DB call
		$univaictly = $this->externaldb->univaictly_db();

		$builder2 = $univaictly->table('cmpbankmst');
		$builder2->where('bank_id', $row['bank_id']);
		$builder2->where('cmp_id', $this->company_id);

		return $builder2->get()->getRowArray();
	}
	
	public function getHeadOfficeWithAddress(): array
	{
		$row = $this->univaictly->table('hobomaster h')
			->select('
				h.hobo_id, h.cmp_id, h.hobo_name, h.hobo_alias, h.hobo_op_date, h.hobo_cl_date, h.hobo_zone,
				a.hobo_addr_id, a.hobo_addr1, a.hobo_addr2, a.hobo_city, a.hobo_state, a.hobo_pin_zip, a.hobo_country
			')
			->join('hoboaddrmt a', 'a.hobo_id = h.hobo_id AND a.cmp_id = h.cmp_id', 'left')
			->where('h.cmp_id', $this->company_id)
			->where('h.mark_ho', 1)
			->orderBy('h.hobo_id', 'ASC')
			->get()
			->getRowArray();

		if (!$row) {
			return [];
		}

		$row['state_code'] = '';
		if (!empty($row['hobo_country']) && !empty($row['hobo_state'])) {
			$state = $this->CommonModel->get_state_info($row['hobo_country'], $row['hobo_state']);
			$row['state_code'] = sprintf('%02d', $state['state_code'] ?? 0);
		}

		return $row;
	}

    public function get_branch_gstin_info(int $bo_id): array
	{
		$builder = $this->univaictly->table('hobogstinm m');
		$builder->select('m.*, d.hobo_gstdet_id, d.hobo_gstin_jurisd, d.hobo_gstin_jurisd_st, d.hobo_gstin_jurisd_ct, d.hobo_gstin_legal_name, d.hobo_gstin_trade_name, d.hobo_gstin_wef_act, d.hobo_gstin_inact_date');
		$builder->join('hobogstdet d', 'd.hobo_gstin_id = m.hobo_gstin_id', 'left');
		$builder->where('m.hobo_id', $bo_id);
		$result =(array) $builder->get()->getRowArray();
		return $result;
	}
  
   function get_credntial_gstin_info($site_id=''){
	   return $this->db->table("erppassmgr")->where('cmp_id',$this->company_id)->where('erp_pass_id',$site_id)->get()->getRowArray(); 
   }
   
   function get_einvmaster_info($vch_txn_id,$compId=null){
			if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
	   return $this->db->table("einvmaster")->where('cmp_id',$sel_compId)->where('vch_txn_id',$vch_txn_id)->get()->getRowArray(); 
   }
   function getSeriesId($vch_type_id,$compId=null){
	   if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;

	   $row =  $this->db->table("vchseriesn")->where('cmp_id',$sel_compId)->where('vch_type_id',$vch_type_id)->get()->getRowArray(); 
	   if($row)
		   return $row['vch_series_id'];
	   else
		   return 0;
   }
   
   public function getVoucherOverrideSupplyTypeId($vch_txn_id,$compId=null): ?int
	{
		 if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;


		$builder = $this->db->table('vchgstsumn');
		$query = $builder
			->select('inv_supply_id')
			->where('vch_txn_id', $vch_txn_id)->where('cmp_id',$sel_compId)
			->whereIn('inv_supply_id', [16, 17, 18, 19, 20]) // voucher-level IDs
			->groupBy('inv_supply_id')
			->limit(1)
			->get();

		$row = $query->getRowArray();
		return $row['inv_supply_id'] ?? null;
	}
   
   public function add_transport_data($ewb_sub_supply_type,$ewb_id,$einv_id,$voucher_txn_id,$data){
	    $mst_data = [
		    'ewb_id'		 => $ewb_id,
			'trans_veh_no'  => $data['vehicle_no'],
			'trans_veh_type'		 => $data['vehicle_type'],
			'trans_mode'	 => $data['transport_mode'],
			'trans_doc_no'	 => $data['transporter_doc_no'],
			'trans_doc_date'	 => $data['transport_doc_date'] ?? date('Y-m-d'),
			'trans_dist'	 => $data['transport_distance'],
			'trans_frm_place'	 =>'' ,
			'trans_frm_state_code'	 => '',
			'vch_txn_id'	 => $voucher_txn_id,
			'tpt_id'	 => $data['transporter_id'],
			'tpt_gstin'	 => $data['gstin_id'],
		];
        $this->db->table("ewbpartbdt")->insert($mst_data);   
	   
   }
 public function get_ewbmstreqn_data(int $vch_txn_id,$compId=null): array
{
	if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
    // Fetch EWB master by voucher txn id
    $ewb = $this->db->table('ewbmastern')
        ->where('vch_txn_id', $vch_txn_id)
		->where('cmp_id',$sel_compId)
        ->limit(1)
        ->get()
        ->getRowArray();

    if (!$ewb) {
        return [];
    }

    // Fetch dispatch-from info (gstdispfrm) for this EWB
    $ewb['gstdispfrm_info'] = $this->db->table('gstdispfrm')
        ->where('ewb_id', $ewb['ewb_id'])
        ->orderBy('dispfrm_id', 'DESC')
        ->limit(1)
        ->get()
        ->getRowArray() ?: [];

    // Fetch ship-to info (gstshipton) for this EWB
    $ewb['gstshipton_info'] = $this->db->table('gstshipton')
        ->where('ewb_id', $ewb['ewb_id'])
        ->orderBy('shipto_id', 'DESC')
        ->limit(1)
        ->get()
        ->getRowArray() ?: [];

    return $ewb;
}
   
   public function mc_info(int $centre_id): array
{
    $row = $this->db->table('matcentmst m')
        ->select('
            m.mat_cent_id,
            m.mat_cent_name,
            m.mat_cent_alias,
            m.mat_cent_print_name,
            m.mat_cent_is_active,
            d.mat_cent_addr1,
            d.mat_cent_addr2,
            d.mat_cent_city,
            d.mat_cent_state,
            d.mat_cent_pin_zip,
            d.mat_cent_country
        ')
        ->join('matcentdet d', 'd.mat_cent_id = m.mat_cent_id AND d.cmp_id = m.cmp_id', 'left')
        ->where('m.mat_cent_id', $centre_id)
        ->where('m.cmp_id', $this->company_id)
        ->limit(1)
        ->get()
        ->getRowArray();

    if (!$row) {
        return [];
    }

    // Compute state_code (2-digit) if country/state present
    $row['state_code'] = '';
    if (!empty($row['mat_cent_country']) && !empty($row['mat_cent_state'])) {
        $state = $this->CommonModel->get_state_info($row['mat_cent_country'], $row['mat_cent_state']);
        $row['state_code'] = sprintf('%02d', $state['state_code'] ?? 0);
    }

    return $row;
}
   
   public function get_gst_taxinfo($tax_id,$voucher_date){
        $cmpId = $this->company_id;
        $tax_cat_type = 1;
        $wef_date     = date("Y-m-d",strtotime($voucher_date));
        // Get the Query Builder instance for the 'taxcatrate' table
        $builder = $this->db->table('taxcatrate tcr');
    
        // Select the rate and sub-type
        $builder->select('tcr.tax_cat_rate, tcr.tax_cat_sub_type');
    
        // Join with the 'taxcatmstn' table on the common ID
        $builder->join('taxcatmstn tcm', 'tcm.tax_cat_mst_id = tcr.tax_cat_mst_id');
    
        // Add a WHERE clause to filter by the tax category type
        $builder->where('tcm.tax_cat_type', 1);
         $builder->where('tcm.tax_cat_mst_id',$tax_id);
    
        // Filter to find rates with a 'wef' date less than or equal to the provided date
        $builder->where('tcr.tax_cat_wef <=', $wef_date);
    
        // Fetch all records that match the most recent 'wef' date
        // This subquery is necessary to get all tax_cat_sub_types for the latest date.
        $builder->whereIn('tcr.tax_cat_wef', function($subquery) use ($wef_date) {
            $subquery->selectMax('tax_cat_wef');
            $subquery->from('taxcatrate');
            $subquery->where('tax_cat_wef <=', $wef_date);
        });
    
        // Execute the query
        $tax_rates = $builder->get()->getResultArray();
    
        // Process the results into a single associative array
        $final_rates = [];
    
        foreach ($tax_rates as $rate) {
            switch ($rate['tax_cat_sub_type']) {
                case '1':
                    $final_rates['igst'] = $rate['tax_cat_rate'];
                    $final_rates['igst_tax_name'] = 'IGST';
                    break;
                case '2':
                    $final_rates['sgst'] = $rate['tax_cat_rate'];
                    $final_rates['sgst_tax_name'] = 'SGST';
                    break;
                case '3':
                    $final_rates['cgst'] = $rate['tax_cat_rate'];
                     $final_rates['cgst_tax_name'] = 'CGST';
                    break;
                case '4':
                    $final_rates['ut_tax'] = $rate['tax_cat_rate'];
                    $final_rates['ut_tax_name'] = 'UT Tax';
                    break;
                case '5':
                    $final_rates['cess'] = $rate['tax_cat_rate'];
                    $final_rates['cess_tax_name'] = 'Cess';
                    break;
            }
        }
        
        return $final_rates;
           
    }

    public function GetGSTTaxesList(){
        $cmpId = $this->company_id;
        $tax_cat_type =1;
        
       // Get the Query Builder instance for the 'taxcatrate' table
        $builder = $this->db->table('taxcatrate tcr');
    
        // Select the rate and sub-type
        $builder->select('tcm.tax_cat_mst_id,tcr.tax_cat_rate, tcr.tax_cat_sub_type,tcr.tax_cat_wef');
    
        // Join with the 'taxcatmstn' table on the common ID
        $builder->join('taxcatmstn tcm', 'tcm.tax_cat_mst_id = tcr.tax_cat_mst_id');
    
        // Add a WHERE clause to filter by the tax category type
        $builder->where('tcm.tax_cat_type', 1);
		 $builder->where('tcm.cmp_id', $cmpId);
		 $builder->where('tcr.cmp_id', $cmpId);
        
        // Filter to find rates with a 'wef' date less than or equal to the provided date
    
        // Execute the query
        $tax_rates = $builder->get()->getResultArray();
    
        // Process the results into a single associative array
        $final_rates = [];
        
        foreach ($tax_rates as $rate) {
            $tax_wef = date('d-m-Y', strtotime($rate['tax_cat_wef']));
            $tax_id  = $rate['tax_cat_mst_id'];
            switch ($rate['tax_cat_sub_type']) {
               
                case '1':
                    $final_rates[$tax_id][$tax_wef]['igst'] = $rate['tax_cat_rate'];
                    break;
                case '2':
                    $final_rates[$tax_id][$tax_wef]['sgst'] = $rate['tax_cat_rate'];
                    break;
                case '3':
                    $final_rates[$tax_id][$tax_wef]['cgst'] = $rate['tax_cat_rate'];
                    break;
                case '4':
                    $final_rates[$tax_id][$tax_wef]['ut_tax'] = $rate['tax_cat_rate'];
                    break;
                case '5':
                    $final_rates[$tax_id][$tax_wef]['cess'] = $rate['tax_cat_rate'];
                   break;
            }
        }
        
        return $final_rates;
    }
	
	 public function Voucher_TxnApproval($compId=null,$uuid=null,$profile_id=null){
		 if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;

		if($uuid)
			$sel_uuid = $compId;
		 else 
			$sel_uuid = $this->session->get('uuid');
		
		if($profile_id)
			$sel_profile_id = $profile_id;
		 else 
			$sel_profile_id = $this->profile_id;
				
		$builder = $this->univerpaic_db->table("erptxnaprv");
		$builder->join("erpacsprof", "erpacsprof.erp_acs_prof_id = erptxnaprv.erp_acs_prof_id");
		$builder->select("erptxnaprv.txn_aprv_min_limit,erptxnaprv.txn_aprv_uuid");
		$builder->where("erptxnaprv.erp_acs_prof_id", $sel_profile_id);
		$builder->where("erptxnaprv.txn_aprv_min_limit IS NOT NULL");
		$builder->where("erpacsprof.cmp_id", $sel_compId);
		$response = $builder->get()->getRowArray();
		$approval_amt  = null; 
		$txn_aprv_uuid = 0;
		if ($response) {
			$approval_amt = $response['txn_aprv_min_limit'];
			$txn_aprv_uuid = $response['txn_aprv_uuid'];
		}
		return array("approval_amt"=>$approval_amt,"uuid_to"=>$txn_aprv_uuid);
	 }
	 
	 public function UpdateNotificationStatus($vch_txn_id){
	     $this->db->table("erpnotifyn")
			        ->where('cmp_id', $this->company_id)
			        ->where('vch_txn_id', $vch_txn_id)
			        ->where('uuid_to', $this->session->get('uuid'))
			        ->update(['notify_read' => 0]);
	     
	     }
  
	 public function checkDateEntryAllowed($series_id, $dateposted){
		$builder = $this->univerpaic_db->table("erpbdeacsn");
		$builder->join("erpacsprof", "erpacsprof.erp_acs_prof_id = erpbdeacsn.erp_acs_prof_id");
		$builder->select("erpbdeacsn.erp_bde");
		$builder->where("erpbdeacsn.vch_series_id", $series_id);
		$builder->where("erpbdeacsn.erp_acs_prof_id", $this->profile_id);
		$builder->where("erpacsprof.cmp_id", $this->company_id);
		$response = $builder->get()->getRowArray();
		//echo $this->univerpaic_db->getlastquery();
		//die();
		$allowed = true;
		$allowedBackDate = null;
		if ($response) {
			// Calculate the allowed back date
			$allowedBackDate = date('Y-m-d', strtotime('-' . (int)$response['erp_bde'] . ' days'));
            $dateposted      = date("Y-m-d",strtotime($dateposted)); 
			// Compare dates (both as YYYY-MM-DD)
			if ($dateposted < $allowedBackDate) {
				$allowed = false; // date is older than allowed
			}
		}
		$final_response = json_encode([
			"allowed" => $allowed,
			"allowed_back_date" => $allowedBackDate
		    ]);
		return $final_response;
	} 
  
  public  function add_bill_master($data,$compId=null,$boId=null,$fyId=null){
        if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
		if($boId)
			$sel_boId = $boId;
		 else 
			$sel_boId = $this->bo_id;
		if($fyId)
			$sel_fyId = $fyId;
		 else 
			$sel_fyId = $this->fy_id;	
		$bill_mst = $this->db->table("billmaster")
						->where('acc_id', $data['acc_id'])
						->where('cmp_id', $sel_compId)
						->where('bill_ref_name', $data['bill_ref_name'])
						->get()->getRowArray();
		if($bill_mst){
			return $bill_mst['bill_ref_id'];
		}
		$mst_data = [
		    'cmp_id'		 => $data['cmp_id'],
			'bill_ref_name'  => $data['bill_ref_name'],
			'acc_id'		 => $data['acc_id'],
			'bill_due_date'	 => validate_date_by_fy($data['bill_due_date']),
			'bill_status'	 => $data['bill_status'],
		];
        $this->db->table("billmaster")->insert($mst_data);
        $bills_ref_id = $this->db->insertID();
        // Add Bill By Bill Opening Balances
		$op_data = [
		    'cmp_id'        => $sel_compId,
			'cmpfymastr_id' => $sel_fyId,
      		'bill_ref_id' 	=> $bills_ref_id,      		
      		'bill_op_bal' 	=> $data['bill_op_bal'] ?? 0,
      		'bill_py_bal' 	=> 0,
			'hobo_id' 		=> $sel_boId,
      	];
      	$this->db->table("billoppybal")->insert($op_data);		
	    return $bills_ref_id;
    }
	
   public function against_voucher_dropdown($voucher_type_id, $voucher_subtype_array, $date)
	{
		if ($voucher_type_id == 18) {
			$joinTable     = "gstroutsup g";
			$billRefColumn = "g.outsup_bill_ref_no";
			$idColumn      = "g.outsup_id";
		} elseif ($voucher_type_id == 11) {
			$joinTable     = "gstrinwsup g";
			$billRefColumn = "g.inwsup_bill_ref_no";
			$idColumn      = "g.inwsup_id";
		} else {
			return [];
		}

		//LEFT JOIN with vchbridgen to detect Composition / Regular
		$builder = $this->db->table("vchtxnconso vc")
			->select("
				vc.vch_txn_id,
				vc.vch_date,
				{$idColumn} as bill_id,
				{$billRefColumn} as bill_ref_no,
				vb.vch_txn_id_src as comp_txn_id
			")
			->join($joinTable, "g.vch_txn_id = vc.vch_txn_id", "inner")
			->join(
				"vchbridgen vb",
				"vb.vch_txn_id_src = vc.vch_txn_id AND vb.vch_bridge_type = 4",
				"left"
			)
			->where("vc.vch_type_id", $voucher_type_id)
			->whereIn("vc.vch_sub_type_id", $voucher_subtype_array)
			->where("vc.vch_date <=", $date)
			->where("vc.cmp_id", $this->company_id)
			->where("vc.hobo_id", $this->bo_id);

		$result = $builder->get()->getResultArray();

		$final_list = [];

		if ($result) {
			foreach ($result as $row) {

				if (!empty($row['bill_ref_no'])) {

					$voucher_date = date('d/m/Y', strtotime($row['vch_date']));

					// If record exists in bridge then Composition, else Regular
					$tag = (!empty($row['comp_txn_id'])) ? 'C' : 'R';

					$final_list[$row['bill_id'].'||'.$row['vch_txn_id'].'||'.$tag] =
						"BILL REF NO.: " . $row['bill_ref_no'] .
						" DATED " . $voucher_date;
				}
			}
		}

		return $final_list;
	}

public function show_bill_ref_no($voucher_type_id, $series_id, $voucher_txn_id, $date)
{
	$date = date("Y-m-d",strtotime($date));
    // Allow only expected voucher types
    if (!in_array($voucher_type_id, [18, 3, 11, 2], true)) {
        return [];
    }

    if ($voucher_type_id === 18 || $voucher_type_id === 3) {
        $joinTable    = 'gstroutsup g';
        $billRefCol   = 'g.outsup_bill_ref_no';
        $idCol        = 'g.outsup_id';
    } else { // 11 or 2
        $joinTable    = 'gstrinwsup g';
        $billRefCol   = 'g.inwsup_bill_ref_no';
        $idCol        = 'g.inwsup_id';
    }

    $builder = $this->db->table('vchtxnconso vc')
        ->select([
            'vc.vch_txn_id',
            'vc.vch_date::date AS vch_date',   // Postgres cast to date
            "{$idCol} AS bill_id",
            "{$billRefCol} AS bill_ref_no",
        ], false) // allow raw identifiers/aliases
        ->join($joinTable, 'g.vch_txn_id = vc.vch_txn_id', 'inner')
        ->where('vc.vch_type_id', $voucher_type_id)
        ->where('vc.vch_txn_id', $voucher_txn_id)
        ->where('vc.vch_series_id', $series_id)
        ->where('vc.vch_date <=', $date) // Postgres accepts string dates
        ->where('vc.cmp_id', $this->company_id)
        ->where('vc.hobo_id', $this->bo_id);

    $rows = $builder->get()->getResultArray();

    $final = '';
    foreach ($rows as $row) {
        if (!empty($row['bill_ref_no'])) {
            $final = $row['bill_ref_no'];
        }
    }

    return $final;
}
	
 public function GetAccountTaxOutID($bsd_type,$bsd_input_output,$tax_cat_type,$tax_cat_sub_type,$compId=null, $boId=null){
     if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
		if($boId)
			$sel_boId = $boId;
		 else 
			$sel_boId = $this->bo_id;
	 $builder = $this->db->table("acctmaster");
     $builder->select("acctmaster.acc_id");
     $builder->join("billsundry","billsundry.bsd_id=acctmaster.bsd_id");
	 $builder->where('billsundry.bsd_type', $bsd_type);
	 $builder->where('billsundry.bsd_input_output', $bsd_input_output);
	 $builder->where('billsundry.tax_cat_type', $tax_cat_type);
	 $builder->where('billsundry.tax_cat_sub_type', $tax_cat_sub_type); 
	 $builder->where('acctmaster.cmp_id', $sel_compId);
	 $response = $builder->get()->getRowArray(); 
	 
	 SaveErrorLog("Getting Tax Account Entry->". $this->db->getlastquery());
	 if($response)
	 return $response['acc_id'];
	 else
	 return 0;
     
 }   
 
 public function account_detail_info($account_id,$cmpid=null,$fyid=null){	
     if($cmpid)
        $sel_comp_id = $cmpid;
	else
		 $sel_comp_id = $this->company_id;
	 
	 
	 if($fyid)
        $sel_fy_id = $fyid;
	else
		 $sel_fy_id = $this->fy_id;
        $acctmaster_tbl  = "acctmaster";
		$acctmstdet_tbl  = "acctmstdet";
		$undercrsmt_tbl  = "undercrsmt";
		$builder = $this->db->table($acctmaster_tbl);
		$builder->join($acctmstdet_tbl, "$acctmstdet_tbl.acc_id = $acctmaster_tbl.acc_id", 'left');
		$builder->join($undercrsmt_tbl, "$undercrsmt_tbl.crs_mst_id = $acctmaster_tbl.acc_id AND $undercrsmt_tbl.crs_mst_type =1", 'left');
		$builder->where("$acctmaster_tbl.acc_id", $account_id);	
		$builder->where("$acctmaster_tbl.cmp_id", $sel_comp_id);		
		$builder->where("$undercrsmt_tbl.cmpfymastr_id = $sel_fy_id
			AND 
			$undercrsmt_tbl.cmp_id = $sel_comp_id"
			);		
		$response = $builder->get()->getRowArray();  	   
	   return $response;
   }

  /**
   * Posts one GST component (IGST / CGST / SGST / UTGST / cess) to the company's tax ledger for that
   * component: a positive $tax_value is a debit, a negative one a credit.
   *
   * @return float the amount actually posted, or 0.00 when the company has no ledger for the component
   *               (the row is then NOT written, which the caller has to account for).
   */
  public function save_taxacc_yes_out_data($voucher_txn_id,$voucher_series,$tax_value,$tax_cat_sub_type,$voucher_date,$bsd_input_output=2,$compId=null, $boId=null){
      $bsd_type     =1;
	  if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
		if($boId)
			$sel_boId = $boId;
		 else 
			$sel_boId = $this->bo_id;
      
      $tax_cat_type=1;// gst tax payer
      $billsundry_id = $this->GetAccountTaxOutID($bsd_type,$bsd_input_output,$tax_cat_type,$tax_cat_sub_type,$sel_compId,$sel_boId);
      SaveErrorLog("Company ID: ".$sel_compId.", Voucher Txn ID (Tax Applied): ".$voucher_txn_id.", Bill Sundry ID: ".$billsundry_id);
	  if($billsundry_id>0){
	  $insert_data = [
							  "cmp_id"          => $sel_compId,
							  "vch_series_id"   => $voucher_series,
							  "vch_txn_id"      => $voucher_txn_id,
							  "master_id"       => $billsundry_id,
							  'master_id_type'  => 'tax'
							 ];
	  $txn_id = $this->add_comp_txn_data($insert_data);
	  
	  $acc_txn_data  = [
							  'cmp_id'            => $sel_compId,
							  'acc_id'            => $billsundry_id,
							  'acc_txn_date'      => $voucher_date,
							  'acc_txn_dr_cr'     => ($tax_value<0) ? 2 : 1,
							  'acc_txn_amt'       => parseAmount(abs($tax_value)),
							  'acc_txn_fcy'       => 0,
							  'vch_txn_id'        => $voucher_txn_id,
							  'txn_id'            => $txn_id,
							  'hobo_id'           => $sel_boId,
							  'acc_txn_type'      => 1
							];
	  $this->add_acc_txn_data($acc_txn_data);
	  SaveErrorLog("Saving tax account entry: " . json_encode($acc_txn_data));
	  \App\Libraries\CompositionPosting::touch($voucher_txn_id);
	  return parseAmount(abs($tax_value));
      }
      // The tax ledger for this component is not set up for the company, so NO row was written. The
      // caller's own total must not assume one was: a leg that silently disappears is what leaves a
      // voucher one-sided. Returning 0.00 lets the caller (and CompositionPosting) see it.
      SaveErrorLog("No tax account is mapped for bsd_type 1 / input-output $bsd_input_output / category $tax_cat_sub_type"
                   . " of company $sel_compId: no ledger row was written for " . parseAmount(abs($tax_value)));
      return 0.0;
  }
  
  public  function add_sublgr_master($data){
        $bill_mst = $this->db->table("subacctmst")
						->where('acc_id', $data['acc_id'])
						->where('cmp_id', $this->company_id)
						->where('LOWER(sub_acc_name)', strtolower(trim($data['sub_acc_name'])))
						->get()->getRowArray();
		if($bill_mst){
			return $bill_mst['sub_acc_id'];
		}
		$mst_data = [
		    'cmp_id'		    => $data['cmp_id'],
			'sub_acc_name'      => $data['sub_acc_name'],
			'acc_id'		    => $data['acc_id'],
			'sub_due_date'	    => validate_date_by_fy($data['sub_due_date']),
			'sub_acc_is_active'	=> $data['sub_acc_is_active'],
		];
        $this->db->table("subacctmst")->insert($mst_data);
        $sub_acc_id = $this->db->insertID();
        // Add Bill By Bill Opening Balances
		$op_data = [
		    'cmp_id'         => $this->company_id,
			'cmpfymastr_id'  => $this->fy_id,
      		'sub_acc_id' 	 => $sub_acc_id,      		
      		'sub_acc_op_bal' => $data['sub_acc_op_bal'] ?? 0,
      		'sub_acc_py_bal' => 0,
			'hobo_id' 		 => $this->bo_id,
      	];
      	$this->db->table("suboppybal")->insert($op_data);		
	    return $sub_acc_id;
    }
	
  public  function getUndefinedBillRefId($account_id,$compId=null,$boId=null,$fyId=null){
     	if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
		if($boId)
			$sel_boId = $boId;
		 else 
			$sel_boId = $this->bo_id;
		if($fyId)
			$sel_fyId = $fyId;
		 else 
			$sel_fyId = $this->fy_id;	
			$data = $this->db->table("billmaster")
							->select('bill_ref_id')
							->where('acc_id', $account_id)
							->where('cmp_id', $sel_compId)
							->where('bill_ref_name', 'UNDEFINED')
							->get()->getRowArray();

      if($data)
	  		return $data['bill_ref_id'];
        else
			return $this->createUndefinedBillRefId($account_id,$sel_compId,$sel_boId,$sel_fyId);	  
    }

  public  function createUndefinedBillRefId($account_id,$compId=null,$boId=null,$fyId=null){
    	if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
		if($boId)
			$sel_boId = $boId;
		 else 
			$sel_boId = $this->bo_id;
		if($fyId)
			$sel_fyId = $fyId;
		$data = [
                 'cmp_id'         => $sel_compId,
				 'bill_ref_name'  => 'UNDEFINED',
                 'acc_id'         => $account_id,
                 'bill_status'    => 3,
                 'bill_due_date'  => company()->fy_from_date
                ];
        $this->db->table("billmaster")->insert($data);
        $bills_ref_id = $this->db->insertID();
		$data = [   'cmp_id'        => $sel_compId,
		            'cmpfymastr_id' => $sel_fyId,
					'bill_ref_id'	=> $bills_ref_id,					
					'bill_op_bal'	=> 0,
					'bill_py_bal'	=> 0,
					'hobo_id'		=> $sel_boId,
				];
		$exists = $this->db->table("billoppybal")
							->select('bill_ref_id')
							->where('bill_ref_id', $bills_ref_id)
							->where('cmp_id', $sel_compId)
							->where('hobo_id', $sel_boId)	
							->countAllResults();
		if($exists==0)							
		$this->db->table("billoppybal")->insert($data);

	    return $bills_ref_id;
    }
	
  public  function update_bill_master($id,$data,$compId=null,$boId=null,$fyId=null){
        if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
		if($boId)
			$sel_boId = $boId;
		 else 
			$sel_boId = $this->bo_id;
		if($fyId)
			$sel_fyId = $fyId;
		
		$this->db->table("billmaster")
			        ->where('bill_ref_id', $id)
			        ->update(['bill_due_date' => $data['bill_due_date']]);
		if(isset($data['bills_op_bal'])){
			$balance = $data['bills_op_bal'] ?? 0;			
			$exists = $this->db->table("billoppybal")
								->where('bill_ref_id',$id)->where('cmp_id',$sel_compId)
								->where('cmpfymastr_id',$sel_fyId)
								->where('hobo_id',$sel_boId)
								->get()->getRowArray();
			if($exists){
				$this->db->table("billoppybal")
					->where('bill_ref_id',$id)
					->where('cmp_id',$sel_compId)
					->where('cmpfymastr_id',$sel_fyId)
					->where('hobo_id',$sel_boId)
					->update(['bill_op_bal' => floatval($balance)]);
			}
			else{
				$op_data = [
					'cmp_id'        => $this->company_id,
					'cmpfymastr_id' => $sel_fyId,
					'bill_ref_id'	=> $id,				
					'bill_op_bal'	=> floatval($balance),
					'bill_py_bal'	=> 0,
					'hobo_id'	    => $sel_boId,
				];
				$this->db->table("billoppybal")->insert($op_data);
			   }
		  }
		
		}
		
  public  function update_sublgr_master($id,$data){
        $this->db->table("subacctmst")
			        ->where('sub_acc_id', $id)
			        ->update(['sub_due_date' => $data['sub_due_date']]);
		if(isset($data['sub_acc_op_bal'])){
			$balance = $data['sub_acc_op_bal'] ?? 0;			
			$exists  = $this->db->table("suboppybal")
								->where('sub_acc_id',$id)->where('cmp_id',$this->company_id)
								->where('cmpfymastr_id',$this->fy_id)
								->where('hobo_id',$this->bo_id)
								->get()->getRowArray();
			if($exists){
				$this->db->table("suboppybal")
					->where('sub_acc_id',$id)
					->where('cmp_id',$this->company_id)
					->where('cmpfymastr_id',$this->fy_id)
					->where('hobo_id',$this->bo_id)
					->update(['sub_acc_op_bal' => floatval($balance)]);
			}
			else{
				$op_data = [
					'cmp_id'         => $this->company_id,
					'cmpfymastr_id'  => $this->fy_id,
					'sub_acc_id'	 => $id,				
					'sub_acc_op_bal' => floatval($balance),
					'sub_acc_py_bal' => 0,
					'hobo_id'	     => $this->bo_id,
				  ];
				 $this->db->table("suboppybal")->insert($op_data);
			   }
		   }		
		}
   
  public  function SaveCostCentreData($ccdata,$voucher_date,$voucher_txn_id,$txn_id){
	   if($ccdata){
				foreach($ccdata as $key => $value){            
					if(isset($value['cc_id']) && $value['cc_id'] >0){
						$amount   = $value["cc_txn_amt"];
						$amountfc = 0;
						if(isset($value["cc_txn_amtfc"]))
						$amountfc = $value["cc_txn_amtfc"];
					    
						
						$cc_txn_data = [
						  'cmp_id'           => $this->company_id,
						  'cc_id'            => $value['cc_id'],
						  'acc_id'           => $value['account_id'],
						  'cc_txn_date'      => $voucher_date,
						  'cc_txn_dr_cr'     => ($value['cc_txn_drcr']=='C')? 2:1,
						  'cc_txn_amt'       => $amount,
						  'cc_txn_fcy'       => $amountfc,
						  'cc_txn_narr'      => $value['cc_txn_narr'],
						  'vch_txn_id'       => $voucher_txn_id,
						  'txn_id'           => $txn_id,
						  'hobo_id'          => $this->bo_id
						];
						$this->add_cc_txn($cc_txn_data);
					}
				}
        }
   }
   
   
  public  function SaveSubLedgerData($sblgrdata,$voucher_date,$voucher_txn_id,$txn_id){
	  if($sblgrdata){
			 foreach($sblgrdata as $key => $value){
					$sub_acc_id   = 0;
					$bill_due_date = $value['due_date'] != '' ? validate_date_by_fy($value['due_date']) : $voucher_date;
					  if(strtolower(trim($value['method'])) == 'new ref.'){
						$sblgr_master_data = [
						  'cmp_id'            => $this->company_id,
						  'sub_acc_name'      => $value['reference'],
						  'acc_id'            => $value['account_id'],
						  'sub_due_date'      => $bill_due_date,
						  'sub_acc_is_active' => 1
						 ];
						 $sub_acc_id = $this->add_sublgr_master($sblgr_master_data);
					  }
					  if(strtolower(trim($value['method'])) == 'adjustment'){
  						  if(isset($value['reference_id']) && $value['reference_id']!=''){
						  $sub_acc_id       = $value['reference_id'];
						  $bill_master_data = ['sub_due_date'  => $bill_due_date,'sub_acc_is_active'=>1];
						  $this->update_sublgr_master($sub_acc_id, $bill_master_data);
						  }						
					  }					  
					  if($sub_acc_id){
						$sublgr_txn_data = [
						  'cmp_id'            => $this->company_id,
						  'sub_acc_id'        => $sub_acc_id,
						  'acc_id'            => $value['account_id'],
						  'sub_acc_txn_date'  => $voucher_date,
						  'sub_acc_txn_dr_cr' => ($value['drcr']=='D') ? 1:2,
						  'sub_acc_txn_amt'   => $value['amount'],
						  'sub_acc_txn_fcy'   => $value['amountfc'],
						  'sub_acc_txn_narr'  => $value['narration'],
						  'vch_txn_id'        => $voucher_txn_id,
						  'txn_id'            => $txn_id,
						  'hobo_id'           => $this->bo_id
						  ];
						$this->add_sublgr_txn($sublgr_txn_data);						
					  }
				}
			}
   }
  public function get_account_info($account_id){
   $data       = $this->db->table("acctmaster")
		       ->select('acc_id,acc_name')
		       ->where('acc_is_active',1)
		       ->get()->getRowArray();
        return $data;  
	  
  } 
 
  public function get_cc(){
	   $data       = $this->db->table("ccmasternn")
		       ->select('cc_id as id, cc_name as label, cc_name as value')
		       ->where('cc_name !=', 'UNDEFINED')
			   ->where('cmp_id', $this->company_id)
			   ->where('cc_is_active',1)
		       ->get()->getResultArray();
        return $data;  
   }
   
   public function get_purchase_cc($itmsdata){	   
	 $cc_accounts = [];
    foreach($itmsdata as $key => $value) {
      $account_id  =  $value['item_pur_acc'];
      $amount      =  $value['item_total_amount'];
      $index = array_search($account_id, array_column($cc_accounts, 'account_id'));
      if($index != ''){
        $cc_accounts[$index]['amount'] += $amount;
      }
      else{
        $account = $this->get_account_info($account_id);
        $account_name = $account['acc_name'];
        $cc_accounts[] = [
          "account_id"    => $account_id,
          "acc_type"      => 'acc',
          "account_name"  => $account_name,
          "drcr"          => 'D',
          "amount"        => $amount,
        ];
      }                
    }
    $data['cc_accounts'] = $cc_accounts;
    $data['cc_list'] = $this->get_cc();
    return $data;  
   }
   
   public function account_full_info($account_id,$cmpid=null,$fyid=null,$uuid=null)
{
	 if($cmpid)
        $sel_comp_id = $cmpid;
	else
		 $sel_comp_id = $this->company_id;
	 
	 
	 if($uuid)
        $sel_uuid = $uuid;
	else
		 $sel_uuid = $this->session->get('uuid');
	 
	 if($fyid)
        $sel_fy_id = $fyid;
	else
		 $sel_fy_id = $this->fy_id;
	 
    $acctmaster_tbl  = "acctmaster";
    $acctmstdet_tbl  = "acctmstdet";
    $undercrsmt_tbl  = "undercrsmt";
    $uuid            = $sel_uuid;

    // -------- Account core data (from local DB) --------
    $builder = $this->db->table($acctmaster_tbl);
    $builder->join($acctmstdet_tbl, "$acctmstdet_tbl.acc_id = $acctmaster_tbl.acc_id", 'left');
    $builder->join(
        $undercrsmt_tbl,
        "$undercrsmt_tbl.crs_mst_id = $acctmaster_tbl.acc_id
         AND $undercrsmt_tbl.crs_mst_type = 1
         AND $undercrsmt_tbl.cmpfymastr_id = $sel_fy_id
         AND $undercrsmt_tbl.cmp_id = $sel_comp_id",
        'left'
    );

    $builder->where("$undercrsmt_tbl.cmpfymastr_id", $sel_fy_id);
    $builder->where("$undercrsmt_tbl.cmpfymastr_id IS NOT NULL");
    $builder->where("$acctmaster_tbl.acc_id", $account_id);
    $builder->where("$acctmaster_tbl.cmp_id", $sel_comp_id);

    $response = $builder->get()->getRowArray();
    if (!$response) {
        return [];
    }

    $response['address_info'] = [];

    // -------- QUERY 1: Get Address/Contact from 'contactaic' DB --------
   $builderq = $this->db->table("acctmstadr");
	 $builderq->where("acc_id", $account_id);
     $builderq->where("cmp_id", $sel_comp_id);
	 $adrs_response = $builderq->get()->getRowArray();
	 if( $adrs_response){
		$response['address_info']=["contact_country"=>$adrs_response['acc_country'] ,
		                           "contact_state"=>$adrs_response['acc_state'],
								   "contact_city"=>$adrs_response['acc_city'],
								   "contact_add1"=>$adrs_response['acc_addr1'],
								   "contact_add2"=>$adrs_response['acc_addr2'],
								   "contact_pin"=>$adrs_response['acc_pin'],
								   "contact_email"=>$adrs_response['acc_email'],
								   "contact_mobile"=>$adrs_response['acc_mobile'],
								   "contact_wamobile"=>$adrs_response['acc_mobile']
								   ]; 
	 }else{
		$response['address_info']=["contact_country"=>1 ,
		                           "contact_state"=>0,
								   "contact_city"=>"",
								   "contact_add1"=>"",
								   "contact_add2"=>"",
								   "contact_pin"=>"",
								   "contact_email"=>"",
								   "contact_mobile"=>"",
								   "contact_wamobile"=>""
								   ];  
	 }

    return $response;
}

   function add_ewb_data($data){
	   $this->db->table("ewbmastern")->insert($data);
	   return $this->db->insertID();
   }
   function add_einvoice_data($data){
	   $this->db->table("einvmaster")->insert($data);
	   return $this->db->insertID();
   }
   function update_eway_data($voucher_txn_id,$data){
	   $eway_date      = $data["ewayBillDate"]; 
	   $eway_valid_dt  = $data["validUpto"]; 
	   
	    $EWAYdate = \DateTime::createFromFormat('d/m/Y h:i:s A', $eway_date);
		$formattedEwayBillDate = $EWAYdate->format('Y-m-d H:i:s');
	   
	   
	   $validDate = \DateTime::createFromFormat('d/m/Y h:i:s A', $eway_valid_dt);
	   $formattedValidUpto = $validDate->format('Y-m-d H:i:s');
	   
	   $ewayBillNo     = $data['ewayBillNo'];
	   
	   $rep_data       = array("ewb_no"=>$ewayBillNo,
		                       "ewb_date"=>$formattedEwayBillDate,"ewb_valid_dt"=>$formattedValidUpto
							   );
	   $this->db->table("ewbmastern")->where('cmp_id', $this->company_id)
				->where('vch_txn_id', $voucher_txn_id)->update($rep_data);
				
   }
   
   function update_einvoice_data($voucher_txn_id,$data){
	   $einv_date      = date('Y-m-d H:i:s',strtotime($data["AckDt"])); 
	   $einv_valid_dt  = date('Y-m-d H:i:s',strtotime($data["AckDt"])); 
	   $einv_status  ='';
	   if($data["Status"]=='ACT')
		 $einv_status =1;//ACTIVE
	  else if($data["Status"]=='CNL')
		 $einv_status =2;//CANCELLED
	   else
		 $einv_status =0;  
	   $rep_data       = array("einv_no"=>$data["AckNo"],
		                       "einv_date"=>$einv_date,"einv_valid_dt"=>$einv_valid_dt,
							   "einv_status"=>$einv_status,"einv_irn"=>$data["Irn"],
							   "env_signedinvoice"=>$data["SignedInvoice"],
							   "einv_qr_code"=>$data["SignedQRCode"]
							   );
	   
	   $this->db->table("einvmaster")->where('cmp_id', $this->company_id)
				->where('vch_txn_id', $voucher_txn_id)->update($rep_data);
     }
   
   public function add_ewb_txntype_data(int $taxtype_id, int $ewb_id, int $einv_id,int $voucher_txn_id, array $data,$compId=null): void
	{
		 if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;


		// Table names are fixed in PostgreSQL
		$shipTable = 'gstshipton';
		$dispTable = 'gstdispfrm';

		// BILL TO – SHIP TO (taxtype_id = 2)
		if ($taxtype_id === 2) {
			$exists = $this->db->table($shipTable)
				->where('ewb_id', $ewb_id)
				->where('cmp_id', $sel_compId)
				->where('vch_txn_id', $voucher_txn_id)
				->countAllResults();

			$payload = [
				'ewb_id'            => $ewb_id,
				'einv_id'           => $einv_id,
				'shipto_addr1'      => $data['shipto2_addr1'] ?? '',
				'shipto_addr2'      => $data['shipto2_addr2'] ?? '',
				'shipto_place'      => $data['shipto2_place'] ?? '',
				'shipto_pin'        => $data['shipto2_pin'] ?? '',
				'shipto_state_code' => sprintf( '%02d',$data['shipto2_state_code']) ?? null,
				'shipto_gstin'      => $data['shipto2_gstin'] ?? '',
				'shipto_legal_name' => $data['shipto2_legalname'] ?? '',
				'shipto_trade_name' => $data['shipto2_tradename'] ?? '',
				'cmp_id'            => $this->company_id,
				'vch_txn_id'        => $voucher_txn_id
			];

			if ($exists == 0) {
				$this->db->table($shipTable)->insert($payload);
			} else {
				unset($payload['ewb_id'], $payload['einv_id']);
				$this->db->table($shipTable)->where('vch_txn_id', $voucher_txn_id)->where('cmp_id',$sel_compId)->where('ewb_id', $ewb_id)->update($payload);
			}
		}

		// BILL FROM – DISPATCH FROM (taxtype_id = 3)
		if ($taxtype_id === 3) {
			$exists = $this->db->table($dispTable)
				->where('ewb_id', $ewb_id)
				->where('cmp_id', $sel_compId)
				->where('vch_txn_id', $voucher_txn_id)
				->countAllResults();

			$payload = [
				'ewb_id'             => $ewb_id,
				'einv_id'            => $einv_id,
				'dispfrm_addr1'      => $data['dispfrm3_addr1'] ?? '',
				'dispfrm_addr2'      => $data['dispfrm3_addr2'] ?? '',
				'dispfrm_place'      => $data['dispfrm3_place'] ?? '',
				'dispfrm_pin'        => $data['dispfrm3_pin'] ?? '',
				'dispfrm_state_code' => sprintf( '%02d',$data['dispfrm3_state_code']) ?? null,
				'cmp_id'             => $sel_compId,
				'vch_txn_id'         => $voucher_txn_id
			];

			if ($exists == 0) {
				$this->db->table($dispTable)->insert($payload);
			} else {
				unset($payload['ewb_id'], $payload['einv_id']);
				$this->db->table($dispTable)->where('vch_txn_id', $voucher_txn_id)->where('cmp_id', $sel_compId)->where('ewb_id', $ewb_id)->update($payload);
			}
		}

		// BILL TO – SHIP TO – BILL FROM – DISPATCH FROM (taxtype_id = 4)
		if ($taxtype_id === 4) {
			// shipto
			$shipExists = $this->db->table($shipTable)
				->where('ewb_id', $ewb_id)
				->where('cmp_id', $sel_compId)
				->where('vch_txn_id', $voucher_txn_id)
				->countAllResults();

			$shipPayload = [
				'ewb_id'             => $ewb_id,
				'einv_id'            => $einv_id,
				'shipto_addr1'       => $data['shipto4_addr1'] ?? '',
				'shipto_addr2'       => $data['shipto4_addr2'] ?? '',
				'shipto_place'       => $data['shipto4_place'] ?? '',
				'shipto_pin'         => $data['shipto4_pin'] ?? '',
				'shipto_state_code'  => sprintf( '%02d',$data['shipto4_state_code']) ?? null,
				'shipto_gstin'       => $data['shipto4_gstin'] ?? '',
				'shipto_legal_name'  => $data['shipto4_legalname'] ?? '',
				'shipto_trade_name'  => $data['shipto4_tradename'] ?? '',
				'cmp_id'             => $sel_compId,
				'vch_txn_id'         => $voucher_txn_id
			  ];

			if ($shipExists == 0) {
				$this->db->table($shipTable)->insert($shipPayload);
			} else {
				unset($shipPayload['ewb_id'], $shipPayload['einv_id']);
				$this->db->table($shipTable)->where('vch_txn_id', $voucher_txn_id)->where('cmp_id', $sel_compId)->where('ewb_id', $ewb_id)->update($shipPayload);
			}

			// dispfrom
			$dispExists = $this->db->table($dispTable)
				->where('ewb_id', $ewb_id)
				->where('cmp_id',$sel_compId)
				->where('vch_txn_id', $voucher_txn_id)
				->countAllResults();

			$dispPayload = [
				'ewb_id'             => $ewb_id,
				'einv_id'            => $einv_id,
				'dispfrm_addr1'      => $data['dispfrm4_addr1'] ?? '',
				'dispfrm_addr2'      => $data['dispfrm4_addr2'] ?? '',
				'dispfrm_place'      => $data['dispfrm4_place'] ?? '',
				'dispfrm_pin'        => $data['dispfrm4_pin'] ?? '',
				'dispfrm_state_code' => sprintf( '%02d',$data['dispfrm4_state_code']) ?? null,
				'cmp_id'             => $sel_compId,
				'vch_txn_id'         => $voucher_txn_id
			];

			if ($dispExists == 0) {
				$this->db->table($dispTable)->insert($dispPayload);
			} else {
				unset($dispPayload['ewb_id'], $dispPayload['einv_id']);
				$this->db->table($dispTable)->where('cmp_id',$sel_compId)->where('ewb_id', $ewb_id)->update($dispPayload);
			}
		}
	}
   
   function party_gst_info($party_id)
	{
		// --- Pull GST & name info from acctmaster + acctmstdet ---
		$acctmaster_tbl = 'acctmaster';
		$acctmstdet_tbl = 'acctmstdet';

		$builder = $this->db->table("$acctmaster_tbl AS am");
		$builder->select('am.acc_name,ad.acc_gstin');
		$builder->join("$acctmstdet_tbl AS ad", 'ad.acc_id = am.acc_id', 'left');
		$builder->where('am.acc_id', $party_id);

		$gstRow = $builder->get()->getRowArray();

		// --- Get address -> state -> country info from contact tables + reference lists ---
		$addrTbl = 'contactaic_mycontaddr_univdb';
		$contTbl = 'contactaic_mycontacts_univdb';

		$addrBuilder = $this->contactaic_db->table("$addrTbl AS ca");
		// Prefer state_id if present; fall back to acc_state
		$addrBuilder->select('COALESCE(ca.contact_state) AS state_id');
		$addrBuilder->join("$contTbl AS c", 'c.contact_id = ca.contact_id', 'left');
		$addrBuilder->where('ca.contact_uuid', $this->session->get('uuid'));
		$addrBuilder->where('ca.party_id', $party_id);

		$addrRow = $addrBuilder->get()->getRowArray();

		$statecode    = '';
		$country_name = '';

		if ($addrRow && !empty($addrRow['state_id'])) {
			$state_data = $this->aicountly_db
				->table('aicountly_stateslist_univdb')
				->select('state_code, state_id, country_id')
				->where('state_id', $addrRow['state_id'])
				->get()
				->getRowArray();

			if ($state_data) {
				$country_id = $state_data['country_id'];

				$country_data = $this->aicountly_db
					->table('aicountly_countrylst_univdb')
					->select('countryid, countryname')
					->where('countryid', $country_id)
					->get()
					->getRowArray();

				$country_name = $country_data ? $country_data['countryname'] : '';
				$statecode    = sprintf('%02d', $state_data['state_code']);
			}
		}

		// --- Final payload ---
		if ($gstRow) {
			return [
				'acc_gstin'      => $gstRow['acc_gstin'] ?? '',
				'acc_legal_name' => $gstRow['acc_name'] ?? '',
				'acc_trade_name' => $gstRow['acc_name'] ?? '',
				'statecode'      => $statecode,
				'country'        => $country_name,
			];
		}

		return [
			'acc_gstin'      => '-',
			'acc_trade_name' => '-',
			'acc_legal_name' => '-',
			'statecode'      => '-',
			'country'        => '-',
		];
	}

  public function get_pr(){
	   $data       = $this->db->table("projectmst")
		       ->select('project_id as id, project_name as label, project_name as value')
		       ->where('project_is_active',1)
			    ->where('cmp_id', $this->company_id)
		       ->get()->getResultArray();
        return $data;  
   }
   
   public function SaveProjectReportingData($prdata,$voucher_date,$voucher_txn_id,$txn_id,$compId=null,$boId=null,$fyId=null){
	 if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
		if($boId)
			$sel_boId = $boId;
		 else 
			$sel_boId = $this->bo_id;

		if($fyId)
			$sel_fyId = $fyId;
		 else 
			$sel_fyId = $this->fy_id;
	 if($prdata){
				foreach($prdata as $key => $value){   
					$amount   = $value["proj_txn_amt"];
					$amountfc = 0;
					if(isset($value["proj_txn_amtfc"]))
					$amountfc = $value["proj_txn_amtfc"];
					if(isset($value['acc_type']) && $value['acc_type']=='acc')
						 $acc_bsd_type = 1;
					else if(isset($value['acc_type']) && $value['acc_type']=='bsd')
						 $acc_bsd_type = 2; 
					else
						$acc_bsd_type = 1; 	
				    $proj_txn_data = [
					    'cmp_id'             => $sel_compId,
						'project_id'         => $value['project_id'],
						'project_txn_date'   => $voucher_date,	
						'project_txn_dr_cr'  => ($value['proj_txn_drcr']=='C') ? 2 : 1,
						'project_txn_amt'    => $amount,
						'project_txn_fcy'    => $amountfc,
						'project_txn_narr'   => $value['proj_txn_narr'],
						'vch_txn_id'         => $voucher_txn_id,
						'txn_id'             => $txn_id,
						'hobo_id'            => $sel_boId,
						'project_txn_type'   => ($value['proj_txn_drcr']=='C') ? 1 : 2, //1 for Liability 2 for Asset
						'acc_bsd_id'         => $value['acc_id'],
						'acc_bsd_type'       => $acc_bsd_type  //1 for Accounts 2 for Bill Sundry	
					  ];					  
					  $this->add_pr_txn_data($proj_txn_data);  
					}

				}
       
   }
   
  public function SaveBillByBillData($bbbdata,$voucher_date,$voucher_txn_id,$txn_id,$compId=null,$boId=null,$fyId=null){
		if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
		if($boId)
			$sel_boId = $boId;
		 else 
			$sel_boId = $this->bo_id;

		if($fyId)
			$sel_fyId = $fyId;
		 else 
			$sel_fyId = $this->fy_id;
	
		if($bbbdata){
			 foreach($bbbdata as $key => $value){
					$bill_ref_id   = 0;
					$bill_due_date = $value['due_date'] != '' ? validate_date_by_fy($value['due_date']) : $voucher_date;
					  if(strtolower(trim($value['method'])) == 'new ref.'){
						$bill_master_data = [
						  'cmp_id'         => $sel_compId,
						  'bill_ref_name'  => $value['reference'],
						  'acc_id'         => $value['account_id'],
						  'bill_due_date'  => $bill_due_date,
						  'bill_status'    => 0,
						 ];
						 $bill_ref_id = $this->add_bill_master($bill_master_data,$sel_compId,$sel_boId,$sel_fyId);
					  }
					  if(strtolower(trim($value['method'])) == 'adjustment'){
						if($value['reference_id'] == 0 && $value['reference'] == 'UNDEFINED'){
						  $bill_ref_id = $this->getUndefinedBillRefId($value['account_id'],$sel_compId,$sel_boId,$sel_fyId);  
						}
						else{
						  $bill_ref_id      = $value['reference_id'];
						  $bill_master_data = ['bill_due_date'  => $bill_due_date,'bill_status'=>1];
						  $this->update_bill_master($bill_ref_id, $bill_master_data,$sel_compId,$sel_boId,$sel_fyId);
						}
					  }
					  if($bill_ref_id){
						$bill_txn_data = [
						  'cmp_id'          => (int)$sel_compId,
						  'bill_ref_id'     => (int)$bill_ref_id,
						  'acc_id'          => (int)$value['account_id'],
						  'bill_txn_date'   => $voucher_date,
						  'bill_txn_dr_cr'  => ($value['drcr']=='D') ? 1:2,
						  'bill_txn_amt'    => $value['amount'],
						  'bill_txn_fcy'    => $value['amountfc'] ?? 0,
						  'bill_txn_narr'   => $value['narration'],
						  'vch_txn_id'      => (int)$voucher_txn_id,
						  'txn_id'          => (int)$txn_id,
						  'hobo_id'         => (int)$sel_boId
						  ];
						$this->add_bill_txn($bill_txn_data);						
					  }
				}
			}		
		
	}
  
  public function add_batchmastr_master($batch_data){
	 $data  = $this->db->table("batchmastr")
		       ->select('batch_master_id,batch_no')
		       ->where('LOWER(batch_no)',strtolower($batch_data['batch_no']))->where('batch_is_active',1)->where('item_id_unit_id',$batch_data['item_id_unit_id'])
		       ->get()->getRowArray();  
	 if($data){
		 return $data['batch_master_id'];
	 }else{
		 $this->db->table("batchmastr")->insert($batch_data);
		 $batch_master_id = $this->db->insertID();
		 
        // Add Batch Opening Balances
		$op_data = [
		    'cmp_id'            => (int)$this->company_id,
			'cmpfymastr_id'     => (int)$this->fy_id,
      		'batch_master_id' 	=> (int)$batch_master_id,      		
      		'batch_op_bal_qty' 	=> (int)$batch_data['batch_qty'] ?? 0,
      		'batch_py_bal_qty' 	=> (int)0,
			'hobo_id' 		    => (int)$this->bo_id,
      	 ];
      	 $this->db->table("batchoppyb")->insert($op_data);
		 return $batch_master_id;
	 }		   
  }
  
   public function SaveBatchData($batchdata,$voucher_date,$voucher_txn_id,$txn_id,$matrcntr_id,$compId=null,$boId=null,$fyId=null){
		if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
		if($boId)
			$sel_boId = $boId;
		 else 
			$sel_boId = $this->bo_id;

		if($fyId)
			$sel_fyId = $fyId;
		 else 
			$sel_fyId = $this->fy_id;
		if($batchdata){
			 foreach($batchdata as $key => $value){
					$batch_id             = $value['batch_id'];
					$expiry_date          = $value['expiry_date'] != '' ? validate_date_by_fy($value['expiry_date']) : $voucher_date;
					$manufacturing_date   = $value['manufacturing_date'] != '' ? validate_date_by_fy($value['manufacturing_date']) : $voucher_date;
					$item_id_unit_id      = $value['item_id'].'_'.$value['batch_uom_id'];
					if(strtolower(trim($value['batch_method'])) == 'new ref.'){
						$batchmastr_master_data = [
						  'cmp_id'            => $sel_compId,
						  'batch_no'          => $value['batch_no'],
						  'batch_expiry_date' => $expiry_date,
						  'batch_mfr_date'    => $manufacturing_date,
						  'item_id_unit_id'   => $item_id_unit_id,
						  'batch_qty'         => parseAmount($value['batch_qty']),
						  'batch_is_active'   => 1
						 ];
						 $batch_id = $this->add_batchmastr_master($batchmastr_master_data);
					  }	
					  
					  if($batch_id){
						$bill_txn_data = [
						  'cmp_id'           => $sel_compId,
						  'batch_master_id'  => $batch_id,
						  'batch_txn_date'   => $voucher_date,
						  'batch_txn_dr_cr'  => $value['drcr_type'],
						  'batch_txn_qty'    => $value['batch_qty'],
						  'vch_txn_id'       => $voucher_txn_id,
						  'txn_id'           => $txn_id,
						  'mat_cent_id'      => $matrcntr_id,
						  'hobo_id'          => $sel_boId
						  ];
						$this->add_batch_txn($bill_txn_data);						
					  }
				}
			}		
		
	}
	
  public  function get_bills_txn_data($voucher_txn_id,$compId=null,$boId=null){
	    if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
		if($boId)
			$sel_boId = $boId;
		 else 
			$sel_boId = $this->bo_id;
	    $builder = $this->db->table('billtxnmst txn');
		$builder->select("
				txn.vch_txn_id,
				txn.txn_id,
				txn.bill_txn_date,
				txn.acc_id,			
				txn.bill_ref_id,
				txn.bill_txn_fcy,
				txn.bill_txn_amt,				
				txn.bill_txn_narr,
				bmst.bill_ref_name,
				bmst.bill_due_date,				
				CASE 
					WHEN txn.bill_txn_dr_cr = 1 THEN 'D'
					WHEN txn.bill_txn_dr_cr = 2 THEN 'C'
					ELSE 'D'
				END AS bill_txn_dr_cr	
			", false);
		$builder->join('billmaster bmst', 'bmst.bill_ref_id = txn.bill_ref_id', 'left');		
		$builder->where('bmst.bill_is_active',1);
		$builder->where('bmst.cmp_id', (int)$sel_compId);
		$builder->where('txn.cmp_id', (int)$sel_compId);
		$builder->where('txn.hobo_id', (int)$sel_boId);
		$builder->where('txn.vch_txn_id', (int)$voucher_txn_id);
		$builder->where('txn.vch_txn_id IS NOT NULL');
		$result = $builder->get()->getResultArray();
		
		$final = array();
		if($result){
			foreach($result as $row){
				$account_id = $row['acc_id'];
				$final[$account_id][]= $row;
			}
		}
		
		$master =[];
		foreach($final as $acc_id => $res){
			$master[] = [
                    'acc_id' => $acc_id,
                    'bills_txn_list' => $res
                ];
		}		
    	return $master;
	} 
	/*********  Updated Function *************/
	public  function get_bills_txn_lists($voucher_txn_id,$compId=null,$boId=null){
	    if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
		if($boId)
			$sel_boId = $boId;
		 else 
			$sel_boId = $this->bo_id;
	    $builder = $this->db->table('billtxnmst txn');
		$builder->select("
				txn.vch_txn_id,
				txn.txn_id,
				txn.bill_txn_date,
				txn.acc_id,			
				txn.bill_ref_id,
				txn.bill_txn_fcy,
				txn.bill_txn_amt,				
				txn.bill_txn_narr,
				bmst.bill_ref_name,
				bmst.bill_due_date,				
				CASE 
					WHEN txn.bill_txn_dr_cr = 1 THEN 'D'
					WHEN txn.bill_txn_dr_cr = 2 THEN 'C'
					ELSE 'D'
				END AS bill_txn_dr_cr	
			", false);
		$builder->join('billmaster bmst', 'bmst.bill_ref_id = txn.bill_ref_id', 'left');		
		$builder->where('bmst.bill_is_active',1);
		$builder->where('bmst.cmp_id', (int)$sel_compId);
		$builder->where('txn.cmp_id', (int)$sel_compId);
		$builder->where('txn.hobo_id', (int)$sel_boId);
		$builder->where('txn.vch_txn_id', (int)$voucher_txn_id);
		$builder->where('txn.vch_txn_id IS NOT NULL');
		$result = $builder->get()->getResultArray();
		
		$final = array();
		if($result){
			foreach($result as $row){
				$account_id = $row['acc_id'];
				$final[$account_id][$row['txn_id']][] = $row;
			}
		}
		
		$master = [];
		foreach ($final as $acc_id => $txnGroups) {
			foreach ($txnGroups as $txn_id => $rows) {
				$master[] = [
					'acc_id' => $acc_id,
					'txn_id' => $txn_id,
					'bills_txn_list' => $rows
				];
			}
		}
    	return $master;
	}
	
   public function get_cc_txn_data($voucher_txn_id,$compId=null,$boId=null){
    if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
		if($boId)
			$sel_boId = $boId;
		 else 
			$sel_boId = $this->bo_id;
    $builder = $this->db->table('cctxnmstnn txn');
    $builder->select("
        txn.vch_txn_id,
        txn.txn_id,
        txn.cc_txn_date,
        txn.acc_id,			
        txn.cc_id,			
        txn.cc_txn_amt,
        txn.cc_txn_fcy,
        txn.cc_txn_narr,
        bmst.cc_name,							
        CASE 
            WHEN txn.cc_txn_dr_cr = 1 THEN 'D'
            WHEN txn.cc_txn_dr_cr = 2 THEN 'C'
            ELSE 'D'
        END AS cc_txn_dr_cr	
    ", false);

    $builder->join('ccmasternn bmst', 'bmst.cc_id = txn.cc_id', 'left');

    $builder->where('bmst.cc_is_active',1);
    $builder->where('bmst.cmp_id', (int)$sel_compId);
    $builder->where('txn.cmp_id', (int)$sel_compId);
    $builder->where('txn.hobo_id', (int)$sel_boId);
    $builder->where('txn.vch_txn_id', (int)$voucher_txn_id);

    $result = $builder->get()->getResultArray();

    // 🔥 STEP 1: GROUP BY acc_id + txn_id
    $final = [];

    if($result){
        foreach($result as $row){
            $acc_id = $row['acc_id'];
            $txn_id = $row['txn_id'];

            $final[$acc_id][$txn_id][] = $row;
        }
    }

    // 🔥 STEP 2: FLATTEN STRUCTURE
    $master = [];

    foreach($final as $acc_id => $txnGroups){

        foreach($txnGroups as $txn_id => $rows){

            $master[] = [
                'acc_id' => $acc_id,
                'txn_id' => $txn_id,
                'cc_txn_list' => $rows
            ];
        }
    }

    return $master;
}

public function get_pr_txn_data($voucher_txn_id, $compId = null, $boId = null)
{
    if ($compId)
        $sel_compId = $compId;
    else 
        $sel_compId = $this->company_id;

    if ($boId)
        $sel_boId = $boId;
    else 
        $sel_boId = $this->bo_id;

    $builder = $this->db->table('prjtxnmstn txn');

    $builder->select("
        txn.vch_txn_id,
        txn.txn_id,
        txn.project_txn_date,
        txn.acc_bsd_id as acc_id,			
        txn.acc_bsd_type,
        txn.project_id,			
        txn.project_txn_amt,
        txn.project_txn_amt as proj_txn_amt,
        txn.project_txn_fcy,
        txn.project_txn_fcy as proj_txn_amtfc,
        txn.project_txn_narr,
        txn.project_txn_narr as proj_txn_narr,
        bmst.project_name,
        CASE 
            WHEN txn.project_txn_dr_cr = 1 THEN 'D'
            WHEN txn.project_txn_dr_cr = 2 THEN 'C'
            ELSE 'D'
        END AS project_txn_dr_cr,
        CASE 
            WHEN txn.project_txn_dr_cr = 1 THEN 'D'
            WHEN txn.project_txn_dr_cr = 2 THEN 'C'
            ELSE 'D'
        END AS proj_txn_drcr	
    ", false);

    $builder->join('projectmst bmst', 'bmst.project_id = txn.project_id', 'left');

    $builder->where('bmst.project_is_active', 1);
    $builder->where('bmst.cmp_id', (int)$sel_compId);
    $builder->where('txn.cmp_id', (int)$sel_compId);
    $builder->where('txn.hobo_id', (int)$sel_boId);
    $builder->where('txn.vch_txn_id', (int)$voucher_txn_id);

    $result = $builder->get()->getResultArray();

    // 🔥 STEP 1: GROUP BY acc_id + txn_id (with acc_type stored)
    $final = [];

    if ($result) {
        foreach ($result as $row) {
            $acc_id = $row['acc_id'];
            $txn_id = $row['txn_id'];
            // Convert acc_bsd_type: 1 = 'acc', otherwise = 'bsd'
            $acc_type = ($row['acc_bsd_type'] == 1) ? 'acc' : 'bsd';

            // Initialize the group if not exists
            if (!isset($final[$acc_id][$txn_id])) {
                $final[$acc_id][$txn_id] = [
                    'acc_type' => $acc_type,
                    'rows' => []
                ];
            }

            $final[$acc_id][$txn_id]['rows'][] = $row;
        }
    }

    // 🔥 STEP 2: FLATTEN STRUCTURE
    $master = [];

    foreach ($final as $acc_id => $txnGroups) {
        foreach ($txnGroups as $txn_id => $data) {
            $master[] = [
                'acc_id' => $acc_id,
                'txn_id' => $txn_id,
                'acc_type' => $data['acc_type'],
                'pr_txn_list' => $data['rows']
            ];
        }
    }

    return $master;
}
	
  public function get_pr_txn_dataoldee($voucher_txn_id,$compId=null,$boId=null){
	  if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
		if($boId)
			$sel_boId = $boId;
		 else 
			$sel_boId = $this->bo_id;
	    $builder = $this->db->table('prjtxnmstn txn');
		$builder->select("
				txn.vch_txn_id,
				txn.project_txn_date,
				txn.acc_bsd_id as acc_id,			
				txn.project_id,			
				txn.project_txn_amt,
				txn.project_txn_amt as proj_txn_amt,
				txn.project_txn_fcy,txn.project_txn_fcy as proj_txn_amtfc,
				txn.project_txn_narr,txn.project_txn_narr as proj_txn_narr,
				bmst.project_name						
				,
				CASE 
					WHEN txn.project_txn_dr_cr = 1 THEN 'D'
					WHEN txn.project_txn_dr_cr = 2 THEN 'C'
					ELSE 'D'
				END AS project_txn_dr_cr,
				CASE 
					WHEN txn.project_txn_dr_cr = 1 THEN 'D'
					WHEN txn.project_txn_dr_cr = 2 THEN 'C'
					ELSE 'D'
				END AS proj_txn_drcr	
			", false);
		$builder->join('projectmst bmst', 'bmst.project_id = txn.project_id', 'left');		
		$builder->where('bmst.project_is_active',1);
		$builder->where('bmst.cmp_id', (int)$sel_compId);
		$builder->where('txn.cmp_id', (int)$sel_compId);
		$builder->where('txn.hobo_id', (int)$sel_boId);
		$builder->where('txn.vch_txn_id', (int)$voucher_txn_id);
		$builder->where('txn.vch_txn_id IS NOT NULL');
		$result = $builder->get()->getResultArray();
		$final = array();
		if($result){
			foreach($result as $row){
				$account_id = $row['acc_id'];
				$final[$account_id][]= $row;
			}
		}
		
		$master =[];
		foreach($final as $acc_id => $res){
			$master[] = [
                    'acc_id' => $acc_id,
					'pr_txn_list' => $res
                ];
		}		
    	return $master;
	}
	
	public function get_itembatch_txn_data($voucher_txn_id,$compId=null,$boId=null){
	    if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
		if($boId)
			$sel_boId = $boId;
		 else 
			$sel_boId = $this->bo_id;
		$builder = $this->db->table('batchtxnmt txn');
		$builder->select("
			txn.vch_txn_id,
			-- MySQL DATE_FORMAT replacement
			TO_CHAR(bmst.batch_expiry_date, 'DD-MM-YYYY') AS expiry_date,
			TO_CHAR(bmst.batch_mfr_date, 'DD-MM-YYYY') AS manufacturing_date,
			bmst.batch_master_id AS batch_id,
			bmst.item_id_unit_id,
			bmst.batch_no,
			bmst.batch_qty
		", false);   // IMPORTANT: disable escaping

		$builder->join(
			'batchmastr bmst',
			'bmst.batch_master_id = txn.batch_master_id',
			'left'
		);

		// WHERE conditions
		$builder->where('bmst.batch_is_active', 1);
		$builder->where('bmst.cmp_id', (int)$sel_compId);
		$builder->where('txn.cmp_id', (int)$sel_compId);
		$builder->where('txn.hobo_id', (int)$sel_boId);
		$builder->where('txn.vch_txn_id', (int)$voucher_txn_id);
		$builder->where('txn.vch_txn_id IS NOT NULL', null, false);
		$result = $builder->get()->getResultArray();
		$final  = array();
		if($result){
			foreach($result as $row){
				$item_id_unit_id = $row['item_id_unit_id'];
				$row['batch_method'] = 'Adjustment';
				$final[$item_id_unit_id][]= $row;				
			}
		}
		
		$master =[];
		foreach($final as $item_id_unit_id => $res){
			$item_unit_info  = explode("_",$item_id_unit_id);
			$item_id         = $item_unit_info[0];
			$unit_id         = $item_unit_info[1];	
			$master[] = [
						'item_id'       => $item_id,
						'item_unit_id'  => $unit_id,
						'grid'          => $res
                       ];
		}		
	
    	return $master;
	}
	
   public function get_sblgr_txn_data($voucher_txn_id)
{
    $builder = $this->db->table('subacctxnm txn');

    $builder->select("
        txn.vch_txn_id,
        txn.txn_id,
        txn.sub_acc_txn_date,
        txn.acc_id,			
        txn.sub_acc_id,			
        txn.sub_acc_txn_amt,
        txn.sub_acc_txn_fcy,
        txn.sub_acc_txn_narr,
        bmst.sub_acc_name,
        CASE 
            WHEN txn.sub_acc_txn_dr_cr = 1 THEN 'D'
            WHEN txn.sub_acc_txn_dr_cr = 2 THEN 'C'
            ELSE 'D'
        END AS sub_acc_txn_dr_cr	
    ", false);

    $builder->join('subacctmst bmst', 'bmst.sub_acc_id = txn.sub_acc_id', 'left');

    $builder->where('bmst.sub_acc_is_active', 1);
    $builder->where('bmst.cmp_id', (int)$this->company_id);
    $builder->where('txn.cmp_id', (int)$this->company_id);
    $builder->where('txn.hobo_id', (int)$this->bo_id);
    $builder->where('txn.vch_txn_id', (int)$voucher_txn_id);
    $builder->where('txn.vch_txn_id IS NOT NULL');

    $result = $builder->get()->getResultArray();

    $final = [];

    if ($result) {
        foreach ($result as $row) {
            $account_id = $row['acc_id'];
            $txn_id     = $row['txn_id'];

            // 🔴 IMPORTANT: group like bill-by-bill
            $final[$account_id][$txn_id][] = $row;
        }
    }

    $master = [];

    foreach ($final as $acc_id => $txnGroups) {
        foreach ($txnGroups as $txn_id => $rows) {

            $master[] = [
                'acc_id' => $acc_id,
                'txn_id' => $txn_id, // 🔴 ADD THIS
                'sblgr_txn_list' => $rows
            ];
        }
    }

    return $master;
}
	
  public function add_bill_txn($bill_txn_data){
		 $this->db->table("billtxnmst")->insert($bill_txn_data);	
	}
  public function add_batch_txn($batch_txn_data){
		 $this->db->table("batchtxnmt")->insert($batch_txn_data);	
	}
	
  public function SaveNotifications($erp_notify_log,$voucher_txn_id,$Voucher_TxnApproval_UUID,$amount){
	    $logged_user_name = $this->session->get('f_name').' '.$this->session->get('l_name');
	    $logged_user_uuid = $this->session->get('uuid');	   
	    // Replace placeholders with actual values
		$erp_notify_log = str_replace(
			['[USERNAME]', '[UUID]','[VCHAMOUNT]'],
			[$logged_user_name, $logged_user_uuid, $amount],
			$erp_notify_log
		);	
	    $activity = [
	        'cmp_id'           => (int)$this->company_id,
			'uuid_to'          => (int)$Voucher_TxnApproval_UUID,
			'notify_date_time' => date('Y-m-d H:i:s'),
			'notify_log'       => (int)$erp_notify_log,
			'vch_txn_id'       => (int)$voucher_txn_id,
			'notify_read'      => 1
		    ];
		 $this->db->table("erpnotifyn")->insert($activity);	
		 SaveErrorLog($this->db->getlastquery());
	}
	
  public function SaveUserActivity($erp_activity_log,$voucher_txn_id){
	   $logged_user_name = $this->session->get('f_name').' '.$this->session->get('l_name');
	   $logged_user_uuid = $this->session->get('uuid');	   
	    // Replace placeholders with actual values
		$erp_activity_log = str_replace(
			['[USERNAME]', '[UUID]'],
			[$logged_user_name, $logged_user_uuid],
			$erp_activity_log
		);	
	    $activity = [
	        'cmp_id'                 => (int)$this->company_id,
			'uuid'                   => (int)$logged_user_uuid,
			'erp_activity_date_time' => date('Y-m-d H:i:s'),
			'erp_activity_log'       => $erp_activity_log,
			'vch_txn_id'             => (int)$voucher_txn_id,
		    ];
		 $this->db->table("erpactivty")->insert($activity);	
	}
	
  public  function add_sublgr_txn($sblgr_txn_data){
		 $this->db->table("subacctxnm")->insert($sblgr_txn_data);	
	}
	
   public function add_cc_txn($cc_txn_data){
		 $this->db->table("cctxnmstnn")->insert($cc_txn_data);	
	}
  
   public function add_taxsummary_fcy_data($fcy_txn_data){
		 $this->db->table("vchgstfcyn")->insert($fcy_txn_data);	
	}
 

public function computeGstTotals(int $voucher_txn_id): array
{
    $totInvValue = $cgstValue = $sgstValue = $igstValue = $cessValue = 0.0;
    $TotInvValFc = 0.0;

    // From vchgstsumn (local)
    $sum = $this->db->table('vchgstsumn')
        ->select('
            COALESCE(SUM(vch_taxable_value),0)      AS taxable_sum,
            COALESCE(SUM(vch_total_tax),0)          AS tax_sum,
            COALESCE(SUM(vch_cgst),0)               AS cgst_sum,
            COALESCE(SUM(vch_sgst_ugst),0)          AS sgst_sum,
            COALESCE(SUM(vch_igst),0)               AS igst_sum,
            COALESCE(SUM(vch_cess),0)               AS cess_sum
        ')
        ->where('vch_txn_id', $voucher_txn_id)
        ->get()
        ->getRowArray();

    if ($sum) {
        $totInvValue = (float)$sum['taxable_sum'] + (float)$sum['tax_sum'];
        $cgstValue   = (float)$sum['cgst_sum'];
        $sgstValue   = (float)$sum['sgst_sum'];
        $igstValue   = (float)$sum['igst_sum'];
        $cessValue   = (float)$sum['cess_sum'];
    }

    // From vchgstfcyn (FCY totals)
    $fcy = $this->db->table('vchgstfcyn')
        ->select('
            COALESCE(SUM(vch_taxable_value_fcy + vch_total_tax_fcy),0) AS tot_fcy
        ')
        ->where('vch_txn_id', $voucher_txn_id)
        ->get()
        ->getRowArray();

    if ($fcy) {
        $TotInvValFc = (float)$fcy['tot_fcy'];
    }

    return [
        'totInvValue' => $totInvValue,
        'TotInvValFc' => $TotInvValFc,
        'cgstValue'   => $cgstValue,
        'sgstValue'   => $sgstValue,
        'igstValue'   => $igstValue,
        'cessValue'   => $cessValue,
    ];
}

  public function get_item_batch_list($item_id_unit_id){       
	   $batch_data   = $this->db->table("batchmastr")->select(array('batch_master_id','batch_no','item_id_unit_id','batch_qty','batch_expiry_date','batch_mfr_date'))->where('batch_is_active',1)->where('cmp_id',$this->company_id)->where('item_id_unit_id',$item_id_unit_id)->get()->getResultArray();
	   $all_batches =  array();	   
	   if($batch_data){
		   foreach($batch_data as $row){
			 if($row['batch_expiry_date']!=''){
				$batch_expiry =date('d-m-Y',strtotime($row['batch_expiry_date']));
			 }  
			 else{
				$batch_expiry=''; 
			 }
			 
			 if($row['batch_mfr_date']!=''){
				$batch_mfr =date('d-m-Y',strtotime($row['batch_mfr_date']));
				
			 }  
			 else{
				$batch_mfr=''; 
			 }
			 
			 $all_batches[]=array("id"=>$row['batch_master_id'],"label"=>$row['batch_no'],"value"=>$row['batch_no'],
			                       "item_id_unit_id"=>$row['item_id_unit_id'],'batch_qty'=>$row['batch_qty'],
								   'batch_expiry'=>$batch_expiry,'batch_mfr'=>$batch_mfr 
								   );   
		   }
		   
	   }
	   	
      return $all_batches; 
    }  
  
	public function all_item_batch_list(){
	  $batch_data = $this->db->table("batchmastr")
			->select("
				DISTINCT ON (batch_no)
				batch_master_id,
				batch_no,
				item_id_unit_id,
				batch_qty,
				batch_expiry_date,
				batch_mfr_date
			", false) // Pass false to prevent escaping the raw select expression
			->where('batch_is_active', 1)
			->where('cmp_id', $this->company_id)
			->orderBy('batch_no', 'ASC')
			->orderBy('batch_master_id', 'ASC') // Important for consistent DISTINCT ON results
			->get()
			->getResultArray();

		$all_batches = [];
		if ($batch_data) {
			foreach ($batch_data as $row) {
				$batch_expiry = '';
				if (!empty($row['batch_expiry_date'])) {
					$batch_expiry = date('d-m-Y', strtotime($row['batch_expiry_date']));
				}

				$batch_mfr = '';
				if (!empty($row['batch_mfr_date'])) {
					$batch_mfr = date('d-m-Y', strtotime($row['batch_mfr_date']));
				}

				$all_batches[] = [
					"id"              => $row['batch_master_id'],
					"label"           => $row['batch_no'],
					"value"           => $row['batch_no'],
					"item_id_unit_id" => $row['item_id_unit_id'],
					'batch_qty'       => $row['batch_qty'],
					'batch_expiry'    => $batch_expiry,
					'batch_mfr'       => $batch_mfr,
				];
			}
		}

		return $all_batches;
	}
 
  public function get_account_bill_refs($account_id){
    // cast to int for safety (prevent injection via string)
    $account_id = (int) $account_id;
    $builder = $this->db->table('billmaster');
    // Use TO_CHAR for PostgreSQL date formatting. Pass false to avoid CI escaping the expression.
    $builder->select(
        "bill_ref_id AS id,
         bill_ref_name AS label,
         bill_ref_name AS value,
         bill_due_date,
         TO_CHAR(bill_due_date, 'DD-MM-YYYY') AS due_date",
        false
    );
	$builder->where('acc_id', $account_id);
    $builder->where('cmp_id', $this->company_id);
    $builder->where('bill_ref_name !=', 'UNDEFINED');
    $data = $builder->get()->getResultArray();
    return $data;
   }
	
 public function get_account_subledger_refs($account_id)
{
    $data = $this->db->table("subacctmst")
        ->select("
            sub_acc_id as id,
            sub_acc_name as label,
            sub_acc_name as value,
            sub_due_date,
            TO_CHAR(sub_due_date::date, 'DD-MM-YYYY') as due_date
        ", false)
        ->where('acc_id', $account_id)
        ->where('cmp_id', $this->company_id)
        ->where('sub_acc_name !=', 'UNDEFINED')
        ->get()
        ->getResultArray();

    return $data;
}
	
  public function get_currency_list()
	{
		$data = $this->univaictly->table("cmpfcymstn")
		        ->select("cmp_fcy_mst_id AS comp_currency_id, cmp_fcy_name AS curr_name, cmp_fcy_symbol AS curr_symbol" )
				->where('cmp_id', $this->company_id)->get()->getResultArray();
       return $data;
	}
	
  public function save_voucher_fcyrate($data){
	 $exists = $this->db->table("vchfcyrate")->where('cmp_id',$data['cmp_id'])->where('vch_txn_id',$data['vch_txn_id'])->get()->getRowArray();
	   if($exists){
	     $this->db->table("vchfcyrate")->where('cmp_id',$data['cmp_id'])->where('vch_txn_id',$data['vch_txn_id'])->update(["vch_fcy_rate"=>$data["vch_fcy_rate"],"cmp_fcy_mst_id"=>$data["cmp_fcy_mst_id"]]);	 
	   }else{
		  $this->db->table("vchfcyrate")->insert($data);		 
	   }	
	}
   	
  public function all_mig_bo_lists(){ 
      $data           = $this->univaictly->table("hobomaster")->where('hobo_id !=',$this->bo_id)->orderBy('hobo_name')->get()->getResultArray();
	  $final_result   = array();
	  if($data){
	   foreach($data as $row)
	     $final_result[$row['hobo_id']] = ucwords($row['hobo_name']);			   			
	   } 
	  return $final_result; 
    }
  
   public function get_vouchertype_info($voucher_type_id){	 
	  return $this->db->table("vchtypemst")->where('vch_type_id', $voucher_type_id)->get()->getRowArray();   	   
    } 	
	
	
	public function get_voucher_bridge_info($src_vch_txn_id, $vch_bridge_type = null, $compId = null)
{
    try {

        // Resolve Company ID
        $sel_compId = $compId ?? $this->company_id;

        $builder = $this->db->table('vchbridgen')
            ->where('cmp_id', $sel_compId)
            ->where('vch_txn_id_src', $src_vch_txn_id);

        if ($vch_bridge_type !== null) {
            $builder->where('vch_bridge_type', $vch_bridge_type);
        }

        $row = $builder->get()->getRowArray();

        // ✅ Return data if found, else false
        return !empty($row) ? $row : false;

    } catch (\Throwable $e) {

        log_message('error', 'get_voucher_bridge_info - ' . $e->getMessage());
        log_message('error', 'File: ' . $e->getFile() . ' | Line: ' . $e->getLine());

        return false;
    }
}
	 
	 public function update_voucher_bridge_data($data,$vch_txn_id_dest,$vch_bridge_type,$compId=null){
		 if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;

		 $this->db->table("vchbridgen")->where('cmp_id',$sel_compId)->where('vch_txn_id_dest',$vch_txn_id_dest)->where('vch_bridge_type',$vch_bridge_type)->update($data);		 
	 }
	 	
	
    public function update_voucher_cons_data($data,$voucher_txn_id,$comp_id,$boId=null){
		if($boId)
			$sel_boId = $boId;
		 else 
			$sel_boId = $this->bo_id;	

		if($sel_boId)
		$this->db->table("vchtxnconso")->where('hobo_id',$sel_boId)->where('cmp_id',$comp_id)->where('vch_txn_id',$voucher_txn_id)->update($data);		 
         else 
		 $this->db->table("vchtxnconso")->where('cmp_id',$comp_id)->where('vch_txn_id',$voucher_txn_id)->update($data);		 
	 }
	 
	 public function getVoucherNameByTxnId(int $vch_txn_id): ?string
	{
		$row = $this->db->table('vchtxnconso t')
			->select('m.vch_name')
			->join('vchtypemst m', 'm.vch_type_id = t.vch_type_id', 'left')
			->where('t.cmp_id', $this->company_id)   // keep company scoping
			->where('t.vch_txn_id', $vch_txn_id)
			->get()
			->getRowArray();

		return $row['vch_name'] ?? null;
	}
	 
	 public function get_voucher_gstpaid_bridge_info($vch_txn_id_dest, $vch_bridge_type = null)
	{
		$builder = $this->db->table('vchbridgen')
			->where('cmp_id', $this->company_id)
			->where('vch_txn_id_dest', $vch_txn_id_dest);

		if ($vch_bridge_type !== null) {
			$builder->where('vch_bridge_type', $vch_bridge_type);
		}

		return $builder->get()->getRowArray();
	}
	 
	 public function update_comps_voucher_cons_data($data,$voucher_txn_id,$comp_id,$voucher_type_id,$boId=null){
		if($boId)
			$sel_boId = $boId;
		 else 
			$sel_boId = $this->bo_id;

		if($sel_boId)
		$this->db->table("vchtxnconso")->where('hobo_id', $sel_boId)->where('cmp_id',$comp_id)->where('vch_txn_id',$voucher_txn_id)->where('vch_type_id',$voucher_type_id)->update($data);		 
         else 
		 $this->db->table("vchtxnconso")->where('cmp_id',$comp_id)->where('vch_txn_id',$voucher_txn_id)->where('vch_type_id',$voucher_type_id)->update($data);		 
	 }
	 public function getVoucherInfoTxnId(int $vch_txn_id): ?array
	{
		$row = $this->db->table('vchtxnconso t')
			->join('vchtypemst m', 'm.vch_type_id = t.vch_type_id', 'left')
			->where('t.cmp_id', $this->company_id)   // keep company scoping
			->where('t.vch_txn_id', $vch_txn_id)
			->get()
			->getRowArray();

		return $row;
	}
	 
	 public function update_gstroutsup_data($voucher_txn_id,$data,$compId=null){
		 if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;

		
		$exists = $this->db->table("gstroutsup")->where('cmp_id',$sel_compId)->where('vch_txn_id',$voucher_txn_id)->get()->getRowArray();
		 if($exists)
		$this->db->table("gstroutsup")->where('cmp_id',$sel_compId)->where('vch_txn_id',$voucher_txn_id)->update($data);		 		 
	   else{
		$data['cmp_id']=$sel_compId;
		$data['vch_txn_id']=$voucher_txn_id; 		
		$this->db->table("gstroutsup")->insert($data);   
	   }
	
		 
	 }
	 public function update_gstrinwsup_data($voucher_txn_id,$data,$compId=null){
	     if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		 $exists = $this->db->table("gstrinwsup")->where('cmp_id',$sel_compId)->where('vch_txn_id',$voucher_txn_id)->get()->getRowArray();
		 if($exists)
		 $this->db->table("gstrinwsup")->where('cmp_id',$sel_compId)->where('vch_txn_id',$voucher_txn_id)->update($data);		 
	   else{
		$data['cmp_id']=$sel_compId;
		$data['vch_txn_id']=$voucher_txn_id; 		
		$this->db->table("gstrinwsup")->insert($data);   
	   }
		   
	 }	
  
  public function get_voucher_no($vch_type_id){
    $builder = $this->db->table("vchtxnconso");     
    $builder->select('COALESCE(MAX(vch_txn_id), 0) as max_comp_vch_no', false);
    $builder->where('vch_type_id', $vch_type_id);    
    $result = $builder->get()->getRowArray();    
    if($result){
        $max_comp_vch_no = (int)$result['max_comp_vch_no'];
        return $max_comp_vch_no + 1;
     }
     else {
        return 1;
      }
   }  
   
   public function get_short_narration_info($voucher_txn_id,$txn_id,$compId=null)
 	{
		if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
 		$narration = '';
 		$narr      = $this->db->table("vchshrtnar")
	   					->where('vch_txn_id',$voucher_txn_id)
						->where('txn_id',$txn_id)
						->where('cmp_id',$sel_compId)
	   					->get()->getRowArray();
	   	if($narr){
	   			$narration = $narr['vch_short_narr'];
	   		}
	   	return $narration;
 	}
   
   public  function get_comp_txn_data($voucher_txn_id){
	 	$comp_txn_master_tbl = 'cmptxnmstn';
	   	return $this->db->table($comp_txn_master_tbl)->where('vch_txn_id', $voucher_txn_id)->get()->getResultArray();
	}
	
	public  function get_comp_txn_party_data($voucher_txn_id){	 	
		$builder = $this->db->table('cmptxnmstn c');
        $row = $builder->select('c.*, a.acc_name, a.acc_alias, a.acc_print_name')
               ->join('acctmaster a', 'a.acc_id = c.master_id', 'left')
               ->where('c.vch_txn_id', $voucher_txn_id)
               ->where('c.master_id_type', 'acc')
			   ->orderBy('c.txn_id', 'ASC')
               ->get(1)   // limit 1
               ->getRowArray();		
			return $row;
			
	}
   public function get_cash_groups($compId=null)
    {
		if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
	    $groups = [23,21];
		$array = $this->get_sub_group_ids($groups,$sel_compId);
        $data = $this->db->table('accgrpmstn')
				->select('acc_grp_id')
				->like('LOWER(acc_grp_name)','cash','left')
				->where('cmp_id', $sel_compId)->where('acc_grp_is_active',1)
				->get()
				->getResultArray();
			if($data){
	    		foreach($data as $key => $value) {
	    			array_push($array, $value['acc_grp_id']);
	    		}
	    	}
        return $array;
    }
	
	
	 public function get_bankod_groups()
    {
	    $groups = [];
		$array = $this->get_sub_group_ids($groups);
        $data = $this->db->table('accgrpmstn')
				->select('acc_grp_id')
				->like('LOWER(acc_grp_name)','bank od','left')
				->where('cmp_id', $this->company_id)->where('acc_grp_is_active',1)
				->get()
				->getResultArray();
			if($data){
	    		foreach($data as $key => $value) {
	    			array_push($array, $value['acc_grp_id']);
	    		}
	    	}
        return $array;
    }
	
  public  function get_bbb_groups_list($compId=null)
      {
		  if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
        $groups = [16,22,4,5];
		$array = $this->get_sub_group_ids_list($groups,$sel_compId); 
		$data = $this->db->table('accgrpmstn')
				->select('acc_grp_id')
				->like('LOWER(acc_grp_name)','current liabilities','left')
				->like('LOWER(acc_grp_name)','current assets','left')
				->like('LOWER(acc_grp_name)','trade payable','left')
				->like('LOWER(acc_grp_name)','trade receivables','left')
				->where('cmp_id', $sel_compId)->where('acc_grp_is_active',1)
				->get()
				->getResultArray();
			if($data){
	    		foreach($data as $key => $value) {
	    			array_push($array, $value['acc_grp_id']);
	    		}
	    	}			
		
        return array_unique($array);
      }
	public function get_cc_groups_list($compId=null)
      {
		  if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
        $groups = [7,11,13];
		$array = $this->get_sub_group_ids_list($groups,$sel_compId); 
		$data = $this->db->table('accgrpmstn')
				->select('acc_grp_id')
				->like('LOWER(acc_grp_name)','purchase','left')
				->like('LOWER(acc_grp_name)','direct expenses','left')
				->like('LOWER(acc_grp_name)','indirect expenses','left')
				->where('cmp_id',$sel_compId)->where('acc_grp_is_active',1)
				->get()
				->getResultArray();
			if($data){
	    		foreach($data as $key => $value) {
	    			array_push($array, $value['acc_grp_id']);
	    		}
	    	}			
        return array_unique($array);
      }	
   public function get_sub_group_ids_list($array,$compId=null){  
        if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;   
    	if(!empty($array)){
    	    $builder = $this->db->table("accgrpmstn acgrpmst");
    	    $builder->join("undercrsmt", "undercrsmt.crs_mst_id = acgrpmst.acc_grp_id AND undercrsmt.crs_mst_type =2 AND undercrsmt.cmp_id =$sel_compId", 'left');
    	    $builder->select('acgrpmst.acc_grp_id');
    	    $builder->whereIn('undercrsmt.under_main_id', $array);
			$data = $builder->get()->getResultArray();
	    	if($data){
	    		foreach($data as $key => $value) {
	    			array_push($array, $value['acc_grp_id']);
	    		}
	    	}
    	}
    	return $array;
    }
    
  public function get_memoamount_sale_txn($voucher_txn_id,$txn_id,$type,$acc_id,$compId=null,$boId=null){
     if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
		if($boId)
			$sel_boId = $boId;
		 else 
			$sel_boId = $this->bo_id;

	 
	 $txn_row        = $this->db->table("accttxnmst")->select('acc_txn_amt')->where('hobo_id',$sel_boId)->where('cmp_id',$sel_compId)->where('txn_id',$txn_id)->where('acc_txn_type',3)->where('vch_txn_id',$voucher_txn_id)->get()->getRowArray(); 
     if($txn_row)
      return $txn_row['acc_txn_amt'];
      else
      return 0;
  }    
 
  public function get_taxamount_sale_txn($voucher_txn_id,$type,$txn_id,$compId=null){
     if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;

	 $txn_row        = $this->db->table("acctamtinc")->select('acc_txn_inc_amt')->where('cmp_id',$sel_compId)->where('vch_txn_id',$voucher_txn_id)->where('txn_id',$txn_id)->get()->getRowArray(); 
     if($txn_row)
      return $txn_row['acc_txn_inc_amt'];
      else
      return 0;	
  } 
  
  public function BillNoDuplicate($billno,$type,$is_edit=0,$vch_txn_id=0){
	if($is_edit==1){
		if($type=='purchase')
          $txn_row        = $this->db->table("gstrinwsup")->select('inwsup_bill_ref_no')->where('cmp_id',$this->company_id)->where('vch_txn_id !=',$vch_txn_id)->where('LOWER(inwsup_bill_ref_no)',strtolower($billno))->get()->getRowArray(); 
        else if($type=='sale')
         $txn_row        = $this->db->table("gstroutsup")->select('outsup_bill_ref_no')->where('cmp_id',$this->company_id)->where('vch_txn_id !=',$vch_txn_id)->where('LOWER(outsup_bill_ref_no)',strtolower($billno))->get()->getRowArray(); 
    }
	
    if($is_edit==0){
	    if($type=='purchase')
          $txn_row        = $this->db->table("gstrinwsup")->select('inwsup_bill_ref_no')->where('cmp_id',$this->company_id)->where('LOWER(inwsup_bill_ref_no)',strtolower($billno))->get()->getRowArray(); 
       else if($type=='sale')
         $txn_row        = $this->db->table("gstroutsup")->select('outsup_bill_ref_no')->where('cmp_id',$this->company_id)->where('LOWER(outsup_bill_ref_no)',strtolower($billno))->get()->getRowArray(); 	
	} 
	
    if($txn_row)
     return 1;
    else
      return 0;	
  }
   
  public function grid_item_transactions($voucher_txn_id, $compId = null, $boId = null)
{
    $sel_compId = $compId ?: $this->company_id;
    $sel_boId   = $boId   ?: $this->bo_id;

    $builder = $this->db->table("cmptxnmstn");
    $builder->where('vch_txn_id', $voucher_txn_id);
    $builder->where('cmp_id', $sel_compId);
    $builder->whereIn('master_id_type', ['itm']);
    $builder->orderBy('txn_id', 'asc');
    $comp_txns = $builder->get()->getResultArray();

    $result = [];

    foreach ($comp_txns as $key => $value) {

        $builder = $this->db->table('itemtxnmst t');
        $builder->select("
            t.*,
            unt.itm_unit_name,
            m.itm_id,
            m.itm_name,
            acd.itm_hsn,
            acd.itm_sales_acc_id,
            acd.itm_pur_acc_id,
            m.tax_cat_mst_id,
            crsmt.under_crs_mst_id AS acc_grp_id
        ", false);

        /*
        |--------------------------------------------------------------------------
        | Safe item_id extraction
        |--------------------------------------------------------------------------
        */
        $builder->join(
            'itemmaster m',
            "m.itm_id = CAST(NULLIF(split_part(t.itm_id_unit_id, '_', 1), '') AS INTEGER)",
            'left',
            false
        );

        $builder->join(
            'itmmstdetn acd',
            'acd.itm_id = m.itm_id',
            'left'
        );

        $builder->join(
            'undercrsmt crsmt',
            'crsmt.crs_mst_id = m.itm_id AND crsmt.crs_mst_type = 3',
            'left'
        );

        /*
        |--------------------------------------------------------------------------
        | Safe unit_id extraction
        |--------------------------------------------------------------------------
        */
        $builder->join(
            'itmunitmst unt',
            "unt.itm_unit_id = CAST(
                NULLIF(
                    split_part(
                        t.itm_id_unit_id,
                        '_',
                        array_length(string_to_array(t.itm_id_unit_id, '_'), 1)
                    ),
                    ''
                ) AS INTEGER
            )",
            'left',
            false
        );

        $builder->where('t.vch_txn_id', $voucher_txn_id);
        $builder->where('t.hobo_id', $sel_boId);

        // Safe master_id comparison
        $builder->where(
            "CAST(NULLIF(split_part(t.itm_id_unit_id, '_', 1), '') AS INTEGER) = " . (int)$value['master_id'],
            null,
            false
        );

        $builder->where('t.txn_id', $value['txn_id']);
        $builder->where('t.itm_txn_type', 1);

        // Optional extra protection: only valid numeric item_id_unit_id
        $builder->where("t.itm_id_unit_id ~ '^[0-9]+(_[0-9]+)+$'", null, false);

        $data = $builder->get()->getRowArray();

        if ($data) {

            if ($compId != null) {
                $tax_cat_mst_id = $data['tax_cat_mst_id'];
                $tax_info       = $this->getTaxRatesByCategory($tax_cat_mst_id, $sel_compId);

                if ($tax_info) {
                    $tax_details = [
                        'igst'   => $tax_info['igst'] ?? 0,
                        'cgst'   => $tax_info['cgst'] ?? 0,
                        'sgst'   => $tax_info['sgst'] ?? 0,
                        'ut_tax' => $tax_info['utgst'] ?? 0,
                        'cess'   => $tax_info['cess'] ?? 0,
                    ];
                    $igst = $tax_info['igst'] ?? 0;
                } else {
                    $tax_details = [
                        'igst'   => 0,
                        'cgst'   => 0,
                        'sgst'   => 0,
                        'ut_tax' => 0,
                        'cess'   => 0
                    ];
                    $igst = 0;
                }

            } else {

                $buildert = $this->db->table("vchgstsumn t");
                $buildert->where('t.vch_txn_id', $voucher_txn_id);
                $buildert->where('t.txn_id', $value['txn_id']);
                $buildert->where('t.acc_bsd_id', $data['itm_id']);
                $buildert->where('t.cmp_id', $sel_compId);
                $buildert->where('t.acc_bsd_type', 3);
                $taxsummry_row = $buildert->get()->getRowArray();

                $igst = 0;

                if ($taxsummry_row) {
                    if ($taxsummry_row['vch_igst_rate'] == 0) {
                        $igst = ($taxsummry_row['vch_cgst_rate'] + $taxsummry_row['vch_sgst_ugst_rate']);
                    } else {
                        $igst = $taxsummry_row['vch_igst_rate'];
                    }

                    $tax_details = [
                        'igst'   => $igst,
                        'cgst'   => $taxsummry_row['vch_cgst_rate'],
                        'sgst'   => $taxsummry_row['vch_sgst_ugst_rate'],
                        'ut_tax' => $taxsummry_row['vch_sgst_ugst_rate'],
                        'cess'   => $taxsummry_row['vch_cess_rate']
                    ];
                } else {
                    $tax_cat_mst_id = $data['tax_cat_mst_id'];
                    $tax_info       = $this->getTaxRatesByCategory($tax_cat_mst_id, $sel_compId);

                    if ($tax_info) {
                        $tax_details = [
                            'igst'   => $tax_info['igst'] ?? 0,
                            'cgst'   => $tax_info['cgst'] ?? 0,
                            'sgst'   => $tax_info['sgst'] ?? 0,
                            'ut_tax' => $tax_info['utgst'] ?? 0,
                            'cess'   => $tax_info['cess'] ?? 0,
                        ];
                        $igst = $tax_info['igst'] ?? 0;
                    } else {
                        $tax_details = [
                            'igst'   => 0,
                            'cgst'   => 0,
                            'sgst'   => 0,
                            'ut_tax' => 0,
                            'cess'   => 0
                        ];
                        $igst = 0;
                    }
                }
            }

            $item_unit_id_info = explode("_", (string)$data['itm_id_unit_id']);
            $item_unit_id      = $item_unit_id_info[1] ?? '';

            $acc_description = $this->get_short_narration_info($voucher_txn_id, $value['txn_id'], $sel_compId);
            $itemPrice       = parseAmountPrice(($data['itm_txn_amt'] / (($data['itm_txn_qty'] > 0) ? $data['itm_txn_qty'] : 1)), 4);

            $result[] = [
                'item_id'         => $data['itm_id'],
                'item_hsn'        => $data['itm_hsn'],
                'tax_cat_id'      => $data['tax_cat_mst_id'],
                'item_name'       => $data['itm_name'],
                'item_unit_name'  => $data['itm_unit_name'],
                'item_unit_id'    => $item_unit_id,
                'item_qty'        => floatval($data['itm_txn_qty']),
                'item_txn_rate'   => floatval($data['itm_txn_rate']),
                'item_txn_drcr'   => ($data['itm_txn_dr_cr'] == 2) ? 'C' : 'D',
                'item_amount'     => parseAmount($data['itm_txn_amt']),
				'item_amountfc'     => parseAmount($data['itm_txn_fcy']),
                'description'     => '',
                'item_price'      => $data['itm_txn_rate'],
                'txn_id'          => $value['txn_id'],
                'is_bsd'          => 0,
                'is_item'         => 1,
                'item_mrp'        => 0,
                'item_sales_acc'  => $data['itm_sales_acc_id'],
                'item_pur_acc'    => $data['itm_pur_acc_id'],
                'tax_details'     => $tax_details,
                'igst_rate'       => $igst
            ];
        }
    }

    return $result;
}


  public function get_journal_account_transactions($voucher_txn_id)
{
    $bbb_groups = $this->get_bbb_groups_list();
    $cc_groups  = $this->get_cc_groups_list();

    /*
    |--------------------------------------------------------------------------
    | STEP 1: Get mapped item accounts (PostgreSQL compatible)
    |--------------------------------------------------------------------------
    */
    $uniqueItems = $this->db->table('itemtxnmst t')
    ->select("
        DISTINCT t.itm_txn_dr_cr,
        m.itm_sales_acc_id,
        m.itm_pur_acc_id
    ", false) // ✅ VERY IMPORTANT
    ->join(
        'itmmstdetn m',
        "m.itm_id = split_part(t.itm_id_unit_id, '_', 1)::int",
        'left'
    )
    ->where('t.vch_txn_id', $voucher_txn_id)
    ->where('t.cmp_id', $this->company_id)
    ->get()
    ->getResultArray();

    /*
    |--------------------------------------------------------------------------
    | STEP 2: Prepare mapping arrays (optimized)
    |--------------------------------------------------------------------------
    */
    $l_map_accounts = [];
    $r_map_accounts = [];

    foreach ($uniqueItems as $row) {

        if ($row['itm_txn_dr_cr'] == 1) {
            if (!empty($row['itm_sales_acc_id'])) {
                $l_map_accounts[] = $row['itm_sales_acc_id'];
            }
            if (!empty($row['itm_pur_acc_id'])) {
                $l_map_accounts[] = $row['itm_pur_acc_id'];
            }
        }

        if ($row['itm_txn_dr_cr'] == 2) {
            if (!empty($row['itm_sales_acc_id'])) {
                $r_map_accounts[] = $row['itm_sales_acc_id'];
            }
            if (!empty($row['itm_pur_acc_id'])) {
                $r_map_accounts[] = $row['itm_pur_acc_id'];
            }
        }
    }

    // Remove duplicates
    $l_map_accounts = array_unique($l_map_accounts);
    $r_map_accounts = array_unique($r_map_accounts);

    /*
    |--------------------------------------------------------------------------
    | STEP 3: Fetch account transactions
    |--------------------------------------------------------------------------
    */
    $acc_txns = $this->db->table("accttxnmst t")
        ->select("
            t.acc_id,
            t.acc_txn_dr_cr,
            t.acc_txn_amt,
            m.acc_name,
            m.tax_cat_mst_id,
            crsmt.under_crs_mst_id AS acc_grp_id,
            crsmt.crs_mst_parent_id AS acc_grp_parent_id
        ")
        ->join("acctmaster m", "m.acc_id = t.acc_id", "left")
        ->join(
            "undercrsmt crsmt",
            "crsmt.crs_mst_id = m.acc_id AND crsmt.crs_mst_type = 1",
            "left"
        )
        ->where([
            't.vch_txn_id' => $voucher_txn_id,
            't.hobo_id'    => $this->bo_id,
            't.cmp_id'     => $this->company_id,
            't.acc_txn_type' => 1
        ])
        ->get()
        ->getResultArray();

    /*
    |--------------------------------------------------------------------------
    | STEP 4: Process result (clean + optimized)
    |--------------------------------------------------------------------------
    */
    $l_accounts = [];
    $r_accounts = [];

    foreach ($acc_txns as $row) {

        $is_bbb = (
            in_array($row['acc_grp_id'], $bbb_groups) ||
            in_array($row['acc_grp_parent_id'], [16, 22, 4, 5])
        ) ? 1 : 0;

        $is_cc = (
            in_array($row['acc_grp_id'], $cc_groups) ||
            in_array($row['acc_grp_parent_id'], [7, 11, 13])
        ) ? 1 : 0;

        $accountData = [
            'acc_id'     => $row['acc_id'],
            'acc_name'   => $row['acc_name'],
            'acc_type'   => 'acc',
            'acc_amount' => $row['acc_txn_amt'],
            'is_cc'      => $is_cc,
            'is_bbb'     => $is_bbb,
        ];

        // DR
        if ($row['acc_txn_dr_cr'] == 1) {
            if (!in_array($row['acc_id'], $l_map_accounts)) {
                $l_accounts[] = $accountData;
            }
        }

        // CR
        if ($row['acc_txn_dr_cr'] == 2) {
            if (!in_array($row['acc_id'], $r_map_accounts)) {
                $r_accounts[] = $accountData;
            }
        }
    }

    return [
        'l_accounts' => $l_accounts,
        'r_accounts' => $r_accounts,
    ];
}
   
   public function get_journal_item_transactions($voucher_txn_id)
{
    /*
    |--------------------------------------------------------------------------
    | STEP 1: Fetch item transactions (PostgreSQL compatible)
    |--------------------------------------------------------------------------
    */
    $item_txns = $this->db->table("itemtxnmst t")
        ->select("
            t.itm_id_unit_id,
            t.itm_txn_dr_cr,
            t.itm_txn_qty,
            t.itm_txn_amt,
            t.itm_txn_rate,

            split_part(t.itm_id_unit_id, '_', 1)::int AS item_id,
            split_part(t.itm_id_unit_id, '_', 2)::int AS item_unit_id,

            m.itm_name,
            m.tax_cat_mst_id,

            unt.itm_unit_name,

            acd.itm_pur_acc_id,
            acd.itm_sales_acc_id
        ")
        ->join(
            "itemmaster m",
            "m.itm_id = split_part(t.itm_id_unit_id, '_', 1)::int",
            "left"
        )
        ->join("itmmstdetn acd", "acd.itm_id = m.itm_id", "left")
        ->join(
            "itmunitmst unt",
            "unt.itm_unit_id = split_part(t.itm_id_unit_id, '_', 2)::int",
            "left"
        )
        ->where([
            't.vch_txn_id' => $voucher_txn_id,
            't.cmp_id'     => $this->company_id,
            't.hobo_id'    => $this->bo_id,
            't.itm_txn_type' => 1
        ])
        ->get()
        ->getResultArray();

    /*
    |--------------------------------------------------------------------------
    | STEP 2: Process result (optimized)
    |--------------------------------------------------------------------------
    */
    $l_items = [];
    $r_items = [];

    foreach ($item_txns as $row) {

        $itemData = [
            'item_id'         => $row['item_id'],
            'item_name'       => $row['itm_name'],
            'item_unit_name'  => $row['itm_unit_name'],
            'item_unit_id'    => $row['item_unit_id'],
            'item_qty'        => (float) $row['itm_txn_qty'],
            'item_txn_drcr'   => $row['itm_txn_dr_cr'],
            'item_amount'     => parseAmount($row['itm_txn_amt']),
            'description'     => "",
            'item_pur_acc'    => $row['itm_pur_acc_id'],
            'item_sales_acc'  => $row['itm_sales_acc_id'],
            'item_price'      => parseAmountPrice($row['itm_txn_rate'], 4),
            'tax_cat_id'      => $row['tax_cat_mst_id'],
        ];

        // DR
        if ($row['itm_txn_dr_cr'] == 1) {
            $l_items[] = $itemData;
        }

        // CR
        if ($row['itm_txn_dr_cr'] == 2) {
            $r_items[] = $itemData;
        }
    }

    return [
        'l_items' => $l_items,
        'r_items' => $r_items,
    ];
}


  public function grid_gstpaid_account_transactions($voucher_txn_id){
        $bbb_groups  = $this->get_bbb_groups_list();
		$cc_groups   = $this->get_cc_groups_list();
		$cash_groups = $this->get_cash_groups();
    	$builder = $this->db->table("cmptxnmstn");
    	$builder->where('vch_txn_id', $voucher_txn_id);
		$builder->where('cmp_id', $this->company_id);
    	$builder->whereIn('master_id_type', ['acc','tax']);
    	$builder->orderBy('txn_id', 'asc');
    	$comp_txns = $builder->get()->getResultArray();
		$result = [];
    	foreach ($comp_txns as $key => $value) {			
    		
    			$builder = $this->db->table("accttxnmst t");
				$builder->select("t.*, m.acc_name,acd.acc_gstin,acd.acc_sac, m.acc_alias,m.tax_cat_mst_id, m.acc_print_name, crsmt.under_crs_mst_id AS acc_grp_id, crsmt.crs_mst_parent_id AS acc_grp_parent_id"); // select desired fields from both tables
				$builder->join("acctmaster m", "m.acc_id = t.acc_id", "left");
				$builder->join("acctmstdet acd", "acd.acc_id = m.acc_id", "left");
				$builder->join("undercrsmt crsmt", "crsmt.crs_mst_id = m.acc_id AND crsmt.crs_mst_type =1", 'left');
				$builder->where('t.vch_txn_id', $voucher_txn_id);
				$builder->where('t.hobo_id', $this->session->get('ses_boid'));
				$builder->where('t.acc_id', $value['master_id']);
				$builder->where('t.txn_id', $value['txn_id']);
				$builder->where('t.acc_txn_type',1 );
				$data = $builder->get()->getRowArray();
				
		    	if($data){
	    			$is_bbb = 0;
					if(in_array($data['acc_grp_id'], $bbb_groups) || in_array($data['acc_grp_parent_id'], [16,22,4,5] )){
						$is_bbb = 1;
					}
					
				   $is_cc = 0;
					if(in_array($data['acc_grp_id'], $cc_groups) || in_array($data['acc_grp_parent_id'], [7,11,13])){
						$is_cc = 1;
					}
				
					$is_cash = 0;
					if(in_array($data['acc_grp_id'], $cash_groups)){
						$is_cash = 1;
					}	      	                
					
						/***********  tax summary *************/
				 
				$buildert = $this->db->table("vchgstsumn t");
				$buildert->where('t.vch_txn_id', $voucher_txn_id);				
				$buildert->where('t.acc_bsd_id', $value['master_id']);
				$buildert->where('t.cmp_id', $this->company_id);
				$buildert->where('t.acc_bsd_type',1);
				$taxsummry_row = $buildert->get()->getRowArray();
				
			    $igst=0;
				if($taxsummry_row){
				    if($taxsummry_row['vch_igst_rate']==0){
				      $igst =   ($taxsummry_row['vch_cgst_rate']+$taxsummry_row['vch_sgst_ugst_rate']);
				    }else{
				      $igst =   ($taxsummry_row['vch_igst_rate']);  
				    }
				    
				    
			       $tax_details=[
				                        'igst'=>$igst,
				                        'cgst'=>$taxsummry_row['vch_cgst_rate'],
				                        'sgst'=>$taxsummry_row['vch_sgst_ugst_rate'],
				                        'ut_tax'=>$taxsummry_row['vch_sgst_ugst_rate'],
				                        'cess'=>$taxsummry_row['vch_cess_rate']
				                  ];
				}
				else{
				    $tax_details=[
				                        'igst'=>0,
				                        'cgst'=>0,
				                        'sgst'=>0,
				                        'ut_tax'=>0,
				                        'cess'=>0
				                  ];
				}
				
				/**************  end tax summary  **************/
				
				$memo_amount = $this->get_memoamount_sale_txn($voucher_txn_id,$value['txn_id'],'acc',$data['acc_id']);
		        $taxinc_amount  = $this->get_taxamount_sale_txn($voucher_txn_id,'acc',$value['txn_id']);
					
					
					$amount=0;
					$amountfcy =0;
			       	$acc_description = $this->get_short_narration_info($voucher_txn_id,$value['txn_id']);
					if($data['acc_txn_dr_cr'] == 2){
	                    $credit   = parseAmount($data["acc_txn_amt"]);
						$creditfc = parseAmount($data["acc_txn_fcy"]);
						$debit    = 0;
						$debitfc  = '';
						$acc_txn_drcr ='C';
						$amount       = $credit;
						$amountfcy    = $creditfc;
	                }
	                else{
	                    $debit    = parseAmount($data["acc_txn_amt"]);
						$debitfc  = parseAmount($data["acc_txn_fcy"]);
						$credit   = 0;
						$creditfc = '';
						$acc_txn_drcr ='D';
						
						$amount       = $debit;
						$amountfcy    = $debitfc;
						
	                 }				
					if($credit >0 || $debit>0){		
		    		$result[] = [
		    				'account_id' 		=> $data['acc_id'],
							'acc_id' 		    => $data['acc_id'],
							'account_name' 		=> $data['acc_name'],
							'acc_name' 		    => $data['acc_name'],
							'acc_type' 			=> 'acc',
							'description' 		=> $acc_description,
							'credit' 			=> $credit,
							'debit' 			=> $debit,
							'creditfc' 			=> $creditfc,
							'debitfc' 			=> $debitfc,
							'drcr'				=> $acc_txn_drcr,
							'is_cc'				=> $is_cc,
							'is_bbb'			=> $is_bbb,
							'is_cash'	  		=> $is_cash,
							'amount'            => $data['acc_txn_amt'],
							'amountfc'          => $data['acc_txn_fcy'],
							'cannotselected'    => '',
							'tax_details'       => $tax_details,
							'vch_txn_type'      => $data['acc_txn_type'],
							'tax_cat_id'        => $data['tax_cat_mst_id'],
							'memo_amt'          => $memo_amount,
							'memo_amount'       => $memo_amount,// use when resave voucher 
					     	'amounttxs'         => $taxinc_amount,
							'txinc_amount'      => $taxinc_amount, // use when resave voucher 
					     	'item_hsn_sac'      => $data['acc_sac'],
							'igst_rate'         => $igst
		    			];	
					}
			     } 
    		
    	}
    	return $result;
  }
   
 public function get_original_sale_invoice_details(int $sale_voucher_txn_id, $compId = null): array
{
    $sel_compId = $compId ?: $this->company_id;

    // 1. Original Invoice No. from gstroutsup
    $gstroutsup = $this->db->table('gstroutsup')
        ->select('outsup_bill_ref_no')
        ->where('vch_txn_id', $sale_voucher_txn_id)
        ->where('cmp_id', $sel_compId)
        ->get()
        ->getRowArray();

    $original_inv_no = $gstroutsup['outsup_bill_ref_no'] ?? '';

    // 2. Original Invoice Date from vchtxnconso
    $vchtxnconso = $this->db->table('vchtxnconso')
        ->select('vch_date')
        ->where('vch_txn_id', $sale_voucher_txn_id)
        ->where('cmp_id', $sel_compId)
        ->get()
        ->getRowArray();

    $original_inv_date = '';
    if (!empty($vchtxnconso['vch_date'])) {
        $original_inv_date = date('d-m-Y', strtotime($vchtxnconso['vch_date']));
    }

    // 3. Original Invoice Value = SUM(vch_taxable_value + vch_total_tax) from vchgstsumn
    $vchgstsumn = $this->db->table('vchgstsumn')
        ->selectSum('vch_taxable_value', 'total_taxable')
        ->selectSum('vch_total_tax', 'total_tax')
        ->where('vch_txn_id', $sale_voucher_txn_id)
        ->where('cmp_id', $sel_compId)
        ->get()
        ->getRowArray();

    $original_inv_value = 0;
    if ($vchgstsumn) {
        $original_inv_value = (float)($vchgstsumn['total_taxable'] ?? 0)
                            + (float)($vchgstsumn['total_tax']     ?? 0);
    }

    return [
        'original_inv_no'    => $original_inv_no,
        'original_inv_date'  => $original_inv_date,
        'original_inv_value' => $original_inv_value,
    ];
}

   
  public function grid_account_transactions($voucher_txn_id,$compId=null,$boId=null){
        if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
		if($boId)
			$sel_boId = $boId;
		 else 
			$sel_boId = $this->bo_id;

		$bbb_groups  = $this->get_bbb_groups_list($compId);
		$cc_groups   = $this->get_cc_groups_list($compId);
		$cash_groups = $this->get_cash_groups($compId);
    	$builder = $this->db->table("cmptxnmstn");
    	$builder->where('vch_txn_id', $voucher_txn_id);
		$builder->where('cmp_id', $sel_compId);
    	$builder->whereIn('master_id_type', ['acc']);
    	$builder->orderBy('txn_id', 'asc');
    	$comp_txns = $builder->get()->getResultArray();
		$result = [];
    	foreach ($comp_txns as $key => $value) {			
    		if($key >0)
    		{
    			$builder = $this->db->table("accttxnmst t");
				$builder->select("t.*, m.acc_name,acd.acc_gstin,acd.acc_sac, m.acc_alias,m.tax_cat_mst_id, m.acc_print_name, crsmt.under_crs_mst_id AS acc_grp_id, crsmt.crs_mst_parent_id AS acc_grp_parent_id"); // select desired fields from both tables
				$builder->join("acctmaster m", "m.acc_id = t.acc_id", "left");
				$builder->join("acctmstdet acd", "acd.acc_id = m.acc_id", "left");
				$builder->join("undercrsmt crsmt", "crsmt.crs_mst_id = m.acc_id AND crsmt.crs_mst_type =1", 'left');
				$builder->where('t.vch_txn_id', $voucher_txn_id);
				$builder->where('t.hobo_id', $sel_boId);
				$builder->where('t.acc_id', $value['master_id']);
				$builder->where('t.txn_id', $value['txn_id']);
				$builder->where('t.acc_txn_type',1 );
				$data = $builder->get()->getRowArray();
				
		    	if($data){
	    			$is_bbb = 0;
					if(in_array($data['acc_grp_id'], $bbb_groups) || in_array($data['acc_grp_parent_id'], [16,22,4,5] )){
						$is_bbb = 1;
					}
					
				   $is_cc = 0;
					if(in_array($data['acc_grp_id'], $cc_groups) || in_array($data['acc_grp_parent_id'], [7,11,13])){
						$is_cc = 1;
					}
				
					$is_cash = 0;
					if(in_array($data['acc_grp_id'], $cash_groups)){
						$is_cash = 1;
					}	      	                
				
						/***********  tax summary *************/
				if($compId!=null){ // when Voucher Resave is called this function 
					$tax_cat_mst_id = $data['tax_cat_mst_id'];
					$tax_info       = $this->getTaxRatesByCategory($tax_cat_mst_id,$sel_compId);
				    if($tax_info){
					 $tax_details=[
				                        'igst'=>$tax_info['igst'] ?? 0,
				                        'cgst'=>$tax_info['cgst'] ?? 0,
				                        'sgst'=>$tax_info['sgst'] ?? 0,
				                        'ut_tax'=>$tax_info['utgst'] ?? 0,
				                        'cess'=>$tax_info['cess'] ?? 0,
				                  ];
					$igst	= $tax_info['igst'] ?? 0;			
					}else{
					$tax_details=[
				                        'igst'=>0,
				                        'cgst'=>0,
				                        'sgst'=>0,
				                        'ut_tax'=>0,
				                        'cess'=>0
				                  ];
					$igst	=  0;			  
					}
					
					
				}
				else{
				$buildert = $this->db->table("vchgstsumn t");
				$buildert->where('t.vch_txn_id', $voucher_txn_id);				
				$buildert->where('t.acc_bsd_id', $value['master_id']);
				$buildert->where('t.cmp_id',$sel_compId);
				$buildert->where('t.acc_bsd_type',1);
				$taxsummry_row = $buildert->get()->getRowArray();
				
			    $igst=0;
				if($taxsummry_row){
				    if($taxsummry_row['vch_igst_rate']==0){
				      $igst =   ($taxsummry_row['vch_cgst_rate']+$taxsummry_row['vch_sgst_ugst_rate']);
				    }else{
				      $igst =   ($taxsummry_row['vch_igst_rate']);  
				    }
				    
				    
			       $tax_details=[
				                        'igst'=>$igst,
				                        'cgst'=>$taxsummry_row['vch_cgst_rate'],
				                        'sgst'=>$taxsummry_row['vch_sgst_ugst_rate'],
				                        'ut_tax'=>$taxsummry_row['vch_sgst_ugst_rate'],
				                        'cess'=>$taxsummry_row['vch_cess_rate']
				                  ];
				}
					else{
						$tax_details=[
											'igst'=>0,
											'cgst'=>0,
											'sgst'=>0,
											'ut_tax'=>0,
											'cess'=>0
									  ];
					}
				}
				
				/**************  end tax summary  **************/
				
				$memo_amount = $this->get_memoamount_sale_txn($voucher_txn_id,$value['txn_id'],'acc',$data['acc_id'],$sel_compId,$sel_boId);
		        $taxinc_amount  = $this->get_taxamount_sale_txn($voucher_txn_id,'acc',$value['txn_id'],$sel_compId);
					
					
					$amount=0;
					$amountfcy =0;
			       	$acc_description = $this->get_short_narration_info($voucher_txn_id,$value['txn_id'],$sel_compId);
					if($data['acc_txn_dr_cr'] == 2){
	                    $credit   = parseAmount($data["acc_txn_amt"]);
						$creditfc = parseAmount($data["acc_txn_fcy"]);
						$debit    = 0;
						$debitfc  = '';
						$acc_txn_drcr ='C';
						$amount       = $credit;
						$amountfcy    = $creditfc;
	                }
	                else{
	                    $debit    = parseAmount($data["acc_txn_amt"]);
						$debitfc  = parseAmount($data["acc_txn_fcy"]);
						$credit   = 0;
						$creditfc = '';
						$acc_txn_drcr ='D';
						
						$amount       = $debit;
						$amountfcy    = $debitfc;
						
	                 }					
		    		$result[] = [
		    				'account_id' 		=> $data['acc_id'],
							'acc_id' 		    => $data['acc_id'],
							'account_name' 		=> $data['acc_name'],
							'acc_name' 		    => $data['acc_name'],
							'acc_type' 			=> 'acc',
							'description' 		=> $acc_description,
							'credit' 			=> $credit,
							'debit' 			=> $debit,
							'creditfc' 			=> $creditfc,
							'debitfc' 			=> $debitfc,
							'drcr'				=> $acc_txn_drcr,
							'is_cc'				=> $is_cc,
							'is_bbb'			=> $is_bbb,
							'is_cash'	  		=> $is_cash,
							'amount'            => $data['acc_txn_amt'],
							'amountfc'          => $data['acc_txn_fcy'],
							'cannotselected'    => '',
							'tax_details'       => $tax_details,
							'vch_txn_type'      => $data['acc_txn_type'],
							'tax_cat_id'        => $data['tax_cat_mst_id'],
							'memo_amt'          => $memo_amount,
							'memo_amount'       => $memo_amount,// use when resave voucher 
					     	'amounttxs'         => $taxinc_amount,
							'txinc_amount'      => $taxinc_amount, // use when resave voucher 
					     	'item_hsn_sac'      => $data['acc_sac'],
							'txn_id'      => $data['txn_id'],
							'igst_rate'         => $igst
		    			];	
			     } 
    		}
    	}
    	return $result;
  } 
  
  public function bsdconfign_info($bsd_id,$compId=null){
	   if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
      	$builder = $this->db->table("bsdconfign");
      	$builder->select('bsd_base,bsd_taxable_type,bsd_hsn_sac');
      	$builder->where("cmp_id",$sel_compId);
      	$builder->where("bsd_id",$bsd_id);
      	$response = $builder->get()->getRowArray();
      	return $response;
  }
  
  public function grid_bsd_transactions($voucher_txn_id,$compId=null,$boId=null){
	   if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
		if($boId)
			$sel_boId = $boId;
		 else 
			$sel_boId = $this->bo_id;
		
        $bbb_groups  = $this->get_bbb_groups_list($sel_compId);
		$cc_groups   = $this->get_cc_groups_list($sel_compId);
		$cash_groups = $this->get_cash_groups($sel_compId);
    	$builder = $this->db->table("cmptxnmstn");
    	$builder->where('vch_txn_id', $voucher_txn_id);
		$builder->where('cmp_id', $sel_compId);
    	$builder->whereIn('master_id_type', ['bsd']);
    	$builder->orderBy('txn_id', 'asc');
    	$comp_txns = $builder->get()->getResultArray();
		
    	$result = [];
    	foreach ($comp_txns as $key => $value) {
			  	$builder = $this->db->table("accttxnmst t");
				$builder->select("t.*, m.acc_name, m.acc_alias,m.tax_cat_mst_id, m.acc_print_name, crsmt.under_crs_mst_id AS acc_grp_id, crsmt.crs_mst_parent_id AS acc_grp_parent_id,bsd.bsd_type,bsd.bsd_nature,bsd.bsd_input_output,bsd.tax_cat_type,bsd.tax_cat_sub_type"); // select desired fields from both tables
				$builder->join("acctmaster m", "m.acc_id = t.acc_id", "left");
				$builder->join("billsundry bsd", "bsd.bsd_id = m.bsd_id", "left");
				$builder->join("undercrsmt crsmt", "crsmt.crs_mst_id = m.acc_id AND crsmt.crs_mst_type =1", 'left');
				$builder->where('t.vch_txn_id', $voucher_txn_id);
				$builder->where('t.hobo_id', $sel_boId);
				$builder->where('t.acc_id', $value['master_id']);
				$builder->where('m.bsd_id IS NOT NULL');
				$builder->where('bsd.bsd_type',0);
				$builder->where('t.cmp_id', $sel_compId);
				$builder->where('t.txn_id', $value['txn_id']);
				$builder->where('t.acc_txn_type',1 );
				$data = $builder->get()->getRowArray();
				
			   /***********  tax summary *************/
			   if($compId!=null){ // when Voucher Resave is called this function
					 // check bsd is taxable or non taxable
					// if tax account than no tax category will be there 
					$tax_cat_mst_id = $data['tax_cat_mst_id'] ?? 0;
					$tax_info       = $this->getTaxRatesByCategory($tax_cat_mst_id,$sel_compId);
				    if($tax_info){
					 $tax_details=[
				                        'igst'=>$tax_info['igst'] ?? 0,
				                        'cgst'=>$tax_info['cgst'] ?? 0,
				                        'sgst'=>$tax_info['sgst'] ?? 0,
				                        'ut_tax'=>$tax_info['utgst'] ?? 0,
				                        'cess'=>$tax_info['cess'] ?? 0,
				                  ];
					$igst	= $tax_info['igst'] ?? 0;			
					}else{
					$tax_details=[
				                        'igst'=>0,
				                        'cgst'=>0,
				                        'sgst'=>0,
				                        'ut_tax'=>0,
				                        'cess'=>0
				                  ];
					$igst	=  0;			  
					}
					
					
				}
				else{
			     $igst =0;
				 $tax_details=[
				                        'igst'=>0,
				                        'cgst'=>0,
				                        'sgst'=>0,
				                        'ut_tax'=>0,
				                        'cess'=>0
				                  ];
				$buildert = $this->db->table("vchgstsumn t");
				$buildert->where('t.vch_txn_id', $voucher_txn_id);
				$buildert->where('t.txn_id', $value['txn_id']);
				$buildert->where('t.acc_bsd_id', $value['master_id']);
				$buildert->where('t.cmp_id', $sel_compId);
				$buildert->where('t.acc_bsd_type',2);
				$taxsummry_row = $buildert->get()->getRowArray();
				if($taxsummry_row){
				  if($taxsummry_row['vch_cgst_rate']==0 && 	$taxsummry_row['vch_sgst_ugst_rate']==0)
					$igst =  $taxsummry_row['vch_igst_rate'];
				   else
				   $igst = ($taxsummry_row['vch_cgst_rate']+$taxsummry_row['vch_sgst_ugst_rate']);
			    $tax_details=[
				                        'igst'=>$igst,
				                        'cgst'=>$taxsummry_row['vch_cgst_rate'],
				                        'sgst'=>$taxsummry_row['vch_sgst_ugst_rate'],
				                        'ut_tax'=>$taxsummry_row['vch_sgst_ugst_rate'],
				                        'cess'=>$taxsummry_row['vch_cess_rate']
				                  ];
				}
				}
				/**************  end tax summary  **************/
				if($data){
	    			$is_bbb = 0;
					if(in_array($data['acc_grp_id'], $bbb_groups) || in_array($data['acc_grp_parent_id'], [16,22,4,5] )){
						$is_bbb = 1;
					}
					
				   $is_cc = 0;
					if(in_array($data['acc_grp_id'], $cc_groups) || in_array($data['acc_grp_parent_id'], [7,11,13])){
						$is_cc = 1;
					}
				
					$is_cash = 0;
					if(in_array($data['acc_grp_id'], $cash_groups)){
						$is_cash = 1;
					}	      	                
					
					$bsdconfign_info   = $this->bsdconfign_info($data['acc_id'],$compId);
					if($bsdconfign_info){
					    $bl_hsn_sac = $bsdconfign_info['bsd_hsn_sac'];
					    $bl_ipt_ott = $bsdconfign_info['bl_ipt_ott'];
					    $bl_nature  = $bsdconfign_info['bl_nature'];
					}else{
					    $bl_hsn_sac ='';
					    $bl_ipt_ott = '';
					    $bl_nature  = '';
					}
					
					$amount=0;
					$amountfcy =0;
			       	$acc_description = $this->get_short_narration_info($voucher_txn_id,$value['txn_id'],$compId);
					if($data['acc_txn_dr_cr'] == 2){
	                    $credit   = parseAmount($data["acc_txn_amt"]);
						$creditfc = parseAmount($data["acc_txn_fcy"]);
						$debit    = 0;
						$debitfc  = '';
						$acc_txn_drcr ='C';
						$amount       = $credit;
						$amountfcy    = $creditfc;
	                }
	                else{
	                    $debit    = parseAmount($data["acc_txn_amt"]);
						$debitfc  = parseAmount($data["acc_txn_fcy"]);
						$credit   = 0;
						$creditfc = '';
						$acc_txn_drcr ='D';
						
						$amount       = $debit;
						$amountfcy    = $debitfc;
						
	                 }	
	                $memo_amount = $this->get_memoamount_sale_txn($voucher_txn_id,$value['txn_id'],'acc',$data['acc_id'],$sel_compId,$sel_boId);
		           
		    		$result[] = [
		    				'billsundry_id' 	=> $data['acc_id'],
							'id' 		        => $data['acc_id'],
							'billsundry_name' 	=> $data['acc_name'],
							'label' 		    => $data['acc_name'],
							'acc_type' 			=> 'bsd',
							'description' 		=> $acc_description,
							'credit' 			=> $credit,
							'debit' 			=> $debit,
							'creditfc' 			=> $creditfc,
							'debitfc' 			=> $debitfc,
							'drcr'				=> $acc_txn_drcr,
							'is_cc'				=> $is_cc,
							'is_bbb'			=> $is_bbb, 
							'is_cash'	  		=> $is_cash,
							'billsundry_amount' => $amount,
							'billsundry_fcy_amount'          => $amountfcy,
							'billsundry_amountfc'   => $amountfcy,
							'billsundry_memoamnt' => $memo_amount,
							'memo_amount'       => $memo_amount, // use for resave voucher 
							'cannotselected'    => '',
							'tax_details'       => $tax_details,
							'bl_hsn_sac'        => $bl_hsn_sac,
							'is_tax_account'    => 0,
							'bl_ipt_ott'        => $bl_ipt_ott,
							'bl_nature'         => $bl_nature,
							'vch_txn_type'      => $data['acc_txn_type'],
							'tax_cat_mst_id'    => $data['tax_cat_mst_id'],
							'tax_catg_id'       => $data['tax_cat_mst_id'],
							'igst_rate'         => $igst,
							"tax_cat_id"        => $data['tax_cat_mst_id']
							
		    			];	
			     } 
    		
    	}
    	return $result;
  } 
  
  public  function get_all_account_transactions($voucher_txn_id) //voucher contra payment receipt journal
    {	
	    $bbb_groups  = $this->get_bbb_groups_list();
		$cc_groups   = $this->get_cc_groups_list();
		$cash_groups = $this->get_cash_groups();
		$subledger =   $this->get_sblgr_txn_data($voucher_txn_id);
    	$builder = $this->db->table("cmptxnmstn");
    	$builder->where('vch_txn_id', $voucher_txn_id);
		$builder->where('cmp_id', $this->company_id);
    	$builder->whereIn('master_id_type', ['acc','bsd']);
    	$builder->orderBy('txn_id', 'asc');
    	$comp_txns = $builder->get()->getResultArray();
		
    	$result = [];
    	foreach ($comp_txns as $key => $value) {
			
    		if($value['master_id_type'] == 'acc')
    		{
    			$builder = $this->db->table("accttxnmst t");
				$builder->select("t.*, m.acc_name, m.acc_alias, m.acc_print_name, crsmt.under_crs_mst_id AS acc_grp_id, crsmt.crs_mst_parent_id AS acc_grp_parent_id"); // select desired fields from both tables
				$builder->join("acctmaster m", "m.acc_id = t.acc_id", "left");
				$builder->join("undercrsmt crsmt", "crsmt.crs_mst_id = m.acc_id AND crsmt.crs_mst_type =1", 'left');
				$builder->where('t.vch_txn_id', $voucher_txn_id);
				$builder->where('t.hobo_id', $this->session->get('ses_boid'));
				$builder->where('t.acc_id', $value['master_id']);
				$builder->where('t.txn_id', $value['txn_id']);
				$data = $builder->get()->getRowArray();
				
		    	if($data){
	    			$is_bbb = 0;
					if(in_array($data['acc_grp_id'], $bbb_groups) || in_array($data['acc_grp_parent_id'], [16,22,4,5] )){
						$is_bbb = 1;
					}
					
				   $is_cc = 0;
					if(in_array($data['acc_grp_id'], $cc_groups) || in_array($data['acc_grp_parent_id'], [7,11,13])){
						$is_cc = 1;
					}
				
					$is_cash = 0;
					if(in_array($data['acc_grp_id'], $cash_groups)){
						$is_cash = 1;
					}	      	   
                    $is_sblgr =0;	
                   if($subledger)					
					$is_sblgr =1;
					$amount=0;
					$amountfcy =0;
			       	$acc_description = $this->get_short_narration_info($voucher_txn_id,$value['txn_id']);
					if($data['acc_txn_dr_cr'] == 2){
	                    $credit   = parseAmount($data["acc_txn_amt"]);
						$creditfc = parseAmount($data["acc_txn_fcy"]);
						$debit    = 0;
						$debitfc  = '';
						$acc_txn_drcr ='C';
						$amount       = $credit;
						$amountfcy    = $creditfc;
	                }
	                else{
	                    $debit    = parseAmount($data["acc_txn_amt"]);
						$debitfc  = parseAmount($data["acc_txn_fcy"]);
						$credit   = 0;
						$creditfc = '';
						$acc_txn_drcr ='D';
						
						$amount       = $debit;
						$amountfcy    = $debitfc;
						
	                 }					
		    		$result[] = [
		    				'account_id' 		=> $data['acc_id'],
							'acc_id' 		    => $data['acc_id'],
							'account_name' 		=> $data['acc_name'],
							'acc_name' 		    => $data['acc_name'],
							'acc_type' 			=> 'acc',
							'description' 		=> $acc_description,
							'credit' 			=> $credit,
							'debit' 			=> $debit,
							'creditfc' 			=> $creditfc,
							'debitfc' 			=> $debitfc,
							'drcr'				=> $acc_txn_drcr,
							'is_cc'				=> $is_cc,
							'is_bbb'			=> $is_bbb,
							'is_cash'	  		=> $is_cash,
							'is_sblgr'          => $is_sblgr,
							'amount'            => $amount,
							'amountfc'          => $amountfcy,
							'cannotselected'    => '',
							'vch_txn_type'      => $data['acc_txn_type'],
							'txn_id'      => $value['txn_id']
		    			];	
			     } 
    		}
    	}
		
    	return $result;
    }
	
  public  function update_comp_txn_data($txn_id,$voucher_txn_id,$acc_id,$data){
	 if($txn_id >0){  
	 $this->db->table("cmptxnmstn")
	     ->where('cmp_id',$this->company_id)
		 ->where('txn_id',$txn_id)->where('master_id',$acc_id)
		 ->where('vch_txn_id',$voucher_txn_id)->update($data);	
	   }
    }	
  public  function update_acc_txn_data($txn_id,$voucher_txn_id,$data){
	 if($txn_id >0){  
	    $this->db->table("accttxnmst")
	     ->where('cmp_id',$this->company_id)
		 ->where('txn_id',$txn_id)
		 ->where('hobo_id',$this->bo_id)
		 ->where('vch_txn_id',$voucher_txn_id)->update($data);	
	    }
    }
	
	public function GetDrftVoucher($draft_vch_rec_id){
		 $postgr_db = $this->externaldb->postgr_db();
		return $postgr_db->table("drftvchrec")->where("draft_vch_rec_id",$draft_vch_rec_id)->get()->getRowArray();
	}
	
	public function clear_voucher_fcyrate($voucher_txn_id){
	 $this->db->table("vchfcyrate")->where("vch_txn_id",$voucher_txn_id)->delete();	
	}
	
	public function RemoveDrftVoucher($draft_vch_rec_id){
		 $postgr_db = $this->externaldb->postgr_db();
		 $postgr_db->table("drftvchrec")->where("draft_vch_rec_id",$draft_vch_rec_id)->delete();
	}
	
	public function UpdateConso($voucher_txn_id,$updatedata){
		$this->db->table("vchtxnconso")->where("vch_txn_id",$voucher_txn_id)->update($updatedata);
	}
	
	function clear_register_txn_data($voucher_txn_id,$acct_vch_type,$compId=null,$boId=null){
		if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		if($boId)
			$sel_boId = $boId;
		 else 
			$sel_boId = $this->bo_id;
		
	    $this->db->table("acctvchreg")
	     ->where('cmp_id',$sel_compId)
		 ->where('hobo_id',$sel_boId)->where('acct_vch_type',$acct_vch_type)
		 ->where('vch_txn_id',$voucher_txn_id)->delete();
        //SaveErrorLog("deleting acctvchreg entries=>".$this->db->getlastquery());		 
    }
	
	public function clear_table_txn_data($voucher_txn_id,$table_name,$compId=null){	   
	    if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		$this->db->table($table_name)
	     ->where('cmp_id',$sel_compId)		
		 ->where('vch_txn_id',$voucher_txn_id)->delete();		   
    }
	public function get_system_journal_vch_info($src_vch_txn_id){
	$data =  $this->db->table("vchbridgen")->where('vch_txn_id_src', $src_vch_txn_id)->where('cmp_id', $this->company_id)->get()->getRowArray();
	   return $data;		
	}
	public function clear_system_journal_bridge_data($voucher_txn_id,$vch_bridge_type){	
      $bridge_info = $this->get_voucher_bridge_info($voucher_txn_id,$vch_bridge_type);	
      if($bridge_info){
		  $vch_txn_id_dest = $bridge_info['vch_txn_id_dest'];
		  $this->db->table("cmptxnmstn")
				->where('cmp_id',$this->company_id)		
				->where('vch_txn_id',$vch_txn_id_dest)->delete();
		  $this->db->table("vchtxnconso")
				->where('cmp_id',$this->company_id)		
				->where('vch_txn_id',$vch_txn_id_dest)->delete();		
		$this->db->table("accttxnmst")
				->where('cmp_id',$this->company_id)		
				->where('vch_txn_id',$vch_txn_id_dest)->delete();
				
		$this->db->table("acctvchreg")
				->where('cmp_id',$this->company_id)		
				->where('vch_txn_id',$vch_txn_id_dest)->delete();	

		$this->db->table("vchbridgen")
				->where('cmp_id',$this->company_id)		
				->where('vch_txn_id_src',$voucher_txn_id)->delete();			
	  }	 	   
    }
	
	public function clear_system_journal_txn_data($voucher_txn_id,$vch_bridge_type,$compId=null){	
	if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
      $bridge_info = $this->get_voucher_bridge_info($voucher_txn_id,$vch_bridge_type,$sel_compId);	
      if($bridge_info){
		  $vch_txn_id_dest = $bridge_info['vch_txn_id_dest'];
		  $this->db->table("cmptxnmstn")
				->where('cmp_id',$sel_compId)		
				->where('vch_txn_id',$vch_txn_id_dest)->delete();		  	
		$this->db->table("accttxnmst")
				->where('cmp_id',$sel_compId)		
				->where('vch_txn_id',$vch_txn_id_dest)->delete();
        $this->db->table("acctvchreg")
				->where('cmp_id',$sel_compId)		
				->where('vch_txn_id',$vch_txn_id_dest)->delete();
				
	  }	 	   
    }
	
	
	public function clear_table_narrations_data($voucher_txn_id,$compId=null){	   
	    if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		
		$this->db->table('vchlongnar')
	    ->where('vch_txn_id',$voucher_txn_id)
		->where('cmp_id',$sel_compId)->delete();

		 $this->db->table('vchshrtnar')
	    ->where('vch_txn_id',$voucher_txn_id)
		->where('cmp_id',$sel_compId)->delete();	
    }	
  public  function comp_voucher_series($comp_id,$voucher_type_id){
	   $data =  $this->db->table("vchseriesn")->where('vch_type_id', $voucher_type_id)->where('cmp_id', $this->company_id)->orderBy('vch_series_name','ASC')->get()->getResultArray();
	   $final_result      = array();
	   $final_result['']  = '';
	   if($data){
		  foreach($data as $row){
              $final_result[$row['vch_series_id']] = $row['vch_series_name'];			   
	        }
        }
	  return $final_result;	
     } 
   
	public  function comp_voucher_result($comp_id,$voucher_type_id){
	   $data =  $this->db->table("vchseriesn")->where('vch_type_id', $voucher_type_id)->where('cmp_id', $this->company_id)->orderBy('vch_series_id','ASC')->get()->getResultArray();
	   return $data;	
     }
	 
	 public  function get_item_details_info($item_id,$compId=null){
		  if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
	   $data =  $this->db->table("itmmstdetn")->where('itm_id', $item_id)->where('cmp_id',$sel_compId)->get()->getRowArray();
	   return $data;	
     }
	 
	 public  function get_bsd_details_info($bsd_id,$compId=null){
		  if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
	   $data =  $this->db->table("bsdconfign")->where('bsd_id', $bsd_id)->where('cmp_id',$sel_compId)->get()->getRowArray();
	   return $data;	
     }
	 
	 public  function get_acc_details_info($acc_id,$compId=null){
	    if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
	 
	   $data =  $this->db->table("acctmstdet")->where('acc_id', $acc_id)->where('cmp_id',$sel_compId)->get()->getRowArray();
	   return $data;	
     }
    
	public function SuppltSubSupplyTypes(){
	  $builder = $this->db->table("invtypemst");
	  $builder->join("invsubtype","invsubtype.inv_type_mst_id=invtypemst.inv_type_mst_id");
	  $builder->select("invsubtype.inv_sub_type_id,invtypemst.inv_type_mst_id,invsubtype.inv_sub_type_name");
	  $response = $builder->get()->getResultArray();
	  $final = [];
	  if($response){
		   foreach($response as $row){
			 $final[$row['inv_type_mst_id']][] = array("id"=>$row["inv_sub_type_id"],"name"=>$row['inv_sub_type_name']);
		   }
	     }
	  return $final;	 
    }	
	
	public function party_dropdown($array = [23,22,16,21]){ // group ids
    	//$final_array     = $this->get_sub_group_ids($array);
    	$bbb_groups      = $this->get_bbb_groups_list();
		$undercrsmt_tbl  = "undercrsmt";
		$acctmaster_tbl  = "acctmaster";
		$acctgstmst_tbl  = "accgrpmstn"; 
		$acctmstdet_tbl  = "acctmstdet";		
		$comp_id         = $this->company_id;
		$builder         = $this->db->table($acctmaster_tbl);
		$builder->join($undercrsmt_tbl, "$undercrsmt_tbl.crs_mst_id = $acctmaster_tbl.acc_id AND $undercrsmt_tbl.crs_mst_type =1", 'left');
		$builder->join($acctgstmst_tbl, "$acctgstmst_tbl.acc_grp_id = $undercrsmt_tbl.under_crs_mst_id", 'left');
		$builder->join($acctmstdet_tbl, "$acctmstdet_tbl.acc_id = $acctmaster_tbl.acc_id", 'left');
		
		$builder->select([
			"$undercrsmt_tbl.crs_mst_parent_id as acc_grp_parent_id",
			"$undercrsmt_tbl.under_crs_mst_id as acc_grp_id",
			"$acctmaster_tbl.acc_name",			
			"$acctmaster_tbl.acc_id",
			"$acctmstdet_tbl.acc_gstin",
			"$acctmstdet_tbl.acc_is_sez",
			"$acctmstdet_tbl.acc_sac"
		]);
		$builder->where("$acctmaster_tbl.acc_is_active", 1);
		$builder->where("$acctmaster_tbl.cmp_id", $this->company_id);
		$builder->where("$undercrsmt_tbl.cmpfymastr_id = $this->fy_id
			AND 
			$undercrsmt_tbl.cmp_id = $this->company_id");
		// Group your LIKE conditions
		$builder->groupStart()
			->like("LOWER($acctgstmst_tbl.acc_grp_name)", 'cash & cash equivalents', 'both')
			->orLike("LOWER($acctgstmst_tbl.acc_grp_name)", 'trade payable', 'both')
			->orLike("LOWER($acctgstmst_tbl.acc_grp_name)", 'bank od', 'both')
			->orLike("LOWER($acctgstmst_tbl.acc_grp_name)", 'trade receivables', 'both')
		->groupEnd();
		$builder->orderBy("$acctmaster_tbl.acc_name", "ASC");
		$data = $builder->get()->getResultArray();		
		if($data){
			foreach($data as $key => $value){  
				 $account_full_info   = $this->account_full_info($value['acc_id']);
				 $state_code   = 0;
				 if($account_full_info){
					 if($account_full_info['address_info']){
						 $acc_country = $account_full_info['address_info']['contact_country'];
						 $acc_state = $account_full_info['address_info']['contact_state'];
						 
						 $state_info    =  $this->CommonModel->get_state_info($acc_country,$acc_state);
						 if($state_info)
							$state_code   = sprintf( '%02d', $state_info['state_code'] );
						else
							$state_code   = 0;
					 }
				 }
				 
				$data[$key]['gstin']       = $value['acc_gstin'];
				$data[$key]['is_sez']      = $value['acc_is_sez'];
				$data[$key]['dealer_type'] = 0;
				$data[$key]['is_bbb']      = 0;
				$data[$key]['grpid']       = $value['acc_grp_id'];
				$data[$key]['state_code']  = sprintf( '%02d', $state_code);
		       	if(in_array($value['acc_grp_id'], $bbb_groups)){
		           	$data[$key]['is_bbb'] = 1;
		       	}			   
			}
		} 
		return $data;	
	}
	
	function getTaxAmount($rate, $amount) {
     return ($rate / 100) * $amount;
    }
    
   public function check_sale_taxinc($voucher_txn_id,$compId=null){
	   if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id; 
    	 $txn_row        = $this->db->table("acctamtinc")->where('cmp_id',$sel_compId)->where('vch_txn_id',$voucher_txn_id)->countAllResults(); 
    	 if($txn_row >0)
    	  return 1;
         else
         return 0;
   }
   
   public function check_sale_memoentry($voucher_txn_id,$compId=null){
	   if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
    	$txn_row        = $this->db->table("accttxnmst")->where('cmp_id',$sel_compId)->where('acc_txn_amt >',0)->where('acc_txn_type',3)->where('vch_txn_id',$voucher_txn_id)->get()->getRowArray(); 
    	if($txn_row)
    		return "1";
         else
         return "0";
   }
   
   public function GetTotalTax(
    $itmsdata,
    $type,
    $gstinType = 1,
    $sale_against_status = 1,
    $cmp_supply_type = null,
    $vchtype = null,
    $pos_code = null
) {
    $tax_total     = 0;
    $tax_total_fcy = 0;

    $bo_state_code = $this->session->get('ses_bostecd');

    if (!$itmsdata) {
        return ['tax_amount' => 0, 'fcy_tax_amount' => 0];
    }

    foreach ($itmsdata as $row) {

        // -------------------------------
        // Base tax rate
        // -------------------------------
        $igst_rate = (float)($row['igst_rate'] ?? ($row['igst'] ?? 0));

        // -------------------------------
        // Amount selection
        // -------------------------------
        if ($type === 'acc') {
            $amount     = $row['amount'] ?? 0;
            $amount_fcy = $row['amountfc'] ?? 0;
        } elseif ($type === 'bsd') {
            $amount     = $row['billsundry_amount'] ?? 0;
            $amount_fcy = $row['billsundry_fcy_amount'] ?? 0;
        } elseif ($type === 'itm') {
            $amount     = $row['item_total_amount'] ?? 0;
            $amount_fcy = $row['item_total_fcy_amount'] ?? 0;
        } else {
            continue;
        }

        $tax_details = $row['tax_details'] ?? [];
        $cess_rate   = (float)($tax_details['cess'] ?? 0);

        // -------------------------------------------------
        // GST DECISION LOGIC
        // -------------------------------------------------
        $apply_tax             = false;
        $use_composition_rates = false;

        if ((int)$vchtype === 2) { // CREDIT NOTE

            if ((int)$gstinType === 2 && (int)$sale_against_status === 1) {
                $apply_tax = true;
            } elseif ((int)$gstinType === 2 && (int)$sale_against_status === 2) {
                $apply_tax = true;
                $use_composition_rates = true;
            }
			else if ((int)$gstinType === 1 && (int)$sale_against_status === 1) {
                $apply_tax = true;
            }

        } elseif ((int)$vchtype === 3) { // DEBIT NOTE

            if ((int)$gstinType === 1 && (int)$sale_against_status === 1) {
                $apply_tax = true;
            }

        } else { // SALE / PURCHASE

            if ((int)$gstinType === 1) {
                $apply_tax = true;
            }
        }

        if (!$apply_tax) {
            continue;
        }

        // -------------------------------------------------
        // Composition override
        // -------------------------------------------------
        if ($use_composition_rates) {
            helper('composition');
            $comp      = get_composition_rates($cmp_supply_type);
            $igst_rate = (float)$comp['igst'];
            $cess_rate = (float)($comp['cess'] ?? 0);
        }

        // -------------------------------------------------
        // 🔥 CORE FIX: STATE LOGIC
        // -------------------------------------------------
		if($pos_code){
			$is_interstate = ($bo_state_code != $pos_code);

			// -------------------------------
			// GST CALCULATION
			// -------------------------------
			if ($igst_rate > 0) {

				if ($is_interstate) {
					// IGST
					$tax_total     += parseAmount(($amount * $igst_rate) / 100);
					$tax_total_fcy += parseAmount(($amount_fcy * $igst_rate) / 100);

				} else {
					// CGST + SGST split
					$cgst_rate = $igst_rate / 2;
					$sgst_rate = $igst_rate / 2;

					$tax_total     += parseAmount(($amount * $cgst_rate) / 100);
					$tax_total     += parseAmount(($amount * $sgst_rate) / 100);

					$tax_total_fcy += parseAmount(($amount_fcy * $cgst_rate) / 100);
					$tax_total_fcy += parseAmount(($amount_fcy * $sgst_rate) / 100);
				}
			}	
		}
		else{
			if ($igst_rate > 0) { 
			$tax_total += parseAmount(($amount * $igst_rate) / 100);
			$tax_total_fcy += parseAmount(($amount_fcy * $igst_rate) / 100);
			}
	}
        

        // -------------------------------
        // CESS (always separate)
        // -------------------------------
        if ($cess_rate > 0) {
            $tax_total     += parseAmount(($amount * $cess_rate) / 100);
            $tax_total_fcy += parseAmount(($amount_fcy * $cess_rate) / 100);
        }
    }

    return [
        'tax_amount'     => parseAmount($tax_total),
        'fcy_tax_amount' => parseAmount($tax_total_fcy),
    ];
}

	public function GetTotalTax_Olde($itmsdata, $type) {
		$tax_total     = 0;
		$tax_total_fcy = 0;

		if (!$itmsdata) {
			return ['tax_amount' => 0, 'fcy_tax_amount' => 0];
		}

		foreach ($itmsdata as $row) {
			$igst_rate = $row['igst_rate'] ?? ($row['igst'] ?? 0);

			// Determine the amount fields based on type
			if ($type === 'acc') {
				$amount     = $row['amount'];
				$amount_fcy = $row['amountfc'];
			} elseif ($type === 'bsd') {
				$amount     = $row['billsundry_amount'];
				$amount_fcy = $row['billsundry_fcy_amount'];
			}
			elseif ($type === 'itm') {
				$amount     = $row['item_total_amount'];
				$amount_fcy = $row['item_total_fcy_amount'];
			}	else {
				continue;  // Skip unknown type
			}

			$tax_details = $row['tax_details'] ?? [];

			if (isset($tax_details['cess']) && $tax_details['cess'] > 0) {
				$tax_total += parseAmount(($amount * $tax_details['cess']) / 100);
			}

			$tax_total += parseAmount(($amount * $igst_rate) / 100);
			$tax_total_fcy += parseAmount(($amount_fcy * $igst_rate) / 100);
		}

		return [
			'tax_amount'     => parseAmount($tax_total),
			'fcy_tax_amount' => $tax_total_fcy
		];
	}
	
	public function vchfcyrate_info($voucher_txn_id,$cmp_fcy_mst_id=0,$compId=null){
		if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
		$builder = $this->db->table("vchfcyrate");
	  	$builder->where('vch_txn_id', $voucher_txn_id);
		$builder->where('cmp_id', $sel_compId);	   
	  	$result = $builder->get()->getRowArray();
		return $result;
	}
	
	public function getLastVoucherDate(int $vch_type_id){
    $builder = $this->db->table('vchtxnconso');
    $builder->select('vch_date AS last_date')
            ->where('vch_type_id', $vch_type_id)
            ->where('cmp_id',  $this->company_id)   // optional, if you filter by company
            ->where('hobo_id', $this->bo_id)
			->orderBy('vch_txn_id', 'DESC')
			->limit(1);       // optional, if you filter by branch/bo
    $row = $builder->get()->getRowArray();

    if(!empty($row['last_date']))
    return date('d-m-Y',strtotime($row['last_date'])) ?? date("d-m-Y");
    else 
	return  date("d-m-Y");	
}



  public function get_voucher_cons_info_for_comsp($voucher_type_id,$draft_vch_rec_id,$voucher_series_id)
{
    $voucher_last_date = '';
    $result            = [];

    // 1) Base voucher consolidation row
    $builder = $this->db->table('vchtxnconso');
	$builder->where('cmp_id', $this->company_id);
    $builder->where('hobo_id', $this->bo_id);
     $builder->where('vch_series_id', $voucher_series_id);
      $builder->where('draft_vch_rec_id', $draft_vch_rec_id);
      
    if ($voucher_type_id > 0) {
        $builder->where('vch_type_id', $voucher_type_id);
    }
    $rowConso = $builder->get()->getRowArray();
    SaveErrorLog($this->db->getlastquery());
    return $rowConso;
}


  public function get_voucher_cons_info($voucher_txn_id, $voucher_type_id = 0)
{
    $voucher_last_date = '';
    $result            = [];

    // 1) Base voucher consolidation row
    $builder = $this->db->table('vchtxnconso');
	$builder->where('cmp_id', $this->company_id);
    $builder->where('vch_txn_id', $voucher_txn_id);
    $builder->where('hobo_id', $this->bo_id);
    if ($voucher_type_id > 0) {
        $builder->where('vch_type_id', $voucher_type_id);
    }
    $rowConso = $builder->get()->getRowArray();
    if ($rowConso) {
        $result = $rowConso;

        // 2) FCY info
        $vchfcyrate_info = $this->vchfcyrate_info($voucher_txn_id);
        if ($vchfcyrate_info) {
            $result['forexcrncy_rate'] = $vchfcyrate_info['vch_fcy_rate'];
            $result['currency_id']     = $vchfcyrate_info['cmp_fcy_mst_id'];
        } else {
            $result['forexcrncy_rate'] = '';
            $result['currency_id']     = 1;
        }

        // 3) Last voucher date for this type
        $voucher_last_date = $this->getLastVoucherDate(
            ($voucher_type_id > 0) ? $voucher_type_id : (int)($rowConso['vch_type_id'] ?? 0)
        );

        // 4) Party info (first acc master linked to this txn)
        $builder = $this->db->table('cmptxnmstn c');
        $row     = $builder->select('c.*, a.acc_name, a.acc_alias, a.acc_print_name')
            ->join('acctmaster a', 'a.acc_id = c.master_id', 'left')
            ->where('c.vch_txn_id', $voucher_txn_id)
            ->where('c.master_id_type', 'acc')
			->where('c.cmp_id', $this->company_id)
			->where('a.cmp_id', $this->company_id)
            ->orderBy('c.txn_id', 'ASC')
            ->get(1)
            ->getRowArray();

        if ($row) {
            $result['party_id']   = $row['master_id'];
            $result['party_name'] = $row['acc_name'];
        } else {
            $result['party_id']   = 0;
            $result['party_name'] = '';
        }

        // 5) Series info (from vchseries table)
        // NOTE: Update table name if your schema uses a different one (e.g., 'vchseriesn' or 'vchseries').
        $cmpId          = (int)($this->company_id);
        $resolvedTypeId = ($voucher_type_id > 0)
            ? (int)$voucher_type_id
            : (int)($rowConso['vch_type_id'] ?? 0);

        $resolvedSeriesId = (int)($rowConso['vch_series_id'] ?? 0); // if stored in vchtxnconso

        $seriesRow = null;

        if ($resolvedSeriesId > 0) {
            // Prefer exact series id if consolidation row carries it
            $seriesRow = $this->db->table('vchseriesn')
                ->select('vch_series_id, vch_series_name')
                ->where('vch_series_id', $resolvedSeriesId)
				->where('cmp_id', $cmpId)
                ->get(1)
                ->getRowArray();
        }

        if (!$seriesRow && $cmpId > 0 && $resolvedTypeId > 0) {
            // Fallback: match by company and voucher type
            $seriesRow = $this->db->table('vchseriesn')
                ->select('vch_series_id, vch_series_name')
                ->where('cmp_id', $cmpId)
                ->where('vch_type_id', $resolvedTypeId)
                ->orderBy('vch_series_id', 'ASC')
                ->get(1)
                ->getRowArray();
        }

        if ($seriesRow) {
            $result['vch_series_id']   = (int)$seriesRow['vch_series_id'];
            $result['vch_series_name'] = (string)$seriesRow['vch_series_name'];
        } else {
            $result['vch_series_id']   = 0;
            $result['vch_series_name'] = '';
        }
    }

    $result['voucher_last_date'] = $voucher_last_date;
    return $result;
} 
	  
	public function ajax_receipt_accounts_list(){ 
	     /* 23 => Cash & Cash Equivalents   Under Current Assets*/
		 /* 59 => OTHER INTEREST EXPENSES (under) */
	    $cash_groups    = $this->get_cash_groups();	
		$babnkod_groups    = $this->get_bankod_groups();	
		$all_groups = array_merge(
			$cash_groups ?? [],
			$babnkod_groups ?? []
		);
    	$final_array    = $this->get_sub_group_ids($all_groups);
		$comp_id        = $this->company_id;	
		$undercrsmt_tbl  = "undercrsmt";
		$acctmaster_tbl  = "acctmaster";
		$acctgstmst_tbl  = "accgrpmstn"; 
		
		$builder = $this->db->table($acctmaster_tbl);
		$builder->join($undercrsmt_tbl, "$undercrsmt_tbl.crs_mst_id = $acctmaster_tbl.acc_id AND $undercrsmt_tbl.cmpfymastr_id = $this->fy_id
			AND 
			$undercrsmt_tbl.cmp_id = $this->company_id AND $undercrsmt_tbl.crs_mst_type =1", 'left');
		$builder->join($acctgstmst_tbl, "$acctgstmst_tbl.acc_grp_id = $undercrsmt_tbl.under_crs_mst_id", 'left');
		
		
		$builder->select([
			"$undercrsmt_tbl.crs_mst_parent_id as acc_grp_parent_id",
			"$undercrsmt_tbl.under_crs_mst_id as acc_grp_id",
			"$acctmaster_tbl.acc_name",			
			"$acctmaster_tbl.acc_id"
		]);
		// Group your LIKE conditions
		$builder->groupStart()
			->like("LOWER($acctgstmst_tbl.acc_grp_name)", 'cash & cash equivalents', 'both')
			->orLike("LOWER($acctgstmst_tbl.acc_grp_name)", 'bank od', 'both')
			
		->groupEnd();
		 $builder->where("$undercrsmt_tbl.cmpfymastr_id IS NOT NULL");	
	 	 $builder->where("$undercrsmt_tbl.cmpfymastr_id",$this->fy_id);
         $builder->where("$undercrsmt_tbl.cmp_id",$this->company_id); 		 
		 $builder->whereIn("$undercrsmt_tbl.under_crs_mst_id", $final_array);
		 $builder->orderBy("$acctmaster_tbl.acc_name","ASC");
		 $data = $builder->get()->getResultArray();
   			 
		 $final = array();				  
		 if($data){
			foreach($data as $key => $value){
			  $is_cash = 0;
			       	if(in_array($value['acc_grp_id'], $cash_groups)){
			          	$is_cash = 1;
			       	}
               $final[]= array("is_cash"=>$is_cash,"dealer_type"=>"","gstin"=>"","state_code"=>"","is_bbb"=>"","acc_id"=>$value['acc_id'],"account_name"=>$value['acc_name']);
			}
		}		
		return $final;	
	} 
  public function get_voucher_long_narration($voucher_txn_id,$compId=null)
 	{
		if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;  
 		$narration = '';
 		$narr = $this->db->table("vchlongnar")
	   					->where('vch_txn_id',$voucher_txn_id)
						->where('cmp_id',$sel_compId)
	   					->get()->getRowArray();
	   	if($narr){
	   			$narration = $narr['vch_long_narr'];
	   		}
	   	return $narration;
 	}	
	
 public  function get_sub_group_ids($array,$compId=null){ 
 if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;
 
    	if(!empty($array)){
    	    $builder = $this->db->table("accgrpmstn acgrpmst");
    	    $builder->join("undercrsmt", "undercrsmt.crs_mst_id = acgrpmst.acc_grp_id AND undercrsmt.crs_mst_type =2 AND undercrsmt.cmp_id =$sel_compId", 'left');
    	    $builder->select('acgrpmst.acc_grp_id');
    	    $builder->whereIn('undercrsmt.under_main_id', $array);
			$data = $builder->get()->getResultArray();
	    	if($data){
	    		foreach($data as $key => $value) {
	    			array_push($array, $value['acc_grp_id']);
	    		}
	    	}
    	}
    	return $array;
    }	
	
  public  function add_voucher_cons_data($data,$VchOthrBo=0){
	      
		 $comp_vch_cons_tbl = 'vchtxnconso';		
		 if($this->session->get('ses_boid')!='' && $VchOthrBo==0)
			$bo_id =  $this->session->get('ses_boid');
		  else 
			$bo_id =$VchOthrBo;
		  $data['hobo_id'] = $bo_id;
		  $this->db->table($comp_vch_cons_tbl)->insert($data);
		  $new_vch_txn_id = $this->db->insertID();
		  if ((int)($data['vch_type_id'] ?? 0) === \App\Libraries\CompositionPosting::JOURNAL_TYPE) {
			  // a composition "GST PAID A/C" journal: its entry is completed and checked before commit
			  \App\Libraries\CompositionPosting::touch($new_vch_txn_id);
		  }
		  return $new_vch_txn_id;
	}
    public function SaveDrftVoucher($data){		
		 $postgr_db = $this->externaldb->postgr_db();
		 $postgr_db->table("drftvchrec")->insert($data);		  
		  return $postgr_db->insertID();
	} 
	
	public function UpdateDrftVoucher($draft_vch_rec_id,$data){
		 $postgr_db = $this->externaldb->postgr_db();
		 $postgr_db->table("drftvchrec")->where("draft_vch_rec_id",$draft_vch_rec_id)->update($data);		  
			
	}
     
   public function add_comp_txn_data($data){
	 	$comp_txn_master_tbl ='cmptxnmstn';
	   	$this->db->table($comp_txn_master_tbl)->insert($data);	
       	return  $this->db->insertID();
	}
   
   public function add_gstroutsup_data($data){
	   	$this->db->table("gstroutsup")->insert($data);	
       	return  $this->db->insertID();
	}
	
  public function add_gstrinwsup_data($data){
	   	$this->db->table("gstrinwsup")->insert($data);	
       	return  $this->db->insertID();
	}	
	
	public function get_gstroutsup_info($voucher_txn_id,$compId=null){
		if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id; 
	   	$builder = $this->db->table("gstroutsup");
	   	$builder->where('cmp_id',$sel_compId);
	   	$builder->where('vch_txn_id',$voucher_txn_id);
	   	$response = $builder->get()->getRowArray();
	   	return $response;
	}
	
	public function get_gstrinwsup_info($voucher_txn_id){
	   	$builder = $this->db->table("gstrinwsup");
	   	$builder->where('cmp_id',$this->company_id);
	   	$builder->where('vch_txn_id',$voucher_txn_id);
	   	$response = $builder->get()->getRowArray();
	   	return $response;
	}
	
	
   public function get_narration_txn_id($vch_txn_id){
	return $this->db->table("cmptxnmstn")->select('txn_id')
	     ->where('vch_txn_id',$vch_txn_id)
		 ->where('cmp_id',$this->company_id)
		 ->where('master_id',0)
		 ->where('master_id_type','nrr')->get()->getRowArray();   
   }	
   public function get_item_tax_summary($vch_txn_id)
    {
        $builder = $this->db->table('vchgstsumn');
        $builder->select('
            vch_hsn_sac, 
            vch_igst, 
            vch_cgst, 
            vch_sgst_ugst, 
            vch_cess, 
            vch_total_tax,
            (vch_igst_rate + vch_cgst_rate + vch_sgst_ugst_rate) as tax_rate
        ');
        $builder->where('vch_txn_id', $vch_txn_id);
        $builder->where('cmp_id', $this->company_id);
        
        $query = $builder->get();
        $response =  $query->getResultArray();
		
		return $response;
    }
		
  public  function save_voucher_narration($vch_txn_id,$txn_id,$narr_type,$narration_txt,$compId=null){
	 if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;


	 if(empty($narration_txt))
		  $narration_txt='N/A';
	   if($narr_type=='long'){
		  $long_narr_tbl = "vchlongnar";
		  $result = $this->db->table($long_narr_tbl)->where('vch_txn_id', $vch_txn_id)->countAllResults();
	     if($result){
			 $data = array('cmp_id'=>$sel_compId,'vch_long_narr'=>trim($narration_txt)); 
			 $this->db->table($long_narr_tbl)->where('vch_txn_id',$vch_txn_id)->update($data);  
		  }else{		 
			$data = array('cmp_id'=>$sel_compId,'vch_txn_id'=>$vch_txn_id,'vch_long_narr'=>trim($narration_txt));
			$this->db->table($long_narr_tbl)->insert($data); 
		  }
	   }
	   else  if($narr_type=='short'){
		$short_narr_tbl = "vchshrtnar";
		
		$result = $this->db->table($short_narr_tbl)->where('txn_id',$txn_id)->where('vch_txn_id',$vch_txn_id)->countAllResults();	 
        if($result){
		  $data = array('cmp_id'=>$sel_compId,'vch_short_narr'=>trim($narration_txt));
			$this->db->table($short_narr_tbl)->where('txn_id',$txn_id)->where('vch_txn_id',$vch_txn_id)->update($data);  	
		}  
		else{
			$data = array('cmp_id'=>$sel_compId,'vch_txn_id'=>$vch_txn_id,'txn_id'=>$txn_id,'vch_short_narr'=>trim($narration_txt));
			$this->db->table($short_narr_tbl)->insert($data);  
	   	  }
	    }  	  
     }	
	 
  public   function update_voucher_narration($vch_txn_id,$txn_id,$narr_type,$narration_txt){
	  if(empty($narration_txt))
		  $narration_txt='N/A';
	   if($narr_type=='long'){
		  $long_narr_tbl = "vchlongnar";
		  $data = array('cmp_id'=>$this->company_id,'vch_long_narr'=>trim($narration_txt));
		  $this->db->table($long_narr_tbl)->where('vch_txn_id',$vch_txn_id)->update($data); 
	   }
	   else  if($narr_type=='short'){
		 $short_narr_tbl = "vchshrtnar";
		 $data = array('cmp_id'=>$this->company_id,'vch_short_narr'=>trim($narration_txt));
		 $this->db->table($short_narr_tbl)->where('vch_txn_id',$vch_txn_id)
		            ->where('txn_id',$txn_id)->update($data);  
	    }  	  
     }	 
	 
   public function material_centre_dropdown(){
	   $data              = $this->db->table("matcentmst")->where('cmp_id',$this->company_id)->orderBy('mat_cent_name','ASC')->get()->getResultArray();
	   $final_result      = array();
	   $final_result['']  = '';
	   if($data){
		  foreach($data as $row){
              $final_result[$row['mat_cent_id']] = $row['mat_cent_name'];   
	        }
        }
	  return $final_result;	
     }	 
	 
 public function units_dropdown(){
	   $data =  $this->db->table("itmunitmst")->where('cmp_id', $this->company_id)->orderBy('itm_unit_name','ASC')->get()->getResultArray();
	   $final_result = array();
	   $final_result['']  = '';
	   if($data){
		  foreach($data as $row){
              $final_result[$row['itm_unit_id']] = $row['itm_unit_name'];			   
	        }
        }
	  return $final_result;	
     } 

 public function units_grid(){
	   $data =  $this->db->table("itmunitmst")->where('cmp_id', $this->company_id)->orderBy('itm_unit_name','ASC')->get()->getResultArray();
	   $final_result = array();
	   if($data){
		  foreach($data as $row){
              $final_result[] =array("label" => $row['itm_unit_name'],
			                         "value" => $row['itm_unit_name'],
									 "id"    => $row['itm_unit_id']
									 );			   
	        }
        }
	  return $final_result;	
     }
	 
  public  function add_acc_txn_data($data){  
		 $this->db->table("accttxnmst")->insert($data);		 		  
    }
  
   public  function add_itm_txn_data($data){  
		 $this->db->table("itemtxnmst")->insert($data);		 		  
    }
	
  public  function SaveTxnApprvLog($data,$voucher_txn_id,$uuid_to,$erp_txn_aprv_status){  
		 $this->db->table("erptxnaprv")->insert($data);		 		  
    } 
	
  public function add_pr_txn_data($data){  
		 $this->db->table("prjtxnmstn")->insert($data);		 		  
    }
 
  public function add_taxsummary_data(array $data, $tax_required_flag,$gstinType=null)
	{
		 if($gstinType)
			$sel_gstinType = $gstinType;
		 else 
			$sel_gstinType = (int) ($this->session->get('bo_gstin_type') ?? 1);

		// Normalize the flag
		$taxRequired = ($tax_required_flag === 1 || $tax_required_flag === '1' || $tax_required_flag === true);
       if($sel_gstinType==1){
		if (!$taxRequired) {
			// No tax: force all tax components and rates to zero
			$data['vch_igst']            = 0;
			$data['vch_igst_rate']       = 0;
			$data['vch_cgst']            = 0;
			$data['vch_cgst_rate']       = 0;
			$data['vch_sgst_ugst']       = 0;
			$data['vch_sgst_ugst_rate']  = 0;
			$data['vch_cess']            = 0;
			$data['vch_cess_rate']       = 0;
			$data['vch_total_tax']       = 0;

			// Keep rate group consistent with zero-tax
			$data['gst_rate_grp'] = '0,0';
		} else {
			// Tax applies: ensure numeric types (fallback to 0 if missing)
			$data['vch_igst']            = parseAmount($data['vch_igst']            ?? 0);
			$data['vch_igst_rate']       = parseAmount($data['vch_igst_rate']       ?? 0);
			$data['vch_cgst']            = parseAmount($data['vch_cgst']            ?? 0);
			$data['vch_cgst_rate']       = parseAmount($data['vch_cgst_rate']       ?? 0);
			$data['vch_sgst_ugst']       = parseAmount($data['vch_sgst_ugst']       ?? 0);
			$data['vch_sgst_ugst_rate']  = parseAmount($data['vch_sgst_ugst_rate']  ?? 0);
			$data['vch_cess']            = parseAmount($data['vch_cess']            ?? 0);
			$data['vch_cess_rate']       = parseAmount($data['vch_cess_rate']       ?? 0);
			$data['vch_total_tax']       = parseAmount($data['vch_total_tax']       ?? 0);
			$data['gst_rate_grp']        = (string)$data['gst_rate_grp'];
		}
	   }
	   else{
		// Tax applies: ensure numeric types (fallback to 0 if missing)
			$data['vch_igst']            = parseAmount($data['vch_igst']            ?? 0);
			$data['vch_igst_rate']       = parseAmount($data['vch_igst_rate']       ?? 0);
			$data['vch_cgst']            = parseAmount($data['vch_cgst']            ?? 0);
			$data['vch_cgst_rate']       = parseAmount($data['vch_cgst_rate']       ?? 0);
			$data['vch_sgst_ugst']       = parseAmount($data['vch_sgst_ugst']       ?? 0);
			$data['vch_sgst_ugst_rate']  = parseAmount($data['vch_sgst_ugst_rate']  ?? 0);
			$data['vch_cess']            = parseAmount($data['vch_cess']            ?? 0);
			$data['vch_cess_rate']       = parseAmount($data['vch_cess_rate']       ?? 0);
			$data['vch_total_tax']       = parseAmount($data['vch_total_tax']       ?? 0);
			$data['gst_rate_grp']        = (string)$data['gst_rate_grp'];   
	   }

		$this->db->table('vchgstsumn')->insert($data);
		return $this->db->insertID();
	}
 
 public function add_hsnsummary_data(array $data, $tax_required_flag,$gstinType=null)
	{
		
		if($gstinType)
			$sel_gstinType = $gstinType;
		 else 
			$sel_gstinType = (int) ($this->session->get('bo_gstin_type') ?? 1);
		// Normalize flag to strict boolean
		$taxRequired = ($tax_required_flag === 1 || $tax_required_flag === '1' || $tax_required_flag === true);
		if($sel_gstinType==1){
		if (!$taxRequired) {
			// No tax: force all tax components to zero
			$data['vch_hsn_sac_igst']      = 0;
			$data['vch_hsn_sac_cgst']      = 0;
			$data['vch_hsn_sac_sgst_ugst'] = 0;
			$data['vch_hsn_sac_cess']      = 0;
		} else {
			// Tax applies: ensure numeric values (fallback to 0 if missing)
			$data['vch_hsn_sac_igst']      = parseAmount($data['vch_hsn_sac_igst']      ?? 0);
			$data['vch_hsn_sac_cgst']      = parseAmount($data['vch_hsn_sac_cgst']      ?? 0);
			$data['vch_hsn_sac_sgst_ugst'] = parseAmount($data['vch_hsn_sac_sgst_ugst'] ?? 0);
			$data['vch_hsn_sac_cess']      = parseAmount($data['vch_hsn_sac_cess']      ?? 0);
		}
		}else{
		    $data['vch_hsn_sac_igst']      = parseAmount($data['vch_hsn_sac_igst']      ?? 0);
			$data['vch_hsn_sac_cgst']      = parseAmount($data['vch_hsn_sac_cgst']      ?? 0);
			$data['vch_hsn_sac_sgst_ugst'] = parseAmount($data['vch_hsn_sac_sgst_ugst'] ?? 0);
			$data['vch_hsn_sac_cess']      = parseAmount($data['vch_hsn_sac_cess']      ?? 0);
		}

		return $this->db->table('vchhsnsacn')->insert($data);
	}
   public function getGstPaidAccountId($compId=null)
	{
		if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;

		$row = $this->db->table('acctmaster a')
        ->select('acc_id')
        ->where('a.cmp_id', $sel_compId)
	   ->where('upper(a.acc_name)', 'GST PAID A/C')
		->where('a.acc_is_restrict', 2)
        ->limit(1)
        ->get()
        ->getRowArray();
      return $row ? (int)$row['acc_id'] : null;		
	}	
	
   public function getTaxRatesByCategory($tax_cat_mst_id,$compId=null)
    {
		if($compId)
			$sel_compId = $compId;
		 else 
			$sel_compId = $this->company_id;


        $builder = $this->db->table("taxcatrate");
        $builder->select('tax_cat_sub_type, tax_cat_rate as tax_rate');
        $builder->where("tax_cat_mst_id", $tax_cat_mst_id); 
		$builder->where("cmp_id", $sel_compId);
        $rates = $builder->get()->getResultArray();
        $result = [
            'igst' => 0,
            'cgst' => 0,
            'sgst' => 0,
            'utgst' => 0,
            'cess' => 0,
        ];
    
        foreach ($rates as $row) {
            switch ($row['tax_cat_sub_type']) {
                case 1: // IGST
                    $result['igst'] = $row['tax_rate'];
                    break;
                case 2: // SGST
                    $result['sgst'] = $row['tax_rate'];
                    break;
                case 3: // CGST
                    $result['cgst'] = $row['tax_rate'];
                    break;
                case 4: // UTGST
                    $result['utgst'] = $row['tax_rate'];
                    break;
                case 5: // CESS
                    $result['cess'] = $row['tax_rate'];
                    break;
            }
        }
    
        return $result;
    }      
   public  function add_register_txn_data($data){  
	/****** Save payments, receipt, contra, journal entries without item for register  ******/
     $acctvchreg_tbl = "acctvchreg";		 
	 $this->db->table($acctvchreg_tbl)->insert($data);
    }	
   
   public  function add_item_register_txn_data($data){  
	 $this->db->table("itmvchregn")->insert($data);
	 return  $this->db->insertID();
    }
	
	public  function add_vchbridgen_txn_data($data){  
	 $this->db->table("vchbridgen")->insert($data);
	 return  $this->db->insertID();
    }
	
	
	public  function add_item_sub_register_txn_data($data){  
	 $this->db->table("itmvchregd")->insert($data);	 
    }
	
   public  function add_taxinc_txn_data($data){  
	 $this->db->table("acctamtinc")->insert($data);
    }	    
      
  function pos_state_info($state_code){
	return $this->aicountly_db->table('aicountly_stateslist_univdb')->select('state_code,state_id,state_name,country_id')->where('state_code', $state_code)->get()->getRowArray();		
		
	}
	
  function show_states_lists($show_empty=''){
	   $final    = array();
	   if($show_empty=='')
	   $final['']   = 'Choose';
	   $response =  $this->aicountly_db->table('aicountly_stateslist_univdb')->orderBy('state_name')->get()->getResultArray();
	   foreach($response as $row){
	   		$state_code = sprintf( '%02d', $row['state_code'] );
	       $final[$state_code] = $row['state_name'].'('.$state_code.')';
	   }
	   return $final;
	   
     }    
  
  function isvoucher_autobillno($voucher_type_id,$comp_vch_series_id){
	$cmpvchseri_tbl = 'vchseriesn'; 
	$vchseriesa_tbl = 'vchseriesa';
	$vhtxnconso_tbl = 'vchtxnconso';
	
	$builder = $this->db->table($cmpvchseri_tbl.' cmpvchseri');	
    $builder->join($vchseriesa_tbl.' vchseriesa', 'vchseriesa.vch_series_id=cmpvchseri.vch_series_id');
    $builder->where('cmpvchseri.vch_type_id', $voucher_type_id);   
    $builder->where('cmpvchseri.vch_series_method', 1);
    if($comp_vch_series_id>0)
	 $builder->where('cmpvchseri.vch_series_id', $comp_vch_series_id); 
     $builder->orderBy('cmpvchseri.vch_series_id');
     $response = $builder->get()->getRowArray();
     if($response)
      return 1;
     else 
	  return 0;
 }
 
  function get_billno_format($voucher_type_id, $comp_vch_series_id, $voucher_date, $format_counter = 0, $counter = '')
{
    // Table names
    $cmpvchseri_tbl = 'vchseriesn';
    $vchseriesa_tbl = 'vchseriesa';
    $vhtxnconso_tbl = 'vchtxnconso';

    // Calendar / FY data
    $calendaer_data = fy_calender_js();
    $half_years = $calendaer_data['half_years'];
    $quarters   = $calendaer_data['quarters'];

    // FY anchor (for yearly reset)
    $fyStartMD = date('m-d', strtotime($calendaer_data['from_date'])); // e.g. 04-01
    $fyEndMD   = date('m-d', strtotime($calendaer_data['to_date']));   // e.g. 03-31

    $current_date      = strtotime(date('Y-m-d'));
    $voucher_timestamp = strtotime($voucher_date);

    $short_start_year = date('y', strtotime($calendaer_data['from_date']));
    $short_end_year   = date('y', strtotime($calendaer_data['to_date']));
    $long_start_year  = date('Y', strtotime($calendaer_data['from_date']));
    $long_end_year    = date('Y', strtotime($calendaer_data['to_date']));

    // Determine current quarter (for placeholders)
    $current_quarter = null;
    $qr_short_start_month = $qr_short_end_month = '';
    $qr_long_start_month  = $qr_long_end_month  = '';
    foreach ($quarters as $index => $quarter) {
        if ($current_date >= strtotime($quarter['from_date']) &&
            $current_date <= strtotime($quarter['to_date'])) {
            $current_quarter = $index;
            $qr_short_start_month = date('m', strtotime($quarter['from_date']));
            $qr_short_end_month   = date('m', strtotime($quarter['to_date']));
            $qr_long_start_month  = date('M', strtotime($quarter['from_date']));
            $qr_long_end_month    = date('M', strtotime($quarter['to_date']));
            break;
        }
    }
    $qr_strings = [];
    foreach ($quarters as $q) {
        $qr_strings[] = date('m', strtotime($q['from_date'])) . '-' . date('m', strtotime($q['to_date']));
    }
    $qr_all_string = implode('/', $qr_strings);

    // Get series configuration (method=1, auto)
    $builder = $this->db->table($cmpvchseri_tbl . ' cmpvchseri');
    $builder->join($vchseriesa_tbl . ' vchseriesa', 'vchseriesa.vch_series_id = cmpvchseri.vch_series_id', 'inner', false);
    $builder->where('cmpvchseri.vch_type_id', $voucher_type_id);
    $builder->where('cmpvchseri.vch_series_method', 1);
    $builder->where('cmpvchseri.cmp_id', $this->company_id);
    if ($comp_vch_series_id > 0) {
        $builder->where('cmpvchseri.vch_series_id', $comp_vch_series_id);
    }
    $builder->orderBy('cmpvchseri.vch_series_id');
    $response = $builder->get()->getRowArray();

    if (!$response) {
        return $format_counter > 0 ? '001' : 1;
    }

    // Extract series configuration
    $comp_vch_renum      = $response['vch_series_renum'];
    $comp_vch_prefix     = $response['vch_series_prefix'] ?? '';
    $comp_vch_suffix     = $response['vch_series_suffix'] ?? '';
    $comp_vch_start      = (int) $response['vch_series_start'];
    $comp_vch_no_padding = $response['vch_series_padding'];
    $comp_vch_no_length  = (int) $response['vch_series_length'];

    // Use series id from config when caller didn't pass one (prevents counter reset)
    $series_id_for_count = ($comp_vch_series_id > 0)
        ? (int)$comp_vch_series_id
        : (int)$response['vch_series_id'];

    // Parse prefix/suffix
    $prefix_pattern = '';
    $prefix_separator = '';
    if ($comp_vch_prefix !== '') {
        $prefix_pattern   = substr($comp_vch_prefix, 0, -1);
        $prefix_separator = substr($comp_vch_prefix, -1);
    }

    $suffix_pattern = '';
    $suffix_separator = '';
    if ($comp_vch_suffix !== '') {
        $suffix_separator = mb_substr($comp_vch_suffix, 0, 1);
        $suffix_pattern   = substr($comp_vch_suffix, 1);
    }

    // Labels
    $prefix_label = $this->generatePrefixSuffix(
        $prefix_pattern, $prefix_separator, $comp_vch_renum,
        $voucher_date, $short_start_year, $short_end_year,
        $long_start_year, $long_end_year, $half_years, $quarters,
        $qr_short_start_month, $qr_short_end_month,
        $qr_long_start_month, $qr_long_end_month,
        $qr_all_string, $comp_vch_prefix, true
    );

    $suffix_label = $this->generatePrefixSuffix(
        $suffix_pattern, $suffix_separator, $comp_vch_renum,
        $voucher_date, $short_start_year, $short_end_year,
        $long_start_year, $long_end_year, $half_years, $quarters,
        $qr_short_start_month, $qr_short_end_month,
        $qr_long_start_month, $qr_long_end_month,
        $qr_all_string, $comp_vch_suffix, false
    );

    // Determine FY window for YEARLY renum based on voucher_date
    $vDate = date('Y-m-d', strtotime($voucher_date));
    $md    = date('m-d', strtotime($vDate));
    if ($md >= $fyStartMD) {
        $fy_start = date('Y', strtotime($vDate)) . '-' . $fyStartMD;
    } else {
        $fy_start = (date('Y', strtotime($vDate)) - 1) . '-' . $fyStartMD;
    }
    $fy_end = date('Y-m-d', strtotime($fy_start . ' +1 year -1 day')); // aligns to FY end day

    // Build counter query (now using series_id_for_count)
    $conso_builder = $this->db->table($vhtxnconso_tbl);
    $conso_builder->select('COALESCE(COUNT(*),0) AS total_count', false);
   // $conso_builder->where('vch_type_id', $voucher_type_id);
    $conso_builder->where('cmp_id', $this->company_id);
    $conso_builder->where('vch_series_id', $series_id_for_count);
	
	

    switch ($comp_vch_renum) {
        case '1': // Yearly -> align to FY window derived from voucher_date
            $conso_builder->where('vch_date >=', $fy_start);
            $conso_builder->where('vch_date <=', $fy_end);
            break;
        case '2': // Half yearly
            foreach ($half_years as $hdates) {
                if ($vDate >= $hdates['from_date'] && $vDate <= $hdates['to_date']) {
                    $conso_builder->where('vch_date >=', $hdates['from_date']);
                    $conso_builder->where('vch_date <=', $hdates['to_date']);
                    break;
                }
            }
            break;
        case '3': // Quarterly
            foreach ($quarters as $qdates) {
                if ($vDate >= $qdates['from_date'] && $vDate <= $qdates['to_date']) {
                    $conso_builder->where('vch_date >=', $qdates['from_date']);
                    $conso_builder->where('vch_date <=', $qdates['to_date']);
                    break;
                }
            }
            break;
        case '4': // Daily
            $conso_builder->where('vch_date', $vDate);
            break;
        default:  // No renewal scope filter
            break;
    }

    $result = $conso_builder->get()->getRowArray();
    
   
    
	
    $existing_count = isset($result['total_count']) ? (int)$result['total_count'] : 0;

    // Next counter within the chosen window
    $comp_vch_counter = $comp_vch_start + $existing_count;

	
    // Choose which number to format
    $number_to_format = ($counter === '' && $format_counter > 0) ? $format_counter : $comp_vch_counter;
    $formatted_number = sprintf("%0" . $comp_vch_no_length . "d", $number_to_format);
    $saved_seriesformat = trim($prefix_label) . $formatted_number . trim($suffix_label);



	
    // Return formatted string when requested, otherwise numeric counter
    return $format_counter > 0 ? $saved_seriesformat : $comp_vch_counter;
}

/**
 * Helper function to generate prefix/suffix
 */
private function generatePrefixSuffix($pattern, $separator, $renum_freq, $voucher_date, 
                                      $short_start_year, $short_end_year, $long_start_year, $long_end_year,
                                      $half_years, $quarters, $qr_short_start, $qr_short_end,
                                      $qr_long_start, $qr_long_end, $qr_all_string, $default_value, $is_prefix = true) {
    
    $label = '';
    
    switch($renum_freq) {
        case '1': // Yearly
            if($pattern == 'YY-YY') {
                $label = $short_start_year .  '-' . $short_end_year;
            } elseif($pattern == 'YYYY-YY') {
                $label = $long_start_year . '-' . $short_end_year;
            } elseif($pattern == 'YY/YY') {
                $label = $short_start_year .  '/' . $short_end_year;
            } elseif($pattern == 'YYYY/YY') {
                $label = $long_start_year .  '/' . $short_end_year;
            } else {
                return $default_value;
            }
            break;
            
        case '2': // Half yearly
            $year1_start = $half_years[0]['from_date'];
            $year2_start = $half_years[1]['from_date'];
            $short_start_month = date('m', strtotime($year1_start));
            $short_end_month = date('m', strtotime($year2_start));
            $long_start_month = date('M', strtotime($year1_start));
            $long_end_month = date('M', strtotime($year2_start));
            
            if($pattern == 'MM-MM') {
                $label = $short_start_month .  '-' . $short_end_month;
            } elseif($pattern == 'MMM-MMM') {
                $label = $long_start_month . '-' . $long_end_month;
            } elseif($pattern == 'MM/MM') {
                $label = $short_start_month .  '/' . $short_end_month;
            } elseif($pattern == 'MMM/MMM') {
                $label = $long_start_month .  '/' . $long_end_month;
            } else {
                return $default_value;
            }
            break;
            
        case '3': // Quarterly
            if($pattern == 'MM-MM') {
                $label = $qr_short_start . '-' . $qr_short_end;
            } elseif($pattern == 'MMM-MMM') {
                $label = $qr_long_start . '-' . $qr_long_end;
            } elseif($pattern == 'MM/MM') {
                $label = $qr_short_start . '/' . $qr_short_end;
            } elseif($pattern == 'MMM/MMM') {
                $label = $qr_long_start . '/' .  $qr_long_end;
            } elseif($pattern == 'Q1/Q2/Q3/Q4') {
                $label = $qr_all_string;
            } else {
                return $default_value;
            }
            break;
            
        case '4': // Daily
            $short_day = date('d', strtotime($voucher_date));
            $long_month = date('M', strtotime($voucher_date));
            $short_month = date('m', strtotime($voucher_date));
            
            if($pattern == 'DD-MMM') {
                $label = $short_day . '-' . $long_month;
            } elseif($pattern == 'DD/MM') {
                $label = $short_day . '/' . $short_month;
            } elseif($pattern == 'DD/MMM') {
                $label = $short_day . '/' . $long_month;
            } else {
                return $default_value;
            }
            break;
            
        default:
            return $default_value;
    }
    
    // Add separator
    if($is_prefix) {
        return $label . $separator;
    } else {
        return $separator . $label;
    }
}    
  function show_eco_lists(){
	   $final    = array();
	   $final['']   = 'Not Applicable';
	   return $final;
   }
  function supply_types_list(){
	   $final    = array();
	   $final[''] = 'Choose';
	   $response  = $this->db->table('invtypemst')->orderBy('inv_type_name')->get()->getResultArray();
	   foreach($response as $row){	   		
	       $final[$row['inv_type_mst_id']] = $row['inv_type_name'];
	   }
	   return $final;
   }
    
	private function fyStartFor(\DateTimeImmutable $dt): \DateTimeImmutable {
        $y=(int)$dt->format('Y'); $m=(int)$dt->format('n'); $fyY = ($m>=4)?$y:$y-1;
        return new \DateTimeImmutable("$fyY-04-01");
    }
    private function fyEndFor(\DateTimeImmutable $dt): \DateTimeImmutable {
        return $this->fyStartFor($dt)->modify('+1 year -1 day');
    }
    private function methodId(string $method): string
	{
		$m = strtoupper(trim($method));
		return in_array($m, ['AVG','FIFO','LIFO'], true) ? $m : 'AVG';
	}
	function markSnapshotDirty($date){
		$postgr_db = $this->externaldb->postgr_db();
		$cmpId  = $this->company_id;
		$d   = new \DateTimeImmutable($date);
        $fyS = $this->fyStartFor($d)->format('Y-m-d');
        $fyE = $this->fyEndFor($d)->format('Y-m-d');
		$key='';
		$method ='ALL';

        $mids = ($method === 'ALL') ? ['AVG','FIFO','LIFO'] : [ $this->methodId($method) ];
		$methods = array_map(fn($m)=> strtoupper(trim((string)$m)), $mids);

		// Params (cmp/fy/date first, then methods)
		$params = array_merge([$cmpId, $fyS, $fyE, $date], $methods);

		// Make placeholders for the ARRAY[...] part
		$ph = implode(',', array_fill(0, count($methods), '?'));
        $sql =
          "UPDATE itmsnapsht
              SET itm_snapshot_is_dirty = TRUE
            WHERE cmp_id = ?
              AND itm_snapshot_date BETWEEN ? AND ?
              AND itm_snapshot_date >= ?
               AND val_method_id = ANY(ARRAY[$ph]::text[])";
        if ($key) { $sql .= " AND itm_id_unit_id = ?"; $params[] = $key; }

        $postgr_db->query($sql, $params);
		SaveErrorLog($postgr_db->getlastquery());
	}  
   
}