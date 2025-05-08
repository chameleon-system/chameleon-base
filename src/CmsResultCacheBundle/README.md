# Chameleon System CmsResultCacheBundle
======================================

The **CmsResultCacheBundle** provides a simple, database-backed cache for arbitrary PHP results. It allows you to store, retrieve, and invalidate cached data, with built-in expiration and garbage collection via a cron job.

Table of Contents
-----------------
- [Overview](#overview)
- [Services](#services)
  - [DataBaseCacheManager](#databasecachemanager)
  - [Cron Job: Garbage Collection](#cron-job-garbage-collection)
- [Usage Examples](#usage-examples)
  - [Storing a Value](#storing-a-value)
  - [Retrieving a Value](#retrieving-a-value)
  - [Checking Existence](#checking-existence)
  - [Manual Garbage Collection](#manual-garbage-collection)
- [Implementation Details](#implementation-details)

## Overview

This bundle enables you to cache any PHP value (strings, arrays, objects) by serializing and storing it in the `pkg_cms_result_cache` table. Cached entries can be set to expire after a timestamp, and optionally auto-removed when expired.

## Services

### DataBaseCacheManager
- **Service ID**: `chameleon_system_cms_result_cache.bridge_chameleon_service.data_base_cache_manager`
- **Class**: `ChameleonSystem\CmsResultCacheBundle\Bridge\Chameleon\Service\DataBaseCacheManager`

Key methods:
- `get` – returns cached value or `false`.
- `exists` – checks if an entry exists (ignores expiration).
- `set` – stores a value with optional expire timestamp.
- `garbageCollector` – deletes expired entries marked for garbage collection.

### Cron Job: Garbage Collection
- **Service ID**: `chameleon_system_cms_result_cache.cronjob.garbage_collector_cronjob`
- **Class**: `TCMSCronJob_PkgCmsResultCache_GarbageCollector`

This cron job invokes `DataBaseCacheManager::garbageCollector()` to purge expired entries.

## Usage Examples

Obtain the service via Symfony DI or the Chameleon ServiceLocator:
```php
use ChameleonSystem\CmsResultCacheBundle\Bridge\Chameleon\Service\DataBaseCacheManager;
use ChameleonSystem\CoreBundle\ServiceLocator;

/** @var DataBaseCacheManager $cacheManager */
$cacheManager = ServiceLocator::get('chameleon_system_cms_result_cache.bridge_chameleon_service.data_base_cache_manager');
```

### Storing a Value
```php
$owner = 'MyService';
$key = 'user_list_page_1';
$data = ['id' => 1, 'name' => 'Alice'];
$expireAt = time() + 3600; // 1 hour
$cacheManager->set($owner, $key, $data, $expireAt);
```

### Retrieving a Value
```php
$cached = $cacheManager->get($owner, $key);
if (false !== $cached) {
    // use $cached
} else {
    // cache miss
}
```

### Checking Existence
```php
if ($cacheManager->exists($owner, $key)) {
    // an entry exists (may be expired)
}
```

### Manual Garbage Collection
Invoke directly if needed:
```php
$cacheManager->garbageCollector();
```

## Implementation Details

- **Serialization**: Values are serialized and base64-encoded.
- **Expiration**: Entries expire when `date_expire_after` passes; `get()` returns `false` on expiry.
- **Garbage Collection**: Controlled by `garbage_collect_when_expired` flag; cleaned up by cron job or manual call.
- **Unique Keys**: Identified by SHA-1 hash of `owner` and `key`.

For more advanced caching scenarios, you can extend the `DataBaseCacheManager` or adjust the cron frequency in your system settings.