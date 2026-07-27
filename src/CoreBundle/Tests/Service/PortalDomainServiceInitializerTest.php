<?php

declare(strict_types=1);

namespace ChameleonSystem\CoreBundle\Tests\Service;

use ChameleonSystem\CoreBundle\DataAccess\CmsPortalDomainsDataAccessInterface;
use ChameleonSystem\CoreBundle\Service\DomainPathVariantResolver;
use ChameleonSystem\CoreBundle\Service\Initializer\PortalDomainServiceInitializer;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\RequestStack;

class PortalDomainServiceInitializerTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        if (false === \defined('TCMSUSERINPUT_DEFAULTFILTER')) {
            \define('TCMSUSERINPUT_DEFAULTFILTER', 'TCMSUserInput_BaseText');
        }
    }

    public function testSoleSuffixedDomainCanBeUsedAsCanonicalRedirectTarget(): void
    {
        $domain = [
            'id' => 'domain-fr',
            'cms_portal_id' => 'portal-1',
            'url_suffix' => 'fr',
        ];

        $subject = new TestPortalDomainServiceInitializer(
            $this->createMock(InputFilterUtilInterface::class),
            $this->createMock(ContainerInterface::class),
            new RequestStack(),
            $this->createMock(CmsPortalDomainsDataAccessInterface::class),
            new DomainPathVariantResolver()
        );

        self::assertSame($domain, $subject->getFallbackDomainForPortalForTest([$domain], 'portal-1'));
    }
}

class TestPortalDomainServiceInitializer extends PortalDomainServiceInitializer
{
    /**
     * @param array<int, array<string, mixed>> $domainCandidates
     *
     * @return array<string, mixed>|null
     */
    public function getFallbackDomainForPortalForTest(array $domainCandidates, ?string $portalId): ?array
    {
        return $this->getFallbackDomainForPortal($domainCandidates, $portalId);
    }
}
