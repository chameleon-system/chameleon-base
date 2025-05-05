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
 * each main node and its children is forced into ONE UL.
 * /**/
class TCCustomNavigationFlat extends TCCustomNavigation
{
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
        while ($oNode = $oChildren->Next()) {
            /* @var $oNode TCMSTreeNode */
            $this->FetchTreeDataForCacheTriggers($oNode);
            if ($this->_ShowNode($oNode, 0, $count)) {
                $ulSiblingStyle = $this->_WriteSubmenuStyle($oNode, $count, $totalSiblings);
                $oGrandChildren = $oNode->GetChildren();
                $menu .= "<ul {$ulSiblingStyle}>".$this->linefeed;
                $showNodesChildren = $this->_ShowChildren($oNode, $count, 0);
                $bHasChildren = ($oGrandChildren->Length() > 0 && $showNodesChildren);
                $nodeStyle = $this->_WriteRootNodeListClass($oNode, $bHasChildren);
                $link = $this->_RenderRootNode($oNode);
                if ($oGrandChildren->Length() > 0 && $this->_ShowChildren($oNode, $count, 0)) {
                    $menu .= "<li {$nodeStyle}>{$link}</li>".$this->linefeed;
                    $menu .= $this->_Render($oGrandChildren, $oNode, 1);
                } else {
                    $menu .= "<li {$nodeStyle}>{$link}</li>".$this->linefeed;
                }
                $menu .= '</ul>'.$this->linefeed;
                ++$count;
                $menu .= $this->_OuterULSeperator();
            }
        }

        return $menu;
    }

    /**
     * The recursive function that renders all subnodes. returns the complete subnavi
     * for the root node oParentNode.
     *
     * @param TIterator $oSubnodes
     * @param TCMSTreeNode $oParentNode
     * @param int $level
     *
     * @return string
     */
    public function _Render($oSubnodes, $oParentNode, $level = 0)
    {
        $menu = '';
        if ($this->_ShowChildren($oParentNode, 0, $level)) {
            $submenuCSS = $this->_GetSubmenuBlockCSS($oParentNode, $level);

            $iframeCode = $this->_GetSelectBoxWorkAround();
            $row = 0;
            $menuContent = '';
            $totalSiblings = $oParentNode->CountChildren();
            while ($oNode = $oSubnodes->Next()) {
                /* @var $oNode TCMSTreeNode */
                $this->FetchTreeDataForCacheTriggers($oNode);
                if ($this->_ShowNode($oNode, $level, $row)) {
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
                    if ($oChildren->Length() > 0 && $this->_ShowChildren($oNode, $row, $level)) {
                        $menuContent .= "<li {$nodeStyle}>{$iframeCode}{$link}</li>".$this->linefeed;
                        $menuContent .= $this->_Render($oChildren, $oNode, $level + 1);
                    } else {
                        $menuContent .= "<li {$nodeStyle}>{$iframeCode}{$link}</li>".$this->linefeed;
                    }
                    ++$row;
                }
            }

            if (!empty($menuContent)) {
                $menu = $menuContent;
            }
        }

        return $menu;
    }

    /**
     * returns a string that seperates the main UL nodes of the navigation.
     *
     * @return string
     */
    protected function _OuterULSeperator()
    {
        return '<div class="cleardiv">&nbsp;</div>';
    }
}
