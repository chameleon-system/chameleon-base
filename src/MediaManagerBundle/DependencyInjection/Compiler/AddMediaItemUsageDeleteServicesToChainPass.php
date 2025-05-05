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

use ChameleonSystem\MediaManager\Interfaces\MediaItemUsageDeleteServiceInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class AddMediaItemUsageDeleteServicesToChainPass implements CompilerPassInterface
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
        $serviceDefinitionChain = $container->getDefinition(
            'chameleon_system_media_manager.usages.delete_service_chain'
        );
        $taggedServices = $container->findTaggedServiceIds('chameleon_system_media_manager.usage_delete_service');
        foreach (array_keys($taggedServices) as $serviceId) {
            $deleteServiceDefinition = $container->getDefinition($serviceId);
            $interfaces = class_implements($deleteServiceDefinition->getClass(), true);
            if (false === in_array(MediaItemUsageDeleteServiceInterface::class, $interfaces, true)) {
                throw new \LogicException(
                    sprintf(
                        'Usage delete service must implement MediaItemUsageDeleteServiceInterface in service %s',
                        $serviceId
                    )
                );
            }
            $serviceDefinitionChain->addMethodCall(
                'addUsageDeleteService',
                [$deleteServiceDefinition]
            );
        }
    }
}
