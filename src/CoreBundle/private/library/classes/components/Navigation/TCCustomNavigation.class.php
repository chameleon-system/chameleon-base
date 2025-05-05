<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;
use ChameleonSystem\CoreBundle\Service\TreeServiceInterface;
use ChameleonSystem\CoreBundle\Util\UrlNormalization\UrlNormalizationUtil;

class TCCustomNavigation
{
    /**
     * root node of the tree.
     *
     * @var TdbCmsTree
     */
    public $oRootNode;

    /**
     * current active page.
     *
     * @var int
     */
    public $iCurrentPage;

    /**
     * added as linefeed after TAGs. sometimes the IE6 needs it.
     *
     * @var string
     */
    public $linefeed = '';

    /**
     * current active page.
     *
     * @var TCMSPage
     */
    public $oActiveBreadcrumb;

    /**
     * if set to true, each main node is placed within a <ul>.
     *
     * @var bool
     */
    public $bPlaceEachRootNodeInASeparateBlock = true;

    /**
     * optional spacer string after <a href=""></a> links tags.
     *
     * @var string
     */
    public $sLinkSpacer = '';

    /**
     * array of all tree nodes (table: cms_tree) that were rendered recursively
     * (values = ids)
     * useable to get cache trigger ids.
     *
     * @var array
     */
    public $aNodeIDs = [];

    /**
     * array of all connected pages of the rendered tree nodes
     * (values = ids)
     * useable to get cache trigger ids.
     *
     * @var array
     */
    public $aPageIDs = [];

    /**
     * you may add a "<span>[{sLinkText}]</span>" here or any other HTML
     * [{sLinkText}] is replaced with the link.
     *
     * @var string
     */
    public $sEncloseLinkTextHTML = '';

    /**
     * divides the sublevel every n-times and adds an additional </ul><ul>.
     *
     * @var int
     */
    public $iLevelSplitCounter = 0;

    public function __construct()
    {
        $this->oActiveBreadcrumb = $this->getActivePageService()->getActivePage()->getBreadcrumb();
    }

    /**
     * @param string $rootNodeId
     * @param int $iCurrentPage
     */
    public function Load($rootNodeId, $iCurrentPage)
    {
        $this->iCurrentPage = $iCurrentPage;
        $this->oRootNode = $this->getRootNode($rootNodeId);
    }

    /**
     * For backwards compatibility we return an uninitialized TdbCmsTree if the tree node was not found.
     *
     * @param string $rootNodeId
     *
     * @return TdbCmsTree
     */
    private function getRootNode($rootNodeId)
    {
        $rootNode = $this->getTreeService()->getById($rootNodeId);
        if (null !== $rootNode) {
            return $rootNode;
        }

        return TdbCmsTree::GetNewInstance();
    }

    /**
     * returns an array of all tree nodes (table: cms_tree) that were rendered
     * recursively (values = ids)
     * triggers Render() method if aNodesIDs is empty.
     *
     * @return array
     */
    public function GetIdListOfAllTreeNodes()
    {
        if (!is_array($this->aNodeIDs) || 0 == count($this->aNodeIDs)) {
            $this->Render();
        }

        return $this->aNodeIDs;
    }

    /**
     * returns an array of all pages that are connected to the rendered tree nodes
     * (values = ids)
     * triggers Render() method if aPageIDs is empty.
     *
     * @return array
     */
    public function GetIdListOfAllConnectedPages()
    {
        if (!is_array($this->aPageIDs) || 0 == count($this->aPageIDs)) {
            $this->Render();
        }

        return $this->aPageIDs;
    }

    /**
     * saves tree node ids and all connected page ids in arrays for cache trigger usage.
     *
     * @param TdbCmsTree $oNode
     */
    protected function FetchTreeDataForCacheTriggers($oNode)
    {
        // save ID in list of all rendered node IDs
        if (false === array_search($oNode->id, $this->aNodeIDs)) {
            $this->aNodeIDs[] = $oNode->id;
        }

        $oPagesList = $oNode->GetAllLinkedPages();
        while ($oPage = $oPagesList->Next()) {
            if (false === array_search($oPage->id, $this->aPageIDs)) {
                $this->aPageIDs[] = $oPage->id;
            }
        }
    }

