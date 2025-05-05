<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManagerBundle\Bridge\Chameleon\TableEditor;

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\MediaManager\Exception\DataAccessException;
use ChameleonSystem\MediaManager\Exception\UsageFinderException;
use ChameleonSystem\MediaManager\Interfaces\MediaItemDataAccessInterface;
use ChameleonSystem\MediaManager\MediaItemChainUsageFinder;
use esono\pkgCmsCache\CacheInterface;
use Psr\Log\LoggerInterface;

class CmsMediaTableEditor extends \TCMSTableEditorMedia
{
    /**
     * Don't call parent - parent is completely replaced by this.
     *
     * @param string $sImageId
     *
     * @return void
     */
    public function ClearCacheOfObjectsUsingImage($sImageId)
    {
        $cache = $this->getCache();

        if (false === $cache->isActive()) {
            return;
        }

        $chainUsageFinder = $this->getMediaItemChainUsageFinder();
        $mediaItemDataAccess = $this->getMediaItemDataAccess();
        try {
            $usages = $chainUsageFinder->findUsages(
                $mediaItemDataAccess->getMediaItem($sImageId, $this->getBackendSession()->getCurrentEditLanguageId())
            );

            foreach ($usages as $usage) {
                $cache->callTrigger($usage->getTargetTableName(), $usage->getTargetRecordId());
            }
        } catch (UsageFinderException $e) {
            $this->getLogger()->error(
                sprintf('Usages not found when clearing cache of image with ID %s: %s', $sImageId, $e->getMessage())
            );
        } catch (DataAccessException $e) {
            $this->getLogger()->error(
                sprintf('Image with ID %s not found: %s', $sImageId, $e->getMessage())
            );
        }
    }

    /**
     * @return CacheInterface
     */
    private function getCache()
    {
        return ServiceLocator::get('chameleon_system_cms_cache.cache');
    }

    /**
     * @return MediaItemChainUsageFinder
     */
    private function getMediaItemChainUsageFinder()
    {
        return ServiceLocator::get('chameleon_system_media_manager.usages.chain_finder');
    }

    /**
     * @return MediaItemDataAccessInterface
     */
    private function getMediaItemDataAccess()
    {
        return ServiceLocator::get('chameleon_system_media_manager.media_item.data_access');
    }

    private function getLogger(): LoggerInterface
    {
        return ServiceLocator::get('logger');
    }
}
