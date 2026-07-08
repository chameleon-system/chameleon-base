<?php

declare(strict_types=1);

namespace ChameleonSystem\CoreBundle\Service;

class DomainPathVariantResolver
{
    /**
     * @param array<int, array<string, mixed>> $domainCandidates
     * @param array<string, string> $portalIdentifiers
     */
    public function resolve(
        string $host,
        string $path,
        array $domainCandidates,
        array $portalIdentifiers = []
    ): DomainPathMatch {
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
            if ([] === $suffixCandidates) {
                $suffixCandidates = $this->filterCandidatesBySuffixCaseInsensitive($domainCandidates, $suffixSegment);
            }
            if ([] !== $suffixCandidates) {
                $hasDomainSuffix = true;
                ++$pathOffset;

                $candidateSelection = $this->identifySuitableCandiate($suffixCandidates, false);
                $matchedDomain = $candidateSelection['candidate'];
                $isAmbiguous = $candidateSelection['isAmbiguous'];
                $consumedDomainSuffix = $this->resolveConsumedDomainSuffix($suffixSegment, $suffixCandidates, $matchedDomain);
            }
        }

        if (null === $matchedDomain && false === $isAmbiguous) {
            $suffixlessCandidates = $this->filterCandidatesBySuffix($domainCandidates, '');
            if ([] !== $suffixlessCandidates) {
                $candidateSelection = $this->identifySuitableCandiate($suffixlessCandidates, true);
                $matchedDomain = $candidateSelection['candidate'];
                $isAmbiguous = $candidateSelection['isAmbiguous'];
            }
        }

        $remainingPath = '/'.\implode('/', \array_slice($pathSegments, $pathOffset));

        if (true === $isAmbiguous || null === $matchedDomain) {
            $matchType = (true === $isAmbiguous) ?
                DomainPathMatch::MATCH_TYPE_AMBIGUOUS :
                DomainPathMatch::MATCH_TYPE_NO_MATCH;

            return new DomainPathMatch(
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
                $matchType,
                true
            );
        }

        $matchedPortalId = (string) ($matchedDomain['cms_portal_id'] ?? $matchedPortalId);
        $matchedDomainId = $matchedDomain['id'] ?? null;
        $matchedLanguageId = $matchedDomain['cms_language_id'] ?? null;

        return new DomainPathMatch(
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
        $filteredCandidates = [];

        foreach ($domainCandidates as $candidate) {
            if ($portalId !== (string) ($candidate['cms_portal_id'] ?? '')) {
                continue;
            }

            $filteredCandidates[] = $candidate;
        }

        return $filteredCandidates;
    }

    /**
     * @param array<int, array<string, mixed>> $domainCandidates
     *
     * @return array<int, array<string, mixed>>
     */
    private function filterCandidatesBySuffix(array $domainCandidates, string $suffix): array
    {
        $filteredCandidates = [];

        foreach ($domainCandidates as $candidate) {
            if ($suffix !== (string) ($candidate['url_suffix'] ?? '')) {
                continue;
            }

            $filteredCandidates[] = $candidate;
        }

        return $filteredCandidates;
    }

    /**
     * @param array<int, array<string, mixed>> $domainCandidates
     *
     * @return array<int, array<string, mixed>>
     */
    private function filterCandidatesBySuffixCaseInsensitive(array $domainCandidates, string $suffix): array
    {
        $filteredCandidates = [];

        foreach ($domainCandidates as $candidate) {
            $candidateSuffix = (string) ($candidate['url_suffix'] ?? '');

            if ('' === $candidateSuffix) {
                continue;
            }

            if (0 !== \strcasecmp($suffix, $candidateSuffix)) {
                continue;
            }

            $filteredCandidates[] = $candidate;
        }

        return $filteredCandidates;
    }

    /**
     * @param array<int, array<string, mixed>> $candidates
     *
     * @return array{candidate: array<string, mixed>|null, isAmbiguous: bool}
     */
    private function identifySuitableCandiate(array $candidates, bool $allowMasterDomainTieBreaker): array
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
        $pathPart = $path;
        if (false === str_starts_with($pathPart, '/')) {
            $parsedPath = parse_url($pathPart, \PHP_URL_PATH);
            if (false === $parsedPath || null === $parsedPath) {
                $pathPart = '';
            } else {
                $pathPart = $parsedPath;
            }
        } else {
            $queryPosition = strpos($pathPart, '?');
            if (false !== $queryPosition) {
                $pathPart = substr($pathPart, 0, $queryPosition);
            }

            $fragmentPosition = strpos($pathPart, '#');
            if (false !== $fragmentPosition) {
                $pathPart = substr($pathPart, 0, $fragmentPosition);
            }
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
            return DomainPathMatch::MATCH_TYPE_HOST_MATCH_WITH_PORTAL_IDENTIFIER_AND_DOMAIN_SUFFIX;
        }

        if ($hasPortalIdentifier) {
            return DomainPathMatch::MATCH_TYPE_HOST_MATCH_WITH_PORTAL_IDENTIFIER;
        }

        if ($hasDomainSuffix) {
            return DomainPathMatch::MATCH_TYPE_HOST_MATCH_WITH_DOMAIN_SUFFIX;
        }

        return DomainPathMatch::MATCH_TYPE_HOST_MATCH_WITHOUT_SUFFIX;
    }

    /**
     * @param array<int, array<string, mixed>> $suffixCandidates
     * @param array<string, mixed>|null $matchedDomain
     */
    private function resolveConsumedDomainSuffix(string $requestedSuffix, array $suffixCandidates, ?array $matchedDomain): string
    {
        if (null !== $matchedDomain) {
            return (string) ($matchedDomain['url_suffix'] ?? $requestedSuffix);
        }

        $configuredSuffixes = [];
        foreach ($suffixCandidates as $suffixCandidate) {
            $configuredSuffix = (string) ($suffixCandidate['url_suffix'] ?? '');
            if ('' === $configuredSuffix) {
                continue;
            }
            $configuredSuffixes[$configuredSuffix] = $configuredSuffix;
        }

        if (1 === \count($configuredSuffixes)) {
            return \array_values($configuredSuffixes)[0];
        }

        return $requestedSuffix;
    }
}
