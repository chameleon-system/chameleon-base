<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use Psr\Log\LoggerInterface;

/**
 * @deprecated since 6.2.0 - this class contradicts the concept of exceptions as it is not only a passive data object
 *                           but handles the error itself by logging unconditionally. Avoid using it, but let code that
 *                           catches an exception be responsible for error handling.
 */
class TPkgCmsException_Log extends TPkgCmsException
{
    public const LOG_FILE = 'exceptions.log';

    /**
     * @var int|null
     */
    private $logLevel;

    /**
     * @var string|null
     */
    protected $logFilePath;

    /**
     * @param string $message - additional message string (shows up only in the log file)
     * @param array $aContextData - any data you want showing up in the log message to help you debug the exception
     * @param int $iLogLevel
     * @param string $sLogFilePath - path relative to ERROR_LOG_PATH to which the log entry should be added
     */
    public function __construct(
        $message = '',
        $aContextData = [], // any data you want showing up in the log message to help you debug the exception
        $iLogLevel = Monolog\Logger::ERROR,
        $sLogFilePath = self::LOG_FILE
    ) {
        parent::__construct($message, $aContextData);
        $this->logLevel = $iLogLevel;
        $this->logFilePath = $sLogFilePath;

        $this->writeMessageToLog();
    }

    /**
     * @return void
     */
    private function writeMessageToLog()
    {
        $level = $this->getLogLevel();
        switch ($level) {
            case 1:
                $level = Monolog\Logger::ERROR;
                break;
            case 2:
                $level = Monolog\Logger::WARNING;
                break;
            case 3:
                $level = Monolog\Logger::NOTICE;
                break;
            case 4:
                $level = Monolog\Logger::INFO;
                break;
            case 5:
                $level = Monolog\Logger::DEBUG;
                break;
        }

        $this->getExceptionLogger()->log($level, (string) $this, [$this->getContextData()]);
    }

    /**
     * @return IPkgCmsCoreLog
     *
     * @deprecated since 6.3.0 - use getExceptionLogger()
     */
    protected function getLogger()
    {
        return ServiceLocator::get('cmsPkgCore.logChannel.standard');
    }

    private function getExceptionLogger(): LoggerInterface
    {
        return ServiceLocator::get('logger');
    }

    /**
     * @return int|null
     */
    public function getLogLevel()
    {
        return $this->logLevel;
    }

    /**
     * @return string|null
     */
    public function getLogFilePath()
    {
        return $this->logFilePath;
    }

    public function __toString(): string
    {
        $sString = parent::__toString();

        $sString .= "\nLogLevel: ".$this->getLogLevel();

        return $sString;
    }
}
