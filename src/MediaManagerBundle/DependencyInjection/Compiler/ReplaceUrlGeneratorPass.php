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
use Symfony\Component\DependencyInjection\Reference;

class ReplaceUrlGeneratorPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     *
     * @throws ServiceNotFoundException
     * @throws InvalidArgumentException
     * @throws LogicException
     */
    public function process(ContainerBuilder $container)
    {
        $configs = $container->getExtensionConfig('chameleon_system_media_manager');
        $config = $this->getMergedConfiguration($configs);

        $serviceDefinitionUrlGenerator = $container->getDefinition(
            'chameleon_system_core.media_manager.url_generator'
        );
        $serviceDefinitionUrlGenerator->setClass(
            '\ChameleonSystem\MediaManagerBundle\Bridge\Chameleon\MediaManagerUrlGenerator'
        );
        $serviceDefinitionUrlGenerator->addArgument($config['open_in_new_window']);
        $serviceDefinitionUrlGenerator->addArgument(new Reference('chameleon_system_core.util.input_filter'));
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
