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

use ChameleonSystem\CoreBundle\Exception\InvalidPortalDomainException;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Util\RoutingUtilInterface;
use ChameleonSystem\CoreBundle\Util\UrlPrefixGeneratorInterface;
use ChameleonSystem\CoreBundle\Util\UrlUtil;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Config\Loader\Loader;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;
use Symfony\Component\Routing\Route;
use Symfony\Component\Routing\RouteCollection;

class CmsRouteLoader extends Loader
{
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly UrlPrefixGeneratorInterface $urlPrefixGenerator,
        private readonly RoutingUtilInterface $routingUtil,
        private readonly UrlUtil $urlUtil,
        private readonly LanguageServiceInterface $languageService,
        private readonly LoggerInterface $logger = new NullLogger()
    ) {
        parent::__construct();
    }

    /**
     * Loads a resource.
     *
     * @param mixed $resource The resource
     * @param string $type The resource type
     *
     * @return RouteCollection
     *
     * @throws \LogicException|InvalidPortalDomainException
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

                foreach ($this->getNonDefaultLanguagesSortedByPrefix(
                    $portal,
                    $languages,
                    $defaultLanguageId
                ) as $language) {
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

    private function getDefaultPortalLanguage(\TdbCmsPortal $portal): ?\TdbCmsLanguage
    {
        if ('' !== $portal->fieldCmsLanguageId) {
            return $portal->GetFieldCmsLanguage();
        }

        return \TdbCmsConfig::GetInstance()->GetFieldTranslationBaseLanguage();
    }

    /**
     * Orders non-default languages so that prefixed routes are registered before prefixless ones.
     *
     * This prevents generic catch-all routes such as "/{pagePath}" from swallowing more specific language-prefixed
     * routes like "/it/" or "/fr/" during route matching.
     *
     * @param array<string, \TdbCmsLanguage> $languages
     *
     * @return \TdbCmsLanguage[]
     */
    private function getNonDefaultLanguagesSortedByPrefix(
        \TdbCmsPortal $portal,
        array $languages,
        ?string $defaultLanguageId
    ): array {
        $prefixedLanguages = [];
        $prefixlessLanguages = [];

        foreach ($languages as $language) {
            if ($language->id === $defaultLanguageId) {
                continue;
            }

            try {
                $prefix = $this->urlPrefixGenerator->generatePrefix($portal, $language);
            } catch (InvalidPortalDomainException $exception) {
                $this->logMissingDomain($portal, $language, $exception);
                continue;
            }

            if ('' === $prefix) {
                $prefixlessLanguages[] = $language;
                continue;
            }

            $prefixedLanguages[] = $language;
        }

        return array_merge($prefixedLanguages, $prefixlessLanguages);
    }

    /**
     * @throws InvalidPortalDomainException
     */
    private function importRoutes(
        RouteCollection $collection,
        array $routeConfig,
        ?\TdbCmsPortal $portal = null,
        ?\TdbCmsLanguage $language = null
    ): void {
        try {
            $importedRoutes = $this->getImportedRoutes($routeConfig, $portal, $language);
        } catch (InvalidPortalDomainException $exception) {
            if (null === $portal || null === $language) {
                throw $exception;
            }
            $this->logMissingDomain($portal, $language, $exception);

            return;
        }
        $collection->addCollection($importedRoutes);
    }

    /**
     * @throws InvalidPortalDomainException
     */
    private function getImportedRoutes(
        array $routeConfig,
        ?\TdbCmsPortal $portal = null,
        ?\TdbCmsLanguage $language = null
    ): RouteCollection {
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
                if (false === $collectionGenerator instanceof CollectionGeneratorInterface) {
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
                if (false === str_starts_with($resource, '@')) {
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
            // Collection generators may legitimately return no routes for a given portal/language pair.
            if (0 === $importedRoutes->count()) {
                return $importedRoutes;
            }
            $importedRoutes = $this->createPortalAndLanguageRoutes(
                $importedRoutes,
                $portal,
                $language
            );
        }

        return $importedRoutes;
    }

    /**
     * @throws InvalidPortalDomainException
     */
    private function createPortalAndLanguageRoutes(
        RouteCollection $importedRoutes,
        \TdbCmsPortal $portal,
        \TdbCmsLanguage $language
    ): RouteCollection {
        $urlPrefix = $this->urlPrefixGenerator->generatePrefix($portal, $language);
        $hasTrailingSlash = false === CHAMELEON_SEO_URL_REMOVE_TRAILING_SLASH
            && true === $portal->fieldUseSlashInSeoUrls;
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
                $this->configureRoute(
                    $defaultRoute,
                    $urlPrefix,
                    $hasTrailingSlash,
                    $domainPlaceholder,
                    $domainRequirement,
                    $language
                );
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
            $this->logMissingDomain($portal, $language);
        }

        return $routes;
    }

    private function logMissingDomain(
        \TdbCmsPortal $portal,
        \TdbCmsLanguage $language,
        ?InvalidPortalDomainException $exception = null
    ): void {
        $this->logger->warning(
            'Skipping routes: no domain configured for portal ID {portal_id} and language ID {language_id}.',
            ['portal_id' => $portal->id, 'language_id' => $language->id, 'exception' => $exception]
        );
    }

    private function getRouteNameWithPortalAndLanguageInformation(
        string $name,
        \TdbCmsPortal $portal,
        \TdbCmsLanguage $language
    ): string {
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
     *
     * @throws InvalidPortalDomainException
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
        $domainList->GoToStart();
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

        return $this->languageService->getCmsBaseLanguageId();
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

    private function addPrefix(Route $route, string $prefix): void
    {
        $route->setPath($prefix.$route->getPath());
    }

    private function handleTrailingSlash(Route $route, string $urlPrefix, bool $hasTrailingSlash): void
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

    private function handleLocale(Route $route, \TdbCmsLanguage $language): void
    {
        $route->setDefault('_locale', $language->fieldIso6391);
    }

    private function handleSecurityAndFinalRoutePath(Route $route): void
    {
        $path = $route->getPath();
        if (true === str_starts_with($path, '/http://') || true === str_starts_with($path, '/https://')) {
            $path = substr($path, 1);
        }
        if (false === $this->urlUtil->isUrlAbsolute($path)) {
            return;
        }
        if ($this->urlUtil->isUrlSecure($path)) {
            $route->setSchemes(['https']);
        }
        $route->setPath($this->urlUtil->getRelativeUrl($path));
    }

    public function supports($resource, ?string $type = null): bool
    {
        return 'chameleon' === $type;
    }
}
