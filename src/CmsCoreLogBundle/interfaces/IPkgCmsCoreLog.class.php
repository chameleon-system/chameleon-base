<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @deprecated since 6.3.0 - use Psr\Log\LoggerInterface instead
 */
interface IPkgCmsCoreLog
{
    public function __construct(Psr\Log\LoggerInterface $oLogger);

    /**
     * @return void
     */
    public function setLogger(Psr\Log\LoggerInterface $logger);

    /**
     * System is unusable.
     *
     * @param string $message
     * @param string $sFile
     * @param int $iLine
     *
     * @return void
     */
    public function emergency($message, $sFile, $iLine, array $context = []);

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param string $sFile
     * @param int $iLine
     *
     * @return void
     */
    public function alert($message, $sFile, $iLine, array $context = []);

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param string $sFile
     * @param int $iLine
     *
     * @return void
     */
    public function critical($message, $sFile, $iLine, array $context = []);

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param string $sFile
     * @param int $iLine
     *
     * @return void
     */
    public function error($message, $sFile, $iLine, array $context = []);

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param string $sFile
     * @param int $iLine
     *
     * @return void
     */
    public function warning($message, $sFile, $iLine, array $context = []);

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param string $sFile
     * @param int $iLine
     *
     * @return void
     */
    public function notice($message, $sFile, $iLine, array $context = []);

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param string $sFile
     * @param int $iLine
     *
     * @return void
     */
    public function info($message, $sFile, $iLine, array $context = []);

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param string $sFile
     * @param int $iLine
     *
     * @return void
     */
    public function debug($message, $sFile, $iLine, array $context = []);

    /**
     * Logs with an arbitrary level.
     *
     * @param string $message
     * @param string $sFile
     * @param int $iLine
     *
     * @return void
     */
    public function log($level, $message, $sFile, $iLine, array $context = []);
}
