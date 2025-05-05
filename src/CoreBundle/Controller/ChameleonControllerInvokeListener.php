<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\Controller;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ChameleonControllerInvokeListener implements EventSubscriberInterface
{
    /**
     * @var ContainerInterface
     */
    private $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @return void
     */
    public function onInvoke(ChameleonControllerInvokeEvent $event)
    {
        $this->container->set('chameleon_system_core.chameleon_controller', $event->getController());
    }

    public static function getSubscribedEvents(): array
    {
        return [ChameleonControllerEvents::INVOKE => 'onInvoke'];
    }
}
