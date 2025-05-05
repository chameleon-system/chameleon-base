<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use Doctrine\DBAL\Connection;

/**
 * Treemanagement Module for the CMS Navigation tree.
 */
class CMSModulePageTreeRPC extends TCMSModelBase
{
    public $rpcData;
    public $treeTable = 'cms_tree';
    public $treeContentTable = 'cms_tree_node';
    public $contentTable = 'cms_tpl_page';
    public $dbObjectCLass = 'TCMSTreeNode';
    public $onTitleClickCallBackFnc;
    public $actualPageID;

    public function Execute()
    {
        parent::Execute();

        $jsonData = $this->global->GetUserData('data');
        $this->rpcData = json_decode($jsonData);

        $rpcAction = $this->global->GetUserData('action');

        if ($this->global->UserDataExists('pageID')) {
            $this->actualPageID = $this->global->GetUserData('pageID');
        }

        if ('getChildren' == $rpcAction) {
            $this->data['jsonEncodedReturnData'] = $this->getChildren();
        } else {
            if ('createChild' == $rpcAction) {
                $this->data['jsonEncodedReturnData'] = $this->createChild();
            } else {
                if ('removeNode' == $rpcAction) {
                    $this->data['jsonEncodedReturnData'] = $this->removeNode();
                } else {
                    if ('move' == $rpcAction) {
                        $this->data['jsonEncodedReturnData'] = $this->move();
                    }
                }
            }
        }

        return $this->data;
    }

    public function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = ['getChildren', 'removeNode', 'move'];
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    /**
     * get children of current tree node.
     */
    public function getChildren()
    {
        $sPortalCondition = $this->getChildrenPortalCondition();

        $databaseConnection = $this->getDatabaseConnection();
        $quotedTreeTable = $databaseConnection->quoteIdentifier($this->treeTable);
        $quotedParentId = $databaseConnection->quote($this->rpcData->node->widgetId);

        $query = "SELECT T.*
                  FROM $quotedTreeTable AS T
                 WHERE T.`parent_id` = $quotedParentId
                 {$sPortalCondition}
                 ORDER BY entry_sort";

        $childrensArray = [];

        $oTreeNodes = new TCMSRecordList($this->dbObjectCLass, null, $query);

        while ($oTreeNode = $oTreeNodes->Next()) {
            /* @var $oTreeNode TCMSTreeNode */
            $childrensArray[] = $this->_nodeProperties($oTreeNode);
        }

        return json_encode($childrensArray);
    }

    /**
     * get the portal node ids the user is NOT allowed to view, and exclude them from the list.
     *
     * @return string
     */
    public function getChildrenPortalCondition()
    {
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        $portalIds = $securityHelper->getUser()?->getPortals();
        $sPortalList = '';
        if (null !== $portalIds && count($portalIds) > 0) {
            $sPortalList = implode(', ', array_map(fn (string $portalId) => $this->getDatabaseConnection()->quote($portalId), array_keys($portalIds)));
        }

        $query = 'SELECT `main_node_tree` FROM `cms_portal`';
        if ('' !== $sPortalList) {
            $query .= ' WHERE `id` NOT IN ('.$sPortalList.')';
        }
        $portalMainNodes = $this->getDatabaseConnection()->fetchAllAssociative($query);
        $aPortalExcludeList = array_map(fn ($row) => $this->getDatabaseConnection()->quote($row['main_node_tree']), $portalMainNodes);

        $sPortalCondition = '';
        if (count($aPortalExcludeList) > 0) {
            $sPortalCondition .= ' AND T.`id` NOT IN ('.implode(', ', $aPortalExcludeList).')';
        }

        return $sPortalCondition;
    }

