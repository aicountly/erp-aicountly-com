<?php
/** SANDBOX-ONLY: runs the real books:carry-opening-stock command against the throw-away DB.
 *  The Stock Status report reads item tables this sandbox does not carry, so in THIS sandbox copy only it is
 *  replaced by a stub that returns whatever CARRY_ROWS holds. What is under test is the command, not the report. */
require __DIR__ . '/boot.php';
harness_session();
$SBX  = getenv('SBX') ?: '/var/tmp/erp_sbx';
$rows = getenv('CARRY_ROWS');
if ($rows === false) {
    $rows = json_encode([
        ['itm_id_unit_id' => 'ITEM-A_1', 'item_name' => 'P.S PLASTIC STRIPS V200', 'method' => 'AVG',
         'item_qty_avail' => 2186,  'item_value_avail' => 4262806.62],
        ['itm_id_unit_id' => 'ITEM-B_1', 'item_name' => 'FRAMED PICTURES', 'method' => 'AVG',
         'item_qty_avail' => 1,     'item_value_avail' => 408.54],
        ['itm_id_unit_id' => 'ITEM-D_1', 'item_name' => 'NAILS', 'method' => 'AVG',
         'item_qty_avail' => 5,     'item_value_avail' => 425.00],
        // stock held at no cost: the quantity still has to be carried, or the next year issues from nothing
        ['itm_id_unit_id' => 'ITEM-E_1', 'item_name' => 'FREE SAMPLES', 'method' => 'AVG',
         'item_qty_avail' => 3,     'item_value_avail' => 0.00],
    ]);
}
$stub = "<?php\nnamespace App\\Models\\Admin;\nclass StockStatusModel {\n"
      . " public function __construct() {}\n"
      . " public function closingStockTotal(\$from, \$to, array \$filters = []): float { return 0.0; }\n"
      . " public function inventoryStatusPaged(\$cmpId = null, \$boId = null, \$fyId = null, \$input = null) {\n"
      . "   file_put_contents(sys_get_temp_dir() . '/carry_fyid', (string)\$fyId);\n"
      . "   \$rows = json_decode(" . var_export($rows, true) . ", true) ?: [];\n"
      . "   \$page = max(1, (int)(\$input['pq_curpage'] ?? 1)); \$rpp = max(1, (int)(\$input['pq_rpp'] ?? 100));\n"
      . "   return json_encode(['curPage' => \$page, 'totalRecords' => count(\$rows),\n"
      . "                       'data' => array_slice(\$rows, (\$page - 1) * \$rpp, \$rpp)]);\n"
      . " }\n}\n";
file_put_contents("$SBX/app/Models/Admin/StockStatusModel.php", $stub);
$_SERVER['argv'] = array_merge(['spark', 'books:carry-opening-stock'], array_slice($argv, 1));
\CodeIgniter\CLI\CLI::init();
$cmd = new \App\Commands\CarryOpeningStock(\Config\Services::logger(), \Config\Services::commands());
exit((int)$cmd->run([]));
