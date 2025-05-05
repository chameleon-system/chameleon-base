<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

function gcf_ActivityFeedName($name, $row)
{
    $oNotify = TdbPkgComActivityFeedNotify::GetNewInstance();
    if ($oNotify->Load($row['pkg_com_activity_feed_notify_id'])) {
        return $oNotify->GetName();
    } else {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('translator')->trans('chameleon_system_core.list.gcf_activity_feed_name_no_feed_found');
    }
}
