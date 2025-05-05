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
use ChameleonSystem\CoreBundle\Service\PageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use esono\pkgCmsCache\CacheInterface;

class MTPkgNewsletterSignupCoreEndPoint extends TUserCustomModelBase
{
    /**
     * @var string
     */
    protected $sStep = '';

    /**
     * @var bool
     */
    protected $bDeleteAvailableNewsletter = false;

    /**
     * @var TdbPkgNewsletterUser|null
     */
    protected $oNewsletterSignup;

    /**
     * @var bool
     */
    protected $bAllowHTMLDivWrapping = true;

    public const INPUT_DATA_NAME = 'aPkgNewsletter';
    public const NEWSLETTEROPTINSEND = 'sNewsletterOptInSend';

    /**
     * @var TdbPkgNewsletterModuleSignupConfig
     */
    private $oModuleConfig;

    /**
     * set to true if you want a user to be able to sign up with an email that does not match the mail of the extranet user.
     *
     * @var bool
     */
    protected $bAllowSigningUpEMailsNotBelongingToUser = false;

    /**
     * @return void
     */
    public function Init()
    {
        parent::Init();

        if ($this->global->UserDataExists('optincode')) {
            $this->sStep = 'ConfirmSignup';
        } else {
            // check if the user has a newsletter... if he does, transfer to correct step
            $oUserNewsletter = null;
            if (false == $this->bAllowSigningUpEMailsNotBelongingToUser) {
                $oUserNewsletter = TdbPkgNewsletterUser::GetInstanceForActiveUser();
            }
            if (!is_null($oUserNewsletter)) {
                $oNewsletterConfig = $this->GetConfig();
                $aAvailableForUserList = $oUserNewsletter->CompareNewsletterLists($oNewsletterConfig->GetFieldPkgNewsletterGroupList());
                if (count($aAvailableForUserList) > 0) {
                    $this->sStep = 'SignUp';
                    $this->data['aAvailableForUserList'] = $aAvailableForUserList;
                } else {
                    $this->sStep = 'NoNewSignedUp';
                }
            } else {
                $this->sStep = 'SignUp';
            }
        }
    }

    /**
     * Get the module config (when module is static get module config from main module).
     *
     * @return TdbPkgNewsletterModuleSignupConfig $oNewsletterConfig
     */
    protected function GetConfig()
    {
        if (is_null($this->oModuleConfig)) {
            if (is_null($this->instanceID)) {
                if (array_key_exists('main_instance_id', $this->aModuleConfig)) {
                    $sInstanceId = $this->aModuleConfig['main_instance_id'];
                } else {
                    $sInstanceId = null;
                }
            } else {
                $sInstanceId = $this->instanceID;
            }
            $this->oModuleConfig = TdbPkgNewsletterModuleSignupConfig::GetNewInstance();
            $this->oModuleConfig->LoadFromField('cms_tpl_module_instance_id', $sInstanceId);
            if ($this->oModuleConfig->fieldMainModuleInstanceId) {
                $this->oModuleConfig->LoadFromField('cms_tpl_module_instance_id', $this->oModuleConfig->fieldMainModuleInstanceId);
            }
        }

        return $this->oModuleConfig;
    }

