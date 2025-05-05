<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function gcf_FetchCMSUserById($id, $row)
{
    $name = ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.list.gcf_cms_user_unknown');
    if (!empty($id)) {
        $oCmsUser = TdbCmsUser::GetNewInstance();
        /* @var $oCmsUser TdbCmsUser */
        $oCmsUser->Load($id);
        if (!is_null($oCmsUser)) {
            $name = $oCmsUser->GetDisplayValue();
        }
    }

    return $name;
}
