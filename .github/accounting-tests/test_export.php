<?php
/**
 * SANDBOX-ONLY: screen-versus-file parity for Balance Sheet / Profit & Loss / Trial Balance.
 *   screen = the REAL Reportings controller methods (rendering mocked, so we capture the data handed to the view)
 *   file   = the REAL Export controller methods, run in a child process, xlsx and csv bytes read back
 * The file's own `export_qs` (what the page's Excel/CSV buttons send) drives the export request.
 */
require __DIR__ . '/boot.php';
use PhpOffice\PhpSpreadsheet\IOFactory;

$fail = 0; $pass = 0;
function ok(bool $c, string $label, string $detail = ''): void { global $fail, $pass; if ($c) { $pass++; } else { $fail++; echo "  FAIL: $label $detail\n"; } }

// ---- what the grid prints, as plain values (independent of the writer's own cleaning code) ------------------------
function t($s): string { $s = (string)$s; $s = preg_replace('/<[^>]*>/', ' ', $s); $s = html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    $s = str_replace(["\xC2\xA0", '»'], ' ', $s); return trim(preg_replace('/\s+/u', ' ', $s)); }
function n($s): ?float { $s = (string)$s; if ($s === '') return null; $x = preg_replace('/[^0-9.\-]/', '', html_entity_decode($s, ENT_QUOTES | ENT_HTML5, 'UTF-8')); return $x === '' ? null : round((float)$x, 2); }
function same(?float $a, ?float $b): bool { return ($a === null && $b === null) || ($a !== null && $b !== null && abs($a - $b) < 0.005); }

// ---- screen: the real Reportings methods with the renderer swapped for a recorder ---------------------------------
function screen(string $method, array $get): array {
    $_GET = $get;
    $rec = new class { public $data = []; public $name = ''; public function setData($d, $c = null) { $this->data = $d; return $this; } public function render($n, $o = [], $s = null) { $this->name = $n; return ''; } };
    \Config\Services::injectMock('renderer', $rec);
    $ctl = (new ReflectionClass(\App\Controllers\Admin\Reportings::class))->newInstanceWithoutConstructor();
    $ctl->ReportingModel = harness_reporting();
    $ctl->folder_path = 'admin/'; $ctl->base_url = 'http://sandbox/admin/'; $ctl->bo_id = 1;
    $ctl->$method();
    return ['view' => $rec->name, 'data' => $rec->data];
}
// ---- file: the real Export methods in a child process ------------------------------------------------------------
function file_of(string $method, string $qs): string {
    $cmd = 'php ' . escapeshellarg(__DIR__ . '/export_child.php') . ' ' . escapeshellarg($method) . ' ' . escapeshellarg($qs) . ' 2>/tmp/export_child.err';
    $out = shell_exec($cmd);
    return (string)$out;
}
function xlsx_rows(string $bytes): array {
    $f = tempnam(sys_get_temp_dir(), 'x') . '.xlsx'; file_put_contents($f, $bytes);
    $ss = IOFactory::load($f); @unlink($f); $sh = $ss->getActiveSheet();
    $rows = []; $maxR = $sh->getHighestRow(); $maxC = $sh->getHighestColumn();
    for ($r = 1; $r <= $maxR; $r++) { $rows[$r] = $sh->rangeToArray("A{$r}:{$maxC}{$r}", null, false, false, false)[0]; }
    return $rows;
}
function csv_rows(string $bytes): array {
    $rows = []; $h = fopen('php://temp', 'r+'); fwrite($h, $bytes); rewind($h); $r = 1;
    while (($line = fgetcsv($h, 0, ',', '"', '\\')) !== false) { $rows[$r++] = $line; }
    fclose($h); return $rows;
}
function cell(array $rows, int $r, int $c) { $v = $rows[$r][$c] ?? null; return ($v === '' ? null : $v); }
function cnum(array $rows, int $r, int $c): ?float { $v = cell($rows, $r, $c); return ($v === null) ? null : round((float)$v, 2); }

