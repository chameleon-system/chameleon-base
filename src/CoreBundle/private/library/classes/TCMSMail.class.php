<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Interfaces\TransformOutgoingMailTargetsServiceInterface;
use ChameleonSystem\CoreBundle\Security\Https\HttpsContextInterface;
use PHPMailer\PHPMailer\PHPMailer;

/**
 * a mail wrapper to send mails using the template engine.
 */
class TCMSMail extends PHPMailer
{
    public const TEMPLATETYPE_MODULE = 0;
    public const TEMPLATETYPE_OBJECT = 1;
    public const TEMPLATETYPE_PACKAGEOBJECT = 2;

    /**
     * @var string|null
     */
    protected $sHTMLTemplate;
    /**
     * @var string|null
     */
    protected $sTextTemplate;
    /**
     * @var string|null
     */
    protected $sModuleName;
    /**
     * @var array
     */
    protected $aObjectTemplateData = ['sSubType' => '', 'sType' => ''];
    /**
     * @var int
     */
    protected $iTemplateType = 0;
    /**
     * @var string
     */
    protected $actualSubject = '';
    /**
     * @var TTools
     */
    protected $oTools;
    /**
     * @var HttpsContextInterface
     */
    protected $mailPeerSecurity;
    /**
     * @var TransformOutgoingMailTargetsServiceInterface
     */
    private $transformationService;
    /**
     * @var string
     */
    private $smtpHost;
    /**
     * @var int
     */
    private $smtpPort;
    /**
     * @var string
     */
    private $smtpUser;
    /**
     * @var string
     */
    private $smtpPassword;

    public function __construct($exceptions = null)
    {
        parent::__construct($exceptions);

        $this->XMailer = null;
    }

    /**
     * @param TTools $oTools
     */
    public function setTools($oTools)
    {
        $this->oTools = $oTools;
    }

    /**
     * Set Subject.
     *
     * @param string $sSubject
     */
    public function SetSubject($sSubject)
    {
        $sSubject = $this->FilterLineFeeds($sSubject);
        if (CHAMELEON_EMAIL_ENCODING != 'UTF-8') {
            $sSubject = utf8_decode($sSubject);
        }
        $this->Subject = $sSubject;
    }

    /**
     * Set HTML and Text Template. if no html template is set, then the mail
     * will be send as text.
     *
     * @param string $sModuleName
     * @param string|null $sHTMLTemplate - example "test/mail" for a template ".test/mail.view.php"
     * @param string|null $sTextTemplate
     */
    public function SetTemplates($sModuleName, $sHTMLTemplate = null, $sTextTemplate = null)
    {
        $this->iTemplateType = self::TEMPLATETYPE_MODULE;
        $this->sModuleName = $sModuleName;
        $sHTMLTemplate = trim($sHTMLTemplate);
        $sTextTemplate = trim($sTextTemplate);
        if (empty($sHTMLTemplate)) {
            $sHTMLTemplate = null;
        }
        if (empty($sTextTemplate)) {
            $sTextTemplate = null;
        }
        $this->sHTMLTemplate = $sHTMLTemplate;
        $this->sTextTemplate = $sTextTemplate;
    }

    /**
     * uses a view out of the objectviews folder.
     *
     * @param string $sSubType
     * @param string $sType
     * @param string $sHTMLTemplate - example "test/mail" for a template ".test/mail.view.php"
     * @param string $sTextTemplate
     */
    public function SetObjectTemplate($sSubType = '', $sType = 'Core', $sHTMLTemplate = null, $sTextTemplate = null)
    {
        $this->iTemplateType = self::TEMPLATETYPE_OBJECT;
        $this->aObjectTemplateData['sSubType'] = $sSubType;
        $this->aObjectTemplateData['sType'] = $sType;

        $sHTMLTemplate = trim($sHTMLTemplate);
        $sTextTemplate = trim($sTextTemplate);
        if (empty($sHTMLTemplate)) {
            $sHTMLTemplate = null;
        }
        if (empty($sTextTemplate)) {
            $sTextTemplate = null;
        }
        $this->sHTMLTemplate = $sHTMLTemplate;
        $this->sTextTemplate = $sTextTemplate;
    }

    /**
     * uses a view out of the objectviews folder.
     *
     * @param string $sSubType
     * @param string $sType
     * @param string $sHTMLTemplate - example "test/mail" for a template ".test/mail.view.php"
     * @param string $sTextTemplate
     */
    public function SetPackageObjectTemplate($sSubType = '', $sType = 'Core', $sHTMLTemplate = null, $sTextTemplate = null)
    {
        $this->SetObjectTemplate($sSubType, $sType, $sHTMLTemplate, $sTextTemplate);
        $this->iTemplateType = self::TEMPLATETYPE_PACKAGEOBJECT;
    }

