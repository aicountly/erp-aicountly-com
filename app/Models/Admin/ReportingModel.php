<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Models\CommonModel;
use App\Models\Admin\VouchersModel;
use App\Models\Admin\StockStatusModel;
use App\Libraries\externaldb;


class ReportingModel extends Model{

	use ReportingPresenters;
	
	/** ====== TABLES (match your DB) ====== */
    private string $T_GROUPS   = 'accgrpmstn';
    private string $T_UNDER    = 'undercrsmt';
    private string $T_ACCOUNTS = 'acctmaster';
    private string $T_TXN      = 'accttxnmst';
    /** ==================================== */
	
	/**
     * Balance Sheet (Horizontal)
     *
     * @param string $view          condensed|detailed|schedules
     * @param string $from_date     YYYY-MM-DD
     * @param string $to_date       YYYY-MM-DD
     * @param string $nil_type      show|hide  (hide => suppress zero rows)
     * @param bool   $consolidated  true => ignore cmp_id filter
     * @return array                Data structure for the horizontal view
     */
	 
	function __construct() {
		parent::__construct();        
		/* ======= TABLE NAMES (match your schema) ======= */
    $T_GROUPS   = 'accgrpmstn';
     $T_UNDER    = 'undercrsmt';
     $T_ACCOUNTS = 'acctmaster';
     $T_TXN      = 'accttxnmst';
    /* =============================================== */
		$this->externaldb    =  new externaldb();	
		$this->session       =  \Config\Services::session();
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->VouchersModel =  new VouchersModel();
		$this->StockStatusModel =  new StockStatusModel();		
		$this->dberpunvrsl   =  $this->externaldb->erp_db();
		$this->aicountly_db  =  $this->externaldb->aicountly_db();
		$this->CommonModel   =  new CommonModel();	   		
		$this->company_id    =  $this->session->get('ses_company_id');
		$this->fy_id         =  $this->session->get('ses_comp_fy_id');
		$this->bo_id         =  $this->session->get('ses_boid');
		$this->univaictly    =  $this->externaldb->univaictly_db();
	} 
	
	public function user_uuid_info($uuid){
	    return $this->aicountly_db->table("aicountly_useraictly_univdb")->where('uuid',$uuid)
		           ->get()->getRowArray();  
	}
	
	public function VchApprvLogInfo($vch_txn_id){
	    
	 $response =  $this->db->table("erptxnaprv")->where('vch_txn_id',$vch_txn_id)
		           ->get()->getRowArray();  
		           
		//echo $this->db->getlastquery();
		//echo'<br>';
		return $response;
     }
	public function load_voucher_approvals($from_date, $to_date, $voucher_type_id, $type)
{
    /*******  type =4 means Pending Approvals  (acc_txn_type IS 4)***/
    /*******  type =5 means Rejected Txn (acc_txn_type IS 5) ***/
    
    $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
    $pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10;

    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP < 1) $pq_rPP = 10;
    
    // FIX: Correct table name
    $voucher_tbl    = 'acctvchreg';
    $account_tbl    = 'acctmaster';
    $accttxnmst_tbl = 'accttxnmst';
    $vchtypemst     = 'vchtypemst';

    $builder = $this->db->table("$voucher_tbl v");
    
