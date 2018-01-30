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

abstract class AbstractDatabaseDumper implements DatabaseDumperInterface
{
    /**
     * @var array
     */
    protected $connection;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param array           $connection
     * @param LoggerInterface $logger
     */
    public function __construct(array $connection, LoggerInterface $logger)
    {
        $this->connection = $connection;
        $this->logger = $logger;
    }

    /**
     * @param array $databases
     *
     * @return array
     */
    public function dump(array $databases)
    {
        $dumpedFiles = [];
        foreach ($databases as $database) {
            try {
                $dumpFilename = $this->getDumpFilename($database['name']);
                $this->logger->notice('Dumping '.$database['name'].' to '.$dumpFilename);

                // todo: this should be fixed during configuration stage
                $database['exclude_tables'] = isset($database['exclude_tables']) ? $database['exclude_tables'] : [];

                // todo: add filename to wipe list
                $this->dumpDatabase($database['name'], $database['exclude_tables'], $dumpFilename);
                $dumpedFiles[] = $dumpFilename;
            } catch (\Exception $e) {
                $this->logger->error('Exception while dumping '.$database['name'].': '.$e->getMessage());
            }
        }

        return $dumpedFiles;
    }

    /**
     * @param string $databaseName
     *
     * @return string
     *
     * @throws \Exception
     */
    protected function getDumpFilename($databaseName)
    {
        $filename = date('YmdHis').'_'.$this->getDriverName().'_'.$databaseName.'.sql';
        if (is_file($filename)) {
            throw new \Exception('Database dump file '.$filename.' already exists');
        }

        return $filename;
    }

    abstract protected function dumpDatabase($database, array $excludeTables, $target);

    abstract protected function getDriverName();
}
