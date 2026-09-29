<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Libraries\externaldb;

class GstexportModel extends Model	{
public function __construct() {
        parent::__construct();        
       $this->externaldb    = new externaldb();	
	   $this->dberpunvrsl   = $this->externaldb->erp_db();
	   $this->session       = \Config\Services::session();
	   $this->company_id    = $this->session->get('ses_company_id');
	   $this->aicountly_db  = $this->externaldb->aicountly_db();
	   $this->bo_id = $this->session->get('ses_boid');
    }

private function isValidGstin(?string $gstin): bool
{
    $gstin = trim((string)$gstin);
    if (strlen($gstin) !== 15) {
        return false;
    }

    if (function_exists('inputmask_gstin')) {
        return inputmask_gstin($gstin) ? true : false;
    }

    return true;
}
private function safeDate(string $date): string
{
    if (empty($date) || strtotime($date) === false) {
        return '';
    }
    return date('d-m-Y', strtotime($date));
}

private function round2($value): float
{
    return round((float)$value, 2, PHP_ROUND_HALF_UP);
}
function get_party_info($voucher_txn_id){
		$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($comp_txn_tbl);
    	$builder->where('voucher_txn_id', $voucher_txn_id);
		$builder->where('master_id_type', 'acc');
    	$builder->orderBy('txn_id', 'asc');
    	$builder->limit(1);
    	$comp_txn = $builder->get()->getRowArray();
		return $comp_txn;
	}
	
  function gstroutsup_info($voucher_txn_id){
	 $gstroutsup_tbl = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');	
     $response = $this->db->table($gstroutsup_tbl)->where('voucher_txn_id',$voucher_txn_id)->get()->getRowArray();
	 return $response;			
	}	
 function gstrinwsup_info($voucher_txn_id){
	 $gstrinwsup_tbl = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');	
     $response = $this->db->table($gstrinwsup_tbl)->where('voucher_txn_id',$voucher_txn_id)->get()->getRowArray();
	 return $response;			
	}
		
 function GetVoucherInvoiceVal($from_date,$to_date,$vch_txn_id,$cmp_tax_short_code=''){
	$acctgstsum_tbl  = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	$builders        = $this->db->table($acctgstsum_tbl.' gstsum');
	$builders->orderBy('gstsum.vch_txn_id');	        
	$builders->where('gstsum.acc_txn_date >=', $from_date);
	$builders->where('gstsum.acc_txn_date <=', $to_date); 
	$builders->where('gstsum.vch_txn_id', $vch_txn_id);
	if($cmp_tax_short_code)
	$builders->whereNotIn('gstsum.cmp_tax_short_code', $cmp_tax_short_code);
	
	$response = $builders->get()->getResultArray();
	//echo $this->db->GetLastQuery();
	//echo '<br>';
	$final_sum =0;
	$items    = array();
	if($response){
	  foreach($response as $row){
		 if(parseAmount($row["acc_igst_rate"])==0 || parseAmount($row["acc_igst"])==0){
			$rt = parseAmount($row["acc_cgst_rate"])+parseAmount($row["acc_sgst_rate"]);
			$iamt = parseAmount($row["acc_cgst"])+parseAmount($row["acc_sgst"]);
		    
			$samt = parseAmount($row["acc_sgst"]);
		    $camt = parseAmount($row["acc_cgst"]);
		    
			
			$items[]= array("num"=>(integer)$row["tgsmid"],"itm_det"=>array("txval"=>parseAmount($row["taxable_amt"]),"rt"=>(integer)$rt,"samt"=>parseAmount($samt),"camt"=>parseAmount($camt),"csamt"=>parseAmount($row["acc_cess"]) )); 
		
		 
		 }else{
			$rt = parseAmount($row["acc_igst_rate"]);
			$iamt = parseAmount($row["acc_igst"]);
		 	$items[]= array("num"=>(integer)$row["tgsmid"],"itm_det"=>array("txval"=>parseAmount($row["taxable_amt"]),"rt"=>(integer)$rt,"iamt"=>parseAmount($iamt),"csamt"=>parseAmount($row["acc_cess"]) )); 
		 
		 } 
		  
		  $final_sum = $final_sum+($row["taxable_amt"]+$row["total_tax"]); 
		 
	     }	
	   }	  
	 return array("total_val"=>parseAmount($final_sum),"items"=>$items);
	 
	}
	
 function account_info($account_id){	 
		$account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
		return $this->db->table($account_master_tbl)->where('acc_id', $account_id)->get()->getRowArray();   	   
	}
	
public function load_eway_listing(
    $from_date,
    $to_date,
    $view,
    $search,
    $voucher_txn_id = 0,
    $ewbval = 1
) {
    // --- 1. Get request parameters and set up initial variables ---
    $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
    $pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10;
    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP < 1)     $pq_rPP = 10;

    $from_date = date('Y-m-d', strtotime($_POST["from_date"]));
    $to_date   = date('Y-m-d', strtotime($_POST["to_date"]));
    $cid       = $this->company_id;

    $search = '';
    $search_column = '';
    if (!empty($_POST["pq_filter"])) {
        $pq_filter = json_decode($_POST["pq_filter"], true);
        if (isset($pq_filter['data'][0])) {
            $search        = $pq_filter['data'][0]['value'];
            $search_column = $pq_filter['data'][0]['dataIndx'];
        }
    }

    // --- 2. Create the Base Query ---
    $base = $this->db->table('vchtxnconso v');

    // --- SELECT Clause ---
    $base->select("
        v.vch_txn_id, v.vch_type_id, v.vch_series_id, v.vch_date, vt.vch_name,
        ewbm.ewb_id, ewbm.ewb_no, ewbm.ewb_status,ewbm.ewb_date, ewbm.ewb_valid_dt,   
        ewb_partb.trans_veh_no,ewb_partb.gsttpt_id,  
        COALESCE(SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END), 0) AS debit_total,
        COALESCE(SUM(CASE WHEN a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END), 0) AS credit_total,
        COALESCE(SUM(gst_sum.vch_taxable_value + gst_sum.vch_total_tax), 0) AS total_invoice_value
    ", false);
    
