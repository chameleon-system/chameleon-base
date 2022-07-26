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
use esono\pkgCmsCache\CacheInterface;

ini_set('unserialize_callback_func', 'ClearCacheOnFailedLoad');
function ClearCacheOnFailedLoad($classname)
{
    return false;
}
/**
 * stores some result in the database along with information which tables and records
 * are connected to the cache.
 *
 * @deprecated since 5.0.0 - use chameleon_system_core.cache instead. Do not simply exchange the calls, but instead
 * think about the cache strategy to use. The problem with this implementation was that caching was used too
 * deliberately, leading to way too many cache entries. Instead it is recommended to cache mostly module contents.
/**/
class TCacheManager implements ICacheManager
{
    /**
     * @var ICacheManagerStorage
     */
    private $oCacheStorage = null;

    /**
     * @static
     *
     * @return TCacheManager
     */
    public static function &GetInstance()
    {
        static $oInstance = null;
        if (null === $oInstance) {
            $oInstance = new self();
        }

        return $oInstance;
    }

    /**
     * @param ICacheManagerStorage $oCacheStorage
     */
    public function SetCacheStorage(ICacheManagerStorage $oCacheStorage)
    {
        $this->oCacheStorage = $oCacheStorage;
    }

    /**
     * @return ICacheManagerStorage
     */
    public function GetCacheStorage()
    {
        return $this->oCacheStorage;
    }

    /**
     * allows you to disable caching during runtime.
     *
     * @static
     *
     * @param bool $bDisableCaching
     */
    public static function SetDisableCaching($bDisableCaching)
    {
        if (true === $bDisableCaching) {
            self::getCache()->disable();
        } else {
            self::getCache()->enable();
        }
    }

    public static function IsCachingEnabled()
    {
        return self::getCache()->isActive();
    }

    /**
     * return the contents for the key identified by $key
     * returns false if no cache entry exists.
     *
     * @param string $key - key generated with GetKey
     *
     * @return mixed - returns the cache object or false if not found
     */
    public static function GetContents($key)
    {
        return false;
    }

    /**
     * add or updates a cache object.
     *
     * @param string       $key               - the cache key
     * @param object|array $oContent          - object to be stored
     * @param array        $aTableInfos       - cache trigger array(array('table'=>'','id'=>''),array('table'=>'','id'=>''),...);
     * @param bool         $isPage
     * @param string       $cleanKey
     * @param int          $iMaxLiveInSeconds - max age in seconds before the cache content expires - default = 30 days
     */
    public static function SetContent($key, &$oContent, $aTableInfos = null, $isPage = false, $cleanKey = null, $iMaxLiveInSeconds = null)
    {
    }

    public static function CleanKeyExists($cleanKey)
    {
    }

    /**
     * removes a cache object by key.
     *
     * @param string $key
     * @param bool   $bIsInternalCall - set to true if you only want to trigger the delete for external systems (such as memcache)
     *                                but not for the internal system
     *
     * @return bool
     */
    public static function DeleteContent($key, $bIsInternalCall = false)
    {
    }

    /**
     * return the delete context.
     *
     * @return string
     */
    protected static function GetDeleteContext()
    {
    }

    /**
     * clears full cached pages only from cache table.
     */
    public static function ClearPageCache()
    {
    }

    public static function SetClassNotFoundFlag($value = null)
    {
    }

    /**
     * returns true, if no lock exists (for the key or all keys)
     * if a key was give, and we have write access, then the lock for that key is acquired
     * if no key is passed, the method will acquire the global lock.
     *
     * @param string $sKey
     * @param int    $iTimeout - optional timeout
     *
     * @return bool
     */
    protected static function AllowCacheWriteAccess($sKey = null, $iTimeout = 0)
    {
    }

    /**
     * clear the write lock for the key - or if non given for the global lock.
     *
     * @param string|null $sKey
     */
    protected static function ClearCacheWriteLock($sKey = null)
    {
    }

    /**
     * clears the whole cache.
     */
    public static function ClearCache()
    {
        self::getCache()->clearAll();
    }

    /**
     * removes all cached objects based on table and optional record id.
     *
     * @param string     $table
     * @param int|string $id
     */
    public static function PerformeTableChange($table, $id = null)
    {
        self::getCache()->callTrigger($table, $id);
    }

    /**
     * returns a cache key for given parameter array.
     *
     * @param array $aParameters
     *
     * @return string
     */
    public static function GetKey($aParameters)
    {
        return self::getCache()->getKey($aParameters, true);
    }

    /**
     * converts the parameters passed to a cache key - will not add any key attributes to the key (such as language, currency, protocol etc).
     *
     * @static
     *
     * @param array $aParameters
     */
    public static function GetKeyStateless($aParameters)
    {
        return self::getCache()->getKey($aParameters, false);
    }

    /**
     * return global cache key parameter. Cached for each call... so we collect only the very first time we call the method on each php call.
     *
     * @static
     *
     * @return array
     */
    protected static function GetGlobalCacheKeyParameter()
    {
        return '';
    }

    /**
     * use this hook to force refresh (who expected that) of global cache keys (TCacheManager::GetGlobalCacheKeyParameter())
     * or to get the state whether to refresh global cache keys or not.
     *
     * @param bool|null $bRefresh pass the new state to change the current ones. the new state will be returned until it is changed again
     *
     * @return bool the active or new state if passed
     */
    public static function refreshGlobalCacheKeys($bRefresh = null)
    {
        return false;
    }

    /**
     * returns the table name for the table that holds the cache data (content) depending on memcache is active or not
     * if memcache is active the corresponding table will be used. we need to do that because of performance optimization
     * the memcache cache tables are running on MEMORY engine and not innoDB or MyISAM that performs very fast but cannot save text / blob data
     * that's the reason why we want to use other tables than for the normal database caching.
     *
     * @return string
     */
    public static function GetCacheTable()
    {
        return '_cms_cache';
    }

    /**
     * returns the table name for the table that holds the cache keys (info) depending on memcache is active or not
     * if memcache is active the corresponding table will be used. we need to do that because of performance optimization
     * the memcache cache tables are running on MEMORY engine and not innoDB or MyISAM that performs very fast but cannot save text / blob data
     * that's the reason why we want to use other tables than for the normal database caching.
     *
     * @return string
     */
    public static function GetCacheInfoTable()
    {
        return '_cms_cache_info';
    }

    /**
     * @return CacheInterface
     */
    private static function getCache()
    {
        return ServiceLocator::get('chameleon_system_core.cache');
    }
}
