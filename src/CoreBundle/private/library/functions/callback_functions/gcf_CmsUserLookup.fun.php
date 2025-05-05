<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function gcf_CmsUserLookup($field, $row, $fieldName)
{
    static $aCMSUserLookup = [];
    $sText = ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.list.gcf_cms_user_unknown');
    if (!empty($field)) {
        if (!array_key_exists($field, $aCMSUserLookup)) {
            $oUser = TdbCmsUser::GetNewInstance();
            if ($oUser->Load($field)) {
                $aCMSUserLookup[$field] = $oUser->GetName();
            }
        }
        $sText = TGlobal::OutHTML($aCMSUserLookup[$field]);
    }

    return $sText;
}
