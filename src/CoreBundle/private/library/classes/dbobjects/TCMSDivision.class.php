<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSDivision extends TCMSDivisionAutoParent
{
    public static function GetStopNodes($portalID = null)
    {
        static $aStopNodes;
        if (is_null($portalID)) {
            $key = 'none';
        } else {
            $key = $portalID;
        }
        if (!$aStopNodes || !array_key_exists($key, $aStopNodes)) {
            if (!isset($aStopNodes)) {
                $aStopNodes = [];
            }
            $aStopNodes[$key] = [];
            $query = 'SELECT `cms_division`.`cms_tree_id_tree`
                    FROM `cms_division`
                 ';
            if (!is_null($portalID)) {
                $query .= " WHERE `cms_division`.`cms_portal_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($portalID)."'";
            }
            $rDivisions = MySqlLegacySupport::getInstance()->query($query);
            while ($aDivision = MySqlLegacySupport::getInstance()->fetch_assoc($rDivisions)) {
                $aStopNodes[$key][] = $aDivision['cms_tree_id_tree'];
            }
        }

        return $aStopNodes[$key];
    }

    /**
     * returns a pointer to the page division.
     *
     * @param TCMSPage $oPage
     *
     * @return TdbCmsDivision|null
     */
    public static function GetPageDivision($oPage)
    {
        $aKey = [
            'class' => __CLASS__,
            'method' => 'getPageDivision',
            'pageid' => $oPage->id,
            'language' => $oPage->GetLanguage(),
        ];
        $cache = ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.cache');
        $key = $cache->getKey($aKey, false);
        $oPageDivision = $cache->get($key);
        if (null === $oPageDivision) {
            $aStopNodes = TdbCmsDivision::GetStopNodes();
            /** @var $oNaviPath TCMSPageBreadcrumb */
            $oNaviPath = $oPage->GetNavigationPath($aStopNodes);
            $oPageDivision = false;
            $oNaviPath->GoToStart();
            if ($oNaviPath->Length() > 0) {
                /** @var $oDivisionNode TCMSTreeNode */
                $oDivisionNode = $oNaviPath->Next();
                // either the first node in the breadcrumb is the division node, or its parent is the division node...
                $sDivisionNodeId = '';
                if (in_array($oDivisionNode->id, $aStopNodes)) {
                    $sDivisionNodeId = $oDivisionNode->id;
                } else {
                    $sDivisionNodeId = $oDivisionNode->sqlData['parent_id'];
                }

                $oPageDivision = TdbCmsDivision::GetNewInstance();
                if (false === $oPageDivision->LoadFromField('cms_tree_id_tree', $sDivisionNodeId)) {
                    $oPageDivision = false;
                }
            }

            $cache->set($key, $oPageDivision, [
                    ['table' => 'cms_tree', 'id' => null],
                    ['table' => 'cms_tpl_page', 'id' => $oPage->id],
                ]
            );
        }

        return (false !== $oPageDivision) ? $oPageDivision : null;
    }

    /**
     * returns the root node of the division.
     *
     * @return TCMSTreeNode
     */
    public function GetDivisionNode()
    {
        $oNode = new TCMSTreeNode();
        $oNode->Load($this->sqlData['cms_tree_id_tree']);

        return $oNode;
    }

    /**
     * finds the division to which the tree node iTreeNode belongs. returns
     * null if no match can be found.
     *
     * @param int $iTreeNode - cms_tree id
     *
     * @return TdbCmsDivision
     */
    public static function GetTreeNodeDivision($iTreeNode)
    {
        static $aDivisionLookup = [];
        static $aNodeLookup = [];
        $oDivision = null;
        $iOriginalTreeNode = $iTreeNode;
        if (false == array_key_exists($iTreeNode, $aNodeLookup)) {
            $aNodeLookup[$iOriginalTreeNode] = false;
            if (!empty($iTreeNode)) {
                $aDivisionTreeNodes = self::GetStopNodes();
                $notDone = (!in_array($iTreeNode, $aDivisionTreeNodes));
                $iMaxIterations = 30;
                $iIteration = 0;
                while ($notDone) {
                    ++$iIteration;
                    $query = "SELECT `id`, `parent_id` FROM `cms_tree` WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($iTreeNode)."'";
                    $parentNode = MySqlLegacySupport::getInstance()->fetch_assoc(MySqlLegacySupport::getInstance()->query($query));
                    if ($iIteration < $iMaxIterations) {
                        if (false !== $parentNode) {
                            $iTreeNode = $parentNode['parent_id'];
                        }
                        $notDone = ((false !== $parentNode) && (!in_array($iTreeNode, $aDivisionTreeNodes)));
                    } else {
                        // Something went wrong while querying mysql - bailing out
                        exit('No proper mysql result (db down?) or max iterations in while loop reached in '.__FILE__.' on line '.__LINE__.'...exiting!');
                    }
                }

                if (in_array($iTreeNode, $aDivisionTreeNodes)) {
                    $aNodeLookup[$iOriginalTreeNode] = $iTreeNode;
                }
            }
        }
        if (false != $aNodeLookup[$iOriginalTreeNode]) {
            $iDivisionNode = $aNodeLookup[$iOriginalTreeNode];
            if (false == array_key_exists($iDivisionNode, $aDivisionLookup)) {
                $aDivisionLookup[$iDivisionNode] = TdbCmsDivision::GetNewInstance();
                if (false == $aDivisionLookup[$iDivisionNode]->LoadFromField('cms_tree_id_tree', $iDivisionNode)) {
                    $aDivisionLookup[$iDivisionNode] = null;
                }
            }
            $oDivision = $aDivisionLookup[$iDivisionNode];
        }

        return $oDivision;
    }
}
