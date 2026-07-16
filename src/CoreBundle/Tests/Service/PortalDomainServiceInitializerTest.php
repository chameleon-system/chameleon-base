<?php

declare(strict_types=1);

namespace ChameleonSystem\CoreBundle\Tests\Service;

use ChameleonSystem\CoreBundle\DataAccess\CmsPortalDomainsDataAccessInterface;
use ChameleonSystem\CoreBundle\RequestType\RequestTypeInterface;
use ChameleonSystem\CoreBundle\Service\DomainPathMatch;
use ChameleonSystem\CoreBundle\Service\DomainPathVariantResolver;
use ChameleonSystem\CoreBundle\Service\Initializer\PortalDomainServiceInitializer;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use esono\pkgCmsCache\CacheInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class PortalDomainServiceInitializerTest extends TestCase
{
    public static function setUpBeforeClass(): void
    {
        if (false === \defined('TCMSUSERINPUT_DEFAULTFILTER')) {
            \define('TCMSUSERINPUT_DEFAULTFILTER', 'TCMSUserInput_BaseText');
        }
        if (false === \defined('PATH_CUSTOMER_FRAMEWORK_CONTROLLER')) {
            \define('PATH_CUSTOMER_FRAMEWORK_CONTROLLER', '/index.php');
        }
    }

    public function testSoleSuffixedDomainCanBeUsedForCanonicalization(): void
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

    public function testDomainPathMatchIsStoredInCachePayload(): void
    {
        $requestStack = new RequestStack();
        $requestStack->push(Request::create('https://www.example.com/FR/page'));

        $domainPathMatch = new DomainPathMatch(
            ['id' => 'domain-fr'],
            'domain-fr',
            'portal-1',
            'language-fr',
            '',
            'fr',
            '/page',
            '/fr',
            false,
            true,
            true,
            DomainPathMatch::MATCH_TYPE_HOST_MATCH_WITH_DOMAIN_SUFFIX,
            false
        );

        $requestInfoService = $this->createMock(RequestInfoServiceInterface::class);
        $requestInfoService->method('isChameleonRequestType')->willReturnCallback(
            static fn (int $requestType): bool => RequestTypeInterface::REQUEST_TYPE_FRONTEND === $requestType
        );
        $requestInfoService->method('isCmsTemplateEngineEditMode')->willReturn(false);

        $cache = $this->createMock(CacheInterface::class);
        $cache->method('getKey')->willReturn('portal-domain-cache-key');
        $cache->method('get')->willReturn(null);
        $cache->expects(self::once())
            ->method('set')
            ->with(
                'portal-domain-cache-key',
                self::callback(static function (array $cacheData) use ($domainPathMatch): bool {
                    self::assertSame($domainPathMatch->toArray(), $cacheData['domainPathMatch'] ?? null);

                    return true;
                }),
                self::isType('array')
            );

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')->willReturnCallback(
            static function (string $serviceId) use ($requestInfoService, $cache) {
                return match ($serviceId) {
                    'chameleon_system_core.request_info_service' => $requestInfoService,
                    'chameleon_system_cms_cache.cache' => $cache,
                };
            }
        );

        $domainDataAccess = $this->createMock(CmsPortalDomainsDataAccessInterface::class);
        $domainDataAccess->method('getDomainCandidatesByHost')->willReturn([
            ['name' => 'www.example.com'],
        ]);
        $domainDataAccess->method('getPortalPrefixListForDomain')->willReturn([]);

        $domainPathVariantResolver = $this->createMock(DomainPathVariantResolver::class);
        $domainPathVariantResolver->method('resolve')->willReturn($domainPathMatch);

        $subject = new TestPortalDomainServiceInitializer(
            $this->createMock(InputFilterUtilInterface::class),
            $container,
            $requestStack,
            $domainDataAccess,
            $domainPathVariantResolver
        );

        $subject->initialize($this->createMock(PortalDomainServiceInterface::class));
    }
}

class TestPortalDomainServiceInitializer extends PortalDomainServiceInitializer
{
    protected function loadPortalAndDomainFromMatch(
        ?DomainPathMatch $domainPathMatch,
        bool $allowInactivePortals
    ): ?array {
        return null;
    }

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
