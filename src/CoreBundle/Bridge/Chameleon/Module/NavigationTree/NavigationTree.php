<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Bridge\Chameleon\Module\NavigationTree;

use ChameleonSystem\CoreBundle\CoreEvents;
use ChameleonSystem\CoreBundle\DataModel\BackendTreeNodeDataModel;
use ChameleonSystem\CoreBundle\Event\ChangeNavigationTreeNodeEvent;
use ChameleonSystem\CoreBundle\Factory\BackendTreeNodeFactory;
use ChameleonSystem\CoreBundle\Service\FlashMessageService;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\TableEditor\NestedSet\NestedSetHelperFactoryInterface;
use ChameleonSystem\CoreBundle\TableEditor\NestedSet\NestedSetHelperInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use Doctrine\DBAL\Connection;
use esono\pkgCmsCache\CacheInterface;
use MTPkgViewRendererAbstractModuleMapper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Translation\TranslatorInterface;
use TTools;
use TGlobal;

class NavigationTree extends MTPkgViewRendererAbstractModuleMapper
{
    /**
     * The mysql tablename of the tree.
     *
     * @var string
     */
    private $treeTable = 'cms_tree';

    /**
     * @var string
     */
    private $rootNodeId = '';

    /**
     * @var string
     */
    private $currentPageId = '';

    /**
     * @var string
     */
    private $primaryConnectedNodeIdOfCurrentPage = '';

    /**
     * Nodes that should not be assignable or that should have only a
     * restricted context menu.
     *
     * @var array
     */
    private $restrictedNodes = [];

    /**
     * Count of portals in this CMS
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

    /**
     * @var CacheInterface
     */
    private $cache;

