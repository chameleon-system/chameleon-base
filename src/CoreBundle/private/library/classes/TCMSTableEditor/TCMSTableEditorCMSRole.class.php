<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Doctrine\DBAL\Exception as DBALException;

/**
 * add a menu item to switch to any user if the current user has that right.
 */
class TCMSTableEditorCMSRole extends TCMSTableEditor
{
    protected function PostSaveHook($oFields, $oPostTable)
    {
        parent::PostSaveHook($oFields, $oPostTable);
        $this->rightsChanged();
    }

    /**
     * @see \ChameleonSystem\SecurityBundle\CmsUser\CmsUserDataAccess::userHasBeenModified
     */
    protected function rightsChanged(): void
    {
        $query = 'UPDATE `cms_user`
            INNER JOIN `cms_user_cms_role_mlt` ON `cms_user_cms_role_mlt`.source_id = `cms_user`.`id`
            SET `date_modified` = :date_time
            WHERE `cms_user_cms_role_mlt`.`target_id` = :role_id';

        try {
            $this->getDatabaseConnection()->executeQuery($query, ['date_time' => date('Y-m-d H:i:s'), 'role_id' => $this->sId]);
        } catch (DBALException) {
            return;
        }
    }

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
