<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ExtranetBundle\EventListener;

use ChameleonSystem\CoreBundle\Service\RequestInfoServiceInterface;
use ChameleonSystem\ExtranetBundle\Interfaces\ExtranetUserProviderInterface;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class ValidateLoginDataListener
{
    /**
     * @var RequestInfoServiceInterface
     */
    private $requestInfoService;
    /**
     * @var ExtranetUserProviderInterface
     */
    private $extranetUserProvider;

    public function __construct(RequestInfoServiceInterface $requestInfoService, ExtranetUserProviderInterface $extranetUserProvider)
    {
        $this->requestInfoService = $requestInfoService;
        $this->extranetUserProvider = $extranetUserProvider;
    }

    public function onKernelRequest(GetResponseEvent $event): void
    {
        if (false === $event->isMasterRequest()) {
            return;
        }
        if (true === $this->requestInfoService->isBackendMode()) {
            return;
        }

        $activeUser = $this->extranetUserProvider->getActiveUser();
        if (null === $activeUser) {
            return;
        }
        $activeUser->validateLogin();
    }
}