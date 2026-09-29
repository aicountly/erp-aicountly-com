<?php
namespace App\Models\Api;

use CodeIgniter\Model;
use App\Models\Admin\StockStatusModel;
use App\Models\Admin\LedgerModel;
use App\Libraries\externaldb;

class ResaveSaleVouchersModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $external_db         = \Config\Database::connect();
        $this->externaldb    = new externaldb();
        $this->univaictly    = $this->externaldb->univaictly_db();
		$this->aicountly_db  = $this->externaldb->aicountly_db();
		$this->sispluuid_db  = $this->externaldb->sispluuid_db();
		$this->pg_univaictlydb  = $this->externaldb->postgr_univaictlydb();
		$this->StockStatusModel =  new StockStatusModel();
		$this->LedgerModel         = new LedgerModel();  
    }
	
/**
 * =====================================================================
 * COMPLETE ResaveVoucherStatus with single voucher support
 * When vch_txn_id is passed → resave only that one voucher
 * When vch_txn_id is null/0 → resave all vouchers in date range
 * =====================================================================
 */
public function ResaveVoucherStatus(int $compId, int $boId, int $fyId, array $input): string
{
    $db = $this->db;
    $VM = new \App\Models\Admin\VouchersModel();
    $CM = new \App\Models\CommonModel();

    $startDate    = $input['start_date']   ?? '';
    $endDate      = $input['end_date']     ?? '';
    $voucherType  = $input['voucher_type'] ?? 'sale';
	$uuid         = $input['uuid'] ?? 0;
	$profile_id   = $input['profile_id'] ?? 0;
    $singleVtxnId = isset($input['vch_txn_id']) ? (int)$input['vch_txn_id'] : 0;

    $vchTypeMap = ['sale' => 18, 'purchase' => 11];
    $voucher_type_id = $vchTypeMap[strtolower($voucherType)] ?? 0;

    if ($voucher_type_id === 0) {
        return json_encode(['status' => false, 'message' => 'Invalid voucher_type. Use sale or purchase.']);
    }

    // ── Single voucher mode: no date range needed ──
    if ($singleVtxnId > 0) {
        $startDate = '';
        $endDate   = '';
    } else {
        // ── Date range mode: validate dates ──
        $startDate = date('Y-m-d', strtotime($startDate));
        $endDate   = date('Y-m-d', strtotime($endDate));
        if ($startDate === '1970-01-01' || $endDate === '1970-01-01') {
            return json_encode(['status' => false, 'message' => 'Invalid date format. Use YYYY-MM-DD.']);
        }
    }

    // ── Branch / GSTIN info ──
    $adrs_info = $CM->get_comp_ho_adrs_info($compId, $boId);
    if (!empty($adrs_info)) {
        $bo_state_code = sprintf('%02d', ($adrs_info['state_code']));
        $ses_boname    = $adrs_info['hobo_name'];
        $bo_id         = $adrs_info['hobo_id'];
        $gstinType     = $adrs_info['hobo_gstin_type'];
    } else {
        $bo_state_code = 0;
        $ses_boname    = "";
        $bo_id         = 0;
        $gstinType     = 1;
    }

    $ugst_states = ['35','36','37','26','31','04','06'];

    // ── Fetch vouchers ──
    $builder = $db->table('vchtxnconso')
        ->where('cmp_id', $compId)
        ->where('hobo_id', $boId)
        ->where('vch_type_id', $voucher_type_id);

    if ($singleVtxnId > 0) {
        // Single voucher mode
        $builder->where('vch_txn_id', $singleVtxnId);
    } else {
        // Date range mode
        $builder->where('vch_date >=', $startDate)
                ->where('vch_date <=', $endDate);
    }

    $builder->orderBy('vch_date', 'ASC')->orderBy('vch_txn_id', 'ASC');
    $vouchers = $builder->get()->getResultArray();

    if (empty($vouchers)) {
        $msg = ($singleVtxnId > 0)
            ? "No voucher found with vch_txn_id={$singleVtxnId}"
            : 'No vouchers found in date range.';
        return json_encode([
            'status'=>true, 'message'=>$msg,
            'total'=>0, 'processed'=>0, 'skipped'=>0, 'errors'=>[]
        ]);
    }

    $totalCount = count($vouchers);
    $processedCount = $skippedCount = 0;
    $errorList = [];

    foreach ($vouchers as $voucher) {
        $vtxn   = (int)$voucher['vch_txn_id'];
        $vSubId = (int)$voucher['vch_sub_type_id'];
        $vDate  = $voucher['vch_date'];
        $vSer   = (int)$voucher['vch_series_id'];
        $matId  = (int)($voucher['mat_cent_id'] ?? 0);
        $partyId = 0;
        $pName   = '';

        //try {
            $partyRow = $VM->get_comp_txn_party_data($vtxn);
			
		
            if (!$partyRow) {
                $errorList[] = [
                    'vch_txn_id'=>$vtxn, 'vch_date'=>$vDate,
                    'party_id'=>0, 'party_name'=>'',
                    'error'=>'No party found'
                ];
                $skippedCount++;
                continue;
            }
            $partyId = (int)$partyRow['master_id'];
			
            $accInfo = $VM->account_detail_info($partyId,$compId,$fyId);
			
            $pName   = $accInfo['acc_name'] ?? '';
            if ($pName === '') {
                $errorList[] = [
                    'vch_txn_id'=>$vtxn, 'vch_date'=>$vDate,
                    'party_id'=>$partyId, 'party_name'=>'',
                    'error'=>'Party name is empty'
                ];
                $skippedCount++;
                continue;
            }

            // Validate party state code
            $fullInfo = $VM->account_full_info($partyId,$compId,$fyId,$uuid);
			
            $pStateCode = 0;
            if ($fullInfo && !empty($fullInfo['address_info'])) {
                $aCnt = $fullInfo['address_info']['contact_country'] ?? '';
                $aSt  = $fullInfo['address_info']['contact_state'] ?? '';
                if ($aCnt !== '' && $aSt !== '') {
                    $si = $CM->get_state_info($aCnt, $aSt);
                    if ($si) $pStateCode = sprintf('%02d', $si['state_code']);
                }
            }
			
            if ($pStateCode == 0 || $pStateCode === '00') {
                $errorList[] = [
                    'vch_txn_id'=>$vtxn, 'vch_date'=>$vDate,
                    'party_id'=>$partyId, 'party_name'=>$pName,
                    'error'=>'Party state code missing. Cannot determine IGST/CGST/SGST/UGST. Update party address.'
                ];
                $skippedCount++;
                continue;
            }

            // Read existing data
            $outsupInfo  = $VM->get_gstroutsup_info($vtxn,$compId);
            $pos         = $outsupInfo['outsup_pos'] ?? $bo_state_code;
            $revChg      = $outsupInfo['outsup_rev_chg'] ?? 0;
            $billno      = $outsupInfo['outsup_bill_ref_no'] ?? '';
            $outsupEco   = (int)($outsupInfo['outsup_eco'] ?? 0);
            $cmpSupType  = $outsupInfo['gst_supply_type'] ?? null;

            $fcyInfo     = $VM->vchfcyrate_info($vtxn, $fyId,$compId);
            $fcyRate     = $fcyInfo['vch_fcy_rate'] ?? 0;
            $currId      = $fcyInfo['cmp_fcy_mst_id'] ?? 0;

            $longNarr    = $VM->get_voucher_long_narration($vtxn,$compId);
            $taxIncl     = $VM->check_sale_taxinc($vtxn,$compId);
            $memoChk     = $VM->check_sale_memoentry($vtxn,$compId);
            $bbbData     = $VM->get_bills_txn_data($vtxn,$compId,$boId);
            $prData      = $VM->get_pr_txn_data($vtxn,$compId,$boId);

            $ewbData     = $VM->get_ewbmstreqn_data($vtxn,$compId);
            $ewbSupType  = $ewbData['ewb_supply_type'] ?? 0;
            $ewbSubType  = $ewbData['ewb_sub_supply_type'] ?? 0;
            $einvInfo    = $VM->get_einvmaster_info($vtxn,$compId);
            $einvChk     = !empty($einvInfo);
            $tptInfo     = $VM->transporter_info($vtxn,$compId);
            $bsdTxns     = $VM->grid_bsd_transactions($vtxn,$compId,$boId);

            $taxFlag = 1;
            $etx = $db->table('vchgstsumn')
                ->where('vch_txn_id', $vtxn)
                ->where('cmp_id', $compId)
                ->limit(1)->get()->getRowArray();
            if ($etx && ($etx['gst_rate_grp'] ?? '') === '0,0') $taxFlag = 0;

            $apprInfo = $VM->Voucher_TxnApproval($compId,$uuid,$profile_id);
            $apprAmt  = $apprInfo['approval_amt'];
            $overSup  = $VM->getVoucherOverrideSupplyTypeId($vtxn,$compId);

            $args = [
                $db, $VM, $vtxn, $voucher_type_id, $vDate, $vSer, $matId,
                $partyId, $pName, $compId, $boId,
                $bo_state_code, $pos, $ugst_states, $gstinType, $cmpSupType,
                $fcyRate, $currId, $longNarr, $taxIncl, $memoChk,
                $bbbData, $prData, $bsdTxns,
                $ewbData, $ewbSupType, $ewbSubType, $einvChk, $tptInfo,
                $taxFlag, $apprAmt, $overSup, $billno, $revChg, $outsupEco,$fyId
            ];
            if ($vSubId == 8) {
               $this->_resaveSaleWithItems(...$args);
            } elseif ($vSubId == 0) {
                $this->_resaveSaleWithoutItems(...$args);
            } else {
                $errorList[] = [
                    'vch_txn_id'=>$vtxn, 'vch_date'=>$vDate,
                    'party_id'=>$partyId, 'party_name'=>$pName,
                    'error'=>'Unknown vch_sub_type_id: '.$vSubId
                ];
                $skippedCount++;
                continue;
            }
            $processedCount++;

        /*} catch (\Throwable $e) {
            $errorList[] = [
                'vch_txn_id'=>$vtxn, 'vch_date'=>$vDate,
                'party_id'=>$partyId ?? 0, 'party_name'=>$pName ?? '',
                'error'=>'Line '.$e->getLine().' '.$e->getFile().': '.$e->getMessage()
            ];
            $skippedCount++;
            log_message('error', 'ResaveVoucher vtxn='.$vtxn.': '.$e->getMessage());
            continue;
        } */
    }

    $modeLabel = ($singleVtxnId > 0) ? "Single(vtxn={$singleVtxnId})" : "Range({$startDate} to {$endDate})";

    return json_encode([
        'status'    => true,
        'message'   => "{$modeLabel} Done. Processed:{$processedCount} Skipped:{$skippedCount} Total:{$totalCount}",
        'mode'      => ($singleVtxnId > 0) ? 'single' : 'range',
        'total'     => $totalCount,
        'processed' => $processedCount,
        'skipped'   => $skippedCount,
        'errors'    => $errorList
    ]);
}

