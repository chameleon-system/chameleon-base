<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Util;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Routing\Exception\RouteNotFoundException;

class PageServiceUtil implements PageServiceUtilInterface
{
    /**
     * @var UrlUtil
     */
    private $urlUtil;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var bool
     */
    private $removeTrailingSlash;

    /**
     * @param bool $removeTrailingSlash
     */
    public function __construct(UrlUtil $urlUtil, ContainerInterface $container, $removeTrailingSlash)
    {
        $this->urlUtil = $urlUtil;
        $this->container = $container; // avoid circular dependency on RoutingUtil
        $this->removeTrailingSlash = $removeTrailingSlash;
    }

    /**
     * {@inheritdoc}
     */
    public function getPagePath(\TdbCmsTplPage $page, \TdbCmsLanguage $language)
    {
        $portal = $page->GetPortal();
        $routes = $this->getRoutingUtil()->getAllPageRoutes($portal, $language);
        if (false === isset($routes[$page->id])) {
            throw new RouteNotFoundException(sprintf('No route found for page with ID %s.', $page->id));
        }

        $pagePath = $routes[$page->id]->getPrimaryPath();
        if ('' === $pagePath || '/' === $pagePath) {
            return '';
        }
        /*
         * The routes are (depending on configuration) stored with a trailing slash, but as the Symfony router will
         * interpret the result of this method as a simple parameter and will add another slash, we need to remove it
         * it here to avoid doubling.
         */
        $pagePath = rtrim($pagePath, '/');

        if (false === $portal->fieldUseSlashInSeoUrls) {
            $pagePath .= '.html';
        }

        return $pagePath;
    }

    /**
     * {@inheritdoc}
     */
    public function postProcessUrl($url, \TdbCmsPortal $portal, \TdbCmsLanguage $language, $forceSecure)
    {
        if (true === $forceSecure) {
            $url = $this->getSecureUrlIfNeeded($url, $portal, $language);
        }

        return $this->handleTrailingSlash($url, $portal);
    }

    /**
     * Symfony currently does not allow to enforce generation of secure URLs (a secure URL will only be generated if the
     * route requires HTTPS or if the current request is secure), therefore we turn the URL secure manually.
     *
     * @param string $url
     *
     * @return string
     */
    private function getSecureUrlIfNeeded($url, ?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null)
    {
        if (false === $this->urlUtil->isUrlSecure($url)) {
            $url = $this->urlUtil->getAbsoluteUrl($url, true, null, $portal, $language);
        }

        return $url;
    }

    /**
     * @param string $url
     *
     * @return string
     */
    private function handleTrailingSlash($url, \TdbCmsPortal $portal)
    {
        $urlParts = explode('?', $url);
        $path = $urlParts[0];
        $path = rtrim($path, '/');
        if (false === $this->removeTrailingSlash && true === $portal->fieldUseSlashInSeoUrls) {
            $path .= '/';
        }
        if ('' === $path) {
            $path = '/';
        }

        if (count($urlParts) > 1) {
            return $path.'?'.$urlParts[1];
        }

        return $path;
    }

    /**
     * @return RoutingUtilInterface
     */
    private function getRoutingUtil()
    {
        return $this->container->get('chameleon_system_core.util.routing');
    }
}
