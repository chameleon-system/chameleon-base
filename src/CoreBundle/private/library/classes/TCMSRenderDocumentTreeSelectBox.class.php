<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use Doctrine\DBAL\Connection;

class TCMSRenderDocumentTreeSelectBox
{
    public $treeHTML = '';
    private $selectedID;

    /**
     * @param string|null $selectedID
     *
     * @return string
     */
    public function GetTreeOptions($selectedID = null)
    {
        $this->selectedID = $selectedID;
        $this->RenderDocumentTree();

        return $this->treeHTML;
    }

    /**
     * renders one tree level as <option>.
     *
     * @param string|int $parent_id
     * @param int $level
     */
    public function RenderDocumentTree($parent_id = 1, $level = 0)
    {
        $paddingString = '&nbsp;';
        for ($i = 0; $i < $level; ++$i) {
            $paddingString .= '&nbsp;&nbsp;';
        }

        ++$level;

        $quotedParentId = $this->getDatabaseConnection()->quote($parent_id);
        $query = "SELECT * FROM `cms_document_tree` WHERE `parent_id` = $quotedParentId ORDER BY `entry_sort`";
        $treeList = TdbCmsDocumentTreeList::GetList($query);

        $symbol = '';
        if ($level > 0) {
            $symbol = '&#187;&nbsp;';
        }

        while ($element = $treeList->Next()) {
            $selected = '';
            if (null !== $this->selectedID && $this->selectedID == $element->id) {
                $selected = 'selected="selected"';
            }

            $directoryName = $element->fieldName;
            $this->treeHTML .= sprintf('<option value="%s" %s>%s%s%s</option>\n', $element->id, $selected, $paddingString, $symbol, $directoryName);
            $this->RenderDocumentTree($element->id, $level);
        }
    }

    private function getDatabaseConnection(): Connection
    {
        return ServiceLocator::get('database_connection');
    }
}
