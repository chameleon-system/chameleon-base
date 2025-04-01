<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Security\Password\PasswordHashGeneratorInterface;
use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\ExtranetBundle\Exception\PasswordGenerationFailedException;
use ChameleonSystem\ExtranetBundle\Interfaces\ExtranetUserProviderInterface;
use ChameleonSystem\ExtranetBundle\MessageCodes;
use ChameleonSystem\ExtranetBundle\objects\ExtranetUserConstants;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Contracts\Translation\TranslatorInterface;

class MTExtranetCoreEndPoint extends TUserCustomModelBase
{
    const MSG_CONSUMER_NAME = '_mtextranetcore';
    const MSG_CONSUMER_FORMNAME = '_mtextranetcoreform';

    const FIELD_BLACKLIST_TYPE_USER = 'user';
    const FIELD_BLACKLIST_TYPE_ADDRESS = 'address';

    /**
     * @var array<string, string[]>
     * @psalm-var array<self::FIELD_BLACKLIST_TYPE_*, string[]>
     */
    private static $fieldBlacklist = array(
        self::FIELD_BLACKLIST_TYPE_USER => array('id', 'isadmin', 'data_extranet_group_mlt', 'cms_portal_id', 'session_key', 'login_salt', 'datecreated', 'confirmed', 'confirmedon', 'reg_email_send'),
        self::FIELD_BLACKLIST_TYPE_ADDRESS => array(),
    );

    /**
     * messages for this spot.
     *
     * @var TIterator
     */
    protected $oMessages = null;
    /**
     * set to true if a login attempt failed.
     *
     * @var bool
     */
    protected $bLoginAttemptedFailed = false;
    /**
     * set to true if the user requested a confirmation of his account and succeeded.
     *
     * @var bool
     */
    protected static $bRegistrationConfirmationSuccess = false;
    /**
     * set to true if the password as been send to the user.
     *
     * @var bool
     */
    protected static $bPasswordSendToUser = false;
    /**
     * @var bool
     */
    protected $bAllowHTMLDivWrapping = true;
    /**
     * set to true if you want to prevent the module from redirecting. This is useful if you want to use the
     * module from within another module.
     *
     * @var bool
     */
    protected $bPreventRedirects = false;
    /**
     * @var bool
     */
    private $bPasswordSent = false;

    /**
     * @param bool $bAllowRedirect
     *
     * @return void
     */
    public function SetPreventRedirects($bAllowRedirect)
    {
        $this->bPreventRedirects = $bAllowRedirect;
    }

