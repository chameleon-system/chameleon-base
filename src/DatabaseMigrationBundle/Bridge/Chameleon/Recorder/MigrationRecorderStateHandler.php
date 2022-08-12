<?php

namespace ChameleonSystem\DatabaseMigrationBundle\Bridge\Chameleon\Recorder;

use ChameleonSystem\DatabaseMigration\Constant\MigrationRecorderConstants;
use ChameleonSystem\DatabaseMigration\Exception\AccessDeniedException;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use TCMSUser;

class MigrationRecorderStateHandler
{
    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     */
    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    /**
     * @return bool
     */
    public function isDatabaseLoggingAllowed()
    {
        $currentUser = &TCMSUser::GetActiveUser();
        if (null === $currentUser) {
            return false;
        }
        if (null === $currentUser->oAccessManager) {
            return false;
        }
        if (false === $currentUser->oAccessManager->PermitFunction('dbchangelog-manager')) {
            return false;
        }

        return true;
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
     * @throws AccessDeniedException
     * @throws \LogicException
     *
     * @return void
     */
    public function toggleDatabaseLogging()
    {
        $this->setDatabaseLoggingActive(false === $this->isDatabaseLoggingActive());
    }

    /**
     * @param bool $isActive
     *
     * @throws AccessDeniedException
     * @throws \LogicException
     *
     * @return void
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
     * @throws \LogicException
     *
     * @return void
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
