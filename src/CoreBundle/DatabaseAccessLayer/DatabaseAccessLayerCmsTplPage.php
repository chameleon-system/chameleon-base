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

/**
 * @deprecated since 6.1.0 use methods in chameleon_system_core.page_service instead
 */
class DatabaseAccessLayerCmsTplPage extends AbstractDatabaseAccessLayer implements DatabaseAccessLayerCmsTplPageInterface
{
    /**
     * @var bool
     */
    private $isLoaded = false;
    /**
     * @var PortalDomainServiceInterface
     */
    private $portalDomainService;

    public function __construct(PortalDomainServiceInterface $portalDomainService)
    {
        $this->portalDomainService = $portalDomainService;
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated since 6.1.0 - use chameleon_system_core.page_service::getById() instead
     */
    public function loadFromId($id)
    {
        $this->loadAllPages();

        return $this->getFromCache($id);
    }

    /**
     * {@inheritdoc}
     *
     * @deprecated since 6.1.0 - use chameleon_system_core.page_service::getByTreeId() instead
     */
    public function loadForTreeId($treeId, $bPreventFilter = false)
    {
        $this->loadAllPages();

        $cacheKeyData = array('cms_tree.id' => $treeId);
        $mappedKey = $this->getMapLookupKey($cacheKeyData);
        if (null !== $mappedKey) {
            $page = $this->getFromCacheViaMappedKey($mappedKey);
            if (null !== $page) {
                return $page;
            }
        }

        return false;
    }

    /**
     * @return void
     */
    private function loadAllPages()
    {
        if (true === $this->isLoaded) {
            return;
        }
        $this->isLoaded = true;

        $sQuery = 'SELECT `cms_tpl_page`.*, `cms_tree_node`.`cms_tree_id` AS __cms_tree_id
                     FROM `cms_tpl_page`
               INNER JOIN `cms_tree_node` ON `cms_tpl_page`.`id` = `cms_tree_node`.`contid`
               ORDER BY `cms_tpl_page`.`cmsident`, `cms_tree_node`.`start_date` DESC, `cms_tree_node`.`cmsident` DESC';

        $pages = $this->getDatabaseConnection()->fetchAll($sQuery);
        $treeIds = array();
        $pageIdsFound = array();
        foreach ($pages as $page) {
            $pageId = $page['id'];
            $treeId = $page['__cms_tree_id'];
            if (isset($treeIds[$treeId])) {
                continue;
            }
            $treeIds[$treeId] = $pageId;
            $cacheKeyData = array('cms_tree.id' => $treeId);
            $mappedKey = $this->getMapLookupKey($cacheKeyData);
            $this->setCacheKeyMapping($mappedKey, $pageId);

            if (isset($pageIdsFound[$pageId])) {
                continue;
            }
            $pageIdsFound[$pageId] = true;

            $pageObject = \TdbCmsTplPage::GetNewInstance($page);
            $this->setCache($pageId, $pageObject);
        }
    }
}