    /**
     * {@inheritdoc}
     */
    protected function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = array('Register', 'UpdateUser', 'UpdateUserPasswordRequired', 'UpdateUserAddress', 'Login', 'Logout', 'SendPassword', 'ConfirmUser');
        $externalFunctions[] = 'SelectBillingAddress';
        $externalFunctions[] = 'SelectShippingAddress';
        $externalFunctions[] = 'DeleteBillingAddress';
        $externalFunctions[] = 'DeleteShippingAddress';
        $externalFunctions[] = 'ChangeForgotPassword';
        $externalFunctions[] = 'SendDoubleOptInEMail';
        $externalFunctions[] = 'ChangeUserPassword';
        $externalFunctions[] = 'ChangeUserEmail';

        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    /**
     * {@inheritdoc}
     */
    public function Init()
    {
        parent::Init();
        /**
         * Load any messages for this spot.
         */
        $this->oMessages = null;
        $oMessageManager = TCMSMessageManager::GetInstance();
        if ($oMessageManager->ConsumerHasMessages($this->sModuleSpotName)) {
            $this->oMessages = $oMessageManager->ConsumeMessages($this->sModuleSpotName, true, false);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function &Execute()
    {
        parent::Execute();
        $this->data['aInput'] = $this->GetFilteredUserData('aInput');
        $this->data['oMessages'] = $this->oMessages;
        $this->data['oUser'] = $this->getExtranetUserProvider()->getActiveUser();
        $oExtranetConfig = TdbDataExtranet::GetInstance();
        $this->data['oExtranetConfig'] = $oExtranetConfig;
        $this->data['bRegistrationConfirmationSuccess'] = self::$bRegistrationConfirmationSuccess;
        $this->data['bPasswordSent'] = $this->bPasswordSent;

        if (!array_key_exists('name', $this->data)) {
            $this->data['name'] = '';
        }
        $this->data['sSuccessURL'] = $this->getSuccessUrlFromRequest(TCMSUserInput::FILTER_URL_INTERNAL);
        $this->data['sFailureURL'] = $this->getFailureUrlFromRequest(TCMSUserInput::FILTER_URL_INTERNAL);

        return $this->data;
    }

    /**
     * Confirms a user registration.
     *
     * @return void
     */
    public function ConfirmUser()
    {
        self::$bRegistrationConfirmationSuccess = false;
        $sKey = $this->getInputFilterUtil()->getFilteredInput('key');
        if (!empty($sKey)) {
            $extranetUserProvider = $this->getExtranetUserProvider();
            $oUser = $extranetUserProvider->getActiveUser();
            $oUser->Logout();
            $extranetUserProvider->reset();
            $oUser = $extranetUserProvider->getActiveUser();

            if ($oUser->LoadFromField('tmpconfirmkey', $sKey)) {
                if (!$oUser->fieldConfirmed) {
                    $aData = $oUser->sqlData;
                    $aData['confirmed'] = '1';
                    $aData['confirmedon'] = date('Y-m-d H:i:s');
                    $oUser->LoadFromRow($aData);
                    $oUser->AllowEditByAll(true);
                    $oUser->Save();
                    $oUser->AllowEditByAll(false);
                    if ($oUser->DirectLoginWithoutPassword($oUser->fieldName)) {
                        self::$bRegistrationConfirmationSuccess = true;
                    }
                }
            }
        }
    }

    /**
     * Registers a customer.
     *
     * @param string|null $sSuccessURL - optional success redirect URL
     * @param string|null $sFailureURL - optional failure redirect URL
     *
     * @return void
     */
    public function Register($sSuccessURL = null, $sFailureURL = null)
    {
        if (is_null($sSuccessURL)) {
            $sSuccessURL = $this->getSuccessUrlFromRequest();
        }
        if (is_null($sFailureURL)) {
            $sFailureURL = $this->getFailureUrlFromRequest();
        }

        $aData = $this->GetFilteredUserData('aUser');

        if (is_array($aData)) {
            reset($aData);
            $extranetUserProvider = $this->getExtranetUserProvider();
            $oOldUser = $extranetUserProvider->getActiveUser();
            // @TODO move the shop stuff in extension
            if (property_exists('TdbDataExtranetUser', 'fieldCustomerNumber')) {
                $sOldCustNr = $oOldUser->fieldCustomerNumber;
                if (!empty($sOldCustNr)) {
                    $aData['customer_number'] = $sOldCustNr;
                }
            }

            $this->PrepareSubmittedData($aData);
            $extranetUserProvider->reset();
            $oUser = $extranetUserProvider->getActiveUser();
            $oUser->LoadFromRow($aData);

            $bDataValid = $this->ValidateUserLoginData();
            $bDataValid = $this->ValidateUserData() && $bDataValid;

            // validate shipping address (if passed...)
            // @TODO move the shop stuff in extension
            if (class_exists('TdbDataExtranetUserAddress', false)) {
                $aShipping = $this->getInputFilterUtil()->getFilteredPostInput(TdbDataExtranetUserAddress::FORM_DATA_NAME_SHIPPING);
                if ($aShipping && is_array($aShipping)) {
                    $oTmpAdr = TdbDataExtranetUserAddress::GetNewInstance();
                    $oTmpAdr->LoadFromRowProtected($aShipping);
                    $bDataValid = ($bDataValid && $oTmpAdr->ValidateData(TdbDataExtranetUserAddress::FORM_DATA_NAME_SHIPPING));
                    if (!$bDataValid) {
                        $oUser->GetShippingAddress(false, true);
                    } // update content in session
                }
            }

            if (true === $bDataValid) {
                $registrationResult = $oUser->Register();
                $bDataValid = false !== $registrationResult;
            }

            if (true === $bDataValid) {
                $this->UpdateUserAddress(null, null, true);

                $oExtranetConf = &TdbDataExtranet::GetInstance();
                if (true === $oExtranetConf->fieldUserMustConfirmRegistration) {
                    $oMsgManager = TCMSMessageManager::GetInstance();
                    $oMsgManager->AddMessage(\TDataExtranetUser::FORM_DATA_NAME_USER, MessageCodes::WAIT_FOR_EMAIL_CONFIRM);
                } else {
                    // redirect to registration success page
                    if (!is_null($sSuccessURL)) {
                        $this->RedirectToURL($sSuccessURL, true);
                    } else {
                        $this->RedirectToURL($oExtranetConf->GetLinkRegisterSuccessPage(), true);
                    }
                }
            }
        }
        if (!is_null($sFailureURL)) {
            $this->RedirectToURL($sFailureURL, true);
        }
    }

    /**
     * Updates the user's billing/shipping address.
     *
     * @param string|null $sSuccessURL   - redirect on success (can also be passed via POST)
     * @param string|null $sFailureURL   - redirect on failure (can also be passed via POST)
     * @param bool        $bInternalCall - if set to true the redirect URLs will be ignored
     *
     * @return bool
     */
    public function UpdateUserAddress($sSuccessURL = null, $sFailureURL = null, $bInternalCall = false)
    {
        if (is_null($sSuccessURL)) {
            $sSuccessURL = $this->getSuccessUrlFromRequest();
        }
        if (is_null($sFailureURL)) {
            $sFailureURL = $this->getFailureUrlFromRequest();
        }

        $bDataValid = true;
        $anyAddressChanged = false;

        //TODO bring the shop stuff in extension
        if (class_exists('TdbDataExtranetUserAddress', false)) {
            // get billing address...
            $aBillingAddress = $this->GetFilteredUserData(TdbDataExtranetUserAddress::FORM_DATA_NAME_BILLING, self::FIELD_BLACKLIST_TYPE_ADDRESS);
            $this->PrepareSubmittedDataForUserAddress($aBillingAddress);
            if (is_array($aBillingAddress)) {
                $anyAddressChanged = true;
                $bDataValid = $this->UpdateBillingAddress($aBillingAddress);
                if (!$bDataValid) {
                    $_SESSION[TdbDataExtranetUserAddress::FORM_DATA_NAME_BILLING] = $aBillingAddress;
                } else {
                    if (array_key_exists(TdbDataExtranetUserAddress::FORM_DATA_NAME_BILLING, $_SESSION)) {
                        $_SESSION[TdbDataExtranetUserAddress::FORM_DATA_NAME_BILLING];
                    }
                }
            }

            // now update shipping
            $aShipping = $this->GetFilteredUserData(TdbDataExtranetUserAddress::FORM_DATA_NAME_SHIPPING, self::FIELD_BLACKLIST_TYPE_ADDRESS);
            $this->PrepareSubmittedDataForUserAddress($aShipping);
            if (is_array($aShipping)) {
                $anyAddressChanged = true;
                $bDataValid = ($this->UpdateShippingAddress($aShipping) && $bDataValid);
                if (!$bDataValid) {
                    $_SESSION[TdbDataExtranetUserAddress::FORM_DATA_NAME_SHIPPING] = $aShipping;
                } else {
                    if (array_key_exists(TdbDataExtranetUserAddress::FORM_DATA_NAME_SHIPPING, $_SESSION)) {
                        $_SESSION[TdbDataExtranetUserAddress::FORM_DATA_NAME_SHIPPING];
                    }
                }
            }
        }

        if (!$bInternalCall && $anyAddressChanged) {
            if ($bDataValid) {
                if (!is_null($sSuccessURL)) {
                    $this->RedirectToURL($sSuccessURL, true);
                } else {
                    $oExtranet = &TdbDataExtranet::GetInstance();
                    $this->RedirectToURL($oExtranet->GetLinkRegisterSuccessPage(), true);
                }
            } else {
                if (!is_null($sFailureURL)) {
                    $this->RedirectToURL($sFailureURL, true);
                }
            }
        }

        return $bDataValid;
    }

    /**
     * Deletes a billing address.
     *
     * @param string $selectedAddressId
     * @param string $sSuccessURL       - redirect on success (can also be passed via get/post)
     * @param string $sFailureURL       - redirect on failure (can also be passed via get/post)
     * @param bool   $bInternalCall     - if set to true the redirect urls will be ignored
     *
     * @return bool
     */
    protected function DeleteBillingAddress($selectedAddressId = null, $sSuccessURL = null, $sFailureURL = null, $bInternalCall = false)
    {
        $bDataValid = false;
        $aUserData = $this->global->GetUserData(TdbDataExtranetUserAddress::FORM_DATA_NAME_BILLING);
        if (is_null($selectedAddressId)) {
            if (is_array($aUserData) && array_key_exists('selectedAddressId', $aUserData)) {
                $selectedAddressId = $aUserData['selectedAddressId'];
            }
            if (empty($selectedAddressId)) {
                $selectedAddressId = null;
            }
        }

        if (is_null($sSuccessURL)) {
            if ($this->global->UserDataExists('sSuccessURL')) {
                $sSuccessURL = $this->global->GetUserData('sSuccessURL', array(), TCMSUserInput::FILTER_URL);
            }
            if (empty($sSuccessURL)) {
                $sSuccessURL = null;
            }
        }
        if (is_null($sFailureURL)) {
            if ($this->global->UserDataExists('sFailureURL')) {
                $sFailureURL = $this->global->GetUserData('sFailureURL', array(), TCMSUserInput::FILTER_URL);
            }
            if (empty($sFailureURL)) {
                $sFailureURL = null;
            }
        }

        $oUser = $this->getExtranetUserProvider()->getActiveUser();
        if (!is_null($selectedAddressId) && $oUser->IsLoggedIn() && !is_null($oUser->id)) {
            // make sure the user has that address
            $oAdr = TdbDataExtranetUserAddress::GetNewInstance();
            /** @var $oAdr TdbDataExtranetUserAddress */
            if ($oAdr->LoadFromFields(array('data_extranet_user_id' => $oUser->id, 'id' => $selectedAddressId))) {
                if ($oAdr->id === $oUser->GetBillingAddress()->id) {
                } else {
                    $bDataValid = true;
                    $oCurrentShipping = $oUser->GetShippingAddress();
                    $bNeedNewShippingSet = ($oCurrentShipping->id == $oAdr->id);
                    $oAdr->Delete();
                    if ($bNeedNewShippingSet) {
                        $oUser->ShipToBillingAddress(true);
                    }
                }
            }
        }

        if (!$bInternalCall) {
            if ($bDataValid) {
                if (!is_null($sSuccessURL)) {
                    $this->RedirectToURL($sSuccessURL, true);
                }
            } else {
                if (!is_null($sFailureURL)) {
                    $this->RedirectToURL($sFailureURL, true);
                }
            }
        }

        return $bDataValid;
    }

    /**
     * Deletes a shipping address.
     *
     * @param string|null $selectedAddressId
     * @param string|null $sSuccessURL       - redirect on success (can also be passed via POST)
     * @param string|null $sFailureURL       - redirect on failure (can also be passed via POST)
     * @param bool        $bInternalCall     - if set to true the redirect URLs will be ignored
     *
     * @return bool
     */
    protected function DeleteShippingAddress($selectedAddressId = null, $sSuccessURL = null, $sFailureURL = null, $bInternalCall = false)
    {
        $bDataValid = false;
        $aUserData = $this->getInputFilterUtil()->getFilteredPostInput(TdbDataExtranetUserAddress::FORM_DATA_NAME_SHIPPING);
        if (is_null($selectedAddressId)) {
            if (is_array($aUserData) && array_key_exists('selectedAddressId', $aUserData)) {
                $selectedAddressId = $aUserData['selectedAddressId'];
            }
            if (empty($selectedAddressId)) {
                $selectedAddressId = null;
            }
        }

        if (is_null($sSuccessURL)) {
            $sSuccessURL = $this->getSuccessUrlFromRequest();
        }
        if (is_null($sFailureURL)) {
            $sFailureURL = $this->getFailureUrlFromRequest();
        }

        $oUser = $this->getExtranetUserProvider()->getActiveUser();
        if (!is_null($selectedAddressId) && $oUser->IsLoggedIn() && !is_null($oUser->id)) {
            // make sure the user has that address
            $oAdr = TdbDataExtranetUserAddress::GetNewInstance();
            /** @var $oAdr TdbDataExtranetUserAddress */
            if ($oAdr->LoadFromFields(array('data_extranet_user_id' => $oUser->id, 'id' => $selectedAddressId))) {
                if ($oAdr->id != $oUser->GetBillingAddress()->id) {
                    $bDataValid = true;
                    $oCurrentShipping = $oUser->GetShippingAddress();
                    $bNeedNewShippingSet = ($oCurrentShipping->id == $oAdr->id);
                    $oAdr->Delete();
                    if ($bNeedNewShippingSet) {
                        $oUser->ShipToBillingAddress(true);
                    }
                }
            }
        }

        if (!$bInternalCall) {
            if ($bDataValid) {
                if (!is_null($sSuccessURL)) {
                    $this->RedirectToURL($sSuccessURL, true);
                }
            } else {
                if (!is_null($sFailureURL)) {
                    $this->RedirectToURL($sFailureURL, true);
                }
            }
        }

        return $bDataValid;
    }

    /**
     * Selects a new billing address.
     *
     * @param string|null $selectedAddressId - new billing address - can also be set via GET/POST
     * @param string|null $sRedirectToURL
     * @param bool        $bInternalCall     - if set to true the redirect URLs will be ignored
     *
     * @return void
     */
    public function SelectBillingAddress($selectedAddressId = null, $sRedirectToURL = null, $bInternalCall = false)
    {
        $aUserData = $this->getInputFilterUtil()->getFilteredInput(TdbDataExtranetUserAddress::FORM_DATA_NAME_BILLING);
        if (is_null($selectedAddressId)) {
            if (is_array($aUserData) && array_key_exists('selectedAddressId', $aUserData)) {
                $selectedAddressId = $aUserData['selectedAddressId'];
            }
            if (empty($selectedAddressId)) {
                $selectedAddressId = null;
            }
        }
        if (is_null($sRedirectToURL)) {
            $sRedirectToURL = $this->getRedirectToUrlFromRequest();
        }
        $oUser = $this->getExtranetUserProvider()->getActiveUser();
        if (!is_null($selectedAddressId) && $oUser->IsLoggedIn() && !is_null($oUser->id)) {
            $iNewBillingAddressId = null;
            if ('new' == $selectedAddressId) {
                // create a new address, and set it
                $oAdr = TdbDataExtranetUserAddress::GetNewInstance();
                $oLocal = &TCMSLocal::GetActive();
                $aData = array('name' => 'neue Addresse (angelegt am '.$oLocal->FormatDate(date('Y-m-d')).')');
                $oAdr->LoadFromRowProtected($aData);
                $oAdr->Save();
                $iNewBillingAddressId = $oAdr->id;
            } else {
                $iNewBillingAddressId = $selectedAddressId;
            }
            $aUser = $oUser->sqlData;
            $aUser['default_billing_address_id'] = $iNewBillingAddressId;
            $oUser->LoadFromRow($aUser);
            // check if we are shipping to billing address
            $oUser->Save();
            $oBillingAddress = $oUser->GetBillingAddress(true);

            $oShop = TdbShop::GetInstance();
            if ($oShop->fieldSyncProfileDataWithBillingData) {
                $oUser->SetUserBaseDataUsingAddress($oBillingAddress);
            }

            $oShippingAddress = $oUser->GetShippingAddress();
            if ($oShippingAddress->id == $iNewBillingAddressId) {
                $oUser->ShipToBillingAddress(true);
            }
        }

        if (!$bInternalCall) {
            if (!is_null($sRedirectToURL)) {
                $this->RedirectToURL($sRedirectToURL, true);
            }
        }
    }

    /**
     * Selects a new shipping address.
     *
     * @param string|null $selectedAddressId - new billing address - can also be set via GET/POST
     * @param string|null $sRedirectToURL
     * @param bool        $bInternalCall     - if set to true the redirect URLs will be ignored
     *
     * @return void
     */
    public function SelectShippingAddress($selectedAddressId = null, $sRedirectToURL = null, $bInternalCall = false)
    {
        $aUserData = $this->getInputFilterUtil()->getFilteredInput(TdbDataExtranetUserAddress::FORM_DATA_NAME_SHIPPING);
        if (is_null($selectedAddressId)) {
            if (is_array($aUserData) && array_key_exists('selectedAddressId', $aUserData)) {
                $selectedAddressId = $aUserData['selectedAddressId'];
            }
            if (empty($selectedAddressId)) {
                $selectedAddressId = null;
            }
        }
        if (is_null($sRedirectToURL)) {
            $sRedirectToURL = $this->getRedirectToUrlFromRequest();
        }
        $oUser = $this->getExtranetUserProvider()->getActiveUser();
        if (!is_null($selectedAddressId) && $oUser->IsLoggedIn() && !is_null($oUser->id)) {
            $iNewShippingAddressId = null;
            if ('new' == $selectedAddressId) {
                // create a new address, and set it
                $oAdr = TdbDataExtranetUserAddress::GetNewInstance();
                /** @var $oAdr TdbDataExtranetUserAddress */
                $oLocal = &TCMSLocal::GetActive();
                $aData = array('name' => 'neue Addresse (angelegt am '.$oLocal->FormatDate(date('Y-m-d')).')');
                $oAdr->LoadFromRowProtected($aData);
                $oAdr->Save();
                $iNewShippingAddressId = $oAdr->id;
            } else {
                $iNewShippingAddressId = $selectedAddressId;
            }
            $aUser = $oUser->sqlData;
            $aUser['default_shipping_address_id'] = $iNewShippingAddressId;
            $oUser->LoadFromRow($aUser);
            // check if we are shipping to billing address
            $oUser->Save();
            $oBillingAddress = $oUser->GetBillingAddress(true);
            $oUser->GetShippingAddress(true);
            if ($oBillingAddress->id == $iNewShippingAddressId) {
                $oUser->ShipToBillingAddress(true);
            }
        }

        if (!$bInternalCall) {
            if (!is_null($sRedirectToURL)) {
                $this->RedirectToURL($sRedirectToURL, true);
            }
        }
    }

    /**
     * Updates the billing address with the data passed.
     *
     * @param array $aBillingAddress
     *
     * @return bool
     */
    protected function UpdateBillingAddress($aBillingAddress)
    {
        $bUpdateOk = false;
        if (is_array($aBillingAddress)) {
            $oUser = $this->getExtranetUserProvider()->getActiveUser();

            $oTmpAdr = TdbDataExtranetUserAddress::GetNewInstance();
            /** @var $oTmpAdr TdbDataExtranetUserAddress */
            $oTmpAdr->LoadFromRowProtected($aBillingAddress);
            if ($oTmpAdr->ValidateData(TdbDataExtranetUserAddress::FORM_DATA_NAME_BILLING)) {
                $oUser->UpdateBillingAddress($aBillingAddress);
                $bUpdateOk = true;
                $oBillingAdr = $oUser->GetBillingAddress();

                // if the billing address has an id, then update the user as well to point to that address
                if (!is_null($oBillingAdr->id)) {
                    $oUser->SaveFieldsFast(array('default_billing_address_id' => $oBillingAdr->id));
                }
            }
        }

        return $bUpdateOk;
    }

    /**
     * Updates the shipping address with the data passed.
     *
     * @param array $aAddress
     *
     * @return bool
     */
    protected function UpdateShippingAddress($aAddress)
    {
        $bUpdateOk = false;
        if (is_array($aAddress)) {
            $oUser = $this->getExtranetUserProvider()->getActiveUser();

            $oTmpAdr = TdbDataExtranetUserAddress::GetNewInstance();
            /** @var $oTmpAdr TdbDataExtranetUserAddress */
            $oTmpAdr->LoadFromRowProtected($aAddress);
            if ($oTmpAdr->ValidateData(TdbDataExtranetUserAddress::FORM_DATA_NAME_SHIPPING)) {
                $bUpdateOk = $oUser->UpdateShippingAddress($aAddress);
            }
        }

        return $bUpdateOk;
    }

    /**
     * Updates the user's base data only if the user entered the correct password.
     * password fields sRequirePassword and sRequirePassword2 via get/post required     *.
     *
     * @param string|null $sSuccessURL
     * @param string|null $sFailureURL
     *
     * @return void
     */
    public function UpdateUserPasswordRequired($sSuccessURL = null, $sFailureURL = null)
    {
        $this->UpdateUser($sSuccessURL, $sFailureURL, true);
    }

    /**
     * Updates the user's base data.
     * If you want to change a user's login or password, please use ChangeUserEmail() and ChangeUserPassword() instead.
     *
     * @param string|null $sSuccessURL       - redirect on success (can also be passed via POST)
     * @param string|null $sFailureURL       - redirect on failure (can also be passed via POST)
     * @param bool        $bPasswordRequired
     *
     * @return void
     */
    public function UpdateUser($sSuccessURL = null, $sFailureURL = null, $bPasswordRequired = false)
    {
        if (is_null($sSuccessURL)) {
            $sSuccessURL = $this->getSuccessUrlFromRequest();
        }
        if (is_null($sFailureURL)) {
            $sFailureURL = $this->getFailureUrlFromRequest();
        }

        $aData = $this->GetFilteredUserData('aUser');
        // check if the email address exists...
        if (is_array($aData)) {
            // kick id and admin flag... never save it
            if (array_key_exists('id', $aData)) {
                unset($aData['id']);
            }
            if (array_key_exists('isadmin', $aData)) {
                unset($aData['isadmin']);
            }

            $this->PrepareSubmittedData($aData);
            $oUser = $this->getExtranetUserProvider()->getActiveUser();
            $oOldUser = clone $oUser;
            $aOrgData = $oOldUser->sqlData;
            foreach ($aData as $key => $val) {
                $aOrgData[$key] = $val;
            }

            $bValidateLoginRequired = false;
            /*
            * We check if the user is trying to change her/his login or password. If yes, we need to validate the login data and eventually
            * fake the password if it isn't set, so validation doesn't fail. We need this procedure for backwards compatibility,
            * please use ChangeUserEmail() and ChangeUserPassword() to change login data!
            */
            if (array_key_exists('name', $aData) || array_key_exists('password', $aData)) {
                $bValidateLoginRequired = true;
                if (!array_key_exists('password', $aData) && !array_key_exists('password2', $aData)) {
                    $requirePassword = $this->getInputFilterUtil()->getFilteredPostInput('sRequirePassword', null, false, TCMSUserInput::FILTER_PASSWORD);
                    if (null !== $requirePassword) {
                        $aOrgData['password'] = $requirePassword;
                        $aOrgData['password2'] = $aOrgData['password'];
                    }
                }
            }

            $oUser->LoadFromRowProtected($aOrgData);

            if ($bValidateLoginRequired) {
                $bDataValid = $this->ValidateUserLoginData();
            } else {
                $bDataValid = true;
            }

            $bDataValid = $this->ValidateUserData() && $bDataValid;
            if ($bDataValid) {
                if ($bPasswordRequired || $this->PasswordIsRequiredForUpdateUser($oOldUser, $oUser)) {
                    $bPasswordRequired = $this->PasswordIsRequiredForUserActionValid($oOldUser);
                } else {
                    $bPasswordRequired = true;
                }
                if ($bPasswordRequired) {
                    if ($bValidateLoginRequired) {
                        $oUser->HandleEmailChangeOnUpdateUser($oOldUser->GetUserEMail());
                    }
                    $oUser->Save();
                    $oMsgManager = TCMSMessageManager::GetInstance();
                    $oMsgManager->AddMessage(MTCMSWizardCore::MSG_HANDLER_NAME, 'USER-PROFILE-DATA-UPDATED');
                    $this->PostUpdateUserHook();
                    if (!is_null($sSuccessURL)) {
                        $this->RedirectToURL($sSuccessURL, true);
                    } else {
                        $oExtranet = &TdbDataExtranet::GetInstance();
                        $this->RedirectToURL($oExtranet->GetLinkRegisterSuccessPage(), true);
                    }
                }
            }
        }
        if (!is_null($sFailureURL)) {
            $this->RedirectToURL($sFailureURL, true);
        }
    }

    /**
     * Returns true if the password is needed in order to change the provided data.
     *
     * @param TdbDataExtranetUser $oOldUser
     * @param TdbDataExtranetUser $oUser
     *
     * @return bool
     */
    protected function PasswordIsRequiredForUpdateUser($oOldUser, $oUser)
    {
        $bPasswordIsRequired = false;
        $aFieldsNeededPasswordChange = TdbDataExtranetUser::GetFieldsNeededPasswordChange();
        foreach ($aFieldsNeededPasswordChange as $sFieldName) {
            if (is_array($oOldUser->sqlData) && array_key_exists($sFieldName, $oOldUser->sqlData) && is_array($oUser->sqlData) && array_key_exists($sFieldName, $oUser->sqlData)) {
                $sNewValue = $oUser->sqlData[$sFieldName];
                if ('password' == $sFieldName) {
                    if (false === $this->getPasswordHashGenerator()->verify($sNewValue, $oOldUser->sqlData[$sFieldName])) {
                        $bPasswordIsRequired = true;
                        break;
                    }
                } else {
                    if ($sNewValue != $oOldUser->sqlData[$sFieldName]) {
                        $bPasswordIsRequired = true;
                        break;
                    }
                }
            }
        }

        return $bPasswordIsRequired;
    }

    /**
     * If you call this function before your extranet function, the user needs to enter her/his password to get access
     * to the extranet function.
     * Returns true if user has correctly entered his password and password check.
     *
     * @param TdbDataExtranetUser $oUser
     *
     * @return bool
     */
    protected function PasswordIsRequiredForUserActionValid($oUser)
    {
        $inputFilterUtil = $this->getInputFilterUtil();
        $sRequirePassword = $inputFilterUtil->getFilteredPostInput('sRequirePassword', '', false, TCMSUserInput::FILTER_DEFAULT);
        $sRequirePassword2 = $inputFilterUtil->getFilteredPostInput('sRequirePassword2', '', false, TCMSUserInput::FILTER_DEFAULT);
        $oMessageManager = TCMSMessageManager::GetInstance();
        if (!empty($sRequirePassword)) {
            if ($sRequirePassword == $sRequirePassword2 || empty($sRequirePassword2)) {
                if ($oUser->PasswordIsUserPassword($sRequirePassword)) {
                    $PasswordIsRequiredForUserActionValid = true;
                } else {
                    $oMessageManager->AddMessage(TdbDataExtranetUser::MSG_FORM_FIELD.'-sRequirePassword', 'ERROR-EXTRANET-PASSWORD-REQUIRE-NOT-USER-PASSWORD');
                    $PasswordIsRequiredForUserActionValid = false;
                }
            } else {
                $oMessageManager->AddMessage(TdbDataExtranetUser::MSG_FORM_FIELD.'-sRequirePassword', 'ERROR-USER-REGISTER-PWD-NO-MATCH');
                $PasswordIsRequiredForUserActionValid = false;
            }
        } else {
            $oMessageManager->AddMessage(TdbDataExtranetUser::MSG_FORM_FIELD.'-sRequirePassword', 'ERROR-USER-REQUIRED-FIELD-MISSING');
            $PasswordIsRequiredForUserActionValid = false;
        }

        return $PasswordIsRequiredForUserActionValid;
    }

    /**
     * @return void
     */
    protected function Redirect()
    {
    }

    /**
     * Called after the user is saved via UpdateUser().
     *
     * @return void
     */
    protected function PostUpdateUserHook()
    {
    }

    /**
     * @param array $aData
     *
     * @return void
     */
    protected function PrepareSubmittedData(&$aData)
    {
    }

    /**
     * @param array $aData
     *
     * @return void
     */
    protected function PrepareSubmittedDataForUserAddress(&$aData)
    {
    }

    /**
     * Validates the user login data (login name and password).
     *
     * @return bool
     */
    protected function ValidateUserLoginData()
    {
        $user = $this->getExtranetUserProvider()->getActiveUser();

        return $user->ValidateLoginData($user->sqlData);
    }

    /**
     * Sends a double-opt-in email to the active user.
     *
     * @param bool $sRequestUserKey user id of the user who get the double opt in email
     * @param bool $bIsExternalCall if true redirects to calling page; set to false to avoid the redirect
     *
     * @return void
     */
    protected function SendDoubleOptInEMail($sRequestUserKey = false, $bIsExternalCall = true)
    {
        $oExtranetConfig = &TdbDataExtranet::GetInstance();
        if (false === $sRequestUserKey) {
            $sRequestUserKey = $this->getInputFilterUtil()->getFilteredInput('sUserKey');
        }
        $oUser = $this->getExtranetUserProvider()->getActiveUser();
        if ($oUser->id == $sRequestUserKey && $oUser->IsLoggedIn() && !$oUser->IsConfirmedUser() && $oExtranetConfig->fieldUserMustConfirmRegistration) {
            $oUser->SendRegistrationNotification('registration-with-confirmation');
        }

        if ($bIsExternalCall) {
            $this->RedirectToURL($oExtranetConfig->GetLinkRegisterSuccessPage(true));
        }
    }

    /**
     * Validates the extranet user data (in the current extranet object).
     *
     * @return bool
     */
    protected function ValidateUserData()
    {
        return $this->getExtranetUserProvider()->getActiveUser()->ValidateData();
    }

    /**
     * User login.
     *
     * @param string|null $sSuccessURL
     * @param string|null $sFailureURL
     *
     * @return void
     */
    public function Login($sSuccessURL = null, $sFailureURL = null)
    {
        if (is_null($sSuccessURL)) {
            $sSuccessURL = $this->getSuccessUrlFromRequest();
        }
        if (is_null($sFailureURL)) {
            $sFailureURL = $this->getFailureUrlFromRequest();
        }
        $oUser = $this->getExtranetUserProvider()->getActiveUser();
        if (!$oUser->Login()) {
            $inputFilterUtil = $this->getInputFilterUtil();
            $sConsumer = $inputFilterUtil->getFilteredPostInput('sConsumer', self::MSG_CONSUMER_NAME);

            $this->data['bLoginError'] = true;
            $this->bLoginAttemptedFailed = true;
            $oExtranetConfig = &TdbDataExtranet::GetInstance();
            $aParams = array('forgotPwdLinkStart' => '<a href="'.$oExtranetConfig->GetLinkForgotPasswordPage().'">', 'forgotPwdLinkEnd' => '</a>');
            $oMessage = TCMSMessageManager::GetInstance();
            $loginName = $inputFilterUtil->getFilteredPostInput(ExtranetUserConstants::LOGIN_FORM_FIELD_LOGIN_NAME);
            $loginUser = $oUser->GetValidatedLoginUser($loginName);
            if (null !== $loginUser && '' === $loginUser->fieldPassword) {
                $oMessage->AddMessage($sConsumer, 'ERROR-EXTRANET-LOGIN-RESET-PASSWORD', $aParams);
            } else {
                $oMessage->AddMessage($sConsumer, 'ERROR-EXTRANET-LOGIN-FAILED', $aParams);
            }

            if (!is_null($sFailureURL)) {
                $this->RedirectToURL($sFailureURL, true);
            }
        } else {
            // if a login successpage is set, then we redirect to it
            $this->OnLoginSuccess($sSuccessURL);
        }
    }

    /**
     * Called after a successful login.
     *
     * @param string|null $successURL
     *
     * @return void
     */
    protected function OnLoginSuccess($successURL = null)
    {
        if (null !== $successURL) {
            $this->RedirectToURL($successURL, true);
        }

        $extranetConfig = TdbDataExtranet::GetInstance();

        $redirectURL = $extranetConfig->getLinkLoginSuccessPage();
        
        if (null === $redirectURL) {
            $redirectURL = $extranetConfig->GetLinkMyAccountPage();
        }
        
        $this->RedirectToURL($redirectURL, true);
    }

    /**
     * User logout.
     *
     * @param string $sRedirectToURL - optional URL to redirect after logout
     *
     * @return void
     */
    public function Logout($sRedirectToURL = null)
    {
        $this->getExtranetUserProvider()->getActiveUser()->Logout();
        // now redirect to current page
        if (is_null($sRedirectToURL)) {
            $oConf = &TdbDataExtranet::GetInstance();
            $sLink = $oConf->GetLinkLogoutPage();
            if ($sLink) {
                $sRedirectToURL = $sLink;
            } else {
                $sRedirectToURL = $this->getActivePageService()->getActivePage()->GetRealURLPlain();
            }
        }
        $this->RedirectToURL($sRedirectToURL, true);
    }

    /**
     * Loads a user based on the username or email field.
     *
     * @return TdbDataExtranetUser|null
     */
    protected function GetRequestPasswordUser()
    {
        $oRequestPasswordUser = null;
        $inputFilterUtil = $this->getInputFilterUtil();
        $sUserName = $inputFilterUtil->getFilteredPostInput('name', null, false, TCMSUserInput::FILTER_DEFAULT);
        $oRequestPasswordUser = $this->GetRequestPasswordUserFromField('name', $sUserName);
        if (is_null($oRequestPasswordUser)) {
            $sUserName = $inputFilterUtil->getFilteredPostInput('email', null, false, TCMSUserInput::FILTER_DEFAULT);
            $oRequestPasswordUser = $this->GetRequestPasswordUserFromField('email', $sUserName);
        }

        return $oRequestPasswordUser;
    }

    /**
     * Loads a user by fieldname and value.
     *
     * @param string $sFieldName
     * @param string $sFieldValue
     *
     * @return TdbDataExtranetUser|null
     */
    protected function GetRequestPasswordUserFromField($sFieldName, $sFieldValue)
    {
        $oSendPasswordUser = null;
        if ('' !== $sFieldName && '' !== $sFieldValue) {
            $oUser = TdbDataExtranetUser::GetNewInstance();
            if ($oUser->LoadFromField($sFieldName, $sFieldValue)) {
                $oSendPasswordUser = $oUser;
            }
        }

        return $oSendPasswordUser;
    }

    /**
     * Change view to enter new password and update user with new password.
     *
     * @return void
     */
    public function ChangeForgotPassword()
    {
        $oMessage = TCMSMessageManager::GetInstance();
        $sentData = $this->GetFilteredUserData('aUser');
        $oUser = $this->getExtranetUserProvider()->getActiveUser();
        if ($oUser->IsLoggedIn()) {
            $oUser->Logout();
        }

        $inputFilterUtil = $this->getInputFilterUtil();
        $passwordHashGenerator = $this->getPasswordHashGenerator();

        $this->data['bPasswordChanged'] = false;
        $sUsername = '';

        $sToken = $inputFilterUtil->getFilteredInput(TdbDataExtranet::URL_PARAMETER_CHANGE_PASSWORD);
        $this->data['sKey'] = $sToken;
        $bPasswordChangeKeyValid = false;

        if (is_array($sentData) && array_key_exists('password', $sentData) && array_key_exists('password2', $sentData) && array_key_exists('name', $sentData)) {
            // process password change form
            $aData = array('password' => $sentData['password'], 'password2' => $sentData['password2']);
            $oUser = $oUser->GetValidatedLoginUser($sentData['name']);
            if (null !== $oUser && $passwordHashGenerator->verify($sToken, $oUser->fieldPasswordChangeKey)) {
                if ($oUser->IsPasswordChangeKeyValid()) {
                    $bPasswordChangeKeyValid = true;
                    $aOrgData = $oUser->sqlData;
                    foreach ($aData as $key => $val) {
                        $aOrgData[$key] = $val;
                    }
                    $oUser->LoadFromRow($aOrgData);

                    $bDataValid = $oUser->ValidatePassword($sentData['password'], $sentData['password2']);

                    if (true !== $bDataValid) {
                        // invalid password
                        $oMessage->AddMessage(TdbDataExtranetUser::MSG_FORM_FIELD.'-password', $bDataValid);
                    } else {
                        $oUser->sqlData['password_change_key'] = '';
                        $oUser->sqlData['password_change_time_stamp'] = '0000-00-00 00:00:00';
                        $oUser->Save();
                        $oMessage->AddMessage(TdbDataExtranetUser::MSG_FORM_FIELD, 'EXTRANET-FORGOT-PASSWORD-CHANGED');
                        $this->data['bPasswordChanged'] = true;
                    }
                }
            } elseif (null !== $oUser && false === $oUser->IsPasswordChangeKeyValid()) {
                $oMessage->AddMessage(TdbDataExtranetUser::MSG_FORM_FIELD, 'EXTRANET-FORGOT-PASSWORD-CHANGE-KEY-INVALID');
            } else {
                $oMessage->AddMessage(TdbDataExtranetUser::MSG_FORM_FIELD, 'EXTRANET-FORGOT-PASSWORD-WRONG-USER-NAME');
            }
        } else { // show password change form
            $oUser = null;
        }

        $this->data['sUsername'] = $sUsername;
        $this->data['oPasswordChangeUser'] = (null !== $oUser) ? $oUser : TdbDataExtranetUser::GetNewInstance();
        $this->data['bPasswordChangeKeyValid'] = $bPasswordChangeKeyValid;

        $this->SetTemplate($this->aModuleConfig['model'], 'inc/ChangePassword');
    }

    /**
     * {@inheritdoc}
     */
    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        $aIncludes = array_merge($aIncludes, $this->getResourcesForSnippetPackage('common/userInput/form'));
        $aIncludes = array_merge($aIncludes, $this->getResourcesForSnippetPackage('common/textBlock'));

        return $aIncludes;
    }

    /**
     * {@inheritdoc}
     */
    public function _AllowCache()
    {
        return false;
    }

    /**
     * {@inheritdoc}
     */
    public function _GetCacheTableInfos()
    {
        $aData = parent::_GetCacheTableInfos();
        if (!is_array($aData)) {
            $aData = array();
        }
        $oActivePage = $this->getActivePageService()->getActivePage();
        $iPageId = '';

        if (!is_null($oActivePage)) {
            $iPageId = $oActivePage->id;
        }
        $aData[] = array('table' => 'cms_tpl_page', 'id' => $iPageId);
        $aData[] = array('table' => 'cms_tpl_page_cms_usergroup_mlt', 'id' => '');
        $aData[] = array('table' => 'cms_tpl_page_data_extranet_group_mlt', 'id' => '');
        $aData[] = array('table' => 'data_extranet_group', 'id' => '');
        $aData[] = array('table' => 'data_extranet_user', 'id' => '');
        $aData[] = array('table' => 'data_extranet', 'id' => '');
        $aData[] = array('table' => 'data_extranet_user_address', 'id' => '');
        $aData[] = array('table' => 'data_extranet_user_data_extranet_group_mlt', 'id' => '');

        return $aData;
    }

    /**
     * {@inheritdoc}
     */
    public function AllowAccessWithoutAuthenticityToken($sMethodName)
    {
        if ('ChangeForgotPassword' === $sMethodName || 'ConfirmUser' === $sMethodName) {
            return true;
        } else {
            return parent::AllowAccessWithoutAuthenticityToken($sMethodName);
        }
    }

    /**
     * Gets user data from input - filtered.
     *
     * @param string $sInputArrayName
     * @param string $fieldBlacklistType Defines which field blacklist to use - one of MTExtranetCoreEndPoint::FIELD_BLACKLIST_TYPE_*
     *
     * @psalm-param self::FIELD_BLACKLIST_TYPE_* $fieldBlacklistType
     *
     * @return array
     */
    protected function GetFilteredUserData($sInputArrayName, $fieldBlacklistType = self::FIELD_BLACKLIST_TYPE_USER)
    {
        $inputFilterUtil = $this->getInputFilterUtil();

        /** @var array $aData */
        $aData = $inputFilterUtil->getFilteredPostInput($sInputArrayName);

        if ($aData) {
            $aFieldBlacklist = $this->getFieldBlackList($fieldBlacklistType);
            if (!empty($aFieldBlacklist)) {
                foreach ($aData as $sFieldName => $sValue) {
                    if (in_array($sFieldName, $aFieldBlacklist)) {
                        unset($aData[$sFieldName]);
                    } else {
                        // trim input
                        if (!is_array($aData[$sFieldName])) {
                            $aData[$sFieldName] = trim($aData[$sFieldName]);
                        }
                    }
                }
                reset($aData);
            }

            // use proper filter for fields
            $aFilterList = array(
                TCMSUserInput::FILTER_DEFAULT => array('name', 'email'),
                TCMSUserInput::FILTER_PASSWORD => array('password', 'password2'),
            );
            $aUnfiltered = $inputFilterUtil->getFilteredPostInput($sInputArrayName, array(), false, TCMSUserInput::FILTER_DEFAULT);
            foreach ($aFilterList as $sFilterClass => $aFields) {
                foreach ($aFields as $sFieldName) {
                    if (array_key_exists($sFieldName, $aUnfiltered)) {
                        $aData[$sFieldName] = $inputFilterUtil->filterValue($aUnfiltered[$sFieldName], $sFilterClass);
                    }
                }
            }
        }

        return $aData;
    }

    /**
     * List of fields that should never be set via post data.
     *
     * @param string $blacklistType Defines which field blacklist to use - one of MTExtranetCoreEndPoint::FIELD_BLACKLIST_TYPE_*
     *
     * @psalm-param self::FIELD_BLACKLIST_TYPE_* $blacklistType
     *
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function getFieldBlackList($blacklistType = self::FIELD_BLACKLIST_TYPE_USER)
    {
        if (!isset(self::$fieldBlacklist[$blacklistType])) {
            throw new \InvalidArgumentException('Invalid field blacklist type: '.$blacklistType);
        }

        return self::$fieldBlacklist[$blacklistType];
    }

    /**
     * Redirects to a URL. Redirect is only allowed if $this->bPreventRedirects is false.
     *
     * @param string $sURL
     * @param bool   $bAllowOnlyRelativeURLs
     *
     * @return void
     */
    protected function RedirectToURL($sURL, $bAllowOnlyRelativeURLs = false)
    {
        if (false == $this->bPreventRedirects) {
            $this->getRedirect()->redirect($sURL, Response::HTTP_FOUND, $bAllowOnlyRelativeURLs);
        }
    }

    /**
     * Changes the e-mail address of a user. The user's password is required to verify the change.
     *
     * @return bool $bSuccess
     */
    public function ChangeUserEmail()
    {
        $bSuccess = false;

        $inputFilterUtil = $this->getInputFilterUtil();
        $sCustomConsumer = $inputFilterUtil->getFilteredPostInput('sCustomConsumer', TdbDataExtranetUser::MSG_FORM_FIELD);

        $aData = $this->GetFilteredUserData('aUser');
        if (is_array($aData)) {
            $this->PrepareSubmittedData($aData);
            $oUser = $this->getExtranetUserProvider()->getActiveUser();
            $sPassword = $inputFilterUtil->getFilteredPostInput('sRequirePassword', null, false, TCMSUserInput::FILTER_PASSWORD);
            if ($oUser->PasswordIsUserPassword($sPassword)) {
                $oExtranetConfig = TdbDataExtranet::GetInstance();
                $sNewEmail = '';
                if (!$oExtranetConfig->fieldLoginIsEmail) {
                    if (array_key_exists('email', $aData)) {
                        $sNewEmail = $aData['email'];
                    } elseif (array_key_exists('name', $aData)) {
                        $sNewEmail = $aData['name'];
                    }
                } elseif (array_key_exists('name', $aData)) {
                    $sNewEmail = $aData['name'];
                }
                $bSuccess = $oUser->ChangeEmail($sNewEmail, $sPassword, $sCustomConsumer);
            } else {
                $oMsgManager = TCMSMessageManager::GetInstance();
                $oMsgManager->AddMessage($sCustomConsumer.'-sRequirePassword', 'ERROR-EXTRANET-PASSWORD-REQUIRE-NOT-USER-PASSWORD');
            }
        }
        if ($bSuccess) {
            $oMsgManager = TCMSMessageManager::GetInstance();
            $oMsgManager->AddMessage($sCustomConsumer.'-sChangeEmailSuccess', 'SUCCESS-EXTRANET-EMAIL-CHANGED');

            $this->ChangeUserEmailSuccessHook();
        } else {
            $this->ChangeUserEmailFailureHook();
        }

        return $bSuccess;
    }

    /**
     * Hook is executed when a user successfully changed his email address.
     *
     * @return void
     */
    protected function ChangeUserEmailSuccessHook()
    {
        $url = $this->getSuccessUrlFromRequest();
        if (null !== $url) {
            $this->RedirectToURL($url);
        }
    }

    /**
     * Hook is executed when changing email address of a user failed.
     *
     * @return void
     */
    protected function ChangeUserEmailFailureHook()
    {
        $url = $this->getFailureUrlFromRequest();
        if (null !== $url) {
            $this->RedirectToURL($url);
        }
    }

    /**
     * Sets a new password for a user, old password is required to verify the change.
     * You may add a hidden field with name sConsumerName to your form to overwrite the consumer.
     *
     * @return bool $bSuccess
     */
    public function ChangeUserPassword()
    {
        $bSuccess = false;
        $aData = $this->GetFilteredUserData('aUser');

        $sCustomConsumer = $this->getInputFilterUtil()->getFilteredPostInput('sCustomConsumer', TdbDataExtranetUser::MSG_FORM_FIELD);

        if (is_array($aData)) {
            $this->PrepareSubmittedData($aData);
            if (!array_key_exists('password', $aData)) {
                $aData['password'] = '';
            }
            if (!array_key_exists('password2', $aData)) {
                $aData['password2'] = '';
            }
            if ($this->ChangeUserPasswordIsAllowed($sCustomConsumer)) {
                $bSuccess = $this->getExtranetUserProvider()->getActiveUser()->ChangePassword($aData['password'], $aData['password2'], true, $sCustomConsumer);
            }
        }
        if ($bSuccess) {
            $oMsgManager = TCMSMessageManager::GetInstance();
            $oMsgManager->AddMessage($sCustomConsumer.'-sChangePasswordSuccess', 'SUCCESS-EXTRANET-PASSWORD-CHANGED');
            $this->ChangeUserPasswordSuccessHook();
        } else {
            $this->ChangeUserPasswordFailureHook();
        }

        return $bSuccess;
    }

    /**
     * Use this method to specify any requirements a user needs to meet in order to change her/his password.
     *
     * @param string $sCustomMessageConsumer
     *
     * @return bool $bIsAllowed
     */
    protected function ChangeUserPasswordIsAllowed($sCustomMessageConsumer = TdbDataExtranetUser::MSG_FORM_FIELD)
    {
        $bIsAllowed = false;
        $oUser = $this->getExtranetUserProvider()->getActiveUser();
        if ($oUser->IsLoggedIn()) {
            $sPassword = $this->getInputFilterUtil()->getFilteredPostInput('sRequirePassword', null, false, TCMSUserInput::FILTER_PASSWORD);
            if ($oUser->PasswordIsUserPassword($sPassword)) {
                $bIsAllowed = true;
            } else {
                $oMsgManager = TCMSMessageManager::GetInstance();
                $oMsgManager->AddMessage($sCustomMessageConsumer.'-sRequirePassword', 'ERROR-EXTRANET-PASSWORD-REQUIRE-NOT-USER-PASSWORD');
            }
        }

        return $bIsAllowed;
    }

    /**
     * Hook is executed when user changed her/his password successfully.
     *
     * @return void
     */
    protected function ChangeUserPasswordSuccessHook()
    {
        $url = $this->getSuccessUrlFromRequest();
        if (null !== $url) {
            $this->RedirectToURL($url);
        }
    }

    /**
     * Hook is executed when changing password of a user failed.
     *
     * @return void
     */
    protected function ChangeUserPasswordFailureHook()
    {
        $url = $this->getFailureUrlFromRequest();
        if (null !== $url) {
            $this->RedirectToURL($url);
        }
    }

    /**
     * Sends an email containing password recovery information if the email address is valid.
     *
     * @return void
     */
    public function SendPassword()
    {
        $bSuccess = false;
        $inputFilterUtil = $this->getInputFilterUtil();
        $sUserEmail = trim($inputFilterUtil->getFilteredPostInput('name', '', false, TCMSUserInput::FILTER_DEFAULT));
        if (empty($sUserEmail)) {
            $sUserEmail = trim($inputFilterUtil->getFilteredPostInput('email', '', false, TCMSUserInput::FILTER_DEFAULT));
        }
        $bIsValid = $this->SendPasswordEmailIsValid($sUserEmail);
        if ($bIsValid) {
            $oExtranetUser = $this->GetRequestPasswordUser();
            if (null !== $oExtranetUser) {
                try {
                    $bSuccess = $oExtranetUser->SendPasswordUsingSaveMode($this->sModuleSpotName);
                } catch (PasswordGenerationFailedException $e) {
                    $bSuccess = false;
                    /*
                     * Don't be too specific about the concrete error. Most likely the error was insufficient entropy,
                     * which is nothing the user should know or should want to know.
                     */
                    $this->data['sErrorMsg'] = $this->getTranslator()->trans('chameleon_system_extranet.send_password.general_error');
                }
            }
        }

        if ($bSuccess) {
            $this->data['bSendPasswordFormSubmitted'] = true;
            $this->SendPasswordSuccessHook();
        } else {
            $this->SendPasswordFailureHook();
        }
    }

    /**
     * Checks if the passed email address is valid for send-password action.
     *
     * @param string $sUserEmail
     *
     * @return bool
     */
    protected function SendPasswordEmailIsValid($sUserEmail)
    {
        $bIsValid = true;

        $this->data['name'] = $sUserEmail;
        $this->data['Name'] = $sUserEmail;
        if (empty($sUserEmail)) {
            $bIsValid = false;
            $this->data['bError'] = true;
            $this->data['sErrorMsg'] = TGlobal::Translate('chameleon_system_extranet.send_password.email_required');
        }

        $oExtranetConfig = TdbDataExtranet::GetInstance();
        if ($bIsValid && $oExtranetConfig->fieldLoginIsEmail && false == TTools::IsValidEMail($sUserEmail)) {
            $bIsValid = false;

            $this->data['bError'] = true;
            $this->data['sErrorMsg'] = TGlobal::Translate('chameleon_system_extranet.send_password.email_invalid');
        }

        return $bIsValid;
    }

    /**
     * Hook is executed when the forgot-password function was successful
     * (which is also true if we didn't find a user for the given email address).
     *
     * @return void
     */
    protected function SendPasswordSuccessHook()
    {
        $this->bPasswordSent = true;
    }

    /**
     * Hook is executed when the forgot-password function failed.
     *
     * @return void
     */
    protected function SendPasswordFailureHook()
    {
        $this->bPasswordSent = false;
        $this->data['bError'] = true;
    }

    /**
     * @param string $filter
     *
     * @return string|null
     */
    protected function getSuccessUrlFromRequest($filter = TCMSUserInput::FILTER_URL)
    {
        return $this->getUrlFromRequest('sSuccessURL', $filter);
    }

    /**
     * @param string $filter
     *
     * @return string|null
     */
    protected function getFailureUrlFromRequest($filter = TCMSUserInput::FILTER_URL)
    {
        return $this->getUrlFromRequest('sFailureURL', $filter);
    }

    /**
     * @param string $filter
     *
     * @return string|null
     */
    protected function getRedirectToUrlFromRequest($filter = TCMSUserInput::FILTER_URL)
    {
        return $this->getUrlFromRequest('sRedirectToURL', $filter);
    }

    /**
     * @param string $name
     * @param string $filter
     *
     * @return string|null
     */
    protected function getUrlFromRequest($name, $filter = TCMSUserInput::FILTER_URL)
    {
        $url = trim($this->getInputFilterUtil()->getFilteredPostInput($name, '', false, $filter));
        if (empty($url)) {
            $url = null;
        }

        return $url;
    }

    /**
     * @return ExtranetUserProviderInterface
     */
    private function getExtranetUserProvider()
    {
        return ServiceLocator::get('chameleon_system_extranet.extranet_user_provider');
    }

    /**
     * @return InputFilterUtilInterface
     */
    private function getInputFilterUtil()
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }

    /**
     * @return PasswordHashGeneratorInterface
     */
    private function getPasswordHashGenerator()
    {
        return ServiceLocator::get('chameleon_system_core.security.password.password_hash_generator');
    }

    /**
     * @return ICmsCoreRedirect
     */
    private function getRedirect()
    {
        return ServiceLocator::get('chameleon_system_core.redirect');
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslator()
    {
        return ServiceLocator::get('translator');
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return ServiceLocator::get('chameleon_system_core.active_page_service');
    }
}
