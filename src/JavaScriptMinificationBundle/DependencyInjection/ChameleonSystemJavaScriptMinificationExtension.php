<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\JavaScriptMinificationBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ChameleonSystemJavaScriptMinificationExtension extends Extension
{
    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/'));
        $loader->load('services.xml');
        $jsMinifierToUse = $config['js_minifier_to_use'];
        if (null === $jsMinifierToUse) {
            $minifierEventListenerDefinition = $container->getDefinition('chameleon_system_javascript_minify.javascript_minify_event_listener');
            $minifierEventListenerDefinition->clearTag('kernel.event_listener');
        }
        $container->setParameter('js_minifier_integration', $jsMinifierToUse);
    }
}