    $base->select("
        (SELECT acp.acc_name
         FROM cmptxnmstn cm_p
         JOIN acctmaster acp ON acp.acc_id = cm_p.master_id
         WHERE cm_p.vch_txn_id = v.vch_txn_id AND cm_p.master_id_type = 'acc'
         ORDER BY cm_p.txn_id ASC LIMIT 1) AS party_name
    ", false);

    // --- JOINs ---
    $base->join('vchtypemst vt', 'vt.vch_type_id = v.vch_type_id', 'left');
    $base->join('accttxnmst a', 'a.vch_txn_id = v.vch_txn_id', 'left');
    $base->join('vchgstsumn gst_sum', 'gst_sum.vch_txn_id = v.vch_txn_id', 'left');
    $base->join('ewbmastern ewbm', 'ewbm.vch_txn_id = v.vch_txn_id', 'left');
    $base->join('ewbpartbdt ewb_partb', 'ewb_partb.vch_txn_id = v.vch_txn_id', 'left');

    // --- WHERE Clause (Filters) ---
    $base->where('v.cmp_id', $cid);
    $base->where('v.hobo_id', $this->bo_id);
    $base->where('a.acc_txn_type', 1);
    $base->where('v.vch_type_id !=', 8);
    $base->where('v.vch_date >=', $from_date);
    $base->where('v.vch_date <=', $to_date);

    if ($search !== '' && $search_column !== '') {
        $base->groupStart();
        switch ($search_column) {
            case 'date': $base->like('v.vch_date::text', date('Y-m-d', strtotime($search))); break;
            case 'voucher_type': $base->like('LOWER(vt.vch_name)', strtolower($search)); break;
            case 'ewb_no': $base->like('LOWER(ewbm.ewb_no)', strtolower($search)); break;
            case 'trans_veh_no': $base->like('LOWER(ewb_partb.trans_veh_no)', strtolower($search)); break;
        }
        $base->groupEnd();
    }
    
    // ** CORRECTED SYNTAX for $ewbval WHERE clauses **
    if ($ewbval == 3) {
        $base->where('ewbm.ewb_no IS NULL');
    }
    if ($ewbval == 4) {
        $base->where('ewbm.ewb_no IS NOT NULL', null, false);
        $base->where('ewbm.ewb_status', 'PART-B PENDING');
    }
    if ($ewbval == 6) {
        $base->where('ewbm.ewb_no IS NOT NULL', null, false);
        $base->where('ewbm.ewb_status', 'ACTIVE');
    }

    // --- GROUP BY Clause ---
    $base->groupBy([
        'v.vch_txn_id', 'v.vch_type_id', 'v.vch_series_id', 'v.vch_date', 'vt.vch_name',
        'ewbm.ewb_id', 'ewbm.ewb_no', 'ewbm.ewb_status', 'ewbm.ewb_date', 'ewbm.ewb_valid_dt',   
        'ewb_partb.trans_veh_no', 'ewb_partb.gsttpt_id'
    ]);
    
    // --- HAVING Clause ---
    if ($ewbval == 2) {
        $base->having('COALESCE(SUM(gst_sum.vch_taxable_value + gst_sum.vch_total_tax), 0) >=', 50000);
    }
    if ($ewbval == 5) {
        $base->having('COALESCE(SUM(gst_sum.vch_taxable_value + gst_sum.vch_total_tax), 0) <', 50000);
    }

    // --- 3. Get Total Record Count ---
    $countSql = $base->getCompiledSelect(false);
    $countQuery = $this->db->query("SELECT COUNT(*) as total FROM ({$countSql}) AS count_subquery");
    $total_Records = (int)($countQuery->getRow()->total ?? 0);

    // --- 4. Get Paginated Data ---
    $pq_curPage = max(1, $pq_curPage);
    $offset = $pq_rPP * ($pq_curPage - 1);
    
    $result = $base
        ->orderBy('v.vch_date', 'ASC')
        ->orderBy('v.vch_txn_id', 'ASC')
        ->limit($pq_rPP, $offset)
        ->get()
        ->getResultArray();

    // --- 5. Process and Format Data for Output ---
    $data = [];
    $voucher_count = $offset; 

    foreach ($result as $value) {
        $voucher_count++;
        $debit_total = (float)$value['debit_total'];
        $credit_total = (float)$value['credit_total'];
        $total_invoice_value = (float)$value['total_invoice_value']; 
        $ewb_id = $value['ewb_id'];

        switch ((int)$value['vch_type_id']) {
            case 18: case 13: case 3: $credit_total = 0.0; break;
            case 11: case 9:  case 2: $debit_total = 0.0; break;
        }
		$eway_no=$ewb_status=$ewb_expiry='';
				
		 $ewb_status ='VALIDATED';  	
		 $pq_cellattr = ''; 
		 $is_eway_expired = "0";
		 if($value['ewb_no']!=''){
		  $eway_no    = trim($value['ewb_no']);	
		  
		  if(time() >strtotime($value['ewb_valid_dt'])){
			 $is_eway_expired = '1'; 					
		  }
		  
		  $ewb_expiry = date('d M, Y h:i A',strtotime($value['ewb_valid_dt']));
		 }
		 if($value['ewb_no']==''){
			$ewb_status ='NOT GENERATED';  
		   }
		 if(!empty($value['ewb_no']) && (trim($value['ewb_status'])=='PART-B PENDING')){
			$ewb_status ='PART-B PENDING';  					
		   } 
		 if(!empty($value['ewb_no']) && (trim($value['ewb_status'])=='ACTIVE')){
			   $ewb_status ='ACTIVE';  
		   }
		  				
		
		if(empty($eway_no) && $total_invoice_value >=50000){
		 $pq_rowattr = array("style"=>array("font-weight"=>"bold"));	
		}
		else if($total_invoice_value<50000){
		 $pq_rowattr = array("style"=>array("font-style"=>"italic"));	
		}
		else{
		$pq_rowattr=[];	
		}	
		$date   = date("d-m-Y", strtotime($value['vch_date']));
		
		if($ewb_status=='PART-B PENDING')
		  $eway_action ='<a href="javascript:void(0);" title="Update PART-B" data-ajax="'.$eway_no.'"  data-title="'.$value['gsttpt_id'].'" data-id="'.$value['vch_txn_id'].'" class="update_partb_status"><img src="'.base_url().'/public/assets/img/manage_history.png" title="Update PART-B"></a>&nbsp;&nbsp;<a href="javascript:void(0);" title="Refresh E-Way Status" data-ajax="'.$eway_no.'"  data-tablelkey="'.$ewb_id.'" data-title="'.$value['gsttpt_id'].'" data-id="'.$value['vch_txn_id'].'" class="refresh_eway_status"><img src="'.base_url().'/public/assets/img/refresh.png" width="24" height="24" title="Refresh E-Way Status"></a>';
		  
		else
			$eway_action ='<a href="javascript:void(0);" data-ajax="'.$eway_no.'" data-id="'.$value['vch_txn_id'].'" style="cursor:not-allowed;opacity: 0.4;"><img src="'.base_url().'/public/assets/img/manage_history.png"></a>';
				
				
        $data[] = [
            'checkbox'        => '<input name="voucher_ids[]" class="checkbox hidden voucher_row" data-id="'.$value['vch_series_id'].'||'.$value['vch_txn_id'].'" type="checkbox" value="'.$value['vch_series_id'].'||'.$value['vch_txn_id'].'">',
            'particulars'     => $value['party_name'],
            'date'            => date("d-m-Y", strtotime($value['vch_date'])),
            'credit'          => ($credit_total > 0) ? formatAmount($credit_total) : '',
            'debit'           => ($debit_total > 0) ? formatAmount($debit_total) : '',
            'credit_total'    => $credit_total,
            'debit_total'     => $debit_total,
            'voucher_no'      => $voucher_count,
            'voucher_type'    => $value['vch_name'],
            'voucher_type_id' => (int)$value['vch_type_id'],
            'voucher_txn_id'  => $value['vch_txn_id'],
            'eway_no'         => $value['ewb_no'],
            'eway_status'     => $ewb_status,
			'eway_expiry'     => $ewb_expiry,
			'is_eway_expired' => $is_eway_expired,
            'trans_veh_no'    => $value['trans_veh_no'],
			'eway_action'     => $eway_action,
            'pq_rowattr'      => $pq_rowattr,
        ];
    }

    return "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":" . json_encode($data) . "}";
}

 public function load_einvoice_listing(
    $from_date,
    $to_date,
    $view,
    $search,
    $voucher_txn_id = 0,
    $einvval = 1
) {

    $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
    $pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10;

    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP < 1 && $pq_rPP != -1) $pq_rPP = 10;

    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date   = date('Y-m-d', strtotime($to_date));
    $cid       = $this->company_id;

    $search_column = '';
    if (!empty($_POST["pq_filter"])) {
        $pq_filter = json_decode($_POST["pq_filter"], true);
        if (isset($pq_filter['data'][0])) {
            $search_column = $pq_filter['data'][0]['dataIndx'];
        }
    }

    // ✅ BASE QUERY
    $base = $this->db->table('vchtxnconso v');

    $base->select("
        v.vch_txn_id,
        v.vch_date,
        v.vch_type_id,
        v.vch_series_id,
        vt.vch_name as voucher_type,

        einv.einv_id,
        einv.einv_no,
        einv.env_date,
        einv.einv_valid_dt,
        einv.env_irn,
        einv.env_status,

        (SELECT acp.acc_name
         FROM cmptxnmstn cm_p
         JOIN acctmaster acp ON acp.acc_id = cm_p.master_id
         WHERE cm_p.vch_txn_id = v.vch_txn_id
         AND cm_p.master_id_type = 'acc'
         ORDER BY cm_p.txn_id ASC
         LIMIT 1) AS particulars,

        -- ✅ FIX: Avoid duplicate by aggregation
        MIN(g_out.outsup_bill_ref_no) as billno,

        (SELECT a.acc_txn_amt FROM accttxnmst a WHERE a.vch_txn_id = v.vch_txn_id AND a.acc_txn_dr_cr = 1 LIMIT 1) as debit_total,
        (SELECT a.acc_txn_amt FROM accttxnmst a WHERE a.vch_txn_id = v.vch_txn_id AND a.acc_txn_dr_cr = 2 LIMIT 1) as credit_total
    ", false);

    // ✅ FIXED JOIN
    $base->join('vchtypemst vt', 'vt.vch_type_id = v.vch_type_id', 'left');
    $base->join('gstroutsup g_out', 'g_out.vch_txn_id = v.vch_txn_id', 'left'); // 🔥 FIX
    $base->join('einvmaster einv', 'einv.vch_txn_id = v.vch_txn_id', 'left');

    // ✅ FILTERS
    $base->where('v.cmp_id', $cid);
    $base->where('v.hobo_id', $this->bo_id);
    $base->where('v.vch_date >=', $from_date);
    $base->where('v.vch_date <=', $to_date);
    $base->whereIn('v.vch_type_id', [18, 2]);

    if ($voucher_txn_id > 0) {
        $base->where("v.vch_txn_id", $voucher_txn_id);
    }

    // ✅ GROUPING (VERY IMPORTANT)
    $base->groupBy([
        'v.vch_txn_id',
        'v.vch_date',
        'v.vch_type_id',
        'v.vch_series_id',
        'vt.vch_name',
        'einv.einv_id',
        'einv.einv_no',
        'einv.env_date',
        'einv.einv_valid_dt',
        'einv.env_irn',
        'einv.env_status'
    ]);

    // ✅ E-INVOICE FILTER
    if ($einvval == 2) {
        $base->where('einv.einv_no IS NULL');
    } elseif ($einvval == 3) {
        $base->where('einv.env_status', 2);
    } elseif ($einvval == 4) {
        $base->where('einv.env_status', 1);
    }

    // ✅ COUNT FIX
    $countBuilder = clone $base;
    $total_Records = count($countBuilder->get()->getResultArray());

    // ✅ PAGINATION
    $offset = ($pq_rPP != -1) ? $pq_rPP * ($pq_curPage - 1) : 0;

    $result = $base
        ->orderBy('v.vch_date', 'DESC')
        ->orderBy('v.vch_txn_id', 'DESC')
        ->limit($pq_rPP, $offset)
        ->get()
        ->getResultArray();

    // ✅ OUTPUT
    $data = [];

    foreach ($result as $value) {

        $debit_total  = (float)$value['debit_total'];
        $credit_total = (float)$value['credit_total'];

        $einv_status = 'NOT GENERATED';
        $pq_rowattr = [];

        if (!empty($value['einv_no'])) {
            if ($value['env_status'] == 1) {
                $einv_status = 'ACTIVE';
                $pq_rowattr = ["style" => ["font-weight" => "bold"]];
            } elseif ($value['env_status'] == 2) {
                $einv_status = 'CANCELLED';
            }
        } else {
            $pq_rowattr = ["style" => ["font-style" => "italic"]];
        }

        $einv_expiry = !empty($value['einv_valid_dt'])
            ? date('d M, Y h:i A', strtotime($value['einv_valid_dt']))
            : '';

        $data[] = [
            'checkbox'        => '<input type="checkbox" value="'.$value['vch_txn_id'].'">',
            'particulars'     => $value['particulars'],
            'date'            => date("d-m-Y", strtotime($value['vch_date'])),
            'debit'           => ($debit_total > 0) ? formatAmount($debit_total) : '',
            'credit'          => ($credit_total > 0) ? formatAmount($credit_total) : '',
            'voucher_type'    => $value['voucher_type'],
            'voucher_txn_id'  => $value['vch_txn_id'],
            'billno'          => $value['billno'],
            'einvoice_status' => $einv_status,
            'einvoice_expiry' => $einv_expiry,
            'pq_rowattr'      => $pq_rowattr,
            'einv_no'         => $value['einv_no']
        ];
    }

    return json_encode([
        "totalRecords" => $total_Records,
        "curPage"      => $pq_curPage,
        "data"         => $data
    ]);
}
 public function load_einvoice_listing_11_04_2026(
    $from_date,
    $to_date,
    $view,
    $search,
    $voucher_txn_id = 0,
    $einvval = 1
) {
    // --- 1. Get request parameters ---
    $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
    $pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10;
    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP < 1 && $pq_rPP != -1) $pq_rPP = 10;
    
    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date   = date('Y-m-d', strtotime($to_date));
    $cid       = $this->company_id;
    
    $search_column = '';
    if (!empty($_POST["pq_filter"])) {
        $pq_filter = json_decode($_POST["pq_filter"], true);
        if (isset($pq_filter['data'][0])) {
            $search_column = $pq_filter['data'][0]['dataIndx'];
        }
    }

    // --- 2. Create the Base Query ---
    $base = $this->db->table('vchtxnconso v');

    // --- SELECT Clause ---
    $base->select("
        v.vch_txn_id, v.vch_date, v.vch_type_id, v.vch_series_id,
        vt.vch_name as voucher_type,
        -- ** CORRECTED THE FIELD NAME HERE **
        einv.einv_id, einv.einv_no, einv.env_date, einv.einv_valid_dt, einv.env_irn, einv.env_status,
        -- Get Party Name (Particulars) directly
        (SELECT acp.acc_name
         FROM cmptxnmstn cm_p JOIN acctmaster acp ON acp.acc_id = cm_p.master_id
         WHERE cm_p.vch_txn_id = v.vch_txn_id AND cm_p.master_id_type = 'acc'
         ORDER BY cm_p.txn_id ASC LIMIT 1) AS particulars,
        g_out.outsup_bill_ref_no as billno,
        -- Calculate Debit/Credit for the primary party
        (SELECT a.acc_txn_amt FROM accttxnmst a WHERE a.vch_txn_id = v.vch_txn_id AND a.acc_txn_dr_cr = 1 LIMIT 1) as debit_total,
        (SELECT a.acc_txn_amt FROM accttxnmst a WHERE a.vch_txn_id = v.vch_txn_id AND a.acc_txn_dr_cr = 2 LIMIT 1) as credit_total
    ", false);

    // --- JOINs ---
    $base->join('vchtypemst vt', 'vt.vch_type_id = v.vch_type_id', 'left');
    $base->join('gstroutsup g_out', 'g_out.vch_txn_id = v.vch_txn_id', 'inner');
    $base->join('einvmaster einv', 'einv.vch_txn_id = v.vch_txn_id', 'left');

    // --- WHERE Clauses (Filters) ---
    $base->where('v.cmp_id', $cid);
    $base->where('v.hobo_id', $this->bo_id);
    $base->where('v.vch_date >=', $from_date);
    $base->where('v.vch_date <=', $to_date);

    if ($voucher_txn_id > 0) {
        $base->where("v.vch_txn_id", $voucher_txn_id);
    }


   // $base->whereNotIn("g_out.outsup_inv_type", ['B2CS', 'B2CL']);
    $base->whereIn('v.vch_type_id', [18, 2]);

    if ($search !== '' && $search_column !== '') {
        // Search logic remains here
    }
    
    // E-Invoice status filters
    if ($einvval == 2) {
        $base->where('einv.einv_no IS NULL');
    } elseif ($einvval == 3) {
        $base->where('einv.einv_status', 2); // CANCELLED
    } elseif ($einvval == 4) {
        $base->where('einv.einv_status', 1); // ACTIVE
    }

    // --- 3. Get Total Record Count ---
    $countBuilder = clone $base;
    $total_Records = $countBuilder->countAllResults();

    // --- 4. Get Paginated Data ---
    $offset = 0;
    if ($pq_rPP != -1) {
        $offset = $pq_rPP * ($pq_curPage - 1);
    }
    
    $result = $base
        ->orderBy('v.vch_date', 'DESC')->orderBy('v.vch_txn_id', 'DESC')
        ->limit($pq_rPP, $offset)
        ->get()
        ->getResultArray();

    // --- 5. Process and Format Data for Output ---
    $data = [];
    foreach ($result as $value) {
        $debit_total = (float)$value['debit_total'];
        $credit_total = (float)$value['credit_total'];
        
        $einv_status = 'NOT GENERATED';
        $pq_rowattr = [];

        if (!empty($value['einv_no'])) {
            if ($value['einv_status'] == 1) { // ACTIVE
                $einv_status = 'ACTIVE';
                $pq_rowattr = ["style" => ["font-weight" => "bold"]];
            } elseif ($value['einv_status'] == 2) { // CANCELLED
                $einv_status = 'CANCELLED';
            }
        } else {
             $pq_rowattr = ["style" => ["font-style" => "italic"]];
        }

        $einv_expiry = !empty($value['einv_valid_dt']) ? date('d M, Y h:i A', strtotime($value['einv_valid_dt'])) : '';

        $einv_action = '<a href="javascript:void(0);" style="cursor:not-allowed;opacity: 0.4;"><img src="'.base_url('/public/assets/img/refresh.png').'" width="24" height="24"></a>';
        if ($einv_status == 'ACTIVE') {
            $einv_action = '<a href="javascript:void(0);" title="Refresh E-Invoice Status" data-ajax="'.$value['einv_irn'].'" data-id="'.$value['vch_txn_id'].'" class="refresh_einv_status"><img src="'.base_url('/public/assets/img/refresh.png').'" width="24" height="24" title="Refresh E-Invoice Status"></a>';
        }
        
        $data[] = [
            'checkbox'        =>'<input name="voucher_ids[]" class="eway_one_checkbox" data-ajax="Voucher No: '.$value['vch_series_id'].'" data-id="'.$value['vch_txn_id'].'" type="checkbox" value="'.$value['vch_txn_id'].'">',
            'particulars'     => $value['particulars'],
            'date'            => date("d-m-Y", strtotime($value['vch_date'])),
            'debit'           => ($debit_total > 0) ? formatAmount($debit_total) : '',
            'credit'          => ($credit_total > 0) ? formatAmount($credit_total) : '',
            'debit_total'     => $debit_total,
            'credit_total'    => $credit_total,
            'voucher_type'    => $value['voucher_type'],
            'voucher_txn_id'  => $value['vch_txn_id'],
            'billno'          => $value['billno'],
            'einvoice_status' => $einv_status,
            'einvoice_expiry' => $einv_expiry,
            'einvoice_action' => $einv_action,
            'pq_rowattr'      => $pq_rowattr,
            'einv_no'         => $value['einv_no']
        ];
    }
    
    return "{\"totalRecords\":".$total_Records.",\"curPage\":".$pq_curPage.",\"data\":".json_encode($data)."}";
}

