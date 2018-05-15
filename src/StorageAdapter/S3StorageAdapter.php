<?php
/*
 * This file is part of the DB Backup utility.
 *
 * (c) Tom Adam <tomadam@instantiate.co.uk>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Instantiate\DatabaseBackup\StorageAdapter;

use Aws\S3\S3Client;
use League\Flysystem\AwsS3v3\AwsS3Adapter;
use League\Flysystem\Filesystem;

class S3StorageAdapter extends AbstractFlysystemStorageAdapter
{
    /**
     * Provide S3 filesystem
     *
     * {@inheritdoc}
     */
    protected function getFilesystem(array $config): Filesystem
    {
        $client = new S3Client([
            'credentials' => [
                'key' => $config['key'],
                'secret' => $config['secret'],
            ],
            'region' => $config['region'],
            'version' => 'latest',
        ]);

        $adapter = new AwsS3Adapter($client, $config['bucket'], $config['prefix']);

        return new Filesystem($adapter);
    }
}
