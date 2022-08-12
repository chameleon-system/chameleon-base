<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\EventListener;

use ChameleonSystem\CoreBundle\Maintenance\MaintenanceMode\MaintenanceModeServiceInterface;
use ChameleonSystem\CoreBundle\Service\Initializer\RequestInitializer;
use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class InitializeRequestListener
{
    /**
     * @var RequestInitializer
     */
    private $requestInitializer;

    /**
     * @var MaintenanceModeServiceInterface
     */
    private $maintenanceModeService;

    /**
     * @var RequestInfoServiceInterface
     */
    private $requestInfoService;

    public function __construct(
        RequestInitializer $requestInitializer,
        MaintenanceModeServiceInterface $maintenanceModeService,
        RequestInfoServiceInterface $requestInfoService
    ) {
        $this->requestInitializer = $requestInitializer;
        $this->maintenanceModeService = $maintenanceModeService;
        $this->requestInfoService = $requestInfoService;
    }

    /**
     * @return void
     */
    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        if (false === $this->requestInfoService->isBackendMode() && false === $this->requestInfoService->isCmsTemplateEngineEditMode()) {
            $this->recheckMaintenanceMode();
        }

        $this->requestInitializer->initialize($event->getRequest());
    }

    private function recheckMaintenanceMode(): void
    {
        if (true === $this->maintenanceModeService->isActive()) {
            $this->showMaintenanceModePage();
        }
    }

    private function showMaintenanceModePage(): void
    {
        if (\file_exists(PATH_WEB.'/maintenance.php')) {
            require PATH_WEB.'/maintenance.php';

            exit();
        }

        die('Sorry! This page is down for maintenance.');
    }
}
