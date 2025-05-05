<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManagerBundle\DependencyInjection\Compiler;

use ChameleonSystem\MediaManager\Interfaces\MediaManagerExtensionInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class AddMediaManagerExtensionsToCollectionPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     *
     * @return void
     *
     * @throws ServiceNotFoundException
     * @throws InvalidArgumentException
     * @throws \LogicException
     */
    public function process(ContainerBuilder $container)
    {
        $serviceDefinitionCollection = $container->getDefinition(
            'chameleon_system_media_manager.extension_collection'
        );
        $taggedServices = $container->findTaggedServiceIds('chameleon_system_media_manager.extension');
        foreach (array_keys($taggedServices) as $serviceId) {
            $extensionServiceDefinition = $container->getDefinition($serviceId);
            $interfaces = class_implements($extensionServiceDefinition->getClass(), true);
            if (false === in_array(MediaManagerExtensionInterface::class, $interfaces, true)) {
                throw new \LogicException(
                    sprintf(
                        'Media manager extension must implement MediaManagerExtensionInterface in service %s',
                        $serviceId
                    )
                );
            }
            $serviceDefinitionCollection->addMethodCall('addExtension', [$extensionServiceDefinition]);
        }
    }
}
