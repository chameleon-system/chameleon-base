<?php

namespace ChameleonSystem\CoreBundle\Tests\Util;

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Util\UrlPrefixGenerator;
use PHPUnit\Framework\TestCase;

class UrlPrefixGeneratorTest extends TestCase
{
    public function testDomainAdditionalLanguagesOnlyChangeTheAdditionalLanguageUrl(): void
    {
        $portalDomainService = $this->createMock(PortalDomainServiceInterface::class);
        $languageService = $this->createMock(LanguageServiceInterface::class);
        $generator = new UrlPrefixGenerator($portalDomainService, $languageService);

        $portal = $this->getMockBuilder('TdbCmsPortal')->disableAutoload()->getMock();
        $portal->fieldIdentifier = 'shop';
        $portal->fieldUseMultilanguage = true;

        $domain = $this->getMockBuilder('TdbCmsPortalDomains')
            ->disableAutoload()
            ->setMethods(['GetFieldCmsLanguageIdList'])
            ->getMock();
        $domain->fieldCmsLanguageId = 'de';
        $domain->method('GetFieldCmsLanguageIdList')->willReturn(['fr']);

        $german = $this->getMockBuilder('TdbCmsLanguage')->disableAutoload()->getMock();
        $german->id = 'de';
        $german->fieldIso6391 = 'de';
        $french = $this->getMockBuilder('TdbCmsLanguage')->disableAutoload()->getMock();
        $french->id = 'fr';
        $french->fieldIso6391 = 'fr';

        self::assertSame('/shop', $generator->generatePrefix($portal, $german, $domain));
        self::assertSame('/shop/fr', $generator->generatePrefix($portal, $french, $domain));

        $portal->fieldUseMultilanguage = false;
        self::assertSame('/shop', $generator->generatePrefix($portal, $french, $domain));
    }

    public function testGetLanguagePrefixCachesPrimaryDomainLookupPerLanguageAndDomainCombination(): void
    {
        $portalDomainService = $this->createMock(PortalDomainServiceInterface::class);
        $portalDomainService->expects(self::once())
            ->method('getPrimaryDomain')
            ->with('portal-1', 'fr')
            ->willReturn($this->createPrimaryDomain('de'));

        $languageService = $this->createMock(LanguageServiceInterface::class);
        $generator = new UrlPrefixGenerator($portalDomainService, $languageService);

        $portal = $this->createPortal('portal-1', true, '', 'shop');
        $language = $this->createLanguage('fr', 'fr');

        self::assertSame('fr', $generator->getLanguagePrefix($portal, $language));
        self::assertSame('fr', $generator->getLanguagePrefix($portal, $language));
    }

    public function testGetLanguagePrefixCachesDomainLanguageListLookup(): void
    {
        $portalDomainService = $this->createMock(PortalDomainServiceInterface::class);
        $portalDomainService->expects(self::never())->method('getPrimaryDomain');

        $languageService = $this->createMock(LanguageServiceInterface::class);
        $generator = new UrlPrefixGenerator($portalDomainService, $languageService);

        $portal = $this->createPortal('portal-1', true, 'de', 'shop');
        $domain = $this->getMockBuilder('TdbCmsPortalDomains')
            ->disableAutoload()
            ->onlyMethods(['GetFieldCmsLanguageIdList'])
            ->getMock();
        $domain->id = 'domain-1';
        $domain->fieldCmsLanguageId = '';
        $domain->expects(self::once())
            ->method('GetFieldCmsLanguageIdList')
            ->willReturn(['fr']);

        $language = $this->createLanguage('fr', 'fr');

        self::assertSame('fr', $generator->getLanguagePrefix($portal, $language, $domain));
        self::assertSame('fr', $generator->getLanguagePrefix($portal, $language, $domain));
    }

    private function createPortal(
        string $id,
        bool $useMultilanguage,
        string $defaultLanguageId,
        string $identifier
    ): \TdbCmsPortal {
        $portal = $this->getMockBuilder('TdbCmsPortal')->disableAutoload()->getMock();
        $portal->id = $id;
        $portal->fieldUseMultilanguage = $useMultilanguage;
        $portal->fieldCmsLanguageId = $defaultLanguageId;
        $portal->fieldIdentifier = $identifier;

        return $portal;
    }

    private function createLanguage(string $id, string $isoCode, string $urlPrefix = ''): \TdbCmsLanguage
    {
        $language = $this->getMockBuilder('TdbCmsLanguage')->disableAutoload()->getMock();
        $language->id = $id;
        $language->fieldIso6391 = $isoCode;
        $language->fieldUrlPrefix = $urlPrefix;

        return $language;
    }

    private function createPrimaryDomain(string $languageId): \TdbCmsPortalDomains
    {
        $domain = $this->getMockBuilder('TdbCmsPortalDomains')->disableAutoload()->getMock();
        $domain->fieldCmsLanguageId = $languageId;

        return $domain;
    }
}
