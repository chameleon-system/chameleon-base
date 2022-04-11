<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class MTPkgExternalTracker_MTFeedbackCore extends MTPkgExternalTracker_MTFeedbackCoreAutoParent
{
    /**
     * hook to do things after the email was send successfully (e.g. save data to db).
     *
     * @return void
     */
    public function AfterSendEMailSuccess()
    {
        parent::AfterSendEMailSuccess();

        $oPkgExternalTracker = TdbPkgExternalTrackerList::GetActiveInstance();
        $oPkgExternalTracker->AddEvent(TPkgExternalTrackerState::EVENT_CONTACT_FORM_SUBMIT);
    }
}
