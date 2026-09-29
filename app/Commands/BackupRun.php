<?php namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use Google_Client;
use Google_Service_Drive;
use Google_Http_MediaFileUpload;

class BackupRun extends BaseCommand
{
    protected $group       = 'backup';
    protected $name        = 'backup:run';
    protected $description = 'CLI helper that dumps DB and uploads to Google Drive';

    public function run(array $params)
    {
        $jobId = $params[0] ?? '';
        $jobDir  = WRITEPATH . "backups/$jobId";
        $metaFile = "$jobDir/meta.json";
        if (!$jobId || !is_file($metaFile)) return;

        $meta   = json_decode(file_get_contents($metaFile), true);
        $sqlGz  = $meta['sql'];
        $chunk  = $meta['chunk'];

        /* 1️⃣  mysqldump → gzip (stream) */
        $cnf = config('Database')->default;
        $cmd = sprintf(
            'mysqldump -h%s -u%s -p%s %s --single-transaction 2>/dev/null | gzip > %s',
            escapeshellarg($cnf['hostname']),
            escapeshellarg($cnf['username']),
            escapeshellarg($cnf['password']),
            escapeshellarg($cnf['database']),
            escapeshellarg($sqlGz)
        );
        exec($cmd);

        $meta['progress'] = 10;
        file_put_contents($metaFile, json_encode($meta));

        /* 2️⃣  Drive upload in chunks */
        $client = new Google_Client();
        $client->setAccessToken(
            json_decode(file_get_contents("$jobDir/google_token.json"), true)
        );
        $drive = new Google_Service_Drive($client);

        $file   = new \Google_Service_Drive_DriveFile([
            'name' => basename($sqlGz)
        ]);
        $fileSize = filesize($sqlGz);

        $media = new Google_Http_MediaFileUpload(
            $client,
            $drive->files->create($file, ['fields' => 'id']),
            'application/gzip',
            null,
            true,
            $chunk
        );
        $media->setFileSize($fileSize);

        $fh   = fopen($sqlGz, 'rb');
        $sent = 0;
        while (!$media->nextChunk(fread($fh, $chunk)) === false) {
            $sent += $chunk;
            $meta['progress'] = min(10 + intval($sent / $fileSize * 90), 99);
            file_put_contents($metaFile, json_encode($meta));
        }
        fclose($fh);

        /* 3️⃣  finalise */
        $meta['progress'] = 100;
        file_put_contents($metaFile, json_encode($meta));

        /* 4️⃣  wipe temp files + dir */
        @unlink($sqlGz);
        @unlink("$jobDir/google_token.json");
        @unlink($metaFile);
        helper('filesystem');
        delete_files($jobDir, true);
        @rmdir($jobDir);
    }
}
