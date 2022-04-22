<?php

namespace ChameleonSystem\CmsChangeLogBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\XmlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\ConfigurableExtension;

class ChameleonSystemCmsChangeLogExtension extends ConfigurableExtension
{
    /**
     * {@inheritDoc}
     *
     * @return void
     */
    protected function loadInternal(array $mergedConfig, ContainerBuilder $container)
    {
        $loader = new XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config/'));
        $loader->load('services.xml');

        $cronjobDefinition = $container->getDefinition('chameleon_system_cms_changelog.cronjob.archive_changelog_cronjob');
        $cronjobDefinition->replaceArgument(2, $mergedConfig['days']);
    }
}
