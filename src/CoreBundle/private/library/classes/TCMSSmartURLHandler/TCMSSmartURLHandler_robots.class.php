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

class TCMSSmartURLHandler_robots extends TCMSSmartURLHandler
{
    public function GetPageDef()
    {
        $requestInfoService = ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.request_info_service');

        $requestURI = strtolower($requestInfoService->getPathInfoWithoutPortalAndLanguagePrefix());
        if ('/robots.txt' !== $requestURI) {
            return false;
        }

        $activePortal = $this->getPortalDomainService()->getActivePortal();
        header('Content-Type: text');
        if (null !== $activePortal) {
            $robots = trim($activePortal->fieldRobots);
            echo $robots;
        }
        exit;
    }

    /**
     * @return PortalDomainServiceInterface
     */
    private function getPortalDomainService()
    {
        return ChameleonSystem\CoreBundle\ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }
}
