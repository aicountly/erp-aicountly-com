<?php
/** SANDBOX-ONLY child process: runs the REAL Export controller method with the given query string; the file goes to stdout. */
require __DIR__ . '/boot.php';
[$self, $method, $qs] = $argv + [null, 'balance_sheet', ''];
parse_str($qs, $_GET);
$m   = harness_reporting();
$ctl = (new ReflectionClass(\App\Controllers\Admin\Export::class))->newInstanceWithoutConstructor();
$ctl->session        = \Config\Services::session();
$ctl->ReportingModel = $m;
$ctl->$method();          // ends with ReportSheetWriter::deliver() -> exit
fwrite(STDERR, "controller returned without sending a file\n");
exit(3);
