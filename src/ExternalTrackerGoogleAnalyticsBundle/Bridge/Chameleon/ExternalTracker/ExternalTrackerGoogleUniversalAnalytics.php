<?php

namespace ChameleonSystem\ExternalTrackerGoogleAnalyticsBundle\Bridge\Chameleon\ExternalTracker;

/**
 * @deprecated Google Universal Analytics will be removed in July 2023. Use {@see ExternalTrackerGoogleAnalyticsGa4} instead.
 */
class ExternalTrackerGoogleUniversalAnalytics extends \TdbPkgExternalTracker
{
    /**
     * {@inheritdoc}
     */
    public function GetPreBodyClosingCode(\TPkgExternalTrackerState $oState)
    {
        $lines = parent::GetPreBodyClosingCode($oState);
        $identifier = $this->getIdentifierCode($oState);

        if ('' === $identifier) {
            return $lines;
        }

        $quotedIdentifier = \TGlobal::OutJS($identifier);

        $orderComplete = '';
        if (is_a($oState, 'TPkgExternalTrackerState_PkgShop')) {
            $orderComplete = $this->getOrderCompleteEvent($oState);
        }

        $events = $this->getEvents($oState);

        $googleCode = sprintf(
            "
<script>

    (function($, window, document, undefined) {
        var pluginName = 'esonoGaOptoutOptin';
        var analyticsId = '%s';
        var disableString = 'ga-disable-' + analyticsId;
        
        function Plugin(element) {
            this.element = $(element);
            this.optOutElement = this.element.find('.ga-optout-link');
            this.optInElement = this.element.find('.ga-optin-link');
            if (0 === this.optOutElement.length || 0 === this.optInElement.length) {
                return;
            }
            this._name = pluginName;
            this.init();
        }
        
        $.extend(Plugin.prototype, {
            init: function () {
                this.optOutElement.on('click', { plugin: this }, function (event) {
                    event.data.plugin.optOut();
                });
                this.optInElement.on('click', { plugin: this }, function (event) {
                    event.data.plugin.optIn();
                });
                if(this.isOptOut()) {
                    this.optOut();
                } else {
                    this.optIn();
                }
                this.element.css('display', '');
            },
            isOptOut: function () {
                return document.cookie.indexOf(disableString + '=true') > -1;
            },
            optIn: function () {
                document.cookie = disableString + '=true; expires=Thu, 01 Jan 1970 00:00:01 GMT; path=/';
                window[disableString] = false;
                this.optOutElement.css('display', '');
                this.optInElement.css('display', 'none');
            },
            optOut: function () {
                document.cookie = disableString + '=true; expires=Thu, 31 Dec 2099 23:59:59 UTC; path=/';
                window[disableString] = true;
                this.optOutElement.css('display', 'none');
                this.optInElement.css('display', '');
            }
        });
        
        $.fn[pluginName] = function() {
            return this.each(function() {
                if (!$.data(this, 'plugin_' + pluginName)) {
                    $.data(this, 'plugin_' + pluginName, new Plugin(this));
                }
            });
        }
    }(jQuery, window, document));
    
    $('.ga-optout-optin-link').esonoGaOptoutOptin();
    
    (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
    (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
    m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
    })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
    
    ga('create', '%s', 'auto');
    ga('set', 'anonymizeIp', true);
    ga('send', 'pageview');
    %s
    %s
</script>", $quotedIdentifier, $quotedIdentifier, $events, $orderComplete);

        $lines[] = $googleCode;

        return $lines;
    }

    /**
     * Get identifier from active portal with fallback to tracker identifier.
     *
     * @return string|null
     */
    protected function getIdentifierCode(\TPkgExternalTrackerState $state)
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
     * @return string
     */
    protected function getEvents(\TPkgExternalTrackerState $state)
    {
        $events = [];
        $addToBasketEvent = $this->getAddToBasketEvent($state);
        if (null !== $addToBasketEvent) {
            $events[] = $addToBasketEvent;
        }

        return implode("\n", $events);
    }

    /**
     * @return string|null
     */
    protected function getAddToBasketEvent(\TPkgExternalTrackerState $state)
    {
        $event = null;
        if (is_a($state, 'TPkgExternalTrackerState_PkgShop')) {
            // basket event

            /** @var \TShopBasketArticle|false $addToBasketEvent */
            $addToBasketEvent = $state->GetEventData(\TPkgExternalTrackerState::EVENT_PKG_SHOP_ADD_TO_BASKET);

            if (false !== $addToBasketEvent) {
                $event = sprintf("ga('send', 'event', 'products', 'AddToBasket', '%s','%s');",
                    \TGlobal::OutJS($addToBasketEvent->fieldArticlenumber),
                    \TGlobal::OutJS($addToBasketEvent->dAmount));
            }
        }

        return $event;
    }

    /**
     * @return string
     */
    protected function getOrderCompleteEvent(\TPkgExternalTrackerState $state)
    {
        $html = '';
        /** @var \TdbShopOrder|false $orderEvent */
        $orderEvent = $state->GetEventData(\TPkgExternalTrackerState::EVENT_PKG_SHOP_CREATE_ORDER);

        if (false !== $orderEvent) {
            $htmlLines = [];
            $htmlLines[] = "ga('require', 'ecommerce');";

            $htmlLines[] = $this->getOrderCompleteEventOrderDetails($orderEvent);
            $orderItems = $orderEvent->GetFieldShopOrderItemList();
            $orderItems->GoToStart();
            while ($orderItem = $orderItems->Next()) {
                $htmlLines[] = $this->getOrderCompleteEventOrderItemDetails($orderEvent, $orderItem);
            }
            $htmlLines[] = "ga('ecommerce:send');";

            $html = implode("\n", $htmlLines);
        }

        return $html;
    }

    /**
     * @return string
     */
    protected function getOrderCompleteEventOrderDetails(\TdbShopOrder $orderEvent)
    {
        $params = [
            'id' => $orderEvent->fieldOrdernumber,
            'affiliation' => $orderEvent->fieldAffiliateCode,
            'revenue' => $orderEvent->fieldValueTotal,
            'tax' => $orderEvent->fieldValueVatTotal,
            'shipping' => $orderEvent->fieldShopShippingGroupPrice,
        ];

        return sprintf("ga('ecommerce:addTransaction', %s );", json_encode($params));
    }

    /**
     * @return string
     */
    protected function getOrderCompleteEventOrderItemDetails(\TdbShopOrder $orderEvent, \TdbShopOrderItem $orderItem)
    {
        $dUnitPrice = $orderItem->fieldOrderPriceAfterDiscounts / $orderItem->fieldOrderAmount;

        $params = [
            'id' => $orderEvent->fieldOrdernumber,
            'sku' => $orderItem->fieldArticlenumber,
            'name' => $orderItem->fieldName,
            'category' => $orderItem->fieldNameVariantInfo,
            'price' => $dUnitPrice,
            'quantity' => $orderItem->fieldOrderAmount,
        ];

        return sprintf("ga('ecommerce:addItem', %s );", json_encode($params));
    }
}
