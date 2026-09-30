<?php
/** SANDBOX-ONLY: runs the real books:repair-composition command against the throw-away DB. */
require __DIR__ . '/boot.php';
harness_session(['bo_gstin_type' => 2]);
// The sandbox has no item tables, so the stock walk the Balance Sheet does is replaced by fixed figures in THIS
// sandbox copy only - exactly as run_audit.php does. REPAIR_NO_STOCK_STUB=1 leaves the real model in place, which
// makes the reports fail here: that is how the savepoint around the reconciliation read is tested.
$SBX = getenv('SBX') ?: '/var/tmp/erp_sbx';
if (getenv('REPAIR_NO_STOCK_STUB') === '1') {
    // put the real model back: it reads item tables the sandbox does not have, so the reports fail here
    copy(dirname(__DIR__, 2) . '/app/Models/Admin/StockStatusModel.php', "$SBX/app/Models/Admin/StockStatusModel.php");
} else {
    \App\Models\Admin\StockStatusModelStub::$closing = (float)(getenv('STOCK_CLOSE') !== false ? getenv('STOCK_CLOSE') : 65000);
    file_put_contents("$SBX/app/Models/Admin/StockStatusModel.php", "<?php\nnamespace App\\Models\\Admin;\nclass StockStatusModel {\n public function __construct() {}\n public function closingStockTotal(\$from, \$to, array \$filters = []): float { return StockStatusModelStub::\$closing; }\n}\n");
}
$_SERVER['argv'] = array_merge(['spark', 'books:repair-composition'], array_slice($argv, 1));
\CodeIgniter\CLI\CLI::init();
$cmd = new \App\Commands\RepairComposition(\Config\Services::logger(), \Config\Services::commands());
exit((int)$cmd->run([]));
