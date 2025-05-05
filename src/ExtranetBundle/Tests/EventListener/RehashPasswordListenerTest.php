<?php

namespace ChameleonSystem\ExtranetBundle\EventListener;

use ChameleonSystem\CoreBundle\Security\Password\PasswordHashGeneratorInterface;
use ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface;
use ChameleonSystem\ExtranetBundle\objects\ExtranetUserEvent;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;

class RehashPasswordListenerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @var \TdbDataExtranetUser|\PHPUnit_Framework_MockObject_MockObject
     */
    private $user;
    /**
     * @var RehashPasswordListener
     */
    private $rehashPasswordListener;
    /**
     * @var InputFilterUtilInterface|ObjectProphecy
     */
    private $inputFilterUtilMock;
    /**
     * @var PasswordHashGeneratorInterface|ObjectProphecy
     */
    private $passwordHashGeneratorMock;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        if (!defined('TCMSUSERINPUT_DEFAULTFILTER')) {
            define('TCMSUSERINPUT_DEFAULTFILTER', 'TCMSUserInput_BaseText');
        }
    }

    /**
     * {@inheritdoc}
     */
    protected function tearDown(): void
    {
        $this->inputFilterUtilMock = null;
        $this->passwordHashGeneratorMock = null;
        $this->rehashPasswordListener = null;
    }

    /**
     * @dataProvider getRehashPasswordData
     *
     * @param string $oldHashedPassword
     * @param string $newPlainPassword
     * @param bool $isNewPasswordHashRequired
     */
    public function testRehashPassword($oldHashedPassword, $newPlainPassword, $isNewPasswordHashRequired)
    {
        $this->givenAnExtranetUser($oldHashedPassword, $isNewPasswordHashRequired);
        $this->givenAPasswordInRequestPostData($newPlainPassword);
        $this->givenARehashPasswordListener($isNewPasswordHashRequired);
        $this->whenICallRehashPassword();
        $this->thenThePasswordShouldBeBCrypted();
    }

    /**
     * @param string $oldHashedPassword
     * @param bool $isNewPasswordHashRequired
     */
    private function givenAnExtranetUser($oldHashedPassword, $isNewPasswordHashRequired)
    {
        $this->user = $this
            ->getMockBuilder('\TdbDataExtranetUser')
            ->disableAutoload()
            ->disableOriginalConstructor()
            ->setMethods(['SaveFieldsFast'])
            ->getMock();
        $this->user->fieldPassword = $oldHashedPassword;
        $this->user->expects($this->exactly($isNewPasswordHashRequired ? 1 : 0))->method('SaveFieldsFast');
    }

    /**
     * @param string $newPlainPassword
     */
    private function givenAPasswordInRequestPostData($newPlainPassword)
    {
        $this->inputFilterUtilMock = $this->prophesize('ChameleonSystem\CoreBundle\Util\InputFilterUtilInterface');
        $this->inputFilterUtilMock->getFilteredPostInput('password')->willReturn($newPlainPassword);
    }

    /**
     * @param bool $isNewPasswordHashRequired
     */
    private function givenARehashPasswordListener($isNewPasswordHashRequired)
    {
        $this->passwordHashGeneratorMock = $this->prophesize(PasswordHashGeneratorInterface::class);
        $this->passwordHashGeneratorMock->needsRehash(Argument::any())->willReturn($isNewPasswordHashRequired);
        $this->passwordHashGeneratorMock->hash(Argument::any())->willReturn('$2y$12$bar');
        $this->rehashPasswordListener = new RehashPasswordListener($this->inputFilterUtilMock->reveal(), $this->passwordHashGeneratorMock->reveal());
    }

    private function whenICallRehashPassword()
    {
        $event = new ExtranetUserEvent($this->user);
        $this->rehashPasswordListener->rehashPassword($event);
    }

    private function thenThePasswordShouldBeBCrypted()
    {
        $this->assertStringStartsWith('$2y$12$', $this->user->fieldPassword);
    }

    /**
     * @return array
     */
    public function getRehashPasswordData()
    {
        return [
            [
                'd4628394e|66752b939acaffc7f3c58f56676a995f7add1895',
                'foo',
                true,
            ],
            [
                '$2y$12$LRDuXjBoib0Du5QxjUW2CuJJzxQLSyS2e4UZOctFCmvGkJv1UoRh2',
                'foo',
                false,
            ],
        ];
    }
}
