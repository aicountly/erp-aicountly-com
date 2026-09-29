<?php
/**
 * SANDBOX-ONLY: runs the real audit:books command (via run_audit.php) and checks what it reports.
 *   stress ledger   : the ledger imbalance / difference in opening it reports equal the hand-known figures; no FAIL; xlsx == page
 *   rollover ledger : with data as the OLD carry-forward wrote it (emulated by SQL) the attribution of 'Difference in Opening'
 *                     names both causes and adds up; with data from the fixed carry-forward everything is zero.
 * Also: the database is byte-identical before and after (the audit is read only).
 */
$H = __DIR__; $fail = 0; $pass = 0;
function ok(bool $c, string $l, string $d = ''): void { global $fail, $pass; if ($c) { $pass++; } else { $fail++; echo "  FAIL: $l $d\n"; } }
function sh(string $cmd): string { return (string)shell_exec('(' . $cmd . ') 2>&1'); }
$port = getenv('ERP_TEST_PGPORT') ?: '5433';
$psql = "psql -h 127.0.0.1 -p $port -U postgres -d erp_test -At";
$digest = fn() => trim(sh($psql . " -c " . escapeshellarg("select md5(string_agg(t,'|' order by t)) from (select 'a'||md5(x::text) t from accttxnmst x union all select 'b'||md5(x::text) from accoppybal x union all select 'c'||md5(x::text) from undercrsmt x union all select 'd'||md5(x::text) from acctmaster x union all select 'e'||md5(x::text) from vchtxnconso x union all select 'f'||md5(x::text) from itmoppyval x union all select 'g'||md5(x::text) from vchbridgen x union all select 'h'||md5(x::text) from vchgstsumn x union all select 'i'||md5(x::text) from gstrinwsup x union all select 'j'||md5(x::text) from itemtxnmst x) z")));
$audit = function (string $args, string $env = '') use ($H): array {
    $json = tempnam(sys_get_temp_dir(), 'aud') . '.json';
    $out = sh("cd " . escapeshellarg($H) . " && $env php run_audit.php $args --json " . escapeshellarg($json));
    $d = is_file($json) ? json_decode(file_get_contents($json), true) : null; @unlink($json);
    return [$out, $d ?? []];
};

// ---- stress ledger -------------------------------------------------------------------------------------------
sh("cd " . escapeshellarg($H) . " && ./reload_db.sh stress");
$before = $digest();
[$out, $d] = $audit('--company 1 --fy 1 --branch 1 --no-legacy');
ok($digest() === $before, 'the audit leaves the database unchanged');
ok(($d['verdict']['fail'] ?? -1) === 0, 'no FAIL: the corrected code agrees with independent SQL', '(' . json_encode($d['verdict'] ?? null) . ')');
ok(abs(($d['imbalance']['engine'] ?? 0) - 300.0) < 0.005 && abs(($d['imbalance']['raw_sql'] ?? 0) - 300.0) < 0.005, 'ledger imbalance = 300.00 Dr by engine and by SQL', json_encode($d['imbalance'] ?? null));
ok(abs(($d['openings']['difference'] ?? 0) + 5000.0) < 0.005, "'Difference in Opening' = 5,000.00 Cr", json_encode($d['openings'] ?? null));
ok(($d['excel']['differences'] ?? -1) === 0 && ($d['excel']['files'] ?? 0) === 30, '30 workbooks/CSVs read back equal the page rows', json_encode($d['excel'] ?? null));
ok(str_contains($out, 'GST PAID A/C system journals') && str_contains($out, '1,000.00 Dr'), 'the GST PAID A/C journals are named and quantified (1,000.00 Dr)');
ok(str_contains($out, 'Composition GST journal'), 'the voucher-type breakdown names type 23');
// the old-vs-corrected comparison, line by line (condensed Profit & Loss): equal here; then the SANDBOX copy of the old code is
// nudged (+1,000 on the Sales line) to prove the report names the line that moved
[$outL, ] = $audit('--company 1 --fy 1 --branch 1 --no-excel');
ok(str_contains($outL, 'line by line: the old code and the corrected one agree on every line'), 'old vs corrected P&L (condensed): every line agrees on this ledger');
$sbx = (getenv('SBX') ?: '/var/tmp/erp_sbx') . '/app/Models/Admin/ReportingModel.php';
$orig = (string)file_get_contents($sbx);
$fn = (int)strpos($orig, 'public function legacy_load_profit_loss_horizontal');
$needle = "\$bal = parseAmount(\$row['balance'] ?? 0);";
$at = (int)strpos($orig, $needle, $fn);
if ($fn > 0 && $at > $fn) {
    file_put_contents($sbx, substr($orig, 0, $at) . "\$bal = parseAmount(\$row['balance'] ?? 0) + (\$parentId == 8 ? 1000 : 0);" . substr($orig, $at + strlen($needle)));
    [$outM, ] = $audit('--company 1 --fy 1 --branch 1 --no-excel');
    file_put_contents($sbx, $orig);
    ok(preg_match('/Sales\s+99,700\.00\s+100,700\.00\s+\+1,000\.00/', $outM) === 1 && str_contains($outM, 'the lines the correction changed'), 'old vs corrected P&L (condensed): the line that moved is named with old / corrected / change', substr($outM, (int)strpos($outM, 'Profit & Loss (condensed'), 400));
} else { ok(false, 'could not patch the sandbox copy of the old P&L code for the line-by-line test'); }
[$outC, $dC] = $audit('--company 1 --fy 1 --consolidated --no-legacy --no-excel');
ok(($dC['verdict']['fail'] ?? -1) === 0, 'consolidated run: no FAIL');
[$outNo, ] = $audit('--company 1 --fy 1');
ok(str_contains($outNo, 'Give --branch ID'), 'a company with two branches asks for --branch');
[$outBad, ] = $audit('--company 1 --fy 99 --branch 1');
ok(str_contains($outBad, 'was not found'), 'an unknown financial year is refused');

