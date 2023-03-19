<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSTableEditorPortalNavigation extends TCMSTableEditor
{
    /**
     * {@inheritdoc}
     */
    public function Delete($sId = null)
    {
        // delete the pagedef first..
        parent::Delete($sId);
        if (!empty($this->oTable->sqlData['tree_node'])) {
            $this->UpdateSubtreePathCache($this->oTable->sqlData['tree_node']);
        }
    }

    /**
     * gets called after save if all posted data was valid.
     *
     * @param TIterator  $oFields    holds an iterator of all field classes from DB table with the posted values or default if no post data is present
     * @param TCMSRecord $oPostTable holds the record object of all posted data
     */
    protected function PostSaveHook($oFields, $oPostTable)
    {
        $this->UpdateSubtreePathCache($oPostTable->sqlData['tree_node']);
        if (!is_null($this->oTablePreChangeData) && !empty($this->oTablePreChangeData->sqlData['tree_node'])) {
            $this->UpdateSubtreePathCache($this->oTablePreChangeData->sqlData['tree_node']);
        }
    }

    /**
     * updates all nodes with the path cache info.
     */
    protected function UpdateAllNodes()
    {
        $query = "SELECT * FROM cms_tree WHERE parent_id = '0'";
        $oCmsTreeList = TdbCmsTreeList::GetList($query);
        /** @var $oCmsTreeList TdbCmsTreeList */
        while ($oCmsTree = $oCmsTreeList->Next()) {
            /** @var $oCmsTree TdbCmsTree */
            $this->UpdateSubtreePathCache($oCmsTree->id);
        }
    }

    /**
     * cache the tree path to each node of the given subtree.
     */
    protected function UpdateSubtreePathCache($iNodeId)
    {
        $oNode = new TCMSTreeNode();
        /** @var $oNode TCMSTreeNode */
        $oNode->Load($iNodeId);
        $sPath = $oNode->GetTextPathToNode();
        $query = "UPDATE `cms_tree`
                   SET `pathcache` = '".MySqlLegacySupport::getInstance()->real_escape_string($sPath)."'
                 WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($oNode->id)."'
               ";
        MySqlLegacySupport::getInstance()->query($query);
        TCacheManager::PerformeTableChange('cms_tree', $iNodeId);
        $oChildren = $oNode->GetChildren();
        while ($oChild = $oChildren->Next()) {
            /** @var $oChild TCMSTreeNode */
            $this->UpdateSubtreePathCache($oChild->id);
        }
    }
}
