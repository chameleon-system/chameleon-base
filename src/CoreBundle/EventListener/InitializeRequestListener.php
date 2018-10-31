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

use ChameleonSystem\CoreBundle\Exception\MaintenanceModeErrorException;
use ChameleonSystem\CoreBundle\Maintenance\MaintenanceMode\MaintenanceModeServiceInterface;
use ChameleonSystem\CoreBundle\Service\Initializer\RequestInitializer;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

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

    public function __construct(
        RequestInitializer $requestInitializer,
        MaintenanceModeServiceInterface $maintenanceModeService
    ) {
        $this->requestInitializer = $requestInitializer;
        $this->maintenanceModeService = $maintenanceModeService;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }

        $this->recheckMaintenanceMode($event);

        $this->requestInitializer->initialize($event->getRequest());
    }

    private function recheckMaintenanceMode(GetResponseEvent $event): void
    {
        try {
            if (true === $this->maintenanceModeService->isActivated()) {
                $this->redirectToCurrentPage($event);
            }
        } catch (MaintenanceModeErrorException $exception) {
            // TODO what to do here?
        }
    }

    private function redirectToCurrentPage(GetResponseEvent $event): void
    {
        $request = $event->getRequest();
        if (true === $request->isMethodSafe()) {
            $event->setResponse(new RedirectResponse($_SERVER['REQUEST_URI']));
        } else {
            // Redirect is not meaningful for a POST request:
            $event->setResponse(new Response('Maintenance mode is now active', Response::HTTP_SERVICE_UNAVAILABLE));
        }
    }
}
