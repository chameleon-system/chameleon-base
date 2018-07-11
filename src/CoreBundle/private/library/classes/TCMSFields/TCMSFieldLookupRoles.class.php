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
 * a lookup field with special cases to prevent non admins to select roles they are not allowed to.
/**/
class TCMSFieldLookupRoles extends TCMSFieldLookupMultiselectCheckboxes
{
    protected function GetMLTRecordRestrictions()
    {
        return parent::GetMLTRecordRestrictions()." AND `cms_role`.`is_chooseable` = '1' ";
    }

    /**
     * {@inheritdoc}
     */
    protected function getMltRecordData($listGroupFieldColumn)
    {
        $data = parent::getMltRecordData($listGroupFieldColumn);
        $activeUser = &TCMSUser::GetActiveUser();
        if ($activeUser->oAccessManager->user->IsAdmin()) {
            return $data;
        }

        $rolesOfActiveUser = $activeUser->GetFieldCmsRoleIdList();
        foreach ($data as &$record) {
            if (false === $record['editable']) {
                continue;
            }
            $record['editable'] = in_array($record['id'], $rolesOfActiveUser, true);
        }

        return $data;
    }
}
