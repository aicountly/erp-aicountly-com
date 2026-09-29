<?php
namespace App\Controllers;
use App\Models\InventoryCronSnapshotModel;
use App\Models\CommonModel;
use App\Controllers\BaseController;

class ErpCronJob extends BaseController{

    function __construct(){  
        helper(['form', 'url', 'text', 'custom_hepler']);   
        $this->InventorySnapshot = new InventoryCronSnapshotModel();
    }

    public function index()
    {
        // ⏱️ Set maximum execution time = 3 hours
        set_time_limit(10800);

        // 💾 Set memory limit for long running process
        ini_set('memory_limit', '256M');

        // 🔑 Key Authentication
        $key = $this->request->getGet('key');
        $secret_key = 'xK9#mP2$qL7!nR4';  

        if ($key !== $secret_key) {
            return $this->response->setStatusCode(403)->setBody('Forbidden');
        }

        // 🔒 Lock File — Prevent overlapping cron runs
        $lockFile = sys_get_temp_dir() . '/erpcronjob.lock';

        if (file_exists($lockFile)) {
            $pid = (int) file_get_contents($lockFile);

            // Check if the previous process is still running
            if ($pid > 0 && file_exists("/proc/$pid")) {
                echo "Cron job already running (PID: $pid). Exiting safely.";
                exit(0); // ← Safe exit, do NOT run again
            } else {
                // Old lock file exists but process is dead — remove it
                unlink($lockFile);
            }
        }

        // ✅ Create lock file with current process ID
        file_put_contents($lockFile, getmypid());

        // 📝 Log start time
        $startTime = date('Y-m-d H:i:s');
        file_put_contents(
            WRITEPATH . 'logs/erpcronjob_log.txt',
            "[$startTime] Cron Started\n",
            FILE_APPEND
        );

        try {
            // ▶️ Run the actual cron job
            $result = $this->InventorySnapshot->runAll();
            echo $result;

            // 📝 Log success
            $endTime = date('Y-m-d H:i:s');
            file_put_contents(
                WRITEPATH . 'logs/erpcronjob_log.txt',
                "[$endTime] Cron Completed Successfully\n",
                FILE_APPEND
            );

        } catch (\Exception $e) {
            // 📝 Log error if something goes wrong
            $errorTime = date('Y-m-d H:i:s');
            file_put_contents(
                WRITEPATH . 'logs/erpcronjob_log.txt',
                "[$errorTime] ERROR: " . $e->getMessage() . "\n",
                FILE_APPEND
            );
            echo "Error: " . $e->getMessage();

        } finally {
            // 🔓 Always remove lock file when done (success or error)
            if (file_exists($lockFile)) {
                unlink($lockFile);
            }
        }
    }
}