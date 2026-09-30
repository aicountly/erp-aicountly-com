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
// two bill-sundry (tax) ledgers placed directly under Indirect Expenses: the old condensed P&L leaves them out
sh($psql . " -c " . escapeshellarg("INSERT INTO acctmaster (acc_id, cmp_id, acc_name, bsd_id) VALUES (150,1,'Tax Output A',3),(151,1,'Tax Output B',4);
  INSERT INTO undercrsmt (cmp_id,cmpfymastr_id,crs_mst_type,crs_mst_id,under_crs_mst_id,crs_mst_parent_id,under_main_id,crs_mst_is_primary) VALUES (1,1,14,150,0,13,0,0),(1,1,14,151,0,13,0,0);
  INSERT INTO accttxnmst (cmp_id,hobo_id,acc_id,acc_txn_date,acc_txn_dr_cr,acc_txn_amt,acc_txn_type,txn_id,vch_txn_id) VALUES (1,1,110,'2025-07-01',1,2000,1,20,20),(1,1,150,'2025-07-01',2,1000,1,20,20),(1,1,151,'2025-07-01',2,1000,1,20,20);
  INSERT INTO vchtxnconso (vch_txn_id,cmp_id,hobo_id,txn_id,vch_type_id,vch_date) VALUES (20,1,1,20,1,'2025-07-01');"));
[$outBs, ] = $audit('--company 1 --fy 1 --branch 1 --no-excel');
ok(preg_match('/Indirect Expenses\s+[\d,]+\.\d\d\s+[\d,]+\.\d\d\s+-2,000\.00/', $outBs) === 1, 'old condensed P&L: two bill-sundry ledgers under Indirect Expenses are left out (Indirect Expenses 2,000.00 too high)');
ok(str_contains($outBs, 'bill-sundry (tax) ledgers that sit under a profit & loss category') && preg_match('/Tax Output A\s+Indirect Expenses\s+1,000\.00 Cr/', $outBs) === 1 && str_contains($outBs, 'Together they explain the change on: Indirect Expenses (-2,000.00)'), 'the audit names those ledgers and shows that together they explain the change');
sh("cd " . escapeshellarg($H) . " && ./reload_db.sh stress");
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

// ---- a journal with a ZERO GST PAID debit; a difference that two different sets of legs both explain (fixture_gst.sql) ----------------
sh("cd " . escapeshellarg($H) . " && ./reload_db.sh gst");
$before = $digest();
$csvG = tempnam(sys_get_temp_dir(), 'gst') . '.csv';
[$outG, $dG] = $audit('--company 1 --fy 1 --branch 1 --no-legacy --no-excel --csv ' . escapeshellarg($csvG));
ok($digest() === $before, 'the audit leaves the database unchanged (gst fixture)');
ok(($dG['verdict']['fail'] ?? -1) === 0 && abs(($dG['imbalance']['engine'] ?? 0) - 790.0) < 0.005, 'gst fixture: no FAIL, whole-ledger imbalance 790.00 Dr', json_encode($dG['imbalance'] ?? null));
ok(preg_match('/the credit legs on CGST INPUT \+ SGST INPUT \[GST PAID A\/C debit is 0\.00\]\s+1\s+710\.00 Cr/', $outG) === 1, 'the journal with a 0.00 GST PAID debit: the two tax credits equal the difference, and the zero debit is flagged');
ok(preg_match('/the debit legs on GST PAID A\/C\s+1\s+1,800\.00 Dr/', $outG) === 1 && str_contains($outG, '(1 group(s) fit more than one set of legs'), 'a difference that two sets of legs explain is counted once (first alternative) and the ambiguity is stated');
ok(preg_match('/where the counted legs sit:\s+Profit & Loss accounts Cr 0\.00 \/ Dr 1,800\.00 - as booked they lower the profit by 1,800\.00;\s+Balance Sheet accounts Cr 1,010\.00 \/ Dr 0\.00\s*\n/', $outG) === 1 && abs(($dG['linked_explained_effect']['profit_as_booked'] ?? 0) + 1800.0) < 0.005, 'where the counted legs sit: a GST PAID debit lowers the profit (-1,800.00); the tax credits (710.00 + 300.00) are Balance Sheet', json_encode($dG['linked_explained_effect'] ?? null));
$rowsG = []; if (is_file($csvG) && ($fh = fopen($csvG, 'r'))) { $head = fgetcsv($fh); while (($r = fgetcsv($fh)) !== false) { $rowsG[(int)$r[0]] = array_combine($head, $r); } fclose($fh); }
@unlink($csvG);
ok(preg_match('/the credit legs on CGST X\s+1\s+300\.00 Cr/', $outG) === 1 && ($rowsG[55]['group_difference_equals'] ?? '') === 'Cr CGST X 300.00', 'two identical credit lines on one account are ONE explanation, not an ambiguity', json_encode($rowsG[55] ?? null));
ok(($rowsG[53]['group_difference_equals'] ?? '') === 'Dr GST PAID A/C 1,800.00  OR  Dr CGST X 900.00 + SGST X 900.00', 'csv: voucher 53 lists both alternatives', json_encode($rowsG[53] ?? null));
ok(($rowsG[52]['group_difference_equals'] ?? '') === 'Cr CGST INPUT 355.00 + SGST INPUT 355.00 [the GST PAID A/C debit leg of this group is 0.00]' && ($rowsG[52]['linked_voucher_ids'] ?? '') === '51', 'csv: voucher 52 (journal linked to the sale by link type 4) - the two tax credits, zero GST PAID debit', json_encode($rowsG[52] ?? null));

// ---- linked vouchers: composition-scheme purchase + its GST PAID A/C system journal (fixture_pairs.sql) ----------------------
sh("cd " . escapeshellarg($H) . " && ./reload_db.sh pairs");
$before = $digest();
$csvFile = tempnam(sys_get_temp_dir(), 'unb') . '.csv';
[$out, $d] = $audit('--company 1 --fy 1 --branch 1 --no-legacy --no-excel --vouchers 2 --voucher 21,22,33,37,38 --csv ' . escapeshellarg($csvFile));
ok($digest() === $before, 'the audit leaves the database unchanged (linked vouchers, detail, csv)');
$l = $d['linked'] ?? [];
ok(($d['verdict']['fail'] ?? -1) === 0, 'no FAIL with linked vouchers', json_encode($d['verdict'] ?? null));
ok(abs(($d['imbalance']['engine'] ?? 0) + 15780.0) < 0.005 && abs(($d['imbalance']['raw_sql'] ?? 0) + 15780.0) < 0.005, 'whole-ledger imbalance = 15,780.00 Cr (engine and SQL)', json_encode($d['imbalance'] ?? null));
ok(($l['unbalanced_alone'] ?? 0) === 14 && ($l['linked'] ?? 0) === 7 && ($l['balance_with_link'] ?? 0) === 4, 'grouping: 14 unbalanced vouchers, 7 linked, 4 balance with their link', json_encode($l));
ok(($l['groups_still_unbalanced'] ?? 0) === 9 && abs(($l['net_still_unbalanced'] ?? 0) + 15780.0) < 0.005 && ($l['linked_outside_window'] ?? 0) === 2, '9 groups remain, together 15,780.00 Cr; 2 vouchers have their linked voucher outside the year', json_encode($l));
ok(preg_match('/11 Purchase\s+10\s+10\s+22,940\.00 Cr/', $out) === 1 && str_contains($out, 'unbalanced  of all'), 'by-type table: unbalanced purchases shown against all purchases of the year (10 of 10)');
ok(str_contains($out, 'branch GST registration type: 2 (composition scheme'), 'header: the branch GST registration type is shown (composition scheme)');
ok(str_contains($out, '7 of the 14 unbalanced voucher(s) are linked to another voucher; 4 of them balance once the linked voucher is added; 2 more are linked to a voucher outside this year'), 'the linked-voucher summary is printed');
ok(str_contains($out, '9 voucher group(s) still do not balance, together 15,780.00 Cr'), 'the real residual is printed');
foreach (['11 Purchase + 23 Composition GST journal', '11 Purchase (no linked voucher)', '23 Composition GST journal (no linked voucher)', '11 Purchase (linked voucher in another year)',
          '11 Purchase (journal rows inside the voucher)', '11 Purchase (linked journal has no ledger rows)'] as $kind) {
    ok(str_contains($out, $kind), "still-unbalanced kind named: $kind");
}
ok(preg_match('/11 Purchase \(no linked voucher\)\s+3\s+6,500\.00 Cr/', $out) === 1, 'the three unlinked purchases are summed in one line of the by-kind table (6,500.00 Cr)');
// what the differences equal
ok(preg_match('/the credit legs on CGST INPUT A\/C \+ SGST INPUT A\/C\s+1\s+1,800\.00 Cr/', $out) === 1, 'the difference of the linked pair 35+36 equals the credit legs on CGST INPUT + SGST INPUT (1,800.00)');
ok(preg_match('/the credit legs on Central Tax \(Output\) \+ State Tax \(Output\)\s+1\s+2,400\.00 Cr/', $out) === 1, 'the difference of the self-linked purchase 37 equals the credit legs on Central Tax + State Tax (2,400.00)');
ok(preg_match('/the credit legs on CESS INPUT A\/C \+ CGST INPUT A\/C \+ SGST INPUT A\/C\s+1\s+2,000\.00 Cr/', $out) === 1, 'the difference of purchase 40 equals THREE credit legs (CGST + SGST + cess = 2,000.00)');
ok(preg_match('/the debit legs on GST PAID A\/C\s+1\s+500\.00 Dr/', $out) === 1, 'the single-sided journal 27 equals its debit to GST PAID A/C (500.00)');
ok(preg_match('/\(no combination of up to 3 legs equals it: a leg is missing\)\s+5\s+10,080\.00 Cr/', $out) === 1, 'five groups have no combination of legs that equals the difference: a leg is missing (10,080.00)');
ok(preg_match('/CGST INPUT A\/C\s+yes\s+Cr 1,800\.00 in 2 group\(s\)\s+1,800\.00 Cr\s+Duties & Taxes \/ Current Liabilities/', $out) === 1, 'the accounts behind the legs: CGST INPUT A/C is a bill-sundry (tax) ledger, credited 1,800.00 in 2 groups, closing 1,800.00 Cr, placed under Duties & Taxes / Current Liabilities');
ok(preg_match('/GST PAID A\/C\s+Dr 500\.00 in 1 group\(s\)\s+13,360\.00 Dr\s+\(primary account\) \/ Indirect Expenses/', $out) === 1, 'the accounts behind the legs: GST PAID A/C (an Indirect Expenses ledger) with its closing balance');
ok(preg_match('/where the counted legs sit:\s+Profit & Loss accounts Cr 2,400\.00 \/ Dr 500\.00 - as booked they raise the profit by 1,900\.00;\s+Balance Sheet accounts Cr 3,800\.00 \/ Dr 0\.00\s*\n/', $out) === 1, 'where the counted legs sit: the two Output-tax credits (2,400.00) and the GST PAID debit (500.00) are inside the profit, net +1,900.00; the input-tax credits (3,800.00) are Balance Sheet');
// detail
ok(preg_match('/voucher 23   date 2025-09-10   type 11 Purchase   sub-type 5   series 3   branch 1/', $out) === 1, 'detail: header of voucher 23');
ok(preg_match('/Cr\s+59,000\.00\s+ABC Suppliers \(acc 103\)/', $out) === 1 && preg_match('/Dr\s+50,000\.00\s+Purchases \(acc 108\)/', $out) === 1, 'detail: the ledger rows of voucher 23 with account names');
ok(preg_match('/linked voucher 24 \(link type 3, this voucher is the source\): date 2025-09-10, type 23 Composition GST journal/', $out) === 1, 'detail: the linked system journal 24 is shown');
ok(preg_match('/together with the linked voucher\(s\): debit 54,500\.00\s+credit 59,000\.00\s+difference 4,500\.00 Cr/', $out) === 1, 'detail: voucher 23 + its journal are 4,500.00 Cr short');
ok(str_contains($out, 'Vouchers that balance only together with their linked voucher'), 'the "balances together" examples are shown for comparison');
ok(preg_match('/voucher 21: its debit\/credit difference \(1,800\.00 Cr\) equals the total tax in its GST summary \(1,800\.00\)/', $out) === 1, '--voucher 21: the difference equals the GST summary tax');
ok(str_contains($out, 'supplier bill no: INV-101') && str_contains($out, 'stock rows: 1, value 10,000.00') && preg_match('/GST summary \(vchgstsumn, 1 row\(s\)\): taxable 10,000\.00\s+IGST 1,800\.00/', $out) === 1, 'detail: bill number, stock rows and GST summary of voucher 21');
ok(preg_match('/GST summary \(vchgstsumn, 1 row\(s\)\): taxable 5,000\.00\s+IGST 500\.00.*total tax 500\.00/', $out) === 1 && !str_contains($out, 'voucher 33: its debit/credit difference'), '--voucher 33: a difference that is NOT the GST summary tax is not presented as if it were');
ok(preg_match('/voucher 25 .*linked voucher 27 \(link type 1, this voucher is the source\).*\[not a composition-scheme link: shown for information, not added\]/s', $out) === 1 && !preg_match('/voucher 25 [^\n]*\n(?:[^\n]*\n){0,12}?\s+together with the linked voucher/', $out), 'a type-1 link (credit note against invoice) is shown but never added to the voucher');
ok(preg_match('/voucher 22 .*type 23 Composition GST journal.*linked voucher 21 \(link type 3, this voucher is the destination\): date 2025-08-05, type 11 Purchase/s', $out) === 1, '--voucher 22: the link is followed from the destination side too');
$b37 = substr($out, (int)strrpos($out, 'voucher 37   date'), 1500); $b37 = substr($b37, 0, (int)strpos($b37, 'voucher 38   date') ?: 1500);
ok(str_contains($b37, 'linked to itself (link type 3): its GST PAID A/C journal rows are posted inside this same voucher') && !str_contains($b37, 'together with the linked voucher') && !str_contains($b37, 'linked voucher 37'), '--voucher 37: a voucher linked to itself is named as such and is not added to itself');
ok(str_contains($b37, 'the difference of this voucher group equals: Cr Central Tax (Output) 1,200.00 + State Tax (Output) 1,200.00'), '--voucher 37: the legs that equal the difference are named');
$b38 = substr($out, (int)strrpos($out, 'voucher 38   date'));
ok(preg_match('/linked voucher 39 \(link type 3, this voucher is the source\): date 2026-02-20, type 23 Composition GST journal, branch 1\s+\(no ledger rows\)/', $b38) === 1 && str_contains($b38, 'a leg is missing [linked journal has no ledger rows]'), '--voucher 38: the linked journal exists but has no ledger rows; a leg is missing');
[$out1, ] = $audit('--company 1 --fy 1 --branch 1 --no-legacy --no-excel --vouchers 1');
$cmp = substr($out1, (int)strpos($out1, 'for comparison'));
$shownIds = []; foreach ([21, 23, 25, 27, 29, 31, 33, 35, 37, 38, 40] as $vid) { if (preg_match('/voucher ' . $vid . ' {3}date/', $out1)) { $shownIds[] = $vid; } }
ok(preg_match('/voucher 21 /', $cmp) === 1 && $shownIds === [21, 23, 25, 27, 29, 37, 38], '--vouchers 1 shows the single largest group of each kind (25, 23, 37, 38, 27, 29) and one that balances together (21), and none of the smaller ones', json_encode($shownIds));
ok(str_contains($out1, '----- 11 Purchase (no linked voucher): 3 group(s), 6,500.00 Cr -----') && str_contains($out1, '(1 per kind, largest first)'), '--vouchers: each kind is introduced with its group count and net');
$rows = []; if (is_file($csvFile) && ($fh = fopen($csvFile, 'r'))) { $head = fgetcsv($fh); while (($r = fgetcsv($fh)) !== false) { $rows[(int)$r[0]] = array_combine($head, $r); } fclose($fh); }
@unlink($csvFile);
ok(count($rows) === 14, 'csv: one row per unbalanced voucher (14)', (string)count($rows));
ok(($rows[21]['status'] ?? '') === 'balances together with linked voucher' && ($rows[21]['linked_voucher_ids'] ?? '') === '22' && ($rows[21]['diff_debit_minus_credit'] ?? '') === '-1800.00'
   && ($rows[21]['largest_leg_account'] ?? '') === 'ABC Suppliers' && ($rows[21]['gst_summary_total_tax'] ?? '') === '1800.00' && ($rows[21]['supplier_bill_no'] ?? '') === 'INV-101', 'csv: voucher 21 row', json_encode($rows[21] ?? null));
ok(($rows[23]['status'] ?? '') === 'STILL UNBALANCED with linked voucher' && ($rows[23]['group_diff'] ?? '') === '-4500.00', 'csv: voucher 23 still unbalanced with its journal', json_encode($rows[23] ?? null));
ok(($rows[25]['status'] ?? '') === 'STILL UNBALANCED (no linked voucher)' && ($rows[27]['status'] ?? '') === 'STILL UNBALANCED (no linked voucher)' && ($rows[27]['diff_debit_minus_credit'] ?? '') === '500.00'
   && ($rows[27]['group_difference_equals'] ?? '') === 'Dr GST PAID A/C 500.00', 'csv: vouchers without a link; the single-sided journal equals its debit to GST PAID A/C', json_encode([$rows[25] ?? null, $rows[27] ?? null]));
ok(($rows[29]['status'] ?? '') === 'STILL UNBALANCED (linked voucher in another year)', 'csv: linked voucher in another year', json_encode($rows[29] ?? null));
ok(($rows[35]['status'] ?? '') === 'STILL UNBALANCED with linked voucher' && ($rows[35]['group_difference_equals'] ?? '') === 'Cr CGST INPUT A/C 900.00 + SGST INPUT A/C 900.00' && ($rows[35]['linked_voucher_ids'] ?? '') === '36', 'csv: voucher 35 (linked journal balances by itself) - the difference equals the two tax credits', json_encode($rows[35] ?? null));
ok(($rows[37]['status'] ?? '') === 'STILL UNBALANCED (journal rows inside the voucher)' && ($rows[37]['group_difference_equals'] ?? '') === 'Cr Central Tax (Output) 1,200.00 + State Tax (Output) 1,200.00', 'csv: voucher 37 (self-linked) - journal rows inside the voucher', json_encode($rows[37] ?? null));
ok(($rows[40]['status'] ?? '') === 'STILL UNBALANCED (no linked voucher)' && ($rows[40]['group_difference_equals'] ?? '') === 'Cr CGST INPUT A/C 900.00 + SGST INPUT A/C 900.00 + CESS INPUT A/C 200.00', 'csv: voucher 40 - three credit legs, largest first', json_encode($rows[40] ?? null));
ok(($rows[38]['status'] ?? '') === 'STILL UNBALANCED (linked journal has no ledger rows)' && str_contains($rows[38]['group_difference_equals'] ?? '', 'a leg is missing'), 'csv: voucher 38 - linked journal without ledger rows', json_encode($rows[38] ?? null));
// the same ledger with every counted leg on a Balance Sheet account, and one in no group at all
sh($psql . " -c " . escapeshellarg("UPDATE undercrsmt SET under_crs_mst_id=13, crs_mst_parent_id=4, under_main_id=13, crs_mst_is_primary=0 WHERE crs_mst_type=1 AND crs_mst_id IN (117,143,144); DELETE FROM undercrsmt WHERE crs_mst_type=1 AND crs_mst_id=145;"));
[$outN, $dN] = $audit('--company 1 --fy 1 --branch 1 --no-legacy --no-excel');
ok(preg_match('/where the counted legs sit:\s+Profit & Loss accounts Cr 0\.00 \/ Dr 0\.00 - as booked they do not change the profit;\s+Balance Sheet accounts Cr 6,000\.00 \/ Dr 500\.00;\s+in no group: Cr 200\.00 \/ Dr 0\.00\s*\n/', $outN) === 1 && isset($dN['linked_explained_effect']['profit_as_booked']) && abs($dN['linked_explained_effect']['profit_as_booked']) < 0.005, 'where the counted legs sit: none on Profit & Loss (profit not changed), the CESS credit (200.00) is in no group', json_encode($dN['linked_explained_effect'] ?? null));
// where a voucher came from. A system-generated voucher has no linked voucher, so the series, the narration
// and the registers it was written to are the only trail back to whatever made it.
[$outC, $dC] = $audit('--company 1 --fy 1 --branch 1 --no-legacy --no-excel --voucher 23');
ok(preg_match('/series name: PURCHASE-MAIN/', $outC) === 1, 'the voucher detail names the series the voucher belongs to',
   substr($outC, (int)strpos($outC, 'voucher 23 '), 400));
ok(preg_match('/narration: Composition purchase from ABC Suppliers$/m', $outC) === 1,
   'and its narration, with the markup and the runs of spaces taken out', substr($outC, (int)strpos($outC, 'voucher 23 '), 400));
ok(preg_match('/register \(acctvchreg\), 3 row\(s\), columns: acct_vch_reg_id, acct_vch_type, cmp_id/', $outC) === 1,
   'the register rows are listed, with the columns this installation actually has',
   substr($outC, (int)strpos($outC, 'voucher 23 '), 900));
ok(preg_match('/acct_vch_type=3\s+vch_date=2025-09-10\s+acc_id=108\s+acc_txn_dr_amt=50000\.00.*Purchases/', $outC) === 1,
   'each row shows every field it carries, and the ledger name behind the account id',
   substr($outC, (int)strpos($outC, 'voucher 23 '), 900));
ok(!preg_match('/\bcmp_id=/', substr($outC, (int)strpos($outC, 'register (acctvchreg)'), 400)), 'the columns that only repeat the query are left out');
ok(!str_contains(substr($outC, (int)strpos($outC, 'voucher 23 ')), 'no ledger row in this voucher'),
   'voucher 23: every register account has a ledger row, so nothing is flagged');
[$outE, $dE] = $audit('--company 1 --fy 1 --branch 1 --no-legacy --no-excel --voucher 25');
ok(preg_match('/accounts in the register that have no ledger row in this voucher: CGST INPUT A\/C \(acc 141\)/', $outE) === 1,
   'an account the register carries but the ledger never got is named: that is the leg that went missing',
   substr($outE, (int)strpos($outE, 'voucher 25 '), 700));
ok(($dE['voucher_detail'][0]['register_only_accounts'][0] ?? '') === 'CGST INPUT A/C (acc 141)', 'and it is in the JSON',
   json_encode($dE['voucher_detail'][0]['register_only_accounts'] ?? null));
ok(($dC['voucher_detail'][0]['series_name'] ?? '') === 'PURCHASE-MAIN'
   && str_contains((string)($dC['voucher_detail'][0]['narration'] ?? ''), 'ABC Suppliers')
   && count($dC['voucher_detail'][0]['register'] ?? []) === 3, 'the same three are in the JSON', json_encode($dC['voucher_detail'][0] ?? null));
// An installation whose register keeps different columns (this is what production turned out to have):
// the rows must still be printed and the account with no ledger row must still be named.
sh($psql . " -c " . escapeshellarg("ALTER TABLE acctvchreg DROP COLUMN acc_txn_dr_amt, DROP COLUMN acc_txn_cr_amt, DROP COLUMN acc_txn_type"));
[$outF, $dF] = $audit('--company 1 --fy 1 --branch 1 --no-legacy --no-excel --voucher 25');
ok(($dF['verdict']['fail'] ?? -1) === 0 && preg_match('/register \(acctvchreg\), 2 row\(s\), columns: /', $outF) === 1
   && preg_match('/accounts in the register that have no ledger row in this voucher: CGST INPUT A\/C \(acc 141\)/', $outF) === 1,
   'a register with a different set of columns is still read, and still names the leg that went missing',
   substr($outF, (int)strpos($outF, 'voucher 25 '), 700));

sh($psql . " -c " . escapeshellarg("DROP TABLE vchseriesn, vchlongnar, acctvchreg"));
[$outD, $dD] = $audit('--company 1 --fy 1 --branch 1 --no-legacy --no-excel --voucher 23');
ok(($dD['verdict']['fail'] ?? -1) === 0 && preg_match('/voucher 23 /', $outD) === 1
   && !str_contains($outD, 'series name:') && !str_contains($outD, 'narration:') && !str_contains($outD, 'register ('),
   'with those three tables absent the voucher is still shown and nothing fails', substr($outD, (int)strpos($outD, 'voucher 23 '), 300));

// --ledger: what a ledger opened with, what moved through it and what it closes at - the view needed to
// decide whether two ledgers under different names are the same account.
[$outG, $dG] = $audit('--company 1 --fy 1 --branch 1 --no-legacy --no-excel --ledger 103,108,999');
ok(preg_match('/2c  Ledgers asked for with --ledger/', $outG) === 1, 'the section is printed');
ok(preg_match('/103\s+ABC Suppliers\s+80,000\.00 Cr\s+\d+\s+210,940\.00 Cr\s+290,940\.00 Cr\s+2025-04-15\s+2026-02-25\s+Sundry Creditors \/ Current Liabilities/', $outG) === 1,
   'a ledger shows its opening, movement, closing, first and last entry, and where the report places it',
   substr($outG, (int)strpos($outG, '2c  Ledgers'), 700));
ok(preg_match('/108\s+Purchases\s+0\.00\s+\d+\s+183,000\.00 Dr\s+183,000\.00 Dr/', $outG) === 1,
   'a ledger with no opening is shown as 0.00, not blank', substr($outG, (int)strpos($outG, '2c  Ledgers'), 700));
ok(preg_match('/999\s+\(not in the account master\).*no balance and no rows in this year/', $outG) === 1,
   'an id that is not a ledger of this company is said so rather than guessed at',
   substr($outG, (int)strpos($outG, '2c  Ledgers'), 700));
ok(count($dG['ledger_detail'] ?? []) === 3
   && abs((float)($dG['ledger_detail'][0]['opening_raw'] ?? 0) + 80000.0) < 0.005
   && abs((float)($dG['ledger_detail'][0]['closing_raw'] ?? 0) + 290940.0) < 0.005
   && array_key_exists('closing_raw', $dG['ledger_detail'][2]) && $dG['ledger_detail'][2]['closing_raw'] === null,
   'and the same three are in the JSON, as numbers, with null for the ledger that does not exist',
   json_encode($dG['ledger_detail'] ?? null));
ok($dG['ledger_detail'][1]['bsd'] === '' && ($audit('--company 1 --fy 1 --branch 1 --no-legacy --no-excel --ledger 141')[1]['ledger_detail'][0]['bsd'] ?? '') === 'yes',
   'a bill-sundry (tax) ledger is marked as one');
// "movement" means this financial year, start to report date - not the report period. With a later --from
// the period movement would be smaller; the column must not change.
[$outH, $dH] = $audit('--company 1 --fy 1 --branch 1 --no-legacy --no-excel --from 2026-01-01 --ledger 103');
ok(preg_match('/103\s+ABC Suppliers\s+80,000\.00 Cr\s+\d+\s+210,940\.00 Cr\s+290,940\.00 Cr/', $outH) === 1,
   'the movement is the whole financial year to date even when the report period starts later',
   substr($outH, (int)strpos($outH, '2c  Ledgers'), 500));

// the same ledger id twice is one row, not two
[$outI, $dI] = $audit('--company 1 --fy 1 --branch 1 --no-legacy --no-excel --ledger 103,103,108');
ok(count($dI['ledger_detail'] ?? []) === 2, 'a ledger asked for twice is listed once', json_encode(array_column($dI['ledger_detail'] ?? [], 'id')));

// a single-branch run counts only its own branch's rows
sh($psql . " -c " . escapeshellarg("INSERT INTO accttxnmst (cmp_id,hobo_id,acc_id,acc_txn_date,acc_txn_dr_cr,acc_txn_amt,acc_txn_type,txn_id,vch_txn_id)
   VALUES (1,2,103,'2025-12-01',1,55555,1,23,23)"));
[$outJ, $dJ] = $audit('--company 1 --fy 1 --branch 1 --no-legacy --no-excel --ledger 103');
ok(($dJ['ledger_detail'][0]['>rows'] ?? '') === ($dG['ledger_detail'][0]['>rows'] ?? 'x')
   && abs((float)($dJ['ledger_detail'][0]['closing_raw'] ?? 0) + 290940.0) < 0.005,
   'a row of the same ledger in another branch is left out of a single-branch run',
   json_encode([$dJ['ledger_detail'][0] ?? null, $dG['ledger_detail'][0]['>rows'] ?? null]));
sh($psql . " -c " . escapeshellarg("DELETE FROM accttxnmst WHERE hobo_id = 2 AND acc_txn_amt = 55555"));

// the figures must be the report's own, not a second calculation
$tbTot = 0.0;
foreach (($dG['ledger_detail'] ?? []) as $L) { if ($L['closing_raw'] !== null) { $tbTot += $L['closing_raw']; } }
ok(abs($tbTot - (-80000.0 - 210940.0 + 183000.0)) < 0.005, 'closing = opening + movement for every ledger shown', (string)$tbTot);

// optional tables missing (an older database, a different build): rows still shown, nothing fails
sh($psql . " -c " . escapeshellarg("DROP TABLE vchgstsumn, gstrinwsup, itemtxnmst"));
[$out2, $d2] = $audit('--company 1 --fy 1 --branch 1 --no-legacy --no-excel --vouchers 1');
ok(($d2['verdict']['fail'] ?? -1) === 0 && preg_match('/voucher 23 /', $out2) === 1 && preg_match('/Cr\s+59,000\.00\s+ABC Suppliers/', $out2) === 1, 'GST summary / stock / bill tables missing: the ledger rows are still shown and nothing fails');
sh($psql . " -c " . escapeshellarg("DROP TABLE vchbridgen"));
[$out3, $d3] = $audit('--company 1 --fy 1 --branch 1 --no-legacy --no-excel --vouchers 1');
ok(($d3['verdict']['fail'] ?? -1) === 0 && str_contains($out3, "'vchbridgen' could not be read here"), 'link table missing: reported, nothing fails');

echo "\nchecks passed: $pass | failed: $fail\n"; exit($fail ? 1 : 0);
