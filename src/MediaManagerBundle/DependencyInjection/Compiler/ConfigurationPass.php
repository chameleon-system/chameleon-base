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

use ChameleonSystem\MediaManagerBundle\DependencyInjection\Configuration;
use LogicException;
use Symfony\Component\Config\Definition\Processor;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Exception\InvalidArgumentException;
use Symfony\Component\DependencyInjection\Exception\ServiceNotFoundException;

class ConfigurationPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws ServiceNotFoundException
     * @throws InvalidArgumentException
     * @throws LogicException
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        $configs = $container->getExtensionConfig('chameleon_system_media_manager');
        $config = $this->getMergedConfiguration($configs);

        $serviceDefinitionPageSizeMapper = $container->getDefinition(
            'chameleon_system_media_manager.backend_module_mapper.page_size'
        );
        $arguments = $serviceDefinitionPageSizeMapper->getArguments();
        $arguments[0] = $config['available_page_sizes'];
        $serviceDefinitionPageSizeMapper->setArguments($arguments);

        $serviceDefinitionListStateService = $container->getDefinition(
            'chameleon_system_media_manager.list_state_service'
        );
        $arguments = $serviceDefinitionListStateService->getArguments();
        $arguments[2] = $config['default_page_size'];
        $serviceDefinitionListStateService->setArguments($arguments);
    }

    /**
     * @param array $configs
     *
     * @return array
     */
    private function getMergedConfiguration(array $configs)
    {
        $processor = new Processor();
        $configuration = new Configuration();

        return $processor->processConfiguration($configuration, $configs);
    }
}
