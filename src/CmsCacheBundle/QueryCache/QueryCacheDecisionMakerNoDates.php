<?php

namespace ChameleonSystem\CmsCacheBundle\QueryCache;

class QueryCacheDecisionMakerNoDates implements QueryCacheDecisionMakerInterface
{
    public function isCacheable(array $tableNamesInQuery, string $normalizedQuery, array $params, array $types): QueryIsCacheableDecision
    {
        if ($this->containsDateTimeParameter($params)) {
            return QueryIsCacheableDecision::NO;
        }

        return QueryIsCacheableDecision::NOT_DECIDED;
    }

    /**
     * Checks the parameter list recursively for date values with a time component.
     *
     * @param array<string, mixed>|array<int, mixed> $params
     */
    private function containsDateTimeParameter(array $params): bool
    {
        foreach ($params as $param) {
            if (true === $this->parameterContainsDateTime($param)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Detects whether a single parameter value contains a date with a time portion.
     *
     * String values such as "YYYY-mm-dd HH:ii[:ss]" and DateTime instances with a
     * non-midnight time are treated as non-cacheable.
     */
    private function parameterContainsDateTime(mixed $parameter): bool
    {
        if (is_array($parameter)) {
            foreach ($parameter as $value) {
                if (true === $this->parameterContainsDateTime($value)) {
                    return true;
                }
            }

            return false;
        }

        if ($parameter instanceof \DateTimeInterface) {
            return '00:00:00.000000' !== $parameter->format('H:i:s.u');
        }

        if (false === is_string($parameter)) {
            return false;
        }

        return 1 === preg_match(
            '/^\d{4}-\d{2}-\d{2}[ T]\d{1,2}:\d{2}(?::\d{2}(?:\.\d{1,6})?)?(?: ?(?:Z|[+-]\d{2}:\d{2}))?$/',
            trim($parameter)
        );
    }
}
