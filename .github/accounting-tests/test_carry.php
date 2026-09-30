<?php
/**
 * SANDBOX-ONLY: books:carry-opening-stock.
 *
 * The roll-over copies the previous year's OPENING into the new year instead of its CLOSING, so a company
 * that started at nil opens at nil for ever. This command sets the opening from what the previous year
 * actually closed with - BOTH the quantity (itmoppybal) and the value (itmoppyval), because the valuation
 * engine needs both before it will use an average, and the value on its own leaves the year worse off.
 *
 * The Stock Status report is stubbed here (run_carry.php): what is under test is what the command does with
 * the previous year's closing figures, not the report that produces them.
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
    $out = sh('cd ' . escapeshellarg($H) . ' && ./reload_db.sh stock');
    if (!str_contains($out, 'reloaded')) { echo "  FAIL: could not reload the stock fixture: $out\n"; exit(2); }
};
$run = function (string $args, ?string $rows = null) use ($H) {
    $env = $rows === null ? '' : 'CARRY_ROWS=' . escapeshellarg($rows) . ' ';
    return sh('cd ' . escapeshellarg($H) . ' && ' . $env . 'php run_carry.php ' . $args);
};
/** Every stored opening row, as "table:item:method=amount", so a test can pin the whole table at once. */
$stored = function () use ($psql) {
    $out = [];
    foreach (explode("\n", trim(sh($psql . ' -c ' . escapeshellarg(
        "SELECT 'qty:'||itm_id_unit_id||'='||TRIM(TO_CHAR(itm_op_bal_qty,'FM9999999990.00')) FROM itmoppybal
          WHERE cmp_id=1 AND cmpfymastr_id=2 AND hobo_id=1
         UNION ALL
         SELECT 'val:'||itm_id_unit_id||':'||itm_val_method_id||'='||TRIM(TO_CHAR(itm_op_val_amt,'FM9999999990.00')) FROM itmoppyval
          WHERE cmp_id=1 AND cmpfymastr_id=2 AND hobo_id=1
         ORDER BY 1")))) as $l) {
        if ($l !== '') { $out[] = $l; }
    }
    return $out;
};
$openingTotal = function () use ($psql) {
    return round((float)trim(sh($psql . ' -c ' . escapeshellarg(
        "SELECT COALESCE(SUM(itm_op_val_amt),0) FROM itmoppyval WHERE cmp_id=1 AND cmpfymastr_id=2 AND hobo_id=1"))), 2);
};

// =====================================================================================================
//  the dry run
// =====================================================================================================
$reload();
$before = $stored();
ok(in_array('qty:ITEM-A_1=0.00', $before, true) && in_array('val:ITEM-A_1:3=0.00', $before, true),
   'the fixture starts the way the roll-over leaves a year: the big item at nil', implode(' ', $before));
ok(abs($openingTotal() - 1499.00) < 0.005, 'and its stored opening totals 1,499.00', (string)$openingTotal());

$dry = $run('--company 1 --fy 2 --branch 1');
ok(str_contains($dry, 'DRY RUN (nothing is written)'), 'it is a dry run unless --apply is given', $dry);
ok(str_contains($dry, 'from fy 1 (2023-04-01 .. 2024-03-31)'),
   'it finds the previous financial year on its own', $dry);
ok(str_contains($dry, 'previous year closed with : 4 item(s), value 4,263,640.16'),
   'it reads the previous closing from the report, item by item', $dry);
ok(str_contains($dry, 'rows to change            : 9'), 'and plans nine rows', $dry);
// stock carried at no cost: the quantity still matters, or next year issues from an empty pool
ok(str_contains($dry, 'FREE SAMPLES                       quantity                                       (new row)  ->  3.00'),
   'an item with a quantity but no value is still carried', $dry);
ok(trim((string)@file_get_contents(sys_get_temp_dir() . '/carry_fyid')) === '1',
   'the closing it reads is the PREVIOUS year\'s, not this one\'s',
   (string)@file_get_contents(sys_get_temp_dir() . '/carry_fyid'));

// the point of the whole command: BOTH tables, never one
ok(substr_count($dry, 'itmoppybal') >= 3 && substr_count($dry, 'itmoppyval') >= 4,
   'it writes the quantity table as well as the value table', $dry);
ok(str_contains($dry, 'itmoppybal  P.S PLASTIC STRIPS V200            quantity')
   && str_contains($dry, 'was 0.00  ->  2,186.00'),
   'the quantity goes in as 2,186.00', $dry);
ok(str_contains($dry, 'itmoppyval  P.S PLASTIC STRIPS V200            value (AVG)')
   && str_contains($dry, 'was 0.00  ->  4,262,806.62'),
   'the value goes in as 4,262,806.62 on the AVG row', $dry);
ok(str_contains($dry, 'FRAMED PICTURES                    quantity                                       (new row)'),
   'an item with no stored row at all is inserted, not skipped', $dry);
ok(str_contains($dry, 'value (LIFO) cleared, it would be counted twice was 500.00  ->  0.00'),
   'another method\'s row is cleared: the reports add every method row together', $dry);
ok(str_contains($dry, 'ITEM-C_1         12.00         999.00')
   && str_contains($dry, 'left alone - this year opens with these, the previous year did not close with them'),
   'stock the previous year did not close with is reported and left alone, not deleted', $dry);
ok(str_contains($dry, 'rows inserted / updated : 4 / 5'), 'four inserts and five updates', $dry);
ok(str_contains($dry, "previous year's closing : 4,263,640.16")
   && str_contains($dry, 'left alone on items the previous year did not close with : 999.00')
   && str_contains($dry, 'the two together        : 4,264,639.16   - they agree'),
   'the check adds the untouched stock back before comparing, instead of condemning a correct run', $dry);
ok(str_contains($dry, 'opening quantities match the previous closing, item by item: yes'),
   'and it checks the quantities one by one, not just the total', $dry);
ok($stored() === $before, 'the dry run wrote nothing at all', implode(' ', $stored()));

// =====================================================================================================
//  applying it
// =====================================================================================================
$app = $run('--company 1 --fy 2 --branch 1 --apply');
ok(str_contains($app, 'APPLIED and committed'), 'it applies', substr($app, -400));
$after = $stored();
ok(in_array('qty:ITEM-A_1=2186.00', $after, true) && in_array('val:ITEM-A_1:3=4262806.62', $after, true),
   'the big item now opens with 2,186 units at 4,262,806.62', implode(' ', $after));
ok(in_array('qty:ITEM-B_1=1.00', $after, true) && in_array('val:ITEM-B_1:3=408.54', $after, true),
   'the inserted item is there with both its rows', implode(' ', $after));
ok(in_array('val:ITEM-D_1:2=0.00', $after, true) && in_array('val:ITEM-D_1:3=425.00', $after, true),
   'the LIFO row is cleared and the AVG row carries the value', implode(' ', $after));
ok(in_array('qty:ITEM-C_1=12.00', $after, true) && in_array('val:ITEM-C_1:3=999.00', $after, true),
   'the item left for a person is untouched', implode(' ', $after));
ok(in_array('val:ITEM-A_1:1=0.00', $after, true),
   'the FIFO row that was already nil stays nil rather than being written again', implode(' ', $after));
ok(abs($openingTotal() - 4264639.16) < 0.005,
   'the opening the reports read is the previous closing plus the untouched 999.00', (string)$openingTotal());

$again = $run('--company 1 --fy 2 --branch 1');
ok(str_contains($again, "Nothing to change: this year already opens with the previous year's closing stock."),
   'running it again finds nothing to do', substr($again, -300));
ok($stored() === $after, 'and changes nothing', implode(' ', $stored()));

// =====================================================================================================
//  what it refuses
// =====================================================================================================
$reload();
$neg = $run('--company 1 --fy 2 --branch 1', json_encode([
    ['itm_id_unit_id' => 'ITEM-A_1', 'item_name' => 'BROKEN ITEM', 'method' => 'AVG',
     'item_qty_avail' => -1804, 'item_value_avail' => 4590450.00],
]));
ok(str_contains($neg, 'closed the previous year on -1,804.00 units, which no stock can do'),
   'a negative closing quantity is named rather than carried', $neg);
ok(!str_contains($neg, 'BROKEN ITEM                        quantity'),
   'and no row is planned for it - the previous year has to be fixed first', $neg);
ok($stored() === $before, 'nothing was written', implode(' ', $stored()));

$reload();
$empty = $run('--company 1 --fy 2 --branch 1', '[]');
ok(str_contains($empty, 'the previous year closed with no stock; there is nothing to carry'),
   'a previous year with no stock is a no-op, not an error', $empty);
ok($stored() === $before, 'and writes nothing', implode(' ', $stored()));

$noFy = $run('--company 1 --fy 1 --branch 1');
ok(str_contains($noFy, 'no earlier financial year'),
   'the first year of a company has nothing to carry from', $noFy);

$noArgs = $run('--company 1 --fy 2');
ok(str_contains($noArgs, '--company, --fy and --branch are all required'),
   'the branch is required: opening stock is held per branch', $noArgs);

// =====================================================================================================
//  the CSV, so a change can be undone by hand
// =====================================================================================================
$reload();
$csvFile = $H . '/carry.csv';
@unlink($csvFile);
$run('--company 1 --fy 2 --branch 1 --csv ' . escapeshellarg($csvFile));
$csv = is_file($csvFile) ? (string)file_get_contents($csvFile) : '';
ok(substr_count(trim($csv), "\n") === 9, 'the CSV has a line per planned row', $csv);
ok(str_contains($csv, 'itmoppybal,1,2,1,ITEM-A_1,"P.S PLASTIC STRIPS V200",,quantity,0.0000,2186.0000'),
   'each line records the value it replaces, so it can be put back', $csv);
ok(str_contains($csv, 'itmoppyval,1,2,1,ITEM-B_1,"FRAMED PICTURES",3,"value (AVG)",,408.5400'),
   'a new row is marked by an empty "was" rather than a nil that was never there', $csv);
@unlink($csvFile);

// =====================================================================================================
//  if what it wrote does not read back, it keeps nothing
// =====================================================================================================
$reload();
sh($psql . ' -c ' . escapeshellarg(
    "CREATE FUNCTION skim() RETURNS trigger AS \$\$ BEGIN NEW.itm_op_val_amt := NEW.itm_op_val_amt - 1; RETURN NEW; END \$\$ LANGUAGE plpgsql;
     CREATE TRIGGER skim_all BEFORE INSERT OR UPDATE ON itmoppyval FOR EACH ROW EXECUTE FUNCTION skim()"));
$skimmed = $run('--company 1 --fy 2 --branch 1 --apply');
ok(str_contains($skimmed, 'THEY DO NOT AGREE'), 'a write that lands differently is caught', substr($skimmed, -600));
ok(str_contains($skimmed, 'Rolled back: what was written did not read back'),
   'and the whole thing is rolled back rather than half-applied', substr($skimmed, -400));
ok($stored() === $before, 'the stored rows are exactly as they were', implode(' ', $stored()));
sh($psql . ' -c ' . escapeshellarg('DROP TRIGGER skim_all ON itmoppyval; DROP FUNCTION skim()'));
$reload();
sh($psql . ' -c ' . escapeshellarg(
    "CREATE FUNCTION bend() RETURNS trigger AS \$\$ BEGIN NEW.itm_op_bal_qty := NEW.itm_op_bal_qty + 1; RETURN NEW; END \$\$ LANGUAGE plpgsql;
     CREATE TRIGGER bend_qty BEFORE INSERT OR UPDATE ON itmoppybal FOR EACH ROW EXECUTE FUNCTION bend()"));
$bent = $run('--company 1 --fy 2 --branch 1 --apply');
ok(str_contains($bent, 'opening quantities match the previous closing, item by item: NO'),
   'a quantity that lands wrong is caught even though every value total is right', substr($bent, -700));
ok(str_contains($bent, 'Rolled back: what was written did not read back'),
   'and that alone rolls the whole run back', substr($bent, -400));
ok($stored() === $before, 'nothing was kept', implode(' ', $stored()));
sh($psql . ' -c ' . escapeshellarg('DROP TRIGGER bend_qty ON itmoppybal; DROP FUNCTION bend()'));
$clean = $run('--company 1 --fy 2 --branch 1 --apply');
ok(str_contains($clean, 'APPLIED and committed'), 'with the interference gone the same run applies', substr($clean, -300));

echo "\nchecks passed: $pass | failed: $fail\n";
exit($fail ? 1 : 0);
