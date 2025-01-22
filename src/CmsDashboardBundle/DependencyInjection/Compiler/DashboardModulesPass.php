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
                $collectionPriority = $attributes['collectionPriority'] ?? 0;
                $priority = $attributes['priority'] ?? 0;

                $widgets[] = [
                    'serviceId' => $serviceId,
                    'collection' => $collection,
                    'collectionPriority' => (int) $collectionPriority,
                    'priority' => (int) $priority,
                ];
            }
        }

        // Sort widgets by collectionPriority and then by priority
        usort($widgets, function ($a, $b) {
            if ($a['collectionPriority'] !== $b['collectionPriority']) {
                return $b['collectionPriority'] <=> $a['collectionPriority'];
            }

            return $b['priority'] <=> $a['priority'];
        });

        // Add widgets to the dashboard service
        foreach ($widgets as $widget) {
            $moduleServiceDefinition->addMethodCall(
                'addDashboardWidget',
                [new Reference($widget['serviceId']), $widget['serviceId'], $widget['collection'], $widget['priority']]
            );
        }
    }
}
