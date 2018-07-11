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
 * class can be used to store data that is used multiple times within one call request.
 *
 * IMPORTANT!!!!! we can NOT make $aCache static since PHP unset() will NOT clear a referenced object when
 * unsetting contents of a static var within a function (see php documentation of unset for details)
 *
 *
 *
/**/
class TCacheManagerRuntimeCache
{
    public $aCache = array();
    private static $bEnableAutoCaching = CHAMELEON_CACHING_ENABLE_RUNTIME_PER_DEFAULT;

    /**
     * @static
     *
     * @return TCacheManagerRuntimeCache
     */
    public static function &GetInstance()
    {
        static $oInstance = null;
        if (null === $oInstance) {
            $oInstance = new self();
        }

        return $oInstance;
    }

    public static function SetEnableAutoCaching($bEnableAutoCaching)
    {
        self::$bEnableAutoCaching = $bEnableAutoCaching;
    }

    public static function GetEnableAutoCaching()
    {
        if (TGlobal::IsCMSMode()) {
            return false;
        }

        return self::$bEnableAutoCaching;
    }

    public static function SetContent($sKey, $vItem, $sQueueId = '-', $iMaxQueueSize = null)
    {
        if (false === isset(self::GetInstance()->aCache[$sQueueId])) {
            self::GetInstance()->aCache[$sQueueId] = array();
        }
        if (null !== $iMaxQueueSize && count(self::GetInstance()->aCache[$sQueueId]) >= $iMaxQueueSize) {
            $aKeys = array_keys(self::GetInstance()->aCache[$sQueueId]);
            self::UnsetKey($aKeys[0], $sQueueId);
        }
        self::GetInstance()->aCache[$sQueueId][$sKey] = $vItem;
    }

    public static function UnsetKey($sKey, $sQueueId = '-')
    {
        if (self::KeyExists($sKey, $sQueueId)) {
            self::GetInstance()->aCache[$sQueueId][$sKey] = null;
            unset(self::GetInstance()->aCache[$sQueueId][$sKey]);
        }
    }

    public static function GetContents($sKey, $sQueueId = '-')
    {
        if (self::KeyExists($sKey, $sQueueId)) {
            return self::GetInstance()->aCache[$sQueueId][$sKey];
        } else {
            return false;
        }
    }

    public static function KeyExists($sKey, $sQueueId = '-')
    {
        return isset(self::GetInstance()->aCache[$sQueueId]) && is_array(self::GetInstance()->aCache[$sQueueId]) && isset(self::GetInstance()->aCache[$sQueueId][$sKey]);
    }

    public static function GetKey($aKey)
    {
        return TCacheManager::GetKey($aKey);
    }
}
