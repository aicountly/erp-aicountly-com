<?php
/** SANDBOX-ONLY: step "roll" = run Rewrite-Books roll-overs FY1->FY2->FY3 with the FYModel currently in the sandbox; step "show" = FY3 reports. */
require __DIR__ . '/boot.php';
\App\Models\Admin\StockStatusModelStub::$opening = 0.0; \App\Models\Admin\StockStatusModelStub::$closing = 0.0;
$db = harness_db();
$FY = [1 => ['2023-04-01', '2024-03-31'], 2 => ['2024-04-01', '2025-03-31'], 3 => ['2025-04-01', '2026-03-31']];
if (($argv[1] ?? '') === 'roll') {
    foreach ([1, 2] as $from) {
        $sess = ['ses_comp_fy_id' => $from, 'ses_company_fy_beginning' => $FY[$from][0], 'ses_company_fy_end' => $FY[$from][1]];
        harness_session($sess); $fy = new \App\Models\FYModel(); $fy->ReportingModel = harness_reporting($sess);
        $fy->clear_next_fy_account_balance(); $common = $fy->prepare_common_fy_data();
        foreach ($db->query("SELECT acc_id FROM acctmaster WHERE cmp_id=1 ORDER BY acc_id")->getResultArray() as $r) { $fy->update_fy_account_balance((int)$r['acc_id'], $common); }
    }
    echo "rolled FY1->FY2->FY3\n"; exit;
}
$m = harness_reporting(['ses_comp_fy_id' => 3, 'ses_company_fy_beginning' => $FY[3][0], 'ses_company_fy_end' => $FY[3][1]]);
$rows = $m->load_balance_sheet_horizontal(1, $FY[3][0], $FY[3][1], 0, 0);
$t = fn($s) => trim(preg_replace('/\s+/', ' ', html_entity_decode(strip_tags((string)$s))));
foreach ($rows as $r) { printf("  %-32s %10s | %-24s %10s\n", $t($r['l_group_name'] ?? ''), $t($r['l_balance'] ?? ''), $t($r['r_group_name'] ?? ''), $t($r['r_balance'] ?? '')); }
$sum = $db->query("SELECT COALESCE(SUM(acc_op_bal),0) v FROM accoppybal WHERE cmpfymastr_id=3 AND hobo_id=1")->getRow()->v;
echo "  FY3 sum of opening balances = $sum\n";
