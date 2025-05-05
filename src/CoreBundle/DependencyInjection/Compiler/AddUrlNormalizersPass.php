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

class AddUrlNormalizersPass implements CompilerPassInterface
{
    /**
     * {@inheritDoc}
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        $utilServiceDefinition = $container->getDefinition('chameleon_system_core.util.url_normalization');
        $urlNormalizerServices = $container->findTaggedServiceIds('chameleon_system.url_normalizer');

        $urlNormalizerList = [];

        foreach ($urlNormalizerServices as $urlNormalizerServiceId => $tags) {
            $urlNormalizerDefinition = $container->getDefinition($urlNormalizerServiceId);
            foreach ($tags as $tag) {
                $priority = isset($tag['priority']) ? $tag['priority'] : 0;
                if (!isset($urlNormalizerList[$priority])) {
                    $urlNormalizerList[$priority] = [];
                }
                $urlNormalizerList[$priority][] = $urlNormalizerDefinition;
            }
        }
        krsort($urlNormalizerList);
        foreach ($urlNormalizerList as $urlNormalizerByPriorityList) {
            foreach ($urlNormalizerByPriorityList as $urlNormalizer) {
                $utilServiceDefinition->addMethodCall('addNormalizer', [$urlNormalizer]);
            }
        }
    }
}
