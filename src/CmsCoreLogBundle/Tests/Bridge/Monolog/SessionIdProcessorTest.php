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
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class SessionIdProcessorTest extends TestCase
{
    /**
     * @var MockObject<RequestStack>
     */
    private $mockRequestStack;

    /**
     * @var SessionIdProcessor
     */
    private $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mockRequestStack = $this->createMock(RequestStack::class);
        $this->subject = new SessionIdProcessor($this->mockRequestStack);
    }

    public function testInvokeAddsTheSessionId(): void
    {
        $this->mockRequestWithSessionId('session-id');
        $result = $this->subject->__invoke(['extra' => []]);
        $this->assertSessionIdExistsInExtra($result, 'session-id');
    }

    public function testInvokeAddsTheExtraBlockIfMissing(): void
    {
        $this->mockRequestWithSessionId('session-id');
        $result = $this->subject->__invoke([]);
        $this->assertSessionIdExistsInExtra($result, 'session-id');
    }

    public function testInvokeReturnsTheInputWithoutRequest(): void
    {
        $input = ['extra' => [], 'dummy' => 'test'];
        $result = $this->subject->__invoke($input);
        $this->assertEquals($input, $result);
    }

    public function testInvokeReturnsTheInputWithoutSession(): void
    {
        $this->mockRequestWithSessionId(null);
        $input = ['extra' => [], 'dummy' => 'test2'];
        $result = $this->subject->__invoke($input);
        $this->assertEquals($input, $result);
    }

    /**
     * Pass `null` as session id to mock not having a session.
     */
    private function mockRequestWithSessionId(?string $sessionId): void
    {
        $request = $this->createMock(Request::class);
        $this->mockRequestStack->method('getCurrentRequest')->willReturn($request);
        if (null === $sessionId) {
            // mock not having a session
            $request->method('hasSession')->willReturn(false);
            $request->method('getSession')->willReturn(null);

            return;
        }
        // Mock having a session with $sessionId
        $session = $this->createMock(SessionInterface::class);
        $session->method('getId')->willReturn($sessionId);
        $request->method('hasSession')->willReturn(true);
        $request->method('getSession')->willReturn($session);
    }

    /**
     * Asserts that the given sessionId has been added as extra.session_id correctly.
     */
    private function assertSessionIdExistsInExtra(array $result, string $sessionId): void
    {
        $this->assertArrayHasKey('extra', $result);
        $this->assertArrayHasKey('session_id', $result['extra']);
        $this->assertContains($sessionId, $result['extra']);
    }
}
