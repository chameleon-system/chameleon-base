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

Console Commands
----------------
- `php bin/console chameleon_system:cache:clear` — Clears the entire cache (only if caching is active).

License
-------
This bundle is licensed under the MIT License. See the `LICENSE` file at the project root for details.