    /**
     * @return array<string, mixed>
     */
    public function Execute()
    {
        parent::Execute();
        $this->data['oNewsletterConfig'] = $this->GetConfig();
        $this->data['oNewsletterSignup'] = $this->LoadNewsletterSignup();
        $this->data['aMainModuleInfo'] = $this->GetMainModuleInfo($this->data['oNewsletterConfig']->fieldCmsTplModuleInstanceId);
        if (!empty($this->instanceID) && $this->instanceID == $this->data['oNewsletterConfig']->fieldCmsTplModuleInstanceId) {
            if ('ConfirmSignup' == $this->sStep) {
                $this->ConfirmSignup();
                $this->SetTemplate('MTPkgNewsletterSignup', 'system/signedup');
            } elseif ('SignUp' == $this->sStep) {
                if (!is_null($this->data['oNewsletterSignup']) && !empty($this->data['oNewsletterSignup']->id)) {
                    if (array_key_exists('aAvailableForUserList', $this->data) && is_array($this->data['aAvailableForUserList']) && count($this->data['aAvailableForUserList']) > 0 && !$this->bDeleteAvailableNewsletter) {
                        $bShowStandard = true;
                    } else {
                        $oUser = TdbDataExtranetUser::GetInstance();
                        if ($oUser->IsLoggedIn() && $oUser->fieldName == $this->oNewsletterSignup->fieldEmail) {
                            $this->SetTemplate('MTPkgNewsletterSignup', 'system/signedup');
                        } else {
                            if ($this->data['oNewsletterConfig']->fieldUseDoubleoptin) {
                                $this->SetTemplate('MTPkgNewsletterSignup', 'system/confirm');
                            } else {
                                $this->SetTemplate('MTPkgNewsletterSignup', 'system/signedup');
                            }
                        }
                    }
                }
            } elseif ('NoNewSignedUp' == $this->sStep) {
                $this->SetTemplate('MTPkgNewsletterSignup', 'system/nonewstosignup');
            } else {
            }
        }

        return $this->data;
    }

    /**
     * @return void
     */
    protected function DefineInterface()
    {
        parent::DefineInterface();
        $this->methodCallAllowed[] = 'SignUp';
    }

    /**
     * lazy load newsletter object.
     *
     * @return TdbPkgNewsletterUser
     */
    protected function LoadNewsletterSignup()
    {
        if (is_null($this->oNewsletterSignup)) {
            $oUser = TdbDataExtranetUser::GetInstance();
            $this->oNewsletterSignup = TdbPkgNewsletterUser::GetNewInstance();
            if (false == $this->bAllowSigningUpEMailsNotBelongingToUser) {
                $oNewsletter = TdbPkgNewsletterUser::GetInstanceForActiveUser();
                if (!is_null($oNewsletter)) {
                    $this->oNewsletterSignup = $oNewsletter;
                }
            }

            $oGlobal = TGlobal::instance();
            if ($oGlobal->UserDataExists(self::INPUT_DATA_NAME)) {
                $aData = $oGlobal->GetUserData(self::INPUT_DATA_NAME);
                if (is_array($aData)) {
                    if (array_key_exists('email', $aData) && $aData['email'] != $oUser->fieldName) {
                        $this->oNewsletterSignup = TdbPkgNewsletterUser::GetNewInstance();
                    } elseif (!array_key_exists('email', $aData) && $oUser->IsLoggedIn()) {
                        $aData['email'] = $oUser->GetUserEMail();
                    }
                    $oPortal = $this->getPortalDomainService()->getActivePortal();
                    if (!is_null($oPortal)) {
                        $aData['cms_portal_id'] = $oPortal->id;
                    }
                    $this->oNewsletterSignup->LoadFromRowProtected($aData);
                }
            }
        }

        return $this->oNewsletterSignup;
    }

    /**
     * returns array of all fields, that are marked as required in pkg_newsletter_user table
     * always sets email as mandatory field.
     *
     * @return string[]
     */
    protected function getRequiredFields()
    {
        $aRequiredFields = $this->oNewsletterSignup->GetRequiredFields();
        if (false === array_search('email', $aRequiredFields)) {
            $aRequiredFields[] = 'email';
        }

        return $aRequiredFields;
    }

