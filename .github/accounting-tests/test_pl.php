<?php
require __DIR__ . '/boot.php';
$m = harness_reporting();
$F='2025-04-01'; $T='2026-03-31';
$f = fn($x)=>number_format((float)$x,2,'.','');
$fail=0; function chk($l,$g,$w){global $fail;$ok=abs($g-$w)<0.005;printf("  %-58s %s%s\n",$l,$ok?'PASS':'FAIL',$ok?'':"  got $g want $w");if(!$ok)$fail++;}

echo "--- NEW P&L horizontal ---\n";
foreach ([0,1,2] as $v) {
  $rows = $m->load_profit_loss_horizontal($v,$F,$T,1,0);
  $np=$nl=$gp=$gl=null;
  foreach($rows as $r){ if(($r['l_group_name']??'')==='Net Profit C/D'){$np=$r['l_balance_total'];$nl=$r['r_balance_total'];} if(($r['l_group_name']??'')==='Gross Profit C/F'){$gp=$r['l_balance_total'];$gl=$r['r_balance_total'];} }
  chk("H view $v gross profit 70,000",$gp,70000); chk("H view $v net profit 28,200",$np,28200); chk("H view $v net loss 0",$nl,0);
}
echo "--- NEW P&L vertical ---\n";
foreach ([0,1,2] as $v) {
  $rows = $m->load_profit_loss_vertical($v,$F,$T,1,0);
  $by=[]; foreach($rows as $r){ $by[$r['group_name']] = $r['amt']; }
  chk("V view $v gross profit 70,000",$by['Gross Profit C/F']??0,70000); chk("V view $v net profit 28,200",$by['Net Profit C/D']??0,28200);
}
echo "--- LEGACY (for comparison) ---\n";
foreach ([0,1,2] as $v) {
  $rows = $m->legacy_load_profit_loss_horizontal($v,$F,$T,1,0); $last=end($rows);
  printf("  legacy H view %d net profit %s / loss %s\n",$v,$f($last['l_balance_total']),$f($last['r_balance_total']));
}
foreach ([1,2] as $v) {
  $rows = $m->legacy_load_profit_loss_vertical($v,$F,$T,1,0); $by=[]; foreach($rows as $r){$by[$r['group_name']]=$r['balance'];}
  printf("  legacy V view %d net profit %s\n",$v,$by['Net Profit C/D']??'-');
}
echo $fail? "\n$fail FAILED\n":"\nALL P&L CHECKS PASSED\n"; exit($fail?1:0);
