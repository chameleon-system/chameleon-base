<?php

namespace ChameleonSystem\SnippetRendererBundle\DependencyInjection\Compiler;

use ChameleonSystem\SnippetRendererBundle\CacheWarmer\NullCacheWarmer;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Deactivates the Twig cache warmer that builds the templates.php file.
 * This is necessary because that warmer generates absolute paths, which aren't correct if the container is built in
 * another environment than the production environment.
 * This compiler pass needs to be removed after upgrading to Symfony 3.2+, where only relative paths are generated.
 */
class UnregisterTwigTemplateCacheWarmerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container)
    {
        if (false === $container->hasDefinition('templating.cache_warmer.template_paths')) {
            return;
        }

        $definition = $container->getDefinition('templating.cache_warmer.template_paths');
        $definition->setClass(NullCacheWarmer::class);
    }
}
