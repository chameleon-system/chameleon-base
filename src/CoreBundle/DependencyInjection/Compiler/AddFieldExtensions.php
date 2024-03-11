<?php

namespace ChameleonSystem\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class AddFieldExtensions implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->has('chameleon_system_core.service.field_extension_render_service')) {
            return;
        }

        $definition = $container->findDefinition('chameleon_system_core.service.field_extension_render_service');

        $taggedServices = $container->findTaggedServiceIds('chameleon_system_core.field_extension');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('addFieldExtension', [new Reference($id)]);
        }
    }
}
