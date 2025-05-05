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
 * add a menu item to switch to any user if the current user has that right.
 * /**/
class TCMSTableEditorCMSUsergroup extends TCMSTableEditor
{
    /**
     * add table-specific buttons to the editor (add them directly to $this->oMenuItems).
     */
    protected function GetCustomMenuItems()
    {
        parent::GetCustomMenuItems();

        if ('1' == $this->oTable->sqlData['is_system']) {
            $this->oMenuItems->RemoveItem('sItemKey', 'copy');
            $this->oMenuItems->RemoveItem('sItemKey', 'delete');
        }
    }
}
