<?php
/**
 * SANDBOX-ONLY: drives the REAL FYModel roll-over ("Rewrite Books": clear next-FY openings, then
 * update_fy_account_balance() for every account) over three synthetic financial years and compares the openings it
 * writes with an independent raw-SQL computation. Ledger-only (stock stubbed to zero), so all figures are hand-checkable.
 */
require __DIR__ . '/boot.php';
\App\Models\Admin\StockStatusModelStub::$opening = 0.0;
\App\Models\Admin\StockStatusModelStub::$closing = 0.0;
$db = harness_db();
$fail = 0; $pass = 0;
function ok(bool $c, string $l, string $d = ''): void { global $fail, $pass; if ($c) { $pass++; echo "  PASS  $l\n"; } else { $fail++; echo "  FAIL  $l $d\n"; } }
$FY = [1 => ['2023-04-01', '2024-03-31'], 2 => ['2024-04-01', '2025-03-31'], 3 => ['2025-04-01', '2026-03-31']];
$PL_CATS = [6, 7, 8, 9, 10, 11, 12, 13];

function opening(int $fy, int $acc): float { global $db; return (float)($db->query("SELECT COALESCE(SUM(acc_op_bal),0) v FROM accoppybal WHERE cmp_id=1 AND cmpfymastr_id=$fy AND acc_id=$acc AND hobo_id=1")->getRow()->v); }
function movement(int $fy, int $acc): float { global $db, $FY; [$a, $b] = $FY[$fy];
    return (float)($db->query("SELECT COALESCE(SUM(CASE WHEN acc_txn_dr_cr=1 THEN acc_txn_amt ELSE -acc_txn_amt END),0) v FROM accttxnmst WHERE cmp_id=1 AND hobo_id=1 AND acc_txn_type=1 AND vch_txn_id>0 AND acc_id=$acc AND acc_txn_date BETWEEN '$a' AND '$b'")->getRow()->v); }
function category(int $fy, int $acc): int { global $db; $r = $db->query("SELECT crs_mst_parent_id p FROM undercrsmt WHERE cmp_id=1 AND cmpfymastr_id=$fy AND crs_mst_type=1 AND crs_mst_id=$acc LIMIT 1")->getRow(); return $r ? (int)$r->p : 0; }
function plAppId(): int { global $db; $r = $db->query("SELECT acc_id FROM acctmaster WHERE cmp_id=1 AND LOWER(acc_name)='profit & loss appropriation'")->getRow(); return $r ? (int)$r->acc_id : 0; }

function roll(int $from): void {
    global $FY, $db;
    $sess = ['ses_comp_fy_id' => $from, 'ses_company_fy_beginning' => $FY[$from][0], 'ses_company_fy_end' => $FY[$from][1]];
    harness_session($sess);
    $fy = new \App\Models\FYModel();
    $fy->ReportingModel = harness_reporting($sess);          // real report code, stock stubbed
    $fy->clear_next_fy_account_balance();                      // what Rewrite Books does first
    $common = $fy->prepare_common_fy_data();
    foreach ($db->query("SELECT acc_id FROM acctmaster WHERE cmp_id=1 ORDER BY acc_id")->getResultArray() as $r) {
        $errs = $fy->update_fy_account_balance((int)$r['acc_id'], $common);
        if ($errs) { echo "    (account {$r['acc_id']}: " . implode('; ', $errs) . ")\n"; }
    }
}

foreach ([1, 2] as $from) {
    $to = $from + 1;
    echo "\n=== roll-over FY$from -> FY$to ===\n";
    // independent expectation, computed BEFORE the code under test runs
    $profit = 0.0; $expect = []; $plId = plAppId();
    foreach ($db->query("SELECT acc_id FROM acctmaster WHERE cmp_id=1 ORDER BY acc_id")->getResultArray() as $r) {
        $id = (int)$r['acc_id']; $cat = category($from, $id);
        $closing = opening($from, $id) + movement($from, $id);
        if (in_array($cat, $PL_CATS, true)) { $profit += -movement($from, $id); $expect[$id] = 0.0; }        // P&L accounts start the year at zero
        elseif ($id !== $plId)               { $expect[$id] = round($closing, 2); }                         // balance-sheet ledgers carry their closing
    }
    $plClosing = $plId ? opening($from, $plId) + movement($from, $plId) : 0.0;                               // Dr+ / Cr-
    $expectPl  = round($plClosing - $profit, 2);                                                            // accumulated + this year's profit, as a credit
    roll($from);
    $plId = plAppId();
    printf("  FY$from profit %.2f | P&L Appropriation closing before = %.2f (Dr+) | expected next opening %.2f\n", $profit, $plClosing, $expectPl);
    foreach ($expect as $id => $want) {
        $name = $db->query("SELECT acc_name n FROM acctmaster WHERE acc_id=$id")->getRow()->n;
        $got = opening($to, $id);
        ok(abs($got - $want) < 0.005, sprintf("FY$to opening of %-22s expected %10.2f  actual %10.2f", $name, $want, $got));
    }
    $got = opening($to, $plId);
    ok(abs($got - $expectPl) < 0.005, sprintf("FY$to opening of %-22s expected %10.2f  actual %10.2f", 'P&L Appropriation', $expectPl, $got));
    $sum = (float)$db->query("SELECT COALESCE(SUM(acc_op_bal),0) v FROM accoppybal WHERE cmp_id=1 AND cmpfymastr_id=$to AND hobo_id=1")->getRow()->v;
    ok(abs($sum) < 0.005, sprintf("FY$to opening trial balance balances (sum of openings = %.2f)", $sum));
}

// FY creation path: account_tables() copies balance-sheet openings and writes the P&L Appropriation opening from the report
echo "\n=== new-FY creation (FYModel::account_tables) ===\n";
$db->query("UPDATE cmpfymastr SET is_imported = 0 WHERE cmpfymastr_id = 3");                              // FY3 is the 'new' FY, FY2 the latest imported
$db->query("DELETE FROM accoppybal WHERE cmpfymastr_id = 3");
$sess = ['ses_comp_fy_id' => 2, 'ses_company_fy_beginning' => $FY[2][0], 'ses_company_fy_end' => $FY[2][1]];
harness_session($sess);
$fy = new \App\Models\FYModel(); $fy->ReportingModel = harness_reporting($sess);
$profit2 = 0.0; foreach ([103, 104, 105, 106] as $id) { $profit2 += -movement(2, $id); }
$resp = $fy->account_tables();
$plId = plAppId();
$pl3  = $plId ? opening(3, $plId) : 0.0;
ok(abs($pl3 - (-$profit2)) < 0.005 || abs($pl3 - (opening(2, $plId) - $profit2)) < 0.005,
   sprintf("FY3 P&L Appropriation opening from FY creation is a CREDIT of the year's profit (profit %.2f, opening %.2f)", $profit2, $pl3));
$txn = $db->query("SELECT acc_txn_dr_cr d, acc_txn_amt a FROM accttxnmst WHERE cmp_id=1 AND acc_id=$plId AND vch_txn_id=0 ORDER BY acc_txn_id DESC LIMIT 1")->getRow();
ok($txn && (int)$txn->d === ($profit2 >= 0 ? 2 : 1), sprintf("FY3 opening mirror row for P&L Appropriation is posted %s", $profit2 >= 0 ? 'Cr (profit)' : 'Dr (loss)'), $txn ? "(dr_cr={$txn->d})" : '(none)');

echo "\nchecks passed: $pass | failed: $fail\n"; exit($fail ? 1 : 0);
