<?php
namespace App\Models\Admin;
use CodeIgniter\Model;
use App\Libraries\externaldb;
use App\Models\Admin\VouchersModel;

class RegistersModel extends Model	{

    public function __construct() {
        parent::__construct();        
       $this->externaldb    = new externaldb();	
	   $this->session       = \Config\Services::session();	   
	   $this->VouchersModel  = new VouchersModel();
	   $this->company_id    =  $this->session->get('ses_company_id');
       $this->bo_id         = $this->session->get('ses_boid');	
	   $this->univaictly    =  $this->externaldb->univaictly_db();
    }
		
	
	public function purchase_return_register_script(){
    // --------------------------------------------------
    // CONFIG
    // --------------------------------------------------
    $PURCHASE_RETURN_VCH_TYPES = [3]; // debit note / purchase return voucher type IDs
    $LOG_FILE = APPPATH . 'logs/backfill_purchase_return_registers.log';

    $company_id = $this->session->get('ses_company_id');
    $bo_id      = $this->session->get('ses_boid');

    $this->db->transBegin();
    $log = function($msg) use ($LOG_FILE) {
        @file_put_contents($LOG_FILE, '['.date('Y-m-d H:i:s').'] '.$msg.PHP_EOL, FILE_APPEND);
    };

    try {
        // ------------------------------------------------------------------
        // PHASE 1: ITEM REGISTER (only vouchers with items: vch_sub_type_id != 0)
        // ------------------------------------------------------------------
        $qb = $this->db->table('itemtxnmst si');
        $qb->select("
            si.vch_txn_id,
            si.txn_id,
            si.itm_id_unit_id,
            si.itm_txn_date,
            si.itm_txn_dr_cr,
            si.itm_txn_qty,
            si.itm_txn_rate,
            si.itm_txn_amt,
            si.mat_cent_id,
            si.hobo_id,
            si.itm_txn_type,
            v.vch_type_id,
            v.vch_sub_type_id,
            v.cmp_id,
            v.hobo_id AS v_hobo_id,
            ln.vch_long_narr
        ");
        $qb->join('vchtxnconso v', 'v.vch_txn_id = si.vch_txn_id', 'inner');
        $qb->join('vchlongnar ln', 'ln.vch_txn_id = si.vch_txn_id AND ln.cmp_id = v.cmp_id', 'left');
        $qb->whereIn('v.vch_type_id', $PURCHASE_RETURN_VCH_TYPES);
        $qb->where('v.vch_sub_type_id !=', 0); // only vouchers with items
        $qb->where('v.cmp_id', $company_id);
        $qb->where('si.cmp_id', $company_id);
        if (!empty($bo_id)) {
            $qb->where('v.hobo_id', $bo_id);
            $qb->where('si.hobo_id', $bo_id);
        }

        $rows = $qb->get()->getResultArray();
        $log("ITEM REG: fetched rows: ".count($rows));

        // header map
        $headerRows = [];
        foreach ($rows as $r) {
            $key = $r['vch_txn_id'].'|'.$r['txn_id'].'|'.$r['itm_id_unit_id'];
            $headerRows[$key] = [
                'itm_vch_type'   => $r['vch_type_id'],
                'cmp_id'         => $r['cmp_id'],
                'vch_txn_id'     => $r['vch_txn_id'],
                'txn_id'         => $r['txn_id'],
                'vch_date'       => $r['itm_txn_date'],
                'itm_id_unit_id' => $r['itm_id_unit_id'],
                'vch_narr'       => $r['vch_long_narr'] ?? '',
                'hobo_id'        => $r['hobo_id'],
                'itm_txn_type'   => $r['itm_txn_type'] ?? 1,
            ];
        }

        // existing headers
        $vchIds   = array_values(array_unique(array_column($rows, 'vch_txn_id')));
        $existing = [];
        if (!empty($vchIds)) {
            $q = $this->db->table('itmvchregn')
                ->select('itm_vch_reg_id, vch_txn_id, txn_id, itm_id_unit_id')
                ->where('cmp_id', $company_id)
                ->whereIn('vch_txn_id', $vchIds)
                ->get()->getResultArray();
            foreach ($q as $h) {
                $k = $h['vch_txn_id'].'|'.$h['txn_id'].'|'.$h['itm_id_unit_id'];
                $existing[$k] = $h['itm_vch_reg_id'];
            }
        }

        // insert / update headers
        $toInsert = [];
        $toUpdate = [];
        foreach ($headerRows as $k => $hdr) {
            if (isset($existing[$k])) {
                $hdr['itm_vch_reg_id'] = $existing[$k];
                $toUpdate[] = $hdr;
            } else {
                $toInsert[] = $hdr;
            }
        }
        if (!empty($toInsert)) {
            $this->db->table('itmvchregn')->insertBatch($toInsert);
            $newRows = $this->db->table('itmvchregn')
                ->select('itm_vch_reg_id, vch_txn_id, txn_id, itm_id_unit_id')
                ->where('cmp_id', $company_id)
                ->whereIn('vch_txn_id', $vchIds)
                ->get()->getResultArray();
            foreach ($newRows as $h) {
                $k = $h['vch_txn_id'].'|'.$h['txn_id'].'|'.$h['itm_id_unit_id'];
                $existing[$k] = $h['itm_vch_reg_id'];
            }
        }
        if (!empty($toUpdate)) {
            $this->db->table('itmvchregn')->updateBatch($toUpdate, 'itm_vch_reg_id');
        }
        $log("ITEM REG: headers inserted: ".count($toInsert).", updated: ".count($toUpdate));

        // refresh reg ids and wipe old details
        $regIds = array_values($existing);
        if (!empty($regIds)) {
            $this->db->table('itmvchregd')->whereIn('itm_vch_reg_id', $regIds)->delete();
        }

        // detail rows
        $detailInsert = [];
        foreach ($rows as $r) {
            $k     = $r['vch_txn_id'].'|'.$r['txn_id'].'|'.$r['itm_id_unit_id'];
            $regId = $existing[$k] ?? null;
            if (!$regId) continue;

            $isCr   = ((int)$r['itm_txn_dr_cr'] === 2); // 1=DR, 2=CR (purchase return: inventory goes CR)
            $dr_qty = $isCr ? 0 : ($r['itm_txn_qty'] ?? 0);
            $cr_qty = $isCr ? ($r['itm_txn_qty'] ?? 0) : 0;

            $detailInsert[] = [
                'itm_vch_reg_id' => $regId,
                'mat_cent_id'    => $r['mat_cent_id'],
                'itm_txn_dr_qty'  => $dr_qty,
                'itm_txn_dr_rate' => $isCr ? 0 : ($r['itm_txn_rate'] ?? 0),
                'itm_txn_dr_amt'  => $isCr ? 0 : ($r['itm_txn_amt'] ?? 0),
                'itm_txn_cr_qty'  => $cr_qty,
                'itm_txn_cr_rate' => $isCr ? ($r['itm_txn_rate'] ?? 0) : 0,
                'itm_txn_cr_amt'  => $isCr ? ($r['itm_txn_amt'] ?? 0) : 0,
                'vch_narr'        => $r['vch_long_narr'] ?? '',
            ];
        }
        if (!empty($detailInsert)) {
            $this->db->table('itmvchregd')->insertBatch($detailInsert);
        }
        $log("ITEM REG: details inserted: ".count($detailInsert));

        // ------------------------------------------------------------------
        // PHASE 2: ACCOUNT REGISTER (ALL vouchers: with or without items)
        // ------------------------------------------------------------------
        $acctRows = $this->db->table('accttxnmst a')
            ->select("
                a.vch_txn_id,
                a.txn_id,
                a.acc_id,
                a.acc_txn_date,
                a.acc_txn_dr_cr,
                a.acc_txn_amt,
                a.hobo_id,
                a.acc_txn_type,
                v.vch_type_id,
                v.vch_sub_type_id,
                v.cmp_id,
                v.hobo_id AS v_hobo_id,
                ln.vch_long_narr
            ")
            ->join('vchtxnconso v', 'v.vch_txn_id = a.vch_txn_id', 'inner')
            ->join('vchlongnar ln', 'ln.vch_txn_id = a.vch_txn_id AND ln.cmp_id = v.cmp_id', 'left')
            ->whereIn('v.vch_type_id', $PURCHASE_RETURN_VCH_TYPES)
            ->where('a.cmp_id', $company_id)
            ->where('a.acc_txn_type', 1)
            ->where('v.cmp_id', $company_id);
        if (!empty($bo_id)) {
            $acctRows->where('v.hobo_id', $bo_id);
            $acctRows->where('a.hobo_id', $bo_id);
        }
        $acctRows = $acctRows->get()->getResultArray();
        $log("ACCT REG: acct rows: ".count($acctRows));

        foreach ($acctRows as $row) {
            $isCr = ((int)$row['acc_txn_dr_cr'] === 2);
            $drAmt = $isCr ? 0 : (float)$row['acc_txn_amt'];   // purchase return: items/charges usually CR, party DR
            $crAmt = $isCr ? (float)$row['acc_txn_amt'] : 0;

            $regData = [
                'acct_vch_type'  => $row['vch_type_id'],
                'cmp_id'         => $company_id,
                'vch_txn_id'     => $row['vch_txn_id'],
                'txn_id'         => $row['txn_id'] ?? NULL, // can be NULL
                'vch_date'       => $row['acc_txn_date'],
                'acc_id'         => $row['acc_id'],
                'acc_txn_dr_amt' => $drAmt,
                'acc_txn_cr_amt' => $crAmt,
                'vch_narr'       => $row['vch_long_narr'] ?? '',
                'hobo_id'        => $row['hobo_id'],
                'acc_txn_type'   => $row['acc_txn_type'],
            ];

            $builder = $this->db->table('acctvchreg')
                ->select('acctvchregn_id')
                ->where('cmp_id', $company_id)
                ->where('vch_txn_id', $row['vch_txn_id'])
                ->where('acc_id', $row['acc_id']);

            if ($row['txn_id'] === null) {
                $builder->where('txn_id IS NULL', null, false);
            } else {
                $builder->where('txn_id', $row['txn_id']);
            }

            $existingReg = $builder->get()->getRowArray();

            if ($existingReg) {
                $this->db->table('acctvchreg')
                    ->where('acctvchregn_id', $existingReg['acctvchregn_id'])
                    ->update($regData);
            } else {
                $this->db->table('acctvchreg')->insert($regData);
            }
        }

        // Party row (txn_id NULL, total DR for the party in purchase return)
        $voucherRowsAll = $this->db->table('vchtxnconso v')
            ->select('v.vch_txn_id, v.vch_type_id, v.vch_date, v.cmp_id, v.hobo_id, ln.vch_long_narr')
            ->join('vchlongnar ln', 'ln.vch_txn_id = v.vch_txn_id AND ln.cmp_id = v.cmp_id', 'left')
            ->whereIn('v.vch_type_id', $PURCHASE_RETURN_VCH_TYPES)
            ->where('v.cmp_id', $company_id);
        if (!empty($bo_id)) $voucherRowsAll->where('v.hobo_id', $bo_id);
        $voucherRowsAll = $voucherRowsAll->get()->getResultArray();

        foreach ($voucherRowsAll as $vrow) {
            $vch_txn_id = $vrow['vch_txn_id'];

            $party_q = $this->db->table('cmptxnmstn')
                ->select('master_id')
                ->where('cmp_id', $company_id)
                ->where('vch_txn_id', $vch_txn_id)
                ->where('master_id_type', 'acc')
                ->orderBy('txn_id', 'asc')
                ->orderBy('vch_txn_id', 'asc')
                ->limit(1)
                ->get()->getRowArray();
            $party_id = $party_q['master_id'] ?? null;
            if (!$party_id) {
                $log("ACCT REG PARTY: skip vch_txn_id {$vch_txn_id} (no party found)");
                continue;
            }

            $acct_sums = $this->db->table('accttxnmst')
                ->select("
                    SUM(CASE WHEN acc_txn_dr_cr = 1 THEN acc_txn_amt ELSE 0 END) AS dr_sum,
                    MAX(acc_txn_type) AS acc_txn_type
                ")
                ->where('cmp_id', $company_id)
                ->where('vch_txn_id', $vch_txn_id)
                ->where('acc_txn_type', 1);
            if (!empty($bo_id)) $acct_sums->where('hobo_id', $bo_id);
            $acct_sums = $acct_sums->get()->getRowArray();
            $dr_sum      = (float)($acct_sums['dr_sum'] ?? 0);
            $acc_txn_type = (int)($acct_sums['acc_txn_type'] ?? 1);

            $partyData = [
                'acct_vch_type'  => $vrow['vch_type_id'],
                'cmp_id'         => $company_id,
                'vch_txn_id'     => $vch_txn_id,
                'txn_id'         => null,
                'vch_date'       => $vrow['vch_date'],
                'acc_id'         => $party_id,
                'acc_txn_dr_amt' => $dr_sum,  // debit party for purchase return
                'acc_txn_cr_amt' => 0,
                'vch_narr'       => $vrow['vch_long_narr'] ?? '',
                'hobo_id'        => $vrow['hobo_id'],
                'acc_txn_type'   => $acc_txn_type,
            ];

            $existingParty = $this->db->table('acctvchreg')
                ->select('acctvchregn_id')
                ->where('cmp_id', $company_id)
                ->where('vch_txn_id', $vch_txn_id)
                ->where('acc_id', $party_id)
                ->where('txn_id IS NULL', null, false)
                ->get()->getRowArray();

            if ($existingParty) {
                $this->db->table('acctvchreg')
                    ->where('acctvchregn_id', $existingParty['acctvchregn_id'])
                    ->update($partyData);
            } else {
                $this->db->table('acctvchreg')->insert($partyData);
            }
        }

        // Commit
        $this->db->transCommit();
        $log("Backfill completed OK");
        echo "Done. Item headers inserted: ".count($toInsert).", updated: ".count($toUpdate).", item details: ".count($detailInsert).", acct rows: ".count($acctRows);

    } catch (\Throwable $e) {
        $this->db->transRollback();
        $log("ERROR: ".$e->getMessage());
        echo "Error: ".$e->getMessage();
    }
}
    /************************  Start of Tool To Remap Sale Return Register Entries (ALL COMPANIES, NO DETAIL DELETE)  ********************/
public function sale_return_register_script_all()
{
    // --------------------------------------------------
    // CONFIG
    // --------------------------------------------------
    $SALES_RETURN_VCH_TYPES = [2]; // credit note / sales return voucher type IDs
    $LOG_FILE = APPPATH . 'logs/backfill_sale_return_registers_all.log';

    // Optional: limit to a specific BO; set to null to include all
    $bo_id = $this->session->get('ses_boid');

    $log = function($msg) use ($LOG_FILE) {
        @file_put_contents($LOG_FILE, '['.date('Y-m-d H:i:s').'] '.$msg.PHP_EOL, FILE_APPEND);
    };

    // Fetch all companies
    $companies = $this->db->table('cmpmastern')->select('cmp_id')->get()->getResultArray();
    if (empty($companies)) {
        echo "No companies found.\n";
        return;
    }

    foreach ($companies as $cRow) {
        $company_id = (int)$cRow['cmp_id'];
        $this->db->transBegin();

        try {
            $log("---- START company {$company_id} ----");

            // ------------------------------------------------------------------
            // PHASE 1: ITEM REGISTER (only vouchers with items: vch_sub_type_id != 0)
            // ------------------------------------------------------------------
            $qb = $this->db->table('itemtxnmst si');
            $qb->select("
                si.vch_txn_id,
                si.txn_id,
                si.itm_id_unit_id,
                si.itm_txn_date,
                si.itm_txn_dr_cr,
                si.itm_txn_qty,
                si.itm_txn_rate,
                si.itm_txn_amt,
                si.mat_cent_id,
                si.hobo_id,
                si.itm_txn_type,
                v.vch_type_id,
                v.vch_sub_type_id,
                v.cmp_id,
                v.hobo_id AS v_hobo_id,
                ln.vch_long_narr
            ");
            $qb->join('vchtxnconso v', 'v.vch_txn_id = si.vch_txn_id', 'inner');
            $qb->join('vchlongnar ln', 'ln.vch_txn_id = si.vch_txn_id AND ln.cmp_id = v.cmp_id', 'left');
            $qb->whereIn('v.vch_type_id', $SALES_RETURN_VCH_TYPES);
            $qb->where('v.vch_sub_type_id !=', 0); // only vouchers with items
            $qb->where('v.cmp_id', $company_id);
            $qb->where('si.cmp_id', $company_id);
            

            $rows = $qb->get()->getResultArray();
            $log("ITEM REG: fetched rows: ".count($rows));
            $headerRows = [];
            foreach ($rows as $r) {
                $key = $r['vch_txn_id'].'|'.$r['txn_id'].'|'.$r['itm_id_unit_id'];
                $headerRows[$key] = [
                    'itm_vch_type'   => $r['vch_type_id'],
                    'cmp_id'         => $r['cmp_id'],
                    'vch_txn_id'     => $r['vch_txn_id'],
                    'txn_id'         => $r['txn_id'],
                    'vch_date'       => $r['itm_txn_date'],
                    'itm_id_unit_id' => $r['itm_id_unit_id'],
                    'vch_narr'       => $r['vch_long_narr'] ?? '',
                    'hobo_id'        => $r['hobo_id'],
                    'itm_txn_type'   => $r['itm_txn_type'] ?? 1,
                ];
            }

            $vchIds   = array_values(array_unique(array_column($rows, 'vch_txn_id')));
            $existing = [];
            if (!empty($vchIds)) {
                $q = $this->db->table('itmvchregn')
                    ->select('itm_vch_reg_id, vch_txn_id, txn_id, itm_id_unit_id')
                    ->where('cmp_id', $company_id)
                    ->whereIn('vch_txn_id', $vchIds)
                    ->get()->getResultArray();
                foreach ($q as $h) {
                    $k = $h['vch_txn_id'].'|'.$h['txn_id'].'|'.$h['itm_id_unit_id'];
                    $existing[$k] = $h['itm_vch_reg_id'];
                }
            }

            $toInsert = [];
            $toUpdate = [];
            foreach ($headerRows as $k => $hdr) {
                if (isset($existing[$k])) {
                    $hdr['itm_vch_reg_id'] = $existing[$k];
                    $toUpdate[] = $hdr;
                } else {
                    $toInsert[] = $hdr;
                }
            }
            if (!empty($toInsert)) {
                $this->db->table('itmvchregn')->insertBatch($toInsert);
                $newRows = $this->db->table('itmvchregn')
                    ->select('itm_vch_reg_id, vch_txn_id, txn_id, itm_id_unit_id')
                    ->where('cmp_id', $company_id)
                    ->whereIn('vch_txn_id', $vchIds)
                    ->get()->getResultArray();
                foreach ($newRows as $h) {
                    $k = $h['vch_txn_id'].'|'.$h['txn_id'].'|'.$h['itm_id_unit_id'];
                    $existing[$k] = $h['itm_vch_reg_id'];
                }
            }
            if (!empty($toUpdate)) {
                $this->db->table('itmvchregn')->updateBatch($toUpdate, 'itm_vch_reg_id');
            }
            $log("ITEM REG: headers inserted: ".count($toInsert).", updated: ".count($toUpdate));

            $regIds = array_values($existing);

            // Build detail rows (no delete, we will upsert)
            $detailInsert = [];
            foreach ($rows as $r) {
                $k = $r['vch_txn_id'].'|'.$r['txn_id'].'|'.$r['itm_id_unit_id'];
                $regId = $existing[$k] ?? null;
                if (!$regId) continue;

                $isCr = ((int)$r['itm_txn_dr_cr'] === 2); // 1=DR, 2=CR
                $dr_qty  = $isCr ? 0 : ($r['itm_txn_qty'] ?? 0);
                $cr_qty  = $isCr ? ($r['itm_txn_qty'] ?? 0) : 0;
                $dr_rate = $isCr ? 0 : ($r['itm_txn_rate'] ?? 0);
                $cr_rate = $isCr ? ($r['itm_txn_rate'] ?? 0) : 0;
                $dr_amt  = $isCr ? 0 : ($r['itm_txn_amt'] ?? 0);
                $cr_amt  = $isCr ? ($r['itm_txn_amt'] ?? 0) : 0;

                $detailInsert[] = [
                    'itm_vch_reg_id' => $regId,
                    'mat_cent_id'    => $r['mat_cent_id'],
                    'itm_txn_dr_qty'  => $dr_qty,
                    'itm_txn_dr_rate' => $dr_rate,
                    'itm_txn_dr_amt'  => $dr_amt,
                    'itm_txn_cr_qty'  => $cr_qty,
                    'itm_txn_cr_rate' => $cr_rate,
                    'itm_txn_cr_amt'  => $cr_amt,
                    'vch_narr'        => $r['vch_long_narr'] ?? '',
                ];
            }

            // UPSERT detail rows (no delete). Key used: itm_vch_reg_id + mat_cent_id
            if (!empty($detailInsert)) {
                // Fetch existing detail rows for these headers
                $existingDet = [];
                if (!empty($regIds)) {
                    $detRows = $this->db->table('itmvchregd')
                        ->select('itm_vch_reg_d_id, itm_vch_reg_id, mat_cent_id')
                        ->whereIn('itm_vch_reg_id', $regIds)
                        ->get()->getResultArray();
                    foreach ($detRows as $d) {
                        $key = $d['itm_vch_reg_id'].'|'.$d['mat_cent_id'];
                        $existingDet[$key] = $d['itm_vch_reg_d_id'];
                    }
                }

                $toUpdateDet = [];
                $toInsertDet = [];
                foreach ($detailInsert as $row) {
                    $key = $row['itm_vch_reg_id'].'|'.$row['mat_cent_id'];
                    if (isset($existingDet[$key])) {
                        $row['itm_vch_reg_d_id'] = $existingDet[$key];
                        $toUpdateDet[] = $row;
                    } else {
                        $toInsertDet[] = $row;
                    }
                }

                if (!empty($toUpdateDet)) {
                    $this->db->table('itmvchregd')->updateBatch($toUpdateDet, 'itm_vch_reg_d_id');
                }
                if (!empty($toInsertDet)) {
                    $this->db->table('itmvchregd')->insertBatch($toInsertDet);
                }
            }
            $log("ITEM REG: details processed (insert+update): ".count($detailInsert));

            // ------------------------------------------------------------------
            // PHASE 2: ACCOUNT REGISTER (ALL vouchers: with or without items)
            // ------------------------------------------------------------------
            $acctRows = $this->db->table('accttxnmst a')
                ->select("
                    a.vch_txn_id,
                    a.txn_id,
                    a.acc_id,
                    a.acc_txn_date,
                    a.acc_txn_dr_cr,
                    a.acc_txn_amt,
                    a.hobo_id,
                    a.acc_txn_type,
                    v.vch_type_id,
                    v.vch_sub_type_id,
                    v.cmp_id,
                    v.hobo_id AS v_hobo_id,
                    ln.vch_long_narr
                ")
                ->join('vchtxnconso v', 'v.vch_txn_id = a.vch_txn_id', 'inner')
                ->join('vchlongnar ln', 'ln.vch_txn_id = a.vch_txn_id AND ln.cmp_id = v.cmp_id', 'left')
                ->whereIn('v.vch_type_id', $SALES_RETURN_VCH_TYPES)
                ->where('a.cmp_id', $company_id)
                ->where('a.acc_txn_type', 1)
                ->where('v.cmp_id', $company_id);
            
            $acctRows = $acctRows->get()->getResultArray();
            $log("ACCT REG: acct rows: ".count($acctRows));

            foreach ($acctRows as $row) {
                $isCr = ((int)$row['acc_txn_dr_cr'] === 2);
                $drAmt = $isCr ? 0 : (float)$row['acc_txn_amt'];
                $crAmt = $isCr ? (float)$row['acc_txn_amt'] : 0;

                $regData = [
                    'acct_vch_type'  => $row['vch_type_id'],
                    'cmp_id'         => $company_id,
                    'vch_txn_id'     => $row['vch_txn_id'],
                    'txn_id'         => $row['txn_id'], // can be NULL
                    'vch_date'       => $row['acc_txn_date'],
                    'acc_id'         => $row['acc_id'],
                    'acc_txn_dr_amt' => $drAmt,
                    'acc_txn_cr_amt' => $crAmt,
                    'vch_narr'       => $row['vch_long_narr'] ?? '',
                    'hobo_id'        => $row['hobo_id'],
                    'acc_txn_type'   => $row['acc_txn_type'],
                ];

                $builder = $this->db->table('acctvchreg')
                    ->select('acctvchregn_id')
                    ->where('cmp_id', $company_id)
                    ->where('vch_txn_id', $row['vch_txn_id'])
                    ->where('acc_id', $row['acc_id']);

                if ($row['txn_id'] === null) {
                    $builder->where('txn_id IS NULL', null, false);
                } else {
                    $builder->where('txn_id', $row['txn_id']);
                }

                $existingReg = $builder->get()->getRowArray();

                if ($existingReg) {
                    $this->db->table('acctvchreg')
                        ->where('acctvchregn_id', $existingReg['acctvchregn_id'])
                        ->update($regData);
                } else {
                    $this->db->table('acctvchreg')->insert($regData);
                }
            }

            // Party row (txn_id NULL, total CR for the party)
            $voucherRowsAll = $this->db->table('vchtxnconso v')
                ->select('v.vch_txn_id, v.vch_type_id, v.vch_date, v.cmp_id, v.hobo_id, ln.vch_long_narr')
                ->join('vchlongnar ln', 'ln.vch_txn_id = v.vch_txn_id AND ln.cmp_id = v.cmp_id', 'left')
                ->whereIn('v.vch_type_id', $SALES_RETURN_VCH_TYPES)
                ->where('v.cmp_id', $company_id);
            if (!empty($bo_id)) $voucherRowsAll->where('v.hobo_id', $bo_id);
            $voucherRowsAll = $voucherRowsAll->get()->getResultArray();

            foreach ($voucherRowsAll as $vrow) {
                $vch_txn_id = $vrow['vch_txn_id'];

                $party_q = $this->db->table('cmptxnmstn')
                    ->select('master_id')
                    ->where('cmp_id', $company_id)
                    ->where('vch_txn_id', $vch_txn_id)
                    ->where('master_id_type', 'acc')
                    ->orderBy('txn_id', 'asc')
                    ->orderBy('vch_txn_id', 'asc')
                    ->limit(1)
                    ->get()->getRowArray();
                $party_id = $party_q['master_id'] ?? null;
                if (!$party_id) {
                    $log("ACCT REG PARTY: skip vch_txn_id {$vch_txn_id} (no party found)");
                    continue;
                }

                $acct_sums = $this->db->table('accttxnmst')
                    ->select("
                        SUM(CASE WHEN acc_txn_dr_cr = 2 THEN acc_txn_amt ELSE 0 END) AS cr_sum,
                        MAX(acc_txn_type) AS acc_txn_type
                    ")
                    ->where('cmp_id', $company_id)
                    ->where('vch_txn_id', $vch_txn_id)
                    ->where('acc_txn_type', 1);
                if (!empty($bo_id)) $acct_sums->where('hobo_id', $bo_id);
                $acct_sums = $acct_sums->get()->getRowArray();
                $cr_sum = (float)($acct_sums['cr_sum'] ?? 0);
                $acc_txn_type = (int)($acct_sums['acc_txn_type'] ?? 1);

                $partyData = [
                    'acct_vch_type'  => $vrow['vch_type_id'],
                    'cmp_id'         => $company_id,
                    'vch_txn_id'     => $vch_txn_id,
                    'txn_id'         => null,
                    'vch_date'       => $vrow['vch_date'],
                    'acc_id'         => $party_id,
                    'acc_txn_dr_amt' => 0,
                    'acc_txn_cr_amt' => $cr_sum,  // credit party for sales return
                    'vch_narr'       => $vrow['vch_long_narr'] ?? '',
                    'hobo_id'        => $vrow['hobo_id'],
                    'acc_txn_type'   => $acc_txn_type,
                ];

                $existingParty = $this->db->table('acctvchreg')
                    ->select('acctvchregn_id')
                    ->where('cmp_id', $company_id)
                    ->where('vch_txn_id', $vch_txn_id)
                    ->where('acc_id', $party_id)
                    ->where('txn_id IS NULL', null, false)
                    ->get()->getRowArray();

                if ($existingParty) {
                    $this->db->table('acctvchreg')
                        ->where('acctvchregn_id', $existingParty['acctvchregn_id'])
                        ->update($partyData);
                } else {
                    $this->db->table('acctvchreg')->insert($partyData);
                }
            }

            // Commit for this company
            $this->db->transCommit();
            $log("Backfill OK for company {$company_id}. Item headers inserted: ".count($toInsert).", updated: ".count($toUpdate).", item details processed: ".count($detailInsert).", acct rows: ".count($acctRows));

        } catch (\Throwable $e) {
			helper('error');
            $this->db->transRollback();
			$error = formatDbException($e);
            $log("ERROR company {$company_id}: ".json_encode($error));
            echo "Error (company {$company_id}): ".json_encode($error)."\n";
            // Continue with next company instead of stopping all
            continue;
        }
    }

    echo "Done for all companies. Check log: {$LOG_FILE}\n";
}
/************************   End of Tool To Remap Sale Return Register Entries (ALL COMPANIES, NO DETAIL DELETE)  ********************/
	
	
	/************************  Start of Tool To Remap Purchase Return Register Entries (ALL COMPANIES, NO DETAIL DELETE, SAFE GET)  ********************/
public function purchase_return_register_script_all()
{
    // --------------------------------------------------
    // CONFIG
    // --------------------------------------------------
    $PURCHASE_RETURN_VCH_TYPES = [3]; // debit note / purchase return voucher type IDs
    $LOG_FILE = APPPATH . 'logs/backfill_purchase_return_registers_all.log';

    // Optional: limit to a specific BO; set to null to include all
    $bo_id = $this->session->get('ses_boid');

    $log = function($msg) use ($LOG_FILE) {
        @file_put_contents($LOG_FILE, '['.date('Y-m-d H:i:s').'] '.$msg.PHP_EOL, FILE_APPEND);
    };

    // Safe fetch helper
    $safeGet = function($builder, $label) {
        $q = $builder->get();
        if ($q === false) {
            $err = service('db')->error(); // CI4 DB error
            throw new \RuntimeException("$label failed: ".$err['message']);
        }
        return $q->getResultArray();
    };

    // Fetch all companies
    $companies = $this->db->table('cmpmastern')->select('cmp_id')->get()->getResultArray();
    if (empty($companies)) {
        echo "No companies found.\n";
        return;
    }

    foreach ($companies as $cRow) {
        $company_id = (int)$cRow['cmp_id'];
        $this->db->transBegin();

        try {
            $log("---- START company {$company_id} ----");

            // ------------------------------------------------------------------
            // PHASE 1: ITEM REGISTER (only vouchers with items: vch_sub_type_id != 0)
            // ------------------------------------------------------------------
            $qb = $this->db->table('itemtxnmst si');
            $qb->select("
                si.vch_txn_id,
                si.txn_id,
                si.itm_id_unit_id,
                si.itm_txn_date,
                si.itm_txn_dr_cr,
                si.itm_txn_qty,
                si.itm_txn_rate,
                si.itm_txn_amt,
                si.mat_cent_id,
                si.hobo_id,
                si.itm_txn_type,
                v.vch_type_id,
                v.vch_sub_type_id,
                v.cmp_id,
                v.hobo_id AS v_hobo_id,
                ln.vch_long_narr
            ");
            $qb->join('vchtxnconso v', 'v.vch_txn_id = si.vch_txn_id', 'inner');
            $qb->join('vchlongnar ln', 'ln.vch_txn_id = si.vch_txn_id AND ln.cmp_id = v.cmp_id', 'left');
            $qb->whereIn('v.vch_type_id', $PURCHASE_RETURN_VCH_TYPES);
            $qb->where('v.vch_sub_type_id !=', 0); // only vouchers with items
            $qb->where('v.cmp_id', $company_id);
            $qb->where('si.cmp_id', $company_id);
            /* if (!empty($bo_id)) {
                $qb->where('v.hobo_id', $bo_id);
                $qb->where('si.hobo_id', $bo_id);
            } */

            $rows = $safeGet($qb, "ITEM REG fetch (company {$company_id})");
            $log("ITEM REG: fetched rows: ".count($rows));

            $headerRows = [];
            foreach ($rows as $r) {
                $key = $r['vch_txn_id'].'|'.$r['txn_id'].'|'.$r['itm_id_unit_id'];
                $headerRows[$key] = [
                    'itm_vch_type'   => $r['vch_type_id'],
                    'cmp_id'         => $r['cmp_id'],
                    'vch_txn_id'     => $r['vch_txn_id'],
                    'txn_id'         => $r['txn_id'],
                    'vch_date'       => $r['itm_txn_date'],
                    'itm_id_unit_id' => $r['itm_id_unit_id'],
                    'vch_narr'       => $r['vch_long_narr'] ?? '',
                    'hobo_id'        => $r['hobo_id'],
                    'itm_txn_type'   => $r['itm_txn_type'] ?? 1,
                ];
            }

            $vchIds   = array_values(array_unique(array_column($rows, 'vch_txn_id')));
            $existing = [];
            if (!empty($vchIds)) {
                $q = $this->db->table('itmvchregn')
                    ->select('itm_vch_reg_id, vch_txn_id, txn_id, itm_id_unit_id')
                    ->where('cmp_id', $company_id)
                    ->whereIn('vch_txn_id', $vchIds);
                $regRows = $safeGet($q, "ITEM REG existing headers (company {$company_id})");
                foreach ($regRows as $h) {
                    $k = $h['vch_txn_id'].'|'.$h['txn_id'].'|'.$h['itm_id_unit_id'];
                    $existing[$k] = $h['itm_vch_reg_id'];
                }
            }

            $toInsert = [];
            $toUpdate = [];
            foreach ($headerRows as $k => $hdr) {
                if (isset($existing[$k])) {
                    $hdr['itm_vch_reg_id'] = $existing[$k];
                    $toUpdate[] = $hdr;
                } else {
                    $toInsert[] = $hdr;
                }
            }
            if (!empty($toInsert)) {
                $this->db->table('itmvchregn')->insertBatch($toInsert);
                $q = $this->db->table('itmvchregn')
                    ->select('itm_vch_reg_id, vch_txn_id, txn_id, itm_id_unit_id')
                    ->where('cmp_id', $company_id)
                    ->whereIn('vch_txn_id', $vchIds);
                $newRows = $safeGet($q, "ITEM REG refresh headers (company {$company_id})");
                foreach ($newRows as $h) {
                    $k = $h['vch_txn_id'].'|'.$h['txn_id'].'|'.$h['itm_id_unit_id'];
                    $existing[$k] = $h['itm_vch_reg_id'];
                }
            }
            if (!empty($toUpdate)) {
                $this->db->table('itmvchregn')->updateBatch($toUpdate, 'itm_vch_reg_id');
            }
            $log("ITEM REG: headers inserted: ".count($toInsert).", updated: ".count($toUpdate));

            $regIds = array_values($existing);

            // Build detail rows (no delete, upsert)
            $detailInsert = [];
            foreach ($rows as $r) {
                $k = $r['vch_txn_id'].'|'.$r['txn_id'].'|'.$r['itm_id_unit_id'];
                $regId = $existing[$k] ?? null;
                if (!$regId) continue;

                $isCr   = ((int)$r['itm_txn_dr_cr'] === 2); // 1=DR, 2=CR
                $dr_qty = $isCr ? 0 : ($r['itm_txn_qty'] ?? 0);
                $cr_qty = $isCr ? ($r['itm_txn_qty'] ?? 0) : 0;

                $detailInsert[] = [
                    'itm_vch_reg_id' => $regId,
                    'mat_cent_id'    => $r['mat_cent_id'],
                    'itm_txn_dr_qty'  => $dr_qty,
                    'itm_txn_dr_rate' => $isCr ? 0 : ($r['itm_txn_rate'] ?? 0),
                    'itm_txn_dr_amt'  => $isCr ? 0 : ($r['itm_txn_amt'] ?? 0),
                    'itm_txn_cr_qty'  => $cr_qty,
                    'itm_txn_cr_rate' => $isCr ? ($r['itm_txn_rate'] ?? 0) : 0,
                    'itm_txn_cr_amt'  => $isCr ? ($r['itm_txn_amt'] ?? 0) : 0,
                    'vch_narr'        => $r['vch_long_narr'] ?? '',
                ];
            }

            // UPSERT detail rows (key: itm_vch_reg_id + mat_cent_id)
            if (!empty($detailInsert)) {
                $existingDet = [];
                if (!empty($regIds)) {
                    $q = $this->db->table('itmvchregd')
                        ->select('itm_vch_reg_d_id, itm_vch_reg_id, mat_cent_id')
                        ->whereIn('itm_vch_reg_id', $regIds);
                    $detRows = $safeGet($q, "ITEM REG existing details (company {$company_id})");
                    foreach ($detRows as $d) {
                        $key = $d['itm_vch_reg_id'].'|'.$d['mat_cent_id'];
                        $existingDet[$key] = $d['itm_vch_reg_d_id'];
                    }
                }

                $toUpdateDet = [];
                $toInsertDet = [];
                foreach ($detailInsert as $row) {
                    $key = $row['itm_vch_reg_id'].'|'.$row['mat_cent_id'];
                    if (isset($existingDet[$key])) {
                        $row['itm_vch_reg_d_id'] = $existingDet[$key];
                        $toUpdateDet[] = $row;
                    } else {
                        $toInsertDet[] = $row;
                    }
                }

                if (!empty($toUpdateDet)) {
                    $this->db->table('itmvchregd')->updateBatch($toUpdateDet, 'itm_vch_reg_d_id');
                }
                if (!empty($toInsertDet)) {
                    $this->db->table('itmvchregd')->insertBatch($toInsertDet);
                }
            }
            $log("ITEM REG: details processed (insert+update): ".count($detailInsert));

            // ------------------------------------------------------------------
            // PHASE 2: ACCOUNT REGISTER (ALL vouchers: with or without items)
            // ------------------------------------------------------------------
            $acctRowsB = $this->db->table('accttxnmst a')
                ->select("
                    a.vch_txn_id,
                    a.txn_id,
                    a.acc_id,
                    a.acc_txn_date,
                    a.acc_txn_dr_cr,
                    a.acc_txn_amt,
                    a.hobo_id,
                    a.acc_txn_type,
                    v.vch_type_id,
                    v.vch_sub_type_id,
                    v.cmp_id,
                    v.hobo_id AS v_hobo_id,
                    ln.vch_long_narr
                ")
                ->join('vchtxnconso v', 'v.vch_txn_id = a.vch_txn_id', 'inner')
                ->join('vchlongnar ln', 'ln.vch_txn_id = a.vch_txn_id AND ln.cmp_id = v.cmp_id', 'left')
                ->whereIn('v.vch_type_id', $PURCHASE_RETURN_VCH_TYPES)
                ->where('a.cmp_id', $company_id)
                ->where('a.acc_txn_type', 1)
                ->where('v.cmp_id', $company_id);
            
            $acctRows = $safeGet($acctRowsB, "ACCT REG fetch (company {$company_id})");
            $log("ACCT REG: acct rows: ".count($acctRows));

            foreach ($acctRows as $row) {
                $isCr = ((int)$row['acc_txn_dr_cr'] === 2);
                $drAmt = $isCr ? 0 : (float)$row['acc_txn_amt'];   // purchase return: party DR, items/charges CR
                $crAmt = $isCr ? (float)$row['acc_txn_amt'] : 0;

                $regData = [
                    'acct_vch_type'  => $row['vch_type_id'],
                    'cmp_id'         => $company_id,
                    'vch_txn_id'     => $row['vch_txn_id'],
                    'txn_id'         => $row['txn_id'] ?? NULL, // can be NULL
                    'vch_date'       => $row['acc_txn_date'],
                    'acc_id'         => $row['acc_id'],
                    'acc_txn_dr_amt' => $drAmt,
                    'acc_txn_cr_amt' => $crAmt,
                    'vch_narr'       => $row['vch_long_narr'] ?? '',
                    'hobo_id'        => $row['hobo_id'],
                    'acc_txn_type'   => $row['acc_txn_type'],
                ];

                $builder = $this->db->table('acctvchreg')
                    ->select('acctvchregn_id')
                    ->where('cmp_id', $company_id)
                    ->where('vch_txn_id', $row['vch_txn_id'])
                    ->where('acc_id', $row['acc_id']);

                if ($row['txn_id'] === null) {
                    $builder->where('txn_id IS NULL', null, false);
                } else {
                    $builder->where('txn_id', $row['txn_id']);
                }

                $existingReg = $safeGet($builder, "ACCT REG existing row (company {$company_id})");
                $existingReg = $existingReg[0] ?? null;

                if ($existingReg) {
                    $this->db->table('acctvchreg')
                        ->where('acctvchregn_id', $existingReg['acctvchregn_id'])
                        ->update($regData);
                } else {
                    $this->db->table('acctvchreg')->insert($regData);
                }
            }

            // Party row (txn_id NULL, total DR for the party)
            $voucherRowsAllB = $this->db->table('vchtxnconso v')
                ->select('v.vch_txn_id, v.vch_type_id, v.vch_date, v.cmp_id, v.hobo_id, ln.vch_long_narr')
                ->join('vchlongnar ln', 'ln.vch_txn_id = v.vch_txn_id AND ln.cmp_id = v.cmp_id', 'left')
                ->whereIn('v.vch_type_id', $PURCHASE_RETURN_VCH_TYPES)
                ->where('v.cmp_id', $company_id);
            if (!empty($bo_id)) $voucherRowsAllB->where('v.hobo_id', $bo_id);
            $voucherRowsAll = $safeGet($voucherRowsAllB, "ACCT REG voucher rows (company {$company_id})");

            foreach ($voucherRowsAll as $vrow) {
                $vch_txn_id = $vrow['vch_txn_id'];

                $party_qB = $this->db->table('cmptxnmstn')
                    ->select('master_id')
                    ->where('cmp_id', $company_id)
                    ->where('vch_txn_id', $vch_txn_id)
                    ->where('master_id_type', 'acc')
                    ->orderBy('txn_id', 'asc')
                    ->orderBy('vch_txn_id', 'asc')
                    ->limit(1);
                $party_q = $safeGet($party_qB, "ACCT REG party fetch (company {$company_id}, vch_txn_id {$vch_txn_id})");
                $party_id = $party_q[0]['master_id'] ?? null;
                if (!$party_id) {
                    $log("ACCT REG PARTY: skip vch_txn_id {$vch_txn_id} (no party found)");
                    continue;
                }

                $acct_sumsB = $this->db->table('accttxnmst')
                    ->select("
                        SUM(CASE WHEN acc_txn_dr_cr = 1 THEN acc_txn_amt ELSE 0 END) AS dr_sum,
                        MAX(acc_txn_type) AS acc_txn_type
                    ")
                    ->where('cmp_id', $company_id)
                    ->where('vch_txn_id', $vch_txn_id)
                    ->where('acc_txn_type', 1);
                if (!empty($bo_id)) $acct_sumsB->where('hobo_id', $bo_id);
                $acct_sums = $safeGet($acct_sumsB, "ACCT REG party sums (company {$company_id}, vch_txn_id {$vch_txn_id})");
                $acct_sums = $acct_sums[0] ?? [];
                $dr_sum = (float)($acct_sums['dr_sum'] ?? 0);
                $acc_txn_type = (int)($acct_sums['acc_txn_type'] ?? 1);

                $partyData = [
                    'acct_vch_type'  => $vrow['vch_type_id'],
                    'cmp_id'         => $company_id,
                    'vch_txn_id'     => $vch_txn_id,
                    'txn_id'         => null,
                    'vch_date'       => $vrow['vch_date'],
                    'acc_id'         => $party_id,
                    'acc_txn_dr_amt' => $dr_sum,  // debit party for purchase return
                    'acc_txn_cr_amt' => 0,
                    'vch_narr'       => $vrow['vch_long_narr'] ?? '',
                    'hobo_id'        => $vrow['hobo_id'],
                    'acc_txn_type'   => $acc_txn_type,
                ];

                $existingPartyB = $this->db->table('acctvchreg')
                    ->select('acctvchregn_id')
                    ->where('cmp_id', $company_id)
                    ->where('vch_txn_id', $vch_txn_id)
                    ->where('acc_id', $party_id)
                    ->where('txn_id IS NULL', null, false);
                $existingParty = $safeGet($existingPartyB, "ACCT REG party existing (company {$company_id}, vch_txn_id {$vch_txn_id})");
                $existingParty = $existingParty[0] ?? null;

                if ($existingParty) {
                    $this->db->table('acctvchreg')
                        ->where('acctvchregn_id', $existingParty['acctvchregn_id'])
                        ->update($partyData);
                } else {
                    $this->db->table('acctvchreg')->insert($partyData);
                }
            }

            // Commit for this company
            $this->db->transCommit();
            $log("Backfill OK for company {$company_id}. Item headers inserted: ".count($toInsert).", updated: ".count($toUpdate).", item details processed: ".count($detailInsert).", acct rows: ".count($acctRows));

        } catch (\Throwable $e) {
            $this->db->transRollback();
            $log("ERROR company {$company_id}: ".$e->getMessage());
            echo "Error (company {$company_id}): ".$e->getMessage()."\n";
            // Continue with next company instead of stopping all
            continue;
        }
    }

    echo "Done for all companies. Check log: {$LOG_FILE}\n";
}
/************************   End of Tool To Remap Purchase Return Register Entries (ALL COMPANIES, NO DETAIL DELETE, SAFE GET)  ********************/
	
	
    /************************   Tool To Remap Purchase Register Entries       ********************/
    
	public function purchase_register_script()
{
    // --------------------------------------------------
    // CONFIG
    // --------------------------------------------------
    $PURCHASE_VCH_TYPES = [11]; // purchase voucher type IDs
    $LOG_FILE = APPPATH . 'logs/backfill_purchase_registers.log';

    $company_id = $this->session->get('ses_company_id');
    $bo_id      = $this->session->get('ses_boid');

    $this->db->transBegin();
    $log = function($msg) use ($LOG_FILE) {
        @file_put_contents($LOG_FILE, '['.date('Y-m-d H:i:s').'] '.$msg.PHP_EOL, FILE_APPEND);
    };

    try {
        // ------------------------------------------------------------------
        // PHASE 1: ITEM REGISTER (only vouchers with items: vch_sub_type_id != 0)
        // ------------------------------------------------------------------
        $qb = $this->db->table('itemtxnmst si');
        $qb->select("
            si.vch_txn_id,
            si.txn_id,
            si.itm_id_unit_id,
            si.itm_txn_date,
            si.itm_txn_dr_cr,
            si.itm_txn_qty,
            si.itm_txn_rate,
            si.itm_txn_amt,
            si.mat_cent_id,
            si.hobo_id,
            si.itm_txn_type,
            v.vch_type_id,
            v.vch_sub_type_id,
            v.cmp_id,
            v.hobo_id AS v_hobo_id,
            ln.vch_long_narr
        ");
        $qb->join('vchtxnconso v', 'v.vch_txn_id = si.vch_txn_id', 'inner');
        $qb->join('vchlongnar ln', 'ln.vch_txn_id = si.vch_txn_id AND ln.cmp_id = v.cmp_id', 'left');
        $qb->whereIn('v.vch_type_id', $PURCHASE_VCH_TYPES);
        $qb->where('v.vch_sub_type_id !=', 0);           // only vouchers with items
        $qb->where('v.cmp_id', $company_id);
        $qb->where('si.cmp_id', $company_id);
        if (!empty($bo_id)) {
            $qb->where('v.hobo_id', $bo_id);
            $qb->where('si.hobo_id', $bo_id);
        }

        $rows = $qb->get()->getResultArray();
        $log("ITEM REG: fetched rows: ".count($rows));
        $headerRows = [];
        foreach ($rows as $r) {
            $key = $r['vch_txn_id'].'|'.$r['txn_id'].'|'.$r['itm_id_unit_id'];
            $headerRows[$key] = [
                'itm_vch_type'   => $r['vch_type_id'],
                'cmp_id'         => $r['cmp_id'],
                'vch_txn_id'     => $r['vch_txn_id'],
                'txn_id'         => $r['txn_id'],
                'vch_date'       => $r['itm_txn_date'],
                'itm_id_unit_id' => $r['itm_id_unit_id'],
                'vch_narr'       => $r['vch_long_narr'] ?? '',
                'hobo_id'        => $r['hobo_id'],
                'itm_txn_type'   => $r['itm_txn_type'] ?? 1,
            ];
        }

        $vchIds   = array_values(array_unique(array_column($rows, 'vch_txn_id')));
        $existing = [];
        if (!empty($vchIds)) {
            $q = $this->db->table('itmvchregn')
                ->select('itm_vch_reg_id, vch_txn_id, txn_id, itm_id_unit_id')
                ->where('cmp_id', $company_id)
                ->whereIn('vch_txn_id', $vchIds)
                ->get()->getResultArray();
            foreach ($q as $h) {
                $k = $h['vch_txn_id'].'|'.$h['txn_id'].'|'.$h['itm_id_unit_id'];
                $existing[$k] = $h['itm_vch_reg_id'];
            }
        }

        $toInsert = [];
        $toUpdate = [];
        foreach ($headerRows as $k => $hdr) {
            if (isset($existing[$k])) {
                $hdr['itm_vch_reg_id'] = $existing[$k];
                $toUpdate[] = $hdr;
            } else {
                $toInsert[] = $hdr;
            }
        }
        if (!empty($toInsert)) {
            $this->db->table('itmvchregn')->insertBatch($toInsert);
            $newRows = $this->db->table('itmvchregn')
                ->select('itm_vch_reg_id, vch_txn_id, txn_id, itm_id_unit_id')
                ->where('cmp_id', $company_id)
                ->whereIn('vch_txn_id', $vchIds)
                ->get()->getResultArray();
            foreach ($newRows as $h) {
                $k = $h['vch_txn_id'].'|'.$h['txn_id'].'|'.$h['itm_id_unit_id'];
                $existing[$k] = $h['itm_vch_reg_id'];
            }
        }
        if (!empty($toUpdate)) {
            $this->db->table('itmvchregn')->updateBatch($toUpdate, 'itm_vch_reg_id');
        }
        $log("ITEM REG: headers inserted: ".count($toInsert).", updated: ".count($toUpdate));

        $regIds = array_values($existing);
        if (!empty($regIds)) {
            $this->db->table('itmvchregd')->whereIn('itm_vch_reg_id', $regIds)->delete();
        }

        $detailInsert = [];
        foreach ($rows as $r) {
            $k = $r['vch_txn_id'].'|'.$r['txn_id'].'|'.$r['itm_id_unit_id'];
            $regId = $existing[$k] ?? null;
            if (!$regId) continue;

            $isCr = ((int)$r['itm_txn_dr_cr'] === 2); // 1=DR, 2=CR
            $dr_qty  = $isCr ? 0 : ($r['itm_txn_qty'] ?? 0);
            $cr_qty  = $isCr ? ($r['itm_txn_qty'] ?? 0) : 0;
            $dr_rate = $isCr ? 0 : ($r['itm_txn_rate'] ?? 0);
            $cr_rate = $isCr ? ($r['itm_txn_rate'] ?? 0) : 0;
            $dr_amt  = $isCr ? 0 : ($r['itm_txn_amt'] ?? 0);
            $cr_amt  = $isCr ? ($r['itm_txn_amt'] ?? 0) : 0;

            $detailInsert[] = [
                'itm_vch_reg_id' => $regId,
                'mat_cent_id'    => $r['mat_cent_id'],
                'itm_txn_dr_qty'  => $dr_qty,
                'itm_txn_dr_rate' => $dr_rate,
                'itm_txn_dr_amt'  => $dr_amt,
                'itm_txn_cr_qty'  => $cr_qty,
                'itm_txn_cr_rate' => $cr_rate,
                'itm_txn_cr_amt'  => $cr_amt,
                'vch_narr'        => $r['vch_long_narr'] ?? '',
            ];
        }
        if (!empty($detailInsert)) {
            $this->db->table('itmvchregd')->insertBatch($detailInsert);
        }
        $log("ITEM REG: details inserted: ".count($detailInsert));

        // ------------------------------------------------------------------
        // PHASE 2: ACCOUNT REGISTER (ALL vouchers: with or without items)
        // For each accttxnmst row (acc_txn_type=1), upsert acctvchreg with txn_id (can be NULL)
        // Also ensure party row (txn_id NULL) with total DR for the party.
        // ------------------------------------------------------------------
        $acctRows = $this->db->table('accttxnmst a')
            ->select("
                a.vch_txn_id,
                a.txn_id,
                a.acc_id,
                a.acc_txn_date,
                a.acc_txn_dr_cr,
                a.acc_txn_amt,
                a.hobo_id,
                a.acc_txn_type,
                v.vch_type_id,
                v.vch_sub_type_id,
                v.cmp_id,
                v.hobo_id AS v_hobo_id,
                ln.vch_long_narr
            ")
            ->join('vchtxnconso v', 'v.vch_txn_id = a.vch_txn_id', 'inner')
            ->join('vchlongnar ln', 'ln.vch_txn_id = a.vch_txn_id AND ln.cmp_id = v.cmp_id', 'left')
            ->whereIn('v.vch_type_id', $PURCHASE_VCH_TYPES)
            ->where('a.cmp_id', $company_id)
            ->where('a.acc_txn_type', 1)
            ->where('v.cmp_id', $company_id);
        if (!empty($bo_id)) {
            $acctRows->where('v.hobo_id', $bo_id);
            $acctRows->where('a.hobo_id', $bo_id);
        }
        $acctRows = $acctRows->get()->getResultArray();
        $log("ACCT REG: acct rows: ".count($acctRows));

        foreach ($acctRows as $row) {
            $isCr = ((int)$row['acc_txn_dr_cr'] === 2);
            $drAmt = $isCr ? 0 : (float)$row['acc_txn_amt'];
            $crAmt = $isCr ? (float)$row['acc_txn_amt'] : 0;

            $regData = [
                'acct_vch_type'  => $row['vch_type_id'],
                'cmp_id'         => $company_id,
                'vch_txn_id'     => $row['vch_txn_id'],
                'txn_id'         => $row['txn_id'], // can be NULL
                'vch_date'       => $row['acc_txn_date'],
                'acc_id'         => $row['acc_id'],
                'acc_txn_dr_amt' => $drAmt,
                'acc_txn_cr_amt' => $crAmt,
                'vch_narr'       => $row['vch_long_narr'] ?? '',
                'hobo_id'        => $row['hobo_id'],
                'acc_txn_type'   => $row['acc_txn_type'],
            ];

            $builder = $this->db->table('acctvchreg')
                ->select('acctvchregn_id')
                ->where('cmp_id', $company_id)
                ->where('vch_txn_id', $row['vch_txn_id'])
                ->where('acc_id', $row['acc_id']);

            if ($row['txn_id'] === null) {
                $builder->where('txn_id IS NULL', null, false);
            } else {
                $builder->where('txn_id', $row['txn_id']);
            }

            $existingReg = $builder->get()->getRowArray();

            if ($existingReg) {
                $this->db->table('acctvchreg')
                    ->where('acctvchregn_id', $existingReg['acctvchregn_id'])
                    ->update($regData);
            } else {
                $this->db->table('acctvchreg')->insert($regData);
            }
        }

        // Party row (txn_id NULL, total DR for the party)
        $voucherRowsAll = $this->db->table('vchtxnconso v')
            ->select('v.vch_txn_id, v.vch_type_id, v.vch_date, v.cmp_id, v.hobo_id, ln.vch_long_narr')
            ->join('vchlongnar ln', 'ln.vch_txn_id = v.vch_txn_id AND ln.cmp_id = v.cmp_id', 'left')
            ->whereIn('v.vch_type_id', $PURCHASE_VCH_TYPES)
            ->where('v.cmp_id', $company_id);
        if (!empty($bo_id)) $voucherRowsAll->where('v.hobo_id', $bo_id);
        $voucherRowsAll = $voucherRowsAll->get()->getResultArray();

        foreach ($voucherRowsAll as $vrow) {
            $vch_txn_id = $vrow['vch_txn_id'];

            $party_q = $this->db->table('cmptxnmstn')
                ->select('master_id')
                ->where('cmp_id', $company_id)
                ->where('vch_txn_id', $vch_txn_id)
                ->where('master_id_type', 'acc')
                ->orderBy('txn_id', 'asc')
                ->orderBy('vch_txn_id', 'asc')
                ->limit(1)
                ->get()->getRowArray();
            $party_id = $party_q['master_id'] ?? null;
            if (!$party_id) {
                $log("ACCT REG PARTY: skip vch_txn_id {$vch_txn_id} (no party found)");
                continue;
            }

            $acct_sums = $this->db->table('accttxnmst')
                ->select("
                    SUM(CASE WHEN acc_txn_dr_cr = 1 THEN acc_txn_amt ELSE 0 END) AS dr_sum,
                    MAX(acc_txn_type) AS acc_txn_type
                ")
                ->where('cmp_id', $company_id)
                ->where('vch_txn_id', $vch_txn_id)
                ->where('acc_txn_type', 1);
            if (!empty($bo_id)) $acct_sums->where('hobo_id', $bo_id);
            $acct_sums = $acct_sums->get()->getRowArray();
            $dr_sum = (float)($acct_sums['dr_sum'] ?? 0);
            $acc_txn_type = (int)($acct_sums['acc_txn_type'] ?? 1);

            $partyData = [
                'acct_vch_type'  => $vrow['vch_type_id'],
                'cmp_id'         => $company_id,
                'vch_txn_id'     => $vch_txn_id,
                'txn_id'         => null,
                'vch_date'       => $vrow['vch_date'],
                'acc_id'         => $party_id,
                'acc_txn_dr_amt' => $dr_sum,
                'acc_txn_cr_amt' => 0,
                'vch_narr'       => $vrow['vch_long_narr'] ?? '',
                'hobo_id'        => $vrow['hobo_id'],
                'acc_txn_type'   => $acc_txn_type,
            ];

            $existingParty = $this->db->table('acctvchreg')
                ->select('acctvchregn_id')
                ->where('cmp_id', $company_id)
                ->where('vch_txn_id', $vch_txn_id)
                ->where('acc_id', $party_id)
                ->where('txn_id IS NULL', null, false)
                ->get()->getRowArray();

            if ($existingParty) {
                $this->db->table('acctvchreg')
                    ->where('acctvchregn_id', $existingParty['acctvchregn_id'])
                    ->update($partyData);
            } else {
                $this->db->table('acctvchreg')->insert($partyData);
            }
        }

        // Commit
        $this->db->transCommit();
        $log("Backfill completed OK");
        echo "Done. Item headers inserted: ".count($toInsert).", updated: ".count($toUpdate).", item details: ".count($detailInsert).", acct rows: ".count($acctRows)."\n";

    } catch (\Throwable $e) {
        $this->db->transRollback();
        $log("ERROR: ".$e->getMessage());
        echo "Error: ".$e->getMessage()."\n";
    }
}
	
	/************************  End of Tool To Remap Purchase Register Entries                    ********************/
    
	/************************   Tool To Remap Sale Register Entries    ********************/
   
  public function sale_register_script()
{
    // --------------------------------------------------
    // CONFIG
    // --------------------------------------------------
    $SALES_VCH_TYPES = [18]; // TODO: set your sales voucher type IDs
    $LOG_FILE = APPPATH . 'logs/backfill_sale_registers.log';

    $company_id = $this->session->get('ses_company_id');
    $bo_id      = $this->session->get('ses_boid');

    $this->db->transBegin();
    $log = function($msg) use ($LOG_FILE) {
        @file_put_contents($LOG_FILE, '['.date('Y-m-d H:i:s').'] '.$msg.PHP_EOL, FILE_APPEND);
    };

    try {
        // ------------------------------------------------------------------
        // PHASE 1: ITEM REGISTER (only vouchers with items: vch_sub_type_id != 0)
        // ------------------------------------------------------------------
        $qb = $this->db->table('itemtxnmst si');
        $qb->select("
            si.vch_txn_id,
            si.txn_id,
            si.itm_id_unit_id,
            si.itm_txn_date,
            si.itm_txn_dr_cr,
            si.itm_txn_qty,
            si.itm_txn_rate,
            si.itm_txn_amt,
            si.mat_cent_id,
            si.hobo_id,
            si.itm_txn_type,
            v.vch_type_id,
            v.vch_sub_type_id,
            v.cmp_id,
            v.hobo_id AS v_hobo_id,
            ln.vch_long_narr
        ");
        $qb->join('vchtxnconso v', 'v.vch_txn_id = si.vch_txn_id', 'inner');
        $qb->join('vchlongnar ln', 'ln.vch_txn_id = si.vch_txn_id AND ln.cmp_id = v.cmp_id', 'left');
        $qb->whereIn('v.vch_type_id', $SALES_VCH_TYPES);
        $qb->where('v.vch_sub_type_id !=', 0);           // only vouchers with items
        $qb->where('v.cmp_id', $company_id);
        $qb->where('si.cmp_id', $company_id);
        if (!empty($bo_id)) {
            $qb->where('v.hobo_id', $bo_id);
            $qb->where('si.hobo_id', $bo_id);
        }

        $rows = $qb->get()->getResultArray();
        $log("ITEM REG: fetched rows: ".count($rows));
        $headerRows = [];
        foreach ($rows as $r) {
            $key = $r['vch_txn_id'].'|'.$r['txn_id'].'|'.$r['itm_id_unit_id'];
            $headerRows[$key] = [
                'itm_vch_type'   => $r['vch_type_id'],
                'cmp_id'         => $r['cmp_id'],
                'vch_txn_id'     => $r['vch_txn_id'],
                'txn_id'         => $r['txn_id'],
                'vch_date'       => $r['itm_txn_date'],
                'itm_id_unit_id' => $r['itm_id_unit_id'],
                'vch_narr'       => $r['vch_long_narr'] ?? '',
                'hobo_id'        => $r['hobo_id'], // from item row
                'itm_txn_type'   => $r['itm_txn_type'] ?? 1,
            ];
        }

        $vchIds   = array_values(array_unique(array_column($rows, 'vch_txn_id')));
        $existing = [];
        if (!empty($vchIds)) {
            $q = $this->db->table('itmvchregn')
                ->select('itm_vch_reg_id, vch_txn_id, txn_id, itm_id_unit_id')
                ->where('cmp_id', $company_id)
                ->whereIn('vch_txn_id', $vchIds)
                ->get()->getResultArray();
            foreach ($q as $h) {
                $k = $h['vch_txn_id'].'|'.$h['txn_id'].'|'.$h['itm_id_unit_id'];
                $existing[$k] = $h['itm_vch_reg_id'];
            }
        }

        $toInsert = [];
        $toUpdate = [];
        foreach ($headerRows as $k => $hdr) {
            if (isset($existing[$k])) {
                $hdr['itm_vch_reg_id'] = $existing[$k];
                $toUpdate[] = $hdr;
            } else {
                $toInsert[] = $hdr;
            }
        }
        if (!empty($toInsert)) {
            $this->db->table('itmvchregn')->insertBatch($toInsert);
            $newRows = $this->db->table('itmvchregn')
                ->select('itm_vch_reg_id, vch_txn_id, txn_id, itm_id_unit_id')
                ->where('cmp_id', $company_id)
                ->whereIn('vch_txn_id', $vchIds)
                ->get()->getResultArray();
            foreach ($newRows as $h) {
                $k = $h['vch_txn_id'].'|'.$h['txn_id'].'|'.$h['itm_id_unit_id'];
                $existing[$k] = $h['itm_vch_reg_id'];
            }
        }
        if (!empty($toUpdate)) {
            $this->db->table('itmvchregn')->updateBatch($toUpdate, 'itm_vch_reg_id');
        }
        $log("ITEM REG: headers inserted: ".count($toInsert).", updated: ".count($toUpdate));

        $regIds = array_values($existing);
        if (!empty($regIds)) {
            $this->db->table('itmvchregd')->whereIn('itm_vch_reg_id', $regIds)->delete();
        }

        $detailInsert = [];
        foreach ($rows as $r) {
            $k = $r['vch_txn_id'].'|'.$r['txn_id'].'|'.$r['itm_id_unit_id'];
            $regId = $existing[$k] ?? null;
            if (!$regId) continue;

            $isCr = ((int)$r['itm_txn_dr_cr'] === 2); // 1=DR, 2=CR
            $dr_qty  = $isCr ? 0 : ($r['itm_txn_qty'] ?? 0);
            $cr_qty  = $isCr ? ($r['itm_txn_qty'] ?? 0) : 0;
            $dr_rate = $isCr ? 0 : ($r['itm_txn_rate'] ?? 0);
            $cr_rate = $isCr ? ($r['itm_txn_rate'] ?? 0) : 0;
            $dr_amt  = $isCr ? 0 : ($r['itm_txn_amt'] ?? 0);
            $cr_amt  = $isCr ? ($r['itm_txn_amt'] ?? 0) : 0;

            $detailInsert[] = [
                'itm_vch_reg_id' => $regId,
                'mat_cent_id'    => $r['mat_cent_id'],
                'itm_txn_dr_qty'  => $dr_qty,
                'itm_txn_dr_rate' => $dr_rate,
                'itm_txn_dr_amt'  => $dr_amt,
                'itm_txn_cr_qty'  => $cr_qty,
                'itm_txn_cr_rate' => $cr_rate,
                'itm_txn_cr_amt'  => $cr_amt,
                'vch_narr'        => $r['vch_long_narr'] ?? '',
            ];
        }
        if (!empty($detailInsert)) {
            $this->db->table('itmvchregd')->insertBatch($detailInsert);
        }
        $log("ITEM REG: details inserted: ".count($detailInsert));

        // ------------------------------------------------------------------
        // PHASE 2: ACCOUNT REGISTER (ALL vouchers: with or without items)
        // For each accttxnmst row (acc_txn_type=1), upsert acctvchreg with txn_id (can be NULL)
        // Also ensure party row (txn_id NULL) with total DR for the party.
        // ------------------------------------------------------------------
        $acctRows = $this->db->table('accttxnmst a')
            ->select("
                a.vch_txn_id,
                a.txn_id,
                a.acc_id,
                a.acc_txn_date,
                a.acc_txn_dr_cr,
                a.acc_txn_amt,
                a.hobo_id,
                a.acc_txn_type,
                v.vch_type_id,
                v.vch_sub_type_id,
                v.cmp_id,
                v.hobo_id AS v_hobo_id,
                ln.vch_long_narr
            ")
            ->join('vchtxnconso v', 'v.vch_txn_id = a.vch_txn_id', 'inner')
            ->join('vchlongnar ln', 'ln.vch_txn_id = a.vch_txn_id AND ln.cmp_id = v.cmp_id', 'left')
            ->whereIn('v.vch_type_id', $SALES_VCH_TYPES)
            ->where('a.cmp_id', $company_id)
            ->where('a.acc_txn_type', 1) // only regular
            ->where('v.cmp_id', $company_id);
        if (!empty($bo_id)) {
            $acctRows->where('v.hobo_id', $bo_id);
            $acctRows->where('a.hobo_id', $bo_id);
        }
        $acctRows = $acctRows->get()->getResultArray();
        $log("ACCT REG: acct rows: ".count($acctRows));

        foreach ($acctRows as $row) {
            $isCr = ((int)$row['acc_txn_dr_cr'] === 2);
            $drAmt = $isCr ? 0 : (float)$row['acc_txn_amt'];
            $crAmt = $isCr ? (float)$row['acc_txn_amt'] : 0;

            $regData = [
                'acct_vch_type'  => $row['vch_type_id'],
                'cmp_id'         => $company_id,
                'vch_txn_id'     => $row['vch_txn_id'],
                'txn_id'         => $row['txn_id'], // can be NULL
                'vch_date'       => $row['acc_txn_date'],
                'acc_id'         => $row['acc_id'],
                'acc_txn_dr_amt' => $drAmt,
                'acc_txn_cr_amt' => $crAmt,
                'vch_narr'       => $row['vch_long_narr'] ?? '',
                'hobo_id'        => $row['hobo_id'],
                'acc_txn_type'   => $row['acc_txn_type'],
            ];

            $builder = $this->db->table('acctvchreg')
                ->select('acctvchregn_id')
                ->where('cmp_id', $company_id)
                ->where('vch_txn_id', $row['vch_txn_id'])
                ->where('acc_id', $row['acc_id']);

            if ($row['txn_id'] === null) {
                $builder->where('txn_id IS NULL', null, false);
            } else {
                $builder->where('txn_id', $row['txn_id']);
            }

            $existingReg = $builder->get()->getRowArray();

            if ($existingReg) {
                $this->db->table('acctvchreg')
                    ->where('acctvchregn_id', $existingReg['acctvchregn_id'])
                    ->update($regData);
            } else {
                $this->db->table('acctvchreg')->insert($regData);
            }
        }

        // Party row (txn_id NULL, total DR for the party)
        $voucherRowsAll = $this->db->table('vchtxnconso v')
            ->select('v.vch_txn_id, v.vch_type_id, v.vch_date, v.cmp_id, v.hobo_id, ln.vch_long_narr')
            ->join('vchlongnar ln', 'ln.vch_txn_id = v.vch_txn_id AND ln.cmp_id = v.cmp_id', 'left')
            ->whereIn('v.vch_type_id', $SALES_VCH_TYPES)
            ->where('v.cmp_id', $company_id);
        if (!empty($bo_id)) $voucherRowsAll->where('v.hobo_id', $bo_id);
        $voucherRowsAll = $voucherRowsAll->get()->getResultArray();

        foreach ($voucherRowsAll as $vrow) {
            $vch_txn_id = $vrow['vch_txn_id'];

            $party_q = $this->db->table('cmptxnmstn')
                ->select('master_id')
                ->where('cmp_id', $company_id)
                ->where('vch_txn_id', $vch_txn_id)
                ->where('master_id_type', 'acc')
                ->orderBy('txn_id', 'asc')
                ->orderBy('vch_txn_id', 'asc')
                ->limit(1)
                ->get()->getRowArray();
            $party_id = $party_q['master_id'] ?? null;
            if (!$party_id) {
                $log("ACCT REG PARTY: skip vch_txn_id {$vch_txn_id} (no party found)");
                continue;
            }

            $acct_sums = $this->db->table('accttxnmst')
                ->select("
                    SUM(CASE WHEN acc_txn_dr_cr = 1 THEN acc_txn_amt ELSE 0 END) AS dr_sum,
                    MAX(acc_txn_type) AS acc_txn_type
                ")
                ->where('cmp_id', $company_id)
                ->where('vch_txn_id', $vch_txn_id)
                ->where('acc_txn_type', 1);
            if (!empty($bo_id)) $acct_sums->where('hobo_id', $bo_id);
            $acct_sums = $acct_sums->get()->getRowArray();
            $dr_sum = (float)($acct_sums['dr_sum'] ?? 0);
            $acc_txn_type = (int)($acct_sums['acc_txn_type'] ?? 1);

            $partyData = [
                'acct_vch_type'  => $vrow['vch_type_id'],
                'cmp_id'         => $company_id,
                'vch_txn_id'     => $vch_txn_id,
                'txn_id'         => null,
                'vch_date'       => $vrow['vch_date'],
                'acc_id'         => $party_id,
                'acc_txn_dr_amt' => $dr_sum,
                'acc_txn_cr_amt' => 0,
                'vch_narr'       => $vrow['vch_long_narr'] ?? '',
                'hobo_id'        => $vrow['hobo_id'],
                'acc_txn_type'   => $acc_txn_type,
            ];

            $existingParty = $this->db->table('acctvchreg')
                ->select('acctvchregn_id')
                ->where('cmp_id', $company_id)
                ->where('vch_txn_id', $vch_txn_id)
                ->where('acc_id', $party_id)
                ->where('txn_id IS NULL', null, false)
                ->get()->getRowArray();

            if ($existingParty) {
                $this->db->table('acctvchreg')
                    ->where('acctvchregn_id', $existingParty['acctvchregn_id'])
                    ->update($partyData);
            } else {
                $this->db->table('acctvchreg')->insert($partyData);
            }
        }

        // Commit
        $this->db->transCommit();
        $log("Backfill completed OK");
        echo "Done. Item headers inserted: ".count($toInsert).", updated: ".count($toUpdate).", item details: ".count($detailInsert).", acct rows: ".count($acctRows)."\n";

    } catch (\Throwable $e) {
        $this->db->transRollback();
        $log("ERROR: ".$e->getMessage());
        echo "Error: ".$e->getMessage()."\n";
    }
}
   /**************************                   *************************/
	
	function ajax_load_vouchers_list($vouchers){
	 $zip = new \ZipArchive();
	    $folder_path = WRITEPATH.'uploads/db_backup_temp';	  
	    $_name = 'sales_multi_'.rand(1000,9999).'_'.time();
	    $zip_file_path =  $folder_path.'/'.$_name.'.zip';
        if($zip->open($zip_file_path, \ZipArchive::CREATE) == TRUE) {
			$content = 'multi printing vouchers';
			$zip->addFromString('notes.txt', $content);
			$zip->close();
	    }	
	 $voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	 $builder = $this->db->table($voucher_tbl);
	 $builder->select('comp_vch_no,voucher_txn_id');
	 $builder->where($voucher_tbl.'.comp_id', $this->company_id);
	 $builder->whereIn($voucher_tbl.'.voucher_txn_id', $vouchers);
	 $builder->where($voucher_tbl.'.bo_id', $this->bo_id);
     $result = $builder->get()->getResultArray();
	 $info   = array(); 
	 foreach($result as $row){ 
		$voucher_no   = $row['comp_vch_no'];
		$info[]      = array("zippath"=>$_name,"name"=>$voucher_no,"voucher_txn_id"=>$row["voucher_txn_id"],"id"=>$row["voucher_txn_id"]);
		}
    return $info;		
	}	
  
   function get_voucher_info($voucher_type_id,$comp_id){	 
	  $comp_voucher_type_tbl = $comp_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($comp_voucher_type_tbl)->where('voucher_type_id', $voucher_type_id)->where('comp_id', $comp_id)->get()->getRowArray();   	   
    }   
	
	function get_account_info($acc_id){	 
	  $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	  return $this->db->table($account_master_tbl)->where('acc_id', $acc_id)->get()->getRowArray();   	   
    } 
    
   function get_company_all_accounts($company_id){
	    $account_master_tbl = $company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
	    $builder = $this->db->table($account_master_tbl); 
		$builder->select(array('acc_id','acc_name'));
        $builder->where('comp_id', $company_id);
		$result = $builder->get()->getResultArray();
	    return $result;
   }
   
   public function get_comp_all_acc_transactions($voucher_txn_id,$comp_id,$txn_id,$limit=''){
	   $ses_comp_fy_id    = $this->session->get('ses_comp_fy_id');
	   $list   = array();
	   $company_all_accounts = $this->get_company_all_accounts($comp_id);
	   if($company_all_accounts){
		   foreach($company_all_accounts as $company_row){
			    $account_id         =  $company_row['acc_id'];
				$show_account_name  =  $company_row['acc_name'];
			    $voucher_txn_table  =  $comp_id.'_accnttxnnn_'.$account_id.'_'.$ses_comp_fy_id;	
			    
				$builder = $this->db->table($voucher_txn_table); 
				$builder->orderBy('txn_id');         
				$builder->where('txn_id', $txn_id);		
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
				$builder->where('comp_id', $comp_id);
				$builder->where('acc_id', $company_row['acc_id']);
                
				$result = $builder->get()->getResultArray();
			    if($result){			
				   foreach($result as $values){				
					  $posted_on = $values['posted_on'];
					  if($values['acc_txn_drcr']=='d'){
						 $debit  = $values['acc_txn_amount'];
						 $credit = '0.00';
					   }
					   else if($values['acc_txn_drcr']=='c'){
						 $credit = $values['acc_txn_amount'];
						 $debit  = '0.00';
						 }
						$list[] = array("bill_no"=>$values['comp_vch_series_no'],"narration"=>'',"comp_id"=>$comp_id,"txn_id"=>$txn_id,"txn_date" =>$values['acc_txn_date'],'account_name'=>$show_account_name,'debit'=>$debit,'credit'=>$credit,'posted_on'=>$posted_on);
				       } 
				    }
				 }
	          }
	   return $list;
   }
      
    
   function voucher_first_transaction($comp_id,$voucher_txn_id){
	    $txn_rows=array();
	    $ses_comp_fy_id    = $this->session->get('ses_comp_fy_id');	  
	    $vch_txn_trail_tbl = $comp_id.'_vhtxntrail_'.$this->session->get('ses_comp_fy_id');
	    $builder           = $this->db->table($vch_txn_trail_tbl ); 
	    
        $builder->orderBy('txn_id');                
		$builder->where('comp_id', $comp_id);	
		$builder->where('voucher_txn_id', $voucher_txn_id);
		$result = $builder->get()->getRowArray();
		if($result){
			$txn_id = $result['txn_id'];
			$txn_rows = $this->get_comp_all_acc_transactions($voucher_txn_id,$comp_id,$txn_id);
			// get voucher all trail txn data with sum of credit & debit
           $trial_credit_debit = $this->sum_trail_debite_credit($comp_id,$voucher_txn_id,$txn_id);
			return $txn_rows;
		}else
			return $txn_rows;
     }   

 function sum_trail_debite_credit($comp_id,$voucher_txn_id,$txn_id){
	   
	    $vch_txn_trail_tbl = $comp_id.'_vhtxntrail_'.$this->session->get('ses_comp_fy_id');
	    $txn_rows          = array();
	    $ses_comp_fy_id    = $this->session->get('ses_comp_fy_id');	   
	    $builder           = $this->db->table($vch_txn_trail_tbl); 
        $builder->orderBy('txn_id');                
		$builder->where('comp_id', $comp_id);	
		$builder->where('voucher_txn_id', $voucher_txn_id);
		
		$result = $builder->get()->getResultArray();
		
		if($result){
		foreach($result as $row){	
			$txn_id   = $row['txn_id'];
			$data_list = $this->get_comp_all_acc_transactions($voucher_txn_id,$comp_id,$txn_id);
			
			$txn_rows[$voucher_txn_id][] = $data_list;
		    }
		}
		
		
		$all_credit_list=array();
		$all_debit_list=array();
		 $counter=0;
		foreach($txn_rows as $key =>  $txrow){		
		    if(!empty($txrow)){	
			
			   $credit=$debit=0;
			  
				 foreach($txrow as $keynew =>  $txrow_new){
					  if(!empty($txrow_new)){
                        $counter++;				
							
                            $credit=$credit+$txrow_new['0']['credit'];
							$debit=$debit+$txrow_new['0']['debit'];
							
							$all_credit_list[$counter][$key]['cr'] =$txrow_new['0']['credit'];
							$all_credit_list[$counter][$key]['dr'] = $txrow_new['0']['debit'];
					  }
				 }
			}
		}

   }
   
   
  function get_item_balance($comp_id,$item_id,$voucher_date){
                $ses_comp_fy_id  = $this->session->get('ses_comp_fy_id');
                $item_txn_table  = $comp_id.'_itemtxnnnn_'.$item_id.'_'.$ses_comp_fy_id;	
				$builder = $this->db->table($item_txn_table);
				$builder->limit(1);
				$builder->orderBy('item_txn_id','DESC');     
				$builder->where('comp_id', $comp_id);
				$builder->where('item_id', $item_id);
				$builder->where('item_txn_date',$voucher_date);
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
				$result = $builder->get()->getRowArray();
                 if($result)
                   return $result['item_bal_qty'];
                 else
                  return "0";
         } 
         
  public function ajax_stockjournal_register_list($from_date, $to_date, $voucher_type_id, $vch_subtype_id){
    
    $comp_id         = $this->session->get('ses_company_id');
    $ses_comp_fy_id  = $this->session->get('ses_comp_fy_id');

    $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
    $pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10;
    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP < 1) $pq_rPP = 10;

    // Fetch "From" Items
    $fromBuilder = $this->db->table('itmvchregn regn');
    
    // FIX: Use correct column names (item_vch_type instead of itm_vch_type)
    $fromBuilder->select("
        regn.itm_vch_reg_id,
        regn. item_vch_type,  
        regn.vch_txn_id,
        regn.vch_date,
        regn.vch_narr,
        vd.mat_cent_id AS from_matcent_id,
        vd.itm_txn_cr_qty AS from_qty,
        vd.itm_txn_cr_rate AS from_rate,
        vd.itm_txn_cr_amt AS from_amount,
        im.itm_name AS from_item_name,
        mc_from.mat_cent_name
    ", false);
    
    $fromBuilder->join('itmvchregd vd', 'regn.itm_vch_reg_id = vd.itm_vch_reg_id', 'inner', false);
    $fromBuilder->join('itemmaster im', "im.itm_id = SPLIT_PART(regn.itm_id_unit_id::TEXT, '_', 1)::INTEGER", 'inner', false);
    $fromBuilder->join('matcentmst mc_from', 'mc_from.mat_cent_id = vd.mat_cent_id', 'inner', false);
    $fromBuilder->join('vchtxnconso fconso', 'fconso.vch_txn_id = regn.vch_txn_id', 'inner', false);
    
    // FIX: Use correct column name
    $fromBuilder->where('regn.item_vch_type', $voucher_type_id);
    $fromBuilder->where('fconso.vch_sub_type_id', $vch_subtype_id);
    $fromBuilder->where('regn.vch_date >=', $from_date);
    $fromBuilder->where('regn.vch_date <=', $to_date);
    $fromBuilder->where('vd.itm_txn_cr_qty >', 0);
    
    $countBuilder = clone $fromBuilder;
    $total_records = $countBuilder->countAllResults(false);
    
    if ($pq_curPage == 0) $pq_curPage = 1;
    $offset = ($pq_rPP * ($pq_curPage - 1));

    if ($offset > $total_records) {
        $pq_curPage = (int)ceil($total_records / $pq_rPP);
        $offset = ($pq_rPP * ($pq_curPage - 1));
    }
    if ($offset < 0) {
        $offset = 0;
    }
    
    $fromBuilder->orderBy('regn.vch_date', 'ASC');
    $fromBuilder->orderBy('regn.itm_vch_reg_id', 'ASC');
    $fromBuilder->limit($pq_rPP, $offset);
    $fromItems = $fromBuilder->get()->getResultArray();


    // Fetch "To" Items
    $toBuilder = $this->db->table('itmvchregn regn');
    
    // FIX: Use correct column names
    $toBuilder->select("
        regn. itm_vch_reg_id,
        regn.item_vch_type,
        regn.vch_txn_id,
        regn.vch_date,
        regn.vch_narr,
        vd.mat_cent_id AS to_matcent_id,
        vd.itm_txn_dr_qty AS to_qty,
        vd.itm_txn_dr_rate AS to_rate,
        vd.itm_txn_dr_amt AS to_amount,
        im.itm_name AS to_item_name,
        mc_to.mat_cent_name
    ", false);
    
    $toBuilder->join('itmvchregd vd', 'regn.itm_vch_reg_id = vd. itm_vch_reg_id', 'inner', false);
    $toBuilder->join('itemmaster im', "im.itm_id = SPLIT_PART(regn.itm_id_unit_id::TEXT, '_', 1)::INTEGER", 'inner', false);
    $toBuilder->join('matcentmst mc_to', 'mc_to.mat_cent_id = vd.mat_cent_id', 'inner', false);
    $toBuilder->join('vchtxnconso tconso', 'tconso.vch_txn_id = regn.vch_txn_id', 'inner', false);
    
    // FIX: Use correct column name
    $toBuilder->where('regn. item_vch_type', $voucher_type_id);
    $toBuilder->where('tconso.vch_sub_type_id', $vch_subtype_id);
    $toBuilder->where('regn.vch_date >=', $from_date);
    $toBuilder->where('regn.vch_date <=', $to_date);
    $toBuilder->where('vd.itm_txn_dr_qty >', 0);
    
    $countBuilder = clone $toBuilder;
    $total_records = $countBuilder->countAllResults(false);
    
    if ($pq_curPage == 0) $pq_curPage = 1;
    $offset = ($pq_rPP * ($pq_curPage - 1));

    if ($offset > $total_records) {
        $pq_curPage = (int)ceil($total_records / $pq_rPP);
        $offset = ($pq_rPP * ($pq_curPage - 1));
    }
    if ($offset < 0) {
        $offset = 0;
    }
     
    $toBuilder->orderBy('regn.vch_date', 'ASC');
    $toBuilder->orderBy('regn.itm_vch_reg_id', 'ASC');
    $toBuilder->limit($pq_rPP, $offset);
    $toItems = $toBuilder->get()->getResultArray();

    $fromMap = [];
    foreach ($fromItems as $row) {
        $key = $row['vch_date'] . '||' . $row['vch_narr'];
        $fromMap[$key][] = $row;
    }

    $final = [];
    foreach ($toItems as $row) {
        $key = $row['vch_date'] . '||' . $row['vch_narr'];
        if (isset($fromMap[$key])) {
            foreach ($fromMap[$key] as $fromRow) {
                $final[] = [
                    'voucher_date'    => $fromRow['vch_date'],
                    'material_centre' => $fromRow['mat_cent_name'],
                    'from_item_name'  => $fromRow['from_item_name'],
                    'from_item_qty'   => $fromRow['from_qty'],
                    'from_item_price' => $fromRow['from_rate'],
                    'from_item_amount'=> $fromRow['from_amount'],
                    'to_item_name'    => $row['to_item_name'],
                    'to_item_qty'     => $row['to_qty'],
                    'to_item_price'   => $row['to_rate'],
                    'to_item_amount'  => $row['to_amount'],
                    'narration'       => $fromRow['vch_narr'],
                    'voucher_type_id' => $voucher_type_id,
                    'voucher_txn_id'  => $fromRow['vch_txn_id'],
                ];
            }
        }
    }
    
    $totalRecords = count($final);
    return json_encode([
        'totalRecords' => $totalRecords,
        'curPage'      => $pq_curPage,
        'data'         => $final
    ]);	     
}
  
  public function ajax_physicalverification_register_list(){
        $comp_id          = $this->session->get('ses_company_id');
	    $ses_comp_fy_id   = $this->session->get('ses_comp_fy_id');
	    $comptxnmst_tbl   = $comp_id.'_comptxnmst_'.$ses_comp_fy_id;
	    $base_url         = base_url().'/'.getenv('AdminPath');
	    $voucher_type_id  = '10';
	    $from_date        =  $_POST["from_date"];
	    $to_date          =  $_POST["to_date"];
	    if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
            
        
	    $vch_txn_conso_tbl = $comp_id.'_vhtxnconso_'.$ses_comp_fy_id;
	    $builder           = $this->db->table($vch_txn_conso_tbl); 
	    
        $builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);	 
		$builder->where('voucher_type_id',$voucher_type_id);
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
	   
	    $result      = $builder->get()->getResultArray();
    	$all_records = array();	
        $records     = array();
        foreach($result as $values){
		            $builder   = $this->db->table($comptxnmst_tbl); 
                    $builder->orderBy('txn_id');      
                    $builder->where('voucher_txn_id', $values['voucher_txn_id']);	
                    $builder->where('comp_id', $comp_id);	
            		$result = $builder->get()->getResultArray();
                    foreach($result as $rowvalues){		
                        if($rowvalues['master_id_type']=='itm'){
                          	$itm_txn_tables_result =  $this->items_transactions($comp_id,$rowvalues['master_id'],$rowvalues['txn_id']);
            		  	    if($itm_txn_tables_result)
            			       $all_records[$rowvalues['txn_id']] = $itm_txn_tables_result;
            		        }
                    }
		     }	 	

	 $final_records = array();     
	 foreach($all_records as $row){
	    if(isset($row['mat_cent_id'])){  
	     
	    $material_centre_info = $this->material_centre_info($comp_id,$row['mat_cent_id']);
	    if($material_centre_info)
	        $material_centre = $material_centre_info['mat_cent_name'];
	       else
	       $material_centre  = '';
	    }
	    else
	    $material_centre  = '';
	    
	    $final_records[]=array("voucher_date"=>date("d M Y", strtotime($row['txn_date'])),"bill_no"=>$row['voucher_no'],"item_name"=>$row['item_name'],
	                           "material_centre"=>$material_centre,"phy_stock"=>$row["item_qty"],"book_stock"=>$row['book_stock'],
	                            "stock_diff"=>$row['stock_diff'],"narration"=>$row['item_txn_narr'],"voucher_txn_id"=>$row['voucher_txn_id'],
	                            "voucher_type_id"=>$row['voucher_type_id']
	                            ); 
	     
	 }	     
	 
	 $total_Records	   = count($final_records);  
if($pq_curPage=='0') $pq_curPage='1';
		$offset = ($pq_rPP * ($pq_curPage - 1));

            if ($offset > $total_Records)
            {        
                $pq_curPage = ceil($total_Records / $pq_rPP);
                $offset = ($pq_rPP * ($pq_curPage - 1));
            }
	 	
	 	
	 	
   $menuItems = array_slice( $final_records, $offset, $pq_rPP );	 
		     
    echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($menuItems)."}"; 
      
  } 
  
   function material_centre_info($comp_id,$id){
	   $mat_centre_master_tbl =$comp_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
	   return $this->db->table($mat_centre_master_tbl)->where('mat_cent_id', $id)->where('comp_id', $comp_id)->orderBy('mat_cent_name','ASC')->get()->getRowArray();
	 }
     
  function items_transactions($comp_id,$item_id,$txn_id){
	            $ses_comp_fy_id     = $this->session->get('ses_comp_fy_id');
	            $list               = array();
			    $voucher_txn_table  = $comp_id.'_itemtxnnnn_'.$item_id.'_'.$ses_comp_fy_id;	
			    
			    $item_info = $this->get_item_info($item_id);
                if($item_info)
                  $item_name = $item_info['item_name'];
                else
                  $item_name = "";
                
               
               $item_unit_info  = $this->item_unit_info($comp_id,$item_info['item_unit']); 
             if($item_unit_info)
                 $item_unit_name = $item_unit_info['item_unit'];
              else
                $item_unit_name ='' ;
              
			    
			  
				$builder = $this->db->table($voucher_txn_table); 
				$builder->orderBy('item_txn_date');     
				$builder->where('comp_id', $comp_id);
				$builder->where('item_id', $item_id);
				if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));

				$builder->where('txn_id', $txn_id);
				$values = $builder->get()->getRowArray();
			    $list   = array();
			    if($values){			
				$long_narr_tbl = $this->company_id.'_long_narrn_'.$this->session->get('ses_comp_fy_id');
				$narr = $this->db->table($long_narr_tbl)
							->where('txn_id',$txn_id)
							->where('vch_txn_id',$values['voucher_txn_id'])
							->get()->getRowArray();
				if($narr){
					$narration = $narr['vch_narr'];
				}else
					$narration="";
				
			
			
			
			$vhtxnconso_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	        $vchinfo = $this->db->table($vhtxnconso_tbl)
				            		->where('voucher_txn_id', $values['voucher_txn_id'])
				            		->get()->getRowArray();
									
				
				   //foreach($result as $values){	
                      $account_id   = $values['item_id'];	
                      $acc_txn_drcr = $values['item_txn_drcr'];
                      $acc_txn_narr = $narration;					  
					  $posted_on    = $values['item_txn_date'];
					  $item_qty     = $values['item_bal_qty'];
					  $matrcentrid  = $values['mat_cent_id'];
					  $voucher_no   = $vchinfo['comp_vch_no'];
					  $item_price   = $values['item_txn_amount'];
					  $remarks      = "";
					  $voucher_type_id = $values['voucher_type_id'];
					  $voucher_txn_id = $values['voucher_txn_id'];
					  $itemtxn_qty    = $values['item_txn_qty'];
					  
					  if($remarks){
					      $remarks =  explode("_",$remarks);
					      $book_stock = $remarks[1];
					      $stock_diff = $remarks[2];
					  }else{
					       $book_stock = '';
					       $stock_diff = '';
					      }
					  
					  if($values['item_txn_drcr']=='d'){
						 $debit  = $values['item_txn_amount'];
						 $credit = '0.00';
					   }
					  else if($values['item_txn_drcr']=='c'){
						 $credit = $values['item_txn_amount'];
						 $debit  = '0.00';
						 }
						 $item_txn_amount  = $values['item_txn_amount'];
				 	    $item_balance = $this->get_item_balance($comp_id,$values['item_id'],$values['item_txn_date']);
						$list        = array("itemtxn_qty"=>$itemtxn_qty,"item_txn_amount"=>$item_txn_amount,"voucher_txn_id"=>$voucher_txn_id,"voucher_type_id"=>$voucher_type_id,"stock_diff"=>$stock_diff,"book_stock"=>$book_stock,"voucher_no"=>$voucher_no,"mat_cent_id"=>$matrcentrid,"item_unit"=>$item_unit_name,"item_price"=>$item_price,"item_qty"=>$item_qty,"item_name"=>$item_name,"item_txn_narr"=>$acc_txn_narr,"comp_id"=>$comp_id,"txn_id"=>$values['txn_id'],"txn_date" =>$values['item_txn_date'],'item_id'=>$item_id,'item_txn_drcr'=>$acc_txn_drcr,'debit'=>$debit,'credit'=>$credit,'item_balance'=>$item_balance);
				      // } 
				    }
	   return $list;  
   } 

  	public function load_purchase_register($pq_curPage, $limit, $from_date, $to_date, $view, $pq_filter,$export_excel=0)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->where('acc_grp_parent_id', 7)
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->where('acc_grp_parent_id', 7);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$gstroutsup_tbl = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
			$mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
		$total_Records=0;
		if($export_excel==0){		
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.inwsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 11);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
			if($pq_curPage=='0') { $pq_curPage='1'; }
				$offset = ($limit * ($pq_curPage - 1));

		  if ($offset > $total_Records){        
			$pq_curPage = ceil($total_Records / $limit);
			$offset = ($limit * ($pq_curPage - 1));
		  }
	    }
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.inwsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 11);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			if($export_excel==0){
			$builder->limit($limit,$offset);
			}
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';
				$billno =  $value['inwsup_bill_ref_no'];
	        	$amount = 0;
	    	    $amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
				$party_gst =''; 
		    foreach ($result2 as $key2 => $value2) {
		    		if($value2['master_id_type'] == 'acc')
		    	{
					if($key2==0){
					  $party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);			    	 	
					  if($party_gst_info){
				        $party_gst        = $party_gst_info['acc_gstin'];
					   }else
						$party_gst=''; 
					  }
				}
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			if ($acc_txns['acc_txn_drcr'] == 'd'){
			    			$amount_total += floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}
		    			// if ($acc_txns['acc_txn_drcr'] == 'c'){
			    		// 	$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    		// 	$account_name .=  $account_info['acc_name'].', ';
		    			// }

		    		}
	    		}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';


		    	/* $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	} */
		    $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$mc_name = $value['mat_cent_name'];
		    	}	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
					'bill_no'           =>  $billno,
					'party_gst'         =>  $party_gst,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
					'amount_total'   	=>	$amount_total,					
	    			'mc_name'			=>	$mc_name
					];
		    	
			}

			return [
				'totalRecords'	=>	$total_Records,
				'curPage'	=>	$pq_curPage,
				'data'	=>	$final,
			];
  	}

	  public function load_purchase_register_print( $from_date, $to_date,$pq_filter,$export_excel=0)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->where('acc_grp_parent_id', 7)
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->where('acc_grp_parent_id', 7);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$gstroutsup_tbl = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
			$mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
		$total_Records=0;
		if($export_excel==0){		
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.inwsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		

			$pq_filter_data = $pq_filter;
		
		
		
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
			
			}
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 11);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
		
	    }
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.inwsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		

			$pq_filter_data = $pq_filter;
		
		
		
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
		
			}
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 11);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			if($export_excel==0){
		
			}
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';
				$billno =  $value['inwsup_bill_ref_no'];
	        	$amount = 0;
	    	    $amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
				$party_gst =''; 
		    foreach ($result2 as $key2 => $value2) {
		    		if($value2['master_id_type'] == 'acc')
		    	{
					if($key2==0){
					  $party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);			    	 	
					  if($party_gst_info){
				        $party_gst        = $party_gst_info['acc_gstin'];
					   }else
						$party_gst=''; 
					  }
				}
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			if ($acc_txns['acc_txn_drcr'] == 'd'){
			    			$amount_total += floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}
		    			// if ($acc_txns['acc_txn_drcr'] == 'c'){
			    		// 	$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    		// 	$account_name .=  $account_info['acc_name'].', ';
		    			// }

		    		}
	    		}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';


		    	/* $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	} */
		    $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$mc_name = $value['mat_cent_name'];
		    	}	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
					'bill_no'           =>  $billno,
					'party_gst'         =>  $party_gst,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
					'amount_total'   	=>	$amount_total,					
	    			'mc_name'			=>	$mc_name
					];
		    	
			}

			return $final;
			
  	}

  	public function load_purchase_due_register($pq_curPage, $limit, $from_date, $to_date, $view, $pq_filter,$export_excel=0)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->where('acc_grp_parent_id', 7)
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->where('acc_grp_parent_id', 7);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$gstroutsup_tbl = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
			$mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
			$total_Records=0;
			if($export_excel==0){
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.inwsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');

			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}
			$builder->where('(voucher_tag LIKE "PESIDUE%" OR voucher_tag LIKE "ICEPSUR%")');
			$builder->where($voucher_tbl.'.voucher_type_id', 11);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
				if($pq_curPage=='0') { $pq_curPage='1'; }
					$offset = ($limit * ($pq_curPage - 1));

			  if ($offset > $total_Records){        
				$pq_curPage = ceil($total_Records / $limit);
				$offset = ($limit * ($pq_curPage - 1));
			  }
			}

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.inwsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}
			$builder->where('(voucher_tag LIKE "PESIDUE%" OR voucher_tag LIKE "ICEPSUR%")');
			$builder->where($voucher_tbl.'.voucher_type_id', 11);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			if($export_excel==0){
			$builder->limit($limit,$offset);
			}
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';
				$billno =  $value['inwsup_bill_ref_no'];
				$amount = 0;
				$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->whereIn('master_id_type', ['acc','aco']);
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
			$party_gst ='';
		    foreach ($result2 as $key2 => $value2) {
		    	
		    	if($value2['master_id_type'] == 'acc')
		    	{   if($key2==0){
					  $party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);			    	 	
					  if($party_gst_info){
				        $party_gst        = $party_gst_info['acc_gstin'];
					   }else
						$party_gst=''; 
					  }
		    		$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_txn_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);
						
			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			if ($acc_txns['acc_txn_drcr'] == 'd'){
				    			$amount_total += floatval($acc_txns['acc_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    			// if ($acc_txns['acc_txn_drcr'] == 'c'){
				    		// 	$amount_total += -floatval($acc_txns['acc_txn_amount']);
				    		// 	$account_name .=  $account_info['acc_name'].', ';
			    			// }

			    		}
		    		}
		    	}

		    	if($value2['master_id_type'] == 'aco')
		    	{
					
					if($key2==0){
					$party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);
			    		
					 if($party_gst_info){
				       $party_gst  = $party_gst_info['acc_gstin'].'=='.$key2.'=='.$value2['master_id'];
					 }else
						$party_gst ='';
				     }	
					 
		    		$acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_oth_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);

			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'd'){
				    			$amount_total += floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    			// if ($acc_txns['acc_oth_txn_drcr'] == 'c'){
				    		// 	$amount_total += -floatval($acc_txns['acc_oth_txn_amount']);
				    		// 	$account_name .=  $account_info['acc_name'].', ';
			    			// }

			    		}
		    		}
		    	}	
						
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';


		    	/* $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	} */
		    $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$mc_name = $value['mat_cent_name'];
		    	}	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
					'bill_no'           =>  $billno,
					'party_gst'         =>  $party_gst,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
	    			'amount_total'   	=>	$amount_total,					
	    			'mc_name'			=>	$mc_name
	    		];
		    	
			}

			return [
				'totalRecords'	=>	$total_Records,
				'curPage'	=>	$pq_curPage,
				'data'	=>	$final,
			];
  	}

	  public function load_purchase_due_register_print( $from_date, $to_date,  $pq_filter,$export_excel=0)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->where('acc_grp_parent_id', 7)
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->where('acc_grp_parent_id', 7);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$gstroutsup_tbl = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
			$mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
			$total_Records=0;
			if($export_excel==0){
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.inwsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');

			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		
			$pq_filter_data = $pq_filter;
		
		
	
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
			
			}
			$builder->where('(voucher_tag LIKE "PESIDUE%" OR voucher_tag LIKE "ICEPSUR%")');
			$builder->where($voucher_tbl.'.voucher_type_id', 11);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
		
			}

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.inwsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		
		
			$pq_filter_data = $pq_filter;

			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
	
			}
			$builder->where('(voucher_tag LIKE "PESIDUE%" OR voucher_tag LIKE "ICEPSUR%")');
			$builder->where($voucher_tbl.'.voucher_type_id', 11);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			if($export_excel==0){
	
			}
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';
				$billno =  $value['inwsup_bill_ref_no'];
				$amount = 0;
				$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->whereIn('master_id_type', ['acc','aco']);
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
			$party_gst ='';
		    foreach ($result2 as $key2 => $value2) {
		    	
		    	if($value2['master_id_type'] == 'acc')
		    	{   if($key2==0){
					  $party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);			    	 	
					  if($party_gst_info){
				        $party_gst        = $party_gst_info['acc_gstin'];
					   }else
						$party_gst=''; 
					  }
		    		$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_txn_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);
						
			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			if ($acc_txns['acc_txn_drcr'] == 'd'){
				    			$amount_total += floatval($acc_txns['acc_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    			// if ($acc_txns['acc_txn_drcr'] == 'c'){
				    		// 	$amount_total += -floatval($acc_txns['acc_txn_amount']);
				    		// 	$account_name .=  $account_info['acc_name'].', ';
			    			// }

			    		}
		    		}
		    	}

		    	if($value2['master_id_type'] == 'aco')
		    	{
					
					if($key2==0){
					$party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);
			    		
					 if($party_gst_info){
				       $party_gst  = $party_gst_info['acc_gstin'].'=='.$key2.'=='.$value2['master_id'];
					 }else
						$party_gst ='';
				     }	
					 
		    		$acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_oth_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);

			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'd'){
				    			$amount_total += floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    			// if ($acc_txns['acc_oth_txn_drcr'] == 'c'){
				    		// 	$amount_total += -floatval($acc_txns['acc_oth_txn_amount']);
				    		// 	$account_name .=  $account_info['acc_name'].', ';
			    			// }

			    		}
		    		}
		    	}	
						
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';


		    	/* $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	} */
		    $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$mc_name = $value['mat_cent_name'];
		    	}	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
					'bill_no'           =>  $billno,
					'party_gst'         =>  $party_gst,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
	    			'amount_total'   	=>	$amount_total,					
	    			'mc_name'			=>	$mc_name
	    		];
		    	
			}

			return $final;
  	}

  	public function load_purchase_inward_supplies_expenses_register($pq_curPage, $limit, $from_date, $to_date, $view, $pq_filter,$export_excel=0)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [8,10,11,12,13])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [8,10,11,12,13]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$gstroutsup_tbl = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
			$mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
			$total_Records=0;
			if($export_excel==0){
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.inwsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 11);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
			if($pq_curPage=='0') { $pq_curPage='1'; }
				$offset = ($limit * ($pq_curPage - 1));

		  if ($offset > $total_Records){        
			$pq_curPage = ceil($total_Records / $limit);
			$offset = ($limit * ($pq_curPage - 1));
		  }
	  
			}

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.inwsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 11);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			if($export_excel==0){
			$builder->limit($limit,$offset);
			}
			$result = $builder->get()->getResultArray();
			
			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';
				$billno =  $value['inwsup_bill_ref_no'];
				$amount = 0;
				$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		if($key2==0){
					  $party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);			    	 	
					  if($party_gst_info){
				        $party_gst        = $party_gst_info['acc_gstin'];
					   }else
						$party_gst=''; 
					  }
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			if ($acc_txns['acc_txn_drcr'] == 'd'){
			    			$amount_total += floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}
		    			if ($acc_txns['acc_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}

		    		}
	    		}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';

			$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$mc_name = $value['mat_cent_name'];
		    	}	
		    	/* $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	} */
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'		    =>	$amount,
	    			'mc_name'			=>	$mc_name,
					'bill_no'           =>  $billno,
					'amount_total'   	=>	$amount_total,
					'party_gst'         =>  $party_gst,
	    		];
		    	
			}

			return [
				'totalRecords'	=>	$total_Records,
				'curPage'	=>	$pq_curPage,
				'data'	=>	$final,
			];
  	}


	  public function load_purchase_inward_supplies_expenses_register_print( $from_date, $to_date,  $pq_filter,$export_excel=0)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [8,10,11,12,13])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [8,10,11,12,13]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$gstroutsup_tbl = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
			$mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
			$total_Records=0;
			if($export_excel==0){
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.inwsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		

			$pq_filter_data = $pq_filter;
		

			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
	
			}
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 11);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    

	  
			}

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.inwsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		

			$pq_filter_data = $pq_filter;
		
	
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
	
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 11);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			if($export_excel==0){
			}
			$result = $builder->get()->getResultArray();
			
			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';
				$billno =  $value['inwsup_bill_ref_no'];
				$amount = 0;
				$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		if($key2==0){
					  $party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);			    	 	
					  if($party_gst_info){
				        $party_gst        = $party_gst_info['acc_gstin'];
					   }else
						$party_gst=''; 
					  }
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			if ($acc_txns['acc_txn_drcr'] == 'd'){
			    			$amount_total += floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}
		    			if ($acc_txns['acc_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}

		    		}
	    		}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';

			$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$mc_name = $value['mat_cent_name'];
		    	}	
		    	/* $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	} */
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'		    =>	$amount,
	    			'mc_name'			=>	$mc_name,
					'bill_no'           =>  $billno,
					'amount_total'   	=>	$amount_total,
					'party_gst'         =>  $party_gst,
	    		];
		    	
			}

			return $final;
  	}

  	public function load_purchase_inward_supplies_assets_register($pq_curPage, $limit, $from_date, $to_date, $view, $pq_filter,$export_excel=0)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [1,2,3,4,5])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [1,2,3,4,5]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	    $accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$gstroutsup_tbl = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
			$mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
		$total_Records=0;
		if($export_excel==0){	
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.inwsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 11);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
			if($pq_curPage=='0') { $pq_curPage='1'; }
				$offset = ($limit * ($pq_curPage - 1));

			   if ($offset > $total_Records){        
				$pq_curPage = ceil($total_Records / $limit);
				$offset = ($limit * ($pq_curPage - 1));
			  }
		  
		 }

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.inwsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 11);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			if($export_excel==0){
			$builder->limit($limit,$offset);
			}
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';
				$billno =  $value['inwsup_bill_ref_no'];

				$amount = 0;
				$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		if($key2==0){
					  $party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);			    	 	
					  if($party_gst_info){
				        $party_gst        = $party_gst_info['acc_gstin'];
					   }else
						$party_gst=''; 
					  }
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			if ($acc_txns['acc_txn_drcr'] == 'd'){
			    			$amount_total += floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}
		    			if ($acc_txns['acc_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}

		    		}
	    		}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';


		    	/* $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	} */
		    	
	        $account_name = rtrim($account_name, ", ");     
            $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$mc_name = $value['mat_cent_name'];
		    	}	

	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
					'bill_no'           =>  $billno,
					'party_gst'         =>  $party_gst,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
	    			'mc_name'			=>	$mc_name,
					'amount_total'   	=>	$amount_total
	    		];
		    	
			}

			return [
				'totalRecords'	=>	$total_Records,
				'curPage'	=>	$pq_curPage,
				'data'	=>	$final,
			];
  	}

	  public function load_purchase_inward_supplies_assets_register_print( $from_date, $to_date,  $pq_filter,$export_excel=0)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [1,2,3,4,5])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [1,2,3,4,5]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	    $accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$gstroutsup_tbl = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
			$mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
		$total_Records=0;
		if($export_excel==0){	
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.inwsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		
	
			$pq_filter_data = $pq_filter;
		
	
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				
			}
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 11);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
		
		  
		 }

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.inwsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		

			$pq_filter_data = $pq_filter;
		

			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 

			}
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 11);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			if($export_excel==0){

			}
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';
				$billno =  $value['inwsup_bill_ref_no'];

				$amount = 0;
				$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		if($key2==0){
					  $party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);			    	 	
					  if($party_gst_info){
				        $party_gst        = $party_gst_info['acc_gstin'];
					   }else
						$party_gst=''; 
					  }
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			if ($acc_txns['acc_txn_drcr'] == 'd'){
			    			$amount_total += floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}
		    			if ($acc_txns['acc_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}

		    		}
	    		}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';


		    	/* $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	} */
		    	
	        $account_name = rtrim($account_name, ", ");     
            $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$mc_name = $value['mat_cent_name'];
		    	}	

	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
					'bill_no'           =>  $billno,
					'party_gst'         =>  $party_gst,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
	    			'mc_name'			=>	$mc_name,
					'amount_total'   	=>	$amount_total
	    		];
		    	
			}

			return $final;
  	}

  	public function load_purchase_condensed_register($pq_curPage, $limit, $from_date, $to_date, $view, $pq_filter,$export_excel=0)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [1,2,3,4,5,7,8,10,11,12,13])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [1,2,3,4,5,7,8,10,11,12,13]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$gstroutsup_tbl = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
			$mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
		$total_Records=0;
		if($export_excel==0){	
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.inwsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');

			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 11);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
			if($pq_curPage=='0') { $pq_curPage='1'; }
			$offset = ($limit * ($pq_curPage - 1));

		  if ($offset > $total_Records){        
			$pq_curPage = ceil($total_Records / $limit);
			$offset = ($limit * ($pq_curPage - 1));
		  }
		}

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.inwsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');

			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			
			if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}		
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 11);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			if($export_excel==0){
			$builder->limit($limit,$offset);
			}
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';
				$billno =  $value['inwsup_bill_ref_no'];

				$amount = 0;
				$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->whereIn('master_id_type', ['acc','aco']);
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		
					if($value2['master_id_type'] == 'acc')
		    	{
					if($key2==0){
					  $party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);			    	 	
					  if($party_gst_info){
				        $party_gst        = $party_gst_info['acc_gstin'];
					   }else
						$party_gst=''; 
					  }
		    		$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_txn_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);

			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			if ($acc_txns['acc_txn_drcr'] == 'd'){
				    			$amount_total += floatval($acc_txns['acc_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    			if ($acc_txns['acc_txn_drcr'] == 'c'){
				    			// $amount_total += -floatval($acc_txns['acc_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}

			    		}
		    		}
		    	}

		    	if($value2['master_id_type'] == 'aco')
		    	{
					if($key2==0){
					$party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);
			    		
					 if($party_gst_info){
				       $party_gst  = $party_gst_info['acc_gstin'].'=='.$key2.'=='.$value2['master_id'];
					 }else
						$party_gst ='';
				     }	
		    		$acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_oth_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);

			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'd'){
				    			$amount_total += floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'c'){
				    			// $amount_total += -floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}

			    		}
		    		}
		    	}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';


		    	/* $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	} */
		    $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$mc_name = $value['mat_cent_name'];
		    	}	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
					'party_gst'         =>  $party_gst,
	    			'mc_name'			=>	$mc_name,
					'bill_no'            => $billno,
					'amount_total'		=>	$amount_total,
	    		];
		    	
			}

			return [
				'totalRecords'	=>	$total_Records,
				'curPage'	=>	$pq_curPage,
				'data'	=>	$final,
			];
  	}


	  public function load_purchase_condensed_register_print( $from_date, $to_date, $pq_filter,$export_excel=0)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [1,2,3,4,5,7,8,10,11,12,13])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [1,2,3,4,5,7,8,10,11,12,13]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$gstroutsup_tbl = $this->company_id.'_gstrinwsup_'.$this->session->get('ses_comp_fy_id');
			$mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
		$total_Records=0;
		if($export_excel==0){	
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.inwsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');

			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		
	
			$pq_filter_data = $pq_filter;
		
			
		
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];

				// print_r($search);
				// print_r($search_col);
				// print_r($search_condition);
				// die();


				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 

			}
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 11);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
			
		}

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.inwsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');

			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			
			if(!empty($pq_filter)){
		

			$pq_filter_data = $pq_filter;
		
			
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];

			

				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('inwsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('inwsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('inwsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('inwsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				
			}		
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 11);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			if($export_excel==0){
			
			}
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';
				$billno =  $value['inwsup_bill_ref_no'];

				$amount = 0;
				$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->whereIn('master_id_type', ['acc','aco']);
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		
					if($value2['master_id_type'] == 'acc')
		    	{
					if($key2==0){
					  $party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);			    	 	
					  if($party_gst_info){
				        $party_gst        = $party_gst_info['acc_gstin'];
					   }else
						$party_gst=''; 
					  }
		    		$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_txn_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);

			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			if ($acc_txns['acc_txn_drcr'] == 'd'){
				    			$amount_total += floatval($acc_txns['acc_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    			if ($acc_txns['acc_txn_drcr'] == 'c'){
				    			// $amount_total += -floatval($acc_txns['acc_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}

			    		}
		    		}
		    	}

		    	if($value2['master_id_type'] == 'aco')
		    	{
					if($key2==0){
					$party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);
			    		
					 if($party_gst_info){
				       $party_gst  = $party_gst_info['acc_gstin'].'=='.$key2.'=='.$value2['master_id'];
					 }else
						$party_gst ='';
				     }	
		    		$acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_oth_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);

			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'd'){
				    			$amount_total += floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'c'){
				    			// $amount_total += -floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}

			    		}
		    		}
		    	}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';


		    	/* $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	} */
		    $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$mc_name = $value['mat_cent_name'];
		    	}	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
					'party_gst'         =>  $party_gst,
	    			'mc_name'			=>	$mc_name,
					'bill_no'            => $billno,
					'amount_total'		=>	$amount_total,
	    		];
		    	
			}

			return 	$final;
  	}

  	public function load_purchase_return_register($pq_curPage, $limit, $from_date, $to_date, $view, $search,$export_excel=0)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->where('acc_grp_parent_id', 7)
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->where('acc_grp_parent_id', 7);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		$total_Records=0;
		if($export_excel==0){
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 3);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
			if($pq_curPage=='0') { $pq_curPage='1'; }
				$offset = ($limit * ($pq_curPage - 1));

		  if ($offset > $total_Records){        
			$pq_curPage = ceil($total_Records / $limit);
			$offset = ($limit * ($pq_curPage - 1));
		  }
		}

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 3);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			
			if($export_excel==0){
				$builder->limit($limit,$offset);
			}
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';

	    	$amount = 0;
	    	$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			// if ($acc_txns['acc_txn_drcr'] == 'd'){
			    		// 	$amount_total += floatval($acc_txns['acc_txn_amount']);
			    		// 	$account_name .=  $account_info['acc_name'].', ';
		    			// }
		    			if ($acc_txns['acc_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}

		    		}
	    		}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';


		    	$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	}
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
	    			'mc_name'			=>	$mc_name,
					'amount_total'      =>  $amount_total
	    		];
		    	
			}

			return [
				'totalRecords'	=>	$total_Records,
				'curPage'	=>	$pq_curPage,
				'data'	=>	$final,
			];
  	}

	  public function load_purchase_return_register_print( $from_date, $to_date, $export_excel=0)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->where('acc_grp_parent_id', 7)
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->where('acc_grp_parent_id', 7);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		$total_Records=0;
		if($export_excel==0){
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 3);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    

		}

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 3);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			
			if($export_excel==0){
			
			}
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';

	    	$amount = 0;
	    	$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			// if ($acc_txns['acc_txn_drcr'] == 'd'){
			    		// 	$amount_total += floatval($acc_txns['acc_txn_amount']);
			    		// 	$account_name .=  $account_info['acc_name'].', ';
		    			// }
		    			if ($acc_txns['acc_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}

		    		}
	    		}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';


		    	$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	}
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
	    			'mc_name'			=>	$mc_name,
					'amount_total'      =>  $amount_total
	    		];
		    	
			}

			return	$final;
			
  	}

  	public function load_purchase_return_inward_supplies_expenses_register($pq_curPage, $limit, $from_date, $to_date, $view, $search,$export_excel=0)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [8,10,11,12,13])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [8,10,11,12,13]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		$total_Records=0;
		if($export_excel==0){
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 3);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
			if($pq_curPage=='0') { $pq_curPage='1'; }
				$offset = ($limit * ($pq_curPage - 1));

		  if ($offset > $total_Records){        
			$pq_curPage = ceil($total_Records / $limit);
			$offset = ($limit * ($pq_curPage - 1));
		  }
		}

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 3);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			
		if($export_excel==0){
			$builder->limit($limit,$offset);
		}
			$result = $builder->get()->getResultArray();
			
			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';

	    	$amount = 0;
	    	$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			if ($acc_txns['acc_txn_drcr'] == 'd'){
			    			$amount_total += floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}
		    			if ($acc_txns['acc_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}

		    		}
	    		}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';


		    	$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	}
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
	    			'mc_name'			=>	$mc_name,
					'amount_total'      =>  $amount_total
	    		];
		    	
			}

			return [
				'totalRecords'	=>	$total_Records,
				'curPage'	=>	$pq_curPage,
				'data'	=>	$final,
			];
  	}

	  public function load_purchase_return_inward_supplies_expenses_register_print( $from_date, $to_date, $export_excel=0)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [8,10,11,12,13])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [8,10,11,12,13]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		$total_Records=0;
		if($export_excel==0){
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 3);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
	
		}

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 3);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			
		if($export_excel==0){
		
		}
			$result = $builder->get()->getResultArray();
			
			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';

	    	$amount = 0;
	    	$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			if ($acc_txns['acc_txn_drcr'] == 'd'){
			    			$amount_total += floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}
		    			if ($acc_txns['acc_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}

		    		}
	    		}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';


		    	$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	}
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
	    			'mc_name'			=>	$mc_name,
					'amount_total'      =>  $amount_total
	    		];
		    	
			}

			return 	$final;
			
  	}

  	public function load_purchase_return_inward_supplies_assets_register($pq_curPage, $limit, $from_date, $to_date, $view, $search,$export_excel=0)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [1,2,3,4,5])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [1,2,3,4,5]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		$total_Records=0;
		if($export_excel==0){
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 3);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
			if($pq_curPage=='0') { $pq_curPage='1'; }
				$offset = ($limit * ($pq_curPage - 1));

		  if ($offset > $total_Records){        
			$pq_curPage = ceil($total_Records / $limit);
			$offset = ($limit * ($pq_curPage - 1));
		  }
		}

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 3);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			
		if($export_excel==0){
			$builder->limit($limit,$offset);
		}
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';

				$amount = 0;
				$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			if ($acc_txns['acc_txn_drcr'] == 'd'){
			    			$amount_total += floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}
		    			if ($acc_txns['acc_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}

		    		}
	    		}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';


		    	$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	}
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
	    			'mc_name'			=>	$mc_name,
					'amount_total'      =>  $amount_total
	    		];
		    	
			}

			return [
				'totalRecords'	=>	$total_Records,
				'curPage'	=>	$pq_curPage,
				'data'	=>	$final,
			];
  	}

	  public function load_purchase_return_inward_supplies_assets_register_print( $from_date, $to_date,$export_excel=0)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [1,2,3,4,5])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [1,2,3,4,5]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		$total_Records=0;
		if($export_excel==0){
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 3);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
	
		}

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 3);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			
		if($export_excel==0){
	
		}
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';

				$amount = 0;
				$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			if ($acc_txns['acc_txn_drcr'] == 'd'){
			    			$amount_total += floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}
		    			if ($acc_txns['acc_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}

		    		}
	    		}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';


		    	$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	}
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
	    			'mc_name'			=>	$mc_name,
					'amount_total'      =>  $amount_total
	    		];
		    	
			}

			return 	$final;
			
  	}

  	public function load_purchase_return_condensed_register($pq_curPage, $limit, $from_date, $to_date, $view, $search,$export_excel=0)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [1,2,3,4,5,7,8,10,11,12,13])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [1,2,3,4,5,7,8,10,11,12,13]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		$total_Records=0;
		if($export_excel==0){
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');

			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 3);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
			if($pq_curPage=='0') { $pq_curPage='1'; }
				$offset = ($limit * ($pq_curPage - 1));

		  if ($offset > $total_Records){        
			$pq_curPage = ceil($total_Records / $limit);
			$offset = ($limit * ($pq_curPage - 1));
		  }
		}
		
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');

			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 3);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			
			if($export_excel==0){
				$builder->limit($limit,$offset);
			}
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';

	    	$amount = 0;
	    	$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->whereIn('master_id_type', ['acc','aco']);
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		
					if($value2['master_id_type'] == 'acc')
		    	{
		    		$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_txn_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);

			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			if ($acc_txns['acc_txn_drcr'] == 'd'){
				    			// $amount_total += floatval($acc_txns['acc_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    			if ($acc_txns['acc_txn_drcr'] == 'c'){
				    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}

			    		}
		    		}
		    	}

		    	if($value2['master_id_type'] == 'aco')
		    	{
		    		$acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_oth_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);

			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'd'){
				    			// $amount_total += floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'c'){
				    			$amount_total += -floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}

			    		}
		    		}
		    	}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';


		    	$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	}
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
	    			'mc_name'			=>	$mc_name,
					'amount_total'      =>  $amount_total
	    		];
		    	
			}

			return [
				'totalRecords'	=>	$total_Records,
				'curPage'	=>	$pq_curPage,
				'data'	=>	$final,
			];
  	}
  
	  public function load_purchase_return_condensed_register_print( $from_date, $to_date, $export_excel=0)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [1,2,3,4,5,7,8,10,11,12,13])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [1,2,3,4,5,7,8,10,11,12,13]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		$total_Records=0;
		if($export_excel==0){
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');

			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 3);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    

		}
		
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');

			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 3);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			
			if($export_excel==0){
				
			}
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';

	    	$amount = 0;
	    	$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->whereIn('master_id_type', ['acc','aco']);
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		
					if($value2['master_id_type'] == 'acc')
		    	{
		    		$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_txn_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);

			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			if ($acc_txns['acc_txn_drcr'] == 'd'){
				    			// $amount_total += floatval($acc_txns['acc_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    			if ($acc_txns['acc_txn_drcr'] == 'c'){
				    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}

			    		}
		    		}
		    	}

		    	if($value2['master_id_type'] == 'aco')
		    	{
		    		$acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_oth_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);

			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'd'){
				    			// $amount_total += floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'c'){
				    			$amount_total += -floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}

			    		}
		    		}
		    	}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';


		    	$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	}
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
	    			'mc_name'			=>	$mc_name,
					'amount_total'      =>  $amount_total
	    		];
		    	
			}

			return 	$final;
			
  	}

  	public function load_sale_register($pq_curPage, $limit, $from_date, $to_date, $view, $pq_filter)
  	{    $uuid  = $this->session->get('uuid');
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->where('acc_grp_parent_id', 8)
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
		$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
		$builder = $this->db->table($acctmaster_tbl);
		$builder->select('acc_id');
		$builder->where('acc_grp_parent_id', 8);
		if($groups)
			$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();
	        foreach ($result as $key => $value) {
    		  $accounts[] = $value['acc_id'];
    	    }
    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	   }

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$gstroutsup_tbl = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
			$mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.outsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
           
			if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	   
			if($pq_curPage=='0') { $pq_curPage='1'; }
				$offset = ($limit * ($pq_curPage - 1));

		    if ($offset > $total_Records){        
			  $pq_curPage = ceil($total_Records / $limit);
			   $offset = ($limit * ($pq_curPage - 1));
		    }

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.outsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			
			if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$builder->limit($limit,$offset);
			$result = $builder->get()->getResultArray();
			$final = [];
			$party_gst ='';
			foreach ($result as $key => $value) {
				 // fetch long narration
				$get_narration_info = $this->TransactionModel->get_voucher_narration_info($value['voucher_txn_id'],'long',''); 
				if($get_narration_info){
				$lng_narration = $get_narration_info['vch_narr'];
				}else{
				$lng_narration = '';
				}
			    //$billno =  $this->get_billno_info($value['voucher_txn_id']);
				$billno =  $value['outsup_bill_ref_no'];
				
				$voucher_type_id = $value['voucher_type_id'];
				$vch_subtype_id = $value['vch_subtype_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';
				
				$get_default_template=0;
			if($vch_subtype_id==8){ 
			    $info = array('uuid'=>$uuid,'usr_config_id'=>272,'usr_config_value'=>array(270));
	            $get_default_template = $this->TransactionModel->check_default_prnttheme_info($info);
			 
			 }
			  if($vch_subtype_id==0 || $vch_subtype_id==9 || $vch_subtype_id==10){
				$info = array('uuid'=>$uuid,'usr_config_id'=>273,'usr_config_value'=>array(271));
	            $get_default_template = $this->TransactionModel->check_default_prnttheme_info($info);
			  }

	    	$amount = 0;
	    	$amount_total = 0;
				
				$builder_st = $this->db->table($comp_txn_tbl);
				$builder_st->where('voucher_txn_id', $value['voucher_txn_id']);
				$builder_st->whereIn('master_id_type', ['acc']);
				$builder_st->orderBy('txn_id', 'asc');
				$prtyacc_txns = $builder_st->get()->getRowArray();
				$main_party_id = $prtyacc_txns['master_id'];
		
		
					$party_gst_info   = $this->TransactionModel->party_gst_info($main_party_id);
			    		
					 if($party_gst_info){
				       $party_gst  = $party_gst_info['acc_gstin'];
					 }else
						$party_gst ='';
				    		
					 
				
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);
					$show_party_info = $this->account_info($main_party_id);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			// if ($acc_txns['acc_txn_drcr'] == 'd'){
			    		// 	$amount_total += floatval($acc_txns['acc_txn_amount']);
			    		// 	$account_name .=  $account_info['acc_name'].', ';
		    			// }
		    			if ($acc_txns['acc_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $show_party_info['acc_name'].', ';
		    			}

		    		}
	    		}
	    	}

	    	$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id,master_id_type,txn_id');
				$builder->where('master_id_type', 'bsd');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
				foreach ($result2 as $key2 => $value2) {
		    		
					$sundrytxnn_tbl = $this->company_id.'_sundrytxnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($sundrytxnn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('bill_sundry_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$sundrytxnn = $builder->get()->getRowArray();
			    	
		    	if($sundrytxnn){
		   
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'd'){
		    			$amount_total += floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'c'){
		    			$amount_total += -floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    		}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';

				$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$mc_name = $value['mat_cent_name'];
		    	}
		    	/* $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	} */
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
				    'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,					
	    			'amount'			=>	$amount,
					'amount_total'		=>	$amount_total,
					'bill_no'           =>  $billno,
					'narration'         =>  $lng_narration,
	    			'mc_name'			=>	$mc_name,
					'party_gst'         => $party_gst,
					'get_dft_tmplt'     =>  $get_default_template
	    		];
		    	
			}

			return [
				'totalRecords'	=>	$total_Records,
				'curPage'	=>	$pq_curPage,
				'data'	=>	$final,
			];
  	}

	  public function load_sale_register_print( $from_date, $to_date,  $pq_filter)
  	{    $uuid  = $this->session->get('uuid');
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->where('acc_grp_parent_id', 8)
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
		$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
		$builder = $this->db->table($acctmaster_tbl);
		$builder->select('acc_id');
		$builder->where('acc_grp_parent_id', 8);
		if($groups)
			$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();
	        foreach ($result as $key => $value) {
    		  $accounts[] = $value['acc_id'];
    	    }
    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	   }

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$gstroutsup_tbl = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
			$mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.outsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
           
			if(!empty($pq_filter)){
		
			// $pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter;

			$builder->groupStart();
			
			foreach ($pq_filter_data as $row_data) {
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
			
				if ($search_col == 'voucher_date') {
					$formattedDate = date('Y-m-d', strtotime($search));
			
					if ($search_condition == 'contain') 
						$builder->orLike('voucher_date', $formattedDate);
					if ($search_condition == 'equal') 
						$builder->orWhere('voucher_date', $formattedDate);
					if ($search_condition == 'notequal') 
						$builder->orWhere('voucher_date !=', $formattedDate);
					if ($search_condition == 'less') 
						$builder->orWhere('voucher_date <=', $formattedDate);
					if ($search_condition == 'great') 
						$builder->orWhere('voucher_date >=', $formattedDate);
				}
			
				if ($search_col == 'voucher_no') {
					if ($search_condition == 'contain') 
						$builder->orLike('comp_vch_no', $search);
					if ($search_condition == 'equal') 
						$builder->orWhere('comp_vch_no', $search);
					if ($search_condition == 'notequal') 
						$builder->orWhere('comp_vch_no !=', $search);
					if ($search_condition == 'less') 
						$builder->orWhere('comp_vch_no <=', $search);
					if ($search_condition == 'great') 
						$builder->orWhere('comp_vch_no >=', $search);
				}
			
				if ($search_col == 'bill_no') {
					if ($search_condition == 'contain') 
						$builder->orLike('outsup_bill_ref_no', $search);
					if ($search_condition == 'equal') 
						$builder->orWhere('outsup_bill_ref_no', $search);
					if ($search_condition == 'notequal') 
						$builder->orWhere('outsup_bill_ref_no !=', $search);
					if ($search_condition == 'less') 
						$builder->orWhere('outsup_bill_ref_no <=', $search);
					if ($search_condition == 'great') 
						$builder->orWhere('outsup_bill_ref_no >=', $search);
				}
			
				if ($search_col == 'mc_name') {
					if ($search_condition == 'contain') 
						$builder->orLike('mat_cent_name', $search);
					if ($search_condition == 'equal') 
						$builder->orWhere('mat_cent_name', $search);
					if ($search_condition == 'notequal') 
						$builder->orWhere('mat_cent_name !=', $search);
					if ($search_condition == 'less') 
						$builder->orWhere('mat_cent_name <=', $search);
					if ($search_condition == 'great') 
						$builder->orWhere('mat_cent_name >=', $search);
				}
			}
			
			$builder->groupEnd();
			
		
			
			}
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	   

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.outsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			
			if(!empty($pq_filter)){
		
			// $pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter;

$builder->groupStart();

foreach ($pq_filter_data as $row_data) {
    $search = $row_data['value'];
    $search_col = $row_data['dataIndx'];
    $search_condition = $row_data['condition'];

    if ($search_col == 'voucher_date') {
        $formattedDate = date('Y-m-d', strtotime($search));

        if ($search_condition == 'contain') 
            $builder->orLike('voucher_date', $formattedDate);
        if ($search_condition == 'equal') 
            $builder->orWhere('voucher_date', $formattedDate);
        if ($search_condition == 'notequal') 
            $builder->orWhere('voucher_date !=', $formattedDate);
        if ($search_condition == 'less') 
            $builder->orWhere('voucher_date <=', $formattedDate);
        if ($search_condition == 'great') 
            $builder->orWhere('voucher_date >=', $formattedDate);
    }

    if ($search_col == 'voucher_no') {
        if ($search_condition == 'contain') 
            $builder->orLike('comp_vch_no', $search);
        if ($search_condition == 'equal') 
            $builder->orWhere('comp_vch_no', $search);
        if ($search_condition == 'notequal') 
            $builder->orWhere('comp_vch_no !=', $search);
        if ($search_condition == 'less') 
            $builder->orWhere('comp_vch_no <=', $search);
        if ($search_condition == 'great') 
            $builder->orWhere('comp_vch_no >=', $search);
    }

    if ($search_col == 'bill_no') {
        if ($search_condition == 'contain') 
            $builder->orLike('outsup_bill_ref_no', $search);
        if ($search_condition == 'equal') 
            $builder->orWhere('outsup_bill_ref_no', $search);
        if ($search_condition == 'notequal') 
            $builder->orWhere('outsup_bill_ref_no !=', $search);
        if ($search_condition == 'less') 
            $builder->orWhere('outsup_bill_ref_no <=', $search);
        if ($search_condition == 'great') 
            $builder->orWhere('outsup_bill_ref_no >=', $search);
    }

    if ($search_col == 'mc_name') {
        if ($search_condition == 'contain') 
            $builder->orLike('mat_cent_name', $search);
        if ($search_condition == 'equal') 
            $builder->orWhere('mat_cent_name', $search);
        if ($search_condition == 'notequal') 
            $builder->orWhere('mat_cent_name !=', $search);
        if ($search_condition == 'less') 
            $builder->orWhere('mat_cent_name <=', $search);
        if ($search_condition == 'great') 
            $builder->orWhere('mat_cent_name >=', $search);
    }
}

$builder->groupEnd();

		
	
			}
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$result = $builder->get()->getResultArray();
			// print_r($result);
			// die();
			$final = [];
			$party_gst ='';
			foreach ($result as $key => $value) {
				 // fetch long narration
				$get_narration_info = $this->TransactionModel->get_voucher_narration_info($value['voucher_txn_id'],'long',''); 
				if($get_narration_info){
				$lng_narration = $get_narration_info['vch_narr'];
				}else{
				$lng_narration = '';
				}
			    //$billno =  $this->get_billno_info($value['voucher_txn_id']);
				$billno =  $value['outsup_bill_ref_no'];
				
				$voucher_type_id = $value['voucher_type_id'];
				$vch_subtype_id = $value['vch_subtype_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';
				
				$get_default_template=0;
			if($vch_subtype_id==8){ 
			    $info = array('uuid'=>$uuid,'usr_config_id'=>272,'usr_config_value'=>array(270));
	            $get_default_template = $this->TransactionModel->check_default_prnttheme_info($info);
			 
			 }
			  if($vch_subtype_id==0 || $vch_subtype_id==9 || $vch_subtype_id==10){
				$info = array('uuid'=>$uuid,'usr_config_id'=>273,'usr_config_value'=>array(271));
	            $get_default_template = $this->TransactionModel->check_default_prnttheme_info($info);
			  }

	    	$amount = 0;
	    	$amount_total = 0;
				
				$builder_st = $this->db->table($comp_txn_tbl);
				$builder_st->where('voucher_txn_id', $value['voucher_txn_id']);
				$builder_st->whereIn('master_id_type', ['acc']);
				$builder_st->orderBy('txn_id', 'asc');
				$prtyacc_txns = $builder_st->get()->getRowArray();
				$main_party_id = $prtyacc_txns['master_id'];
		
		
					$party_gst_info   = $this->TransactionModel->party_gst_info($main_party_id);
			    		
					 if($party_gst_info){
				       $party_gst  = $party_gst_info['acc_gstin'];
					 }else
						$party_gst ='';
				    		
					 
				
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);
					$show_party_info = $this->account_info($main_party_id);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			// if ($acc_txns['acc_txn_drcr'] == 'd'){
			    		// 	$amount_total += floatval($acc_txns['acc_txn_amount']);
			    		// 	$account_name .=  $account_info['acc_name'].', ';
		    			// }
		    			if ($acc_txns['acc_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $show_party_info['acc_name'].', ';
		    			}

		    		}
	    		}
	    	}

	    	$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id,master_id_type,txn_id');
				$builder->where('master_id_type', 'bsd');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
				foreach ($result2 as $key2 => $value2) {
		    		
					$sundrytxnn_tbl = $this->company_id.'_sundrytxnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($sundrytxnn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('bill_sundry_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$sundrytxnn = $builder->get()->getRowArray();
			    	
		    	if($sundrytxnn){
		   
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'd'){
		    			$amount_total += floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'c'){
		    			$amount_total += -floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    		}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';

				$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$mc_name = $value['mat_cent_name'];
		    	}
		    	/* $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	} */
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
				    'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,					
	    			'amount'			=>	$amount,
					'amount_total'		=>	$amount_total,
					'bill_no'           =>  $billno,
					'narration'         =>  $lng_narration,
	    			'mc_name'			=>	$mc_name,
					'party_gst'         => $party_gst,
					'get_dft_tmplt'     =>  $get_default_template
	    		];
		    	
			}

			return $final;
  	}


	
	
