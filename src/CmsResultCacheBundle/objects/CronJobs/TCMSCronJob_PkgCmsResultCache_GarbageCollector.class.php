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
 * Clears expired result cache entries.
 */
class TCMSCronJob_PkgCmsResultCache_GarbageCollector extends TdbCmsCronjobs
{
    /**
     * Runs result cache manager garbage collector to delete expired content.
     */
    protected function _ExecuteCron(): void
    {
        $this->getDataBaseCacheManagerService()->garbageCollector();
    }

    private function getDataBaseCacheManagerService(): DataBaseCacheManager
    {
        return ServiceLocator::get('chameleon_system_cms_result_cache.bridge_chameleon_service.data_base_cache_manager');
    }
}