// ---- roll-over ledger, data as the OLD carry-forward wrote it ------------------------------------------------
sh("cd " . escapeshellarg($H) . " && ./reload_db.sh rollover && php repair_rehearsal.php roll");
[$out, $d] = $audit('--company 1 --fy 3 --branch 1 --no-legacy --no-excel', 'STOCK_OPEN=0 STOCK_CLOSE=0');
ok(abs($d['carry']['difference'] ?? 1) < 0.005 && abs($d['carry']['explained'] ?? 1) < 0.005, 'data from the fixed carry-forward: nothing to explain', json_encode($d['carry'] ?? null));
sh($psql . " -c " . escapeshellarg("UPDATE accoppybal SET acc_op_bal=40, acc_py_bal=40 WHERE cmpfymastr_id=3 AND acc_id=107; INSERT INTO accoppybal (cmp_id,cmpfymastr_id,acc_id,hobo_id,acc_op_bal) VALUES (1,3,104,1,100),(1,3,105,1,-50);"));
$before = $digest();
[$out, $d] = $audit('--company 1 --fy 3 --branch 1 --no-legacy --no-excel', 'STOCK_OPEN=0 STOCK_CLOSE=0');
$c = $d['carry'] ?? [];
ok($digest() === $before, 'the audit leaves the database unchanged (roll-over ledger)');
ok(abs(($c['pl_ledgers'] ?? 0) - 50.0) < 0.005, 'attribution: P&L ledgers carried in = 50.00 Dr', json_encode($c));
ok(abs(($c['appropriation'] ?? 0) - 420.0) < 0.005, 'attribution: P&L Appropriation off by 420.00 (sign of the accumulated balance reversed)');
ok(abs(($c['difference'] ?? 0) - 470.0) < 0.005 && abs(($c['explained'] ?? 0) - 470.0) < 0.005, "attribution adds up to the whole 'Difference in Opening' (470.00)");
ok(str_contains($out, 'equals what the OLD carry-forward formula stores'), 'the audit recognises the old formula');