    public function __construct(
        Connection $dbConnection,
        EventDispatcherInterface $eventDispatcher,
        InputFilterUtilInterface $inputFilterUtil,
        BackendTreeNodeFactory $backendTreeNodeFactory,
        PortalDomainServiceInterface $portalDomainService,
        TranslatorInterface $translator,
        UrlUtil $urlUtil,
        NestedSetHelperFactoryInterface $nestedSetHelperFactory,
        CacheInterface $cache
    ) {
        $this->dbConnection = $dbConnection;
        $this->eventDispatcher = $eventDispatcher;
        $this->inputFilterUtil = $inputFilterUtil;
        $this->backendTreeNodeFactory = $backendTreeNodeFactory;
        $this->portalDomainService = $portalDomainService;
        $this->translator = $translator;
        $this->urlUtil = $urlUtil;
        $this->nestedSetHelperFactory = $nestedSetHelperFactory;
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(\IMapperVisitorRestricted $visitor, $cachingEnabled, \IMapperCacheTriggerRestricted $cacheTriggerManager)
    {
        $isInIframe = $this->inputFilterUtil->getFilteredInput('isInIframe', '0');
        $visitor->SetMappedValue('isInIframe', $isInIframe);

        $noAssignDialog = $this->inputFilterUtil->getFilteredGetInput('noassign', '0');
        $visitor->SetMappedValue('noAssignDialog', $noAssignDialog);

        $tools = $this->getTools();
        $pageTableId = $tools->GetCMSTableId('cms_tpl_page');
        $treeTableName = 'cms_tree';
        $treeTableRecordId = $tools->GetCMSTableId($treeTableName);
        $treeNodeTableName = 'cms_tree_node';
        $treeNodeTableRecordId = $tools->GetCMSTableId($treeNodeTableName);
        $currentPageId = $this->inputFilterUtil->getFilteredGetInput('id', '');
        $primaryConnectedNodeIdOfCurrentPage = $this->inputFilterUtil->getFilteredGetInput('primaryTreeNodeId', '');
        $this->rootNodeId = $this->inputFilterUtil->getFilteredGetInput('rootID', \TCMSTreeNode::TREE_ROOT_ID);

        $url = $this->urlUtil->getArrayAsUrl(['pagedef' => 'navigationTree', 'module_fnc' => ['contentmodule' => 'ExecuteAjaxCall'], '_fnc' => 'getTreeNodesJson', 'currentPageId' => $currentPageId, 'primaryTreeNodeId' => $primaryConnectedNodeIdOfCurrentPage, 'rootID' => $this->rootNodeId], PATH_CMS_CONTROLLER.'?', '&');
        $visitor->SetMappedValue('treeNodesAjaxUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(['id' => $treeNodeTableRecordId, 'pagedef' => 'tablemanagerframe', 'sRestrictionField' => 'cms_tree_id'], PATH_CMS_CONTROLLER.'?', '&');
        $visitor->SetMappedValue('openPageConnectionListUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(['tableid' => $pageTableId, 'pagedef' => 'templateengine'], PATH_CMS_CONTROLLER.'?', '&');
        $visitor->SetMappedValue('openPageEditorUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(['tableid' => $pageTableId, 'pagedef' => 'tableeditor'], PATH_CMS_CONTROLLER.'?', '&');
        $visitor->SetMappedValue('openPageConfigEditorUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(['tableid' => $treeTableRecordId, 'pagedef' => 'tableeditorPopup'], PATH_CMS_CONTROLLER.'?', '&');
        $visitor->SetMappedValue('openTreeNodeEditorUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(['tableid' => $treeTableRecordId, 'pagedef' => 'tableeditorPopup', 'module_fnc' => ['contentmodule' => 'Insert']], PATH_CMS_CONTROLLER.'?', '&');
        $visitor->SetMappedValue('openTreeNodeEditorAddNewNodeUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(['pagedef' => 'navigationTree', 'module_fnc' => ['contentmodule' => 'ExecuteAjaxCall'], '_fnc' => 'deleteNode', 'tableid' => $treeTableRecordId], PATH_CMS_CONTROLLER.'?', '&');
        $visitor->SetMappedValue('deleteNodeUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(['tableid' => $treeNodeTableRecordId, 'pagedef' => 'tableeditorPopup', 'sRestrictionField' => 'cms_tree_id', 'module_fnc' => ['contentmodule' => 'Insert'], 'active' => '1', 'preventTemplateEngineRedirect' => '1', 'contid' => $currentPageId], PATH_CMS_CONTROLLER.'?', '&');
        $visitor->SetMappedValue('assignPageUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(['pagedef' => 'navigationTree', 'module_fnc' => ['contentmodule' => 'ExecuteAjaxCall'], '_fnc' => 'moveNode', 'table' => $treeTableName], PATH_CMS_CONTROLLER.'?', '&');
        $visitor->SetMappedValue('moveNodeUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(['pagedef' => 'navigationTree', 'module_fnc' => ['contentmodule' => 'ExecuteAjaxCall'], '_fnc' => 'connectPageToNode', 'table' => $treeNodeTableName, 'currentPageId' => $currentPageId], PATH_CMS_CONTROLLER.'?', '&');
        $visitor->SetMappedValue('connectPageOnSelectUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(['pagedef' => 'navigationTree', 'module_fnc' => ['contentmodule' => 'ExecuteAjaxCall'], '_fnc' => 'disconnectPageFromNode', 'table' => $treeNodeTableName, 'currentPageId' => $currentPageId], PATH_CMS_CONTROLLER.'?', '&');
        $visitor->SetMappedValue('disconnectPageOnDeselectUrl', $url);

        $this->setCaching($cachingEnabled, $cacheTriggerManager);
    }

    protected function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = [
            'getTreeNodesJson',
            'moveNode',
            'deleteNode',
            'connectPageToNode',
            'disconnectPageFromNode',
            ];
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    /**
     * Moves node to new position and updates all relevant node positions
     * and the parent node connection if changed.
     */
    public function moveNode(): bool
    {
        $tools = $this->getTools();
        $treeTableName = $this->inputFilterUtil->getFilteredGetInput('table', '');
        $tableId = $tools->GetCMSTableId($treeTableName);
        $nodeId = $this->inputFilterUtil->getFilteredGetInput('nodeId', '');
        $parentNodeId = $this->inputFilterUtil->getFilteredGetInput('parentNodeId', '');
        $position = $this->inputFilterUtil->getFilteredGetInput('position', '');

        if ('' === $treeTableName || '' === $nodeId || '' === $parentNodeId || '' === $position) {
            return false;
        }

        $updatedNodes = [];

        $entrySortField = $this->getSortFieldName();

        $quotedTreeTable = $this->dbConnection->quoteIdentifier($this->treeTable);
        $quotedParentNodeId = $this->dbConnection->quote($parentNodeId);
        if (0 == $position) { // set every other node pos+1
            $query = 'SELECT * FROM '.$quotedTreeTable.' WHERE parent_id = '.$quotedParentNodeId;
            $cmsTreeList = \TdbCmsTreeList::GetList($query);
            while ($cmsTreeNode = $cmsTreeList->Next()) {
                $tableEditor = $tools->GetTableEditorManager($treeTableName, $cmsTreeNode->id);
                $newSortOrder = $tableEditor->oTableEditor->oTable->sqlData[$entrySortField] + 1;
                $tableEditor->SaveField('entry_sort', $newSortOrder);
                $updatedNodes[] = $cmsTreeNode;
            }

            $tableEditor->Init($tableId, $nodeId);
            $tableEditor->SaveField('entry_sort', 0);
            $tableEditor->SaveField('parent_id', $parentNodeId);
        } else {
            $quotedNodeId = $this->dbConnection->quote($nodeId);
            $quotedEntrySortField = $this->dbConnection->quoteIdentifier($entrySortField);
            $query = 'SELECT * FROM '.$quotedTreeTable.' WHERE parent_id = '.$quotedParentNodeId.' AND id != '.$quotedNodeId.' ORDER BY '.$quotedEntrySortField.' ASC';
            $cmsTreeList = \TdbCmsTreeList::GetList($query, \TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());

            $count = 0;
            while ($cmsTree = $cmsTreeList->Next()) {
                if ($position === $count) { // skip new position of moved node
                    ++$count;
                }

                $tableEditor = $tools->GetTableEditorManager($treeTableName, $cmsTree->id);
                $tableEditor->SaveField('entry_sort', $count);
                ++$count;
            }

            $tableEditor->Init($tableId, $nodeId);
            $tableEditor->SaveField('entry_sort', $position);
            $tableEditor->SaveField('parent_id', $parentNodeId);
            $updatedNodes[] = $cmsTree;
        }

        // update cache
        // @todo needed? Should be tested
        // $this->cache->callTrigger($this->treeTable, $nodeId);
        $this->updateSubtreePathCache($nodeId);

        $node = \TdbCmsTree::GetNewInstance($nodeId);
        $this->getNestedSetHelper()->updateNode($node);
        $this->writeSqlLog();

        $updatedNodes[] = $node;
        $event = new ChangeNavigationTreeNodeEvent($updatedNodes);
        $this->eventDispatcher->dispatch(CoreEvents::UPDATE_NAVIGATION_TREE_NODE, $event);

        $messageService = $this->getFlashMessageService();
        if (true === $messageService->consumerHasMessages(\TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER)) {
            $messageService->addMessage(\TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER, 'ERROR_GENERAL');
        }

        return true;
    }

    /**
     * Deletes a node and all subnodes.
     *
     * @return string|null - returns null or id of the node that needs to be removed from the html tree
     */
    public function deleteNode(): ?string
    {
        $tableId = $this->inputFilterUtil->getFilteredGetInput('tableid', '');
        $nodeId = $this->inputFilterUtil->getFilteredGetInput('nodeId', '');

        if ('' === $tableId || '' === $nodeId) {
            return null;
        }

        $tools = $this->getTools();
        $tableEditor = $tools->GetTableEditorManager($this->treeTable, $nodeId);
        $tableEditor->Delete($nodeId);

        return $nodeId;
    }

    public function connectPageToNode(): ?string
    {
        $tools = $this->getTools();
        $treeNodeTableName = $this->inputFilterUtil->getFilteredGetInput('table', '');
        $tableId = $tools->GetCMSTableId($treeNodeTableName);
        $nodeId = $this->inputFilterUtil->getFilteredGetInput('nodeId', '');
        $currentPageId = $this->inputFilterUtil->getFilteredGetInput('currentPageId', '');

        if ('' === $treeNodeTableName || '' === $nodeId || '' === $currentPageId) {
            return null;
        }

        $tableEditor = $tools->GetTableEditorManager($treeNodeTableName);
        $insertSuccessData = $tableEditor->Insert();
        $recordId = $insertSuccessData->id;

        $recordData = [];
        $recordData['tableid'] = $tableId;
        $recordData['id'] = $recordId;
        $recordData['active'] = '1';
        $recordData['start_date'] = '0000-00-00 00:00:00';
        $recordData['end_date'] = '0000-00-00 00:00:00';
        $recordData['cms_tree_id'] = $nodeId;
        $recordData['contid'] = $currentPageId;
        $recordData['tbl'] = 'cms_tpl_page';

        $tableEditor->AllowEditByAll(true);
        $tableEditor->Save($recordData);
        $tableEditor->AllowEditByAll(false);

        return $nodeId;
    }

    public function disconnectPageFromNode(): ?string
    {
        $treeNodeTableName = $this->inputFilterUtil->getFilteredGetInput('table', '');
        $nodeId = $this->inputFilterUtil->getFilteredGetInput('nodeId', '');
        $currentPageId = $this->inputFilterUtil->getFilteredGetInput('currentPageId', '');

        if ('' === $treeNodeTableName || '' === $nodeId || '' === $currentPageId) {
            return null;
        }

        $query = 'SELECT *
                FROM `cms_tree_node`
               WHERE `cms_tree_node`.`contid` = '.$this->dbConnection->quote($currentPageId).'
                 AND `cms_tree_node`.`cms_tree_id` = '.$this->dbConnection->quote($nodeId)."
                 AND `cms_tree_node`.`tbl` = 'cms_tpl_page'
               ";
        $nodePageConnectionList = \TdbCmsTreeNodeList::GetList($query);
        $tools = $this->getTools();
        while ($nodePageConnection = $nodePageConnectionList->Next()) {
            $tableEditor = $tools->GetTableEditorManager($treeNodeTableName, $nodePageConnection->id);
            $tableEditor->Delete($nodePageConnection->id);
        }

        return $nodeId;
    }

    /**
     * Renders all children of a node.
     * Is called via ajax and converted to JSON.
     */
    public function getTreeNodesJson(): array
    {
        $this->currentPageId = $this->inputFilterUtil->getFilteredGetInput('currentPageId', '');
        $this->primaryConnectedNodeIdOfCurrentPage = $this->inputFilterUtil->getFilteredGetInput('primaryTreeNodeId', '');
        $this->rootNodeId = $this->inputFilterUtil->getFilteredGetInput('rootID', \TCMSTreeNode::TREE_ROOT_ID);
        $this->restrictedNodes = $this->getPortalNavigationStartNodes();

        $startNodeId = $this->inputFilterUtil->getFilteredGetInput('id', '#');

        if ('#' === $startNodeId) {  // initial loading
            return $this->createRootTree();
        } else {
            return $this->loadChildrenOfNode($startNodeId);
        }
    }

    private function getPortalBasedRootNodeByPageId(string $pageId): ?string
    {
        $page = \TdbCmsTplPage::GetNewInstance();
        if (false === $page->Load($pageId)) {
            return null;
        }

        $portal = $page->GetPortal();

        return $portal->fieldMainNodeTree;
    }

    private function createRootTree(): array
    {
        $portalBasedRootNodeId = '';
        if ('' !== $this->currentPageId) {
            // If called from page (further page connections).
            $portalBasedRootNodeId = $this->getPortalBasedRootNodeByPageId($this->currentPageId);
        } else if (\TCMSTreeNode::TREE_ROOT_ID !== $this->rootNodeId) {
            // If called from portal record (id is rootID here instead of id for legacy reasons).
            $portalBasedRootNodeId = $this->rootNodeId;
        }

        $treeData = [];
        if ('' === $portalBasedRootNodeId) {
            $rootTreeNode = new \TdbCmsTree();
            $rootTreeNode->SetLanguage(\TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());
            $rootTreeNode->Load($this->rootNodeId);

            $rootTreeNodeDataModel = $this->backendTreeNodeFactory->createTreeNodeDataModelFromTreeRecord($rootTreeNode);
            $rootTreeNodeDataModel->setOpened(true);
            $rootTreeNodeDataModel->setType('folderRootRestrictedMenu');
            $rootTreeNodeDataModel->addListHtmlClass('no-checkbox');

            $defaultPortal = $this->portalDomainService->getDefaultPortal();
            $defaultPortalMainNodeId = $defaultPortal->fieldMainNodeTree;

            $portalList = \TdbCmsPortalList::GetList();
            $this->portalCount = $portalList->Length();

            if ($this->portalCount > 0) {
                while ($portal = $portalList->Next()) {
                    $portalId = $portal->fieldMainNodeTree;
                    $rootTreeNodeDataModel->addChildren($this->getPortalTree($portalId, $defaultPortalMainNodeId));
                }
            }
            array_push($treeData, $rootTreeNodeDataModel);
        } else {
            // Only load the portal of the current page.
            array_push($treeData, $this->getPortalTree($portalBasedRootNodeId, $portalBasedRootNodeId));
        }

        return $treeData;
    }

    private function getPortalTree(string $portalId, string $defaultPortalMainNodeId = ''): BackendTreeNodeDataModel
    {
        $portalTreeNode = new \TdbCmsTree();
        $portalTreeNode->SetLanguage(\TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());
        $portalTreeNode->Load($portalId);

        if ($portalId === $defaultPortalMainNodeId) {
            $portalTreeNodeDataModel = $this->createTreeDataModel($portalTreeNode, 1);
            $portalTreeNodeDataModel->setOpened(true);
        } else {
            $portalTreeNodeDataModel = $this->backendTreeNodeFactory->createTreeNodeDataModelFromTreeRecord($portalTreeNode);
            $portalTreeNodeDataModel->setChildrenAjaxLoad(true);
            $portalTreeNodeDataModel->setType('folder');
            if (true === \in_array($portalTreeNode->id, $this->restrictedNodes)) {
                $typeRestricted = $portalTreeNodeDataModel->getType().'RestrictedMenu';
                $portalTreeNodeDataModel->setType($typeRestricted);
            }
        }
        $portalTreeNodeDataModel->addListHtmlClass('no-checkbox');

        return $portalTreeNodeDataModel;
    }

    private function loadChildrenOfNode(string $startNodeId): array
    {
        $level = 2; // standard is folder or page
        if ($startNodeId === $this->rootNodeId) {
            $level = 1; // children of root are portals
        }

        $node = new \TdbCmsTree();
        $node->SetLanguage(\TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());
        $node->Load($startNodeId);

        $childrenArray = [];
        $children = $node->GetChildren(true);
        while ($child = $children->Next()) {
            array_push($childrenArray, $this->createTreeDataModel($child, $level));
        }

        return $childrenArray;
    }

    private function createTreeDataModel(\TdbCmsTree $node, int $level): BackendTreeNodeDataModel
    {
        $treeNodeDataModel = $this->backendTreeNodeFactory->createTreeNodeDataModelFromTreeRecord($node);

        $treeNodeDataModel->setName($this->translateNodeName($treeNodeDataModel->getName(), $node));
        $this->setTypeAndAttributes($treeNodeDataModel, $node);

        // $level 0 == rootNode, 1 = portal, >2 = folder or page
        if ($level < 2) {
            $treeNodeDataModel->addListHtmlClass('no-checkbox');
            $treeNodeDataModel->setOpened(true);
        }

        ++$level;
        $children = $node->GetChildren(true);
        while ($child = $children->Next()) {
            $childTreeNodeDataModel = $this->createTreeDataModel($child, $level);
            $treeNodeDataModel->addChildren($childTreeNodeDataModel);
        }

        return $treeNodeDataModel;
    }

    private function translateNodeName(string $name, \TdbCmsTree $node): string
    {
        $cmsUser = \TCMSUser::GetActiveUser();
        $editLanguage = $cmsUser->GetCurrentEditLanguageObject();
        $node->SetLanguage($editLanguage->id);

        if ('' === $name) {
            $name = $this->getGlobal()->OutHTML($this->translator->trans('chameleon_system_core.text.unnamed_record'));
        }

        if (false === CMS_TRANSLATION_FIELD_BASED_EMPTY_TRANSLATION_FALLBACK_TO_BASE_LANGUAGE) {
            return $name;
        }

        $cmsConfig = \TCMSConfig::GetInstance();
        $defaultLanguage = $cmsConfig->GetFieldTranslationBaseLanguage();
        if (null !== $defaultLanguage && $defaultLanguage->id !== $editLanguage->id) {
            if ('' === $node->sqlData['name__'.$editLanguage->fieldIso6391]) {
                $name .= ' <span class="bg-danger px-1"><i class="fas fa-language" title="'.$this->translator->trans('chameleon_system_core.cms_module_table_editor.not_translated').'"></i></span>';
            }
        }

        return $name;
    }

    private function setTypeAndAttributes(BackendTreeNodeDataModel $treeNodeDataModel, \TdbCmsTree $node): void
    {
        $treeNodeDataModel->setType($this->setInitialType($node));

        $nodeHidden = false;
        if (true == $node->fieldHidden) {
            $nodeHidden = true;
            $treeNodeDataModel->addLinkHtmlClass('node-hidden');
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
                $treeNodeDataModel->addLinkHtmlClass('page-hidden');
                $treeNodeDataModel->setType('pageHidden');
                $foundSecuredPage = true;
            }

            if (true === $connectedPage->fieldExtranetPage) {
                $treeNodeDataModel->addLinkHtmlClass('locked');
                $treeNodeDataModel->setType('locked');
            }
        }

        if (count($pages) > 0) {
            if ('folder' === $treeNodeDataModel->getType()) {
                $treeNodeDataModel->setType('folderWithPage');
            }

            $primaryPageID = $node->GetLinkedPage(true);
            if (false !== $primaryPageID) {
                $treeNodeDataModel->addListAttribute('isPageId', $primaryPageID);
            }

            // current page is connected to this node
            if ($this->currentPageId && in_array($this->currentPageId, $pages)) {
                $treeNodeDataModel->addLinkHtmlClass('activeConnectedNode');
                $treeNodeDataModel->setOpened(true);
                $treeNodeDataModel->setSelected(true);

                if ($this->primaryConnectedNodeIdOfCurrentPage === $node->id) {
                    $treeNodeDataModel->addLinkHtmlClass('primaryConnectedNode');
                    $treeNodeDataModel->setDisabled(true);
                }
            } else {
                $treeNodeDataModel->addLinkHtmlClass('otherConnectedNode');
            }
        }
    }

    private function setInitialType(\TdbCmsTree $node): string
    {
        $children = $node->GetChildren(true);
        if ($children->Length() > 0) {
            $type = 'folder';
        } else {
            $type = 'page';
        }

        if ('' !== $node->sqlData['link']) {
            $type = 'externalLink';
        }

        if (in_array($node->id, $this->restrictedNodes)) {
            $type .= 'RestrictedMenu';
        }

        if (true == $node->fieldHidden) {
            $type = 'nodeHidden';
        }

        return $type;
    }

    /**
     * Fetches a list of all restricted nodes (of all portals)
     * a restricted node is a startnavigation nodes of a portal.
     */
    private function getPortalNavigationStartNodes(): array
    {
        $portalList = \TdbCmsPortalList::GetList(null, \TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());

        $restrictedNodes = [];
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
     * field is not translatable or field based translation is off.
     */
    private function getSortFieldName(): string
    {
        $entrySortField = 'entry_sort';
        if (\TdbCmsTree::CMSFieldIsTranslated('entry_sort')) {
            $sLanguagePrefix = $this->getGlobal()->GetLanguagePrefix($user = \TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());
            if ('' !== $sLanguagePrefix) {
                $entrySortField .= '__'.$sLanguagePrefix;
            }
        }

        return $entrySortField;
    }

    /**
     * @param bool $chachingEnabled
     */
    private function setCaching($cachingEnabled, \IMapperCacheTriggerRestricted $cacheTriggerManager)
    {
        if (true === $cachingEnabled) {
            $backendUser = \TCMSUser::GetActiveUser();
            $cacheTriggerManager->addTrigger('cms_user', $backendUser->id);
            $cacheTriggerManager->addTrigger('cms_user_cms_language_mlt', null);
            $cacheTriggerManager->addTrigger('cms_user_cms_portal_mlt', null);
            $cacheTriggerManager->addTrigger('cms_user_cms_role_mlt', null);
            $cacheTriggerManager->addTrigger('cms_user_cms_usergroup_mlt', null);
            $cacheTriggerManager->addTrigger('cms_role_cms_right_mlt', null);
            $cacheTriggerManager->addTrigger('cms_tree', null);
            $cacheTriggerManager->addTrigger('cms_tree_node', null);
        }
    }

    /**
     * Update the cache of the tree path to each node of the given subtree.
     */
    private function updateSubtreePathCache(string $nodeId): void
    {
        $oNode = \TdbCmsTree::GetNewInstance();
        $oNode->SetLanguage(\TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());
        $oNode->Load($nodeId);
        $oNode->TriggerUpdateOfPathCache();
    }

    /**
     * {@inheritdoc}
     */
    public function GetHtmlHeadIncludes()
    {
        $includes = parent::GetHtmlHeadIncludes();
        $includes[] = sprintf('<script src="%s" type="text/javascript"></script>', $this->getGlobal()->GetStaticURLToWebLib('/javascript/jsTree/3.3.8/jstree.js'));
        $includes[] = sprintf('<script src="%s" type="text/javascript"></script>', $this->getGlobal()->GetStaticURLToWebLib('/javascript/navigationTree.js'));
        $includes[] = sprintf('<script src="%s" type="text/javascript"></script>', $this->getGlobal()->GetStaticURLToWebLib('/javascript/jquery/cookie/jquery.cookie.js'));
        $includes[] = sprintf('<link rel="stylesheet" href="%s">', $this->getGlobal()->GetStaticURLToWebLib('/javascript/jsTree/3.3.8/themes/default/style.css'));
        $includes[] = sprintf('<link rel="stylesheet" href="%s">', $this->getGlobal()->GetStaticURLToWebLib('/javascript/jsTree/customStyles/style.css'));

        return $includes;
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

        if (false === is_array($parameters)) {
            $parameters = [];
        }

        $cmsUser = \TCMSUser::GetActiveUser();
        $parameters['ouserid'] = $cmsUser->id;
        $parameters['noassign'] = $this->inputFilterUtil->getFilteredGetInput('noassign');
        $parameters['id'] = $this->inputFilterUtil->getFilteredGetInput('id');
        $parameters['rootID'] = $this->inputFilterUtil->getFilteredGetInput('rootID');
        $parameters['table'] = $this->inputFilterUtil->getFilteredGetInput('table');

        return $parameters;
    }

    private function writeSqlLog(): void
    {
        $command = <<<COMMAND
\TCMSLogChange::initializeNestedSet('{$this->treeTable}', 'parent_id', 'entry_sort');
COMMAND;
        \TCMSLogChange::WriteSqlTransactionWithPhpCommands('update nested set for table '.$this->treeTable, [$command]);
    }

    /**
     * @return NestedSetHelperInterface
     */
    protected function getNestedSetHelper()
    {
        return $this->nestedSetHelperFactory->createNestedSetHelper($this->treeTable, 'parent_id', 'entry_sort');
    }

    /**
     * @return TTools
     */
    protected function getTools()
    {
        return ServiceLocator::get('chameleon_system_core.tools');
    }

    /**
     * @return FlashMessageService
     */
    private function getFlashMessageService()
    {
        return ServiceLocator::get('chameleon_system_core.flash_messages');
    }

    /**
     * @return TGlobal
     */
    private function getGlobal()
    {
        return ServiceLocator::get('chameleon_system_core.global');
    }
}
