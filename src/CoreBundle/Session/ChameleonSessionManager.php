<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Session;

use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use Doctrine\DBAL\Connection;
use Psr\Log\LoggerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\MemcachedSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\NullSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\PdoSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\Handler\WriteCheckSessionHandler;
use Symfony\Component\HttpFoundation\Session\Storage\MetadataBag;

class ChameleonSessionManager implements ChameleonSessionManagerInterface
{
    private const SESSION_BASE_NAME = 'PHPSESSID';

    private RequestStack $requestStack;
    private Connection $databaseConnection;
    private \PDO $sessionPdo;
    private ContainerInterface $container;
    private int $metaDataTimeout;
    private array $sessionOptions = [];
    private bool $isSessionStarting = false;
    private PortalDomainServiceInterface $portalDomainService;
    private RequestInfoServiceInterface $requestInfoService;
    private LoggerInterface $logger;

    /**
     * @param int $metaDataTimeout
     * @param array<string, mixed> $sessionOptions
     */
    public function __construct(RequestStack $requestStack, Connection $databaseConnection, \PDO $sessionPdo, ContainerInterface $container, $metaDataTimeout, array $sessionOptions, PortalDomainServiceInterface $portalDomainService, RequestInfoServiceInterface $requestInfoService, LoggerInterface $logger)
    {
        $this->requestStack = $requestStack;
        $this->databaseConnection = $databaseConnection;
        $this->sessionPdo = $sessionPdo;
        $this->container = $container;
        $this->metaDataTimeout = $metaDataTimeout;
        $this->sessionOptions = $sessionOptions;
        $this->portalDomainService = $portalDomainService;
        $this->requestInfoService = $requestInfoService;
        $this->logger = $logger;
    }

    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        $sessionHandler = null;
        if (true === \TdbCmsConfig::RequestIsInBotList()) {
            $sessionHandler = new NullSessionHandler();
        } else {
            $memcacheSessionsServer = \ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_core.cache.memcache_sessions_server1');
            if ($memcacheSessionsServer) {
                $memcache = \TCMSMemcache::GetSessionInstance();
                $sessionHandler = new MemcachedSessionHandler($memcache->getDriver());
            } else {
                $options = [
                    'db_table' => '`_cms_sessions`',
                    'db_id_col' => '`key`',
                    'db_data_col' => '`data`',
                    'db_time_col' => '`time`',
                    'lock_mode' => PdoSessionHandler::LOCK_NONE,
                ];
                $this->sessionPdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
                $sessionHandler = new PdoSessionHandler($this->sessionPdo, $options);
            }
        }

        // we use our own native session handler to provide methods required by TPKgCmsSession::restartSessionWithWriteLock and TPKgCmsSession::reloadSession
        $options = $this->sessionOptions;
        if (true === \TGlobal::IsCMSMode()) {
            $options['cookie_lifetime'] = 0;
        }
        if ($options['cookie_lifetime'] > $options['gc_maxlifetime']) {
            $options['gc_maxlifetime'] = $options['cookie_lifetime'];
        }

        $options['cookie_httponly'] = 1;

        // we set the threshold of the meta data bag to 20 seconds so that the decoration WriteCheckSessionHandler can prevent writing unchanged session data within a 20 second time frame
        $metaDataBag = new MetadataBag('_sf2_meta', $this->metaDataTimeout);
        $sessionStorage = new \TPkgCmsSession_NativeSessionStorage($options, $sessionHandler, $metaDataBag);

        // add decorator to enforce write order (old data may not overwrite new data)
        $sessionHandler = new \TPkgCmsSessionHandler_Decorator_EnforceWriteSequence($sessionStorage->getSaveHandler());

        // add session locking support if enabled
        if (false === DISABLE_SESSION_LOCKING) {
            $oLockManager = new \TPkgCmsSessionStorageLock_ViaDatabase();
            $oLockManager->setDatabaseConnection($this->databaseConnection);
            // turns out: the locking decorator must be tha last decorator in the chain, as it introduces a new method that is not defined in \SessionHandlerInterface
            // if you keep decorating after that, you will remove the added definition.
            $sessionHandler = new \TPkgCmsSessionHandler_Decorator_Locking($sessionHandler);
            $sessionHandler->AddLockManager($oLockManager);
        }

        $sessionStorage->setSaveHandler($sessionHandler);

        $session = new \TPKgCmsSession($sessionStorage);

        $cookieName = $this->getCookieName();
        $session->setName($cookieName);

        if (false === DISABLE_SESSION_LOCKING) {
            $session->setSessionLockingEnabled(true);
        }

        $request = $this->getRequest();
        $request->setSession($session);

        $this->isSessionStarting = true;
        $request->getSession()->start();
        $this->isSessionStarting = false;
        $this->registerSessionInContainer($request->getSession());
    }

    /**
     * @return Request|null
     */
    protected function getRequest()
    {
        return $this->requestStack->getCurrentRequest();
    }

    private function registerSessionInContainer(SessionInterface $session): void
    {
        $this->container->set('session', $session);
    }

    /**
     * @return bool
     */
    public function isSessionStarting()
    {
        return $this->isSessionStarting;
    }

    private function getCookieName(): string
    {
        if ($this->requestInfoService->isBackendMode()) {
            return self::SESSION_BASE_NAME.'CMS';
        }

        if ($this->requestInfoService->isCmsTemplateEngineEditMode()) {
            $this->logger->notice('Using backend session because we are in template engine edit mode');

            return self::SESSION_BASE_NAME.'CMS';
        }

        if ($this->requestInfoService->isPreviewMode()) {
            $this->logger->notice('Using backend session because we are in preview mode');

            return self::SESSION_BASE_NAME.'CMS';
        }

        if (!CHAMELEON_EXTRANET_USER_IS_PORTAL_DEPENDANT) {
            return self::SESSION_BASE_NAME;
        }

        $portal = $this->portalDomainService->getActivePortal();
        if (null === $portal) {
            return self::SESSION_BASE_NAME;
        }

        return self::SESSION_BASE_NAME.$portal->sqlData['cmsident'] ?? '';
    }
}
