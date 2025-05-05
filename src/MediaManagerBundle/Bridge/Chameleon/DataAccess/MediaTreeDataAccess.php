<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManagerBundle\Bridge\Chameleon\DataAccess;

use ChameleonSystem\MediaManager\DataModel\MediaTreeDataModel;
use ChameleonSystem\MediaManager\DataModel\MediaTreeNodeDataModel;
use ChameleonSystem\MediaManager\Exception\DataAccessException;
use ChameleonSystem\MediaManager\Interfaces\MediaTreeDataAccessInterface;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;

class MediaTreeDataAccess implements MediaTreeDataAccessInterface
{
    /**
     * @var Connection
     */
    private $databaseConnection;

    /**
     * @var \TTools
     */
    private $tools;

    public function __construct(Connection $databaseConnection, \TTools $tools)
    {
        $this->databaseConnection = $databaseConnection;
        $this->tools = $tools;
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaTree($languageId)
    {
        try {
            $rowRootNode = $this->databaseConnection->fetchAssociative(
                "SELECT * FROM `cms_media_tree` WHERE `parent_id` = ''"
            );
            $rootNode = $this->createMediaTreeNodeModelFromTableObject(
                \TdbCmsMediaTree::GetNewInstance($rowRootNode, $languageId)
            );
        } catch (DBALException $e) {
            throw new DataAccessException(sprintf('Error fetching media tree: %s', $e->getMessage()), 0, $e);
        }

        return new MediaTreeDataModel($rootNode);
    }

    /**
     * @return MediaTreeNodeDataModel
     *
     * @throws DBALException
     */
    private function createMediaTreeNodeModelFromTableObject(\TdbCmsMediaTree $tableObject)
    {
        $children = $this->getChildrenForMediaTreeNodeId($tableObject->id, $tableObject->GetLanguage());

        $dataModel = new MediaTreeNodeDataModel($tableObject->id, $tableObject->fieldName, $children);
        $dataModel->setIconPath(
            '' !== $tableObject->fieldIcon ? URL_USER_CMS_PUBLIC.'/blackbox/images/icons/'.$tableObject->fieldIcon : null
        );

        return $dataModel;
    }

    /**
     * @param string $id
     * @param string $languageId
     *
     * @return MediaTreeNodeDataModel[]
     *
     * @throws DBALException
     */
    private function getChildrenForMediaTreeNodeId($id, $languageId)
    {
        $children = [];
        $rows = $this->databaseConnection->fetchAllAssociative(
            'SELECT * FROM `cms_media_tree` WHERE `parent_id` = :parentId ORDER BY `entry_sort`',
            ['parentId' => $id]
        );
        foreach ($rows as $row) {
            $children[] = $this->createMediaTreeNodeModelFromTableObject(
                \TdbCmsMediaTree::GetNewInstance($row, $languageId)
            );
        }

        return $children;
    }

    /**
     * {@inheritdoc}
     */
    public function insertMediaTreeNode($parentId, $name, $languageId)
    {
        $tableEditor = $this->tools->GetTableEditorManager('cms_media_tree', null, $languageId);
        if (false === $tableEditor->Save(
            [
                'parent_id' => $parentId,
                'icon' => '',
                'entry_sort' => $this->getMaxEntrySort($parentId) + 1,
                'name' => $name,
            ]
        )
        ) {
            throw new DataAccessException(sprintf("Could not create '%s' with parent %s", $name, $parentId));
        }

        return $this->getMediaTreeNode($tableEditor->sId, $languageId);
    }

    /**
     * @param string $parentId
     *
     * @return int
     */
    private function getMaxEntrySort($parentId)
    {
        $query = 'SELECT MAX(entry_sort) AS entry_sort FROM `cms_media_tree` WHERE `parent_id` = :parentId';
        $row = $this->databaseConnection->fetchAssociative($query, ['parentId' => $parentId]);

        return (int) $row['entry_sort'];
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaTreeNode($id, $languageId)
    {
        try {
            $row = $this->databaseConnection->fetchAssociative(
                'SELECT * FROM `cms_media_tree` WHERE `id` = :id',
                ['id' => $id]
            );
            if (false === is_array($row)) {
                return null;
            }

            return $this->createMediaTreeNodeModelFromTableObject(\TdbCmsMediaTree::GetNewInstance($row, $languageId));
        } catch (DBALException $e) {
            throw new DataAccessException(
                sprintf('Error fetching media tree node with ID %s: %s', $id, $e->getMessage()), 0, $e
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function renameMediaTreeNode($id, $name, $languageId)
    {
        $tableEditor = $this->tools->GetTableEditorManager('cms_media_tree', $id, $languageId);
        if (false === $tableEditor->Save(
            [
                'id' => $id,
                'name' => $name,
            ]
        )
        ) {
            throw new DataAccessException(sprintf("Could not rename media tree node '%s'.", $id));
        }

        return $this->getMediaTreeNode($tableEditor->sId, $languageId);
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMediaTreeNode($id)
    {
        $tableEditor = $this->tools->GetTableEditorManager('cms_media_tree', $id);
        if (false === $tableEditor->Delete($id)) {
            throw new DataAccessException(sprintf("Could not delete media tree node '%s'.", $id));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function moveMediaTreeNode($id, $parentId, $position, $languageId)
    {
        $parent = $this->getMediaTreeNode($parentId, $languageId);
        if (null === $parent) {
            throw new DataAccessException(sprintf("Media tree node '%s' not found.", $id));
        }
        $children = $parent->getChildren();

        $tableEditor = $this->tools->GetTableEditorManager('cms_media_tree', $id, $languageId);
        if (false === $tableEditor->SaveField('parent_id', $parentId)) {
            throw new DataAccessException(sprintf("Moving media tree node '%s' to '%s' failed.", $id, $parentId));
        }
        $tableEditor->SaveField('entry_sort', $position, true);

        $positionChild = 0;
        foreach ($children as $child) {
            if ($child->getId() === $id) {
                continue;
            }
            if ($position === $positionChild) {
                ++$positionChild;
            }
            $tableEditor = $this->tools->GetTableEditorManager('cms_media_tree', $child->getId(), $languageId);
            $tableEditor->SaveField('entry_sort', $positionChild);
            ++$positionChild;
        }
    }
}
