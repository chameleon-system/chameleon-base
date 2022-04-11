<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\TrackViewsBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ChameleonSystemTrackViewsExtension extends Extension
{
    /**
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);
        $bundleAlias = $this->getAlias();

        if ($config['enabled']) {
            $container->setParameter($bundleAlias.'.enabled', true);
            $container->setParameter($bundleAlias.'.target_table', $config['target_table']);
            $container->setParameter($bundleAlias.'.time_to_live', $config['time_to_live']);

            $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/'));
            $loader->load('services.xml');
        } else {
            $container->setParameter($bundleAlias.'.enabled', false);
        }
    }
}
