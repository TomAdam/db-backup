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
 * @copyright  Copyright (c) 2016 Instantiate
 *
 * @link       http://www.instantiate.co.uk/
 *
 * @license    For the full copyright and license information, please view the
 *             LICENSE file that was distributed with this source code.
 */

namespace Instantiate\DatabaseBackup\Console\Command;

use Instantiate\DatabaseBackup\DatabaseDumper\DatabaseDumperFactory;
use Instantiate\DatabaseBackup\FileEncrypter\FileEncrypterFactory;
use Instantiate\DatabaseBackup\Logger\LoggerFactory;
use Instantiate\DatabaseBackup\StorageAdapter\S3StorageAdapter;
use Instantiate\DatabaseBackup\Util\ConfigLoader;
use Instantiate\DatabaseBackup\Util\FileWiper;
use Monolog\ErrorHandler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Stopwatch\Stopwatch;

class DbBackupCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('db-backup')
            ->setDescription('Backup databases')
            ->addOption('config-file', 'c', InputOption::VALUE_REQUIRED, 'Config to load', null);
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $stopwatch = new Stopwatch();
        $stopwatch->start('dbBackup');
        $dumpedFiles = [];
        $encryptedFiles = [];

        $config = ConfigLoader::loadConfig($input->getOption('config-file') ?: 'config.yml');
        $logger = LoggerFactory::getLogger($config, $output);
        ErrorHandler::register($logger);

        try {
            // dump
            foreach ($config['connections'] as $connection) {
                $dumper = DatabaseDumperFactory::getDumper($connection, $logger);
                $dumpedFiles = array_merge($dumpedFiles, $dumper->dump($connection['databases']));
            }

            // encrypt
            $encrypter = FileEncrypterFactory::getEncrypter($config['encryption'], $logger);
            $encryptedFiles = $encrypter->encrypt($dumpedFiles);

            // store
            // todo: add factory?
            $store = new S3StorageAdapter($config['s3'], $logger);
            $store->store($encryptedFiles);

            // rotate
            //$rotator = new BackupRotator($config['rotation']);
            //$rotator->rotate($store);
        } catch (\Exception $e) {
            $logger->error('Unhandled exception: '.$e->getMessage());
        }

        // cleanup
        FileWiper::wipe($dumpedFiles, $logger);
        FileWiper::wipe($encryptedFiles, $logger);

        $logger->notice('Completed backup in '.(string) $stopwatch->stop('dbBackup'));

        // todo: return non-zero if any errors
        return 0;
    }
}
