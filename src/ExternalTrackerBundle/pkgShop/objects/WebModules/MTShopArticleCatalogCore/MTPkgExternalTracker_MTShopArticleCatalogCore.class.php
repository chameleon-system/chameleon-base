<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * @deprecated since 6.2.0 - no longer used.
 */
class MTPkgExternalTracker_MTShopArticleCatalogCore extends MTPkgExternalTracker_MTShopArticleCatalogCoreAutoParent
{
    /**
     * load the article list and store it in $this->oList.
     *
     * @return void
     */
    protected function LoadArticleList()
    {
        parent::LoadArticleList();

        $oPkgExternalTracker = TdbPkgExternalTrackerList::GetActiveInstance();
        $oPkgExternalTracker->AddEvent(TPkgExternalTrackerState::EVENT_PKG_SHOP_PRODUCT_LIST, $this->oList);
    }
}
