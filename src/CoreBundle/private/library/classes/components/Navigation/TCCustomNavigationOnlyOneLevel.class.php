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
 * show only the root nodes of the navi.
 * /**/
class TCCustomNavigationOnlyOneLevel extends TCCustomNavigation
{
    /**
     * {@inheritDoc}
     * show only the children that are part of the current path...
     */
    protected function _ShowChildren($oNode, $row = null, $level = null)
    {
        return false;
    }
}
