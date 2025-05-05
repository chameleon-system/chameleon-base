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

class TDataMailProfile extends TDataMailProfileAutoParent
{
    /**
     * recipient email address.
     *
     * @var string
     */
    protected $sToMail;

    /**
     * recipient name.
     *
     * @var string
     */
    protected $sToMailName;

    /**
     * sender email address.
     *
     * @var string
     */
    protected $sFromMail;

    /**
     * sender name.
     *
     * @var string
     */
    protected $sFromMailName;

    /**
     * reply name.
     *
     * @var string
     */
    protected $sReplyMailName;

    /**
     * array of variables that will be replaced in mail text [{varname}].
     *
     * @var array
     */
    protected $aMailData = [];

    /**
     * email subject.
     *
     * @var string
     */
    protected $sSubject;

    /**
     * reply mail.
     *
     * @var string
     */
    protected $sReplyMail;

    /**
     * array of all "TO" addresses.
     *
     * @var array - key = email, value = name (optional)
     */
    protected $aReceiverEMails = [];

    /**
     * list of all BCC emails set via addBccEmail() or via fieldMailbcc.
     *
     * @var array
     */
    protected $bccEmails = [];

    /**
     * list of all CC emails set via addCcEmail().
     *
     * @var array
     */
    protected $ccEmails = [];

    /**
     * adds data to the replacement variable array.
     *
     * @param string $key
     * @param string $value
     */
    public function AddData($key, $value)
    {
        $this->aMailData[$key] = $value;
    }

    /**
     * adds data to the replacment variable array.
     *
     * @param array $aData
     */
    public function AddDataArray($aData)
    {
        $this->aMailData = array_merge($this->aMailData, $aData);
    }

    /**
     * gets data from the replacement variable array.
     *
     * @param string $key
     *
     * @return string $value
     */
    public function GetData($key)
    {
        if (array_key_exists($key, $this->aMailData)) {
            return $this->aMailData[$key];
        }

        return false;
    }

    /**
     * Fetches a profile with the idcode $sProfileName.
     *
     * @param string $sProfileName
     * @param string $iLanguage
     *
     * @return TdbDataMailProfile|null
     */
    public static function GetProfile($sProfileName, $iLanguage = null)
    {
        $oActivePortal = self::getMyPortalDomainService()->getActivePortal();
        if ($oActivePortal) {
            $sPortalQueryPart = " AND (`cms_portal_id` = '".MySqlLegacySupport::getInstance()->real_escape_string(
                $oActivePortal->id
            )."' OR `cms_portal_id` = '') ORDER BY `cms_portal_id` DESC";
        } else {
            $sPortalQueryPart = " AND `cms_portal_id` = ''";
        }
        $sQuery = "SELECT * FROM `data_mail_profile` WHERE `idcode` = '".MySqlLegacySupport::getInstance(
        )->real_escape_string($sProfileName)."'".$sPortalQueryPart;
        $oProfileList = TdbDataMailProfileList::GetList($sQuery, $iLanguage);
        $oInstance = $oProfileList->Next();
        if (false === $oInstance) {
            $oInstance = null;
        } else {
            $oInstance->sSubject = $oInstance->fieldSubject;
        }

        return $oInstance;
    }

    /**
     * Fetch a profile with the idcode $sProfileName for a portal.
     *
     * @param string $sProfileName
     * @param TdbCmsPortal $oPortal
     * @param string $iLanguage
     *
     * @return TDataMailProfile
     */
    public static function GetProfileForPortal($sProfileName, $oPortal, $iLanguage = null)
    {
        $oInstance = null;

        if (is_object($oPortal)) {
            $oInstance = TdbDataMailProfile::GetNewInstance();
            $oInstance->SetLanguage($iLanguage);
            $sQuery = "SELECT * FROM `data_mail_profile` WHERE `idcode` = '".MySqlLegacySupport::getInstance(
            )->real_escape_string($sProfileName)."'";
            $sQuery .= " AND (`cms_portal_id` = '".MySqlLegacySupport::getInstance()->real_escape_string(
                $oPortal->id
            )."' OR `cms_portal_id` = '') ORDER BY `cms_portal_id` DESC";
            $oProfileList = TdbDataMailProfileList::GetList($sQuery, $iLanguage);
            $oInstance = $oProfileList->Next();
            if (false === $oInstance) {
                $oInstance = null;
            }
        }

        return $oInstance;
    }

    /**
     * {@inheritdoc}
     */
    protected function PostLoadHook()
    {
        parent::PostLoadHook();
        $this->sToMail = $this->sqlData['mailto'];
        $this->sToMailName = $this->GetMailName('mailto_name');
        $this->sFromMail = $this->sqlData['mailfrom'];
        $this->sFromMailName = $this->GetMailName('mailfrom_name');
        $this->SetSubject($this->fieldSubject);
    }

