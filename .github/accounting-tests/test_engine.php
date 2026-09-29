<?php
require __DIR__ . '/boot.php';
harness_session();
$db  = harness_db();
$eng = new \App\Libraries\AccountingEngine($db, 1, 1, 1, '2025-04-01', '2026-03-31');
$s   = $eng->snapshot('2025-04-01', '2026-03-31', false);
$f = fn($x) => number_format((float)$x, 2, '.', '');
$fail = 0;
function check($label, $got, $want) { global $fail; $ok = (is_float($want)||is_int($want)) ? abs($got-$want) < 0.005 : $got === $want; printf("  %-62s %s%s\n", $label, $ok ? 'PASS' : 'FAIL', $ok ? '' : "  (got ".var_export($got,true).", want ".var_export($want,true).")"); if(!$ok) $fail++; }

echo "accounts in snapshot: ".count($s->accounts)."  groups: ".count($s->groups)."\n";
check('every account present (16 incl. unmapped suspense)', count($s->accounts), 16);
check('suspense is unmapped, cat 0',  [$s->accounts[116]['mapped'], $s->accounts[116]['cat']], [false, 0]);
check('salary (sub-group 11) rolls up to root group 10, cat 13', [$s->accounts[111]['root_gid'], $s->accounts[111]['cat']], [10, 13]);
check('CGST via type-14 mapping -> cat 4', $s->accounts[113]['cat'], 4);
check('discount = primary account under cat 13', [$s->accounts[115]['group_id'], $s->accounts[115]['cat']], [0, 13]);
check('bank closing = 475,500', $s->accounts[105]['closing'], 475500.0);
check('opening total of ledger = -40,000', $eng->openingTotal($s), -40000.0);
check('ledger imbalance (Dr-Cr) = 0', $eng->imbalance($s), 0.0);
check('no unbalanced vouchers', count($eng->unbalancedVouchers($s)), 0);
check('group 10 (Indirect Exp) total INCLUDES sub-group = 42,000', $s->groupSum(10, 'mv'), 42000.0);
check('group 11 (Salaries sub-group) = 30,000', $s->groupSum(11, 'mv'), 30000.0);

$un = $eng->unclassified($s);
check('exactly one unclassified ledger (suspense, +1,000)', [count($un), $un[0]['id'] ?? 0, $un[0]['closing'] ?? 0], [1, 116, 1000.0]);

$pl = $eng->profitLoss($s, 40000.0, 65000.0, 'mv');
check('P&L: sales 100,000', $pl[8], 100000.0);
check('P&L: purchase 50,000', $pl[11], 50000.0);
check('P&L: gross profit 70,000', $pl['gross_profit'], 70000.0);
check('P&L: indirect expenses 42,300 (rent+salary+discount, NO double count)', $pl[13], 42300.0);
check('P&L: net profit 28,200', $pl['net'], 28200.0);

$items = $eng->categoryItems($s, 13, 'mv', fn($a) => $eng->plSlot($a));
check('cat 13 total = 42,300', $items['total'], 42300.0);
check('cat 13: one top group (Indirect Expenses) + one primary account (Discount)', [count($items['groups']), count($items['accounts'])], [1, 1]);
echo $fail ? "\n$fail FAILED\n" : "\nALL ENGINE CHECKS PASSED\n";
exit($fail ? 1 : 0);
