<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\DatabaseAccessLayer;

use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;

/**
 * @deprecated since 6.1.0 use methods in chameleon_system_core.tree_service instead
 */
class DatabaseAccessLayerCmsTree extends AbstractDatabaseAccessLayer
{
    /**
     * @var bool
     */
    private $isLoaded = false;
    /**
     * @var PortalDomainServiceInterface
     */
    private $portalDomainService;

    /**
     * @var LanguageServiceInterface
     */
    private $languageService;

    /**
     * @var array<string, \TdbCmsTree|null>
     */
    private $objectCache = array();

    public function __construct(PortalDomainServiceInterface $portalDomainService, LanguageServiceInterface $languageService)
    {
        $this->portalDomainService = $portalDomainService;
        $this->languageService = $languageService;
    }

    /**
     * @param string $id
     * @param string $languageId
     *
     * @return \TdbCmsTree
     */
    public function loadFromId($id, $languageId = null)
    {
        $this->loadAllTreeNodes();

        return $this->getFromObjectCache($id, $languageId);
    }

    /**
     * @param string $id
     * @param string $languageId
     *
     * @return \TdbCmsTree
     */
    private function getFromObjectCache($id, $languageId = null)
    {
        if (null === $languageId) {
            $languageId = $this->languageService->getActiveLanguageId();
        }
        $key = $languageId.'_'.$id;
        if (!array_key_exists($key, $this->objectCache)) {
            $treeObject = null;
            $treeNode = $this->getFromCache($id);
            if (null !== $treeNode) {
                $treeObject = new \TdbCmsTree();
                $treeObject->SetLanguage($languageId);
                $treeObject->LoadFromRow($treeNode);
            }
            $this->objectCache[$key] = $treeObject;
        }

        return $this->objectCache[$key];
    }

    /**
     * @param string $id
     * @param bool   $includeHidden
     * @param string $languageId
     *
     * @return \TIterator
     */
    public function getChildren($id, $includeHidden, $languageId = null)
    {
        $this->loadAllTreeNodes();
        /** @var \TdbCmsTree[] $matches */
        $matches = $this->findDbObjectFromFieldInCache('parent_id', $id, $languageId);

        /**
         * @psalm-suppress InvalidArgument
         * @FIXME returning `null` from a sorting method is not allowed, should probably return `0`.
         */
        usort($matches, function (\TdbCmsTree $a, \TdbCmsTree $b) {
            if ($a->fieldEntrySort === $b->fieldEntrySort) {
                return null;
            }

            return ($a->fieldEntrySort < $b->fieldEntrySort) ? -1 : 1;
        }
        );

        $iterator = new \TIterator();
        foreach ($matches as $match) {
            if (false === $includeHidden && $match->fieldHidden) {
                continue;
            }
            $iterator->AddItem($match);
        }

        return $iterator;
    }

    /**
     * {@inheritdoc}
     */
    protected function findDbObjectFromFieldInCache($field, $value, $languageId = null)
    {
        $results = array();
        $cache = $this->getCompleteCache();
        reset($cache);
        $compareValue = (string) $value;
        foreach ($cache as $cacheKey => $cacheValue) {
            if (false === is_array($cache[$cacheKey])) {
                continue;
            }
            if (isset($cache[$cacheKey][$field]) && ((string) $cache[$cacheKey][$field]) === $compareValue) {
                $results[] = $this->getFromObjectCache($cache[$cacheKey]['id'], $languageId);
            }
        }
        reset($cache);

        return $results;
    }

    /**
     * @return void
     */
    private function loadAllTreeNodes()
    {
        if (true === $this->isLoaded) {
            return;
        }
        $this->isLoaded = true;

        $params = array();

        if ($this->portalDomainService->getActivePortal()) {
            $sQuery = 'SELECT DISTINCT `cms_tree`.*
                 FROM `cms_tree`
           INNER JOIN `cms_tree` AS parent ON (cms_tree.lft >= parent.lft and cms_tree.rgt <= parent.rgt)
           INNER JOIN `cms_portal` ON parent.id = `cms_portal`.`main_node_tree`
                WHERE `cms_portal`.`id` = :activePortalId
             ORDER BY `cms_tree`.`lft`

    ';
            $params['activePortalId'] = $this->portalDomainService->getActivePortal()->id;
        } else {
            $sQuery = 'SELECT * FROM `cms_tree` ORDER BY `lft`';
        }

        $treeNodes = $this->getDatabaseConnection()->fetchAll($sQuery, $params);
        foreach ($treeNodes as $treeNode) {
            $treeId = $treeNode['id'];
            $this->setCache($treeId, $treeNode);
        }
    }
}
