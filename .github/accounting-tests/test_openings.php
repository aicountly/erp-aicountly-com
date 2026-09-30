<?php
/**
 * SANDBOX-ONLY: books:carry-openings.
 *
 * The command sets named ledgers' openings to what the previous year closed them at. The reason it insists
 * on being told which ledgers is the whole point of these checks: a retired ledger and its replacement look
 * exactly like a pair of mistakes, and on the real books one pair that looks identical is two different
 * accounts both opened wrongly. So the fixture carries both shapes and the tests prove that only what is
 * named is ever written.
 *
 * fixture_openings.sql: stored openings net 60.00 Cr; correcting 102, 109 and 110 takes that to 0.00, and
 * the 107/108 pair nets to nothing, which is why leaving it alone is safe.
 */
require __DIR__ . '/boot.php';

$H = __DIR__;
$fail = 0; $pass = 0;
function ok(bool $c, string $l, string $d = ''): void { global $fail, $pass; if ($c) { $pass++; } else { $fail++; echo "  FAIL: $l $d\n"; } }
function sh(string $cmd): string { return (string)shell_exec('(' . $cmd . ') 2>&1'); }
$port = getenv('ERP_TEST_PGPORT') ?: '5433';
$psql = "psql -h 127.0.0.1 -p $port -U postgres -d erp_test -At";

