<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CoreBundle\Security\AuthenticityToken\AuthenticityTokenManagerInterface;
use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;

class TCmsLanguage extends TCmsLanguageAutoParent
{
    /**
     * Return translated page URL.
     *
     * @return string
     */
    public function GetTranslatedPageURL()
    {
        $activePageService = $this->getActivePageService();

        return $activePageService->getLinkToActivePageAbsolute([
        ], [
            'module_fnc' => 'pkgLanguage',
            'l',
            AuthenticityTokenManagerInterface::TOKEN_ID,
        ],
            $this);
    }

    /**
     * a lot of objects use the language info stored in TCMSSmartURLData - to have these objects use
     * the current language instead, we temporarily change the data in TCMSSmartURLData.
     *
     * @param bool $bStart
     */
    public function TargetLanguageSimulation($bStart)
    {
        static $originalLanguageId = null;
        $languageService = self::getLanguageService();
        if (true === $bStart) {
            $originalLanguage = $languageService->getActiveLanguage();
            if (null !== $originalLanguage) {
                $originalLanguageId = $originalLanguage->id;
            }
            $languageService->setActiveLanguage($this->id);
        } else {
            $languageService->setActiveLanguage($originalLanguageId);
        }
    }

    /**
     * @return ActivePageServiceInterface
     */
    private function getActivePageService()
    {
        return ServiceLocator::get('chameleon_system_core.active_page_service');
    }
}
