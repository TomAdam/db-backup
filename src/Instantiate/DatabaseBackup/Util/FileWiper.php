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
