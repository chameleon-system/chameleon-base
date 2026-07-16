<?php

declare(strict_types=1);

namespace ChameleonSystem\CoreBundle\Tests\Service;

use ChameleonSystem\CoreBundle\DataAccess\CmsPortalDomainsDataAccessInterface;
use ChameleonSystem\CoreBundle\Service\DomainPathMatch;
use ChameleonSystem\CoreBundle\Service\Initializer\PortalDomainServiceInitializerInterface;
use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainService;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class PortalDomainServiceTest extends TestCase
{
    use ProphecyTrait;

    public function testReturnsKnownDomainPathMatchWithoutReinitializingWhileServiceIsInitializing(): void
    {
        $eventDispatcher = $this->prophesize(EventDispatcherInterface::class);
        $initializer = $this->prophesize(PortalDomainServiceInitializerInterface::class);
        $domainDataAccess = $this->prophesize(CmsPortalDomainsDataAccessInterface::class);
        $languageService = $this->prophesize(LanguageServiceInterface::class);

        $initializer->initialize(\Prophecy\Argument::cetera())->shouldNotBeCalled();

        $subject = new PortalDomainService(
            $eventDispatcher->reveal(),
            $initializer->reveal(),
            $domainDataAccess->reveal(),
            $languageService->reveal()
        );

        $domainPathMatch = new DomainPathMatch(
            [
                'id' => 'domain-fr',
                'cms_portal_id' => 'portal-1',
                'cms_language_id' => 'fr-id',
            ],
            'domain-fr',
            'portal-1',
            'fr-id',
            '',
            'fr',
            '/',
            '/fr',
            false,
            true,
            true,
            DomainPathMatch::MATCH_TYPE_HOST_MATCH_WITH_DOMAIN_SUFFIX,
            false
        );
        $subject->setActiveDomainPathMatch($domainPathMatch);

        $reflection = new \ReflectionObject($subject);
        $property = $reflection->getProperty('isInitializing');
        $property->setAccessible(true);
        $property->setValue($subject, true);

        self::assertSame($domainPathMatch, $subject->getActiveDomainPathMatch());
    }
}
