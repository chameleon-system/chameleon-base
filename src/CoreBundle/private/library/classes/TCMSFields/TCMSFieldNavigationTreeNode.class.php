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
/**/
class TCMSFieldNavigationTreeNode extends TCMSFieldTreeNode
{
    public function _GetOpenWindowJS()
    {
        $url = PATH_CMS_CONTROLLER.'?pagedef=treenodeselect&fieldName='.urlencode($this->name).'&id='.urlencode($this->data).'&portalID='.$this->oTableRow->sqlData['cms_portal_id'];
        $js = "CreateModalIFrameDialogCloseButton('".TGlobal::OutHTML($url)."')";

        return $js;
    }
}
