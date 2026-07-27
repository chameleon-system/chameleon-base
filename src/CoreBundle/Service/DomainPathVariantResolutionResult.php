<?php

declare(strict_types=1);

namespace ChameleonSystem\CoreBundle\Service;

class DomainPathVariantResolutionResult
{
    public const REQUEST_ATTRIBUTE_NAME = 'chameleon.domain_path_variant_resolution';

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

    /**
     * @return array<string, mixed>
     */
    public function toArray(): array
    {
        return [
            'matchedDomain' => $this->matchedDomain,
            'matchedDomainId' => $this->matchedDomainId,
            'matchedPortalId' => $this->matchedPortalId,
            'matchedLanguageId' => $this->matchedLanguageId,
            'consumedPortalIdentifier' => $this->consumedPortalIdentifier,
            'consumedDomainSuffix' => $this->consumedDomainSuffix,
            'remainingPath' => $this->remainingPath,
            'canonicalPrefix' => $this->canonicalPrefix,
            'hasPortalIdentifier' => $this->hasPortalIdentifier,
            'hasDomainSuffix' => $this->hasDomainSuffix,
            'isDomainVariantMatched' => $this->isDomainVariantMatched,
            'matchType' => $this->matchType,
            'isAmbiguous' => $this->isAmbiguous,
        ];
    }

    /**
     * @param array<string, mixed> $data
     */
    public static function createFromArray(array $data): self
    {
        $matchedDomain = $data['matchedDomain'] ?? null;
        if (null !== $matchedDomain && false === \is_array($matchedDomain)) {
            $matchedDomain = null;
        }

        return new self(
            $matchedDomain,
            self::getNullableString($data, 'matchedDomainId'),
            self::getNullableString($data, 'matchedPortalId'),
            self::getNullableString($data, 'matchedLanguageId'),
            (string) ($data['consumedPortalIdentifier'] ?? ''),
            (string) ($data['consumedDomainSuffix'] ?? ''),
            (string) ($data['remainingPath'] ?? '/'),
            (string) ($data['canonicalPrefix'] ?? ''),
            true === ($data['hasPortalIdentifier'] ?? false),
            true === ($data['hasDomainSuffix'] ?? false),
            true === ($data['isDomainVariantMatched'] ?? false),
            (string) ($data['matchType'] ?? self::MATCH_TYPE_NO_MATCH),
            true === ($data['isAmbiguous'] ?? false)
        );
    }

    /**
     * @param array<string, mixed> $data
     */
    private static function getNullableString(array $data, string $key): ?string
    {
        $value = $data[$key] ?? null;
        if (null === $value) {
            return null;
        }

        return (string) $value;
    }
}
