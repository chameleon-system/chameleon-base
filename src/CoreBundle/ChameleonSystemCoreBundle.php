<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle;

use ChameleonSystem\CoreBundle\DependencyInjection\Compiler\AddBackendMainMenuItemProvidersPass;
use ChameleonSystem\CoreBundle\DependencyInjection\Compiler\AddCronJobsPass;
use ChameleonSystem\CoreBundle\DependencyInjection\Compiler\AddMappersPass;
use ChameleonSystem\CoreBundle\DependencyInjection\Compiler\AddUrlNormalizersPass;
use ChameleonSystem\CoreBundle\DependencyInjection\Compiler\ChameleonModulePass;
use ChameleonSystem\CoreBundle\DependencyInjection\Compiler\CollectRequestStateElementProvidersPass;
use ChameleonSystem\CoreBundle\DependencyInjection\Compiler\ControllerResolverPass;
use ChameleonSystem\CoreBundle\DependencyInjection\Compiler\MakeLoggerPublicPass;
use ChameleonSystem\CoreBundle\DependencyInjection\Compiler\SetChameleonHttpKernelPass;
use ChameleonSystem\CoreBundle\DependencyInjection\Compiler\SetCsrfTokenManagerFactoryPass;
use ChameleonSystem\CoreBundle\DependencyInjection\Compiler\SetCsrfTokenStoragePass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ChameleonSystemCoreBundle extends Bundle
{
    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function build(ContainerBuilder $container)
    {
        $container->addCompilerPass(new AddBackendMainMenuItemProvidersPass());
        $container->addCompilerPass(new AddCronJobsPass());
        $container->addCompilerPass(new AddMappersPass());
        $container->addCompilerPass(new AddUrlNormalizersPass());
        $container->addCompilerPass(new ChameleonModulePass());
        $container->addCompilerPass(new ControllerResolverPass());
        $container->addCompilerPass(new SetChameleonHttpKernelPass());
        $container->addCompilerPass(new SetCsrfTokenManagerFactoryPass());
        $container->addCompilerPass(new SetCsrfTokenStoragePass());
        $container->addCompilerPass(new CollectRequestStateElementProvidersPass());
        $container->addCompilerPass(new MakeLoggerPublicPass());
    }
}
