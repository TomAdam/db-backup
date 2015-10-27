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
namespace Instantiate\DatabaseBackup\FileEncrypter;

abstract class AbstractFileEncrypter
{
    /**
     * @param array $fileList
     * @return array
     */
    public function encrypt(array $fileList)
    {
        $outputFiles = [];
        foreach ($fileList as $id => $file) {
            $outputFile = $this->getEncryptedFilename($file);
            echo 'Encrypting '.$file.' to '.$outputFile.'...';
            $this->encryptFile($file, $outputFile);
            echo " done\n";
            $outputFiles[$id] = $outputFile;
        }

        return $outputFiles;
    }

    abstract protected function getEncryptedFilename($inputFile);

    abstract protected function encryptFile($inputFile, $outputFile);
}
