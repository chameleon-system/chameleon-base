<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

interface IPkgCmsSessionStorageLock
{
    public function GetLock($sLockIdentifier, $iMaxTimeToWaitForLockInSeconds);

    public function ReleaseLock($sLockIdentifier);

    public function AllowWriteAccess($sLockIdentifier);

    /**
     * returns true if the session is locked by the current request.
     *
     * @param string $sLockIdentifier
     *
     * @return bool
     */
    public function sessionLockedByMe($sLockIdentifier);
}