    /**
     * render menu.
     *
     * @return string
     */
    public function Render()
    {
        // never render the rootnode...
        $menu = '';
        $count = 0;
        $totalSiblings = $this->oRootNode->CountChildren();
        $oChildren = $this->oRootNode->GetChildren();
        $oChildren->GoToStart();
        if (!$this->bPlaceEachRootNodeInASeparateBlock) {
            $menu .= '<ul>';
        }
        while ($oNode = $oChildren->Next()) {
            /* @var $oNode TdbCmsTree */
            $this->FetchTreeDataForCacheTriggers($oNode);

            if ($this->_ShowNode($oNode, 0, $count)) {
                if ($this->bPlaceEachRootNodeInASeparateBlock) {
                    $ulSiblingStyle = $this->_WriteSubmenuStyle($oNode, $count, $totalSiblings);
                    $menu .= "<ul {$ulSiblingStyle}>".$this->linefeed;
                }
                $link = $this->_RenderRootNode($oNode);
                $oGrandChildren = $oNode->GetChildren();
                $showNodesChildren = $this->_ShowChildren($oNode, $count, 0);

                $bHasModuleNaviNodes = false;
                if ($showNodesChildren) {
                    // try to fetch additional subnavigations from a connected module instance
                    $sModuleNaviContent = $oNode->GetModuleSubNavi();
                    if (!empty($sModuleNaviContent)) {
                        $link .= $sModuleNaviContent;
                        $bHasModuleNaviNodes = true;
                    }
                }

                $bHasChildren = ($showNodesChildren && ($bHasModuleNaviNodes || $oGrandChildren->Length() > 0));
                $nodeStyle = $this->_WriteRootNodeListClass($oNode, $bHasChildren, $count, $totalSiblings);

                if ($bHasChildren && !$bHasModuleNaviNodes) {
                    $menu .= "<li {$nodeStyle}>{$link}".$this->linefeed;
                    $children = $this->_Render($oGrandChildren, $oNode, 1);
                    if (!empty($children)) {
                        $menu .= "{$children}";
                    }
                    $menu .= '</li>'.$this->linefeed;
                } else {
                    $menu .= "<li {$nodeStyle}>{$link}</li>".$this->linefeed;
                }
                if ($this->bPlaceEachRootNodeInASeparateBlock) {
                    $menu .= '</ul>'.$this->linefeed;
                }
                ++$count;
                $menu .= $this->_OuterULSeperator();
            } else {
                --$totalSiblings;
            } // reduce sibling count
        }
        if (!$this->bPlaceEachRootNodeInASeparateBlock) {
            $menu .= '</ul>';
        }

        $menu = $this->PostMenuRender($menu);

        return $menu;
    }

    /**
     * allow final processing of menu.
     *
     * @param string $sMenu - the complete rendered menu
     *
     * @return string - the rendered menu
     */
    protected function PostMenuRender($sMenu)
    {
        return $sMenu;
    }

    /**
     * called for each submenu (<ul>) block.
     *
     * @param string $sSubmenuBlock - the submenu block (including its children)
     * @param TdbCmsTree $oParentNode
     * @param int $activeNodeLevel - the level of the active node within the block
     * @param int $level - the level of the block
     *
     * @return string
     */
    protected function WrapSubmenuString($sSubmenuBlock, $oParentNode, $activeNodeLevel, $level)
    {
        return $sSubmenuBlock;
    }

    /**
     * returns a string that seperates the main UL nodes of the navigation.
     *
     * @return string
     */
    protected function _OuterULSeperator()
    {
        return '';
    }

