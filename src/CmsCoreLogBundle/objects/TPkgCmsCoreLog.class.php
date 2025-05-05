<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\RequestType\RequestTypeInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * @deprecated since 6.3.0 - use Psr\Log\LoggerInterface in conjunction with Monolog logging instead
 */
class TPkgCmsCoreLog implements IPkgCmsCoreLog
{
    /**
     * @var Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var string|null
     */
    private $requestUID;

    /**
     * @var bool
     */
    private $logMetaData = true;

    public function __construct(Psr\Log\LoggerInterface $oLogger)
    {
        $this->setLogger($oLogger);
        $this->requestUID = md5(uniqid((string) rand()));
    }

    /**
     * {@inheritDoc}
     */
    public function setLogger(Psr\Log\LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * System is unusable.
     *
     * @param string $message
     * @param string $sFile
     * @param int $iLine
     *
     * @return void
     */
    public function emergency($message, $sFile, $iLine, array $context = [])
    {
        $context = $this->addMetaData($context, $sFile, $iLine);
        $this->logger->emergency($message, $context);
    }

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
    public function alert($message, $sFile, $iLine, array $context = [])
    {
        $context = $this->addMetaData($context, $sFile, $iLine);
        $this->logger->alert($message, $context);
    }

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
    public function critical($message, $sFile, $iLine, array $context = [])
    {
        $context = $this->addMetaData($context, $sFile, $iLine);
        $this->logger->critical($message, $context);
    }

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
    public function error($message, $sFile, $iLine, array $context = [])
    {
        $context = $this->addMetaData($context, $sFile, $iLine);
        $this->logger->error($message, $context);
    }

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
    public function warning($message, $sFile, $iLine, array $context = [])
    {
        $context = $this->addMetaData($context, $sFile, $iLine);
        $this->logger->warning($message, $context);
    }

    /**
     * Normal but significant events.
     *
     * @param string $message
     * @param string $sFile
     * @param int $iLine
     *
     * @return void
     */
    public function notice($message, $sFile, $iLine, array $context = [])
    {
        $context = $this->addMetaData($context, $sFile, $iLine);
        $this->logger->notice($message, $context);
    }

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
    public function info($message, $sFile, $iLine, array $context = [])
    {
        $context = $this->addMetaData($context, $sFile, $iLine);
        $this->logger->info($message, $context);
    }

    /**
     * Detailed debug information.
     *
     * @param string $message
     * @param string $sFile
     * @param int $iLine
     *
     * @return void
     */
    public function debug($message, $sFile, $iLine, array $context = [])
    {
        $context = $this->addMetaData($context, $sFile, $iLine);
        $this->logger->debug($message, $context);
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param mixed $level - defined via Psr\Log\LogLevel
     * @param string $message
     * @param string $sFile
     * @param int $iLine
     *
     * @return void
     */
    public function log($level, $message, $sFile, $iLine, array $context = [])
    {
        $context = $this->addMetaData($context, $sFile, $iLine);
        $this->logger->log($level, $message, $context);
    }

    /**
     * @param bool $logMetaData
     *
     * @return void
     */
    public function setLogMetaData($logMetaData)
    {
        $this->logMetaData = $logMetaData;
    }

    /**
     * @return string|null
     */
    protected function getRequestUID()
    {
        return $this->requestUID;
    }

    /**
     * @param array $context
     * @param string $sFile
     * @param int $iLine
     *
     * @return array
     */
    protected function addMetaData($context, $sFile, $iLine)
    {
        if (false === $this->logMetaData) {
            return $context;
        }
        /** @var Request $request */
        $request = ChameleonSystem\CoreBundle\ServiceLocator::get('request_stack')->getCurrentRequest();
        if (null === $request) {
            return $context;
        }
        $sSessionId = 'not initiated yet';
        if (true === $request->hasSession()) {
            $sSessionId = $request->getSession()->getId();
        }

        $aRequestDetails = [
            'session' => $sSessionId,
            'uid' => $this->requestUID,
            'file' => $sFile,
            'line' => $iLine,
            'ip' => '',
            'data_extranet_user_id' => '',
            'data_extranet_user_name' => '',
            'cms_user_id' => '',
            'requestURL' => '',
            'referrerURL' => '',
            'httpMethod' => $request->getMethod(),
            'server' => $request->server->get('SERVER_NAME', ''),
        ];

        $aRequestDetails['ip'] = $request->getClientIp() ?? '';

        $aRequestDetails['referrerURL'] = $request->server->get('HTTP_REFERER', '');

        if ($this->isFrontendRequest()) {
            $frontendUserDetails = $this->getFrontendUserDetails();
            $aRequestDetails = array_merge($aRequestDetails, $frontendUserDetails);
        }

        $aRequestDetails['requestURL'] = $request->getPathInfo();

        $context['_cmsRequestDetails'] = $aRequestDetails;

        return $context;
    }

    /**
     * @return bool
     */
    private function isFrontendRequest()
    {
        // the service should really be injected into the constructor. Unfortunately there are many uses of the class, making that
        // change in a patch level unacceptable.
        $requestInfoService = ChameleonSystem\CoreBundle\ServiceLocator::get(
            'chameleon_system_core.request_info_service'
        );

        return $requestInfoService->isChameleonRequestType(RequestTypeInterface::REQUEST_TYPE_FRONTEND);
    }

    /**
     * @return array
     */
    private function getFrontendUserDetails()
    {
        $oUser = $this->getActiveFrontendUser();
        if (null === $oUser) {
            return [];
        }

        return [
            'data_extranet_user_name' => $oUser->fieldName ?? '',
            'data_extranet_user_id' => $oUser->id ?? '',
        ];
    }

    /**
     * @return TdbDataExtranetUser
     */
    private function getActiveFrontendUser()
    {
        $userProvider = ChameleonSystem\CoreBundle\ServiceLocator::get(
            'chameleon_system_extranet.extranet_user_provider'
        );

        return $userProvider->getActiveUser();
    }
}
