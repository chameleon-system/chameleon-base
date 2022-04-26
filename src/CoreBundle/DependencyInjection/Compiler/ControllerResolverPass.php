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
use Symfony\Component\DependencyInjection\Reference;

class ControllerResolverPass implements CompilerPassInterface
{
    /**
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('controller_resolver');
        $container->setDefinition('default.controller_resolver', $definition);

        $container->setAlias('controller_resolver', 'chameleon_system_core.controller_resolver');

        if ($container->getParameter('kernel.debug')) {
            $definition = $container->findDefinition('debug.controller_resolver');
            $arguments = $definition->getArguments();
            $arguments[0] = new Reference('chameleon_system_core.controller_resolver');
            $definition->setArguments($arguments);
        }
    }
}
