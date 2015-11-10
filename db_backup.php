#!/usr/bin/env php
<?php

use Instantiate\DatabaseBackup\BackupRotator\BackupRotator;
use Instantiate\DatabaseBackup\FileEncrypter\CleartextFileEncrypter;
use Instantiate\DatabaseBackup\StorageAdapter\S3StorageAdapter;
use Instantiate\DatabaseBackup\Util\ConfigLoader;
use Instantiate\DatabaseBackup\DatabaseDumper\MysqlDumper;
use Instantiate\DatabaseBackup\DatabaseDumper\PostgresDumper;
use Instantiate\DatabaseBackup\FileEncrypter\GpgFileEncrypter;
use Instantiate\DatabaseBackup\Util\FileWiper;

require('vendor/autoload.php');

$config_file = isset($argv[1]) ? $argv[1] : __DIR__.'/config.yml';
$config = ConfigLoader::loadConfig($config_file);

$dumpedFiles = [];
foreach ($config['connections'] as $id => $connection) {
    // dump
    switch ($connection['driver']) {
        case 'mysql':
            $dumper = new MysqlDumper($connection['host'], $connection['user'], $connection['password_file']);
            break;
        case 'postgres':
            $dumper = new PostgresDumper($connection['host'], $connection['user'], $connection['password_file']);
            break;
        default:
            throw new Exception('Driver type "'.$connection['driver'].'"" for connection '.$id.' invalid');
    }
    $dumpedFiles = array_merge($dumpedFiles, $dumper->dump($connection['databases']));
}

// encrypt
switch ($config['encryption']['type']) {
    case 'gpg':
        $encrypter = new GpgFileEncrypter($config['encryption']['config']);
        break;
    case 'cleartext':
        $encrypter = new CleartextFileEncrypter();
        break;
    default:
        throw new Exception('Encryption type "'.$config['encryption']['type'].'" invalid');
}
$encryptedFiles = $encrypter->encrypt($dumpedFiles);

// store
$store = new S3StorageAdapter($config['s3']);
$store->store($encryptedFiles);

// cleanup
FileWiper::wipe($dumpedFiles);
FileWiper::wipe($encryptedFiles);

// rotate
//$rotator = new BackupRotator($config['rotation']);
//$rotator->rotate($store);

echo "Completed\n";
