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
use Symfony\Component\DependencyInjection\Reference;

class AddMappersPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        $mapperServiceIds = $container->findTaggedServiceIds('chameleon_system.mapper');
        $services = [];

        foreach (\array_keys($mapperServiceIds) as $mapperId) {
            $mapperDefinition = $container->getDefinition($mapperId);
            if (false === \is_subclass_of($mapperDefinition->getClass(), \IViewMapper::class)) {
                throw new \LogicException('Chameleon mappers must implement IViewMapper. This one doesn\'t: '.$mapperId);
            }

            $services[$mapperId] = new Reference($mapperId);
        }

        $mapperLoader = $container->getDefinition('chameleon_system_core.mapper_loader');
        $mapperLoader->replaceArgument(0, ServiceLocatorTagPass::register($container, $services));
    }
}
