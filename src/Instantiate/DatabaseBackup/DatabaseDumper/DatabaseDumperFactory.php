<?php
/*
 * This file is part of the DB Backup utility.
 *
 * (c) Tom Adam <tomadam@instantiate.co.uk>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Instantiate\DatabaseBackup\DatabaseDumper;

use Psr\Log\LoggerInterface;

class DatabaseDumperFactory
{
    /**
     * @param array           $connection
     * @param LoggerInterface $logger
     *
     * @return DatabaseDumperInterface
     *
     * @throws \Exception
     */
    public static function getDumper(array $connection, LoggerInterface $logger)
    {
        switch ($connection['driver']) {
            case 'mysql':
                return new MysqlDumper($connection, $logger);
                break;
            case 'postgres':
                return new PostgresDumper($connection, $logger);
                break;
            default:
                throw new \Exception('Driver type "'.$connection['driver'].'" invalid');
        }
    }
}
