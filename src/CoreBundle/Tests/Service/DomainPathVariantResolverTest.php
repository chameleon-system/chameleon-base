<?php

declare(strict_types=1);

namespace ChameleonSystem\CoreBundle\Tests\Service;

use ChameleonSystem\CoreBundle\Service\DomainPathMatch;
use ChameleonSystem\CoreBundle\Service\DomainPathVariantResolver;
use PHPUnit\Framework\TestCase;

class DomainPathVariantResolverTest extends TestCase
{
    private DomainPathVariantResolver $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->subject = new DomainPathVariantResolver();
    }

    public function testResolvesSuffixlessDefaultDomain(): void
    {
        $result = $this->subject->resolve('www.tischwelt.ch', '/foo', [
            $this->createDomainCandidate('domain-de', 'portal-1', 'lang-de', ''),
        ]);

        $this->assertResolvedMatch(
            $result,
            'domain-de',
            'portal-1',
            'lang-de',
            '',
            '',
            '/foo',
            '',
            DomainPathMatch::MATCH_TYPE_HOST_MATCH_WITHOUT_SUFFIX
        );
    }

    public function testResolvesConfiguredDomainSuffix(): void
    {
        $result = $this->subject->resolve('www.tischwelt.ch', '/fr/foo', [
            $this->createDomainCandidate('domain-de', 'portal-1', 'lang-de', ''),
            $this->createDomainCandidate('domain-fr', 'portal-1', 'lang-fr', 'fr', false),
        ]);

        $this->assertResolvedMatch(
            $result,
            'domain-fr',
            'portal-1',
            'lang-fr',
            '',
            'fr',
            '/foo',
            '/fr',
            DomainPathMatch::MATCH_TYPE_HOST_MATCH_WITH_DOMAIN_SUFFIX
        );
    }

    public function testResolvesConfiguredDomainSuffixCaseInsensitivelyToCanonicalCase(): void
    {
        $result = $this->subject->resolve('www.tischwelt.ch', '/FR/foo', [
            $this->createDomainCandidate('domain-de', 'portal-1', 'lang-de', ''),
            $this->createDomainCandidate('domain-fr', 'portal-1', 'lang-fr', 'fr', false),
        ]);

        $this->assertResolvedMatch(
            $result,
            'domain-fr',
            'portal-1',
            'lang-fr',
            '',
            'fr',
            '/foo',
            '/fr',
            DomainPathMatch::MATCH_TYPE_HOST_MATCH_WITH_DOMAIN_SUFFIX
        );
    }

    public function testUnknownSegmentStaysInRemainingPath(): void
    {
        $result = $this->subject->resolve('www.tischwelt.ch', '/frankfurt/foo', [
            $this->createDomainCandidate('domain-de', 'portal-1', 'lang-de', ''),
            $this->createDomainCandidate('domain-fr', 'portal-1', 'lang-fr', 'fr', false),
        ]);

        $this->assertResolvedMatch(
            $result,
            'domain-de',
            'portal-1',
            'lang-de',
            '',
            '',
            '/frankfurt/foo',
            '',
            DomainPathMatch::MATCH_TYPE_HOST_MATCH_WITHOUT_SUFFIX
        );
    }

    public function testPortalIdentifierAndDomainSuffixAreResolvedSeparately(): void
    {
        $result = $this->subject->resolve(
            'www.tischwelt.ch',
            '/shop/fr/foo',
            [
                $this->createDomainCandidate('domain-de', 'portal-1', 'lang-de', ''),
                $this->createDomainCandidate('domain-fr', 'portal-1', 'lang-fr', 'fr', false),
            ],
            ['shop' => 'portal-1']
        );

        $this->assertResolvedMatch(
            $result,
            'domain-fr',
            'portal-1',
            'lang-fr',
            'shop',
            'fr',
            '/foo',
            '/shop/fr',
            DomainPathMatch::MATCH_TYPE_HOST_MATCH_WITH_PORTAL_IDENTIFIER_AND_DOMAIN_SUFFIX
        );
    }

    public function testPortalIdentifierWithoutDomainSuffixFallsBackToSuffixlessDomain(): void
    {
        $result = $this->subject->resolve(
            'www.tischwelt.ch',
            '/shop/foo',
            [
                $this->createDomainCandidate('domain-de', 'portal-1', 'lang-de', ''),
                $this->createDomainCandidate('domain-fr', 'portal-1', 'lang-fr', 'fr', false),
            ],
            ['shop' => 'portal-1']
        );

        $this->assertResolvedMatch(
            $result,
            'domain-de',
            'portal-1',
            'lang-de',
            'shop',
            '',
            '/foo',
            '/shop',
            DomainPathMatch::MATCH_TYPE_HOST_MATCH_WITH_PORTAL_IDENTIFIER
        );
    }

    public function testPortalIdentifierKeepsUnknownSecondSegmentInRemainingPath(): void
    {
        $result = $this->subject->resolve(
            'www.tischwelt.ch',
            '/shop/frankfurt/',
            [
                $this->createDomainCandidate('domain-de', 'portal-1', 'lang-de', ''),
                $this->createDomainCandidate('domain-fr', 'portal-1', 'lang-fr', 'fr', false),
            ],
            ['shop' => 'portal-1']
        );

        $this->assertResolvedMatch(
            $result,
            'domain-de',
            'portal-1',
            'lang-de',
            'shop',
            '',
            '/frankfurt',
            '/shop',
            DomainPathMatch::MATCH_TYPE_HOST_MATCH_WITH_PORTAL_IDENTIFIER
        );
    }

    public function testExplicitDefaultSuffixDoesNotRequireSuffixlessFallback(): void
    {
        $result = $this->subject->resolve('www.tischwelt.ch', '/de/foo', [
            $this->createDomainCandidate('domain-de', 'portal-1', 'lang-de', 'de'),
            $this->createDomainCandidate('domain-fr', 'portal-1', 'lang-fr', 'fr', false),
        ]);

        $this->assertResolvedMatch(
            $result,
            'domain-de',
            'portal-1',
            'lang-de',
            '',
            'de',
            '/foo',
            '/de',
            DomainPathMatch::MATCH_TYPE_HOST_MATCH_WITH_DOMAIN_SUFFIX
        );
    }

    public function testNoSuffixlessCandidateDoesNotAutoMatchDefaultLanguage(): void
    {
        $result = $this->subject->resolve('www.tischwelt.ch', '/foo', [
            $this->createDomainCandidate('domain-de', 'portal-1', 'lang-de', 'de'),
            $this->createDomainCandidate('domain-fr', 'portal-1', 'lang-fr', 'fr', false),
        ]);

        self::assertNull($result->getMatchedDomain());
        self::assertNull($result->getMatchedDomainId());
        self::assertSame('/foo', $result->getRemainingPath());
        self::assertSame('', $result->getCanonicalPrefix());
        self::assertSame(DomainPathMatch::MATCH_TYPE_NO_MATCH, $result->getMatchType());
        self::assertFalse($result->isMatched());
        self::assertTrue($result->isAmbiguous());
    }

    public function testDoubleSlashesAreIgnoredWhileParsingSegments(): void
    {
        $result = $this->subject->resolve('www.tischwelt.ch', '//fr//foo', [
            $this->createDomainCandidate('domain-de', 'portal-1', 'lang-de', ''),
            $this->createDomainCandidate('domain-fr', 'portal-1', 'lang-fr', 'fr', false),
        ]);

        $this->assertResolvedMatch(
            $result,
            'domain-fr',
            'portal-1',
            'lang-fr',
            '',
            'fr',
            '/foo',
            '/fr',
            DomainPathMatch::MATCH_TYPE_HOST_MATCH_WITH_DOMAIN_SUFFIX
        );
    }

    public function testPortalIdentifierFiltersCandidatesAcrossPortals(): void
    {
        $result = $this->subject->resolve(
            'www.tischwelt.ch',
            '/shop/fr/foo',
            [
                $this->createDomainCandidate('domain-shop-fr', 'portal-1', 'lang-fr', 'fr'),
                $this->createDomainCandidate('domain-outlet-fr', 'portal-2', 'lang-fr', 'fr'),
            ],
            [
                'shop' => 'portal-1',
                'outlet' => 'portal-2',
            ]
        );

        $this->assertResolvedMatch(
            $result,
            'domain-shop-fr',
            'portal-1',
            'lang-fr',
            'shop',
            'fr',
            '/foo',
            '/shop/fr',
            DomainPathMatch::MATCH_TYPE_HOST_MATCH_WITH_PORTAL_IDENTIFIER_AND_DOMAIN_SUFFIX
        );
    }

    public function testAmbiguousSuffixlessCandidatesAreReturnedDefensively(): void
    {
        $result = $this->subject->resolve('www.tischwelt.ch', '/foo', [
            $this->createDomainCandidate('domain-portal-1', 'portal-1', 'lang-de', '', false),
            $this->createDomainCandidate('domain-portal-2', 'portal-2', 'lang-de', '', false),
        ]);

        self::assertNull($result->getMatchedDomain());
        self::assertNull($result->getMatchedDomainId());
        self::assertNull($result->getMatchedPortalId());
        self::assertSame('/foo', $result->getRemainingPath());
        self::assertSame('', $result->getCanonicalPrefix());
        self::assertSame(DomainPathMatch::MATCH_TYPE_AMBIGUOUS, $result->getMatchType());
        self::assertFalse($result->isMatched());
        self::assertTrue($result->isAmbiguous());
    }

    public function testQueryStringIsIgnoredDuringParsing(): void
    {
        $result = $this->subject->resolve('www.tischwelt.ch', '/fr/foo?bar=baz', [
            $this->createDomainCandidate('domain-de', 'portal-1', 'lang-de', ''),
            $this->createDomainCandidate('domain-fr', 'portal-1', 'lang-fr', 'fr', false),
        ]);

        $this->assertResolvedMatch(
            $result,
            'domain-fr',
            'portal-1',
            'lang-fr',
            '',
            'fr',
            '/foo',
            '/fr',
            DomainPathMatch::MATCH_TYPE_HOST_MATCH_WITH_DOMAIN_SUFFIX
        );
    }

    private function assertResolvedMatch(
        DomainPathMatch $result,
        string $expectedDomainId,
        string $expectedPortalId,
        string $expectedLanguageId,
        string $expectedPortalIdentifier,
        string $expectedDomainSuffix,
        string $expectedRemainingPath,
        string $expectedCanonicalPrefix,
        string $expectedMatchType
    ): void {
        self::assertNotNull($result->getMatchedDomain());
        self::assertSame($expectedDomainId, $result->getMatchedDomainId());
        self::assertSame($expectedPortalId, $result->getMatchedPortalId());
        self::assertSame($expectedLanguageId, $result->getMatchedLanguageId());
        self::assertSame($expectedPortalIdentifier, $result->getConsumedPortalIdentifier());
        self::assertSame($expectedDomainSuffix, $result->getConsumedDomainSuffix());
        self::assertSame($expectedRemainingPath, $result->getRemainingPath());
        self::assertSame($expectedCanonicalPrefix, $result->getCanonicalPrefix());
        self::assertSame($expectedMatchType, $result->getMatchType());
        self::assertSame('' !== $expectedPortalIdentifier, $result->hasPortalIdentifier());
        self::assertSame('' !== $expectedDomainSuffix, $result->hasDomainSuffix());
        self::assertTrue($result->isMatched());
        self::assertFalse($result->isAmbiguous());
    }

    /**
     * @return array<string, string>
     */
    private function createDomainCandidate(
        string $id,
        string $portalId,
        string $languageId,
        string $urlSuffix,
        bool $isMasterDomain = true,
        string $host = 'www.tischwelt.ch'
    ): array {
        return [
            'id' => $id,
            'cms_portal_id' => $portalId,
            'cms_language_id' => $languageId,
            'name' => $host,
            'sslname' => '',
            'url_suffix' => $urlSuffix,
            'is_master_domain' => $isMasterDomain ? '1' : '0',
        ];
    }
}