    /**
     * @param array<string, mixed> $aData
     *
     * @return bool - found body
     */
    protected function prepareMailData($aData)
    {
        // remove empty recipient addresses
        foreach ($this->to as $key => $aRecipient) {
            if (empty($aRecipient[0])) {
                unset($this->to[$key]);
            }
        }
        reset($this->to);

        foreach ($this->cc as $key => $aRecipient) {
            if (empty($aRecipient[0])) {
                unset($this->cc[$key]);
            }
        }
        reset($this->cc);

        foreach ($this->bcc as $key => $aRecipient) {
            if (empty($aRecipient[0])) {
                unset($this->bcc[$key]);
            }
        }
        reset($this->bcc);

        $transformationService = $this->getTransformationService();
        // overload recipient addresses with development mail account
        foreach ($this->to as $key => $aRecipient) {
            $aRecipient[0] = $transformationService->transform($aRecipient[0]);
            $this->to[$key] = $aRecipient;
        }
        reset($this->to);

        foreach ($this->cc as $key => $aRecipient) {
            $aRecipient[0] = $transformationService->transform($aRecipient[0]);
            $this->cc[$key] = $aRecipient;
        }
        reset($this->cc);

        foreach ($this->bcc as $key => $aRecipient) {
            $aRecipient[0] = $transformationService->transform($aRecipient[0]);
            $this->bcc[$key] = $aRecipient;
        }
        reset($this->bcc);

        $this->actualSubject = $this->Subject;
        $this->SetSubject($this->getTransformationService()->transformSubject($this->Subject));

        // filter email-addresses and names
        $this->FilterEmailAddresses();

        $bFoundBody = true;
        if (!is_null($this->sHTMLTemplate)) {
            $aData['__FIELDS'] = print_r($aData, true);

            $this->Body = $this->GetHTMLBody($aData);
            if (!is_null($this->sTextTemplate)) {
                $this->AltBody = $this->GetTextBody($aData);
            }
            $this->isHTML(true);
        } elseif (!is_null($this->sTextTemplate)) {
            $aData['__FIELDS'] = print_r($aData, true);
            $this->Body = $this->GetTextBody($aData);
            $this->isHTML(false);
        } elseif (!empty($this->Body) || !empty($this->AltBody)) {
            // body was set external
        } else {
            $bFoundBody = false;
        }

        // set encoding - default is UTF-8
        $this->CharSet = CHAMELEON_EMAIL_ENCODING;

        // decode body if necessary
        if (CHAMELEON_EMAIL_ENCODING !== 'UTF-8') {
            $this->Body = utf8_decode($this->Body);
            $this->AltBody = utf8_decode($this->AltBody);
        }

        return $bFoundBody;
    }

    /**
     * processes the email templates and starts sending the email.
     *
     * @param array $aData - assoc array of variables that will be replaced in templates
     *
     * @return bool
     */
    public function Send($aData = [])
    {
        $bFoundBody = $this->prepareMailData($aData);
        $wasSent = false;
        if ($bFoundBody) {
            $mailPeerSecurity = $this->getMailPeerSecurity();
            $this->SMTPOptions['ssl']['verify_peer'] = $mailPeerSecurity->isVerifyPeer();
            $this->SMTPOptions['ssl']['verify_peer_name'] = $mailPeerSecurity->isVerifyPeerName();
            $this->SMTPOptions['ssl']['allow_self_signed'] = $mailPeerSecurity->isAllowSelfSigned();
            $this->SetSMTPLogin();
            $wasSent = parent::send();
            if (false == $wasSent) {
                TTools::WriteLogEntry('Error sending mail "'.$this->Subject.'" to ['.print_r($this->to, true).']:'.print_r($this->ErrorInfo, true), 1, __FILE__, __LINE__);
            }
        } else {
            TTools::WriteLogEntry('Error sending mail "'.$this->Subject.'" to ['.print_r($this->to, true).']: no body found', 1, __FILE__, __LINE__);
        }
        if (defined('CMS_PREFIX_ALL_MAIL_SUBJECTS') && false !== CMS_PREFIX_ALL_MAIL_SUBJECTS) {
            $this->Subject = $this->actualSubject;
        }

        return $wasSent;
    }

    /**
     * filters email addresses (sender and receiver)
     * uses utf8_decode always for emails and if necessary for sender name.
     */
    protected function FilterEmailAddresses()
    {
        $this->From = utf8_decode($this->From);
        $this->FromName = $this->FilterLineFeeds($this->FromName);

        $this->filterAddressBag($this->to);
        $this->filterAddressBag($this->cc);
        $this->filterAddressBag($this->bcc);
    }

    protected function filterAddressBag(array $addressBag)
    {
        foreach ($addressBag as $key => $aRecipient) {
            $aRecipient[0] = utf8_decode($aRecipient[0]);
            if (CHAMELEON_EMAIL_ENCODING !== 'UTF-8') {
                $aRecipient[1] = utf8_decode($aRecipient[1]);
            }
            if (!empty($aRecipient[1])) {
                $this->FilterLineFeeds($aRecipient[1]);
            }
            $addressBag[$key] = $aRecipient;
        }
        reset($addressBag);
    }

