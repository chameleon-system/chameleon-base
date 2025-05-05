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

class TPkgRunFrontendAction extends TPkgRunFrontendActionAutoParent
{
    public const TIMEOUT_IN_SECONDS = 60;

    public const URL_IDENTIFIER = '__fronted_action__';

    public function runAction()
    {
        $aParams = $this->getParameter();
        $this->AllowEditByAll(true);
        $this->Delete();
        $oMessageManager = TCMSMessageManager::GetInstance();
        if ($oMessageManager->ConsumeMessages(TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER)) {
            $oMessageManager->ConsumeMessages(TCMSTableEditorManager::MESSAGE_MANAGER_CONSUMER);
        }
        $oAction = new $this->fieldClass(); /* @var $oAction IPkgRunFrontendAction* */

        return $oAction->runAction($aParams);
    }

    /**
     * @return array
     */
    protected function getParameter()
    {
        $aParameter = [];
        if (!empty($this->fieldParameter)) {
            $aParameter = TTools::mb_safe_unserialize($this->fieldParameter);
        }

        return $aParameter;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->fieldExpireDate > date('Y-m-d H:i:s');
    }

    /**
     * @param string $sClass
     * @param string $sPortalId
     * @param array $aParameter
     * @param string $sLanguageId
     *
     * @return TdbPkgRunFrontendAction|null
     */
    public static function CreateAction($sClass, $sPortalId = null, $aParameter = null, $sLanguageId = null)
    {
        $oAction = null;
        $logger = ServiceLocator::get('logger');
        if (!class_exists($sClass)) {
            $logger->warning(sprintf('Class %s doesn\'t exist.', $sClass));
        } else {
            $aData = ['class' => $sClass];
            $aData['expire_date'] = date('Y-m-d H:i:s', time() + TdbPkgRunFrontendAction::TIMEOUT_IN_SECONDS);
            $aData['random_key'] = TTools::GenerateRandomPassword(200);
            if (null !== $sPortalId) {
                $aData['cms_portal_id'] = $sPortalId;
            } else {
                $oPortal = self::getStaticPortalDomainService()->getActivePortal();
                if ($oPortal) {
                    $aData['cms_portal_id'] = $oPortal->id;
                }
            }
            if (null === $aParameter || !is_array($aParameter)) {
                $aParameter = [];
            }
            $aData['parameter'] = TTools::mb_safe_serialize($aParameter);
            if (null === $sLanguageId) {
                $sLanguageId = self::getLanguageService()->getActiveLanguageId();
            }
            $aData['cms_language_id'] = $sLanguageId;
            $oAction = TdbPkgRunFrontendAction::GetNewInstance($aData);
            $oAction->onCreateActionHook($aData);
            $oAction->Save();
        }

        return $oAction;
    }

    /**
     * @param array $aData
     */
    public function onCreateActionHook($aData)
    {
    }

    /**
     * @param bool|null $forceSecure
     *
     * @return string
     */
    public function getUrlToRunAction($forceSecure = null)
    {
        $portal = $this->GetFieldCmsPortal();
        if (null === $portal) {
            return '';
        }
        if ('' === $this->fieldCmsLanguageId) {
            $language = null;
        } else {
            $language = self::getLanguageService()->getLanguage($this->fieldCmsLanguageId);
        }
        if (null === $forceSecure) {
            $forceSecure = false;
        }
        $sUrl = self::getPageService()->getLinkToPortalHomePageAbsolute([], $portal, $language, $forceSecure);
        if ('/' !== substr($sUrl, -1)) {
            $sUrl .= '/';
        }
        $sUrl .= TdbPkgRunFrontendAction::URL_IDENTIFIER.$this->fieldRandomKey;

        return $sUrl;
    }

    private static function getStaticPortalDomainService(): PortalDomainServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }
}
