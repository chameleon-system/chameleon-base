<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Field;

/**
 * Allows the selection of only the portal root tree nodes (level 1 of tree).
 *
 * {@inheritDoc}
 */
class FieldTreeNodePortalSelect extends \TCMSFieldTreeNode
{
    /**
     * {@inheritDoc}
     */
    public function _GetOpenWindowJS()
    {
        $url = PATH_CMS_CONTROLLER.'?pagedef=navigationTreeSingleSelect&fieldName='.urlencode($this->name).'&id='.urlencode($this->data).'&portalSelect=1';
        $js = "CreateModalIFrameDialogCloseButton('".\TGlobal::OutHTML($url)."')";

        return $js;
    }
}
