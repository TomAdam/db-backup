<?php
/*
 * This file is part of the DB Backup utility.
 *
 * (c) Tom Adam <tomadam@instantiate.co.uk>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Instantiate\DatabaseBackup\Logger;

use Monolog\Handler\BufferHandler;
use Monolog\Handler\FingersCrossed\ErrorLevelActivationStrategy;
use Monolog\Handler\FingersCrossedHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Handler\SwiftMailerHandler;
use Monolog\Logger;
use Swift_Mailer;
use Swift_Message;
use Swift_SmtpTransport;
use Symfony\Bridge\Monolog\Handler\ConsoleHandler;
use Symfony\Component\Console\Output\OutputInterface;

class LoggerFactory
{
    /**
     * @param array           $config
     * @param OutputInterface $output
     *
     * @return Logger
     */
    public static function getLogger(array $config, OutputInterface $output)
    {
        $logger = new Logger('app');
        $logger->pushHandler(new ConsoleHandler($output));

        if (isset($config['mailer'])) {
            $logger->pushHandler(self::getEmailHandler($config));
        }

        if (isset($config['log'])) {
            $logger->pushHandler(self::getLogHandler($config));
        }

        return $logger;
    }

    /**
     * @param array $config
     *
     * @return FingersCrossedHandler
     */
    private static function getEmailHandler(array $config)
    {
        $mailerTransport = new Swift_SmtpTransport(
            $config['mailer']['host'],
            $config['mailer']['port']
        );

        $mailer = new Swift_Mailer($mailerTransport);
        $baseMessage = new Swift_Message($config['mailer']['subject'], null, null, 'utf-8');
        $baseMessage
            ->setFrom($config['mailer']['from'])
            ->setTo($config['mailer']['recipient']);

        $emailHandler = new SwiftMailerHandler($mailer, $baseMessage);
        $bufferHandler = new BufferHandler($emailHandler);
        $activationStrategy = new ErrorLevelActivationStrategy(Logger::ERROR);

        return new FingersCrossedHandler($bufferHandler, $activationStrategy);
    }

    /**
     * @param array $config
     *
     * @return StreamHandler
     */
    private static function getLogHandler(array $config)
    {
        return new StreamHandler($config['log']['file'], Logger::DEBUG);
    }
}
