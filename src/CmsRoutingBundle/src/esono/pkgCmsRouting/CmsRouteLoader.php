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
    public function __construct(
        private readonly ContainerInterface $container,
        private readonly UrlPrefixGeneratorInterface $urlPrefixGenerator,
        private readonly RoutingUtilInterface $routingUtil,
        private readonly UrlUtil $urlUtil
    ) {
    }

    /**
     * Loads a resource.
     *
     * @param mixed $resource The resource
     * @param string $type The resource type
     *
     * @throws \LogicException
     */
    public function load(mixed $resource, $type = null): RouteCollection
    {
        $collection = new RouteCollection();
        if (!is_array($resource)) {
            return $collection;
        }
        foreach ($resource as $routeConfig) {
            $portal = null;
            if (false === isset($routeConfig['portal_id'])) {
                $this->importRoutes($collection, $routeConfig);
                continue;
            }

            $portal = \TdbCmsPortal::GetNewInstance();
            $portal->Load($routeConfig['portal_id']);
            $defaultLanguage = $this->getDefaultPortalLanguage($portal);
            $defaultLanguageId = (null === $defaultLanguage) ? null : $defaultLanguage->id;

            foreach ($this->getRouteLanguagesForPortalAndDomains($portal) as $language) {
                if ($language->id === $defaultLanguageId) {
                    continue;
                }
                $this->importRoutes($collection, $routeConfig, $portal, $language);
                $this->importAdditionalDomainVariantRoutes($collection, $routeConfig, $portal, $language);
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
                $this->importAdditionalDomainVariantRoutes($collection, $routeConfig, $portal, $defaultLanguage);
            }
        }

        return $collection;
    }

    /**
     * @return \TdbCmsLanguage[]
     */
    private function getRouteLanguagesForPortalAndDomains(\TdbCmsPortal $portal): array
    {
        $languagesById = [];

        $portalLanguageList = $portal->GetFieldCmsLanguageList();
        while ($language = $portalLanguageList->Next()) {
            $languagesById[$language->id] = $language;
        }

        $portalDomainList = $portal->GetFieldCmsPortalDomainsList();
        while ($domain = $portalDomainList->Next()) {
            if ('' === $domain->fieldCmsLanguageId) {
                continue;
            }
            $language = \TdbCmsLanguage::GetNewInstance();
            if (false === $language->Load($domain->fieldCmsLanguageId)) {
                continue;
            }

            $languagesById[$language->id] = $language;
        }

        return array_values($languagesById);
    }

    private function getDefaultPortalLanguage(\TdbCmsPortal $portal): ?\TdbCmsLanguage
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
    private function importRoutes(
        RouteCollection $collection,
        array $routeConfig,
        $portal = null,
        $language = null,
        ?\TdbCmsPortalDomains $domain = null
    ) {
        $importedRoutes = $this->getImportedRoutes($routeConfig, $portal, $language, $domain);
        $collection->addCollection($importedRoutes);
    }

    /**
     * @return RouteCollection
     *
     * @throws \LogicException
     */
    private function getImportedRoutes(
        array $routeConfig,
        ?\TdbCmsPortal $portal = null,
        ?\TdbCmsLanguage $language = null,
        ?\TdbCmsPortalDomains $domain = null
    ) {
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
            $importedRoutes = $this->getRoutesWithFinalNames($importedRoutes, $portal, $language, $domain);
            $domainRequirementPlaceholder = $this->routingUtil->getHostRequirementPlaceholder();
            $urlPrefix = $this->urlPrefixGenerator->generatePrefixForDomain($portal, $language, $domain);
            $currentDomainPathSegment = $this->urlPrefixGenerator->getDomainLanguagePathSegment($portal, $language, $domain);
            $excludedDomainSuffixes = [];
            if ('' === $currentDomainPathSegment) {
                $referenceDomain = $domain ?? $this->getPrimaryRoutingDomain($portal, $language);
                if (null !== $referenceDomain) {
                    $excludedDomainSuffixes = $this->getConfiguredDomainSuffixesForDomainFamily($portal, $referenceDomain);
                }
            }
            $hasTrailingSlash = false === CHAMELEON_SEO_URL_REMOVE_TRAILING_SLASH && true === $portal->fieldUseSlashInSeoUrls;
            foreach ($importedRoutes as $route) {
                $this->handlePortalAndLanguagePrefix($route, $urlPrefix);
                $this->handleTrailingSlash($route, $urlPrefix, $hasTrailingSlash);
                $this->preventDomainSuffixFromBeingCapturedAsPagePath($route, $excludedDomainSuffixes);
                $this->handleDomainRequirements($route, $domainRequirementPlaceholder, $portal, $language);
                $this->handleLocale($route, $language);
            }
        }

        return $importedRoutes;
    }

    /**
     * @return RouteCollection
     */
    private function getRoutesWithFinalNames(
        RouteCollection $importedRoutes,
        \TdbCmsPortal $portal,
        \TdbCmsLanguage $language,
        ?\TdbCmsPortalDomains $domain = null
    ) {
        $adjustedRoutes = new RouteCollection();
        foreach ($importedRoutes->all() as $name => $route) {
            $finalRouteName = $this->getRouteNameWithPortalDomainAndLanguageInformation($name, $portal, $language, $domain);
            $adjustedRoutes->add($finalRouteName, $route);
        }

        return $adjustedRoutes;
    }

    /**
     * @return string
     */
    private function getRouteNameWithPortalDomainAndLanguageInformation(
        string $name,
        \TdbCmsPortal $portal,
        \TdbCmsLanguage $language,
        ?\TdbCmsPortalDomains $domain = null
    ) {
        if (null !== $domain) {
            return $name.'-'.$portal->id.'-'.$language->fieldIso6391.'-domain-'.$domain->id;
        }

        return $name.'-'.$portal->id.'-'.$language->fieldIso6391;
    }

    private function importAdditionalDomainVariantRoutes(
        RouteCollection $collection,
        array $routeConfig,
        \TdbCmsPortal $portal,
        \TdbCmsLanguage $language
    ): void {
        $primaryDomain = $this->getPrimaryRoutingDomain($portal, $language);
        if (null === $primaryDomain) {
            return;
        }

        $primaryPrefix = $this->urlPrefixGenerator->generatePrefixForDomain($portal, $language, $primaryDomain);
        $domainList = $this->getCompatiblePortalDomains($portal, $language);
        foreach ($domainList as $domain) {
            if ($domain->id === $primaryDomain->id) {
                continue;
            }

            $domainPrefix = $this->urlPrefixGenerator->generatePrefixForDomain($portal, $language, $domain);
            if ($domainPrefix === $primaryPrefix) {
                continue;
            }

            $this->importRoutes($collection, $routeConfig, $portal, $language, $domain);
        }
    }

    /**
     * @return \TdbCmsPortalDomains[]
     */
    private function getCompatiblePortalDomains(\TdbCmsPortal $portal, \TdbCmsLanguage $language): array
    {
        $domains = [];
        $portalDomainList = $portal->GetFieldCmsPortalDomainsList();
        while ($domain = $portalDomainList->Next()) {
            if ('' !== $domain->fieldCmsLanguageId && $domain->fieldCmsLanguageId !== $language->id) {
                continue;
            }
            $domains[] = $domain;
        }

        return $domains;
    }

    private function getPrimaryDomain(\TdbCmsPortal $portal, \TdbCmsLanguage $language): ?\TdbCmsPortalDomains
    {
        $fallbackDomain = null;
        foreach ($this->getCompatiblePortalDomains($portal, $language) as $domain) {
            if (true !== (bool) $domain->fieldIsMasterDomain) {
                continue;
            }
            if ($domain->fieldCmsLanguageId === $language->id) {
                return $domain;
            }
            if ('' === $domain->fieldCmsLanguageId) {
                $fallbackDomain = $domain;
            }
        }

        return $fallbackDomain;
    }

    private function getPrimaryRoutingDomain(\TdbCmsPortal $portal, \TdbCmsLanguage $language): ?\TdbCmsPortalDomains
    {
        foreach ($this->getCompatiblePortalDomains($portal, $language) as $domain) {
            if ($domain->fieldCmsLanguageId === $language->id) {
                return $domain;
            }
        }

        return $this->getPrimaryDomain($portal, $language);
    }

    /**
     * @param string $prefix
     */
    private function handlePortalAndLanguagePrefix(Route $route, $prefix): void
    {
        if ($route->hasDefault('containsPortalAndLanguagePrefix')) {
            if (false === $route->getDefault('containsPortalAndLanguagePrefix')) {
                $this->addPrefix($route, $prefix);
            }
            $defaults = $route->getDefaults();
            unset($defaults['containsPortalAndLanguagePrefix']);
            $route->setDefaults($defaults);
        } else {
            $this->addPrefix($route, $prefix);
        }
    }

    /**
     * @param string $prefix
     */
    private function addPrefix(Route $route, $prefix): void
    {
        $route->setPath($prefix.$route->getPath());
    }

    /**
     * @param string $urlPrefix
     * @param bool $hasTrailingSlash
     */
    private function handleTrailingSlash(Route $route, $urlPrefix, $hasTrailingSlash): void
    {
        if (true === $hasTrailingSlash) {
            return;
        }
        if ('/' === ($route->getDefault('pagePath') ?? null)) {
            return;
        }
        $path = rtrim($route->getPath(), '/');
        if ($path === $urlPrefix) {
            $route->setPath($path);
        }
    }

    /**
     * @param string[] $excludedDomainSuffixes
     */
    private function preventDomainSuffixFromBeingCapturedAsPagePath(Route $route, array $excludedDomainSuffixes): void
    {
        if ([] === $excludedDomainSuffixes || false === $route->hasRequirement('pagePath')) {
            return;
        }

        $requirement = (string) $route->getRequirement('pagePath');
        if ('' === $requirement) {
            return;
        }

        $suffixPattern = implode('|', array_map(
            static fn (string $suffix): string => preg_quote($suffix, '#'),
            $excludedDomainSuffixes
        ));
        if ('' === $suffixPattern) {
            return;
        }

        $guard = '(?!(?:'.$suffixPattern.')(?:/|$))';
        if (str_contains($requirement, $guard)) {
            return;
        }

        $route->setRequirement('pagePath', $guard.$requirement);
    }

    /**
     * @return string[]
     */
    private function getConfiguredDomainSuffixesForDomainFamily(
        \TdbCmsPortal $portal,
        \TdbCmsPortalDomains $referenceDomain
    ): array {
        $suffixes = [];
        $familyHosts = $this->getDomainHosts($referenceDomain);
        $portalDomainList = $portal->GetFieldCmsPortalDomainsList();
        while ($domain = $portalDomainList->Next()) {
            if (false === $this->belongsToDomainFamily($domain, $familyHosts)) {
                continue;
            }
            $suffix = trim((string) $domain->fieldUrlSuffix);
            if ('' === $suffix) {
                continue;
            }
            $suffixes[$suffix] = $suffix;
        }

        return array_values($suffixes);
    }

    /**
     * @param string[] $familyHosts
     */
    private function belongsToDomainFamily(\TdbCmsPortalDomains $domain, array $familyHosts): bool
    {
        foreach ($this->getDomainHosts($domain) as $host) {
            if (true === isset($familyHosts[$host])) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return array<string, string>
     */
    private function getDomainHosts(\TdbCmsPortalDomains $domain): array
    {
        $hosts = [];
        foreach ([$domain->fieldName, $domain->fieldSslname] as $host) {
            $host = trim((string) $host);
            if ('' === $host) {
                continue;
            }
            $hosts[$host] = $host;
        }

        return $hosts;
    }

    /**
     * @param string $domainRequirementPlaceholder
     *
     * @return void
     *
     * @throws \LogicException if no primary domain isw set
     */
    private function handleDomainRequirements(
        Route $route,
        $domainRequirementPlaceholder,
        \TdbCmsPortal $portal,
        \TdbCmsLanguage $language
    ) {
        $route->setHost('{'.$domainRequirementPlaceholder.'}');
        $requirements = $route->getRequirements();
        $secure = in_array('https', $route->getSchemes(), true);
        $domainRequirementValue = $this->routingUtil->getDomainRequirement($portal, $language, $secure);
        if ('' === $domainRequirementValue) {
            throw new \LogicException(sprintf('There is no primary domain configured for the portal with ID %s. Route generation will only work with a primary domain.', $portal->id));
        }
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
