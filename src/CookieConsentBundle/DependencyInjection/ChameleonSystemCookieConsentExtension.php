<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CookieConsentBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ChameleonSystemCookieConsentExtension extends Extension
{
    /**
     * @return void
     *
     * @throws \Exception
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/'));
        $loader->load('services.xml');

        $definition = $container->getDefinition('chameleon_system_cookie_consent.add_cookie_consent_includes_listener');
        $definition->replaceArgument(0, $config['position']);
        $definition->replaceArgument(1, $config['theme']);
        $definition->replaceArgument(2, $config['bg_color']);
        $definition->replaceArgument(3, $config['button_bg_color']);
        $definition->replaceArgument(4, $config['button_text_color']);
        $definition->replaceArgument(5, $config['privacy_policy_system_page_name']);
    }
}
