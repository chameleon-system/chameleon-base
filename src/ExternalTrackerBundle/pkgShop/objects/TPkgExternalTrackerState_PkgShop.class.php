<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class TPkgExternalTrackerState_PkgShop extends TPkgExternalTrackerState_PkgShopAutoParent
{
    const EVENT_PKG_SHOP_ADD_TO_BASKET = 'pkgShop__AddToBasket';
    const EVENT_PKG_SHOP_REMOVE_FROM_BASKET = 'pkgShop__RemoveFromBasket';
    const EVENT_PKG_SHOP_CREATE_ORDER = 'pkgShop__CreateOrder';
    const EVENT_PKG_SHOP_SEARCH = 'pkgShop__Search';
    const EVENT_PKG_SHOP_SEARCH_WITH_ITEMS = 'pkgShop__SearchItems';
    const EVENT_PKG_SHOP_PRODUCT_LIST = 'pkgShop__ProductCatalog';
    const EVENT_PKG_SHOP_BASKET_STEP = 'pkgShop__BasketStep';
}
