<?php

/**
 * @deprecated since 6.2.0 - no longer used.
 */

/** @var $oShop TdbShop */
/** @var $oPaymentHandler TdbShopPaymentHandler */
/** @var $aCallTimeVars array */
/** @var $sFormInitURL string */
/** @var $sMessageConsumer string */
$oMsgManager = TCMSMessageManager::GetInstance();
$sSpotName = '';
if (array_key_exists('sSpotName', $aCallTimeVars)) {
    $sSpotName = $aCallTimeVars['sSpotName'];
}
if ($oMsgManager->ConsumerHasMessages($sMessageConsumer)) {
    echo $oMsgManager->RenderMessages($sMessageConsumer);
}
//if($oPaymentHandler->id == TShopBasket::GetInstance()->GetActivePaymentMethod()->GetFieldShopPaymentHandler()->id) {
if ('1' == $aCallTimeVars['bInputIsActive'] && !empty($sFormInitURL)) {
    echo '<iframe src="'.$sFormInitURL.'" width="100%" style="height:400px;width:100%;"></iframe>';
}
