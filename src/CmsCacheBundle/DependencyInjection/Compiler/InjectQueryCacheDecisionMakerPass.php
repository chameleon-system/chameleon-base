<?php

namespace ChameleonSystem\CmsCacheBundle\DependencyInjection\Compiler;

use ChameleonSystem\CmsCacheBundle\QueryCache\QueryCacheDecisionMakerChain;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class InjectQueryCacheDecisionMakerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        if (false === $container->has('database_connection')) {
            return;
        }

        if (false === $container->has(QueryCacheDecisionMakerChain::class)) {
            return;
        }

        $connectionDefinition = $container->findDefinition('database_connection');
        $connectionDefinition->addMethodCall('setQueryCacheDecisionMaker', [
            new Reference(QueryCacheDecisionMakerChain::class),
        ]);
    }
}