    /**
     * callback function to create the node properties.
     *
     * @param TCMSTreeNode $oTreeNode
     */
    public function _nodeProperties($oTreeNode)
    {
        $child = [];

        // on navigation trees we need to highlight entries where a page is already connected
        if (!is_null($this->contentTable) && 'cms_tpl_page' == $this->contentTable) {
            $databaseConnection = $this->getDatabaseConnection();
            $quotedTreeContentTable = $databaseConnection->quoteIdentifier($this->treeContentTable);
            $quotedTreeNodeId = $databaseConnection->quote($oTreeNode->id);
            // check if node has connected pages
            $connectionQuery = "
                SELECT P.*
                  FROM $quotedTreeContentTable AS TD
             LEFT JOIN `cms_tpl_page` AS P ON TD.`contid` = P.`id`
                 WHERE TD.`tbl` = 'cms_tpl_page'
                   AND TD.`cms_tree_id` = $quotedTreeNodeId";

            $connectionResult = MySqlLegacySupport::getInstance()->query($connectionQuery);

            if (isset($connectionResult) && MySqlLegacySupport::getInstance()->num_rows($connectionResult) > 0) {
                $connectedPageClass = ' otherConnectedNode';
                if (!is_null($this->actualPageID)) {
                    while ($row = MySqlLegacySupport::getInstance()->fetch_assoc($connectionResult)) {
                        // check if node is actual page
                        if ($row['id'] == $this->actualPageID) {
                            $connectedPageClass = ' activeConnectedNode';
                        }
                    }
                }
            } else {
                $connectedPageClass = '';
            }
        } else {
            $connectedPageClass = '';
        }

        if (isset($oTreeNode->sqlData['hidden']) && '1' == $oTreeNode->sqlData['hidden']) {
            $hiddenClass = ' hiddenNode';
        } else {
            $hiddenClass = '';
        }

        $classesString = trim($hiddenClass.$connectedPageClass);

        $name = $oTreeNode->sqlData['name'];
        if (empty($name)) {
            $name = ServiceLocator::get('translator')->trans('chameleon_system_core.text.unnamed_record');
        }
        $child['title'] = '<span class=\''.$classesString.'\'>'.TGlobal::OutHTML($name).'</span>';

        $child['isFolder'] = ($oTreeNode->CountChildren(true) > 0);
        $child['childIconSrc'] = '';
        $child['widgetId'] = $oTreeNode->id;
        $child['widgetType'] = 'TreeNode';
        $child['objectId'] = $oTreeNode->id;
        $child['pageId'] = $oTreeNode->GetLinkedPage();

        if (!empty($oTreeNode->sqlData['link'])) {
            if (stristr($oTreeNode->sqlData['link'], 'http://')) {
                $child['afterLabel'] = "<a href='".$oTreeNode->sqlData['link']."' target='_blank'><img src='".TGlobal::GetPathTheme()."/images/icons/page_url.gif' style='padding-left: 5px;' border='0' /></a>";
            } else {
                $child['afterLabel'] = '<img src=\''.TGlobal::GetPathTheme().'/images/icons/page_url.gif\' style=\'padding-left: 5px;\' alt=\''.$oTreeNode->sqlData['link'].'" title="'.$oTreeNode->sqlData['link'].'\' />';
            }
        }

        return $child;
    }

    /**
     * delete node and all subnodes.
     */
    public function removeNode($target_id = null)
    {
        if (is_null($target_id)) {
            $target_id = $this->rpcData->node->widgetId;
        }

        // delete the portal tree
        $oTreeTableConf = new TCMSTableConf();
        /* @var $oTreeTableConf TCMSTableConf */
        $oTreeTableConf->LoadFromField('name', 'cms_tree');

        $oTreeEditor = new TCMSTableEditorManager();
        /* @var $oTreeEditor TCMSTableEditorManager */
        $oTreeEditor->Init($oTreeTableConf->id, $target_id);
        $oTreeEditor->Delete($target_id);

        return json_encode(true);
    }

