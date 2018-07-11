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
use ChameleonSystem\CoreBundle\Service\SystemPageServiceInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

/**
 * @deprecated since 6.0.13 - not needed anymore, technically antiquated, and imposed security risks
 */
class MTSendAFriendCore extends TUserCustomModelBase
{
    /**
     * TFormWizard Object.
     *
     * @var TFormWizard
     */
    public $oForm = null;

    public $allowSend = true;

    /**
     * default width of text fields of the form.
     *
     * @var int
     */
    protected $iDefaultFieldWidth = 250;

    /**
     * enable/disable antispam check.
     *
     * @var bool
     */
    protected $bUseAntiSpam = true;

    protected $bAllowHTMLDivWrapping = true;

    public function Init()
    {
        parent::Init();
        if (!isset($this->aModuleConfig['isOpener'])) {
            if ($this->bUseAntiSpam) {
                $this->GenerateAntiSpamValue();
            }
            $this->GenerateForm();
        }
    }

    public function &Execute()
    {
        $this->data = parent::Execute();

        if (isset($this->aModuleConfig['isOpener'])) {
            $this->LoadButtonInfos();
        }

        return $this->data;
    }

    /**
     * loads target form url from systempage or by instance id
     * sets current url as promote url.
     */
    protected function LoadButtonInfos()
    {
        self::SetSendAFriendURL(); // save current URL to session for the send a friend email

        $aAdditionalParameter = array('loadURL' => 'true');
        try {
            $sSendAFriendFormURL = $this->getSystemPageService()->getLinkToSystemPageRelative('sendAFriend', $aAdditionalParameter);
        } catch (RouteNotFoundException $e) {
            $sSendAFriendFormURL = '';
        }
        if ('javascript:' != substr($sSendAFriendFormURL, 0, 11)) {
            $this->data['sendAFriendFormURL'] = $sSendAFriendFormURL;
        } else {
            $oModuleInstance = new TCMSTPLModuleInstance();
            $oModuleInstance->Load($this->aModuleConfig['instanceID']);
            $query = "SELECT cms_tpl_page.*
                  from cms_tpl_page
            INNER JOIN cms_tpl_page_cms_master_pagedef_spot ON cms_tpl_page.id = cms_tpl_page_cms_master_pagedef_spot.cms_tpl_page_id
             WHERE cms_tpl_page_cms_master_pagedef_spot.cms_tpl_module_instance_id = '".MySqlLegacySupport::getInstance()->real_escape_string($oModuleInstance->id)."'
            ";
            $oPages = TdbCmsTplPageList::GetList($query);
            $oFirstFoundPage = $oPages->Current();
            $this->data['sendAFriendFormURL'] = $this->getPageService()->getLinkToPageObjectRelative($oFirstFoundPage, array(
                'loadURL' => 'true',
            ));
        }
    }

    protected function RedirectOnSuccess()
    {
        $this->SetTemplate('MTSendAFriend', 'inc/thankyou');
    }

    /**
     * sets the send a friend promote url
     * (will be added to the send-a-friend mail instead of portal url).
     *
     * @param string $url
     */
    public static function SetSendAFriendURL($url = '')
    {
        //get original_url from $_COOKIE if exists
        if (isset($_COOKIE['original_url'])) {
            $sOriginURL = $_COOKIE['original_url'];
        } else {
            $sOriginURL = '';
        }

        //if there is no original_url in $_COOKIE build own one!
        if (empty($sOriginURL)) {
            $sOriginURL = self::getActivePageService()->getLinkToActivePageAbsolute();
        }

        $_SESSION['MTSendAFriend']['url'] = $sOriginURL;
    }

    public function GenerateAntiSpamValue()
    {
        if (!$this->global->UserDataExists('spamcheck')) {
            $_SESSION['_spamcheckvals'] = array('a' => rand(1, 9), 'b' => rand(1, 9));
        }
    }

    public function GenerateForm()
    {
        $this->oForm = new TFormWizard('sendAFriend');

        // sender name
        $element = new TFormElement('from_name');
        $element->fieldType = 'text';
        $element->description = TGlobal::Translate('chameleon_system_core.module_send_a_friend.from_name').':';
        $element->width = $this->iDefaultFieldWidth;
        $element->setRequired(true, TGlobal::Translate('chameleon_system_core.module_send_a_friend.error_missing_from_name'));
        $this->oForm->addElement($element);

        // sender email
        $element = new TFormElement('from_email');
        $element->fieldType = 'text';
        $element->description = TGlobal::Translate('chameleon_system_core.module_send_a_friend.from_email').':';
        $element->width = $this->iDefaultFieldWidth;
        $element->setRequired(true, TGlobal::Translate('chameleon_system_core.module_send_a_friend.error_missing_from_email'));
        $element->setValidEmail(true, TGlobal::Translate('chameleon_system_core.module_send_a_friend.error_invalid_email'));
        $this->oForm->addElement($element);

        // receiver name
        $element = new TFormElement('to_name');
        $element->fieldType = 'text';
        $element->description = TGlobal::Translate('chameleon_system_core.module_send_a_friend.to_name').':';
        $element->width = $this->iDefaultFieldWidth;
        $element->setRequired(true, TGlobal::Translate('chameleon_system_core.module_send_a_friend.error_missing_to_name'));
        $this->oForm->addElement($element);

        // receiver email
        $element = new TFormElement('to_email');
        $element->fieldType = 'text';
        $element->description = TGlobal::Translate('chameleon_system_core.module_send_a_friend.to_email').':';
        $element->width = $this->iDefaultFieldWidth;
        $element->setRequired(true, TGlobal::Translate('chameleon_system_core.module_send_a_friend.error_missing_to_email'));
        $element->setValidEmail(true, TGlobal::Translate('chameleon_system_core.module_send_a_friend.error_invalid_email'));
        $this->oForm->addElement($element);

        // message
        $element = new TFormElement('message');
        $element->fieldType = 'textarea';
        $element->description = TGlobal::Translate('chameleon_system_core.module_send_a_friend.message').':';
        $element->width = $this->iDefaultFieldWidth;
        $element->height = 150;
        $element->maxlength = 500;
        $this->oForm->addElement($element);

        // spamcheck
        if ($this->bUseAntiSpam) {
            $element = new TFormElement('spamcheck');
            $element->fieldType = 'AntiSpam';
            $this->oForm->addElement($element);
        }

        // info
        $element = new TFormElement();
        $element->defaultValue = TGlobal::Translate('chameleon_system_core.module_send_a_friend.help_required_fields');
        $element->fieldType = 'separator';
        $element->colspan = 2;
        $element->showOnValidSubmit = false;
        $this->oForm->addElement($element);

        $this->oForm->buttonsHTML = '<button type="submit" class="submitButton">'.TGlobal::Translate('chameleon_system_core.module_send_a_friend.action_send').'</button>';

        $this->data['HTMLform'] = $this->oForm->PrintForm();
        $this->data['HTMLformHiddenFields'] = $this->oForm->hiddenFields;

        return true;
    }

