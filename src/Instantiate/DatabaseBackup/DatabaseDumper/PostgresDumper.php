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

use Instantiate\DatabaseBackup\Util\Command;

class PostgresDumper extends AbstractDatabaseDumper
{
    /**
     * @var string
     */
    private $host;
    /**
     * @var string
     */
    private $user;
    /**
     * @var string
     */
    private $pgpassFile;

    /**
     * @param string $host
     * @param string $user
     * @param string $pgpassFile
     */
    public function __construct($host, $user, $pgpassFile)
    {
        $this->host = $host;
        $this->user = $user;
        $this->pgpassFile = $pgpassFile;
    }

    /**
     * @param string $database
     * @param array $excludeTables
     * @param string $target
     */
    protected function dumpDatabase($database, array $excludeTables, $target)
    {
        // structure + tables
        Command::exec(
            'pg_dump -v -w -h {host} -U {user} -d {db} {exclude_tables} > {temp_sql_file}',
            [
                '{host}' => $this->host,
                '{user}' => $this->user,
                '{db}' => $database,
                '{exclude_tables}' => $this->buildExcludeTablesArgument($excludeTables),
                '{temp_sql_file}' => $target,
            ],
            [
                'PGPASSFILE' => $this->pgpassFile
            ]
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
