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

use Instantiate\DatabaseBackup\Util\Process;

class GpgFileEncrypter extends AbstractFileEncrypter
{
    /**
     * @param string $inputFile
     * @param string $outputFile
     */
    protected function encryptFile($inputFile, $outputFile)
    {
        Process::exec(
            'gpg2 -v --encrypt {sign} --recipient {recipient} --batch --yes {passphrase} --output {output_file} {input_file}',
            [
                '{sign}' => $this->config['sign'] ? '--sign' : '',
                '{recipient}' => $this->config['recipient'],
                '{passphrase}' => $this->config['sign'] && $this->config['key_file']
                    ? '--passphrase-file '.$this->config['key_file']
                    : '',
                '{input_file}' => $inputFile,
                '{output_file}' => $outputFile,
            ],
            [],
            $this->logger
        );
    }

    protected function getEncryptedFilename($inputFile)
    {
        return $inputFile.'.gpg';
    }
}
