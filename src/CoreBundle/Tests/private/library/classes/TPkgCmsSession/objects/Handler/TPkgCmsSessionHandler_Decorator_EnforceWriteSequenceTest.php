<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;

class TPkgCmsSessionHandler_Decorator_EnforceWriteSequenceTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var PHPUnit_Framework_MockObject_MockObject
     */
    protected $oMockStorage = null;
    private $mockExistingSessionData;
    private $mockNewSessionData;
    /**
     * @var TPkgCmsSessionHandler_Decorator_EnforceWriteSequence
     */
    private $writeSequenceEnforcer;
    private $mockSessionId;
    /**
     * @var \SessionHandlerInterface|\Prophecy\Prophecy\ObjectProphecy
     */
    private $mockStorage = null;

    public function setUp(): void
    {
        parent::setUp();
        $this->oMockStorage = $this->createMock('SessionHandlerInterface', array('read', 'write', 'open', 'close', 'gc', 'destroy'));
    }

    public function tearDown(): void
    {
        $this->oMockStorage = null;
        $this->mockExistingSessionData = null;
        $this->mockNewSessionData = null;
        $this->writeSequenceEnforcer = null;
        $this->mockSessionId = null;
        $this->mockStorage = null;
    }

    /**
     * @test
     */
    public function it_will_return_stored_data()
    {
        $this->given_a_session_id('mock-session-id');
        $this->given_an_old_session_exists_with_data(array('lastWrite' => 1, 'data' => array('foo' => 'bar')));
        $this->given_an_instance_of_the_write_sequence_enforcer();
        $this->then_we_expect_session_to_contain(array('foo' => 'bar'));
    }

    /**
     * @test
     */
    public function it_will_write_data_into_an_empty_instance()
    {
        $this->given_a_session_id('mock-session-d');
        $this->given_an_old_session_exists_with_data(null); // empty string
        $newSessionData = array('foo' => 'bar');
        $this->given_new_session_data($newSessionData);
        $this->given_an_instance_of_the_write_sequence_enforcer();
        $this->when_we_call_write();
        $this->then_we_expect_session_to_contain($newSessionData);
    }

    public function testWrite_ExistingOlderEntry()
    {
        $this->given_a_session_id('mock-session-id');
        $this->given_an_old_session_exists_with_data(array('lastWrite' => 1, 'data' => array('foo' => 'bar')));
        $this->given_new_session_data(array('foo' => 'bar2'));
        $this->given_an_instance_of_the_write_sequence_enforcer();
        $this->when_we_call_write();
        $this->then_we_expect_session_to_contain(array('foo' => 'bar2'));
    }

    public function testWrite_ExistingOlderEntryWithoutACounter()
    {
        $this->given_a_session_id('mock-session-id');
        $this->given_an_old_session_exists_with_data(array('data' => array('foo' => 'bar')));
        $this->given_new_session_data(array('foo' => 'bar2'));
        $this->given_an_instance_of_the_write_sequence_enforcer();
        $this->when_we_call_write();
        $this->then_we_expect_session_to_contain(array('foo' => 'bar2'));
    }

    public function testWrite_ExistingNewerEntry()
    {
        $this->given_a_session_id('mock-session-id');
        $this->given_an_old_session_exists_with_data(array('lastWrite' => 10, 'data' => array('foo' => 'bar')));

        $this->given_new_session_data(array('foo' => 'bar2'));
        $this->given_an_instance_of_the_write_sequence_enforcer();

        $this->given_the_session_data_was_changed_to(array('lastWrite' => 11, 'data' => array('foo' => 'bar3')));
        $this->when_we_call_write();
        $this->then_we_expect_session_to_contain(array('foo' => 'bar3'));
    }

    private function given_an_old_session_exists_with_data($sessionData)
    {
        $this->mockExistingSessionData = $sessionData;
    }

    private function given_new_session_data($sessionData)
    {
        $this->mockNewSessionData = $sessionData;
    }

    private function given_an_instance_of_the_write_sequence_enforcer()
    {
        $foo = null;
        /** @var \SessionHandlerInterface|\Prophecy\Prophecy\ObjectProphecy $mockStorage */
        $this->mockStorage = $this->prophesize('SessionHandlerInterface');

        $existingSessionPayload = (null !== $this->mockExistingSessionData) ? serialize($this->mockExistingSessionData) : '';
        $this->mockStorage->read($this->mockSessionId)->willReturn($existingSessionPayload);

        $this->mockStorage->write(\Prophecy\Argument::any(), \Prophecy\Argument::any())->will(function ($args, $mockStorage) {
            /** @var \SessionHandlerInterface|\Prophecy\Prophecy\ObjectProphecy $mockStorage */
            $mockStorage->read($args[0])->willReturn($args[1]);
        }
        );
        $this->writeSequenceEnforcer = new TPkgCmsSessionHandler_Decorator_EnforceWriteSequence($this->mockStorage->reveal());
        $this->writeSequenceEnforcer->read($this->mockSessionId);
    }

    private function when_we_call_write()
    {
        $this->writeSequenceEnforcer->write($this->mockSessionId, $this->mockNewSessionData);
    }

    private function given_a_session_id($sessionId)
    {
        $this->mockSessionId = $sessionId;
    }

    private function then_we_expect_session_to_contain($expectedSessionData)
    {
        $sessionRawData = $this->mockStorage->reveal()->read($this->mockSessionId);
        $newSessionData = unserialize($sessionRawData);
        $this->assertEquals($expectedSessionData, $newSessionData['data']);
    }

    private function given_the_session_data_was_changed_to($newSessionData)
    {
        $this->mockStorage->read($this->mockSessionId)->willReturn(serialize($newSessionData));
    }
}
