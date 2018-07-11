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

use ChameleonSystem\CoreBundle\Service\Initializer\RequestInitializer;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Class InitializeRequestListener.
 */
class InitializeRequestListener
{
    /**
     * @var RequestInitializer
     */
    private $requestInitializer;

    /**
     * @param RequestInitializer $requestInitializer
     */
    public function __construct(RequestInitializer $requestInitializer)
    {
        $this->requestInitializer = $requestInitializer;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (!$event->isMasterRequest()) {
            return;
        }
        $this->requestInitializer->initialize($event->getRequest());
    }
}
