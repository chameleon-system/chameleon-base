<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\CoreEvents;
use ChameleonSystem\CoreBundle\Event\ChangeNavigationTreeNodeEvent;
use ChameleonSystem\CoreBundle\Factory\BackendTreeNodeFactory;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\TableEditor\NestedSet\NestedSetHelperFactoryInterface;
use ChameleonSystem\CoreBundle\TableEditor\NestedSet\NestedSetHelperInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use Doctrine\DBAL\Connection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Treemanagement Module for the CMS Navigation tree.
/**/
class CMSModulePageTree extends TCMSModelBase
{
    /**
     * the root node id.
     *
     * @var string
     */
    protected $iRootNode = null;

    /**
     * the root node object.
     *
     * @var TCMSTreeNode
     */
    protected $oRootNode = null;

    /**
     * the mysql tablename of the tree.
     *
     * @var string
     */
    protected $treeTable = 'cms_tree';

    /**
     * currentPageId
     *
     * @var string
     */
    protected $currentPageId = '';

    /**
     * primaryConnectedNodeIdOfCurrentPage
     *
     * @var string
     */
    protected $primaryConnectedNodeIdOfCurrentPage = '';


    /**
     * nodes that should not be assignable or that should have only a
     * restricted context menu.
     *
     * @var array
     */
    protected $aRestrictedNodes = array();

    /**
     * array of all currently open tree nodes.
     *
     * @var array
     */
    protected $aOpenTreeNodes = array();

    /**
     * count of portals in this CMS
     * tree level pre rendering is based on this.
     *
     * @var int
     */
    protected $iPortalCount = 1;

