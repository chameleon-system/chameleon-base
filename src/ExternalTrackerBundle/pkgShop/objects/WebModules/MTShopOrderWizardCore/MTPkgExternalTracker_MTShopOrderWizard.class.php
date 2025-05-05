<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class MTPkgExternalTracker_MTShopOrderWizard extends MTPkgExternalTracker_MTShopOrderWizardAutoParent
{
    public function Init()
    {
        parent::Init();
        TdbPkgExternalTrackerList::GetActiveInstance()->AddStateData('oShopOrderStep', ['oStep' => $this->oActiveOrderStep, 'oStepList' => TdbShopOrderStepList::GetNavigationStepList($this->oActiveOrderStep)]);
    }
}