    /**
     * The recursive function that renders all subnodes. returns the complete subnavi
     * for the root node oParentNode.
     *
     * @param TIterator $oSubnodes
     * @param TdbCmsTree $oParentNode
     * @param int $level
     *
     * @return string
     */
    protected function _Render($oSubnodes, $oParentNode, $level = 0)
    {
        $menu = '';
        if ($this->_ShowChildren($oParentNode, 0, $level)) {
            $submenuCSS = $this->_GetSubmenuBlockCSS($oParentNode, $level);

            $iframeCode = $this->_GetSelectBoxWorkAround();
            $row = 0;
            $menuContent = '';
            $activeNodeLevel = 0;
            $totalSiblings = $oParentNode->CountChildren();

            $oSubnodes->GoToStart();

            while ($oNode = $oSubnodes->Next()) {
                /* @var $oNode TdbCmsTree */
                $this->FetchTreeDataForCacheTriggers($oNode);
                if ($this->_ShowNode($oNode, $level, $row)) {
                    if ($this->oActiveBreadcrumb->NodeInBreadcrumb($oNode->id)) {
                        $activeNodeLevel = $row;
                    }
                    $oChildren = $oNode->GetChildren();
                    $link = $this->_RenderNode($oNode, $oParentNode);
                    $showNodesChildren = $this->_ShowChildren($oNode, $row, $level);
                    $bHasModuleNaviNodes = false;
                    if ($showNodesChildren) {
                        // try to fetch additional subnavigations from a connected module instance
                        $sModuleNaviContent = $oNode->GetModuleSubNavi();
                        if (!empty($sModuleNaviContent)) {
                            $link .= $sModuleNaviContent;
                            $bHasModuleNaviNodes = true;
                        }
                    }

                    $bHasChildren = ($showNodesChildren && ($bHasModuleNaviNodes || $oChildren->Length() > 0));

                    $nodeStyle = $this->_GetNodeStyle($oNode, $level, $row, $bHasChildren, $totalSiblings);

                    $isLast = ($totalSiblings - 1 == $row + 1);
                    // echo $row;
                    // echo ($totalSiblings)-1;
                    if ($this->iLevelSplitCounter > 0 && 0 == $row) {
                        if (empty($submenuCSS)) {
                            $submenuCSS = "class='firstul'";
                        } else {
                            $submenuCSS = str_replace('class="', 'class="firstul ', $submenuCSS);
                        }
                        // $submenuCSS .= " firstul";
                    }
                    if ($this->iLevelSplitCounter > 0 && 0 == ($row % $this->iLevelSplitCounter) && 0 != $row) {
                        if ($isLast) {
                            $ulColumnclass = 'lastul'.($row / $this->iLevelSplitCounter);
                        } else {
                            $ulColumnclass = 'column'.($row / $this->iLevelSplitCounter);
                        }
                        $menuContent .= "</ul><ul class='".$ulColumnclass."'>";
                    }
                    if ($bHasChildren && !$bHasModuleNaviNodes) {
                        $menuContent .= "<li {$nodeStyle}>{$iframeCode}{$link}".$this->linefeed;
                        $children = $this->_Render($oChildren, $oNode, $level + 1);
                        if (!empty($children)) {
                            $menuContent .= "{$children}";
                        }
                        $menuContent .= '</li>'.$this->linefeed;
                    } else {
                        $menuContent .= "<li {$nodeStyle}>{$iframeCode}{$link}</li>".$this->linefeed;
                    }
                    ++$row;
                } else {
                    --$totalSiblings;
                } // reduce sibling count
            }
            if (!empty($menuContent)) {
                $menu = "<ul {$submenuCSS}>".$this->linefeed.$menuContent.'</ul>'.$this->linefeed;
                $menu = $this->WrapSubmenuString($menu, $oParentNode, $activeNodeLevel, $level);
            }
        }

        return $menu;
    }

    /**
     * returns false if you want to hide the children of $oNode.
     *
     * @param TdbCmsTree $oNode - parent node
     * @param int $row - row within current submenu
     * @param int $level - level within navi of the parent node
     *
     * @return bool
     */
    protected function _ShowChildren($oNode, $row = null, $level = null)
    {
        return $oNode->IsActive();
    }

    /**
     * return false if you do not want to show the node $oNode.
     *
     * @param TdbCmsTree $oNode - node in question
     * @param int $level - level within the navi
     * @param int $row - position within current level
     *
     * @return bool
     */
    protected function _ShowNode($oNode, $level = null, $row = null)
    {
        $showNode = $oNode->IsActive();
        if (true === $showNode) {
            $showNode = $oNode->AllowAccessByCurrentUser();
        }

        return $showNode;
    }