    /**
     * {@inheritdoc}
     */
    public function &Execute()
    {
        parent::Execute();

        $this->data['treeTableID'] = TTools::GetCMSTableId('cms_tree');
        $this->data['treeNodeTableID'] = TTools::GetCMSTableId('cms_tree_node');

        $inputFilterUtil = $this->getInputFilterUtil();
        $this->data['isInIframe'] = false;

        $isInIframe = $inputFilterUtil->getFilteredInput('isInIframe');
        if (null !== $isInIframe) {
            $this->data['isInIframe'] = true;
        }

        $this->data['rootNodeName'] = 'Root';

        $sPageTableId = TTools::GetCMSTableId('cms_tpl_page');
        $this->data['tplPageTableID'] = $sPageTableId;

        $this->data['showAssignDialog'] = true;
        if ($this->global->UserDataExists('noassign') && '1' == $this->global->GetUserData('noassign')) {
            $this->data['showAssignDialog'] = false;
        }

        $this->data['currentPageId'] = null;
        $this->data['primaryConnectedNodeIdOfCurrentPage'] = null;

        if ($this->global->UserDataExists('id')) {
            $this->data['currentPageId'] = $this->global->GetUserData('id');
            $this->currentPageId = $this->data['currentPageId'];
        }

        if ($this->global->UserDataExists('primaryTreeNodeId')) {
            $this->data['primaryConnectedNodeIdOfCurrentPage'] = $this->global->GetUserData('primaryTreeNodeId');
            $this->primaryConnectedNodeIdOfCurrentPage = $this->data['primaryConnectedNodeIdOfCurrentPage'];
        }

        $rootNodeID = $inputFilterUtil->getFilteredGetInput('rootID', TCMSTreeNode::TREE_ROOT_ID);
        $this->data['rootID'] = $rootNodeID;
        $this->iRootNode = $rootNodeID;
        $this->GetRootNodeName($rootNodeID);
        $this->LoadTreeState();
        $this->aRestrictedNodes = $this->GetPortalNavigationStartNodes();

        // Check if we have more than 3 portals (needed because of performance issues in tree pre-rendering)
        $oPortalList = TdbCmsPortalList::GetList();
        $this->iPortalCount = $oPortalList->Length();

        if ($this->global->UserDataExists('table')) {
            $this->data['table'] = $this->global->GetUserData('table');
        }

        $urlUtil = $this->getUrlUtil();
        $this->data['treeNodesAjaxUrl'] = $urlUtil->getArrayAsUrl(array('pagedef' => 'CMSModulePageTree', 'module_fnc' => array('contentmodule' => 'ExecuteAjaxCall'), '_fnc' => 'getTreeNodesJson',), PATH_CMS_CONTROLLER . '?', '&');
        $this->data['openPageConnectionListUrl'] = $urlUtil->getArrayAsUrl(array('id' => $this->data['treeNodeTableID'], 'pagedef' => 'tablemanagerframe', 'sRestrictionField' => 'cms_tree_id',), PATH_CMS_CONTROLLER . '?', '&');
        $this->data['openPageEditorUrl'] = $urlUtil->getArrayAsUrl(array('tableid' => $this->data['tplPageTableID'], 'pagedef' => 'templateengine'), PATH_CMS_CONTROLLER . '?', '&');
        $this->data['openPageConfigEditorUrl'] = $urlUtil->getArrayAsUrl(array('tableid' => $this->data['tplPageTableID'], 'pagedef' => 'tableeditor'), PATH_CMS_CONTROLLER . '?', '&');
        $this->data['openTreeNodeEditorUrl'] = $urlUtil->getArrayAsUrl(array('tableid' => $this->data['treeTableID'], 'pagedef' => 'tableeditorPopup'), PATH_CMS_CONTROLLER . '?', '&');
        $this->data['openTreeNodeEditorAddNewNodeUrl'] = $urlUtil->getArrayAsUrl(array('tableid' => $this->data['treeTableID'], 'pagedef' => 'tableeditorPopup', 'module_fnc' => array('contentmodule' => 'Insert')), PATH_CMS_CONTROLLER . '?', '&');
        $this->data['deleteNodeUrl'] = $urlUtil->getArrayAsUrl(array('pagedef' => 'CMSModulePageTree', 'module_fnc' => array('contentmodule' => 'ExecuteAjaxCall'), '_fnc' => 'DeleteNode', 'tableid' => $this->data['treeTableID']), PATH_CMS_CONTROLLER . '?', '&');
        $this->data['assignPageUrl'] = $urlUtil->getArrayAsUrl(array('tableid' => $this->data['treeNodeTableID'], 'pagedef' => 'tableeditorPopup', 'sRestrictionField' => 'cms_tree_id', 'module_fnc' => array('contentmodule' => 'Insert'), 'active' => '1', 'preventTemplateEngineRedirect' => '1'), PATH_CMS_CONTROLLER . '?', '&');
        $this->data['moveNodeUrl'] = $urlUtil->getArrayAsUrl(array('pagedef' => 'CMSModulePageTree', 'module_fnc' => array('contentmodule' => 'ExecuteAjaxCall'), '_fnc' => 'MoveNode', 'tableid' => $this->data['treeTableID']), PATH_CMS_CONTROLLER . '?', '&');

        $this->data['connectPageOnSelectUrl'] = $urlUtil->getArrayAsUrl(array('pagedef' => 'CMSModulePageTree', 'module_fnc' => array('contentmodule' => 'ExecuteAjaxCall'), '_fnc' => 'connectPageToNode', 'tableid' => $this->data['treeNodeTableID']), PATH_CMS_CONTROLLER . '?', '&');
        $this->data['disconnectPageOnDeselectUrl'] = $urlUtil->getArrayAsUrl(array('pagedef' => 'CMSModulePageTree', 'module_fnc' => array('contentmodule' => 'ExecuteAjaxCall'), '_fnc' => 'disconnectPageFromNode', 'tableid' => $this->data['treeNodeTableID']), PATH_CMS_CONTROLLER . '?', '&');

        return $this->data;
    }

