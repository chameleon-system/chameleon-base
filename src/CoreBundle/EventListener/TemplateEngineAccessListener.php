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

use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use TCMSUser;

/**
 * TemplateEngineAccessListener checks if the current user has access to the template engine, which is the case if the
 * user is logged in to the backend. In the past, this check was performed quite early in the request processing, but
 * now we can check only after the session was started in the InitializeRequestListener.
 */
class TemplateEngineAccessListener
{
    /**
     * @var RequestInfoServiceInterface
     */
    private $requestInfoService;

    /**
     * @param RequestInfoServiceInterface $requestInfoService
     */
    public function __construct(RequestInfoServiceInterface $requestInfoService)
    {
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
        if (!$this->requestInfoService->isCmsTemplateEngineEditMode()) {
            return;
        }
        if (!TCMSUser::CMSUserDefined()) {
            throw new AccessDeniedException('Template engine requested without permission.');
        }
    }
}
