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
use Symfony\Contracts\Translation\TranslatorInterface;

class MTFeedbackCoreEndPoint extends TUserCustomModelBase
{
    /**
     * holds the record from the module_single_dynamic_page.
     *
     * @var TdbModuleFeedback
     */
    protected $_oTableRow;

    /**
     * all input errors get placed here.
     *
     * @var MTFeedbackErrorsCore
     */
    protected $_oErrors;

    public function __construct()
    {
        parent::__construct();

        $this->bAllowHTMLDivWrapping = true;
        $this->_oErrors = new MTFeedbackErrorsCore();
    }

    public function Execute()
    {
        $this->LoadTableRow();
        $this->data = parent::Execute();
        $this->SetDefaultInputParameters();
        $this->_GetUserData();
        $this->data['oTableRow'] = $this->_oTableRow;
        $this->data['oError'] = $this->_oErrors;
        if ($this->global->GetUserData('success')) {
            $this->RedirectOnSuccess();
        }

        return $this->data;
    }

    protected function RedirectOnSuccess()
    {
        $this->SetTemplate('MTFeedback', 'inc/thankyou');
    }

    public function SetDefaultInputParameters()
    {
        $this->data['aInput'] = [];
        // set defaults
        $this->data['aInput']['name'] = '';
        $this->data['aInput']['email'] = '';
        $this->data['aInput']['subject'] = $this->_oTableRow->sqlData['default_subject'];
        $this->data['aInput']['body'] = $this->_oTableRow->sqlData['default_body'];
    }

    /**
     * {@inheritdoc}
     */
    protected function DefineInterface()
    {
        parent::DefineInterface();
        $externalFunctions = ['SendEMail'];
        $this->methodCallAllowed = array_merge($this->methodCallAllowed, $externalFunctions);
    }

    /**
     * this method is called via the form.
     */
    public function SendEMail()
    {
        $this->sendEmailExecute();
    }

    /**
     * finally sends the emails.
     */
    protected function sendEmailExecute()
    {
        $this->LoadTableRow();
        $this->_GetUserData();
        // check required fields
        $this->_ValidateFields();

        if (true === $this->_oErrors->HasErrors()) {
            return;
        }

        $mailBody = $this->_GetMailBody();

        $mailer = $this->getMailer();
        $mailer->AddReplyTo($this->_GetUserEMail(), $this->_GetUserName());
        $mailer->From = $this->getFromAddress();
        $mailer->FromName = $this->_GetUserName();
        $mailer->AddAddress($this->GetEMailToAddress());

        $bccArray = $this->GetBCCArray();
        if (!empty($bccArray) && is_array($bccArray)) {
            foreach ($bccArray as $address) {
                $address = trim($address);
                if (!empty($address)) {
                    $mailer->AddBCC($address);
                }
            }
        }

        if (false === $this->AllowSendingEMail()) {
            return;
        }

        $mailer->isHTML(false);

        $sSubject = $this->GetSubject();
        $mailer->SetSubject($sSubject);
        $mailer->Body = $mailBody;

        $csvFile = $this->GetCSVFileData();
        if (false !== $csvFile) {
            $mailer->addStringAttachment($csvFile, $this->GetCSVFilenName());
        }
        $this->AddAttachments($mailer);

        if (false === $mailer->Send()) {
            $translator = $this->getTranslationService();
            $this->_oErrors->AddError('mail', $translator->trans('chameleon_system_core.module_feedback.mailer_error'));

            return;
        }

        $this->AfterSendEMailSuccess();
        $this->redirectToSuccessPage();
    }

    protected function redirectToSuccessPage()
    {
        $parameters = $this->GetRedirectToSuccessPageParameters();
        $activePage = $this->getActivePageService()->getActivePage();
        $redirectUrl = str_replace('&amp;', '&', $activePage->GetRealURLPlain($parameters));
        $this->getRedirect()->redirect($redirectUrl);
    }

    /**
     * returns from address, which should be a no-reply address of the used SMTP host
     * the real sender email address is set as reply address.
     *
     * @return string
     */
    protected function getFromAddress()
    {
        $fromAddress = $this->_oTableRow->fieldFromEmail;
        if ('' === $fromAddress) {
            $fromAddress = $this->GetEMailToAddress();
        }

        return $fromAddress;
    }

    /**
     * add attachments to the mail.
     *
     * @param PHPMailer $oMail
     */
    protected function AddAttachments($oMail)
    {
    }