    protected function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = array('SetConnection', 'MoveNode', 'DeleteNode', 'connectPageToNode', 'disconnectPageFromNode', 'getTreeNodesJson', 'GetTransactionDetails');
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    /**
     * fetches the name of the root node of the tree and fills $this->oRootNode with the CMSTreeNode object.
     *
     * @param int $nodeID
     */
    protected function GetRootNodeName($nodeID)
    {
        $oCMSTreeNode = TdbCmsTree::GetNewInstance(); /** @var $oCMSTreeNode TCMSTreeNode */
        $oCMSTreeNode->SetLanguage(TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());
        $oCMSTreeNode->Load($nodeID);

        $this->oRootNode = &$oCMSTreeNode;
        $this->data['rootNodeName'] = $oCMSTreeNode->sqlData['name'];
    }

    /**
     * loads the open tree nodes from cookie.
     */
    protected function LoadTreeState()
    {
        if (array_key_exists('chameleonTreeState', $_COOKIE) && !empty($_COOKIE['chameleonTreeState'])) {
            $this->aOpenTreeNodes = explode(',', $_COOKIE['chameleonTreeState']);
        }
    }

    /**
     * renders all children of a node
     * is called via ajax.
     *
     * @return string
     */
    public function getTreeNodesJson()
    {
        if ( $_GET["currentPageId"] !== "" ) {
            $this->currentPageId = $_GET["currentPageId"];
        }
        if ( $_GET["primaryConnectedNodeIdOfCurrentPage"] !== "" ) {
            $this->primaryConnectedNodeIdOfCurrentPage = $_GET["primaryConnectedNodeIdOfCurrentPage"];
        }


        if ( $_GET["id"] === "#" ) {
            $treeData = $this->createRootTreeWithDefaultPortalItems();
        }
        else {
            $treeNode = new TdbCmsTree();
            $treeNode->SetLanguage(TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());
            $treeNode->Load($_GET["id"]);
            $treeData = $this->createTreeDataModel($treeNode);
        }

        return $treeData;
    }

    protected function createRootTreeWithDefaultPortalItems ()
    {
        $inputFilterUtil = $this->getInputFilterUtil();
        $this->data['rootNodeName'] = 'Root';
        $rootNodeID = $inputFilterUtil->getFilteredGetInput('rootID', TCMSTreeNode::TREE_ROOT_ID);
        $this->data['rootID'] = $rootNodeID;
        $this->iRootNode = $rootNodeID;
        $this->GetRootNodeName($rootNodeID);
        $this->LoadTreeState();
        $this->aRestrictedNodes = $this->GetPortalNavigationStartNodes();

        $portalDomainService = $this->getPortalDomainService();
        $defaultPortal = $portalDomainService->getDefaultPortal();
        $defaultPortalMainNodeId = $defaultPortal->fieldMainNodeTree;

        $rootTreeNode = new TdbCmsTree();
        $rootTreeNode->SetLanguage(TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());
        $rootTreeNode->Load($this->iRootNode);

        $treeNodeFactory = $this->getBackendTreeNodeFactory();
        $rootTreeNodeDataModel = $treeNodeFactory->createTreeNodeDataModelFromTreeRecord($rootTreeNode);
        $rootTreeNodeDataModel->setOpened(true);
        $rootTreeNodeDataModel->setType('folderRootRestrictedMenu');
        $rootTreeNodeDataModel->setLiAttr(["class" => "no-checkbox"]);

        $oPortalList = TdbCmsPortalList::GetList();
        $this->iPortalCount = $oPortalList->Length();

        if ($oPortalList->Length() > 0) {
            while ($portal = $oPortalList->Next()) {

                $portalId = $portal->fieldMainNodeTree;

                $portalTreeNode = new TdbCmsTree();
                $portalTreeNode->SetLanguage(TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());
                $portalTreeNode->Load($portalId);

                if ($portalId === $defaultPortalMainNodeId) {
                    $portalTreeNodeDataModel = $this->createTreeDataModel($portalTreeNode);
                    $portalTreeNodeDataModel->setOpened(true);
                } else {
                    $treeNodeFactory = $this->getBackendTreeNodeFactory();
                    $portalTreeNodeDataModel = $treeNodeFactory->createTreeNodeDataModelFromTreeRecord($portalTreeNode);
                    $portalTreeNodeDataModel->setChildrenAjaxLoad(true);
                    $portalTreeNodeDataModel->setType("folder");
                    if (in_array($portalTreeNode->id, $this->aRestrictedNodes)) {
                        $typeRestricted = $portalTreeNodeDataModel->getType()."RestrictedMenu";
                        $portalTreeNodeDataModel->setType($typeRestricted);
                    }
                }
                $liAttr = $portalTreeNodeDataModel->getLiAttr();
                $liAttr = array_merge($liAttr, ["class" => "no-checkbox"]);
                $portalTreeNodeDataModel->setLiAttr($liAttr);

                $rootTreeNodeDataModel->addChildren($portalTreeNodeDataModel);
            }
        }
        return $rootTreeNodeDataModel;
    }

