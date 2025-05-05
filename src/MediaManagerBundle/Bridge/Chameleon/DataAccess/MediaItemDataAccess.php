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

use ChameleonSystem\CoreBundle\Interfaces\FlashMessageServiceInterface;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Util\FieldTranslationUtil;
use ChameleonSystem\MediaManager\DataModel\MediaItemDataModel;
use ChameleonSystem\MediaManager\DataModel\MediaTreeNodeDataModel;
use ChameleonSystem\MediaManager\Exception\DataAccessException;
use ChameleonSystem\MediaManager\Interfaces\MediaItemDataAccessInterface;
use ChameleonSystem\MediaManager\Interfaces\SortColumnInterface;
use ChameleonSystem\MediaManager\MediaManagerListRequest;
use ChameleonSystem\MediaManager\MediaManagerListResult;
use ChameleonSystem\MediaManager\SortColumnCollection;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\DBALException;
use Doctrine\DBAL\ForwardCompatibility\DriverResultStatement;
use Doctrine\DBAL\ForwardCompatibility\DriverStatement;
use TdbCmsMessageManagerBackendMessage;

class MediaItemDataAccess implements MediaItemDataAccessInterface
{
    /**
     * @var Connection
     */
    private $databaseConnection;

    /**
     * @var \TTools
     */
    private $tools;

    /**
     * @var FlashMessageServiceInterface
     */
    private $flashMessageService;

    /**
     * @var SortColumnCollection
     */
    private $sortColumnCollection;

    /**
     * @var FieldTranslationUtil
     */
    private $fieldTranslationUtil;

    /**
     * @var LanguageServiceInterface
     */
    private $languageService;

