<?php

namespace ChameleonSystem\CoreBundle\Tests\Util;

use ChameleonSystem\CoreBundle\Util\DomainSupportsLanguageTrait;
use PHPUnit\Framework\TestCase;

class DomainSupportsLanguageTraitTest extends TestCase
{
    public function testDomainLanguageMatrix(): void
    {
        $subject = new class() {
            use DomainSupportsLanguageTrait;

            public function supports(\TdbCmsPortalDomains $domain, \TdbCmsPortal $portal, \TdbCmsLanguage $language): bool
            {
                return $this->domainSupportsLanguage($domain, $portal, $language, 'de');
            }
        };

        $portal = $this->getMockBuilder('TdbCmsPortal')
            ->disableAutoload()
            ->setMethods(['GetFieldCmsLanguageIdList'])
            ->getMock();
        $portal->fieldCmsLanguageId = 'de';
        $portal->fieldUseMultilanguage = true;
        $portal->method('GetFieldCmsLanguageIdList')->willReturn(['de', 'en', 'fr']);

        $multilanguageDomain = $this->getMockBuilder('TdbCmsPortalDomains')
            ->disableAutoload()
            ->setMethods(['GetFieldCmsLanguageIdList'])
            ->getMock();
        $multilanguageDomain->fieldCmsLanguageId = '';
        $multilanguageDomain->method('GetFieldCmsLanguageIdList')->willReturn(['fr']);

        self::assertTrue($subject->supports($multilanguageDomain, $portal, $this->language('de')));
        self::assertTrue($subject->supports($multilanguageDomain, $portal, $this->language('fr')));
        self::assertFalse($subject->supports($multilanguageDomain, $portal, $this->language('en')));

        $legacyDomain = $this->getMockBuilder('TdbCmsPortalDomains')
            ->disableAutoload()
            ->setMethods(['GetFieldCmsLanguageIdList'])
            ->getMock();
        $legacyDomain->fieldCmsLanguageId = '';
        $legacyDomain->method('GetFieldCmsLanguageIdList')->willReturn([]);

        self::assertTrue($subject->supports($legacyDomain, $portal, $this->language('en')));
        self::assertFalse($subject->supports($legacyDomain, $portal, $this->language('it')));

        $portal->fieldUseMultilanguage = false;
        $multilanguageDomain->fieldCmsLanguageId = 'de';
        self::assertFalse($subject->supports($multilanguageDomain, $portal, $this->language('fr')));
    }

    private function language(string $id): \TdbCmsLanguage
    {
        $language = $this->getMockBuilder('TdbCmsLanguage')->disableAutoload()->getMock();
        $language->id = $id;

        return $language;
    }
}
