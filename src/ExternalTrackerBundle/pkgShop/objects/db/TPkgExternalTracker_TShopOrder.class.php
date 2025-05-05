<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgExternalTracker_TShopOrder extends TPkgExternalTracker_TShopOrderAutoParent
{
    /**
     * method is called after all data from the basket has been saved to the order tables.
     *
     * @return void
     */
    public function CreateOrderInDatabaseCompleteHook()
    {
        parent::CreateOrderInDatabaseCompleteHook();
        if (false == $this->fieldCanceled) {
            $oCopyOfOrder = clone $this;
            unset($oCopyOfOrder->fieldObjectMail);
            unset($oCopyOfOrder->sqlData['object_mail']);
            unset($oCopyOfOrder->aResultCache);
            $oCopyOfOrder->aResultCache = [];
            TdbPkgExternalTrackerList::GetActiveInstance()->AddEvent(TPkgExternalTrackerState::EVENT_PKG_SHOP_CREATE_ORDER, $oCopyOfOrder);
        }
    }
}