    /**
     * every submenu can have different styles placed into the <li> around it
     * depending on which sibling it is within its parent.
     *
     * @param TdbCmsTree $oNode
     * @param int $siblingCount - at what position within the parent <li> is it
     * @param int $totalSiblings - total number of <ul> in the parent <li>
     *
     * @return string
     */
    protected function _WriteSubmenuStyle($oNode, $siblingCount, $totalSiblings)
    {
        $nodeCssClasses = [];
        if ($oNode->IsInBreadcrumb()) {
            $nodeCssClasses[] = 'expanded';
        }
        if (0 == $siblingCount) {
            $nodeCssClasses[] = 'firstmenu';
        } elseif ($siblingCount == ($totalSiblings - 1)) {
            $nodeCssClasses[] = 'lastmenu';
        } else {
            $nodeCssClasses[] = 'menuitem'.$siblingCount;
        }
        if ('' != trim($oNode->fieldCssClasses)) {
            $nodeCssClasses[] = $oNode->fieldCssClasses;
        }

        $nodeCssClass = '';
        if (!empty($nodeCssClasses)) {
            $nodeCssClass = 'class="'.implode(' ', $nodeCssClasses).'"';
        }

        return $nodeCssClass;
    }

    /**
     * the return value of this function is inserted as a string in the <li> tag
     * around the root node given by oNode.
     *
     * @param TdbCmsTree $oNode
     * @param bool $bHasChildren - set to true if the node has children and is allowed to show them
     * @param int $row
     * @param int $totalSiblings
     *
     * @return string
     */
    protected function _WriteRootNodeListClass($oNode, $bHasChildren = false, $row = 0, $totalSiblings = 0)
    {
        $nodeCssClasses = [];
        $nodeCssClasses[] = 'liNavi'.str_replace(['-', '_'], '', $oNode->sqlData['urlname']);

        $isFirst = (0 == $row);
        $isLast = (($totalSiblings - 1) == $row);

        if ($oNode->IsInBreadcrumb()) {
            $nodeCssClasses[] = 'expanded';
        }
        if ($bHasChildren) {
            $nodeCssClasses[] = 'haschildren';
        }
        if ($isFirst) {
            $nodeCssClasses[] = 'firstnode';
        }
        if ($isLast) {
            $nodeCssClasses[] = 'lastnode';
        }

        if ('' != trim($oNode->fieldCssClasses)) {
            $nodeCssClasses[] = $oNode->fieldCssClasses;
        }

        // add class with name of node and prefix "node" to allow custom styling per node
        $sClassName = str_replace('\n', '', $this->getUrlNormalizationUtil()->normalizeUrl($oNode->sqlData['name']));
        $sClassName = preg_replace('[^a-zA-Z]', '', $sClassName);

        $nodeCssClasses[] = 'node'.strtolower($sClassName);

        $nodeCssClass = '';
        if (!empty($nodeCssClasses)) {
            $nodeCssClass = 'class="'.implode(' ', $nodeCssClasses).'"';
        }

        return $nodeCssClass;
    }

    /**
     * allows one to set the properties for a given <li> tag around a node
     * level is the depth of the navigation (0=root level)
     * while row is the row within one level (first point = 0).
     *
     * @param TdbCmsTree $oNode
     * @param int $level
     * @param int $row
     * @param bool $bHasChildren - set to true if the node has children, and they are allowed to be displayed
     * @param int $totalSiblings
     *
     * @return string
     */
    protected function _GetNodeStyle($oNode, $level, $row, $bHasChildren = false, $totalSiblings = 0)
    {
        $nodeCssClasses = [];
        $nodeCssClasses[] = 'liNavi'.str_replace(['-', '_'], '', $oNode->sqlData['urlname']);

        if (1 == $level && 0 == $row) {
            $nodeCssClasses[] = 'firstrow';
        }
        if ($oNode->IsInBreadcrumb()) {
            $nodeCssClasses[] = 'expanded';
        }
        if ($bHasChildren) {
            $nodeCssClasses[] = 'haschildren';
        }

        $isFirst = (0 == $row);
        $isLast = (($totalSiblings - 1) == $row);
        if ($isFirst) {
            $nodeCssClasses[] = 'firstnode';
        }
        if ($isLast) {
            $nodeCssClasses[] = 'lastnode';
        }

        if ('' != trim($oNode->fieldCssClasses)) {
            $nodeCssClasses[] = $oNode->fieldCssClasses;
        }

        // add class with name of node and prefix "node" to allow custom styling per node
        $sClassName = $oNode->GetCSSClassName();
        $nodeCssClasses[] = $sClassName;

        $nodeClass = '';
        if (!empty($nodeCssClasses)) {
            $nodeClass = 'class="'.implode(' ', $nodeCssClasses).'"';
        }

        return $nodeClass;
    }

