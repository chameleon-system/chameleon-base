<?php

namespace ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\Recorder;

use ChameleonSystem\DatabaseMigration\Constant\MigrationRecorderConstants;
use ChameleonSystem\DatabaseMigration\Exception\AccessDeniedException;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class MigrationRecorderStateHandler
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    public function __construct(RequestStack $requestStack, readonly private Security $security)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return bool
     */
    public function isDatabaseLoggingAllowed()
    {
        return $this->security->isGranted('CMS_RIGHT_DBCHANGELOG-MANAGER');
    }

    /**
     * @return bool
     */
    public function isDatabaseLoggingActive()
    {
        $session = $this->getSession();
        if (null === $session) {
            return false;
        }

        return true === $session->get(MigrationRecorderConstants::SESSION_PARAM_MIGRATION_RECORDING_ACTIVE);
    }

    /**
     * @return void
     *
     * @throws AccessDeniedException
     * @throws \LogicException
     */
    public function toggleDatabaseLogging()
    {
        $this->setDatabaseLoggingActive(false === $this->isDatabaseLoggingActive());
    }

    /**
     * @param bool $isActive
     *
     * @return void
     *
     * @throws AccessDeniedException
     * @throws \LogicException
     */
    private function setDatabaseLoggingActive($isActive)
    {
        $session = $this->getSession();
        if (null === $session) {
            throw new \LogicException('Database logging requested but not allowed.');
        }
        if (false === $this->isDatabaseLoggingAllowed()) {
            throw new AccessDeniedException('Database logging requested but not allowed.');
        }

        $session->set(MigrationRecorderConstants::SESSION_PARAM_MIGRATION_RECORDING_ACTIVE, $isActive);
    }

    /**
     * @return string|null
     */
    public function getCurrentBuildNumber()
    {
        $session = $this->getSession();
        if (null === $session) {
            return null;
        }

        return $session->get(MigrationRecorderConstants::SESSION_PARAM_MIGRATION_BUILD_NUMBER);
    }

    /**
     * @param string $buildNumber
     *
     * @return void
     *
     * @throws \LogicException
     */
    public function setCurrentBuildNumber($buildNumber)
    {
        $session = $this->getSession();
        if (null === $session) {
            throw new \LogicException('No session available, but expected.');
        }
        $session->set(MigrationRecorderConstants::SESSION_PARAM_MIGRATION_BUILD_NUMBER, $buildNumber);
    }

    /**
     * @return SessionInterface|null
     */
    private function getSession()
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return null;
        }
        if (false === $request->hasSession()) {
            return null;
        }
        $session = $request->getSession();
        if (false === $session->isStarted()) {
            return null;
        }

        return $session;
    }
}
