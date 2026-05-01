<?php

namespace ChameleonSystem\CmsCacheBundle\DependencyInjection\Compiler;

use ChameleonSystem\CmsCacheBundle\QueryCache\QueryCacheDecisionMakerChain;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class BuildQueryCacheDecisionMakerChainPass implements CompilerPassInterface
{
    private const string TAG_NAME = 'chameleon_system_cms_cache.query_cache_decision_maker';

    public function process(ContainerBuilder $container): void
    {
        if (false === $container->has(QueryCacheDecisionMakerChain::class)) {
            return;
        }

        $taggedServices = $container->findTaggedServiceIds(self::TAG_NAME);
        $orderedServices = [];

        foreach ($taggedServices as $serviceId => $tags) {
            if (QueryCacheDecisionMakerChain::class === $serviceId) {
                continue;
            }

            $priority = null;
            foreach ($tags as $tag) {
                $tagPriority = (int) ($tag['priority'] ?? 0);
                $priority = null === $priority ? $tagPriority : max($priority, $tagPriority);
            }

            $orderedServices[] = [
                'service_id' => $serviceId,
                'priority' => $priority ?? 0,
            ];
        }

        usort($orderedServices, static function (array $serviceA, array $serviceB): int {
            $priorityComparison = $serviceB['priority'] <=> $serviceA['priority'];
            if (0 !== $priorityComparison) {
                return $priorityComparison;
            }

            return $serviceA['service_id'] <=> $serviceB['service_id'];
        });

        $references = [];
        foreach ($orderedServices as $service) {
            $references[] = new Reference($service['service_id']);
        }

        $container->findDefinition(QueryCacheDecisionMakerChain::class)->setArgument(0, $references);
    }
}
