<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Translation\TranslatorInterface;

/**
 * {@inheritdoc}
 */
class TCMSFieldPassword extends TCMSFieldVarchar
{
    /**
     * @var null|string
     */
    private $backendLanguageCode;

    /**
     * {@inheritdoc}
     */
    public function GetHTML()
    {
        parent::GetHTML();

        $value = $this->_GetHTMLValue();
        if (!empty($value)) {
            $passwordMessage = 'chameleon_system_core.field_password.has_password';
            $sMessageClass = 'tinyNotice';
        } else {
            $passwordMessage = 'chameleon_system_core.field_password.has_no_password';
            $sMessageClass = 'tinyError';
        }

        $viewRenderer = $this->getViewRenderer();
        $viewRenderer->AddSourceObject('passwordMessage', $passwordMessage);
        $viewRenderer->AddSourceObject('messageClass', $sMessageClass);
        $viewRenderer->AddSourceObject('fieldName', $this->name);
        $viewRenderer->AddSourceObject('backendLanguageCode', $this->getBackendLanguageCode());

        return $viewRenderer->Render('TCMSFieldPassword/passwordInput.html.twig', null, false);
    }

    /**
     * {@inheritdoc}
     */
    public function ConvertPostDataToSQL()
    {
        $bGetFromDB = false;
        $returnVal = false;
        if (!empty($this->data)) {
            if (array_key_exists($this->name.'_check', $this->oTableRow->sqlData) && ($this->data == $this->oTableRow->sqlData[$this->name.'_check'])) {
                $returnVal = $this->data;
            } else {
                $returnVal = false;
            }
        } else {
            $bGetFromDB = true;
        }
        if ($bGetFromDB) {
            $sQuery = 'SELECT * FROM `'.MySqlLegacySupport::getInstance()->real_escape_string($this->oTableRow->table)."` WHERE `id` = '".MySqlLegacySupport::getInstance()->real_escape_string($this->recordId)."' ";
            $res = MySqlLegacySupport::getInstance()->query($sQuery);
            if (MySqlLegacySupport::getInstance()->num_rows($res) > 0) {
                $aTableRow = MySqlLegacySupport::getInstance()->fetch_assoc($res);
                if ('' != trim($aTableRow[$this->oDefinition->sqlData['name']])) {
                    return trim($aTableRow[$this->oDefinition->sqlData['name']]);
                }
            }
        }

        return $returnVal;
    }

    /**
     * {@inheritdoc}
     */
    public function GetCMSHtmlHeadIncludes()
    {
        $this->getBackendLanguageCode();

        $includes = array();
        $includes[] = '<link href="'.URL_CMS.'/fields/TCMSFieldPassword/strength-meter/css/strength-meter.min.css" rel="stylesheet" type="text/css" />';
        $includes[] = '<script src="'.URL_CMS.'/fields/TCMSFieldPassword/strength-meter/js/strength-meter.min.js" type="text/javascript"></script>';
        $includes[] = '<script src="'.URL_CMS.'/fields/TCMSFieldPassword/TCMSFieldPassword.js" type="text/javascript"></script>';

        $validMappedLanguageCode = $this->getMappedLanguageCodeForStrengthMeterPlugin();
        if (null !== $validMappedLanguageCode) {
            $includes[] = '<script src="'.URL_CMS.'/fields/TCMSFieldPassword/strength-meter/js/locales/strength-meter-'.TGlobal::OutHTML($validMappedLanguageCode).'.js" type="text/javascript"></script>';
        }

        $includes[] = "<script>
        $(function() {
            $('#".TGlobal::OutJS($this->name)."_check').on('blur', function() {
                checkPasswordSimilarity('".TGlobal::OutJS($this->name)."', '".TGlobal::OutJS($this->name)."_check', '".TGlobal::OutJS($this->getTranslator()->trans('chameleon_system_core.field_password.error_password_check_does_not_match'))."');
            });
        });
        </script>";

        return $includes;
    }

    /**
     * @return null|string
     */
    private function getMappedLanguageCodeForStrengthMeterPlugin()
    {
        $backendLanguageIsoCode = strtolower($this->getBackendLanguageCode());

        $languageMapping = array(
            'de' => 'de',
            'es' => 'es',
            'fr' => 'fr',
            'hu' => 'hu',
            'it' => 'it',
            'nl' => 'nl',
            'pl' => 'pl',
            'pt' => 'pt-BR',
            'ru' => 'ru',
            'sr' => 'sr',
            'zh' => 'zh-CN',
        );

        if (false === isset($languageMapping[$backendLanguageIsoCode])) {
            return null;
        }

        return $languageMapping[$backendLanguageIsoCode];
    }

    /**
     * {@inheritdoc}
     */
    public function GetReadOnly()
    {
        $viewRenderer = $this->getViewRenderer();
        $viewRenderer->AddSourceObject('messageType', 'warning');
        $viewRenderer->AddSourceObject('message', 'chameleon_system_core.field_password.display_not_permitted');

        return $viewRenderer->Render('alert.html.twig', null, false);
    }

    /**
     * {@inheritdoc}
     */
    public function DataIsValid()
    {
        $dataIsValid = parent::DataIsValid();

        if (false === $dataIsValid) {
            return false;
        }

        $checkFieldName = $this->name.'_check';
        $passwordIsSet = '' !== trim($this->data);
        $passwordCheckEqualsPassword = (isset($this->oTableRow->sqlData[$checkFieldName]) && $this->data === $this->oTableRow->sqlData[$checkFieldName]);

        if (true === $passwordIsSet && false === $passwordCheckEqualsPassword) {
            $dataIsValid = false;
            $oMessageManager = TCMSMessageManager::GetInstance();
            $sConsumerName = TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER;
            $sFieldTitle = $this->oDefinition->GetName();
            $oMessageManager->AddMessage($sConsumerName, 'TABLEEDITOR_FIELD_PASSWORD_CHECK_NOT_VALID', array('sFieldName' => $this->name, 'sFieldTitle' => $sFieldTitle));
        }

        return $dataIsValid;
    }

    /**
     * {@inheritdoc}
     */
    public function HasContent()
    {
        $bHasContent = false;
        if ('' === trim($this->data)) {
            $sContent = $this->ConvertPostDataToSQL();
            if ('' !== $sContent) {
                $bHasContent = true;
            }
        } else {
            $bHasContent = true;
        }

        return $bHasContent;
    }

    /**
     * returns the ISO6391 language code of the current CMS user.
     *
     * @return string
     */
    private function getBackendLanguageCode()
    {
        if (null === $this->backendLanguageCode) {
            $backendUser = TdbCmsUser::GetActiveUser();
            $backendLanguage = $backendUser->GetFieldCmsLanguage();
            $this->backendLanguageCode = $backendLanguage->fieldIso6391;
        }

        return $this->backendLanguageCode;
    }

    /**
     * @return ViewRenderer
     */
    private function getViewRenderer()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
    }

    /**
     * @return TranslatorInterface
     */
    private function getTranslator()
    {
        return \ChameleonSystem\CoreBundle\ServiceLocator::get('translator');
    }
}
