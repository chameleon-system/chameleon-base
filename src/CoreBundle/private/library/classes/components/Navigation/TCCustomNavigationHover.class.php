<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCCustomNavigationHover extends TCCustomNavigation
{
    /**
     * tree node lookup for divisions.
     *
     * @var array
     */
    protected $aDivisionNodeIdList = [];

    /**
     * current division of the level being parsed.
     *
     * @var TdbCmsDivision
     */
    protected $oCurrentDivision;

    public $bPlaceEachRootNodeInASeparateBlock = false;

    /**
     * render menu.
     *
     * @return string
     */
    public function Render()
    {
        // never render the root node...
        $menu = '';
        $count = 0;
        $totalSiblings = $this->oRootNode->CountChildren();
        $oChildren = $this->oRootNode->GetChildren();
        $oChildren->GoToStart();
        if (!$this->bPlaceEachRootNodeInASeparateBlock) {
            $menu .= '<ul>';
        }
        while ($oNode = $oChildren->Next()) {
            /* @var $oNode TCMSTreeNode */
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
                    // try to fetch additional subNavigations from a connected module instance
                    $sModuleNaviContent = $oNode->GetModuleSubNavi();
                    if (!empty($sModuleNaviContent)) {
                        $link .= $sModuleNaviContent;
                        $bHasModuleNaviNodes = true;
                    }
                }

                $bHasChildren = ($showNodesChildren && ($bHasModuleNaviNodes || $oGrandChildren->Length() > 0));

                $oDivision = TdbCmsDivision::GetTreeNodeDivision($oNode->id);
                if (!is_null($oDivision) && is_array($oDivision->sqlData)) {
                    $iStopLevel = (int) $oDivision->sqlData['menu_stop_level'];
                    if ($iStopLevel > 0) {
                        $bHasChildren = ($bHasChildren && $iStopLevel >= 1);
                    }
                }

                $nodeStyle = $this->_WriteRootNodeListClass($oNode, $bHasChildren);
                if ($bHasChildren && !$bHasModuleNaviNodes) {
                    $menu .= "<li {$nodeStyle}>{$link}".$this->linefeed;
                    $children = $this->_Render($oGrandChildren, $oNode, 1);
                    if (!empty($children)) {
                        $menu .= $children;
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
            }
        }
        if (!$this->bPlaceEachRootNodeInASeparateBlock) {
            $menu .= '</ul>';
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

                    $oDivision = TdbCmsDivision::GetTreeNodeDivision($oNode->id);
                    if (!is_null($oDivision) && is_array($oDivision->sqlData)) {
                        $iStopLevel = (int) $oDivision->sqlData['menu_stop_level'];
                        if ($iStopLevel > 0) {
                            $bHasChildren = ($bHasChildren && $iStopLevel > $level);
                        }
                    }

                    $nodeStyle = $this->_GetNodeStyle($oNode, $level, $row, $bHasChildren, $totalSiblings);
                    if ($bHasChildren && !$bHasModuleNaviNodes) {
                        $menuContent .= "<li {$nodeStyle}>{$iframeCode}{$link}".$this->linefeed;
                        $children = $this->_Render($oChildren, $oNode, $level + 1);
                        if (!empty($children)) {
                            $menuContent .= $children;
                        }
                        $menuContent .= '</li>'.$this->linefeed;
                    } else {
                        $menuContent .= "<li {$nodeStyle}>{$iframeCode}{$link}</li>".$this->linefeed;
                    }
                    ++$row;
                }
            }
            if (!empty($menuContent)) {
                $menu = "<ul {$submenuCSS}>".$this->linefeed.$menuContent.'</ul>'.$this->linefeed;
            }
        }

        return $menu;
    }

    public function Load($rootNodeId, $iCurrentPage)
    {
        /** @var $oPagePortal TdbCmsPortal */
        $oPagePortal = TdbCmsPortal::GetPagePortal($iCurrentPage);
        $this->aDivisionNodeIdList = [];
        if ($oPagePortal) {
            $oDivisions = $oPagePortal->GetFieldCmsPortalDivisionsList();
            while ($oDivision = $oDivisions->Next()) {
                $this->aDivisionNodeIdList[$oDivision->sqlData['cms_tree_id_tree']] = $oDivision;
            }
            parent::Load($rootNodeId, $iCurrentPage);
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function _GetNodeStyle($oNode, $level, $row, $bHasChildren = false, $totalSiblings = 0)
    {
        $this->oCurrentDivision = TdbCmsDivision::GetTreeNodeDivision($oNode->id);
        $nodeClass = parent::_GetNodeStyle($oNode, $level, $row, $bHasChildren, $totalSiblings);
        if ('Links' == $this->oCurrentDivision->sqlData['menu_direction']) {
            if (!empty($nodeClass)) {
                $nodeClass = substr($nodeClass, 0, -1); // remove "
                $nodeClass = $nodeClass.' directionLeft"';
            } else {
                $nodeClass = 'class="directionLeft"';
            }
        }

        return $nodeClass;
    }

    /**
     * {@inheritDoc}
     * hide the children IF we are viewing the page through the CMS.
     */
    public function _ShowChildren($oNode, $row = null, $level = null)
    {
        if (TGlobal::IsCMSMode()) {
            return false;
        } else {
            return parent::_ShowChildren($oNode, $row, $level);
        }
    }
}
