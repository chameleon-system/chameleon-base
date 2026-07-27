<?php

namespace ChameleonSystem\CoreBundle\Tests\Util;

use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Util\UrlPrefixGenerator;
use PHPUnit\Framework\TestCase;

class UrlPrefixGeneratorTest extends TestCase
{
    public function testDomainAdditionalLanguagesOnlyChangeTheAdditionalLanguageUrl(): void
    {
        $portalDomainService = $this->createMock(PortalDomainServiceInterface::class);
        $generator = new UrlPrefixGenerator($portalDomainService);

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
}
