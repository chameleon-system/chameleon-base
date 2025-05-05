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

use ChameleonSystem\MediaManager\Interfaces\MediaItemUsageFinderInterface;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class AddMediaItemFindersToChainPass implements CompilerPassInterface
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
            'chameleon_system_media_manager.usages.chain_finder'
        );
        $taggedServices = $container->findTaggedServiceIds('chameleon_system_media_manager.usage_finder');
        foreach (array_keys($taggedServices) as $serviceId) {
            $finderServiceDefinition = $container->getDefinition($serviceId);
            $interfaces = class_implements($finderServiceDefinition->getClass(), true);
            if (false === in_array(MediaItemUsageFinderInterface::class, $interfaces, true)) {
                throw new \LogicException(
                    sprintf(
                        'Usage finder service must implement MediaItemUsageFinderInterface in service %s',
                        $serviceId
                    )
                );
            }
            $serviceDefinitionChain->addMethodCall('addFinder', [$finderServiceDefinition]);
        }
    }
}
