<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManager\Interfaces;

use ChameleonSystem\MediaManager\DataModel\MediaItemDataModel;
use ChameleonSystem\MediaManager\DataModel\MediaTreeNodeDataModel;
use ChameleonSystem\MediaManager\Exception\DataAccessException;
use ChameleonSystem\MediaManager\MediaManagerListRequest;
use ChameleonSystem\MediaManager\MediaManagerListResult;

/**
 * Provides access to media items.
 */
interface MediaItemDataAccessInterface
{
    /**
     * Get a media item from the media pool.
     *
     * @param string $id
     * @param string $languageId
     *
     * @return MediaItemDataModel|null
     *
     * @throws DataAccessException
     */
    public function getMediaItem($id, $languageId);

    /**
     * Get multiple media items from the media pool at once.
     *
     * @param string $languageId
     *
     * @return MediaItemDataModel[]
     *
     * @throws DataAccessException
     */
    public function getMediaItems(array $ids, $languageId);

    /**
     * Get media items that are children of a media tree node.
     *
     * @param string $languageId
     * @param bool $includeSubtree
     *
     * @return MediaItemDataModel[]
     *
     * @throws DataAccessException
     */
    public function getMediaItemsInMediaTreeNode(
        MediaTreeNodeDataModel $mediaTreeNode,
        $languageId,
        $includeSubtree = true
    );

    /**
     * Delete a media item.
     *
     * @param string $id
     *
     * @return void
     *
     * @throws DataAccessException
     */
    public function deleteMediaItem($id);

    /**
     * Get a list of media items for a list request.
     *
     * @param string $languageId
     *
     * @return MediaManagerListResult
     *
     * @throws DataAccessException
     */
    public function getMediaItemList(MediaManagerListRequest $mediaManagerListRequest, $languageId);

    /**
     * Assign media item to a media tree node.
     *
     * @param string $mediaItemId
     * @param string $mediaTreeNodeId
     * @param string $languageId
     *
     * @return void
     *
     * @throws DataAccessException
     */
    public function setMediaTreeNodeOfMediaItem($mediaItemId, $mediaTreeNodeId, $languageId);

    /**
     * Update the description of a media item.
     *
     * @param string $mediaItemId
     * @param string $description
     * @param string $languageId
     *
     * @return void
     *
     * @throws DataAccessException
     */
    public function updateDescription($mediaItemId, $description, $languageId);

    /**
     * Set tags of a media item, delete tags not in list.
     *
     * @param string $mediaItemId
     * @param array $tagList
     * @param string $languageId
     *
     * @return void
     *
     * @throws DataAccessException
     */
    public function updateTags($mediaItemId, $tagList, $languageId);

    /**
     * Update the system name of a media item.
     *
     * @param string $mediaItemId
     * @param string $systemName
     * @param string $languageId
     *
     * @return void
     *
     * @throws DataAccessException
     */
    public function updateSystemName($mediaItemId, $systemName, $languageId);

    /**
     * Get a list of terms to be suggested when typing in search input.
     *
     * @param string $searchTerm
     * @param string $languageId
     *
     * @return array
     *
     * @throws DataAccessException
     */
    public function getTermsToAutoSuggestForSearchTerm($searchTerm, $languageId);
}
