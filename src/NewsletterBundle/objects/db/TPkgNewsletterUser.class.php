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
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Service\SystemPageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\ExtranetBundle\Interfaces\ExtranetUserProviderInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class TPkgNewsletterUser extends TPkgNewsletterUserAutoParent
{
    const URL_USER_ID_PARAMETER = 'TPkgNewsletterUserId';

    /**
     * Get newsletter user for given email and active portal.
     *
     * @param $sEmail
     *
     * @return TdbPkgNewsletterUser|null
     */
    public static function &GetInstanceForMail($sEmail)
    {
        $oInst = null;
        $oPortal = self::getPortalDomainServiceStatic()->getActivePortal();
        $oInst = TdbPkgNewsletterUser::GetNewInstance();
        if (!is_null($oPortal)) {
            if (!$oInst->LoadFromFields(array('email' => $sEmail, 'cms_portal_id' => $oPortal->id))) {
                $oInst = null;
            }
        } else {
            if (!$oInst->LoadFromField('email', $sEmail)) {
                $oInst = null;
            }
        }

        return $oInst;
    }

    /**
     * return instance for current active user. null if user has not registered yet.
     *
     * @param bool $bRefresh
     *
     * @return TdbPkgNewsletterUser
     */
    public static function &GetInstanceForActiveUser($bRefresh = false)
    {
        static $oInst = false;
        if (false === $oInst || $bRefresh) {
            $oInst = null;
            $user = self::getExtranetUserProvider()->getActiveUser();
            if (null !== $user && $user->IsLoggedIn()) {
                /** @var $oInst TdbPkgNewsletterUser */
                $oInst = TdbPkgNewsletterUser::GetNewInstance();
                $oPortal = self::getPortalDomainServiceStatic()->getActivePortal();
                $bNewsletterUserLoaded = false;
                $bNewsletterLoadedOnlyFromEMail = false;
                if (!is_null($oPortal)) {
                    if (!$oInst->LoadFromFields(array('data_extranet_user_id' => $user->id, 'cms_portal_id' => $oPortal->id))) {
                        if ($oInst->LoadFromFields(array('email' => $user->GetUserEMail(), 'cms_portal_id' => $oPortal->id))) {
                            $bNewsletterLoadedOnlyFromEMail = true;
                        }
                    } else {
                        $bNewsletterUserLoaded = true;
                    }
                } else {
                    if (!$oInst->LoadFromField('data_extranet_user_id', $user->id)) {
                        if ($oInst->LoadFromField('email', $user->GetUserEMail())) {
                            $bNewsletterLoadedOnlyFromEMail = true;
                        }
                    } else {
                        $bNewsletterUserLoaded = true;
                    }
                }
                if (true == $bNewsletterLoadedOnlyFromEMail) {
                    if ($oInst->fieldDataExtranetUserId < 1) {
                        $aData = $oInst->sqlData;
                        $aData['data_extranet_user_id'] = $user->id;
                        // update user name using login data
                        $aData['data_extranet_salutation_id'] = $user->fieldDataExtranetSalutationId;
                        $aData['lastname'] = $user->fieldLastname;
                        $aData['firstname'] = $user->fieldFirstname;
                        $oInst->LoadFromRow($aData);
                        $oInst->AllowEditByAll(true);
                        $oInst->Save();
                        $bNewsletterUserLoaded = true;
                    }
                }
                if (false === $bNewsletterUserLoaded) {
                    $oInst = null;
                }
            }
        }

        return $oInst;
    }

    /**
     * return list of available newsletters.
     *
     * @param TdbPkgNewsletterGroupList $oAvailableNewsletterList
     *
     * @return array
     */
    public function CompareNewsletterLists($oAvailableNewsletterList)
    {
        $aAvailableForUserList = array();
        while ($oAvailableNewsletter = &$oAvailableNewsletterList->Next()) {
            if (!$this->HasConnection('pkg_newsletter_group_mlt', $oAvailableNewsletter->id)) {
                $aAvailableForUserList[$oAvailableNewsletter->id] = $oAvailableNewsletter;
            }
        }

        return $aAvailableForUserList;
    }

    /**
     * return instance based on the id in the url
     * TdbPkgNewsletterUser.
     */
    public static function &GetInstanceFromURLId()
    {
        static $oInstance = false;
        if (false === $oInstance) {
            $oGlobal = TGlobal::instance();
            $sId = $oGlobal->GetUserData(TdbPkgNewsletterUser::URL_USER_ID_PARAMETER);
            /** @var $oInstance TdbPkgNewsletterUser */
            $oInstance = TdbPkgNewsletterUser::GetNewInstance();
            if (!$oInstance->Load($sId)) {
                $oInstance = null;
            }
        }

        return $oInstance;
    }

    /**
     * confirm the newsletter sign up
     * if you add a newsletter confirmation this will be confirmed other whise
     * the newsletter use will be confirmed.
     *
     * @param TdbPkgNewsletterConfirmation $oNewsletterConfirmation
     */
    public function ConfirmSignup($oNewsletterConfirmation = null)
    {
        if (is_null($oNewsletterConfirmation)) {
            if (!$this->fieldOptin) {
                $aData = $this->sqlData;
                $aData['optin'] = '1';
                $aData['optin_date'] = date('Y-m-d H:i:s');
                $this->LoadFromRow($aData);
                $this->AllowEditByAll(true);
                $this->Save();
                $this->PostConfirmNewsletterUserHook();
            }
        } else {
            if (0 == $oNewsletterConfirmation->fieldConfirmation) {
                $oNewsletterConfirmation->sqlData['confirmation'] = 1;
                $oNewsletterConfirmation->AllowEditByAll();
                $oNewsletterConfirmation->Save();
                $oNewsletterConfirmation->AllowEditByAll(false);
                $aNewsletterGroupIdList = array($oNewsletterConfirmation->sqlData['pkg_newsletter_group_id']);
                $this->AddNewsletterGroupConnection($aNewsletterGroupIdList, true);
            }
        }
    }

    /**
     * return a link that can be used to confirm the registration.
     *
     * @param TdbPkgNewsletterModuleSignupConfig|null $oNewsletterConfig
     *
     * @return string
     */
    public function GetLinkConfirmRegistration($oNewsletterConfig = null)
    {
        $aLinkParams = array(
            'optincode' => $this->sqlData['optincode'],
        );
        if (is_null($oNewsletterConfig)) {
            try {
                return $this->getSystemPageService()->getLinkToSystemPageAbsolute('newsletterconfirm', $aLinkParams);
            } catch (RouteNotFoundException $e) {
                return '';
            }
        } else {
            $ConfirmUrl = $this->GetNewsletterModuleLink($oNewsletterConfig->fieldCmsTplModuleInstanceId, $aLinkParams);
            if (!empty($ConfirmUrl)) {
                return $ConfirmUrl;
            } else {
                return $this->getActivePageService()->getLinkToActivePageAbsolute($aLinkParams);
            }
        }
    }

    /**
     * @param string $sModuleInstanceId
     * @param array  $aLinkParams
     *
     * @return string
     */
    public function GetNewsletterModuleLink($sModuleInstanceId, $aLinkParams = array())
    {
        $oPortal = self::getPortalDomainServiceStatic()->getActivePortal();
        $oModuleInstance = TdbCmsTplModuleInstance::GetNewInstance();
        $oModuleInstance->Load($sModuleInstanceId);
        $oMasterPageList = $oModuleInstance->GetFieldCmsTplPageCmsMasterPagedefSpotList();
        $sReturnURL = '';
        while ($oMasterPage = &$oMasterPageList->Next()) {
            $oPage = TdbCmsTplPage::GetNewInstance();
            $oPage->Load($oMasterPage->fieldCmsTplPageId);
            if ($oPage->fieldCmsPortalId == $oPortal->id) {
                $sReturnURL = self::getPageService()->getLinkToPageObjectAbsolute($oPage, $aLinkParams);
                break;
            }
        }

        return $sReturnURL;
    }

    /**
     * return link to un-subscribe page.
     *
     * @param $sPkgNewsletterGroupId
     *
     * @return string
     */
    public function GetLinkUnsubscribe($sPkgNewsletterGroupId)
    {
        $aParams = $this->GetUnsubscribeLinkParameter($sPkgNewsletterGroupId);
        if (is_array($sPkgNewsletterGroupId)) {
            $sPkgNewsletterGroupId = $sPkgNewsletterGroupId[0];
        }

        return $this->GenerateUnsubscribeLink($sPkgNewsletterGroupId, $aParams);
    }

    /**
     * Get link for page where newsletter sign out module was configured at.
     *
     * @param string $sPkgNewsletterGroupId
     * @param array  $aURLParameter
     *
     * @return string
     */
    protected function GenerateUnsubscribeLink($sPkgNewsletterGroupId, $aURLParameter)
    {
        $oPortal = null;
        $oGroup = TdbPkgNewsletterGroup::GetNewInstance();
        $oGroup->Load($sPkgNewsletterGroupId);
        $oPortal = $oGroup->GetFieldCmsPortal();

        return $this->getSystemPageService()->getLinkToSystemPageAbsolute('unsubscribe', $aURLParameter, $oPortal);
    }

    /**
     * Get link parameter for unsubscribe link.
     *
     * @param string $sPkgNewsletterGroupId
     * @param string|false   $sUnsubscribeCode      if given, add opt-out-key to link (use with new newsletter module).O
     *                                      Otherwise generate link for old newsletter module.
     *
     * @return array<string, mixed>
     */
    protected function GetUnsubscribeLinkParameter($sPkgNewsletterGroupId, $sUnsubscribeCode = false)
    {
        if (false === $sUnsubscribeCode) {
            $aParams = array(
                MTPkgNewsletterSignoutCore::URL_PARAM_DATA => array(
                    MTPkgNewsletterSignoutCore::URL_PARAM_NEWSLETTER_USER_ID => $this->id,
                    MTPkgNewsletterSignoutCore::URL_PARAM_GROUP_ID => $sPkgNewsletterGroupId,
                ),
            );
        } else {
            $aParams = array('optoutcode' => $sUnsubscribeCode);
        }

        return $aParams;
    }

    /**
     * Get unsubscribe link with opt-out-code to unsubscribe user without double opt-out-email.
     *
     * @param string|array $sPkgNewsletterGroupId
     * @param bool         $bUnsubscribeCodeForAll
     *
     * @return string
     */
    public function GetLinkUnsubscribeWithCode($sPkgNewsletterGroupId, $bUnsubscribeCodeForAll = false)
    {
        $sUnsubscribeCode = false;
        if (false === $bUnsubscribeCodeForAll) {
            $oNewsletterConfirmationList = $this->GetFieldPkgNewsletterConfirmationList();
            if (is_array($sPkgNewsletterGroupId)) {
                $sUnsubscribeCode = array();
                while ($oNewsletterConfirmation = $oNewsletterConfirmationList->Next()) {
                    if (in_array($oNewsletterConfirmation->fieldPkgNewsletterGroupId, $sPkgNewsletterGroupId)) {
                        $sUnsubscribeCode[] = $this->SetOptOutCodeToConfirmation($oNewsletterConfirmation);
                    }
                }
            } else {
                while ($oNewsletterConfirmation = $oNewsletterConfirmationList->Next()) {
                    if ($oNewsletterConfirmation->fieldPkgNewsletterGroupId == $sPkgNewsletterGroupId) {
                        $sUnsubscribeCode = $this->SetOptOutCodeToConfirmation($oNewsletterConfirmation);
                        break;
                    }
                }
            }
        }

        if (false === $sUnsubscribeCode) {
            $sUnsubscribeCode = $this->SetOptOutCodeToNewsletterUser();
        }
        if (is_array($sPkgNewsletterGroupId)) {
            $sPkgNewsletterGroupId = $sPkgNewsletterGroupId[0];
        }
        $aParams = $this->GetUnsubscribeLinkParameter($sPkgNewsletterGroupId, $sUnsubscribeCode);
        $sURL = $this->GenerateUnsubscribeLink($sPkgNewsletterGroupId, $aParams);

        return $sURL;
    }

    /**
     * return true if the email has already been registered.
     *
     * @return bool
     */
    public function EMailAlreadyRegistered()
    {
        $bIsRegistered = true;
        $query = 'SELECT *
                  FROM `pkg_newsletter_user`';
        $query .= "  WHERE `email` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->fieldEmail)."'";
        if (!is_null($this->id)) {
            $query .= "AND `id` != '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'";
        }
        if (property_exists($this, 'fieldCmsPortalId') && !is_null($this->fieldCmsPortalId)) {
            $query .= " AND `cms_portal_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->fieldCmsPortalId)."'";
        }

        $rResult = MySqlLegacySupport::getInstance()->query($query);
        if (MySqlLegacySupport::getInstance()->num_rows($rResult) < 1) {
            $bIsRegistered = false;
        }

        return $bIsRegistered;
    }

    /**
     * return true if the email has already been registered by an extranet user.
     *
     * @return bool
     */
    public function EMailAlreadyRegisteredUser()
    {
        $bIsRegistered = true;
        $query = 'SELECT *
                  FROM `data_extranet_user`';
        $query .= "  WHERE `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->fieldEmail)."'";
        $rResult = MySqlLegacySupport::getInstance()->query($query);
        if (MySqlLegacySupport::getInstance()->num_rows($rResult) < 1) {
            $bIsRegistered = false;
        }

        return $bIsRegistered;
    }

    /**
     * return true if newsletter is bind to extranet_user.
     *
     * @return bool
     */
    protected function NewsletterBoundToExtranet()
    {
        return TTools::FieldExists('pkg_newsletter_user', 'data_extranet_user_id');
    }

    /**
     * load data from row, but protected items that the user should not set via get/post.
     *
     * @param array $aData
     */
    public function LoadFromRowProtected($aData)
    {
        $aWhiteList = $this->getFieldWhitelistForLoadByRow();
        $aRealData = $this->sqlData;
        foreach ($aData as $field => $val) {
            if (in_array($field, $aWhiteList)) {
                $aRealData[$field] = $val;
            }
        }
        $oUser = TdbDataExtranetUser::GetInstance();
        $aRealData['data_extranet_user_id'] = $oUser->id;
        $this->LoadFromRow($aRealData);
    }

    /**
     * @return array
     */
    protected function getFieldWhitelistForLoadByRow()
    {
        $aWhiteList = array('email', 'data_extranet_salutation_id', 'lastname', 'firstname', 'cms_portal_id', 'company');

        return $aWhiteList;
    }

    public function PostInsertHook()
    {
        parent::PostInsertHook();
        $oNewsletterConfig = $this->GetFromInternalCache('tmpoNewsletterConfigForPostInsertHook');

        // set opt in code
        if (!array_key_exists('optin', $this->sqlData) || '1' != $this->sqlData['optin']) {
            $aData = $this->sqlData;
            $aData['signup_date'] = date('Y-m-d H:i:s');
            $aData['optincode'] = md5(uniqid(rand(), true));
            if (is_null($oNewsletterConfig)) {
                $aData['optin'] = '0';
                $this->LoadFromRow($aData);
                $this->Save();
                $this->SendDoubleOptInEMail();
            } else {
                $oUser = TdbDataExtranetUser::GetInstance();
                if ($oNewsletterConfig->fieldUseDoubleoptin && (!$oUser->IsLoggedIn() || ($oUser->IsLoggedIn() && $oUser->fieldName != $aData['email']))) {
                    $oFindUserByEmail = TdbDataExtranetUser::GetNewInstance();
                    $oFindUserByEmail->LoadFromField('name', $aData['email']);

                    $aData['optin'] = '0';
                    $this->LoadFromRow($aData);
                    if (!empty($oFindUserByEmail->id)) {
                        $this->sqlData['data_extranet_user_id'] = $oFindUserByEmail->id;
                    } else {
                        $this->sqlData['data_extranet_user_id'] = '';
                    }
                    $this->Save();
                    $this->SendDoubleOptInEMail($oNewsletterConfig);
                } else {
                    $this->ConfirmSignupNew($aData);
                }
            }
        }
    }

    public function SendDoubleOptInEMail($oNewsletterConfig = null)
    {
        $oMail = TdbDataMailProfile::GetProfile('newsletter-double-opt-in');
        $aData = $this->sqlData;
        $oSal = &$this->GetFieldDataExtranetSalutation();
        if (is_null($oSal)) {
            $data_extranet_salutation_name = '';
        } else {
            $data_extranet_salutation_name = $oSal->GetName();
        }
        $aData['data_extranet_salutation_name'] = $data_extranet_salutation_name;
        $aData['link'] = $this->GetLinkConfirmRegistration($oNewsletterConfig);

        $oNewsletterGroupList = $this->GetFieldPkgNewsletterGroupList();
        $oNewsletterGroupList->GoToStart();
        $oNewsletterGroup = $oNewsletterGroupList->Current();
        if ($oNewsletterGroup) {
            $aData['unsubscribe_link'] = $this->GetLinkUnsubscribe($oNewsletterGroup->id);
        }

        if (is_null($oSal)) {
            $aData['sFullName'] = implode(' ', array($this->fieldFirstname, $this->fieldLastname));
        } else {
            $aData['sFullName'] = implode(' ', array($oSal->GetName(), $this->fieldFirstname, $this->fieldLastname));
        }
        $oMail->AddDataArray($aData);
        if (empty($aData['sFullName'])) {
            $aData['sFullName'] = $this->fieldEmail;
        }
        $oMail->ChangeToAddress($this->fieldEmail, $aData['sFullName']);
        $oMail->SendUsingObjectView('emails', 'Customer');
    }

    //------ Functions for new newslettermodule--------------------------------

    /**
     * after updating a confirmation save new opt-in code to newsletter user.
     *
     * @param TdbPkgNewsletterModuleSignupConfig $oNewsletterConfig
     */
    public function PostConfirmationUpdateHook($oNewsletterConfig)
    {
        $oUser = TdbDataExtranetUser::GetInstance();
        $this->sqlData['optincode'] = md5(uniqid(rand(), true));
        $this->AllowEditByAll(true);
        $this->Save();
        $this->AllowEditByAll(true);
        if (!is_null($oNewsletterConfig) && $oNewsletterConfig->fieldUseDoubleoptin && (!$oUser->IsLoggedIn() || ($oUser->IsLoggedIn() && $oUser->fieldName != $this->sqlData['email']))) {
            $this->SendDoubleOptInEMail($oNewsletterConfig);
        }
    }

    /**
     * Save active data... create new record if no id present
     * returns id on success... else false.
     *
     * @param TdbPkgNewsletterModuleSignupConfig $oNewsletterConfig
     *
     * @return string|false - id on success... else false
     */
    public function Save($oNewsletterConfig = null)
    {
        $this->SetInternalCache('tmpoNewsletterConfigForPostInsertHook', $oNewsletterConfig);
        $rReturnValue = parent::Save();
        unset($this->aResultCache['tmpoNewsletterConfigForPostInsertHook']);

        return $rReturnValue;
    }

    /**
     * save one confirmation for each chosen newsletter group and updates mlt connections from newsletter user to confirmation.
     *
     * @param array                              $aNewsletterList   list with newslettergroupids
     * @param TdbPkgNewsletterModuleSignupConfig $oNewsletterConfig
     *
     * @return bool $bDoConfirmationUpdate true = add or update a confirmation , false = no add/update
     */
    public function SaveNewsletterConfirmations($aNewsletterList, $oNewsletterConfig = null)
    {
        $aUpdateNewsletterGroupIdList = array();
        $oNewsletterConfirmationList = $this->GetFieldPkgNewsletterConfirmationList();
        //Get the length here because length uses lazy loading and CreateConfirmation can change the length.
        $confirmationListCount = $oNewsletterConfirmationList->Length();
        $bDoConfirmationUpdate = false;
        foreach ($aNewsletterList as $sNewsletterGroupId) {
            if ('all' != $sNewsletterGroupId) {
                $oExistingConfirmation = null;
                while ($oNewsletterConfirmation = $oNewsletterConfirmationList->Next()) {
                    if ($sNewsletterGroupId == $oNewsletterConfirmation->fieldPkgNewsletterGroupId) {
                        $oExistingConfirmation = $oNewsletterConfirmation;
                    }
                }
                $oNewsletterConfirmationList->GoToStart();
                if (is_null($oExistingConfirmation)) {
                    $oNewsletterConfirmation = $this->CreateConfirmation($sNewsletterGroupId);
                    if (!is_null($oNewsletterConfirmation->id)) {
                        $bDoConfirmationUpdate = true;
                        $aUpdateNewsletterGroupIdList[] = $oNewsletterConfirmation->id;
                    }
                } else {
                    if (!$oExistingConfirmation->fieldConfirmation) {
                        $this->UpdateConfirmation($oExistingConfirmation);
                        $bDoConfirmationUpdate = true;
                    }
                    $aUpdateNewsletterGroupIdList[] = $oExistingConfirmation->id;
                }
            }
        }
        $this->AddNewsletterConfirmationConnection($aUpdateNewsletterGroupIdList, true);
        if ($confirmationListCount > 0 && $bDoConfirmationUpdate) {
            $this->PostConfirmationUpdateHook($oNewsletterConfig);
        }

        return $bDoConfirmationUpdate;
    }

    /**
     * Was called after sign up newsletter user only.
     * This will be used if no newsletter groups are existing.
     */
    public function PostSignUpNewsletterUserOnly()
    {
    }

    /**
     * update confirmation with new registration date.
     *
     * @param TdbPkgNewsletterConfirmation $oNewsletterConfirmation
     */
    protected function UpdateConfirmation($oNewsletterConfirmation)
    {
        $oNewsletterConfirmation->sqlData['registration_date'] = date('Y-m-d H:i:s');
        $oNewsletterConfirmation->AllowEditByAll();
        $oNewsletterConfirmation->Save();
        $oNewsletterConfirmation->AllowEditByAll(false);
    }

    /**
     * create one confirmation to one newsletter group
     * by default none confirmed confirmation will be created.
     *
     * @param string $sNewsletterId
     * @param bool   $bCreateConfirmed
     *
     * @return TdbPkgNewsletterConfirmation $oNewsletterConfirmation
     */
    public function CreateConfirmation($sNewsletterId, $bCreateConfirmed = false)
    {
        $oNewsletterConfirmation = TdbPkgNewsletterConfirmation::GetNewInstance();
        $oNewsletterConfirmation->sqlData['registration_date'] = date('Y-m-d H:i:s');
        $oNewsletterConfirmation->sqlData['pkg_newsletter_group_id'] = $sNewsletterId;
        if ($bCreateConfirmed) {
            $oNewsletterConfirmation->sqlData['confirmation'] = 1;
            $oNewsletterConfirmation->sqlData['confirmation_date'] = date('Y-m-d H:i:s');
        }
        $oNewsletterConfirmation->AllowEditByAll();
        $oNewsletterConfirmation->Save();
        $oNewsletterConfirmation->AllowEditByAll(false);
        $this->PostCreateConfirmationHook($oNewsletterConfirmation);

        return $oNewsletterConfirmation;
    }

    /**
     * Was called after creating confirmation.
     *
     * @param TdbPkgNewsletterConfirmation $oNewsletterConfirmation
     */
    protected function PostCreateConfirmationHook($oNewsletterConfirmation)
    {
    }

    /**
     * confirm all of the users confirmations.
     *
     * @param array|null $aNewData
     *
     * @internal param string $sNewsletterId
     *
     * @return bool
     */
    public function ConfirmSignupNew($aNewData = null)
    {
        $bWasSignedUp = false;
        $this->aResultCache = array();
        $oConfirmationList = $this->GetFieldPkgNewsletterConfirmationList();
        if (!is_null($oConfirmationList) && $oConfirmationList->Length() > 0) {
            $aNewsletterGroupIdList = array();
            while ($oConfirmation = $oConfirmationList->Next()) {
                if ('1' != $oConfirmation->sqlData['confirmation']) {
                    $oConfirmation->sqlData['confirmation_date'] = date('Y-m-d H:i:s');
                    $oConfirmation->sqlData['confirmation'] = '1';
                    $oConfirmation->AllowEditByAll(true);
                    $oConfirmation->Save();
                    $oConfirmation->AllowEditByAll(false);
                    $bWasSignedUp = true;
                    $this->PostConfirmNewsletterUserHook($oConfirmation->fieldPkgNewsletterGroupId);
                }
                $aNewsletterGroupIdList[] = $oConfirmation->fieldPkgNewsletterGroupId;
            }
            $this->AddNewsletterGroupConnection($aNewsletterGroupIdList, true);
        }
        if (!$this->fieldOptin) {
            $aData = $this->sqlData;
            $aData['optin'] = '1';
            $aData['optin_date'] = date('Y-m-d H:i:s');
            if (!is_null($aNewData)) {
                $aData = array_merge($aNewData, $aData);
            }
            $this->LoadFromRow($aData);
            $this->AllowEditByAll(true);
            $this->Save();
            $bWasSignedUp = true;
            $this->PostConfirmNewsletterUserHook();
        }

        return $bWasSignedUp;
    }

    /**
     * Was called after confirming newsletter confirmation.
     *
     * @param bool|string $sNewsletterGroupId
     */
    protected function PostConfirmNewsletterUserHook($sNewsletterGroupId = false)
    {
    }

    /**
     * add given newsletter groups to newsletter user.
     *
     * @param $aNewNewsletterConfirmationIdList
     * @param bool $bNoDelete
     */
    public function AddNewsletterGroupConnection($aNewsletterGroupIdList, $bNoDelete = false)
    {
        $this->AllowEditByAll(true);
        $this->UpdateMLT('pkg_newsletter_group_mlt', $aNewsletterGroupIdList, $bNoDelete);
        $this->AllowEditByAll(false);
    }

    /**
     * add given newsletter confirmations to newsletter user.
     *
     * @param $aNewNewsletterConfirmationIdList
     * @param bool $bNoDelete
     */
    public function AddNewsletterConfirmationConnection($aNewNewsletterConfirmationIdList, $bNoDelete = false)
    {
        $this->AllowEditByAll(true);
        $this->UpdateMLT('pkg_newsletter_confirmation_mlt', $aNewNewsletterConfirmationIdList, $bNoDelete);
        $this->AllowEditByAll(false);
    }

    /**
     * Get a list of all signed in newsletter groups.
     *
     * @return TdbPkgNewsletterGroupList $oSignedInNewsletterList
     */
    public function GetSignedInNewsletterList()
    {
        $oSignedInNewsletterList = $this->GetFieldPkgNewsletterGroupList();

        return $oSignedInNewsletterList;
    }

    /**
     * remove all newsletter group connections for newsletter groups given in array $aNewsletterGroupSignOutList.
     *
     * @param array $aNewsletterGroupSignOutList list removable newslettergroups
     *
     * @return bool true = newsletterusert still have connection to a newslettergroup , false = no existing connection to a newslettergroup for user
     */
    public function RemoveNewsletterGroupConnection($aNewsletterGroupSignOutList)
    {
        $aNewNewsletterGroupIdList = array();
        $aSignedInNewsletterGroupIdList = $this->GetMLTIdList('pkg_newsletter_group', 'pkg_newsletter_group_mlt');
        foreach ($aSignedInNewsletterGroupIdList as $sNewsletterGroupId) {
            if (!in_array($sNewsletterGroupId, $aNewsletterGroupSignOutList)) {
                $aNewNewsletterGroupIdList[] = $sNewsletterGroupId;
            }
        }
        if (count($aNewNewsletterGroupIdList) > 0) {
            $this->AddNewsletterGroupConnection($aNewNewsletterGroupIdList);

            return true;
        } else {
            return false;
        }
    }

    /**
     * remove all confirmations for newsletter groups given in array $aNewsletterGroupSignOutList.
     *
     * @param array $aNewsletterGroupSignOutList list removable newslettergroup s
     *
     * @return bool true = newsletterusert still have confirmations , false = no existing confirmations for user
     */
    public function RemoveConfirmationConnection($aNewsletterGroupSignOutList)
    {
        $aNewNewsletterConfirmationIdList = array();
        $oSignedInConfirmationList = $this->GetFieldPkgNewsletterConfirmationList();
        while ($oSignedInConfirmation = &$oSignedInConfirmationList->Next()) {
            if (!in_array($oSignedInConfirmation->fieldPkgNewsletterGroupId, $aNewsletterGroupSignOutList)) {
                $aNewNewsletterConfirmationIdList[] = $oSignedInConfirmation->id;
            } else {
                $oSignedInConfirmation->AllowEditByAll(true);
                $this->PostUnSubscribeNewsletterUser($oSignedInConfirmation->fieldPkgNewsletterGroupId);
                $oSignedInConfirmation->Delete();
            }
        }
        if (count($aNewNewsletterConfirmationIdList) > 0) {
            $this->AddNewsletterConfirmationConnection($aNewNewsletterConfirmationIdList);

            return true;
        } else {
            return false;
        }
    }

    /**
     * send double opt out mail to newsletter user with a link for each signed in newsletter.
     *
     * @return bool
     */
    protected function SendDoubleOptOutMail()
    {
        $oMail = TdbDataMailProfile::GetProfile('newsletter-double-opt-out');
        $aData = $this->sqlData;
        $oSal = &$this->GetFieldDataExtranetSalutation();
        if (is_null($oSal)) {
            $data_extranet_salutation_name = '';
        } else {
            $data_extranet_salutation_name = $oSal->GetName();
        }
        $aData['data_extranet_salutation_name'] = $data_extranet_salutation_name;
        $aLink = $this->GetOptOutConfirmationLink();
        $aData['linkblock'] = $this->GenerateLinkBlock($aLink);
        $aData['link'] = $this->GenerateLinkBlock($aLink, false);
        if (is_null($oSal)) {
            $aData['sFullName'] = implode(' ', array($this->fieldFirstname, $this->fieldLastname));
        } else {
            $aData['sFullName'] = implode(' ', array($oSal->GetName(), $this->fieldFirstname, $this->fieldLastname));
        }
        $oMail->AddDataArray($aData);
        if (empty($aData['sFullName'])) {
            $aData['sFullName'] = $this->fieldEmail;
        }
        $oMail->ChangeToAddress($this->fieldEmail, $aData['sFullName']);

        return $oMail->SendUsingObjectView('emails', 'Customer');
    }

    /**
     * generate link-block for email to confirm a sign-out.
     *
     * @param array $aLinks (Key is used as linkname and value is the real link)
     * @param bool  $bHtml  generate html code or text code
     *
     * @return string $sLinkBlock return all links as string
     */
    protected function GenerateLinkBlock($aLinks, $bHtml = true)
    {
        $sLinkBlock = '';
        foreach ($aLinks as $NewsletterName => $sLink) {
            if ($bHtml) {
                $sLinkBlock .= '<div><a href="'.$sLink.'" title="'.$NewsletterName.'" >'.$NewsletterName.'</a><div><br />';
            } else {
                $sLinkBlock .= $NewsletterName.': '.$sLink.'\r\n';
            }
        }

        return $sLinkBlock;
    }

    /**
     * Set new OptOut codes to all none confirmed confirmations and return array of all opt-out links.
     *
     * @return array array with all newsletter signout links
     */
    protected function GetOptOutConfirmationLink()
    {
        $oConfirmationList = $this->GetFieldPkgNewsletterConfirmationList();
        $aLink = array();
        $aParameter = array('optoutcode' => $this->SetOptOutCodeToNewsletterUser());
        if ($oConfirmationList->Length() > 1) {
            $aLink[TGlobal::Translate('chameleon_system_newsletter.action.unsubscribe_all')] = $this->getActivePageService()->getLinkToActivePageAbsolute($aParameter).'  ';
        } elseif ($oConfirmationList->Length() < 1) {
            $aLink[TGlobal::Translate('chameleon_system_newsletter.action.unsubscribe')] = $this->getActivePageService()->getLinkToActivePageAbsolute($aParameter).'  ';
        }
        while ($oConfirmation = &$oConfirmationList->Next()) {
            $oNewsletterGroup = TdbPkgNewsletterGroup::GetNewInstance();
            $oNewsletterGroup->Load($oConfirmation->fieldPkgNewsletterGroupId);
            $aLink[$oNewsletterGroup->fieldName] = $this->GetOptOutConfirmationLinkForConfirmation($oConfirmation).' ';
        }

        return $aLink;
    }

    /**
     * Get opt-out link for unsubsctibe from newsletter.
     *
     * @param TdbPkgNewsletterConfirmation $oConfirmation
     *
     * @return string
     */
    public function GetOptOutConfirmationLinkForConfirmation($oConfirmation)
    {
        return $this->getActivePageService()->getLinkToActivePageAbsolute(array(
            'optoutcode' => $this->SetOptOutCodeToConfirmation($oConfirmation),
        ));
    }

    /**
     * Set new OptOutCode to given confirmation.
     *
     * @param TdbPkgNewsletterConfirmation $oConfirmation
     *
     * @return string
     */
    protected function SetOptOutCodeToConfirmation($oConfirmation)
    {
        $sOptOutCode = $oConfirmation->fieldOptoutKey;
        if (0 == strlen($sOptOutCode)) {
            $oConfirmation->AllowEditByAll(true);
            $sOptOutCode = md5(uniqid(rand(), true));
            $oConfirmation->sqlData['optout_key'] = $sOptOutCode;
            $oConfirmation->Save();
            $oConfirmation->AllowEditByAll(false);
        }

        return $sOptOutCode;
    }

    /**
     * Set new OptOutCode to Newsletter user.
     * If opt-out-code already set we use the old code otherwise create new.
     * We need to use the old code for example old newsletter subscribe links.
     *
     * @return string
     */
    protected function SetOptOutCodeToNewsletterUser()
    {
        $sOptOutCode = $this->fieldOptoutcode;
        if (0 == strlen($sOptOutCode)) {
            $this->AllowEditByAll(true);
            $sOptOutCode = md5(uniqid(rand(), true));
            $this->sqlData['optoutcode'] = $sOptOutCode;
            $this->Save();
            $this->AllowEditByAll(false);
        }

        return $sOptOutCode;
    }

    /**
     * Sign out Newsletter.
     *
     * @param array $aNewsletterGroupSignOutList if is only a string then the signout came from old singout module so confirmed is set to true
     * @param bool  $bConfirmSignOut             sign out was confirmed -> delete all confirmations with newslettergroups in $aNewsletterGroupSignOutList
     *
     * @return bool
     */
    public function SignOut($aNewsletterGroupSignOutList = array(), $bConfirmSignOut = false)
    {
        $bSignedOut = true;
        $oUser = TdbDataExtranetUser::GetInstance();
        if (!is_array($aNewsletterGroupSignOutList)) {
            $aNewsletterGroupSignOutList = array($aNewsletterGroupSignOutList);
        }
        if ((!$oUser->IsLoggedIn() && !$bConfirmSignOut)) {
            $bSignedOut = $this->SendDoubleOptOutMail();
        } else {
            $bNoDeleteNewsletterUser = $this->RemoveNewsletterGroupConnection($aNewsletterGroupSignOutList);
            if (TCMSRecord::TableExists('pkg_newsletter_confirmation')) {
                $bNoDeleteNewsletterUser = $this->RemoveConfirmationConnection($aNewsletterGroupSignOutList) || $bNoDeleteNewsletterUser;
            }
            $this->SendSignOutConfirmation($aNewsletterGroupSignOutList);
            if (!$bNoDeleteNewsletterUser) {
                if (0 == count($aNewsletterGroupSignOutList)) {
                    $this->PostUnSubscribeNewsletterUser();
                }
                $this->AllowEditByAll(true);
                $this->Delete();
            }
        }

        return $bSignedOut;
    }

    /**
     * Was called after unsubscribing newsletter user.
     *
     * @param bool|string $sNewsletterGroupId
     */
    protected function PostUnSubscribeNewsletterUser($sNewsletterGroupId = false)
    {
    }

    /**
     * Sign out Newsletter.
     *
     * @param array $aNewsletterGroupSignOutList if is only a string then the signout came from old singout module so confirmed is set to true
     *
     * @return void
     */
    public function SendSignOutConfirmation($aNewsletterGroupSignOutList = array())
    {
        $oMail = TdbDataMailProfile::GetProfile('newsletter-sign-out-confirmation');
        $aData = $this->sqlData;

        $oSal = &$this->GetFieldDataExtranetSalutation();
        if (is_null($oSal)) {
            $data_extranet_salutation_name = '';
        } else {
            $data_extranet_salutation_name = $oSal->GetName();
        }
        $aData['data_extranet_salutation_name'] = $data_extranet_salutation_name;

        $aData['signoutinfo'] = '';
        $aData['polite-signoutinfo'] = '';
        if (is_array($aNewsletterGroupSignOutList) && count($aNewsletterGroupSignOutList) > 0) {
            $aData['signoutinfo'] .= TGlobal::Translate('chameleon_system_newsletter.text.group_unsubscribe_info').' <br />';
            $aData['polite-signoutinfo'] .= TGlobal::Translate('chameleon_system_newsletter.text.group_unsubscribe_info_formal').' <br />';
            $oNewsletterGroup = TdbPkgNewsletterGroup::GetNewInstance();
            foreach ($aNewsletterGroupSignOutList as $sNewsletterGroupID) {
                $oNewsletterGroup->Load($sNewsletterGroupID);
                $aData['signoutinfo'] .= $oNewsletterGroup->GetName().'<br />';
                $aData['polite-signoutinfo'] .= $oNewsletterGroup->GetName().'<br />';
            }
            //$aData['signoutinfo'] .= implode('<br />', $aNewsletterGroupSignOutList);
        }
        $oGroupList = $this->GetSignedInNewsletterList();
        $oGroupList->GoToStart();
        $bHasGroups = false;
        if ($oGroupList->Length() != count($aNewsletterGroupSignOutList)) {
            while ($oGroup = $oGroupList->Next()) {
                $aData['signoutinfo'] .= '<br />'.TGlobal::Translate('chameleon_system_newsletter.text.list_of_subscribed_groups').' <br/>';
                $aData['polite-signoutinfo'] .= '<br />'.TGlobal::Translate('chameleon_system_newsletter.text.list_of_subscribed_groups_polite').' <br/>';
                $aData['signoutinfo'] .= $oGroup->GetName().'<br />';
                $aData['polite-signoutinfo'] .= $oGroup->GetName().'<br />';
                $bHasGroups = true;
            }
        }
        if (!$bHasGroups) {
            $aData['signoutinfo'] .= TGlobal::Translate('chameleon_system_newsletter.text.unsubscribed');
            $aData['polite-signoutinfo'] .= TGlobal::Translate('chameleon_system_newsletter.text.unsubscribed_polite');
        }
        $oMail->AddDataArray($aData);
        $oMail->ChangeToAddress($this->fieldEmail, $this->fieldEmail);
        $oMail->SendUsingObjectView('emails', 'Customer');
    }

    /**
     * return TdbPkgNewsletterUser if the email has already been registered otherwise null.
     *
     * @return TdbPkgNewsletterUser|null
     */
    public function EMailAlreadyRegisteredNew()
    {
        $oFoundNewsletterUser = TdbPkgNewsletterUser::GetNewInstance();
        $oPortal = self::getPortalDomainServiceStatic()->getActivePortal();
        if (!is_null($oPortal)) {
            $oFoundNewsletterUser->LoadFromFields(array('email' => $this->fieldEmail, 'cms_portal_id' => $oPortal->id));
        } else {
            $oFoundNewsletterUser->LoadFromField('email', $this->fieldEmail);
        }
        if (is_null($oFoundNewsletterUser)) {
            return null;
        } else {
            return $oFoundNewsletterUser;
        }
    }

    /**
     * Return true if newsletter user is a member of the given group.
     *
     * @param $sPkgNewsletterGroupId
     *
     * @return bool
     */
    public function isInGroup($sPkgNewsletterGroupId)
    {
        $aGroupList = $this->GetFromInternalCache('aNewsletterGroupIdList');
        if (null === $aGroupList) {
            $aGroupList = $this->GetFieldPkgNewsletterGroupIdList();
            $this->SetInternalCache('aNewsletterGroupIdList', $aGroupList);
        }

        return is_array($aGroupList) && in_array($sPkgNewsletterGroupId, $aGroupList);
    }

    /**
     * @return ExtranetUserProviderInterface
     */
    private static function getExtranetUserProvider()
    {
        return ServiceLocator::get('chameleon_system_extranet.extranet_user_provider');
    }

    /**
     * @return SystemPageServiceInterface
     */
    private function getSystemPageService()
    {
        return ServiceLocator::get('chameleon_system_core.system_page_service');
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return ServiceLocator::get('chameleon_system_core.active_page_service');
    }

    private static function getPortalDomainServiceStatic(): PortalDomainServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }
}