    protected function createTreeDataModel(TdbCmsTree $node, $level = 0)
    {
        $treeNodeFactory = $this->getBackendTreeNodeFactory();
        $treeNodeDataModel = $treeNodeFactory->createTreeNodeDataModelFromTreeRecord($node);

        $liAttr = $treeNodeDataModel->getLiAttr();
        $aAttr = [];
        $liClass = "";
        $aClass = "";

        $translator = $this->getTranslator();

        if ('' === $treeNodeDataModel->GetName()) {
            $unnamedRecordTitle = TGlobal::OutHTML($translator->trans('chameleon_system_core.text.unnamed_record'));
            $treeNodeDataModel->setName($unnamedRecordTitle);
        }

        $oCmsUser = TCMSUser::GetActiveUser();
        $oEditLanguage = $oCmsUser->GetCurrentEditLanguageObject();

        if (true === CMS_TRANSLATION_FIELD_BASED_EMPTY_TRANSLATION_FALLBACK_TO_BASE_LANGUAGE) {
            $cmsConfig = TCMSConfig::GetInstance();
            $defaultLanguage = $cmsConfig->GetFieldTranslationBaseLanguage();
            if (null !== $defaultLanguage && $defaultLanguage->id !== $oEditLanguage->id) {
                if ('' === $node->sqlData['name__' . $oEditLanguage->fieldIso6391]) {
                    $treeNodeDataModel->setName($treeNodeDataModel->getName().' <span class="bg-danger px-1"><i class="fas fa-language" title="'.$translator->trans('chameleon_system_core.cms_module_table_editor.not_translated').'"></i></span>');
                }
            }
        }

        $children = $node->GetChildren(true);
        if ($children->Length() > 0) {
            $treeNodeDataModel->setType('folder');
        } else {
            $treeNodeDataModel->setType('page');
        }
        if ("" !== $node->sqlData['link']) {
            $treeNodeDataModel->setType('externalLink');
        }

        if (in_array($node->id, $this->aRestrictedNodes)) {
            $typeRestricted = $treeNodeDataModel->getType()."RestrictedMenu";
            $treeNodeDataModel->setType($typeRestricted);
        }

        $nodeHidden = false;

        $node->SetLanguage($oEditLanguage->id);
        if (true == $node->fieldHidden) {
            $nodeHidden = true;
            $aClass .= " node-hidden";
            $treeNodeDataModel->setType('node-hidden');
        }

        $aPages = [];

        $oConnectedPages = $node->GetAllLinkedPages();
        $bFoundSecuredPage = false;
        while ($connectedPage = $oConnectedPages->Next()) {
            $aPages[] = $connectedPage->id;

            if (false === $nodeHidden
                && false === $bFoundSecuredPage
                && (true === $connectedPage->fieldExtranetPage
                    && false === $node->fieldShowExtranetPage)) {
                $aClass .= " page-hidden";
                $treeNodeDataModel->setType('page-hidden');
                $bFoundSecuredPage = true;
            }

            if (true === $connectedPage->fieldExtranetPage) {
                $treeNodeDataModel->setType('locked');
                $aClass .= " locked";
            }
        }

        if (count($aPages) > 0) {
            if ($treeNodeDataModel->getType() === "folder") {
                $treeNodeDataModel->setType('folderWithPage');
            }

            $sPrimaryPageID = $node->GetLinkedPage(true);
            if (false !== $sPrimaryPageID) {
                $liAttr = array_merge($liAttr, ["ispageid" => TGlobal::OutHTML($sPrimaryPageID)]);
            }

            // current page is connected to this node
            if ($this->currentPageId && in_array($this->currentPageId, $aPages)) {
                $aClass .= ' activeConnectedNode';
                $treeNodeDataModel->setOpened(true);
                $treeNodeDataModel->setSelected(true);

                if ($this->primaryConnectedNodeIdOfCurrentPage === $node->id) {
                    $aClass .= ' primaryConnectedNode';
                    $treeNodeDataModel->setDisabled(true);
                }
            } else {
                $aClass .= ' otherConnectedNode';
            }
        }

        if ($level == 0) {
            $treeNodeDataModel->setOpened(true);
        }

        $liAttr = array_merge($liAttr, ["class" => $liClass]);
        $treeNodeDataModel->setLiAttr($liAttr);

        $aAttr = array_merge($aAttr, ["class" => $aClass]);
        $treeNodeDataModel->setAAttr($aAttr);

        ++$level;
        while ($child = $children->Next()) {
            $childTreeNodeObj = $this->createTreeDataModel($child, $level);
            if ($childTreeNodeObj->isOpened()) {
                $treeNodeDataModel->setOpened(true);
            }
            $treeNodeDataModel->addChildren($childTreeNodeObj);
        }

        return $treeNodeDataModel;
    }

