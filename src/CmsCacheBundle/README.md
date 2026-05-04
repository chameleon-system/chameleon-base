# Chameleon System CmsCacheBundle
=================================

Overview
--------
The CmsCacheBundle provides a flexible caching layer for the Chameleon System. It supports
key-based caching, cache invalidation triggers, and multiple storage backends (e.g., Memcache,
null storage, Redis using an additional bundle). 
You can enable or disable caching at runtime and clear or invalidate cache entries programmatically or via console commands.

Features
--------
- Toggleable cache layer (`enable()` / `disable()` at runtime)
- Key generation with optional request state fingerprinting
- CRUD operations: `get()`, `set()`, `delete()`
- Invalidation by trigger: `callTrigger(table, id)`
- Full cache wipe: `clearAll()` or via console: `chameleon_system:cache:clear`
- Storage backends: Memcache or NullStorage (no-op)
- Automatic DBAL read caching via `CachingConnectionWrapper`
- Structured cacheability decisions via `QueryCacheDecision`
- Debug logging for cache hit, miss, skip, and store events

Installation
------------
The bundle is included and enabled by default in Chameleon System. Ensure parameters in your
environment or `config/packages/chameleon_system_core.yaml` are set:

```yaml
chameleon_system_core:
  cache:
    allow: true                           # enable/disable caching globally
    memcache_server1: '127.0.0.1'
    memcache_port1: 11211
    memcache_server2: null
    memcache_port2: null
    memcache_sessions_server1: '127.0.0.1'
    memcache_sessions_port1: 11211
    memcache_sessions_server2: null
    memcache_sessions_port2: null

# Mandatory set a secret prefix for cache keys
framework:
  secret: '%env(APP_SECRET)%'
```

DBAL is modified to cache queries to tables marked with `enable_query_cache` in `cms_tbl_conf`. Queries restricted by date or time
parameters will be excluded (see `\ChameleonSystem\CmsCacheBundle\QueryCache\QueryCacheDecisionMakerNoDates`).
You may add additional query cache decisions by implementing `\ChameleonSystem\CmsCacheBundle\QueryCache\QueryCacheDecisionMakerInterface`.
This gives you controll over what queries will be cached.

Usage
-----
Inject the cache service (`esono\pkgCmsCache\CacheInterface`) into your code:

```php
use esono\pkgCmsCache\CacheInterface;

class MyService
{
    public function __construct(private CacheInterface $cache) {}

    public function getPageData(int $pageId): array
    {
        // 1. Build a cache key
        $key = $this->cache->getKey(['page' => $pageId]);

        // 2. Try to load from cache
        $data = $this->cache->get($key);
        if (null !== $data) {
            return $data;
        }

        // 3. Fetch or compute data
        $data = $this->loadFromDatabase($pageId);

        // 4. Store in cache with invalidation trigger
        $this->cache->set(
            $key,
            $data,
            [['table' => 'cms_page', 'id' => $pageId]],
            3600 // seconds to live
        );

        return $data;
    }

    public function clearPageCache(int $pageId): void
    {
        // invalidates all cache entries for this page ID
        $this->cache->callTrigger('cms_page', $pageId);
    }
}
```

Doctrine Query Caching
----------------------
The bundle also registers `ChameleonSystem\CmsCacheBundle\Doctrine\CachingConnectionWrapper`
as the Doctrine DBAL wrapper. This wrapper adds transparent cache handling for selected read
queries executed through methods such as `fetchAssociative()`, `fetchNumeric()`, `fetchOne()`,
and `fetchAllAssociative()`.

A query is only cached when all of the following conditions are true:

- the SQL is a `SELECT`
- at least one table can be detected from `FROM` or `JOIN`
- all detected tables are in the configured allowlist
- no parameter contains a date-time value with a non-midnight time component

The configured allowlist is built from:

- generic default tables from `chameleon-base`, `chameleon-shop`
- `additional_cacheable_tables` from the project configuration
- optional removals from `excluded_cacheable_tables`

The cacheability decision is represented by
`ChameleonSystem\CmsCacheBundle\DataModel\QueryCacheDecision`.
It exposes:

- `isCacheable()`
- `getReason()`
- `getTables()`
- `getUncacheableTables()`

Current decision reasons are:

- `ok`
- `sql_not_string`
- `sql_empty`
- `not_select`
- `datetime_parameter`
- `no_tables_detected`
- `contains_uncacheable_tables`

When a query is cacheable, the wrapper derives a cache key from:

- the wrapper class name
- the SQL string
- the bound parameters
- the DBAL parameter types

The stored cache entry is linked to invalidation triggers for each cacheable table found in the query.

Debug Logging
-------------
The wrapper writes `debug` logs through the default PSR-3 logger for the following events:

- `SQL cache skipped for query.`
- `SQL cache read skipped because cache is inactive.`
- `SQL cache miss.`
- `SQL cache hit.`
- `SQL cache write skipped because cache is inactive.`
- `SQL cache entry stored.`

The log context is intended to make SQL cache generation debuggable without stepping through the wrapper.
Typical context keys are:

- `sql_hash`
- `sql_preview`
- `tables`
- `param_count`
- `params`
- `types`
- `skip_reason`
- `uncacheable_tables`
- `cache_key`
- `trigger_count`
- `triggers`
- `result_summary`

`sql_preview` is intentionally shortened, while `sql_hash` gives you a stable identifier to correlate
skip, miss, hit, and store log lines for the same query shape.

Console Commands
----------------
- `php bin/console chameleon_system:cache:clear` — Clears the entire cache (only if caching is active).

License
-------
This bundle is licensed under the MIT License. See the `LICENSE` file at the project root for details.
