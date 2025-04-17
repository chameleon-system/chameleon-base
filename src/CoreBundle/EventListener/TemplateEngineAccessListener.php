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
use ChameleonSystem\SecurityBundle\ChameleonSystemSecurityConstants;
use ChameleonSystem\SecurityBundle\Service\SecurityHelperAccess;
use ChameleonSystem\SecurityBundle\Voter\CmsUserRoleConstants;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * TemplateEngineAccessListener checks if the current user has access to the template engine, which is the case if the
 * user is logged in to the backend. In the past, this check was performed quite early in the request processing, but
 * now we can check only after the session was started in the InitializeRequestListener.
 */
class TemplateEngineAccessListener
{
    public function __construct(
        readonly private RequestInfoServiceInterface $requestInfoService,
        readonly private SecurityHelperAccess $securityHelperAccess,
        readonly private Security $security
    ) {
    }

    /**
     * @return void
     */
    public function onKernelRequest(RequestEvent $event)
    {
        if (!$event->isMainRequest()) {
            return;
        }
        if (!$this->requestInfoService->isCmsTemplateEngineEditMode()) {
            return;
        }
        $firewallName = $this->security->getFirewallConfig($event->getRequest())?->getName();

        if (ChameleonSystemSecurityConstants::FIREWALL_BACKEND_NAME !== $firewallName) {
            // ignore frontend requests (such as access to the less files)
            return;
        }

        if (false === $this->securityHelperAccess->isGranted(CmsUserRoleConstants::CMS_USER)) {
            throw new AccessDeniedException('Template engine requested without permission.');
        }
    }
}
