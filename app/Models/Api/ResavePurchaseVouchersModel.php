<?php
namespace App\Models\Api;

use CodeIgniter\Model;
use App\Libraries\externaldb;

class ResavePurchaseVouchersModel extends Model
{
    public function __construct()
    {
        parent::__construct();
        $this->externaldb    = new externaldb();
        $this->univaictly    = $this->externaldb->univaictly_db();
        $this->aicountly_db  = $this->externaldb->aicountly_db();
        $this->sispluuid_db  = $this->externaldb->sispluuid_db();
        $this->pg_univaictlydb = $this->externaldb->postgr_univaictlydb();
    }

    /**
     * Main entry point – resave purchase vouchers
     * If vch_txn_id is passed → resave only that one voucher
     * If vch_txn_id is null/0 → resave all vouchers in date range
     */
    public function ResaveVoucherStatus(int $compId, int $boId, int $fyId, array $input): string
    {
        $db = $this->db;
        $VM = new \App\Models\Admin\VouchersModel();
        $CM = new \App\Models\CommonModel();

        $startDate    = $input['start_date']   ?? '';
        $endDate      = $input['end_date']     ?? '';
        $voucherType  = $input['voucher_type'] ?? 'purchase';
        $uuid         = $input['uuid'] ?? 0;
        $profile_id   = $input['profile_id'] ?? 0;
        $singleVtxnId = isset($input['vch_txn_id']) ? (int)$input['vch_txn_id'] : 0;

        $voucher_type_id = 11; // Purchase

        // ── Single voucher mode: no date range needed ──
        if ($singleVtxnId > 0) {
            $startDate = '';
            $endDate   = '';
        } else {
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
            $builder->where('vch_txn_id', $singleVtxnId);
        } else {
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

           // try {
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
                $accInfo = $VM->account_detail_info($partyId, $compId, $fyId);
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

                // Validate party (vendor) state code
                $fullInfo = $VM->account_full_info($partyId, $compId, $fyId, $uuid);
                $vendorStateCode = 0;
                if ($fullInfo && !empty($fullInfo['address_info'])) {
                    $aCnt = $fullInfo['address_info']['contact_country'] ?? '';
                    $aSt  = $fullInfo['address_info']['contact_state'] ?? '';
                    if ($aCnt !== '' && $aSt !== '') {
                        $si = $CM->get_state_info($aCnt, $aSt);
                        if ($si) $vendorStateCode = sprintf('%02d', $si['state_code']);
                    }
                }
                if ($vendorStateCode == 0 || $vendorStateCode === '00') {
                    $errorList[] = [
                        'vch_txn_id'=>$vtxn, 'vch_date'=>$vDate,
                        'party_id'=>$partyId, 'party_name'=>$pName,
                        'error'=>'Vendor state code missing. Cannot determine IGST/CGST/SGST/UGST. Update party address.'
                    ];
                    $skippedCount++;
                    continue;
                }

                // Read existing data
                $inwsupInfo  = $VM->get_gstrinwsup_info($vtxn, $compId);
                $pos         = $inwsupInfo['inwsup_pos'] ?? $bo_state_code;
                $revChg      = $inwsupInfo['inwsup_rev_chg'] ?? 0;
                $billno      = $inwsupInfo['inwsup_bill_ref_no'] ?? '';
                $inwsupEco   = (int)($inwsupInfo['inwsup_eco'] ?? 0);

                $fcyInfo     = $VM->vchfcyrate_info($vtxn, $fyId, $compId);
                $fcyRate     = $fcyInfo['vch_fcy_rate'] ?? 0;
                $currId      = $fcyInfo['cmp_fcy_mst_id'] ?? 0;

                $longNarr    = $VM->get_voucher_long_narration($vtxn, $compId);
                $taxIncl     = $VM->check_sale_taxinc($vtxn, $compId);
                $memoChk     = $VM->check_sale_memoentry($vtxn, $compId);
                $bbbData     = $VM->get_bills_txn_data($vtxn, $compId, $boId);
                $prData      = $VM->get_pr_txn_data($vtxn, $compId, $boId);
                $ccData      = $VM->get_cc_txn_data($vtxn, $compId, $boId);
                $bsdTxns     = $VM->grid_bsd_transactions($vtxn, $compId, $boId);

                $taxFlag = 1;
                $etx = $db->table('vchgstsumn')
                    ->where('vch_txn_id', $vtxn)
                    ->where('cmp_id', $compId)
                    ->limit(1)->get()->getRowArray();
                if ($etx && ($etx['gst_rate_grp'] ?? '') === '0,0') $taxFlag = 0;

                $apprInfo = $VM->Voucher_TxnApproval($compId, $uuid, $profile_id);
                $apprAmt  = $apprInfo['approval_amt'];
                $overSup  = $VM->getVoucherOverrideSupplyTypeId($vtxn, $compId);

                $args = [
                    $db, $VM, $CM, $vtxn, $voucher_type_id, $vDate, $vSer, $matId,
                    $partyId, $pName, $compId, $boId,
                    $bo_state_code, $vendorStateCode, $pos, $ugst_states, $gstinType,
                    $fcyRate, $currId, $longNarr, $taxIncl, $memoChk,
                    $bbbData, $prData, $ccData, $bsdTxns,
                    $taxFlag, $apprAmt, $overSup, $billno, $revChg, $inwsupEco, $fyId, $uuid
                ];
				
                if ($vSubId == 5) {
                   $this->_resavePurchaseWithItems(...$args);
                } elseif ($vSubId == 0) {
                   $this->_resavePurchaseWithoutItems(...$args);
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

            /* } catch (\Throwable $e) {
                $errorList[] = [
                    'vch_txn_id'=>$vtxn, 'vch_date'=>$vDate,
                    'party_id'=>$partyId ?? 0, 'party_name'=>$pName ?? '',
                    'error'=>'Line '.$e->getLine().' '.$e->getFile().': '.$e->getMessage()
                ];
                $skippedCount++;
                log_message('error', 'ResavePurchaseVoucher vtxn='.$vtxn.': '.$e->getMessage());
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

    private function _clearPurchaseVoucherData($VM, $vtxn, $vchTypeId, $compId, $boId)
    {
        $tables = ['cmptxnmstn','itemtxnmst','accttxnmst','billtxnmst','cctxnmstnn',
            'prjtxnmstn','subacctxnm','vchfcyrate','vchgstfcyn',
            'vchgstsumn','acctamtinc','batchtxnmt'];
        foreach ($tables as $t) {
            $VM->clear_table_txn_data($vtxn, $t, $compId);
        }
        $VM->clear_table_narrations_data($vtxn, $compId);
        $VM->clear_register_txn_data($vtxn, $vchTypeId, $compId, $boId);
        $VM->clear_system_journal_txn_data($vtxn, 3, $compId); // 3 for purchase bridge
    }

    private function _saveFcyRate($VM, $vtxn, $compId, $fcyRate, $currId)
    {
        if (parseAmount($fcyRate) > 0) {
            $VM->save_voucher_fcyrate([
                "cmp_id"=>$compId, "vch_txn_id"=>$vtxn,
                "vch_fcy_rate"=>$fcyRate, "cmp_fcy_mst_id"=>$currId
            ]);
        } else {
            $VM->clear_voucher_fcyrate($vtxn);
        }
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

    private function _flattenCcData($ccData): array
    {
        $flat = [];
        if (!empty($ccData)) {
            foreach ($ccData as $group) {
                $accId = $group['acc_id'];
                foreach ($group['cc_txn_list'] as $row) {
                    $flat[] = [
                        'account_id'    => $accId,
                        'cc_id'         => $row['cc_id'] ?? 0,
                        'cc_txn_drcr'   => $row['cc_txn_dr_cr'] ?? 'D',
                        'cc_txn_amt'    => $row['cc_txn_amt'] ?? 0,
                        'cc_txn_amtfc'  => $row['cc_txn_fcy'] ?? 0,
                        'cc_txn_narr'   => $row['cc_txn_narr'] ?? '',
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
                        'drcr_type'          => 1, // Purchase = Debit (inward)
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

    /**
     * Purchase-specific tax calculation using vendor state code
     * Rule:
     *   IF (VENDOR_STATE == POS && POS == MY_STATE) → CGST+SGST or CGST+UTGST
     *   ELSE IF (VENDOR_STATE != POS && POS == MY_STATE) → IGST
     *   ELSE → fallback inter/intra
     */
    private function _calculatePurchaseTax(
        float $amount, float $igstRate,
        string $vendorStateCode, string $posCode, string $boStateCode,
        array $ugstStates
    ): array
    {
        $halfRate = $igstRate / 2;
        $result = [
            'igst'=>0, 'igst_rate'=>0,
            'cgst'=>0, 'cgst_rate'=>0,
            'sgst_ugst'=>0, 'sgst_ugst_rate'=>0,
            'total_tax'=>0
        ];

        if ($vendorStateCode && $posCode && $boStateCode) {
            if ($vendorStateCode == $posCode && $posCode == $boStateCode) {
                if (in_array($boStateCode, $ugstStates)) {
                    // CGST + UTGST
                    $c = ($amount * $halfRate) / 100;
                    $u = ($amount * $halfRate) / 100;
                    $result['cgst'] = $c; $result['cgst_rate'] = $halfRate;
                    $result['sgst_ugst'] = $u; $result['sgst_ugst_rate'] = $halfRate;
                    $result['total_tax'] = $c + $u;
                    $result['tax_type'] = 'utgst';
                } else {
                    // CGST + SGST
                    $c = ($amount * $halfRate) / 100;
                    $s = ($amount * $halfRate) / 100;
                    $result['cgst'] = $c; $result['cgst_rate'] = $halfRate;
                    $result['sgst_ugst'] = $s; $result['sgst_ugst_rate'] = $halfRate;
                    $result['total_tax'] = $c + $s;
                    $result['tax_type'] = 'sgst';
                }
            } elseif ($vendorStateCode != $posCode && $posCode == $boStateCode) {
                // IGST
                $v = ($amount * $igstRate) / 100;
                $result['igst'] = $v; $result['igst_rate'] = $igstRate;
                $result['total_tax'] = $v;
                $result['tax_type'] = 'igst';
            } else {
                // Fallback
                $result = $this->_fallbackTaxCalc($amount, $igstRate, $halfRate, $boStateCode, $posCode, $ugstStates);
            }
        } else {
            // No vendor/POS → fallback
            $result = $this->_fallbackTaxCalc($amount, $igstRate, $halfRate, $boStateCode, $posCode, $ugstStates);
        }

        return $result;
    }

    private function _fallbackTaxCalc($amount, $igstRate, $halfRate, $boStateCode, $posCode, $ugstStates): array
    {
        $result = [
            'igst'=>0, 'igst_rate'=>0,
            'cgst'=>0, 'cgst_rate'=>0,
            'sgst_ugst'=>0, 'sgst_ugst_rate'=>0,
            'total_tax'=>0, 'tax_type'=>''
        ];

        if ($boStateCode != $posCode) {
            $v = ($amount * $igstRate) / 100;
            $result['igst'] = $v; $result['igst_rate'] = $igstRate;
            $result['total_tax'] = $v;
            $result['tax_type'] = 'igst';
        } elseif (in_array($posCode, $ugstStates)) {
            $c = ($amount * $halfRate) / 100;
            $u = ($amount * $halfRate) / 100;
            $result['cgst'] = $c; $result['cgst_rate'] = $halfRate;
            $result['sgst_ugst'] = $u; $result['sgst_ugst_rate'] = $halfRate;
            $result['total_tax'] = $c + $u;
            $result['tax_type'] = 'utgst';
        } else {
            $c = ($amount * $halfRate) / 100;
            $s = ($amount * $halfRate) / 100;
            $result['cgst'] = $c; $result['cgst_rate'] = $halfRate;
            $result['sgst_ugst'] = $s; $result['sgst_ugst_rate'] = $halfRate;
            $result['total_tax'] = $c + $s;
            $result['tax_type'] = 'sgst';
        }

        return $result;
    }

    
    /**
     * Calculate and insert tax summary for purchase items/accounts
     * Uses vendor state code logic (different from sale)
     */
    private function _calculateAndInsertPurchaseTaxSummary(
        $row, $vtxn, $txnId, $masterId, $accBsdType,
        $boStateCode, $vendorStateCode, $pos, $ugstStates, $gstinType,
        $compId, $vDate, $VM, $fcyRate, $taxFlag, $invSupplyId, $type
    ): array
    {
        if ($type === 'itm') {
            $amount    = parseAmount($row['item_total_amount'] ?? $row['item_amount'] ?? 0);
            $amountFcy = parseAmount($row['item_total_fcy_amount'] ?? 0);
            $itemInfo  = $VM->get_item_details_info($masterId, $compId);
            $hsn       = $itemInfo['itm_hsn'] ?? '';
        } else {
            $amount    = parseAmount($row['amount'] ?? 0);
            $amountFcy = parseAmount($row['amountfc'] ?? 0);
            $accInfo   = $VM->get_acc_details_info($masterId, $compId);
            $hsn       = $accInfo['acc_sac'] ?? '';
        }

        $posCode   = sprintf('%02d', $pos);
        $taxDet    = $row['tax_details'] ?? [];
        $igstRate  = parseAmount($row['igst_rate'] ?? 0);
        $cessRate  = isset($taxDet['cess']) ? parseAmount($taxDet['cess']) : 0;

        // Calculate tax using vendor state code logic
        $taxCalc = $this->_calculatePurchaseTax(
            $amount, $igstRate, $vendorStateCode, $posCode, $boStateCode, $ugstStates
        );

        $ts = [
            'cmp_id'=>$compId, 'vch_txn_id'=>$vtxn, 'txn_id'=>$txnId,
            'acc_bsd_id'=>$masterId, 'acc_bsd_type'=>$accBsdType,
            'vch_igst'=>$taxCalc['igst'], 'vch_igst_rate'=>$taxCalc['igst_rate'],
            'vch_cgst'=>$taxCalc['cgst'], 'vch_cgst_rate'=>$taxCalc['cgst_rate'],
            'vch_sgst_ugst'=>$taxCalc['sgst_ugst'], 'vch_sgst_ugst_rate'=>$taxCalc['sgst_ugst_rate'],
            'vch_cess'=>0, 'vch_cess_rate'=>0,
            'vch_taxable_value'=>$amount,
            'vch_total_tax'=>$taxCalc['total_tax']
        ];

        $rIgst = $taxCalc['igst'];
        $rCgst = $taxCalc['cgst'];
        $rSgst = ($taxCalc['tax_type'] ?? '') === 'sgst' ? $taxCalc['sgst_ugst'] : 0;
        $rUtgst = ($taxCalc['tax_type'] ?? '') === 'utgst' ? $taxCalc['sgst_ugst'] : 0;
        $rCess = 0;

        // CESS
        if ($cessRate > 0) {
            $cv = ($amount * $cessRate) / 100;
            $ts['vch_cess'] = $cv;
            $ts['vch_cess_rate'] = $cessRate;
            $ts['vch_total_tax'] += $cv;
            $rCess = $cv;
        }

        // GST tag
        $gstTag = in_array($invSupplyId, [1,2,3], true)
            ? ($gstinType === 1 ? 1 : ($gstinType === 2 ? 2 : 1))
            : ([16=>3,17=>4,20=>5,4=>6,5=>6,6=>6,7=>6,8=>7,9=>7,10=>7,11=>7,18=>8,19=>9][$invSupplyId] ?? 0);

        $ts['vch_date']      = $vDate;
        $ts['is_outward']    = 2; // Purchase = inward
        $ts['gst_rate_grp']  = $gstTag.','.parseAmount($igstRate).','.parseAmount($cessRate);
        $ts['vch_hsn_sac']   = $hsn;
        $ts['inv_supply_id'] = $invSupplyId;

        $gstSumId = $VM->add_taxsummary_data($ts, $taxFlag, $gstinType);

        // HSN summary
        $hsnQty = 0;
        $hsnUom = 0;
        if ($type === 'itm') {
            $hsnQty = $row['item_qty'] ?? 0;
            $hsnUom = $row['item_unit_id'] ?? 0;
        }

        $VM->add_hsnsummary_data([
            'cmp_id'=>$compId, 'vch_txn_id'=>$vtxn, 'txn_id'=>$txnId,
            'vch_hsn_sac'=>$hsn, 'vch_hsn_sac_taxbl_val'=>$amount,
            'vch_hsn_sac_qty'=>$hsnQty, 'vch_hsn_sac_uom'=>$hsnUom,
            'vch_hsn_sac_igst'=>$ts['vch_igst'], 'vch_hsn_sac_cgst'=>$ts['vch_cgst'],
            'vch_hsn_sac_sgst_ugst'=>$ts['vch_sgst_ugst'], 'vch_hsn_sac_cess'=>$ts['vch_cess'],
            'inv_supply_id'=>$invSupplyId, 'vch_gst_sum_id'=>$gstSumId
        ], $taxFlag, $gstinType);

        // FCY Tax Summary
        if (parseAmount($fcyRate) > 0) {
            $fcyTax = $this->_calculatePurchaseTax(
                $amountFcy, $igstRate, $vendorStateCode, $posCode, $boStateCode, $ugstStates
            );

            $tf = [
                'cmp_id'=>$compId, 'vch_txn_id'=>$vtxn, 'txn_id'=>$txnId,
                'acc_bsd_id'=>$masterId, 'acc_bsd_type'=>$accBsdType,
                'vch_igst_fcy'=>$fcyTax['igst'],
                'vch_cgst_fcy'=>$fcyTax['cgst'],
                'vch_sgst_ugst_fcy'=>$fcyTax['sgst_ugst'],
                'vch_cess_fcy'=>0,
                'vch_taxable_value_fcy'=>$amountFcy,
                'vch_total_tax_fcy'=>$fcyTax['total_tax']
            ];

            if ($cessRate > 0) {
                $cv = ($amountFcy * $cessRate) / 100;
                $tf['vch_cess_fcy'] = $cv;
                $tf['vch_total_tax_fcy'] += $cv;
            }

            $VM->add_taxsummary_fcy_data($tf);
        }

        return ['igst'=>$rIgst, 'cgst'=>$rCgst, 'sgst'=>$rSgst, 'utgst'=>$rUtgst, 'cess'=>$rCess];
    }

    
/**
 * =====================================================================
 * PART 3: _resavePurchaseWithItems method (COMPLETE)
 * For ResavePurchaseVouchersModel class
 * =====================================================================
 */

private function _resavePurchaseWithItems(
    $db,$VM,$CM,$vtxn,$vchTypeId,$vDate,$vSer,$matId,$partyId,$pName,$compId,$boId,
    $boStateCode,$vendorStateCode,$pos,$ugstStates,$gstinType,
    $fcyRate,$currId,$longNarr,$taxIncl,$memoChk,
    $bbbData,$prData,$ccData,$bsdTxns,
    $taxFlag,$apprAmt,$overSup,$billno,$revChg,$outsupEco,$fyId,$uuid
) {
    $itmsdata  = $VM->grid_item_transactions($vtxn, $compId, $boId);
    $batchdata = $VM->get_itembatch_txn_data($vtxn, $compId, $boId);

    if (empty($itmsdata)) {
        throw new \RuntimeException('No item transactions found for purchase voucher vtxn=' . $vtxn);
    }

    // Enrich items with FCY, memo, tax-inclusive, supply_type_id
    foreach ($itmsdata as &$item) {
        $itmTxn = $db->table('itemtxnmst')
            ->where('vch_txn_id', $vtxn)
            ->where('cmp_id', $compId)
            ->where('txn_id', $item['txn_id'])
            ->where('itm_txn_type', 1)
            ->get()->getRowArray();

        $item['item_total_amount']     = $item['item_amount'];
        $item['item_total_fcy_amount'] = parseAmount($itmTxn['itm_txn_fcy'] ?? 0);
        $item['memo_amount']  = $VM->get_memoamount_sale_txn($vtxn, $item['txn_id'], 'itm', $item['item_id'], $compId, $boId);
        $item['txinc_amount'] = $VM->get_taxamount_sale_txn($vtxn, 'itm', $item['txn_id'], $compId);

        $gstRow = $db->table('vchgstsumn')
            ->where('vch_txn_id', $vtxn)
            ->where('txn_id', $item['txn_id'])
            ->where('acc_bsd_id', $item['item_id'])
            ->where('cmp_id', $compId)
            ->where('acc_bsd_type', 3)
            ->get()->getRowArray();

        $item['supply_type_id'] = (int)($gstRow['inv_supply_id'] ?? ($overSup ?? 1));
    }
    unset($item);

    $billsndry = $this->_enrichBsdData($bsdTxns, $vtxn, $compId, $db, $VM);
    $bbbFlat   = $this->_flattenBbbData($bbbData);
    $prFlat    = $this->_flattenPrData($prData);
    $ccFlat    = $this->_flattenCcData($ccData);
    $batchFlat = $this->_flattenBatchData($batchdata);

    // ---- Totals ----
    $purchaseTotal    = array_sum(array_column($itmsdata, 'item_total_amount'));
    $purchaseFcyTotal = array_sum(array_column($itmsdata, 'item_total_fcy_amount'));
    $bsdTotal         = array_sum(array_column($billsndry, 'billsundry_amount'));
    $bsdFcyTotal      = array_sum(array_column($billsndry, 'billsundry_fcy_amount'));
    $purchaseMemo     = array_sum(array_column($itmsdata, 'memo_amount'));
    $bsdMemo          = array_sum(array_column($billsndry, 'memo_amount'));

    $totalTax = $totalFcyTax = 0;
    if ($taxFlag == 1) {
        $totalTax    += $VM->GetTotalTax($itmsdata, 'itm')['tax_amount'];
        $totalFcyTax += $VM->GetTotalTax($itmsdata, 'itm')['fcy_tax_amount'];
        $totalTax    += $VM->GetTotalTax($billsndry, 'bsd')['tax_amount'];
        $totalFcyTax += $VM->GetTotalTax($billsndry, 'bsd')['fcy_tax_amount'];
    }

    // ---- CLEAR OLD DATA ----
    $this->_clearVoucherData($VM, $vtxn, $vchTypeId, $compId, $boId);

    // ---- Invoice values ----
    $invoiceValue    = $purchaseTotal + $bsdTotal + $totalTax;
    $invoiceFcyValue = $purchaseFcyTotal + $bsdFcyTotal + $totalFcyTax;
    $memoValue       = $purchaseMemo + $bsdMemo;
    $memoFcyValue    = (parseAmount($fcyRate) > 0) ? $memoValue * $fcyRate : 0;

    // ---- Composition (gstinType == 2) ----
    $compVtxn     = 0;
    $compSeriesId = $vSer;

    if ($gstinType == 2) {
        // Bridge type 3 for purchase composition
        $bridge = $VM->get_voucher_bridge_info($vtxn, 3, $compId);
        if ($bridge) {
            $compVtxn      = $bridge['vch_txn_id_dest'];
            $gstPaidAccId  = $VM->getGstPaidAccountId($compId);
            $compVchTypeId = 23;
            $compSeriesId  = $VM->getSeriesId($compVchTypeId, $compId);

            $VM->update_comps_voucher_cons_data([
                "vch_series_id"    => $compSeriesId,
                "vch_type_id"      => $compVchTypeId,
                "vch_sub_type_id"  => 0,
                "vch_date"         => $vDate,
                "mat_cent_id"      => $matId,
                "draft_vch_rec_id" => 0
            ], $compVtxn, $compId, $compVchTypeId, $boId);

            $compMainTxn = $VM->add_comp_txn_data([
                "cmp_id" => $compId, "vch_series_id" => $compSeriesId,
                "vch_txn_id" => $compVtxn, "master_id" => $gstPaidAccId,
                'master_id_type' => 'acc'
            ]);

            $VM->add_comp_txn_data([
                "cmp_id" => $compId, "vch_series_id" => $compSeriesId,
                "vch_txn_id" => $compVtxn, "master_id" => 0,
                'master_id_type' => 'nrr'
            ]);

            $VM->save_voucher_narration($compVtxn, $compMainTxn, 'long', $longNarr, $compId);

            $VM->add_acc_txn_data([
                'cmp_id' => $compId, 'acc_id' => $gstPaidAccId, 'acc_txn_date' => $vDate,
                'acc_txn_dr_cr' => 1, 'acc_txn_amt' => $totalTax, 'acc_txn_fcy' => $totalFcyTax,
                'vch_txn_id' => $compVtxn, 'txn_id' => $compMainTxn,
                'hobo_id' => $boId, 'acc_txn_type' => 1
            ]);

            $VM->add_register_txn_data([
                'acct_vch_type' => $compVchTypeId, 'cmp_id' => $compId,
                'vch_txn_id' => $compVtxn, 'txn_id' => NULL, 'vch_date' => $vDate,
                'acc_id' => $gstPaidAccId,
                'acc_txn_dr_amt' => $totalTax, 'acc_txn_cr_amt' => 0,
                'vch_narr' => $longNarr ?? '', 'hobo_id' => $boId, 'acc_txn_type' => 1
            ]);
        }
    }

    // ---- Update vchtxnconso ----
    $VM->update_voucher_cons_data([
        "vch_series_id"    => $vSer,
        "vch_date"         => $vDate,
        "draft_vch_rec_id" => 0,
        "mat_cent_id"      => $matId
    ], $vtxn, $compId, $boId);

    // ---- FCY rate ----
    $this->_saveFcyRate($VM, $vtxn, $compId, $fcyRate, $currId);

    // ---- GST Inward Supply (gstrinwsup) ----
    $inwSupData = [
        "inwsup_pos"                => $pos,
        "inwsup_bill_ref_no"        => $billno,
        "inwsup_rev_chg"            => (int)$revChg,
        "inwsup_cr_note_outsup_id"  => 0,
        "inwsup_eco"                => (int)$outsupEco
    ];
    $VM->update_gstrinwsup_data((int)$vtxn, $inwSupData, $compId);

    // ---- Approval ----
    $accTxnType = 1;
    if ($apprAmt != '' && parseAmount($invoiceValue) > parseAmount($apprAmt)) {
        $accTxnType = 4;
    }

    // ---- Party Account Entry (Credit for purchase) ----
    $mainTxnId = $VM->add_comp_txn_data([
        "cmp_id" => $compId, "vch_series_id" => $vSer,
        "vch_txn_id" => $vtxn, "master_id" => $partyId,
        'master_id_type' => 'acc'
    ]);

    $VM->add_acc_txn_data([
        'cmp_id' => $compId, 'acc_id' => $partyId, 'acc_txn_date' => $vDate,
        'acc_txn_dr_cr' => 2,  // Credit for purchase
        'acc_txn_amt' => $invoiceValue, 'acc_txn_fcy' => $invoiceFcyValue,
        'vch_txn_id' => $vtxn, 'txn_id' => $mainTxnId,
        'hobo_id' => $boId, 'acc_txn_type' => $accTxnType
    ]);

    // ---- Party Memo ----
    if ($memoValue > 0) {
        $VM->add_acc_txn_data([
            'cmp_id' => $compId, 'acc_id' => $partyId, 'acc_txn_date' => $vDate,
            'acc_txn_dr_cr' => 2,  // Credit for purchase memo
            'acc_txn_amt' => $memoValue, 'acc_txn_fcy' => $memoFcyValue,
            'vch_txn_id' => $vtxn, 'txn_id' => $mainTxnId,
            'hobo_id' => $boId, 'acc_txn_type' => 3
        ]);
    }

    // ---- Party Register ----
    $VM->add_register_txn_data([
        'acct_vch_type' => $vchTypeId, 'cmp_id' => $compId, 'vch_txn_id' => $vtxn,
        'txn_id' => NULL, 'vch_date' => $vDate, 'acc_id' => $partyId,
        'acc_txn_dr_amt' => $invoiceValue, 'acc_txn_cr_amt' => 0,
        'vch_narr' => $longNarr ?? '', 'hobo_id' => $boId, 'acc_txn_type' => $accTxnType
    ]);

    // ====================================================================
    // ITEM WISE GRID ENTRIES
    // ====================================================================
    $taxIgst  = 0;
    $taxCgst  = 0;
    $taxSgst  = 0;
    $taxUtgst = 0;
    $taxCess  = 0;
    $itemAccountArr    = [];
    $itemAccountFcyArr = [];

    $posCode  = sprintf('%02d', $pos);

    foreach ($itmsdata as $itemRow) {
        $invSupplyId = $itemRow['supply_type_id'] ?? 1;

        $itemTxnId = $VM->add_comp_txn_data([
            "cmp_id" => $compId, "vch_series_id" => $vSer,
            "vch_txn_id" => $vtxn, "master_id" => $itemRow['item_id'],
            'master_id_type' => 'itm'
        ]);

        // ---- Memo item entry ----
        if ($memoChk == "1" && isset($itemRow['memo_amount']) && $itemRow['memo_amount'] > 0) {
            $VM->add_itm_txn_data([
                'cmp_id'         => $compId,
                'itm_id_unit_id' => $itemRow['item_id'] . '_' . $itemRow['item_unit_id'],
                'itm_txn_qty'    => $itemRow['item_qty'],
                'itm_txn_date'   => $vDate,
                'itm_txn_dr_cr'  => 1,  // Debit for purchase memo
                'itm_txn_rate'   => 1,
                'itm_txn_amt'    => parseAmount($itemRow['memo_amount']),
                'itm_txn_fcy'    => 0,
                'vch_txn_id'     => $vtxn,
                'txn_id'         => $itemTxnId,
                'mat_cent_id'    => $matId,
                'hobo_id'        => $boId,
                'itm_txn_type'   => 3
            ]);
        }

        // ---- Main item entry ----
        if (isset($itemRow['item_qty']) && $itemRow['item_qty'] > 0) {
            $VM->add_itm_txn_data([
                'cmp_id'         => $compId,
                'itm_id_unit_id' => $itemRow['item_id'] . '_' . $itemRow['item_unit_id'],
                'itm_txn_qty'    => $itemRow['item_qty'],
                'itm_txn_date'   => $vDate,
                'itm_txn_dr_cr'  => 1,  // Debit for purchase (stock inward)
                'itm_txn_rate'   => parseAmountPrice($itemRow['item_price'], 4),
                'itm_txn_amt'    => parseAmount($itemRow['item_total_amount']),
                'itm_txn_fcy'    => parseAmount($itemRow['item_total_fcy_amount']),
                'vch_txn_id'     => $vtxn,
                'txn_id'         => $itemTxnId,
                'mat_cent_id'    => $matId,
                'hobo_id'        => $boId,
                'itm_txn_type'   => 1
            ]);

            // Short narration
            $VM->save_voucher_narration($vtxn, $itemTxnId, 'short', $itemRow['description'] ?? '', $compId);

            // Tax inclusive
            if ($taxIncl && isset($itemRow['txinc_amount']) && $itemRow['txinc_amount'] > 0) {
                $VM->add_taxinc_txn_data([
                    'cmp_id'          => $compId,
                    'acc_itm_id'      => $itemRow['item_id'],
                    'acc_txn_inc_amt' => $itemRow['txinc_amount'],
                    'vch_txn_id'      => $vtxn,
                    'txn_id'          => $itemTxnId,
                    'acc_txn_dr_cr'   => 1  // Debit for purchase
                ]);
            }

            // Item register
            $regId = $VM->add_item_register_txn_data([
                'itm_vch_type'   => $vchTypeId,
                'cmp_id'         => $compId,
                'vch_txn_id'     => $vtxn,
                'txn_id'         => $itemTxnId,
                'vch_date'       => $vDate,
                'itm_id_unit_id' => $itemRow['item_id'] . '_' . $itemRow['item_unit_id'],
                'vch_narr'       => $longNarr ?? '',
                'hobo_id'        => $boId,
                'itm_txn_type'   => 1
            ]);

            // Item sub register
            $VM->add_item_sub_register_txn_data([
                'itm_vch_reg_id' => $regId,
                'mat_cent_id'    => $matId,
                'itm_txn_dr_qty'  => 0,
                'itm_txn_dr_rate' => 0,
                'itm_txn_dr_amt'  => 0,
                'itm_txn_cr_qty'  => $itemRow['item_qty'],
                'itm_txn_cr_rate' => parseAmountPrice($itemRow['item_price'], 4),
                'itm_txn_cr_amt'  => parseAmount($itemRow['item_total_amount']),
                'vch_narr'        => $longNarr ?? ''
            ]);

            // Aggregate purchase accounts (item_pur_acc for purchase)
            $purAccId = $itemRow['item_pur_acc'];
            $itemAccountArr[$purAccId]    = ($itemAccountArr[$purAccId] ?? 0) + parseAmount($itemRow['item_total_amount']);
            $itemAccountFcyArr[$purAccId] = ($itemAccountFcyArr[$purAccId] ?? 0) + parseAmount($itemRow['item_total_fcy_amount']);

            // ================================================================
            // TAX SUMMARY (Purchase three-way state logic)
            // ================================================================
            $amount    = parseAmount($itemRow['item_total_amount']);
            $amountFcy = parseAmount($itemRow['item_total_fcy_amount']);
            $taxDetailRates = $itemRow['tax_details'] ?? [];

            $itemInfo = $VM->get_item_details_info($itemRow['item_id'], $compId);
            $itemHsn  = $itemInfo['itm_hsn'] ?? '';

            $igstRate = parseAmount($itemRow['igst_rate'] ?? 0);
            $halfRate = $igstRate / 2;

            $taxsummaryData = [
                'cmp_id'             => $compId,
                'vch_txn_id'         => $vtxn,
                'txn_id'             => $itemTxnId,
                'acc_bsd_id'         => $itemRow['item_id'],
                'acc_bsd_type'       => 3,
                'vch_igst'           => 0,
                'vch_igst_rate'      => 0,
                'vch_cgst'           => 0,
                'vch_cgst_rate'      => 0,
                'vch_sgst_ugst'      => 0,
                'vch_sgst_ugst_rate' => 0,
                'vch_cess'           => 0,
                'vch_cess_rate'      => 0,
                'vch_taxable_value'  => $amount,
                'vch_total_tax'      => 0
            ];

            // ---- Purchase three-way state code tax logic ----
            $this->_applyPurchaseTaxLogic(
                $taxsummaryData,
                $vendorStateCode, $posCode, $boStateCode, $ugstStates,
                $amount, $igstRate, $halfRate,
                $taxIgst, $taxCgst, $taxSgst, $taxUtgst
            );

            // Cess
            $cessRate = 0;
            if (isset($taxDetailRates['cess']) && $taxDetailRates['cess'] > 0) {
                $cessRate  = $taxDetailRates['cess'];
                $cessValue = ($amount * $cessRate) / 100;
                $taxsummaryData['vch_cess']      = $cessValue;
                $taxsummaryData['vch_cess_rate'] = $cessRate;
                $taxsummaryData['vch_total_tax'] += $cessValue;
                $taxCess += $cessValue;
            }

            // GST tag
            $gstTag = in_array($invSupplyId, [1, 2, 3], true)
                ? ($gstinType === 1 ? 1 : ($gstinType === 2 ? 2 : 1))
                : ([16=>3,17=>4,20=>5,4=>6,5=>6,6=>6,7=>6,8=>7,9=>7,10=>7,11=>7,18=>8,19=>9][$invSupplyId] ?? 0);

            $taxsummaryData['vch_date']      = $vDate;
            $taxsummaryData['is_outward']    = 2;  // 2 for purchase (inward)
            $taxsummaryData['gst_rate_grp']  = $gstTag . ',' . parseAmount($igstRate) . ',' . parseAmount($cessRate);
            $taxsummaryData['vch_hsn_sac']   = $itemHsn;
            $taxsummaryData['inv_supply_id'] = $invSupplyId;

            $vchGstSumId = $VM->add_taxsummary_data($taxsummaryData, $taxFlag, $gstinType);

            // HSN summary
            $VM->add_hsnsummary_data([
                'cmp_id'                => $compId,
                'vch_txn_id'            => $vtxn,
                'txn_id'                => $itemTxnId,
                'vch_hsn_sac'           => $itemHsn,
                'vch_hsn_sac_taxbl_val' => $amount,
                'vch_hsn_sac_qty'       => $itemRow['item_qty'],
                'vch_hsn_sac_uom'       => $itemRow['item_unit_id'],
                'vch_hsn_sac_igst'      => $taxsummaryData['vch_igst'],
                'vch_hsn_sac_cgst'      => $taxsummaryData['vch_cgst'],
                'vch_hsn_sac_sgst_ugst' => $taxsummaryData['vch_sgst_ugst'],
                'vch_hsn_sac_cess'      => $taxsummaryData['vch_cess'],
                'inv_supply_id'         => $invSupplyId,
                'vch_gst_sum_id'        => $vchGstSumId
            ], $taxFlag, $gstinType);

            // ---- FCY Tax Summary ----
            if (parseAmount($fcyRate) > 0) {
                $taxsummaryFcyData = [
                    'cmp_id'                => $compId,
                    'vch_txn_id'            => $vtxn,
                    'txn_id'                => $itemTxnId,
                    'acc_bsd_id'            => $itemRow['item_id'],
                    'acc_bsd_type'          => 3,
                    'vch_igst_fcy'          => 0,
                    'vch_cgst_fcy'          => 0,
                    'vch_sgst_ugst_fcy'     => 0,
                    'vch_cess_fcy'          => 0,
                    'vch_taxable_value_fcy' => $amountFcy,
                    'vch_total_tax_fcy'     => 0
                ];

                $dummyI = $dummyC = $dummyS = $dummyU = 0;
                $this->_applyPurchaseTaxLogicFcy(
                    $taxsummaryFcyData,
                    $vendorStateCode, $posCode, $boStateCode, $ugstStates,
                    $amountFcy, $igstRate, $halfRate
                );

                // Cess FCY
                if (isset($taxDetailRates['cess']) && $taxDetailRates['cess'] > 0) {
                    $cessFcyValue = ($amountFcy * $taxDetailRates['cess']) / 100;
                    $taxsummaryFcyData['vch_cess_fcy']      = $cessFcyValue;
                    $taxsummaryFcyData['vch_total_tax_fcy'] += $cessFcyValue;
                }

                $VM->add_taxsummary_fcy_data($taxsummaryFcyData);
            }
        }
    }

    // ---- Purchase Account Entries (Debit) ----
    foreach ($itemAccountArr as $accId => $amt) {
        $amtFcy = $itemAccountFcyArr[$accId] ?? 0;
        $accTxnId = $VM->add_comp_txn_data([
            "cmp_id" => $compId, "vch_series_id" => $vSer,
            "vch_txn_id" => $vtxn, "master_id" => $accId,
            'master_id_type' => 'acc'
        ]);
        $VM->add_acc_txn_data([
            'cmp_id' => $compId, 'acc_id' => $accId, 'acc_txn_date' => $vDate,
            'acc_txn_dr_cr' => 1,  // Debit for purchase accounts
            'acc_txn_amt' => $amt, 'acc_txn_fcy' => $amtFcy,
            'vch_txn_id' => $vtxn, 'txn_id' => $accTxnId,
            'hobo_id' => $boId, 'acc_txn_type' => 1
        ]);
    }

    // ---- Bill Sundry Entries (purchase-style with vendor state logic) ----
    $this->_processPurchaseBillSundryEntries(
        $billsndry, $vtxn, $vSer, $vDate, $compId, $boId,
        $boStateCode, $pos, $ugstStates, $fcyRate, $memoChk, $VM, $taxFlag, $gstinType,
        $vendorStateCode,
        $taxIgst, $taxCgst, $taxSgst, $taxUtgst, $taxCess
    );

    // ---- Tax Account Entries (input for purchase, bsd_input_output=1) ----
    $this->_savePurchaseTaxAccountEntries(
        $gstinType, $vtxn, $vSer,
        $taxIgst, $taxCgst, $taxSgst, $taxUtgst, $taxCess,
        $vDate, $VM, $compVtxn, $compSeriesId, $compId, $boId
    );

    // ---- Long Narration ----
    $nrrTxnId = $VM->add_comp_txn_data([
        "cmp_id" => $compId, "vch_series_id" => $vSer,
        "vch_txn_id" => $vtxn, "master_id" => 0,
        'master_id_type' => 'nrr'
    ]);
    $VM->save_voucher_narration($vtxn, $nrrTxnId, 'long', $longNarr, $compId);

    // ---- Bill By Bill ----
    if (!empty($bbbFlat)) {
        $VM->SaveBillByBillData($bbbFlat, $vDate, $vtxn, $mainTxnId, $compId, $boId, $fyId);
    }

    // ---- Cost Centre ----
    if (!empty($ccFlat)) {
        $VM->SaveCostCentreData($ccFlat, $vDate, $vtxn, $mainTxnId, $compId, $boId);
    }

    // ---- Project Reporting ----
    if (!empty($prFlat)) {
        $VM->SaveProjectReportingData($prFlat, $vDate, $vtxn, $mainTxnId, $compId, $boId, $fyId);
    }

    // ---- Batch Data ----
    if (!empty($batchFlat)) {
        $VM->SaveBatchData($batchFlat, $vDate, $vtxn, $mainTxnId, $matId, $compId, $boId, $fyId);
    }
}


// ====================================================================
// PURCHASE-SPECIFIC HELPER: Three-way tax logic for amounts
// ====================================================================

private function _applyPurchaseTaxLogic(
    array &$ts,
    $vendorStateCode, $posCode, $boStateCode, $ugstStates,
    $amount, $igstRate, $halfRate,
    &$taxIgst, &$taxCgst, &$taxSgst, &$taxUtgst
) {
    if ($vendorStateCode && $posCode && $boStateCode) {

        if ($vendorStateCode == $posCode && $posCode == $boStateCode) {
            // Same vendor state, POS, and my state
            if (in_array($boStateCode, $ugstStates)) {
                // CGST + UTGST
                $cgstVal = ($amount * $halfRate) / 100;
                $ugstVal = ($amount * $halfRate) / 100;
                $ts['vch_cgst']           = $cgstVal;
                $ts['vch_cgst_rate']      = $halfRate;
                $ts['vch_sgst_ugst']      = $ugstVal;
                $ts['vch_sgst_ugst_rate'] = $halfRate;
                $ts['vch_total_tax']      = $cgstVal + $ugstVal;
                $taxCgst  += $cgstVal;
                $taxUtgst += $ugstVal;
            } else {
                // CGST + SGST
                $cgstVal = ($amount * $halfRate) / 100;
                $sgstVal = ($amount * $halfRate) / 100;
                $ts['vch_cgst']           = $cgstVal;
                $ts['vch_cgst_rate']      = $halfRate;
                $ts['vch_sgst_ugst']      = $sgstVal;
                $ts['vch_sgst_ugst_rate'] = $halfRate;
                $ts['vch_total_tax']      = $cgstVal + $sgstVal;
                $taxCgst += $cgstVal;
                $taxSgst += $sgstVal;
            }

        } elseif ($vendorStateCode != $posCode && $posCode == $boStateCode) {
            // IGST
            $igstVal = ($amount * $igstRate) / 100;
            $ts['vch_igst']      = $igstVal;
            $ts['vch_igst_rate'] = $igstRate;
            $ts['vch_total_tax'] = $igstVal;
            $taxIgst += $igstVal;

        } else {
            // Fallback
            $this->_applyFallbackTaxLogic(
                $ts, $boStateCode, $posCode, $ugstStates,
                $amount, $igstRate, $halfRate,
                $taxIgst, $taxCgst, $taxSgst, $taxUtgst
            );
        }

    } else {
        // No vendor/POS: fallback
        $this->_applyFallbackTaxLogic(
            $ts, $boStateCode, $posCode, $ugstStates,
            $amount, $igstRate, $halfRate,
            $taxIgst, $taxCgst, $taxSgst, $taxUtgst
        );
    }
}

private function _applyFallbackTaxLogic(
    array &$ts,
    $boStateCode, $posCode, $ugstStates,
    $amount, $igstRate, $halfRate,
    &$taxIgst, &$taxCgst, &$taxSgst, &$taxUtgst
) {
    if ($boStateCode != $posCode) {
        $igstVal = ($amount * $igstRate) / 100;
        $ts['vch_igst']      = $igstVal;
        $ts['vch_igst_rate'] = $igstRate;
        $ts['vch_total_tax'] = $igstVal;
        $taxIgst += $igstVal;
    } elseif (in_array($posCode, $ugstStates)) {
        $cgstVal = ($amount * $halfRate) / 100;
        $ugstVal = ($amount * $halfRate) / 100;
        $ts['vch_cgst']           = $cgstVal;
        $ts['vch_cgst_rate']      = $halfRate;
        $ts['vch_sgst_ugst']      = $ugstVal;
        $ts['vch_sgst_ugst_rate'] = $halfRate;
        $ts['vch_total_tax']      = $cgstVal + $ugstVal;
        $taxCgst  += $cgstVal;
        $taxUtgst += $ugstVal;
    } else {
        $cgstVal = ($amount * $halfRate) / 100;
        $sgstVal = ($amount * $halfRate) / 100;
        $ts['vch_cgst']           = $cgstVal;
        $ts['vch_cgst_rate']      = $halfRate;
        $ts['vch_sgst_ugst']      = $sgstVal;
        $ts['vch_sgst_ugst_rate'] = $halfRate;
        $ts['vch_total_tax']      = $cgstVal + $sgstVal;
        $taxCgst += $cgstVal;
        $taxSgst += $sgstVal;
    }
}


// ====================================================================
// PURCHASE-SPECIFIC HELPER: Three-way tax logic for FCY amounts
// ====================================================================

private function _applyPurchaseTaxLogicFcy(
    array &$tf,
    $vendorStateCode, $posCode, $boStateCode, $ugstStates,
    $amountFcy, $igstRate, $halfRate
) {
    if ($vendorStateCode && $posCode && $boStateCode) {

        if ($vendorStateCode == $posCode && $posCode == $boStateCode) {
            if (in_array($boStateCode, $ugstStates)) {
                $cgstVal = ($amountFcy * $halfRate) / 100;
                $ugstVal = ($amountFcy * $halfRate) / 100;
                $tf['vch_cgst_fcy']      = $cgstVal;
                $tf['vch_sgst_ugst_fcy'] = $ugstVal;
                $tf['vch_total_tax_fcy'] = $cgstVal + $ugstVal;
            } else {
                $cgstVal = ($amountFcy * $halfRate) / 100;
                $sgstVal = ($amountFcy * $halfRate) / 100;
                $tf['vch_cgst_fcy']      = $cgstVal;
                $tf['vch_sgst_ugst_fcy'] = $sgstVal;
                $tf['vch_total_tax_fcy'] = $cgstVal + $sgstVal;
            }

        } elseif ($vendorStateCode != $posCode && $posCode == $boStateCode) {
            $igstVal = ($amountFcy * $igstRate) / 100;
            $tf['vch_igst_fcy']      = $igstVal;
            $tf['vch_total_tax_fcy'] = $igstVal;

        } else {
            $this->_applyFallbackTaxLogicFcy($tf, $boStateCode, $posCode, $ugstStates, $amountFcy, $igstRate, $halfRate);
        }

    } else {
        $this->_applyFallbackTaxLogicFcy($tf, $boStateCode, $posCode, $ugstStates, $amountFcy, $igstRate, $halfRate);
    }
}

private function _applyFallbackTaxLogicFcy(
    array &$tf,
    $boStateCode, $posCode, $ugstStates,
    $amountFcy, $igstRate, $halfRate
) {
    if ($boStateCode != $posCode) {
        $igstVal = ($amountFcy * $igstRate) / 100;
        $tf['vch_igst_fcy']      = $igstVal;
        $tf['vch_total_tax_fcy'] = $igstVal;
    } elseif (in_array($posCode, $ugstStates)) {
        $cgstVal = ($amountFcy * $halfRate) / 100;
        $ugstVal = ($amountFcy * $halfRate) / 100;
        $tf['vch_cgst_fcy']      = $cgstVal;
        $tf['vch_sgst_ugst_fcy'] = $ugstVal;
        $tf['vch_total_tax_fcy'] = $cgstVal + $ugstVal;
    } else {
        $cgstVal = ($amountFcy * $halfRate) / 100;
        $sgstVal = ($amountFcy * $halfRate) / 100;
        $tf['vch_cgst_fcy']      = $cgstVal;
        $tf['vch_sgst_ugst_fcy'] = $sgstVal;
        $tf['vch_total_tax_fcy'] = $cgstVal + $sgstVal;
    }
}


// ====================================================================
// PURCHASE Bill Sundry entries with vendor state logic
// ====================================================================

private function _processPurchaseBillSundryEntries(
    $bsdData, $vtxn, $vSer, $vDate, $compId, $boId,
    $boStateCode, $pos, $ugstStates, $fcyRate, $memoChk, $VM, $taxFlag, $gstinType,
    $vendorStateCode,
    &$tIgst, &$tCgst, &$tSgst, &$tUtgst, &$tCess
) {
    if (empty($bsdData)) return;

    $posCode = sprintf('%02d', $pos);

    foreach ($bsdData as $row) {
        $txnId = $VM->add_comp_txn_data([
            "cmp_id" => $compId, "vch_series_id" => $vSer,
            "vch_txn_id" => $vtxn, "master_id" => $row['billsundry_id'],
            'master_id_type' => 'bsd'
        ]);

        // Memo
        if ($memoChk == "1" && isset($row['memo_amount']) && $row['memo_amount'] > 0) {
            $VM->add_acc_txn_data([
                'cmp_id' => $compId, 'acc_id' => $row['billsundry_id'], 'acc_txn_date' => $vDate,
                'acc_txn_dr_cr' => ($row['memo_amount'] < 0) ? 2 : 1,
                'acc_txn_amt' => $row['memo_amount'],
                'acc_txn_fcy' => (parseAmount($fcyRate) > 0) ? ($row['memo_amount'] * $fcyRate) : 0,
                'vch_txn_id' => $vtxn, 'txn_id' => $txnId,
                'hobo_id' => $boId, 'acc_txn_type' => 3
            ]);
        }

        // Account
        $VM->add_acc_txn_data([
            'cmp_id' => $compId, 'acc_id' => $row['billsundry_id'], 'acc_txn_date' => $vDate,
            'acc_txn_dr_cr' => ($row['billsundry_amount'] < 0) ? 2 : 1,
            'acc_txn_amt' => $row['billsundry_amount'],
            'acc_txn_fcy' => $row['billsundry_fcy_amount'] ?? 0,
            'vch_txn_id' => $vtxn, 'txn_id' => $txnId,
            'hobo_id' => $boId, 'acc_txn_type' => 1
        ]);

        $VM->save_voucher_narration($vtxn, $txnId, 'short', '', $compId);

        // Tax summary
        $amount    = parseAmount($row['billsundry_amount']);
        $amountFcy = parseAmount($row['billsundry_fcy_amount'] ?? 0);
        $igstRate  = (float)($row['igst_rate'] ?? 0);
        $halfRate  = $igstRate / 2;
        $taxDet    = $row['tax_details'] ?? [];

        $bsdInfo = $VM->get_bsd_details_info($row['billsundry_id'], $compId);
        $bsdHsn  = $bsdInfo['bsd_hsn_sac'] ?? '';

        $ts = [
            'cmp_id' => $compId, 'vch_txn_id' => $vtxn, 'txn_id' => $txnId,
            'acc_bsd_id' => $row['billsundry_id'], 'acc_bsd_type' => 2,
            'vch_igst' => 0, 'vch_igst_rate' => 0,
            'vch_cgst' => 0, 'vch_cgst_rate' => 0,
            'vch_sgst_ugst' => 0, 'vch_sgst_ugst_rate' => 0,
            'vch_cess' => 0, 'vch_cess_rate' => 0,
            'vch_taxable_value' => $amount, 'vch_total_tax' => 0
        ];

        $this->_applyPurchaseTaxLogic(
            $ts, $vendorStateCode, $posCode, $boStateCode, $ugstStates,
            $amount, $igstRate, $halfRate,
            $tIgst, $tCgst, $tSgst, $tUtgst
        );

        // Cess
        $cessRate = 0;
        if (isset($taxDet['cess']) && $taxDet['cess'] > 0) {
            $cessRate  = $taxDet['cess'];
            $cessValue = ($amount * $cessRate) / 100;
            $ts['vch_cess']      = $cessValue;
            $ts['vch_cess_rate'] = $cessRate;
            $ts['vch_total_tax'] += $cessValue;
            $tCess += $cessValue;
        }

        $ts['vch_date']      = $vDate;
        $ts['is_outward']    = 2;  // 2 for purchase
        $ts['gst_rate_grp']  = parseAmount($igstRate) . ',' . parseAmount($cessRate);
        $ts['vch_hsn_sac']   = $bsdHsn;
        $ts['inv_supply_id'] = 0;

        $gstSumId = $VM->add_taxsummary_data($ts, $taxFlag, $gstinType);

        $VM->add_hsnsummary_data([
            'cmp_id' => $compId, 'vch_txn_id' => $vtxn, 'txn_id' => $txnId,
            'vch_hsn_sac' => $bsdHsn, 'vch_hsn_sac_taxbl_val' => $amount,
            'vch_hsn_sac_qty' => 0, 'vch_hsn_sac_uom' => 0,
            'vch_hsn_sac_igst' => $ts['vch_igst'],
            'vch_hsn_sac_cgst' => $ts['vch_cgst'],
            'vch_hsn_sac_sgst_ugst' => $ts['vch_sgst_ugst'],
            'vch_hsn_sac_cess' => $ts['vch_cess'],
            'inv_supply_id' => 0, 'vch_gst_sum_id' => $gstSumId
        ], $taxFlag, $gstinType);

        // FCY tax summary for BSD
        if (parseAmount($fcyRate) > 0) {
            $tf = [
                'cmp_id' => $compId, 'vch_txn_id' => $vtxn, 'txn_id' => $txnId,
                'acc_bsd_id' => $row['billsundry_id'], 'acc_bsd_type' => 2,
                'vch_igst_fcy' => 0, 'vch_cgst_fcy' => 0,
                'vch_sgst_ugst_fcy' => 0, 'vch_cess_fcy' => 0,
                'vch_taxable_value_fcy' => $amountFcy, 'vch_total_tax_fcy' => 0
            ];

            $this->_applyPurchaseTaxLogicFcy(
                $tf, $vendorStateCode, $posCode, $boStateCode, $ugstStates,
                $amountFcy, $igstRate, $halfRate
            );

            if (isset($taxDet['cess']) && $taxDet['cess'] > 0) {
                $cessFcyVal = ($amountFcy * $taxDet['cess']) / 100;
                $tf['vch_cess_fcy']      = $cessFcyVal;
                $tf['vch_total_tax_fcy'] += $cessFcyVal;
            }

            $VM->add_taxsummary_fcy_data($tf);
        }
    }
}




/**
 * =====================================================================
 * _resavePurchaseWithoutItems (vch_sub_type_id == 0)
 * Purchase voucher without stock items (account-based grid)
 * =====================================================================
 */
private function _resavePurchaseWithoutItems(
    $db,$VM,$CM,$vtxn,$vchTypeId,$vDate,$vSer,$matId,$partyId,$pName,$compId,$boId,
    $boStateCode,$vendorStateCode,$pos,$ugstStates,$gstinType,
    $fcyRate,$currId,$longNarr,$taxIncl,$memoChk,
    $bbbData,$prData,$ccData,$bsdTxns,
    $taxFlag,$apprAmt,$overSup,$billno,$revChg,$outsupEco,$fyId,$uuid
) {
    // ---- Load account-based grid transactions ----
    $accountTxns = $VM->grid_account_transactions($vtxn, $compId, $boId);

    if (empty($accountTxns)) {
        throw new \RuntimeException('No account transactions found for purchase w/o item voucher vtxn=' . $vtxn);
    }

    // ---- Map to standard format ----
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

    // ---- Enrich bill sundry & flatten auxiliary data ----
    $billsndry = $this->_enrichBsdData($bsdTxns, $vtxn, $compId, $db, $VM);
    $bbbFlat   = $this->_flattenBbbData($bbbData);
    $prFlat    = $this->_flattenPrData($prData);
    $ccFlat    = $this->_flattenCcData($ccData);

    // ---- Calculate totals ----
    $purchaseTotal    = array_sum(array_column($itmsdata, 'amount'));
    $purchaseFcyTotal = array_sum(array_column($itmsdata, 'amountfc'));
    $bsdTotal         = array_sum(array_column($billsndry, 'billsundry_amount'));
    $bsdFcyTotal      = array_sum(array_column($billsndry, 'billsundry_fcy_amount'));
    $purchaseMemo     = array_sum(array_column($itmsdata, 'memo_amount'));
    $bsdMemo          = array_sum(array_column($billsndry, 'memo_amount'));

    $totalTax = $totalFcyTax = 0;
    if ($taxFlag == 1) {
        $totalTax    += $VM->GetTotalTax($itmsdata, 'acc')['tax_amount'];
        $totalFcyTax += $VM->GetTotalTax($itmsdata, 'acc')['fcy_tax_amount'];
        $totalTax    += $VM->GetTotalTax($billsndry, 'bsd')['tax_amount'];
        $totalFcyTax += $VM->GetTotalTax($billsndry, 'bsd')['fcy_tax_amount'];
    }

    // ---- CLEAR OLD DATA ----
    $this->_clearPurchaseVoucherData($VM, $vtxn, $vchTypeId, $compId, $boId);

    // ---- Invoice values ----
    $invoiceValue    = $purchaseTotal + $bsdTotal + $totalTax;
    $invoiceFcyValue = $purchaseFcyTotal + $bsdFcyTotal + $totalFcyTax;
    $memoValue       = $purchaseMemo + $bsdMemo;
    $memoFcyValue    = (parseAmount($fcyRate) > 0) ? $memoValue * $fcyRate : 0;
	    // ---- Composition (gstinType == 2) ----
    $compVtxn     = 0;
    $compSeriesId = $vSer;

    if ($gstinType == 2) {
        // Bridge type 3 for purchase composition
        $bridge = $VM->get_voucher_bridge_info($vtxn, 3, $compId);
        if ($bridge) {
            $compVtxn      = $bridge['vch_txn_id_dest'];
            $gstPaidAccId  = $VM->getGstPaidAccountId($compId);
            $compVchTypeId = 23;
            $compSeriesId  = $VM->getSeriesId($compVchTypeId, $compId);

            $VM->update_comps_voucher_cons_data([
                "vch_series_id"    => $compSeriesId,
                "vch_type_id"      => $compVchTypeId,
                "vch_sub_type_id"  => 0,
                "vch_date"         => $vDate,
                "mat_cent_id"      => 0,  // No material center for w/o items
                "draft_vch_rec_id" => 0
            ], $compVtxn, $compId, $compVchTypeId, $boId);

            $compMainTxn = $VM->add_comp_txn_data([
                "cmp_id" => $compId, "vch_series_id" => $compSeriesId,
                "vch_txn_id" => $compVtxn, "master_id" => $gstPaidAccId,
                'master_id_type' => 'acc'
            ]);

            $VM->add_comp_txn_data([
                "cmp_id" => $compId, "vch_series_id" => $compSeriesId,
                "vch_txn_id" => $compVtxn, "master_id" => 0,
                'master_id_type' => 'nrr'
            ]);

            $VM->save_voucher_narration($compVtxn, $compMainTxn, 'long', $longNarr, $compId);

            $VM->add_acc_txn_data([
                'cmp_id' => $compId, 'acc_id' => $gstPaidAccId, 'acc_txn_date' => $vDate,
                'acc_txn_dr_cr' => 1, 'acc_txn_amt' => $totalTax, 'acc_txn_fcy' => $totalFcyTax,
                'vch_txn_id' => $compVtxn, 'txn_id' => $compMainTxn,
                'hobo_id' => $boId, 'acc_txn_type' => 1
            ]);

            $VM->add_register_txn_data([
                'acct_vch_type' => $compVchTypeId, 'cmp_id' => $compId,
                'vch_txn_id' => $compVtxn, 'txn_id' => NULL, 'vch_date' => $vDate,
                'acc_id' => $gstPaidAccId,
                'acc_txn_dr_amt' => $totalTax, 'acc_txn_cr_amt' => 0,
                'vch_narr' => $longNarr ?? '', 'hobo_id' => $boId, 'acc_txn_type' => 1
            ]);
        }
    }
	    // ---- Update vchtxnconso ----
    $VM->update_voucher_cons_data([
        "vch_series_id"    => $vSer,
        "vch_date"         => $vDate,
        "draft_vch_rec_id" => 0
    ], $vtxn, $compId, $boId);

    // ---- FCY rate ----
    $this->_saveFcyRate($VM, $vtxn, $compId, $fcyRate, $currId);

    // ---- GST Inward Supply (gstrinwsup) ----
    $inwSupData = [
        "inwsup_pos"                => $pos,
        "inwsup_bill_ref_no"        => $billno,
        "inwsup_rev_chg"            => (int)$revChg,
        "inwsup_cr_note_outsup_id"  => 0,
        "inwsup_eco"                => (int)$outsupEco
    ];
    $VM->update_gstrinwsup_data((int)$vtxn, $inwSupData, $compId);

    // ---- Approval ----
    $accTxnType = 1;
    if ($apprAmt != '' && parseAmount($invoiceValue) > parseAmount($apprAmt)) {
        $accTxnType = 4;
    }

    // ---- Party Account Entry (Credit for purchase) ----
    $mainTxnId = $VM->add_comp_txn_data([
        "cmp_id" => $compId, "vch_series_id" => $vSer,
        "vch_txn_id" => $vtxn, "master_id" => $partyId,
        'master_id_type' => 'acc'
    ]);

    $VM->add_acc_txn_data([
        'cmp_id' => $compId, 'acc_id' => $partyId, 'acc_txn_date' => $vDate,
        'acc_txn_dr_cr' => 2,  // Credit for purchase party
        'acc_txn_amt' => $invoiceValue, 'acc_txn_fcy' => $invoiceFcyValue,
        'vch_txn_id' => $vtxn, 'txn_id' => $mainTxnId,
        'hobo_id' => $boId, 'acc_txn_type' => $accTxnType
    ]);

    // ---- Party Memo ----
    if ($memoValue > 0) {
        $VM->add_acc_txn_data([
            'cmp_id' => $compId, 'acc_id' => $partyId, 'acc_txn_date' => $vDate,
            'acc_txn_dr_cr' => 2,  // Credit for purchase memo
            'acc_txn_amt' => $memoValue, 'acc_txn_fcy' => $memoFcyValue,
            'vch_txn_id' => $vtxn, 'txn_id' => $mainTxnId,
            'hobo_id' => $boId, 'acc_txn_type' => 3
        ]);
    }

    // ---- Party Register ----
    $VM->add_register_txn_data([
        'acct_vch_type' => $vchTypeId, 'cmp_id' => $compId, 'vch_txn_id' => $vtxn,
        'txn_id' => NULL, 'vch_date' => $vDate, 'acc_id' => $partyId,
        'acc_txn_dr_amt' => $invoiceValue, 'acc_txn_cr_amt' => 0,
        'vch_narr' => $longNarr ?? '', 'hobo_id' => $boId, 'acc_txn_type' => $accTxnType
    ]);
	    // ====================================================================
    // ACCOUNT WISE GRID ENTRIES
    // ====================================================================
    $taxIgst  = 0;
    $taxCgst  = 0;
    $taxSgst  = 0;
    $taxUtgst = 0;
    $taxCess  = 0;

    $posCode = sprintf('%02d', $pos);

    foreach ($itmsdata as $ir) {
        $supId = $ir['supply_type_id'] ?? 1;

        $txnId = $VM->add_comp_txn_data([
            "cmp_id" => $compId, "vch_series_id" => $vSer,
            "vch_txn_id" => $vtxn, "master_id" => $ir['account_id'],
            'master_id_type' => 'acc'
        ]);

        // ---- Memo ----
        if ($memoChk == "1" && isset($ir['memo_amount']) && $ir['memo_amount'] > 0) {
            $VM->add_acc_txn_data([
                'cmp_id' => $compId, 'acc_id' => $ir['account_id'], 'acc_txn_date' => $vDate,
                'acc_txn_dr_cr' => 1,  // Debit for purchase memo
                'acc_txn_amt' => $ir['memo_amount'],
                'acc_txn_fcy' => (parseAmount($fcyRate) > 0) ? ($ir['memo_amount'] * $fcyRate) : 0,
                'vch_txn_id' => $vtxn, 'txn_id' => $txnId,
                'hobo_id' => $boId, 'acc_txn_type' => 3
            ]);
        }

        // ---- Account transaction (Debit for purchase) ----
        $VM->add_acc_txn_data([
            'cmp_id' => $compId, 'acc_id' => $ir['account_id'], 'acc_txn_date' => $vDate,
            'acc_txn_dr_cr' => 1,  // Debit for purchase accounts
            'acc_txn_amt' => $ir['amount'], 'acc_txn_fcy' => $ir['amountfc'],
            'vch_txn_id' => $vtxn, 'txn_id' => $txnId,
            'hobo_id' => $boId, 'acc_txn_type' => 1
        ]);

        // ---- Register ----
        $VM->add_register_txn_data([
            'acct_vch_type' => $vchTypeId, 'cmp_id' => $compId, 'vch_txn_id' => $vtxn,
            'txn_id' => $txnId, 'vch_date' => $vDate, 'acc_id' => $ir['account_id'],
            'acc_txn_dr_amt' => $ir['amount'], 'acc_txn_cr_amt' => 0,
            'vch_narr' => $ir['description'], 'hobo_id' => $boId, 'acc_txn_type' => $accTxnType
        ]);

        // ---- Short narration ----
        $VM->save_voucher_narration($vtxn, $txnId, 'short', $ir['description'], $compId);

        // ---- Tax inclusive ----
        if ($taxIncl && isset($ir['txinc_amount']) && $ir['txinc_amount'] > 0) {
            $VM->add_taxinc_txn_data([
                'cmp_id'          => $compId,
                'acc_itm_id'      => $ir['account_id'],
                'acc_txn_inc_amt' => $ir['txinc_amount'],
                'vch_txn_id'      => $vtxn,
                'txn_id'          => $txnId,
                'acc_txn_dr_cr'   => 1  // Debit for purchase
            ]);
        }

        // ================================================================
        // TAX SUMMARY (Purchase three-way state logic for accounts)
        // ================================================================
        $amount    = parseAmount($ir['amount']);
        $amountFcy = parseAmount($ir['amountfc']);
        $taxDetailRates = $ir['tax_details'] ?? [];

        $accDetailsInfo = $VM->get_acc_details_info($ir['account_id'], $compId);
        $accHsn = $accDetailsInfo['acc_sac'] ?? '';

        $igstRate = parseAmount($ir['igst_rate'] ?? 0);
        $halfRate = $igstRate / 2;

        $taxsummaryData = [
            'cmp_id'             => $compId,
            'vch_txn_id'         => $vtxn,
            'txn_id'             => $txnId,
            'acc_bsd_id'         => $ir['account_id'],
            'acc_bsd_type'       => 1,  // Account type
            'vch_igst'           => 0,
            'vch_igst_rate'      => 0,
            'vch_cgst'           => 0,
            'vch_cgst_rate'      => 0,
            'vch_sgst_ugst'      => 0,
            'vch_sgst_ugst_rate' => 0,
            'vch_cess'           => 0,
            'vch_cess_rate'      => 0,
            'vch_taxable_value'  => $amount,
            'vch_total_tax'      => 0
        ];

        // Apply purchase three-way tax logic
        $this->_applyPurchaseTaxLogic(
            $taxsummaryData,
            $vendorStateCode, $posCode, $boStateCode, $ugstStates,
            $amount, $igstRate, $halfRate,
            $taxIgst, $taxCgst, $taxSgst, $taxUtgst
        );

        // Cess
        $cessRate = 0;
        if (isset($taxDetailRates['cess']) && $taxDetailRates['cess'] > 0) {
            $cessRate  = $taxDetailRates['cess'];
            $cessValue = ($amount * $cessRate) / 100;
            $taxsummaryData['vch_cess']      = $cessValue;
            $taxsummaryData['vch_cess_rate'] = $cessRate;
            $taxsummaryData['vch_total_tax'] += $cessValue;
            $taxCess += $cessValue;
        }

        // GST tag
        $gstTag = in_array($supId, [1, 2, 3], true)
            ? ($gstinType === 1 ? 1 : ($gstinType === 2 ? 2 : 1))
            : ([16=>3,17=>4,20=>5,4=>6,5=>6,6=>6,7=>6,8=>7,9=>7,10=>7,11=>7,18=>8,19=>9][$supId] ?? 0);

        $taxsummaryData['vch_date']      = $vDate;
        $taxsummaryData['is_outward']    = 2;  // 2 for purchase (inward)
        $taxsummaryData['gst_rate_grp']  = $gstTag . ',' . parseAmount($igstRate) . ',' . parseAmount($cessRate);
        $taxsummaryData['vch_hsn_sac']   = $accHsn;
        $taxsummaryData['inv_supply_id'] = $supId;

        $vchGstSumId = $VM->add_taxsummary_data($taxsummaryData, $taxFlag, $gstinType);

        // HSN summary (no qty/uom for account-based)
        $VM->add_hsnsummary_data([
            'cmp_id'                => $compId,
            'vch_txn_id'            => $vtxn,
            'txn_id'                => $txnId,
            'vch_hsn_sac'           => $accHsn,
            'vch_hsn_sac_taxbl_val' => $amount,
            'vch_hsn_sac_qty'       => 0,
            'vch_hsn_sac_uom'       => 0,
            'vch_hsn_sac_igst'      => $taxsummaryData['vch_igst'],
            'vch_hsn_sac_cgst'      => $taxsummaryData['vch_cgst'],
            'vch_hsn_sac_sgst_ugst' => $taxsummaryData['vch_sgst_ugst'],
            'vch_hsn_sac_cess'      => $taxsummaryData['vch_cess'],
            'inv_supply_id'         => $supId,
            'vch_gst_sum_id'        => $vchGstSumId
        ], $taxFlag, $gstinType);

        // ---- FCY Tax Summary ----
        if (parseAmount($fcyRate) > 0) {
            $taxsummaryFcyData = [
                'cmp_id'                => $compId,
                'vch_txn_id'            => $vtxn,
                'txn_id'                => $txnId,
                'acc_bsd_id'            => $ir['account_id'],
                'acc_bsd_type'          => 1,
                'vch_igst_fcy'          => 0,
                'vch_cgst_fcy'          => 0,
                'vch_sgst_ugst_fcy'     => 0,
                'vch_cess_fcy'          => 0,
                'vch_taxable_value_fcy' => $amountFcy,
                'vch_total_tax_fcy'     => 0
            ];

            $this->_applyPurchaseTaxLogicFcy(
                $taxsummaryFcyData,
                $vendorStateCode, $posCode, $boStateCode, $ugstStates,
                $amountFcy, $igstRate, $halfRate
            );

            // Cess FCY
            if (isset($taxDetailRates['cess']) && $taxDetailRates['cess'] > 0) {
                $cessFcyValue = ($amountFcy * $taxDetailRates['cess']) / 100;
                $taxsummaryFcyData['vch_cess_fcy']      = $cessFcyValue;
                $taxsummaryFcyData['vch_total_tax_fcy'] += $cessFcyValue;
            }

            $VM->add_taxsummary_fcy_data($taxsummaryFcyData);
        }
    }
	    // ---- Bill Sundry Entries (purchase-style with vendor state logic) ----
    $this->_processPurchaseBillSundryEntries(
        $billsndry, $vtxn, $vSer, $vDate, $compId, $boId,
        $boStateCode, $pos, $ugstStates, $fcyRate, $memoChk, $VM, $taxFlag, $gstinType,
        $vendorStateCode,
        $taxIgst, $taxCgst, $taxSgst, $taxUtgst, $taxCess
    );

    // ---- Tax Account Entries (input for purchase, bsd_input_output=1) ----
    $this->_savePurchaseTaxAccountEntries(
        $gstinType, $vtxn, $vSer,
        $taxIgst, $taxCgst, $taxSgst, $taxUtgst, $taxCess,
        $vDate, $VM, $compVtxn, $compSeriesId, $compId, $boId
    );

    // ---- Long Narration ----
    $nrrTxnId = $VM->add_comp_txn_data([
        "cmp_id" => $compId, "vch_series_id" => $vSer,
        "vch_txn_id" => $vtxn, "master_id" => 0,
        'master_id_type' => 'nrr'
    ]);
    $VM->save_voucher_narration($vtxn, $nrrTxnId, 'long', $longNarr, $compId);

    // ---- Bill By Bill ----
    if (!empty($bbbFlat)) {
        $VM->SaveBillByBillData($bbbFlat, $vDate, $vtxn, $mainTxnId, $compId, $boId, $fyId);
    }

    // ---- Cost Centre ----
    if (!empty($ccFlat)) {
        $VM->SaveCostCentreData($ccFlat, $vDate, $vtxn, $mainTxnId, $compId, $boId);
    }

    // ---- Project Reporting ----
    if (!empty($prFlat)) {
        $VM->SaveProjectReportingData($prFlat, $vDate, $vtxn, $mainTxnId, $compId, $boId, $fyId);
    }

    // ---- Activity Log ----
    // $VM->SaveUserActivity("Purchase w/o item voucher re-saved via API", $vtxn);
   }
private function _clearVoucherData($VM, $vtxn, $vchTypeId, $compId, $boId)
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
    $VM->clear_system_journal_txn_data($vtxn, 3,$compId);
}
/**
 * Save Tax Account Entries for Purchase
 * gstinType==1 → uses $vtxn/$vSer, POSITIVE amounts, bsd_input_output=1 (input credit)
 * gstinType==2 → uses $compVtxn/$compSeriesId, NEGATIVE amounts, bsd_input_output=1
 */
private function _savePurchaseTaxAccountEntries(
    $gstinType, $vtxn, $vSer,
    $igst, $cgst, $sgst, $utgst, $cess,
    $vDate, $VM, $compVtxn, $compSeriesId, $compId, $boId
) {
    if ($gstinType == 1) {
        if ($igst > 0) {
            $VM->save_taxacc_yes_out_data($vtxn, $vSer, $igst, 1, $vDate, 1, $compId, $boId);
        } elseif ($cgst > 0 && $sgst > 0) {
            $VM->save_taxacc_yes_out_data($vtxn, $vSer, $cgst, 2, $vDate, 1, $compId, $boId);
            $VM->save_taxacc_yes_out_data($vtxn, $vSer, $sgst, 3, $vDate, 1, $compId, $boId);
        } elseif ($cgst > 0 && $utgst > 0) {
            $VM->save_taxacc_yes_out_data($vtxn, $vSer, $cgst, 2, $vDate, 1, $compId, $boId);
            $VM->save_taxacc_yes_out_data($vtxn, $vSer, $utgst, 4, $vDate, 1, $compId, $boId);
        }
        if ($cess > 0) {
            $VM->save_taxacc_yes_out_data($vtxn, $vSer, $cess, 5, $vDate, 1, $compId, $boId);
        }
    }

    if ($gstinType == 2) {
        if ($igst > 0) {
            $VM->save_taxacc_yes_out_data($compVtxn, $compSeriesId, -$igst, 1, $vDate, 1, $compId, $boId);
        } elseif ($cgst > 0 && $sgst > 0) {
            $VM->save_taxacc_yes_out_data($compVtxn, $compSeriesId, -$cgst, 2, $vDate, 1, $compId, $boId);
            $VM->save_taxacc_yes_out_data($compVtxn, $compSeriesId, -$sgst, 3, $vDate, 1, $compId, $boId);
        } elseif ($cgst > 0 && $utgst > 0) {
            $VM->save_taxacc_yes_out_data($compVtxn, $compSeriesId, -$cgst, 2, $vDate, 1, $compId, $boId);
            $VM->save_taxacc_yes_out_data($compVtxn, $compSeriesId, -$utgst, 4, $vDate, 1, $compId, $boId);
        }
        if ($cess > 0) {
            $VM->save_taxacc_yes_out_data($compVtxn, $compSeriesId, -$cess, 5, $vDate, 1, $compId, $boId);
        }
    }
}
}