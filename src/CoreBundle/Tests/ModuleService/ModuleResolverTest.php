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

class ModuleResolverTest extends TestCase
{
    /**
     * @var ModuleResolver
     */
    private $service;

    protected function setUp()
    {
        parent::setUp();
        $containerMock = $this->prophesize('Symfony\Component\DependencyInjection\ContainerInterface');
        $containerMock->get('service_id')->willReturn('success');
        $containerMock->get('service_id2')->willReturn('success2');
        $this->service = new ModuleResolver($containerMock->reveal());
    }

    protected function tearDown()
    {
        parent::tearDown();
        $this->service = null;
    }

    /**
     * @test
     */
    public function it_lets_you_add_service_ids()
    {
        $this->service->addModule('service_id');
        $this->assertEquals('success', $this->service->getModule('service_id'));
    }

    /**
     * @test
     */
    public function it_will_return_null_on_nonexisting_module()
    {
        $this->assertNull($this->service->getModule('foo'));
    }

    /**
     * @test
     */
    public function it_lets_you_check_if_a_module_exists()
    {
        $this->service->addModule('service_id');
        $this->assertTrue($this->service->hasModule('service_id'));
        $this->assertFalse($this->service->hasModule('name2'));
    }

    /**
     * @test
     */
    public function it_takes_an_array_of_modules()
    {
        $this->service->addModules(array('service_id', 'service_id2'));
        $this->assertEquals('success', $this->service->getModule('service_id'));
        $this->assertEquals('success2', $this->service->getModule('service_id2'));
    }

    /**
     * @test
     */
    public function it_provides_a_list_of_modules()
    {
        $this->service->addModules(array('service_id', 'service_id2'));
        $this->assertEquals(array('service_id', 'service_id2'), $this->service->getModules());
    }
}