    /**
     * Fetches a list of all restricted nodes (of all portals)
     * a restricted node is a startnavigation nodes of a portal.
     *
     * @return array
     */
    protected function GetPortalNavigationStartNodes()
    {
        $oPortalList = TdbCmsPortalList::GetList(null, TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());

        $aRestrictedNodes = array();
        while ($oPortal = $oPortalList->Next()) {
            $aRestrictedNodes[] = $oPortal->fieldMainNodeTree;
            $oNavigationList = $oPortal->GetFieldPropertyNavigationsList();
            while ($oNavigation = $oNavigationList->Next()) {
                $aRestrictedNodes[] = $oNavigation->fieldTreeNode;
            }
        }

        return $aRestrictedNodes;
    }

    /**
     * get the portal node ids the user is NOT allowed to view, and exclude them from the list.
     *
     * @return string
     */
    protected function GetChildrenPortalCondition()
    {
        static $internalCache = null;
        if (null !== $internalCache) {
            $aPortalExcludeList = $internalCache;
        } else {
            $oUser = &TCMSUser::GetActiveUser();
            $sPortalList = $oUser->oAccessManager->user->portals->PortalList();
            $query = 'SELECT * FROM `cms_portal`';
            if (false !== $sPortalList) {
                $query .= ' WHERE `id` NOT IN ('.$sPortalList.')';
            }
            $aPortalExcludeList = array();
            $portalRes = MySqlLegacySupport::getInstance()->query($query);
            while ($portal = MySqlLegacySupport::getInstance()->fetch_assoc($portalRes)) {
                if (!empty($portal['main_node_tree'])) {
                    $aPortalExcludeList[] = $portal['main_node_tree'];
                }
            }
            $internalCache = $aPortalExcludeList;
        }

        $sPortalCondition = '';
        if (count($aPortalExcludeList) > 0) {
            $databaseConnection = $this->getDatabaseConnection();
            $quotedTableName = $databaseConnection->quoteIdentifier($this->treeTable);
            $portalExcludeListString = implode(',', array_map(array($databaseConnection, 'quote'), $aPortalExcludeList));
            $sPortalCondition .= " AND $quotedTableName.`id` NOT IN ($portalExcludeListString)";
        }

        return $sPortalCondition;
    }

