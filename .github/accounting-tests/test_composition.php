<?php
/**
 * SANDBOX-ONLY. The root cause of the books not balancing, end to end.
 *
 * PART 1 - SAVING. Drives the real posting helpers inside the real runTransaction() wrapper, writing a
 * composition purchase and a composition sale exactly the way Purchase.php and Sales.php write them (the
 * purchase voucher with no tax legs of its own, the journal's GST PAID row taken from a separate total or
 * posted as 0.00). With the old code each save left the books out of balance by the GST; the entry is now
 * completed before the transaction commits, so the ledger balances after every save.
 *
 * PART 2 - REPAIR. Runs `books:repair-composition` over the shapes found in the two audited companies
 * (fixture_pairs + fixture_gst): tax legs credited with no debit, a GST PAID leg of 0.00, a journal with
 * no rows, a journal that balances by itself, rows posted inside the purchase, rounding of a paisa. The
 * dry run must change nothing; --apply must bring the ledger to zero apart from the entries it reports as
 * needing a person; running it twice must find nothing the second time.
 *
 * PART 3 - LIMITS. What the tool must NOT touch: a voucher with a leg awaiting approval, a difference the
 * rows cannot explain, and a bill-sundry ledger that is not a tax ledger (freight).
 */
require __DIR__ . '/boot.php';

use App\Libraries\CompositionPosting;

