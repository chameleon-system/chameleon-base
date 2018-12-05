<?php

namespace ChameleonSystem\CoreBundle\Tests\Service;

use\ChameleonSystem\CoreBundle\Service\LanguageServiceInterface;
use ChameleonSystem\CoreBundle\Service\PortalDomainServiceInterface;
use ChameleonSystem\CoreBundle\Service\RequestInfoService;
use ChameleonSystem\CoreBundle\Util\UrlPrefixGeneratorInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\RequestStack;

class RequestInfoServiceTest extends TestCase
{
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
     * @var RequestInfoService
     */
    private $subject;

    protected function setUp()
    {
        parent::setUp();

        $this->mockRequestStack = $this->prophesize(RequestStack::class);
        $this->mockPortalDomainService = $this->prophesize(PortalDomainServiceInterface::class);
        $this->mockLanguageService = $this->prophesize(LanguageServiceInterface::class);
        $this->mockUrlPrefixGenerator = $this->prophesize(UrlPrefixGeneratorInterface::class);

        $this->subject = new RequestInfoService(
            $this->mockRequestStack->reveal(),
            $this->mockPortalDomainService->reveal(),
            $this->mockLanguageService->reveal(),
            $this->mockUrlPrefixGenerator->reveal()
        );
    }

    public function testGetRequestIdReturnsSomething(): void
    {
        $requestId = $this->subject->getRequestId();

        $this->assertNotEmpty($requestId);
    }

    public function testGetRequestIdReturnsSameIdOnSecondCall(): void
    {
        $requestId1 = $this->subject->getRequestId();
        $requestId2 = $this->subject->getRequestId();

        $this->assertSame($requestId2, $requestId1);
    }

    public function testGetRequestIdReturnsDifferentIdOnSecondInstance(): void
    {
        $subject2 = new RequestInfoService(
            $this->mockRequestStack->reveal(),
            $this->mockPortalDomainService->reveal(),
            $this->mockLanguageService->reveal(),
            $this->mockUrlPrefixGenerator->reveal()
        );

        $requestId1 = $this->subject->getRequestId();
        $requestId2 = $subject2->getRequestId();

        $this->assertNotEquals($requestId2, $requestId1);
    }
}
