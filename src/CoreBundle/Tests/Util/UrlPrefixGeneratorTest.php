<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Tests\Util;

use ChameleonSystem\CoreBundle\DataAccess\CmsPortalDomainsDataAccessInterface;
use ChameleonSystem\CoreBundle\Exception\InvalidPortalDomainException;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Util\UrlPrefixGenerator;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class UrlPrefixGeneratorTest extends TestCase
{
    use ProphecyTrait;

    public function testKeepsLegacyPortalLanguagePrefixBehaviorWithoutDomainSuffixConfiguration(): void
    {
        $portal = $this->createPortal('portal-id', 'shop', 'de-id', true);
        $language = $this->createLanguage('fr-id', 'fr');
        $domain = $this->createDomain('', '', 'www.example.com', 'www.example.com');

        $portalDomainService = $this->prophesize(PortalDomainServiceInterface::class);
        $portalDomainService->getPrimaryDomain('portal-id', 'fr-id')->willReturn($domain);

        $domainDataAccess = $this->prophesize(CmsPortalDomainsDataAccessInterface::class);
        $domainDataAccess->getDomainCandidatesByHostAndPortal('www.example.com', 'portal-id')->willReturn([
            ['id' => '1', 'url_suffix' => ''],
            ['id' => '2', 'url_suffix' => ''],
        ]);

        $subject = new UrlPrefixGenerator($portalDomainService->reveal(), $domainDataAccess->reveal());

        self::assertSame('fr', $subject->getLanguagePrefix($portal, $language));
        self::assertSame('fr', $subject->getDomainLanguagePathSegment($portal, $language));
        self::assertSame('/shop/fr', $subject->getPathPrefix($portal, $language));
        self::assertSame('/shop/fr', $subject->generatePrefix($portal, $language));
    }

    public function testUsesDomainSuffixConfigurationForSuffixlessDefaultLanguage(): void
    {
        $portal = $this->createPortal('portal-id', '', 'de-id', true);
        $languageDe = $this->createLanguage('de-id', 'de');
        $languageFr = $this->createLanguage('fr-id', 'fr');
        $domainDe = $this->createDomain('de-id', '', 'www.example.com', 'www.example.com');
        $domainFr = $this->createDomain('fr-id', 'fr', 'www.example.com', 'www.example.com');

        $portalDomainService = $this->prophesize(PortalDomainServiceInterface::class);
        $portalDomainService->getPrimaryDomain('portal-id', 'de-id')->willReturn($domainDe);
        $portalDomainService->getPrimaryDomain('portal-id', 'fr-id')->willReturn($domainFr);

        $domainDataAccess = $this->prophesize(CmsPortalDomainsDataAccessInterface::class);
        $domainDataAccess->getDomainCandidatesByHostAndPortal('www.example.com', 'portal-id')->willReturn([
            ['id' => '1', 'url_suffix' => ''],
            ['id' => '2', 'url_suffix' => 'fr'],
        ]);

        $subject = new UrlPrefixGenerator($portalDomainService->reveal(), $domainDataAccess->reveal());

        self::assertSame('', $subject->getDomainLanguagePathSegment($portal, $languageDe));
        self::assertSame('fr', $subject->getDomainLanguagePathSegment($portal, $languageFr));
        self::assertSame('', $subject->getPathPrefix($portal, $languageDe));
        self::assertSame('/fr', $subject->getPathPrefix($portal, $languageFr));
        self::assertSame('', $subject->generatePrefix($portal, $languageDe));
        self::assertSame('/fr', $subject->generatePrefix($portal, $languageFr));
    }

    public function testSupportsExplicitDefaultSuffix(): void
    {
        $portal = $this->createPortal('portal-id', '', 'de-id', true);
        $languageDe = $this->createLanguage('de-id', 'de');
        $languageFr = $this->createLanguage('fr-id', 'fr');
        $domainDe = $this->createDomain('de-id', 'de', 'www.example.com', 'www.example.com');
        $domainFr = $this->createDomain('fr-id', 'fr', 'www.example.com', 'www.example.com');

        $portalDomainService = $this->prophesize(PortalDomainServiceInterface::class);
        $portalDomainService->getPrimaryDomain('portal-id', 'de-id')->willReturn($domainDe);
        $portalDomainService->getPrimaryDomain('portal-id', 'fr-id')->willReturn($domainFr);

        $domainDataAccess = $this->prophesize(CmsPortalDomainsDataAccessInterface::class);
        $domainDataAccess->getDomainCandidatesByHostAndPortal('www.example.com', 'portal-id')->willReturn([
            ['id' => '1', 'url_suffix' => 'de'],
            ['id' => '2', 'url_suffix' => 'fr'],
        ]);

        $subject = new UrlPrefixGenerator($portalDomainService->reveal(), $domainDataAccess->reveal());

        self::assertSame('de', $subject->getDomainLanguagePathSegment($portal, $languageDe));
        self::assertSame('fr', $subject->getDomainLanguagePathSegment($portal, $languageFr));
        self::assertSame('/de', $subject->getPathPrefix($portal, $languageDe));
        self::assertSame('/fr', $subject->getPathPrefix($portal, $languageFr));
        self::assertSame('/de', $subject->generatePrefix($portal, $languageDe));
        self::assertSame('/fr', $subject->generatePrefix($portal, $languageFr));
    }

    public function testCombinesPortalAndDomainSuffix(): void
    {
        $portal = $this->createPortal('portal-id', 'shop', 'de-id', true);
        $language = $this->createLanguage('fr-id', 'fr');
        $domain = $this->createDomain('fr-id', 'fr', 'www.example.com', 'www.example.com');

        $portalDomainService = $this->prophesize(PortalDomainServiceInterface::class);
        $portalDomainService->getPrimaryDomain('portal-id', 'fr-id')->willReturn($domain);

        $domainDataAccess = $this->prophesize(CmsPortalDomainsDataAccessInterface::class);
        $domainDataAccess->getDomainCandidatesByHostAndPortal('www.example.com', 'portal-id')->willReturn([
            ['id' => '1', 'url_suffix' => ''],
            ['id' => '2', 'url_suffix' => 'fr'],
        ]);

        $subject = new UrlPrefixGenerator($portalDomainService->reveal(), $domainDataAccess->reveal());

        self::assertSame('fr', $subject->getDomainLanguagePathSegment($portal, $language));
        self::assertSame('/shop/fr', $subject->getPathPrefix($portal, $language));
        self::assertSame('/shop/fr', $subject->generatePrefix($portal, $language));
    }

    public function testGeneratesVariantPrefixForExplicitDomainVariant(): void
    {
        $portal = $this->createPortal('portal-id', '', 'de-id', true);
        $language = $this->createLanguage('de-id', 'de');
        $primaryDomain = $this->createDomain('de-id', '', 'www.example.com', 'www.example.com');
        $variantDomain = $this->createDomain('de-id', 'fr', 'www.example.com', 'www.example.com');

        $portalDomainService = $this->prophesize(PortalDomainServiceInterface::class);
        $portalDomainService->getPrimaryDomain('portal-id', 'de-id')->willReturn($primaryDomain);

        $domainDataAccess = $this->prophesize(CmsPortalDomainsDataAccessInterface::class);
        $domainDataAccess->getDomainCandidatesByHostAndPortal('www.example.com', 'portal-id')->willReturn([
            ['id' => '1', 'url_suffix' => ''],
            ['id' => '2', 'url_suffix' => 'fr'],
        ]);

        $subject = new UrlPrefixGenerator($portalDomainService->reveal(), $domainDataAccess->reveal());

        self::assertSame('fr', $subject->getDomainLanguagePathSegment($portal, $language, $variantDomain));
        self::assertSame('/fr', $subject->getPathPrefix($portal, $language, $variantDomain));
        self::assertSame('/fr', $subject->generatePrefixForDomain($portal, $language, $variantDomain));
    }

    public function testDoesNotUseLanguageIsoCodeForExplicitDomainWithoutSuffix(): void
    {
        $portal = $this->createPortal('portal-id', '', 'de-id', true);
        $language = $this->createLanguage('it-id', 'it');
        $targetDomain = $this->createDomain('', '', 'www.tischwelt.de.example.com', 'www.tischwelt.de.example.com');

        $portalDomainService = $this->prophesize(PortalDomainServiceInterface::class);

        $domainDataAccess = $this->prophesize(CmsPortalDomainsDataAccessInterface::class);
        $domainDataAccess->getDomainCandidatesByHostAndPortal('www.tischwelt.de.example.com', 'portal-id')->willReturn([
            ['id' => '1', 'url_suffix' => ''],
        ]);

        $subject = new UrlPrefixGenerator($portalDomainService->reveal(), $domainDataAccess->reveal());

        self::assertSame('', $subject->getDomainLanguagePathSegment($portal, $language, $targetDomain));
        self::assertSame('', $subject->getPathPrefix($portal, $language, $targetDomain));
        self::assertSame('', $subject->generatePrefixForDomain($portal, $language, $targetDomain));
    }

    public function testKeepsPortalPrefixWithoutDomainSuffixWhenFamilyUsesDomainSuffixes(): void
    {
        $portal = $this->createPortal('portal-id', 'shop', 'de-id', true);
        $language = $this->createLanguage('de-id', 'de');
        $domain = $this->createDomain('de-id', '', 'www.example.com', 'www.example.com');

        $portalDomainService = $this->prophesize(PortalDomainServiceInterface::class);
        $portalDomainService->getPrimaryDomain('portal-id', 'de-id')->willReturn($domain);

        $domainDataAccess = $this->prophesize(CmsPortalDomainsDataAccessInterface::class);
        $domainDataAccess->getDomainCandidatesByHostAndPortal('www.example.com', 'portal-id')->willReturn([
            ['id' => '1', 'url_suffix' => ''],
            ['id' => '2', 'url_suffix' => 'fr'],
        ]);

        $subject = new UrlPrefixGenerator($portalDomainService->reveal(), $domainDataAccess->reveal());

        self::assertSame('', $subject->getDomainLanguagePathSegment($portal, $language));
        self::assertSame('/shop', $subject->getPathPrefix($portal, $language));
        self::assertSame('/shop', $subject->generatePrefix($portal, $language));
    }

    public function testUsesConfiguredDomainSuffixInsteadOfLanguageIsoCode(): void
    {
        $portal = $this->createPortal('portal-id', '', 'de-id', true);
        $language = $this->createLanguage('fr-id', 'fr');
        $domain = $this->createDomain('fr-id', 'francais', 'www.example.com', 'www.example.com');

        $portalDomainService = $this->prophesize(PortalDomainServiceInterface::class);
        $portalDomainService->getPrimaryDomain('portal-id', 'fr-id')->willReturn($domain);

        $domainDataAccess = $this->prophesize(CmsPortalDomainsDataAccessInterface::class);
        $domainDataAccess->getDomainCandidatesByHostAndPortal('www.example.com', 'portal-id')->willReturn([
            ['id' => '1', 'url_suffix' => ''],
            ['id' => '2', 'url_suffix' => 'francais'],
        ]);

        $subject = new UrlPrefixGenerator($portalDomainService->reveal(), $domainDataAccess->reveal());

        self::assertSame('francais', $subject->getDomainLanguagePathSegment($portal, $language));
        self::assertSame('/francais', $subject->getPathPrefix($portal, $language));
        self::assertSame('/francais', $subject->generatePrefix($portal, $language));
    }

    public function testFallsBackToLegacyPrefixWhenNoMatchingDomainWasFound(): void
    {
        $portal = $this->createPortal('portal-id', '', 'de-id', true);
        $language = $this->createLanguage('fr-id', 'fr');

        $portalDomainService = $this->prophesize(PortalDomainServiceInterface::class);
        $portalDomainService->getPrimaryDomain('portal-id', 'fr-id')->willThrow(new InvalidPortalDomainException('missing'));

        $domainDataAccess = $this->prophesize(CmsPortalDomainsDataAccessInterface::class);

        $subject = new UrlPrefixGenerator($portalDomainService->reveal(), $domainDataAccess->reveal());

        self::assertSame('fr', $subject->getDomainLanguagePathSegment($portal, $language));
        self::assertSame('/fr', $subject->getPathPrefix($portal, $language));
        self::assertSame('/fr', $subject->generatePrefix($portal, $language));
    }

    private function createPortal(string $id, string $identifier, string $defaultLanguageId, bool $useMultilanguage): \TdbCmsPortal
    {
        $portal = $this->getMockBuilder('TdbCmsPortal')->disableAutoload()->getMock();
        $portal->id = $id;
        $portal->fieldIdentifier = $identifier;
        $portal->fieldCmsLanguageId = $defaultLanguageId;
        $portal->fieldUseMultilanguage = $useMultilanguage;

        return $portal;
    }

    private function createLanguage(string $id, string $isoCode): \TdbCmsLanguage
    {
        $language = $this->getMockBuilder('TdbCmsLanguage')->disableAutoload()->getMock();
        $language->id = $id;
        $language->fieldIso6391 = $isoCode;

        return $language;
    }

    private function createDomain(string $languageId, string $urlSuffix, string $insecureDomain, string $secureDomain): \TdbCmsPortalDomains
    {
        $domain = $this->getMockBuilder('TdbCmsPortalDomains')
            ->disableAutoload()
            ->setMethods(['getUrlSuffix', 'getInsecureDomainName', 'getSecureDomainName'])
            ->getMock();
        $domain->fieldCmsLanguageId = $languageId;
        $domain->method('getUrlSuffix')->willReturn($urlSuffix);
        $domain->method('getInsecureDomainName')->willReturn($insecureDomain);
        $domain->method('getSecureDomainName')->willReturn($secureDomain);

        return $domain;
    }
}
