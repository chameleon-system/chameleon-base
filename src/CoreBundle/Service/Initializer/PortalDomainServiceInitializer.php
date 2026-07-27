<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Service\Initializer;

use ChameleonSystem\CoreBundle\DataAccess\CmsPortalDomainsDataAccessInterface;
use ChameleonSystem\CoreBundle\Exception\InvalidPortalDomainException;
use ChameleonSystem\CoreBundle\RequestType\RequestTypeInterface;
use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;
use ChameleonSystem\CoreBundle\Service\DomainPathMatch;
use ChameleonSystem\CoreBundle\Service\DomainPathVariantResolver;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\ServiceLocator;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;
use esono\pkgCmsCache\CacheInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Class PortalDomainServiceInitializer.
 */
class PortalDomainServiceInitializer implements PortalDomainServiceInitializerInterface
{
    public function __construct(
        private readonly InputFilterUtilInterface $inputFilterUtil,
        private readonly ContainerInterface $container,
        private readonly RequestStack $requestStack,
        private readonly CmsPortalDomainsDataAccessInterface $cmsPortalDomainsDataAccess,
        private readonly DomainPathVariantResolver $domainPathVariantResolver
    ) {
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(PortalDomainServiceInterface $portalDomainService)
    {
        $request = $this->requestStack->getCurrentRequest();
        if (null === $request) {
            return;
        }

        $this->storeDomainPathMatchState($portalDomainService, $request, null);

        $requestInfoService = $this->getRequestInfoService();
        if ($requestInfoService->isChameleonRequestType(RequestTypeInterface::REQUEST_TYPE_FRONTEND)
            && true === $requestInfoService->isCmsTemplateEngineEditMode()
        ) {
            [$portal, $domain] = $this->determinePortalAndDomainForCmsTemplateEngineMode($portalDomainService);
        } elseif ($requestInfoService->isChameleonRequestType(RequestTypeInterface::REQUEST_TYPE_BACKEND)) {
            $portal = null;
            $domain = null;
        } else {
            [$portal, $domain] = $this->determinePortalAndDomainDefault($request, $portalDomainService);
        }

        $portalDomainService->setActivePortal($portal);
        $portalDomainService->setActiveDomain($domain);
    }

    /**
     * @throws InvalidPortalDomainException
     */
    private function determinePortalAndDomainForCmsTemplateEngineMode(PortalDomainServiceInterface $portalDomainService): array
    {
        $previewLanguageID = $this->inputFilterUtil->getFilteredInput('previewLanguageId');

        $portal = $this->getActivePageService()->getActivePage()?->GetPortal();
        $domain = $portalDomainService->getPrimaryDomain($portal->id, $previewLanguageID);

        return [$portal, $domain];
    }

    /**
     * @throws InvalidPortalDomainException
     */
    private function determinePortalAndDomainDefault(Request $request, PortalDomainServiceInterface $portalDomainService): array
    {
        $portal = null;
        $domain = null;
        $domainPathMatch = null;

        $sName = $request->getHost();
        $sRelativePath = $request->getPathInfo();
        $isUserSignedInToBackend = $this->isUserSignedInToBackend($request);

        $frontController = PATH_CUSTOMER_FRAMEWORK_CONTROLLER;
        if ('/' !== substr($frontController, 0, 1)) {
            $frontController = '/'.$frontController;
        }
        if ($frontController === $sRelativePath) {
            $pagedef = $request->attributes->get('pagedef');
            if (null !== $pagedef) {
                $oPage = \TdbCmsTplPage::GetNewInstance();
                if ($oPage->Load($pagedef)) {
                    $portal = $oPage->GetPortal();

                    if (true === $portal->fieldDeactivePortal && false === $isUserSignedInToBackend) {
                        $portal = null;
                    } else {
                        $previewLanguageId = $this->inputFilterUtil->getFilteredInput('previewLanguageId');
                        $domain = $portalDomainService->getPrimaryDomain($portal->id, $previewLanguageId);

                        return [$portal, $domain, null];
                    }
                }
            }
        }

        $domainCandidates = $this->cmsPortalDomainsDataAccess->getDomainCandidatesByHost($sName);
        $portalPrefixList = $this->cmsPortalDomainsDataAccess->getPortalPrefixListForDomain($sName);
        $portalPrefix = $this->extractPortalPrefixFromRelativePath($sRelativePath, $portalPrefixList);

        $aKey = [
            'class' => __CLASS__,
            'method' => 'setPortalAndDomainFromRequest',
            'host' => $sName,
            'path' => $sRelativePath,
            'prefix' => $portalPrefix,
            'userIsSignedIntoCMSBackend' => $isUserSignedInToBackend,
            'bTemplateEngineEditMode' => false,
        ];

        $cache = $this->getCache();
        $sKey = $cache->getKey($aKey, false);

        $aResultData = $cache->get($sKey);
        if (null !== $aResultData) {
            $domainPathMatch = $this->createDomainPathMatchFromCacheData($aResultData['domainPathMatch'] ?? null);
            $this->storeDomainPathMatchState($portalDomainService, $request, $domainPathMatch);

            return [$aResultData['portal'], $aResultData['domain']];
        }

        $aResultData = [
            'portal' => null,
            'domain' => null,
        ];

        $domainPathMatch = $this->resolveDomainPathVariant(
            $sName,
            $sRelativePath,
            $domainCandidates,
            $portalPrefixList,
            $isUserSignedInToBackend
        );
        $this->storeDomainPathMatchState($portalDomainService, $request, $domainPathMatch);
        $resolvedPortalAndDomain = $this->loadPortalAndDomainFromMatch($domainPathMatch, $isUserSignedInToBackend);
        $aResultData = $resolvedPortalAndDomain ?? $this->determinePortalAndDomainFallback(
            $domainCandidates,
            $portalPrefix,
            $isUserSignedInToBackend
        );

        $cache->set(
            $sKey,
            $aResultData,
            [
                ['table' => 'cms_portal', 'id' => null],
                ['table' => 'cms_portal_domains', 'id' => null],
            ]
        );

        $portal = $aResultData['portal'];
        $domain = $aResultData['domain'];

        return [$portal, $domain];
    }

    /**
     * @param array<int, array<string, mixed>> $domainCandidates
     * @param string[] $portalPrefixList
     *
     * @return array{portal: \TCMSPortal|null, domain: \TCMSPortalDomain|null}|null
     */
    protected function resolveDomainPathVariant(
        string $host,
        string $path,
        array $domainCandidates,
        array $portalPrefixList,
        bool $allowInactivePortals
    ): ?DomainPathMatch {
        if ([] === $domainCandidates) {
            return null;
        }

        $portalIdentifiers = $this->buildPortalIdentifierMap($domainCandidates, $portalPrefixList, $allowInactivePortals);

        return $this->domainPathVariantResolver->resolve($host, $path, $domainCandidates, $portalIdentifiers);
    }

    /**
     * @return array{portal: \TCMSPortal|null, domain: \TCMSPortalDomain|null}|null
     */
    protected function loadPortalAndDomainFromMatch(
        ?DomainPathMatch $domainPathMatch,
        bool $allowInactivePortals
    ): ?array {
        if (null === $domainPathMatch || false === $domainPathMatch->isMatched() || null === $domainPathMatch->getMatchedDomain()) {
            return null;
        }

        return [
            'portal' => $this->loadPortalById($domainPathMatch->getMatchedPortalId(), $allowInactivePortals),
            'domain' => \TdbCmsPortalDomains::GetNewInstance($domainPathMatch->getMatchedDomain()),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $domainCandidates
     *
     * @return array{
     *     portal: \TdbCmsPortal|null,
     *     domain: \TdbCmsPortalDomains|null
     * }
     */
    protected function determinePortalAndDomainFallback(
        array $domainCandidates,
        string $prefix,
        bool $allowInactivePortals
    ): array {
        $emptyResult = [
            'portal' => null,
            'domain' => null,
        ];

        if ([] === $domainCandidates) {
            return $emptyResult;
        }

        $portalIdList = $this->getUniquePortalIds($domainCandidates);

        if ([] === $portalIdList) {
            return $emptyResult;
        }

        $portalData = null;

        if (\count($portalIdList) > 1) {
            $portalData = $this->cmsPortalDomainsDataAccess->getActivePortalCandidate(
                $portalIdList,
                $prefix,
                $allowInactivePortals
            );

            if (null === $portalData) {
                return $emptyResult;
            }

            $resolvedPortalId = (string) $portalData['id'];
        } else {
            $resolvedPortalId = (string) $portalIdList[0];
        }

        $resolvedDomain = $this->getFallbackDomainForPortal($domainCandidates, $resolvedPortalId);

        return [
            'portal' => null !== $portalData
                ? \TdbCmsPortal::GetNewInstance($portalData)
                : $this->loadPortalById($resolvedPortalId, $allowInactivePortals),

            'domain' => null !== $resolvedDomain
                ? \TdbCmsPortalDomains::GetNewInstance($resolvedDomain)
                : null,
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $domainCandidates
     * @param string[] $portalPrefixList
     *
     * @return array<string, string>
     */
    protected function buildPortalIdentifierMap(
        array $domainCandidates,
        array $portalPrefixList,
        bool $allowInactivePortals
    ): array {
        if ([] === $domainCandidates || [] === $portalPrefixList) {
            return [];
        }

        $portalIdList = $this->getUniquePortalIds($domainCandidates);
        $portalIdentifierMap = [];
        foreach ($portalPrefixList as $portalPrefix) {
            if ('' === $portalPrefix) {
                continue;
            }
            $portalData = $this->cmsPortalDomainsDataAccess->getActivePortalCandidate($portalIdList, $portalPrefix, $allowInactivePortals);
            if (null === $portalData) {
                continue;
            }
            if ($portalPrefix !== (string) ($portalData['identifier'] ?? '')) {
                continue;
            }
            $portalIdentifierMap[$portalPrefix] = (string) $portalData['id'];
        }

        return $portalIdentifierMap;
    }

    /**
     * @param array<int, array<string, mixed>> $domainCandidates
     *
     * @return string[]
     */
    protected function getUniquePortalIds(array $domainCandidates): array
    {
        $portalIds = [];
        foreach ($domainCandidates as $domainCandidate) {
            $portalId = (string) ($domainCandidate['cms_portal_id'] ?? '');
            if ('' === $portalId) {
                continue;
            }
            $portalIds[$portalId] = $portalId;
        }

        return array_values($portalIds);
    }

    /**
     * @param array<int, array<string, mixed>> $domainCandidates
     *
     * @return array<string, mixed>|null
     */
    protected function getFallbackDomainForPortal(array $domainCandidates, ?string $portalId): ?array
    {
        if (null === $portalId || '' === $portalId) {
            return null;
        }

        $portalDomains = [];
        foreach ($domainCandidates as $domainCandidate) {
            if ($portalId === (string) ($domainCandidate['cms_portal_id'] ?? '')) {
                $portalDomains[] = $domainCandidate;
            }
        }

        if (1 === \count($portalDomains)) {
            return $portalDomains[0];
        }

        $suffixlessDomains = [];
        foreach ($portalDomains as $portalDomain) {
            if ('' !== (string) ($portalDomain['url_suffix'] ?? '')) {
                continue;
            }

            $suffixlessDomains[] = $portalDomain;
        }

        if (1 === \count($suffixlessDomains)) {
            return $suffixlessDomains[0];
        }

        return null;
    }

    /**
     * @param string[] $portalPrefixList
     */
    protected function extractPortalPrefixFromRelativePath(string $relativePath, array $portalPrefixList): string
    {
        $prefix = '';
        if (\strlen($relativePath) <= 1) {
            return $prefix;
        }

        $secondSlashPosition = strpos($relativePath, '/', 1);
        if (false === $secondSlashPosition) {
            $prefix = substr($relativePath, 1);
        } else {
            $prefix = substr($relativePath, 1, $secondSlashPosition - 1);
        }

        if ('' !== $prefix && false === in_array($prefix, $portalPrefixList, true)) {
            $prefix = '';
        }

        return $prefix;
    }

    protected function isUserSignedInToBackend(Request $request): bool
    {
        if (false === $request->hasSession()) {
            return false;
        }

        /** @var SecurityHelperAccess $securityHelper */
        $securityHelper = ServiceLocator::get(SecurityHelperAccess::class);

        return $securityHelper->isGranted(CmsUserRoleConstants::CMS_USER);
    }

    protected function loadPortalById(?string $portalId, bool $allowInactivePortals): ?\TCMSPortal
    {
        if (null === $portalId || '' === $portalId) {
            return null;
        }

        $portal = \TdbCmsPortal::GetNewInstance();
        if (false === $portal->Load($portalId)) {
            return null;
        }

        if (false === $allowInactivePortals && true === $portal->fieldDeactivePortal) {
            return null;
        }

        return $portal;
    }

    private function getRequestInfoService(): RequestInfoServiceInterface
    {
        return $this->container->get('chameleon_system_core.request_info_service');
    }

    private function getCache(): CacheInterface
    {
        return $this->container->get('chameleon_system_cms_cache.cache');
    }

    private function getActivePageService(): ActivePageServiceInterface
    {
        return $this->container->get('chameleon_system_core.active_page_service');
    }

    /**
     * @param array<string, mixed>|null $cacheData
     */
    private function createDomainPathMatchFromCacheData(?array $cacheData): ?DomainPathMatch
    {
        if (null === $cacheData) {
            return null;
        }

        return DomainPathMatch::createFromArray($cacheData);
    }

    private function storeDomainPathMatchState(
        PortalDomainServiceInterface $portalDomainService,
        Request $request,
        ?DomainPathMatch $domainPathMatch
    ): void {
        $portalDomainService->setActiveDomainPathMatch($domainPathMatch);
        $request->attributes->set(DomainPathMatch::REQUEST_ATTRIBUTE_NAME, $domainPathMatch);
    }
}