    /**
     * move node and all subnodes.
     */
    public function move()
    {
        $nodeID = $this->rpcData->child->widgetId; // the node that moved
        $newParentNodeID = $this->rpcData->newParent->widgetId; // the parent target node
        $newIndex = 0;
        if (!empty($this->rpcData->newIndex)) {
            $newIndex = $this->rpcData->newIndex; // the sort index if placed between 2 nodes
        } else {
            $newIndex = 0; // set as first node
        }

        if (0 == $newIndex) {
            $query = 'UPDATE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->treeTable).'`
                     SET `entry_sort` = `entry_sort` +1
                   WHERE `entry_sort` >= '.MySqlLegacySupport::getInstance()->real_escape_string($newIndex)."
                     AND `parent_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($newParentNodeID)."'";
            MySqlLegacySupport::getInstance()->query($query);

            $query = 'UPDATE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->treeTable)."`
                     SET `entry_sort` = '".MySqlLegacySupport::getInstance()->real_escape_string($newIndex)."',
                         `parent_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($newParentNodeID)."'
                   WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($nodeID)."'";
            MySqlLegacySupport::getInstance()->query($query);
        } else {
            $query = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($this->treeTable)."` WHERE `parent_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($newParentNodeID)."' AND `id` != '".MySqlLegacySupport::getInstance()->real_escape_string($nodeID)."' ORDER BY `entry_sort`  ASC";
            $result = MySqlLegacySupport::getInstance()->query($query);

            $count = 0;
            while ($row = MySqlLegacySupport::getInstance()->fetch_assoc($result)) {
                if ($newIndex == $count) {
                    ++$count;
                }

                $updateQuery = 'UPDATE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->treeTable)."`
                       SET `entry_sort` = '".MySqlLegacySupport::getInstance()->real_escape_string($count)."'
                     WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($row['id'])."'";
                MySqlLegacySupport::getInstance()->query($updateQuery);

                ++$count;
            }

            $updateQuery2 = 'UPDATE `'.MySqlLegacySupport::getInstance()->real_escape_string($this->treeTable)."`
                      SET `entry_sort` = '".MySqlLegacySupport::getInstance()->real_escape_string($newIndex)."',
                          `parent_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($newParentNodeID)."'
                     WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($nodeID)."'";
            MySqlLegacySupport::getInstance()->query($updateQuery2);
        }

        $node = TdbCmsTree::GetNewInstance($nodeID);
        $this->getNestedSetHelper()->updateNode($node);
        $this->writeSqlLog();
        // update cache
        $this->getCacheService()->callTrigger($this->treeTable, $nodeID);
        $this->UpdateSubtreePathCache($nodeID);

        return json_encode(true);
    }

    /**
     * cache the tree path to each node of the given subtree.
     */
    protected function UpdateSubtreePathCache($iNodeId)
    {
        $oNode = TdbCmsTree::GetNewInstance();
        $oNode->Load($iNodeId);
        $oNode->TriggerUpdateOfPathCache();
    }

    private function writeSqlLog()
    {
        $command = <<<COMMAND
TCMSLogChange::initializeNestedSet('{$this->treeTable}', 'parent_id', 'entry_sort');
COMMAND;
        TCMSLogChange::WriteSqlTransactionWithPhpCommands('update nested set for table '.$this->treeTable, [$command]);
    }

    /**
     * @return ChameleonSystem\CoreBundle\TableEditor\NestedSet\NestedSetHelperInterface
     */
    protected function getNestedSetHelper()
    {
        /** @var $factory \ChameleonSystem\CoreBundle\TableEditor\NestedSet\NestedSetHelperFactoryInterface */
        $factory = ServiceLocator::get('chameleon_system_core.table_editor_nested_set_helper_factory');

        return $factory->createNestedSetHelper($this->treeTable, 'parent_id', 'entry_sort');
    }

    /**
     * @return Connection
     */
    private function getDatabaseConnection()
    {
        return ServiceLocator::get('database_connection');
    }
}
