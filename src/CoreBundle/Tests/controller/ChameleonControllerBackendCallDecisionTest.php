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

class ChameleonControllerBackendCallDecisionTest extends TestCase
{
    private $controller;
    /** @var ReflectionMethod */
    private $isBackendCallMethod;
    /**
     * @var array
     */
    private $originalServerVars;

    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();
        define('REQUEST_PROTOCOL', 'https');
    }

    protected function setUp(): void
    {
        parent::setUp();
        include_once __DIR__.'/../../FrontController/chameleon.php';
        $this->controller = new chameleon();
        $controllerReflection = new ReflectionObject($this->controller);
        $this->isBackendCallMethod = $controllerReflection->getMethod('isBackendCall');
        $this->isBackendCallMethod->setAccessible(true);
        $this->originalServerVars = $_SERVER;
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        $this->controller = null;
        $this->isBackendCallMethod = null;
        $_SERVER = $this->originalServerVars;
    }

    /**
     * @test
     */
    public function it_ignores_host()
    {
        $_SERVER['HTTP_HOST'] = 'cms.foo.bar';
        $requestUri = '/foobar';
        $this->assertFalse($this->isBackendCallMethod->invoke($this->controller, $requestUri));
    }

    /**
     * @test
     */
    public function it_ignores_host_with_admin()
    {
        $_SERVER['HTTP_HOST'] = 'admin.foo.bar';
        $requestUri = '/foobar';
        $this->assertFalse($this->isBackendCallMethod->invoke($this->controller, $requestUri));
    }

    /**
     * @test
     */
    public function it_uses_request_uri()
    {
        $_SERVER['HTTP_HOST'] = 'cms.foo.bar';
        $requestUri = '/cms';
        $this->assertTrue($this->isBackendCallMethod->invoke($this->controller, $requestUri));
    }

    /**
     * @test
     */
    public function it_uses_request_uri_that_only_ends_with_cms()
    {
        $_SERVER['HTTP_HOST'] = 'cms.foo.bar';
        $requestUri = '/foobar/cms';
        $this->assertFalse($this->isBackendCallMethod->invoke($this->controller, $requestUri));
    }

    /**
     * @test
     */
    public function it_accepts_frontend_calls()
    {
        $_SERVER['HTTP_HOST'] = 'cms.foo.bar';
        $requestUri = '/cmsfoobar';
        $this->assertFalse($this->isBackendCallMethod->invoke($this->controller, $requestUri));
    }
}
