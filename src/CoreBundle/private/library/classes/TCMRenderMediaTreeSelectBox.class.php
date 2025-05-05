<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMRenderMediaTreeSelectBox
{
    public $treeHTML = '';
    protected $selectedID;

    /**
     * if set to true, the class should show only folders writable to the user
     * how this is implemented is decided by extensions of this cllas.
     *
     * @var bool
     */
    protected $bShowOnlyWritableFolders = false;

    /**
     * render selectbox for the tree.
     *
     * @param int $selectedID
     * @param bool $bShowOnlyWritableFolders
     *
     * @return string
     */
    public function GetTreeOptions($selectedID = null, $bShowOnlyWritableFolders = false)
    {
        $this->selectedID = $selectedID;
        $this->bShowOnlyWritableFolders = $bShowOnlyWritableFolders;
        $this->RenderMediaTree();

        return $this->treeHTML;
    }

    /**
     * renders one tree level as <option>.
     *
     * @param int $parent_id
     * @param int $level
     * @param string $levelDirectoryName
     */
    protected function RenderMediaTree($parent_id = 1, $level = 0, $levelDirectoryName = '')
    {
        $paddingString = '';
        if ($level > 0) {
            $paddingString = str_repeat('-', $level);
        }
        for ($i = 0; $i < $level; ++$i) {
            $paddingString .= '--';
        }

        ++$level;

        static $internalCache = [];
        /* @var $oCmsMediaTreeList TdbCmsMediaTreeList */
        if (array_key_exists($parent_id, $internalCache)) {
            $oCmsMediaTreeList = $internalCache[$parent_id];
            $oCmsMediaTreeList->GoToStart();
        } else {
            $query = "SELECT *
                      FROM `cms_media_tree`
                     WHERE `parent_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($parent_id)."'
                  ORDER BY `entry_sort`
                   ";

            $oCmsMediaTreeList = TdbCmsMediaTreeList::GetList($query);
            $internalCache[$parent_id] = $oCmsMediaTreeList;
        }

        $symbol = '';
        if ($level > 0) {
            $symbol = '&#187&nbsp;';
        }

        if (!empty($levelDirectoryName)) {
            $levelDirectoryName .= ' > ';
        }

        while ($oCmsMediaTree = $oCmsMediaTreeList->Next()) {
            /** @var $oCmsMediaTree TdbCmsMediaTree */
            $selected = '';
            if (!is_null($this->selectedID) && $this->selectedID == $oCmsMediaTree->id) {
                $selected = ' selected="selected"';
            }

            $directoryName = $oCmsMediaTree->GetName();
            $fullDirname = $levelDirectoryName.$directoryName;

            $this->treeHTML .= '<option value="'.TGlobal::OutHTML($oCmsMediaTree->id).'" title="'.TGlobal::OutHTML($fullDirname)."\" {$selected}>".$paddingString.$symbol.TGlobal::OutHTML($directoryName)."</option>\n";
            $this->RenderMediaTree($oCmsMediaTree->id, $level, $fullDirname);
        }
    }

    /**
     * @param string $iTreeId
     */
    public function AddTree($iTreeId)
    {
        $oNode = new TCMSRecord();
        /* @var $oNode TCMSRecord */
        $oNode->table = 'cms_media_tree';
        $level = 1;
        $levelDirectoryName = '';
        if ($oNode->Load($iTreeId)) {
            $paddingString = '&nbsp;';
            if ($level > 0) {
                $paddingString = str_repeat('&nbsp;', $level);
            }
            $symbol = '';
            if ($level > 0) {
                $symbol = '&#187;&nbsp;';
            }
            if (!empty($levelDirectoryName)) {
                $levelDirectoryName .= ' > ';
            }

            $selected = '';
            if (!is_null($this->selectedID) && $this->selectedID == $oNode->id) {
                $selected = ' selected="selected"';
            }

            $directoryName = $oNode->sqlData['name'];
            $fullDirname = $levelDirectoryName.$directoryName;

            $this->treeHTML .= "<option value=\"{$oNode->id}\" title=\"".TGlobal::OutHTML($fullDirname)."\" {$selected}>".$paddingString.$symbol.TGlobal::OutHTML($directoryName)."</option>\n";
            $this->RenderMediaTree($iTreeId, 1);
        }
    }
}
