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

use ChameleonSystem\MediaManager\DataModel\MediaTreeDataModel;
use ChameleonSystem\MediaManager\DataModel\MediaTreeNodeDataModel;
use ChameleonSystem\MediaManager\Exception\DataAccessException;

/**
 * Provide access to the media tree.
 */
interface MediaTreeDataAccessInterface
{
    /**
     * Get root media tree including sub items in given language.
     *
     * @param string $languageId
     *
     * @return MediaTreeDataModel
     *
     * @throws DataAccessException
     */
    public function getMediaTree($languageId);

    /**
     * Get media tree node including sub items in given language.
     *
     * @param string $id
     * @param string $languageId
     *
     * @return MediaTreeNodeDataModel|null
     *
     * @throws DataAccessException
     */
    public function getMediaTreeNode($id, $languageId);

    /**
     * Insert media tree node under given parent.
     *
     * @param string $parentId
     * @param string $name
     * @param string $languageId
     *
     * @return MediaTreeNodeDataModel|null
     *
     * @throws DataAccessException
     */
    public function insertMediaTreeNode($parentId, $name, $languageId);

    /**
     * Rename a media tree node.
     *
     * @param string $id
     * @param string $name
     * @param string $languageId
     *
     * @return MediaTreeNodeDataModel|null
     *
     * @throws DataAccessException
     */
    public function renameMediaTreeNode($id, $name, $languageId);

    /**
     * Delete a media tree node.
     *
     * @param string $id
     *
     * @return void
     *
     * @throws DataAccessException
     */
    public function deleteMediaTreeNode($id);

    /**
     * Move media tree node to a parent at given position.
     *
     * @param string $id
     * @param string $parentId
     * @param int $position
     * @param string $languageId
     *
     * @return void
     *
     * @throws DataAccessException
     */
    public function moveMediaTreeNode($id, $parentId, $position, $languageId);
}
