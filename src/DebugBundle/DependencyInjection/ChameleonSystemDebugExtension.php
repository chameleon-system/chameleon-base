<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\DebugBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class ChameleonSystemDebugExtension extends Extension
{
    /**
     * @return void
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $config = $this->processConfiguration(new Configuration(), $configs);

        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/'));
        $loader->load('services.xml');

        $this->handleLegacyConfig($config);
        $this->configureDatabaseProfiler($container, $config['database_profiler'], $loader);
    }

    private function handleLegacyConfig(array &$config): void
    {
        if (isset($config['backtrace_enabled'])) {
            $config['database_profiler']['backtrace_enabled'] = $config['backtrace_enabled'];
        }
        if (isset($config['backtrace_limit'])) {
            $config['database_profiler']['backtrace_limit'] = $config['backtrace_limit'];
        }
    }

    private function configureDatabaseProfiler(ContainerBuilder $container, array $config, XmlFileLoader $loader): void
    {
        if (false === $config['enabled']) {
            return;
        }

        $loader->load('database_profiler.xml');

        foreach ([
            $container->getDefinition('chameleon_system_debug.database_collector'),
            $container->getDefinition('chameleon_system_debug.profiler_database_connection'),
         ] as $definition) {
            $definition->addMethodCall('setBacktraceEnabled', [$config['backtrace_enabled']]);
            $definition->addMethodCall('setBacktraceLimit', [$config['backtrace_limit']]);
        }
    }
}
