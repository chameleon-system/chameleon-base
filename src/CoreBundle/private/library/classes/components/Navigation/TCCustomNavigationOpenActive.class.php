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
 * /**/
class TCCustomNavigationOpenActive extends TCCustomNavigation
{
    /**
     * {@inheritDoc}
     * show only the children that are part of the current path...
     */
    protected function _ShowChildren($oNode, $row = null, $level = null)
    {
        return parent::_ShowChildren($oNode, $row, $level) && $oNode->IsInBreadcrumb();
    }
}
