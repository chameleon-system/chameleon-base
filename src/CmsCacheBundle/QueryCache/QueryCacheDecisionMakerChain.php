<?php

namespace ChameleonSystem\CmsCacheBundle\QueryCache;

readonly class QueryCacheDecisionMakerChain implements QueryCacheDecisionMakerInterface
{
    /**
     * @param QueryCacheDecisionMakerInterface[] $decisionMaker
     */
    public function __construct(private array $decisionMaker)
    {
    }

    public function isCacheable(
        array $tableNamesInQuery,
        string $normalizedQuery,
        array $params,
        array $types
    ): QueryIsCacheableDecision {
        foreach ($this->decisionMaker as $decisionMaker) {
            $decision = $decisionMaker->isCacheable($tableNamesInQuery, $normalizedQuery, $params, $types);
            if (QueryIsCacheableDecision::NOT_DECIDED === $decision) {
                continue;
            }

            return $decision;
        }

        return QueryIsCacheableDecision::NO;
    }
}
