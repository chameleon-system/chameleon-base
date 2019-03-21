<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

if (false === defined('PKG_EXTERNAL_TRACKER_GOOGLE_ANALYTICS_ENABLE_CROSS_DOMAIN_TRACKING')) {
    define('PKG_EXTERNAL_TRACKER_GOOGLE_ANALYTICS_ENABLE_CROSS_DOMAIN_TRACKING', false);
}

/**
 * @deprecated since 6.1.9 - this version of Google Analytics is outdated.
 * Please use ExternalTrackerGoogleUniversalAnalytics instead.
 */
class TPkgExternalTrackerGoogleAnalytics extends TdbPkgExternalTracker
{
    /**
     * @param TPkgExternalTrackerState $oState
     *
     * @return array
     */
    public function GetHTMLHeadIncludes(TPkgExternalTrackerState $oState)
    {
        return array();
    }

    /**
     * @param TPkgExternalTrackerState $oState
     *
     * @return array
     */
    public function GetPostBodyOpeningCode(TPkgExternalTrackerState $oState)
    {
        return array();
    }

    /**
     * @param TPkgExternalTrackerState $oState
     *
     * @return array
     */
    public function GetPreBodyClosingCode(TPkgExternalTrackerState $oState)
    {
        $aLines = parent::GetPreBodyClosingCode($oState);

        $sOrderComplete = '';
        if (is_a($oState, 'TPkgExternalTrackerState_PkgShop')) {
            $sOrderComplete = $this->GetOrderCompleteEvent($oState);
        }
        $sIdentifier = $this->getIdentifierCode($oState);
        $sEvents = $this->GetEvents($oState);
        $sAdditionalGoogleData = $this->GetAdditionalGoogleReportData($oState);
        $sGoogleCode = "<script type=\"text/javascript\">
        var _gaq = _gaq || [];
        _gaq.push(['_setAccount', '".TGlobal::OutJS($sIdentifier)."']);
        _gaq.push(['_gat._anonymizeIp']);
        _gaq.push(['_trackPageview']);

        {$sEvents}
        {$sOrderComplete}
        {$sAdditionalGoogleData}

        (function() {
          var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
          ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
          var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
        })();
       </script>
       ";
        $aLines[] = $sGoogleCode;

        return $aLines;
    }

    /**
     * Get Identifier from active portal with fallback to tracker identifier.
     *
     * @param TPkgExternalTrackerState $oState
     *
     * @return string|null
     */
    protected function getIdentifierCode(TPkgExternalTrackerState $oState)
    {
        $sIdentifier = $this->fieldIdentifier;
        // check if the code is set @portal - if so, use that
        $oPage = $oState->GetActivePage();
        if ($oPage && !empty($oPage->oActivePortal->fieldGoogleAnalyticNumber)) {
            $sIdentifier = $oPage->oActivePortal->fieldGoogleAnalyticNumber;
        }

        return $sIdentifier;
    }

    /**
     * use the hook to add data to _gaq (variables pushed to google) - called by GetPreBodyClosingCode.
     *
     * @param TPkgExternalTrackerState $oState
     *
     * @return string
     */
    protected function GetAdditionalGoogleReportData(TPkgExternalTrackerState $oState)
    {
        $aAdditionalData = array();
        if (true === PKG_EXTERNAL_TRACKER_GOOGLE_ANALYTICS_ENABLE_CROSS_DOMAIN_TRACKING) {
            $oURLData = TCMSSmartURLData::GetActive();
            $aAdditionalData[] = "_gaq.push(['_setDomainName', '".TGlobal::OutJS($oURLData->sOriginalDomainName)."']);";
        }

        return implode("\n", $aAdditionalData);
    }

