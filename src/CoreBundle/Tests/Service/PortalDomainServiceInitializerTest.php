<?php

declare(strict_types=1);

namespace ChameleonSystem\CoreBundle\Tests\Service;

use ChameleonSystem\CoreBundle\DataAccess\CmsPortalDomainsDataAccessInterface;
use ChameleonSystem\CoreBundle\RequestType\RequestTypeInterface;
use ChameleonSystem\CoreBundle\Service\DomainPathVariantResolutionResult;
use ChameleonSystem\CoreBundle\Service\DomainPathVariantResolver;
use ChameleonSystem\CoreBundle\Service\Initializer\PortalDomainServiceInitializer;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use esono\pkgCmsCache\CacheInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class PortalDomainServiceInitializerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var InputFilterUtilInterface|ObjectProphecy
     */
    private $inputFilterUtil;

    /**
     * @var ContainerInterface|ObjectProphecy
     */
    private $container;

    /**
     * @var CmsPortalDomainsDataAccessInterface|ObjectProphecy
     */
    private $domainDataAccess;

    /**
     * @var RequestInfoServiceInterface|ObjectProphecy
     */
    private $requestInfoService;

    /**
     * @var CacheInterface|ObjectProphecy
     */
    private $cache;

    /**
     * @var PortalDomainServiceInterface|ObjectProphecy
     */
    private $portalDomainService;

    private RequestStack $requestStack;

    private TestPortalDomainServiceInitializer $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->inputFilterUtil = $this->prophesize(InputFilterUtilInterface::class);
        $this->container = $this->prophesize(ContainerInterface::class);
        $this->domainDataAccess = $this->prophesize(CmsPortalDomainsDataAccessInterface::class);
        $this->requestInfoService = $this->prophesize(RequestInfoServiceInterface::class);
        $this->cache = $this->prophesize(CacheInterface::class);
        $this->portalDomainService = $this->prophesize(PortalDomainServiceInterface::class);

        $this->requestStack = new RequestStack();

        $this->container->get('chameleon_system_core.request_info_service')->willReturn($this->requestInfoService->reveal());
        $this->container->get('chameleon_system_cms_cache.cache')->willReturn($this->cache->reveal());

        $this->requestInfoService->isChameleonRequestType(RequestTypeInterface::REQUEST_TYPE_FRONTEND)->willReturn(true);
        $this->requestInfoService->isCmsTemplateEngineEditMode()->willReturn(false);
        $this->requestInfoService->isChameleonRequestType(RequestTypeInterface::REQUEST_TYPE_BACKEND)->willReturn(false);

        $this->cache->getKey(Argument::type('array'), false)->willReturn('portal-domain-cache-key');
        $this->cache->get('portal-domain-cache-key')->willReturn(null);
        $this->cache->set('portal-domain-cache-key', Argument::type('array'), Argument::type('array'))->willReturn(null);

        $this->subject = new TestPortalDomainServiceInitializer(
            $this->inputFilterUtil->reveal(),
            $this->container->reveal(),
            $this->requestStack,
            $this->domainDataAccess->reveal(),
            new DomainPathVariantResolver()
        );
    }

    public function testKeepsLegacyBehaviourForSingleSuffixlessDomain(): void
    {
        $host = 'www.example.com';
        $portal = $this->mockPortal('portal-1');
        $domain = $this->mockDomain('domain-1');
        $this->subject->setPortal('portal-1', $portal);
        $this->subject->setDomain('domain-1', $domain);

        $request = Request::create('https://'.$host.'/');
        $this->requestStack->push($request);

        $domainCandidates = [
            $this->createDomainCandidate('domain-1', 'portal-1', $host, ''),
        ];
        $this->domainDataAccess->getDomainCandidatesByHost($host)->willReturn($domainCandidates);
        $this->domainDataAccess->getPortalPrefixListForDomain($host)->willReturn([]);
        $this->domainDataAccess->getDomainDataByName(Argument::cetera())->shouldNotBeCalled();

        $this->portalDomainService->setActivePortal($portal)->shouldBeCalled();
        $this->portalDomainService->setActiveDomain($domain)->shouldBeCalled();
        $this->portalDomainService->setActiveDomainPathVariantResolutionResult(Argument::type(DomainPathVariantResolutionResult::class))->shouldBeCalled();

        $this->subject->initialize($this->portalDomainService->reveal());

        self::assertInstanceOf(
            DomainPathVariantResolutionResult::class,
            $request->attributes->get(DomainPathVariantResolutionResult::REQUEST_ATTRIBUTE_NAME)
        );
    }

    public function testRootPathUsesSuffixlessVariantWhenHostHasMultipleLanguageDomains(): void
    {
        $host = 'www.tischwelt.ch';
        $portal = $this->mockPortal('portal-1');
        $domain = $this->mockDomain('domain-de');
        $this->subject->setPortal('portal-1', $portal);
        $this->subject->setDomain('domain-de', $domain);

        $request = Request::create('https://'.$host.'/');
        $this->requestStack->push($request);

        $this->domainDataAccess->getDomainCandidatesByHost($host)->willReturn([
            $this->createDomainCandidate('domain-de', 'portal-1', $host, ''),
            $this->createDomainCandidate('domain-fr', 'portal-1', $host, 'fr'),
            $this->createDomainCandidate('domain-it', 'portal-1', $host, 'it'),
        ]);
        $this->domainDataAccess->getPortalPrefixListForDomain($host)->willReturn([]);
        $this->domainDataAccess->getDomainDataByName(Argument::cetera())->shouldNotBeCalled();

        $this->portalDomainService->setActivePortal($portal)->shouldBeCalled();
        $this->portalDomainService->setActiveDomain($domain)->shouldBeCalled();

        $this->subject->initialize($this->portalDomainService->reveal());
    }

    public function testConfiguredLanguageSuffixSelectsMatchingDomainVariant(): void
    {
        $host = 'www.tischwelt.ch';
        $portal = $this->mockPortal('portal-1');
        $domain = $this->mockDomain('domain-fr');
        $this->subject->setPortal('portal-1', $portal);
        $this->subject->setDomain('domain-fr', $domain);

        $request = Request::create('https://'.$host.'/fr/');
        $this->requestStack->push($request);

        $this->domainDataAccess->getDomainCandidatesByHost($host)->willReturn([
            $this->createDomainCandidate('domain-de', 'portal-1', $host, ''),
            $this->createDomainCandidate('domain-fr', 'portal-1', $host, 'fr'),
            $this->createDomainCandidate('domain-it', 'portal-1', $host, 'it'),
        ]);
        $this->domainDataAccess->getPortalPrefixListForDomain($host)->willReturn([]);

        $this->portalDomainService->setActivePortal($portal)->shouldBeCalled();
        $this->portalDomainService->setActiveDomain($domain)->shouldBeCalled();

        $this->subject->initialize($this->portalDomainService->reveal());
    }

    public function testUnknownSegmentRemainsRegularPathAndUsesSuffixlessDomain(): void
    {
        $host = 'www.tischwelt.ch';
        $portal = $this->mockPortal('portal-1');
        $domain = $this->mockDomain('domain-de');
        $this->subject->setPortal('portal-1', $portal);
        $this->subject->setDomain('domain-de', $domain);

        $request = Request::create('https://'.$host.'/frankfurt/');
        $this->requestStack->push($request);

        $this->domainDataAccess->getDomainCandidatesByHost($host)->willReturn([
            $this->createDomainCandidate('domain-de', 'portal-1', $host, ''),
            $this->createDomainCandidate('domain-fr', 'portal-1', $host, 'fr'),
        ]);
        $this->domainDataAccess->getPortalPrefixListForDomain($host)->willReturn([]);

        $this->portalDomainService->setActivePortal($portal)->shouldBeCalled();
        $this->portalDomainService->setActiveDomain($domain)->shouldBeCalled();

        $this->subject->initialize($this->portalDomainService->reveal());
    }

    public function testExplicitDefaultSuffixCanBeResolvedWithoutSuffixlessDomain(): void
    {
        $host = 'www.tischwelt.ch';
        $portal = $this->mockPortal('portal-1');
        $domain = $this->mockDomain('domain-de');
        $this->subject->setPortal('portal-1', $portal);
        $this->subject->setDomain('domain-de', $domain);

        $request = Request::create('https://'.$host.'/de/');
        $this->requestStack->push($request);

        $this->domainDataAccess->getDomainCandidatesByHost($host)->willReturn([
            $this->createDomainCandidate('domain-de', 'portal-1', $host, 'de'),
            $this->createDomainCandidate('domain-fr', 'portal-1', $host, 'fr'),
        ]);
        $this->domainDataAccess->getPortalPrefixListForDomain($host)->willReturn([]);

        $this->portalDomainService->setActivePortal($portal)->shouldBeCalled();
        $this->portalDomainService->setActiveDomain($domain)->shouldBeCalled();

        $this->subject->initialize($this->portalDomainService->reveal());
    }

    public function testNoSuffixlessRootFallbackDoesNotSelectArbitraryDomain(): void
    {
        $host = 'www.tischwelt.ch';
        $portal = $this->mockPortal('portal-1');
        $this->subject->setPortal('portal-1', $portal);

        $request = Request::create('https://'.$host.'/');
        $this->requestStack->push($request);

        $this->domainDataAccess->getDomainCandidatesByHost($host)->willReturn([
            $this->createDomainCandidate('domain-de', 'portal-1', $host, 'de'),
            $this->createDomainCandidate('domain-fr', 'portal-1', $host, 'fr'),
        ]);
        $this->domainDataAccess->getPortalPrefixListForDomain($host)->willReturn([]);

        $this->portalDomainService->setActivePortal($portal)->shouldBeCalled();
        $this->portalDomainService->setActiveDomain(null)->shouldBeCalled();

        $this->subject->initialize($this->portalDomainService->reveal());
    }

    public function testPortalIdentifierAndDomainSuffixResolveWithinPortal(): void
    {
        $host = 'www.example.com';
        $portal = $this->mockPortal('portal-1');
        $domain = $this->mockDomain('domain-fr');
        $this->subject->setPortal('portal-1', $portal);
        $this->subject->setDomain('domain-fr', $domain);

        $request = Request::create('https://'.$host.'/shop/fr/');
        $this->requestStack->push($request);

        $domainCandidates = [
            $this->createDomainCandidate('domain-de', 'portal-1', $host, ''),
            $this->createDomainCandidate('domain-fr', 'portal-1', $host, 'fr'),
        ];
        $this->domainDataAccess->getDomainCandidatesByHost($host)->willReturn($domainCandidates);
        $this->domainDataAccess->getPortalPrefixListForDomain($host)->willReturn(['shop']);
        $this->domainDataAccess->getActivePortalCandidate(['portal-1'], 'shop', false)->willReturn([
            'id' => 'portal-1',
            'identifier' => 'shop',
        ]);

        $this->portalDomainService->setActivePortal($portal)->shouldBeCalled();
        $this->portalDomainService->setActiveDomain($domain)->shouldBeCalled();

        $this->subject->initialize($this->portalDomainService->reveal());
    }

    /**
     * @return array<string, string>
     */
    private function createDomainCandidate(string $id, string $portalId, string $host, string $urlSuffix): array
    {
        return [
            'id' => $id,
            'cms_portal_id' => $portalId,
            'cms_language_id' => 'lang-'.$id,
            'name' => $host,
            'sslname' => $host,
            'url_suffix' => $urlSuffix,
            'is_master_domain' => '' === $urlSuffix ? '1' : '0',
        ];
    }

    private function mockPortal(string $portalId): \TCMSPortal
    {
        $portal = $this->createMock(\TCMSPortal::class);
        $portal->id = $portalId;
        $portal->fieldDeactivePortal = false;

        return $portal;
    }

    private function mockDomain(string $domainId): \TCMSPortalDomain
    {
        $domain = $this->createMock(\TCMSPortalDomain::class);
        $domain->id = $domainId;

        return $domain;
    }
}

class TestPortalDomainServiceInitializer extends PortalDomainServiceInitializer
{
    /**
     * @var array<string, \TCMSPortal>
     */
    private array $portals = [];

    /**
     * @var array<string, \TCMSPortalDomain>
     */
    private array $domains = [];

    public function setPortal(string $portalId, \TCMSPortal $portal): void
    {
        $this->portals[$portalId] = $portal;
    }

    public function setDomain(string $domainId, \TCMSPortalDomain $domain): void
    {
        $this->domains[$domainId] = $domain;
    }

    protected function isUserSignedInToBackend(Request $request): bool
    {
        return false;
    }

    protected function createPortalFromData(array $portalData): \TCMSPortal
    {
        return $this->portals[(string) $portalData['id']];
    }

    protected function loadPortalById(?string $portalId, bool $allowInactivePortals): ?\TCMSPortal
    {
        if (null === $portalId) {
            return null;
        }

        return $this->portals[$portalId] ?? null;
    }

    protected function createDomainFromData(array $domainData): \TCMSPortalDomain
    {
        return $this->domains[(string) $domainData['id']];
    }
}
