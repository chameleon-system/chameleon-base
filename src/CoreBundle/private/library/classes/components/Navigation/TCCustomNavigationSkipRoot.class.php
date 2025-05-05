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
 * only show the children of the active node.
 * /**/
class TCCustomNavigationSkipRoot extends TCCustomNavigation
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
        // for each child, get is children, then run render...
        if (!$this->bPlaceEachRootNodeInASeparateBlock) {
            $menu .= '<ul>';
        }
        while ($oRootNode = $oChildren->Next()) {
            /* @var $oRootNode TCMSTreeNode */
            $this->FetchTreeDataForCacheTriggers($oRootNode);
            if ($oRootNode->IsInBreadcrumb()) {
                $oChildrenLevel2 = $oRootNode->GetChildren();
                $oChildrenLevel2->GoToStart();
                $tTotalSubNodeChildren = $oChildrenLevel2->Length();
                while ($oNode = $oChildrenLevel2->Next()) {
                    /* @var $oNode TCMSTreeNode */
                    $this->FetchTreeDataForCacheTriggers($oNode);
                    if ($this->_ShowNode($oNode, 0, $count)) {
                        if ($this->bPlaceEachRootNodeInASeparateBlock) {
                            $ulSiblingStyle = $this->_WriteSubmenuStyle($oNode, $count, $totalSiblings);
                            $menu .= "<ul {$ulSiblingStyle}>".$this->linefeed;
                        }
                        $oGrandChildren = $oNode->GetChildren();
                        $showNodesChildren = $this->_ShowChildren($oNode, $count, 0);
                        $bHasChildren = ($oGrandChildren->Length() > 0);
                        $nodeStyle = $this->_WriteRootNodeListClass($oNode, $bHasChildren, $count, $tTotalSubNodeChildren);
                        $link = $this->_RenderRootNode($oNode);
                        if ($oGrandChildren->Length() > 0 && $showNodesChildren) {
                            $menu .= "<li {$nodeStyle}>{$link}".$this->linefeed;
                            $children = $this->_Render($oGrandChildren, $oNode, 1);
                            //            if (!empty($children)) $menu .= "</li><li>{$children}";
                            if (!empty($children)) {
                                $menu .= "{$children}";
                            }
                            //          $menu .= $this->_Render($oGrandChildren,$oNode,1);
                            $menu .= '</li>'.$this->linefeed;
                        } else {
                            $menu .= "<li {$nodeStyle}>{$link}</li>".$this->linefeed;
                        }
                        if ($this->bPlaceEachRootNodeInASeparateBlock) {
                            $menu .= '</ul>'.$this->linefeed;
                        }
                        ++$count;
                        $menu .= $this->_OuterULSeperator();
                    }
                }
            }
        }
        if (!$this->bPlaceEachRootNodeInASeparateBlock) {
            $menu .= '</ul>';
        }

        return $menu;
    }
}
