<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Tests\moduleservice;

use ChameleonSystem\CoreBundle\ModuleService\ModuleResolver;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Psr\Container\ContainerInterface;

class ModuleResolverTest extends TestCase
{
    /**
     * @var ModuleResolver
     */
    private $subject;

    protected function setUp(): void
    {
        parent::setUp();
        $containerMock = $this->prophesize(ContainerInterface::class);
        $containerMock->has(Argument::any())->willReturn(false);
        $containerMock->has('service_id')->willReturn(true);
        $containerMock->get('service_id')->willReturn('success');
        $containerMock->has('service_id2')->willReturn(true);
        $containerMock->get('service_id2')->willReturn('success2');
        $this->subject = new ModuleResolver($containerMock->reveal());
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->subject = null;
    }

    /**
     * @test
     */
    public function it_returns_modules(): void
    {
        $this->assertEquals('success', $this->subject->getModule('service_id'));
    }

    /**
     * @test
     */
    public function it_returns_null_on_nonexisting_module()
    {
        $this->assertNull($this->subject->getModule('foo'));
    }

    /**
     * @test
     */
    public function it_lets_you_check_if_a_module_exists()
    {
        $this->assertTrue($this->subject->hasModule('service_id'));
        $this->assertFalse($this->subject->hasModule('name2'));
    }
}
