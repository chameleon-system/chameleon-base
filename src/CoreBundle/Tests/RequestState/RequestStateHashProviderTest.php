<?php

namespace ChameleonSystem\CoreBundle\Tests\RequestState;

use ChameleonSystem\CoreBundle\RequestState\Interfaces\HashCalculationLockInterface;
use ChameleonSystem\CoreBundle\RequestState\Interfaces\RequestStateElementProviderInterface;
use ChameleonSystem\CoreBundle\RequestState\RequestStateHashProvider;
use ChameleonSystem\CoreBundle\Util\HashInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class RequestStateHashProviderTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var RequestStateHashProvider
     */
    private $subject;
    /**
     * @var SessionInterface|ObjectProphecy
     */
    private $mockSession;
    /**
     * @var Request|ObjectProphecy
     */
    private $mockRequest;
    /**
     * @var RequestStack
     */
    private $mockRequestStack;
    /**
     * @var HashInterface|ObjectProphecy
     */
    private $mockHashArray;

    /**
     * @var RequestStateElementProviderInterface|ObjectProphecy
     */
    private $mockElementProvider1;
    /**
     * @var RequestStateElementProviderInterface|ObjectProphecy
     */
    private $mockElementProvider2;
    /**
     * @var ObjectProphecy|HashCalculationLockInterface
     */
    private $mockLock;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->mockHashArray = $this->prophesize(HashInterface::class);
        $this->mockHashArray->hash32(Argument::any())->willReturn('fake-hash');
        $this->mockRequest = $this->prophesize(Request::class);
        $this->mockRequest->hasSession()->willReturn(true);
        $this->mockSession = $this->prophesize(SessionInterface::class);
        $this->mockElementProvider1 = $this->prophesize(RequestStateElementProviderInterface::class);
        $this->mockElementProvider2 = $this->prophesize(RequestStateElementProviderInterface::class);
        $this->mockElementProvider1->getStateElements(Argument::any())->willReturn([]);
        $this->mockElementProvider2->getStateElements(Argument::any())->willReturn([]);
        $this->mockRequest->getSession()->willReturn($this->mockSession);
        $this->mockLock = $this->prophesize(HashCalculationLockInterface::class);
        $this->mockLock->lock()->willReturn(true);
        $this->mockLock->release()->willReturn();
        $this->mockRequestStack = new RequestStack();
        $this->mockRequestStack->push($this->mockRequest->reveal());

        $this->subject = new RequestStateHashProvider(
            $this->mockHashArray->reveal(),
            $this->mockLock->reveal(),
            $this->mockRequestStack,
            [$this->mockElementProvider1->reveal(), $this->mockElementProvider2->reveal()]
        );
    }

    public function testReturnHashEvenIfSessionIsNotStarted()
    {
        $this->mockRequest->hasSession()->willReturn(false);
        $this->mockElementProvider1->getStateElements($this->mockRequest)->willReturn(['test']);
        $this->mockHashArray->hash32(['test'])->willReturn('hash-while-starting-session');
        $this->assertEquals('hash-while-starting-session', $this->subject->getHash(null));
    }

    public function testReturnNullIfNoRequest()
    {
        $this->mockRequestStack->pop();
        $this->assertNull($this->subject->getHash(null));
    }

    public function testUsesRequestPassed()
    {
        /** @var Request|ObjectProphecy $request */
        $request = $this->prophesize(Request::class);
        $request->hasSession()->willReturn(true);
        $this->mockElementProvider1->getStateElements($request)->willReturn(['new1' => 'b1']);
        $this->mockElementProvider2->getStateElements($request)->willReturn(['new2' => 'b2']);
        $this->mockHashArray->hash32(
            ['new1' => 'b1', 'new2' => 'b2']
        )->willReturn('hash');

        $this->assertEquals('hash', $this->subject->getHash($request->reveal()));
    }

    public function testHashState()
    {
        $this->mockElementProvider1->getStateElements($this->mockRequest->reveal())->willReturn(['a1' => 'b1']);
        $this->mockElementProvider2->getStateElements($this->mockRequest->reveal())->willReturn(['ac' => 'fc1']);
        $this->mockHashArray->hash32(
            ['a1' => 'b1', 'ac' => 'fc1']
        )->willReturn('hash');
        $this->assertEquals('hash', $this->subject->getHash(null));
    }

    public function testHashStateWithIntersectingKeys()
    {
        $this->mockElementProvider1->getStateElements($this->mockRequest->reveal())->willReturn(['overlap' => 'b1']);
        $this->mockElementProvider2->getStateElements($this->mockRequest->reveal())->willReturn(
            ['ac' => 'fc1', 'overlap' => 'fc1']
        );
        $this->mockHashArray->hash32(
            ['overlap' => 'fc1', 'ac' => 'fc1']
        )->willReturn('hash');
        $this->assertEquals('hash', $this->subject->getHash(null));
    }

    public function testLockDuringHashCalculation()
    {
        $this->mockLock->lock()->shouldBeCalled()->will(
            function ($args) {
                $this->release()->shouldBeCalled();
            }
        );
        $this->subject->getHash();
    }

    public function testReturnNullIfHashGenerationIsAlreadyRunning()
    {
        $this->mockLock->lock()->willReturn(false);

        $this->assertNull($this->subject->getHash());
    }
}