    /**
     * draw the contents (link and text) of the root node oNode.
     *
     * @param TdbCmsTree $oNode
     *
     * @return string
     */
    protected function _RenderRootNode($oNode)
    {
        $link = $this->_GetNodeName($oNode);
        $linkStyle = $this->AddLinkStyle($oNode);
        $aLinkAttributes = $oNode->GetLinkAttributes();
        if (!array_key_exists('href', $aLinkAttributes) || empty($aLinkAttributes['href'])) {
            $link = "<span {$linkStyle}>{$link}</span>".$this->sLinkSpacer;
        } else {
            $link = "<a {$linkStyle} ".$oNode->GetLinkAttributesAsString().">{$link}</a>".$this->sLinkSpacer;
        }

        return $link;
    }

    /**
     * returns name for URL, based on tree node URL name
     * you may add additional HTML here or add it as a navigation parameter
     * in the CMS backend in the spot config parameters (sEncloseLinkTextHTML).
     *
     * @param TdbCmsTree $oNode
     *
     * @return string
     */
    protected function _GetNodeName($oNode)
    {
        $sLinkText = TGlobal::OutHTML($oNode->GetName());
        if (!empty($this->sEncloseLinkTextHTML)) {
            $sLinkText = str_replace('[{sLinkText}]', $sLinkText, $this->sEncloseLinkTextHTML);
        }
        $sLinkText = str_replace('&#92;n', '<br />', $sLinkText);

        return $sLinkText;
    }

    /**
     * Draws the contents of a node (NOT rootnode!) (link and text).
     *
     * @param TdbCmsTree $oNode
     * @param TdbCmsTree $oParentNode
     *
     * @return string
     */
    protected function _RenderNode($oNode, $oParentNode)
    {
        $link = $this->_GetNodeName($oNode);
        $linkStyle = $this->AddLinkStyle($oNode);

        $aLinkAttributes = $oNode->GetLinkAttributes();
        if (!array_key_exists('href', $aLinkAttributes) || empty($aLinkAttributes['href'])) {
            $link = "<span {$linkStyle}>{$link}</span>".$this->sLinkSpacer;
        } else {
            $link = "<a {$linkStyle} ".$oNode->GetLinkAttributesAsString().">{$link}</a>";
        }

        // try to fetch additional subnavigations from a connected module instance
        $link .= $oNode->GetModuleSubNavi();

        return $link;
    }

    /**
     * allows setting classes and styles for the <a> tag around the node.
     *
     * @param TdbCmsTree $oNode
     *
     * @return string
     */
    protected function AddLinkStyle($oNode)
    {
        $nodeCssClasses = [];
        $nodeCssClasses[] = 'aNavi'.str_replace(['-', '_'], '', $oNode->sqlData['urlname']);

        if ($oNode->IsActiveNode()) {
            $nodeCssClasses[] = 'activelink';
        }
        if ($oNode->CountChildren() > 0) {
            $nodeCssClasses[] = 'haschildren';
        }
        if ($oNode->IsInBreadcrumb()) {
            $nodeCssClasses[] = 'expanded';
        }
        if ('' != trim($oNode->fieldCssClasses)) {
            $nodeCssClasses[] = $oNode->fieldCssClasses;
        }

        if ('' != trim($oNode->fieldCssClasses)) {
            $nodeCssClasses[] = $oNode->fieldCssClasses;
        }

        $nodeCssClass = '';
        if (!empty($nodeCssClasses)) {
            $nodeCssClass = 'class="'.implode(' ', $nodeCssClasses).'"';
        }

        return $nodeCssClass;
    }

    /**
     * allows one to modify the submenu block (the <ul> around the submenu) for a given node.
     *
     * @param TdbCmsTree $oParentNode
     * @param int $level
     *
     * @return string
     */
    protected function _GetSubmenuBlockCSS($oParentNode, $level)
    {
        return '';
    }

    protected function _GetSelectBoxWorkAround()
    {
        return ''; // "<iframe src=\"javascript:;\"></iframe>";
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }

    /**
     * @return TreeServiceInterface
     */
    private function getTreeService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.tree_service');
    }

    /**
     * @return UrlNormalizationUtil
     */
    private function getUrlNormalizationUtil()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.util.url_normalization');
    }
}
