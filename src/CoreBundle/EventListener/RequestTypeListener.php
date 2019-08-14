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

use chameleon;
use ChameleonSystem\CoreBundle\RequestType\AbstractRequestType;
use ChameleonSystem\CoreBundle\RequestType\RequestTypeInterface;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

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

    /**
     * @param ContainerInterface  $container
     * @param AbstractRequestType $backendRequestType
     * @param AbstractRequestType $frontendRequestType
     * @param AbstractRequestType $assetRequestType
     */
    public function __construct(ContainerInterface $container, AbstractRequestType $backendRequestType, AbstractRequestType $frontendRequestType, AbstractRequestType $assetRequestType)
    {
        $this->container = $container;
        $this->backendRequestType = $backendRequestType;
        $this->frontendRequestType = $frontendRequestType;
        $this->assetRequestType = $assetRequestType;
    }

    /**
     * @param GetResponseEvent $event
     */
    public function onKernelRequest(GetResponseEvent $event)
    {
        if (false === $event->isMasterRequest()) {
            return;
        }
        $request = $event->getRequest();
        $requestType = $this->getRequestType();

        if (true === $this->canAllowAdditionalDomains($request)) {
            $refererHost = $this->getRefererHost();
            if (null !== $refererHost && $request->getHost() !== $refererHost) {
                $requestType->setAllowedDomains([$refererHost]);
            }
        }

        $requestType->initialize();

        
        $request->attributes->set('chameleon.request_type', $requestType->getRequestType());
        $this->container->set('chameleon_core.request_type', $requestType);
    }

    private function canAllowAdditionalDomains(Request $request): bool
    {
        // TODO CMSUserDefined() does not work here yet (?)
        return 'true' === $request->get('__previewmode'); // && true === \TGlobalBase::CMSUserDefined();
    }

    private function getRefererHost(): ?string
    {
        if (false === \array_key_exists('HTTP_REFERER', $_SERVER)) {
            return null;
        }

        $urlParts = parse_url($_SERVER['HTTP_REFERER']);

        if (false === \array_key_exists('host', $urlParts)) {
            return null;
        }

        return $urlParts['host'];
    }

    /**
     * @return RequestTypeInterface
     */
    protected function getRequestType()
    {
        switch (chameleon::getRequestType()) {
            case RequestTypeInterface::REQUEST_TYPE_BACKEND:
                return $this->backendRequestType;
            case RequestTypeInterface::REQUEST_TYPE_ASSETS:
                return $this->assetRequestType;
            default:
                return $this->frontendRequestType;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function setContainer(ContainerInterface $container = null)
    {
        $this->container = $container;
    }
}
