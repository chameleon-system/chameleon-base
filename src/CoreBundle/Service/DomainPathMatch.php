<?php

declare(strict_types=1);

namespace ChameleonSystem\CoreBundle\Service;

class DomainPathMatch
{
    public const REQUEST_ATTRIBUTE_NAME = 'chameleon.domain_path_match';

    public const MATCH_TYPE_NO_MATCH = 'no_match';
    public const MATCH_TYPE_AMBIGUOUS = 'ambiguous';
    public const MATCH_TYPE_HOST_MATCH_WITHOUT_SUFFIX = 'host_match_without_suffix';
    public const MATCH_TYPE_HOST_MATCH_WITH_DOMAIN_SUFFIX = 'host_match_with_domain_suffix';
    public const MATCH_TYPE_HOST_MATCH_WITH_PORTAL_IDENTIFIER = 'host_match_with_portal_identifier';
    public const MATCH_TYPE_HOST_MATCH_WITH_PORTAL_IDENTIFIER_AND_DOMAIN_SUFFIX = 'host_match_with_portal_identifier_and_domain_suffix';

    /**
     * @param array<string, mixed>|null $matchedDomain
     */
    public function __construct(
        private ?array $matchedDomain,
        private ?string $matchedDomainId,
        private ?string $matchedPortalId,
        private ?string $matchedLanguageId,
        private string $consumedPortalIdentifier,
        private string $consumedDomainSuffix,
        private string $remainingPath,
        private string $canonicalPrefix,
        private bool $hasPortalIdentifier,
        private bool $hasDomainSuffix,
        private bool $isMatched,
        private string $matchType,
        private bool $isAmbiguous
    ) {

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

    public function isMatched(): bool
    {
        return $this->isMatched;
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
            'isMatched' => $this->isMatched,
            'isDomainVariantMatched' => $this->isMatched,
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
            $data['matchedDomainId'] ?? null,
            $data['matchedPortalId'] ?? null,
            $data['matchedLanguageId'] ?? null,
            (string) ($data['consumedPortalIdentifier'] ?? ''),
            (string) ($data['consumedDomainSuffix'] ?? ''),
            (string) ($data['remainingPath'] ?? '/'),
            (string) ($data['canonicalPrefix'] ?? ''),
            true === ($data['hasPortalIdentifier'] ?? false),
            true === ($data['hasDomainSuffix'] ?? false),
            true === ($data['isMatched'] ?? $data['isDomainVariantMatched'] ?? false),
            (string) ($data['matchType'] ?? self::MATCH_TYPE_NO_MATCH),
            true === ($data['isAmbiguous'] ?? false)
        );
    }
}
