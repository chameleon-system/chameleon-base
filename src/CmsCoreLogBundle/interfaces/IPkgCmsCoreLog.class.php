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
     * @param Psr\Log\LoggerInterface $logger
     *
     * @return void
     */
    public function setLogger(Psr\Log\LoggerInterface $logger);

    /**
     * System is unusable.
     *
     * @param string $message
     * @param string $sFile
     * @param int $iLine
     * @param array $context
     *
     * @return void
     */
    public function emergency($message, $sFile, $iLine, array $context = array());

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param string $sFile
     * @param int $iLine
     * @param array $context
     *
     * @return void
     */
    public function alert($message, $sFile, $iLine, array $context = array());

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param string $sFile
     * @param int $iLine
     * @param array $context
     *
     * @return void
     */
    public function critical($message, $sFile, $iLine, array $context = array());

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param string $sFile
     * @param int $iLine
     * @param array $context
     *
     * @return void
     */
    public function error($message, $sFile, $iLine, array $context = array());

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param string $sFile
     * @param int $iLine
     * @param array $context
     *
     * @return void
     */
    public function warning($message, $sFile, $iLine, array $context = array());

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param string $sFile
     * @param int $iLine
     * @param array $context
     *
     * @return void
     */
    public function notice($message, $sFile, $iLine, array $context = array());

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param string $sFile
     * @param int $iLine
     * @param array $context
     *
     * @return void
     */
    public function info($message, $sFile, $iLine, array $context = array());

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param string $sFile
     * @param int $iLine
     * @param array $context
     *
     * @return void
     */
    public function debug($message, $sFile, $iLine, array $context = array());

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param string $sFile
     * @param int $iLine
     * @param array $context
     *
     * @return void
     */
    public function log($level, $message, $sFile, $iLine, array $context = array());
}
