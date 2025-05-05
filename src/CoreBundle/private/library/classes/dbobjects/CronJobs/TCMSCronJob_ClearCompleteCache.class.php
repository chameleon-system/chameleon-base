<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use esono\pkgCmsCache\CacheInterface;

/**
 * clear cache.
 * /**/
class TCMSCronJob_ClearCompleteCache extends TdbCmsCronjobs
{
    protected function _ExecuteCron()
    {
        /** @var CacheInterface $cache */
        $cache = ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.cache');
        $cache->clearAll();
    }
}