    // PostgreSQL: 
    // 1.  Use STRING_AGG instead of GROUP_CONCAT
    // 2.  All non-aggregated columns must be in GROUP BY or use aggregate functions
    // 3. Use SUM/MAX for columns used in CASE or displayed
    $builder->select("
        MAX(v.vch_date) AS vch_date,				
        v.vch_txn_id, 
        v. acct_vch_type,
        MAX(v.acc_txn_type) AS acc_txn_type,
        MAX(vtmst.vch_name) AS vch_type_name,
        STRING_AGG(a.acc_name, ', ' ORDER BY a.acc_name) AS account_names, 
        SUM(CASE WHEN v.acc_txn_dr_amt != 0 THEN v.acc_txn_dr_amt ELSE v.acc_txn_cr_amt END) AS amount, 
        MAX(v.vch_narr) AS narration,
        SUM(v.acc_txn_dr_amt) AS dr_amount,
        SUM(v.acc_txn_cr_amt) AS cr_amount
    ", false);
    
    // All JOINs with false to prevent escaping issues
    $builder->join("$account_tbl a", 'a.acc_id = v.acc_id', 'left', false);
    $builder->join("$vchtypemst vtmst", 'vtmst.vch_type_id = v. acct_vch_type', 'left', false);	
    
    $builder->where('v.cmp_id', $this->company_id);	
    
    if($type == 4)
        $builder->where('v.acc_txn_type', 4);
    else if($type == 5)
        $builder->where('v. acc_txn_type', 5);	
    else
        $builder->where('v.acc_txn_type', 4);
    
    // PostgreSQL: Use proper syntax for IS NULL
    $builder->where('v. txn_id IS NULL', null, false);	    
    $builder->where('v.hobo_id', $this->bo_id);
    
    if($type != 2) // optional voucher have no type will exists all vouchers
        $builder->where('v.acct_vch_type', $voucher_type_id);
    
    if (! empty($from_date)) {
        $builder->where('v.vch_date >=', $from_date);
    }
    if (!empty($to_date)) {
        $builder->where('v.vch_date <=', $to_date);
    }
    
    // PostgreSQL: Only include non-aggregated columns in GROUP BY
    $builder->groupBy('v.vch_txn_id, v.acct_vch_type');
    
    // Clone for count BEFORE adding ORDER BY
    $countBuilder = clone $builder;
    $countSql = $countBuilder->getCompiledSelect(false);
    $countQuery = $this->db->query("SELECT COUNT(*) as total FROM ({$countSql}) AS count_sub");
    $countResult = $countQuery->getRowArray();
    $total_records = (int)($countResult['total'] ??  0);

    // Pagination logic
    if ($pq_curPage == 0) $pq_curPage = 1;
    $offset = ($pq_rPP * ($pq_curPage - 1));

    if ($offset > $total_records) {
        $pq_curPage = (int)ceil($total_records / $pq_rPP);
        $offset = ($pq_rPP * ($pq_curPage - 1));
    }
    if ($offset < 0) {
        $offset = 0;
    }
    
    // PostgreSQL: ORDER BY must use aggregated column or column in GROUP BY
    // Use false as 3rd parameter to prevent escaping
    $builder->orderBy('MAX(v.vch_date)', 'ASC', false);
    $builder->orderBy('v.vch_txn_id', 'ASC');
    $builder->limit($pq_rPP, $offset);
    
    // Fetch final data
    $result = $builder->get()->getResultArray();
    
    $records = [];
    $last_vch_no = 0;
    
    foreach($result as $key => $value)
    {
        $voucher_date = date("d-m-Y", strtotime($value['vch_date']));			
        $vch_no       = $value['vch_txn_id'];
        $voucher_no   = ($vch_no != $last_vch_no) ? $value['vch_txn_id'] : '';
        $vch_date     = ($vch_no != $last_vch_no) ? $voucher_date : '';	
        
        $approver_name = '';
        
        if($value['acc_txn_type'] == 4) { // approval name 
            $VchApprvLogInfo = $this->VchApprvLogInfo($value['vch_txn_id']);
            if($VchApprvLogInfo) {
                $uuid_to = $VchApprvLogInfo['uuid_to'];
                $user_uuid_info = $this->user_uuid_info($uuid_to);
                $approver_name = $user_uuid_info['user_firstname'] .  ' ' . $user_uuid_info['user_lastname'] . '(' . $VchApprvLogInfo['uuid_to'] . ')';
            }
        } 
        
        if($value['dr_amount'] > 0)
            $dr_cr = 'DR';
        else
            $dr_cr = 'CR';

        $records[] = [
            'voucher_txn_id'  => $value['vch_txn_id'],
            'voucher_type_id' => $value['acct_vch_type'],
            'voucher_type'    => $value['vch_type_name'],
            'account_name'    => $value['account_names'],
            'voucher_date'    => $vch_date,
            'amount'          => formatAmount($value['amount']),
            'amount_total'    => parseAmount($value['amount']),
            'narration'       => $value['narration'],
            'approver'        => $approver_name
        ];
        
        $last_vch_no = $value['vch_txn_id'];
    }
    
    echo "{\"totalRecords\":" .  $total_records .  ",\"curPage\":" . $pq_curPage . ",\"data\":" . json_encode($records) . "}";        
}

public function legacy_load_balance_sheet_vertical($view, $from_date, $to_date, $nil_type, $consolidated)
{
    /**
     * Detailed mode fix:
     * - Show ALL descendant accounts/Bill Sundry under each primary group (like horizontal detailed view).
     * - Totals still roll up all descendants.
     *
     * Sign rule (per requirement):
     * - BS LEFT (liabilities/equity): DR -> negative, CR -> positive  ⇒ display = -signed
     * - BS RIGHT (assets)          : DR -> positive, CR -> negative  ⇒ display = signed
     * where signed = Opening + (Dr − Cr), so + = debit, − = credit.
     */

    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date   = date('Y-m-d', strtotime($to_date));

    // ---------- helpers ----------
    $fmt = function ($n) { return ($n !== '' && $n != 0) ? formatAmount($n) : ''; };

    $hdrRow = function (string $title) {
        return [
            'group_id'      => 0,
            'group_name'    => $title,
            'type'          => 'hdr',
            'balance'       => '',
            'balance_total' => 0,
            'pq_rowattr'    => ['style' => 'background:#E6E6FA;font-weight:bold;'],
            'pq_cellattr'   => [],
            'pq_cellcls'    => [],
        ];
    };

    $makeRow = function (string $name, float $amount, string $type = 'row', int $id = 0,
                         string $style = '', bool $addToTotal = true) use ($fmt) {
        return [
            'group_id'      => $id,
            'group_name'    => $name,
            'type'          => $type,
            'balance'       => $fmt($amount),
            'balance_total' => $addToTotal ? $amount : 0.0,
            'pq_rowattr'    => $style ? ['style' => $style] : [],
            'pq_cellattr'   => [
                'balance' => [
                    'data-group_name' => $name,
                    'data-group_id'   => $id,
                    'data-dataIndx'   => 'group_name',
                    'data-id'         => $id,
                    'data-type'       => $type,
                ]
            ],
            'pq_cellcls'    => ['balance' => 'hover-cell'],
        ];
    };

    // ---------- Inventory valuation ----------
    $inventoryVal = (float)$this->StockStatusModel->closingStockTotal($from_date, $to_date);

    // ---------- category id map ----------
    $fyId = (int)($this->session->get('ses_comp_fy_id') ?? $this->fy_id ?? 0);
    $CAT_L_OWNER_FUND   = 1;
    $CAT_L_NON_CURRENT  = 2;
    $CAT_A_NON_CURRENT  = 3;
    $CAT_L_CURRENT      = 4;
    $CAT_A_CURRENT      = 5;

    $LIAB_CATS  = [$CAT_L_OWNER_FUND, $CAT_L_NON_CURRENT, $CAT_L_CURRENT];
    $ASSET_CATS = [$CAT_A_NON_CURRENT, $CAT_A_CURRENT];
    $CAT_ALL    = array_merge($LIAB_CATS, $ASSET_CATS);

    // ---------- GROUP hierarchy (type=2) only under balance-sheet parents ----------
    $gRows = $this->db->table('accgrpmstn g')
        ->select('g.acc_grp_id, g.acc_grp_name, u.crs_mst_is_primary, u.under_crs_mst_id, u.crs_mst_parent_id')
        ->join("undercrsmt u", "u.crs_mst_id = g.acc_grp_id AND u.crs_mst_type = 2 AND u.cmp_id = {$this->company_id}", 'left')
        ->where('u.cmpfymastr_id', $fyId)
        ->where('u.cmpfymastr_id IS NOT NULL', null, false)
        ->where('g.cmp_id', $this->company_id)
        ->whereIn('u.crs_mst_parent_id', $CAT_ALL)
        ->orderBy('g.acc_grp_name','asc')
        ->get()->getResultArray();

    $groupInfo          = [];
    $children           = [];
    $primaryByParentCat = [];
    $allGroupIds        = [];

    foreach ($gRows as $r) {
        $gid        = (int)$r['acc_grp_id'];
        $name       = $r['acc_grp_name'];
        $isPrimary  = (int)$r['crs_mst_is_primary'];
        $parentGid  = (int)($r['under_crs_mst_id'] ?? 0);
        $parentCat  = (int)($r['crs_mst_parent_id'] ?? 0);
        $allGroupIds[$gid] = true;

        $groupInfo[$gid] = [
            'name'       => $name,
            'is_primary' => $isPrimary,
            'parent_gid' => $parentGid,
            'parent_cat' => $parentCat,
        ];
        if ($parentGid > 0) {
            if (!isset($children[$parentGid])) $children[$parentGid] = [];
            $children[$parentGid][] = $gid;
        }
        if ($isPrimary === 1 && $parentCat > 0) {
            if (!isset($primaryByParentCat[$parentCat])) $primaryByParentCat[$parentCat] = [];
            $primaryByParentCat[$parentCat][] = $gid;
        }
    }

    $validGroupIds = array_keys($allGroupIds);
    if (empty($validGroupIds)) $validGroupIds = [0];

    // ---------- ACCOUNTS mappings (type=1 + type=14 (Bill Sundry)) ----------
    $accRows1 = $this->db->table('acctmaster a')
        ->select('a.acc_id, a.acc_name, a.bsd_id, ua.under_crs_mst_id, ua.crs_mst_parent_id')
        ->join("undercrsmt ua", "ua.crs_mst_id = a.acc_id AND ua.crs_mst_type = 1 AND ua.cmp_id = {$this->company_id}", 'left')
        ->where("
            ( ua.under_crs_mst_id IN (" . implode(',', $validGroupIds) . ")
           OR (ua.under_crs_mst_id = 0 AND ua.crs_mst_parent_id IN (" . implode(',', $CAT_ALL) . ")) )
        ", null, false)
        ->where('ua.cmpfymastr_id', $fyId)
        ->where('ua.cmpfymastr_id IS NOT NULL', null, false)
        ->where('a.cmp_id', $this->company_id)
        ->orderBy('a.acc_name', 'ASC')
        ->get()->getResultArray();

    $accRowsBS = $this->db->table('acctmaster a')
        ->select('a.acc_id, a.acc_name, a.bsd_id, ub.under_crs_mst_id, ub.crs_mst_parent_id')
        ->join("undercrsmt ub", "ub.crs_mst_id = a.acc_id AND ub.crs_mst_type = 14 AND ub.cmp_id = {$this->company_id}", 'left')
        ->where("
            ( ub.under_crs_mst_id IN (" . implode(',', $validGroupIds) . ")
           OR (ub.under_crs_mst_id = 0 AND ub.crs_mst_parent_id IN (" . implode(',', $CAT_ALL) . ")) )
        ", null, false)
        ->where('ub.cmpfymastr_id', $fyId)
        ->where('ub.cmpfymastr_id IS NOT NULL', null, false)
        ->where('a.cmp_id', $this->company_id)
        ->where('a.bsd_id IS NOT NULL', null, false)
        ->orderBy('a.acc_name', 'ASC')
        ->get()->getResultArray();

    // Merge preferring Bill Sundry mapping (type=14)
    $accIndex = [];
    foreach ($accRows1 as $row) {
        $accIndex[(int)$row['acc_id']] = $row + ['_via_bs' => 0];
    }
    foreach ($accRowsBS as $row) {
        $accIndex[(int)$row['acc_id']] = $row + ['_via_bs' => 1];
    }
    $accRows = array_values($accIndex);

    $accountsByGroup = [];
    $primaryAccByCat = [];
    $accNameById     = [];
    $allAccIds       = [];

    foreach ($accRows as $a) {
        $aid    = (int)$a['acc_id'];
        $under  = (int)$a['under_crs_mst_id'];
        $pcat   = (int)$a['crs_mst_parent_id'];
        $isBSD  = !empty($a['bsd_id']);

        $accNameById[$aid] = $a['acc_name'];
        $allAccIds[] = $aid;

        if ($under === 0) {
            if (!isset($primaryAccByCat[$pcat])) $primaryAccByCat[$pcat] = [];
            $primaryAccByCat[$pcat][] = ['id'=>$aid, 'name'=>$a['acc_name'], 'is_bsd'=>$isBSD];
        } else {
            if (!isset($accountsByGroup[$under])) $accountsByGroup[$under] = [];
            $accountsByGroup[$under][] = $aid;
        }
    }

    // ---------- OPENING + TXNs → closing per account (+DR, −CR) ----------
    $opByAcc = [];
    if (!empty($allAccIds)) {
        $opQB = $this->db->table('accoppybal ob')
            ->select('ob.acc_id, SUM(ob.acc_op_bal) AS op_bal', false)
            ->where('ob.cmp_id', $this->company_id)
            ->where('ob.cmpfymastr_id', $fyId)
            ->whereIn('ob.acc_id', $allAccIds)
            ->groupBy('ob.acc_id');
        if ((int)$consolidated === 0 && !empty($this->bo_id)) { $opQB->where('ob.hobo_id', $this->bo_id); }
        foreach ($opQB->get()->getResultArray() as $r) {
            $opByAcc[(int)$r['acc_id']] = (float)($r['op_bal'] ?? 0);
        }
    }

    $txByAcc = [];
    if (!empty($allAccIds)) {
        $txQB = $this->db->table('accttxnmst a');
        $txQB->select("
            a.acc_id,
            SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END) AS dr,
            SUM(CASE WHEN a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END) AS cr
        ", false)
        ->join('vchtxnconso v', 'v.vch_txn_id = a.vch_txn_id', 'inner')
        ->whereIn('a.acc_txn_type', [1])
        ->where('a.cmp_id', $this->company_id)
        ->whereIn('a.acc_id', $allAccIds)
        ->where('a.vch_txn_id > 0', null, false)
        ->where('v.vch_date >=', $from_date)
        ->where('v.vch_date <=', $to_date)
        ->groupBy('a.acc_id');
        if ((int)$consolidated === 0 && !empty($this->bo_id)) { $txQB->where('a.hobo_id', $this->bo_id); }
        foreach ($txQB->get()->getResultArray() as $t) {
            $txByAcc[(int)$t['acc_id']] = [
                'dr' => (float)($t['dr'] ?? 0),
                'cr' => (float)($t['cr'] ?? 0),
            ];
        }
    }

    $closingByAcc = [];
    foreach ($allAccIds as $aid) {
        $op = $opByAcc[$aid] ?? 0.0;
        $dr = $txByAcc[$aid]['dr'] ?? 0.0;
        $cr = $txByAcc[$aid]['cr'] ?? 0.0;
        $closingByAcc[$aid] = $op + ($dr - $cr); // +DR (Asset), −CR (Liability)
    }

    // ---------- recursive group roll-up (include descendants) ----------
    $sumCache = [];
    $sumGroupClosings = function (int $gid) use (&$sumCache, $children, $accountsByGroup, $closingByAcc, &$sumGroupClosings): float {
        if (isset($sumCache[$gid])) return $sumCache[$gid];
        $sum = 0.0;
        if (!empty($accountsByGroup[$gid])) {
            foreach ($accountsByGroup[$gid] as $aid) $sum += $closingByAcc[$aid] ?? 0.0;
        }
        if (!empty($children[$gid])) {
            foreach ($children[$gid] as $cg) $sum += $sumGroupClosings($cg);
        }
        return $sumCache[$gid] = $sum;
    };

    // Collect descendant groups (for detailed listing)
    $descCache = [];
    $collectDesc = function (int $gid) use (&$descCache, $children, &$collectDesc): array {
        if (isset($descCache[$gid])) return $descCache[$gid];
        $out = [$gid];
        if (!empty($children[$gid])) {
            foreach ($children[$gid] as $cg) {
                $out = array_merge($out, $collectDesc($cg));
            }
        }
        return $descCache[$gid] = $out;
    };

    // Display transforms per sign rule
    // Liability side: DR -> negative, CR -> positive => display = -signed
    $displayLiab  = function (float $signed) { return -$signed; };
    // Asset side: DR -> positive, CR -> negative => display = signed
    $displayAsset = function (float $signed) { return $signed; };

    // ---------- CONDENSED ----------
    if ((int)$view === 0) {
        $final = [];

        $final[] = $hdrRow('LIABILITIES');

        $sumCategorySigned = function (int $catId) use ($primaryByParentCat, $primaryAccByCat, $sumGroupClosings, $closingByAcc): float {
            $tot = 0.0;
            foreach (($primaryByParentCat[$catId] ?? []) as $gid) $tot += $sumGroupClosings($gid);
            foreach (($primaryAccByCat[$catId] ?? []) as $pa)  $tot += ($closingByAcc[$pa['id']] ?? 0.0);
            return $tot;
        };

        $ownFund = $displayLiab($sumCategorySigned($CAT_L_OWNER_FUND));

        $plRows  = $this->legacy_load_profit_loss_horizontal(1, $from_date, $to_date, $nil_type, $consolidated);
        $plLast  = end($plRows);
        $np      = (float)($plLast['l_balance_total'] ?? 0.0);
        $nl      = (float)($plLast['r_balance_total'] ?? 0.0);
        $plSigned= $np - $nl;

        $ncl = $displayLiab($sumCategorySigned($CAT_L_NON_CURRENT));
        $cl  = $displayLiab($sumCategorySigned($CAT_L_CURRENT));

        if (!((int)$nil_type === 0 && $ownFund == 0)) $final[] = $makeRow("Owner's Fund", $ownFund, 'cat', $CAT_L_OWNER_FUND, 'font-weight:bold;');
        if (!((int)$nil_type === 0 && $plSigned == 0)) $final[] = $makeRow('Profit / Loss', $plSigned, 'pl', 0, 'font-weight:bold;');
        if (!((int)$nil_type === 0 && $ncl == 0))      $final[] = $makeRow('Non Current Liabilities', $ncl, 'cat', $CAT_L_NON_CURRENT, 'font-weight:bold;');
        if (!((int)$nil_type === 0 && $cl == 0))       $final[] = $makeRow('Current Liabilities', $cl, 'cat', $CAT_L_CURRENT, 'font-weight:bold;');

        $liabTotal = $ownFund + $plSigned + $ncl + $cl;
        $final[] = [
            'group_id'=>0,'group_name'=>'TOTAL LIABILITIES','type'=>'ttl',
            'balance'=>$fmt($liabTotal),
            'balance_total'=> 0, // Not part of final sum
            'pq_rowattr'=>['style'=>'background:#E6E6FA;font-weight:bold;']
        ];

        $final[] = $hdrRow('ASSETS');

        $nca = $displayAsset($sumCategorySigned($CAT_A_NON_CURRENT));
        $ca  = $displayAsset($sumCategorySigned($CAT_A_CURRENT)) + $inventoryVal;

        if (!((int)$nil_type === 0 && $nca == 0))    $final[] = $makeRow('Non Current Assets', $nca, 'cat', $CAT_A_NON_CURRENT, 'font-weight:bold;');
        if (!((int)$nil_type === 0 && $ca == 0))     $final[] = $makeRow('Current Assets', $ca, 'cat', $CAT_A_CURRENT, 'font-weight:bold;');

        $assetTotal = $nca + $ca;
        $final[] = [
            'group_id'=>0,'group_name'=>'TOTAL ASSETS','type'=>'ttl',
            'balance'=>$fmt($assetTotal),
            'balance_total'=> 0, // Not part of final sum
            'pq_rowattr'=>['style'=>'background:#E6E6FA;font-weight:bold;']
        ];

        $diffOp = (float)$this->calculateDiffInOpBalance($from_date, $to_date, $consolidated);
        if (abs($diffOp) > 0.01) {
            $final[] = $makeRow('Difference in Opening', $diffOp, 'opn', 0, 'font-weight:bold;', false);
        }

        return $final;
    }

    // ---------- SCHEDULES & DETAILED ----------
    $liabilities_rows = [];
    $assets_rows      = [];

    $emitCatBlock = function (int $catId, bool $isLiability) use (
        $view, $nil_type, $fmt, $makeRow, $groupInfo, $primaryByParentCat,
        $accountsByGroup, $closingByAcc, $sumGroupClosings, $collectDesc,
        $displayLiab, $displayAsset, $inventoryVal, $primaryAccByCat, $accNameById
    ) {
        $rows = [];
        $catName = [
            1 => "Owner's Fund", 2 => 'Non Current Liabilities', 3 => 'Non Current Assets',
            4 => 'Current Liabilities', 5 => 'Current Assets'
        ][$catId] ?? '';

        // Heading (display only, not part of totals)
        $rows[] = $makeRow($catName, 0.0, 'cat', $catId, 'font-weight:bold;', false);

        // Primary groups under this category (rolled-up totals)
        foreach (($primaryByParentCat[$catId] ?? []) as $gid) {
            $gName  = $groupInfo[$gid]['name'] ?? '';
            $signed = $sumGroupClosings($gid);
            $disp   = $isLiability ? $displayLiab($signed) : $displayAsset($signed);
            if ((int)$nil_type === 0 && round($disp, 2) == 0.0) continue;

            $rows[] = $makeRow('&nbsp;&nbsp;» ' . $gName, $disp, 'grp', $gid, ($view==2)?'font-weight:bold;':'', true);

            // Detailed: list ALL descendant accounts for this primary group
            if ((int)$view === 2) {
                $allDesc = $collectDesc($gid);
                $accIds  = [];
                foreach ($allDesc as $dgid) {
                    if (!empty($accountsByGroup[$dgid])) {
                        foreach ($accountsByGroup[$dgid] as $aid) $accIds[] = $aid;
                    }
                }
                if (!empty($accIds)) {
                    $nameMap = [];
                    foreach ($accIds as $aid) $nameMap[$aid] = $accNameById[$aid] ?? ('Acc#'.$aid);
                    asort($nameMap, SORT_NATURAL|SORT_FLAG_CASE);

                    foreach ($nameMap as $aid => $nm) {
                        $signedAcc = $closingByAcc[$aid] ?? 0.0;
                        $aDisp     = $isLiability ? $displayLiab($signedAcc) : $displayAsset($signedAcc);
                        if ((int)$nil_type === 0 && round($aDisp, 2) == 0.0) continue;
                        // Detailed rows don't contribute to the main total directly, only their parent group does.
                        $rows[] = $makeRow('&nbsp;&nbsp;&nbsp;&nbsp;»» ' . $nm, $aDisp, 'acc', (int)$aid, '', false);
                    }
                }
            }
        }

        // Primary ACCOUNTS (under_crs_mst_id = 0) for the category
        foreach (($primaryAccByCat[$catId] ?? []) as $pa) {
            $aid   = (int)$pa['id'];
            $signed = $closingByAcc[$aid] ?? 0.0;
            $disp   = $isLiability ? $displayLiab($signed) : $displayAsset($signed);
            if ((int)$nil_type === 0 && round($disp, 2) == 0.0) continue;

            $suffix = !empty($pa['is_bsd']) ? ' <sub><em>(Bill Sundry)</em></sub>' : ' <sub><em>(Primary Account)</em></sub>';
            $rows[] = $makeRow('&nbsp;&nbsp;» ' . $pa['name'] . $suffix, $disp, 'acc', $aid, '', true);
        }

        // Inventories row under Current Assets
        if (!$isLiability && $catId === 5) {
            if (!((int)$nil_type === 0 && round($inventoryVal, 2) == 0.0)) {
                $rows[] = $makeRow('&nbsp;&nbsp;» Inventories', $displayAsset($inventoryVal), 'inv', 0, '', true);
            }
        }
        return $rows;
    };

    // ===== LIABILITIES =====
    $liabilities_rows[] = $hdrRow('LIABILITIES');

    $liabilities_rows = array_merge($liabilities_rows, $emitCatBlock($CAT_L_OWNER_FUND, true));

    // Profit / Loss row
    $plRows  =  $this->legacy_load_profit_loss_horizontal(1, $from_date, $to_date, $nil_type, $consolidated);
    $plSigned = 0.0;
    if (is_array($plRows) && !empty($plRows)) {
        $plLast  =  end($plRows);
        $np      = (float)($plLast['l_balance_total'] ?? 0.0);
        $nl      = (float)($plLast['r_balance_total'] ?? 0.0);
        $plSigned = $np - $nl;
    }
    if (!((int)$nil_type === 0 && round($plSigned, 2) == 0.0)) {
        $liabilities_rows[] = $makeRow('Profit / Loss', $plSigned, 'pl', 0, 'font-weight:bold;', true);
    }

    $liabilities_rows = array_merge($liabilities_rows, $emitCatBlock($CAT_L_NON_CURRENT, true));
    $liabilities_rows = array_merge($liabilities_rows, $emitCatBlock($CAT_L_CURRENT, true));

    $liabTotal = array_sum(array_column($liabilities_rows, 'balance_total'));
    $liabilities_rows[] = [
        'group_id'=>0, 'group_name'=>'TOTAL LIABILITIES', 'type'=>'ttl', 'balance'=>$fmt($liabTotal), 'balance_total'=>0,
        'pq_rowattr'=>['style'=>'background:#E6E6FA;font-weight:bold;']
    ];

    // ===== ASSETS =====
    $assets_rows[] = $hdrRow('ASSETS');

    $assets_rows = array_merge($assets_rows, $emitCatBlock($CAT_A_NON_CURRENT, false));
    $assets_rows = array_merge($assets_rows, $emitCatBlock($CAT_A_CURRENT, false));

    $assetTotal = array_sum(array_column($assets_rows, 'balance_total'));
    $assets_rows[] = [
        'group_id'=>0, 'group_name'=>'TOTAL ASSETS', 'type'=>'ttl', 'balance'=>$fmt($assetTotal), 'balance_total'=>0,
        'pq_rowattr'=>['style'=>'background:#E6E6FA;font-weight:bold;']
    ];

    // ===== MERGE and FINAL =====
    $final = array_merge($liabilities_rows, $assets_rows);

    $diffOp = (float)$this->calculateDiffInOpBalance($from_date, $to_date, $consolidated);
    if (abs($diffOp) > 0.01) {
        $final[] = $makeRow('Difference in Opening', $diffOp, 'opn', 0, 'font-weight:bold;', false);
    }

    return $final;
}

/**
 * Opening Stock Total (matches StockStatusModel opening logic)
 *
 * Session input:
 *   - ses_dflt_val_method can be 'AUTO'|'FIFO'|'LIFO'|'AVG' OR 1|2|3
 *
 * Behavior:
 *   - If opening_date_ymd == FY start date:
 *       returns SUM(itmoppyval.itm_op_val_amt) scoped by cmp_id + fy_id + hobo_id
 *       (same source as StockStatusModel::loadOpenings → inventoryStatusPaged opening)
 *     By default it does NOT filter by valuation method (to avoid partial totals like 37,08,761.51).
 *     If you want to filter by method, pass $filters['force_method_filter']=true.
 *
 *   - Else:
 *       opening = closingStockTotal(opening_date - 1)
 */
public function openingStockTotal($opening_date_ymd, $cmp_id, $cmpfymasr_id, $itm_val_method_id, array $filters = [],$consolidated=0)
{
    $opening_date_ymd = date('Y-m-d', strtotime($opening_date_ymd));
    $fy_start_ymd     = $filters['fy_start_ymd'] ?? $this->getFyStartDateFromMaster($cmpfymasr_id);
    if (!$fy_start_ymd) {                       // universal DB not answering: fall back to the session FY
        $fy_start_ymd = $this->session->get('ses_company_fy_beginning');
    }
    if ($fy_start_ymd) {                        // the column may carry a time part; compare dates only
        $fy_start_ymd = date('Y-m-d', strtotime((string)$fy_start_ymd));
    }
    // One consolidated flag for BOTH branches below (callers used either the 5th or the 6th argument).
    $consolidated = (int)($filters['consolidated'] ?? $consolidated);

    $normalizeMethod = function ($m) {
        if ($m === null || $m === '') return null;
        if (is_numeric($m)) return (int)$m;
        $m = strtoupper(trim((string)$m));
        if ($m === 'AUTO') return 'AUTO';
        if ($m === 'FIFO') return 1;
        if ($m === 'LIFO') return 2;
        if ($m === 'AVG')  return 3;
        return null;
    };

    $methodNormalized = $normalizeMethod($itm_val_method_id);

    if ($fy_start_ymd && $opening_date_ymd === $fy_start_ymd) {

        $qb = $this->db->table('itmoppyval v')
            ->select('COALESCE(SUM(v.itm_op_val_amt),0) AS total', false)
            ->where('v.cmp_id', (int)$cmp_id)
            ->where('v.cmpfymastr_id', (int)$cmpfymasr_id);

        // ✅ FIX: only filter by hobo_id when NOT consolidated
        $isConsolidated = $consolidated;
        if ($isConsolidated === 0) {
            $hoboId = $filters['hobo_id'] ?? ($this->bo_id ?? null);
            if (!empty($hoboId)) {
                $qb->where('v.hobo_id', (int)$hoboId);
            }
        }
        // when consolidated=1 → no hobo_id filter → SUM across ALL branches ✅

        if (!empty($filters['mat_cent_id'])) {
            $qb->where('v.mat_cent_id', (int)$filters['mat_cent_id']);
        }
        if (!empty($filters['item_ids']) && is_array($filters['item_ids'])) {
            $qb->whereIn('v.itm_id_unit_id', $filters['item_ids']);
        }
        if (!empty($filters['force_method_filter']) && $methodNormalized !== null && $methodNormalized !== 'AUTO') {
            $qb->where('v.itm_val_method_id', $methodNormalized);
        }

        $row   = $qb->get()->getRowArray();
        $total = (float)($row['total'] ?? 0.0);
        return function_exists('parseAmount') ? parseAmount($total) : $total;
    }

    // Opening stock of a date after the FY start = closing stock at the end of the PREVIOUS day.
    // (closingStockTotal() values stock as at its SECOND argument; the arguments used to be swapped,
    //  which returned the stock at the end of the opening date itself.)
    $yesterday = date('Y-m-d', strtotime($opening_date_ymd . ' -1 day'));
    return $this->StockStatusModel->closingStockTotal($opening_date_ymd, $yesterday, ['consolidated' => $consolidated]);
} 
public function openingStockTotal_11_04_2026($opening_date_ymd, $cmp_id, $cmpfymasr_id, $itm_val_method_id, array $filters = [])
{
    // Normalize date
    $opening_date_ymd = date('Y-m-d', strtotime($opening_date_ymd));

    // FY start date
    $fy_start_ymd = $filters['fy_start_ymd'] ?? $this->getFyStartDateFromMaster($cmpfymasr_id);

    // Helper: normalize method coming from session or caller
    $normalizeMethod = function ($m) {
        if ($m === null || $m === '') return null;

        // Numeric mapping (common in DB): 1=FIFO, 2=LIFO, 3=AVG
        if (is_numeric($m)) {
            return (int)$m; // keep 1/2/3
        }

        $m = strtoupper(trim((string)$m));
        if ($m === 'AUTO') return 'AUTO';
        if ($m === 'FIFO') return 1;
        if ($m === 'LIFO') return 2;
        if ($m === 'AVG')  return 3;

        // Unknown string => don't filter
        return null;
    };

    $methodNormalized = $normalizeMethod($itm_val_method_id);

    // If FY start date: read from itmoppyval (same as StockStatusModel openings)
    if ($fy_start_ymd && $opening_date_ymd === $fy_start_ymd) {

        $qb = $this->db->table('itmoppyval v')
            ->select('COALESCE(SUM(v.itm_op_val_amt),0) AS total', false)
            ->where('v.cmp_id', (int)$cmp_id)
            ->where('v.cmpfymastr_id', (int)$cmpfymasr_id);

        // StockStatusModel scoping: always use BO (hobo_id)
        $hoboId = $filters['hobo_id'] ?? ($this->bo_id ?? null);
        if (!empty($hoboId)) {
            $qb->where('v.hobo_id', (int)$hoboId);
        }

        // Optional MC filter (StockStatusModel sums across ALL MCs by default)
        if (!empty($filters['mat_cent_id'])) {
            $qb->where('v.mat_cent_id', (int)$filters['mat_cent_id']);
        }

        // Optional item filter
        if (!empty($filters['item_ids']) && is_array($filters['item_ids'])) {
            $qb->whereIn('v.itm_id_unit_id', $filters['item_ids']);
        }

        /**
         * OPTIONAL method filter:
         * Only enable if your itmoppyval is stored method-wise.
         *
         * IMPORTANT:
         * - If ses_dflt_val_method == 'AUTO', do NOT filter here (AUTO means per-item method).
         * - If normalized method is null/unknown, do NOT filter.
         */
        if (!empty($filters['force_method_filter']) && $methodNormalized !== null && $methodNormalized !== 'AUTO') {
            $qb->where('v.itm_val_method_id', $methodNormalized);
        }

        $row = $qb->get()->getRowArray();
        $total = (float)($row['total'] ?? 0.0);

        return function_exists('parseAmount') ? parseAmount($total) : $total;
    }

    // Otherwise opening = closing as-of previous day
    $yesterday = date('Y-m-d', strtotime($opening_date_ymd . ' -1 day'));
    return $this->StockStatusModel->closingStockTotal($yesterday, $opening_date_ymd);
}



public function closingStockTotal_moved_to_stockstatusmodel($from_date, $to_date, $filters = [])
{
    $mysql = $this->db;

    // Context
    $cmpId = $this->company_id;  
    $boId  = $this->bo_id;  
    $fyId  = $this->fy_id;

    // Parse dates
    $asOfStr = date('Y-m-d', strtotime($to_date));
    $fyStart = date('Y-m-d', strtotime($from_date));
    
    // Valuation method - default to AVG if not specified
    $valReq = $this->session->get('ses_dflt_val_method') ?? 'AVG';
    if (!in_array($valReq, ['AUTO','FIFO','LIFO','AVG'], true)) {
        $valReq = 'AVG';
    }
    
    // Optional filters from $filters array
    $itemId = $filters['item_id'] ?? '';
    $unitId = $filters['unit_id'] ?? '';
    $mcId   = $filters['mc_id'] ?? '';

    // Get all items in the system
    $allKeys = $this->findItemKeysForReport($mysql, $cmpId, $boId, $fyId, $fyStart, $asOfStr, $itemId, $unitId, $mcId);
    
    if (empty($allKeys)) {
        return 0.0; // No items found
    }

    // Get opening balances
    [$openQtyMap, $openValMap] = $this->loadOpenings($mysql, $cmpId, $boId, $fyId, $allKeys);

    // Method resolution for AUTO
    $methodMap = $this->loadItemMethodMap($mysql, $cmpId, $boId, $fyId);
    $resolveMethod = function(string $key) use ($valReq, $methodMap): string {
        if ($valReq !== 'AUTO') return $valReq;
        $m = $methodMap[$key] ?? 'AVG';
        if (is_numeric($m)) {
            return match ((int)$m) { 1 => 'FIFO', 2 => 'LIFO', 3 => 'AVG', default => 'AVG' };
        }
        return strtoupper($m);
    };

    // Initialize grand total
    $grand_cl_value = 0.0;

    // Process each item
    foreach ($allKeys as $key) {
        $method = $resolveMethod($key);
        
        // Get opening balance for this item
        $opQty  = (float)($openQtyMap[$key] ?? 0.0);
        $opVal  = (float)($openValMap[$key] ?? 0.0);
        $opRate = ($opQty > 0) ? ($opVal / $opQty) : 0.0;

        // Initialize running totals with opening
        $running_qty = $opQty;
        $running_value = $opVal;
        $running_avg = $opRate;

        // Get all transactions for this item in the period
        $txns = $this->fetchTxnsWithVoucherInfo($mysql, $cmpId, $boId, $key, $fyStart, $asOfStr, $mcId);
        
        // Group transactions by voucher to detect Stock Journal entries
        $voucher_groups = [];
        foreach ($txns as $t) {
            $vch_id = $t['vch_txn_id'];
            if (!isset($voucher_groups[$vch_id])) {
                $voucher_groups[$vch_id] = [
                    'voucher_type' => $t['voucher_type'] ?? '',
                    'vch_type_id' => $t['vch_type_id'] ?? '',
                    'date' => $t['itm_txn_date'],
                    'in_qty' => 0,
                    'in_amt' => 0,
                    'out_qty' => 0,
                    'out_amt' => 0
                ];
            }
            
            if ((int)$t['itm_txn_dr_cr'] === 1) {
                $voucher_groups[$vch_id]['in_qty'] += (float)$t['itm_txn_qty'];
                $voucher_groups[$vch_id]['in_amt'] += (float)$t['itm_txn_amt'];
            } else {
                $voucher_groups[$vch_id]['out_qty'] += (float)$t['itm_txn_qty'];
                $voucher_groups[$vch_id]['out_amt'] += (float)$t['itm_txn_amt'];
            }
        }
        
        // Process each voucher group
        foreach ($voucher_groups as $vch_id => $vg) {
            $is_stock_journal = (strtolower($vg['voucher_type']) == 'stock journal' || $vg['vch_type_id'] == '20');
            
            if ($is_stock_journal && $vg['in_qty'] > 0 && $vg['out_qty'] > 0) {
                // Stock Journal logic from inventoryStatusPaged
                $cogs = $vg['out_qty'] * $running_avg;
                
                // For stock journals, the profit/loss is the difference between
                // the value of the outgoing item and the value of the incoming item.
                // However, we just need to adjust the running value correctly.
                
                // Remove old stock value
                $running_qty -= $vg['out_qty'];
                $running_value -= $cogs;
                
                // Add new stock value
                $running_qty += $vg['in_qty'];
                $running_value += $vg['in_amt'];
                
                // Recalculate average
                if ($running_qty > 0) {
                    $running_avg = $running_value / $running_qty;
                } else {
                    $running_avg = 0; // Avoid division by zero
                }
                
            } else {
                // Normal transactions
                if ($vg['out_qty'] > 0) {
                    $cogs = $vg['out_qty'] * $running_avg;
                    $running_qty -= $vg['out_qty'];
                    $running_value -= $cogs;
                }
                
                if ($vg['in_qty'] > 0) {
                    $new_total_value = $running_value + $vg['in_amt'];
                    $new_total_qty = $running_qty + $vg['in_qty'];
                    
                    if ($new_total_qty > 0) {
                        $running_avg = $new_total_value / $new_total_qty;
                    } else {
                         $running_avg = 0;
                    }
                    
                    $running_qty = $new_total_qty;
                    $running_value = $new_total_value;
                }
            }
        }
        
        // Add this item's closing value to grand total
        if ($running_qty > 0 && $running_value > 0) {
            $grand_cl_value += $running_value;
        }
    }

    return (float)$grand_cl_value;
}
private function fetchTxnsWithVoucherInfo($mysql, int $cmpId, int $boId, string $itmKey, 
                                          string $fromDate, string $toDate, $mcId): array
{
    $builder = $mysql->table('itemtxnmst t')
        ->select('
            t.itm_txn_date, 
            t.itm_txn_dr_cr, 
            t.itm_txn_qty, 
            t.itm_txn_rate, 
            t.itm_txn_amt, 
            t.itm_txn_id, 
            t.vch_txn_id,
            c.vch_type_id, 
            vt.vch_name as voucher_type
        ')
        ->join('vchtxnconso c', 'c.vch_txn_id = t.vch_txn_id', 'left')
        ->join('vchtypemst vt', 'vt.vch_type_id = c.vch_type_id', 'left')
        ->where('t.cmp_id', $cmpId)
        ->where('t.hobo_id', $boId)
        ->where('t.itm_id_unit_id', $itmKey)
        ->where('t.itm_txn_date >=', $fromDate)
        ->where('t.itm_txn_date <=', $toDate);
    
    if (!empty($mcId)) {
        $builder->where('t.mat_cent_id', $mcId);
    }
    
    $builder->orderBy('t.itm_txn_date', 'ASC')
            ->orderBy('t.vch_txn_id', 'ASC')
            ->orderBy('t.itm_txn_id', 'ASC');
    
    return $builder->get()->getResultArray();
}


public function closingStockTotaldddd($from_date,$to_date){
		$mysql = $this->db;
		$pg    = $this->externaldb->postgr_db();

		// === context (use the same way you set these elsewhere) ===
		$cmpId = $this->company_id;  $boId = $this->bo_id;  $fyId = $this->fy_id;

		// === inputs ===
		$asOfStr   = date('Y-m-d',strtotime($to_date));//$this->request->getVar('as_of');   // YYYY-MM-DD (e.g., FY end)
		$valReq    = $this->session->get('ses_dflt_val_method'); // AUTO|FIFO|LIFO|AVG
		$allowNeg  = 1;
		$itemId    = ''; // optional
		$unitId    = ''; // optional
		$mcId      = '';   // optional (branch/store/MC filter)
		//if (!$asOfStr) return $this->response->setJSON(['ok'=>false,'error'=>'as_of required']);

		$asOf    = new \DateTimeImmutable($asOfStr);
		
	    $fyStart = date('Y-m-d',strtotime($from_date));//$this->fyStartFor($asOf)->format('Y-m-d');

		// Per-item default method map for AUTO
		$methodMap = $this->loadItemMethodMap($mysql, $cmpId, $boId, $fyId);
		$resolveMethod = function(string $key) use ($valReq, $methodMap): string {
			if ($valReq !== 'AUTO') return $valReq;
			$m = $methodMap[$key] ?? 3; return match ($m) {1=>'FIFO',2=>'LIFO',3=>'AVG',default=>'AVG'};
		};

		// Universe of items considered for closing stock (apply optional filters)
		$allKeys = $this->findItemKeysForReport($mysql, $cmpId, $boId, $fyId, $fyStart, $asOfStr, $itemId, $unitId, $mcId);

		// Openings for all keys
		[$openQtyAll, $openValAll] = $this->loadOpenings($mysql, $cmpId, $boId, $fyId, $allKeys);

		// Helpers shared with report
		$ballsVal = fn(array $balls): float => array_reduce($balls, fn($v,$b)=>$v + ($b['qty']*$b['cost']), 0.0);
		$pushBall = function(array &$val_balls, string $key, float $qty, float $cost): void {
			if (!isset($val_balls[$key])) $val_balls[$key] = [];
			if ($qty == 0.0) return;
			$n = count($val_balls[$key]);
			if ($n>0 && abs($val_balls[$key][$n-1]['cost'] - $cost) < 1e-10) $val_balls[$key][$n-1]['qty'] += $qty;
			else $val_balls[$key][] = ['qty'=>$qty,'cost'=>$cost];
			while (!empty($val_balls[$key]) && abs($val_balls[$key][0]['qty']) <= 1e-12) array_shift($val_balls[$key]);
			while (!empty($val_balls[$key]) && abs($val_balls[$key][count($val_balls[$key])-1]['qty']) <= 1e-12) array_pop($val_balls[$key]);
		};
		$consumeBalls = function(array &$val_balls, string $key, float $qty, string $method, bool $allowNeg): float {
			$value=0.0; $need=$qty;
			if (!isset($val_balls[$key])) $val_balls[$key]=[];
			if ($method==='FIFO') {
				while ($need>1e-12 && !empty($val_balls[$key])) {
					$take=min($need,$val_balls[$key][0]['qty']);
					if ($take>0){ $value+=$take*$val_balls[$key][0]['cost']; $val_balls[$key][0]['qty']-=$take; $need-=$take; }
					if ($val_balls[$key][0]['qty']<=1e-12) array_shift($val_balls[$key]);
				}
			} else {
				while ($need>1e-12 && !empty($val_balls[$key])) {
					$i=count($val_balls[$key])-1; $take=min($need,$val_balls[$key][$i]['qty']);
					if ($take>0){ $value+=$take*$val_balls[$key][$i]['cost']; $val_balls[$key][$i]['qty']-=$take; $need-=$take; }
					if ($val_balls[$key][$i]['qty']<=1e-12) array_pop($val_balls[$key]);
				}
			}
			if ($need>1e-12) { if(!$allowNeg) throw new \RuntimeException('Insufficient stock'); $val_balls[$key][]=['qty'=>-$need,'cost'=>0.0]; }
			return $value;
		};
		$avgInit = fn(array &$avgState, string $key, float $qty, float $avg) => $avgState[$key]=['qty'=>$qty,'avg'=>$avg];
		$avgIn   = function(array &$avgState, string $key, float $inQty, float $inRate): void {
			$q0=(float)($avgState[$key]['qty']??0.0); $a0=(float)($avgState[$key]['avg']??0.0);
			$val=$q0*$a0 + $inQty*$inRate; $q1=$q0+$inQty; $avgState[$key]=['qty'=>$q1,'avg'=>$q1>0?$val/$q1:0.0];
		};
		$avgOut  = function(array &$avgState, string $key, float $outQty, bool $allowNeg): float {
			$q0=(float)($avgState[$key]['qty']??0.0); $a0=(float)($avgState[$key]['avg']??0.0);
			$issue=$outQty*$a0; $q1=$q0-$outQty; if ($q1<-1e-12 && !$allowNeg) throw new \RuntimeException('Insufficient AVG');
			$avgState[$key]=['qty'=>$q1,'avg'=>$a0]; return $issue;
		};

		// === compute grand closing value ===
		$grand_cl_value = 0.0;

		foreach ($allKeys as $key) {
			$method = $resolveMethod($key);

			// seed opening
			$opQty = (float)($openQtyAll[$key] ?? 0.0);
			$opVal = (float)($openValAll[$key] ?? 0.0);
			$opRate = ($opQty>0 && $opVal>0) ? $opVal/$opQty : 0.0;

			$val_balls=[]; $avgState=[];
			if ($opQty!=0.0) { if ($method==='AVG') $avgInit($avgState,$key,$opQty,$opRate); else $pushBall($val_balls,$key,$opQty,$opRate); }

			// snapshot seed within FY, ignore dirty
			[$seedDate, $seedPayload] = $this->selectSeedWithinFY($pg, $cmpId, $key, $method, $fyStart, $asOfStr);

			$txFrom = $fyStart;
			if ($seedDate && $seedPayload) {
				if ($method==='AVG') $avgInit($avgState,$key,(float)($seedPayload['qty']??0),(float)($seedPayload['avg_rate']??0));
				else $val_balls[$key] = array_map(fn($b)=>['qty'=>(float)$b['qty'],'cost'=>(float)$b['rate']], $seedPayload['balls'] ?? []);
				$txFrom = (new \DateTimeImmutable($seedDate))->modify('+1 day')->format('Y-m-d');
			}

			// replay txns up to as-of (respect optional mc filter for P&L segmenting if you want)
			$txns = $this->fetchTxnsForReport($mysql, $cmpId, $boId, $key, $txFrom, $asOfStr, $mcId);

			foreach ($txns as $t) {
				$qty  = (float)$t['itm_txn_qty'];
				$amt  = (float)$t['itm_txn_amt'];
				$rate = ($qty!=0.0) ? $amt/$qty : (float)$t['itm_txn_rate'];

				if ((int)$t['itm_txn_dr_cr'] === 1) {
					if ($method==='AVG') $avgIn($avgState,$key,$qty,$rate); else $pushBall($val_balls,$key,$qty,$rate);
				} else {
					if ($method==='AVG') $avgOut($avgState,$key,$qty,$allowNeg); else $consumeBalls($val_balls,$key,$qty,$method,$allowNeg);
				}
			}

			// accumulate closing valuation
			if ($method==='AVG') {
				$q=(float)($avgState[$key]['qty'] ?? 0); $ar=(float)($avgState[$key]['avg'] ?? 0);
				$grand_cl_value += $q*$ar;
			} else {
				$balls = $val_balls[$key] ?? [];
				$grand_cl_value += $ballsVal($balls);
			}
		}

		return parseAmount($grand_cl_value);
	}   
	
	

private function getFyStartDateFromMaster($cmpfymasr_id)
{
    // Example; adapt to your actual table/columns.
	$row = $this->univaictly->table('cmpfymastr')->select('fy_beg_date')->where('cmpfymastr_id', (int)$cmpfymasr_id)->where('cmp_id', $this->company_id)->get()->getRowArray();
	return $row ? $row['fy_beg_date'] : null;
}

/**
 * Sums opening valuation from itmoppyval with the same scoping as P&L.
 * Filters by company, FY, valuation method, and (optionally) material center, branch (hobo), and item set.
 *
 * @return float
 */
private function sumOpeningValueFromTable($cmp_id, $cmpfymasr_id, $itm_val_method_id, array $filters)
{
   $qb = $this->db->table('itmoppyval v')
    ->selectSum('v.itm_op_val_amt', 'total')
    ->where('v.cmp_id', $cmp_id)
    ->where('v.cmpfymastr_id', $cmpfymasr_id);

    // If you always store per-method opening values, keep this filter.
    if (!empty($itm_val_method_id)) {
        $qb->where('v.itm_val_method_id',$itm_val_method_id);
    }

    if (!empty($filters['mat_cent_id'])) {
        $qb->where('v.mat_cent_id', (int)$filters['mat_cent_id']);
    }
    if (!empty($filters['hobo_id'])) {
        $qb->where('v.hobo_id', (int)$filters['hobo_id']);
    }
    if (!empty($filters['item_ids']) && is_array($filters['item_ids'])) {
        $qb->where_in('v.itm_id_unit_id', $filters['item_ids']);
    }

    $row = $qb->get()->getRowArray();
	
    return (float)($row['total'] ?? 0.0);
}


	private function loadOpenings($mysql, int $cmpId, int $boId, int $fyId, array $keys): array
{
    if (empty($keys)) return [[], []];

    // Sum opening QTY across ALL MCs for each itm_id_unit_id
    $qRows = $mysql->table('itmoppybal')
        ->select('itm_id_unit_id, COALESCE(SUM(itm_op_bal_qty),0) AS qty_sum')
        ->where('cmpfymastr_id', $fyId)
        ->where('cmp_id', $cmpId)
        ->where('hobo_id', $boId)
        ->whereIn('itm_id_unit_id', $keys)
        ->groupBy('itm_id_unit_id')
        ->get()->getResultArray();

    $qmap = [];
    foreach ($qRows as $r) {
        $qmap[$r['itm_id_unit_id']] = (float)$r['qty_sum'];
    }

    // Sum opening VALUE across ALL MCs for each itm_id_unit_id
    $vRows = $mysql->table('itmoppyval')
        ->select('itm_id_unit_id, COALESCE(SUM(itm_op_val_amt),0) AS val_sum')
        ->where('cmpfymastr_id', $fyId)
        ->where('cmp_id', $cmpId)
        ->where('hobo_id', $boId)
        ->whereIn('itm_id_unit_id', $keys)
        ->groupBy('itm_id_unit_id')
        ->get()->getResultArray();

    $vmap = [];
    foreach ($vRows as $r) {
        $vmap[$r['itm_id_unit_id']] = (float)$r['val_sum'];
    }

    return [$qmap, $vmap];
}
  
  /**
     * Choose the seed snapshot within FY, ignoring dirty ranges.
     * Returns [seedDate, payloadArray, sumPnLToSeed]
     */
	 private function methodId(string $method): string
{
    $m = strtoupper(trim($method));
    return in_array($m, ['AVG','FIFO','LIFO'], true) ? $m : 'AVG';
}
    private function selectSeedWithinFY($pg, int $cmpId, string $itmKey, string $method, string $fyStart, string $asOf): array
    {
      //  $mid = match($method){'FIFO'=>1,'LIFO'=>2,'AVG'=>3,default=>3};
        $mid = $this->methodId($method); 
        // first dirty date in window?
        $dirty = $pg->query(
            "SELECT MIN(itm_snapshot_date) AS first_dirty
               FROM itmsnapsht
              WHERE cmp_id=? AND itm_id_unit_id=? AND val_method_id=?
                AND itm_snapshot_date BETWEEN ? AND ? AND itm_snapshot_is_dirty=TRUE",
            [$cmpId, $itmKey, $mid, $fyStart, $asOf]
        )->getFirstRow('array');
        $firstDirty = $dirty && $dirty['first_dirty'] ? $dirty['first_dirty'] : null;

        // latest clean snapshot no later than min(asOf, firstDirty-1), and not before FY start
        $seedUpper = $asOf;
        if ($firstDirty) {
            $seedUpper = (new \DateTimeImmutable($firstDirty))->modify('-1 day')->format('Y-m-d');
            if ($seedUpper < $fyStart) $seedUpper = $fyStart;
        }

        $seed = null;
        if ($seedUpper >= $fyStart) {
            $seed = $pg->query(
                "SELECT itm_snapshot_date, itm_balls_snapshot
                   FROM itmsnapsht
                  WHERE cmp_id=? AND itm_id_unit_id=? AND val_method_id=?
                    AND itm_snapshot_is_dirty=FALSE
                    AND itm_snapshot_date BETWEEN ? AND ?
               ORDER BY itm_snapshot_date DESC
                  LIMIT 1",
                [$cmpId, $itmKey, $mid, $fyStart, $seedUpper]
            )->getFirstRow('array');
        }

        $seedDate   = $seed['itm_snapshot_date'] ?? null;
        $payloadArr = $seed ? json_decode($seed['itm_balls_snapshot'], true) : null;

        // sum P&L from FY start to seedDate (clean only)
        $sumPnL = 0.0;
        if ($seedDate) {
            $r = $pg->query(
                "SELECT COALESCE(SUM(itm_snapshot_pnl),0) AS s
                   FROM itmsnapsht
                  WHERE cmp_id=? AND itm_id_unit_id=? AND val_method_id=?
                    AND itm_snapshot_is_dirty=FALSE
                    AND itm_snapshot_date BETWEEN ? AND ?",
                [$cmpId, $itmKey, $mid, $fyStart, $seedDate]
            )->getFirstRow('array');
            $sumPnL = (float)($r['s'] ?? 0.0);
        }

        return [$seedDate, $payloadArr, $sumPnL];
    }
	
	private function fyStartFor(\DateTimeImmutable $dt): \DateTimeImmutable {
        $y = (int)$dt->format('Y'); $m = (int)$dt->format('n');
        $fyY = ($m>=4) ? $y : $y-1; return new \DateTimeImmutable("$fyY-04-01");
    }
	private function findItemKeysForReport($mysql, int $cmpId, int $boId, int $fyId,
                                           string $fromDate, string $toDate,
                                           $itemId, $unitId, $mcId): array
    {
        $keys = [];

        // From openings
        $op = $mysql->table('itmoppybal')
            ->select('itm_id_unit_id')
            ->where('cmp_id',$cmpId)->where('hobo_id',$boId)->where('cmpfymastr_id',$fyId)
            ->get()->getResultArray();
        foreach ($op as $r) $keys[$r['itm_id_unit_id']] = true;

        // From activity in FY window
        $b = $mysql->table('itemtxnmst')
		    ->distinct()
            ->select('itm_id_unit_id')
            ->where('cmp_id',$cmpId)->where('hobo_id',$boId)
            ->where('itm_txn_date >=', $fromDate)
            ->where('itm_txn_date <=', $toDate);
        if (!empty($itemId)) $b->where('SUBSTRING_INDEX(itm_id_unit_id, "_", 1)', $itemId);
        if (!empty($unitId)) $b->where('SUBSTRING_INDEX(itm_id_unit_id, "_", -1)', $unitId);
        if (!empty($mcId))   $b->where('mat_cent_id', $mcId);
        $tx = $b->get()->getResultArray();
        foreach ($tx as $r) $keys[$r['itm_id_unit_id']] = true;

        // If both item_id & unit_id supplied but no openings/txns, still include that key
        if (!empty($itemId) && !empty($unitId)) {
            $key = $itemId.'_'.$unitId;
            if (!isset($keys[$key])) $keys[$key] = true;
        }

        $arr = array_keys($keys);
        sort($arr, SORT_STRING); // stable ordering for paging
        return $arr;
    }
    private function fyEndFor(\DateTimeImmutable $dt): \DateTimeImmutable {
        return $this->fyStartFor($dt)->modify('+1 year -1 day');
    }
    private function fetchTxnsForReport($mysql, int $cmpId, int $boId, string $itmKey, string $fromDate, string $toDate, $mcId): array
    {
        $b = $mysql->table('itemtxnmst t')
            ->select('t.itm_txn_date, t.itm_txn_dr_cr, t.itm_txn_qty, t.itm_txn_rate, t.itm_txn_amt, t.itm_txn_id')
            ->where('t.cmp_id', $cmpId)
            ->where('t.hobo_id', $boId)
            ->where('t.itm_id_unit_id', $itmKey)
            ->where('t.itm_txn_date >=', $fromDate)
            ->where('t.itm_txn_date <=', $toDate)
            ->orderBy('t.itm_txn_date','ASC')
            ->orderBy('t.itm_txn_id','ASC');
        if (!empty($mcId)) $b->where('t.mat_cent_id', $mcId);
        return $b->get()->getResultArray();
    }
    private function loadItemMethodMap($mysql, int $cmpId, int $boId, int $fyId): array {
        $rows = $mysql->table('itmoppyval')
            ->select('itm_id_unit_id, itm_val_method_id')
            ->where('cmp_id',$cmpId)->where('hobo_id',$boId)->where('cmpfymastr_id',$fyId)
            ->get()->getResultArray();
        $m=[]; foreach($rows as $r){ $m[$r['itm_id_unit_id']] = ($r['itm_val_method_id'] ?? 'AVG'); }
        return $m;
    }
	

public function legacy_load_balance_sheet_horizontal($view, $from_date, $to_date, $nil_type, $consolidated)
{
    /*
     * Unified Balance Sheet (Condensed + Schedules + Detailed)
     *
     * VIEWS
     *  0: Condensed   (parents only + P/L + Diff in Opening)
     *  1: Schedules   (parent headings + primary groups + primary accounts under parent)
     *  2: Detailed    (Schedules + list all descendant accounts under each primary group in DETAIL)
     *
     * LEFT  (Liabilities & Equity): 1 = Owner's Fund, 2 = Non Current Liabilities, 4 = Current Liabilities
     * RIGHT (Assets)              : 3 = Non Current Assets, 5 = Current Assets
     *
     * Signs:
     *  - Account signed closing = Opening + (Dr − Cr); +ve = debit (assets), −ve = credit (liab/equity)
     *  - Display rule (per requirement): BS LEFT shows DR as − and CR as + (i.e., display = -signed);
     *    BS RIGHT shows DR as + and CR as − (i.e., display = signed).
     *  - "Profit / Loss" is shown on LEFT with its own sign (profit +ve, loss −ve), same as ERP1.0.
     *
     * Difference in Opening:
     *  - Only opening balances are considered (via calculateDiffInOpBalance).
     *  - No transaction (Dr/Cr) deltas are used to compute this row.
     */

    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date   = date('Y-m-d', strtotime($to_date));

    // Enforce current FY everywhere
    $fyId = (int)$this->fy_id;

    // Parents
    $LEFT_PARENTS  = [1, 2, 4];
    $RIGHT_PARENTS = [3, 5];
    $ALL_PARENTS   = array_unique(array_merge($LEFT_PARENTS, $RIGHT_PARENTS));
    $BS_PARENT_NAMES = [
        1 => "Owner's Fund",
        2 => "Non Current Liabilities",
        4 => "Current Liabilities",
        3 => "Non Current Assets",
        5 => "Current Assets",
    ];

    // Helpers
    $fmt = function ($n) { return ($n !== '' && $n != 0) ? formatAmount($n) : ''; };
    $toDisplay = function (float $signed, string $side) {
        // BS LEFT: DR→−, CR→+ ⇒ display = -signed
        // BS RIGHT: DR→+, CR→− ⇒ display = signed
        return ($side === 'L') ? -$signed : $signed;
    };

    // Inventory valuation
    $inventoryVal = (float)$this->StockStatusModel->closingStockTotal($from_date, $to_date,['consolidated' => (int)$consolidated]);

    /* A) GROUP hierarchy (type=2) limited to BS parents */
    $gRows = $this->db->table('accgrpmstn g')
        ->select('g.acc_grp_id, g.acc_grp_name, u.crs_mst_is_primary, u.under_crs_mst_id, u.crs_mst_parent_id')
        ->join("undercrsmt u", "u.crs_mst_id = g.acc_grp_id AND u.crs_mst_type = 2 AND u.cmp_id = {$this->company_id}", 'left')
        ->where('u.cmpfymastr_id', $fyId)
        ->where('u.cmpfymastr_id IS NOT NULL', null, false)
        ->where('g.cmp_id', $this->company_id)
        ->whereIn('u.crs_mst_parent_id', $ALL_PARENTS)
        ->orderBy('g.acc_grp_name', 'asc')
        ->get()->getResultArray();

    $groupInfo = [];
    $children  = [];
    $primaryByParentCat = [];
    $allGroupIds = [];

    foreach ($gRows as $r) {
        $gid        = (int)$r['acc_grp_id'];
        $name       = $r['acc_grp_name'];
        $isPrimary  = (int)$r['crs_mst_is_primary'];
        $parentGid  = (int)($r['under_crs_mst_id'] ?? 0);
        $parentCat  = (int)($r['crs_mst_parent_id'] ?? 0);

        $allGroupIds[$gid] = true;
        $groupInfo[$gid] = [
            'name'       => $name,
            'is_primary' => $isPrimary,
            'parent_gid' => $parentGid,
            'parent_cat' => $parentCat,
        ];
        if ($parentGid > 0) {
            if (!isset($children[$parentGid])) $children[$parentGid] = [];
            $children[$parentGid][] = $gid;
        }
        if ($isPrimary === 1 && $parentCat > 0) {
            if (!isset($primaryByParentCat[$parentCat])) $primaryByParentCat[$parentCat] = [];
            $primaryByParentCat[$parentCat][] = $gid;
        }
    }

    $validGroupIds = array_keys($allGroupIds);
    if (empty($validGroupIds)) $validGroupIds = [0];

    /* B) ACCOUNTS mapping (normal + Bill Sundry, prefer BS when both) */
    $accRows1 = $this->db->table('acctmaster a')
        ->select('a.acc_id, a.acc_name, a.bsd_id, ua.under_crs_mst_id, ua.crs_mst_parent_id')
        ->join("undercrsmt ua", "ua.crs_mst_id = a.acc_id AND ua.crs_mst_type = 1 AND ua.cmp_id = {$this->company_id}", 'left')
        ->where("
            (
                ua.under_crs_mst_id IN (" . implode(',', $validGroupIds) . ")
                OR
                (ua.under_crs_mst_id = 0 AND ua.crs_mst_parent_id IN (" . implode(',', $ALL_PARENTS) . "))
            )
        ", null, false)
        ->where('ua.cmpfymastr_id', $fyId)
        ->where('ua.cmpfymastr_id IS NOT NULL', null, false)
        ->where('a.cmp_id', $this->company_id)
        ->orderBy('a.acc_name', 'ASC')
        ->get()->getResultArray();

    $accRowsBS = $this->db->table('acctmaster a')
        ->select('a.acc_id, a.acc_name, a.bsd_id, ub.under_crs_mst_id, ub.crs_mst_parent_id')
        ->join("undercrsmt ub", "ub.crs_mst_id = a.acc_id AND ub.crs_mst_type = 14 AND ub.cmp_id = {$this->company_id}", 'left')
        ->where("
            (
                ub.under_crs_mst_id IN (" . implode(',', $validGroupIds) . ")
                OR
                (ub.under_crs_mst_id = 0 AND ub.crs_mst_parent_id IN (" . implode(',', $ALL_PARENTS) . "))
            )
        ", null, false)
        ->where('ub.cmpfymastr_id', $fyId)
        ->where('ub.cmpfymastr_id IS NOT NULL', null, false)
        ->where('a.cmp_id', $this->company_id)
        ->where('a.bsd_id IS NOT NULL', null, false)
        ->orderBy('a.acc_name', 'ASC')
        ->get()->getResultArray();

    $accIndex = [];
    foreach ($accRows1 as $row) {
        $accIndex[(int)$row['acc_id']] = $row;
    }
    foreach ($accRowsBS as $row) {
        $accIndex[(int)$row['acc_id']] = $row + ['_via_bs' => 1];
    }
    $accRows = array_values($accIndex);

    $accountsByGroup   = [];
    $primaryAccByCat   = [];
    $allAccIds         = [];
    $accNameById       = [];

    foreach ($accRows as $a) {
        $aid    = (int)$a['acc_id'];
        $accName= $a['acc_name'];
        $underG = (int)$a['under_crs_mst_id'];
        $pcat   = (int)$a['crs_mst_parent_id'];
        $isBSD  = !empty($a['bsd_id']);

        $accNameById[$aid] = $accName;
        $allAccIds[] = $aid;

        if ($underG === 0) {
            if (!isset($primaryAccByCat[$pcat])) $primaryAccByCat[$pcat] = [];
            $primaryAccByCat[$pcat][] = ['id'=>$aid, 'name'=>$accName, 'is_bsd'=>$isBSD];
        } else {
            if (!isset($accountsByGroup[$underG])) $accountsByGroup[$underG] = [];
            $accountsByGroup[$underG][] = $aid;
        }
    }

    /* C) Opening + Transactions -> Closing per account (signed) */
    $opByAcc = [];
    if (!empty($allAccIds)) {
        $opQB = $this->db->table('accoppybal ob')
            ->select('ob.acc_id, SUM(ob.acc_op_bal) AS op_bal', false)
            ->where('ob.cmp_id', $this->company_id)
            ->where('ob.cmpfymastr_id', $fyId)
            ->whereIn('ob.acc_id', $allAccIds)
            ->groupBy('ob.acc_id');
        if ((int)$consolidated === 0) {
            $opQB->where('ob.hobo_id', $this->bo_id);
        }
        foreach ($opQB->get()->getResultArray() as $r) {
            $opByAcc[(int)$r['acc_id']] = (float)($r['op_bal'] ?? 0);
        }
    }

    $txByAcc = [];
    if (!empty($allAccIds)) {
        $txQB = $this->db->table('accttxnmst a');
        $txQB->select("
            a.acc_id,
            SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END) AS dr,
            SUM(CASE WHEN a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END) AS cr
        ", false)
        // ✅ FIX: Removed vchtxnconso join, use acc_txn_date directly
        ->whereIn('a.acc_txn_type', [1])
        ->where('a.cmp_id', $this->company_id)
        ->whereIn('a.acc_id', $allAccIds)
        ->where('a.vch_txn_id > 0', null, false)
        ->where('a.acc_txn_date >=', $from_date)
        ->where('a.acc_txn_date <=', $to_date)
        ->groupBy('a.acc_id');
        if ((int)$consolidated === 0) {
            $txQB->where('a.hobo_id', $this->bo_id);
        }
        foreach ($txQB->get()->getResultArray() as $t) {
            $txByAcc[(int)$t['acc_id']] = [
                'dr' => (float)($t['dr'] ?? 0),
                'cr' => (float)($t['cr'] ?? 0),
            ];
        }
    }

    $closingByAcc = [];
    foreach ($allAccIds as $aid) {
        $op = $opByAcc[$aid] ?? 0.0;
        $dr = $txByAcc[$aid]['dr'] ?? 0.0;
        $cr = $txByAcc[$aid]['cr'] ?? 0.0;
        $closingByAcc[$aid] = $op + ($dr - $cr);  // +ve = debit (asset), −ve = credit (liability/equity)
    }

    /* D) Recursive totals on groups (include descendants) */
    $sumCache = [];
    $sumGroupClosings = function (int $gid) use (&$sumCache, $children, $accountsByGroup, $closingByAcc, &$sumGroupClosings): float {
        if (isset($sumCache[$gid])) return $sumCache[$gid];
        $sum = 0.0;
        if (!empty($accountsByGroup[$gid])) {
            foreach ($accountsByGroup[$gid] as $aid) {
                $sum += $closingByAcc[$aid] ?? 0.0;
            }
        }
        if (!empty($children[$gid])) {
            foreach ($children[$gid] as $cg) {
                $sum += $sumGroupClosings($cg);
            }
        }
        return $sumCache[$gid] = $sum;
    };

    $descCache = [];
    $collectDesc = function (int $gid) use (&$descCache, $children, &$collectDesc): array {
        if (isset($descCache[$gid])) return $descCache[$gid];
        $out = [$gid];
        if (!empty($children[$gid])) {
            foreach ($children[$gid] as $cg) {
                $out = array_merge($out, $collectDesc($cg));
            }
        }
        return $descCache[$gid] = $out;
    };

    /* E) Profit / Loss (from existing P&L) */
    $plRows = $this->legacy_load_profit_loss_horizontal(1, $from_date, $to_date, $nil_type, $consolidated);
    $netProfit = 0.0;
    $netLoss   = 0.0;
    if (is_array($plRows)) {
        foreach ($plRows as $rr) {
            if (!empty($rr['l_group_name']) && $rr['l_group_name'] === 'Net Profit C/D') {
                $netProfit = (float)($rr['l_balance_total'] ?? 0);
            }
            if (!empty($rr['r_group_name']) && $rr['r_group_name'] === 'Net Loss C/D') {
                $netLoss = (float)($rr['r_balance_total'] ?? 0);
            }
        }
        if ($netProfit == 0.0 && $netLoss == 0.0 && !empty($plRows)) {
            $last = end($plRows);
            $netProfit = (float)($last['l_balance_total'] ?? 0);
            $netLoss   = (float)($last['r_balance_total'] ?? 0);
        }
    }
    $plSignedDisplay = $netProfit - $netLoss; // +ve profit, −ve loss

    /* F) Builders for SCHEDULES / DETAILED (views 1 & 2) */
    $leftRows  = [];
    $rightRows = [];

    $emitCategoryBlock = function (int $parentId, string $side) use (
        $view, $nil_type, $fmt, $toDisplay,
        $groupInfo, $primaryByParentCat, $sumGroupClosings, $collectDesc,
        $accountsByGroup, $closingByAcc, $primaryAccByCat, $accNameById, $BS_PARENT_NAMES,
        &$leftRows, &$rightRows,
        $inventoryVal
    ) {
        $hdr = [
            ($side==='L'?'l_group_id':'r_group_id')       => $parentId,
            ($side==='L'?'l_group_name':'r_group_name')   => $BS_PARENT_NAMES[$parentId] ?? ('Category '.$parentId),
            ($side==='L'?'l_balance':'r_balance')         => '',
            ($side==='L'?'l_balance_total':'r_balance_total') => 0,
            ($side==='L'?'l_type':'r_type')               => 'prt',
            ($side==='L'?'l_style':'r_style')             => 'font-weight:bold;'
        ];
        if ($side === 'L') $leftRows[] = $hdr; else $rightRows[] = $hdr;

        $gids = $primaryByParentCat[$parentId] ?? [];
        foreach ($gids as $gid) {
            $gName = $groupInfo[$gid]['name'];
            $gTotSigned = $sumGroupClosings($gid);
            $disp = $toDisplay($gTotSigned, $side);

            $row = [
                ($side==='L'?'l_group_id':'r_group_id')          => $gid,
                ($side==='L'?'l_group_name':'r_group_name')      => '&nbsp;&nbsp;» '.$gName,
                ($side==='L'?'l_balance':'r_balance')            => $fmt($disp),
                ($side==='L'?'l_balance_total':'r_balance_total')=> $disp,
                ($side==='L'?'l_detail':'r_detail')              => '',
                ($side==='L'?'l_type':'r_type')                  => 'grp',
                ($side==='L'?'l_style':'r_style')                => ($view==2)?'font-weight:bold;':'',
            ];
            if ($side === 'L') $leftRows[] = $row; else $rightRows[] = $row;

            if ((int)$view === 2) {
                $allDesc = $collectDesc($gid);
                $accIds  = [];
                foreach ($allDesc as $dgid) {
                    if (!empty($accountsByGroup[$dgid])) {
                        foreach ($accountsByGroup[$dgid] as $aid) $accIds[] = $aid;
                    }
                }
                if (!empty($accIds)) {
                    foreach ($accIds as $aid) {
                        $signed = $closingByAcc[$aid] ?? 0.0;
                        $aDisp  = $toDisplay($signed, $side);

                        $nm = $accNameById[$aid] ?? ('Acc#'.$aid);
                        $drow = [
                            ($side==='L'?'l_group_id':'r_group_id')          => $aid,
                            ($side==='L'?'l_group_name':'r_group_name')      => '&nbsp;&nbsp;&nbsp;&nbsp;»» '.$nm,
                            ($side==='L'?'l_detail':'r_detail')              => $fmt($aDisp),
                            ($side==='L'?'l_balance':'r_balance')            => '',
                            ($side==='L'?'l_balance_total':'r_balance_total')=> 0,
                            ($side==='L'?'l_type':'r_type')                  => 'acc',
                        ];
                        if ($side === 'L') $leftRows[] = $drow; else $rightRows[] = $drow;
                    }
                }
            }
        }

        $paList = $primaryAccByCat[$parentId] ?? [];
        foreach ($paList as $pa) {
            $aid    = (int)$pa['id'];
            $signed = $closingByAcc[$aid] ?? 0.0;
            $disp   = $toDisplay($signed, $side);

            $suffix = !empty($pa['is_bsd']) ? ' <sub><em>(Bill Sundry)</em></sub>' : ' <sub><em>(Primary Account)</em></sub>';
            $lbl = '&nbsp;&nbsp;» ' . $pa['name'] . $suffix;
            $row = [
                ($side==='L'?'l_group_id':'r_group_id')          => $aid,
                ($side==='L'?'l_group_name':'r_group_name')      => $lbl,
                ($side==='L'?'l_balance':'r_balance')            => $fmt($disp),
                ($side==='L'?'l_balance_total':'r_balance_total')=> $disp,
                ($side==='L'?'l_detail':'r_detail')              => '',
                ($side==='L'?'l_type':'r_type')                  => 'acc',
            ];
            if ($side === 'L') $leftRows[] = $row; else $rightRows[] = $row;
        }

        if ($side === 'R' && $parentId === 5) {
            $rightRows[] = [
                'r_group_id'       => 0,
                'r_group_name'     => '&nbsp;&nbsp;» Inventories',
                'r_balance'        => $fmt($inventoryVal),
                'r_balance_total'  => $inventoryVal,
                'r_detail'         => '',
                'r_type'           => 'inv',
            ];
        }
    };

    /* G) Build rows per VIEW */
    if ((int)$view === 0) {
        $sumCategorySigned = function (int $catId) use ($primaryByParentCat, $primaryAccByCat, $sumGroupClosings, $closingByAcc): float {
            $tot = 0.0;
            foreach (($primaryByParentCat[$catId] ?? []) as $gid) $tot += $sumGroupClosings($gid);
            foreach (($primaryAccByCat[$catId] ?? []) as $pa)  $tot += ($closingByAcc[$pa['id']] ?? 0.0);
            return $tot;
        };

        $ownFund = $toDisplay($sumCategorySigned(1), 'L');
        $ncl     = $toDisplay($sumCategorySigned(2), 'L');
        $cl      = $toDisplay($sumCategorySigned(4), 'L');
        $nca     = $toDisplay($sumCategorySigned(3), 'R');
        $ca      = $toDisplay($sumCategorySigned(5), 'R') + $inventoryVal;

        $leftRows = [];
        $rightRows = [];

        $L = [
            [1,"Owner's Fund",            $ownFund,  'prt', ''],
            [0,"Profit / Loss",           $plSignedDisplay, 'pl', 'font-weight:bold;'],
            [2,"Non Current Liabilities", $ncl,      'prt', ''],
            [4,"Current Liabilities",     $cl,       'prt', ''],
        ];
        foreach ($L as [$gid,$nm,$amt,$typ,$sty]) {
            $leftRows[] = [
                'l_group_id'      => $gid,
                'l_group_name'    => $nm,
                'l_detail'        => '',
                'l_balance'       => $fmt($amt),
                'l_balance_total' => $amt,
                'l_type'          => $typ,
                'l_style'         => $sty
            ];
        }

        $R = [
            [3,"Non Current Assets", $nca, 'prt', ''],
            [5,"Current Assets",     $ca,  'prt', ''],
        ];
        foreach ($R as [$gid,$nm,$amt,$typ,$sty]) {
            $rightRows[] = [
                'r_group_id'      => $gid,
                'r_group_name'    => $nm,
                'r_detail'        => '',
                'r_balance'       => $fmt($amt),
                'r_balance_total' => $amt,
                'r_type'          => $typ,
                'r_style'         => $sty
            ];
        }

    } else {
        foreach ($LEFT_PARENTS as $catId) {
            $emitCategoryBlock($catId, 'L');
            if ($catId === 1) {
                $leftRows[] = [
                    'l_group_id'      => 0,
                    'l_group_name'    => 'Profit / Loss',
                    'l_balance'       => $fmt($plSignedDisplay),
                    'l_balance_total' => $plSignedDisplay,
                    'l_detail'        => '',
                    'l_type'          => 'pl',
                    'l_style'         => 'font-weight:bold;',
                ];
            }
        }
        foreach ($RIGHT_PARENTS as $catId) {
            $emitCategoryBlock($catId, 'R');
        }
    }

    /* H) Merge rows */
    $l_total_unfiltered = array_sum(array_column($leftRows, 'l_balance_total'));
    $r_total_unfiltered = array_sum(array_column($rightRows, 'r_balance_total'));

    $l_total_unfiltered = round($l_total_unfiltered, 2);
    $r_total_unfiltered = round($r_total_unfiltered, 2);

    $final = [];
    $count = max(count($leftRows), count($rightRows));
    for ($i=0; $i<$count; $i++) {
        $l = $leftRows[$i]  ?? [];
        $r = $rightRows[$i] ?? [];
        if (!$l && !$r) continue;

        if ((int)$nil_type === 0) {
            $lt = $l['l_balance_total'] ?? 0.0;
            $ld = $l['l_detail'] ?? '';
            $rt = $r['r_balance_total'] ?? 0.0;
            $rd = $r['r_detail'] ?? '';
            $isLZero = ($lt == 0.0 && ($ld === '' || $ld == '0.00'));
            $isRZero = ($rt == 0.0 && ($rd === '' || $rd == '0.00'));
            $isLHeader = ($l['l_type'] ?? '') === 'prt';
            $isRHeader = ($r['r_type'] ?? '') === 'prt';
            if ($isLHeader && $isRZero) continue;
            if ($isRHeader && $isLZero) continue;
            if (!$isLHeader && !$isRHeader && $isLZero && $isRZero) continue;
        }

        $row = array_merge($l, $r);
        if (isset($row['l_style'])) $row['pq_cellattr']['l_group_name'] = ['style'=>$row['l_style']];
        if (isset($row['r_style'])) $row['pq_cellattr']['r_group_name'] = ['style'=>$row['r_style']];
        $row['pq_cellcls'] = [
            'l_balance'=>'hover-cell','r_balance'=>'hover-cell',
            'l_detail' =>'hover-cell','r_detail' =>'hover-cell'
        ];
        if (isset($row['l_group_id'])) {
            $row['pq_cellattr']['l_balance']['data-group_id']   = $row['l_group_id'];
            $row['pq_cellattr']['l_balance']['data-group_name'] = $row['l_group_name'] ?? '';
            $row['pq_cellattr']['l_balance']['data-dataIndx']   = 'l_group_name';
            $row['pq_cellattr']['l_balance']['data-type']       = $row['l_type'] ?? '';
        }
        if (isset($row['r_group_id'])) {
            $row['pq_cellattr']['r_balance']['data-group_id']   = $row['r_group_id'];
            $row['pq_cellattr']['r_balance']['data-group_name'] = $row['r_group_name'] ?? '';
            $row['pq_cellattr']['r_balance']['data-dataIndx']   = 'r_group_name';
            $row['pq_cellattr']['r_balance']['data-type']       = $row['r_type'] ?? '';
        }
        $final[] = $row;
    }

    /* I) Difference in Opening (ONLY opening balances, no txn adjustments) */
    $openingDiff = parseAmount($this->calculateDiffInOpBalance($from_date, $to_date, $consolidated)); // debit - credit
    $diff_left  = 0.0;
    $diff_right = 0.0;

    if (abs($openingDiff) > 0.01) {
        if ($openingDiff > 0) { // credits > debits, show on 'Assets' side (which is BS RIGHT, but we add to LEFT side values)
            $diff_left = abs($openingDiff);
        } else { // debits > credits, show on 'Liabilities' side (which is BS LEFT, but we add to RIGHT side values)
            $diff_right = abs($openingDiff);
        }

        $row = [
            'l_group_id'   => 0, 'l_group_name' => '', 'l_balance' => '', 'l_balance_total' => 0,
            'r_group_id'   => 0, 'r_group_name' => '', 'r_balance' => '', 'r_balance_total' => 0,
            'l_type' => 'bal', 'r_type' => 'bal',
        ];
        if ($diff_left > 0) {
            $row['l_group_name'] = 'Difference in Opening';
            $row['l_balance'] = $fmt($diff_left);
            $row['l_balance_total'] = $diff_left;
            $row['pq_cellattr']['l_group_name'] = ['style'=>'font-weight:bold;'];
        } else {
            $row['r_group_name'] = 'Difference in Opening';
            $row['r_balance'] = $fmt($diff_right);
            $row['r_balance_total'] = $diff_right;
            $row['pq_cellattr']['r_group_name'] = ['style'=>'font-weight:bold;'];
        }
        $final[] = $row;
    }

    /* J) Grand total row (shows actual totals for both sides) */
    $l_total = round($l_total_unfiltered + $diff_left, 2);
    $r_total = round($r_total_unfiltered + $diff_right, 2);

    $final[] = [
        'l_group_id'      => 0,
        'l_group_name'    => '',
        'l_balance'       => $fmt($l_total),
        'l_balance_total' => $l_total,
        'r_group_id'      => 0,
        'r_group_name'    => '',
        'r_balance'       => $fmt($r_total),
        'r_balance_total' => $r_total,
        'pq_rowattr'      => ['style' => 'background:#E6E6FA;font-weight:bold;']
    ];

    return $final;
}

public function calculateProfitLoss($from_date, $to_date,$consolidated){ 
        $expense_group_ids = [7, 11,13]; // PURCHASES, DIRECT EXPENSES
        $income_group_ids  = [8, 10,12]; // SALES, DIRECT INCOMES
        $calculateTotal    = function($group_ids) use ($from_date, $to_date,$consolidated) {
        $builder = $this->db->table('accttxnmst actmst');		
		$builder->join("undercrsmt", "undercrsmt.crs_mst_id = actmst.acc_id AND undercrsmt.crs_mst_type =1 AND undercrsmt.cmp_id =$this->company_id", 'left');
        $builder->select("
            SUM(CASE WHEN actmst.acc_txn_dr_cr = '1' THEN actmst.acc_txn_amt ELSE 0 END) AS total_dr,
            SUM(CASE WHEN actmst.acc_txn_dr_cr = '2' THEN actmst.acc_txn_amt ELSE 0 END) AS total_cr
         ");
		 if($group_ids)
        $builder->whereIn('undercrsmt.crs_mst_parent_id', $group_ids);
		$builder->where('actmst.acc_txn_type',1);
        $builder->where('actmst.acc_txn_date >=', $from_date);
        $builder->where('actmst.acc_txn_date <=', $to_date);
		$builder->where('actmst.cmp_id', $this->company_id);
		if($consolidated==0)		
		$builder->where('actmst.hobo_id', $this->bo_id); 
        $res = $builder->get()->getRow();	
        return ($res->total_cr ?? 0) + ($res->total_dr ?? 0); // Net effect
     };
     // Calculate totals
     $total_income  = $calculateTotal($income_group_ids);  
     $total_expense = $calculateTotal($expense_group_ids);
     $profit_loss   = $total_income - $total_expense;   
	return $profit_loss;
   }



	
public function load_trial_balance_opn($from_date, $to_date, $consolidated, $nill_type)
{
    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date   = date('Y-m-d', strtotime($to_date));

    // Opening stock at the FY start, scoped like the ledgers (same figure every other view uses)
    $opn_inventory = $this->openingStockFigure($this->engine()->fyStart(), (int)$consolidated);

    $rows = [];
    $total_debit  = 0.0;
    $total_credit = 0.0;

    /* A) Pre-aggregate OPENING balances per account (include Bill Sundry) */
    $opQB = $this->db->table('accoppybal ob')
        ->select('ob.acc_id, SUM(ob.acc_op_bal) AS op_bal', false)
        ->where('ob.cmp_id', $this->company_id)
        ->where('ob.cmpfymastr_id', $this->fy_id)
        ->groupBy('ob.acc_id');

    if ((int)$consolidated === 0) {
        $opQB->where('ob.hobo_id', $this->bo_id);
    }

    $opSubSql = $opQB->getCompiledSelect();

    /* B) Build main list from ACCTMASTER */
    $b = $this->db->table('acctmaster m');
    $b->join("($opSubSql) op", 'op.acc_id = m.acc_id', 'left');
    $b->join(
        'undercrsmt crsbs',
        "crsbs.crs_mst_id = m.acc_id AND crsbs.crs_mst_type = 14 AND crsbs.cmp_id = {$this->company_id} AND crsbs.cmpfymastr_id = {$this->fy_id}",
        'left'
    );
    $b->join(
        'undercrsmt crsmt',
        "crsmt.crs_mst_id = m.acc_id AND crsmt.crs_mst_type = 1 AND crsmt.cmp_id = {$this->company_id} AND crsmt.cmpfymastr_id = {$this->fy_id}",
        'left'
    );
    $b->join('accgrpmstn g_bs', 'crsbs.under_crs_mst_id = g_bs.acc_grp_id', 'left');
    $b->join('accgrpmstn g_n',  'crsmt.under_crs_mst_id = g_n.acc_grp_id',  'left');
    $b->join(
        'grpparentn gp_bs',
        "((crsbs.under_crs_mst_id = 0 AND crsbs.crs_mst_parent_id > 0 AND crsbs.crs_mst_parent_id = gp_bs.acc_grp_parent_id))",
        'left',
        false
    );
    $b->join(
        'grpparentn gp_n',
        "((crsmt.under_crs_mst_id = 0 AND crsmt.crs_mst_parent_id > 0 AND crsmt.crs_mst_parent_id = gp_n.acc_grp_parent_id))",
        'left',
        false
    );

    $b->select("
        m.acc_id,
        m.acc_name,
        m.bsd_id AS acc_bsd_id,
        COALESCE(op.op_bal, 0) AS op_bal,
        COALESCE(
            CASE
                WHEN crsbs.crs_mst_id IS NOT NULL THEN
                    CASE
                        WHEN crsbs.under_crs_mst_id > 0 THEN g_bs.acc_grp_name
                        WHEN crsbs.under_crs_mst_id = 0 AND crsbs.crs_mst_parent_id > 0 THEN gp_bs.acc_grp_parent_name
                        ELSE NULL
                    END
                ELSE NULL
            END,
            CASE
                WHEN crsmt.crs_mst_id IS NOT NULL THEN
                    CASE
                        WHEN crsmt.under_crs_mst_id > 0 THEN g_n.acc_grp_name
                        WHEN crsmt.under_crs_mst_id = 0 AND crsmt.crs_mst_parent_id > 0 THEN gp_n.acc_grp_parent_name
                        ELSE NULL
                    END
                ELSE NULL
            END,
            'Unmapped'
        ) AS account_group
    ", false);

    $b->where('m.cmp_id', $this->company_id);

    // Show all ledgers for opening view; optional filters:
    if ((int)$consolidated === 0) {
        $b->where('op.acc_id IS NOT NULL', null, false);
    }
    if ((int)$nill_type === 0) {
        $b->where('COALESCE(op.op_bal,0) <> 0', null, false);
    }

    $b->orderBy('account_group', 'ASC');
    $b->orderBy('m.acc_name', 'ASC');

    $result = $b->get()->getResultArray();

    if ($result) {
        foreach ($result as $r) {
            $closing = (float)$r['op_bal']; // +DR, -CR
            $debit   = $closing >= 0 ? $closing : 0.0;
            $credit  = $closing <  0 ? abs($closing) : 0.0;

            $total_debit  += $debit;
            $total_credit += $credit;

            $isBSD   = !empty($r['acc_bsd_id']);
            $label   = $r['acc_name'] . ($isBSD ? ' <sub><em>(Bill Sundry)</em></sub>' : '');

            $rows[] = [
                'group_id'           => (int)$r['acc_id'],
                'group_name'         => $label,
                'parent'             => $r['account_group'],
                'credit'             => $credit ? formatAmount($credit) : '',
                'debit'              => $debit  ? formatAmount($debit)  : '',
                'credit_total'       => $credit,
                'debit_total'        => $debit,
                'transaction_status' => 1,
                'type'               => 'dfs',
                'pq_cellcls'         => ['credit' =>'hover-cell','debit' =>'hover-cell'],
                'pq_cellattr'        => [
                    'credit' => ['data-group_id'=>0,'data-groupname'=>'','data-dataindx'=>'','data-id'=>0,'data-type' => 'dfs'],
                    'debit'  => ['data-group_id'=>0,'data-groupname'=>'','data-dataindx'=>'','data-id'=>0,'data-type' => 'dfs'],
                ],
            ];
        }
    }

    /* C) Opening Stock row */
    $stock_credit = $opn_inventory < 0 ? abs($opn_inventory) : 0;
    $stock_debit  = $opn_inventory >=0 ? abs($opn_inventory) : 0;

    $total_debit  += $stock_debit;
    $total_credit += $stock_credit;

    $rows[] = [
        'group_id'           => 0,
        'group_name'         => 'Opening Stock',
        'parent'             => '',
        'credit'             => $stock_credit ? formatAmount($stock_credit) : '',
        'debit'              => $stock_debit  ? formatAmount($stock_debit)  : '',
        'credit_total'       => $stock_credit,
        'debit_total'        => $stock_debit,
        'transaction_status' => 0,
        'type'               => 'opn',
        'pq_cellcls'         => ['credit' =>'hover-cell','debit' =>'hover-cell'],
        'pq_cellattr'        => [
            'credit' => ['data-group_id'=>0,'data-groupname'=>'','data-dataindx'=>'','data-id'=>0,'data-type' => 'opn'],
            'debit'  => ['data-group_id'=>0,'data-groupname'=>'','data-dataindx'=>'','data-id'=>0,'data-type' => 'opn'],
        ],
    ];

    /* D) Difference in Opening row (use row totals, fallback to calc diff) */
    $diff_rows = parseAmount($total_debit - $total_credit); // +ve => debit>credit
    $opening_diff = parseAmount($this->calculateDiffInOpBalance($from_date, $to_date, $consolidated));

    // If rows look balanced but opening_diff exists, use opening_diff
    $difference = (abs($diff_rows) > 0.01) ? $diff_rows : $opening_diff;

    if (abs($difference) > 0.01) {
        $diff_credit = $difference > 0 ? $difference : 0;      // debit>credit -> add to CREDIT
        $diff_debit  = $difference < 0 ? abs($difference) : 0; // credit>debit -> add to DEBIT

        $rows[] = [
            'group_id'           => 0,
            'group_name'         => 'Difference in Opening',
            'parent'             => '',
            'credit'             => $diff_credit ? formatAmount($diff_credit) : '',
            'debit'              => $diff_debit  ? formatAmount($diff_debit)  : '',
            'credit_total'       => $diff_credit,
            'debit_total'        => $diff_debit,
            'transaction_status' => 0,
            'type'               => 'diff',
            'pq_cellcls'         => ['credit' =>'hover-cell','debit' =>'hover-cell'],
            'pq_cellattr'        => [
                'credit' => ['data-group_id'=>0,'data-groupname'=>'','data-dataindx'=>'','data-id'=>0,'data-type' => 'diff'],
                'debit'  => ['data-group_id'=>0,'data-groupname'=>'','data-dataindx'=>'','data-id'=>0,'data-type' => 'diff'],
            ],
        ];
    }

    return $rows;
}

	

	

public function legacy_load_trial_balance_accnts($from_date, $to_date, $consolidated,$is_export=0)
{
    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date   = date('Y-m-d', strtotime($to_date));

    // Use ONLY the raw opening imbalance for the difference line
    $opening_difference = parseAmount(
        $this->calculateDiffInOpBalance($from_date, $to_date, $consolidated)
    );

    $rows = [];

    /* -----------------------------------------------------------------
       Closing Inventory + Inventory Difference (match the GROUPS view)
       ----------------------------------------------------------------- */
    $inventory = $this->StockStatusModel->closingStockTotal($from_date, $to_date);

    // Closing Inventory row (Current Assets, DR)
    $rows[] = [
        'group_id'            => '',
        'group_name'          => 'Closing Inventory',
        'parent'              => 'Current Assets',
        'credit'              => '',
        'debit'               => !empty($inventory) ? formatAmount($inventory) : '',
        'credit_total'        => 0,
        'debit_total'         => $inventory,
        'transaction_status'  => 1,
        'type'                => 'cls',
        'pq_cellcls'          => ['credit' =>'hover-cell','debit' =>'hover-cell'],
        'pq_cellattr'         => [
            'credit' => ['data-group_id'=>0,'data-groupname'=>'','data-dataindx'=>'','data-id'=>0,'data-type' => 'cls'],
            'debit'  => ['data-group_id'=>0,'data-groupname'=>'','data-dataindx'=>'','data-id'=>0,'data-type' => 'cls']
        ]
    ];

    // Opening inventory (consistent FY source)
    $opn_inventory = $this->openingStockTotal(
        $from_date,
        $this->company_id,
        $this->fy_id,
        $this->session->get('ses_dflt_val_method')
    );

    // Inventory Difference row
    $diff       = parseAmount($inventory - $opn_inventory);
    $inv_credit = $diff > 0 ? $diff : 0;
    $inv_debit  = $diff < 0 ? abs($diff) : 0;
    
    $rows[] = [
        'group_id'            => '',
        'group_name'          => 'Inventory Difference',
        'parent'              => 'Profit & Loss A/C',
        'credit'              => $inv_credit ? formatAmount($inv_credit) : '',
        'debit'               => $inv_debit  ? formatAmount($inv_debit)  : '',
        'credit_total'        => $inv_credit,
        'debit_total'         => $inv_debit,
        'transaction_status'  => 1,
        'type'                => 'dfs',
        'pq_cellcls'          => ['credit' =>'hover-cell','debit' =>'hover-cell'],
        'pq_cellattr'         => [
            'credit' => ['data-group_id'=>0,'data-groupname'=>'','data-dataindx'=>'','data-id'=>0,'data-type' => 'dfs'],
            'debit'  => ['data-group_id'=>0,'data-groupname'=>'','data-dataindx'=>'','data-id'=>0,'data-type' => 'dfs']
        ]
    ];

    /* ------------------------------------------------------------
       Get ALL accounts with their group/parent mapping
       ------------------------------------------------------------ */
    // Normal mappings (type=1)
    $accList1 = $this->db->table('acctmaster m')
        ->join("undercrsmt crsmt", "crsmt.crs_mst_id = m.acc_id AND crsmt.crs_mst_type = 1 AND crsmt.cmp_id = {$this->company_id}", 'left')
        ->join('accgrpmstn g', 'crsmt.under_crs_mst_id = g.acc_grp_id', 'left')
        ->join(
            'grpparentn gp',
            "
            (
                (crsmt.under_crs_mst_id > 0 AND crsmt.crs_mst_parent_id = 0 AND crsmt.crs_mst_parent_id = gp.acc_grp_parent_id)
                OR
                (crsmt.under_crs_mst_id = 0 AND crsmt.crs_mst_parent_id > 0 AND crsmt.crs_mst_parent_id = gp.acc_grp_parent_id)
            )
            ",
            'left',
            false
        )
        ->select("
            m.acc_id,
            m.acc_name,
            m.bsd_id,
            CASE 
                WHEN crsmt.under_crs_mst_id > 0 AND crsmt.crs_mst_parent_id = 0 THEN crsmt.crs_mst_parent_id
                WHEN crsmt.under_crs_mst_id > 0 AND crsmt.crs_mst_parent_id > 0 THEN crsmt.crs_mst_parent_id
                WHEN crsmt.under_crs_mst_id = 0 AND crsmt.crs_mst_parent_id > 0 THEN gp.acc_grp_parent_id        
            END AS acc_grp_parent_id,
            CASE 
                WHEN crsmt.under_crs_mst_id > 0 AND crsmt.crs_mst_parent_id = 0 THEN g.acc_grp_name
                WHEN crsmt.under_crs_mst_id > 0 AND crsmt.crs_mst_parent_id > 0 THEN g.acc_grp_name
                WHEN crsmt.under_crs_mst_id = 0 AND crsmt.crs_mst_parent_id > 0 THEN gp.acc_grp_parent_name
            END AS account_group
        ", false)
        ->where('crsmt.cmpfymastr_id IS NOT NULL')
        ->where('crsmt.cmpfymastr_id', $this->fy_id)
        ->orderBy('m.acc_name', 'ASC')
        ->get()
        ->getResultArray();

    // Bill Sundry mappings (type=14)
    $accListBS = $this->db->table('acctmaster m')
        ->join("undercrsmt crsbs", "crsbs.crs_mst_id = m.acc_id AND crsbs.crs_mst_type = 14 AND crsbs.cmp_id = {$this->company_id}", 'left')
        ->join('accgrpmstn g2', 'crsbs.under_crs_mst_id = g2.acc_grp_id', 'left')
        ->join(
            'grpparentn gp2',
            "
            (
                (crsbs.under_crs_mst_id > 0 AND crsbs.crs_mst_parent_id = 0 AND crsbs.crs_mst_parent_id = gp2.acc_grp_parent_id)
                OR
                (crsbs.under_crs_mst_id = 0 AND crsbs.crs_mst_parent_id > 0 AND crsbs.crs_mst_parent_id = gp2.acc_grp_parent_id)
            )
            ",
            'left',
            false
        )
        ->select("
            m.acc_id,
            m.acc_name,
            m.bsd_id,
            CASE 
                WHEN crsbs.under_crs_mst_id > 0 AND crsbs.crs_mst_parent_id = 0 THEN crsbs.crs_mst_parent_id
                WHEN crsbs.under_crs_mst_id > 0 AND crsbs.crs_mst_parent_id > 0 THEN crsbs.crs_mst_parent_id
                WHEN crsbs.under_crs_mst_id = 0 AND crsbs.crs_mst_parent_id > 0 THEN gp2.acc_grp_parent_id        
            END AS acc_grp_parent_id,
            CASE 
                WHEN crsbs.under_crs_mst_id > 0 AND crsbs.crs_mst_parent_id = 0 THEN g2.acc_grp_name
                WHEN crsbs.under_crs_mst_id > 0 AND crsbs.crs_mst_parent_id > 0 THEN g2.acc_grp_name
                WHEN crsbs.under_crs_mst_id = 0 AND crsbs.crs_mst_parent_id > 0 THEN gp2.acc_grp_parent_name
            END AS account_group
        ", false)
        ->where('crsbs.cmpfymastr_id IS NOT NULL')
        ->where('crsbs.cmpfymastr_id', $this->fy_id)
        ->where('m.bsd_id IS NOT NULL', null, false)
        ->orderBy('m.acc_name', 'ASC')
        ->get()
        ->getResultArray();

    // Merge: prefer Bill Sundry mapping when both exist
    $accIndex = [];
    foreach ($accList1 as $r) {
        $accIndex[(int)$r['acc_id']] = $r;
    }
    foreach ($accListBS as $r) {
        $accIndex[(int)$r['acc_id']] = $r + ['_via_bs' => 1];
    }
    $accList = array_values($accIndex);

    // Keep result ordered by account name
    usort($accList, static function($a, $b) {
        return strcasecmp($a['acc_name'], $b['acc_name']);
    });

    if (!empty($accList)) {

        $accIds = array_map(static fn($r) => (int)$r['acc_id'], $accList);

        // Opening balances from accoppybal
        $opMap = [];
        $opQB = $this->db->table('accoppybal ob')
            ->select('ob.acc_id, SUM(ob.acc_op_bal) AS op_bal', false)
            ->where('ob.cmp_id', $this->company_id)
            ->where('ob.cmpfymastr_id', $this->fy_id)
            ->whereIn('ob.acc_id', $accIds)
            ->groupBy('ob.acc_id');

        if ($consolidated == 0) {
            $opQB->where('ob.hobo_id', $this->bo_id);
        }

        foreach ($opQB->get()->getResultArray() as $r) {
            $opMap[(int)$r['acc_id']] = (float)($r['op_bal'] ?? 0);
        }

        // Period DR/CR from accttxnmst
        $txMap = [];
        $txQB = $this->db->table('accttxnmst a')
            ->select("
                a.acc_id,
                SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END) AS dr,
                SUM(CASE WHEN a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END) AS cr
            ", false)
            ->where('a.acc_txn_type', 1)
            ->where('a.cmp_id', $this->company_id)
            ->whereIn('a.acc_id', $accIds)
            ->where('a.vch_txn_id >0', null, false)
            ->where('a.acc_txn_date >=', $from_date)
            ->where('a.acc_txn_date <=', $to_date)
            ->groupBy('a.acc_id');

        if ($consolidated == 0) {
            $txQB->where('a.hobo_id', $this->bo_id);
        }

        foreach ($txQB->get()->getResultArray() as $t) {
            $txMap[(int)$t['acc_id']] = [
                'dr' => (float)($t['dr'] ?? 0),
                'cr' => (float)($t['cr'] ?? 0),
            ];
        }

        // Build account rows
        foreach ($accList as $acc) {
            $accId   = (int)$acc['acc_id'];
            $accName = $acc['acc_name'];

            $op = $opMap[$accId]       ?? 0.0;
            $dr = $txMap[$accId]['dr'] ?? 0.0;
            $cr = $txMap[$accId]['cr'] ?? 0.0;

            // Skip if no activity
            if ($op == 0.0 && $dr == 0.0 && $cr == 0.0) {
                continue;
            }

            // Closing = Opening + (DR − CR)
            $closing = $op + ($dr - $cr);

            if ($closing < 0) {
                $credit = abs($closing);
                $debit  = 0.0;
            } else {
                $debit  = $closing;
                $credit = 0.0;
            }

            $rows[] = [
                'group_id'            => $accId,
                'group_name'          => $accName,
                'parent'              => $acc['account_group'],
                'credit'              => $credit ? formatAmount($credit) : '',
                'debit'               => $debit  ? formatAmount($debit)  : '',
                'credit_total'        => $credit,
                'debit_total'         => $debit,
                'transaction_status'  => 1,
                'type'                => 'dfs',
                'pq_cellcls'          => ['credit' =>'hover-cell','debit' =>'hover-cell'],
                'pq_cellattr'         => [
                    'credit' => ['data-group_id'=>0,'data-groupname'=>'','data-dataindx'=>'','data-id'=>0,'data-type' => 'dfs'],
                    'debit'  => ['data-group_id'=>0,'data-groupname'=>'','data-dataindx'=>'','data-id'=>0,'data-type' => 'dfs']
                ]
            ];
        }
    }

    /* ------------------------------------------------------------
       Difference in Opening line (ONLY use $opening_difference)
       ------------------------------------------------------------ */
    if (abs($opening_difference) > 0.01) {
        // opening_difference is debit - credit (positive => debit higher)
        $bal_credit = $opening_difference > 0 ? $opening_difference : 0;   // add to CREDIT if debit>credit
        $bal_debit  = $opening_difference < 0 ? abs($opening_difference) : 0; // add to DEBIT if credit>debit

        $rows[] = [
            'group_id'            => '',
            'group_name'          => 'Difference in Opening',
            'parent'              => '',
            'credit'              => $bal_credit ? formatAmount($bal_credit) : '',
            'debit'               => $bal_debit  ? formatAmount($bal_debit)  : '',
            'credit_total'        => $bal_credit,
            'debit_total'         => $bal_debit,
            'transaction_status'  => 0,
            'type'                => 'opn',
            'pq_cellcls'          => ['credit' =>'hover-cell','debit' =>'hover-cell'],
            'pq_cellattr'         => [
                'credit' => ['data-group_id'=>0,'data-groupname'=>'','data-dataindx'=>'','data-id'=>0,'data-type' => 'opn'],
                'debit'  => ['data-group_id'=>0,'data-groupname'=>'','data-dataindx'=>'','data-id'=>0,'data-type' => 'opn']
            ]
        ];
    }

    return $rows;
}
function group_parent_info($acc_grp_id){	 
  	$account_grp_tbl = 'grpparentn'; 
  	return $this->db->table($account_grp_tbl)->where('acc_grp_parent_id', $acc_grp_id)->get()->getRowArray();   	   
  }
  
public function legacy_load_trial_balance_grps($from_date, $to_date, $consolidated = 0)
{
    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date   = date('Y-m-d', strtotime($to_date));
   
    $final = [];
    
    /* ── Inventory rows */
    $inventory = $this->StockStatusModel->closingStockTotal($from_date, $to_date);
    if ($inventory != 0) {
        $final[] = [
            'group_id'           => 0,
            'group_name'         => 'Closing Inventory',
            'parent'             => 'Current Assets',
            'credit'             => '',
            'debit'              => formatAmount($inventory),
            'credit_total'       => 0,
            'debit_total'        => $inventory,
            'transaction_status' => 1,
            'type'               => 'cls',
            'pq_cellcls'         => ['credit' =>'hover-cell','debit' =>'hover-cell'],
            'pq_cellattr'        => [
                'credit' => ['data-group_id'=>'0','data-groupname'=>'','data-dataindx'=>'','data-id'=>0,'data-type' => 'cls'],
                'debit'  => ['data-group_id'=>'0','data-groupname'=>'','data-dataindx'=>'','data-id'=>0,'data-type' => 'cls']
            ],
        ];
    }

    $opn_inventory = $this->openingStockTotal($from_date, $this->company_id, $this->fy_id, $this->session->get('ses_dflt_val_method'));
    $diff = parseAmount($inventory - $opn_inventory);
    
    if ($diff != 0) {
        $inv_credit = $diff > 0 ? $diff : 0;
        $inv_debit  = $diff < 0 ? abs($diff) : 0;
        
        $final[] = [
            'group_id'           => 0,
            'group_name'         => 'Inventory Difference',
            'parent'             => 'Profit & Loss A/C',
            'credit'             => $inv_credit ? formatAmount($inv_credit) : '',
            'debit'              => $inv_debit  ? formatAmount($inv_debit)  : '',
            'credit_total'       => $inv_credit,
            'debit_total'        => $inv_debit,
            'transaction_status' => 1,
            'type'               => 'dfs',
            'pq_cellcls'         => ['credit' =>'hover-cell','debit' =>'hover-cell'],
            'pq_cellattr'        => [
                'credit' => ['data-group_id'=>'0','data-groupname'=>'','data-dataindx'=>'','data-id'=>0,'data-type' => 'dfs'],
                'debit'  => ['data-group_id'=>'0','data-groupname'=>'','data-dataindx'=>'','data-id'=>0,'data-type' => 'dfs']
            ],
        ];
    }

    /* ── 1) Load ALL groups (type=2) & build full tree */
    $gRows = $this->db->table('accgrpmstn g')
        ->select('g.acc_grp_id, g.acc_grp_name, u.crs_mst_is_primary, u.under_crs_mst_id, u.crs_mst_parent_id')
        ->join("undercrsmt u", "u.crs_mst_id = g.acc_grp_id AND u.crs_mst_type = 2 AND u.cmp_id = {$this->company_id}", 'left')
        ->where('u.cmpfymastr_id', $this->fy_id)
        ->where('u.cmpfymastr_id IS NOT NULL')
        ->get()->getResultArray();

    $groupInfo = [];
    $children  = [];

    foreach ($gRows as $r) {
        $gid = (int)$r['acc_grp_id'];
        $parent_gid = (int)($r['under_crs_mst_id'] ?? 0);
        $groupInfo[$gid] = [
            'name'           => $r['acc_grp_name'],
            'is_primary'     => (int)$r['crs_mst_is_primary'],
            'parent_gid'     => $parent_gid,
            'parent_cat_id'  => (int)($r['crs_mst_parent_id'] ?? 0),
        ];
        if ($parent_gid > 0) {
            if (!isset($children[$parent_gid])) $children[$parent_gid] = [];
            $children[$parent_gid][] = $gid;
        }
    }

    /* ── 2) Load ALL accounts & map to their immediate group */
    $accRows1 = $this->db->table('acctmaster a')
        ->select('a.acc_id, a.acc_name, a.bsd_id, u.under_crs_mst_id, u.crs_mst_parent_id')
        ->join("undercrsmt u", "u.crs_mst_id = a.acc_id AND u.crs_mst_type = 1 AND u.cmp_id = {$this->company_id}", 'left')
        ->where('u.cmpfymastr_id', $this->fy_id)
        ->where('u.cmpfymastr_id IS NOT NULL')
        ->get()->getResultArray();

    $accRowsBS = $this->db->table('acctmaster a')
        ->select('a.acc_id, a.acc_name, a.bsd_id, ub.under_crs_mst_id, ub.crs_mst_parent_id')
        ->join("undercrsmt ub", "ub.crs_mst_id = a.acc_id AND ub.crs_mst_type = 14 AND ub.cmp_id = {$this->company_id}", 'left')
        ->where('ub.cmpfymastr_id', $this->fy_id)
        ->where('ub.cmpfymastr_id IS NOT NULL')
        ->where('a.bsd_id IS NOT NULL', null, false)
        ->get()->getResultArray();

    $accIndex = [];
    foreach ($accRows1 as $row) {
        $accIndex[(int)$row['acc_id']] = $row;
    }
    foreach ($accRowsBS as $row) {
        $accIndex[(int)$row['acc_id']] = $row + ['_via_bs' => 1];
    }
    $accRows = array_values($accIndex);

    $accountsByGroup = [];
    $primaryAccounts = [];
    $allAccIdsSet    = [];

    foreach ($accRows as $a) {
        $accId = (int)$a['acc_id'];
        $allAccIdsSet[$accId] = true;

        $under_gid = (int)$a['under_crs_mst_id'];
        if ($under_gid === 0) {
            $primaryAccounts[] = [
                'acc_id'            => $accId,
                'acc_name'          => $a['acc_name'],
                'acc_grp_parent_id' => (int)$a['crs_mst_parent_id'],
                'is_bsd'            => !empty($a['bsd_id']),
            ];
        } else {
            if (!isset($accountsByGroup[$under_gid])) $accountsByGroup[$under_gid] = [];
            $accountsByGroup[$under_gid][] = $accId;
        }
    }
    $allAccIds = array_keys($allAccIdsSet);

    /* ── 3) Preload OPENING + TXNs once */
    $opByAcc = [];
    if (!empty($allAccIds)) {
        $opQB = $this->db->table('accoppybal ob')
            ->select('ob.acc_id, SUM(ob.acc_op_bal) AS op_bal', false)
            ->where('ob.cmp_id', $this->company_id)
            ->where('ob.cmpfymastr_id', $this->fy_id)
            ->whereIn('ob.acc_id', $allAccIds)
            ->groupBy('ob.acc_id');
        if ($consolidated == 0) { $opQB->where('ob.hobo_id', $this->bo_id); }
        foreach ($opQB->get()->getResultArray() as $r) {
            $opByAcc[(int)$r['acc_id']] = (float)($r['op_bal'] ?? 0);
        }
    }

    $txByAcc = [];
    if (!empty($allAccIds)) {
        $txQB = $this->db->table('accttxnmst a');
        $txQB->select("
            a.acc_id,
            SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END) AS dr,
            SUM(CASE WHEN a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END) AS cr
        ", false)
        ->whereIn('a.acc_txn_type', [1])
        ->where('a.cmp_id', $this->company_id)
        ->whereIn('a.acc_id', $allAccIds)
        ->where('a.vch_txn_id >0', null, false)
        ->where('a.acc_txn_date >=', $from_date)
        ->where('a.acc_txn_date <=', $to_date)
        ->groupBy('a.acc_id');
        if ($consolidated == 0) { $txQB->where('a.hobo_id', $this->bo_id); }
        foreach ($txQB->get()->getResultArray() as $t) {
            $txByAcc[(int)$t['acc_id']] = [
                'dr' => (float)($t['dr'] ?? 0),
                'cr' => (float)($t['cr'] ?? 0),
            ];
        }
    }

    $closingByAcc = [];
    foreach ($allAccIds as $aid) {
        $op = $opByAcc[$aid] ?? 0.0;
        $dr = $txByAcc[$aid]['dr'] ?? 0.0;
        $cr = $txByAcc[$aid]['cr'] ?? 0.0;
        $closingByAcc[$aid] = $op + ($dr - $cr);
    }

    /* ── 4) Sum closings for each group INCLUDING descendants */
    $sumCache = [];
    $sumGroupClosings = null;
    $sumGroupClosings = function (int $gid) use (&$sumGroupClosings, &$sumCache, $children, $accountsByGroup, $closingByAcc): float {
        if (isset($sumCache[$gid])) return $sumCache[$gid];
        $sum = 0.0;

        if (!empty($accountsByGroup[$gid])) {
            foreach ($accountsByGroup[$gid] as $aid) {
                $sum += $closingByAcc[$aid] ?? 0.0;
            }
        }
        if (!empty($children[$gid])) {
            foreach ($children[$gid] as $childGid) {
                $sum += $sumGroupClosings($childGid);
            }
        }
        return $sumCache[$gid] = $sum;
    };

    /* ── 5) Emit ONLY PRIMARY GROUPS */
    foreach ($groupInfo as $gid => $info) {
        if ((int)$info['is_primary'] !=1) continue;

        $sumClosing = $sumGroupClosings($gid);
        if ($sumClosing == 0) continue;
        
        $credit_total = $sumClosing < 0 ? abs($sumClosing) : 0.0;
        $debit_total  = $sumClosing < 0 ? 0.0 : $sumClosing;

        $parentLabel = '';
        $pid = $info['parent_cat_id'] ?? 0;
        if ($pid) {
            $gp = $this->group_parent_info($pid);
            if ($gp) $parentLabel = $gp['acc_grp_parent_name'];
        }

        $final[] = [
            'group_id'           => $gid,
            'group_name'         => $info['name'],
            'parent'             => $parentLabel,
            'credit'             => $credit_total ? formatAmount($credit_total) : '',
            'debit'              => $debit_total  ? formatAmount($debit_total)  : '',
            'credit_total'       => $credit_total,
            'debit_total'        => $debit_total,
            'transaction_status' => 1,
            'type'               => 'grp',
            'pq_cellcls'         => ['credit' =>'hover-cell','debit' =>'hover-cell'],
            'pq_cellattr'        => [
                'credit' => ['data-group_id'=>$gid,'data-groupname'=>$info['name'],'data-dataindx'=>$info['name'],'data-id'=>$gid,'data-type' => 'grp'],
                'debit'  => ['data-group_id'=>$gid,'data-groupname'=>$info['name'],'data-dataindx'=>$info['name'],'data-id'=>$gid,'data-type' => 'grp'],
            ],
        ];
    }

    /* ── 6) Emit ONLY PRIMARY ACCOUNTS */
    foreach ($primaryAccounts as $pa) {
        $acc_id  = (int)$pa['acc_id'];
        $closing = $closingByAcc[$acc_id] ?? 0.0;
        if ($closing == 0.0) continue;

        $credit_total = $closing < 0 ? abs($closing) : 0.0;
        $debit_total  = $closing < 0 ? 0.0 : $closing;

        $acc_name = $pa['acc_name'] . ' <sub><em>(' . (!empty($pa['is_bsd']) ? 'Bill Sundry' : 'Primary Account') . ')</em></sub>';

        $parent = '';
        if (!empty($pa['acc_grp_parent_id'])) {
            $gpInfo = $this->group_parent_info((int)$pa['acc_grp_parent_id']);
            if ($gpInfo) $parent = $gpInfo['acc_grp_parent_name'];
        }

        $final[] = [
            'group_id'           => $acc_id,
            'group_name'         => $acc_name,
            'parent'             => $parent,
            'credit'             => $credit_total ? formatAmount($credit_total) : '',
            'debit'              => $debit_total  ? formatAmount($debit_total)  : '',
            'credit_total'       => $credit_total,
            'debit_total'        => $debit_total,
            'transaction_status' => 1,
            'type'               => 'acc',
            'pq_cellcls'         => ['credit' =>'hover-cell','debit' =>'hover-cell'],
            'pq_cellattr'        => [
                'credit' => ['data-group_id'=>$acc_id,'data-groupname'=>$acc_name,'data-dataindx'=>$acc_name,'data-id'=>$acc_id,'data-type' => 'acc'],
                'debit'  => ['data-group_id'=>$acc_id,'data-groupname'=>$acc_name,'data-dataindx'=>$acc_name,'data-id'=>$acc_id,'data-type' => 'acc'],
            ],
        ];
    }

    /* ── 7) Difference in Opening line: ONLY use opening imbalance */
    $opening_difference = parseAmount(
        $this->calculateDiffInOpBalance($from_date, $to_date, $consolidated)
    );

    if (abs($opening_difference) > 0.01) {
        // opening_difference is debit - credit (positive => debit higher)
        $bal_credit = $opening_difference > 0 ? $opening_difference : 0;    // add to CREDIT if debit>credit
        $bal_debit  = $opening_difference < 0 ? abs($opening_difference) : 0; // add to DEBIT if credit>debit

        $final[] = [
            'group_id'           => 0,
            'group_name'         => 'Difference in Opening',
            'parent'             => '',
            'credit'             => $bal_credit ? formatAmount($bal_credit) : '',
            'debit'              => $bal_debit  ? formatAmount($bal_debit)  : '',
            'credit_total'       => $bal_credit,
            'debit_total'        => $bal_debit,
            'transaction_status' => 0,
            'type'               => 'opn',
            'pq_cellcls'         => ['credit' =>'hover-cell','debit' =>'hover-cell'],
            'pq_cellattr'        => [
                'credit' => ['data-group_id'=>0,'data-groupname'=>'','data-dataindx'=>'','data-id'=>0,'data-type' => 'opn'],
                'debit'  => ['data-group_id'=>0,'data-groupname'=>'','data-dataindx'=>'','data-id'=>0,'data-type' => 'opn'],
            ],
        ];
    }

    return $final;
}

/* Helper left as-is (unused in the final balancing step, but keep if referenced elsewhere) */
public function calculateDiffInOpBalance($from_date, $to_date, $consolidated = 0)
{
    // Debit minus credit of ALL FY opening balances (every ledger of the company / FY / branch, mapped or not)
    // plus the opening stock at the FY start. A non-zero value is a real opening imbalance in the books;
    // nothing else is ever added to it.
    $cons     = (int)$consolidated;
    $eng      = $this->engine();
    $openingStock = $this->openingStockFigure($eng->fyStart(), $cons);
    return round($eng->openingTotalDirect((bool)$cons) + $openingStock, 2);
}
public function calculateDiffInOpBalance_11_04_2026($from_date, $to_date, $consolidated = 0)
{   $fyId = (int)($this->session->get('ses_comp_fy_id') ?? $this->fy_id ?? 0);
    $opDebitQB = $this->db->table('accoppybal ob')
        ->select('SUM(CASE WHEN ob.acc_op_bal > 0 THEN ob.acc_op_bal ELSE 0 END) AS debit_total', false)
        ->where('ob.cmp_id', $this->company_id)
        ->where('ob.cmpfymastr_id', $fyId);
    
    if ($consolidated == 0) { 
        $opDebitQB->where('ob.hobo_id', $this->bo_id); 
    }
    
    $debitResult = $opDebitQB->get()->getRowArray();
    $total_debit_opening = (float)($debitResult['debit_total'] ?? 0);
    
    $opCreditQB = $this->db->table('accoppybal ob')
        ->select('SUM(CASE WHEN ob.acc_op_bal < 0 THEN ABS(ob.acc_op_bal) ELSE 0 END) AS credit_total', false)
        ->where('ob.cmp_id', $this->company_id)
        ->where('ob.cmpfymastr_id', $fyId);
    
    if ($consolidated == 0) { 
        $opCreditQB->where('ob.hobo_id', $this->bo_id); 
    }
    
    $creditResult = $opCreditQB->get()->getRowArray();
    $total_credit_opening = (float)($creditResult['credit_total'] ?? 0);
    
    $opn_inventory = $this->openingStockTotal($from_date, $this->company_id, $this->fy_id, $this->session->get('ses_dflt_val_method'));
    $total_debit_opening += $opn_inventory;
    
    return $total_debit_opening - $total_credit_opening;
}


public function getAllSubGroupIds($parent_id, $level = 1){
    $ids = [];
    if ($level > 5) {
        return $ids; // stop recursion after level 5
    }
    
     $builder =  $this->db->table("accgrpmstn acgrpmst");
	 $builder->join("undercrsmt", "undercrsmt.under_crs_mst_id = acgrpmst.acc_grp_id AND undercrsmt.crs_mst_type =2 AND undercrsmt.cmp_id =$this->company_id", 'left');
     $builder->select('acgrpmst.acc_grp_id');		
     $builder->where('undercrsmt.crs_mst_parent_id', $parent_id);		
     $subgroups 	=$builder->get()->getResultArray();
     foreach ($subgroups as $sub) {
        $ids[] = $sub['acc_grp_id'];
        $childIds = $this->getAllSubGroupIds($sub['acc_grp_id'], $level + 1);
        if (is_array($childIds)) {
            $ids = array_merge($ids, $childIds);
        }
    }
    return $ids;
}
	
   function acc_opn_balance_info($acc_id,$consolidated){
	$builder = $this->db->table('accttxnmst');
	$builder->select("
		SUM(CASE WHEN acc_txn_dr_cr = '1' THEN acc_txn_amt ELSE 0 END) AS total_dr,
		SUM(CASE WHEN acc_txn_dr_cr = '2' THEN acc_txn_amt ELSE 0 END) AS total_cr
	");
	$builder->where('acc_id', $acc_id);	
	$builder->where('acc_txn_type',1);	
	$builder->where('cmp_id', $this->company_id);	
    if($consolidated==0)	
	$builder->where('hobo_id', $this->bo_id); 
	$res = $builder->get()->getRow();	                    				
	return ($res->total_dr ?? 0) - ($res->total_cr ?? 0);    	
  } 
   
   private function getAccountBalancePL($acc_id, $date, $is_strict = false, $consolidated)
	{
		$builder = $this->db->table('accttxnmst');
		$builder->select("
			SUM(CASE WHEN acc_txn_dr_cr = 1 THEN acc_txn_amt ELSE 0 END) AS total_dr,
			SUM(CASE WHEN acc_txn_dr_cr = 2 THEN acc_txn_amt ELSE 0 END) AS total_cr
		");
		$builder->where('acc_id', $acc_id);
		$builder->where($is_strict ? 'acc_txn_date <' : 'acc_txn_date <=', $date);
		$builder->where('acc_txn_type', 1);
		$builder->where('cmp_id', $this->company_id);
		if ((int)$consolidated === 0) {
			$builder->where('hobo_id', $this->bo_id);
		}
		return $builder->get()->getRow();
	}

public function GetParentBalance(array $all_group_ids, string $from_date, string $to_date, int $consolidated)
{
    // PostgreSQL error fix:
    // When selecting aggregates without GROUP BY, you cannot ORDER BY non-aggregated columns.
    // So we must REMOVE orderBy() and limit(1). The query returns a single aggregate row anyway.

    $builder = $this->db->table('accttxnmst actmst');

    $builder->join(
        'undercrsmt undercrsmt',
        "undercrsmt.crs_mst_id = actmst.acc_id
         AND undercrsmt.crs_mst_type = 1
         AND undercrsmt.cmp_id = {$this->company_id}",
        'left'
    );

    $builder->select("
        SUM(CASE WHEN actmst.acc_txn_dr_cr = '1' AND actmst.acc_txn_type = 1 THEN actmst.acc_txn_amt ELSE 0 END) AS total_dr,
        SUM(CASE WHEN actmst.acc_txn_dr_cr = '2' AND actmst.acc_txn_type = 1 THEN actmst.acc_txn_amt ELSE 0 END) AS total_cr
    ", false);

    // Parent match (either immediate group parent or BS parent)
    $builder->groupStart()
        ->whereIn('undercrsmt.under_crs_mst_id', $all_group_ids)
        ->orWhereIn('undercrsmt.crs_mst_parent_id', $all_group_ids)
    ->groupEnd();

    // Date and company filters
    $builder->where('actmst.acc_txn_date >=', date('Y-m-d', strtotime($from_date)));
    $builder->where('actmst.acc_txn_date <=', date('Y-m-d', strtotime($to_date)));
    $builder->where('actmst.acc_txn_type', 1);
    $builder->where('actmst.cmp_id', $this->company_id);

    // Branch/HO filter when not consolidated
    if ((int)$consolidated === 0 && !empty($this->bo_id)) {
        $builder->where('actmst.hobo_id', $this->bo_id);
    }

    // IMPORTANT: Do NOT order or limit; Postgres requires ORDER BY columns to be grouped/selected.
    // $builder->orderBy('actmst.acc_txn_date', 'desc');
    // $builder->orderBy('actmst.vch_txn_id', 'desc');
    // $builder->orderBy('actmst.acc_txn_id', 'desc');
    // $builder->limit(1);

    $row = $builder->get()->getRow();
    return $row ? ((float)$row->total_dr - (float)$row->total_cr) : 0.0;
}


   function get_parent_group_details_pl($acc_grp_parent_id, $view, $from_date, $to_date,$consolidated)
    {
		$from_date = date('Y-m-d', strtotime($from_date));
	    $to_date   = date('Y-m-d', strtotime($to_date));
		$group_ids = $this->getAllSubGroupIds($acc_grp_parent_id);		
		$final_ids = array_merge([$acc_grp_parent_id],$group_ids);		
		$parent_sub_parent_sum = $this->GetParentBalance($final_ids,$from_date, $to_date,$consolidated);
		$final  = [];
    	$final1 = [];
    	$final2 = [];
    	$grpparentn_tbl = 'grpparentn';
    	$parent =  $this->db->table($grpparentn_tbl)->where('acc_grp_parent_id', $acc_grp_parent_id)->get()->getRowArray();
        if($parent)
    	{
			if($view == 1){
				$final1[] = [
					'group_id'	 	=> $parent['acc_grp_parent_id'],
					'group_name' 	=> $parent['acc_grp_parent_name'],
					'balance'	 	=> '',
					'balance_d'	 	=> '',
					'type'			=> 'prt',
					'style'			=> 'font-weight:bold;',
					'dataId'		=> '1'
				];
			}
			
            if($view == 2){
				$final2[] = [
					'group_id'	 	=> $parent['acc_grp_parent_id'],
					'group_name' 	=> $parent['acc_grp_parent_name'],
					'balance'	 	=> $parent_sub_parent_sum ?? '',
					'balance_d'	 	=> '',
					'type'			=> 'prt',
					'style'			=> 'font-weight:bold;',
					'dataId'		=> '2'
				];
			}

    		$account_grp_tbl    = "accgrpmstn";
    		$account_master_tbl = "acctmaster";
    		$undercrsmt_tbl     = "undercrsmt";  
    		$builder =  $this->db->table("accgrpmstn acgrpmst");
			$builder->join("undercrsmt", "undercrsmt.crs_mst_parent_id = acgrpmst.acc_grp_id AND undercrsmt.crs_mst_type =2 AND undercrsmt.cmp_id =$this->company_id", 'left');
     		$builder->where('undercrsmt.crs_mst_is_primary', 1);
			$builder->where('undercrsmt.cmpfymastr_id', $this->fy_id);
			$builder->where('undercrsmt.cmpfymastr_id IS NOT NULL');
     		$builder->where('undercrsmt.crs_mst_parent_id', $acc_grp_parent_id);
     		$result = $builder->get()->getResultArray();
    		$balance_total = 0;
    		if($result) 
    		{
    			foreach ($result as $key => $value) 
    			{
    				$group_id   = $value['acc_grp_id'];
    				$group_name = $value['acc_grp_name'];
    				$balance = 0;
    				$credit_total = 0;
    				$debit_total = 0;
                    if($view == 2){
						$final2[] = [
							'group_id'	 	=> $group_id,
							'group_name' 	=> '&nbsp;&nbsp; &raquo; '.$group_name,
							'balance'	 	=> '',
							'balance_d'	 	=> '',
							'type'			=> 'grp',
							'style'			=> 'font-weight:500;'
						];
					 }

			  		//check accounts
					$builder = $this->db->table('accttxnmst a');
					$builder->select("
						a.acc_id,
						acctmst.acc_name,
						SUM(CASE WHEN a.acc_txn_date < '$from_date' AND a.acc_txn_type=1 AND a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END) AS total_frmdr,
						SUM(CASE WHEN a.acc_txn_date < '$from_date' AND a.acc_txn_type=1 AND a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END) AS total_frmcr,
						SUM(CASE WHEN a.acc_txn_date <= '$to_date' AND a.acc_txn_type=1 AND a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END) AS total_todr,
						SUM(CASE WHEN a.acc_txn_date <= '$to_date' AND a.acc_txn_type=1 AND a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END) AS total_tocr
					");

					$builder->join("$account_master_tbl acctmst", 'acctmst.acc_id = a.acc_id');
					$builder->join("undercrsmt undercrsmt", "undercrsmt.crs_mst_id = a.acc_id AND undercrsmt.crs_mst_type =1 AND undercrsmt.cmp_id =$this->company_id", 'left');
					$builder->where('undercrsmt.under_crs_mst_id', $value['acc_grp_id']);
					$builder->where('a.acc_txn_type', 1);
					$builder->where('undercrsmt.cmpfymastr_id', $this->fy_id);
					$builder->where('undercrsmt.cmpfymastr_id IS NOT NULL');
					$builder->where('a.cmp_id', $this->company_id);
					if($consolidated==0)
					$builder->where('a.hobo_id', $this->bo_id);
					$builder->groupBy('a.acc_id, acctmst.acc_name');
					$builder->orderBy('acctmst.acc_name', 'asc');
					$results = $builder->get()->getResultArray();
					$credit_detail_total = 0;
    				$debit_detail_total  = 0;
					if($results){
						foreach ($results as $account_row) {
							$to_balance =(($account_row['total_dr'] ?? 0) - ($account_row['total_cr'] ?? 0)); 
							$fr_balance =(($account_row['total_frmdr'] ?? 0) - ($account_row['total_frmcr'] ?? 0)); 
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
							if($view == 2){
    						$final2[] = [
    							'group_id'	 	=> $account_row['acc_id'],
    							'group_name' 	=> '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &raquo;&raquo; '.$account_row['acc_name'],
    							'balance'	 	=> '',
								'balance_d'	 	=> $balance,
    							'type'			=> 'acc',
    							'style'			=> 'font-style:italic;'
    						];
						    }
						}
					}
					/************  Add Bill Sundry Account Check Latesr  ***************/
			
					//check sub group
					
					$builder =  $this->db->table("accgrpmstn acgrpmst");
					$builder->join("undercrsmt", "undercrsmt.under_crs_mst_id = acgrpmst.acc_grp_id AND undercrsmt.crs_mst_type =2 AND undercrsmt.cmp_id =$this->company_id", 'left');
					$builder->where('undercrsmt.under_main_id', $value['acc_grp_id']);
					$builder->where('undercrsmt.crs_mst_is_primary', 0);
					$builder->where('undercrsmt.cmpfymastr_id', $this->fy_id);
					$builder->where('undercrsmt.cmpfymastr_id IS NOT NULL');
					$result2 =  $builder->get()->getResultArray();
					if($result2)
    				{
    					foreach ($result2 as $key2 => $value2) {

    						$credit_detail_total = 0;
    						$debit_detail_total = 0;
							
							//check accounts
							$builder = $this->db->table('accttxnmst a');
							$builder->select("
								a.acc_id,
								acctmst.acc_name,
								SUM(CASE WHEN a.acc_txn_date < '$from_date' AND a.acc_txn_type=1 AND a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END) AS total_frmdr,
								SUM(CASE WHEN a.acc_txn_date < '$from_date' AND a.acc_txn_type=1 AND a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END) AS total_frmcr,
								SUM(CASE WHEN a.acc_txn_date <= '$to_date' AND a.acc_txn_type=1 AND a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END) AS total_todr,
								SUM(CASE WHEN a.acc_txn_date <= '$to_date' AND a.acc_txn_type=1 AND a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END) AS total_tocr
							");

							$builder->join("$account_master_tbl acctmst", 'acctmst.acc_id = a.acc_id');
							$builder->join("undercrsmt undercrsmt", "undercrsmt.crs_mst_id = a.acc_id AND undercrsmt.crs_mst_type =1 AND undercrsmt.cmp_id =$this->company_id", 'left');
							$builder->where('undercrsmt.under_crs_mst_id', $value2['acc_grp_id']);
						    $builder->where('undercrsmt.cmpfymastr_id', $this->fy_id);
							$builder->where('undercrsmt.cmpfymastr_id IS NOT NULL');
							$builder->where('a.acc_txn_type', 1);
							$builder->where('a.cmp_id', $this->company_id);
							$builder->where('a.hobo_id', $this->bo_id);
							$builder->groupBy('a.acc_id, acctmst.acc_name');
							$builder->orderBy('acctmst.acc_name', 'asc');

							$results = $builder->get()->getResultArray();
							
						   if($results){
							foreach ($results as $account_row) {
								$to_balance =(($account_row['total_dr'] ?? 0) - ($account_row['total_cr'] ?? 0)); 
								$fr_balance =(($account_row['total_frmdr'] ?? 0) - ($account_row['total_frmcr'] ?? 0)); 
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
								if($view == 2){
								$final2[] = [
									'group_id'	 	=> $value2['acc_grp_id'],
									'group_name' 	=> '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &raquo;&raquo; '.$value2['acc_grp_name'],
									'balance'	 	=> '',
									'balance_d'	 	=> $balance,
									'type'			=> 'grp',
									'style'			=> 'font-style:italic;'
								];
							   }
							}
						}
						//check sundry accounts
							}
						}
				
						$balance = $debit_total - $credit_total;
						$balance_total += $balance;
						if($view == 1){
						$final1[] = [
							'group_id'	 	=> $group_id,
							'group_name' 	=> '&nbsp;&nbsp; &raquo; '.$group_name,
							'balance'	 	=> $balance,
							'balance_d'	 	=> '',
							'type'			=> 'grp',
							'style'			=> '',
						];
						}
    			}
    		}
			
			$builder = $this->db->table($account_master_tbl.' actmst'); 
			$builder->join("undercrsmt undercrsmt", "undercrsmt.crs_mst_id = actmst.acc_id AND undercrsmt.crs_mst_type =1 AND undercrsmt.cmp_id =$this->company_id", 'left');
    		$builder->select(array('actmst.acc_id','actmst.acc_name','undercrsmt.under_crs_mst_id as acc_grp_id'));
    		$builder->where('undercrsmt.crs_mst_parent_id', $acc_grp_parent_id);
			$builder->where('undercrsmt.cmpfymastr_id', $this->fy_id);
			$builder->where('undercrsmt.cmpfymastr_id IS NOT NULL');
			$builder->orderBy('actmst.acc_name', 'asc');
    		$result3 = $builder->get()->getResultArray();				
    		if($result3) 
    		{
    			foreach ($result3 as $key3 => $value3) 
    			{
    				$group_id     = $value3['acc_id'];
    				$group_name   = $value3['acc_name'];
    				$balance      = 0;
    				$credit_total = 0;
    				$debit_total  = 0;
    				$op_balance   = 0;
    				$fr_balance   = 0;
    				$to_balance   = 0;
    				$op_balance   = $this->acc_opn_balance_info($value3['acc_id'],$consolidated);    				
				    //check last transaction- from  - To 
					$res_from   = $this->getAccountBalancePL($value3['acc_id'], $from_date, true,$consolidated);
					$fr_balance = $res_from ? (($res_from->total_dr ?? 0) - ($res_from->total_cr ?? 0)) : $op_balance;
					$res_to     = $this->getAccountBalancePL($value3['acc_id'], $to_date, false,$consolidated);
  	  		    	$to_balance = $res_to ? (($res_to->total_dr ?? 0) - ($res_to->total_cr ?? 0)) : $op_balance;
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
								'balance_d'	 	=> '',
								'type'			=> 'acc',
								'style'			=> ''								
						       ];
				    				
					$final2[] = [
							'group_id'	 	=> $group_id,
							'group_name' 	=> '&nbsp;&nbsp; &raquo; '.$group_name,
							'balance'	 	=> '',
							'balance_d'	 	=> $balance,
							'type'			=> 'acc',
							'style'			=> 'font-weight:500;'
						 ];
					
    			}
    		} 

    		/********* Bilsundry Daat is penijng ***********/
			if($view == 0){
				$final = [0 => [
					'group_id'	 	=> $parent['acc_grp_parent_id'],
					'group_name' 	=> $parent['acc_grp_parent_name'],
					'balance'	 	=> $balance_total,	
					'balance_d'	 	=> '',	
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
 
   function get_parent_group_details($acc_grp_parent_id, $view, $from_date, $to_date,$consolidated)
  {
	     if($view==2){
	      $group_ids = $this->getAllSubGroupIds($acc_grp_parent_id);		
		  $final_ids = array_merge([$acc_grp_parent_id],$group_ids);		
		  $parent_sub_parent_sum = $this->GetParentBalance($final_ids,$from_date, $to_date,$consolidated);
		}
		else 
			$parent_sub_parent_sum=0;
		
		$sub_groups_ids=[];
    	$final = [];
    	$final1 = [];
    	$final2 = [];
    	$acc_grp_par_tbl = 'grpparentn';
    	$parent =  $this->db->table($acc_grp_par_tbl)->where('acc_grp_parent_id', $acc_grp_parent_id)->get()->getRowArray();
    	
    	if($parent)
    	{
			
    		$final1[] = [
    			'group_id'	 	=> $parent['acc_grp_parent_id'],
    			'group_name' 	=> $parent['acc_grp_parent_name'],
    			'balance'	 	=> '',
				'balance_d'	 	=> '',
    			'type'			=> 'prt',
    			'style'			=> 'font-weight:bold;'
    		];
			
		 	
    		$final2[] = [
    			'group_id'	 	=> $parent['acc_grp_parent_id'],
    			'group_name' 	=> $parent['acc_grp_parent_name'],
    			'balance'	 	=> $parent_sub_parent_sum,
				'balance_d'	 	=> '',
    			'type'			=> 'prt',
    			'style'			=> 'font-weight:bold;'
    		];
          
    		$account_grp_tbl = 'accgrpmstn';
    		$account_master_tbl = 'acctmaster';
    		
    		$builder =  $this->db->table("accgrpmstn acgrpmst");
			$builder->join("undercrsmt", "undercrsmt.crs_mst_id = acgrpmst.acc_grp_id AND undercrsmt.crs_mst_type =2 AND undercrsmt.cmp_id =$this->company_id", 'left');
     		$builder->where('undercrsmt.crs_mst_parent_id', $acc_grp_parent_id);
     		$builder->where('undercrsmt.crs_mst_is_primary', 1);
			$builder->where('undercrsmt.cmpfymastr_id', $this->fy_id);
    		$result =  $builder->get()->getResultArray();
    		SaveErrorLog("<br />Start of accgrpmstn =>".$this->db->getlastquery());
			$balance_total = 0;
    		if($result) 
    		{
    			foreach ($result as $key => $value) 
    			{
    				$group_id   = $value['acc_grp_id'];
    				$group_name = $value['acc_grp_name'];
    				$balance = 0;
    				$credit_total = 0;
    				$debit_total = 0;
					$debit_detail_total=0;
					$credit_detail_total=0;
				    $sub_groups_ids[]=$group_id;
    				$final2[] = [
    					'group_id'	 	=> $group_id,
    					'group_name' 	=> '&nbsp;&nbsp; &raquo; '.$group_name,
    					'balance'	 	=> '',
						'balance_d'	 	=> '',
    					'type'			=> 'grp',
    					'style'			=> 'font-weight:500;'
    				];

			  		//check accounts
					$builder = $this->db->table($account_master_tbl.' actmst'); 
					$builder->join("undercrsmt undercrsmt", "undercrsmt.crs_mst_id = actmst.acc_id AND undercrsmt.crs_mst_type =1 AND undercrsmt.cmp_id =$this->company_id", 'left');
					$builder->select(array('actmst.acc_id','actmst.acc_name','undercrsmt.under_crs_mst_id as acc_grp_id'));
					$builder->where('undercrsmt.under_crs_mst_id', $value['acc_grp_id']);							
					$builder->where('undercrsmt.cmpfymastr_id', $this->fy_id);
					$builder->orderBy('actmst.acc_name', 'asc');
					$accounts = $builder->get()->getResultArray();
					SaveErrorLog("<br />Start of account master iunder group ".$value['acc_grp_id']."==>".$this->db->getlastquery());
					if($accounts){
    					foreach ($accounts as $account) {
    						$credit_detail_total = 0;
    						$debit_detail_total = 0;
							
							$builder = $this->db->table('accttxnmst a');
						    $builder->select("
								SUM(CASE WHEN a.acc_txn_date >= '$from_date' AND a.acc_txn_date <= '$to_date' AND a.acc_txn_type=1 AND a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END) AS total_dr,
								SUM(CASE WHEN a.acc_txn_date >= '$from_date' AND a.acc_txn_date <= '$to_date' AND a.acc_txn_type=1 AND a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END) AS total_cr
							");
							$builder->where('a.acc_id',$account['acc_id']);	
							$builder->where('a.acc_txn_type', 1);
							$builder->where('a.cmp_id', $this->company_id);
							
							if($consolidated==0)
							$builder->where('a.hobo_id', $this->bo_id);
							$builder->groupBy('a.acc_id');							
							$transaction = $builder->get()->getRowArray();
							if($transaction){
								$acc_balance =(($transaction['total_dr'] ?? 0) - ($transaction['total_cr'] ?? 0)); 
								
							if($acc_balance < 0){
										$credit_total += abs($acc_balance);
										$credit_detail_total = abs($acc_balance);
									}
									if($acc_balance >= 0){
										$debit_total += $acc_balance;
										$debit_detail_total = $acc_balance;
									}
							}
    						$balance = $debit_detail_total - $credit_detail_total;
    						$final2[] = [
    							'group_id'	 	=> $account['acc_id'],
    							'group_name' 	=> '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &raquo;&raquo; '.$account['acc_name'],
    							'balance'	 	=> '',
								'balance_d'	 	=> $balance,
    							'type'			=> 'acc',
    							'style'			=> 'font-style:italic;'

    						];
    					 }
    				  }
					  
				//check sub group					
				$builder =  $this->db->table("accgrpmstn acgrpmst");
				$builder->join("undercrsmt", "undercrsmt.crs_mst_id = acgrpmst.acc_grp_id AND undercrsmt.crs_mst_type =2 AND undercrsmt.cmp_id =$this->company_id", 'left');
				$builder->where('undercrsmt.under_main_id', $value['acc_grp_id']);
				$builder->where('undercrsmt.crs_mst_is_primary', 0);
				$builder->where('undercrsmt.cmpfymastr_id', $this->fy_id);
				$result2 =  $builder->get()->getResultArray();
				SaveErrorLog("<br />Checking sub  group ".$value['acc_grp_id']."==>".$this->db->getlastquery());
				if($result2)
				{
				  foreach ($result2 as $key2 => $value2) {
					    $sub_groups_ids[]=$value2['acc_grp_id'];
						$credit_detail_total = 0;
						$debit_detail_total  = 0;						
						$builder = $this->db->table($account_master_tbl.' actmst'); 
						$builder->join("undercrsmt undercrsmt", "undercrsmt.crs_mst_id = actmst.acc_id AND undercrsmt.crs_mst_type =1 AND undercrsmt.cmp_id =$this->company_id", 'left');
						$builder->select(array('actmst.acc_id','actmst.acc_name','undercrsmt.under_crs_mst_id as acc_grp_id'));
						$builder->where('undercrsmt.under_crs_mst_id', $value2['acc_grp_id']);							
						$builder->where('undercrsmt.cmpfymastr_id', $this->fy_id);
						$builder->orderBy('actmst.acc_name', 'asc');
						$accounts = $builder->get()->getResultArray();
						SaveErrorLog("<br />Start of sub group account master iunder group ".$value['acc_grp_id']."==>".$this->db->getlastquery());
						if($accounts){
							foreach ($accounts as $account) {
								$credit_detail_total = 0;
								$debit_detail_total = 0;
								
								$builder = $this->db->table('accttxnmst a');
								$builder->select("
									SUM(CASE WHEN a.acc_txn_date >= '$from_date' AND a.acc_txn_date <= '$to_date' AND a.acc_txn_type=1 AND a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END) AS total_dr,
									SUM(CASE WHEN a.acc_txn_date >= '$from_date' AND a.acc_txn_date <= '$to_date' AND a.acc_txn_type=1 AND a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END) AS total_cr
								");
								$builder->where('a.acc_id',$account['acc_id']);	
								$builder->where('a.acc_txn_type', 1);
								$builder->where('a.cmp_id', $this->company_id);
								if($consolidated==0)
								$builder->where('a.hobo_id', $this->bo_id);
								$builder->groupBy('a.acc_id');							
								$transaction = $builder->get()->getRowArray();
								if($transaction){
									$acc_balance =(($transaction['total_dr'] ?? 0) - ($transaction['total_cr'] ?? 0)); 
									
								if($acc_balance < 0){
											$credit_total += abs($acc_balance);
											$credit_detail_total = abs($acc_balance);
										}
										if($acc_balance >= 0){
											$debit_total += $acc_balance;
											$debit_detail_total = $acc_balance;
										}
								}
								$balance = $debit_detail_total - $credit_detail_total;
								$final2[] = [
									'group_id'	 	=> $value2['acc_grp_id'],
									'group_name' 	=> '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp; &raquo;&raquo; '.$value2['acc_grp_name'],
									'balance'	 	=> '',
									'balance_d'	 	=> $balance,
									'type'			=> 'grp',
									'style'			=> 'font-style:italic;'
								];
							}
						}
					}
				}
    				$balance = $debit_total - $credit_total;
    				$balance_total += $balance;
    				$final1[] = [
    					'group_id'	 	=> $group_id,
    					'group_name' 	=> '&nbsp;&nbsp; &raquo; '.$group_name,
    					'balance'	 	=> $balance,
						'balance_d'	 	=> '',
    					'type'			=> 'grp',
    					'style'			=> '',
    				];
    			}
    		}
		    //acc_grp_parent_id acount masters 
			if($sub_groups_ids)
			$sub_group_ids = array_unique($sub_groups_ids);
		    $builder = $this->db->table($account_master_tbl.' actmst'); 
			$builder->join("undercrsmt undercrsmt", "undercrsmt.crs_mst_id = actmst.acc_id AND undercrsmt.crs_mst_type =1 AND undercrsmt.cmp_id =$this->company_id", 'left');
			$builder->select(array('actmst.acc_id','actmst.acc_name','undercrsmt.under_crs_mst_id as acc_grp_id'));
			
			$builder->where('undercrsmt.crs_mst_parent_id', $acc_grp_parent_id);
			$builder->where('undercrsmt.crs_mst_type', 1);
			$builder->where('undercrsmt.cmpfymastr_id', $this->fy_id);
			$builder->orderBy('actmst.acc_name', 'asc');
			$result3 = $builder->get()->getResultArray();
			SaveErrorLog("<br />Start of parent account master under parent ".$acc_grp_parent_id."==>".$this->db->getlastquery());
			//echo $this->db->getlastquery();
			//echo '<br >';
			if($result3) 
				{
					foreach ($result3 as $key3 => $value3) 
					{
						$group_id     = $value3['acc_id'];
						$group_name   = $value3['acc_name'];
						$balance      = 0;
						$credit_total = 0;
						$debit_total  = 0;						
						$builder      = $this->db->table('accttxnmst a');
					    $builder->select("
							SUM(CASE WHEN a.acc_txn_date >= '$from_date' AND a.acc_txn_date <= '$to_date' AND a.acc_txn_type=1 AND a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END) AS total_dr,
							SUM(CASE WHEN a.acc_txn_date >= '$from_date' AND a.acc_txn_date <= '$to_date' AND a.acc_txn_type=1 AND a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END) AS total_cr
					    ");
						$builder->where('a.acc_id',$value3['acc_id']);	
						$builder->where('a.acc_txn_type', 1);
						$builder->where('a.cmp_id', $this->company_id);
						if($consolidated==0)
						$builder->where('a.hobo_id', $this->bo_id);
						$builder->groupBy('a.acc_id');					
						$transaction = $builder->get()->getRowArray();
						if($transaction){
							$acc_balance =(($transaction['total_dr'] ?? 0) - ($transaction['total_cr'] ?? 0)); 
							
						if($acc_balance < 0){
									$credit_total += abs($acc_balance);
									$credit_detail_total = abs($acc_balance);
								}
								if($acc_balance >= 0){
									$debit_total += $acc_balance;
									$debit_detail_total = $acc_balance;
								}
						}	
						$balance = $debit_total - $credit_total;
						$balance_total += $balance;
						$final1[] = [
							'group_id'	 	=> $group_id,
							'group_name' 	=> '&nbsp;&nbsp; &raquo; '.$group_name,
							'balance'	 	=> $balance,
							'balance_d'	 	=> '',
							'type'			=> 'acc',
							'style'			=> '',
						 ];

						$final2[] = [
							'group_id'	 	=> $group_id,
							'group_name' 	=> '&nbsp;&nbsp; &raquo; '.$group_name,
							'balance'	 	=> '',
							'balance_d'	 	=> $balance,
							'type'			=> 'acc',
							'style'			=> 'font-weight:500;'
						 ];	
						
					}
				}

    		$final = [0 => [
    			'group_id'	 	=> $parent['acc_grp_parent_id'],
    			'group_name' 	=> $parent['acc_grp_parent_name'],
    			'balance'	 	=> $balance_total,
				'balance_d'	 	=> '',
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

public function legacy_load_profit_loss_horizontal($view, $from_date, $to_date, $nil_type, $consolidated)
{
    /*
     * VIEWS
     *  - 0: Condensed (existing behaviour)
     *  - 1: Schedules (primary groups + accounts directly under each P&L parent; totals include full subtree)
     *  - 2: Detailed  (Schedules + list accounts under each group collected from the whole subtree)
     *
     * LEFT (Debit) P&L parents:   11=Purchases, 7=Direct Expenses, 13=Indirect Expenses
     * RIGHT (Credit) P&L parents:  8=Sales,     10=Direct Income,  12=Indirect Income
     */

    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date   = date('Y-m-d', strtotime($to_date));

    $LEFT_STAGE1  = [11, 7];
    $RIGHT_STAGE1 = [8, 10];
    $LEFT_STAGE2  = [13];
    $RIGHT_STAGE2 = [12];
    $ALL_PARENTS  = array_unique(array_merge($LEFT_STAGE1, $RIGHT_STAGE1, $LEFT_STAGE2, $RIGHT_STAGE2));

    $fmt = function ($n) { return ($n !== '' && $n != 0) ? formatAmount($n) : ''; };

    if ((int)$view === 0) {
        $left  = [];
        $right = [];

        $opnStock = $this->openingStockTotal(
            $from_date,
            $this->company_id,
            $this->session->get('ses_comp_fy_id'),
            $this->session->get('ses_dflt_val_method')
        );
        $left[] = [
            'l_parent_id'     => 0,
            'l_group_id'      => 0,
            'l_group_name'    => 'Opening Stock',
            'l_balance'       => $fmt($opnStock),
            'l_balance_total' => $opnStock !== '' ? $opnStock : 0,
            'l_type'          => 'opn',
            'l_style'         => 'font-weight:bold;'
        ];

        $pushParentTotals = function (int $parentId, string $side) use ($from_date, $to_date, $consolidated, $fmt, &$left, &$right) {
            $rows = $this->get_parent_group_details_pl($parentId, 0, $from_date, $to_date, $consolidated);
            if (!$rows) return;
            foreach ($rows as $row) {
                if (!isset($row['type']) || $row['type'] !== 'prt') continue;
                $bal = parseAmount($row['balance'] ?? 0);
                if ($side === 'L') {
                    $left[] = [
                        'l_parent_id'     => $parentId,
                        'l_group_id'      => $row['group_id'],
                        'l_group_name'    => $row['group_name'],
                        'l_balance'       => $fmt($bal),
                        'l_balance_total' => $bal,
                        'l_type'          => $row['type'],
                        'l_style'         => $row['style'] ?? '',
                    ];
                } else {
                    $right[] = [
                        'r_parent_id'     => $parentId,
                        'r_group_id'      => $row['group_id'],
                        'r_group_name'    => $row['group_name'],
                        'r_balance'       => $fmt(-$bal),
                        'r_balance_total' => -$bal,
                        'r_type'          => $row['type'],
                        'r_style'         => $row['style'] ?? '',
                    ];
                }
            }
        };

        $pushParentTotals(11, 'L');
        $pushParentTotals(7,  'L');
        $pushParentTotals(8,  'R');
        $pushParentTotals(10, 'R');

        $clsStock = $this->StockStatusModel->closingStockTotal($from_date, $to_date);
        $right[] = [
            'r_parent_id'     => 0,
            'r_group_id'      => 0,
            'r_group_name'    => 'Closing Stock',
            'r_balance'       => $fmt($clsStock),
            'r_balance_total' => $clsStock !== '' ? $clsStock : 0,
            'r_type'          => 'clo',
            'r_style'         => 'font-weight:bold;',
        ];

        $final = [];
        $count = max(count($left), count($right));
        for ($i=0; $i<$count; $i++) {
            $l = $left[$i] ?? [];
            $r = $right[$i] ?? [];
            if ($l || $r) {
                $row = array_merge($l, $r);
                $l_style = isset($row['l_style']) ? ['style' => $row['l_style']] : [];
                $r_style = isset($row['r_style']) ? ['style' => $row['r_style']] : [];
                $row['pq_cellattr'] = [
                    'r_balance' => [
                        'data-group_name' => $row['r_group_name'] ?? '',
                        'data-group_id'   => $row['r_group_id']   ?? 0,
                        'data-dataIndx'   => 'r_group_name',
                        'data-id'         => $row['r_group_id']   ?? 0,
                        'data-type'       => $row['r_type']       ?? '',
                    ],
                    'l_balance' => [
                        'data-group_name' => $row['l_group_name'] ?? '',
                        'data-group_id'   => $row['l_group_id']   ?? 0,
                        'data-dataIndx'   => 'l_group_name',
                        'data-id'         => $row['l_group_id']   ?? 0,
                        'data-type'       => $row['l_type']       ?? '',
                    ],
                    'l_group_name' => $l_style,
                    'r_group_name' => $r_style
                ];
                $row['pq_cellcls'] = ['l_balance'=>'hover-cell','r_balance'=>'hover-cell'];
                $final[] = $row;
            }
        }

        $l_total = array_sum(array_column($left,  'l_balance_total'));
        $r_total = array_sum(array_column($right, 'r_balance_total'));

        $l_diff = 0; $r_diff = 0; $gross_total = max($l_total, $r_total);
        if ($l_total > $r_total) { $r_diff = $l_total - $r_total; }
        elseif ($l_total < $r_total) { $l_diff = $r_total - $l_total; }

        $final[] = [
            'l_group_id'      => 0,
            'l_group_name'    => 'Gross Profit C/F',
            'l_balance'       => $fmt($l_diff),
            'l_balance_total' => $l_diff,
            'r_group_id'      => 0,
            'r_group_name'    => 'Gross Loss C/F',
            'r_balance'       => $fmt($r_diff),
            'r_balance_total' => $r_diff,
        ];
        $final[] = [
            'l_group_id'      => 0,
            'l_group_name'    => '',
            'l_balance'       => $fmt($gross_total),
            'l_balance_total' => $gross_total,
            'r_group_id'      => 0,
            'r_group_name'    => '',
            'r_balance'       => $fmt($gross_total),
            'r_balance_total' => $gross_total,
            'step'            => 0,
            'pq_rowattr'      => ['style' => 'background:#E6E6FA;font-weight:bold;']
        ];

        $left  = [];
        $right = [];
        $left[]  = ['l_group_id'=>0,'l_group_name'=>'Gross Loss B/D','l_balance'=>$fmt($r_diff),'l_balance_total'=>$r_diff];
        $right[] = ['r_group_id'=>0,'r_group_name'=>'Gross Profit B/D','r_balance'=>$fmt($l_diff),'r_balance_total'=>$l_diff];

        $pushParentTotals(13, 'L');
        $pushParentTotals(12, 'R');

        $count = max(count($left), count($right));
        for ($i=0; $i<$count; $i++) {
            $l = $left[$i] ?? [];
            $r = $right[$i] ?? [];
            if ($l || $r) {
                $row = array_merge($l, $r);
                $row['pq_cellattr'] = [
                    'r_balance' => [
                        'data-group_name' => $row['r_group_name'] ?? '',
                        'data-group_id'   => $row['r_group_id']   ?? 0,
                        'data-dataIndx'   => 'r_group_name',
                        'data-id'         => $row['r_group_id']   ?? 0,
                        'data-type'       => $row['r_type']       ?? '',
                    ],
                    'l_balance' => [
                        'data-group_name' => $row['l_group_name'] ?? '',
                        'data-group_id'   => $row['l_group_id']   ?? 0,
                        'data-dataIndx'   => 'l_group_name',
                        'data-id'         => $row['l_group_id']   ?? 0,
                        'data-type'       => $row['l_type']       ?? '',
                    ],
                ];
                $row['pq_cellcls'] = ['l_balance'=>'hover-cell','r_balance'=>'hover-cell'];
                $final[] = $row;
            }
        }

        $l_total2 = array_sum(array_column($left,  'l_balance_total'));
        $r_total2 = array_sum(array_column($right, 'r_balance_total'));
        $np_left  = 0; $nl_right = 0;
        if ($l_total2 > $r_total2) { $nl_right = $l_total2 - $r_total2; }
        elseif ($l_total2 < $r_total2) { $np_left = $r_total2 - $l_total2; }

        $final[] = [
            'l_group_id'      => 0,
            'l_group_name'    => 'Net Profit C/D',
            'l_balance'       => $fmt($np_left),
            'l_balance_total' => $np_left,
            'r_group_id'      => 0,
            'r_group_name'    => 'Net Loss C/D',
            'r_balance'       => $fmt($nl_right),
            'r_balance_total' => $nl_right,
        ];
        return $final;
    }

    $gRows = $this->db->table('accgrpmstn g')
        ->select('g.acc_grp_id, g.acc_grp_name, u.crs_mst_is_primary, u.under_crs_mst_id, u.crs_mst_parent_id')
        ->join("undercrsmt u", "u.crs_mst_id = g.acc_grp_id AND u.crs_mst_type = 2 AND u.cmp_id = {$this->company_id}", 'left')
        ->where('u.cmpfymastr_id', $this->fy_id)
        ->where('u.cmpfymastr_id IS NOT NULL')
        ->orderBy('g.acc_grp_name','asc')
        ->get()->getResultArray();

    $groupInfo = [];
    $children  = [];
    $groupsByParentCat = [];
    foreach ($gRows as $r) {
        $gid        = (int)$r['acc_grp_id'];
        $name       = $r['acc_grp_name'];
        $isPrimary  = (int)$r['crs_mst_is_primary'];
        $parentGid  = (int)($r['under_crs_mst_id'] ?? 0);
        $parentCat  = (int)($r['crs_mst_parent_id'] ?? 0);

        $groupInfo[$gid] = [
            'name'       => $name,
            'is_primary' => $isPrimary,
            'parent_gid' => $parentGid,
            'parent_cat' => $parentCat,
        ];
        if ($parentGid > 0) {
            if (!isset($children[$parentGid])) $children[$parentGid] = [];
            $children[$parentGid][] = $gid;
        }
        if ($parentCat > 0 && in_array($parentCat, $ALL_PARENTS, true)) {
            if (!isset($groupsByParentCat[$parentCat])) $groupsByParentCat[$parentCat] = [];
            $groupsByParentCat[$parentCat][] = $gid;
        }
    }

    $isInTree = [];
    $checkTree = function (int $gid) use (&$checkTree, &$isInTree, $groupInfo, $ALL_PARENTS): bool {
        if (isset($isInTree[$gid])) return $isInTree[$gid];
        $info = $groupInfo[$gid] ?? null;
        if (!$info) return $isInTree[$gid] = false;
        if (in_array($info['parent_cat'], $ALL_PARENTS, true)) {
            return $isInTree[$gid] = true;
        }
        $pg = $info['parent_gid'] ?? 0;
        if ($pg <= 0) return $isInTree[$gid] = false;
        return $isInTree[$gid] = $checkTree($pg);
    };
    foreach (array_keys($groupInfo) as $gid) {
        $checkTree($gid);
    }
    $validGroupIds = array_keys(array_filter($isInTree, fn($v) => $v));
    if (empty($validGroupIds)) $validGroupIds = [0];

    $accMasterTable = 'acctmaster';
    try {
        if (method_exists($this->db, 'tableExists') && !$this->db->tableExists('acctmstn')) {
            $accMasterTable = 'acctmaster';
        }
    } catch (\Throwable $e) {
        $accMasterTable = 'acctmaster';
    }

    $accRowsRaw = $this->db->table($accMasterTable . ' a')
        ->select('a.acc_id, a.acc_name, ua.under_crs_mst_id, ua.crs_mst_parent_id')
        ->join("undercrsmt ua", "ua.crs_mst_id = a.acc_id AND ua.cmp_id = {$this->company_id} AND ua.crs_mst_type IN (1,14)", 'left')
        ->where("
            (ua.under_crs_mst_id IN (" . implode(',', $validGroupIds) . ")
             OR (ua.under_crs_mst_id = 0 AND ua.crs_mst_parent_id IN (" . implode(',', $ALL_PARENTS) . ")))
        ", null, false)
        ->where('ua.cmpfymastr_id', $this->fy_id)
        ->where('ua.cmpfymastr_id IS NOT NULL')
        ->orderBy('a.acc_name', 'ASC')
        ->get()->getResultArray();

    $accSeen = [];
    $accountsByGroup = [];
    $primaryAccByCat = [];
    $allAccIds       = [];
    $accNameById     = [];

    $resolvePrimaryParentCat = function (string $accName, int $parentCat): int {
        $n = strtoupper(trim($accName));

        if (strpos($n, 'FOC UNDER RCM') !== false || strpos($n, 'FOC EXPENSE') !== false) {
            return 7;
        }

        if (
            strpos($n, 'PURCHASE ACCOUNT') !== false ||
            strpos($n, 'OPENING STOCK') !== false ||
            preg_match('/\bPURCHASE\b/', $n)
        ) {
            return 11;
        }

        return $parentCat;
    };

    foreach ($accRowsRaw as $a) {
        $accId    = (int)$a['acc_id'];
        if (isset($accSeen[$accId])) {
            continue;
        }
        $accSeen[$accId] = true;

        $accName  = $a['acc_name'];
        $underGid = (int)$a['under_crs_mst_id'];
        $parentCat= (int)$a['crs_mst_parent_id'];

        $allAccIds[] = $accId;
        $accNameById[$accId] = $accName;

        if ($underGid === 0) {
            $resolvedParentCat = $resolvePrimaryParentCat($accName, $parentCat);

            if (!isset($primaryAccByCat[$resolvedParentCat])) {
                $primaryAccByCat[$resolvedParentCat] = [];
            }
            $primaryAccByCat[$resolvedParentCat][$accId] = ['id'=>$accId,'name'=>$accName];
        } else {
            if (!isset($accountsByGroup[$underGid])) $accountsByGroup[$underGid] = [];
            $accountsByGroup[$underGid][] = $accId;
        }
    }

    foreach ($primaryAccByCat as $catId => $map) {
        $primaryAccByCat[$catId] = array_values($map);
    }

    $opByAcc = [];
    if (!empty($allAccIds)) {
        $opQB = $this->db->table('accoppybal ob')
            ->select('ob.acc_id, SUM(ob.acc_op_bal) AS op_bal', false)
            ->where('ob.cmp_id', $this->company_id)
            ->where('ob.cmpfymastr_id', $this->fy_id)
            ->where('ob.bsd_id IS NULL', null, false)
            ->whereIn('ob.acc_id', $allAccIds)
            ->groupBy('ob.acc_id');
        if ((int)$consolidated === 0) { $opQB->where('ob.hobo_id', $this->bo_id); }
        foreach ($opQB->get()->getResultArray() as $r) {
            $opByAcc[(int)$r['acc_id']] = (float)($r['op_bal'] ?? 0);
        }
    }

    $txByAcc = [];
    if (!empty($allAccIds)) {
        $txQB = $this->db->table('accttxnmst a');
        $txQB->select("
            a.acc_id,
            SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END) AS dr,
            SUM(CASE WHEN a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END) AS cr
        ", false)
        ->where('a.acc_txn_type', 1)
        ->where('a.cmp_id', $this->company_id)
        ->whereIn('a.acc_id', $allAccIds)
        ->where('a.vch_txn_id > 0', null, false)
        ->where('a.acc_txn_date >=', $from_date)
        ->where('a.acc_txn_date <=', $to_date)
        ->groupBy('a.acc_id');

        if ((int)$consolidated === 0) {
            $txQB->where('a.hobo_id', $this->bo_id);
        }

        foreach ($txQB->get()->getResultArray() as $t) {
            $txByAcc[(int)$t['acc_id']] = [
                'dr' => (float)($t['dr'] ?? 0),
                'cr' => (float)($t['cr'] ?? 0),
            ];
        }
    }

    $closingByAcc = [];
    foreach ($allAccIds as $aid) {
        $op = $opByAcc[$aid] ?? 0.0;
        $dr = $txByAcc[$aid]['dr'] ?? 0.0;
        $cr = $txByAcc[$aid]['cr'] ?? 0.0;
        $closingByAcc[$aid] = $op + ($dr - $cr);
    }

    $sumCache = [];
    $sumGroupClosings = null;
    $sumGroupClosings = function (int $gid) use (&$sumGroupClosings, &$sumCache, $children, $accountsByGroup, $closingByAcc): float {
        if (isset($sumCache[$gid])) return $sumCache[$gid];
        $sum = 0.0;
        if (!empty($accountsByGroup[$gid])) {
            foreach ($accountsByGroup[$gid] as $aid) {
                $sum += $closingByAcc[$aid] ?? 0.0;
            }
        }
        if (!empty($children[$gid])) {
            foreach ($children[$gid] as $cg) {
                $sum += $sumGroupClosings($cg);
            }
        }
        $sumCache[$gid] = $sum;
        return $sum;
    };

    $descCache = [];
    $collectDesc = null;
    $collectDesc = function (int $gid) use (&$collectDesc, &$descCache, $children): array {
        if (isset($descCache[$gid])) return $descCache[$gid];
        $out = [$gid];
        if (!empty($children[$gid])) {
            foreach ($children[$gid] as $cg) {
                $out = array_merge($out, $collectDesc($cg));
            }
        }
        $descCache[$gid] = $out;
        return $out;
    };

    $leftRows  = [];
    $rightRows = [];

    $opnStock = $this->openingStockTotal(
        $from_date,
        $this->company_id,
        $this->session->get('ses_comp_fy_id'),
        $this->session->get('ses_dflt_val_method')
    );
    $leftRows[] = [
        'l_group_id'      => 0,
        'l_group_name'    => 'Opening Stock',
        'l_balance'       => $fmt($opnStock),
        'l_balance_total' => $opnStock ?: 0,
        'l_type'          => 'opn',
        'l_style'         => 'font-weight:bold;'
    ];

    $sideAmount = function (float $signedClosing, string $side) {
        return ($side === 'L') ? $signedClosing : -$signedClosing;
    };

    $emitCat = function (int $catId, string $catName, string $side) use (&$leftRows, &$rightRows) {
        if ($side === 'L') {
            $leftRows[] = [
                'l_group_id'=> $catId,
                'l_group_name'=>$catName,
                'l_balance'=>'',
                'l_balance_total'=>0,
                'l_type'=>'grp',
                'l_style'=>'font-weight:bold;'
            ];
        } else {
            $rightRows[] = [
                'r_group_id'=> $catId,
                'r_group_name'=>$catName,
                'r_balance'=>'',
                'r_balance_total'=>0,
                'r_type'=>'grp',
                'r_style'=>'font-weight:bold;'
            ];
        }
    };

    $emitRow = function (array $row, string $side) use (&$leftRows, &$rightRows) {
        if ($side === 'L') { $leftRows[] = $row; } else { $rightRows[] = $row; }
    };

    $buildParentBlock = function (int $parentId, string $side) use (
        $view, $nil_type, $fmt, $sideAmount, $emitRow, $emitCat,
        $groupInfo, $groupsByParentCat, $sumGroupClosings, $collectDesc,
        $accountsByGroup, $closingByAcc, $primaryAccByCat, $accNameById
    ) {
        $catName = ($side === 'L')
            ? ($parentId === 11 ? 'Purchase' : ($parentId === 7 ? 'Direct Expenses' : 'Indirect Expenses'))
            : ($parentId === 8  ? 'Sales'    : ($parentId === 10 ? 'Direct Income'   : 'Indirect Income'));

        $emitCat($parentId, $catName, $side);

        $gids = $groupsByParentCat[$parentId] ?? [];
        foreach ($gids as $gid) {
            $gName = $groupInfo[$gid]['name'];
            $gTotSigned = $sumGroupClosings($gid);
            $amt = $sideAmount($gTotSigned, $side);

            if (!((int)$nil_type === 0 && $amt == 0.0)) {
                $emitRow([
                    $side === 'L' ? 'l_group_id'      : 'r_group_id'       => $gid,
                    $side === 'L' ? 'l_group_name'    : 'r_group_name'     => '&nbsp;&nbsp;» ' . $gName,
                    $side === 'L' ? 'l_balance'       : 'r_balance'        => $fmt($amt),
                    $side === 'L' ? 'l_balance_total' : 'r_balance_total'  => $amt,
                    $side === 'L' ? 'l_detail'        : 'r_detail'         => '',
                    $side === 'L' ? 'l_type'          : 'r_type'           => 'grp',
                    $side === 'L' ? 'l_style'         : 'r_style'          => ($view==2)?'font-weight:bold;':'',
                ], $side);
            }

            if ((int)$view === 2) {
                $allDesc = $collectDesc($gid);
                $accIds  = [];
                foreach ($allDesc as $dgid) {
                    if (!empty($accountsByGroup[$dgid])) {
                        foreach ($accountsByGroup[$dgid] as $aid) $accIds[] = $aid;
                    }
                }
                if (!empty($accIds)) {
                    foreach ($accIds as $aid) {
                        $signed = $closingByAcc[$aid] ?? 0.0;
                        $aAmt   = $sideAmount($signed, $side);
                        if ((int)$nil_type === 0 && $aAmt == 0.0) continue;

                        $accName = $accNameById[$aid] ?? ('Acc#'.$aid);
                        if ($side === 'L') {
                            $emitRow([
                                'l_group_id'       => $aid,
                                'l_group_name'     => '&nbsp;&nbsp;&nbsp;&nbsp;»» ' . $accName,
                                'l_detail'         => $fmt($aAmt),
                                'l_balance'        => '',
                                'l_balance_total'  => 0,
                                'l_type'           => 'acc',
                            ], 'L');
                        } else {
                            $emitRow([
                                'r_group_id'       => $aid,
                                'r_group_name'     => '&nbsp;&nbsp;&nbsp;&nbsp;»» ' . $accName,
                                'r_detail'         => $fmt($aAmt),
                                'r_balance'        => '',
                                'r_balance_total'  => 0,
                                'r_type'           => 'acc',
                            ], 'R');
                        }
                    }
                }
            }
        }

        $paList = $primaryAccByCat[$parentId] ?? [];
        foreach ($paList as $pa) {
            $aid = (int)$pa['id'];
            $signed = $closingByAcc[$aid] ?? 0.0;
            $amt    = $sideAmount($signed, $side);
            if ((int)$nil_type === 0 && $amt == 0.0) continue;

            $emitRow([
                $side === 'L' ? 'l_group_id'      : 'r_group_id'       => $aid,
                $side === 'L' ? 'l_group_name'    : 'r_group_name'     => '&nbsp;&nbsp;» ' . $pa['name'] . ' <sub><em>(Primary Account)</em></sub>',
                $side === 'L' ? 'l_balance'       : 'r_balance'        => $fmt($amt),
                $side === 'L' ? 'l_balance_total' : 'r_balance_total'  => $amt,
                $side === 'L' ? 'l_detail'        : 'r_detail'         => '',
                $side === 'L' ? 'l_type'          : 'r_type'           => 'acc',
            ], $side);
        }
    };

    $buildParentBlock(11, 'L');
    $buildParentBlock(7,  'L');
    $buildParentBlock(8,  'R');
    $buildParentBlock(10, 'R');

    $clsStock = $this->StockStatusModel->closingStockTotal($from_date, $to_date);
    $rightRows[] = [
        'r_group_id'      => 0,
        'r_group_name'    => 'Closing Stock',
        'r_balance'       => $fmt($clsStock),
        'r_balance_total' => $clsStock ?: 0,
        'r_type'          => 'clo',
        'r_style'         => 'font-weight:bold;',
        'r_detail'        => '',
    ];

    $final = [];
    $count = max(count($leftRows), count($rightRows));
    for ($i=0; $i<$count; $i++) {
        $l = $leftRows[$i] ?? [];
        $r = $rightRows[$i] ?? [];
        if ($l || $r) {
            $row = array_merge($l, $r);
            $l_style = isset($row['l_style']) ? ['style' => $row['l_style']] : [];
            $r_style = isset($row['r_style']) ? ['style' => $row['r_style']] : [];
            if (!empty($l_style) || !empty($r_style)) {
                $row['pq_cellattr']['l_group_name'] = $l_style;
                $row['pq_cellattr']['r_group_name'] = $r_style;
            }
            $row['pq_cellcls'] = [
                'l_balance'=>'hover-cell','r_balance'=>'hover-cell',
                'l_detail' =>'hover-cell','r_detail' =>'hover-cell'
            ];
            $final[] = $row;
        }
    }
    $l_total = array_sum(array_column($leftRows,  'l_balance_total'));
    $r_total = array_sum(array_column($rightRows, 'r_balance_total'));

    $gp_left = 0; $gl_right = 0;
    if ($l_total > $r_total) { $gl_right = $l_total - $r_total; }
    elseif ($l_total < $r_total) { $gp_left = $r_total - $l_total; }

    $final[] = [
        'l_group_id'      => 0,
        'l_group_name'    => 'Gross Profit C/F',
        'l_balance'       => $fmt($gp_left),
        'l_balance_total' => $gp_left,
        'r_group_id'      => 0,
        'r_group_name'    => 'Gross Loss C/F',
        'r_balance'       => $fmt($gl_right),
        'r_balance_total' => $gl_right,
    ];

    $leftRows  = [['l_group_id'=>0,'l_group_name'=>'Gross Loss B/D','l_balance'=>$fmt($gl_right),'l_balance_total'=>$gl_right,'l_detail'=>'']];
    $rightRows = [['r_group_id'=>0,'r_group_name'=>'Gross Profit B/D','r_balance'=>$fmt($gp_left),'r_balance_total'=>$gp_left,'r_detail'=>'']];

    $buildParentBlock(13, 'L');
    $buildParentBlock(12, 'R');

    $count = max(count($leftRows), count($rightRows));
    for ($i=0; $i<$count; $i++) {
        $l = $leftRows[$i] ?? [];
        $r = $rightRows[$i] ?? [];
        if ($l || $r) {
            $row = array_merge($l, $r);
            $l_style = isset($row['l_style']) ? ['style' => $row['l_style']] : [];
            $r_style = isset($row['r_style']) ? ['style' => $row['r_style']] : [];
            if (!empty($l_style) || !empty($r_style)) {
                $row['pq_cellattr']['l_group_name'] = $l_style;
                $row['pq_cellattr']['r_group_name'] = $r_style;
            }
            $row['pq_cellcls'] = [
                'l_balance'=>'hover-cell','r_balance'=>'hover-cell',
                'l_detail' =>'hover-cell','r_detail' =>'hover-cell'
            ];
            $final[] = $row;
        }
    }
    $l_total2 = array_sum(array_column($leftRows,  'l_balance_total'));
    $r_total2 = array_sum(array_column($rightRows, 'r_balance_total'));

    $np_left = 0; $nl_right = 0;
    if ($l_total2 > $r_total2) { $nl_right = $l_total2 - $r_total2; }
    elseif ($l_total2 < $r_total2) { $np_left = $r_total2 - $l_total2; }

    $final[] = [
        'l_group_id'      => 0,
        'l_group_name'    => 'Net Profit C/D',
        'l_balance'       => $fmt($np_left),
        'l_balance_total' => $np_left,
        'r_group_id'      => 0,
        'r_group_name'    => 'Net Loss C/D',
        'r_balance'       => $fmt($nl_right),
        'r_balance_total' => $nl_right,
    ];

    return $final;
}

/* small helper to print account name in Detailed (avoid another query on every row) */
private function getAccountName($accId)
{
    static $cache = [];
    if (isset($cache[$accId])) return $cache[$accId];
    $row = $this->db->table('acctmaster')->select('acc_name')->where('acc_id', $accId)->get()->getRowArray();
    $cache[$accId] = $row['acc_name'] ?? ('Account #'.$accId);
    return $cache[$accId];
}

public function 	GroupInfo($group_id){
	  return $this->db->table('accgrpmstn')
            ->select('acc_grp_id,acc_grp_name')
            ->where('cmp_id',$this->company_id)
			->where('acc_grp_parent_id',$group_id)
			->get()->getRowArray();
   }	
   
   public function legacy_load_profit_loss_vertical($view, $from_date, $to_date, $nil_type, $consolidated)
{
    /*
     * VIEWS
     *  0: Condensed  (always show parents; show ₹0.00 too)
     *  1: Schedules  (primary groups + primary accounts; totals include full subtree)
     *  2: Detailed   (Schedules + list accounts under each primary group from the whole subtree;
     *                 NOTE: group rows are headings only, no amount → avoids double counting)
     */

    $from_date = date('Y-m-d', strtotime($from_date));
    $to_date   = date('Y-m-d', strtotime($to_date));

    $fmtAlways = function ($n) { return formatAmount((float)$n); };
    $fmtHide0  = function ($n) { $n=(float)$n; return $n==0.0 ? '' : formatAmount($n); };

    $hdrRow = function (string $title) {
        return [
            'group_id'    => 0,
            'group_name'  => $title,
            'type'        => 'hdr',
            'balance'     => '',
            'pq_rowattr'  => ['style' => 'background:#E6E6FA;font-weight:bold;'],
            'pq_cellattr' => [],
            'pq_cellcls'  => [],
        ];
    };

    $makeRow = function (string $name, float $amount, string $type = 'prt', int $id = 0, string $style = '', bool $always = true) use ($fmtAlways, $fmtHide0) {
        $bal = $always ? $fmtAlways($amount) : $fmtHide0($amount);
        return [
            'group_id'    => $id,
            'group_name'  => $name,
            'type'        => $type,
            'balance'     => $bal,
            'pq_rowattr'  => $style ? ['style' => $style] : [],
            'pq_cellattr' => [
                'balance' => [
                    'data-group_name' => $name,
                    'data-group_id'   => $id,
                    'data-dataIndx'   => 'group_name',
                    'data-id'         => $id,
                    'data-type'       => $type,
                ]
            ],
            'pq_cellcls'  => ['balance' => 'hover-cell'],
        ];
    };

    /* ============================================================== */
    /* CONDENSED                                                      */
    /* ============================================================== */
    if ((int)$view === 0) {

        $left1  = [];
        $right1 = [];

        $opnStock = $this->openingStockTotal(
            $from_date,
            $this->company_id,
            $this->session->get('ses_comp_fy_id'),
            $this->session->get('ses_dflt_val_method')
        );
        $left1[] = ['name' => 'Opening Stock', 'amt' => (float)$opnStock, 'id'=>0, 'type'=>'opn'];

        $pushParentTotals = function (int $parentId, string $side) use (&$left1, &$right1, $from_date, $to_date, $consolidated) {
            $rows = $this->get_parent_group_details_pl($parentId, 0, $from_date, $to_date, $consolidated);
            if (!$rows) return;
            foreach ($rows as $row) {
                if (!isset($row['type']) || $row['type'] !== 'prt') continue;
                $bal  = parseAmount($row['balance'] ?? 0);
                $name = $row['group_name'] ?? '';
                $id   = (int)($row['group_id'] ?? 0);
                if ($side === 'L')  $left1[]  = ['name'=>$name, 'amt'=>(float)$bal,   'id'=>$id, 'type'=>'prt'];
                else                $right1[] = ['name'=>$name, 'amt'=>(float)(-$bal),'id'=>$id, 'type'=>'prt'];
            }
        };

        $pushParentTotals(11, 'L');
        $pushParentTotals(7,  'L');
        $pushParentTotals(8,  'R');
        $pushParentTotals(10, 'R');

        $clsStock = (float)$this->StockStatusModel->closingStockTotal($from_date, $to_date);
        $right1[] = ['name'=>'Closing Stock','amt'=>$clsStock, 'id'=>0, 'type'=>'clo'];

        $l_total = array_sum(array_column($left1,  'amt'));
        $r_total = array_sum(array_column($right1, 'amt'));
        $l_diff  = 0.0;
        $r_diff  = 0.0;
        if ($l_total > $r_total)       $r_diff = $l_total - $r_total;
        elseif ($l_total < $r_total)   $l_diff = $r_total - $l_total;

        $left2  = [];
        $right2 = [];
        if ($r_diff > 0) $left2[]  = ['name'=>'Gross Loss B/D',  'amt'=>$r_diff, 'id'=>0,'type'=>'gr_bd'];
        if ($l_diff > 0) $right2[] = ['name'=>'Gross Profit B/D','amt'=>$l_diff, 'id'=>0,'type'=>'gp_bd'];

        $pushParentTotals2 = function (int $parentId, string $side) use (&$left2, &$right2, $from_date, $to_date, $consolidated) {
            $rows = $this->get_parent_group_details_pl($parentId, 0, $from_date, $to_date, $consolidated);
            if (!$rows) return;
            foreach ($rows as $row) {
                if (!isset($row['type']) || $row['type'] !== 'prt') continue;
                $bal  = parseAmount($row['balance'] ?? 0);
                $name = $row['group_name'] ?? '';
                $id   = (int)($row['group_id'] ?? 0);
                if ($side === 'L')  $left2[]  = ['name'=>$name, 'amt'=>(float)$bal,   'id'=>$id, 'type'=>'prt'];
                else                $right2[] = ['name'=>$name, 'amt'=>(float)(-$bal),'id'=>$id, 'type'=>'prt'];
            }
        };

        $pushParentTotals2(13, 'L');
        $pushParentTotals2(12, 'R');

        $l_total2 = array_sum(array_column($left2,  'amt'));
        $r_total2 = array_sum(array_column($right2, 'amt'));
        $np_left  = 0.0;
        $nl_right = 0.0;
        if ($l_total2 > $r_total2)       $nl_right = $l_total2 - $r_total2;
        elseif ($l_total2 < $r_total2)   $np_left  = $r_total2 - $l_total2;

        $final = [];

        $final[] = $hdrRow('CREDITS');
        foreach ($right1 as $r) $final[] = $makeRow($r['name'], $r['amt'], $r['type'], $r['id'], '', true);
        $final[] = $makeRow('Gross Loss C/F', $r_diff, 'gr_cf', 0, '', true);

        $final[] = $hdrRow('DEBITS');
        foreach ($left1 as $l)  $final[] = $makeRow($l['name'], $l['amt'], $l['type'], $l['id'], '', true);
        $final[] = $makeRow('Gross Profit C/F', $l_diff, 'gp_cf', 0, '', true);

        $final[] = $hdrRow('CREDITS');
        foreach ($right2 as $r) $final[] = $makeRow($r['name'], $r['amt'], $r['type'], $r['id'], '', true);
        $final[] = $makeRow('Net Loss C/D', $nl_right, 'nl_cd', 0, '', true);

        $final[] = $hdrRow('DEBITS');
        foreach ($left2 as $l)  $final[] = $makeRow($l['name'], $l['amt'], $l['type'], $l['id'], '', true);
        $final[] = $makeRow('Net Profit C/D', $np_left, 'np_cd', 0, 'background:#e9ffe9;', true);

        return $final;
    }

    /* ============================================================== */
    /* SCHEDULES & DETAILED                                           */
    /* ============================================================== */

    $PL_LEFT  = [11 => 'Purchase', 7 => 'Direct Expenses', 13 => 'Indirect Expenses'];
    $PL_RIGHT = [ 8 => 'Sales',    10 => 'Direct Income', 12 => 'Indirect Income'];
    $ALL_PARENTS = array_unique(array_merge(array_keys($PL_LEFT), array_keys($PL_RIGHT)));

    $gRows = $this->db->table('accgrpmstn g')
        ->select('g.acc_grp_id, g.acc_grp_name, u.crs_mst_is_primary, u.under_crs_mst_id, u.crs_mst_parent_id')
        ->join("undercrsmt u", "u.crs_mst_id = g.acc_grp_id AND u.crs_mst_type = 2 AND u.cmp_id = {$this->company_id}", 'left')
        ->where('u.cmpfymastr_id', $this->fy_id)
        ->where('u.cmpfymastr_id IS NOT NULL')
        ->whereIn('u.crs_mst_parent_id', $ALL_PARENTS)
        ->orderBy('g.acc_grp_name','asc')
        ->get()->getResultArray();

    $groupInfo = [];
    $children  = [];
    $primaryByParentCat = [];
    $allGroupIds = [];

    foreach ($gRows as $r) {
        $gid        = (int)$r['acc_grp_id'];
        $name       = $r['acc_grp_name'];
        $isPrimary  = (int)$r['crs_mst_is_primary'];
        $parentGid  = (int)($r['under_crs_mst_id'] ?? 0);
        $parentCat  = (int)($r['crs_mst_parent_id'] ?? 0);
        $allGroupIds[$gid] = true;

        $groupInfo[$gid] = [
            'name'       => $name,
            'is_primary' => $isPrimary,
            'parent_gid' => $parentGid,
            'parent_cat' => $parentCat,
        ];
        if ($parentGid > 0) {
            if (!isset($children[$parentGid])) $children[$parentGid] = [];
            $children[$parentGid][] = $gid;
        }
        if ($isPrimary === 1 && $parentCat > 0) {
            if (!isset($primaryByParentCat[$parentCat])) $primaryByParentCat[$parentCat] = [];
            $primaryByParentCat[$parentCat][] = $gid;
        }
    }

    $validGroupIds = array_keys($allGroupIds);
    if (empty($validGroupIds)) $validGroupIds = [0];

    $accMasterTable = 'acctmaster';
    try {
        if (method_exists($this->db, 'tableExists') && !$this->db->tableExists('acctmstn')) {
            $accMasterTable = 'acctmaster';
        }
    } catch (\Throwable $e) {
        $accMasterTable = 'acctmaster';
    }

    $accRowsRaw = $this->db->table($accMasterTable . ' a')
        ->select('a.acc_id, a.acc_name, ua.under_crs_mst_id, ua.crs_mst_parent_id')
        ->join("undercrsmt ua", "ua.crs_mst_id = a.acc_id AND ua.cmp_id = {$this->company_id} AND ua.crs_mst_type IN (1,14)", 'left')
        ->where("
            (ua.under_crs_mst_id IN (" . implode(',', $validGroupIds) . ")
             OR (ua.under_crs_mst_id = 0 AND ua.crs_mst_parent_id IN (" . implode(',', $ALL_PARENTS) . ")))
        ", null, false)
        ->where('ua.cmpfymastr_id', $this->fy_id)
        ->where('ua.cmpfymastr_id IS NOT NULL')
        ->orderBy('a.acc_name', 'ASC')
        ->get()->getResultArray();

    $accSeen = [];
    $accountsByGroup = [];
    $primaryAccByCat = [];
    $allAccIds       = [];
    $accNameById     = [];

    $resolvePrimaryParentCat = function (string $accName, int $parentCat): int {
        $n = strtoupper(trim($accName));

        if (strpos($n, 'FOC UNDER RCM') !== false || strpos($n, 'FOC EXPENSE') !== false) {
            return 7;
        }

        if (
            strpos($n, 'PURCHASE ACCOUNT') !== false ||
            strpos($n, 'OPENING STOCK') !== false ||
            preg_match('/\bPURCHASE\b/', $n)
        ) {
            return 11;
        }

        return $parentCat;
    };

    foreach ($accRowsRaw as $a) {
        $accId = (int)$a['acc_id'];
        if (isset($accSeen[$accId])) {
            continue;
        }
        $accSeen[$accId] = true;

        $accName  = $a['acc_name'];
        $underGid = (int)$a['under_crs_mst_id'];
        $parentCat= (int)$a['crs_mst_parent_id'];

        $allAccIds[] = $accId;
        $accNameById[$accId] = $accName;

        if ($underGid === 0) {
            $resolvedParentCat = $resolvePrimaryParentCat($accName, $parentCat);

            if (!isset($primaryAccByCat[$resolvedParentCat])) {
                $primaryAccByCat[$resolvedParentCat] = [];
            }
            $primaryAccByCat[$resolvedParentCat][$accId] = ['id'=>$accId,'name'=>$accName];
        } else {
            if (!isset($accountsByGroup[$underGid])) $accountsByGroup[$underGid] = [];
            $accountsByGroup[$underGid][] = $accId;
        }
    }

    foreach ($primaryAccByCat as $catId => $map) {
        $primaryAccByCat[$catId] = array_values($map);
    }

    $opByAcc = [];
    if (!empty($allAccIds)) {
        $opQB = $this->db->table('accoppybal ob')
            ->select('ob.acc_id, SUM(ob.acc_op_bal) AS op_bal', false)
            ->where('ob.cmp_id', $this->company_id)
            ->where('ob.cmpfymastr_id', $this->fy_id)
            ->where('ob.bsd_id IS NULL', null, false)
            ->whereIn('ob.acc_id', $allAccIds)
            ->groupBy('ob.acc_id');
        if ((int)$consolidated === 0) { $opQB->where('ob.hobo_id', $this->bo_id); }
        foreach ($opQB->get()->getResultArray() as $r) {
            $opByAcc[(int)$r['acc_id']] = (float)($r['op_bal'] ?? 0);
        }
    }

    $txByAcc = [];
    if (!empty($allAccIds)) {
        $txQB = $this->db->table('accttxnmst a');
        $txQB->select("
            a.acc_id,
            SUM(CASE WHEN a.acc_txn_dr_cr = 1 THEN a.acc_txn_amt ELSE 0 END) AS dr,
            SUM(CASE WHEN a.acc_txn_dr_cr = 2 THEN a.acc_txn_amt ELSE 0 END) AS cr
        ", false)
        ->where('a.acc_txn_type', 1)
        ->where('a.cmp_id', $this->company_id)
        ->whereIn('a.acc_id', $allAccIds)
        ->where('a.vch_txn_id >0', null, false)
        ->where('a.acc_txn_date >=', $from_date)
        ->where('a.acc_txn_date <=', $to_date)
        ->groupBy('a.acc_id');
        if ((int)$consolidated === 0) { $txQB->where('a.hobo_id', $this->bo_id); }
        foreach ($txQB->get()->getResultArray() as $t) {
            $txByAcc[(int)$t['acc_id']] = [
                'dr' => (float)($t['dr'] ?? 0),
                'cr' => (float)($t['cr'] ?? 0),
            ];
        }
    }

    $closingByAcc = [];
    foreach ($allAccIds as $aid) {
        $op = $opByAcc[$aid] ?? 0.0;
        $dr = $txByAcc[$aid]['dr'] ?? 0.0;
        $cr = $txByAcc[$aid]['cr'] ?? 0.0;
        $closingByAcc[$aid] = $op + ($dr - $cr);
    }

    $sumCache = [];
    $sumGroupClosings = null;
    $sumGroupClosings = function (int $gid) use (&$sumGroupClosings, &$sumCache, $children, $accountsByGroup, $closingByAcc): float {
        if (isset($sumCache[$gid])) return $sumCache[$gid];
        $sum = 0.0;
        if (!empty($accountsByGroup[$gid])) {
            foreach ($accountsByGroup[$gid] as $aid) $sum += $closingByAcc[$aid] ?? 0.0;
        }
        if (!empty($children[$gid])) {
            foreach ($children[$gid] as $cg) $sum += $sumGroupClosings($cg);
        }
        return $sumCache[$gid] = $sum;
    };

    $descCache = [];
    $collectDesc = null;
    $collectDesc = function (int $gid) use (&$collectDesc, &$descCache, $children): array {
        if (isset($descCache[$gid])) return $descCache[$gid];
        $out = [$gid];
        if (!empty($children[$gid])) {
            foreach ($children[$gid] as $cg) $out = array_merge($out, $collectDesc($cg));
        }
        return $descCache[$gid] = $out;
    };

    $left1 = [];
    $right1 = [];
    $left2 = [];
    $right2 = [];

    $sideAmount = function (float $signed, string $side) {
        return ($side === 'L') ? $signed : -$signed;
    };

    $emitParent = function (int $parentId, string $side) use (
        $view, $nil_type, $groupInfo, $primaryByParentCat, $sumGroupClosings, $collectDesc,
        $accountsByGroup, $closingByAcc, $primaryAccByCat, $sideAmount, $PL_LEFT, $PL_RIGHT,
        &$left1, &$right1, &$left2, &$right2, $accNameById
    ) {
        $catName = $side === 'L' ? ($PL_LEFT[$parentId] ?? '') : ($PL_RIGHT[$parentId] ?? '');
        $asList  = ($parentId === 11 || $parentId === 7)
            ? ($side === 'L' ? 'left1' : 'right1')
            : ($side === 'L' ? 'left2' : 'right2');

        ${$asList}[] = ['name'=>$catName,'amt'=>0.0,'id'=>0,'type'=>'prt_hd','style'=>'font-weight:bold;'];

        foreach (($primaryByParentCat[$parentId] ?? []) as $gid) {
            $gName  = $groupInfo[$gid]['name'];
            $signed = $sumGroupClosings($gid);
            $amt    = $sideAmount($signed, $side);

            $displayAmt = ((int)$view === 1) ? $amt : 0.0;

            $skip = false;
            if ((int)$view === 1 && (int)$nil_type === 0 && $displayAmt == 0.0) {
                $skip = true;
            }
            if (!$skip) {
                ${$asList}[] = ['name'=>'» '.$gName,'amt'=>$displayAmt,'id'=>$gid,'type'=>'grp','style'=>''];
            }

            if ((int)$view === 2) {
                $accIds = [];
                foreach ($collectDesc($gid) as $dgid) {
                    if (!empty($accountsByGroup[$dgid])) {
                        foreach ($accountsByGroup[$dgid] as $aid) $accIds[] = $aid;
                    }
                }
                foreach ($accIds as $aid) {
                    $signedA = $closingByAcc[$aid] ?? 0.0;
                    $aAmt    = $sideAmount($signedA, $side);
                    if ((int)$nil_type === 0 && $aAmt == 0.0) continue;
                    $accName = $accNameById[$aid] ?? ('Acc#'.$aid);
                    ${$asList}[] = ['name'=>'»» '.$accName,'amt'=>$aAmt,'id'=>$aid,'type'=>'acc','style'=>''];
                }
            }
        }

        foreach (($primaryAccByCat[$parentId] ?? []) as $pa) {
            $aid    = (int)$pa['id'];
            $signed = $closingByAcc[$aid] ?? 0.0;
            $amt    = $sideAmount($signed, $side);
            if ((int)$nil_type === 0 && $amt == 0.0) continue;
            ${$asList}[] = [
                'name'=>'» '.$pa['name'].' <sub><em>(Primary Account)</em></sub>',
                'amt'=>$amt,
                'id'=>$aid,
                'type'=>'acc',
                'style'=>''
            ];
        }
    };

    $left1[] = [
        'name'=>'Opening Stock',
        'amt'=>(float)$this->openingStockTotal(
            $from_date,
            $this->company_id,
            $this->session->get('ses_comp_fy_id'),
            $this->session->get('ses_dflt_val_method')
        ),
        'id'=>0,
        'type'=>'opn',
        'style'=>'font-weight:bold;'
    ];

    $emitParent(11, 'L');
    $emitParent(7,  'L');
    $emitParent(8,  'R');
    $emitParent(10, 'R');

    $right1[] = [
        'name'=>'Closing Stock',
        'amt'=>(float)$this->StockStatusModel->closingStockTotal($from_date,$to_date),
        'id'=>0,
        'type'=>'clo',
        'style'=>'font-weight:bold;'
    ];

    $l_total = array_sum(array_column($left1,  'amt'));
    $r_total = array_sum(array_column($right1, 'amt'));
    $l_diff  = 0.0;
    $r_diff  = 0.0;
    if ($l_total > $r_total)       $r_diff = $l_total - $r_total;
    elseif ($l_total < $r_total)   $l_diff = $r_total - $l_total;

    if ($r_diff > 0) $left2[]  = ['name'=>'Gross Loss B/D',  'amt'=>$r_diff, 'id'=>0, 'type'=>'gr_bd','style'=>''];
    if ($l_diff > 0) $right2[] = ['name'=>'Gross Profit B/D','amt'=>$l_diff, 'id'=>0, 'type'=>'gp_bd','style'=>''];

    $emitParent(13, 'L');
    $emitParent(12, 'R');

    $l_total2 = array_sum(array_column($left2,  'amt'));
    $r_total2 = array_sum(array_column($right2, 'amt'));
    $np_left  = 0.0;
    $nl_right = 0.0;
    if ($l_total2 > $r_total2)       $nl_right = $l_total2 - $r_total2;
    elseif ($l_total2 < $r_total2)   $np_left  = $r_total2 - $l_total2;

    $final = [];

    $final[] = $hdrRow('CREDITS');
    foreach ($right1 as $r) $final[] = $makeRow($r['name'], $r['amt'], $r['type'], $r['id'], $r['style'] ?? '', false);
    $final[] = $makeRow('Gross Loss C/F', $r_diff, 'gr_cf', 0, '', false);

    $final[] = $hdrRow('DEBITS');
    foreach ($left1 as $l)  $final[] = $makeRow($l['name'], $l['amt'], $l['type'], $l['id'], $l['style'] ?? '', false);
    $final[] = $makeRow('Gross Profit C/F', $l_diff, 'gp_cf', 0, '', false);

    $final[] = $hdrRow('CREDITS');
    foreach ($right2 as $r) $final[] = $makeRow($r['name'], $r['amt'], $r['type'], $r['id'], $r['style'] ?? '', false);
    $final[] = $makeRow('Net Loss C/D', $nl_right, 'nl_cd', 0, '', false);

    $final[] = $hdrRow('DEBITS');
    foreach ($left2 as $l)  $final[] = $makeRow($l['name'], $l['amt'], $l['type'], $l['id'], $l['style'] ?? '', false);
    $final[] = $makeRow('Net Profit C/D', $np_left, 'np_cd', 0, 'background:#e9ffe9;', false);

    return $final;
}

  

}	
?>