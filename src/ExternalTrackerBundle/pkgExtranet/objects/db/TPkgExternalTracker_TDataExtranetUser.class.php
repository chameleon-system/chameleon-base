<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgExternalTracker_TDataExtranetUser extends TPkgExternalTracker_TDataExtranetUserAutoParent
{
    /**
     * @return void
     */
    protected function PostLoginHook()
    {
        parent::PostLoginHook();
        TdbPkgExternalTrackerList::GetActiveInstance()->AddEvent(TPkgExternalTrackerState::EVENT_EXTRANET_LOGIN, $this);
    }

    /**
     * @return void
     */
    protected function PostInsertHook()
    {
        parent::PostInsertHook();
        TdbPkgExternalTrackerList::GetActiveInstance()->AddEvent(TPkgExternalTrackerState::EVENT_EXTRANET_REGISTRATION, $this);
    }
}