$reload = function () use ($H) {
    try { harness_db()->close(); } catch (\Throwable $e) { /* not connected yet */ }
    $out = sh('cd ' . escapeshellarg($H) . ' && ./reload_db.sh openings');
    if (!str_contains($out, 'reloaded')) { echo "  FAIL: could not reload the openings fixture: $out\n"; exit(2); }
};
$run = function (string $args) use ($H) {
    return sh('cd ' . escapeshellarg($H) . ' && php run_openings.php ' . $args);
};
/** Every stored opening of FY 2, as "acc=amount", so a test can pin the whole table at once. */
$stored = function () use ($psql) {
    $out = [];
    foreach (explode("\n", trim(sh($psql . ' -c ' . escapeshellarg(
        "SELECT acc_id||'='||TRIM(TO_CHAR(acc_op_bal,'FM9999999990.00')) FROM accoppybal
          WHERE cmp_id=1 AND cmpfymastr_id=2 AND hobo_id=1 ORDER BY acc_id")))) as $l) {
        if ($l !== '') { $out[] = $l; }
    }
    return $out;
};

// =====================================================================================================
//  reporting only: without --accounts nothing may be written
// =====================================================================================================
$reload();
$before = $stored();
ok($before === ['101=-500.00', '102=540.00', '107=0.00', '108=-100.00', '110=0.00'],
   'the fixture opens the way it says it does', implode(' ', $before));

$look = $run('--company 1 --fy 2 --branch 1');
ok(str_contains($look, "'Difference in Opening' as it stands : 60.00 Cr"),
   'it reads the opening difference the reports read', $look);
ok(str_contains($look, 'previous year: profit 200.00 Cr'), "and the previous year's result", $look);
ok(str_contains($look, 'Nothing named, so nothing is written'), 'without --accounts it writes nothing', $look);
ok($stored() === $before, 'and the database is untouched', implode(' ', $stored()));

// the list: what differs, and nothing else
ok(str_contains($look, '102     HDFC Bank           550.00 Dr    540.00 Dr')
   && str_contains($look, '109     MISSING ROW LTD     250.00 Dr         0.00')
   && str_contains($look, '108     NEW SUPPLIER             0.00    100.00 Cr')
   && str_contains($look, '107     OLD SUPPLIER        100.00 Cr         0.00'),
   'every ledger whose opening is not the previous closing is listed, with both figures', $look);
ok(!str_contains($look, 'Owner Capital'),
   'a ledger that already opens correctly is not listed at all', $look);
ok(!str_contains($look, 'Sales') && !str_contains($look, 'Purchases'),
   'profit & loss ledgers are not listed: they start every year at zero', $look);
ok(str_contains($look, 'A retired ledger and its replacement look exactly like a pair of mistakes here'),
   'and the list says in so many words that it is not a work order', $look);

// the Appropriation is the one ledger whose opening is not simply last year's closing
ok(str_contains($look, "'Profit & Loss Appropriation' (acc 110): opening 0.00; the previous year closed it at 0.00 and made 200.00 Cr")
   && str_contains($look, 'so carrying both would put it at 200.00 Cr. Pass --appropriation 200.00Cr to set it.'),
   'the Appropriation gets its own line, with the figure worked out', $look);

// =====================================================================================================
//  correcting the three that were decided
// =====================================================================================================
$dry = $run('--company 1 --fy 2 --branch 1 --accounts 102,109,110 --appropriation 200Cr');
ok(str_contains($dry, 'rows inserted / updated : 1 / 2'),
   'one insert for the ledger with no row, two updates', $dry);
ok(str_contains($dry, "acc 102    HDFC Bank                      the previous year's closing balance  was 540.00 Dr  ->  550.00 Dr")
   && str_contains($dry, "acc 109    MISSING ROW LTD                the previous year's closing balance  (no row)  ->  250.00 Dr")
   && str_contains($dry, 'acc 110    Profit & Loss Appropriation    given with --appropriation           was 0.00  ->  200.00 Cr'),
   'each row says where its figure came from', $dry);
ok(str_contains($dry, "'Difference in Opening' before : 60.00 Cr")
   && str_contains($dry, "'Difference in Opening' after  : 0.00")
   && str_contains($dry, 'predicted from the changes     : 0.00   - they agree')
   && str_contains($dry, 'The opening data of this year now balances.'),
   'the three together take the opening difference to nothing', $dry);
ok(str_contains($dry, '102     HDFC Bank           550.00 Dr    540.00 Dr     550.00 Dr  yes'),
   'the named ledgers are marked in the list', $dry);
ok($stored() === $before, 'the dry run wrote nothing', implode(' ', $stored()));

$app = $run('--company 1 --fy 2 --branch 1 --accounts 102,109,110 --appropriation 200Cr --apply');
ok(str_contains($app, 'APPLIED and committed'), 'it applies', substr($app, -300));
$after = $stored();
ok($after === ['101=-500.00', '102=550.00', '107=0.00', '108=-100.00', '109=250.00', '110=-200.00'],
   'exactly the three named rows moved, and 109 was inserted', implode(' ', $after));
ok(in_array('107=0.00', $after, true) && in_array('108=-100.00', $after, true),
   'the retired pair is exactly as it was: a migration is not a mistake', implode(' ', $after));
$again = $run('--company 1 --fy 2 --branch 1 --accounts 102,109,110 --appropriation 200Cr');
ok(str_contains($again, 'Nothing named, so nothing is written')
   || str_contains($again, 'Every balance-sheet ledger already opens'),
   'running it again finds nothing to do', substr($again, -400));
ok($stored() === $after, 'and changes nothing', implode(' ', $stored()));

// =====================================================================================================
//  the Appropriation will not move without being given a figure
// =====================================================================================================
$reload();
$noAmt = $run('--company 1 --fy 2 --branch 1 --accounts 110');
ok(str_contains($noAmt, "is 'Profit & Loss Appropriation'. Its opening is the previous closing less that year's result")
   && str_contains($noAmt, 'Left alone.'),
   'naming the Appropriation without a figure is refused, with the reason', $noAmt);
ok($stored() === $before, 'and nothing is written', implode(' ', $stored()));

$noName = $run('--company 1 --fy 2 --branch 1 --accounts 102 --appropriation 200Cr');
ok(str_contains($noName, '--appropriation was given but ledger 110 is not in --accounts; ignored'),
   'a figure given for a ledger that was not named is ignored, and says so', $noName);

// Dr / Cr, so a credit needs no leading minus on a command line
$reload();
$drRun = $run('--company 1 --fy 2 --branch 1 --accounts 110 --appropriation 200Dr');
ok(str_contains($drRun, 'was 0.00  ->  200.00 Dr'), '"200Dr" is a debit', $drRun);
$bare = $run('--company 1 --fy 2 --branch 1 --accounts 110 --appropriation 1,234.50');
ok(str_contains($bare, 'was 0.00  ->  1,234.50 Dr'), 'a bare number is a debit, and commas are tolerated', $bare);

// =====================================================================================================
//  naming a ledger that should have been left alone is allowed, and reported honestly
// =====================================================================================================
$reload();
$retired = $run('--company 1 --fy 2 --branch 1 --accounts 107');
ok(str_contains($retired, 'acc 107    OLD SUPPLIER'), 'a retired ledger IS corrected when named: the reader decides', $retired);
ok(str_contains($retired, "'Difference in Opening' after  : 160.00 Cr")
   && str_contains($retired, 'predicted from the changes     : 160.00 Cr   - they agree')
   && str_contains($retired, '160.00 Cr is still left'),
   'and the command says plainly that this takes the opening further out, not closer', $retired);

// =====================================================================================================
//  the CSV, so a change can be undone by hand
// =====================================================================================================
$reload();
$csvFile = $H . '/openings.csv';
@unlink($csvFile);
$run('--company 1 --fy 2 --branch 1 --accounts 102,109,110 --appropriation 200Cr --csv ' . escapeshellarg($csvFile));
$csv = is_file($csvFile) ? (string)file_get_contents($csvFile) : '';
ok(substr_count(trim($csv), "\n") === 3, 'the CSV has a line per planned row', $csv);
ok(str_contains($csv, '1,2,1,102,"HDFC Bank","the previous year\'s closing balance",540.00,550.00'),
   'each line records the value it replaces, so it can be put back', $csv);
ok(str_contains($csv, '1,2,1,109,"MISSING ROW LTD","the previous year\'s closing balance",,250.00'),
   'a ledger with NO ROW is marked by an empty "was", not a nil it never held - the two are undone differently',
   $csv);
@unlink($csvFile);

// =====================================================================================================
//  if what it wrote does not read back, it keeps nothing
// =====================================================================================================
$reload();
sh($psql . ' -c ' . escapeshellarg(
    "CREATE FUNCTION tilt() RETURNS trigger AS \$\$ BEGIN NEW.acc_op_bal := NEW.acc_op_bal + 5; RETURN NEW; END \$\$ LANGUAGE plpgsql;
     CREATE TRIGGER tilt_bal BEFORE INSERT OR UPDATE ON accoppybal FOR EACH ROW EXECUTE FUNCTION tilt()"));
$tilted = $run('--company 1 --fy 2 --branch 1 --accounts 102,109,110 --appropriation 200Cr --apply');
ok(str_contains($tilted, 'THEY DO NOT AGREE'), 'a write that lands differently is caught', substr($tilted, -600));
ok(str_contains($tilted, 'Rolled back: the openings did not read back as the changes predicted'),
   'and the whole run is rolled back rather than half-applied', substr($tilted, -400));
ok($stored() === $before, 'the stored openings are exactly as they were', implode(' ', $stored()));
sh($psql . ' -c ' . escapeshellarg('DROP TRIGGER tilt_bal ON accoppybal; DROP FUNCTION tilt()'));
$clean = $run('--company 1 --fy 2 --branch 1 --accounts 102,109,110 --appropriation 200Cr --apply');
ok(str_contains($clean, 'APPLIED and committed'), 'with the interference gone the same run applies', substr($clean, -300));

// =====================================================================================================
//  what it refuses outright
// =====================================================================================================
$noArgs = $run('--company 1 --fy 2');
ok(str_contains($noArgs, '--company, --fy and --branch are all required'),
   'the branch is required: openings are held per branch', $noArgs);
$firstYear = $run('--company 1 --fy 1 --branch 1');
ok(str_contains($firstYear, 'no earlier financial year'),
   'the first year of a company has nothing to carry from', $firstYear);

echo "\nchecks passed: $pass | failed: $fail\n";
exit($fail ? 1 : 0);
