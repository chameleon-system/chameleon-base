<?php
/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsDashboardBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class DashboardModulesPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('chameleon_system_cms_dashboard.modules_provider_service')) {
            return;
        }

        $moduleServiceDefinition = $container->getDefinition('chameleon_system_cms_dashboard.modules_provider_service');
        $taggedServices = $container->findTaggedServiceIds('chameleon_system.dashboard_widget');

        $widgets = [];

        foreach ($taggedServices as $serviceId => $tags) {
            foreach ($tags as $attributes) {
                $collection = $attributes['collection'] ?? 'default';
                $priority = $attributes['priority'] ?? 0;

                $widgets[] = [
                    'serviceId' => $serviceId,
                    'collection' => $collection,
                    'priority' => (int) $priority,
                ];
            }
        }

        // sort widgets by collection und priority
        usort($widgets, function ($a, $b) {
            return $b['priority'] <=> $a['priority'];
        });

        // add widgets to dashboard service
        foreach ($widgets as $widget) {
            $moduleServiceDefinition->addMethodCall(
                'addDashboardWidget',
                [new Reference($widget['serviceId']), $widget['serviceId'], $widget['collection'], $widget['priority']]
            );
        }
    }
}