// ===================== HELPER METHODS =====================

private function _clearVoucherData($VM, $vtxn, $vchTypeId,$compId,$boId)
{
    $tables = ['cmptxnmstn','itemtxnmst','accttxnmst','billtxnmst','cctxnmstnn',
        'prjtxnmstn','subacctxnm','vchfcyrate','vchgstfcyn','vchhsnsacn',
        'vchgstsumn','acctamtinc','batchtxnmt','einvmaster','ewbmastern',
        'gstdispfrm','gstshipton'];
    foreach ($tables as $t) {
        $VM->clear_table_txn_data($vtxn, $t,$compId);
    }
    $VM->clear_table_narrations_data($vtxn,$compId);
    $VM->clear_register_txn_data($vtxn, $vchTypeId,$compId,$boId);
    $VM->clear_system_journal_txn_data($vtxn, 4,$compId);
}

private function _saveFcyRate($VM, $vtxn, $compId, $fcyRate, $currId)
{
    if (parseAmount($fcyRate) > 0) {
        $VM->save_voucher_fcyrate([
            "cmp_id"=>$compId,"vch_txn_id"=>$vtxn,
            "vch_fcy_rate"=>$fcyRate,"cmp_fcy_mst_id"=>$currId
        ]);
    } else {
        $VM->clear_voucher_fcyrate($vtxn);
    }
}

private function _saveEwbEinvoice($VM, $vtxn, $compId, $boId, $vDate,
    $ewbData, $ewbSupType, $ewbSubType, $einvChk, $tptInfo)
{
    $ewbId = $VM->add_ewb_data([
        "hobo_id"=>$boId,"cmp_id"=>$compId,"ewb_no"=>$ewbData['ewb_no']??'',
        "ewb_date"=>$vDate,"ewb_supply_type"=>$ewbSupType,
        "ewb_sub_supply_type"=>$ewbSubType,"vch_txn_id"=>$vtxn
    ]);

    if ($einvChk) {
        $einvId = $VM->add_einvoice_data([
            "einv_no"=>0,"env_date"=>$vDate,"vch_txn_id"=>$vtxn,"cmp_id"=>$compId
        ]);
        $disp = $ewbData['gstdispfrm_info'] ?? [];
        $ship = $ewbData['gstshipton_info'] ?? [];
        if (!empty($ship) || !empty($disp)) {
            $post = $this->_buildEwbPostData($ship, $disp, $tptInfo, $ewbSubType);
            $VM->add_ewb_txntype_data($ewbSubType, $ewbId, $einvId, $vtxn, $post,$compId);
        }
        if ($tptInfo) {
            $VM->add_transport_data($ewbSubType, $ewbId, $einvId, $vtxn, [
                'vehicle_no'=>$tptInfo['trans_veh_no']??'','vehicle_type'=>$tptInfo['trans_veh_type']??'',
                'transport_mode'=>$tptInfo['trans_mode']??'','transporter_doc_no'=>$tptInfo['trans_doc_no']??'',
                'transport_doc_date'=>$tptInfo['trans_doc_date']??date('Y-m-d'),
                'transport_distance'=>$tptInfo['trans_dist']??'',
                'transporter_id'=>$tptInfo['tpt_id']??'','gstin_id'=>$tptInfo['tpt_gstin']??'',
            ]);
        }
    }
}

private function _buildEwbPostData($ship, $disp, $tptInfo, $ewbSubType): array
{
    $d = [];
    if ($ewbSubType == 2 || $ewbSubType == 4) {
        $p = ($ewbSubType == 2) ? 'shipto2_' : 'shipto4_';
        $d[$p.'addr1']      = $ship['shipto_addr1'] ?? '';
        $d[$p.'addr2']      = $ship['shipto_addr2'] ?? '';
        $d[$p.'place']      = $ship['shipto_place'] ?? '';
        $d[$p.'pin']        = $ship['shipto_pin'] ?? '';
        $d[$p.'state_code'] = $ship['shipto_state_code'] ?? '';
        $d[$p.'gstin']      = $ship['shipto_gstin'] ?? '';
        $d[$p.'legalname']  = $ship['shipto_legal_name'] ?? '';
        $d[$p.'tradename']  = $ship['shipto_trade_name'] ?? '';
    }
    if ($ewbSubType == 3 || $ewbSubType == 4) {
        $p = ($ewbSubType == 3) ? 'dispfrm3_' : 'dispfrm4_';
        $d[$p.'addr1']      = $disp['dispfrm_addr1'] ?? '';
        $d[$p.'addr2']      = $disp['dispfrm_addr2'] ?? '';
        $d[$p.'place']      = $disp['dispfrm_place'] ?? '';
        $d[$p.'pin']        = $disp['dispfrm_pin'] ?? '';
        $d[$p.'state_code'] = $disp['dispfrm_state_code'] ?? '';
    }
    return $d;
}

private function _enrichBsdData($bsdTxns, $vtxn, $compId, $db, $VM): array
{
    $result = [];
    foreach ($bsdTxns as $row) {
        $row['billsundry_fcy_amount'] = $row['billsundry_fcy_amount'] ?? $row['billsundry_amountfc'] ?? 0;
        $row['memo_amount']           = $row['memo_amount'] ?? $row['billsundry_memoamnt'] ?? 0;
        $row['tax_cat_id']            = $row['tax_cat_id'] ?? $row['tax_cat_mst_id'] ?? 0;
        $result[] = $row;
    }
    return $result;
}

private function _flattenBbbData($bbbData): array
{
    $flat = [];
    if (!empty($bbbData)) {
        foreach ($bbbData as $group) {
            $accId = $group['acc_id'];
            foreach ($group['bills_txn_list'] as $row) {
                $flat[] = [
                    'account_id'   => $accId,
                    'reference'    => $row['bill_ref_name'] ?? '',
                    'reference_id' => $row['bill_ref_id'] ?? 0,
                    'method'       => 'Adjustment',
                    'due_date'     => isset($row['bill_due_date']) ? date('d-m-Y', strtotime($row['bill_due_date'])) : '',
                    'drcr'         => $row['bill_txn_dr_cr'] ?? 'D',
                    'amount'       => $row['bill_txn_amt'] ?? 0,
                    'amountfc'     => $row['bill_txn_fcy'] ?? 0,
                    'narration'    => $row['bill_txn_narr'] ?? '',
                ];
            }
        }
    }
    return $flat;
}

private function _flattenPrData($prData): array
{
    $flat = [];
    if (!empty($prData)) {
        foreach ($prData as $group) {
            $accId = $group['acc_id'];
            foreach ($group['pr_txn_list'] as $row) {
                $flat[] = [
                    'acc_id'         => $accId,
                    'project_id'     => $row['project_id'] ?? 0,
                    'proj_txn_drcr'  => $row['proj_txn_drcr'] ?? 'D',
                    'proj_txn_amt'   => $row['proj_txn_amt'] ?? 0,
                    'proj_txn_amtfc' => $row['proj_txn_amtfc'] ?? 0,
                    'proj_txn_narr'  => $row['proj_txn_narr'] ?? '',
                    'acc_type'       => 'acc',
                ];
            }
        }
    }
    return $flat;
}

