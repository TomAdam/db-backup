<?php
/**
 * Instantiate
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Instantiate EULA that is bundled with
 * this package in the file LICENSE.
 *
 * If you did not receive a copy of the license please send an email
 * to info@instantiate.co.uk so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade the extension
 * to newer versions in the future. If you wish to customize the extension
 * for your needs please send an email to info@instantiate.co.uk for more
 * information
 *
 * @author     Instantiate
 * @copyright  Copyright (c) 2015 Instantiate
 *
 * @see       http://www.instantiate.co.uk/
 *
 * @license    For the full copyright and license information, please view the
 *             LICENSE file that was distributed with this source code.
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
