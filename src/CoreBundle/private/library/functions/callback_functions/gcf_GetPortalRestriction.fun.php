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

/**
 * a table list restriction. it will restrict the field passed to all
 * portals assigned to the user.
 *
 * @param TCMSTableConf $oTableConf
 * @param TCMSRecord $oTableRestriction
 */
function gcf_GetPortalRestriction($oTableConf, $oTableRestriction)
{
    $sRestriction = '';
    /** @var SecurityHelperAccess $securityHelper */
    $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
    $portals = $securityHelper->getUser()?->getPortals();
    $sPortalList = implode(', ', array_map(static fn (string $portalId) => $portalId, array_keys($portals)));

    if (!empty($sPortalList)) {
        $sRestriction = "{$oTableRestriction->sqlData['name']} IN (".$sPortalList.')';
    }

    return $sRestriction;
}
