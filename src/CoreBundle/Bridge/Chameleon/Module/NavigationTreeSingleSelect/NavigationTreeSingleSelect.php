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

use ChameleonSystem\CoreBundle\Factory\BackendTreeNodeFactory;
use ChameleonSystem\CoreBundle\DataModel\BackendTreeNodeDataModel;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use Doctrine\DBAL\Connection;
use MTPkgViewRendererAbstractModuleMapper;


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
    private $treePathHTML = "";

    /**
     * nodes that should not be assignable or that should have only a
     * restricted context menu.
     *
     * @var array
     */
    protected $aRestrictedNodes = array();


    public function __construct(
        Connection $dbConnection,
        EventDispatcherInterface $eventDispatcher,
        InputFilterUtilInterface $inputFilterUtil,
        BackendTreeNodeFactory $backendTreeNodeFactory
    ) {
        $this->dbConnection = $dbConnection;
        $this->eventDispatcher = $eventDispatcher;
        $this->inputFilterUtil = $inputFilterUtil;
        $this->backendTreeNodeFactory = $backendTreeNodeFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(\IMapperVisitorRestricted $visitor, $cachingEnabled, \IMapperCacheTriggerRestricted $cacheTriggerManager)
    {
        $fieldName = $this->inputFilterUtil->getFilteredGetInput('fieldName', '');
        $nodeID = $this->inputFilterUtil->getFilteredGetInput('id', '');

        $this->aRestrictedNodes = $this->getPortalNavigationStartNodes();

        $rootTreeId = $this->getPortalTreeRootNode();
        if ('' !== $rootTreeId) {
            $oRootNode = new \TdbCmsTree();
            $oRootNode->SetLanguage(\TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());
            $oRootNode->Load($rootTreeId);

            $treeNodes = $this->createTreeDataModel($oRootNode, $fieldName);
            $visitor->SetMappedValue('treeNodes', $treeNodes);
        }
        $visitor->SetMappedValue('treePathHTML', $this->treePathHTML);
        $visitor->SetMappedValue('activeId', $nodeID);
        $visitor->SetMappedValue('fieldName', $fieldName);
        $visitor->SetMappedValue('level', 0);
    }

    protected function getPortalTreeRootNode(): string
    {
        $portalId = $this->inputFilterUtil->getFilteredGetInput('portalID', '');

        $rootTreeId = \TCMSTreeNode::TREE_ROOT_ID;
        if ('' !== $portalId) {
            // load portal object
            $oPortal = new \TCMSPortal();
            $oPortal->Load($portalId);
            $rootTreeId = $oPortal->sqlData['main_node_tree'];
        }
        return $rootTreeId;
    }

    /**
     * Renders all children of a node
     *
     * @return BackendTreeNodeDataModel
     */
    protected function createTreeDataModel(\TdbCmsTree $node, string $fieldName, $path = ''): BackendTreeNodeDataModel
    {
        $treeNodeDataModel = $this->backendTreeNodeFactory->createTreeNodeDataModelFromTreeRecord($node);
        $children = $node->GetChildren(true);

        $path = $this->addBreadcrumbHmlForNode($node, $fieldName, $path);

        while ($child = $children->Next()) {
            $childTreeNodeObj = $this->createTreeDataModel($child, $fieldName, $path);
            $treeNodeDataModel->addChildren($childTreeNodeObj);
        }

        return $treeNodeDataModel;
    }

    protected function addBreadcrumbHmlForNode(\TdbCmsTree $node, string $fieldName, string $path = ''): string
    {
        $path .= '<li class="breadcrumb-item">'.$node->fieldName."</li>\n";
        $this->treePathHTML .= '<div id="'.$fieldName.'_tmp_path_'.$node->id.'" style="display:none;"><ol class="breadcrumb ml-0"><li class="breadcrumb-item"><i class="fas fa-sitemap"></i></li>'.$path.'</ol></div>'."\n";

        return $path;
    }

    /**
     * Fetches a list of all restricted nodes (of all portals)
     * a restricted node is a startnavigation nodes of a portal.
     *
     * @return array
     */
    protected function getPortalNavigationStartNodes(): array
    {
        $oPortalList = \TdbCmsPortalList::GetList();

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
     * @{inheritDoc}
     */
    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        $aIncludes[] = '<script src="'.\TGlobal::GetStaticURLToWebLib('/javascript/jquery-ui-1.12.1.custom/jquery-ui.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<script src="'.\TGlobal::GetStaticURLToWebLib('/javascript/jquery/cookie/jquery.cookie.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<script src="'.\TGlobal::GetStaticURL('/bundles/chameleonsystemcore/javascript/jsTree/3.3.8/jstree.js').'"></script>';
        $aIncludes[] = '<script src="'.\TGlobal::GetStaticURL('/bundles/chameleonsystemcore/javascript/navigationTree.js').'"></script>';
        $aIncludes[] = sprintf('<link rel="stylesheet" href="%s">', \TGlobal::GetStaticURL('/bundles/chameleonsystemcore/javascript/jsTree/3.3.8/themes/default/style.css'));
        $aIncludes[] = sprintf('<link rel="stylesheet" href="%s">', \TGlobal::GetStaticURL('/bundles/chameleonsystemcore/javascript/jsTree/customStyles/style.css'));

        return $aIncludes;
    }

}