/** Expected layout from screen rows -> compare with file rows (0-based columns). */
function compare_horizontal(string $tag, array $screenRows, array $file, bool $detail): void {
    $cols = $detail ? [[0, 1, 2], [3, 4, 5]] : [[0, null, 1], [2, null, 3]];
    $r = 4;
    foreach ($screenRows as $i => $row) {
        foreach ([['l', $cols[0]], ['r', $cols[1]]] as [$p, [$nc, $dc, $ac]]) {
            $name = t($row[$p . '_group_name'] ?? '');
            $fn   = cell($file, $r, $nc); $fn = $fn === null ? '' : trim((string)$fn);
            ok($fn === $name, "$tag row $i $p name", "(file '$fn' screen '$name')");
            ok(same(cnum($file, $r, $ac), n($row[$p . '_balance'] ?? '')), "$tag row $i $p amount", '(file ' . var_export(cnum($file, $r, $ac), true) . ' screen ' . var_export(n($row[$p . '_balance'] ?? ''), true) . ')');
            if ($detail) { ok(same(cnum($file, $r, $dc), n($row[$p . '_detail'] ?? '')), "$tag row $i $p detail", '(file ' . var_export(cnum($file, $r, $dc), true) . ' screen ' . var_export(n($row[$p . '_detail'] ?? ''), true) . ')'); }
        }
        $r++;
    }
    return;
}
function compare_vertical(string $tag, array $screenRows, array $file): void {
    $r = 4;
    foreach ($screenRows as $i => $row) {
        $name = t($row['group_name'] ?? ''); $fn = cell($file, $r, 0); $fn = $fn === null ? '' : trim((string)$fn);
        ok($fn === $name, "$tag row $i name", "(file '$fn' screen '$name')");
        ok(same(cnum($file, $r, 1), n($row['balance'] ?? '')), "$tag row $i amount", '(file ' . var_export(cnum($file, $r, 1), true) . ' screen ' . var_export(n($row['balance'] ?? ''), true) . ')');
        $r++;
    }
}
function compare_tb(string $tag, array $screenRows, array $file): void {
    $r = 4; $dr = 0.0; $cr = 0.0;
    foreach ($screenRows as $i => $row) {
        $fn = cell($file, $r, 0); $fn = $fn === null ? '' : trim((string)$fn);
        ok($fn === t($row['group_name'] ?? ''), "$tag row $i name", "(file '$fn' screen '" . t($row['group_name'] ?? '') . "')");
        $fp = cell($file, $r, 1); $fp = $fp === null ? '' : trim((string)$fp);
        ok($fp === t($row['parent'] ?? ''), "$tag row $i parent", "(file '$fp' screen '" . t($row['parent'] ?? '') . "')");
        ok(same(cnum($file, $r, 2), n($row['debit'] ?? '')), "$tag row $i debit", '(file ' . var_export(cnum($file, $r, 2), true) . ' screen ' . var_export(n($row['debit'] ?? ''), true) . ')');
        ok(same(cnum($file, $r, 3), n($row['credit'] ?? '')), "$tag row $i credit", '(file ' . var_export(cnum($file, $r, 3), true) . ' screen ' . var_export(n($row['credit'] ?? ''), true) . ')');
        $dr += (float)$row['debit_total']; $cr += (float)$row['credit_total']; $r++;
    }
    $dr = round($dr, 2); $cr = round($cr, 2);   // the grid's footer sums debit_total / credit_total
    ok(trim((string)cell($file, $r, 0)) === 'TOTAL', "$tag has TOTAL row");
    ok(same(cnum($file, $r, 2), $dr), "$tag total debit equals the grid footer", '(file ' . var_export(cnum($file, $r, 2), true) . " footer $dr)");
    ok(same(cnum($file, $r, 3), $cr), "$tag total credit equals the grid footer", '(file ' . var_export(cnum($file, $r, 3), true) . " footer $cr)");
    if (abs($dr - $cr) > 0.005) { ok(str_starts_with((string)cell($file, $r + 1, 0), 'DIFFERENCE'), "$tag difference line present"); }
}
function compare_notes(string $tag, array $notes, array $file): void {
    $all = []; foreach ($file as $row) { foreach ($row as $v) { if ($v !== null && $v !== '') $all[] = trim((string)$v); } }
    foreach ($notes as $k => $note) { $txt = is_array($note) ? $note['text'] : $note; ok(in_array(trim($txt), $all, true), "$tag note $k is in the file", "($txt)"); }
    $texts = array_map(fn($n) => is_array($n) ? $n['text'] : $n, $notes);
    $warn  = array_filter($notes, fn($n) => is_array($n) && $n['level'] === 'warn');
    if (!$warn) { foreach ($all as $v) { ok(!str_contains($v, 'does not tally'), "$tag no 'does not tally' line when the ledger balances"); } }
    // nothing in the file that claims more than the page does
    foreach ($all as $v) { if (str_contains($v, 'carry opening balances') || str_contains($v, "'Difference in Opening':")) { ok(in_array($v, $texts, true), "$tag every note line in the file is one the page shows", "($v)"); } }
}

