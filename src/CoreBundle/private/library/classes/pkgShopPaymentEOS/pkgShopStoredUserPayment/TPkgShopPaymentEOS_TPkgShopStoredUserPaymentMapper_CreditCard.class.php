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
class TPkgShopPaymentEOS_TPkgShopStoredUserPaymentMapper_CreditCard extends TPkgShopStoredUserPaymentMapper_StoredItem
{
    /**
     * @param TdbShopPaymentMethod  $oPaymentMethod
     * @param TdbShopPaymentHandler $oPaymentHandler
     *
     * @return array
     */
    protected function getDataFromPaymentHandler(
        TdbShopPaymentMethod $oPaymentMethod,
        TdbShopPaymentHandler $oPaymentHandler
    ) {
        $aData = array(
            'cardNumber' => '*****'.$oPaymentHandler->GetUserPaymentDataItem('suffix'),
            'paymentExternalType' => $oPaymentHandler->GetUserPaymentDataItem('card-type'),
        );

        return $aData;
    }
}