/******************************* Forced Bill Numbering Start           ***************************/	
	
	public function sale_condensed_register_series($from_date, $to_date, $view)
  	{   $uuid  = $this->session->get('uuid');
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [1,2,3,4,5,7,8,10,11,12,13])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

			$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [1,2,3,4,5,7,8,10,11,12,13]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
			}

			$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select(array($voucher_series_tbl.'.comp_vch_series',$voucher_series_tbl.'.comp_vch_method'));
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');

			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');			
			$result = $builder->get()->getResultArray();

			$final = [];
			foreach ($result as $key => $value) {
				$final[$value['comp_vch_series_id']]=array("series_id"=>$value['comp_vch_series_id'],
				               "series_name"=>$value['comp_vch_series'],
							   "series_method"=>$value['comp_vch_method']);
			}
			
		if($final){
		  $list=array();	
		  foreach($final as $rr){
 		     $list[]=array("series_id"=>$rr['series_id'],
				               "series_name"=>$rr['series_name'],
							   "series_method"=>$rr['series_method'],
							   "view"=>$view);
		    }
		 return json_encode(['status' => true, 'list' => $list,'view'=>$view]);		 
		}
		 return json_encode(['status' => false, 'message' => 'Something went wrong']);
  	}
	
	public function sale_condensed_register_series_vouchers($series_id,$from_date, $to_date, $view){
		$uuid  = $this->session->get('uuid');
		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [1,2,3,4,5,7,8,10,11,12,13])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

			$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [1,2,3,4,5,7,8,10,11,12,13]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
			}

			$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$vchseriesa_tbl = $this->company_id.'_vchseriesa_'.$this->session->get('ses_comp_fy_id');
	
	
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select(array($voucher_series_tbl.'.comp_vch_series',$voucher_series_tbl.'.comp_vch_method'));
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			//$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');

			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->where($voucher_tbl.'.comp_vch_series_id', $series_id);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');			
			$result = $builder->get()->getResultArray();
			$final = [];
			foreach ($result as $key => $value) {
				// fetch bill ref no used in auto numbering
				   $vchsa_builder = $this->db->table($vchseriesa_tbl);
					$vchsa_builder->where('comp_vch_series_id',$value['comp_vch_series_id']);
					$vchsa_result = $vchsa_builder->get()->getRowArray();
					if($value['comp_vch_method']=="1") // in case of automatic
				 	  $bill_no_counter = $vchsa_result['comp_vch_start'];
				    else
					  $bill_no_counter = "0";
				 $final[$value['voucher_txn_id']]=array("voucher_txn_id"=>$value['voucher_txn_id'],
				               "series_id"=>$value['comp_vch_series_id'],
							   "series_method"=>$value['comp_vch_method'],
							   "voucher_no"=>$value['comp_vch_no'],"bno"=>$bill_no_counter,
							   "view"=>$view);
			    }
			if($final){
			  $list=array();	
			  foreach($final as $rr){
				 $list[] = array("series_id"=>$rr['series_id'],
								 "voucher_no"=>$rr['voucher_no'],
								 "series_method"=>$rr['series_method'],
								 "voucher_txn_id"=>$rr['voucher_txn_id'],
								 "view"=>$view,"bno"=>$rr['bno']
								);
				}			
			 return json_encode(['status' => true, 'list' => $list,'view'=>$view]);			 
			}
		    return json_encode(['status' => false, 'message' => 'Something went wrong']);
		
	}
	
	public function sale_outward_supplies_revenue_register_series($from_date, $to_date, $view)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [10,12])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [10,12]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	   }

			$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select(array($voucher_series_tbl.'.comp_vch_series',$voucher_series_tbl.'.comp_vch_method'));
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$result = $builder->get()->getResultArray();

			$final = [];
			foreach ($result as $key => $value) {
				$final[$value['comp_vch_series_id']]=array("series_id"=>$value['comp_vch_series_id'],
				               "series_name"=>$value['comp_vch_series'],
							   "series_method"=>$value['comp_vch_method']);
			}	
		
		if($final){
		  $list=array();	
		  foreach($final as $rr){
 		     $list[]=array("series_id"=>$rr['series_id'],
				               "series_name"=>$rr['series_name'],
							   "series_method"=>$rr['series_method'],
							   "view"=>$view);
		    }
		 return json_encode(['status' => true, 'list' => $list,'view'=>$view]);
		 
		}

		 return json_encode(['status' => false, 'message' => 'Something went wrong']);
  	}
	
	public function sale_outward_supplies_revenue_register_series_vouchers($series_id,$from_date, $to_date, $view){
		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [10,12])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [10,12]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	   }

			$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$vchseriesa_tbl = $this->company_id.'_vchseriesa_'.$this->session->get('ses_comp_fy_id');
	
	
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select(array($voucher_series_tbl.'.comp_vch_series',$voucher_series_tbl.'.comp_vch_method'));
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			//$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->where($voucher_tbl.'.comp_vch_series_id', $series_id);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$result = $builder->get()->getResultArray();
			$final = [];
			foreach ($result as $key => $value) {
				// fetch bill ref no used in auto numbering
				   $vchsa_builder = $this->db->table($vchseriesa_tbl);
					$vchsa_builder->where('comp_vch_series_id',$value['comp_vch_series_id']);
					$vchsa_result = $vchsa_builder->get()->getRowArray();
					if($value['comp_vch_method']=="1") // in case of automatic
				 	  $bill_no_counter = $vchsa_result['comp_vch_start'];
				    else
					  $bill_no_counter = "0";
				 $final[$value['voucher_txn_id']]=array("voucher_txn_id"=>$value['voucher_txn_id'],
				               "series_id"=>$value['comp_vch_series_id'],
							   "series_method"=>$value['comp_vch_method'],
							   "voucher_no"=>$value['comp_vch_no'],"bno"=>$bill_no_counter,
							   "view"=>$view);
			    }
			if($final){
			  $list=array();	
			  foreach($final as $rr){
				 $list[] = array("series_id"=>$rr['series_id'],
								 "voucher_no"=>$rr['voucher_no'],
								 "series_method"=>$rr['series_method'],
								 "voucher_txn_id"=>$rr['voucher_txn_id'],
								 "view"=>$view,"bno"=>$rr['bno']
								);
				}			
			 return json_encode(['status' => true, 'list' => $list,'view'=>$view]);			 
			}
		    return json_encode(['status' => false, 'message' => 'Something went wrong']);
		
	}
	
	public function sale_outward_supplies_assets_register_series($from_date, $to_date, $view)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [1,2,3,4,5])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [1,2,3,4,5]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	     }

    	    $accountss = implode(",",$accounts);
			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select(array($voucher_series_tbl.'.comp_vch_series',$voucher_series_tbl.'.comp_vch_method'));
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');			
			$result = $builder->get()->getResultArray();
			$final = [];
			foreach ($result as $key => $value) {
				$final[$value['comp_vch_series_id']]=array("series_id"=>$value['comp_vch_series_id'],
				               "series_name"=>$value['comp_vch_series'],
							   "series_method"=>$value['comp_vch_method']);
			}			
		 if($final){
		  $list=array();	
		   foreach($final as $rr){
 		     $list[]=array("series_id"=>$rr['series_id'],
				               "series_name"=>$rr['series_name'],
							   "series_method"=>$rr['series_method'],
							   "view"=>$view);		    }
		    return json_encode(['status' => true, 'list' => $list,'view'=>$view]);		 
		 }
		return json_encode(['status' => false, 'message' => 'Something went wrong']);
  	}
	
	public function sale_outward_supplies_assets_register_series_vouchers($series_id,$from_date, $to_date, $view){
		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [1,2,3,4,5])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [1,2,3,4,5]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	     }

    	    $accountss = implode(",",$accounts);
			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$vchseriesa_tbl = $this->company_id.'_vchseriesa_'.$this->session->get('ses_comp_fy_id');
	
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select(array($voucher_series_tbl.'.comp_vch_series',$voucher_series_tbl.'.comp_vch_method'));
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			//$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->where($voucher_tbl.'.comp_vch_series_id', $series_id);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');			
			$result = $builder->get()->getResultArray();
			$final = [];
			foreach ($result as $key => $value) {
				// fetch bill ref no used in auto numbering
				   $vchsa_builder = $this->db->table($vchseriesa_tbl);
					$vchsa_builder->where('comp_vch_series_id',$value['comp_vch_series_id']);
					$vchsa_result = $vchsa_builder->get()->getRowArray();
					if($value['comp_vch_method']=="1") // in case of automatic
				 	  $bill_no_counter = $vchsa_result['comp_vch_start'];
				    else
					  $bill_no_counter = "0";
				 $final[$value['voucher_txn_id']]=array("voucher_txn_id"=>$value['voucher_txn_id'],
				               "series_id"=>$value['comp_vch_series_id'],
							   "series_method"=>$value['comp_vch_method'],
							   "voucher_no"=>$value['comp_vch_no'],"bno"=>$bill_no_counter,
							   "view"=>$view);
			    }
			if($final){
			  $list=array();	
			  foreach($final as $rr){
				 $list[] = array("series_id"=>$rr['series_id'],
								 "voucher_no"=>$rr['voucher_no'],
								 "series_method"=>$rr['series_method'],
								 "voucher_txn_id"=>$rr['voucher_txn_id'],
								 "view"=>$view,"bno"=>$rr['bno']
								);
				}			
			 return json_encode(['status' => true, 'list' => $list,'view'=>$view]);			 
			}
		    return json_encode(['status' => false, 'message' => 'Something went wrong']);
		
	}
	
	public function sale_outward_supplies_expenses_register_series($from_date, $to_date, $view)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [7,10,11,12,13])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [7,10,11,12,13]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select(array($voucher_series_tbl.'.comp_vch_series',$voucher_series_tbl.'.comp_vch_method'));
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');			
			$result = $builder->get()->getResultArray();
			$final = [];
			foreach ($result as $key => $value) {
				$final[$value['comp_vch_series_id']]=array("series_id"=>$value['comp_vch_series_id'],
				               "series_name"=>$value['comp_vch_series'],
							   "series_method"=>$value['comp_vch_method']);
			}
		
		
		if($final){
		  $list=array();	
		  foreach($final as $rr){
 		     $list[]=array("series_id"=>$rr['series_id'],
				               "series_name"=>$rr['series_name'],
							   "series_method"=>$rr['series_method'],
							   "view"=>$view);
		    }
		 return json_encode(['status' => true, 'list' => $list,'view'=>$view]);		 
		 }
		 return json_encode(['status' => false, 'message' => 'Something went wrong']);
  	}
	
	public function sale_outward_supplies_expenses_register_series_vouchers($series_id,$from_date, $to_date, $view){
	  $acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [7,10,11,12,13])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [7,10,11,12,13]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$vchseriesa_tbl = $this->company_id.'_vchseriesa_'.$this->session->get('ses_comp_fy_id');
	
	
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select(array($voucher_series_tbl.'.comp_vch_series',$voucher_series_tbl.'.comp_vch_method'));
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			//$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->where($voucher_tbl.'.comp_vch_series_id', $series_id);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');			
			$result = $builder->get()->getResultArray();
			$final = [];
			foreach ($result as $key => $value) {
				// fetch bill ref no used in auto numbering
				   $vchsa_builder = $this->db->table($vchseriesa_tbl);
					$vchsa_builder->where('comp_vch_series_id',$value['comp_vch_series_id']);
					$vchsa_result = $vchsa_builder->get()->getRowArray();
					if($value['comp_vch_method']=="1") // in case of automatic
				 	  $bill_no_counter = $vchsa_result['comp_vch_start'];
				    else
					  $bill_no_counter = "0";
				 $final[$value['voucher_txn_id']]=array("voucher_txn_id"=>$value['voucher_txn_id'],
				               "series_id"=>$value['comp_vch_series_id'],
							   "series_method"=>$value['comp_vch_method'],
							   "voucher_no"=>$value['comp_vch_no'],"bno"=>$bill_no_counter,
							   "view"=>$view);
			    }
			if($final){
			  $list=array();	
			  foreach($final as $rr){
				 $list[] = array("series_id"=>$rr['series_id'],
								 "voucher_no"=>$rr['voucher_no'],
								 "series_method"=>$rr['series_method'],
								 "voucher_txn_id"=>$rr['voucher_txn_id'],
								 "view"=>$view,"bno"=>$rr['bno']
								);
				}			
			 return json_encode(['status' => true, 'list' => $list,'view'=>$view]);			 
			}
		    return json_encode(['status' => false, 'message' => 'Something went wrong']);	
	}
	
	public function sale_due_register_series($from_date, $to_date, $view)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result         = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->where('acc_grp_parent_id', 8)
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	    $accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->where('acc_grp_parent_id', 8);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	   $accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select(array($voucher_series_tbl.'.comp_vch_series',$voucher_series_tbl.'.comp_vch_method'));
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('(voucher_tag LIKE "SEDCDUE%" OR voucher_tag LIKE "DCESDUE%")');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');			
			$result = $builder->get()->getResultArray();
			$final = [];
			foreach ($result as $key => $value) {
				$final[$value['comp_vch_series_id']]=array("series_id"=>$value['comp_vch_series_id'],
				               "series_name"=>$value['comp_vch_series'],
							   "series_method"=>$value['comp_vch_method']);
			 }
			if($final){
		    $list=array();	
		     foreach($final as $rr){
 		        $list[]=array("series_id"=>$rr['series_id'],
				               "series_name"=>$rr['series_name'],
							   "series_method"=>$rr['series_method'],
							   "view"=>$view);
		      }
		      return json_encode(['status' => true, 'list' => $list,'view'=>$view]);		 
		    }

		 return json_encode(['status' => false, 'message' => 'Something went wrong']);
		
  	}
	public function sale_due_register_series_vouchers($series_id,$from_date, $to_date, $view){
		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result         = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->where('acc_grp_parent_id', 8)
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	    $accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->where('acc_grp_parent_id', 8);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	   $accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$vchseriesa_tbl = $this->company_id.'_vchseriesa_'.$this->session->get('ses_comp_fy_id');
	
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select(array($voucher_series_tbl.'.comp_vch_series',$voucher_series_tbl.'.comp_vch_method'));
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			//$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('(voucher_tag LIKE "SEDCDUE%" OR voucher_tag LIKE "DCESDUE%")');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->where($voucher_tbl.'.comp_vch_series_id', $series_id);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');			
			$result = $builder->get()->getResultArray();
			$final = [];
			foreach ($result as $key => $value) {
				// fetch bill ref no used in auto numbering
				   $vchsa_builder = $this->db->table($vchseriesa_tbl);
					$vchsa_builder->where('comp_vch_series_id',$value['comp_vch_series_id']);
					$vchsa_result = $vchsa_builder->get()->getRowArray();
					if($value['comp_vch_method']=="1") // in case of automatic
				 	  $bill_no_counter = $vchsa_result['comp_vch_start'];
				    else
					  $bill_no_counter = "0";
					
				
				$final[$value['voucher_txn_id']]=array("voucher_txn_id"=>$value['voucher_txn_id'],
				               "series_id"=>$value['comp_vch_series_id'],
							   "series_method"=>$value['comp_vch_method'],
							   "voucher_no"=>$value['comp_vch_no'],"bno"=>$bill_no_counter,
							   "view"=>$view);
			}
		
		
		if($final){
		  $list=array();	
		  foreach($final as $rr){
 		     $list[] = array("series_id"=>$rr['series_id'],
				             "voucher_no"=>$rr['voucher_no'],
						     "series_method"=>$rr['series_method'],
						     "voucher_txn_id"=>$rr['voucher_txn_id'],
						     "view"=>$view,"bno"=>$rr['bno']
						    );
		    }
		
		 return json_encode(['status' => true, 'list' => $list,'view'=>$view]);		 
		}
		return json_encode(['status' => false, 'message' => 'Something went wrong']);		
	}
	
	public function sale_register_series($from_date, $to_date,$view)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->where('acc_grp_parent_id', 8)
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->where('acc_grp_parent_id', 8);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	   }

    	    $accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select(array($voucher_series_tbl.'.comp_vch_series',$voucher_series_tbl.'.comp_vch_method'));
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$result = $builder->get()->getResultArray();
			$final = [];
			foreach ($result as $key => $value) {
				$final[$value['comp_vch_series_id']]=array("series_id"=>$value['comp_vch_series_id'],
				               "series_name"=>$value['comp_vch_series'],
							   "series_method"=>$value['comp_vch_method']);
			}
		
		
		if($final){
		  $list=array();	
		  foreach($final as $rr){
 		     $list[]=array("series_id"=>$rr['series_id'],
				               "series_name"=>$rr['series_name'],
							   "series_method"=>$rr['series_method'],
							   "view"=>$view);
		    }
		 return json_encode(['status' => true, 'list' => $list,'view'=>$view]);
		 
		}

		 return json_encode(['status' => false, 'message' => 'Something went wrong']);
		
  	}
	
	
	public function sale_register_series_vouchers($series_id,$from_date, $to_date,$view)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->where('acc_grp_parent_id', 8)
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->where('acc_grp_parent_id', 8);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	   }

    	    $accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$vchseriesa_tbl = $this->company_id.'_vchseriesa_'.$this->session->get('ses_comp_fy_id');
	
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select(array($voucher_series_tbl.'.comp_vch_series_id',$voucher_series_tbl.'.comp_vch_series',$voucher_series_tbl.'.comp_vch_method'));
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			//$builder->where($voucher_tbl.'.bo_id', $this->bo_id);			
			$builder->where($voucher_tbl.'.comp_vch_series_id', $series_id);			
			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$result = $builder->get()->getResultArray();
			$final = [];
			foreach ($result as $key => $value) {
				// fetch bill ref no used in auto numbering
				   $vchsa_builder = $this->db->table($vchseriesa_tbl);
					$vchsa_builder->where('comp_vch_series_id',$value['comp_vch_series_id']);
					$vchsa_result = $vchsa_builder->get()->getRowArray();
					if($value['comp_vch_method']=="1") // in case of automatic
				 	  $bill_no_counter = $vchsa_result['comp_vch_start'];
				    else
					  $bill_no_counter = "0";
					
				
				$final[$value['voucher_txn_id']]=array("voucher_txn_id"=>$value['voucher_txn_id'],
				               "series_id"=>$value['comp_vch_series_id'],
							   "series_method"=>$value['comp_vch_method'],
							   "voucher_no"=>$value['comp_vch_no'],"bno"=>$bill_no_counter,
							   "view"=>$view);
			}
		
		
		if($final){
		  $list=array();	
		  foreach($final as $rr){
 		     $list[] = array("series_id"=>$rr['series_id'],
				             "voucher_no"=>$rr['voucher_no'],
						     "series_method"=>$rr['series_method'],
						     "voucher_txn_id"=>$rr['voucher_txn_id'],
						     "view"=>$view,"bno"=>$rr['bno']
						    );
		    }
		
		 return json_encode(['status' => true, 'list' => $list,'view'=>$view]);
		 
		}

		 return json_encode(['status' => false, 'message' => 'Something went wrong']);
		
  	}
	
	function verify_series_vouchers($series_id, $voucher_txn_id,$bill_no_counter){
			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$vchseriesa_tbl = $this->company_id.'_vchseriesa_'.$this->session->get('ses_comp_fy_id');
	
	
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select(array($voucher_series_tbl.'.comp_vch_series_id',$voucher_series_tbl.'.comp_vch_series',$voucher_series_tbl.'.comp_vch_method'));
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			//$builder->where($voucher_tbl.'.bo_id', $this->bo_id);			
			$builder->where($voucher_tbl.'.comp_vch_series_id', $series_id);
			$builder->where($voucher_tbl.'.voucher_txn_id', $voucher_txn_id);
            $result = $builder->get()->getRowArray();
			if($result){
				$bo_id = $result['bo_id'];
				if($result['comp_vch_method']=="1"){
					$voucher_type_id = $result['voucher_type_id'];
					$voucher_series  = $result['comp_vch_series_id'];
					$voucher_date    = $result['voucher_date']; 					
					$consodata       = array("vch_bill_ref_no"=>$bill_no_counter);
				    $this->TransactionModel->update_voucher_cons_data($voucher_txn_id,$consodata);
					
					$voucher_info    = $this->TransactionModel->get_voucherconsinfo($voucher_txn_id, $voucher_type_id);
				    $vch_bill_ref_no = $voucher_info['vch_bill_ref_no'];					
				    $bill_no_format  = $this->TransactionModel->get_billno_format($voucher_type_id,$voucher_series,$voucher_date,$vch_bill_ref_no);				   
				    $gstroutsup_insert_data = array("outsup_bill_ref_no"=>$bill_no_format);
				    $this->TransactionModel->updategstroutsup_info($voucher_txn_id,$gstroutsup_insert_data);
				    
					return 1;
					 
				 }
				 else
				  return 0;	 
					
				
			}else
             return 0;				
		
	}
	/******************************* Forced Bill Numbering End           ***************************/
	
	public function load_sale_register_export($from_date, $to_date, $view)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->where('acc_grp_parent_id', 8)
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->where('acc_grp_parent_id', 8);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	

			$accountss = implode(",",$accounts);
	
			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');

			
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {
				 // fetch long narration
				$get_narration_info = $this->TransactionModel->get_voucher_narration_info($value['voucher_txn_id'],'long',''); 
				if($get_narration_info){
				$lng_narration = $get_narration_info['vch_narr'];
				}else{
				$lng_narration = '';
				}
			    $billno =  $this->get_billno_info($value['voucher_txn_id']);
				
				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';

	    	$amount = 0;
	    	$amount_total = 0;
				
				$builder_st = $this->db->table($comp_txn_tbl);
				$builder_st->where('voucher_txn_id', $value['voucher_txn_id']);
				$builder_st->whereIn('master_id_type', ['acc']);
				$builder_st->orderBy('txn_id', 'asc');
				$prtyacc_txns = $builder_st->get()->getRowArray();
				$main_party_id = $prtyacc_txns['master_id'];
		
				
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);
					$show_party_info = $this->account_info($main_party_id);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			// if ($acc_txns['acc_txn_drcr'] == 'd'){
			    		// 	$amount_total += floatval($acc_txns['acc_txn_amount']);
			    		// 	$account_name .=  $account_info['acc_name'].', ';
		    			// }
		    			if ($acc_txns['acc_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $show_party_info['acc_name'].', ';
		    			}

		    		}
	    		}
	    	}

	    	$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id,master_id_type,txn_id');
				$builder->where('master_id_type', 'bsd');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
				foreach ($result2 as $key2 => $value2) {
		    		
					$sundrytxnn_tbl = $this->company_id.'_sundrytxnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($sundrytxnn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('bill_sundry_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$sundrytxnn = $builder->get()->getRowArray();
			    	
		    	if($sundrytxnn){
		   
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'd'){
		    			$amount_total += floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'c'){
		    			$amount_total += -floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    		}
	    	}
		    	
	    	if($amount_total >= 0){
	    		$amount_cur = formatAmount($amount_total) . ' DR';
				$amount = $amount_total;
				$balance_type ='DR';
			}
	    	else
			{
	    		$amount_cur = formatAmount(abs($amount_total)) . ' CR';
			    $amount = $amount_total;
				$balance_type ='CR';
			}


		    	$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	}
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,					
	    			'amount_cur'		=>	$amount_cur,
					'amount'			=>	$amount,
					'balance_type'      =>  $balance_type,
					'bill_no'           =>  $billno,
					'narration'         =>  $lng_narration,
	    			'mc_name'			=>	$mc_name,
					'amount_total'      =>  $amount_total
	    		];
		    	
			}

			return $final;
  	}
	
	function get_billno_info($voucher_txn_id){
		$billno ='';
		$gstroutsup_tbl = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
		$response = $this->db->table($gstroutsup_tbl)->select('outsup_bill_ref_no')->where('voucher_txn_id',$voucher_txn_id)->get()->getRowArray();
	   if($response){
		 $billno = $response['outsup_bill_ref_no'];
	  }
	  return $billno;
	}

  	public function load_sale_due_register($pq_curPage, $limit, $from_date, $to_date, $view, $pq_filter)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->where('acc_grp_parent_id', 8)
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->where('acc_grp_parent_id', 8);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$gstroutsup_tbl = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
			$mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');


			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.outsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');

			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}
			$builder->where('(voucher_tag LIKE "SEDCDUE%" OR voucher_tag LIKE "DCESDUE%")');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
	    if($pq_curPage=='0') { $pq_curPage='1'; }
			$offset = ($limit * ($pq_curPage - 1));

      if ($offset > $total_Records){        
        $pq_curPage = ceil($total_Records / $limit);
        $offset = ($limit * ($pq_curPage - 1));
      }

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.outsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}
			$builder->where('(voucher_tag LIKE "SEDCDUE%" OR voucher_tag LIKE "DCESDUE%")');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$builder->limit($limit,$offset);
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$vch_subtype_id = $value['vch_subtype_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';
				
				$get_default_template=0;
			if($vch_subtype_id==8){ 
			    $info = array('uuid'=>$uuid,'usr_config_id'=>272,'usr_config_value'=>array(270));
	            $get_default_template = $this->TransactionModel->check_default_prnttheme_info($info);
			 
			 }
			  if($vch_subtype_id==0 || $vch_subtype_id==9 || $vch_subtype_id==10){
				$info = array('uuid'=>$uuid,'usr_config_id'=>273,'usr_config_value'=>array(271));
	            $get_default_template = $this->TransactionModel->check_default_prnttheme_info($info);
			  }
				
				// fetch long narration
				$get_narration_info = $this->TransactionModel->get_voucher_narration_info($value['voucher_txn_id'],'long',''); 
				if($get_narration_info){
				$lng_narration = $get_narration_info['vch_narr'];
				}else{
				$lng_narration = '';
				}
			    //$billno =  $this->get_billno_info($value['voucher_txn_id']);
				$billno =  $value['outsup_bill_ref_no'];
				

	    	$amount = 0;
	    	$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->whereIn('master_id_type', ['acc','aco']);
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    	
		    	if($value2['master_id_type'] == 'acc')
		    	{
					if($key2==0){
					$party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);
			    		
					 if($party_gst_info){
				       $party_gst  = $party_gst_info['acc_gstin'];
					 }else
						$party_gst ='';
					}
					
		    		$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_txn_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);

			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			// if ($acc_txns['acc_txn_drcr'] == 'd'){
				    		// 	$amount_total += floatval($acc_txns['acc_txn_amount']);
				    		// 	$account_name .=  $account_info['acc_name'].', ';
			    			// }
			    			if ($acc_txns['acc_txn_drcr'] == 'c'){
				    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}

			    		}
		    		}
		    	}

		    	if($value2['master_id_type'] == 'aco')
		    	{
					if($key2==0){
					$party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);
			    		
					 if($party_gst_info){
				       $party_gst  = $party_gst_info['acc_gstin'];
					 }else
						$party_gst ='';
					}
					
		    		$acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_oth_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);

			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			// if ($acc_txns['acc_oth_txn_drcr'] == 'd'){
				    		// 	$amount_total += floatval($acc_txns['acc_oth_txn_amount']);
				    		// 	$account_name .=  $account_info['acc_name'].', ';
			    			// }
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'c'){
				    			$amount_total += -floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}

			    		}
		    		}
		    	}	
	
	    	}

	    	$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id,master_id_type,txn_id');
				$builder->whereIn('master_id_type', ['bsd','bso']);
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
				foreach ($result2 as $key2 => $value2) {
		    		
		    	if($value2['master_id_type'] == 'bsd')
		    	{
		    		$sundrytxnn_tbl = $this->company_id.'_sundrytxnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($sundrytxnn_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('bill_sundry_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$sundrytxnn = $builder->get()->getRowArray();
				    	
			    	if($sundrytxnn){
			   
		    			if ($sundrytxnn['sundry_txn_drcr'] == 'd'){
			    			$amount_total += floatval($sundrytxnn['sundry_txn_amount']);
		    			}
		    			if ($sundrytxnn['sundry_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($sundrytxnn['sundry_txn_amount']);
		    			}
		    		}
		    	}

		    	if($value2['master_id_type'] == 'bso')
		    	{
		    		$acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_oth_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('acc_oth_txn_tag','BILSDRY');
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);

			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'd'){
				    			$amount_total += floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'c'){
				    			$amount_total += -floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    		}
		    		}
		    	}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';

			$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$mc_name = $value['mat_cent_name'];
		    	}
		    	/* $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	} */
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
					'amount_total'		=>	$amount_total,
	    			'mc_name'			=>	$mc_name,
					'bill_no'           =>  $billno,
					'narration'         =>  $lng_narration,
					'get_dft_tmplt'     =>  $get_default_template
	    		];
		    	
			}

			return [
				'totalRecords'	=>	$total_Records,
				'curPage'	=>	$pq_curPage,
				'data'	=>	$final,
			];
  	}
	
	  public function load_sale_due_register_print( $from_date, $to_date,  $pq_filter)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->where('acc_grp_parent_id', 8)
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->where('acc_grp_parent_id', 8);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$gstroutsup_tbl = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
			$mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');


			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.outsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');

			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		

			$pq_filter_data = $pq_filter;
		
			
	
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
			
			}
			$builder->where('(voucher_tag LIKE "SEDCDUE%" OR voucher_tag LIKE "DCESDUE%")');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    


			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.outsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		

			$pq_filter_data = $pq_filter;
		

			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
		
			}
			$builder->where('(voucher_tag LIKE "SEDCDUE%" OR voucher_tag LIKE "DCESDUE%")');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$vch_subtype_id = $value['vch_subtype_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';
				
				$get_default_template=0;
			if($vch_subtype_id==8){ 
			    $info = array('uuid'=>$uuid,'usr_config_id'=>272,'usr_config_value'=>array(270));
	            $get_default_template = $this->TransactionModel->check_default_prnttheme_info($info);
			 
			 }
			  if($vch_subtype_id==0 || $vch_subtype_id==9 || $vch_subtype_id==10){
				$info = array('uuid'=>$uuid,'usr_config_id'=>273,'usr_config_value'=>array(271));
	            $get_default_template = $this->TransactionModel->check_default_prnttheme_info($info);
			  }
				
				// fetch long narration
				$get_narration_info = $this->TransactionModel->get_voucher_narration_info($value['voucher_txn_id'],'long',''); 
				if($get_narration_info){
				$lng_narration = $get_narration_info['vch_narr'];
				}else{
				$lng_narration = '';
				}
			    //$billno =  $this->get_billno_info($value['voucher_txn_id']);
				$billno =  $value['outsup_bill_ref_no'];
				

	    	$amount = 0;
	    	$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->whereIn('master_id_type', ['acc','aco']);
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    	
		    	if($value2['master_id_type'] == 'acc')
		    	{
					if($key2==0){
					$party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);
			    		
					 if($party_gst_info){
				       $party_gst  = $party_gst_info['acc_gstin'];
					 }else
						$party_gst ='';
					}
					
		    		$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_txn_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);

			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			// if ($acc_txns['acc_txn_drcr'] == 'd'){
				    		// 	$amount_total += floatval($acc_txns['acc_txn_amount']);
				    		// 	$account_name .=  $account_info['acc_name'].', ';
			    			// }
			    			if ($acc_txns['acc_txn_drcr'] == 'c'){
				    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}

			    		}
		    		}
		    	}

		    	if($value2['master_id_type'] == 'aco')
		    	{
					if($key2==0){
					$party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);
			    		
					 if($party_gst_info){
				       $party_gst  = $party_gst_info['acc_gstin'];
					 }else
						$party_gst ='';
					}
					
		    		$acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_oth_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);

			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			// if ($acc_txns['acc_oth_txn_drcr'] == 'd'){
				    		// 	$amount_total += floatval($acc_txns['acc_oth_txn_amount']);
				    		// 	$account_name .=  $account_info['acc_name'].', ';
			    			// }
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'c'){
				    			$amount_total += -floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}

			    		}
		    		}
		    	}	
	
	    	}

	    	$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id,master_id_type,txn_id');
				$builder->whereIn('master_id_type', ['bsd','bso']);
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
				foreach ($result2 as $key2 => $value2) {
		    		
		    	if($value2['master_id_type'] == 'bsd')
		    	{
		    		$sundrytxnn_tbl = $this->company_id.'_sundrytxnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($sundrytxnn_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('bill_sundry_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$sundrytxnn = $builder->get()->getRowArray();
				    	
			    	if($sundrytxnn){
			   
		    			if ($sundrytxnn['sundry_txn_drcr'] == 'd'){
			    			$amount_total += floatval($sundrytxnn['sundry_txn_amount']);
		    			}
		    			if ($sundrytxnn['sundry_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($sundrytxnn['sundry_txn_amount']);
		    			}
		    		}
		    	}

		    	if($value2['master_id_type'] == 'bso')
		    	{
		    		$acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_oth_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('acc_oth_txn_tag','BILSDRY');
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);

			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'd'){
				    			$amount_total += floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'c'){
				    			$amount_total += -floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    		}
		    		}
		    	}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';

			$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$mc_name = $value['mat_cent_name'];
		    	}
		    	/* $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	} */
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
					'amount_total'		=>	$amount_total,
	    			'mc_name'			=>	$mc_name,
					'bill_no'           =>  $billno,
					'narration'         =>  $lng_narration,
					'get_dft_tmplt'     =>  $get_default_template
	    		];
		    	
			}

			return 	$final;
  	}
	public function load_sale_due_register_export($from_date, $to_date, $view)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->where('acc_grp_parent_id', 8)
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->where('acc_grp_parent_id', 8);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');


			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('(voucher_tag LIKE "SEDCDUE%" OR voucher_tag LIKE "DCESDUE%")');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');			
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';
				
				// fetch long narration
				$get_narration_info = $this->TransactionModel->get_voucher_narration_info($value['voucher_txn_id'],'long',''); 
				if($get_narration_info){
				$lng_narration = $get_narration_info['vch_narr'];
				}else{
				$lng_narration = '';
				}
			    $billno =  $this->get_billno_info($value['voucher_txn_id']);
				

	    	$amount = 0;
	    	$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->whereIn('master_id_type', ['acc','aco']);
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    	
		    	if($value2['master_id_type'] == 'acc')
		    	{
		    		$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_txn_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);

			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			// if ($acc_txns['acc_txn_drcr'] == 'd'){
				    		// 	$amount_total += floatval($acc_txns['acc_txn_amount']);
				    		// 	$account_name .=  $account_info['acc_name'].', ';
			    			// }
			    			if ($acc_txns['acc_txn_drcr'] == 'c'){
				    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}

			    		}
		    		}
		    	}

		    	if($value2['master_id_type'] == 'aco')
		    	{
		    		$acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_oth_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);

			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			// if ($acc_txns['acc_oth_txn_drcr'] == 'd'){
				    		// 	$amount_total += floatval($acc_txns['acc_oth_txn_amount']);
				    		// 	$account_name .=  $account_info['acc_name'].', ';
			    			// }
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'c'){
				    			$amount_total += -floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}

			    		}
		    		}
		    	}	
	
	    	}

	    	$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id,master_id_type,txn_id');
				$builder->whereIn('master_id_type', ['bsd','bso']);
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
				foreach ($result2 as $key2 => $value2) {
		    		
		    	if($value2['master_id_type'] == 'bsd')
		    	{
		    		$sundrytxnn_tbl = $this->company_id.'_sundrytxnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($sundrytxnn_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('bill_sundry_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$sundrytxnn = $builder->get()->getRowArray();
				    	
			    	if($sundrytxnn){
			   
		    			if ($sundrytxnn['sundry_txn_drcr'] == 'd'){
			    			$amount_total += floatval($sundrytxnn['sundry_txn_amount']);
		    			}
		    			if ($sundrytxnn['sundry_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($sundrytxnn['sundry_txn_amount']);
		    			}
		    		}
		    	}

		    	if($value2['master_id_type'] == 'bso')
		    	{
		    		$acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_oth_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('acc_oth_txn_tag','BILSDRY');
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);

			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'd'){
				    			$amount_total += floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'c'){
				    			$amount_total += -floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    		}
		    		}
		    	}
	    	}
		    	
	    	if($amount_total >= 0){
	    		$amount = $amount_total;
				$amount_cur = formatAmount($amount_total) . ' DR';
				$balance_type ='DR';
			}
	    	else{
	    		$amount_cur = formatAmount(abs($amount_total)) . ' CR';
				$balance_type ='CR';
				$amount = $amount_total;
				
			}


		    	$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	}
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
					'balance_type'      =>  $balance_type,
					'amount_cur'		=>	$amount_cur,
	    			'mc_name'			=>	$mc_name,
					'bill_no'           =>  $billno,
					'narration'         =>  $lng_narration,
					'amount_total'      =>  $amount_total
	    		];
		    	
			}

			return $final;
  	}

  	public function load_sale_outward_supplies_expenses_register($pq_curPage, $limit, $from_date, $to_date, $view, $search)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [7,10,11,12,13])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [7,10,11,12,13]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$gstroutsup_tbl = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
			$mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.outsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
			
			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}	
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
	    if($pq_curPage=='0') { $pq_curPage='1'; }
			$offset = ($limit * ($pq_curPage - 1));

      if ($offset > $total_Records){        
        $pq_curPage = ceil($total_Records / $limit);
        $offset = ($limit * ($pq_curPage - 1));
      }

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.outsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}	
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$builder->limit($limit,$offset);
			$result = $builder->get()->getResultArray();
			
			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';
				$vch_subtype_id = $value['vch_subtype_id'];
				$get_default_template=0;
			if($vch_subtype_id==8){ 
			    $info = array('uuid'=>$uuid,'usr_config_id'=>272,'usr_config_value'=>array(270));
	            $get_default_template = $this->TransactionModel->check_default_prnttheme_info($info);
			 
			 }
			  if($vch_subtype_id==0 || $vch_subtype_id==9 || $vch_subtype_id==10){
				$info = array('uuid'=>$uuid,'usr_config_id'=>273,'usr_config_value'=>array(271));
	            $get_default_template = $this->TransactionModel->check_default_prnttheme_info($info);
			  }
				
				// fetch long narration
				$get_narration_info = $this->TransactionModel->get_voucher_narration_info($value['voucher_txn_id'],'long',''); 
				if($get_narration_info){
				$lng_narration = $get_narration_info['vch_narr'];
				}else{
				$lng_narration = '';
				}
			    //$billno =  $this->get_billno_info($value['voucher_txn_id']);
				$billno =  $value['outsup_bill_ref_no'];

	    	$amount = 0;
	    	$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
				$party_gst ='';
		    foreach ($result2 as $key2 => $value2) {
		    		if($key2==0){
					$party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);
			    		
					 if($party_gst_info){
				       $party_gst  = $party_gst_info['acc_gstin'];
					 }else
						$party_gst ='';
					}
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			if ($acc_txns['acc_txn_drcr'] == 'd'){
			    			$amount_total += floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}
		    			if ($acc_txns['acc_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}

		    		}
	    		}
	    	}

	    	$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id,master_id_type,txn_id');
				$builder->where('master_id_type', 'bsd');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
				foreach ($result2 as $key2 => $value2) {
		    		
					$sundrytxnn_tbl = $this->company_id.'_sundrytxnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($sundrytxnn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('bill_sundry_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$sundrytxnn = $builder->get()->getRowArray();
			    	
		    	if($sundrytxnn){
		   
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'd'){
		    			$amount_total += floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'c'){
		    			$amount_total += -floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    		}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';
				
				$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$mc_name = $value['mat_cent_name'];
		    	}
		    	/* $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	} */
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
					'amount_total'		=>	$amount_total,
	    			'mc_name'			=>	$mc_name,
					'bill_no'           =>  $billno,
					'narration'         =>  $lng_narration,
					'party_gst'         =>  $party_gst,
					'get_dft_tmplt'     =>  $get_default_template
	    		];
		    	
			}

			return [
				'totalRecords'	=>	$total_Records,
				'curPage'	=>	$pq_curPage,
				'data'	=>	$final,
			];
  	}
	


	  public function load_sale_outward_supplies_expenses_register_print( $from_date, $to_date)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [7,10,11,12,13])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [7,10,11,12,13]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$gstroutsup_tbl = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
			$mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.outsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
			
			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}	
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
	
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.outsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}	
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
	
			$result = $builder->get()->getResultArray();
			
			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';
				$vch_subtype_id = $value['vch_subtype_id'];
				$get_default_template=0;
			if($vch_subtype_id==8){ 
			    $info = array('uuid'=>$uuid,'usr_config_id'=>272,'usr_config_value'=>array(270));
	            $get_default_template = $this->TransactionModel->check_default_prnttheme_info($info);
			 
			 }
			  if($vch_subtype_id==0 || $vch_subtype_id==9 || $vch_subtype_id==10){
				$info = array('uuid'=>$uuid,'usr_config_id'=>273,'usr_config_value'=>array(271));
	            $get_default_template = $this->TransactionModel->check_default_prnttheme_info($info);
			  }
				
				// fetch long narration
				$get_narration_info = $this->TransactionModel->get_voucher_narration_info($value['voucher_txn_id'],'long',''); 
				if($get_narration_info){
				$lng_narration = $get_narration_info['vch_narr'];
				}else{
				$lng_narration = '';
				}
			    //$billno =  $this->get_billno_info($value['voucher_txn_id']);
				$billno =  $value['outsup_bill_ref_no'];

	    	$amount = 0;
	    	$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
				$party_gst ='';
		    foreach ($result2 as $key2 => $value2) {
		    		if($key2==0){
					$party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);
			    		
					 if($party_gst_info){
				       $party_gst  = $party_gst_info['acc_gstin'];
					 }else
						$party_gst ='';
					}
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			if ($acc_txns['acc_txn_drcr'] == 'd'){
			    			$amount_total += floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}
		    			if ($acc_txns['acc_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}

		    		}
	    		}
	    	}

	    	$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id,master_id_type,txn_id');
				$builder->where('master_id_type', 'bsd');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
				foreach ($result2 as $key2 => $value2) {
		    		
					$sundrytxnn_tbl = $this->company_id.'_sundrytxnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($sundrytxnn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('bill_sundry_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$sundrytxnn = $builder->get()->getRowArray();
			    	
		    	if($sundrytxnn){
		   
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'd'){
		    			$amount_total += floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'c'){
		    			$amount_total += -floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    		}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';
				
				$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$mc_name = $value['mat_cent_name'];
		    	}
		    	/* $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	} */
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
					'amount_total'		=>	$amount_total,
	    			'mc_name'			=>	$mc_name,
					'bill_no'           =>  $billno,
					'narration'         =>  $lng_narration,
					'party_gst'         =>  $party_gst,
					'get_dft_tmplt'     =>  $get_default_template
	    		];
		    	
			}

			return $final;


  	}

	public function load_sale_outward_supplies_expenses_register_export($from_date, $to_date, $view)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [7,10,11,12,13])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [7,10,11,12,13]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}


    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');


			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');			
			$result = $builder->get()->getResultArray();
			
			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';
				
				// fetch long narration
				$get_narration_info = $this->TransactionModel->get_voucher_narration_info($value['voucher_txn_id'],'long',''); 
				if($get_narration_info){
				$lng_narration = $get_narration_info['vch_narr'];
				}else{
				$lng_narration = '';
				}
			    $billno =  $this->get_billno_info($value['voucher_txn_id']);

	    	$amount = 0;
	    	$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			if ($acc_txns['acc_txn_drcr'] == 'd'){
			    			$amount_total += floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}
		    			if ($acc_txns['acc_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}

		    		}
	    		}
	    	}

	    	$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id,master_id_type,txn_id');
				$builder->where('master_id_type', 'bsd');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
				foreach ($result2 as $key2 => $value2) {
		    		
					$sundrytxnn_tbl = $this->company_id.'_sundrytxnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($sundrytxnn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('bill_sundry_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$sundrytxnn = $builder->get()->getRowArray();
			    	
		    	if($sundrytxnn){
		   
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'd'){
		    			$amount_total += floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'c'){
		    			$amount_total += -floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    		}
	    	}
		    
			if($amount_total >= 0){
							$amount = $amount_total;
							$amount_cur = formatAmount($amount_total) . ' DR';
							$balance_type ='DR';
						}
						else{
							$amount_cur = formatAmount(abs($amount_total)) . ' CR';
							$balance_type ='CR';
							$amount = $amount_total;
							
						}
			
	    	


		    	$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	}
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
					'balance_type'      =>  $balance_type,
					'amount_cur'		=>	$amount_cur,
	    			'mc_name'			=>	$mc_name,
					'bill_no'           =>  $billno,
					'narration'         =>  $lng_narration,
					'amount_total'      =>  $amount_total
	    		];
		    	
			}

			return $final;
  	}

  	public function load_sale_outward_supplies_assets_register($pq_curPage, $limit, $from_date, $to_date, $view, $pq_filter)
  	{  $uuid                               = $this->session->get('uuid');
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [1,2,3,4,5])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [1,2,3,4,5]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$gstroutsup_tbl = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
			$mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
			$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");		 
			
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.outsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);			
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
	    if($pq_curPage=='0') { $pq_curPage='1'; }
			$offset = ($limit * ($pq_curPage - 1));

      if ($offset > $total_Records){        
        $pq_curPage = ceil($total_Records / $limit);
        $offset = ($limit * ($pq_curPage - 1));
      }
			$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");		 

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.outsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			
			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
		if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);			
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$builder->limit($limit,$offset);
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';
				$vch_subtype_id = $value['vch_subtype_id'];
				$get_default_template=0;
			if($vch_subtype_id==8){ 
			    $info = array('uuid'=>$uuid,'usr_config_id'=>272,'usr_config_value'=>array(270));
	            $get_default_template = $this->TransactionModel->check_default_prnttheme_info($info);
			 
			 }
			  if($vch_subtype_id==0 || $vch_subtype_id==9 || $vch_subtype_id==10){
				$info = array('uuid'=>$uuid,'usr_config_id'=>273,'usr_config_value'=>array(271));
	            $get_default_template = $this->TransactionModel->check_default_prnttheme_info($info);
			  }
				
				// fetch long narration
				$get_narration_info = $this->TransactionModel->get_voucher_narration_info($value['voucher_txn_id'],'long',''); 
				if($get_narration_info){
				$lng_narration = $get_narration_info['vch_narr'];
				}else{
				$lng_narration = '';
				}
			   // $billno =  $this->get_billno_info($value['voucher_txn_id']);
				$billno =  $value['outsup_bill_ref_no'];

	    	$amount = 0;
	    	$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
			$party_gst ='';
		    foreach ($result2 as $key2 => $value2) {
		    		if($key2==0){
					$party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);
			    		
					 if($party_gst_info){
				       $party_gst  = $party_gst_info['acc_gstin'];
					 }else
						$party_gst ='';
					}
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			if ($acc_txns['acc_txn_drcr'] == 'd'){
			    			$amount_total += floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}
		    			if ($acc_txns['acc_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}

		    		}
	    		}
	    	}

	    	$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id,master_id_type,txn_id');
				$builder->where('master_id_type', 'bsd');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
				foreach ($result2 as $key2 => $value2) {
		    		
					$sundrytxnn_tbl = $this->company_id.'_sundrytxnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($sundrytxnn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('bill_sundry_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$sundrytxnn = $builder->get()->getRowArray();
			    	
		    	if($sundrytxnn){
		   
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'd'){
		    			$amount_total += floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'c'){
		    			$amount_total += -floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    		}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';

$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$mc_name = $value['mat_cent_name'];
		    	}
		    	/* $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	} */
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
					'amount_total'		=>	$amount_total,
	    			'mc_name'			=>	$mc_name,
					'bill_no'           =>  $billno,
					'narration'         =>  $lng_narration,
					'party_gst'         => $party_gst,
					'get_dft_tmplt'     =>  $get_default_template
	    		];
		    	
			}

			return [
				'totalRecords'	=>	$total_Records,
				'curPage'	=>	$pq_curPage,
				'data'	=>	$final,
			];
  	}

	  public function load_sale_outward_supplies_assets_register_print($from_date, $to_date, $pq_filter)
  	{  $uuid                               = $this->session->get('uuid');
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [1,2,3,4,5])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [1,2,3,4,5]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$gstroutsup_tbl = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
			$mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
			$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");		 
			
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.outsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);			
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
	 
			$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");		 

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.outsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			
			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
		if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);			
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';
				$vch_subtype_id = $value['vch_subtype_id'];
				$get_default_template=0;
			if($vch_subtype_id==8){ 
			    $info = array('uuid'=>$uuid,'usr_config_id'=>272,'usr_config_value'=>array(270));
	            $get_default_template = $this->TransactionModel->check_default_prnttheme_info($info);
			 
			 }
			  if($vch_subtype_id==0 || $vch_subtype_id==9 || $vch_subtype_id==10){
				$info = array('uuid'=>$uuid,'usr_config_id'=>273,'usr_config_value'=>array(271));
	            $get_default_template = $this->TransactionModel->check_default_prnttheme_info($info);
			  }
				
				// fetch long narration
				$get_narration_info = $this->TransactionModel->get_voucher_narration_info($value['voucher_txn_id'],'long',''); 
				if($get_narration_info){
				$lng_narration = $get_narration_info['vch_narr'];
				}else{
				$lng_narration = '';
				}
			   // $billno =  $this->get_billno_info($value['voucher_txn_id']);
				$billno =  $value['outsup_bill_ref_no'];

	    	$amount = 0;
	    	$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
			$party_gst ='';
		    foreach ($result2 as $key2 => $value2) {
		    		if($key2==0){
					$party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);
			    		
					 if($party_gst_info){
				       $party_gst  = $party_gst_info['acc_gstin'];
					 }else
						$party_gst ='';
					}
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			if ($acc_txns['acc_txn_drcr'] == 'd'){
			    			$amount_total += floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}
		    			if ($acc_txns['acc_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}

		    		}
	    		}
	    	}

	    	$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id,master_id_type,txn_id');
				$builder->where('master_id_type', 'bsd');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
				foreach ($result2 as $key2 => $value2) {
		    		
					$sundrytxnn_tbl = $this->company_id.'_sundrytxnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($sundrytxnn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('bill_sundry_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$sundrytxnn = $builder->get()->getRowArray();
			    	
		    	if($sundrytxnn){
		   
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'd'){
		    			$amount_total += floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'c'){
		    			$amount_total += -floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    		}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';

$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$mc_name = $value['mat_cent_name'];
		    	}
		    	/* $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	} */
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
					'amount_total'		=>	$amount_total,
	    			'mc_name'			=>	$mc_name,
					'bill_no'           =>  $billno,
					'narration'         =>  $lng_narration,
					'party_gst'         => $party_gst,
					'get_dft_tmplt'     =>  $get_default_template
	    		];
		    	
			}

			return $final;
  	}
	
	public function load_sale_outward_supplies_assets_register_export($from_date, $to_date, $view)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [1,2,3,4,5])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [1,2,3,4,5]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}


    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');

			
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');			
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';
				
				// fetch long narration
				$get_narration_info = $this->TransactionModel->get_voucher_narration_info($value['voucher_txn_id'],'long',''); 
				if($get_narration_info){
				$lng_narration = $get_narration_info['vch_narr'];
				}else{
				$lng_narration = '';
				}
			    $billno =  $this->get_billno_info($value['voucher_txn_id']);
				

	    	$amount = 0;
	    	$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			if ($acc_txns['acc_txn_drcr'] == 'd'){
			    			$amount_total += floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}
		    			if ($acc_txns['acc_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}

		    		}
	    		}
	    	}

	    	$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id,master_id_type,txn_id');
				$builder->where('master_id_type', 'bsd');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
				foreach ($result2 as $key2 => $value2) {
		    		
					$sundrytxnn_tbl = $this->company_id.'_sundrytxnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($sundrytxnn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('bill_sundry_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$sundrytxnn = $builder->get()->getRowArray();
			    	
		    	if($sundrytxnn){
		   
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'd'){
		    			$amount_total += floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'c'){
		    			$amount_total += -floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    		}
	    	}
		    	
	    	if($amount_total >= 0){
							$amount = $amount_total;
							$amount_cur = formatAmount($amount_total) . ' DR';
							$balance_type ='DR';
						}
						else{
							$amount_cur = formatAmount(abs($amount_total)) . ' CR';
							$balance_type ='CR';
							$amount = $amount_total;
							
						}


		    	$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	}
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
					'balance_type'      =>  $balance_type,
					'amount_cur'		=>	$amount_cur,
	    			'mc_name'			=>	$mc_name,
					'bill_no'           =>  $billno,
					'narration'         =>  $lng_narration,
					'amount_total'      =>  $amount_total
	    		];
		    	
			}

			return $final;
  	}

  	public function load_sale_outward_supplies_revenue_register($pq_curPage, $limit, $from_date, $to_date, $view, $search)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [10,12])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [10,12]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
            $gstroutsup_tbl = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
			$mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
			$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");		 
			 
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.outsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
				
			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			
			if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}
			
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);			
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
	    if($pq_curPage=='0') { $pq_curPage='1'; }
			$offset = ($limit * ($pq_curPage - 1));

      if ($offset > $total_Records){        
        $pq_curPage = ceil($total_Records / $limit);
        $offset = ($limit * ($pq_curPage - 1));
      }
			$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");		 
			
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.outsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
					
			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);			
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$builder->limit($limit,$offset);
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';
				$vch_subtype_id = $value['vch_subtype_id'];
				$get_default_template=0;
			if($vch_subtype_id==8){ 
			    $info = array('uuid'=>$uuid,'usr_config_id'=>272,'usr_config_value'=>array(270));
	            $get_default_template = $this->TransactionModel->check_default_prnttheme_info($info);
			 
			 }
			  if($vch_subtype_id==0 || $vch_subtype_id==9 || $vch_subtype_id==10){
				$info = array('uuid'=>$uuid,'usr_config_id'=>273,'usr_config_value'=>array(271));
	            $get_default_template = $this->TransactionModel->check_default_prnttheme_info($info);
			  }
				
				// fetch long narration
				$get_narration_info = $this->TransactionModel->get_voucher_narration_info($value['voucher_txn_id'],'long',''); 
				if($get_narration_info){
				$lng_narration = $get_narration_info['vch_narr'];
				}else{
				$lng_narration = '';
				}
			    //$billno =  $this->get_billno_info($value['voucher_txn_id']);
				$billno =  $value['outsup_bill_ref_no'];
				

	    	$amount = 0;
	    	$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
		   $party_gst ='';
		    foreach ($result2 as $key2 => $value2) {
		    		if($key2==0){
					$party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);
			    		
					 if($party_gst_info){
				       $party_gst  = $party_gst_info['acc_gstin'];
					 }else
						$party_gst ='';
					}
					
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			if ($acc_txns['acc_txn_drcr'] == 'd'){
			    			$amount_total += floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}
		    			if ($acc_txns['acc_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}

		    		}
	    		}
	    	}

	    	$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id,master_id_type,txn_id');
				$builder->where('master_id_type', 'bsd');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
				foreach ($result2 as $key2 => $value2) {
		    		
					$sundrytxnn_tbl = $this->company_id.'_sundrytxnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($sundrytxnn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('bill_sundry_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$sundrytxnn = $builder->get()->getRowArray();
			    	
		    	if($sundrytxnn){
		   
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'd'){
		    			$amount_total += floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'c'){
		    			$amount_total += -floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    		}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';


		    	/* $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	} */
		    	$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$mc_name = $value['mat_cent_name'];
		    	}
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
					'amount_total'		=>	$amount_total,
	    			'mc_name'			=>	$mc_name,
					'bill_no'           =>  $billno,
					'narration'         =>  $lng_narration,
					'party_gst'         =>  $party_gst,
					'get_dft_tmplt'     =>  $get_default_template
	    		];
		    	
			}

			return [
				'totalRecords'	=>	$total_Records,
				'curPage'	=>	$pq_curPage,
				'data'	=>	$final,
			];
  	}
	
	  public function load_sale_outward_supplies_revenue_register_print( $from_date, $to_date)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [10,12])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [10,12]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
            $gstroutsup_tbl = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
			$mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
			$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");		 
			 
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.outsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
				
			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			
			if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}
			
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);			
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
	
			$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");		 
			
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.outsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
					
			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);			
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';
				$vch_subtype_id = $value['vch_subtype_id'];
				$get_default_template=0;
			if($vch_subtype_id==8){ 
			    $info = array('uuid'=>$uuid,'usr_config_id'=>272,'usr_config_value'=>array(270));
	            $get_default_template = $this->TransactionModel->check_default_prnttheme_info($info);
			 
			 }
			  if($vch_subtype_id==0 || $vch_subtype_id==9 || $vch_subtype_id==10){
				$info = array('uuid'=>$uuid,'usr_config_id'=>273,'usr_config_value'=>array(271));
	            $get_default_template = $this->TransactionModel->check_default_prnttheme_info($info);
			  }
				
				// fetch long narration
				$get_narration_info = $this->TransactionModel->get_voucher_narration_info($value['voucher_txn_id'],'long',''); 
				if($get_narration_info){
				$lng_narration = $get_narration_info['vch_narr'];
				}else{
				$lng_narration = '';
				}
			    //$billno =  $this->get_billno_info($value['voucher_txn_id']);
				$billno =  $value['outsup_bill_ref_no'];
				

	    	$amount = 0;
	    	$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
		   $party_gst ='';
		    foreach ($result2 as $key2 => $value2) {
		    		if($key2==0){
					$party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);
			    		
					 if($party_gst_info){
				       $party_gst  = $party_gst_info['acc_gstin'];
					 }else
						$party_gst ='';
					}
					
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			if ($acc_txns['acc_txn_drcr'] == 'd'){
			    			$amount_total += floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}
		    			if ($acc_txns['acc_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}

		    		}
	    		}
	    	}

	    	$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id,master_id_type,txn_id');
				$builder->where('master_id_type', 'bsd');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
				foreach ($result2 as $key2 => $value2) {
		    		
					$sundrytxnn_tbl = $this->company_id.'_sundrytxnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($sundrytxnn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('bill_sundry_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$sundrytxnn = $builder->get()->getRowArray();
			    	
		    	if($sundrytxnn){
		   
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'd'){
		    			$amount_total += floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'c'){
		    			$amount_total += -floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    		}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';


		    	/* $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	} */
		    	$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$mc_name = $value['mat_cent_name'];
		    	}
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
					'amount_total'		=>	$amount_total,
	    			'mc_name'			=>	$mc_name,
					'bill_no'           =>  $billno,
					'narration'         =>  $lng_narration,
					'party_gst'         =>  $party_gst,
					'get_dft_tmplt'     =>  $get_default_template
	    		];
		    	
			}

			return $final;
			
  	}


	public function load_sale_outward_supplies_revenue_register_export($from_date, $to_date)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [10,12])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [10,12]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	
        if($accounts)
    	$accountss = implode(",",$accounts);
	   else
		   $accountss =0;

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');

			
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');			
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';
				
				// fetch long narration
				$get_narration_info = $this->TransactionModel->get_voucher_narration_info($value['voucher_txn_id'],'long',''); 
				if($get_narration_info){
				$lng_narration = $get_narration_info['vch_narr'];
				}else{
				$lng_narration = '';
				}
			    $billno =  $this->get_billno_info($value['voucher_txn_id']);
				

	    	$amount = 0;
	    	$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			if ($acc_txns['acc_txn_drcr'] == 'd'){
			    			$amount_total += floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}
		    			if ($acc_txns['acc_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}

		    		}
	    		}
	    	}

	    	$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id,master_id_type,txn_id');
				$builder->where('master_id_type', 'bsd');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
				foreach ($result2 as $key2 => $value2) {
		    		
					$sundrytxnn_tbl = $this->company_id.'_sundrytxnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($sundrytxnn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('bill_sundry_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$sundrytxnn = $builder->get()->getRowArray();
			    	
		    	if($sundrytxnn){
		   
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'd'){
		    			$amount_total += floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'c'){
		    			$amount_total += -floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    		}
	    	}
		    	
	    	if($amount_total >= 0){
							$amount = $amount_total;
							$amount_cur = formatAmount($amount_total) . ' DR';
							$balance_type ='DR';
						}
						else{
							$amount_cur = formatAmount(abs($amount_total)) . ' CR';
							$balance_type ='CR';
							$amount = $amount_total;
							
						}


		    	$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	}
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
					'balance_type'      =>  $balance_type,
					'amount_cur'		=>	$amount_cur,
	    			'mc_name'			=>	$mc_name,
					'bill_no'           =>  $billno,
					'narration'         =>  $lng_narration,
					'amount_total'      =>  $amount_total
	    		];
		    	
			}

			return $final;
  	}

  	public function load_sale_condensed_register($pq_curPage, $limit, $from_date, $to_date, $view, $pq_filter,$filterall=0)
  	{
			 $start = microtime(true);
			 $acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
			 $result = $this->db->table($acctgroupn_tbl)
								->select('acc_grp_id')
								->whereIn('acc_grp_parent_id', [1,2,3,4,5,7,8,10,11,12,13])
								->get()->getResultArray();
			//$elapsed = microtime(true) - $start;
			$elapsed = round((microtime(true) - $start) * 1000, 3);
		    $lsquery = $this->db->getlastquery();
			$qry_response=['url'=>current_url(true)->getPath(),'query'=>$lsquery,'time_seconds'=>$elapsed];
			$this->TransactionModel->SaveServerQueryLog($qry_response);			
			$groups = [];
			foreach ($result as $key => $value) {
				$groups[] = $value['acc_grp_id'];
			}
				
			$start = microtime(true);	
    	    $accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);			
			$builder->select('acc_id');							
			$builder->whereIn('acc_grp_parent_id', [1,2,3,4,5,7,8,10,11,12,13]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();
			$elapsed = round((microtime(true) - $start) * 1000, 3);
			$lsquery = $this->db->getlastquery();
			$qry_response=['url'=>current_url(true)->getPath(),'query'=>$lsquery,'time_seconds'=>$elapsed];
			$this->TransactionModel->SaveServerQueryLog($qry_response);
			foreach ($result as $key => $value) {
				$accounts[] = $value['acc_id'];
			}

			if(!$accounts){
				return [
						'totalRecords'	=>	0,
						'curPage'	=>	1,
						'data'	=>	[],
					];
			}
			 $start = microtime(true);
    	    $accountss   = implode(",",$accounts);
			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$gstroutsup_tbl = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
			$mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
			$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");		 
			$total_Records=0;
			if($filterall==0){
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.outsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			
			if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
			$elapsed = round((microtime(true) - $start) * 1000, 3);
			$lsquery = $this->db->getlastquery();
			$qry_response=['url'=>current_url(true)->getPath(),'query'=>$lsquery,'time_seconds'=>$elapsed];
			$this->TransactionModel->SaveServerQueryLog($qry_response);
	        if($pq_curPage==0){ $pq_curPage=1;}
	      	$offset = ($limit * ($pq_curPage - 1));

	      	if ($offset > $total_Records)
	      	{        
	      		$pq_curPage = ceil($total_Records / $limit);
	      		$offset = ($limit * ($pq_curPage - 1));
	      	}
			if($offset<0)
             $offset=0;			
			}
			$start = microtime(true);
			$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");		 
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id');
			$builder->select($gstroutsup_tbl.'.outsup_bill_ref_no');
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');
            if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			
			if(!empty($pq_filter)){
			
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}
			
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);			
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			if($filterall==0){
			$builder->limit($limit,$offset);
			}
			$result = $builder->get()->getResultArray();
			 $elapsed = round((microtime(true) - $start) * 1000, 3);
			 $lsquery = $this->db->getlastquery();
			$qry_response=['url'=>current_url(true)->getPath(),'query'=>$lsquery,'time_seconds'=>$elapsed];
			$this->TransactionModel->SaveServerQueryLog($qry_response);
			// echo $this->db->getlastquery();
			//die();
			$final = [];
            $main_total=0;
			$uuid  = $this->session->get('uuid');
			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$vch_subtype_id = $value['vch_subtype_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';
				$get_default_template=0;
			   if($vch_subtype_id==8){ 
			    $info = array('uuid'=>$uuid,'usr_config_id'=>272,'usr_config_value'=>array(270));
	            $get_default_template = $this->TransactionModel->check_default_prnttheme_info($info);
			 
			 }
			  if($vch_subtype_id==0 || $vch_subtype_id==9 || $vch_subtype_id==10){
				$info = array('uuid'=>$uuid,'usr_config_id'=>273,'usr_config_value'=>array(271));
	            $get_default_template = $this->TransactionModel->check_default_prnttheme_info($info);
			  }
			 
	    	$amount = 0;
	    	$amount_total = 0;
				
				// fetch long narration
				$get_narration_info = $this->TransactionModel->get_voucher_narration_info($value['voucher_txn_id'],'long',''); 
				if($get_narration_info){
				$lng_narration = $get_narration_info['vch_narr'];
				}else{
				$lng_narration = '';
				}
			    $billno =  $value['outsup_bill_ref_no'];
				
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->whereIn('master_id_type', ['acc','aco']);
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
			$party_gst =''; 
		    foreach ($result2 as $key2 => $value2) {
		    		$amount_total = 0;
					
					if($value2['master_id_type'] == 'acc')
		    	{
					if($key2==0){
					  $party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);			    	 	
					  if($party_gst_info){
				        $party_gst        = $party_gst_info['acc_gstin'];
					   }else
						$party_gst=''; 
					  }					 
		    		    $acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						if ($this->db->tableExists($acc_txn_tbl)) {
						$builder = $this->db->table($acc_txn_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
						}else {
							$acc_txns = ''; // or handle error/log message
						}
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);
						if (in_array($value2['master_id'], $accounts)){
			   
			    			if ($acc_txns['acc_txn_drcr'] == 'd'){
				    			// $amount_total += floatval($acc_txns['acc_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    			if ($acc_txns['acc_txn_drcr'] == 'c'){
				    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}

			    		}
		    		}
		    	}

		    	if($value2['master_id_type'] == 'aco')
		    	{    if($key2==0){
					$party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);
			    		
					 if($party_gst_info){
				       $party_gst  = $party_gst_info['acc_gstin'].'=='.$key2.'=='.$value2['master_id'];
					 }else
						$party_gst ='';
				     }					
		    		$acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_oth_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);

			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'd'){
				    			// $amount_total += floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'c'){
				    			$amount_total += -floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}

			    		}
		    		}
		    	}
	    	}
		

				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id,master_id_type,txn_id');
				$builder->where('master_id_type', 'bsd');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();				
				foreach ($result2 as $key2 => $value2) {
		    		
					$sundrytxnn_tbl = $this->company_id.'_sundrytxnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($sundrytxnn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('bill_sundry_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$sundrytxnn = $builder->get()->getRowArray();
			    	
		    	if($sundrytxnn){
		   
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'd'){
		    			$amount_total += floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'c'){
		    			$amount_total += -floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    		}
	    	}
		    	
	    	if($amount_total >= 0){
	    		$amount = formatAmount($amount_total) . ' DR';
				
			}
	    	else{
	    		$amount = formatAmount(abs($amount_total)) . ' CR';
				
			}


		    	/* $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	} */
				$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$mc_name = $value['mat_cent_name'];
		    	}
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
				    'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
					'amount_total'		=>	$amount_total,					
	    			'mc_name'			=>	$mc_name,
					'bill_no'           =>  $billno,
					'narration'         =>  $lng_narration,
					'party_gst'         =>  $party_gst,
					'get_dft_tmplt'     =>  $get_default_template,
					'get_dft_tmplt'     =>  $get_default_template,
					'pq_cellattr'       =>  ['account_name'=>['title'=>$account_name],
					                         'narration'=>['title'=>$lng_narration]
										    ]
	    		];
		    
			}
			
			
			
		  	return [
				'totalRecords'	=>	$total_Records,
				'curPage'	    =>	$pq_curPage,
				'data'	        =>	$final,
			];
  	}

	  public function load_sale_condensed_register_print( $from_date, $to_date, $pq_filter,$filterall=0)
  	{
		   
			$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
			$result = $this->db->table($acctgroupn_tbl)
								->select('acc_grp_id')
								->whereIn('acc_grp_parent_id', [1,2,3,4,5,7,8,10,11,12,13])
								->get()->getResultArray();
			$groups = [];
			foreach ($result as $key => $value) {
				$groups[] = $value['acc_grp_id'];
			}

    	    $accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);			
			$builder->select('acc_id');							
			$builder->whereIn('acc_grp_parent_id', [1,2,3,4,5,7,8,10,11,12,13]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

			foreach ($result as $key => $value) {
				$accounts[] = $value['acc_id'];
			}

			if(!$accounts){
				return [
						'totalRecords'	=>	0,
						'curPage'	=>	1,
						'data'	=>	[],
					];
			}

    	    $accountss   = implode(",",$accounts);
			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$gstroutsup_tbl = $this->company_id.'_gstroutsup_'.$this->session->get('ses_comp_fy_id');
			$mcmasternn_tbl = $this->company_id.'_mcmasternn_'.$this->session->get('ses_comp_fy_id');
			$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");		 
			$total_Records=0;
			if($filterall==0){
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id','left');
			$builder->select($gstroutsup_tbl.'.outsup_bill_ref_no');			
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			
			if(!empty($pq_filter)){
		
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
			}
			$this->db->query("SET sql_mode = ( SELECT REPLACE(@@sql_mode, 'ONLY_FULL_GROUP_BY', '') );");		 
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->join($gstroutsup_tbl, $gstroutsup_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id');
			$builder->select($gstroutsup_tbl.'.outsup_bill_ref_no');
			$builder->join($mcmasternn_tbl, $mcmasternn_tbl.'.mat_cent_id  ='.$voucher_tbl.'.mat_cent_id','left');
			$builder->select($mcmasternn_tbl.'.mat_cent_name');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');
            if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
			
			if(!empty($pq_filter)){
			
			$pq_filter_mode = $pq_filter['mode'];
			$pq_filter_data = $pq_filter['data'];
		
			if($pq_filter_mode=='OR' && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->orWhere('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->orWhere('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->orWhere('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->orWhere('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->orWhere('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->orWhere('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->orWhere('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}
			if($pq_filter_mode=='AND'  && CheckEmptyFilters($pq_filter_data)==1){
			$builder->groupStart();
			foreach($pq_filter_data as $row_data){
				$search = $row_data['value'];
				$search_col = $row_data['dataIndx'];
				$search_condition = $row_data['condition'];
				if($search_col=='voucher_date'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('voucher_date', date('Y-m-d',strtotime($search))); 
					if($search_condition=='notequal') 
					   $builder->Where('voucher_date !=', date('Y-m-d',strtotime($search))); 	
					if($search_condition=='less') 
					   $builder->Where('voucher_date <=', date('Y-m-d',strtotime($search)));
					if($search_condition=='great') 
					   $builder->Where('voucher_date >=', date('Y-m-d',strtotime($search))); 						
					
				 }
			    if($search_col=='voucher_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('comp_vch_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('comp_vch_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('comp_vch_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('comp_vch_no >=', $search); 						
			     }
				 
				if($search_col=='bill_no'){
				    if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('outsup_bill_ref_no', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('outsup_bill_ref_no !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('outsup_bill_ref_no <=', $search);
					if($search_condition=='great') 
					   $builder->Where('outsup_bill_ref_no >=', $search);
				}
			    if($search_col=='mc_name'){
					if($search_condition=='contain' || $search_condition=='equal') 
					   $builder->Where('mat_cent_name', $search); 
					if($search_condition=='notequal') 
					   $builder->Where('mat_cent_name !=', $search); 	
					if($search_condition=='less') 
					   $builder->Where('mat_cent_name <=', $search);
					if($search_condition=='great') 
					   $builder->Where('mat_cent_name >=', $search);
				   }				
				}
				 $builder->groupEnd(); 
				}	
			}
			
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);			
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			
			$result = $builder->get()->getResultArray();
			
			//die();
			$final = [];
            $main_total=0;
			$uuid  = $this->session->get('uuid');
			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$vch_subtype_id = $value['vch_subtype_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';
				$get_default_template=0;
			   if($vch_subtype_id==8){ 
			    $info = array('uuid'=>$uuid,'usr_config_id'=>272,'usr_config_value'=>array(270));
	            $get_default_template = $this->TransactionModel->check_default_prnttheme_info($info);
			 
			 }
			  if($vch_subtype_id==0 || $vch_subtype_id==9 || $vch_subtype_id==10){
				$info = array('uuid'=>$uuid,'usr_config_id'=>273,'usr_config_value'=>array(271));
	            $get_default_template = $this->TransactionModel->check_default_prnttheme_info($info);
			  }
			 
	    	$amount = 0;
	    	$amount_total = 0;
				
				// fetch long narration
				$get_narration_info = $this->TransactionModel->get_voucher_narration_info($value['voucher_txn_id'],'long',''); 
				if($get_narration_info){
				$lng_narration = $get_narration_info['vch_narr'];
				}else{
				$lng_narration = '';
				}
			    $billno =  $value['outsup_bill_ref_no'];
				
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->whereIn('master_id_type', ['acc','aco']);
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
			$party_gst =''; 
		    foreach ($result2 as $key2 => $value2) {
		    		$amount_total = 0;
					
					if($value2['master_id_type'] == 'acc')
		    	{
					if($key2==0){
					  $party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);			    	 	
					  if($party_gst_info){
				        $party_gst        = $party_gst_info['acc_gstin'];
					   }else
						$party_gst=''; 
					  }					 
		    		$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_txn_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);
						if (in_array($value2['master_id'], $accounts)){
			   
			    			if ($acc_txns['acc_txn_drcr'] == 'd'){
				    			// $amount_total += floatval($acc_txns['acc_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    			if ($acc_txns['acc_txn_drcr'] == 'c'){
				    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}

			    		}
		    		}
		    	}

		    	if($value2['master_id_type'] == 'aco')
		    	{    if($key2==0){
					$party_gst_info   = $this->TransactionModel->party_gst_info($value2['master_id']);
			    		
					 if($party_gst_info){
				       $party_gst  = $party_gst_info['acc_gstin'].'=='.$key2.'=='.$value2['master_id'];
					 }else
						$party_gst ='';
				     }					
		    		$acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_oth_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);

			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'd'){
				    			// $amount_total += floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'c'){
				    			$amount_total += -floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}

			    		}
		    		}
		    	}
	    	}
		

				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id,master_id_type,txn_id');
				$builder->where('master_id_type', 'bsd');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();				
				foreach ($result2 as $key2 => $value2) {
		    		
					$sundrytxnn_tbl = $this->company_id.'_sundrytxnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($sundrytxnn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('bill_sundry_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$sundrytxnn = $builder->get()->getRowArray();
			    	
		    	if($sundrytxnn){
		   
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'd'){
		    			$amount_total += floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'c'){
		    			$amount_total += -floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    		}
	    	}
		    	
	    	if($amount_total >= 0){
	    		$amount = formatAmount($amount_total) . ' DR';
				
			}
	    	else{
	    		$amount = formatAmount(abs($amount_total)) . ' CR';
				
			}


		    	/* $mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	} */
				$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$mc_name = $value['mat_cent_name'];
		    	}
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
				    'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
					'amount_total'		=>	$amount_total,					
	    			'mc_name'			=>	$mc_name,
					'bill_no'           =>  $billno,
					'narration'         =>  $lng_narration,
					'party_gst'         =>  $party_gst,
					'get_dft_tmplt'     =>  $get_default_template,
					'get_dft_tmplt'     =>  $get_default_template,
					'pq_cellattr'       =>  ['account_name'=>['title'=>$account_name],
					                         'narration'=>['title'=>$lng_narration]
										    ]
	    		];
		    
			}
			
			
			
		  	return 	$final;
  	}
	
	public function load_sale_condensed_register_export($from_date, $to_date, $view)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [1,2,3,4,5,7,8,10,11,12,13])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [1,2,3,4,5,7,8,10,11,12,13]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');

			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);
				
			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 18);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';

	    	$amount = 0;
	    	$amount_total = 0;
				
				// fetch long narration
				$get_narration_info = $this->TransactionModel->get_voucher_narration_info($value['voucher_txn_id'],'long',''); 
				if($get_narration_info){
				$lng_narration = $get_narration_info['vch_narr'];
				}else{
				$lng_narration = '';
				}
			    $billno =  $this->get_billno_info($value['voucher_txn_id']);
				
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->whereIn('master_id_type', ['acc','aco']);
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		
					if($value2['master_id_type'] == 'acc')
		    	{
		    		$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_txn_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);

			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			if ($acc_txns['acc_txn_drcr'] == 'd'){
				    			// $amount_total += floatval($acc_txns['acc_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    			if ($acc_txns['acc_txn_drcr'] == 'c'){
				    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}

			    		}
		    		}
		    	}

		    	if($value2['master_id_type'] == 'aco')
		    	{
		    		$acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_oth_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);

			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'd'){
				    			// $amount_total += floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'c'){
				    			$amount_total += -floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}

			    		}
		    		}
		    	}
	    	}

	    	$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id,master_id_type,txn_id');
				$builder->where('master_id_type', 'bsd');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();
				foreach ($result2 as $key2 => $value2) {
		    		
					$sundrytxnn_tbl = $this->company_id.'_sundrytxnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($sundrytxnn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('bill_sundry_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$sundrytxnn = $builder->get()->getRowArray();
			    	
		    	if($sundrytxnn){
		   
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'd'){
		    			$amount_total += floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    			if ($sundrytxnn['sundry_txn_drcr'] == 'c'){
		    			$amount_total += -floatval($sundrytxnn['sundry_txn_amount']);
	    			}
	    		}
	    	}
		    	
	    	if($amount_total >= 0){
							$amount = $amount_total;
							$amount_cur = formatAmount($amount_total) . ' DR';
							$balance_type ='DR';
						}
						else{
							$amount_cur = formatAmount(abs($amount_total)) . ' CR';
							$balance_type ='CR';
							$amount = $amount_total;
							
						}


		    	$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	}
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
					'balance_type'      =>  $balance_type,
					'amount_cur'		=>	$amount_cur,
	    			'mc_name'			=>	$mc_name,
					'bill_no'           =>  $billno,
					'narration'         =>  $lng_narration,
					'amount_total'      =>  $amount_total
	    		];
		    	
			}

			return $final;
  	}

  	public function load_sale_return_register($pq_curPage, $limit, $from_date, $to_date, $view, $search,$export_excel=0)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->where('acc_grp_parent_id', 8)
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->where('acc_grp_parent_id', 8);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}
		if($accounts)
    	$accountss = implode(",",$accounts);
	    else 
			$accountss =0;
	  

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		$total_Records=0;
		if($export_excel==0){
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 2);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
			if($pq_curPage=='0') { $pq_curPage='1'; }
				$offset = ($limit * ($pq_curPage - 1));

		  if ($offset > $total_Records){        
			$pq_curPage = ceil($total_Records / $limit);
			$offset = ($limit * ($pq_curPage - 1));
		  }
		}

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 2);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			if($export_excel==0){
			$builder->limit($limit,$offset);
			}
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';

	    	$amount = 0;
	    	$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			if ($acc_txns['acc_txn_drcr'] == 'd'){
			    			$amount_total += floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}
		    			// if ($acc_txns['acc_txn_drcr'] == 'c'){
			    		// 	$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    		// 	$account_name .=  $account_info['acc_name'].', ';
		    			// }

		    		}
	    		}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';


		    	$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	}
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'  => $value['voucher_txn_id'],
	    			'voucher_type_id' => $value['voucher_type_id'],
	    			'voucher_type'	  => $voucher_type,
	    			'voucher_no'	  => $voucher_no,
	    			'voucher_date'	  => $voucher_date,
	    			'account_name'    => $account_name,
	    			'amount'		  => $amount,
	    			'mc_name'		  => $mc_name,
					'amount_total'    => $amount_total
	    		];
		    	
			}

			return [
				'totalRecords'	=>	$total_Records,
				'curPage'	=>	$pq_curPage,
				'data'	=>	$final,
			];
  	}


	  public function load_sale_return_register_print( $from_date, $to_date, $export_excel=0)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->where('acc_grp_parent_id', 8)
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->where('acc_grp_parent_id', 8);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}
		if($accounts)
    	$accountss = implode(",",$accounts);
	    else 
			$accountss =0;
	  

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		$total_Records=0;
		if($export_excel==0){
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 2);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
	
		}

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 2);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			if($export_excel==0){

			}
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';

	    	$amount = 0;
	    	$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			if ($acc_txns['acc_txn_drcr'] == 'd'){
			    			$amount_total += floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}
		    			// if ($acc_txns['acc_txn_drcr'] == 'c'){
			    		// 	$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    		// 	$account_name .=  $account_info['acc_name'].', ';
		    			// }

		    		}
	    		}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';


		    	$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	}
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'  => $value['voucher_txn_id'],
	    			'voucher_type_id' => $value['voucher_type_id'],
	    			'voucher_type'	  => $voucher_type,
	    			'voucher_no'	  => $voucher_no,
	    			'voucher_date'	  => $voucher_date,
	    			'account_name'    => $account_name,
	    			'amount'		  => $amount,
	    			'mc_name'		  => $mc_name,
					'amount_total'    => $amount_total
	    		];
		    	
			}

			return 	$final;
  	}

  	public function load_sale_return_outward_supplies_expenses_register($pq_curPage, $limit, $from_date, $to_date, $view, $search,$export_excel=0)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [7,10,11,12,13])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [7,10,11,12,13]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		$total_Records=0;
		if($export_excel==0){
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 2);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
			if($pq_curPage=='0') { $pq_curPage='1'; }
				$offset = ($limit * ($pq_curPage - 1));

		  if ($offset > $total_Records){        
			$pq_curPage = ceil($total_Records / $limit);
			$offset = ($limit * ($pq_curPage - 1));
		  }
		}
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 2);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
		
		   if($export_excel==0){
			$builder->limit($limit,$offset);
		   }
			$result = $builder->get()->getResultArray();
			
			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';

				$amount = 0;
				$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			if ($acc_txns['acc_txn_drcr'] == 'd'){
			    			$amount_total += floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}
		    			if ($acc_txns['acc_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}

		    		}
	    		}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';


		    	$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	}
		    	
	        $account_name = rtrim($account_name, ", ");     
	    	$final[] = [
	    			'voucher_txn_id'  => $value['voucher_txn_id'],
	    			'voucher_type_id' => $value['voucher_type_id'],
	    			'voucher_type'	  => $voucher_type,
	    			'voucher_no'	  => $voucher_no,
	    			'voucher_date'    => $voucher_date,
	    			'account_name'	  => $account_name,
	    			'amount'		  => $amount,
	    			'mc_name'		  => $mc_name,
					'amount_total'    => $amount_total
	    		];		    	
			}
			return [
				'totalRecords'	=>	$total_Records,
				'curPage'	=>	$pq_curPage,
				'data'	=>	$final,
			];
  	}

	  public function load_sale_return_outward_supplies_expenses_register_print($from_date, $to_date ,$export_excel=0)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [7,10,11,12,13])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [7,10,11,12,13]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		$total_Records=0;
		if($export_excel==0){
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 2);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
	
		}
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 2);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
		
		   if($export_excel==0){

		   }
			$result = $builder->get()->getResultArray();
			
			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';

				$amount = 0;
				$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			if ($acc_txns['acc_txn_drcr'] == 'd'){
			    			$amount_total += floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}
		    			if ($acc_txns['acc_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}

		    		}
	    		}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';


		    	$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	}
		    	
	        $account_name = rtrim($account_name, ", ");     
	    	$final[] = [
	    			'voucher_txn_id'  => $value['voucher_txn_id'],
	    			'voucher_type_id' => $value['voucher_type_id'],
	    			'voucher_type'	  => $voucher_type,
	    			'voucher_no'	  => $voucher_no,
	    			'voucher_date'    => $voucher_date,
	    			'account_name'	  => $account_name,
	    			'amount'		  => $amount,
	    			'mc_name'		  => $mc_name,
					'amount_total'    => $amount_total
	    		];		    	
			}
			return $final;
  	}

  	public function load_sale_return_outward_supplies_assets_register($pq_curPage, $limit, $from_date, $to_date, $view, $search,$export_excel=0)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [1,2,3,4,5])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [1,2,3,4,5]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		$total_Records=0;
		if($export_excel==0){
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 2);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
			if($pq_curPage=='0') { $pq_curPage='1'; }
				$offset = ($limit * ($pq_curPage - 1));

		  if ($offset > $total_Records){        
			$pq_curPage = ceil($total_Records / $limit);
			$offset = ($limit * ($pq_curPage - 1));
		  }
		}
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 2);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			
		if($export_excel==0){
			$builder->limit($limit,$offset);
		}
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';

				$amount = 0;
				$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			if ($acc_txns['acc_txn_drcr'] == 'd'){
			    			$amount_total += floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}
		    			if ($acc_txns['acc_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}

		    		}
	    		}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';


		    	$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	}
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
	    			'mc_name'			=>	$mc_name,
					'amount_total'      =>  $amount_total
	    		];
		    	
			}

			return [
				'totalRecords'	=>	$total_Records,
				'curPage'	=>	$pq_curPage,
				'data'	=>	$final,
			];
  	}

	  public function load_sale_return_outward_supplies_assets_register_print( $from_date, $to_date ,$export_excel=0)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [1,2,3,4,5])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [1,2,3,4,5]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		$total_Records=0;
		if($export_excel==0){
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 2);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    

		}
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND '.$comp_txn_tbl.'.`master_id_type` = "acc" )');				
			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 2);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			
		if($export_excel==0){
	
		}
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';

				$amount = 0;
				$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->where('master_id_type', 'acc');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		
					$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
					$builder = $this->db->table($acc_txn_tbl);
					$builder->where('txn_id', $value2['txn_id']);
					$builder->where('acc_id', $value2['master_id']);
					$builder->where('bo_id', $this->bo_id);
					$acc_txns = $builder->get()->getRowArray();
			    	
		    	if($acc_txns){
		    	 	$account_info = $this->account_info($acc_txns['acc_id']);

		    		if (in_array($value2['master_id'], $accounts)){
		   
		    			if ($acc_txns['acc_txn_drcr'] == 'd'){
			    			$amount_total += floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}
		    			if ($acc_txns['acc_txn_drcr'] == 'c'){
			    			$amount_total += -floatval($acc_txns['acc_txn_amount']);
			    			$account_name .=  $account_info['acc_name'].', ';
		    			}

		    		}
	    		}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';


		    	$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	}
		    	
	        $account_name = rtrim($account_name, ", ");     


	    		$final[] = [
	    			'voucher_txn_id'	=>	$value['voucher_txn_id'],
	    			'voucher_type_id'	=>	$value['voucher_type_id'],
	    			'voucher_type'		=>	$voucher_type,
	    			'voucher_no'		=>	$voucher_no,
	    			'voucher_date'		=>	$voucher_date,
	    			'account_name'		=>	$account_name,
	    			'amount'			=>	$amount,
	    			'mc_name'			=>	$mc_name,
					'amount_total'      =>  $amount_total
	    		];
		    	
			}

			return 	$final;
  	}

  	public function load_sale_return_condensed_register($pq_curPage, $limit, $from_date, $to_date, $view, $search,$export_excel=0)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [1,2,3,4,5,7,8,10,11,12,13])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [1,2,3,4,5,7,8,10,11,12,13]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		$total_Records=0;
	    if($export_excel==0){		
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');

			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 2);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
			if($pq_curPage=='0') { $pq_curPage='1'; }
				$offset = ($limit * ($pq_curPage - 1));

		  if ($offset > $total_Records){        
			$pq_curPage = ceil($total_Records / $limit);
			$offset = ($limit * ($pq_curPage - 1));
		  }
		}

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');

			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 2);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			if($export_excel==0){	
			$builder->limit($limit,$offset);
			}
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';

				$amount = 0;
				$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->whereIn('master_id_type', ['acc','aco']);
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		
					if($value2['master_id_type'] == 'acc')
		    	{
		    		$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_txn_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);

			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			if ($acc_txns['acc_txn_drcr'] == 'd'){
				    			$amount_total += floatval($acc_txns['acc_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    			if ($acc_txns['acc_txn_drcr'] == 'c'){
				    			// $amount_total += -floatval($acc_txns['acc_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}

			    		}
		    		}
		    	}

		    	if($value2['master_id_type'] == 'aco')
		    	{
		    		$acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_oth_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);

			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'd'){
				    			// $amount_total += floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'c'){
				    			$amount_total += -floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}

			    		}
		    		}
		    	}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';


		    	$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	}
		    	
	           $account_name = rtrim($account_name, ", ");     

	    		$final[] = [
	    			'voucher_txn_id'  => $value['voucher_txn_id'],
	    			'voucher_type_id' => $value['voucher_type_id'],
	    			'voucher_type'	  => $voucher_type,
	    			'voucher_no'	  => $voucher_no,
	    			'voucher_date'	  => $voucher_date,
	    			'account_name'	  => $account_name,
	    			'amount'		  => $amount,
	    			'mc_name'		  => $mc_name,
					'amount_total'    => $amount_total
	    		];		    	
			}

			return [
				'totalRecords'	=>	$total_Records,
				'curPage'	=>	$pq_curPage,
				'data'	=>	$final,
			];
  	}

	  public function load_sale_return_condensed_register_print( $from_date, $to_date, $export_excel=0)
  	{
  		$acctgroupn_tbl = $this->company_id.'_acctgroupn_'.$this->session->get('ses_comp_fy_id');
    	$result = $this->db->table($acctgroupn_tbl)
	    					->select('acc_grp_id')
	    					->whereIn('acc_grp_parent_id', [1,2,3,4,5,7,8,10,11,12,13])
	    					->get()->getResultArray();
    	$groups = [];
    	foreach ($result as $key => $value) {
    		$groups[] = $value['acc_grp_id'];
    	}

    	$accounts = [];
			$acctmaster_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
			$builder = $this->db->table($acctmaster_tbl);
			$builder->select('acc_id');
			$builder->whereIn('acc_grp_parent_id', [1,2,3,4,5,7,8,10,11,12,13]);
			if($groups)
				$builder->orWhereIn('acc_grp_id', $groups);
			$result = $builder->get()->getResultArray();

	    foreach ($result as $key => $value) {
    		$accounts[] = $value['acc_id'];
    	}

    	if(!$accounts){
    		return [
					'totalRecords'	=>	0,
					'curPage'	=>	1,
					'data'	=>	[],
				];
    	}

    	$accountss = implode(",",$accounts);

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
		$total_Records=0;
	    if($export_excel==0){		
			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');

			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 2);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			$total_Records = $builder->countAllResults();
	    
	
		}

			$builder = $this->db->table($voucher_tbl);
			$builder->select($voucher_tbl.'.*');
			$builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
			$builder->select($voucher_type_tbl.'.comp_vch_type');
			$builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
			$builder->select($voucher_series_tbl.'.comp_vch_series');
			$builder->where($voucher_tbl.'.comp_id', $this->company_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);

			$builder->where('EXISTS (SELECT NULL FROM '.$comp_txn_tbl.' where '.$comp_txn_tbl.'.`voucher_txn_id` = '.$voucher_tbl.'.`voucher_txn_id` AND '.$comp_txn_tbl.'.`master_id` IN ('.$accountss.') AND ('.$comp_txn_tbl.'.`master_id_type` = "acc" OR '.$comp_txn_tbl.'.`master_id_type` = "aco") )');

			if($from_date!='')
				$builder->where('voucher_date >=', $from_date);
			if($to_date!='')
				$builder->where('voucher_date <=', $to_date);

			$builder->where('voucher_tag !=', 'OPTIONL');
			$builder->where($voucher_tbl.'.voucher_type_id', 2);
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
			if($export_excel==0){	
			$builder->limit($limit,$offset);
			}
			$result = $builder->get()->getResultArray();

			$final = [];

			foreach ($result as $key => $value) {

				$voucher_type_id = $value['voucher_type_id'];
				$voucher_type = $value['comp_vch_type'];
				$voucher_no = $value['comp_vch_no'];
				$voucher_date = date("d-m-Y", strtotime($value['voucher_date']));
				$account_name = '';

				$amount = 0;
				$amount_total = 0;
				
				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('master_id, master_id_type, txn_id');
				$builder->whereIn('master_id_type', ['acc','aco']);
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$result2 = $builder->get()->getResultArray();

		    foreach ($result2 as $key2 => $value2) {
		    		
					if($value2['master_id_type'] == 'acc')
		    	{
		    		$acc_txn_tbl = $this->company_id.'_accnttxnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_txn_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);

			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			if ($acc_txns['acc_txn_drcr'] == 'd'){
				    			$amount_total += floatval($acc_txns['acc_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    			if ($acc_txns['acc_txn_drcr'] == 'c'){
				    			// $amount_total += -floatval($acc_txns['acc_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}

			    		}
		    		}
		    	}

		    	if($value2['master_id_type'] == 'aco')
		    	{
		    		$acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
						$builder = $this->db->table($acc_oth_tbl);
						$builder->where('txn_id', $value2['txn_id']);
						$builder->where('acc_id', $value2['master_id']);
						$builder->where('bo_id', $this->bo_id);
						$acc_txns = $builder->get()->getRowArray();
				    	
			    	if($acc_txns){
			    	 	$account_info = $this->account_info($acc_txns['acc_id']);

			    		if (in_array($value2['master_id'], $accounts)){
			   
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'd'){
				    			// $amount_total += floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}
			    			if ($acc_txns['acc_oth_txn_drcr'] == 'c'){
				    			$amount_total += -floatval($acc_txns['acc_oth_txn_amount']);
				    			$account_name .=  $account_info['acc_name'].', ';
			    			}

			    		}
		    		}
		    	}
	    	}
		    	
	    	if($amount_total >= 0)
	    		$amount = formatAmount($amount_total) . ' DR';
	    	else
	    		$amount = formatAmount(abs($amount_total)) . ' CR';


		    	$mc_name = '';
		    	if($value['mat_cent_id']){
		    		$material_centre_info = $this->material_centre_info($this->company_id,$value['mat_cent_id']);
	        		if($material_centre_info)
	        	    $mc_name = $material_centre_info['mat_cent_name'];
		    	}
		    	
	           $account_name = rtrim($account_name, ", ");     

	    		$final[] = [
	    			'voucher_txn_id'  => $value['voucher_txn_id'],
	    			'voucher_type_id' => $value['voucher_type_id'],
	    			'voucher_type'	  => $voucher_type,
	    			'voucher_no'	  => $voucher_no,
	    			'voucher_date'	  => $voucher_date,
	    			'account_name'	  => $account_name,
	    			'amount'		  => $amount,
	    			'mc_name'		  => $mc_name,
					'amount_total'    => $amount_total
	    		];		    	
			}

			return 	$final;
  	}


  public function account_amount($voucher_txn_id,$voucher_series_id,$comp_id,$account_id,$txn_id){
    $ses_comp_fy_id  = $this->session->get('ses_comp_fy_id');
    $acnttxnmst_tbl  = $comp_id.'_accnttxnnn_'.$account_id.'_'.$ses_comp_fy_id;  
    $builder           = $this->db->table($acnttxnmst_tbl); 
    $builder->orderBy('acc_txn_date');                
    $builder->where('comp_id', $comp_id);	 
	$builder->where('voucher_txn_id',$voucher_txn_id);    
if($this->session->get('ses_boid')!='')
	$builder->where('bo_id', $this->session->get('ses_boid'));	
    $builder->where('txn_id',$txn_id);  
    $result  = $builder->get()->getRowArray();
    if($result)
    return $result['acc_txn_amount'];
    else
    return "0";
  }
  
 public  function get_invoice_account_info($voucher_txn_id,$comp_vch_series_id){
      $act_master_tbl =  $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');	 
      $result =  $this->db->table($act_master_tbl)->where('comp_vch_series_id',$comp_vch_series_id)
                      ->where('voucher_txn_id',$voucher_txn_id)->where('master_id_type','acc')
                      ->where('comp_id', $this->company_id)->orderBy('txn_id','ASC')->get()->getRowArray();  
      if($result){
          $account_id = $result['master_id'];
          $account_info =  $this->get_account_info($account_id); 
          return  $account_info['acc_name'];
          
      }
      else
       return "";
      
  }
  
  function company_sales_accounts(){
     $act_master_tbl =  $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');	 
	 $result =  $this->db->table($act_master_tbl)->where('acc_grp_id','9')->where('comp_id', $this->company_id)->orderBy('acc_name','ASC')->get()->getResultArray();  
    
    $all_sales_acnt = array();
    if($result){
        foreach($result as $row){
          $all_sales_acnt[$row['acc_id']]=  $row['acc_id']; 
            
        }
        
    }
   return $all_sales_acnt;
  }
  
    function company_purchase_accounts(){
     $act_master_tbl =  $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');	 
	 $result =  $this->db->table($act_master_tbl)->where('acc_grp_id','8')->where('comp_id', $this->company_id)->orderBy('acc_name','ASC')->get()->getResultArray();  
    
    $all_sales_acnt = array();
    if($result){
        foreach($result as $row){
          $all_sales_acnt[$row['acc_id']]=  $row['acc_id']; 
            
        }
        
    }
   return $all_sales_acnt;
  }
  
  

  function item_unit_info($comp_id,$unit_id){
       $item_unit_master_tbl =  $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');	 
	   return  $this->db->table($item_unit_master_tbl)->where('unit_id', $unit_id)->where('comp_id', $comp_id)->orderBy('item_unit','ASC')->get()->getRowArray();  
         
     }
  
     
  public function get_item_info($item_id){
       $comp_id = $this->company_id;
       $item_master_tbl = $comp_id.'_itemmaster_'.$this->session->get('ses_comp_fy_id');
     return $this->db->table($item_master_tbl)->where('item_id', $item_id)->where('comp_id', $comp_id)->get()->getRowArray();  
   }    
   
 function all_items_transactions($comp_id,$item_id,$item_type){
	            $ses_comp_fy_id     = $this->session->get('ses_comp_fy_id');
	            $list               = array();
			    $voucher_txn_table  = $comp_id.'_itemtxnnnn_'.$item_id.'_'.$ses_comp_fy_id;	
			    
			    $item_info = $this->get_item_info($item_id);
                if($item_info)
                  $item_name = $item_info['item_name'];
                else
                  $item_name = "";
                
               
               $item_unit_info  = $this->item_unit_info($comp_id,$item_info['item_unit']); 
             if($item_unit_info)
                 $item_unit_name = $item_unit_info['item_unit'];
              else
                $item_unit_name ='' ;
              
				$builder = $this->db->table($voucher_txn_table); 
				$builder->orderBy('item_txn_id');     
				$builder->where('comp_id', $comp_id);
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
				$builder->where('item_txn_drcr', $item_type);               
				$result = $builder->get()->getResultArray();
			
			    if($result){			
				   foreach($result as $values){	
                      $account_id    = $values['item_id'];	
                      $acc_txn_drcr  = $values['item_txn_drcr'];
                      $acc_txn_narr  = $values['item_txn_narr'];					  
					  $item_txn_date = $values['item_txn_date'];
					  $item_qty      = $values['item_txn_qty'];
					  $matrcentrid   = $values['mat_cent_id'];
					  $txn_id        = $values['txn_id'];
					  $comp_vch_series_no = $values['comp_vch_series_no'];
					   
					  $item_price   = $values['item_txn_amount'];
					  if($values['item_txn_drcr']=='d'){
						 $debit  = $values['item_txn_amount'];
						 $credit = '0.00';
					   }
					   else if($values['item_txn_drcr']=='c'){
						 $credit = $values['item_txn_amount'];
						 $debit  = '0.00';
						 }
						$list= array("mat_cent_id"=>$matrcentrid,"item_unit"=>$item_unit_name,"item_price"=>$item_price,"item_qty"=>$item_qty,"item_name"=>$item_name,"item_txn_narr"=>$acc_txn_narr,"comp_id"=>$comp_id,"txn_id"=>$txn_id,"txn_date" =>$item_txn_date,'item_id'=>$item_id,'item_txn_drcr'=>$acc_txn_drcr,'debit'=>$debit,'credit'=>$credit);
				       } 
				    }
	   return $list;  
   }
   
   function all_transactions($comp_id,$txn_id){
	   $ses_comp_fy_id    = $this->session->get('ses_comp_fy_id');
	   $list   = array();
	   $company_all_accounts = $this->get_company_all_accounts($comp_id);
	   if($company_all_accounts){
		   foreach($company_all_accounts as $company_row){
			    $account_id         =  $company_row['acc_id'];
				$show_account_name  =  $company_row['acc_name'];
			    $voucher_txn_table  =  $comp_id.'_accnttxnnn_'.$account_id.'_'.$ses_comp_fy_id;	
			   
				$builder = $this->db->table($voucher_txn_table); 
				$builder->orderBy('txn_id');         
				$builder->where('txn_id', $txn_id);		
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
				$builder->where('comp_id', $comp_id);
				$builder->where('acc_id', $company_row['acc_id']);                
				$result = $builder->get()->getResultArray();
			    if($result){			
				   foreach($result as $values){	
                      $account_id   = $values['acc_id'];	
                      $acc_txn_drcr = $values['acc_txn_drcr'];
                      $acc_txn_narr = $values['acc_txn_narr'];					  
					  $posted_on    = $values['posted_on'];
					  if($values['acc_txn_drcr']=='d'){
						 $debit  = $values['acc_txn_amount'];
						 $credit = '0.00';
					   }
					   else if($values['acc_txn_drcr']=='c'){
						 $credit = $values['acc_txn_amount'];
						 $debit  = '0.00';
						 }
						$list = array("acc_txn_narr"=>$acc_txn_narr,"comp_id"=>$comp_id,"txn_id"=>$txn_id,"txn_date" =>$values['acc_txn_date'],'account_id'=>$account_id,'acc_txn_drcr'=>$acc_txn_drcr,'account_name'=>$show_account_name,'debit'=>$debit,'credit'=>$credit,'posted_on'=>$posted_on);
				       } 
				    }
				 }
	          }
	   return $list;  
   }
   
  public function financial_vouchers_list($voucher_type_id,$comp_id,$from_date,$to_date){ 	   
	    $ses_comp_fy_id  = $this->session->get('ses_comp_fy_id');
	    $base_url        = base_url().'/'.getenv('AdminPath');
	    
	    $vch_txn_conso_tbl = $comp_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	    $builder           = $this->db->table($vch_txn_conso_tbl); 
        $builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);	
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
		$builder->where('voucher_type_id', $voucher_type_id);
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
		$result = $builder->get()->getResultArray();
       $records= array();
        foreach($result as $values){
		   $voucher_first_trans = $this->voucher_first_transaction($comp_id,$values['voucher_txn_id']);	
		  
		   if($voucher_first_trans){
			   foreach($voucher_first_trans as $trsnkey => $transvalues){				    	
				   $records[] = array(  
                          'voucher_txn_id'=> $values['voucher_txn_id'],                   
						  'txn_date'=>date('d-M-Y',strtotime($transvalues['txn_date']))	,
						  'account_name'=>$transvalues['account_name'],		
						  'debit'=>$transvalues['debit'],	
						  'credit'=>$transvalues['credit'],
						  'bill_no'=> $transvalues['bill_no'],
						  'narration'=> '',//$transvalues['narration']
				          );  
			        }
		        }           
		     }	 		
      return $records;
   }

   	function account_info($account_id){	 
       $account_master_tbl = $this->company_id.'_acctmaster_'.$this->session->get('ses_comp_fy_id');
       return $this->db->table($account_master_tbl)->where('acc_id', $account_id)->get()->getRowArray();   	   
    }

    function bill_sundry_info($id)
    {
    	$bill_sundry_tbl = $this->company_id.'_billsundry_'.$this->session->get('ses_comp_fy_id');
		if($this->session->get('ses_boid')!='')		
		$result = $this->db->table($bill_sundry_tbl)->where('bo_id', $this->session->get('ses_boid'))->where('bill_sundry_id', $id)->get()->getRowArray();
		else 
	    $result = $this->db->table($bill_sundry_tbl)->where('bill_sundry_id', $id)->get()->getRowArray();
	    return $result; 
    }
    function get_voucher_narration_info($voucher_txn_id,$narr_type,$txn_id){
		if($narr_type=='long'){
			$long_narr_tbl = $this->company_id.'_long_narrn_'.$this->session->get('ses_comp_fy_id');
			return $this->db->table($long_narr_tbl)->where('vch_txn_id',$voucher_txn_id)->get()->getRowArray();
		}
		else if($narr_type=='short'){
	        $short_narr_tbl = $this->company_id.'_short_narr_'.$this->session->get('ses_comp_fy_id');
			return $this->db->table($short_narr_tbl)->where('txn_id',$txn_id)->where('vch_txn_id',$voucher_txn_id)->get()->getRowArray();
		}		
  }

