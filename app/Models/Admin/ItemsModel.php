<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Models\CommonModel;

class ItemsModel extends Model	{

    public function __construct() {
       parent::__construct();
	    $this->common        = \Config\Database::connect();	  
	    $this->session       = \Config\Services::session();
	    $this->CommonModel   =  new CommonModel();
	   	$this->company_id    =  $this->session->get('ses_company_id');
		$this->fy_id         =  $this->session->get('ses_comp_fy_id');
		$this->bo_id         =  $this->session->get('ses_boid');
    }
    
   function tax_category_dropdown($tax_cat_type=''){ 
	    $builder = $this->db->table("taxcatmstn t")
            ->select("t.tax_cat_is_active,t.tax_cat_mst_id, t.tax_cat_name, t.tax_cat_type, t.tax_cat_section, r.tax_cat_rate")
            ->join("taxcatrate r", "t.tax_cat_mst_id = r.tax_cat_mst_id AND t.cmp_id = r.cmp_id", "left")
            ->where("t.cmp_id", $this->company_id)
            ->where("t.tax_cat_is_active", 1);
         if($tax_cat_type!='')   
            $builder->where("t.tax_cat_type", $tax_cat_type);
            $data  = $builder->get()->getResultArray();
	   $final_result      = array();
	   $final_result['']  = '';
	   if($data){
		  foreach($data as $row){
		      $rate = parseAmount($row['tax_cat_rate']);
              $final_result[$row['tax_cat_mst_id']] = strtoupper(strtolower($row['tax_cat_name']));		   
	        }
        }
	 
	  return $final_result;	
     }
   
    public function get_last_qty_balance(array $data)
{
    $itemId   = (int)($data['item_id']   ?? 0);
    $unitId   = (int)($data['unit_id']   ?? 0);
    $mcId     = (int)($data['mc_id']     ?? 0);
    $vchTxnId = (int)($data['voucher_txn_id'] ?? 0);
    $asOfDate = date('Y-m-d', strtotime($data['date'] ?? 'today'));

    // itm_id_unit_id is stored as "itemId_unitId"
    $itmIdUnitId = $itemId . '_' . $unitId;

    $builder = $this->db->table('itemtxnmst');
    $builder->select('itm_txn_rate AS last_price, itm_txn_date, vch_txn_id');
    $builder->where('cmp_id', $this->company_id);
    $builder->where('itm_id_unit_id', $itmIdUnitId);
    if ($mcId > 0) {
        $builder->where('mat_cent_id', $mcId);
    }
    if (!empty($this->bo_id)) {
        $builder->where('hobo_id', $this->bo_id);
    }
    $builder->where('itm_txn_date <=', $asOfDate);
    if ($vchTxnId > 0) {
        $builder->where('vch_txn_id !=', $vchTxnId); // exclude current voucher
    }
    $builder->orderBy('itm_txn_date', 'DESC');
    $builder->orderBy('itm_txn_id', 'DESC');
    $builder->limit(1);

    $row = $builder->get()->getRowArray();

    // Shape response like the JS expects: response.balance.LastPrice
    return [
        'LastPrice' => isset($row['last_price']) ? (float)$row['last_price'] : 0.0,
        'LastDate'  => $row['itm_txn_date'] ?? null,
        'LastVch'   => $row['vch_txn_id'] ?? null,
    ];
}
	
	function matrcntr_dropdown(){
		 $comp_mtcnt_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
	     $data =  $this->db->table($comp_mtcnt_tbl)->where('comp_id', $this->company_id)->orderBy('mat_cent_name','ASC')->get()->getResultArray();
	     $final_result      = array();
	     if($data){
		  foreach($data as $row){
              $final_result[$row['mat_cent_id']] =$row['mat_cent_name'];			   
	        }
        }
	  return $final_result;	
     }

     function matrcntr_grp_dropdown(){
		 $mcgrpmstnn_tbl = $this->company_id.'_mcgrpmstnn_'.$this->session->get('ses_comp_fy_id');
	     $data =  $this->db->table($mcgrpmstnn_tbl)->where('comp_id', $this->company_id)->orderBy('mc_grp_name','ASC')->get()->getResultArray();
	     $final_result      = array();
	     if($data){
		  foreach($data as $row){
              $final_result[$row['mc_grp_id']] =$row['mc_grp_name'];			   
	        }
        }
	  return $final_result;	
     }
   
