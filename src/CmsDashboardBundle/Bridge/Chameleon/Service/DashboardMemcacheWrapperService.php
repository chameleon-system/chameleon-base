<?php

namespace ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Service;

use ChameleonSystem\CmsResultCacheBundle\Bridge\Chameleon\Service\DataBaseCacheManager;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use esono\pkgCmsCache\CacheInterface;

class DashboardMemcacheWrapperService
{
    public function getInstanceByServiceId(string $id): ?\TCMSMemcache
    {
        $paramId = 'chameleon_system_core.cache.memcache_activate';
        $param = ServiceLocator::getParameter($paramId);
        if (true === $param) {
            return ServiceLocator::get($id);
        } else {
            return null;
        }
    }
}
