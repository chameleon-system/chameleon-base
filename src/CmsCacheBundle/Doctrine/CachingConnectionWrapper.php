<?php

namespace ChameleonSystem\CmsCacheBundle\Doctrine;

use ChameleonSystem\CmsCacheBundle\QueryCache\QueryCacheDecisionMakerInterface;
use ChameleonSystem\CmsCacheBundle\QueryCache\QueryIsCacheableDecision;
use ChameleonSystem\CoreBundle\ServiceLocator;
use Doctrine\Common\EventManager;
use Doctrine\DBAL\Cache\QueryCacheProfile;
use Doctrine\DBAL\Configuration;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Driver;
use Doctrine\DBAL\Exception;
use esono\pkgCmsCache\CacheInterface;

class CachingConnectionWrapper extends Connection
{
    private ?QueryCacheDecisionMakerInterface $queryCacheDecisionMaker = null;

    public function __construct(
        array $params,
        Driver $driver,
        ?Configuration $config = null,
        ?EventManager $eventManager = null
    ) {
        parent::__construct($params, $driver, $config, $eventManager);
    }

    /**
     * @param array<string, mixed>|array<int, mixed> $params
     * @param array<string, mixed>|array<int, mixed> $types
     *
     * @throws Exception
     */
    public function executeQuery($sql, array $params = [], $types = [], ?QueryCacheProfile $qcp = null)
    {
        return parent::executeQuery($sql, $params, $types, $qcp);
    }

    public function prepare($sql)
    {
        return parent::prepare($sql);
    }

    /**
     * @param array<string, mixed>|array<int, mixed> $params
     * @param array<string, mixed>|array<int, mixed> $types
     *
     * @return array<string, mixed>|false
     *
     * @throws Exception
     */
    public function fetchAssociative(string $query, array $params = [], array $types = [])
    {
        if (false === $this->getCache()->isActive()) {
            return parent::fetchAssociative($query, $params, $types);
        }
        $normalizedQuery = $this->normalizeQuery($query);
        $tableNames = $this->extractTables($normalizedQuery);
        $decision = $this->getQueryCacheDecisionMaker()->isCacheable($tableNames, $normalizedQuery, $params, $types);

        if (QueryIsCacheableDecision::YES === $decision) {
            $cachedResult = $this->getFromCache($normalizedQuery, $params, $types);
            if (null !== $cachedResult) {
                return $cachedResult;
            }
        }

        $result = parent::fetchAssociative($query, $params, $types);

        if (QueryIsCacheableDecision::YES === $decision) {
            $this->saveInCache($tableNames, $result, $normalizedQuery, $params, $types);
        }

        return $result;
    }

    /**
     * @param array<string, mixed>|array<int, mixed> $params
     * @param array<string, mixed>|array<int, mixed> $types
     *
     * @return array<int, mixed>|false
     *
     * @throws Exception
     */
    public function fetchNumeric(string $query, array $params = [], array $types = [])
    {
        if (false === $this->getCache()->isActive()) {
            return parent::fetchNumeric($query, $params, $types);
        }

        $normalizedQuery = $this->normalizeQuery($query);
        $tableNames = $this->extractTables($normalizedQuery);
        $decision = $this->getQueryCacheDecisionMaker()->isCacheable($tableNames, $normalizedQuery, $params, $types);

        if (QueryIsCacheableDecision::YES === $decision) {
            $cachedResult = $this->getFromCache($normalizedQuery, $params, $types);
            if (null !== $cachedResult) {
                return $cachedResult;
            }
        }

        $result = parent::fetchNumeric($query, $params, $types);

        if (QueryIsCacheableDecision::YES === $decision) {
            $this->saveInCache($tableNames, $result, $normalizedQuery, $params, $types);
        }

        return $result;
    }

    /**
     * @param array<string, mixed>|array<int, mixed> $params
     * @param array<string, mixed>|array<int, mixed> $types
     *
     * @return mixed|false
     *
     * @throws Exception
     */
    public function fetchOne(string $query, array $params = [], array $types = [])
    {
        if (false === $this->getCache()->isActive()) {
            return parent::fetchOne($query, $params, $types);
        }
        $normalizedQuery = $this->normalizeQuery($query);
        $tableNames = $this->extractTables($normalizedQuery);
        $decision = $this->getQueryCacheDecisionMaker()->isCacheable($tableNames, $normalizedQuery, $params, $types);

        if (QueryIsCacheableDecision::YES === $decision) {
            $cachedResult = $this->getFromCache($normalizedQuery, $params, $types);
            if (null !== $cachedResult) {
                return $cachedResult;
            }
        }

        $result = parent::fetchOne($query, $params, $types);

        if (QueryIsCacheableDecision::YES === $decision) {
            $this->saveInCache($tableNames, $result, $normalizedQuery, $params, $types);
        }

        return $result;
    }

