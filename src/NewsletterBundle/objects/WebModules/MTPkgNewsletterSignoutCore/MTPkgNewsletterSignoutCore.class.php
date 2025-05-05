<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;

class MTPkgNewsletterSignoutCore extends TUserCustomModelBase
{
    public const URL_PARAM_DATA = 'aPkgNewsletter';
    public const URL_PARAM_MAIL = 'mail';
    public const URL_PARAM_NEWSLETTER_USER_ID = TdbPkgNewsletterUser::URL_USER_ID_PARAMETER;
    public const URL_PARAM_GROUP_ID = 'groupid';
    public const URL_PARAM_UNSUBSCRIBE_KEY = 'unsbkey';
    public const MSG_MANAGER_NAME = 'aPkgNewsletter';

    /**
     * @var string
     */
    protected $sStep = '';

    /**
     * @var TdbPkgNewsletterUser|null
     */
    protected $oNewsletterSignup;
    /**
     * @var TdbPkgNewsletterModuleSignupConfig|null
     */
    protected $oModuleConfig;

    /**
     * @var true
     */
    protected $bAllowHTMLDivWrapping = true;

    public const INPUT_DATA_NAME = 'aPkgNewsletterOut';

    public function Init()
    {
        parent::Init();
        $this->oModuleConfig = $this->GetConfig();
        $oUser = TdbDataExtranetUser::GetInstance();
        if ($this->global->UserDataExists('optoutcode')) {
            $this->sStep = 'ConfirmSignout';
        } else {
            $aUserData = $this->global->GetUserData(self::URL_PARAM_DATA);
            $requestHasNewsletterUserId = is_array($aUserData) && array_key_exists(self::URL_PARAM_NEWSLETTER_USER_ID, $aUserData);
            $requestHasNewsletterUserEmail = !empty($aUserData) && is_array($aUserData) && count($aUserData) > 0 && array_key_exists(self::URL_PARAM_MAIL, $aUserData);
            if (true === $requestHasNewsletterUserId || true === $requestHasNewsletterUserEmail) {
                if (!defined('CHAMELEON_PKG_NEWSLETTER_NEW_MODULE') || CHAMELEON_PKG_NEWSLETTER_NEW_MODULE === false) {
                    $this->UnsubscribeUser();
                    $this->sStep = 'SignedOut';
                } else {
                    $oUserNewsletter = null;
                    if (true === $requestHasNewsletterUserId) {
                        $oUserNewsletter = TdbPkgNewsletterUser::GetNewInstance();
                        if (false === $oUserNewsletter->Load($aUserData[self::URL_PARAM_NEWSLETTER_USER_ID])) {
                            $oUserNewsletter = null;
                        }
                    } elseif (true === $requestHasNewsletterUserEmail) {
                        $oUserNewsletter = TdbPkgNewsletterUser::GetInstanceForMail($aUserData[self::URL_PARAM_MAIL]);
                    }
                    if (!is_null($oUserNewsletter)) {
                        $oUserNewsletter->SignOut();
                    }
                    $oMsgManager = TCMSMessageManager::GetInstance();
                    $oMsgManager->AddMessage(self::INPUT_DATA_NAME, 'NEWSLETTER_ERROR_INCOMING_OLD_UNSUBSCRIBE');
                    $this->sStep = 'SignOut';
                }
            } elseif ($oUser->IsLoggedIn()) {
                $oUserNewsletter = TdbPkgNewsletterUser::GetInstanceForActiveUser();
                if (is_null($oUserNewsletter)) {
                    $this->sStep = 'NoSignedIn';
                } else {
                    $this->sStep = 'SignOut';
                }
            } else {
                $this->sStep = 'SignOut';
            }
        }
    }

