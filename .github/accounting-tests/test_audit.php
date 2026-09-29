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
$digest = fn() => trim(sh($psql . " -c " . escapeshellarg("select md5(string_agg(t,'|' order by t)) from (select 'a'||md5(x::text) t from accttxnmst x union all select 'b'||md5(x::text) from accoppybal x union all select 'c'||md5(x::text) from undercrsmt x union all select 'd'||md5(x::text) from acctmaster x union all select 'e'||md5(x::text) from vchtxnconso x union all select 'f'||md5(x::text) from itmoppyval x) z")));
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

echo "\nchecks passed: $pass | failed: $fail\n"; exit($fail ? 1 : 0);