// ---- linked vouchers: composition-scheme purchase + its GST PAID A/C system journal (fixture_pairs.sql) ----------------------
sh("cd " . escapeshellarg($H) . " && ./reload_db.sh pairs");
$before = $digest();
$csvFile = tempnam(sys_get_temp_dir(), 'unb') . '.csv';
[$out, $d] = $audit('--company 1 --fy 1 --branch 1 --no-legacy --no-excel --vouchers 2 --voucher 21,22,33 --csv ' . escapeshellarg($csvFile));
ok($digest() === $before, 'the audit leaves the database unchanged (linked vouchers, detail, csv)');
$l = $d['linked'] ?? [];
ok(($d['verdict']['fail'] ?? -1) === 0, 'no FAIL with linked vouchers', json_encode($d['verdict'] ?? null));
ok(abs(($d['imbalance']['engine'] ?? 0) + 8680.0) < 0.005 && abs(($d['imbalance']['raw_sql'] ?? 0) + 8680.0) < 0.005, 'whole-ledger imbalance = 8,680.00 Cr (engine and SQL)', json_encode($d['imbalance'] ?? null));
ok(($l['unbalanced_alone'] ?? 0) === 10 && ($l['linked'] ?? 0) === 6 && ($l['balance_with_link'] ?? 0) === 4, 'grouping: 10 unbalanced vouchers, 6 linked, 4 balance with their link', json_encode($l));
ok(($l['groups_still_unbalanced'] ?? 0) === 5 && abs(($l['net_still_unbalanced'] ?? 0) + 8680.0) < 0.005 && ($l['linked_outside_window'] ?? 0) === 1, '5 groups remain, together 8,680.00 Cr; 1 linked voucher lies outside the year', json_encode($l));
ok(preg_match('/11 Purchase\s+6\s+6\s+15,840\.00 Cr/', $out) === 1 && str_contains($out, 'unbalanced  of all'), 'by-type table: unbalanced purchases shown against all purchases of the year (6 of 6)');
ok(str_contains($out, 'branch GST registration type: 2 (composition scheme'), 'header: the branch GST registration type is shown (composition scheme)');
ok(str_contains($out, '6 of the 10 unbalanced voucher(s) are linked to another voucher; 4 of them balance once the linked voucher is added'), 'the linked-voucher summary is printed');
ok(str_contains($out, '5 voucher group(s) still do not balance, together 8,680.00 Cr'), 'the real residual is printed');
foreach (['11 Purchase + 23 Composition GST journal', '11 Purchase (no linked voucher)', '23 Composition GST journal (no linked voucher)', '11 Purchase (linked voucher outside this year)'] as $kind) {
    ok(str_contains($out, $kind), "still-unbalanced kind named: $kind");
}
ok(preg_match('/voucher 23   date 2025-09-10   type 11 Purchase   sub-type 5   series 3   branch 1/', $out) === 1, 'detail: header of voucher 23');
ok(preg_match('/Cr\s+59,000\.00\s+ABC Suppliers \(acc 103\)/', $out) === 1 && preg_match('/Dr\s+50,000\.00\s+Purchases \(acc 108\)/', $out) === 1, 'detail: the ledger rows of voucher 23 with account names');
ok(preg_match('/linked voucher 24 \(link type 3, this voucher is the source\): date 2025-09-10, type 23 Composition GST journal/', $out) === 1, 'detail: the linked system journal 24 is shown');
ok(preg_match('/together with the linked voucher\(s\): debit 54,500\.00\s+credit 59,000\.00\s+difference 4,500\.00 Cr/', $out) === 1, 'detail: voucher 23 + its journal are 4,500.00 Cr short');
ok(str_contains($out, 'Vouchers that balance only together with their linked voucher'), 'the "balances together" examples are shown for comparison');
ok(preg_match('/voucher 21: its debit\/credit difference \(1,800\.00 Cr\) equals the total tax in its GST summary \(1,800\.00\)/', $out) === 1, '--voucher 21: the difference equals the GST summary tax');
ok(str_contains($out, 'supplier bill no: INV-101') && str_contains($out, 'stock rows: 1, value 10,000.00') && preg_match('/GST summary \(vchgstsumn, 1 row\(s\)\): taxable 10,000\.00\s+IGST 1,800\.00/', $out) === 1, 'detail: bill number, stock rows and GST summary of voucher 21');
ok(preg_match('/GST summary \(vchgstsumn, 1 row\(s\)\): taxable 5,000\.00\s+IGST 500\.00.*total tax 500\.00/', $out) === 1 && !str_contains($out, 'voucher 33: its debit/credit difference'), '--voucher 33: a difference that is NOT the GST summary tax is not presented as if it were');
ok(preg_match('/11 Purchase \(no linked voucher\)\s+2\s+4,500\.00 Cr/', $out) === 1, 'the two unlinked purchases are summed in one line of the by-kind table (4,500.00 Cr)');
ok(preg_match('/voucher 25 .*linked voucher 27 \(link type 1, this voucher is the source\).*\[not a composition-scheme link: shown for information, not added\]/s', $out) === 1 && !preg_match('/voucher 25 [^\n]*\n(?:[^\n]*\n){0,12}?\s+together with the linked voucher/', $out), 'a type-1 link (credit note against invoice) is shown but never added to the voucher');
ok(preg_match('/voucher 22 .*type 23 Composition GST journal.*linked voucher 21 \(link type 3, this voucher is the destination\): date 2025-08-05, type 11 Purchase/s', $out) === 1, '--voucher 22: the link is followed from the destination side too');
[$out1, ] = $audit('--company 1 --fy 1 --branch 1 --no-legacy --no-excel --vouchers 1');
$cmp = substr($out1, (int)strpos($out1, 'for comparison'));
ok(preg_match('/voucher 21 /', $cmp) === 1 && !str_contains($out1, 'voucher 31 ') && preg_match('/voucher 23 /', $out1) === 1 && !str_contains($out1, 'voucher 33 '), '--vouchers 1 shows only the single largest of each kind (still unbalanced: 23, balanced together: 21)');
$rows = []; if (is_file($csvFile) && ($fh = fopen($csvFile, 'r'))) { $head = fgetcsv($fh); while (($r = fgetcsv($fh)) !== false) { $rows[(int)$r[0]] = array_combine($head, $r); } fclose($fh); }
@unlink($csvFile);
ok(count($rows) === 10, 'csv: one row per unbalanced voucher (10)', (string)count($rows));
ok(($rows[21]['status'] ?? '') === 'balances together with linked voucher' && ($rows[21]['linked_voucher_ids'] ?? '') === '22' && ($rows[21]['diff_debit_minus_credit'] ?? '') === '-1800.00'
   && ($rows[21]['largest_leg_account'] ?? '') === 'ABC Suppliers' && ($rows[21]['gst_summary_total_tax'] ?? '') === '1800.00' && ($rows[21]['supplier_bill_no'] ?? '') === 'INV-101', 'csv: voucher 21 row', json_encode($rows[21] ?? null));
