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
class TShopPaymentHandler_EOSNeoPayCreditCard extends TShopPaymentHandler_EOSNeoPay
{
    /**
     * @return string
     */
    protected function getMessageConsumer()
    {
        return 'eos-payment-cc';
    }

    /**
     * the api payment identifier.
     *
     * @return string
     */
    protected function getEOSPaymentMethodIdentifier()
    {
        return 'creditcard';
    }

    /**
     * @return string
     */
    protected function getXSDVersion()
    {
        return '1_2_1';
    }

    /**
     * credit card form is handled in iframe.
     *
     * @return bool
     */
    protected function paymentMethodIsHandledInIFrame()
    {
        return true;
    }

    /**
     * - Set Recurring param to true, so payment user data is returned by api
     * - Store basket identifier as external reference - shows up on merchant's bill.
     *
     * @return array
     */
    protected function getInitFormParameter()
    {
        $aParams = parent::getInitFormParameter();
        if ($this->needsRecurringOption()) {
            $aParams['Recurring'] = 'true';
        }
        $aParams['External_Reference1'] = TShopBasket::GetInstance()->sBasketIdentifier;

        return $aParams;
    }

    /**
     * Recurring is needed to get credit card aliases.
     *
     * @return bool
     */
    protected function needsRecurringOption()
    {
        $bRecurringNeeded = $this->usesPaymentAlias();

        return $bRecurringNeeded;
    }

    /**
     * @return bool
     */
    protected function usesPaymentAlias()
    {
        $sAliasesAreUsed = $this->GetConfigParameter('useAlias');

        return '1' == $sAliasesAreUsed || 'true' == $sAliasesAreUsed;
    }

    /**
     * - Store order number as external reference - shows up on merchant's bill
     * - Store order number as external description - shows up on customer's bill.
     *
     * @param TdbShopOrder $oOrder
     *
     * @return array
     */
    protected function getCaptureParameter($oOrder)
    {
        $aParams = parent::getCaptureParameter($oOrder);
        $sShopName = '';
        $oShop = $oOrder->GetFieldShop();
        if ($oShop) {
            $sShopName = $oShop->fieldName.' ';
        }
        $aParams['External_CustomerDescription1'] = $sShopName.'Nr. '.$oOrder->fieldOrdernumber;
        $aParams['External_Reference1'] = $oOrder->fieldOrdernumber;

        return $aParams;
    }

    /**
     * Store payment information like credit card alias in user payment data.
     *
     * @param object $oFormStatusObject
     * @param string $sMessageConsumer
     *
     * @return bool
     */
    protected function handleFormStatus($oFormStatusObject, $sMessageConsumer = null)
    {
        $bSuccess = parent::handleFormStatus($oFormStatusObject, $sMessageConsumer);
        if ($bSuccess && $oFormStatusObject->{'payment-data'}->{'credit-card'}) {
            $aPaymentData = $this->aPaymentUserData;
            foreach ($oFormStatusObject->{'payment-data'}->{'creditcard'}->children() as $oPaymentData) {
                $aPaymentData[$oPaymentData->getName()] = (string) $oPaymentData;
            }
        }

        return $bSuccess;
    }

    /**
     * some payment types like credit card can return additional data (aliases), these can be handled here.
     *
     * @param SimpleXMLElement $oPaymentData
     */
    protected function handlePaymentDataObject($oPaymentData)
    {
        parent::handlePaymentDataObject($oPaymentData);
        $oPaymentDataCreditCard = $oPaymentData->{'creditcard'};
        if (count($oPaymentDataCreditCard) > 0) {
            $this->aPaymentUserData['payment-alias-id'] = (string) $oPaymentDataCreditCard->{'payment-alias-id'};
            $this->aPaymentUserData['card-type'] = (string) $oPaymentDataCreditCard->{'card-type'};
            $this->aPaymentUserData['suffix'] = (string) $oPaymentDataCreditCard->{'suffix'};
            $this->aPaymentUserData['owner'] = (string) $oPaymentDataCreditCard->{'owner'};
            $this->aPaymentUserData['expiry-month'] = (string) $oPaymentDataCreditCard->{'expiry-month'};
            $this->aPaymentUserData['expiry-year'] = (string) $oPaymentDataCreditCard->{'expiry-year'};
        }
    }

    /**
     * ATTENTION: we save the payment data later (PostProcessExternalPaymentHandlerHook) when we have all the
     * information we need.
     *
     * @return bool
     */
    public function isStorageOfUserPaymentAllowed()
    {
        return false;
    }

    /**
     * Go on in process if form status has no errors = user has completed payment form.
     *
     * @return bool
     */
    public function PostProcessExternalPaymentHandlerHook()
    {
        $bSuccess = parent::PostProcessExternalPaymentHandlerHook();
        if ($this->usesPaymentAlias()) {
            if ($bSuccess) {
                try {
                    if (true === property_exists($this, 'fieldStoredUserPaymentAllowStorage')
                        && true === $this->fieldStoredUserPaymentAllowStorage
                        && false === $this->getFromStorageId()) {
                        $oUser = TdbDataExtranetUser::GetInstance();
                        if (true === $oUser->IsLoggedIn()) {
                            // allow storage only if the user is logged in
                            $oBasket = TShopBasket::GetInstance();
                            $oActivePaymentMethod = $oBasket->GetActivePaymentMethod();
                            if ($oActivePaymentMethod) {
                                $oManager = new TPkgShopStoredUserPaymentManager(TdbDataExtranetUser::GetInstance());
                                $oManager->saveUserPayment($oActivePaymentMethod);
                            }
                        }
                    }
                } catch (TPkgShopStoredUserPaymentException $e) {
                    $oMsgManager = TCMSMessageManager::GetInstance();
                    $oMsgManager->AddMessage(TShopStepShippingCore::MSG_PAYMENT_METHOD, $e->getMessageCode(), $e->getAdditionalData());
                }
            }
        }

        return $bSuccess;
    }
}
