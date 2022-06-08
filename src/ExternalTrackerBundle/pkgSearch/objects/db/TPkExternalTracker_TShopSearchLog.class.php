<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkExternalTracker_TShopSearchLog extends TPkExternalTracker_TShopSearchLogAutoParent
{
    /**
     * @return void
     */
    protected function PostInsertHook()
    {
        parent::PostInsertHook();
        TdbPkgExternalTrackerList::GetActiveInstance()->AddEvent(TPkgExternalTrackerState::EVENT_PKG_SHOP_SEARCH, $this);
    }
}
