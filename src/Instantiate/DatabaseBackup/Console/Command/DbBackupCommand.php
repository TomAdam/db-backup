<?php
/*
 * This file is part of the DB Backup utility.
 *
 * (c) Tom Adam <tomadam@instantiate.co.uk>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Instantiate\DatabaseBackup\Console\Command;

use Instantiate\DatabaseBackup\DatabaseDumper\DatabaseDumperFactory;
use Instantiate\DatabaseBackup\FileEncrypter\FileEncrypterFactory;
use Instantiate\DatabaseBackup\Logger\LoggerFactory;
use Instantiate\DatabaseBackup\StorageAdapter\StorageAdapterFactory;
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
            $store = StorageAdapterFactory::getStorageAdapter($config['storage'], $logger);
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
