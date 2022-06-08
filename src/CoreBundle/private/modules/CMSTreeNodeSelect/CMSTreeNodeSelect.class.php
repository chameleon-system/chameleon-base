<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @deprecated since 6.3.8 - use NavigationTreeSingleSelect instead
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

        $this->data['treeHTML'] = '';
        $this->data['treePathHTML'] = '';
        $this->aRestrictedNodes = $this->GetPortalNavigationStartNodes();
        $this->RenderTree($oRootNode, $this->nodeID, $this->fieldName);

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
     * renders the tree.
     *
     * @param TdbCmsTree $oNode
     * @param int        $activeID
     * @param string $fieldName
     * @param string $path
     * @param int    $level
     */
    protected function RenderTree(&$oNode, $activeID, $fieldName, $path = '', $level = 0)
    {
        $sNodeName = $oNode->fieldName;
        if (!empty($sNodeName)) {
            $spacer = '';
            for ($i = 0; $i < $level; ++$i) {
                $spacer .= '  ';
            }

            ++$level;

            $path .= '<li class="breadcrumb-item">'.$oNode->fieldName."</li>\n";
            $this->data['treePathHTML'] .= '<div id="'.$fieldName.'_tmp_path_'.$oNode->id.'" style="display:none;"><ol class="breadcrumb ml-0"><li class="breadcrumb-item"><i class="fas fa-sitemap"></i></li>'.$path.'</ol></div>'."\n";

            $this->data['treeHTML'] .= $spacer.'<li id="node'.$oNode->sqlData['cmsident'].'">';

            $this->data['treeHTML'] .= '<a href="#" onClick="'.$this->getOnClick($fieldName, $oNode).'">'.$sNodeName;

            if ($activeID == $oNode->id) {
                $this->data['treeHTML'] .= '<i class="fas fa-check"></i>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</span>';
            }

            $this->data['treeHTML'] .= '</a>';

            $oChildren = &$oNode->GetChildren(true);
            $iChildrenCount = $oChildren->Length();
            if ($iChildrenCount > 0) {
                $this->data['treeHTML'] .= "\n".$spacer."<ul>\n";
            }

            while ($oChild = $oChildren->Next()) {
                $this->RenderTree($oChild, $activeID, $fieldName, $path, $level);
            }
            if ($iChildrenCount > 0) {
                $this->data['treeHTML'] .= "\n</ul>\n";
            }

            $this->data['treeHTML'] .= "</li>\n";
        }
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
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery/jsTree/jquery.jstree.js').'" type="text/javascript"></script>';

        return $aIncludes;
    }

    /**
     * @param string       $fieldName
     * @param TCMSTreeNode $oNode
     *
     * @return string
     */
    protected function getOnClick($fieldName, $oNode)
    {
        return "chooseTreeNode('{$fieldName}','{$oNode->id}');";
    }
}
