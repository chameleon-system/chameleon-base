<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\MediaManagerBundle;

use ChameleonSystem\MediaManagerBundle\DependencyInjection\Compiler\AddMediaItemFindersToChainPass;
use ChameleonSystem\MediaManagerBundle\DependencyInjection\Compiler\AddMediaItemSortColumnsToCollectionPass;
use ChameleonSystem\MediaManagerBundle\DependencyInjection\Compiler\AddMediaItemUsageDeleteServicesToChainPass;
use ChameleonSystem\MediaManagerBundle\DependencyInjection\Compiler\AddMediaManagerExtensionsToCollectionPass;
use ChameleonSystem\MediaManagerBundle\DependencyInjection\Compiler\ConfigurationPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ChameleonSystemMediaManagerBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new ConfigurationPass());
        $container->addCompilerPass(new AddMediaItemFindersToChainPass());
        $container->addCompilerPass(new AddMediaItemUsageDeleteServicesToChainPass());
        $container->addCompilerPass(new AddMediaItemSortColumnsToCollectionPass());
        $container->addCompilerPass(new AddMediaManagerExtensionsToCollectionPass());
    }
}
