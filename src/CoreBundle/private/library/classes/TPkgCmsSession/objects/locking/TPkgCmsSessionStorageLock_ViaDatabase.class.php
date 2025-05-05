<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\DBAL\Connection;

class TPkgCmsSessionStorageLock_ViaDatabase implements IPkgCmsSessionStorageLock
{
    // **************************************************************************

    /**
     * @var Connection
     */
    private $databaseConnection;

    public function GetLock($sLockIdentifier, $iMaxTimeToWaitForLockInSeconds)
    {
        $bLocked = false;
        $query = 'SELECT GET_LOCK('.$this->getDatabaseConnection()->quote($sLockIdentifier).','.$iMaxTimeToWaitForLockInSeconds.')';
        $tRes = $this->getDatabaseConnection()->executeQuery($query);
        if ($aTmp = $tRes->fetchNumeric()) {
            if (isset($aTmp[0]) && '1' != $aTmp[0]) {
                $bLocked = false;
            // unable to obtain lock... exit...
            } else {
                $bLocked = true;
            }
        }

        return $bLocked;
    }

    public function ReleaseLock($sLockIdentifier)
    {
        $query = 'SELECT RELEASE_LOCK('.$this->getDatabaseConnection()->quote($sLockIdentifier).')';
        $this->getDatabaseConnection()->executeQuery($query);
    }

    public function AllowWriteAccess($sLockIdentifier)
    {
        $sLockConnectionIdentifier = '';

        $bIsLocked = false;
        $bMyLock = false;
        $query = 'SELECT IS_USED_LOCK('.$this->getDatabaseConnection()->quote($sLockIdentifier).')';
        if ($aData = $this->getDatabaseConnection()->fetchNumeric($query)) {
            if (isset($aData[0]) && null !== $aData[0]) {
                $sLockConnectionIdentifier = $aData[0];
                $bIsLocked = true;
            }
        }

        if ($bIsLocked) {
            $query = 'SELECT CONNECTION_ID()';
            if ($aData = $this->getDatabaseConnection()->fetchNumeric($query)) {
                if (isset($aData[0])) {
                    $bMyLock = ($aData[0] == $sLockConnectionIdentifier);
                }
            }
        }

        $bAllowWrite = (!$bIsLocked || $bMyLock);

        return $bAllowWrite;
    }

    public function sessionLockedByMe($sLockIdentifier)
    {
        $sLockConnectionIdentifier = '';

        $bIsLocked = false;
        $bMyLock = false;
        $query = 'SELECT IS_USED_LOCK('.$this->getDatabaseConnection()->quote($sLockIdentifier).')';
        if ($aData = $this->getDatabaseConnection()->fetchNumeric($query)) {
            if (isset($aData[0]) && null !== $aData[0]) {
                $sLockConnectionIdentifier = $aData[0];
                $bIsLocked = true;
            }
        }

        if ($bIsLocked) {
            $query = 'SELECT CONNECTION_ID()';
            if ($aData = $this->getDatabaseConnection()->fetchNumeric($query)) {
                if (isset($aData[0])) {
                    $bMyLock = ($aData[0] == $sLockConnectionIdentifier);
                }
            }
        }

        return $bMyLock;
    }

    public function setDatabaseConnection(Connection $connection)
    {
        $this->databaseConnection = $connection;
    }

    /**
     * @return Connection
     *
     * @throws TPkgCmsException_Log
     */
    protected function getDatabaseConnection()
    {
        if (null === $this->databaseConnection) {
            throw new TPkgCmsException_Log('you need to set a db connection for the session lock class');
        }

        return $this->databaseConnection;
    }
}
