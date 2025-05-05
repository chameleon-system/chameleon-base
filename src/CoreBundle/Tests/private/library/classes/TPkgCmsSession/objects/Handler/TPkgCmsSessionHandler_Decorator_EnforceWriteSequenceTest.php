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
     * @var PHPUnit\Framework\MockObject\MockObject
     */
    protected $oMockStorage;
    private $mockExistingSessionData;
    private $mockNewSessionData;
    /**
     * @var TPkgCmsSessionHandler_Decorator_EnforceWriteSequence
     */
    private $writeSequenceEnforcer;
    private $mockSessionId;
    /**
     * @var SessionHandlerInterface|Prophecy\Prophecy\ObjectProphecy
     */
    private $mockStorage;

    public function setUp(): void
    {
        parent::setUp();
        $this->oMockStorage = $this->createMock('SessionHandlerInterface', ['read', 'write', 'open', 'close', 'gc', 'destroy']);
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
    public function itWillReturnStoredData()
    {
        $this->given_a_session_id('mock-session-id');
        $this->given_an_old_session_exists_with_data(['lastWrite' => 1, 'data' => ['foo' => 'bar']]);
        $this->given_an_instance_of_the_write_sequence_enforcer();
        $this->then_we_expect_session_to_contain(['foo' => 'bar']);
    }

    /**
     * @test
     */
    public function itWillWriteDataIntoAnEmptyInstance()
    {
        $this->given_a_session_id('mock-session-d');
        $this->given_an_old_session_exists_with_data(null); // empty string
        $newSessionData = ['foo' => 'bar'];
        $this->given_new_session_data($newSessionData);
        $this->given_an_instance_of_the_write_sequence_enforcer();
        $this->when_we_call_write();
        $this->then_we_expect_session_to_contain($newSessionData);
    }

    public function testWriteExistingOlderEntry()
    {
        $this->given_a_session_id('mock-session-id');
        $this->given_an_old_session_exists_with_data(['lastWrite' => 1, 'data' => ['foo' => 'bar']]);
        $this->given_new_session_data(['foo' => 'bar2']);
        $this->given_an_instance_of_the_write_sequence_enforcer();
        $this->when_we_call_write();
        $this->then_we_expect_session_to_contain(['foo' => 'bar2']);
    }

    public function testWriteExistingOlderEntryWithoutACounter()
    {
        $this->given_a_session_id('mock-session-id');
        $this->given_an_old_session_exists_with_data(['data' => ['foo' => 'bar']]);
        $this->given_new_session_data(['foo' => 'bar2']);
        $this->given_an_instance_of_the_write_sequence_enforcer();
        $this->when_we_call_write();
        $this->then_we_expect_session_to_contain(['foo' => 'bar2']);
    }

    public function testWriteExistingNewerEntry()
    {
        $this->given_a_session_id('mock-session-id');
        $this->given_an_old_session_exists_with_data(['lastWrite' => 10, 'data' => ['foo' => 'bar']]);

        $this->given_new_session_data(['foo' => 'bar2']);
        $this->given_an_instance_of_the_write_sequence_enforcer();

        $this->given_the_session_data_was_changed_to(['lastWrite' => 11, 'data' => ['foo' => 'bar3']]);
        $this->when_we_call_write();
        $this->then_we_expect_session_to_contain(['foo' => 'bar3']);
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
        /* @var \SessionHandlerInterface|\Prophecy\Prophecy\ObjectProphecy $mockStorage */
        $this->mockStorage = $this->prophesize('SessionHandlerInterface');

        $existingSessionPayload = (null !== $this->mockExistingSessionData) ? serialize($this->mockExistingSessionData) : '';
        $this->mockStorage->read($this->mockSessionId)->willReturn($existingSessionPayload);

        $this->mockStorage->write(Prophecy\Argument::any(), Prophecy\Argument::any())->will(function ($args, $mockStorage) {
            /* @var \SessionHandlerInterface|\Prophecy\Prophecy\ObjectProphecy $mockStorage */
            $mockStorage->read($args[0])->willReturn($args[1]);

            return true;
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
