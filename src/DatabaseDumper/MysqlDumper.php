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