    /**
     * remove the user from the newsletter group.
     *
     * @param string $sPkgNewsletterUserMail
     * @param string|null $sPkgNewsletterGroupId
     *
     * @return void
     */
    protected function UnsubscribeUser($sPkgNewsletterUserMail = null, $sPkgNewsletterGroupId = null)
    {
        $bError = false;
        $aUserData = $this->global->GetUserData(self::URL_PARAM_DATA);
        if (false === is_array($aUserData)) {
            $aUserData = [];
        }
        $newsletterUserId = null;
        if (true === array_key_exists(self::URL_PARAM_NEWSLETTER_USER_ID, $aUserData)) {
            $newsletterUserId = $aUserData[self::URL_PARAM_NEWSLETTER_USER_ID];
        }
        if (array_key_exists(self::URL_PARAM_MAIL, $aUserData)) {
            $sPkgNewsletterUserMail = $aUserData[self::URL_PARAM_MAIL];
        }
        if (array_key_exists(self::URL_PARAM_GROUP_ID, $aUserData)) {
            $sPkgNewsletterGroupId = $aUserData[self::URL_PARAM_GROUP_ID];
        }
        $sUnsubscribeCode = false;
        if (array_key_exists(self::URL_PARAM_UNSUBSCRIBE_KEY, $aUserData)) {
            $sUnsubscribeCode = $aUserData[self::URL_PARAM_UNSUBSCRIBE_KEY];
        }
        $oMsgManager = TCMSMessageManager::GetInstance();
        $oNewsUser = TdbPkgNewsletterUser::GetNewInstance();
        if (null === $newsletterUserId || false === $oNewsUser->Load($newsletterUserId)) { // Fallback to email.
            $oNewsUser = TdbPkgNewsletterUser::GetNewInstance();
            if ('' === trim($sPkgNewsletterUserMail) || !$oNewsUser->LoadFromField('email', $sPkgNewsletterUserMail)) {
                $oMsgManager->AddMessage(self::INPUT_DATA_NAME.'-confirmoptout', 'ERROR-UNSUBSCRIBE-NEWSLETTER-USER-NOT-FOUND');
                $bError = true;
            }
        }

        if (!$bError) {
            $bConfirme = false;
            /*
             * @psalm-suppress UndefinedPropertyFetch
             * @FIXME Does `fieldUseDoubleOptOut` exist?
             */
            if (!$this->oModuleConfig->fieldUseDoubleOptOut || $this->IsUnsubscribeCodeValid($sUnsubscribeCode, $oNewsUser->id, $sPkgNewsletterGroupId)) {
                $bConfirme = true;
            }

            if (!$oNewsUser->SignOut($sPkgNewsletterGroupId, $bConfirme)) {
                $oMsgManager->AddMessage(self::INPUT_DATA_NAME.'-confirmoptout', 'NEWSLETTER_ERROR_NOT_SUBSCRIBED');
                $bError = true;
            }
        }
        $oMsgManager->AddMessage(self::INPUT_DATA_NAME.'-confirmoptout', 'NEWSLETTER_UNSUBSCRIBED', ['mail' => $sPkgNewsletterUserMail]);
    }

    /**
     * @param string $sUnsubscribeCode
     * @param string $sNewsletterUserId
     * @param string $sNewsletterGroupId
     *
     * @return bool
     */
    protected function IsUnsubscribeCodeValid($sUnsubscribeCode, $sNewsletterUserId, $sNewsletterGroupId)
    {
        $bValid = false;
        if (false !== $sUnsubscribeCode && strlen($sUnsubscribeCode)) {
            $sSelect = "SELECT * FROM `pkg_newsletter_unsubscribe_code`
                                WHERE `pkg_newsletter_user_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sNewsletterUserId)."'
                                AND `pkg_newsletter_group_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sNewsletterGroupId)."'
                                AND `name` = '".$sUnsubscribeCode."'
                                AND (`expire_date` > ".date('Y-m-d').' OR `expire_date` = 0000-00-00)';
            $oUnsubscribeCodeList = TdbPkgNewsletterUnsubscribeCodeList::GetList($sSelect);
            if ($oUnsubscribeCodeList->Length() > 0) {
                $bValid = true;
            }
        }

        return $bValid;
    }

