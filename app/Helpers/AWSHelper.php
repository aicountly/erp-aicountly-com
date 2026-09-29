<?php
namespace App\Helpers;
require APPPATH.'ThirdParty/aws-autoloader.php';
use Aws\S3\S3Client;

class AWSHelper
{
    private $s3Client;
   // Add an optional parameter for the endpoint
    public function __construct($endpoint = null)
    {
        $config = [
            'version' => 'latest',
            'region'  => 'de', // Replace with your AWS region or OVHcloud region
            'credentials' => [
              'key'    => '0ba5d225cdce45c395236251b008af84',
              'secret' => '9640c8e093bb4c1f952555442775d3c8',
            ],
        ];

        // Add the 'endpoint' option if provided
        if ($endpoint) {
            $config['endpoint'] = $endpoint;
        }

        $this->s3Client = new S3Client($config);
    }

    public function createBucket($bucketName)
    {
        try {
            $this->s3Client->createBucket([
                'Bucket' => $bucketName,
            ]);
            return true;
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return [];
        }
    }

    public function uploadFile($bucketName, $localFilePath, $s3Key)
    {
        try {
            $this->s3Client->putObject([
                'Bucket' => $bucketName,
                'Key'    => $s3Key,
                'Body'   => file_get_contents($localFilePath),
            ]);
            return true;
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return false;
        }
    }

    public function listFiles($bucketName)
    {
        try {
            $objects = $this->s3Client->listObjects([
                'Bucket' => $bucketName,
            ]);

            $files = [];
            foreach ($objects['Contents'] as $object) {
                $files[] = [
                    'name' => $object['Key'],
                    'url'  => $this->s3Client->getObjectUrl($bucketName, $object['Key']),
                    'Bucket' => $bucketName,
                ];
            }

            return $files;
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return [];
        }
    }
    public function downloadFile($bucketName, $s3Key, $localFilePath)
    {
      try {
        $directory = dirname($localFilePath);
        if (!is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

        $result = $this->s3Client->getObject([
            'Bucket' => $bucketName,
            'Key'    => $s3Key,
            'SaveAs' => $localFilePath,
        ]);
     
        return true;
      } catch (\Exception $e) {
 
        echo 'Error downloading the file:' . $e->getMessage();
        return false;
      }
    }

    public function download($bucketName, $s3Key)
    {
      try {
        // $directory = dirname($localFilePath);
        // if (!is_dir($directory)) {
        //     mkdir($directory, 0777, true);
        // }

        $result = $this->s3Client->getObject([
            'Bucket' => $bucketName,
            'Key'    => $s3Key,
            // 'SaveAs' => $localFilePath,
        ]);
     
        return $result;
      } catch (\Exception $e) {
 
        echo 'Error downloading the file:' . $e->getMessage();
        return false;
      }
    }
    // Add this method to your AWSHelper class
    public function deleteFile($bucketName, $s3Key)
    {
        try {
            $this->s3Client->deleteObject([
                'Bucket' => $bucketName,
                'Key'    => $s3Key,
            ]);
            return true;
        } catch (\Exception $e) {
            // Handle errors
            return false;
        }
    }
    public function renameFileOnS3($bucketName, $originalS3Key, $newS3Key)
    {
        try {
            // Check if the new name already exists
            if ($this->fileExistsOnS3($bucketName, $newS3Key)) {
                // Handle the case where the new name already exists
                return false;
            }
    
            // Copy the file to the new name
            $this->s3Client->copyObject([
                'Bucket'     => $bucketName,
                'CopySource' => $bucketName . '/' . $originalS3Key,
                'Key'        => $newS3Key,
            ]);
    
            // Delete the original file
            $this->s3Client->deleteObject([
                'Bucket' => $bucketName,
                'Key'    => $originalS3Key,
            ]);
    
            return true;
        } catch (\Exception $e) {
            // Handle errors
            return false;
        }
    }

    public function fileExistsOnS3($bucketName, $s3Key)
    {
        try {
            $result = $this->s3Client->headObject([
                'Bucket' => $bucketName,
                'Key'    => $s3Key,
            ]);
    
            return true;
        } catch (\Exception $e) {
            // Object does not exist
            return false;
        }
    }
    // Add this method to your AWSHelper class
    public function uploadAndRenameFile($bucketName, $localFilePath, $newS3Key)
    {
        try {
            // Upload the file to S3 with the new key
            $this->s3Client->putObject([
                'Bucket' => $bucketName,
                'Key'    => $newS3Key,
                'Body'   => file_get_contents($localFilePath),
            ]);
    
            // Delete the original file from S3 if needed
            // Uncomment the following line if you want to delete the original file
            // $this->deleteFile($bucketName, $originalS3Key);
    
            return true;
        } catch (\Exception $e) {
            echo 'Error: ' . $e->getMessage();
            return [];
        }
    }

}