$FYF = '01-04-2025'; $FYT = '31-03-2026';
$variants = function (string $rep) use ($FYF, $FYT): array {
    $v = [['label' => 'no params', 'q' => []],
          ['label' => 'empty params', 'q' => ['view' => '', 'nil_type' => '', 'format' => '', 'from_date' => '', 'to_date' => '', 'consolidated' => '']],
          ['label' => 'garbage params', 'q' => ['view' => '9', 'format' => '7', 'nil_type' => 'abc', 'from_date' => 'xx', 'to_date' => '2099-01-01', 'consolidated' => 'maybe']],
          ['label' => 'quarter', 'q' => ['view' => '1', 'format' => '1', 'nil_type' => '1', 'from_date' => '01-10-2025', 'to_date' => '31-12-2025', 'consolidated' => '0']]];
    foreach ([0, 1, 2] as $view) foreach ([0, 1] as $nil) foreach ([0, 1] as $cons) {
        if ($rep === 'trial_balance') { $v[] = ['label' => "v$view n$nil c$cons", 'q' => ['view' => $view, 'nil_type' => $nil, 'consolidated' => $cons, 'from_date' => $FYF, 'to_date' => $FYT]]; continue; }
        foreach ([1, 2] as $fmt) { $v[] = ['label' => "v$view f$fmt n$nil c$cons", 'q' => ['view' => $view, 'format' => $fmt, 'nil_type' => $nil, 'consolidated' => $cons, 'from_date' => $FYF, 'to_date' => $FYT]]; }
    }
    return $v;
};

$only = getenv('ONLY') ?: '';
foreach (['balance_sheet', 'profit_loss', 'trial_balance'] as $rep) {
    if ($only && $only !== $rep) continue;
    $n = 0;
    foreach ($variants($rep) as $var) {
        $scr  = screen($rep, $var['q']);
        $data = $rep === 'trial_balance' ? ($GLOBALS['__tb'] = null) : null;
        $rows = $rep === 'trial_balance' ? $scr['data'] : $scr['data'];
        // Reportings hands the rows to the view under 'data' (BS, P&L) or 'response' (TB)
        $rows = $scr['data']['data'] ?? $scr['data']['response'] ?? [];
        $qs   = (string)($scr['data']['export_qs'] ?? '');
        ok($qs !== '', "$rep [{$var['label']}] page provides export_qs");
        $view   = (int)($scr['data']['view'] ?? 0); $format = (int)($scr['data']['format'] ?? 1);
        $notes  = $scr['data']['recon_notes'] ?? [];
        foreach (['excel', 'csv'] as $kind) {
            $tag   = "$rep [{$var['label']}] $kind";
            $bytes = file_of($rep, $qs . '&export=' . $kind);
            if ($bytes === '') { ok(false, "$tag produced a file", trim((string)@file_get_contents('/tmp/export_child.err'))); continue; }
            $isXlsx = $kind === 'excel';
            ok($isXlsx ? str_starts_with($bytes, 'PK') : !str_starts_with($bytes, 'PK'), "$tag has the right file type");
            $file = $isXlsx ? xlsx_rows($bytes) : csv_rows($bytes);
            if ($rep === 'trial_balance')       { compare_tb($tag, $rows, $file); }
            elseif ($format === 2)              { compare_vertical($tag, $rows, $file); }
            else                                { compare_horizontal($tag, $rows, $file, $view === 2); }
            compare_notes($tag, $notes, $file);
            $n++;
        }
    }
    echo "  $rep: $n files compared with the page\n";
}
echo "\nchecks passed: $pass | failed: $fail\n";
exit($fail ? 1 : 0);
