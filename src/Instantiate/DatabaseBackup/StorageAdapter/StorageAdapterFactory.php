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

use Psr\Log\LoggerInterface;

class StorageAdapterFactory
{
    /**
     * @param array           $config
     * @param LoggerInterface $logger
     *
     * @return StorageAdapterInterface
     *
     * @throws \Exception
     */
    public static function getStorageAdapter(array $config, LoggerInterface $logger)
    {
        switch ($config['type']) {
            case 's3':
                return new S3StorageAdapter($config['config'], $logger);
                break;
            default:
                throw new \Exception('Storage adapter type "'.$config['type'].'" invalid');
        }
    }
}
