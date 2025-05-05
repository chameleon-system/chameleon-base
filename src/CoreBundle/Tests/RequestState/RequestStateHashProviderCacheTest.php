<?php

namespace ChameleonSystem\CoreBundle\Tests\RequestState;

use ChameleonSystem\CoreBundle\RequestState\Interfaces\RequestStateHashProviderInterface;
use ChameleonSystem\CoreBundle\RequestState\RequestStateHashProviderCache;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\EventDispatcher\Event;

class RequestStateHashProviderCacheTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var RequestStateHashProviderCache
     */
    private $subject;
    /**
     * @var RequestStateHashProviderInterface
     */
    private $mockSubject;
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

    private $result;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->mockSubject = $this->prophesize(RequestStateHashProviderInterface::class);
        $this->mockSubject->getHash(Argument::any())->willReturn('some-fake-hash');

        $this->mockRequest = $this->prophesize(Request::class);
        $this->mockRequest->hasSession()->willReturn(true);
        $this->mockSession = $this->prophesize(SessionInterface::class);
        $this->mockSession->isStarted()->willReturn(true);
        $this->mockRequest->getSession()->willReturn($this->mockSession);
        $this->mockRequestStack = new RequestStack();
        $this->mockRequestStack->push($this->mockRequest->reveal());

        $this->subject = new RequestStateHashProviderCache($this->mockSubject->reveal(), $this->mockRequestStack);
    }

    public function testUseCacheIfSessionIsStarted()
    {
        $this
            ->givenThatTheMockSubjectReturnsTheHash('test')
            ->and()
            ->givenThatTheHashWasRetrievedFromTheMockSubject()
            ->and()
            ->givenThatTheMockSubjectReturnsTheHash('new data')

            ->whenGetHashIsCalled()

            ->thenTheResultShouldBe('test');
    }

    public function testDoesNotUseCacheIfSessionIsNotStarted()
    {
        $this
            ->givenThatTheSessionIsNotStarted()
            ->and()
            ->givenThatTheMockSubjectReturnsTheHash('test')
            ->and()
            ->givenThatTheHashWasRetrievedFromTheMockSubject()
            ->and()
            ->givenThatTheMockSubjectReturnsTheHash('new data')

            ->whenGetHashIsCalled()

            ->thenTheResultShouldBe('new data');
    }

    public function testReturnNullIfThereIsNoCurrentRequest()
    {
        $mockRequestStack = new RequestStack();
        $subject = new RequestStateHashProviderCache($this->mockSubject->reveal(), $mockRequestStack);
        $this->assertNull($subject->getHash());
    }

    public function testCacheReset()
    {
        $this->givenThatTheMockSubjectReturnsTheHash('test')
            ->and()
            ->givenThatTheHashWasRetrievedFromTheMockSubject()
            ->and()
            ->givenThatTheMockSubjectReturnsTheHash('new data')

            ->whenStateDataChangedIsCalled()
            ->and()
            ->whenGetHashIsCalled()

            ->thenTheResultShouldBe('new data');
    }

    /**
     * @param string $hash
     *
     * @return $this
     */
    private function givenThatTheMockSubjectReturnsTheHash($hash)
    {
        $this->mockSubject->getHash(Argument::any())->willReturn($hash);

        return $this;
    }

    /**
     * @return $this
     */
    private function givenThatTheHashWasRetrievedFromTheMockSubject()
    {
        $this->subject->getHash();

        return $this;
    }

    /**
     * @return $this
     */
    private function givenThatTheSessionIsNotStarted()
    {
        $this->mockSession->isStarted()->willReturn(false);

        return $this;
    }

    /**
     * @return $this
     */
    private function and()
    {
        return $this;
    }

    /**
     * @return $this
     */
    private function whenGetHashIsCalled()
    {
        $this->result = $this->subject->getHash();

        return $this;
    }

    /**
     * @return $this
     */
    private function thenTheResultShouldBe($expectedResult)
    {
        $this->assertEquals($expectedResult, $this->result);

        return $this;
    }

    /**
     * @return $this
     */
    private function whenStateDataChangedIsCalled()
    {
        $this->subject->onStateDataChanged(new Event());

        return $this;
    }
}
