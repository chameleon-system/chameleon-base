<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TCMSRenderDocumentTreeSelectBox
{
    public $treeHTML = '';
    private $selectedID = null;

    public function TCMSRenderDocumentTreeSelectBox()
    {
    }

    public function GetTreeOptions($selectedID = null)
    {
        $this->selectedID = $selectedID;
        $this->RenderDocumentTree();

        return $this->treeHTML;
    }

    /**
     * renders one tree level as <option>.
     *
     * @param in  $parent_id
     * @param int $level
     */
    public function RenderDocumentTree($parent_id = 1, $level = 0)
    {
        /*
        IE7 has noe clue what to do with option padding... *snort*

        $padding = 5;
        for ($i=0;$i<$level;$i++) {
          $padding = $padding +10;
        }
        */

        $paddingString = '&nbsp;';
        for ($i = 0; $i < $level; ++$i) {
            $paddingString .= '&nbsp;&nbsp;';
        }

        ++$level;

        $query = "SELECT * FROM `cms_document_tree` WHERE `parent_id` = '".$parent_id."' ORDER BY `entry_sort`";
        $result = MySqlLegacySupport::getInstance()->query($query);

        $symbol = '';
        if ($level > 0) {
            $symbol = '&#187;&nbsp;';
        }

        while ($row = MySqlLegacySupport::getInstance()->fetch_assoc($result)) {
            $selected = '';
            if (!is_null($this->selectedID) && $this->selectedID == $row['id']) {
                $selected = ' selected="selected"';
            }

            $directoryName = $row['name'];
            $this->treeHTML .= "<option value=\"{$row['id']}\"{$selected}>".$paddingString.$symbol.$directoryName."</option>\n";
            $this->RenderDocumentTree($row['id'], $level);
        }
    }
}
