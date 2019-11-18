<?php

namespace ChameleonSystem\CoreBundle\Bridge\Chameleon\Module\NavigationTree;

use ChameleonSystem\CoreBundle\CoreEvents;
use ChameleonSystem\CoreBundle\Event\ChangeNavigationTreeNodeEvent;
use ChameleonSystem\CoreBundle\Factory\BackendTreeNodeFactory;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\TableEditor\NestedSet\NestedSetHelperFactoryInterface;
use ChameleonSystem\CoreBundle\TableEditor\NestedSet\NestedSetHelperInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use Doctrine\DBAL\Connection;
use IMapperCacheTriggerRestricted;
use IMapperVisitorRestricted;
use MTPkgViewRendererAbstractModuleMapper;
use MySqlLegacySupport;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;
use TCacheManager;
use TCMSConfig;
use TCMSLogChange;
use TCMSTableEditorManager;
use TCMSTreeNode;
use TCMSUser;
use TdbCmsPortalList;
use TdbCmsTree;
use TdbCmsTreeList;
use TdbCmsTreeNodeList;
use TdbCmsUser;
use TGlobal;
use TTools;

class NavigationTree extends MTPkgViewRendererAbstractModuleMapper
{
    /**
     * the root node id.
     *
     * @var string
     */
    private $rootNode = null;

    /**
     * the mysql tablename of the tree.
     *
     * @var string
     */
    private $treeTable = 'cms_tree';

    /**
     * currentPageId.
     *
     * @var string
     */
    private $currentPageId = '';

    /**
     * primaryConnectedNodeIdOfCurrentPage.
     *
     * @var string
     */
    private $primaryConnectedNodeIdOfCurrentPage = '';

    /**
     * nodes that should not be assignable or that should have only a
     * restricted context menu.
     *
     * @var array
     */
    private $restrictedNodes = array();

    /**
     * array of all currently open tree nodes.
     *
     * @var array
     */
    private $aOpenTreeNodes = array();

    /**
     * count of portals in this CMS
     * tree level pre rendering is based on this.
     *
     * @var int
     */
    private $portalCount = 1;
    /**
     * @var Connection
     */
    private $dbConnection;
    /**
     * @var EventDispatcherInterface
     */
    private $eventDispatcher;
    /**
     * @var InputFilterUtilInterface
     */
    private $inputFilterUtil;
    /**
     * @var BackendTreeNodeFactory
     */
    private $backendTreeNodeFactory;
    /**
     * @var PortalDomainServiceInterface
     */
    private $portalDomainService;
    /**
     * @var TranslatorInterface
     */
    private $translator;
    /**
     * @var UrlUtil
     */
    private $urlUtil;
    /**
     * @var NestedSetHelperFactoryInterface
     */
    private $nestedSetHelperFactory;

