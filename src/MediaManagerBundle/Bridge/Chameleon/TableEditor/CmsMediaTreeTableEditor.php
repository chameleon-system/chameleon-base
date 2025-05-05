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
use ChameleonSystem\MediaManager\Interfaces\MediaItemDataAccessInterface;
use ChameleonSystem\MediaManager\Interfaces\MediaTreeDataAccessInterface;
use ErrorException;

class CmsMediaTreeTableEditor extends \TCMSTableEditorMediaTree
{
    /**
     * {@inheritDoc}
     */
    protected function updateCache($treeNodeId)
    {
        $languageId = $this->getBackendSession()->getCurrentEditLanguageId();
        $mediaTreeNode = $this->getMediaTreeNodeDataAccess()->getMediaTreeNode($treeNodeId, $languageId);
        if (null !== $mediaTreeNode) {
            /**
             * @var CmsMediaTableEditor $imageTableEditor
             */
            $imageTableEditor = \TTools::GetTableEditorManager('cms_media')->oTableEditor;

            try {
                $images = $this->getMediaItemDataAccess()->getMediaItemsInMediaTreeNode($mediaTreeNode, $languageId);
            } catch (DataAccessException $e) {
                /*
                 * @psalm-suppress InvalidArgument
                 * @FIXME Incorrect constructor arguments to `ErrorException`. Should be the following:
                 * throw new ErrorException($e->getMessage(), 0, 1, $e->getFile(), $e->getLine(), $e);
                 */
                throw new \ErrorException($e->getMessage(), 0, $e);
            }
            foreach ($images as $image) {
                $imageTableEditor->ClearCacheOfObjectsUsingImage($image->getId());
            }
        }
    }

    /**
     * @return MediaTreeDataAccessInterface
     */
    private function getMediaTreeNodeDataAccess()
    {
        return ServiceLocator::get(
            'chameleon_system_media_manager.media_tree.data_access'
        );
    }

    /**
     * @return MediaItemDataAccessInterface
     */
    private function getMediaItemDataAccess()
    {
        return ServiceLocator::get(
            'chameleon_system_media_manager.media_item.data_access'
        );
    }
}
