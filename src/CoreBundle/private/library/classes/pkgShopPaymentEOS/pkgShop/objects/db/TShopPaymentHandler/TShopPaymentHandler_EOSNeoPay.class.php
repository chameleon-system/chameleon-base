<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;

/**
 * This is the base class for EOS NEOPAY payment handler. Extend this class for payment methods.
 * Make sure you have the eos neopay seo url handler installed.
 *
 * @deprecated since 6.2.0 - no longer used.
 */
class TShopPaymentHandler_EOSNeoPay extends TdbShopPaymentHandler
{
    const VIEW_PATH = 'pkgShop/views/db/TShopPaymentHandler/TShopPaymentHandler_EOSNeoPay';

    const API_URL_LIVE = 'https://webservices.eos-payment.com/processing/payment';

    const API_URL_TEST = 'https://webservices-int.eos-payment.com/processing/payment';

    const URL_IDENTIFIER = '_eospayment_';

    protected function GetViewPath()
    {
        return self::VIEW_PATH;
    }

    /**
     * Get the message consumer all error messages are sent to. Display this in your payment-handler's view.
     *
     * @return string
     */
    protected function getMessageConsumer()
    {
        return 'eos-payment';
    }

    /**
     * Get base url for communication with test or live api respecting live-mode from your payment handler group's settings.
     *
     * @return string
     */
    protected function getAPIBaseURL()
    {
        if (IPkgShopOrderPaymentConfig::ENVIRONMENT_PRODUCTION === $this->getEnvironment()) {
            $sApiURL = self::API_URL_LIVE;
        } else {
            $sApiURL = self::API_URL_TEST;
        }

        return $sApiURL;
    }

    /**
     * Return basic parameters that have to be sent with every request.
     *
     * @return array
     */
    protected function getBaseRequestParameter()
    {
        $aBaseParameter = array();
        $aBaseParameter['RequestID'] = $this->generateRequestId();
        $aBaseParameter['XsdVersion'] = $this->getXSDVersion();

        return $aBaseParameter;
    }

    /**
     * A unique request id, so requests are not processed twice by EOS.
     *
     * @return string
     */
    protected function generateRequestId()
    {
        return uniqid('', true);
    }

    /**
     * @return string
     */
    protected function getProfileId()
    {
        if (IPkgShopOrderPaymentConfig::ENVIRONMENT_PRODUCTION === $this->getEnvironment()) {
            $sProfileId = $this->GetConfigParameter('ProfileID');
        } else {
            $sProfileId = $this->GetConfigParameter('Test_ProfileID');
        }

        return $sProfileId;
    }

    /**
     * @return string
     */
    protected function getSalt()
    {
        if (IPkgShopOrderPaymentConfig::ENVIRONMENT_PRODUCTION === $this->getEnvironment()) {
            $sSalt = $this->GetConfigParameter('Salt');
        } else {
            $sSalt = $this->GetConfigParameter('Test_Salt');
        }

        return $sSalt;
    }

    /**
     * @param string $sWebservice - the webservice name to use
     * @param array  $aParams
     * @param string $sTxId       - the transaction id returned by api (exception: init-requests, use null)
     * @param null   $sAxId       - action id for the request, needed for action status requests or refund/storno
     *
     * @return string
     */
    protected function generateRequestURL($sWebservice, $aParams, $sTxId = null, $sAxId = null)
    {
        $sRequestURL = $this->getAPIBaseURL();
        $sRequestURL .= '/'.$this->getEOSPaymentMethodIdentifier();
        $sRequestURL .= '/'.$this->getProfileId();
        if (null !== $sTxId) {
            $sRequestURL .= '/'.$sTxId;
        }
        if (null !== $sAxId) {
            $sRequestURL .= '/'.$sAxId;
        }
        $sRequestURL .= '/'.$sWebservice.'.xml';
        $aParams = array_merge($this->getBaseRequestParameter(), $aParams);
        $aURLParameter = array();
        foreach ($aParams as $sParameterName => $sParameterValue) {
            $aURLParameter[] = urlencode($sParameterName).'='.urlencode($sParameterValue);
        }
        $sRequestURL .= '?'.implode('&', $aURLParameter);
        $sRequestURL = $this->addSecurityHashToRequestURL($sRequestURL);

        return $sRequestURL;
    }

