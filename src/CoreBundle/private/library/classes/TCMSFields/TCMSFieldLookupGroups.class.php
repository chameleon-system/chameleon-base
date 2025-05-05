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
 * a lookup field with special cases to prevent non admins to select groups they are not allowed to (admin).
 * /**/
class TCMSFieldLookupGroups extends TCMSFieldLookupMultiselectCheckboxes
{
    protected function GetMLTRecordRestrictions()
    {
        return parent::GetMLTRecordRestrictions()." AND `cms_usergroup`.`is_chooseable` = '1' ";
    }
}