    /**
     * @param array<string, mixed>|array<int, mixed> $params
     * @param array<string, mixed>|array<int, mixed> $types
     *
     * @return array<int, array<string, mixed>>
     *
     * @throws Exception
     */
    public function fetchAllAssociative(string $query, array $params = [], array $types = []): array
    {
        if (false === $this->getCache()->isActive()) {
            return parent::fetchAllAssociative($query, $params, $types);
        }
        $normalizedQuery = $this->normalizeQuery($query);
        $tableNames = $this->extractTables($normalizedQuery);
        $decision = $this->getQueryCacheDecisionMaker()->isCacheable($tableNames, $normalizedQuery, $params, $types);

        if (QueryIsCacheableDecision::YES === $decision) {
            $cachedResult = $this->getFromCache($normalizedQuery, $params, $types);
            if (null !== $cachedResult) {
                return $cachedResult;
            }
        }
        $result = parent::fetchAllAssociative($query, $params, $types);

        if (QueryIsCacheableDecision::YES === $decision) {
            $this->saveInCache($tableNames, $result, $normalizedQuery, $params, $types);
        }

        return $result;
    }

    /**
     * @param array<string, mixed>|array<int, mixed> $params
     * @param array<string, mixed>|array<int, mixed> $types
     *
     * @return int|string
     *
     * @throws Exception
     */
    public function executeStatement($sql, array $params = [], array $types = [])
    {
        return parent::executeStatement($sql, $params, $types);
    }

    public function query()
    {
        $args = func_get_args();

        return parent::query(...$args);
    }

    /**
     * @return int|string
     *
     * @throws Exception
     */
    public function exec($sql)
    {
        return parent::exec($sql);
    }

    private function normalizeQuery(string $query): string
    {
        return trim(preg_replace('/\s+/', ' ', $query) ?? '');
    }

    /**
     * @param array<string, mixed>|array<int, mixed> $params
     * @param array<string, mixed>|array<int, mixed> $types
     */
    private function getFromCache($sql, array $params = [], array $types = []): mixed
    {
        $cache = $this->getCache();
        if (false === $cache->isActive()) {
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
            return null;
        }

        return $cachedQuery;
    }

    /**
     * @param array<string, mixed>|array<int, mixed> $params
     * @param array<string, mixed>|array<int, mixed> $types
     */
    private function saveInCache(array $tableNames, $queryResult, $sql, array $params = [], array $types = []): void
    {
        $cache = $this->getCache();
        if (false === $cache->isActive()) {
            return;
        }

        $triggers = [];
        foreach ($tableNames as $table) {
            $triggers[] = [
                'table' => $table,
                'id' => null,
            ];
        }

        $cacheKey = $cache->getKey([
            'owner' => __CLASS__,
            'sql' => $sql,
            'params' => $params,
            'types' => $types,
        ]);

        $cache->set($cacheKey, $queryResult, $triggers);
    }

    /**
     * @return array<int, string>
     */
    private function extractTables($sql): array
    {
        preg_match_all('/\b(?:FROM|JOIN)\s+`?([a-zA-Z0-9_]+)`?/i', (string) $sql, $tableMatches);

        $tables = array_values(array_unique($tableMatches[1] ?? []));

        return array_filter($tables, static fn (string $table) => !str_ends_with($table, '_mlt'));
    }

    public function setQueryCacheDecisionMaker(QueryCacheDecisionMakerInterface $queryCacheDecisionMaker): void
    {
        $this->queryCacheDecisionMaker = $queryCacheDecisionMaker;
    }

    private function getQueryCacheDecisionMaker(): QueryCacheDecisionMakerInterface
    {
        if (null === $this->queryCacheDecisionMaker) {
            throw new \LogicException(sprintf('Missing query cache decision maker injection for %s.', __CLASS__));
        }

        return $this->queryCacheDecisionMaker;
    }

    private function getCache(): CacheInterface
    {
        /* @var CacheInterface */
        return ServiceLocator::get('chameleon_system_cms_cache.cache');
    }
}
