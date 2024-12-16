<?php

namespace ChameleonSystem\CmsDashboardBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\ServiceLocatorTagPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class DashboardModulesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $moduleServiceDefinition = $container->getDefinition('chameleon_system_cms_dashboard.modules_provider_service');
        $moduleServiceIds = array_keys($container->findTaggedServiceIds('chameleon_system.dashboard_module'));
        $services = [];

        foreach ($moduleServiceIds as $moduleServiceId) {
            $moduleDefinition = $container->getDefinition($moduleServiceId);
            $moduleServiceDefinition->addMethodCall("addDashboardModule", [new Reference($moduleServiceId), $moduleServiceId]);
            //$services[$moduleServiceId] = new Reference($moduleServiceId);
        }
    }
}