<?php
/** SANDBOX-ONLY: runs the real books:carry-openings command against the throw-away DB.
 *  The sandbox has no item tables, so the stock walk the reports do is replaced by a fixed figure in THIS
 *  sandbox copy only, exactly as run_repair.php does. */
require __DIR__ . '/boot.php';
harness_session();
$SBX = getenv('SBX') ?: '/var/tmp/erp_sbx';
$close = getenv('STOCK_CLOSE') !== false ? (float)getenv('STOCK_CLOSE') : 0.0;
file_put_contents("$SBX/app/Models/Admin/StockStatusModel.php",
    "<?php\nnamespace App\\Models\\Admin;\nclass StockStatusModel {\n public function __construct() {}\n"
  . " public function closingStockTotal(\$from, \$to, array \$filters = []): float { return " . var_export($close, true) . "; }\n}\n");
$_SERVER['argv'] = array_merge(['spark', 'books:carry-openings'], array_slice($argv, 1));
\CodeIgniter\CLI\CLI::init();
$cmd = new \App\Commands\CarryOpenings(\Config\Services::logger(), \Config\Services::commands());
exit((int)$cmd->run([]));