$H = __DIR__;
$fail = 0; $pass = 0;
function ok(bool $c, string $l, string $d = ''): void { global $fail, $pass; if ($c) { $pass++; } else { $fail++; echo "  FAIL: $l $d\n"; } }
function sh(string $cmd): string { return (string)shell_exec('(' . $cmd . ') 2>&1'); }
$port = getenv('ERP_TEST_PGPORT') ?: '5433';
$psql = "psql -h 127.0.0.1 -p $port -U postgres -d erp_test -At";
// DROP DATABASE fails while this process still holds a connection, and reload_db.sh would then leave the
// old data in place, so the connection is closed first (the next query reopens it).
$reload = function (string $which) use ($H) {
    try { harness_db()->close(); } catch (\Throwable $e) { /* not connected yet */ }
    $out = sh('cd ' . escapeshellarg($H) . " && ./reload_db.sh $which");
    if (!str_contains($out, 'reloaded')) { echo "  FAIL: could not reload the $which fixture: $out\n"; exit(2); }
};
/** Debit minus credit of the financial year, which is what the reports and the audit measure. */
$imb = function () use ($psql) {
    return round((float)trim(sh($psql . ' -c ' . escapeshellarg(
        "SELECT COALESCE(SUM(CASE WHEN acc_txn_dr_cr=1 THEN acc_txn_amt WHEN acc_txn_dr_cr=2 THEN -acc_txn_amt ELSE 0 END),0)
           FROM accttxnmst WHERE cmp_id=1 AND acc_txn_type=1 AND vch_txn_id>0
             AND acc_txn_date BETWEEN '2025-04-01' AND '2026-03-31'"))), 2);
};
$rowsOf = function (int $vch) use ($psql) {
    $out = [];
    foreach (explode("\n", trim(sh($psql . ' -c ' . escapeshellarg(
        "SELECT acc_id||':'||acc_txn_dr_cr||':'||acc_txn_amt FROM accttxnmst
          WHERE cmp_id=1 AND vch_txn_id=$vch AND acc_txn_type=1 ORDER BY acc_id, acc_txn_dr_cr, acc_txn_amt")))) as $l) {
        if ($l !== '') { $out[] = $l; }
    }
    return $out;
};

// =====================================================================================================
//  PART 1: the save path
// =====================================================================================================
$reload('pairs');
harness_session(['bo_gstin_type' => 2, 'ses_bostecd' => '09']);

/** A stand-in for the voucher controllers: the same trait, the same model helpers, the same order. */
class CompositionPoster
{
    use \App\Traits\TransactionTrait;

    public \App\Models\Admin\VouchersModel $m;

    public function __construct() { $this->m = new \App\Models\Admin\VouchersModel(); }

    /**
     * A composition purchase the way Purchase.php writes one: the supplier is credited base + tax, only
     * the base is debited to Purchases, and the GST goes on a type-23 journal (Dr GST PAID from its own
     * total, Cr each tax ledger from the per-line components - two calculations that can disagree).
     */
    public function purchase(string $date, float $base, float $cgst, float $sgst, float $totalTax, bool $emptyJournal = false)
    {
        return $this->runTransaction(function ($db) use ($date, $base, $cgst, $sgst, $totalTax, $emptyJournal) {
            $journal = $this->m->add_voucher_cons_data(['cmp_id' => 1, 'vch_series_id' => 7, 'vch_type_id' => 23,
                'vch_sub_type_id' => 0, 'vch_date' => $date]);
            if (!$emptyJournal) {
                $this->m->add_acc_txn_data(['cmp_id' => 1, 'acc_id' => 117, 'acc_txn_date' => $date, 'acc_txn_dr_cr' => 1,
                    'acc_txn_amt' => $totalTax, 'acc_txn_fcy' => 0, 'vch_txn_id' => $journal, 'txn_id' => 0,
                    'hobo_id' => 1, 'acc_txn_type' => 1]);
            }
            $vch = $this->m->add_voucher_cons_data(['cmp_id' => 1, 'vch_series_id' => 3, 'vch_type_id' => 11,
                'vch_sub_type_id' => 5, 'vch_date' => $date]);
            $this->m->add_vchbridgen_txn_data(['cmp_id' => 1, 'vch_bridge_type' => 3, 'vch_txn_id_src' => $vch,
                'vch_txn_id_dest' => $journal]);
            $this->m->add_acc_txn_data(['cmp_id' => 1, 'acc_id' => 108, 'acc_txn_date' => $date, 'acc_txn_dr_cr' => 1,
                'acc_txn_amt' => $base, 'acc_txn_fcy' => 0, 'vch_txn_id' => $vch, 'txn_id' => 0, 'hobo_id' => 1, 'acc_txn_type' => 1]);
            $this->m->add_acc_txn_data(['cmp_id' => 1, 'acc_id' => 103, 'acc_txn_date' => $date, 'acc_txn_dr_cr' => 2,
                'acc_txn_amt' => $base + $totalTax, 'acc_txn_fcy' => 0, 'vch_txn_id' => $vch, 'txn_id' => 0, 'hobo_id' => 1, 'acc_txn_type' => 1]);
            $db->table('vchgstsumn')->insert(['cmp_id' => 1, 'vch_txn_id' => $vch, 'txn_id' => 1, 'acc_bsd_id' => 1,
                'acc_bsd_type' => 3, 'vch_taxable_value' => $base, 'vch_igst' => 0, 'vch_total_tax' => $totalTax]);
            if (!$emptyJournal) {
                // gstinType == 2, so the tax legs go on the journal as credits and the purchase gets none
                $this->m->save_taxacc_yes_out_data($journal, 7, -$cgst, 2, $date, 1);
                $this->m->save_taxacc_yes_out_data($journal, 7, -$sgst, 3, $date, 1);
            }
            return ['data' => ['vch' => $vch, 'journal' => $journal]];
        });
    }

    /**
     * A composition sale the way Sales.php writes one: the customer is debited the goods value only, and
     * the journal's GST PAID debit comes out as 0.00 because GetTotalTax() is called with the arguments
     * shifted (the supply type lands in the $sale_against_status slot, $vchtype stays null) and its
     * SALE/PURCHASE branch applies tax only for a regular registration.
     */
    public function sale(string $date, float $goods, float $cgst, float $sgst)
    {
        return $this->runTransaction(function ($db) use ($date, $goods, $cgst, $sgst) {
            $journal = $this->m->add_voucher_cons_data(['cmp_id' => 1, 'vch_series_id' => 7, 'vch_type_id' => 23,
                'vch_sub_type_id' => 0, 'vch_date' => $date]);
            $this->m->add_acc_txn_data(['cmp_id' => 1, 'acc_id' => 117, 'acc_txn_date' => $date, 'acc_txn_dr_cr' => 1,
                'acc_txn_amt' => 0, 'acc_txn_fcy' => 0, 'vch_txn_id' => $journal, 'txn_id' => 0, 'hobo_id' => 1, 'acc_txn_type' => 1]);
            $vch = $this->m->add_voucher_cons_data(['cmp_id' => 1, 'vch_series_id' => 2, 'vch_type_id' => 18,
                'vch_sub_type_id' => 8, 'vch_date' => $date]);
            $this->m->add_vchbridgen_txn_data(['cmp_id' => 1, 'vch_bridge_type' => 4, 'vch_txn_id_src' => $journal,
                'vch_txn_id_dest' => $vch]);
            $this->m->add_acc_txn_data(['cmp_id' => 1, 'acc_id' => 104, 'acc_txn_date' => $date, 'acc_txn_dr_cr' => 1,
                'acc_txn_amt' => $goods, 'acc_txn_fcy' => 0, 'vch_txn_id' => $vch, 'txn_id' => 0, 'hobo_id' => 1, 'acc_txn_type' => 1]);
            $this->m->add_acc_txn_data(['cmp_id' => 1, 'acc_id' => 107, 'acc_txn_date' => $date, 'acc_txn_dr_cr' => 2,
                'acc_txn_amt' => $goods, 'acc_txn_fcy' => 0, 'vch_txn_id' => $vch, 'txn_id' => 0, 'hobo_id' => 1, 'acc_txn_type' => 1]);
            $this->m->save_taxacc_yes_out_data($journal, 7, -$cgst, 2, $date, 2);
            $this->m->save_taxacc_yes_out_data($journal, 7, -$sgst, 3, $date, 2);
            return ['data' => ['vch' => $vch, 'journal' => $journal]];
        });
    }

    /**
     * A composition sale whose tax rows land on the sale voucher itself, with no separate journal and no
     * link - the shape found on one of the audited companies. Nothing here is a type-23 voucher, so the
     * only thing that brings it to the check is the tax posting itself.
     */
    public function saleWithTaxOnTheVoucher(string $date, float $goods, float $cgst, float $sgst)
    {
        return $this->runTransaction(function ($db) use ($date, $goods, $cgst, $sgst) {
            $vch = $this->m->add_voucher_cons_data(['cmp_id' => 1, 'vch_series_id' => 2, 'vch_type_id' => 18,
                'vch_sub_type_id' => 8, 'vch_date' => $date]);
            $this->m->add_acc_txn_data(['cmp_id' => 1, 'acc_id' => 104, 'acc_txn_date' => $date, 'acc_txn_dr_cr' => 1,
                'acc_txn_amt' => $goods, 'acc_txn_fcy' => 0, 'vch_txn_id' => $vch, 'txn_id' => 0, 'hobo_id' => 1, 'acc_txn_type' => 1]);
            $this->m->add_acc_txn_data(['cmp_id' => 1, 'acc_id' => 117, 'acc_txn_date' => $date, 'acc_txn_dr_cr' => 1,
                'acc_txn_amt' => 0, 'acc_txn_fcy' => 0, 'vch_txn_id' => $vch, 'txn_id' => 0, 'hobo_id' => 1, 'acc_txn_type' => 1]);
            $this->m->add_acc_txn_data(['cmp_id' => 1, 'acc_id' => 107, 'acc_txn_date' => $date, 'acc_txn_dr_cr' => 2,
                'acc_txn_amt' => $goods, 'acc_txn_fcy' => 0, 'vch_txn_id' => $vch, 'txn_id' => 0, 'hobo_id' => 1, 'acc_txn_type' => 1]);
            $this->m->save_taxacc_yes_out_data($vch, 2, -$cgst, 2, $date, 2);
            $this->m->save_taxacc_yes_out_data($vch, 2, -$sgst, 3, $date, 2);
            return ['data' => ['vch' => $vch]];
        });
    }

    /** A purchase whose GST cannot be posted: the branch has no ledger for the component at all. */
    public function purchaseWithoutTaxLedger(string $date, float $base, float $tax)
    {
        return $this->runTransaction(function ($db) use ($date, $base, $tax) {
            $journal = $this->m->add_voucher_cons_data(['cmp_id' => 1, 'vch_series_id' => 7, 'vch_type_id' => 23,
                'vch_sub_type_id' => 0, 'vch_date' => $date]);
            $vch = $this->m->add_voucher_cons_data(['cmp_id' => 1, 'vch_series_id' => 3, 'vch_type_id' => 11,
                'vch_sub_type_id' => 5, 'vch_date' => $date]);
            $this->m->add_vchbridgen_txn_data(['cmp_id' => 1, 'vch_bridge_type' => 3, 'vch_txn_id_src' => $vch,
                'vch_txn_id_dest' => $journal]);
            $this->m->add_acc_txn_data(['cmp_id' => 1, 'acc_id' => 108, 'acc_txn_date' => $date, 'acc_txn_dr_cr' => 1,
                'acc_txn_amt' => $base, 'acc_txn_fcy' => 0, 'vch_txn_id' => $vch, 'txn_id' => 0, 'hobo_id' => 1, 'acc_txn_type' => 1]);
            $this->m->add_acc_txn_data(['cmp_id' => 1, 'acc_id' => 103, 'acc_txn_date' => $date, 'acc_txn_dr_cr' => 2,
                'acc_txn_amt' => $base + $tax, 'acc_txn_fcy' => 0, 'vch_txn_id' => $vch, 'txn_id' => 0, 'hobo_id' => 1, 'acc_txn_type' => 1]);
            // tax_cat_sub_type 1 (IGST) has no ledger in this company: the row is silently not written
            $this->m->save_taxacc_yes_out_data($journal, 7, -$tax, 1, $date, 1);
            return ['data' => ['vch' => $vch, 'journal' => $journal]];
        });
    }
}

$before = $imb();
$poster = new CompositionPoster();

// ---- a composition purchase: 10,000 base, 1,800 GST -------------------------------------------------
$r = $poster->purchase('2025-06-05', 10000, 900, 900, 1800);
ok(($r['status'] ?? false) === true, 'a composition purchase saves', json_encode($r['message'] ?? null));
$p1 = $r['result']['vch'] ?? 0; $j1 = $r['result']['journal'] ?? 0;
ok($imb() === $before, 'the books still balance after saving it (the GST no longer hangs on one side)', 'imbalance ' . $imb() . ' was ' . $before);
ok($rowsOf($p1) === ['103:2:11800.00', '108:1:10000.00', '141:1:900.00', '142:1:900.00'],
   'the purchase now carries the input-tax debits its supplier credit already included', json_encode($rowsOf($p1)));
ok($rowsOf($j1) === ['117:1:1800.00', '141:2:900.00', '142:2:900.00'],
   'the journal still moves that tax to GST PAID A/C', json_encode($rowsOf($j1)));

// ---- a composition sale: the journal's GST PAID leg was 0.00 ----------------------------------------
$r = $poster->sale('2025-06-10', 71000, 355, 355);
ok(($r['status'] ?? false) === true, 'a composition sale saves', json_encode($r['message'] ?? null));
$j2 = $r['result']['journal'] ?? 0;
ok($imb() === $before, 'the books still balance after saving it');
ok($rowsOf($j2) === ['117:1:710.00', '143:2:355.00', '144:2:355.00'],
   'the 0.00 GST PAID leg of the sale journal now carries the composition tax it credits to the output-tax ledgers',
   json_encode($rowsOf($j2)));

// ---- rounding: the components come to a paisa less than the tax charged -----------------------------
$r = $poster->purchase('2025-06-15', 100000.00, 5000.01, 5000.01, 10000.03);
ok(($r['status'] ?? false) === true, 'a purchase whose tax total and components differ by 0.01 saves', json_encode($r['message'] ?? null));
$p3 = $r['result']['vch'] ?? 0;
ok($imb() === $before, 'the books still balance: the odd paisa went on a tax leg, not into the difference');
ok(in_array('141:1:5000.02', $rowsOf($p3), true) || in_array('142:1:5000.02', $rowsOf($p3), true),
   'the rounding paisa is carried by the largest mirrored tax leg', json_encode($rowsOf($p3)));

// ---- a journal with no rows at all: the GST summary corroborates the amount -------------------------
$r = $poster->purchase('2025-06-20', 5000, 0, 0, 900, true);
ok(($r['status'] ?? false) === true, 'a purchase whose journal never got any rows saves', json_encode($r['message'] ?? null));
$p4 = $r['result']['vch'] ?? 0; $j4 = $r['result']['journal'] ?? 0;
ok($imb() === $before, 'the books still balance');
ok($rowsOf($j4) === ['117:1:900.00'] || $rowsOf($p4) === ['103:2:5900.00', '108:1:5000.00', '117:1:900.00'],
   'the GST is posted to GST PAID A/C, the amount taken from the voucher\'s own GST summary',
   json_encode(['purchase' => $rowsOf($p4), 'journal' => $rowsOf($j4)]));

// ---- the tax rows on the sale voucher itself, with no journal and no link at all --------------------
$r = $poster->saleWithTaxOnTheVoucher('2025-06-22', 40000, 200, 200);
ok(($r['status'] ?? false) === true, 'a sale that carries its own composition tax rows saves', json_encode($r['message'] ?? null));
$p5 = $r['result']['vch'] ?? 0;
ok($imb() === $before, 'the books still balance: a voucher with no journal of its own is checked just the same');
ok($rowsOf($p5) === ['104:1:40000.00', '107:2:40000.00', '117:1:400.00', '143:2:200.00', '144:2:200.00'],
   'its 0.00 GST PAID row carries the composition tax the sale credits', json_encode($rowsOf($p5)));

// ---- no tax ledger for the component: the save is refused rather than left one-sided ----------------
$r = $poster->purchaseWithoutTaxLedger('2025-06-25', 20000, 3600);
ok(($r['status'] ?? true) === false, 'a purchase whose GST can be posted nowhere is refused, not saved one-sided');
ok(str_contains(strtolower((string)($r['message'] ?? '')), 'does not balance'), 'the refusal says what is wrong', (string)($r['message'] ?? ''));
ok($imb() === $before, 'and nothing of it is left in the books (the transaction rolled back)');

// =====================================================================================================
//  PART 2: repairing what is already in the books
// =====================================================================================================
$reload('pairs');
$digest = fn() => trim(sh($psql . ' -c ' . escapeshellarg(
    "SELECT md5(string_agg(t,'|' ORDER BY t)) FROM (SELECT 'a'||md5(x::text) t FROM accttxnmst x
       UNION ALL SELECT 'b'||md5(x::text) FROM acctvchreg x UNION ALL SELECT 'c'||md5(x::text) FROM cmptxnmstn x) z")));
$run = function (string $args) use ($H) { return sh('cd ' . escapeshellarg($H) . ' && php run_repair.php ' . $args); };

$imbPairs = $imb();
ok(abs($imbPairs + 15780.0) < 0.005, 'fixture_pairs starts 15,780.00 Cr out of balance', (string)$imbPairs);

$snapshot = $digest();
$dry = $run('--company 1 --fy 1 --branch 1');
ok($digest() === $snapshot, 'the dry run changes nothing at all');
ok(str_contains($dry, 'DRY RUN'), 'it says it is a dry run');
ok(preg_match('/ledger before\s+: debit - credit = 15,780\.00 Cr/', $dry) === 1, 'it reports the ledger before');
ok(preg_match('/ledger after\s+: debit - credit = ([\d,]+\.\d\d Cr|0\.00)/', $dry) === 1, 'it measures the ledger after for real, then rolls back');
ok(preg_match('/by rule:/', $dry) === 1 && preg_match('/\bM\b\s+7 row\(s\)/', $dry) === 1 && preg_match('/\bS\b\s+2 row\(s\)/', $dry) === 1,
   'it groups the changes by the rule that justifies them', substr($dry, (int)strpos($dry, 'by rule'), 400));
ok(!str_contains($dry, 'remove  voucher'), 'it removes no ledger row on this ledger: everything is a correction or an addition',
   substr($dry, (int)strpos($dry, 'the changes'), 600));
ok(preg_match('/change\s+voucher 24\s+Dr\s+9,000\.00\s+GST PAID A\/C\s+\(was Dr 4,500\.00\)\s+\[S\]/', $dry) === 1,
   'a GST PAID leg posted at half the tax is corrected to the tax the voucher\'s own GST summary records, not deleted');
ok(preg_match('/voucher\(s\) 27\s+difference 500\.00 Dr/', $dry) === 1,
   'a lone GST PAID debit with no tax leg and no GST summary is left for a person, never removed');
ok(str_contains($dry, 'left exactly as it is, for a person to decide'), 'it lists what it will not touch');

$applied = $run('--company 1 --fy 1 --branch 1 --apply');
ok(str_contains($applied, 'APPLIED and committed'), 'the repair applies', substr($applied, -400));
$after = $imb();
$review = 0;
if (preg_match('/entry groups still unbalanced : (\d+)/', $applied, $mm)) { $review = (int)$mm[1]; }
ok($after !== $imbPairs, 'the ledger imbalance changed', (string)$after);

// What is left must be only what it reported for review, plus the one leg that sits in the previous year.
//   voucher 27      500.00 Dr   a lone GST PAID debit, nothing to measure it against
//   voucher 33      900.00 Cr   its GST summary says 500, not the 900 the voucher is short
//   voucher 38 + 39 900.00 Cr   the linked journal has no rows and voucher 38 has no GST summary
//   voucher 29      180.00 Cr   balances only with voucher 30, which is dated in the previous year
ok($review === 3, 'it reports three entry groups still needing a person', "review groups=$review");
ok(abs($after + 1480.0) < 0.005, 'the ledger is left 1,480.00 Cr out: those three plus the leg dated in the previous year',
   (string)$after);
foreach (['27', '33', '38 \+ 39'] as $who) {
    ok(preg_match('/voucher\(s\) ' . $who . '\s+difference/', $applied) === 1, "it names voucher(s) $who as needing a person");
}
// every voucher it did complete now balances on its own; the rest balance with the voucher they are linked to
ok((int)trim(sh($psql . ' -c ' . escapeshellarg(
    "SELECT COUNT(*) FROM (SELECT vch_txn_id FROM accttxnmst WHERE cmp_id=1 AND acc_txn_type=1 AND vch_txn_id IN (25,35,36,37,40)
       GROUP BY vch_txn_id HAVING ABS(SUM(CASE WHEN acc_txn_dr_cr=1 THEN acc_txn_amt ELSE -acc_txn_amt END))>0.005) z"))) === 0,
   'each voucher the repair completed balances on its own');

$again = $run('--company 1 --fy 1 --branch 1');
ok(preg_match('/entries to complete\s+: 0\b/', $again) === 1, 'running it again finds nothing more to do (it is idempotent)',
   substr($again, (int)strpos($again, 'entries to complete'), 120));

// the reports agree with the repaired ledger
$m = harness_reporting(['bo_gstin_type' => 2]);
$rec = $m->reconciliation('2025-04-01', '2026-03-31', 0);
ok(abs((float)$rec['ledger_imbalance'] - $after) < 0.005, 'the reporting engine sees the same ledger as raw SQL',
   $rec['ledger_imbalance'] . ' vs ' . $after);
$tb = $m->load_trial_balance_grps('2025-04-01', '2026-03-31', 0);
$d = 0.0; $c = 0.0;
foreach ($tb as $row) { $d += (float)($row['debit_total'] ?? 0); $c += (float)($row['credit_total'] ?? 0); }
ok(abs(round($d - $c, 2) - $after) < 0.02, 'the Trial Balance differs by exactly what is left in the ledger, nothing more',
   round($d - $c, 2) . ' vs ' . $after);

// ---- the sales / zero-leg / duplicate-line shapes ---------------------------------------------------
$reload('gst');
$imbGst = $imb();
ok(abs($imbGst - 790.0) < 0.005, 'fixture_gst starts 790.00 Dr out of balance', (string)$imbGst);
$g = $run('--company 1 --fy 1 --branch 1 --apply');
ok(str_contains($g, 'APPLIED and committed'), 'the repair applies to the sales shapes too', substr($g, -300));
ok($rowsOf(52) === ['117:1:710.00', '141:2:355.00', '142:2:355.00'],
   'the sale journal whose GST PAID leg was 0.00 now carries the 710.00 it credits', json_encode($rowsOf(52)));
// Two identical credit lines on one ledger: either one of them is a double posting or a debit is missing,
// and the rows cannot say which. That is left for a person rather than guessed at.
ok($rowsOf(55) === ['103:2:10000.00', '108:1:10000.00', '117:1:300.00', '146:2:300.00', '146:2:300.00'],
   'a voucher with two identical credit lines on one ledger is left exactly as it was', json_encode($rowsOf(55)));
ok(str_contains($g, 'voucher(s) 55'), 'and voucher 55 is reported as needing a person',
   substr($g, (int)strpos($g, 'left exactly as it is'), 400));

// ---- the financial-year master lives in the separate company database, not in the ledger database -----
// This is what made the first real run fail: cmpfymastr was read from the main connection, where a live
// installation does not have it. The main schema's table is taken away here, leaving only the view the
// company connection sees, so the repair must still find the year.
$reload('pairs');
$plannedSame = function (string $out): bool {
    return preg_match('/ledger before\s+: debit - credit = 15,780\.00 Cr/', $out) === 1
        && preg_match('/entries to complete\s+: 5\b/', $out) === 1;
};
sh($psql . ' -c ' . escapeshellarg('ALTER TABLE public.cmpfymastr RENAME TO cmpfymastr_moved_away'));
ok($plannedSame($run('--company 1 --fy 1 --branch 1')),
   'the year master is found in the separate company database, and the same repair is planned',
   substr($run('--company 1 --fy 1 --branch 1'), 0, 500));

sh($psql . ' -c ' . escapeshellarg('DROP VIEW univ.cmpfymastr'));

// the year row exists but carries no dates: unusable, and it must not be run with an empty date
sh($psql . ' -c ' . escapeshellarg('ALTER TABLE public.cmpfymastr_moved_away RENAME TO cmpfymastr;
  UPDATE public.cmpfymastr SET fy_beg_date = NULL, fy_end_date = NULL WHERE cmpfymastr_id = 1'));
$nullDates = $run('--company 1 --fy 1 --branch 1');
ok(str_contains($nullDates, 'Could not read financial year') && !str_contains($nullDates, '1970-'),
   'a year row with no start or end date is refused, not run over an empty period',
   substr($nullDates, 0, 400));
sh($psql . ' -c ' . escapeshellarg("UPDATE public.cmpfymastr SET fy_beg_date = '2025-04-01', fy_end_date = '2026-03-31' WHERE cmpfymastr_id = 1;
  ALTER TABLE public.cmpfymastr RENAME TO cmpfymastr_moved_away"));

$noFy = $run('--company 1 --fy 1 --branch 1');
ok(str_contains($noFy, 'Could not read financial year') && str_contains($noFy, '--fy-start'),
   'with the year master nowhere to be found it says so and offers the dates as options, not a stack trace',
   substr($noFy, 0, 400));
ok($plannedSame($run('--company 1 --fy 1 --branch 1 --fy-start 2025-04-01 --fy-end 2026-03-31')),
   'and with the dates given on the command line it plans exactly the same repair');

sh($psql . ' -c ' . escapeshellarg('ALTER TABLE public.cmpfymastr_moved_away RENAME TO cmpfymastr;
  CREATE VIEW univ.cmpfymastr AS SELECT * FROM public.cmpfymastr'));

// =====================================================================================================
//  PART 3: what it must not touch
// =====================================================================================================
$reload('pairs');
$posting = new CompositionPosting(harness_db(), 1);

// A leg awaiting approval on voucher 23, whose entry (23 + its journal 24) is 4,500.00 Cr short: the entry
// is incomplete by design while an approval is pending, so nothing may be added to it.
sh($psql . ' -c ' . escapeshellarg("INSERT INTO accttxnmst (cmp_id,hobo_id,acc_id,acc_txn_date,acc_txn_dr_cr,acc_txn_amt,acc_txn_type,txn_id,vch_txn_id)
   VALUES (1,1,103,'2025-09-10',2,500,4,23,23)"));
$plan = $posting->plan([23]);
ok($plan['actions'] === [], 'a voucher with a leg waiting for approval is never changed', json_encode($plan['actions']));
ok(count($plan['review']) === 1 && str_contains($plan['review'][0]['why'], 'waiting for approval'),
   'it is reported as waiting for approval', json_encode($plan['review']));
sh($psql . ' -c ' . escapeshellarg("DELETE FROM accttxnmst WHERE acc_txn_type=4"));
ok($posting->plan([23])['actions'] !== [], 'once the approval is no longer pending the same entry is completed again');

// a difference the rows cannot explain
sh($psql . ' -c ' . escapeshellarg("INSERT INTO vchtxnconso (vch_txn_id,cmp_id,hobo_id,txn_id,vch_type_id,vch_date,vch_sub_type_id,vch_series_id)
   VALUES (61,1,1,61,11,'2025-09-01',5,3);
  INSERT INTO accttxnmst (cmp_id,hobo_id,acc_id,acc_txn_date,acc_txn_dr_cr,acc_txn_amt,acc_txn_type,txn_id,vch_txn_id)
   VALUES (1,1,108,'2025-09-01',1,1000,1,61,61),(1,1,103,'2025-09-01',2,1234.56,1,61,61)"));
$plan = $posting->plan([61]);
ok($plan['actions'] === [] && count($plan['review']) === 1, 'a difference the stored rows cannot explain is left alone',
   json_encode([$plan['actions'], $plan['review']]));
ok(str_contains($plan['review'][0]['why'], 'do not show which leg is missing'), 'and it says so plainly', json_encode($plan['review'][0]));

// Freight is a bill-sundry ledger but NOT a tax ledger, so it must never be mirrored. The ledger is added
// before the object that reads the master, because that set is read once and kept.
sh($psql . ' -c ' . escapeshellarg("INSERT INTO acctmaster (acc_id,cmp_id,acc_name,bsd_id) VALUES (149,1,'Freight Inward',19);
  INSERT INTO undercrsmt (cmp_id,cmpfymastr_id,crs_mst_type,crs_mst_id,under_crs_mst_id,crs_mst_parent_id,under_main_id,crs_mst_is_primary)
   VALUES (1,1,1,149,0,13,0,1);
  INSERT INTO vchtxnconso (vch_txn_id,cmp_id,hobo_id,txn_id,vch_type_id,vch_date,vch_sub_type_id,vch_series_id)
   VALUES (62,1,1,62,11,'2025-09-02',5,3);
  INSERT INTO accttxnmst (cmp_id,hobo_id,acc_id,acc_txn_date,acc_txn_dr_cr,acc_txn_amt,acc_txn_type,txn_id,vch_txn_id)
   VALUES (1,1,108,'2025-09-02',1,1000,1,62,62),(1,1,103,'2025-09-02',2,1000,1,62,62),(1,1,149,'2025-09-02',2,500,1,62,62)"));
// voucher 62 is 500.00 Cr short and that is exactly its freight leg, so anything that treated freight as a
// tax ledger would "complete" it with a freight debit the supplier never charged
$fresh = new CompositionPosting(harness_db(), 1);
ok(!in_array(149, $fresh->taxAccounts(), true) && in_array(141, $fresh->taxAccounts(), true),
   'only GST tax ledgers are in the set the repair may touch, freight is not',
   json_encode(array_values($fresh->taxAccounts())));
$plan = $fresh->plan([62]);
ok($plan['actions'] === [], 'a freight bill-sundry leg is not treated as tax and is never mirrored', json_encode($plan['actions']));

// =====================================================================================================
//  PART 4: the Balance Sheet reconciliation
//  It must tie the two totals on the screen to the vouchers behind them: the report's own difference, the
//  ledger's, what the stored rows account for, what is left, and what the Balance Sheet then reads.
// =====================================================================================================
$reload('pairs');
$money = function (string $s): float {                       // "12,694.56 Cr" -> -12694.56
    if (!preg_match('/([\d,]+\.\d\d)(?:\s+(Dr|Cr))?/', $s, $m)) { return NAN; }
    $v = (float)str_replace(',', '', $m[1]);
    return (($m[2] ?? 'Dr') === 'Cr') ? -$v : $v;
};
$bsLine = '/Balance Sheet\s+liabilities\s+([\d,]+\.\d\d)\s+assets\s+([\d,]+\.\d\d)\s+difference\s+([\d,]+\.\d\d(?:\s+Dr|\s+Cr)?)/';

$snapshot = $digest();
$dry = $run('--company 1 --fy 1 --branch 1');
$sect = substr($dry, (int)strpos($dry, 'BALANCE SHEET RECONCILIATION'));
ok($digest() === $snapshot, 'the reconciliation reads the reports inside the transaction and still changes nothing');
ok(str_contains($dry, 'BALANCE SHEET RECONCILIATION'), 'the dry run reconciles the Balance Sheet', substr($dry, -400));

// ---- step 1: the report's difference must be the ledger's own -------------------------------------
preg_match_all($bsLine, $sect, $bs, PREG_SET_ORDER);
preg_match('/Ledger\s+debit minus credit of every posted row\s+([\d,]+\.\d\d(?:\s+Dr|\s+Cr)?)/', $sect, $led);
ok(count($bs) === 2 && count($led) === 2, 'it prints the Balance Sheet totals before and after, and the ledger difference', $sect);
$before = $money($led[1] ?? '0');
ok(abs(((float)str_replace(',', '', $bs[0][1]) - (float)str_replace(',', '', $bs[0][2])) + $before) < 0.02,
   'liabilities minus assets is the ledger difference seen from the other side', ($bs[0][1] ?? '') . ' - ' . ($bs[0][2] ?? '') . ' vs ' . ($led[1] ?? ''));
ok(($bs[0][3] ?? 'x') === ($led[1] ?? 'y'), 'and the two are printed as the same figure', ($bs[0][3] ?? '') . ' vs ' . ($led[1] ?? ''));
ok(str_contains($sect, 'Both are the same figure') && str_contains($sect, 'not a reporting one and not an accounting difference'),
   'so it states that the report adds up and the ledger is what does not');
ok(preg_match('/ledger before\s+: debit - credit = ' . preg_quote($led[1], '/') . '/', $dry) === 1,
   'the figure it reconciles is the same one the command measured at the start', $led[1]);

// ---- step 2: the split must add back up exactly ----------------------------------------------------
preg_match('/entr(?:y|ies) whose own rows show which leg is missing \(the posting defect\)\s+([\d,]+\.\d\d(?:\s+Dr|\s+Cr)?)/', $sect, $p1);
preg_match('/entry group\(s\) whose rows do not show it, for a person to decide\s+([\d,]+\.\d\d(?:\s+Dr|\s+Cr)?)/', $sect, $p2);
ok(count($p1) === 2 && count($p2) === 2, 'it splits the difference into what the rows explain and what needs a person', $sect);
ok(abs(($money($p1[1]) + $money($p2[1])) - $before) < 0.005, 'the two parts add up to the whole difference, to the paisa',
   $p1[1] . ' + ' . $p2[1] . ' vs ' . $led[1]);
ok(str_contains($sect, 'exactly the difference in step 1'), 'and it says so only when they do');
ok($money($p2[1]) === $money((string)(preg_match('/ledger after\s+: debit - credit = ([\d,]+\.\d\d(?:\s+Dr|\s+Cr)?|0\.00)/', $dry, $la) ? $la[1] : 'x')),
   'the part needing a person is exactly what the measured repair leaves behind', ($p2[1] ?? '') . ' vs ' . ($la[1] ?? ''));

// ---- step 3: the Balance Sheet as it would then read -----------------------------------------------
ok(($bs[1][1] ?? '') !== ($bs[0][1] ?? '') || ($bs[1][2] ?? '') !== ($bs[0][2] ?? ''),
   'the "after" figures are read again inside the transaction, not a cached copy of the "before" ones',
   json_encode([$bs[0] ?? null, $bs[1] ?? null]));
ok(abs(((float)str_replace(',', '', $bs[1][1]) - (float)str_replace(',', '', $bs[1][2])) + $money($p2[1])) < 0.02,
   'what the Balance Sheet would then be out by is exactly what was left for a person', json_encode($bs[1]));
ok(preg_match('/Profit \/ Loss\s+(-?[\d,]+\.\d\d)\s+\(it reads (-?[\d,]+\.\d\d) today\)/', $sect, $pl) === 1 && $pl[1] !== $pl[2],
   'it shows what the profit would become next to what it reads today', $sect);
ok(str_contains($sect, 'no figure here was adjusted and no balancing entry was invented'), 'and it says nothing was forced');
ok(!str_contains($sect, 'The Balance Sheet tallies') && str_contains($sect, 'has to be looked at before anyone calls it a genuine difference'),
   'while something is still left it does not claim the Balance Sheet tallies', $sect);

// ---- the read is fenced: if the reports cannot be built, the repair still does its job --------------
// PostgreSQL aborts a whole transaction when one statement fails, so an unreadable report would take the
// repair down with it. Here the real stock model is put back, which needs item tables the sandbox has not got.
$reload('pairs');
$noRep = sh('cd ' . escapeshellarg($H) . ' && REPAIR_NO_STOCK_STUB=1 php run_repair.php --company 1 --fy 1 --branch 1 --apply');
ok(str_contains($noRep, 'APPLIED and committed'), 'with the reports unreadable the repair still applies and commits', substr($noRep, -500));
ok(str_contains($noRep, 'could not be built from here'), 'and it says the Balance Sheet could not be read instead of inventing figures');
ok(abs($imb() + 1480.0) < 0.005, 'the ledger really was repaired in that run', (string)$imb());

// ---- the two sides of step 1 are compared, not assumed equal -----------------------------------------
// Data alone cannot produce a report that disagrees with its own ledger, so the comparison itself is tested.
$agrees = new ReflectionMethod(\App\Commands\RepairComposition::class, 'reportAgreesWithLedger');
$agrees->setAccessible(true);
$cmd = new \App\Commands\RepairComposition(\Config\Services::logger(), \Config\Services::commands());
ok($agrees->invoke($cmd, 474774.94, -474774.94) === true, 'a Balance Sheet that shows exactly the ledger difference agrees');
ok($agrees->invoke($cmd, 474774.94, -474774.93) === true, 'a paisa of rounding still agrees');
ok($agrees->invoke($cmd, 474774.94, -474774.90) === false, 'four paisa do not: that part would be the report\'s own doing');
ok($agrees->invoke($cmd, 474774.94, -474674.94) === false, 'and a hundred rupees certainly do not');
ok($agrees->invoke($cmd, 0.0, 0.0) === true, 'books that balance agree with a Balance Sheet that tallies');

// ---- when everything is explainable, it says the Balance Sheet tallies --------------------------------
$reload('pairs');
sh($psql . ' -c ' . escapeshellarg(
    'DELETE FROM accttxnmst WHERE vch_txn_id IN (27,29,30,33,38,39); DELETE FROM vchtxnconso WHERE vch_txn_id IN (27,29,30,33,38,39)'));
$clean = $run('--company 1 --fy 1 --branch 1');
ok(str_contains($clean, 'The Balance Sheet tallies'), 'with nothing left for a person it says the Balance Sheet tallies',
   substr($clean, (int)strpos($clean, 'BALANCE SHEET RECON')));
ok(!str_contains($clean, 'has to be looked at before anyone calls it a genuine difference'),
   'and it does not then talk about a difference to look at');

// ---- if applying the plan does not move the ledger by what the rules planned, it refuses to stand behind it
// A trigger quietly adds a rupee to every row the repair inserts, so the plan and the effect part company.
$reload('pairs');
sh($psql . ' -c ' . escapeshellarg(
    "CREATE FUNCTION nudge() RETURNS trigger AS \$\$ BEGIN NEW.acc_txn_amt := NEW.acc_txn_amt + 1; RETURN NEW; END \$\$ LANGUAGE plpgsql;
     CREATE TRIGGER nudge_ins BEFORE INSERT ON accttxnmst FOR EACH ROW EXECUTE FUNCTION nudge()"));
$nudged = $run('--company 1 --fy 1 --branch 1');
ok(str_contains($nudged, 'DOES NOT match step 1 - do not act on this run'),
   'when the ledger does not move by the planned amount the tie-out says so', substr($nudged, (int)strpos($nudged, 'BALANCE SHEET RECON')));
ok(str_contains($nudged, '[WARN] completing those entries moved the ledger by'),
   'and it names both figures instead of hiding the gap');
sh($psql . ' -c ' . escapeshellarg('DROP TRIGGER nudge_ins ON accttxnmst; DROP FUNCTION nudge()'));
$unnudged = $run('--company 1 --fy 1 --branch 1');
ok(!str_contains($unnudged, 'DOES NOT match') && !str_contains($unnudged, '[WARN] completing those entries'),
   'with the interference gone the same books tie out again', substr($unnudged, (int)strpos($unnudged, 'BALANCE SHEET RECON')));

echo "\nchecks passed: $pass | failed: $fail\n";
exit($fail ? 1 : 0);