private function _flattenBatchData($batchData): array
{
    $flat = [];
    if (!empty($batchData)) {
        foreach ($batchData as $group) {
            foreach ($group['grid'] as $row) {
                $flat[] = [
                    'batch_id'           => $row['batch_id'] ?? 0,
                    'batch_no'           => $row['batch_no'] ?? '',
                    'item_id'            => $group['item_id'],
                    'batch_uom_id'       => $group['item_unit_id'],
                    'batch_qty'          => $row['batch_qty'] ?? 0,
                    'expiry_date'        => $row['expiry_date'] ?? '',
                    'manufacturing_date' => $row['manufacturing_date'] ?? '',
                    'batch_method'       => $row['batch_method'] ?? 'Adjustment',
                    'drcr_type'          => 2,
                ];
            }
        }
    }
    return $flat;
}

private function _getAccountSupplyTypeId($db, $vtxn, $accId, $compId, $overrideId): int
{
    $row = $db->table('vchgstsumn')
        ->where('vch_txn_id', $vtxn)->where('acc_bsd_id', $accId)
        ->where('cmp_id', $compId)->where('acc_bsd_type', 1)
        ->limit(1)->get()->getRowArray();
    return (int)($row['inv_supply_id'] ?? ($overrideId ?? 1));
}

private function _saveCompositionEntries($VM, $vtxn, $compId, $boId, $vDate, $vSer, $matId,
    $longNarr, $gstinType, $cmpSupType, $totalTax, $totalFcyTax, $itmsdata, $billsndrydata,
    $taxType, &$compVtxn, &$compSeriesId)
{
    $compVtxn = 0;
    $compSeriesId = $vSer;
    if ($gstinType != 2) return;

    $totalTax     += $VM->GetTotalTax($itmsdata, $taxType, $gstinType, $cmpSupType)['tax_amount'];
    $totalFcyTax  += $VM->GetTotalTax($itmsdata, $taxType, $gstinType, $cmpSupType)['fcy_tax_amount'];
    $totalTax     += $VM->GetTotalTax($billsndrydata, 'bsd', $gstinType, $cmpSupType)['tax_amount'];
    $totalFcyTax  += $VM->GetTotalTax($billsndrydata, 'bsd', $gstinType, $cmpSupType)['fcy_tax_amount'];

    $bridge = $VM->get_voucher_bridge_info($vtxn, 4);
    if (!$bridge) return;

    $compVtxn     = $bridge['vch_txn_id_dest'];
    $gstPaidId    = $VM->getGstPaidAccountId();
    $compTypeId   = 23;
    $compSeriesId = $VM->getSeriesId($compTypeId);

    $VM->update_comps_voucher_cons_data([
        "vch_series_id"=>$compSeriesId,"vch_type_id"=>$compTypeId,"vch_sub_type_id"=>0,
        "vch_date"=>$vDate,"mat_cent_id"=>$matId,"draft_vch_rec_id"=>0
    ], $compVtxn, $compId, $compTypeId);

    $cMainTxn = $VM->add_comp_txn_data([
        "cmp_id"=>$compId,"vch_series_id"=>$compSeriesId,
        "vch_txn_id"=>$compVtxn,"master_id"=>$gstPaidId,'master_id_type'=>'acc'
    ]);
    $VM->add_comp_txn_data([
        "cmp_id"=>$compId,"vch_series_id"=>$compSeriesId,
        "vch_txn_id"=>$compVtxn,"master_id"=>0,'master_id_type'=>'nrr'
    ]);
    $VM->save_voucher_narration($compVtxn, $cMainTxn, 'long', $longNarr);
    $VM->add_acc_txn_data([
        'cmp_id'=>$compId,'acc_id'=>$gstPaidId,'acc_txn_date'=>$vDate,
        'acc_txn_dr_cr'=>1,'acc_txn_amt'=>$totalTax,'acc_txn_fcy'=>$totalFcyTax,
        'vch_txn_id'=>$compVtxn,'txn_id'=>$cMainTxn,'hobo_id'=>$boId,'acc_txn_type'=>1
    ]);
    $VM->add_register_txn_data([
        'acct_vch_type'=>$compTypeId,'cmp_id'=>$compId,'vch_txn_id'=>$compVtxn,
        'txn_id'=>NULL,'vch_date'=>$vDate,'acc_id'=>$gstPaidId,
        'acc_txn_dr_amt'=>$totalTax,'acc_txn_cr_amt'=>0,
        'vch_narr'=>$longNarr??'','hobo_id'=>$boId,'acc_txn_type'=>1
    ]);
}

/**
 * Save Tax Account Yes Entries IGST/CGST/SGST/UT TAX/CESS
 * Fixed to match actual sale edit controller logic:
 *   gstinType==1 → uses $vtxn / $vSer (the sale voucher itself)
 *   gstinType==2 → uses $compVtxn / $compSeriesId (the composition voucher)
 */
private function _saveTaxAccountEntries($gstinType, $vtxn, $vSer,
    $igst, $cgst, $sgst, $utgst, $cess, $vDate, $VM, $compVtxn, $compSeriesId,$compId, $boId)
{
    if ((int)$gstinType === 1) { 
        if ($igst > 0) {
            $VM->save_taxacc_yes_out_data($vtxn, $vSer, -$igst, 1, $vDate, 2,$compId, $boId);
        } elseif ($cgst > 0 && $sgst > 0) {
            $VM->save_taxacc_yes_out_data($vtxn, $vSer, -$cgst, 2, $vDate, 2,$compId, $boId);
            $VM->save_taxacc_yes_out_data($vtxn, $vSer, -$sgst, 3, $vDate, 2,$compId, $boId);
        } elseif ($cgst > 0 && $utgst > 0) {
            $VM->save_taxacc_yes_out_data($vtxn, $vSer, -$cgst, 2, $vDate, 2,$compId, $boId);
            $VM->save_taxacc_yes_out_data($vtxn, $vSer, -$utgst, 4, $vDate, 2,$compId, $boId);
        }
        if ($cess > 0) {
            $VM->save_taxacc_yes_out_data($vtxn, $vSer, -$cess, 5, $vDate, 2,$compId, $boId);
        }
    }

    if ($gstinType == 2) {
        if ($igst > 0) {
            $VM->save_taxacc_yes_out_data($compVtxn, $compSeriesId, -$igst, 1, $vDate, 2,$compId, $boId);
        } elseif ($cgst > 0 && $sgst > 0) {
            $VM->save_taxacc_yes_out_data($compVtxn, $compSeriesId, -$cgst, 2, $vDate, 2,$compId, $boId);
            $VM->save_taxacc_yes_out_data($compVtxn, $compSeriesId, -$sgst, 3, $vDate, 2,$compId, $boId);
        } elseif ($cgst > 0 && $utgst > 0) {
            $VM->save_taxacc_yes_out_data($compVtxn, $compSeriesId, -$cgst, 2, $vDate, 2,$compId, $boId);
            $VM->save_taxacc_yes_out_data($compVtxn, $compSeriesId, -$utgst, 4, $vDate, 2,$compId, $boId);
        }
        if ($cess > 0) {
            $VM->save_taxacc_yes_out_data($compVtxn, $compSeriesId, -$cess, 5, $vDate, 2,$compId, $boId);
        }
    }
}

