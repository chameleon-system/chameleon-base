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
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\ViewRenderer\Interfaces\ThemeServiceInterface;

class ThemeService implements ThemeServiceInterface
{
    private PortalDomainServiceInterface $portalDomainService;
    private CmsConfigDataAccessInterface $cmsConfigDataAccess;
    private RequestInfoServiceInterface $requestInfoService;

    private ?\TdbPkgCmsTheme $themeOverride = null;

    public function __construct(
        PortalDomainServiceInterface $portalDomainService,
        CmsConfigDataAccessInterface $cmsConfigDataAccess,
        RequestInfoServiceInterface $requestInfoService
    ) {
        $this->portalDomainService = $portalDomainService;
        $this->cmsConfigDataAccess = $cmsConfigDataAccess;
        $this->requestInfoService = $requestInfoService;
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

        if (null !== $portal) {
            return $portal->GetFieldPkgCmsTheme();
        }

        if (true === $this->requestInfoService->isBackendMode()) {
            return $this->cmsConfigDataAccess->getBackendTheme();
        }

        return null;
    }

    /**
     * Replace the "default" theme with a specific one.
     * As long as it is not null it will always be returned by getTheme() without any further checks.
     */
    public function setOverrideTheme(?\TdbPkgCmsTheme $themeOverride): void
    {
        $this->themeOverride = $themeOverride;
    }
}
