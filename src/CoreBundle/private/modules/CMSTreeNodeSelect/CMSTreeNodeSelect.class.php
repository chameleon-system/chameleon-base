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

        $this->data['treeHTML'] = '';
        $this->data['treePathHTML'] = '';
        $this->aRestrictedNodes = $this->GetPortalNavigationStartNodes();
        $this->RenderTree($oRootNode, $this->nodeID, $this->fieldName);

//        $viewRenderer = new ViewRenderer();
//        $viewRenderer->AddSourceObject('oNode', $oRootNode);
//        $viewRenderer->AddSourceObject('activeId', $this->nodeID);
//        $viewRenderer->AddSourceObject('fieldName', $this->fieldName);
//        $this->data['treeHTML'] = $viewRenderer->Render('CMSModulePageTree/standard.html.twig');

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
     * @param $fieldName
     * @param string $path
     * @param int    $level
     */
    protected function RenderTree(&$oNode, $activeID, $fieldName, $path = '', $level = 0)
    {
        $sNodeName = $oNode->fieldName;
        if (!empty($sNodeName)) {

            ++$level;

            $path .= '<li class="breadcrumb-item">'.$oNode->fieldName."</li>\n";
            $this->data['treePathHTML'] .= '<div id="'.$fieldName.'_tmp_path_'.$oNode->id.'" style="display:none;"><ol class="breadcrumb ml-0"><li class="breadcrumb-item"><i class="fas fa-sitemap"></i></li>'.$path.'</ol></div>'."\n";

            $oChildren = &$oNode->GetChildren(true);
            $iChildrenCount = $oChildren->Length();

            $disabled = "";
            $cssClass = "";
            if ($level <= 2) {
                $disabled = " ,&quot;checkbox_disabled&quot;: true";
                $cssClass = ' class="no-checkbox"';
            }

            if ($iChildrenCount > 0) {
                $nodeType = ' data-jstree="{&quot;type&quot;:&quot;pageFolder&quot;'.$disabled.'}"';
            } else {
                $nodeType = ' data-jstree="{&quot;type&quot;:&quot;page&quot;'.$disabled.'}"';
            }
            $datasForSelection = ' data-selection="{&quot;fieldName&quot;:&quot;'. $fieldName .'&quot; ,&quot;nodeId&quot;: &quot;'. $oNode->id .'&quot;}"';

            $this->data['treeHTML'] .= '<li id="node'.$oNode->sqlData['cmsident'].'" '. $cssClass . $nodeType . $datasForSelection .'>';
            $active = '';
            if ($activeID == $oNode->id) {
                $active = ' class="jstree-clicked"';
            }

            $this->data['treeHTML'] .= '<a href="#"'. $active .'>'.$sNodeName;

            $this->data['treeHTML'] .= '</a>';


            if ($iChildrenCount > 0) {
                $this->data['treeHTML'] .= "\n<ul>\n";
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

//                $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/javascript/jquery/jsTree/jquery.jstree.js').'" type="text/javascript"></script>';
        $aIncludes[] = '<script src="'.TGlobal::GetStaticURL('/bundles/chameleonsystemmediamanager/lib/jstree/3.3.8/jstree.js').'"></script>';
        $aIncludes[] = sprintf('<link rel="stylesheet" href="%s">', TGlobal::GetStaticURL('/bundles/chameleonsystemmediamanager/lib/jstree/3.3.8/themes/default/style.css'));
        $aIncludes[] = sprintf('<link rel="stylesheet" href="%s">', TGlobal::GetStaticURL('/bundles/chameleonsystemmediamanager/lib/jstree/customStyles/style.css'));

        return $aIncludes;
    }

}
