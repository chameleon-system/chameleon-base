<?php

namespace ChameleonSystem\CmsCacheBundle\QueryCache;

use Doctrine\DBAL\Connection;
use esono\pkgCmsCache\CacheInterface;

class QueryCacheDecisionMakerBasedOnTableName implements QueryCacheDecisionMakerInterface
{
    private array|null $tablesWithEnabledQueryCacheHashTable = null;

    public function __construct(private readonly Connection $connection, private readonly CacheInterface $cache)
    {

    }
    public function isCacheable(array $tableNamesInQuery, string $normalizedQuery, array $params, array $types): QueryIsCacheableDecision
    {
        if (count($tableNamesInQuery) === 0) {
            return QueryIsCacheableDecision::NO;
        }

        $tablesWithEnabledQueryCache = $this->getTablesWithEnabledQueryCacheHashTable();
        foreach ($tableNamesInQuery as $tableName) {
            if (false === isset($tablesWithEnabledQueryCache[$tableName])) {
                return QueryIsCacheableDecision::NO;
            }
        }

        return QueryIsCacheableDecision::YES;
    }

    private function getTablesWithEnabledQueryCacheHashTable(): array
    {
        if (null !== $this->tablesWithEnabledQueryCacheHashTable) {
            return $this->tablesWithEnabledQueryCacheHashTable;
        }
        $keyData = ['class'=>__CLASS__, 'method'=>__METHOD__];
        $key = $this->cache->getKey($keyData, false);

        $tablesWithEnabledQueryCacheString = $this->cache->get($key);

        if (null !== $tablesWithEnabledQueryCacheString) {
            $this->tablesWithEnabledQueryCacheHashTable = array_flip(explode(',', $tablesWithEnabledQueryCacheString));
            return $this->tablesWithEnabledQueryCacheHashTable;
        }

        $query = "SELECT name FROM cms_tbl_conf WHERE enable_query_cache = '1'";
        $tableNames = $this->connection->fetchFirstColumn($query);
        $this->tablesWithEnabledQueryCacheHashTable = array_flip($tableNames);

        $this->cache->set($key, implode(',',$tableNames),array(array('table'=>'cms_tbl_conf','id'=>null)));

        return $this->tablesWithEnabledQueryCacheHashTable;

    }



}