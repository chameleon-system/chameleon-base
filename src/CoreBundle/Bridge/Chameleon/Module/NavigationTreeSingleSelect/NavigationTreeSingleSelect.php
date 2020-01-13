<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Bridge\Chameleon\Module\NavigationTreeSingleSelect;

use ChameleonSystem\CoreBundle\DataModel\BackendTreeNodeDataModel;
use ChameleonSystem\CoreBundle\Factory\BackendTreeNodeFactory;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use Doctrine\DBAL\Connection;
use MTPkgViewRendererAbstractModuleMapper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use TGlobal;

class NavigationTreeSingleSelect extends MTPkgViewRendererAbstractModuleMapper
{
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
     * @var string
     */
    private $treePathHTML = '';

    /**
     * Nodes that should not be assignable or that should have only a
     * restricted context menu.
     *
     * @var array
     */
    protected $restrictedNodes = [];

    /**
     * @var TGlobal
     */
    private $global;

    public function __construct(
        Connection $dbConnection,
        EventDispatcherInterface $eventDispatcher,
        InputFilterUtilInterface $inputFilterUtil,
        BackendTreeNodeFactory $backendTreeNodeFactory,
        TGlobal $global
    ) {
        $this->dbConnection = $dbConnection;
        $this->eventDispatcher = $eventDispatcher;
        $this->inputFilterUtil = $inputFilterUtil;
        $this->backendTreeNodeFactory = $backendTreeNodeFactory;
        $this->global = $global;
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(\IMapperVisitorRestricted $visitor, $cachingEnabled, \IMapperCacheTriggerRestricted $cacheTriggerManager)
    {
        $fieldName = $this->inputFilterUtil->getFilteredGetInput('fieldName', '');
        $activeNodeId = $this->inputFilterUtil->getFilteredGetInput('id', '');
        $portalSelect = $this->inputFilterUtil->getFilteredGetInput('portalSelect', 0);
        $isPortalSelectMode = $portalSelect === '1' ? true : false;

        $this->restrictedNodes = $this->getPortalNavigationStartNodes();

        $rootTreeId = $this->getPortalTreeRootNodeId();
        if ('' !== $rootTreeId) {
            $rootNode = new \TdbCmsTree();
            $rootNode->SetLanguage(\TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());
            $rootNode->Load($rootTreeId);

            $treeNodes = $this->createTreeDataModel($rootNode, $fieldName);
            $visitor->SetMappedValue('treeNodes', $treeNodes);
        }
        $visitor->SetMappedValue('treePathHTML', $this->treePathHTML);
        $visitor->SetMappedValue('activeId', $activeNodeId);
        $visitor->SetMappedValue('fieldName', $fieldName);
        $visitor->SetMappedValue('level', 0);
        $visitor->SetMappedValue('isPortalSelectMode', $isPortalSelectMode);
    }

    private function getPortalTreeRootNodeId(): string
    {
        $portalId = $this->inputFilterUtil->getFilteredGetInput('portalID', '');

        if ('' === $portalId) {
            return \TCMSTreeNode::TREE_ROOT_ID;
        }

        $portal = new \TdbCmsPortal();
        if (false === $portal->Load($portalId)) {
            return \TCMSTreeNode::TREE_ROOT_ID;
        }

        return $portal->fieldMainNodeTree;
    }

    private function createTreeDataModel(\TdbCmsTree $node, string $fieldName, $path = ''): BackendTreeNodeDataModel
    {
        $treeNodeDataModel = $this->backendTreeNodeFactory->createTreeNodeDataModelFromTreeRecord($node);
        $children = $node->GetChildren(true);

        $path = $this->addBreadcrumbHtmlForNode($node, $fieldName, $path);

        while ($child = $children->Next()) {
            $childTreeNodeObj = $this->createTreeDataModel($child, $fieldName, $path);
            $treeNodeDataModel->addChildren($childTreeNodeObj);
        }

        return $treeNodeDataModel;
    }

    protected function addBreadcrumbHtmlForNode(\TdbCmsTree $node, string $fieldName, string $path = ''): string
    {
        $path .= '<li class="breadcrumb-item">'.$node->fieldName."</li>\n";
        $this->treePathHTML .= '<div id="'.$fieldName.'_tmp_path_'.$node->id.'" style="display:none;"><ol class="breadcrumb ml-0"><li class="breadcrumb-item"><i class="fas fa-sitemap"></i></li>'.$path.'</ol></div>'."\n";

        return $path;
    }

    /**
     * Fetches a list of all restricted nodes (of all portals).
     * A restricted node is a startnavigation nodes of a portal.
     */
    private function getPortalNavigationStartNodes(): array
    {
        $portalList = \TdbCmsPortalList::GetList();

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
}
