<?php
/**
 * SANDBOX-ONLY harness: boots the real CodeIgniter app code from a sandbox copy (no .env, own writable/)
 * against a throw-away local PostgreSQL, sets the session values the models read, stubs the external
 * DB library and stock valuation, and gives helpers to run the real report models.
 */
namespace App\Libraries {
    class externaldb {
        public $session;
        public function __construct() { helper(['custom']); $this->session = \Config\Services::session(); }
        public function __call($n, $a) { return \Config\Database::connect(); }   // every "other" DB = the test DB
    }
}

namespace App\Models\Admin {
    /** Stock valuation is outside the scope of the accounting fixes; feed fixed values. */
    class StockStatusModelStub {
        public static float $opening = 40000.0;
        public static float $closing = 65000.0;
        public function closingStockTotal($from, $to, array $filters = []): float { return self::$closing; }
    }
}

namespace {
    $SBX = getenv('SBX') ?: '/var/tmp/erp_sbx';
    if (!is_dir("$SBX/app")) { fwrite(STDERR, "sandbox missing: run sync_sandbox.sh\n"); exit(2); }

    // Point the app's default DB group at the local throw-away server (BaseConfig env overrides).
    foreach ([
        'database.default.hostname' => '127.0.0.1',
        'database.default.port'     => getenv('ERP_TEST_PGPORT') ?: '5433',
        'database.default.username' => 'postgres',
        'database.default.password' => '',
        'database.default.database' => 'erp_test',
        'database.default.DBDriver' => 'Postgre',
        'CI_ENVIRONMENT'            => 'development',
    ] as $k => $v) { putenv("$k=$v"); $_ENV[$k] = $v; $_SERVER[$k] = $v; }

    chdir($SBX);
    error_reporting(E_ALL);
    $_SERVER['CI_ENVIRONMENT'] = 'development';
    define('ENVIRONMENT', 'development');
    define('CI_DEBUG', true);
    define('HOMEPATH', realpath($SBX) . DIRECTORY_SEPARATOR);
    define('CONFIGPATH', realpath($SBX . '/app/Config') . DIRECTORY_SEPARATOR);
    define('PUBLICPATH', HOMEPATH);
    require CONFIGPATH . 'Paths.php';
    $paths = new \Config\Paths();
    define('APPPATH', realpath(rtrim($paths->appDirectory, '\\/ ')) . DIRECTORY_SEPARATOR);
    define('ROOTPATH', realpath(APPPATH . '../') . DIRECTORY_SEPARATOR);
    define('SYSTEMPATH', realpath(rtrim($paths->systemDirectory, '\\/')) . DIRECTORY_SEPARATOR);
    define('WRITEPATH', realpath(rtrim($paths->writableDirectory, '\\/ ')) . DIRECTORY_SEPARATOR);
    define('TESTPATH', HOMEPATH . 'tests/');
    define('CIPATH', realpath(SYSTEMPATH . '../') . DIRECTORY_SEPARATOR);
    define('FCPATH', HOMEPATH);
    define('SUPPORTPATH', HOMEPATH . 'tests/_support/');
    define('COMPOSER_PATH', (string) realpath(HOMEPATH . 'vendor/autoload.php'));
    define('VENDORPATH', realpath(HOMEPATH . 'vendor') . DIRECTORY_SEPARATOR);
    require $paths->systemDirectory . '/Boot.php';
    \CodeIgniter\Boot::bootTest($paths);
    error_reporting(E_ALL & ~E_DEPRECATED);

    function harness_session(array $over = []): void {
        $_SESSION = array_merge([
            'ses_company_id'           => 1,
            'ses_comp_fy_id'           => 1,
            'ses_boid'                 => 1,
            'ses_company_fy_beginning' => '2025-04-01',
            'ses_company_fy_end'       => '2026-03-31',
            'ses_dflt_val_method'      => 'AVG',
            'ses_company_name'         => 'Test Co',
        ], $over);
    }

    /** Real ReportingModel with stock replaced by a stub. */
    function harness_reporting(array $sess = []): \App\Models\Admin\ReportingModel {
        harness_session($sess);
        $m = new \App\Models\Admin\ReportingModel();
        $m->StockStatusModel = new \App\Models\Admin\StockStatusModelStub();
        return $m;
    }

    function harness_db(): \CodeIgniter\Database\BaseConnection { return \Config\Database::connect(); }
}
