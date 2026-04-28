<?php

namespace ChameleonSystem\CmsCacheBundle\Doctrine;

use ChameleonSystem\CmsCacheBundle\DataModel\QueryCacheDecision;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use ChameleonSystem\CoreBundle\ServiceLocator;
use Psr\Log\LoggerInterface;
use esono\pkgCmsCache\CacheInterface;

class CachingConnectionWrapper extends Connection
{
    private const int LOG_SQL_PREVIEW_LENGTH = 300;
    private ?LoggerInterface $logger = null;
    /**
     * @var array<int, string>|null
     */
    private ?array $cacheableTables = null;

    public function __construct(
        array $params,
        Driver $driver,
        ?Configuration $config = null,
        ?EventManager $eventManager = null
    ){
        parent::__construct($params, $driver, $config, $eventManager);
    }
    /**
     * @param mixed $sql
     * @param array<string, mixed>|array<int, mixed> $params
     * @param array<string, mixed>|array<int, mixed> $types
     */
    public function executeQuery($sql, array $params = [], $types = [], ?QueryCacheProfile $qcp = null)
    {
        return parent::executeQuery($sql, $params, $types, $qcp);
    }

    /**
     * @param mixed $sql
     */
    public function prepare($sql)
    {
        return parent::prepare($sql);
    }

    /**
     * @param string $query
     * @param array<string, mixed>|array<int, mixed> $params
     * @param array<string, mixed>|array<int, mixed> $types
     *
     * @return array<string, mixed>|false
     */
    public function fetchAssociative(string $query, array $params = [], array $types = [])
    {
        $cacheDecision = $this->getCacheDecision($query, $params);
        if (false === $cacheDecision->isCacheable()) {
            $this->logCacheSkip($query, $params, $types, $cacheDecision);
        }

        if (true === $cacheDecision->isCacheable()) {
            $cachedResult = $this->getFromCache($query, $params, $types);
            if (null !== $cachedResult) {
                return $cachedResult;
            }
        }

        $result = parent::fetchAssociative($query, $params, $types);

        if (true === $cacheDecision->isCacheable()) {
            $this->saveInCache($result, $query, $params, $types);
        }

        return $result;
    }

    /**
     * @param string $query
     * @param array<string, mixed>|array<int, mixed> $params
     * @param array<string, mixed>|array<int, mixed> $types
     *
     * @return array<int, mixed>|false
     */
    public function fetchNumeric(string $query, array $params = [], array $types = [])
    {
        $cacheDecision = $this->getCacheDecision($query, $params);
        if (false === $cacheDecision->isCacheable()) {
            $this->logCacheSkip($query, $params, $types, $cacheDecision);
        }

        if (true === $cacheDecision->isCacheable()) {
            $cachedResult = $this->getFromCache($query, $params, $types);
            if (null !== $cachedResult) {
                return $cachedResult;
            }
        }

        $result = parent::fetchNumeric($query, $params, $types);

        if (true === $cacheDecision->isCacheable()) {
            $this->saveInCache($result, $query, $params, $types);
        }

        return $result;
    }

    /**
     * @param string $query
     * @param array<string, mixed>|array<int, mixed> $params
     * @param array<string, mixed>|array<int, mixed> $types
     *
     * @return mixed|false
     */
    public function fetchOne(string $query, array $params = [], array $types = [])
    {
        $cacheDecision = $this->getCacheDecision($query, $params);
        if (false === $cacheDecision->isCacheable()) {
            $this->logCacheSkip($query, $params, $types, $cacheDecision);
        }

        if (true === $cacheDecision->isCacheable()) {
            $cachedResult = $this->getFromCache($query, $params, $types);
            if (null !== $cachedResult) {
                return $cachedResult;
            }
        }

        $result = parent::fetchOne($query, $params, $types);

        if (true === $cacheDecision->isCacheable()) {
            $this->saveInCache($result, $query, $params, $types);
        }

        return $result;
    }