    /**
     * If email name is not empty in given field set email name configured in email template.
     * If email name is empty in given field set default email name from portal name.
     *
     * @param string $sFieldName
     *
     * @return string
     */
    protected function GetMailName($sFieldName)
    {
        $sEmailName = $this->sqlData[$sFieldName];
        if (empty($sEmailName)) {
            $portalService = self::getMyPortalDomainService();
            $activePortal = $portalService->getActivePortal();

            if (null !== $activePortal && !empty($activePortal->fieldName)) {
                $sEmailName = $activePortal->fieldName;
            }
        }

        return $sEmailName;
    }

    /**
     * changes the primary receiver email address and name.
     *
     * @param string $sMail
     * @param string $sName - optional (if missing, name = email)
     */
    public function ChangeToAddress($sMail, $sName = null)
    {
        $this->sToMail = trim($sMail);
        if (null !== $sName) {
            $this->sToMailName = $sName;
        } else {
            $this->sToMailName = $sMail;
        }
    }

    /**
     * adds a receiver email and name.
     *
     * @param string $sMail
     * @param string $sName - optional (if missing, name = email)
     */
    public function AddToAddress($sMail, $sName = null)
    {
        if (null === $sName) {
            $sName = $sMail;
        }

        if (empty($this->sToMail)) {
            $this->sToMail = $sMail;
            $this->sToMailName = $sName;
        } else {
            $this->aReceiverEMails[$sMail] = $sName;
        }
    }

    /**
     * changes the from email address and name.
     *
     * @param string $sMail
     * @param string $sName
     */
    public function ChangeFromAddress($sMail, $sName = null)
    {
        $this->sFromMail = $sMail;
        if (null !== $sName) {
            $this->sFromMailName = $sName;
        } else {
            $this->sFromMailName = $sMail;
        }
    }

    /**
     * changes the reply email address and name.
     *
     * @param string $sMail
     * @param string $sName
     */
    public function ChangeReplyAddress($sMail, $sName = null)
    {
        $this->sReplyMail = $sMail;
        if (null !== $sName) {
            $this->sReplyMailName = $sName;
        } else {
            $this->sReplyMailName = $sMail;
        }
    }

    /**
     * sends an email if e-mail template location is set
     * Optional: array of files to be attached.
     *
     * @param string $sModuleInWhichTheViewsAreToBeFound
     * @param array|null $aAttachFiles
     *
     * @return bool
     */
    public function Send($sModuleInWhichTheViewsAreToBeFound, $aAttachFiles = null)
    {
        $oMail = $this->GetMailObject($aAttachFiles);
        $oMail->SetTemplates($sModuleInWhichTheViewsAreToBeFound, $this->sqlData['template'], $this->sqlData['template_text']);

        return $oMail->Send($this->aMailData);
    }

    /**
     * sends an email using a template from object class path.
     *
     * @param string $sSubType - subdirectory
     * @param string $sType - Core/Customer
     * @param array|null $aAttachFiles
     *
     * @return bool
     */
    public function SendUsingObjectView($sSubType = '', $sType = 'Core', $aAttachFiles = null)
    {
        $oMail = $this->GetMailObject($aAttachFiles);

        $oMail->SetObjectTemplate($sSubType, $sType, $this->sqlData['template'], $this->sqlData['template_text']);

        return $oMail->Send($this->aMailData);
    }

    /**
     * @param string $sSubType
     * @param string $sType
     * @param array|null $aAttachFiles
     *
     * @return bool
     */
    public function SendUsingPackageObjectView($sSubType = '', $sType = 'Core', $aAttachFiles = null)
    {
        $oMail = $this->GetMailObject($aAttachFiles);
        $oMail->SetPackageObjectTemplate($sSubType, $sType, $this->sqlData['template'], $this->sqlData['template_text']);

        return $oMail->Send($this->aMailData);
    }

