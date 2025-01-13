<?php

namespace ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Service;

use ChameleonSystem\CmsResultCacheBundle\Bridge\Chameleon\Service\DataBaseCacheManager;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use esono\pkgCmsCache\CacheInterface;

/**
 * This service is a hybrid cache service that uses the cache service in prod mode and database cache in dev mode.
 */
class DashboardCacheService
{
    private int $cacheTimeToLiveInSec;

    public function __construct(
        private readonly CacheInterface $cache,
        private readonly DataBaseCacheManager $dataBaseCacheManager,
        private readonly SecurityHelperAccess $securityHelperAccess,
        int $cacheTimeToLiveInSec = 86400) // one day
    {
        $this->cacheTimeToLiveInSec = $cacheTimeToLiveInSec;
    }

    public function setCachedBodyHtml(string $cacheKey, string $bodyHtml): void
    {
        if (true === $this->cache->isActive()) {
            $this->cache->set($cacheKey, $bodyHtml, [], $this->getCacheTimeToLiveInSec());
            $this->cache->set($this->getCacheTimestampKey($cacheKey), time(), [], $this->getCacheTimeToLiveInSec());
        } else {
            $this->dataBaseCacheManager->set('DashboardWidget', $cacheKey, $bodyHtml, time() + $this->getCacheTimeToLiveInSec(), true);
            $this->dataBaseCacheManager->set('DashboardWidget', $this->getCacheTimestampKey($cacheKey), time(), time() + $this->getCacheTimeToLiveInSec(), true);
        }
    }

    public function getCachedBodyHtml(string $cacheKey): ?string
    {
        if (true === $this->cache->isActive()) {
            $bodyFromCache = $this->cache->get($cacheKey);
        } else {
            // forced cache usage in dev mode
            $bodyFromCache = false === ($result = $this->dataBaseCacheManager->get('DashboardWidget', $cacheKey)) ? null : $result;
        }

        return $bodyFromCache;
    }

    public function getCacheCreationTime(string $cacheKey): ?int
    {
        if (true === $this->cache->isActive()) {
            $cacheValue = $this->cache->get($cacheKey);
        } else {
            $cacheValue = $this->dataBaseCacheManager->get('DashboardWidget', $cacheKey);
        }

        if (is_int($cacheValue)) {
            return $cacheValue;
        }

        return time();
    }

    public function setCacheTimeToLiveInSec(int $cacheTimeToLiveInSec): void
    {
        $this->cacheTimeToLiveInSec = $cacheTimeToLiveInSec;
    }

    private function getCacheTimeToLiveInSec(): int
    {
        return $this->cacheTimeToLiveInSec;
    }

    public function getCacheKey(string $callerClass): string
    {
        $user = $this->securityHelperAccess->getUser();

        return md5('dashboard_widget_body_'.$callerClass.'_'.($user ? $user->getId() : ''));
    }

    public function getCacheTimestampKey(string $cacheKey): string
    {
        return md5('dashboard_widget_body_timestamp_'.$cacheKey);
    }
}