    /**
     * @return bool
     */
    protected function ValidateInput()
    {
        $bContinue = true;
        $oMsgManager = TCMSMessageManager::GetInstance();
        // validate data...
        $aRequiredFields = $this->getRequiredFields();
        if (!is_array($this->oNewsletterSignup->sqlData)) {
            $this->oNewsletterSignup->sqlData = [];
        }

        foreach ($aRequiredFields as $sFieldName) {
            $sVal = '';
            if (array_key_exists($sFieldName, $this->oNewsletterSignup->sqlData)) {
                $sVal = trim($this->oNewsletterSignup->sqlData[$sFieldName]);
            }

            // handle fields that are not in database (like privacy checkboxes)
            if (empty($sVal)) {
                $aData = $this->global->GetUserData(self::INPUT_DATA_NAME);
                if (isset($aData[$sFieldName])) {
                    $sVal = $aData[$sFieldName];
                }
            }

            if (empty($sVal)) {
                $oMsgManager->AddMessage(self::INPUT_DATA_NAME.'-'.$sFieldName, 'ERROR-USER-REQUIRED-FIELD-MISSING');
                $bContinue = false;
            } else {
                $this->oNewsletterSignup->sqlData[$sFieldName] = $sVal;
            }
        }

        // check format for email field
        if (!empty($this->oNewsletterSignup->fieldEmail) && !TTools::IsValidEMail($this->oNewsletterSignup->fieldEmail)) {
            $oMsgManager->AddMessage(self::INPUT_DATA_NAME.'-email', 'ERROR-E-MAIL-INVALID-INPUT');
            $bContinue = false;
        }

        return $bContinue;
    }

    /**
     * sign up selected newsletter.
     *
     * @return void
     */
    public function SignUp()
    {
        $this->LoadNewsletterSignup();
        $oNewsletterConfig = $this->GetConfig();
        $bContinue = $this->ValidateInput();
        $oMsgManager = TCMSMessageManager::GetInstance();
        $aData = [];
        $bSaveConfirmations = false;
        if ($bContinue) {
            $oGlobal = TGlobal::instance();
            $aData = $oGlobal->GetUserData(self::INPUT_DATA_NAME);
            if (array_key_exists('newsletter', $aData) && count($aData['newsletter']) > 0) {
                $bSaveConfirmations = true;
            } else {
                $oConfiguredNewsletterList = $oNewsletterConfig->GetFieldPkgNewsletterGroupList();
                if ($oConfiguredNewsletterList->Length() > 0) {
                    $bContinue = false;
                    $oMsgManager->AddMessage(self::INPUT_DATA_NAME.'-newsletter', 'ERROR-NEWSLETTER-CHOOSE-NEWSLETTER');
                }
            }
        }
        $oUser = TdbDataExtranetUser::GetInstance();
        $bNewsletterUserAlreadyRegistered = false;
        if ($bContinue) {
            // check email
            $oRegisteredNewsletterUser = $this->oNewsletterSignup->EMailAlreadyRegisteredNew();
            if (!is_null($oRegisteredNewsletterUser->id)) {
                // someone else has this email...
                if (!empty($oRegisteredNewsletterUser->fieldDataExtranetUserId) && $oRegisteredNewsletterUser->fieldDataExtranetUserId != $oUser->id) {
                    $bContinue = false;
                    $oMsgManager->AddMessage(self::INPUT_DATA_NAME, 'ERROR-NEWSLETTER-SIGNIN-E-MAIL-ALREADY-REGISTERED-WITH-USER');
                } else {
                    $this->oNewsletterSignup = $oRegisteredNewsletterUser;
                }
                $bNewsletterUserAlreadyRegistered = true;
            }
        }

        if ($bContinue) {
            // save!
            $bUpdateConfirmations = false;
            $this->oNewsletterSignup->AllowEditByAll(true);
            if (!$bNewsletterUserAlreadyRegistered) {
                $oMsgManager->AddMessage(self::NEWSLETTEROPTINSEND, 'INFO-NEWSLETTER-SIGNIN-SENDNEWSOPTIN');
            }
            $this->oNewsletterSignup->Save($oNewsletterConfig);
            if ($bSaveConfirmations) {
                $bUpdateConfirmations = $this->oNewsletterSignup->SaveNewsletterConfirmations($this->TransformInputNewsletterList($aData['newsletter']), $oNewsletterConfig);
            }

            if ($bUpdateConfirmations) {
                if (!$oNewsletterConfig->fieldUseDoubleoptin || ($oUser->IsLoggedIn() && $oUser->fieldName == $this->oNewsletterSignup->sqlData['email'])) {
                    $this->oNewsletterSignup->ConfirmSignupNew();
                }
            } else {
                if ($bNewsletterUserAlreadyRegistered) {
                    $oMsgManager->AddMessage(self::INPUT_DATA_NAME.'-email', 'ERROR-NEWSLETTER-SIGNIN-FOR-E-MAIL-ALREADY-SIGNEDIN');
                    $this->oNewsletterSignup = null;
                } else {
                    if (!$bSaveConfirmations) {
                        $this->oNewsletterSignup->PostSignUpNewsletterUserOnly();
                    }
                }
            }
            if (array_key_exists('newsletter', $aData) && is_array($aData['newsletter'])) {
                foreach ($aData['newsletter'] as $NewsletterGroupId => $sValue) {
                    if ('all' != $NewsletterGroupId) {
                        if (array_key_exists('aAvailableForUserList', $this->data) && is_array($this->data['aAvailableForUserList']) && array_key_exists($NewsletterGroupId, $this->data['aAvailableForUserList'])) {
                            unset($this->data['aAvailableForUserList'][$NewsletterGroupId]);
                            $this->bDeleteAvailableNewsletter = true;
                        }
                    }
                }
            }
        }
    }

