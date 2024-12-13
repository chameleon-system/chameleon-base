<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace esono\pkgCmsRouting;

use ChameleonSystem\CoreBundle\Util\RoutingUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlPrefixGeneratorInterface;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;

abstract class AbstractRouteController implements RouteControllerInterface
{
    public function __construct(
        protected readonly PortalDomainServiceInterface $portalDomainService,
        protected readonly LanguageServiceInterface $languageService,
        protected readonly UrlPrefixGeneratorInterface $urlPrefixGenerator,
        protected readonly UrlUtil $urlUtil,
        protected readonly RoutingUtilInterface $routingUtil)
    {
    }

    /**
     * @param string $relativeURL - url without portal/language prefix
     * @param string $prefixedURL - url that includes portal and/or language prefix
     *
     * @return bool
     */
    protected function compareRelativeAndPrefixedURL($relativeURL, $prefixedURL)
    {
        $diff = substr($prefixedURL, 0, -1 * strlen($relativeURL));

        $activePortal = $this->portalDomainService->getActivePortal();
        $activeLanguage = $this->languageService->getActiveLanguage();

        $prefix = $this->urlPrefixGenerator->generatePrefix($activePortal, $activeLanguage);

        if (mb_strtolower($diff) === mb_strtolower($prefix)) {
            return ($diff.$relativeURL) === $prefixedURL;
        }

        return false;
    }
}