    public function Execute()
    {
        parent::Execute();
        $oUser = TdbDataExtranetUser::GetInstance();
        $this->data['oNewsletterConfig'] = $this->GetConfig();
        $this->data['oNewsletterSignup'] = $this->LoadNewsletterSignup(true);
        $this->data['oSignedInNewsletterList'] = $this->data['oNewsletterSignup']->GetSignedInNewsletterList();
        if ('ConfirmSignout' == $this->sStep) {
            if ($this->ConfirmSignout()) {
                $this->SetTemplate('MTPkgNewsletterSignout', 'system/signedout');
            } else {
                $bShowStandard = true;
            }
        } elseif ('SignOut' == $this->sStep) {
            if (!is_null($this->data['oNewsletterSignup']) && !empty($this->data['oNewsletterSignup']->id)) {
                if ($oUser->IsLoggedIn()) {
                    if (!array_key_exists('bSignedOut', $this->data) || empty($this->data['bSignedOut'])) {
                        $bShowStandard = true;
                    } else {
                        $this->SetTemplate('MTPkgNewsletterSignout', 'system/signedout');
                    }
                } else {
                    if (!array_key_exists('bSignedOut', $this->data) || empty($this->data['bSignedOut'])) {
                        $bShowStandard = true;
                    } else {
                        $this->SetTemplate('MTPkgNewsletterSignout', 'system/signoutconfirm');
                    }
                }
            } else {
                if ($oUser->IsLoggedIn()) {
                    if (array_key_exists('bSignedOut', $this->data) && !empty($this->data['bSignedOut'])) {
                        $this->SetTemplate('MTPkgNewsletterSignout', 'system/signedout');
                    } else {
                        $this->SetTemplate('MTPkgNewsletterSignout', 'system/nosignedup');
                    }
                }
            }
        } elseif ('SignedOut' == $this->sStep) {
            $this->SetTemplate('MTPkgNewsletterSignout', 'system/signedout');
        } elseif ('NoSignedIn' == $this->sStep) {
            $this->SetTemplate('MTPkgNewsletterSignout', 'system/nosignedup');
        } else {
        }

        return $this->data;
    }

    /**
     * Get the module config (when module is static get module config from main module).
     *
     * @return TdbPkgNewsletterModuleSignupConfig $oNewsletterConfig
     */
    protected function GetConfig()
    {
        static $oConfig = null;
        if (is_null($oConfig)) {
            if (is_null($this->instanceID)) {
                if (array_key_exists('main_instance_id', $this->aModuleConfig)) {
                    $sInstanceId = $this->aModuleConfig['main_instance_id'];
                } else {
                    $sInstanceId = null;
                }
            } else {
                $sInstanceId = $this->instanceID;
            }
            $oNewsletterConfig = TdbPkgNewsletterModuleSignoutConfig::GetNewInstance();
            $oNewsletterConfig->LoadFromField('cms_tpl_module_instance_id', $sInstanceId);
        }

        return $oNewsletterConfig;
    }

    /**
     * add your custom methods as array to $this->methodCallAllowed here
     * to allow them to be called from web.
     */
    protected function DefineInterface()
    {
        parent::DefineInterface();
        $this->methodCallAllowed[] = 'SignOut';
    }

    /**
     * lazzy load newsletter object.
     *
     * @param bool $bRefresh
     *
     * @return TdbPkgNewsletterUser
     */
    protected function LoadNewsletterSignup($bRefresh = false)
    {
        if (is_null($this->oNewsletterSignup)) {
            $this->oNewsletterSignup = TdbPkgNewsletterUser::GetNewInstance();
            $oNewsletter = TdbPkgNewsletterUser::GetInstanceForActiveUser($bRefresh);
            if (!is_null($oNewsletter)) {
                $this->oNewsletterSignup = $oNewsletter;
            } else {
                $oGlobal = TGlobal::instance();
                if ($oGlobal->UserDataExists(self::INPUT_DATA_NAME)) {
                    $oUserdata = $oGlobal->GetUserData(self::INPUT_DATA_NAME);
                    if (array_key_exists('signoutmail', $oUserdata) && !empty($oUserdata['signoutmail'])) {
                        $oNewsletter = TdbPkgNewsletterUser::GetInstanceForMail($oUserdata['signoutmail']);
                        if (!is_null($oNewsletter)) {
                            $this->oNewsletterSignup = $oNewsletter;
                        }
                    }
                }
            }
        }

        return $this->oNewsletterSignup;
    }

