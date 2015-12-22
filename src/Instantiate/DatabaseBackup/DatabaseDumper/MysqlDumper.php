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

class MysqlDumper extends AbstractDatabaseDumper
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
    private $settingsFile;

    /**
     * @param string $host
     * @param string $user
     * @param string $settingsFile
     */
    public function __construct($host, $user, $settingsFile)
    {
        $this->host = $host;
        $this->user = $user;
        $this->settingsFile = $settingsFile;
    }

    /**
     * @param string $database
     * @param array $excludeTables
     * @param string $target
     */
    protected function dumpDatabase($database, array $excludeTables, $target)
    {
        // structure
        Command::exec(
            'mysqldump --defaults-extra-file={settings_file} -v -h {host} -u {user} --quick --single-transaction --no-data {db} > {temp_sql_file}',
            [
                '{settings_file}' => $this->settingsFile,
                '{host}' => $this->host,
                '{user}' => $this->user,
                '{db}' => $database,
                '{temp_sql_file}' => $target,
            ]
        );

        // data
        Command::exec(
            'mysqldump --defaults-extra-file={settings_file} -v -h {host} -u {user} --quick --single-transaction --no-create-info {exclude_tables} {db} >> {temp_sql_file}',
            [
                '{settings_file}' => $this->settingsFile,
                '{host}' => $this->host,
                '{user}' => $this->user,
                '{exclude_tables}' => $this->buildExcludeTablesArgument($database, $excludeTables),
                '{db}' => $database,
                '{temp_sql_file}' => $target,
            ]
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
     * @param array $excludeTables
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
