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
 * ensures that an older session is not able to overwrite a newer session. Example:
 * - request (a) reads session at time 1 and request (b) read session at time 2
 * - request (b) finishes and updates session
 * - request (a) finishes and tries to update session. since the last write is newer than the read from (a) the write will fail
 *   protecting the newer data written by (b).
 * /**/
class TPkgCmsSessionHandler_Decorator_EnforceWriteSequence extends Symfony\Component\HttpFoundation\Session\Storage\Proxy\SessionHandlerProxy
{
    private $writeSequenceCounter = 0;

    /**
     * @return mixed
     *
     * @throws TPkgCmsSessionStorageLockException
     */
    public function read(string $sSessionId): string|false
    {
        $this->writeSequenceCounter = 0;
        $sSessionData = parent::read($sSessionId);
        if ('' === $sSessionData) { // no session exists
            return $sSessionData;
        }
        $aSessionData = @unserialize($sSessionData);

        if (false === $aSessionData || false === is_array($aSessionData)) {
            return $sSessionData;
        }

        if (false === isset($aSessionData['data'])) {
            return $sSessionData;
        }

        $this->writeSequenceCounter = $this->getSequenceNumberFromPayload($aSessionData);

        return $aSessionData['data'];
    }

    protected function getStoredWriteSequenceCounter($sSessionId)
    {
        $sSessionData = parent::read($sSessionId);
        $aSessionData = @unserialize($sSessionData);

        return $this->getSequenceNumberFromPayload($aSessionData);
    }

    public function write(string $sSessionId, string $aSessionData): bool
    {
        $storedWriteSequenceCounter = $this->getStoredWriteSequenceCounter($sSessionId);

        // allow write only if the session has not been modified since we read it
        if ($storedWriteSequenceCounter > $this->writeSequenceCounter) {
            return true;
        }

        ++$this->writeSequenceCounter; // = microtime(true);

        $aNewSessionData = ['lastWrite' => $this->writeSequenceCounter, 'data' => $aSessionData];
        $sSessionData = serialize($aNewSessionData);

        return parent::write($sSessionId, $sSessionData);
    }

    private function getSequenceNumberFromPayload($aSessionData)
    {
        if (isset($aSessionData['lastWrite'])) {
            return (int) $aSessionData['lastWrite'];
        }

        return 0;
    }
}
