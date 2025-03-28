<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Interfaces\FlashMessageServiceInterface;
use ChameleonSystem\CoreBundle\Security\Password\PasswordHashGeneratorInterface;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * {@inheritdoc}
 */
class TCMSFieldPassword extends TCMSFieldVarchar
{
    public const DEFAULT_MINIMUM_PASSWORD_LENGTH = 6;

    private ?string $backendLanguageCode = null;

    /**
     * {@inheritdoc}
     */
    public function GetHTML()
    {
        parent::GetHTML();

        $value = $this->_GetHTMLValue();
        if (!empty($value)) {
            $passwordMessage = 'chameleon_system_core.field_password.has_password';
            $messageClass = 'alert alert-info';
            $iconClass = 'fas fa-check-circle text-success';
        } else {
            $passwordMessage = 'chameleon_system_core.field_password.has_no_password';
            $messageClass = 'alert alert-danger';
            $iconClass = 'fas fa-times-circle text-danger';
        }

        $viewRenderer = $this->getViewRenderer();
        $viewRenderer->AddSourceObject('passwordMessage', $passwordMessage);
        $viewRenderer->AddSourceObject('messageClass', $messageClass);
        $viewRenderer->AddSourceObject('iconClass', $iconClass);
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

        $includes = [];
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

    private function getMappedLanguageCodeForStrengthMeterPlugin(): ?string
    {
        $backendLanguageIsoCode = $this->getBackendLanguageCode();
        if (null === $backendLanguageIsoCode) {
            return null;
        }

        $backendLanguageIsoCode = strtolower($backendLanguageIsoCode);

        $languageMapping = [
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
        ];

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
        if (false === parent::DataIsValid()) {
            return false;
        }

        if ('' === trim($this->data)) {
            return true;
        }

        if (false === $this->isRepeatedPasswordValid()) {
            return false;
        }

        if (false === $this->isPasswordLengthValid()) {
            return false;
        }

        return true;
    }

    private function isRepeatedPasswordValid(): bool
    {
        $checkFieldName = $this->name.'_check';

        if (true === isset($this->oTableRow->sqlData[$checkFieldName])
            && $this->data === $this->oTableRow->sqlData[$checkFieldName]) {
            return true;
        }

        $this->addBackendErrorMessage('TABLEEDITOR_FIELD_PASSWORD_CHECK_NOT_VALID');

        return false;
    }

    private function isPasswordLengthValid(): bool
    {
        $passwordLength = \mb_strlen($this->data);
        $minimumLength = $this->getMinimumLength();
        if ($passwordLength < $minimumLength) {
            $this->addBackendErrorMessage('TABLEEDITOR_FIELD_PASSWORD_TOO_SHORT', [
                'min' => $minimumLength,
            ]);

            return false;
        }
        $maximumLength = $this->getMaximumLength();
        if ($passwordLength > $maximumLength) {
            $this->addBackendErrorMessage('TABLEEDITOR_FIELD_PASSWORD_TOO_LONG', [
                'max' => $maximumLength,
            ]);

            return false;
        }

        return true;
    }

    private function getMinimumLength(): int
    {
        $minimumLength = $this->oDefinition->GetFieldtypeConfigKey('minimumLength');
        if (false === \is_numeric($minimumLength)) {
            return self::DEFAULT_MINIMUM_PASSWORD_LENGTH;
        }

        return (int) $minimumLength;
    }

    private function getMaximumLength(): int
    {
        return PasswordHashGeneratorInterface::MAXIMUM_PASSWORD_LENGTH;
    }

    private function addBackendErrorMessage(string $messageKey, array $additionalVariables = []): void
    {
        $variables = \array_merge([
            'sFieldName' => $this->name,
            'sFieldTitle' => $this->oDefinition->GetName(),
        ], $additionalVariables);

        $flashMessageService = $this->getFlashMessageService();
        $flashMessageService->addMessage(
            TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER,
            $messageKey,
            $variables
        );
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
     */
    private function getBackendLanguageCode(): ?string
    {
        if (null !== $this->backendLanguageCode) {
            return $this->backendLanguageCode;
        }

        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);
        $languageId = $securityHelper->getUser()?->getCmsLanguageId();
        if (null === $languageId) {
            return null;
        }
        /** @var LanguageServiceInterface $languageService */
        $languageService = ServiceLocator::get('chameleon_system_core.language_service');
        $this->backendLanguageCode = $languageService->getLanguageIsoCode($languageId);

        return $this->backendLanguageCode;
    }

    private function getFlashMessageService(): FlashMessageServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.flash_messages');
    }

    private function getViewRenderer(): ViewRenderer
    {
        return ServiceLocator::get('chameleon_system_view_renderer.view_renderer');
    }

    private function getTranslator(): TranslatorInterface
    {
        return ServiceLocator::get('translator');
    }
}
