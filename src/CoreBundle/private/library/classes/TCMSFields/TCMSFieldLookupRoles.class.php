<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;

/**
 * a lookup field with special cases to prevent non admins to select roles they are not allowed to.
 * /**/
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
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        if ($securityHelper->isGranted(CmsUserRoleConstants::CMS_ADMIN)) {
            return $data;
        }

        $roles = $securityHelper->getUser()?->getRoles();
        if (null === $roles) {
            $roles = [];
        }
        $roles = array_keys($roles);

        foreach ($data as $key => $record) {
            if (false === $record['editable']) {
                continue;
            }
            $data[$key]['editable'] = in_array($record['id'], $roles, true);
        }

        return $data;
    }
}