    /**
     * loads email object.
     *
     * @param array $aAttachFiles - list of file path to attach to the email. if the array keys are non numeric, the method will use them as file names
     *
     * @return TCMSMail
     */
    protected function GetMailObject(?array $aAttachFiles = null)
    {
        $mailer = $this->getMailer();
        $this->aMailData['subject'] = $this->GetSubject();
        $mailer->SetSubject($this->aMailData['subject']);
        $mailer->SetFromData($this->sFromMail, $this->sFromMailName);

        // add primary receiver
        if (null !== $this->sReplyMail && !empty($this->sReplyMail)) {
            $mailer->clearReplyTos();
            $mailer->AddReplyTo($this->sReplyMail, $this->sReplyMailName);
        }

        // add additional receiver
        $mailer->AddAddress($this->sToMail, $this->sToMailName);
        foreach ($this->aReceiverEMails as $sReceiverEMail => $sReceiverName) {
            $mailer->AddAddress($sReceiverEMail, $sReceiverName);
        }

        $this->aMailData['__FIELDS'] = print_r($this->aMailData, true);
        $this->aMailData['body'] = $this->GetBody();
        // fix image urls for iOS mail clients
        $iOSMailURLEncoder = new TPkgCmsStringUtilities_iOSMailURLEncoder();
        $this->aMailData['body'] = $iOSMailURLEncoder->encode($this->aMailData['body']);
        $this->aMailData['bodyText'] = $this->GetBodyText();

        // add attachments to the mail
        if (null !== $aAttachFiles) {
            foreach ($aAttachFiles as $sFileName => $sFilePath) {
                $sNameUsed = (!is_numeric($sFileName)) ? $sFileName : '';
                $mailer->addAttachment($sFilePath, $sNameUsed);
            }
        }

        // add attachments from mail profile configuration to the mail
        $oDownloads = $this->GetDownloads('attachment');
        while ($oDownload = $oDownloads->Next()) {
            $mailer->addAttachment($oDownload->GetRealPath(), $oDownload->GetName());
        }

        $aBCC = $this->GetBCCList();
        foreach ($aBCC as $bccEmail => $bccName) {
            $mailer->AddBCC($bccEmail, $bccName);
        }

        $ccEmails = $this->getCcList();
        foreach ($ccEmails as $ccEmail => $ccName) {
            $mailer->AddCC($ccEmail, $ccName);
        }

        return $mailer;
    }

    /**
     * @param string $email
     * @param string|null $name
     */
    public function addBccEmail($email, $name = null)
    {
        if (null === $name || '' === $name) {
            $name = $email;
        }

        $this->bccEmails[$email] = $name;
    }

    /**
     * @param string $email
     * @param string|null $name
     */
    public function addCcEmail($email, $name = null)
    {
        if (null === $name || '' === $name) {
            $name = $email;
        }

        $this->ccEmails[$email] = $name;
    }

    /**
     * return bcc list.
     *
     * @return array key = email, val = name
     */
    protected function GetBCCList()
    {
        $bccList = [];
        $bccLines = explode("\n", trim($this->sqlData['mailbcc']));
        foreach ($bccLines as $email) {
            $email = trim($email);
            if ('' !== $email && !isset($bccList[$email]) && !isset($this->bccEmails[$email])) {
                $bccList[$email] = $email;
            }
        }

        return array_merge($bccList, $this->bccEmails);
    }

    /**
     * @return array key = email, val = name
     */
    protected function getCcList()
    {
        return $this->ccEmails;
    }

    /**
     * loads the subject and replaces placeholder variables.
     *
     * @return string
     */
    protected function GetSubject()
    {
        return $this->injectVarInString($this->sSubject);
    }

    /**
     * replaces default subject from database with custom subject.
     *
     * @param string $sSubject
     */
    public function SetSubject($sSubject)
    {
        $this->sSubject = $sSubject;
    }

    /**
     * parses and replaces variables and returns the HTML body text.
     *
     * @return string
     */
    protected function GetBody()
    {
        $currentNonSSLSetting = TCMSImage::ForceNonSSLURLs(
        ); // we need this to prevent resetting this setting by accident
        if (!$currentNonSSLSetting) {
            TCMSImage::ForceNonSSLURLs(true);
        }
        $sBody = $this->GetTextForExternalUsage('body', 600, false, $this->aMailData);
        reset($this->aMailData);
        if (!$currentNonSSLSetting) {
            TCMSImage::ForceNonSSLURLs(false);
        }

        return $sBody;
    }

    /**
     * parses and replaces variables and returns the plaintext body.
     *
     * @return string
     */
    protected function GetBodyText()
    {
        $sBody = $this->fieldBodyText;

        $oText = new TCMSTextField();
        /* @var $oText TCMSTextField */
        $oText->content = $sBody;
        $sBody = $oText->GetPlainTextWordSave(null, $this->aMailData);
        reset($this->aMailData);

        return $sBody;
    }

    protected function injectVarInString($string)
    {
        if (false === is_array($this->aMailData) || 0 === count($this->aMailData)) {
            return $string;
        }

        $oStringReplace = new TPkgCmsStringUtilities_VariableInjection();
        $sContent = $oStringReplace->replace($string, $this->aMailData, false);

        // add twig support
        $oSnippet = TPkgSnippetRenderer::GetNewInstance($sContent, IPkgSnippetRenderer::SOURCE_TYPE_STRING);
        reset($this->aMailData);
        foreach (array_keys($this->aMailData) as $sKey) {
            $oSnippet->setVar($sKey, $this->aMailData[$sKey]);
        }
        reset($this->aMailData);
        $sContent = $oSnippet->render();
        unset($oSnippet);

        return $sContent;
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private static function getMyPortalDomainService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }

    /**
     * @return TCMSMail
     */
    private function getMailer()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.mailer');
    }
}
