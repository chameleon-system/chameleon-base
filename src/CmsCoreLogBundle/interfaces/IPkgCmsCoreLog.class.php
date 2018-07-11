<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

interface IPkgCmsCoreLog
{
    public function __construct(Psr\Log\LoggerInterface $oLogger);

    public function setLogger(Psr\Log\LoggerInterface $logger);

    /**
     * System is unusable.
     *
     * @param string $message
     * @param $sFile
     * @param $iLine
     * @param array $context
     */
    public function emergency($message, $sFile, $iLine, array $context = array());

    /**
     * Action must be taken immediately.
     *
     * Example: Entire website down, database unavailable, etc. This should
     * trigger the SMS alerts and wake you up.
     *
     * @param string $message
     * @param $sFile
     * @param $iLine
     * @param array $context
     */
    public function alert($message, $sFile, $iLine, array $context = array());

    /**
     * Critical conditions.
     *
     * Example: Application component unavailable, unexpected exception.
     *
     * @param string $message
     * @param $sFile
     * @param $iLine
     * @param array $context
     */
    public function critical($message, $sFile, $iLine, array $context = array());

    /**
     * Runtime errors that do not require immediate action but should typically
     * be logged and monitored.
     *
     * @param string $message
     * @param $sFile
     * @param $iLine
     * @param array $context
     */
    public function error($message, $sFile, $iLine, array $context = array());

    /**
     * Exceptional occurrences that are not errors.
     *
     * Example: Use of deprecated APIs, poor use of an API, undesirable things
     * that are not necessarily wrong.
     *
     * @param string $message
     * @param $sFile
     * @param $iLine
     * @param array $context
     */
    public function warning($message, $sFile, $iLine, array $context = array());

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param $sFile
     * @param $iLine
     * @param array $context
     */
    public function notice($message, $sFile, $iLine, array $context = array());

    /**
     * Interesting events.
     *
     * Example: User logs in, SQL logs.
     *
     * @param string $message
     * @param $sFile
     * @param $iLine
     * @param array $context
     */
    public function info($message, $sFile, $iLine, array $context = array());

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param $sFile
     * @param $iLine
     * @param array $context
     */
    public function debug($message, $sFile, $iLine, array $context = array());

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed  $level
     * @param string $message
     * @param $sFile
     * @param $iLine
     * @param array $context
     */
    public function log($level, $message, $sFile, $iLine, array $context = array());
}
