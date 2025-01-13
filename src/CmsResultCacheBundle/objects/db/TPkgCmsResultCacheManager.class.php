<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CmsResultCacheBundle\Bridge\Chameleon\Service\DataBaseCacheManager;
use ChameleonSystem\CoreBundle\ServiceLocator;

/**
 * Can be used to cache results.
 * Independent of cache manager in db.
 * Can be used for caching templates from other sites, credit or address checks.
 *
 * @deprecated since 8.0.0 - use the drop in replacement ServiceLocator::get('chameleon_system_cms_result_cache.bridge_chameleon_service.data_base_cache_manager') instead
 **/
class TPkgCmsResultCacheManager
{
    /**
     * Get cache entry value for owner and key. Note: the object will be returned even if expired
     * Returns false if no entry was found.
     *
     * @param string $sOwner
     * @param string $sKey
     * @param bool $bIgnoreExpireTime
     *
     * @return bool|string
     */
    public function get($sOwner, $sKey, $bIgnoreExpireTime = false)
    {
        return $this->getDataBaseCacheManagerService()->get($sOwner, $sKey, $bIgnoreExpireTime);
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
        return $this->getDataBaseCacheManagerService()->exists($sOwner, $sKey);
    }

    /**
     * Set value to cache.
     *
     * @param string $sOwner
     * @param string $sKey
     * @param string $sValue
     * @param string|bool $expireTimestamp
     * @param bool $bAllowGarbageCollection true = garbage collector delete entry if expired
     *
     * @return void
     */
    public function set($sOwner, $sKey, $sValue, $expireTimestamp, $bAllowGarbageCollection = true)
    {
        $this->getDataBaseCacheManagerService()->set($sOwner, $sKey, $sValue, $expireTimestamp, $bAllowGarbageCollection);
    }

    /**
     * Deletes expired cache entries.
     */
    public function garbageCollector(): void
    {
        $this->getDataBaseCacheManagerService()->garbageCollector();
    }

    private function getDataBaseCacheManagerService(): DataBaseCacheManager
    {
        return ServiceLocator::get('chameleon_system_cms_result_cache.bridge_chameleon_service.data_base_cache_manager');
    }
}
