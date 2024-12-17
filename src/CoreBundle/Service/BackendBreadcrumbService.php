<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Service;

use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;
use esono\pkgCmsCache\CacheInterface;

/**
 * manages the history object (also called "breadcrumb"), uses a cache and session object as fallback (esp. in development mode).
 */
class BackendBreadcrumbService implements BackendBreadcrumbServiceInterface
{
    private const BREADCRUMB_SESSION_KEY = '_cmsurlhistory';
    private const USER_BREADCRUMB_CACHE_TTL = 3 * 24 * 60 * 60; // 3 day (friday to monday), in seconds

    protected ?\TCMSURLHistory $history = null;

    public function __construct(
        private readonly CacheInterface $cache,
        private readonly SecurityHelperAccess $securityHelperAccess
    ) {
    }

    public function getBreadcrumb(): ?\TCMSURLHistory
    {
        if (false === $this->securityHelperAccess->isGranted(CmsUserRoleConstants::CMS_USER)) {
            return null;
        }

        $cmsUser = $this->securityHelperAccess->getUser();
        if (null === $cmsUser) {
            return null;
        }

        $backendUser = new \TCMSUser($cmsUser->getId());

        $breadCrumbHistory = $this->getBreadcrumbFromSession($backendUser);

        if (false === $breadCrumbHistory->paramsParameterExists()) {
            if (true === $this->cache->isActive()) {
                return $this->resetCache($backendUser);
            }

            return $this->resetSession();
        }

        return $this->getBreadcrumbFromSession($backendUser);
    }

    private function getBreadcrumbFromSession(\TCMSUser $backendUser): \TCMSURLHistory
    {
        if (null !== $this->history) {
            return $this->history;
        }

        if (true === $this->cache->isActive()) {
            $key = $this->getUserCacheKey($backendUser);

            $this->history = $this->cache->get($key);
            if (null !== $this->history) {
                // callback is locally not assigned yet
                $this->setOnChangeCallback();

                return $this->history;
            }

            return $this->resetCache($backendUser, $key);
        }

        return $this->resetSession();
    }

    /**
     * creates new empty breadcrumb in session object.
     */
    private function resetSession(): \TCMSURLHistory
    {
        $this->history = new \TCMSURLHistory();
        $_SESSION[self::BREADCRUMB_SESSION_KEY] = $this->history;

        return $this->history;
    }

    private function getUserCacheKey(\TCMSUser $backendUser): string
    {
        return $this->cache->getKey([
            'class' => __CLASS__,
            'method' => __METHOD__,
            'cms_user_id' => $backendUser->id,
        ]);
    }

    /**
     * creates new empty breadcrumb in cache.
     */
    private function resetCache(\TCMSUser $backendUser, ?string $key = null): \TCMSURLHistory
    {
        $this->history = new \TCMSURLHistory();
        $this->setOnChangeCallback();
        $this->setCacheValue($backendUser, $key);

        return $this->history;
    }

    private function setOnChangeCallback(): void
    {
        $this->history->setOnChangeCallback([$this, 'setCacheValue']);
    }

    public function setCacheValue(?\TCMSUser $backendUser = null, ?string $key = null): void
    {
        if (null === $backendUser) {
            $cmsUser = $this->securityHelperAccess->getUser();

            if (null === $cmsUser) {
                return;
            }

            $backendUser = new \TCMSUser($cmsUser->getId());
        }

        $key ??= $this->getUserCacheKey($backendUser);
        $this->cache->set($key, $this->history, ['cms_user' => $backendUser->id], self::USER_BREADCRUMB_CACHE_TTL);
    }
}
