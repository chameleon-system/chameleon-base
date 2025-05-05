<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPKgCmsSession extends Symfony\Component\HttpFoundation\Session\Session
{
    private $sessionLockingEnabled = false;

    public function start(): bool
    {
        $result = parent::start();
        $this->postStartHook($this->all());

        return $result;
    }

    protected function postStartHook($sessionArray)
    {
        $sessionWakeUp = new TPkgCmsSessionWakeUpService();
        $sessionWakeUp->wakeUpSessionData($sessionArray);
    }

    /**
     * acquire write lock (will reload session from storage!).
     */
    public function restartSessionWithWriteLock(): bool
    {
        if (false === $this->sessionLockingEnabled) {
            return true;
        }

        $bOpenForWriting = false;
        if (method_exists($this->storage->getSaveHandler(), 'SetRequireWriteLock')) {
            ignore_user_abort(true); // a call with a lock should not be aborted by a browser request
            $this->storage->getSaveHandler()->SetRequireWriteLock(true);
            if (true === $this->storage->getSaveHandler()->sessionLockedByMe(session_id())) {
                return true;
            }
            $bOpenForWriting = $this->storage->getSaveHandler()->GetLock(session_id());
            if ($bOpenForWriting) {
                $this->reloadSession();
            }
        }

        return $bOpenForWriting;
    }

    /**
     * discards content of session and reloads from storage without changing the session id.
     */
    public function reloadSession(): void
    {
        if (false === $this->sessionLockingEnabled) {
            return;
        }
        // reopen session to make sure we are working the newest data. NOTICE: this will discard the current session data!
        $storageCopy = $this->storage;

        /** @var TPkgCmsSession_NativeSessionStorage $storageCopy */
        if (true === $storageCopy->closeWithoutWriting()) {
            $this->start();
        }
    }

    /**
     * @param bool $sessionLockingEnabled
     */
    public function setSessionLockingEnabled($sessionLockingEnabled): void
    {
        $this->sessionLockingEnabled = $sessionLockingEnabled;
    }
}
