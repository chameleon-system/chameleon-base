<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Util;

use Doctrine\DBAL\Connection;

class RoutingUtilDataAccess implements RoutingUtilDataAccessInterface
{
    /**
     * @var UrlUtil
     */
    private $urlUtil;
    /**
     * @var Connection
     */
    private $databaseConnection;
    /**
     * @var FieldTranslationUtil
     */
    private $fieldTranslationUtil;

    public function __construct(UrlUtil $urlUtil, Connection $databaseConnection, FieldTranslationUtil $fieldTranslationUtil)
    {
        $this->urlUtil = $urlUtil;
        $this->databaseConnection = $databaseConnection;
        $this->fieldTranslationUtil = $fieldTranslationUtil;
    }

    /**
     * {@inheritdoc}
     */
    public function getNaviLookup(\TdbCmsPortal $portal, \TdbCmsLanguage $language)
    {
        $lookup = [];
        $naviList = $portal->GetFieldPropertyNavigationsList();
        $naviList->GoToStart();
        while ($navi = $naviList->Next()) {
            $rootNode = \TdbCmsTree::GetNewInstance($navi->fieldTreeNode);
            $naviNodes = $this->getTreeLookup($portal->fieldHomeNodeId, $rootNode, '', true, $portal, $language);
            $lookup = array_merge($lookup, $naviNodes);
        }

        return $lookup;
    }

    /**
     * loads the full subtree for a node
     * called recursively.
     *
     * @param string $portalHomeNodeId
     * @param string $pathToNode
     * @param bool $jumpToChildren
     *
     * @return array
     */
    private function getTreeLookup($portalHomeNodeId, \TdbCmsTree $node, $pathToNode, $jumpToChildren, \TdbCmsPortal $portal, \TdbCmsLanguage $language)
    {
        $lookup = [];
        if ('' !== $node->fieldLink) {
            return $lookup;
        }
        $node->SetLanguage($language->id);
        if (false === $jumpToChildren) {
            $linkedPage = $node->GetLinkedPageObject();
            $pathToNode = $this->urlUtil->normalizeURL($pathToNode.'/'.$node->fieldUrlname, $portal, $language);
            if (null !== $linkedPage && false !== $linkedPage) {
                $groups = [];
                if (true === $linkedPage->fieldExtranetPage) {
                    $groups = $linkedPage->GetMLTIdList('data_extranet_group', 'data_extranet_group_mlt');
                }
                $lookup[$pathToNode] = [
                    'id' => $linkedPage->id,
                    'usessl' => $linkedPage->fieldUsessl,
                    'extranet_page' => $linkedPage->fieldExtranetPage,
                    'access_not_confirmed_user' => $linkedPage->fieldAccessNotConfirmedUser,
                    'data_extranet_group_mlt' => $groups,
                ];

                if ($node->id === $portalHomeNodeId) {
                    $lookup['/'] = $lookup[$pathToNode];
                }
            }
        }
        $children = $node->GetChildren(true, $language->id);
        while ($child = $children->Next()) {
            $childrenLookup = $this->getTreeLookup($portalHomeNodeId, $child, $pathToNode, false, $portal, $language);
            $lookup = array_merge($lookup, $childrenLookup);
        }

        return $lookup;
    }

    /**
     * {@inheritdoc}
     */
    public function getPageTreeNodes(\TdbCmsTplPage $page, ?\TdbCmsLanguage $language = null)
    {
        $query = "SELECT *
          FROM `cms_tree`
          WHERE `id` IN (
            SELECT DISTINCT `cms_tree_id`
            FROM `cms_tree_node`
            WHERE `tbl` = 'cms_tpl_page'
            AND `contid` = :contId
            AND `active` = '1'
            AND `start_date` <= :date
            AND (`end_date` >= :date OR `end_date` = '0000-00-00 00:00:00')
            ORDER BY `start_date` DESC, `cmsident` DESC
          )";
        $statement = $this->databaseConnection->prepare($query);
        $result = $statement->executeQuery([
            'contId' => $page->id,
            'date' => date('Y-m-d H:i:s'),
        ]);
        $retValue = [];
        $count = 0;
        $primaryTreeIdPosition = 0;
        while ($row = $result->fetchAssociative()) {
            $node = new \TdbCmsTree();
            if (null !== $language) {
                $node->SetLanguage($language->id);
            }
            $node->LoadFromRow($row);
            $retValue[] = $node;
            if ($node->id === $page->fieldPrimaryTreeIdHidden) {
                $primaryTreeIdPosition = $count;
            }
            ++$count;
        }
        // move the primary tree node to the first position
        if ($primaryTreeIdPosition > 0) {
            $temp = $retValue[0];
            $retValue[0] = $retValue[$primaryTreeIdPosition];
            $retValue[$primaryTreeIdPosition] = $temp;
        }

        return $retValue;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllPageAssignments(\TdbCmsPortal $portal, \TdbCmsLanguage $language)
    {
        $activeFieldName = $this->fieldTranslationUtil->getTranslatedFieldName('cms_tree_node', 'active', $language);
        $activeFieldName = $this->databaseConnection->quoteIdentifier($activeFieldName);

        $query = "SELECT DISTINCT t.`id` AS tree_id, p.`id` AS page_id FROM cms_tree AS t
          JOIN `cms_tree_node` AS tn
          ON t.`id` = tn.`cms_tree_id`
          JOIN `cms_tpl_page` AS p
          ON tn.`contid` = p.`id`
          WHERE p.`cms_portal_id` = :portalId
          AND tn.$activeFieldName = '1'
          AND tn.`start_date` <= :date
          AND (tn.`end_date` >= :date OR tn.`end_date` = '0000-00-00 00:00:00')
          ORDER BY 
            CASE WHEN (t.`id` = p.`primary_tree_id_hidden`)
                 THEN 1
                 ELSE 0
             END DESC,
            t.`id`, 
            tn.`start_date` DESC, 
            tn.`cmsident` DESC
        ";

        $statement = $this->databaseConnection->prepare($query);
        $result = $statement->executeQuery([
            'portalId' => $portal->id,
            'date' => date('Y-m-d H:i:s'),
        ]);

        $pageAssignmentList = [];
        foreach ($result->fetchAllAssociative() as $row) {
            //        while ($row = $result->fetch()) {
            $pageId = $row['page_id'];
            $treeId = $row['tree_id'];

            if (isset($pageAssignmentList[$treeId])) {
                /*
                 * There might be multiple pages assigned to a tree node. By ordering the result by start_date and cmsident
                 * above, we assured that the correct page is always at the first position (yes, there is no explicit
                 * representation of that state). This means we need to skip a result if we already found a page for a tree node.
                 */
                continue;
            }

            $pageAssignmentList[$treeId] = $pageId;
        }

        return $pageAssignmentList;
    }
}
