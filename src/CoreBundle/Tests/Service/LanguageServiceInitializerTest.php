<?php

declare(strict_types=1);

namespace ChameleonSystem\CoreBundle\Tests\Service;

use ChameleonSystem\CoreBundle\DataAccess\DataAccessCmsLanguageInterface;
use ChameleonSystem\CoreBundle\RequestType\RequestTypeInterface;
use ChameleonSystem\CoreBundle\Service\DomainPathMatch;
use ChameleonSystem\CoreBundle\Service\ActivePageServiceInterface;
use ChameleonSystem\CoreBundle\Service\Initializer\LanguageServiceInitializer;
use ChameleonSystem\CoreBundle\Service\LanguageService;
use ChameleonSystem\CoreBundle\Service\PageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class LanguageServiceInitializerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var InputFilterUtilInterface|ObjectProphecy
     */
    private $inputFilterUtil;

    /**
     * @var PortalDomainServiceInterface|ObjectProphecy
     */
    private $portalDomainService;

    /**
     * @var RequestInfoServiceInterface|ObjectProphecy
     */
    private $requestInfoService;

    /**
     * @var ActivePageServiceInterface|ObjectProphecy
     */
    private $activePageService;

    /**
     * @var PageServiceInterface|ObjectProphecy
     */
    private $pageService;

    /**
     * @var DataAccessCmsLanguageInterface|ObjectProphecy
     */
    private $languageDataAccess;

    /**
     * @var EventDispatcherInterface|ObjectProphecy
     */
    private $eventDispatcher;

    private RequestStack $requestStack;

    private Container $container;

    private TestLanguageServiceInitializer $initializer;

    protected function setUp(): void
    {
        parent::setUp();

        $this->inputFilterUtil = $this->prophesize(InputFilterUtilInterface::class);
        $this->portalDomainService = $this->prophesize(PortalDomainServiceInterface::class);
        $this->requestInfoService = $this->prophesize(RequestInfoServiceInterface::class);
        $this->activePageService = $this->prophesize(ActivePageServiceInterface::class);
        $this->pageService = $this->prophesize(PageServiceInterface::class);
        $this->languageDataAccess = $this->prophesize(DataAccessCmsLanguageInterface::class);
        $this->eventDispatcher = $this->prophesize(EventDispatcherInterface::class);

        $this->requestStack = new RequestStack();
        $this->container = $this->createMock(Container::class);

        $this->container->method('get')->willReturnCallback(function (string $id) {
            return match ($id) {
                'chameleon_system_core.portal_domain_service' => $this->portalDomainService->reveal(),
                'chameleon_system_core.request_info_service' => $this->requestInfoService->reveal(),
                'chameleon_system_core.active_page_service' => $this->activePageService->reveal(),
                'chameleon_system_core.page_service' => $this->pageService->reveal(),
                default => throw new \InvalidArgumentException(sprintf('Unexpected service id "%s".', $id)),
            };
        });

        $this->inputFilterUtil->getFilteredInput('previewLanguageId')->willReturn(null);

        $this->requestInfoService->isChameleonRequestType(RequestTypeInterface::REQUEST_TYPE_FRONTEND)->willReturn(true);
        $this->requestInfoService->isCmsTemplateEngineEditMode()->willReturn(false);
        $this->requestInfoService->isChameleonRequestType(RequestTypeInterface::REQUEST_TYPE_BACKEND)->willReturn(false);
        $this->requestInfoService->isPreviewMode()->willReturn(false);

        $this->activePageService->getActivePage()->willReturn(null);
        $this->eventDispatcher->dispatch(\Prophecy\Argument::cetera())->willReturnArgument(0);

        $this->initializer = new TestLanguageServiceInitializer(
            $this->inputFilterUtil->reveal(),
            $this->requestStack,
            $this->container,
            $this->createMock(Connection::class)
        );
    }

    public function testUsesLanguageFromSuffixlessDomain(): void
    {
        $request = Request::create('https://www.tischwelt.ch/');
        $this->requestStack->push($request);

        $this->portalDomainService->getActiveDomain()->willReturn($this->createDomain('de-id'));
        $this->portalDomainService->getActivePortal()->willReturn($this->createPortal('de-id', true));

        $languageService = $this->createLanguageService([
            'de-id' => 'de',
        ]);

        self::assertSame('de-id', $languageService->getActiveLanguageId());
        self::assertSame('de', $request->attributes->get('_locale'));
    }

    public function testUsesLanguageFromDomainSuffixFr(): void
    {
        $request = Request::create('https://www.tischwelt.ch/fr/');
        $this->requestStack->push($request);

        $this->portalDomainService->getActiveDomain()->willReturn($this->createDomain('fr-id'));
        $this->portalDomainService->getActivePortal()->willReturn($this->createPortal('de-id', true));

        $languageService = $this->createLanguageService([
            'fr-id' => 'fr',
        ]);

        self::assertSame('fr-id', $languageService->getActiveLanguageId());
        self::assertSame('fr', $request->attributes->get('_locale'));
    }

    public function testUnknownSegmentDoesNotChangeLanguageWhenDomainAlreadyResolved(): void
    {
        $request = Request::create('https://www.tischwelt.ch/frankfurt/');
        $this->requestStack->push($request);

        $this->portalDomainService->getActiveDomain()->willReturn($this->createDomain('de-id'));
        $this->portalDomainService->getActivePortal()->willReturn($this->createPortal('de-id', true));

        $languageService = $this->createLanguageService([
            'de-id' => 'de',
        ]);

        self::assertSame('de-id', $languageService->getActiveLanguageId());
        self::assertSame('de', $request->attributes->get('_locale'));
    }

    public function testExplicitDefaultSuffixUsesConfiguredDomainLanguage(): void
    {
        $request = Request::create('https://www.tischwelt.ch/de/');
        $this->requestStack->push($request);

        $this->portalDomainService->getActiveDomain()->willReturn($this->createDomain('de-id'));
        $this->portalDomainService->getActivePortal()->willReturn($this->createPortal('de-id', true));

        $languageService = $this->createLanguageService([
            'de-id' => 'de',
        ]);

        self::assertSame('de-id', $languageService->getActiveLanguageId());
        self::assertSame('de', $request->attributes->get('_locale'));
    }

    public function testExistingInstallationWithoutUrlSuffixKeepsDomainLanguageBehaviour(): void
    {
        $request = Request::create('https://www.example.com/');
        $this->requestStack->push($request);

        $this->portalDomainService->getActiveDomain()->willReturn($this->createDomain('de-id'));
        $this->portalDomainService->getActivePortal()->willReturn($this->createPortal('de-id', false));

        $languageService = $this->createLanguageService([
            'de-id' => 'de',
        ]);

        self::assertSame('de-id', $languageService->getActiveLanguageId());
        self::assertSame('de', $request->attributes->get('_locale'));
    }

    public function testLegacyPortalLanguagePrefixLogicRemainsAsFallback(): void
    {
        $request = Request::create('https://www.example.com/fr/');
        $this->requestStack->push($request);

        $this->portalDomainService->getActiveDomain()->willReturn($this->createDomain(''));
        $this->portalDomainService->getActivePortal()->willReturn($this->createPortal('de-id', true));
        $this->initializer->setLanguageFromIsoCode('fr', 'fr-id');

        $languageService = $this->createLanguageService([
            'fr-id' => 'fr',
        ]);

        self::assertSame('fr-id', $languageService->getActiveLanguageId());
        self::assertSame('fr', $request->attributes->get('_locale'));
    }

    public function testUsesMatchedLanguageIdFromDomainPathVariantWhileActiveDomainIsStillNull(): void
    {
        $request = Request::create('https://www.tischwelt.ch/fr/');
        $this->requestStack->push($request);

        $this->portalDomainService->getActiveDomain()->willReturn(null);
        $this->portalDomainService->getActiveDomainPathMatch()->willReturn(
            new DomainPathMatch(
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
            )
        );
        $this->portalDomainService->getActivePortal()->willReturn($this->createPortal('de-id', true));

        $languageService = $this->createLanguageService([
            'fr-id' => 'fr',
        ]);

        self::assertSame('fr-id', $languageService->getActiveLanguageId());
        self::assertSame('fr', $request->attributes->get('_locale'));
    }

    public function testUsesRequestDomainPathMatchBeforePortalDomainServiceState(): void
    {
        $request = Request::create('https://www.tischwelt.ch/fr/');
        $request->attributes->set(
            DomainPathMatch::REQUEST_ATTRIBUTE_NAME,
            new DomainPathMatch(
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
            )
        );
        $this->requestStack->push($request);

        $this->portalDomainService->getActiveDomain()->shouldNotBeCalled();
        $this->portalDomainService->getActiveDomainPathMatch()->shouldNotBeCalled();
        $this->portalDomainService->getActivePortal()->shouldNotBeCalled();

        $languageService = $this->createLanguageService([
            'fr-id' => 'fr',
        ]);

        self::assertSame('fr-id', $languageService->getActiveLanguageId());
        self::assertSame('fr', $request->attributes->get('_locale'));
    }

    /**
     * @param array<string, string> $languageMap
     */
    private function createLanguageService(array $languageMap): LanguageService
    {
        foreach ($languageMap as $languageId => $isoCode) {
            $language = $this->createLanguage($languageId, $isoCode);
            $this->languageDataAccess->getLanguage($languageId, $languageId)->willReturn($language);
        }

        return new LanguageService(
            $this->requestStack,
            $this->eventDispatcher->reveal(),
            $this->initializer,
            $this->languageDataAccess->reveal()
        );
    }

    private function createPortal(string $defaultLanguageId, bool $useMultilanguage): \TdbCmsPortal
    {
        $portal = $this->createMock(\TdbCmsPortal::class);
        $portal->id = 'portal-1';
        $portal->fieldCmsLanguageId = $defaultLanguageId;
        $portal->fieldUseMultilanguage = $useMultilanguage;
        $portal->fieldIdentifier = '';

        return $portal;
    }

    private function createDomain(string $languageId): \TCMSPortalDomain
    {
        $domain = $this->createMock(\TCMSPortalDomain::class);
        $domain->fieldCmsLanguageId = $languageId;

        return $domain;
    }

    private function createLanguage(string $languageId, string $isoCode): \TdbCmsLanguage
    {
        $language = $this->createMock(\TdbCmsLanguage::class);
        $language->id = $languageId;
        $language->fieldIso6391 = $isoCode;
        $language->sqlData = [];
        $language->expects(self::once())->method('SetLanguage')->with($languageId);
        $language->expects(self::once())->method('LoadFromRow')->with([]);

        return $language;
    }
}

class TestLanguageServiceInitializer extends LanguageServiceInitializer
{
    /**
     * @var array<string, string|null>
     */
    private array $languageByIsoCode = [];

    public function setLanguageFromIsoCode(string $isoCode, ?string $languageId): void
    {
        $this->languageByIsoCode[$isoCode] = $languageId;
    }

    public function getLanguageFromPersistence(\TdbCmsPortal $activePortal, $languageCode)
    {
        return $this->languageByIsoCode[$languageCode] ?? null;
    }
}
