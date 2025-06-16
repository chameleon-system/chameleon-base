<?php

namespace ChameleonSystem\CmsDashboardBundle\Bridge\Chameleon\Service;

use ChameleonSystem\CoreBundle\ServiceLocator;

class DashboardMemcacheWrapperService
{
    public function getInstanceByServiceId(string $id): ?\TCMSMemcache
    {
        // @see \ChameleonSystem\CmsCacheBundle\DependencyInjection\ChameleonSystemCmsCacheExtension::load for the parameter id
        $paramId = 'chameleon_system_core.cache.memcache_activate';
        $param = ServiceLocator::getParameter($paramId);
        if (true === $param) {
            return ServiceLocator::get($id);
        } else {
            return null;
        }
    }
}
