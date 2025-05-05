<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class MTPkgExternalTracker_MTShopBasketCore extends MTPkgExternalTracker_MTShopBasketCoreAutoParent
{
    /**
     * hook ist called after a successfull change in the basket... oArticle is what was added/removed from the basket.
     *
     * @param TShopBasketArticle $oArticle
     * @param bool $bItemWasUpdated
     *
     * @return void
     */
    protected function PostUpdateItemInBasketEvent($oArticle, $bItemWasUpdated)
    {
        parent::PostUpdateItemInBasketEvent($oArticle, $bItemWasUpdated);
        if ($oArticle->dAmount > 0) {
            TdbPkgExternalTrackerList::GetActiveInstance()->AddEvent(TPkgExternalTrackerState::EVENT_PKG_SHOP_ADD_TO_BASKET, $oArticle);
        } else {
            TdbPkgExternalTrackerList::GetActiveInstance()->AddEvent(TPkgExternalTrackerState::EVENT_PKG_SHOP_REMOVE_FROM_BASKET, $oArticle);
        }
    }

    /**
     * @param TShopBasketArticle $oArticle
     *
     * @return void
     */
    protected function PostRemoveItemInBasketHook($oArticle)
    {
        parent::PostRemoveItemInBasketHook($oArticle);
        TdbPkgExternalTrackerList::GetActiveInstance()->AddEvent(TPkgExternalTrackerState::EVENT_PKG_SHOP_REMOVE_FROM_BASKET, $oArticle);
    }
}
