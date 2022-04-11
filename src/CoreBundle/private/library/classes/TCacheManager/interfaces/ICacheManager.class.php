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
 * @deprecated since 6.2.0 - no longer used.
 */
interface ICacheManager
{
    /**
     * @static
     *
     * @return ICacheManager
     */
    public static function &GetInstance();

    /**
     * @param ICacheManagerStorage $oCacheStorage
     */
    public function SetCacheStorage(ICacheManagerStorage $oCacheStorage);

    /**
     * @return ICacheManagerStorage
     */
    public function GetCacheStorage();

    /**
     * allows you to disable caching during runtime.
     *
     * @static
     *
     * @param bool $bDisableCaching
     */
    public static function SetDisableCaching($bDisableCaching);

    public static function IsCachingEnabled();

    /**
     * return the contents for the key identified by $key
     * returns false if no cache entry exists.
     *
     * @param string $key - key generated with GetKey
     *
     * @return mixed - returns the cache object or false if not found
     */
    public static function GetContents($key);

    /**
     * add or updates a cache object.
     *
     * @param string $key               - the cache key
     * @param object $oContent          - object to be stored
     * @param array  $aTableInfos       - cache trigger array(array('table'=>'','id'=>''),array('table'=>'','id'=>''),...);
     * @param bool   $isPage
     * @param string $cleanKey
     * @param int    $iMaxLiveInSeconds - max age in seconds before the cache content expires - default = 30 days
     */
    public static function SetContent($key, &$oContent, $aTableInfos = null, $isPage = false, $cleanKey = null, $iMaxLiveInSeconds = null);

    public static function CleanKeyExists($cleanKey);

    /**
     * removes a cache object by key.
     *
     * @param string $key
     * @param bool   $bIsInternalCall - set to true if you only want to trigger the delete for external systems (such as memcache)
     *                                but not for the internal system
     *
     * @return bool
     */
    public static function DeleteContent($key, $bIsInternalCall = false);

    /**
     * clears full cached pages only from cache table.
     */
    public static function ClearPageCache();

    public static function SetClassNotFoundFlag($value = null);

    /**
     * clears the whole cache.
     */
    public static function ClearCache();

    /**
     * removes all cached objects based on table and optional record id.
     *
     * @param string     $table
     * @param int|string $id
     */
    public static function PerformeTableChange($table, $id = null);

    /**
     * returns a cache key for given parameter array.
     *
     * @param array $aParameters
     *
     * @return string
     */
    public static function GetKey($aParameters);

    /**
     * converts the parameters passed to a cache key - will not add any key attributes to the key (such as language, currency, protocol etc).
     *
     * @static
     *
     * @param array $aParameters
     */
    public static function GetKeyStateless($aParameters);

    /**
     * returns the table name for the table that holds the cache data (content) depending on memcache is active or not
     * if memcache is active the corresponding table will be used. we need to do that because of performance optimization
     * the memcache cache tables are running on MEMORY engine and not innoDB or MyISAM that performs very fast but cannot save text / blob data
     * that's the reason why we want to use other tables than for the normal database caching.
     *
     * @return string
     */
    public static function GetCacheTable();

    /**
     * returns the table name for the table that holds the cache keys (info) depending on memcache is active or not
     * if memcache is active the corresponding table will be used. we need to do that because of performance optimization
     * the memcache cache tables are running on MEMORY engine and not innoDB or MyISAM that performs very fast but cannot save text / blob data
     * that's the reason why we want to use other tables than for the normal database caching.
     *
     * @return string
     */
    public static function GetCacheInfoTable();
}
