<?php
/**
 * SANDBOX-ONLY. Root cause found on real data: bill-sundry (tax) ledgers that sit directly under a profit & loss category were left out
 * of the OLD condensed Profit & Loss (its code says "bill sundry data is pending"), so its net result differed from the other layouts.
 * On the basic ledger (net profit 28,200) a balanced voucher is added that credits two such ledgers 1,000 each against Office Rent 2,000:
 * the profit does not change, the corrected report shows 28,200 in all six layouts, the old condensed view shows 26,200.
 */
require __DIR__ . '/boot.php';
$db = \Config\Database::connect();
$db->query("INSERT INTO acctmaster (acc_id, cmp_id, acc_name, bsd_id) VALUES (150,1,'Tax Output A',3),(151,1,'Tax Output B',4)");
$db->query("INSERT INTO undercrsmt (cmp_id,cmpfymastr_id,crs_mst_type,crs_mst_id,under_crs_mst_id,crs_mst_parent_id,under_main_id,crs_mst_is_primary) VALUES (1,1,14,150,0,13,0,0),(1,1,14,151,0,13,0,0)");
$db->query("INSERT INTO accttxnmst (cmp_id,hobo_id,acc_id,acc_txn_date,acc_txn_dr_cr,acc_txn_amt,acc_txn_type,txn_id,vch_txn_id) VALUES (1,1,110,'2025-07-01',1,2000,1,20,20),(1,1,150,'2025-07-01',2,1000,1,20,20),(1,1,151,'2025-07-01',2,1000,1,20,20)");
$db->query("INSERT INTO vchtxnconso (vch_txn_id,cmp_id,hobo_id,txn_id,vch_type_id,vch_date) VALUES (20,1,1,20,1,'2025-07-01')");
$m = harness_reporting();
$F = '2025-04-01'; $T = '2026-03-31';
$fail = 0; function chk($l, $g, $w) { global $fail; $ok = abs($g - $w) < 0.005; printf("  %-62s %s%s\n", $l, $ok ? 'PASS' : 'FAIL', $ok ? '' : "  got $g want $w"); if (!$ok) { $fail++; } }
$last = fn($rows) => (function () use ($rows) { $l = end($rows); return round((float)$l['l_balance_total'] - (float)$l['r_balance_total'], 2); })();

foreach ([0, 1, 2] as $v) {
    chk("corrected H view $v net profit 28,200 (tax ledgers included)", $last($m->load_profit_loss_horizontal($v, $F, $T, 1, 0)), 28200);
    $by = []; foreach ($m->load_profit_loss_vertical($v, $F, $T, 1, 0) as $r) { $by[$r['group_name']] = $r['amt']; }
    chk("corrected V view $v net profit 28,200", (float)($by['Net Profit C/D'] ?? 0), 28200);
}
// the condensed view shows the category line: Indirect Expenses = rent 12,000 + salary 30,000 + discount 300 + ... + 2,000 - 2,000 (tax credits)
$cond = []; foreach ($m->load_profit_loss_horizontal(0, $F, $T, 1, 0) as $r) { if (($r['l_type'] ?? '') === 'prt') { $cond[(int)($r['l_parent_id'] ?? 0)] = (float)$r['l_balance_total']; } }
$legacy = []; foreach ($m->legacy_load_profit_loss_horizontal(0, $F, $T, 1, 0) as $r) { if (($r['l_type'] ?? '') === 'prt') { $legacy[(int)($r['l_parent_id'] ?? 0)] = (float)$r['l_balance_total']; } }
chk('corrected condensed Indirect Expenses is 2,000 lower than the old condensed view', ($legacy[13] ?? 0) - ($cond[13] ?? 0), 2000);
echo $fail ? "\n$fail FAILED\n" : "\nALL BILL-SUNDRY P&L CHECKS PASSED\n";
exit($fail ? 1 : 0);