    /**
     * return array for the header redirect after success.
     *
     * @return array
     */
    protected function GetRedirectToSuccessPageParameters()
    {
        return ['success' => '1'];
    }

    /**
     * return email address.
     *
     * @return string
     */
    protected function GetEMailToAddress()
    {
        return $this->_oTableRow->sqlData['to_email'];
    }

    /**
     * @return array
     */
    protected function GetBCCArray()
    {
        $bccString = $this->_oTableRow->sqlData['bcc_email'];
        $bccString = str_replace(',', ';', $bccString);
        $bccString = str_replace(' ', ';', $bccString);
        $bccString = str_replace("\r\n", ';', $bccString);
        $bccString = str_replace("\n", ';', $bccString);

        return explode(';', $bccString);
    }

    /**
     * @return bool|string
     */
    public function GetCSVFileData()
    {
        return false;
    }

    /**
     * @return string
     */
    public function GetCSVFilenName()
    {
        return 'submitted_form_data.txt';
    }

    /**
     * @return bool
     */
    protected function AllowSendingEMail()
    {
        return true;
    }

    /**
     * returns the subject of the email.
     *
     * @return string
     */
    protected function GetSubject()
    {
        return $this->data['aInput']['subject'];
    }

    /**
     * hook to do things after the email was send successfully (e.g. save data to db).
     */
    public function AfterSendEMailSuccess()
    {
    }

    /**
     * returns the name of the sender (from form or fixed value).
     *
     * @return string
     */
    public function _GetUserName()
    {
        return $this->data['aInput']['name'];
    }

    /**
     * returns the senders email address (from form or fixed value).
     *
     * @return string
     */
    public function _GetUserEMail()
    {
        return $this->data['aInput']['email'];
    }

    public function _GetUserData()
    {
        $this->data['aInput']['name'] = $this->global->GetUserData('name');
        $this->data['aInput']['email'] = $this->global->GetUserData('email');
        $this->data['aInput']['subject'] = $this->global->GetUserData('subject');
        $this->data['aInput']['body'] = $this->global->GetUserData('body');
    }

    /**
     * validates the input fields and sets field errors.
     */
    public function _ValidateFields()
    {
        if (empty($this->data['aInput']['name'])) {
            $this->_oErrors->AddError('name', 'required');
        }
        if (empty($this->data['aInput']['email'])) {
            $this->_oErrors->AddError('email', 'required');
        }
        if (empty($this->data['aInput']['subject'])) {
            $this->_oErrors->AddError('subject', 'required');
        }
        if (empty($this->data['aInput']['body'])) {
            $this->_oErrors->AddError('body', 'required');
        }
    }

    /**
     * returns the mailbody that will be send.
     *
     * @return string
     */
    public function _GetMailBody()
    {
        $mailBody = "Kundenkontakt:\n".'Name:    '.$this->data['aInput']['name']."\n".'E-Mail:  '.$this->data['aInput']['email']."\n".'Betreff: '.$this->data['aInput']['subject']."\n\nNachricht:\n".$this->data['aInput']['body'];

        return $mailBody;
    }

    /**
     * loads the feedback module config table (module_feedback).
     */
    public function LoadTableRow()
    {
        if (null !== $this->_oTableRow) {
            return;
        }

        $oModuleFeedback = TdbModuleFeedback::GetNewInstance();
        $oModuleFeedback->LoadFromField('cms_tpl_module_instance_id', $this->instanceID);
        $this->_oTableRow = $oModuleFeedback;
    }

    /**
     * {@inheritdoc}
     */
    public function GetHtmlHeadIncludes()
    {
        $aIncludes = parent::GetHtmlHeadIncludes();

        // load optional custm css from /chameleon/web_modules/ directory
        if (file_exists(PATH_USER_CMS_PUBLIC.'/web_modules/MTFeedbackCore/css/MTFeedbackCore.css')) {
            $aIncludes[] = '<link href="'.TGlobal::GetStaticURL(
                '/chameleon/web_modules/MTFeedbackCore/css/MTFeedbackCore.css'
            ).'" rel="stylesheet" type="text/css" media="screen" />';
        }

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
     * @return TCMSMail
     */
    private function getMailer()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.mailer');
    }

    /**
     * @return ICmsCoreRedirect
     */
    private function getRedirect()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.redirect');
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslationService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
    }
}
