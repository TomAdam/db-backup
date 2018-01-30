<?php
/*
 * This file is part of the DB Backup utility.
 *
 * (c) Tom Adam <tomadam@instantiate.co.uk>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Instantiate\DatabaseBackup\FileEncrypter;

use Psr\Log\LoggerInterface;

class FileEncrypterFactory
{
    /**
     * @param array           $config
     * @param LoggerInterface $logger
     *
     * @return FileEncrypterInterface
     *
     * @throws \Exception
     */
    public static function getEncrypter(array $config, LoggerInterface $logger)
    {
        switch ($config['type']) {
            case 'gpg':
                return new GpgFileEncrypter($config['config'], $logger);
                break;
            case 'cleartext':
                return new CleartextFileEncrypter($config['config'], $logger);
                break;
            default:
                throw new \Exception('Encryption type "'.$config['type'].'" invalid');
        }
    }
}