private function _processBillSundryEntries(
    $bsdData, $vtxn, $vSer, $vDate, $compId, $boId,
    $boStateCode, $pos, $ugstStates, $fcyRate, $memoChk, $VM, $taxFlag,$gstinType,
    &$tIgst, &$tCgst, &$tSgst, &$tUtgst, &$tCess)
{
    if (empty($bsdData)) return;

    foreach ($bsdData as $row) {
        $txnId = $VM->add_comp_txn_data([
            "cmp_id"=>$compId,"vch_series_id"=>$vSer,
            "vch_txn_id"=>$vtxn,"master_id"=>$row['billsundry_id'],'master_id_type'=>'bsd'
        ]);

        // Memo
        if ($memoChk == "1" && isset($row['memo_amount']) && $row['memo_amount'] > 0) {
            $VM->add_acc_txn_data([
                'cmp_id'=>$compId,'acc_id'=>$row['billsundry_id'],'acc_txn_date'=>$vDate,
                'acc_txn_dr_cr'=>($row['memo_amount']<0)?2:1,'acc_txn_amt'=>$row['memo_amount'],
                'acc_txn_fcy'=>(parseAmount($fcyRate)>0)?($row['memo_amount']*$fcyRate):0,
                'vch_txn_id'=>$vtxn,'txn_id'=>$txnId,'hobo_id'=>$boId,'acc_txn_type'=>3
            ]);
        }

        // Account
        $VM->add_acc_txn_data([
            'cmp_id'=>$compId,'acc_id'=>$row['billsundry_id'],'acc_txn_date'=>$vDate,
            'acc_txn_dr_cr'=>($row['billsundry_amount']<0)?2:1,
            'acc_txn_amt'=>$row['billsundry_amount'],
            'acc_txn_fcy'=>$row['billsundry_fcy_amount'] ?? 0,
            'vch_txn_id'=>$vtxn,'txn_id'=>$txnId,'hobo_id'=>$boId,'acc_txn_type'=>1
        ]);

        $VM->save_voucher_narration($vtxn, $txnId, 'short', '',$compId);

        // Tax summary for BSD
        $amount    = parseAmount($row['billsundry_amount']);
        $amountFcy = parseAmount($row['billsundry_fcy_amount'] ?? 0);
        $posCode   = sprintf('%02d', $pos);
        $igstRate  = $row['igst_rate'] ?? 0;
        $taxDet    = $row['tax_details'] ?? [];

        $bsdInfo = $VM->get_bsd_details_info($row['billsundry_id'],$compId);
        $bsdHsn  = $bsdInfo['bsd_hsn_sac'] ?? '';

        $ts = [
            'cmp_id'=>$compId,'vch_txn_id'=>$vtxn,'txn_id'=>$txnId,
            'acc_bsd_id'=>$row['billsundry_id'],'acc_bsd_type'=>2,
            'vch_igst'=>0,'vch_igst_rate'=>0,'vch_cgst'=>0,'vch_cgst_rate'=>0,
            'vch_sgst_ugst'=>0,'vch_sgst_ugst_rate'=>0,'vch_cess'=>0,'vch_cess_rate'=>0,
            'vch_taxable_value'=>$amount,'vch_total_tax'=>0
        ];

        if ($boStateCode != $posCode && !in_array($posCode, $ugstStates)) {
            $v = ($amount * $igstRate) / 100;
            $ts['vch_igst']=$v; $ts['vch_igst_rate']=$igstRate; $ts['vch_total_tax']=$v;
            $tIgst += $v;
        } elseif ($boStateCode == $posCode) {
            $c=($amount*$igstRate/2)/100; $s=$c;
            $ts['vch_cgst']=$c; $ts['vch_cgst_rate']=$igstRate/2;
            $ts['vch_sgst_ugst']=$s; $ts['vch_sgst_ugst_rate']=$igstRate/2;
            $ts['vch_total_tax']=$c+$s;
            $tCgst+=$c; $tSgst+=$s;
        } elseif (in_array($posCode, $ugstStates)) {
            $c=($amount*$igstRate/2)/100; $u=$c;
            $ts['vch_cgst']=$c; $ts['vch_cgst_rate']=$igstRate/2;
            $ts['vch_sgst_ugst']=$u; $ts['vch_sgst_ugst_rate']=$igstRate/2;
            $ts['vch_total_tax']=$c+$u;
            $tCgst+=$c; $tUtgst+=$u;
        }

        $cessRate = 0;
        if (isset($taxDet['cess']) && $taxDet['cess'] > 0) {
            $cessRate = $taxDet['cess'];
            $cv = ($amount * $cessRate) / 100;
            $ts['vch_cess']=$cv; $ts['vch_cess_rate']=$cessRate;
            $ts['vch_total_tax']+=$cv; $tCess+=$cv;
        }

        $ts['vch_date']      = $vDate;
        $ts['is_outward']    = 1;
        $ts['gst_rate_grp']  = '1,'.parseAmount($igstRate).','.parseAmount($cessRate);
        $ts['vch_hsn_sac']   = $bsdHsn;
        $ts['inv_supply_id'] = 0;

        $gstSumId = $VM->add_taxsummary_data($ts, $taxFlag,$gstinType);

        $VM->add_hsnsummary_data([
            'cmp_id'=>$compId,'vch_txn_id'=>$vtxn,'txn_id'=>$txnId,
            'vch_hsn_sac'=>$bsdHsn,'vch_hsn_sac_taxbl_val'=>$amount,
            'vch_hsn_sac_qty'=>0,'vch_hsn_sac_uom'=>0,
            'vch_hsn_sac_igst'=>$ts['vch_igst'],'vch_hsn_sac_cgst'=>$ts['vch_cgst'],
            'vch_hsn_sac_sgst_ugst'=>$ts['vch_sgst_ugst'],'vch_hsn_sac_cess'=>$ts['vch_cess'],
            'inv_supply_id'=>0,'vch_gst_sum_id'=>$gstSumId
        ], $taxFlag,$gstinType);

        // FCY tax summary for BSD
        if (parseAmount($fcyRate) > 0) {
            $tf = [
                'cmp_id'=>$compId,'vch_txn_id'=>$vtxn,'txn_id'=>$txnId,
                'acc_bsd_id'=>$row['billsundry_id'],'acc_bsd_type'=>2,
                'vch_igst_fcy'=>0,'vch_cgst_fcy'=>0,'vch_sgst_ugst_fcy'=>0,
                'vch_cess_fcy'=>0,'vch_taxable_value_fcy'=>$amountFcy,'vch_total_tax_fcy'=>0
            ];
            if ($boStateCode != $posCode && !in_array($posCode, $ugstStates)) {
                $v=($amountFcy*$igstRate)/100; $tf['vch_igst_fcy']=$v; $tf['vch_total_tax_fcy']=$v;
            } elseif ($boStateCode == $posCode) {
                $c=($amountFcy*$igstRate/2)/100; $s=$c;
                $tf['vch_cgst_fcy']=$c; $tf['vch_sgst_ugst_fcy']=$s; $tf['vch_total_tax_fcy']=$c+$s;
            } elseif (in_array($posCode, $ugstStates)) {
                $u=($amountFcy*$igstRate/2)/100;
                $tf['vch_sgst_ugst_fcy']=$u; $tf['vch_total_tax_fcy']=$u;
            }
            if (isset($taxDet['cess']) && $taxDet['cess'] > 0) {
                $cv=($amountFcy*$taxDet['cess'])/100;
                $tf['vch_cess_fcy']=$cv; $tf['vch_total_tax_fcy']+=$cv;
            }
            $VM->add_taxsummary_fcy_data($tf);
        }
    }
}
/**
 * =====================================================================
 * PART 2: Tax Summary calculation + insert method
 * Paste this into your App\Models\Api\AppCommonModel class (after Part 1)
 * =====================================================================
 */

