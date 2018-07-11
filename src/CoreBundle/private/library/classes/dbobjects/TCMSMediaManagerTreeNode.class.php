<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSMediaManagerTreeNode extends TCMSTreeNode
{
    /**
     * children of node.
     *
     * @var TdbCmsMediaTreeList
     */
    public $oChildren = null;

    public function TCMSMediaManagerTreeNode($id = null)
    {
        $table = 'cms_media_tree';
        parent::TCMSRecord($table, $id);
    }

    /**
     * {@inheritdoc}
     */
    public function &GetChildren($includeHidden = false, $languageId = null)
    {
        if (is_null($this->oChildren)) {
            $query = "SELECT `cms_media_tree`.*
                   FROM `cms_media_tree`
                   WHERE `cms_media_tree`.`parent_id` ='".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'
                   ORDER BY entry_sort";
            $this->oChildren = TdbCmsMediaTreeList::GetList($query, $languageId);
        }

        return $this->oChildren;
    }

    public function GetFilesInDirectory()
    {
        $oFileList = false;
        if (!is_null($this->id)) {
            $query = "SELECT `cms_media`.*
                    FROM `cms_media`
                   WHERE `cms_media`.`cms_media_tree_id` ='".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'
                   ";

            $oFileList = TdbCmsMediaList::GetList($query);
        }

        return $oFileList;
    }

    /**
     * {@inheritdoc}
     */
    public function GetTextPathToNode($sSeperator = '/', $bDropFirstItem = true, $bDisableLowerCaseConversion = false)
    {
        $sPath = '';
        $oBreadCrumb = $this->GetBreadcrumb(true);
        /** @var $oNode TCMSTreeNode */
        while ($oNode = $oBreadCrumb->Next()) {
            if (!empty($sPath)) {
                $sPath .= $sSeperator;
            }
            $sPath .= $oNode->sqlData['name'];
        }

        return $sPath;
    }
}