    /**
     * @param string $query
     * @param array<string, mixed>|array<int, mixed> $params
     * @param array<string, mixed>|array<int, mixed> $types
     *
     * @return array<int, array<string, mixed>>
     */
    public function fetchAllAssociative(string $query, array $params = [], array $types = []): array
    {
        $cacheDecision = $this->getCacheDecision($query, $params);
        if (false === $cacheDecision->isCacheable()) {
            $this->logCacheSkip($query, $params, $types, $cacheDecision);
        }

        if (true === $cacheDecision->isCacheable()) {
            $cachedResult = $this->getFromCache($query, $params, $types);
            if (null !== $cachedResult) {
                return $cachedResult;
            }
        }

        $result = parent::fetchAllAssociative($query, $params, $types);

        if (true === $cacheDecision->isCacheable()) {
            $this->saveInCache($result, $query, $params, $types);
        }

        return $result;
    }

    /**
     * @param mixed $sql
     * @param array<string, mixed>|array<int, mixed> $params
     * @param array<string, mixed>|array<int, mixed> $types
     *
     * @return int|string
     */
    public function executeStatement($sql, array $params = [], array $types = [])
    {
        return parent::executeStatement($sql, $params, $types);
    }

    /**
     * @param mixed ...$args
     */
    public function query()
    {
        $args = func_get_args();
        return parent::query(...$args);
    }

    /**
     * @param mixed $sql
     *
     * @return int|string
     */
    public function exec($sql)
    {
        return parent::exec($sql);
    }

