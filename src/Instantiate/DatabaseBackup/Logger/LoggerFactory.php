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
 * @copyright  Copyright (c) 2016 Instantiate
 *
 * @link       http://www.instantiate.co.uk/
 *
 * @license    For the full copyright and license information, please view the
 *             LICENSE file that was distributed with this source code.
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
        $mailerTransport = Swift_SmtpTransport::newInstance(
            $config['mailer']['host'],
            $config['mailer']['port']
        );

        $mailer = Swift_Mailer::newInstance($mailerTransport);
        $baseMessage = Swift_Message::newInstance($config['mailer']['subject'], null, null, 'utf-8');
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
