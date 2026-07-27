<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Tests\Service;

use ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\DomainPathVariantResolutionResult;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Service\PreviewModeServiceInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoService;
use ChameleonSystem\CoreBundle\Util\UrlPrefixGeneratorInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestInfoServiceTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var RequestStack|ObjectProphecy
     */
    private $mockRequestStack;

    /**
     * @var PortalDomainServiceInterface|ObjectProphecy
     */
    private $mockPortalDomainService;

    /**
     * @var LanguageServiceInterface|ObjectProphecy
     */
    private $mockLanguageService;

    /**
     * @var UrlPrefixGeneratorInterface|ObjectProphecy
     */
    private $mockUrlPrefixGenerator;

    /**
     * @var PreviewModeServiceInterface|ObjectProphecy
     */
    private $mockPreviewModeService;

    /**
     * @var RequestInfoService
     */
    private $subject;

    /**
     * @var RequestInfoService
     */
    private $subject2;

    /**
     * @var string
     */
    private $returnedValue;

    /**
     * @var string
     */
    private $returnedValue2;

    protected function setUp(): void
    {
        parent::setUp();

        $this->mockRequestStack = $this->prophesize(RequestStack::class);
        $this->mockPortalDomainService = $this->prophesize(PortalDomainServiceInterface::class);
        $this->mockLanguageService = $this->prophesize(LanguageServiceInterface::class);
        $this->mockUrlPrefixGenerator = $this->prophesize(UrlPrefixGeneratorInterface::class);
        $this->mockPreviewModeService = $this->prophesize(PreviewModeServiceInterface::class);
        $this->mockPreviewModeService->currentSessionHasPreviewAccess()->willReturn(false);
        $this->mockPreviewModeService->previewTokenExists('')->willReturn(false);

        $this->subject = new RequestInfoService(
            $this->mockRequestStack->reveal(),
            $this->mockPortalDomainService->reveal(),
            $this->mockLanguageService->reveal(),
            $this->mockUrlPrefixGenerator->reveal(),
            $this->mockPreviewModeService->reveal()
        );
    }

    public function testGetRequestIdReturnsSomething(): void
    {
        $this->whenICallGetRequestId();

        $this->thenThereShouldHaveBeenReturnedSomething();
    }

    public function testGetRequestIdReturnsSameIdOnSecondCall(): void
    {
        $this->whenICallGetRequestIdTwice();

        $this->thenTheTwoReturnedValuesShouldBeTheSame();
    }

    public function testGetRequestIdReturnsDifferentIdOnSecondInstance(): void
    {
        $this->givenASecondSubject();

        $this->whenICallGetRequestIdTwiceOnDifferentSubject();

        $this->thenTheTwoReturnedValuesShouldBeDifferent();
    }

    private function givenASecondSubject(): void
    {
        $this->subject2 = new RequestInfoService(
            $this->mockRequestStack->reveal(),
            $this->mockPortalDomainService->reveal(),
            $this->mockLanguageService->reveal(),
            $this->mockUrlPrefixGenerator->reveal(),
            $this->mockPreviewModeService->reveal()
        );
    }

    public function testUsesConsumedDomainSuffixFromResolverForPathInfo(): void
    {
        $request = Request::create('https://www.tischwelt.ch/fr/service/');
        $request->attributes->set('chameleon.request_type', 0);
        $this->mockRequestStack->getCurrentRequest()->willReturn($request);

        $portal = $this->createMock(\TdbCmsPortal::class);
        $language = $this->createMock(\TdbCmsLanguage::class);
        $this->mockPortalDomainService->getActivePortal()->willReturn($portal);
        $this->mockLanguageService->getActiveLanguage()->willReturn($language);
        $this->mockPortalDomainService->getActiveDomainPathVariantResolutionResult()->willReturn(
            $this->createResolutionResult('/fr', '/service')
        );
        $this->mockUrlPrefixGenerator->generatePrefix($portal, $language)->shouldNotBeCalled();

        self::assertSame('/service/', $this->subject->getPathInfoWithoutPortalAndLanguagePrefix());
    }

    public function testUsesConsumedPortalAndDomainSuffixFromResolverForPathInfo(): void
    {
        $request = Request::create('https://www.tischwelt.ch/shop/fr/service/');
        $request->attributes->set('chameleon.request_type', 0);
        $this->mockRequestStack->getCurrentRequest()->willReturn($request);

        $portal = $this->createMock(\TdbCmsPortal::class);
        $language = $this->createMock(\TdbCmsLanguage::class);
        $this->mockPortalDomainService->getActivePortal()->willReturn($portal);
        $this->mockLanguageService->getActiveLanguage()->willReturn($language);
        $this->mockPortalDomainService->getActiveDomainPathVariantResolutionResult()->willReturn(
            $this->createResolutionResult('/shop/fr', '/service')
        );
        $this->mockUrlPrefixGenerator->generatePrefix($portal, $language)->shouldNotBeCalled();

        self::assertSame('/service/', $this->subject->getPathInfoWithoutPortalAndLanguagePrefix());
    }

    public function testUnknownSegmentStaysUntouchedWhenResolverDidNotConsumeSuffix(): void
    {
        $request = Request::create('https://www.tischwelt.ch/frankfurt/service/');
        $request->attributes->set('chameleon.request_type', 0);
        $this->mockRequestStack->getCurrentRequest()->willReturn($request);

        $portal = $this->createMock(\TdbCmsPortal::class);
        $language = $this->createMock(\TdbCmsLanguage::class);
        $this->mockPortalDomainService->getActivePortal()->willReturn($portal);
        $this->mockLanguageService->getActiveLanguage()->willReturn($language);
        $this->mockPortalDomainService->getActiveDomainPathVariantResolutionResult()->willReturn(
            $this->createResolutionResult('', '/frankfurt/service')
        );
        $this->mockUrlPrefixGenerator->generatePrefix($portal, $language)->willReturn('');

        self::assertSame('/frankfurt/service/', $this->subject->getPathInfoWithoutPortalAndLanguagePrefix());
    }

    public function testUsesConsumedPortalIdentifierWithoutDomainSuffixForPathInfo(): void
    {
        $request = Request::create('https://www.tischwelt.ch/shop/service/');
        $request->attributes->set('chameleon.request_type', 0);
        $this->mockRequestStack->getCurrentRequest()->willReturn($request);

        $portal = $this->createMock(\TdbCmsPortal::class);
        $language = $this->createMock(\TdbCmsLanguage::class);
        $this->mockPortalDomainService->getActivePortal()->willReturn($portal);
        $this->mockLanguageService->getActiveLanguage()->willReturn($language);
        $this->mockPortalDomainService->getActiveDomainPathVariantResolutionResult()->willReturn(
            $this->createResolutionResult('/shop', '/service')
        );
        $this->mockUrlPrefixGenerator->generatePrefix($portal, $language)->shouldNotBeCalled();

        self::assertSame('/service/', $this->subject->getPathInfoWithoutPortalAndLanguagePrefix());
    }

    public function testUsesExplicitDefaultDomainSuffixForPathInfo(): void
    {
        $request = Request::create('https://www.tischwelt.ch/de/service/');
        $request->attributes->set('chameleon.request_type', 0);
        $this->mockRequestStack->getCurrentRequest()->willReturn($request);

        $portal = $this->createMock(\TdbCmsPortal::class);
        $language = $this->createMock(\TdbCmsLanguage::class);
        $this->mockPortalDomainService->getActivePortal()->willReturn($portal);
        $this->mockLanguageService->getActiveLanguage()->willReturn($language);
        $this->mockPortalDomainService->getActiveDomainPathVariantResolutionResult()->willReturn(
            $this->createResolutionResult('/de', '/service')
        );
        $this->mockUrlPrefixGenerator->generatePrefix($portal, $language)->shouldNotBeCalled();

        self::assertSame('/service/', $this->subject->getPathInfoWithoutPortalAndLanguagePrefix());
    }

    public function testUsesConsumedDomainSuffixForRootPathInfo(): void
    {
        $request = Request::create('https://www.tischwelt.ch/fr/');
        $request->attributes->set('chameleon.request_type', 0);
        $this->mockRequestStack->getCurrentRequest()->willReturn($request);

        $portal = $this->createMock(\TdbCmsPortal::class);
        $language = $this->createMock(\TdbCmsLanguage::class);
        $this->mockPortalDomainService->getActivePortal()->willReturn($portal);
        $this->mockLanguageService->getActiveLanguage()->willReturn($language);
        $this->mockPortalDomainService->getActiveDomainPathVariantResolutionResult()->willReturn(
            $this->createResolutionResult('/fr', '/')
        );
        $this->mockUrlPrefixGenerator->generatePrefix($portal, $language)->shouldNotBeCalled();

        self::assertSame('/', $this->subject->getPathInfoWithoutPortalAndLanguagePrefix());
    }

    public function testFallsBackToLegacyPrefixStrippingWithoutDomainVariantMatch(): void
    {
        $request = Request::create('https://www.tischwelt.ch/fr/service/');
        $request->attributes->set('chameleon.request_type', 0);
        $this->mockRequestStack->getCurrentRequest()->willReturn($request);

        $portal = $this->createMock(\TdbCmsPortal::class);
        $language = $this->createMock(\TdbCmsLanguage::class);
        $this->mockPortalDomainService->getActivePortal()->willReturn($portal);
        $this->mockLanguageService->getActiveLanguage()->willReturn($language);
        $this->mockPortalDomainService->getActiveDomainPathVariantResolutionResult()->willReturn(
            $this->createResolutionResult('', '/fr/service', false)
        );
        $this->mockUrlPrefixGenerator->generatePrefix($portal, $language)->willReturn('/fr');

        self::assertSame('/service/', $this->subject->getPathInfoWithoutPortalAndLanguagePrefix());
    }

    private function createResolutionResult(string $canonicalPrefix, string $remainingPath, bool $isMatched = true): DomainPathVariantResolutionResult
    {
        $prefixParts = array_values(array_filter(explode('/', trim($canonicalPrefix, '/'))));
        $consumedPortalIdentifier = '';
        $consumedDomainSuffix = '';
        if (2 <= count($prefixParts)) {
            $consumedPortalIdentifier = $prefixParts[0];
            $consumedDomainSuffix = $prefixParts[1];
        } elseif (1 === count($prefixParts)) {
            $consumedDomainSuffix = $prefixParts[0];
        }

        return new DomainPathVariantResolutionResult(
            $isMatched ? ['id' => 'domain-1', 'cms_portal_id' => 'portal-1', 'cms_language_id' => 'lang-1'] : null,
            $isMatched ? 'domain-1' : null,
            $isMatched ? 'portal-1' : null,
            $isMatched ? 'lang-1' : null,
            $consumedPortalIdentifier,
            $consumedDomainSuffix,
            $remainingPath,
            $canonicalPrefix,
            '' !== $consumedPortalIdentifier,
            '' !== $consumedDomainSuffix,
            $isMatched,
            $isMatched ? DomainPathVariantResolutionResult::MATCH_TYPE_HOST_MATCH_WITH_DOMAIN_SUFFIX : DomainPathVariantResolutionResult::MATCH_TYPE_NO_MATCH,
            false
        );
    }

    private function whenICallGetRequestId(): void
    {
        $this->returnedValue = $this->subject->getRequestId();
    }

    private function whenICallGetRequestIdTwice(): void
    {
        $this->returnedValue = $this->subject->getRequestId();
        $this->returnedValue2 = $this->subject->getRequestId();
    }

    private function whenICallGetRequestIdTwiceOnDifferentSubject(): void
    {
        $this->returnedValue = $this->subject->getRequestId();
        $this->returnedValue2 = $this->subject2->getRequestId();
    }

    private function thenThereShouldHaveBeenReturnedSomething(): void
    {
        $this->assertNotEmpty($this->returnedValue);
    }

    private function thenTheTwoReturnedValuesShouldBeTheSame(): void
    {
        $this->assertSame($this->returnedValue, $this->returnedValue2);
    }

    private function thenTheTwoReturnedValuesShouldBeDifferent(): void
    {
        $this->assertNotEquals($this->returnedValue, $this->returnedValue2);
    }
}
