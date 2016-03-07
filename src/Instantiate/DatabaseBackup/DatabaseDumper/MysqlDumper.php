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

use Instantiate\DatabaseBackup\Util\Process;

class MysqlDumper extends AbstractDatabaseDumper
{
    /**
     * @param string $database
     * @param array  $excludeTables
     * @param string $target
     */
    protected function dumpDatabase($database, array $excludeTables, $target)
    {
        // structure
        Process::exec(
            'mysqldump --defaults-extra-file={password_file} -v -h {host} -u {user} --quick --single-transaction --no-data {db} > {temp_sql_file}',
            [
                '{password_file}' => $this->connection['password_file'],
                '{host}' => $this->connection['host'],
                '{user}' => $this->connection['user'],
                '{db}' => $database,
                '{temp_sql_file}' => $target,
            ],
            [],
            $this->logger
        );

        // data
        Process::exec(
            'mysqldump --defaults-extra-file={password_file} -v -h {host} -u {user} --quick --single-transaction --no-create-info {exclude_tables} {db} >> {temp_sql_file}',
            [
                '{password_file}' => $this->connection['password_file'],
                '{host}' => $this->connection['host'],
                '{user}' => $this->connection['user'],
                '{exclude_tables}' => $this->buildExcludeTablesArgument($database, $excludeTables),
                '{db}' => $database,
                '{temp_sql_file}' => $target,
            ],
            [],
            $this->logger
        );
    }

    /**
     * @return string
     */
    protected function getDriverName()
    {
        return 'mysql';
    }

    /**
     * @param string $database
     * @param array  $excludeTables
     *
     * @return string
     */
    private function buildExcludeTablesArgument($database, array $excludeTables)
    {
        $argument = array_map(function ($table) use ($database) {
            return '--ignore-table='.$database.'.'.$table;
        }, $excludeTables);

        return implode(' ', $argument);
    }
}