    /**
     * @param mixed $sql
     * @param array<string, mixed>|array<int, mixed> $params
     */
    private function getCacheDecision($sql, array $params = []): QueryCacheDecision
    {
        if (false === is_string($sql)) {
            return QueryCacheDecision::sqlNotString();
        }

        $normalizedSql = trim(preg_replace('/\s+/', ' ', $sql) ?? '');
        if ('' === $normalizedSql) {
            return QueryCacheDecision::sqlEmpty();
        }

        if (1 !== preg_match('/^SELECT\b/i', $normalizedSql)) {
            return QueryCacheDecision::notSelect();
        }

        if (true === $this->containsDateTimeParameter($params)) {
            return QueryCacheDecision::dateTimeParameter();
        }

        $tables = $this->extractTables($normalizedSql);

        if (0 === count($tables)) {
            return QueryCacheDecision::noTablesDetected();
        }

        $uncacheableTables = [];
        foreach ($tables as $table) {
            if (false === in_array($table, $this->getCacheableTables(), true)) {
                $uncacheableTables[] = $table;
            }
        }

        if ([] !== $uncacheableTables) {
            return QueryCacheDecision::containsUncacheableTables($tables, $uncacheableTables);
        }

        return QueryCacheDecision::cacheable($tables);
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

    /**
     * @param mixed $sql
     * @param array<string, mixed>|array<int, mixed> $params
     * @param array<string, mixed>|array<int, mixed> $types
     */
    private function getFromCache($sql, array $params = [], array $types = []): mixed
    {
        $cache = $this->getCache();
        if (false === $cache->isActive()) {
            $this->getLogger()->debug('SQL cache read skipped because cache is inactive.', $this->buildLogContext($sql, $params, $types));

            return null;
        }

        $cacheKey = $cache->getKey([
            'owner' => __CLASS__,
            'sql' => $sql,
            'params' => $params,
            'types' => $types,
        ]);

        $cachedQuery = $cache->get($cacheKey);
        if (null === $cachedQuery) {
            $this->getLogger()->debug('SQL cache miss.', $this->buildLogContext($sql, $params, $types, [
                'cache_key' => $cacheKey,
            ]));

            return null;
        }

        $this->getLogger()->debug('SQL cache hit.', $this->buildLogContext($sql, $params, $types, [
            'cache_key' => $cacheKey,
            'result_summary' => $this->summarizeResult($cachedQuery),
        ]));

        return $cachedQuery;
    }

    /**
     * @param mixed $queryResult
     * @param mixed $sql
     * @param array<string, mixed>|array<int, mixed> $params
     * @param array<string, mixed>|array<int, mixed> $types
     */
    private function saveInCache($queryResult, $sql, array $params = [], array $types = []): void
    {
        $cache = $this->getCache();
        if (false === $cache->isActive()) {
            $this->getLogger()->debug('SQL cache write skipped because cache is inactive.', $this->buildLogContext($sql, $params, $types));

            return;
        }

        $tables = $this->extractTables((string) $sql);
        $triggers = [];
        foreach ($tables as $table) {
            if (true === in_array($table, $this->getCacheableTables(), true)) {
                $triggers[] = [
                    'table' => $table,
                    'id' => null,
                ];
            }
        }

        $cacheKey = $cache->getKey([
            'owner' => __CLASS__,
            'sql' => $sql,
            'params' => $params,
            'types' => $types,
        ]);

        $cache->set($cacheKey, $queryResult, $triggers);
        $this->getLogger()->debug('SQL cache entry stored.', $this->buildLogContext($sql, $params, $types, [
            'cache_key' => $cacheKey,
            'trigger_count' => count($triggers),
            'triggers' => $triggers,
            'result_summary' => $this->summarizeResult($queryResult),
        ]));
    }

    /**
     * @param mixed $sql
     * @param array<string, mixed>|array<int, mixed> $params
     * @param array<string, mixed>|array<int, mixed> $types
     * @param QueryCacheDecision $decision
     */
    private function logCacheSkip($sql, array $params, array $types, QueryCacheDecision $decision): void
    {
        $this->getLogger()->debug('SQL cache skipped for query.', $this->buildLogContext($sql, $params, $types, [
            'skip_reason' => $decision->getReason(),
            'tables' => $decision->getTables(),
            'uncacheable_tables' => $decision->getUncacheableTables(),
        ]));
    }

    /**
     * @param mixed $sql
     *
     * @return array<int, string>
     */
    private function extractTables($sql): array
    {
        preg_match_all('/\b(?:FROM|JOIN)\s+`?([a-zA-Z0-9_]+)`?/i', (string) $sql, $tableMatches);

        return array_values(array_unique($tableMatches[1] ?? []));
    }

    /**
     * @param mixed $sql
     * @param array<string, mixed>|array<int, mixed> $params
     * @param array<string, mixed>|array<int, mixed> $types
     * @param array<string, mixed> $extraContext
     *
     * @return array<string, mixed>
     */
    private function buildLogContext($sql, array $params = [], array $types = [], array $extraContext = []): array
    {
        $normalizedSql = trim(preg_replace('/\s+/', ' ', (string) $sql) ?? '');

        return array_merge([
            'sql_hash' => sha1((string) $sql),
            'sql_preview' => mb_substr($normalizedSql, 0, self::LOG_SQL_PREVIEW_LENGTH),
            'param_count' => count($params),
            'params' => $this->normalizeForLog($params),
            'types' => $this->normalizeForLog($types),
        ], $extraContext);
    }

    private function summarizeResult(mixed $result): array
    {
        if (false === $result) {
            return ['kind' => 'false'];
        }

        if (null === $result) {
            return ['kind' => 'null'];
        }

        if (is_array($result)) {
            $isList = array_is_list($result);

            return [
                'kind' => $isList ? 'list' : 'map',
                'count' => count($result),
            ];
        }

        return [
            'kind' => get_debug_type($result),
            'value' => $result,
        ];
    }

    private function normalizeForLog(mixed $value): mixed
    {
        if ($value instanceof \DateTimeInterface) {
            return [
                'type' => get_debug_type($value),
                'value' => $value->format(\DateTimeInterface::ATOM),
            ];
        }

        if (is_array($value)) {
            $normalized = [];
            foreach ($value as $key => $item) {
                $normalized[$key] = $this->normalizeForLog($item);
            }

            return $normalized;
        }

        if (is_object($value)) {
            return ['type' => get_debug_type($value)];
        }

        if (is_resource($value)) {
            return ['type' => 'resource'];
        }

        return $value;
    }

    private function getLogger(): LoggerInterface
    {
        if (null !== $this->logger) {
            return $this->logger;
        }

        $this->logger = ServiceLocator::get('logger');
        return $this->logger;
    }

    /**
     * @return array<int, string>
     */
    private function getCacheableTables(): array
    {
        if (null !== $this->cacheableTables) {
            return $this->cacheableTables;
        }

        $tables = ServiceLocator::getParameter('chameleon_system_cms_cache.dbal_query_cache.cacheable_tables');
        if (false === is_array($tables)) {
            return [];
        }

        /** @var array<int, string> $tables */
        $this->cacheableTables = $tables;

        return $this->cacheableTables;
    }

    private function getCache(): CacheInterface
    {
        /** @var CacheInterface */
        return ServiceLocator::get('chameleon_system_cms_cache.cache');
    }
}
