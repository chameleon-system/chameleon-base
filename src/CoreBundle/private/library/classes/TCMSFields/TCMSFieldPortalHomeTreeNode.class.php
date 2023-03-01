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
 *
 * {@inheritdoc}
 */
class TCMSFieldPortalHomeTreeNode extends TCMSFieldTreeNode
{
    // todo - doctrine transformation

    public function _GetOpenWindowJS()
    {
        $js = "loadHomeTreeNodeSelection('".TGlobal::OutHTML($this->name)."')";

        return $js;
    }
}