private function _calculateAndInsertTaxSummary(
    $row, $vtxn, $txnId, $masterId, $accBsdType,
    $boStateCode, $pos, $ugstStates, $gstinType, $cmpSupType,
    $compId, $vDate, $VM, $fcyRate, $taxFlag, $invSupplyId, $type
): array
{
    // Determine amount and fcy amount based on type
    if ($type === 'itm') {
        $amount    = parseAmount($row['item_total_amount'] ?? $row['item_amount'] ?? 0);
        $amountFcy = parseAmount($row['item_total_fcy_amount'] ?? 0);
        $itemInfo  = $VM->get_item_details_info($masterId,$compId);
        $hsn       = $itemInfo['itm_hsn'] ?? '';
    } else {
        $amount    = parseAmount($row['amount'] ?? 0);
        $amountFcy = parseAmount($row['amountfc'] ?? 0);
        $accInfo   = $VM->get_acc_details_info($masterId,$compId);
        $hsn       = $accInfo['acc_sac'] ?? '';
    }

    $posCode   = sprintf('%02d', $pos);
    $taxDet    = $row['tax_details'] ?? [];

    // Effective rates
    $igstEff  = parseAmount($row['igst_rate'] ?? 0);
    $cgstEff  = $igstEff / 2;
    $sgstEff  = $igstEff / 2;
    $utgstEff = $igstEff / 2;
    $cessEff  = isset($taxDet['cess']) ? parseAmount($taxDet['cess']) : 0;

    // Composition override
    if ((int)$gstinType === 2) {
        helper('composition');
        $comp     = get_composition_rates($cmpSupType);
        $igstEff  = $comp['igst'];
        $cgstEff  = $comp['cgst'];
        $sgstEff  = $comp['sgst'];
        $utgstEff = $comp['ut'];
        $cessEff  = $comp['cess'] ?? 0;
    }

    $ts = [
        'cmp_id'=>$compId,'vch_txn_id'=>$vtxn,'txn_id'=>$txnId,
        'acc_bsd_id'=>$masterId,'acc_bsd_type'=>$accBsdType,
        'vch_igst'=>0,'vch_igst_rate'=>0,'vch_cgst'=>0,'vch_cgst_rate'=>0,
        'vch_sgst_ugst'=>0,'vch_sgst_ugst_rate'=>0,'vch_cess'=>0,'vch_cess_rate'=>0,
        'vch_taxable_value'=>$amount,'vch_total_tax'=>0
    ];

    $rIgst = $rCgst = $rSgst = $rUtgst = $rCess = 0;

    // IGST (different states)
    if ($boStateCode != $posCode && !in_array($posCode, $ugstStates)) {
        $v = ($amount * $igstEff) / 100;
        $ts['vch_igst']=$v; $ts['vch_igst_rate']=$igstEff; $ts['vch_total_tax']=$v;
        $rIgst = $v;
    }
    // CGST + SGST (same state)
    elseif ($boStateCode == $posCode) {
        $c = ($amount * $cgstEff) / 100;
        $s = ($amount * $sgstEff) / 100;
        $ts['vch_cgst']=$c; $ts['vch_cgst_rate']=$cgstEff;
        $ts['vch_sgst_ugst']=$s; $ts['vch_sgst_ugst_rate']=$sgstEff;
        $ts['vch_total_tax']=$c+$s;
        $rCgst=$c; $rSgst=$s;
    }
    // UGST
    elseif (in_array($posCode, $ugstStates)) {
        $c = ($amount * $cgstEff) / 100;
        $u = ($amount * $utgstEff) / 100;
        $ts['vch_cgst']=$c; $ts['vch_cgst_rate']=$cgstEff;
        $ts['vch_sgst_ugst']=$u; $ts['vch_sgst_ugst_rate']=$utgstEff;
        $ts['vch_total_tax']=$c+$u;
        $rCgst=$c; $rUtgst=$u;
    }

    // CESS
    if ($cessEff > 0) {
        $cv = ($amount * $cessEff) / 100;
        $ts['vch_cess']=$cv; $ts['vch_cess_rate']=$cessEff;
        $ts['vch_total_tax']+=$cv; $rCess=$cv;
    }

    // GST tag
    $gstTag = in_array($invSupplyId, [1,2,3], true)
        ? ($gstinType===1?1:($gstinType===2?2:null))
        : ([16=>3,17=>4,20=>5,4=>6,5=>6,6=>6,7=>6,8=>7,9=>7,10=>7,11=>7,18=>8,19=>9][$invSupplyId] ?? 0);

    $ts['vch_date']      = $vDate;
    $ts['is_outward']    = 1;
    $ts['gst_rate_grp']  = $gstTag.','.parseAmount($igstEff).','.parseAmount($cessEff);
    $ts['vch_hsn_sac']   = $hsn;
    $ts['inv_supply_id'] = $invSupplyId;

    $gstSumId = $VM->add_taxsummary_data($ts, $taxFlag,$gstinType);

    // HSN summary
    $hsnQty = 0;
    $hsnUom = 0;
    if ($type === 'itm') {
        $hsnQty = $row['item_qty'] ?? 0;
        $hsnUom = $row['item_unit_id'] ?? 0;
    }

    $VM->add_hsnsummary_data([
        'cmp_id'=>$compId,'vch_txn_id'=>$vtxn,'txn_id'=>$txnId,
        'vch_hsn_sac'=>$hsn,'vch_hsn_sac_taxbl_val'=>$amount,
        'vch_hsn_sac_qty'=>$hsnQty,'vch_hsn_sac_uom'=>$hsnUom,
        'vch_hsn_sac_igst'=>$ts['vch_igst'],'vch_hsn_sac_cgst'=>$ts['vch_cgst'],
        'vch_hsn_sac_sgst_ugst'=>$ts['vch_sgst_ugst'],'vch_hsn_sac_cess'=>$ts['vch_cess'],
        'inv_supply_id'=>$invSupplyId,'vch_gst_sum_id'=>$gstSumId
    ], $taxFlag,$gstinType);

    // FCY Tax Summary
    if (parseAmount($fcyRate) > 0) {
        $tf = [
            'cmp_id'=>$compId,'vch_txn_id'=>$vtxn,'txn_id'=>$txnId,
            'acc_bsd_id'=>$masterId,'acc_bsd_type'=>$accBsdType,
            'vch_igst_fcy'=>0,'vch_cgst_fcy'=>0,'vch_sgst_ugst_fcy'=>0,
            'vch_cess_fcy'=>0,'vch_taxable_value_fcy'=>$amountFcy,'vch_total_tax_fcy'=>0
        ];

        if ($boStateCode != $posCode && !in_array($posCode, $ugstStates)) {
            $v=($amountFcy*$igstEff)/100;
            $tf['vch_igst_fcy']=$v; $tf['vch_total_tax_fcy']=$v;
        } elseif ($boStateCode == $posCode) {
            $c=($amountFcy*$cgstEff)/100; $s=($amountFcy*$sgstEff)/100;
            $tf['vch_cgst_fcy']=$c; $tf['vch_sgst_ugst_fcy']=$s; $tf['vch_total_tax_fcy']=$c+$s;
        } elseif (in_array($posCode, $ugstStates)) {
            $c=($amountFcy*$cgstEff)/100; $u=($amountFcy*$utgstEff)/100;
            $tf['vch_sgst_ugst_fcy']=$u; $tf['vch_total_tax_fcy']=$c+$u;
        }

        if ($cessEff > 0) {
            $cv=($amountFcy*$cessEff)/100;
            $tf['vch_cess_fcy']=$cv; $tf['vch_total_tax_fcy']+=$cv;
        }

        $VM->add_taxsummary_fcy_data($tf);
    }

    return ['igst'=>$rIgst,'cgst'=>$rCgst,'sgst'=>$rSgst,'utgst'=>$rUtgst,'cess'=>$rCess];
}

/**
 * =====================================================================
 * PART 3: _resaveSaleWithItems method (COMPLETE)
 * Paste this into your App\Models\Api\AppCommonModel class
 * =====================================================================
 */