    public function SendEMail()
    {
        $this->getLogger()->warning('Someone tried to use MTSendAFriend - this is not possible anymore. If this function is needed, consider a custom implementation.');
    }

    public function AfterSendEMailSuccess()
    {
    }

    protected function _GetUserName()
    {
        return $this->global->GetUserData('from_name');
    }

    protected function _GetUserEMail()
    {
        return $this->global->GetUserData('from_email');
    }

    protected function _GetToEMail()
    {
        return $this->global->GetUserData('to_email');
    }

    protected function _GetToName()
    {
        return $this->global->GetUserData('to_name');
    }

    protected function _GetUserMessage()
    {
        return $this->global->GetUserData('message');
    }

    protected function GetSubject()
    {
        $subject = TGlobal::Translate('chameleon_system_core.module_send_a_friend.mail_subject');

        return $subject;
    }

    protected function _ValidateFields()
    {
        $this->oForm->formMode = 'check';
        $this->data['HTMLform'] = $this->oForm->CheckForm();
        if (is_array($this->oForm->errorMessageArray) && count($this->oForm->errorMessageArray) > 0) {
            $this->allowSend = false;
        }
    }

    protected function _GetMailBody()
    {
        $aNames = array();
        $aNames['%name%'] = $this->_GetToName();
        $aNames['%username%'] = $this->_GetUserName();
        $message = TGlobal::Translate('chameleon_system_core.module_send_a_friend.mail_intro', $aNames);
        $message .= "\n===============================================================================\n";
        $message .= $this->GetPromoteURL()."\n";
        $message .= "===============================================================================\n";
        $message .= TGlobal::Translate('chameleon_system_core.module_send_a_friend.mail_message_from', $aNames);
        $message .= "\n".$this->_GetUserMessage()."\n\n";
        $message .= "===============================================================================\n";

        return $message;
    }

    /**
     * fetches promote url from session or returns portal home url.
     *
     * @return string
     */
    protected function GetPromoteURL()
    {
        $bLoadFromSession = false;
        $sLoadFromSession = $this->global->GetUserData('loadURL');
        if (!empty($sLoadFromSession) && 'true' == strtolower($sLoadFromSession)) {
            $bLoadFromSession = true;
        }

        $sURL = '';
        if ($bLoadFromSession && array_key_exists('MTSendAFriend', $_SESSION) && array_key_exists('url', $_SESSION['MTSendAFriend']) && !empty($_SESSION['MTSendAFriend']['url'])) {
            $sURL = $_SESSION['MTSendAFriend']['url'];
        } else {
            $sURL = $this->getPageService()->getLinkToPortalHomePageAbsolute();
        }

        $sURL = str_replace(' ', '+', $sURL);

        return $sURL;
    }

    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();

        $aIncludes[] = '<link href="'.TGlobal::GetStaticURLToWebLib('/web_modules/MTConfigurableFeedbackCore/css/uni-form.css').'" media="screen" rel="stylesheet" type="text/css" />';

        $aIncludes[] = "<!--[if lte ie 6]>
      <style type=\"text/css\" media=\"screen\">
        .uniForm,
        .uniForm fieldset,
        .ctrlHolder,
        .ctrlHolder span,
        .formHint{
          zoom:1;
        }
        .blockLabels .formHint{
          margin-top:0;
        }
      </style>
      <![endif]-->\n";

        $aIncludes[] = '<script src="'.TGlobal::GetStaticURLToWebLib('/web_modules/MTConfigurableFeedbackCore/js/uni-form.jquery.js').'" type="text/javascript"></script>';

        // load optional custom css from /chameleon/web_modules/ directory
        if (file_exists(PATH_USER_CMS_PUBLIC.'/web_modules/MTSendAFriendCore/css/uni-form.css')) {
            $aIncludes[] = '<link href="'.TGlobal::GetStaticURL('/web_modules/MTSendAFriendCore/css/uni-form.css').'" media="screen" rel="stylesheet" type="text/css" />';
        }

        return $aIncludes;
    }

    protected function DefineInterface()
    {
        $externalFunctions = array('SendEMail');
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.active_page_service');
    }

    /**
     * @return PageServiceInterface
     */
    private function getPageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.page_service');
    }

    /**
     * @return SystemPageServiceInterface
     */
    private function getSystemPageService()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.system_page_service');
    }

    /**
     * @return IPkgCmsCoreLog
     */
    private function getLogger()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('cmspkgcore.logchannel.standard');
    }
}
