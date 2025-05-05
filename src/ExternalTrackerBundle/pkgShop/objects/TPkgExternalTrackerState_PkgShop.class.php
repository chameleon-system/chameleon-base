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
    public const EVENT_PKG_SHOP_ADD_TO_BASKET = 'pkgShop__AddToBasket';
    public const EVENT_PKG_SHOP_REMOVE_FROM_BASKET = 'pkgShop__RemoveFromBasket';
    public const EVENT_PKG_SHOP_CREATE_ORDER = 'pkgShop__CreateOrder';
    public const EVENT_PKG_SHOP_SEARCH = 'pkgShop__Search';
    public const EVENT_PKG_SHOP_SEARCH_WITH_ITEMS = 'pkgShop__SearchItems';
    public const EVENT_PKG_SHOP_PRODUCT_LIST = 'pkgShop__ProductCatalog';
    public const EVENT_PKG_SHOP_BASKET_STEP = 'pkgShop__BasketStep';
}
