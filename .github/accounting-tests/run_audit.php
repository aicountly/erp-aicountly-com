<?php
/** SANDBOX-ONLY: run the real audit:books command against the throw-away DB. usage: php run_audit.php --company=1 --fy=1 --branch=1 [...] */
require __DIR__ . '/boot.php';
\App\Models\Admin\StockStatusModelStub::$opening = (float)(getenv('STOCK_OPEN') !== false ? getenv('STOCK_OPEN') : 40000);
\App\Models\Admin\StockStatusModelStub::$closing = (float)(getenv('STOCK_CLOSE') !== false ? getenv('STOCK_CLOSE') : 65000);
// the sandbox has no item tables: the stock walk is replaced by the fixed figures above in THIS sandbox copy only
$SBX = getenv('SBX') ?: '/var/tmp/erp_sbx';
file_put_contents("$SBX/app/Models/Admin/StockStatusModel.php", "<?php\nnamespace App\\Models\\Admin;\nclass StockStatusModel {\n public function __construct() {}\n public function closingStockTotal(\$from, \$to, array \$filters = []): float { return StockStatusModelStub::\$closing; }\n}\n");
$_SERVER['argv'] = array_merge(['spark', 'audit:books'], array_slice($argv, 1));
\CodeIgniter\CLI\CLI::init();
$cmd = new \App\Commands\AuditBooks(\Config\Services::logger(), \Config\Services::commands());
exit((int)$cmd->run([]));
