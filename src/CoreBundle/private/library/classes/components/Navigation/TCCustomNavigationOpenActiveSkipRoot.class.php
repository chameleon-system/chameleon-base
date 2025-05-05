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
class TCCustomNavigationOpenActiveSkipRoot extends TCCustomNavigationOpenActive
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
        $oChildren = $this->oRootNode->GetChildren();
        $oChildren->GoToStart();
        // for each child, get is children, then run render...
        while ($oRootNode = $oChildren->Next()) {
            /* @var $oRootNode TCMSTreeNode */
            $this->FetchTreeDataForCacheTriggers($oRootNode);
            if (!$this->ShowOnlyBreadcrumbNodes() || $oRootNode->IsInBreadcrumb()) {
                $oChildrenLevel2 = $oRootNode->GetChildren();
                $oChildrenLevel2->GoToStart();
                $totalSiblings = $oChildrenLevel2->Length();
                while ($oNode = $oChildrenLevel2->Next()) {
                    /* @var $oNode TCMSTreeNode */
                    $this->FetchTreeDataForCacheTriggers($oNode);
                    if ($this->_ShowNode($oNode, 0, $count)) {
                        $ulSiblingStyle = $this->_WriteSubmenuStyle($oNode, $count, $totalSiblings);
                        $menu .= "<ul {$ulSiblingStyle}>".$this->linefeed;
                        $oGrandChildren = $oNode->GetChildren();
                        $showNodesChildren = $this->_ShowChildren($oNode, $count, 0);
                        $bHasChildren = ($oGrandChildren->Length() > 0);
                        $nodeStyle = $this->_WriteRootNodeListClass($oNode, $bHasChildren, $count, $totalSiblings);
                        $link = $this->_RenderRootNode($oNode);
                        if ($oGrandChildren->Length() > 0 && $showNodesChildren) {
                            $menu .= "<li {$nodeStyle}>{$link}".$this->linefeed;
                            $children = $this->_Render($oGrandChildren, $oNode, 1);
                            if (!empty($children)) {
                                $menu .= "{$children}";
                            }
                            $menu .= '</li>'.$this->linefeed;
                        } else {
                            $menu .= "<li {$nodeStyle}>{$link}</li>".$this->linefeed;
                        }
                        $menu .= '</ul>'.$this->linefeed;
                        ++$count;
                        $menu .= $this->_OuterULSeperator();
                    }
                }
            }
        }

        return $menu;
    }

    protected function ShowOnlyBreadcrumbNodes()
    {
        return false;
    }
}
