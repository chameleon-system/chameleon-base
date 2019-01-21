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
            $this->mockUrlPrefixGenerator->reveal()
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