private function _resaveSaleWithItems(
    $db,$VM,$vtxn,$vchTypeId,$vDate,$vSer,$matId,$partyId,$pName,$compId,$boId,
    $boStateCode,$pos,$ugstStates,$gstinType,$cmpSupType,
    $fcyRate,$currId,$longNarr,$taxIncl,$memoChk,
    $bbbData,$prData,$bsdTxns,
    $ewbData,$ewbSupType,$ewbSubType,$einvChk,$tptInfo,
    $taxFlag,$apprAmt,$overSup,$billno,$revChg,$outsupEco,$fyId
) {
    $itmsdata  = $VM->grid_item_transactions($vtxn,$compId,$boId);
	
    $batchdata = $VM->get_itembatch_txn_data($vtxn,$compId,$boId);

    if (empty($itmsdata)) {
        throw new \RuntimeException('No item transactions found');
    }

    // Enrich items
    foreach ($itmsdata as &$item) {
        $itmTxn = $db->table('itemtxnmst')
            ->where('vch_txn_id', $vtxn)->where('cmp_id', $compId)
            ->where('txn_id', $item['txn_id'])->where('itm_txn_type', 1)
            ->get()->getRowArray();
        $item['item_total_amount']     = $item['item_amount'];
        $item['item_total_fcy_amount'] = parseAmount($itmTxn['itm_txn_fcy'] ?? 0);
        $item['memo_amount']  = $VM->get_memoamount_sale_txn($vtxn, $item['txn_id'], 'itm', $item['item_id'],$compId,$boId);
        $item['txinc_amount'] = $VM->get_taxamount_sale_txn($vtxn, 'itm', $item['txn_id'],$compId);

        $gstRow = $db->table('vchgstsumn')
            ->where('vch_txn_id', $vtxn)->where('txn_id', $item['txn_id'])
            ->where('acc_bsd_id', $item['item_id'])->where('cmp_id', $compId)
            ->where('acc_bsd_type', 3)->get()->getRowArray();
        $item['supply_type_id'] = (int)($gstRow['inv_supply_id'] ?? ($overSup ?? 1));
    }
    unset($item);

    $billsndry = $this->_enrichBsdData($bsdTxns, $vtxn, $compId, $db, $VM);
    $bbbFlat   = $this->_flattenBbbData($bbbData);
    $prFlat    = $this->_flattenPrData($prData);
    $batchFlat = $this->_flattenBatchData($batchdata);

    // Totals
    $saleT    = array_sum(array_column($itmsdata, 'item_total_amount'));
    $saleFcy  = array_sum(array_column($itmsdata, 'item_total_fcy_amount'));
    $bsdT     = array_sum(array_column($billsndry, 'billsundry_amount'));
    $bsdFcy   = array_sum(array_column($billsndry, 'billsundry_fcy_amount'));
    $saleMemo = array_sum(array_column($itmsdata, 'memo_amount'));
    $bsdMemo  = array_sum(array_column($billsndry, 'memo_amount'));

    $totalTax = $totalFcyTax = 0;
    if ($taxFlag == 1) {
        $totalTax    += $VM->GetTotalTax($itmsdata, 'itm')['tax_amount'];
        $totalFcyTax += $VM->GetTotalTax($itmsdata, 'itm')['fcy_tax_amount'];
        $totalTax    += $VM->GetTotalTax($billsndry, 'bsd')['tax_amount'];
        $totalFcyTax += $VM->GetTotalTax($billsndry, 'bsd')['fcy_tax_amount'];
    }

    // CLEAR OLD DATA
    $this->_clearVoucherData($VM, $vtxn, $vchTypeId,$compId,$boId);

    // ---- Composition ----
    $compVtxn = 0;
    $compSeriesId = $vSer;

    if ($gstinType == 2) {
        $totalTax    += $VM->GetTotalTax($itmsdata, 'itm', $gstinType, $cmpSupType)['tax_amount'];
        $totalFcyTax += $VM->GetTotalTax($itmsdata, 'itm', $gstinType, $cmpSupType)['fcy_tax_amount'];
        $totalTax    += $VM->GetTotalTax($billsndry, 'bsd', $gstinType, $cmpSupType)['tax_amount'];
        $totalFcyTax += $VM->GetTotalTax($billsndry, 'bsd', $gstinType, $cmpSupType)['fcy_tax_amount'];

        $bridge = $VM->get_voucher_bridge_info($vtxn, 4,$compId);
        if ($bridge) {
            $compVtxn     = $bridge['vch_txn_id_dest'];
            $gstPaidId    = $VM->getGstPaidAccountId($compId);
            $compTypeId   = 23;
            $compSeriesId = $VM->getSeriesId($compTypeId,$compId);

            $VM->update_comps_voucher_cons_data([
                "vch_series_id" => $compSeriesId, "vch_type_id" => $compTypeId,
                "vch_sub_type_id" => 0, "vch_date" => $vDate,
                "mat_cent_id" => $matId, "draft_vch_rec_id" => 0
            ], $compVtxn, $compId, $compTypeId);

            $cMain = $VM->add_comp_txn_data([
                "cmp_id" => $compId, "vch_series_id" => $compSeriesId,
                "vch_txn_id" => $compVtxn, "master_id" => $gstPaidId, 'master_id_type' => 'acc'
            ]);
            $VM->add_comp_txn_data([
                "cmp_id" => $compId, "vch_series_id" => $compSeriesId,
                "vch_txn_id" => $compVtxn, "master_id" => 0, 'master_id_type' => 'nrr'
            ]);
            $VM->save_voucher_narration($compVtxn, $cMain, 'long', $longNarr,$compId);
            $VM->add_acc_txn_data([
                'cmp_id' => $compId, 'acc_id' => $gstPaidId, 'acc_txn_date' => $vDate,
                'acc_txn_dr_cr' => 1, 'acc_txn_amt' => $totalTax, 'acc_txn_fcy' => $totalFcyTax,
                'vch_txn_id' => $compVtxn, 'txn_id' => $cMain,
                'hobo_id' => $boId, 'acc_txn_type' => 1
            ]);
            $VM->add_register_txn_data([
                'acct_vch_type' => $compTypeId, 'cmp_id' => $compId,
                'vch_txn_id' => $compVtxn, 'txn_id' => NULL, 'vch_date' => $vDate,
                'acc_id' => $gstPaidId, 'acc_txn_dr_amt' => $totalTax, 'acc_txn_cr_amt' => 0,
                'vch_narr' => $longNarr ?? '', 'hobo_id' => $boId, 'acc_txn_type' => 1
            ]);
        }
    }

    // ---- Update conso ----
    $VM->update_voucher_cons_data([
        "vch_series_id" => $vSer, "vch_date" => $vDate,
        "draft_vch_rec_id" => 0, "mat_cent_id" => $matId
    ], $vtxn, $compId,$boId);

    if ($gstinType == 2 && $compVtxn > 0) {
        $VM->update_voucher_bridge_data(["vch_txn_id_src" => $vtxn], $compVtxn, 4,$compId);
    }

    // ---- FCY ----
    $this->_saveFcyRate($VM, $vtxn, $compId, $fcyRate, $currId);

    // ---- GST Outward Supply ----
    $VM->update_gstroutsup_data($vtxn, [
        "outsup_pos" => $pos, "outsup_bill_ref_no" => $billno,
        "outsup_rev_chg" => $revChg, "dr_note_inwsup_id" => 0,
        "outsup_eco" => $outsupEco, "gst_supply_type" => (int)$cmpSupType
    ],$compId);

    // ---- EWB + E-Invoice ----
    $this->_saveEwbEinvoice($VM, $vtxn, $compId, $boId, $vDate,
        $ewbData, $ewbSupType, $ewbSubType, $einvChk, $tptInfo);

    // ---- Invoice value ----
    $invVal  = $saleT + $bsdT + $totalTax;
    $invFcy  = $saleFcy + $bsdFcy + $totalFcyTax;
    $memoVal = $saleMemo + $bsdMemo;
    $memoFcy = (parseAmount($fcyRate) > 0) ? $memoVal * $fcyRate : 0;

    // ---- Approval ----
    $accTxnType = 1;
    if ($apprAmt != '' && parseAmount($invVal) > parseAmount($apprAmt)) {
        $accTxnType = 4;
    }

    // ---- Party Account Entry ----
    $mainTxn = $VM->add_comp_txn_data([
        "cmp_id" => $compId, "vch_series_id" => $vSer,
        "vch_txn_id" => $vtxn, "master_id" => $partyId, 'master_id_type' => 'acc'
    ]);

    $VM->add_acc_txn_data([
        'cmp_id' => $compId, 'acc_id' => $partyId, 'acc_txn_date' => $vDate,
        'acc_txn_dr_cr' => 1, 'acc_txn_amt' => $invVal, 'acc_txn_fcy' => $invFcy,
        'vch_txn_id' => $vtxn, 'txn_id' => $mainTxn,
        'hobo_id' => $boId, 'acc_txn_type' => $accTxnType
    ]);

    // ---- Party Memo ----
    if ($memoVal > 0) {
        $VM->add_acc_txn_data([
            'cmp_id' => $compId, 'acc_id' => $partyId, 'acc_txn_date' => $vDate,
            'acc_txn_dr_cr' => 1, 'acc_txn_amt' => $memoVal, 'acc_txn_fcy' => $memoFcy,
            'vch_txn_id' => $vtxn, 'txn_id' => $mainTxn,
            'hobo_id' => $boId, 'acc_txn_type' => 3
        ]);
    }

    // ---- Party Register ----
    $VM->add_register_txn_data([
        'acct_vch_type' => $vchTypeId, 'cmp_id' => $compId, 'vch_txn_id' => $vtxn,
        'txn_id' => NULL, 'vch_date' => $vDate, 'acc_id' => $partyId,
        'acc_txn_dr_amt' => $invVal, 'acc_txn_cr_amt' => 0,
        'vch_narr' => $longNarr ?? '', 'hobo_id' => $boId, 'acc_txn_type' => $accTxnType
    ]);

    // ---- Item Wise Entries ----
    $tI = $tC = $tS = $tU = $tCe = 0;
    $accArr = [];
    $accFcyArr = [];

    foreach ($itmsdata as $ir) {
        $supId = $ir['supply_type_id'] ?? 1;

        $iTxn = $VM->add_comp_txn_data([
            "cmp_id" => $compId, "vch_series_id" => $vSer,
            "vch_txn_id" => $vtxn, "master_id" => $ir['item_id'], 'master_id_type' => 'itm'
        ]);

        // Memo item entry
        if ($memoChk == "1" && isset($ir['memo_amount']) && $ir['memo_amount'] > 0) {
            $VM->add_itm_txn_data([
                'cmp_id' => $compId,
                'itm_id_unit_id' => $ir['item_id'] . '_' . $ir['item_unit_id'],
                'itm_txn_qty' => $ir['item_qty'], 'itm_txn_date' => $vDate,
                'itm_txn_dr_cr' => 2, 'itm_txn_rate' => 1,
                'itm_txn_amt' => parseAmount($ir['memo_amount']), 'itm_txn_fcy' => 0,
                'vch_txn_id' => $vtxn, 'txn_id' => $iTxn,
                'mat_cent_id' => $matId, 'hobo_id' => $boId, 'itm_txn_type' => 3
            ]);
        }

        // Main item entry
        if (isset($ir['item_qty']) && $ir['item_qty'] > 0) {
            $VM->add_itm_txn_data([
                'cmp_id' => $compId,
                'itm_id_unit_id' => $ir['item_id'] . '_' . $ir['item_unit_id'],
                'itm_txn_qty' => $ir['item_qty'], 'itm_txn_date' => $vDate,
                'itm_txn_dr_cr' => 2,
                'itm_txn_rate' => parseAmountPrice($ir['item_price'], 4),
                'itm_txn_amt' => parseAmount($ir['item_total_amount']),
                'itm_txn_fcy' => parseAmount($ir['item_total_fcy_amount']),
                'vch_txn_id' => $vtxn, 'txn_id' => $iTxn,
                'mat_cent_id' => $matId, 'hobo_id' => $boId, 'itm_txn_type' => 1
            ]);

            // Short narration
            $VM->save_voucher_narration($vtxn, $iTxn, 'short', $ir['description'] ?? '',$compId);

            // Tax inclusive
            if ($taxIncl && isset($ir['txinc_amount']) && $ir['txinc_amount'] > 0) {
                $VM->add_taxinc_txn_data([
                    'cmp_id' => $compId, 'acc_itm_id' => $ir['item_id'],
                    'acc_txn_inc_amt' => $ir['txinc_amount'], 'vch_txn_id' => $vtxn,
                    'txn_id' => $iTxn, 'acc_txn_dr_cr' => 2
                ]);
            }

            // Item register
            $regId = $VM->add_item_register_txn_data([
                'itm_vch_type' => $vchTypeId, 'cmp_id' => $compId,
                'vch_txn_id' => $vtxn, 'txn_id' => $iTxn, 'vch_date' => $vDate,
                'itm_id_unit_id' => $ir['item_id'] . '_' . $ir['item_unit_id'],
                'vch_narr' => $longNarr ?? '', 'hobo_id' => $boId, 'itm_txn_type' => 1
            ]);

            // Item sub register
            $VM->add_item_sub_register_txn_data([
                'itm_vch_reg_id' => $regId, 'mat_cent_id' => $matId,
                'itm_txn_dr_qty' => 0, 'itm_txn_dr_rate' => 0, 'itm_txn_dr_amt' => 0,
                'itm_txn_cr_qty' => $ir['item_qty'],
                'itm_txn_cr_rate' => parseAmountPrice($ir['item_price'], 4),
                'itm_txn_cr_amt' => parseAmount($ir['item_total_amount']),
                'vch_narr' => $longNarr ?? ''
            ]);

            // Aggregate sales accounts
            $salesAccId = $ir['item_sales_acc'];
            $accArr[$salesAccId]    = ($accArr[$salesAccId] ?? 0) + parseAmount($ir['item_total_amount']);
            $accFcyArr[$salesAccId] = ($accFcyArr[$salesAccId] ?? 0) + parseAmount($ir['item_total_fcy_amount']);

            // Tax summary
            $taxRes = $this->_calculateAndInsertTaxSummary(
                $ir, $vtxn, $iTxn, $ir['item_id'], 3,
                $boStateCode, $pos, $ugstStates, $gstinType, $cmpSupType,
                $compId, $vDate, $VM, $fcyRate, $taxFlag, $supId, 'itm'
            );
            $tI  += $taxRes['igst'];
            $tC  += $taxRes['cgst'];
            $tS  += $taxRes['sgst'];
            $tU  += $taxRes['utgst'];
            $tCe += $taxRes['cess'];
        }
    }

    // ---- Sales Account Entries ----
    foreach ($accArr as $accId => $amt) {
        $amtFcy = $accFcyArr[$accId] ?? 0;
        $sTxn = $VM->add_comp_txn_data([
            "cmp_id" => $compId, "vch_series_id" => $vSer,
            "vch_txn_id" => $vtxn, "master_id" => $accId, 'master_id_type' => 'acc'
        ]);
        $VM->add_acc_txn_data([
            'cmp_id' => $compId, 'acc_id' => $accId, 'acc_txn_date' => $vDate,
            'acc_txn_dr_cr' => 2, 'acc_txn_amt' => $amt, 'acc_txn_fcy' => $amtFcy,
            'vch_txn_id' => $vtxn, 'txn_id' => $sTxn,
            'hobo_id' => $boId, 'acc_txn_type' => 1
        ]);
    }
    // ---- Bill Sundry Entries ----
    $this->_processBillSundryEntries(
        $billsndry, $vtxn, $vSer, $vDate, $compId, $boId,
        $boStateCode, $pos, $ugstStates, $fcyRate, $memoChk, $VM, $taxFlag,$gstinType,
        $tI, $tC, $tS, $tU, $tCe
    );

    // ---- Tax Account Entries ----
    $this->_saveTaxAccountEntries(
        $gstinType, $vtxn, $vSer,
        $tI, $tC, $tS, $tU, $tCe,
        $vDate, $VM, $compVtxn, $compSeriesId,$compId, $boId
    );

    // ---- Narration ----
    $nTxn = $VM->add_comp_txn_data([
        "cmp_id" => $compId, "vch_series_id" => $vSer,
        "vch_txn_id" => $vtxn, "master_id" => 0, 'master_id_type' => 'nrr'
    ]);
    $VM->save_voucher_narration($vtxn, $nTxn, 'long', $longNarr,$compId);

    // ---- BBB / PR / Batch ----
    if (!empty($bbbFlat)) {
        $VM->SaveBillByBillData($bbbFlat, $vDate, $vtxn, $mainTxn,$compId,$boId,$fyId);
    }
    if (!empty($prFlat)) {
        $VM->SaveProjectReportingData($prFlat, $vDate, $vtxn, $mainTxn,$compId,$boId,$fyId);
    }
    if (!empty($batchFlat)) {
        $VM->SaveBatchData($batchFlat, $vDate, $vtxn, $mainTxn, $matId,$compId,$boId,$fyId);
    }

    // ---- Activity Log ----
   // $VM->SaveUserActivity("Sale with item voucher re-saved via API", $vtxn);
}