    /**
     * remove theuser from the newsletter group.
     *
     * @return void
     */
    protected function SignOut()
    {
        $oMsgManager = TCMSMessageManager::GetInstance();
        $aSignOutData = $this->global->GetUserData(self::INPUT_DATA_NAME);
        if (!is_array($aSignOutData)) {
            $aSignOutData = [];
        }
        if (array_key_exists('signoutmail', $aSignOutData)) {
            if (!empty($aSignOutData['signoutmail'])) {
                $oNewsletterUser = $this->LoadNewsletterSignup();
                if (is_null($oNewsletterUser->id)) {
                    $oMsgManager->AddMessage(self::INPUT_DATA_NAME.'-newsletteremail', 'ERROR-NEWSLETTER-NOT-FOUND-EMAIL');
                    $this->data['bSignedOut'] = false;
                } else {
                    if ($oNewsletterUser->SignOut()) {
                        $this->data['bSignedOut'] = true;
                    } else {
                        $this->data['bSignedOut'] = false;
                    }
                }
            } else {
                $oMsgManager->AddMessage(self::INPUT_DATA_NAME.'-newsletteremail', 'ERROR-NEWSLETTER-EMPTY-EMAIL');
                $this->data['bSignedOut'] = false;
            }
        } else {
            $oNewsletterUser = $this->LoadNewsletterSignup();
            /** @var TdbPkgNewsletterUser $oNewsletterUser */
            $oNewsLetterSignedInList = $oNewsletterUser->GetFieldPkgNewsletterGroupList();
            if (array_key_exists('newsletter', $aSignOutData) && count($aSignOutData['newsletter']) > 0 || $oNewsLetterSignedInList->Length() < 1) {
                if (!array_key_exists('newsletter', $aSignOutData) || count($aSignOutData['newsletter']) < 1) {
                    $aSignOutData['newsletter'] = [];
                }
                $oNewsletterUser->SignOut($this->TransformInputNewsletterList($aSignOutData['newsletter']));
                $this->data['bSignedOut'] = true;
            } else {
                $oMsgManager->AddMessage(self::INPUT_DATA_NAME.'-newsletterout', 'ERROR-NEWSLETTER-CHOOSE-NEWSLETTER');
                $this->data['bSignedOut'] = false;
            }
        }
    }

    /**
     * transform the newsletter array from key => value to $value => key.
     *
     * @param array $aNewsletterList
     *
     * @return array $aNewsletterList
     */
    protected function TransformInputNewsletterList($aNewsletterList)
    {
        $aNewNewsletterList = [];
        if (array_key_exists('all', $aNewsletterList)) {
            $oActivePortal = $this->getPortalDomainService()->getActivePortal();
            if (null === $oActivePortal) {
                $oNewsletterGroupList = TdbPkgNewsletterGroupList::GetList();
            } else {
                $oNewsletterGroupList = TdbPkgNewsletterGroupList::GetListForCmsPortalId($oActivePortal->id);
            }
            $aNewsletterList = [];
            while ($oNewsletterGroup = $oNewsletterGroupList->Next()) {
                $aNewsletterList[$oNewsletterGroup->id] = '1';
            }
        }
        foreach ($aNewsletterList as $key => $value) {
            $aNewNewsletterList[] = $key;
        }

        return $aNewNewsletterList;
    }

