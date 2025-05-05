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
    public const URL_USER_ID_PARAMETER = 'TPkgNewsletterUserId';

    /**
     * Get newsletter user for given email and active portal.
     *
     * @param string $sEmail
     *
     * @return TdbPkgNewsletterUser|null
     */
    public static function GetInstanceForMail($sEmail)
    {
        $newsletterUser = null;
        $portal = self::getPortalDomainServiceStatic()->getActivePortal();
        $newsletterUser = TdbPkgNewsletterUser::GetNewInstance();
        if (!is_null($portal)) {
            if (!$newsletterUser->LoadFromFields(['email' => $sEmail, 'cms_portal_id' => $portal->id])) {
                $newsletterUser = null;
            }
        } else {
            if (!$newsletterUser->LoadFromField('email', $sEmail)) {
                $newsletterUser = null;
            }
        }

        return $newsletterUser;
    }

    /**
     * return instance for current active user. null if user has not registered yet.
     *
     * @param bool $bRefresh
     *
     * @return TdbPkgNewsletterUser|null
     */
    public static function GetInstanceForActiveUser($bRefresh = false)
    {
        static $newsletterUser = false;

        if (false === $newsletterUser || $bRefresh) {
            $newsletterUser = null;
            $user = self::getExtranetUserProvider()->getActiveUser();
            if (null !== $user && $user->IsLoggedIn()) {
                $newsletterUser = TdbPkgNewsletterUser::GetNewInstance();
                $oPortal = self::getPortalDomainServiceStatic()->getActivePortal();
                $newsletterUserLoaded = false;
                $newsletterLoadedOnlyFromEMail = false;
                if (!is_null($oPortal)) {
                    if (!$newsletterUser->LoadFromFields(['data_extranet_user_id' => $user->id, 'cms_portal_id' => $oPortal->id])) {
                        if ($newsletterUser->LoadFromFields(['email' => $user->GetUserEMail(), 'cms_portal_id' => $oPortal->id])) {
                            $newsletterLoadedOnlyFromEMail = true;
                        }
                    } else {
                        $newsletterUserLoaded = true;
                    }
                } else {
                    if (!$newsletterUser->LoadFromField('data_extranet_user_id', $user->id)) {
                        if ($newsletterUser->LoadFromField('email', $user->GetUserEMail())) {
                            $newsletterLoadedOnlyFromEMail = true;
                        }
                    } else {
                        $newsletterUserLoaded = true;
                    }
                }
                if (true == $newsletterLoadedOnlyFromEMail) {
                    if ($newsletterUser->fieldDataExtranetUserId < 1) {
                        $data = $newsletterUser->sqlData;
                        $data['data_extranet_user_id'] = $user->id;
                        // update user name using login data
                        $data['data_extranet_salutation_id'] = $user->fieldDataExtranetSalutationId;
                        $data['lastname'] = $user->fieldLastname;
                        $data['firstname'] = $user->fieldFirstname;
                        $newsletterUser->LoadFromRow($data);
                        $newsletterUser->AllowEditByAll(true);
                        $newsletterUser->Save();
                        $newsletterUserLoaded = true;
                    }
                }
                if (false === $newsletterUserLoaded) {
                    $newsletterUser = null;
                }
            }
        }

        return $newsletterUser;
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
        $availableForUserList = [];
        while ($availableNewsletter = $oAvailableNewsletterList->Next()) {
            if (!$this->HasConnection('pkg_newsletter_group_mlt', $availableNewsletter->id)) {
                $availableForUserList[$availableNewsletter->id] = $availableNewsletter;
            }
        }

        return $availableForUserList;
    }

    /**
     * return instance based on the id in the url
     * TdbPkgNewsletterUser.
     *
     * @return TdbPkgNewsletterUser|null
     */
    public static function GetInstanceFromURLId()
    {
        static $newsletterUser = false;

        if (false === $newsletterUser) {
            $global = TGlobal::instance();
            $id = $global->GetUserData(TdbPkgNewsletterUser::URL_USER_ID_PARAMETER);
            /** @var $newsletterUser TdbPkgNewsletterUser */
            $newsletterUser = TdbPkgNewsletterUser::GetNewInstance();
            if (!$newsletterUser->Load($id)) {
                $newsletterUser = null;
            }
        }

        return $newsletterUser;
    }

    /**
     * confirm the newsletter sign up
     * if you add a newsletter confirmation this will be confirmed other whise
     * the newsletter use will be confirmed.
     *
     * @param TdbPkgNewsletterConfirmation $oNewsletterConfirmation
     *
     * @return void
     */
    public function ConfirmSignup($oNewsletterConfirmation = null)
    {
        if (is_null($oNewsletterConfirmation)) {
            if (!$this->fieldOptin) {
                $data = $this->sqlData;
                $data['optin'] = '1';
                $data['optin_date'] = date('Y-m-d H:i:s');
                $this->LoadFromRow($data);
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
                $aNewsletterGroupIdList = [$oNewsletterConfirmation->sqlData['pkg_newsletter_group_id']];
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
        $linkParams = [
            'optincode' => $this->sqlData['optincode'],
        ];
        if (is_null($oNewsletterConfig)) {
            try {
                return $this->getSystemPageService()->getLinkToSystemPageAbsolute('newsletterconfirm', $linkParams);
            } catch (RouteNotFoundException $e) {
                return '';
            }
        } else {
            $ConfirmUrl = $this->GetNewsletterModuleLink($oNewsletterConfig->fieldCmsTplModuleInstanceId, $linkParams);
            if (!empty($ConfirmUrl)) {
                return $ConfirmUrl;
            } else {
                return $this->getActivePageService()->getLinkToActivePageAbsolute($linkParams);
            }
        }
    }

    /**
     * @param string $sModuleInstanceId
     * @param array $aLinkParams
     *
     * @return string
     */
    public function GetNewsletterModuleLink($sModuleInstanceId, $aLinkParams = [])
    {
        $portal = self::getPortalDomainServiceStatic()->getActivePortal();
        $moduleInstance = TdbCmsTplModuleInstance::GetNewInstance();
        $moduleInstance->Load($sModuleInstanceId);
        $masterPageList = $moduleInstance->GetFieldCmsTplPageCmsMasterPagedefSpotList();
        $returnURL = '';
        while ($masterPage = $masterPageList->Next()) {
            $page = TdbCmsTplPage::GetNewInstance();
            $page->Load($masterPage->fieldCmsTplPageId);
            if ($page->fieldCmsPortalId == $portal->id) {
                $returnURL = self::getPageService()->getLinkToPageObjectAbsolute($page, $aLinkParams);
                break;
            }
        }

        return $returnURL;
    }

    /**
     * return link to un-subscribe page.
     *
     * @param string[]|string $sPkgNewsletterGroupId
     *
     * @return string
     */
    public function GetLinkUnsubscribe($sPkgNewsletterGroupId)
    {
        $params = $this->GetUnsubscribeLinkParameter($sPkgNewsletterGroupId);
        if (is_array($sPkgNewsletterGroupId)) {
            $sPkgNewsletterGroupId = $sPkgNewsletterGroupId[0];
        }

        return $this->GenerateUnsubscribeLink($sPkgNewsletterGroupId, $params);
    }

    /**
     * Get link for page where newsletter sign out module was configured at.
     *
     * @param string $sPkgNewsletterGroupId
     * @param array $aURLParameter
     *
     * @return string
     */
    protected function GenerateUnsubscribeLink($sPkgNewsletterGroupId, $aURLParameter)
    {
        $portal = null;
        $group = TdbPkgNewsletterGroup::GetNewInstance();
        $group->Load($sPkgNewsletterGroupId);
        $portal = $group->GetFieldCmsPortal();

        return $this->getSystemPageService()->getLinkToSystemPageAbsolute('unsubscribe', $aURLParameter, $portal);
    }

    /**
     * Get link parameter for unsubscribe link.
     *
     * @param string $sPkgNewsletterGroupId
     * @param string|false $sUnsubscribeCode if given, add opt-out-key to link (use with new newsletter module).O
     *                                       Otherwise generate link for old newsletter module.
     *
     * @return array<string, mixed>
     */
    protected function GetUnsubscribeLinkParameter($sPkgNewsletterGroupId, $sUnsubscribeCode = false)
    {
        if (false === $sUnsubscribeCode) {
            $params = [
                MTPkgNewsletterSignoutCore::URL_PARAM_DATA => [
                    MTPkgNewsletterSignoutCore::URL_PARAM_NEWSLETTER_USER_ID => $this->id,
                    MTPkgNewsletterSignoutCore::URL_PARAM_GROUP_ID => $sPkgNewsletterGroupId,
                ],
            ];
        } else {
            $params = ['optoutcode' => $sUnsubscribeCode];
        }

        return $params;
    }

    /**
     * Get unsubscribe link with opt-out-code to unsubscribe user without double opt-out-email.
     *
     * @param string|array $sPkgNewsletterGroupId
     * @param bool $bUnsubscribeCodeForAll
     *
     * @return string
     */
    public function GetLinkUnsubscribeWithCode($sPkgNewsletterGroupId, $bUnsubscribeCodeForAll = false)
    {
        $unsubscribeCode = false;
        if (false === $bUnsubscribeCodeForAll) {
            $newsletterConfirmationList = $this->GetFieldPkgNewsletterConfirmationList();
            if (is_array($sPkgNewsletterGroupId)) {
                $unsubscribeCode = [];
                while ($oNewsletterConfirmation = $newsletterConfirmationList->Next()) {
                    if (in_array($oNewsletterConfirmation->fieldPkgNewsletterGroupId, $sPkgNewsletterGroupId)) {
                        $unsubscribeCode[] = $this->SetOptOutCodeToConfirmation($oNewsletterConfirmation);
                    }
                }
            } else {
                while ($oNewsletterConfirmation = $newsletterConfirmationList->Next()) {
                    if ($oNewsletterConfirmation->fieldPkgNewsletterGroupId == $sPkgNewsletterGroupId) {
                        $unsubscribeCode = $this->SetOptOutCodeToConfirmation($oNewsletterConfirmation);
                        break;
                    }
                }
            }
        }

        if (false === $unsubscribeCode) {
            $unsubscribeCode = $this->SetOptOutCodeToNewsletterUser();
        }
        if (is_array($sPkgNewsletterGroupId)) {
            $sPkgNewsletterGroupId = $sPkgNewsletterGroupId[0];
        }
        $params = $this->GetUnsubscribeLinkParameter($sPkgNewsletterGroupId, $unsubscribeCode);

        return $this->GenerateUnsubscribeLink($sPkgNewsletterGroupId, $params);
    }

    /**
     * return true if the email has already been registered.
     *
     * @return bool
     */
    public function EMailAlreadyRegistered()
    {
        $isRegistered = true;
        $query = 'SELECT *
                  FROM `pkg_newsletter_user`';
        $query .= "  WHERE `email` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->fieldEmail)."'";
        if (!is_null($this->id)) {
            $query .= "AND `id` != '".MySqlLegacySupport::getInstance()->real_escape_string($this->id)."'";
        }
        if (property_exists($this, 'fieldCmsPortalId') && !is_null($this->fieldCmsPortalId)) {
            $query .= " AND `cms_portal_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->fieldCmsPortalId)."'";
        }

        $result = MySqlLegacySupport::getInstance()->query($query);
        if (MySqlLegacySupport::getInstance()->num_rows($result) < 1) {
            $isRegistered = false;
        }

        return $isRegistered;
    }

    /**
     * return true if the email has already been registered by an extranet user.
     *
     * @return bool
     */
    public function EMailAlreadyRegisteredUser()
    {
        $isRegistered = true;
        $query = 'SELECT *
                  FROM `data_extranet_user`';
        $query .= "  WHERE `name` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->fieldEmail)."'";
        $result = MySqlLegacySupport::getInstance()->query($query);
        if (MySqlLegacySupport::getInstance()->num_rows($result) < 1) {
            $isRegistered = false;
        }

        return $isRegistered;
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
     * @param array $data
     *
     * @return void
     */
    public function LoadFromRowProtected($data)
    {
        $allowList = $this->getFieldWhitelistForLoadByRow();
        $realData = $this->sqlData;
        foreach ($data as $field => $val) {
            if (in_array($field, $allowList)) {
                $realData[$field] = $val;
            }
        }
        $user = TdbDataExtranetUser::GetInstance();
        $realData['data_extranet_user_id'] = $user->id;
        $this->LoadFromRow($realData);
    }

    /**
     * @return array
     */
    protected function getFieldWhitelistForLoadByRow()
    {
        $allowList = ['email', 'data_extranet_salutation_id', 'lastname', 'firstname', 'cms_portal_id', 'company'];

        return $allowList;
    }

    /**
     * @return void
     */
    public function PostInsertHook()
    {
        parent::PostInsertHook();
        $newsletterConfig = $this->GetFromInternalCache('tmpoNewsletterConfigForPostInsertHook');

        // set opt in code
        if (!array_key_exists('optin', $this->sqlData) || '1' != $this->sqlData['optin']) {
            $data = $this->sqlData;
            $data['signup_date'] = date('Y-m-d H:i:s');
            $data['optincode'] = md5(uniqid((string) rand(), true));
            if (is_null($newsletterConfig)) {
                $data['optin'] = '0';
                $this->LoadFromRow($data);
                $this->Save();
                $this->SendDoubleOptInEMail();
            } else {
                $user = TdbDataExtranetUser::GetInstance();
                if ($newsletterConfig->fieldUseDoubleoptin && (!$user->IsLoggedIn() || ($user->IsLoggedIn() && $user->fieldName != $data['email']))) {
                    $findUserByEmail = TdbDataExtranetUser::GetNewInstance();
                    $findUserByEmail->LoadFromField('name', $data['email']);

                    $data['optin'] = '0';
                    $this->LoadFromRow($data);
                    if (!empty($findUserByEmail->id)) {
                        $this->sqlData['data_extranet_user_id'] = $findUserByEmail->id;
                    } else {
                        $this->sqlData['data_extranet_user_id'] = '';
                    }
                    $this->Save();
                    $this->SendDoubleOptInEMail($newsletterConfig);
                } else {
                    $this->ConfirmSignupNew($data);
                }
            }
        }
    }

    /**
     * @param TdbPkgNewsletterModuleSignupConfig|null $oNewsletterConfig
     *
     * @return void
     */
    public function SendDoubleOptInEMail($oNewsletterConfig = null)
    {
        $mail = TdbDataMailProfile::GetProfile('newsletter-double-opt-in');
        $data = $this->sqlData;
        $salutation = $this->GetFieldDataExtranetSalutation();
        if (is_null($salutation)) {
            $dataExtranetSalutationName = '';
        } else {
            $dataExtranetSalutationName = $salutation->GetName();
        }
        $data['data_extranet_salutation_name'] = $dataExtranetSalutationName;
        $data['link'] = $this->GetLinkConfirmRegistration($oNewsletterConfig);

        $newsletterGroupList = $this->GetFieldPkgNewsletterGroupList();
        $newsletterGroupList->GoToStart();
        $newsletterGroup = $newsletterGroupList->Current();
        if ($newsletterGroup) {
            $data['unsubscribe_link'] = $this->GetLinkUnsubscribe($newsletterGroup->id);
        }

        if (is_null($salutation)) {
            $data['sFullName'] = implode(' ', [$this->fieldFirstname, $this->fieldLastname]);
        } else {
            $data['sFullName'] = implode(' ', [$salutation->GetName(), $this->fieldFirstname, $this->fieldLastname]);
        }
        $mail->AddDataArray($data);
        if (empty($data['sFullName'])) {
            $data['sFullName'] = $this->fieldEmail;
        }
        $mail->ChangeToAddress($this->fieldEmail, $data['sFullName']);
        $mail->SendUsingObjectView('emails', 'Customer');
    }

    // ------ Functions for new newslettermodule--------------------------------

    /**
     * after updating a confirmation save new opt-in code to newsletter user.
     *
     * @param TdbPkgNewsletterModuleSignupConfig $oNewsletterConfig
     *
     * @return void
     */
    public function PostConfirmationUpdateHook($oNewsletterConfig)
    {
        $user = TdbDataExtranetUser::GetInstance();
        $this->sqlData['optincode'] = md5(uniqid((string) rand(), true));
        $this->AllowEditByAll(true);
        $this->Save();
        $this->AllowEditByAll(true);
        if (!is_null($oNewsletterConfig) && $oNewsletterConfig->fieldUseDoubleoptin && (!$user->IsLoggedIn() || ($user->IsLoggedIn() && $user->fieldName != $this->sqlData['email']))) {
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
        $returnValue = parent::Save();
        unset($this->aResultCache['tmpoNewsletterConfigForPostInsertHook']);

        return $returnValue;
    }

    /**
     * save one confirmation for each chosen newsletter group and updates mlt connections from newsletter user to confirmation.
     *
     * @param array $aNewsletterList list with newslettergroupids
     * @param TdbPkgNewsletterModuleSignupConfig $oNewsletterConfig
     *
     * @return bool $bDoConfirmationUpdate true = add or update a confirmation , false = no add/update
     */
    public function SaveNewsletterConfirmations($aNewsletterList, $oNewsletterConfig = null)
    {
        $updateNewsletterGroupIdList = [];
        $newsletterConfirmationList = $this->GetFieldPkgNewsletterConfirmationList();
        // Get the length here because length uses lazy loading and CreateConfirmation can change the length.
        $confirmationListCount = $newsletterConfirmationList->Length();
        $doConfirmationUpdate = false;
        foreach ($aNewsletterList as $newsletterGroupId) {
            if ('all' != $newsletterGroupId) {
                $existingConfirmation = null;
                while ($oNewsletterConfirmation = $newsletterConfirmationList->Next()) {
                    if ($newsletterGroupId == $oNewsletterConfirmation->fieldPkgNewsletterGroupId) {
                        $existingConfirmation = $oNewsletterConfirmation;
                    }
                }
                $newsletterConfirmationList->GoToStart();
                if (is_null($existingConfirmation)) {
                    $oNewsletterConfirmation = $this->CreateConfirmation($newsletterGroupId);
                    if (!is_null($oNewsletterConfirmation->id)) {
                        $doConfirmationUpdate = true;
                        $updateNewsletterGroupIdList[] = $oNewsletterConfirmation->id;
                    }
                } else {
                    if (!$existingConfirmation->fieldConfirmation) {
                        $this->UpdateConfirmation($existingConfirmation);
                        $doConfirmationUpdate = true;
                    }
                    $updateNewsletterGroupIdList[] = $existingConfirmation->id;
                }
            }
        }
        $this->AddNewsletterConfirmationConnection($updateNewsletterGroupIdList, true);
        if ($confirmationListCount > 0 && $doConfirmationUpdate) {
            $this->PostConfirmationUpdateHook($oNewsletterConfig);
        }

        return $doConfirmationUpdate;
    }

    /**
     * Was called after sign up newsletter user only.
     * This will be used if no newsletter groups are existing.
     *
     * @return void
     */
    public function PostSignUpNewsletterUserOnly()
    {
    }

    /**
     * update confirmation with new registration date.
     *
     * @param TdbPkgNewsletterConfirmation $oNewsletterConfirmation
     *
     * @return void
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
     * @param bool $bCreateConfirmed
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
     *
     * @return void
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
        $wasSignedUp = false;
        $this->aResultCache = [];
        $confirmationList = $this->GetFieldPkgNewsletterConfirmationList();
        if (!is_null($confirmationList) && $confirmationList->Length() > 0) {
            $newsletterGroupIdList = [];
            while ($confirmation = $confirmationList->Next()) {
                if ('1' != $confirmation->sqlData['confirmation']) {
                    $confirmation->sqlData['confirmation_date'] = date('Y-m-d H:i:s');
                    $confirmation->sqlData['confirmation'] = '1';
                    $confirmation->AllowEditByAll(true);
                    $confirmation->Save();
                    $confirmation->AllowEditByAll(false);
                    $wasSignedUp = true;
                    $this->PostConfirmNewsletterUserHook($confirmation->fieldPkgNewsletterGroupId);
                }
                $newsletterGroupIdList[] = $confirmation->fieldPkgNewsletterGroupId;
            }
            $this->AddNewsletterGroupConnection($newsletterGroupIdList, true);
        }
        if (!$this->fieldOptin) {
            $data = $this->sqlData;
            $data['optin'] = '1';
            $data['optin_date'] = date('Y-m-d H:i:s');
            if (!is_null($aNewData)) {
                $data = array_merge($aNewData, $data);
            }
            $this->LoadFromRow($data);
            $this->AllowEditByAll(true);
            $this->Save();
            $wasSignedUp = true;
            $this->PostConfirmNewsletterUserHook();
        }

        return $wasSignedUp;
    }

    /**
     * Was called after confirming newsletter confirmation.
     *
     * @param bool|string $sNewsletterGroupId
     *
     * @return void
     */
    protected function PostConfirmNewsletterUserHook($sNewsletterGroupId = false)
    {
    }

    /**
     * add given newsletter groups to newsletter user.
     *
     * @param bool $bNoDelete
     * @param string[] $aNewsletterGroupIdList
     *
     * @return void
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
     * @param bool $bNoDelete
     * @param string[] $aNewNewsletterConfirmationIdList
     *
     * @return void
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
        return $this->GetFieldPkgNewsletterGroupList();
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
        $newNewsletterGroupIdList = [];
        $signedInNewsletterGroupIdList = $this->GetMLTIdList('pkg_newsletter_group', 'pkg_newsletter_group_mlt');
        foreach ($signedInNewsletterGroupIdList as $sNewsletterGroupId) {
            if (!in_array($sNewsletterGroupId, $aNewsletterGroupSignOutList)) {
                $newNewsletterGroupIdList[] = $sNewsletterGroupId;
            }
        }
        if (count($newNewsletterGroupIdList) > 0) {
            $this->AddNewsletterGroupConnection($newNewsletterGroupIdList);

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
        $newNewsletterConfirmationIdList = [];
        $signedInConfirmationList = $this->GetFieldPkgNewsletterConfirmationList();
        while ($signedInConfirmation = $signedInConfirmationList->Next()) {
            if (!in_array($signedInConfirmation->fieldPkgNewsletterGroupId, $aNewsletterGroupSignOutList)) {
                $newNewsletterConfirmationIdList[] = $signedInConfirmation->id;
            } else {
                $signedInConfirmation->AllowEditByAll(true);
                $this->PostUnSubscribeNewsletterUser($signedInConfirmation->fieldPkgNewsletterGroupId);
                $signedInConfirmation->Delete();
            }
        }
        if (count($newNewsletterConfirmationIdList) > 0) {
            $this->AddNewsletterConfirmationConnection($newNewsletterConfirmationIdList);

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
        $mail = TdbDataMailProfile::GetProfile('newsletter-double-opt-out');
        $data = $this->sqlData;
        $salutation = $this->GetFieldDataExtranetSalutation();
        if (is_null($salutation)) {
            $dataExtranetSalutationName = '';
        } else {
            $dataExtranetSalutationName = $salutation->GetName();
        }
        $data['data_extranet_salutation_name'] = $dataExtranetSalutationName;
        $link = $this->GetOptOutConfirmationLink();
        $data['linkblock'] = $this->GenerateLinkBlock($link);
        $data['link'] = $this->GenerateLinkBlock($link, false);
        if (is_null($salutation)) {
            $data['sFullName'] = implode(' ', [$this->fieldFirstname, $this->fieldLastname]);
        } else {
            $data['sFullName'] = implode(' ', [$salutation->GetName(), $this->fieldFirstname, $this->fieldLastname]);
        }
        $mail->AddDataArray($data);
        if (empty($data['sFullName'])) {
            $data['sFullName'] = $this->fieldEmail;
        }
        $mail->ChangeToAddress($this->fieldEmail, $data['sFullName']);

        return $mail->SendUsingObjectView('emails', 'Customer');
    }

    /**
     * generate link-block for email to confirm a sign-out.
     *
     * @param array $aLinks (Key is used as linkname and value is the real link)
     * @param bool $bHtml generate html code or text code
     *
     * @return string $sLinkBlock return all links as string
     */
    protected function GenerateLinkBlock($aLinks, $bHtml = true)
    {
        $linkBlock = '';
        foreach ($aLinks as $newsletterName => $link) {
            if ($bHtml) {
                $linkBlock .= '<div><a href="'.$link.'" title="'.$newsletterName.'" >'.$newsletterName.'</a><div><br />';
            } else {
                $linkBlock .= $newsletterName.': '.$link.'\r\n';
            }
        }

        return $linkBlock;
    }

    /**
     * Set new OptOut codes to all none confirmed confirmations and return array of all opt-out links.
     *
     * @return array array with all newsletter signout links
     */
    protected function GetOptOutConfirmationLink()
    {
        $confirmationList = $this->GetFieldPkgNewsletterConfirmationList();
        $link = [];
        $aParameter = ['optoutcode' => $this->SetOptOutCodeToNewsletterUser()];
        if ($confirmationList->Length() > 1) {
            $link[ServiceLocator::get('translator')->trans('chameleon_system_newsletter.action.unsubscribe_all')] = $this->getActivePageService()->getLinkToActivePageAbsolute($aParameter).'  ';
        } elseif ($confirmationList->Length() < 1) {
            $link[ServiceLocator::get('translator')->trans('chameleon_system_newsletter.action.unsubscribe')] = $this->getActivePageService()->getLinkToActivePageAbsolute($aParameter).'  ';
        }
        while ($confirmation = $confirmationList->Next()) {
            $newsletterGroup = TdbPkgNewsletterGroup::GetNewInstance();
            $newsletterGroup->Load($confirmation->fieldPkgNewsletterGroupId);
            $link[$newsletterGroup->fieldName] = $this->GetOptOutConfirmationLinkForConfirmation($confirmation).' ';
        }

        return $link;
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
        return $this->getActivePageService()->getLinkToActivePageAbsolute([
            'optoutcode' => $this->SetOptOutCodeToConfirmation($oConfirmation),
        ]);
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
        $optOutCode = $oConfirmation->fieldOptoutKey;
        if (0 == strlen($optOutCode)) {
            $oConfirmation->AllowEditByAll(true);
            $sOptOutCode = md5(uniqid((string) rand(), true));
            $oConfirmation->sqlData['optout_key'] = $sOptOutCode;
            $oConfirmation->Save();
            $oConfirmation->AllowEditByAll(false);
        }

        return $optOutCode;
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
        $optOutCode = $this->fieldOptoutcode;
        if (0 == strlen($optOutCode)) {
            $this->AllowEditByAll(true);
            $optOutCode = md5(uniqid((string) rand(), true));
            $this->sqlData['optoutcode'] = $optOutCode;
            $this->Save();
            $this->AllowEditByAll(false);
        }

        return $optOutCode;
    }

    /**
     * Sign out Newsletter.
     *
     * @param array $aNewsletterGroupSignOutList if is only a string then the signout came from old singout module so confirmed is set to true
     * @param bool $bConfirmSignOut sign out was confirmed -> delete all confirmations with newslettergroups in $aNewsletterGroupSignOutList
     *
     * @return bool
     */
    public function SignOut($aNewsletterGroupSignOutList = [], $bConfirmSignOut = false)
    {
        $signedOut = true;
        $user = TdbDataExtranetUser::GetInstance();
        if (!is_array($aNewsletterGroupSignOutList)) {
            $aNewsletterGroupSignOutList = [$aNewsletterGroupSignOutList];
        }
        if (!$user->IsLoggedIn() && !$bConfirmSignOut) {
            $signedOut = $this->SendDoubleOptOutMail();
        } else {
            $noDeleteNewsletterUser = $this->RemoveNewsletterGroupConnection($aNewsletterGroupSignOutList);
            if (TCMSRecord::TableExists('pkg_newsletter_confirmation')) {
                $noDeleteNewsletterUser = $this->RemoveConfirmationConnection($aNewsletterGroupSignOutList) || $noDeleteNewsletterUser;
            }
            $this->SendSignOutConfirmation($aNewsletterGroupSignOutList);
            if (!$noDeleteNewsletterUser) {
                if (0 == count($aNewsletterGroupSignOutList)) {
                    $this->PostUnSubscribeNewsletterUser();
                }
                $this->AllowEditByAll(true);
                $this->Delete();
            }
        }

        return $signedOut;
    }

    /**
     * Was called after unsubscribing newsletter user.
     *
     * @param bool|string $sNewsletterGroupId
     *
     * @return void
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
    public function SendSignOutConfirmation($aNewsletterGroupSignOutList = [])
    {
        $mail = TdbDataMailProfile::GetProfile('newsletter-sign-out-confirmation');
        $data = $this->sqlData;

        $oSal = $this->GetFieldDataExtranetSalutation();
        if (is_null($oSal)) {
            $dataExtranetSalutationName = '';
        } else {
            $dataExtranetSalutationName = $oSal->GetName();
        }
        $data['data_extranet_salutation_name'] = $dataExtranetSalutationName;

        $data['signoutinfo'] = '';
        $data['polite-signoutinfo'] = '';
        if (is_array($aNewsletterGroupSignOutList) && count($aNewsletterGroupSignOutList) > 0) {
            $data['signoutinfo'] .= ServiceLocator::get('translator')->trans('chameleon_system_newsletter.text.group_unsubscribe_info').' <br />';
            $data['polite-signoutinfo'] .= ServiceLocator::get('translator')->trans('chameleon_system_newsletter.text.group_unsubscribe_info_formal').' <br />';
            $oNewsletterGroup = TdbPkgNewsletterGroup::GetNewInstance();
            foreach ($aNewsletterGroupSignOutList as $sNewsletterGroupID) {
                $oNewsletterGroup->Load($sNewsletterGroupID);
                $data['signoutinfo'] .= $oNewsletterGroup->GetName().'<br />';
                $data['polite-signoutinfo'] .= $oNewsletterGroup->GetName().'<br />';
            }
            // $aData['signoutinfo'] .= implode('<br />', $aNewsletterGroupSignOutList);
        }
        $groupList = $this->GetSignedInNewsletterList();
        $groupList->GoToStart();
        $hasGroups = false;
        if ($groupList->Length() != count($aNewsletterGroupSignOutList)) {
            while ($oGroup = $groupList->Next()) {
                $data['signoutinfo'] .= '<br />'.ServiceLocator::get('translator')->trans('chameleon_system_newsletter.text.list_of_subscribed_groups').' <br/>';
                $data['polite-signoutinfo'] .= '<br />'.ServiceLocator::get('translator')->trans('chameleon_system_newsletter.text.list_of_subscribed_groups_polite').' <br/>';
                $data['signoutinfo'] .= $oGroup->GetName().'<br />';
                $data['polite-signoutinfo'] .= $oGroup->GetName().'<br />';
                $hasGroups = true;
            }
        }
        if (!$hasGroups) {
            $data['signoutinfo'] .= ServiceLocator::get('translator')->trans('chameleon_system_newsletter.text.unsubscribed');
            $data['polite-signoutinfo'] .= ServiceLocator::get('translator')->trans('chameleon_system_newsletter.text.unsubscribed_polite');
        }
        $mail->AddDataArray($data);
        $mail->ChangeToAddress($this->fieldEmail, $this->fieldEmail);
        $mail->SendUsingObjectView('emails', 'Customer');
    }

    /**
     * return TdbPkgNewsletterUser if the email has already been registered otherwise null.
     *
     * @psalm-suppress TypeDoesNotContainNull
     *
     * @FIXME This method currently ALWAYS returns `TdbPkgNewsletterUser`, irrespective of the registration state as `TdbPkgNewsletterUser::GetNewInstance` will also always return an instance.
     *
     * @return TdbPkgNewsletterUser|null
     */
    public function EMailAlreadyRegisteredNew()
    {
        $oFoundNewsletterUser = TdbPkgNewsletterUser::GetNewInstance();
        $oPortal = self::getPortalDomainServiceStatic()->getActivePortal();
        if (!is_null($oPortal)) {
            $oFoundNewsletterUser->LoadFromFields(['email' => $this->fieldEmail, 'cms_portal_id' => $oPortal->id]);
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
     * @param string $sPkgNewsletterGroupId
     *
     * @return bool
     */
    public function isInGroup($sPkgNewsletterGroupId)
    {
        $groupList = $this->GetFromInternalCache('aNewsletterGroupIdList');
        if (null === $groupList) {
            $groupList = $this->GetFieldPkgNewsletterGroupIdList();
            $this->SetInternalCache('aNewsletterGroupIdList', $groupList);
        }

        return is_array($groupList) && in_array($sPkgNewsletterGroupId, $groupList);
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
