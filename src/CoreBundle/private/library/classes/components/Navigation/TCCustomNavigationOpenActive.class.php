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
 * only show the children of the active node.
/**/
class TCCustomNavigationOpenActive extends TCCustomNavigation
{
    /**
     * show only the children that are part of the current path...
     *
     * @param TCMSTreeNode $oNode - parent node
     * @param $row - row within current submenu
     * @param $level - level within navi of the parent node
     *
     * @return bool
     */
    protected function _ShowChildren(&$oNode, $row = null, $level = null)
    {
        return parent::_ShowChildren($oNode, $row, $level) && $oNode->IsInBreadcrumb();
    }
}
