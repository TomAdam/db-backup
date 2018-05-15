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

class PostgresDumper extends AbstractDatabaseDumper
{
    /**
     * @param string $database
     * @param array  $excludeTables
     * @param string $target
     */
    protected function dumpDatabase($database, array $excludeTables, $target)
    {
        // structure + tables
        Process::exec(
            'pg_dump -v -w -h {host} -U {user} -d {db} {exclude_tables} > {temp_sql_file}',
            [
                '{host}' => $this->connection['host'],
                '{user}' => $this->connection['user'],
                '{db}' => $database,
                '{exclude_tables}' => $this->buildExcludeTablesArgument($excludeTables),
                '{temp_sql_file}' => $target,
            ],
            [
                'PGPASSWORD' => $this->connection['password'],
            ],
            $this->logger
        );
    }

    /**
     * @return string
     */
    protected function getDriverName()
    {
        return 'postgres';
    }

    /**
     * @param array $excludeTables
     *
     * @return string
     */
    private function buildExcludeTablesArgument(array $excludeTables)
    {
        $argument = array_map(function ($table) {
            return '--exclude-table-data='.$table;
        }, $excludeTables);

        return implode(' ', $argument);
    }
}
