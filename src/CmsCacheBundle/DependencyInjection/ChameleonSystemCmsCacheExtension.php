<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsCacheBundle\DependencyInjection;

use ChameleonSystem\CmsCacheBundle\QueryCache\QueryCacheDecisionMakerInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ChameleonSystemCmsCacheExtension extends Extension implements PrependExtensionInterface
{
    /**
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/'));
        $loader->load('services.xml');
        $container->registerForAutoconfiguration(QueryCacheDecisionMakerInterface::class)->addTag('chameleon_system_cms_cache.query_cache_decision_maker');
    }

    public function prepend(ContainerBuilder $container): void
    {
        $container->prependExtensionConfig('doctrine', [
            'dbal' => [
                'wrapper_class' => 'ChameleonSystem\CmsCacheBundle\Doctrine\CachingConnectionWrapper',
            ],
        ]);
    }
}
