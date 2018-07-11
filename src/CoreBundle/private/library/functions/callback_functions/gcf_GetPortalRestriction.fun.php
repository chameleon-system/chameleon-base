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
 * a table list restriction. it will restrict the field passed to all
 * portals assigned to the user.
 *
 * @param TCMSTableConf $oTableConf
 * @param TCMSRecord    $oTableRestriction
 */
function gcf_GetPortalRestriction(&$oTableConf, &$oTableRestriction)
{
    $sRestriction = '';
    $oGlobal = TGlobal::instance();
    $sPortalList = $oGlobal->oUser->oAccessManager->user->portals->PortalList();
    if (!empty($sPortalList)) {
        $sRestriction = "{$oTableRestriction->sqlData['name']} IN (".$sPortalList.')';
    }

    return $sRestriction;
}
