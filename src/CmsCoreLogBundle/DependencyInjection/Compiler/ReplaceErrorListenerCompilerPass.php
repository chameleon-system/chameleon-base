<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\CmsCoreLogBundle\DependencyInjection\Compiler;

use ChameleonSystem\CmsCoreLogBundle\Listener\TreatNotFoundErrorListener;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ReplaceErrorListenerCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     */
    public function process(ContainerBuilder $container)
    {
        if ($container->hasDefinition('exception_listener')) {
            $container->getDefinition('exception_listener')->setClass(TreatNotFoundErrorListener::class);
        }
        if ($container->hasDefinition('twig.exception_listener')) {
            $container->getDefinition('twig.exception_listener')->setClass(TreatNotFoundErrorListener::class);
        }
    }
}
