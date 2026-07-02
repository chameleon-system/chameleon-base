<?php

declare(strict_types=1);

namespace ChameleonSystem\CoreBundle\Service;

class DomainPathVariantResolver
{
    /**
     * @param array<int, array<string, mixed>> $domainCandidates
     * @param array<string, string>            $portalIdentifiers
     */
    public function resolve(
        string $host,
        string $path,
        array $domainCandidates,
        array $portalIdentifiers = []
    ): DomainPathVariantResolutionResult {
        $pathSegments = $this->getPathSegments($path);
        $domainCandidates = $this->filterCandidatesForHost($host, $domainCandidates);

        $consumedPortalIdentifier = '';
        $matchedPortalId = null;
        $hasPortalIdentifier = false;
        $pathOffset = 0;

        if (isset($pathSegments[0], $portalIdentifiers[$pathSegments[0]])) {
            $consumedPortalIdentifier = $pathSegments[0];
            $matchedPortalId = (string) $portalIdentifiers[$consumedPortalIdentifier];
            $hasPortalIdentifier = true;
            $pathOffset = 1;
            $domainCandidates = $this->filterCandidatesByPortalId($domainCandidates, $matchedPortalId);
        }

        $consumedDomainSuffix = '';
        $hasDomainSuffix = false;
        $matchedDomain = null;
        $isAmbiguous = false;

        $suffixSegment = $pathSegments[$pathOffset] ?? null;
        if (null !== $suffixSegment) {
            $suffixCandidates = $this->filterCandidatesBySuffix($domainCandidates, $suffixSegment);
            if ([] !== $suffixCandidates) {
                $consumedDomainSuffix = $suffixSegment;
                $hasDomainSuffix = true;
                $pathOffset++;

                $candidateSelection = $this->selectCandidate($suffixCandidates, false);
                $matchedDomain = $candidateSelection['candidate'];
                $isAmbiguous = $candidateSelection['isAmbiguous'];
            }
        }

        if (null === $matchedDomain && false === $isAmbiguous) {
            $suffixlessCandidates = $this->filterCandidatesBySuffix($domainCandidates, '');
            if ([] !== $suffixlessCandidates) {
                $candidateSelection = $this->selectCandidate($suffixlessCandidates, true);
                $matchedDomain = $candidateSelection['candidate'];
                $isAmbiguous = $candidateSelection['isAmbiguous'];
            }
        }

        $remainingPath = $this->buildPath(\array_slice($pathSegments, $pathOffset));

        if (true === $isAmbiguous) {
            return new DomainPathVariantResolutionResult(
                null,
                null,
                $matchedPortalId,
                null,
                $consumedPortalIdentifier,
                $consumedDomainSuffix,
                $remainingPath,
                '',
                $hasPortalIdentifier,
                $hasDomainSuffix,
                false,
                DomainPathVariantResolutionResult::MATCH_TYPE_AMBIGUOUS,
                true
            );
        }

        if (null === $matchedDomain) {
            return new DomainPathVariantResolutionResult(
                null,
                null,
                $matchedPortalId,
                null,
                $consumedPortalIdentifier,
                $consumedDomainSuffix,
                $remainingPath,
                '',
                $hasPortalIdentifier,
                $hasDomainSuffix,
                false,
                DomainPathVariantResolutionResult::MATCH_TYPE_NO_MATCH,
                false
            );
        }

        $matchedPortalId = (string) ($matchedDomain['cms_portal_id'] ?? $matchedPortalId);
        $matchedDomainId = $this->getNullableStringValue($matchedDomain, 'id');
        $matchedLanguageId = $this->getNullableStringValue($matchedDomain, 'cms_language_id');

        return new DomainPathVariantResolutionResult(
            $matchedDomain,
            $matchedDomainId,
            $matchedPortalId,
            $matchedLanguageId,
            $consumedPortalIdentifier,
            $consumedDomainSuffix,
            $remainingPath,
            $this->buildCanonicalPrefix($consumedPortalIdentifier, $consumedDomainSuffix),
            $hasPortalIdentifier,
            $hasDomainSuffix,
            true,
            $this->determineMatchType($hasPortalIdentifier, $hasDomainSuffix),
            false
        );
    }

    /**
     * @param array<int, array<string, mixed>> $domainCandidates
     *
     * @return array<int, array<string, mixed>>
     */
    private function filterCandidatesForHost(string $host, array $domainCandidates): array
    {
        if ('' === $host) {
            return [];
        }

        return \array_values(\array_filter(
            $domainCandidates,
            static function (array $candidate) use ($host): bool {
                return $host === (string) ($candidate['name'] ?? '')
                    || $host === (string) ($candidate['sslname'] ?? '');
            }
        ));
    }