    /**
     * @param array $aNewsletterList
     *
     * @return string[]
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
     * @return CacheInterface
     */
    protected function getCacheManager()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.cache');
    }

    /**
     * get main module info.
     *
     * @param string $sModuleInstanceId
     *
     * @return array $aReturnArray (page url of main module & spotname of mainmodule)
     */
    protected function GetMainModuleInfo($sModuleInstanceId)
    {
        $oConfig = $this->GetConfig();
        $aKey = ['class' => __CLASS__, 'method' => 'GetMainModuleInfo', 'sModuleInstanceId' => $sModuleInstanceId, 'sActivePageId' => '', 'instanceID' => $this->instanceID, 'sModuleSpotName' => $this->sModuleSpotName, 'sModuleConfigId' => $oConfig->id];
        $sKey = $this->getCacheManager()->getKey($aKey);
        $aReturnArray = $this->getCacheManager()->get($sKey);
        if (null === $aReturnArray) {
            $aReturnArray = [];
            if (!empty($this->instanceID) && $this->instanceID == $oConfig->fieldCmsTplModuleInstanceId) {
                $aReturnArray['spotname'] = $this->sModuleSpotName;
                $aReturnArray['URL'] = preg_replace(
                    '/\?.*/',
                    '',
                    $this->getActivePageService()->getLinkToActivePageRelative()
                );
            } else {
                $oPortal = $this->getPortalDomainService()->getActivePortal();
                $oModuleInstance = TdbCmsTplModuleInstance::GetNewInstance();
                $oModuleInstance->Load($sModuleInstanceId);
                if (method_exists($oModuleInstance, 'GetFieldCmsTplPageList')) {
                    /** @var TdbCmsTplPageList $oPageList */
                    $oPageList = $oModuleInstance->GetFieldCmsTplPageList();
                    $bFound = false;
                    while ($oPage = $oPageList->Next()) {
                        if ($oPage->fieldCmsPortalId == $oPortal->id && !$bFound) {
                            $aReturnArray['URL'] = $this->getPageService()->getLinkToPageObjectAbsolute($oPage);
                            $query = "SELECT `cms_master_pagedef_spot`.`name` FROM `cms_tpl_page_cms_master_pagedef_spot`
                 LEFT JOIN `cms_master_pagedef_spot` ON `cms_master_pagedef_spot`.`id`=`cms_tpl_page_cms_master_pagedef_spot`.`cms_master_pagedef_spot_id`
                     WHERE `cms_tpl_page_cms_master_pagedef_spot`.`cms_tpl_page_id` ='".MySqlLegacySupport::getInstance()->real_escape_string($oPage->id)."'
                       AND `cms_tpl_page_cms_master_pagedef_spot`.`cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sModuleInstanceId)."'";
                            $res = MySqlLegacySupport::getInstance()->query($query);
                            while ($aSpotRow = MySqlLegacySupport::getInstance()->fetch_assoc($res)) {
                                $aReturnArray['spotname'] = $aSpotRow['name'];
                            }
                            $bFound = true;
                        }
                    }
                } elseif (method_exists($oModuleInstance, 'GetFieldCmsTplPageCmsMasterPagedefSpotList')) {
                    $oPageDefSpotList = $oModuleInstance->GetFieldCmsTplPageCmsMasterPagedefSpotList();
                    $bFound = false;
                    while ($oPageDefSpot = $oPageDefSpotList->Next()) {
                        $oPage = TdbCmsTplPage::GetNewInstance();
                        $oPage->Load($oPageDefSpot->fieldCmsTplPageId);
                        if ($oPage->fieldCmsPortalId == $oPortal->id && !$bFound) {
                            $aReturnArray['URL'] = $this->getPageService()->getLinkToPageObjectAbsolute($oPage);
                            $query = "SELECT `cms_master_pagedef_spot`.`name` FROM `cms_tpl_page_cms_master_pagedef_spot`
                 LEFT JOIN `cms_master_pagedef_spot` ON `cms_master_pagedef_spot`.`id`=`cms_tpl_page_cms_master_pagedef_spot`.`cms_master_pagedef_spot_id`
                     WHERE `cms_tpl_page_cms_master_pagedef_spot`.`cms_tpl_page_id` ='".MySqlLegacySupport::getInstance()->real_escape_string($oPage->id)."'
                       AND `cms_tpl_page_cms_master_pagedef_spot`.`cms_tpl_module_instance_id` = '".MySqlLegacySupport::getInstance()->real_escape_string($sModuleInstanceId)."'";
                            $res = MySqlLegacySupport::getInstance()->query($query);
                            while ($aSpotRow = MySqlLegacySupport::getInstance()->fetch_assoc($res)) {
                                $aReturnArray['spotname'] = $aSpotRow['name'];
                            }
                            $bFound = true;
                        }
                    }
                }
            }
            $aTrigger = [['table' => 'cms_tpl_page_cms_master_pagedef_spot', 'id' => null], ['table' => 'cms_tpl_page', 'id' => null], ['table' => $oConfig->table, 'id' => $oConfig->id], ['table' => 'cms_tpl_module_instance', 'id' => null]];
            $this->getCacheManager()->set($sKey, $aReturnArray, $aTrigger);
        }

        return $aReturnArray;
    }

    /**
     * confirm a sign up request (double-opt-in).
     *
     * @return bool
     */
    public function ConfirmSignup()
    {
        $bSignUpConfirmed = false;
        if ($this->global->UserDataExists('optincode')) {
            $oSignUp = TdbPkgNewsletterUser::GetNewInstance();
            if ($oSignUp->LoadFromField('optincode', $this->global->GetUserData('optincode'))) {
                $this->data['oNewsletterSignup'] = $oSignUp;
                // do not opt in if we have an opt in
                $bSignUpConfirmed = $oSignUp->ConfirmSignupNew();
                if (!$bSignUpConfirmed) {
                    $oMsgManager = TCMSMessageManager::GetInstance();
                    $oMsgManager->AddMessage(self::INPUT_DATA_NAME, 'ERROR-NEWSLETTER-SIGNIN-FOR-E-MAIL-ALREADY-SIGNEDIN');
                }
            } else {
                $oMsgManager = TCMSMessageManager::GetInstance();
                $oMsgManager->AddMessage(self::INPUT_DATA_NAME, 'ERROR-NEWSLETTER-SIGNIN-DOUBLE-OPT-IN-CODE-INVALID');
            }
        }

        return $bSignUpConfirmed;
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();
        $aIncludes = array_merge($aIncludes, $this->getResourcesForSnippetPackage('pkgNewsletter'));
        $aIncludes = array_merge($aIncludes, $this->getResourcesForSnippetPackage('pkgNewsletter/signUp'));
        $aIncludes = array_merge($aIncludes, $this->getResourcesForSnippetPackage('pkgNewsletter/signOut'));

        return $aIncludes;
    }

    public function _AllowCache()
    {
        return false;
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }

    /**
     * @return PageServiceInterface
     */
    private function getPageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.page_service');
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private function getPortalDomainService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }
}
