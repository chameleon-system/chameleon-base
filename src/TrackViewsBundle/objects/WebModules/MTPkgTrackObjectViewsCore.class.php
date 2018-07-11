<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class MTPkgTrackObjectViewsCore extends TUserCustomModelBase
{
    public function &Execute()
    {
        parent::Execute();
        $isEnabled = \ChameleonSystem\CoreBundle\ServiceLocator::getParameter('chameleon_system_track_views.enabled');
        if ($isEnabled) {
            if (false == TGlobal::IsCMSTemplateEngineEditMode()) {
                $oTracker = &TPkgTrackObjectViews::GetInstance();
                $this->data['sTrackHTML'] = $oTracker->Render();
            } else {
                $this->data['sTrackHTML'] = '<!-- tracking pixel - disabled in cms backend -->';
            }
        } else {
            $this->data['sTrackHTML'] = '';
        }

        return $this->data;
    }
}
