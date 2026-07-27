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
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class CmsRouteLoader extends Loader
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var UrlPrefixGeneratorInterface
     */
    private $urlPrefixGenerator;
    /**
     * @var RoutingUtilInterface
     */
    private $routingUtil;
    /**
     * @var UrlUtil
     */
    private $urlUtil;

    public function __construct(
        ContainerInterface $container,
        UrlPrefixGeneratorInterface $urlPrefixGenerator,
        RoutingUtilInterface $routingUtil,
        UrlUtil $urlUtil
    ) {
        $this->container = $container;
        $this->urlPrefixGenerator = $urlPrefixGenerator;
        $this->routingUtil = $routingUtil;
        $this->urlUtil = $urlUtil;
    }

    /**
     * Loads a resource.
     *
     * @param mixed $resource The resource
     * @param string $type The resource type
     *
     * @return RouteCollection
     *
     * @throws \LogicException
     */
    public function load(mixed $resource, $type = null)
    {
        $collection = new RouteCollection();
        if (!is_array($resource)) {
            return $collection;
        }
        foreach ($resource as $routeConfig) {
            $portal = null;
            if (isset($routeConfig['portal_id'])) {
                $portal = \TdbCmsPortal::GetNewInstance();
                $portal->Load($routeConfig['portal_id']);
                $languageList = $portal->GetFieldCmsLanguageList();
                $languages = [];
                while ($language = $languageList->Next()) {
                    $languages[$language->id] = $language;
                }
                $defaultLanguage = $this->getDefaultPortalLanguage($portal);
                $defaultLanguageId = (null === $defaultLanguage) ? null : $defaultLanguage->id;

                foreach ($languages as $language) {
                    if ($language->id === $defaultLanguageId) {
                        continue;
                    }
                    $this->importRoutes($collection, $routeConfig, $portal, $language);
                }
                /*
                 * For the default language no language prefix will be generated. Therefore we generate the default
                 * language routes last for the following reasons:
                 * - route matching is slightly more performant because large "blocks" of routes starting with language
                 *   prefixes can be skipped with few checks.
                 * - routes can be defined more deliberately, as "match-all" routes can be matched in a language-specific
                 *   way. This is currently needed for the product paths.
                 */
                if (null !== $defaultLanguage) {
                    $this->importRoutes($collection, $routeConfig, $portal, $defaultLanguage);
                }
            } else {
                $this->importRoutes($collection, $routeConfig);
            }
        }

        return $collection;
    }

    /**
     * @return \TdbCmsLanguage|null
     */
    private function getDefaultPortalLanguage(\TdbCmsPortal $portal)
    {
        if ('' !== $portal->fieldCmsLanguageId) {
            return $portal->GetFieldCmsLanguage();
        }

        return \TdbCmsConfig::GetInstance()->GetFieldTranslationBaseLanguage();
    }

    /**
     * @param \TdbCmsPortal|null $portal
     * @param \TdbCmsLanguage|null $language
     *
     * @return void
     *
     * @throws \LogicException
     */
    private function importRoutes(RouteCollection $collection, array $routeConfig, $portal = null, $language = null)
    {
        $importedRoutes = $this->getImportedRoutes($routeConfig, $portal, $language);
        $collection->addCollection($importedRoutes);
    }

    /**
     * @return RouteCollection
     *
     * @throws \LogicException
     */
    private function getImportedRoutes(array $routeConfig, ?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null)
    {
        switch ($routeConfig['type']) {
            case 'service':
            case 'class':
                if (null === $portal || null === $language) {
                    throw new \LogicException('portal and language must be given when importing routes.');
                }
                /* @var CollectionGeneratorInterface $collectionGenerator */
                if ('service' === $routeConfig['type']) {
                    try {
                        $collectionGenerator = $this->container->get($routeConfig['resource']);
                    } catch (ServiceNotFoundException $e) {
                        throw new \LogicException('Routing service resource not found: '.$routeConfig['resource']);
                    }
                } else {
                    $collectionGenerator = new $routeConfig['resource']();
                }
                if (!$collectionGenerator instanceof CollectionGeneratorInterface) {
                    throw new \LogicException(
                        sprintf(
                            "Collection generator '%s' does not implement interface esono\\pkgCmsRouting\\CollectionGeneratorInterface.",
                            $routeConfig['resource']
                        )
                    );
                }
                $importedRoutes = $collectionGenerator->getCollection($routeConfig, $portal, $language);
                break;
            case 'yaml':
            default:
                $resource = $routeConfig['resource'];
                // backwards compatibility: paths were relative to the vendor directory before Chameleon 6.1.0
                if ('@' !== substr($resource, 0, 1)) {
                    $resource = PATH_VENDORS.$resource;
                }
                $importedRoutes = $this->import($resource, $routeConfig['type']);
                break;
        }
        if (null !== $importedRoutes) {
            if (true === isset($routeConfig['system_page_name']) && '' !== $routeConfig['system_page_name']) {
                $systemPageId = $portal->GetSystemPageId($routeConfig['system_page_name'], $language);
                /** @var Route $route */
                foreach ($importedRoutes->all() as $route) {
                    $route->addDefaults(['pagedef' => $systemPageId]);
                }
            }
        }

        foreach ($importedRoutes as $route) {
            $this->handleSecurityAndFinalRoutePath($route);
        }
        if (null !== $portal && null !== $language) {
            $importedRoutes = $this->createPortalAndLanguageRoutes(
                $importedRoutes,
                $portal,
                $language
            );
        }

        return $importedRoutes;
    }

    private function createPortalAndLanguageRoutes(
        RouteCollection $importedRoutes,
        \TdbCmsPortal $portal,
        \TdbCmsLanguage $language
    ): RouteCollection {
        $urlPrefix = $this->urlPrefixGenerator->generatePrefix($portal, $language);
        $hasTrailingSlash = false === CHAMELEON_SEO_URL_REMOVE_TRAILING_SLASH && true === $portal->fieldUseSlashInSeoUrls;
        $routes = new RouteCollection();
        $domainPlaceholder = $this->routingUtil->getHostRequirementPlaceholder();
        $alternativeDomains = $this->getAlternativeDomains($portal, $language, $urlPrefix);

        foreach ($importedRoutes->all() as $name => $route) {
            $this->removeExistingPrefix($route, $urlPrefix);
            $finalRouteName = $this->getRouteNameWithPortalAndLanguageInformation($name, $portal, $language);
            $secure = in_array('https', $route->getSchemes(), true);
            $domainRequirement = $this->routingUtil->getDomainRequirement($portal, $language, $secure);

            if ('' !== $domainRequirement) {
                $defaultRoute = clone $route;
                $this->configureRoute($defaultRoute, $urlPrefix, $hasTrailingSlash, $domainPlaceholder, $domainRequirement, $language);
                $routes->add($finalRouteName, $defaultRoute);
            }

            foreach ($alternativeDomains as $domain) {
                $domainRoute = clone $route;
                $domainPrefix = $this->urlPrefixGenerator->generatePrefix($portal, $language, $domain);
                $this->configureRoute(
                    $domainRoute,
                    $domainPrefix,
                    $hasTrailingSlash,
                    $domainPlaceholder,
                    $this->getDomainRequirement($domain, $secure),
                    $language
                );
                $routes->add($finalRouteName.'-domain-'.$domain->id, $domainRoute);
            }
        }

        if (0 === $routes->count()) {
            // todo #70643: replace exception with a log message. The exception is not new, but since it breaks routing for all portals, we should remove it!
            throw new \LogicException(sprintf('There is no domain configured for portal ID %s and language ID %s.', $portal->id, $language->id));
        }

        return $routes;
    }

    /**
     * @param string $name
     *
     * @return string
     */
    private function getRouteNameWithPortalAndLanguageInformation($name, \TdbCmsPortal $portal, \TdbCmsLanguage $language)
    {
        return $name.'-'.$portal->id.'-'.$language->fieldIso6391;
    }

    private function removeExistingPrefix(Route $route, string $prefix): void
    {
        if ($route->hasDefault('containsPortalAndLanguagePrefix')) {
            if ('' !== $prefix
                && true === $route->getDefault('containsPortalAndLanguagePrefix')
                && str_starts_with($route->getPath(), $prefix)
            ) {
                $route->setPath(substr($route->getPath(), strlen($prefix)));
            }
            $defaults = $route->getDefaults();
            unset($defaults['containsPortalAndLanguagePrefix']);
            $route->setDefaults($defaults);
        }
    }

    private function configureRoute(
        Route $route,
        string $prefix,
        bool $hasTrailingSlash,
        string $domainPlaceholder,
        string $domainRequirement,
        \TdbCmsLanguage $language
    ): void {
        $this->addPrefix($route, $prefix);
        $this->handleTrailingSlash($route, $prefix, $hasTrailingSlash);
        $this->handleDomainRequirements($route, $domainPlaceholder, $domainRequirement);
        $this->handleLocale($route, $language);
    }

    /**
     * @return \TdbCmsPortalDomains[]
     */
    private function getAlternativeDomains(
        \TdbCmsPortal $portal,
        \TdbCmsLanguage $language,
        string $defaultPrefix
    ): array {
        if (false === $portal->fieldUseMultilanguage) {
            return [];
        }

        $domains = [];
        $domainList = $portal->GetFieldCmsPortalDomainsList();
        while ($domain = $domainList->Next()) {
            $additionalLanguageIds = $domain->GetFieldCmsLanguageIdList();
            if ([] === $additionalLanguageIds) {
                continue;
            }
            if ($language->id !== $this->getDefaultLanguageId($portal, $domain)
                && false === in_array($language->id, $additionalLanguageIds, true)
            ) {
                continue;
            }
            if ($defaultPrefix === $this->urlPrefixGenerator->generatePrefix($portal, $language, $domain)) {
                continue;
            }

            $domains[] = $domain;
        }

        return $domains;
    }

    private function getDefaultLanguageId(\TdbCmsPortal $portal, \TdbCmsPortalDomains $domain): string
    {
        if ('' !== $domain->fieldCmsLanguageId) {
            return $domain->fieldCmsLanguageId;
        }
        if ('' !== $portal->fieldCmsLanguageId) {
            return $portal->fieldCmsLanguageId;
        }

        return \TdbCmsConfig::GetInstance()->fieldTranslationBaseLanguageId;
    }

    private function getDomainRequirement(\TdbCmsPortalDomains $domain, bool $secure): string
    {
        if ('' !== $domain->fieldSslname) {
            if ($secure) {
                return $domain->fieldSslname;
            }
            if ($domain->fieldSslname !== $domain->fieldName) {
                return $domain->fieldSslname.'|'.$domain->fieldName;
            }
        }

        return $domain->fieldName;
    }

    /**
     * @param string $prefix
     *
     * @return void
     */
    private function addPrefix(Route $route, $prefix)
    {
        $route->setPath($prefix.$route->getPath());
    }

    /**
     * @param string $urlPrefix
     * @param bool $hasTrailingSlash
     *
     * @return void
     */
    private function handleTrailingSlash(Route $route, $urlPrefix, $hasTrailingSlash)
    {
        if (true === $hasTrailingSlash) {
            return;
        }
        $path = rtrim($route->getPath(), '/');
        if ($path === $urlPrefix) {
            $route->setPath($path);
        }
    }

    private function handleDomainRequirements(
        Route $route,
        string $domainRequirementPlaceholder,
        string $domainRequirementValue
    ): void {
        $route->setHost('{'.$domainRequirementPlaceholder.'}');
        $requirements = $route->getRequirements();
        $requirements[$domainRequirementPlaceholder] = $domainRequirementValue;
        $route->setRequirements($requirements);
    }

    /**
     * @return void
     */
    private function handleLocale(Route $route, \TdbCmsLanguage $language)
    {
        $route->setDefault('_locale', $language->fieldIso6391);
    }

    /**
     * @return void
     */
    private function handleSecurityAndFinalRoutePath(Route $route)
    {
        $path = $route->getPath();
        if (0 === strpos($path, '/http://') || 0 === strpos($path, '/https://')) {
            $path = substr($path, 1);
        }
        if (!$this->urlUtil->isUrlAbsolute($path)) {
            return;
        }
        if ($this->urlUtil->isUrlSecure($path)) {
            $route->setSchemes(['https']);
        }
        $route->setPath($this->urlUtil->getRelativeUrl($path));
    }

    /**
     * {@inheritdoc}
     */
    public function supports($resource, ?string $type = null): bool
    {
        return 'chameleon' === $type;
    }
}
