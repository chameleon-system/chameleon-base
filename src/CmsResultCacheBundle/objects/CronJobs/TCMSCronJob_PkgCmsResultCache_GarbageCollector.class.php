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
 * clear expired result cache entries.
/**/
class TCMSCronJob_PkgCmsResultCache_GarbageCollector extends TdbCmsCronjobs
{
    /**
     * runs result cache manager garbage collector to delete expired content.
     *
     * @return void
     */
    protected function _ExecuteCron()
    {
        $oResultCacheManager = new TPkgCmsResultCacheManager();
        $oResultCacheManager->garbageCollector();
    }
}
