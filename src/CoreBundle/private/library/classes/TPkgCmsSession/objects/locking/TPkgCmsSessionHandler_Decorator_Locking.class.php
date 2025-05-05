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

class TPkgCmsSessionHandler_Decorator_Locking extends Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy
{
    // **************************************************************************
    /**
     * @var IPkgCmsSessionStorageLock
     */
    private $oLockManager;
    private $bRequireWriteLock = false;

    /**
     * @var bool
     *           flag used to suppress session writing - required for the reloadSessionFromStorage method
     */
    private $bDisableSessionWrite = false;

    // ------------------------------------------------------------------------
    public function AddLockManager(IPkgCmsSessionStorageLock $oLockManager)
    {
        // ------------------------------------------------------------------------
        $this->oLockManager = $oLockManager;
    }

    // ------------------------------------------------------------------------

    // ------------------------------------------------------------------------
    public function SetRequireWriteLock($bRequireWriteLock)
    {
        // ------------------------------------------------------------------------
        $this->bRequireWriteLock = $bRequireWriteLock;
    }

    // ------------------------------------------------------------------------

    public function GetLock($sLockIdentifier)
    {
        return $this->oLockManager->GetLock($sLockIdentifier, 60);
    }

    public function sessionLockedByMe($sLockIdentifier)
    {
        return $this->oLockManager->sessionLockedByMe($sLockIdentifier);
    }

    /**
     * @param mixed $sSessionId
     *
     * @return mixed
     *
     * @throws TPkgCmsSessionStorageLockException
     */
    public function read(string $sSessionId): string|false
    {
        if (false == $this->bRequireWriteLock || $this->oLockManager->GetLock($sSessionId, 60)) {
            return parent::read($sSessionId);
        } else {
            // unable to obtain lock. Log error and redirect to maintenance page
            if (!defined('TESTSUITE')) {
                $logger = $this->getLogger();
                $logger->error('Unable to obtain session lock for id '.$sSessionId);
            }
            throw new TPkgCmsSessionStorageLockException('Unable to obtain session lock for id '.$sSessionId);
        }
    }

    public function write(string $sSessionId, string $aSessionData): bool
    {
        if (true === $this->bDisableSessionWrite) {
            return true;
        }
        // allow writing the session only if we are the lock owner
        if ($this->oLockManager->AllowWriteAccess($sSessionId)) {
            $rResult = parent::write($sSessionId, $aSessionData);
            $this->oLockManager->ReleaseLock($sSessionId);

            return $rResult;
        } else {
            if (!defined('TESTSUITE')) {
                $logger = $this->getLogger();
                $logger->warning('unable to write '.$sSessionId.' because the session was locked by another thread');
            }
        }

        return true;
    }

    /**
     * discards session content and reloads from storage.
     */
    public function closeWithoutWriting()
    {
        $this->bDisableSessionWrite = true;
        session_write_close();
        if (isset($_SESSION)) { // php 5.3 < does not reset $_SESSION (but symfony session manager requires that
            unset($_SESSION);
        }
        $this->bDisableSessionWrite = false;
    }

    private function getLogger(): LoggerInterface
    {
        return ServiceLocator::get('logger');
    }
}