public function load_voucher_register($from_date, $to_date, $voucher_type_id, $view, $type, $consoview = 0){

    $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
    $pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10;
    if ($pq_curPage < 1) $pq_curPage = 1;
    if ($pq_rPP < 1) $pq_rPP = 10;

    $builder = $this->db->table("acctvchreg v");

    /*
    |--------------------------------------------------------------------------
    | CONDENSED MODE
    |--------------------------------------------------------------------------
    */
    if($view == 0){

        $builder->select("
            MAX(v.vch_date) AS vch_date,
            v.vch_txn_id,
            v.acct_vch_type,
            MAX(vtmst.vch_name) AS vch_type_name,
            STRING_AGG(DISTINCT a.acc_name, ', ' ORDER BY a.acc_name) AS account_names,
            SUM(v.acc_txn_dr_amt) AS dr_amount,
            SUM(v.acc_txn_cr_amt) AS cr_amount,
            CASE 
                WHEN SUM(v.acc_txn_dr_amt) <> 0 THEN SUM(v.acc_txn_dr_amt)
                ELSE SUM(v.acc_txn_cr_amt)
            END AS amount,
            MAX(v.vch_narr) AS narration,
            MAX(v.hobo_id) AS hobo_id,
            (SELECT g2.outsup_bill_ref_no
             FROM gstroutsup g2
             WHERE g2.vch_txn_id = v.vch_txn_id
             ORDER BY g2.outsup_bill_ref_no DESC
             LIMIT 1) AS bill_ref_no
        ", false);

        $builder->join("acctmaster a", 'a.acc_id = v.acc_id', 'left', false);
        $builder->join("vchtypemst vtmst", 'vtmst.vch_type_id = v.acct_vch_type', 'left', false);

        // ❌ REMOVED hobomaster join

        $builder->where('v.cmp_id', $this->company_id);

        if ($type == 2)       $builder->where('v.acc_txn_type', 2);
        elseif ($type == 3)   $builder->where('v.acc_txn_type', 3);
        else                  $builder->where('v.acc_txn_type', 1);

        $builder->where('v.txn_id IS NULL', null, false);

        if ($consoview == 0) {
            $builder->where('v.hobo_id', $this->bo_id);
        }

        if ($type != 2) $builder->where('v.acct_vch_type', $voucher_type_id);
        if (!empty($from_date)) $builder->where('v.vch_date >=', $from_date);
        if (!empty($to_date))   $builder->where('v.vch_date <=', $to_date);

        $builder->groupBy('v.vch_txn_id, v.acct_vch_type');

        $countBuilder  = clone $builder;
        $countSql      = $countBuilder->getCompiledSelect(false);
        $countQuery    = $this->db->query("SELECT COUNT(*) AS total FROM ({$countSql}) AS count_sub");
        $total_records = (int)($countQuery->getRowArray()['total'] ?? 0);

        $builder->orderBy('MAX(v.vch_date)', 'ASC', false);
        $builder->orderBy('v.vch_txn_id', 'ASC');
    }

    /*
    |--------------------------------------------------------------------------
    | DETAILED MODE
    |--------------------------------------------------------------------------
    */
    if($view == 1){

        $builder->select("
            v.vch_date,
            v.vch_txn_id,
            v.txn_id,
            v.acct_vch_type,
            vtmst.vch_name AS vch_type_name,
            a.acc_name AS account_names,
            v.acc_txn_dr_amt,
            v.acc_txn_cr_amt,
            CASE
                WHEN v.acc_txn_dr_amt <> 0 THEN v.acc_txn_dr_amt
                ELSE v.acc_txn_cr_amt
            END AS amount,
            v.vch_narr AS narration,
            v.acc_txn_dr_amt AS dr_amount,
            v.acc_txn_cr_amt AS cr_amount,
            v.hobo_id,
            (SELECT g2.outsup_bill_ref_no
             FROM gstroutsup g2
             WHERE g2.vch_txn_id = v.vch_txn_id
             ORDER BY g2.outsup_bill_ref_no DESC
             LIMIT 1) AS bill_ref_no
        ", false);

        $builder->join("acctmaster a", 'a.acc_id = v.acc_id', 'left', false);
        $builder->join("vchtypemst vtmst", 'vtmst.vch_type_id = v.acct_vch_type', 'left', false);

        // ❌ REMOVED hobomaster join

        $builder->where('v.cmp_id', $this->company_id);
        $builder->where('v.txn_id IS NOT NULL', null, false);

        if ($type == 2)       $builder->where('v.acc_txn_type', 2);
        elseif ($type == 3)   $builder->where('v.acc_txn_type', 3);
        else                  $builder->where('v.acc_txn_type', 1);

        if ($consoview == 0) {
            $builder->where('v.hobo_id', $this->bo_id);
        }

        if ($type != 2) $builder->where('v.acct_vch_type', $voucher_type_id);
        if (!empty($from_date)) $builder->where('v.vch_date >=', $from_date);
        if (!empty($to_date))   $builder->where('v.vch_date <=', $to_date);

        $countBuilder  = clone $builder;
        $total_records = $countBuilder->countAllResults(false);

        $builder->orderBy('v.vch_date', 'ASC');
        $builder->orderBy('v.vch_txn_id', 'ASC');
        $builder->orderBy('v.txn_id', 'ASC');
        $builder->orderBy('v.acc_id', 'ASC');
    }

    /*
    |--------------------------------------------------------------------------
    | PAGINATION
    |--------------------------------------------------------------------------
    */
    $offset = max(0, $pq_rPP * ($pq_curPage - 1));
    $builder->limit($pq_rPP, $offset);
    $result = $builder->get()->getResultArray();

//echo $this->db->getlastquery();
    /*
    |--------------------------------------------------------------------------
    | 🔥 FETCH HOBOMASTER FROM EXTERNAL DB
    |--------------------------------------------------------------------------
    */
    $hobo_ids = array_column($result, 'hobo_id');
    $hobo_ids = array_unique(array_filter($hobo_ids));

    $hobo_map = [];

    if (!empty($hobo_ids)) {
        $univaictly = $this->externaldb->univaictly_db();

        $hobo_rows = $univaictly->table('hobomaster')
            ->select('hobo_id, hobo_name')
            ->whereIn('hobo_id', $hobo_ids)
            ->get()
            ->getResultArray();

        foreach ($hobo_rows as $row) {
            $hobo_map[$row['hobo_id']] = $row['hobo_name'];
        }
    }

    /*
    |--------------------------------------------------------------------------
    | RESPONSE
    |--------------------------------------------------------------------------
    */
    $records = [];
    $last_vch_no = 0;

    foreach ($result as $key => $value) {

        $voucher_date = date("d-m-Y", strtotime($value['vch_date']));
        $branch_name = $hobo_map[$value['hobo_id']] ?? '';

        if ($view == 1) {
            $vch_no     = $value['vch_txn_id'];
            $voucher_no = ($vch_no != $last_vch_no) ? $vch_no : '';
            $vch_date   = ($vch_no != $last_vch_no) ? $voucher_date : '';
        } else {
            $voucher_no = $value['vch_txn_id'];
            $vch_date   = $voucher_date;
        }

        $records[] = [
            'voucher_txn_id'  => $value['vch_txn_id'],
            'voucher_type_id' => $value['acct_vch_type'],
            'voucher_type'    => $value['vch_type_name'],
            'account_name'    => $value['account_names'] . '(' . $value['vch_txn_id'] . ')',
            'voucher_date'    => $vch_date,
            'amount'          => formatAmount($value['amount']),
            'amount_total'    => parseAmount($value['amount']),
            'bill_ref_no'     => $value['bill_ref_no'] ?? '',
            'narration'       => $value['narration'],
            'branch_name'     => $branch_name,
        ];

        if ($view == 1) {
            $next_vch_no = $result[$key + 1]['vch_txn_id'] ?? null;
            if ($value['vch_txn_id'] !== $next_vch_no) {
                $records[] = [];
            }
            $last_vch_no = $value['vch_txn_id'];
        }
    }

    echo json_encode([
        "totalRecords" => $total_records,
        "curPage"      => $pq_curPage,
        "data"         => $records,
    ]);
}


 
	public function load_draft_vouchers($from_date, $to_date, $voucher_type_id,$view)
     {
	    $postgr_db  = $this->externaldb->postgr_db();		
	    $pq_curPage = isset($_POST["pq_curpage"]) ? (int)$_POST["pq_curpage"] : 1;
		$pq_rPP     = isset($_POST["pq_rpp"]) ? (int)$_POST["pq_rpp"] : 10; // default 10 rows per page

		if ($pq_curPage < 1) $pq_curPage = 1;
		if ($pq_rPP < 1) $pq_rPP = 10;
		
		$voucher_tbl    = 'drftvchrec';
		$builder = $postgr_db->table("$voucher_tbl v");		
			$builder->select(			     
				'v.log_date_time, 
				 v.draft_vch_rec_id,
				 v.vch_type_id,
				 v.vch_particulars AS account_names, 
				 v.vch_amt AS amount',
				false
			);
			$builder->where('v.uuid_aictly', $this->session->get('uuid'));
			$builder->where('v.cmp_id', $this->company_id);		
			$builder->where('v.hobo_id', $this->bo_id);
			$builder->where('v.vch_type_id', $voucher_type_id);
			if (!empty($from_date)) {
				$builder->where('v.log_date_time >=', $from_date);
			}
			if (!empty($to_date)) {
				$builder->where('v.log_date_time <=', $to_date);
			}		
		// Clone for total count before applying limit
		$countBuilder = clone $builder;
		$total_records = $countBuilder->countAllResults(false);

		// Pagination logic
		if ($pq_curPage == 0) $pq_curPage = 1;
		$offset = ($pq_rPP * ($pq_curPage - 1));

		if ($offset > $total_records) {
			$pq_curPage = ceil($total_records / $pq_rPP);
			$offset = ($pq_rPP * ($pq_curPage - 1));
		}
		if ($offset < 0) {
			$offset = 0;
		}

		// Apply order and limit
		$builder->orderBy('v.log_date_time', 'ASC');
		$builder->limit($pq_rPP, $offset);		
		// Fetch final data
		$result = $builder->get()->getResultArray();
		$records = [];
		$last_vch_no = 0;
		foreach($result as $key => $value)
          {
			$vch_date   = date("d-m-Y", strtotime($value['log_date_time']));
			
			 if($value['amount'] >0)
			      $dr_cr='DR';
			  else
				   $dr_cr='CR';

			$records[] = [
			  'voucher_txn_id'  => $value['draft_vch_rec_id'],
			  'voucher_type_id' => $value['vch_type_id'],
			  'account_name'    => $value['account_names'],
			  'voucher_date'    => $vch_date,
			  'amount'          => formatAmount($value['amount']),
			  'amount_total'    => parseAmount($value['amount'])			  
			  ];			  
		  }		
		echo  "{\"totalRecords\":" .$total_records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($records)."}";        
   	 }

	

   	public function load_memorandum_register($pq_curPage, $limit, $from_date, $to_date, $voucher_type_id)
    {
        $voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	    $voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	    $voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
	
		$account_memo_tbl = $this->company_id.'_memotxnnnn_'.$this->session->get('ses_comp_fy_id');
	    
	    $builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	    $builder->join($account_memo_tbl, $account_memo_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id');
	    $builder->select($account_memo_tbl.'.acc_type, '.$account_memo_tbl.'.acc_id, '. $account_memo_tbl.'.acc_txn_drcr, '. $account_memo_tbl.'.acc_bal');
	    
		$builder->where($voucher_tbl.'.comp_id', $this->company_id);
		if($this->session->get('ses_boid')!='')
		$builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));

		$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);					
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}
		$builder->orderBy('voucher_txn_id');
		$total_records = $builder->countAllResults();
		
		if($pq_curPage=='0') $pq_curPage='1';
		$offset = ($limit * ($pq_curPage - 1));
        if ($offset > $total_records){        
            $pq_curPage = ceil($total_records / $limit);
            $offset = ($limit * ($pq_curPage - 1));
        }            

		$builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	    $builder->join($account_memo_tbl, $account_memo_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id');
	    $builder->select($account_memo_tbl.'.acc_type, '.$account_memo_tbl.'.acc_id, '. $account_memo_tbl.'.acc_txn_drcr, '. $account_memo_tbl.'.acc_bal, '. $account_memo_tbl.'.acc_txn_amount, '. $account_memo_tbl.'.acc_txn_id');
	    
		$builder->where($voucher_tbl.'.comp_id', $this->company_id);
		$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);				
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}
		$builder->orderBy('voucher_date');
		$builder->orderBy('voucher_txn_id');
		$builder->limit($limit,$offset);  
		$result = $builder->get()->getResultArray();

		$data = [];
		foreach($result as $key => $value)
        {
        	$voucher_txn_id  = $value['voucher_txn_id'];
        	$comp_vch_no = $value['comp_vch_no'];
        	$account_name = '';
        	$narration = '';
        	$credit = '';
        	$debit = '';

            if($value['acc_type'] == 'acc'){
    			$account_info   = $this->account_info($value['acc_id']);
				$account_name   = $account_info['acc_name'];
         	}
         	if($value['acc_type'] == 'bsd'){
         		$bill_sundry_info   = $this->bill_sundry_info($value['master_id']);
				$account_name 		= $bill_sundry_info['bill_sundry_name'];
         	}

         	if($value['acc_txn_drcr'] == 'c')
            	$credit 		= formatAmount($value['acc_txn_amount']);
         	
         	if($value['acc_txn_drcr'] == 'd')
            	$debit 		  	= formatAmount($value['acc_txn_amount']);

         	$voucher_narration = $this->get_voucher_narration_info($voucher_txn_id,'short',$value['acc_txn_id']);
         	if($voucher_narration){
         		$narration = $voucher_narration['vch_short_narr'];
         	}

        	$voucher_date         = date("d-m-Y", strtotime($value['voucher_date']));
        	$data[] = [

                'voucher_txn_id'  => $voucher_txn_id,
                'voucher_type_id' => $voucher_type_id,
                'voucher_no'      => $comp_vch_no,
                'account_name'    => $account_name,
                'voucher_date'    => $voucher_date,
                'credit'          => $credit,
                'debit'           => $debit,
                'narration'		  => $narration
            ];	
        }
		// echo "<pre>";print_r($data);exit;
		return  [
			'totalRecords'	=> $total_records,
			'curPage'		=> $pq_curPage,
			'data'			=> $data,
		];	
	}
	
	function GetPackingLevel($list_id){
	  $cupackingn_tbl  = $this->company_id.'_cupackingn_'.$this->session->get('ses_comp_fy_id'); 	
	  $builder         = $this->db->table($cupackingn_tbl); 
	  $builder->where('list_id',$list_id);
	  $builder->orderBy('packing_id','DESC');
	  $builder->limit(1);
	  return $builder->get()->getRowArray(); 
  } 

	public function load_consignment_packing_register($pq_curPage, $limit, $from_date, $to_date)
    {
        $voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	    $voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	    $voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
	
		$pcklistmst_tbl = $this->company_id.'_pcklistmst_'.$this->session->get('ses_comp_fy_id');
		$cupackingn_tbl = $this->company_id.'_cupackingn_'.$this->session->get('ses_comp_fy_id');
		
		$pcklistqty_tbl = $this->company_id.'_pcklistqty_'.$this->session->get('ses_comp_fy_id');
		$listpacked_tbl = $this->company_id.'_listpacked_'.$this->session->get('ses_comp_fy_id');
	    
	    $builder = $this->db->table($pcklistmst_tbl);	    
		$builder->join($voucher_tbl,$voucher_tbl.'.voucher_tag='.$pcklistmst_tbl.'.list_id');
		$builder->where($pcklistmst_tbl.'.comp_id', $this->company_id);					
		if($from_date!=''){
			$builder->where($voucher_tbl.'.voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where($voucher_tbl.'.voucher_date <=', $to_date);
		}
		if($this->session->get('ses_boid')!='')
		$builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));
		$builder->orderBy($pcklistmst_tbl.'.list_id');
		$total_records = $builder->countAllResults();
		
		if($pq_curPage=='0') $pq_curPage='1';
		$offset = ($limit * ($pq_curPage - 1));
        if ($offset > $total_records){        
            $pq_curPage = ceil($total_records / $limit);
            $offset = ($limit * ($pq_curPage - 1));
        }            

		$builder = $this->db->table($pcklistmst_tbl);	    
		$builder->join($voucher_tbl,$voucher_tbl.'.voucher_tag='.$pcklistmst_tbl.'.list_id');
		$builder->where($pcklistmst_tbl.'.comp_id', $this->company_id);					
		if($from_date!=''){
			$builder->where($voucher_tbl.'.voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where($voucher_tbl.'.voucher_date <=', $to_date);
		}
		$builder->orderBy($pcklistmst_tbl.'.list_id');
		$builder->limit($limit,$offset);  
		$result = $builder->get()->getResultArray();

		$data = [];
		foreach($result as $key => $value)
        {
			
        	$builder = $this->db->table($cupackingn_tbl);
		    $builder->select('COUNT(cu_id) as total_cu,MAX(list_level_id) as max_level');
			$builder->where('list_id', $value['list_id']);
			$cupackingn    = $builder->get()->getRowArray();
			$total_cu      = $cupackingn['total_cu'] ?? 0;
			$max_level     = $cupackingn['max_level'] ?? 0;
        	$voucher_date  = date("d-m-Y", strtotime($value['voucher_date']));

			// count qty packed or unpacked
			$builder1 = $this->db->table($pcklistqty_tbl);
		    $builder1->select('SUM(item_qty_available) as total_unpacked');
			$builder1->where('list_id', $value['list_id']);
			$pcklistqty_row = $builder1->get()->getRowArray();
			$total_unpacked = $pcklistqty_row['total_unpacked'] ?? 0;
			
			$builder2 = $this->db->table($listpacked_tbl);
		    $builder2->select('SUM(item_qty_packed) as total_packed');
			$builder2->where('list_id', $value['list_id']);
			$listpacked_row = $builder2->get()->getRowArray();
			$total_packed   = $listpacked_row['total_packed'] ?? 0;
			
            // get packing max level 	
			$packing_level = $this->GetPackingLevel($value['list_id']); 			
            $list_level_id = $packing_level['list_level_id'];
        	$data[] = [
                'list_id'  		  => $value['list_id'],
                'list_name' 	  => $value['list_name'],
                'voucher_no'      => $value['comp_vch_no'],
                'no_of_level'     => $max_level,
                'voucher_date'    => $voucher_date,
                'total_cu'        => $total_cu,
                'total_packed'    => $total_packed,
                'total_unpacked'  => $total_unpacked,
				'list_level_id'   => $list_level_id,
                'status'  		  => '',
               ];	
        }
		// echo "<pre>";print_r($data);exit;
		return  [
			'totalRecords'	=> $total_records,
			'curPage'		=> $pq_curPage,
			'data'			=> $data,
		];	
	}

	public function load_optional_register($pq_curPage, $limit, $from_date, $to_date, $voucher_type_id)
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
	    $builder->join($comp_txn_tbl, $comp_txn_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id  AND (master_id_type = "acc" or master_id_type = "bsd")', 'left');
	    $builder->select($comp_txn_tbl.'.txn_id, '.$comp_txn_tbl.'.master_id, '. $comp_txn_tbl.'.master_id_type');
	    
		$builder->where($voucher_tbl.'.comp_id', $this->company_id);
		if($this->session->get('ses_boid')!='')
		$builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));

		if($voucher_type_id != 0)
			$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);	

		$builder->where($voucher_tbl.'.voucher_tag', 'OPTIONL');				
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}

		$builder->orderBy('voucher_txn_id');
		$total_records = $builder->countAllResults();
		
		if($pq_curPage=='0') $pq_curPage='1';
		$offset = ($limit * ($pq_curPage - 1));

            if ($offset > $total_records)
            {        
                $pq_curPage = ceil($total_records / $limit);
                $offset = ($limit * ($pq_curPage - 1));
            }

		$builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	    $builder->join($comp_txn_tbl, $comp_txn_tbl.'.voucher_txn_id  ='.$voucher_tbl.'.voucher_txn_id AND (master_id_type = "acc" or master_id_type = "bsd")', 'left');
	    $builder->select($comp_txn_tbl.'.txn_id, '.$comp_txn_tbl.'.master_id, '. $comp_txn_tbl.'.master_id_type');
	    
		$builder->where($voucher_tbl.'.comp_id', $this->company_id);
		if($voucher_type_id != 0)
			$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);
		$builder->where($voucher_tbl.'.voucher_tag', 'OPTIONL');
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}

		$builder->orderBy('voucher_date');
		$builder->orderBy('voucher_txn_id');
		$builder->limit($limit,$offset);  
		$result = $builder->get()->getResultArray();

		$data = [];
		foreach($result as $key => $value)
        {
        	$voucher_txn_id  = $value['voucher_txn_id'];
        	$voucher_type_id  = $value['voucher_type_id'];
        	$voucher_type  = $value['comp_vch_type'];
        	$comp_vch_no = $value['comp_vch_no'];
        	$account_name = 'Self';
        	$narration = '';
        	$credit = '';
        	$debit = '';

            if($value['master_id_type'] == 'acc'){

         		$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
              	$builder = $this->db->table($table)->select($table.'.*');
    			$builder->where($table.'.txn_id', $value['txn_id']);
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
              	$account = $builder->get()->getRowArray();
              	if($account){

                 	if($account['acc_oth_txn_drcr'] == 'c'){
                 		$account_info   = $this->account_info($value['master_id']);
						$account_name   = $account_info['acc_name'];
                    	$credit 		= $account['acc_oth_txn_amount'];
                 	}
                 	if($account['acc_oth_txn_drcr'] == 'd'){
                 		$account_info   = $this->account_info($value['master_id']);
						$account_name   = $account_info['acc_name'];
                    	$debit 		  	= $account['acc_oth_txn_amount'];
              		}
              	}
         	}
         	if($value['master_id_type'] == 'bsd'){
				$table = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
				$builder = $this->db->table($table);
				$builder->where('txn_id', $value['txn_id']);
				if($this->session->get('ses_boid')!='')
					$builder->where('bo_id', $this->session->get('ses_boid'));
				$account = $builder->get()->getRowArray();
				if($account){
					if($account['acc_oth_txn_drcr'] == 'c'){
						$bill_sundry_info   = $this->bill_sundry_info($value['master_id']);
						$account_name 		= $bill_sundry_info['bill_sundry_name'];
						$credit 			= $account['acc_oth_txn_drcr'];
					}
					if($account['acc_oth_txn_drcr'] == 'd'){
						$bill_sundry_info   = $this->bill_sundry_info($value['master_id']);
						$account_name  	    = $bill_sundry_info['bill_sundry_name'];
						$debit  			= $account['acc_oth_txn_drcr'];
					}
				}
         	}

         	$credit = $credit != '' ? formatAmount($credit) : '';
         	$debit = $debit != '' ? formatAmount($debit) : '';

         	$voucher_narration = $this->get_voucher_narration_info($voucher_txn_id,'short',$value['txn_id']);
         	if($voucher_narration){
         		$narration = $voucher_narration['vch_short_narr'];
         	}

        	$voucher_date         = date("d-m-Y", strtotime($value['voucher_date']));
        	$data[] = [

                'voucher_txn_id'  => $voucher_txn_id,
                'voucher_type_id' => $voucher_type_id,
                'voucher_type' 	  => $voucher_type,
                'voucher_no'      => $comp_vch_no,
                'account_name'    => $account_name,
                'voucher_date'    => $voucher_date,
                'credit'          => $credit,
                'debit'           => $debit,
                'narration'		  => $narration
            ];
			
        }
		return  [
			'totalRecords'	=> $total_records,
			'curPage'		=> $pq_curPage,
			'data'			=> $data,
		];
		
	}
   function get_unit_name($unit_id){	 
	 $item_unit_master_tbl = $this->company_id.'_itmunitmst_'.$this->session->get('ses_comp_fy_id');
	 $item_unit = $this->db->table($item_unit_master_tbl)->where('unit_id', $unit_id)->where('comp_id', $this->company_id)->get()->getRowArray();
	 return $item_unit['item_unit'];
    }
    
     public function ajax_material_issue_register_list(){
        $company_sales_accounts = $this->company_sales_accounts();
        $comp_id         = $this->session->get('ses_company_id');
	    $ses_comp_fy_id  = $this->session->get('ses_comp_fy_id');
	    $base_url        = base_url().'/'.getenv('AdminPath');
	    $comptxnmst_tbl  = $comp_id.'_comptxnmst_'.$ses_comp_fy_id;
	    $from_date       =  $_POST["from_date"];
	    $to_date         =  $_POST["to_date"];
	   
	   if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
	   if($from_date!='' || $to_date!='')
         {
	    
        
         
	    $vch_txn_conso_tbl = $comp_id.'_vhtxnconso_'.$ses_comp_fy_id;
	    $builder = $this->db->table($vch_txn_conso_tbl); 
        $builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);	 
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));
		$builder->where('voucher_type_id','7');
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
		$total_Records = $builder->countAllResults();
		
		if($pq_curPage=='0') $pq_curPage='1';
		$offset = ($pq_rPP * ($pq_curPage - 1));

            if ($offset > $total_Records)
            {        
                $pq_curPage = ceil($total_Records / $pq_rPP);
                $offset = ($pq_rPP * ($pq_curPage - 1));
            }
            
		
		$builder = $this->db->table($vch_txn_conso_tbl);                
		$builder->where('comp_id', $comp_id);	 
		$builder->where('voucher_type_id','7');
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
		$builder->orderBy('voucher_date');
		$builder->orderBy('voucher_txn_id');
	    $builder->limit($pq_rPP,$offset);
		$result  = $builder->get()->getResultArray();
		
		
		$all_records = array();	
		if($result){
		    foreach($result as $row){
		       $voucher_txn_id   = $row['voucher_txn_id'];
		       $voucher_type_id  = $row['voucher_type_id'];
		       $comp_vch_series_id = $row['comp_vch_series_id'];
		       
		       	$builder = $this->db->table($comptxnmst_tbl); 
                $builder->orderBy('txn_id');    
                $builder->limit(1);
                $builder->where('voucher_txn_id', $voucher_txn_id);	
                $builder->where('master_id_type', 'acc');
                $builder->where('comp_vch_series_id', $voucher_type_id);	
        		$builder->where('comp_id', $comp_id);	
        		$party_data = $builder->get()->getRowArray();
        		
        		$account_name = 'Self';
        		if($party_data)
    		    {
        		    $account_info = $this->get_account_info($party_data['master_id']);     
        		    if(isset($account_info['acc_name'])){
        		        $account_name  = $account_info['acc_name'];
        		    }
    		    }
        		
        		 $material_centre_info = $this->material_centre_info($comp_id,$row['mat_cent_id']);
        	     if($material_centre_info)
        	        $material_centre = $material_centre_info['mat_cent_name'];
        	     else
        	        $material_centre  = '';     
	       
	            $item_data = $this->db->table($comptxnmst_tbl)
                                            ->select('txn_id, master_id')
                                            ->where('voucher_txn_id', $voucher_txn_id)
                                            ->where('comp_vch_series_id', $voucher_type_id)
                                            ->where('master_id_type', 'itm')
                                            ->where('comp_id', $comp_id)
                                            ->get()->getResultArray();
                if($item_data)
    		    {
    		        foreach($item_data as $key2 => $value2)
    		        {
    		            
    		            $item_info = $this->get_item_info($value2['master_id']);
                        $item_name = $item_info['item_name'];
                        $item_unit = $this->get_unit_name($item_info['item_unit']);
                        
                        
    		            $item_txn_table =  $this->company_id.'_itemtxnnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
						if($this->session->get('ses_boid')!=''){
							$single_item_data = $this->db->table($item_txn_table)
                                            ->select('*')
                                            ->where('txn_id', $value2['txn_id'])//2
											->where('bo_id', $this->session->get('ses_boid'))
                                            ->get()->getRowArray();
						}
						else{
						$single_item_data = $this->db->table($item_txn_table)
                                            ->select('*')
                                            ->where('txn_id', $value2['txn_id'])//2
                                            ->get()->getRowArray();
						}
 
                        $quantity = 0;
                        if($single_item_data['item_txn_drcr']=='c') // Sales
						 {
						    $quantity = $single_item_data['item_txn_qty'];
						 }
					     
						 
						 $price = ($single_item_data['item_txn_amount'] / $single_item_data['item_txn_qty']);
						 $amount = $single_item_data['item_txn_amount'];
						 $short_narration = $single_item_data['item_txn_narr'];
						 
						$all_records[] = [
						    'voucher_txn_id'            => $row['voucher_txn_id'],
						    'voucher_type_id'           => $row['voucher_type_id'],
						    'comp_vch_series_id'        => $row['comp_vch_series_id'],
						    'voucher_date'              => date("d-m-Y", strtotime($row['voucher_date'])),
						    'material_centre'           => $material_centre,
						    'comp_vch_no'               => $row['comp_vch_no'],
						    'account_name'              => $account_name,
						    'item_name'                 => $item_name,
						    'item_unit'                 => $item_unit,
						    'quantity'                  => $quantity,
						    'price'                     => formatAmount($price),
						    'amount'                    => formatAmount($amount),
						    'short_narration'           => $short_narration
						    ];

                        
    		        }
    		    }
        	
		      }
		  }  
	