    /**
     * sets mailer to smtp mode
     * uses smtp data from cms config if available or custom data.
     *
     * @param string $host
     * @param string $login
     * @param string $password
     * @param int $port
     */
    protected function SetSMTPLogin($host = '', $login = '', $password = '', $port = 25)
    {
        if (empty($host)) {
            $host = $this->smtpHost;
            $port = $this->smtpPort;
            $login = $this->smtpUser;
            $password = $this->smtpPassword;
        }
        if (!empty($host)) {
            $this->Host = $host;
            $this->Username = $login;
            $this->Password = $password;
            $this->Port = $port;
            if (!empty($this->Username)) {
                $this->SMTPAuth = true;
            } else {
                $this->SMTPAuth = false;
            }
            $this->isSMTP();
        }
    }

    protected function GetHTMLBody($aData)
    {
        $oTemplateParser = new TViewParser();
        $oTemplateParser->AddVarArray($aData);
        $sContent = '';
        if (self::TEMPLATETYPE_MODULE == $this->iTemplateType) {
            $sContent = $oTemplateParser->Render($this->sModuleName, $this->sHTMLTemplate);
        } elseif (self::TEMPLATETYPE_OBJECT == $this->iTemplateType) {
            $sContent = $oTemplateParser->RenderObjectView($this->sHTMLTemplate, $this->aObjectTemplateData['sSubType'], $this->aObjectTemplateData['sType']);
        } else {
            $sContent = $oTemplateParser->RenderObjectPackageView($this->sHTMLTemplate, $this->aObjectTemplateData['sSubType'], $this->aObjectTemplateData['sType']);
        }

        return $sContent;
    }

    protected function GetTextBody($aData)
    {
        $oTemplateParser = new TViewParser();
        $oTemplateParser->AddVarArray($aData);
        $sContent = '';
        if (self::TEMPLATETYPE_MODULE == $this->iTemplateType) {
            $sContent = $oTemplateParser->Render($this->sModuleName, $this->sTextTemplate);
        } elseif (self::TEMPLATETYPE_OBJECT == $this->iTemplateType) {
            $sContent = $oTemplateParser->RenderObjectView($this->sTextTemplate, $this->aObjectTemplateData['sSubType'], $this->aObjectTemplateData['sType']);
        } else {
            $sContent = $oTemplateParser->RenderObjectPackageView($this->sTextTemplate, $this->aObjectTemplateData['sSubType'], $this->aObjectTemplateData['sType']);
        }

        return $sContent;
    }

    /**
     * Set where the mail comes from - also sets replyto.
     *
     * @param string $sMail - mail address
     * @param string $sName - mail name
     */
    public function SetFromData($sMail, $sName = '')
    {
        $this->From = $sMail;
        $this->FromName = $sName;

        $this->ReplyTo = [];
        $this->addReplyTo($sMail, $sName);
    }

    /**
     * filter line feeds to protect mail from injected headers.
     *
     * @param string $sValue
     *
     * @return string
     */
    protected function FilterLineFeeds($sValue)
    {
        $sValue = str_replace("\r\n", ';', $sValue);
        $sValue = str_replace("\n", ';', $sValue);

        return $sValue;
    }

    public function setTransformationService(TransformOutgoingMailTargetsServiceInterface $transformationService)
    {
        $this->transformationService = $transformationService;
    }

    /**
     * @return TransformOutgoingMailTargetsServiceInterface
     */
    private function getTransformationService()
    {
        if (null !== $this->transformationService) {
            return $this->transformationService;
        }

        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.mail_target_transformation_service');
    }

    /**
     * @return HttpsContextInterface
     */
    private function getMailPeerSecurity()
    {
        if (null !== $this->mailPeerSecurity) {
            return $this->mailPeerSecurity;
        }

        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.security.mail.mail_peer_security');
    }

    public function setMailPeerSecurity(HttpsContextInterface $mailPeerSecurity)
    {
        $this->mailPeerSecurity = $mailPeerSecurity;
    }

    /**
     * @param string $smtpHost
     */
    public function setSmtpHost($smtpHost)
    {
        $port = 25;
        if (false !== ($portPosition = \mb_strrpos($smtpHost, ':'))) {
            $portInHost = \mb_substr($smtpHost, $portPosition + 1);
            if (true === \is_numeric($portInHost)) {
                $port = (int) $portInHost;
                $smtpHost = \mb_substr($smtpHost, 0, $portPosition);
            }
        }
        $this->smtpHost = $smtpHost;
        $this->smtpPort = $port;
    }

    /**
     * @param string $smtpUser
     */
    public function setSmtpUser($smtpUser)
    {
        $this->smtpUser = $smtpUser;
    }

    /**
     * @param string $smtpPassword
     */
    public function setSmtpPassword($smtpPassword)
    {
        $this->smtpPassword = $smtpPassword;
    }
}
