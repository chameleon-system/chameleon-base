<?php

/*
 * This file is part of the Chameleon System (https://www.chameleonsystem.com).
 *
 * (c) ESONO AG (https://www.esono.de)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace ChameleonSystem\JavaScriptMinificationBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class JsMinifyPass implements CompilerPassInterface
{
    /**
     * Gets all tagged javascript minify integrations and set the one configured in configuration.
     *
     * {@inheritdoc}
     *
     * @return void
     */
    public function process(ContainerBuilder $container)
    {
        $jsMinifierIntegrationToUse = $container->getParameter('js_minifier_integration');
        if (null === $jsMinifierIntegrationToUse) {
            return;
        }

        if (!$container->hasDefinition('chameleon_system_javascript_minify.minify_js')) {
            return;
        }
        $minifierServiceDefinition = $container->getDefinition('chameleon_system_javascript_minify.minify_js');
        $taggedJsMinifierServices = $container->findTaggedServiceIds('chameleon_system.minify_js');
        foreach ($taggedJsMinifierServices as $minifierIntegrationServiceId => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                if ($attributes['alias'] !== $jsMinifierIntegrationToUse) {
                    continue;
                }
                $minifierServiceDefinition->addMethodCall(
                    'setMinifierJsIntegration',
                    [new Reference($minifierIntegrationServiceId)]
                );

                return;
            }
        }
        throw new \LogicException('Js minifier was configured ('.$jsMinifierIntegrationToUse.'), but no service for this was found');
    }
}
