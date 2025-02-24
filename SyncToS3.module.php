<?php

namespace ProcessWire;

/**
 * ProcessWire 'SyncToS3' Module
 *
 * Sync and Backup all page assets uploaded into PW to Amazon S3 and Deliver them via Amazon Cloudfront
 *
 * ProcessWire 3.x or higher
 * Copyright (C) 2025 by Rudy Affandi
 * Licensed under GNU/GPL v2
 *
 * https://github.com/lesaff/processwiresynctos3
 */

use Aws\S3\S3Client;
use Aws\Exception\AwsException;

class SyncToS3 extends Process
{
    public function __construct()
    {
        // Load AWS SDK
        require_once __DIR__ . '/aws/aws-autoloader.php';
    }

    public function init()
    {
        $this->addHookAfter('Pages::saveReady', $this, 'startBackgroundSync');

        if ($this->cloudfront_url) {
            // Redirect image' URLS to Cloudfront
            $this->addHookAfter('Pageimage::url', $this, 'redirectURL');

            // Redirect assets' URLS to Cloudfront
            $this->addHookAfter('PagefilesManager::url', $this, 'redirectURL');
        }
    }

    /**
     * Function to run the sync job in the background
     *
     * @param HookEvent $event
     * @return void
     */
    public function startBackgroundSync(HookEvent $event)
    {
        // Run the sync job in the background
        $scriptPath = __DIR__ . '/synctos3.sh ' . $this->s3_bucket_name . ' ' . $this->wire->config->paths->site . ' ' . $this->s3_region . ' ' . $this->access_key_id . ' ' . $this->secret_access_key;

        $output = shell_exec($scriptPath);
        $this->wire->log->save('SyncToS3', $output);
    }

    /**
     * Function to redirect the URL to Cloudfront
     *
     * @param [type] $event
     * @return void
     */
    public function redirectURL($event)
    {
        if ($event->page->template == 'admin') {
            return;
        } else {
            $event->return = $this->cloudfront_url . "/site/assets/files/" .  $event->object->page . "/" . $event->object->name;
        }
    }

    /**
     * Function to sync the site folder to S3
     *
     * @param HookEvent $event
     * @return void
     */
    public function syncToS3(HookEvent $event)
    {
        // AWS S3 configuration
        $bucket = $this->s3_bucket_name;
        $awsKey = $this->access_key_id;
        $awsSecret = $this->secret_access_key;
        $region = $this->s3_region;
        $folders = explode(',', str_replace(' ', '', $this->folders_to_include));

        // Initialize S3 client
        $s3Client = new S3Client([
            'version' => 'latest',
            'region' => $region,
            'credentials' => [
                'key' => $awsKey,
                'secret' => $awsSecret,
            ],
        ]);

        // Define the directories to sync
        $directoriesToSync = preg_filter('/^/', $this->wire->config->paths->site, $folders);

        // Sync each directory to S3
        foreach ($directoriesToSync as $directory) {
            $this->syncDirectoryToS3($s3Client, $bucket, $directory);
        }
    }

    /**
     * Function to 
     *
     * @param [type] $s3Client
     * @param [type] $bucket
     * @param [type] $sourceDir
     * @return void
     */
    private function syncDirectoryToS3($s3Client, $bucket, $sourceDir)
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($sourceDir)
        );

        // Get the list of existing objects in the S3 bucket
        $existingFiles = [];
        try {
            $result = $s3Client->listObjects([
                'Bucket' => $bucket,
            ]);

            if (isset($result['Contents'])) {
                foreach ($result['Contents'] as $object) {
                    $existingFiles[$object['Key']] = $object['LastModified'];
                }
            }
        } catch (AwsException $e) {
            $this->wire->log->save('SyncToS3', "Error listing objects: " . $e->getMessage());
            return; // Exit if we can't list objects
        }

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $filePath = $file->getPathname();

                // Find the position of "site" in the path
                $sitePath = strpos($filePath, 'site');

                // Check if "public" was found
                if ($sitePath !== false) {
                    // Get the substring starting from "public"
                    $key = substr($filePath, $sitePath);
                }


                // Check if the file exists in S3 and if it has been modified
                if (!isset($existingFiles[$key]) || $file->getMTime() > $existingFiles[$key]->getTimestamp()) {
                    try {
                        // Upload the file to S3
                        $s3Client->putObject([
                            'Bucket' => $bucket,
                            'Key' => $key,
                            'SourceFile' => $filePath,
                        ]);
                        $this->wire->log->save('SyncToS3', "Uploaded: $key");
                    } catch (AwsException $e) {
                        $this->wire->log->save('SyncToS3', "Error uploading $key: " . $e->getMessage());
                    }
                } else {
                    $this->wire->log->save('SyncToS3', "Skipped (no change): $key");
                }
            }
        }
    }
}