    public function __construct(
        Connection $dbConnection,
        EventDispatcherInterface $eventDispatcher,
        InputFilterUtilInterface $inputFilterUtil,
        BackendTreeNodeFactory $backendTreeNodeFactory,
        PortalDomainServiceInterface $portalDomainService,
        TranslatorInterface $translator,
        UrlUtil $urlUtil,
        NestedSetHelperFactoryInterface $nestedSetHelperFactory
    ) {
        $this->dbConnection = $dbConnection;
        $this->eventDispatcher = $eventDispatcher;
        $this->inputFilterUtil = $inputFilterUtil;
        $this->backendTreeNodeFactory = $backendTreeNodeFactory;
        $this->portalDomainService = $portalDomainService;
        $this->translator = $translator;
        $this->urlUtil = $urlUtil;
        $this->nestedSetHelperFactory = $nestedSetHelperFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(IMapperVisitorRestricted $visitor, $cachingEnabled, IMapperCacheTriggerRestricted $cacheTriggerManager)
    {
        $treeTableRecordId = TTools::GetCMSTableId('cms_tree');
        $visitor->SetMappedValue('treeTableID', $treeTableRecordId);

        $treeTableNodeRecordId = TTools::GetCMSTableId('cms_tree_node');
        $visitor->SetMappedValue('treeNodeTableID', $treeTableNodeRecordId);

        $isInIframe = false;
        $inIframe = $this->inputFilterUtil->getFilteredInput('isInIframe');
        if (null !== $inIframe) {
            $isInIframe = true;
        }
        $visitor->SetMappedValue('isInIframe', $isInIframe);

        $showAssignDialog = true;
        if ($this->global->UserDataExists('noassign') && '1' == $this->global->GetUserData('noassign')) {
            $showAssignDialog = false;
        }
        $visitor->SetMappedValue('showAssignDialog', $showAssignDialog);

        $this->currentPageId = null;


        if ($this->global->UserDataExists('id')) {
            $this->currentPageId = $this->global->GetUserData('id');
        }
        $visitor->SetMappedValue('currentPageId', $this->currentPageId);


        $this->primaryConnectedNodeIdOfCurrentPage = null;
        if ($this->global->UserDataExists('primaryTreeNodeId')) {
            $this->primaryConnectedNodeIdOfCurrentPage = $this->global->GetUserData('primaryTreeNodeId');
        }
        $visitor->SetMappedValue('primaryConnectedNodeIdOfCurrentPage', $this->primaryConnectedNodeIdOfCurrentPage);


        $this->rootNode = $this->inputFilterUtil->getFilteredGetInput('rootID', TCMSTreeNode::TREE_ROOT_ID);
        $visitor->SetMappedValue('rootID', $this->rootNode);

        $this->LoadTreeState();
        $this->restrictedNodes = $this->getPortalNavigationStartNodes();

        // Check if we have more than 3 portals (needed because of performance issues in tree pre-rendering)
        $portalList = TdbCmsPortalList::GetList();
        $this->portalCount = $portalList->Length();

        if ($this->global->UserDataExists('table')) {
            $visitor->SetMappedValue('table', $this->global->GetUserData('table'));
        }

        $pageTableId = TTools::GetCMSTableId('cms_tpl_page');

        $url = $this->urlUtil->getArrayAsUrl(array('pagedef' => 'CMSModulePageTree', 'module_fnc' => array('contentmodule' => 'ExecuteAjaxCall'), '_fnc' => 'getTreeNodesJson'), PATH_CMS_CONTROLLER.'?', '&');
        $visitor->SetMappedValue('treeNodesAjaxUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(array('id' => $treeTableNodeRecordId, 'pagedef' => 'tablemanagerframe', 'sRestrictionField' => 'cms_tree_id'), PATH_CMS_CONTROLLER.'?', '&');
        $visitor->SetMappedValue('openPageConnectionListUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(array('tableid' => $pageTableId, 'pagedef' => 'templateengine'), PATH_CMS_CONTROLLER.'?', '&');
        $visitor->SetMappedValue('openPageEditorUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(array('tableid' => $pageTableId, 'pagedef' => 'tableeditor'), PATH_CMS_CONTROLLER.'?', '&');
        $visitor->SetMappedValue('openPageConfigEditorUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(array('tableid' => $treeTableRecordId, 'pagedef' => 'tableeditorPopup'), PATH_CMS_CONTROLLER.'?', '&');
        $visitor->SetMappedValue('openTreeNodeEditorUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(array('tableid' => $treeTableRecordId, 'pagedef' => 'tableeditorPopup', 'module_fnc' => array('contentmodule' => 'Insert')), PATH_CMS_CONTROLLER.'?', '&');
        $visitor->SetMappedValue('openTreeNodeEditorAddNewNodeUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(array('pagedef' => 'CMSModulePageTree', 'module_fnc' => array('contentmodule' => 'ExecuteAjaxCall'), '_fnc' => 'deleteNode', 'tableid' => $treeTableRecordId), PATH_CMS_CONTROLLER.'?', '&');
        $visitor->SetMappedValue('deleteNodeUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(array('tableid' => $treeTableNodeRecordId, 'pagedef' => 'tableeditorPopup', 'sRestrictionField' => 'cms_tree_id', 'module_fnc' => array('contentmodule' => 'Insert'), 'active' => '1', 'preventTemplateEngineRedirect' => '1'), PATH_CMS_CONTROLLER.'?', '&');
        $visitor->SetMappedValue('assignPageUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(array('pagedef' => 'CMSModulePageTree', 'module_fnc' => array('contentmodule' => 'ExecuteAjaxCall'), '_fnc' => 'moveNode', 'tableid' => $treeTableRecordId), PATH_CMS_CONTROLLER.'?', '&');
        $visitor->SetMappedValue('moveNodeUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(array('pagedef' => 'CMSModulePageTree', 'module_fnc' => array('contentmodule' => 'ExecuteAjaxCall'), '_fnc' => 'connectPageToNode', 'tableid' => $treeTableNodeRecordId), PATH_CMS_CONTROLLER.'?', '&');
        $visitor->SetMappedValue('connectPageOnSelectUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(array('pagedef' => 'CMSModulePageTree', 'module_fnc' => array('contentmodule' => 'ExecuteAjaxCall'), '_fnc' => 'disconnectPageFromNode', 'tableid' => $treeTableNodeRecordId), PATH_CMS_CONTROLLER.'?', '&');
        $visitor->SetMappedValue('disconnectPageOnDeselectUrl', $url);


        $backendUser = TCMSUser::GetActiveUser();
        $cacheTriggerManager->addTrigger('cms_user', $backendUser->id);
        $cacheTriggerManager->addTrigger('cms_user_cms_language_mlt', null);
        $cacheTriggerManager->addTrigger('cms_user_cms_portal_mlt', null);
        $cacheTriggerManager->addTrigger('cms_user_cms_role_mlt', null);
        $cacheTriggerManager->addTrigger('cms_user_cms_usergroup_mlt', null);
        $cacheTriggerManager->addTrigger('cms_role_cms_right_mlt', null);
        $cacheTriggerManager->addTrigger('cms_tree', null);
        $cacheTriggerManager->addTrigger('cms_tree_node', null);
    }


    protected function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = array('getTreeNodesJson', 'moveNode', 'deleteNode', 'connectPageToNode', 'disconnectPageFromNode');
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
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
        $currentPageId = $this->inputFilterUtil->getFilteredGetInput('currentPageId');
        if (null !== $currentPageId) {
            $this->currentPageId = $currentPageId;
        }

        $primaryConnectedNodeIdOfCurrentPage = $this->inputFilterUtil->getFilteredGetInput('primaryConnectedNodeIdOfCurrentPage');
        if (null !== $primaryConnectedNodeIdOfCurrentPage) {
            $this->primaryConnectedNodeIdOfCurrentPage = $primaryConnectedNodeIdOfCurrentPage;
        }

        $startNodeId = $this->inputFilterUtil->getFilteredGetInput('id');

        if ('#' === $startNodeId) {
            $treeData = $this->createRootTreeWithDefaultPortalItems();
        } else {
            $treeNode = new TdbCmsTree();
            $treeNode->SetLanguage(TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());
            $treeNode->Load($startNodeId);
            $treeData = $this->createTreeDataModel($treeNode);
        }

        return $treeData;
    }

    protected function createRootTreeWithDefaultPortalItems()
    {
        $rootNodeID = $this->inputFilterUtil->getFilteredGetInput('rootID', TCMSTreeNode::TREE_ROOT_ID);
        $this->rootNode = $rootNodeID;
        $this->LoadTreeState();
        $this->restrictedNodes = $this->getPortalNavigationStartNodes();

        $defaultPortal = $this->portalDomainService->getDefaultPortal();
        $defaultPortalMainNodeId = $defaultPortal->fieldMainNodeTree;

        $rootTreeNode = new TdbCmsTree();
        $rootTreeNode->SetLanguage(TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());
        $rootTreeNode->Load($this->rootNode);

        $rootTreeNodeDataModel = $this->backendTreeNodeFactory->createTreeNodeDataModelFromTreeRecord($rootTreeNode);
        $rootTreeNodeDataModel->setOpened(true);
        $rootTreeNodeDataModel->setType('folderRootRestrictedMenu');
        $rootTreeNodeDataModel->setLiAttr(['class' => 'no-checkbox']);

        $portalList = TdbCmsPortalList::GetList();
        $this->portalCount = $portalList->Length();

        if ($portalList->Length() > 0) {
            while ($portal = $portalList->Next()) {
                $portalId = $portal->fieldMainNodeTree;

                $portalTreeNode = new TdbCmsTree();
                $portalTreeNode->SetLanguage(TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());
                $portalTreeNode->Load($portalId);

                if ($portalId === $defaultPortalMainNodeId) {
                    $portalTreeNodeDataModel = $this->createTreeDataModel($portalTreeNode);
                    $portalTreeNodeDataModel->setOpened(true);
                } else {
                    $portalTreeNodeDataModel = $this->backendTreeNodeFactory->createTreeNodeDataModelFromTreeRecord($portalTreeNode);
                    $portalTreeNodeDataModel->setChildrenAjaxLoad(true);
                    $portalTreeNodeDataModel->setType('folder');
                    if (in_array($portalTreeNode->id, $this->restrictedNodes)) {
                        $typeRestricted = $portalTreeNodeDataModel->getType().'RestrictedMenu';
                        $portalTreeNodeDataModel->setType($typeRestricted);
                    }
                }
                $liAttr = $portalTreeNodeDataModel->getLiAttr();
                $liAttr = array_merge($liAttr, ['class' => 'no-checkbox']);
                $portalTreeNodeDataModel->setLiAttr($liAttr);

                $rootTreeNodeDataModel->addChildren($portalTreeNodeDataModel);
            }
        }

        return $rootTreeNodeDataModel;
    }

    protected function createTreeDataModel(TdbCmsTree $node, $level = 0)
    {
        $treeNodeDataModel = $this->backendTreeNodeFactory->createTreeNodeDataModelFromTreeRecord($node);

        $liAttr = $treeNodeDataModel->getLiAttr();
        $aAttr = [];
        $liClass = '';
        $aClass = '';

        if ('' === $treeNodeDataModel->GetName()) {
            $unnamedRecordTitle = TGlobal::OutHTML($this->translator->trans('chameleon_system_core.text.unnamed_record'));
            $treeNodeDataModel->setName($unnamedRecordTitle);
        }

        $cmsUser = TCMSUser::GetActiveUser();
        $editLanguage = $cmsUser->GetCurrentEditLanguageObject();

        if (true === CMS_TRANSLATION_FIELD_BASED_EMPTY_TRANSLATION_FALLBACK_TO_BASE_LANGUAGE) {
            $cmsConfig = TCMSConfig::GetInstance();
            $defaultLanguage = $cmsConfig->GetFieldTranslationBaseLanguage();
            if (null !== $defaultLanguage && $defaultLanguage->id !== $editLanguage->id) {
                if ('' === $node->sqlData['name__'.$editLanguage->fieldIso6391]) {
                    $treeNodeDataModel->setName($treeNodeDataModel->getName().' <span class="bg-danger px-1"><i class="fas fa-language" title="'.$this->translator->trans('chameleon_system_core.cms_module_table_editor.not_translated').'"></i></span>');
                }
            }
        }

        $children = $node->GetChildren(true);
        if ($children->Length() > 0) {
            $treeNodeDataModel->setType('folder');
        } else {
            $treeNodeDataModel->setType('page');
        }
        if ('' !== $node->sqlData['link']) {
            $treeNodeDataModel->setType('externalLink');
        }

        if (in_array($node->id, $this->restrictedNodes)) {
            $typeRestricted = $treeNodeDataModel->getType().'RestrictedMenu';
            $treeNodeDataModel->setType($typeRestricted);
        }

        $nodeHidden = false;

        $node->SetLanguage($editLanguage->id);
        if (true == $node->fieldHidden) {
            $nodeHidden = true;
            $aClass .= ' node-hidden';
            $treeNodeDataModel->setType('node-hidden');
        }

        $pages = [];

        $connectedPages = $node->GetAllLinkedPages();
        $foundSecuredPage = false;
        while ($connectedPage = $connectedPages->Next()) {
            $pages[] = $connectedPage->id;

            if (false === $nodeHidden
                && false === $foundSecuredPage
                && (true === $connectedPage->fieldExtranetPage
                    && false === $node->fieldShowExtranetPage)) {
                $aClass .= ' page-hidden';
                $treeNodeDataModel->setType('page-hidden');
                $foundSecuredPage = true;
            }

            if (true === $connectedPage->fieldExtranetPage) {
                $treeNodeDataModel->setType('locked');
                $aClass .= ' locked';
            }
        }

        if (count($pages) > 0) {
            if ('folder' === $treeNodeDataModel->getType()) {
                $treeNodeDataModel->setType('folderWithPage');
            }

            $primaryPageID = $node->GetLinkedPage(true);
            if (false !== $primaryPageID) {
                $liAttr = array_merge($liAttr, ['ispageid' => TGlobal::OutHTML($primaryPageID)]);
            }

            // current page is connected to this node
            if ($this->currentPageId && in_array($this->currentPageId, $pages)) {
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

        if (0 == $level) {
            $treeNodeDataModel->setOpened(true);
        }

        $liAttr = array_merge($liAttr, ['class' => $liClass]);
        $treeNodeDataModel->setLiAttr($liAttr);

        $aAttr = array_merge($aAttr, ['class' => $aClass]);
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
    protected function getPortalNavigationStartNodes()
    {
        $portalList = TdbCmsPortalList::GetList(null, TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());

        $restrictedNodes = array();
        while ($portal = $portalList->Next()) {
            $restrictedNodes[] = $portal->fieldMainNodeTree;
            $navigationList = $portal->GetFieldPropertyNavigationsList();
            while ($navigation = $navigationList->Next()) {
                $restrictedNodes[] = $navigation->fieldTreeNode;
            }
        }

        return $restrictedNodes;
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
    protected function getSortFieldName($sTreeId)
    {
        $entrySortField = 'entry_sort';
        if (TdbCmsTree::CMSFieldIsTranslated('entry_sort')) {
            $sLanguagePrefix = TGlobal::GetLanguagePrefix($user = TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());
            if ('' !== $sLanguagePrefix) {
                $entrySortField .= '__'.$sLanguagePrefix;
            }
        }

        return $entrySortField;
    }


    /**
     * moves node to new position and updates all relevant node positions
     * and the parent node connection if changed.
     *
     * @return bool
     */
    public function moveNode()
    {
        $returnVal = false;
        if ($this->global->UserDataExists('tableid') && $this->global->UserDataExists('nodeID') && $this->global->UserDataExists('parentNodeID') && $this->global->UserDataExists('position')) {
            $updatedNodes = array();
            $tableID = $this->global->GetUserData('tableid');
            $nodeID = $this->global->GetUserData('nodeID');
            $parentNodeID = $this->global->GetUserData('parentNodeID');
            $position = $this->global->GetUserData('position');
            if (!empty($tableID) && !empty($nodeID) && 'undefined' != $nodeID && !empty($parentNodeID)) { // prevent saving if node was dropped on empty space instead of an other node
                $tableEditor = new TCMSTableEditorManager();
                $entrySortField = $this->getSortFieldName($parentNodeID);
                $databaseConnection = $this->getDatabaseConnection();
                $quotedTreeTable = $databaseConnection->quoteIdentifier($this->treeTable);
                $quotedParentNodeId = $databaseConnection->quote($parentNodeID);
                if (0 == $position) { // set every other node pos+1
                    $query = "SELECT * FROM $quotedTreeTable WHERE `parent_id` = $quotedParentNodeId";
                    $cmsTreeList = TdbCmsTreeList::GetList($query);
                    while ($cmsTreeNode = &$cmsTreeList->Next()) {
                        $tableEditor->Init($tableID, $cmsTreeNode->id);
                        $newSortOrder = $tableEditor->oTableEditor->oTable->sqlData[$entrySortField] + 1;
                        $tableEditor->SaveField('entry_sort', $newSortOrder);
                        $updatedNodes[] = $cmsTreeNode;
                    }

                    $tableEditor->Init($tableID, $nodeID);
                    $tableEditor->SaveField('entry_sort', 0);
                    $tableEditor->SaveField('parent_id', $parentNodeID);
                } else {
                    $quotedNodeId = $databaseConnection->quote($nodeID);
                    $quotedEntrySortField = $databaseConnection->quoteIdentifier($entrySortField);
                    $query = "SELECT * FROM $quotedTreeTable WHERE `parent_id` = $quotedParentNodeId AND `id` != $quotedNodeId ORDER BY $quotedEntrySortField  ASC";
                    $cmsTreeList = &TdbCmsTreeList::GetList($query, TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());

                    $count = 0;
                    while ($cmsTree = &$cmsTreeList->Next()) {
                        if ($position == $count) { // skip new position of moved node
                            ++$count;
                        }

                        $tableEditor->Init($tableID, $cmsTree->id);
                        $tableEditor->SaveField('entry_sort', $count);
                        ++$count;
                    }

                    $tableEditor->Init($tableID, $nodeID);
                    $tableEditor->SaveField('entry_sort', $position);
                    $tableEditor->SaveField('parent_id', $parentNodeID);
                    $updatedNodes[] = $cmsTree;
                }

                // update cache
                TCacheManager::PerformeTableChange($this->treeTable, $nodeID);
                $this->UpdateSubtreePathCache($nodeID);

                $returnVal = true;
            }

            $node = TdbCmsTree::GetNewInstance($nodeID);
            $this->getNestedSetHelper()->updateNode($node);
            $this->writeSqlLog();

            $updatedNodes[] = $node;
            $event = new ChangeNavigationTreeNodeEvent($updatedNodes);
            $this->eventDispatcher->dispatch(CoreEvents::UPDATE_NAVIGATION_TREE_NODE, $event);
        }

        return $returnVal;
    }

    /**
     * deletes a node and all subnodes.
     *
     * @return mixed - returns false or id of the node that needs to be removed from the html tree
     */
    public function deleteNode()
    {
        $returnVal = false;
        if ($this->global->UserDataExists('tableid') && $this->global->UserDataExists('nodeID')) {
            $tableID = $this->global->GetUserData('tableid');
            $nodeID = $this->global->GetUserData('nodeID');
            $tableEditor = new TCMSTableEditorManager();
            $tableEditor->Init($tableID, $nodeID);
            $returnVal = $nodeID;
            $tableEditor->Delete($nodeID);
        }

        return $returnVal;
    }

    public function connectPageToNode(): string
    {
        if ($this->global->UserDataExists('tableid') && $this->global->UserDataExists('cms_tree_id')) {
            $tableID = $this->global->GetUserData('tableid');
            $nodeID = $this->global->GetUserData('cms_tree_id');
            $connectedPageId = $this->global->GetUserData('contid');
            $tableEditor = new TCMSTableEditorManager();
            $tableEditor->Init($tableID);
            $insertSuccessData = $tableEditor->Insert();
            $recordId = $insertSuccessData->id;

            $postData = [];
            $postData['id'] = $recordId;
            $postData['active'] = '1';
            $postData['cms_tree_id'] = $nodeID;
            $postData['contid'] = $connectedPageId;
            $postData['tbl'] = 'cms_tpl_page';

            $tableEditor->Save($postData);

            return $nodeID;
        }

        return '';
    }

    public function disconnectPageFromNode(): string
    {
        $dbConnection = $this->getDatabaseConnection();

        if ($this->global->UserDataExists('tableid') && $this->global->UserDataExists('contid')) {
            $tableID = $this->global->GetUserData('tableid');
            $nodeID = $this->global->GetUserData('cms_tree_id');
            $contId = $this->global->GetUserData('contid');

            $query = 'SELECT * 
                        FROM `cms_tree_node` 
                       WHERE `cms_tree_node`.`contid` = '.$dbConnection->quote($contId).'
                         AND `cms_tree_node`.`cms_tree_id` = '.$dbConnection->quote($nodeID)."
                         AND `cms_tree_node`.`tbl` = 'cms_tpl_page'
                       ";
            $nodePageConnectionList = TdbCmsTreeNodeList::GetList($query);
            while ($nodePageConnection = $nodePageConnectionList->Next()) {
                $tableEditor = new TCMSTableEditorManager();
                $tableEditor->Init($tableID, $nodePageConnection->id);
                $tableEditor->Delete($nodePageConnection->id);
            }
        }

        return $nodeID;
    }

    /**
     * update the cache of the tree path to each node of the given subtree.
     *
     * @param int $nodeID
     */
    protected function UpdateSubtreePathCache($nodeID)
    {
        $oNode = TdbCmsTree::GetNewInstance();
        $oNode->SetLanguage(TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());
        $oNode->Load($nodeID);
        $oNode->TriggerUpdateOfPathCache();
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

        $cmsUser = &TCMSUser::GetActiveUser();
        $parameters['ouserid'] = $cmsUser->id;
        $parameters['noassign'] = $this->global->GetUserData('noassign');
        $parameters['id'] = $this->global->GetUserData('id');
        $parameters['rootID'] = $this->global->GetUserData('rootID');
        $parameters['table'] = $this->global->GetUserData('table');

        return $parameters;
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
        return $this->nestedSetHelperFactory->createNestedSetHelper($this->treeTable, 'parent_id', 'entry_sort');
    }
}