    /**
     * Get the sort field name for the active language.
     * Get default field name if active language is same as portal main language,
     * field is not translateable or field based translation is off.
     *
     * @param string $sTreeId Id of an existing tree (need to check if language field exists)
     *
     * @return string
     */
    protected function GetSortFieldName($sTreeId)
    {
        $sEntrySortField = 'entry_sort';
        if (TdbCmsTree::CMSFieldIsTranslated('entry_sort')) {
            $sLanguagePrefix = TGlobal::GetLanguagePrefix($user = TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());
            if ('' !== $sLanguagePrefix) {
                $sEntrySortField .= '__'.$sLanguagePrefix;
            }

            return $sEntrySortField;
        }

        return $sEntrySortField;
    }

    /**
     * moves node to new position and updates all relevant node positions
     * and the parent node connection if changed.
     *
     * @return bool
     */
    public function MoveNode()
    {
        $returnVal = false;
        if ($this->global->UserDataExists('tableid') && $this->global->UserDataExists('nodeID') && $this->global->UserDataExists('parentNodeID') && $this->global->UserDataExists('position')) {
            $updatedNodes = array();
            $iTableID = $this->global->GetUserData('tableid');
            $iNodeID = $this->global->GetUserData('nodeID');
            $iParentNodeID = $this->global->GetUserData('parentNodeID');
            $iPosition = $this->global->GetUserData('position');
            if (!empty($iTableID) && !empty($iNodeID) && 'undefined' != $iNodeID && !empty($iParentNodeID)) { // prevent saving if node was dropped on empty space instead of an other node
                $oTableEditor = new TCMSTableEditorManager();
                $sEntrySortField = $this->GetSortFieldName($iParentNodeID);
                $databaseConnection = $this->getDatabaseConnection();
                $quotedTreeTable = $databaseConnection->quoteIdentifier($this->treeTable);
                $quotedParentNodeId = $databaseConnection->quote($iParentNodeID);
                if (0 == $iPosition) { // set every other node pos+1
                    $query = "SELECT * FROM $quotedTreeTable WHERE `parent_id` = $quotedParentNodeId";
                    $oCmsTreeList = TdbCmsTreeList::GetList($query);
                    while ($oCmsTreeNode = &$oCmsTreeList->Next()) {
                        $oTableEditor->Init($iTableID, $oCmsTreeNode->id);
                        $newSortOrder = $oTableEditor->oTableEditor->oTable->sqlData[$sEntrySortField] + 1;
                        $oTableEditor->SaveField('entry_sort', $newSortOrder);
                        $updatedNodes[] = $oCmsTreeNode;
                    }

                    $oTableEditor->Init($iTableID, $iNodeID);
                    $oTableEditor->SaveField('entry_sort', 0);
                    $oTableEditor->SaveField('parent_id', $iParentNodeID);
                } else {
                    $quotedNodeId = $databaseConnection->quote($iNodeID);
                    $quotedEntrySortField = $databaseConnection->quoteIdentifier($sEntrySortField);
                    $query = "SELECT * FROM $quotedTreeTable WHERE `parent_id` = $quotedParentNodeId AND `id` != $quotedNodeId ORDER BY $quotedEntrySortField  ASC";
                    $oCmsTreeList = &TdbCmsTreeList::GetList($query, TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());

                    $count = 0;
                    while ($oCmsTree = &$oCmsTreeList->Next()) {
                        if ($iPosition == $count) { // skip new position of moved node
                            ++$count;
                        }

                        $oTableEditor->Init($iTableID, $oCmsTree->id);
                        $oTableEditor->SaveField('entry_sort', $count);
                        ++$count;
                    }

                    $oTableEditor->Init($iTableID, $iNodeID);
                    $oTableEditor->SaveField('entry_sort', $iPosition);
                    $oTableEditor->SaveField('parent_id', $iParentNodeID);
                    $updatedNodes[] = $oCmsTree;
                }

                // update cache
                TCacheManager::PerformeTableChange($this->treeTable, $iNodeID);
                $this->UpdateSubtreePathCache($iNodeID);

                $returnVal = true;
            }

            $node = TdbCmsTree::GetNewInstance($iNodeID);
            $this->getNestedSetHelper()->updateNode($node);
            $this->writeSqlLog();

            $updatedNodes[] = $node;
            $event = new ChangeNavigationTreeNodeEvent($updatedNodes);
            $this->getEventDispatcher()->dispatch(CoreEvents::UPDATE_NAVIGATION_TREE_NODE, $event);
        }

        return $returnVal;
    }

