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

use ChameleonSystem\CoreBundle\RequestType\AbstractRequestType;
use ChameleonSystem\CoreBundle\RequestType\RequestTypeInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\Event\RequestEvent;

class RequestTypeListener implements ContainerAwareInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;
    /**
     * @var AbstractRequestType
     */
    private $backendRequestType;
    /**
     * @var AbstractRequestType
     */
    private $frontendRequestType;
    /**
     * @var AbstractRequestType
     */
    private $assetRequestType;

    public function __construct(ContainerInterface $container, AbstractRequestType $backendRequestType, AbstractRequestType $frontendRequestType, AbstractRequestType $assetRequestType)
    {
        $this->container = $container;
        $this->backendRequestType = $backendRequestType;
        $this->frontendRequestType = $frontendRequestType;
        $this->assetRequestType = $assetRequestType;
    }

    /**
     * @return void
     */
    public function onKernelRequest(RequestEvent $event)
    {
        if (false === $event->isMainRequest()) {
            return;
        }
        $request = $event->getRequest();
        $requestType = $this->getRequestType();
        $requestType->initialize();

        $request->attributes->set('chameleon.request_type', $requestType->getRequestType());
        $this->container->set('chameleon_core.request_type', $requestType);
    }

    /**
     * @return RequestTypeInterface
     */
    protected function getRequestType()
    {
        switch (\chameleon::getRequestType()) {
            case RequestTypeInterface::REQUEST_TYPE_BACKEND:
                return $this->backendRequestType;
            case RequestTypeInterface::REQUEST_TYPE_ASSETS:
                return $this->assetRequestType;
            default:
                return $this->frontendRequestType;
        }
    }

    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function setContainer(?ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
