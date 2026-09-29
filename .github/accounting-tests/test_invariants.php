<?php
require __DIR__ . '/boot.php';
$db = harness_db();
$fail=0; $pass=0;
function ok($cond,$label,$detail=''){ global $fail,$pass; if($cond){$pass++;} else {$fail++; echo "  FAIL: $label $detail\n";} }
$near=fn($a,$b)=>abs($a-$b)<0.0101;
$FY0='2025-04-01';

// ---- independent raw SQL (no engine, no presenter) -------------------------------------------------------------
$rawImb = function(string $to, bool $cons) use($db,$FY0){
  $b = $cons ? '' : ' AND hobo_id = 1';
  $r = $db->query("SELECT COALESCE(SUM(CASE WHEN acc_txn_dr_cr=1 THEN acc_txn_amt WHEN acc_txn_dr_cr=2 THEN -acc_txn_amt ELSE 0 END),0) t FROM accttxnmst WHERE cmp_id=1 AND acc_txn_type=1 AND vch_txn_id>0 AND acc_txn_date BETWEEN '$FY0' AND '$to' $b")->getRowArray();
  return round((float)$r['t'],2);
};
$rawClosing = function(string $to, bool $cons) use($db,$FY0){
  $b1 = $cons ? '' : ' AND hobo_id = 1';
  $out=[];
  foreach($db->query("SELECT acc_id, SUM(acc_op_bal) op FROM accoppybal WHERE cmp_id=1 AND cmpfymastr_id=1 $b1 GROUP BY acc_id")->getResultArray() as $r) $out[(int)$r['acc_id']] = (float)$r['op'];
  foreach($db->query("SELECT acc_id, SUM(CASE WHEN acc_txn_dr_cr=1 THEN acc_txn_amt WHEN acc_txn_dr_cr=2 THEN -acc_txn_amt ELSE 0 END) t FROM accttxnmst WHERE cmp_id=1 AND acc_txn_type=1 AND vch_txn_id>0 AND acc_txn_date BETWEEN '$FY0' AND '$to' $b1 GROUP BY acc_id")->getResultArray() as $r) $out[(int)$r['acc_id']] = ($out[(int)$r['acc_id']] ?? 0) + (float)$r['t'];
  return array_map(fn($x)=>round($x,2), $out);
};

$m = harness_reporting();
$scen = 0;
foreach ([false,true] as $cons) foreach (['2025-04-01','2025-10-01'] as $from) foreach (['2026-03-31','2025-12-31'] as $to) {
  $scen++; $c=(int)$cons; $tag="cons=$c from=$from to=$to";
  $imb = $rawImb($to,$cons);

  // TB: debit - credit == ledger imbalance (views 0,1); opening view balances
  foreach ([0,1] as $v) {
    $rows = $v==0 ? $m->load_trial_balance_grps($from,$to,$c) : $m->load_trial_balance_accnts($from,$to,$c);
    $d=$cr=0.0; foreach($rows as $r){$d+=$r['debit_total'];$cr+=$r['credit_total'];}
    ok($near($d-$cr,$imb),"TB view $v $tag: Dr-Cr equals raw ledger imbalance","(Dr-Cr=".round($d-$cr,2)." raw=$imb)");
  }
  $rows=$m->load_trial_balance_opn($from,$to,$c,1); $d=$cr=0.0; foreach($rows as $r){$d+=$r['debit_total'];$cr+=$r['credit_total'];}
  ok($near($d,$cr),"TB opening view $tag balances","(Dr=$d Cr=$cr)");

  // TB accounts view: every ledger equals raw closing (FY-start..to)
  $rc = $rawClosing($to,$cons); $rows=$m->load_trial_balance_accnts($from,$to,$c); $by=[];
  foreach($rows as $r){ if(in_array($r['type'],['dfs']) && $r['group_id']>0) $by[(int)$r['group_id']] = round($r['debit_total']-$r['credit_total'],2); }
  foreach($rc as $id=>$val){ if(abs($val)<0.005) continue; ok($near($by[$id]??0.0,$val),"TB acct #$id $tag equals raw closing","(got ".($by[$id]??'missing')." want $val)"); }

  // P&L net identical across every layout/view (period from..to)
  $nets=[];
  foreach([0,1,2] as $v){ $rows=$m->load_profit_loss_horizontal($v,$from,$to,1,$c); $l=end($rows); $nets["H$v"]=round($l['l_balance_total']-$l['r_balance_total'],2); }
  foreach([0,1,2] as $v){ $rows=$m->load_profit_loss_vertical($v,$from,$to,1,$c); $by2=[]; foreach($rows as $r){$by2[$r['group_name']]=$r['amt'];} $nets["V$v"]=round(($by2['Net Profit C/D']??0)-($by2['Net Loss C/D']??0),2); }
  ok(count(array_unique(array_map('strval',$nets)))===1,"P&L net identical in all 6 views $tag",json_encode($nets));
  $plNet = reset($nets);

  // BS: Left - Right == -(ledger imbalance) in every view/layout; P&L line == FY-to-date P&L
  $fyPl = $m->load_profit_loss_horizontal(1,$FY0,$to,1,$c); $l=end($fyPl); $fyNet=round($l['l_balance_total']-$l['r_balance_total'],2);
  foreach([0,1,2] as $v){
    $rows=$m->load_balance_sheet_horizontal($v,$from,$to,1,$c); $last=end($rows);
    ok($near($last['l_balance_total']-$last['r_balance_total'], -$imb),"BS H$v $tag: Left-Right equals -imbalance","(diff=".round($last['l_balance_total']-$last['r_balance_total'],2)." want ".(-$imb).")");
    $pl=null; $bf=0; foreach($rows as $r){ if(($r['l_type']??'')==='pl'){ if(($r['l_group_name']??'')==='Profit / Loss') $pl=$r['l_balance_total']; else $bf=$r['l_balance_total']; } }
    ok($near($pl,$fyNet),"BS H$v $tag: profit line equals FY-to-date P&L","(bs=$pl pl=$fyNet)");
    $rows=$m->load_balance_sheet_vertical($v,$from,$to,1,$c); $by3=[]; foreach($rows as $r){$by3[$r['group_name']]=$r['amt'];}
    ok($near(($by3['TOTAL LIABILITIES']??0)-($by3['TOTAL ASSETS']??0), -$imb),"BS V$v $tag: Total Liab - Total Assets equals -imbalance","(diff=".round(($by3['TOTAL LIABILITIES']??0)-($by3['TOTAL ASSETS']??0),2).")");
  }
  if ($from===$FY0) ok($near($plNet,$fyNet),"P&L report net equals BS profit basis when from=FY start $tag","(pl=$plNet bs=$fyNet)");
}

// ---- reconciliation report attributes the difference ---------------------------------------------------------
$rec = $m->reconciliation($FY0,'2026-03-31',0);
ok($near($rec['ledger_imbalance'],$rawImb('2026-03-31',false)),"reconciliation: ledger imbalance equals raw");
ok($near($rec['gst_paid_journals'],1000.0),"reconciliation: GST PAID system journal (type 23) = 1,000","(got {$rec['gst_paid_journals']})");
$ids=array_column($rec['vouchers'],'vch_txn_id'); sort($ids);
ok($ids===[9,13],"reconciliation: unbalanced vouchers are exactly #9 (GST paid) and #13 (pending party leg)",json_encode($ids));
ok($near($rec['other'],-700.0),"reconciliation: the rest (-700) is the pending-approval voucher","(got {$rec['other']})");

echo "\nscenarios run: $scen | checks passed: $pass | failed: $fail\n";
exit($fail?1:0);
