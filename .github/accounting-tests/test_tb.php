<?php
require __DIR__ . '/boot.php';
$m = harness_reporting();
$F='2025-04-01'; $T='2026-03-31';
$fail=0; function chk($l,$g,$w){global $fail;$ok=abs($g-$w)<0.005;printf("  %-66s %s%s\n",$l,$ok?'PASS':'FAIL',$ok?'':"  got $g want $w");if(!$ok)$fail++;}
$tot=function($rows){$d=$c=0.0;foreach($rows as $r){$d+=$r['debit_total'];$c+=$r['credit_total'];}return [round($d,2),round($c,2)];};

foreach ([0=>'Groups',1=>'Accounts',2=>'Opening'] as $v=>$label) {
  $rows = match($v){0=>$m->load_trial_balance_grps($F,$T,0),1=>$m->load_trial_balance_accnts($F,$T,0),2=>$m->load_trial_balance_opn($F,$T,0,1)};
  [$d,$c]=$tot($rows);
  echo "--- TB view $v ($label): rows=".count($rows)." Dr=$d Cr=$c ---\n";
  chk("view $v: total debit = total credit", $d, $c);
  if ($v<2) {
     $names = implode('|', array_map(fn($r)=>strip_tags($r['group_name']), $rows));
     $has = str_contains($names,'Suspense'); printf("  %-66s %s\n","view $v: unmapped Suspense ledger is listed", $has?'PASS':'FAIL'); if(!$has)$fail++;
  }
}
// per-account cross-check against raw SQL
$db=harness_db();
$rows=$m->load_trial_balance_accnts($F,$T,0); $by=[]; foreach($rows as $r){ $by[(int)$r['group_id']] = ($r['debit_total']-$r['credit_total']); }
foreach($db->query("SELECT a.acc_id, COALESCE(o.op,0) + COALESCE(t.dr,0) - COALESCE(t.cr,0) AS closing FROM acctmaster a
   LEFT JOIN (SELECT acc_id, SUM(acc_op_bal) op FROM accoppybal WHERE cmp_id=1 AND cmpfymastr_id=1 AND hobo_id=1 GROUP BY acc_id) o ON o.acc_id=a.acc_id
   LEFT JOIN (SELECT acc_id, SUM(CASE WHEN acc_txn_dr_cr=1 THEN acc_txn_amt ELSE 0 END) dr, SUM(CASE WHEN acc_txn_dr_cr=2 THEN acc_txn_amt ELSE 0 END) cr FROM accttxnmst WHERE cmp_id=1 AND hobo_id=1 AND acc_txn_type=1 AND vch_txn_id>0 AND acc_txn_date BETWEEN '$F' AND '$T' GROUP BY acc_id) t ON t.acc_id=a.acc_id WHERE a.cmp_id=1")->getResultArray() as $r) {
   chk("TB account #{$r['acc_id']} equals independent raw-SQL closing", $by[(int)$r['acc_id']] ?? 0.0, (float)$r['closing']);
}
echo "--- LEGACY (for comparison) ---\n";
foreach([0=>'grps',1=>'accnts'] as $v=>$l){ $rows = $v==0? $m->legacy_load_trial_balance_grps($F,$T,0) : $m->legacy_load_trial_balance_accnts($F,$T,0,0); [$d,$c]=$tot($rows); echo "  legacy TB $l: Dr=$d Cr=$c diff=".round($d-$c,2)."\n"; }
echo $fail? "\n$fail FAILED\n":"\nALL TRIAL BALANCE CHECKS PASSED\n"; exit($fail?1:0);
