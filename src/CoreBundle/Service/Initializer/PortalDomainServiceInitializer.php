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
use ChameleonSystem\CoreBundle\Service\DomainPathVariantResolutionResult;
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
    /**
     * @var InputFilterUtilInterface
     */
    private $inputFilterUtil;
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var RequestStack
     */
    private $requestStack;
    /**
     * @var CmsPortalDomainsDataAccessInterface
     */
    private $cmsPortalDomainsDataAccess;
    /**
     * @var DomainPathVariantResolver
     */
    private $domainPathVariantResolver;

    public function __construct(
        InputFilterUtilInterface $inputFilterUtil,
        ContainerInterface $container,
        RequestStack $requestStack,
        CmsPortalDomainsDataAccessInterface $cmsPortalDomainsDataAccess,
        DomainPathVariantResolver $domainPathVariantResolver
    ) {
        $this->inputFilterUtil = $inputFilterUtil;
        $this->container = $container; // avoid circular dependencies
        $this->requestStack = $requestStack;
        $this->cmsPortalDomainsDataAccess = $cmsPortalDomainsDataAccess;
        $this->domainPathVariantResolver = $domainPathVariantResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function initialize(PortalDomainServiceInterface $portalDomainService)
    {
        if (null === $request = $this->requestStack->getCurrentRequest()) {
            return;
        }

        $portalDomainService->setActiveDomainPathVariantResolutionResult(null);

        $requestInfoService = $this->getRequestInfoService();
        if ($requestInfoService->isChameleonRequestType(RequestTypeInterface::REQUEST_TYPE_FRONTEND)
            && true === $requestInfoService->isCmsTemplateEngineEditMode()
        ) {
            list($portal, $domain) = $this->determinePortalAndDomainForCmsTemplateEngineMode($portalDomainService);
        } elseif ($requestInfoService->isChameleonRequestType(RequestTypeInterface::REQUEST_TYPE_BACKEND)) {
            $portal = null;
            $domain = null;
        } else {
            list($portal, $domain, $resolutionResult) = $this->determinePortalAndDomainDefault($request, $portalDomainService);
            $portalDomainService->setActiveDomainPathVariantResolutionResult($resolutionResult);
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
        $resolutionResult = null;

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
        $prefix = $this->extractPortalPrefix($sRelativePath, $portalPrefixList);

        $aKey = [
            'class' => __CLASS__,
            'method' => 'setPortalAndDomainFromRequest',
            'host' => $sName,
            'path' => $sRelativePath,
            'prefix' => $prefix,
            'userIsSignedIntoCMSBackend' => $isUserSignedInToBackend,
            'bTemplateEngineEditMode' => false,
        ];

        $cache = $this->getCache();
        $sKey = $cache->getKey($aKey, false);

        $aResultData = $cache->get($sKey);
        if (null !== $aResultData) {
            $portal = $aResultData['portal'];
            $domain = $aResultData['domain'];
            $resolutionResult = $this->createResolutionResultFromCacheData($aResultData['domainPathVariantResolution'] ?? null);

            return [$portal, $domain, $resolutionResult];
        }

        $aResultData = [
            'portal' => null,
            'domain' => null,
            'domainPathVariantResolution' => null,
        ];

        $resolutionResult = $this->resolveDomainPathVariant(
            $sName,
            $sRelativePath,
            $domainCandidates,
            $portalPrefixList,
            $isUserSignedInToBackend
        );
        $portalDomainService->setActiveDomainPathVariantResolutionResult($resolutionResult);
        $resolvedPortalAndDomain = $this->createPortalAndDomainFromResolution($resolutionResult, $isUserSignedInToBackend);
        if (null !== $resolvedPortalAndDomain) {
            $aResultData = $resolvedPortalAndDomain;
            $aResultData['domainPathVariantResolution'] = $resolutionResult?->toArray();
        } else {
            $aResultData = $this->determinePortalAndDomainFallback(
                $domainCandidates,
                $prefix,
                $isUserSignedInToBackend
            );
            $aResultData['domainPathVariantResolution'] = $resolutionResult?->toArray();
        }

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

        return [$portal, $domain, $resolutionResult];
    }

    /**
     * @param array<int, array<string, mixed>> $domainCandidates
     * @param string[]                         $portalPrefixList
     *
     * @return array{portal: \TCMSPortal|null, domain: \TCMSPortalDomain|null}|null
     */
    protected function resolveDomainPathVariant(
        string $host,
        string $path,
        array $domainCandidates,
        array $portalPrefixList,
        bool $allowInactivePortals
    ): ?DomainPathVariantResolutionResult {
        if ([] === $domainCandidates) {
            return null;
        }

        $portalIdentifiers = $this->buildPortalIdentifierMap($domainCandidates, $portalPrefixList, $allowInactivePortals);
        return $this->domainPathVariantResolver->resolve($host, $path, $domainCandidates, $portalIdentifiers);
    }

    /**
     * @return array{portal: \TCMSPortal|null, domain: \TCMSPortalDomain|null}|null
     */
    protected function createPortalAndDomainFromResolution(
        ?DomainPathVariantResolutionResult $resolution,
        bool $allowInactivePortals
    ): ?array {
        if (null === $resolution || false === $resolution->isDomainVariantMatched() || null === $resolution->getMatchedDomain()) {
            return null;
        }

        return [
            'portal' => $this->loadPortalById($resolution->getMatchedPortalId(), $allowInactivePortals),
            'domain' => $this->createDomainFromData($resolution->getMatchedDomain()),
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $domainCandidates
     *
     * @return array{portal: \TCMSPortal|null, domain: \TCMSPortalDomain|null}
     */
    protected function determinePortalAndDomainFallback(
        array $domainCandidates,
        string $prefix,
        bool $allowInactivePortals
    ): array {
        $result = [
            'portal' => null,
            'domain' => null,
        ];

        if ([] === $domainCandidates) {
            return $result;
        }

        $portalIdList = $this->getUniquePortalIds($domainCandidates);
        $resolvedPortalId = null;
        if (\count($portalIdList) > 1) {
            $portalData = $this->cmsPortalDomainsDataAccess->getActivePortalCandidate($portalIdList, $prefix, $allowInactivePortals);
            if (null !== $portalData) {
                $result['portal'] = $this->createPortalFromData($portalData);
                $resolvedPortalId = (string) $portalData['id'];
                $resolvedDomain = $this->getFallbackDomainForPortal($domainCandidates, $resolvedPortalId);
                if (null !== $resolvedDomain) {
                    $result['domain'] = $this->createDomainFromData($resolvedDomain);
                }
            }
        } else {
            $resolvedPortalId = $portalIdList[0] ?? null;
            $resolvedDomain = $this->getFallbackDomainForPortal($domainCandidates, $resolvedPortalId);
            if (null !== $resolvedDomain) {
                $result['domain'] = $this->createDomainFromData($resolvedDomain);
            }
        }

        if (null === $result['portal'] && null !== $resolvedPortalId) {
            $result['portal'] = $this->loadPortalById($resolvedPortalId, $allowInactivePortals);
        }

        return $result;
    }

    /**
     * @param array<int, array<string, mixed>> $domainCandidates
     * @param string[]                         $portalPrefixList
     *
     * @return array<string, string>
     */
    protected function buildPortalIdentifierMap(array $domainCandidates, array $portalPrefixList, bool $allowInactivePortals): array
    {
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

        $suffixlessDomains = array_values(array_filter(
            $portalDomains,
            static function (array $portalDomain): bool {
                return '' === (string) ($portalDomain['url_suffix'] ?? '');
            }
        ));

        if (1 === \count($suffixlessDomains)) {
            return $suffixlessDomains[0];
        }

        return null;
    }

    /**
     * @param string[] $portalPrefixList
     */
    protected function extractPortalPrefix(string $relativePath, array $portalPrefixList): string
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

    protected function createPortalFromData(array $portalData): \TCMSPortal
    {
        return \TdbCmsPortal::GetNewInstance($portalData);
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

    /**
     * @param array<string, mixed> $domainData
     */
    protected function createDomainFromData(array $domainData): \TCMSPortalDomain
    {
        return \TdbCmsPortalDomains::GetNewInstance($domainData);
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
    private function createResolutionResultFromCacheData(?array $cacheData): ?DomainPathVariantResolutionResult
    {
        if (null === $cacheData) {
            return null;
        }

        return DomainPathVariantResolutionResult::createFromArray($cacheData);
    }
}
