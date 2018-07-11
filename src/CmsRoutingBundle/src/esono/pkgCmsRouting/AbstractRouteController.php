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
    /** @var PortalDomainServiceInterface $portalDomainService */
    protected $portalDomainService;
    /** @var LanguageServiceInterface $languageService */
    protected $languageService;
    /**
     * @var UrlPrefixGeneratorInterface
     */
    protected $urlPrefixGenerator;
    /**
     * @var UrlUtil
     */
    protected $urlUtil;
    /**
     * @var RoutingUtilInterface
     */
    protected $routingUtil;

    /**
     * @param PortalDomainServiceInterface $portalDomainService
     * @param LanguageServiceInterface     $languageService
     * @param UrlPrefixGeneratorInterface  $urlPrefixGenerator
     * @param UrlUtil                      $urlUtil
     * @param RoutingUtilInterface         $routingUtil
     */
    public function __construct(PortalDomainServiceInterface $portalDomainService, LanguageServiceInterface $languageService, UrlPrefixGeneratorInterface $urlPrefixGenerator, UrlUtil $urlUtil, RoutingUtilInterface $routingUtil)
    {
        $this->portalDomainService = $portalDomainService;
        $this->languageService = $languageService;
        $this->urlPrefixGenerator = $urlPrefixGenerator;
        $this->urlUtil = $urlUtil;
        $this->routingUtil = $routingUtil;
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
        } else {
            return false;
        }
    }
}
