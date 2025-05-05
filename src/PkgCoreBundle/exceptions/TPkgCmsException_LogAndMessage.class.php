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
 * @deprecated since 6.2.0 - this class contradicts the concept of exceptions as it is not only a passive data object
 *                           but handles the error itself by logging unconditionally. Avoid using it, but let code that
 *                           catches an exception be responsible for error handling.
 */
class TPkgCmsException_LogAndMessage extends TPkgCmsException_Log
{
    /**
     * @var string|null
     */
    private $messageCode;

    /**
     * @var array|null
     */
    private $additionalData;

    /**
     * @param string $sMessageCode - message code in chameleon
     * @param array $aAdditionalData - this is available in for the message generated via the smessageCode
     * @param string $message - additional message string (shows up only in the log file)
     * @param array $aContextData - any data you want showing up in the log message to help you debug the exception
     * @param int $iLogLevel
     * @param string $sLogFilePath - path relative to cmsdata to which the log entry should be added
     */
    public function __construct(
        $sMessageCode,
        $aAdditionalData = [],
        $message = '',
        $aContextData = [], // any data you want showing up in the log message to help you debug the exception
        $iLogLevel = 1,
        $sLogFilePath = self::LOG_FILE
    ) {
        $this->messageCode = $sMessageCode;
        $this->additionalData = $aAdditionalData;
        parent::__construct($message, $aContextData, $iLogLevel, $sLogFilePath);
    }

    public function __toString(): string
    {
        $sString = parent::__toString();

        $sString .= "\nMessageCode: ".$this->getMessageCode();
        $sString .= "\nAddionalData:\n".print_r($this->getAdditionalData(), true);

        return $sString;
    }

    /**
     * @return array|null
     */
    public function getAdditionalData()
    {
        return $this->additionalData;
    }

    /**
     * @return string|null
     */
    public function getMessageCode()
    {
        return $this->messageCode;
    }
}
