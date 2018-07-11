<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ChameleonModulePass implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @param ContainerBuilder $container
     *
     * @api
     */
    public function process(ContainerBuilder $container)
    {
        $moduleServiceDefinition = $container->getDefinition('chameleon_system_core.module_resolver');
        $moduleServiceIds = array_keys($container->findTaggedServiceIds('chameleon_system.module'));

        foreach ($moduleServiceIds as $moduleServiceId) {
            $moduleDefinition = $container->getDefinition($moduleServiceId);
            if (true === $moduleDefinition->isShared() && ContainerInterface::SCOPE_PROTOTYPE !== $moduleDefinition->getScope()) {
                throw new \LogicException('Chameleon modules must not be shared service instances. This module is shared: '.$moduleServiceId);
            }
        }

        $moduleServiceDefinition->addMethodCall('addModules', array($moduleServiceIds));
    }
}