    function ItemTaxInfo($tax_cat_id){
        $response = $this->db->table('taxcatmstn tc')
                            ->select("
                                MAX(CASE WHEN ts.tax_cat_sub_type = 1 THEN ts.tax_cat_rate END) as igst,
                                MAX(CASE WHEN ts.tax_cat_sub_type = 2 THEN ts.tax_cat_rate END) as cgst,
                                MAX(CASE WHEN ts.tax_cat_sub_type = 3 THEN ts.tax_cat_rate END) as sgst,
                                MAX(CASE WHEN ts.tax_cat_sub_type = 4 THEN ts.tax_cat_rate END) as ugst,
                                MAX(CASE WHEN ts.tax_cat_sub_type = 5 THEN ts.tax_cat_rate END) as cess
                            ")
                            ->join('taxcatrate ts', 'ts.tax_cat_mst_id = tc.tax_cat_mst_id', 'left')
                            ->where('tc.tax_cat_mst_id', $tax_cat_id)
                            ->where('tc.tax_cat_is_active', 1)
							->where('tc.cmp_id', $this->company_id)
                            ->get()
                            ->getRowArray();
                            
        return  $response ;        
        
    }
	
    function company_all_items(){
		$this->fy_id         = $this->session->get('ses_comp_fy_id');
	    $this->company_id    = $this->session->get('ses_company_id');
	    $undercrsmt_tbl  = "undercrsmt";
		$itemmaster_tbl  = "itemmaster";		
		$itmmstdetn_tbl  = "itmmstdetn";
		$itmunitmst_tbl  = "itmunitmst";
		
	    $builder = $this->db->table($itemmaster_tbl);
		$builder->join($undercrsmt_tbl, "$undercrsmt_tbl.crs_mst_id = $itemmaster_tbl.itm_id AND $undercrsmt_tbl.crs_mst_type =3", 'left');
		$builder->join($itmmstdetn_tbl, "$itmmstdetn_tbl.itm_id = $itemmaster_tbl.itm_id", "left");
		$builder->join($itmunitmst_tbl, "$itmunitmst_tbl.itm_unit_id = $itmmstdetn_tbl.itm_def_unit_id", "left");
		$builder->select([			
			"$undercrsmt_tbl.under_crs_mst_id as itm_grp_id",
			"$itemmaster_tbl.itm_name as label",
			"$itemmaster_tbl.itm_name as value",
			"$itemmaster_tbl.itm_id",
			"$itemmaster_tbl.tax_cat_mst_id",
			"$itmmstdetn_tbl.itm_hsn",
			"$itemmaster_tbl.itm_upc",
			"$itmunitmst_tbl.itm_unit_id",
			"$itmunitmst_tbl.itm_unit_name",
			"$itmmstdetn_tbl.itm_pur_acc_id",
			"$itmmstdetn_tbl.itm_sales_acc_id"
			]);	
		$builder->where("$itemmaster_tbl.cmp_id ",$this->company_id);	
		$builder->where("$undercrsmt_tbl.cmpfymastr_id ",$this->fy_id);
		$builder->orderBy('itm_name');
		$data = $builder->get()->getResultArray();	
	    $final_result = array();
	   if($data){
		  foreach($data as $row){
			 $item_unit_name = $row['itm_unit_name']; 
			 $item_pur_acc   = $row['itm_pur_acc_id'];
			 $item_sales_acc = $row['itm_sales_acc_id'];
			 $item_mrp       = 0;	
			 
			 $item_tax_info = $this->ItemTaxInfo($row['tax_cat_mst_id']);
			 if($item_tax_info){
				  $igst_rate  = $item_tax_info['igst']; 
				  $cess_rate  = $item_tax_info['cess']; 
				  $cgst_rate  = $item_tax_info['cgst'];
				  $sgst_rate  = $item_tax_info['sgst'];
			 } else{
				  $igst_rate  = 0; 
				  $cess_rate  = 0; 
				  $cgst_rate  = 0;
				  $sgst_rate  = 0;
			   }   
			 
			 $final_result[]     = array(
				"label"          => ucwords($row['label']),
				'value'          => $row['value'],
				'item_id'        => $row['itm_id'],
				'item_upc'       => $row['itm_upc'],
				'item_unit_name' => $row['itm_unit_name'],
				"item_name"      => ucwords($row['value']),
				'item_unit_id'   => $row['itm_unit_id'],
				'item_hsn_sac'   => $row['itm_hsn'],
				'tax_cat_id'     => $row['tax_cat_mst_id'],
				'igst_rate'      => $igst_rate,
				'cess_rate'      => $cess_rate,
				'cgst_rate'      => $cgst_rate,
				'sgst_rate'      => $sgst_rate,
				'item_mrp'       => $item_mrp,
				'cess_basis'     => 1,
				'supply_type'    => 1,// 1 for Goods, 2 for Services,3 for Capital Goods
				'item_sales_acc' => $item_sales_acc,
				'item_pur_acc'   => $item_pur_acc				
               	);
		    }
	   }		
	   return json_encode($final_result);     
     }  
 
   
    public function ajax_items()
{
    $itemgrpmst_tbl  = "itemgrpmst";
    $item_master_tbl = "itemmaster";
    $itmunitmst_tbl  = "itmunitmst";
    $undercrsmt_tbl  = "undercrsmt";
    $itmmstdetn_tbl  = "itmmstdetn";
    $taxcatmstn_tbl  = "taxcatmstn";
    $itemcatmst_tbl  = "itemcatmst";

    // Pagination
    $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
    $pq_rPP     = isset($_POST["pq_rpp"])     ? (int)$_POST["pq_rpp"]     : 10;
    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP < 1)     $pq_rPP     = 10;

    // Parse pq_filter
    $filters     = [];
    $filter_mode = 'and';
    if (!empty($_POST['pq_filter'])) {
        $filter_data = json_decode($_POST['pq_filter'], true);
        if (!empty($filter_data['data']) && is_array($filter_data['data'])) {
            $filters = $filter_data['data'];
        }
        if (!empty($filter_data['mode']) && strtolower($filter_data['mode']) === 'or') {
            $filter_mode = 'or';
        }
    }

    $builder = $this->db->table($item_master_tbl . ' itemmaster');

    // Select fields
    $builder->select("
        $item_master_tbl.*,
        $itmunitmst_tbl.itm_unit_name,
        $undercrsmt_tbl.under_crs_mst_id,
        $undercrsmt_tbl.crs_is_active,
        $undercrsmt_tbl.cmpfymastr_id,
        $itemgrpmst_tbl.itm_grp_name AS itm_group_name,
        $itmmstdetn_tbl.itm_def_unit_id,
        $itmmstdetn_tbl.itm_sales_acc_id,
        $itmmstdetn_tbl.itm_pur_acc_id,
        $itmmstdetn_tbl.itm_hsn,
        $taxcatmstn_tbl.tax_cat_name,
        $itemcatmst_tbl.itm_cat_name
    ");

    // Joins
    $builder->join(
        $itmmstdetn_tbl,
        "$itmmstdetn_tbl.itm_id = itemmaster.itm_id
         AND $itmmstdetn_tbl.cmp_id = " . $this->db->escape($this->company_id),
        'left'
    );
    $builder->join(
        $taxcatmstn_tbl,
        "$taxcatmstn_tbl.tax_cat_mst_id = itemmaster.tax_cat_mst_id
         AND $taxcatmstn_tbl.cmp_id = " . $this->db->escape($this->company_id),
        'left'
    );
    $builder->join(
        $itmunitmst_tbl,
        "$itmunitmst_tbl.itm_unit_id = $itmmstdetn_tbl.itm_def_unit_id
         AND $itmunitmst_tbl.cmp_id = " . $this->db->escape($this->company_id),
        'left'
    );
    $builder->join(
        $itemcatmst_tbl,
        "$itemcatmst_tbl.itm_cat_id = $itmmstdetn_tbl.itm_cat_id
         AND $itemcatmst_tbl.cmp_id = " . $this->db->escape($this->company_id),
        'left'
    );
    $builder->join(
        $undercrsmt_tbl,
        "$undercrsmt_tbl.crs_mst_id = itemmaster.itm_id
         AND $undercrsmt_tbl.crs_mst_type = 3
         AND $undercrsmt_tbl.cmpfymastr_id = $this->fy_id",
        'left'
    );
    $builder->join(
        $itemgrpmst_tbl,
        "$itemgrpmst_tbl.itm_grp_id = $undercrsmt_tbl.under_crs_mst_id",
        'left'
    );

    // Base filters
    $builder->where("$undercrsmt_tbl.cmpfymastr_id IS NOT NULL");
    $builder->where('itemmaster.cmp_id', $this->company_id);

    // Apply filters only when value is non-empty
    if (!empty($filters)) {
        $builder->groupStart();
        foreach ($filters as $f) {
            $col  = $f['dataIndx']  ?? '';
            $val  = isset($f['value']) ? trim($f['value']) : '';
            $cond = strtolower($f['condition'] ?? 'contain');
            if ($val === '') continue;

            // Map dataIndx -> column + type
            $map = [
                'item_id'     => ['col' => 'itemmaster.itm_id',       'type' => 'int'],
                'item_name'   => ['col' => 'itemmaster.itm_name',     'type' => 'str'],
                'item_alias'  => ['col' => 'itemmaster.itm_alias',    'type' => 'str'],
                'item_sku'    => ['col' => 'itemmaster.itm_sku',      'type' => 'str'],
                'item_upc'    => ['col' => 'itemmaster.itm_upc',      'type' => 'str'],
                'item_group'  => ['col' => "$itemgrpmst_tbl.itm_grp_name", 'type' => 'str'],
                'item_unit'   => ['col' => "$itmunitmst_tbl.itm_unit_name", 'type' => 'str'],
                'item_cat'    => ['col' => "$itemcatmst_tbl.itm_cat_name", 'type' => 'str'],
                'item_tax_cat'=> ['col' => "$taxcatmstn_tbl.tax_cat_name", 'type' => 'str'],
                'item_status' => ['col' => "$undercrsmt_tbl.crs_is_active", 'type' => 'status'], // ACTIVE/INACTIVE
            ];
            if (!isset($map[$col])) continue;

            $dbCol    = $map[$col]['col'];
            $colType  = $map[$col]['type'];
            $valLower = strtolower($val);

            $apply = function($expr, $mode) use ($builder, $filter_mode) {
                if ($filter_mode === 'or') {
                    if (is_array($expr)) {
                        $builder->orWhere($expr[0], $expr[1]);
                    } else {
                        $builder->orWhere($expr, null, false);
                    }
                } else {
                    if (is_array($expr)) {
                        $builder->where($expr[0], $expr[1]);
                    } else {
                        $builder->where($expr, null, false);
                    }
                }
            };

            switch ($colType) {
                case 'int':
                    $castCol = "CAST($dbCol AS TEXT)";
                    if (is_numeric($val)) {
                        if     ($cond === 'equal')    $apply([$dbCol, (int)$val], '=');
                        elseif ($cond === 'notequal') $apply(["$dbCol !=", (int)$val], '!=');
                        elseif ($cond === 'begin')    $apply("$castCol ILIKE " . $this->db->escape($val . '%'), 'like');
                        elseif ($cond === 'end')      $apply("$castCol ILIKE " . $this->db->escape('%' . $val), 'like');
                        elseif ($cond === 'notcontain') $apply("$castCol NOT ILIKE " . $this->db->escape('%' . $val . '%'), 'like');
                        else                           $apply("$castCol ILIKE " . $this->db->escape('%' . $val . '%'), 'like');
                    } else {
                        if     ($cond === 'begin')      $apply("$castCol ILIKE " . $this->db->escape($val . '%'), 'like');
                        elseif ($cond === 'end')        $apply("$castCol ILIKE " . $this->db->escape('%' . $val), 'like');
                        elseif ($cond === 'notcontain') $apply("$castCol NOT ILIKE " . $this->db->escape('%' . $val . '%'), 'like');
                        elseif ($cond === 'equal')      $apply("$castCol ILIKE " . $this->db->escape($val), 'like');
                        elseif ($cond === 'notequal')   $apply("$castCol NOT ILIKE " . $this->db->escape($val), 'like');
                        else                            $apply("$castCol ILIKE " . $this->db->escape('%' . $val . '%'), 'like');
                    }
                    break;

                case 'status': // ACTIVE/INACTIVE -> 1/0
                    if (strpos($valLower, 'inactive') !== false) {
                        $apply([$dbCol, 0], '=');
                    } elseif (strpos($valLower, 'active') !== false) {
                        $apply([$dbCol, 1], '=');
                    }
                    break;

                case 'str':
                default:
                    $likeVal = strtolower($val);
                    $lowerCol = "LOWER($dbCol)";
                    if     ($cond === 'equal')        $apply([$lowerCol, $likeVal], '=');
                    elseif ($cond === 'notequal')     $apply(["$lowerCol !=", $likeVal], '!=');
                    elseif ($cond === 'begin')        $apply("$lowerCol LIKE " . $this->db->escape($likeVal . '%'), 'like');
                    elseif ($cond === 'end')          $apply("$lowerCol LIKE " . $this->db->escape('%' . $likeVal), 'like');
                    elseif ($cond === 'notcontain')   $apply("$lowerCol NOT LIKE " . $this->db->escape('%' . $likeVal . '%'), 'like');
                    elseif ($cond === 'empty')        { ($filter_mode === 'or' ? $builder->orGroupStart() : $builder->groupStart()); $builder->where("$dbCol IS NULL", null, false)->orWhere("TRIM($dbCol) = ''", null, false); $builder->groupEnd(); }
                    elseif ($cond === 'notempty')     { ($filter_mode === 'or' ? $builder->orGroupStart() : $builder->groupStart()); $builder->where("$dbCol IS NOT NULL", null, false)->where("TRIM($dbCol) != ''", null, false); $builder->groupEnd(); }
                    else                              $apply("$lowerCol LIKE " . $this->db->escape('%' . $likeVal . '%'), 'like');
                    break;
            }
        }
        $builder->groupEnd();
    }

    // Count before limit
    $countBuilder = clone $builder;
    $countBuilder->select('1');
    $total_Records = $countBuilder->countAllResults(false);

    // Pagination window
    $offset = ($pq_rPP * ($pq_curPage - 1));
    if ($offset > $total_Records) {
        $pq_curPage = ($total_Records > 0) ? (int)ceil($total_Records / $pq_rPP) : 1;
        $offset = ($pq_rPP * ($pq_curPage - 1));
    }
    if ($offset < 0) $offset = 0;

    $builder->orderBy('itemmaster.itm_name');
    $builder->limit($pq_rPP, $offset);
    $result = $builder->get()->getResultArray();

    // Build response rows
    $records = [];
    foreach ($result as $values) {
        $confirmstatus = ($values['crs_is_active'] == 1) ? 'INACTIVE' : 'ACTIVE';
        $acc_status_vl = ($values['crs_is_active'] == 1) ? 0 : 1;
        $show_group    = $values['itm_group_name'] ?? '';

        $records[] = [
            'checkbox'        => '<input name="item_ids[]" class="checkbox items_row" data-acc_status_vl="'.$acc_status_vl.'" data-confirmstatus="'.$confirmstatus.'" data-id="'.$values['itm_id'].'" type="checkbox" value="'.$values['itm_id'].'">',
            'item_upc'        => $values['itm_upc'],
            'item_name'       => ucwords($values['itm_name']).'('.$values['itm_id'].')',
            'item_alias'      => $values['itm_alias'],
            'item_grp'        => $show_group,
            'item_unit'       => $values['itm_unit_name'],
            'item_cat'        => $values['itm_cat_name'],
            'item_sku'        => $values['itm_sku'],
            'item_id'         => $values['itm_id'],
            'item_tax_catg'   => $values['tax_cat_name'],
            'acc_status_vl'   => $acc_status_vl,
            'item_status'     => ($values['crs_is_active'] == 1) ? 'ACTIVE' : 'INACTIVE',
            'alert_acc_status'=> $confirmstatus
        ];
    }

    echo json_encode([
        "totalRecords" => $total_Records,
        "curPage"      => $pq_curPage,
        "data"         => $records
    ]);
}
	
	
public function all_items_export()
{
    $builder = $this->db->table('itemmaster im');

    $builder->select("
        im.itm_id,
        im.itm_name,
        im.itm_alias,
        im.itm_print_name,
        im.itm_sku,
        im.itm_upc,

        grp.itm_grp_name,
        unit.itm_unit_name,
        cat.itm_cat_name,
        tax.tax_cat_name,

        det.itm_hsn,

        sales.acc_name    AS sales_acc_name,
        purchase.acc_name AS purchase_acc_name,

        opbal.itm_op_bal_qty,
        opval.itm_op_val_amt,
        opval.itm_val_method_id,

        uc.crs_is_active
    ");

    $builder->join(
        'itmmstdetn det',
        "det.itm_id = im.itm_id
        AND det.cmp_id = {$this->company_id}",
        'left'
    );

    $builder->join(
        'taxcatmstn tax',
        "tax.tax_cat_mst_id = im.tax_cat_mst_id
        AND tax.cmp_id = {$this->company_id}",
        'left'
    );

    $builder->join(
        'itmunitmst unit',
        "unit.itm_unit_id = det.itm_def_unit_id
        AND unit.cmp_id = {$this->company_id}",
        'left'
    );

    $builder->join(
        'itemcatmst cat',
        "cat.itm_cat_id = det.itm_cat_id
        AND cat.cmp_id = {$this->company_id}",
        'left'
    );

    $builder->join(
        'acctmaster sales',
        "sales.acc_id = det.itm_sales_acc_id
        AND sales.cmp_id = {$this->company_id}",
        'left'
    );

    $builder->join(
        'acctmaster purchase',
        "purchase.acc_id = det.itm_pur_acc_id
        AND purchase.cmp_id = {$this->company_id}",
        'left'
    );

    $builder->join(
        'undercrsmt uc',
        "uc.crs_mst_id = im.itm_id
        AND uc.crs_mst_type = 3
        AND uc.cmpfymastr_id = {$this->fy_id}",
        'left'
    );

    $builder->join(
        'itemgrpmst grp',
        'grp.itm_grp_id = uc.under_crs_mst_id',
        'left'
    );

    $builder->join(
        'itmoppybal opbal',
        "opbal.itm_id_unit_id::text = im.itm_id::text
        AND opbal.cmpfymastr_id = {$this->fy_id}",
        'left',
        false
    );

    $builder->join(
        'itmoppyval opval',
        "opval.itm_id_unit_id = opbal.itm_id_unit_id
        AND opval.cmpfymastr_id = {$this->fy_id}",
        'left'
    );

    $builder->where('im.cmp_id', $this->company_id);

    $builder->where(
        'uc.cmpfymastr_id IS NOT NULL',
        null,
        false
    );

    $builder->orderBy('im.itm_name', 'ASC');

    return $builder->get()->getResultArray();
}

    public function get_item_op_bal_list($item_id){
		$itmoppyval_tbl = "itmoppyval";
		$itmoppybal_tbl = "itmoppybal";
		$mcmasternn_tbl = "matcentmst";
        $final          = [];
		// 1. Get Opening Balances per Unit
		$op_bal_units = $this->db->table($itmoppybal_tbl)
			->select('itm_id_unit_id, SUM(itm_op_bal_qty) as op_bal_qty')
			 ->where('itm_id_unit_id LIKE', $item_id . '_%')
			 ->where('cmp_id', $this->company_id)
			->where('hobo_id', $this->bo_id) 
			->where('cmpfymastr_id', $this->fy_id)       
			->groupBy('itm_id_unit_id')
			->get()->getResultArray();

		if (empty($op_bal_units)) {
			return [];
		}

		// 2. Get Material Centers once
		$mat_centers = $this->db->table("matcentmst")->where('cmp_id', $this->company_id)->get()->getResultArray();

		// 3. Get all opening values (AVG, FIFO, LIFO) for item
		$all_op_values = $this->db->table("itmoppyval")
			->select('itm_id_unit_id, mat_cent_id, itm_val_method_id, itm_op_val_amt')
			 ->where('itm_id_unit_id LIKE', $item_id . '_%')
			->where('hobo_id', $this->bo_id) 
			 ->where('cmp_id', $this->company_id)
			->where('cmpfymastr_id', $this->fy_id)  
			->orderBy('mat_cent_id')		
			->get()->getResultArray();

		// Arrange by unit, mat_cent_id
		$op_values = [];
		foreach ($all_op_values as $row) {
			$itm_id_unit_id = $row['itm_id_unit_id'];
			$item_unit_info =  explode("_",$itm_id_unit_id);
			$item_unit      =  $item_unit_info[1];
			$op_values[$item_unit][$row['mat_cent_id']][$row['itm_val_method_id']] = floatval($row['itm_op_val_amt']);
		}

		// 4. Get opening quantities per material center
		$all_op_qty = $this->db->table($itmoppybal_tbl)
			->select('itm_id_unit_id, mat_cent_id, itm_op_bal_qty')
			 ->where('itm_id_unit_id LIKE', $item_id . '_%')
			->where('hobo_id', $this->bo_id)    
			 ->where('cmp_id', $this->company_id)
			->where('cmpfymastr_id', $this->fy_id) 
			->get()->getResultArray();

		// Arrange quantities by unit, mat_cent_id
		$op_qty = [];
		foreach ($all_op_qty as $row) {
			$itm_id_unit_id = $row['itm_id_unit_id'];
			$item_unit_info =  explode("_",$itm_id_unit_id);
			$item_unit      =  $item_unit_info[1]; 
			$op_qty[$item_unit][$row['mat_cent_id']] = floatval($row['itm_op_bal_qty']);
		}

		// 5. Get existing transaction status
		$exist_status = $this->db->table("itemtxnmst")
			->select('itm_id_unit_id')
			->where('itm_id_unit_id LIKE', $item_id . '_%')
			 ->where('cmp_id', $this->company_id)
			->where('hobo_id', $this->bo_id)
			->groupBy('itm_id_unit_id')
			->get()->getResultArray();

		$existing_units = array_column($exist_status, 'itm_id_unit_id');

		// 6. Final Merge
		foreach ($op_bal_units as $unit) {
			$list = [];
			foreach ($mat_centers as $center) {
				$mat_id            = $center['mat_cent_id'];
				$itm_id_unit_id         = $unit['itm_id_unit_id'];
				$item_unit_info =  explode("_",$itm_id_unit_id);
				$item_unit      =  $item_unit_info[1]; 

				$list[] = [
					'mat_cent_id'   => $mat_id,
					'mat_cent_name' => $center['mat_cent_name'],
					'op_bal_qty'    => $op_qty[$item_unit][$mat_id] ?? 0,
					'avg'           => $op_values[$item_unit][$mat_id]['AVG'] ?? 0,
					'fifo'          => $op_values[$item_unit][$mat_id]['FIFO'] ?? 0,
					'lifo'          => $op_values[$item_unit][$mat_id]['LIFO'] ?? 0,
				];
			}
			
			$itm_id_unit_id = $unit['itm_id_unit_id'];
			$item_unit_info =  explode("_",$itm_id_unit_id);
			$item_unit      =  $item_unit_info[1]; 

			$final[] = [
				'item_unit'  => $item_unit,
				'op_bal_qty' => $unit['op_bal_qty'],
				'list'       => $list,
				'status'     => in_array($item_unit, $existing_units) ? 1 : 0,
			];
		}
    return $final;
	}	
     
    function item_unit_info($unit_id){
       return  $this->db->table("itmunitmst")->where('itm_unit_id', $unit_id)->where('cmp_id', $this->company_id)->orderBy('itm_unit_name','ASC')->get()->getRowArray();   
    }
	
    public function InsertOppBal($data)
	{
		$this->db->table('itmoppybal')->insert($data);
	}
	public function update_itm_txn_opbal_entry($txn_data,$itmid_unitid,$itm_txn_type){		
	    $this->db->table("itemtxnmst")->where('cmp_id',$this->company_id)
		->where('itm_id_unit_id',$itmid_unitid)->where('itm_txn_type',$itm_txn_type)
		->where('vch_txn_id IS NULL')->update($txn_data);
    }
   
   public function load_items_ledger__working_without_valuation(){
	    $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
		$pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10; // default 10 rows per page
        if ($pq_curPage < 1) $pq_curPage = 1;
		if ($pq_rPP < 1) $pq_rPP = 10;		
		
		$from_date     = $_POST['from_date'] ?? '';
        $to_date       = $_POST['to_date'] ?? '';
        $from_date     = validate_from_date($from_date);
        $to_date       = validate_to_date($to_date);
		$from_date     = date('Y-m-d',strtotime($from_date));
		$to_date       = date('Y-m-d',strtotime($to_date));
		$item_id       = !empty($_POST['item_id'])? $_POST['item_id'] : 0;
		$unit_id       = !empty($_POST['unit_id']) ? $_POST['unit_id'] : 0;
		$mc_id         = !empty($_POST['mc_id']) ? $_POST['mc_id'] : 0;
        $mc_grp_id     = !empty($_POST['mc_grp_id']) ? $_POST['mc_grp_id'] : 0;
		
		$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");		
		$fy_start_date = date('Y', strtotime($from_date)) . '-04-01';
		// VOUCHERS COUNTER AS PER FY DATE AND FILTER START DATE
		$voucher_count = $this->db->table('vchtxnconso vc')
				->select('vc.vch_txn_id')
				->join('cmptxnmstn cm', 'cm.vch_txn_id = vc.vch_txn_id', 'inner')
				->where('vc.cmp_id', $this->company_id)
				->where('vc.hobo_id', $this->bo_id)
				->where('vc.vch_date >=', $fy_start_date)
				->where('vc.vch_date <', $from_date)
				->where('cm.master_id', $item_id)
				->where('cm.master_id_type', 'itm')
				->distinct() 
				->countAllResults();	
				
		$openings = $this->db->table('itmoppybal')
					->select('itm_id_unit_id, itm_op_bal_qty')
					->where('cmpfymastr_id', $this->fy_id)
					->where('hobo_id', $this->bo_id)
					->where('cmp_id', $this->company_id)
					->get()
					->getResultArray();

				// Re-index by itm_id_unit_id
				$openingBalances = [];
				foreach ($openings as $op) {
					$openingBalances[$op['itm_id_unit_id']] = $op['itm_op_bal_qty'];
				}

		$builder = $this->db->table('itemtxnmst t');
		$builder->select("
			t.itm_txn_date,
			c.vch_type_id AS voucher_type_id,
			vt.vch_name AS voucher_type,
			t.itm_id_unit_id,
			u.itm_unit_name AS unit_name,
			t.itm_txn_dr_cr,
			t.itm_txn_rate,
			t.itm_txn_amt,
			t.itm_txn_qty,
			c.vch_txn_id
		");
		$builder->join('vchtxnconso c', 'c.vch_txn_id = t.vch_txn_id', 'left');
		$builder->join('vchtypemst vt', 'vt.vch_type_id = c.vch_type_id', 'left');
		$builder->join('itmunitmst u', 'u.itm_unit_id = SUBSTRING_INDEX(t.itm_id_unit_id, "_", -1)', 'left');

		$builder->where('SUBSTRING_INDEX(t.itm_id_unit_id, "_", 1)', $item_id);
		$builder->where('t.itm_txn_date >=', $from_date);
		$builder->where('t.itm_txn_date <=', $to_date);
		$builder->where('t.cmp_id', $this->company_id);
		$builder->where('t.hobo_id', $this->bo_id);
		$builder->orderBy('t.itm_txn_date', 'ASC');
		$builder->orderBy('t.itm_txn_id', 'ASC');

		$countBuilder = clone $builder;
		$total_records = $countBuilder->countAllResults(false);

		if ($pq_curPage == 0) $pq_curPage = 1;
		$offset = ($pq_rPP * ($pq_curPage - 1));
		if ($offset > $total_records) {
			$pq_curPage = ceil($total_records / $pq_rPP);
			$offset = ($pq_rPP * ($pq_curPage - 1));
		}
		if ($offset < 0) $offset = 0;

		$builder->limit($pq_rPP, $offset);

		$rows = $builder->get()->getResultArray();
		$balances     = []; // Keyed by itm_id_unit_id
		$grouped_data = []; // Grouped by vch_txn_id + itm_id_unit_id
		$voucher_count += $offset;
		$voucher_map = [];

		foreach ($rows as $row) {
			$group_key = $row['vch_txn_id'] . '_' . $row['itm_id_unit_id'];

			
			if (!isset($grouped_data[$group_key])) {
				$grouped_data[$group_key] = [
					'itm_txn_date'    => $row['itm_txn_date'],
					'voucher_type_id' => $row['voucher_type_id'],
					'voucher_type'    => $row['voucher_type'],
					'vch_txn_id'      => $row['vch_txn_id'],
					'itm_id_unit_id'  => $row['itm_id_unit_id'],
					'unit_name'       => $row['unit_name'],
					'inward_qty'      => 0,
					'inward_amount'   => 0,
					'inward_rate'     => '',  // Keep last inward rate
					'outward_qty'     => 0,
					'outward_amount'  => 0,
					'outward_rate'    => '',  // Keep last outward rate
				];
			}

			if ($row['itm_txn_dr_cr'] == 1) { // Inward
				$grouped_data[$group_key]['inward_qty']    += $row['itm_txn_qty'];
				$grouped_data[$group_key]['inward_amount'] += $row['itm_txn_amt'];
				$grouped_data[$group_key]['inward_rate']   = $row['itm_txn_rate'];
			} else { // Outward
				$grouped_data[$group_key]['outward_qty']    += $row['itm_txn_qty'];
				$grouped_data[$group_key]['outward_amount'] += $row['itm_txn_amt'];
				$grouped_data[$group_key]['outward_rate']   = $row['itm_txn_rate'];
			}
		}

		$final_data    = [];
		$last_vch_txn_id = null;

		foreach ($grouped_data as $group) {
			$unit_key = $group['itm_id_unit_id'];

			if (!isset($balances[$unit_key])) {
				$balances[$unit_key] = [
					'qty'    => $openingBalances[$unit_key] ?? 0,
					'amount' => 0
				];
			}

			if ($group['vch_txn_id'] != $last_vch_txn_id) {
				$voucher_count++;
				$last_vch_txn_id = $group['vch_txn_id'];
			}

		
			$balances[$unit_key]['qty']    += $group['inward_qty'] - $group['outward_qty'];
			$balances[$unit_key]['amount'] += $group['inward_amount'] - $group['outward_amount'];
			$balance_crdr = ($balances[$unit_key]['amount'] < 0) ? 'CR.' : 'DR.';

			$final_data[] = [
				'voucher_date'    => $group['itm_txn_date'],
				'voucher_type'    => $group['voucher_type'],
				'voucher_type_id' => $group['voucher_type_id'],
				'particulars'     => 'Item Txn: ' . $group['vch_txn_id'],
				'voucher'         => $group['voucher_type'] . '(No. ' . $voucher_count . ')',
				'inward_qty'      => $group['inward_qty'],
				'inward_rate'     => $group['inward_rate'],
				'inward_amount'   => $group['inward_amount'],
				'outward_qty'     => $group['outward_qty'],
				'outward_rate'    => $group['outward_rate'],
				'outward_amount'  => $group['outward_amount'],
				'closing_qty'     => $balances[$unit_key]['qty'] ?? 0,
				'unit_name'       => $group['unit_name'],
				'balance_amount'  => formatAmount(abs($balances[$unit_key]['amount'])) . ' ' . $balance_crdr,
				'profit'          => '',
				'profit_string'   => '',
				'voucher_txn_id'  => $group['vch_txn_id']
			];
		}
		
     return  "{\"totalRecords\":" .$total_records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($final_data)."}";        
		
		
   }
   
public function get_item_opening_balances($item_id)
{
    $item_id = (int) $item_id;

    $unit_id   = !empty($_GET['unit_id']) ? (int) $_GET['unit_id'] : 0;
    $mc_id     = !empty($_GET['mc_id']) ? (int) $_GET['mc_id'] : 0; // kept for parity
    $from_date = validate_fy_from_date($_GET['from_date'] ?? '');
    $to_date   = validate_fy_to_date($_GET['to_date'] ?? '');
    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date   = date('Y-m-d', strtotime($to_date));
    $valuation_method_id = !empty($_GET['val_id']) ? $_GET['val_id'] : $this->session->get('ses_dflt_val_method');

    // Step 1: material centers
    $matCenters = $this->db->table('matcentmst')
        ->select('mat_cent_id, mat_cent_name')
        ->where('cmp_id', $this->company_id)
        ->get()
        ->getResultArray();

    // Step 2: units for this item
    $units = $this->get_item_units($item_id);

    // Step 3: opening balances/valuation via raw SQL to keep casts intact
    $sql = "
        SELECT
            o.mat_cent_id,
            split_part(o.itm_id_unit_id::text, '_', 2)::int AS unit_id,
            SUM(ob.itm_op_bal_qty) AS opening_qty,
            SUM(o.itm_op_val_amt) AS opening_valuation_value
        FROM itmoppyval o
        LEFT JOIN itmoppybal ob
            ON ob.itm_id_unit_id = o.itm_id_unit_id
           AND ob.mat_cent_id     = o.mat_cent_id
           AND ob.cmp_id          = o.cmp_id
        WHERE o.cmp_id          = ?
          AND o.hobo_id         = ?
          AND o.cmpfymastr_id   = ?
          AND o.itm_val_method_id = ?
          AND split_part(o.itm_id_unit_id::text, '_', 1)::int  = ?
          AND split_part(ob.itm_id_unit_id::text, '_', 1)::int = ?
    ";

    $params = [
        $this->company_id,
        $this->bo_id,
        $this->fy_id,
        $valuation_method_id,
        $item_id,
        $item_id,
    ];

    if ($unit_id > 0) {
        $sql .= " AND split_part(o.itm_id_unit_id::text, '_', 2)::int = ? ";
        $params[] = $unit_id;
    }

    $sql .= " GROUP BY o.mat_cent_id, unit_id ";

    $results = $this->db->query($sql, $params)->getResultArray();

    // Re-index
    $balanceMap = [];
    foreach ($results as $row) {
        $balanceMap[$row['mat_cent_id']][$row['unit_id']] = [
            'opening_qty'             => $row['opening_qty'] ?? '0.00',
            'opening_valuation_value' => $row['opening_valuation_value'] ?? '0.0000',
        ];
    }

    // Step 4: build final data
    $final_data = [];
    foreach ($matCenters as $mc) {
        foreach ($units as $unit) {
            $totals = $balanceMap[$mc['mat_cent_id']][$unit['unit_id']] ?? [
                'opening_qty'             => '0.00',
                'opening_valuation_value' => '0.0000',
            ];
            $final_data[] = [
                'mat_cent_name'            => $mc['mat_cent_name'],
                'unit_id'                  => $unit['unit_id'],
                'unit_name'                => $unit['unit_name'],
                'opening_qty'              => $totals['opening_qty'],
                'opening_valuation_value'  => $totals['opening_valuation_value'],
                'valuation_method_name'    => $valuation_method_id,
            ];
        }
    }

    return json_encode([
        'status' => 'success',
        'data'   => $final_data,
    ]);
}

   public function get_item_closing_totals($item_id)
{
    $unit_id  = (int)($_GET['unit_id'] ?? 0);
    $mc_id    = (int)($_GET['mc_id']   ?? 0);
    $from_date = date('Y-m-d', strtotime(validate_fy_from_date($_GET['from_date'] ?? '')));
    $to_date   = date('Y-m-d', strtotime(validate_fy_to_date($_GET['to_date'] ?? '')));

    // val_id can be 'AUTO'|'FIFO'|'LIFO'|'AVG' or 1/2/3
    $val_req = $_GET['val_id'] ?? $this->session->get('ses_dflt_val_method');

    // 1) Material centers (filter if requested)
    $mcQ = $this->db->table('matcentmst')->where("cmp_id", $this->company_id)
        ->select('mat_cent_id, mat_cent_name');
    if ($mc_id > 0) $mcQ->where('mat_cent_id', $mc_id);
    $matCenters = $mcQ->get()->getResultArray();

    // 2) Units of this item (filter if requested)
    $units = $this->get_item_units($item_id); // expected: [ ['unit_id'=>2,'unit_name'=>'PC'], ... ]
    if ($unit_id > 0) {
        $units = array_values(array_filter($units, fn($u)=> (int)$u['unit_id'] === $unit_id));
    }

    // Helpers
   $resolveMethod = function (string $itmKey, int $mcId) use ($val_req): string
{
    // normalize request to uppercase string
    $req = strtoupper(trim((string)$val_req));

    // If caller specifies a concrete method, trust it
    if (in_array($req, ['AVG','FIFO','LIFO'], true)) {
        return $req;
    }

    // AUTO (or anything else): derive default from openings for this item+MC
    $row = $this->db->table('itmoppyval')
        ->select('itm_val_method, itm_val_method_id') // `itm_val_method` text is optional; `*_id` kept for backward compat
        ->where('cmp_id', $this->company_id)
        ->where('hobo_id', $this->bo_id)
        ->where('cmpfymastr_id', $this->fy_id)
        ->where('itm_id_unit_id', $itmKey)
        ->where('mat_cent_id', $mcId)
        ->limit(1)
        ->get()->getRowArray();

    // Prefer textual column if present
    if (!empty($row['itm_val_method'])) {
        $m = strtoupper(trim($row['itm_val_method']));
        return in_array($m, ['AVG','FIFO','LIFO'], true) ? $m : 'AVG';
    }

};

    $pushBall = function(array &$balls, float $qty, float $rate): void {
        if ($qty == 0.0) return;
        $n = count($balls);
        if ($n>0 && abs($balls[$n-1]['cost'] - $rate) < 1e-10) $balls[$n-1]['qty'] += $qty;
        else $balls[] = ['qty'=>$qty,'cost'=>$rate];
        while (!empty($balls) && abs($balls[0]['qty']) <= 1e-12) array_shift($balls);
        while (!empty($balls) && abs($balls[count($balls)-1]['qty']) <= 1e-12) array_pop($balls);
    };
    $consume = function(array &$balls, float $qty, string $method, bool $allowNeg=true): float {
        $value=0.0; $need=$qty;
        if ($method==='FIFO') {
            while ($need>1e-12 && !empty($balls)) {
                $take = min($need, $balls[0]['qty']);
                if ($take>0){ $value += $take*$balls[0]['cost']; $balls[0]['qty'] -= $take; $need -= $take; }
                if ($balls[0]['qty']<=1e-12) array_shift($balls);
            }
        } else { // LIFO
            while ($need>1e-12 && !empty($balls)) {
                $i = count($balls)-1; $take = min($need, $balls[$i]['qty']);
                if ($take>0){ $value += $take*$balls[$i]['cost']; $balls[$i]['qty'] -= $take; $need -= $take; }
                if ($balls[$i]['qty']<=1e-12) array_pop($balls);
            }
        }
        if ($need>1e-12 && $allowNeg) $balls[]=['qty'=>-$need,'cost'=>0.0];
        return $value;
    };

    $avgIn = function(array &$avg, float $inQty, float $inRate): void {
        $q0 = (float)($avg['qty'] ?? 0.0);
        $a0 = (float)($avg['avg'] ?? 0.0);
        $val = $q0*$a0 + $inQty*$inRate; $q1 = $q0 + $inQty;
        $avg['qty'] = $q1; $avg['avg'] = $q1>0 ? $val/$q1 : 0.0;
    };
    $avgOut = function(array &$avg, float $outQty, bool $allowNeg=true): float {
        $q0 = (float)($avg['qty'] ?? 0.0);
        $a0 = (float)($avg['avg'] ?? 0.0);
        $issue = $outQty*$a0; $q1 = $q0 - $outQty;
        if ($q1<-1e-12 && !$allowNeg) throw new \RuntimeException('Insufficient AVG');
        $avg['qty'] = $q1; return $issue;
    };

    $final = [];

    foreach ($matCenters as $mc) {
        $mcId   = (int)$mc['mat_cent_id'];
        $mcName = $mc['mat_cent_name'];

        foreach ($units as $u) {
            $uid    = (int)$u['unit_id'];
            $uname  = $u['unit_name'];
            $key    = $item_id.'_'.$uid;

            // Resolve method for this (item,unit,mc)
            $method = $resolveMethod($key, $mcId);

            // Opening per MC
            $opqRow = $this->db->table('itmoppybal')
                ->select('COALESCE(SUM(itm_op_bal_qty),0) AS q')
                ->where('cmp_id',$this->company_id)->where('hobo_id',$this->bo_id)->where('cmpfymastr_id',$this->fy_id)
                ->where('itm_id_unit_id',$key)->where('mat_cent_id',$mcId)
                ->get()->getRowArray();
            $opQty = (float)($opqRow['q'] ?? 0.0);

            $opvRow = $this->db->table('itmoppyval')
                ->select('COALESCE(SUM(itm_op_val_amt),0) AS v')
                ->where('cmp_id',$this->company_id)->where('hobo_id',$this->bo_id)->where('cmpfymastr_id',$this->fy_id)
                ->where('itm_id_unit_id',$key)->where('mat_cent_id',$mcId)
                ->get()->getRowArray();
            $opVal  = (float)($opvRow['v'] ?? 0.0);
            $opRate = ($opQty>0 && $opVal>0) ? ($opVal/$opQty) : 0.0;

            // Seed state
            $balls=[]; $avg=['qty'=>0.0,'avg'=>0.0];
            if ($opQty!=0.0) {
                if ($method==='AVG') { $avg=['qty'=>$opQty,'avg'=>$opRate]; }
                else { $pushBall($balls, $opQty, $opRate); }
            }

            // Replay MC-filtered txns (FY window)
            $txns = $this->db->table('itemtxnmst t')
                ->select('t.itm_txn_date, t.itm_txn_dr_cr, t.itm_txn_qty, t.itm_txn_rate, t.itm_txn_amt, t.itm_txn_id')
                ->where('t.cmp_id', $this->company_id)
                ->where('t.hobo_id', $this->bo_id)
                ->where('t.itm_id_unit_id', $key)
                ->where('t.mat_cent_id', $mcId)
                ->where('t.itm_txn_date >=', $from_date)
                ->where('t.itm_txn_date <=', $to_date)
                ->orderBy('t.itm_txn_date','ASC')
                ->orderBy('t.itm_txn_id','ASC')
                ->get()->getResultArray();

            foreach ($txns as $t) {
                $qty  = (float)$t['itm_txn_qty'];
                $amt  = (float)$t['itm_txn_amt'];
                $rate = ($qty!=0.0) ? $amt/$qty : (float)$t['itm_txn_rate'];

                if ((int)$t['itm_txn_dr_cr'] === 1) {
                    if ($method==='AVG') $avgIn($avg, $qty, $rate);
                    else $pushBall($balls, $qty, $rate);
                } else {
                    if ($method==='AVG') { $avgOut($avg, $qty); }
                    else { $consume($balls, $qty, $method); }
                }
            }

            // Closing qty & value
            if ($method==='AVG') {
                $clQty = (float)$avg['qty'];
                $clVal = $clQty * (float)$avg['avg'];
            } else {
                $clQty = 0.0; $clVal = 0.0;
                foreach ($balls as $b) { $clQty += $b['qty']; $clVal += $b['qty'] * $b['cost']; }
            }

            $final[] = [
                'mat_cent_name' => $mcName,
                'unit_id'       => $uid,
                'unit_name'     => $uname,
                'closing_qty'   => parseAmount($clQty),
                'closing_value' => parseAmount($clVal),
                'method_name'   => $method,
            ];
        }
    }

    return json_encode(['status'=>'success','data'=>$final]);
}

public function get_item_summary_totals($item_id){
    $item_id = (int) $item_id;

    $unit_id   = !empty($_GET['unit_id']) ? (int) $_GET['unit_id'] : 0;
    $mc_id     = !empty($_GET['mc_id']) ? (int) $_GET['mc_id'] : 0;
    $from_date = validate_fy_from_date($_GET['from_date'] ?? '');
    $to_date   = validate_fy_to_date($_GET['to_date'] ?? '');
    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date   = date('Y-m-d', strtotime($to_date));

    // Step 1: Get all material centers
    $matCenters = $this->db->table('matcentmst')
        ->select('mat_cent_id, mat_cent_name')
        ->where("cmp_id", $this->company_id)
        ->get()
        ->getResultArray();

    // Step 2: Get all units (assuming get_item_units is already fixed for PostgreSQL)
    $units = $this->get_item_units($item_id);

    // Step 3: Fetch all txn totals in ONE query using PostgreSQL's split_part
    $builder = $this->db->table('itemtxnmst t', false);
    $builder->select("
        c.mat_cent_id,
        split_part(t.itm_id_unit_id::text, '_', 2)::int AS unit_id,
        SUM(CASE WHEN t.itm_txn_dr_cr = 1 THEN t.itm_txn_qty ELSE 0 END) AS total_in_qty,
        SUM(CASE WHEN t.itm_txn_dr_cr = 1 THEN t.itm_txn_amt ELSE 0 END) AS total_in_amount,
        SUM(CASE WHEN t.itm_txn_dr_cr = 2 THEN t.itm_txn_qty ELSE 0 END) AS total_out_qty,
        SUM(CASE WHEN t.itm_txn_dr_cr = 2 THEN t.itm_txn_amt ELSE 0 END) AS total_out_amount
    ", false); // Pass false to prevent escaping of the raw select expression

    $builder->join('vchtxnconso c', 'c.vch_txn_id = t.vch_txn_id', 'left', false);
    $builder->where('t.itm_txn_date >=', $from_date);
    $builder->where('t.itm_txn_date <=', $to_date);
    $builder->where('t.cmp_id', $this->company_id);
    $builder->where('t.hobo_id', $this->bo_id);
    
    // Use split_part with an explicit ::int cast and use binding
    $builder->where("split_part(t.itm_id_unit_id::text, '_', 1)::int =", $item_id, false);

    if ($mc_id > 0) {
        $builder->where('c.mat_cent_id', $mc_id);
    }
    if ($unit_id > 0) {
        // Also cast the unit_id part for comparison
        $builder->where("split_part(t.itm_id_unit_id::text, '_', 2)::int =", $unit_id, false);
    }

    $builder->groupBy('c.mat_cent_id, unit_id');
    $results = $builder->get()->getResultArray();

    // Re-index results for easy lookup
    $txnMap = [];
    foreach ($results as $row) {
        $txnMap[$row['mat_cent_id']][$row['unit_id']] = $row;
    }

    // Step 4: Build final data, ensuring all unit + mc combinations are shown
    $final_data = [];
    foreach ($matCenters as $mc) {
        foreach ($units as $unit) {
            $totals = $txnMap[$mc['mat_cent_id']][$unit['unit_id']] ?? [
                'total_in_qty'     => '0.00',
                'total_in_amount'  => '0.0000',
                'total_out_qty'    => '0.00',
                'total_out_amount' => '0.0000',
            ];

            $final_data[] = [
                'mat_cent_name'    => $mc['mat_cent_name'],
                'unit_id'          => $unit['unit_id'],
                'unit_name'        => $unit['unit_name'],
                'total_in_qty'     => $totals['total_in_qty'],
                'total_in_amount'  => $totals['total_in_amount'],
                'total_out_qty'    => $totals['total_out_qty'],
                'total_out_amount' => $totals['total_out_amount'],
            ];
        }
    }

    return json_encode([
        'status' => 'success',
        'data'   => $final_data
    ]);
}
  
   
   public function load_items_ledger12_sep_2025(){
    /* =========================
       0) Inputs & pagination
       ========================= */
    $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
    $pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10;
    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP < 1) $pq_rPP = 10;

    $from_date = $_POST['from_date'] ?? '';
    $to_date   = $_POST['to_date']   ?? '';
    $from_date = validate_from_date($from_date);
    $to_date   = validate_to_date($to_date);
    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date   = date('Y-m-d', strtotime($to_date));

    $item_id   = !empty($_POST['item_id']) ? $_POST['item_id'] : 0;
    $unit_id   = !empty($_POST['unit_id']) ? $_POST['unit_id'] : 0;  // kept for UI parity
    $mc_id     = !empty($_POST['mc_id']) ? $_POST['mc_id'] : 0;
    $mc_grp_id = !empty($_POST['mc_grp_id']) ? $_POST['mc_grp_id'] : 0;

    // valuation method: AVG | FIFO | LIFO | AUTO (AUTO = per-item from itmoppyval.itm_val_method_id)
    $val_method_req = strtoupper(trim($_POST['val_id'] ?? 'AVG'));
    if (!in_array($val_method_req, ['AVG','FIFO','LIFO','AUTO'], true)) $val_method_req = 'AVG';

    // allow negatives? default yes
    $allowNegative = (int)($_POST['allowNegative'] ?? 1) === 1;

    // one-txn explain mode → prints TXT calculation
    $explainMode       = 1;
    $explain_vch_txnId = isset($_POST['explain_vch_txn_id']) ? (string)$_POST['explain_vch_txn_id'] : '';
    $explain_itemUnit  = $item_id.'_'.$unit_id;

    $this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
    $fy_start_date = date('Y', strtotime($from_date)) . '-04-01';

    /* =========================
       1) Voucher counter pre-count
       ========================= */
    $voucher_count = $this->db->table('vchtxnconso vc')
        ->select('vc.vch_txn_id')
        ->join('cmptxnmstn cm', 'cm.vch_txn_id = vc.vch_txn_id', 'inner')
        ->where('vc.cmp_id', $this->company_id)
        ->where('vc.hobo_id', $this->bo_id)
        ->where('vc.vch_date >=', $fy_start_date)
        ->where('vc.vch_date <', $from_date)
        ->where('cm.master_id', $item_id)
        ->where('cm.master_id_type', 'itm')
        ->distinct()
        ->countAllResults();

    /* =========================
       2) Openings: qty + value + default method
       ========================= */
    // Opening QTY
    $openings = $this->db->table('itmoppybal')
        ->select('itm_id_unit_id, itm_op_bal_qty')
        ->where('cmpfymastr_id', $this->fy_id)
        ->where('hobo_id', $this->bo_id)
        ->where('cmp_id', $this->company_id)
        ->get()->getResultArray();
    $openingQty = [];
    foreach ($openings as $op) {
        $openingQty[$op['itm_id_unit_id']] = (float)$op['itm_op_bal_qty'];
    }

    // Opening VALUE + default method
    $openVals = $this->db->table('itmoppyval')
        ->select('itm_id_unit_id, itm_op_val_amt, itm_val_method_id, mat_cent_id')
        ->where('cmpfymastr_id', $this->fy_id)
        ->where('hobo_id', $this->bo_id)
        ->where('cmp_id', $this->company_id)
        ->get()->getResultArray();

    $openingVal = []; // [itm_id_unit_id] => ['op_val'=>float, 'method'=>1/2/3]
    foreach ($openVals as $ov) {
        $openingVal[$ov['itm_id_unit_id']] = [
            'op_val' => (float)($ov['itm_op_val_amt'] ?? 0),
            'method' => isset($ov['itm_val_method_id']) ? $ov['itm_val_method_id'] : null, // 1=FIFO,2=LIFO,3=AVG
        ];
    }

    // method resolver
    $resolveMethod = function(string $itmIdUnitId) use ($val_method_req, $openingVal): string {
        if ($val_method_req !== 'AUTO') return $val_method_req;
        $m = $openingVal[$itmIdUnitId]['method'] ?? null;
        return match ($m) {
            1 => 'FIFO',
            2 => 'LIFO',
            3 => 'AVG',
            default => 'AVG',
        };
    };

    /* =========================
       3) Core query
       ========================= */
    $builder = $this->db->table('itemtxnmst t');
    $builder->select("
        t.itm_txn_date,
        c.vch_type_id AS voucher_type_id,
        vt.vch_name AS voucher_type,
        t.itm_id_unit_id,
        u.itm_unit_name AS unit_name,
        t.itm_txn_dr_cr,
        t.itm_txn_rate,
        t.itm_txn_amt,
        t.itm_txn_qty,
        c.vch_txn_id,
		t.itm_txn_id
    ");
    $builder->join('vchtxnconso c', 'c.vch_txn_id = t.vch_txn_id', 'left');
    $builder->join('vchtypemst vt', 'vt.vch_type_id = c.vch_type_id', 'left');
    $builder->join('itmunitmst u', 'u.itm_unit_id = SUBSTRING_INDEX(t.itm_id_unit_id, "_", -1)', 'left');

    $builder->where('SUBSTRING_INDEX(t.itm_id_unit_id, "_", 1)', $item_id);
	if($unit_id>0)
	$builder->where('SUBSTRING_INDEX(t.itm_id_unit_id, "_", -1)', $unit_id);
    $builder->where('t.itm_txn_date >=', $from_date);
    $builder->where('t.itm_txn_date <=', $to_date);
    $builder->where('t.cmp_id', $this->company_id);
    $builder->where('t.hobo_id', $this->bo_id);
	if($mc_id>0)
	$builder->where('t.mat_cent_id', $mc_id);	
    $builder->orderBy('t.itm_txn_date', 'ASC');
    $builder->orderBy('t.itm_txn_id', 'ASC');

    $countBuilder  = clone $builder;
    $total_records = (int) $countBuilder
    ->select('COUNT(DISTINCT c.vch_txn_id, t.itm_id_unit_id) as total')
    ->get()
    ->getRow()
    ->total;

    // paging
    if ($pq_curPage == 0) $pq_curPage = 1;
    $offset = ($pq_rPP * ($pq_curPage - 1));
    if ($offset > $total_records) {
        $pq_curPage = ($pq_rPP>0) ? max(1, (int)ceil($total_records / $pq_rPP)) : 1;
        $offset     = ($pq_rPP * ($pq_curPage - 1));
    }
    if ($offset < 0) $offset = 0;
    $builder->limit($pq_rPP, $offset);

    $rows = $builder->get()->getResultArray();

    /* =========================
       4) Group by (vch_txn_id + itm_id_unit_id)
       ========================= */
    $balances       = [];           // your book running balance (qty/amount)
    $grouped_data   = [];
    $voucher_count += $offset;

    foreach ($rows as $row) {
        $group_key = $row['vch_txn_id'] . '_' . $row['itm_id_unit_id'];
        if (!isset($grouped_data[$group_key])) {
            $grouped_data[$group_key] = [
                'itm_txn_date'    => $row['itm_txn_date'],
                'voucher_type_id' => $row['voucher_type_id'],
                'voucher_type'    => $row['voucher_type'],
                'vch_txn_id'      => $row['vch_txn_id'],
				'itm_txn_id'      => $row['itm_txn_id'],
                'itm_id_unit_id'  => $row['itm_id_unit_id'],
                'unit_name'       => $row['unit_name'],
                'inward_qty'      => 0.0,
                'inward_amount'   => 0.0,
                'inward_rate'     => '',
                'outward_qty'     => 0.0,
                'outward_amount'  => 0.0,
                'outward_rate'    => '',
            ];
        }
        if ((int)$row['itm_txn_dr_cr'] === 1) {
            $grouped_data[$group_key]['inward_qty']    += (float)$row['itm_txn_qty'];
            $grouped_data[$group_key]['inward_amount'] += (float)$row['itm_txn_amt'];
            $grouped_data[$group_key]['inward_rate']    = (float)$row['itm_txn_rate'];
        } elseif ((int)$row['itm_txn_dr_cr'] === 2) {
            $grouped_data[$group_key]['outward_qty']    += (float)$row['itm_txn_qty'];
            $grouped_data[$group_key]['outward_amount'] += (float)$row['itm_txn_amt'];
            $grouped_data[$group_key]['outward_rate']    = (float)$row['itm_txn_rate'];
        }
    }

    /* =========================
       5) Valuation state & helpers
       ========================= */
    // Balls (FIFO/LIFO)
    $val_balls = []; // [key] => [ ['qty'=>..,'cost'=>..], ... ]
    $pushBall = function(string $key, float $qty, float $cost) use (&$val_balls): void {
        if (!isset($val_balls[$key])) $val_balls[$key] = [];
        if (abs($qty) <= 0) return;
        $n = count($val_balls[$key]);
        if ($n>0 && abs($val_balls[$key][$n-1]['cost'] - $cost) < 1e-10) {
            $val_balls[$key][$n-1]['qty'] += $qty;
        } else {
            $val_balls[$key][] = ['qty'=>$qty, 'cost'=>$cost];
        }
        while (!empty($val_balls[$key]) && abs($val_balls[$key][0]['qty']) <= 1e-12) array_shift($val_balls[$key]);
        while (!empty($val_balls[$key]) && abs($val_balls[$key][count($val_balls[$key])-1]['qty']) <= 1e-12) array_pop($val_balls[$key]);
    };
    $ballsQty = function(array $balls): float { $q=0.0; foreach ($balls as $b) $q += $b['qty']; return $q; };
    $ballsVal = function(array $balls): float { $v=0.0; foreach ($balls as $b) $v += $b['qty']*$b['cost']; return $v; };
    $ballsAvg = function(array $balls): float { $q=0.0; $v=0.0; foreach ($balls as $b){$q+=$b['qty'];$v+=$b['qty']*$b['cost'];} return $q>0?$v/$q:0.0; };

    $consumeBalls = function(string $key, float $qty, string $method, bool $allowNeg) use (&$val_balls, $ballsAvg): array {
        if (!isset($val_balls[$key])) $val_balls[$key] = [];
        $value = 0.0; $need = $qty; $parts = [];

        if ($method === 'FIFO') {
            while ($need > 1e-12 && !empty($val_balls[$key])) {
                $take = min($need, $val_balls[$key][0]['qty']);
                if ($take > 0) {
                    $parts[] = ['qty'=>$take, 'cost'=>$val_balls[$key][0]['cost']];
                    $value  += $take * $val_balls[$key][0]['cost'];
                    $val_balls[$key][0]['qty'] -= $take;
                    $need -= $take;
                }
                if ($val_balls[$key][0]['qty'] <= 1e-12) array_shift($val_balls[$key]);
            }
        } else { // LIFO
            while ($need > 1e-12 && !empty($val_balls[$key])) {
                $i = count($val_balls[$key]) - 1;
                $take = min($need, $val_balls[$key][$i]['qty']);
                if ($take > 0) {
                    $parts[] = ['qty'=>$take, 'cost'=>$val_balls[$key][$i]['cost']];
                    $value  += $take * $val_balls[$key][$i]['cost'];
                    $val_balls[$key][$i]['qty'] -= $take;
                    $need -= $take;
                }
                if ($val_balls[$key][$i]['qty'] <= 1e-12) array_pop($val_balls[$key]);
            }
        }

        if ($need > 1e-12) {
            if (!$allowNeg) throw new \RuntimeException("Insufficient stock for $method issue");
            $fallbackRate = $ballsAvg($val_balls[$key]);
            $parts[] = ['qty'=>$need, 'cost'=>$fallbackRate, 'note'=>'shortfall'];
            $value += $need * $fallbackRate;
            $val_balls[$key][] = ['qty' => -$need, 'cost' => $fallbackRate]; // negative ball
            $need = 0.0;
        }
        return [$value, $parts];
    };

    // AVG state
    $avgState = []; // [key] => ['qty'=>float, 'avg'=>float]
    $avgInit = function(string $key, float $qty, float $avg) use (&$avgState): void {
        $avgState[$key] = ['qty'=>$qty, 'avg'=>$avg];
    };
    $avgOnHand = function(string $key) use (&$avgState): float { return (float)($avgState[$key]['qty'] ?? 0.0); };
    $avgRate   = function(string $key) use (&$avgState): float { return (float)($avgState[$key]['avg'] ?? 0.0); };
    $avgIn     = function(string $key, float $inQty, float $inRate) use (&$avgState): array {
        $q0 = (float)($avgState[$key]['qty'] ?? 0.0);
        $a0 = (float)($avgState[$key]['avg'] ?? 0.0);
        $val0 = $q0 * $a0;
        $val1 = $val0 + $inQty * $inRate;
        $q1   = $q0 + $inQty;
        $a1   = ($q1 > 0) ? ($val1 / $q1) : 0.0;
        $avgState[$key] = ['qty'=>$q1, 'avg'=>$a1];
        return ['q0'=>$q0,'a0'=>$a0,'val0'=>$val0,'inQty'=>$inQty,'inRate'=>$inRate,'q1'=>$q1,'a1'=>$a1,'val1'=>$val1];
    };
    $avgOut    = function(string $key, float $outQty, bool $allowNeg) use (&$avgState): array {
        $q0  = (float)($avgState[$key]['qty'] ?? 0.0);
        $a0  = (float)($avgState[$key]['avg'] ?? 0.0);
        $val = $outQty * $a0;
        $q1  = $q0 - $outQty;
        if ($q1 < -1e-12 && !$allowNeg) throw new \RuntimeException('Insufficient stock for AVG issue');
        $avgState[$key] = ['qty'=>$q1, 'avg'=>$a0];
        return ['q0'=>$q0,'a0'=>$a0,'outQty'=>$outQty,'issueVal'=>$val,'q1'=>$q1,'a1'=>$a0];
    };

    /* =========================
       6) Seed valuation from openings (qty+value)
       ========================= */
    $seen_keys = [];
	$explain_logs = []; 
    foreach ($grouped_data as $g) {
        $ukey = $g['itm_id_unit_id'];
        if (isset($seen_keys[$ukey])) continue;
        $seen_keys[$ukey] = true;

        $op_qty  = (float)($openingQty[$ukey] ?? 0.0);
        $op_val  = (float)($openingVal[$ukey]['op_val'] ?? 0.0);
        $op_rate = ($op_qty > 0 && $op_val > 0) ? ($op_val / $op_qty) : 0.0;

        // your book running starts with opening
        $balances[$ukey] = ['qty' => $op_qty, 'amount' => $op_val];

        // FIFO/LIFO balls
        if (!isset($val_balls[$ukey])) $val_balls[$ukey] = [];
        if ($op_qty != 0.0) $pushBall($ukey, $op_qty, $op_rate);

        // AVG state
        $avgInit($ukey, $op_qty, $op_rate);
    }

    /* =========================
       7A) Explain mode for ONE txn (returns TXT)
       ========================= */
	    $all_logs = [];  // Store all logs

		$key = $explain_itemUnit;
		$eff_method = $resolveMethod($key);

		$dumpBalls = function(array $balls): string {
			if (empty($balls)) return "[]";
			$parts = [];
			foreach ($balls as $b) {
				$parts[] = sprintf("(%s @ %s)",
					rtrim(rtrim(number_format($b['qty'], 6, '.', ''), '0'), '.'),
					rtrim(rtrim(number_format($b['cost'], 6, '.', ''), '0'), '.')
				);
			}
			return "[" . implode(", ", $parts) . "]";
		};

		foreach ($grouped_data as $g) {
			if ($g['itm_id_unit_id'] !== $key) continue;

			$itm_txn_id = $g['itm_txn_id'];
			$vch_txn_id = $g['vch_txn_id'];

			// Initialize log array for this txn
			$log = [];
			$log[] = "Valuation Explain";
			$log[] = "ItemUnit: {$key}";
			$log[] = "Method:  {$eff_method}";
			$log[] = "AllowNegative: " . ($allowNegative ? 'YES' : 'NO');

			if ($eff_method === 'AVG') {
				$log[] = "---- BEFORE TXN {$g['vch_txn_id']} ({$g['itm_txn_date']}) ----";
				$log[] = sprintf("OnHand=%s, AvgRate=%s",
					rtrim(rtrim(number_format($avgOnHand($key), 6, '.', ''), '0'), '.'),
					rtrim(rtrim(number_format($avgRate($key), 6, '.', ''), '0'), '.')
				);
			} else {
				$log[] = "---- BEFORE TXN {$g['vch_txn_id']} ({$g['itm_txn_date']}) ----";
				$log[] = "Balls: " . $dumpBalls($val_balls[$key] ?? []);
			}

			$isTarget = 1;//((string)$g['vch_txn_id'] === $explain_vch_txnId);

			// IN
			if ($g['inward_qty'] > 0) {
				$in_rate = ($g['inward_qty'] > 0) ? ($g['inward_amount'] / $g['inward_qty']) : 0.0;
				if ($eff_method === 'AVG') {
					$snap = $avgIn($key, (float)$g['inward_qty'], (float)$in_rate);
					if ($isTarget) {
						$log[] = sprintf("IN: qty=%s @ rate=%s",
							rtrim(rtrim(number_format($g['inward_qty'], 6, '.', ''), '0'), '.'),
							rtrim(rtrim(number_format($in_rate, 6, '.', ''), '0'), '.')
						);
						$log[] = sprintf("Avg: (q0=%s * a0=%s + qIn=%s * rIn=%s) / (q0+qIn) => a1=%s; q1=%s",
							rtrim(rtrim(number_format($snap['q0'], 6, '.', ''), '0'), '.'),
							rtrim(rtrim(number_format($snap['a0'], 6, '.', ''), '0'), '.'),
							rtrim(rtrim(number_format($snap['inQty'], 6, '.', ''), '0'), '.'),
							rtrim(rtrim(number_format($snap['inRate'], 6, '.', ''), '0'), '.'),
							rtrim(rtrim(number_format($snap['a1'], 6, '.', ''), '0'), '.'),
							rtrim(rtrim(number_format($snap['q1'], 6, '.', ''), '0'), '.')
						);
					}
				} else {
					$pushBall($key, (float)$g['inward_qty'], (float)$in_rate);
					if ($isTarget) {
						$log[] = sprintf("IN: push %s @ %s",
							rtrim(rtrim(number_format($g['inward_qty'], 6, '.', ''), '0'), '.'),
							rtrim(rtrim(number_format($in_rate, 6, '.', ''), '0'), '.')
						);
						$log[] = "Balls after IN: " . $dumpBalls($val_balls[$key] ?? []);
					}
				}
			}

			// OUT (+ profit line)
			if ($g['outward_qty'] > 0) {
				$issueValue = 0.0;
				if ($eff_method === 'AVG') {
					$snap = $avgOut($key, (float)$g['outward_qty'], $allowNegative);
					$issueValue = $snap['issueVal'];
					if ($isTarget) {
						$log[] = sprintf("OUT: qty=%s @ avg=%s => issue=%s",
							rtrim(rtrim(number_format($g['outward_qty'], 6, '.', ''), '0'), '.'),
							rtrim(rtrim(number_format($snap['a0'], 6, '.', ''), '0'), '.'),
							rtrim(rtrim(number_format($snap['issueVal'], 6, '.', ''), '0'), '.')
						);
						$log[] = sprintf("Post-OUT: q1=%s, avg=%s",
							rtrim(rtrim(number_format($snap['q1'], 6, '.', ''), '0'), '.'),
							rtrim(rtrim(number_format($snap['a1'], 6, '.', ''), '0'), '.')
						);
					}
				} else {
					[$issuedValue, $parts] = $consumeBalls($key, (float)$g['outward_qty'], $eff_method, $allowNegative);
					$issueValue = $issuedValue;
					if ($isTarget) {
						$log[] = sprintf("OUT: qty=%s using %s",
							rtrim(rtrim(number_format($g['outward_qty'], 6, '.', ''), '0'), '.'),
							$eff_method
						);
						foreach ($parts as $p) {
							$note = isset($p['note']) ? ' (shortfall)' : '';
							$log[] = sprintf("  take %s @ %s = %s%s",
								rtrim(rtrim(number_format($p['qty'], 6, '.', ''), '0'), '.'),
								rtrim(rtrim(number_format($p['cost'], 6, '.', ''), '0'), '.'),
								rtrim(rtrim(number_format($p['qty'] * $p['cost'], 6, '.', ''), '0'), '.'),
								$note
							);
						}
						$log[] = "Issue total: " . rtrim(rtrim(number_format($issuedValue, 6, '.', ''), '0'), '.');
						$log[] = "Balls after OUT: " . $dumpBalls($val_balls[$key] ?? []);
					}
				}

				if ($isTarget) {
					$profit = (float)$g['outward_amount'] - $issueValue;
					$log[]  = "Selling amount (book): " . rtrim(rtrim(number_format($g['outward_amount'], 6, '.', ''), '0'), '.');
					$log[]  = "COGS (valuation): " . rtrim(rtrim(number_format($issueValue, 6, '.', ''), '0'), '.');
					$log[]  = "PROFIT: " . rtrim(rtrim(number_format($profit, 6, '.', ''), '0'), '.');
				}
			}

			$final_log = implode("\n", $log);
			
			// Store log by itm_txn_id and vch_txn_id
			$all_logs[$itm_txn_id][$vch_txn_id] = $final_log;
		}
	   
    /* =========================
       7B) Normal ledger mode (with valuation & PROFIT columns)
       ========================= */
    $final_data      = [];
    $last_vch_txn_id = null;

    foreach ($grouped_data as $group) {
        $unit_key = $group['itm_id_unit_id'];

        // Ensure book balance exists
        if (!isset($balances[$unit_key])) {
            $op_qty = (float)($openingQty[$unit_key] ?? 0.0);
            $op_val = (float)($openingVal[$unit_key]['op_val'] ?? 0.0);
            $balances[$unit_key] = ['qty' => $op_qty, 'amount' => $op_val];
        }

        if ($group['vch_txn_id'] != $last_vch_txn_id) {
            $voucher_count++;
            $last_vch_txn_id = $group['vch_txn_id'];
        }

        // Update book running
        $balances[$unit_key]['qty']    += $group['inward_qty'] - $group['outward_qty'];
        $balances[$unit_key]['amount'] += $group['inward_amount'] - $group['outward_amount'];
        $balance_crdr = ($balances[$unit_key]['amount'] < 0) ? 'CR.' : 'DR.';

        // Valuation method for this item
        $eff_method = $resolveMethod($unit_key);

        // Inward valuation
        if ($group['inward_qty'] > 0) {
            $in_rate = ($group['inward_qty'] > 0) ? ($group['inward_amount'] / $group['inward_qty']) : 0.0;
            if ($eff_method === 'AVG') {
                $avgIn($unit_key, (float)$group['inward_qty'], (float)$in_rate);
            } else {
                $pushBall($unit_key, (float)$group['inward_qty'], (float)$in_rate);
            }
        }

        // Outward valuation + PROFIT
        $val_issue_value = 0.0;
        $val_issue_rate  = 0.0;
        $val_profit      = 0.0;
        $val_profit_rate = 0.0;

        if ($group['outward_qty'] > 0) {
            if ($eff_method === 'AVG') {
                $snap = $avgOut($unit_key, (float)$group['outward_qty'], $allowNegative);
                $val_issue_value = $snap['issueVal'];
                $val_issue_rate  = ($group['outward_qty'] > 0) ? ($val_issue_value / $group['outward_qty']) : 0.0;
            } else {
                [$issuedValue, $_parts] = $consumeBalls($unit_key, (float)$group['outward_qty'], $eff_method, $allowNegative);
                $val_issue_value = $issuedValue;
                $val_issue_rate  = ($group['outward_qty'] > 0) ? ($issuedValue / $group['outward_qty']) : 0.0;
            }

            // Profit = book selling amount - valuation COGS
            $val_profit      = (float)$group['outward_amount'] - $val_issue_value;
            $val_profit_rate = ($group['outward_qty'] > 0) ? ($val_profit / $group['outward_qty']) : 0.0;
        }

        // Valuation balances after this row
        if ($eff_method === 'AVG') {
            $val_bal_qty   = $avgOnHand($unit_key);
            $val_bal_rate  = $avgRate($unit_key);
            $val_bal_value = $val_bal_qty * $val_bal_rate;
        } else {
            $balls = $val_balls[$unit_key] ?? [];
            $val_bal_qty   = $ballsQty($balls);
            $val_bal_value = $ballsVal($balls);
            $val_bal_rate  = ($val_bal_qty > 0) ? ($val_bal_value / $val_bal_qty) : 0.0;
        }

        $final_data[] = [
            'voucher_date'        => $group['itm_txn_date'],
            'voucher_type'        => $group['voucher_type'],
            'voucher_type_id'     => $group['voucher_type_id'],
            'particulars'         => 'Item Txn: ' . $group['vch_txn_id'],
            'voucher'             => $group['voucher_type'] . '(No. ' . $voucher_count . ')',
            'inward_qty'          => $group['inward_qty'],
            'inward_rate'         => $group['inward_rate'],
            'inward_amount'       => $group['inward_amount'],
            'outward_qty'         => $group['outward_qty'],
            'outward_rate'        => $group['outward_rate'],
            'outward_amount'      => $group['outward_amount'],
            'closing_qty'         => $balances[$unit_key]['qty'] ?? 0,
            'unit_name'           => $group['unit_name'],
            'balance_amount'      => formatAmount(abs($balances[$unit_key]['amount'])) . ' ' . $balance_crdr,
            // valuation columns
            'effective_val_method'=> $eff_method,
            'val_issue_value'     => round($val_issue_value, 2),
            'val_issue_rate'      => round($val_issue_rate,  4),
            'valuation_calc'      => formatAmount($val_issue_value),
            'val_balance_rate'    => round($val_bal_rate,    4),
            // NEW: PROFIT columns for outward
            'profit'          	  => formatAmount($val_profit).'('.($group['outward_qty']>0)
                                        ? formatAmount($val_profit) . (($val_profit>=0)?' (Profit)':' (Loss)')
                                        : ''.')',
            'val_profit_rate'     => round($val_profit_rate, 4),
            'val_profit_string'   => ($group['outward_qty']>0)
                                        ? formatAmount($val_profit) . (($val_profit>=0)?' (Profit)':' (Loss)')
                                        : '',
            'voucher_txn_id'      => $group['vch_txn_id'],
			'txn_id'              => $group['itm_txn_id'],
            'itm_id_unit_id'      => $group['itm_id_unit_id'],
			'valuation_log'       => $all_logs
        ];
    }
    return "{\"totalRecords\":" .$total_records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($final_data)."}";
}

public function load_items_ledger($compId=null,$boId=null,$fyId=null,$reportData=null)
{
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
    /* =========================
       0) Inputs & pagination
       ========================= */
   
   
    if($reportData){
		 $pq_curPage = isset($reportData["pq_curpage"]) ? (int)$reportData["pq_curpage"] : 1;
    $pq_rPP     = isset($reportData["pq_rpp"]) ? (int)$reportData["pq_rpp"] : 10;
    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP < 1) $pq_rPP = 10;
	 $item_id   = !empty($reportData['item_id']) ? (int)$reportData['item_id'] : 0;
     $unit_id   = !empty($reportData['unit_id']) ? (int)$reportData['unit_id'] : 0;
     $mc_id     = !empty($reportData['mc_id']) ? (int)$reportData['mc_id'] : 0;
     $val_method_req = strtoupper(trim($reportData['val_id'] ?? 'AVG'));

	 $from_date = $reportData['from_date'];
     $to_date   = $reportData['to_date'];
    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date   = date('Y-m-d', strtotime($to_date));
	}else{
		 $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
    $pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10;
    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP < 1) $pq_rPP = 10;
    $item_id   = !empty($_POST['item_id']) ? (int)$_POST['item_id'] : 0;
    $unit_id   = !empty($_POST['unit_id']) ? (int)$_POST['unit_id'] : 0;
    $mc_id     = !empty($_POST['mc_id']) ? (int)$_POST['mc_id'] : 0;
    $val_method_req = strtoupper(trim($_POST['val_id'] ?? 'AVG'));
	
	$from_date = $_POST['from_date'] ?? '';
    $to_date   = $_POST['to_date']   ?? '';
    $from_date = validate_from_date($from_date);
    $to_date   = validate_to_date($to_date);
    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date   = date('Y-m-d', strtotime($to_date));
	}
    if (!in_array($val_method_req, ['AVG','FIFO','LIFO','AUTO'], true)) $val_method_req = 'AVG';
 
    /* =========================
       1) Get Opening Balance
       ========================= */
    $opening_qty = 0;
    $opening_value = 0;
    $opening_rate = 0;
    
    if ($item_id > 0 && $unit_id > 0) {
        $item_unit_key = $item_id . '_' . $unit_id;
        
        $op_bal_query = $this->db->table('itmoppybal')
            ->select('COALESCE(SUM(itm_op_bal_qty), 0) as total_qty', false)
            ->where('cmpfymastr_id', $sel_fyId)
            ->where('hobo_id', $sel_boId)
            ->where('cmp_id', $sel_compId)
            ->where('itm_id_unit_id', $item_unit_key)
            ->get()->getRow();
        
        if ($op_bal_query) {
            $opening_qty = (float)$op_bal_query->total_qty;
        }
        
        $op_val_query = $this->db->table('itmoppyval')
            ->select('COALESCE(SUM(itm_op_val_amt), 0) as total_val', false)
            ->where('cmpfymastr_id', $sel_fyId)
            ->where('hobo_id', $sel_boId)
            ->where('cmp_id', $sel_compId)
            ->where('itm_id_unit_id', $item_unit_key)
            ->where('itm_val_method_id', 'AVG')
            ->get()->getRow();
        
        if ($op_val_query) {
            $opening_value = (float)$op_val_query->total_val;
        }
        
        $opening_rate = ($opening_qty > 0) ? ($opening_value / $opening_qty) : 0;
    }

    /* =========================
       2) Get Transactions (PostgreSQL split_part with casts)
       ========================= */
    $builder = $this->db->table('itemtxnmst t', false);
    $builder->select("
        t.itm_txn_date,
        c.vch_type_id,
        vt.vch_name AS voucher_type,
        t.itm_id_unit_id,
        u.itm_unit_name AS unit_name,
        t.itm_txn_dr_cr,
        t.itm_txn_rate,
        t.itm_txn_amt,
        t.itm_txn_qty,
        c.vch_txn_id,
        t.itm_txn_id
    ", false);

    $builder->join('vchtxnconso c', 'c.vch_txn_id = t.vch_txn_id', 'left', false);
    $builder->join('vchtypemst vt', 'vt.vch_type_id = c.vch_type_id', 'left', false);
	$builder->where("split_part(t.itm_id_unit_id::text, '_', 2) <> ''", null, false);
    // Cast split_part(..., 2) to int so it matches itmunitmst.itm_unit_id (integer)
    $builder->join('itmunitmst u', "u.itm_unit_id = split_part(t.itm_id_unit_id::text, '_', 2)::int", 'left', false);

    // Filters with casts to int — inline the integers (no placeholders) and disable escaping
    $builder->where("split_part(t.itm_id_unit_id::text, '_', 1)::int = " . $item_id, null, false);
    if ($unit_id > 0) { 
        $builder->where("split_part(t.itm_id_unit_id::text, '_', 2)::int = " . $unit_id, null, false); 
    }
    if ($mc_id > 0) { 
        $builder->where('t.mat_cent_id', $mc_id); 
    }

    $builder->where('t.itm_txn_date >=', $from_date);
    $builder->where('t.itm_txn_date <=', $to_date);
    $builder->where('t.cmp_id', $sel_compId);
    $builder->where('t.hobo_id', $sel_boId);
    $builder->orderBy('t.itm_txn_date', 'ASC');
    $builder->orderBy('t.itm_txn_id', 'ASC');

    // Count total records
    $countBuilder = clone $builder;
    $total_records = $countBuilder->countAllResults(false);

    // Apply pagination
    $offset = ($pq_rPP * ($pq_curPage - 1));
    if ($offset > $total_records && $total_records > 0) {
        $pq_curPage = ceil($total_records / $pq_rPP);
        $offset = ($pq_rPP * ($pq_curPage - 1));
    }
    if ($offset < 0) $offset = 0;

    $builder->limit($pq_rPP, $offset);
    $rows = $builder->get()->getResultArray();

    /* =========================
       3) Group transactions by voucher
       ========================= */
    $grouped_data = [];
    $voucher_count = 0;
    
    foreach ($rows as $row) {
        $group_key = $row['vch_txn_id'] . '_' . $row['itm_id_unit_id'];
        
        if (!isset($grouped_data[$group_key])) {
            $grouped_data[$group_key] = [
                'itm_txn_date'    => $row['itm_txn_date'],
                'voucher_type'    => $row['voucher_type'],
                'voucher_type_id' => $row['vch_type_id'],
                'vch_txn_id'      => $row['vch_txn_id'],
                'itm_id_unit_id'  => $row['itm_id_unit_id'],
                'unit_name'       => $row['unit_name'],
                'inward_qty'      => 0,
                'inward_amount'   => 0,
                'inward_rate'     => 0,
                'outward_qty'     => 0,
                'outward_amount'  => 0,
                'outward_rate'    => 0,
            ];
        }
        
        if ((int)$row['itm_txn_dr_cr'] === 1) {
            $grouped_data[$group_key]['inward_qty']    += (float)$row['itm_txn_qty'];
            $grouped_data[$group_key]['inward_amount'] += (float)$row['itm_txn_amt'];
            $grouped_data[$group_key]['inward_rate']    = (float)$row['itm_txn_rate'];
        } else {
            $grouped_data[$group_key]['outward_qty']    += (float)$row['itm_txn_qty'];
            $grouped_data[$group_key]['outward_amount'] += (float)$row['itm_txn_amt'];
            $grouped_data[$group_key]['outward_rate']    = (float)$row['itm_txn_rate'];
        }
    }

    /* =========================
       4) Process with AVG valuation
       ========================= */
    $final_data = [];
    $running_qty = $opening_qty;
    $running_value = $opening_value;
    $running_avg = $opening_rate;
    $last_voucher_id = null;
    
    foreach ($grouped_data as $group) {
        if ($group['vch_txn_id'] != $last_voucher_id) {
            $voucher_count++;
            $last_voucher_id = $group['vch_txn_id'];
        }
        
        $cogs_value = 0;
        $profit_value = 0;
        
        $is_stock_journal = ($group['voucher_type_id'] == '20' || strtolower($group['voucher_type']) == 'stock journal');
        
        if ($is_stock_journal && $group['outward_qty'] > 0 && $group['inward_qty'] > 0) {
            $cogs_value = $group['outward_qty'] * $running_avg;
            if ($group['outward_qty'] == $group['inward_qty']) {
                $profit_value = $cogs_value - $group['inward_amount'];
            }
            $running_qty    = $running_qty - $group['outward_qty'] + $group['inward_qty'];
            $running_value  = $running_value - $cogs_value + $group['inward_amount'];
            if ($running_qty > 0) {
                $running_avg = $running_value / $running_qty;
            }
        } else {
            if ($group['outward_qty'] > 0) {
                $cogs_value = $group['outward_qty'] * $running_avg;
                $running_qty   -= $group['outward_qty'];
                $running_value -= $cogs_value;
                $profit_value   = $group['outward_amount'] - $cogs_value;
            }
            if ($group['inward_qty'] > 0) {
                $running_qty   += $group['inward_qty'];
                $running_value += $group['inward_amount'];
                if ($running_qty > 0) {
                    $running_avg = $running_value / $running_qty;
                }
            }
        }
        
        $balance_crdr = ($running_value < 0) ? 'CR.' : 'DR.';
        
        $profit_string = '';
        if (abs($profit_value) > 0.01) {
            if ($is_stock_journal) {
                $profit_string = formatAmount(abs($profit_value)) . ' ' . (($profit_value >= 0) ? '(Gain)' : '(Loss)');
            } else {
                $profit_string = formatAmount(abs($profit_value)) . ' ' . (($profit_value >= 0) ? '(Profit)' : '(Loss)');
            }
        }
        
        $final_data[] = [
            'voucher_date'    => $group['itm_txn_date'],
            'voucher_type'    => $group['voucher_type'],
            'voucher_type_id' => $group['voucher_type_id'],
            'voucher'         => $group['voucher_type'] . ' (No. ' . $voucher_count . ')',
            'inward_qty'      => $group['inward_qty'],
            'inward_rate'     => $group['inward_rate'],
            'inward_amount'   => $group['inward_amount'],
            'outward_qty'     => $group['outward_qty'],
            'outward_rate'    => $group['outward_rate'],
            'outward_amount'  => $group['outward_amount'],
            'balance_qty'     => $running_qty,
            'unit_name'       => $group['unit_name'],
            'balance_amount'  => formatAmount(abs($running_value)) . ' ' . $balance_crdr,
            'profit'          => formatAmount($profit_value),
            'profit_string'   => $profit_string,
            'valuation_calc'  => formatAmount($cogs_value),
            'voucher_txn_id'  => $group['vch_txn_id'],
            'item_txn_drcr'   => ''
        ];
    }
    
    return "{\"totalRecords\":" . $total_records . ",\"curPage\":" . $pq_curPage . ",\"data\":" . json_encode($final_data) . "}";
}
public function load_items_ledger_24_nov_2025()
{
    /* =========================
       0) Inputs & pagination
       ========================= */
    $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
    $pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10;
    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP < 1) $pq_rPP = 10;

    $from_date = $_POST['from_date'] ?? '';
    $to_date   = $_POST['to_date']   ?? '';
    $from_date = validate_from_date($from_date);
    $to_date   = validate_to_date($to_date);
    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date   = date('Y-m-d', strtotime($to_date));

    $item_id   = !empty($_POST['item_id']) ? $_POST['item_id'] : 0;
    $unit_id   = !empty($_POST['unit_id']) ? $_POST['unit_id'] : 0;  // UI convenience
    $mc_id     = !empty($_POST['mc_id']) ? $_POST['mc_id'] : 0;
    $mc_grp_id = !empty($_POST['mc_grp_id']) ? $_POST['mc_grp_id'] : 0;

    // AVG | FIFO | LIFO | AUTO (AUTO = from itmoppyval.itm_val_method_id)
    $val_method_req = strtoupper(trim($_POST['val_id'] ?? 'AVG'));
    if (!in_array($val_method_req, ['AVG','FIFO','LIFO','AUTO'], true)) $val_method_req = 'AVG';

    // allow negatives? default yes
    $allowNegative = (int)($_POST['allowNegative'] ?? 1) === 1;

    // EXPLAIN is 100% on-demand: only when button posts these fields
    $explainMode       = (int)($_POST['explain'] ?? 0) === 1;
    $explain_vch_txnId = isset($_POST['explain_vch_txn_id'])     ? (string)$_POST['explain_vch_txn_id'] : '';
    $explain_itemUnit  = isset($_POST['explain_itm_id_unit_id']) ? (string)$_POST['explain_itm_id_unit_id'] : '';

    $this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
    $fy_start_date = date('Y', strtotime($from_date)) . '-04-01';

    /* =========================
       1) Voucher counter pre-count (for display)
       ========================= */
    $voucher_count = $this->db->table('vchtxnconso vc')
        ->select('vc.vch_txn_id')
        ->join('cmptxnmstn cm', 'cm.vch_txn_id = vc.vch_txn_id', 'inner')
        ->where('vc.cmp_id', $this->company_id)
        ->where('vc.hobo_id', $this->bo_id)
        ->where('vc.vch_date >=', $fy_start_date)
        ->where('vc.vch_date <', $from_date)
        ->where('cm.master_id', $item_id)
        ->where('cm.master_id_type', 'itm')
        ->distinct()
        ->countAllResults();

    /* =========================
       2) Openings: qty + value + default method (for valuation seeding)
       ========================= */
    $openingQty = [];
    $openings = $this->db->table('itmoppybal')
        ->select('itm_id_unit_id, itm_op_bal_qty')
        ->where('cmpfymastr_id', $this->fy_id)
        ->where('hobo_id', $this->bo_id)
        ->where('cmp_id', $this->company_id)
        ->get()->getResultArray();
    foreach ($openings as $op) {
        $openingQty[$op['itm_id_unit_id']] = (float)$op['itm_op_bal_qty'];
    }

    $openingVal = []; // [itm_id_unit_id] => ['op_val'=>float, 'method'=>1/2/3]
    $openVals = $this->db->table('itmoppyval')
        ->select('itm_id_unit_id, itm_op_val_amt, itm_val_method_id, mat_cent_id')
        ->where('cmpfymastr_id', $this->fy_id)
        ->where('hobo_id', $this->bo_id)
        ->where('cmp_id', $this->company_id)
        ->get()->getResultArray();
    foreach ($openVals as $ov) {
        $openingVal[$ov['itm_id_unit_id']] = [
            'op_val' => (float)($ov['itm_op_val_amt'] ?? 0),
            'method' => isset($ov['itm_val_method_id']) ? $ov['itm_val_method_id'] : null, // 1=FIFO,2=LIFO,3=AVG
        ];
    }

    $resolveMethod = function(string $itmIdUnitId) use ($val_method_req, $openingVal): string {
        if ($val_method_req !== 'AUTO') return $val_method_req;
        $m = $openingVal[$itmIdUnitId]['method'] ?? null;
        return match ($m) { 1 => 'FIFO', 2 => 'LIFO', 3 => 'AVG', default => 'AVG' };
    };

    /* =========================
       3) Core query (same as yours)
       ========================= */
    $builder = $this->db->table('itemtxnmst t');
    $builder->select("
        t.itm_txn_date,
        c.vch_type_id AS voucher_type_id,
        vt.vch_name AS voucher_type,
        t.itm_id_unit_id,
        u.itm_unit_name AS unit_name,
        t.itm_txn_dr_cr,
        t.itm_txn_rate,
        t.itm_txn_amt,
        t.itm_txn_qty,
        c.vch_txn_id,
        t.itm_txn_id
    ");
    $builder->join('vchtxnconso c', 'c.vch_txn_id = t.vch_txn_id', 'left');
    $builder->join('vchtypemst vt', 'vt.vch_type_id = c.vch_type_id', 'left');
    $builder->join('itmunitmst u', 'u.itm_unit_id = SUBSTRING_INDEX(t.itm_id_unit_id, "_", -1)', 'left');

    $builder->where('SUBSTRING_INDEX(t.itm_id_unit_id, "_", 1)', $item_id);
    if ($unit_id>0) { $builder->where('SUBSTRING_INDEX(t.itm_id_unit_id, "_", -1)', $unit_id); }
    if ($mc_id>0)   { $builder->where('t.mat_cent_id', $mc_id); }

    $builder->where('t.itm_txn_date >=', $from_date);
    $builder->where('t.itm_txn_date <=', $to_date);
    $builder->where('t.cmp_id', $this->company_id);
    $builder->where('t.hobo_id', $this->bo_id);

    $builder->orderBy('t.itm_txn_date', 'ASC');
    $builder->orderBy('t.itm_txn_id',   'ASC');

    // total distinct voucher+item rows
    $countBuilder  = clone $builder;
    $total_records = (int)$countBuilder
        ->select('COUNT(DISTINCT c.vch_txn_id, t.itm_id_unit_id) as total')
        ->get()->getRow()->total;

    // paging
    if ($pq_curPage == 0) $pq_curPage = 1;
    $offset = ($pq_rPP * ($pq_curPage - 1));
    if ($offset > $total_records) {
        $pq_curPage = ($pq_rPP>0) ? max(1, (int)ceil($total_records / $pq_rPP)) : 1;
        $offset     = ($pq_rPP * ($pq_curPage - 1));
    }
    if ($offset < 0) $offset = 0;
    $builder->limit($pq_rPP, $offset);

    $rows = $builder->get()->getResultArray();

    /* =========================
       4) Group by (vch_txn_id + itm_id_unit_id)
       ========================= */
    $grouped_data   = [];
    $voucher_count += $offset;

    foreach ($rows as $row) {
        $group_key = $row['vch_txn_id'] . '_' . $row['itm_id_unit_id'];
        if (!isset($grouped_data[$group_key])) {
            $grouped_data[$group_key] = [
                'itm_txn_date'    => $row['itm_txn_date'],
                'voucher_type_id' => $row['voucher_type_id'],
                'voucher_type'    => $row['voucher_type'],
                'vch_txn_id'      => $row['vch_txn_id'],
                'itm_txn_id'      => $row['itm_txn_id'],
                'itm_id_unit_id'  => $row['itm_id_unit_id'],
                'unit_name'       => $row['unit_name'],
                'inward_qty'      => 0.0,
                'inward_amount'   => 0.0,
                'inward_rate'     => '',
                'outward_qty'     => 0.0,
                'outward_amount'  => 0.0,
                'outward_rate'    => '',
            ];
        }
        if ((int)$row['itm_txn_dr_cr'] === 1) {
            $grouped_data[$group_key]['inward_qty']    += (float)$row['itm_txn_qty'];
            $grouped_data[$group_key]['inward_amount'] += (float)$row['itm_txn_amt'];
            $grouped_data[$group_key]['inward_rate']    = (float)$row['itm_txn_rate'];
        } elseif ((int)$row['itm_txn_dr_cr'] === 2) {
            $grouped_data[$group_key]['outward_qty']    += (float)$row['itm_txn_qty'];
            $grouped_data[$group_key]['outward_amount'] += (float)$row['itm_txn_amt'];
            $grouped_data[$group_key]['outward_rate']    = (float)$row['itm_txn_rate'];
        }
    }

    /* =========================
       5) Valuation state & helpers (NO book balances)
       ========================= */
    // FIFO/LIFO balls
    $val_balls = []; // [itm_id_unit_id] => [ ['qty'=>..,'cost'=>..], ... ]
    $pushBall = function(string $key, float $qty, float $cost) use (&$val_balls): void {
        if (!isset($val_balls[$key])) $val_balls[$key] = [];
        if ($qty == 0.0) return;
        $n = count($val_balls[$key]);
        if ($n>0 && abs($val_balls[$key][$n-1]['cost'] - $cost) < 1e-10) {
            $val_balls[$key][$n-1]['qty'] += $qty;
        } else {
            $val_balls[$key][] = ['qty'=>$qty, 'cost'=>$cost];
        }
        while (!empty($val_balls[$key]) && abs($val_balls[$key][0]['qty']) <= 1e-12) array_shift($val_balls[$key]);
        while (!empty($val_balls[$key]) && abs($val_balls[$key][count($val_balls[$key])-1]['qty']) <= 1e-12) array_pop($val_balls[$key]);
    };
    $ballsQty = function(array $balls): float { $q=0.0; foreach ($balls as $b) $q += $b['qty']; return $q; };
    $ballsVal = function(array $balls): float { $v=0.0; foreach ($balls as $b) $v += $b['qty']*$b['cost']; return $v; };
    $ballsAvg = function(array $balls): float { $q=0.0; $v=0.0; foreach ($balls as $b){$q+=$b['qty'];$v+=$b['qty']*$b['cost'];} return $q>0?$v/$q:0.0; };

    $consumeBalls = function(string $key, float $qty, string $method, bool $allowNeg) use (&$val_balls, $ballsAvg): array {
        if (!isset($val_balls[$key])) $val_balls[$key] = [];
        $value = 0.0; $need = $qty; $parts = [];
        if ($method === 'FIFO') {
            while ($need > 1e-12 && !empty($val_balls[$key])) {
                $take = min($need, $val_balls[$key][0]['qty']);
                if ($take > 0) {
                    $parts[] = ['qty'=>$take, 'cost'=>$val_balls[$key][0]['cost']];
                    $value  += $take * $val_balls[$key][0]['cost'];
                    $val_balls[$key][0]['qty'] -= $take;
                    $need -= $take;
                }
                if ($val_balls[$key][0]['qty'] <= 1e-12) array_shift($val_balls[$key]);
            }
        } else { // LIFO
            while ($need > 1e-12 && !empty($val_balls[$key])) {
                $i = count($val_balls[$key]) - 1;
                $take = min($need, $val_balls[$key][$i]['qty']);
                if ($take > 0) {
                    $parts[] = ['qty'=>$take, 'cost'=>$val_balls[$key][$i]['cost']];
                    $value  += $take * $val_balls[$key][$i]['cost'];
                    $val_balls[$key][$i]['qty'] -= $take;
                    $need -= $take;
                }
                if ($val_balls[$key][$i]['qty'] <= 1e-12) array_pop($val_balls[$key]);
            }
        }
        if ($need > 1e-12) {
            if (!$allowNeg) throw new \RuntimeException("Insufficient stock for $method issue");
            $fallbackRate = $ballsAvg($val_balls[$key]); // 0 if empty
            $parts[] = ['qty'=>$need, 'cost'=>$fallbackRate, 'note'=>'shortfall'];
            $value  += $need * $fallbackRate;
            $val_balls[$key][] = ['qty' => -$need, 'cost' => $fallbackRate]; // negative ball
        }
        return [$value, $parts];
    };

    // AVG state
    $avgState = []; // [itm_id_unit_id] => ['qty'=>float, 'avg'=>float]
    $avgInit = function(string $key, float $qty, float $avg) use (&$avgState): void { $avgState[$key] = ['qty'=>$qty, 'avg'=>$avg]; };
    $avgOnHand = function(string $key) use (&$avgState): float { return (float)($avgState[$key]['qty'] ?? 0.0); };
    $avgRate   = function(string $key) use (&$avgState): float { return (float)($avgState[$key]['avg'] ?? 0.0); };
    $avgIn     = function(string $key, float $inQty, float $inRate) use (&$avgState): void {
        $q0=(float)($avgState[$key]['qty'] ?? 0.0); $a0=(float)($avgState[$key]['avg'] ?? 0.0);
        $val1 = $q0*$a0 + $inQty*$inRate; $q1=$q0+$inQty; $a1 = ($q1>0) ? ($val1/$q1) : 0.0;
        $avgState[$key] = ['qty'=>$q1, 'avg'=>$a1];
    };
    $avgOut    = function(string $key, float $outQty, bool $allowNeg) use (&$avgState): float {
        $q0=(float)($avgState[$key]['qty'] ?? 0.0); $a0=(float)($avgState[$key]['avg'] ?? 0.0);
        $issue = $outQty * $a0; $q1 = $q0 - $outQty;
        if ($q1 < -1e-12 && !$allowNeg) throw new \RuntimeException('Insufficient stock for AVG issue');
        $avgState[$key] = ['qty'=>$q1, 'avg'=>$a0];
        return $issue;
    };

    /* =========================
       6) Seed valuation from openings (qty+value)
       ========================= */
    $seeded = [];
    foreach ($grouped_data as $g) {
        $ukey = $g['itm_id_unit_id'];
		
        if (isset($seeded[$ukey])) continue;
        $seeded[$ukey] = true;

        $op_qty  = (float)($openingQty[$ukey] ?? 0.0);
        $op_val  = (float)($openingVal[$ukey]['op_val'] ?? 0.0);
        $op_rate = ($op_qty > 0 && $op_val > 0) ? ($op_val / $op_qty) : 0.0;

        if (!isset($val_balls[$ukey])) $val_balls[$ukey] = [];
        if ($op_qty != 0.0) $pushBall($ukey, $op_qty, $op_rate);

        $avgInit($ukey, $op_qty, $op_rate);
    }

    /* =========================
       7A) Explain (on demand, one txn only; uses copies)
       ========================= */
	    
    //if ($explainMode && $explain_vch_txnId !== '' && $explain_itemUnit !== '') {
        $val_balls_x = $val_balls;
        $avgState_x  = $avgState;

        $key        = $explain_itemUnit;
        $eff_method = $resolveMethod($key);

        $dumpBalls = function(array $balls): string {
            if (empty($balls)) return "[]";
            $parts = [];
            foreach ($balls as $b) {
                $parts[] = sprintf("(%s @ %s)",
                    rtrim(rtrim(number_format($b['qty'], 6, '.', ''), '0'), '.'),
                    rtrim(rtrim(number_format($b['cost'], 6, '.', ''), '0'), '.'));
            }
            return "[" . implode(", ", $parts) . "]";
        };
        $avgOnHandX = function(string $k) use (&$avgState_x): float { return (float)($avgState_x[$k]['qty'] ?? 0.0); };
        $avgRateX   = function(string $k) use (&$avgState_x): float { return (float)($avgState_x[$k]['avg'] ?? 0.0); };
        $avgInX     = function(string $k, float $q, float $r) use (&$avgState_x): void {
            $q0=(float)($avgState_x[$k]['qty']??0); $a0=(float)($avgState_x[$k]['avg']??0);
            $val = $q0*$a0 + $q*$r; $q1=$q0+$q; $a1 = $q1>0 ? $val/$q1 : 0; $avgState_x[$k]=['qty'=>$q1,'avg'=>$a1];
        };
        $avgOutX    = function(string $k, float $q, bool $allowNeg) use (&$avgState_x): float {
            $q0=(float)($avgState_x[$k]['qty']??0); $a0=(float)($avgState_x[$k]['avg']??0);
            $issue=$q*$a0; $q1=$q0-$q; if ($q1<-1e-12 && !$allowNeg) throw new \RuntimeException('Insufficient AVG');
            $avgState_x[$k]=['qty'=>$q1,'avg'=>$a0]; return $issue;
        };
        $consumeBallsX = function(string $k, float $need, string $method, bool $allowNeg) use (&$val_balls_x, $ballsAvg): array {
            if (!isset($val_balls_x[$k])) $val_balls_x[$k]=[];
            $value=0.0; $parts=[];
            if ($method==='FIFO') {
                while ($need>1e-12 && !empty($val_balls_x[$k])) {
                    $take=min($need, $val_balls_x[$k][0]['qty']);
                    if ($take>0){ $parts[]=['qty'=>$take,'cost'=>$val_balls_x[$k][0]['cost']]; $value+=$take*$val_balls_x[$k][0]['cost']; $val_balls_x[$k][0]['qty']-=$take; $need-=$take; }
                    if ($val_balls_x[$k][0]['qty']<=1e-12) array_shift($val_balls_x[$k]);
                }
            } else {
                while ($need>1e-12 && !empty($val_balls_x[$k])) {
                    $i=count($val_balls_x[$k])-1; $take=min($need, $val_balls_x[$k][$i]['qty']);
                    if ($take>0){ $parts[]=['qty'=>$take,'cost'=>$val_balls_x[$k][$i]['cost']]; $value+=$take*$val_balls_x[$k][$i]['cost']; $val_balls_x[$k][$i]['qty']-=$take; $need-=$take; }
                    if ($val_balls_x[$k][$i]['qty']<=1e-12) array_pop($val_balls_x[$k]);
                }
            }
            if ($need>1e-12) {
                if (!$allowNeg) throw new \RuntimeException("Insufficient $method");
                $q=0;$v=0; foreach($val_balls_x[$k] as $b){$q+=$b['qty'];$v+=$b['qty']*$b['cost'];}
                $rate = $q>0 ? $v/$q : 0.0;
                $parts[]=['qty'=>$need,'cost'=>$rate,'note'=>'shortfall'];
                $value += $need*$rate;
                $val_balls_x[$k][]=['qty'=>-$need,'cost'=>$rate];
            }
            return [$value,$parts];
        };

        $log = [];	
       	$all_logs = [];	
        foreach ($grouped_data as $g) {
           // if ($g['itm_id_unit_id'] !== $key) continue;
			
			$itm_txn_id = $g['itm_txn_id'];
            $vch_txn_id = $g['vch_txn_id'];
            $printThis = true;//((string)$g['vch_txn_id'] === $explain_vch_txnId);
            if ($printThis) {
                $log[] = "Valuation Explain";
                $log[] = "ItemUnit: {$key}";
                $log[] = "Voucher: {$g['vch_txn_id']}";
                $log[] = "Method:  {$eff_method}";
                $log[] = "AllowNegative: " . ($allowNegative ? 'YES' : 'NO');
                if ($eff_method==='AVG') {
                    $log[] = "---- BEFORE TXN {$g['vch_txn_id']} ({$g['itm_txn_date']}) ----";
                    $log[] = "OnHand=".rtrim(rtrim(number_format($avgOnHandX($key),6,'.',''),'0'),'.')
                           .", AvgRate=".rtrim(rtrim(number_format($avgRateX($key),6,'.',''),'0'),'.');
                } else {
                    $log[] = "---- BEFORE TXN {$g['vch_txn_id']} ({$g['itm_txn_date']}) ----";
                    $log[] = "Balls: ".$dumpBalls($val_balls_x[$key] ?? []);
                }
            }

            if ($g['inward_qty'] > 0) {
                $rate = ($g['inward_qty']>0) ? ($g['inward_amount']/$g['inward_qty']) : 0.0;
                if ($eff_method==='AVG') $avgInX($key,(float)$g['inward_qty'],(float)$rate);
                else {
                    if (!isset($val_balls_x[$key])) $val_balls_x[$key]=[];
                    $nb=&$val_balls_x[$key]; $m=count($nb);
                    if ($m>0 && abs($nb[$m-1]['cost']-$rate)<1e-10) $nb[$m-1]['qty'] += (float)$g['inward_qty'];
                    else $nb[]=['qty'=>(float)$g['inward_qty'], 'cost'=>$rate];
                }
                if ($printThis) {
                    if ($eff_method==='AVG') {
                        $log[] = "IN: qty=".$g['inward_qty']." @ rate=".rtrim(rtrim(number_format($rate,6,'.',''),'0'),'.');
                        $log[] = "After IN: OnHand=".rtrim(rtrim(number_format($avgOnHandX($key),6,'.',''),'0'),'.')
                               .", Avg=".rtrim(rtrim(number_format($avgRateX($key),6,'.',''),'0'),'.');
                    } else {
                        $log[] = "IN: push ".$g['inward_qty']." @ ".rtrim(rtrim(number_format($rate,6,'.',''),'0'),'.');
                        $log[] = "Balls after IN: ".$dumpBalls($val_balls_x[$key] ?? []);
                    }
                }
            }

            if ($g['outward_qty'] > 0) {
                $issue = 0.0; $parts=[];
                if ($eff_method==='AVG') {
                    $issue = $avgOutX($key,(float)$g['outward_qty'],$allowNegative);
                    if ($printThis) {
                        $log[] = "OUT: qty=".$g['outward_qty']." @ avg=".rtrim(rtrim(number_format($avgRateX($key),6,'.',''),'0'),'.')
                               ." => issue=".rtrim(rtrim(number_format($issue,6,'.',''),'0'),'.');
                    }
                } else {
                    [$issue,$parts] = $consumeBallsX($key,(float)$g['outward_qty'],$eff_method,$allowNegative);
                    if ($printThis) {
                        $log[] = "OUT: qty=".$g['outward_qty']." using ".$eff_method;
                        foreach ($parts as $p) {
                            $note = isset($p['note']) ? ' (shortfall)' : '';
                            $log[] = "  take ".rtrim(rtrim(number_format($p['qty'],6,'.',''),'0'),'.')
                                   ." @ ".rtrim(rtrim(number_format($p['cost'],6,'.',''),'0'),'.')
                                   ." = ".rtrim(rtrim(number_format($p['qty']*$p['cost'],6,'.',''),'0'),'.').$note;
                        }
                        $log[] = "Issue total: ".rtrim(rtrim(number_format($issue,6,'.',''),'0'),'.');
                        $log[] = "Balls after OUT: ".$dumpBalls($val_balls_x[$key] ?? []);
                    }
                }
                if ($printThis) {
                    $profit = (float)$g['outward_amount'] - $issue;
                    $log[]  = "Selling amount (book): ".rtrim(rtrim(number_format($g['outward_amount'],6,'.',''),'0'),'.');
                    $log[]  = "COGS (valuation): ".rtrim(rtrim(number_format($issue,6,'.',''),'0'),'.');
                    $log[]  = "PROFIT: ".rtrim(rtrim(number_format($profit,6,'.',''),'0'),'.');
                    //break; // CHANGED: stop once target is explained
                }
				
            }
		$all_logs[$g['itm_txn_id']][$g['vch_txn_id']] = implode("\n", $log);	
        }

		
        /* return $this->response->setJSON([
            'ok'   => true,
            'mode' => 'explain',
            'text' => implode("\n", $log),
        ]); */
   // }


    /* =========================
       7B) Ledger mode — valuation only (COGS + valuation balances + profit)
       ========================= */
    $final_data      = [];
    $last_vch_txn_id = null;

    foreach ($grouped_data as $group) {
        $unit_key   = $group['itm_id_unit_id'];
        $eff_method = $resolveMethod($unit_key);

        if ($group['vch_txn_id'] != $last_vch_txn_id) {
            $voucher_count++;
            $last_vch_txn_id = $group['vch_txn_id'];
        }

        // Apply IN to valuation state
        if ($group['inward_qty'] > 0) {
            $in_rate = ($group['inward_qty'] > 0) ? ($group['inward_amount'] / $group['inward_qty']) : 0.0;
            if ($eff_method === 'AVG') { $avgIn($unit_key, (float)$group['inward_qty'], (float)$in_rate); }
            else                       { $pushBall($unit_key, (float)$group['inward_qty'], (float)$in_rate); }
        }

        // Apply OUT → COGS + profit
        $val_issue_value = 0.0;
        $val_issue_rate  = 0.0;
        $val_profit      = 0.0;
        $val_profit_rate = 0.0;

        if ($group['outward_qty'] > 0) {
            if ($eff_method === 'AVG') {
                $issue = $avgOut($unit_key, (float)$group['outward_qty'], $allowNegative);
                $val_issue_value = $issue;
                $val_issue_rate  = ($group['outward_qty'] > 0) ? ($issue / $group['outward_qty']) : 0.0;
            } else {
                [$issuedValue, $_parts] = $consumeBalls($unit_key, (float)$group['outward_qty'], $eff_method, $allowNegative);
                $val_issue_value = $issuedValue;
                $val_issue_rate  = ($group['outward_qty'] > 0) ? ($issuedValue / $group['outward_qty']) : 0.0;
            }
            $val_profit      = (float)$group['outward_amount'] - $val_issue_value;
            $val_profit_rate = ($group['outward_qty'] > 0) ? ($val_profit / $group['outward_qty']) : 0.0;
        }

        // Valuation balances AFTER this voucher
        if ($eff_method === 'AVG') {
            $val_bal_qty   = $avgOnHand($unit_key);
            $val_bal_value = $val_bal_qty * $avgRate($unit_key);
        } else {
            $balls         = $val_balls[$unit_key] ?? [];
            $val_bal_qty   = $ballsQty($balls);
            $val_bal_value = $ballsVal($balls);
        }
        $val_bal_rate    = ($val_bal_qty > 0) ? ($val_bal_value / $val_bal_qty) : 0.0;
        $val_balance_crdr= ($val_bal_value < 0) ? 'CR.' : 'DR.';

        // user-facing row
        $final_data[] = [
            'voucher_date'        => $group['itm_txn_date'],
            'voucher_type'        => $group['voucher_type'],
            'voucher_type_id'     => $group['voucher_type_id'],
            'particulars'         => 'Item Txn: ' . $group['vch_txn_id'],
            'voucher'             => $group['voucher_type'] . '(No. ' . $voucher_count . ')',

            'inward_qty'          => $group['inward_qty'],
            'inward_rate'         => $group['inward_rate'],
            'inward_amount'       => $group['inward_amount'],
            'outward_qty'         => $group['outward_qty'],
            'outward_rate'        => $group['outward_rate'],
            'outward_amount'      => $group['outward_amount'],

            // *** BALANCE now shows valuation only ***
            'balance_qty'         => $val_bal_qty,
            'unit_name'           => $group['unit_name'],
            'balance_amount'      => formatAmount(abs($val_bal_value)) . ' ' . $val_balance_crdr,

            // valuation detail
            'effective_val_method'=> $eff_method,
            'val_issue_value'     => round($val_issue_value, 2),          // COGS
            'val_issue_rate'      => round($val_issue_rate,  4),
            'valuation_calc'      => formatAmount($val_issue_value),
            'val_balance_rate'    => round($val_bal_rate,    4),
            'val_balance_value'   => round($val_bal_value,   2),

            // Profit (for outward rows)
            'val_profit'          => round($val_profit, 2),
            'val_profit_rate'     => round($val_profit_rate, 4),
            'val_profit_string'   => ($group['outward_qty']>0)
                                        ? formatAmount($val_profit) . (($val_profit>=0)?' (Profit)':' (Loss)')
                                        : '',
            // convenient alias if your grid expects a "Profit" text column
            'profit'              => ($group['outward_qty']>0)
                                        ? formatAmount($val_profit) . (($val_profit>=0)?' (Profit)':' (Loss)')
                                        : '',

            'voucher_txn_id'      => $group['vch_txn_id'],
            'txn_id'              => $group['itm_txn_id'],
            'itm_id_unit_id'      => $group['itm_id_unit_id'],
			'valuation_log'     => $all_logs

        ];
    }

    return "{\"totalRecords\":" .$total_records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($final_data)."}";
}

	public function InsertOppVal($data)
	{
		$this->db->table('itmoppyval')->insert($data);
	}
	
	function insert_itm_txn_entry($txn_data){
	  	$this->db->table("itemtxnmst")->insert($txn_data);	
    }
	
    function clear_entries($table_name,$item_id)
    {
	 $this->db->table($table_name)
	  ->where('cmp_id',$this->company_id)
	  ->where('itm_id_unit_id LIKE', $item_id . '_%')
	  ->delete();
    }
    
  public function get_item_units($item_id)
{
    $item_id = (int) $item_id;
    $final_units = [];

    // =========================
    // GET UNITS FROM TXN + OPENING
    // =========================
    $sql = "
        SELECT DISTINCT split_part(itm_id_unit_id::text, '_', 2)::int AS itm_unit_id
        FROM itemtxnmst
        WHERE itm_id_unit_id IS NOT NULL
          AND itm_id_unit_id::text <> ''
          AND itm_id_unit_id::text LIKE '%\\_%'
          AND split_part(itm_id_unit_id::text, '_', 2) <> ''
          AND split_part(itm_id_unit_id::text, '_', 1) <> ''
          AND split_part(itm_id_unit_id::text, '_', 1)::int = ?

        UNION

        SELECT DISTINCT split_part(itm_id_unit_id::text, '_', 2)::int AS itm_unit_id
        FROM itmoppybal
        WHERE itm_id_unit_id IS NOT NULL
          AND itm_id_unit_id::text <> ''
          AND itm_id_unit_id::text LIKE '%\\_%'
          AND split_part(itm_id_unit_id::text, '_', 2) <> ''
          AND split_part(itm_id_unit_id::text, '_', 1) <> ''
          AND split_part(itm_id_unit_id::text, '_', 1)::int = ?
    ";

    $query   = $this->db->query($sql, [$item_id, $item_id]);
    $unitIds = array_column($query->getResultArray(), 'itm_unit_id');

    // =========================
    // 🔥 FALLBACK: DEFAULT UNIT
    // =========================
    if (empty($unitIds)) {

        $defaultUnit = $this->db->table('itmmstdetn')
            ->select('itm_def_unit_id')
            ->where('itm_id', $item_id)
            ->where('cmp_id', $this->company_id)
            ->get()
            ->getRow();

        if (!empty($defaultUnit) && !empty($defaultUnit->itm_def_unit_id)) {
            $unitIds[] = (int)$defaultUnit->itm_def_unit_id;
        }
    }

    // =========================
    // STILL EMPTY → RETURN EMPTY
    // =========================
    if (empty($unitIds)) {
        return [];
    }

    // =========================
    // REMOVE DUPLICATES (SAFETY)
    // =========================
    $unitIds = array_unique($unitIds);

    // =========================
    // GET UNIT DETAILS
    // =========================
    $units = $this->db->table('itmunitmst')
        ->select('itm_unit_id, itm_unit_name')
        ->whereIn('itm_unit_id', $unitIds)
        ->where('cmp_id', $this->company_id)
        ->get()
        ->getResultArray();

    foreach ($units as $row) {
        $final_units[] = [
            "unit_id"   => $row['itm_unit_id'],
            "unit_name" => $row['itm_unit_name']
        ];
    }

    return $final_units;
}
	
	public function get_mc_list_by_group($mc_grp_id)
	{
		$final = [];
		$mcmasternn_tbl  = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
    	$builder = $this->db->table($mcmasternn_tbl);
    	$builder->where('mat_cent_grp_id', $mc_grp_id);
    	$result = $builder->get()->getResultArray();
    	foreach ($result as $key => $value) {
    		$final[] = $value['mat_cent_id'];
    	}
    	return $final;
	}    
   
   public function check_item_exists($id,$name){
        $data = $this->db->table("itemmaster")->select('itm_name')->where('itm_is_active',1)->where('cmp_id',$this->company_id)->where('LOWER(itm_name)', strtolower($name))->get()->getRowArray();
       if($data)
        return true; 
        else
        return false;
     }
	 
	public function check_item_txn_exists($id){
        $data = $this->db->table("itemtxnmst")->select('itm_id_unit_id')->where('vch_txn_id IS NOT NULL')->where('txn_id IS NOT NULL')->where('cmp_id',$this->company_id)->where('itm_id_unit_id LIKE', $id . '_%')->where('hobo_id', $this->bo_id)->get()->getRowArray();
       if($data)
        return true; 
        else
        return false;
     } 
	 
	public function check_item_opn_exists($id){
       $data = $this->db->table("itmoppybal")->select('itm_id_unit_id')->where('itm_op_bal_qty >',0)->where('cmpfymastr_id',$this->fy_id)->where('cmp_id',$this->company_id)->where('itm_id_unit_id LIKE', $id . '_%')->where('hobo_id', $this->bo_id)->get()->getRowArray();
       if($data)
         return true; 
        else
         return false;
     }
	
	public function changestatus_single_accounts($id,$status){
	  $this->db->table("itemmaster")->where('cmp_id',$this->company_id)->where("itm_id",$id)->update(["itm_is_active"=>$status]);
	  $this->db->table("undercrsmt")->where('cmp_id',$this->company_id)->where("crs_mst_id",$id)->where("crs_mst_type",3)->where("cmpfymastr_id",$this->fy_id)->update(["crs_is_active"=>$status]);
	 } 	
	 
	public function get_item_name($id){
         $data = $this->db->table("itemmaster")->select('itm_name')->where('itm_id', $id)->get()->getRowArray();
         $item_name = $data['itm_name'];
         return $item_name;
     }
  
   public function check_item_group_exists($id,$name){
     $data = $this->db->table("itemgrpmst")->select('itm_grp_name')->where('itm_grp_is_active',1)->where('cmp_id',$this->company_id)->where('LOWER(itm_grp_name)', strtolower($name))->get()->getRowArray();
     if($data)
        return true; 
        else
        return false;
     }	
     
  public function changestatus_single_group($id,$status){
   $this->db->table("itemgrpmst")->where('cmp_id',$this->company_id)->where("itm_grp_id",$id)->update(["itm_grp_is_active"=>$status]);
   $this->db->table("undercrsmt")->where('cmp_id',$this->company_id)->where("crs_mst_id",$id)->where("crs_mst_type",6)->where("cmpfymastr_id",$this->fy_id)->update(["crs_is_active"=>$status]); 
   } 
   
   public function get_mc_list()
    {
     return $this->db->table("matcentmst")->where('cmp_id',$this->company_id)->orderBy('mat_cent_name')->get()->getResultArray();
    } 
   
   public function add_itm_others($data){
       $this->db->table("itmmstdetn")->insert($data);
   }
   
   public function edit_itm_others($item_id,$data){
       $this->db->table("itmmstdetn")->where('cmp_id',$this->company_id)->where('itm_id',$item_id)->update($data);
   
   }
   
   public function get_itm_others_info($item_id){
       $this->db->table("itmmstdetn")->where('cmp_id',$this->company_id)->where('itm_id',$itm_id)->get()->getRowArray();
   }
   
   
   public function add_item($data){	            
	    $table = $this->db->table("itemmaster")->where('cmp_id',$this->company_id)
            ->where('LOWER(itm_name)', strtolower(trim($data['itm_name'])))
			->where('itm_is_active', 1)
           ->get()->getRowArray();	                                               
	    if($table){
		    return ['status' => false, 'message' => 'Item Name must be unique'];
	    }		
		
	    $table = $this->db->table("itemmaster")->where('cmp_id',$this->company_id)
            ->where('LOWER(itm_alias)', strtolower(trim($data['itm_alias'])))
			->where('itm_is_active', 1)
           ->get()->getRowArray();	                                               
	    if($table){
		    return ['status' => false, 'message' => 'Item Alias must be unique'];
	    }
		
		$table = $this->db->table("itemmaster")->where('cmp_id',$this->company_id)
            ->where('LOWER(itm_print_name)', strtolower(trim($data['itm_print_name'])))
			->where('itm_is_active', 1)
           ->get()->getRowArray();	                                               
	    if($table){
		    return ['status' => false, 'message' => 'Item Print Name must be unique'];
	    }

	    $this->db->table("itemmaster")->insert($data);
	    $item_id          = $this->db->insertID();
	    return ['status' => true, 'item_id' => $item_id];	     
     }
 
   public function account_info($account_id){	 
       return $this->db->table("acctmaster")->where('cmp_id', $this->company_id)->where('acc_id', $account_id)->get()->getRowArray();   	   
     }

	public function item_info($item_id){	 
       return $this->db->table("itemmaster")->where('cmp_id', $this->company_id)->where('itm_id', $item_id)->get()->getRowArray();   	   
     }   
   
	 public function remove_single_items($item_id){
		$this->db->table("itemtxnmst")->where('cmp_id', $this->company_id)->where('itm_id_unit_id LIKE', $item_id . '_%')->delete();
		$this->db->table("itmmstdetn")->where('cmp_id', $this->company_id)->where('itm_id',$item_id)->delete();			
		$this->db->table("undercrsmt")->where('cmpfymastr_id', $this->fy_id)->where('cmp_id', $this->company_id)->where('crs_mst_type',3)->where('crs_mst_id',$item_id)->delete();
		$this->db->table("itemmaster")->where('cmp_id', $this->company_id)->where('itm_id',$item_id)->delete();		
		return TRUE;
	 }
	 
	 function check_item_with_voucher($id)
     {
         $data =  $this->db->table("cmptxnmstn")->where('cmp_id',$this->company_id)->where('master_id', $id)->where('master_id_type','itm')->get()->getRowArray();
	     if($data){
	         return 1;
	     }
	     return 0;
     }
	 
	 public function remove_single_groups($id){
		$this->db->table("itemgrpmst")->where('cmp_id',$this->company_id)->where('itm_grp_id',$id)->delete();
	    $this->db->table("undercrsmt")->where('cmp_id',$this->company_id)->where('crs_mst_type',6)->where('crs_mst_id',$id)->delete();	
	    return TRUE;
	 }

   
	 public function remove_single_category($id){
		$this->db->table("itemcatmst")->where('cmp_id',$this->company_id)->where('itm_cat_id',$id)->delete();
	    $this->db->table("undercrsmt")->where('cmp_id',$this->company_id)->where('crs_mst_type',4)->where('crs_mst_id',$id)->delete();	
	    return TRUE;
	 } 
	 
	public function get_item_category_name($id)
	{
	    $data = $this->db->table("itemcatmst")->select('itm_cat_name')->where('cmp_id',$this->company_id)->where('itm_cat_id', $id)->get()->getRowArray();
        $item_cat_name = $data['itm_cat_name'];
        return $item_cat_name;
	}
	
	 public function check_item_category_exists($id,$name){
     $data = $this->db->table("itemcatmst")->select('itm_cat_name')->where('itm_cat_is_active',1)->where('cmp_id',$this->company_id)->where('LOWER(itm_cat_name)', strtolower($name))->get()->getRowArray();
     if($data)
        return true; 
        else
        return false;
     }
	public function changestatus_single_category($id,$status){
     $this->db->table("itemcatmst")->where('cmp_id',$this->company_id)->where("itm_cat_id",$id)->update(["itm_cat_is_active"=>$status]);
     $this->db->table("undercrsmt")->where('cmp_id',$this->company_id)->where("crs_mst_id",$id)->where("crs_mst_type",4)->where("cmpfymastr_id",$this->fy_id)->update(["crs_is_active"=>$status]); 
   }  
   public function add_category($data){
	   $exists1 = $this->db->table( "itemcatmst")->where('cmp_id',trim($data['cmp_id']))
	               ->where('LOWER(itm_cat_name)', strtolower(trim($data['itm_cat_name'])))
				   ->where('itm_cat_is_active',1)
				   ->get()->getRowArray(); 
	   $exists2 = $this->db->table("itemcatmst")->where('cmp_id',trim($data['cmp_id']))
	               ->where('LOWER(itm_cat_alias)', strtolower(trim($data['itm_cat_alias'])))
				   ->where('itm_cat_is_active',1)
				   ->get()->getRowArray(); 
	    if($exists1)
		    return "-1";
	    else  if($exists2)
		   return "-2";
	    else{
		  $this->db->table("itemcatmst")->insert($data);
		  $itm_cat_id = $this->db->insertID();
		  return $itm_cat_id;
	     }
   }
   
  public function modify_category($update_data,$category_id){
	    $table = $this->db->table("itemcatmst")
	    		->where('LOWER(itm_cat_name)',strtolower(trim($update_data['itm_cat_name'])))
				 ->where('cmp_id',$this->company_id)
                ->where('itm_cat_id !=',$category_id)
        	    ->get()->getRowArray(); 
	    if($table){
	        return ['status' => false, 'message' => 'Name must be unique'];
	    }
	    
	    $table = $this->db->table("itemcatmst")
		       ->where('cmp_id',$this->company_id)
	    	   ->where('itm_cat_id !=',$category_id)
        	   ->Where('LOWER(itm_cat_alias)',strtolower(trim($update_data['itm_cat_alias'])))
        	   ->get()->getRowArray();
	    if($table){
	        return ['status' => false, 'message' => 'Alias must be unique'];
	    }  
	 $table = $this->db->table("itemcatmst")->where('cmp_id',$this->company_id)->where('itm_cat_id',$category_id)->update($update_data);		
     if($table){	
	   return ['status' => true, 'account_id' => $category_id];
	 }
	 return ['status' => false,'message'=>'Something wrong'];	 
   }   
   
	public function get_item_group_name($id){
         $data = $this->db->table("itemgrpmst")->select('itm_grp_name')->where('cmp_id',$this->company_id)->where('itm_grp_id', $id)->get()->getRowArray();
         $item_group_name = $data['itm_grp_name'];
         return $item_group_name;
     }  
	 
   public function add_undercrsmt($txn_data){
	  	$this->db->table("undercrsmt")->insert($txn_data);	
    }	
	
   public function add_group($data){
	    $exists1 = $this->db->table("itemgrpmst")->where('cmp_id',trim($data['cmp_id']))
	              ->where('LOWER(itm_grp_name)', strtolower(trim($data['itm_grp_name'])))
				  ->where('itm_grp_is_active',1)
				  ->get()->getRowArray(); 
	   $exists2 = $this->db->table("itemgrpmst")->where('cmp_id',trim($data['cmp_id']))
	              ->where('LOWER(itm_grp_alias)', strtolower(trim($data['itm_grp_alias'])))
				  ->where('itm_grp_is_active',1)
				  ->get()->getRowArray(); 
	    if($exists1)
		 return "-1";
	    else if($exists2)
		 return "-2";
	    else{
		  $this->db->table("itemgrpmst")->insert($data);
		  $item_grp_id = $this->db->insertID();
		  return $item_grp_id;
	     }
     }   
     
   public function update_group($update_data,$itm_grp_id){
	   $table = $this->db->table("itemgrpmst")
	    		->where('LOWER(itm_grp_name)',strtolower(trim($update_data['itm_grp_name'])))
				 ->where('cmp_id',$this->company_id)
                ->where('itm_grp_id !=',$itm_grp_id)
        	    ->get()->getRowArray(); 
	    if($table){
	        return ['status' => false, 'message' => 'Name must be unique'];
	    }
	    
	    $table = $this->db->table("itemgrpmst")
		       ->where('cmp_id',$this->company_id)
	    	   ->where('itm_grp_id !=',$itm_grp_id)
        	   ->Where('LOWER(itm_grp_alias)',strtolower(trim($update_data['itm_grp_alias'])))
        	   ->get()->getRowArray();
	    if($table){
	        return ['status' => false, 'message' => 'Alias must be unique'];
	    }  
	 $table = $this->db->table("itemgrpmst")->where('cmp_id',$this->company_id)->where('itm_grp_id',$itm_grp_id)->update($update_data);		
     if($table){	
	   return ['status' => true, 'account_id' => $itm_grp_id];
	 }
	 return ['status' => false,'message'=>'Something wrong'];
	 	
   } 
   
   
   public function update_item($data,$item_id){
	       $fields = [
				'itm_name'      => 'Item Name',
				'itm_alias'      => 'Item Alias name',
				'itm_print_name' => 'Item Print name',
			];

			foreach ($fields as $field => $label) {
				if (!empty($data[$field])) {
					$value = strtolower(trim($data[$field]));
					$builder = $this->db->table('itemmaster');
					$builder->where('cmp_id', $this->company_id);
					$builder->where("LOWER($field) = '$value'", null, false); // Raw where clause
					$builder->where('itm_id !=', $item_id);					
					$row = $builder->get();
					if ($row && ($result = $row->getRowArray())) {
						return ['status' => false, 'message' => "$label must be unique"];
					}
				}
			}
	    
	    $this->db->table("itemmaster")->where('cmp_id',$this->company_id)->where('itm_id',$item_id)->update($data);		
		return ['status' => true, 'item_id' => $item_id];	
   } 
   
   public function ajax_category_list(){
	    $builder =  $this->db->table("itemcatmst");
	    $builder->join("undercrsmt", "undercrsmt.crs_mst_id  = itemcatmst.itm_cat_id AND undercrsmt.crs_mst_type =4 AND undercrsmt.cmpfymastr_id = $this->fy_id AND undercrsmt.cmp_id =$this->company_id", 'left');
	    $builder->where('itemcatmst.cmp_id', $this->company_id);
		$builder->where("undercrsmt.cmpfymastr_id IS NOT NULL");	
	    $builder->orderBy('itemcatmst.itm_cat_name');
		$result  = $builder->get()->getResultArray(); 
		$records = array();       
        foreach($result as $values){
			$item_cat  = $values['itm_cat_name'];
			$confirmstatus = ($values['crs_is_active']==1)?'INACTIVE':'ACTIVE';	
		    $acc_status_vl  = ($values['crs_is_active']==1)?0:1;
            $records[] = array(	
					  'checkbox'=>'<input name="category_ids[]" class="checkbox category_row" data-acc_status_vl="'.$acc_status_vl.'" data-confirmstatus= "'.$confirmstatus.'"  data-id="'.$values['itm_cat_id'].'"  type="checkbox" value="'.$values['itm_cat_id'].'">',
                      'category_id' => $values['itm_cat_id'],
                      'item_catg'   => ucwords($item_cat),
                      'item_cat_alias'   =>$values['itm_cat_alias'],
					  'acc_status_vl'   => ($values['crs_is_active']==1)?0:1,
					  'item_catg_status'   => ($values['crs_is_active']==1)?'ACTIVE':'INACTIVE',
					  'alert_acc_status'   => ($values['crs_is_active']==1)?'INACTIVE':'ACTIVE',
				   );  
		     }	
			 
 	     return $records;
     }

     public function all_item_category_export(){
	
		$item_cat_master_tbl = $this->company_id.'_itemcatmst_'.$this->session->get('ses_comp_fy_id');
        $builder  = $this->db->table($item_cat_master_tbl); 
        $builder->orderBy('item_cat');		 	
        $result  = $builder->get()->getResultArray(); 
        $records = array();       
        foreach($result as $values){
			$item_cat = $values['item_cat'];
           $records[] = array(	
		              'category_id' => $values['icatgms_id'],
                      'item_catg'   => ucwords($item_cat),
                      'item_cat_alias'   =>$values['item_cat_alias']
				   );  
		     }	
 	     return $records;
     }
   
   public function update_item_details($data,$item_id){
	   $item_master_tbl = $this->company_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');

	    $this->db->table($item_master_tbl)->where('item_id',$item_id)->update($data);
        $this->company_all_items();		
		return ['status' => true, 'item_id' => $item_id];	
   } 
   
   public function ajax_group_list(){
	    $builder =  $this->db->table("itemgrpmst");
	    $builder->join("undercrsmt", "undercrsmt.crs_mst_id  = itemgrpmst.itm_grp_id AND undercrsmt.crs_mst_type =6 AND undercrsmt.cmpfymastr_id =$this->fy_id AND undercrsmt.cmp_id =$this->company_id", 'left');
	    $builder->where('itemgrpmst.cmp_id', $this->company_id);
		$builder->where("undercrsmt.cmpfymastr_id IS NOT NULL");
	    $builder->orderBy('itemgrpmst.itm_grp_name');
		$result  = $builder->get()->getResultArray(); 
		$records = array();		
        foreach($result as $values){
		   $confirmstatus = ($values['crs_is_active']==1)?'INACTIVE':'ACTIVE';	
		   $acc_status_vl  = ($values['crs_is_active']==1)?0:1;
           $records[] = array(	
		              'checkbox'=>'<input name="group_ids[]" class="checkbox groups_row" data-acc_status_vl="'.$acc_status_vl.'" data-confirmstatus= "'.$confirmstatus.'"  data-id="'.$values['itm_grp_id'].'"  type="checkbox" value="'.$values['itm_grp_id'].'">',
                      'item_grp_id'      => $values['itm_grp_id'],
				  	  'item_grp_name'    => ucwords($values['itm_grp_name']),
					  'item_grp_alias'   => $values['itm_grp_alias'],
					  'acc_status_vl'   => ($values['crs_is_active']==1)?0:1,
					  'item_grp_status'   => ($values['crs_is_active']==1)?'ACTIVE':'INACTIVE',
					  'alert_acc_status'   => ($values['crs_is_active']==1)?'INACTIVE':'ACTIVE',
					  'item_grp_primary' => ($values['crs_mst_is_primary']!='' && $values['crs_mst_is_primary']!='0')?'YES':'No'					  					  	
				       ); 
		            }	
 	       return $records;
     }

   
  public function all_item_group_export()
{
    $builder = $this->db->table('itemgrpmst ig');

   $builder->select("
    ig.itm_grp_id,
    ig.itm_grp_name AS item_grp_name,
    ig.itm_grp_alias AS item_grp_alias,
    CASE
        WHEN uc.crs_mst_is_primary = 1 THEN 'YES'
        ELSE 'NO'
    END AS item_grp_primary,
    parent.itm_grp_name AS parent_group
");

    $builder->join(
        'undercrsmt uc',
        'uc.crs_mst_id = ig.itm_grp_id
        AND uc.crs_mst_type = 6
        AND uc.cmpfymastr_id = '.$this->fy_id.'
        AND uc.cmp_id = '.$this->company_id,
        'left'
    );

    $builder->join(
        'itemgrpmst parent',
        'parent.itm_grp_id = uc.under_crs_mst_id',
        'left'
    );

    $builder->where('ig.cmp_id', $this->company_id);

    $builder->orderBy('ig.itm_grp_name');

    return $builder->get()->getResultArray();
}


    function get_item_detail_info($item_id,$comp_id)
    {
		$itemgrpmst_tbl  = "itemgrpmst";
        $item_master_tbl = "itemmaster";
		$itmunitmst_tbl  = "itmunitmst"; 
		$undercrsmt_tbl  = "undercrsmt";
		$itemcatmst_tbl  = "itemcatmst";
		$itmmstdetn_tbl  = "itmmstdetn";
		$taxcatmstn_tbl  = "taxcatmstn";
		
	    $builder         = $this->db->table($item_master_tbl.' itemmaster');
		$builder->select("
			$item_master_tbl.*, 
			$itemgrpmst_tbl.itm_grp_id as item_grp_id,
 			$itemcatmst_tbl.itm_cat_id as item_cat,  
			$itmunitmst_tbl.itm_unit_name as item_unit,
			$itmmstdetn_tbl.itm_def_unit_id,
			$itmmstdetn_tbl.itm_sales_acc_id,
			$itmmstdetn_tbl.itm_pur_acc_id,
			$itmmstdetn_tbl.itm_hsn,
			$taxcatmstn_tbl.tax_cat_mst_id,
		  ");			
       
       // Joins
		$builder->join(
			$taxcatmstn_tbl,
			"$taxcatmstn_tbl.tax_cat_mst_id = itemmaster.tax_cat_mst_id AND " .
			"$taxcatmstn_tbl.cmp_id = " . $this->db->escape($this->company_id),
			'left'
		);
     
		$builder->join(
			$itmmstdetn_tbl,
			"$itmmstdetn_tbl.itm_id = itemmaster.itm_id AND " .
			"$itmmstdetn_tbl.cmp_id = " . $this->db->escape($this->company_id),
			'left'
		);
		  
	    $builder->join(
			$itmunitmst_tbl,
			"$itmunitmst_tbl.itm_unit_id = $itmmstdetn_tbl.itm_def_unit_id AND " .
			"$itmunitmst_tbl.cmp_id = " . $this->db->escape($this->company_id),
			'left'
		  );	  
		$builder->join(
			$undercrsmt_tbl,
			"$undercrsmt_tbl.crs_mst_id = itemmaster.itm_id 
			AND $undercrsmt_tbl.crs_mst_type = 3
			AND 
			$undercrsmt_tbl.cmpfymastr_id = $this->fy_id
			",
			'left'
		);
		 $builder->join($itemgrpmst_tbl, "$itemgrpmst_tbl.itm_grp_id = $undercrsmt_tbl.under_crs_mst_id AND $undercrsmt_tbl.crs_mst_id =itemmaster.itm_id AND $undercrsmt_tbl.crs_mst_type =3 AND $undercrsmt_tbl.cmp_id =$this->company_id", 'left');
	     $builder->join(
	 		$itemcatmst_tbl,
			"$itemcatmst_tbl.itm_cat_id = $itmmstdetn_tbl.itm_cat_id AND " .
			"$itemcatmst_tbl.cmp_id = " . $this->db->escape($this->company_id),
			'left'
		);
		$builder->where("itemmaster.cmp_id",$this->company_id);
		$builder->where("itemmaster.itm_id",$item_id);
		$result  = $builder->get()->getRowArray();
		//echo $this->db->getlastquery();
      	
        return $result;
    }
    
	
    function get_units_info($unit_id,$comp_id){	 
	  return $this->db->table("itmunitmst")->where('itm_unit_id', $unit_id)->where('cmp_id',$this->company_id)->get()->getRowArray();   	   
    }   
	
	function item_category_info($catg_id){	 
	  return $this->db->table("itemcatmst")->where('itm_cat_id', $catg_id)->where('cmp_id', $this->company_id)->get()->getRowArray();   	   
    }  
	
	public function updateGroupParent($old_parent_id, $new_parent_id){
    
    // Avoid running if IDs are same
    if ((int)$old_parent_id != (int)$new_parent_id) {
   
    // Build query
    $builder = $this->db->table('undercrsmt');
    return $builder->where('cmp_id', $this->company_id)
					 ->where('cmpfymastr_id ',$this->fy_id)
					  ->whereIn('crs_mst_type',[5,6])
                   ->where('crs_mst_parent_id', $old_parent_id)
                   ->set([
                       'crs_mst_parent_id' => $new_parent_id
                   ])
                   ->update();
   }
}
   
   public function update_undercrsmt($data,$account_id,$crs_mst_type){		
	    $undercrsmt_tbl = "undercrsmt";	 
		$table = $this->db->table($undercrsmt_tbl)
		       ->where('cmp_id',$this->company_id)
	    	   ->where('crs_mst_id',$account_id)
			   ->where('crs_mst_type',$crs_mst_type)
			   ->where('cmpfymastr_id ',$this->fy_id)
        	   ->get()->getRowArray(); 
		if($table){
		 $updata =array("under_crs_mst_id"=>$data['under_crs_mst_id'],"crs_mst_parent_id"=>$data['crs_mst_parent_id'],
		                "under_main_id"=>$data['under_main_id'],"crs_mst_is_primary"=>$data['crs_mst_is_primary']);	
		 $this->db->table($undercrsmt_tbl)
		      ->where('cmp_id',$this->company_id)
			  ->where('crs_mst_id',$account_id)
			  ->where('crs_mst_type',$crs_mst_type)
			  ->where('cmpfymastr_id',$this->fy_id)->update($updata);	
		}else{
		 $this->db->table($undercrsmt_tbl)->insert($data);		
		}
		return ['status' => true, 'account_id' => $account_id];	
   }
   
   function item_group_info($item_grp_id){
	  $builder = $this->db->table('itemgrpmst');
	  $builder->join("undercrsmt", "undercrsmt.crs_mst_id = itemgrpmst.itm_grp_id AND undercrsmt.crs_mst_type =6 AND undercrsmt.cmp_id =$this->company_id", 'left');
      $builder->where('itemgrpmst.cmp_id',$this->company_id);
	  $builder->where('undercrsmt.crs_mst_id', $item_grp_id);  
	  $row = $builder->get()->getRowArray();
      return $row;   	   
    }   
   	 
   function items_group_dropdown(){
	   $data =  $this->db->table("itemgrpmst")->where('cmp_id',$this->company_id)->orderBy('itm_grp_name','ASC')->get()->getResultArray();
	   $final_result      = array();
	   $final_result['']  = '';
	   if($data){
		  foreach($data as $row){
              $final_result[$row['itm_grp_id']] = $row['itm_grp_name'];			   
	        }
        }
	  return $final_result;	
     } 
	 
	 function get_groups_by_parent($id)
	 {
		 $array = [];
		 $builder =  $this->db->table("accgrpmstn acgrpmst");
		 $builder->join("undercrsmt", "undercrsmt.crs_mst_id = acgrpmst.acc_grp_id AND undercrsmt.crs_mst_type =2 AND undercrsmt.cmp_id =$this->company_id", 'left');
		 $builder->where('undercrsmt.crs_mst_parent_id', $id);
		 $builder->where('undercrsmt.crs_mst_is_primary', 1);
		 $builder->where('acgrpmst.cmp_id', $this->company_id);
		 $builder->where("undercrsmt.cmpfymastr_id",$this->fy_id);		
		 $data =  $builder->get()->getResultArray();
		 //echo $this->db->getlastquery();
		 if($data){
    		foreach($data as $key => $value) {
    			$array[$value['acc_grp_id']]=$value['acc_grp_id'];
    		}
    	}
    	return $array;
	 }
	 
	 function sales_acc_dropdown(): array
{
    // 1. Start with the main "Sales" group ID itself.
    $salesGroupId = 8;
    $allRelevantGroupIds = [$salesGroupId];

    // 2. Find all child and grandchild groups under the main Sales group.
    $childGroupIds = $this->get_all_child_groups($salesGroupId);

    // 3. Merge the main group ID with all its child group IDs.
    if (!empty($childGroupIds)) {
        $allRelevantGroupIds = array_merge($allRelevantGroupIds, $childGroupIds);
    }
    
    // 4. Now, find all accounts that belong to ANY of these groups.
    $builder = $this->db->table('acctmaster A');
    $builder->select('A.acc_id, A.acc_name');
    
    // Join with undercrsmt to link accounts to their parent groups.
    $builder->join('undercrsmt u', 'u.crs_mst_id = A.acc_id', 'inner');

    // Filter by the account's properties
    $builder->where('A.cmp_id', $this->company_id);
    $builder->where('A.acc_is_active', 1);

    // Filter by the undercrsmt link properties
    $builder->where('u.cmp_id', $this->company_id);
    $builder->where('u.cmpfymastr_id', $this->fy_id);
    $builder->where('u.crs_mst_type', 1); // Type 1 is for Accounts

    // The most important part: find accounts whose parent ID is in our list of groups.
    $builder->whereIn('u.crs_mst_parent_id', $allRelevantGroupIds);

    $builder->orderBy('A.acc_name', 'ASC');
    $data = $builder->get()->getResultArray();
    
    // 5. Format the result for a dropdown.
    $final_result = ['' => '']; // Start with a blank option
    if ($data) {
       foreach ($data as $row) {
           $final_result[$row['acc_id']] = ucwords(strtolower($row['acc_name']));
       }
    }
    
    return $final_result;
}
     
	 
	 function get_all_child_groups(int $parentId): array
{
    // The recursive query to traverse the group hierarchy.
    $sql = "
        WITH RECURSIVE group_hierarchy AS (
            -- Anchor member: Start with the direct children of the parent ID
            SELECT crs_mst_id
            FROM undercrsmt
            WHERE crs_mst_parent_id = ?
              AND crs_mst_type = 2 -- Type 2 is for Account Groups
              AND cmp_id = ?
              AND cmpfymastr_id = ?

            UNION ALL

            -- Recursive member: Find children of the groups we just found
            SELECT u.crs_mst_id
            FROM undercrsmt u
            INNER JOIN group_hierarchy gh ON u.crs_mst_parent_id = gh.crs_mst_id
            WHERE u.crs_mst_type = 2
              AND u.cmp_id = ?
              AND u.cmpfymastr_id = ?
        )
        -- Finally, select all unique group IDs found
        SELECT DISTINCT crs_mst_id FROM group_hierarchy;
    ";

    // We pass company and FY IDs multiple times to bind them safely in both parts of the UNION
    $bindings = [$parentId, $this->company_id, $this->fy_id, $this->company_id, $this->fy_id];

    $query = $this->db->query($sql, $bindings);
    $result = $query->getResultArray();

    // The query returns an array of arrays, e.g., [['crs_mst_id' => 10], ['crs_mst_id' => 12]]
    // We need to flatten it to a simple array of IDs, e.g., [10, 12]
    return array_column($result, 'crs_mst_id');
}

    function purchase_acc_dropdown(): array
{
    // 1. Start with the main "Purchase" group ID itself.
    $purchaseGroupId = 7;
    $allRelevantGroupIds = [$purchaseGroupId];

    // 2. Find all child and grandchild groups under the main Purchase group.
    $childGroupIds = $this->get_all_child_groups($purchaseGroupId);

    // 3. Merge the main group ID with all its child group IDs.
    if (!empty($childGroupIds)) {
        $allRelevantGroupIds = array_merge($allRelevantGroupIds, $childGroupIds);
    }
    
    // 4. Now, find all accounts that belong to ANY of these groups.
    $builder = $this->db->table('acctmaster A');
    $builder->select('A.acc_id, A.acc_name');
    
    // Join with undercrsmt to link accounts to their parent groups.
    // An account's own entry in undercrsmt has crs_mst_type = 1.
    $builder->join('undercrsmt u', 'u.crs_mst_id = A.acc_id', 'inner');

    // Filter by the account's properties
    $builder->where('A.cmp_id', $this->company_id);
    $builder->where('A.acc_is_active', 1);

    // Filter by the undercrsmt link properties
    $builder->where('u.cmp_id', $this->company_id);
    $builder->where('u.cmpfymastr_id', $this->fy_id);
    $builder->where('u.crs_mst_type', 1); // Type 1 is for Accounts

    // The most important part: find accounts whose parent ID is in our list of groups.
    $builder->whereIn('u.crs_mst_parent_id', $allRelevantGroupIds);

    $builder->orderBy('A.acc_name', 'ASC');
    $data = $builder->get()->getResultArray();
    
    // 5. Format the result for a dropdown.
    $final_result = ['' => '']; // Start with a blank option
    if ($data) {
       foreach ($data as $row) {
           $final_result[$row['acc_id']] = ucwords(strtolower($row['acc_name']));
       }
    }
    
    return $final_result;
}
     
     function group_main_dropdown($group_id=''){
		$builder =  $this->db->table("itemgrpmst");
	    $builder->join("undercrsmt", "undercrsmt.crs_mst_id  = itemgrpmst.itm_grp_id AND undercrsmt.crs_mst_type =6 AND undercrsmt.cmp_id =$this->company_id", 'left');
        $builder->where('itemgrpmst.cmp_id', $this->company_id);
		if($group_id!='')
		$builder->where('undercrsmt.crs_mst_id !=', $group_id);
		$builder->orderBy('itemgrpmst.itm_grp_name','ASC'); 
		$data = $builder->get()->getResultArray();
	    $final_result = array();
	    $final_result['']  = 'Choose';
	    if($data){
		  foreach($data as $row){
              $final_result[$row['itm_grp_id']] = $row['itm_grp_name'];			   
	        }
         }
	     return $final_result;	
       } 
     
	 
	 function item_units_list($item_id){
	   $itmoppybal_tbl = $this->company_id.'_itmoppybal_'.$this->session->get('ses_comp_fy_id');
	   $this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");
		
	   $data =  $this->db->table($itmoppybal_tbl)
	                     ->select('item_unit')
	                     ->where('item_id', $item_id)
						 ->where('bo_id', $this->session->get('ses_boid'))
						 ->groupBy('item_unit')
						 ->get()->getResultArray();
	   $final_result = array();
	   if($data){
		  foreach($data as $row){
              $final_result[$row['item_unit']] = $row['item_unit'];			   
	        }
        }
	  return $final_result;	
     } 
	 	 
	 function units_dropdown(){
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
	 
	 function category_dropdown(){
	   $data =  $this->db->table("itemcatmst")->where('cmp_id', $this->company_id)->orderBy('itm_cat_name','ASC')->get()->getResultArray();
	   $final_result      = array();
	   $final_result['']  = '';
	   if($data){
		  foreach($data as $row){
              $final_result[$row['itm_cat_id']] = ucwords(strtolower($row['itm_cat_name']));			   
	        }
        }
	  return $final_result;	
     }
	 
	 function material_centre_dropdown(){
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
	 
     function check_item_with_category($cat_id)
     {
		 $builder =  $this->db->table("itemmaster");
		 $builder->join("undercrsmt", "undercrsmt.crs_mst_id = itemmaster.itm_id AND undercrsmt.crs_mst_type =3 AND undercrsmt.cmp_id =$this->company_id", 'left');
     	 $builder->where('undercrsmt.under_crs_mst_id',$cat_id);
		 $builder->where('itemmaster.cmp_id',$this->company_id);
		 $data    =  $builder->get()->getRowArray();
		 if($data){
	         return 1;
	     }
	     return 0;
     }
     
     function check_item_with_group($group_id)
     {
         $builder =  $this->db->table("itemmaster");
		 $builder->join("undercrsmt", "undercrsmt.crs_mst_id = itemmaster.itm_id AND undercrsmt.crs_mst_type =3 AND undercrsmt.cmp_id =$this->company_id", 'left');
     	 $builder->where('undercrsmt.under_crs_mst_id',$group_id);
		 $data    =  $builder->get()->getRowArray();
		 if($data){
	         return 1;
	     }
	     return 0;
     }
     
     function check_item_group($value)
     {
         $en_value = $value;
         $item_grp_master_tbl = $this->company_id.'_itemgrpmst_'.$this->session->get('ses_comp_fy_id');
	     $data =  $this->db->table($item_grp_master_tbl)->where('comp_id',$this->company_id)->where('LOWER(item_grp_name)', strtolower(trim($en_value)))->get()->getRowArray();
	     if(!empty($data)){
	         return 1;
	     }
	     return 0;
     }
     
     function get_or_create_group_id($value)
     {
         if(trim($value) == ''){
             $value = 'Main';
         }
         else{
             $value = trim($value);
         }
         $en_value = $value;
         $item_grp_master_tbl = $this->company_id.'_itemgrpmst_'.$this->session->get('ses_comp_fy_id');
	     $data =  $this->db->table($item_grp_master_tbl)->where('comp_id',$this->company_id)->where('LOWER(item_grp_name)', strtolower(trim($en_value)))->get()->getRowArray();
	     if(!empty($data)){
	         return $data['item_grp_id'];
	     }
	     else{
	         $item_group = [
	             'comp_id'          => $this->company_id,
	             'item_grp_name'    => $en_value,
	             'item_grp_alias'   => $en_value,
	             'item_grp_primary' => ''
	             ];
	         $this->db->table($item_grp_master_tbl)->insert($item_group);

	         $item_grp_id = $this->db->insertID();

		  $mst_base_id = $this->create_mst_base_id($item_grp_id,'itemgrpmst');
			$this->db->table($item_grp_master_tbl)
					->where('item_grp_id',$item_grp_id)
					->update(['mst_base_id' => $mst_base_id]);

	        return $item_grp_id;
	     }
     }
     
     public function get_or_create_category_id($value)
     {
        if(trim($value) == ''){
            return 0;
        }
        $item_cat_master_tbl = $this->company_id.'_itemcatmst_'.$this->session->get('ses_comp_fy_id');
	    $en_value = trim($value);
	    $data   = $this->db->table($item_cat_master_tbl)->where('comp_id',$this->company_id)->where('LOWER(item_cat)', strtolower(trim($en_value)))->get()->getRowArray(); 
	    if(!empty($data))
		 return $data['icatgms_id'];
	   else{
	       $item_category = [
	            'comp_id'    => $this->company_id,
	            'item_cat'   => $en_value
	       ];
		  $this->db->table($item_cat_master_tbl)->insert($item_category);

		  $icatgms_id = $this->db->insertID();

		  $mst_base_id = $this->create_mst_base_id($icatgms_id,'itemcatmst');
			$this->db->table($item_cat_master_tbl)
					->where('icatgms_id',$icatgms_id)
					->update(['mst_base_id' => $mst_base_id]);
		  return $icatgms_id;
	     }
     }
     
     public function get_or_create_unit_id($value)
     {
         if(trim($value) == ''){
             $name = 'NA';
         }
         else{
             $name = trim($value);
         }
        $item_unit_master_tbl = $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');
	    $en_name = $name;
	    $data   = $this->db->table($item_unit_master_tbl)->where('comp_id',$this->company_id)->where('LOWER(item_unit)', strtolower(trim($en_name)))->get()->getRowArray(); 
	    if(!empty($data))
		 return $data['unit_id'];
	    else{
	       $item_unit = [
	            'comp_id'           => $this->company_id,
	            'item_unit'         => $en_name,
	            'item_unit_alias'   => $en_name,
	            'item_unit_print'   => $en_name,
	            'item_unit_uqc'     => ''
	       ];
		  $this->db->table($item_unit_master_tbl)->insert($item_unit);
		  $unit_id = $this->db->insertID();

		  $mst_base_id = $this->create_mst_base_id($unit_id,'itmunitmst');
		  $this->db->table($item_unit_master_tbl)
					->where('unit_id',$unit_id)
					->update(['mst_base_id' => $mst_base_id]);
		  return $unit_id;
	     }
     }
	
}