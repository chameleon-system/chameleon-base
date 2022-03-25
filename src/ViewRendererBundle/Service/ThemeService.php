<?php
/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ViewRendererBundle\Service;

use ChameleonSystem\CoreBundle\Service\CmsConfigDataAccessInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\ViewRenderer\Interfaces\ThemeServiceInterface;

class ThemeService implements ThemeServiceInterface
{
    private PortalDomainServiceInterface $portalDomainService;
    private CmsConfigDataAccessInterface $cmsConfigDataAccess;

    private ?\TdbPkgCmsTheme $themeOverride = null;

    public function __construct(
        PortalDomainServiceInterface $portalDomainService,
        CmsConfigDataAccessInterface $cmsConfigDataAccess
    ) {
        $this->portalDomainService = $portalDomainService;
        $this->cmsConfigDataAccess = $cmsConfigDataAccess;
    }

    /**
     * {@inheritDoc}
     */
    public function getTheme(?\TdbCmsPortal $portal): ?\TdbPkgCmsTheme
    {
        if (null !== $this->themeOverride) {
            return $this->themeOverride;
        }

        if (null === $portal) {
            $portal = $this->portalDomainService->getActivePortal();
        }
        if (null === $portal) {
            $theme = $this->cmsConfigDataAccess->getBackendTheme();
        } else {
            $theme = $portal->GetFieldPkgCmsTheme();
        }

        return $theme;
    }

    /**
     * {@inheritDoc}
     */
    public function setOverrideTheme(?\TdbPkgCmsTheme $themeOverride): void
    {
        $this->themeOverride = $themeOverride;
    }
}
