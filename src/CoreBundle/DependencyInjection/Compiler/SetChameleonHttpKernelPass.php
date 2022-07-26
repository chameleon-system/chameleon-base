<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CoreBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class SetChameleonHttpKernelPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        $container->setAlias('http_kernel', 'chameleon_system_core.http_kernel');
        $container->getAlias('http_kernel')->setPublic(true);
    }
}
