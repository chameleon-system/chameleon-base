<?php

namespace ChameleonSystem\CmsCoreLogBundle\Tests\Bridge\Monolog;

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use ChameleonSystem\CmsCoreLogBundle\Bridge\Monolog\RequestIdProcessor;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;

class RequestIdProcessorTest extends TestCase
{
    /**
     * @var RequestInfoServiceInterface|ObjectProphecy
     */
    private $mockRequestInfoService;

    /**
     * @var RequestIdProcessor
     */
    private $subject;

    /**
     * @var array
     */
    private $actualResult;

    protected function setUp()
    {
        parent::setUp();

        $this->mockRequestInfoService = $this->prophesize(RequestInfoServiceInterface::class);
        $this->subject = new RequestIdProcessor($this->mockRequestInfoService->reveal());
    }

    public function testInvokeAddsTheRequestId(): void
    {
        $requestId = 'request-id';
        $this->givenARequestId($requestId);

        $this->whenICallInvoke(['extra' => []]);

        $this->thenTheRequestIdShouldHaveBeenAddedToExtra($requestId);
    }

    public function testInvokeAddsTheExtraBlockIfMissing(): void
    {
        $requestId = 'request-id';
        $this->givenARequestId($requestId);

        $this->whenICallInvoke([]);

        $this->thenTheRequestIdShouldHaveBeenAddedToExtra($requestId);
    }

    private function thenTheRequestIdShouldHaveBeenAddedToExtra(string $requestId): void
    {
        $this->assertArrayHasKey('extra', $this->actualResult);
        $this->assertArrayHasKey('request_id', $this->actualResult['extra']);
        $this->assertContains($requestId, $this->actualResult['extra']);
    }

    private function whenICallInvoke(array $input): void
    {
        $this->actualResult = $this->subject->__invoke($input);
    }

    private function givenARequestId(string $requestId): void
    {
        $this->mockRequestInfoService->getRequestId()->willReturn($requestId);
    }
}
