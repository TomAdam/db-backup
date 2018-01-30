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
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process as BaseProcess;

class Process
{
    /**
     * @param string          $command
     * @param array           $arguments
     * @param array           $env
     * @param LoggerInterface $logger
     *
     * @return Process
     */
    public static function exec($command, array $arguments, array $env = [], LoggerInterface $logger = null)
    {
        $process = new BaseProcess(strtr($command, $arguments));
        if ($logger) {
            $logger->debug('Executing '.$process->getCommandLine());
        }

        try {
            $process
                ->setEnv($env)
                ->setTimeout(null)
                ->mustRun();
        } catch (ProcessFailedException $exception) {
            if ($logger) {
                $logger->error($exception->getMessage());
            }
            throw $exception;
        }

        return $process;
    }
}
