<?php
require __DIR__ . '/boot.php';
$m = harness_reporting();
$F='2025-04-01'; $T='2026-03-31';
$fail=0; function chk($l,$g,$w){global $fail;$ok=abs($g-$w)<0.005;printf("  %-64s %s%s\n",$l,$ok?'PASS':'FAIL',$ok?'':"  got $g want $w");if(!$ok)$fail++;}

echo "--- NEW Balance Sheet horizontal ---\n";
foreach ([0,1,2] as $v) {
  $rows = $m->load_balance_sheet_horizontal($v,$F,$T,1,0); $last = end($rows);
  chk("H view $v: LEFT total 891,200",  $last['l_balance_total'], 891200);
  chk("H view $v: RIGHT total 891,200", $last['r_balance_total'], 891200);
  $pl=null; foreach($rows as $r) if(($r['l_type']??'')==='pl' && ($r['l_group_name']??'')==='Profit / Loss') $pl=$r['l_balance_total'];
  chk("H view $v: BS profit line = P&L net profit 28,200", $pl, 28200);
  $unc=null; foreach($rows as $r) if(str_contains(($r['r_group_name']??''),'Suspense') || (($r['r_type']??'')==='unc')) { $unc = $r['r_balance_total'] ?: ($r['r_det']??null); }
  $found=false; foreach($rows as $r){ if(str_contains(strip_tags($r['r_group_name']??''),'Unclassified')) $found=true; }
  printf("  %-64s %s\n","H view $v: unclassified section present", $found?'PASS':'FAIL'); if(!$found)$fail++;
}
echo "--- NEW Balance Sheet vertical ---\n";
foreach ([0,1,2] as $v) {
  $rows = $m->load_balance_sheet_vertical($v,$F,$T,1,0); $by=[]; foreach($rows as $r){ $by[$r['group_name']] = $r['amt']; }
  chk("V view $v: TOTAL LIABILITIES 891,200", $by['TOTAL LIABILITIES'] ?? 0, 891200);
  chk("V view $v: TOTAL ASSETS 891,200",      $by['TOTAL ASSETS'] ?? 0, 891200);
  chk("V view $v: profit line 28,200",         $by['Profit / Loss'] ?? 0, 28200);
}
echo "--- LEGACY (for comparison) ---\n";
foreach ([1] as $v) { $rows=$m->legacy_load_balance_sheet_horizontal($v,$F,$T,1,0); $l=end($rows); printf("  legacy H: LEFT %s RIGHT %s  difference %s\n",$l['l_balance_total'],$l['r_balance_total'],$l['l_balance_total']-$l['r_balance_total']); }
echo $fail? "\n$fail FAILED\n":"\nALL BALANCE SHEET CHECKS PASSED\n"; exit($fail?1:0);