    /**
     * @param TPkgExternalTrackerState $oState
     *
     * @return string
     */
    protected function GetEvents(TPkgExternalTrackerState $oState)
    {
        $aEvents = array();

        if (is_a($oState, 'TPkgExternalTrackerState_PkgShop')) {
            // basket event
            $oAddToBasketEvent = $oState->GetEventData(TPkgExternalTrackerState::EVENT_PKG_SHOP_ADD_TO_BASKET);
            if ($oAddToBasketEvent) {
                /** @var $oAddToBasketEvent TShopBasketArticle */
                $aEvents[] = "_gaq.push(['_trackEvent', 'products', 'AddToBasket', '".TGlobal::OutJS($oAddToBasketEvent->fieldArticlenumber)."', ".TGlobal::OutJS($oAddToBasketEvent->dAmount).']);';
            }
        }

        return implode("\n", $aEvents);
    }

    /**
     * @param TPkgExternalTrackerState $oState
     *
     * @return string
     */
    protected function GetOrderCompleteEvent(TPkgExternalTrackerState $oState)
    {
        $sHTML = '';
        $oOrderEvent = $oState->GetEventData(TPkgExternalTrackerState::EVENT_PKG_SHOP_CREATE_ORDER);
        if ($oOrderEvent) {
            /** @var $oOrderEvent TdbShopOrder */
            $aHTMLLines = array();
            $aHTMLLines[] = $this->GetOrderCompleteEventOrderDetails($oOrderEvent);
            $oOrderItems = $oOrderEvent->GetFieldShopOrderItemList();
            /** @var $oOrderItems TdbShopOrderItemList */
            $oOrderItems->GoToStart();
            while ($oOrderItem = $oOrderItems->Next()) {
                /** @var $oOrderItem TdbShopOrderItem */
                $aHTMLLines[] = $this->GetOrderCompleteEventOrderItemDetails($oOrderEvent, $oOrderItem);
            }
            $aHTMLLines[] = "_gaq.push(['_trackTrans']);";

            $sHTML = implode("\n", $aHTMLLines);
        }

        return $sHTML;
    }

    /**
     * @param TdbShopOrder $oOrderEvent
     *
     * @return string
     */
    protected function GetOrderCompleteEventOrderDetails($oOrderEvent)
    {
        $sCountryCode = '';
        $oCountry = $oOrderEvent->GetFieldAdrBillingCountry();
        if ($oCountry) {
            $oSystemCountry = $oCountry->GetFieldTCountry();
            /** @var $oSystemCountry TdbTCountry */
            if ($oSystemCountry) {
                $sCountryCode = $oSystemCountry->fieldGermanName;
            }
        }

        return sprintf(
            "_gaq.push(['_addTrans', '%s', '%s', '%s', '%s', '%s', '%s', '', '%s' ]);",
            TGlobal::OutJS($oOrderEvent->fieldOrdernumber),
            TGlobal::OutJS($oOrderEvent->fieldAffiliateCode),
            TGlobal::OutJS($oOrderEvent->fieldValueTotal),
            TGlobal::OutJS($oOrderEvent->fieldValueVatTotal),
            TGlobal::OutJS($oOrderEvent->fieldShopShippingGroupPrice),
            TGlobal::OutJS($oOrderEvent->fieldAdrBillingCity),
            TGlobal::OutJS(''),
            TGlobal::OutJS($sCountryCode)
        );
    }

    /**
     * @param TdbShopOrder     $oOrderEvent
     * @param TdbShopOrderItem $oOrderItem
     *
     * @return string
     */
    protected function GetOrderCompleteEventOrderItemDetails($oOrderEvent, $oOrderItem)
    {
        $dUnitPrice = $oOrderItem->fieldOrderPriceAfterDiscounts / $oOrderItem->fieldOrderAmount;

        return sprintf(
            "_gaq.push(['_addItem', '%s', '%s', '%s', '%s', '%s', '%s' ]);",
            TGlobal::OutJS($oOrderEvent->fieldOrdernumber),
            TGlobal::OutJS($oOrderItem->fieldArticlenumber),
            TGlobal::OutJS($oOrderItem->fieldName),
            TGlobal::OutJS($oOrderItem->fieldNameVariantInfo),
            TGlobal::OutJS($dUnitPrice),
            TGlobal::OutJS($oOrderItem->fieldOrderAmount)
            );
    }
}
