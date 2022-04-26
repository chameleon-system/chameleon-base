<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TShopOrderStepPkgExternalTracker extends TShopOrderStepPkgExternalTrackerAutoParent
{
    /**
     * method is called from the init method of the calling module. here you can check
     * if the step may be viewed, and redirect to another step if the user does not have permission.
     *
     * @return void
     */
    public function Init()
    {
        parent::Init();

        $oBasket = $this->getShopService()->getActiveBasket();

        // trigger tracker for basket view (used by Criteo tracker for example)
        if ('basket' == $this->fieldSystemname) {
            $oPkgExternalTracker = TdbPkgExternalTrackerList::GetActiveInstance();
            $oPkgExternalTracker->AddEvent(TPkgExternalTrackerState::EVENT_PKG_SHOP_BASKET_STEP, $oBasket);
        }
    }
}
