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
use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ChameleonMappersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        $mapperServiceDefinition = $container->getDefinition('chameleon_system_core.mapper_loader');

        $mapperServiceIds = array_keys($container->findTaggedServiceIds('chameleon_system.mapper'));
        $services = [];

        foreach ($mapperServiceIds as $mapperServiceId) {
            $services[$mapperServiceId] = new Reference($mapperServiceId);
        }

        $mapperServiceDefinition->replaceArgument(0, ServiceLocatorTagPass::register($container, $services));
    }
}
