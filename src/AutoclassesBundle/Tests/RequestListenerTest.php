<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\AutoclassesBundle\Tests;

use ChameleonSystem\AutoclassesBundle\Listener\RequestListener;
use ChameleonSystem\CoreBundle\RequestType\RequestTypeInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Prophecy\Prophet;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class RequestListenerTest extends TestCase
{
    use ProphecyTrait;

    /**
     * @test
     */
    public function it_generates_autoclasses_if_there_arent_any()
    {
        $cacheWarmer = $this->getCacheWarmerProphet();
        $evt = $this->getResponseEventProphet(HttpKernelInterface::MASTER_REQUEST, RequestTypeInterface::REQUEST_TYPE_BACKEND);
        $infoService = $this->getRequestInfoService(true);
        $listener = new RequestListener(__DIR__.'/fixtures/nonexistantdir', $cacheWarmer->reveal(), $infoService);
        $listener->checkAutoclasses($evt->reveal());
        $cacheWarmer->updateAllTables()->shouldHaveBeenCalled();
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function it_leaves_existing_autoclasses_alone()
    {
        $cacheWarmer = $this->getCacheWarmerProphet();
        $evt = $this->getResponseEventProphet(HttpKernelInterface::MASTER_REQUEST, RequestTypeInterface::REQUEST_TYPE_BACKEND);
        $infoService = $this->getRequestInfoService(true);
        $listener = new RequestListener(__DIR__.'/fixtures/autoclasses', $cacheWarmer->reveal(), $infoService);
        $listener->checkAutoclasses($evt->reveal());
        $cacheWarmer->updateAllTables()->shouldNotHaveBeenCalled();
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function it_only_checks_on_master_request()
    {
        $cacheWarmer = $this->getCacheWarmerProphet();
        $evt = $this->getResponseEventProphet(HttpKernelInterface::SUB_REQUEST, RequestTypeInterface::REQUEST_TYPE_BACKEND);
        $infoService = $this->getRequestInfoService(true);
        $listener = new RequestListener(__DIR__.'/fixtures/nonexistantdir', $cacheWarmer->reveal(), $infoService);
        $listener->checkAutoclasses($evt->reveal());
        $cacheWarmer->updateAllTables()->shouldNotHaveBeenCalled();
        $this->assertTrue(true);
    }

    /**
     * @test
     */
    public function it_only_runs_on_backend_request()
    {
        $cacheWarmer = $this->getCacheWarmerProphet();
        $evt = $this->getResponseEventProphet(HttpKernelInterface::MASTER_REQUEST, RequestTypeInterface::REQUEST_TYPE_FRONTEND);
        $infoService = $this->getRequestInfoService(false);
        $listener = new RequestListener(__DIR__.'/fixtures/nonexistantdir', $cacheWarmer->reveal(), $infoService);
        $listener->checkAutoclasses($evt->reveal());
        $cacheWarmer->updateAllTables()->shouldNotHaveBeenCalled();
        $this->assertTrue(true);
    }

    /**
     * @return ObjectProphecy
     */
    private function getCacheWarmerProphet()
    {
        $prophet = new Prophet();
        $cacheWarmer = $prophet->prophesize('ChameleonSystem\AutoclassesBundle\CacheWarmer\AutoclassesCacheWarmer');
        $cacheWarmer->updateAllTables()->willReturn();

        return $cacheWarmer;
    }

    /**
     * @return ObjectProphecy
     */
    private function getResponseEventProphet($type, $chameleonType)
    {
        $prophet = new Prophet();
        $evt = $prophet->prophesize('Symfony\Component\HttpKernel\Event\GetResponseEvent');
        $evt->getRequestType()->willReturn($type);

        return $evt;
    }

    private function getRequestInfoService($backendMode)
    {
        $prophet = new Prophet();
        $infoService = $prophet->prophesize('ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface');
        $infoService->isBackendMode()->willReturn($backendMode);

        return $infoService->reveal();
    }
}