    public function __construct(
        Connection $databaseConnection,
        \TTools $tools,
        FlashMessageServiceInterface $flashMessageService,
        SortColumnCollection $sortColumnCollection,
        FieldTranslationUtil $fieldTranslationUtil,
        LanguageServiceInterface $languageService
    ) {
        $this->databaseConnection = $databaseConnection;
        $this->tools = $tools;
        $this->flashMessageService = $flashMessageService;
        $this->sortColumnCollection = $sortColumnCollection;
        $this->fieldTranslationUtil = $fieldTranslationUtil;
        $this->languageService = $languageService;
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaItemsInMediaTreeNode(
        MediaTreeNodeDataModel $mediaTreeNode,
        $languageId,
        $includeSubtree = true
    ) {
        $mediaItems = [];
        $treeIds = [$mediaTreeNode->getId()];
        if (true === $includeSubtree) {
            $treeIds = array_merge($treeIds, $this->getSubTreeIdsFromMediaTreeNode($mediaTreeNode));
        }

        try {
            $stm = $this->databaseConnection->executeQuery(
                'SELECT * FROM `cms_media` WHERE `cms_media_tree_id` IN (:mediaTreeIds)',
                ['mediaTreeIds' => $treeIds],
                ['mediaTreeIds' => Connection::PARAM_STR_ARRAY]
            );
            $rows = $stm->fetchAllAssociative();
        } catch (DBALException $e) {
            throw new DataAccessException(
                sprintf('Error getting media items for tree node %s: %s', $mediaTreeNode->getId(), $e->getMessage())
            );
        }
        foreach ($rows as $row) {
            $mediaItems[] = $this->createDataModelFromTableObject(\TdbCmsMedia::GetNewInstance($row, $languageId));
        }

        return $mediaItems;
    }

    /**
     * @return array
     */
    private function getSubTreeIdsFromMediaTreeNode(MediaTreeNodeDataModel $mediaTreeNode)
    {
        $ids = [];
        $children = $mediaTreeNode->getChildren();
        foreach ($children as $child) {
            $ids[] = $child->getId();
            $ids = array_merge($ids, $this->getSubTreeIdsFromMediaTreeNode($child));
        }

        return $ids;
    }

    /**
     * @return MediaItemDataModel
     */
    private function createDataModelFromTableObject(\TdbCmsMedia $cmsMediaTableObject)
    {
        $dataModel = new MediaItemDataModel($cmsMediaTableObject->id, $cmsMediaTableObject->fieldPath);
        $dataModel->setName($cmsMediaTableObject->fieldDescription);

        $tags = [];
        $tagsList = $cmsMediaTableObject->GetFieldCmsTagsList();
        while ($tag = $tagsList->Next()) {
            $tags[] = $tag->fieldName;
        }
        $dataModel->setTags($tags);
        $fieldType = $cmsMediaTableObject->GetFieldCmsFiletype();
        if ($fieldType) {
            $dataModel->setType($fieldType->fieldName);
        }
        $dataModel->setWidth((int) $cmsMediaTableObject->fieldWidth);
        $dataModel->setHeight((int) $cmsMediaTableObject->fieldHeight);
        $dataModel->setAltTag($cmsMediaTableObject->fieldAltTag);
        if ('0000-00-00 00:00:00' !== $cmsMediaTableObject->fieldDateChanged) {
            $dataModel->setDateChanged(new \DateTime($cmsMediaTableObject->fieldDateChanged));
        }
        $dataModel->setSystemName($cmsMediaTableObject->fieldSystemname);
        $dataModel->setIconHtml($this->getFileTypeIconHtml($cmsMediaTableObject->id));

        return $dataModel;
    }

    private function getFileTypeIconHtml(string $imageId): string
    {
        $image = new \TCMSImage($imageId);

        return $image->GetPlainFileTypeIcon();
    }

    /**
     * {@inheritdoc}
     */
    public function deleteMediaItem($id)
    {
        $tableEditor = $this->tools->GetTableEditorManager('cms_media', $id);
        if (false === $tableEditor->Delete($id)) {
            $messageTexts = [];
            $messages = $this->flashMessageService->consumeMessages(\TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER);
            if (null !== $messages) {
                while ($message = $messages->Next()) {
                    /* @var TdbCmsMessageManagerBackendMessage $message */
                    $messageTexts[] = $message->fieldMessage;
                }
            }
            throw new DataAccessException(
                sprintf('Image with ID %s could not be deleted: %s', $id, implode('; ', $messageTexts))
            );
        }

        // eat delete success messages
        $messages = $this->flashMessageService->consumeMessages(\TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER);
        if (null === $messages) {
            return;
        }

        while ($message = $messages->Next()) {
            /** @var \TdbCmsMessageManagerBackendMessage $message */
            if ('TABLEEDITOR_DELETE_RECORD_SUCCESS' !== $message->fieldName) {
                throw new DataAccessException(
                    sprintf(
                        'Error deleting media item with ID %s: %s. Message code was %s.',
                        $id,
                        $message->fieldMessage,
                        $message->fieldName
                    )
                );
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaItemList(MediaManagerListRequest $mediaManagerListRequest, $languageId)
    {
        $query = 'SELECT * FROM `cms_media`';
        $queryRestrictions = [];
        $params = [];
        $paramTypes = [];

        $this->addMediaTreeNodeRestrictions($mediaManagerListRequest, $queryRestrictions, $params, $paramTypes);
        $this->addSearchRestrictions($mediaManagerListRequest, $languageId, $queryRestrictions, $params, $paramTypes);

        if (0 !== count($queryRestrictions)) {
            $query .= ' WHERE ('.implode(') AND (', $queryRestrictions).')';
        }

        $orderBy = $this->getOrderBy($mediaManagerListRequest, $languageId);
        $query .= ' '.$orderBy;

        return $this->getResultForQueryAndParameters(
            $mediaManagerListRequest,
            $query,
            $params,
            $paramTypes,
            $languageId
        );
    }

    /**
     * @return void
     */
    private function addMediaTreeNodeRestrictions(
        MediaManagerListRequest $mediaManagerListRequest,
        array &$queryRestrictions,
        array &$params,
        array &$paramTypes
    ) {
        $mediaTreeNode = $mediaManagerListRequest->getMediaTreeNode();
        if (null === $mediaTreeNode) {
            return;
        }

        $treeIds = [$mediaTreeNode->getId()];
        if (true === $mediaManagerListRequest->isSubTreeIncluded()) {
            $treeIds = array_merge($treeIds, $this->getSubTreeIdsFromMediaTreeNode($mediaTreeNode));
        }
        $queryRestrictions[] = '`cms_media_tree_id` IN (:mediaTreeIds)';
        $params['mediaTreeIds'] = $treeIds;
        $paramTypes['mediaTreeIds'] = Connection::PARAM_STR_ARRAY;
    }

    /**
     * @param string $languageId
     *
     * @return void
     */
    private function addSearchRestrictions(
        MediaManagerListRequest $mediaManagerListRequest,
        $languageId,
        array &$queryRestrictions,
        array &$params,
        array &$paramTypes
    ) {
        $searchTerm = $mediaManagerListRequest->getSearchTerm();
        if (null === $searchTerm || '' === $searchTerm) {
            return;
        }
        $searchTerm = trim($searchTerm);

        $language = $this->languageService->getLanguage($languageId);
        $parts = [];

        $parts[] = '`id` = :fullTerm';
        $parts[] = '`cmsident` = :fullTerm';

        $params['fullTerm'] = $searchTerm;
        $paramTypes['fullTerm'] = \PDO::PARAM_STR;

        $terms = preg_split(',[\.\/\\-\s],', $searchTerm);

        $descriptionFieldName = 'description';
        $descriptionFieldNameTranslated = $this->fieldTranslationUtil->getTranslatedFieldName(
            'cms_media',
            $descriptionFieldName,
            $language
        );

        $pathFieldName = 'path';
        $pathFieldNameTranslated = $this->fieldTranslationUtil->getTranslatedFieldName('cms_media', $pathFieldName, $language);
        $tagNameFieldName = $this->fieldTranslationUtil->getTranslatedFieldName('cms_tags', 'name', $language);

        $descriptionSearchQueryParts = [];
        $i = 0;
        foreach ($terms as $term) {
            ++$i;

            $descriptionSearchQueryParts[$descriptionFieldNameTranslated][] = $this->databaseConnection->quoteIdentifier($descriptionFieldNameTranslated).' LIKE :term'.(string) $i;

            if ($descriptionFieldNameTranslated !== $descriptionFieldName) {
                $descriptionSearchQueryParts[$descriptionFieldName][] = $this->databaseConnection->quoteIdentifier($descriptionFieldName).' LIKE :term'.(string) $i;
            }

            $descriptionSearchQueryParts[$pathFieldNameTranslated][] = $this->databaseConnection->quoteIdentifier($pathFieldNameTranslated).' LIKE :term'.(string) $i;

            if ($pathFieldNameTranslated !== $pathFieldName) {
                $descriptionSearchQueryParts[$pathFieldName][] = $this->databaseConnection->quoteIdentifier($pathFieldName).' LIKE :term'.(string) $i;
            }

            $params['term'.(string) $i] = '%'.$term.'%';
            $paramTypes['term'.(string) $i] = \PDO::PARAM_STR;
        }

        foreach ($descriptionSearchQueryParts as $key => $termItems) {
            if (count($termItems) > 1) {
                $parts[] = ' ('.implode(' AND ', $termItems).') ';
            } else {
                $parts[] = $termItems[0];
            }
        }

        $i = 0;
        $tagParts = [];
        foreach ($terms as $term) {
            ++$i;
            $tagParts[] = '`cms_tags`.'.$this->databaseConnection->quoteIdentifier($tagNameFieldName).' LIKE :term'.(string) $i;
        }
        $tagMatch = '';
        if (count($terms) > 1) {
            $params['termCount'] = count($terms);
            $paramTypes['termCount'] = \PDO::PARAM_INT;
            $tagMatch = 'GROUP BY source_id HAVING count(source_id) = :termCount';
        }

        $parts[] = '`id` IN (SELECT `source_id` FROM `cms_media_cms_tags_mlt` INNER JOIN `cms_tags` ON `cms_media_cms_tags_mlt`.`target_id` = `cms_tags`.`id` WHERE '.implode(
            ' OR ',
            $tagParts
        ).' '.$tagMatch.')';

        $queryRestrictions[] = implode(' OR ', $parts);
    }

    /**
     * @param string $languageId
     *
     * @return string
     */
    private function getOrderBy(MediaManagerListRequest $mediaManagerListRequest, $languageId)
    {
        $sortColumn = $this->sortColumnCollection->getSortColumnBySystemName($mediaManagerListRequest->getSortColumn());
        if (null === $sortColumn) {
            return 'ORDER BY `cmsident` DESC';
        }

        $language = $this->languageService->getLanguage($languageId);
        $sortColumnFieldName = $this->fieldTranslationUtil->getTranslatedFieldName(
            'cms_media',
            $sortColumn->getColumnName(),
            $language
        );

        return sprintf(
            'ORDER BY %s %s',
            $this->databaseConnection->quoteIdentifier($sortColumnFieldName),
            (SortColumnInterface::DIRECTION_DESCENDING === $sortColumn->getSortDirection()) ? 'DESC' : 'ASC'
        );
    }

    /**
     * @param string $query
     * @param string $languageId
     *
     * @return MediaManagerListResult
     *
     * @throws DataAccessException
     */
    private function getResultForQueryAndParameters(
        MediaManagerListRequest $mediaManagerListRequest,
        $query,
        array $params,
        array $paramTypes,
        $languageId
    ) {
        try {
            $result = $this->databaseConnection->executeQuery(
                $query,
                $params,
                $paramTypes
            );
            $numberOfRecords = (int) $result->rowCount();

            $result = $this->transformStatementForPaging($result, $mediaManagerListRequest, $query, $params, $paramTypes);
            $numberOfPages = (int) ceil($numberOfRecords / $mediaManagerListRequest->getPageSize());

            $mediaItems = [];
            $rows = $result->fetchAllAssociative();
            foreach ($rows as $row) {
                $mediaItems[] = $this->createDataModelFromTableObject(\TdbCmsMedia::GetNewInstance($row, $languageId));
            }
        } catch (DBALException $e) {
            throw new DataAccessException(sprintf('error getting media item list: %s', $e->getMessage()));
        }

        $result = new MediaManagerListResult($mediaItems);
        $result->setNumberOfItems($numberOfRecords);
        $result->setNumberOfPages($numberOfPages);

        return $result;
    }

    /**
     * @param DriverStatement|DriverResultStatement $stm
     * @param string $query
     *
     * @return DriverStatement|DriverResultStatement|\PDOStatement
     *
     * @throws DBALException
     */
    private function transformStatementForPaging(
        $stm,
        MediaManagerListRequest $mediaManagerListRequest,
        $query,
        array $params,
        array $paramTypes
    ) {
        if ($mediaManagerListRequest->getPageSize() > 0) {
            $start = $mediaManagerListRequest->getPageNumber() * $mediaManagerListRequest->getPageSize();
            $query .= ' LIMIT '.$start.','.(int) $mediaManagerListRequest->getPageSize();

            $stm = $this->databaseConnection->executeQuery(
                $query,
                $params,
                $paramTypes
            );
        }

        return $stm;
    }

    /**
     * {@inheritdoc}
     */
    public function setMediaTreeNodeOfMediaItem($mediaItemId, $mediaTreeNodeId, $languageId)
    {
        $mediaTreeItem = $this->getMediaItem($mediaItemId, $languageId);
        if (null === $mediaTreeItem) {
            DataAccessException::throwMediaItemNotFoundException($mediaItemId);
        }
        $tableEditor = $this->tools->GetTableEditorManager('cms_media', $mediaTreeItem->getId(), $languageId);
        if (false === $tableEditor->SaveField('cms_media_tree_id', $mediaTreeNodeId, true)) {
            throw new DataAccessException(
                sprintf(
                    'Media item tree connection for %s could not be changed to %s. Error saving field.',
                    $mediaItemId,
                    $mediaTreeNodeId
                )
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaItem($id, $languageId)
    {
        try {
            $row = $this->databaseConnection->fetchAssociative(
                'SELECT * FROM `cms_media` WHERE `id` = :id',
                ['id' => $id]
            );
        } catch (DBALException $e) {
            throw new DataAccessException(
                sprintf('Error getting media item with ID %s: %s', $id, $e->getMessage()),
                0,
                $e
            );
        }
        if (false === is_array($row)) {
            return null;
        }
        $tableObject = \TdbCmsMedia::GetNewInstance($row, $languageId);

        return $this->createDataModelFromTableObject($tableObject);
    }

    /**
     * {@inheritdoc}
     */
    public function getMediaItems(array $ids, $languageId)
    {
        $query = 'SELECT * FROM `cms_media` WHERE `id` IN (:ids)';
        try {
            $result = $this->databaseConnection->executeQuery(
                $query,
                ['ids' => $ids],
                ['ids' => Connection::PARAM_STR_ARRAY]
            );
        } catch (DBALException $e) {
            throw new DataAccessException(
                sprintf('Could not get media items with IDs %s: %s', implode(', ', $ids), $e->getMessage())
            );
        }
        $items = [];
        while ($row = $result->fetchAssociative()) {
            $tableObject = \TdbCmsMedia::GetNewInstance($row, $languageId);
            $items[] = $this->createDataModelFromTableObject($tableObject);
        }

        return $items;
    }

    /**
     * {@inheritdoc}
     */
    public function updateDescription($mediaItemId, $description, $languageId)
    {
        if ('' === trim($description)) {
            throw new DataAccessException(sprintf('Description cannot be empty.'));
        }
        $mediaTreeItem = $this->getMediaItem($mediaItemId, $languageId);
        if (null === $mediaTreeItem) {
            DataAccessException::throwMediaItemNotFoundException($mediaItemId);
        }
        try {
            $saved = $this->saveField($mediaItemId, 'description', $description, $languageId);
        } catch (DBALException $e) {
            throw new DataAccessException(
                sprintf('Description for item with id %s could not be saved: %s', $mediaItemId, $e->getMessage()), 0, $e
            );
        }
        if (false === $saved) {
            throw new DataAccessException(sprintf('Description for item with id %s could not be saved.', $mediaItemId));
        }
    }

    /**
     * @param string $mediaItemId
     * @param string $fieldName
     * @param string $value
     * @param string|null $languageId
     *
     * @return bool|\TCMSstdClass
     *
     * @throws DBALException
     */
    private function saveField($mediaItemId, $fieldName, $value, $languageId)
    {
        $row = $this->databaseConnection->fetchAssociative(
            'SELECT * FROM `cms_media` WHERE `id` = :id',
            ['id' => $mediaItemId]
        );
        if (false === is_array($row)) {
            return false;
        }
        $tableObject = \TdbCmsMedia::GetNewInstance($row, $languageId);
        $data = $tableObject->sqlData;
        $data[$fieldName] = $value;
        $tableEditor = \TTools::GetTableEditorManager('cms_media', $mediaItemId, $languageId);

        // we have to use Save() instead of SaveField() so PostSaveHook doesn't break image path (https://redmine.esono.de/issues/37163)
        return $tableEditor->Save($data, true);
    }

    /**
     * {@inheritdoc}
     */
    public function updateSystemName($mediaItemId, $systemName, $languageId)
    {
        $mediaTreeItem = $this->getMediaItem($mediaItemId, $languageId);
        if (null === $mediaTreeItem) {
            DataAccessException::throwMediaItemNotFoundException($mediaItemId);
        }
        try {
            $saved = $this->saveField($mediaItemId, 'systemname', $systemName, $languageId);
        } catch (DBALException $e) {
            throw new DataAccessException(
                sprintf('System name for item with ID %s could not be saved: %s', $mediaItemId, $e->getMessage()), 0, $e
            );
        }
        if (false === $saved) {
            throw new DataAccessException(sprintf('System name for item with ID %s could not be saved.', $mediaItemId));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function updateTags($mediaItemId, $tagList, $languageId)
    {
        try {
            $tags = $this->getExistingTags($tagList, $languageId);
            foreach ($tagList as $tagName) {
                if (false === \in_array($tagName, $tags, true)) {
                    $tagId = $this->insertTag($tagName, $languageId);
                    $tags[$tagId] = $tagName;
                }
            }

            $this->databaseConnection->executeQuery(
                'DELETE FROM `cms_media_cms_tags_mlt` WHERE `source_id` = :id',
                ['id' => $mediaItemId]
            );
            $i = 0;
            foreach (array_keys($tags) as $tagId) {
                $this->databaseConnection->insert(
                    'cms_media_cms_tags_mlt',
                    ['source_id' => $mediaItemId, 'target_id' => $tagId, 'entry_sort' => $i]
                );
                ++$i;
            }
        } catch (DBALException $e) {
            throw new DataAccessException($e->getMessage(), $e->getCode(), $e);
        }
    }

    /**
     * @param string[] $tagNames
     * @param string $languageId
     *
     * @return string[] - id => name
     *
     * @throws DBALException
     */
    private function getExistingTags(array $tagNames, $languageId)
    {
        $tags = [];
        $language = $this->languageService->getLanguage($languageId);
        $tagNameFieldName = $this->fieldTranslationUtil->getTranslatedFieldName('cms_tags', 'name', $language);

        $query = sprintf(
            'SELECT `id`, %1$s FROM `cms_tags` WHERE %1$s IN (?)',
            $this->databaseConnection->quoteIdentifier($tagNameFieldName)
        );
        $result = $this->databaseConnection->executeQuery($query, [$tagNames], [Connection::PARAM_STR_ARRAY]);
        while ($row = $result->fetchAssociative()) {
            $tags[$row['id']] = $row[$tagNameFieldName];
        }

        return $tags;
    }

    /**
     * @param string $tagName
     * @param string $languageId
     *
     * @return string
     *
     * @throws DBALException
     */
    private function insertTag($tagName, $languageId)
    {
        $language = $this->languageService->getLanguage($languageId);
        $tagNameFieldName = $this->fieldTranslationUtil->getTranslatedFieldName('cms_tags', 'name', $language);
        $id = \TTools::GetUUID();
        $this->databaseConnection->insert('cms_tags', ['id' => $id, $tagNameFieldName => $tagName]);

        return $id;
    }

    /**
     * {@inheritdoc}
     */
    public function getTermsToAutoSuggestForSearchTerm($searchTerm, $languageId)
    {
        $language = $this->languageService->getLanguage($languageId);
        $tagNameFieldName = $this->fieldTranslationUtil->getTranslatedFieldName('cms_tags', 'name', $language);
        try {
            $query = sprintf(
                'SELECT `cms_tags`.`id`, %1$s 
                          FROM `cms_tags` 
                    INNER JOIN `cms_media_cms_tags_mlt` ON `cms_media_cms_tags_mlt`.`target_id` = `cms_tags`.`id` 
                         WHERE %1$s LIKE :termLike 
                         ORDER BY (%1$s LIKE :termPre) DESC, length(%1$s)',
                $this->databaseConnection->quoteIdentifier('cms_tags.'.$tagNameFieldName)
            );
            $rows = $this->databaseConnection->fetchAllAssociative(
                $query,
                ['termLike' => '%'.$searchTerm.'%', 'termPre' => $searchTerm.'%']
            );
        } catch (DBALException $e) {
            throw new DataAccessException(
                sprintf('Could not get terms for auto suggest: %s', $e->getMessage()),
                $e->getCode(),
                $e
            );
        }

        return $rows;
    }
}
