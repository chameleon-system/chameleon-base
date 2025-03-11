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
 * picks a node from a tree, adds cms_portal_id to tree.
 */
class TCMSFieldNavigationTreeNode extends TCMSFieldTreeNode
{
    public function _GetOpenWindowJS()
    {
        $url = PATH_CMS_CONTROLLER.'?pagedef=navigationTreeSingleSelect&fieldName='.urlencode($this->name).
            '&id='.urlencode($this->data).
            '&portalId='.$this->oTableRow->sqlData['cms_portal_id'].
            '&tableName='.$this->sTableName.
            '&currentRecordId='.$this->recordId;

        return "CreateModalIFrameDialogCloseButton('".TGlobal::OutHTML($url)."')";
    }
}
