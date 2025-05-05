<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManagerBundle\Service;

use ChameleonSystem\MediaManager\Interfaces\MediaManagerListRequestFactoryInterface;
use ChameleonSystem\MediaManager\Interfaces\MediaTreeDataAccessInterface;
use ChameleonSystem\MediaManager\MediaManagerListRequest;
use ChameleonSystem\MediaManager\MediaManagerListState;

class MediaManagerListRequestFactory implements MediaManagerListRequestFactoryInterface
{
    /**
     * @var MediaTreeDataAccessInterface
     */
    private $mediaTreeDataAccess;

    public function __construct(MediaTreeDataAccessInterface $mediaTreeDataAccess)
    {
        $this->mediaTreeDataAccess = $mediaTreeDataAccess;
    }

    /**
     * {@inheritdoc}
     */
    public function createListRequestFromListState(MediaManagerListState $listState, $languageId)
    {
        $request = new MediaManagerListRequest();

        $searchTerm = $listState->getSearchTerm();

        if ('' !== $searchTerm) {
            $request->setSearchTerm($searchTerm);
        }

        $mediaTreeNodeId = $listState->getMediaTreeNodeId();
        if (null !== $mediaTreeNodeId) {
            $mediaTreeNode = $this->mediaTreeDataAccess->getMediaTreeNode($mediaTreeNodeId, $languageId);
            $request->setMediaTreeNode($mediaTreeNode);
            $request->setSubTreeIncluded($listState->isShowSubtree());
        }

        $request->setPageSize($listState->getPageSize());
        $request->setPageNumber($listState->getPageNumber());
        $request->setSortColumn($listState->getSortColumn());

        return $request;
    }
}
