<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\NewsletterBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class NewsletterPostProcessorCompiler implements CompilerPassInterface
{
    /**
     * You can modify the container here before it is dumped to PHP code.
     *
     * @api
     *
     * @return void
     */
    public function process(\Symfony\Component\DependencyInjection\ContainerBuilder $container)
    {
        $processorIds = $container->findTaggedServiceIds('chameleon_system_newsletter.post_processor');
        $processorCollectorService = $container->getDefinition('chameleon_system_newsletter.post_processor_collector');

        foreach (array_keys($processorIds) as $processorId) {
            $processorCollectorService->addMethodCall('addPostProcessor', [$container->getDefinition($processorId)]);
        }
    }
}
