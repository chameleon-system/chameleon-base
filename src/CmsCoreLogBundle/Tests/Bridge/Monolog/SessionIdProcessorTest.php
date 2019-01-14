<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsCoreLogBundle\Tests\Bridge\Monolog;

use ChameleonSystem\CmsCoreLogBundle\Bridge\Monolog\SessionIdProcessor;
use PHPUnit\Framework\TestCase;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionIdProcessorTest extends TestCase
{
    /**
     * @var RequestStack|ObjectProphecy
     */
    private $mockRequestStack;

    /**
     * @var null|Request|ObjectProphecy
     */
    private $mockRequest;

    /**
     * @var null|SessionInterface|ObjectProphecy
     */
    private $mockSession;

    /**
     * @var SessionIdProcessor
     */
    private $subject;

    /**
     * @var array
     */
    private $actualResult;

    protected function setUp()
    {
        parent::setUp();

        $this->mockRequestStack = $this->prophesize(RequestStack::class);
        $this->subject = new SessionIdProcessor($this->mockRequestStack->reveal());
    }

    public function testInvokeAddsTheSessionId(): void
    {
        $sessionId = 'session-id';
        $this->givenARequest();
        $this->givenASession($sessionId);

        $this->whenICallInvoke(['extra' => []]);

        $this->thenTheSessionIdShouldHaveBeenAddedToExtra($sessionId);
    }

    public function testInvokeAddsTheExtraBlockIfMissing(): void
    {
        $sessionId = 'session-id';
        $this->givenARequest();
        $this->givenASession($sessionId);

        $this->whenICallInvoke([]);

        $this->thenTheSessionIdShouldHaveBeenAddedToExtra($sessionId);
    }

    public function testInvokeReturnsTheInputWithoutRequest(): void
    {
        $input = ['extra' => [], 'dummy' => 'test'];

        $this->whenICallInvoke($input);

        $this->thenTheInputShouldHaveBeenReturned($input);
    }

    public function testInvokeReturnsTheInputWithoutSession(): void
    {
        $this->givenARequest();

        $input = ['extra' => [], 'dummy' => 'test2'];

        $this->whenICallInvoke($input);

        $this->thenTheInputShouldHaveBeenReturned($input);
    }

    private function givenARequest(): void
    {
        $this->mockRequest = $this->prophesize(Request::class);
        $this->mockRequestStack->getCurrentRequest()->willReturn($this->mockRequest);
    }

    private function givenASession(string $sessionId): void
    {
        $this->mockSession = $this->prophesize(SessionInterface::class);
        $this->mockSession->getId()->willReturn($sessionId);
        $this->mockRequest->getSession()->willReturn($this->mockSession);
    }

    private function whenICallInvoke(array $input): void
    {
        $this->actualResult = $this->subject->__invoke($input);
    }

    private function thenTheSessionIdShouldHaveBeenAddedToExtra(string $sessionId): void
    {
        $this->assertArrayHasKey('extra', $this->actualResult);
        $this->assertArrayHasKey('session_id', $this->actualResult['extra']);
        $this->assertContains($sessionId, $this->actualResult['extra']);
    }

    private function thenTheInputShouldHaveBeenReturned(array $input): void
    {
        $this->assertEquals($input, $this->actualResult);
    }
}
