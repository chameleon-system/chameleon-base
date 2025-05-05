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
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;

class ActiveCmsUserPermission implements ActiveCmsUserPermissionInterface
{
    /**
     * @return bool
     */
    public function hasPermissionToExportTranslationDatabase()
    {
        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        return $securityHelper->isGranted(CmsUserRoleConstants::CMS_USER);
    }
}
