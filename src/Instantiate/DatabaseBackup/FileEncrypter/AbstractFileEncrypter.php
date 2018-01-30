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
 * @see       http://www.instantiate.co.uk/
 *
 * @license    For the full copyright and license information, please view the
 *             LICENSE file that was distributed with this source code.
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