    /**
     * @param array<int, array<string, mixed>> $domainCandidates
     *
     * @return array<int, array<string, mixed>>
     */
    private function filterCandidatesByPortalId(array $domainCandidates, string $portalId): array
    {
        return \array_values(\array_filter(
            $domainCandidates,
            static fn (array $candidate): bool => $portalId === (string) ($candidate['cms_portal_id'] ?? '')
        ));
    }

    /**
     * @param array<int, array<string, mixed>> $domainCandidates
     *
     * @return array<int, array<string, mixed>>
     */
    private function filterCandidatesBySuffix(array $domainCandidates, string $suffix): array
    {
        return \array_values(\array_filter(
            $domainCandidates,
            static fn (array $candidate): bool => $suffix === (string) ($candidate['url_suffix'] ?? '')
        ));
    }

    /**
     * @param array<int, array<string, mixed>> $candidates
     *
     * @return array{candidate: array<string, mixed>|null, isAmbiguous: bool}
     */
    private function selectCandidate(array $candidates, bool $allowMasterDomainTieBreaker): array
    {
        if (0 === \count($candidates)) {
            return [
                'candidate' => null,
                'isAmbiguous' => false,
            ];
        }

        if (1 === \count($candidates)) {
            return [
                'candidate' => $candidates[0],
                'isAmbiguous' => false,
            ];
        }

        if (false === $allowMasterDomainTieBreaker || true === $this->containsMultiplePortalIds($candidates)) {
            return [
                'candidate' => null,
                'isAmbiguous' => true,
            ];
        }

        $masterCandidates = \array_values(\array_filter(
            $candidates,
            static fn (array $candidate): bool => '1' === (string) ($candidate['is_master_domain'] ?? '')
        ));

        if (1 === \count($masterCandidates)) {
            return [
                'candidate' => $masterCandidates[0],
                'isAmbiguous' => false,
            ];
        }

        return [
            'candidate' => null,
            'isAmbiguous' => true,
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $candidates
     */
    private function containsMultiplePortalIds(array $candidates): bool
    {
        $portalIds = [];
        foreach ($candidates as $candidate) {
            $portalIds[(string) ($candidate['cms_portal_id'] ?? '')] = true;
        }

        return \count($portalIds) > 1;
    }

    /**
     * @return string[]
     */
    private function getPathSegments(string $path): array
    {
        $pathPart = parse_url($path, \PHP_URL_PATH);
        if (false === $pathPart || null === $pathPart) {
            $pathPart = '';
        }

        $segments = \array_filter(explode('/', trim($pathPart, '/')), static fn (string $segment): bool => '' !== $segment);

        return \array_values($segments);
    }

    /**
     * @param string[] $segments
     */
    private function buildPath(array $segments): string
    {
        if ([] === $segments) {
            return '/';
        }

        return '/'.implode('/', $segments);
    }

    private function buildCanonicalPrefix(string $portalIdentifier, string $domainSuffix): string
    {
        $prefixParts = [];
        if ('' !== $portalIdentifier) {
            $prefixParts[] = $portalIdentifier;
        }
        if ('' !== $domainSuffix) {
            $prefixParts[] = $domainSuffix;
        }

        if ([] === $prefixParts) {
            return '';
        }

        return '/'.implode('/', $prefixParts);
    }

    private function determineMatchType(bool $hasPortalIdentifier, bool $hasDomainSuffix): string
    {
        if ($hasPortalIdentifier && $hasDomainSuffix) {
            return DomainPathVariantResolutionResult::MATCH_TYPE_HOST_MATCH_WITH_PORTAL_IDENTIFIER_AND_DOMAIN_SUFFIX;
        }

        if ($hasPortalIdentifier) {
            return DomainPathVariantResolutionResult::MATCH_TYPE_HOST_MATCH_WITH_PORTAL_IDENTIFIER;
        }

        if ($hasDomainSuffix) {
            return DomainPathVariantResolutionResult::MATCH_TYPE_HOST_MATCH_WITH_DOMAIN_SUFFIX;
        }

        return DomainPathVariantResolutionResult::MATCH_TYPE_HOST_MATCH_WITHOUT_SUFFIX;
    }

    /**
     * @param array<string, mixed> $candidate
     */
    private function getNullableStringValue(array $candidate, string $key): ?string
    {
        if (false === \array_key_exists($key, $candidate)) {
            return null;
        }

        $value = $candidate[$key];
        if (null === $value || '' === $value) {
            return null;
        }

        return (string) $value;
    }
}
