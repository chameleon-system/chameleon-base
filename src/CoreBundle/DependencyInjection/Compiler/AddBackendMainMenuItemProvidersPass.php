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

class AddBackendMainMenuItemProvidersPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        $sidebarMenuItemFactoryDefinition = $container->getDefinition('chameleon_system_core.bridge.chameleon.module.sidebar.menu_item_factory');
        $menuItemProviderServiceIds = $container->findTaggedServiceIds('chameleon_system.backend_menu_item_provider');

        foreach ($menuItemProviderServiceIds as $menuItemProviderServiceId => $tags) {
            $databaseClass = null;
            foreach ($tags as $tag) {
                if (true === \array_key_exists('databaseclass', $tag)) {
                    $databaseClass = $tag['databaseclass'];
                }
            }
            if (null === $databaseClass) {
                throw new \LogicException("The service $menuItemProviderServiceId is tagged as chameleon_system.backend_menu_item_provider, but the required attribute databaseclass is missing.");
            }
            $sidebarMenuItemFactoryDefinition->addMethodCall('addMenuItemProvider', [
                $databaseClass,
                $container->getDefinition($menuItemProviderServiceId),
            ]);
        }
    }
}