    /**
     * deletes a node and all subnodes.
     *
     * @return mixed - returns false or id of the node that needs to be removed from the html tree
     */
    public function DeleteNode()
    {
        $returnVal = false;
        if ($this->global->UserDataExists('tableid') && $this->global->UserDataExists('nodeID')) {
            $iTableID = $this->global->GetUserData('tableid');
            $iNodeID = $this->global->GetUserData('nodeID');
            $oTableEditor = new TCMSTableEditorManager();
            $oTableEditor->Init($iTableID, $iNodeID);
            $returnVal = $iNodeID;
            $oTableEditor->Delete($iNodeID);
        }

        return $returnVal;
    }

    public function connectPageToNode(): string
    {
        if ($this->global->UserDataExists('tableid') && $this->global->UserDataExists('cms_tree_id')) {
            $tableID = $this->global->GetUserData('tableid');
            $nodeID = $this->global->GetUserData('cms_tree_id');
            $connectedPageId = $this->global->GetUserData('contid');
            $oTableEditor = new TCMSTableEditorManager();
            $oTableEditor->Init($tableID);
            $insertSuccessData = $oTableEditor->Insert();
            $recordId = $insertSuccessData->id;

            $postData = [];
            $postData['id'] = $recordId;
            $postData['active'] = '1';
            $postData['cms_tree_id'] = $nodeID;
            $postData['contid'] = $connectedPageId;
            $postData['tbl'] = 'cms_tpl_page';

            $oTableEditor->Save($postData);

            return $nodeID;
        }

        return '';
    }


    public function disconnectPageFromNode(): string
    {
        $dbConnection = $this->getDatabaseConnection();

        if ($this->global->UserDataExists('tableid') && $this->global->UserDataExists('contid')) {
            $iTableID = $this->global->GetUserData('tableid');
            $nodeID = $this->global->GetUserData('cms_tree_id');
            $contId = $this->global->GetUserData('contid');

            $query = "SELECT * 
                        FROM `cms_tree_node` 
                       WHERE `cms_tree_node`.`contid` = ".$dbConnection->quote($contId)."
                         AND `cms_tree_node`.`cms_tree_id` = ".$dbConnection->quote($nodeID)."
                         AND `cms_tree_node`.`tbl` = 'cms_tpl_page'
                       ";
            $nodePageConnectionList = TdbCmsTreeNodeList::GetList($query);
            while ($nodePageConnection = $nodePageConnectionList->Next()) {
                $oTableEditor = new TCMSTableEditorManager();
                $oTableEditor->Init($iTableID, $nodePageConnection->id);
                $oTableEditor->Delete($nodePageConnection->id);
            }
        }

        return $nodeID;
    }

    /**
     * update the cache of the tree path to each node of the given subtree.
     *
     * @param int $iNodeId
     */
    protected function UpdateSubtreePathCache($iNodeId)
    {
        $oNode = TdbCmsTree::GetNewInstance();
        $oNode->SetLanguage(TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());
        $oNode->Load($iNodeId);
        $oNode->TriggerUpdateOfPathCache();
    }

