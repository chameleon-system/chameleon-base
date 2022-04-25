<?php declare(strict_types=1);

namespace ChameleonSystem\ExternalTrackerGoogleAnalyticsBundle\Bridge\Chameleon\ExternalTracker;

use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\ShopCurrencyBundle\Interfaces\ShopCurrencyServiceInterface;
use TdbPkgExternalTracker;
use TdbShopOrder;
use TdbShopOrderItem;
use TGlobal;
use TPkgExternalTrackerState;
use TPkgExternalTrackerState_PkgShop;
use TShopBasketArticle;

class ExternalTrackerGoogleAnalyticsGa4 extends TdbPkgExternalTracker
{

    /**
     * Set to true to enable debug mode. This will make events sent from the tracker
     * visible in google analytics DebugView (Configure > DebugView in GA dashboard)
     * @var bool
     */
    private $debugMode = false;

    public function GetPreBodyClosingCode(TPkgExternalTrackerState $state)
    {
        $lines = parent::GetPreBodyClosingCode($state);
        $identifier = $this->getIdentifierCode($state);
        if (null === $identifier) {
            return $lines;
        }
        $quotedIdentifier = TGlobal::OutJS($identifier);

        $config = [];
        if ($this->debugMode) {
            $config['debug_mode'] = true;
        }

        $events = [];
        if ($state instanceof \TPkgExternalTrackerState_PkgShop) {
            $events[] = $this->getBasketEvent($state);
            $events[] = $this->getOrderCompleteEvent($state);
        }

        $lines[] = sprintf('
        <script async src="https://www.googletagmanager.com/gtag/js?id=%1$s"></script>
        <script>
          window.dataLayer = window.dataLayer || [];
          function gtag(){dataLayer.push(arguments);}
          gtag("js", new Date());
        
          gtag("config", "%1$s", %2$s);
          
          %3$s
        </script>
        ', $quotedIdentifier, json_encode($config), implode("\n", $events));

        return $lines;
    }

    /**
     * Get identifier from active portal with fallback to tracker identifier.
     */
    protected function getIdentifierCode(TPkgExternalTrackerState $state): ?string
    {
        $identifier = $this->fieldIdentifier;
        $page = $state->GetActivePage();
        if (null === $page) {
            return $identifier;
        }
        $portal = $page->GetPortal();
        if ('' === $portal->fieldGoogleAnalyticNumber) {
            return $identifier;
        }

        return $portal->fieldGoogleAnalyticNumber;
    }


    /**
     * Add to basket and remove from basket use the same payload and only one of them can happen at a time, so they
     * are handled together.
     * @see https://developers.google.com/analytics/devguides/collection/ga4/ecommerce?client_type=gtag#add_to_cart
     *
     * @param TPkgExternalTrackerState&TPkgExternalTrackerState_PkgShop $state
     */
    protected function getBasketEvent(TPkgExternalTrackerState $state): string
    {
        /** @var TShopBasketArticle|false $event */
        $event = false;
        if ($state->HasEvent(TPkgExternalTrackerState::EVENT_PKG_SHOP_ADD_TO_BASKET)) {
            $gaEvent = 'add_to_cart';
            $event = $state->GetEventData(TPkgExternalTrackerState::EVENT_PKG_SHOP_ADD_TO_BASKET);
        } elseif ($state->HasEvent(TPkgExternalTrackerState::EVENT_PKG_SHOP_REMOVE_FROM_BASKET)) {
            $gaEvent = 'remove_from_cart';
            $event = $state->GetEventData(TPkgExternalTrackerState::EVENT_PKG_SHOP_REMOVE_FROM_BASKET);
        }

        if (false === $event) {
            return '';
        }

        $manufacturer = $event->GetFieldShopManufacturer();
        $category = $event->GetFieldShopCategory();

        $data = [
            'value' => (float) $event->fieldPrice,
            'currency' => $this->getActiveCurrencyCode(),
            'items' => [
                'item_id' => $event->fieldArticlenumber,
                'item_name' => $event->fieldName,
                'item_brand' => null !== $manufacturer ? $manufacturer->fieldName : null,
                'item_category' => null !== $category ? $category->fieldName : null,
                'price' => (float) $event->fieldPrice,
                'currency' => $this->getActiveCurrencyCode(),
                'quantity' => (float) $event->fieldQuantityInUnits,
            ]
        ];

        return sprintf('gtag("event", "%s", %s);', $gaEvent, json_encode($data));
    }

    /**
     * @param TPkgExternalTrackerState&TPkgExternalTrackerState_PkgShop $state
     */
    protected function getOrderCompleteEvent(TPkgExternalTrackerState $state): string
    {

        /** @var TdbShopOrder|false $orderEvent */
        $orderEvent = $state->GetEventData(TPkgExternalTrackerState::EVENT_PKG_SHOP_CREATE_ORDER);
        if (false === $orderEvent) {
            return '';
        }

        $data = [
            'transaction_id' => $orderEvent->fieldOrdernumber,
            'affiliation' => $orderEvent->fieldAffiliateCode,
            'value' => (float) $orderEvent->fieldValueTotal,
            'tax' => (float) $orderEvent->fieldValueVatTotal,
            'shipping' => (float) $orderEvent->fieldShopShippingGroupPrice,
            'currency' => $orderEvent->GetFieldPkgShopCurrency()->fieldIso4217,
            'coupon' => $orderEvent->fieldShopOrderDiscount,
            'items' => []
        ];

        foreach ($orderEvent->GetFieldShopOrderItemList() as $i => $orderItem) {
            /** @var TdbShopOrderItem $orderItem */

            $article = $orderItem->GetFieldShopArticle();
            $category = null !== $article ? $article->GetFieldShopCategory() : null;

            $data['items'][] = [
                'index' => $i,
                'item_id' => $orderItem->fieldArticlenumber,
                'item_name' => $orderItem->fieldName,
                'item_brand' => $orderItem->GetFieldShopManufacturer()->fieldName,
                'item_category' => null !== $category ? $category->fieldName : null,
                'affiliation' => $orderEvent->fieldAffiliateCode,
                'price' => (float) $orderItem->fieldPrice,
                'quantity' => (float) $orderItem->fieldQuantityInUnits,
                'discount' => (float) ($orderItem->fieldPrice - $orderItem->fieldPriceDiscounted),
            ];
        }

        return sprintf('gtag("event", "purchase", %s);', json_encode($data));
    }

    private function getActiveCurrencyCode(): ?string
    {
        $id = $this->getCurrencyService()->getActiveCurrencyId();
        if (null === $id) {
            return null;
        }

        $currency = \TdbPkgShopCurrency::GetNewInstance();
        if (false === $currency->Load($id)) {
            return null;
        }

        return $currency->fieldIso4217;
    }

    private function getCurrencyService(): ShopCurrencyServiceInterface
    {
        return ServiceLocator::get('chameleon_system_shop_currency.shop_currency');
    }
}
