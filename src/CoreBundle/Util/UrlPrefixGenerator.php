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

use ChameleonSystem\CoreBundle\DataAccess\CmsPortalDomainsDataAccessInterface;
use ChameleonSystem\CoreBundle\Exception\InvalidPortalDomainException;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;

class UrlPrefixGenerator implements UrlPrefixGeneratorInterface
{
    /**
     * @var PortalDomainServiceInterface
     */
    private $portalDomainService;
    /**
     * @var CmsPortalDomainsDataAccessInterface
     */
    private $cmsPortalDomainsDataAccess;

    public function __construct(
        PortalDomainServiceInterface $portalDomainService,
        CmsPortalDomainsDataAccessInterface $cmsPortalDomainsDataAccess
    )
    {
        $this->portalDomainService = $portalDomainService;
        $this->cmsPortalDomainsDataAccess = $cmsPortalDomainsDataAccess;
    }

    /**
     * {@inheritdoc}
     */
    public function generatePrefixParts(?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null)
    {
        return $this->generatePrefixPartsForDomain($portal, $language);
    }

    /**
     * {@inheritdoc}
     */
    public function generatePrefix(?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null)
    {
        return $this->generatePrefixForDomain($portal, $language);
    }

    public function generatePrefixForDomain(?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null, ?\TdbCmsPortalDomains $domain = null)
    {
        return $this->getPathPrefix($portal, $language, $domain);
    }

    public function getPathPrefix(?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null, ?\TdbCmsPortalDomains $domain = null)
    {
        $prefixParts = $this->generatePrefixPartsForDomain($portal, $language, $domain);
        if (empty($prefixParts)) {
            return '';
        }

        return '/'.implode('/', $prefixParts);
    }

    /**
     * {@inheritdoc}
     *
     * @return string returns an empty string if either
     *                - the given portal is null
     *                - the given language is null
     *                - the given portal is not set to support multi-language
     *                - the domain associated with the portal has a language setting
     */
    public function getLanguagePrefix(?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null)
    {
        if (null === $portal) {
            return '';
        }

        if (null === $language) {
            return '';
        }

        if (false === $portal->fieldUseMultilanguage) {
            return '';
        }

        $primaryTargetDomain = $this->portalDomainService->getPrimaryDomain($portal->id, $language->id);
        if ('' !== $primaryTargetDomain->fieldCmsLanguageId) {
            return '';
        }

        if ('' === $portal->fieldCmsLanguageId || $portal->fieldCmsLanguageId === $language->id) {
            return '';
        }

        return $language->fieldIso6391;
    }

    public function getUrlLanguagePrefix(?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null)
    {
        return $this->getDomainLanguagePathSegment($portal, $language);
    }

    public function getDomainLanguagePathSegment(?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null, ?\TdbCmsPortalDomains $domain = null)
    {
        return $this->getUrlLanguagePrefixForDomain($portal, $language, $domain);
    }

    private function generatePrefixPartsForDomain(?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null, ?\TdbCmsPortalDomains $domain = null): array
    {
        $portalPrefix = $this->getPortalPrefix($portal);
        $languagePrefix = $this->getDomainLanguagePathSegment($portal, $language, $domain);

        $prefixParts = [];
        if (!empty($portalPrefix)) {
            $prefixParts[] = $portalPrefix;
        }
        if (!empty($languagePrefix)) {
            $prefixParts[] = $languagePrefix;
        }

        return $prefixParts;
    }

    private function getUrlLanguagePrefixForDomain(?\TdbCmsPortal $portal = null, ?\TdbCmsLanguage $language = null, ?\TdbCmsPortalDomains $domain = null)
    {
        if (null === $portal) {
            return '';
        }

        if (null === $language) {
            return '';
        }

        if (false === $portal->fieldUseMultilanguage) {
            return '';
        }

        $targetDomain = $domain ?? $this->getPrimaryTargetDomain($portal, $language);
        if (null !== $targetDomain) {
            $configuredDomainPrefix = $this->getConfiguredDomainPrefix($portal, $targetDomain);
            if (null !== $configuredDomainPrefix) {
                return $configuredDomainPrefix;
            }

            if ('' !== $targetDomain->fieldCmsLanguageId) {
                return '';
            }

            if (null !== $domain) {
                return '';
            }
        }

        if ('' === $portal->fieldCmsLanguageId || $portal->fieldCmsLanguageId === $language->id) {
            return '';
        }

        return $language->fieldIso6391;
    }

    /**
     * {@inheritdoc}
     */
    public function getPortalPrefix(?\TdbCmsPortal $portal = null)
    {
        if (null === $portal) {
            return '';
        }

        return $portal->fieldIdentifier;
    }

    private function getPrimaryTargetDomain(\TdbCmsPortal $portal, \TdbCmsLanguage $language): ?\TdbCmsPortalDomains
    {
        try {
            return $this->portalDomainService->getPrimaryDomain($portal->id, $language->id);
        } catch (InvalidPortalDomainException $e) {
            return null;
        }
    }

    private function getConfiguredDomainPrefix(\TdbCmsPortal $portal, \TdbCmsPortalDomains $primaryTargetDomain): ?string
    {
        $domainCandidates = $this->getDomainFamilyCandidates($portal->id, $primaryTargetDomain);
        foreach ($domainCandidates as $domainCandidate) {
            if ('' !== trim((string) ($domainCandidate['url_suffix'] ?? ''))) {
                return trim((string) $primaryTargetDomain->getUrlSuffix());
            }
        }

        return null;
    }

    /**
     * @return array<int, array<string, mixed>>
     */
    private function getDomainFamilyCandidates(string $portalId, \TdbCmsPortalDomains $primaryTargetDomain): array
    {
        $domainCandidatesById = [];
        foreach ($this->getDomainHosts($primaryTargetDomain) as $host) {
            $domainCandidates = $this->cmsPortalDomainsDataAccess->getDomainCandidatesByHostAndPortal($host, $portalId);
            foreach ($domainCandidates as $domainCandidate) {
                $domainCandidatesById[(string) $domainCandidate['id']] = $domainCandidate;
            }
        }

        return array_values($domainCandidatesById);
    }

    /**
     * @return string[]
     */
    private function getDomainHosts(\TdbCmsPortalDomains $primaryTargetDomain): array
    {
        $hosts = [];
        foreach ([$primaryTargetDomain->getInsecureDomainName(), $primaryTargetDomain->getSecureDomainName()] as $host) {
            $host = trim((string) $host);
            if ('' === $host) {
                continue;
            }
            $hosts[$host] = $host;
        }

        return array_values($hosts);
    }
}
