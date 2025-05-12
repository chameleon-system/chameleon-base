Chameleon System ExternalTrackerBundle
======================================

Overview
--------
The ExternalTrackerBundle captures key storefront events—such as product searches, category views, article detail views, and checkout progress—and records them in the `pkg_external_tracker` table. This lightweight tracker is intended to feed external analytics systems or custom reporting tools without coupling your code to third-party SDKs.

Installation
------------
Included in `chameleon-system/chameleon-base`. No additional Composer install is needed beyond the base package.

Bundle Registration
-------------------
Symfony Flex (4+) auto-registers bundles. Without Symfony Flex, add it to `app/AppKernel.php` manually:

```php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = [
        // …
        new ChameleonSystem\\ExternalTrackerBundle\\ChameleonSystemExternalTrackerBundle(),
    ];
    return $bundles;
}
```

Services
--------
All services are declared in `Resources/config/services.xml`:

- **chameleon_system_external_tracker.listener.search_result**
  - Listens on the event `chameleon_system_shop.article_list.result_generated`
  - If the filter is a search filter, logs an event with:
    - `search` parameters
    - `numberOfResults`
    - `items` (array of `TdbShopArticle` objects)

- **chameleon_system_external_tracker.listener.category_result**
  - Also listens on `chameleon_system_shop.article_list.result_generated`
  - If the filter is a category filter, logs:
    - `numberOfResults`
    - `items`

Event Recording
---------------
Listeners delegate to the active tracker instance:
```php
$tracker = TdbPkgExternalTrackerList::GetActiveInstance();
$tracker->AddEvent(
    TPkgExternalTrackerState::EVENT_PKG_SHOP_SEARCH_WITH_ITEMS, // or other event constant
    [
        'search' => $params,
        'numberOfResults' => $count,
        'items' => $items,
    ]
);
```
See `TPkgExternalTrackerState` for available event constants.

Web Modules & Endpoints
-----------------------
The bundle includes WebModules (e.g., `pkgSearch`, `pkgArticle`, `pkgShop`, `pkgExtranet`) that trigger events via the `TPkgExternalTrackerStateEndPoint` object-view endpoint, useful for AJAX-based tracking.

Extending & Custom Events
-------------------------
To record custom events:
1. Create a Symfony event listener for your target event.
2. Inside the listener, call `$tracker->AddEvent($eventCode, $payload)`.
3. Add your event codes to `TPkgExternalTrackerState` for consistency.

License
-------
This bundle is released under the same license as the Chameleon System.
