<?php
/**
 * SANDBOX-ONLY: render the REAL report views through the REAL Reportings controller (header/footer includes stubbed)
 * and check: no PHP warnings, banner shown only when the ledger is out of balance, export URL built by the page's JS.
 */
require __DIR__ . '/boot.php';
$SBX = getenv('SBX') ?: '/var/tmp/erp_sbx';
@mkdir("$SBX/app/Views/includes", 0777, true);
file_put_contents("$SBX/app/Views/includes/header.php", "<html><head><title><?= \$title ?? '' ?></title></head><body>\n");
file_put_contents("$SBX/app/Views/includes/footer_scripts.php", "</body></html>\n");

$fail = 0; $pass = 0;
function ok(bool $c, string $l, string $d = ''): void { global $fail, $pass; if ($c) { $pass++; } else { $fail++; echo "  FAIL: $l $d\n"; } }

set_error_handler(function ($no, $str, $file, $line) { if (!($no & (E_DEPRECATED | E_USER_DEPRECATED))) { throw new ErrorException("$str @ " . basename($file) . ":$line", 0, $no, $file, $line); } return true; });

$cases = [
  ['balance_sheet',  ['view' => 1, 'format' => 1, 'nil_type' => 1, 'consolidated' => 0], 'balance_sheet_horizontal'],
  ['balance_sheet',  ['view' => 2, 'format' => 2, 'nil_type' => 0, 'consolidated' => 1], 'balance_sheet_vertical'],
  ['profit_loss',    ['view' => 1, 'format' => 1, 'nil_type' => 1, 'consolidated' => 0], 'profit_loss_horizontal'],
  ['profit_loss',    ['view' => 0, 'format' => 2, 'nil_type' => 1, 'consolidated' => 0], 'profit_loss_vertical'],
  ['trial_balance',  ['view' => 0, 'nil_type' => 0, 'consolidated' => 0], 'trial_balance'],
  ['trial_balance',  ['view' => 2, 'nil_type' => 1, 'consolidated' => 1], 'trial_balance'],
  ['balance_sheet',  [], 'balance_sheet_horizontal'],
];
$db = harness_db();
$imbalanced = abs((float)$db->query("SELECT COALESCE(SUM(CASE WHEN acc_txn_dr_cr=1 THEN acc_txn_amt ELSE -acc_txn_amt END),0) t FROM accttxnmst WHERE cmp_id=1 AND acc_txn_type=1 AND vch_txn_id>0")->getRow()->t) > 0.005;
echo "ledger " . ($imbalanced ? "is OUT of balance (stress fixture)" : "balances (basic fixture)") . "\n";

foreach ($cases as [$method, $get, $viewFile]) {
    $_GET = $get;
    \Config\Services::reset(true);                           // real renderer again
    harness_session();
    $ctl = (new ReflectionClass(\App\Controllers\Admin\Reportings::class))->newInstanceWithoutConstructor();
    $ctl->ReportingModel = harness_reporting();
    $ctl->folder_path = 'admin/'; $ctl->base_url = 'http://sandbox/admin/'; $ctl->bo_id = 1;
    $tag = "$method " . json_encode($get);
    try { $html = $ctl->$method(); } catch (Throwable $e) { ok(false, "$tag renders", $e->getMessage()); continue; }
    ok(strlen($html) > 5000, "$tag renders a full page", '(' . strlen($html) . ' bytes)');
    $hasBanner = str_contains($html, 'id="recon_notes"');
    if ($method === 'profit_loss') { ok(!$hasBanner, "$tag has no 'does not tally' banner"); }
    else                           { ok($hasBanner === $imbalanced, "$tag banner " . ($imbalanced ? 'shown' : 'hidden') . ' as the ledger dictates'); }
    if ($hasBanner) { ok(str_contains($html, 'does not tally because the ledger itself is out of balance'), "$tag banner text"); }
    // pull EXPORT_QS + the helper functions out of the page and run them
    ok(preg_match('/var EXPORT_QS = ("[^"]*");/', $html, $m) === 1, "$tag page defines EXPORT_QS");
    $qs = json_decode($m[1] ?? '""');
    $js = "var baseurl='http://x/'; var window={location:{search:'',href:''}};\n" . $m[0] . "\n"
        . (preg_match('/function export_report\(kind\)\s*\{.*?\n\}/s', $html, $f) ? $f[0] : '') . "\n"
        . "function print_csv(){export_report('csv');} function print_excel(){export_report('excel');}\n"
        . "print_excel(); console.log(window.location.href); print_csv(); console.log(window.location.href);";
    $out = trim((string)shell_exec('node -e ' . escapeshellarg($js) . ' 2>&1'));
    [$u1, $u2] = array_pad(explode("\n", $out), 2, '');
    $ep = $method === 'trial_balance' ? 'trial_balance' : $method;
    ok($u1 === "http://x/admin/export/$ep?$qs&export=excel", "$tag Excel button URL", "($u1)");
    ok($u2 === "http://x/admin/export/$ep?$qs&export=csv",   "$tag CSV button URL",   "($u2)");
    // the query string must carry the page's own resolved parameters
    parse_str($qs, $q);
    ok(isset($q['view'], $q['nil_type'], $q['consolidated'], $q['from_date'], $q['to_date']), "$tag export_qs has view/nil_type/consolidated/from/to", "($qs)");
    if ($method !== 'trial_balance') { ok(isset($q['format']), "$tag export_qs has format"); }
}
restore_error_handler();
echo "\nchecks passed: $pass | failed: $fail\n"; exit($fail ? 1 : 0);
