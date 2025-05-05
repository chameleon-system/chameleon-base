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
 * the class is used to implement global locks across clustered webserver -
 * we assume, that there is one database server, all webserver have access to - and use it
 * in combination with a memory table, to implement a locking mechanism.
 * /**/
class TCMSLockManager
{
    public const LOCKTABLE = '_cms_lockmanager';
    public const MAXAGE = 5;

    /**
     * Get a lock for Identifier $sIdentifier.
     *
     * @param string $sIdentifier
     *
     * @return bool
     */
    public static function GetLock($sIdentifier)
    {
        $bLocked = false;
        self::GarbageCollector();
        $iMaxWait = 30;
        while (!self::LockIsFree($sIdentifier) && $iMaxWait > 0) {
            sleep(1);
            --$iMaxWait;
        }
        if ($iMaxWait > 0) {
            $sEscapedIdent = MySqlLegacySupport::getInstance()->real_escape_string($sIdentifier);
            $query = 'INSERT INTO `'.self::LOCKTABLE."` (`key`,`created`) VALUES ('{$sEscapedIdent}',".time().")
      ON DUPLICATE KEY UPDATE `created` = '{$sEscapedIdent}'
                 ";
            MySqlLegacySupport::getInstance()->query($query);
            $bLocked = true;
        } else {
            TTools::WriteLogEntry('Unable to get Lock "{$sIdentifier}"', 2, __FILE__, __LINE__);
            $bLocked = false;
        }

        return $bLocked;
    }

    /**
     * free the lock identified by $sIdentifier.
     */
    public static function ReleaseLock($sIdentifier)
    {
        $query = 'DELETE FROM `'.self::LOCKTABLE."` WHERE `key` = '".MySqlLegacySupport::getInstance()->real_escape_string($sIdentifier)."'";
        MySqlLegacySupport::getInstance()->query($query);
    }

    /**
     * return true, if the lock is free.
     *
     * @param string $sIdentifier
     *
     * @return bool
     */
    public static function LockIsFree($sIdentifier)
    {
        $bIsFree = true;
        $sEscapedIdent = MySqlLegacySupport::getInstance()->real_escape_string($sIdentifier);
        $query = 'SELECT * FROM `'.self::LOCKTABLE."` WHERE `key` = '{$sEscapedIdent}'";
        $tRes = MySqlLegacySupport::getInstance()->query($query);

        $sqlError = MySqlLegacySupport::getInstance()->error();
        if (!empty($sqlError)) {
            trigger_error('SQL Error: '.$sqlError, E_USER_WARNING);
        }
        if ($aRes = MySqlLegacySupport::getInstance()->fetch_assoc($tRes)) {
            if ((time() - $aRes['created']) > self::MAXAGE) {
                $bIsFree = true;
                $query = 'DELETE FROM  `'.self::LOCKTABLE."` WHERE `key` = '{$sEscapedIdent}'";
                MySqlLegacySupport::getInstance()->query($query);
            } else {
                $bIsFree = false;
            }
        }

        return $bIsFree;
    }

    /**
     * removes old CacheKeys.
     *
     * @param float $iProbabilityInPercent - 0 to 1 -> how likely it is, that the collector will execute
     */
    protected static function GarbageCollector($iProbabilityInPercent = 10)
    {
        $iRand = rand(1, 100);
        if ($iRand <= $iProbabilityInPercent) {
            $iMaxAge = time() - self::MAXAGE;
            $query = 'DELETE FROM `'.self::LOCKTABLE."` WHERE `created` < {$iMaxAge}";
            MySqlLegacySupport::getInstance()->query($query);
        }
    }
}