// 		echo "<pre>";print_r($all_records);exit;     
  }
  else{
     $total_Records=0; $all_records=array(); 
       
  }
   
    echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($all_records)."}"; 	       

  }
  
  
  public function ajax_inward_challan_due_list(){
        $company_sales_accounts = $this->company_sales_accounts();
        $comp_id         =  $this->session->get('ses_company_id');
	    $ses_comp_fy_id  =  $this->session->get('ses_comp_fy_id');
	    $base_url        =  base_url().'/'.getenv('AdminPath');
	    $comptxnmst_tbl  =  $comp_id.'_comptxnmst_'.$ses_comp_fy_id;
	    $from_date       =  $_POST["from_date"];
	    $to_date         =  $_POST["to_date"];
	   
	   if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
	   if($from_date!='' || $to_date!='')
         {
	    
         
	    $vch_txn_conso_tbl = $comp_id.'_vhtxnconso_'.$ses_comp_fy_id;
	    $builder           = $this->db->table($vch_txn_conso_tbl); 
        $builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));		
		$builder->where('(voucher_tag LIKE "PESIDEF%" OR voucher_tag LIKE "ICEPDEF%")');
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
		$total_Records = $builder->countAllResults();
			if($pq_curPage=='0') $pq_curPage='1';
		$offset = ($pq_rPP * ($pq_curPage - 1));

            if ($offset > $total_Records)
            {        
                $pq_curPage = ceil($total_Records / $pq_rPP);
                $offset = ($pq_rPP * ($pq_curPage - 1));
            }
		
		
		$builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);	 
		$builder->where('(voucher_tag LIKE "PESIDEF%" OR voucher_tag LIKE "ICEPDEF%")');
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
	    $builder->limit($pq_rPP,$offset);
		$result  = $builder->get()->getResultArray();
		
		$all_records = array();	
		if($result){
		    foreach($result as $row){
		       $voucher_txn_id   = $row['voucher_txn_id'];
		       $voucher_type_id  = $row['voucher_type_id'];
		       $comp_vch_series_id = $row['comp_vch_series_id'];
		       
		       	$builder = $this->db->table($comptxnmst_tbl); 
                $builder->orderBy('txn_id');    
                $builder->limit(1);
                $builder->where('voucher_txn_id', $voucher_txn_id);	
                $builder->where('master_id_type', 'acc');
                $builder->where('comp_vch_series_id', $comp_vch_series_id);	
        		$builder->where('comp_id', $comp_id);	
        		$party_data = $builder->get()->getRowArray();
        		//echo $this->db->GetLastquery();
        		$account_name = 'Self';
        		if($party_data)
    		    {
        		    $account_info = $this->get_account_info($party_data['master_id']);     
        		    if(isset($account_info['acc_name'])){
        		        $account_name  = $account_info['acc_name'];
        		    }
    		    }
        		
        		 $material_centre_info = $this->material_centre_info($comp_id,$row['mat_cent_id']);
        	     if($material_centre_info)
        	        $material_centre = $material_centre_info['mat_cent_name'];
        	     else
        	        $material_centre  = '';     
	       
	            $item_data = $this->db->table($comptxnmst_tbl)
                                            ->select('txn_id, master_id')
                                            ->where('voucher_txn_id', $voucher_txn_id)
                                            ->where('comp_vch_series_id', $comp_vch_series_id)
                                            ->where('master_id_type', 'itm')
                                            ->where('comp_id', $comp_id)
                                            ->get()->getResultArray();
                if($item_data)
    		    {
    		        foreach($item_data as $key2 => $value2)
    		        {
    		            
    		            $item_info = $this->get_item_info($value2['master_id']);
                        $item_name = $item_info['item_name'];
                        $item_unit = $this->get_unit_name($item_info['item_unit']);
                        
                        
    		            $item_txn_table =  $this->company_id.'_itemtxnnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
    		            
						if($this->session->get('ses_boid')!=''){
						  $single_item_data = $this->db->table($item_txn_table)
                                            ->select('*')
                                            ->where('txn_id', $value2['txn_id'])
											->where('bo_id', $this->session->get('ses_boid'))
                                            ->get()->getRowArray();
											
						
						}
						else{
						$single_item_data = $this->db->table($item_txn_table)
                                            ->select('*')
                                            ->where('txn_id', $value2['txn_id'])
                                            ->get()->getRowArray();
						}
 
                        $quantity = 0;
                        if($single_item_data['item_txn_drcr']=='d') // Purchase
						 {
						    $quantity = $single_item_data['item_txn_qty'];
						 }
					     
						 
						 $price  = $single_item_data['item_txn_amount'];
						 $amount = $single_item_data['item_txn_amount'];
						 $short_narration = $single_item_data['item_txn_narr'];
						 
						$all_records[] = [
						    'voucher_txn_id'            => $row['voucher_txn_id'],
						    'voucher_type_id'           => $row['voucher_type_id'],
						    'comp_vch_series_id'        => $row['comp_vch_series_id'],
						    'voucher_date'              => date("d-m-Y", strtotime($row['voucher_date'])),
						    'material_centre'           => $material_centre,
						    'comp_vch_no'               => $row['comp_vch_no'],
						    'account_name'              => $account_name,
						    'item_name'                 => $item_name,
						    'item_unit'                 => $item_unit,
						    'quantity'                  => $quantity,
						    'price'                     => $price,
						    'amount'                    => $amount,
						    'short_narration'           => $short_narration
						    ];

                        
    		        }
    		    }
        	
		      }
		  }  
	
