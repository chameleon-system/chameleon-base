<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\i18n;

use ChameleonSystem\CoreBundle\i18n\Interfaces\ActiveCmsUserPermissionInterface;

class ActiveCmsUserPermission implements ActiveCmsUserPermissionInterface
{
    /**
     * @return bool
     */
    public function hasPermissionToExportTranslationDatabase()
    {
        $activeUser = \TdbCmsUser::GetActiveUser();

        return $activeUser && true === $activeUser->bLoggedIn;
    }
}
