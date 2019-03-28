<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\ModuleService;

use Psr\Container\ContainerInterface;

class ModuleResolver implements ModuleResolverInterface
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
     * {@inheritdoc}
     */
    public function getModule($name)
    {
        if (false === $this->container->has($name)) {
            return null;
        }

        return $this->container->get($name);
    }

    /**
     * {@inheritdoc}
     */
    public function hasModule($name)
    {
        return $this->container->has($name);
    }
}
