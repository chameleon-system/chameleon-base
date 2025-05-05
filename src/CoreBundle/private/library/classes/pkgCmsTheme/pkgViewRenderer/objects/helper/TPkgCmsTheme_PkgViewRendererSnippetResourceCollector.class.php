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

class TPkgCmsTheme_PkgViewRendererSnippetResourceCollector extends TPkgCmsTheme_PkgViewRendererSnippetResourceCollectorAutoParent
{
    /**
     * @param TdbCmsPortal $oPortal
     *
     * @return array
     */
    protected function getAdditionalLessResources($oPortal = null)
    {
        $aLayoutLess = [];
        if (null === $oPortal) {
            $oPortal = $this->getPortalDomainService()->getActivePortal();
        }
        if (null !== $oPortal && !empty($oPortal->fieldPkgCmsThemeId)) {
            $sQuery = "SELECT `pkg_cms_theme_block_layout`.* FROM `pkg_cms_theme_block_layout`
                        WHERE `pkg_cms_theme_block_layout`.`less_file` != ''";
            $oLayoutList = TdbPkgCmsThemeBlockLayoutList::GetList($sQuery);
            while ($oLayout = $oLayoutList->Next()) {
                $aLayoutLess[$oLayout->fieldLessFile] = true;
            }
        }

        return $aLayoutLess;
    }

    private function getPortalDomainService(): PortalDomainServiceInterface
    {
        return ServiceLocator::get('chameleon_system_core.portal_domain_service');
    }
}
