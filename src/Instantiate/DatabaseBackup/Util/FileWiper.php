<?php
/*
 * This file is part of the DB Backup utility.
 *
 * (c) Tom Adam <tomadam@instantiate.co.uk>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Instantiate\DatabaseBackup\Util;

use Psr\Log\LoggerInterface;

class FileWiper
{
    /**
     * @param array           $fileList
     * @param LoggerInterface $logger
     */
    public static function wipe(array $fileList, LoggerInterface $logger)
    {
        foreach ($fileList as $file) {
            $logger->notice('Deleting temporary file '.$file);

            // emits E_WARNING on failure that is picked up by monolog error handler
            unlink($file);
        }
    }
}
