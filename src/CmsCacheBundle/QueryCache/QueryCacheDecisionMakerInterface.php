<?php

namespace ChameleonSystem\CmsCacheBundle\QueryCache;

interface QueryCacheDecisionMakerInterface
{
    public function isCacheable(array $tableNamesInQuery, string $normalizedQuery, array $params, array $types): QueryIsCacheableDecision;
}