    /**
     * confirm a signout if url parameter optout exitsts and is correct.
     *
     * @return bool $bConfirmed
     */
    protected function ConfirmSignout()
    {
        $bConfirmed = false;
        if ($this->global->UserDataExists('optoutcode')) {
            $oNewsletterUser = TdbPkgNewsletterUser::GetNewInstance();
            $optOutKey = $this->global->GetUserData('optoutcode');
            if (is_array($optOutKey)) {
                foreach ($optOutKey as $sOptOutKey) {
                    if ($oNewsletterUser->LoadFromField('optoutcode', $sOptOutKey)) {
                        $this->ConfirmSignOutForUser($oNewsletterUser);
                        $bConfirmed = true;
                    } else {
                        $bConfirmed = $this->ConfirmSignOutForConfirmation($sOptOutKey);
                    }
                }
            } else {
                if ($oNewsletterUser->LoadFromField('optoutcode', $optOutKey)) {
                    $this->ConfirmSignOutForUser($oNewsletterUser);
                    $bConfirmed = true;
                } else {
                    $bConfirmed = $this->ConfirmSignOutForConfirmation($optOutKey);
                }
            }
        }

        return $bConfirmed;
    }

    /**
     * Confirm sign out for given newsletter user.
     *
     * @param TdbPkgNewsletterUser $oNewsletterUser
     *
     * @return void
     */
    protected function ConfirmSignOutForUser($oNewsletterUser)
    {
        $oSignedNewsletterList = $oNewsletterUser->GetSignedInNewsletterList();
        $aNewsletterList = [];
        while ($oSignedNewsletter = $oSignedNewsletterList->Next()) {
            $aNewsletterList[] = $oSignedNewsletter->id;
        }
        $oNewsletterUser->SignOut($aNewsletterList, true);
    }

    /**
     * Confirm sign out for given newsletter group confirmation opt-out key.
     *
     * @param string $sOptOutConfirmationKey
     *
     * @return bool
     */
    protected function ConfirmSignOutForConfirmation($sOptOutConfirmationKey)
    {
        $oConfirmation = TdbPkgNewsletterConfirmation::GetNewInstance();
        if ($oConfirmation->LoadFromField('optout_key', $sOptOutConfirmationKey)) {
            /** @var TCMSRecordList<TdbPkgNewsletterUser> $oSignupList */
            $oSignupList = $oConfirmation->GetMLTSourceRecords('pkg_newsletter_user', 'pkg_newsletter_confirmation_mlt', '', 'TdbPkgNewsletterUser', 'CMSDataObjects', 'Core');

            /** @var TdbPkgNewsletterUser $oNewsletterUser */
            $oNewsletterUser = $oSignupList->Current();

            if ($oNewsletterUser) {
                $oNewsletterUser->SignOut([$oConfirmation->fieldPkgNewsletterGroupId], true);
                $bConfirmed = true;
            } else {
                $oMsgManager = TCMSMessageManager::GetInstance();
                $oMsgManager->AddMessage(self::INPUT_DATA_NAME.'-confirmoptout', 'ERROR-NEWSLETTER-SIGNOUT-DOUBLE-OPT-OUT-CODE-INVALID');
                $bConfirmed = false;
            }
        } else {
            $oMsgManager = TCMSMessageManager::GetInstance();
            $oMsgManager->AddMessage(self::INPUT_DATA_NAME.'-confirmoptout', 'ERROR-NEWSLETTER-SIGNOUT-DOUBLE-OPT-OUT-CODE-INVALID');
            $bConfirmed = false;
        }

        return $bConfirmed;
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        $aIncludes = array_merge($aIncludes, $this->getResourcesForSnippetPackage('pkgNewsletter'));
        $aIncludes = array_merge($aIncludes, $this->getResourcesForSnippetPackage('pkgNewsletter/signOut'));
        $aIncludes = array_merge($aIncludes, $this->getResourcesForSnippetPackage('pkgNewsletter/signUp'));

        return $aIncludes;
    }

    public function _AllowCache()
    {
        return false;
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private function getPortalDomainService()
    {
        return ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }

    private function getInputFilterUtil(): InputFilterUtilInterface
    {
        return ServiceLocator::get('chameleon_system_core.util.input_filter');
    }
}
