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

abstract class AbstractFileEncrypter implements FileEncrypterInterface
{
    /**
     * @var array
     */
    protected $config;

    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param array           $config
     * @param LoggerInterface $logger
     */
    public function __construct(array $config, LoggerInterface $logger)
    {
        $this->config = $config;
        $this->logger = $logger;
    }

    /**
     * @param array $fileList
     *
     * @return array
     */
    public function encrypt(array $fileList)
    {
        $outputFiles = [];
        foreach ($fileList as $id => $file) {
            try {
                $outputFile = $this->getEncryptedFilename($file);
                $this->logger->notice('Encrypting '.$file.' to '.$outputFile);

                // todo: add filename to wipe list
                $this->encryptFile($file, $outputFile);
                $outputFiles[$id] = $outputFile;
            } catch (\Exception $e) {
                $this->logger->error('Exception while encrypting '.$file.': '.$e->getMessage());
            }
        }

        return $outputFiles;
    }

    abstract protected function getEncryptedFilename($inputFile);

    abstract protected function encryptFile($inputFile, $outputFile);
}
