<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;

class TPkgCmsNavigation_LanguageSelection extends AbstractViewMapper
{
    /**
     * {@inheritdoc}
     */
    public function GetRequirements(IMapperRequirementsRestricted $oRequirements): void
    {
        $oRequirements->NeedsSourceObject('aTree', 'array', []);
    }

    /**
     * {@inheritdoc}
     */
    public function Accept(IMapperVisitorRestricted $oVisitor, $bCachingEnabled, IMapperCacheTriggerRestricted $oCacheTriggerManager): void
    {
        $aLanguageList = $this->getLanguageList();
        if (count($aLanguageList) < 2) {
            return;
        }
        /*
         * disable if front end translation is disabled.
         */
        if (false === ACTIVE_TRANSLATION) {
            return;
        }

        $aTree = $oVisitor->GetSourceObject('aTree');

        $oActiveLanguage = $this->getLanguageService()->getActiveLanguage();
        if ($oActiveLanguage && $bCachingEnabled) {
            $oCacheTriggerManager->addTrigger($oActiveLanguage->table, $oActiveLanguage->id);
        }

        // change language is part of the page meta module...
        $aChangeLanguageParameter = [
            'module_fnc' => ['pkgLanguage' => 'changeLanguage'],
            'l' => strtolower($oActiveLanguage->fieldIso6391),
        ];

        $oNode = new TPkgCmsNavigationNode();
        $oNode->sLink = '#';
        $oNode->sTitle = $oActiveLanguage->fieldName;
        $oNode->sSeoTitle = $oActiveLanguage->fieldName;
        $oNode->sNavigationIconClass = 'i-flag_'.TTools::GetActiveLanguageIsoName();
        $oNode->sRel = 'nofollow';

        $aChildren = [];
        foreach ($aLanguageList as $sLangIso => $sLangName) {
            if ($sLangIso === $oActiveLanguage->fieldIso6391) {
                continue;
            }
            $aChangeLanguageParameter['l'] = $sLangIso;
            $oChildNode = new TPkgCmsNavigationNode();
            $oChildNode->sLink = '?'.TTools::GetArrayAsURL($aChangeLanguageParameter);
            $oChildNode->sTitle = $sLangName;
            $oChildNode->sSeoTitle = $sLangName;
            $oChildNode->sNavigationIconClass = 'i-flag_'.$sLangIso;
            $oChildNode->sRel = 'nofollow';
            if ($sLangIso === $oActiveLanguage->fieldIso6391) {
                $oChildNode->setIsActive(true);
            }
            $aChildren[] = $oChildNode;
        }
        $oNode->setChildren($aChildren);
        $aTree[] = $oNode;

        $oVisitor->SetMappedValue('aTree', $aTree);
    }

    /**
     * @return mixed[]
     */
    private function getLanguageList()
    {
        $aLanguageList = [];
        $oActivePortal = $this->getPortalDomainService()->getActivePortal();
        if (method_exists($oActivePortal, 'GetFieldBasedTranslationLanguageArray')) {
            $aLanguageList = $oActivePortal->GetFieldBasedTranslationLanguageArray();
            $oPortalLanguage = $oActivePortal->GetFieldCmsLanguage();
            if (!is_null($oPortalLanguage)) {
                $aLanguageList[$oPortalLanguage->fieldIso6391] = $oPortalLanguage->fieldName;
            }
        } else {
            $oCMSConfig = TdbCmsConfig::GetInstance();
            if (method_exists($oCMSConfig, 'GetFieldTranslationBaseLanguage')) {
                $oDefaultLanguage = $oCMSConfig->GetFieldTranslationBaseLanguage();
                $aLanguageList = $oCMSConfig->GetFieldBasedTranslationLanguageArray();
                $aLanguageList[$oDefaultLanguage->fieldIso6391] = $oDefaultLanguage->fieldName;
            }
        }
        ksort($aLanguageList);

        return $aLanguageList;
    }

    /**
     * @return LanguageServiceInterface
     */
    private function getLanguageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.language_service');
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private function getPortalDomainService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }
}