    /**
     * loads workflow transaction infos to show them in toaster messages.
     *
     * @return string
     *
     * @deprecated since 6.2.0 - workflow is not supported anymore
     */
    public function GetTransactionDetails()
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery-ui-1.12.1.custom/jquery-ui.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jsTree/3.3.8/jstree.js').'"></script>';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/navigationTree.js').'"></script>';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery/cookie/jquery.cookie.js').'" type="text/javascript"></script>';
        $aIncludes[] = sprintf('<link rel="stylesheet" href="%s">', TGlobal::GetStaticURLToWebLib('/javascript/jsTree/3.3.8/themes/default/style.css'));
        $aIncludes[] = sprintf('<link rel="stylesheet" href="%s">', TGlobal::GetStaticURLToWebLib('/javascript/jsTree/customStyles/style.css'));


        return $aIncludes;
    }

    /**
     * {@inheritdoc}
     */
    public function _AllowCache()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function _GetCacheParameters()
    {
        $parameters = parent::_GetCacheParameters();

        if (!is_array($parameters)) {
            $parameters = array();
        }

        $oCMSUser = &TCMSUser::GetActiveUser();
        $parameters['ouserid'] = $oCMSUser->id;
        $parameters['noassign'] = $this->global->GetUserData('noassign');
        $parameters['id'] = $this->global->GetUserData('id');
        $parameters['rootID'] = $this->global->GetUserData('rootID');
        $parameters['table'] = $this->global->GetUserData('table');

        return $parameters;
    }

    /**
     * {@inheritdoc}
     */
    public function _GetCacheTableInfos()
    {
        $aTableTriggerList = parent::_GetCacheTableInfos();
        if (!is_array($aTableTriggerList)) {
            $aTableTriggerList = array();
        }

        $oCMSUser = &TCMSUser::GetActiveUser();

        $aTableTriggerList[] = array('table' => 'cms_user', 'id' => $oCMSUser->id);
        $aTableTriggerList[] = array('table' => 'cms_user_cms_language_mlt', 'id' => '');
        $aTableTriggerList[] = array('table' => 'cms_user_cms_portal_mlt', 'id' => '');
        $aTableTriggerList[] = array('table' => 'cms_user_cms_role_mlt', 'id' => '');
        $aTableTriggerList[] = array('table' => 'cms_user_cms_usergroup_mlt', 'id' => '');
        $aTableTriggerList[] = array('table' => 'cms_user_cms_usergroup_mlt', 'id' => '');
        $aTableTriggerList[] = array('table' => 'cms_user_cms_usergroup_mlt', 'id' => '');
        $aTableTriggerList[] = array('table' => 'cms_role_cms_right_mlt', 'id' => '');
        $aTableTriggerList[] = array('table' => 'cms_tree', 'id' => '');
        $aTableTriggerList[] = array('table' => 'cms_tree_node', 'id' => '');

        return $aTableTriggerList;
    }

    private function writeSqlLog()
    {
        $command = <<<COMMAND
TCMSLogChange::initializeNestedSet('{$this->treeTable}', 'parent_id', 'entry_sort');
COMMAND;
        TCMSLogChange::WriteSqlTransactionWithPhpCommands('update nested set for table '.$this->treeTable, array($command));
    }

    /**
     * @return NestedSetHelperInterface
     */
    protected function getNestedSetHelper()
    {
        /** @var $factory NestedSetHelperFactoryInterface */
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

    /**
     * @return EventDispatcherInterface
     */
    private function getEventDispatcher()
    {
        return ServiceLocator::get('event_dispatcher');
    }

    private function getInputFilterUtil(): InputFilterUtilInterface
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }

    private function getBackendTreeNodeFactory(): BackendTreeNodeFactory
    {
        return ServiceLocator::get('chameleon_system_core.factory.backend_tree_node');
    }

    private function getPortalDomainService(): PortalDomainServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }

    private function getTranslator(): TranslatorInterface
    {
        return ServiceLocator::get('translator');
    }

    private function getUrlUtil(): UrlUtil
    {
        return ServiceLocator::get('chameleon_system_core.util.url');
    }
}
