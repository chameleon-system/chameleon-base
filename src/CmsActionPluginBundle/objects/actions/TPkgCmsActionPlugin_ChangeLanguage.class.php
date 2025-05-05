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

class TPkgCmsActionPlugin_ChangeLanguage extends AbstractPkgActionPlugin
{
    /**
     * @return void
     */
    public function changeLanguage(array $data)
    {
        $languageIso = isset($data['l']) ? $data['l'] : '';
        if (empty($languageIso)) {
            return;
        }

        $newLanguage = $this->getLanguageService()->getLanguageFromIsoCode($languageIso);
        $url = $newLanguage->GetTranslatedPageURL();

        $this->getRedirect()->redirect($url);
    }

    /**
     * @return LanguageServiceInterface
     */
    private function getLanguageService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.language_service');
    }

    /**
     * @return cmsCoreRedirect
     */
    private function getRedirect()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.redirect');
    }
}