ok(($rows[23]['status'] ?? '') === 'STILL UNBALANCED with linked voucher' && ($rows[23]['group_diff'] ?? '') === '-4500.00', 'csv: voucher 23 still unbalanced with its journal', json_encode($rows[23] ?? null));
ok(($rows[25]['status'] ?? '') === 'STILL UNBALANCED (no linked voucher)' && ($rows[27]['status'] ?? '') === 'STILL UNBALANCED (no linked voucher)' && ($rows[27]['diff_debit_minus_credit'] ?? '') === '500.00', 'csv: vouchers without a link', json_encode([$rows[25] ?? null, $rows[27] ?? null]));
ok(($rows[29]['status'] ?? '') === 'STILL UNBALANCED (linked voucher outside this year)', 'csv: linked voucher in another year', json_encode($rows[29] ?? null));
// optional tables missing (an older database, a different build): rows still shown, nothing fails
sh($psql . " -c " . escapeshellarg("DROP TABLE vchgstsumn, gstrinwsup, itemtxnmst"));
[$out2, $d2] = $audit('--company 1 --fy 1 --branch 1 --no-legacy --no-excel --vouchers 1');
ok(($d2['verdict']['fail'] ?? -1) === 0 && preg_match('/voucher 23 /', $out2) === 1 && preg_match('/Cr\s+59,000\.00\s+ABC Suppliers/', $out2) === 1, 'GST summary / stock / bill tables missing: the ledger rows are still shown and nothing fails');
sh($psql . " -c " . escapeshellarg("DROP TABLE vchbridgen"));
[$out3, $d3] = $audit('--company 1 --fy 1 --branch 1 --no-legacy --no-excel --vouchers 1');
ok(($d3['verdict']['fail'] ?? -1) === 0 && str_contains($out3, "'vchbridgen' could not be read here"), 'link table missing: reported, nothing fails');

echo "\nchecks passed: $pass | failed: $fail\n"; exit($fail ? 1 : 0);
