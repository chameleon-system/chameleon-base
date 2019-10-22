<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class CMSTreeNodeSelect extends TCMSModelBase
{
    /**
     * the id of the current node.
     *
     * @var int
     */
    protected $nodeID = null;

    /**
     * the name of the current node.
     *
     * @var string
     */
    protected $fieldName = null;

    /**
     * the root node id.
     *
     * @var string
     */
    protected $rootTreeID = TCMSTreeNode::TREE_ROOT_ID;

    /**
     * nodes that should not be assignable or that should have only a
     * restricted context menu.
     *
     * @var array
     */
    protected $aRestrictedNodes = array();

    public function &Execute()
    {
        $this->GetPortalTreeRootNode();

        $this->fieldName = $this->global->GetUserData('fieldName');
        $this->nodeID = $this->global->GetUserData('id');

        $oRootNode = new TdbCmsTree();
        $oRootNode->SetLanguage(TdbCmsUser::GetActiveUser()->GetCurrentEditLanguageID());
        $oRootNode->Load($this->rootTreeID);

        $this->aRestrictedNodes = $this->GetPortalNavigationStartNodes();

        $this->data['treePathHTML'] = '';
        $treeNodes = $this->createTreeDataModel($oRootNode, $this->fieldName);
        $this->data['treeNodes'] = $treeNodes;
        $this->data['nodeId'] = $this->nodeID;
        $this->data['fieldName'] = $this->fieldName;

        return $this->data;
    }

    protected function GetPortalTreeRootNode()
    {
        $portalID = $this->global->GetUserData('portalID');

        if (!empty($portalID)) {
            // load portal object
            $oPortal = new TCMSPortal();
            $oPortal->Load($portalID);
            $this->rootTreeID = $oPortal->sqlData['main_node_tree'];
        }
    }

    /**
     * @deprecated since 6.3.5 - use createTreeDataMode() instead.
     *
     * @param TdbCmsTree $oNode
     * @param string $activeID
     * @param string $fieldName
     * @param string $path
     * @param int $level
     *
     * @return string
     */
    protected function RenderTree(&$oNode, $activeID, $fieldName, $path = '', $level = 0)
    {
        return '';
    }

    protected function createTreeDataModel(TdbCmsTree $node, string $fieldName, $path = '')
    {
        $treeNodeFactory = $this->getBackendTreeNodeFactory();
        $treeNodeDataModel = $treeNodeFactory->createTreeNodeDataModelFromTreeRecord($node);
        $children = $node->GetChildren(true);

        $path .= $this->addBreadcrumbHmlForNode($node, $fieldName, $path);

        while ($child = $children->Next()) {
            $childTreeNodeObj = $this->createTreeDataModel($child, $fieldName, $path);
            $treeNodeDataModel->addChildren($childTreeNodeObj);
        }

        return $treeNodeDataModel;
    }

    protected function addBreadcrumbHmlForNode(TdbCmsTree $node, string $fieldName, string $path = ''): string
    {
        $path .= '<li class="breadcrumb-item">'.$node->fieldName."</li>\n";
        $this->data['treePathHTML'] .= '<div id="'.$fieldName.'_tmp_path_'.$node->id.'" style="display:none;"><ol class="breadcrumb ml-0"><li class="breadcrumb-item"><i class="fas fa-sitemap"></i></li>'.$path.'</ol></div>'."\n";

        return $path;
    }

    /**
     * Fetches a list of all restricted nodes (of all portals)
     * a restricted node is a startnavigation nodes of a portal.
     *
     * @return array
     */
    protected function GetPortalNavigationStartNodes()
    {
        $oPortalList = TdbCmsPortalList::GetList();

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
     * return an array of all js, css, or other header includes that are required
     * in the cms for this field. each include should be in one line, and they
     * should always be typed the same way so that no includes are included mor than once.
     *
     * @return array
     */
    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery-ui-1.12.1.custom/jquery-ui.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery/cookie/jquery.cookie.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURL('/bundles/chameleonsystemcore/javascript/jsTree/3.3.8/jstree.js').'"></script>';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURL('/bundles/chameleonsystemcore/javascript/navigationTree.js').'"></script>';
        $aIncludes[] = sprintf('<link rel="stylesheet" href="%s">', TGlobal::GetStaticURL('/bundles/chameleonsystemcore/javascript/jsTree/3.3.8/themes/default/style.css'));
        $aIncludes[] = sprintf('<link rel="stylesheet" href="%s">', TGlobal::GetStaticURL('/bundles/chameleonsystemcore/javascript/jsTree/customStyles/style.css'));

        return $aIncludes;
    }

    private function getBackendTreeNodeFactory(): \ChameleonSystem\CoreBundle\Factory\BackendTreeNodeFactory
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.factory.backend_tree_node');
    }

}
