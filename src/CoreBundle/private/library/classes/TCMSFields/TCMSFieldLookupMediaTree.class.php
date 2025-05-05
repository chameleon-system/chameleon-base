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
 * lookup.
 * /**/
class TCMSFieldLookupMediaTree extends TCMSFieldLookup
{
    protected function GetOptionsQuery()
    {
        $query = "SELECT * FROM `cms_media_tree` WHERE `parent_id` != '0' AND `parent_id` != '' ORDER BY `parent_id`,`entry_sort`";

        return $query;
    }

    public function GetHTML()
    {
        $escapedName = TGlobal::OutHTML($this->name);

        $html = "<div>\n";
        $selectTag = '<select name="%s" id="%s" class="form-control form-control-sm" data-select2-option=\'{"width": "100%"}\' >';
        $html .= sprintf($selectTag, $escapedName, $escapedName);

        $oTreeSelect = new TCMRenderMediaTreeSelectBox();

        $html .= $oTreeSelect->GetTreeOptions();
        $html .= "</select>\n";
        $html .= "</div>\n";

        return $html;
    }
}