/**
 * =====================================================================
 * PART 4: _resaveSaleWithoutItems method (COMPLETE)
 * Paste this into your App\Models\Api\AppCommonModel class
 * =====================================================================
 */

private function _resaveSaleWithoutItems(
    $db,$VM,$vtxn,$vchTypeId,$vDate,$vSer,$matId,$partyId,$pName,$compId,$boId,
    $boStateCode,$pos,$ugstStates,$gstinType,$cmpSupType,
    $fcyRate,$currId,$longNarr,$taxIncl,$memoChk,
    $bbbData,$prData,$bsdTxns,
    $ewbData,$ewbSupType,$ewbSubType,$einvChk,$tptInfo,
    $taxFlag,$apprAmt,$overSup,$billno,$revChg,$outsupEco,$fyId
) {
    $accountTxns = $VM->grid_account_transactions($vtxn,$compId,$boId);

    if (empty($accountTxns)) {
        throw new \RuntimeException('No account transactions found for non-item voucher');
    }

    // Map to itmsdata format
    $itmsdata = [];
    foreach ($accountTxns as $row) {
        $itmsdata[] = [
            'account_id'     => $row['account_id'],
            'amount'         => $row['amount'],
            'amountfc'       => $row['amountfc'],
            'description'    => $row['description'],
            'igst_rate'      => $row['igst_rate'],
            'tax_cat_id'     => $row['tax_cat_id'],
            'tax_details'    => $row['tax_details'],
            'memo_amount'    => $row['memo_amount'] ?? 0,
            'txinc_amount'   => $row['txinc_amount'] ?? 0,
            'supply_type_id' => $this->_getAccountSupplyTypeId($db, $vtxn, $row['account_id'], $compId, $overSup),
        ];
    }

    $billsndry = $this->_enrichBsdData($bsdTxns, $vtxn, $compId, $db, $VM);
    $bbbFlat   = $this->_flattenBbbData($bbbData);
    $prFlat    = $this->_flattenPrData($prData);

    // Totals
    $saleT    = array_sum(array_column($itmsdata, 'amount'));
    $saleFcy  = array_sum(array_column($itmsdata, 'amountfc'));
    $bsdT     = array_sum(array_column($billsndry, 'billsundry_amount'));
    $bsdFcy   = array_sum(array_column($billsndry, 'billsundry_fcy_amount'));
    $saleMemo = array_sum(array_column($itmsdata, 'memo_amount'));
    $bsdMemo  = array_sum(array_column($billsndry, 'memo_amount'));

    $totalTax = $totalFcyTax = 0;
    if ($taxFlag == 1) {
        $totalTax    += $VM->GetTotalTax($itmsdata, 'acc')['tax_amount'];
        $totalFcyTax += $VM->GetTotalTax($itmsdata, 'acc')['fcy_tax_amount'];
        $totalTax    += $VM->GetTotalTax($billsndry, 'bsd')['tax_amount'];
        $totalFcyTax += $VM->GetTotalTax($billsndry, 'bsd')['fcy_tax_amount'];
    }

    // CLEAR OLD DATA
    $this->_clearVoucherData($VM, $vtxn, $vchTypeId,$compId,$boId);

    // Invoice value
    $invVal  = $saleT + $bsdT + $totalTax;
    $invFcy  = $saleFcy + $bsdFcy + $totalFcyTax;
    $memoVal = $saleMemo + $bsdMemo;
    $memoFcy = (parseAmount($fcyRate) > 0) ? $memoVal * $fcyRate : 0;

    // ---- Composition ----
    $compVtxn = 0;
    $compSeriesId = $vSer;

    if ($gstinType == 2) {
        $totalTax    += $VM->GetTotalTax($itmsdata, 'acc', $gstinType, $cmpSupType)['tax_amount'];
        $totalFcyTax += $VM->GetTotalTax($itmsdata, 'acc', $gstinType, $cmpSupType)['fcy_tax_amount'];
        $totalTax    += $VM->GetTotalTax($billsndry, 'bsd', $gstinType, $cmpSupType)['tax_amount'];
        $totalFcyTax += $VM->GetTotalTax($billsndry, 'bsd', $gstinType, $cmpSupType)['fcy_tax_amount'];

        $bridge = $VM->get_voucher_bridge_info($vtxn, 4,$compId);
        if ($bridge) {
            $compVtxn     = $bridge['vch_txn_id_dest'];
            $gstPaidId    = $VM->getGstPaidAccountId($compId);
            $compTypeId   = 23;
            $compSeriesId = $VM->getSeriesId($compTypeId,$compId);

            $VM->update_comps_voucher_cons_data([
                "vch_series_id" => $compSeriesId, "vch_type_id" => $compTypeId,
                "vch_sub_type_id" => 0, "vch_date" => $vDate,
                "mat_cent_id" => 0, "draft_vch_rec_id" => 0
            ], $compVtxn, $compId, $compTypeId,$boId);

            $cMain = $VM->add_comp_txn_data([
                "cmp_id" => $compId, "vch_series_id" => $compSeriesId,
                "vch_txn_id" => $compVtxn, "master_id" => $gstPaidId, 'master_id_type' => 'acc'
            ]);
            $VM->add_comp_txn_data([
                "cmp_id" => $compId, "vch_series_id" => $compSeriesId,
                "vch_txn_id" => $compVtxn, "master_id" => 0, 'master_id_type' => 'nrr'
            ]);
            $VM->save_voucher_narration($compVtxn, $cMain, 'long', $longNarr,$compId);
            $VM->add_acc_txn_data([
                'cmp_id' => $compId, 'acc_id' => $gstPaidId, 'acc_txn_date' => $vDate,
                'acc_txn_dr_cr' => 1, 'acc_txn_amt' => $totalTax, 'acc_txn_fcy' => $totalFcyTax,
                'vch_txn_id' => $compVtxn, 'txn_id' => $cMain,
                'hobo_id' => $boId, 'acc_txn_type' => 1
            ]);
            $VM->add_register_txn_data([
                'acct_vch_type' => $compTypeId, 'cmp_id' => $compId,
                'vch_txn_id' => $compVtxn, 'txn_id' => NULL, 'vch_date' => $vDate,
                'acc_id' => $gstPaidId, 'acc_txn_dr_amt' => $totalTax, 'acc_txn_cr_amt' => 0,
                'vch_narr' => $longNarr ?? '', 'hobo_id' => $boId, 'acc_txn_type' => 1
            ]);
        }
    }

    // ---- Update conso ----
    $VM->update_voucher_cons_data([
        "vch_series_id" => $vSer, "vch_date" => $vDate, "draft_vch_rec_id" => 0
    ], $vtxn, $compId,$boId);

    if ($gstinType == 2 && $compVtxn > 0) {
        $VM->update_voucher_bridge_data(["vch_txn_id_src" => $vtxn], $compVtxn, 4,$compId);
    }

    // ---- FCY ----
    $this->_saveFcyRate($VM, $vtxn, $compId, $fcyRate, $currId);

    // ---- GST Outward Supply ----
    $VM->update_gstroutsup_data($vtxn, [
        "outsup_pos" => $pos, "outsup_bill_ref_no" => $billno,
        "outsup_rev_chg" => $revChg, "dr_note_inwsup_id" => 0,
        "outsup_eco" => $outsupEco, "gst_supply_type" => (int)$cmpSupType
    ],$compId);

    // ---- EWB + E-Invoice ----
    $this->_saveEwbEinvoice($VM, $vtxn, $compId, $boId, $vDate,
        $ewbData, $ewbSupType, $ewbSubType, $einvChk, $tptInfo);

    // ---- Approval ----
    $accTxnType = 1;
    if ($apprAmt != '' && parseAmount($invVal) > parseAmount($apprAmt)) {
        $accTxnType = 4;
    }

    // ---- Party Account Entry ----
    $mainTxn = $VM->add_comp_txn_data([
        "cmp_id" => $compId, "vch_series_id" => $vSer,
        "vch_txn_id" => $vtxn, "master_id" => $partyId, 'master_id_type' => 'acc'
    ]);

    $VM->add_acc_txn_data([
        'cmp_id' => $compId, 'acc_id' => $partyId, 'acc_txn_date' => $vDate,
        'acc_txn_dr_cr' => 1, 'acc_txn_amt' => $invVal, 'acc_txn_fcy' => $invFcy,
        'vch_txn_id' => $vtxn, 'txn_id' => $mainTxn,
        'hobo_id' => $boId, 'acc_txn_type' => $accTxnType
    ]);

    // ---- Memo ----
    if ($memoVal > 0) {
        $VM->add_acc_txn_data([
            'cmp_id' => $compId, 'acc_id' => $partyId, 'acc_txn_date' => $vDate,
            'acc_txn_dr_cr' => 1, 'acc_txn_amt' => $memoVal, 'acc_txn_fcy' => $memoFcy,
            'vch_txn_id' => $vtxn, 'txn_id' => $mainTxn,
            'hobo_id' => $boId, 'acc_txn_type' => 3
        ]);
    }

    // ---- Register ----
    $VM->add_register_txn_data([
        'acct_vch_type' => $vchTypeId, 'cmp_id' => $compId, 'vch_txn_id' => $vtxn,
        'txn_id' => NULL, 'vch_date' => $vDate, 'acc_id' => $partyId,
        'acc_txn_dr_amt' => $invVal, 'acc_txn_cr_amt' => 0,
        'vch_narr' => $longNarr ?? '', 'hobo_id' => $boId, 'acc_txn_type' => $accTxnType
    ]);

    // ---- Account Wise Grid Entries ----
    $tI = $tC = $tS = $tU = $tCe = 0;

    foreach ($itmsdata as $ir) {
        $supId = $ir['supply_type_id'] ?? 1;

        $txnId = $VM->add_comp_txn_data([
            "cmp_id" => $compId, "vch_series_id" => $vSer,
            "vch_txn_id" => $vtxn, "master_id" => $ir['account_id'], 'master_id_type' => 'acc'
        ]);

        // Memo
        if ($memoChk == "1" && isset($ir['memo_amount']) && $ir['memo_amount'] > 0) {
            $VM->add_acc_txn_data([
                'cmp_id' => $compId, 'acc_id' => $ir['account_id'], 'acc_txn_date' => $vDate,
                'acc_txn_dr_cr' => 2, 'acc_txn_amt' => $ir['memo_amount'],
                'acc_txn_fcy' => (parseAmount($fcyRate) > 0) ? ($ir['memo_amount'] * $fcyRate) : 0,
                'vch_txn_id' => $vtxn, 'txn_id' => $txnId,
                'hobo_id' => $boId, 'acc_txn_type' => 3
            ]);
        }

        // Account txn
        $VM->add_acc_txn_data([
            'cmp_id' => $compId, 'acc_id' => $ir['account_id'], 'acc_txn_date' => $vDate,
            'acc_txn_dr_cr' => 2, 'acc_txn_amt' => $ir['amount'], 'acc_txn_fcy' => $ir['amountfc'],
            'vch_txn_id' => $vtxn, 'txn_id' => $txnId,
            'hobo_id' => $boId, 'acc_txn_type' => 1
        ]);

        // Register
        $VM->add_register_txn_data([
            'acct_vch_type' => $vchTypeId, 'cmp_id' => $compId, 'vch_txn_id' => $vtxn,
            'txn_id' => $txnId, 'vch_date' => $vDate, 'acc_id' => $ir['account_id'],
            'acc_txn_cr_amt' => $ir['amount'], 'acc_txn_dr_amt' => 0,
            'vch_narr' => $ir['description'], 'hobo_id' => $boId, 'acc_txn_type' => $accTxnType
        ]);

        // Short narration
        $VM->save_voucher_narration($vtxn, $txnId, 'short', $ir['description'],$compId);

        // Tax inclusive
        if ($taxIncl && isset($ir['txinc_amount']) && $ir['txinc_amount'] > 0) {
            $VM->add_taxinc_txn_data([
                'cmp_id' => $compId, 'acc_itm_id' => $ir['account_id'],
                'acc_txn_inc_amt' => $ir['txinc_amount'], 'vch_txn_id' => $vtxn,
                'txn_id' => $txnId, 'acc_txn_dr_cr' => 2
            ]);
        }

        // Tax summary
        $taxRes = $this->_calculateAndInsertTaxSummary(
            $ir, $vtxn, $txnId, $ir['account_id'], 1,
            $boStateCode, $pos, $ugstStates, $gstinType, $cmpSupType,
            $compId, $vDate, $VM, $fcyRate, $taxFlag, $supId, 'acc'
        );
        $tI  += $taxRes['igst'];
        $tC  += $taxRes['cgst'];
        $tS  += $taxRes['sgst'];
        $tU  += $taxRes['utgst'];
        $tCe += $taxRes['cess'];
    }

    // ---- Bill Sundry Entries ----
    $this->_processBillSundryEntries(
        $billsndry, $vtxn, $vSer, $vDate, $compId, $boId,
        $boStateCode, $pos, $ugstStates, $fcyRate, $memoChk, $VM, $taxFlag,$gstinType,
        $tI, $tC, $tS, $tU, $tCe
    );

    // ---- Tax Account Entries ----
    $this->_saveTaxAccountEntries(
        $gstinType, $vtxn, $vSer,
        $tI, $tC, $tS, $tU, $tCe,
        $vDate, $VM, $compVtxn, $compSeriesId,$compId, $boId
    );

    // ---- Narration ----
    $nTxn = $VM->add_comp_txn_data([
        "cmp_id" => $compId, "vch_series_id" => $vSer,
        "vch_txn_id" => $vtxn, "master_id" => 0, 'master_id_type' => 'nrr'
    ]);
    $VM->save_voucher_narration($vtxn, $nTxn, 'long', $longNarr,$compId);

    // ---- BBB / PR ----
    if (!empty($bbbFlat)) {
        $VM->SaveBillByBillData($bbbFlat, $vDate, $vtxn, $mainTxn,$compId,$boId,$fyId);
    }
    if (!empty($prFlat)) {
        $VM->SaveProjectReportingData($prFlat, $vDate, $vtxn, $mainTxn,$compId,$boId,$fyId);
    }

    // ---- Activity Log ----
    //$VM->SaveUserActivity("Sale w/o item voucher re-saved via API", $vtxn);
}

}