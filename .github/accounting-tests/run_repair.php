<?php
/** SANDBOX-ONLY: runs the real books:repair-composition command against the throw-away DB. */
require __DIR__ . '/boot.php';
harness_session(['bo_gstin_type' => 2]);
$_SERVER['argv'] = array_merge(['spark', 'books:repair-composition'], array_slice($argv, 1));
\CodeIgniter\CLI\CLI::init();
$cmd = new \App\Commands\RepairComposition(\Config\Services::logger(), \Config\Services::commands());
exit((int)$cmd->run([]));
