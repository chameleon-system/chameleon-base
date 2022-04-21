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
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\TableEditor\NestedSet\NestedSetHelperFactoryInterface;
use ChameleonSystem\CoreBundle\TableEditor\NestedSet\NestedSetHelperInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\CoreBundle\Util\FieldTranslationUtil;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use Doctrine\DBAL\Connection;
use MTPkgViewRendererAbstractModuleMapper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use TGlobal;
use TTools;

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
    private $treeTableSortField = 'entry_sort';

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
    private $fieldName = '';

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
     * @var TTools
     */
    private $tools;

    /**
     * @var TGlobal
     */
    private $global;

    /**
     * @var FieldTranslationUtil
     */
    private $fieldTranslationUtil;

    /**
     * @var \TdbCmsLanguage
     */
    private $editLanguage;

    public function __construct(
        Connection $dbConnection,
        EventDispatcherInterface $eventDispatcher,
        InputFilterUtilInterface $inputFilterUtil,
        BackendTreeNodeFactory $backendTreeNodeFactory,
        PortalDomainServiceInterface $portalDomainService,
        TranslatorInterface $translator,
        UrlUtil $urlUtil,
        NestedSetHelperFactoryInterface $nestedSetHelperFactory,
        TTools $tools,
        TGlobal $global,
        FieldTranslationUtil $fieldTranslationUtil,
        LanguageServiceInterface $languageService
    ) {
        parent::__construct();

        $this->dbConnection = $dbConnection;
        $this->eventDispatcher = $eventDispatcher;
        $this->inputFilterUtil = $inputFilterUtil;
        $this->backendTreeNodeFactory = $backendTreeNodeFactory;
        $this->portalDomainService = $portalDomainService;
        $this->translator = $translator;
        $this->urlUtil = $urlUtil;
        $this->nestedSetHelperFactory = $nestedSetHelperFactory;
        $this->tools = $tools;
        $this->global = $global;
        $this->fieldTranslationUtil = $fieldTranslationUtil;

        $this->editLanguage = $languageService->getActiveEditLanguage();
    }

    /**
     * {@inheritDoc}
     */
    public function Accept(\IMapperVisitorRestricted $visitor, $cachingEnabled, \IMapperCacheTriggerRestricted $cacheTriggerManager)
    {
        $isInIframe = $this->inputFilterUtil->getFilteredInput('isInIframe', '0');
        $visitor->SetMappedValue('isInIframe', $isInIframe);

        $this->fieldName = $this->inputFilterUtil->getFilteredGetInput('fieldName', '');
        $visitor->SetMappedValue('fieldName', $this->fieldName);

        $noAssignDialog = $this->inputFilterUtil->getFilteredGetInput('noassign', '0');
        $visitor->SetMappedValue('noAssignDialog', $noAssignDialog);

        $pageTableId = $this->tools->GetCMSTableId('cms_tpl_page');
        $treeTableName = 'cms_tree';
        $treeTableRecordId = $this->tools->GetCMSTableId($treeTableName);
        $treeNodeTableName = 'cms_tree_node';
        $treeNodeTableRecordId = $this->tools->GetCMSTableId($treeNodeTableName);
        $currentPageId = $this->inputFilterUtil->getFilteredGetInput('id', '');
        $primaryConnectedNodeIdOfCurrentPage = $this->inputFilterUtil->getFilteredGetInput('primaryTreeNodeId', '');
        $this->rootNodeId = $this->inputFilterUtil->getFilteredGetInput('rootID', \TCMSTreeNode::TREE_ROOT_ID);
        if ('' !== $currentPageId) {
            $rootNode = new \TdbCmsTree();
            $rootNode->SetLanguage(\TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());
            $rootNode->Load($this->rootNodeId);
            $visitor->SetMappedValue('pageBreadcrumbsHTML', $this->createPageBreadcrumbs($rootNode));
        }


        $url = $this->urlUtil->getArrayAsUrl(
            [
                'pagedef' => 'navigationTree',
                'module_fnc' =>
                    [
                        'contentmodule' => 'ExecuteAjaxCall'
                    ],
                '_fnc' => 'getTreeNodes',
                'currentPageId' => $currentPageId,
                'primaryTreeNodeId' => $primaryConnectedNodeIdOfCurrentPage,
                'rootID' => $this->rootNodeId,
            ],
            PATH_CMS_CONTROLLER.'?',
            '&'
        );
        $visitor->SetMappedValue('treeNodesAjaxUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(
            [
                'id' => $treeNodeTableRecordId,
                'pagedef' => 'tablemanagerframe',
                'sRestrictionField' => 'cms_tree_id',
            ],
            PATH_CMS_CONTROLLER.'?',
            '&'
        );
        $visitor->SetMappedValue('openPageConnectionListUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(
            [
                'tableid' => $pageTableId,
                'pagedef' => 'templateengine',
            ],
            PATH_CMS_CONTROLLER.'?',
            '&'
        );
        $visitor->SetMappedValue('openPageEditorUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(
            [
                'tableid' => $pageTableId,
                'pagedef' => 'tableeditor'
            ],
            PATH_CMS_CONTROLLER.'?',
            '&'
        );
        $visitor->SetMappedValue('openPageConfigEditorUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(
            [
                'tableid' => $treeTableRecordId,
                'pagedef' => 'tableeditorPopup'
            ],
            PATH_CMS_CONTROLLER.'?',
            '&'
        );
        $visitor->SetMappedValue('openTreeNodeEditorUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(
            [
                'tableid' => $treeTableRecordId,
                'pagedef' => 'tableeditorPopup',
                'module_fnc' =>
                    [
                        'contentmodule' => 'Insert'
                    ]
            ],
            PATH_CMS_CONTROLLER.'?',
            '&'
        );
        $visitor->SetMappedValue('openTreeNodeEditorAddNewNodeUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(
            [
                'pagedef' => 'navigationTree',
                'module_fnc' =>
                    [
                        'contentmodule' => 'ExecuteAjaxCall'
                    ],
                '_fnc' => 'deleteNode',
                'tableid' => $treeTableRecordId
            ],
            PATH_CMS_CONTROLLER . '?',
            '&'
        );
        $visitor->SetMappedValue('deleteNodeUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(
            [
                'tableid' => $treeNodeTableRecordId,
                'pagedef' => 'tableeditorPopup',
                'sRestrictionField' => 'cms_tree_id',
                'module_fnc' =>
                    [
                        'contentmodule' => 'Insert'
                    ],
                'active' => '1',
                'preventTemplateEngineRedirect' => '1',
                'contid' => $currentPageId
            ],
            PATH_CMS_CONTROLLER.'?',
            '&'
        );
        $visitor->SetMappedValue('assignPageUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(
            [
                'pagedef' => 'navigationTree',
                'module_fnc' =>
                    [
                        'contentmodule' => 'ExecuteAjaxCall'
                    ],
                '_fnc' => 'moveNode',
                'table' => $treeTableName
            ],
            PATH_CMS_CONTROLLER.'?',
            '&'
        );
        $visitor->SetMappedValue('moveNodeUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(
            [
                'pagedef' => 'navigationTree',
                'module_fnc' =>
                    [
                        'contentmodule' => 'ExecuteAjaxCall'
                    ],
                '_fnc' => 'connectPageToNode',
                'table' => $treeNodeTableName,
                'currentPageId' => $currentPageId
            ],
            PATH_CMS_CONTROLLER.'?',
            '&'
        );
        $visitor->SetMappedValue('connectPageOnSelectUrl', $url);

        $url = $this->urlUtil->getArrayAsUrl(
            [
                'pagedef' => 'navigationTree',
                'module_fnc' =>
                    [
                        'contentmodule' => 'ExecuteAjaxCall'
                    ],
                '_fnc' => 'disconnectPageFromNode',
                'table' => $treeNodeTableName,
                'currentPageId' => $currentPageId
            ],
            PATH_CMS_CONTROLLER.'?',
            '&'
        );
        $visitor->SetMappedValue('disconnectPageOnDeselectUrl', $url);

        if (true === $cachingEnabled) {
            $this->addCachingTriggers($cacheTriggerManager);
        }
    }

    /**
     * @param string $path
     */
    private function createPageBreadcrumbs(\TdbCmsTree $node, $path = ''): string
    {
        $path .= '<li class="breadcrumb-item">'.$node->fieldName.'</li>';
        $pageBreadcrumbsHTML = '<div id="'.$this->fieldName.'_tmp_path_'.$node->id.'" style="display:none;"><ol class="breadcrumb pl-0"><li class="breadcrumb-item"><i class="fas fa-sitemap"></i></li>'.$path.'</ol></div>'."\n";

        $children = $node->GetChildren(true);
        while ($child = $children->Next()) {
            $pageBreadcrumbsHTML .= $this->createPageBreadcrumbs($child, $path);
        }

        return $pageBreadcrumbsHTML;
    }

    /**
     * {@inheritdoc}
     */
    protected function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions =
            [
                'getTreeNodes',
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
    protected function moveNode(): bool
    {
        $treeTableName = $this->inputFilterUtil->getFilteredGetInput('table', '');
        $tableId = $this->tools->GetCMSTableId($treeTableName);
        $nodeId = $this->inputFilterUtil->getFilteredGetInput('nodeId', '');
        $parentNodeId = $this->inputFilterUtil->getFilteredGetInput('parentNodeId', '');
        $position = (int)($this->inputFilterUtil->getFilteredGetInput('position', '0'));

        if ('' === $treeTableName || '' === $tableId || '' === $nodeId || '' === $parentNodeId || '' === $position) {
            return false;
        }

        $updatedNodes = [];

        $quotedTreeTable = $this->dbConnection->quoteIdentifier($this->treeTable);
        $quotedParentNodeId = $this->dbConnection->quote($parentNodeId);
        if (0 === $position) { // set every other node pos+1
            $query = 'SELECT * FROM '.$quotedTreeTable.' WHERE parent_id = '.$quotedParentNodeId;
            $cmsTreeList = \TdbCmsTreeList::GetList($query);
            while ($cmsTreeNode = $cmsTreeList->Next()) {
                $tableEditor = $this->tools->GetTableEditorManager($treeTableName, $cmsTreeNode->id);
                $newSortOrder = $tableEditor->oTableEditor->oTable->sqlData[$this->treeTableSortField] + 1;
                $tableEditor->SaveField($this->treeTableSortField, $newSortOrder);
                $updatedNodes[] = $cmsTreeNode;
            }
        } else {
            $quotedNodeId = $this->dbConnection->quote($nodeId);
            $quotedEntrySortField = $this->dbConnection->quoteIdentifier($this->treeTableSortField);
            $query = 'SELECT * FROM '.$quotedTreeTable.' WHERE parent_id = '.$quotedParentNodeId.' AND id != '.$quotedNodeId.' ORDER BY '.$quotedEntrySortField.' ASC';
            $cmsTreeList = \TdbCmsTreeList::GetList($query);

            $count = 0;
            while ($cmsTree = $cmsTreeList->Next()) {
                if ($position === $count) { // skip new position of moved node
                    ++$count;
                }

                $tableEditor = $this->tools->GetTableEditorManager($treeTableName, $cmsTree->id);
                $tableEditor->SaveField($this->treeTableSortField, $count);
                ++$count;
            }
            $updatedNodes[] = $cmsTree;
        }

        $tableEditor = $this->tools->GetTableEditorManager($treeTableName, $nodeId);
        $tableEditor->SaveField($this->treeTableSortField, $position);
        $tableEditor->SaveField('parent_id', $parentNodeId);

        $this->updateSubtreePathCache($nodeId);

        $node = \TdbCmsTree::GetNewInstance($nodeId);
        $this->getNestedSetHelper()->updateNode($node);
        $this->writeSqlLog();

        $updatedNodes[] = $node;
        $event = new ChangeNavigationTreeNodeEvent($updatedNodes);
        $this->eventDispatcher->dispatch($event, CoreEvents::UPDATE_NAVIGATION_TREE_NODE);

        return true;
    }

    /**
     * Deletes a node and all subnodes.
     *
     * @return string|null - id of the node that needs to be removed from the html tree or null in case of error
     */
    protected function deleteNode(): ?string
    {
        /** @var string|null $tableId */
        $tableId = $this->inputFilterUtil->getFilteredGetInput('tableid', '');
        /** @var string|null $nodeId */
        $nodeId = $this->inputFilterUtil->getFilteredGetInput('nodeId', '');

        if ('' === $tableId || '' === $nodeId) {
            return null;
        }

        $tableEditor = $this->tools->GetTableEditorManager($this->treeTable, $nodeId);
        $tableEditor->Delete($nodeId);

        return $nodeId;
    }

    protected function connectPageToNode(): ?string
    {
        /** @var string|null $treeNodeTableName */
        $treeNodeTableName = $this->inputFilterUtil->getFilteredGetInput('table', '');
        /** @var string|null $tableId */
        $tableId = $this->tools->GetCMSTableId($treeNodeTableName);
        /** @var string|null $nodeId */
        $nodeId = $this->inputFilterUtil->getFilteredGetInput('nodeId', '');
        /** @var string|null $currentPageId */
        $currentPageId = $this->inputFilterUtil->getFilteredGetInput('currentPageId', '');

        if ('' === $treeNodeTableName || '' === $nodeId || '' === $currentPageId) {
            return null;
        }

        $tableEditor = $this->tools->GetTableEditorManager($treeNodeTableName);
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

    protected function disconnectPageFromNode(): ?string
    {
        /** @var string|null $treeNodeTableName */
        $treeNodeTableName = $this->inputFilterUtil->getFilteredGetInput('table', '');
        /** @var string|null $nodeId */
        $nodeId = $this->inputFilterUtil->getFilteredGetInput('nodeId', '');
        /** @var string|null $currentPageId */
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
        while ($nodePageConnection = $nodePageConnectionList->Next()) {
            $tableEditor = $this->tools->GetTableEditorManager($treeNodeTableName, $nodePageConnection->id);
            $tableEditor->Delete($nodePageConnection->id);
        }

        return $nodeId;
    }

    /**
     * Is called via ajax.
     * Returns requested treeNode or rootTreeNode (id = "#") with all its children.
     * The return value is converted to JSON with array brackets around it, jstree.js needs it that way
     */
    protected function getTreeNodes(): array
    {
        $this->currentPageId = $this->inputFilterUtil->getFilteredGetInput('currentPageId', '');
        $this->primaryConnectedNodeIdOfCurrentPage = $this->inputFilterUtil->getFilteredGetInput('primaryTreeNodeId', '');
        $this->rootNodeId = $this->inputFilterUtil->getFilteredGetInput('rootID', \TCMSTreeNode::TREE_ROOT_ID);
        $this->restrictedNodes = $this->getPortalNavigationStartNodes();

        $startNodeId = $this->inputFilterUtil->getFilteredGetInput('id', '#');

        if ('#' === $startNodeId) {
            return $this->createRootTree();
        } else {
            return $this->loadChildrenOfNode($startNodeId);
        }
    }

    private function getPortalRootNode(string $pageId): ?string
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
        $treeData = [];

        // menu item "Navigation" was called
        if ('' === $this->currentPageId && $this->rootNodeId  === \TCMSTreeNode::TREE_ROOT_ID) {
            $rootTreeNode = new \TdbCmsTree();
            $rootTreeNode->SetLanguage($this->editLanguage->id);
            $rootTreeNode->Load($this->rootNodeId);

            $rootTreeNodeDataModel = $this->backendTreeNodeFactory->createTreeNodeDataModelFromTreeRecord($rootTreeNode);
            $rootTreeNodeDataModel->setOpened(true);
            $rootTreeNodeDataModel->setType('folderRootRestrictedMenu');
            $rootTreeNodeDataModel->addListHtmlClass('no-checkbox');

            $defaultPortal = $this->portalDomainService->getDefaultPortal();
            $defaultPortalMainNodeId = $defaultPortal->fieldMainNodeTree;

            $orderedPortalsQuery = '
                SELECT `cms_portal`.* FROM `cms_portal` 
                    LEFT JOIN `cms_tree` ON `cms_portal`.`main_node_tree` = `cms_tree`.`id` 
                ORDER BY `cms_tree`.`lft`, `cms_portal`.`sort_order`
            ';
            $portalList = \TdbCmsPortalList::GetList($orderedPortalsQuery);

            while ($portal = $portalList->Next()) {
                $portalId = $portal->fieldMainNodeTree;
                $rootTreeNodeDataModel->addChildren($this->getPortalTree($portalId, $defaultPortalMainNodeId));
            }
            $treeData[] = $rootTreeNodeDataModel;
        } else {
            $portalBasedRootNodeId = $this->getPortalBasedRootNodeId();
            // Only load the portal of the current page or current portal
            $treeData[] = $this->getPortalTree($portalBasedRootNodeId, $portalBasedRootNodeId);
        }

        return $treeData;
    }

    /**
     * @return null|string
     */
    private function getPortalBasedRootNodeId(): ?string
    {
        if ('' !== $this->currentPageId) {
            // If called from page (further page connections).
            return $this->getPortalRootNode($this->currentPageId);
        } else {
            // If called from portal record (id is rootID here instead of id for legacy reasons).
            return $this->rootNodeId;
        }
    }


    private function getPortalTree(string $portalId, string $defaultPortalMainNodeId = ''): BackendTreeNodeDataModel
    {
        $portalTreeNode = new \TdbCmsTree();
        $portalTreeNode->SetLanguage($this->editLanguage->id);
        $portalTreeNode->Load($portalId);

        if ($portalId === $defaultPortalMainNodeId) {
            $portalTreeNodeDataModel = $this->createTreeDataModel($portalTreeNode, 1);
            $portalTreeNodeDataModel->setOpened(true);
        } else {
            $portalTreeNodeDataModel = $this->backendTreeNodeFactory->createTreeNodeDataModelFromTreeRecord($portalTreeNode);
            $portalTreeNodeDataModel->setChildrenAjaxLoad(true);
            $portalTreeNodeDataModel->setType('folder');
            if (true === in_array($portalTreeNode->id, $this->restrictedNodes)) {
                $typeRestricted = $portalTreeNodeDataModel->getType().'RestrictedMenu';
                $portalTreeNodeDataModel->setType($typeRestricted);
            }
        }
        if ('' !== $this->currentPageId) {
            $portalTreeNodeDataModel->setDisabled(true);
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
        $node->SetLanguage($this->editLanguage->id);
        $node->Load($startNodeId);

        $childrenArray = [];
        $children = $node->GetChildren(true, $this->editLanguage->id);
        while ($child = $children->Next()) {
            $childrenArray[] = $this->createTreeDataModel($child, $level);
        }

        return $childrenArray;
    }

    private function createTreeDataModel(\TdbCmsTree $node, int $level): BackendTreeNodeDataModel
    {
        $treeNodeDataModel = $this->backendTreeNodeFactory->createTreeNodeDataModelFromTreeRecord($node);

        $treeNodeDataModel->setName($this->translateNodeName($treeNodeDataModel->getName(), $node));
        $this->setTypeAndAttributes($treeNodeDataModel, $node);

        // $level 0 == rootNode, 1 = portal, 2 = Navigations (top, main, footer, system),  >2 = folder or page
        if ($level <= 2) {
            if ('' !== $this->currentPageId) {
                $treeNodeDataModel->setDisabled(true);
            }
            $treeNodeDataModel->addListHtmlClass('no-checkbox');
            $treeNodeDataModel->setOpened(true);
        }

        ++$level;

        $children = $node->GetChildren(true, $this->editLanguage->id);
        while ($child = $children->Next()) {
            $childTreeNodeDataModel = $this->createTreeDataModel($child, $level);
            $treeNodeDataModel->addChildren($childTreeNodeDataModel);
        }

        return $treeNodeDataModel;
    }

    private function translateNodeName(string $name, \TdbCmsTree $node): string
    {
        $node->SetLanguage($this->editLanguage->id);

        if ('' === $name) {
            $name = $this->global->OutHTML($this->translator->trans('chameleon_system_core.text.unnamed_record'));
        }

        if (false === CMS_TRANSLATION_FIELD_BASED_EMPTY_TRANSLATION_FALLBACK_TO_BASE_LANGUAGE) {
            return $name;
        }

        if ($this->fieldTranslationUtil->isTranslationNeeded($this->editLanguage)) {
            $nodeNameFieldName = $this->fieldTranslationUtil->getTranslatedFieldName($this->treeTable, 'name', $this->editLanguage);

            if ('' === $node->sqlData[$nodeNameFieldName]) {
                $name .= ' <span class="bg-danger px-1"><i class="fas fa-language" title="' . $this->translator->trans('chameleon_system_core.cms_module_table_editor.not_translated') . '"></i></span>';
            }
        }

        return $name;
    }

    /**
     * @return void
     */
    private function setTypeAndAttributes(BackendTreeNodeDataModel $treeNodeDataModel, \TdbCmsTree $node)
    {
        $treeNodeDataModel->setType('');

        $children = $node->GetChildren(true);
        if ($children->Length() > 0) {
            $treeNodeDataModel->setType('folder');
            if (true === in_array($node->id, $this->restrictedNodes)) {
                $treeNodeDataModel->setType('folderRestrictedMenu');
            }
        }

        if (true === $node->fieldHidden) {
            $this->addIconToTreeNode($treeNodeDataModel, 'nodeHidden', 'fas fa-eye-slash');
            $treeNodeDataModel->addLinkHtmlClass('node-hidden');
        }

        if ('' !== $node->sqlData['link']) {
            $this->addIconToTreeNode($treeNodeDataModel, 'externalLink', 'fas fa-external-link-alt');
        }

        $linkedPageOfNode = $node->GetLinkedPageObject(true);

        if (false === $linkedPageOfNode) {
            if ('' === $treeNodeDataModel->getType()) {
                $this->addIconToTreeNode($treeNodeDataModel, 'noPage', 'fas fa-genderless');
            }
            return;
        }

        $this->addIconToTreeNode($treeNodeDataModel, 'page', 'far fa-file');
        $treeNodeDataModel->addListAttribute('isPageId', $linkedPageOfNode->id);

        if (true === $linkedPageOfNode->fieldExtranetPage) {
            $treeNodeDataModel->addLinkHtmlClass('locked');
            $this->addIconToTreeNode($treeNodeDataModel, 'locked', 'fas fa-user-lock');
            if (false === $node->fieldShowExtranetPage) {
                $this->addIconToTreeNode($treeNodeDataModel, 'extranetpageHidden', 'far fa-eye-slash');
                $treeNodeDataModel->addLinkHtmlClass('extranetpage-hidden');
            }
        }

        // current page is connected to this node
        if ($this->currentPageId === $linkedPageOfNode->id) {
            $treeNodeDataModel->addListHtmlClass('activeConnectedNode');
            $treeNodeDataModel->setOpened(true);
            $treeNodeDataModel->setSelected(true);

            if ($this->primaryConnectedNodeIdOfCurrentPage === $node->id) {
                $treeNodeDataModel->addListHtmlClass('primaryConnectedNodeOfCurrentPage');
                if ('' !== $this->currentPageId) {
                    $treeNodeDataModel->setDisabled(true);
                    $treeNodeDataModel->addListHtmlClass('no-checkbox');
                }
            }
        } else {
            $treeNodeDataModel->addLinkHtmlClass('otherConnectedNode');
            if ('' !== $this->currentPageId) {
                $treeNodeDataModel->setDisabled(true);
                $treeNodeDataModel->addListHtmlClass('no-checkbox');
            }
        }
    }

    /**
     * @return void
     */
    private function addIconToTreeNode (BackendTreeNodeDataModel $treeNodeDataModel, string $type, string $fontawesomeIcon) {
        if ('' === $treeNodeDataModel->getType()) {
            $treeNodeDataModel->setType($type);
        } else {
            $treeNodeDataModel->addFurtherIconHTML('<i class="'. $fontawesomeIcon .' mr-2"></i>');
        }
    }

    /**
     * Fetches a list of all restricted nodes (of all portals).
     * A restricted node is a start navigation node.
     */
    private function getPortalNavigationStartNodes(): array
    {
        $portalList = \TdbCmsPortalList::GetList(null, $this->editLanguage->id);

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
     * @param bool $chachingEnabled
     *
     * @return void
     */
    private function addCachingTriggers(\IMapperCacheTriggerRestricted $cacheTriggerManager)
    {
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

    /**
     * Update the cache of the tree path to each node of the given subtree.
     */
    private function updateSubtreePathCache(string $nodeId): void
    {
        $oNode = \TdbCmsTree::GetNewInstance();
        $oNode->SetLanguage($this->editLanguage->id);
        $oNode->Load($nodeId);
        $oNode->TriggerUpdateOfPathCache();
    }

    /**
     * {@inheritdoc}
     */
    public function GetHtmlHeadIncludes()
    {
        $includes = parent::GetHtmlHeadIncludes();
        $includes[] = sprintf('<script src="%s" type="text/javascript"></script>', $this->global->GetStaticURLToWebLib('/javascript/jsTree/3.3.8/jstree.js'));
        $includes[] = sprintf('<script src="%s" type="text/javascript"></script>', $this->global->GetStaticURLToWebLib('/javascript/navigationTree.js'));
        $includes[] = sprintf('<script src="%s" type="text/javascript"></script>', $this->global->GetStaticURLToWebLib('/javascript/jquery/cookie/jquery.cookie.js'));
        $includes[] = sprintf('<link rel="stylesheet" href="%s">', $this->global->GetStaticURLToWebLib('/javascript/jsTree/3.3.8/themes/default/style.css'));
        $includes[] = sprintf('<link rel="stylesheet" href="%s">', $this->global->GetStaticURLToWebLib('/javascript/jsTree/customStyles/style.css'));

        return $includes;
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

    private function getNestedSetHelper(): NestedSetHelperInterface
    {
        return $this->nestedSetHelperFactory->createNestedSetHelper($this->treeTable, 'parent_id', 'entry_sort');
    }
}