// 		echo "<pre>";print_r($all_records);exit;     
  }
  else{
     $total_Records=0; $all_records=array(); 
       
  }
   
    echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($all_records)."}"; 	       

  }
  
  
    public function ajax_material_receipt_register_list(){
        $company_sales_accounts = $this->company_sales_accounts();
        $comp_id         = $this->session->get('ses_company_id');
	    $ses_comp_fy_id  = $this->session->get('ses_comp_fy_id');
	    $base_url        = base_url().'/'.getenv('AdminPath');
	    $comptxnmst_tbl  = $comp_id.'_comptxnmst_'.$ses_comp_fy_id;
	    $from_date       =  $_POST["from_date"];
	    $to_date         =  $_POST["to_date"];
	   
	   if(isset($_POST["pq_curpage"]) && isset($_POST["pq_rpp"]) )
            {
                $pq_curPage = (int)$_POST["pq_curpage"];
                $pq_rPP     = (int)$_POST["pq_rpp"];
            } 
	   if($from_date!='' || $to_date!='')
         {
	  
	    $vch_txn_conso_tbl = $comp_id.'_vhtxnconso_'.$ses_comp_fy_id;
	    $builder           = $this->db->table($vch_txn_conso_tbl); 
        $builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);	
		if($this->session->get('ses_boid')!='')
		$builder->where('bo_id', $this->session->get('ses_boid'));		
		$builder->where('voucher_type_id','6');
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
		$total_Records = $builder->countAllResults();
		
		if($pq_curPage=='0') $pq_curPage='1';
		$offset = ($pq_rPP * ($pq_curPage - 1));

            if ($offset > $total_Records)
            {        
                $pq_curPage = ceil($total_Records / $pq_rPP);
                $offset = ($pq_rPP * ($pq_curPage - 1));
            }
            
            
		
		$builder->orderBy('voucher_date');                
		$builder->where('comp_id', $comp_id);	 
		$builder->where('voucher_type_id','6');
		$builder->where('voucher_date >=', $from_date);
		$builder->where('voucher_date <=', $to_date);
	    $builder->limit($pq_rPP,$offset);
		$result  = $builder->get()->getResultArray();
		
		$all_records = array();	
		if($result){
		    foreach($result as $row){
		       $voucher_txn_id   = $row['voucher_txn_id'];
		       $voucher_type_id  = $row['voucher_type_id'];
		       $comp_vch_series_id = $row['comp_vch_series_id'];
		       
		       	$builder = $this->db->table($comptxnmst_tbl); 
                $builder->orderBy('txn_id');    
                $builder->limit(1);
                $builder->where('voucher_txn_id', $voucher_txn_id);	
                $builder->where('master_id_type', 'acc');
                $builder->where('comp_vch_series_id', $voucher_type_id);	
        		$builder->where('comp_id', $comp_id);	
        		$party_data = $builder->get()->getRowArray();
        		
        		$account_name = 'Self';
        		if($party_data)
    		    {
        		    $account_info = $this->get_account_info($party_data['master_id']);     
        		    if(isset($account_info['acc_name'])){
        		        $account_name  = $account_info['acc_name'];
        		    }
    		    }
        		
        		 $material_centre_info = $this->material_centre_info($comp_id,$row['mat_cent_id']);
        	     if($material_centre_info)
        	        $material_centre = $material_centre_info['mat_cent_name'];
        	     else
        	        $material_centre  = '';     
	       
	            $item_data = $this->db->table($comptxnmst_tbl)
                                            ->select('txn_id, master_id')
                                            ->where('voucher_txn_id', $voucher_txn_id)
                                            ->where('comp_vch_series_id', $voucher_type_id)
                                            ->where('master_id_type', 'itm')
                                            ->where('comp_id', $comp_id)
                                            ->get()->getResultArray();
                if($item_data)
    		    {
    		        foreach($item_data as $key2 => $value2)
    		        {
    		            
    		            $item_info = $this->get_item_info($value2['master_id']);
                        $item_name = $item_info['item_name'];
                        $item_unit = $this->get_unit_name($item_info['item_unit']);
                        
                        
    		            $item_txn_table =  $this->company_id.'_itemtxnnnn_'.$value2['master_id'].'_'.$this->session->get('ses_comp_fy_id');
    		            
						if($this->session->get('ses_boid')!=''){
						 $single_item_data = $this->db->table($item_txn_table)
                                            ->select('*')
                                            ->where('txn_id', $value2['txn_id'])
											->where('bo_id', $this->session->get('ses_boid'))
                                            ->get()->getRowArray();
						}
						else{
						$single_item_data = $this->db->table($item_txn_table)
                                            ->select('*')
                                            ->where('txn_id', $value2['txn_id'])
                                            ->get()->getRowArray();
						}
						
						
						
						
 
                        $quantity = 0;
                        if($single_item_data['item_txn_drcr']=='d') // Purchase
						 {
						    $quantity = $single_item_data['item_txn_qty'];
						 }
					     
						 
						 $price = ($single_item_data['item_txn_amount'] / $single_item_data['item_txn_qty']);
						 $amount = $single_item_data['item_txn_amount'];
						 $short_narration = $single_item_data['item_txn_narr'];
						 
						$all_records[] = [
						    'voucher_txn_id'            => $row['voucher_txn_id'],
						    'voucher_type_id'           => $row['voucher_type_id'],
						    'comp_vch_series_id'        => $row['comp_vch_series_id'],
						    'voucher_date'              => date("d-m-Y", strtotime($row['voucher_date'])),
						    'material_centre'           => $material_centre,
						    'comp_vch_no'               => $row['comp_vch_no'],
						    'account_name'              => $account_name,
						    'item_name'                 => $item_name,
						    'item_unit'                 => $item_unit,
						    'quantity'                  => $quantity,
						    'price'                     => formatAmount($price),
						    'amount'                    => formatAmount($amount),
						    'short_narration'           => $short_narration
						    ];

                        
    		        }
    		    }
        	
		      }
		  }  
	
// 		echo "<pre>";print_r($all_records);exit;     
  }
  else{
     $total_Records=0; $all_records=array(); 
       
  }
   
    echo  "{\"totalRecords\":" . $total_Records . ",\"curPage\":" . $pq_curPage . ",\"data\":".json_encode($all_records)."}"; 	       

  }
  
  

  	public function load_sales_order_register($pq_curPage, $limit, $from_date, $to_date){
        
  		$voucher_type_id = 19;
  		$final = [];

        $voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	    $voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	    $voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
	    $comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	    $acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
	    $acc_crsref_tbl = $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
	    
	    $builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	   	$builder->where($voucher_tbl.'.comp_id', $this->company_id);
	   	$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);
		if($this->session->get('ses_boid')!='')
		$builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));
		
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}
		$builder->orderBy('voucher_date');
		$builder->orderBy('voucher_txn_id');
		$total_records = $builder->countAllResults();
		
		if($pq_curPage=='0') $pq_curPage='1';
		$offset = ($limit * ($pq_curPage - 1));

            if ($offset > $total_records)
            {        
                $pq_curPage = ceil($total_records / $limit);
                $offset = ($limit * ($pq_curPage - 1));
            }
            
            

	    $builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	   	$builder->where($voucher_tbl.'.comp_id', $this->company_id);
	   	$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);					
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}
		$builder->orderBy('voucher_txn_id');
	 	$builder->limit($limit,$offset);  
		$result = $builder->get()->getResultArray();

		foreach($result as $key => $value)
		{
			$amount = '';
			$count_amount = 0;

			$builder = $this->db->table($comp_txn_tbl);
			$builder->select('txn_id, master_id');
			$builder->where('voucher_txn_id', $value['voucher_txn_id']);
			$builder->where('master_id_type', 'aco');
			$builder->orderBy('txn_id', 'acc');
			$builder->limit(1);
			$table = $builder->get()->getRowArray();

			$txn_id = $table['txn_id'];
			$account_id = $table['master_id'];
			$account_name = $this->get_account_info($account_id)['acc_name'];

			$builder = $this->db->table($acc_oth_tbl);
			$builder->where('voucher_txn_id', $value['voucher_txn_id']);
			$builder->where('acc_id', $account_id);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
			$data = $builder->get()->getRowArray();

			if($data['acc_oth_txn_drcr'] == 'd')
			{
				$amount = formatAmount($data['acc_oth_txn_amount']).' DR';
                $count_amount = $data['acc_oth_txn_amount'];
			}
			if($data['acc_oth_txn_drcr'] == 'c')
			{
				$amount = formatAmount($data['acc_oth_txn_amount']).' CR';
                $count_amount = -$data['acc_oth_txn_amount'];
			}

			$order_no = '';
			
	 		$builder = $this->db->table($acc_crsref_tbl);
	 		$builder->where('acc_cross_ref_type', 'SONONNN');
	 		$builder->where('txn_id', $txn_id);
	 		$builder->where('voucher_txn_id', $value['voucher_txn_id']);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
	 		$ref = $builder->get()->getRowArray();
	 		if($ref){
	 			$order_no = $ref['acc_cross_ref_data'];
	 		}

			$voucher_date = date('d-m-Y', strtotime($value['voucher_date']));

			$final[] = [
                    'account_name'     =>   $account_name,
                    'voucher_date'     =>   $voucher_date,
                    'voucher_no'       =>   $value['comp_vch_no'],
                    'order_no'         =>   $order_no,
                    'amount'           =>   $amount,
                    'count_amount'     =>   $count_amount,
                    'comp_vch_type'    =>   $value['comp_vch_type'],
                    'voucher_txn_id'   =>   $value['voucher_txn_id'],
                    'voucher_type_id'  =>   $value['voucher_type_id'],
                ];
		}
		// echo "<pre>";print_r($final);exit;
   
   	return [
   			'totalRecords'	=> $total_records,
   			'curPage'		=> $pq_curPage,
   			'data'			=> $final
   		];	       

  	}

    public function load_purchase_order_register($pq_curPage, $limit,  $from_date, $to_date){
        
  		$voucher_type_id = 12;
  		$final = [];

			$voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
			$voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
			$voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
			$comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
			$acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
			$acc_crsref_tbl = $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
	    
	    $builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	   	$builder->where($voucher_tbl.'.comp_id', $this->company_id);
	   	$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);
			$builder->where($voucher_tbl.'.bo_id', $this->bo_id);
		
			if($from_date!=''){
				$builder->where('voucher_date >=', $from_date);
			}
			if($to_date!=''){
				$builder->where('voucher_date <=', $to_date);
			}
			$builder->orderBy('voucher_txn_id'); 
			$total_records = $builder->countAllResults();
			if($pq_curPage=='0') $pq_curPage='1';
			$offset = ($limit * ($pq_curPage - 1));

	    if ($offset > $total_records)
	    {        
	        $pq_curPage = ceil($total_records / $limit);
	        $offset = ($limit * ($pq_curPage - 1));
	    }

	    $builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	   	$builder->where($voucher_tbl.'.comp_id', $this->company_id);
	   	$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);					
			if($from_date!=''){
				$builder->where('voucher_date >=', $from_date);
			}
			if($to_date!=''){
				$builder->where('voucher_date <=', $to_date);
			}
			$builder->orderBy('voucher_date');
			$builder->orderBy('voucher_txn_id');
		 	$builder->limit($limit,$offset);  
			$result = $builder->get()->getResultArray();

			foreach($result as $key => $value)
			{
				$amount = '';
				$count_amount = 0;

				$builder = $this->db->table($comp_txn_tbl);
				$builder->select('txn_id, master_id');
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				$builder->where('master_id_type', 'aco');
				$builder->orderBy('txn_id', 'acc');
				$builder->limit(1);
				$table = $builder->get()->getRowArray();

				$txn_id = $table['txn_id'];

				$account_id = $table['master_id'];
				$account_name = $this->get_account_info($account_id)['acc_name'];

				$builder = $this->db->table($acc_oth_tbl);
				$builder->where('voucher_txn_id', $value['voucher_txn_id']);
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
				$builder->where('acc_id', $account_id);
				$data = $builder->get()->getRowArray();

				if($data['acc_oth_txn_drcr'] == 'd')
				{
					$amount = number_format($data['acc_oth_txn_amount'],2).' DR';
	                $count_amount = $data['acc_oth_txn_amount'];
				}
				if($data['acc_oth_txn_drcr'] == 'c')
				{
					$amount = number_format($data['acc_oth_txn_amount'],2).' CR';
	                $count_amount = -$data['acc_oth_txn_amount'];
				}

				$order_no = '';
				
		 		$builder = $this->db->table($acc_crsref_tbl);
		 		$builder->where('acc_cross_ref_type', 'PONONNN');
		 		$builder->where('txn_id', $txn_id);
				if($this->session->get('ses_boid')!='')
				$builder->where('bo_id', $this->session->get('ses_boid'));
		 		$builder->where('voucher_txn_id', $value['voucher_txn_id']);
		 		$ref = $builder->get()->getRowArray();
		 		if($ref){
		 			$order_no = $ref['acc_cross_ref_data'];
		 		}

				$voucher_date = date('d-m-Y', strtotime($value['voucher_date']));
				$voucher_date = date('d-m-Y', strtotime($value['voucher_date']));

				$final[] = [
	                    'account_name'     =>   $account_name,
	                    'voucher_date'     =>   $voucher_date,
	                    'voucher_no'       =>   $value['comp_vch_no'],
	                    'order_no'         =>   $order_no,
	                    'amount'           =>   $amount,
	                    'count_amount'     =>   $count_amount,
	                    'comp_vch_type'    =>   $value['comp_vch_type'],
	                    'voucher_txn_id'   =>   $value['voucher_txn_id'],
	                    'voucher_type_id'  =>   $value['voucher_type_id'],
	                ];
			}
   
   		return [
   			'totalRecords'	=> $total_records,
   			'curPage'		=> $pq_curPage,
   			'data'			=> $final
   		];	       

	} 

	public function load_quotation_register($pq_curPage, $limit, $from_date, $to_date){
        
  		$voucher_type_id = 17;
  		$final = [];

        $voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	    $voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	    $voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
	    $comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	    $acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
	    $acc_crsref_tbl = $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
	    
	    $builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	   	$builder->where($voucher_tbl.'.comp_id', $this->company_id);
	   	$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);
		if($this->session->get('ses_boid')!='')
		$builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));
		
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}
		$builder->orderBy('voucher_txn_id'); 
		$total_records = $builder->countAllResults();
		if($pq_curPage=='0') $pq_curPage='1';
		$offset = ($limit * ($pq_curPage - 1));

            if ($offset > $total_records)
            {        
                $pq_curPage = ceil($total_records / $limit);
                $offset = ($limit * ($pq_curPage - 1));
            }

	    $builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	   	$builder->where($voucher_tbl.'.comp_id', $this->company_id);
	   	$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);					
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}
		$builder->orderBy('voucher_date');
		$builder->orderBy('voucher_txn_id');
	 	$builder->limit($limit,$offset);  
		$result = $builder->get()->getResultArray();

		foreach($result as $key => $value)
		{
			$amount = '';
			$count_amount = 0;

			$builder = $this->db->table($comp_txn_tbl);
			$builder->select('txn_id, master_id');
			$builder->where('voucher_txn_id', $value['voucher_txn_id']);
			$builder->where('master_id_type', 'aco');
			$builder->orderBy('txn_id', 'acc');
			$builder->limit(1);
			$table = $builder->get()->getRowArray();

			$txn_id = $table['txn_id'];

			$account_id = $table['master_id'];
			$account_name = $this->get_account_info($account_id)['acc_name'];

			$builder = $this->db->table($acc_oth_tbl);
			$builder->where('voucher_txn_id', $value['voucher_txn_id']);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
			$builder->where('acc_id', $account_id);
			$data = $builder->get()->getRowArray();

			if($data['acc_oth_txn_drcr'] == 'd')
			{
				$amount = formatAmount($data['acc_oth_txn_amount']).' DR';
                $count_amount = $data['acc_oth_txn_amount'];
			}
			if($data['acc_oth_txn_drcr'] == 'c')
			{
				$amount = formatAmount($data['acc_oth_txn_amount']).' CR';
                $count_amount = -$data['acc_oth_txn_amount'];
			}


			$voucher_date = date('d-m-Y', strtotime($value['voucher_date']));

			$final[] = [
                    'account_name'     =>   $account_name,
                    'voucher_date'     =>   $voucher_date,
                    'voucher_no'       =>   $value['comp_vch_no'],
                    'unit'         	   =>   '',
                    'quantity'         =>   '',
                    'amount'           =>   $amount,
                    'count_amount'     =>   $count_amount,
                    'comp_vch_type'    =>   $value['comp_vch_type'],
                    'voucher_txn_id'   =>   $value['voucher_txn_id'],
                    'voucher_type_id'  =>   $value['voucher_type_id'],
                ];
		}
		// echo "<pre>";print_r($final);exit;
   
   		return [
   			'totalRecords'	=> $total_records,
   			'curPage'		=> $pq_curPage,
   			'data'			=> $final
   		];	       

	}

	public function load_purchase_requisition_register($pq_curPage, $limit,  $from_date, $to_date){
        
  		$voucher_type_id = 21;
  		$final = [];

        $voucher_tbl = $this->company_id.'_vhtxnconso_'.$this->session->get('ses_comp_fy_id');
	    $voucher_type_tbl = $this->company_id.'_cmpvchtype_'.$this->session->get('ses_comp_fy_id');
	    $voucher_series_tbl = $this->company_id.'_cmpvchseri_'.$this->session->get('ses_comp_fy_id');
	    $comp_txn_tbl = $this->company_id.'_comptxnmst_'.$this->session->get('ses_comp_fy_id');
	    $acc_oth_tbl = $this->company_id.'_accttxnoth_'.$this->session->get('ses_comp_fy_id');
	    $acc_crsref_tbl = $this->company_id.'_acctcrsref_'.$this->session->get('ses_comp_fy_id');
	    
	    $builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	   	$builder->where($voucher_tbl.'.comp_id', $this->company_id);
	   	$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);
		if($this->session->get('ses_boid')!='')
		$builder->where($voucher_tbl.'.bo_id', $this->session->get('ses_boid'));
		
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}
		$builder->orderBy('voucher_txn_id'); 
		$total_records = $builder->countAllResults();
		if($pq_curPage=='0') $pq_curPage='1';
		$offset = ($limit * ($pq_curPage - 1));

            if ($offset > $total_records)
            {        
                $pq_curPage = ceil($total_records / $limit);
                $offset = ($limit * ($pq_curPage - 1));
            }
            
            

	    $builder = $this->db->table($voucher_tbl);
	    $builder->select($voucher_tbl.'.*');
	    $builder->join($voucher_type_tbl, $voucher_type_tbl.'.voucher_type_id ='.$voucher_tbl.'.voucher_type_id');
	    $builder->select($voucher_type_tbl.'.comp_vch_type');
	    $builder->join($voucher_series_tbl, $voucher_series_tbl.'.comp_vch_series_id  ='.$voucher_tbl.'.comp_vch_series_id');
	    $builder->select($voucher_series_tbl.'.comp_vch_series');
	   	$builder->where($voucher_tbl.'.comp_id', $this->company_id);
	   	$builder->where($voucher_tbl.'.voucher_type_id', $voucher_type_id);					
		if($from_date!=''){
			$builder->where('voucher_date >=', $from_date);
		}
		if($to_date!=''){
			$builder->where('voucher_date <=', $to_date);
		}
		$builder->orderBy('voucher_date');
		$builder->orderBy('voucher_txn_id');
	 	$builder->limit($limit,$offset);  
		$result = $builder->get()->getResultArray();

		foreach($result as $key => $value)
		{
			$amount = '';
			$count_amount = 0;

			$builder = $this->db->table($comp_txn_tbl);
			$builder->select('txn_id, master_id');
			$builder->where('voucher_txn_id', $value['voucher_txn_id']);
			$builder->where('master_id_type', 'aco');
			$builder->orderBy('txn_id', 'acc');
			$builder->limit(1);
			$table = $builder->get()->getRowArray();

			$txn_id = $table['txn_id'];

			$account_id = $table['master_id'];
			$account_name = $this->get_account_info($account_id)['acc_name'];

			$builder = $this->db->table($acc_oth_tbl);
			$builder->where('voucher_txn_id', $value['voucher_txn_id']);
			if($this->session->get('ses_boid')!='')
			$builder->where('bo_id', $this->session->get('ses_boid'));
			$builder->where('acc_id', $account_id);
			$data = $builder->get()->getRowArray();

			if($data['acc_oth_txn_drcr'] == 'd')
			{
				$amount = formatAmount($data['acc_oth_txn_amount']).' DR';
                $count_amount = $data['acc_oth_txn_amount'];
			}
			if($data['acc_oth_txn_drcr'] == 'c')
			{
				$amount = formatAmount($data['acc_oth_txn_amount']).' CR';
                $count_amount = -$data['acc_oth_txn_amount'];
			}


			$voucher_date = date('d-m-Y', strtotime($value['voucher_date']));

			$final[] = [
                    'account_name'     =>   $account_name,
                    'voucher_date'     =>   $voucher_date,
                    'voucher_no'       =>   $value['comp_vch_no'],
                    'amount'           =>   $amount,
                    'count_amount'     =>   $count_amount,
                    'comp_vch_type'    =>   $value['comp_vch_type'],
                    'voucher_txn_id'   =>   $value['voucher_txn_id'],
                    'voucher_type_id'  =>   $value['voucher_type_id'],
                ];
		}
		// echo "<pre>";print_r($final);exit;
   
   		return [
   			'totalRecords'	=> $total_records,
   			'curPage'		=> $pq_curPage,
   			'data'			=> $final
   		];	       

	}
    
}