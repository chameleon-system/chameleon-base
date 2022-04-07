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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ControllerResolver;

/**
 * Class ChameleonControllerResolver.
 */
class ChameleonControllerResolver extends ControllerResolver
{
    /** @var ContainerInterface $container */
    private $container;
    /** @var ControllerResolver The default Symfony resolver */
    private $defaultControllerResolver;
    /**
     * @var array
     */
    private $controllerList;
    /**
     * @var ChameleonController
     */
    private $defaultChameleonController;

    /**
     * @param ContainerInterface  $container
     * @param ControllerResolver  $defaultControllerResolver
     * @param array               $controllerList
     * @param ChameleonController $defaultChameleonController
     */
    public function __construct(ContainerInterface $container, ControllerResolver $defaultControllerResolver, array $controllerList, $defaultChameleonController)
    {
        $this->container = $container;
        $this->defaultControllerResolver = $defaultControllerResolver;
        $this->controllerList = $controllerList;
        $this->defaultChameleonController = $defaultChameleonController;
    }

    /**
     * {@inheritdoc}
     */
    public function getController(Request $request)
    {
        $controller = $request->attributes->get('_controller', null);
        if (null === $controller) {
            return null;
        }
        if (is_object($controller)) {
            return $controller;
        }

        /**
         * First check if we have a ChameleonController.
         * If not, then use the default Symfony resolver.
         */
        if (in_array($controller, $this->controllerList)) {
            $controllerObject = $this->container->get($controller);
            $this->container->set('chameleon_system_core.chameleon_controller', $controllerObject);
        } else {
            $controllerObject = $this->defaultControllerResolver->getController($request);
            $this->container->set('chameleon_system_core.chameleon_controller', $this->defaultChameleonController);
        }

        return $controllerObject;
    }
}
