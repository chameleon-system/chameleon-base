<?php

declare(strict_types=1);

namespace ChameleonSystem\CoreBundle\Service;

class DomainPathVariantResolutionResult
{
    public const MATCH_TYPE_NO_MATCH = 'no_match';
    public const MATCH_TYPE_AMBIGUOUS = 'ambiguous';
    public const MATCH_TYPE_HOST_MATCH_WITHOUT_SUFFIX = 'host_match_without_suffix';
    public const MATCH_TYPE_HOST_MATCH_WITH_DOMAIN_SUFFIX = 'host_match_with_domain_suffix';
    public const MATCH_TYPE_HOST_MATCH_WITH_PORTAL_IDENTIFIER = 'host_match_with_portal_identifier';
    public const MATCH_TYPE_HOST_MATCH_WITH_PORTAL_IDENTIFIER_AND_DOMAIN_SUFFIX = 'host_match_with_portal_identifier_and_domain_suffix';

    /**
     * @var array<string, mixed>|null
     */
    private ?array $matchedDomain;

    private ?string $matchedDomainId;

    private ?string $matchedPortalId;

    private ?string $matchedLanguageId;

    private string $consumedPortalIdentifier;

    private string $consumedDomainSuffix;

    private string $remainingPath;

    private string $canonicalPrefix;

    private bool $hasPortalIdentifier;

    private bool $hasDomainSuffix;

    private bool $isDomainVariantMatched;

    private string $matchType;

    private bool $isAmbiguous;

    /**
     * @param array<string, mixed>|null $matchedDomain
     */
    public function __construct(
        ?array $matchedDomain,
        ?string $matchedDomainId,
        ?string $matchedPortalId,
        ?string $matchedLanguageId,
        string $consumedPortalIdentifier,
        string $consumedDomainSuffix,
        string $remainingPath,
        string $canonicalPrefix,
        bool $hasPortalIdentifier,
        bool $hasDomainSuffix,
        bool $isDomainVariantMatched,
        string $matchType,
        bool $isAmbiguous
    ) {
        $this->matchedDomain = $matchedDomain;
        $this->matchedDomainId = $matchedDomainId;
        $this->matchedPortalId = $matchedPortalId;
        $this->matchedLanguageId = $matchedLanguageId;
        $this->consumedPortalIdentifier = $consumedPortalIdentifier;
        $this->consumedDomainSuffix = $consumedDomainSuffix;
        $this->remainingPath = $remainingPath;
        $this->canonicalPrefix = $canonicalPrefix;
        $this->hasPortalIdentifier = $hasPortalIdentifier;
        $this->hasDomainSuffix = $hasDomainSuffix;
        $this->isDomainVariantMatched = $isDomainVariantMatched;
        $this->matchType = $matchType;
        $this->isAmbiguous = $isAmbiguous;
    }

    /**
     * @return array<string, mixed>|null
     */
    public function getMatchedDomain(): ?array
    {
        return $this->matchedDomain;
    }

    public function getMatchedDomainId(): ?string
    {
        return $this->matchedDomainId;
    }

    public function getMatchedPortalId(): ?string
    {
        return $this->matchedPortalId;
    }

    public function getMatchedLanguageId(): ?string
    {
        return $this->matchedLanguageId;
    }

    public function getConsumedPortalIdentifier(): string
    {
        return $this->consumedPortalIdentifier;
    }

    public function getConsumedDomainSuffix(): string
    {
        return $this->consumedDomainSuffix;
    }

    public function getRemainingPath(): string
    {
        return $this->remainingPath;
    }

    public function getCanonicalPrefix(): string
    {
        return $this->canonicalPrefix;
    }

    public function hasPortalIdentifier(): bool
    {
        return $this->hasPortalIdentifier;
    }

    public function hasDomainSuffix(): bool
    {
        return $this->hasDomainSuffix;
    }

    public function isDomainVariantMatched(): bool
    {
        return $this->isDomainVariantMatched;
    }

    public function getMatchType(): string
    {
        return $this->matchType;
    }

    public function isAmbiguous(): bool
    {
        return $this->isAmbiguous;
    }
}
