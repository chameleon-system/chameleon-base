<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;

class MTPkgTrackObjectViewsCore extends TUserCustomModelBase
{
    public function Execute()
    {
        parent::Execute();
        $isEnabled = ServiceLocator::getParameter('chameleon_system_track_views.enabled');
        if ($isEnabled) {
            if (false === $this->getRequestInfoService()->isCmsTemplateEngineEditMode()) {
                $oTracker = TPkgTrackObjectViews::GetInstance();
                $this->data['sTrackHTML'] = $oTracker->Render();
            } else {
                $this->data['sTrackHTML'] = '<!-- tracking pixel - disabled in cms backend -->';
            }
        } else {
            $this->data['sTrackHTML'] = '';
        }

        return $this->data;
    }

    private function getRequestInfoService(): RequestInfoServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.request_info_service');
    }
}
