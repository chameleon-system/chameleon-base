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
 * Can be used to cache results.
 * Independent of cache manager in db.
 * Can bes used for caching templates from other sites, credit or address checks.
 *
/**/
class TPkgCmsResultCacheManager
{
    /**
     * Get cache entry value for owner and key. Note: the object will be returned even if expired
     * Returns false if no entry was found.
     *
     * @param string $sOwner
     * @param string $sKey
     * @param bool   $bIgnoreExpireTime
     *
     * @return bool|string
     */
    public function get($sOwner, $sKey, $bIgnoreExpireTime = false)
    {
        $sCachedValue = false;

        $oResultCache = TdbPkgCmsResultCache::GetNewInstance();
        if ($oResultCache->LoadFromFields(array('owner_hash' => sha1($sOwner), 'hash' => sha1($sKey)))) {
            $sCachedValue = unserialize(base64_decode($oResultCache->fieldData));

            // load worked
            if (false === $bIgnoreExpireTime) {
                $iExpireTimeStamp = strtotime($oResultCache->fieldDateExpireAfter);
                if (time() > $iExpireTimeStamp) {
                    $sCachedValue = false;
                }
            }
        }

        return $sCachedValue;
    }

    /**
     * returns true if the cache entry exists AND is valid.
     *
     * @param string $sOwner
     * @param string $sKey
     *
     * @return bool
     */
    public function exists($sOwner, $sKey)
    {
        $oResultCache = TdbPkgCmsResultCache::GetNewInstance();
        if (false === $oResultCache->LoadFromFields(array('owner_hash' => sha1($sOwner), 'hash' => sha1($sKey)))) {
            return false;
        }

        return true;
    }

    /**
     * Set value to cache.
     *
     * @param string      $sOwner
     * @param string      $sKey
     * @param string      $sValue
     * @param string|bool $expireTimestamp
     * @param bool        $bAllowGarbageCollection true = garbage collector delete entry if expired
     *
     * @return void
     */
    public function set($sOwner, $sKey, $sValue, $expireTimestamp, $bAllowGarbageCollection = true)
    {
        $aCacheEntryData = array();
        $aCacheEntryData['owner_hash'] = sha1($sOwner);
        $aCacheEntryData['hash'] = sha1($sKey);
        $aCacheEntryData['data'] = base64_encode(serialize($sValue));
        if (false !== $expireTimestamp) {
            $aCacheEntryData['date_expire_after'] = date('Y-m-d H:i:s', $expireTimestamp);
        }
        $aCacheEntryData['garbage_collect_when_expired'] = $bAllowGarbageCollection;
        $oResultCache = TdbPkgCmsResultCache::GetNewInstance();
        $aData = $aCacheEntryData;
        if (true === $oResultCache->LoadFromFields(
            array('owner_hash' => $aCacheEntryData['owner_hash'], 'hash' => $aCacheEntryData['hash'])
        )
        ) {
            $aData = $oResultCache->sqlData;
            foreach ($aCacheEntryData as $sKey => $sVal) {
                $aData[$sKey] = $sVal;
            }
        } else {
            $aData['date_created'] = date('Y-m-d H:i:s');
        }
        $oResultCache->LoadFromRow($aData);
        $oResultCache->AllowEditByAll(true);
        $oResultCache->Save();
        $oResultCache->AllowEditByAll(false);
    }

    /**
     * Delete expired cache entries.
     *
     * @return void
     */
    public function garbageCollector()
    {
        $sQuery = "DELETE FROM `pkg_cms_result_cache`
                          WHERE `pkg_cms_result_cache`.`garbage_collect_when_expired` = '1'
                            AND `pkg_cms_result_cache`.`date_expire_after` <  NOW()";
        MySqlLegacySupport::getInstance()->query($sQuery);
    }
}
