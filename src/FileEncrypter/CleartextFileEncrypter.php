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

class CleartextFileEncrypter extends AbstractFileEncrypter
{
    /**
     * @param string $inputFile
     * @param string $outputFile
     */
    protected function encryptFile($inputFile, $outputFile)
    {
    }

    /**
     * @param string $inputFile
     *
     * @return string
     */
    protected function getEncryptedFilename($inputFile)
    {
        // TODO: don't register files for deletion as same filename as input
        return $inputFile;
    }
}