    /**
     * Get response from api, return false on error
     * Curl is used if available, else file_get_contents - remember to set allow_fopen_url then.
     *
     * @param string $sRequestURL
     *
     * @return string|bool
     */
    protected function sendRequest($sRequestURL)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $sRequestURL);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 0);
        $sResponse = curl_exec($ch);
        if (false === $sResponse) {
            TTools::WriteLogEntry('EOS PAYMENT REQUEST Curl-Error: '.$sRequestURL."\nErrorNr.  ".curl_errno($ch)."\n".curl_error($ch), 1, __FILE__, __LINE__);
        }
        if (false !== $sResponse) {
            TTools::WriteLogEntry('EOS PAYMENT REQUEST: '.$sRequestURL."\nReturned: ".$sResponse, 4, __FILE__, __LINE__);
        }

        return $sResponse;
    }

    /**
     * Add security hash to finished request url.
     *
     * @param string $sRequestURL
     *
     * @return string
     */
    protected function addSecurityHashToRequestURL($sRequestURL)
    {
        $sHash = hash('sha256', $sRequestURL.'{'.$this->getSalt().'}');
        $sRequestURL .= '&SecurityCode='.urlencode($sHash);

        return $sRequestURL;
    }

    /**
     * The payment method`s identifier for request urls
     * Override this in the extension for the payment method
     * One of creditcard, elv, paypal, sofortueberweisung, whatevermobile, paysafecard, clickandbuy, giropay.
     *
     * @return string
     */
    protected function getEOSPaymentMethodIdentifier()
    {
        return '-';
    }

    /**
     * The payment method`s XSD-version to ensure backwards compatibility
     * Override this in the extension for the payment method.
     *
     * @return string
     */
    protected function getXSDVersion()
    {
        return '0';
    }

    /**
     * First, we have to decide if payment form is in an iframe or not
     * - if yes: return false, so user is redirected to payment form where iframe has to be displayed
     * - if no: get form url from api and redirect there.
     *
     * @param string $sMessageConsumer
     *
     * @return bool
     */
    public function PostSelectPaymentHook($sMessageConsumer)
    {
        $bContinue = parent::PostSelectPaymentHook($sMessageConsumer);
        if ($bContinue) {
            $bUseWithPaidService = false;
            if ($this->usesPaymentAlias()) {
                if ($this->getFromStorageId()) {
                    $bUseWithPaidService = true;
                }
            }
            if ($bUseWithPaidService) {
                //payment alias
                $bContinue = $this->initWithPaid();
            } else {
                if (!$this->paymentMethodIsHandledInIFrame()) {
                    $sURL = $this->initForm();
                    if (false !== $sURL) {
                        $this->getRedirect()->redirect($sURL);
                    }
                }
                $bContinue = false; //display iframe
            }
        }

        return $bContinue;
    }

    /**
     * Decide if payment form is displayed in an iframe
     * Override this in the extension for the payment method.
     *
     * @return bool
     */
    protected function paymentMethodIsHandledInIFrame()
    {
        return true;
    }

    /**
     * Initialise payment, set transaction and return url to payment form. Return false on error.
     *
     * @return bool|string
     */
    protected function initForm()
    {
        $aPaymentUserData = $this->aPaymentUserData;
        if (null === $aPaymentUserData) {
            $aPaymentUserData = array();
        }
        $sFormURL = false;
        $aParams = $this->getInitFormParameter();
        $sResponse = $this->sendRequest($this->generateRequestURL('init-form', $aParams));
        if (false !== $sResponse) {
            $oXMLObject = $this->handleResponse($sResponse, $this->getMessageConsumer());
            if (false !== $oXMLObject) {
                $oInitForm = $oXMLObject->{'return-values'}->{'init-form'};
                if ('OK' == $oXMLObject->{'return-code'}) {
                    $bSuccess = $this->handleFormStatus($oInitForm->{'form-status'}, $this->getMessageConsumer());
                    if ($bSuccess) {
                        $bSuccess = $this->handleActionStatus($oInitForm->{'form-status'}->{'first-action-status'}, $this->getMessageConsumer());
                        if ($bSuccess) {
                            $sFormURL = (string) $oInitForm->{'start-url'};
                            $aPaymentUserData['txid'] = (string) $oInitForm->{'txid'};
                            $aPaymentUserData['fsid'] = (string) $oInitForm->{'fsid'};
                        }
                    }
                }
            }
        }
        $this->SetPaymentUserData($aPaymentUserData);

        return $sFormURL;
    }

    protected function initWithPaid()
    {
        $aPaymentUserData = $this->aPaymentUserData;
        if (null === $aPaymentUserData) {
            $aPaymentUserData = array();
        }
        $bSuccess = false;
        $aParams = $this->getInitFormParameter();
        $aParams['Paid'] = $this->aPaymentUserData['payment-alias-id'];
        $sResponse = $this->sendRequest($this->generateRequestURL('init-payment-with-paid', $aParams));
        if (false !== $sResponse) {
            $oXMLObject = $this->handleResponse($sResponse, $this->getMessageConsumer());
            if (false !== $oXMLObject) {
                $oInitForm = $oXMLObject->{'return-values'}->{'init-payment-with-paid'};
                if ('OK' == $oXMLObject->{'return-code'}) {
                    $bSuccess = $this->handleActionStatus($oInitForm->{'payment-status'}->{'first-action-status'}, $this->getMessageConsumer());
                    if ($bSuccess) {
                        unset($aPaymentUserData['fsid']);
                        $aPaymentUserData['txid'] = (string) $oInitForm->{'txid'};
                    }
                }
            }
        }
        $this->SetPaymentUserData($aPaymentUserData);

        return $bSuccess;
    }

    /**
     * Convert api response to simple xml object, return false on error.
     *
     * @param string $sResponse
     * @param string $sMessageConsumer - set to null if you don't want to add a message
     *
     * @return object
     */
    protected function handleResponse($sResponse, $sMessageConsumer = null)
    {
        $oXmlObject = @simplexml_load_string($sResponse);
        if (false === $oXmlObject && null !== $sMessageConsumer) {
            $oMessageManager = TCMSMessageManager::GetInstance();
            $oMessageManager->AddMessage($sMessageConsumer, 'ERROR-PAYMENT-EOS-INVALID-XML-RESPONSE');
        }

        return  $oXmlObject;
    }

    /**
     * Get an array of parameters for init-form request.
     * Parameters can vary between payment methods, so add payment method specific params in your extension.
     *
     * @return array
     */
    protected function getInitFormParameter()
    {
        $aParams = array();
        $aParams['OrderType'] = 'ECOM';
        $aParams['FirstActionMode'] = 'RESERVE';
        $aParams['FinalizeMode'] = 'INSTANT';
        $oBasket = TShopBasket::GetInstance();
        $aParams['Amount'] = $this->transformPriceValueForRequest($oBasket->dCostTotal);
        $aParams['Currency'] = $this->GetCurrencyIdentifier();
        $aParams['EndURL'] = $this->getSuccessURL();
        if ($this->useNotify()) {
            $aParams['NotifyURL'] = $this->getNotifyURL();
        }
        $aParams['Language'] = TTools::GetActiveLanguageIsoName();
        $aParams['Country'] = $this->getCountryCode();

        return $aParams;
    }

    /**
     * ISO country code, use billing address to get it.
     *
     * @return string
     */
    protected function getCountryCode()
    {
        $sCountryCode = '';
        $oUser = TdbDataExtranetUser::GetInstance();
        $oBillingAddress = $oUser->GetBillingAddress();
        if ($oBillingAddress) {
            $oBillingCountry = $oBillingAddress->GetFieldDataCountry();
            if ($oBillingCountry) {
                $oTCountry = $oBillingCountry->GetFieldTCountry();
                if ($oTCountry) {
                    $sCountryCode = $oTCountry->fieldIsoCode2;
                }
            }
        }

        return $sCountryCode;
    }

    /**
     * Should api send an IPN?
     * ATTENTION: IPN is currently not implemented because we only need it for delayed payment.
     *
     * @return bool
     */
    protected function useNotify()
    {
        return false;
    }

    /**
     * Get URL for IPN.
     *
     * @return string
     */
    protected function getNotifyURL()
    {
        $oShop = TdbShop::GetInstance();
        $sBasketPage = $oShop->GetLinkToSystemPage('checkout', null, true);
        $sURL = $sBasketPage.'/'.self::URL_IDENTIFIER.'/ipn';
        $sURL = str_replace('&amp;', '&', $sURL);

        return $sURL;
    }

    /**
     * Get URL to redirect to when user has finished payment form.
     * For payment form can be in an iframe, we have to tell the seo handler if we need to breakout.
     *
     * @return string
     */
    protected function getSuccessURL()
    {
        $oActivePage = $this->getActivePageService()->getActivePage();
        $oGlobal = TGlobal::instance();
        $sReturnURLBase = $oActivePage->GetRealURLPlain(array(), true);
        if ('.html' == substr($sReturnURLBase, -5)) {
            $sReturnURLBase = substr($sReturnURLBase, 0, -5);
        }
        if ('/' != substr($sReturnURLBase, -1)) {
            $sReturnURLBase .= '/';
        }
        $sSuccessURL = $sReturnURLBase.self::URL_IDENTIFIER.'/return_from_form/';
        if ($this->paymentMethodIsHandledInIFrame()) {
            $sSuccessURL .= 'breakout/';
        } else {
            $sSuccessURL .= 'redirect/';
        }
        $sSuccessURL .= 'spot_'.$oGlobal->GetExecutingModulePointer()->sModuleSpotName;

        return $sSuccessURL;
    }

    /**
     * Convert price into lowest unit.
     *
     * @param float $dPrice
     *
     * @return int
     */
    protected function transformPriceValueForRequest($dPrice)
    {
        $dPrice = $dPrice * 100;
        $dPrice = number_format($dPrice, 0, '', '');

        return $dPrice;
    }

    /**
     * If we are the active payment handler, we have to init payment and set url to payment form,
     * so our view can display the iframe.
     *
     * @param string $sViewName
     * @param string $sViewType
     *
     * @return array
     */
    protected function GetAdditionalViewVariables($sViewName, $sViewType)
    {
        $aViewVariables = parent::GetAdditionalViewVariables($sViewName, $sViewType);
        $oActivePaymentHandler = null;
        $oActivePaymentMethod = TShopBasket::GetInstance()->GetActivePaymentMethod();
        if ($oActivePaymentMethod) {
            $oActivePaymentHandler = $oActivePaymentMethod->GetFieldShopPaymentHandler();
        }
        if ($oActivePaymentHandler && $this->id == $oActivePaymentHandler->id && 'standard' == $sViewName) {
            $aViewVariables['sFormInitURL'] = $this->initForm();
        }
        $aViewVariables['sMessageConsumer'] = $this->getMessageConsumer();

        return $aViewVariables;
    }

    /**
     * Go on in process if form status has no errors = user has completed payment form.
     *
     * @return bool
     */
    public function PostProcessExternalPaymentHandlerHook()
    {
        $bSuccess = parent::PostProcessExternalPaymentHandlerHook();
        if ($bSuccess) {
            $bSuccess = $this->getFormStatus($this->getMessageConsumer());
        }

        return $bSuccess;
    }

    /**
     * Check status of form for current transaction and throw messages if needed
     * Result of first action is also checked.
     *
     * @param string $sMessageConsumer - set to null if you don't want to throw any messages
     *
     * @return bool
     */
    protected function getFormStatus($sMessageConsumer = null)
    {
        $bSuccess = false;
        $sRequestURL = $this->generateRequestURL('form-status', array(), $this->getTxid(), null);
        $sResponse = $this->sendRequest($sRequestURL);
        if (false !== $sResponse) {
            $oXmlObject = $this->handleResponse($sResponse, $sMessageConsumer);
            if (false !== $oXmlObject) {
                if ('OK' == $oXmlObject->{'return-code'}) {
                    $oFormStatusObject = $oXmlObject->{'return-values'}->{'form-status'};
                    $bSuccess = $this->handleFormStatus($oFormStatusObject, $sMessageConsumer);
                    if ($bSuccess) {
                        $bSuccess = $this->handleActionStatus($oFormStatusObject->{'first-action-status'}, $this->getMessageConsumer());
                    }
                }
            }
        }

        return $bSuccess;
    }

    /**
     * Return true if form status is OK - else throw error messages.
     *
     * @param object $oFormStatusObject
     * @param string $sMessageConsumer  - set to null if you don't want to throw any messages
     *
     * @return bool
     */
    protected function handleFormStatus($oFormStatusObject, $sMessageConsumer = null)
    {
        $bSuccess = false;
        if ('NEW' == $oFormStatusObject->{'form-result'}->{'code'} || 'SUCCESSFUL' == $oFormStatusObject->{'form-result'}->{'code'}) {
            $oPaymentData = $oFormStatusObject->{'payment-data'};
            if (count($oPaymentData) > 0) {
                $this->handlePaymentDataObject($oPaymentData);
            }
            $bSuccess = true;
        } else {
            $this->throwMessageForReason($oFormStatusObject->{'form-result'}->{'reason'}, $sMessageConsumer);
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
    }

    /**
     * Call capture api here.
     *
     * @param TdbShopOrder $oOrder
     * @param string       $sMessageConsumer - send error messages here
     *
     * @return bool
     */
    public function ExecutePayment(TdbShopOrder &$oOrder, $sMessageConsumer = '')
    {
        $bPaymentOk = parent::ExecutePayment($oOrder, $sMessageConsumer);
        if ($bPaymentOk) {
            if (empty($sMessageConsumer)) {
                $sMessageConsumer = $this->getMessageConsumer();
            }
            $bPaymentOk = $this->capturePayment($oOrder, $sMessageConsumer);
        }

        return $bPaymentOk;
    }

    /**
     * Capture payment for current transaction id and check action status.
     *
     * @param TdbShopOrder $oOrder
     * @param string       $sMessageConsumer - set to null if you don't want to throw any messages
     *
     * @return bool
     */
    protected function capturePayment($oOrder, $sMessageConsumer = null)
    {
        $bSuccess = false;
        $aParams = $this->getCaptureParameter($oOrder);
        $sRequestURL = $this->generateRequestURL('capture', $aParams, $this->getTxid(), null);
        $sResponse = $this->sendRequest($sRequestURL);
        $oXmlObject = $this->handleResponse($sResponse, $sMessageConsumer);
        if (false !== $oXmlObject) {
            if ('OK' == $oXmlObject->{'return-code'}) {
                $bSuccess = $this->handleActionStatus($oXmlObject->{'return-values'}->{'action-status'}, $sMessageConsumer);
            }
        }

        return $bSuccess;
    }

    /**
     * Return true if action status is OK - else throw error messages.
     *
     * @param object $oActionStatusObject
     * @param string $sMessageConsumer    - set to null if you don't want to throw any messages
     *
     * @return bool
     */
    protected function handleActionStatus($oActionStatusObject, $sMessageConsumer = null)
    {
        $bSuccess = false;
        if ('NEW' == $oActionStatusObject->{'action-result'}->{'code'} || 'SUCCESSFUL' == $oActionStatusObject->{'action-result'}->{'code'}) {
            $aPaymentUserData = $this->aPaymentUserData;
            $aPaymentUserData['axid'] = (string) $oActionStatusObject->{'axid'};
            $this->SetPaymentUserData($aPaymentUserData);
            $bSuccess = true;
        } else {
            $this->throwMessageForReason($oActionStatusObject->{'action-result'}->{'reason'}, $sMessageConsumer);
        }

        return $bSuccess;
    }

    /**
     * Get parameters for capture request. Note that api will throw an error if amount is higher than reserved amount.
     * Extend this method to set payment method specific External_ parameters.
     *
     * @param TShopOrder $oOrder
     *
     * @return array
     */
    protected function getCaptureParameter($oOrder)
    {
        $aParams = array();
        $aParams['Amount'] = $this->transformPriceValueForRequest($oOrder->fieldValueTotal);

        return $aParams;
    }

    /**
     * Add message for a simple xml reason object (as returned by status requests)
     * Rewrite error codes to corresponding messages here.
     *
     * @param object $oReason
     * @param string $sMessageConsumer - set to null if you don't want to throw any messages
     */
    protected function throwMessageForReason($oReason, $sMessageConsumer = null)
    {
        if (null !== $sMessageConsumer) {
            if ('REJECTED' == $oReason->{'classifier'}) {
                switch ($oReason->{'code'}) {
                    case 'REJE001':
                        $sMessageName = 'ERROR-PAYMENT-EOS-REJECTED-BY-ACQUIRER';
                        break;
                    case 'REJE002':
                        $sMessageName = 'ERROR-PAYMENT-EOS-AUTHENTICATION-FAILED';
                        break;
                    case 'REJE003':
                        $sMessageName = 'ERROR-PAYMENT-EOS-PAYMENT-DATA-NOT-ALLOWED';
                        break;
                    case 'REJE004':
                        $sMessageName = 'ERROR-PAYMENT-EOS-FRAUD';
                        break;
                    case 'REJE005':
                        $sMessageName = 'ERROR-PAYMENT-EOS-LIMIT-REACHED';
                        break;
                    case 'REJE006':
                        $sMessageName = 'ERROR-PAYMENT-EOS-GENERAL-ERROR'; //risk management
                        break;
                    case 'REJE007':
                        $sMessageName = 'ERROR-PAYMENT-EOS-3D-SECURE';
                        break;
                    case 'REJE008':
                        $sMessageName = 'ERROR-PAYMENT-EOS-TRANSACTION-NOT-POSSIBLE';
                        break;
                    default:
                        $sMessageName = 'ERROR-PAYMENT-EOS-GENERAL-ERROR';
                        break;
                }
            } elseif ('ABORTED' == $oReason->{'classifier'}) {
                $sMessageName = 'ERROR-PAYMENT-EOS-USER-ABORTED';
            } else {
                TTools::WriteLogEntry('EOS PAYMENT: Error '.$oReason->{'classifier'}.' '.$oReason->{'code'}, 1, __FILE__, __LINE__);
                $sMessageName = 'ERROR-PAYMENT-EOS-GENERAL-ERROR';
            }
            $oMessageManager = TCMSMessageManager::GetInstance();
            $oMessageManager->AddMessage($sMessageConsumer, $sMessageName, array('sErrorCode' => (string) $oReason->{'code'}, 'sErrorClassifier' => (string) $oReason->{'classifier'}));
        }
    }

    /**
     * Get current action id. Return null if none is set.
     *
     * @return string|null
     */
    protected function getAxid()
    {
        $sAxid = null;
        if (isset($this->aPaymentUserData['axid']) && !empty($this->aPaymentUserData['axid'])) {
            $sAxid = $this->aPaymentUserData['axid'];
        }

        return $sAxid;
    }

    public function GetExternalPaymentReferenceIdentifier()
    {
        $sIdentifier = '';
        if (is_array($this->aPaymentUserData) && array_key_exists('txid', $this->aPaymentUserData)) {
            $sIdentifier = $this->aPaymentUserData['txid'];
        }

        return $sIdentifier;
    }

    /**
     * Get current transaction id. Return null if none is set.
     *
     * @return string|null
     */
    protected function getTxid()
    {
        $sTxid = null;
        if (isset($this->aPaymentUserData['txid']) && !empty($this->aPaymentUserData['txid'])) {
            $sTxid = $this->aPaymentUserData['txid'];
        }

        return $sTxid;
    }

    /**
     * @return bool
     */
    protected function usesPaymentAlias()
    {
        return false;
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }

    /**
     * @return ICmsCoreRedirect
     */
    private function getRedirect()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.redirect');
    }
}
