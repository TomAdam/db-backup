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
 * @link       http://www.instantiate.co.uk/
 *
 * @license    For the full copyright and license information, please view the
 *             LICENSE file that was distributed with this source code.
 */
namespace Instantiate\DatabaseBackup\DatabaseDumper;

abstract class AbstractDatabaseDumper
{
    /**
     * @param array $databases
     * @return array
     *
     * TODO: error handling - should continue after a database fails
     */
    public function dump(array $databases)
    {
        $dumpFilename = [];
        foreach ($databases as $id => $database) {
            $dumpFilename[$id] = $this->getDumpFilename($database['name']);
            echo 'Dumping '.$database['name'].' to '.$dumpFilename[$id].'...';
            $database['exclude_tables'] = isset($database['exclude_tables']) ? $database['exclude_tables'] : [];
            $this->dumpDatabase($database['name'], $database['exclude_tables'], $dumpFilename[$id]);
            echo " done\n";
        }
        return $dumpFilename;
    }

    /**
     * @param string $databaseName
     * @return string
     * @throws \Exception
     */
    protected function getDumpFilename($databaseName)
    {
        $filename = date('YmdHis').'_'.$this->getDriverName().'_'.$databaseName.'.sql';
        if (is_file($filename)) {
            throw new \Exception('Database dump file '. $filename.' already exists');
        }

        return $filename;
    }

    abstract protected function dumpDatabase($database, array $excludeTables, $target);

    abstract protected function getDriverName();
}