function get_eway_voucher_info($voucher_txn_id){
	  	// BASE QUERY
       $base = $this->db->table('vchtxnconso v')
        ->select('v.vch_txn_id, v.vch_type_id, v.vch_series_id, v.vch_date, vt.vch_name')
        // Party name from cmptxnmstn (first acc for this voucher)
        ->select("
            (
                SELECT acp.acc_name
                FROM cmptxnmstn cm_p
                JOIN acctmaster acp ON acp.acc_id = cm_p.master_id
                WHERE cm_p.cmp_id = v.cmp_id
                  AND cm_p.vch_txn_id = v.vch_txn_id
                  AND cm_p.master_id_type = 'acc'
                ORDER BY cm_p.txn_id ASC
                LIMIT 1
            ) AS party_name
        ", false)
        // Aggregated ledger names (distinct, ordered by name to satisfy PG)
        ->select("
            string_agg(DISTINCT ac.acc_name, ', ' ORDER BY ac.acc_name) AS acc_names
        ", false)
        // Amounts
        ->select("
            COALESCE(SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END), 0) AS debit_total,
            COALESCE(SUM(CASE WHEN a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END), 0) AS credit_total
        ", false)
        ->join('vchtypemst vt', 'vt.vch_type_id = v.vch_type_id', 'left', false)
        ->join('vchseriesn vs', 'vs.vch_series_id = v.vch_series_id', 'left', false)
        ->join('accttxnmst a', 'a.vch_txn_id = v.vch_txn_id AND a.cmp_id = v.cmp_id AND a.hobo_id = v.hobo_id', 'left', false)
        ->join('acctmaster ac', 'ac.acc_id = a.acc_id', 'left', false)
		->join('ewbmastern ewbmst', 'ewbmst.vch_txn_id = a.vch_txn_id', 'left', false)
        ->where('v.cmp_id', $this->company_id)
        ->where('v.hobo_id', $this->bo_id)
		->where('a.acc_txn_type', 1)
        ->where('a.vch_txn_id', $voucher_txn_id)
        ->where('v.vch_type_id !=', 8);
		// GROUP BY non-aggregates
		$base->groupBy('v.vch_txn_id, v.vch_type_id, v.vch_series_id, v.vch_date, vt.vch_name');

        $result = $base
         ->orderBy('v.vch_date', 'ASC')
         ->orderBy('v.vch_txn_id', 'ASC')
         ->get()
         ->getResultArray();
		
    $data = [];
    foreach ($result as $value) {       
        $voucher_txn_id  = $value['vch_txn_id'];
        $voucher_type_id = (int)$value['vch_type_id'];
        // Prefer party_name; fallback to aggregated ledger names
        $particulars  = $value['party_name'] ?: $value['acc_names'];
        $credit_total = (float)$value['credit_total'];
        $debit_total  = (float)$value['debit_total'];
        switch ($voucher_type_id) {
            case 18:
            case 13:
            case 3:
                $credit_total = 0.0;
                break;
            case 11:
            case 9:
            case 2:
                $debit_total = 0.0;
                break;
            default:
                break;
        }

        $date = date("d-m-Y", strtotime($value['vch_date']));
        $data[] = [
            'checkbox'        => '<input name="voucher_ids[]" class="checkbox hidden voucher_row" data-id="'.$value['vch_series_id'].'||'.$value['vch_txn_id'].'" type="checkbox" value="'.$value['vch_series_id'].'||'.$value['vch_txn_id'].'">',
            'particulars'     => $particulars,
            'date'            => $date,
            'credit'          => ($credit_total > 0) ? formatAmount($credit_total) : '',
            'debit'           => ($debit_total > 0) ? formatAmount($debit_total) : '',
            'credit_total'    => parseAmount($credit_total),
            'debit_total'     => parseAmount($debit_total),
            'voucher_type'    => $value['vch_name'],
            'voucher_type_id' => $voucher_type_id,
            'voucher_txn_id'  => $voucher_txn_id,
            'bom_id'          => 0,
            'bom_batches'     => 0
        ];
       }
		return  "{\"totalRecords\":" . count($data) . ",\"curPage\":1,\"data\":".json_encode($data)."}";
	}
 
 function get_einvoice_voucher_info($voucher_txn_id){
	  	// BASE QUERY
       $base = $this->db->table('vchtxnconso v')
        ->select('v.vch_txn_id, v.vch_type_id, v.vch_series_id, v.vch_date, vt.vch_name')
        // Party name from cmptxnmstn (first acc for this voucher)
        ->select("
            (
                SELECT acp.acc_name
                FROM cmptxnmstn cm_p
                JOIN acctmaster acp ON acp.acc_id = cm_p.master_id
                WHERE cm_p.cmp_id = v.cmp_id
                  AND cm_p.vch_txn_id = v.vch_txn_id
                  AND cm_p.master_id_type = 'acc'
                ORDER BY cm_p.txn_id ASC
                LIMIT 1
            ) AS party_name
        ", false)
        // Aggregated ledger names (distinct, ordered by name to satisfy PG)
        ->select("
            string_agg(DISTINCT ac.acc_name, ', ' ORDER BY ac.acc_name) AS acc_names
        ", false)
        // Amounts
        ->select("
            COALESCE(SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END), 0) AS debit_total,
            COALESCE(SUM(CASE WHEN a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END), 0) AS credit_total
        ", false)
        ->join('vchtypemst vt', 'vt.vch_type_id = v.vch_type_id', 'left', false)
        ->join('vchseriesn vs', 'vs.vch_series_id = v.vch_series_id', 'left', false)
        ->join('accttxnmst a', 'a.vch_txn_id = v.vch_txn_id AND a.cmp_id = v.cmp_id AND a.hobo_id = v.hobo_id', 'left', false)
        ->join('acctmaster ac', 'ac.acc_id = a.acc_id', 'left', false)
		->join('einvmaster einv', 'einv.vch_txn_id = a.vch_txn_id', 'left', false)
        ->where('v.cmp_id', $this->company_id)
        ->where('v.hobo_id', $this->bo_id)
		->where('a.acc_txn_type', 1)
        ->where('a.vch_txn_id', $voucher_txn_id)
        ->where('v.vch_type_id !=', 8);
		// GROUP BY non-aggregates
		$base->groupBy('v.vch_txn_id, v.vch_type_id, v.vch_series_id, v.vch_date, vt.vch_name');

        $result = $base
         ->orderBy('v.vch_date', 'ASC')
         ->orderBy('v.vch_txn_id', 'ASC')
         ->get()
         ->getResultArray();
		
    $data = [];
    foreach ($result as $value) {       
        $voucher_txn_id  = $value['vch_txn_id'];
        $voucher_type_id = (int)$value['vch_type_id'];
        // Prefer party_name; fallback to aggregated ledger names
        $particulars  = $value['party_name'] ?: $value['acc_names'];
        $credit_total = (float)$value['credit_total'];
        $debit_total  = (float)$value['debit_total'];
        switch ($voucher_type_id) {
            case 18:
            case 13:
            case 3:
                $credit_total = 0.0;
                break;
            case 11:
            case 9:
            case 2:
                $debit_total = 0.0;
                break;
            default:
                break;
        }

        $date = date("d-m-Y", strtotime($value['vch_date']));
        $data[] = [
            'checkbox'        => '<input name="voucher_ids[]" class="checkbox hidden voucher_row" data-id="'.$value['vch_series_id'].'||'.$value['vch_txn_id'].'" type="checkbox" value="'.$value['vch_series_id'].'||'.$value['vch_txn_id'].'">',
            'particulars'     => $particulars,
            'date'            => $date,
            'credit'          => ($credit_total > 0) ? formatAmount($credit_total) : '',
            'debit'           => ($debit_total > 0) ? formatAmount($debit_total) : '',
            'credit_total'    => parseAmount($credit_total),
            'debit_total'     => parseAmount($debit_total),
            'voucher_type'    => $value['vch_name'],
            'voucher_type_id' => $voucher_type_id,
            'voucher_txn_id'  => $voucher_txn_id,
            'bom_id'          => 0,
            'bom_batches'     => 0
        ];
       }
		return  "{\"totalRecords\":" . count($data) . ",\"curPage\":1,\"data\":".json_encode($data)."}";
	}
		  
 function get_voucher_narrationinfo($voucher_txn_id,$narr_type,$txn_id){
	 if($narr_type=='long'){
		 // get txn id from comptxnmst table 
		$comptxnmst_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id'); 
		$txn_row =  $this->db->table($comptxnmst_tbl)->where('voucher_txn_id',$voucher_txn_id)->where('comp_id',$this->company_id)->where('master_id','0')->where('master_id_type','nrr')->get()->getRowArray(); 
		 
		$txn_id  = $txn_row['txn_id'] ?? 0;
        $long_narr_tbl = $this->company_id.'_long_narrn_'.$this->session->get('ses_comp_fy_id');
		return $this->db->table($long_narr_tbl)->where('txn_id',$txn_id)->where('vch_txn_id',$voucher_txn_id)->get()->getRowArray();
		
	 }
	else  if($narr_type=='short'){
        $short_narr_tbl = $this->company_id.'_short_narr_'.$this->session->get('ses_comp_fy_id');
		return $this->db->table($short_narr_tbl)->where('txn_id',$txn_id)->where('vch_txn_id',$voucher_txn_id)->get()->getRowArray();
	}		
	  
  }		
 function get_partytransaction($voucher_txn_id)
    {
    	$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($comp_txn_tbl);
    	$builder->where('voucher_txn_id', $voucher_txn_id);
    	$builder->where('master_id_type', 'acc');
    	$builder->orderBy('txn_id', 'asc');
    	$builder->limit(1);
    	$comp_txn = $builder->get()->getRowArray();

    	$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$comp_txn['master_id'].'_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($acc_txn_tbl);
    	$builder->where('voucher_txn_id', $voucher_txn_id);
		
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));

    	$builder->where('acc_id', $comp_txn['master_id']);
    	$result = $builder->get()->getRowArray();
		if(!$result){
			$result['acc_txn_narr']='';$result['acc_id']='';
		}
		else{
		$get_narration_info = $this->get_voucher_narrationinfo($voucher_txn_id,'short',$result['txn_id']);
		if($get_narration_info){
			$result['acc_txn_narr']=$get_narration_info['vch_short_narr'];
		}else
			$result['acc_txn_narr']='';	
		}		
    	return $result;
    }
 

 function get_accountinfo($acc_id){	 
	  	$account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	  	return $this->db->table($account_master_tbl)->where('acc_id', $acc_id)->get()->getRowArray();   	   
    }
 public function validate_gstr1_json_strict(array $data): array
{
    try {

        $errors = [
            'invoices' => [],
            'hsn'      => [],
            'general'  => []
        ];

        // =========================
        // HELPERS
        // =========================
        $isValidDate = function ($date) {
            if (!$date) return false;
            $d = \DateTime::createFromFormat('d-m-Y', $date);
            return $d && $d->format('d-m-Y') === $date;
        };

        $isValidUQC = function ($uqc) {
            return preg_match('/^[A-Z]{2,3}$/', $uqc);
        };

        // =========================
        // HEADER
        // =========================
        if (!$this->isValidGstin($data['gstin'] ?? '')) {
            $errors['general'][] = "Invalid GSTIN";
        }

        if (empty($data['fp']) || !preg_match('/^\d{6}$/', $data['fp'])) {
            $errors['general'][] = "Invalid Filing Period (MMYYYY)";
        }

        // =========================
        // B2B VALIDATION
        // =========================
        if (!empty($data['b2b'])) {
            foreach ($data['b2b'] as $b2b) {

                $ctin = $b2b['ctin'] ?? '';

                foreach ($b2b['inv'] ?? [] as $inv) {

                    $invNo = $inv['inum'] ?? 'Unknown Invoice';

                    if (!$this->isValidGstin($ctin)) {
                        $errors['invoices'][$invNo][] = "Invalid customer GSTIN";
                    }

                    if (!$isValidDate($inv['idt'] ?? '')) {
                        $errors['invoices'][$invNo][] = "Invalid invoice date";
                    }

                    if (!preg_match('/^\d{2}$/', $inv['pos'] ?? '')) {
                        $errors['invoices'][$invNo][] = "Invalid POS";
                    }

                    $itemIndex = 1;
                    $calcTotal = 0;

                    foreach ($inv['itms'] ?? [] as $item) {

                        if (($item['num'] ?? 0) != $itemIndex) {
                            $errors['invoices'][$invNo][] = "Item numbering must be sequential starting from 1";
                        }

                        $det = $item['itm_det'] ?? [];

                        // ✅ ALWAYS CHECK csamt
                        if (!array_key_exists('csamt', $det)) {
                            $errors['invoices'][$invNo][] = "Missing csamt in item";
                        }

                        $txval = (float)($det['txval'] ?? 0);
                        $rt    = (float)($det['rt'] ?? 0);

                        $iamt = (float)($det['iamt'] ?? 0);
                        $camt = (float)($det['camt'] ?? 0);
                        $samt = (float)($det['samt'] ?? 0);

                        if ($iamt > 0 && ($camt > 0 || $samt > 0)) {
                            $errors['invoices'][$invNo][] = "Invalid tax structure (IGST + CGST/SGST both present)";
                        }

                        if ($iamt == 0 && ($camt == 0 || $samt == 0) && $rt > 0) {
                            $errors['invoices'][$invNo][] = "Invalid intra-state tax split";
                        }

                        $expectedTax = round(($txval * $rt) / 100, 2);
                        $actualTax   = $iamt + $camt + $samt;

                        if (abs($expectedTax - $actualTax) > 0.01) {
                            $errors['invoices'][$invNo][] = "Tax mismatch (strict)";
                        }

                        $calcTotal += $txval + $actualTax;
                        $itemIndex++;
                    }

                    if (abs($calcTotal - ($inv['val'] ?? 0)) > 0.01) {
                        $errors['invoices'][$invNo][] = "Invoice total mismatch (strict)";
                    }
                }
            }
        }

        // =========================
        // EXPORT VALIDATION
        // =========================
        if (!empty($data['exp'])) {
            foreach ($data['exp'] as $exp) {

                foreach ($exp['inv'] ?? [] as $inv) {

                    $invNo = $inv['inum'] ?? 'Unknown Invoice';

                    if (($exp['exp_typ'] ?? '') == 'WPAY') {

                        if (empty($inv['sbnum'])) {
                            $errors['invoices'][$invNo][] = "Shipping bill number required";
                        }

                        if (empty($inv['sbpcode'])) {
                            $errors['invoices'][$invNo][] = "Port code required";
                        }

                        if (!$isValidDate($inv['sbdt'] ?? '')) {
                            $errors['invoices'][$invNo][] = "Invalid shipping bill date";
                        }
                    }
                }
            }
        }

        // =========================
        // HSN VALIDATION (FIXED STRUCTURE)
        // =========================
        if (!empty($data['hsn']['hsn_b2b'])) {

            foreach ($data['hsn']['hsn_b2b'] as $hsn) {

                if (empty($hsn['num'])) {
                    $errors['hsn'][] = "HSN missing sequence number";
                }

                if (empty($hsn['hsn_sc']) || $hsn['hsn_sc'] == '0') {
                    $errors['hsn'][] = "Invalid HSN code";
                }

                if (!$isValidUQC($hsn['uqc'] ?? '')) {
                    $errors['hsn'][] = "Invalid UQC for HSN {$hsn['hsn_sc']}";
                }

                if (!isset($hsn['csamt'])) {
                    $errors['hsn'][] = "HSN missing csamt";
                }

                $txval = (float)($hsn['txval'] ?? 0);
                $tax   = ($hsn['iamt'] ?? 0) + ($hsn['camt'] ?? 0) + ($hsn['samt'] ?? 0);
                $val   = (float)($hsn['val'] ?? 0);

                if (abs(($txval + $tax) - $val) > 0.01) {
                    $errors['hsn'][] = "HSN {$hsn['hsn_sc']}: Value mismatch";
                }
            }
        }

        // =========================
        // DUPLICATE CHECK
        // =========================
        if (!empty($data['b2b'])) {
            $seen = [];

            foreach ($data['b2b'] as $b2b) {
                foreach ($b2b['inv'] ?? [] as $inv) {

                    $invNo = $inv['inum'] ?? '';
                    $key = ($b2b['ctin'] ?? '') . '_' . $invNo;

                    if (isset($seen[$key])) {
                        $errors['invoices'][$invNo][] = "Duplicate invoice";
                    }

                    $seen[$key] = true;
                }
            }
        }

        // =========================
        // ✅ ALWAYS RETURN STRUCTURE
        // =========================
        return [
            'general'  => $errors['general'],
            'invoices' => $errors['invoices'],
            'hsn'      => $errors['hsn']
        ];

    } catch (\Throwable $e) {

        return [
            'general'  => ['Validator crashed: ' . $e->getMessage()],
            'invoices' => [],
            'hsn'      => []
        ];
    }
}
 
 /* =========================
   B2B
========================= */
public function gst_b2b_table_export(string $from, string $to, array $tableinfo, array $states): array
{
    $rows = $this->db->table('vchgstsumn g')
        ->join('gstroutsup o', 'o.vch_txn_id = g.vch_txn_id', 'inner')
        ->join('vchtxnconso v', 'v.vch_txn_id = g.vch_txn_id', 'inner')
        ->where('v.cmp_id', $this->company_id)
        ->where('v.hobo_id', $this->bo_id)
        ->where('v.vch_type_id', 18)
        ->where('g.inv_supply_id', 1)
        ->where('o.outsup_rev_chg', (int)$tableinfo['outsup_rev_chg'])
        ->where('o.outsup_eco', (int)$tableinfo['outsup_eco'])
        ->where('g.vch_date >=', $from)
        ->where('g.vch_date <=', $to)
        ->groupBy('g.vch_txn_id')
        ->select('g.vch_txn_id, MIN(g.vch_date) AS idt, SUM(g.vch_taxable_value + g.vch_total_tax) AS val')
        ->select('MIN(o.outsup_bill_ref_no) AS inum', false)
        ->select('MIN(o.outsup_pos) AS pos', false)
        ->get()
        ->getResultArray();

    $ctinMap = [];

    foreach ($rows as $r) {
        $gstin = trim($this->partyGstin((int)$r['vch_txn_id']));
        if (!$this->isValidGstin($gstin)) {
            continue;
        }

        $itemsInfo = $this->voucherItems($from, $to, (int)$r['vch_txn_id']);
        if (empty($itemsInfo['items'])) {
            continue;
        }

        $inv = [
            'inum'    => (string)$r['inum'],
            'idt'     => $this->safeDate($r['idt']),
            'val'     => $this->round2($itemsInfo['total_val']),
            'pos'     => (string)$r['pos'],
            'rchrg'   => 'N',
            'inv_typ' => 'R',
            'itms'    => $itemsInfo['items']
        ];

        if (!isset($ctinMap[$gstin])) {
            $ctinMap[$gstin] = [
                'ctin' => $gstin,
                'inv'  => []
            ];
        }

        $ctinMap[$gstin]['inv'][] = $inv;
    }

    return array_values($ctinMap);
}

 
 /* ===== B2B (inv_supply_id = 1) ===== */
public function gst_b2b_table_export_old(string $from, string $to, array $tableinfo, array $states): array
{
    $rows = $this->db->table('vchgstsumn g')
        ->join('gstroutsup o','o.vch_txn_id = g.vch_txn_id','inner')
        ->join('vchtxnconso v','v.vch_txn_id = g.vch_txn_id','inner')
        ->where('v.cmp_id', $this->company_id)
        ->where('v.hobo_id', $this->bo_id)
        ->where('v.vch_type_id', 18)
        ->where('g.inv_supply_id', 1)                // B2B
        ->where('o.outsup_rev_chg', (int)$tableinfo['outsup_rev_chg'])
        ->where('o.outsup_eco', (int)$tableinfo['outsup_eco'])
        ->where('g.vch_date >=', $from)
        ->where('g.vch_date <=', $to)
        ->groupBy('g.vch_txn_id')
        ->select('g.vch_txn_id, MIN(g.vch_date) AS idt, SUM(g.vch_taxable_value + g.vch_total_tax) AS val')
        ->select('MIN(o.outsup_bill_ref_no) AS inum', false)
        ->select('MIN(o.outsup_pos) AS pos', false)
        ->get()->getResultArray();

    $ctinMap = [];
    foreach ($rows as $r) {
        $gstin = $this->partyGstin((int)$r['vch_txn_id']);
        $itemsInfo = $this->voucherItems($from,$to,(int)$r['vch_txn_id']);
        $inv = [
            'inum'    => $r['inum'],
            'idt'     => date('d-m-Y', strtotime($r['idt'])),
            'val'     => parseAmount($r['val']),
            'pos'     => $r['pos'],
            'rchrg'   => 'N',
            'inv_typ' => 'R',
            'itms'    => $itemsInfo['items']
        ];
        $ctinMap[$gstin]['inv'][] = $inv;
    }
    $out = [];
    foreach ($ctinMap as $gstin=>$data) {
        $out[] = ['ctin'=>$gstin,'inv'=>$data['inv']];
    }
    return $out;
}

/* =========================
   B2CL
========================= */
public function gst_b2cl_table_export(string $from, string $to, array $tableinfo, array $states): array
{
    $rows = $this->db->table('vchgstsumn g')
        ->join('gstroutsup o', 'o.vch_txn_id = g.vch_txn_id', 'inner')
        ->join('vchtxnconso v', 'v.vch_txn_id = g.vch_txn_id', 'inner')
        ->where('v.cmp_id', $this->company_id)
        ->where('v.hobo_id', $this->bo_id)
        ->where('v.vch_type_id', 18)
        ->where('g.inv_supply_id', 2)
        ->where('o.outsup_eco', (int)$tableinfo['outsup_eco'])
        ->where('g.vch_date >=', $from)
        ->where('g.vch_date <=', $to)
        ->groupBy('g.vch_txn_id')
        ->select('g.vch_txn_id, MIN(g.vch_date) AS idt')
        ->select('MIN(o.outsup_bill_ref_no) AS inum', false)
        ->select('MIN(o.outsup_pos) AS pos', false)
        ->get()
        ->getResultArray();

    $grouped = [];

    foreach ($rows as $r) {
        $itemsInfo = $this->voucherItems($from, $to, (int)$r['vch_txn_id']);
        if (empty($itemsInfo['items'])) {
            continue;
        }

        $pos = (string)$r['pos'];
        if (!isset($grouped[$pos])) {
            $grouped[$pos] = [
                'pos' => $pos,
                'inv' => []
            ];
        }

        $grouped[$pos]['inv'][] = [
            'inum'    => (string)$r['inum'],
            'idt'     => $this->safeDate($r['idt']),
            'val'     => $this->round2($itemsInfo['total_val']),
            'pos'     => $pos,
            'inv_typ' => 'R',
            'itms'    => $itemsInfo['items']
        ];
    }

    return array_values($grouped);
}

/* ===== B2CL (inv_supply_id = 2) ===== */
public function gst_b2cl_table_export_old(string $from, string $to, array $tableinfo, array $states): array
{
    $rows = $this->db->table('vchgstsumn g')
        ->join('gstroutsup o','o.vch_txn_id = g.vch_txn_id','inner')
        ->join('vchtxnconso v','v.vch_txn_id = g.vch_txn_id','inner')
        ->where('v.cmp_id',$this->company_id)
        ->where('v.hobo_id',$this->bo_id)
        ->where('v.vch_type_id',18)
        ->where('g.inv_supply_id', 2)                // B2CL
        ->where('o.outsup_eco',(int)$tableinfo['outsup_eco'])
        ->where('g.vch_date >=',$from)
        ->where('g.vch_date <=',$to)
        ->groupBy('g.vch_txn_id')
        ->select('g.vch_txn_id, MIN(g.vch_date) AS idt, SUM(g.vch_taxable_value + g.vch_total_tax) AS val')
        ->select('MIN(o.outsup_bill_ref_no) AS inum', false)
        ->select('MIN(o.outsup_pos) AS pos', false)
        ->get()->getResultArray();

    $out = [];
    foreach ($rows as $r) {
        $itemsInfo = $this->voucherItems($from,$to,(int)$r['vch_txn_id']);
        $out[] = [
            'pos' => $r['pos'],
            'inv' => [
                'inum' => $r['inum'],
                'idt'  => date('d-m-Y', strtotime($r['idt'])),
                'val'  => parseAmount($itemsInfo['total_val']),
                'itms' => $itemsInfo['items'],
            ],
        ];
    }
    return $out;
}

/* =========================
   B2CS
========================= */
public function gst_b2cs_table_export(string $from, string $to, array $tableinfo, array $states): array
{
    $bo_state = sprintf('%02d', $this->session->get('ses_bostecd'));

    $rows = $this->db->table('vchgstsumn g')
        ->join('gstroutsup o', 'o.vch_txn_id = g.vch_txn_id', 'inner')
        ->join('vchtxnconso v', 'v.vch_txn_id = g.vch_txn_id', 'inner')
        ->where('v.cmp_id', $this->company_id)
        ->where('v.hobo_id', $this->bo_id)
        ->where('v.vch_type_id', 18)
        ->where('g.inv_supply_id', 3)
        ->where('g.vch_date >=', $from)
        ->where('g.vch_date <=', $to)
        ->select('g.*, o.outsup_pos')
        ->get()
        ->getResultArray();

    $buckets = [];

    foreach ($rows as $r) {
        $txval = $this->round2($r['vch_taxable_value'] ?? 0);
        $igst  = $this->round2($r['vch_igst'] ?? 0);
        $cgst  = $this->round2($r['vch_cgst'] ?? 0);
        $sgst  = $this->round2($r['vch_sgst_ugst'] ?? 0);
        $cess  = $this->round2($r['vch_cess'] ?? 0);

        $rt = 0;
        if ($igst > 0 && $txval > 0) {
            $rt = (float)($r['vch_igst_rate'] ?? 0);
        } elseif (($cgst + $sgst) > 0 && $txval > 0) {
            $rt = (float)($r['vch_cgst_rate'] ?? 0) + (float)($r['vch_sgst_ugst_rate'] ?? 0);
        }

        $pos = sprintf('%02d', $r['outsup_pos']);
        $sply_ty = ($pos === $bo_state) ? 'INTRA' : 'INTER';
        $key = $sply_ty . '_' . $pos . '_' . $rt;

        if (!isset($buckets[$key])) {
            $buckets[$key] = [
                'sply_ty'      => $sply_ty,
                'rt'           => $this->round2($rt),
                'typ'          => 'OE',
                'pos'          => $pos,
                'diff_percent' => 0,
                'txval'        => 0,
                'iamt'         => 0,
                'camt'         => 0,
                'samt'         => 0,
                'csamt'        => 0
            ];
        }

        $buckets[$key]['txval'] += $txval;
        $buckets[$key]['iamt']  += $igst;
        $buckets[$key]['camt']  += $cgst;
        $buckets[$key]['samt']  += $sgst;
        $buckets[$key]['csamt'] += $cess;
    }

    foreach ($buckets as &$b) {
        $b['txval'] = $this->round2($b['txval']);
        $b['iamt']  = $this->round2($b['iamt']);
        $b['camt']  = $this->round2($b['camt']);
        $b['samt']  = $this->round2($b['samt']);
        $b['csamt'] = $this->round2($b['csamt']);
    }

    return array_values($buckets);
}

/* ===== B2CS (inv_supply_id = 3) ===== */
public function gst_b2cs_table_export_old(string $from, string $to, array $tableinfo, array $states): array
{
    $bo_state = sprintf('%02d', $this->session->get('ses_bostecd'));
    $rows = $this->db->table('vchgstsumn g')
        ->join('gstroutsup o','o.vch_txn_id = g.vch_txn_id','inner')
        ->join('vchtxnconso v','v.vch_txn_id = g.vch_txn_id','inner')
        ->where('v.cmp_id',$this->company_id)
        ->where('v.hobo_id',$this->bo_id)
        ->where('v.vch_type_id',18)
        ->where('g.inv_supply_id', 3)                // B2CS
        ->where('g.vch_date >=',$from)
        ->where('g.vch_date <=',$to)
        ->select('g.*, o.outsup_pos')
        ->get()->getResultArray();

    $buckets = [];
    foreach ($rows as $r) {
        $txval = parseAmount($r['vch_taxable_value']);
        $igst  = parseAmount($r['vch_igst']);
        $cgst  = parseAmount($r['vch_cgst']);
        $sgst  = parseAmount($r['vch_sgst_ugst']);
        $cess  = parseAmount($r['vch_cess']);

        $rt = 0;
        if ($igst > 0 && $txval > 0) {
            $rt = (float) ($r['vch_igst_rate'] ?? 0);
        } elseif (($cgst + $sgst) > 0 && $txval > 0) {
            $rt = (float) ($r['vch_cgst_rate'] ?? 0) + (float) ($r['vch_sgst_ugst_rate'] ?? 0);
        }

        $sply_ty = (sprintf('%02d',$r['outsup_pos']) === $bo_state) ? 'INTRA' : 'INTER';
        $key = $sply_ty.'_'.$rt;

        if (!isset($buckets[$key])) {
            $buckets[$key] = [
                'sply_ty'=>$sply_ty,
                'rt'=>(float)$rt,
                'pos'=>$r['outsup_pos'],
                'typ'=>'OE',
                'diff_percent'=>0,
                'txval'=>0,
                'iamt'=>0,
                'camt'=>0,
                'samt'=>0,
                'csamt'=>0,
            ];
        }
        $buckets[$key]['txval'] += $txval;
        if ($igst > 0) {
            $buckets[$key]['iamt'] += $igst;
        } else {
            $buckets[$key]['camt'] += $cgst;
            $buckets[$key]['samt'] += $sgst;
        }
        $buckets[$key]['csamt'] += $cess;
    }
    return array_values($buckets);
}


/* =========================
   NIL / EXEMPT / NON-GST
========================= */
public function gst_nill_table_export(string $from, string $to, array $states): array
{
    $bo_state = sprintf('%02d', $this->session->get('ses_bostecd'));

    $rows = $this->db->table('vchgstsumn g')
        ->join('gstroutsup o', 'o.vch_txn_id = g.vch_txn_id', 'inner')
        ->join('vchtxnconso v', 'v.vch_txn_id = g.vch_txn_id', 'inner')
        ->where('v.cmp_id', $this->company_id)
        ->where('v.hobo_id', $this->bo_id)
        ->where('v.vch_type_id', 18)
        ->whereIn('g.inv_supply_id', [4,5,6,7,8,9,10,11,12,13,14,15])
        ->where('g.vch_date >=', $from)
        ->where('g.vch_date <=', $to)
        ->whereIn('g.acc_bsd_type', [1,3])
        ->select('g.vch_txn_id, g.inv_supply_id, g.vch_taxable_value, o.outsup_pos')
        ->get()
        ->getResultArray();

    $template = [
        'INTER-B2B' => ['sply_ty' => 'INTER', 'expt_amt' => 0, 'nil_amt' => 0, 'ngsup_amt' => 0],
        'INTER-B2C' => ['sply_ty' => 'INTER', 'expt_amt' => 0, 'nil_amt' => 0, 'ngsup_amt' => 0],
        'INTRA-B2B' => ['sply_ty' => 'INTRA', 'expt_amt' => 0, 'nil_amt' => 0, 'ngsup_amt' => 0],
        'INTRA-B2C' => ['sply_ty' => 'INTRA', 'expt_amt' => 0, 'nil_amt' => 0, 'ngsup_amt' => 0],
    ];

    foreach ($rows as $r) {
        $pos = sprintf('%02d', $r['outsup_pos']);
        $gstin = trim($this->partyGstin((int)$r['vch_txn_id']));
        $isRegistered = $this->isValidGstin($gstin);

        $bucketKey = (($pos === $bo_state) ? 'INTRA' : 'INTER') . '-' . ($isRegistered ? 'B2B' : 'B2C');
        $invSup = (int)($r['inv_supply_id'] ?? 0);
        $tx = $this->round2($r['vch_taxable_value'] ?? 0);

        if (in_array($invSup, [4,5,6,7])) {
            $template[$bucketKey]['nil_amt'] += $tx;
        } elseif (in_array($invSup, [8,9,10,11])) {
            $template[$bucketKey]['expt_amt'] += $tx;
        } elseif (in_array($invSup, [12,13,14,15])) {
            $template[$bucketKey]['ngsup_amt'] += $tx;
        }
    }

    $out = [];
    foreach ($template as $key => $row) {
        [$sply, $type] = explode('-', $key);
        $out[] = [
            'sply_ty'   => $sply,
            'typ'       => $type,
            'expt_amt'  => $this->round2($row['expt_amt']),
            'nil_amt'   => $this->round2($row['nil_amt']),
            'ngsup_amt' => $this->round2($row['ngsup_amt']),
        ];
    }

    return ['inv' => $out];
}


/* ===== NIL (inv_supply_id 4-15) ===== */
public function gst_nill_table_export_old(string $from, string $to, array $states): array
{
    $bo_state = sprintf('%02d', $this->session->get('ses_bostecd'));
    $rows = $this->db->table('vchgstsumn g')
        ->join('gstroutsup o','o.vch_txn_id = g.vch_txn_id','inner')
        ->join('vchtxnconso v','v.vch_txn_id = g.vch_txn_id','inner')
        ->where('v.cmp_id',$this->company_id)
        ->where('v.hobo_id',$this->bo_id)
        ->where('v.vch_type_id',18)
        ->whereIn('g.inv_supply_id',[4,5,6,7,8,9,10,11,12,13,14,15])
        ->where('g.vch_date >=',$from)
        ->where('g.vch_date <=',$to)
        ->whereIn('g.acc_bsd_type', [1,3])
        ->get()->getResultArray();

    $template = [
        'INTRB2B'=>['sply_ty'=>'INTRB2B','expt_amt'=>0,'nil_amt'=>0,'ngsup_amt'=>0],
        'INTRB2C'=>['sply_ty'=>'INTRB2C','expt_amt'=>0,'nil_amt'=>0,'ngsup_amt'=>0],
        'INTRAB2B'=>['sply_ty'=>'INTRAB2B','expt_amt'=>0,'nil_amt'=>0,'ngsup_amt'=>0],
        'INTRAB2C'=>['sply_ty'=>'INTRAB2C','expt_amt'=>0,'nil_amt'=>0,'ngsup_amt'=>0],
    ];

    foreach ($rows as $r) {
        $pos = sprintf('%02d', $r['outsup_pos']);
        $gstin = $this->partyGstin((int)$r['vch_txn_id']);
        $sply = (!empty($gstin) ? ($pos===$bo_state ? 'INTRAB2B':'INTRB2B')
                                : ($pos===$bo_state ? 'INTRAB2C':'INTRB2C'));
        $invSup = (int)($r['inv_supply_id'] ?? 0);
        $tx = parseAmount($r['vch_taxable_value']);

        if (in_array($invSup,[4,5,6,7])) {                // Nil rated
            $template[$sply]['nil_amt'] += $tx;
        } elseif (in_array($invSup,[8,9,10,11])) {        // Exempted
            $template[$sply]['expt_amt'] += $tx;
        } elseif (in_array($invSup,[12,13,14,15])) {      // Non-GST
            $template[$sply]['ngsup_amt'] += $tx;
        } else {
            $template[$sply]['nil_amt'] += $tx;
        }
    }
    return ['data'=>['inv'=>array_values($template)]];
}


/* =========================
   EXPORTS
========================= */
public function gst_exports_table_export(string $from, string $to, array $states): array
{
    $rows = $this->db->table('vchgstsumn g')
        ->join('gstroutsup o', 'o.vch_txn_id = g.vch_txn_id', 'inner')
        ->join('vchtxnconso v', 'v.vch_txn_id = g.vch_txn_id', 'inner')
        ->where('v.cmp_id', $this->company_id)
        ->where('v.hobo_id', $this->bo_id)
        ->where('v.vch_type_id', 18)
        ->whereIn('g.inv_supply_id', [16, 17])
        ->where('g.vch_date >=', $from)
        ->where('g.vch_date <=', $to)
        ->groupBy('g.vch_txn_id')
        ->select('g.vch_txn_id, MIN(g.vch_date) AS idt')
        ->select('MIN(o.outsup_bill_ref_no) AS inum', false)
        ->select('MIN(o.exp_port_code) AS exp_port_code', false)
        ->select('MIN(o.exp_sb_no) AS exp_sb_no', false)
        ->select('MIN(o.exp_sb_date) AS exp_sb_date', false)
        ->select('MIN(g.inv_supply_id) AS inv_sup', false)
        ->get()
        ->getResultArray();

    $grouped = [];

    foreach ($rows as $r) {

        $items = $this->voucherItems($from, $to, (int)$r['vch_txn_id']);
        if (empty($items['items'])) {
            continue;
        }

        $invSup = (int)$r['inv_sup'];
        $expTyp = ($invSup === 16) ? 'WPAY' : 'WOPAY';

        // ── WOPAY: Strip all tax amounts, keep rate & taxable value ──────
        if ($invSup === 17) {

            foreach ($items['items'] as &$it) {

                // ✅ Handle both flat and nested itm_det structure
                if (isset($it['itm_det']) && is_array($it['itm_det'])) {
                    // Nested structure: { num, itm_det: { rt, txval, iamt, camt, samt, csamt } }
                    $it['itm_det']['iamt']  = 0;
                    $it['itm_det']['camt']  = 0;
                    $it['itm_det']['samt']  = 0;
                    $it['itm_det']['csamt'] = 0;
                    // ✅ Keep rt (rate) — GSTN still requires it
                    // ✅ Keep txval as-is
                } else {
                    // Flat structure
                    $it['iamt']  = 0;
                    $it['camt']  = 0;
                    $it['samt']  = 0;
                    $it['csamt'] = 0;
                    // ✅ Do NOT zero rt
                }
            }
            unset($it);

            // ✅ Invoice value = sum of taxable values only (no tax)
            $items['total_val'] = array_sum(
                array_map(function ($it) {
                    // Handle both flat and nested
                    return isset($it['itm_det'])
                        ? (float)($it['itm_det']['txval'] ?? 0)
                        : (float)($it['txval'] ?? 0);
                }, $items['items'])
            );
        }

        if (!isset($grouped[$expTyp])) {
            $grouped[$expTyp] = [
                'exp_typ' => $expTyp,
                'inv'     => [],
            ];
        }

        // Format shipping bill date
        $sbdt = !empty($r['exp_sb_date'])
            ? date('d-m-Y', strtotime($r['exp_sb_date']))
            : '';

        // ── WPAY: Include SB details ──────────────────────────────────────
        if ($expTyp === 'WPAY') {

            $grouped[$expTyp]['inv'][] = [
                'inum'    => (string)$r['inum'],
                'idt'     => $this->safeDate($r['idt']),
                'val'     => $this->round2($items['total_val']),
                'sbpcode' => $r['exp_port_code'] ?? '',
                'sbnum'   => $r['exp_sb_no'] ?? '',
                'sbdt'    => $sbdt,
                'itms'    => $items['items'],
            ];

        // ── WOPAY: No SB details, no tax amounts ─────────────────────────
        } else {

            $grouped[$expTyp]['inv'][] = [
                'inum' => (string)$r['inum'],
                'idt'  => $this->safeDate($r['idt']),
                'val'  => $this->round2($items['total_val']),
                'itms' => $items['items'],
            ];
        }
    }

    return array_values($grouped);
}

/* ===== EXPORTS (inv_supply_id 16,17) ===== */
public function gst_exports_table_export_old(string $from, string $to, array $states): array
{
    $rows = $this->db->table('vchgstsumn g')
        ->join('gstroutsup o','o.vch_txn_id = g.vch_txn_id','inner')
        ->join('vchtxnconso v','v.vch_txn_id = g.vch_txn_id','inner')
        ->where('v.cmp_id',$this->company_id)
        ->where('v.hobo_id',$this->bo_id)
        ->where('v.vch_type_id',18)
        ->whereIn('g.inv_supply_id',[16,17])
        ->where('g.vch_date >=',$from)
        ->where('g.vch_date <=',$to)
        ->groupBy('g.vch_txn_id')
        ->select('g.vch_txn_id, MIN(g.vch_date) AS idt')
        ->select('MIN(o.outsup_bill_ref_no) AS inum', false)
        ->select('MIN(g.inv_supply_id) AS inv_sup', false)
        ->get()->getResultArray();

    $map = [];
    foreach ($rows as $r) {
        $items = $this->voucherItems($from,$to,(int)$r['vch_txn_id']);
        $expTyp = ((int)$r['inv_sup'] === 16) ? 'WPAY' : 'WOPAY'; // 16 EXPWP, 17 EXPWOP
        $map[$expTyp]['inv'][] = [
            'inum'    => $r['inum'],
            'idt'     => date('d-m-Y', strtotime($r['idt'])),
            'val'     => parseAmount($items['total_val']),
            'sbpcode' => '',
            'sbnum'   => '',
            'sbdt'    => '',
            'itms'    => $items['items'],
        ];
    }
    $out = [];
    foreach ($map as $expTyp=>$p) {
        $out[] = ['exp_typ'=>$expTyp, 'inv'=>$p['inv']];
    }
    return ['inv'=>$out];
}
/* =========================
   CDNR
========================= */
public function gst_cdnr_table_export(string $from, string $to, array $states): array
{
    $rows = $this->db->table('vchgstsumn g')
        ->join('gstrinwsup i', 'i.vch_txn_id = g.vch_txn_id', 'inner')
        ->join('vchtxnconso v', 'v.vch_txn_id = g.vch_txn_id', 'inner')
        ->where('v.cmp_id', $this->company_id)
        ->where('v.hobo_id', $this->bo_id)
        ->where('v.vch_type_id', 2)
        ->where('g.is_outward', 2)
        ->where('i.inwsup_rev_chg', 0)
        ->where('i.inwsup_eco', '0')
        ->where('g.vch_date >=', $from)
        ->where('g.vch_date <=', $to)
        ->groupBy('g.vch_txn_id')
        ->select('g.vch_txn_id, MIN(g.vch_date) AS idt')
        ->select('MIN(i.inwsup_pos) AS pos', false)
        ->select('MIN(i.inwsup_bill_ref_no) AS inum', false)
        ->get()
        ->getResultArray();

    $ctinMap = [];

    foreach ($rows as $r) {
        $gstin = trim($this->partyGstin((int)$r['vch_txn_id']));
        if (!$this->isValidGstin($gstin)) {
            continue;
        }

        $items = $this->voucherItems($from, $to, (int)$r['vch_txn_id']);
        if (empty($items['items'])) {
            continue;
        }

        if (!isset($ctinMap[$gstin])) {
            $ctinMap[$gstin] = [
                'ctin' => $gstin,
                'nt'   => []
            ];
        }

        $ctinMap[$gstin]['nt'][] = [
            'nt_num'  => (string)$r['inum'],
            'nt_dt'   => $this->safeDate($r['idt']),
            'ntty'    => 'C',
            'val'     => $this->round2($items['total_val']),
            'pos'     => (string)$r['pos'],
            'rchrg'   => 'N',
            'inv_typ' => 'R',
            'itms'    => $items['items']
        ];
    }

    return array_values($ctinMap);
}

/* ===== CDNR (credit notes, registered) ===== */
public function gst_cdnr_table_export_old(string $from, string $to, array $states): array
{
    $rows = $this->db->table('vchgstsumn g')
        ->join('gstrinwsup i','i.vch_txn_id = g.vch_txn_id','inner')
        ->join('vchtxnconso v','v.vch_txn_id = g.vch_txn_id','inner')
        ->where('v.cmp_id',$this->company_id)
        ->where('v.hobo_id',$this->bo_id)
        ->where('v.vch_type_id',2)          // credit note
        ->where('g.is_outward', 2)          // outward credit notes
        ->where('i.inwsup_rev_chg',0)
        ->where('i.inwsup_eco','0')         // varchar -> compare as string
        ->where('g.vch_date >=',$from)
        ->where('g.vch_date <=',$to)
        ->groupBy('g.vch_txn_id')
        ->select('g.vch_txn_id, MIN(g.vch_date) AS idt')
        ->select('MIN(i.inwsup_pos) AS pos', false)
        ->select('MIN(i.inwsup_bill_ref_no) AS inum', false)
        ->get()->getResultArray();

    $ctinMap = [];
    foreach ($rows as $r) {
        $gstin = $this->partyGstin((int)$r['vch_txn_id']);
        $items = $this->voucherItems($from,$to,(int)$r['vch_txn_id']);
        $inv = [
            'nt_num'=> (string)$r['inum'],
            'nt_dt' => date('d-m-Y', strtotime($r['idt'])),
            'ntty'  => 'C',
            'val'   => parseAmount($items['total_val']),
            'pos'   => $r['pos'],
            'rchrg' => 'N',
            'inv_typ'=>'R',
            'itms'  => $items['items']
        ];
        $ctinMap[$gstin]['nt'][] = $inv;
    }
    $out = [];
    foreach ($ctinMap as $gstin=>$data) {
        $out[] = ['ctin'=>$gstin,'nt'=>$data['nt']];
    }
    return ['inv'=>$out];
}


/* =========================
   CDNUR
========================= */
public function gst_cdnur_table_export(string $from, string $to, array $states): array
{
    $rows = $this->db->table('vchgstsumn g')
        ->join('gstrinwsup i', 'i.vch_txn_id = g.vch_txn_id', 'inner')
        ->join('vchtxnconso v', 'v.vch_txn_id = g.vch_txn_id', 'inner')
        ->where('v.cmp_id', $this->company_id)
        ->where('v.hobo_id', $this->bo_id)
        ->where('v.vch_type_id', 2)
        ->where('g.is_outward', 2)
        ->where('i.inwsup_rev_chg', 0)
        ->where('i.inwsup_eco', '0')
        ->where('g.vch_date >=', $from)
        ->where('g.vch_date <=', $to)
        ->groupBy('g.vch_txn_id')
        ->select('g.vch_txn_id, MIN(g.vch_date) AS idt')
        ->select('MIN(i.inwsup_pos) AS pos', false)
        ->select('MIN(i.inwsup_bill_ref_no) AS inum', false)
        ->get()
        ->getResultArray();

    $out = [];

    foreach ($rows as $r) {
        $gstin = trim($this->partyGstin((int)$r['vch_txn_id']));
        if ($this->isValidGstin($gstin)) {
            continue;
        }

        $items = $this->voucherItems($from, $to, (int)$r['vch_txn_id']);
        if (empty($items['items'])) {
            continue;
        }

        $out[] = [
            'nt_num'  => (string)$r['inum'],
            'nt_dt'   => $this->safeDate($r['idt']),
            'ntty'    => 'C',
            'val'     => $this->round2($items['total_val']),
            'pos'     => (string)$r['pos'],
            'rchrg'   => 'N',
            'inv_typ' => 'B2CL',
            'itms'    => $items['items']
        ];
    }

    return $out;
}

/* ===== CDNUR (credit notes, unregistered) ===== */
public function gst_cdnur_table_export_old(string $from, string $to, array $states): array
{
    $rows = $this->db->table('vchgstsumn g')
        ->join('gstrinwsup i','i.vch_txn_id = g.vch_txn_id','inner')
        ->join('vchtxnconso v','v.vch_txn_id = g.vch_txn_id','inner')
        ->where('v.cmp_id',$this->company_id)
        ->where('v.hobo_id',$this->bo_id)
        ->where('v.vch_type_id',2)          // credit note
        ->where('g.is_outward', 2)
        ->where('i.inwsup_rev_chg',0)
        ->where('i.inwsup_eco','0')         // varchar -> compare as string
        ->where('g.vch_date >=',$from)
        ->where('g.vch_date <=',$to)
        ->groupBy('g.vch_txn_id')
        ->select('g.vch_txn_id, MIN(g.vch_date) AS idt')
        ->select('MIN(i.inwsup_pos) AS pos', false)
        ->select('MIN(i.inwsup_bill_ref_no) AS inum', false)
        ->get()->getResultArray();

    $out = [];
    foreach ($rows as $r) {
        $items = $this->voucherItems($from,$to,(int)$r['vch_txn_id']);
        $out[] = [
            'nt_num'=> (string)$r['inum'],
            'nt_dt' => date('d-m-Y', strtotime($r['idt'])),
            'ntty'  => 'C',
            'val'   => parseAmount($items['total_val']),
            'pos'   => $r['pos'],
            'rchrg' => 'N',
            'inv_typ'=>'R',
            'itms'  => $items['items']
        ];
    }
    return ['inv'=>$out];
}


/* =========================
   DOC ISSUE
========================= */
public function gst_docs_table_export(string $from, string $to, array $states): array
{
    $rows = $this->db->table('vchtxnconso v')
        ->join('gstroutsup o', 'o.vch_txn_id = v.vch_txn_id', 'left')
        ->join('gstrinwsup i', 'i.vch_txn_id = v.vch_txn_id', 'left')
        ->where('v.cmp_id', $this->company_id)
        ->where('v.hobo_id', $this->bo_id)
        ->whereIn('v.vch_type_id', [18, 2])
        ->where('v.vch_date >=', $from)
        ->where('v.vch_date <=', $to)
        ->select('v.vch_type_id, v.vch_txn_id')
        ->select('COALESCE(MIN(o.outsup_bill_ref_no), MIN(i.inwsup_bill_ref_no)) AS billno', false)
        ->groupBy('v.vch_type_id, v.vch_txn_id')
        ->get()
        ->getResultArray();

    $typeLabel = [
        18 => 'Invoices for outward supply',
        2  => 'Credit Note',
    ];

    $grouped = [];

    foreach ($rows as $r) {
        $vt   = (int)$r['vch_type_id'];
        $bill = trim((string)$r['billno']);

        if ($bill === '') continue;

        if (!isset($grouped[$vt])) {
            $grouped[$vt] = [];
        }

        $grouped[$vt][] = $bill;
    }

    $doc_det = [];
    $docNum  = 1;

    foreach ($grouped as $vt => $bills) {

        // 🔥 IMPORTANT: Remove duplicates
        $bills = array_unique($bills);

        // 🔥 IMPORTANT: Natural sort (GST expects sequence)
        sort($bills, SORT_NATURAL);

        $fromNo = $bills[0] ?? '';
        $toNo   = end($bills) ?: '';
        $totnum = count($bills);

        $doc_det[] = [
            'doc_num' => $docNum++,
            'doc_typ' => $typeLabel[$vt] ?? 'Doc',
            'docs'    => [[
                'num'       => 1,
                'from'      => (string)$fromNo,
                'to'        => (string)$toNo,
                'totnum'    => $totnum,
                'cancel'    => 0,
                'net_issue' => $totnum
            ]]
        ];
    }

    // ✅ FINAL STRUCTURE (CRITICAL FIX)
    return [
        'doc_det' => $doc_det
    ];
}

/* ===== DOC ISSUE ===== */
public function gst_docs_table_export_old(string $from, string $to, array $states): array
{
    $rows = $this->db->table('vchtxnconso v')
        ->join('gstroutsup o','o.vch_txn_id = v.vch_txn_id','left')
        ->join('gstrinwsup i','i.vch_txn_id = v.vch_txn_id','left')
        ->where('v.cmp_id',$this->company_id)
        ->where('v.hobo_id',$this->bo_id)
        ->whereIn('v.vch_type_id',[18,2])
        ->where('v.vch_date >=',$from)
        ->where('v.vch_date <=',$to)
        ->select('v.vch_type_id, v.vch_txn_id')
        ->select('COALESCE(MIN(o.outsup_bill_ref_no), MIN(i.inwsup_bill_ref_no)) AS billno', false)
        ->groupBy('v.vch_type_id, v.vch_txn_id')
        ->get()->getResultArray();

    $typeLabel = [
        18 => 'Invoices for outward supply',
        2  => 'Credit Note',
    ];

    $docNum=0; $grouped=[];
    foreach ($rows as $r) {
        $vt = (int)$r['vch_type_id'];
        $bill = $r['billno'];
        if (!isset($grouped[$vt])) $grouped[$vt]=[];
        $grouped[$vt][]=$bill;
    }

    $out=[];
    foreach ($grouped as $vt=>$bills) {
        $docNum++;
        sort($bills, SORT_NATURAL);
        $fromNo = $bills[0] ?? '';
        $toNo   = end($bills) ?: '';
        $totnum = count($bills);
        $out[] = [
            'doc_num' => $docNum,
            'doc_typ' => $typeLabel[$vt] ?? 'Doc',
            'docs'    => [[
                'num'      => 1,
                'from'     => (string)$fromNo,
                'to'       => (string)$toNo,
                'totnum'   => $totnum,
                'cancel'   => 0,
                'net_issue'=> $totnum
            ]]
        ];
    }
    return ['doc_det'=>$out];
}


/* =========================
   HSN
========================= */
public function gst_hsn_table_export($from_date, $to_date, $states)
{
    $rows = $this->db->table('vchgstsumn g')
        ->join('vchtxnconso v', 'v.vch_txn_id = g.vch_txn_id', 'inner')
        ->select('
            COALESCE(g.inv_supply_id,0) as supply_type,
            g.vch_hsn_sac,
            g.vch_taxable_value,
            g.vch_igst,
            g.vch_cgst,
            g.vch_sgst_ugst,
            g.vch_cess,
            g.vch_igst_rate,
            g.vch_cgst_rate,
            g.vch_sgst_ugst_rate
        ', false)
        ->where('v.cmp_id', $this->company_id)
        ->where('v.hobo_id', $this->bo_id)
        ->where('g.is_outward', 1)
        ->where('g.vch_date >=', $from_date)
        ->where('g.vch_date <=', $to_date)
        ->get()
        ->getResultArray();

    $hsn_b2b = [];
    $hsn_b2c = [];

    foreach ($rows as $row) {

        $supply_type = (int)$row['supply_type'];

        // 🔥 FIX: WOP → NO TAX
        $is_wop = in_array($supply_type, [17, 19]);

        // 🔥 RATE
        if ($is_wop) {
            $rate = 0;
        } else {
            if ($this->round2($row['vch_igst_rate']) > 0) {
                $rate = (float)$row['vch_igst_rate'];
            } else {
                $rate = (float)$row['vch_cgst_rate'] + (float)$row['vch_sgst_ugst_rate'];
            }
        }

        $rate = $this->round2($rate);

        // 🔥 HSN
        $hsn = trim((string)$row['vch_hsn_sac']);
        if ($hsn === '') $hsn = '0';

        $key = $hsn . '_' . $rate;

        // 🔥 TAX VALUES
        $txval = $this->round2($row['vch_taxable_value']);

        $iamt  = $is_wop ? 0 : $this->round2($row['vch_igst']);
        $camt  = $is_wop ? 0 : $this->round2($row['vch_cgst']);
        $samt  = $is_wop ? 0 : $this->round2($row['vch_sgst_ugst']);
        $csamt = $is_wop ? 0 : $this->round2($row['vch_cess']);

        // 🔥 CLASSIFICATION
        $target = ($supply_type === 1) ? 'b2b' : 'b2c';

        if ($target === 'b2b') {

            if (!isset($hsn_b2b[$key])) {
                $hsn_b2b[$key] = [
                    'num'    => 0,
                    'hsn_sc' => $hsn,
                    'desc'   => 'OTH',
                    'uqc'    => 'OTH',
                    'qty'    => 0,
                    'rt'     => $rate,
                    'txval'  => 0,
                    'iamt'   => 0,
                    'camt'   => 0,
                    'samt'   => 0,
                    'csamt'  => 0
                ];
            }

            $hsn_b2b[$key]['qty']   += 1;
            $hsn_b2b[$key]['txval'] += $txval;
            $hsn_b2b[$key]['iamt']  += $iamt;
            $hsn_b2b[$key]['camt']  += $camt;
            $hsn_b2b[$key]['samt']  += $samt;
            $hsn_b2b[$key]['csamt'] += $csamt;

        } else {

            if (!isset($hsn_b2c[$key])) {
                $hsn_b2c[$key] = [
                    'num'    => 0,
                    'hsn_sc' => $hsn,
                    'desc'   => 'OTH',
                    'uqc'    => 'OTH',
                    'qty'    => 0,
                    'rt'     => $rate,
                    'txval'  => 0,
                    'iamt'   => 0,
                    'camt'   => 0,
                    'samt'   => 0,
                    'csamt'  => 0
                ];
            }

            $hsn_b2c[$key]['qty']   += 1;
            $hsn_b2c[$key]['txval'] += $txval;
            $hsn_b2c[$key]['iamt']  += $iamt;
            $hsn_b2c[$key]['camt']  += $camt;
            $hsn_b2c[$key]['samt']  += $samt;
            $hsn_b2c[$key]['csamt'] += $csamt;
        }
    }

    // 🔥 FORMAT
    $format = function ($data) {
        $out = array_values($data);

        foreach ($out as $i => &$row) {

            $row['num']   = $i + 1;

            $row['txval'] = round($row['txval'], 2);
            $row['iamt']  = round($row['iamt'], 2);
            $row['camt']  = round($row['camt'], 2);
            $row['samt']  = round($row['samt'], 2);
            $row['csamt'] = round($row['csamt'], 2);

            $row['val'] = round(
                $row['txval'] +
                $row['iamt'] +
                $row['camt'] +
                $row['samt'] +
                $row['csamt'], 2
            );

            $row['qty'] = round($row['qty'], 2);
        }

        return $out;
    };

    return [
        'hsn_b2b' => $format($hsn_b2b),
        'hsn_b2c' => $format($hsn_b2c)
    ];
}

/* ===== HSN ===== */
public function gst_hsn_table_export_old($from_date, $to_date, $states)
{
    ini_set('precision', 10);
    ini_set('serialize_precision', 10);

    // Fetch ALL outward vouchers with their inv_supply_id
    $rows = $this->db->table('vchgstsumn g')
        ->join('vchtxnconso v', 'v.vch_txn_id = g.vch_txn_id', 'inner')
        ->select('g.*, 
                  COALESCE(g.inv_supply_id, 0) AS supply_type,
                  g.vch_hsn_sac,
                  g.vch_taxable_value,
                  g.vch_igst,
                  g.vch_cgst,
                  g.vch_sgst_ugst,
                  g.vch_cess,
                  g.vch_igst_rate,
                  g.vch_cgst_rate,
                  g.vch_sgst_ugst_rate', false)
        ->where('v.cmp_id', $this->company_id)
        ->where('v.hobo_id', $this->bo_id)
        ->where('g.is_outward', 1)
        ->where('g.vch_date >=', $from_date)
        ->where('g.vch_date <=', $to_date)
        ->get()->getResultArray();

    $hsn_b2b = [];
    $hsn_b2c = [];

    foreach ($rows as $row) {
        // Determine tax rate
        $rate = 0;
        if (parseAmount($row['vch_igst_rate']) > 0) {
            $rate = $row['vch_igst_rate'];
        } elseif (parseAmount($row['vch_cgst_rate']) > 0) {
            $rate = $row['vch_cgst_rate'] + $row['vch_sgst_ugst_rate'];
        }

        $hsn = !empty($row['vch_hsn_sac']) ? $row['vch_hsn_sac'] : '0';
        $groupKey = $hsn . '_' . parseAmount($rate);

        // Determine B2B vs B2C based on inv_supply_id
        // B2B: inv_supply_id = 1 (Registered)
        // B2C: inv_supply_id IN (2,3,20) (B2CL, B2CS, Exports)
        $supply_type = (int)$row['supply_type'];
        
        if ($supply_type === 1) {
            // B2B
            if (!isset($hsn_b2b[$groupKey])) {
                $hsn_b2b[$groupKey] = [
                    'num'    => count($hsn_b2b) + 1,
                    'hsn_sc' => (string)$hsn,
                    'desc'   => 'OTH',
                    'uqc'    => 'OTH',
                    'qty'    => 0,
                    'rt'     => parseAmount($rate),
                    'txval'  => 0,
                    'iamt'   => 0,
                    'samt'   => 0,
                    'camt'   => 0,
                    'csamt'  => 0,
                ];
            }

            $hsn_b2b[$groupKey]['qty']   += 1;
            $hsn_b2b[$groupKey]['txval'] += parseAmount($row['vch_taxable_value']);
            $hsn_b2b[$groupKey]['iamt']  += parseAmount($row['vch_igst']);
            $hsn_b2b[$groupKey]['samt']  += parseAmount($row['vch_sgst_ugst']);
            $hsn_b2b[$groupKey]['camt']  += parseAmount($row['vch_cgst']);
            $hsn_b2b[$groupKey]['csamt'] += parseAmount($row['vch_cess']);

        } else if ($supply_type === 3 || $supply_type === 2 || $supply_type === 16  || $supply_type === 17) {
            // B2C (all other supply types)
            if (!isset($hsn_b2c[$groupKey])) {
                $hsn_b2c[$groupKey] = [
                    'num'    => count($hsn_b2c) + 1,
                    'hsn_sc' => (string)$hsn,
                    'desc'   => 'OTH',
                    'uqc'    => 'OTH',
                    'qty'    => 0,
                    'rt'     => parseAmount($rate),
                    'txval'  => 0,
                    'iamt'   => 0,
                    'samt'   => 0,
                    'camt'   => 0,
                    'csamt'  => 0,
                ];
            }

            $hsn_b2c[$groupKey]['qty']   += 1;
            $hsn_b2c[$groupKey]['txval'] += parseAmount($row['vch_taxable_value']);
            $hsn_b2c[$groupKey]['iamt']  += parseAmount($row['vch_igst']);
            $hsn_b2c[$groupKey]['samt']  += parseAmount($row['vch_sgst_ugst']);
            $hsn_b2c[$groupKey]['camt']  += parseAmount($row['vch_cgst']);
            $hsn_b2c[$groupKey]['csamt'] += parseAmount($row['vch_cess']);
        }
    }

    // Renumber after grouping
    $hsn_b2b = array_values($hsn_b2b);
    foreach ($hsn_b2b as $idx => &$item) {
        $item['num'] = $idx + 1;
    }

    $hsn_b2c = array_values($hsn_b2c);
    foreach ($hsn_b2c as $idx => &$item) {
        $item['num'] = $idx + 1;
    }

    return [
        'hsn_b2b' => $hsn_b2b,
        'hsn_b2c' => $hsn_b2c,
    ];
}


	private function firstPartyId(int $vchTxnId): ?int
{
    $row = $this->db->table('cmptxnmstn')
        ->select('master_id')
        ->where('vch_txn_id', $vchTxnId)
        ->where('master_id_type', 'acc')
        ->orderBy('txn_id', 'asc')
        ->limit(1)
        ->get()
        ->getRowArray();

    return $row['master_id'] ?? null;
}

private function partyGstin(int $vchTxnId): string
{
    $accId = $this->firstPartyId($vchTxnId);
    if (!$accId) {
        return '';
    }

    $row = $this->db->table('acctmstdet')
        ->select('acc_gstin')
        ->where('acc_id', $accId)
        ->get()
        ->getRowArray();

    return trim((string)($row['acc_gstin'] ?? ''));
}

    /* =========================
   ITEM HELPER
========================= */

private function gst_calc_tax($txval, $rate, $isIntra)
{
    $txval = (float)$txval;
    $rate  = (float)$rate;

    // =============================
    // 🔥 STEP 1: CALCULATE TOTAL TAX FIRST (CRITICAL)
    // =============================
    $tax = round(($txval * $rate) / 100, 2);

    $iamt = 0.00;
    $camt = 0.00;
    $samt = 0.00;

    // =============================
    // 🔥 STEP 2: SPLIT TAX CORRECTLY
    // =============================
    if ($isIntra) {
        // CGST + SGST

        $camt = round($tax / 2, 2);

        // 🔥 IMPORTANT: DO NOT ROUND AGAIN
        $samt = $tax - $camt;

    } else {
        // IGST
        $iamt = $tax;
    }

    // =============================
    // 🔥 STEP 3: FINAL VALUE
    // =============================
    $val = round($txval + $tax, 2);

    return [
        'txval' => round($txval, 2),
        'rt'    => $rate,
        'iamt'  => round($iamt, 2),
        'camt'  => round($camt, 2),
        'samt'  => round($samt, 2),
        'csamt' => 0.00,
        'val'   => $val
    ];
}

private function voucherItems(string $from, string $to, int $vchTxnId, array $excludeTaxShort = []): array
{
    $rows = $this->db->table('vchgstsumn')
        ->where('vch_txn_id', $vchTxnId)
        ->where('vch_date >=', $from)
        ->where('vch_date <=', $to)
        ->orderBy('vch_gst_sum_id', 'ASC')
        ->get()
        ->getResultArray();

    $items = [];
    $total = 0;
    $lineNo = 1;

    foreach ($rows as $r) {

        $txval = (float)($r['vch_taxable_value'] ?? 0);
        $igst  = (float)($r['vch_igst'] ?? 0);
        $cgst  = (float)($r['vch_cgst'] ?? 0);
        $sgst  = (float)($r['vch_sgst_ugst'] ?? 0);
        $cess  = (float)($r['vch_cess'] ?? 0);

        // =============================
        // 🔥 STRICT ROUNDING
        // =============================
        $txval = round($txval, 2);
        $cess  = round($cess, 2);

        // =============================
        // 🔥 RATE DETECTION
        // =============================
        $rt = 0;

        if ($igst > 0 && $txval > 0) {
            $rt = (float)($r['vch_igst_rate'] ?? 0);
        } elseif (($cgst + $sgst) > 0 && $txval > 0) {
            $rt = (float)($r['vch_cgst_rate'] ?? 0) + (float)($r['vch_sgst_ugst_rate'] ?? 0);
        }

        // =============================
        // 🔥 GST STRICT STRUCTURE
        // =============================
        $itm_det = [
            'txval' => $txval,
            'rt'    => (float)$rt,
            'iamt'  => 0,
            'camt'  => 0,
            'samt'  => 0,
            'csamt' => $cess
        ];

        // =============================
        // 🔥 FIX: USE GST CALCULATION ENGINE
        // =============================
        $isIntra = ($igst == 0); // intra if no IGST

        $taxData = $this->gst_calc_tax($txval, $rt, $isIntra);

        $itm_det['iamt'] = $taxData['iamt'];
        $itm_det['camt'] = $taxData['camt'];
        $itm_det['samt'] = $taxData['samt'];

        // =============================
        // 🔥 STRICT ITEM TOTAL
        // =============================
        $itemTotal = round(
            $txval +
            $itm_det['iamt'] +
            $itm_det['camt'] +
            $itm_det['samt'] +
            $itm_det['csamt'],
        2);

        $total += $itemTotal;

        $items[] = [
            'num'     => $lineNo++,
            'itm_det' => $itm_det
        ];
    }

    return [
        'total_val' => round($total, 2),
        'items'     => $items
    ];
}
private function voucherItems_oldee(string $from, string $to, int $vchTxnId, array $excludeTaxShort = []): array
{
    $rows = $this->db->table('vchgstsumn')
        ->where('vch_txn_id', $vchTxnId)
        ->where('vch_date >=', $from)
        ->where('vch_date <=', $to)
        ->orderBy('vch_gst_sum_id', 'ASC')
        ->get()
        ->getResultArray();

    $items = [];
    $total = 0;
    $lineNo = 1;

    foreach ($rows as $r) {
        $txval = $this->round2($r['vch_taxable_value'] ?? 0);
        $igst  = $this->round2($r['vch_igst'] ?? 0);
        $cgst  = $this->round2($r['vch_cgst'] ?? 0);
        $sgst  = $this->round2($r['vch_sgst_ugst'] ?? 0);
        $cess  = $this->round2($r['vch_cess'] ?? 0);

        $rt = 0;
        if ($igst > 0 && $txval > 0) {
            $rt = (float)($r['vch_igst_rate'] ?? 0);
        } elseif (($cgst + $sgst) > 0 && $txval > 0) {
            $rt = (float)($r['vch_cgst_rate'] ?? 0) + (float)($r['vch_sgst_ugst_rate'] ?? 0);
        }

        $itm_det = [
            'txval' => $txval,
            'rt'    => $this->round2($rt),
        ];

        if ($igst > 0) {
            $itm_det['iamt'] = $igst;
        } else {
            $itm_det['camt'] = $cgst;
            $itm_det['samt'] = $sgst;
        }

        if ($cess > 0) {
            $itm_det['csamt'] = $cess;
        }

        $items[] = [
            'num'     => $lineNo++,
            'itm_det' => $itm_det
        ];

        $total += $txval + $igst + $cgst + $sgst + $cess;
    }

    return [
        'total_val' => $this->round2($total),
        'items'     => $items
    ];
}
	
	function get_acc_state_code($account_id){
		$acctaddmst_tbl = $this->company_id.'_acctaddmst_'.$this->session->get('ses_comp_fy_id');
		$res_row =  $this->db->table($acctaddmst_tbl)
					  ->select('acc_id,acc_state,acc_city,acc_country')
					  ->where('acc_id', $account_id)
					  ->where('comp_id', $this->company_id)
					  ->orderBy('acc_id','ASC')
					  ->get()->getRowArray();	
		if($res_row){
		   $response = $this->aicountly_db->table('aicountly_stateslist_univdb')->select('state_id, country_id, state_code')->where('state_id',$res_row['acc_state'])->where('country_id',$res_row['acc_country'])->get()->getRowArray();
		   if($response)
            return $response['state_code'];		
            else
			return "04";		   
		  }
	  	  else
		   return "04";
					  
	}
  function GetBCSSVal($from_date,$to_date,$vch_txn_id){
	$party_info       = $this->get_party_info($row["vch_txn_id"]);				  
				  $party_id         = $party_info['master_id'];
			      $party_state_code = $this->get_acc_state_code($party_id);
				  $bo_state_code    = $this->session->get('ses_bostecd');
				  if($bo_state_code ==$party_state_code) 
				    $sply_ty ='INTRA';
                  else
					$sply_ty ='INTER';   
	$acctgstsum_tbl  = $this->company_id.'_acctgstsum_'.$this->session->get('ses_comp_fy_id');
	$builders        = $this->db->table($acctgstsum_tbl.' gstsum');
	$builders->orderBy('gstsum.vch_txn_id');	        
	$builders->where('gstsum.acc_txn_date >=', $from_date);
	$builders->where('gstsum.acc_txn_date <=', $to_date); 
	$builders->where('gstsum.vch_txn_id', $vch_txn_id);
	$response = $builders->get()->getResultArray();
	//echo $this->db->GetLastQuery();
	//echo '<br>';
	$final_sum =0;
	$items    = array();
	if($response){
	  foreach($response as $row){
		  
		 if(parseAmount($row["acc_igst_rate"])==0 || parseAmount($row["acc_igst"])==0){
			$rt = parseAmount($row["acc_cgst_rate"])+parseAmount($row["acc_sgst_rate"]);
			$iamt = parseAmount($row["acc_cgst"])+parseAmount($row["acc_sgst"]);
		    
			$samt = parseAmount($row["acc_sgst"]);
		    $camt = parseAmount($row["acc_cgst"]);
		    
			
			$items[]= array("num"=>(integer)$row["tgsmid"],"itm_det"=>array("txval"=>parseAmount($row["taxable_amt"]),"rt"=>(integer)$rt,"samt"=>parseAmount($samt),"camt"=>parseAmount($camt),"csamt"=>parseAmount($row["acc_cess"]) )); 
		
		 
		 }else{
			$rt = parseAmount($row["acc_igst_rate"]);
			$iamt = parseAmount($row["acc_igst"]);
		 	$items[]= array("num"=>(integer)$row["tgsmid"],"itm_det"=>array("txval"=>parseAmount($row["taxable_amt"]),"rt"=>(integer)$rt,"iamt"=>parseAmount($iamt),"csamt"=>parseAmount($row["acc_cess"]) )); 
		 
		 } 
		  
		  $final_sum = $final_sum+($row["taxable_amt"]+$row["total_tax"]); 
		 
	     }	
	   }	  
	 return array("total_val"=>parseAmount($final_sum),"items"=>$items);
	 
	}
	
		
}