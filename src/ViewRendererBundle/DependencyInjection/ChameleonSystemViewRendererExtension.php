<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\ViewRendererBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class ChameleonSystemViewRendererExtension extends ConfigurableExtension
{
    /**
     * {@inheritDoc}
     *
     * @param array<string, mixed> $mergedConfig
     *
     * @return void
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/'));
        $loader->load('services.xml');

        $lessCompiler = $container->getDefinition('chameleon_system_view_renderer.less_compiler');
        $lessCompiler->replaceArgument(0, $mergedConfig['css_dir']);
        if ('' !== $mergedConfig['static_content_url']) {
            $lessCompiler->addMethodCall(
                'addAdditionalVariables',
                [['STATIC_CONTENT_URL' => '"'.$mergedConfig['static_content_url'].'"']]
            );
            // NOTE The quotes here around $mergedConfig[...] are necessary for the pre-processing in the less compiler.
        }
    }
}
